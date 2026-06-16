<?php
class Mreport_traceability_transaction extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_getUserDetail();
        
    }
    
    public function _getUserDetail(){
        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, op.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_supplychain_org o ON o.OrgID=s.ObjID AND o.OrgType=s.ObjType
                    LEFT JOIN ktv_supplychain_org_partner op ON op.SupplychainID=o.SupplychainID
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array(@$_SESSION['userid']))->result_array();
        $staff = @$query[0];
        if(@$staff['ObjType']=='private' || @$staff['ObjType']=='private'){
            $this->PartnerID = @$staff['ObjID'];
        }else{
            $this->PartnerID = @$staff['PartnerID'];
        }
        $this->DistrictID = @$staff['DistrictID'];
        $this->SupplychainID = @$staff['SupplychainID'];
        $this->OrgID = @$staff['ObjID'];
        $this->OrgType = @$staff['ObjType'];
    }
    
    public function readStoreGridTransactionMill($pSearch,$start,$limit,$sortingField,$sortingDir, $Mill, $DO, $Agent, $DateFrom, $DateTo){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';
        
        $m1 = $Mill=='' || $Mill=='false' ? '/*' : '';
        $m2 = $Mill=='' || $Mill=='false' ? '*/' : '';
        $d1 = $DO=='' || $DO=='false' ? '/*' : '';
        $d2 = $DO=='' || $DO=='false' ? '*/' : '';
        $a1 = $Agent=='' || $Agent=='false' ? '/*' : '';
        $a2 = $Agent=='' || $Agent=='false' ? '*/' : '';
        
        
        $sql="SELECT SQL_CALC_FOUND_ROWS
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID) SupplyTransID,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyType, st1.SupplyType) SupplyType,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchNumber, sb1.SupplyBatchNumber) SupplyBatchNumber,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) Name,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchID, sb1.SupplyBatchID) AgentBatchID,  
        IF(st3.SupplyTransID IS NOT NULL, vso2.`OrgID`, vso1.`OrgID`) FarmerID,
        v1.Village,
        sd1.SubDistrict,
        d1.District,            
        IF(st3.SupplyTransID IS NOT NULL, st2.DateTransaction, st1.DateTransaction) DateTransaction,
        IF(st3.SupplyTransID IS NOT NULL, IFNULL(st2.VolumeBruto1,0) - IFNULL(st2.VolumeBruto2, 0), IFNULL(st1.VolumeBruto1,0) - IFNULL(st1.VolumeBruto2, 0)) Bruto,
        IF(st3.SupplyTransID IS NOT NULL, st2.VolumeNetto, st1.VolumeNetto) Netto,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyID, st1.SupplyID) SupplyID,
        IF(st3.SupplyTransID IS NOT NULL, sb1.DeliveryDate, st1.DateTransaction) DeliveryDate,
        IF(st3.SupplyTransID IS NOT NULL, 
            vso1.`Name`,
            IFNULL(CONCAT('[',m.MemberDisplayID,'] ', m.MemberName), CONCAT('[',nf.FarmerID,'] ',nf.FarmerName))
        ) BatchFrom,
        IF(st3.SupplyTransID IS NOT NULL, 
            IF(st2.DateTransaction IS NULL, 'Pending', IFNULL(sb2.SupplyDestStatus, 'Open')),
            IF(st1.DateTransaction IS NULL, 'Pending', IFNULL(sb1.SupplyDestStatus, 'Open'))
        ) SupplyBatchStatus,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) BatchTo
    FROM
        ktv_supplychain_transaction st1
        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber AND st2.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber AND st3.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
        LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
        LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
        
        LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
        LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
        LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st1.SupplyID
    -- MILL
    LEFT JOIN ktv_village v1 ON v1.VillageID=IF(st3.SupplyTransID IS NOT NULL, vso3.VillageID, vso2.VillageID)
        LEFT JOIN ktv_subdistrict sd1 ON sd1.SubDistrictID=v1.SubDistrictID
        LEFT JOIN ktv_district d1 ON d1.DistrictID=sd1.DistrictID   

    WHERE 
        st1.SupplyType='Farmer'
                $m1 AND IFNULL(st3.SupplychainID, IFNULL(st2.SupplychainID, st1.SupplychainID))=? $m2
                $d1 AND IFNULL(st2.SupplychainID, st1.SupplychainID=? $d2
                $a1 AND st1.SupplychainID=? $a2
                AND st1.DateTransaction BETWEEN ? AND ?
            GROUP BY IFNULL(st3.SupplyTransID, st2.SupplyTransID)
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array($Mill, $DO, $Agent, $DateFrom, $DateTo, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
    
    

public function readStoreGridTransactionDO($pSearch,$start,$limit,$sortingField,$sortingDir, $Mill, $DO, $Agent, $DateFrom, $DateTo){
    if($sortingField == "") $sortingField = 'DateTransaction';
    if($sortingDir == "") $sortingDir = 'DESC';
    
    $m1 = $Mill=='' || $Mill=='false' ? '/*' : '';
    $m2 = $Mill=='' || $Mill=='false' ? '*/' : '';
    $d1 = $DO=='' || $DO=='false' ? '/*' : '';
    $d2 = $DO=='' || $DO=='false' ? '*/' : '';
    $a1 = $Agent=='' || $Agent=='false' ? '/*' : '';
    $a2 = $Agent=='' || $Agent=='false' ? '*/' : '';
    
    
    $sql="SELECT SQL_CALC_FOUND_ROWS
    IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID) SupplyTransID,
    IF(st3.SupplyTransID IS NOT NULL, st2.SupplyType, st1.SupplyType) SupplyType,
    IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchNumber, sb1.SupplyBatchNumber) SupplyBatchNumber,
    IF(st3.SupplyTransID IS NOT NULL, vso2.`Name`, vso1.`Name`) Name,       
    IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchID, sb1.SupplyBatchID) MillBatchID,  
    IF(st3.SupplyTransID IS NOT NULL, vso2.`OrgID`, vso1.`OrgID`) DoID,
    v1.Village,
    sd1.SubDistrict,
    d1.District,            
    IF(st3.SupplyTransID IS NOT NULL, st2.DateTransaction, st1.DateTransaction) DateTransaction,
    IF(st3.SupplyTransID IS NOT NULL, IFNULL(st2.VolumeBruto1,0) - IFNULL(st2.VolumeBruto2, 0), IFNULL(st1.VolumeBruto1,0) - IFNULL(st1.VolumeBruto2, 0)) Bruto,
    IF(st3.SupplyTransID IS NOT NULL, st2.VolumeNetto, st1.VolumeNetto) Netto,
    IF(st3.SupplyTransID IS NOT NULL, st2.SupplyID, st1.SupplyID) SupplyID,
    IF(st3.SupplyTransID IS NOT NULL, sb1.DeliveryDate, st1.DateTransaction) DeliveryDate,
    IF(st3.SupplyTransID IS NOT NULL, 
        vso1.`Name`,
        IFNULL(CONCAT('[',m.MemberDisplayID,'] ', m.MemberName), CONCAT('[',nf.FarmerID,'] ',nf.FarmerName))
    ) BatchFrom,
    IF(st3.SupplyTransID IS NOT NULL, 
        IF(st2.DateTransaction IS NULL, 'Pending', IFNULL(sb2.SupplyDestStatus, 'Open')),
        IF(st1.DateTransaction IS NULL, 'Pending', IFNULL(sb1.SupplyDestStatus, 'Open'))
    ) SupplyBatchStatus,
    IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) BatchTo
FROM
    ktv_supplychain_transaction st1
    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber AND st2.SupplyType='Batch'
    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber AND st3.SupplyType='Batch'
    LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
    LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
    
    LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
    LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
    LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st1.SupplyID
    -- DO
    LEFT JOIN ktv_village v1 ON v1.VillageID=IF(st3.SupplyTransID IS NOT NULL, vso2.VillageID, vso1.VillageID)
    
    LEFT JOIN ktv_subdistrict sd1 ON sd1.SubDistrictID=v1.SubDistrictID
    LEFT JOIN ktv_district d1 ON d1.DistrictID=sd1.DistrictID   

WHERE 
    st1.SupplyType='Farmer'
            $m1 AND IFNULL(st3.SupplychainID, IFNULL(st2.SupplychainID, st1.SupplychainID))=? $m2
            $d1 AND IFNULL(st2.SupplychainID, st1.SupplychainID=? $d2
            $a1 AND st1.SupplychainID=? $a2
            AND st1.DateTransaction BETWEEN ? AND ?
        GROUP BY IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID)
        ORDER BY $sortingField $sortingDir
        LIMIT ?,?";
    $query = $this->db->query($sql, array($Mill, $DO, $Agent, $DateFrom, $DateTo, intval($start), intval($limit)));
    //echo "<pre>".$this->db->last_query();exit;
    $result['data'] = $query->result_array();

    $query = $this->db->query('SELECT FOUND_ROWS() AS total');
    $result['total'] = $query->row()->total;

    return $result;
}

      
    public function readStoreGridTransactionAgent($pSearch,$start,$limit,$sortingField,$sortingDir, $Mill, $DO, $Agent, $DateFrom, $DateTo){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';
        
        $m1 = $Mill=='' || $Mill=='false' ? '/*' : '';
        $m2 = $Mill=='' || $Mill=='false' ? '*/' : '';
        $d1 = $DO=='' || $DO=='false' ? '/*' : '';
        $d2 = $DO=='' || $DO=='false' ? '*/' : '';
        $a1 = $Agent=='' || $Agent=='false' ? '/*' : '';
        $a2 = $Agent=='' || $Agent=='false' ? '*/' : '';
        
        
        $sql="SELECT SQL_CALC_FOUND_ROWS
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID) SupplyTransID,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyType, st1.SupplyType) SupplyType,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchNumber, sb1.SupplyBatchNumber) SupplyBatchNumber,
        IF(st3.SupplyTransID IS NOT NULL, vso1.`Name`, NULL) Name,           
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchID, sb1.SupplyBatchID) DoBatchID,  
        IF(st3.SupplyTransID IS NOT NULL, vso2.`OrgID`, vso1.`OrgID`) AgentID,
        v1.Village,
        sd1.SubDistrict,
        d1.District,            
        IF(st3.SupplyTransID IS NOT NULL, st2.DateTransaction, st1.DateTransaction) DateTransaction,
        IF(st3.SupplyTransID IS NOT NULL, IFNULL(st2.VolumeBruto1,0) - IFNULL(st2.VolumeBruto2, 0), IFNULL(st1.VolumeBruto1,0) - IFNULL(st1.VolumeBruto2, 0)) Bruto,
        IF(st3.SupplyTransID IS NOT NULL, st2.VolumeNetto, st1.VolumeNetto) Netto,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyID, st1.SupplyID) SupplyID,
        IF(st3.SupplyTransID IS NOT NULL, sb1.DeliveryDate, st1.DateTransaction) DeliveryDate,
        IF(st3.SupplyTransID IS NOT NULL, 
            vso1.`Name`,
            IFNULL(CONCAT('[',m.MemberDisplayID,'] ', m.MemberName), CONCAT('[',nf.FarmerID,'] ',nf.FarmerName))
        ) BatchFrom,
        IF(st3.SupplyTransID IS NOT NULL, 
            IF(st2.DateTransaction IS NULL, 'Pending', IFNULL(sb2.SupplyDestStatus, 'Open')),
            IF(st1.DateTransaction IS NULL, 'Pending', IFNULL(sb1.SupplyDestStatus, 'Open'))
        ) SupplyBatchStatus,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) BatchTo
    FROM
        ktv_supplychain_transaction st1
        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber AND st2.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber AND st3.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
        LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
        LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
        
        LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
        LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
        LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st1.SupplyID
        -- AGENT
    LEFT JOIN ktv_village v1 ON v1.VillageID=vso1.VillageID
        LEFT JOIN ktv_subdistrict sd1 ON sd1.SubDistrictID=v1.SubDistrictID
        LEFT JOIN ktv_district d1 ON d1.DistrictID=sd1.DistrictID   

    WHERE 
        st1.SupplyType='Farmer'
                $m1 AND IFNULL(st3.SupplychainID, IFNULL(st2.SupplychainID, st1.SupplychainID))=? $m2
                $d1 AND IFNULL(st2.SupplychainID, st1.SupplychainID=? $d2
                $a1 AND st1.SupplychainID=? $a2
                AND st1.DateTransaction BETWEEN ? AND ?
            GROUP BY st1.SupplyTransID HAVING SupplyTransID IS NOT NULL
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array($Mill, $DO, $Agent, $DateFrom, $DateTo, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
    
    ///////////////////////////////////////////////////
    
    function readTransactionSetting(){
        $returs = array(
            'OrgID' => $this->OrgID,
            'OrgType' => $this->OrgType,
            'SupplychainID' => $this->SupplychainID,
        );
        return $returs;
    }

    public function readStoreGridTransactionMill_Excell($start,$limit,$sortingField,$sortingDir, $Mill, $DO, $Agent, $DateFrom, $DateTo){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';
        
        $m1 = $Mill=='' || $Mill=='null' ? '/*' : '';
        $m2 = $Mill=='' || $Mill=='null' ? '*/' : '';
        $d1 = $DO=='' || $DO=='null' ? '/*' : '';
        $d2 = $DO=='' || $DO=='null' ? '*/' : '';
        $a1 = $Agent=='' || $Agent=='null' ? '/*' : '';
        $a2 = $Agent=='' || $Agent=='null' ? '*/' : '';
        
        
        $sql="SELECT SQL_CALC_FOUND_ROWS
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID) SupplyTransID,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyType, st1.SupplyType) SupplyType,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchNumber, sb1.SupplyBatchNumber) SupplyBatchNumber,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) Name,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchID, sb1.SupplyBatchID) AgentBatchID,  
        IF(st3.SupplyTransID IS NOT NULL, vso2.`OrgID`, vso1.`OrgID`) FarmerID,
        v1.Village,
        sd1.SubDistrict,
        d1.District,            
        IF(st3.SupplyTransID IS NOT NULL, st2.DateTransaction, st1.DateTransaction) DateTransaction,
        IF(st3.SupplyTransID IS NOT NULL, IFNULL(st2.VolumeBruto1,0) - IFNULL(st2.VolumeBruto2, 0), IFNULL(st1.VolumeBruto1,0) - IFNULL(st1.VolumeBruto2, 0)) Bruto,
        IF(st3.SupplyTransID IS NOT NULL, st2.VolumeNetto, st1.VolumeNetto) Netto,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyID, st1.SupplyID) SupplyID,
        IF(st3.SupplyTransID IS NOT NULL, sb1.DeliveryDate, st1.DateTransaction) DeliveryDate,
        IF(st3.SupplyTransID IS NOT NULL, 
            vso1.`Name`,
            IFNULL(CONCAT('[',m.MemberDisplayID,'] ', m.MemberName), CONCAT('[',nf.FarmerID,'] ',nf.FarmerName))
        ) BatchFrom,
        IF(st3.SupplyTransID IS NOT NULL, 
            IF(st2.DateTransaction IS NULL, 'Pending', IFNULL(sb2.SupplyDestStatus, 'Open')),
            IF(st1.DateTransaction IS NULL, 'Pending', IFNULL(sb1.SupplyDestStatus, 'Open'))
        ) SupplyBatchStatus,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) BatchTo
    FROM
        ktv_supplychain_transaction st1
        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber AND st2.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber AND st3.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
        LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
        LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
        
        LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
        LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
        LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st1.SupplyID
    -- MILL
    LEFT JOIN ktv_village v1 ON v1.VillageID=IF(st3.SupplyTransID IS NOT NULL, vso3.VillageID, vso2.VillageID)
        LEFT JOIN ktv_subdistrict sd1 ON sd1.SubDistrictID=v1.SubDistrictID
        LEFT JOIN ktv_district d1 ON d1.DistrictID=sd1.DistrictID   

    WHERE 
        st1.SupplyType='Farmer'
                $m1 AND IFNULL(st3.SupplychainID, IFNULL(st2.SupplychainID, st1.SupplychainID))=? $m2
                $d1 AND IFNULL(st2.SupplychainID, st1.SupplychainID=? $d2
                $a1 AND st1.SupplychainID=? $a2
                AND st1.DateTransaction BETWEEN ? AND ?
            GROUP BY IFNULL(st3.SupplyTransID, st2.SupplyTransID)
            ORDER BY $sortingField $sortingDir";

        $query = $this->db->query($sql, array($Mill, $DO, $Agent, $DateFrom, $DateTo, intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function readStoreGridTransactionDO_Excell($start,$limit,$sortingField,$sortingDir, $Mill, $DO, $Agent, $DateFrom, $DateTo){
        
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';
        
        $m1 = $Mill=='' || $Mill=='null' ? '/*' : '';
        $m2 = $Mill=='' || $Mill=='null' ? '*/' : '';
        $d1 = $DO=='' || $DO=='null' ? '/*' : '';
        $d2 = $DO=='' || $DO=='null' ? '*/' : '';
        $a1 = $Agent=='' || $Agent=='null' ? '/*' : '';
        $a2 = $Agent=='' || $Agent=='null' ? '*/' : '';

        $sql="SELECT SQL_CALC_FOUND_ROWS
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID) SupplyTransID,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyType, st1.SupplyType) SupplyType,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchNumber, sb1.SupplyBatchNumber) SupplyBatchNumber,
        IF(st3.SupplyTransID IS NOT NULL, vso2.`Name`, vso1.`Name`) Name,       
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchID, sb1.SupplyBatchID) MillBatchID,  
        IF(st3.SupplyTransID IS NOT NULL, vso2.`OrgID`, vso1.`OrgID`) DoID,
        v1.Village,
        sd1.SubDistrict,
        d1.District,            
        IF(st3.SupplyTransID IS NOT NULL, st2.DateTransaction, st1.DateTransaction) DateTransaction,
        IF(st3.SupplyTransID IS NOT NULL, IFNULL(st2.VolumeBruto1,0) - IFNULL(st2.VolumeBruto2, 0), IFNULL(st1.VolumeBruto1,0) - IFNULL(st1.VolumeBruto2, 0)) Bruto,
        IF(st3.SupplyTransID IS NOT NULL, st2.VolumeNetto, st1.VolumeNetto) Netto,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyID, st1.SupplyID) SupplyID,
        IF(st3.SupplyTransID IS NOT NULL, sb1.DeliveryDate, st1.DateTransaction) DeliveryDate,
        IF(st3.SupplyTransID IS NOT NULL, 
            vso1.`Name`,
            IFNULL(CONCAT('[',m.MemberDisplayID,'] ', m.MemberName), CONCAT('[',nf.FarmerID,'] ',nf.FarmerName))
        ) BatchFrom,
        IF(st3.SupplyTransID IS NOT NULL, 
            IF(st2.DateTransaction IS NULL, 'Pending', IFNULL(sb2.SupplyDestStatus, 'Open')),
            IF(st1.DateTransaction IS NULL, 'Pending', IFNULL(sb1.SupplyDestStatus, 'Open'))
        ) SupplyBatchStatus,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) BatchTo
    FROM
        ktv_supplychain_transaction st1
        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber AND st2.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber AND st3.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
        LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
        LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
        
        LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
        LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
        LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st1.SupplyID
        -- DO
        LEFT JOIN ktv_village v1 ON v1.VillageID=IF(st3.SupplyTransID IS NOT NULL, vso2.VillageID, vso1.VillageID)
        
        LEFT JOIN ktv_subdistrict sd1 ON sd1.SubDistrictID=v1.SubDistrictID
        LEFT JOIN ktv_district d1 ON d1.DistrictID=sd1.DistrictID   
    
    WHERE 
        st1.SupplyType='Farmer'
    $m1 AND IFNULL(st3.SupplychainID, IFNULL(st2.SupplychainID, st1.SupplychainID))=? $m2
    $d1 AND IFNULL(st2.SupplychainID, st1.SupplychainID=? $d2 $d1)$d2
    $a1 AND st1.SupplychainID=? $a2
        AND st1.DateTransaction BETWEEN ? AND ?
    GROUP BY IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID)
    ORDER BY $sortingField $sortingDir";
    ;

    
    $query = $this->db->query($sql, array($Mill, $DO, $Agent, $DateFrom, $DateTo, intval($start), intval($limit)));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;

        ///

    }



    public function readStoreGridTransactionAgent_Excell($start,$limit,$sortingField,$sortingDir, $Mill, $DO, $Agent, $DateFrom, $DateTo){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';
        
        $m1 = $Mill=='' || $Mill=='null' ? '/*' : '';
        $m2 = $Mill=='' || $Mill=='null' ? '*/' : '';
        $d1 = $DO=='' || $DO=='null' ? '/*' : '';
        $d2 = $DO=='' || $DO=='null' ? '*/' : '';
        $a1 = $Agent=='' || $Agent=='null' ? '/*' : '';
        $a2 = $Agent=='' || $Agent=='null' ? '*/' : '';
        
        
        $sql="SELECT SQL_CALC_FOUND_ROWS
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyTransID, st1.SupplyTransID) SupplyTransID,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyType, st1.SupplyType) SupplyType,
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchNumber, sb1.SupplyBatchNumber) SupplyBatchNumber,
        IF(st3.SupplyTransID IS NOT NULL, vso1.`Name`, NULL) Name,           
        IF(st3.SupplyTransID IS NOT NULL, sb2.SupplyBatchID, sb1.SupplyBatchID) DoBatchID,  
        IF(st3.SupplyTransID IS NOT NULL, vso2.`OrgID`, vso1.`OrgID`) AgentID,
        v1.Village,
        sd1.SubDistrict,
        d1.District,            
        IF(st3.SupplyTransID IS NOT NULL, st2.DateTransaction, st1.DateTransaction) DateTransaction,
        IF(st3.SupplyTransID IS NOT NULL, IFNULL(st2.VolumeBruto1,0) - IFNULL(st2.VolumeBruto2, 0), IFNULL(st1.VolumeBruto1,0) - IFNULL(st1.VolumeBruto2, 0)) Bruto,
        IF(st3.SupplyTransID IS NOT NULL, st2.VolumeNetto, st1.VolumeNetto) Netto,
        IF(st3.SupplyTransID IS NOT NULL, st2.SupplyID, st1.SupplyID) SupplyID,
        IF(st3.SupplyTransID IS NOT NULL, sb1.DeliveryDate, st1.DateTransaction) DeliveryDate,
        IF(st3.SupplyTransID IS NOT NULL, 
            vso1.`Name`,
            IFNULL(CONCAT('[',m.MemberDisplayID,'] ', m.MemberName), CONCAT('[',nf.FarmerID,'] ',nf.FarmerName))
        ) BatchFrom,
        IF(st3.SupplyTransID IS NOT NULL, 
            IF(st2.DateTransaction IS NULL, 'Pending', IFNULL(sb2.SupplyDestStatus, 'Open')),
            IF(st1.DateTransaction IS NULL, 'Pending', IFNULL(sb1.SupplyDestStatus, 'Open'))
        ) SupplyBatchStatus,
        IF(st3.SupplyTransID IS NOT NULL, vso3.`Name`, vso2.`Name`) BatchTo
    FROM
        ktv_supplychain_transaction st1
        LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=st1.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchNumber AND st2.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber AND st3.SupplyType='Batch'
        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
        LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=st3.SupplychainID
        LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=st2.SupplychainID
        
        LEFT JOIN view_supplychain_org vso1 ON vso1.SupplychainID=st1.SupplychainID
        LEFT JOIN ktv_members m ON (m.MemberID=st1.SupplyID OR m.MemberDisplayID=st1.SupplyID)
        LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st1.SupplyID
        -- AGENT
    LEFT JOIN ktv_village v1 ON v1.VillageID=vso1.VillageID
        LEFT JOIN ktv_subdistrict sd1 ON sd1.SubDistrictID=v1.SubDistrictID
        LEFT JOIN ktv_district d1 ON d1.DistrictID=sd1.DistrictID   

    WHERE 
        st1.SupplyType='Farmer'
                $m1 AND IFNULL(st3.SupplychainID, IFNULL(st2.SupplychainID, st1.SupplychainID))=? $m2
                $d1 AND IFNULL(st2.SupplychainID, st1.SupplychainID=? $d2
                $a1 AND st1.SupplychainID=? $a2
                AND st1.DateTransaction BETWEEN ? AND ?
            GROUP BY st1.SupplyTransID HAVING SupplyTransID IS NOT NULL
            ORDER BY $sortingField $sortingDir";
        $query = $this->db->query($sql, array($Mill, $DO, $Agent, $DateFrom, $DateTo, intval($start), intval($limit)));
     //   echo "<pre>".$this->db->last_query();exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
    
    
    public function readFarmerGridList($TransID,$pSearch,$start,$limit,$sortingField,$sortingDir){

        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';

                $sql="SELECT
                IF(st.SUpplyType!='Batch', st.DateTransaction, IF(st2.SupplyType!='Batch', st2.DateTransaction, st3.DateTransaction)) DateTransaction,
                IFNULL(m.MemberName, nf.FarmerName) FarmerName,
                IF(st.SUpplyType!='Batch', IF(st.PlotNr IS NULL || st.PlotNr=0,1,st.PlotNr), IF(st2.SupplyType!='Batch', IF(st2.PlotNr IS NULL || st2.PlotNr=0,1,st2.PlotNr), IF(st3.PlotNr IS NULL || st3.PlotNr=0,1,st3.PlotNr))) PlotNr,
                v.Village,
                IF(st.SUpplyType!='Batch', IFNULL(st.VolumeBruto1,0)-IFNULL(st.VolumeBruto2,0), IF(st2.SupplyType!='Batch', IFNULL(st2.VolumeBruto1,0)-IFNULL(st2.VolumeBruto2,0), IFNULL(st3.VolumeBruto1,0)-IFNULL(st3.VolumeBruto2,0))) VolumeBruto,
                IF(st.SUpplyType!='Batch', st.VolumeNetto, IF(st2.SupplyType!='Batch', st2.VolumeNetto, st3.VolumeNetto)) VolumeNetto,
                IF(st.SUpplyType!='Batch', st.Bjr, IF(st2.SupplyType!='Batch', st2.Bjr, st3.Bjr)) Bjr,
                IF(st.SUpplyType!='Batch', st.NumberPackage, IF(st2.SupplyType!='Batch', st2.NumberPackage, st3.NumberPackage)) Tandan
                
            FROM
                ktv_supplychain_transaction st 
                LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID AND st2.SupplyType='Batch'
                LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                LEFT JOIN ktv_members m ON m.MemberDisplayID=IF(st.SUpplyType!='Batch', st.SupplyID, IF(st2.SupplyType!='Batch', st2.SupplyID, st3.SupplyID))
                LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=IF(st.SUpplyType!='Batch', st.SupplyID, IF(st2.SupplyType!='Batch', st2.SupplyID, st3.SupplyID))
                LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=IF(st.SUpplyType!='Batch', IF(st.PlotNr IS NULL || st.PlotNr=0,1,st.PlotNr), IF(st2.SupplyType!='Batch', IF(st2.PlotNr IS NULL || st2.PlotNr=0,1,st2.PlotNr), IF(st3.PlotNr IS NULL || st3.PlotNr=0,1,st3.PlotNr)))
                
                
                LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, IFNULL(m.VillageID,nf.FarmerVillageID))
                WHERE st.SupplyTransID=$TransID
                GROUP BY st.SupplyTransID
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";

       
        $query = $this->db->query($sql, array(intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    
    
    function readSupplyIDList($key, $tipe, $start, $limit){
        $d1 = $this->DistrictID=='' ? '/*' : '';
        $d2 = $this->DistrictID=='' ? '*/' : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    m.MemberID id, 
                    m.MemberDisplayID displayid,
                    m.MemberName name, m.Nin noktp, Village village, SubDistrict subdistrict, District district, IFNULL(fg.GroupName,'-') groupname,
                    m.Handphone handphone, CASE m.Gender WHEN 'm' THEN 'Male' WHEN 'f' THEN 'Female' ELSE '-' END gender
                FROM
                    ktv_members m
                    LEFT JOIN ktv_member_role mr ON mr.MemberID=m.MemberID
                    LEFT JOIN ktv_farmer_group fg ON fg.FarmerGroupID=m.FarmerGroupID
                    LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                WHERE m.StatusCode='active' AND mr.MRoleID=1 AND (m.MemberDisplayID LIKE ? OR m.Nin LIKE ? OR m.MemberName LIKE ?) $d1 AND SUBSTR(m.VillageID,1,4) IN (?) $d2 LIMIT ?,?";
        $query = $this->db->query($sql, array("%$key%", "%$key%", "%$key%", $this->DistrictID, intval($start), intval($limit)));
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }
    
    function createTransaction($SupplyTransID, $SupplyType, $SupplyID, $DateTransaction, $DateWeight1, $TimeWeight1, $DateWeight2, $TimeWeight2, $Weight1, $Weight2, $Tandan, $AdjustWeight1, $AdjustWeight2, $AdjustNetto, $Price, $TotalPayment, $FakturNumber, $userid){
        $date1 = $DateWeight1.' '.$TimeWeight1.':00';
        if($DateWeight2!=''){
            $date2 = $DateWeight2.' '.$TimeWeight2.':00';
        }else{
            $date2 = NULL;
        }
        $Tandan = $Tandan == ''?NULL:$Tandan;
        $AdjustWeight1 = $AdjustWeight1 == ''?NULL:$AdjustWeight1;
        $AdjustWeight2 = $AdjustWeight2 == ''?NULL:$AdjustWeight2;
        $sql = "INSERT INTO ktv_supplychain_transaction(SupplychainID, DateTransaction, SupplyType, SupplyID, DateBruto1, DateBruto2, VolumeBruto1, VolumeBruto2, NumberPackage, Pemotongan1, Pemotongan2, VolumeNetto, NetPrice, TotalPayment, FakturNumber, DateCreated, CreatedBy)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        $query = $this->db->query($sql, array($this->SupplychainID, $DateTransaction, $SupplyType, $SupplyID, $date1, $date2, $Weight1, $Weight2, $Tandan, $AdjustWeight1, $AdjustWeight2, $AdjustNetto, $Price, $TotalPayment, $FakturNumber, $_SESSION['userid']));
        
        if ($query) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to create record";
        }
        //echo "<pre> ini : ".print_r($results,1); exit;
        return $results;
    }

    function readComboFarmer(){
        $sql = "SELECT m.MemberName as NameFarmer FROM ktv_members m
        LEFT JOIN ktv_member_role mr ON mr.MemberID=m.MemberID 
        WHERE MRoleID=1";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    
    function readComboMill(){
        $sql = "SELECT vs.Name AS NameMill, SupplychainID as MillID FROM view_supplychain_org vs WHERE OrgType='mill' GROUP BY vs.Name";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    function readComboDO(){
        $sql = "SELECT vs.Name AS NameDO FROM view_supplychain_org vs WHERE OrgType='agent' GROUP BY vs.Name";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    function readComboAgent(){
        $sql = "SELECT vs.Name AS NameAgent FROM view_supplychain_org vs WHERE OrgType='agent' GROUP BY vs.Name";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }


    
    function readTransactionDetail($SupplyTransID){
        $sql = "SELECT 
                    st.*, IFNULL(m.MemberDisplayID, st.SupplyID) DisplaySupplyID, 
                    IFNULL(mv.Village, ov.Village) Village, IFNULL(fg.GroupName,'-') GroupName, IFNULL(m.MemberName, org2.Name) Name,
                    SUM(st2.VolumeNetto) DestWeight, SUM(IFNULL(st2.NumberPackage,0)) DestNumberPackage,
                    IFNULL(org3.Name, '-') AgentName
                FROM
                    ktv_supplychain_transaction st
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN ktv_village mv ON mv.VillageID=m.VillageID AND st.SupplyType='Farmer'
                    LEFT JOIN ktv_farmer_group fg ON fg.FarmerGroupID=m.FarmerGroupID
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN view_supplychain_org org2 ON org2.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN ktv_village ov ON ov.VillageID=org2.VillageID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID AND st2.SupplyType='Batch'
                    LEFT JOIN view_supplychain_org org3 ON org3.SupplychainID=sb3.SupplyOrgID
                WHERE st.SupplyTransID=? GROUP BY st.SupplyTransID";
        $query = $this->db->query($sql, array($SupplyTransID));
        return $query->row_array(0);
    }
    
    function updateTransaction($SupplyTransID, $SupplyType, $SupplyID, $DateTransaction, $DateWeight1, $TimeWeight1, $DateWeight2, $TimeWeight2, $Weight1, $Weight2, $Tandan, $AdjustWeight1, $AdjustWeight2, $AdjustNetto, $Price, $TotalPayment, $FakturNumber, $userid){
        $date1 = $DateWeight1.' '.$TimeWeight1.':00';
        if($DateWeight2!=''){
            $date2 = $DateWeight2.' '.$TimeWeight2.':00';
        }else{
            $date2 = NULL;
        }
        $Tandan = $Tandan == ''?NULL:$Tandan;
        $AdjustWeight1 = $AdjustWeight1 == ''?NULL:$AdjustWeight1;
        $AdjustWeight2 = $AdjustWeight2 == ''?NULL:$AdjustWeight2;
        $sql = "UPDATE ktv_supplychain_transaction SET DateTransaction=?, SupplyID=?, FakturNumber=?, DateBruto1=?, DateBruto2=?, VolumeBruto1=?, VolumeBruto2=?, NumberPackage=?, Pemotongan1=?, Pemotongan2=?, VolumeNetto=?, NetPrice=?, TotalPayment=?, DateUpdated=NOW(), LastModifiedBy=? WHERE SupplyTransID=?";
        $query = $this->db->query($sql, array($DateTransaction, $SupplyID, $FakturNumber, $date1, $date2, $Weight1, $Weight2, $Tandan, $AdjustWeight1, $AdjustWeight2, $AdjustNetto, $Price, $TotalPayment, $_SESSION['userid'], $SupplyTransID));
        
        if ($query) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to update record";
        }
        //echo "<pre> ini : ".print_r($results,1); exit;
        return $results;
    }
    
    function createBatch($SupplychainID){
        $sql = "INSERT INTO ktv_supplychain_batch(SupplyOrgID, DateCreated, CreatedBy) VALUES (?, NOW(), ?)";
        $query = $this->db->query($sql, array($this->SupplychainID, $_SESSION['userid']));
        $SupplyBatchID = $this->db->insert_id();
        
        if ($query) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Record created.";
            $results['SupplyBatchID'] = $SupplyBatchID;
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to create record";
        }
        //echo "<pre> ini : ".print_r($results,1); exit;
        return $results;
    }
    
    function deleteBatch($SupplyBatchID){
        $this->db->trans_start();
        $sql = "UPDATE ktv_supplychain_transaction SET SupplyBatchID=NULL WHERE SupplyBatchID=?";
        $query = $this->db->query($sql, array($SupplyBatchID));
        
        $sql = "DELETE FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
        $query = $this->db->query($sql, array($SupplyBatchID));
        
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    
    function updateBatch($SupplyBatchID, $SupplyBatchNumber, $DestPO, $SupplyBatchDate, $SupplyBatchTime, $DeliveryDate, $DeliveryTime, $EstimatedDate, $EstimatedTime, $VolumeBruto, $VolumeNetto, $SupplyDestOrgID, $DestWeight, $DestNumberPackage, $DestDriver, $DestDriverAddress, $DestDriverHandphone, $DesNoPolisi){
        $this->db->trans_start();
        $date1 = $SupplyBatchDate.' '.$SupplyBatchTime.':00';
        if($DeliveryDate!=''){
            $date2 = $DeliveryDate.' '.$DeliveryTime.':00';
        }else{
            $date2 = NULL;
        }
        if($EstimatedDate!=''){
            $date3 = $EstimatedDate.' '.$EstimatedTime.':00';
        }else{
            $date3 = NULL;
        }
        $DestWeight = $DestWeight == ''?NULL:$DestWeight;
        $DestNumberPackage = $DestNumberPackage == ''?NULL:$DestNumberPackage;
        $update = array(
            'SupplyBatchNumber' => $SupplyBatchNumber,
            'DestPO' => $DestPO,
            'SupplyBatchDate' => $date1,
            'DeliveryDate' => $date2,
            'EstimatedDate' => $date3,
            'VolumeBruto' => $VolumeBruto,
            'VolumeNetto' => $VolumeNetto,
            'SupplyDestOrgID' => $SupplyDestOrgID,
            'DestWeight' => $DestWeight,
            'DestNumberPackage' => $DestNumberPackage,
            'DestDriver' => $DestDriver,
            'DestDriverAddress' => $DestDriverAddress,
            'DestDriverhandphone' => $DestDriverhandphone,
            'DestNoPolisi' => $DestNoPolisi
        );
        $this->db->where('SupplyBatchID', $SupplyBatchID);
        $query = $this->db->update('ktv_supplychain_batch', $update);
        $this->createAutoBatch($SupplyBatchID);
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    
    public function readTransactionAvailableList($pSearch,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'ASC';
        
        $sql="SELECT 
                    st.SupplyTransID, st.SupplyType, st.DateTransaction, st.FakturNumber,
                    IF(st.SupplyType='Farmer', m.MemberName, org.Name) Name,
                    IF(st.SupplyType='Batch', SUM(st2.VolumeNetto), st.VolumeNetto) VolumeNetto,
                    CASE WHEN st.SupplyBatchID IS NULL AND st.DateTransaction IS NULL THEN 'Pending' WHEN st.SupplyBatchID IS NULL AND st.DateTransaction IS NOT NULL THEN 'Open' WHEN st.SupplyBatchID IS NOT NULL THEN 'Sent' END SupplyStatus 
                FROM 
                    ktv_supplychain_transaction st
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN view_supplychain_org org ON org.SupplychainID=st.SupplychainID
                WHERE st.SupplychainID=?
            GROUP BY st.SupplyTransID HAVING SupplyStatus='Open'
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array($this->SupplychainID, intval($start), intval($limit)));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
    
    public function createAutoBatch($SupplyBatchID){
        $sql = "SELECT * FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
        $query = $this->db->query($sql, array($SupplyBatchID));
        $Batch = $query->row();
        
        $sql = "INSERT INTO ktv_supplychain_transaction(SupplychainID, DateTransaction, SupplyType, SupplyID, VolumeBruto1, VolumeNetto, DateCreated, CreatedBy) VALUES(?, NULL, 'Batch', ?, ?, ?, NOW(), ?)";
        $insert = $this->db->query($sql, array($Batch->SupplyDestOrgID, $Batch->SupplyBatchNumber, $Batch->VolumeBruto, $Batch->VolumeNetto, 1));
        if ($insert) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Record created";
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to create record";
        }
    }
    
    public function TransactionToBatch($SupplyBatchID, $Trans){
        $this->db->trans_start();
        $Transaction = explode('|', $Trans);
        for($i=0; $i < count($Transaction); $i++){
            $sql = "UPDATE ktv_supplychain_transaction SET SupplyBatchID=? WHERE SupplyTransID=?";
            $query = $this->db->query($sql, array($SupplyBatchID, $Transaction[$i]));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['icon'] = "ext-mb-success";
            $results['info'] = "Success";
            $results['message'] = "Success";
        } else {
            $results['success'] = false;
            $results['icon'] = "ext-mb-error";
            $results['info'] = "Warning";
            $results['message'] = "Failed to add transaction";
        }
        return $results;
    }
    
    public function readTransactionBatchList($SupplyBatchID, $pSearch,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'ASC';
        
        $sql="SELECT 
                    st.SupplyTransID, st.SupplyType, st.DateTransaction, st.FakturNumber,
                    IF(st.SupplyType='Farmer', m.MemberName, org.Name) Name,
                    IF(st.SupplyType='Batch', SUM(st2.VolumeNetto), st.VolumeNetto) VolumeNetto,
                    IF(st.SupplyType='Batch', SUM(IFNULL(st2.NumberPackage,0)), IFNULL(st.NumberPackage,0)) NumberPackage,
                    CASE WHEN st.SupplyBatchID IS NULL AND st.DateTransaction IS NULL THEN 'Pending' WHEN st.SupplyBatchID IS NULL AND st.DateTransaction IS NOT NULL THEN 'Open' WHEN st.SupplyBatchID IS NOT NULL THEN 'Sent' END SupplyStatus 
                FROM 
                    ktv_supplychain_transaction st
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN view_supplychain_org org ON org.SupplychainID=st.SupplychainID
                WHERE st.SupplyBatchID=?
            GROUP BY st.SupplyTransID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array($SupplyBatchID, intval($start), intval($limit)));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
    
    public function readDestinationList(){
        $sql="SELECT org.SupplychainID id, org.Name name
                FROM ktv_supplychain_org_rel rel
                LEFT JOIN view_supplychain_org org ON org.SupplychainID=rel.ParentOrgId
                WHERE ChildOrgId=?";
        $query = $this->db->query($sql, array($this->SupplychainID));
        return $query->result_array();
    }
    
    public function readBatchGridList($pSearch,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'DeliveryDate';
        if($sortingDir == "") $sortingDir = 'DESC';
        $sql="SELECT 
                    sb.SupplyBatchID, sb.DeliveryDate, org.Name Destination, sb.VolumeNetto, sb.SupplyDestStatus
                FROM 
                    ktv_supplychain_batch sb
                    LEFT JOIN view_supplychain_org org ON org.SupplychainID=sb.SupplyDestOrgID
                WHERE sb.SupplyOrgID=?
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array($this->SupplychainID, intval($start), intval($limit)));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
}
?>