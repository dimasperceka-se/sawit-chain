<?php

class Mdashboard extends CI_Model
{

    function __construct()
    {
        parent::__construct();
	}
         

    function readDataTraceabilityIndex($user = '', $awal = '', $akhir = '', $wh = '', $ch = '', $bs = '', $cert = '')
    {
        /*if($ch=='5'){
            $ch = '';
            $wh = 5;
        }*/
        if($wh!='' && $wh!='false'){        
            $w1 = ''; $w2 = '';
        }else{ 
            $w1 = '/*';  $w2 = '*/'; 
        }
        if($ch!='' && $ch!='false'){        
            $c1 = ''; $c2 = ''; //$w1 = '/*';  $w2 = '*/';
        }else{ 
            $c1 = '/*';  $c2 = '*/';
        }
        if($bs!='' && $bs!='false'){        
            $b1 = ''; $b2 = ''; //$w1 = '/*';  $w2 = '*/'; $c1 = '/*';  $c2 = '*/';
        }else{ 
            $b1 = '/*';  $b2 = '*/'; 
        }
        $where_all = "";
        if($wh!='' && $wh!='false'){
            $wh_orgid = "";//"AND (supplyorgid_2 IS NULL OR 2_destorgid=$wh)";
            if($ch!='' && $ch!='false'){
                if($bs!='' && $bs!='false'){
                    $transid = "transid_1";
                    $date = "date_1";
                    $label = "'Warehouse'";
                    $label_name = "IF(name_2 IS NULL, 'Open Batch', name_1)";
                    $group_by = "supplyorgid_2";
                    $group_by_trend = "supplyorgid_1";
                    $berat = "netto_1";
                    $pending = "supplyorgid_2";
                    $wherew = "AND supplyorgid_1 IS NOT NULL";
                }else{
                    $transid = "transid_2";
                    $date = "date_1";
                    $label = "'Warehouse'";
                    $label_name = "IF(name_3 IS NULL, 'Open Batch', name_2)";
                    $group_by = "supplyorgid_3";
                    $group_by_trend = "supplyorgid_2";
                    $berat = "netto_farmer_2";
                    $pending = "supplyorgid_3";
                    $wherew = "AND supplyorgid_2 IS NOT NULL";
                }
            }else{
                $transid = "name_3";
                $date = "date_1";
                $group_by = "supplyorgid_3";
                $group_by_trend = "supplyorgid_3";
                $wh_orgid = "";
                $label = "'Warehouse'";
                $label_name = "IFNULL(name_3, 'Open Batch')";
                $berat = "netto_farmer_3";
                $pending = "supplyorgid_3";
                $where_all = "AND supplyorgid_3 IS NOT NULL";
                $wherew = "AND supplyorgid_3 IS NOT NULL";
            }
        }else{
            if($ch!='' && $ch!='false'){
                if($bs!='' && $bs!='false'){
                    $transid = "transid_1";
                    $date = "date_1";
                    $label = "'Warehouse'";
                    $label_name = "IF(name_2 IS NULL, 'Open Batch', name_1)";
                    $group_by = "supplyorgid_2";
                    $group_by_trend = "supplyorgid_1";
                    $berat = "netto_1";
                    $pending = "supplyorgid_2";
                    $wherew = "AND supplyorgid_1 IS NOT NULL";
                }else{
                    $transid = "transid_2";
                    $date = "date_1";
                    $label = "'Warehouse'";
                    $label_name = "IF(name_3 IS NULL, 'Open Batch', name_2)";
                    $group_by = "supplyorgid_3";
                    $group_by_trend = "supplyorgid_2";
                    $berat = "netto_farmer_2";
                    $pending = "supplyorgid_3";
                    $wherew = "AND supplyorgid_2 IS NOT NULL";
                }
            }else{
                $transid = "name_3";
                $date = "date_1";
                $group_by = "supplyorgid_3";
                $group_by_trend = "supplyorgid_3";
                $wh_orgid = "";
                $label = "'Warehouse'";
                $label_name = "IFNULL(name_3, 'Open Batch')";
                $berat = "netto_farmer_3";
                $pending = "supplyorgid_3";
                $where_all = "AND supplyorgid_3 IS NOT NULL";
                $wherew = "AND supplyorgid_3 IS NOT NULL";
            }
        }
        //$pending = "netto_farmer_3";
        //$group_by = "name_3";
        
        $label = "''";
        //if($cert!='' && $cert!='false'){    $ct1 = ''; $ct2 = ''; }else{ $ct1 = '/*';  $ct2 = '*/'; }
        
        if($wh!='' && $wh!='false'){        
            $w1 = ''; $w2 = '';
        }else{ 
            $w1 = '/*';  $w2 = '*/'; 
        }
        if($ch!='' && $ch!='false'){        
            $c1 = ''; $c2 = ''; //$w1 = '/*';  $w2 = '*/';
        }else{ 
            $c1 = '/*';  $c2 = '*/';
        }
        if($bs!='' && $bs!='false'){        
            $b1 = ''; $b2 = ''; //$w1 = '/*';  $w2 = '*/'; $c1 = '/*';  $c2 = '*/';
        }else{ 
            $b1 = '/*';  $b2 = '*/'; 
        }
        if($w1!='' && $c1!=''){
            $c3 = "/*";
        }else{
            $c3 = "";
        }
        if($b1!='' && $c1!=''){
            $b3 = "/*";
        }else{
            $b3 = "";
        }
        if($ch=='5'){
            if($bs==''){
                $p = "";
            }else{
                $p = ")";
            }
            $cch1 = "AND (IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3)=5 AND IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife' $p"; $cch2 = "";
        }else{
            $cch1 = ""; $cch2 = "";
        }
        $dt = "SELECT
                        FarmerID, 
                        FarmerName,
                        rpt.CPGId,
                        GroupName,
                        Village,
                        SubDistrict,
                        District,
                        date_1 FarmerDate,
                        IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1) SupplyType,
                        
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        batchid_1) batchid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        transid_1) transid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        date_1) date_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        supplyorgid_1) supplyorgid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        faktur_1) faktur_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        bruto_1) bruto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        netto_1) netto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        orgtype_1) orgtype_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        name_1) name_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        batchnumber_1) batchnumber_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        destpo_1) destpo_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        status_1) status_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        deliverydate_1) deliverydate_1,
                        
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),batchid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, batchid_2)) batchid_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),transid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, transid_2)) transid_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),date_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, date_2)) date_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),supplyorgid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, supplyorgid_2)) supplyorgid_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),faktur_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, faktur_2)) faktur_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),bruto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, bruto_2)) bruto_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),netto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, netto_farmer_2)) netto_farmer_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),bruto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, bruto_farmer_2)) bruto_farmer_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),netto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, netto_2)) netto_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),orgtype_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, orgtype_2)) orgtype_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),name_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, name_2)) name_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),batchnumber_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, batchnumber_2)) batchnumber_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),destpo_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, destpo_2)) destpo_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),status_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, status_2)) status_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),deliverydate_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, deliverydate_2)) deliverydate_2,

                        IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3) supplyorgid_3,
                        IF(orgtype_2='Gudang', date_2, date_3) date_3,
                        IF(orgtype_2='Gudang', transid_2, transid_3) transid_3,
                        IF(orgtype_2='Gudang', batchid_2, batchid_3) batchid_3,
                        IF(orgtype_2='Gudang', orgid_2, orgid_3) orgid_3,
                        IF(orgtype_2='Gudang', name_2, name_3) name_3,
                        IF(orgtype_2='Gudang', faktur_2, faktur_3) faktur_3,
                        IF(orgtype_2='Gudang', bruto_2, bruto_3) bruto_3,
                        IF(orgtype_2='Gudang', netto_2, netto_3) netto_3,
                        IF(orgtype_2='Gudang', bruto_farmer_2, bruto_farmer_3) bruto_farmer_3,
                        IF(orgtype_2='Gudang', netto_farmer_2, netto_farmer_3) netto_farmer_3,
                        IF(orgtype_2='Gudang', destpo_2, destpo_3) destpo_3,
                        IF(orgtype_2='Gudang', status_2, status_3) status_3
                    FROM
                        rpt_tc_trans_detail rpt
                        LEFT JOIN view_supplychain_org dest ON dest.SupplychainID=supplydestorgid_1
                        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),batchid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, batchid_2))
                        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,
                        batchid_1)
                        LEFT JOIN (
                            SELECT DISTINCT
                                    ch.SupplychainID, 
                                    IF(i.ValidityStart IS NOT NULL && i.ValidityStart!='0000-00-00', i.ValidityStart, i.CertificationStart) ValidityStart,
                                    IF(i.ValidityEnd IS NOT NULL && i.ValidityEnd!='0000-00-00', i.ValidityEnd, i.CertificationEnd) ValidityEnd
                            FROM
                                ktv_certification_holders ch 
                                LEFT JOIN ktv_ims i ON i.CertHolderID=ch.CertHolderID
                            WHERE
                                ch.StatusCode='active'
                        ) ch1 ON ch1.SupplychainID=supplyorgid_1 AND date_1 BETWEEN ch1.ValidityStart AND ch1.ValidityEnd
                        LEFT JOIN (
                            SELECT DISTINCT
                                    ch.SupplychainID, 
                                    IF(i.ValidityStart IS NOT NULL && i.ValidityStart!='0000-00-00', i.ValidityStart, i.CertificationStart) ValidityStart,
                                    IF(i.ValidityEnd IS NOT NULL && i.ValidityEnd!='0000-00-00', i.ValidityEnd, i.CertificationEnd) ValidityEnd
                            FROM
                                ktv_certification_holders ch 
                                LEFT JOIN ktv_ims i ON i.CertHolderID=ch.CertHolderID
                            WHERE
                                ch.StatusCode='active'
                        ) ch2 ON ch2.SupplychainID=supplyorgid_2 AND date_2 BETWEEN ch2.ValidityStart AND ch2.ValidityEnd
                    WHERE partnerid_1=8 AND IFNULL(sb2.SupplyBatchDate, sb1.SupplyBatchDate) BETWEEN ? AND ?
                        $w1
                            AND (IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3)=$wh  
                            $c1 OR IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3) IS NULL $c2
                            $c3)
                        $w2
                        $cch1
                        $c1
                            AND (IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),supplyorgid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, supplyorgid_2))=$ch
                            $b1 OR IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),supplyorgid_1,
                            IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='cocoalife', NULL, supplyorgid_2)) IS NULL $b2
                            $b3)
                        $c2
                        $cch2
                        $b1
                            AND IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer' && (transcert_2 IS NULL OR transcert_2!='cocoalife'),'utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,
                            supplyorgid_1)=$bs
                        $b2
                    "; 
        
        $sql1 = "SELECT
                    $label label,
                    COUNT(DISTINCT f.FarmerID) farmer,
                    COUNT(DISTINCT IF(f.isCertified='1',f.FarmerID,NULL)) farmer_certified,
                    COUNT(DISTINCT IF(f.isCertified!='1' && ft.FarmertypeID='2',f.FarmerID,NULL)) farmer_uncertified
                    /*COUNT(DISTINCT r.farmer_id) farmer_selling,
                    COUNT(DISTINCT IF(r.farmer_iscertified='1',r.farmer_id,NULL)) farmer_certified_selling,
                    (COUNT(DISTINCT r.farmer_id) - COUNT(DISTINCT IF(r.farmer_iscertified='1',r.farmer_id,NULL))) farmer_uncertified_selling*/
                FROM
                    ktv_cocoa_farmer f
                    LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
                    LEFT JOIN ktv_cpg c ON c.CPGid = f.CPGid
                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
                WHERE
                    f.StatusCode = 'active' AND cp.PartnerID = 8";
        $sql1 = "SELECT
                    '' label,
                    COUNT(DISTINCT ccf.FarmerID) farmer,
                    COUNT(DISTINCT IF(i.IMSID!='141',ccf.FarmerID,NULL)) farmer_certified,
                    COUNT(DISTINCT IF(i.IMSID='141',ccf.FarmerID,NULL)) farmer_uncertified 
                FROM 
                    ktv_ims i 
                    LEFT JOIN ktv_first_buyer fb ON fb.FirstBuyerID=i.FirstBuyerID
                    LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = i.CertHolderID
                    LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.IMSID=i.IMSID
                WHERE
                    NOW() BETWEEN i.CertificationStart  AND i.CertificationEnd
                    AND fb.FirstBuyerPartnerID=8 $c1 AND ch.SupplychainID=? $c2
                    AND ( i.CertificationStart BETWEEN ? AND ? OR i.CertificationEnd BETWEEN ? AND ? ) 
                    AND i.CertEventStatus = '2'";
        $query_traceability_farmer = $this->db->query($sql1, array($ch, $awal, $akhir, $awal, $akhir));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $sql_tc = "SELECT
                    COUNT(DISTINCT dt.FarmerID) farmer_selling,
                    COUNT(DISTINCT IF(dt.SupplyType='utz',dt.FarmerID, NULL)) farmer_certified_selling,
                    COUNT(DISTINCT IF(dt.SupplyType='cocoalife',dt.FarmerID, NULL)) farmer_uncertified_selling,
                    COUNT(DISTINCT IFNULL(dt.transid_1, dt.transid_2)) farmer_transaction,
                    COUNT(DISTINCT dt.supplyorgid_1) buying_unit
                FROM
                    ($dt) dt WHERE 1=1 $wherew";
        $tc_farmer = $this->db->query($sql_tc, array("$awal 00:00:00", "$akhir 23:59:59"))->result_array();
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $sql1 = "SELECT
                    dt.label, dt.farmer, dt.farmer_certified, dt.farmer_uncertified,
                    dt.farmer farmer_selling, dt.farmer_certified farmer_certified_selling, dt.farmer_uncertified farmer_uncertified_selling
                FROM
                (
                SELECT
                        '' label,
                        COUNT(DISTINCT IFNULL(st3.SupplyID, IFNULL(st2.SupplyID,st.SupplyID))) farmer,
                        COUNT(DISTINCT 
                            CASE
                                    WHEN st3.SupplyTransID IS NOT NULL AND st3.TransCertification='utz' THEN st3.SupplyID
                                    WHEN st2.SupplyTransID IS NOT NULL AND st2.TransCertification='utz' THEN st2.SupplyID
                                    WHEN st.SupplyTransID  IS NOT NULL AND st.TransCertification ='utz' THEN st.SupplyID
                                    ELSE NULL
                            END
                        ) farmer_certified,
                        COUNT(DISTINCT 
                            CASE
                                    WHEN st3.SupplyTransID IS NOT NULL AND st3.TransCertification='cocoalife' THEN st3.SupplyID
                                    WHEN st2.SupplyTransID IS NOT NULL AND st2.TransCertification='cocoalife' THEN st2.SupplyID
                                    WHEN st.SupplyTransID  IS NOT NULL AND st.TransCertification ='cocoalife' THEN st.SupplyID
                                    ELSE NULL
                            END
                        ) farmer_uncertified
                FROM
                        ktv_supplychain_batch sb
                        LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID AND st2.SupplyType='Batch'
                        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                        LEFT JOIN view_supplychain_org org ON org.SupplychainID=sb.SupplyOrgID
                        LEFT JOIN ktv_village kv ON kv.VillageID = org.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                        LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                WHERE 1=1 
                        AND kp.ProvinceID=?
                        AND sb.DeliveryDate BETWEEN '2017-01-01' AND '2018-01-01'
                ) dt";
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        //$query_traceability_farmer       = $this->db->query(sprintf($this->traceability_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        
        $sql2 = "SELECT
                    $label_name label,
                    /*SUM(g.Production) production,
                    SUM(IF(f.isCertified='1',g.Production,0)) production_certified,
                    SUM(IF(f.isCertified!='1' && ft.FarmertypeID='2',g.Production,0)) production_uncertified,*/
                    SUM($berat) farmer_selling,
                    SUM(IF(r.farmer_iscertified='1',$berat,0)) farmer_certified_selling,
                    SUM(IF(r.farmer_iscertified!='1' && ft.FarmertypeID='2',$berat,0)) farmer_uncertified_selling
                FROM ktv_cocoa_farmer f
                    LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
                    LEFT JOIN ktv_cpg c ON c.CPGid = f.CPGid
                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
                    LEFT JOIN rpt_traceability r ON r.farmer_id=f.FarmerID $wh_orgid $w1 AND r.wh_orgid=? $w2 $c1 AND (r.1_supplychainid=? OR r.2_supplychainid=?) $c2 $b1 AND r.1_supplychainid=? $b2 AND IFNULL(r.1_date,r.2_date) BETWEEN ? AND ?
                    /*LEFT JOIN (
                        SELECT dt.FarmerID, dt.IsCertified,
                            SUM(
                                IF(
                                    IFNULL(((IFNULL(g.PanenBiasaMonths,0) * IFNULL(g.PanenBiasaPanenMonth,0) * IFNULL(g.PanenBiasaKg,0)) + (IFNULL(g.PanenTrekMonths,0) * IFNULL(g.PanenTrekPanenMonth,0) * IFNULL(g.PanenTrekKg,0)) + (IFNULL(g.PanenRayaMonths,0) * IFNULL(g.PanenRayaPanenMonth,0) * IFNULL(g.PanenRayaKg,0))),0) > IFNULL(g.Production,0),
                                    IFNULL(((IFNULL(g.PanenBiasaMonths,0) * IFNULL(g.PanenBiasaPanenMonth,0) * IFNULL(g.PanenBiasaKg,0)) + (IFNULL(g.PanenTrekMonths,0) * IFNULL(g.PanenTrekPanenMonth,0) * IFNULL(g.PanenTrekKg,0)) + (IFNULL(g.PanenRayaMonths,0) * IFNULL(g.PanenRayaPanenMonth,0) * IFNULL(g.PanenRayaKg,0))),0),
                                    IFNULL(g.Production,0)
                                )
                            ) Production
                        FROM
                            (
                            SELECT g.FarmerID, f.IsCertified, g.GardenNr, MAX(g.SurveyNr) SurveyNr
                            FROM ktv_cocoa_farmer_garden g
                                    LEFT JOIN ktv_cocoa_farmer f ON g.FarmerID=f.FarmerID
                                    LEFT JOIN ktv_cpg c ON c.CPGid = f.CPGid
                                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
                            WHERE f.StatusCode='active' AND cp.PartnerID=8  AND GardenNr!=0
                            GROUP BY g.FarmerID, g.GardenNr
                            ) dt 
                            LEFT JOIN ktv_cocoa_farmer_garden g ON g.FarmerID=dt.FarmerID AND g.GardenNr=dt.GardenNr AND g.SurveyNr=dt.SurveyNr
                        GROUP BY dt.FarmerID
                    ) g ON g.FarmerID=f.FarmerID*/
                WHERE f.StatusCode = 'active' AND cp.PartnerID = 8 GROUP BY $group_by HAVING label IS NOT NULL";
        
        
        
        $sql3 = "SELECT
                    $label_name label,
                    SUM(g.Production) total,
                    SUM(g.PohonTM) tm
                FROM ktv_cocoa_farmer f
                    LEFT JOIN ktv_cpg c ON c.CPGid = f.CPGid
                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
                    LEFT JOIN rpt_traceability r ON r.farmer_id=f.FarmerID $wh_orgid $w1 AND r.wh_orgid=? $w2 $c1 AND (r.1_supplychainid=? OR r.2_supplychainid=?) $c2 $b1 AND r.1_supplychainid=? $b2 AND IFNULL(r.1_date,r.2_date) BETWEEN ? AND ?
                    LEFT JOIN (
                        SELECT dt.FarmerID, dt.IsCertified,
                            SUM(
                                IF(
                                    IFNULL(((IFNULL(g.PanenBiasaMonths,0) * IFNULL(g.PanenBiasaPanenMonth,0) * IFNULL(g.PanenBiasaKg,0)) + (IFNULL(g.PanenTrekMonths,0) * IFNULL(g.PanenTrekPanenMonth,0) * IFNULL(g.PanenTrekKg,0)) + (IFNULL(g.PanenRayaMonths,0) * IFNULL(g.PanenRayaPanenMonth,0) * IFNULL(g.PanenRayaKg,0))),0) > IFNULL(g.Production,0),
                                    IFNULL(((IFNULL(g.PanenBiasaMonths,0) * IFNULL(g.PanenBiasaPanenMonth,0) * IFNULL(g.PanenBiasaKg,0)) + (IFNULL(g.PanenTrekMonths,0) * IFNULL(g.PanenTrekPanenMonth,0) * IFNULL(g.PanenTrekKg,0)) + (IFNULL(g.PanenRayaMonths,0) * IFNULL(g.PanenRayaPanenMonth,0) * IFNULL(g.PanenRayaKg,0))),0),
                                    IFNULL(g.Production,0)
                                )
                            ) Production, SUM(IFNULL(g.PohonTM,0)) PohonTM
                        FROM
                            (
                            SELECT g.FarmerID, f.IsCertified, g.GardenNr, MAX(g.SurveyNr) SurveyNr
                            FROM ktv_cocoa_farmer_garden g
                                    LEFT JOIN ktv_cocoa_farmer f ON g.FarmerID=f.FarmerID
                                    LEFT JOIN ktv_cpg c ON c.CPGid = f.CPGid
                                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
                            WHERE f.StatusCode='active' AND cp.PartnerID=8  AND GardenNr!=0
                            GROUP BY g.FarmerID, g.GardenNr
                            ) dt 
                            LEFT JOIN ktv_cocoa_farmer_garden g ON g.FarmerID=dt.FarmerID AND g.GardenNr=dt.GardenNr AND g.SurveyNr=dt.SurveyNr
                        GROUP BY dt.FarmerID
                    ) g ON g.FarmerID=f.FarmerID
                WHERE f.StatusCode = 'active' AND cp.PartnerID = 8 GROUP BY $group_by HAVING label IS NOT NULL";
        $cc1 = $ch=='' || $ch=='false' ? '/*' : '';
        $cc2 = $ch=='' || $ch=='false' ? '*/' : '';
        $sql3 = "SELECT 
                    IF(i.IMSID!=141, 'UTZ Certification', 'Cocoalife') label,
                    SUM(ccf.SalesQuota) total
                FROM
                    ktv_ims i
                    LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = i.CertHolderID
                    LEFT JOIN ktv_first_buyer fb ON fb.FirstBuyerID = i.FirstBuyerID
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID = ch.SupplychainID
                    LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.IMSID=i.IMSID
                WHERE
                    fb.FirstBuyerPartnerID = 8 $cc1 AND ch.SupplychainID=? $cc2
                    AND ( i.CertificationStart BETWEEN ? AND ? OR i.CertificationEnd BETWEEN ? AND ? ) 
                    AND i.CertEventStatus = '2'
                GROUP BY label";
        
        
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        //$query_traceability_production   = $this->db->query(sprintf($this->traceability_production, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $where_partner = " AND wh.PartnerID = 8";
        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        
        
            
        
        
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT($date,'%Y%m')='{$value['yearmonth']}',$berat,0)) AS sell_{$value['yearmonth']}
                            ,COUNT( IF(DATE_FORMAT($date,'%Y%m')='{$value['yearmonth']}',FarmerID,NULL)) AS trans_{$value['yearmonth']}";
            }
        }
        $this->traceability_total_cargill = "
            SELECT 
                $label_name label,
                SUM($berat) total_penjualan,
                COUNT(DISTINCT IFNULL(1_transid,2_transid)) total_transaction,
                COUNT(DISTINCT r.farmer_id) total_farmer_sell,
                SUM($berat) total_sell,
                PERIOD_DIFF(DATE_FORMAT(max($date),'%Y%m'),DATE_FORMAT(min($date),'%Y%m')) + 1 bulan,
                min(date($date)) date_min,
                max(date($date)) date_max
                $SELECT
            FROM	
                rpt_traceability r
                LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=r.farmer_id
                LEFT JOIN ktv_cpg c ON c.CPGid = f.CPGid
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
                LEFT JOIN ktv_province p ON p.ProvinceID=SUBSTR(r.farmer_id,1,2)
            WHERE cp.PartnerID=8 $w1 AND r.wh_orgid=? $w2 $c1 AND (r.1_supplychainid=? OR r.2_supplychainid=?) $c2 $b1 AND r.1_supplychainid=? $b2 AND $date BETWEEN ? AND ? $wh_orgid
            GROUP BY $group_by";
        
        $this->traceability_total_cargill = "
            SELECT 
                    $label_name label,
                    SUM($berat) total_penjualan,
                    COUNT(FarmerID) total_transaction,
                    COUNT(DISTINCT FarmerID) total_farmer_sell,
                    SUM($berat) total_sell,
                    PERIOD_DIFF(DATE_FORMAT(max(date_1),'%Y%m'),DATE_FORMAT(min(date_1),'%Y%m')) + 1 bulan,
                    min(date(date_1)) date_min,
                    max(date(date_1)) date_max
                    $SELECT
                FROM 
                ($dt) dt
                WHERE 1=1 GROUP BY $group_by";
        
        $this->traceability_sales_certified_cargill = "
            SELECT 
                    $label_name label,
                    COUNT(DISTINCT FarmerID) farmer,
                    COUNT(DISTINCT IF(SupplyType='utz',FarmerID,NULL)) farmer_certified,
                    COUNT(DISTINCT IF(SupplyType='cocoalife',FarmerID,NULL)) farmer_uncertified,
                    SUM($berat) netto,
                    SUM( IF(SupplyType='utz' && $pending IS NOT NULL,$berat,0)) netto_certified,
                    SUM( IF(SupplyType='cocoalife'  && $pending IS NOT NULL,$berat,0)) netto_uncertified,
                    SUM( IF(SupplyType='utz' && $pending IS NULL,$berat,0)) netto_certified_pending,
                    SUM( IF(SupplyType='cocoalife'  && $pending IS NULL,$berat,0)) netto_uncertified_pending
                FROM ($dt) dt
                WHERE 1=1 $wherew GROUP BY $group_by";
        
        $this->traceability_bu = "
            SELECT 
                $label_name label,
                COUNT(DISTINCT supplyorgid_1) total
            FROM	
                ($dt) dt
            WHERE 1=1 $wherew
            GROUP BY $group_by";
        
        $this->traceability_koperasi = "
            SELECT 
                $label_name label,
                COUNT(DISTINCT supplyorgid_2) total
            FROM	
                ($dt) dt
            WHERE 1=1 $wherew
            GROUP BY $group_by";
        
        $this->traceability_wh = "
            SELECT 
                $label_name label,
                COUNT(DISTINCT name_3) total
            FROM	
                ($dt) dt
            WHERE 1=1 $wherew
            GROUP BY $group_by";
        
        $this->traceability_cl_farmer = "
            SELECT 
                $label_name label,
                COUNT(DISTINCT FarmerID) total
            FROM	
                ($dt) dt
            WHERE 1=1 $wherew AND SupplyType='cocoalife'
            GROUP BY $group_by";
        
        $this->traceability_cl_sales = "
            SELECT 
                $label_name label,
                ROUND(SUM((($berat/1000))),2) total
            FROM	
                ($dt) dt
            WHERE 1=1 $wherew AND SupplyType='cocoalife'
            GROUP BY $group_by";
        

        $query_total        = $this->db->query($this->traceability_total_cargill, array("$awal 00:00:00", "$akhir 23:59:59"));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $query_certified    = $this->db->query($this->traceability_sales_certified_cargill, array("$awal 00:00:00", "$akhir 23:59:59"));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $query_trader       = $this->db->query($this->traceability_bu, array("$awal 00:00:00", "$akhir 23:59:59"));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $debug['trader']    = $this->db->last_query();
        $query_koperasi     = $this->db->query($this->traceability_cl_sales, array("$awal 00:00:00", "$akhir 23:59:59"));
        $debug['koperasi']  = $this->db->last_query();
        $query_warehouse    = $this->db->query($this->traceability_cl_farmer, array("$awal 00:00:00", "$akhir 23:59:59"));
        
        $debug['warehouse'] = $this->db->last_query();
        
        
        $rfarmer = $query_traceability_farmer->result_array();
        $rfarmer[0]['farmer_selling'] = $tc_farmer[0]['farmer_selling'];
        $rfarmer[0]['farmer_certified_selling'] = $tc_farmer[0]['farmer_certified_selling'];
        $rfarmer[0]['farmer_uncertified_selling'] = $tc_farmer[0]['farmer_uncertified_selling'];
        $rfarmer[0]['farmer_transaction'] = $tc_farmer[0]['farmer_transaction'];
        $rfarmer[0]['buying_unit'] = $tc_farmer[0]['buying_unit'];
        
        $results['traceability_farmer']         = $rfarmer;
        
        $query_produksi = $this->db->query($sql3, array($ch=='319' ? 404 : $ch, $awal, $akhir, $awal, $akhir));
        
        $results['produksi']        = $query_produksi->result_array();
        $produksi_utz = 0;
        $produksi_cocoalife = 0;
        foreach($results['produksi'] as $k=>$v){
            if($v['label']=='Cocoalife'){
                $produksi_cocoalife = $produksi_cocoalife + $v['total'];
            }else{
                $produksi_utz = $produksi_utz + $v['total'];
            }
        }
        $sql2 = "SELECT 
                    $label label,
                    SUM(SalesQuota) production,
                    $produksi_utz production_certified,
                    $produksi_cocoalife production_uncertified,
                    SUM(netto) farmer_selling,
                    SUM(certified_netto) farmer_certified_selling,
                    SUM(uncertified_netto) farmer_uncertified_selling 
                FROM 
                    (SELECT 
                        dt.FarmerID,
                        SUM($berat) netto,
                        SUM(IF(dt.SupplyType='utz',$berat, 0)) certified_netto,
                        SUM(IF(dt.SupplyType='cocoalife',$berat, 0)) uncertified_netto,
                        ccf.SalesQuota
                    FROM  ($dt) dt
                        LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.FarmerID=dt.FarmerID
                    WHERE 1=1 $wherew
                    GROUP BY dt.FarmerID
                    ) dt";
        $query_traceability_production   = $this->db->query($sql2, array("$awal 00:00:00", "$akhir 23:59:59"));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['traceability_production']     = $query_traceability_production->result_array();

        $results['total']       = $query_total->result_array();
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['certified']   = $query_certified->result_array();

        $results['trader']          = $query_trader->result_array();
        $results['koperasi']        = $query_koperasi->result_array();
        $results['warehouse']       = $query_warehouse->result_array();
        
        

        $results['months']          = $query_months->result_array();
        //$results['debug']           = $debug;
        return $results;
    }
    
    
}

?>
