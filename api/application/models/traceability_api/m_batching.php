<?php

class M_batching extends CI_Model 
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

        $is_admin = $_SESSION['is_admin'];

        $status = array('Open', 'Closed');

        $this->db->select("SQL_CALC_FOUND_ROWS 
            sb.SupplyBatchID
            , sb.SupplyBatchDate
            , sb.SupplyOrgID
            , sb.SupplyDestOrgID
            , sb.SupplyBatchStatus AS Status
            , sb.SupplyBatchNumber
            , sb.DeliveryDate
            , sb.DestPO
            , sb.DestWeight
            , sb.ReceivedDate
            , sb.DateCreated
            , IF (SUM(st.VolumeBruto) IS NULL, FORMAT(0, 2), SUM(st.VolumeBruto)) AS VolumeBruto
            , vso.Name AS AgentName
            , vso1.Name AS DestinationName", FALSE);
        $this->db->from("ktv_tc_supplychain_batch sb");
        $this->db->join('view_tc_supplychain_org vso', 'vso.SupplychainID = sb.SupplyOrgID', 'left');
        $this->db->join('view_tc_supplychain_org vso1', 'vso1.SupplychainID = sb.SupplyDestMillOrgID', 'left');
        $this->db->join('ktv_tc_supplychain_transaction st', 'st.SupplyBatchID = sb.SupplyBatchID', 'left');
        $this->db->where("sb.StatusCode","active");
        $this->db->where_in("sb.SupplyBatchStatus",$status);
        $this->db->group_by("sb.SupplyBatchID");

        if ($is_admin != 1) {
            $this->db->where("sb.SupplyOrgID",@$SupplychainID);
        }

        //========== Search (Begin) =====================
        if ($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',', $pSearch['ArrFilter']);
            for ($i = 0; $i < count($ArrTmp); $i++) {
                switch ($ArrTmp[$i]) {
                    case 'SupplyBatchNumber':
                        $this->db->like('sb.SupplyBatchNumber', $pSearch['TextFilterSupplyBatchNumber'], 'both');
                        break;
                    case 'SupplyBatchStatusID':
                        $this->db->where("sb.SupplyBatchStatusID",$pSearch['TextFilterSupplyBatchStatusID']);
                        break;
                    case 'StartSupplyBatchDate':
                        $this->db->where('DATE_FORMAT(sb.SupplyBatchDate, "%Y-%m-%d") >=',$pSearch['TextFilterStartSupplyBatchDate']);
                        break;
                    case 'EndSupplyBatchDate':
                        $this->db->where('DATE_FORMAT(sb.SupplyBatchDate, "%Y-%m-%d") <=',$pSearch['TextFilterEndSupplyBatchDate']);
                        break;
                }
            }
        }

        $this->db->group_by('sb.SupplyBatchID');
        $this->db->order_by($sortingField,$sortingDir);
        $this->db->limit($limit, $start);
        $query = $this->db->get();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        
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

    public function SupplychainBatchFormOpen($SupplyBatchID)
    {
        $return = array();

        $this->db->select("
            sb.SupplyBatchID,
            sb.SupplyBatchNumber,
            sb.SupplyBatchStatus AS SupplyBatchStatusID,
            DATE_FORMAT(sb.DateCreated, '%Y-%m-%d') AS SupplyBatchDate,
            sb.ExternalCode AS ExternalBatchCode
            ", FALSE);
        $this->db->from("ktv_tc_supplychain_batch sb");
        $this->db->where("sb.StatusCode","active");
        $this->db->where("sb.SupplyBatchID",$SupplyBatchID);
        
        $query = $this->db->get();
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function GetSupplychainBatchTransaction($SupplyBatchID) 
    {
        $this->db->select('a.`SupplyTransID` AS TransSupplyID,
                           a.`SupplyBatchID`,
                           a.`SupplyType` AS SupplyType,
                           a.`TransNumber` AS SupplyTransNumber,
                           a.`DateTransaction`,
                           a.`VolumeNetto` AS NettWeight,
                           a.`VolumeBruto` AS GrossWeight,
                           IF(
                            b.MemberName IS NULL OR b.MemberName = "",
                            IF(
                                m2.MillName IS NULL OR m2.MillName = "",
                                IF(
                                    a.MillOther IS NULL OR a.MillOther = "",
                                    IF(
                                        mem.Name IS NULL OR mem.Name = "",
                                        IF(
                                           kms.agCompanyName IS NULL OR kms.agCompanyName = "",
                                            IF(
                                                a.DOOther IS NULL OR a.DOOther = "",
                                                IF(
                                                    a.AgentOther IS NULL OR a.AgentOther = "",
                                                    "Nonfarmer",
                                                    a.AgentOther
                                                ),
                                                a.DOOther
                                            ),
                                            kms.agCompanyName
                                        ),
                                        mem.Name
                                    ),
                                    a.MillOther
                                ),
                                m2.MillName
                            ),
                            b.MemberName
                        ) AS MemberName,
                        d.`SupplyBatchNumber`', false);
        $this->db->from("ktv_tc_supplychain_transaction a");
        $this->db->join('ktv_members b',"a.SupplyID=b.MemberID", 'left');
        $this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyBatchID", 'left');
        $this->db->join('view_tc_supplychain_org e', 'e.SupplychainID=d.SupplyOrgID', 'left');
        $this->db->join('ktv_mill m2', 'm2.MillID=a.MillID', 'left');
        $this->db->join('view_tc_supplychain_org mem', 'mem.SupplychainID = a.MillID', 'left');
        $this->db->join('view_tc_supplychain_org vso3', "vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'", 'left');
        $this->db->join('ktv_members_extension kms', 'kms.MemberID = vso3.ObjID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where("d.SupplyBatchID", $SupplyBatchID);
        $this->db->order_by("a.SupplyBatchID","DESC");     

        $query = $this->db->get();

        $data = $query->result_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function GetGridTransactionMain($pSearch, $start, $limit, $sortingField, $sortingDir) 
    {
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        $is_admin = $_SESSION['is_admin'];
        
        $this->db->select(' a.`SupplyTransID` AS TransSupplyID,
                            a.`SupplychainID`,
                            a.`SupplyBatchID`,
                            a.`TransNumber`,
                            a.`InvoiceNumber`,
                            a.`DateTransaction`,
                            a.`SupplyType` AS TransTypeName,
                            CASE
                            WHEN a.SupplyType = "Farmer" THEN "Dealer"
                            WHEN a.SupplyType = "Nonfarmer" THEN "Own State"
                            WHEN a.SupplyType = "Batch" THEN "Own State"
                            ELSE "-"
                            END SupplyType,
                            IFNULL(e.ObjID, a.`SupplyID`) SupplyID,
                            a.`VolumeBruto` AS GrossWeight,
                            a.`VolumeNetto` AS NettWeight,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`,
                            IF(b.isCertified != "", cp.CertProgName,"Not Certified") Certified,
                            IF(
                                b.MemberName IS NULL OR b.MemberName = "",
                                IF(
                                    m2.MillName IS NULL OR m2.MillName = "",
                                    IF(
                                        a.MillOther IS NULL OR a.MillOther = "",
                                        IF(
                                            mem.Name IS NULL OR mem.Name = "",
                                            IF(
                                               kms.agCompanyName IS NULL OR kms.agCompanyName = "",
                                                IF(
                                                    a.DOOther IS NULL OR a.DOOther = "",
                                                    IF(
                                                        a.AgentOther IS NULL OR a.AgentOther = "",
                                                        "Nonfarmer",
                                                        a.AgentOther
                                                    ),
                                                    a.DOOther
                                                ),
                                                kms.agCompanyName
                                            ),
                                            mem.Name
                                        ),
                                        a.MillOther
                                    ),
                                    m2.MillName
                                ),
                                b.MemberName
                            ) AS MemberName,
                        ', false);
        $this->db->join('ktv_members b',"a.SupplyID=b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'", 'left');
        $this->db->join('ktv_ref_certification_program cp', 'cp.CertProgID = b.isCertified', 'left');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		$this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyID", 'left');
		$this->db->join('view_tc_supplychain_org e', 'e.SupplychainID=d.SupplyOrgID', 'left');
        $this->db->join('ktv_mill m2', 'm2.MillID=a.MillID', 'left');
		$this->db->join('view_tc_supplychain_org mem', 'mem.SupplychainID = a.MillID', 'left');
        $this->db->join('view_tc_supplychain_org vso3', "vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'", 'left');
        $this->db->join('ktv_members_extension kms', 'kms.MemberID = vso3.ObjID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplyBatchID IS NULL', NULL, TRUE);
        $this->db->where('a.isDelivery IS NULL', NULL, TRUE);
        $this->db->where('a.SupplyType !=', "Delivery");                     
      
        $this->db->order_by('a.SupplyTransID', 'DESC');
     
		if($this->input->get('SBID')!=''){
			$this->db->where('a.SupplyBatchID', $this->input->get('SBID'));
		} 
       
        if(!empty($pSearch['MemberName'])) {
            $this->db->like('b.MemberName', $pSearch['MemberName'], 'both');
        } 

        if(!empty($pSearch['StartTransactionDate'])) {
            $this->db->where('DATE_FORMAT(a.DateTransaction, "%Y-%m-%d") >=',$pSearch['StartTransactionDate']);
        }

        if(!empty($pSearch['EndTransactionDate'])) {
            $this->db->where('DATE_FORMAT(a.DateTransaction, "%Y-%m-%d") <=',$pSearch['EndTransactionDate']);
        }

        if ($is_admin != 1) {
            $this->db->where("a.SupplychainID", @$SupplychainID);
        }

        $query = $this->db->get('ktv_tc_supplychain_transaction a')->result_array();

        $result['data'] = $query;

        return $result;
    }

    public function InsertBatchTransaction($paramPost)
    {
    	$this->db->trans_begin();
        $results = array();
        $TransSupplyID = json_decode($paramPost['TransDetailID']);
        
        if(!empty($TransSupplyID)) {
            $this->db->trans_begin();
            
            // update or insert tb : ktv_tc_supplychain_batch
            $SupplychainBatch = $this->InsertSupplychainBatch($paramPost);
            
            $SupplyBatchID = $SupplychainBatch['SupplyBatchID'];
          
            $supplychain_batch = $this->CheckSupplyBatchStatusID($SupplyBatchID);
            
            if($supplychain_batch['SupplyBatchStatus'] == 'Open') {
                
                //insert into tb : ktv_tc_supplychain_batch_transaction
                foreach ($TransSupplyID as $key => $value) {
                    
                    $SupplyTransID                              = $value->TransSupplyID;
                    $dataBatchTransaction['SupplyBatchID']      = $SupplyBatchID;
                    $dataBatchTransaction['SupplyID']           = $value->SupplyID;
                    $dataBatchTransaction['TransNumber']        = $value->TransNumber;
                    $dataBatchTransaction['SupplyType']         = $value->TransTypeName;
                    $dataBatchTransaction['DateTransaction']    = $value->DateTransaction;
                    $dataBatchTransaction['VolumeBruto']        = $value->GrossWeight;
                    $dataBatchTransaction['VolumeNetto']        = $value->NettWeight;
                    $dataBatchTransaction['StatusCode']         = 'active';
                    $dataBatchTransaction['CreatedBy']          = $_SESSION['userid'];
                    $dataBatchTransaction['DateCreated']        = date('Y-m-d H:i:s');
                    $dataBatchTransaction['isDelivery']         = 1;

                    $this->db->where('SupplyTransID', $SupplyTransID);
                    $this->db->update('ktv_tc_supplychain_transaction', $dataBatchTransaction);
                }

                //update total weight and package table tb : ktv_tc_supplychain_batch
                $this->calculateWeightBatch($SupplyBatchID);

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = lang("Failed to save data");
                } else {
                    $this->db->trans_commit();
                    $results['success']       = true;
                    $results['message']       = lang("Data saved");
                    $results['SupplyBatchID'] = $SupplyBatchID;
                }

            } else {
                $results['success']       = false;
                $results['message']       = lang("Failed to save data because processing status is " . $supplychain_batch['SupplyBatchStatus']);
                $results['SupplyBatchID'] = $SupplyBatchID;
            }

        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }

        return $results;
    }

    public function InsertSupplychainBatch($paramPost) 
    {
        $results = array();
        
        $this->db->trans_begin();
       
        $uid = $this->getUID();

        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        if (!empty($paramPost['SupplyBatchDate'])) {
            $SupplyBatchDate = date($paramPost['SupplyBatchDate']).' '.date('H:i:s');
        } else {
            $SupplyBatchDate = NULL;
        }
        
        $generateTransSupplyNumber = $this->_generateBatchNumber($_SESSION['SupplychainID'],$paramPost['SupplyBatchDate']);
        
        $dataBatch['SupplyOrgID']         = @$SupplychainID;
        $dataBatch['SupplyBatchNumber']   = $generateTransSupplyNumber;
        $dataBatch['SupplyBatchDate']     = $SupplyBatchDate;
        $dataBatch['ExternalCode']        = !empty($paramPost['ExternalBatchCode']) ? $paramPost['ExternalBatchCode'] : null;
    
        $dataBatch['DestWeight']         = !empty($paramPost['FinalWeight']) ? $paramPost['FinalWeight'] : null;

        if($paramPost['SupplyBatchStatus'] == 2) { 
            $dataBatch['RemainingWeight'] = !empty($paramPost['FinalWeight']) ? $paramPost['FinalWeight'] : null;
        }

        if($paramPost['SupplyBatchStatusID'] == 1 || $paramPost['SupplyBatchStatusID'] == 'Open'){
            $status = 'Open';
        }
        
        $SupplyBatchNumber          = $dataBatch['SupplyBatchNumber'];
        
        $TransSupplyID = json_decode($paramPost['TransDetailID']);

        foreach ($TransSupplyID as $key => $value){
            $sumRemainingWeight += $value->GrossWeight;
        }

        $dataBatch['RemainingWeight']     = $sumRemainingWeight;
        
        if(!empty($paramPost['SupplyBatchID'])){
            foreach ($TransSupplyID as $key => $value){
                
                $SupplyBatchID                    = $paramPost['SupplyBatchID'];
               
                $dataBatch['SupplyDestOrgID']     = $SupplychainID;

                $dataBatch['DestWeight']          = $value->GrossWeight;
                
                $dataBatch['StatusCode']          = 'active';
                $dataBatch['SupplyBatchStatus']   = $status;
                $dataBatch['SupplyBatchDate']     = $SupplyBatchDate;
                $dataBatch['CreatedBy']           = $_SESSION['userid'];
                $dataBatch['DateUpdated']         = date('Y-m-d H:i:s');
                $dataBatch['SupplyBatchNumber']   = $SupplyBatchNumber; 
                $dataBatch['uid']                 = $uid;
                
                $this->db->where('SupplyBatchID', $SupplyBatchID);
                $query = $this->db->update('ktv_tc_supplychain_batch', $dataBatch);
                
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = lang("Failed to save data");
                } else {
                    $this->db->trans_commit();
                    $results['success']             = true;
                    $results['message']             = lang("Data saved");
                    $results['SupplyBatchID']       = $SupplyBatchID;
                    $results['SupplyBatchStatusID'] = $paramPost['SupplyBatchStatusID'];
                }
            }
        } else {

            $dataBatch['RemainingWeight']     = $sumRemainingWeight;
            
            foreach ($TransSupplyID as $key => $value){
                $SupplyBatchID                    = $paramPost['SupplyBatchID'];;
                
                $dataBatch['SupplyDestOrgID']     = $SupplychainID;
                
                $dataBatch['DestWeight']          = $value->GrossWeight;

                $dataBatch['SupplyBatchStatus']   = $status;
                $dataBatch['StatusCode']          = 'active';
                $dataBatch['SupplyBatchDate']     = $SupplyBatchDate;
                $dataBatch['CreatedBy']           = $_SESSION['userid'];
                $dataBatch['DateCreated']         = date('Y-m-d H:i:s');
                $dataBatch['SupplyBatchNumber']   = $SupplyBatchNumber;
                $dataBatch['uid']                 = $uid;
                
                $query = $this->db->insert('ktv_tc_supplychain_batch', $dataBatch);
            
                $SupplyBatchID = $this->db->insert_id();

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = lang("Failed to save data");
                } else {
                    $this->db->trans_commit();
                    $results['success']             = true;
                    $results['message']             = lang("Data saved");
                    $results['SupplyBatchID']       = $SupplyBatchID;
                    $results['SupplyBatchStatusID'] = $paramPost['SupplyBatchStatusID'];
                }
            }
        }

        return $results;
    }

    public function InsertProcessStartDateEndDate($SupplyBatchID) 
    {
        
        $dataBatch['ProcessStartDate'] = $data['ProcessStartDate'];
        $dataBatch['ProcessEndDate']   = $data['ProcessEndDate'];

        $this->db->where('SupplyBatchID', $SupplyBatchID);
        $this->db->update('ktv_tc_supplychain_batch', $dataBatch);
    }

    public function DeleteSupplychainBatchTransaction($paramDelete)
    {
        $results = array();
        $this->db->trans_begin();
        
        $dataUpdate = array(
                    "SupplyBatchID"  => NULL,
                    "isDelivery"     => NULL
        );
        
        $this->db->where('SupplyTransID', $paramDelete['TransSupplyID']);
        $query = $this->db->update('ktv_tc_supplychain_transaction', $dataUpdate);

        $this->calculateWeightBatch($paramDelete['SupplyBatchID']);
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
            $results['status']  = $status;
        }

        return $results;
    }

    public function UpdateSupplychainBatchClose($paramPost)
    {
        $SupplyBatchID = $paramPost['SupplyBatchID'];
        
        //Check SupplyBatchStatusID harus Open
        $supplychain_batch = $this->CheckSupplyBatchStatusID($SupplyBatchID);
    
        $SupplyBatchNumber = $supplychain_batch['SupplyBatchNumber'];
       
        if($supplychain_batch['SupplyBatchStatus'] == 'Open') {
            $this->db->trans_begin();

            $dataStatus['SupplyBatchStatus']   = 'Closed';
            $dataStatus['LastModifiedBy']      = $_SESSION['userid'];
            $dataStatus['DateUpdated']         = date('Y-m-d H:i:s');
            
            $this->db->where('SupplyBatchNumber', $SupplyBatchNumber);
            $this->db->update('ktv_tc_supplychain_batch', $dataStatus);
    
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = lang("Failed to update data");
            } else {
                $this->db->trans_commit();
                $results['success']       = true;
                $results['message']       = lang("Data updated");
                $results['SupplyBatchID'] = $SupplyBatchID;
            }

        } else {
            $results['success']       = false;
            $results['message']       = lang("Failed to save data because processing status is " . $supplychain_batch['Status']);
            $results['SupplyBatchID'] = $SupplyBatchID;
        }

        return $results;
    }
    
    public function DeleteSupplychainBatch($SupplyBatchID) 
    {
        $results = array();
        $this->db->trans_begin();
        
        $dataUpdate = array(
                    "StatusCode"     => "nullified",
                    "DateUpdated"    => date("Y-m-d H:i:s"),
                    "LastModifiedBy" => $_SESSION['userid']
                );

        $this->db->where('SupplyBatchID', $SupplyBatchID);
        $query = $this->db->update('ktv_tc_supplychain_batch', $dataUpdate);

        //remove SupplyBatchID on tb : ktv_tc_neo_supplychain_transaction_detail
        $dataUpdateTransaction['SupplyBatchID'] = null;
        $dataUpdateTransaction['IsProcess']     = 0;

        $this->db->where('SupplyBatchID', $SupplyBatchID);
        $this->db->update('ktv_tc_supplychain_transaction_detail', $dataUpdateTransaction);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        }

        return $results;
    }

    public function DeleteSupplychainBatchAPI($data) 
    {
        $return = array();

        $dataUpdate = array(
            "StatusCode"     => "nullified",
            "DateUpdated"    => date("Y-m-d H:i:s"),
            "SupplyBatchID" => $data['SupplyBatchID']
        );

        if($dataUpdate){        
           
            if($dataUpdate['SupplyBatchID'] !='' && $dataUpdate['SupplyBatchID']!='0'){

                $check = $this->db->query("SELECT SupplyBatchID FROM ktv_tc_supplychain_batch WHERE SupplyBatchID=?", array($dataUpdate['SupplyBatchID']));
                
                if($check->num_rows() > 0) {
                   
                    //update tb : ktv_tc_supplychain_delivery
                    $this->db->where('SupplyBatchID', $dataUpdate['SupplyBatchID']);
                    $query = $this->db->update('ktv_tc_supplychain_batch', $dataUpdate);
                    $SupplyBatchID = $dataUpdate['SupplyBatchID'];
                    
                    $return['success'] = true;
                    $return['message'] = lang("Data deleted");
                } 
            } else {
                $return['success'] = false;
            }
        }

        return $return;
    }

    public function CheckSupplyBatchStatusID($SupplyBatchID)
    {   
        $sql = "SELECT 
                    sb.SupplyBatchID,
                    sb.SupplyBatchStatus,
                    sb.SupplyBatchNumber
                FROM 
                    ktv_tc_supplychain_batch sb
                WHERE 
                    sb.StatusCode = 'active' 
                AND sb.SupplyBatchID = {$SupplyBatchID}";

        $SupplyBatchStatusID = $this->db->query($sql)->row_array();
        
        return $SupplyBatchStatusID;
    }

    public function calculateWeight($SupplyTransID)
    {
        $sql = "SELECT 
                    SUM(GrossWeight) AS GrossWeightTrans,
                    SUM(NettWeight) AS NettWeightTrans,
                    SUM(PackageTotal) AS PackageTotalTrans
                FROM 
                    ktv_tc_neo_supplychain_transaction_detail std 
                WHERE 
                    std.StatusCode='active' 
                AND std.SupplyTransID = {$SupplyTransID}";

        $transaction_detail = $this->db->query($sql)->row_array();

        return $transaction_detail;
    }

    public function getRoaster(){
        $query =  $this->db->from('ktv_supplier as s')
                ->join('ktv_sme_type as st','st.SMEID=s.SupplierID','left')
                ->select('s.SupplierID AS id, s.SupplierName AS label')
                ->where('st.SMETypeID','1')
                ->get();
       
        return $query->result_array();
        
    }

    public function getTransactionDetail($SeaweedTypeID, $SupplyTransID) {
        $this->db->select('SupplyTransID
                          ,SUM(GrossWeight) GrossWeightTotal
                          ,SUM(NettWeight) NettWeightTotal
                          ,SUM(PackageTotal) PackageTotal', FALSE);
        $this->db->where('SeaweedTypeID', $SeaweedTypeID);
        $this->db->where('SupplyTransID', $SupplyTransID);
        $query = $this->db->get('ktv_tc_neo_supplychain_transaction_detail');

        return $query;
    }   

    function _generateBatchNumber($SupplyOrgID, $SupplyBatchDate){
        $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_batch WHERE SupplyOrgID=? AND SupplyBatchDate LIKE ?";
        $query = $this->db->query($sql, array($SupplyOrgID, "%$SupplyBatchDate%"));
        
        $total = intval(@$query->row()->total)+1;

        $batch_number = 'XB'.sprintf("%04d", $SupplyOrgID).date('Ymd', strtotime($SupplyBatchDate)).sprintf("%06d", $total);
        return $batch_number;
    }

    public function calculateWeightBatch($SupplyBatchID)
    {
        $reload_page = 0;

        $sql = "SELECT 
                    VolumeBruto AS GrossWeight
                FROM 
                    ktv_tc_supplychain_transaction st
                WHERE 
                    st.SupplyBatchID = {$SupplyBatchID} AND st.StatusCode = 'active'";

        $getTransactionWeight = $this->db->query($sql)->row_array();

        $dataUpdateBatch['DestWeight']      = !empty($getTransactionWeight['GrossWeight']) ? $getTransactionWeight['GrossWeight'] : 0;
        $dataUpdateBatch['RemainingWeight'] = !empty($getTransactionWeight['GrossWeight']) ? $getTransactionWeight['GrossWeight'] : 0;
        
        $this->db->where('SupplyBatchID', $SupplyBatchID);
        $update = $this->db->update('ktv_tc_supplychain_batch', $dataUpdateBatch);

        if ($update) {
            $reload_page = 1;
        }

        return $reload_page;
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
