<?php defined('BASEPATH') or exit('No direct script access allowed');

class Report extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report/mreport');
    }

    public function totalcpgs_get()
    {
        $data = $this->mreport->readTotalCpgs();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }

    public function totalfarmers_get()
    {}

    public function cpgsall_get()
    {
        $data = $this->mreport->readCpgsAll();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }

    public function Provinsis_get()
    {
        $data = $this->mreport->readProvinsis();
        $this->response($data, 200);
    }

    public function Warehouse_get()
    {
        $data = $this->mreport->readWarehouse($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function bu_get()
    {
        $data = $this->mreport->readBu();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function Kabupatens_get()
    {
        $data = $this->mreport->readKabupatens($this->get('key'));
        $this->response($data, 200);
    }

    public function combo_kecamatan_get(){
        $data = $this->mreport->getComboKecamatan($this->get('kab'));
        $this->response($data, 200);
    }

    public function combo_desa_get(){
        $data = $this->mreport->getComboDesa($this->get('kec'));
        $this->response($data, 200);
    }

    public function combo_role_farmer_get(){
        $data = $this->mreport->getComboRoleFarmer($this->get('desa'));
        $this->response($data, 200);
    }

    public function combo_role_agent_get(){
        $data = $this->mreport->getComboRoleAgent($this->get('desa'));
        $this->response($data, 200);
    }

    public function combo_mill_get(){
        $data = $this->mreport->getComboMill($this->get('desa'));
        $this->response($data, 200);
    }

    public function Kecamatans_get()
    {
        $sesPartner = ($_SESSION['PartnerID'] > 1) ? $_SESSION['PartnerID'] : 'ALL';
        $data       = $this->mreport->readKecamatans($this->get('key'), $sesPartner);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function cpg_get()
    {
        if ($this->get('dist')) {
            $data = $this->mreport->getCPG($this->get('dist'));
        } elseif ($this->get('subd')) {
            $data = $this->mreport->getCPGSubdisrict($this->get('subd'));
        }
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function format($data, $style, $default = 1)
    {
        if ($style == '') {
            $style = $default;
        }

        if ($style == 2) {
            $data = number_format($data, 0);
        } elseif ($style == 3) {
            $data = number_format($data, 2);
        } elseif ($style == 4) {
            $data = number_format($data, 2) . '%';
        } elseif ($style == 5) {
            // $data = 12.0;
            //         echo $data.'|';
            $data = number_format($data, 1, ',', '.');
            //       echo $data.'|'.substr($data,strlen($data)-1,1);
            if (substr($data, strlen($data) - 1, 1) == '0') {
                $data = number_format($data, 0);
            }

            //     echo $data;exit;
        }
        return $data;
    }

    // export excel
    public function laporan_post()
    {
        $kab          = ($this->post('Kabupaten') == '') ? " -- All --" : $this->post('Kabupaten');
        $jenis        = $this->post('jenis');
        $LatestSurvey = $this->post('LatestSurvey');
        //$stat = 'export';
        $mem_ini = ini_get('memory_limit');
        //ini_set('memory_limit', '16M');
        ini_set('memory_limit', '1048576M');
        $sesPartner = ($_SESSION['FlagAccess'] > 0) ? $_SESSION['PartnerID'] : 'ALL';
        $arData     = $this->mreport->exportFarmer(
            $this->post('Provinsi'),
            $kab,
            $jenis,
            $this->post('Survey'),
            $this->post('trainingYear'),
            $this->post('CertificationType'),
            $LatestSurvey,
            $sesPartner
        );
        //$prov,$kab,$jenis,$survey,$this->post('trainingDate'));
        //$arData = $data['data'];

        if ($arData) {
            if ($jenis == 'Farmer Detail Data') {
                $width[0]   = $width[1]   = 25;
                $width[2]   = $width[9]   = $width[10]   = $width[14]   = 20;
                $format[19] = $format[20] = $format[21] = $format[22] = $format[23] = $format[24] =
                $format[25] = $format[26] = $format[27] = $format[28] = 2;
                $format[16] = 3;
            } elseif ($jenis == 'Summary Garden Data') {
                $width      = array(20, 20);
                $default    = 2;
                $format     = array(1, 1);
                $format[13] = 1;
                $format[10] = 4;
                $align[13]  = 'right';
                $keys       = array_keys($arData[0]);
                for ($i = 0; $i < sizeof($arData['data']); $i++) {
                    for ($j = 2; $j < sizeof($keys); $j++) {
                        $sum[$j] += $arData[$i][$keys[$j]];
                    }
                }
                $sum[10] = round($sum[7] / $sum[6] * 100) . '%';
            } elseif ($jenis == 'Summary CPG') {
                $width     = array(30, 10, 40);
                $format[3] = $format[4] = $format[5] = 2;
                $align     = array('left', 'left', 'left');
            } elseif ($jenis == 'Summary Master Training') {
                $width[2] = $width[7] = $width[8] = 25;
                $width[3] = $width[4] = $width[5] = 30;
            } elseif ($jenis == 'Summary Kader Training') {
                $width[2]   = $width[3]   = $width[4]   = $width[6]   = $width[8]   = $width[9]   = 25;
                $width[5]   = 30;
                $format[10] = $format[11] = $format[12] = $format[13] = 2;
            } elseif ($jenis == 'Summary CPG Training') {
                $width[2] = $width[3] = $width[6] = $width[7] = $width[8] = $width[9] = 25;
                $width[5] = 30;
            } elseif ($jenis == 'Total Beneficiaries') {
                $width   = array(30, 30);
                $default = 2;
                $format  = array(1, 1);
            } elseif ($jenis == 'Current Farmer Data') {
                $width      = array(30, 30, 25);
                $format[16] = $format[17] = $format[18] = $format[19] = $format[20] = $format[21] = $format[22] = $format[23] =
                $format[24] = $format[25] = $format[26] = $format[27] = $format[28] = $format[29] = 2;
            } elseif ($jenis == 'Labour Data') {
                $width   = array(30, 30);
                $default = 2;
                $format  = array(1, 1);
                $keys    = array_keys($arData[0]);
                for ($i = 0; $i < sizeof($arData); $i++) {
                    for ($j = 1; $j < sizeof($keys); $j++) {
                        $sum[$j] += $arData[$i][$keys[$j]];
                    }
                }
            } elseif ($jenis == 'Nutrisi') {
                $width = array(20, 20, 20, 30, 10, 25, 10, 10, 10);
            } elseif ($jenis == 'PPI') {
                $format[7] = $format[8] = $format[9] = 5;
                $width     = array(20, 20, 20, 30, 10, 25, 20, 10, 50, 50, 20, 50, 50, 10, 10, 10);
            } elseif ($jenis == 'GAP Participants') {
                $width = array(20, 20, 20, 30, 10, 25);
            } elseif ($jenis == 'GNP Participants') {
                $width = array(20, 20, 20, 30, 10, 25, 20, 20);
            } elseif ($jenis == 'GFP Participants') {
                $width = array(20, 20, 20, 30, 10, 25, 20, 20);
            } elseif ($jenis == 'Cumulative GAP Participants') {
                $width = array(20, 20, 20, 30, 10, 25);
            } elseif ($jenis == 'Cumulative GNP Participants') {
                $width = array(20, 20, 20, 30, 10, 25, 20, 20);
            } elseif ($jenis == 'Cumulative GFP Participants') {
                $width = array(20, 20, 20, 30, 10, 25, 20, 20);
            } elseif ($jenis == 'Certification') {
                $width = array(20, 20, 20, 50, 10, 25, 20, 20, 20, 20, 20, 30, 10, 25, 20, 20, 20);
            } elseif ($jenis == 'Certification Summary') {
                $width = array(20, 20, 20, 30, 10, 25, 20, 20);
            } elseif ($jenis == 'Nutrition Summary') {
                $width = array(20, 20, 20, 30, 10, 25, 20, 20);
            }

            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $styleBorder = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            $borderHeader = array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    ),
                ),
            );
            $arr = array_keys($arData[0]);

            $objPHPExcel->getActiveSheet(0)->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $a  = 'A';
            $st = 4;
            for ($i = 0; $i < sizeof($arr); $i++) {
                $objPHPExcel->getActiveSheet(0)->getColumnDimension($a)->setWidth($width[$i] == '' ? 10 : $width[$i]);
                $objPHPExcel->getActiveSheet(0)->setCellValue($a . $st, $arr[$i])
                    ->getStyle($a . $st)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if (($default > 1 and $format[$i] == '') or $format[$i] > 1 or $align[$i] == 'right') {
                    $objPHPExcel->getActiveSheet(0)->getStyle($a . ($st + 1) . ':' . $a . ($st + 1 + sizeof($arData)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                } else {
                    $objPHPExcel->getActiveSheet(0)->getStyle($a . ($st + 1) . ':' . $a . ($st + 1 + sizeof($arData)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                }

                $b = $a;
                $a++;
            }
            for ($i = 0; $i < sizeof($arData); $i++) {
                $a = 'A';
                for ($j = 0; $j < sizeof($arr); $j++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a . ($i + $st + 1), $this->format($arData[$i][$arr[$j]], $format[$j], $default));
                    $a++;
                }
            }
            $d = $i;

            if ($jenis == 'Farmer Detail Data') {
                $nama_file = 'detail_farmer.xls';
            } elseif ($jenis == 'Summary Garden Data') {
                $d++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . ($st + $d) . ':B' . ($st + $d));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($st + $d), 'Total')
                    ->getStyle('A' . ($d + $st))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $a = 'C';
                for ($j = 2; $j < sizeof($arr); $j++) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a . ($st + $d), $this->format($sum[$j], $format[$j], $default));
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($a . ($st + $d))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $a++;
                }
                $objPHPExcel->getActiveSheet(0)->getStyle('A' . ($st + $d) . ':' . $b . ($st + $d))->getFont()->setBold(true);
                $nama_file = 'summary_garden.xls';
            } elseif ($jenis == 'Summary CPG') {
                $nama_file = 'summary_cpg.xls';
            } elseif ($jenis == 'Summary Master Training') {
                $nama_file = 'summary_master_training.xls';
            } elseif ($jenis == 'Summary Kader Training') {
                $nama_file = 'summary_kader_training.xls';
            } elseif ($jenis == 'Summary CPG Training') {
                $nama_file = 'summary_cpg_training.xls';
            } elseif ($jenis == 'Total Beneficiaries') {
                $nama_file = 'total_beneficiaries.xls';
            } elseif ($jenis == 'Current Farmer Data') {
                $nama_file = 'current_farmer.xls';
            } elseif ($jenis == 'Certification') {
                $nama_file = 'certification.xls';
            } elseif ($jenis == 'Certification Summary') {
                $nama_file = 'certification_summary.xls';
            } elseif ($jenis == 'Nutrition Summary') {
                $nama_file = 'nutrition_summary.xls';
            } elseif ($jenis == 'Labour Data') {
                $d++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . ($st + $d) . ':B' . ($st + $d));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($st + $d), 'Total')
                    ->getStyle('A' . ($st + $d3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $a = 'C';
                for ($j = 2; $j < sizeof($arr); $j++) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($a . ($st + $d), $this->format($sum[$j], $format[$j], $default));
                    $a++;
                }
                $objPHPExcel->getActiveSheet(0)->getStyle('A' . ($st + $d) . ':' . $b . ($st + $d))->getFont()->setBold(true);
                $nama_file = 'labour_data.xls';
            } elseif ($jenis == 'Nutrisi') {
                $nama_file = 'nutrisi.xls';
            } elseif ($jenis == 'PPI') {
                $nama_file = 'ppi.xls';
            } elseif ($jenis == 'GAP Participants') {
                $nama_file = 'GAP_Participants.xls';
            } elseif ($jenis == 'GFP Participants') {
                $nama_file = 'GFP_Participants.xls';
            } elseif ($jenis == 'GNP Participants') {
                $nama_file = 'GNP_Participants.xls';
            } elseif ($jenis == 'Cumulative GAP Participants') {
                $nama_file = 'Cumulative_GAP_Participants.xls';
            } elseif ($jenis == 'Cumulative GFP Participants') {
                $nama_file = 'Cumulative_GFP_Participants.xls';
            } elseif ($jenis == 'Cumulative GNP Participants') {
                $nama_file = 'Cumulative_GNP_Participants.xls';
            }

            $objPHPExcel->getActiveSheet()->getStyle("A$st:$b" . ($st + $d))->applyFromArray($styleBorder);

            $objPHPExcel->getActiveSheet(0)->mergeCells('A1' . ':' . $b . '1');
            $objPHPExcel->getActiveSheet(0)->setCellValue('A1', $jenis)
                ->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet(0)->getStyle("A$st:$b$st")->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet(0)->getStyle("A$st:$b$st")->getFill()->getStartColor()->setRGB('A1B4C9');
            $objPHPExcel->getActiveSheet(0)->getStyle("A$st:$b$st")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);

            $objPHPExcel->getActiveSheet(0)->getStyle('A2:' . $b . '2')->applyFromArray($borderHeader);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save($nama_file);
            if ($this->post('jenis') == '') {
                ini_set('memory_limit', $mem_ini);
                return $nama_file;
            }
            $this->response(array('success' => true, 'file' => base_url() . $nama_file), 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

        ini_set('memory_limit', $mem_ini);
    }

    public function generate_excel_get()
    {
        $prov  = ' -- All --';
        $kab   = ' -- All --';
        $jenis = array('Total Beneficiaries', 'Summary Garden Data', 'Nutrisi', 'PPI', 'Summary CPG',
            'Summary Master Training', 'Summary Kader Training');
        for ($i = 0; $i < sizeof($jenis); $i++) {
            $nama = $this->laporan_post($prov, $kab, $jenis[$i], $survey, 'backup');
            rename(getcwd() . '/' . $nama, '/var/www/data/backup_xls/' . date('Ymd_') . $nama);
        }
        header('Location: ' . base_url() . 'copy_file.php');
    }

    public function laporan_get()
    {
        $sesPartner = ($_SESSION['FlagAccess'] > 0) ? $_SESSION['PartnerID'] : 'ALL';
        $kab        = ($this->get('Kabupaten') == '') ? " -- All --" : $this->get('Kabupaten');
        //$stat =
        $data = $this->mreport->readFarmer(
            $this->get('Provinsi'),
            $kab,
            $this->get('jenis'),
            $this->get('Survey'),
            $this->get('trainingDate'),
            $this->get('CertificationType'),
            $this->get('sort'),
            $this->get('start'),
            $this->get('limit'),
            $this->get('LatestSurvey'),
            $stat,
            $sesPartner
        );
        //$data = true;
        if ($data) {
            $keys    = array_keys($data['data'][0]);
            $fields  = array();
            $columns = array();
            switch ($this->get('jenis')) {
                case 'GAP Participants':
                case 'GFP Participants':
                case 'GNP Participants':
                case 'Cumulative GAP Participants':
                case 'Cumulative GFP Participants':
                case 'Cumulative GNP Participants':
                    foreach ($keys as $field) {
                        if ($field == 'FarmerID' ||
                            $field == 'Male' ||
                            $field == 'Female' ||
                            $field == 'Participants' ||
                            $field == 'Family Male' ||
                            $field == 'Family Female') {
                            $tipe      = 'int';
                            $columns[] = array(
                                "text"        => $field,
                                "dataIndex"   => $field,
                                "summaryType" => "sum",
                            );
                        } else {
                            $tipe      = 'string';
                            $columns[] = array(
                                "text"      => $field,
                                "dataIndex" => $field,
                            );
                        }
                        $fields[] = array(
                            "name" => $field,
                            "type" => $tipe,
                        );
                    }
                    break; /*
                case'Certification':

                break; */
                default:
                    $arInteger = array('TotalFarmer', 'TBM', 'TM', 'TR', 'Total Trees', 'Shade Trees',
                        'Total Ha', 'Tree/Ha', 'Shade', 'Production Kg', 'Kg/Ha', 'Ha/Farmer',
                    );
                    foreach ($keys as $field) {
                        if (in_array($field, $arInteger)) {
                            $tipeField = 'int';
                            $columns[] = array(
                                "text"      => $field,
                                "dataIndex" => $field,
                                "align"     => 'right',
                                "renderer"  => 'numberRender',
                            );
                        } else {
                            $tipeField = 'string';
                            $columns[] = array(
                                "text"      => $field,
                                "dataIndex" => $field,
                                "align"     => 'left',
                            );
                        }

                        $fields[] = array(
                            "name" => $field,
                            "type" => $tipeField,
                        );
                        /*
                    $columns[] = array(
                    "text" => $field,
                    "dataIndex" => $field,
                    "align"=>$align,
                    "renderer" => $render
                    //"format"=>$format
                    //,"flex" => 1
                    );
                     *
                     */
                    }
                    break;
            }

            $result = array(
                "count"    => $data['total'],
                "data"     => $data['data'],
                "metaData" => array(
                    "idProperty" => "",
                    "fields"     => $fields,
                    "columns"    => $columns,
                ),
                "success"  => true,
            );
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }

    public function piestore_get()
    {
        $kab        = ($this->get('Kabupaten') == '') ? " -- All --" : $this->get('Kabupaten');
        $sesPartner = ($_SESSION['FlagAccess'] > 0) ? $_SESSION['PartnerID'] : 'ALL';
        $data       = $this->mreport->readChart(
            $this->get('Provinsi'),
            $kab,
            $this->get('jenis'),
            $this->get('Survey'),
            $this->get('trainingDate'),
            $this->get('CertificationType'),
            $this->get('LatestSurvey'),
            $sesPartner
        );

        if ($data) {
            $records = array();
            switch ($this->get('jenis')) {
                case 'GAP Participants':
                case 'GFP Participants':
                case 'GNP Participants':
                case 'Cumulative GAP Participants':
                case 'Cumulative GFP Participants':
                case 'Cumulative GNP Participants':
                    if ($this->get('type') == 'province') {
                        if ($this->get('Provinsi') != ' -- All --') {
                            foreach ($data as $record) {
                                if (array_key_exists($record['District'], $records)) {
                                    $records[$record['District']]['data'] += $record['FarmerID'];
                                } else {
                                    $records[$record['District']] = array(
                                        'name' => $record['District'],
                                        'data' => $record['FarmerID'],
                                    );
                                }
                            }
                        } else {
                            foreach ($data as $record) {
                                if (array_key_exists($record['Province'], $records)) {
                                    $records[$record['Province']]['data'] += $record['FarmerID'];
                                } else {
                                    $records[$record['Province']] = array(
                                        'name' => $record['Province'],
                                        'data' => $record['FarmerID'],
                                    );
                                }
                            }
                        }

                    } else {
                        $records['Female'] = array(
                            'name' => 'Female',
                            'data' => 0,
                        );
                        $records['Male'] = array(
                            'name' => 'Male',
                            'data' => 0,
                        );
                        foreach ($data as $record) {
                            $records['Female']['data'] += $record['Female'];
                            $records['Male']['data'] += $record['Male'];
                        }
                    }
                    break;
                case 'Summary Garden Data':
                    if ($this->get('Provinsi') == ' -- All --') {
                        if ($this->get('type') == 'farmer') {
                            $val = 'TotalFarmer';
                        } else {
                            $val = 'Production Kg';
                        }

                        foreach ($data as $record) {
                            if (array_key_exists($record['Province'], $records)) {
                                $records[$record['Province']]['data'] += $record[$val];
                            } else {
                                $records[$record['Province']] = array(
                                    'name' => $record['Province'],
                                    'data' => $record[$val],
                                );
                            }
                        }
                    } else {
                        if ($this->get('type') == 'farmer') {
                            $val = 'TotalFarmer';
                        } else {
                            $val = 'Production Kg';
                        }

                        foreach ($data as $record) {
                            if (array_key_exists($record['District'], $records)) {
                                $records[$record['District']]['data'] += $record[$val];
                            } else {
                                $records[$record['District']] = array(
                                    'name' => $record['District'],
                                    'data' => $record[$val],
                                );
                            }
                        }
                    }
                    break;
                case 'Certification Summary':
                    if ($this->get('type') == 'totalcert') {
                        if ($this->get('Provinsi') != ' -- All --') {
                            foreach ($data as $record) {
                                if (array_key_exists($record['District'], $records)) {
                                    $records[$record['District']]['data'] += $record['totalcert'];
                                } else {
                                    $records[$record['District']] = array(
                                        'name' => $record['District'],
                                        'data' => $record['totalcert'],
                                    );
                                }
                            }
                        } else {
                            foreach ($data as $record) {
                                if (array_key_exists($record['Province'], $records)) {
                                    $records[$record['Province']]['data'] += $record['totalcert'];
                                } else {
                                    $records[$record['Province']] = array(
                                        'name' => $record['Province'],
                                        'data' => $record['totalcert'],
                                    );
                                }
                            }
                        }

                    } else {
                        $records['Female'] = array(
                            'name' => 'Female',
                            'data' => 0,
                        );
                        $records['Male'] = array(
                            'name' => 'Male',
                            'data' => 0,
                        );
                        foreach ($data as $record) {
                            $records['Female']['data'] += $record['Female'];
                            $records['Male']['data'] += $record['Male'];
                        }
                    }
                    break;
                case 'Nutrition Summary':
                    if ($this->get('type') == 'gnpnutsum') {
                        if ($this->get('Provinsi') != ' -- All --') {
                            foreach ($data as $record) {
                                if (array_key_exists($record['District'], $records)) {
                                    $records[$record['District']]['data'] += $record['total'];
                                } else {
                                    $records[$record['District']] = array(
                                        'name' => $record['District'],
                                        'data' => $record['total'],
                                    );
                                }
                            }
                        } else {
                            foreach ($data as $record) {
                                if (array_key_exists($record['Province'], $records)) {
                                    $records[$record['Province']]['data'] += $record['total'];
                                } else {
                                    $records[$record['Province']] = array(
                                        'name' => $record['Province'],
                                        'data' => $record['total'],
                                    );
                                }
                            }
                        }

                    } else {
                        $records['Female'] = array(
                            'name' => 'Female',
                            'data' => 0,
                        );
                        $records['Male'] = array(
                            'name' => 'Male',
                            'data' => 0,
                        );
                        foreach ($data as $record) {
                            $records['Female']['data'] += $record['Female'];
                            $records['Male']['data'] += $record['Male'];
                        }
                    }
                    break;
            }
            $result = array(
                'success' => true,
                'data'    => array_values($records),
            );
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }

    public function progress_get()
    {
        if ($this->get('Provinsi') == ' -- All --') {
            $prov = '';
        } else {
            $prov = $this->get('Provinsi');
        }

        $data = $this->mreport->readProgress($prov, $this->get('Kabupaten'), $this->get('jenis'),
            $this->get('start'), $this->get('end'));

//         if ($this->get('submit')=='tab') {
        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah harvest',
            'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance', 'Environment baru', 'Ubah environment', 'Village baru', 'Ubah village');

        for ($i = 0; $i < sizeof($keys); $i++) {
            $result['columndata'][$i]['header']    = $keys[$i];
            $result['columndata'][$i]['dataIndex'] = 'name' . $i;
            $result['columndata'][$i]['width']     = ($width[$i] < 1 ? 15 : $width[$i]) * 5.7;
            $result['fielddata'][$i]['name']       = 'name' . $i;
        }

        for ($i = 0; $i < sizeof($data['petani']); $i++) {
            for ($j = 0; $j < 3; $j++) {
                $result['values'][$i]['name' . $j] = $this->format($data['petani'][$i][$keys[$j]], $format[$j], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['garden']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 3)] = $this->format($data['garden'][$i][$keys[$j + 3]], $format[$j + 3], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['post']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 5)] = $this->format($data['post'][$i][$keys[$j + 5]], $format[$j + 5], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['nutrition']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 7)] = $this->format($data['nutrition'][$i][$keys[$j + 7]], $format[$j + 7], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['ppi']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 9)] = $this->format($data['ppi'][$i][$keys[$j + 9]], $format[$j + 9], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['finance']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 11)] = $this->format($data['finance'][$i][$keys[$j + 11]], $format[$j + 11], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['environment']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 13)] = $this->format($data['environment'][$i][$keys[$j + 13]], $format[$j + 13], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['village']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 15)] = $this->format($data['village'][$i][$keys[$j + 15]], $format[$j + 15], $default);
            }
        }

//         } elseif ($this->get('submit')=='graph') {
        for ($i = 0; $i < sizeof($data['petani']); $i++) {
            $result['cat'][]                      = $data['petani'][$i]['Periode'];
            $result['data']['Petani baru'][]      = (int) $data['petani'][$i]['Petani baru'];
            $result['data']['Ubah petani'][]      = (int) $data['petani'][$i]['Ubah petani'];
            $result['data']['Garden baru'][]      = (int) $data['garden'][$i]['Garden baru'];
            $result['data']['Ubah garden'][]      = (int) $data['garden'][$i]['Ubah garden'];
            $result['data']['Harvest baru'][]     = (int) $data['post'][$i]['Harvest baru'];
            $result['data']['Ubah harvest'][]     = (int) $data['post'][$i]['Ubah harvest'];
            $result['data']['Nutrition baru'][]   = (int) $data['nutrition'][$i]['Nutrition baru'];
            $result['data']['Ubah nutrition'][]   = (int) $data['nutrition'][$i]['Ubah nutrition'];
            $result['data']['PPI baru'][]         = (int) $data['ppi'][$i]['PPI baru'];
            $result['data']['Ubah PPI'][]         = (int) $data['ppi'][$i]['Ubah PPI'];
            $result['data']['Finance baru'][]     = (int) $data['finance'][$i]['Finance baru'];
            $result['data']['Ubah finance'][]     = (int) $data['finance'][$i]['Ubah finance'];
            $result['data']['Environment baru'][] = (int) $data['environment'][$i]['Environment baru'];
            $result['data']['Ubah environment'][] = (int) $data['environment'][$i]['Ubah environment'];
            $result['data']['Village baru'][]     = (int) $data['village'][$i]['Village baru'];
            $result['data']['Ubah village'][]     = (int) $data['village'][$i]['Ubah village'];
        }
        //       }

        $result['title']   = 'Progress Report';
        $result['success'] = true;
        //print_r($result);exit;
        if ($data) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function details_get()
    {
        if ($this->get('star') == '') {
            $result = true;
        } else {
            if ($this->get('prov') == ' -- All --') {
                $prov = '';
            } else {
                $prov = $this->get('prov');
            }

            $c    = $this->get('c');
            $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah Harvest',
                'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance');
            $data = $this->mreport->readProgresDetails(
                $this->get('c'),
                $this->get('r'),
                $prov,
                $this->get('kab'),
                $this->get('star'),
                $this->get('en')
            );
            $keys = array_keys($data[0]);
            for ($i = 0; $i < sizeof($keys); $i++) {
                $result['columndata'][$i]['header']    = $keys[$i];
                $result['columndata'][$i]['dataIndex'] = 'name' . $i;
                $result['columndata'][$i]['width']     = (100 / sizeof($keys)) . '%';
                $result['fielddata'][$i]['name']       = 'name' . $i;
            }
            for ($i = 0; $i < sizeof($data); $i++) {
                for ($j = 0; $j < sizeof($keys); $j++) {
                    $result['values'][$i]['name' . $j] = $data[$i][$keys[$j]];
                }
            }
            $result['success'] = true;
        }
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function export_excel_detailsp_get($c, $r, $prov, $kab, $start, $end)
    {
        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah Harvest',
            'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance');
        if ($kab == 'null') {
            $kab = '';
        } else {
            $kab = str_replace('-', ',', $kab);
            $kab = str_replace('_', ' ', $kab);
        }
        $prov = str_replace('_', ' ', $prov);
        if ($prov == 'null') {
            $prov = '';
        }

        $data = $this->mreport->readProgresDetails($c, $r, $prov, $kab, $start, $end);
        $keys = array_keys($data[0]);

        $this->load->library('Excel', null, 'PHPExcel');
        $filename = 'activity_report.xls';
        $this->PHPExcel->filename($filename);

        $sheet['title']    = 'Activity Report';
        $sheet['header'][] = 'Activity Report';

        $sheet['cols'] = array();
        foreach ($keys as $title) {
            $sheet['cols'][] = array(
                'name'  => $title,
                'data'  => $title,
                'size'  => 25,
                'align' => 'left',
                'wrap'  => true,
            );
        }
        $sheet['data'] = $data;

        $path = $this->PHPExcel->create(compact('sheet'), '');
        header('Location: ' . base_url() . $filename);
        exit;
    }

    public function surveys_get()
    {
        if($this->get('jenisSurvey') == "AO"){
            $yearNya = date('Y');
            $data = array();
            $incre = 0;

            for ($i = $yearNya; $i >= ($yearNya - 10); $i--) {
                $data[$incre]['id'] = $i;
                $data[$incre]['label'] = $i;
                $incre++;
            }
        }else{
            $data = $this->mreport->readSurveys($this->get('addLatestSurvey'));
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function staffs_get()
    {
        $data = $this->mreport->readStaffs($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function staff_get()
    {
        $data = $this->mreport->readStaffProgress($this->get('Staff'), $this->get('jenis'),
            $this->get('start'), $this->get('end'));
        //echo '<pre>'; print_r($data); exit;
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        //         if ($this->get('submit')=='tab') {

        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden','Harvest baru', 'Ubah harvest', 'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI','Finance baru', 'Ubah finance','Environment baru', 'Ubah environment', 'Village baru', 'Ubah village');

        for ($i = 0; $i < sizeof($keys); $i++) {
            $result['columndata'][$i]['header']    = $keys[$i];
            $result['columndata'][$i]['dataIndex'] = 'name' . $i;
            $result['columndata'][$i]['width']     = ($width[$i] < 1 ? 15 : $width[$i]) * 5.7;
            $result['fielddata'][$i]['name']       = 'name' . $i;
        }
        for ($i = 0; $i < sizeof($data['petani']); $i++) {
            for ($j = 0; $j < 3; $j++) {
                $result['values'][$i]['name' . $j] = $this->format($data['petani'][$i][$keys[$j]], $format[$j], $default);
            }
        }
        for ($i = 0; $i < sizeof($data['garden']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 3)] = $this->format($data['garden'][$i][$keys[$j + 3]], $format[$j + 3], $default);
            }
        }
        for ($i = 0; $i < sizeof($data['post']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 5)] = $this->format($data['post'][$i][$keys[$j + 5]], $format[$j + 5], $default);
            }
        }
        for ($i = 0; $i < sizeof($data['nutrition']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 7)] = $this->format($data['nutrition'][$i][$keys[$j + 7]], $format[$j + 7], $default);
            }
        }
        for ($i = 0; $i < sizeof($data['ppi']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 9)] = $this->format($data['ppi'][$i][$keys[$j + 9]], $format[$j + 9], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['finance']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 11)] = $this->format($data['finance'][$i][$keys[$j + 11]], $format[$j + 11], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['environment']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 13)] = $this->format($data['environment'][$i][$keys[$j + 13]], $format[$j + 13], $default);
            }
        }

        for ($i = 0; $i < sizeof($data['village']); $i++) {
            for ($j = 0; $j < 2; $j++) {
                $result['values'][$i]['name' . ($j + 15)] = $this->format($data['village'][$i][$keys[$j + 15]], $format[$j + 15], $default);
            }
        }
//         } elseif ($this->get('submit')=='graph') {
        for ($i = 0; $i < sizeof($data['petani']); $i++) {
            $result['cat'][]                    = $data['petani'][$i]['Periode'];
            $result['data']['Petani baru'][]    = (int) $data['petani'][$i]['Petani baru'];
            $result['data']['Ubah petani'][]    = (int) $data['petani'][$i]['Ubah petani'];
            $result['data']['Garden baru'][]    = (int) $data['garden'][$i]['Garden baru'];
            $result['data']['Ubah garden'][]    = (int) $data['garden'][$i]['Ubah garden'];
            $result['data']['Harvest baru'][]   = (int) $data['post'][$i]['Harvest baru'];
            $result['data']['Ubah harvest'][]   = (int) $data['post'][$i]['Ubah harvest'];
            $result['data']['Nutrition baru'][] = (int) $data['nutrition'][$i]['Nutrition baru'];
            $result['data']['Ubah nutrition'][] = (int) $data['nutrition'][$i]['Ubah nutrition'];
            $result['data']['PPI baru'][]       = (int) $data['ppi'][$i]['PPI baru'];
            $result['data']['Ubah PPI'][]       = (int) $data['ppi'][$i]['Ubah PPI'];
            $result['data']['Finance baru'][]   = (int) $data['finance'][$i]['Finance baru'];
            $result['data']['Ubah finance'][]   = (int) $data['finance'][$i]['Ubah finance'];
            $result['data']['Environment baru'][]   = (int) $data['environment'][$i]['Environment baru'];
            $result['data']['Ubah environment'][]   = (int) $data['environment'][$i]['Ubah environment'];
            $result['data']['Village baru'][]   = (int) $data['village'][$i]['Village baru'];
            $result['data']['Ubah village'][]   = (int) $data['village'][$i]['Ubah village'];
        }
        //       }
        $result['title']   = 'Staff Activity Report';
        $result['success'] = true;
        //print_r($result);exit;
        if ($data) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function staff_details_get()
    {
        $c    = $this->get('c');
        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah harvest',
            'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance');

        $data = $this->mreport->readStaffProgresDetails(
            $this->get('c'),
            $this->get('r'),
            $this->get('staff'),
            $this->get('star'),
            $this->get('en')
        );
        $keys = array_keys($data[0]);

        for ($i = 0; $i < sizeof($keys); $i++) {
            $result['columndata'][$i]['header']    = $keys[$i];
            $result['columndata'][$i]['dataIndex'] = 'name' . $i;
            $result['columndata'][$i]['width']     = (100 / sizeof($keys)) . '%';
            $result['fielddata'][$i]['name']       = 'name' . $i;
        }
        for ($i = 0; $i < sizeof($data); $i++) {
            for ($j = 0; $j < sizeof($keys); $j++) {
                $result['values'][$i]['name' . $j] = $data[$i][$keys[$j]];
            }
        }
        $result['success'] = true;

        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    // http://cocoatrace.dev/api/index.php/report/export_excel_details/4/1/187/2014-09-01/2015-05-20
    public function export_excel_details_get($c, $r, $staff, $start, $end)
    {
        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah harvest',
            'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance');
        $staff = str_replace('_', ' ', $staff);
        $data  = $this->mreport->readStaffProgresDetails($c, $r, $staff, $start, $end);
        $keys  = array_keys($data[0]);

        $this->load->library('Excel', null, 'PHPExcel');
        $filename = 'staff_activity_report.xls';
        $this->PHPExcel->filename($filename);

        $sheet['title']    = 'Staff Activity Report';
        $sheet['header'][] = 'Staff Activity Report';

        $sheet['cols'] = array();
        foreach ($keys as $title) {
            $sheet['cols'][] = array(
                'name'  => $title,
                'data'  => $title,
                'size'  => 25,
                'align' => 'left',
                'wrap'  => true,
            );
        }
        $sheet['data'] = $data;

        $path = $this->PHPExcel->create(compact('sheet'), '');
        header('Location: ' . base_url() . $filename);
        exit;
    }

//printout
    public function printout_cpgbatch_get()
    {
        $data = $this->mreport->readPrintoutCpgbatch($this->get('prov'), $this->get('kab'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }
    public function printout_cpg_get()
    {
        $data = $this->mreport->readPrintoutCpg($this->get('prov'), $this->get('kab'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }
    public function printout_petani_get()
    {
        $data = $this->mreport->readPrintoutPetani($this->get('prov'), $this->get('kab'), $this->get('cpgbatch'),
            $this->get('cpg'), $this->get('sert'), $this->get('jenissurvey'), $this->get('survey'), $this->get('jenisform'));
        $this->response($data, 200);
        /*
    if($data) $this->response($data, 200);
    else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
     */
    }
    public function printout_petani_sertifikasi_get()
    {
        $data = $this->mreport->readPrintoutPetaniSertifikasi($this->get('prov'), $this->get('kab'), $this->get('cpg'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }
    public function printout_petani_cpg()
    {
        $data = $this->mreport->readPrintoutPetaniCpg($this->get('cpg'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }
    public function rekap_petani_get()
    {
        $data = $this->mreport->getFarmerCpg($this->get('cpg'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }
    public function printout_list_training_get()
    {
        $data = $this->mreport->readPrintoutListTraining($this->get('prov'), $this->get('kab'), $this->get('cpgbatch'),
            $this->get('cpg'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }
    public function printout_list_learning_get()
    {
        $data = $this->mreport->readPrintoutListLearning($this->get('prov'), $this->get('kab'), $this->get('cpgbatch'),
            $this->get('cpg'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }
    public function printout_list_trader_get()
    {
        $data = $this->mreport->readPrintoutListTrader($this->get('prov'), $this->get('kab'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }

    }

    public function menu_get()
    {
        $data = $this->mreport->readMenu($this->get('kategori'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find menu!'), 404);
        }

    }

    public function year_get()
    {
        $dat = array();
        for ($x = 2010; $x <= date("Y"); $x++) {
            $dat[] = array('label' => $x);
        }
        $year = array(
            'data' => $dat,
        );
        $this->response($year, 200);
    }

    public function batches_get()
    {
        $batches = $this->mreport->getBatch($this->get('kab'));
        $this->response($batches);
    }

    public function cpgs_get()
    {
        $batches = $this->mreport->getCPG($this->get('kab'));
        $this->response($batches);
    }

    public function activity_detail_get()
    {
        $batch_id = $this->get('batch');
        $cpg_id   = $this->get('cpg');
        $data     = $this->mreport->getActivityDetail($batch_id, $cpg_id, $this->get('start'), $this->get('limit'));
        $this->response($data);
    }

    public function activity_detail_excel_get($batch_id, $cpg_id = '')
    {
        //load bahasa
        $this->load->language('general', $this->get('lang'));

        $start = 0;
        $limit = 100000;
        if ($cpg_id == 'null') {
            $cpg_id = '';
        } else {
            $cpg_id = urldecode($cpg_id);
        }
        $result         = $this->mreport->getActivityDetail($batch_id, $cpg_id, $start, $limit);
        $data['detail'] = $result['data'];

        $batch_detail = $this->mreport->getBatchDetail($batch_id);
        if (!empty($cpg_id)) {
            $cpg_detail = $this->mreport->getCPGDetail($cpg_id);
        }

        $this->load->library('Excel', null, 'PHPExcel');
        $filename = 'Report Activity.xls';
        $this->PHPExcel->filename($filename);

        // data modification
        foreach ($data['detail'] as $key => $value) {
            $data['detail'][$key]['Photo'] = !empty($value['Photo']) ? 'Yes' : 'No';
        }
        // echo '<pre>'; print_r($data); echo '</pre>';exit;
        // sheet
        $sheet['title']    = 'Activity';
        $sheet['header'][] = 'Activity Report';
        $sheet['header'][] = $batch_detail['BatchNumber'] . ' - ' . $batch_detail['PartnerName'];
        if (!empty($cpg_detail)) {
            foreach ($cpg_detail as $key => $value) {
                $sheet['header'][] = $value['CPGid'] . ' - ' . $value['GroupName'];
            }
        }
        $sheet['cols'] = array(
            // array(
            //     'name' => 'Header Column',   // Teks header table
            //     'data' => 'data_key',        // index key dari data
            //     'size' => 5,                 // size
            //     'align' => 'center'          // horizontal alignment
            //     'wrap' => true,              // wrap if too long
            //     'type' => 'text',            // set type to text, misal untuk menampilkan nomor telp 08637263872
            // ),
            // array(
            //     'name' => 'No',
            //     'data' => 'no',
            //     'size' => 5,
            //     'align' => 'center'
            // ),
            array(
                'name'  => lang('Farmer ID'),
                'data'  => 'FarmerID',
                'size'  => 15,
                'align' => 'left',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Farmer Name'),
                'data'  => 'FarmerName',
                'size'  => 30,
                'align' => 'left',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Village'),
                'data'  => 'Village',
                'size'  => 23,
                'align' => 'left',
            )
            , array(
                'name'  => lang('SurveyNr'),
                'data'  => 'SurveyNr',
                'size'  => 10,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('FarmerPhoto'),
                'data'  => 'Photo',
                'size'  => 10,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Family Number'),
                'data'  => 'FamilyNumber',
                'size'  => 10,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Farmer'),
                'data'  => 'Farmer',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Kebun'),
                'data'  => 'Garden',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Paska Panen'),
                'data'  => 'PostHarvest',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Nutrition'),
                'data'  => 'Nutrition',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => 'PPI',
                'data'  => 'PPI',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Finance'),
                'data'  => 'GFP',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('Environment'),
                'data'  => 'Environment',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => 'GPS',
                'data'  => 'GPS',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            )
            , array(
                'name'  => lang('FieldStaff'),
                'data'  => 'NamaFF',
                'size'  => 12,
                'align' => 'center',
                // 'wrap' => true,
                // 'type' => 'text',
            ),
        );
        $sheet['data'] = $data['detail'];

        $path = $this->PHPExcel->create(compact('sheet'));
    }

    public function certification_period_get()
    {
        $warehouseId = $this->get('wh');

        $data = $this->mreport->getCertificationCycle($warehouseId);

        $this->response($data, 200);
    }

    public function farmer_summary_list_get($prov = '', $dist = '', $subd = '', $cpg = '')
    {
        echo "<pre>";
        print_r($this->get(null));
        echo "</pre>";exit;
    }

    public function printout_excel_get($start, $end)
    {
        $data = $this->mreport->getImport($start, $end);
//print_r($data);exit;
        $filename = "import_report_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $data['start'] = $start;
        $data['end']   = $end;

        $this->load->view('report_import', $data);
    }

    public function cooperatives_get()
    {
        $Province = $this->get('key');
        $data     = $this->mreport->getCooperatives($Province);
        $this->response($data, 200);
    }

    public function po_warehouse_get()
    {
        $data = $this->mreport->readPoWarehouse();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function po_periode_get()
    {
        $data = $this->mreport->readPoPeriode($this->get('wh'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function po_excel_get($wh, $sert)
    {
        $periode      = explode(' s.d. ', str_replace('%20', ' ', $sert));
        $data['data'] = $this->mreport->getPoExcel($wh, $periode[0], $periode[1]);
        $filename     = "import_report_po_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $data['start'] = $periode[0];
        $data['end']   = $periode[1];

        $this->load->view('report_import_po', $data);
    }
    public function cetak_certified_traceability_get()
    {
        $ProvinceID  = $this->get('ProvinceID');
        $WarehouseID = $this->get('WarehouseID');

        $warehouse = $this->mreport->getWarehouseDetail($WarehouseID);
        $data      = $this->mreport->getCertifiedTraceability($ProvinceID, $WarehouseID);
        $cycle     = $this->mreport->getWarehouseCertification($WarehouseID);

        $filename = "certified_traceability_{$warehouse['WarehouseName']}.xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $this->load->view('cetak_certified_traceability', compact('data', 'warehouse', 'cycle'));
    }

    //rekap
    public function rekap_koperasi_get()
    {
        $data = $this->mreport->readRekapKoperasi($this->get('WarehouseID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function rekap_bs_get()
    {
        $data = $this->mreport->readRekapBs($this->get('CoopID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function rekap_excel_get($wh, $coop, $bs, $status, $awal, $akhir)
    {
        $data['data'] = $this->mreport->getRekapExcel($wh, $coop, $bs, $status, $awal, $akhir);
        $filename     = "import_report_rekap_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $data['start'] = $awal;
        $data['end']   = $akhir;

        $this->load->view('report_import_rekap', $data);
    }

    public function purchase_warehouses_get()
    {
        $data = $this->mreport->readPurchaseWarehouses();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function purchase_cooperatives_get()
    {
        $data = $this->mreport->readPurchaseCooperatives($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function purchase_buying_stations_get()
    {
        $data = $this->mreport->readPurchaseBuyingStations($this->get('warehouse'), $this->get('coop'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function printout_excel_purchase_get($warehouse, $coop, $bs, $status, $start, $end)
    {
        $data     = $this->mreport->getPurchase($warehouse, $coop, $bs, $status, $start, $end);
        $filename = "transaksi_pembelian_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $data['start'] = $start;
        $data['end']   = $end;
        $this->load->view('report_import_purchase', $data);
    }

    public function unit_get()
    {
        $data = $this->mreport->readUnits($this->get('District'), $this->get('query'), $this->get('page'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function batch_get()
    {
        $data = $this->mreport->readBatchs($this->get('SupplychainID'), $this->get('query'), $this->get('page'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    /**
     * Fetching field-field yang di query;
     * @author ardiantoro@koltiva.com
     * @param  String $subject  string jenis laporan yang ingin di generate
     * @return Json             json hasil fetch field2 di view
     */
    public function getcols_get($subject = false)
    {

        $data = $this->mreport->getCols($subject);

        if ($data) {$this->response($data, 200);} else { $this->response(array('error' => 'Couldn\'t find any data!'), 404);}
    }

    public function generatedata_get()
    {
        $cols    = json_decode($this->get('cols'), true);
        $subject = $this->get('type');
        $xls     = $this->get('xls');
        $data    = $this->mreport->generateData($subject);

        if ($data) {
            if ($xls == 'true') {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=exceldata.xls");
                header("Pragma: no-cache");
                header("Expires: 0");
                $this->load->view('report_cooperative', array('data' => $data, 'cols' => $cols));
            } else {
                $this->response($data, 200);
            }
        } else { $this->response(array('error' => 'Couldn\'t find any data!'), 404);}
    }

    public function traceabilitysync_get()
    {
        $this->load->model('report/mreport_traceability_sync','_report');
        $batch_date = strtotime($this->get('batch_date'));
        if ($batch_date) {
            $batch_date = date('Y-m-d', $batch_date);
        }


        $batch_number  = $this->get('batch_number');
        $faktur_number = $this->get('FakturNumber');
        $farmer        = $this->get('FarmerID');
        $batch_from    = $this->get('batch_date_from');
        $batch_to      = $this->get('batch_date_to');
        $district      = $this->get('districtid');
        $subdistrict   = $this->get('subdistrict');
        $village       = $this->get('village');
        $trans_from    = $this->get('trans_date_from');
        $trans_to      = $this->get('trans_date_to');
        $nopo          = $this->get('DestPO');
        $orgid         = (int)$this->get('orgid');

        $limit         = $this->get('limit');
        $start         = $this->get('start');
        $xls           = $this->get('xls');

        $data          = $this->_report->get_data_traceability_sync($limit, $start, $xls, $orgid, $batch_from, $batch_to, $trans_from, $trans_to,
                                                                    $district, $subdistrict, $village, $batch_number, $nopo, $farmer);

        if ($data) {

            if ($xls == 'true') {
                ini_set('memory_limit',-1);
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=exceldata.xls");
                header("Pragma: no-cache");
                header("Expires: 0");

                $this->load->view('report_traceability_sync', array('data' => $data));

            } else {
                $this->response($data, 200);
            }

        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }

    }

    public function Koperasi_get()
    {
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        $data = $this->mreport->readKoperasi($this->get('wh'), $dStart[0], $dEnd[0]);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function BuyingUnit_get()
    {
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        $data = $this->mreport->readBuyingUnit($this->get('wh'), $dStart[0], $dEnd[0]);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function Farmer_get()
    {
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        $data = $this->mreport->readFarmerTraceability($this->get('wh'), $dStart[0], $dEnd[0]);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function reportmember_get(){
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = '';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = $this->input->get();

        $data = $this->mreport->getMemberReport($start,$limit,$sort,$dir,$filter);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function membertransactions_get(){
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = 'mt.MemberTransactionDate';
        $dir = 'DESC';

        $filter = array();
        if($this->input->get('trxDate') != ''){ $filter['mt.MemberTransactionDate'] = $this->input->get('trxDate'); }
        if($this->input->get('trxNo') != ''){ $filter['mt.MemberTransactionNumber'] = $this->input->get('trxNo'); }
        if($this->input->get('memberNo') != ''){ $filter['m.primaryNo'] = $this->input->get('memberNo'); }
        if($this->input->get('savingType') != ''){ $filter['st.savingTypeID'] = $this->input->get('savingType'); }
        if($this->input->get('trxType') != ''){ $filter['mt.MemberTransactionType'] = $this->input->get('trxType'); }
        if($this->input->get('cashboxName') != ''){ $filter['cs.cashSourceName'] = $this->input->get('cashboxName'); }

        $data = $this->mreport->getMemberTransactions($start,$limit,$sort,$dir,$filter);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function operationaltransactions_get(){
        $qstart = $this->input->get('start');
        $start = ($qstart == 0) ? '0' : $qstart;

        $limit = $this->input->get('limit');
        $sort = 't.transactionDate';
        $dir = 'DESC';

        $filter = array();
        if($this->input->get('trxDate') != ''){ $filter['t.transactionDate'] = $this->input->get('trxDate'); }
        if($this->input->get('trxNo') != ''){ $filter['t.transactionNumber'] = $this->input->get('trxNo'); }
        if($this->input->get('trxType') != ''){ $filter['t.transactionType'] = $this->input->get('trxType'); }
        if($this->input->get('cashboxName') != ''){ $filter['cs.cashSourceName'] = $this->input->get('cashboxName'); }
        if($this->input->get('coa') != ''){ $filter['c.coaCode'] = $this->input->get('coa'); }
        if($this->input->get('trxName') != ''){ $filter['t.transactionName'] = $this->input->get('trxName'); }

        $data = $this->mreport->getOperationalTransactions($start,$limit,$sort,$dir,$filter);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function combo_savingtype_get(){
        $data = $this->mreport->getComboSavingType();

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any record!'), 404);
    }

    public function combo_coa_get(){
        $query = (strlen($this->input->get('query')) >= 1) ? $this->input->get('query') : '';
        $data = $this->mreport->getComboCOA($query);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any record!'), 404);
    }

    public function combo_district_get(){
        $query = (strlen($this->input->get('query')) >= 1) ? $this->input->get('query') : '';

        $data = $this->mreport->getComboDistrict($query);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any record!'), 404);
    }

    public function combo_subdistrict_get(){
        $DistID = ($this->input->get('DistrictID') != '') ? $this->input->get('DistrictID') : 0;
        $query = (strlen($this->input->get('query')) >= 1) ? $this->input->get('query') : '';

        $data = $this->mreport->getComboSubDistrict($DistID, $query);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any record!'), 404);
    }

    public function combo_village_get(){
        $SubDistID = ($this->input->get('SubDistrictID') != '') ? $this->input->get('SubDistrictID') : 0;
        $query = '';
        // $query = (strlen($this->input->get('query')) >= 1) ? $this->input->get('query') : '';

        $data = $this->mreport->getComboVillage($SubDistID, $query);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any record!'), 404);
    }

} //end of class