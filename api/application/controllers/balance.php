<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Rest API for Reference 
 */

/**
 * @package API
 * @author hariardi@gmail.com
 */
class Balance extends REST_Controller {

    function __construct() {
        parent::__construct();
        ini_set('display_errors',true);
        error_reporting(E_ALL);

        $this->load->model('accounting/mbalance', '_model');
        $this->load->model('accounting/mpnl', '_model_pnl');
        $this->load->model('accounting/mlaporan');
    }

    public function createexcel_get($smonth = false, $emonth = false, $year = false) {
        $starting = 1;

        $months = $this->output_balance($smonth, $emonth, $year);
        $count = array();
        $whole = array();

        for ($starting; $starting <= $emonth; $starting++) {
            array_push($whole, $starting);
        }

        for ($smonth; $smonth <= $emonth; $smonth++) {
            array_push($count, $smonth);
        }

        foreach ($whole as $keyc) {
            $start_date = date('Y-m-d', strtotime($year . '-' . $keyc . '-01'));
            $end_date = date('Y-m-t', strtotime($start_date));
            if ($keyc > 1) {
                $totalpnl[$keyc] = $totalpnl[$keyc - 1] + $this->_model_pnl->getTotalPnl($start_date, $end_date);
            } else {
                $totalpnl[$keyc] = $this->_model_pnl->getTotalPnl($start_date, $end_date);
            }
        }

        $data['period'] = ''; //date('d M Y',strtotime($start_date)) . ' to ' . date('d M Y',strtotime($end_date));
        $data['output'] = $months;
        $data['months'] = $count;
        $data['totalpnl'] = $totalpnl;
        $data['border'] = 1;
        $filename = "balance_sheet_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $this->load->view('balancesheet', $data);
    }

    public function generate2_post()
    {
        $this->load->model('accounting/mbalance','_balance');
        
        $parent_group_aktiva                     = $this->_balance->getCoaGroupParent(1);
        $parent_group_kewajiban                  = $this->_balance->getCoaGroupParent(2);
        $parent_group_modal                      = $this->_balance->getCoaGroupParent(3);
        $parent_group_pend_usaha                 = $this->_balance->getCoaGroupParent(4);
        $parent_group_harga_pokok_penjualan      = $this->_balance->getCoaGroupParent(5);
        $parent_group_beban_usaha                = $this->_balance->getCoaGroupParent(6);
        
        $data = array(
            'parent_group_aktiva'                => $parent_group_aktiva,
            'parent_group_kewajiban'             => $parent_group_kewajiban,
            'parent_group_modal'                 => $parent_group_modal,
            'parent_group_pend_usaha'            => $parent_group_pend_usaha,
            'parent_group_harga_pokok_penjualan' => $parent_group_harga_pokok_penjualan,
            'parent_group_beban_usaha'           => $parent_group_beban_usaha
        );
        
        //echo '<pre>';
        //var_dump($data);die;
        
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-type:   application/x-msexcel; charset=utf-8");
        header("Content-Disposition: attachment;filename=dataexcel.xls"); //tell browser what's the file name
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        
        $this->load->view('balancesheet', $data);

    }

    public function generate2_get($xls = false) {
        $START_MONTH_ID = $this->get('START_MONTH_ID');
        $END_MONTH_ID = $this->get('END_MONTH_ID');
        $YEAR_ID = $this->get('YEAR_ID');

        $this->load->model('accounting/mbalance','_balance');
        
        $parent_group_aktiva                     = $this->_balance->getCoaGroupParent(1);
        $parent_group_kewajiban                  = $this->_balance->getCoaGroupParent(2);
        $parent_group_modal                      = $this->_balance->getCoaGroupParent(3);
        $parent_group_pend_usaha                 = $this->_balance->getCoaGroupParent(4);
        $parent_group_harga_pokok_penjualan      = $this->_balance->getCoaGroupParent(5);
        $parent_group_beban_usaha                = $this->_balance->getCoaGroupParent(6);
        
        $data = array(
            'parent_group_aktiva'                => $parent_group_aktiva,
            'parent_group_kewajiban'             => $parent_group_kewajiban,
            'parent_group_modal'                 => $parent_group_modal,
            'parent_group_pend_usaha'            => $parent_group_pend_usaha,
            'parent_group_harga_pokok_penjualan' => $parent_group_harga_pokok_penjualan,
            'parent_group_beban_usaha'           => $parent_group_beban_usaha,
            'start_month'                        => $START_MONTH_ID,
            'end_month'                          => $END_MONTH_ID,
            'year'                               => $YEAR_ID
        );
        
        if($xls){
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-type:   application/x-msexcel; charset=utf-8");
            header("Content-Disposition: attachment;filename=dataexcel.xls"); //tell browser what's the file name
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        
            return $this->generateExcel($data);
        }
        echo $this->db->last_query();exit;
        $this->load->view('balancesheet', $data);
    }

    protected function generateExcel($data) {
        
        extract($data);
        
        $totalrows_side_left = count($parent_group_aktiva);
        $totalrows_side_right = count($parent_group_kewajiban) + count($parent_group_modal);
        $totalaktiva = 0;
        $totalpasiva = 0;

        foreach($parent_group_aktiva as $key_aktiva => $aktiva) {
            $totalrows_side_left++;
        }

        foreach($parent_group_kewajiban as $key_kewajiban => $kewajiban) {
            $totalrows_side_right++;
        }

        foreach($parent_group_modal as $key_modal => $modal) {
            $totalrows_side_right++;
        }

        $needrows = $totalrows_side_left - $totalrows_side_right;
        
        
    
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        
        //Merge Cell for title
        $objPHPExcel->getActiveSheet()->mergeCells("A1:F1");
        $objPHPExcel->getActiveSheet()->mergeCells("A2:F2");
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Laporan Neraca');
        
        //Kolom Aktiva
        $objPHPExcel->getActiveSheet()->mergeCells("A3:C3");
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', 'AKTIVA');
        $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'KODE');
        $objPHPExcel->getActiveSheet()->SetCellValue('B4', 'DESKRIPSI');
        $objPHPExcel->getActiveSheet()->SetCellValue('C4', 'JUMLAH');
        
        //Kolom Pasiva
        $objPHPExcel->getActiveSheet()->mergeCells("D3:F3");
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', 'PASIVA');
        $objPHPExcel->getActiveSheet()->SetCellValue('D4', 'KODE');
        $objPHPExcel->getActiveSheet()->SetCellValue('E4', 'DESKRIPSI');
        $objPHPExcel->getActiveSheet()->SetCellValue('F4', 'JUMLAH');
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        
        $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 12
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ));

        
        $objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($styleArray);
        
        
        $iaktiva = 5;
        $ipasiva = 5;
        
        //begin with the datarow
        foreach($parent_group_aktiva as $key_aktiva => $aktiva) {
            $iaktiva++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$iaktiva, $aktiva['CoaGroupCode']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$iaktiva, $aktiva['CoaGroupTitle']);
            
            if(count($aktiva['children']) > 0) {
                foreach($aktiva['children'] as $akey => $aktivachild) {
                    $totalaktiva += $aktivachild['saldo'];
                    $iaktiva++;
                    
                    $aktivachild['saldo'] = $aktivachild['saldo']<0?'('.number_format(abs($aktivachild['saldo']),2).')':number_format($aktivachild['saldo'],2);
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$iaktiva, $aktivachild['CoaGroupCode']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$iaktiva, $aktivachild['CoaGroupTitle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$iaktiva, $aktivachild['saldo']);
                }
            }
        }
        
        foreach($parent_group_kewajiban as $key_kewajiban => $kewajiban) {
            $ipasiva++;
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$ipasiva, $kewajiban['CoaGroupCode']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$ipasiva, $kewajiban['CoaGroupTitle']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$ipasiva, '');
            
            if(count($kewajiban['children']) > 0) {
                foreach($kewajiban['children'] as $akey => $kewajibanchild) {
                    $totalpasiva += $kewajibanchild['saldo'];
                    $ipasiva++;
                    
                    $kewajibanchild['saldo'] = $kewajibanchild['saldo']<0?'('.number_format(abs($kewajibanchild['saldo']),2).')':number_format($kewajibanchild['saldo'],2);
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$ipasiva, $kewajibanchild['CoaGroupCode']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$ipasiva, $kewajibanchild['CoaGroupTitle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$ipasiva, $kewajibanchild['saldo']);
                }
            }
        }
        
        foreach($parent_group_modal as $key_modal => $modal) {
            $ipasiva++;
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$ipasiva, $modal['CoaGroupCode']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$ipasiva, $modal['CoaGroupTitle']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$ipasiva, '');
            
            if(count($modal['children']) > 0) {
                foreach($modal['children'] as $akey => $modalchild) {
                    
                    $ipasiva++;
                    
                    $modalchild['saldo'] = $modalchild['saldo']<0?'('.number_format(abs($modalchild['saldo']),2).')':number_format($modalchild['saldo'],2);
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$ipasiva, $modalchild['CoaGroupCode']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$ipasiva, $modalchild['CoaGroupTitle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$ipasiva, $modalchild['saldo']);
                }
            }
        }
        
        $totalrow = $iaktiva > $ipasiva?$iaktiva:$ipasiva;
        
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$totalrow, '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$totalrow, 'Total ');
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$totalrow, $totalaktiva);

        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$totalrow, '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$totalrow, 'Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$totalrow, $totalpasiva);
                    
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Neraca');


        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        //ob_get_clean();
        $objWriter->save('php://output');
        //ob_end_flush();
    }
    
    public function generate_post() {
        $starting = 1;
        $smonth = $this->input->post('START_MONTH_ID'); // start
        $emonth = $this->input->post('END_MONTH_ID'); // end
        $year = $this->input->post('YEAR_ID');

        $months = $this->output_balance($smonth, $emonth, $year);
        $count = array();
        $whole = array();

        for ($starting; $starting <= $emonth; $starting++) {
            array_push($whole, $starting);
        }

        for ($smonth; $smonth <= $emonth; $smonth++) {
            array_push($count, $smonth);
        }
        
        foreach ($whole as $keyc) {
            $start_date = date('Y-m-d', strtotime($year . '-' . $keyc . '-01'));
            $end_date = date('Y-m-t', strtotime($start_date));
            if ($keyc > 1) {
                $totalpnl[$keyc] = $totalpnl[$keyc - 1] + $this->_model_pnl->getTotalPnl($start_date, $end_date);
            } else {
                $totalpnl[$keyc] = $this->_model_pnl->getTotalPnl($start_date, $end_date);
            }
        }

        $data['period'] = ''; //date('d M Y',strtotime($start_date)) . ' to ' . date('d M Y',strtotime($end_date));
        $data['output'] = $months;
        $data['months'] = $count;
        $data['totalpnl'] = $totalpnl;
        $data['border'] = 0;
        $this->load->view('balancesheet', $data);
    }

   

    public function output_balance($start_month, $end_month, $year) {

        $starting_month = 1;
        $whole_months = array();
        $coa = array();

        for ($starting_month; $starting_month <= $end_month; $starting_month++) {
            array_push($whole_months, $starting_month);
        }
        $pl_class = $this->_model->get_pl_class();
        $count = array();

        for ($start_month; $start_month <= $end_month; $start_month++) {
            array_push($count, $start_month);
        }

        foreach ($pl_class as $key => $value) {

            $children = $this->_model->get_coa($value['coaClassID']);
            if ($children) {
                array_push($coa, $children);
            }
        }
        foreach ($coa as $ckey => $childs) {
            foreach ($childs as $childsk => $child) {
                foreach ($child['CHILDREN'] as $cckey => $cval) {
                    foreach ($whole_months as $keyc) {

                        $start_date = date('Y-m-d', strtotime($year . '-' . $keyc . '-01'));
                        $end_date = date('Y-m-t', strtotime($start_date));
                        $coatype = $this->_model->getCoaType($cval['coaCode']);
                        if ($keyc == 1) {
                            $balance = $this->_model->getCoaBalance($cval['coaCode']);
                        } else {
                            $balance = $coa[$ckey][$childsk]['CHILDREN'][$cckey]['VALUE'][($keyc - 1)]['SALDO'];
                        }

                        $debet = $this->_model->getJournalAmountByCoa($cval['coaCode'], $type = 1, $start_date, $end_date);
                        $kredit = $this->_model->getJournalAmountByCoa($cval['coaCode'], $type = 2, $start_date, $end_date);

                        //var_dump($totalpnl);die;
                        //$coa[$ckey][$childsk]['CHILDREN'][$cckey]['TOTAL_PNL'][$keyc] = $totalpnl;
                        if (true) {

                            if ($cval['coaType'] == 1) {

                                $sum = $balance + ($debet - $kredit);
                                if ($keyc > 1) {
                                    //$sum = $balance;
                                }
                                $coa[$ckey][$childsk]['CHILDREN'][$cckey]['VALUE'][$keyc] = array(
                                    'DEBET' => $sum,
                                    'KREDIT' => 0,
                                    'SALDO' => $sum
                                );
                            }

                            if ($cval['coaType'] == 2) {
                                //-21,591,500.00 + (0 - 21,591,500.00)
                                $sum = $balance + ($kredit - $debet);
                                if ($keyc > 1) {
                                    //$sum = $balance;
                                }
                                $coa[$ckey][$childsk]['CHILDREN'][$cckey]['VALUE'][$keyc] = array(
                                    'DEBET' => 0,
                                    'KREDIT' => $sum,
                                    'SALDO' => $sum
                                );
                            }
                        } else {
                            $coa[$ckey][$childsk]['CHILDREN'][$cckey]['VALUE'][$keyc] = array(
                                'DEBET' => 0,
                                'KREDIT' => 0,
                                'SALDO' => 0
                            );
                        }
                    }
                }
            }
        }

        return $coa;
    }

}
