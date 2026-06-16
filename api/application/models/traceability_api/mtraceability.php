<?php

class Mtraceability extends CI_Model {

    function calculateReward($formula,$standard,$result) {
            //echo '<hr>';
        $output = 0;
        if($result != NULL && $result > 0 && strlen($formula) > 0) {
            //echo 'formula: ' . $formula . '; ';
            //echo 'standard: ' . $standard . '; ';
            //echo 'result: ' . $result . '; ';

            $reward = false;
            $claim = false;
            $find = array('[R]','[S]');
            $replace = array('$result','$standard');
            $result = floatval($result);
            $standard = floatval($standard);
            $formula = str_replace($find,$replace,$formula); //echo '<br>formula: ' . $result . '-' . $standard;
            eval("\$hasil = $formula;"); //dangerous but, nevermind~
            //echo '<br>reward: ' . $hasil . '; ';

            $output = $hasil;
        }

        return $output;
    }

    function injectData($bu,$wa,$no,$now,$data,$userid){
      $this->db->trans_begin();
	  $sql_batch = "
         insert into ktv_supplychain_batch (SupplyOrgID, SupplyDestOrgID, SupplyDestStatus, SupplyBatchNumber,
         	VolumeBruto, VolumeNetto, VolumeNettoMoisture, SupplyBatchDate, PerwakilanOrgID, DeliveryDate,
            DestWeight, DestICS, DestDriver,
         	DestNoPolisi, DestKarungStart, DestKarungEnd, DestPO, NoPO,
         	CreatedBy, DateCreated, LastModifiedBy, DateUpdated)
         values (?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";
      $sql_transaction = "
         insert into ktv_supplychain_transaction (SupplyBatchID, InvoiceNumber, DateTransaction, SupplyType, SupplyID,
         	FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
         	FAQVolumeMoisture,FAQRewardBruto,FAQRewardBonus,FAQReward,
         	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
         values (?,?,?,?,?,   ?,?,?,?,?,   ?,?,?,?,   $userid,now(),$userid,now())";
      $sql_transaction_dtl = "
         insert into ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID, PackageID, Type, Weight,WeightPackage,
            Moisture, Netto, NettoDelivery,
         	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
         select ?,?,PackageID,?,?,?,   ?,?,?,   $userid,now(),$userid,now() from ktv_supplychain_package
         where PackageWeight=0 and PackageSupplychainID=?";
      $sql_transaction_dtl_farmer = "
         insert into ktv_supplychain_transaction_dtl_farmer (DetailID, FarmerID, Berat)
         values (?,?,?)";
      $sql_package = "
         select * from ktv_supplychain_package where PackageSupplychainID=? order by PackageID desc limit 1";
      $sql_quality = "
         select * from ktv_supplychain_quality where QualitySupplychainID=? AND QualityDateStart <= NOW() AND QualityDateEnd >= NOW() order by QualityID desc limit 1";
      $sql_insert_package = "
         INSERT IGNORE INTO ktv_supplychain_package(PackageSupplychainID,PackageType,PackageWeight,PackageCapasity)
         VALUES (?,'Tanpa Pemotongan',0,0)";
      $sql_add_import = "
         INSERT INTO ktv_supplychain_import (DateImported, PO, Status, File, Description, CreatedBy) VALUES (now(),?,?,?,?,$userid)";
      $sql_add_import_detail = "
         INSERT INTO ktv_supplychain_import_detail (ImportID, SupplychainID, Date, FarmerID, Weight, CoopID)
         VALUES (?,?,?,?,?,?)";
        $sql_quality_standard = "SELECT b.DetailID, b.StandardID, b.FAQValue AS FAQStandard, FAQFormula, b.FFValue AS FFStandard, FFFormula, b.Order, b.Name FROM ktv_supplychain_quality_standard a LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.StandardID = b.StandardID WHERE a.StandardSupplychainID = ? ORDER BY `Order`";
        $sql_quality_standard_detail = "INSERT INTO ktv_supplychain_transaction_quality(DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward) VALUES (?,?,?,?,?,?)";
      //cargill pedagang
      $sqlll = "SELECT OrgID FROM ktv_supplychain_org_view WHERE SupplychainID=?";
      $query = $this->db->query($sqlll,array($wa));
      $result = $query->result_array();
        //*Mars*//
        if($result[0]['OrgID']=='50010'){
            //echo "<pre>".print_r($data,1);
            $DestPO = $data[0][1][2];
            $DateTransaction_start = $data[0][1][1];
            $check_nopo = $this->db->query("SELECT a.* FROM ktv_supplychain_transaction a LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID WHERE b.DestPO=? AND a.SupplyID=? AND a.DateTransaction LIKE '$DateTransaction_start%' AND FAQVolumeBruto=? AND FAQVolumeNetto=?",
                          array($DestPO, $data[0][1][13],$data[0][1][6],$data[0][1][8]));
            if($check_nopo->num_rows() > 0){
                $returns = array(
                    'check_nopo' => 'false_nopo',
                    'nopo' => $DestPO//.":1"//."|".$sceid[$ii]
                );
                return $returns;
            }
            $SupplyBatchDate = $data[0][1][1];
            if ($data[0][1][2]==''){
                echo 'Not found'; exit;
            }
            $sql_batch = "INSERT INTO ktv_supplychain_batch (SupplyOrgID,SupplyDestOrgID,SupplyDestStatus,SupplyBatchNumber,SupplyBatchDate,PerwakilanOrgID,DestPO,CreatedBy,DateCreated) VALUES (?,?,?,?,?,?,?,$userid,NOW())";
            $sql_batch_update = "UPDATE ktv_supplychain_batch SET VolumeBruto=?,VolumeNetto=?,DestWeight=?,DeliveryDate=? WHERE SupplyBatchID=?";
            $sql_transaction = "INSERT INTO ktv_supplychain_transaction (SupplyBatchID,DateTransaction,SupplyType,SupplyID,FAQVolumeBruto,FAQVolumeNetto,FAQNetPrice,FAQTotalPayment,DateCreated,CreatedBy,IsQuota) VALUES (?,?,?,?,?,?,?,?,NOW(),$userid,1)";
            $sql_transaction_dtl = "INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID,PackageID,Type,Weight,Netto,NettoDelivery,DateCreated,CreatedBy) VALUES (?,?,?,?,?,?,?,NOW(),$userid)";
            $DestCoop = 127;
            $DestWh = 128;
            $PackageIDCoop = 1153;
            $PackageIDWh = 1154;
            $total_data = count($data[0]);
            $id_bu = "";
            $VolumeBruto = 0;
            $VolumeNetto = 0;
            $VolumeBrutoAll = 0;
            $VolumeNettoAll = 0;
            $CoopSupplyBatchID = "";
            $WhSupplyBatchID = "";
            $DateTransaction = "";
            $BuSupplyBatchNumber = "";
            for($i=1;$i<$total_data;$i++){
                //$DestPO = $data[0][1][2];
                if($id_bu!=$data[0][$i][3]){
                    if($i > 1){
                        $this->db->query($sql_batch_update, array($VolumeBruto,$VolumeNetto,$VolumeNetto,$SupplyBatchDate,$BuSupplyBatchID));

                        $this->db->query($sql_transaction , array($CoopSupplyBatchID, $DateTransaction, 'Batch', $BuSupplyBatchNumber, $VolumeNetto, $VolumeNetto, NULL, NULL));

                        $this->db->query($sql_transaction_dtl, array($CoopSupplyBatchID, $this->db->insert_id(), $PackageIDCoop, 'FAQ', $VolumeNetto, $VolumeNetto, $VolumeNetto));

                        $VolumeBruto = 0;
                        $VolumeNetto = 0;

                    }else{
                        //echo "<pre>".print_r($data[0][$i],1);
                        $CoopSupplyBatchNumber = getBatchNumber($DestCoop);
                        $this->db->query($sql_batch, array($DestCoop, $DestWh, 'Delivered', $CoopSupplyBatchNumber, $SupplyBatchDate, NULL, $DestPO));
                        $CoopSupplyBatchID = $this->db->insert_id();
                        //echo $this->db->last_query();

                        $WhSupplyBatchNumber = getBatchNumber($DestWh);
                        $this->db->query($sql_batch, array($DestWh, NULL, 'Open', $WhSupplyBatchNumber, $SupplyBatchDate, NULL, $DestPO));
                        $WhSupplyBatchID = $this->db->insert_id();
                        //echo $this->db->last_query();
                    }
                    $id_bu = $data[0][$i][3];
                    $BuSupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_supplychain_org_view WHERE OrgID=? AND (OrgType='Pedagang' OR OrgType='sce')",array($id_bu))->row()->SupplychainID;
                    //echo $this->db->last_query();
                    $BuSupplyBatchNumber = getBatchNumber($BuSupplychainID);
                    $this->db->query($sql_batch,array($BuSupplychainID,$DestCoop,'Delivered',$BuSupplyBatchNumber,$data[0][$i][1],NULL,$DestPO));
                    $BuSupplyBatchID = $this->db->insert_id();
                }
                $total_payment = $data[0][$i][8] * $data[0][$i][9];
                $DateTransaction = $data[0][$i][1].' 00:00:00';
                $SupplyBatchDate = $data[0][$i][1];
                $this->db->query($sql_transaction, array($BuSupplyBatchID,$data[0][$i][1].' 00:00:00','Farmer',$data[0][$i][13],$data[0][$i][6],$data[0][$i][8],$data[0][$i][9],$total_payment));
                $BuSupplyTransID = $this->db->insert_id();
                $PackageID = $this->db->query("SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=? AND PackageWeight=?", array($BuSupplychainID, $data[0][$i][7]))->row()->PackageID;
                if(@$PackageID==''){
                    $this->db->query("INSERT INTO ktv_supplychain_package (PackageSupplychainID,PackageType,PackageWeight,DateCreated,CreatedBy) VALUES (?,?,?,NOW(), $userid)", array($BuSupplychainID,"Karung (".$data[0][$i][7].")",$data[0][$i][7]));
                    $PackageID = $this->db->insert_id();
                }
                $this->db->query($sql_transaction_dtl, array($BuSupplyBatchID,$BuSupplyTransID,$PackageID,'FAQ',$data[0][$i][8],$data[0][$i][8],$data[0][$i][8]));
                $VolumeBruto = $VolumeBruto + $data[0][$i][6];
                $VolumeNetto = $VolumeNetto + $data[0][$i][8];

                $VolumeBrutoAll = $VolumeBrutoAll + $data[0][$i][6];
                $VolumeNettoAll = $VolumeNettoAll + $data[0][$i][8];
            }

            $this->db->query($sql_transaction , array($CoopSupplyBatchID, $DateTransaction, 'Batch', $BuSupplyBatchNumber, $VolumeNetto, $VolumeNetto, NULL, NULL));
            $this->db->query($sql_transaction_dtl, array($CoopSupplyBatchID, $this->db->insert_id(), $PackageIDCoop, 'FAQ', $VolumeNetto, $VolumeNetto, $VolumeNetto));
            //echo $this->db->last_query();
            //$VolumeBruto = 0;
            //$VolumeNetto = 0;

            $this->db->query($sql_batch_update, array($VolumeBruto,$VolumeNetto,$VolumeNetto,$SupplyBatchDate,$BuSupplyBatchID));
            $this->db->query($sql_batch_update, array($VolumeNettoAll, $VolumeNettoAll, $VolumeNettoAll, $SupplyBatchDate, $CoopSupplyBatchID));
            $this->db->query($sql_batch_update, array($VolumeNettoAll, $VolumeNettoAll, $VolumeNettoAll, $SupplyBatchDate, $WhSupplyBatchID));
            $this->db->query($sql_transaction , array($WhSupplyBatchID, $DateTransaction_start, 'Batch', $CoopSupplyBatchNumber, $VolumeNettoAll, $VolumeNettoAll, NULL, NULL));
            $this->db->query($sql_transaction_dtl, array($WhSupplyBatchID, $this->db->insert_id(), $PackageIDWh, 'FAQ', $VolumeNettoAll, $VolumeNettoAll, $VolumeNettoAll));

            if ($this->db->trans_status() === TRUE){
                $this->db->trans_commit();
            }else{
                $this->db->trans_rollback();
            }
            $returns = array(
                'success' => 'true'

            );
            return $return;
        }
        //**//

        //JebeKoko
        if($wa=='49'){
            $check_bs = explode(':',$data[0][2][0]);
            if(intval($check_bs[1])==0){
                $DestPO = $data[1][2][2];
                $check_nopo = $this->db->query("SELECT a.* FROM ktv_supplychain_transaction a LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID WHERE b.DestPO=? AND a.DateTransaction LIKE '$DateTransaction_start%' AND FAQVolumeBruto=? AND FAQVolumeNetto=?",
                          array($DestPO,$data[1][2][5],$data[1][2][5]));
            }else{
                $jml = count($data);
                $DestPO = $data[$jml-1][2][2];
                $check_nopo = $this->db->query("SELECT a.* FROM ktv_supplychain_transaction a LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID WHERE b.DestPO=? AND a.DateTransaction LIKE '$DateTransaction_start%' AND FAQVolumeBruto=? AND FAQVolumeNetto=?",
                          array($DestPO,$data[$jml-1][2][5],$data[$jml-1][2][5]));
            }
            
            if($check_nopo->num_rows() > 0){
                $returns = array(
                    'check_nopo' => 'false_nopo',
                    'nopo' => $DestPO//.":1"//."|".$sceid[$ii]
                );
                return $returns;
            }

            $sql_transaction_dtl = "INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID,PackageID,Type,Weight,Netto,NettoDelivery,DateCreated,CreatedBy) VALUES (?,?,?,?,?,?,?,NOW(),$userid)";
            $sql_quality = "select * from ktv_supplychain_quality where QualitySupplychainID=? order by QualityID desc limit 1";
            $sql_quality_standard = "SELECT b.DetailID, b.StandardID, b.FAQValue AS FAQStandard, FAQFormula, b.FFValue AS FFStandard, FFFormula, b.Order, b.Name FROM ktv_supplychain_quality_standard a LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.StandardID = b.StandardID WHERE a.StandardID = ? ORDER BY `Order`";
            $sql_quality_standard_detail = "INSERT INTO ktv_supplychain_transaction_quality(DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward) VALUES (?,?,?,?,?,?)";
            $sql_batch_update = "UPDATE ktv_supplychain_batch SET VolumeBruto=?,VolumeNetto=?,DestWeight=? WHERE SupplyBatchID=?";
            $sql_transaction_dtl = "INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID,PackageID,Type,Weight,Netto,NettoDelivery,DateCreated,CreatedBy) VALUES (?,?,?,?,?,?,?,NOW(),$userid)";

            if(intval($check_bs[1])==0){
                $jumlah = count($data[0]);
                //echo "<pre>".print_r($data,1);
                $batchnumber = getBatchNumber($bu);
                
                $query = $this->db->query($sql_batch,array($bu, $wa, 'Delivered', $batchnumber, 0, 0, NULL, $data[0][6][1], NULL, $data[0][$jumlah-1][1], 0, NULL, NULL, NULL, NULL, NULL, $data[1][2][2], NULL));
                $SupplyBatchID = $this->db->insert_id();


                //62.5
                $bruto = 0;
                $netto = 0;

                $PackageID = $this->db->query("SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=? ORDER BY PackageID DESC", array($bu))->row()->PackageID;


                for($i=6;$i<$jumlah;$i++){

                    //$sql_package = "select * from ktv_supplychain_package where PackageSupplychainID=? order by PackageID desc limit 1";

                    $invoicenumber = getInvoiceNumber($bu);
                    $this->db->query($sql_transaction, array($SupplyBatchID, $invoicenumber, $data[0][$i][1], 'Farmer', $data[0][$i][3], $data[0][$i][5], $data[0][$i][6], $data[0][$i][11], $data[0][$i][11], $data[0][$i][12], NULL, NULL, NULL, NULL));
                    $SupplyTransID = $this->db->insert_id();

                    $this->db->query($sql_transaction_dtl, array(NULL, $SupplyTransID, $PackageID, 'FAQ', $data[0][$i][5], $data[0][$i][5], $data[0][$i][5]));

                    $query = $this->db->query($sql_quality,array($bu));
                    $quality = $this->db->query($sql_quality_standard,array($query->row()->StandardID));

                    foreach ($quality->result() as $row) {
                        if($row->Order=='1'){
                            $FAQResult = $data[0][$i][7];
                        }else if($row->Order=='2'){
                            $FAQResult = $data[0][$i][8];
                        }else if($row->Order=='3'){
                            $FAQResult = $data[0][$i][9];
                        }else if($row->Order=='4'){
                            $FAQResult = $data[0][$i][10];
                        }else{
                            $FAQResult = NULL;
                        }
                        $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                        $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                    }

                    $bruto = $bruto + $data[0][$i][5];
                    $netto = $netto + $data[0][$i][6];
                }


                $this->db->query($sql_batch_update, array($bruto, $netto, $netto, $SupplyBatchID));

                $for = floor($netto / 62.5);
                $a = 0;

                for($j=0;$j<$for;$j++){
                    $this->db->query($sql_transaction_dtl, array($SupplyBatchID, NULL, $PackageID, 'FAQ', 62.5, 62.5, 62.5));
                    $a = $a + 62.5;
                }
                $sisa = $netto - $a;
                $this->db->query($sql_transaction_dtl, array($SupplyBatchID, NULL, $PackageID, 'FAQ', $sisa, $sisa, $sisa));

                $wa_batchnumber = getBatchNumber($wa);
                $query = $this->db->query($sql_batch,array($wa, NULL, 'Open', $wa_batchnumber, $data[1][2][5], $data[1][2][5], NULL, $data[1][2][1], NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, $data[1][2][2], NULL));
                $wa_SupplyBatchID = $this->db->insert_id();

                $total_payment = $data[1][2][5] * $data[1][2][9];
                $wa_invoicenumber = getInvoiceNumber($wa);
                $this->db->query($sql_transaction, array($wa_SupplyBatchID, $wa_invoicenumber, $data[1][2][1], 'Batch', $batchnumber, $data[1][2][5], $data[1][2][5], $data[1][2][8], $data[1][2][9], $total_payment, NULL, NULL, NULL, NULL));
                $wa_SupplyTransID = $this->db->insert_id();

                $PackageID = $this->db->query("SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=? ORDER BY PackageID DESC", array($wa))->row()->PackageID;
                $this->db->query($sql_transaction_dtl, array(NULL, $wa_SupplyTransID, $PackageID, 'FAQ', $data[1][2][5], $data[1][2][5], $data[1][2][5]));
                

                $query = $this->db->query($sql_quality,array($wa));
                $quality = $this->db->query($sql_quality_standard,array($query->row()->StandardID));

                foreach ($quality->result() as $row) {
                    if($row->Order=='1'){
                        $FAQResult = $data[1][2][13];
                    }else if($row->Order=='2'){
                        $FAQResult = $data[1][2][16];
                    }else if($row->Order=='3'){
                        $FAQResult = $data[1][2][18];
                    }else if($row->Order=='4'){
                        $FAQResult = $data[1][2][20];
                    }else{
                        $FAQResult = $data[1][2][22];
                    }
                    $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                    $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $wa_SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                }

                if ($this->db->trans_status() === TRUE){
                    $this->db->trans_commit();
                    $returns = array(
                        'success' => 'true'
                    );
                }else{
                    $this->db->trans_rollback();
                    $returns = array(
                        'name' => 'false'
                    );
                }
                return $return;
            }else{ //Transaksi melalui BS
                //echo "<pre>".print_r($data,1);
                $sql_batch_update = "UPDATE ktv_supplychain_batch SET SupplyBatchDate=?, DeliveryDate=?, VolumeBruto=?,VolumeNetto=?,DestWeight=? WHERE SupplyBatchID=?";
                $jumlahi = count($data);
                $coop = $bu;
                //*Koperasi*//
                /*$sql_batch = "
                     insert into ktv_supplychain_batch (SupplyOrgID, SupplyDestOrgID, SupplyDestStatus, SupplyBatchNumber,
                        VolumeBruto, VolumeNetto, VolumeNettoMoisture, SupplyBatchDate, PerwakilanOrgID, DeliveryDate,
                        DestWeight, DestICS, DestDriver,
                        DestNoPolisi, DestKarungStart, DestKarungEnd, DestPO, NoPO,
                        CreatedBy, DateCreated, LastModifiedBy, DateUpdated)
                     values (?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";*/
                $coop_batchnumber = getBatchNumber($coop);
                $query = $this->db->query($sql_batch,array($coop, $wa, 'Delivered', $coop_batchnumber, $data[$jumlahi-1][2][4], $data[$jumlahi-1][2][4], NULL, $data[$jumlahi-1][2][1], NULL, $data[$jumlahi-1][2][1], 0, NULL, NULL, NULL, NULL, NULL, $data[$jumlahi-1][2][2], NULL));
                $coop_SupplyBatchID = $this->db->insert_id();

                $coop_PackageID = $this->db->query("SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=? ORDER BY PackageID DESC", array($coop))->row()->PackageID;
                $this->db->query($sql_transaction_dtl, array($coop_SupplyBatchID, NULL, $coop_PackageID, 'FAQ', $data[$jumlahi-1][2][4], $data[$jumlahi-1][2][4], $data[$jumlahi-1][2][4]));
                //**//
                //*Warehouse*//
                $wa_batchnumber = getBatchNumber($wa);
                $query = $this->db->query($sql_batch,array($wa, NULL, 'Open', $wa_batchnumber, $data[$jumlahi-1][2][5], $data[$jumlahi-1][2][5], NULL, $data[$jumlahi-1][2][1], NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, $data[$jumlahi-1][2][2], NULL));
                $wa_SupplyBatchID = $this->db->insert_id();

                $total_payment = $data[$jumlahi-1][2][5] * $data[$jumlahi-1][2][9];
                $wa_invoicenumber = getInvoiceNumber($wa);
                $this->db->query($sql_transaction, array($wa_SupplyBatchID, $wa_invoicenumber, $data[$jumlahi-1][2][1], 'Batch', $coop_batchnumber, $data[$jumlahi-1][2][5], $data[$jumlahi-1][2][5], $data[$jumlahi-1][2][8], $data[$jumlahi-1][2][9], $total_payment, NULL, NULL, NULL, NULL));
                $wa_SupplyTransID = $this->db->insert_id();
                $wa_PackageID = $this->db->query("SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=? ORDER BY PackageID DESC", array($wa))->row()->PackageID;
                $this->db->query($sql_transaction_dtl, array(NULL, $wa_SupplyTransID, $wa_PackageID, 'FAQ', $data[$jumlahi-1][2][5], $data[$jumlahi-1][2][5], $data[$jumlahi-1][2][5]));

                $query = $this->db->query($sql_quality,array($wa));
                $quality = $this->db->query($sql_quality_standard,array($query->row()->StandardID));

                foreach ($quality->result() as $row) {
                    if($row->Order=='1'){
                        $FAQResult = $data[$jumlahi-1][2][13];
                    }else if($row->Order=='2'){
                        $FAQResult = $data[$jumlahi-1][2][16];
                    }else if($row->Order=='3'){
                        $FAQResult = $data[$jumlahi-1][2][18];
                    }else if($row->Order=='4'){
                        $FAQResult = $data[$jumlahi-1][2][20];
                    }else{
                        $FAQResult = $data[$jumlahi-1][2][22];
                    }
                    $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                    $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $wa_SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                }
                //**//


                $coopbatchdate = '1991-01-01';
                for($i=0;$i<$jumlahi-1;$i++){
                    $jumlahj = count($data[$i]);
                    //BS - Batch
                    $buyingu = explode(':',$data[$i][2][0]);
                    $bs = intval($buyingu[1]);
                    $sql = "
                       SELECT SupplychainID
                       FROM ktv_supplychain_org_view ksov
                       LEFT JOIN sce_farmer sf ON sf.SceID=ksov.OrgID
                       WHERE FarmerID=? AND OrgType='sce'";
                    $query = $this->db->query($sql,array($bs));
                    $result = $query->result_array();
                    if (empty($result)) {
                       $sql = "
                          SELECT SupplychainID
                          FROM ktv_supplychain_org_view ksov
                          LEFT JOIN sce_farmer sf ON sf.SceID=ksov.OrgID
                          WHERE OrgID=? AND OrgType='Pedagang'";
                       $query = $this->db->query($sql,array($bs));
                       $result = $query->result_array();
                    }
                    $buyingunit = $result[0]['SupplychainID'];
                    $batchnumber = getBatchNumber($buyingunit);

                    $query = $this->db->query($sql_batch,array($buyingunit, $bu, 'Delivered', $batchnumber, 0, 0, NULL, $data[$i][6][1], NULL /*PerwakilanOrgID*/, NULL /*DeliveryDate*/, 0, NULL, NULL, NULL, NULL, NULL, $data[$jumlahi-1][2][2], NULL));
                    $SupplyBatchID = $this->db->insert_id();
                    $PackageID = $this->db->query("SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=? ORDER BY PackageID DESC", array($buyingunit))->row()->PackageID;
                    $bruto = 0;
                    $netto = 0;
                    $total = 0;
                    $DeliveryDate = '1991-01-01';
                    $BatchDate = '5000-01-01';

                    for($j=6;$j<$jumlahj;$j++){
                        if($data[$i][$j][3]!=''){
                            $invoicenumber = getInvoiceNumber($buyingunit);
                            $this->db->query($sql_transaction, array($SupplyBatchID, $invoicenumber, $data[$i][$j][1], 'Farmer', $data[$i][$j][3], $data[$i][$j][5], $data[$i][$j][5], $data[$i][$j][11], $data[$i][$j][11], $data[$i][$j][12], NULL, NULL, NULL, NULL));
                            $SupplyTransID = $this->db->insert_id();

                            $this->db->query($sql_transaction_dtl, array(NULL, $SupplyTransID, $PackageID, 'FAQ', $data[$i][$j][5], $data[$i][$j][5], $data[$i][$j][5]));

                            $query = $this->db->query($sql_quality,array($buyingunit));
                            $quality = $this->db->query($sql_quality_standard,array($query->row()->StandardID));

                            foreach ($quality->result() as $row) {
                                if($row->Order=='1'){
                                    $FAQResult = $data[$i][$j][7];
                                }else if($row->Order=='2'){
                                    $FAQResult = $data[$i][$j][8];
                                }else if($row->Order=='3'){
                                    $FAQResult = $data[$i][$j][9];
                                }else if($row->Order=='4'){
                                    $FAQResult = $data[$i][$j][10];
                                }else{
                                    $FAQResult = NULL;
                                }
                                $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                                $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                            }

                            $bruto = $bruto + $data[$i][$j][5];
                            $netto = $netto + $data[$i][$j][5];
                            $total = $total + $data[$i][$j][12];
                            $price = $data[$i][$j][11];
                            if (str_replace("-", "", $data[$i][$j][1]) > str_replace("-", "", $coopbatchdate)) {
                                $coopbatchdate = $data[$i][$j][1];
                            }
                            if (str_replace("-", "", $data[$i][$j][1]) > str_replace("-", "", $DeliveryDate)) {
                                $DeliveryDate = $data[$i][$j][1];
                            }
                            if (str_replace("-", "", $data[$i][$j][1]) < str_replace("-", "", $BatchDate)) {
                                $BatchDate = $data[$i][$j][1];
                            }
                        }
                    }

                    $this->db->query($sql_batch_update, array($BatchDate, $DeliveryDate, $bruto, $netto, $netto, $SupplyBatchID));
                    $this->db->query($sql_transaction_dtl, array($SupplyBatchID, NULL, $PackageID, 'FAQ', $netto, $netto, $netto));


                    /*$sql_transaction = "
                         insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
                            FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
                            FAQVolumeMoisture,FAQRewardBruto,FAQRewardBonus,FAQReward,
                            CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
                         values (?,?,?,?,   ?,?,?,?,?,   ?,?,?,?,   $userid,now(),$userid,now())";
                      $sql_transaction_dtl = "
                         insert into ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID, PackageID, Type, Weight,WeightPackage,
                            Moisture, Netto, NettoDelivery,
                            CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
                         select ?,?,PackageID,?,?,?,   ?,?,?,   $userid,now(),$userid,now() from ktv_supplychain_package
                         where PackageWeight=0 and PackageSupplychainID=?";*/
                    //*Koperasi*//
                    $coop_invoicenumber = getInvoiceNumber($coop);
                    $this->db->query($sql_transaction, array($coop_SupplyBatchID, $coop_invoicenumber, $BatchDate, 'Batch', $batchnumber, $bruto, $netto, $price, $price, $total, NULL, NULL, NULL, NULL));
                    $coop_SupplyTransID = $this->db->insert_id();
                    
                    $this->db->query($sql_transaction_dtl, array(NULL, $coop_SupplyTransID, $coop_PackageID, 'FAQ', $bruto, $bruto, $bruto));

                    $query = $this->db->query($sql_quality,array($coop));
                    $quality = $this->db->query($sql_quality_standard,array($query->row()->StandardID));

                    foreach ($quality->result() as $row) {
                        if($row->Order=='1'){
                            $FAQResult = $data[$jumlahi-1][2][11];
                        }else if($row->Order=='2'){
                            $FAQResult = $data[$jumlahi-1][2][14];
                        }else if($row->Order=='3'){
                            $FAQResult = $data[$jumlahi-1][2][17];
                        }else if($row->Order=='4'){
                            $FAQResult = $data[$jumlahi-1][2][19];
                        }else{
                            $FAQResult = $data[$jumlahi-1][2][21];
                        }
                        $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                        $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $coop_SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                    }
                    //**//

                }
                $this->db->query("UPDATE ktv_supplychain_batch SET SupplyBatchDate=?, DeliveryDate=? WHERE SupplyBatchID=?", array($coopbatchdate, $coopbatchdate, $coop_SupplyBatchID));
                $this->db->query("UPDATE ktv_supplychain_transaction SET DateTransaction=? WHERE SupplyBatchID=?", array($coopbatchdate, $coop_SupplyBatchID));

                if ($this->db->trans_status() === TRUE){
                    $this->db->trans_commit();
                    $returns = array(
                        'success' => 'true'
                    );
                }else{
                    $this->db->trans_rollback();
                    $returns = array(
                        'name' => 'false'
                    );
                }
                return $return;
            }
        }


      //kokajaya
      if ($result[0]['OrgID']=='50008') {
         //$this->db->trans_start();
         //$this->db->trans_begin();
         $numb = 0;
         for ($i=0;$i<sizeof($data);$i++) {
            if ($data[$i][6][5]>0 and trim($data[$i][6][1])!='') $numb++;
            else break;
         }

         for ($ii=0;$ii<$numb;$ii++) {
            //cek farmer sertifikasi atau bukan
            $sql_cek_farmer = "
               SELECT FarmerID
               FROM ktv_certification
               WHERE FarmerID=? and ExternalDate!='0000-00-00' and (? between CertificationStart and CertificationEnd)";

            for ($i=6;$i<sizeof($data[$ii]);$i++) {
               if ($data[$ii][$i][5]>0 and trim($data[$ii][$i][1])!='') {
                  $query = $this->db->query($sql_cek_farmer, array($data[$ii][$i][3],$data[$ii][$i][1]));
                  $result = $query->result_array();
                  if ($result[0]['FarmerID']=='') {
                     $non[] = '['.$data[$ii][$i][3].'] '.$data[$ii][$i][2];
                     $farm_date[] = $data[$ii][$i][1];
                     $farm_id[] = $data[$ii][$i][3];
                     $farm_weight[] = $data[$ii][$i][5];
                  }
               }
            }
            $bs = explode(':',$data[$ii][2][0]);
            $sql = "
               SELECT SupplychainID
               FROM ktv_supplychain_org_view ksov
               LEFT JOIN sce_farmer sf ON sf.SceID=ksov.OrgID
               WHERE FarmerID=? AND OrgType='sce'";
            $query = $this->db->query($sql,array(trim($bs[1])));
            $result = $query->result_array();
            if (empty($result)) {
               $sql = "
                  SELECT SupplychainID
                  FROM ktv_supplychain_org_view ksov
                  LEFT JOIN sce_farmer sf ON sf.SceID=ksov.OrgID
                  WHERE OrgID=? AND OrgType='Pedagang'";
               $query = $this->db->query($sql,array(trim($bs[1])));
               $result = $query->result_array();
            }
            $sceid[$ii] = $result[0]['SupplychainID'];
         }
         if (is_array($non)) {
            $result['name_sertifikasi'] = implode(', ', $non);
            $this->db->query($sql_add_import, array($data[$numb][2][2],'fail',null,'Farmer non certification'));
            $idi = $this->db->insert_id();
            for ($i=0;$i<sizeof($non);$i++) {
               $this->db->query($sql_add_import_detail, array($idi,$sceid[$i],$farm_date[$i],$farm_id[$i],$farm_weight[$i],$bu));
            }
            return $result;
         }

         for ($ii=0;$ii<$numb;$ii++) {
            $result = $this->cek_farmer($data[$ii],3,6);
            if ($result['name']!='') return $result;
            //Professional Farmer
            //if ($sceid=='') $sceid = trim($bs[1]);
            //print_r($sceid);exit;
            $bruto = 0;
            for ($i=6;$i<sizeof($data[$ii]);$i++) {
               if ($data[$ii][$i][5]>0 and trim($data[$ii][$i][1])!='') {
                  $bruto += $data[$ii][$i][5];//berat petani
                  $harga += $data[$ii][$i][12];//berat petani
                  $tgl_delivery[$ii] = $data[$ii][$i][1];
               }
            }
            $berr[$ii] = $bruto;
            $total_berr += $berr[$ii];
            $nosceid[$ii] = $this->mtransaction->readNumber('',$sceid[$ii]);
            $nopo = $data[$numb][2][2];
            //print_r($sceid);exit;
			//*Checking PO*//
			$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo,$sceid[$ii]));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo//.":1"//."|".$sceid[$ii]
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Duplicate PO Number'));
				$idi = $this->db->insert_id();

				for ($i=6;$i<sizeof($data[$ii]);$i++) {
					if ($data[$ii][$i][5]>0 and trim($data[$ii][$i][1])!='') {
						$this->db->query($sql_add_import_detail, array($idi,$sceid[$ii],$data[$ii][$i][1],$data[$ii][$i][3],$data[$ii][$i][5],null));
					}
				}
				return $returns;
			}
			//*Checking Date*//
			$date_check = $data[$ii][6][1];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":1"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid PO Date'));
				$idi = $this->db->insert_id();

				for ($i=6;$i<sizeof($data[$ii]);$i++) {
					if ($data[$ii][$i][5]>0 and trim($data[$ii][$i][1])!='') {
						$this->db->query($sql_add_import_detail, array($idi,$sceid[$ii],$data[$ii][$i][1],$data[$ii][$i][3],$data[$ii][$i][5],null));
					}
				}
				return $returns;
			}
			//**//
            $query = $this->db->query($sql_batch,array($sceid[$ii],$bu,'Delivered',$nosceid[$ii]['number'],   $bruto,$bruto,NULL,$data[$ii][6][1],null,
               $tgl_delivery[$ii],   $bruto,null,null,   null,null,null,$nopo,NULL));

            $idbatch[$ii] = $this->db->insert_id();
            $query_quality_standard = $this->db->query($sql_quality_standard,array($sceid[$ii]));
            $sql_transaction = "
               insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
               	FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
               	FAQVolumeMoisture,FAQRewardBruto,FAQRewardBonus,FAQReward,
               	FAQQualityKA,FAQQualityWaste,FAQQualityBC,FAQQualityMouldy,FAQQualityInsect,
               	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
               values (?,?,?,?,   ?,?,?,?,?,   ?,?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";
            $query = $this->db->query($sql_insert_package,array($sceid[$ii]));

            for ($i=6;$i<sizeof($data[$ii]);$i++) {
               if ($data[$ii][$i][5]>0 and trim($data[$ii][$i][1])!='') {
					//*Checking Transaction Date*//
					$transdate_check = $data[$ii][$i][1];
					if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
						$returns = array(
							'check_transdate' => 'false_transdate',
							'tanggal_transaksi' => $transdate_check
						);
						$this->db->trans_rollback();

						$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid Transaction Date'));
						$idi = $this->db->insert_id();
						$this->db->query($sql_add_import_detail, array($idi,$sceid[$ii],$data[$ii][$i][1],$data[$ii][$i][3],$data[$ii][$i][5],null));

						return $returns;
					}
					//**//
                  $query = $this->db->query($sql_transaction, array($idbatch[$ii],$data[$ii][$i][1],'Farmer',$data[$ii][$i][3],
                     $data[$ii][$i][5],$data[$ii][$i][5],$data[$ii][$i][11],$data[$ii][$i][11],$data[$ii][$i][12],
                     null,null,null,null,    $data[$ii][$i][7],$data[$ii][$i][8],$data[$ii][$i][9],$data[$ii][$i][10],null));
                    $SupplyTransID = $this->db->insert_id();
                  $query = $this->db->query($sql_transaction_dtl, array(NULL,$this->db->insert_id(),'FAQ',$data[$ii][$i][5],0,
                     null,$data[$ii][$i][5],NULL,$sceid[$ii]));
                    if($query_quality_standard->num_rows() > 0){
                        foreach($query_quality_standard->result() as $row){
                            //DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward
                            if($row->Order=='1'){
                                $FAQResult = $data[$ii][$i][7];
                            }else if($row->Order=='2'){
                                $FAQResult = $data[$ii][$i][8];
                            }else if($row->Order=='3'){
                                $FAQResult = $data[$ii][$i][8];
                            }else if($row->Order=='4'){
                                $FAQResult = $data[$ii][$i][8];
                            }else{
                                $FAQResult = NULL;
                            }
                            $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                            $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                        }
                    }
               }
            }
         }
         //koperasi
         $idbatch_ = $idbatch;
         $bruto = $data[$numb][2][4];
			//*Checking PO*//
			$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo,$bu));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo//.":2"//."|".$bu
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Duplicate PO Number'));
				$idi = $this->db->insert_id();

				for ($ii=0;$ii<$numb;$ii++) {
					$this->db->query($sql_add_import_detail, array($idi,$bu,$tgl_delivery[$numb-1],null,$berr[$ii],null));
				}
				return $returns;
			}
			//*Checking Date*//
			$date_check = $tgl_delivery[$numb-1];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":2"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid PO Date'));
				$idi = $this->db->insert_id();

				for ($ii=0;$ii<$numb;$ii++) {
					$this->db->query($sql_add_import_detail, array($idi,$bu,$tgl_delivery[$numb-1],null,$berr[$ii],null));
				}
				return $returns;
			}
			//**//
         $query = $this->db->query($sql_batch,array($bu,$wa,'Delivered',$no['number'],   $bruto,$bruto,NULL,$tgl_delivery[$numb-1],null,
            $data[$ii][2][1],   $bruto,null,null,   null,null,null,$nopo,NULL));

         $idbatch = $this->db->insert_id();
         $query_quality_standard = $this->db->query($sql_quality_standard,array($bu));
         for ($ii=0;$ii<$numb;$ii++) {
			//*Checking Transaction Date*//
			$transdate_check = $tgl_delivery[$numb-1];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
				$returns = array(
					'check_transdate' => 'false_transdate',
					'tanggal_transaksi' => $transdate_check
				);
				$this->db->trans_rollback();
				return $returns;
			}
			//**//
            $query = $this->db->query($sql_transaction, array($idbatch,$tgl_delivery[$numb-1],'Batch',$nosceid[$ii]['number'],
               $berr[$ii],$berr[$ii],$harga/$berr[$ii],$harga/$berr[$ii],$harga,
               null,null,null,null,   null,null,null,null,null));
            $idtrans = $this->db->insert_id();
            $query = $this->db->query($sql_insert_package,array($bu));
            $query = $this->db->query($sql_transaction_dtl, array($idbatch_[$ii],$idtrans,'FAQ',$berr[$ii],0,
               null,$berr[$ii]/$total_berr*$bruto,$berr[$ii],$bu));
            if($query_quality_standard->num_rows() > 0){
                foreach($query_quality_standard->result() as $row){
                    if($row->Order=='1'){
                        $FAQResult = $data[$numb][2][11];
                    }else if($row->Order=='2'){
                        $FAQResult = $data[$numb][2][14];
                    }else if($row->Order=='3'){
                        $FAQResult = $data[$numb][2][17];
                    }else if($row->Order=='4'){
                        $FAQResult = $data[$numb][2][19];
                    }else if($row->Order=='5'){
                        $FAQResult = $data[$numb][2][21];
                    }else{
                        $FAQResult = NULL;
                    }
                    $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                    $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $idtrans, $row->FAQStandard, $FAQResult, $FAQReward));
                }
            }
         }
         //warehouse
         $idbatch_ = $idbatch;
         $bruto = $data[$numb][2][5];
			//*Checking PO*//
			$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo,$wa));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo//.":3"//."|".$wa
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Duplicate PO Number'));
				$idi = $this->db->insert_id();
				$this->db->query($sql_add_import_detail, array($idi,$wa,$data[$numb][2][1],null,$bruto,null));

				return $returns;
			}
			//*Checking Date*//
			$date_check = $data[$numb][2][1];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":3"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid PO Date'));
				$idi = $this->db->insert_id();
				$this->db->query($sql_add_import_detail, array($idi,$wa,$data[$numb][2][1],null,$bruto,null));
				return $returns;
			}
			//**//
         $query = $this->db->query($sql_batch,array($wa,null,'Open',$now['number'],   $bruto,$bruto,NULL,$data[$numb][2][1],null,
            $data[$numb][2][1],   $bruto,null,null,   null,null,null,$nopo,NULL));
         $idbatch = $this->db->insert_id();
         $query_quality_standard = $this->db->query($sql_quality_standard,array($wa));
         $query = $this->db->query($sql_transaction, array($idbatch,$data[$numb][2][1],'Batch',$no['number'],
            $bruto,$bruto,$harga/$bruto,$harga/$bruto,$harga,
            null,null,null,null,   $data[$numb][2][13],$data[$numb][2][16],$data[$numb][2][18],$data[$numb][2][20],$data[0][$i][22]));
         $idtrans = $this->db->insert_id();
         $query = $this->db->query($sql_transaction_dtl, array($idbatch_,$idtrans,'FAQ',$bruto,0,
            null,$bruto,$bruto,$wa));
            if($query_quality_standard->num_rows() > 0){
                foreach($query_quality_standard->result() as $row){
                    //DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward
                    if($row->Order=='1'){
                        $FAQResult = $data[$numb][2][13];
                    }else if($row->Order=='2'){
                        $FAQResult = $data[$numb][2][16];
                    }else if($row->Order=='3'){
                        $FAQResult = $data[$numb][2][18];
                    }else if($row->Order=='4'){
                        $FAQResult = $data[$numb][2][20];
                    }else if($row->Order=='5'){
                        $FAQResult = $data[$numb][2][22];
                    }else{
                        $FAQResult = NULL;
                    }
                    $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                    $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $idtrans, $row->FAQStandard, $FAQResult, $FAQReward));
                }
            }
         $this->db->query($sql_add_import, array($data[$numb][2][2],'success',null,null));
         //$this->db->trans_complete();
		/*if ($this->db->trans_status() === TRUE && @$trans_status == ''){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}*/
		if ($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}
         return;
		//cargill
      } elseif ($wa=='5') {
	     //$this->db->trans_begin();
		 $result = $this->cek_farmer($data[0],4,6);
         if ($result['name']!='') return $result;
         $sql_transaction = "
            insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
            	FAQVolumeBruto, FAQVolumeNetto, FAQBeratBersihSetara, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
            	FAQVolumeMoisture,FAQRewardBruto,FAQRewardBonus,FAQReward,
            	FAQQualityKA,FAQQualityBC,FAQQualityWaste,FakturNumber,FAQVerifikasi,
            	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
            values (?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";
         $bruto = $netto = 0;
         for ($i=6;$i<sizeof($data[0]);$i++) {
            if ($data[0][$i][8]>0 and trim($data[0][$i][1])!='') {
               $bruto += $data[0][$i][8];//berat petani
               $netto += $data[0][$i][14];//berat petani
            }
         }
         //pedagang
         $tgl_kirim = ($data[0][2][4]==''?$data[0][2][2]:$data[0][2][4]);
         $nopo = ($data[0][3][4]==''?$data[0][3][2]:$data[0][3][4]);
			//*Checking PO*//
			$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo,$bu));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo//.":4"//."|".$bu
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Duplicate PO Number'));
				$idi = $this->db->insert_id();

				for ($i=6;$i<sizeof($data[0]);$i++) {
					if (trim($data[0][$i][1])!='') {
						$this->db->query($sql_add_import_detail, array($idi,$bu,$data[0][$i][3],$data[0][$i][4],$data[0][$i][8],null));
					}
				}
				return $returns;
			}
			//*Checking Date*//
			$date_check = $data[0][6][3];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":4"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid PO Date'));
				$idi = $this->db->insert_id();

				for ($i=6;$i<sizeof($data[0]);$i++) {
					if (trim($data[0][$i][1])!='') {
						$this->db->query($sql_add_import_detail, array($idi,$bu,$data[0][$i][3],$data[0][$i][4],$data[0][$i][8],null));
					}
				}
				return $returns;
			}
			//**//
         $query = $this->db->query($sql_batch,array($bu,$wa,'Delivered',$no['number'],   $bruto,$bruto,NULL,$data[0][6][3],null,
            $tgl_kirim,   $bruto,null,null,   null,null,null,$nopo,NULL));
         $idbatch = $this->db->insert_id();
         $query_quality_standard = $this->db->query($sql_quality_standard,array($bu));
         //print_r($data[0]);exit;
         for ($i=6;$i<sizeof($data[0]);$i++) {
            if (trim($data[0][$i][1])!='') {
				//*Checking Transaction Date*//
				$transdate_check = $data[0][$i][3];
				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
					$returns = array(
						'check_transdate' => 'false_transdate',
						'tanggal_transaksi' => $transdate_check
					);
					$this->db->trans_rollback();

					$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid Transaction Date'));
					$idi = $this->db->insert_id();
					$this->db->query($sql_add_import_detail, array($idi,$bu,$data[0][$i][3],$data[0][$i][4],$data[0][$i][8],null));


					return $returns;
				}
				//**//
               $query = $this->db->query($sql_transaction, array($idbatch,$data[0][$i][3],'Farmer',$data[0][$i][4],
                  $data[0][$i][8],$data[0][$i][8],$data[0][$i][14],str_replace(',','',$data[0][$i][12]),str_replace(',','',$data[0][$i][13]),
                  $data[0][$i][15],   null,null,null,null,
                  $data[0][$i][9],$data[0][$i][10],$data[0][$i][11],$data[0][$i][1],$data[0][$i][16]));
                $SupplyTransID = $this->db->insert_id();
               $query = $this->db->query($sql_transaction_dtl, array(NULL,$this->db->insert_id(),'FAQ',$data[0][$i][8],0,
                  null,$data[0][$i][8],NULL,$bu));
                if($query_quality_standard->num_rows() > 0){
                    foreach($query_quality_standard->result() as $row){
                        //DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward
                        if($row->Order=='1'){
                            $FAQResult = $data[0][$i][9];
                        }else if($row->Order=='2'){
                            $FAQResult = $data[0][$i][11];
                        }else if($row->Order=='3'){
                            $FAQResult = $data[0][$i][10];
                        }else if($row->Order=='4'){
                            $FAQResult = NULL;
                        }else{
                            $FAQResult = NULL;
                        }
                        $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                        $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
                    }
                }
            }
         }
         //packing list warehouse
         $query = $this->db->query($sql_package,array($wa));
         $result = $query->result_array();
         $jumlah_karung = ceil($bruto/$result[0]['PackageCapasity']);
         $berat_total = $pengurang = $nett = 0;
         for ($i=0;$i<$jumlah_karung;$i++) {
            if ($i==$jumlah_karung-1) $result[0]['PackageCapasity'] = $bruto-($i*$result[0]['PackageCapasity']);
            $netto = round($result[0]['PackageCapasity']/$bruto*$data[1][1][8],2);
            if ($i==$jumlah_karung-1) $netto = $data[1][1][8] - $nett;
            $nett += $netto;
            $query = $this->db->query($sql_transaction_dtl,array($idbatch,NULL,'FAQ',NULL,0,
               NULL,NULL,$result[0]['PackageCapasity'],$wa));
         }

		//warehouse
		$sql_transaction = "
            insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
            	FAQVolumeBruto, FAQVolumeNetto, FAQBeratBersihSetara, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
            	FAQVolumeMoisture,FAQRewardBruto,FAQRewardBonus,FAQReward,
            	FAQQualityKA,FAQQualityBC,FAQQualityWaste,FakturNumber,FAQVerifikasi,
            	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
            values (?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";
		$tgl_transaksi = date('Y-m-d H:i:s',date(strtotime("+1 day", strtotime($tgl_kirim))));
		$query = $this->db->query($sql_batch,array($wa,NULL,'Open',$now['number'],   $bruto,$bruto,NULL,$tgl_transaksi,null,null,null,null,null,null,null,null,null,null));
		$idbatch = $this->db->insert_id();
        $query_quality_standard = $this->db->query($sql_quality_standard,array($wa));

		$query = $this->db->query($sql_transaction, array($idbatch,$tgl_transaksi,'Batch', $no['number'],$bruto,$bruto,null,null,null,null,null,null,null,null,null,null,null,null,null,));
        $SupplyTransID = $this->db->insert_id();
        if($query_quality_standard->num_rows() > 0){
            foreach($query_quality_standard->result() as $row){
                //DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward
                $FAQResult = NULL;
                $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward));
            }
        }

		/*if ($this->db->trans_status() === TRUE && @$trans_status == ''){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}*/
		if ($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}
        return;
		//BT Cacao
      } else if ($wa=='27') {
         $result = $this->cek_farmer($data[0],4,5);
         if ($result['name']!='') return $result;
         /*var_dump($bu);exit;
         if ($bu=='') {
            print_r($data);
            return;
         }*/
         //BT Cacao pedagang -> warehouse
         $bruto = $netto = 0;
         for ($i=5;$i<sizeof($data[0]);$i++) {
            if (trim($data[0][$i][2])!='') {
               $ff += $data[0][$i][6];
               $sf += $data[0][$i][7];
               $faq += $data[0][$i][8];

               $ff_gudang += $data[0][$i][15];
               $sf_gudang += $data[0][$i][16];
               $faq_gudang += $data[0][$i][17];
            }
         }
         $total = $ff+$sf+$faq;
         $total_gudang = $ff_gudang+$sf_gudang+$faq_gudang;
         //pedagang
         $tgl_kirim = $data[0][0][9];
			//*Checking PO*//
			/*$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo
				);
				return $returns;
			}*/
			//*Checking Date*//
			$date_check = $data[0][5][1];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":5"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array(null,'fail',null,'Invalid PO Date'));
				$idi = $this->db->insert_id();

				for ($i=5;$i<sizeof($data[0]);$i++) {
					if (trim($data[0][$i][2])!='') {
						$this->db->query($sql_add_import_detail, array($idi,$bu,$data[0][$i][1],$data[0][$i][4],$data[0][$i][8],null));
					}
				}
				return $returns;
			}
			//**//
         $query = $this->db->query($sql_batch,array($bu,$wa,'Delivered',$no['number'],   $total,$total,NULL,$data[0][5][1],null,
            $tgl_kirim,   $total_gudang,null,null,   null,null,null,NULL,NULL));
         $idbatch = $this->db->insert_id();
         $query_quality_standard = $this->db->query($sql_quality_standard,array($bu));
         $sql_transaction = "
            insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
            	FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment, FAQQualityKA,FAQQualityBC,FAQQualityWaste,
            	FFVolumeBruto, FFVolumeNetto, FFContractPrice,FFNetPrice, FFTotalPayment, FFQualityKA,FFQualityBC,FFQualityWaste,
            	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
            values (?,?,?,?,   ?,?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,   $userid,now(),$userid,now())";
         for ($i=5;$i<sizeof($data[0]);$i++) {
            if (trim($data[0][$i][2])!='') {
               $faq_harga = $ff_harga = $faq_total = $ff_total = 0;
               if ($data[0][$i][8]>0) {
                  $faq_harga = str_replace(',','',$data[0][$i][13]);
                  $faq_total = str_replace(',','',$data[0][$i][14]);
                  $jenis = 'FAQ';
                  $weight = $data[0][$i][8];
                  $faq_ka = $data[0][$i][9];
                  $faq_bc = $data[0][$i][11];
                  $faq_waste = $data[0][$i][10];
               } else {
                  $ff_harga = str_replace(',','',$data[0][$i][13]);
                  $ff_total = str_replace(',','',$data[0][$i][14]);
                  $jenis = 'FF';
                  $weight = $data[0][$i][6];
                  $ff_ka = $data[0][$i][9];
                  $ff_bc = $data[0][$i][11];
                  $ff_waste = $data[0][$i][10];
               }
				//*Checking Transaction Date*//
				$transdate_check = $data[0][$i][1];
				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
					$returns = array(
						'check_transdate' => 'false_transdate',
						'tanggal_transaksi' => $transdate_check
					);
					 $this->db->trans_rollback();
					$this->db->query($sql_add_import, array(null,'fail',null,'Invalid Transaction Date'));
					$idi = $this->db->insert_id();
					$this->db->query($sql_add_import_detail, array($idi,null,$data[0][$i][1],$data[0][$i][4],$data[0][$i][8],null));
					return $returns;
				}
				//**//
               $query = $this->db->query($sql_transaction,array($idbatch,$data[0][$i][1],'Farmer',$data[0][$i][4],
                  $data[0][$i][8],$data[0][$i][8],$faq_harga,$faq_harga,$faq_total,$faq_ka,$faq_bc,$faq_waste,
                  $data[0][$i][6],$data[0][$i][6],$ff_harga,$ff_harga,$ff_total,$ff_ka,$ff_bc,$ff_waste));
                $SupplyTransID = $this->db->insert_id();
               $query = $this->db->query($sql_transaction_dtl,array(NULL,$this->db->insert_id(),$jenis,$weight,0,
                  $data[0][$i][9],$weight,NULL,$bu));
                if($query_quality_standard->num_rows() > 0){
                    foreach($query_quality_standard->result() as $row){
                        //DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward
                        if($row->Order=='1'){
                            $FAQResult = $faq_ka;
                            $FFResult = $ff_ka;
                        }else if($row->Order=='2'){
                            $FAQResult = $faq_waste;
                            $FFResult = $ff_waste;
                        }else if($row->Order=='3'){
                            $FAQResult = $faq_bc;
                            $FFResult = $ff_bc;
                        }else{
                            $FAQResult = NULL;
                            $FFResult = NULL;
                        }
                        $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                        $FFReward = $this->calculateReward($row->FFFormula,$row->FFStandard,$FFResult);
                        $sql_quality_standard_detail = "INSERT INTO ktv_supplychain_transaction_quality(DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward,FFStandard,FFResult,FFReward) VALUES (?,?,?,?,?,?,?,?,?)";
                        $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward, $row->FFStandard, $FFResult, $FFReward));
                    }
                }
               $ii = $i;
            }
         }
         //warehouse
         $query = $this->db->query($sql_quality,array($wa));
         $result = $query->result_array();
			//*Checking PO*//
			/*$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo
				);
				return $returns;
			}*/
			//*Checking Date*//
			$date_check = $data[0][0][9];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":6"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array(null,'fail',null,'Invalid Transaction Date'));
				$idi = $this->db->insert_id();
				if($jenis=='FF') {
					$wight = @$ff_gudang;
				}else{
					$wight = @$faq_gudang;
				}
				$this->db->query($sql_add_import_detail, array($idi,null,$data[0][$i][9],null,$weight,null));
				return $returns;
			}
			//**//
         $query = $this->db->query($sql_batch,array($wa,null,'Open',$now['number'],
            $total_gudang,$total_gudang,NULL,$data[0][0][9],null,null,
            null,null,null,   null,null,null,NULL,NULL));
         $idbatchw = $this->db->insert_id();
         $query_quality_standard = $this->db->query($sql_quality_standard,array($wa));
         $i = $ii;
         $i++;
         if($jenis=='FF') {
            $FFQualityKA = $data[0][$i][19];
            $FFQualityBC = $data[0][$i][18];
            $FFQualityWaste = $data[0][$i][22];
            $FFQualityMouldy = $data[0][$i][20];
            $FFQualityInsect = $data[0][$i][21];
            $FFQualitySlaty = $data[0][$i][23];
            $ka = $result[0]['FFMoisture'] - $FFQualityKA;
            if ($ka>0) $ka = 0;
            $bc = ($result[0]['FFBeanCount'] - $FFQualityBC)*0.2;
            if ($bc>0) $bc = 0;
            $waste = $result[0]['FFWaste'] - $FFQualityWaste;
            if ($waste>0) $waste = 0;
            $mouldy = $result[0]['FFMouldy'] - $FFQualityMouldy;
            if ($mouldy>0) $mouldy = 0;
            $insect = $result[0]['FFInsect'] - $FFQualityInsect;
            if ($insect>0) $insect = 0;
            $slaty = $result[0]['FFSlaty'] - $FFQualitySlaty;
            if ($slaty>0) $slaty = 0;
            $guality_ff = $ka+$bc+$waste+$mouldy+$insect+$slaty;
         } else {
            $FAQQualityKA = $data[0][$i][19];
            $FAQQualityBC = $data[0][$i][18];
            $FAQQualityWaste = $data[0][$i][22];
            $FAQQualityMouldy = $data[0][$i][20];
            $FAQQualityInsect = $data[0][$i][21];
            $FAQQualitySlaty = $data[0][$i][23];
            $fka = $result[0]['FAQMoisture'] - $FAQQualityKA;
            if ($fka>0) $fka = 0;
            $fbc = ($result[0]['FAQBeanCount'] - $FAQQualityBC)*0.2;
            if ($fbc>0) $fbc = 0;
            $fwaste = $result[0]['FAQWaste'] - $FAQQualityWaste;
            if ($fwaste>0) $fwaste = 0;
            $fmouldy = $result[0]['FAQMouldy'] - $FAQQualityMouldy;
            if ($fmouldy>0) $fmouldy = 0;
            $finsect = $result[0]['FAQInsect'] - $FAQQualityInsect;
            if ($finsect>0) $finsect = 0;
            $fslaty = $result[0]['FAQSlaty'] - $FAQQualitySlaty;
            if ($fslaty>0) $fslaty = 0;
            $guality_faq = $kfa+$fbc+$fwaste+$fmouldy+$finsect+$fslaty;
         }
         $sql_transaction = "
            insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
            	FFVolumeBruto, FFVolumeNetto, FFContractPrice,FFNetPrice,FFTotalPayment,
               	FFQualityKA, FFQualityBC, FFQualityWaste, FFQualityMouldy, FFQualityInsect, FFQualitySlaty,
               	#FFRewardMoisture,FFRewardBeanCount,FFRewardWaste,FFRewardMouldy,FFRewardInsect,FFRewardSlaty,
               FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
               	FAQQualityKA, FAQQualityBC, FAQQualityWaste, FAQQualityMouldy, FAQQualityInsect, FAQQualitySlaty,
               	#FAQRewardMoisture,FAQRewardBeanCount,FAQRewardWaste,FAQRewardMouldy,FAQRewardInsect,FAQRewardSlaty,
            	FFRewardBruto,FFRewardBonus,FFReward,FAQRewardBruto,FAQRewardBonus,FAQReward,
            	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
            values (?,?,?,?,   ?,?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,?,  ?,?,?,?,?,?,
               $userid,now(),$userid,now())";
			//*Checking Transaction Date*//
			$transdate_check = $data[0][0][9];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
				$returns = array(
					'check_transdate' => 'false_transdate',
					'tanggal_transaksi' => $transdate_check
				);
				 $this->db->trans_rollback();
				return $returns;
			}
			//**//
         $query = $this->db->query($sql_transaction,array($idbatchw,$data[0][0][9],'Batch',$no['number'],
            $ff_gudang,$ff_gudang,($jenis=='FF'?$data[0][1][9]:0),($jenis=='FF'?$data[0][1][9]:0),($jenis=='FF'?$data[0][1][9]*$ff_gudang:0),
            $FFQualityKA, $FFQualityBC, $FFQualityWaste, $FFQualityMouldy, $FFQualityInsect, $FFQualitySlaty,
            $faq_gudang,$faq_gudang,($jenis=='FAQ'?$data[0][1][9]:0),($jenis=='FAQ'?$data[0][1][9]:0),($jenis=='FAQ'?$data[0][1][9]*$faq_gudang:0),
            $FAQQualityKA, $FAQQualityBC, $FAQQualityWaste, $FAQQualityMouldy, $FAQQualityInsect, $FAQQualitySlaty,
            $guality_ff,0,$guality_ff,$guality_faq,0,$guality_faq));
         $SupplyTransID = $this->db->insert_id();
         $query = $this->db->query($sql_transaction_dtl,array($idbatch,$this->db->insert_id(),$jenis,$total_gudang,0,
            null,$total_gudang,$total_gudang,$wa));
            if($query_quality_standard->num_rows() > 0){
                foreach($query_quality_standard->result() as $row){
                    //DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward
                    if($row->Order=='1'){
                        $FAQResult = $FAQQualityKA;
                        $FFResult = $FFQualityKA;
                    }else if($row->Order=='2'){
                        $FAQResult = $FAQQualityWaste;
                        $FFResult = $FFQualityWaste;
                    }else if($row->Order=='3'){
                        $FAQResult = $FAQQualityBC;
                        $FFResult = $FFQualityBC;
                    }else if($row->Order=='4'){
                        $FAQResult = $FAQQualityMouldy;
                        $FFResult = $FFQualityMouldy;
                    }else if($row->Order=='5'){
                        $FAQResult = $FAQQualityInsect;
                        $FFResult = $FFQualityInsect;
                    }else if($row->Order=='6'){
                        $FAQResult = $FAQQualitySlaty;
                        $FFResult = $FFQualitySlaty;
                    }else{
                        $FAQResult = NULL;
                        $FFResult = NULL;
                    }
                    $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                    $FFReward = $this->calculateReward($row->FFFormula,$row->FFStandard,$FFResult);
                    $sql_quality_standard_detail = "INSERT INTO ktv_supplychain_transaction_quality(DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward,FFStandard,FFResult,FFReward) VALUES (?,?,?,?,?,?,?,?,?)";
                    $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $SupplyTransID, $row->FAQStandard, $FAQResult, $FAQReward, $row->FFStandard, $FFResult, $FFReward));
                }
            }
		if ($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}
         return;
      }
      $result = $this->cek_farmer($data[0],1,4);
      if ($result['name']!='') return $result;

      $bruto = $netto = 0;
      for ($i=4;$i<sizeof($data[0]);$i++) {
         if ($data[0][$i][3]>0 and trim($data[0][$i][1])!='') {
            $bruto += $data[0][$i][3];//berat petani
         }
      }
      //echo $netto;exit;
      $perwakilan = explode('/',$data[0][1][1]);

      //koperasi
		//*Checking PO*//
		/*$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
		$check_nopo = $this->db->query($sql_nopo,array($nopo));
		if($check_nopo->num_rows() > 0){
			$returns = array(
				'check_nopo' => 'false_nopo',
				'nopo' => $nopo
			);
			return $returns;
		}*/
		//*Checking Date*//
		$date_check = $data[0][4][0];
		if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
			$returns = array(
				'check_date' => 'false_date',
				'tanggal_po' => $date_check//.":7"
			);
			$this->db->trans_rollback();
			$this->db->query($sql_add_import, array(null,'fail',null,'Invalid Transaction Date'));
			$idi = $this->db->insert_id();
			for ($i=4;$i<sizeof($data[0]);$i++) {
				if (trim($data[0][$i][1])!='') {
					$this->db->query($sql_add_import_detail, array($idi,null,$data[0][$i][0],$data[0][$i][1],$data[0][$i][3],null));
				}
			}
			return $returns;
		}
		//**//
      $query = $this->db->query($sql_batch,array($bu,$wa,'Delivered',$no['number'],   $bruto,$bruto,NULL,$data[0][4][0],$perwakilan[0],
         $data[1][1][3],$bruto,null,$data[1][1][22],   $data[1][1][21],$data[1][2][7],($data[1][1][7]+$data[1][2][7]-1),NULL,NULL));
      $idbatch = $this->db->insert_id();
      for ($i=4;$i<sizeof($data[0]);$i++) {
         if (trim($data[0][$i][1])!='') {
			//*Checking Transaction Date*//
			$transdate_check = $data[0][$i][0];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
				$returns = array(
					'check_transdate' => 'false_transdate',
					'tanggal_transaksi' => $transdate_check
				);
				 $this->db->trans_rollback();
				$this->db->query($sql_add_import, array(null,'fail',null,'Invalid Transaction Date'));
				$idi = $this->db->insert_id();
				$this->db->query($sql_add_import_detail, array($idi,null,$data[0][$i][0],$data[0][$i][1],$data[0][$i][3],null));

				return $returns;
			}
			//**//
            $query = $this->db->query($sql_transaction,array($idbatch,$data[0][$i][0],'Farmer',$data[0][$i][1],
               $data[0][$i][3],$data[0][$i][3],str_replace(',','',$data[0][$i][5]),str_replace(',','',$data[0][$i][5]),
               $data[0][$i][6],null,null,null,null));
            $query = $this->db->query($sql_transaction_dtl,array(NULL,$this->db->insert_id(),'FAQ',$data[0][$i][3],0,
               $data[0][$i][4],$data[0][$i][3],NULL,$bu));
         }
      }

      //warehouse
		//*Checking PO*//
		$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
		$check_nopo = $this->db->query($sql_nopo,array($data[1][1][4],$wa));
		if($check_nopo->num_rows() > 0){
			$returns = array(
				'check_nopo' => 'false_nopo',
				'nopo' => $nopo//.":5"//."|".$wa
			);
			$this->db->trans_rollback();
			$this->db->query($sql_add_import, array($nopo,'fail',null,'Duplicate PO Number'));
			$idi = $this->db->insert_id();
			$this->db->query($sql_add_import_detail, array($idi,$wa,$data[1][1][3],null,$data[1][1][8],null));

			return $returns;
		}
		//*Checking Date*//
		$date_check = $data[1][1][3];
		if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
			$returns = array(
				'check_date' => 'false_date',
				'tanggal_po' => $date_check//.":8"
			);
			$this->db->trans_rollback();
			$this->db->query($sql_add_import, array($nopo,'fail',null,'Invalid PO Date'));
			$idi = $this->db->insert_id();
			$this->db->query($sql_add_import_detail, array($idi,$wa,$data[1][1][3],null,$data[1][1][8],null));

			return $returns;
		}
		//**//
      $query = $this->db->query($sql_batch,array($wa,NULL,'Other',$now['number'],
         $data[1][1][8],$data[1][1][8],$data[1][1][9],$data[1][1][3],NULL,$data[1][1][3],$data[1][1][9],NULL,$data[1][1][22],   $data[1][1][21],
         $data[1][2][7],($data[1][1][7]+$data[1][2][7]-1),$data[1][1][4],NULL));
      $idbatchw = $this->db->insert_id();
		//*Checking Transaction Date*//
		$transdate_check = $data[1][1][3];
		if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
			$returns = array(
				'check_transdate' => 'false_transdate',
				'tanggal_transaksi' => $transdate_check
			);
			 $this->db->trans_rollback();
			return $returns;
		}
		//**//
      $query = $this->db->query($sql_transaction,array($idbatchw,$data[1][1][3],'Batch',$no['number'],
         $data[1][1][8],$data[1][1][8],$data[1][1][6],$data[1][1][28],$data[1][1][26],   $data[1][1][10],0-$data[1][1][20],
         0,0-$data[1][1][20]));
      $idtransw = $this->db->insert_id();

      //packing list warehouse
      $bruto_ = $bruto;
      $bruto = $data[1][1][9];
      $idtrans = $this->db->insert_id();
      $query = $this->db->query($sql_package,array($wa));
      $result = $query->result_array();
      $jumlah_karung = ceil($bruto/$result[0]['PackageCapasity']);
      $ja = 4;
      $berat_total = $pengurang = $nett = 0;
      for ($i=0;$i<$jumlah_karung;$i++) {
         if ($i==$jumlah_karung-1) $result[0]['PackageCapasity'] = $bruto-($i*$result[0]['PackageCapasity']);
         $netto = round($result[0]['PackageCapasity']/$bruto*$data[1][1][8],2);
         if ($i==$jumlah_karung-1) $netto = $data[1][1][8] - $nett;
         $nett += $netto;
         $sql_transaction_dtl = "
            insert into ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID, PackageID, Type, Weight,WeightPackage,
               Moisture, Netto, NettoDelivery,
            	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
            select ?,?,PackageID,?,?,?,   ?,?,?,   $userid,now(),$userid,now() from ktv_supplychain_package
            where PackageWeight=0 and PackageSupplychainID=?";
         $query = $this->db->query($sql_transaction_dtl,array($idbatchw,NULL,'FAQ',NULL,0,
            NULL,NULL,$result[0]['PackageCapasity'],$wa));
         //$query = $this->db->query($sql_transaction_dtl,array($idbatch,$idtransw,'FAQ',$netto,0,
           // NULL,$netto,$result[0]['PackageCapasity'],$wa));
         $iddetail = $this->db->insert_id();
         for ($j=$ja;$j<sizeof($data[0]);$j++) {
            if (trim($data[0][$j][1])!='') {
               $berat = $data[0][$j][3] - $pengurang;
               $pengurang = 0;
               $berat_total += $berat;
               if ($berat_total<$result[0]['PackageCapasity'] OR $berat_total==$result[0]['PackageCapasity'])
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
               elseif ($berat_total>$result[0]['PackageCapasity']) {
                  $berat = $pengurang = $berat - ($berat_total-$result[0]['PackageCapasity']);
                  $berat_total = 0;
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
                  break;
               }
               $ja++;
            }
         }
      }
      //packing_list koperasi
      $query = $this->db->query($sql_package,array($wa));
      $result = $query->result_array();
      $ja = 4;
      $berat_total = $pengurang = 0;
      for ($i=1;$i<sizeof($data[1]);$i++) {
         if ($data[1][$i][0]=='Total') break;
         $sql_transaction_dtl = "
            insert into ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID, PackageID, Type, Weight,WeightPackage,
               Moisture, Netto, NettoDelivery,
            	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
            select ?,?,PackageID,?,?,?,   ?,?,?,   $userid,now(),$userid,now() from ktv_supplychain_package
            where PackageWeight=0 and PackageSupplychainID=?";
         $berat_wh = round($data[1][1][9]/$bruto_*$data[1][$i][1], 2);
         if (sizeof($data[1])-2==$i) $berat_wh = round($data[1][1][9]-$total_wh, 2);
         $total_wh += $berat_wh;
         $query = $this->db->query($sql_transaction_dtl,array($idbatch,$idtransw,'FAQ',$berat_wh,0,
            NULL,$berat_wh,$data[1][$i][1],$wa));
         //$query = $this->db->query($sql_transaction_dtl,array($idbatchw,NULL,'FAQ',NULL,0,
           // NULL,NULL,$data[1][$i][1],$wa));
         $iddetail = $this->db->insert_id();
         for ($j=$ja;$j<sizeof($data[0]);$j++) {
            if (trim($data[0][$j][1])!='') {
               $berat = $data[0][$j][3] - $pengurang;
               $pengurang = 0;
               $berat_total += $berat;
               if ($berat_total<$data[1][$i][1] OR $berat_total==$data[1][$i][1])
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
               elseif ($berat_total>$result[0]['PackageCapasity']) {
                  $berat = $pengurang = $berat - ($berat_total-$data[1][$i][1]);
                  $berat_total = 0;
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
                  break;
               }
               $ja++;
            }
         }
      }
      /*packing list koperasi
      $idtrans = $this->db->insert_id();
      $query = $this->db->query($sql_package,array($wa));
      $result = $query->result_array();
      $jumlah_karung = ceil($bruto/$result[0]['PackageCapasity']);
      $ja = 4;
      $berat_total = $pengurang = $nett = 0;
      for ($i=0;$i<$jumlah_karung;$i++) {
         if ($i==$jumlah_karung-1) $result[0]['PackageCapasity'] = $bruto-($i*$result[0]['PackageCapasity']);
         $netto = round($result[0]['PackageCapasity']/$bruto*$data[1][1][8],2);
         if ($i==$jumlah_karung-1) $netto = $data[1][1][8] - $nett;
         $nett += $netto;
         $query = $this->db->query($sql_transaction_dtl,array($idbatch,$idtransw,'FAQ',$netto,0,
            NULL,$netto,$result[0]['PackageCapasity'],$wa));
         $iddetail = $this->db->insert_id();
         for ($j=$ja;$j<sizeof($data[0]);$j++) {
            if (trim($data[0][$j][1])!='') {
               $berat = $data[0][$j][3] - $pengurang;
               $pengurang = 0;
               $berat_total += $berat;
               if ($berat_total<$result[0]['PackageCapasity'] OR $berat_total==$result[0]['PackageCapasity'])
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
               elseif ($berat_total>$result[0]['PackageCapasity']) {
                  $berat = $pengurang = $berat - ($berat_total-$result[0]['PackageCapasity']);
                  $berat_total = 0;
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
                  break;
               }
               $ja++;
            }
         }
      }*/
      /*packing_list warehouse
      $query = $this->db->query($sql_package,array($wa));
      $result = $query->result_array();
      $ja = 4;
      $berat_total = $pengurang = 0;
      for ($i=1;$i<sizeof($data[1]);$i++) {
         if ($data[1][$i][0]=='Total') break;
         $query = $this->db->query($sql_transaction_dtl,array($idbatchw,NULL,'FAQ',NULL,0,
            NULL,NULL,$data[1][$i][1],$wa));
         $iddetail = $this->db->insert_id();
         for ($j=$ja;$j<sizeof($data[0]);$j++) {
            if (trim($data[0][$j][1])!='') {
               $berat = $data[0][$j][3] - $pengurang;
               $pengurang = 0;
               $berat_total += $berat;
               if ($berat_total<$data[1][$i][1] OR $berat_total==$data[1][$i][1])
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
               elseif ($berat_total>$result[0]['PackageCapasity']) {
                  $berat = $pengurang = $berat - ($berat_total-$data[1][$i][1]);
                  $berat_total = 0;
                  $query = $this->db->query($sql_transaction_dtl_farmer, array($iddetail,$data[0][$j][1],$berat));
                  break;
               }
               $ja++;
            }
         }
      }*/
		if ($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}
      return $query;
    }

    function injectDataBT($bu,$no,$data,$userid){
		$this->db->trans_begin();
	  $result = $this->cek_farmer($data[0],5,5);
      if ($result['name']!='') return $result;

      $sql_batch = "
         insert into ktv_supplychain_batch (SupplyOrgID, SupplyDestOrgID, SupplyDestStatus, SupplyBatchNumber,
         	VolumeBruto, VolumeNetto, VolumeNettoMoisture, SupplyBatchDate, PerwakilanOrgID, DeliveryDate, DestWeight, DestICS, DestDriver,
         	DestNoPolisi, DestKarungStart, DestKarungEnd, DestPO, NoPO,
         	CreatedBy, DateCreated, LastModifiedBy, DateUpdated)
         values (?,?,?,?,   ?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";
      $sql_transaction = "
         insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
         	FFVolumeBruto, FFVolumeNetto, FFContractPrice,FFNetPrice,FFTotalPayment,
            	FFQualityKA, FFQualityBC, FFQualityWaste, FFQualityMouldy, FFQualityInsect, FFQualitySlaty,
            	#FFRewardMoisture,FFRewardBeanCount,FFRewardWaste,FFRewardMouldy,FFRewardInsect,FFRewardSlaty,
            FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
            	FAQQualityKA, FAQQualityBC, FAQQualityWaste, FAQQualityMouldy, FAQQualityInsect, FAQQualitySlaty,
            	#FAQRewardMoisture,FAQRewardBeanCount,FAQRewardWaste,FAQRewardMouldy,FAQRewardInsect,FAQRewardSlaty,
         	FFRewardBruto,FFRewardBonus,FFReward,FAQRewardBruto,FAQRewardBonus,FAQReward,
         	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
         select ?,?,?,FarmerID,   ?,?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,?,  ?,?,?,?,?,?,
            $userid,now(),$userid,now() from ktv_farmer where oldFarmerID=? OR FarmerID=?";
      $sql_transaction_dtl = "
         insert into ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID, PackageID, Type, Weight,WeightPackage,
            Moisture, Netto, NettoDelivery,
         	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
         select ?,?,PackageID,?,?,?,   ?,?,?,   $userid,now(),$userid,now() from ktv_supplychain_package
         where PackageWeight=0 and PackageSupplychainID=?";
      $sql_quality = "
         select * from ktv_supplychain_quality where QualitySupplychainID=? order by QualityID desc limit 1";
         for ($i=5;$i<sizeof($data[0]);$i++) {
            if ($data[0][$i][5]!='') $total_b += $data[0][$i][13]+$data[0][$i][15];
         }
			//*Checking PO*//
			/*$sql_nopo = "SELECT * FROM ktv_supplychain_batch WHERE DestPO <> '' AND DestPO=? AND SupplyOrgID=? AND SupplyDestOrgID IS NULL";
			$check_nopo = $this->db->query($sql_nopo,array($nopo));
			if($check_nopo->num_rows() > 0){
				$returns = array(
					'check_nopo' => 'false_nopo',
					'nopo' => $nopo
				);
				return $returns;
			}*/
			//*Checking Date*//
			$date_check = $data[0][5][1];
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date_check) OR  (strtotime(Date('Y-m-d')) < strtotime($date_check)) OR $tgl_delivery[$numb-1]=='0000-00-00' OR $date_check=='0000-00-00'){
				$returns = array(
					'check_date' => 'false_date',
					'tanggal_po' => $date_check//.":9"
				);
				$this->db->trans_rollback();
				$this->db->query($sql_add_import, array(null,'fail',null,'Invalid PO Date'));
				$idi = $this->db->insert_id();

				for ($i=5;$i<sizeof($data[0]);$i++) {
					if ($data[0][$i][5]!='') {
						$ff = $faq = $ffconPrice = $faqconPrice = $ffnetPrice = $faqnetPrice = $ffPrice = $faqPrice = 0;
						if ($data[0][$i][13]>0) {
						  $ff = $data[0][$i][13];
						  $jenis = 'FF';
						  $ffKA = $data[0][$i][16];
						  $ffBC = $data[0][$i][17];
						  $ffWaste = $data[0][$i][18];
						  $ffMouldy = $data[0][$i][19];
						  $ffInsect = $data[0][$i][20];
						  $ffSlaty = $data[0][$i][21];
						  $ffconPrice = $data[0][$i][22];
						  $ffnetPrice = $data[0][$i][22];
						  $ka = $result[0]['FFMoisture'] - $ffKA;
						  if ($ka>0) $ka = 0;
						  $bc = ($result[0]['FFBeanCount'] - $ffBC)*0.2;
						  if ($bc>0) $bc = 0;
						  $waste = $result[0]['FFWaste'] - $ffWaste;
						  if ($waste>0) $waste = 0;
						  $mouldy = $result[0]['FFMouldy'] - $ffMouldy;
						  if ($mouldy>0) $mouldy = 0;
						  $insect = $result[0]['FFInsect'] - $ffInsect;
						  if ($insect>0) $insect = 0;
						  $slaty = $result[0]['FFSlaty'] - $ffSlaty;
						  if ($slaty>0) $slaty = 0;
						  $guality_ff = $ka+$bc+$waste+$mouldy+$insect+$slaty;
						} else {
						  $faq = $data[0][$i][15];
						  $jenis = 'FAQ';
						  $faqKA = $data[0][$i][16];
						  $faqBC = $data[0][$i][17];
						  $faqWaste = $data[0][$i][18];
						  $faqMouldy = $data[0][$i][19];
						  $faqInsect = $data[0][$i][20];
						  $faqSlaty = $data[0][$i][21];
						  $faqconPrice = $data[0][$i][22];
						  $faqnetPrice = $data[0][$i][22];
						  $fka = $result[0]['FAQMoisture'] - $faqKA;
						  if ($fka>0) $fka = 0;
						  $fbc = ($result[0]['FAQBeanCount'] - $faqBC)*0.2;
						  if ($fbc>0) $fbc = 0;
						  $fwaste = $result[0]['FAQWaste'] - $faqWaste;
						  if ($fwaste>0) $fwaste = 0;
						  $fmouldy = $result[0]['FAQMouldy'] - $faqMouldy;
						  if ($fmouldy>0) $fmouldy = 0;
						  $finsect = $result[0]['FAQInsect'] - $faqInsect;
						  if ($finsect>0) $finsect = 0;
						  $fslaty = $result[0]['FAQSlaty'] - $faqSlaty;
						  if ($fslaty>0) $fslaty = 0;
						  $guality_faq = $kfa+$fbc+$fwaste+$fmouldy+$finsect+$fslaty;
						}
					}
				}
				if (trim($data[0][$i][0])!='' and trim($data[0][$i][5])!='') {
					if($jenis=='FF'){
						$weight = $ff;
					}else{
						$weight = $faq;
					}
					$this->db->query($sql_add_import_detail, array($idi,null,$data[0][5][1],null,$weight,null));
				}

				return $returns;
			}
			//**//
         $query = $this->db->query($sql_batch,array($bu,NULL,'Open',$no['number'],   $total_b,
            $total_b,NULL,$data[0][5][1],null,
            null,null,null,null,   null,null,null,NULL,NULL));
         $idbatch = $this->db->insert_id();
         $query = $this->db->query($sql_quality,array($bu));
         $result = $query->result_array();
         for ($i=5;$i<sizeof($data[0]);$i++) {
            if ($data[0][$i][5]!='') {
               $ff = $faq = $ffconPrice = $faqconPrice = $ffnetPrice = $faqnetPrice = $ffPrice = $faqPrice = 0;
               if ($data[0][$i][13]>0) {
                  $ff = $data[0][$i][13];
                  $jenis = 'FF';
                  $ffKA = $data[0][$i][16];
                  $ffBC = $data[0][$i][17];
                  $ffWaste = $data[0][$i][18];
                  $ffMouldy = $data[0][$i][19];
                  $ffInsect = $data[0][$i][20];
                  $ffSlaty = $data[0][$i][21];
                  $ffconPrice = $data[0][$i][22];
                  $ffnetPrice = $data[0][$i][22];
                  $ka = $result[0]['FFMoisture'] - $ffKA;
                  if ($ka>0) $ka = 0;
                  $bc = ($result[0]['FFBeanCount'] - $ffBC)*0.2;
                  if ($bc>0) $bc = 0;
                  $waste = $result[0]['FFWaste'] - $ffWaste;
                  if ($waste>0) $waste = 0;
                  $mouldy = $result[0]['FFMouldy'] - $ffMouldy;
                  if ($mouldy>0) $mouldy = 0;
                  $insect = $result[0]['FFInsect'] - $ffInsect;
                  if ($insect>0) $insect = 0;
                  $slaty = $result[0]['FFSlaty'] - $ffSlaty;
                  if ($slaty>0) $slaty = 0;
                  $guality_ff = $ka+$bc+$waste+$mouldy+$insect+$slaty;
               } else {
                  $faq = $data[0][$i][15];
                  $jenis = 'FAQ';
                  $faqKA = $data[0][$i][16];
                  $faqBC = $data[0][$i][17];
                  $faqWaste = $data[0][$i][18];
                  $faqMouldy = $data[0][$i][19];
                  $faqInsect = $data[0][$i][20];
                  $faqSlaty = $data[0][$i][21];
                  $faqconPrice = $data[0][$i][22];
                  $faqnetPrice = $data[0][$i][22];
                  $fka = $result[0]['FAQMoisture'] - $faqKA;
                  if ($fka>0) $fka = 0;
                  $fbc = ($result[0]['FAQBeanCount'] - $faqBC)*0.2;
                  if ($fbc>0) $fbc = 0;
                  $fwaste = $result[0]['FAQWaste'] - $faqWaste;
                  if ($fwaste>0) $fwaste = 0;
                  $fmouldy = $result[0]['FAQMouldy'] - $faqMouldy;
                  if ($fmouldy>0) $fmouldy = 0;
                  $finsect = $result[0]['FAQInsect'] - $faqInsect;
                  if ($finsect>0) $finsect = 0;
                  $fslaty = $result[0]['FAQSlaty'] - $faqSlaty;
                  if ($fslaty>0) $fslaty = 0;
                  $guality_faq = $kfa+$fbc+$fwaste+$fmouldy+$finsect+$fslaty;
               }
               if (trim($data[0][$i][0])!='' and trim($data[0][$i][5])!='') {
            $sql_transaction = "
               insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
               	FFVolumeBruto, FFVolumeNetto, FFContractPrice,FFNetPrice,FFTotalPayment,
                  	FFQualityKA, FFQualityBC, FFQualityWaste, FFQualityMouldy, FFQualityInsect, FFQualitySlaty,
                  	#FFRewardMoisture,FFRewardBeanCount,FFRewardWaste,FFRewardMouldy,FFRewardInsect,FFRewardSlaty,
                  FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
                  	FAQQualityKA, FAQQualityBC, FAQQualityWaste, FAQQualityMouldy, FAQQualityInsect, FAQQualitySlaty,
                  	#FAQRewardMoisture,FAQRewardBeanCount,FAQRewardWaste,FAQRewardMouldy,FAQRewardInsect,FAQRewardSlaty,
               	FFRewardBruto,FFRewardBonus,FFReward,FAQRewardBruto,FAQRewardBonus,FAQReward,
               	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
               select ?,?,?,FarmerID,   ?,?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,?,  ?,?,?,?,?,?,
                  $userid,now(),$userid,now() from ktv_farmer where oldFarmerID=? OR FarmerID=?";
					//*Checking Transaction Date*//
					$transdate_check = $data[0][$i][1];
					if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$transdate_check) OR  (strtotime(Date('Y-m-d')) < strtotime($transdate_check)) OR $transdate_check=='0000-00-00'){
						$returns = array(
							'check_transdate' => 'false_transdate',
							'tanggal_transaksi' => $transdate_check
						);
						$this->db->trans_rollback();
						$this->db->query($sql_add_import, array(null,'fail',null,'Invalid PO Date'));
						$idi = $this->db->insert_id();
						if($jenis=='FF'){
							$weight = $ff;
						}else{
							$weight = $faq;
						}
						$this->db->query($sql_add_import_detail, array($idi,null,$data[0][5][1],null,$weight,null));
						return $returns;
					}
					//**//
                  $query = $this->db->query($sql_transaction,array($idbatch,$data[0][$i][1],'Farmer',
                     $ff,$ff,$ffconPrice,$ffnetPrice,$ffnetPrice*$ff,   $ffKA,$ffBC,$ffWaste,$ffMouldy,$ffInsect,$ffSlaty,
                     $faq,$faq,$faqconPrice,$faqnetPrice,$faqnetPrice*$faq,   $faqKA,$faqBC,$faqWaste,$faqMouldy,$faqInsect,$faqSlaty,
                     $guality_ff,0,$guality_ff,$guality_faq,0,$guality_faq,$data[0][$i][5],$data[0][$i][5]));
                  if ($this->db->insert_id()>0)
                  $query = $this->db->query($sql_transaction_dtl,array(NULL,$this->db->insert_id(),$jenis,$ff+$faq,0,
                     0,$ff+$faq,null,$bu));
               }
            }
         }
		if ($this->db->trans_status() === TRUE && @$trans_status == ''){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

      return $query;
    }
    function injectDataBT_($bu,$no,$data,$userid){
      $sql_batch = "
         insert into ktv_supplychain_batch (SupplyOrgID, SupplyDestOrgID, SupplyDestStatus, SupplyBatchNumber,
         	VolumeBruto, VolumeNetto, VolumeNettoMoisture, SupplyBatchDate, PerwakilanOrgID, DeliveryDate, DestWeight, DestICS, DestDriver,
         	DestNoPolisi, DestKarungStart, DestKarungEnd, DestPO, NoPO,
         	CreatedBy, DateCreated, LastModifiedBy, DateUpdated)
         values (?,?,?,?,   ?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,   $userid,now(),$userid,now())";
      $sql_transaction = "
         insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
         	FFVolumeBruto, FFVolumeNetto, FFContractPrice,FFNetPrice,FFTotalPayment,
            	FFQualityKA, FFQualityBC, FFQualityWaste, FFQualityMouldy, FFQualityInsect, FFQualitySlaty,
            	#FFRewardMoisture,FFRewardBeanCount,FFRewardWaste,FFRewardMouldy,FFRewardInsect,FFRewardSlaty,
            FAQVolumeBruto, FAQVolumeNetto, FAQContractPrice,FAQNetPrice, FAQTotalPayment,
            	FAQQualityKA, FAQQualityBC, FAQQualityWaste, FAQQualityMouldy, FAQQualityInsect, FAQQualitySlaty,
            	#FAQRewardMoisture,FAQRewardBeanCount,FAQRewardWaste,FAQRewardMouldy,FAQRewardInsect,FAQRewardSlaty,
         	FFRewardBruto,FFRewardBonus,FFReward,FAQRewardBruto,FAQRewardBonus,FAQReward,
         	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
         select ?,?,?,FarmerID,   ?,?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,?,  ?,?,?,?,?,?,  ?,?,?,?,?,?,
            $userid,now(),$userid,now() from ktv_farmer where oldFarmerID=? OR FarmerID=?";
      $sql_transaction_dtl = "
         insert into ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID, PackageID, Type, Weight,WeightPackage,
            Moisture, Netto, NettoDelivery,
         	CreatedBy,DateCreated,LastModifiedBy,DateUpdated)
         select ?,?,PackageID,?,?,?,   ?,?,?,   $userid,now(),$userid,now() from ktv_supplychain_package
         where PackageWeight=0 and PackageSupplychainID=?";

      //koperasi
      for ($j=0;$j<sizeof($data);$j++) {
         $query = $this->db->query($sql_batch,array($bu,NULL,'Open','00'.($no['number']+$j),   0,0,NULL,$data[$j][2][1],null,
            null,null,null,null,   null,null,null,NULL,NULL));
         $idbatch = $this->db->insert_id();
         for ($i=2;$i<sizeof($data[$j]);$i++) {
            $ff = $faq = $ffconPrice = $faqconPrice = $ffnetPrice = $faqnetPrice = $ffPrice = $faqPrice = 0;
            if ($j%2==0) {
               $ff = $data[$j][$i][6];
               $jenis = 'FF';
               $ffKA = $data[$j][$i][7];
               $ffBC = $data[$j][$i][8];
               $ffWaste = $data[$j][$i][9];
               $ffMouldy = $data[$j][$i][10];
               $ffInsect = $data[$j][$i][11];
               $ffSlaty = $data[$j][$i][12];
               $ffconPrice = $data[$j][$i][14];
               $ffnetPrice = $data[$j][$i][15];
               $ffPrice = $data[$j][$i][16];
               $ffbruto = $data[$j][$i][13];
               $ffreward = $data[$j][$i][13];
            } else {
               $faq = $data[$j][$i][6];
               $jenis = 'FAQ';
               $faqKA = $data[$j][$i][7];
               $faqBC = $data[$j][$i][8];
               $faqWaste = $data[$j][$i][9];
               $faqMouldy = $data[$j][$i][10];
               $faqInsect = $data[$j][$i][11];
               $faqSlaty = $data[$j][$i][12];
               $faqconPrice = $data[$j][$i][14];
               $faqnetPrice = $data[$j][$i][15];
               $faqPrice = $data[$j][$i][16];
               $faqbruto = $data[$j][$i][13];
               $faqreward = $data[$j][$i][13];
            }
            if (trim($data[$j][$i][0])!='' and trim($data[$j][$i][5])!='') {
               $query = $this->db->query($sql_transaction,array($idbatch,$data[$j][$i][1],'Farmer',
                  $ff,$ff,$ffconPrice,$ffnetPrice,$ffPrice,   $ffKA,$ffBC,$ffWaste,$ffMouldy,$ffInsect,$ffSlaty,
                  $faq,$faq,$faqconPrice,$faqnetPrice,$faqPrice,   $faqKA,$faqBC,$faqWaste,$faqMouldy,$faqInsect,$faqSlaty,
                  $ffbruto,0,$ffreward,$faqbruto,0,$faqreward,$data[$j][$i][5],$data[$j][$i][5]));
               if ($this->db->insert_id()>0)
               $query = $this->db->query($sql_transaction_dtl,array(NULL,$this->db->insert_id(),$jenis,$data[$j][$i][6],0,
                  0,$data[$j][$i][6],null,$bu));
            }
         }
      }

      return $query;
    }
    function cek_farmer($data,$colf,$start) {
         $a = 0;
         for ($i = 'A'; $i<'ZZ'; $i++) {
             if ($a==$colf) {
                  $colfa = $i;
                  break;
             }
             $a++;
         }
         $sql = "SELECT FarmerID, FarmerName FROM ktv_farmer WHERE FarmerID=?";
         for ($i=$start;$i<sizeof($data);$i++) {
            if ($data[$i][$colf]!='') {
               $query = $this->db->query($sql, array($data[$i][$colf]));
               $result = $query->result_array();
               if ($result[0]['FarmerID']!=$data[$i][$colf] OR $result[0]['FarmerName']=='')
                  $id[] = $data[$i][$colf].' (baris '.($i+1).', kolom '.$colfa.')';
            }
         }
         if (is_array($id)) $resul['name'] = implode(', ', $id);
         return $resul;
    }
	function readDataTemplateSupplychain()
    {
        $sql = "SELECT TmplFile AS id, TmplName AS label FROM tmpl_supplychain WHERE StatusCode='active'";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;
    }
    
    function getBulan($bulan){
        if($bulan=='Januari'){
            return '01';
        }else if($bulan=='Februari'){
            return '02';
        }else if($bulan=='Maret'){
            return '03';
        }else if($bulan=='April'){
            return '04';
        }else if($bulan=='Mei'){
            return '05';
        }else if($bulan=='Juni'){
            return '06';
        }else if($bulan=='Juli'){
            return '07';
        }else if($bulan=='Agustus'){
            return '08';
        }else if($bulan=='September'){
            return '09';
        }else if($bulan=='Oktober'){
            return '10';
        }else if($bulan=='November'){
            return '11';
        }else{
            return '12';
        }
    }
            
    function injectDataCargillDT($wh, $data, $userid){
        $this->db->trans_start();
        $sql_batch = "INSERT INTO ktv_supplychain_batch (SupplyOrgID, SupplyDestOrgID, SupplyDestStatus, SupplyBatchNumber, DestPO, SupplyBatchDate, VolumeBruto, VolumeNetto, PerwakilanOrgID,DateCreated, CreatedBy)
                      VALUES (?,?,?,?,   ?,?,?,?,?,now(),?)";
        $sql_transaction = "INSERT INTO ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
                FFVolumeBruto, FFVolumePackage,FFVolumeMoisture,FFVolumeNetto,
                FAQVolumeBruto, FAQVolumePackage,FAQVolumeMoisture,FAQVolumeNetto,
                FFRewardBruto,FFRewardBonus,FFReward, FFContractPrice, FFNetPrice, FFTotalPayment,
                FAQRewardBruto,FAQRewardBonus,FAQReward, FAQContractPrice, FAQNetPrice, FAQTotalPayment, Dp, StatusCode,
            	WeightBy, SupervisorID, AdminID, VehicleNo,DateCreated, CreatedBy,
                FakturNumber,InvoiceNumber,FFBeratBersihSetara,FFVerifikasi,FAQBeratBersihSetara,FAQVerifikasi,IsQuota)
                VALUES (?,?,?,?,   ?,?,?,?,   ?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,?,?,?,?, ?,?,?,?,now(),?,   ?,?,?,?,?,?,?)";
        $sql_quality = "SELECT b.* FROM `ktv_supplychain_quality_standard` a
                    LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.StandardID=b.StandardID WHERE StandardSupplychainID=?  ORDER BY `Order`";
        $sql_quality_standard_detail = "INSERT INTO ktv_supplychain_transaction_quality(DetailID,StandardID,SupplyTransID,FAQStandard,FAQResult,FAQReward) VALUES (?,?,?,?,?,?)";
        $sql_batch_update = "UPDATE ktv_supplychain_batch SET VolumeBruto=?,VolumeNetto=?,DestWeight=?,SupplyBatchDate=?,DeliveryDate=? WHERE SupplyBatchID=?";
        $sql_supplychainid = "SELECT SupplychainID FROM ktv_supplychain_org_view WHERE OrgID=? AND (OrgType='Pedagang' OR OrgType='sce')";
        $sql_transaction_dtl = "INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,SupplyTransID,PackageID,Type,Weight,Netto,NettoDelivery,DateCreated,CreatedBy) VALUES (?,?,?,?,?,?,?,NOW(),$userid)";
        $sql_package = "SELECT PackageID FROM ktv_supplychain_package WHERE PackageSupplychainID=?";
        
        if($data[0][1][2]!=""){ //BU - DT - WH
            $bu_orgid = $this->db->query($sql_supplychainid,array($data[0][1][2]))->row()->SupplychainID; 
            $dt_orgid = $this->db->query($sql_supplychainid,array($data[0][1][3]))->row()->SupplychainID;
            $wh_orgid = 5;
            $DestPO = $data[0][1][0];
            $check_nopo = $this->db->query("SELECT * FROM ktv_supplychain_batch WHERE DestPO=?",array($DestPO));
            if($check_nopo->num_rows() > 0){
                $returns = array(
                    'check_nopo' => 'false_nopo',
                    'nopo' => $DestPO//.":1"//."|".$sceid[$ii]
                );
                return $returns;
            }
            $bu_batchnumber = getBatchNumber($bu_orgid);
            $dt_batchnumber = getBatchNumber($dt_orgid);
            $wh_batchnumber = getBatchNumber($wh_orgid);
            
            $dt_invoicenumber = getInvoiceNumber($dt_orgid);
            $wh_invoicenumber = getInvoiceNumber($wh_orgid);
            //BU
            $query = $this->db->query($sql_batch, array($bu_orgid, $dt_orgid, 'Delivered', $bu_batchnumber, $DestPO, NULL, NULL, NULL, NULL, $userid));
            $bu_batchid = $this->db->insert_id();
            $bu_quality = $this->db->query($sql_quality,array($bu_orgid));
            $bu_packageid = $this->db->query($sql_package,array($bu_orgid))->row()->PackageID;
            $jumlah = count($data[0]);
            $total_berat = 0;
            $wh_total_berat = 0;
            $minDate = '3000-01-01';
            $maxDate = '1991-01-01';
            for($i=1;$i<$jumlah;$i++){
                $tgl = $data[0][$i][6];
                if(strlen($tgl)==1){$tgl = "0".$tgl;}
                $bu_datetrans = $data[0][$i][4]."-".$this->getBulan($data[0][$i][5])."-".$tgl;
                $bu_totalpayment = $data[0][$i][12] * $data[0][$i][16];
                if (intval(str_replace("-", "", $bu_datetrans)) > intval(str_replace("-", "", $maxDate))) {
                    $maxDate = $bu_datetrans;
                }
                if (intval(str_replace("-", "", $bu_datetrans)) < intval(str_replace("-", "", $minDate))) {
                    $minDate = $bu_datetrans;
                }
                
                $bu_invoicenumber = getInvoiceNumber($bu_orgid);
                $query = $this->db->query($sql_transaction, array($bu_batchid, $bu_datetrans, 'FarmerNonCert', $data[0][$i][7],
                0, NULL, NULL, 0,
                $data[0][$i][12], NULL, NULL, $data[0][$i][12],
                NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, $data[0][$i][16], $bu_totalpayment, NULL, NULL,
                NULL, NULL, NULL, NULL, $userid,
                $data[0][$i][1], $bu_invoicenumber, NULL, NULL, $data[0][$i][12], NULL, '1'));
                $bu_transid = $this->db->insert_id();

                $this->db->query($sql_transaction_dtl, array(NULL, $bu_transid, $bu_packageid, 'FAQ', $data[0][$i][12], $data[0][$i][12], $data[0][$i][12]));

                if($bu_quality->num_rows() > 0){
                    foreach($bu_quality->result() as $row){
                        if (strpos($row->Name, 'Bean Count') !== false) {
                            $FAQResult = $data[0][$i][13];
                        }else if (strpos($row->Name, 'Moisture') !== false) {
                            $FAQResult = $data[0][$i][14];
                        }else if (strpos($row->Name, 'Waste') !== false) {
                            $FAQResult = $data[0][$i][15];
                        }
                        $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                        $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $bu_transid, $row->FAQStandard, $FAQResult, $FAQReward));
                    }    
                }
                $total_berat = $total_berat + $data[0][$i][12];
                $wh_total_berat = $wh_total_berat + $data[0][$i][18];
            }
            $this->db->query($sql_batch_update, array($total_berat,$total_berat,$total_berat,$minDate,$maxDate,$bu_batchid));
            $for = floor($total_berat / 62.5);
            $a = 0;
            for($j=0;$j<$for;$j++){
                $this->db->query($sql_transaction_dtl, array($bu_batchid, NULL, $bu_packageid, 'FAQ', 62.5, 62.5, 62.5));
                $a = $a + 62.5;
            }
            $sisa = $total_berat - $a;
            if($sisa != 0){
                $this->db->query($sql_transaction_dtl, array($bu_batchid, NULL, $bu_packageid, 'FAQ', $sisa, $sisa, $sisa));
            }
            //DT
            $query = $this->db->query($sql_batch, array($dt_orgid, $wh_orgid, 'Delivered', $dt_batchnumber, $DestPO, NULL, NULL, NULL, NULL, $userid));
            $dt_batchid = $this->db->insert_id();
            $dt_quality = $this->db->query($sql_quality,array($dt_orgid));
            $dt_packageid = $this->db->query($sql_package,array($dt_orgid))->row()->PackageID;

            $query = $this->db->query($sql_transaction, array($dt_batchid, $maxDate, 'Batch', $bu_batchnumber,
                NULL, NULL, NULL, NULL,
                $total_berat, NULL, NULL, $total_berat,
                NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, $userid,
                NULL, $dt_invoicenumber, NULL, NULL, $total_berat, NULL, '1'));
            $dt_transid = $this->db->insert_id();

            $this->db->query($sql_transaction_dtl, array(NULL, $dt_transid, $dt_packageid, 'FAQ', $total_berat, $total_berat, $total_berat));

            $this->db->query($sql_batch_update, array($total_berat,$total_berat,$total_berat,$maxDate,$maxDate,$dt_batchid));    
            $for = floor($total_berat / 62.5);
            $a = 0;
            for($j=0;$j<$for;$j++){
                $this->db->query($sql_transaction_dtl, array($dt_batchid, NULL, $dt_packageid, 'FAQ', 62.5, 62.5, 62.5));
                $a = $a + 62.5;
            }
            $sisa = $total_berat - $a;
            if($sisa != 0){
                $this->db->query($sql_transaction_dtl, array($dt_batchid, NULL, $dt_packageid, 'FAQ', $sisa, $sisa, $sisa));
            }
            //WH
            $whDate = date('Y-m-d',date(strtotime("+1 day", strtotime($maxDate))));
            $query = $this->db->query($sql_batch, array($wh_orgid, NULL, 'Open', $wh_batchnumber, $DestPO, NULL, NULL, NULL, NULL, $userid));
            $wh_batchid = $this->db->insert_id();
            $wh_quality = $this->db->query($sql_quality,array($wh_orgid));
            $wh_packageid = $this->db->query($sql_package,array($wh_orgid))->row()->PackageID;

            $query = $this->db->query($sql_transaction, array($wh_batchid, $whDate, 'Batch', $dt_batchnumber,
                NULL, NULL, NULL, NULL,
                $wh_total_berat, NULL, NULL, $wh_total_berat,
                NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, $userid,
                NULL, $wh_invoicenumber, NULL, NULL, $wh_total_berat, NULL, '1'));
            $wh_transid = $this->db->insert_id();
            $this->db->query($sql_transaction_dtl, array(NULL, $wh_transid, $wh_packageid, 'FAQ', $wh_total_berat, $wh_total_berat, $wh_total_berat));
            $this->db->query($sql_batch_update, array($wh_total_berat,$wh_total_berat,$wh_total_berat,$whDate,NULL,$wh_batchid));    
        }else{ //DT - WH
            //$bu_orgid = $this->db->query($sql_supplychainid,array($data[0][1][2]))->row()->SupplychainID; 
            $dt_orgid = $this->db->query($sql_supplychainid,array($data[0][1][3]))->row()->SupplychainID;
            $wh_orgid = 5;
            $DestPO = $data[0][1][0];
            $check_nopo = $this->db->query("SELECT * FROM ktv_supplychain_batch WHERE DestPO=?",array($DestPO));
            if($check_nopo->num_rows() > 0){
                $returns = array(
                    'check_nopo' => 'false_nopo',
                    'nopo' => $DestPO//.":1"//."|".$sceid[$ii]
                );
                return $returns;
            }
            //$bu_batchnumber = getBatchNumber($bu_orgid);
            $dt_batchnumber = getBatchNumber($dt_orgid);
            $wh_batchnumber = getBatchNumber($wh_orgid);
            //$bu_invoicenumber = getInvoiceNumber($bu_orgid);
            $dt_invoicenumber = getInvoiceNumber($dt_orgid);
            $wh_invoicenumber = getInvoiceNumber($wh_orgid);
            //DT
            $query = $this->db->query($sql_batch, array($dt_orgid, $wh_orgid, 'Delivered', $dt_batchnumber, $DestPO, NULL, NULL, NULL, NULL, $userid));
            $dt_batchid = $this->db->insert_id();
            $dt_quality = $this->db->query($sql_quality,array($dt_orgid));
            $dt_packageid = $this->db->query($sql_package,array($dt_orgid))->row()->PackageID;
            $jumlah = count($data[0]);
            $total_berat = 0;
            $wh_total_berat = 0;
            $minDate = '3000-01-01';
            $maxDate = '1991-01-01';
            for($i=1;$i<$jumlah;$i++){
                $tgl = $data[0][$i][6];
                if(strlen($tgl)==1){$tgl = "0".$tgl;}
                $dt_datetrans = $data[0][$i][4]."-".$this->getBulan($data[0][$i][5])."-".$tgl;
                $dt_totalpayment = $data[0][$i][12] * $data[0][$i][16];
                if (intval(str_replace("-", "", $dt_datetrans)) > intval(str_replace("-", "", $maxDate))) {
                    $maxDate = $dt_datetrans;
                }
                if (intval(str_replace("-", "", $dt_datetrans)) < intval(str_replace("-", "", $minDate))) {
                    $minDate = $dt_datetrans;
                }

                $query = $this->db->query($sql_transaction, array($dt_batchid, $dt_datetrans, 'FarmerNonCert', $data[0][$i][7],
                0, NULL, NULL, 0,
                $data[0][$i][12], NULL, NULL, $data[0][$i][12],
                NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, $data[0][$i][16], $dt_totalpayment, NULL, NULL,
                NULL, NULL, NULL, NULL, $userid,
                $data[0][$i][1], $dt_invoicenumber, NULL, NULL, $data[0][$i][12], NULL, '1'));
                $dt_transid = $this->db->insert_id();

                $this->db->query($sql_transaction_dtl, array(NULL, $dt_transid, $dt_packageid, 'FAQ', $data[0][$i][12], $data[0][$i][12], $data[0][$i][12]));

                if($dt_quality->num_rows() > 0){
                    foreach($dt_quality->result() as $row){
                        if (strpos($row->Name, 'Bean Count') !== false) {
                            $FAQResult = $data[0][$i][13];
                        }else if (strpos($row->Name, 'Moisture') !== false) {
                            $FAQResult = $data[0][$i][14];
                        }else if (strpos($row->Name, 'Waste') !== false) {
                            $FAQResult = $data[0][$i][15];
                        }
                        $FAQReward = $this->calculateReward($row->FAQFormula,$row->FAQStandard,$FAQResult);
                        $this->db->query($sql_quality_standard_detail, array($row->DetailID, $row->StandardID, $dt_transid, $row->FAQStandard, $FAQResult, $FAQReward));
                    }    
                }
                $total_berat = $total_berat + $data[0][$i][12];
                $wh_total_berat = $wh_total_berat + $data[0][$i][18];
            }
            $this->db->query($sql_batch_update, array($total_berat,$total_berat,$total_berat,$minDate,$maxDate,$dt_batchid));
            $for = floor($total_berat / 62.5);
            $a = 0;
            for($j=0;$j<$for;$j++){
                $this->db->query($sql_transaction_dtl, array($dt_batchid, NULL, $dt_packageid, 'FAQ', 62.5, 62.5, 62.5));
                $a = $a + 62.5;
            }
            $sisa = $total_berat - $a;
            if($sisa != 0){
                $this->db->query($sql_transaction_dtl, array($dt_batchid, NULL, $dt_packageid, 'FAQ', $sisa, $sisa, $sisa));
            }
            //DT
            /*$query = $this->db->query($sql_batch, array($dt_orgid, $wh_orgid, 'Delivered', $dt_batchnumber, $DestPO, NULL, NULL, NULL, NULL, $userid));
            $dt_batchid = $this->db->insert_id();
            $dt_quality = $this->db->query($sql_quality,array($dt_orgid));
            $dt_packageid = $this->db->query($sql_package,array($dt_orgid))->row()->PackageID;

            $query = $this->db->query($sql_transaction, array($dt_batchid, $maxDate, 'Batch', $bu_batchnumber,
                NULL, NULL, NULL, NULL,
                $total_berat, NULL, NULL, $total_berat,
                NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, $userid,
                NULL, $dt_invoicenumber, NULL, NULL, $total_berat, NULL, '1'));
            $dt_transid = $this->db->insert_id();

            $this->db->query($sql_transaction_dtl, array(NULL, $dt_transid, $dt_packageid, 'FAQ', $total_berat, $total_berat, $total_berat));

            $this->db->query($sql_batch_update, array($total_berat,$total_berat,$total_berat,$maxDate,$maxDate,$dt_batchid));    
            $for = floor($total_berat / 62.5);
            $a = 0;
            for($j=0;$j<$for;$j++){
                $this->db->query($sql_transaction_dtl, array($dt_batchid, NULL, $dt_packageid, 'FAQ', 62.5, 62.5, 62.5));
                $a = $a + 62.5;
            }
            $sisa = $total_berat - $a;
            if($sisa != 0){
                $this->db->query($sql_transaction_dtl, array($dt_batchid, NULL, $dt_packageid, 'FAQ', $sisa, $sisa, $sisa));
            }*/
            //WH
            $whDate = date('Y-m-d',date(strtotime("+1 day", strtotime($maxDate))));
            $query = $this->db->query($sql_batch, array($wh_orgid, NULL, 'Open', $wh_batchnumber, $DestPO, NULL, NULL, NULL, NULL, $userid));
            $wh_batchid = $this->db->insert_id();
            $wh_quality = $this->db->query($sql_quality,array($wh_orgid));
            $wh_packageid = $this->db->query($sql_package,array($wh_orgid))->row()->PackageID;

            $query = $this->db->query($sql_transaction, array($wh_batchid, $whDate, 'Batch', $dt_batchnumber,
                NULL, NULL, NULL, NULL,
                $wh_total_berat, NULL, NULL, $wh_total_berat,
                NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, $userid,
                NULL, $wh_invoicenumber, NULL, NULL, $wh_total_berat, NULL, '1'));
            $wh_transid = $this->db->insert_id();
            $this->db->query($sql_transaction_dtl, array(NULL, $wh_transid, $wh_packageid, 'FAQ', $wh_total_berat, $wh_total_berat, $wh_total_berat));
            $this->db->query($sql_batch_update, array($wh_total_berat,$wh_total_berat,$wh_total_berat,$whDate,NULL,$wh_batchid));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            return TRUE;
        }else{
            return array('name'=>'false');
        }
        
    }

}
?>
