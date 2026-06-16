<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends REST_Controller {

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct() {
        parent::__construct();
        $this->load->model('mmigration', '_migration');
    }

    public function traceability_transaction_get() {

        $batchid = $this->get('batchid');
        $batchstartdate = strtotime($this->get('startdate'));
        $batchenddate = strtotime($this->get('enddate'));

        $data = $this->_migration->get_transaction($batchid, $batchstartdate, $batchenddate);

        foreach ($data as $key => $value) {

            $migrate = $this->_migration->migrate_transaction($value);

            if ($migrate) {
                echo 'Migrasi data ' . $value['SupplyBatchNumber'] . ' <b style="color:green">Berhasil</b><br/>';
            }
        }

        die("Done!");
    }
    
    private function _getBatchFromSupply($suratjalan,$faktur) {
        $this->db->select('SupplyBatchID');
        $this->db->from('ktv_supplychain_transaction');
        $this->db->where('FakturNumber',$faktur);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            if(strlen($row->SupplyBatchID) > 0) {
                return $row->SupplyBatchID;
            }
        }
        
        return false;
    }
    
    public function traceabilitysoppengnopo_get() {
        
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $this->load->helper('directory');


        $src = directory_map('migrasi/migrasinopo');
        foreach ($src as $srcindex => $srcfile) {

            $data = array();
            $inputFileName = 'migrasi/migrasinopo/' . $srcfile;

            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            //var_dump($inputFileType);die;
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
                
            $a = 0;
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                
                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $nrColumns = ord($highestColumn) - 64;
                for ($row = 1; $row <= $highestRow; ++$row) {
                    $col = 1;
                    $suratjalan = $worksheet->getCellByColumnAndRow(0, $row);
                    $faktur = $worksheet->getCellByColumnAndRow(1, $row);
                    $batch = $this->_getBatchFromSupply($suratjalan,$faktur);
                    
                    if($batch) {
                        $this->db->set('DestPO',$suratjalan);
                        $this->db->where('SupplyBatchID',$batch);
                        $this->db->update('ktv_supplychain_batch');
                        
                    }
                    
                    echo '<p>' . $row . ' faktur: ' . $faktur . '; batch: ' . $batch . '</p>';
                    
                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, "'".$batch);
                    /*
                      $faktur         = $worksheet->getCellByColumnAndRow(0, $row);
                      $celldate       = $worksheet->getCellByColumnAndRow(4, $row);
                      $cellmonth      = $worksheet->getCellByColumnAndRow(3, $row);
                      $cellyear       = $worksheet->getCellByColumnAndRow(2, $row);
                      $transdate      = date('Y-m-d',strtotime($celldate->getValue().' '.$cellmonth->getValue().' '.$cellyear->getValue()));
                      $farmer         = $worksheet->getCellByColumnAndRow(5, $row);
                      $orgid          = $worksheet->getCellByColumnAndRow(1, $row);
                      $bruto          = $worksheet->getCellByColumnAndRow(12, $row);
                      $netto          = $worksheet->getCellByColumnAndRow(18, $row);
                      $moisture       = $worksheet->getCellByColumnAndRow(13, $row);
                      $bc             = $worksheet->getCellByColumnAndRow(14, $row);
                      $waste          = $worksheet->getCellByColumnAndRow(15, $row);
                      $contract_price = $worksheet->getCellByColumnAndRow(16, $row);
                      $net_price      = $worksheet->getCellByColumnAndRow(17, $row);
                      $total          = $worksheet->getCellByColumnAndRow(19, $row);

                      $data[] = array(
                      'date'            =>  $celldate->getValue(),
                      'month'           =>  $cellmonth->getValue(),
                      'year'            =>  $cellyear->getValue(),
                      'faktur'          =>  $faktur->getValue(),
                      'date'            =>  $transdate,
                      'farmer'          =>  $farmer->getValue(),
                      'orgid'           =>  $pedagang[substr($orgid->getValue(),0,5)],
                      'bruto'           =>  $bruto->getValue(),
                      'netto'           =>  $netto->getValue(),
                      'moisture'        =>  $moisture->getValue(),
                      'bc'              =>  $bc->getValue(),
                      'waste'           =>  $waste->getValue(),
                      'contract_price'  =>  $contract_price->getValue(),
                      'net_price'       =>  $net_price->getValue(),
                      'total'           =>  $total->getValue(),
                      );
                     */
                }
                
                $objWriter->save('result.xlsx');
            }


            //echo '<span>file: ' . $srcfile . ', No. Batch: ' . $batch['SupplyBatchNumber'] . ', ID Batch: ' . $batch['SupplyBatchID'] . '</span> <br />';
            //echo json_encode($batch);
        }

        echo 'Done!';
        die;
    }

    public function traceabilitysoppeng_get() {

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $this->load->helper('directory');


        $src = directory_map('migrasi');

        foreach ($src['migrasi'] as $srcindex => $srcfile) {

            $data = array();
            $inputFileName = 'migrasi/migrasi/' . $srcfile;

            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            //var_dump($inputFileType);die;
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);

            $a = 0;
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $nrColumns = ord($highestColumn) - 64;
                $pedagang = array(
                    '40072' => '30',
                    '40073' => '4',
                    '40074' => '28',
                    '40075' => '29',
                    '40076' => '46'
                );
                $orgid = '';
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $col = 1;
                    $faktur = $worksheet->getCellByColumnAndRow(0, $row);
                    $celldate = $worksheet->getCellByColumnAndRow(4, $row);
                    $cellmonth = $worksheet->getCellByColumnAndRow(3, $row);
                    $cellyear = $worksheet->getCellByColumnAndRow(2, $row);
                    $transdate = date('Y-m-d', strtotime($celldate->getValue() . ' ' . $cellmonth->getValue() . ' ' . $cellyear->getValue()));
                    $farmer = $worksheet->getCellByColumnAndRow(5, $row);
                    $orgid = $worksheet->getCellByColumnAndRow(1, $row);
                    $bruto = $worksheet->getCellByColumnAndRow(12, $row);
                    $netto = $worksheet->getCellByColumnAndRow(18, $row);
                    $moisture = $worksheet->getCellByColumnAndRow(13, $row);
                    $bc = $worksheet->getCellByColumnAndRow(14, $row);
                    $waste = $worksheet->getCellByColumnAndRow(15, $row);
                    $contract_price = $worksheet->getCellByColumnAndRow(16, $row);
                    $net_price = $worksheet->getCellByColumnAndRow(17, $row);
                    $total = $worksheet->getCellByColumnAndRow(19, $row);

                    $data[] = array(
                        'date' => $celldate->getValue(),
                        'month' => $cellmonth->getValue(),
                        'year' => $cellyear->getValue(),
                        'faktur' => $faktur->getValue(),
                        'date' => $transdate,
                        'farmer' => $farmer->getValue(),
                        'orgid' => $pedagang[substr($orgid->getValue(), 0, 5)],
                        'bruto' => $bruto->getValue(),
                        'netto' => $netto->getValue(),
                        'moisture' => $moisture->getValue(),
                        'bc' => $bc->getValue(),
                        'waste' => $waste->getValue(),
                        'contract_price' => $contract_price->getValue(),
                        'net_price' => $net_price->getValue(),
                        'total' => $total->getValue(),
                    );
                    $orgid = $pedagang[substr($orgid->getValue(), 0, 5)];
                }
            }

            $batch = $this->_createBatch($data, $orgid);
            echo '<span>file: ' . $srcfile . ', No. Batch: ' . $batch['SupplyBatchNumber'] . ', ID Batch: ' . $batch['SupplyBatchID'] . '</span> <br />';
            //echo json_encode($batch);
        }

        echo 'Done!';
        die;
    }

    private function _createBatch($data, $orgid) {

        $start_date = $data[0]['date'];
        $end = end($data)['date'];
        $netto = 0;
        $bruto = 0;

        foreach ($data as $keys => $values) {
            $netto += $values['netto'];
            $bruto += $values['bruto'];
        }

        //create batch
        $insert = array(
            'SupplyOrgID' => $orgid,
            'SupplyDestOrgID' => '5',
            'SupplyDestStatus' => 'Sent',
            'SupplyBatchNumber' => getBatchNumber($orgid),
            'SupplyBatchType' => '',
            'VolumeBruto' => $bruto,
            'VolumeNetto' => $netto,
            'SupplyBatchDate' => $start_date,
            'DeliveryDate' => $end,
            'DestWeight' => $netto,
        );
        //var_dump($insert);die;
        $this->db->insert('ktv_supplychain_batch', $insert);
        $batchid = $this->db->insert_id();
        if ($batchid) {
            $insert['SupplyBatchID'] = $this->db->insert_id();
            foreach ($data as $keys => $values) {
                $trans = array(
                    'SupplyBatchID' => $batchid,
                    'DateTransaction' => $values['date'],
                    'SupplyType' => 'FarmerNonCert',
                    'SupplyID' => $values['farmer'],
                    'FAQVolumeBruto' => $values['bruto'],
                    'FAQVolumeNetto' => $values['netto'],
                    'FAQQualityKA' => $values['moisture'],
                    'FAQQualityBC' => $values['bc'],
                    'FAQQualityWaste' => $values['waste'],
                    'FAQContractPrice' => $values['contract_price'],
                    'FAQNetPrice' => $values['net_price'],
                    'FAQTotalPayment' => $values['total'],
                    'FakturNumber' => $values['faktur'],
                    'FAQBeratBersihSetara' => $values['netto'],
                );
                $this->db->insert('ktv_supplychain_transaction', $trans);
                $transid = $this->db->insert_id();
                if ($transid) {
                    $trans['SupplyTransID'] = $transid;
                    $insert['trans'][] = $trans;

                    //insert detail
                    $detail = array(
                        'SupplyTransID' => $transid,
                        'PackageID' => 4,
                        'Type' => 'FAQ',
                        'Weight' => $values['bruto']
                    );

                    $this->db->insert('ktv_supplychain_transaction_dtl', $detail);
                    $detailid = $this->db->insert_id();

                    if ($detailid) {
                        $detail['DetailID'] = $detailid;
                        $insert['detail'][] = $detail;
                    }
                }
            }
        }

        $packing = array(
            'FromBatchID' => $batchid,
            'PackageID' => 4,
            'Type' => 'FAQ',
            'Weight' => 1
        );

        $this->db->insert('ktv_supplychain_transaction_dtl', $packing);

        $packid = $this->db->insert_id();
        if ($packid) {
            $packing['PackingID'] = $packid;
            $insert['packing'][] = $packing;
        }

        return $insert;
    }

    public function traceability_setting_copy_post() {

        $supplychainsource = $this->post('source');
        $supplychaintarget = $this->post('target');
        $districtid = $this->post('district');

        /*
          var_dump($supplychainsource);
          var_dump($supplychaintarget);
          var_dump($districtid);
          die;
         */

        // Tab Setting
        $source_setting = $this->_migration->getSourceDataSetting($supplychainsource);
        if ($source_setting) {
            $migrate_setting = $this->_migration->migrateSettingTab($source_setting, $supplychaintarget, $districtid);
        }

        // Tab Relation
        $source_relation = $this->_migration->getSourceDataRelation($supplychainsource);
        if ($source_relation) {
            //$migrate_setting = $this->_migration->migrateRelationTab($source_relation,$supplychaintarget,$districtid);
        }

        // Tab Quality Std & Tab Quality
        $source_std = $this->_migration->getSourceDataStd($supplychainsource);
        if ($source_std) {
            $migrate_setting = $this->_migration->migrateStdTab($source_std, $supplychainsource, $supplychaintarget, $districtid);
        }

        // Tab Quality
        //$source_quality = $this->_migration->getSourceDataQuality($supplychainsource);
        //if($source_quality){
        //$migrate_setting = $this->_migration->migrateQualityTab($source_quality,$supplychaintarget,$districtid);
        //}
        // Tab Price
        $source_price = $this->_migration->getSourceDataPrice($supplychainsource);
        if ($source_price) {
            $migrate_setting = $this->_migration->migratePriceTab($source_price, $supplychaintarget, $districtid);
        }

        // Tab Packaging
        $source_packaging = $this->_migration->getSourceDataPackaging($supplychainsource);
        if ($source_packaging) {
            //$migrate_setting = $this->_migration->migratePackagingTab($source_packaging,$supplychaintarget,$districtid);
        }

        // Tab Kurs
        $source_kurs = $this->_migration->getSourceDataKurs($supplychainsource);
        if ($source_kurs) {
            $migrate_setting = $this->_migration->migrateKursTab($source_kurs, $supplychaintarget, $districtid);
        }

        // Tab Premium
        $source_premium = $this->_migration->getSourceDataPremium($supplychainsource);
        if ($source_premium) {
            $migrate_setting = $this->_migration->migratePremiumTab($source_premium, $supplychaintarget, $districtid);
        }

        if ($migrate_setting) {
            return $this->response(array('success' => 'Done!'), 200);
        }

        return $this->response(array('success' => 'Done!'), 200);
    }

    public function saving_migration_get($memberid = false) {

        $this->load->model('member/mmember');

        $member = $this->_migration->getDataMember($memberid);
        if ($member) {
            foreach ($member as $key => $val) {

                $this->mmember->updateStatus($val['memberID'], 1);
            }
        }
    }

    public function tes_cargill_get() {
        $this->_migration->migrasiCreateBatchForCargill();
    }
    
    /**
     * Fungsi2 untuk migrasi data coop
     */
    public function move_coop_table_get() {
        
        $coops = array('1','11');
        
        foreach($coops as $coop) {
            $sql = "INSERT INTO `cocoatrace_live`.`accounting_coa_balance_copy` (`CoaBalanceID`, `CoaCode`, `CoaBalanceAmount`, `JournalClosedID`, `CoopID`, `CoaID`, `DateCreated`, `uid`) VALUES ('1', '', '100000.00000', NULL, '11', '17', '2017-02-03', NULL)";
        }
        die;
    }
    
    
    /**
     * Fungsi delete bulk berdasarkan uid
     */
    
    function deletedhis_get($uid = false) {
        
        $this->db->select('uid');
        $this->db->from('uid_del');
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            foreach($result as $keys => $values) {
                
                $urldhis = 'http://mobile.cocoatrace.com/';
        
                $action = 'DELETE';
                $url = $urldhis . 'api/events/' . $values['uid'];

                $this->load->helper('file');

                $dhispassword = 'Basic YWRtaW46S29sdGl2YTIwMTMh';
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: ' . $dhispassword
                ));

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $result = curl_exec($ch);

                $curlresult = json_decode($result, true);
                echo($result);
                $this->db->delete('uid_del',array('uid' => $values['uid']));
                echo '\n';
                echo '\n';
            }
        }
    }
    
    function cekuid_get($district = false) {
        
        $this->db->select('uid');
        $this->db->from('ktv_farmer');
        $this->db->where('uid IS NOT NULL','',FALSE);
        if($district) {
            $this->db->where('SUBSTR(VillageID,1,4)',$district,false);
        }
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            
            $missing = 0;
            $notmissing = 0;
            $total = count($result);
            
            foreach($result as $keys => $values) {
                
                $urldhis = 'http://mobile.cocoatrace.com/';
        
                $action = 'GET';
                $url = $urldhis . 'api/events/' . $values['uid'];
                //$url = $urldhis . 'api/events/tesuhuy';

                $this->load->helper('file');

                $dhispassword = 'Basic YWRtaW46S29sdGl2YTIwMTMh';
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: ' . $dhispassword
                ));

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $result = curl_exec($ch);

                $curlresult = json_decode($result, true);
                
                $status = $curlresult['httpStatusCode'];
                
                if($status == 404) {
                    $missing ++;
                    echo '<p>uid: '.$values['uid'] . 'is missing</p>';
                    
                    //$this->db->where('uid',$values['uid']);
                    //$this->db->update('ktv_farmer',array('uid' => NULL));
                } else {
                    $notmissing++;
                }
                
                echo '<p>uid: '.$values['uid'].'</p>';
            }
            
            echo 'missing: ' . $missing;
            echo "\n";
            echo "\n";
            echo "\n";
            echo 'not missing: ' . $notmissing;
            echo "\n";
            echo "\n";
            echo "\n";
            echo 'total: ' . $total;
            die;
        }
    }
    
}
