<?php
class Mreport_traceability_new extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function ReadRPT($wh, $ch, $bs){
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
            $cch1 = "AND (IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3)=5 AND IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife' $p "; $cch2 = "";
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
                        IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1) SupplyType,
                        
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        batchid_1) batchid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        transid_1) transid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        date_1) date_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        supplyorgid_1) supplyorgid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        faktur_1) faktur_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        bruto_1) bruto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        netto_1) netto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        orgtype_1) orgtype_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        name_1) name_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        batchnumber_1) batchnumber_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        destpo_1) destpo_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        status_1) status_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),NULL,
                        deliverydate_1) deliverydate_1,
                        
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),batchid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, batchid_2)) batchid_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),transid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, transid_2)) transid_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),date_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, date_2)) date_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),supplyorgid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, supplyorgid_2)) supplyorgid_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),faktur_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, faktur_2)) faktur_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),bruto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, bruto_2)) bruto_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),netto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, netto_farmer_2)) netto_farmer_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),bruto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, bruto_farmer_2)) bruto_farmer_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),netto_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, netto_2)) netto_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),orgtype_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, orgtype_2)) orgtype_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),name_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, name_2)) name_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),batchnumber_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, batchnumber_2)) batchnumber_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),destpo_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, destpo_2)) destpo_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),status_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, status_2)) status_2,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),deliverydate_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, deliverydate_2)) deliverydate_2,

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
                        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),batchid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, batchid_2))
                        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,
                        batchid_1)
                        LEFT JOIN ktv_certification_holders ch1 ON ch1.SupplychainID=supplyorgid_1 AND ch1.StatusCode='active'
                        LEFT JOIN ktv_certification_holders ch2 ON ch2.SupplychainID=supplyorgid_2 AND ch2.StatusCode='active'
                    WHERE partnerid_1=".$_SESSION['PartnerID']." AND IFNULL(sb2.SupplyBatchDate, sb1.SupplyBatchDate) BETWEEN ? AND ?
                        $w1
                            AND (IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3)=$wh  
                            $c1 OR IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3) IS NULL $c2
                            $c3)
                        $w2
                        $cch1
                        $c1
                            AND (IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' || ch1.SupplychainID IS NOT NULL),supplyorgid_1,
                        IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, supplyorgid_2))=$ch
                            $b1 OR IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),supplyorgid_1,
                            IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='cocoalife', NULL, supplyorgid_2)) IS NULL $b2
                            $b3)
                        $c2
                        $cch2
                        $b1
                            AND IF(IF(transcert_1 IS NULL || transcert_1='', IF(supplytype_1='Farmer','utz','cocoalife'), transcert_1)='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,
                            supplyorgid_1)=$bs
                        $b2
                    ";
        return $dt;
    }
    
    public function ReadComboPartner($PartnerID='')
    {
        if($PartnerID=='' || $PartnerID=='1'){
            $p1 = "/*"; $p2 = "*/";
        }else{
            $p1 = ""; $p2 = "";
        }
        $sql = "SELECT DISTINCT b.PartnerID id, b.PartnerName label
                FROM ktv_warehouse a
                LEFT JOIN ktv_program_partner b ON a.PartnerID=b.PartnerID
                WHERE b.PartnerID IS NOT NULL AND a.StatusCode='active' AND a.PartnerID NOT IN (9) $p1 AND a.PartnerID=? $p2
                ORDER BY label";
        $query = $this->db->query($sql, array($PartnerID));
        $result['data'] = $query->result_array();
        return $result;
    }
    
    public function ReadComboMill($PartnerID)
    {
        $sql = "SELECT vso.SupplychainID id, vso.`Name` label
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_access_partner_mill apm ON apm.apmiMillID=vso.OrgID
                WHERE
                    vso.OrgType='mill' AND apm.apmiPartnerID=? 
                ORDER BY label";
        $query = $this->db->query($sql, array($PartnerID));
        $result['data'] = $query->result_array();
        return $result;
    }
    
    public function ReadComboDO($start, $end, $partnerid='')
    {
        $sql = "SELECT
                    vso.SupplychainID id,
                    vso.`Name` label 
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_org_partner op ON op.SupplychainID = vso.SupplychainID
                    LEFT JOIN view_do vd ON vd.MemberID=vso.OrgID
                WHERE
                    vso.OrgType = 'agent' AND vd.MemberID IS NOT NULL 
                    AND op.PartnerID =?
                ORDER BY label";
        $query = $this->db->query($sql, array($partnerid));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function ReadComboAgent($start, $end, $partnerid='')
    {
        $sql = "SELECT
                    vso.SupplychainID id,
                    vso.`Name` label 
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_org_partner op ON op.SupplychainID = vso.SupplychainID
                    LEFT JOIN view_do vd ON vd.MemberID=vso.OrgID
                WHERE
                    vso.OrgType = 'agent' AND vd.MemberID IS NULL 
                    AND op.PartnerID =?
                ORDER BY label";
        $query = $this->db->query($sql, array($partnerid));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    
    public function ReadComboBS($ch, $start, $end, $partnerid)
    {
        $sql = "SELECT DISTINCT vso.SupplychainID id, vso.`Name` label 
                FROM
                    ktv_ims i
                    LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=i.CertHolderID
                    LEFT JOIN ktv_first_buyer fb ON fb.FirstBuyerID=i.FirstBuyerID
                    LEFT JOIN ktv_ims_buying_unit ibu ON ibu.IMSID=i.IMSID
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=ibu.SupplychainID
                WHERE
                    vso.SupplychainID IS NOT NULL AND fb.FirstBuyerPartnerID=? AND ch.SupplychainID=?
                    AND !(? > i.CertificationEnd OR ? < i.CertificationStart)
                    /*AND i.CertEventStatus='2'*/
                ORDER BY label";
        $query = $this->db->query($sql, array($partnerid, $ch, $start, $end));
        
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    
    function ReadGridMill($PartnerID, $awal, $akhir, $mill, $do, $agent, $start = 0, $limit = 50){
        $m1 = $mill=='' ? '/*' : ''; $m2 = $mill=='' ? '*/' : '';
        $d1 = $do=='' ? '/*' : ''; $d2 = $do=='' ? '*/' : '';
        $a1 = $agent=='' ? '/*' : ''; $a2 = $agent=='' ? '*/' : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    d.District, 
                    sd.SubDistrict,
                    IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL))) MillID,
                    IF(vso1.OrgType='mill', vso1.Name, IF(vso2.OrgType='mill', vso2.Name, IF(vso3.OrgType='mill', vso3.Name, NULL))) MillName,
                    IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL))) MillBatchID,
                    SUBSTR(IF(vso1.OrgType='mill', st1.DateTransaction, IF(vso2.OrgType='mill', st2.DateTransaction, IF(vso3.OrgType='mill', st3.DateTransaction, NULL))),1,10) DateTransaction,
                    IF(vso1.OrgType='mill', st1.VolumeNetto, IF(vso2.OrgType='mill', st2.VolumeNetto, IF(vso3.OrgType='mill', st3.VolumeNetto, NULL))) VolumeNetto, 
                    IF(
                        vso1.OrgType='mill',
                        IFNULL(IF(st1.Bjr>0, ROUND(st1.VolumeBruto1 / st1.Bjr), IF(st1.NumberPackage > 0, st1.NumberPackage, NULL)), NULL),
                        IF(
                            vso2.OrgType='mill',
                            IFNULL(IF(st2.Bjr>0, ROUND(st2.VolumeBruto1 / st2.Bjr), IF(st2.NumberPackage > 0, st2.NumberPackage, NULL)), NULL),
                            IF(
                                vso3.OrgType='mill',
                                IFNULL(IF(st3.Bjr>0, ROUND(st3.VolumeBruto1 / st3.Bjr), IF(st3.NumberPackage > 0, st3.NumberPackage, NULL)), NULL),
                                NULL
                            )
                        )
                    ) FFB,
                    IF(vso1.OrgType='mill', NULL, IF(vso2.OrgType='mill', vso1.Name, IF(vso3.OrgType='mill', vso2.Name, NULL))) BatchFrom
                FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    LEFT JOIN ktv_village v ON v.VillageID=IF(vso1.OrgType='mill', vso1.VillageID, IF(vso2.OrgType='mill', vso2.VillageID, IF(vso3.OrgType='mill', vso3.VillageID, NULL)))
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=?
                    AND st1.DateTransaction BETWEEN ? AND ?
                    AND IF(vso1.OrgType='mill', vso1.OrgType, IF(vso2.OrgType='mill', vso2.OrgType, IF(vso3.OrgType='mill', vso3.OrgType, NULL)))='mill'
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2
                GROUP BY IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL)))
                ORDER BY DateTransaction DESC
                LIMIT ?,?";
        $query = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT 
                    FORMAT(SUM(tot.VolumeNetto),2) TotalNetto
                FROM
                    (
                        SELECT 
                            IF(vso1.OrgType='mill', st1.VolumeNetto, IF(vso2.OrgType='mill', st2.VolumeNetto, IF(vso3.OrgType='mill', st3.VolumeNetto, NULL))) VolumeNetto
                        FROM
                            ktv_supplychain_transaction st1
                            LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                            LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                            LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                            LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                            LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                            LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                            LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                            LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                            LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                            LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                            LEFT JOIN ktv_village v ON v.VillageID=IF(vso1.OrgType='mill', vso1.VillageID, IF(vso2.OrgType='mill', vso2.VillageID, IF(vso3.OrgType='mill', vso3.VillageID, NULL)))
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        WHERE 
                            st1.SupplyType!='Batch' AND pm.apmiPartnerID=?
                            AND st1.DateTransaction BETWEEN ? AND ?
                            AND IF(vso1.OrgType='mill', vso1.OrgType, IF(vso2.OrgType='mill', vso2.OrgType, IF(vso3.OrgType='mill', vso3.OrgType, NULL)))='mill'
                            $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                            $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                            $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2
                        GROUP BY IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL)))
                    ) tot";
        $total_all = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalNetto' => $total_all->row()->TotalNetto
                );
        }else{
            return false;
        }
    }

    function ReadGridDO($PartnerID, $awal, $akhir, $mill, $do, $agent, $start = 0, $limit = 50){
        $m1 = $mill=='' ? '/*' : ''; $m2 = $mill=='' ? '*/' : '';
        $d1 = $do=='' ? '/*' : ''; $d2 = $do=='' ? '*/' : '';
        $a1 = $agent=='' ? '/*' : ''; $a2 = $agent=='' ? '*/' : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    dt.District,
                    dt.SubDistrict,
                    dt.DOID,
                    dt.DOName,
                    dt.DOBatchID,
                    SUBSTR(dt.DOBatchDate,1,10) DOBatchDate,
                    SUM(st.VolumeNetto) VolumeNetto,
                    IFNULL(SUM(IF(st.Bjr>0, ROUND(st.VolumeBruto1 / st.Bjr), IF(st.NumberPackage > 0, st.NumberPackage, NULL))), '-') FFB,
                    dt.MillID,
                    dt.MillName,
                    dt.MillBatchID
                FROM
                (
                    SELECT
                    d.District, 
                    sd.SubDistrict, 
                    -- DO --
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID)) DOID,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.`Name`, vso2.`Name`)) DOName,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, sb1.SupplyBatchID, sb2.SupplyBatchID)) DOBatchID,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, sb1.SupplyBatchDate, sb2.SupplyBatchDate)) DOBatchDate,
                    -- Mill -- 
                    IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL))) MillID,
                    IF(vso1.OrgType='mill', vso1.`Name`, IF(vso2.OrgType='mill', vso2.`Name`, IF(vso3.OrgType='mill', vso3.`Name`, NULL))) MillName,
                    IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL))) MillBatchID
                    
                FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    -- DO Check --
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    -- Village --
                    LEFT JOIN ktv_village v ON v.VillageID=IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.VillageID, vso2.VillageID))
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=?
                    AND st1.DateTransaction BETWEEN ? AND ?
                    AND IF(vso1.OrgType='mill', vso1.OrgType, IF(vso2.OrgType='mill', vso2.OrgType, IF(vso3.OrgType='mill', vso3.OrgType, NULL)))='mill' 
                    -- DO --
                    AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID)) IS NOT NULL
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2
                GROUP BY IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, st1.SupplyBatchID, st1.SupplyBatchID))
                ) dt
                LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=dt.DOBatchID
                GROUP BY dt.DOBatchID LIMIT ?,?";
        $query = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    FORMAT(SUM(st.VolumeNetto),2) TotalNetto
                FROM
                (
                    SELECT
                    d.District, 
                    sd.SubDistrict, 
                    -- DO --
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID)) DOID,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.`Name`, vso2.`Name`)) DOName,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, sb1.SupplyBatchID, sb2.SupplyBatchID)) DOBatchID,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, sb1.SupplyBatchDate, sb2.SupplyBatchDate)) DOBatchDate,
                    -- Mill -- 
                    IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL))) MillID,
                    IF(vso1.OrgType='mill', vso1.`Name`, IF(vso2.OrgType='mill', vso2.`Name`, IF(vso3.OrgType='mill', vso3.`Name`, NULL))) MillName,
                    IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL))) MillBatchID
                    
                FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    -- DO Check --
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    -- Village --
                    LEFT JOIN ktv_village v ON v.VillageID=IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.VillageID, vso2.VillageID))
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=?
                    AND st1.DateTransaction BETWEEN ? AND ?
                    AND IF(vso1.OrgType='mill', vso1.OrgType, IF(vso2.OrgType='mill', vso2.OrgType, IF(vso3.OrgType='mill', vso3.OrgType, NULL)))='mill' 
                    -- DO --
                    AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID)) IS NOT NULL
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2
                GROUP BY IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, st1.SupplyBatchID, st1.SupplyBatchID))
                ) dt
                LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=dt.DOBatchID";
        $total_all = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalNetto' => $total_all->row()->TotalNetto
                );
        }else{
            return false;
        }
    }

    function ReadGridAgent($PartnerID, $awal, $akhir, $mill, $do, $agent, $start = 0, $limit = 50){
        $m1 = $mill=='' ? '/*' : ''; $m2 = $mill=='' ? '*/' : '';
        $d1 = $do=='' ? '/*' : ''; $d2 = $do=='' ? '*/' : '';
        $a1 = $agent=='' ? '/*' : ''; $a2 = $agent=='' ? '*/' : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    d.District, 
                    sd.SubDistrict, 
                    -- Agent --
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', vso1.OrgID) AgentID,
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', vso1.`Name`) AgentName,
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', sb1.SupplyBatchID) AgentBatchID,
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', SUBSTR(sb1.SupplyBatchDate,1,10)) AgentBatchDate,
                    SUM(st1.VolumeNetto) VolumeNetto, 
                    IFNULL(SUM(IF(st1.Bjr>0, ROUND(st1.VolumeBruto1 / st1.Bjr), IF(st1.NumberPackage > 0, st1.NumberPackage, NULL))), '-') FFB,
                    
                    -- DO --
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID)) DOID,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.`Name`, vso2.`Name`)) DOName,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, sb1.SupplyBatchID, sb2.SupplyBatchID)) DOBatchID,
                    -- Mill -- 
                IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL))) MillID,
                    IF(vso1.OrgType='mill', vso1.`Name`, IF(vso2.OrgType='mill', vso2.`Name`, IF(vso3.OrgType='mill', vso3.`Name`, NULL))) MillName,
                    IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL))) MillBatchID
                    
                FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    -- DO Check --
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    -- Village --
                    LEFT JOIN ktv_village v ON v.VillageID=IF(do1.MemberID IS NOT NULL, NULL, vso1.VillageID)
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=? 
                    AND st1.DateTransaction BETWEEN ? AND ?
                    -- Agent --
                    AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID) IS NOT NULL
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2
                GROUP BY sb1.SupplyBatchID LIMIT ?,?";
        $query = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    FORMAT(SUM(st1.VolumeNetto),2) TotalNetto
                FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    -- DO Check --
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    -- Village --
                    LEFT JOIN ktv_village v ON v.VillageID=IF(do1.MemberID IS NOT NULL, NULL, vso1.VillageID)
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=? 
                    AND st1.DateTransaction BETWEEN ? AND ?
                    -- Agent --
                    AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID) IS NOT NULL
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2";
        $total_all = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalNetto' => $total_all->row()->TotalNetto
                );
        }else{
            return false;
        }
    }

    function ReadGridFarmer($PartnerID, $awal, $akhir, $mill, $do, $agent, $start = 0, $limit = 50){
        $m1 = $mill=='' ? '/*' : ''; $m2 = $mill=='' ? '*/' : '';
        $d1 = $do=='' ? '/*' : ''; $d2 = $do=='' ? '*/' : '';
        $a1 = $agent=='' ? '/*' : ''; $a2 = $agent=='' ? '*/' : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    d.District, 
                    sd.SubDistrict, 
                    m.MemberDisplayID, 
                    m.MemberName, 
                    st1.PlotNr,
                    SUBSTR(st1.DateTransaction, 1,10) DateTransaction, 
                    st1.VolumeNetto, 
                    IF(st1.Bjr>0, ROUND(st1.VolumeBruto1 / st1.Bjr), IF(st1.NumberPackage > 0, st1.NumberPackage, '-')) FFB,
                    -- Agent --
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', vso1.OrgID) AgentID,
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', vso1.`Name`) AgentName,
                    IF(do1.MemberID IS NOT NULL, 'Direct to DO', sb1.SupplyBatchID) AgentBatchID,
                    -- DO --
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID)) DOID,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, vso1.`Name`, vso2.`Name`)) DOName,
                    IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, 'Direct to Mill', IF(do1.MemberID IS NOT NULL, sb1.SupplyBatchID, sb2.SupplyBatchID)) DOBatchID,
                    -- Mill -- 
                    IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL))) MillID,
                    IF(vso1.OrgType='mill', vso1.`Name`, IF(vso2.OrgType='mill', vso2.`Name`, IF(vso3.OrgType='mill', vso3.`Name`, NULL))) MillName,
                    IF(vso1.OrgType='mill', st1.SupplyTransID, IF(vso2.OrgType='mill', st2.SupplyTransID, IF(vso3.OrgType='mill', st3.SupplyTransID, NULL))) MillBatchID
                    
                FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    -- Farmer --
                    LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
                    LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=?
                    AND st1.DateTransaction BETWEEN ? AND ?
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2
                ORDER BY sb1.SupplyBatchID, st1.DateTransaction LIMIT ?,?";
        $query = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    FORMAT(SUM(st1.VolumeNetto),2) TotalNetto
                    FROM
                    ktv_supplychain_transaction st1
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
                    LEFT JOIN view_do do1 ON do1.MemberID=vso1.OrgID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
                    LEFT JOIN view_do do2 ON do2.MemberID=vso2.OrgID
                    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
                    
                    LEFT JOIN ktv_access_partner_mill pm ON pm.apmiMillID=IF(vso1.OrgType='mill', vso1.OrgID, IF(vso2.OrgType='mill', vso2.OrgID, IF(vso3.OrgType='mill', vso3.OrgID, NULL)))
                    -- Farmer --
                    LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
                    LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE 
                    st1.SupplyType!='Batch' AND pm.apmiPartnerID=?
                    AND st1.DateTransaction BETWEEN ? AND ?
                    $m1 AND IF(vso1.OrgType='mill', vso1.SupplychainID, IF(vso2.OrgType='mill', vso2.SupplychainID, IF(vso3.OrgType='mill', vso3.SupplychainID, NULL)))=? $m2
                    $d1 AND IF(vso2.OrgType='mill' && do1.MemberID IS  NULL, NULL, IF(do1.MemberID IS NOT NULL, vso1.OrgID, vso2.OrgID))=? $d2
                    $a1 AND IF(do1.MemberID IS NOT NULL, NULL, vso1.SupplychainID)=? $a2";
        $total_all = $this->db->query($sql, array($PartnerID, $awal." 00:00:00", $akhir." 23:59:59", $mill, $do, $agent));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalNetto' => $total_all->row()->TotalNetto
                );
        }else{
            return false;
        }
    }
    
    function ReadStoreTransactionCH($awal, $akhir, $wh, $ch, $bs, $cert, $SupplyBatchID, $start = 0, $limit = 50, $BatchIDWH=''){
        $bwh1 = $BatchIDWH=='' ? '/*' : '';
        $bwh2 = $BatchIDWH=='' ? '*/' : '';
        if($cert!='' && $cert!='false'){        
            $cert1 = ''; $cert2 = '';
        }else{ 
            $cert1 = '/*';  $cert2 = '*/'; 
        }
        $dt = $this->ReadRPT($wh, $ch, $bs);
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    dt.SupplyType, dt.name_2 Name, 
                    sb.SupplyBatchDate DateTransaction, 
                    dt.destpo_2 DestPO, 
                    /*dt.bruto_2 Bruto, 
                    dt.netto_2 Netto,*/
                    SUM(dt.bruto_farmer_2) Bruto,
                    SUM(dt.netto_farmer_2) Netto,
                    dt.name_3 Destination, 
                    IF(dt.deliverydate_1 IS NOT NULL, deliverydate_1, IF(dt.deliverydate_1 IS NULL && dt.status_2 IS NOT NULL, SUBSTR(dt.date_2, 1,10), NULL)) DeliveryDate,
                    IFNULL(sb.DestWeight, dt.netto_2) DestWeight, sb.SupplyBatchID, 
                    IFNULL(status_2,'Open Batch') SupplyStatus
                FROM ($dt) dt
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=IFNULL(dt.batchid_2, dt.batchid_1)
                WHERE 1=1 $bwh1 AND (dt.batchid_3=$BatchIDWH OR dt.batchid_2=$BatchIDWH) $bwh2
                    $cert1 AND dt.SupplyType=? $cert2
                    AND supplyorgid_2 IS NOT NULL 
                GROUP BY IFNULL(batchid_2, batchid_1)
                ORDER BY DateTransaction DESC
                LIMIT ?,?";
        $query = $this->db->query($sql, array($awal, $akhir, $cert, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT FORMAT(SUM(tot.Bruto),2) TotalBruto, FORMAT(SUM(tot.Netto),2) TotalNetto, FORMAT(SUM(tot.NettoPending),2) TotalNettoPending
                FROM
                    (SELECT
                        IFNULL(dt.bruto_farmer_2,0) Bruto, 
                        IFNULL(dt.netto_farmer_2,0) Netto,
                        IF(supplyorgid_3 IS NULL, IFNULL(dt.netto_farmer_2,0),0) NettoPending
                        
                    FROM ($dt) dt
                        LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=IFNULL(dt.batchid_2, dt.batchid_1)
                    WHERE 1=1 $bwh1 AND (dt.batchid_3=$BatchIDWH OR dt.batchid_2=$BatchIDWH) $bwh2
                        $cert1 AND dt.SupplyType=? $cert2
                        AND supplyorgid_2 IS NOT NULL
                    ) tot";
        $total_all = $this->db->query($sql, array($awal, $akhir, $cert, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalBruto' => $total_all->row()->TotalBruto,
                'TotalNetto' => $total_all->row()->TotalNetto,
                'TotalNettoPending' => $total_all->row()->TotalNettoPending
                );
        }else{
            return false;
        }
    }
    
    function ReadStoreTransactionBS($awal, $akhir, $wh, $ch, $bs, $cert, $SupplyBatchID, $start = 0, $limit = 50, $BatchIDWH, $BatchIDCH){
        if($cert!='' && $cert!='false'){        
            $cert1 = ''; $cert2 = '';
        }else{ 
            $cert1 = '/*';  $cert2 = '*/'; 
        }
        if($BatchIDCH!=''){
            $where = "AND (dt.batchid_1=$BatchIDCH OR dt.batchid_2=$BatchIDCH)";
        }else if ($BatchIDWH!=''){
            $where = "AND (dt.batchid_1=$BatchIDWH OR dt.batchid_2=$BatchIDWH OR dt.batchid_3=$BatchIDWH)";
        }else{
            $where = "";
        }
        $dt = $this->ReadRPT($wh, $ch, $bs);
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    dt.SupplyType, dt.name_1 Name, 
                    sb.SupplyBatchDate DateTransaction, 
                    dt.destpo_1 DestPO, 
                    SUM(dt.bruto_1) Bruto, 
                    SUM(dt.netto_1) Netto, 
                    IFNULL(dt.name_2, dt.name_3) Destination, 
                    sb.DeliveryDate DeliveryDate,
                    sb.DestWeight DestWeight, sb.SupplyBatchID, 
                    IFNULL(status_1,'Open Batch') SupplyStatus
                FROM ($dt) dt
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=dt.batchid_1
                WHERE 1=1 AND dt.supplyorgid_1 IS NOT NULL $where
                    $cert1 AND dt.SupplyType=? $cert2
                GROUP BY batchid_1
                ORDER BY DateTransaction DESC
                LIMIT ?,?";
        $query = $this->db->query($sql, array($awal, $akhir, $cert, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT FORMAT(SUM(tot.Bruto),2) TotalBruto, FORMAT(SUM(tot.Netto),2) TotalNetto
                FROM
                    (SELECT
                        SUM(IFNULL(dt.bruto_1,0)) Bruto, 
                        SUM(IFNULL(dt.netto_1,0)) Netto
                        
                    FROM ($dt) dt
                        LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=dt.batchid_1
                    WHERE 1=1 AND dt.supplyorgid_1 IS NOT NULL $where
                        $cert1 AND dt.SupplyType=? $cert2
                    GROUP BY batchid_1
                    ) tot";
        $total_all = $this->db->query($sql, array($awal, $akhir, $cert, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalBruto' => $total_all->row()->TotalBruto,
                'TotalNetto' => $total_all->row()->TotalNetto
                );
        }else{
            return false;
        }
    }
    
    function ReadStoreTransactionFarmer($awal, $akhir, $wh, $ch, $bs, $cert, $SupplyBatchID, $start = 0, $limit = 50, $BatchIDWH='', $BatchIDCH='', $BatchIDBS=''){
        if($cert!='' && $cert!='false'){        
            $cert1 = ''; $cert2 = '';
        }else{ 
            $cert1 = '/*';  $cert2 = '*/'; 
        }
        if($BatchIDBS!=''){
            $where = "AND (dt.batchid_1=$BatchIDBS)";
        }elseif($BatchIDCH!=''){
            $where = "AND (dt.batchid_1=$BatchIDCH OR dt.batchid_2=$BatchIDCH)";
        }else if ($BatchIDWH!=''){
            $where = "AND (dt.batchid_1=$BatchIDWH OR dt.batchid_2=$BatchIDWH OR dt.batchid_3=$BatchIDWH)";
        }else{
            $where = "";
        }
        $dt = $this->ReadRPT($wh, $ch, $bs);
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    dt.SupplyType,
                    dt.FarmerID,
                    REPLACE(dt.FarmerName, '`', '') FarmerName,
                    dt.CPGId,
                    dt.GroupName,
                    dt.Village,
                    dt.SubDistrict,
                    dt.District,
                    REPLACE(dt.name_1, '`', '') name_1,
                    dt.date_1,
                    dt.batchnumber_1,
                    dt.destpo_1,
                    dt.bruto_1,
                    dt.netto_1,
                    dt.status_1,
                    dt.deliverydate_1,
                    REPLACE(dt.name_2, '`', '') name_2,
                    dt.batchnumber_2,
                    dt.bruto_farmer_2 bruto_2,
                    dt.netto_farmer_2 netto_2,
                    dt.destpo_2,
                    dt.status_2,
                    dt.deliverydate_2,
                    dt.name_3,
                    dt.destpo_3,
                    dt.bruto_farmer_3 bruto_3,
                    dt.netto_farmer_3 netto_3,
                    dt.status_3,
                    dt.date_2, dt.date_3
                FROM ($dt) dt
                WHERE 1=1 $where
                    $cert1 AND dt.SupplyType=? $cert2
                ORDER BY date_1 DESC
                LIMIT ?,?";
        $query = $this->db->query($sql, array($awal, $akhir, $cert, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        
        $sql = "SELECT FORMAT(SUM(tot.Bruto),2) TotalBruto, FORMAT(SUM(tot.Netto),2) TotalNetto
                FROM
                    (SELECT
                        SUM(IFNULL(dt.bruto_1,dt.bruto_2)) Bruto, 
                        SUM(IFNULL(dt.netto_1,dt.netto_2)) Netto
                    FROM ($dt) dt
                    WHERE 1=1 $where
                        $cert1 AND dt.SupplyType=? $cert2
                    ) tot";
        $total_all = $this->db->query($sql, array($awal, $akhir, $cert, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                'TotalBruto' => $total_all->row()->TotalBruto,
                'TotalNetto' => $total_all->row()->TotalNetto
                );
        }else{
            return false;
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    public function readTotalCpgs()
    {
        //tes
        $sql = "SELECT %s
            FROM `ktv_cpg`";
        $query           = $this->db->query(sprintf($sql, 'count(*) as total', ''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readCpgsAll()
    {
        $sql = "select %s
            FROM ktv_cpg a, ktv_province b
            WHERE
            SUBSTR(a.RegionID FROM 1 FOR 2)=b.ProvinceID
            GROUP BY  b.Province";
        $query          = $this->db->query(sprintf($sql, 'b.Province,count(*) total'));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readFarmer($provinsi, $kabupaten, $jenis, $survey, $trainingYear, $certificationType = '', $sort = "", $start = 0, $limit = '', $LatestSurvey, $stat, $sesPartner)
    {
        switch ($jenis) {
            case 'Nutrition Summary':
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND a.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    //$where = "AND kprov.Province = '{$provinsi}' $where_kab ";
                    $where = "WHERE a.Province = '{$provinsi}' $where_kab ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }

                $sql = "SELECT
                            %s
                        FROM(
                            SELECT
                                kprov.Province,
                                kdis.District,
                                ksubdis.SubDistrict,
                                kcf.FarmerID,
                                if(kcbtf.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
                            FROM
                                ktv_cpg_batch_trainings kcbt
                                INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
                                LEFT JOIN ktv_cocoa_farmer kcf ON kcbtf.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_family kf ON kcbtf.FamilyID = kf.FamilyID
                                LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID,1,2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID,1,4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kcf.VillageID,1,7) = ksubdis.SubDistrictID
                            WHERE kcbt.CPGtrainingsID=2
                            UNION ALL
                            SELECT
                                kprov.Province,
                                kdis.District,
                                ksubdis.SubDistrict,
                                kcf.FarmerID,
                                if(kktp.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
                            FROM
                                ktv_kader_trainings kkt
                                INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID= kktp.CpgKaderTrainingID
                                LEFT JOIN ktv_cocoa_farmer kcf ON kktp.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_family kf ON kktp.FamilyID = kf.FamilyID
                                LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID,1,2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID,1,4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kcf.VillageID,1,7) = ksubdis.SubDistrictID
                            WHERE kkt.CPGtrainingsID=2
                        ) a
                        LEFT JOIN (
                            SELECT *
                            FROM (
                                SELECT
                                    kp.Province,
                                    kdis.District,
                                    round(sum(kn.Score)/count(knb.FarmerID),2) total,
                                    sum(kn.Score) sc,
                                    count(knb.FarmerID) fa
                                FROM
                                    ktv_nutrition kn
                                    INNER JOIN (
                    SELECT
                                            FarmerID,
                                            max(SurveyNr) LastSurveyNr
                    FROM
                                            ktv_nutrition
                    GROUP BY FarmerID
                                    ) knb on kn.FarmerID=knb.FarmerID and kn.SurveyNr=knb.LastSurveyNr
                                    LEFT JOIN ktv_cocoa_farmer kcf ON knb.FarmerID = kcf.FarmerID
                                    LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    SUBSTR(kcf.VillageID,1,4) NOT IN (7317,7322,7325) AND
                                    (kn.Score>0 AND kn.Score<10)
                                GROUP BY
                                    kp.Province,
                                    kdis.District
                                ) a
                            WHERE total>0
                        ) b on a.District = b.District
                        LEFT JOIN (
                            SELECT
                                kp.Province,
                                kdis.District,
                                sum(IFNULL(GardenHaUnCertified,0)) as total,
                                count(distinct a.FarmerID) as jumlah,
                                count(a.FarmerID) as kebun,
                                sum(PohonTM) AS tree,
                                SUM(GardenHaUnCertified) ha
                            FROM ktv_cocoa_farmer_garden a
                                INNER JOIN (
                                    SELECT
                    FarmerID,
                    GardenNr,
                    max(SurveyNr) LatestSurveyNr
                                    FROM
                    ktv_cocoa_certification
                                    WHERE
                    ExternalDate>'0000-00-00'
                                    GROUP BY
                    FarmerID,GardenNr
                                ) z on z.FarmerID=a.FarmerID and z.GardenNr=a.GardenNr and z.LatestSurveyNr=a.SurveyNr
                                INNER JOIN (
                                    SELECT
                    FarmerID,
                    GardenNr,
                    max(SurveyNr) LatestSurveyNr
                                    FROM
                    ktv_cocoa_farmer_garden
                                    GROUP BY
                    FarmerID,
                    GardenNr
                                ) y on a.FarmerID = y.FarmerID and a.GardenNr = y.GardenNr
                                LEFT JOIN ktv_cocoa_farmer kcf on a.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                            WHERE
                                kcf.StatusCode='active' AND
                                kcf.VillageID and GardenHaUnCertified>0 AND
                                kcf.VillageID IS NOT NULL
                            GROUP BY kp.Province, kdis.District
                        ) c on a.District = c.District
                        LEFT JOIN ktv_cocoa_farmer kcf ON a.FarmerID = kcf.FarmerID
                        {$join}
                        {$where}
                    GROUP BY
                        a.Province,
                        a.District";
                $query = $this->db->query(sprintf($sql, "a.Province,
                            a.District,
                            COUNT(a.FarmerID) as 'GNP Participants',
                            sum(IF(kelamin=2,1,0)) 'Female Participants',
                            b.total as IDDS,
                            c.total as 'Nutrition Garden Area'"));
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total'));
                $result['total'] = $query2->num_rows();
                break;
            case 'Certification Summary':
                if ($trainingYear > 0) {
                    $where1 = " YEAR(ExternalDate)={$trainingYear} ";
                } else {
                    $where1 = " ExternalDate>'0000-00-00' ";
                }

                if ($certificationType > 0) {
                    $where1 .= " AND Certification={$certificationType} ";
                }

                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    //$where = "AND kprov.Province = '{$provinsi}' $where_kab ";
                    $where = "WHERE kprov.Province = '{$provinsi}' $where_kab ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }

                $sql = "SELECT
                            %s
                        FROM (
                            SELECT *
                            FROM
                                ktv_cocoa_certification
                            WHERE
                                {$where1}
                            GROUP BY FarmerID
                            ) kcc
                            LEFT JOIN ktv_cocoa_farmer kcf on kcc.FarmerID=kcf.FarmerID
                            LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID, 1, 2) = kprov.ProvinceID
                            LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                            INNER JOIN (
                                SELECT
                                    kdis.DistrictID,
                                    COUNT(kcc.GardenNr) as total_cert_farm
                                FROM ktv_cocoa_certification kcc
                                    LEFT JOIN ktv_cocoa_farmer kcf ON kcc.FarmerID=kcf.FarmerID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    {$where1}
                                    AND SUBSTR(kcf.VillageID,1,2) IS NOT NULL
                                GROUP BY
                                    kdis.District
                            ) c ON kdis.DistrictID = c.DistrictID
                            INNER JOIN (
                                SELECT
                                    kdis.DistrictID,
                                    sum(IFNULL(GardenHaUnCertified,0)) as total,
                                    sum(
                                        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0)
                                        )+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0)
                                        )+(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
                                    ) as total_productivity,
                                    round(sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
                                        SUM(IFNULL(GardenHaUnCertified,0)),0) as produktivitas
                                FROM ktv_cocoa_farmer_garden kcfg
                                    LEFT JOIN ktv_cocoa_farmer kcf on kcfg.FarmerID=kcf.FarmerID
                                    INNER JOIN (
                                        SELECT
                                            FarmerID,
                                            GardenNr,
                                            max(SurveyNr) LatestSurveyNr
                                        FROM
                                            ktv_cocoa_certification
                                        WHERE
                                            {$where1}
                                        GROUP BY
                                            FarmerID,
                                            GardenNr
                                    ) z on z.FarmerID=kcfg.FarmerID and z.GardenNr=kcfg.GardenNr and z.LatestSurveyNr=kcfg.SurveyNr
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    SUBSTR(kcf.VillageID,1,2) IS NOT NULL
                                GROUP BY
                                    kdis.DistrictID
                            ) d ON kdis.DistrictID = d.DistrictID
                            INNER JOIN (
                                SELECT
                                    kdis.DistrictID,
                                    (SUM(PohonTM)/SUM(GardenHaUnCertified)) as avg_trees,
                                    SUM(PohonTM) AS trees,
                                    SUM(GardenHaUnCertified) ha
                                FROM ktv_cocoa_farmer_garden kcfg
                                    INNER JOIN (
                                        SELECT
                                            FarmerID,
                                            GardenNr,
                                            max(SurveyNr) LatestSurveyNr
                                        FROM
                                            ktv_cocoa_certification
                                        WHERE
                                            {$where1}
                                        GROUP BY
                                            FarmerID,GardenNr
                                    ) z on z.FarmerID=kcfg.FarmerID and z.GardenNr=kcfg.GardenNr and z.LatestSurveyNr=kcfg.SurveyNr
                                    INNER JOIN (
                                        SELECT
                                            FarmerID,
                                            GardenNr,
                                            max(SurveyNr) LatestSurveyNr
                                        FROM
                                            ktv_cocoa_farmer_garden
                                        GROUP BY
                                            FarmerID,
                                            GardenNr
                                    ) y on kcfg.FarmerID = y.FarmerID and kcfg.GardenNr = y.GardenNr
                                    LEFT JOIN ktv_cocoa_farmer kcf on kcfg.FarmerID = kcf.FarmerID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    kcf.StatusCode='active' AND
                                    kcf.VillageID and GardenHaUnCertified>0 AND
                                    kcf.VillageID IS NOT NULL
                                GROUP BY
                                    kdis.DistrictID
                            ) e ON e.DistrictID = kdis.DistrictID
                            {$join}
                            {$where}
                            GROUP BY
                                kprov.Province,
                                kdis.District";

                $query = $this->db->query(sprintf($sql, "kprov.Province,
                            kdis.District,
                            count(kcc.FarmerID) as 'Total Cert Farmer',
                            c.total_cert_farm as 'Total Cert Farm',
                            sum(IF(kcf.Gender=1,1,0)) Male,
                            sum(IF(kcf.Gender=2,1,0)) Female,
                            d.total as 'Total Cert Farm Area',
                            d.total_productivity as 'Total Cert Productivity',
                            d.produktivitas as 'Average Productivity of Certified Farm',
                            (e.trees/e.ha) as 'Average Trees per Ha'"));
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total'));
                $result['total'] = $query2->num_rows();
                break;
            case 'Farmer Detail Data':
                if ($kabupaten != ' -- All --') {
                    //$kab = ($stat == 'preview')?explode(',', $kabupaten):$kabupaten;
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kd.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = " AND Province = '{$provinsi}' $where_kab ";
                }

                //if ($survey!='') $where .= " AND kcfg.SurveyNr = $survey ";
                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }

                $whereLatestSurvey = "";
                if ($LatestSurvey === 'true') {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_cocoa_farmer_garden
                                            GROUP BY FarmerID
                                        ) z ON kcf.FarmerID = z.FarmerID
                                        INNER JOIN ktv_cocoa_farmer_garden kcfg ON z.FarmerID = kcfg.FarmerID
                                        AND z.LatestSurveyNr=kcfg.SurveyNr ";
                } else {
                    $joinLatestSurvey = "LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }

                //LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid
                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT %s
                        FROM
                            ktv_cocoa_farmer kcf
                            LEFT JOIN ktv_cpg kcpg ON kcf.CPGid=kcpg.CPGid
                            LEFT JOIN ktv_province kp ON SUBSTR(kcf.VillageID, 1, 2) = kp.ProvinceID
                            LEFT JOIN ktv_district kd ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                            LEFT JOIN ktv_subdistrict ks ON SUBSTR(kcf.VillageID, 1, 7) = ks.SubDistrictID
                            $joinLatestSurvey
                            $join
                        WHERE
                            kcf.StatusCode='Active' $where $whereLatestSurvey %s";

                $query = $this->db->query(sprintf($sql, "Province,District,SubDistrict,kcpg.CPGid,kcpg.OldCPGid,kcf.FarmerID,
                     kcf.OldFarmerID,kcfg.SurveyNr,kcfg.GardenNr,kcfg.DateCollection,FarmerName,IF(Gender = '1', 'male', 'female') Gender,
                     IF(MaritalStatus = '1','Menikah',IF(MaritalStatus = '2','Single',IF(MaritalStatus = '3','Janda',IF(
                     MaritalStatus = '4','Menikah','Duda')))) AS marital,YEAR(NOW()) - YEAR(Birthdate) AS Age,HandPhone,
                     kcfg.GardenNr,kcfg.Latitude,kcfg.Longitude,kcfg.TahunTanamanCocoa AS Planted,kcfg.GardenHaUnCertified AS Hectare,
                     kcfg.PohonTBM AS TBM,kcfg.PohonTM AS TM,kcfg.PohonRehab AS TR,(IFNULL(kcfg.PohonTBM, 0) + IFNULL(kcfg.PohonTM, 0) + IFNULL(kcfg.PohonRehab, 0)
                     ) AS TotalTrees,ROUND((IFNULL(kcfg.PohonTBM, 0) + IFNULL(kcfg.PohonTM, 0) + IFNULL(kcfg.PohonRehab, 0)
                     ) / IFNULL(kcfg.GardenHaUnCertified, 0)) AS 'Trees/HA',kcfg.GraftedTrees,kcfg.ReplantedTrees,kcfg.ShadeTreesNr AS 'Shade Trees',
                    ROUND(kcfg.ShadeTreesNr / kcfg.GardenHaUnCertified) AS 'Shade Trees/HA',(IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                    ) + (IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)) + (
                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                    ) AS 'Kg Production',ROUND(((IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                    ) + (IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)) + (
                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0))
                    ) / kcfg.GardenHaUnCertified) AS 'Kg/Ha/Year',CAST((((IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)) + (IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)) + (IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                    )) / (ROUND((IFNULL(kcfg.PohonTBM, 0) + IFNULL(kcfg.PohonTM, 0) + IFNULL(kcfg.PohonRehab, 0)) / IFNULL(kcfg.GardenHaUnCertified, 0)))) as DECIMAL(10,2)) as 'Tree Productivity (Kg/Tree/Year)'", $limi));

                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->row()->total;
                break;
            case 'Summary Garden Data':
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "AND kprov.Province = '{$provinsi}' $where_kab ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                }
                /*
                if ($survey!=''){
                $where .= "AND kcfg.SurveyNr = $survey ";
                }
                 */
                $whereLatestSurvey = "";
                if ($LatestSurvey === 'true') {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_cocoa_farmer_garden
                                            GROUP BY FarmerID
                                        ) z ON kf.FarmerID = z.FarmerID
                                        INNER JOIN ktv_cocoa_farmer_garden kcfg ON z.FarmerID = kcfg.FarmerID
                                        AND z.LatestSurveyNr=kcfg.SurveyNr ";
                } else {
                    $joinLatestSurvey = "LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }
                $sql = 'SELECT
                            %s
                        FROM (
                            SELECT
                                kprov.Province,kdis.District,SUM(kcfg.PohonTBM) TBM,SUM(kcfg.PohonTM) TM,
                                SUM(kcfg.PohonRehab) TR,
                                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS TT,
                                SUM(kcfg.ShadeTreesNr) AS ST,
                                SUM(kcfg.GardenHaUncertified) AS TH,
                                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab)/SUM(kcfg.GardenHaUncertified) AS TPH,
                                SUM(kcfg.ShadeTreesNr)/SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS "Shade",
                                SUM(
                                (
                                    IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                                )
                                ) AS PK,
                                SUM(
                                (
                                    IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                                )
                                )/SUM(kcfg.GardenHaUncertified) AS KPH,
                                kf.StatusCode,
                                kcfg.SurveyNr
                            FROM
                                ktv_cocoa_farmer kf
                                LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksub ON SUBSTR(kf.VillageID, 1, 7) = ksub.SubDistrictID
                                ' . $join . '
                                ' . $joinLatestSurvey . '
                                WHERE
                                    kf.StatusCode="active"
                                    ' . $where . '
                                    ' . $whereLatestSurvey . '
                                GROUP BY
                                    kprov.Province,
                                    kdis.District
                            ) a
                            LEFT JOIN (
                                SELECT kprov.Province,kdis.District,StatusCode,COUNT(kf.FarmerID) TotalFarmer
                                FROM
                                    ktv_cocoa_farmer kf
                                    LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                                GROUP BY
                                    kprov.Province,
                                    kdis.District
                            ) b ON a.Province=b.Province and a.District=b.District and
                            a.StatusCode=b.StatusCode
                            ORDER BY a.Province';
                /*
                if ($kabupaten!=' -- All --') {
                $kab = ($stat == 'preview')?explode(',', $kabupaten):$kabupaten;
                $where_kab = "AND District IN ('".implode("','", $kab)."') ";
                //$where_kab = "AND a.District IN ('".implode("','", $kabupaten)."') ";
                }
                if ($provinsi!=' -- All --') $where = " AND a.Province = '{$provinsi}' $where_kab ";
                if ($survey!='') $where .= " AND a.SurveyNr = $survey ";
                $sql = 'SELECT
                %s
                FROM (
                SELECT
                kprov.Province,kdis.District,SUM(kcfg.PohonTBM) TBM,SUM(kcfg.PohonTM) TM,
                SUM(kcfg.PohonRehab) TR,
                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS TT,
                SUM(kcfg.ShadeTreesNr) AS ST,
                SUM(kcfg.GardenHaUncertified) AS TH,
                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab)/SUM(kcfg.GardenHaUncertified) AS TPH,
                SUM(kcfg.ShadeTreesNr)/SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS "Shade",
                SUM(
                (
                IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                ) + (
                IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                ) + (
                IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                )
                ) AS PK,
                SUM(
                (
                IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                ) + (
                IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                ) + (
                IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                )
                )/SUM(kcfg.GardenHaUncertified) AS KPH,
                kf.StatusCode,
                kcfg.SurveyNr
                FROM
                ktv_cocoa_farmer kf
                LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                LEFT JOIN ktv_subdistrict ksub ON SUBSTR(kf.VillageID, 1, 7) = ksub.SubDistrictID
                INNER JOIN
                (SELECT
                FarmerID,
                MAX(SurveyNr) LatestSurveyNr
                FROM
                ktv_cocoa_farmer_garden
                GROUP BY FarmerID
                ) z ON kf.FarmerID = z.FarmerID
                INNER JOIN ktv_cocoa_farmer_garden kcfg ON z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr=kcfg.SurveyNr
                GROUP BY kprov.Province,kdis.District,ksub.SubDistrict
                ) a
                LEFT JOIN (
                SELECT kprov.Province,kdis.District,StatusCode,COUNT(kf.FarmerID) TotalFarmer
                FROM
                ktv_cocoa_farmer kf
                LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                GROUP BY
                kprov.Province,
                kdis.District
                ) b ON a.Province=b.Province and a.District=b.District and
                a.StatusCode=b.StatusCode
                WHERE
                a.StatusCode="active" '.$where;

                $query = $this->db->query(sprintf($sql,"a.Province,a.District,TotalFarmer,TBM,TM,TR,TT as \"Total Trees\",ST as \"Shade Trees\","
                . "round(TH) as \"Total Ha\",round(TPH) as \"Tree/Ha\",round(Shade*100) as Shade,round(PK) as \"Production Kg\",round(KPH) as \"Kg/Ha\","
                . "round(round(TH)/TotalFarmer,2) as \"Ha/Farmer\""));
                $result['data'] = $query->result_array();
                $query2 = $this->db->query(sprintf($sql,'count(*) as total'));
                $result['total'] = $query2->row()->total;
                 *
                 */
                $query = $this->db->query(sprintf($sql, "a.Province,
                            a.District,
                            b.TotalFarmer,
                            a.TBM,
                            a.TM,
                            a.TR,
                            a.TT as \"Total Trees\",
                            a.ST as \"Shade Trees\",
                            round(a.TH) as \"Total Ha\",
                            round(a.TPH) as \"Tree/Ha\",
                            round(a.Shade*100) as Shade,
                            round(a.PK) as \"Production Kg\",
                            round(a.KPH) as \"Kg/Ha\",
                            round(round(a.TH)/b.TotalFarmer,2) as \"Ha/Farmer\""));
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total'));
                $result['total'] = $query2->row()->total;
                break;
            case 'Summary CPG':
                if ($kabupaten != ' -- All --') {
                    $kab = explode(',', $kabupaten);
                    //$where_kab = "AND kdis.District IN ('".implode("','", $kab)."') ";
                    $where_kab = "AND kd.District IN ('" . implode("','", $kab) . "') ";
                    //AND kd.District = 'Kolaka'
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE kp.Province = '{$provinsi}' {$where_kab} ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $leftjoin = "LEFT JOIN ktv_cpg_partner f ON kc.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner}";
                } elseif ($sesPartner != 'ALL') {
                    $leftjoin = "LEFT JOIN ktv_cpg_partner f ON kc.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }

                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                // LEFT JOIN ktv_district kd ON kp.ProvinceID = kd.ProvinceID
                $sql = 'SELECT %s
                        FROM
                            ktv_province kp
                            INNER JOIN ktv_cpg kc ON kp.ProvinceID = SUBSTR(kc.VillageID,1,2)
                            LEFT OUTER JOIN ktv_cpg_batch_trainings
                            INNER JOIN ktv_cpg_trainings ON ktv_cpg_batch_trainings.CPGtrainingsID = ktv_cpg_trainings.CpgTrainingsID
                            LEFT OUTER JOIN ktv_cpg_batch_trainings_farmers ON
                                ktv_cpg_batch_trainings.CpgBatchTrainingID = ktv_cpg_batch_trainings_farmers.CpgBatchTrainingID
                                ON kc.CPGid = ktv_cpg_batch_trainings.CPGid
                            LEFT JOIN ktv_district kd ON SUBSTR(kc.CPGid,1,4) = kd.DistrictID
                            ' . $leftjoin . '
                        ' . $where . '
                        GROUP BY ktv_cpg_trainings.CpgTrainings,
                            kp.Province,
                            ktv_cpg_batch_trainings.CpgBatchID
                        ORDER BY kp.Province %s ';

                $query = $this->db->query(sprintf($sql, "kp.Province,
                            ktv_cpg_batch_trainings.CpgBatchID AS Batch,
                            ktv_cpg_trainings.CpgTrainings AS \"Training Type\",
                            COUNT(DISTINCT kc.CPGid) AS \"Num of CPG\",
                            COUNT(DISTINCT ktv_cpg_batch_trainings.CpgBatchTrainingID) AS \"Num of Trainings\",
                            COUNT(ktv_cpg_batch_trainings_farmers.CpgBatchTrainingsFarmerID) AS \"Num ofParticipants\"", $limi));
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows();
                break;
            case 'Summary Master Training':
                $where = '';
                if ($provinsi != ' -- All --') {
                    $where .= "WHERE kprov.Province = '{$provinsi}' ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = " LEFT JOIN ktv_cpg_batch_trainings kcbt ON a.CpgBatchID = kcbt.CpgBatchID
                                  LEFT JOIN ktv_cpg_partner kcp ON kcbt.CPGid = kcp.CPGid ";
                    $where .= "AND kcp.PartnerID = {$sesPartner}";
                } elseif ($sesPartner != 'ALL') {
                    $join = " LEFT JOIN ktv_cpg_batch_trainings kcbt ON a.CpgBatchID = kcbt.CpgBatchID
                                  LEFT JOIN ktv_cpg_partner kcp ON kcbt.CPGid = kcp.CPGid ";
                    $where .= "WHERE kcp.PartnerID = {$sesPartner} ";
                }
                if ($limit > 0) {
                    $limi = " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT %s
                        FROM
                            ktv_master_trainings a
                            LEFT JOIN ktv_province kprov ON a.TrainingProvince = kprov.ProvinceID
                            LEFT JOIN ktv_cpg_trainings kct ON a.CPGtrainingsID = kct.CpgTrainingsID
                            LEFT JOIN ktv_program_staff b ON a.StaffID = b.StaffID
                            LEFT JOIN ktv_persons kp ON b.PersonID = kp.PersonID
                            LEFT JOIN ktv_private_staff c ON a.PrivateStaffID = c.PrivateStaffID
                            INNER JOIN ktv_master_trainings_participants d ON a.MasterTrainingID = d.MasterTrainingID
                            LEFT JOIN ktv_extension_staff e ON d.ParticipantStaffID = e.ExtensionID
                            LEFT JOIN ktv_program_staff kps ON d.ParticipantStaffID = kps.StaffID
                            LEFT JOIN ktv_persons kp2 ON kps.PersonID = kp2.PersonID
                            LEFT JOIN ktv_private_staff kprs ON d.ParticipantStaffID = kprs.PrivateStaffID
                            $join
                        $where
                        GROUP BY
                            a.MasterTrainingID,
                            a.CpgBatchID,
                            kprov.Province,
                            kct.CpgTrainings,
                            a.TotLocation,
                            kp.PersonNm,
                            c.StaffName,
                            a.TrainingStart,
                            a.TrainingEnd,
                            a.TrainingDays
                        ORDER BY a.TrainingStart %s";

                $query = $this->db->query(sprintf($sql, "a.MasterTrainingID,a.CpgBatchID,kprov.Province,kct.CpgTrainings,a.TotLocation,
                    kp.PersonNm SCPPStaff,c.StaffName PartnerStaff,a.TrainingStart,a.TrainingEnd,a.TrainingDays,
                    COUNT(d.MasterTrainingsStaffID) TotalParticipants,
                    COUNT(e.ExtensionID) GovernmentParticipants,SUM(CASE WHEN e.Gender = 1 THEN 1 ELSE 0 END) GovernmentParticipantsMale,
                    SUM(CASE WHEN e.Gender = 2 THEN 1 ELSE 0 END) GovernmentParticipantsFemale,
                    COUNT(kps.StaffID) ProgramStaffParticipants,SUM(CASE WHEN kp2.Gender = 'm' THEN 1 ELSE 0 END) ProgramStaffParticipantsMale,
                    SUM(CASE WHEN kp2.Gender = 'f' THEN 1 ELSE 0 END) ProgramStaffParticipantsFemale,
                    COUNT(kprs.PrivateStaffID) PrivateStaffParticipants,
                    SUM(CASE WHEN kprs.StaffGender = 1 THEN 1 ELSE 0 END) PrivateStaffParticipantsMale,
                    SUM(CASE WHEN kprs.StaffGender = 2 THEN 1 ELSE 0 END) PrivateStaffParticipantsFemale", $limi));

                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows();
                break;
            case 'Summary Kader Training':
                if ($kabupaten != ' -- All --') {
//                    $kab = ($stat == 'preview')?explode(',', $kabupaten):$kabupaten;
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND District IN ('" . implode("','", $kab) . "') ";
                } else {
                    $where_kab = "";
                }
                //if ($kabupaten[0]!=' -- All --') $where_kab = "AND kdis.District IN ('".implode("','", $kabupaten)."') ";
                if ($provinsi != ' -- All --') {
                    $where = "WHERE kprov.Province = '{$provinsi}' $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "AND g.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "WHERE g.PartnerID = {$sesPartner} ";
                }
                if ($limit > 0) {
                    $limi = " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT %s
                        FROM
                            ktv_kader_trainings a
                            LEFT JOIN ktv_cpg_trainings kct ON a.CPGtrainingsID = kct.CpgTrainingsID
                            LEFT JOIN ktv_province kprov ON SUBSTR(a.TrainingDistrict,1,2) = kprov.ProvinceID
                            LEFT JOIN ktv_district kdis ON a.TrainingDistrict = kdis.DistrictID
                            LEFT JOIN ktv_program_staff b ON a.StaffID = b.StaffID
                            LEFT JOIN ktv_persons kp ON b.PersonID = kp.PersonID
                            LEFT JOIN ktv_private_staff c ON a.PrivateStaffID = c.PrivateStaffID
                            INNER JOIN ktv_kader_trainings_participants d ON a.CpgKaderTrainingID = d.CpgKaderTrainingID
                            INNER JOIN ktv_cocoa_farmer e ON d.FarmerID = e.FarmerID
                            $join
                        $where
                        GROUP BY  a.CpgKaderTrainingID,
                            a.CpgBatchID,
                            kprov.Province,
                            kdis.District,
                            a.TotLocation,
                            kct.CpgTrainings,
                            kp.PersonNm,
                            c.StaffName,
                            a.TrainingStart,
                            a.TrainingEnd,
                            a.TrainingDays %s";

                $query = $this->db->query(sprintf($sql, "a.CpgKaderTrainingID,a.CpgBatchID,kprov.Province,kdis.District,
                          a.TotLocation,kct.CpgTrainings,kp.PersonNm SCPPStaff,c.StaffName PartnerStaff,a.TrainingStart,
                          a.TrainingEnd,a.TrainingDays,COUNT(d.FarmerID) TotalParticipants,SUM(CASE WHEN e.Gender = 1 THEN 1 ELSE 0 END
                          ) TotalParticipantsMale,SUM(CASE WHEN e.Gender = 2 THEN 1 ELSE 0 END) TotalParticipantsFemale ", $limi));

                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows();
                break;
            case 'Summary CPG Training':
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = " AND kdis.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE kprov.Province = '{$provinsi}' $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "AND g.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "WHERE g.PartnerID = {$sesPartner} ";
                }
                if ($limit > 0) {
                    $limi = " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT %s
                        FROM
                            ktv_cpg_batch_trainings a
                            LEFT JOIN `ktv_cpg_batch` batch ON batch.`CpgBatchID` = a.`CpgBatchID`
                            LEFT JOIN ktv_cpg_trainings kct ON a.CPGtrainingsID = kct.CpgTrainingsID
                            LEFT JOIN ktv_cpg kc ON a.CPGid = kc.CPGid
                            LEFT JOIN ktv_province kprov ON SUBSTR(kc.VillageID,1,2) = kprov.ProvinceID
                            LEFT JOIN ktv_district kdis ON SUBSTR(kc.VillageID,1,4) = kdis.DistrictID
                            LEFT JOIN ktv_program_staff b ON a.ProgramStaffID = b.StaffID
                            LEFT JOIN ktv_persons kp ON b.PersonID = kp.PersonID
                            LEFT JOIN ktv_extension_staff c ON a.ExtensionStaffID = c.ExtensionID
                            INNER JOIN ktv_cpg_batch_trainings_farmers d ON a.CpgBatchTrainingID = d.CpgBatchTrainingID
                            INNER JOIN ktv_cocoa_farmer e ON d.FarmerID = e.FarmerID
                            $join
                        $where
                        GROUP BY
                            a.CpgBatchTrainingID,
                            kc.OldCPGid,
                            kprov.Province,
                            kdis.District,
                            a.CpgBatchID,
                            kct.CpgTrainings,
                            kp.PersonNm,
                            c.StaffName,
                            a.TrainingStart,
                            a.TrainingEnd,
                            a.TrainingDays %s";

                $query = $this->db->query(sprintf($sql, "a.CpgBatchTrainingID,kc.CPGid CPGid,kprov.Province,
                    kdis.District,batch.`BatchNumber` AS CpgBatchID,kct.CpgTrainings,kp.PersonNm SCPPStaff,c.StaffName PartnerStaff,
                    a.TrainingStart,a.TrainingEnd,a.TrainingDays,COUNT(d.FarmerID) TotalParticipants,
                    SUM(CASE WHEN e.Gender = 1 THEN 1 ELSE 0 END) TotalParticipantsMale,
                    SUM(CASE WHEN e.Gender = 2 THEN 1 ELSE 0 END) TotalParticipantsFemale ", $limi));
                // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows();
                break;
            case 'Total Beneficiaries':
                if ($kabupaten != ' -- All --') {
                    $kab       = ($stat == 'preview') ? explode(',', $kabupaten) : $kabupaten;
                    $where_kab = "AND District IN ('" . implode("','", $kab) . "') ";
                }
                $where = '';
                if ($provinsi != ' -- All --') {
                    $where .= " AND kprov.Province = '{$provinsi}' $where_kab ";
                }

                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kf.CPGid = kcp.CPGid ';
                }
                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT %s
                        FROM
                            ktv_cocoa_farmer kf
                            LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID,1,2)=kprov.ProvinceID
                            LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID,1,4)=kdis.DistrictID
                            LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kf.VillageID,1,7)=ksubdis.SubDistrictID
                            LEFT JOIN (SELECT FarmerID,COUNT(FamilyID) Family FROM ktv_family GROUP BY FarmerID) kfam ON kf.FarmerID = kfam.FarmerID
                            $join
                        WHERE kf.StatusCode='Active' $where
                        GROUP BY kprov.Province,kdis.District %s";
                $query = $this->db->query(sprintf($sql, "kprov.Province,
                     kdis.District,
                     COUNT(DISTINCT ksubdis.SubDistrict) TotalSubDistrict,
                     COUNT(DISTINCT kf.VillageID) TotalVillage,
                     COUNT(kf.FarmerID) TotalFarmer,
                     AVG(YEAR(NOW()) - YEAR(Birthdate)) AS AvgAge,
                     SUM(
                         CASE
                           WHEN kf.Gender = 1
                           THEN 1
                           ELSE 0
                         END
                       ) Male,
                       SUM(
                         CASE
                           WHEN kf.Gender = 2
                           THEN 1
                           ELSE 0
                         END
                       ) Female,
                       100*SUM(
                         CASE
                           WHEN kf.Gender = 2
                           THEN 1
                           ELSE 0
                         END
                       )/COUNT(kf.FarmerID) AS FemalePercent,
                       COUNT(kfam.Family) FamilyMembers", $limi));

                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows();
                break;
            case 'Current Farmer Data':
                if ($kabupaten != ' -- All --') {
                    $kab       = ($stat == 'preview') ? explode(',', $kabupaten) : $kabupaten;
                    $where_kab = "AND kd.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = " AND Province = '{$provinsi}' $where_kab ";
                }

                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }
                if ($limit > 0) {
                    $limi = " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT %s
                        FROM
                            ktv_cocoa_farmer kcf
                            LEFT JOIN ktv_province kp ON SUBSTR(kcf.VillageID, 1, 2) = kp.ProvinceID
                            LEFT JOIN ktv_district kd ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                            LEFT JOIN ktv_subdistrict ks ON SUBSTR(kcf.VillageID, 1, 7) = ks.SubDistrictID
                            LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcf.FarmerID = kcfg.FarmerID
                            INNER JOIN (SELECT FarmerID,MAX(SurveyNr) LatestSurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID) kcfgb ON kcfg.FarmerID = kcfgb.FarmerID AND kcfgb.LatestSurveyNr=kcfg.SurveyNr
                            $join
                        WHERE kcfg.FarmerID>0 $where
                        GROUP BY kcf.FarmerID, kcfg.SurveyNr, kcfg.GardenNr %s";

                $query = $this->db->query(sprintf($sql, "Province,
                    District,
                    SubDistrict,
                    kcf.CPGid,
                    kcf.FarmerID,
                    FarmerName,
                    Gender,
                    IF(
                      MaritalStatus = '1',
                      'Menikah',
                      IF(
                        MaritalStatus = '2',
                        'Single',
                        IF(
                          MaritalStatus = '3',
                          'Janda',
                          IF(
                            MaritalStatus = '4',
                            'Menikah',
                            'Duda'
                          )
                        )
                      )
                    ) AS marital,
                    YEAR(NOW()) - YEAR(Birthdate) AS Age,
                    HandPhone,
                    kcfg.SurveyNr,
                    kcfg.GardenNr,
                    kcfg.Latitude,
                    kcfg.Longitude,
                    kcfg.TahunTanamanCocoa AS Planted,
                    kcfg.GardenHaUnCertified AS Hectare,kcfg.PohonTBM AS TBM,kcfg.PohonTM AS TM,kcfg.PohonRehab AS TR, SUM(IFNULL(kcfg.PohonTBM,0)+IFNULL(kcfg.PohonTM,0)+IFNULL(kcfg.PohonRehab,0)) AS TotalTrees,SUM(IFNULL(kcfg.PohonTBM,0)+IFNULL(kcfg.PohonTM,0)+IFNULL(kcfg.PohonRehab,0))/IFNULL(kcfg.GardenHaUnCertified,0) AS 'Trees/HA',kcfg.GraftedTrees,kcfg.ReplantedTrees,kcfg.ShadeTreesNr AS 'Shade Trees',kcfg.ShadeTreesNr/kcfg.GardenHaUnCertified AS 'Shade Trees/HA'
                   , (kcfg.PanenTrekMonths * kcfg.TimeHarvestTrek * kcfg.PanenTrekKg) + (kcfg.PanenBiasaMonths * kcfg.TimeHarvestBiasa * kcfg.PanenBiasaKg) + (kcfg.PanenRayaMonths *kcfg.TimeHarvestRaya * kcfg.PanenRayaKg) AS 'Kg Production'
                   ,((kcfg.PanenTrekMonths * kcfg.TimeHarvestTrek * kcfg.PanenTrekKg) + (kcfg.PanenBiasaMonths * kcfg.TimeHarvestBiasa * kcfg.PanenBiasaKg) + (kcfg.PanenRayaMonths *kcfg.TimeHarvestRaya * kcfg.PanenRayaKg))/kcfg.GardenHaUnCertified AS 'Kg/Ha'", $limi));

                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows(); //$query2->row()->total;
                break;
            case 'Labour Data':
                if ($kabupaten != ' -- All --') {
                    $kab       = ($stat == 'preview') ? explode(',', $kabupaten) : $kabupaten;
                    $where_kab = "AND d.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE c.Province = '{$provinsi}' {$where_kab} ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $where .= "AND e.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner e ON b.CPGid = e.CPGid ';
                } elseif ($sesPartner != 'ALL') {
                    $where .= "WHERE e.PartnerID = {$sesPartner}";
                    $join = ' LEFT JOIN ktv_cpg_partner e ON b.CPGid = e.CPGid ';
                }
                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT
                            %s
                        FROM
                            ktv_cocoa_farmer_post_harvest a
                            INNER JOIN ktv_cocoa_farmer b ON a.FarmerID = b.FarmerID
                            INNER JOIN ktv_province c ON SUBSTR(b.VillageID,1,2) = c.ProvinceID
                            LEFT JOIN ktv_district d ON SUBSTR(b.VillageID, 1, 4) = d.DistrictID
                        {$join}
                        {$where}
                        GROUP BY c.Province, d.District %s";
                $query = $this->db->query(sprintf($sql, "c.Province,d.District,SUM(a.AnggotaKerjaKebun) AS FamilyMembers,
                            SUM(a.BuruhSeasonal) AS SeasonalFarmLabour,SUM(a.BuruhFullTime) AS FullTimeFarmLabour", $limi));
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->num_rows();
                break;
            case 'Nutrisi':
                //if ($kabupaten[0]!=' -- All --'){
                if ($kabupaten != ' -- All --') {
                    //$kab = ($stat == 'preview')?explode(',', $kabupaten):$kabupaten;
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kd.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE ktv_province.Province = '{$provinsi}' {$where_kab} ";
                }

                //if ($survey!='') $where .= " AND kcfg.SurveyNr = $survey ";
                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }
                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                $whereLatestSurvey = "";
                if ($LatestSurvey === 'true') {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_nutrition
                                            GROUP BY
                                                FarmerID
                                        ) z ON kcf.FarmerID = z.FarmerID
                                        LEFT JOIN ktv_nutrition kcfg ON z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr = kcfg.SurveyNr";
                } else {
                    $joinLatestSurvey = "INNER JOIN ktv_nutrition kcfg ON kcf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }

                $sql = "SELECT %s
                         FROM
                            ktv_cocoa_farmer kcf
                            $joinLatestSurvey
                            INNER JOIN ktv_province ON SUBSTR(kcf.VillageID,1,2) = ktv_province.ProvinceID
                            INNER JOIN ktv_district kd ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                            $join
                        $where $whereLatestSurvey %s";

                $query = $this->db->query(sprintf($sql, "ktv_province.Province,kd.District,
                    kcf.FarmerID,kcf.FarmerName,kcfg.SurveyNr,kcfg.InterviewDate,kcfg.KebunPanjang,kcfg.KebunLebar,kcfg.Score", $limi));
                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->row()->total;
                break;
            case 'PPI':
                if ($kabupaten != ' -- All --') {
                    //$kab = ($stat == 'preview')?explode(',', $kabupaten):$kabupaten;
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kd.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE ktv_province.Province = '{$provinsi}' {$where_kab} ";
                }

                //if ($survey!='') $where .= " AND kcfg.SurveyNr = $survey ";
                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }

                $whereLatestSurvey = "";
                if ($LatestSurvey === 'true') {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_ppiscore2012
                                            GROUP BY
                                                FarmerID
                                        ) z ON kcf.FarmerID = z.FarmerID
                                        LEFT JOIN ktv_ppiscore2012 kcfg ON z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr = kcfg.SurveyNr";
                } else {
                    $joinLatestSurvey = "INNER JOIN ktv_ppiscore2012 kcfg ON kcf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }

                $sql = "SELECT %s
                        FROM
                            ktv_cocoa_farmer kcf
                            $joinLatestSurvey
                            INNER JOIN ktv_province ON SUBSTR(kcf.VillageID,1,2) = ktv_province.ProvinceID
                            INNER JOIN ktv_district kd ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                            $join
                        $where $whereLatestSurvey %s";
                $query = $this->db->query(sprintf($sql, "ktv_province.Province,kd.District,
                    kcf.FarmerID,kcf.FarmerName,kcfg.SurveyNr,kcfg.InterviewDate,
                    kcfg.Score,kcfg.National,`1.25/day` as `PPI1`,`2.5/day` as `PPI2`", $limi));

                $result['data']  = $query->result_array();
                $query2          = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                $result['total'] = $query2->row()->total;
                break;
            case 'GAP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                Male,
                                Female,
                                MIN(Joined) AS Joined
                        FROM
                        (
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 1) THEN 1 ELSE 0 END) AS `Male`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 2) THEN 1 ELSE 0 END) AS `Female`,
                                MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((((`ktv_cpg_batch_trainings_farmers`
                                JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                            WHERE
                            (
                                (`ktv_cpg_trainings`.`CpgTrainingsID` = '1')
                                AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov}
                                {$where_kab}
                            )
                            GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`) UNION (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 1) THEN 1 ELSE 0 END) AS `Male`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 2) THEN 1 ELSE 0 END) AS `Female`,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                            (
                                (`ktv_cpg_trainings`.`CpgTrainingsID` = '1')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab}
                             )
                            GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                            HAVING MIN(Joined) = {$trainingYear}
                    ) x
                    GROUP BY Province,District,CpgTrainings,Joined";
                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query          = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female"));
                $result['data'] = $query->result_array();
                //$query2 = $this->db->query(sprintf($sql,'count(*) as total'));
                //$result['total'] = $query2->num_rows();
                break;
            case 'GFP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            %s
                        FROM
                        (
                            SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                    WHERE
                                        ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                        AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                        {$where_prov} {$where_kab})
                                    GROUP BY
                                        `ktv_province`.`Province` ,
                                        `ktv_district`.`District` ,
                                        `ktv_cpg_trainings`.`CpgTrainings` ,
                                        `ktv_cocoa_farmer`.`FarmerID`
                                ) UNION
                                (SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                    (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                    (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                    MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((`ktv_kader_trainings`
                                    JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                    JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                        AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                        {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`)
                                ) base
                                $join
                                $where2
                                GROUP BY
                                    Province,District,CpgTrainings,FarmerID
                                    HAVING MIN(Joined) = {$trainingYear}
                                ) x
                                GROUP BY
                                    Province,District,CpgTrainings,Joined";

                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query          = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as 'Family Male',sum(Fam_Female) as 'Family Female'"));
                $result['data'] = $query->result_array();
                //$query2 = $this->db->query(sprintf($sql,'count(*) as total',''));
                //$result['total'] = $query2->num_rows();
                break;
            case 'GNP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            )
                            UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`
                        )) base
                        $join
                        $where2
                    GROUP BY
                        Province,District,CpgTrainings,FarmerID
                        HAVING MIN(Joined) = {$trainingYear}
                    ) x
                    GROUP BY
                        Province,District,CpgTrainings,Joined";
                $query          = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as 'Familiy Male',sum(Fam_Female) as 'Familiy Female'"));
                $result['data'] = $query->result_array();
                break;
            case 'Cumulative GAP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                //if ($limit>0) $limi .= " LIMIT {$start},{$limit}";
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GAP good agriculture practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab}
                                    )
                                GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`
                                ) UNION
                                (SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                    MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((`ktv_kader_trainings`
                                    JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                    JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GAP good agriculture practices')
                                    AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab}
                                    )
                                GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                            ) base
                            $join
                            $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                        ) x
                    GROUP BY
                        Province,District,CpgTrainings,Joined";
                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query          = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female"));
                $result['data'] = $query->result_array();
                //$query2 = $this->db->query(sprintf($sql,'count(*) as total'));
                //$result['total'] = $query2->num_rows();
                break;
            case 'Cumulative GFP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                        JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                        JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                        JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                        LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                        JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                        JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                        JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                        JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province`,
                                    `ktv_district`.`District`,
                                    `ktv_cpg_trainings`.`CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID`)
                            UNION
                                (SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                    (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                    (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                    MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((`ktv_kader_trainings`
                                    JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                    JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                    AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`)
                            ) base
                            $join
                            $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                        ) x
                    GROUP BY
                        Province,District,CpgTrainings,Joined ";
                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query          = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as 'Family Male',sum(Fam_Female) as 'Family Female'"));
                $result['data'] = $query->result_array();
                //$query2 = $this->db->query(sprintf($sql,'count(*) as total'));
                //$result['total'] = $query2->num_rows();
                break;
            case 'Cumulative GNP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                     AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                    ) x
                GROUP BY
                    Province,District,CpgTrainings,Joined";
                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query          = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as 'Family Male',sum(Fam_Female) as 'Family Female'"));
                $result['data'] = $query->result_array();
                //$query2 = $this->db->query(sprintf($sql,'count(*) as total'));
                //$result['total'] = $query2->num_rows();
                break;
            case 'Certification':
                $myFile = "test.txt";
                $fh     = fopen($myFile, 'w');
                fwrite($fh, $certificationType);
                fclose($fh);
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND f.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE e.Province = '{$provinsi}' {$where_kab} ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $leftjoin = "LEFT JOIN ktv_cpg_partner j ON d.CPGid = j.CPGid ";
                    $where .= "AND j.PartnerID = {$sesPartner}";
                } elseif ($sesPartner != 'ALL') {
                    $leftjoin = "LEFT JOIN ktv_cpg_partner j ON d.CPGid = j.CPGid ";
                    $where .= "WHERE j.PartnerID = {$sesPartner} ";
                }

                if ($trainingYear > 0) {
                    $where .= " AND YEAR(ExternalDate) = {$trainingYear} ";
                }

                if ($certificationType > 0) {
                    $where .= " AND c.Certification = {$certificationType} ";
                }

                if ($limit > 0) {
                    $limi .= " LIMIT {$start},{$limit}";
                }

                $sql = "SELECT
                            %s
                        FROM
                            ktv_cocoa_certification_audit_log a
                            JOIN (
                                SELECT
                                    FarmerID,
                                    MAX(DateCreated) as DateCreated
                                FROM
                                    ktv_cocoa_certification_audit_log
                                GROUP BY
                                    FarmerID
                            ) b ON a.FarmerID = b.FarmerID AND a.DateCreated = b.DateCreated
                            LEFT JOIN ktv_cocoa_certification c ON a.FarmerID = c.FarmerID AND a.SurveyNr = c.SurveyNr
                            LEFT JOIN ktv_cocoa_farmer d ON b.FarmerID = d.FarmerID
                            LEFT JOIN ktv_province e ON SUBSTR(d.VillageID, 1, 2) = e.ProvinceID
                            LEFT JOIN ktv_district f ON SUBSTR(d.VillageID, 1, 4) = f.DistrictID
                            LEFT JOIN ktv_subdistrict g ON SUBSTR(d.VillageID, 1, 7) = g.SubDistrictID
                            INNER JOIN (
                                SELECT
                                    FarmerID,
                                    MAX(SurveyNr) LatestSurveyNr
                                FROM
                                    ktv_cocoa_farmer_garden
                                GROUP BY FarmerID
                            ) i ON d.FarmerID = i.FarmerID
                            INNER JOIN (
                                SELECT
                                    FarmerID,
                                    SurveyNr,
                                    (PohonTBM + PohonTM + PohonRehab) as TotalTrees,
                                    GardenHaUncertified as TotalHa,
                                    ROUND(
                                        (
                                            IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
                                        ) + (
                                            IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
                                        ) + (
                                            IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
                                        )
                                    ) as Kg,
                                    ROUND(
                                        (
                                            IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
                                        ) + (
                                            IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
                                        ) + (
                                            IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
                                        )
                                    )/GardenHaUncertified as KgHa,
                                    ShadeTreesNr as ShadeTrees,
                                    ROUND(ShadeTreesNr/GardenHaUncertified) as ShadeTreesHa,
                                    Herbisida,
                                    CASE
                                        WHEN Herbisida1 > 0
                                            THEN ' - Round Up'
                                        WHEN Herbisida2 > 0
                                            THEN ' - Basmilang'
                                        WHEN Herbisida3 > 0
                                            THEN ' - Pilar Up'
                                        WHEN Herbisida4 > 0
                                            THEN ' - Sun Up'
                                        WHEN Herbisida5 > 0
                                            THEN ' - Gramoxone'
                                        WHEN Herbisida6 > 0
                                            THEN ' - Supremo'
                                        WHEN Herbisida7 > 0
                                            THEN ' - Sapurata'
                                        WHEN Herbisida8 > 0
                                            THEN ' - Rambo'
                                        WHEN Herbisida9 > 0
                                            THEN ' - Para Special'
                                        WHEN Herbisida10 > 0
                                            THEN ' - Noxone'
                                        WHEN MerekHerbisida <> '' OR MerekHerbisida > 0
                                            THEN CONCAT(' - ',MerekHerbisida)
                                        ELSE ''
                                    END as MerkHerbisida,
                                    Insectisida,
                                    CASE
                                        WHEN Insectisida1 > 0
                                            THEN ' - Alika'
                                        WHEN Insectisida2 > 0
                                            THEN ' - Matador'
                                        WHEN Insectisida3 > 0
                                            THEN ' - Capture'
                                        WHEN Insectisida4 > 0
                                            THEN ' - Bento'
                                        WHEN Insectisida5 > 0
                                            THEN ' - Regent'
                                        WHEN Insectisida6 > 0
                                            THEN ' - Drusban'
                                        WHEN Insectisida7 > 0
                                            THEN ' - Penalti'
                                        WHEN Insectisida8 > 0
                                            THEN ' - Nurelle'
                                        WHEN Insectisida9 > 0
                                            THEN ' - Cloromit'
                                        WHEN Insectisida10 > 0
                                            THEN ' - Decis'
                                        WHEN Insectisida11 > 0
                                            THEN ' - Organik'
                                        WHEN MerekInsectisida <> '' OR MerekInsectisida > 0
                                            THEN CONCAT(' - ',MerekInsectisida)
                                        ELSE ''
                                    END as MerkInsectisida,
                                    Fungisida,
                                    CASE
                                        WHEN Fungisida1 > 0
                                            THEN ' - Nordox'
                                        WHEN Fungisida2 > 0
                                            THEN ' - Dithane'
                                        WHEN Fungisida3 > 0
                                            THEN ' - Amistartop'
                                        WHEN Fungisida4 > 0
                                            THEN ' - Scorpio'
                                        WHEN Fungisida5 > 0
                                            THEN ' - Rhidomil'
                                        WHEN Fungisida6 > 0
                                            THEN ' - Antila'
                                        WHEN Fungisida7 > 0
                                            THEN ' - Antracol'
                                        WHEN Fungisida8 > 0
                                            THEN ' - Capture'
                                        WHEN Fungisida9 > 0
                                            THEN ' - Polidor'
                                        WHEN Fungisida10 > 0
                                            THEN ' - Cozeb'
                                        WHEN Fungisida11 > 0
                                            THEN ' - Organik'
                                        WHEN MerekFungisida <> '' OR MerekFungisida > 0
                                            THEN CONCAT(' - ',MerekFungisida)
                                        ELSE ''
                                    END as MerkFungisida
                                FROM
                                    ktv_cocoa_farmer_garden
                            ) h ON i.FarmerID = h.FarmerID AND i.LatestSurveyNr=h.SurveyNr
                            {$leftjoin}
                            {$where}
                        ORDER BY
                            a.FarmerID %s";
                #LEFT JOIN ktv_cpg_partner j ON d.CPGid = j.CPGid
                $query = $this->db->query(sprintf($sql, "a.FarmerID,
                                                            d.FarmerName,
                                                            IF(d.Gender = '1','Pria','Wanita') as Gender,
                                                            concat(e.Province,'/',f.District,'/',g.SubDistrict) as Location,
                                                            h.TotalTrees,
                                                            h.TotalHa,
                                                            h.Kg,
                                                            h.KgHa as 'Kg/Ha',
                                                            h.ShadeTrees,
                                                            h.ShadeTreesHa as 'Shades Tress/Ha',
                                                            IF(h.Herbisida = '1',CONCAT('Ya',h.MerkHerbisida),'Tidak') as Herbisida,
                                                            IF(h.Insectisida = '1',CONCAT('Ya',h.MerkInsectisida),'Tidak') as Insectisida,
                                                            IF(h.Fungisida = '1',CONCAT('Ya',h.MerkFungisida),'Tidak') as Fungisida,
                                                            c.CandidateSelection,
                                                            c.CertificationStart,
                                                            c.CertificationEnd,
                                                            a.DateCreated as 'Internal Audit',
                                                            CASE
                                                                WHEN a.StatusAudit=1
                                                                    THEN 'Lolos'
                                                                WHEN a.StatusAudit=2
                                                                    THEN 'Tidak Lolos'
                                                                ELSE ''
                                                            END as StatusAudit,
                                                            a.CommentAudit,
                                                            c.ExternalDate", $limi));
                $result['data'] = $query->result_array();
                $query2         = $this->db->query(sprintf($sql, 'count(*) as total', ''));
                //$result['total'] = $query2->num_rows();
                $result['total'] = $query2->row()->total;
                break;
        } // end switch
        return $result;
    }

    public function exportFarmer($provinsi, $kabupaten, $jenis, $survey, $trainingYear, $CertificationType = '', $LatestSurvey, $sesPartner)
    {
        switch ($jenis) {
            case 'Nutrition Summary':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND a.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE a.Province = ? $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }

                $sql = "SELECT
                            a.Province,
                            a.District,
                            COUNT(a.FarmerID) as 'GNP Participants',
                            sum(IF(kelamin=2,1,0)) 'Female Participants',
                            b.total as IDDS,
                            c.total as 'Nutrition Garden Area'
                        FROM(
                            SELECT
                                kprov.Province,
                                kdis.District,
                                ksubdis.SubDistrict,
                                kcf.FarmerID,
                                if(kcbtf.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
                            FROM
                                ktv_cpg_batch_trainings kcbt
                                INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
                                LEFT JOIN ktv_cocoa_farmer kcf ON kcbtf.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_family kf ON kcbtf.FamilyID = kf.FamilyID
                                LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID,1,2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID,1,4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kcf.VillageID,1,7) = ksubdis.SubDistrictID
                            WHERE kcbt.CPGtrainingsID=2
                            UNION ALL
                            SELECT
                                kprov.Province,
                                kdis.District,
                                ksubdis.SubDistrict,
                                kcf.FarmerID,
                                if(kktp.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
                            FROM
                                ktv_kader_trainings kkt
                                INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID= kktp.CpgKaderTrainingID
                                LEFT JOIN ktv_cocoa_farmer kcf ON kktp.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_family kf ON kktp.FamilyID = kf.FamilyID
                                LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID,1,2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID,1,4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kcf.VillageID,1,7) = ksubdis.SubDistrictID
                            WHERE kkt.CPGtrainingsID=2
                        ) a
                        LEFT JOIN (
                            SELECT *
                            FROM (
                                SELECT
                                    kp.Province,
                                    kdis.District,
                                    round(sum(kn.Score)/count(knb.FarmerID),2) total,
                                    sum(kn.Score) sc,
                                    count(knb.FarmerID) fa
                                FROM
                                    ktv_nutrition kn
                                    INNER JOIN (
                    SELECT
                                            FarmerID,
                                            max(SurveyNr) LastSurveyNr
                    FROM
                                            ktv_nutrition
                    GROUP BY FarmerID
                                    ) knb on kn.FarmerID=knb.FarmerID and kn.SurveyNr=knb.LastSurveyNr
                                    LEFT JOIN ktv_cocoa_farmer kcf ON knb.FarmerID = kcf.FarmerID
                                    LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    SUBSTR(kcf.VillageID,1,4) NOT IN (7317,7322,7325) AND
                                    (kn.Score>0 AND kn.Score<10)
                                GROUP BY
                                    kp.Province,
                                    kdis.District
                                ) a
                            WHERE total>0
                        ) b on a.District = b.District
                        LEFT JOIN (
                            SELECT
                                kp.Province,
                                kdis.District,
                                sum(IFNULL(GardenHaUnCertified,0)) as total,
                                count(distinct a.FarmerID) as jumlah,
                                count(a.FarmerID) as kebun,
                                sum(PohonTM) AS tree,
                                SUM(GardenHaUnCertified) ha
                            FROM ktv_cocoa_farmer_garden a
                                INNER JOIN (
                                    SELECT
                    FarmerID,
                    GardenNr,
                    max(SurveyNr) LatestSurveyNr
                                    FROM
                    ktv_cocoa_certification
                                    WHERE
                    ExternalDate>'0000-00-00'
                                    GROUP BY
                    FarmerID,GardenNr
                                ) z on z.FarmerID=a.FarmerID and z.GardenNr=a.GardenNr and z.LatestSurveyNr=a.SurveyNr
                                INNER JOIN (
                                    SELECT
                    FarmerID,
                    GardenNr,
                    max(SurveyNr) LatestSurveyNr
                                    FROM
                    ktv_cocoa_farmer_garden
                                    GROUP BY
                    FarmerID,
                    GardenNr
                                ) y on a.FarmerID = y.FarmerID and a.GardenNr = y.GardenNr
                                LEFT JOIN ktv_cocoa_farmer kcf on a.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                            WHERE
                                kcf.StatusCode='active' AND
                                kcf.VillageID and GardenHaUnCertified>0 AND
                                kcf.VillageID IS NOT NULL
                            GROUP BY kp.Province, kdis.District
                        ) c on a.District = c.District
                        LEFT JOIN ktv_cocoa_farmer kcf ON a.FarmerID = kcf.FarmerID
                        {$join}
                        {$where}
                    GROUP BY
                        a.Province,
                        a.District";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Certification Summary':
                if ($trainingYear > 0) {
                    $where1 = " YEAR(ExternalDate)={$trainingYear} ";
                } else {
                    $where1 = " ExternalDate>'0000-00-00' ";
                }

                if ($certificationType > 0) {
                    $where1 .= " AND Certification={$certificationType} ";
                }

                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE kprov.Province = ? $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kcf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }

                $sql = "SELECT
                            kprov.Province,
                            kdis.District,
                            count(kcc.FarmerID) as 'Total Cert Farmer',
                            c.total_cert_farm as 'Total Cert Farm',
                            sum(IF(kcf.Gender=1,1,0)) Male,
                            sum(IF(kcf.Gender=2,1,0)) Female,
                            d.total as 'Total Cert Farm Area',
                            d.total_productivity as 'Total Cert Productivity',
                            d.produktivitas as 'Average Productivity of Certified Farm',
                            (e.trees/e.ha) as 'Average Trees per Ha'
                        FROM (
                            SELECT *
                            FROM
                                ktv_cocoa_certification
                            WHERE
                                {$where1}
                            GROUP BY FarmerID
                            ) kcc
                            LEFT JOIN ktv_cocoa_farmer kcf on kcc.FarmerID=kcf.FarmerID
                            LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID, 1, 2) = kprov.ProvinceID
                            LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                            INNER JOIN (
                                SELECT
                                    kdis.DistrictID,
                                    COUNT(kcc.GardenNr) as total_cert_farm
                                FROM ktv_cocoa_certification kcc
                                    LEFT JOIN ktv_cocoa_farmer kcf ON kcc.FarmerID=kcf.FarmerID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    {$where1}
                                    AND SUBSTR(kcf.VillageID,1,2) IS NOT NULL
                                GROUP BY
                                    kdis.District
                            ) c ON kdis.DistrictID = c.DistrictID
                            INNER JOIN (
                                SELECT
                                    kdis.DistrictID,
                                    sum(IFNULL(GardenHaUnCertified,0)) as total,
                                    sum(
                                        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0)
                                        )+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0)
                                        )+(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
                                    ) as total_productivity,
                                    round(sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
                                        SUM(IFNULL(GardenHaUnCertified,0)),0) as produktivitas
                                FROM ktv_cocoa_farmer_garden kcfg
                                    LEFT JOIN ktv_cocoa_farmer kcf on kcfg.FarmerID=kcf.FarmerID
                                    INNER JOIN (
                                        SELECT
                                            FarmerID,
                                            GardenNr,
                                            max(SurveyNr) LatestSurveyNr
                                        FROM
                                            ktv_cocoa_certification
                                        WHERE
                                            {$where1}
                                        GROUP BY
                                            FarmerID,
                                            GardenNr
                                    ) z on z.FarmerID=kcfg.FarmerID and z.GardenNr=kcfg.GardenNr and z.LatestSurveyNr=kcfg.SurveyNr
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    SUBSTR(kcf.VillageID,1,2) IS NOT NULL
                                GROUP BY
                                    kdis.DistrictID
                            ) d ON kdis.DistrictID = d.DistrictID
                            INNER JOIN (
                                SELECT
                                    kdis.DistrictID,
                                    (SUM(PohonTM)/SUM(GardenHaUnCertified)) as avg_trees,
                                    SUM(PohonTM) AS trees,
                                    SUM(GardenHaUnCertified) ha
                                FROM ktv_cocoa_farmer_garden kcfg
                                    INNER JOIN (
                                        SELECT
                                            FarmerID,
                                            GardenNr,
                                            max(SurveyNr) LatestSurveyNr
                                        FROM
                                            ktv_cocoa_certification
                                        WHERE
                                            {$where1}
                                        GROUP BY
                                            FarmerID,GardenNr
                                    ) z on z.FarmerID=kcfg.FarmerID and z.GardenNr=kcfg.GardenNr and z.LatestSurveyNr=kcfg.SurveyNr
                                    INNER JOIN (
                                        SELECT
                                            FarmerID,
                                            GardenNr,
                                            max(SurveyNr) LatestSurveyNr
                                        FROM
                                            ktv_cocoa_farmer_garden
                                        GROUP BY
                                            FarmerID,
                                            GardenNr
                                    ) y on kcfg.FarmerID = y.FarmerID and kcfg.GardenNr = y.GardenNr
                                    LEFT JOIN ktv_cocoa_farmer kcf on kcfg.FarmerID = kcf.FarmerID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                                WHERE
                                    kcf.StatusCode='active' AND
                                    kcf.VillageID and GardenHaUnCertified>0 AND
                                    kcf.VillageID IS NOT NULL
                                GROUP BY
                                    kdis.DistrictID
                            ) e ON e.DistrictID = kdis.DistrictID
                            {$join}
                            {$where}
                            GROUP BY
                                kprov.Province,
                                kdis.District";

                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Farmer Detail Data':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "AND Province = ? $where_kab ";
                }

                //if ($survey!='') $where .= "AND kcfg.SurveyNr = $survey ";
                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }
                //if ($limit>0) $where .= ' LIMIT 50';
                $whereLatestSurvey = "";
                if ($LatestSurvey > 0 || $LatestSurvey === true) {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_cocoa_farmer_garden
                                            GROUP BY FarmerID
                                        ) z ON kcf.FarmerID = z.FarmerID
                                        INNER JOIN ktv_cocoa_farmer_garden kcfg ON z.FarmerID = kcfg.FarmerID
                                        AND z.LatestSurveyNr=kcfg.SurveyNr ";
                } else {
                    $joinLatestSurvey = "LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }
                $sql = "SELECT
                    Province,
                    District,
                    SubDistrict,
                    kcpg.CPGid,
                    kcpg.OldCPGid,
                    kcf.FarmerID,
                    kcf.OldFarmerID,
                    kcfg.SurveyNr,
                    kcfg.GardenNr,
                    kcfg.DateCollection,
                    FarmerName,
                    IF(Gender = '1', 'male', 'female') Gender,
                    IF(MaritalStatus = '1','Menikah',IF(MaritalStatus = '2','Single',IF(MaritalStatus = '3','Janda',IF(MaritalStatus = '4','Menikah','Duda')))
                    ) AS marital,
                    YEAR(NOW()) - YEAR(Birthdate) AS Age,
                    HandPhone,
                    kcfg.GardenNr,
                    kcfg.Latitude,
                    kcfg.Longitude,
                    kcfg.TahunTanamanCocoa AS Planted,
                    kcfg.GardenHaUnCertified AS Hectare,
                    kcfg.PohonTBM AS TBM,
                    kcfg.PohonTM AS TM,
                    kcfg.PohonRehab AS TR,
                    (
                    IFNULL(kcfg.PohonTBM, 0) + IFNULL(kcfg.PohonTM, 0) + IFNULL(kcfg.PohonRehab, 0)
                    ) AS TotalTrees
                    , ROUND((
                        IFNULL(kcfg.PohonTBM, 0) + IFNULL(kcfg.PohonTM, 0) + IFNULL(kcfg.PohonRehab, 0)
                    ) / IFNULL(kcfg.GardenHaUnCertified, 0)) AS 'Trees/HA'
                    , kcfg.GraftedTrees,
                    kcfg.ReplantedTrees,
                    kcfg.ShadeTreesNr AS 'Shade Trees'
                    , ROUND((kcfg.ShadeTreesNr / kcfg.GardenHaUnCertified),2) AS 'Shade Trees/HA'
                    ,(
                        IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                    ) + (
                        IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                    ) + (
                        IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                    ) AS 'Kg Production'
                    , ROUND(
                    (
                        (
                            IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                        ) + (
                            IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                        ) + (
                            IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                        )
                    ) / kcfg.GardenHaUnCertified
                    ) AS 'Kg/Ha/Year'
                    , ROUND((kcfg.PohonTM / kcfg.GardenHaUnCertified),2) AS 'Tree Productivity (Kg/Tree/Year)'
                FROM
                    ktv_cocoa_farmer kcf
                    LEFT JOIN ktv_cpg kcpg ON kcf.CPGid=kcpg.CPGid
                    LEFT JOIN ktv_province kp
                        ON SUBSTR(kcf.VillageID, 1, 2) = kp.ProvinceID
                    LEFT JOIN ktv_district kd
                        ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                    LEFT JOIN ktv_subdistrict ks
                        ON SUBSTR(kcf.VillageID, 1, 7) = ks.SubDistrictID
                    $joinLatestSurvey
                    $join
                WHERE
                    kcf.StatusCode='active' AND kcfg.StatusCode='active'
                    $where $whereLatestSurvey";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Summary Garden Data':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "AND kprov.Province = ? $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg e ON kf.CPGid = e.CPGid LEFT JOIN ktv_cpg_partner f ON e.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                }
                //if ($survey!='') $where .= "AND kcfg.SurveyNr = $survey ";
                $whereLatestSurvey = "";
                if ($LatestSurvey > 0 || $LatestSurvey === true) {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_cocoa_farmer_garden
                                            GROUP BY FarmerID
                                        ) z ON kf.FarmerID = z.FarmerID
                                        INNER JOIN ktv_cocoa_farmer_garden kcfg ON z.FarmerID = kcfg.FarmerID
                                        AND z.LatestSurveyNr=kcfg.SurveyNr ";
                } else {
                    $joinLatestSurvey = "LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }

                $sql = 'SELECT
                            a.Province,
                            a.District,
                            b.TotalFarmer,
                            a.TBM,
                            a.TM,
                            a.TR,
                            a.TT as "Total Trees",
                            a.ST as "Shade Trees",
                            round(a.TH) as "Total Ha",
                            round(a.TPH) as "Tree/Ha",
                            round(a.Shade*100) as Shade,
                            round(a.PK) as "Production Kg",
                            round(a.KPH) as "Kg/Ha",
                            round(round(a.TH)/b.TotalFarmer,2) as "Ha/Farmer"
                        FROM (
                            SELECT
                                kprov.Province,kdis.District,SUM(kcfg.PohonTBM) TBM,SUM(kcfg.PohonTM) TM,
                                SUM(kcfg.PohonRehab) TR,
                                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS TT,
                                SUM(kcfg.ShadeTreesNr) AS ST,
                                SUM(kcfg.GardenHaUncertified) AS TH,
                                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab)/SUM(kcfg.GardenHaUncertified) AS TPH,
                                SUM(kcfg.ShadeTreesNr)/SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS "Shade",
                                SUM(
                                (
                                    IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                                )
                                ) AS PK,
                                SUM(
                                (
                                    IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                                )
                                )/SUM(kcfg.GardenHaUncertified) AS KPH,
                                kf.StatusCode,
                                kcfg.SurveyNr
                            FROM
                                ktv_cocoa_farmer kf
                                LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksub ON SUBSTR(kf.VillageID, 1, 7) = ksub.SubDistrictID
                                ' . $join . '
                                ' . $joinLatestSurvey . '
                                WHERE
                                    kf.StatusCode="active"
                                    ' . $where . '
                                    ' . $whereLatestSurvey . '
                                GROUP BY
                                    kprov.Province,
                                    kdis.District
                            ) a
                            LEFT JOIN (
                                SELECT kprov.Province,kdis.District,StatusCode,COUNT(kf.FarmerID) TotalFarmer
                                FROM
                                    ktv_cocoa_farmer kf
                                    LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                                GROUP BY
                                    kprov.Province,
                                    kdis.District
                            ) b ON a.Province=b.Province and a.District=b.District and
                            a.StatusCode=b.StatusCode
                            ORDER BY a.Province';
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Summary CPG':
                if ($provinsi != ' -- All --') {
                    $where = "WHERE ktv_province.Province = ? ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $leftjoin = "LEFT JOIN ktv_cpg_partner f ON kc.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner}";
                } elseif ($sesPartner != 'ALL') {
                    $leftjoin = "LEFT JOIN ktv_cpg_partner f ON kc.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }
                $sql = 'SELECT %s
                        FROM
                            ktv_province kp
                            INNER JOIN ktv_cpg kc ON kp.ProvinceID = SUBSTR(kc.VillageID,1,2)
                            LEFT OUTER JOIN ktv_cpg_batch_trainings
                            INNER JOIN ktv_cpg_trainings ON ktv_cpg_batch_trainings.CPGtrainingsID = ktv_cpg_trainings.CpgTrainingsID
                            LEFT OUTER JOIN ktv_cpg_batch_trainings_farmers ON
                                ktv_cpg_batch_trainings.CpgBatchTrainingID = ktv_cpg_batch_trainings_farmers.CpgBatchTrainingID
                                ON kc.CPGid = ktv_cpg_batch_trainings.CPGid
                            LEFT JOIN ktv_district kd ON SUBSTR(kc.CPGid,1,4) = kd.DistrictID
                            ' . $leftjoin . '
                        ' . $where . '
                        GROUP BY ktv_cpg_trainings.CpgTrainings,
                            kp.Province,
                            ktv_cpg_batch_trainings.CpgBatchID
                        ORDER BY kp.Province';

                $query = $this->db->query(sprintf($sql, "kp.Province,
                            ktv_cpg_batch_trainings.CpgBatchID AS Batch,
                            ktv_cpg_trainings.CpgTrainings AS \"Training Type\",
                            COUNT(DISTINCT kc.CPGid) AS \"Num of CPG\",
                            COUNT(DISTINCT ktv_cpg_batch_trainings.CpgBatchTrainingID) AS \"Num of Trainings\",
                            COUNT(ktv_cpg_batch_trainings_farmers.CpgBatchTrainingsFarmerID) AS \"Num of Participants\""));
                break;
            case 'Summary Master Training':
                if ($provinsi != ' -- All --') {
                    $where = "WHERE kprov.Province = ? ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = " LEFT JOIN ktv_cpg_batch_trainings kcbt ON a.CpgBatchID = kcbt.CpgBatchID
                                  LEFT JOIN ktv_cpg_partner kcp ON kcbt.CPGid = kcp.CPGid ";
                    $where .= "AND kcp.PartnerID = {$sesPartner}";
                } elseif ($sesPartner != 'ALL') {
                    $join = " LEFT JOIN ktv_cpg_batch_trainings kcbt ON a.CpgBatchID = kcbt.CpgBatchID
                              LEFT JOIN ktv_cpg_partner kcp ON kcbt.CPGid = kcp.CPGid ";
                    $where .= "WHERE kcp.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                    a.MasterTrainingID,
                    a.CpgBatchID,
                    kprov.Province,
                    kct.CpgTrainings,
                    a.TotLocation,
                    kp.PersonNm SCPPStaff,
                    c.StaffName PartnerStaff,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays,
                    COUNT(d.MasterTrainingsStaffID) TotalParticipants,
                    COUNT(e.ExtensionID) GovernmentParticipants,
                    SUM(
                      CASE
                        WHEN e.Gender = 1
                        THEN 1
                        ELSE 0
                      END
                    ) GovernmentParticipantsMale,
                    SUM(
                      CASE
                        WHEN e.Gender = 2
                        THEN 1
                        ELSE 0
                      END
                    ) GovernmentParticipantsFemale,
                    COUNT(kps.StaffID) ProgramStaffParticipants,
                    SUM(
                      CASE
                        WHEN kp2.Gender = 'm'
                        THEN 1
                        ELSE 0
                      END
                    ) ProgramStaffParticipantsMale,
                    SUM(
                      CASE
                        WHEN kp2.Gender = 'f'
                        THEN 1
                        ELSE 0
                      END
                    ) ProgramStaffParticipantsFemale,
                    COUNT(kprs.PrivateStaffID) PrivateStaffParticipants,
                    SUM(
                      CASE
                        WHEN kprs.StaffGender = 1
                        THEN 1
                        ELSE 0
                      END
                    ) PrivateStaffParticipantsMale,
                    SUM(
                      CASE
                        WHEN kprs.StaffGender = 2
                        THEN 1
                        ELSE 0
                      END
                    ) PrivateStaffParticipantsFemale
                  FROM
                    ktv_master_trainings a
                    LEFT JOIN ktv_province kprov ON a.TrainingProvince = kprov.ProvinceID
                    LEFT JOIN ktv_cpg_trainings kct
                      ON a.CPGtrainingsID = kct.CpgTrainingsID
                    LEFT JOIN ktv_program_staff b
                      ON a.StaffID = b.StaffID
                    LEFT JOIN ktv_persons kp
                      ON b.PersonID = kp.PersonID
                    LEFT JOIN ktv_private_staff c
                      ON a.PrivateStaffID = c.PrivateStaffID
                    INNER JOIN ktv_master_trainings_participants d
                      ON a.MasterTrainingID = d.MasterTrainingID
                    LEFT JOIN ktv_extension_staff e
                      ON d.ParticipantStaffID = e.ExtensionID
                    LEFT JOIN ktv_program_staff kps
                      ON d.ParticipantStaffID = kps.StaffID
                    LEFT JOIN ktv_persons kp2
                      ON kps.PersonID = kp2.PersonID
                    LEFT JOIN ktv_private_staff kprs
                      ON d.ParticipantStaffID = kprs.PrivateStaffID
                  $join
                  $where
                  GROUP BY
                    a.MasterTrainingID,
                    a.CpgBatchID,
                    kprov.Province,
                    kct.CpgTrainings,
                    a.TotLocation,
                    kp.PersonNm,
                    c.StaffName,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays
                    ORDER BY a.TrainingStart";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Summary Kader Training':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE kprov.Province = ? $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "AND g.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "WHERE g.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                    a.CpgKaderTrainingID,
                    a.CpgBatchID,
                    kprov.Province,
                    kdis.District,
                    a.TotLocation,
                    kct.CpgTrainings,
                    kp.PersonNm SCPPStaff,
                    c.StaffName PartnerStaff,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays,
                    COUNT(d.FarmerID) TotalParticipants,
                    SUM(
                      CASE
                        WHEN e.Gender = 1
                        THEN 1
                        ELSE 0
                      END
                    ) TotalParticipantsMale,
                    SUM(
                      CASE
                        WHEN e.Gender = 2
                        THEN 1
                        ELSE 0
                      END
                    ) TotalParticipantsFemale
                  FROM
                    ktv_kader_trainings a
                    LEFT JOIN ktv_cpg_trainings kct ON a.CPGtrainingsID = kct.CpgTrainingsID
                    LEFT JOIN ktv_province kprov ON SUBSTR(a.TrainingDistrict,1,2) = kprov.ProvinceID
                    LEFT JOIN ktv_district kdis ON a.TrainingDistrict = kdis.DistrictID
                    LEFT JOIN ktv_program_staff b ON a.StaffID = b.StaffID
                    LEFT JOIN ktv_persons kp ON b.PersonID = kp.PersonID
                    LEFT JOIN ktv_private_staff c ON a.PrivateStaffID = c.PrivateStaffID
                    INNER JOIN ktv_kader_trainings_participants d ON a.CpgKaderTrainingID = d.CpgKaderTrainingID
                    INNER JOIN ktv_cocoa_farmer e ON d.FarmerID = e.FarmerID
                  $join
                  $where
                  GROUP BY   a.CpgKaderTrainingID,
                    a.CpgBatchID,
                    kprov.Province,
                    kdis.District,
                    a.TotLocation,
                    kct.CpgTrainings,
                    kp.PersonNm,
                    c.StaffName,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Summary CPG Training':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE kprov.Province = ? $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "AND g.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg f ON e.CPGid = f.CPGid LEFT JOIN ktv_cpg_partner g ON f.CPGid = g.CPGid ";
                    $where .= "WHERE g.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                    a.CpgBatchTrainingID,
                    kc.CPGid CPGid,
                    kprov.Province,
                    kdis.District,
                    batch.`BatchNumber` AS CpgBatchID,
                    kct.CpgTrainings,
                    kp.PersonNm SCPPStaff,
                    c.StaffName PartnerStaff,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays,
                    COUNT(d.FarmerID) TotalParticipants,
                    SUM(
                      CASE
                        WHEN e.Gender = 1
                        THEN 1
                        ELSE 0
                      END
                    ) TotalParticipantsMale,
                    SUM(
                      CASE
                        WHEN e.Gender = 2
                        THEN 1
                        ELSE 0
                      END
                    ) TotalParticipantsFemale
                  FROM
                    ktv_cpg_batch_trainings a
                    LEFT JOIN `ktv_cpg_batch` batch ON batch.`CpgBatchID` = a.`CpgBatchID`
                    LEFT JOIN ktv_cpg_trainings kct ON a.CPGtrainingsID = kct.CpgTrainingsID
                    LEFT JOIN ktv_cpg kc ON a.CPGid = kc.CPGid
                    LEFT JOIN ktv_province kprov ON SUBSTR(kc.VillageID,1,2) = kprov.ProvinceID
                    LEFT JOIN ktv_district kdis ON SUBSTR(kc.VillageID,1,4) = kdis.DistrictID
                    LEFT JOIN ktv_program_staff b ON a.ProgramStaffID = b.StaffID
                    LEFT JOIN ktv_persons kp ON b.PersonID = kp.PersonID
                    LEFT JOIN ktv_extension_staff c ON a.ExtensionStaffID = c.ExtensionID
                    INNER JOIN ktv_cpg_batch_trainings_farmers d ON a.CpgBatchTrainingID = d.CpgBatchTrainingID
                    INNER JOIN ktv_cocoa_farmer e ON d.FarmerID = e.FarmerID
                    $join
                  $where
                  GROUP BY
                    a.CpgBatchTrainingID,
                    kc.OldCPGid,
                    kprov.Province,
                    kdis.District,
                    a.CpgBatchID,
                    kct.CpgTrainings,
                    kp.PersonNm,
                    c.StaffName,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Total Beneficiaries':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "AND kprov.Province = ? $where_kab ";
                }

                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kf.CPGid = kcp.CPGid ';
                }
                $sql = "SELECT
                     kprov.Province,
                     kdis.District,
                     COUNT(DISTINCT ksubdis.SubDistrict) TotalSubDistrict,
                     COUNT(DISTINCT kf.VillageID) TotalVillage,
                     COUNT(kf.FarmerID) TotalFarmer,
                     AVG(YEAR(NOW()) - YEAR(Birthdate)) AS AvgAge,
                     SUM(
                         CASE
                           WHEN kf.Gender = 1
                           THEN 1
                           ELSE 0
                         END
                       ) Male,
                       SUM(
                         CASE
                           WHEN kf.Gender = 2
                           THEN 1
                           ELSE 0
                         END
                       ) Female,
                       100*SUM(
                         CASE
                           WHEN kf.Gender = 2
                           THEN 1
                           ELSE 0
                         END
                       )/COUNT(kf.FarmerID) AS FemalePercent,
                       COUNT(kfam.Family) FamilyMembers
                     FROM
                     ktv_cocoa_farmer kf
                     LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID,1,2)=kprov.ProvinceID
                     LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID,1,4)=kdis.DistrictID
                     LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kf.VillageID,1,7)=ksubdis.SubDistrictID
                     LEFT JOIN (SELECT FarmerID,COUNT(FamilyID) Family FROM ktv_family GROUP BY FarmerID) kfam ON kf.FarmerID = kfam.FarmerID
                    $join
                    WHERE kf.StatusCode='Active' $where
                     GROUP BY kprov.Province,
                     kdis.District";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Current Farmer Data':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "AND Province = ? $where_kab ";
                }

                //if ($limit>0) $limi = ' LIMIT 50';
                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }
                $sql = "SELECT
                    Province,
                    District,
                    SubDistrict,
                    kcf.CPGid,
                    kcf.FarmerID,
                    FarmerName,
                    Gender,
                    IF(
                      MaritalStatus = '1',
                      'Menikah',
                      IF(
                        MaritalStatus = '2',
                        'Single',
                        IF(
                          MaritalStatus = '3',
                          'Janda',
                          IF(
                            MaritalStatus = '4',
                            'Menikah',
                            'Duda'
                          )
                        )
                      )
                    ) AS marital,
                    YEAR(NOW()) - YEAR(Birthdate) AS Age,
                    HandPhone,
                    kcfg.SurveyNr,
                    kcfg.GardenNr,
                    kcfg.Latitude,
                    kcfg.Longitude,
                    kcfg.TahunTanamanCocoa AS Planted,
                    kcfg.GardenHaUnCertified AS Hectare,kcfg.PohonTBM AS TBM,kcfg.PohonTM AS TM,kcfg.PohonRehab AS TR, SUM(IFNULL(kcfg.PohonTBM,0)+IFNULL(kcfg.PohonTM,0)+IFNULL(kcfg.PohonRehab,0)) AS TotalTrees,SUM(IFNULL(kcfg.PohonTBM,0)+IFNULL(kcfg.PohonTM,0)+IFNULL(kcfg.PohonRehab,0))/IFNULL(kcfg.GardenHaUnCertified,0) AS 'Trees/HA',kcfg.GraftedTrees,kcfg.ReplantedTrees,kcfg.ShadeTreesNr AS 'Shade Trees',kcfg.ShadeTreesNr/kcfg.GardenHaUnCertified AS 'Shade Trees/HA'
                   ,(kcfg.PanenTrekMonths * kcfg.TimeHarvestTrek * kcfg.PanenTrekKg) + (kcfg.PanenBiasaMonths * kcfg.TimeHarvestBiasa * kcfg.PanenBiasaKg) + (kcfg.PanenRayaMonths *kcfg.TimeHarvestRaya * kcfg.PanenRayaKg) AS 'Kg Production'
                   ,((kcfg.PanenTrekMonths * kcfg.TimeHarvestTrek * kcfg.PanenTrekKg) + (kcfg.PanenBiasaMonths * kcfg.TimeHarvestBiasa * kcfg.PanenBiasaKg) + (kcfg.PanenRayaMonths *kcfg.TimeHarvestRaya * kcfg.PanenRayaKg))/kcfg.GardenHaUnCertified AS 'Kg/Ha'
                  FROM
                    ktv_cocoa_farmer kcf
                    LEFT JOIN ktv_province kp
                      ON SUBSTR(kcf.VillageID, 1, 2) = kp.ProvinceID
                    LEFT JOIN ktv_district kd
                      ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                    LEFT JOIN ktv_subdistrict ks
                      ON SUBSTR(kcf.VillageID, 1, 7) = ks.SubDistrictID
                    LEFT JOIN ktv_cocoa_farmer_garden kcfg
                      ON kcf.FarmerID = kcfg.FarmerID
                    INNER JOIN (SELECT FarmerID,MAX(SurveyNr) LatestSurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID) kcfgb
                      ON kcfg.FarmerID = kcfgb.FarmerID
                      AND kcfgb.LatestSurveyNr=kcfg.SurveyNr
                  $join
                  WHERE kcfg.FarmerID>0 $where
                  GROUP BY kcf.FarmerID, kcfg.SurveyNr, kcfg.GardenNr $limi";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Labour Data':
                if ($kabupaten != ' -- All --') {
                    $kab       = ($stat == 'preview') ? explode(',', $kabupaten) : $kabupaten;
                    $where_kab = "AND d.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "WHERE c.Province = '{$provinsi}' {$where_kab}";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $where .= "AND e.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner e ON b.CPGid = e.CPGid ';
                } elseif ($sesPartner != 'ALL') {
                    $where .= "WHERE e.PartnerID = {$sesPartner}";
                    $join = ' LEFT JOIN ktv_cpg_partner e ON b.CPGid = e.CPGid ';
                }
                $sql = "SELECT
                            c.Province,d.District,SUM(a.AnggotaKerjaKebun) AS FamilyMembers,
                            SUM(a.BuruhSeasonal) AS SeasonalFarmLabour,SUM(a.BuruhFullTime) AS FullTimeFarmLabour
                        FROM
                            ktv_cocoa_farmer_post_harvest a
                            INNER JOIN ktv_cocoa_farmer b ON a.FarmerID = b.FarmerID
                            INNER JOIN ktv_province c ON SUBSTR(b.VillageID,1,2) = c.ProvinceID
                            LEFT JOIN ktv_district d ON SUBSTR(b.VillageID, 1, 4) = d.DistrictID
                        {$join}
                        {$where}
                        GROUP BY c.Province, d.District";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Nutrisi':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kd.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE ktv_province.Province = ?";
                }

                //if ($survey!='') $where .= "AND kcfg.SurveyNr = $survey ";
                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }
                $whereLatestSurvey = "";
                if ($LatestSurvey > 0 || $LatestSurvey === true) {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_nutrition
                                            GROUP BY
                                                FarmerID
                                        ) z ON kcf.FarmerID = z.FarmerID
                                        LEFT JOIN ktv_nutrition kcfg ON z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr = kcfg.SurveyNr";
                } else {
                    $joinLatestSurvey = "INNER JOIN ktv_nutrition kcfg ON kcf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }
                $sql = "SELECT
                    ktv_province.Province,kd.District,
                    kcf.FarmerID,kcf.FarmerName,kcfg.SurveyNr,kcfg.InterviewDate,kcfg.KebunPanjang,kcfg.KebunLebar,kcfg.Score
                  FROM
                    ktv_cocoa_farmer kcf
                    $joinLatestSurvey
                    INNER JOIN ktv_province ON SUBSTR(kcf.VillageID,1,2) = ktv_province.ProvinceID
                    INNER JOIN ktv_district kd ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                    $where $whereLatestSurvey";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'PPI':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND kd.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE ktv_province.Province = ?";
                }

                //if ($survey!='') $where .= "AND kcfg.SurveyNr = $survey ";
                if ($sesPartner != 'ALL') {
                    $where .= " AND kcp.PartnerID = {$sesPartner} ";
                    $join = ' LEFT JOIN ktv_cpg_partner kcp ON kcf.CPGid = kcp.CPGid ';
                }

                $whereLatestSurvey = "";
                if ($LatestSurvey > 0 || $LatestSurvey === true) {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_ppiscore2012
                                            GROUP BY
                                                FarmerID
                                        ) z ON kcf.FarmerID = z.FarmerID
                                        LEFT JOIN ktv_ppiscore2012 kcfg ON z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr = kcfg.SurveyNr";
                } else {
                    $joinLatestSurvey = "INNER JOIN ktv_ppiscore2012 kcfg ON kcf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }

                $sql = "SELECT
                    ktv_province.Province,kd.District,
                    kcf.FarmerID,kcf.FarmerName,kcfg.SurveyNr,kcfg.InterviewDate,
                    kcfg.Score,kcfg.National,`1.25/day` as `PPI1`,`2.5/day` as `PPI2`
                  FROM
                    ktv_cocoa_farmer kcf
                    $joinLatestSurvey
                    INNER JOIN ktv_province ON SUBSTR(kcf.VillageID,1,2) = ktv_province.ProvinceID
                    INNER JOIN ktv_district kd ON SUBSTR(kcf.VillageID, 1, 4) = kd.DistrictID
                    $join
                    $where $whereLatestSurvey";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'GAP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten[0] == ' -- All --' || $kabupaten[0] == '') {
                    $where_kab = '';
                } else {
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kabupaten) . "'))";
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                Male,
                                Female,
                                MIN(Joined) AS Joined
                        FROM
                        (
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 1) THEN 1 ELSE 0 END) AS `Male`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 2) THEN 1 ELSE 0 END) AS `Female`,
                                MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((((`ktv_cpg_batch_trainings_farmers`
                                JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                            WHERE
                            (
                                (`ktv_cpg_trainings`.`CpgTrainings` = 'GaP good agriculture practices')
                                AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov}
                                {$where_kab}
                            )
                            GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`) UNION (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 1) THEN 1 ELSE 0 END) AS `Male`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 2) THEN 1 ELSE 0 END) AS `Female`,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                            (
                                (`ktv_cpg_trainings`.`CpgTrainings` = 'GaP good agriculture practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab}
                             )
                            GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                            HAVING MIN(Joined) = {$trainingYear}
                    ) x
                    GROUP BY Province,District,CpgTrainings,Joined";
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female"));
                break;
            case 'GFP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten[0] != ' -- All --') {
                    //$kab = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kabupaten) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        Province,
                        District,
                        CpgTrainings as Trainings,
                        Joined,
                        count(FarmerID) as Participants,
                        sum(Male) as Male,
                        sum(Female) as Female,
                        sum(Fam_Male) as 'Family Male',
                        sum(Fam_Female) as 'Family Female'
                        FROM
                        (
                        SELECT
                            Province,District,CpgTrainings,FarmerID,Male,Female, Fam_Male, Fam_Female, MIN(Joined) AS Joined
                        FROM
                        ((SELECT
                            `ktv_province`.`Province` AS `Province`,
                            `ktv_district`.`District` AS `District`,
                            `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                            `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                            (CASE
                                WHEN
                                    ktv_cocoa_farmer.Gender = 1
                                    AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1
                                THEN
                                    1
                                ELSE 0
                            END) AS Male,
                            (CASE
                                WHEN
                                    ktv_cocoa_farmer.Gender = 2
                                    AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1
                                THEN
                                    1
                                ELSE 0
                            END) AS Female,
                            (CASE
                                WHEN
                                    ktv_cpg_batch_trainings_farmers.PetaniKakao = 2
                                    AND Anggotagender = 1
                                THEN
                                    1
                                ELSE 0
                            END) AS Fam_male,
                            (CASE
                                WHEN
                                    ktv_cpg_batch_trainings_farmers.PetaniKakao = 2
                                    AND Anggotagender = 2
                                THEN
                                    1
                                ELSE 0
                            END) AS Fam_female,
                            MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                        FROM
                            ((((((((`ktv_cpg_batch_trainings_farmers`
                            JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                            JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                            JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                            LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                            JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                            JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                            JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                        WHERE
                            ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                            AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                            {$where_prov} {$where_kab}
                        )
                        GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`) UNION (SELECT
                            `ktv_province`.`Province` AS `Province`,
                            `ktv_district`.`District` AS `District`,
                            `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                            `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                        (CASE
                            WHEN
                                ktv_cocoa_farmer.Gender = 1
                                AND ktv_kader_trainings_participants.PetaniKakao = 1
                            THEN
                                1
                        END) AS Male,
                        (CASE
                            WHEN
                                ktv_cocoa_farmer.Gender = 2
                                AND ktv_kader_trainings_participants.PetaniKakao = 1
                            THEN
                                1
                        END) AS Female,
                        (CASE
                            WHEN ktv_family.AnggotaGender = 1 THEN 1
                        END) AS Fam_male,
                        (CASE
                            WHEN ktv_family.AnggotaGender = 2 THEN 1
                        END) AS Fam_female,
                        MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                    FROM
                        ((((((`ktv_kader_trainings`
                        JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                        JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                        JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                        JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                        JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                        LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                    WHERE
                    (
                        (`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                        AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                        {$where_prov} {$where_kab}
                    )
                    GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                ) base
                $join
                $where2
                GROUP BY
                    Province,District,CpgTrainings,FarmerID
                    HAVING MIN(Joined) = {$trainingYear}
                ) x
                GROUP BY
                Province,District,CpgTrainings,Joined";
                $query = $this->db->query($sql);
                break;
            case 'GNP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten[0] == ' -- All --' || $kabupaten[0] == '') {
                    $where_kab = '';
                } else {
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kabupaten) . "'))";
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            )
                            UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`
                        )) base
                        $join
                        $where2
                    GROUP BY
                        Province,District,CpgTrainings,FarmerID
                        HAVING MIN(Joined) = {$trainingYear}
                    ) x
                    GROUP BY
                        Province,District,CpgTrainings,Joined";
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as 'Familiy Male',sum(Fam_Female) as 'Familiy Female'"));
                break;
            case 'Cumulative GAP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten[0] == ' -- All --' || $kabupaten[0] == '') {
                    $where_kab = '';
                } else {
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kabupaten) . "'))";
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GAP good agriculture practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab}
                                    )
                                GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`
                                ) UNION
                                (SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                    MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((`ktv_kader_trainings`
                                    JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                    JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GAP good agriculture practices')
                                    AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab}
                                    )
                                GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                            ) base
                            $join
                            $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                        ) x
                    GROUP BY
                        Province,District,CpgTrainings,Joined";
                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female"));
                //$query = $this->db->query($sql);
                break;
            case 'Cumulative GFP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten[0] != ' -- All --') {
                    //$kab = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kabupaten) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                Province,
                District,
                CpgTrainings as Trainings,
                Joined,
                count(FarmerID) as Participants,
                sum(Male) as Male,
                sum(Female) as Female,
                sum(Fam_Male) as 'Family Male',
                sum(Fam_Female) as 'Family Female'
                FROM
                (SELECT
                Province,District,CpgTrainings,FarmerID,Male,Female, Fam_Male, Fam_Female, MIN(Joined) AS Joined
                FROM
                ((SELECT
                `ktv_province`.`Province` AS `Province`,
                `ktv_district`.`District` AS `District`,
                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                (CASE
                WHEN
                ktv_cocoa_farmer.Gender = 1
                AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1
                THEN 1
                ELSE 0
                END) AS Male,
                (CASE
                WHEN
                ktv_cocoa_farmer.Gender = 2
                AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1
                THEN 1
                ELSE 0
                END) AS Female,
                (CASE
                WHEN
                ktv_cpg_batch_trainings_farmers.PetaniKakao = 2
                AND Anggotagender = 1
                THEN 1
                ELSE 0
                END) AS Fam_male,
                (CASE
                WHEN
                ktv_cpg_batch_trainings_farmers.PetaniKakao = 2
                AND Anggotagender = 2
                THEN 1
                ELSE 0
                END) AS Fam_female,
                MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                FROM
                ((((((((`ktv_cpg_batch_trainings_farmers`
                JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                WHERE
                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                {$where_prov} {$where_kab}
                )
                GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`) UNION (SELECT
                `ktv_province`.`Province` AS `Province`,
                `ktv_district`.`District` AS `District`,
                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                (CASE
                WHEN
                ktv_cocoa_farmer.Gender = 1
                AND ktv_kader_trainings_participants.PetaniKakao = 1
                THEN 1
                END) AS Male,
                (CASE
                WHEN
                ktv_cocoa_farmer.Gender = 2
                AND ktv_kader_trainings_participants.PetaniKakao = 1
                THEN 1
                END) AS Female,
                (CASE
                WHEN ktv_family.AnggotaGender = 1 THEN 1
                END) AS Fam_male,
                (CASE
                WHEN ktv_family.AnggotaGender = 2 THEN 1
                END) AS Fam_female,
                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                FROM
                ((((((`ktv_kader_trainings`
                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                WHERE
                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                {$where_prov} {$where_kab}
                )
                GROUP BY `ktv_province`.`Province` , `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                ) base
                $join
                $where2
                GROUP BY
                Province,District,CpgTrainings,FarmerID
                ) x
                GROUP BY
                Province,District,CpgTrainings,Joined";
                $query = $this->db->query($sql);
                break;
            case 'Cumulative GNP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten[0] == ' -- All --' || $kabupaten[0] == '') {
                    $where_kab = '';
                } else {
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kabupaten) . "'))";
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                     AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` , `ktv_cpg_trainings`.`CpgTrainings` , `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                    ) x
                GROUP BY
                    Province,District,CpgTrainings,Joined";
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings as Trainings,Joined,count(FarmerID) as Participants,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as 'Family Male',sum(Fam_Female) as 'Family Female'"));
                break;
            case 'Certification':
                if ($kabupaten[0] != ' -- All --') {
                    $where_kab = "AND f.District IN ('" . implode("','", $kabupaten) . "') ";
                }

                if ($provinsi != ' -- All --') {
                    $where = "WHERE e.Province = ? $where_kab ";
                }

                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner j ON d.CPGid = j.CPGid ";
                    $where .= "AND j.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner j ON d.CPGid = j.CPGid ";
                    $where .= "AND j.PartnerID = {$sesPartner} ";
                }
                if ($trainingYear > 0) {
                    $where .= " AND YEAR(ExternalDate) = {$trainingYear} ";
                }

                if ($certificationType > 0) {
                    $where .= " AND c.Certification = {$certificationType} ";
                }
                $sql = "SELECT
                            a.FarmerID,
                            d.FarmerName,
                            IF(d.Gender = '1','Pria','Wanita') as Gender,
                            concat(e.Province,'/',f.District,'/',g.SubDistrict) as Location,
                            h.TotalTrees,
                            h.TotalHa,
                            h.Kg,
                            h.KgHa as 'Kg/Ha',
                            h.ShadeTrees,
                            h.ShadeTreesHa as 'Shades Tress/Ha',
                            IF(h.Herbisida = '1',CONCAT('Ya',h.MerkHerbisida),'Tidak') as Herbisida,
                            IF(h.Insectisida = '1',CONCAT('Ya',h.MerkInsectisida),'Tidak') as Insectisida,
                            IF(h.Fungisida = '1',CONCAT('Ya',h.MerkFungisida),'Tidak') as Fungisida,
                            c.CandidateSelection,
                            c.CertificationStart,
                            c.CertificationEnd,
                            a.DateCreated as 'Internal Audit',
                            CASE
                                WHEN a.StatusAudit=1
                                THEN 'Lolos'
                                WHEN a.StatusAudit=2
                                THEN 'Tidak Lolos'
                                ELSE ''
                            END as StatusAudit,
                            a.CommentAudit,
                            c.ExternalDate
                        FROM
                            ktv_cocoa_certification_audit_log a
                            JOIN (
                                SELECT
                                    FarmerID,
                                    MAX(DateCreated) as DateCreated
                                FROM
                                    ktv_cocoa_certification_audit_log
                                GROUP BY
                                    FarmerID
                            ) b ON a.FarmerID = b.FarmerID AND a.DateCreated = b.DateCreated
                            LEFT JOIN ktv_cocoa_certification c ON a.FarmerID = c.FarmerID AND a.SurveyNr = c.SurveyNr
                            LEFT JOIN ktv_cocoa_farmer d ON b.FarmerID = d.FarmerID
                            LEFT JOIN ktv_province e ON SUBSTR(d.VillageID, 1, 2) = e.ProvinceID
                            LEFT JOIN ktv_district f ON SUBSTR(d.VillageID, 1, 4) = f.DistrictID
                            LEFT JOIN ktv_subdistrict g ON SUBSTR(d.VillageID, 1, 7) = g.SubDistrictID
                            INNER JOIN (
                                SELECT
                                    FarmerID,
                                    MAX(SurveyNr) LatestSurveyNr
                                FROM
                                    ktv_cocoa_farmer_garden
                                GROUP BY FarmerID
                            ) i ON d.FarmerID = i.FarmerID
                            INNER JOIN (
                                SELECT
                                    FarmerID,
                                    SurveyNr,
                                    (PohonTBM + PohonTM + PohonRehab) as TotalTrees,
                                    GardenHaUncertified as TotalHa,
                                    ROUND(
                                        (
                                            IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
                                        ) + (
                                            IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
                                        ) + (
                                            IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
                                        )
                                    ) as Kg,
                                    ROUND(
                                        (
                                            IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
                                        ) + (
                                            IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
                                        ) + (
                                            IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
                                        )
                                    )/GardenHaUncertified as KgHa,
                                    ShadeTreesNr as ShadeTrees,
                                    ROUND(ShadeTreesNr/GardenHaUncertified) as ShadeTreesHa,
                                    Herbisida,
                                    CASE
                                        WHEN Herbisida1 > 0
                                            THEN ' - Round Up'
                                        WHEN Herbisida2 > 0
                                            THEN ' - Basmilang'
                                        WHEN Herbisida3 > 0
                                            THEN ' - Pilar Up'
                                        WHEN Herbisida4 > 0
                                            THEN ' - Sun Up'
                                        WHEN Herbisida5 > 0
                                            THEN ' - Gramoxone'
                                        WHEN Herbisida6 > 0
                                            THEN ' - Supremo'
                                        WHEN Herbisida7 > 0
                                            THEN ' - Sapurata'
                                        WHEN Herbisida8 > 0
                                            THEN ' - Rambo'
                                        WHEN Herbisida9 > 0
                                            THEN ' - Para Special'
                                        WHEN Herbisida10 > 0
                                            THEN ' - Noxone'
                                        WHEN MerekHerbisida <> '' OR MerekHerbisida > 0
                                            THEN CONCAT(' - ',MerekHerbisida)
                                        ELSE ''
                                    END as MerkHerbisida,
                                    Insectisida,
                                    CASE
                                        WHEN Insectisida1 > 0
                                            THEN ' - Alika'
                                        WHEN Insectisida2 > 0
                                            THEN ' - Matador'
                                        WHEN Insectisida3 > 0
                                            THEN ' - Capture'
                                        WHEN Insectisida4 > 0
                                            THEN ' - Bento'
                                        WHEN Insectisida5 > 0
                                            THEN ' - Regent'
                                        WHEN Insectisida6 > 0
                                            THEN ' - Drusban'
                                        WHEN Insectisida7 > 0
                                            THEN ' - Penalti'
                                        WHEN Insectisida8 > 0
                                            THEN ' - Nurelle'
                                        WHEN Insectisida9 > 0
                                            THEN ' - Cloromit'
                                        WHEN Insectisida10 > 0
                                            THEN ' - Decis'
                                        WHEN Insectisida11 > 0
                                            THEN ' - Organik'
                                        WHEN MerekInsectisida <> '' OR MerekInsectisida > 0
                                            THEN CONCAT(' - ',MerekInsectisida)
                                        ELSE ''
                                    END as MerkInsectisida,
                                    Fungisida,
                                    CASE
                                        WHEN Fungisida1 > 0
                                            THEN ' - Nordox'
                                        WHEN Fungisida2 > 0
                                            THEN ' - Dithane'
                                        WHEN Fungisida3 > 0
                                            THEN ' - Amistartop'
                                        WHEN Fungisida4 > 0
                                            THEN ' - Scorpio'
                                        WHEN Fungisida5 > 0
                                            THEN ' - Rhidomil'
                                        WHEN Fungisida6 > 0
                                            THEN ' - Antila'
                                        WHEN Fungisida7 > 0
                                            THEN ' - Antracol'
                                        WHEN Fungisida8 > 0
                                            THEN ' - Capture'
                                        WHEN Fungisida9 > 0
                                            THEN ' - Polidor'
                                        WHEN Fungisida10 > 0
                                            THEN ' - Cozeb'
                                        WHEN Fungisida11 > 0
                                            THEN ' - Organik'
                                        WHEN MerekFungisida <> '' OR MerekFungisida > 0
                                            THEN CONCAT(' - ',MerekFungisida)
                                        ELSE ''
                                    END as MerkFungisida
                                FROM
                                    ktv_cocoa_farmer_garden
                            ) h ON i.FarmerID = h.FarmerID AND i.LatestSurveyNr=h.SurveyNr
                            {$join}
                            {$where}
                        ORDER BY
                            a.FarmerID";
                $query = $this->db->query($sql, array($provinsi));
                break;
        } // end switch
        $result = $query->result_array();
        return $result;
    }

    // $provinsi,$kabupaten,$jenis,$survey,$trainingYear,
    public function readChart($provinsi, $kabupaten, $jenis, $survey, $trainingYear, $CertificationType = '', $LatestSurvey, $sesPartner)
    {
        switch ($jenis) {
            case 'Nutrition Summary':
                $where = '';
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND a.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where .= "WHERE a.Province = ? $where_kab ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner f ON kcf.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner f ON kcf.CPGid = f.CPGid ";
                    $where .= "WHERE f.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            a.Province,
                            a.District,
                            COUNT(a.FarmerID) as total,
                            sum(IF(kelamin=1,1,0)) Male,
                            sum(IF(kelamin=2,1,0)) Female,
                            sum(IF((kelamin not in (1,2) || kelamin is null),1,0)) other
                        FROM (
                            SELECT
                                kprov.Province,
                                kdis.District,
                                ksubdis.SubDistrict,
                                kcf.FarmerID,
                                if(kcbtf.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
                            FROM
                                ktv_cpg_batch_trainings kcbt
                                INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID = kcbtf.CpgBatchTrainingID
                                LEFT JOIN ktv_cocoa_farmer kcf ON kcbtf.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_family kf ON kcbtf.FamilyID = kf.FamilyID
                                LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID,1,2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID,1,4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kcf.VillageID,1,7) = ksubdis.SubDistrictID
                            WHERE kcbt.CPGtrainingsID=2
                            UNION ALL
                            SELECT
                                kprov.Province,
                                kdis.District,
                                ksubdis.SubDistrict,
                                kcf.FarmerID,
                                if(kktp.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
                            FROM
                                ktv_kader_trainings kkt
                                INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
                                LEFT JOIN ktv_cocoa_farmer kcf ON kktp.FarmerID = kcf.FarmerID
                                LEFT JOIN ktv_family kf ON kktp.FamilyID = kf.FamilyID
                                LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID,1,2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID,1,4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksubdis ON SUBSTR(kcf.VillageID,1,7) = ksubdis.SubDistrictID
                            WHERE kkt.CPGtrainingsID=2
                        ) a
                        LEFT JOIN ktv_cocoa_farmer kcf ON a.FarmerID = kcf.FarmerID
                        {$join}
                        {$where}
                    GROUP BY
                        a.Province,
                        a.District";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'Certification Summary':
                if ($trainingYear > 0) {
                    $where = " YEAR(kcc.ExternalDate)={$trainingYear} ";
                } else {
                    $where = " kcc.ExternalDate>'0000-00-00' ";
                }
                if ($CertificationType > 0) {
                    $where .= " AND kcc.Certification={$CertificationType} ";
                }
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where .= "AND kprov.Province = ? $where_kab ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner f ON kcf.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner f ON kcf.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            kprov.Province,
                            kdis.District,
                            count(kcc.FarmerID) as totalcert,
                            sum(IF(kcf.Gender=1,1,0)) Male,
                            sum(IF(kcf.Gender=2,1,0)) Female
                        FROM (
                            SELECT
                                FarmerID,
                                Certification,
                                ExternalDate
                            FROM
                                ktv_cocoa_certification
                            GROUP BY FarmerID
                            ) kcc
                            LEFT JOIN ktv_cocoa_farmer kcf on kcc.FarmerID=kcf.FarmerID
                            LEFT JOIN ktv_province kprov ON SUBSTR(kcf.VillageID, 1, 2) = kprov.ProvinceID
                            LEFT JOIN ktv_district kdis ON SUBSTR(kcf.VillageID, 1, 4) = kdis.DistrictID
                            {$join}
                        WHERE
                            {$where}
                        GROUP BY
                            kprov.Province,
                            kdis.District";
                $query = $this->db->query($sql, array($provinsi));
                break;
            case 'GAP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 1) THEN 1 ELSE 0 END) AS `Male`,
                                    (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 2) THEN 1 ELSE 0 END) AS `Female`,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    (
                                    (`ktv_cpg_trainings`.`CpgTrainingsID` = '1')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov}
                                    {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 1) THEN 1 ELSE 0 END) AS `Male`,
                                (CASE WHEN (`ktv_cocoa_farmer`.`Gender` = 2) THEN 1 ELSE 0 END) AS `Female`,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                (
                                (`ktv_cpg_trainings`.`CpgTrainingsID` = '1')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                            HAVING MIN(Joined) = {$trainingYear}
                    ) x
                    GROUP BY Province,District,CpgTrainings,Joined";
                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings,Joined,count(FarmerID) as FarmerID,sum(Male) as Male,sum(Female) as Female"));
                break;
            case 'GFP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                            %s
                        FROM
                        (
                            SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                            HAVING MIN(Joined) = {$trainingYear}
                    ) x
                    GROUP BY
                        Province,District,CpgTrainings,Joined";

                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings,Joined,count(FarmerID) as FarmerID,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as Fam_Male,sum(Fam_Female) as Fam_Female"));
                break;
            case 'GNP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                        GROUP BY
                            Province,District,CpgTrainings,FarmerID
                            HAVING MIN(Joined) = {$trainingYear}
                    ) x
                GROUP BY
                    Province,District,CpgTrainings,Joined";

                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings,Joined,count(FarmerID) as FarmerID,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as Fam_Male,sum(Fam_Female) as Fam_Female"));
                break;
            case 'Cumulative GAP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((((`ktv_cpg_batch_trainings_farmers`
                                JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GAP good agriculture practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab}
                                )
                            GROUP BY
                                `ktv_province`.`Province`,
                                `ktv_district`.`District`,
                                `ktv_cpg_trainings`.`CpgTrainings`,
                                `ktv_cocoa_farmer`.`FarmerID`
                        ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GAP good agriculture practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province`,
                                `ktv_district`.`District`,
                                `ktv_cpg_trainings`.`CpgTrainings`,
                                `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                    GROUP BY
                        Province,District,CpgTrainings,FarmerID
                ) x
                GROUP BY
                Province,District,CpgTrainings,Joined";

                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings,Joined,count(FarmerID) as FarmerID,sum(Male) as Male,sum(Female) as Female"));
                break;
            case 'Cumulative GFP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GfP good financial practice')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`)
                        ) base
                        $join
                        $where2
                    GROUP BY
                        Province,District,CpgTrainings,FarmerID
                ) x
                GROUP BY
                Province,District,CpgTrainings,Joined";

                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings,Joined,count(FarmerID) as FarmerID,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as Fam_Male,sum(Fam_Female) as Fam_Female"));
                break;
            case 'Cumulative GNP Participants':
                $trainingDate = "{$trainingYear}-12-31 00:00:00";
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND (`ktv_district`.District IN ('" . implode("','", $kab) . "'))";
                } else {
                    $where_kab = '';
                }
                if ($provinsi != ' -- All --') {
                    $where_prov = "AND (`ktv_province`.Province = '{$provinsi}')";
                }

                $join   = '';
                $where2 = '';
                if ($sesPartner != 'ALL') {
                    $join   = "LEFT JOIN ktv_cpg_partner y ON base.CPGid = y.CPGid ";
                    $where2 = "WHERE y.PartnerID = {$sesPartner} ";
                }
                $sql = "SELECT
                        %s
                        FROM
                            (SELECT
                                Province,
                                District,
                                CpgTrainings,
                                FarmerID,
                                base.CPGid,
                                Male,
                                Female,
                                Fam_Male,
                                Fam_Female,
                                MIN(Joined) AS Joined
                            FROM
                                ((SELECT
                                    `ktv_province`.`Province` AS `Province`,
                                    `ktv_district`.`District` AS `District`,
                                    `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                    `ktv_cocoa_farmer`.`FarmerID` AS `FarmerID`,
                                    `ktv_cocoa_farmer`.`CPGid`,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Male,
                                    (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_cpg_batch_trainings_farmers.PetaniKakao = 1 THEN 1 ELSE 0 END) AS Female,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 1 THEN 1 ELSE 0 END) AS Fam_male,
                                    (CASE WHEN ktv_cpg_batch_trainings_farmers.PetaniKakao = 2 AND Anggotagender = 2 THEN 1 ELSE 0 END) AS Fam_female,
                                    MIN(YEAR(`ktv_cpg_batch_trainings`.`TrainingStart`)) AS `Joined`
                                FROM
                                    ((((((((`ktv_cpg_batch_trainings_farmers`
                                    JOIN `ktv_cpg_batch_trainings` ON ((`ktv_cpg_batch_trainings_farmers`.`CpgBatchTrainingID` = `ktv_cpg_batch_trainings`.`CpgBatchTrainingID`)))
                                    JOIN `ktv_cocoa_farmer` ON ((`ktv_cpg_batch_trainings_farmers`.`FarmerID` = `ktv_cocoa_farmer`.`FarmerID`)))
                                    JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_batch_trainings`.`CPGtrainingsID` = `ktv_cpg_trainings`.`CpgTrainingsID`)))
                                    LEFT JOIN `ktv_family` ON ((`ktv_cpg_batch_trainings_farmers`.`FamilyID` = `ktv_family`.`FamilyID`)))
                                    JOIN `ktv_village` ON ((`ktv_cocoa_farmer`.`VillageID` = `ktv_village`.`VillageID`)))
                                    JOIN `ktv_subdistrict` ON ((`ktv_village`.`SubDistrictID` = `ktv_subdistrict`.`SubDistrictID`)))
                                    JOIN `ktv_district` ON ((`ktv_subdistrict`.`DistrictID` = `ktv_district`.`DistrictID`)))
                                    JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_district`.`ProvinceID`)))
                                WHERE
                                    ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                    AND (`ktv_cpg_batch_trainings`.`TrainingStart` <= '{$trainingDate}')
                                    {$where_prov} {$where_kab})
                                GROUP BY
                                    `ktv_province`.`Province` ,
                                    `ktv_district`.`District` ,
                                    `ktv_cpg_trainings`.`CpgTrainings` ,
                                    `ktv_cocoa_farmer`.`FarmerID`
                            ) UNION
                            (SELECT
                                `ktv_province`.`Province` AS `Province`,
                                `ktv_district`.`District` AS `District`,
                                `ktv_cpg_trainings`.`CpgTrainings` AS `CpgTrainings`,
                                `ktv_kader_trainings_participants`.`FarmerID` AS `FarmerID`,
                                `ktv_cocoa_farmer`.`CPGid`,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 1 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Male,
                                (CASE WHEN ktv_cocoa_farmer.Gender = 2 AND ktv_kader_trainings_participants.PetaniKakao = 1 THEN 1 END) AS Female,
                                (CASE WHEN ktv_family.AnggotaGender = 1 THEN 1 END) AS Fam_male,
                                (CASE WHEN ktv_family.AnggotaGender = 2 THEN 1 END) AS Fam_female,
                                MIN(YEAR(`ktv_kader_trainings`.`TrainingStart`)) AS `Joined`
                            FROM
                                ((((((`ktv_kader_trainings`
                                JOIN `ktv_kader_trainings_participants` ON ((`ktv_kader_trainings_participants`.`CpgKaderTrainingID` = `ktv_kader_trainings`.`CpgKaderTrainingID`)))
                                JOIN `ktv_cpg_trainings` ON ((`ktv_cpg_trainings`.`CpgTrainingsID` = `ktv_kader_trainings`.`CPGtrainingsID`)))
                                JOIN `ktv_cocoa_farmer` ON ((`ktv_cocoa_farmer`.`FarmerID` = `ktv_kader_trainings_participants`.`FarmerID`)))
                                JOIN `ktv_district` ON ((`ktv_district`.`DistrictID` = `ktv_kader_trainings`.`TrainingDistrict`)))
                                JOIN `ktv_province` ON ((`ktv_province`.`ProvinceID` = `ktv_kader_trainings`.`TrainingProvince`)))
                                LEFT JOIN `ktv_family` ON ((`ktv_kader_trainings_participants`.`FamilyID` = `ktv_family`.`FamilyID`)))
                            WHERE
                                ((`ktv_cpg_trainings`.`CpgTrainings` = 'GnP good nutrition practices')
                                AND (`ktv_kader_trainings`.`TrainingStart` <= '{$trainingDate}')
                                {$where_prov} {$where_kab})
                            GROUP BY
                                `ktv_province`.`Province` ,
                                `ktv_district`.`District` ,
                                `ktv_cpg_trainings`.`CpgTrainings` ,
                                `ktv_cocoa_farmer`.`FarmerID`
                        )) base
                        $join
                        $where2
                    GROUP BY
                        Province,District,CpgTrainings,FarmerID
                ) x
                GROUP BY
                    Province,District,CpgTrainings,Joined";

                // Province,District,CpgTrainings,FarmerID,Male,Female, MIN(Joined) AS Joined
                $query = $this->db->query(sprintf($sql, "Province,District,CpgTrainings,Joined,count(FarmerID) as FarmerID,sum(Male) as Male,sum(Female) as Female,sum(Fam_Male) as Fam_Male,sum(Fam_Female) as Fam_Female"));
                break;
            case 'Summary Garden Data':
                if ($kabupaten != ' -- All --') {
                    $kab       = explode(',', $kabupaten);
                    $where_kab = "AND kdis.District IN ('" . implode("','", $kab) . "') ";
                }
                if ($provinsi != ' -- All --') {
                    $where = "AND kprov.Province = ? $where_kab ";
                }
                if ($provinsi != ' -- All --' && $sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner f ON kf.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                    $where2 = "WHERE f.PartnerID = {$sesPartner} ";
                } elseif ($sesPartner != 'ALL') {
                    $join = "LEFT JOIN ktv_cpg_partner f ON kf.CPGid = f.CPGid ";
                    $where .= "AND f.PartnerID = {$sesPartner} ";
                    $where2 = "WHERE f.PartnerID = {$sesPartner} ";
                }
                /*
                if ($survey!=''){
                $where .= "AND kcfg.SurveyNr = $survey ";
                }
                 *
                 */
                $whereLatestSurvey = "";
                if ($LatestSurvey === 'true') {
                    $joinLatestSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                MAX(SurveyNr) LatestSurveyNr
                                            FROM
                                                ktv_cocoa_farmer_garden
                                            GROUP BY FarmerID
                                        ) z ON kf.FarmerID = z.FarmerID
                                        INNER JOIN ktv_cocoa_farmer_garden kcfg ON z.FarmerID = kcfg.FarmerID
                                        AND z.LatestSurveyNr=kcfg.SurveyNr ";
                } else {
                    $joinLatestSurvey = "LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kf.FarmerID = kcfg.FarmerID ";
                    if ($survey != '') {
                        $whereLatestSurvey = "AND kcfg.SurveyNr = {$survey} ";
                    }

                }
                $sql = 'SELECT
                            a.Province,
                            a.District,
                            b.TotalFarmer,
                            a.TBM,
                            a.TM,
                            a.TR,
                            a.TT as "Total Trees",
                            a.ST as "Shade Trees",
                            round(a.TH) as "Total Ha",
                            round(a.TPH) as "Tree/Ha",
                            round(a.Shade*100) as Shade,
                            round(a.PK) as "Production Kg",
                            round(a.KPH) as "Kg/Ha",
                            round(round(a.TH)/b.TotalFarmer,2) as "Ha/Farmer"
                        FROM (
                            SELECT
                                kprov.Province,kdis.District,SUM(kcfg.PohonTBM) TBM,SUM(kcfg.PohonTM) TM,
                                SUM(kcfg.PohonRehab) TR,
                                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS TT,
                                SUM(kcfg.ShadeTreesNr) AS ST,
                                SUM(kcfg.GardenHaUncertified) AS TH,
                                SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab)/SUM(kcfg.GardenHaUncertified) AS TPH,
                                SUM(kcfg.ShadeTreesNr)/SUM(kcfg.PohonTBM+ kcfg.PohonTM + kcfg.PohonRehab) AS "Shade",
                                SUM(
                                (
                                    IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                                )
                                ) AS PK,
                                SUM(
                                (
                                    IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
                                ) + (
                                    IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
                                )
                                )/SUM(kcfg.GardenHaUncertified) AS KPH,
                                kf.StatusCode,
                                kcfg.SurveyNr
                            FROM
                                ktv_cocoa_farmer kf
                                LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                                LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                                LEFT JOIN ktv_subdistrict ksub ON SUBSTR(kf.VillageID, 1, 7) = ksub.SubDistrictID
                                ' . $join . '
                                ' . $joinLatestSurvey . '
                                WHERE
                                    kf.StatusCode="active"
                                    ' . $where . '
                                    ' . $whereLatestSurvey . '
                                GROUP BY
                                    kprov.Province,
                                    kdis.District
                            ) a
                            LEFT JOIN (
                                SELECT kprov.Province,kdis.District,StatusCode,COUNT(kf.FarmerID) TotalFarmer
                                FROM
                                    ktv_cocoa_farmer kf
                                    LEFT JOIN ktv_province kprov ON SUBSTR(kf.VillageID, 1, 2) = kprov.ProvinceID
                                    LEFT JOIN ktv_district kdis ON SUBSTR(kf.VillageID, 1, 4) = kdis.DistrictID
                                    ' . $join . '
                                ' . $where2 . '
                                GROUP BY
                                    kprov.Province,
                                    kdis.District
                            ) b ON a.Province=b.Province and a.District=b.District and
                            a.StatusCode=b.StatusCode
                            ORDER BY a.Province';
                $query = $this->db->query($sql, array($provinsi));
                break;
        }
        $result = $query->result_array();
        return $result;
    }

    public function readProvinsis($sesPartner, $provinceids = '')
    {
        $join  = '';
        $where = '';
        if ($sesPartner != 'ALL') {
            $join .= ' LEFT JOIN ktv_cpg_partner b ON SUBSTR(b.CPGid,1,2) = a.ProvinceID';
            $where .= " AND b.PartnerID = {$sesPartner} ";
        }
        if ($provinceids) {
            $where .= " AND a.ProvinceID IN ($provinceids)";
        }
        $userid = $_SESSION['userid'];
        /*$sql = "SELECT
                    distinct a.Province as label,
                    a.ProvinceID as id
                FROM
                    ktv_province a
                    $join
                WHERE
                    a.ProvinceID not in (31)
                    $where
                ORDER BY a.Province";*/
        $sql = "SELECT
                    distinct a.Province as label,
                    a.ProvinceID as id
                FROM
                    ktv_province a
                    LEFT JOIN ktv_access_staff b ON SUBSTR(b.DistrictID,1,2)=a.ProvinceID
                WHERE
                    a.StatusCode='active' AND b.UserId=?

                ORDER BY a.Province";

        $query          = $this->db->query($sql, array($userid));
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        $result['data'] = $result['data'];
        return $result;
    }

    public function getWarehouseDetail($id)
    {
        $query = $this->db->get_where('ktv_warehouse', array('WarehouseID' => $id), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function readWarehouse($province)
    {
        //cek tipe user (private or program)
        /*
        $sql = "SELECT
                    kps.`PersonID` AS program_person_id,
                    kpvs.`PersonID` AS private_person_id
                FROM
                    ktv_persons kp
                    LEFT JOIN ktv_program_staff kps ON kp.`PersonID` = kps.`PersonID`
                    LEFT JOIN ktv_private_staff kpvs ON kp.`PersonID` = kpvs.`PersonID`
                WHERE
                    kp.`UserID` = ?
                LIMIT 1";
         */
        $sql = "SELECT a.PersonID, a.ObjID, a.ObjType
                FROM
                    ktv_staffs a
                    LEFT JOIN ktv_persons b ON b.PersonID=a.PersonID
                WHERE
                    b.UserID=?";
        $query    = $this->db->query($sql, array($_SESSION['userid']));
        $dataUser = $query->row_array();

        if ($dataUser['ObjType'] == "program") {
            $tipeUser = 'program';
        } elseif ($dataUser['ObjType'] == "private") {
            $tipeUser = 'private';
        } else {
            $tipeUser = 'adminOrOther';
        }

        switch ($tipeUser) {
            case 'private':
                /*$sql = "SELECT
                        kw.WarehouseID AS id,
                        kw.WarehouseName AS label
                    FROM
                        ktv_warehouse kw
                        LEFT JOIN ktv_program_partner kpp ON kw.`PartnerID` = kpp.`PartnerID`
                        LEFT JOIN ktv_private_staff kpvs ON kpp.`PartnerID` = kpvs.`PartnerID`
                        LEFT JOIN ktv_persons kperson   ON kpvs.`UserId` = kperson.`UserID`
                        LEFT JOIN sys_user su ON kperson.`UserID` = su.`UserId`
                    WHERE
                        SUBSTR(kw.VillageID,1,2) = {$province} AND
                        su.`UserId` = " . $_SESSION['userid'] . " AND
                        kw.`StatusCode` = 'active'
                    ";*/
                $sql = "SELECT DISTINCT
                        kw.WarehouseID AS id,
                        kw.WarehouseName AS label
                    FROM
                        ktv_warehouse kw
                    WHERE
                        kw.StatusCode='active' AND SUBSTR(kw.VillageID,1,2) = {$province} AND kw.PartnerID=?";
                $query = $this->db->query($sql, array($dataUser['ObjID']));
                break;
            default:
                $sql = "SELECT
                    WarehouseID as id,
                    WarehouseName as label
                FROM
                    ktv_warehouse
                WHERE
                    StatusCode='active' AND SUBSTR(VillageID,1,2) = {$province} AND
                    `StatusCode` = 'active'
                ";
                $query = $this->db->query($sql);
                break;
        }

        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    public function readKabupatens($prov, $partnerID)
    {
        if ($partnerID != 'ALL') {
            $sql_where2 = " AND DistrictID in (SELECT DistrictID FROM ktv_district_partner WHERE PartnerID = {$partnerID})";
        }

        //cek ktv_access_staff nya
        $sql_where=" AND a.DistrictID IN ({$_SESSION['daerah_access']}) ";

        $sql = "SELECT distinct District as label, DistrictID as id
                FROM ktv_district a
                    LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                WHERE
                    (a.ProvinceID = ? OR b.Province = ?) %s %s
                ORDER BY District";
        $query = $this->db->query(sprintf($sql, $sql_where, $sql_where2), array($prov, $prov));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --')), $result['data']);
        $result['data'] = $result['data'];
        return $result;
    }

    public function readKecamatans($prov, $partnerID)
    {
        $sql_where = "/*and DistrictID not in (1171,7373,7271,1377,7371)*/";
        if ($partnerID != 'ALL') {
            $sql_where2 = " AND DistrictID in (SELECT DistrictID FROM ktv_district_partner WHERE PartnerID = {$partnerID})";
        }
        $sql = "SELECT sd.`SubDistrictID` AS id, sd.`SubDistrict` AS label
FROM ktv_subdistrict sd
                WHERE sd.`DistrictID` = ? %s %s
                ORDER BY label";
        $query = $this->db->query(sprintf($sql, $sql_where, $sql_where2), array($prov, $prov));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result['data'] = $query->result_array();
        // $result['data'] = array_merge(array(array('label'=>' -- All --')),$result['data']);
        $result['data'] = $result['data'];
        return $result;
    }

    public function readDesas($sd){
        $sql = "SELECT
                    Village as label,
                    VIllageID as id
                FROM
                    ktv_village
                WHERE
                    SubDistrictID=?
                ORDER BY Village";
        $query          = $this->db->query($sql, array($sd));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readCpgs($district){
        $sql = "SELECT
                    GroupName as label,
                    CPGid as id
                FROM
                    ktv_cpg
                WHERE
                    SUBSTR(CPGid,1,4)=?
                ORDER BY GroupName";
        $query          = $this->db->query($sql, array($district));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readBuyingUnits($district){
        $sql = "SELECT
                    b.`Name` label,
                    b.SupplychainID id
                FROM
                    ktv_supplychain_area a
                    LEFT JOIN  view_supplychain_org b ON a.SupplychainID=b.SupplychainID
                WHERE a.DistrictID=? AND b.OrgType IN ('Pedagang', 'sce')
                ORDER BY label";
        $query          = $this->db->query($sql, array($district));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readProgress($prov, $kab, $jenis, $awal, $akhir)
    {
        if ($prov != '') {
            $sql_prov = "Province like '$prov' AND";
        }

        if ($kab[0] != '' and trim($kab[0]) != '-- All --') {
            $sql_kab = "District in ('" . implode("','", $kab) . "') AND";
        }

        $aw   = explode('-', $awal);
        $jd1  = GregorianToJD($aw[1], $aw[2], $aw[0]);
        $ak   = explode('-', $akhir);
        $jd2  = GregorianToJD($ak[1], $ak[2], $ak[0]);
        $days = $jd2 - $jd1 + 1;
        if ($days == 1) {
            $sql_j = 'HOUR';
        } elseif ($days < 8) {
            $sql_j = 'DATE';
        } elseif ($days < 32) {
            $sql_j = 'WEEK';
        } elseif ($days < 365) {
            $sql_j = 'MONTH';
        } else {
            $sql_j = 'YEAR';
        }

        $sql_periode = "
         select adddate('$awal',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) periode from
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
         limit $days";

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF(date(DateCreated)=periode,1,0)) as 'Petani baru',
            sum(IF(((date(DateUpdated)=periode) and (DateCreated!=DateUpdated OR DateCreated is null)),1,0)) as 'Ubah petani'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer ON FarmerID>0
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where $sql_prov $sql_kab periode between ? and ?
         group by $sql_j(periode)";

        $query            = $this->db->query($sql, array($awal, $akhir));
        $result['petani'] = $query->result_array();

        // Survey Garden baru
        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF(date(kcfg.DateCreated)=periode,1,0)) as 'Garden baru',
            sum(IF(((date(kcfg.DateUpdated)=periode) and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah garden'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcfg.FarmerID>0
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where $sql_prov $sql_kab periode between ? and ?
         group by $sql_j(periode)";
        $query = $this->db->query($sql, array($awal, $akhir));
        //echo $sql;exit;
        $result['garden'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF(date(kcfg.DateCreated)=periode,1,0)) as 'Harvest baru',
            sum(IF(((date(kcfg.DateUpdated)=periode) and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah harvest'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer_post_harvest kcfg ON kcfg.FarmerID>0
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where $sql_prov $sql_kab periode between ? and ?
         group by $sql_j(periode)";
        $query          = $this->db->query($sql, array($awal, $akhir));
        $result['post'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF(date(kcfg.DateCreated)=periode,1,0)) as 'Nutrition baru',
            sum(IF(((date(kcfg.DateUpdated)=periode) and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah nutrition'
         from ($sql_periode) v
         LEFT JOIN ktv_nutrition kcfg ON kcfg.FarmerID>0
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where $sql_prov $sql_kab periode between ? and ?
         group by $sql_j(periode)";
        $query               = $this->db->query($sql, array($awal, $akhir));
        $result['nutrition'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF(date(kcfg.DateCreated)=periode,1,0)) as 'PPI baru',
            sum(IF(((date(kcfg.DateUpdated)=periode) and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah PPI'
         from ($sql_periode) v
         LEFT JOIN ktv_ppiscore2012 kcfg ON kcfg.FarmerID>0
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where $sql_prov $sql_kab periode between ? and ?
         group by $sql_j(periode)";
        $query         = $this->db->query($sql, array($awal, $akhir));
        $result['ppi'] = $query->result_array();
        //print_r($result);exit;

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF(date(kcfg.DateCreated)=periode,1,0)) as 'Finance baru',
            sum(IF(((date(kcfg.DateUpdated)=periode) and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah finance'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer_financial kcfg ON kcfg.FarmerID>0
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where $sql_prov $sql_kab periode between ? and ?
         group by $sql_j(periode)";
        $query             = $this->db->query($sql, array($awal, $akhir));
        $result['finance'] = $query->result_array();

        //environment
        $sql="SELECT
                $sql_j(periode) AS Periode,
                SUM(IF(DATE(envi.DateCreated)=periode,1,0)) AS 'Environment baru',
                SUM(IF(((DATE(envi.DateUpdated)=periode) AND (envi.DateCreated!=envi.DateUpdated OR envi.DateCreated IS NULL)),1,0)) AS 'Ubah environment'
            FROM
                ($sql_periode) v
                LEFT JOIN ktv_environment envi ON envi.`FarmerID` > 0
                LEFT JOIN ktv_cocoa_farmer farmer ON envi.`FarmerID` = farmer.`FarmerID`
                LEFT JOIN ktv_province pro ON pro.`ProvinceID` = SUBSTR(farmer.`VillageID`,1,2)
                LEFT JOIN ktv_district dis ON dis.`DistrictID` = SUBSTR(farmer.`VillageID`,1,4)
            WHERE
                $sql_prov $sql_kab
                periode between ? and ?
            GROUP BY $sql_j(periode)";
        $query = $this->db->query($sql, array($awal, $akhir));
        $result['environment'] = $query->result_array();

        // village
        $sql = "
         SELECT
            Periode,
            SUM(baru) AS 'Village baru',
            SUM(ubah) AS 'Ubah village'
         FROM (
             SELECT $sql_j(periode) as Periode,
                SUM(IF(DATE(kv.DateCreated)=periode,1,0)) AS baru,
                SUM(IF(((DATE(kv.DateUpdated)=periode) AND (kv.DateCreated!=kv.DateUpdated OR kv.DateCreated IS NULL))
                ,1,0)) AS ubah
             from ($sql_periode) v
             LEFT JOIN ktv_village kv ON 1 = 1
             LEFT JOIN ktv_province ON ProvinceID=substr(kv.VillageID,1,2)
             LEFT JOIN ktv_district ON DistrictID=substr(kv.VillageID,1,4)
             WHERE $sql_prov $sql_kab periode between ? and ?
             GROUP by $sql_j(periode)
             UNION ALL
             SELECT $sql_j(periode) as Periode,
                SUM(IF(DATE(vc.DateCreated)=periode,1,0)) AS baru,
                SUM(IF(((DATE(vc.DateUpdated)=periode) AND (vc.DateCreated!=vc.DateUpdated OR vc.DateCreated IS NULL))
                ,1,0)) AS ubah
             from ($sql_periode) v
             LEFT JOIN ktv_village_crop vc ON 1 = 1
             LEFT JOIN ktv_village kv ON vc.VillageID = kv.VillageID
             LEFT JOIN ktv_province ON ProvinceID=substr(kv.VillageID,1,2)
             LEFT JOIN ktv_district ON DistrictID=substr(kv.VillageID,1,4)
             WHERE $sql_prov $sql_kab periode between ? and ?
             GROUP by $sql_j(periode)
             UNION ALL
             SELECT $sql_j(periode) as Periode,
                SUM(IF(DATE(vi.DateCreated)=periode,1,0)) AS baru,
                SUM(IF(((DATE(vi.DateUpdated)=periode) AND (vi.DateCreated!=vi.DateUpdated OR vi.DateCreated IS NULL))
                ,1,0)) AS ubah
             from ($sql_periode) v
             LEFT JOIN ktv_village_infrastructure vi ON 1 = 1
             LEFT JOIN ktv_village kv ON vi.VillageID = kv.VillageID
             LEFT JOIN ktv_province ON ProvinceID=substr(kv.VillageID,1,2)
             LEFT JOIN ktv_district ON DistrictID=substr(kv.VillageID,1,4)
             WHERE $sql_prov $sql_kab periode between ? and ?
             GROUP by $sql_j(periode)
         )r
         GROUP BY Periode";

        $query = $this->db->query($sql, array($awal, $akhir, $awal, $akhir, $awal, $akhir));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['village'] = $query->result_array();

        return $result;
    }

    public function readProgresDetails($c, $r, $prov, $kab, $awal, $akhir)
    {
        $kab   = explode(',', $kab);
        $aww   = explode('T', $awal);
        $awal  = $aww[0];
        $akk   = explode('T', $akhir);
        $akhir = $akk[0];
        if ($prov != '') {
            $sql_prov = "Province like '$prov' AND";
        }

        if ($kab[0] != '' and trim($kab[0]) != '-- All --') {
            $sql_kab = "District in ('" . implode("','", $kab) . "') AND";
        }

        $aw   = explode('-', $awal);
        $jd1  = GregorianToJD($aw[1], $aw[2], $aw[0]);
        $ak   = explode('-', $akhir);
        $jd2  = GregorianToJD($ak[1], $ak[2], $ak[0]);
        $days = $jd2 - $jd1 + 1;
        if ($days == 1) {
            $sql_j = 'HOUR';
        } elseif ($days < 8) {
            $sql_j = 'DATE';
        } elseif ($days < 32) {
            $sql_j = 'WEEK';
        } elseif ($days < 365) {
            $sql_j = 'MONTH';
        } else {
            $sql_j = 'YEAR';
        }

        $sql_periode = "
         select adddate('$awal',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) periode from
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
         limit $days";
        $sql = "
         select $sql_j(periode) as Periode,group_concat(periode order by periode separator \"','\") as tgl
         from ($sql_periode) v
         where periode between ? and ?
         group by $sql_j(periode)";
        $query   = $this->db->query($sql, array($awal, $akhir));
        $periode = $query->result_array();

        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah harvest',
            'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance', 'Village baru', 'Ubah village');
        if ($c == '1') {
            $sql = "
            select FarmerID,FarmerName, UserRealName as oleh,DateCreated as waktu
            from ktv_cocoa_farmer
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=CreatedBy
            where $sql_prov $sql_kab date(DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '2') {
            $sql = "
            select FarmerID,FarmerName, UserRealName as oleh,DateUpdated as waktu
            from ktv_cocoa_farmer
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=LastModifiedBy
            where $sql_prov $sql_kab date(DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '3') {
            $sql = "
            select GardenNr,SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_cocoa_farmer_garden kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where $sql_prov $sql_kab date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '4') {
            $sql = "
            select GardenNr,SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_cocoa_farmer_garden kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where $sql_prov $sql_kab date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '5') {
            $sql = "
            select SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_cocoa_farmer_post_harvest kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where $sql_prov $sql_kab date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '6') {
            $sql = "
            select SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_cocoa_farmer_post_harvest kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where $sql_prov $sql_kab date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '7') {
            $sql = "
            select SurveyNr,InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_nutrition kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where $sql_prov $sql_kab date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '8') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_nutrition kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where $sql_prov $sql_kab date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '9') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_ppiscore2012 kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where $sql_prov $sql_kab date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '10') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_ppiscore2012 kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where $sql_prov $sql_kab date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '11') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_cocoa_farmer_financial kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where $sql_prov $sql_kab date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '12') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_cocoa_farmer_financial kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where $sql_prov $sql_kab date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '13') {
            $sql = "
        SELECT * FROM (
            select 'Village' AS `Object`, Village AS `Name`,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_village kcfg
            LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
            LEFT JOIN sys_user ON (UserID=kcfg.CreatedBy)
            WHERE $sql_prov $sql_kab (
                date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            )
            UNION ALL
            select
                'Village Crop' AS `Object`,
                CONCAT(Village, ' : ',
                    CASE vc.CropName
                       WHEN 1 THEN 'Kakao'
                       WHEN 2 THEN 'Jagung'
                       WHEN 3 THEN 'Sawit'
                       WHEN 4 THEN 'Karet'
                       WHEN 5 THEN 'Cengkeh'
                       WHEN 6 THEN 'Padi'
                       WHEN 7 THEN 'Buah-buahan'
                       WHEN 8 THEN 'Kayu-kayuan'
                    END
                ) AS Name,
                UserRealName as oleh,vc.DateCreated as waktu
            FROM ktv_village_crop vc
            JOIN ktv_village kcfg ON vc.VillageID = kcfg.VillageID
            LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
            LEFT JOIN sys_user ON (UserID=vc.CreatedBy)
            where $sql_prov $sql_kab (
                date(vc.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            )
            UNION ALL
            select
                'Village Infrastructure' AS `Object`,
                CONCAT(Village, ' : ', vi.InfrastructureName) AS Name,
                UserRealName as oleh,vi.DateCreated as waktu
            FROM ktv_village_infrastructure vi
            JOIN ktv_village kcfg ON vi.VillageID = kcfg.VillageID
            LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
            LEFT JOIN sys_user ON (UserID=vi.CreatedBy)
            where $sql_prov $sql_kab (
                date(vi.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            )
        ) r
            order by waktu";
        } elseif ($c == '14') {
            $sql = "
            SELECT * FROM (
                select
                    'Village' AS `Object`,
                    Village AS Name,
                    UserRealName as oleh,kcfg.DateUpdated as waktu
                FROM ktv_village kcfg
                LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
                LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
                LEFT JOIN sys_user ON (UserID=kcfg.LastModifiedBy)
                where $sql_prov $sql_kab (
                    date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
                )
                UNION ALL
                select
                    'Village Crop' AS `Object`,
                    CONCAT(Village, ' : ',
                        CASE vc.CropName
                           WHEN 1 THEN 'Kakao'
                           WHEN 2 THEN 'Jagung'
                           WHEN 3 THEN 'Sawit'
                           WHEN 4 THEN 'Karet'
                           WHEN 5 THEN 'Cengkeh'
                           WHEN 6 THEN 'Padi'
                           WHEN 7 THEN 'Buah-buahan'
                           WHEN 8 THEN 'Kayu-kayuan'
                        END
                    ) AS Name,
                    UserRealName as oleh,kcfg.DateUpdated as waktu
                FROM ktv_village_crop vc
                JOIN ktv_village kcfg ON vc.VillageID = kcfg.VillageID
                LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
                LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
                LEFT JOIN sys_user ON (UserID=vc.LastModifiedBy)
                where $sql_prov $sql_kab (
                    date(vc.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
                )
                UNION ALL
                select
                    'Village Infrastructure' AS `Object`,
                    CONCAT(Village, ' : ', vi.InfrastructureName) AS Name,
                    UserRealName as oleh,kcfg.DateUpdated as waktu
                FROM ktv_village_infrastructure vi
                JOIN ktv_village kcfg ON vi.VillageID = kcfg.VillageID
                LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
                LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
                LEFT JOIN sys_user ON (UserID=vi.LastModifiedBy)
                where $sql_prov $sql_kab (
                    date(vi.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
                )
            ) r
            order by waktu";
        }

        // echo '<pre>'; print_r($sql); echo '</pre>'; exit;
        $query = $this->db->query($sql, array());
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result = $query->result_array();
        return $result;
    }

    public function readSurveys($addLatestSurvey = "no")
    {
        if ($addLatestSurvey == "yes") {
            $sql_addition = "SELECT 'latest' AS id, 'Latest Survey' AS label UNION ";
        } else {
            $sql_addition = "";
        }

        $sql = "
            $sql_addition
            (SELECT a.SurveyNr as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            ORDER BY a.SurveyNr)";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function readStaffs($DistrictID = '')
    {
        $sql="SELECT
                suser.`UserId` AS id,
                CONCAT(suser.`UserRealName`,' [',pp.PartnerName,' - ',pos.Position,']') AS label
            FROM
                sys_user suser
                LEFT JOIN ktv_access_staff akses ON suser.`UserId` = akses.`UserId`
                LEFT JOIN sys_user_role urole ON suser.`UserId` = urole.`UserId`
                LEFT JOIN ktv_persons p ON p.UserID = suser.UserId
                LEFT JOIN ktv_staffs s ON s.PersonID = p.PersonID
                LEFT JOIN (
SELECT
    sp.StaffPosStaffID AS StaffID,
    CONCAT(pos.PositionName) AS `Position`
FROM ktv_staff_positions sp
LEFT JOIN ktv_ref_position_type pos ON pos.PositionID = sp.StaffPosPositionID
GROUP BY sp.StaffPosStaffID
                ) AS pos ON pos.StaffID = s.StaffID
                LEFT JOIN ktv_program_partner pp ON pp.PartnerID = s.ObjID AND s.ObjType IN ('private', 'program')
            WHERE
                akses.`DistrictID` = ? AND
                urole.`RoleId` = '6' AND
                suser.`StatusCode` = 'active'
            GROUP BY suser.`UserId`
            ORDER BY suser.`UserRealName` ASC";
        $query = $this->db->query($sql, array($DistrictID));
        return $query->result_array();
    }

    public function readStaffProgress($userId, $jenis, $awal, $akhir)
    {
        $aw   = explode('-', $awal);
        $jd1  = GregorianToJD($aw[1], $aw[2], $aw[0]);
        $ak   = explode('-', $akhir);
        $jd2  = GregorianToJD($ak[1], $ak[2], $ak[0]);
        $days = $jd2 - $jd1 + 1;
        if ($days == 1) {
            $sql_j = 'HOUR';
        } elseif ($days < 8) {
            $sql_j = 'DATE';
        } elseif ($days < 32) {
            $sql_j = 'WEEK';
        } elseif ($days < 365) {
            $sql_j = 'MONTH';
        } else {
            $sql_j = 'YEAR';
        }

        $sql_periode = "
         select adddate('$awal',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) periode from
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
         limit $days";
        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF((date(DateCreated)=periode and CreatedBy=?),1,0)) as 'Petani baru',
            sum(IF((date(DateUpdated)=periode and LastModifiedBy=? and (DateCreated!=DateUpdated OR DateCreated is null)),1,0)) as 'Ubah petani'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer ON FarmerID>0 AND (CreatedBy=? OR LastModifiedBy=?)
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where (periode between ? and ?)
         group by $sql_j(periode)";
        $query = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['petani'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF((date(kcfg.DateCreated)=periode and kcfg.CreatedBy=?),1,0)) as 'Garden baru',
            sum(IF((date(kcfg.DateUpdated)=periode and kcfg.LastModifiedBy=? and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah garden'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcfg.FarmerID>0 AND (kcfg.CreatedBy=? OR kcfg.LastModifiedBy=?)
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where (periode between ? and ?)
         group by $sql_j(periode)";
        $query = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        //echo $sql;exit;
        $result['garden'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF((date(kcfg.DateCreated)=periode and kcfg.CreatedBy=?),1,0)) as 'Harvest baru',
            sum(IF((date(kcfg.DateUpdated)=periode and kcfg.LastModifiedBy=? and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah harvest'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer_post_harvest kcfg ON kcfg.FarmerID>0 AND (kcfg.CreatedBy=? OR kcfg.LastModifiedBy=?)
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where (periode between ? and ?)
         group by $sql_j(periode)";
        $query          = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        $result['post'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF((date(kcfg.DateCreated)=periode and kcfg.CreatedBy=?),1,0)) as 'Nutrition baru',
            sum(IF((date(kcfg.DateUpdated)=periode and kcfg.LastModifiedBy=? and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah nutrition'
         from ($sql_periode) v
         LEFT JOIN ktv_nutrition kcfg ON kcfg.FarmerID>0 AND (kcfg.CreatedBy=? OR kcfg.LastModifiedBy=?)
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where (periode between ? and ?)
         group by $sql_j(periode)";
        $query               = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        $result['nutrition'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF((date(kcfg.DateCreated)=periode and kcfg.CreatedBy=?),1,0)) as 'PPI baru',
            sum(IF((date(kcfg.DateUpdated)=periode and kcfg.LastModifiedBy=? and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah PPI'
         from ($sql_periode) v
         LEFT JOIN ktv_ppiscore2012 kcfg ON kcfg.FarmerID>0 AND (kcfg.CreatedBy=? OR kcfg.LastModifiedBy=?)
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where (periode between ? and ?)
         group by $sql_j(periode)";
        $query         = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        $result['ppi'] = $query->result_array();

        $sql = "
         select $sql_j(periode) as Periode,
            sum(IF((date(kcfg.DateCreated)=periode and kcfg.CreatedBy=?),1,0)) as 'Finance baru',
            sum(IF((date(kcfg.DateUpdated)=periode and kcfg.LastModifiedBy=? and (kcfg.DateCreated!=kcfg.DateUpdated OR kcfg.DateCreated is null)),1,0)) as 'Ubah finance'
         from ($sql_periode) v
         LEFT JOIN ktv_cocoa_farmer_financial kcfg ON kcfg.FarmerID>0 AND (kcfg.CreatedBy=? OR kcfg.LastModifiedBy=?)
         LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
         LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
         LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
         where (periode between ? and ?)
         group by $sql_j(periode)";
        $query             = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        $result['finance'] = $query->result_array();

        //environment
        $sql="SELECT
                $sql_j(periode) AS Periode,
                sum(IF((date(envi.DateCreated)=periode and envi.CreatedBy=?),1,0)) as 'Environment baru',
                sum(IF((date(envi.DateUpdated)=periode and envi.LastModifiedBy=? and (envi.DateCreated!=envi.DateUpdated OR envi.DateCreated is null)),1,0)) as 'Ubah environment'
            FROM
                ($sql_periode) v
                LEFT JOIN ktv_environment envi ON envi.`FarmerID` > 0 AND (envi.CreatedBy=? OR envi.LastModifiedBy=?)
                LEFT JOIN ktv_cocoa_farmer farmer ON envi.`FarmerID` = farmer.`FarmerID`
                LEFT JOIN ktv_province pro ON pro.`ProvinceID` = SUBSTR(farmer.`VillageID`,1,2)
                LEFT JOIN ktv_district dis ON dis.`DistrictID` = SUBSTR(farmer.`VillageID`,1,4)
            WHERE
                (periode between ? and ?)
            GROUP BY $sql_j(periode)";
        $query             = $this->db->query($sql, array($userId, $userId, $userId, $userId, $awal, $akhir));
        $result['environment'] = $query->result_array();

        // village
        $sql = "
         SELECT
            Periode,
            SUM(baru) AS 'Village baru',
            SUM(ubah) AS 'Ubah village'
         FROM (
             select $sql_j(periode) as Periode,
                SUM(IF(kv.CreatedBy=? AND DATE(kv.DateCreated)=periode,1,0)) AS 'baru',
                SUM(IF((kv.LastModifiedBy=? AND (DATE(kv.DateUpdated)=periode) AND (kv.DateCreated!=kv.DateUpdated OR kv.DateCreated IS NULL))
                ,1,0)) AS 'ubah'
             from ($sql_periode) v
             LEFT JOIN ktv_village kv ON (kv.CreatedBy = ? OR kv.LastModifiedBy = ?)
             LEFT JOIN ktv_province ON ProvinceID=substr(kv.VillageID,1,2)
             LEFT JOIN ktv_district ON DistrictID=substr(kv.VillageID,1,4)
             where (periode between ? and ?)
             group by $sql_j(periode)
             UNION ALL
             select $sql_j(periode) as Periode,
                SUM(IF(vc.CreatedBy=? AND DATE(vc.DateCreated)=periode,1,0)) AS 'baru',
                SUM(IF(
                (vc.LastModifiedBy=? AND (DATE(vc.DateUpdated)=periode) AND (vc.DateCreated!=vc.DateUpdated OR vc.DateCreated IS NULL))
                ,1,0)) AS 'ubah'
             from ($sql_periode) v
             LEFT JOIN ktv_village_crop vc ON (vc.CreatedBy = ? OR vc.LastModifiedBy = ?)
             LEFT JOIN ktv_village kv ON vc.VillageID = kv.VillageID
             LEFT JOIN ktv_province ON ProvinceID=substr(kv.VillageID,1,2)
             LEFT JOIN ktv_district ON DistrictID=substr(kv.VillageID,1,4)
             where (periode between ? and ?)
             group by $sql_j(periode)
             UNION ALL
             select $sql_j(periode) as Periode,
                SUM(IF(vi.CreatedBy=? AND DATE(vi.DateCreated)=periode,1,0)) AS 'baru',
                SUM(IF(
                (vi.LastModifiedBy=? AND (DATE(vi.DateUpdated)=periode) AND (vi.DateCreated!=vi.DateUpdated OR vi.DateCreated IS NULL))
                ,1,0)) AS 'ubah'
             from ($sql_periode) v
             LEFT JOIN ktv_village_infrastructure vi ON (vi.CreatedBy = ? OR vi.LastModifiedBy = ?)
             LEFT JOIN ktv_village kv ON vi.VillageID = kv.VillageID
             LEFT JOIN ktv_province ON ProvinceID=substr(kv.VillageID,1,2)
             LEFT JOIN ktv_district ON DistrictID=substr(kv.VillageID,1,4)
             where (periode between ? and ?)
             group by $sql_j(periode)
         ) r
         GROUP BY Periode";

        $query = $this->db->query($sql, array(
            $userId, $userId, $userId, $userId, $awal, $akhir,
            $userId, $userId, $userId, $userId, $awal, $akhir,
            $userId, $userId, $userId, $userId, $awal, $akhir,
        ));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['village'] = $query->result_array();
        //print_r($result);exit;
        return $result;
    }

    public function readStaffProgresDetails($c, $r, $staff, $awal, $akhir)
    {
        $kab   = explode(',', $kab);
        $aww   = explode('T', $awal);
        $awal  = $aww[0];
        $akk   = explode('T', $akhir);
        $akhir = $akk[0];
        $aw    = explode('-', $awal);
        $jd1   = GregorianToJD($aw[1], $aw[2], $aw[0]);
        $ak    = explode('-', $akhir);
        $jd2   = GregorianToJD($ak[1], $ak[2], $ak[0]);
        $days  = $jd2 - $jd1 + 1;
        if ($days == 1) {
            $sql_j = 'HOUR';
        } elseif ($days < 8) {
            $sql_j = 'DATE';
        } elseif ($days < 32) {
            $sql_j = 'WEEK';
        } elseif ($days < 365) {
            $sql_j = 'MONTH';
        } else {
            $sql_j = 'YEAR';
        }

        $sql_periode = "
         select adddate('$awal',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) periode from
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
          (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
         limit $days";
        $sql = "
         select $sql_j(periode) as Periode,group_concat(periode order by periode separator \"','\") as tgl
         from ($sql_periode) v
         where periode between ? and ?
         group by $sql_j(periode)";
        $query   = $this->db->query($sql, array($awal, $akhir));
        $periode = $query->result_array();

        $keys = array('Periode', 'Petani baru', 'Ubah petani', 'Garden baru', 'Ubah garden', 'Harvest baru', 'Ubah harvest',
            'Nutrition baru', 'Ubah nutrition', 'PPI baru', 'Ubah PPI', 'Finance baru', 'Ubah finance');
        if ($c == '1') {
            $sql = "
            select FarmerID,FarmerName, UserRealName as oleh,DateCreated as waktu
            from ktv_cocoa_farmer
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=CreatedBy
            where CreatedBy=? and date(DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '2') {
            $sql = "
            select FarmerID,FarmerName, UserRealName as oleh,DateUpdated as waktu
            from ktv_cocoa_farmer
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=LastModifiedBy
            where LastModifiedBy=? and date(DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '3') {
            $sql = "
            select GardenNr,SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_cocoa_farmer_garden kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where kcfg.CreatedBy=? and date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '4') {
            $sql = "
            select GardenNr,SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_cocoa_farmer_garden kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where kcfg.LastModifiedBy=? and date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '5') {
            $sql = "
            select SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_cocoa_farmer_post_harvest kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where kcfg.CreatedBy=? and date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '6') {
            $sql = "
            select SurveyNr,kcfg.DateCollection,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_cocoa_farmer_post_harvest kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where kcfg.LastModifiedBy=? and date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '7') {
            $sql = "
            select SurveyNr,InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_nutrition kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where kcfg.CreatedBy=? and date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '8') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_nutrition kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where kcfg.LastModifiedBy=? and date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '9') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_ppiscore2012 kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where kcfg.CreatedBy=? and date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '10') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_ppiscore2012 kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where kcfg.LastModifiedBy=? and date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '11') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_cocoa_farmer_financial kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.CreatedBy
            where kcfg.CreatedBy=? and date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '12') {
            $sql = "
            select SurveyNr,kcfg.InterviewDate,kcf.FarmerID,kcf.FarmerName,
               UserRealName as oleh,kcfg.DateUpdated as waktu
            FROM ktv_cocoa_farmer_financial kcfg
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN ktv_province ON ProvinceID=substr(VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(VillageID,1,4)
            LEFT JOIN sys_user ON UserID=kcfg.LastModifiedBy
            where kcfg.LastModifiedBy=? and date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
            order by waktu";
        } elseif ($c == '13') {
            $sql = "
        SELECT * FROM (
            select 'Village' AS `Object`, Village AS `Name`,
               UserRealName as oleh,kcfg.DateCreated as waktu
            FROM ktv_village kcfg
            LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
            LEFT JOIN sys_user ON (UserID=kcfg.CreatedBy)
            WHERE kcfg.CreatedBy = ? AND (
                date(kcfg.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            )
            UNION ALL
            select
                'Village Crop' AS `Object`,
                CONCAT(Village, ' : ',
                    CASE vc.CropName
                       WHEN 1 THEN 'Kakao'
                       WHEN 2 THEN 'Jagung'
                       WHEN 3 THEN 'Sawit'
                       WHEN 4 THEN 'Karet'
                       WHEN 5 THEN 'Cengkeh'
                       WHEN 6 THEN 'Padi'
                       WHEN 7 THEN 'Buah-buahan'
                       WHEN 8 THEN 'Kayu-kayuan'
                    END
                ) AS Name,
                UserRealName as oleh,vc.DateCreated as waktu
            FROM ktv_village_crop vc
            JOIN ktv_village kcfg ON vc.VillageID = kcfg.VillageID
            LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
            LEFT JOIN sys_user ON (UserID=vc.CreatedBy)
            where vc.CreatedBy = ? AND (
                date(vc.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            )
            UNION ALL
            select
                'Village Infrastructure' AS `Object`,
                CONCAT(Village, ' : ', vi.InfrastructureName) AS Name,
                UserRealName as oleh,vi.DateCreated as waktu
            FROM ktv_village_infrastructure vi
            JOIN ktv_village kcfg ON vi.VillageID = kcfg.VillageID
            LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
            LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
            LEFT JOIN sys_user ON (UserID=vi.CreatedBy)
            where vi.CreatedBy = ? AND (
                date(vi.DateCreated) in ('" . $periode[$r]['tgl'] . "')
            )
        ) r
            order by waktu";
        } elseif ($c == '14') {
            $sql = "
            SELECT * FROM (
                select
                    'Village' AS `Object`,
                    Village AS Name,
                    UserRealName as oleh,kcfg.DateUpdated as waktu
                FROM ktv_village kcfg
                LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
                LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
                LEFT JOIN sys_user ON (UserID=kcfg.LastModifiedBy)
                where kcfg.LastModifiedBy = ? AND (
                    date(kcfg.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
                )
                UNION ALL
                select
                    'Village Crop' AS `Object`,
                    CONCAT(Village, ' : ',
                        CASE vc.CropName
                           WHEN 1 THEN 'Kakao'
                           WHEN 2 THEN 'Jagung'
                           WHEN 3 THEN 'Sawit'
                           WHEN 4 THEN 'Karet'
                           WHEN 5 THEN 'Cengkeh'
                           WHEN 6 THEN 'Padi'
                           WHEN 7 THEN 'Buah-buahan'
                           WHEN 8 THEN 'Kayu-kayuan'
                        END
                    ) AS Name,
                    UserRealName as oleh,kcfg.DateUpdated as waktu
                FROM ktv_village_crop vc
                JOIN ktv_village kcfg ON vc.VillageID = kcfg.VillageID
                LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
                LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
                LEFT JOIN sys_user ON (UserID=vc.LastModifiedBy)
                where vc.LastModifiedBy = ? AND (
                    date(vc.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
                )
                UNION ALL
                select
                    'Village Infrastructure' AS `Object`,
                    CONCAT(Village, ' : ', vi.InfrastructureName) AS Name,
                    UserRealName as oleh,kcfg.DateUpdated as waktu
                FROM ktv_village_infrastructure vi
                JOIN ktv_village kcfg ON vi.VillageID = kcfg.VillageID
                LEFT JOIN ktv_province ON ProvinceID=substr(kcfg.VillageID,1,2)
                LEFT JOIN ktv_district ON DistrictID=substr(kcfg.VillageID,1,4)
                LEFT JOIN sys_user ON (UserID=vi.LastModifiedBy)
                where vi.LastModifiedBy = ? AND (
                    date(vi.DateUpdated) in ('" . $periode[$r]['tgl'] . "')
                )
            ) r
            order by waktu";
        }

        $query  = $this->db->query($sql, array($staff, $staff, $staff));
        $result = $query->result_array();
        return $result;
    }

//printout
    public function readPrintoutCpgbatch($prov = '', $kab = '')
    {
        if ($prov != '') {
            $left = " left join ktv_province kp on kp.ProvinceID=substr(kdp.DistrictID,1,2)";
            $add  = " and Province='$prov'";
        }
        if ($kab != '') {
            $left .= " left join ktv_district kd on kd.DistrictID=kdp.DistrictID";
            $add .= " and District in ('$kab')";
        }
        $sql = "
            select CpgBatchID id, concat(BatchNumber,' - ',PartnerName) label
            from ktv_cpg_batch kcb
            left join ktv_program_partner kpp on kcb.PartnerID=kpp.PartnerID
            left join ktv_district_partner kdp on kdp.PartnerID=kpp.PartnerID
            $left
            WHERE CpgBatchID>0 $add";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function readPrintoutCpg($prov = '', $kab = '')
    {
        if ($prov != '') {
            $left = " left join ktv_province kp on kp.ProvinceID=substr(VillageID,1,2)";
            $add  = " and Province='$prov'";
        }
        if ($kab != '') {
            $left .= " left join ktv_district kd on kd.DistrictID=substr(VillageID,1,4)";
            $add .= " and District in ('$kab')";
        }

        //cek hak akses control data
        if($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program" ){
            $sqlWhereAkses = " AND a.CPGid IN (SELECT sub_a.`CPGid` FROM ktv_cpg_partner sub_a WHERE sub_a.`PartnerID` = '{$_SESSION['PartnerID']}') ";
        }else{
            $sqlWhereAkses = "";
        }

        $sql = "
            select CPGid id, concat('[',CPGid,'] ',GroupName) label
            from ktv_cpg a
            $left
            where CPGid>0 $add $sqlWhereAkses ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function readPrintoutPetani($prov = '', $kab = '', $cpgbatch = '', $cpg = '', $sert = '', $jenissurvey = '', $survey = '', $jenisform = '')
    {
        $fieldSurveyNr = "SurveyNr";

        if ($prov != '') {
            $left = " left join ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)";
            $add  = " and Province='$prov'";
        }
        if ($kab != '') {
            $left .= " left join ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)";
            $add .= " and District in ('$kab')";
        }
        if ($cpgbatch != '') {
            $left = "left join ktv_cpg_batch_trainings_farmers kcbtf on kcbtf.farmerID=kcf.FarmerID
               left join ktv_cpg_batch_trainings kcbt on kcbtf.CpgBatchTrainingID=kcbt.CpgBatchTrainingID";
            $add .= " and kcbt.CpgBatchID=$cpgbatch";
        }
        if ($cpg != '') {
            $left .= " left join ktv_cpg kc on kc.CPGid=kcf.CPGid";
            $add .= " and kc.CPGid='$cpg'";
        }
        if ($sert != '' && $sert != 'Semua') {
            $left .= " left join ktv_cocoa_certification kcc on kcc.FarmerID = kcf.FarmerID ";
            if ($sert == 'Tersertifikasi') {
                $add .= " AND kcc.FarmerID IS NOT NULL AND CURDATE() BETWEEN kcc.CertificationStart AND kcc.CertificationEnd ";
            } else {
                $add .= " AND kcc.FarmerID IS NULL ";
            }
        }
        if ($survey != '') {
            if ($jenissurvey == 'GAP' || $jenissurvey == 'GAP Certification') {
                $tab = 'ktv_cocoa_farmer_garden';
            } elseif ($jenissurvey == 'GNP') {
                $tab = 'ktv_nutrition';
            }

            if ($jenissurvey == 'GFP') {
                $tab = 'ktv_cocoa_farmer_financial';
            }

            if ($jenissurvey == 'PPI') {
                $tab = 'ktv_ppiscore2012';
            }

            if ($jenissurvey == 'Saving Pilot') {
                $tab = 'ktv_saving_pilot';
            }

            if ($jenissurvey == 'AO') {
                $tab = 'ktv_adoption_observations';
                $fieldSurveyNr = "SurveyYear";
            }

            if ($jenissurvey == 'GEP') {
                $tab = 'ktv_environment';
            }

            if($jenissurvey == 'FCP'){
                $tab = 'ktv_fdp';
                $fieldSurveyNr = "SurveyYear";
            }

            $left .= " left join $tab tab on tab.FarmerID=kcf.FarmerID and tab.$fieldSurveyNr='$survey'";

            if ($jenisform == 'Form Hasil') {
                $add .= " and tab.FarmerID is not null";
            }

        }
        if ($jenisform == "Farmer Summary") {
            $add .= " AND kcf.isLoanPassed = 1";
        }

        $sql = "SELECT kcf.FarmerID id, concat('[',kcf.FarmerID,'] ',kcf.FarmerName) label
            from ktv_cocoa_farmer kcf
            $left
            where kcf.StatusCode='active' AND kcf.FarmerID>0 $add";
        //echo $sql;exit;
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        //print_r($result);exit;
        return $result;
    }
    public function readPrintoutPetaniSertifikasi($prov = '', $kab = '', $cpg = '')
    {
        $where = '';
//         FROM ktv_cocoa_certification c
        // INNER JOIN (SELECT c.FarmerID, MAX(c.SurveyNr) AS SurveyNr FROM ktv_cocoa_certification c WHERE c.ICSDate GROUP BY c.FarmerID) z ON c.FarmerID = z.FarmerID
        // JOIN ktv_cocoa_farmer f ON f.FarmerID = c.FarmerID
        $sql = "SELECT
    f.FarmerID id
    , CONCAT('[',f.FarmerID,'] ',f.FarmerName) label

FROM ktv_cocoa_farmer f
JOIN ktv_village v ON v.VillageID = f.VillageID
JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
JOIN ktv_district d ON d.DistrictID = sd.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
WHERE
    1 = 1
    --where--
GROUP BY f.FarmerID";
        if ($cpg != '') {
            $where .= " and f.CPGid = $cpg";
        }
        $sql   = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getFarmerCpg($cpgId)
    {
        $sql = "SELECT FarmerID id, CONCAT('[',FarmerID,'] ',FarmerName) label
                FROM ktv_cocoa_farmer
                WHERE CPGid={$cpgId} ORDER BY FarmerID";
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        //print_r($result);exit;
        return $result;
    }

    public function readPrintoutListTraining($prov = '', $kab = '', $cpgbatch = '', $cpg = '', $jenis = '')
    {
        if ($prov != '') {
            $left = " left join ktv_province kp on kp.ProvinceID=kmt.TrainingProvince";
            $add  = " and Province='$prov'";
        }
        if ($kab != '') {
            if ($jenis == 'Master') {
                $left .= " left join ktv_district kd on kd.DistrictID=kmt.DistrictID";
            } else {
                $left .= " left join ktv_district kd on kd.DistrictID=kmt.TrainingDistrict";
            }

            $add .= " and District in ('$kab')";
        }
        if ($cpgbatch != '') {
            $add .= " and kmt.CpgBatchID=$cpgbatch";
        }
        $left .= " left join ktv_cpg_trainings kct on kct.CpgTrainingsID=kmt.CpgTrainingsID";
        /*if ($cpg!='') {
        $left .= " left join ktv_cpg kc on kc.CPGid=kcf.CPGid";
        $add .= " and kc.GroupName='$cpg'";
        }*/
        if ($jenis != '') {
            if ($jenis == 'Master') {
                $sql = "
                  SELECT MasterTrainingID id, concat('[',date(TrainingStart),' s.d. ',date(TrainingEnd),'] ',CpgTrainings) label
                  FROM ktv_master_trainings kmt
                  $left
                  where kmt.CpgTrainingsID>0 $add";
            } elseif ($jenis == 'Kader') {
                $sql = "
                  SELECT CpgKaderTrainingID id, concat('[',date(TrainingStart),' s.d. ',date(TrainingEnd),'] ',CpgTrainings) label
                  FROM ktv_kader_trainings kmt
                  $left
                  where kmt.CpgTrainingsID>0 $add";
            } else {
                $sql = "
                  SELECT CpgBatchTrainingID id, concat('[',date(TrainingStart),' s.d. ',date(TrainingEnd),'] ',CpgTrainings) label
                  FROM ktv_cpg_batch_trainings kmt
                  left join ktv_cpg_trainings kct on kct.CpgTrainingsID=kmt.CpgTrainingsID
                  where kmt.CpgTrainingsID>0 and CPGid=$cpg";
            }
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function readPrintoutListLearning($prov = '', $kab = '', $cpgbatch = '', $cpg = '')
    {
        /*if ($prov!='') {
        $left = " left join ktv_province kp on kp.ProvinceID=kmt.TrainingProvince";
        $add = " and Province='$prov'";
        }
        if ($kab!='') {
        $left .= " left join ktv_district kd on kd.DistrictID=kmt.TrainingDistrict";
        $add .= " and District in ('$kab')";
        }*/
        if ($cpgbatch != '') {
            $add .= " and kmt.CpgBatchID=$cpgbatch";
        }
        if ($cpg != '') {
            $left .= " left join ktv_cpg kc on kc.CPGid=kmt.CPGid";
            $add .= " and kc.CPGid='$cpg'";
        }
        $sql = "
            SELECT CpgBatchTrainingID id, concat('[',date(TrainingStart),' s.d. ',date(TrainingEnd),'] ',CpgTrainings) label
            FROM ktv_cpg_batch_trainings kmt
            left join ktv_cpg_trainings kct on kct.CpgTrainingsID=kmt.CpgTrainingsID
            $left
            where kmt.CpgTrainingsID>0 $add";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function readPrintoutListTrader($prov = '', $kab = '')
    {
        if ($prov != '') {
            $left = " left join ktv_province kp on kp.ProvinceID=substr(VillageID,1,2)";
            $add  = " and Province='$prov'";
        }
        if ($kab != '') {
            $left .= " left join ktv_district kd on kd.DistrictID=substr(VillageID,1,4)";
            $add .= " and District in ('$kab')";
        }
        if($_SESSION['PartnerID']==''){
            $p1 = "/*"; $p2 = "*/";
        }else{
            $p1 = ""; $p2 = "";
        }
        $sql = "
            SELECT TraderID id, concat(TraderName,IF(Company!='',concat(' (',Company,')'),'')) label
            FROM ktv_traders kmt
            $left
            LEFT JOIN ktv_supplychain_org kso ON kso.OrgID=kmt.TraderID AND kso.OrgType='trader'
            LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID=kso.SupplychainID
            where kmt.TraderID>0 $add $p1 AND sop.PartnerID=? $p2";
        $query = $this->db->query($sql, array($_SESSION['PartnerID']));
        return $query->result_array();
    }

    public function readBu()
    {
        $sql = "
            select SupplychainID id,concat('[',OrgType,'] ',Name) label
            from ktv_supplychain_org_view
            order by label";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function readMenu($kategori, $userId)
    {
        /*
        $sql = "SELECT MenuName "
        . "FROM sys_menu_report "
        . "WHERE "
        . "Kategori = {$kategori}";
         *
         */
        $sql = "SELECT
                    a.MenuName
                FROM sys_menu_report a
                    LEFT JOIN sys_group_report b ON a.MenuName = b.MenuName
                    LEFT JOIN sys_user_group c ON b.GroupId = c.UserGroupGroupId
                WHERE
                    c.UserGroupUserId = ? and
                    a.Kategori = ?";
        $query  = $this->db->query($sql, array($userId, $kategori));
        $result = $query->result_array();
        $x      = 1;
        $y      = 0;
        $data   = array();
        if (count($data > 0)) {
            foreach ($result as $hasil) {
                if ($x > 2) {
                    $x = 1;
                    $y++;
                }
                $idx            = ($x === 1) ? 'col_left' : 'col_right';
                $data[$y][$idx] = $hasil['MenuName'];
                $x++;
            }
        }
        return $data;
    }

    public function getBatch($DistrictID)
    {
        $sql = "SELECT
    b.CpgBatchID AS id,
    CONCAT(b.BatchNumber,' - ',p.PartnerName) AS label
FROM `ktv_cpg_batch_trainings`bt
JOIN ktv_cpg c ON c.CPGid = bt.CPGid
JOIN ktv_cpg_batch b ON b.CpgBatchID = bt.CpgBatchID
JOIN ktv_program_partner p ON p.PartnerID = b.PartnerID
WHERE
    SUBSTR(c.VillageID FROM 1 FOR 4) = ?
GROUP BY label
ORDER BY b.BatchNumber
        ";
        return $this->db->query($sql, array($DistrictID))->result_array();
    }

    public function getCPG($DistrictID)
    {
        $sql = "SELECT
    c.`CPGid` AS id,
    CONCAT(c.`CPGid`,' - ',c.`GroupName`) AS label
FROM
    `ktv_cpg` c
WHERE
    SUBSTR(c.VillageID FROM 1 FOR 4) = ?
GROUP BY c.`CPGid`
        ";
        return $this->db->query($sql, array($DistrictID))->result_array();
    }

    public function getCPGSubdisrict($SubDistrictID)
    {
        $sql = "SELECT
    c.`CPGid` AS id,
    CONCAT(c.`CPGid`,' - ',c.`GroupName`) AS label
FROM
    `ktv_cpg` c
WHERE
    SUBSTR(c.VillageID FROM 1 FOR 7) = ?
GROUP BY c.`CPGid`
        ";
        return $this->db->query($sql, array($SubDistrictID))->result_array();
    }

    public function getActivityDetail($batch_id, $cpg_id = '', $start = 0, $limit = 10)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
    f.`FarmerID`
    ,f.`FarmerName`
    ,vil.`Village`
    ,g.`SurveyNr`
    ,DATE(f.`DateCreated`) AS Farmer
    ,f.`Photo`
    ,IFNULL(fm.Family, 0) AS FamilyNumber
    ,DATE(g.`DateCollection`) AS Garden
    ,DATE(ph.DateCollection) AS PostHarvest
    ,DATE(n.InterviewDate) AS Nutrition
    ,DATE(IF(IFNULL(p2.`InterviewDate`,'1970-01-01')>IFNULL(p1.`InterviewDate`,'1970-01-01'), p2.`InterviewDate`, p1.`InterviewDate`)) AS PPI
    ,DATE(fn.InterviewDate) AS GFP
    ,DATE(envi.`InterviewDate`) AS Environment
    ,CONCAT(g.`Latitude`,', ',g.`Longitude`) AS GPS
    ,ff.label AS NamaFF
FROM (
SELECT
    DISTINCT btf.`FarmerID`,`CPGtrainingsID`,`ProgramStaffID`
FROM `ktv_cpg_batch_trainings_farmers` btf
JOIN `ktv_cpg_batch_trainings` bt ON bt.`CpgBatchTrainingID` = btf.`CpgBatchTrainingID`
WHERE
    bt.CpgBatchID = ?
    --where_cpg--
) r
JOIN `ktv_cocoa_farmer` f ON f.`FarmerID` = r.FarmerID
LEFT JOIN ktv_village vil ON f.`VillageID` = vil.`VillageID`
JOIN `ktv_cocoa_farmer_garden` g ON g.`FarmerID` = f.`FarmerID`
JOIN `ktv_cpg_trainings` t ON t.`CpgTrainingsID` = r.CPGtrainingsID
LEFT JOIN (
    SELECT
    StaffID AS id,
    PersonNm AS label
FROM
    ktv_program_staff a
    LEFT JOIN ktv_persons b
        ON a.PersonID = b.PersonID
UNION
SELECT
    PrivateStaffID AS id,
    StaffName AS label
FROM
    ktv_private_staff
) ff ON ff.id = ProgramStaffID
LEFT JOIN (
SELECT
    f.`FarmerID`,
    COUNT(FamilyID) AS Family
FROM `ktv_family` f
GROUP BY f.`FarmerID`
) fm ON fm.FarmerID = f.`FarmerID`
LEFT JOIN `ktv_cocoa_farmer_post_harvest` ph ON ph.FarmerID = f.`FarmerID` AND g.`SurveyNr` = ph.`SurveyNr` AND ph.`SurveyNr` = 0
LEFT JOIN `ktv_nutrition` n ON n.FarmerID = f.`FarmerID` AND g.`SurveyNr` = n.`SurveyNr` AND n.`SurveyNr` = 0
LEFT JOIN `ktv_cocoa_farmer_financial` fn ON fn.FarmerID = f.`FarmerID` AND g.`SurveyNr` = fn.`SurveyNr` AND fn.`SurveyNr` = 0
LEFT JOIN `ktv_ppiscore` p1 ON p1.`FarmerID` = f.`FarmerID` AND g.`SurveyNr` = p1.`SurveyNr` AND p1.`SurveyNr` = 0
LEFT JOIN `ktv_ppiscore2012` p2 ON p2.`FarmerID` = f.`FarmerID` AND g.`SurveyNr` = p2.`SurveyNr` AND p2.`SurveyNr` = 0
LEFT JOIN ktv_environment envi ON envi.`FarmerID` = f.`FarmerID` AND g.`SurveyNr` = envi.`SurveyNr` AND envi.`SurveyNr` = 0
ORDER BY f.`FarmerID`, g.`SurveyNr`
LIMIT ?, ?
        ";
        $where_cpg = '';
        if (!empty($cpg_id)) {
            $where_cpg = "AND bt.`CPGid` IN (" . $cpg_id . ")";
        }
        $sql   = str_replace('--where_cpg--', $where_cpg, $sql);
        $query = $this->db->query($sql, array($batch_id, intval($start), intval($limit)));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $total = 0;
        $data  = array();
        if ($query->num_rows() > 0) {
            $data        = $query->result_array();
            $query_total = $this->db->query("SELECT FOUND_ROWS() AS total");
            $total       = $query_total->row(0)->total;
        }
        return compact('data', 'total');
    }

    public function getBatchDetail($batch_id)
    {
        $sql = "SELECT
    b.`CpgBatchID`,
    b.`PartnerID`,
    b.`BatchNumber`,
    p.`PartnerName`
FROM `ktv_cpg_batch` b
JOIN `ktv_program_partner` p ON p.`PartnerID` = b.`PartnerID`
WHERE
    b.CpgBatchID = ?
        ";
        $query = $this->db->query($sql, array($batch_id));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getCPGDetail($cpg_id)
    {
        $sql = "SELECT
    `CPGid`,
    `GroupName`
FROM
    `ktv_cpg`
WHERE
    `CPGid` IN
        ";
        $query = $this->db->query($sql . "($cpg_id)");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getCertificationCycle($warehouseId)
    {
        $sql = "SELECT DISTINCT CONCAT('Periode (',CertificationStart,' - ',CertificationEnd,')') AS `year`, CertificationStart `start`, CertificationEnd `end`
                FROM rpt_traceability a
                LEFT JOIN ktv_cocoa_certification b ON a.farmer_id=b.FarmerID
                WHERE wh_dest LIKE ? AND Certification!=0 AND ExternalDate!='0000-00-00' AND CertificationStart!='0000-00-00' AND CertificationEnd!='0000-00-00' ORDER BY CertificationStart";
        $query = $this->db->query($sql, array("%$warehouseId%"));
        return $query->result_array();
    }

    public function getImport($start, $end)
    {
        $sql = "
            SELECT *
            FROM `ktv_supplychain_import`
            WHERE Date between ? and ?";
        $query          = $this->db->query($sql, array($start, $end));
        $result['data'] = $query->result_array();
        $sql_detail     = "
            SELECT b.*,PO,c.OrgID,c.Name,d.FarmerID,FarmerName,District,f.OrgID CoopID,f.Name CoopName
            FROM `ktv_supplychain_import` a
            LEFT JOIN `ktv_supplychain_import_detail` b ON a.ImportID=b.ImportID
            LEFT JOIN `ktv_supplychain_org_view` c ON b.SupplychainID=c.SupplychainID
            LEFT JOIN `ktv_cocoa_farmer` d ON d.FarmerID=b.FarmerID
            LEFT JOIN `ktv_district` e ON e.DistrictID=substr(b.FarmerID,1,4)
            LEFT JOIN `ktv_supplychain_org_view` f ON f.SupplychainID=b.CoopID
            WHERE (a.Date between ? and ?) and b.DetailID IS NOT NULL";
        $query            = $this->db->query($sql_detail, array($start, $end));
        $result['detail'] = $query->result_array();
        return $result;
    }

    public function getCooperatives($Province)
    {
        $sql = "SELECT
    c.CoopID AS id,
    c.CoopName AS label
FROM ktv_cooperatives c
JOIN ktv_province p ON p.ProvinceID = SUBSTR(c.VillageID,1,2)
WHERE
    p.Province = ?
        ";
        $query = $this->db->query($sql, array($Province));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function readPoWarehouse()
    {
        if($_SESSION['PartnerID']==''){
            $p1 = "/*"; $p2 = "*/";
        }else{
            $p1 = ""; $p2 = "";
        }
        $sql = "
            SELECT
               WarehouseID AS id,
               WarehouseName AS label
            FROM ktv_warehouse
            WHERE  StatusCode='active' $p1 AND PartnerID=? $p2
            ORDER By WarehouseName";
        $query = $this->db->query($sql, array($_SESSION['PartnerID']));
        return $query->result_array();
    }
    public function readPoPeriode($wh)
    {
        //#CertificationID AS id,
        $sql = "
            SELECT

               concat(DateStart,' s.d. ',DateEnd) AS label
            FROM ktv_certification_cycle
            WHERE WarehouseID=?";
        $query = $this->db->query($sql, array($wh));
        return $query->result_array();
    }
    public function getPoExcel($wh, $awal, $akhir)
    {
        $sql = "
            SELECT wh_po po, wh_date tanggal,2_name koperasi, 1_name bs,sum(1_bruto) bruto,SUM(wh_netto) netto,
               wh_dollar premium, SUM(wh_netto)/1000*wh_dollar jumlah, IF(2_ispaid='1','Paid','Not Paid') status
            FROM rpt_traceability
            WHERE wh_orgid=? AND (wh_date between ? and ?)
            GROUP BY wh_po
            ORDER BY wh_po";
        $query = $this->db->query($sql, array($wh, $awal, $akhir));
        return $query->result_array();
    }
    public function readPurchaseWarehouses()
    {
        $sql = "SELECT
                    a.WarehouseName as label,
                    CONCAT(b.SupplychainID,'_',WarehouseID) as id
                FROM
                    ktv_warehouse a
                    LEFT JOIN ktv_supplychain_org b ON a.WarehouseID=b.OrgID AND b.OrgType='warehouse'
                ORDER BY WarehouseName";
        $query          = $this->db->query($sql);
        $result['data'] = $query->result_array();
        $result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => 'all')), $result['data']);
        $result['data'] = $result['data'];
        return $result;
    }

    public function readPurchaseCooperatives($key)
    {
        $data        = explode('_', $key);
        $ParentOrgId = $data[0];
        $sql         = "SELECT
                    a.ChildOrgId AS id,
                    c.CoopName AS label
                FROM
                    `ktv_supplychain_org_rel` a
                    LEFT JOIN ktv_supplychain_org b ON a.ChildOrgId=b.SupplychainID
                    left join ktv_cooperatives c ON b.OrgID=c.CoopID
                WHERE
                    ParentOrgId=? AND b.OrgType='koperasi' ORDER BY c.CoopName";
        $query          = $this->db->query($sql, array($ParentOrgId));
        $result['data'] = $query->result_array();
        $result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => 'all')), $result['data']);
        $result['data'] = $result['data'];
        return $result;
    }

    public function readPurchaseBuyingStations($warehouse, $coop)
    {
        $data        = explode('_', $warehouse);
        $ParentOrgId = $data[0];
        $sql         = "SELECT ChildOrgId, b.OrgID AS id, b.OrgType,
                IF(b.OrgType='trader', c.TraderName,e.FarmerName) AS label
                FROM ktv_supplychain_org_rel  a
                LEFT JOIN ktv_supplychain_org b ON a.ChildOrgId=b.SupplychainID
                LEFT JOIN ktv_traders c ON b.OrgID=c.TraderID
                LEFT JOIN sce_farmer d ON b.OrgID=d.SceID
                LEFT JOIN ktv_cocoa_farmer e ON d.FarmerID=e.FarmerID
                WHERE ParentOrgId=? OR ParentOrgId=? AND b.OrgType!='koperasi' ORDER BY label";
        $query          = $this->db->query($sql, array($ParentOrgId, $coop));
        $result['data'] = $query->result_array();
        $result['data'] = $result['data'];
        return $result;
    }

    public function getPurchase($warehouse, $coop, $bs, $status, $start, $end)
    {
        if ($warehouse != "all") {
            $data_wh  = explode('_', $warehouse);
            $where_wh = " AND wh_orgid='" . $data_wh[1] . "'";
        } else {
            $where_wh = "";
        }
        if ($bs != "all") {
            $bs      = str_replace('::%20', '::', $bs);
            $bs      = str_replace('%20', ' ', $bs);
            $data_bs = explode('::', $bs);
            $bs1     = " 1_orgid IN (";
            $bs2     = " 2_orgid IN (";
            for ($i = 0; $i < count($data_bs); $i++) {
                if ($i == 0) {
                    $koma = "";
                } else {
                    $koma = ",";
                }
                $bs1 .= "$koma '" . @$data_bs[$i] . "'";
                $bs2 .= "$koma '" . @$data_bs[$i] . "'";
            }
            $bs1 .= ")";
            $bs2 .= ")";
            $where_bs = " AND($bs1 OR $bs2) ";
        } else {
            $where_bs = "";
        }

        if ($status != "All") {
            $where_status = " AND (c.SupplyDestStatus='$status' OR e.SupplyDestStatus='$status') ";
        } else {
            $where_status = "";
        }
        if ($coop == "all") {
            $coop = "";
        }

        $sql = "
            SELECT
                IF(1_supplychainid IS NULL,2_orgid,1_orgid) AS bs_id,
                IF(1_supplychainid IS NULL,2_name,1_name) AS nama,
                IF(1_supplychainid IS NULL,2_batchnumber,1_batchnumber) AS batchnumber,
                IF(1_supplychainid IS NULL,2_date,1_date) AS tanggal,
                IF(1_supplychainid IS NULL,2_bruto,1_bruto) AS bruto,
                IF(1_supplychainid IS NULL,2_netto,1_netto) AS netto,
                IF(1_supplychainid IS NULL,e.SupplyDestStatus,c.SupplyDestStatus) AS statusnya
            FROM rpt_traceability a
            LEFT JOIN ktv_supplychain_transaction b ON 1_transid=b.SupplyTransID
            LEFT JOIN ktv_supplychain_batch c ON b.SupplyBatchID=c.SupplyBatchID
            LEFT JOIN ktv_supplychain_transaction d ON 2_transid=d.SupplyTransID
            LEFT JOIN ktv_supplychain_batch e ON d.SupplyBatchID=e.SupplyBatchID
            WHERE
                (1_orgid LIKE ? OR 2_orgid LIKE ?)
                $where_wh
                AND ((1_date >= ? AND 1_date <= ?) OR (2_date >= ? AND 2_date <= ?))
                $where_status
                $where_bs
            ";
        $query = $this->db->query($sql, array(
            "%$coop%",
            "%$coop%",
            $start, $end,
            $start, $end,
            $status, $status));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readRekapKoperasi($wh)
    {
        $sql = "
            SELECT b.OrgID id, CoopName label
            FROM ktv_supplychain_org_rel a
            LEFT JOIN ktv_supplychain_org b ON a.ChildOrgId=b.SupplychainID AND b.OrgType='koperasi'
            LEFT JOIN ktv_supplychain_org c ON a.ParentOrgId=c.SupplychainID AND c.OrgType='warehouse'
            LEFT JOIN ktv_cooperatives ON CoopID=b.OrgID
            WHERE c.OrgID=?
            ORDER BY CoopName";
        $query = $this->db->query($sql, array($wh));
        return $query->result_array();
    }
    public function readRekapBs($coop)
    {
        $sql = "
            SELECT b.OrgID id, d.Name label
            FROM ktv_supplychain_org_rel a
            LEFT JOIN ktv_supplychain_org b ON a.ChildOrgId=b.SupplychainID
            LEFT JOIN ktv_supplychain_org c ON a.ParentOrgId=c.SupplychainID AND c.OrgType='koperasi'
            LEFT JOIN ktv_supplychain_org_view d ON d.SupplychainID=b.SupplychainID
            WHERE c.OrgID=?
            ORDER BY d.Name";
        $query = $this->db->query($sql, array($coop));
        return $query->result_array();
    }
    public function getRekapExcel($wh, $coop, $bs, $status, $awal, $akhir)
    {
        $sql = "
            SELECT IFNULL(c.OrgID,b.OrgID) BsID, IFNULL(c.Name,b.Name) BsName, SupplyBatchNumber BatchNumber,
               SupplyBatchDate Tanggal, VolumeBruto Bruto, VolumeNetto Netto
            FROM ktv_supplychain_batch a
            LEFT JOIN ktv_supplychain_org_view b ON a.SupplyOrgID=b.SupplychainID
            LEFT JOIN ktv_supplychain_org_view c ON PerwakilanOrgID=c.OrgID AND c.OrgType='Pedagang'
            WHERE (b.OrgID=? OR PerwakilanOrgID=?) AND (SupplyBatchDate BETWEEN ? AND ?) and SupplyDestStatus=?
            ORDER BY SupplyBatchDate";
        $query = $this->db->query($sql, array($bs, $bs, $awal, $akhir, $status));
        return $query->result_array();
    }

    public function getCertifiedTraceability($ProvinceID, $WarehouseID)
    {
        $sql = "SELECT a.*
    --select--
FROM (
SELECT
    a.FarmerID, a.FarmerName, CASE WHEN a.Gender=1 THEN 'Male' ELSE 'Female' END Gender,
    a.HandPhone PhoneNr, h.Province, g.District, f.SubDistrict, e.Village,
    COUNT(DISTINCT b.GardenNr) NrCertifiedFarm,
    SUM(b.PohonTBM) TBM, SUM(b.PohonTM) TM, SUM(b.GardenHaUnCertified) CertifiedHa,
    SUM((b.PanenTrekMonths* b.PanenTrekPanenMonth* b.PanenTrekKg)+
    (b.PanenBiasaMonths* b.PanenBiasaPanenMonth* b.PanenBiasaKg)+
    (b.PanenRayaMonths* b.PanenRayaPanenMonth* b.PanenRayaKg)) KgTotal,
    SUM((b.PanenTrekMonths* b.PanenTrekPanenMonth* b.PanenTrekKg)+
    (b.PanenBiasaMonths* b.PanenBiasaPanenMonth* b.PanenBiasaKg)+
    (b.PanenRayaMonths* b.PanenRayaPanenMonth* b.PanenRayaKg))/ SUM(b.GardenHaUnCertified) Kg_Ha,
    SUM((b.PanenTrekMonths* b.PanenTrekPanenMonth* b.PanenTrekKg)+
    (b.PanenBiasaMonths* b.PanenBiasaPanenMonth* b.PanenBiasaKg)+
    (b.PanenRayaMonths* b.PanenRayaPanenMonth* b.PanenRayaKg))/SUM(b.PohonTBM + b.PohonTM) Kg_Tree,
    SUM(i.LastKg_Ha) LastKg_Ha,
    j.BuruhFulltime PermanentWorkers, c.Year YearCert,
    REPLACE(REPLACE(d.CommentAudit, '\r\n', ' '), '\n', ' ') CommentAudit,
    REPLACE(REPLACE(d.RecommendationAudit, '\r\n', ' '), '\n', ' ') RecommendationAudit,
    MAX(d.ICSDate) ICSDate,
    CASE d.StatusAudit WHEN 1 THEN 'Approve' WHEN 2 THEN 'Disapprove' WHEN 3 THEN 'Approve With Condition' END 'Result Audit'
FROM ktv_cocoa_farmer a
INNER JOIN ktv_cocoa_farmer_garden b ON a.FarmerID=b.FarmerID
INNER JOIN ktv_cocoa_certification c ON a.FarmerID=c.FarmerID AND b.GardenNr=c.GardenNr AND b.SurveyNr=c.SurveyNr
INNER JOIN ktv_cocoa_certification_audit_log d ON a.FarmerID=d.FarmerID AND b.GardenNr=d.GardenNr AND b.SurveyNr=d.SurveyNr
LEFT JOIN ktv_village e ON a.VillageID=e.VillageID
LEFT JOIN ktv_subdistrict f ON e.SubDistrictID=f.SubDistrictID
LEFT JOIN ktv_district g ON f.DistrictID=g.DistrictID
LEFT JOIN ktv_province h ON g.ProvinceID=h.ProvinceID
LEFT JOIN ktv_cocoa_farmer_post_harvest j ON a.FarmerID=j.FarmerID AND b.SurveyNr=j.SurveyNr
LEFT JOIN (SELECT FarmerID, GardenNr,
    SUM((PanenTrekMonths* PanenTrekPanenMonth* PanenTrekKg)+
    (PanenBiasaMonths* PanenBiasaPanenMonth* PanenBiasaKg)+
    (PanenRayaMonths* PanenRayaPanenMonth* PanenRayaKg))/SUM(GardenHaUnCertified) LastKg_Ha
    FROM ktv_cocoa_farmer_garden WHERE SurveyNr=0 GROUP BY FarmerID, GardenNr) i ON a.FarmerID=i.FarmerID AND b.GardenNr=i.GardenNr
WHERE
d.StatusAudit=1
AND YEAR(d.ICSDate) = 2015
AND h.Province='aceh'
GROUP BY a.FarmerID) a
--join--
        ";
        $add_select = '';
        $add_join   = '';
        $wh_cycle   = $this->getWarehouseCertification($WarehouseID);
        if (!empty($wh_cycle)) {
            foreach ($wh_cycle as $key => $value) {
                $add_select .= "
    ,total_{$value['year']}
    ,net_price_{$value['year']}
    ,payment_{$value['year']}
                ";
                $add_join .= "
LEFT JOIN (
    SELECT
        farmer_id as SupplyID,
        SUM(wh_netto) as 'total_{$value['year']}',
        net_price AS 'net_price_{$value['year']}',
        SUM(payment) as 'payment_{$value['year']}'
    from rpt_traceability a
    LEFT JOIN
    (
    SELECT
        SupplyTransID,
        IF(FAQNetPrice='0.00',IF(FAQContractPrice='0.00',(SELECT FAQPrice FROM `ktv_supplychain_price` WHERE PriceSupplychainID=49),FAQContractPrice),FAQNetPrice) as 'net_price',
        if(FAQTotalPayment='0.00',FAQVolumeBruto*IF(FAQNetPrice='0.00',IF(FAQContractPrice='0.00',(SELECT FAQPrice FROM `ktv_supplychain_price` WHERE PriceSupplychainID=49),FAQContractPrice),FAQNetPrice),FAQTotalPayment) AS 'payment'
    FROM ktv_supplychain_transaction
    ) b on b.SupplyTransID=a.`1_transid`
    where (1_date BETWEEN '2015-02-01' AND '2016-02-29') and 1_bruto is not null and substr(farmer_id,1,2)=11
    group by farmer_id
) k_{$key} ON k_{$key}.SupplyID=a.FarmerID AND k_{$key}.SupplyID IS NOT NULL
                ";
            }
        }

        $sql   = str_replace('--select--', $add_select, $sql);
        $sql   = str_replace('--join--', $add_join, $sql);
        $query = $this->db->query($sql, array($WarehouseID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;

    }

    public function getWarehouseCertification($WarehouseID)
    {
        $sql = "SELECT
    YEAR(c.DateStart) AS `year`,
    c.DateStart,
    c.DateEnd
FROM ktv_certification_cycle c
WHERE
    c.DateStart IS NOT NULL
    AND c.DateStart != '0000-00-00'
    AND c.WarehouseID = ?
        ";
        $query = $this->db->query($sql, array($WarehouseID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function readUnits($District, $key, $page, $start, $limit)
    {
        $sql = "SELECT SupplychainID, OrgID AS id, OrgType AS tipe, Name AS nama
                FROM ktv_supplychain_org_view
                WHERE

                    (OrgType LIKE ? OR OrgID LIKE ? OR Name LIKE ? )
                ORDER BY Name LIMIT $start,$limit";
        $query          = $this->db->query($sql, array("%$key%", "%$key%", "%$key%"));
        $result['data'] = $query->result_array();
        $result['data'] = $result['data'];
        return $result;
    }

    public function readBatchs($SupplychainID, $key, $page, $start, $limit)
    {
        if ($SupplychainID != "") {
            $where = " AND SupplyOrgID=$SupplychainID ";
        } else {
            $where = "";
        }
        $sql = "SELECT SupplyBatchID, SupplyBatchNumber, DestPO
                FROM ktv_supplychain_batch
                WHERE
                    (SupplyBatchNumber LIKE ? OR DestPO LIKE ?)
                    $where
                ORDER BY SupplyBatchNumber LIMIT $start,$limit";
        $query          = $this->db->query($sql, array("%$key%", "%$key%"));
        $result['data'] = $query->result_array();
        $result['data'] = $result['data'];
        return $result;
    }

    /**
     * Fetching field-field yang ada di view database;
     * @author ardiantoro@koltiva.com
     * @param  String $subject  string jenis laporan yang ingin di generate
     * @return Json             json hasil fetch field2 di view
     */
    public function getcols($subject)
    {

        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $sql_member = 'SELECT memberID,
        registeredDate "Tgl Registrasi",
        if(status = 1,"Active","Inactive") "Status",
        farmerID "Farmer ID",
        primaryNo "No Anggota",
        CONCAT("[",typeCode,"] ",typeName) AS "Jenis Anggota",
        identityNumber "NO. Identitas",
        placeOfBirth "Tempat Lahir",
        dateOfBirth "Tgl Lahir",
        gender "Jenis Kelamin",
        address "Alamat",
        CONCAT("[",village.villageID,"] ",village.Village) as "Desa",
        phone "Telp",
        job "Pekerjaan",
        if(maritalStatus = 1,"Belum Menikah",IF(maritalStatus = 2,"Menikah",IF(maritalStatus = 3,"Janda/Duda",""))) AS "Status Pernikahan"
      FROM
        coop_member
      LEFT JOIN coop_member_type type ON type.typeID = coop_member.typeID
      LEFT JOIN ktv_village village ON village.VillageID = coop_member.villageID';

        $query = $this->db->query($sql_member);

        //var_dump($query);die;
        $fields = array_keys($query->row_array());
        $output = array();
        foreach ($fields as $val) {
            $output[] = array('column' => $val);
        }
        return array('data' => $output, 'total' => count($fields));

    }

    public function generateData($subject)
    {

        switch ($subject) {
            case 'member':
                $sql = 'SELECT memberID,
            registeredDate "Tgl Registrasi",
            if(status = 1,"Active","Inactive") "Status",
            farmerID "Farmer ID",
            primaryNo "No Anggota",
            CONCAT("[",typeCode,"] ",typeName) AS "Jenis Anggota",
            identityNumber "NO. Identitas",
            placeOfBirth "Tempat Lahir",
            dateOfBirth "Tgl Lahir",
            gender "Jenis Kelamin",
            address "Alamat",
            CONCAT("[",village.villageID,"] ",village.Village) as "Desa",
            phone "Telp",
            job "Pekerjaan",
            if(maritalStatus = 1,"Belum Menikah",IF(maritalStatus = 2,"Menikah",IF(maritalStatus = 3,"Janda/Duda",""))) AS "Status Pernikahan"
          FROM
            coop_member
          LEFT JOIN coop_member_type type ON type.typeID = coop_member.typeID
          LEFT JOIN ktv_village village ON village.VillageID = coop_member.villageID';
                break;
            default:
                $sql = 'SELECT memberID,
            registeredDate "Tgl Registrasi",
            if(status = 1,"Active","Inactive") "Status",
            farmerID "Farmer ID",
            primaryNo "No Anggota",
            CONCAT("[",typeCode,"] ",typeName) AS "Jenis Anggota",
            identityNumber "NO. Identitas",
            placeOfBirth "Tempat Lahir",
            dateOfBirth "Tgl Lahir",
            gender "Jenis Kelamin",
            address "Alamat",
            CONCAT("[",village.villageID,"] ",village.Village) as "Desa",
            phone "Telp",
            job "Pekerjaan",
            if(maritalStatus = 1,"Belum Menikah",IF(maritalStatus = 2,"Menikah",IF(maritalStatus = 3,"Janda/Duda",""))) AS "Status Pernikahan"
          FROM
            coop_member
          LEFT JOIN coop_member_type type ON type.typeID = coop_member.typeID
          LEFT JOIN ktv_village village ON village.VillageID = coop_member.villageID';
                break;

        }

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }

        return array('data' => $output, 'total' => count($output));

    }

    public function get_data_traceability_sync($limit = 50, $start = 0, $xls = false, $pedagang = false, $batchdate = false, $batchnumber = false, $nopo = false)
    {

        $output = array();

        $sql = "SELECT b2.SupplyBatchID, CONCAT('[',trader.TraderID,']',trader.TraderName) AS Trader,
                    batch.SupplyBatchNumber,
                    batch.SupplyBatchDate,
                    batch.DestPO,
                    trans.DateTransaction,
                    (SELECT CONCAT('[',trans.SupplyID,']',FarmerName) FROM ktv_cocoa_farmer WHERE FarmerID = trans.SupplyID) AS Farmer,
                    trans.FakturNumber,
                    trans.FAQVolumeBruto,
                    trans.FAQVolumeNetto,
                    trans.FAQQualityKA AS Moisture,
                    trans.FAQQualityBC AS BeanCount,
                    trans.FAQQualityMouldy AS Mouldy,
                    trans.FAQQualityWaste AS Waste,
                    trans.FAQQualityInsect AS Insect,
                    trans.FAQQualitySlaty AS Slaty,
                    trans.FAQContractPrice,
                    trans.FAQNetPrice,
                    trans.FAQTotalPayment
                FROM
                    ktv_supplychain_transaction trans
                    LEFT JOIN ktv_supplychain_batch batch ON batch.SupplyBatchID = trans.SupplyBatchID
                    LEFT JOIN ktv_supplychain_org org ON org.SupplychainID = batch.SupplyOrgID
                    LEFT JOIN ktv_traders trader ON trader.TraderID = org.OrgID
                    LEFT JOIN ktv_supplychain_transaction t2 ON t2.SupplyID=batch.SupplyBatchNumber AND t2.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_batch b2 ON b2.SupplyBatchID=t2.SupplyBatchID
                WHERE SUBSTR(trader.VillageID,1,4) = '7312'";

        if (strlen($pedagang) > 0) {
            $sql .= " AND SupplyOrgID = '" . $pedagang . "'";
        }

        if (strlen($nopo) > 0) {
            $sql .= " AND DestPO like '%" . $nopo . "%'";
        }

        if (strlen($batchnumber) > 0) {
            $sql .= " AND SupplyBatchNumber like '%" . $batchnumber . "%'";
        }

        if (strlen($batchdate) > 0) {
            $sql .= " AND SupplyBatchDate = '" . $batchdate . "'";
        }
        //svar_dump($sql);die;
        $Q     = $this->db->query($sql);
        $total = $Q->num_rows();

        if ($xls == 'true') {
            $output = $Q->result_array();
            return $output;
        }

        //terakhir set limit
        $sql .= " LIMIT " . $start . "," . $limit;
        $Q = $this->db->query($sql);
        if ($Q->num_rows() > 0) {
            $output = $Q->result_array();
        }

        $data['data']  = $output;
        $data['total'] = $total;

        return $data;

    }

    public function readKoperasi($wh, $prov, $kab, $awal, $akhir){
        $d = ($kab=='' || $kab=='null') ? "#" : "";
        $sql = "SELECT DISTINCT (2_orgid) id,2_name label FROM rpt_traceability WHERE 2_orgtype='Koperasi' AND (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND wh_dest LIKE %s AND SUBSTR(farmer_id,1,2)=? $d AND SUBSTR(farmer_id,1,4)=? ";
        $query    = $this->db->query(sprintf($sql,"'%|$wh|%'"), array($awal, $akhir, $prov, $kab));
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    public function readBuyingUnit($wh, $prov, $kab, $awal, $akhir){
        $d = ($kab=='' || $kab=='null') ? "#" : "";
        $sql = "SELECT DISTINCT (IFNULL(1_orgid,2_orgid)) id, CONCAT('[',IFNULL(1_orgtype, 2_orgtype),']',IFNULL(1_name, 2_name)) label FROM rpt_traceability WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND wh_dest LIKE %s AND ( IF(1_orgid IS NULL,2_orgtype,'Pedagang')!='koperasi') AND SUBSTR(farmer_id,1,2)=? $d AND SUBSTR(farmer_id,1,4)=? ";
        $query    = $this->db->query(sprintf($sql,"'%|$wh|%'"), array($awal, $akhir, $prov, $kab));
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    public function readFarmerTraceability($wh, $prov, $kab, $awal, $akhir){
        $d = ($kab=='' || $kab=='null') ? "#" : "";
        $sql = "SELECT DISTINCT (farmer_id) id,CONCAT(FarmerID,'|',FarmerName) label FROM rpt_traceability a LEFT JOIN ktv_cocoa_farmer b ON a.farmer_id=b.FarmerID WHERE IFNULL(1_date,2_date) BETWEEN ? AND ? AND wh_orgid=?";
        $query    = $this->db->query(sprintf($sql,"'%|$wh|%'"), array($awal, $akhir, $wh));
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    public function getMemberReport($start = 0, $limit = 25, $sort = 'memberID', $dir = 'DESC', $filter = array()) {
       $sql = "
            SELECT
                a.`memberID` AS id,
                a.`farmerID`,
                a.`primaryNo`,
                a.`registeredDate`,
                a.`typeID`,
                a.`name`,
                a.`identityType`,
                a.`identityNumber`,
                a.`gender`,
                a.`placeOfBirth`,
                a.`dateOfBirth`,
                a.`address`,
                a.`villageID`,
                b.`Village`,
                c.`subDistrict`,
                d.`district`,
                a.`phone`,
                a.`maritalStatus`,
                a.`education`,
                a.`status`,
                a.`remark`,
                a.`photo`,
                a.`signature`,
                a.`familyName`,
                a.`familyAddress`,
                a.`familyRelation`,
                a.`familyIdentityType`,
                a.`familyIdentityNumber`,
                a.`familyPhone`,
                e.saldo as saldoSimpok,
                f.saldo as saldoWajib,
                g.saldo as saldoSuka,
                cpg.GroupName,
                a.uangPangkal,
                a.savingPokok,
                a.savingWajib
            FROM
                `coop_member` a
            LEFT JOIN ktv_village b ON a.`villageID` = b.`VillageID`
            LEFT JOIN coop_member_type e ON e.`typeID` = a.`typeID`
            LEFT JOIN ktv_subdistrict c ON c.`SubDistrictID` = b.`SubDistrictID`
            LEFT JOIN ktv_district d ON d.`DistrictID` = c.`DistrictID`
            LEFT JOIN ktv_cocoa_farmer farmer ON farmer.farmerID = a.farmerID
            LEFT JOIN ktv_cpg cpg ON cpg.CPGid = farmer.CPGid
            left join (
                select memberID,(setoran-tarikan) as saldo
                from (
                    select a.memberID,a.memberSavingID,a.savingTypeID,b.savingTypeSHU,setoran,tarikan
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        join (select a.memberSavingID,IFNULL(setoran,0) as setoran,IFNULL(tarikan,0) as tarikan
                                from coop_member_saving a
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as setoran
                                    from coop_member_transaction
                                    where memberTransactionType=1
                                    GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as tarikan
                                    from coop_member_transaction
                                    where memberTransactionType=2
                                    GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where b.savingTypeSHU = 1
                ) a
            ) e ON a.memberID = e.memberID
            left join (
                select memberID,(setoran-tarikan) as saldo
                from (
                    select a.memberID,a.memberSavingID,a.savingTypeID,b.savingTypeSHU,setoran,tarikan
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        join (select a.memberSavingID,IFNULL(setoran,0) as setoran,IFNULL(tarikan,0) as tarikan
                                from coop_member_saving a
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as setoran
                                    from coop_member_transaction
                                    where memberTransactionType=1
                                    GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as tarikan
                                    from coop_member_transaction
                                    where memberTransactionType=2
                                    GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where b.savingTypeSHU = 2
                ) a
            ) f ON a.memberID = f.memberID
            left join (
                select memberID,(setoran-tarikan) as saldo
                from (
                    select a.memberID,a.memberSavingID,a.savingTypeID,b.savingTypeSHU,setoran,tarikan
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        join (select a.memberSavingID,IFNULL(setoran,0) as setoran,IFNULL(tarikan,0) as tarikan
                                from coop_member_saving a
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as setoran
                                    from coop_member_transaction
                                    where memberTransactionType=1
                                    GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as tarikan
                                    from coop_member_transaction
                                    where memberTransactionType=2
                                    GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where b.savingTypeSHU = 3
                ) a
            ) g ON a.memberID = g.memberID";

            $sql .= " WHERE a.coopID = ".getCoopID()." ";

            if($filter['memberName'] != ''){
                $sql .= " AND a.name LIKE '%" . $filter['memberName'] ."%' ";
            }

            if($filter['primaryNo'] != ''){
                $sql .= " AND a.primaryNo LIKE '%" . $filter['primaryNo'] ."%' ";
            }

            if($filter['memberStatus'] != ''){
                $sql .= " AND a.status = " . $filter['memberStatus'] ." ";
            }

            if($filter['memberRegDate'] != ''){
                $sql .= " AND a.registeredDate = '" . $filter['memberRegDate'] ."' ";
            }

            if($filter['district'] != ''){
                $sql .= " AND d.DistrictID = '" . $filter['district'] ."' ";
            }

            if($filter['subDistrict'] != ''){
                $sql .= " AND c.SubDistrictID = '" . $filter['subDistrict'] ."' ";
            }

            if($filter['village'] != ''){
                $sql .= " AND b.VillageID = '" . $filter['village'] ."' ";
            }

            $total = $this->db->query($sql)->num_rows();

            $sql .= " ORDER BY a.primaryNo desc LIMIT ".$start.",".$limit;
            $query = $this->db->query($sql);

            if($total){
                $result['data'] = $query->result_array();
                $result['total'] = $total;

                return $result;
            }

            return array('data'=>array(), 'total'=>0);
    }

    public function getMemberTransactions($start = 0, $limit = 25, $sort = 'mt.MemberTransactionID', $dir = 'DESC', $filter = array()){

        $CoopID = getCoopID();
        $result = array('data' => array(), 'total' => 0);

        $this->db->from('coop_member_transaction mt');
        $this->db->join('coop_member m', 'mt.MemberID = m.memberID', 'left');
        $this->db->join('coop_member_saving ms', 'mt.MemberSavingID = ms.memberSavingID', 'left');
        $this->db->join('coop_cash_source cs', 'mt.CashSourceID = cs.cashSourceID', 'left');
        $this->db->join('coop_saving_type st', 'ms.savingTypeID = st.savingTypeID', 'left');

        $this->db->where('mt.CoopID', $CoopID);

        if(count($filter) >= 1){
            $this->db->like($filter);
        }

        $this->db->order_by($sort, $dir);
        $cs = $this->db->_compile_select();

        // var_dump($cs); die();
        $this->db->limit($limit, $start);
        $data = $this->db->get();

        $total = $this->db->query($cs)->num_rows();

        if(count($filter) >= 1){
            $total = $data->num_rows();
        }

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }

    public function getOperationalTransactions($start = 0, $limit = 25, $sort = 't.transactionID', $dir = 'DESC', $filter = array()){
        $CoopID = getCoopID();

        $result = array('data' => array(), 'total' => 0);

        $this->db->from('coop_transactions t');
        $this->db->join('coop_cash_source cs', 't.cashSourceID = cs.cashSourceID', 'left');
        $this->db->join('accounting_coa c', 'cs.coaCode = c.CoaCode', 'left');

        $this->db->where('t.CoopID', $CoopID);

        if(count($filter) >= 1){
            $this->db->like($filter);
        }

        $this->db->order_by($sort, $dir);
        $cs = $this->db->_compile_select();

        $this->db->limit($limit, $start);
        $data = $this->db->get();

        $total = $this->db->query($cs)->num_rows(); //total berupa grand result

        if(count($filter) >= 1){
            $total = $data->num_rows(); //if has filters, replace total with current result
        }

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }


    public function getComboSavingType(){
        $CoopID = getCoopID();
        $result = array('data'=>array(), 'total'=>0);

        $this->db->select('savingTypeID, savingTypeName');
        $this->db->from('coop_saving_type');
        $this->db->where('coopID', $CoopID);

        $data = $this->db->get();
        $total = $data->num_rows();

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }

    public function getComboCOA($query = ''){
        $CoopID = getCoopID();
        $result = array('data'=>array(), 'total'=>0);

        $this->db->select('CoaId, CoaCode, CoaTitle');
        $this->db->from('accounting_coa');
        $this->db->where('CoopID', $CoopID);

        if(strlen($query) >= 1){
            $this->db->like('CoaTitle', $query);
        }

        $data = $this->db->get();
        $total = $data->num_rows();

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }

    public function getComboDistrict($query = ''){
        $result = array('data'=>array(), 'total'=>0);

        $this->db->select('DistrictID, District');
        $this->db->from('ktv_district');

        if(strlen($query) >= 1){
            $this->db->like('District', $query);
        }

        $data = $this->db->get();
        $total = $data->num_rows();

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }

    public function getComboSubDistrict($DistrictID, $query = ''){
        $result = array('data'=>array(), 'total'=>0);

        $this->db->select('SubDistrictID, SubDistrict');
        $this->db->from('ktv_subdistrict');
        $this->db->where('DistrictID', $DistrictID);

        if(strlen($query) >= 1){
            $this->db->like('SubDistrict', $query);
        }

        $data = $this->db->get();
        $total = $data->num_rows();

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }

    public function getComboVillage($SubDistrictID, $query = ''){
        $result = array('data'=>array(), 'total'=>0);

        $this->db->select('VillageID, Village');
        $this->db->from('ktv_village');
        $this->db->where('SubDistrictID', $SubDistrictID);

        if(strlen($query) >= 1){
            $this->db->like('SubDistrict', $query);
        }

        $data = $this->db->get();
        $total = $data->num_rows();

        if($total >= 1){
            $result['data'] = $data->result_array();
            $result['total'] = $total;
        }

        return $result;
    }

    
    
    public function getTraceabilityCH($PartnerID)
    {
        $sql = "SELECT WarehouseID id, WarehouseName label FROM ktv_warehouse WHERE StatusCode='active' AND PartnerID=?";
        $query = $this->db->query($sql, array($PartnerID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getTraceabilityWarehouseCargill($PartnerID){
        $sql = "SELECT b.SupplychainID id, WarehouseName label FROM ktv_warehouse a LEFT JOIN ktv_supplychain_org b ON b.OrgID=a.WarehouseID AND b.OrgType='warehouse' WHERE a.StatusCode='active' AND a.PartnerID=?";
        $query = $this->db->query($sql, array($PartnerID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getTraceabilityCargillProvince($PartnerID){
        $sql = "SELECT DISTINCT SUBSTR(org.VillageID,1,2) id, Province label
                FROM
                    view_supplychain_org org
                    LEFT JOIN ktv_supplychain_org_partner sp ON sp.SupplychainID=org.SupplychainID
                    LEFT JOIN ktv_province p ON p.ProvinceID=SUBSTR(org.VillageID,1,2)
                WHERE sp.PartnerID=?";
        $query = $this->db->query($sql, array($PartnerID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getTraceabilityKoperasi($wh, $awal, $akhir){
        $sql = "SELECT  org2.OrgID id, org2.`Name` label
                FROM
                    ktv_supplychain_org_rel a
                    LEFT JOIN ktv_supplychain_org b ON a.ParentOrgId=b.SupplychainID
                    LEFT JOIN ktv_warehouse c ON c.WarehouseID=b.OrgID
                    LEFT JOIN ktv_supplychain_org kso2 ON kso2.SupplychainID=a.ChildOrgId
                    LEFT JOIN view_supplychain_org org2 ON org2.SupplychainID=kso2.SupplychainID
                    LEFT JOIN ktv_supplychain_org_rel rel2 ON rel2.ParentOrgId=a.ChildOrgId
                    LEFT JOIN ktv_supplychain_org kso1 ON kso1.SupplychainID=rel2.ChildOrgId
                    LEFT JOIN view_supplychain_org org1 ON org1.SupplychainID=kso1.SupplychainID
                WHERE b.OrgType='warehouse'  AND b.OrgID=?
                    AND !(a.EndDate < ? OR a.StartDate > ?)
                    AND !(rel2.EndDate < ? OR rel2.StartDate > ?) AND kso2.OrgType='koperasi'
                GROUP BY org2.SupplychainID
                UNION
                SELECT  org1.OrgID id, org1.`Name` label
                FROM
                    ktv_supplychain_org_rel a
                    LEFT JOIN ktv_supplychain_org b ON a.ParentOrgId=b.SupplychainID
                    LEFT JOIN ktv_warehouse c ON c.WarehouseID=b.OrgID
                    LEFT JOIN ktv_supplychain_org kso2 ON kso2.SupplychainID=a.ChildOrgId
                    LEFT JOIN view_supplychain_org org2 ON org2.SupplychainID=kso2.SupplychainID
                    LEFT JOIN ktv_supplychain_org_rel rel2 ON rel2.ParentOrgId=a.ChildOrgId
                    LEFT JOIN ktv_supplychain_org kso1 ON kso1.SupplychainID=rel2.ChildOrgId
                    LEFT JOIN view_supplychain_org org1 ON org1.SupplychainID=kso1.SupplychainID
                WHERE b.OrgType='warehouse'  AND b.OrgID=?
                    AND !(a.EndDate < ? OR a.StartDate > ?)
                    AND !(rel2.EndDate < ? OR rel2.StartDate > ?) AND kso1.OrgType='koperasi'
                GROUP BY org1.SupplychainID";
        $query = $this->db->query($sql, array($wh, $awal, $akhir, $awal, $akhir, $wh, $awal, $akhir, $awal, $akhir));
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    public function getTraceabilityBuyingUnit($wh, $awal, $akhir){
        $sql = "SELECT  org2.OrgID id, org2.`Name` label
                FROM
                    ktv_supplychain_org_rel a
                    LEFT JOIN ktv_supplychain_org b ON a.ParentOrgId=b.SupplychainID
                    LEFT JOIN ktv_warehouse c ON c.WarehouseID=b.OrgID
                    LEFT JOIN ktv_supplychain_org kso2 ON kso2.SupplychainID=a.ChildOrgId
                    LEFT JOIN view_supplychain_org org2 ON org2.SupplychainID=kso2.SupplychainID
                    LEFT JOIN ktv_supplychain_org_rel rel2 ON rel2.ParentOrgId=a.ChildOrgId
                    LEFT JOIN ktv_supplychain_org kso1 ON kso1.SupplychainID=rel2.ChildOrgId
                    LEFT JOIN view_supplychain_org org1 ON org1.SupplychainID=kso1.SupplychainID
                WHERE b.OrgType='warehouse'  AND b.OrgID=?
                    AND !(a.EndDate < ? OR a.StartDate > ?)
                    AND !(rel2.EndDate < ? OR rel2.StartDate > ?) AND kso2.OrgType!='koperasi'
                GROUP BY org2.SupplychainID
                UNION
                SELECT  org1.OrgID id, org1.`Name` label
                FROM
                    ktv_supplychain_org_rel a
                    LEFT JOIN ktv_supplychain_org b ON a.ParentOrgId=b.SupplychainID
                    LEFT JOIN ktv_warehouse c ON c.WarehouseID=b.OrgID
                    LEFT JOIN ktv_supplychain_org kso2 ON kso2.SupplychainID=a.ChildOrgId
                    LEFT JOIN view_supplychain_org org2 ON org2.SupplychainID=kso2.SupplychainID
                    LEFT JOIN ktv_supplychain_org_rel rel2 ON rel2.ParentOrgId=a.ChildOrgId
                    LEFT JOIN ktv_supplychain_org kso1 ON kso1.SupplychainID=rel2.ChildOrgId
                    LEFT JOIN view_supplychain_org org1 ON org1.SupplychainID=kso1.SupplychainID
                WHERE b.OrgType='warehouse'  AND b.OrgID=?
                    AND !(a.EndDate < ? OR a.StartDate > ?)
                    AND !(rel2.EndDate < ? OR rel2.StartDate > ?) AND kso1.OrgType!='koperasi'
                GROUP BY org1.SupplychainID";
        $query = $this->db->query($sql, array($wh, $awal, $akhir, $awal, $akhir, $wh, $awal, $akhir, $awal, $akhir));
        $result['data'] = $query->result_array();
        //$result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    

    public function readPrintoutListPartner($district = '') {
        if($_SESSION['PartnerID']==''){
            $p1 = "/*"; $p2 = "*/";
        }else{
            $p1 = ""; $p2 = "";
        }
        if ($district != '') {
            $left = 'LEFT JOIN ktv_district_partner kdpp ON kdpp.PartnerID=kpp.PartnerID
               LEFT JOIN ktv_district kd ON kdpp.DistrictID=kd.DistrictID';
            $add = "and District='$district'";
        }
        $sql = "
            select kpp.PartnerID as id, kpp.PartnerName as label
            from ktv_program_partner kpp
            $left
            where kpp.PartnerID>0 $p1 AND kpp.PartnerID=? $p2 $add
            GROUP BY kpp.PartnerID";
        $query = $this->db->query($sql, array($_SESSION['PartnerID']));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function ReadUnitFrom($OrgID, $Start, $End)
    {
        $sql = "SELECT DISTINCT
                    IF(1_orgid=$OrgID, NULL, IF(1_destorgid=$OrgID, 1_orgid, IF(2_orgid=$OrgID, IFNULL(1_orgid,NULL), IF(2_destorgid=$OrgID, 2_orgid, IF(wh_orgid=$OrgID, 2_orgid, NULL))))) id,
                    IF(1_orgid=$OrgID, NULL, IF(1_destorgid=$OrgID, 1_name, IF(2_orgid=$OrgID, IFNULL(1_name,NULL), IF(2_destorgid=$OrgID, 2_name, IF(wh_orgid=$OrgID, 2_name, NULL))))) label
                FROM rpt_traceability r
                WHERE (1_orgid=$OrgID OR 1_destorgid=$OrgID OR 2_orgid=$OrgID OR 2_destorgid=$OrgID OR wh_orgid=$OrgID) AND IFNULL(1_date,2_date) BETWEEN ? AND ?";
        $query = $this->db->query($sql, array($Start, $End));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function ReadUnitDestination($OrgID, $Start, $End)
    {
        $sql = "SELECT DISTINCT
                    IF(1_orgid=$OrgID, 1_destorgid, IF(2_orgid=$OrgID, 2_destorgid, NULL)) id,
                    IF(1_orgid=$OrgID, 1_destname,  IF(2_orgid=$OrgID, 2_destname,  NULL)) label
                FROM rpt_traceability r
                WHERE (1_orgid=$OrgID OR 1_destorgid=$OrgID OR 2_orgid=$OrgID OR 2_destorgid=$OrgID OR wh_orgid=$OrgID) AND IFNULL(1_date,2_date) BETWEEN ? AND ?";
        $query = $this->db->query($sql, array($Start, $End));
        $result['data'] = $query->result_array();
        return $result;
    }

}
?>