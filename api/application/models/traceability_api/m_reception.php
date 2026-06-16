<?php

class M_reception extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir) 
    {
        if ($sortingField == "")
            $sortingField = 'DeliveryID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }
        
        $is_admin = $_SESSION['is_admin'];
        
        $getDeliveryProcessing = $this->db->select('SupplyDestProcessType')
                                ->where('SupplyDestProcessType', 'mill')
                                ->where('SupplyDestOrgID', $SupplychainID)
                                ->get('ktv_tc_supplychain_delivery')
                                ->row();

        $proccess = (array) $getDeliveryProcessing;
        
        $this->db->select("SQL_CALC_FOUND_ROWS
                sd.DeliveryID
                ,sd.SupplychainID
                ,sd.SupplyDestOrgID
                ,sd.SupplyDestMillOrgID
                ,sd.DeliveryStatusID
                ,sd.SupplyDestProcessType
                ,ds.Status
                ,IF(sd.DeliveryStatusID=3,'Pending',ds.Status) DeliveryStatus
                ,sd.ExternalCode
                ,ktsdd.Weight 
                ,sd.PackageNumber
                ,sd.DestWeight
                ,sd.ArrivalEstimation
                ,sd.DestDriver
                ,sd.DeliveryDate AS DateReceipt
                ,vso2.Name AS AgentName
                ,vso1.Name AS DestinationName
                ,IFNULL((stn.DateCreated),'-')
                ,stn.TransNumber
                ,stn.PaymentStatusID
                ,CASE
                        WHEN stn.PaymentStatusID = 1 THEN 'Paid'
                        WHEN stn.PaymentStatusID = 2 THEN 'Waiting Payment'
                        WHEN stn.PaymentStatusID = 3 THEN 'Incomplete Payment'
                        WHEN stn.PaymentStatusID = 5 THEN 'To be confirm'
                        WHEN stn.PaymentStatusID = 99 THEN 'Failed'
                        ELSE 'Not yet paid'
                END AS PaymentStatus,
                ,stn.PaymentPaid as PaymentAmount
                ,stn.PaymentMethodID
                ,stn.uid
                ,ktstd.SupplyTransID
                ,ktsb.SupplyBatchNumber
                ,ktstd.TotalCapacity AS TotalWeight
                ,sd.DeliveryDate
                ,sd.DestTransportNumber
                ", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery sd");
        $this->db->join('ref_tc_delivery_status ds', 'ds.DeliveryStatusID = sd.DeliveryStatusID', 'left');
        $this->db->join('view_tc_supplychain_org vso', 'vso.SupplychainID = sd.SupplychainID', 'left');
        $this->db->join('view_tc_supplychain_org vso1', 'vso1.SupplychainID = sd.SupplyDestMillOrgID', 'left');
        $this->db->join('view_tc_supplychain_org vso2', 'vso2.SupplychainID = IFNULL(sd.SupplyDestDoOrgID,sd.SupplychainID)', 'left');
        $this->db->join('ktv_tc_supplychain_transaction stn', 'stn.SupplyID = sd.DeliveryID', 'left');
        $this->db->join("ktv_tc_supplychain_delivery_detail ktsdd","ktsdd.DeliveryID= sd.DeliveryID",'left');
        $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
        $this->db->join("ktv_tc_supplychain_transaction_detail ktstd","ktstd.DeliveryDetailID = ktsdd.DeliveryID",'left');
        $this->db->where('sd.StatusCode', 'active');
        $this->db->where("sd.SupplyDestMillOrgID", $SupplychainID);
        $this->db->where("sd.SupplyDestProcessType", 'mill');
        $this->db->where_in('sd.DeliveryStatusID', [3, 4, 5]);
        // ========== Search (Begin) =====================
        if ($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',', $pSearch['ArrFilter']);
            for ($i = 0; $i < count($ArrTmp); $i++) {
                switch ($ArrTmp[$i]) {
                    case 'Keyword':
                        $this->db->like('sd.DeliveryNumber', $pSearch['Keyword'], 'both');
                        $this->db->or_like('sd.ExternalCode', $pSearch['Keyword'], 'both');
                        break;
                    case 'WarehouseID':
                        $this->db->where("vso1.SupplychainID",$pSearch['WarehouseID']);
                        break;
                    case 'CollectorID':
                        $this->db->where("vso.SupplychainID",$pSearch['CollectorID']);
                        break;
                    case 'StartShipmentDate':
                        $this->db->where('DATE_FORMAT(sd.DeliveryDate, "%Y-%m-%d") >=',$pSearch['TextFilterStartDeliveryDate']);
                        break;
                    case 'EndShipmentDate':
                        $this->db->where('DATE_FORMAT(sd.DeliveryDate, "%Y-%m-%d") <=',$pSearch['TextFilterEndDeliveryDate']);
                        break;
                }
            }
        }

        $this->db->group_by("sd.DeliveryID");
        $this->db->order_by($sortingField,$sortingDir);
        $this->db->limit($limit, $start);
        $query = $this->db->get();
        // echo $this->db->last_query();die;

        $finalQuery = [];
        foreach ($query->result_array() as $key => $value) {
            $value['SupplychainIDSelf'] = @$SupplychainID;

            array_push($finalQuery, $value);
        }

        $result['data'] = $finalQuery;

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

    function readComboPackage(){
        $query = [];

        if ($_SESSION['is_admin'] == "1") {
            $getSettings = 1;
        } else {
            $getSettings = getSettings($_SESSION['ObjID']);
        }

        if (!empty($getSettings)) {
            $this->db->select("PackageType id, RefPackageName label, PackageWeight weight", FALSE);

            if ($_SESSION['is_admin'] != "1") {
                $PackageID  = "";
                foreach ($getSettings['package'] as $key => $value) {
                        $PackageID  .= "$value->PackageID,";
                }

                $PackageID  = rtrim($PackageID, ",");

                $where = [
                    "ktv_tc_supplychain_package.PackageID IN ($PackageID)" => NULL

                ];

                $this->db->where($where);
            }

            $this->db->join('ref_tc_package', 'ktv_tc_supplychain_package.PackageType = ref_tc_package.RefPackageID');
            $this->db->where('ktv_tc_supplychain_package.StatusCode', 'active');
            $this->db->order_by('label');

            $query = $this->db->get('ktv_tc_supplychain_package')->result_array();
        }
        
        return $query;
    }

    public function getDataBatch($DeliveryID){
        $sql ="SELECT
                sd.DeliveryID,
                sd.DeliveryStatusID,
                sd.DeliveryNumber,
                sd.ExternalCode,
                sd.DeliveryDate,
                sd.ArrivalEstimation,
                sd.PackageNumber,
                sd.DestWeight,
                sd.DestDriver,
                sd.DestTransportNumber,
                sd.DestTransportID,
                sd.TotalWeight AS Weight,
                vso.Name AS Collector,
                rtt.DestTransportName,
                stn.TransNumber
            FROM
                ktv_tc_supplychain_delivery AS sd
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = sd.DeliveryID 
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = sd.SupplychainID
                LEFT JOIN ref_tc_transport rtt ON rtt.DestTransportID = sd.DestTransportID 
                LEFT JOIN ktv_tc_supplychain_transaction stn ON stn.SupplyID = sd.DeliveryID 
            WHERE
                sd.DeliveryID =?";
        $query = $this->db->query($sql,array($DeliveryID));

        return $query->row();
    }

    public function getDataTransaction($get){
    
        $this->db->select('sd.DeliveryID
                           ,SUBSTR(sd.DateCreated, 1, 10) AS DateReceipt
                           ,sd.DestWeight
                           ,stnd.Weight AS Weight
                           ,stn.SupplyTransID
                           ,stn.TransNumber
                           ,stn.PaymentStatusID
                           ,sd.DeliveryStatusID
                           ', FALSE);
        $this->db->join('ktv_tc_supplychain_transaction stn', 'stn.SupplyID = sd.DeliveryID', 'left');
        $this->db->join('ktv_tc_supplychain_transaction_detail stnd', 'stn.SupplyTransID = stnd.SupplyTransID', 'left');
        $this->db->where('sd.DeliveryID', $get['DeliveryID']);
        $query = $this->db->get('ktv_tc_supplychain_delivery sd')->result_array();

        // echo $this->db->last_query();die;
        
        return $query;
    }

    function getLastTransaction($SID,$Date){
        $sql= "SELECT count(*) total_trans
            from ktv_tc_supplychain_transaction
            where 
            SupplychainID=?
            AND date(DateTransaction)=?";
        $query = $this->db->query($sql,array($SID,$Date));
        if($query->num_rows>0){
            $last = $query->row()->total_trans;
        }else{
            $last = 0;
        }
        return $last;
    }

    public function submit_reception($post){
        $encrypt = date('YmdHis').rand(10,100);
        $last_trans = $this->getLastTransaction($post['SupplychainID'],SUBSTR($post['DateTransaction'],0,10))+1;
        $encrypt_trans = sprintf("%05s", $last_trans);

        if($post['FormReception-uid'] != ""){
            $uid = $post['FormReception-uid'];
        }else{
            $uid = generateRandomUID();
        }

        $this->db->trans_begin();

        $data = [
            'DateTransaction'       => SUBSTR($post['DateTransaction'],0,10).' '.$post['TimeTransaction'].':00',
            'Remark'                => $post['ChangeLog'],
            'IsProcess'             => @$post['FormReception-IsProcess']?@$post['FormReception-IsProcess']:0,
            'IsDelivery'            => @$post['FormReception-IsDelivery']?@$post['FormReception-IsDelivery']:0,
            'StatusCode'            => 'active',
            'uid'                   => $uid
        ];

        $this->db->where('TransSupplyID', $post['DeliveryID']);
        $this->db->where('TransSupplyTypeID', '2');
        $checkTransaction = $this->db->get('ktv_tc_supplychain_transaction');

        if ($checkTransaction->num_rows() > 0) {
            $data['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
            $data['DateUpdated'] = date('Y-m-d H:i:s');

            $this->db->where('TransSupplyID', $post['DeliveryID']);
            $query = $this->db->update('ktv_tc_supplychain_transaction', $data);
            
        } 

        $checkPayment = $this->db->query("SELECT SupplyTransID FROM ktv_tc_supplychain_transaction_payment_detail WHERE SupplyTransID=?", array($post['SupplyTransID']));

        if ($checkPayment->num_rows() == 0) {
            $payment = array(
                'SupplyTransID'     => $post['SupplyTransID'],
                'Price'             => (float) str_replace(",", "", $post['FAQTotalPayment']),
                'TotalPayment'      => (float) str_replace(",", "", $post['FAQTotalPayment']),
                'InvoiceNumber'     => "0".$post['SupplychainID'].date('YmdH').$encrypt_trans,
                'CreatedBy'         => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1,
                'DateCreated'       => date('Y-m-d H:i:s'),
                'LastModifiedBy'    => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1,
                'DateUpdated'       => date('Y-m-d H:i:s'),

            );

            $query = $this->db->insert('ktv_tc_supplychain_transaction_payment_detail', $payment);
        } else {
            $payment = [
                'LastModifiedBy' => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1,
                'DateUpdated'    => date('Y-m-d H:i:s'),
                'TotalPayment'   => (float) str_replace(",", "", $post['FAQTotalPayment'])
            ];

            $this->db->where('SupplyTransID',$post['SupplyTransID']);
            $query = $this->db->update('ktv_tc_supplychain_transaction_payment_detail', $payment);
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        }

        if ($query) {
            $DeliveryID = @$post['DeliveryID'];
            $update = $this->db->where('DeliveryID', $post['DeliveryID'])
                               ->update('ktv_tc_supplychain_delivery', ['DeliveryStatusID' => $post['DeliveryStatusID']]);
            $results['success']       = true;
            $results['message']       = "Record Added";
            $results['DeliveryID']    = $post['DeliveryID'];
            $results['SupplyTransID'] = $post['SupplyTransID'];
            $results['DestinationID'] = $this->db->where('DeliveryID', $post['DeliveryID'])->get('ktv_tc_supplychain_delivery')->row()->DestinationID;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to add record";
        }

        $results['data'] = $post; 

        return $results;
    }

    function readComboWarehouse(){
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        $is_admin = $_SESSION['is_admin'];

        $sql = "SELECT
                    vso.SupplychainID id, vso.`Name` label
                FROM
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_mill kw ON kw.MillID=vso.ObjID
                WHERE
                    vso.ObjType = 'Mill' AND kw.StatusCode='active'";
        if ($is_admin != 1) {
            $sql .= " AND vso.SupplychainID = ?";
        }
            $sql .= " ORDER BY vso.Name ASC";
        
        $query = $this->db->query($sql, array($SupplychainID));
        
        return $query->result_array();
    }

    function printInvoice($SupplyTransID){
        $sql = "SELECT 
                    vso.`Name` BuyingUnit,
                    vso.PartnerID,
                    vso.Phone,
                    vso.Address,
                    rbt.SeaweedTypeName,
                    SUBSTR(st.DateTransaction, 1, 10) DateTransaction,
                    SUBSTR(st.DateTransaction, 12, 5) TimeTransaction,
                    st.SupplyTransNumber,
                    vso2.SupplychainID as SupplierCode,
                    st.GrossWeightTrans Bruto,
                    st.NettWeightTrans Netto,
                    std.PackageTotal,
                    
                    pay.InvoiceNumber,
                    pay.Price,
                    pay.NetPrice,
                    pay.TotalPayment,
                    vso2.`Name` BatchFrom,
                    sb2.ExternalCode,
                    sb2.PoliceVehicleNumber
                    
                    
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_transaction_detail std ON std.SupplyTransID=st.SupplyTransID
                    LEFT JOIN ktv_tc_supplychain_transaction_payment_detail pay ON pay.SupplyTransID=st.SupplyTransID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                    LEFT JOIN ref_tc_seaweed_type rbt ON rbt.SeaweedTypeID=std.SeaweedTypeID
                    LEFT JOIN ktv_tc_supplychain_delivery sb2 ON sb2.DeliveryID=st.TransSupplyID AND st.TransSupplyTypeID='2'
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb2.SupplychainID
                
                WHERE
                    st.SupplyTransID=?
                GROUP BY st.SupplyTransID";
        $query = $this->db->query($sql, array($SupplyTransID))->result_array();
        return $query[0];
    }

    function readComboRefUnitType(){
        $query = [];

        if ($_SESSION['is_admin'] == "1") {
            $getSettings = 1;
        } else {
            $getSettings = getSettings($_SESSION['ObjID']);
        }

        if (!empty($getSettings)) {
            $this->db->select("UnitID id, UnitName label", FALSE);

            if ($_SESSION['is_admin'] != "1") {
                $UnitID  = "";
                foreach ($getSettings['unit'] as $key => $value) {
                        $UnitID  .= "$value->UnitID,";
                }

                $UnitID  = rtrim($UnitID, ",");

                $where = [
                    "ref_tc_unit.UnitID IN ($UnitID)" => NULL

                ];

                $this->db->where($where);
            }

            $this->db->where('StatusCode', 'active');

            $query = $this->db->get('ref_tc_unit')->result_array();
        }
        
        return $query;
    }

    public function GetDeliveryReceiving($TransDetailID)
    {
        $where = [
            'TransDetailID' => $TransDetailID
        ];

        $this->db->where($where);
        $data = $this->db->get('ktv_tc_supplychain_transaction_detail')->row_array();
        
        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;

        return $return;
    }

    public function InsertDeliveryReceiving($paramPost)
    {
        $results = array();
        $this->db->trans_begin();
        
        if($paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-Weight'] > $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-TotalCapacity']) {
            $statusWeight = 'Less than total weight';
        } elseif($paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-Weight'] < $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-TotalCapacity']){
            $statusWeight = 'More than total weight';
        } else {
            $statusWeight = 'Same with total weight';
        }
       
        // hapus parameter yang tidak dibutuhkan
        unset($paramPost['OpsiDisplay']);
        unset($paramPost['apiUid']);
        
        $generateTransSupplyNumber = $this->generateTransSupplyNumber();
        if (empty($paramPost['SupplyTransID'])) {
            // insert dulu ke table transaksinya
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
            if($SupplychainID==''){
                @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE ObjID =?", array($_SESSION['ObjID']))->row()->SupplychainID; 
            }

            $getDeliveryData = $this->db->select('DeliveryID','SupplychainID, SupplyDestMillOrgID')
                                        ->where('DeliveryID', $paramPost['MemberID'])
                                        ->get('ktv_tc_supplychain_delivery')
                                        ->row();
            
            unset($paramPost['SupplyTransID']);
            $paramTransaction['SupplychainID']     = @$SupplychainID;
            $paramTransaction['StatusCode']        = 'active';
            $paramTransaction['CreatedBy']         =  $_SESSION['userid'];
            $paramTransaction['DateCreated']       = date('Y-m-d H:i:s');
            $paramTransaction['TransNumber']       = $generateTransSupplyNumber;
            $paramTransaction['SupplyType']        = 4;
            $paramTransaction['SupplyID']          = $getDeliveryData->DeliveryID;
            
            $query = $this->db->insert('ktv_tc_supplychain_transaction', $paramTransaction);
            $SupplyTransID = $this->db->insert_id();
        }
        
        //update delivery menjadi status delivered
        $updateDelivery = $this->db->where('DeliveryID', $paramPost['MemberID'])
                                   ->update('ktv_tc_supplychain_delivery', 
                                    ['DeliveryStatusID' => 4, 'DateUpdated' => date('Y-m-d H:i:s')]);

        $getDeliveryDetailData = $this->db->select('DeliveryID','Weight')
                                        ->where('DeliveryID', $paramPost['MemberID'])
                                        ->get('ktv_tc_supplychain_delivery_detail')
                                        ->row();
        
        $setTransactionDetail = [
           'SupplyTransID'      =>  !empty($paramPost['SupplyTransID']) ? $paramPost['SupplyTransID'] : $SupplyTransID,
           'DeliveryDetailID'   =>  $getDeliveryDetailData->DeliveryID,
           'DetailNumber'       =>  $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-DetailNumber'],
           'Weight'             =>  $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-Weight'],
           'TotalCapacity'      =>  $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-TotalCapacity'],
           'CreatedBy'          =>  $_SESSION['userid'],
           'DateCreated'        =>  date('Y-m-d H:i:s'),
           'statusWeight'       =>  $statusWeight,
        ];
        
        $query = $this->db->insert('ktv_tc_supplychain_transaction_detail', $setTransactionDetail);
        $TransDetailID = $this->db->insert_id();
        
        $SupplychainID = @$SupplychainID;
        
        if(!empty($TransDetailID))
        {
            $dataPost["SupplychainID"]      = $SupplychainID;
            $dataPost["ReceptionNumber"]    = time();
            $dataPost["ReceptionDate"]      = date("Y-m-d");
            $dataPost["Status"]             = "";
            $dataPost["ReceiptDeliveryID"]  = $getDeliveryDetailData->DeliveryID;
            $dataPost["StatusCode"]         = "active";
            $dataPost["CreatedBy"]          = $_SESSION['UserID'];
            $dataPost["DateCreated"]        = date("Y-m-d");
            $insert = $this->db->insert("ktv_tc_reception",$dataPost);

            $ReceptionID =  $this->db->insert_id();
            
            $sql = "SELECT 
                        a.ProductID,
                        a.ProductPercentage,
                        a.StatusCode,
                        b.DespatchID,
                        c.DespatchDetailID,
                        c.DespatchVolume
                    FROM 
                        ktv_tc_supplychain_product a 
                    LEFT JOIN 
                        ktv_tc_despatch b ON a.SupplychainID = b.SupplychainID
                    LEFT JOIN
                        ktv_tc_despatch_detail c ON c.DespatchID = b.DespatchID
                    WHERE 
                        a.SupplychainID = ?
                    AND
                        a.StatusCode = 'active'
                    GROUP BY 
                        a.ProductID";
            $query = $this->db->query($sql,array($SupplychainID));

            if($query->num_rows()>0){
                $dataPostDetail = array();
                foreach($query->result() as $key => $row){
                    
                    $dataPostDetail["ReceptionID"]        = $ReceptionID;
                    $dataPostDetail["DespatchDetailID"]   = $row->DespatchDetailID;
                    $dataPostDetail["ProductID"]          = $row->ProductID;
                    $dataPostDetail["ReceptionVolume"]    = $row->DespatchVolume;
                    $dataPostDetail["RemainingVolume"]    = $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-Weight'];
                    $dataPostDetail["StatusCode"]         = 'active';
                    $dataPostDetail["CreatedBy"]          = $_SESSION['userid'];
                    $dataPostDetail["DateCreated"]        = date("Y-m-d");
                }
                
                $insert = $this->db->insert("ktv_tc_reception_detail",$dataPostDetail);
            }
        }
                    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();

            $results = [
                'success'                   => true,
                'message'                   => lang("Data saved"),
                'SupplyTransID'             => $SupplyTransID
            ];
        }

        return $results;
    }

    public function UpdateDeliveryReceiving($paramPost)
    {
        $results = array();
        $this->db->trans_begin();

        $TransDetailID = $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-TransDetailID'];

        unset($paramPost['OpsiDisplay']);
        unset($paramPost['apiUid']);
        unset($paramPost['MemberID']);
        
        $updateTransactionDetail = [
           'SupplyTransID'  =>  $paramPost['SupplyTransID'],
           'DetailNumber'       =>  $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-DetailNumber'],
           'Weight'    =>  $paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-Weight'],
        ];
        
        $this->db->where('TransDetailID', $TransDetailID);
        $query = $this->db->update('ktv_tc_supplychain_transaction_detail', $updateTransactionDetail);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success']       = true;
            $results['message']       = lang("Data saved");
            $results['TransDetailID'] = $TransDetailID;
            $results['SupplyTransID'] = $paramPost['SupplyTransID'];
        }

        return $results;
    }

    public function GetDeliveryReceivingMainGrid($SupplyTransID) 
    {
        $return = array();
        
        $where = [
            'd.SupplyTransID' => $SupplyTransID
        ];

        $this->db->select(" d.TransDetailID,
                            d.SupplyTransID,
                            d.DeliveryDetailID,
                            d.DetailNumber,
                            d.Weight,
                            d.TotalCapacity,
                            CASE
                                WHEN d.statusWeight = 'Less than total weight' THEN '" . lang('Less than total weight') . "'
                                WHEN d.statusWeight = 'More than total weight' THEN '" . lang('More than total weight') . "'
                                WHEN d.statusWeight = 'Same with total weight' THEN '" . lang('Same with total weight') . "'
                                ELSE '-'
                            END AS statusWeight,
                            stn.PaymentPaid,
                            stn.PaymentStatusID,
                            stn.PaymentMethodID,
                            stn.BankCode,
                            stn.BankName,
                            stn.AccountNumber,
                            stn.AccountName", FALSE);

        $this->db->join('ktv_tc_supplychain_transaction stn', 'stn.SupplyTransID = d.SupplyTransID', 'left');
        $this->db->where($where);
        $this->db->order_by('d.TransDetailID', 'ASC');

        $query = $this->db->get('ktv_tc_supplychain_transaction_detail d')->result_array();
        
        $return['success'] = true;
        $return['data']    = $query;

        return $return;
    }

    public function generateTransSupplyNumber() {
        $this->db->where('StatusCode', 'active');
        $query = $this->db->get('ktv_tc_supplychain_transaction')->result();
        $count = count($query) + 1;

        $generate = "AGR". date('Ymd') . "00" . $count;

        return $generate;
    }

    public function DeleteDeliveryReceiving($paramDelete) 
    {
        $results = array();
        $this->db->trans_begin();

        $this->db->where('TransDetailID', $paramDelete['TransDetailID']);
        $query = $this->db->delete('ktv_tc_supplychain_transaction_detail');

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success']       = true;
            $results['message']       = lang("Data deleted");
            $results['SupplyTransID'] = $paramDelete['SupplyTransID'];
        }

        return $results;
    }

    public function GetReception($SID) 
    {   
        // $this->db->select("
        //     ktsd.DeliveryID
        //     ,ktsd.DeliveryNumber
        //     ,ktsd.ExternalCode
        //     ,ktsd.DeliveryDate
        //     ,ktsd.PackageWeight
        //     ,ktsd.TotalWeight AS DestWeight
        //     ,ktsd.SupplyDestOrgID
        //     ,ktsd.SupplyDestMillOrgID
        //     ,IF(ktsd.SupplyDestMillOtherName IS NULL or ktsd.SupplyDestMillOtherName = '', '', ktsd.SupplyDestMillOtherName) AS SupplyDestMillOtherName 
        //     ,ktsd.SupplyDestMillOtherName
        //     ,ktsd.SupplyDestDoOrgID
        //     ,ktsd.SupplyDestType
        //     ,ktsd.SupplyDestProcessType
        //     ,ktsd.SupplyBatchCategory
        //     ,ktsd.Notes
        //     ,ktsd.ChangeLog
        //     ,ktsd.ChangeBy
        //     ,ktsd.AutoBatchNumber AS DeliveryAutoNumber
        //     ,ktsd.DestPo AS DestPO
        //     ,ktsd.PackageNumber AS DestNumberPackage
        //     ,ktsd.Weather
        //     ,ktsd.ReceivedDate AS ShippingDate
        //     ,CASE
        //     WHEN ktsd.DeliveryStatusID = 1 THEN 'Open'
        //     WHEN ktsd.DeliveryStatusID = 2 THEN 'Close'
        //     WHEN ktsd.DeliveryStatusID = 3 THEN 'Sent'
        //     WHEN ktsd.DeliveryStatusID = 4 THEN 'Delivered'
        //     WHEN ktsd.DeliveryStatusID = 5 THEN 'Finish'
        //     ELSE '-'
        //     END AS StatusDelivery
        //     ,ktsd.ArrivalEstimation
        //     ,ktsd.PackageNumber
        //     ,ktsd.DestDriver
        //     ,ktsd.DestTransportID
        //     ,ktsd.DestTransportNumber
        //     ,ktsd.CreatedBy
        //     ,ktsd.DateCreated
        //     ,ktsd.LastModifiedBy
        //     ,ktsd.DateUpdated
        //     ,ktsd.SMESPCodeID
        //     ,vso.Name Destination
        //     ,sum(ktsdd.Weight) AS VolumeBruto
        //     ,sum(ktsdd.Weight) AS VolumeNetto
        //     ,ktsdd.StatusCode AS StatusCode
        //     ,ktsd.FinalCapacity
        //     ,ktsd.PaymentDelivery
        //     ,ktsd.PalmTypeID
        //     ", FALSE);
        // $this->db->from("ktv_tc_supplychain_delivery ktsd");
        // $this->db->join("view_tc_supplychain_org vso", "vso.SupplychainID = ktsd.SupplyDestMillOrgID", 'left');
        // $this->db->join("ktv_tc_supplychain_delivery_detail ktsdd","ktsdd.DeliveryID= ktsd.DeliveryID",'left');
        // $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= ktsdd.SupplyBatchID",'left');
        // $this->db->where("ktsd.StatusCode","active");
        // $this->db->where("ktsd.DeliveryStatusID != 0", NULL, FALSE);
        // $this->db->where("ktsd.SupplyDestMillOrgID", @$SID);
        // $this->db->where("ktsd.DeliveryStatusID", '3');

        // $this->db->group_by("ktsd.DeliveryID");
        // $this->db->order_by("ktsd.DeliveryID DESC");
        
        // $query = $this->db->get();
        //echo "<pre>".$this->db->last_query();die;
        $sql = "SELECT
                    ktsd.DeliveryID,
                    ktsd.SupplychainID,
                    ktsd.DeliveryNumber,
                    ktsd.ExternalCode,
                    ktsd.DeliveryDate,
                    ktsd.PackageWeight,
                    ktsd.TotalWeight AS DestWeight,
                    ktsd.SupplyDestOrgID,
                    ktsd.SupplyDestMillOrgID,
                    IF( ktsd.SupplyDestMillOtherName IS NULL OR ktsd.SupplyDestMillOtherName = '', '', ktsd.SupplyDestMillOtherName ) AS SupplyDestMillOtherName,
                    ktsd.SupplyDestMillOtherName,
                    ktsd.SupplyDestDoOrgID,
                    ktsd.SupplyDestType,
                    ktsd.SupplyDestProcessType,
                    ktsd.SupplyBatchCategory,
                    ktsd.Notes,
                    ktsd.ChangeLog,
                    ktsd.ChangeBy,
                    ktsd.AutoBatchNumber AS DeliveryAutoNumber,
                    ktsd.DestPo AS DestPO,
                    ktsd.PackageNumber AS DestNumberPackage,
                    ktsd.Weather,
                    ktsd.ReceivedDate AS ShippingDate,
                    CASE
                        WHEN ktsd.DeliveryStatusID = 1 THEN
                        'Open' 
                        WHEN ktsd.DeliveryStatusID = 2 THEN
                        'Close' 
                        WHEN ktsd.DeliveryStatusID = 3 THEN
                        'Sent' 
                        WHEN ktsd.DeliveryStatusID = 4 THEN
                        'Delivered' 
                        WHEN ktsd.DeliveryStatusID = 5 THEN
                        'Finish' ELSE '-' 
                    END AS StatusDelivery,
                    ktsd.ArrivalEstimation,
                    ktsd.PackageNumber,
                    ktsd.DestDriver,
                    ktsd.DestTransportID,
                    ktsd.DestTransportNumber,
                    ktsd.CreatedBy,
                    ktsd.DateCreated,
                    ktsd.LastModifiedBy,
                    ktsd.DateUpdated,
                    ktsd.SMESPCodeID,
                    vso.NAME Destination,
                    sum( ktsdd.Weight ) AS VolumeBruto,
                    sum( ktsdd.Weight ) AS VolumeNetto,
                    ktsdd.StatusCode AS StatusCode,
                    ktsd.FinalCapacity,
                    ktsd.PaymentDelivery,
                    ktsd.PalmTypeID 
                FROM
                    `ktv_tc_supplychain_delivery` ktsd
                    LEFT JOIN `view_tc_supplychain_org` vso ON `vso`.`SupplychainID` = `ktsd`.`SupplyDestMillOrgID`
                    LEFT JOIN `ktv_tc_supplychain_delivery_detail` ktsdd ON `ktsdd`.`DeliveryID` = `ktsd`.`DeliveryID`
                    LEFT JOIN `ktv_tc_supplychain_batch` ktsb ON `ktsb`.`SupplyBatchID` = `ktsdd`.`SupplyBatchID`
                    LEFT JOIN ktv_tc_supplychain_transaction str ON str.SupplyID=ktsd.DeliveryID AND str.SupplyType='Delivery' AND str.StatusCode='active'
                WHERE
                    `ktsd`.`StatusCode` = 'active' 
                    AND str.SupplyTransID IS NULL
                    AND `ktsd`.`DeliveryStatusID` = '3' 
                    AND (`ktsd`.`SupplyDestMillOrgID` = ?
                        OR ktsd.SupplyDestDoOrgID= ? AND ktsd.SupplyDestMillOrgID IS NULL
                    )
                GROUP BY `ktsd`.`DeliveryID` 
                ORDER BY `ktsd`.`DeliveryID` DESC";
        $query = $this->db->query($sql, array($SID, $SID));
        $return = array();
        if($query->num_rows() > 0) {
            $result = $query->result_array();
            foreach ( $result as $k => $v) {
                $result[$k]['DeliveryDetail'] = $this->deliveryDetail($v['DeliveryID']);
            }   
        }
        return $result;
    }

    private function deliveryDetail($DeliveryID)
    {   
        $query = $this->db->select("ktsdd.DeliveryDetailID
                           ,ktsdd.DeliveryID
                           ,ktsdd.StatusCode AS Status  
                           ,ktsdd.SupplyBatchID
                           ,ktsdd.FFBWeight
                           ,ktsb.SupplyBatchNumber
                           ,vso.Name AS SupplyBatchName
                           ,ktsdd.BrondolanWeight
                           ,ktsdd.Weight AS Weight", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery_detail ktsdd");
        $this->db->join("ktv_tc_supplychain_batch ktsb", "ktsb.SupplyBatchID = ktsdd.SupplyBatchID", 'left');
        $this->db->join("view_tc_supplychain_org vso", "vso.SupplychainID = ktsb.SupplyOrgID", 'left');
        $this->db->where("ktsdd.DeliveryID",$DeliveryID);
        $this->db->where("ktsdd.StatusCode","active");
        $this->db->order_by("ktsdd.DeliveryID","DESC");

        $query = $this->db->get();
        // echo '<pre>'.$this->db->last_query();die;
        $result = $query->result_array();

        return $result;
    }

    private function delivery($SupplychainID)
    {   
        $this->db->select("ktsd.SupplyDestMillOrgID", FALSE);
        $this->db->from("ktv_tc_supplychain_delivery ktsd");
        $this->db->where("ktsd.StatusCode","active");
        $this->db->where("ktsd.SupplyDestMillOrgID", $SupplychainID);

        $query = $this->db->get();
        
        $result = $query->result_array();

        return $result;
    }

    public function submit_reception_api_post($data)
    {
        $return['ErrorCode'] = ''; 
        $return['status'] = false;
       
        $getTrans = $this->db->select('SupplyTransID','SupplychainID','TransNumber','SupplyType','SupplyType')
                    ->where('SupplyTransID', $data['SupplyTransID'])
                    ->get('ktv_tc_supplychain_transaction')
                    ->row();
        if($getTrans){
            if($getTrans->SupplyTransID!='' ){    
                $data['SupplyTransID'] = $getTrans->SupplyTransID;
                $trans = $this->tcUpdateTransaction($data);
                $return['status'] = true;
            }else{
                $return['status'] = false;
                $return['SupplyTransID'] = $getTrans->SupplyTransID;
                $return['ErrorCode'] = 2;
            }
            $balance = false;
        }else{
            $trans = $this->tcInsertTransaction($data);
            $return['status'] = true;
            $balance = false;
        }
        
        if($return['status']==true){
            $return['TransDetailID '] = $trans['id'];
        }

        if($return['ErrorCode'] != ''){
            $return['status'] = true;
        }else{
            unset($return['ErrorCode']);
        }

        return $return;
    }

    public function submit_accept_delivery_post($data)
    {
        $results = array();
    
        $getDeliveryDetailData = $this->db->select('DeliveryID','Weight')
                                ->where('DeliveryID', $data['DeliveryID'])
                                ->get('ktv_tc_supplychain_delivery_detail')
                                ->row();
        $setReceipt = [
            'DeliveryID'         =>  $getDeliveryDetailData->DeliveryID,
            'DeliveryStatusID'   =>  $data['Status'],
            'ReceivedDate'       =>  $data['ReceivedDate'],
            'TotalWeight'        =>  $data['ReceivedWeight'],
            'CreatedBy'          =>  $_SESSION['userid'],
            'DateCreated'        =>  date('Y-m-d H:i:s'),
            'DateUpdated'        =>  date('Y-m-d H:i:s'),
        ];
        
        if($data['DeliveryID'] != '') {
                            
            $this->db->where('DeliveryID', $data['DeliveryID']);
            $this->db->update('ktv_tc_supplychain_delivery', $setReceipt);
            
            $DeliveryID = $data['DeliveryID'];

            $return['status'] = true;
        } else {
            $return['status'] = false;
        }
    
        return $return;
    }

    private function tcInsertTransaction($data) {

        $generateTransSupplyNumber = $this->generateTransSupplyNumber();

        // insert dulu ke table transaksinya
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE ObjID =?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }
        
        $generateTransSupplyNumber = $this->generateTransSupplyNumber();
        if (empty($data['SupplyTransID'])) {
            // insert dulu ke table transaksinya
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
            if($SupplychainID==''){
                @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE ObjID =?", array($_SESSION['ObjID']))->row()->SupplychainID; 
            }
            if(!empty($data['trans_detail'])){
                foreach ($data['trans_detail'] as $key => $value) {
                    unset($data['SupplyTransID']);
                    $transaction['SupplychainID']     = @$SupplychainID;
                    $transaction['StatusCode']        = 'active';
                    $transaction['CreatedBy']         =  $_SESSION['userid'];
                    $transaction['DateCreated']       = date('Y-m-d H:i:s');
                    $transaction['TransNumber']       = $generateTransSupplyNumber;
                    $transaction['SupplyType']        = 4;
                    $transaction['SupplyID']          = $value['DeliveryDetailID'];
                    $query = $this->db->insert('ktv_tc_supplychain_transaction', $transaction);
                    $SupplyTransID = $this->db->insert_id();

                    //update delivery menjadi status delivered
                    $updateDelivery = $this->db->where('DeliveryID', $value['DeliveryDetailID'])
                                      ->update('ktv_tc_supplychain_delivery', ['DeliveryStatusID' => 4]);
        
                    $getDeliveryData = $this->db->select('DeliveryID','SupplychainID, SupplyDestMillOrgID')
                                        ->where('DeliveryID', $value['DeliveryDetailID'])
                                        ->get('ktv_tc_supplychain_delivery')
                                        ->row();
                }
            }           
        }

        if(count(@$data['trans_detail'])>0){
            foreach (@$data['trans_detail'] as $transDetail) {
    
                $cekTransDetail = $this->db->select('TransDetailID','SupplyTransID, DeliveryDetailID', 'DetailNumber', 'Weight')
                                    ->where('TransDetailID', $trans_detail['TransDetailID'])
                                    ->get('ktv_tc_supplychain_transaction_detail')
                                    ->row();

                if(!empty($cekTransDetail)){
                    $this->db->where('TransDetailID', $trans_detail['TransDetailID']);
                    $query = $this->db->update('ktv_tc_supplychain_transaction_detail', $transDetail);
                }else{
                    $query = $this->db->insert('ktv_tc_supplychain_transaction_detail', $transDetail);
                    $TransDetailID = $this->db->insert_id();
                }
            }
        }

        return array( 'id' => $TransDetailID);
    }

    private function tcUpdateTransaction($data) {
        
        if(substr($data['DateTransaction'], 11,8)==''){
            $DateTransaction = $data['DateUpdated'];
        }else{
            $DateTransaction = $data['DateTransaction'];
        }

        $id = $data['SupplyTransID'];
        
        if(!empty($data['trans_detail'])){
            foreach ($data['trans_detail'] as $key => $value) {
                unset($data['SupplyTransID']);
                $transaction['SupplychainID']     = @$SupplychainID;
                $transaction['StatusCode']        = 'active';
                $transaction['CreatedBy']         =  $_SESSION['userid'];
                $transaction['DateCreated']       = date('Y-m-d H:i:s');
                $transaction['TransNumber']       = $generateTransSupplyNumber;
                $transaction['SupplyType']        = 4;
                $transaction['SupplyID']          = $value['DeliveryDetailID'];

                $this->db->where('SupplyTransID', $id);
                $query = $this->db->update('ktv_tc_supplychain_transaction', $transaction);
               
                $getDeliveryData = $this->db->select('DeliveryID','SupplychainID, SupplyDestMillOrgID')
                ->where('DeliveryID', $value['DeliveryDetailID'])
                ->get('ktv_tc_supplychain_delivery')
                ->row();
            }
        }           
       
        unset($data['SupplyTransID']);

        if(count(@$data['trans_detail'])>0){
            foreach (@$data['trans_detail'] as $transDetail) {
    
                $cekTransDetail = $this->db->select('TransDetailID','SupplyTransID, DeliveryDetailID', 'DetailNumber', 'Weight')
                                    ->where('TransDetailID', $trans_detail['TransDetailID'])
                                    ->get('ktv_tc_supplychain_transaction_detail')
                                    ->row();

                if(!empty($cekTransDetail)){
                    $this->db->where('TransDetailID', $trans_detail['TransDetailID']);
                    $query = $this->db->update('ktv_tc_supplychain_transaction_detail', $transDetail);
                }else{
                    $query = $this->db->insert('ktv_tc_supplychain_transaction_detail', $transDetail);
                    $TransDetailID = $this->db->insert_id();
                }
            }
        }

        return array( 'id' => $id);
    }

    public function readDetailPayment($get){
        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                a.`SupplyTransID`,
                a.`SupplychainID`,
                a.`SupplyBatchID`,
                b.`SupplybaseType`,
                a.`TransNumber`,
                a.`InvoiceNumber`,
                a.`DateTransaction`,
                CASE
                        WHEN a.SupplybaseCategoryID = 1 THEN 'Farmer Plasma'
                        WHEN a.SupplybaseCategoryID = 2 THEN 'Direct Smallholder'
                        WHEN a.SupplybaseCategoryID = 3 THEN 'Agent / Dealer / Vendor'
                        WHEN a.SupplybaseCategoryID = 4 THEN 'Owner Estate'
                        WHEN a.SupplybaseCategoryID = 5 THEN 'External Estate'
                        ELSE '-'
                END AS SupplyType,
                a.`PlantationNr`,
                a.`PlantationNr` FarmNumber,
                a.`VolumeBruto`,
                a.`VolumeNetto`,
                a.`VolumeCutting`,
                a.`PackageID`,
                a.`Bunches`,
                a.`FFBCount` PackageNumber,
                a.`PackageWeight`,
                a.`DetailTypeID`,
                a.`TransStatusID`,
                a.`FarmingTypeID`,
                a.ContractPrice,
                a.NetPrice,
                a.DiscountPrice,
                a.TotalPayment,
                a.PaymentReduction,
                a.PaymentPaid,
                a.`Notes`,
                a.`ChangeLog`,
                a.`ChangeBy`,
                a.`DateCreated`,
                a.`CreatedBy`,
                a.`DateUpdated`,
                a.`LastModifiedBy`, 
                a.`PaymentStatusID`,
                pm.`PaymentMethodID`,
                pm.`PaymentMethod`,
                a.`BankCode`,
                a.`BankName`,
                a.`AccountNumber`,
                a.`AccountName`,
                a.`uid`,
                a.`CompanyCode`,
                a.`VirtualAccount`,
                a.`ServiceCharge`,
                a.`TotalPaymentWithServiceCharge`,
                vso3.`Name` as BuyingUnitName,
                kpp.`PartnerName` as PartnerName,   
                b.FarmerCategory,    
                b.Latitude,
                b.Longitude,
                CASE
                    WHEN b.Gender = 'm' THEN 'Male'
                    WHEN b.Gender = 'f' THEN 'Female'
                    ELSE '-'
                END Gender,
                b.`Address`,  
                b.BankBranchName,
                b.BankAccNumber,
                b.BankClientID,   
                kv.Village,          
                CONCAT('Country: ', kc.`CountryName`,', Province: ',kp.Province,', District: ',kd.District,', Sub District: ',IFNULL(sd.SubDistrict,'-'),', Village: ',IFNULL(kv.Village,'-')) AS Region,
                FLOOR(DATEDIFF(CURDATE(), b.DateOfBirth) / 365.25) AS Age,
                c.PackageType,
                IF(
                    b.MemberName IS NULL OR b.MemberName = '',
                    IF(
                        m2.MillName IS NULL OR m2.MillName = '',
                        IF(
                            a.MillOther IS NULL OR a.MillOther = '',
                            IF(
                                mem.Name IS NULL OR mem.Name = '',
                                IF(
                                    a.DOOther IS NULL OR a.DOOther = '',
                                    IF(
                                        a.AgentOther IS NULL OR a.AgentOther = '',
                                        'Nonfarmer',
                                        a.AgentOther
                                    ),
                                    a.DOOther
                                ),
                                mem.Name
                            ),
                            a.MillOther
                        ),
                        m2.MillName
                    ),
                    b.MemberName
                ) MemberName,
                e.Name,
                b.`MemberDisplayID`,
                IFNULL(
                        IF(
                            b.MemberID <> 0, b.MemberID, 
                            IF(
                                a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                IF(
                                    a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                    IF(
                                        a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                        IF(
                                            a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                            'Unregistered Supplier'
                                        )
                                    )
                                )
                            )
                        ), 'Unregistered Supplier'
                ) MemberID,
                IFNULL(
                    IF(
                        a.MillID IS NOT NULL OR a.MillID <> '', 'external',
                        IF(
                            a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other'
                        )
                    ), 'other'
                ) SellerType,
                e.ObjID,
                a.MillID,
                a.MillOther,
                IF(a.MillOther IS NULL OR a.MillOther = '', '',true) OtherMill,
                a.DOID,
                a.DOOther,
                IF(a.DOOther IS NULL OR a.DOOther = '', '',true) OtherDO,
                a.AgentID,
                a.AgentOther,
                IF(a.AgentOther IS NULL OR a.AgentOther = '', '',true) OtherAgent,
                a.AgentOtherNIK,
                a.AgentOtherSurvey,
                IF(b.isCertified != '', cp.CertProgName,'Not Certified') Certified,
                CASE
                    WHEN a.SupplyType = 'Farmer' THEN '1'
                    WHEN a.SupplyType = 'Nonfarmer' THEN '2'
                    WHEN a.SupplyType = 'Batch' THEN '3'
                    ELSE '-'
                END SalesType,
                IF( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
                CASE WHEN a.SupplyBatchID IS NULL THEN '-'
                ELSE
                IFNULL(vso2.`Name`, b.MemberName)
                END as BatchFrom
            FROM
                ktv_tc_supplychain_transaction a
            LEFT JOIN
                ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
            LEFT JOIN
                ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
            LEFT JOIN
                ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
            LEFT JOIN
                view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
            LEFT JOIN
                ktv_trace_package c on a.PackageID=c.PackageID
            LEFT JOIN
                ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
            LEFT JOIN
                view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
            LEFT JOIN
                ktv_mill m2 on m2.MillID = a.MillID
            LEFT JOIN
                view_tc_supplychain_org mem on mem.SupplychainID = a.DOID
            LEFT JOIN
                ktv_members mem2 on mem2.MemberID = a.AgentID
            LEFT JOIN 
                ktv_village kv ON b.VillageID = kv.VillageID
            LEFT JOIN 
                ktv_subdistrict sd ON kv.SubDistrictID = sd.SubDistrictID
            LEFT JOIN 
                ktv_district kd ON sd.DistrictID = kd.DistrictID
            LEFT JOIN 
                ktv_province kp ON kd.ProvinceID = kp.ProvinceID
            LEFT JOIN 
                ktv_country kc ON kp.CountryCode = kc.ISO2
            LEFT JOIN 
                ref_tc_payment_method pm ON pm.PaymentMethodID = a.PaymentMethodID
            LEFT JOIN
                view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplychainID
            LEFT JOIN
                ktv_program_partner kpp on kpp.PartnerID = vso3.PartnerID
            WHERE 1=1
            AND
                a.StatusCode = 'active'
            AND 
                a.SupplyTransID = ?
        ";

        $query = $this->db->query($sql, array($get['SupplyTransID']));

        if ($query->num_rows()>0) {
            return $query->row();
        }
        return false;
    }

    function replacestr($form)
    {
        return str_replace(',','',$form);
    }

    public function getPaymentInstruction($get){
        $language = $_SESSION['language'];

        $pay = $this->readDetailPayment($get);
        // var_dump($pay);die;
        
        if(intval($pay->PaymentMethodID)==1){
            $instruction = $this->readDetailPayment($pay->PaymentMethodID,$language);
            $PaymentIntrunction = $instruction->data;
        }else{
            $param = array(
                "PaymentMethodID"   => $pay->PaymentMethodID,
                "uid"               => $pay->uid,
                "LanguageID"        => $_SESSION['language']=='Indonesia'?2:1
            );

            $instruction = $this->APIChekPaymentStatus($param);

            $PaymentIntrunction = $instruction->data->PaymentDetail[0]->PaymentInstruction;
            if(empty($PaymentIntrunction)){
                $instruction = $this->ApiPaymentInstruction(1,$language);
                $PaymentIntrunction = $instruction->data;
            }
        }
        
        //return $instruction->data[0]->Content;
        //die;
        $data=array();
        if(!empty($PaymentIntrunction)){

            foreach ($PaymentIntrunction as $key) {
                $data[] = $key;
                if(strtolower($language)=='indonesia'){
                    $data[0]->Content = str_replace('Petani','Pedagang',$key->Content);
                    $data[0]->Content = str_replace('petani','pedagang',$key->Content);
                }else{
                    $data[0]->Content = str_replace('farmer','trader',$key->Content);
                    $data[0]->Content = str_replace('Farmer','Trader',$key->Content);
                }
                
                
            }
        }

        if ((int) $pay->PaymentMethodID == 2) {
            $paymentLogo                = base_url('images/Logo_BRI.png');
            $virtualAccountNameIdentity = "BRIVA";
        } elseif ((int) $pay->PaymentMethodID == 3) {
            $paymentLogo                = base_url('images/Logo_ATM_BERSAMA.png');
            $virtualAccountNameIdentity = "ATM BERSAMA";
        } else {
            $paymentLogo                = "";
            $virtualAccountNameIdentity = "";
        }

        // var_dump($pay);die;

        $return = array(
            'PaymentVia'           => lang("Payment via")." ".$pay->PaymentMethod,
            'PaymentViaLogo'       => $paymentLogo,
            'TransactionNumber'    => $pay->TransNumber,
            'PaymentMethodID'      => $pay->PaymentMethodID,
            'CompanyCode'          => $pay->CompanyCode,
            'TransactionCode'      => $pay->VirtualAccount,
            'VirtualAccount'       => $pay->CompanyCode?$pay->CompanyCode.''.$pay->VirtualAccount:$pay->VirtualAccount,
            'VirtualAccountName'   => $virtualAccountNameIdentity.' '.$pay->BuyingUnit,
            'TotalPayment'         => "IDR. ".number_format($pay->TotalPaymentWithServiceCharge,2),
            'TotalPaymentOri'      => $pay->TotalPaymentWithServiceCharge,
            'data'                 => $data
        );

        return $return;
    }

    public function UpdateTransaction($paramPost){
        $results = array();
        $this->db->trans_begin();

        $SupplyTransID = $paramPost['SupplyTransID'];
        
        $updateTransaction = [
           "PaymentPaid"        =>  $this->replacestr($paramPost['PaymentPaid']),
           'PaymentStatusID'    =>  $paramPost['PaymentStatusID'],
           'PaymentMethodID'    =>  $paramPost['PaymentMethodID'],
           'BankCode'           =>  $paramPost['BankCode'],
           'BankName'           =>  $paramPost['BankName'],
           'AccountNumber'      =>  $paramPost['AccountNumber'],
           'AccountName'        =>  $paramPost['AccountName'],
           "uid"                => base64_encode(date('His')).$paramPost['TransNumber'].$_SESSION['SupplychainID'].date('YmdHis'),
        ];
        
        $this->db->where('SupplyTransID', $SupplyTransID);
        $query = $this->db->update('ktv_tc_supplychain_transaction', $updateTransaction);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success']       = true;
            $results['message']       = lang("Data saved");
            $results['SupplyTransID'] = $paramPost['SupplyTransID'];
        }

        return $results;
    }

    private function apiSubmitPayment($data){

        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/submit-payment',
            'method'      => 'POST',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(["data" => json_encode($data)]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();
      
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/submit-payment',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "data": '.json_encode($data).'
        }
        ',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        if ($checkError > 0) {
            $response = $checkErrorMsg;
        }

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    private function ApiPaymentInstruction($PaymentMethodID,$Language){
        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'token: '.$token
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/payment-intruction?PaymentMethodID='.$PaymentMethodID.'&Language='.$Language,
            'method'      => 'GET',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(['PaymentMethodID' => $PaymentMethodID, 'Language' => $Language]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/payment-intruction?PaymentMethodID='.$PaymentMethodID.'&Language='.$Language,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => array('PaymentMethodID' => $PaymentMethodID,'Languange' => $Language),
        CURLOPT_HTTPHEADER => array(
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'token: '.$token
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        if ($checkError > 0) {
            $response = $checkErrorMsg;
        }

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    private function APIChekPaymentStatus($param){
        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/check-payment-status',
            'method'      => 'POST',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(["data" => json_encode($param)]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/check-payment-status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "data":'.json_encode($param).'
        }',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        if ($checkError > 0) {
            $response = $checkErrorMsg;
        }

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    private function _postServiceCharge($PaymentMethodID,$totalPaid){
        
        $getAPISettings = dynamicSettingAPIPayment(base_url());

        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/check-service-charge',
            'method'      => 'POST',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(["data" => json_encode(['PaymentMethodID' => $PaymentMethodID, 'TotalPaid' => $totalPaid])]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/check-service-charge',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{   
            "data": {   
                "PaymentMethodID":'.$PaymentMethodID.',
                "TotalPaid": '.$totalPaid.'    
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    public function SubmitPayment($paramPost){ 
        $update = $this->UpdateTransaction($paramPost);

        $PaymentPaid = filter_var($paramPost['PaymentPaid'], FILTER_SANITIZE_NUMBER_INT);
        $recpay  = $this->readDetailPayment($paramPost);
        
        $service = $this->_postServiceCharge($paramPost['PaymentMethodID'],$PaymentPaid)->data;
        
        $data = array(
                "uid"               => $recpay->uid,
                "SupplyTransID"     => $recpay->SupplyTransID,
                "PartnerName"       => $recpay->PartnerName,
                "BuyingUnitName"    => $recpay->BuyingUnitName,
                "TransactionDate"   => $recpay->DateTransaction, 
                "TransactionNumber" => $recpay->TransNumber,
                
                "SupplierID"        => $recpay->MemberDisplayID,
                "SupplierName"      => $recpay->MemberName,
                "SupplierType"      => $recpay->SupplybaseType,
                "BankCode"          => $recpay->BankCode,
                "BankName"          => $recpay->BankName,
                "AccountNumber"     => $recpay->AccountNumber,
                "AccountName"       => $recpay->AccountName,
                "AccountEmoney"     => "",
                "AccountUsername"   => "",

                "PaymentMethodID"   => $recpay->PaymentMethodID?$recpay->PaymentMethodID:2,
                "TotalPaid"         => $PaymentPaid,
                "Notes"             => "",
                "FarmerSignature"   => "",
                "FCMToken"          => "0",
                "LanguageID"        => 2,
                "PaymentDetail"     => [
                    array(
                        "uid"                         => $recpay->SupplierID.$recpay->uid,
                        "PaymentMethodID"             => $recpay->PaymentMethodID?$recpay->PaymentMethodID:2, 
                        "ServiceChargeType"           => $service->ServiceChargeType,
                        "ServiceCharge"               => $service->ServiceCharge,
                        "TotalServiceCharge"          => $service->TotalServiceCharge,
                        "TotalPaid"                   => $service->TotalPaid,
                        "TotalPaidWithServiceCharge"  => $service->TotalPaidWithServiceCharge,
                        "EmoneyToken"                 => "",
                        "PIN"                         => "",
                        "DetailNotes"                 => ""
                    )
                ]
        );

        // echo json_encode($data);
        // die;
    
        $payment = $this->apiSubmitPayment($data);

        $results = array();
        if($payment->success==true){
            $update = array(
                'Status'                        => 'Submitted',
                'PaymentStatusID'               => $payment->data->PaymentStatusID,
                'CompanyCode'                   => $payment->data->PaymentDetail[0]->CompanyCode,
                'VirtualAccount'                => $payment->data->PaymentDetail[0]->VirtualAccount,
                'ServiceCharge'                 => $payment->data->PaymentDetail[0]->TotalServiceCharge,
                'TotalPaymentWithServiceCharge' => $payment->data->PaymentDetail[0]->TotalPaidWithServiceCharge
            );
           
            $this->db->where('SupplyTransID',$recpay->SupplyTransID);
            $this->db->update('ktv_tc_supplychain_transaction',$update);

            $results['success'] = true;
            $results['message'] = "Payment Success";
            $results['PaymentStatusID'] = (string) $payment->data->PaymentStatusID;
            $results['SupplyTransID'] = $recpay->SupplyTransID;
            $results['data'] = $payment->data;
        }else{
            $results['success'] = false;
            $results['message'] = $payment['ErrorMessage'];
            $results['PaymentStatusID'] = $post['PaymentStatusID'];
            $results['SupplyTransID'] = $recpay->SupplyTransID;
            $results['data'] = array();
        }
        
        return $results;
    }

    public function CheckPaymentStatus($get){
        // var_dump($get);
        // die;
        $results =array();
        $param = array(
            "PaymentMethodID" => $get['PaymentMethodID'],
            "uid" => $get['uid'],
            "LanguageID" => $_SESSION['language']=='Indonesia'?2:1
        );
        $PaymentStatus = $this->APIChekPaymentStatus($param);

        if($PaymentStatus->success==true AND  intval($PaymentStatus->data->PaymentStatusID)!=0){
            $ps=$PaymentStatus->data->PaymentDetail[0];
            
            $sql = "SELECT
                        SupplyTransID,
                        PaymentStatusID,
                        CompanyCode,
                        VirtualAccount,
                        TotalPayment,
                        ServiceCharge,
                        TotalPaymentWithServiceCharge,
                        DisburseFee,
                        TotalDisburse,
                        DateCreated
                    FROM
                        ktv_tc_supplychain_transaction
                    WHERE SupplyTransID = ?";
            $check = $this->db->query($sql,array($get['SupplyTransID']))->row();
            if($check->CompanyCode=="" OR $check->VirtualAccount=="" OR $check->ServiceCharge=="" OR $check->DisburseFee=="" OR $check->TotalDisburse==""){
                $update = array(
                    "PaymentStatusID"   => $ps->PaymentStatusID,
                    "PaymentMethodID"   => $ps->PaymentMethodID,
                    "CompanyCode"       => $ps->CompanyCode,
                    "VirtualAccount"    => $ps->VirtualAccount,
                    "ServiceCharge"     => $ps->TotalServiceCharge,
                    "TotalPaymentWithServiceCharge" => $ps->TotalPaidWithServiceCharge,
                    "DisburseFee" => $ps->FeeDisburse,
                    "TotalDisburse" => $ps->TotalDisburse
                );
            }else{
                $update = array(
                    "PaymentStatusID"   => $ps->PaymentStatusID,
                    "PaymentMethodID"   => $ps->PaymentMethodID,
                    "DisburseFee"       => $ps->FeeDisburse,
                    "TotalDisburse"     => $ps->TotalDisburse
                );
            }
            $this->db->where("SupplyTransID",$get['SupplyTransID']);
            $this->db->update("ktv_tc_supplychain_transaction",$update);
            
            $results['success'] = true;
            $results['message'] = "Check Payment Success";
            $results['data'] = $PaymentStatus->data;

        }else{
            $results['success'] = false;
            $results['message'] = "Check Payment Failed";
            $results['data'] = array();
        }

        return $results;
       
    }
}
