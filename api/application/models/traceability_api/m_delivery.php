<?php

class M_delivery extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    } 


    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir) 
    {
        if ($sortingField == "")
            $sortingField = 'DateCreated';
        if ($sortingDir == "")
            $sortingDir = 'DESC';
        
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }
    
        @$MRoleID = $this->db->query("SELECT MRoleID FROM view_tc_supplychain_org WHERE SupplychainID=?", array($_SESSION['SupplychainID']))->row()->MRoleID;
        
        $is_admin = $_SESSION['is_admin'];
        
        $this->db->select("SQL_CALC_FOUND_ROWS 
            ktsd.DeliveryID
            ,ktsd.SupplyChainID
            ,ktsd.ExternalCode
            ,ktsd.DeliveryDate
            ,ktsd.PackageWeight
            ,ktsd.DestWeight AS DestWeight
            ,ktsdd.Weight AS TotalWeight
            ,ktsd.TotalWeight AS SellingWeight
            ,ktsd.SupplyDestMillOrgID
            ,ktsd.SupplyDestOrgID
            ,ktsd.SupplyDestMillOtherName
            ,CASE
            WHEN ktsd.DeliveryStatusID = 1 THEN 'Open'
            WHEN ktsd.DeliveryStatusID = 2 THEN 'Close'
            WHEN ktsd.DeliveryStatusID = 3 THEN 'Sent'
            WHEN ktsd.DeliveryStatusID = 4 THEN 'Delivered'
            WHEN ktsd.DeliveryStatusID = 5 THEN 'Finish'
            ELSE '-'
            END AS Status
            ,ktsd.ArrivalEstimation
            ,ktsd.PackageNumber
            ,ktsd.DateCreated
            ,ktsb.SupplyBatchID
            ,ktsd.DeliveryNumber
            ,ktsb.SupplyBatchNumber
            ", 
            FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->join("ktv_tc_supplychain_delivery_detail ktsdd","ktsdd.DeliveryID= ktsd.DeliveryID",'left');
        $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
        $this->db->where("ktsd.StatusCode","active");
        $this->db->where("ktsd.DeliveryStatusID != 0", NULL, FALSE);
        
        if($MRoleID == ''){
            $this->db->where("ktsd.SupplychainID", @$SupplychainID);
        }

        if($MRoleID == '9'){
            $this->db->where("ktsd.SupplyDestDoOrgID", @$SupplychainID);
        } else {
            $this->db->where("ktsd.SupplychainID", @$SupplychainID);
        }

        //========== Search (Begin) =====================
        if ($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',', $pSearch['ArrFilter']);
            for ($i = 0; $i < count($ArrTmp); $i++) {
                switch ($ArrTmp[$i]) {
                    case 'ExternalCode':
                        $this->db->like('ktsd.ExternalCode', $pSearch['TextFilterExernalCode'], 'both');
                        break;
                        // case 'DestinationID':
                        //     $this->db->where('ktsd.SupplyDestMillOrgID', $pSearch['TextFilterDestinationID'], 'both');
                        //     break;
                    case 'DeliveryStatusID':
                        $this->db->where("ktsd.DeliveryStatusID",$pSearch['TextFilterDeliveryStatusID']);
                        break;
                    case 'StartDeliveryDate':
                        $this->db->where('DATE_FORMAT(ktsd.DeliveryDate, "%Y-%m-%d") >=',$pSearch['TextFilterStartDeliveryDate']);
                        break;
                    case 'EndDeliveryDate':
                        $this->db->where('DATE_FORMAT(ktsd.DeliveryDate, "%Y-%m-%d") <=',$pSearch['TextFilterEndDeliveryDate']);
                        break;
                }
            }
        }
        
        $this->db->group_by("ktsd.DeliveryID");
        $this->db->order_by($sortingField,$sortingDir);
        $this->db->limit($limit, $start);

        $query = $this->db->get();
        // echo '<pre>'.$this->db->last_query();die;

        $result['data'] = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');

        $result['total'] = $query->row()->total;
        
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
            <h4>Information</h4>
            <ul>
                <li>' . number_format($query->row()->total, 0, ".", ",") . ' '.lang('datas, Sorted by').' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
            </ul>
        </div>';

        return $result;
    }

    public function GetGridMainExcel($pSearch, $start, $limit, $sortingField, $sortingDir) 
    {
        if ($sortingField == "")
            $sortingField = 'DateCreated';
        if ($sortingDir == "")
            $sortingDir = 'DESC';
        
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }
    
        @$MRoleID = $this->db->query("SELECT MRoleID FROM view_tc_supplychain_org WHERE SupplychainID=?", array($_SESSION['SupplychainID']))->row()->MRoleID;
        
        $is_admin = $_SESSION['is_admin'];
        
        $this->db->select("SQL_CALC_FOUND_ROWS 
            ktsd.DeliveryID
            ,ktsd.SupplyChainID
            ,ktsd.ExternalCode
            ,ktsd.DeliveryDate
            ,ktsd.PackageWeight
            ,ktsd.DestWeight AS DestWeight
            ,ktsdd.Weight AS TotalWeight
            ,ktsd.TotalWeight AS SellingWeight
            ,ktsd.SupplyDestMillOrgID
            ,ktsd.SupplyDestOrgID
            ,ktsd.SupplyDestMillOtherName
            ,CASE
            WHEN ktsd.DeliveryStatusID = 1 THEN 'Open'
            WHEN ktsd.DeliveryStatusID = 2 THEN 'Close'
            WHEN ktsd.DeliveryStatusID = 3 THEN 'Sent'
            WHEN ktsd.DeliveryStatusID = 4 THEN 'Delivered'
            WHEN ktsd.DeliveryStatusID = 5 THEN 'Finish'
            ELSE '-'
            END AS Status
            ,ktsd.ArrivalEstimation
            ,ktsd.PackageNumber
            ,ktsd.DateCreated
            ,ktsb.SupplyBatchID
            ,ktsd.DeliveryNumber
            ,ktsb.SupplyBatchNumber
            ", 
            FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->join("ktv_tc_supplychain_delivery_detail ktsdd","ktsdd.DeliveryID= ktsd.DeliveryID",'left');
        $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
        $this->db->where("ktsd.StatusCode","active");
        $this->db->where("ktsd.DeliveryStatusID != 0", NULL, FALSE);

        if($MRoleID == ''){
            $this->db->where("ktsd.SupplychainID", @$SupplychainID);
        }

        if($MRoleID == '9'){
            $this->db->where("ktsd.SupplyDestDoOrgID", @$SupplychainID);
        } else {
            $this->db->where("ktsd.SupplychainID", @$SupplychainID);
        }

        //========== Search (Begin) =====================
        if ($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',', $pSearch['ArrFilter']);
            for ($i = 0; $i < count($ArrTmp); $i++) {
                switch ($ArrTmp[$i]) {
                    case 'ExternalCode':
                        $this->db->like('ktsd.ExternalCode', $pSearch['TextFilterExernalCode'], 'both');
                        break;
                        // case 'DestinationID':
                        //     $this->db->where('ktsd.SupplyDestMillOrgID', $pSearch['TextFilterDestinationID'], 'both');
                        //     break;
                    case 'DeliveryStatusID':
                        $this->db->where("ktsd.DeliveryStatusID",$pSearch['TextFilterDeliveryStatusID']);
                        break;
                    case 'StartDeliveryDate':
                        $this->db->where('DATE_FORMAT(ktsd.DeliveryDate, "%Y-%m-%d") >=',$pSearch['TextFilterStartDeliveryDate']);
                        break;
                    case 'EndDeliveryDate':
                        $this->db->where('DATE_FORMAT(ktsd.DeliveryDate, "%Y-%m-%d") <=',$pSearch['TextFilterEndDeliveryDate']);
                        break;
                }
            }
        }
        
        $this->db->group_by("ktsd.DeliveryID");
        $this->db->order_by($sortingField,$sortingDir);
        $query = $this->db->get();
        // echo '<pre>'.$this->db->last_query();die;

        $result['data'] = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');

        $result['total'] = $query->row()->total;
        
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
            <h4>Information</h4>
            <ul>
                <li>' . number_format($query->row()->total, 0, ".", ",") . ' '.lang('datas, Sorted by').' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
            </ul>
        </div>';

        return $result;
    }

    public function SupplychainDeliveryFormOpen($DeliveryID)
    {
        $return = array();
        
        $this->db->select("
            sd.DeliveryID
            ,sd.DestDriver
            ,sd.DestTransportNumber
            ,sd.DestTransportID
            ,sd.ArrivalEstimation
            ,sd.ReceivedDate
            ,sd.DeliveryNumber
            ,sd.DeliveryDate
            ,sd.ExternalCode
            ,sd.DeliveryStatusID
            ,sd.TotalWeight
            ,sd.PackageNumber
            ,sd.DestWeight
            ,sd.PackageID
            ,sd.PackageWeight
            ,sd.SupplyDestMillOrgID
            ,IFNULL(sd.SupplyDestDoOrgID,SupplyDestOrgID) AS SupplyDestOrgID
            ,sd.SMESPCodeID
            ,sd.SupplyDestType
            ,sd.SupplyDestProcessType
            ,sd.DestPo
            ,sd.SupplyDestMillOtherName
            ,sd.FinalCapacity
            ,sd.PaymentDelivery
            ,IFNULL(vtso.name,vtso2.name) AS Destination
            ", 
            FALSE);
        $this->db->from("ktv_tc_supplychain_delivery sd");
        $this->db->join("view_tc_supplychain_org vtso","vtso.SupplychainID = sd.SupplyDestMillOrgID",'left');
        $this->db->join("view_tc_supplychain_org vtso2","vtso2.SupplychainID = sd.SupplyDestDoOrgID",'left');
        $this->db->where("sd.DeliveryID", $DeliveryID);
        
        $query = $this->db->get();
        
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function GetGridPickDeliveryMain($pSearch, $start, $limit, $sortingField, $sortingDir) 
    {

        $SupplychainID = $_SESSION['SupplychainID'];
        
        if($pSearch['SupplyBatchNumber'] != ''){
            $SupplyBatchNumber = $pSearch['SupplyBatchNumber'];

            $whereSupplyBatchNumber .= " AND a.SupplyBatchNumber LIKE '%$SupplyBatchNumber%' ";
        }
        
        if($pSearch['StartDateCreateBatch'] != '' OR $pSearch['EndDateCreateBatch'] != ''){
            $StartDateCreateBatch   = $pSearch['StartDateCreateBatch'];
            $EndDateCreateBatch     = $pSearch['EndDateCreateBatch'];

            $whereFilterDate .= "AND DATE_FORMAT(a.DateCreated, '%Y-%m-%d') >= '$StartDateCreateBatch' AND DATE_FORMAT(a.DateCreated, '%Y-%m-%d') <= '$EndDateCreateBatch'";
        }

        $sql = "SELECT 
                        a.SupplyOrgID,
                        a.SupplyBatchDate,
                        a.SupplyBatchID,
                        a.SupplyOrgID,
                        IFNULL(a.SupplyDestMillOrgID,a.SupplyDestOrgID) Mill,
                        a.SupplyDestMillOtherName,
                        IF(((a.`SupplyDestProcessType` IS NOT NULL AND a.`SupplyDestProcessType` <> 0) OR (a.`SupplyDestDoOrgID` IS NOT NULL AND a.`SupplyDestDoOrgID` <> 0)) AND (a.`SupplyDestOrgID` IS NOT NULL OR a.`SupplyDestMillOrgID` IS NOT NULL), 'do', 'mill') SupplyType,
                        a.SupplyDestDoOrgID,
                        a.SupplyDestProcessType,
                        a.SMESPCodeID,
                        a.SupplyBatchNumber,
                        a.SupplyBatchStatus,
                        DATE(a.DeliveryDate) AS DeliveryDate,
                        a.DestPO,
                        sum(st.VolumeBruto) AS DestWeight,
                        a.DestNumberPackage,
                        a.DestDriver,
                        a.DestTransportID,
                        a.DestTransportNumber,
                        a.DestContainerNumber,
                        a.RemainingWeight,
                        a.Notes,
                        a.ChangeLog,
                        a.ChangeBy,
                        a.StatusCode,
                        a.DateCreated AS DateCreateBatch,
                        a.CreatedBy,
                        a.DateUpdated,
                        a.LastModifiedBy
                    FROM
                        ktv_tc_supplychain_batch a
                    LEFT JOIN
                        ktv_tc_supplychain_transaction st on st.SupplyBatchID = a.SupplyBatchID
                    WHERE 
                        a.SupplyOrgID = '$SupplychainID'
                    AND 
                        a.SupplyBatchStatus = 'Closed'
                    AND 
                        a.RemainingWeight > 0
                    $whereSupplyBatchNumber
                    $whereFilterDate
                    GROUP BY
                        a.SupplyBatchNumber
                    ";
            $Q = $this->db->query($sql,array($SupplychainID));
            // echo '<pre>'.$this->db->last_query();die;
            
            if($Q->num_rows()){
                $result = $Q->result();
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
                return $data;
            }
    }

    public function SupplychainBatchFormOpen($SupplyBatchID)
    {   
        $this->db->select("a.SupplyBatchNumber, a.RemainingWeight AS RemainingWeight", FALSE);
        $this->db->from("ktv_tc_supplychain_batch a");
        $this->db->join("ktv_tc_supplychain_transaction b", "b.SupplyBatchID = a.SupplyBatchID", 'left');
        $this->db->where("a.SupplyBatchID",$SupplyBatchID);
        $query = $this->db->get();

        $data = $query->row_array();
    
        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['success'] = true;
        $return['data'] = $dataRow;
        $return['total'] = $query->row()->total;

        return $return;
    }

    public function GetSupplychainDeliveryDetail($DeliveryID) 
    {
        $this->db->select("ktsdd.DeliveryIDApp", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
        $this->db->join("ktv_tc_supplychain_delivery ktsd","ktsd.DeliveryID= ktsdd.DeliveryID",'left');
        $this->db->where("ktsd.DeliveryID",$DeliveryID);
        $this->db->where("ktsdd.StatusCode","active");
        $this->db->order_by("ktsdd.DeliveryDetailID","DESC");

        $queryBacth = $this->db->get();

        $cekBatch = $queryBacth->result_array();

        if(!empty($cekBatch)){
            foreach ($cekBatch as $key => $value) {
                if (!empty($value['DeliveryIDApp'])) {
                    $this->db->select("SQL_CALC_FOUND_ROWS
                        ktsdd.DeliveryDetailID
                        ,ktsd.DeliveryID
                        ,ktsd.DeliveryDate
                        ,ktsd.DeliveryNumber AS SupplyBatchNumber
                        ,ktsdd.DetailNumber	
                        ,ktsdd.Weight AS Weight
                        ,ktsb.SupplyBatchID
                        ,ktsb.RemainingWeight", FALSE);
                    $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
                    $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
                    $this->db->join("ktv_tc_supplychain_delivery ktsd","ktsd.DeliveryID= ktsdd.DeliveryID",'left');
                    $this->db->where("ktsd.DeliveryID",$DeliveryID);
                    $this->db->where("ktsdd.StatusCode","active");
                    
                    $this->db->order_by("ktsdd.DeliveryDetailID","DESC");

                    $queryBacth = $this->db->get();
                    
                    $dataBatch = $queryBacth->result_array();
                } else {
                    $this->db->select("SQL_CALC_FOUND_ROWS
                        ktsdd.DeliveryDetailID
                        ,ktsd.DeliveryID
                        ,ktsd.DeliveryDate
                        ,ktsb.SupplyBatchNumber
                        ,ktsdd.DetailNumber	
                        ,ktsdd.Weight
                        ,ktsb.SupplyBatchID
                        ,ktsb.RemainingWeight", FALSE);
                    $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
                    $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
                    $this->db->join("ktv_tc_supplychain_delivery ktsd","ktsd.DeliveryID= ktsdd.DeliveryID",'left');
                    $this->db->where("ktsd.DeliveryID",$DeliveryID);
                    $this->db->where("ktsdd.StatusCode","active");
                    $this->db->order_by("ktsdd.DeliveryDetailID","DESC");

                    $queryBacth = $this->db->get();
                    
                    $dataBatch = $queryBacth->result_array();
                }
            }
        }
       
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');


        $return['success'] = true;
        $return['data'] = $dataBatch;
        $return['total'] = $query->row()->total;

        return $return;
    }

    public function GetSupplychainReceptionDetail($DeliveryID) 
    {
        $this->db->select("SQL_CALC_FOUND_ROWS
                    ktsdd.DeliveryIDApp", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
        $this->db->join("ktv_tc_supplychain_delivery ktsd","ktsd.DeliveryID= ktsdd.DeliveryID",'left');
        $this->db->where("ktsd.DeliveryID",$DeliveryID);
        $this->db->where("ktsdd.StatusCode","active");
        $this->db->order_by("ktsdd.DeliveryDetailID","DESC");

        $queryBacth = $this->db->get();

        $cekBatch = $queryBacth->result_array();
       
        if(!empty($cekBatch)){
            foreach ($cekBatch as $key => $value) {
                if (!empty($value['DeliveryIDApp'])) {
                    $this->db->select("SQL_CALC_FOUND_ROWS
                        ktsdd.DeliveryDetailID
                        ,ktsd.DeliveryID
                        ,ktsd.DeliveryNumber AS SupplyBatchNumber
                        ,ktsdd.DetailNumber	
                        ,ktsd.TotalWeight AS Weight
                        ,ktsb.SupplyBatchID
                        ,ktsb.RemainingWeight", FALSE);
                    $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
                    $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
                    $this->db->join("ktv_tc_supplychain_delivery ktsd","ktsd.DeliveryID= ktsdd.DeliveryID",'left');
                    $this->db->where("ktsd.DeliveryID",$DeliveryID);
                    $this->db->where("ktsdd.StatusCode","active");
                    $this->db->group_by("ktsd.DeliveryID");
                    $this->db->order_by("ktsdd.DeliveryDetailID","DESC");

                    $queryBacth = $this->db->get();
                     
                    $dataBatch = $queryBacth->result_array();
                } else {
                    $this->db->select("SQL_CALC_FOUND_ROWS
                        ktsdd.DeliveryDetailID
                        ,ktsd.DeliveryID
                        ,ktsd.DeliveryNumber AS SupplyBatchNumber
                        ,ktsdd.DetailNumber	
                        ,ktsd.TotalWeight AS Weight
                        ,ktsb.SupplyBatchID
                        ,ktsb.RemainingWeight", FALSE);
                    $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
                    $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
                    $this->db->join("ktv_tc_supplychain_delivery ktsd","ktsd.DeliveryID= ktsdd.DeliveryID",'left');
                    $this->db->where("ktsd.DeliveryID",$DeliveryID);
                    $this->db->where("ktsdd.StatusCode","active");
                    $this->db->group_by("ktsd.DeliveryID");
                    $this->db->order_by("ktsdd.DeliveryDetailID","DESC");

                    $queryBacth = $this->db->get();
                    
                    $dataBatch = $queryBacth->result_array();
                }
            }
        }
       
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');


        $return['success'] = true;
        $return['data'] = $dataBatch;
        $return['total'] = $query->row()->total;

        return $return;
    }

    function getLastDelivery($SID,$Date){
        $sql= "SELECT count(*) total_trans
            from ktv_tc_neo_supplychain_delivery
            where 
            SupplychainID=?
            AND date(DeliveryDate)=?";
        $query = $this->db->query($sql,array($SID,$Date));
        if($query->num_rows>0){
            $last = $query->row()->total_trans;
        }else{
            $last = 0;
        }
        return $last;
    }

    public function submit_delivery($paramPost) 
    {
        $results = array();

        $this->db->trans_begin();
        
        $SupplychainID                       = $_SESSION['SupplychainID'];
        $dataDelivery['DeliveryDate']        = !empty($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']) ? $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate'] : null;
        $dataDelivery['ExternalCode']        = !empty($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']) ? $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode'] : null;
        $uid                                 = $this->getUID();                
        
        $check = $this->db->query("SELECT DeliveryID FROM ktv_tc_supplychain_delivery WHERE DeliveryID=?", array($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']));
        if($check->num_rows() > 0) {

            $this->db->select("SQL_CALC_FOUND_ROWS
                ktsd.DeliveryID,ktsd.SupplyDestMillOrgID", FALSE);
            $this->db->from("ktv_tc_supplychain_delivery ktsd");
            $this->db->where("ktsd.DeliveryID",$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']);
            $this->db->order_by("ktsd.DeliveryID","DESC");

            $query = $this->db->get();

            $cekDelivery = $query->result_array();
            
            if($cekDelivery){
                foreach($cekDelivery as $key => $val){
                    $SupplyDestMillOrgID  =  $val['SupplyDestMillOrgID'];
                }
            }

            $DeliveryNumber = $this->_generateDeliveryNumber($SupplychainID, $dataDelivery['DeliveryDate']);
            
            if(!empty($SupplyDestMillOrgID)){
                //update tb : ktv_tc_supplychain_delivery
                $dataDelivery['SupplyDestProcessType']      = 'mill';
                
                $dataDelivery['DeliveryDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']: '';
                $dataDelivery['ExternalCode']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']: '';
                $dataDelivery['SupplyDestMillOtherName']    = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']: null;
                $dataDelivery['SupplyDestType']             = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']: null;
                $dataDelivery['DestPo']                     = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']: null;
                $dataDelivery['SupplyDestMillOrgID']        = !empty(@$SupplyDestMillOrgID) ? @$SupplyDestMillOrgID : null;
                $dataDelivery['SMESPCodeID']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']: null;
                $dataDelivery['PackageNumber']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) : null;
                $dataDelivery['TotalWeight']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) : null;
                $dataDelivery['PackageWeight']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight']) ?@ $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight'] : 1;
                $dataDelivery['DestWeight']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) : null;
                $dataDelivery['FinalCapacity']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-FinalCapacity']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-FinalCapacity']) : null;
                $dataDelivery['PaymentDelivery']            = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PaymentDelivery']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PaymentDelivery']) : null;
                
                $dataDelivery['ReceivedDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate'] : null;
                $dataDelivery['ArrivalEstimation']          = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation'] : null;
                $dataDelivery['DestDriver']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver'] : null;
                $dataDelivery['DestTransportNumber']        = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber'] : null;
                $dataDelivery['DestTransportID']            = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID'] : null;
                $dataDelivery['LastModifiedBy']             = $_SESSION['userid'];
                $dataDelivery['DateUpdated']                = date('Y-m-d H:i:s');
                $dataDelivery['uid']                        = $uid;

                if($dataDelivery['FinalCapacity'] != '' OR $dataDelivery['PaymentDelivery'] != '' ){
                    $dataDelivery['DeliveryStatusID'] = '5';
                }

                if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'agent'){
                    $dataDelivery['SupplyDestOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                }

                if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'mill'){
                    $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                }
    
                if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'do'){
                    $dataDelivery['SupplyDestDoOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestDoOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                    $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                }
                
                $this->db->where('DeliveryID', $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']);
                $this->db->update('ktv_tc_supplychain_delivery', $dataDelivery);
                $DeliveryID = $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID'];
            } else {

                 //update tb : ktv_tc_supplychain_delivery
                 $dataDelivery['SupplyDestProcessType']      = 'mill';
                 $dataDelivery['DeliveryNumber']             = $DeliveryNumber;
                 $dataDelivery['DeliveryDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']: '';
                 $dataDelivery['ExternalCode']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']: '';
                 $dataDelivery['SupplyDestMillOtherName']    = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']: null;
                 $dataDelivery['SupplyDestType']             = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']: null;
                 $dataDelivery['DestPo']                     = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']: null;
                 $dataDelivery['SupplyDestMillOrgID']        = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                 $dataDelivery['SMESPCodeID']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']: null;
                 $dataDelivery['PackageNumber']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) : null;
                 $dataDelivery['TotalWeight']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) : null;
                 $dataDelivery['PackageWeight']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight']) ?@ $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight'] : 1;
                 $dataDelivery['DestWeight']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) : null;
                 $dataDelivery['FinalCapacity']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-FinalCapacity']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-FinalCapacity']) : null;
                 $dataDelivery['PaymentDelivery']            = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PaymentDelivery']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PaymentDelivery']) : null;
                 
                 $dataDelivery['ReceivedDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate'] : null;
                 $dataDelivery['ArrivalEstimation']          = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation'] : null;
                 $dataDelivery['DestDriver']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver'] : null;
                 $dataDelivery['DestTransportNumber']        = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber'] : null;
                 $dataDelivery['DestTransportID']            = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID'] : null;
                 $dataDelivery['LastModifiedBy']             = $_SESSION['userid'];
                 $dataDelivery['DateUpdated']                = date('Y-m-d H:i:s');
                 $dataDelivery['uid']                        = $uid;

                if($dataDelivery['FinalCapacity'] != '' OR $dataDelivery['PaymentDelivery'] != ''){
                    $dataDelivery['DeliveryStatusID'] = '5';
                    $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                }
                
                if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'agent'){
                    $dataDelivery['SupplyDestOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                }

                if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'mill'){
                    $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                }
    
                if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'do'){
                    $dataDelivery['SupplyDestDoOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                    $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                }
                
                $this->db->where('DeliveryID', $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']);
                $this->db->update('ktv_tc_supplychain_delivery', $dataDelivery);
                $DeliveryID = $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID'];
            }

        } else {
            
            if($dataDelivery['FinalCapacity'] != ''){
                $dataDelivery['DeliveryStatusID'] = '5';
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'agent'){
                $dataDelivery['SupplyDestOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'mill'){
                $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'do'){
                $dataDelivery['SupplyDestDoOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
            }

            //insert tb : ktv_tc_supplychain_delivery
            $dataDelivery['DeliveryNumber']   = $DeliveryNumber;
            $dataDelivery['SupplychainID']    = $SupplychainID;
            $dataDelivery['DeliveryStatusID'] = $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryStatusID'];
            $dataDelivery['StatusCode']       = 'active';
            $dataDelivery['uid']              = $uid;
            $dataDelivery['CreatedBy']        = $_SESSION['userid'];
            $dataDelivery['DateCreated']      = date('Y-m-d H:i:s');
            $dataDelivery['uid']              = $uid;
            
            $query = $this->db->insert('ktv_tc_supplychain_delivery', $dataDelivery);
            $DeliveryID = $this->db->insert_id();
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success']    = true;
            $results['message']    = lang("Data saved");
            $results['DeliveryID'] = $DeliveryID;
            $results['DeliveryStatusID'] = $paramPost['Koltiva_view_Traceability_new_Delivery_MainFo;rm-FormBasicData-DeliveryStatusID'];
        }

        return $results;
    }

    public function submit_send($paramPost) 
    {
        
        $results = array();
        $this->db->trans_begin();
        //update tb : ktv_tc_supplychain_delivery
        $dataDelivery['DeliveryStatusID']    = 3;
        
        $this->db->select("SQL_CALC_FOUND_ROWS
                ktsd.DeliveryID,ktsd.SupplyDestMillOrgID", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->where("ktsd.DeliveryID",$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']);
        $this->db->order_by("ktsd.DeliveryID","DESC");

        $query = $this->db->get();

        $cekDelivery = $query->result_array();
        
        if($cekDelivery){
            foreach($cekDelivery as $key => $val){
                $SupplyDestMillOrgID  =  $val['SupplyDestMillOrgID'];
            }
        }
    
        if(!empty($SupplyDestMillOrgID)){

            $dataDelivery['SupplyDestProcessType']      = 'mill';
            $dataDelivery['DeliveryDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']: '';
            $dataDelivery['ExternalCode']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']: '';
            $dataDelivery['SupplyDestMillOtherName']    = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']: '';
            $dataDelivery['SupplyDestType']             = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']: null;
            $dataDelivery['DestPo']                     = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']: null;
            $dataDelivery['SupplyDestMillOrgID']        = !empty(@$SupplyDestMillOrgID) ? @$SupplyDestMillOrgID : null;
            $dataDelivery['SMESPCodeID']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']: null;
            $dataDelivery['PackageNumber']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) : null;
            $dataDelivery['TotalWeight']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) : null;
            $dataDelivery['PackageWeight']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight']) ?@ $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight'] : 1;
            $dataDelivery['DestWeight']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) : null;
            $dataDelivery['ReceivedDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate'] : null;
            $dataDelivery['ArrivalEstimation']          = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation'] : null;
            $dataDelivery['DestDriver']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver'] : null;
            $dataDelivery['DestTransportNumber']        = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber'] : null;
            $dataDelivery['DestTransportID']            = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID'] : null;
            $dataDelivery['LastModifiedBy']             = $_SESSION['userid'];
            $dataDelivery['DateUpdated']                = date('Y-m-d H:i:s');
            
            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'agent'){
                $dataDelivery['SupplyDestOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'mill'){
                $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                $dataDelivery['SupplyDestMillOrgID'] = $SupplyDestMillOrgID;
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'do'){
                $dataDelivery['SupplyDestDoOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
                $dataDelivery['SupplyDestMillOrgID'] = $SupplyDestMillOrgID;
            }
            
            $this->db->where('DeliveryID', $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']);
            $this->db->update('ktv_tc_supplychain_delivery', $dataDelivery);

            $DeliveryID = $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID'];
        } else {
            
            $dataDelivery['SupplyDestProcessType']      = 'mill';
            $dataDelivery['DeliveryDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryDate']: '';
            $dataDelivery['ExternalCode']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ExternalCode']: '';
            $dataDelivery['SupplyDestMillOtherName']    = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestMillOtherName']: '';
            $dataDelivery['SupplyDestType']             = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType']: null;
            $dataDelivery['DestPo']                     = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestPo']: null;
            $dataDelivery['SMESPCodeID']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SMESPCodeID']: null;
            $dataDelivery['PackageNumber']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageNumber']) : null;
            $dataDelivery['TotalWeight']                = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-TotalWeight']) : null;
            $dataDelivery['PackageWeight']              = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight']) ?@ $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-PackageWeight'] : 1;
            $dataDelivery['DestWeight']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) ? (float) str_replace(",", "", @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestWeight']) : null;
            $dataDelivery['ReceivedDate']               = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ReceivedDate'] : null;
            $dataDelivery['ArrivalEstimation']          = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-ArrivalEstimation'] : null;
            $dataDelivery['DestDriver']                 = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestDriver'] : null;
            $dataDelivery['DestTransportNumber']        = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportNumber'] : null;
            $dataDelivery['DestTransportID']            = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DestTransportID'] : null;
            $dataDelivery['LastModifiedBy']             = $_SESSION['userid'];
            $dataDelivery['DateUpdated']                = date('Y-m-d H:i:s');
            
            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'agent'){
                $dataDelivery['SupplyDestOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'mill'){
                $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
            }

            if($paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestType'] == 'do'){
                $dataDelivery['SupplyDestDoOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-SupplyDestOrgID']: null;
                $dataDelivery['SupplyDestMillOrgID'] = !empty(@$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']) ? @$paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-Destination']: null;
            }
            
            $this->db->where('DeliveryID', $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID']);
            $this->db->update('ktv_tc_supplychain_delivery', $dataDelivery);

            $DeliveryID = $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID'];
        }
           

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success']    = true;
            $results['message']    = lang("Data saved");
            $results['DeliveryID'] = $DeliveryID;
            $results['DeliveryStatusID'] = 3;
        }

        return $results;
    }

    public function InsertSupplychainDeliveryDetail($paramPost)
    {
        $this->db->trans_begin();
        
        $SupplychainDelivery = $this->submit_delivery($paramPost);
        
        $DeliveryID = $paramPost['Koltiva_view_Traceability_new_Delivery_MainForm-FormBasicData-DeliveryID'];
        
        //Check DeliveryStatusID harus 1: Open
        $DeliveryStatusID = $this->CheckDeliveryStatusID($DeliveryID);

        $uidBatch = $this->getUID();
      
        $SupplyBatchNumber = $paramPost['SupplyBatchNumber'];
        
        if($DeliveryStatusID['DeliveryStatusID'] == 1) {
            
            $sql = "SELECT 
                        a.SupplyBatchID
                    FROM 
                        ktv_tc_supplychain_batch a 
                    WHERE 
                        a.SupplyBatchNumber =  '$SupplyBatchNumber'
                    ";
            $cekBatch = $this->db->query($sql,array($SupplyBatchNumber));
           
            if($cekBatch->num_rows()>0){
               
                $dataDeliveryDetail = array();
                foreach($cekBatch->result() as $key => $row){
                    $dataDeliveryDetail['uidBatch']              = $uidBatch;
                    $dataDeliveryDetail['DeliveryID']            = $DeliveryID;
                    $dataDeliveryDetail['SupplyBatchID']         = $row->SupplyBatchID;
                    $dataDeliveryDetail['Weight']                = $paramPost['Weight'];
                    $dataDeliveryDetail['CreatedBy']             = $_SESSION['userid'];
                    $dataDeliveryDetail['DateCreated']           = date('Y-m-d H:i:s');
                }

                $this->db->insert('ktv_tc_supplychain_delivery_detail', $dataDeliveryDetail);
            } else {

                $dataDeliveryDetail['DeliveryID']            = $DeliveryID;
                $dataDeliveryDetail['SupplyBatchID']         = $paramPost['SupplyBatchID'];
                $dataDeliveryDetail['Weight']                = $paramPost['Weight'];
                $dataDeliveryDetail['CreatedBy']             = $_SESSION['userid'];
                $dataDeliveryDetail['DateCreated']           = date('Y-m-d H:i:s');

                $this->db->insert('ktv_tc_supplychain_delivery_detail', $dataDeliveryDetail);
            }
            
            //pengurangan remaining pada tb : ktv_tc_supplychain_batch
            $sql = "SELECT 
                        sb.RemainingWeight AS RemainingWeight
                    FROM 
                        ktv_tc_supplychain_batch sb
                    WHERE 
                        sb.SupplyBatchNumber = '{$SupplyBatchNumber}' AND sb.StatusCode = 'active'";

                    $supplychain_batch = $this->db->query($sql)->row_array();
                    
                    $dataBatch['RemainingWeight']  = $supplychain_batch['RemainingWeight'] - $paramPost['Weight'];

                    $this->db->where('SupplyBatchNumber', $SupplyBatchNumber);
                    $this->db->update('ktv_tc_supplychain_batch', $dataBatch);
                    
                    $DeliveryID = $dataDeliveryDetail['DeliveryID'];
            
            $this->calculateWeight($DeliveryID);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = lang("Failed to save data");
            } else {
                $this->db->trans_commit();
                $results['success']    = true;
                $results['message']    = lang("Data saved");
                $results['DeliveryID'] = $DeliveryID;
            }

        } else {
            $results['success']    = false;
            $results['message']    = lang("Failed to save data because processing status is " . $DeliveryStatusID['Status']);
            $results['DeliveryID'] = $DeliveryID;
        }


        return $results;
    }

    public function calculateWeight($DeliveryID)
    {
        $sql = "SELECT 
                    SUM(Weight) AS TotalWeight
                FROM 
                    ktv_tc_supplychain_delivery_detail sdd
                WHERE 
                    sdd.DeliveryDetailID = {$DeliveryID}";

        $supplychain_delivery_detail = $this->db->query($sql)->row_array();

        $dataUpdateDelivery['TotalWeight'] = !empty($supplychain_delivery_detail['TotalWeight']) ? $supplychain_delivery_detail['TotalWeight'] : 0;

        $this->db->where('DeliveryID', $DeliveryID);
        $this->db->update('ktv_tc_supplychain_delivery', $dataUpdateDelivery);
    }

    public function CheckDeliveryStatusID($DeliveryID)
    {
        $sql = "SELECT 
                    sd.DeliveryStatusID
                    , ds.Status
                FROM 
                    ktv_tc_supplychain_delivery sd
                LEFT JOIN ref_tc_delivery_status ds ON ds.DeliveryStatusID = sd.DeliveryStatusID
                WHERE 
                    sd.StatusCode = 'active' 
                    AND sd.DeliveryID = {$DeliveryID}";

        $DeliveryStatusID = $this->db->query($sql)->row_array();
        return $DeliveryStatusID;
    }

    public function validationDelivery()
    {
        $SupplychainID = $_SESSION['SupplychainID'];

        $sql = "SELECT 
                    BatchClosed
                FROM 
                    ktv_tc_supplychain_batch
                WHERE 
                    StatusCode = 'active' 
                AND 
                    SupplyOrgID = '$SupplychainID'
                AND
                    BatchClosed = '1'";

        $SupplychainID = $this->db->query($sql)->row_array();
        return $SupplychainID;
    }

    public function GetDeliveryStatus()
    {
        $this->db->select("DeliveryStatusID AS id, Status AS label", FALSE);
        $this->db->from("ref_tc_delivery_status");
        $this->db->where("StatusCode","active");
        $this->db->where("DeliveryStatusID != 6");

        $data = $this->db->get()->result_array();

        return $data;
    }

   public function CloseDeliveryPick($post){

        $this->db->select("ktsd.DeliveryDate", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->where("ktsd.DeliveryID",$post['DeliveryID']);
        $this->db->order_by("ktsd.DeliveryID","DESC");

        $query = $this->db->get();

        $cekDelivery = $query->result_array();

        if($cekDelivery){
            foreach($cekDelivery as $key => $val){
                $DeliveryDate         =  $val['DeliveryDate'];
            }
        }

        $data = array(
            'DeliveryStatusID'  => 2,
            'LastModifiedBy'    => $_SESSION['userid'],
            'DeliveryDate'      => $DeliveryDate,
            'DateUpdated'       => date('Y-m-d H:i:s')
        );
        
        $this->db->where('DeliveryID', $post['DeliveryID']);
        $query = $this->db->update('ktv_tc_supplychain_delivery', $data);
        if($query){
            $return['success']    = true;
            $return['message']    = lang("Batch is Closed");
            $return['DeliveryStatusID'] = 2;
        }else{
            $return['success']    = false;
            $return['message']    = lang("Failed to Close Batch.");
            $return['DeliveryStatusID'] = 2;
        }

        return $return;
   }

   public function ListDestination($DestinationID = NULL){

        $getSupplyChainID = $_SESSION['SupplychainID'];

        if (!empty($DestinationID)) {
            $getSupplyChainID = $DestinationID;
        }

        
        $sql = "SELECT 
                    a.ParentID AS id, 
                    b.Name as label,
                    b.objID
                FROM
                    ktv_tc_supplychain_org_rel a
                LEFT JOIN
                    view_tc_supplychain_org b on b.SupplychainID=a.ParentID
                WHERE 
                    a.ChildID = '$getSupplyChainID'
                AND
                    a.StatusCode = 'active'

                AND b.ObjType = 'mill' 
                ";
        
        $Q = $this->db->query($sql,array($getSupplyChainID));
      
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }

            $return['data'] = $result;

            return $return;
        }

        return $return;
    }

    public function ListDealers(){

        $getSupplyChainID = $_SESSION['SupplychainID'];
        
        $sql = "SELECT 
                    a.ParentID AS id, 
                    b.Name as label
                FROM
                    ktv_tc_supplychain_org_rel a
                INNER JOIN
                    view_tc_supplychain_org b on b.SupplychainID=a.ParentID
                INNER JOIN
                    ktv_program_partner p on p.PartnerID=b.PartnerID
                WHERE 
                    a.ChildID = '$getSupplyChainID'
                AND
                    b.ObjType = 'agent'
                AND
                    a.StatusCode = 'active' 
                ";
        
        $Q = $this->db->query($sql,array($getSupplyChainID));
        
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
              
            }

            $return['data'] = $result;

            return $return;
        } else {
            
            //DO
            $getSupplyChainID = $_SESSION['SupplychainID'];
        
            $sql = "SELECT 
                        a.SupplychainID AS id, 
                        a.Name as label
                    FROM
                        view_tc_supplychain_org a
                    WHERE 
                        a.SupplychainID = '$getSupplyChainID'
                    AND
                        a.ObjType = 'agent'
                    ";
            
            $Q = $this->db->query($sql,array($getSupplyChainID));
            // echo '<pre>'.$this->db->last_query();die;
            
            if($Q->num_rows()){
                $result = $Q->result();
                foreach($result as $key => $val){
                    $val = $this->check_isNull($val);
                
                }

                $return['data'] = $result;

                return $return;
            }
        }

        return $return;
    }

    public function ListDestTypes(){

        $getMemberID    = $this->db->select('c.ObjID')
                                   ->join('ktv_persons b', 'a.UserId = b.UserID')
                                   ->join('ktv_staffs c', 'b.PersonID = c.PersonID')
                                   ->where('a.UserId', (int) $_SESSION['userid'])
                                   ->get('sys_user a')->row()->ObjID;
        
        $getMemberRole  = $this->db->select('b.MRoleID')
                                   ->join('ktv_member_role b', 'a.MemberID = b.MemberID')
                                   ->where_in('b.MRoleID', ['5','6','7','8','9','10','11','12','13','14'])
                                   ->where('a.MemberID', (int) $getMemberID)
                                   ->get('ktv_members a')->result();

        
        $data = [
            ["id" => "mill",   "label" => lang('Mill')],
            ["id" => "do",     "label" => lang('Mill & DO')],
            ["id" => "agent",  "label" => lang('Agent')]
        ];

        // $roleArray = [];

        if (!empty($getMemberRole)) {
            // foreach ($getMemberRole as $key => $value) {
            //     array_push($roleArray, $value->MRoleID);
            // }

            foreach ($data as $key => $value) {
                foreach ($getMemberRole as $key2 => $value2) {
                    if ((int) $value2->MRoleID == 13) {
                        if ($value['id'] == 'mill') {
                            unset($data[$key]);
                        }
                    } elseif (((int) $value2->MRoleID == 11) || ((int) $value2->MRoleID == 12) || ((int) $value2->MRoleID == 14)) {
                        if ($value['id'] == 'do' || $value['id'] == 'agent') {
                            unset($data[$key]);
                        }
                    } else {}
                }
            }
        }

        $data = array_values($data);

        return $data;
    }

    private function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }

    public function deleteTransaction($del){
       
        $SupplyBatchNumber = $del['SupplyBatchNumber'];
        
        $sql = "SELECT 
                    sb.RemainingWeight AS RemainingWeight
                FROM 
                    ktv_tc_supplychain_batch sb
                WHERE 
                    sb.SupplyBatchNumber = '$SupplyBatchNumber' AND sb.StatusCode = 'active'";

        $supplychain_batch = $this->db->query($sql)->row_array();
        
        $dataBatch['RemainingWeight']  = $supplychain_batch['RemainingWeight'] + $del['Weight'];
        
        $this->db->where('SupplyBatchNumber', $SupplyBatchNumber);
        $this->db->update('ktv_tc_supplychain_batch', $dataBatch);
        
        $check = $this->db->query("SELECT DeliveryDetailID FROM ktv_tc_supplychain_delivery_detail WHERE DeliveryDetailID=?", array($del['DeliveryDetailID']));
        if($check->num_rows()>0){
            $data = $check->row();

            if($data){
                $this->db->where('DeliveryDetailID',$del['DeliveryDetailID']);
                $query = $this->db->delete('ktv_tc_supplychain_delivery_detail');

                $results['success'] = true;
                $results['message'] = "Data Deleted";

            } else {
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            }

            return $results;
        }
    }

    function ListTransportationType(){

        $this->db->select("DestTransportID id, DestTransportName label", FALSE);
        $this->db->where('StatusCode', 'active');
        $this->db->where('PartnerID', 4);
        $this->db->order_by('label');

        $query = $this->db->get('ref_tc_transport')->result_array();
        
        return $query;
    }

    public function getProcessingFromBatch($SupplychainID, $PalmoilTypeID) {
        $is_admin = $_SESSION['is_admin'];

        $this->db->select("SQL_CALC_FOUND_ROWS 
            sb.SupplyBatchID
            , sb.SupplyBatchNumber
            , sb.PalmoilTypeID
            , sb.TotalWeight
            , sb.FinalPalmoilTypeID,
            , sb.FinalWeight
            , sb.Remaining
            ", FALSE);
        $this->db->from("ktv_tc_supplychain_batch sb");
        $this->db->where("sb.StatusCode","active");
        $this->db->where("sb.Remaining >", 0);

        if ($is_admin != 1) {
            $this->db->where("sb.SupplyOrgID", $SupplychainID);
        }

        $query = $this->db->get()->result_array();

        return $query;
    }

    public function getProcessingFromTransactionDetail($SupplychainID, $PalmoilTypeID) {
        $is_admin = $_SESSION['is_admin'];

        $this->db->select("SQL_CALC_FOUND_ROWS 
              td.TransDetailID AS SupplyBatchID
            , t.SupplyTransNumber AS SupplyBatchNumber
            , td.PalmoilTypeID
            , td.GrossWeight AS TotalWeight
            , td.PalmoilTypeID AS FinalPalmoilTypeID,
            , td.NettWeight AS FinalWeight
            , td.Remaining
            , vso.Name Supplier
            , 'Transaction' AS Initial
            ", FALSE);
        $this->db->from("ktv_tc_supplychain_transaction_detail td");
        $this->db->join('ktv_tc_supplychain_transaction t', 't.SupplyTransID = td.SupplyTransID');
        $this->db->join('view_tc_supplychain_org vso', 'vso.SupplychainID = t.SupplychainID');
        $this->db->where("t.Status", 1);
        $this->db->where("td.StatusCode","active");
        $this->db->where("td.Remaining >",0);
        $this->db->where("td.IsProcess", 0);

        if ($is_admin != 1) {
            $this->db->where("t.SupplychainID", $SupplychainID);
        }

        $this->db->where("td.PalmoilTypeID", $PalmoilTypeID);

        $query = $this->db->get()->result_array();

        return $query;
    }

    public function GetDelivery($SID, $PID) 
    {   
        $this->db->select("
            ktsd.DeliveryID
            ,ktsd.DeliveryNumber
            ,ktsd.ExternalCode
            ,ktsd.DeliveryDate
            ,ktsd.PackageWeight
            ,ktsd.TotalWeight AS DestWeight
            ,ktsd.SupplyDestOrgID
            ,ktsd.SupplyDestMillOrgID
            ,IF(ktsd.SupplyDestMillOtherName IS NULL or ktsd.SupplyDestMillOtherName = '', '', ktsd.SupplyDestMillOtherName) AS SupplyDestMillOtherName 
            ,ktsd.SupplyDestMillOtherName
            ,ktsd.SupplyDestDoOrgID
            ,ktsd.SupplyDestType
            ,ktsd.SupplyDestProcessType
            ,ktsd.SupplyBatchCategory
            ,ktsd.Notes
            ,ktsd.ChangeLog
            ,ktsd.ChangeBy
            ,ktsd.AutoBatchNumber AS DeliveryAutoNumber
            ,ktsd.DestPo AS DestPO
            ,ktsd.PackageNumber AS DestNumberPackage
            ,ktsd.Weather
            ,ktsd.ReceivedDate AS ShippingDate
            ,CASE
            WHEN ktsd.DeliveryStatusID = 1 THEN 'Open'
            WHEN ktsd.DeliveryStatusID = 2 THEN 'Close'
            WHEN ktsd.DeliveryStatusID = 3 THEN 'Sent'
            WHEN ktsd.DeliveryStatusID = 4 THEN 'Delivered'
            WHEN ktsd.DeliveryStatusID = 5 THEN 'Finish'
            ELSE '-'
            END AS StatusDelivery
            ,ktsd.ArrivalEstimation
            ,ktsd.PackageNumber
            ,ktsd.DestDriver
            ,ktsd.DestTransportID
            ,ktsd.DestTransportNumber
            ,ktsd.CreatedBy
            ,ktsd.DateCreated
            ,ktsd.LastModifiedBy
            ,ktsd.DateUpdated
            ,ktsd.SMESPCodeID
            ,vso.Name Destination
            ,sum(ktsdd.Weight) AS VolumeBruto
            ,sum(ktsdd.Weight) AS VolumeNetto
            ,ktsdd.StatusCode AS StatusCode
            ,ktsd.FinalCapacity
            ,ktsd.PaymentDelivery
            ,ktsd.PalmTypeID
            ", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->join("view_tc_supplychain_org vso", "vso.SupplychainID = ktsd.SupplyDestMillOrgID", 'left');
        $this->db->join("ktv_tc_supplychain_delivery_detail ktsdd","ktsdd.DeliveryID= ktsd.DeliveryID",'left');
        $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
        $this->db->where("ktsd.StatusCode","active");
        $this->db->where("ktsd.DeliveryStatusID != 0", NULL, FALSE);

    if ($is_admin != 1) {
        $this->db->where("ktsd.SupplychainID", @$SID);
    }

    $this->db->group_by("ktsd.DeliveryID");
    $this->db->order_by("ktsd.DeliveryID DESC");
    
    $query = $this->db->get();
    
    if($query->num_rows() > 0) {
        $getSupplychainID        = $this->delivery($SID);

        if($getSupplychainID > 0){
            foreach($getSupplychainID as $data){
                $SupplychainID = $data['SupplychainID'];
            }
        }

        foreach ($query->result_array() as $row) {
            
            $DeliveryDetail    = $this->deliveryDetail($row['DeliveryID']);
            
            $data = array(
                "DeliveryDetail" => $DeliveryDetail,
                "SupplychainID"  => $SupplychainID
            );
            
            $result[] = array_merge($row,$data);
        }   
        
        return $result;
        } else {
            return $result[] = array(); 
        }
    }

    public function submit_delivery_api_post($data){
       
        $return = array();
 
        if($data['LastModifiedBy']==""){
            $LastModifiedBy=NULL;
        }else{
            $LastModifiedBy=$data['LastModifiedBy'];
        }
       
        $DeliveryDate        = !empty($data['DeliveryDate']) ? $data['DeliveryDate'] : null;
 
        $date = date_create($DeliveryDate);
 
        $newDate = date_format($date,"Y-m-d");
        
        if(!empty($data['DeliveryNumber'])){
            //jika dari fg delivery number nya ga kosong ambil yg dari fg
            $DeliveryNumber = $data['DeliveryNumber'];
        } else {
            //jika dari fg delivery number nya kosong ambil generate api
            $DeliveryNumber = $this->_generateDeliveryNumberAPI($data['SupplychainID'], $newDate);
        }   
       
        
        $data_delivery = array(
            'SupplychainID'         => $data['SupplychainID'],
           
            'DeliveryID'            => $data['DeliveryID'],
 
            'SupplyDestDoOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
            'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
            'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
            'SupplyDestDoOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
            'SMESPCodeID'           => $data['SMESPCodeID'],
            'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
            'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
            'SupplyDestProcessType' => $data['SupplyDestProcessType'],
            'SupplyDestType'        => $data['SupplyDestType'],
            'SupplyDestMillOtherName' => $data['SupplyDestMillOtherName'],
            // 'OtherSeller'          =>!empty(@$data['OtherSeller']) ? @$data['OtherSeller']: null,
            'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
            'DeliveryNumber'        => $DeliveryNumber,
            'DeliveryDate'          => $DeliveryDate,
            'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
            'TotalWeight'           => $data['TotalWeight'],
            'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
            'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
 
            'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
            'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,
            
            'ArrivalEstimation'     => $data['ArrivalEstimation'],
 
            'DestDriver'            => @$data['DestDriver'],
            'DestTransportID'       => @$data['DestTransportID'],
            'DestTransportNumber'   => @$data['DestTransportNumber'],
 
            'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
            'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
            'Weather'               => $data['Weather'],
            'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
 
            'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
            'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
            'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
 
            'StatusCode'            => "active",
            'CreatedBy'             => $data['CreatedBy'],
            'DateCreated'           => $data['DateCreated'],
 
            'LastModifiedBy'        => $data['LastModifiedBy'],
            'DateUpdated'           => $data['DateUpdated']
        );
        
        if($data_delivery){        
 
                //jika save sebelum delivery pick dan sync offline
                if($data_delivery['DeliveryID'] =='' && $data_delivery['DeliveryID'] == '0'){
                    
                    //insert tb : ktv_tc_supplychain_delivery
                    $query = $this->db->insert('ktv_tc_supplychain_delivery', $data_delivery);
                    $DeliveryID = $this->db->insert_id();
                    
                    $return['success'] = true;

                    $check = $this->db->query("SELECT * FROM ktv_tc_supplychain_delivery_detail WHERE DeliveryID=? ", array(@$data['DeliveryID']));
                                
                    $DeliveryDetail = $check->result_array();

                    if(!empty($DeliveryDetail)){
                        if($DeliveryDetail){
                            foreach($DeliveryDetail as $key => $valuesUpdate){

                                $DeliveryDetailID = !empty(@$valuesUpdate['DeliveryDetailID']) ? @$valuesUpdate['DeliveryDetailID']: null;
                                $update['DeliveryIDApp'] = !empty(@$valuesUpdate['DeliveryIDApp']) ? @$valuesUpdate['DeliveryIDApp']: null;
                                $update['DeliveryID'] =  !empty(@$valuesUpdate['DeliveryID']) ? @$valuesUpdate['DeliveryID']: null;
                                $update['SupplyBatchID'] = !empty(@$valuesUpdate['SupplyBatchID']) ? @$valuesUpdate['SupplyBatchID']: null;
                                $update['DetailNumber'] = !empty(@$valuesUpdate['DetailNumber']) ? @$valuesUpdate['DetailNumber']: null;
                                $update['Weight'] = !empty(@$valuesUpdate['Weight']) ? @$valuesUpdate['Weight']: null;
                                $update['FFBWeight'] = !empty(@$valuesUpdate['FFBWeight']) ? @$valuesUpdate['FFBWeight']: null;
                                $update['BrondolanWeight'] = !empty(@$valuesUpdate['BrondolanWeight']) ? @$valuesUpdate['BrondolanWeight']: null;
                                $update['TotalPaymentDelivery'] = !empty(@$valuesUpdate['TotalPaymentDelivery']) ? @$valuesUpdate['TotalPaymentDelivery']: null;
                            }
                            $this->db->where('DeliveryDetailID', $DeliveryDetailID);
                            $this->db->update('ktv_tc_supplychain_delivery_detail', $update);
                            $return['data'] = array(
                                'DeliveryIDApp' => @$data['DeliveryIDApp'],
                                'DeliveryID' => @$DeliveryID,
                                'DeliveryNumber' => @$DeliveryNumber,
                                'DeliveryDetail' =>@$data['DeliveryDetail']
                            );
                     
                            return $return;
                        }
                    }else{
                        unset($data['DeliveryDetailID']);
                        
                        if($data['DeliveryDetail']){
                            foreach ($data['DeliveryDetail'] as $key => $value) {
                                $insert['DeliveryIDApp']        = !empty(@$value['DeliveryIDApp']) ? @$value['DeliveryIDApp']: null;
                                $insert['DeliveryID']           = $DeliveryID;
                                $insert['SupplyBatchID']        = !empty(@$value['SupplyBatchID']) ? @$value['SupplyBatchID']: null;
                                $insert['DetailNumber']         = !empty(@$value['DetailNumber']) ? @$value['DetailNumber']: null;
                                $insert['FFBWeight']            = !empty(@$value['FFBWeight']) ? @$value['FFBWeight']: null;
                                $insert['BrondolanWeight']      = !empty(@$value['BrondolanWeight']) ? @$value['BrondolanWeight']: null;
                                $insert['Weight']               = !empty(@$value['Weight']) ? @$value['Weight']: null;
                                $insert['TotalPaymentDelivery'] = !empty(@$value['TotalPaymentDelivery']) ? @$value['TotalPaymentDelivery']: null;
                            
                                $this->db->insert('ktv_tc_supplychain_delivery_detail', $insert, $DeliveryID);
                                $data['DeliveryDetailID'] = $this->db->insert_id();
                            }
                        }
                    }
                    $return['data'] = array(
                        'DeliveryIDApp' => @$data['DeliveryIDApp'],
                        'DeliveryID' => @$DeliveryID,
                        'DeliveryNumber' => @$DeliveryNumber,
                        'DeliveryDetail' =>@$data['DeliveryDetail']
                    );
             
                    return $return;
                } 
                
                if($data_delivery['DeliveryID'] !='' && $data_delivery['DeliveryID']!='0'){

                    $check = $this->db->query("SELECT DeliveryID FROM ktv_tc_supplychain_delivery WHERE DeliveryID=?", array($data_delivery['DeliveryID']));
                    
                    if($check->num_rows() > 0) {
                        
                        if($data_delivery['SupplyDestType'] == 'mill'){

                            $data_delivery = array(
                                'SupplychainID'         => $data['SupplychainID'],
                                
                                'DeliveryID'            => $data['DeliveryID'],
                                
 
                                'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
                                'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
                                'SupplyDestDoOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
                                'SMESPCodeID'           => $data['SMESPCodeID'],
                                'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
                                'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
                                'SupplyDestProcessType' => $data['SupplyDestProcessType'],
                                'SupplyDestType'        => $data['SupplyDestType'],
                                'SupplyDestMillOtherName' => !empty(@$data['SupplyDestMillOtherName']) ? @$data['SupplyDestMillOtherName']: '',
                                'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
                                'DeliveryNumber'        => $DeliveryNumber,
                                'DeliveryDate'          => $DeliveryDate,
                                'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
                                'TotalWeight'           => $data['TotalWeight'],
                                'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
                                'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
                    
                                'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
                                'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,

                                'ArrivalEstimation'     => $data['ArrivalEstimation'],
                    
                                'DestDriver'            => @$data['DestDriver'],
                                'DestTransportID'       => @$data['DestTransportID'],
                                'DestTransportNumber'   => @$data['DestTransportNumber'],
                    
                                'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
                                'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
                                'Weather'               => $data['Weather'],
                                'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
                    
                                'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
                                'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
                                'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
                    
                                'StatusCode'            => "active",
                                'CreatedBy'             => $data['CreatedBy'],
                                'DateCreated'           => $data['DateCreated'],

                                'LastModifiedBy'        => $data['LastModifiedBy'],
                                'DateUpdated'           => $data['DateUpdated']
                            );
                        } elseif($data_delivery['SupplyDestType'] == 'do') {
                            $data_delivery = array(
                                'SupplychainID'         => $data['SupplychainID'],
                                
                                'DeliveryID'            => $data['DeliveryID'],
                                
 
                                'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
                                'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
                                'SupplyDestDoOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
                                'SMESPCodeID'           => $data['SMESPCodeID'],
                                'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
                                'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
                                'SupplyDestProcessType' => $data['SupplyDestProcessType'],
                                'SupplyDestType'        => $data['SupplyDestType'],
                                'SupplyDestMillOtherName' => !empty(@$data['SupplyDestMillOtherName']) ? @$data['SupplyDestMillOtherName']: '',
                                'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
                                'DeliveryNumber'        => $DeliveryNumber,
                                'DeliveryDate'          => $DeliveryDate,
                                'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
                                'TotalWeight'           => $data['TotalWeight'],
                                'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
                                'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
                    
                                'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
                                'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,

                                'ArrivalEstimation'     => $data['ArrivalEstimation'],
                    
                                'DestDriver'            => @$data['DestDriver'],
                                'DestTransportID'       => @$data['DestTransportID'],
                                'DestTransportNumber'   => @$data['DestTransportNumber'],
                    
                                'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
                                'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
                                'Weather'               => $data['Weather'],
                                'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
                    
                                'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
                                'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
                                'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
                    
                                'StatusCode'            => "active",
                                'CreatedBy'             => $data['CreatedBy'],
                                'DateCreated'           => $data['DateCreated'],

                                'LastModifiedBy'        => $data['LastModifiedBy'],
                                'DateUpdated'           => $data['DateUpdated']
                            );
                        } else {
                            
                            $data_delivery = array(
                                'SupplychainID'         => $data['SupplychainID'],
                                
                                'DeliveryID'            => $data['DeliveryID'],
                                
 
                                'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
                                'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
                                'SupplyDestMillOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
                                'SMESPCodeID'           => $data['SMESPCodeID'],
                                'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
                                'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
                                'SupplyDestProcessType' => $data['SupplyDestProcessType'],
                                'SupplyDestType'        => $data['SupplyDestType'],
                                'SupplyDestMillOtherName' => !empty(@$data['SupplyDestMillOtherName']) ? @$data['SupplyDestMillOtherName']: '',
                                'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
                                'DeliveryNumber'        => $DeliveryNumber,
                                'DeliveryDate'          => $DeliveryDate,
                                'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
                                'TotalWeight'           => $data['TotalWeight'],
                                'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
                                'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
                    
                                'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
                                'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,

                                'ArrivalEstimation'     => $data['ArrivalEstimation'],
                    
                                'DestDriver'            => @$data['DestDriver'],
                                'DestTransportID'       => @$data['DestTransportID'],
                                'DestTransportNumber'   => @$data['DestTransportNumber'],
                    
                                'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
                                'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
                                'Weather'               => $data['Weather'],
                                'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
                    
                                'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
                                'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
                                'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
                    
                                'StatusCode'            => "active",
                                'CreatedBy'             => $data['CreatedBy'],
                                'DateCreated'           => $data['DateCreated'],

                                'LastModifiedBy'        => $data['LastModifiedBy'],
                                'DateUpdated'           => $data['DateUpdated']
                            );
                        }

                        //update tb : ktv_tc_supplychain_delivery
                        $this->db->where('DeliveryID', $data_delivery['DeliveryID']);
                        $this->db->update('ktv_tc_supplychain_delivery', $data_delivery);
                        $DeliveryID = $data_delivery['DeliveryID'];

                        $return['success'] = true;
                    } else {

                        if($data_delivery['SupplyDestType'] == 'mill'){

                            $data_delivery = array(
                                'SupplychainID'         => $data['SupplychainID'],
                                
                                'DeliveryID'            => $data['DeliveryID'],
                                
 
                                'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
                                'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
                                'SupplyDestDoOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
                                'SMESPCodeID'           => $data['SMESPCodeID'],
                                'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
                                'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
                                'SupplyDestProcessType' => $data['SupplyDestProcessType'],
                                'SupplyDestType'        => $data['SupplyDestType'],
                                'SupplyDestMillOtherName' => $data['SupplyDestMillOtherName'],
                                'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
                                'DeliveryNumber'        => $DeliveryNumber,
                                'DeliveryDate'          => $DeliveryDate,
                                'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
                                'TotalWeight'           => $data['TotalWeight'],
                                'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
                                'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
                    
                                'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
                                'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,

                                'ArrivalEstimation'     => $data['ArrivalEstimation'],
                    
                                'DestDriver'            => @$data['DestDriver'],
                                'DestTransportID'       => @$data['DestTransportID'],
                                'DestTransportNumber'   => @$data['DestTransportNumber'],
                    
                                'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
                                'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
                                'Weather'               => $data['Weather'],
                                'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
                    
                                'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
                                'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
                                'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
                    
                                'StatusCode'            => "active",
                                'CreatedBy'             => $data['CreatedBy'],
                                'DateCreated'           => $data['DateCreated'],

                                'LastModifiedBy'        => $data['LastModifiedBy'],
                                'DateUpdated'           => $data['DateUpdated']
                            );
                        } elseif($data_delivery['SupplyDestType'] == 'do') {
                            $data_delivery = array(
                                'SupplychainID'         => $data['SupplychainID'],
                                
                                'DeliveryID'            => $data['DeliveryID'],
                                
 
                                'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
                                'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
                                'SupplyDestDoOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
                                'SMESPCodeID'           => $data['SMESPCodeID'],
                                'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
                                'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
                                'SupplyDestProcessType' => $data['SupplyDestProcessType'],
                                'SupplyDestType'        => $data['SupplyDestType'],
                                'SupplyDestMillOtherName' => $data['SupplyDestMillOtherName'],
                                'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
                                'DeliveryNumber'        => $DeliveryNumber,
                                'DeliveryDate'          => $DeliveryDate,
                                'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
                                'TotalWeight'           => $data['TotalWeight'],
                                'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
                                'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
                    
                                'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
                                'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,

                                'ArrivalEstimation'     => $data['ArrivalEstimation'],
                    
                                'DestDriver'            => @$data['DestDriver'],
                                'DestTransportID'       => @$data['DestTransportID'],
                                'DestTransportNumber'   => @$data['DestTransportNumber'],
                    
                                'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
                                'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
                                'Weather'               => $data['Weather'],
                                'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
                    
                                'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
                                'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
                                'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
                    
                                'StatusCode'            => "active",
                                'CreatedBy'             => $data['CreatedBy'],
                                'DateCreated'           => $data['DateCreated'],

                                'LastModifiedBy'        => $data['LastModifiedBy'],
                                'DateUpdated'           => $data['DateUpdated']
                            );
                        } else {
                            
                            $data_delivery = array(
                                'SupplychainID'         => $data['SupplychainID'],
                                
                                'DeliveryID'            => $data['DeliveryID'],
                                
                                
                                'SupplyDestMillOrgID'   => $data['SupplyDestMillOrgID'],
                                'SupplyDestOrgID'       => $data['SupplyDestOrgID'],
                                'SupplyDestMillOrgID'     => !empty(@$data['SupplyDestDoOrgID']) ? @$data['SupplyDestDoOrgID']: null,
                                'SMESPCodeID'           => $data['SMESPCodeID'],
                                'DestPo'                => !empty(@$data['DestPO']) ? @$data['DestPO']: null,
                                'SupplyBatchCategory'   => !empty(@$data['SupplyBatchCategory']) ? @$data['SupplyBatchCategory']: null,
                                'SupplyDestProcessType' => $data['SupplyDestProcessType'],
                                'SupplyDestType'        => $data['SupplyDestType'],
                                'SupplyDestMillOtherName' => $data['SupplyDestMillOtherName'],
                                'DeliveryStatusID'      => empty($data['DeliveryStatusID']) ? 1 : $data['DeliveryStatusID'],
                                'DeliveryNumber'        => $DeliveryNumber,
                                'DeliveryDate'          => $DeliveryDate,
                                'ExternalCode'          => @$data['ExternalCode']=='' ? NULL : $data['ExternalCode'],
                                'TotalWeight'           => $data['TotalWeight'],
                                'PackageWeight'         => !empty(@$data['PackageWeight']) ? @$data['PackageWeight']: null,
                                'DestWeight'            => !empty(@$data['ReceivedWeight']) ? @$data['ReceivedWeight']: null,
                    
                                'FinalCapacity'         => !empty(@$data['FinalCapacity']) ? @$data['FinalCapacity']: null,
                                'PaymentDelivery'       => !empty(@$data['PaymentDelivery']) ? @$data['PaymentDelivery']: null,

                                'ArrivalEstimation'     => $data['ArrivalEstimation'],
                    
                                'DestDriver'            => @$data['DestDriver'],
                                'DestTransportID'       => @$data['DestTransportID'],
                                'DestTransportNumber'   => @$data['DestTransportNumber'],
                    
                                'AutoBatchNumber'       => !empty(@$data['DeliveryAutoNumber']) ? @$data['DeliveryAutoNumber']: null,
                                'PackageNumber'         => !empty(@$data['DestNumberPackage']) ? @$data['DestNumberPackage']: null,
                                'Weather'               => $data['Weather'],
                                'ReceivedDate'          => !empty(@$data['ShippingDate']) ? @$data['ShippingDate']: null,
                    
                                'Notes'                 => !empty(@$data['Notes']) ? @$data['Notes']: null,
                                'ChangeLog'             => !empty(@$data['ChangeLog']) ? @$data['ChangeLog']: null,
                                'ChangeBy'              => !empty(@$data['ChangeBy']) ? @$data['ChangeBy']: null,
                    
                                'StatusCode'            => "active",
                                'CreatedBy'             => $data['CreatedBy'],
                                'DateCreated'           => $data['DateCreated'],

                                'LastModifiedBy'        => $data['LastModifiedBy'],
                                'DateUpdated'           => $data['DateUpdated']
                            );
                        }
                        
                        //insert tb : ktv_tc_supplychain_delivery
                        $query = $this->db->insert('ktv_tc_supplychain_delivery', $data_delivery);
                        $DeliveryID = $this->db->insert_id();
                        $return['success'] = true;
                    }
                } 
                
                //proses submit ke table : ktv_tc_supplychain_delivery_detail
                if($return['success'] == true){
                    
                    $dataDeliveryDetail = array();

                    if(!empty($data['DeliveryDetail'])){
                    
                    $check = $this->db->query("SELECT * FROM ktv_tc_supplychain_delivery_detail WHERE DeliveryID=? ", array(@$data['DeliveryID']));
                                
                    $DeliveryDetail = $check->result_array();
                
                    if(!empty($DeliveryDetail)){
                        if($DeliveryDetail){
                            foreach($DeliveryDetail as $key => $valuesUpdate){
                                
                                $DeliveryDetailID = !empty(@$valuesUpdate['DeliveryDetailID']) ? @$valuesUpdate['DeliveryDetailID']: null;
                                $update['DeliveryIDApp'] = !empty(@$valuesUpdate['DeliveryIDApp']) ? @$valuesUpdate['DeliveryIDApp']: null;
                                $update['DeliveryID'] =  !empty(@$valuesUpdate['DeliveryID']) ? @$valuesUpdate['DeliveryID']: null;
                                $update['SupplyBatchID'] = !empty(@$valuesUpdate['SupplyBatchID']) ? @$valuesUpdate['SupplyBatchID']: null;
                                $update['DetailNumber'] = !empty(@$valuesUpdate['DetailNumber']) ? @$valuesUpdate['DetailNumber']: null;
                                $update['FFBWeight'] = !empty(@$valuesUpdate['FFBWeight']) ? @$valuesUpdate['FFBWeight']: null;
                                $update['BrondolanWeight'] = !empty(@$valuesUpdate['BrondolanWeight']) ? @$valuesUpdate['BrondolanWeight']: null;
                                $update['Weight'] = !empty(@$valuesUpdate['Weight']) ? @$valuesUpdate['Weight']: null;
                                $update['TotalPaymentDelivery'] = !empty(@$valuesUpdate['TotalPaymentDelivery']) ? @$valuesUpdate['TotalPaymentDelivery']: null;
                            }
                            $this->db->where('DeliveryDetailID', $DeliveryDetailID);
                            $this->db->update('ktv_tc_supplychain_delivery_detail', $update);
                        }
                    }else{
                        unset($data['DeliveryDetailID']);
                        if($data['DeliveryDetail']){
                            foreach ($data['DeliveryDetail'] as $key => $value) {
                                $insert['DeliveryIDApp']        = !empty(@$value['DeliveryIDApp']) ? @$value['DeliveryIDApp']: null;
                                $insert['DeliveryID']           = $DeliveryID;
                                $insert['SupplyBatchID']        = !empty(@$value['SupplyBatchID']) ? @$value['SupplyBatchID']: null;
                                $insert['DetailNumber']         = !empty(@$value['DetailNumber']) ? @$value['DetailNumber']: null;
                                $insert['FFBWeight']            = !empty(@$value['FFBWeight']) ? @$value['FFBWeight']: null;
                                $insert['BrondolanWeight']      = !empty(@$value['BrondolanWeight']) ? @$value['BrondolanWeight']: null;
                                $insert['Weight']               = !empty(@$value['Weight']) ? @$value['Weight']: null;
                                $insert['TotalPaymentDelivery'] = !empty(@$value['TotalPaymentDelivery']) ? @$value['TotalPaymentDelivery']: null;
                            
                                $this->db->insert('ktv_tc_supplychain_delivery_detail', $insert, $DeliveryID);
                                $data['DeliveryDetailID'] = $this->db->insert_id();
                            }
                        }
                    }

                    $return['success'] = true;
                    
                } else {
                    $DeliveryDetail = array();
                }
            } else {
                $return['success'] = false;
            }
        }
    
        $return['data'] = array(
            'DeliveryIDApp' => @$post['DeliveryIDApp'],
            'DeliveryID' => @$DeliveryID,
            'DeliveryNumber' => @$DeliveryNumber,
            'DeliveryDetail' =>@$DeliveryDetail
        );
 
        return $return;
    }

    public function delete_delivery_api($data){
        
        $results = array();

        $data_delivery = array(

            'SupplyBatchID'         => $data['SupplyBatchID'],

            'Weight'                => $data['Weight'],
           
            'DeliveryID'            => $data['DeliveryID'],

            'StatusCode'            => 'nullified'
        );

        $SupplyBatchNumber  = $data['SupplyBatchNumber'];

        $SupplyBatchID      = $data['SupplyBatchID'];
       
        $sql = "SELECT 
                    sb.RemainingWeight AS RemainingWeight
                FROM 
                    ktv_tc_supplychain_batch sb
                WHERE 
                    sb.SupplyBatchNumber = '$SupplyBatchNumber' AND sb.StatusCode = 'active' AND sb.SupplyBatchID = '$SupplyBatchID'";

        $supplychain_batch = $this->db->query($sql)->row_array();
        
        $dataBatch['RemainingWeight']  = $supplychain_batch['RemainingWeight'] + $data_delivery['Weight'];
        
        $this->db->where('SupplyBatchNumber', $SupplyBatchNumber);
        $this->db->update('ktv_tc_supplychain_batch', $dataBatch);
        
        $check = $this->db->query("SELECT DeliveryID FROM ktv_tc_supplychain_delivery_detail WHERE DeliveryID=?", array($data_delivery['DeliveryID']));

        if($check->num_rows()>0){
            $data = $check->row();

            if($data){
                //update tb : ktv_tc_supplychain_delivery
                $this->db->where('DeliveryID', $data_delivery['DeliveryID']);
                $this->db->update('ktv_tc_supplychain_delivery_detail', $data_delivery);
                $DeliveryID = $data_delivery['DeliveryID'];

                $results['success'] = true;
                $results['message'] = lang("Data deleted");

            } else {
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            }

            return $results;
        }
        
        return $results;
    }

    private function deliveryDetail($DeliveryID)
    {   
        $query = $this->db->select("SQL_CALC_FOUND_ROWS
                           ktsdd.DeliveryDetailID
                           ,ktsdd.DeliveryID
                           ,ktsdd.StatusCode AS Status	
                           ,ktsdd.SupplyBatchID
                           ,ktsdd.FFBWeight
                           ,ktsdd.BrondolanWeight
                           ,ktsdd.Weight AS Weight", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
        $this->db->where("ktsdd.DeliveryID",$DeliveryID);
        $this->db->where("ktsdd.StatusCode","active");
        // $this->db->group_by("ktsdd.DeliveryID");
        $this->db->order_by("ktsdd.DeliveryID","DESC");

        $query = $this->db->get();
        // echo '<pre>'.$this->db->last_query();die;

        $result = $query->result_array();
        
        return $result;
    }

    private function InsertdeliveryDetail($DeliveryID,$DataDetail){
        
        $insert['DeliveryIDApp']        = !empty(@$DataDetail['DeliveryIDApp']) ? @$DataDetail['DeliveryIDApp']: null;
        $insert['DeliveryID']           = $DeliveryID;
        $insert['SupplyBatchID']        = !empty(@$DataDetail['SupplyBatchID']) ? @$DataDetail['SupplyBatchID']: null;
        $insert['DetailNumber']         = !empty(@$DataDetail['DetailNumber']) ? @$DataDetail['DetailNumber']: null;
        $insert['Weight']               = !empty(@$DataDetail['Weight']) ? @$DataDetail['Weight']: null;
        $insert['TotalPaymentDelivery'] = !empty(@$DataDetail['TotalPaymentDelivery']) ? @$DataDetail['TotalPaymentDelivery']: null;
    
        $this->db->insert('ktv_tc_supplychain_delivery_detail', $insert, $DeliveryID);
        $data['DeliveryDetailID'] = $this->db->insert_id();

        $return['status'] = true;
    }
    

    private function delivery($SupplychainID)
    {   
        $this->db->select("ktsd.SupplychainID", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->join("view_tc_supplychain_org vso", "vso.SupplychainID = ktsd.SupplyDestMillOrgID", 'left');
        $this->db->where("ktsd.StatusCode","active");
        $this->db->where("ktsd.SupplychainID", $SupplychainID);

        $query = $this->db->get();
    
        $result = $query->result_array();

        return $result;
    }

    public function tcSumbitDeliveryDetail($data,$DeliveryID){

        if($data['DeliveryID'] != "") {
                
            $dataDeliveryDetail['DeliveryID']            = $data['DeliveryID'];
            $dataDeliveryDetail['SupplyBatchID']         = $data['SupplyBatchID'];
            $dataDeliveryDetail['Weight']                = $data['TotalWeight'];
            $dataDeliveryDetail['CreatedBy']             = $_SESSION['userid'];
            $dataDeliveryDetail['DateCreated']           = date('Y-m-d H:i:s');

            $this->db->insert('ktv_tc_supplychain_delivery_detail', $dataDeliveryDetail);
            
            //pengurangan remaining pada tb : ktv_tc_supplychain_batch
            $sql = "SELECT 
                        sb.RemainingWeight
                    FROM 
                        ktv_tc_supplychain_batch sb
                    WHERE 
                        sb.SupplyBatchID = {$dataDeliveryDetail['SupplyBatchID']} AND sb.StatusCode = 'active'";

            $supplychain_batch = $this->db->query($sql)->row_array();

            if(!empty($supplychain_batch)){
                $dataBatch['RemainingWeight']  = $supplychain_batch['RemainingWeight'] - $data['TotalWeight'];
            
                $this->db->where('SupplyBatchID', $dataDeliveryDetail['SupplyBatchID']);
                $this->db->update('ktv_tc_supplychain_batch', $dataBatch);
                
                $DeliveryID = $data['DeliveryID'];
            } else {

               $return['status'] = false;
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = lang("Failed to save data");
            } else {
                $this->db->trans_commit();
                $results['success']    = true;
                $results['message']    = lang("Data saved");
                $results['DeliveryID'] = $DeliveryID;
            }

        } else {
            $results['success']    = false;
            $results['message']    = lang("Failed to save data because processing status is " . $data['Status']);
            $results['DeliveryID'] = $DeliveryID;
        }
        //end
        $DeliveryID = $this->db->insert_id();
        
        $return['status'] = true;
    }

    public function getSPCode($id)
    {
        $sql = "SELECT 
                    ObJID
                FROM 
                    view_tc_supplychain_org 
                WHERE 
                    SupplychainID = '$id'";

        $ObJID = $this->db->query($sql)->row_array();
        
        $MillID = $ObJID['ObjID'];
        
        $sql = "SELECT
                ssp.SMESPCodeID AS id,
                msp.SuratNr AS name
            FROM
                ktv_tc_supplychain_org a
                LEFT JOIN `ktv_tc_supplychain_org_rel` b ON a.SupplyChainID = b.ChildID
                LEFT JOIN `ktv_tc_supplychain_org` a1 ON a1.SupplyChainID = b.ParentID
                LEFT JOIN ktv_members m ON m.MemberID = a1.ObjID
                LEFT JOIN `ktv_sme_sp_code` ssp ON ssp.MemberID = m.MemberID
                LEFT JOIN `ktv_mill_sp_code` msp ON msp.SPCodeID = ssp.SPCodeID 
                LEFT JOIN ktv_mill d on d.MillID = msp.MillID
            WHERE
                d.MillID = ?
            GROUP BY d.MillID";

        $query = $this->db->query($sql,array($MillID));
        
        if($query->num_rows()){
            $result = $query->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }

            $return['data'] = $result;

            return $return;
        }

        return $return;
    }

    function _generateDeliveryNumber($SupplychainID, $DeliveryDate){
        $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_delivery WHERE SupplychainID=? AND DeliveryDate LIKE ?";
        $query = $this->db->query($sql, array($SupplychainID, "%$DeliveryDate%"));
        
        $total = intval(@$query->row()->total)+1;

        $batch_number = 'XD'.sprintf("%04d", $SupplychainID).date('Ymd', strtotime($DeliveryDate)).sprintf("%06d", $total);
        return $batch_number;
    }

    function _generateDeliveryNumberAPI($SupplychainID, $DeliveryDate){
        $sql = "SELECT DeliveryNumber FROM ktv_tc_supplychain_delivery WHERE SupplychainID = ? AND DATE(DeliveryDate) = ? ORDER BY DeliveryID DESC LIMIT 1";
        
        $query = $this->db->query($sql,array($SupplychainID, $DeliveryDate));
        if($query->num_rows() > 0){
            $total = intval(substr($query->row()->DeliveryNumber, 14)) + 1;
            $batch_number = 'XD'.sprintf("%04d", $SupplychainID).date('Ymd', strtotime($DeliveryDate)).sprintf("%06d", $total);
        } else {
            $total = '1';
            $batch_number = 'XD'.sprintf("%04d", $SupplychainID).date('Ymd', strtotime($DeliveryDate)).sprintf("%06d", $total);
        }
        
        return $batch_number;
    }

    public function tcSumbitDeliveryDetailNew($data,$DeliveryID){
       
        $check = $this->db->query("SELECT * FROM ktv_tc_supplychain_delivery_detail WHERE DeliveryDetailID=? AND DeliveryID=? AND SupplyBatchID=?", array(@$data['DeliveryDetailID'], @$data['DeliveryID'], @$data['SupplyBatchID']));
        if($check->num_rows() > 0){
            $this->db->where('DeliveryDetailID', @$data['DeliveryDetailID']);
            $this->db->update('ktv_tc_supplychain_delivery_detail', $data);
            //$DeliveryDetailID = @$data['DeliveryDetailID']
        }else{
            unset($data['DeliveryDetailID']);
            $this->db->insert('ktv_tc_supplychain_delivery_detail', $data);
            $data['DeliveryDetailID'] = $this->db->insert_id();
        }
        return $data;
    }
    
    public function transactionDetail($DeliveryID)
    {
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $status = $this->input->get('status');
        
        /* pencarian */ 
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');

        if($SupplyKey){
            $this->db->like('IFNULL(a.AgentOther,IFNULL( e.Name, b.MemberName ))', $SupplyKey);
        }
        
        $this->db->select("a.DeliveryID, 
                           a.DestTransportNumber,
                           b.SupplyBatchID, 
                           c.SupplyBatchNumber, 
                           CASE
                                WHEN d.SupplyType = 'Farmer' THEN '" . lang('Farmer') . "'
                                WHEN d.SupplyType = 'Batch' THEN '" . lang('Batch') . "'
                                WHEN d.SupplyType = 'Nonfarmer' THEN '" . lang('Nonfarmer') . "'
                                WHEN d.SupplyType = 'Delivery' THEN '" . lang('Delivery') . "'
                                ELSE '-'
                           END AS SupplyType,
                           IFNULL(e.MemberDisplayID,d.SupplyID) AS MemberDisplayID,  
                           d.DateTransaction,
                           IF(
                            e.MemberName IS NULL OR e.MemberName = '',
                            IF(
                                kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                IF(
                                    d.MillOther IS NULL OR d.MillOther = '',
                                    IF(
                                        mem.Name IS NULL OR mem.Name = '',
                                        IF(
                                            d.DOOther IS NULL OR d.DOOther = '',
                                            IF(
                                                d.AgentOther IS NULL OR d.AgentOther = '',
                                                'Nonfarmer',
                                                d.AgentOther
                                            ),
                                            d.DOOther
                                        ),
                                        mem.Name
                                    ),
                                    d.MillOther
                                ),
                                kms.agCompanyName
                            ),
                            e.MemberName
                        ) SupplierName,
                        d.VolumeNetto,
                        d.VolumeBruto,
                        IF(e.isCertified = 1, 'Yes', 'No') Certified",false);
            $this->db->from("ktv_tc_supplychain_delivery a");
            $this->db->join('ktv_tc_supplychain_delivery_detail b',"a.DeliveryID=b.DeliveryID", 'left');
            $this->db->join('ktv_tc_supplychain_batch c', "c.SupplyBatchID=b.SupplyBatchID", 'left');
            $this->db->join('ktv_tc_supplychain_transaction d', "d.SupplyBatchID=c.SupplyBatchID", 'left');
            $this->db->join('ktv_members e', "e.MemberID=d.SupplyID AND d.SupplyType != 'Batch'", 'left');
            $this->db->join('view_tc_supplychain_org mem', 'mem.SupplychainID = a.SupplychainID', 'left');
            $this->db->join('ktv_ref_certification_program cp', 'cp.CertProgID = e.isCertified', 'left');
            $this->db->join('view_tc_supplychain_org vso3', 'vso3.SupplychainID = d.SupplyID AND d.SupplyType = "NonFarmer"','left');
            $this->db->join('ktv_members_extension kms', 'kms.MemberID = vso3.ObjID','left');

            $this->db->where('d.StatusCode', 'active');
            $this->db->where('a.DeliveryID', $DeliveryID);
            // $this->db->group_by('e.MemberID');
            $this->db->order_by('d.TransNumber', 'DESC');
            $this->db->limit($limit, $start);
            $Q = $this->db->get();
            // echo '<pre>'.$this->db->last_query();die;
            
            if($Q->num_rows()){
                $result = $Q->result();
                foreach($result as $row){
                    if($row->AgentOther == ""){
                        $row->AgentOther = "Yes";
                    }else{
                        $row->AgentOther = "No";
                    }

                    if($row->AgentOtherSurvey == 1){
                        $row->AgentOtherSurvey = "Yes";
                    }else{
                        $row->AgentOtherSurvey = "No";
                    }
                }
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
                return $data;
            }
        }

    private function getUID($length = 11) {
        $characters = '012345678910';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
