<?php
class Mtransaction_new extends CI_Model {

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
    
    function readTransactionSetting(){
        $returs = array(
            'OrgID' => $this->OrgID,
            'OrgType' => $this->OrgType,
            'SupplychainID' => $this->SupplychainID,
        );
        return $returs;
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
                LEFT JOIN ktv_members m ON (m.MemberDisplayID=IF(st.SUpplyType!='Batch', st.SupplyID, IF(st2.SupplyType!='Batch', st2.SupplyID, st3.SupplyID)) OR m.MemberID=IF(st.SUpplyType!='Batch', st.SupplyID, IF(st2.SupplyType!='Batch', st2.SupplyID, st3.SupplyID)))
                LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=IF(st.SUpplyType!='Batch', st.SupplyID, IF(st2.SupplyType!='Batch', st2.SupplyID, st3.SupplyID))
                LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=IF(st.SUpplyType!='Batch', IF(st.PlotNr IS NULL || st.PlotNr=0,1,st.PlotNr), IF(st2.SupplyType!='Batch', IF(st2.PlotNr IS NULL || st2.PlotNr=0,1,st2.PlotNr), IF(st3.PlotNr IS NULL || st3.PlotNr=0,1,st3.PlotNr)))
                
                
                LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, IFNULL(m.VillageID,nf.FarmerVillageID))
                WHERE st.SupplyTransID=$TransID
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";

       
        $query = $this->db->query($sql, array(intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function readTransactionGridList($pSearch,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC';
        
        $sql="SELECT 
                    st.SupplyTransID, st.SupplyType, IFNULL(sb2.DeliveryDate, st.DateTransaction) DateTransaction, st.FakturNumber,
                    IFNULL(org2.Name, '-') DOName,
                    IFNULL(org3.Name, '-') AgentName,
                    st.VolumeNetto VolumeNetto,
                    IFNULL(sb2.DestWeight, IFNULL(st.VolumeBruto1, 0) - IFNULL(st.VolumeBruto2, 0)) VolumeBruto,
                    fr.total_farmer Farmers,
                    CASE WHEN st.SupplyBatchID IS NULL AND st.DateTransaction IS NULL THEN 'Pending' WHEN st.SupplyBatchID IS NULL AND st.DateTransaction IS NOT NULL THEN 'Open' WHEN st.SupplyBatchID IS NOT NULL THEN 'Sent' END SupplyStatus 
                FROM 
                    ktv_supplychain_transaction st
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN view_supplychain_org org ON org.SupplychainID=st.SupplychainID
                    LEFT JOIN view_supplychain_org org2 ON org2.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID AND st2.SupplyType='Batch'
                    LEFT JOIN view_supplychain_org org3 ON org3.SupplychainID=sb3.SupplyOrgID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                    LEFT JOIN (
                        SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, nf.FarmerID)) total_farmer
                        FROM
                            ktv_supplychain_transaction st1
                            LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st1.SupplyID 
                            LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                            LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID 
                            LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                            LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                            LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID))
                        WHERE st1.SupplychainID = ?
                        GROUP BY st1.SupplyTransID
                    ) fr ON fr.transid=st.SupplyTransID
                WHERE st.SupplychainID=?
            GROUP BY st.SupplyTransID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array($this->SupplychainID, $this->SupplychainID, intval($start), intval($limit)));
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
        $sql = "SELECT kso.SupplychainID 
                FROM sys_user u
                    LEFT JOIN ktv_persons p ON p.UserID=u.UserId
                    LEFT JOIN ktv_staffs s ON s.PersonID=p.PersonID
                    LEFT JOIN ktv_supplychain_org kso ON kso.OrgID=s.ObjID AND kso.OrgType=s.ObjType
                WHERE u.UserId=?";
        $SupplychainID = $this->db->query($sql, array($_SESSION['userid']))->row()->SupplychainID;
        $s1 = $SupplychainID=='' ? "/*" : "";
        $s2 = $SupplychainID=='' ? "*/" : "";
        
        $sql = "SELECT vso.SupplychainID id, vso.`Name` label
                FROM ktv_supplychain_org_rel kso
                LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=kso.ParentOrgId
                WHERE vso.OrgType = 'mill' $s1 AND ( kso.ParentOrgId=? $s2 OR kso.ChildOrgId=?)
                GROUP BY vso.SupplychainID";
        $query = $this->db->query($sql, array($SupplychainID, $SupplychainID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    function readComboDO(){
        $sql = "SELECT kso.SupplychainID 
                FROM sys_user u
                    LEFT JOIN ktv_persons p ON p.UserID=u.UserId
                    LEFT JOIN ktv_staffs s ON s.PersonID=p.PersonID
                    LEFT JOIN ktv_supplychain_org kso ON kso.OrgID=s.ObjID AND kso.OrgType=s.ObjType
                WHERE u.UserId=?";
        $SupplychainID = $this->db->query($sql, array($_SESSION['userid']))->row()->SupplychainID;
        $s1 = $SupplychainID=='' ? "/*" : "";
        $s2 = $SupplychainID=='' ? "*/" : "";
        
        $sql = "SELECT vso2.SupplychainID id, vso2.`Name` label
                FROM ktv_supplychain_org_rel kso
                LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=kso.ParentOrgId
                LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=kso.ChildOrgId
                WHERE vso.OrgType = 'mill' $s1 AND ( kso.ParentOrgId=? $s2 OR kso.ChildOrgId=?)
                GROUP BY vso2.SupplychainID";
        $query = $this->db->query($sql, array($SupplychainID, $SupplychainID));
        //$sql = "SELECT vs.Name AS NameDO FROM view_supplychain_org vs WHERE OrgType='agent' GROUP BY vs.Name";
        //$query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    function readComboAgent(){
        //$sql = "SELECT vs.Name AS NameAgent FROM view_supplychain_org vs WHERE OrgType='agent' GROUP BY vs.Name";
        //$query = $this->db->query($sql);
        $sql = "SELECT kso.SupplychainID 
                FROM sys_user u
                    LEFT JOIN ktv_persons p ON p.UserID=u.UserId
                    LEFT JOIN ktv_staffs s ON s.PersonID=p.PersonID
                    LEFT JOIN ktv_supplychain_org kso ON kso.OrgID=s.ObjID AND kso.OrgType=s.ObjType
                WHERE u.UserId=?";
        $SupplychainID = $this->db->query($sql, array($_SESSION['userid']))->row()->SupplychainID;
        $s1 = $SupplychainID=='' ? "/*" : "";
        $s2 = $SupplychainID=='' ? "*/" : "";
        
        $sql = "SELECT vso2.SupplychainID id, vso2.`Name` label
                FROM ktv_supplychain_org_rel kso
                LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=kso.ParentOrgId
                LEFT JOIN ktv_supplychain_org_rel kso2 ON kso2.ParentOrgId=kso.ChildOrgId
                LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=kso2.ChildOrgId
                WHERE vso.OrgType = 'mill' $s1 AND ( kso.ParentOrgId=? $s2 OR kso.ChildOrgId=?)
                GROUP BY vso2.SupplychainID";
        $query = $this->db->query($sql, array($SupplychainID, $SupplychainID));
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