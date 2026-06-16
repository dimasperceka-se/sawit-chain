<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mprocessing extends CI_Model {
    protected $_table = 'ktv_receipts';

    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }
    
    private function generateSqlFilter($pSearch) {
        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN) 
        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= " AND (KR.TransactionNumber like '%{$pSearch['textSearch']}%') ";
        } 

        return $sqlFilter;
        //BENTUK QUERY FILTER =============================================== (END)
    }

    public function list_vehicle_type(){
        $sql = "SELECT
            VehicleTypeID
            ,CASE
                WHEN VehicleTypeName = 'Truck' THEN '" . lang('Truck') . "'
                WHEN VehicleTypeName = 'Mini Truck' THEN '" . lang('Mini Truck') . "'
                WHEN VehicleTypeName = 'Pick Up' THEN '" . lang('Pick Up') . "'
                WHEN VehicleTypeName = 'Truck Cold Diesel' THEN '" . lang('Truck Cold Diesel') . "'
                WHEN VehicleTypeName = 'Dump Truck' THEN '" . lang('Dump Truck') . "'
                WHEN VehicleTypeName = 'Other' THEN '" . lang('Other') . "'
                ELSE '-'
            END AS VehicleTypeName
        FROM
            `ref_tc_despatch_vehicle_type` a
        WHERE
            a.StatusCode = 'active'";
        $query = $this->db->query($sql);
        $result["data"]     = $query->result_array();
        $result["total"]    = $query->num_rows();
        $result["sql"]    = $this->db->last_query();
        return $result;
    }

    public function list_destination(){
        $sql = "SELECT
                vso.SupplychainID DestinationID
                , vso.`Name` DestinationName
            FROM
                `ktv_tc_supplychain_org_rel` orel
            LEFT JOIN
                view_tc_supplychain_org vso on vso.SupplychainID = orel.ParentID
            WHERE
                orel.ChildID = ?
            AND
                orel.StatusCode = 'active'
            AND
                vso.ObjType = 'refinery'
            AND
                DATE(NOW()) BETWEEN orel.StartDate AND orel.EndDate";
        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        $result["data"]     = $query->result_array();
        $result["total"]    = $query->num_rows();
        $result["sql"]    = $this->db->last_query();
        return $result;
    }

    public function getDispatchExcel(){
        $sql = "SELECT
                a.DespatchNumber AS DispatchNumber
                , a.ShippingDate
                , IFNULL(vso2.`Name`, vso2.Alias ) Mill
                , IFNULL(vso.`Name`, vso.Alias) Destination
                , ts.TransactionStatusName DispatchStatusName
                , a.PackingDate
                , a.DateCreated
            FROM
                `ktv_tc_despatch` a
                LEFT JOIN 
                    ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                LEFT JOIN 
                    view_tc_supplychain_org vso on vso.SupplychainID = a.DestinationID
                LEFT JOIN 
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = a.SupplychainID
            WHERE
                a.SupplychainID = ?
                AND a.StatusCode = 'active'
        "; 
                     
        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;
        if($query->num_rows()>0){
            $result = $query->result_array();
        }else{
            $result = false;
        }

        return $result;
    }
    
    public function getDispatchDetailExcel(){
        $sql = "SELECT
                a.DespatchNumber AS DispatchNumber
                , IFNULL(vso2.`Name`, vso2.Alias ) Mill
                , IFNULL(vso.`Name`, vso.Alias) Destination
                , a.ShippingDate
                , a.PackingDate
            FROM
                `ktv_tc_despatch` a
                LEFT JOIN 
                    ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                LEFT JOIN 
                    view_tc_supplychain_org vso on vso.SupplychainID = a.DestinationID
                LEFT JOIN 
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = a.SupplychainID
            WHERE
                a.SupplychainID = ?
                AND a.StatusCode = 'active'
        "; 
                     
        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;
        if($query->num_rows()>0){
            $result = $query->result_array();
        }else{
            $result = false;
        }

        return $result;
    }
    
    public function getDispatchVehicleExcel(){
        $sql = "SELECT
               a.DespatchNumber AS DispatchNumber
                , IFNULL(vso2.`Name`, vso2.Alias ) Mill
                , IFNULL(vso.`Name`, vso.Alias) Destination
                , a.ShippingDate
                , a.PackingDate
            FROM
                `ktv_tc_despatch` a
                LEFT JOIN 
                    ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                LEFT JOIN 
                    view_tc_supplychain_org vso on vso.SupplychainID = a.DestinationID
                LEFT JOIN 
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = a.SupplychainID
            WHERE
                a.SupplychainID = ?
        AND a.StatusCode = 'active'
        "; 
                     
        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;
        if($query->num_rows()>0){
            $result = $query->result_array();
        }else{
            $result = false;
        }

        return $result;
    }
    
    public function fetch_data($pSearch, $start, $limit, $sortingField, $sortingDir){
        $sqlFilter = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);
        
        if ($sortingField == "")
            $sortingField = 'pc.ProcessingID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

         $start = (int) $start;
         $limit = (int) $limit;
         $sqlLimit = " LIMIT {$start},{$limit}";
         
        $sql = "SELECT
                    pc.ProcessingID,
                    pc.ProcessingNumber,
                    pc.ProcessingDate,
                    (SELECT SUM(pcd.ProcessingVolume) AS total FROM ktv_tc_processing_detail pcd WHERE pcd.ProcessingID=pc.ProcessingID AND pcd.StatusCode='active') AS ProcessingVolume,
                    SUM(IF(pcp.ProductID='1', pcp.ProductVolume, 0)) AS ProductVolumeCPO,
                    SUM(IF(pcp.ProductID='2', pcp.ProductVolume, 0)) AS ProductVolumePK,
                    SUM(IF(pcp.ProductID='1', pcp.RemainingVolume, 0)) AS RemainingVolumeCPO,
                    SUM(IF(pcp.ProductID='2', pcp.RemainingVolume, 0)) AS RemainingVolumePK

                FROM
                    ktv_tc_processing pc 
                    LEFT JOIN ktc_tc_processing_product pcp ON pcp.ProcessingID=pc.ProcessingID AND pcp.StatusCode='active'
                WHERE
                    pc.StatusCode = 'active'
                    AND pc.SupplychainID=?
                $sqlFilter
                GROUP BY pc.ProcessingID
                ORDER BY $sortingField $sortingDir
                $sqlLimit 
        "; 
                     
        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        $result['data'] = $query->result_array();
        // echo $this->db->last_query();die;
        
        $result['total'] = $query->num_rows();
        return $result;
    }

    public function proses_batch($ProcessingID){
        
        $sql = "SELECT
            a.SupplychainID,
            a.ReceiptDeliveryID
        FROM
            ktv_tc_reception a
        LEFT JOIN
            ktv_tc_processing b ON b.GenerateDelivery = a.ReceiptDeliveryID
        LEFT JOIN 
            ktv_tc_despatch c ON c.SupplychainID = a.SupplychainID 
        WHERE
            a.SupplychainID = ?
        AND 
            a.ProcessingID = 0
        AND 
            c.DestpatchStatusID = '1'";

        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        
        $CheckDeliveryID = $query->result_array();
        if($CheckDeliveryID){
            foreach ($CheckDeliveryID as $key => $value) {
                $update["ProcessingID"]       = $ProcessingID;
                $ReceiptDeliveryID          = $value['ReceiptDeliveryID'];
                
                $this->db->where("ReceiptDeliveryID",$ReceiptDeliveryID);
                $this->db->update("ktv_tc_reception",$update);
            }
        }

        $sql = "SELECT
            *
        FROM
            ktv_tc_despatch_detail
        WHERE
            ProcessingID = ?";
        $query = $this->db->query($sql,array($ProcessingID));

        if($query->num_rows()>0){
            $update["DestpatchStatusID"] = "4";
            
            $this->db->where("ProcessingID",$ProcessingID);
            $this->db->update("ktv_tc_despatch",$update);

            return true;
        }

        return false;
    }

    public function sent_batch($ProcessingID){
        $sql = "SELECT
            *
        FROM
            ktv_tc_despatch_vehicle
        WHERE
            ProcessingID = ?";
        $query = $this->db->query($sql,array($ProcessingID));
        if($query->num_rows()>0){
            $update["DestpatchStatusID"] = "5";

            $this->db->where("ProcessingID",$ProcessingID);
            $query = $this->db->update("ktv_tc_despatch",$update);

            if($query){
                $this->insertReception($ProcessingID);
            }

            return true;
        }
        return false;
    }

    public function insertReception($ProcessingID){
        $sql = "SELECT
                a.DestinationID
                , b.UserID
            FROM
                ktv_tc_despatch a
            LEFT JOIN
                view_tc_supplychain_staff b on b.SupplychainID = a.DestinationID
            WHERE
                a.ProcessingID = ?";
        $query = $this->db->query($sql,array($ProcessingID));

        if($query->num_rows()>0){
            $rows = $query->row();
            
            $dataPost["SupplychainID"]      = $rows->DestinationID;
            $dataPost["ReceptionNumber"]    = time();
            $dataPost["ReceptionDate"]      = date("Y-m-d");
            $dataPost["ProcessingID"]       = $ProcessingID;
            $dataPost["StatusCode"]         = "active";
            $dataPost["CreatedBy"]          = $rows->UserID;
            $dataPost["DateCreated"]        = date("Y-m-d");

            $insert = $this->db->insert("ktv_tc_reception",$dataPost);

            $ReceptionID =  $this->db->insert_id();

            $sql = "SELECT
                        a.DespatchDetailID
                        , a.ProductID
                        , a.DespatchVolume
                        , sp.ProductPercentage
                        , OilType
                    FROM
                        `ktv_tc_despatch_detail` a
                    LEFT JOIN
                        ktv_tc_despatch d on d.ProcessingID = a.ProcessingID
                    LEFT JOIN
                        ref_tc_processing_product pp on pp.ProductID= a.ProductID
                    LEFT JOIN
                        ktv_tc_supplychain_product sp on sp.OilType = pp.ProductName AND sp.SupplychainID = d.DestinationID
                    WHERE
                        a.ProcessingID = ?";
            $query = $this->db->query($sql,array($ProcessingID));

            if($query->num_rows()>0){
                $dataPostDetail = array();
                foreach($query->result() as $key => $row){
                    $dataPostDetail[$key]["ReceptionID"]        = $ReceptionID;
                    $dataPostDetail[$key]["DespatchDetailID"]   = $row->DespatchDetailID;
                    $dataPostDetail[$key]["ProductID"]          = $row->ProductID;
                    $dataPostDetail[$key]["ReceptionVolume"]    = $row->DespatchVolume;
                    $dataPostDetail[$key]["RemainingVolume"]    = 0;
                    $dataPostDetail[$key]["StatusCode"]         = 'active';
                    $dataPostDetail[$key]["CreatedBy"]          = $rows->UserID;
                    $dataPostDetail[$key]["DateCreated"]        = date("Y-m-d");
                }
                $insert = $this->db->insert_batch("ktv_tc_reception_detail",$dataPostDetail);
                if($insert){
                    
                    //Insert Processing Refinery
                    $sql = "SELECT
                        kso.ProductionCapacity
                        , kso.WorkHour
                    FROM
                        ktv_tc_supplychain_org kso
                    WHERE
                        kso.SupplychainID = ?";
                    $query = $this->db->query($sql,array($rows->DestinationID));

                    if($query->num_rows()>0){
                        $keys = $query->row();
                        $post["SupplychainID"]      = $rows->DestinationID;
                        $post["ProcessingNumber"]   = time();
                        $post["ProcessingDate"]     = date("Y-m-d");
                        $post["ProductionCapacity"] = $keys->ProductionCapacity;
                        $post["WorkHour"]           = $keys->WorkHour;
                        $post["StatusCode"]         = 'active';
                        $post["CreatedBy"]          = $rows->UserID;
                        $post["DateCreated"]        = date("Y-m-d");

                        $insert_processing = $this->db->insert("ktv_tc_processing",$post);
                        if($insert_processing){
                            $ProcessingID = $this->db->insert_id();
                            $sql ="SELECT
                                    ReceptionDetailID
                                    , ProductID
                                    , ReceptionVolume
                                FROM
                                    `ktv_tc_reception_detail`
                                WHERE
                                    ReceptionID = ?";
                            $query = $this->db->query($sql,array($ReceptionID));
                            if($query->num_rows()>0){
                                $postDetail = array();
                                foreach($query->result() as $keyl => $rowl){                            
                                    $postDetail[$keyl]["ProcessingID"]      =  $ProcessingID;
                                    $postDetail[$keyl]["ObjTypeID"]         =  '2';
                                    $postDetail[$keyl]["ObjID"]             =  $rowl->ReceptionDetailID;
                                    $postDetail[$keyl]["ProductID"]         =  $rowl->ProductID;
                                    $postDetail[$keyl]["ProcessingVolume"]  =  $rowl->ReceptionVolume;
                                    $postDetail[$keyl]["StatusCode"]        = 'active';
                                    $postDetail[$keyl]["CreatedBy"]         = $rows->UserID;
                                    $postDetail[$keyl]["DateCreated"]       = date("Y-m-d");
                                }
                                $insert = $this->db->insert_batch("ktv_tc_processing_detail",$postDetail);
                                if($insert){
                                    $sql2 = "SELECT
                                            pd.*
                                        FROM
                                            ktv_tc_supplychain_product pd
                                        WHERE
                                            pd.StatusCode='active'
                                            AND NOW() BETWEEN pd.StartDate AND pd.EndDate
                                            AND pd.SupplychainID = ?";
                                    $query2 = $this->db->query($sql2,array($rows->DestinationID));
                                    
                                    if($query2->num_rows()>0){
                                        $product = array();
                                        foreach($query2->result() as $num => $row2){
                                            $total_volume = $this->getTotalVolume($rows->DestinationID,$ProcessingID,$row2->OilType);
                                            $product[$num]["ProcessingID"]       = $ProcessingID;
                                            $product[$num]["ProductID"]          = $row2->ProductID;
                                            $product[$num]["ProductPercentage"]  = $row2->ProductPercentage;
                                            $product[$num]["ProductVolume"]      = ($total_volume*$row2->ProductPercentage)/100;
                                            $product[$num]["RemainingVolume"]    = ($total_volume*$row2->ProductPercentage)/100;
                                            $product[$num]["StatusCode"]         = 'active';
                                            $product[$num]["CreatedBy"]          = 1;
                                            $product[$num]["DateCreated"]        = date("Y-m-d H:i:s");
                                        }
                                        $this->db->insert_batch("ktc_tc_processing_product",$product);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
        }
    }

    public function getTotalVolume($DestinationID, $ProcessingID,$OilType){
        $sql = "SELECT
            IFNULL(SUM(pd.ProcessingVolume),0) total_volume
        FROM
            `ktv_tc_processing_detail` pd
        LEFT JOIN
            ref_tc_processing_product pp on pp.ProductID = pd.ProductID
        INNER JOIN
            ktv_tc_processing p on p.ProcessingID = pd.ProcessingID
        WHERE
            pd.ProcessingID = ?
        AND
            pp.ProductName = ?
        AND
            p.SupplychainID = ?";
        $query = $this->db->query($sql,array($ProcessingID,$OilType,$DestinationID));

        $total_volume = 0;
        if($query->num_rows()>0){
            $row = $query->row();
            $total_volume = $row->total_volume;            
        }

        return $total_volume;
    }
    
    public function fetchvehicle($ProcessingID, $start, $limit, $sortingField, $sortingDir, $ProductID){
        if ($sortingField == "")
            $sortingField = 'ProcessingProductID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        $start = (int) $start;
        $limit = (int) $limit;
        $sqlLimit = " LIMIT {$start},{$limit}";
        
        $sql = "SELECT
                    pp.ProcessingProductID,
                    rpp.ProductName,
                    pp.ProductPercentage,
                    pp.ProductVolume,
                    pp.RemainingVolume
                FROM
                    `ktc_tc_processing_product` pp
                    LEFT JOIN ref_tc_processing_product rpp ON rpp.ProductID=pp.ProductID
                WHERE
                    pp.StatusCode='active'
                    AND pp.ProcessingID=?
        $where
        ORDER BY $sortingField $sortingDir $sqlLimit"; 
                    
        $query = $this->db->query($sql, array( $ProcessingID ) );

        $getData = $query->result_array();

        $result['data']  = $getData;
        $result['total'] = count($getData);

        return $result;
     }

    public function fetchvehiclebyID($ProcessingID, $ProcessingProductID){
        $sql = "SELECT
                    p.ProcessingID,
                    SUM(pd.ProcessingVolume) AS ProcessingVolume,
                    m.HaveOer AS FlagOer
                FROM 
                    ktv_tc_processing p
                    LEFT JOIN ktv_tc_processing_detail pd ON pd.ProcessingID=p.ProcessingID AND pd.StatusCode='active'
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=p.SupplychainID
                    LEFT JOIN ktv_mill m ON m.MillID=vso.ObjID AND vso.ObjType='mill'
                WHERE
                    p.ProcessingID=?";
        $query1 = $this->db->query($sql, [$ProcessingID])->row_array(0);

        $sql = "SELECT
                    pp.ProcessingProductID,
                    pp.ProductID,
                    pp.ProductPercentage,
                    pp.ProductVolume
                FROM 
                    ktc_tc_processing_product pp
                WHERE
                    pp.ProcessingProductID=?";
        $query2 = $this->db->query($sql, [$ProcessingProductID])->row_array(0);

        $data = array(
            'ProcessingID' => @$query1['ProcessingID'],
            'FlagOer' => @$query1['FlagOer'],
            'ProcessingVolume' => @$query1['ProcessingVolume'],
            'ProcessingProductID' => @$query2['ProcessingProductID'],
            'ProductID' => @$query2['ProductID'],
            'ProductPercentage' => @$query2['ProductPercentage'],
            'ProductVolume' => @$query2['ProductVolume'],
        );
        //echo "<pre>".print_r($return, 1);die;
        return $data;
    }
    
    public function fetchpick($ProcessingID, $start, $limit, $sortingField, $sortingDir, $ProductID){
        
        if ($sortingField == "")
            $sortingField = 'ProcessingTransactionID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        $start = (int) $start;
        $limit = (int) $limit;
        $sqlLimit = " LIMIT {$start},{$limit}";
        
        if(intval($ProcessingID) > 0){
            $p1 = '/*'; $p2 = '*/';
        }elseif($ProcessingID == ''){
            $p1 = '/*'; $p2 = '*/';
        }else{
            $p1 = ''; $p2 = '';
        }
        
        $SID = $_SESSION['SupplychainID'];
        
        $sql = "SELECT
                    pd.ProcessingTransactionID,
                    IFNULL(st.DateTransaction, sd.DeliveryDate) AS ReceiveDate,
                    sd.ExternalCode,
                    vso.`Name` AS Supplier,
                    sd.DestTransportNumber,
                    std.TotalCapacity AS Nett
                FROM
                    `ktv_tc_supplychain_transaction_detail` std
                    LEFT JOIN ktv_tc_processing_detail pd ON pd.ObjTypeID='1' AND pd.ObjID=std.SupplyTransID AND pd.StatusCode='active'
                    LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyTransID=std.SupplyTransID
                    LEFT JOIN ktv_tc_supplychain_delivery sd ON sd.DeliveryID=st.SupplyID AND st.SupplyType='Delivery'
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sd.SupplychainID
                WHERE 1=1
                    AND ((pd.ProcessingID=? AND pd.StatusCode='active' AND sd.SupplyDestMillOrgID = '$SID')
                        $p1 OR pd.ProcessingTransactionID IS NULL $p2
                    )

        ORDER BY $sortingField $sortingDir $sqlLimit"; 
                     
        $query = $this->db->query($sql, array( $ProcessingID) );

        $data = array();

        if($query->num_rows()>0){
            $getData = $query->result();

            foreach($getData as $key => $row){
                $data[$key]["ProcessingTransactionID"]  = $row->ProcessingTransactionID;
                $data[$key]["ReceiveDate"]              = $row->ReceiveDate;
                $data[$key]["ExternalCode"]             = $row->ExternalCode;
                $data[$key]["Supplier"]                 = $row->Supplier;
                $data[$key]["DestTransportNumber"]      = $row->DestTransportNumber;
                $data[$key]["Nett"]                     = (float)$row->Nett;
            }
        }
        $result['data'] = $data;
        // echo $this->db->last_query();die;
        
        $result['total'] = $query->num_rows();
        return $result;
    }

    public function fetchProduct($ProcessingID, $start, $limit, $sortingField, $sortingDir){
        
        if ($sortingField == "")
            $sortingField = 'ProcessingProductID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

         $start = (int) $start;
         $limit = (int) $limit;
         $sqlLimit = " LIMIT {$start},{$limit}";
          
        $sql = "SELECT
                    pp.ProcessingProductID,
                    pp.ProcessingID,
                    rpp.ProductName,
                    pp.ProductPercentage,
                    pp.ProductVolume,
                    pp.RemainingVolume
                FROM
                    `ktc_tc_processing_product` pp
                    LEFT JOIN ref_tc_processing_product rpp ON rpp.ProductID=pp.ProductID
                WHERE
                    pp.StatusCode='active'
                    AND pp.ProcessingID=?

        ORDER BY $sortingField $sortingDir $sqlLimit"; 
                     
        $query = $this->db->query($sql, array( $ProcessingID) );

        $data = array();

        if($query->num_rows()>0){
            $getData = $query->result();

            foreach($getData as $key => $row){
                $data[$key]["ProcessingProductID"]  = $row->ProcessingProductID;
                $data[$key]["ProcessingID"]         = $row->ProcessingID;
                $data[$key]["ProductName"]          = $row->ProductName;
                $data[$key]["ProductPercentage"]    = $row->ProductPercentage;
                $data[$key]["ProductVolume"]        = (float)$row->ProductVolume;
                $data[$key]["RemainingVolume"]      = (float)$row->RemainingVolume;
            }
        }
        $result['data'] = $data;
        //echo $this->db->last_query();die;
        
        $result['total'] = $query->num_rows();
        return $result;
    }
    
    public function get_data_quality_value($QualityID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('QualityID,  CONCAT( Label," - ", Description) as Value ', FALSE)
          ->from('ref_quality_combo_value')
          ->where('QualityID', $QualityID)
          ->where('StatusCode', 'active')
          ->get();
        //echo $this->db->last_query();die;
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $return['data'] = $result;
            $return['total'] = $Q->num_rows();
            return $return;
        }
        return $return;
    }
    

    public function submitVehicle($params){
        $result = false;
        $error = 'error';
        $insid = 0; 
        $data=array();
        foreach ($params as $key => $value) { 
            $data[$key] = $value;
        }
        
        try{
            $this->db->trans_begin();
            $insert = array(
                'ProcessingID'          => @$data['ProcessingID'],
                'ProductID'             => @$data['ProductID'],
                'ProductPercentage'     => (float)@$data['ProductPercentage'], 
                'ProductVolume'         => (float)@$data['ProductVolume'],
                'RemainingVolume'       => (float)@$data['ProductVolume'], 
                'FlagOer'               => @$data['FlagOer'],
                'StatusCode'            => 'active',
            );
             
            $Cektrans = $this->db->query("SELECT * FROM ktc_tc_processing_product WHERE ProcessingProductID = ?", array($data['ProcessingProductID'])); 
            
            if($Cektrans->num_rows() > 0 ){ 
                $insert['DateUpdated'] = date('Y-m-d H:i:s');
                $insert['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                
                $this->db->where('ProcessingProductID', $data['ProcessingProductID']);
                $this->db->update('ktc_tc_processing_product', $insert);
            }else{
                $insert['DateCreated'] = date('Y-m-d H:i:s'); 
                $insert['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktc_tc_processing_product', $insert);
            }

            if($this->db->trans_status() == false){
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        //$this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result, 'error' => $error);
        }
    }

    public function generateDoNumber(){
        $sql = "SELECT
            SUBSTRING(DeliveryOrderNumber, -6) DeliveryOrderNumber
        FROM
            `ktv_tc_despatch_vehicle` a
        LEFT JOIN
            ktv_tc_despatch b on b.ProcessingID = a.ProcessingID
        AND
            b.SupplychainID = ?
        AND
            YEAR(SUBSTRING_INDEX(SUBSTRING_INDEX(DeliveryOrderNumber,'.',-2),'.',1)) = YEAR(NOW())
        ORDER BY
            DeliveryOrderNumber DESC
        LIMIT 1";

        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        if($query->num_rows()>0){
            $row    = $query->row();
            $fno    = $row->DeliveryOrderNumber;
            $fno++;
        }else{
            $fno   = 0;
            $fno++;
        }
        if (strlen($fno)==1){
            $strfno = "00000".$fno;
        } else if (strlen($fno)==2){
            $strfno = "0000".$fno;
        } else if (strlen($fno)==3){
            $strfno = "000".$fno;
        } else if (strlen($fno)==4){
            $strfno = "00".$fno;
        } else if (strlen($fno)==5){
            $strfno = "0".$fno;
        } else if (strlen($fno)==5){
            $strfno = $fno;
        }

        $DOnumber = "DO.".$_SESSION["SupplychainID"].".".date("Ymd").".".$strfno;
        return $DOnumber;
    }
        
    public function submit($params){
        $remaining_status = true;
        $result = false;
        $error = 'error';
        $insid = 0; 
        $data=array();
        foreach ($params as $key => $value) { 
            $data[$key] = $value;
        }

        //echo "<pre>".print_r($data, 1);die;

        if($data['SupplychainID']==''){
            $SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
            $data['SupplychainID'] = $SupplychainID;
        }

        try{
            $this->db->trans_begin();
            $insert = array(
                'SupplychainID'     => @$data['SupplychainID'],
                //'ProcessingNumber'  => @$data['ProcessingNumber'], 
                'ProcessingDate'    => @$data['ProcessingDate'],
                'StatusGenerate'    => '1',
                'StatusCode'        => 'active',
                'CreatedBy'         => $_SESSION['userid'],
                'DateCreated'       => date("Y-m-d H:i:s")
            );
             
            $Cektrans = $this->db->query("SELECT * FROM ktv_tc_processing WHERE ProcessingID = ?", array($data['ProcessingID']));

            $sql = "SELECT
                        m.HaveOer AS FlagOer
                    FROM 
                        view_tc_supplychain_org vso
                        LEFT JOIN ktv_mill m ON m.MillID=vso.ObjID AND vso.ObjType='mill'
                    WHERE
                        vso.SupplychainID=?";
            $query1 = $this->db->query($sql, [@$data['SupplychainID']])->row_array(0);
            
            if($Cektrans->num_rows() > 0 ){ 
                $ProductionCapacity = $this->db->query("SELECT SUM(pcd.ProcessingVolume) AS total FROM ktv_tc_processing_detail pcd WHERE pcd.ProcessingID=? AND pcd.StatusCode='active'", [$data['ProcessingID']])->row()->total;

                $insert['ProductionCapacity'] = $ProductionCapacity;
                $insert['DateUpdated'] = date('Y-m-d H:i:s');
                $insert['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                
                $this->db->where('ProcessingID', $data['ProcessingID']);
                $this->db->update('ktv_tc_processing', $insert); 
                $id = $data['ProcessingID'];
                $ProcessingNumber = $Cektrans->row()->ProcessingNumber;
                $ProcessingVolume = $Cektrans->row()->ProductionCapacity;
                $status = "update";

                $dispatch_1 = $this->db->query("SELECT SUM(DespatchVolume) AS total FROM ktv_tc_despatch_detail WHERE StatusCode='active' AND ProcessingProductID=?", [@$data['CPO_ProcessingProductID']])->row()->total;
                $remaining_1 = (float) str_replace(",", "", @$data['CPO_ProductVolume']) - floatval($dispatch_1);
                $update1 = array(
                    'ProductPercentage' => (float) str_replace(",", "", @$data['CPO_ProductPercentage']),
                    'ProductVolume' => (float) str_replace(",", "", @$data['CPO_ProductVolume']),
                    'RemainingVolume' => $remaining_1
                );
                $this->db->where('ProcessingProductID', @$data['CPO_ProcessingProductID']);
                $this->db->update('ktc_tc_processing_product', $update1); 

                $dispatch_2 = $this->db->query("SELECT SUM(DespatchVolume) AS total FROM ktv_tc_despatch_detail WHERE StatusCode='active' AND ProcessingProductID=?", [@$data['PK_ProcessingProductID']])->row()->total;
                $remaining_2 = (float) str_replace(",", "", @$data['PK_ProductVolume']) - floatval($dispatch_2);
                $update2 = array(
                    'ProductPercentage' => (float) str_replace(",", "", @$data['PK_ProductPercentage']),
                    'ProductVolume' => (float) str_replace(",", "", @$data['PK_ProductVolume']),
                    'RemainingVolume' => $remaining_2
                );
                $this->db->where('ProcessingProductID', @$data['PK_ProcessingProductID']);
                $this->db->update('ktc_tc_processing_product', $update2);

                if($remaining_1 < 0 || $remaining_2 < 0){
                    $remaining_status = false;
                } 
            }else{  
                $insert['ProcessingNumber'] = $this->getProcessingCode();
                $insert['DateCreated'] = date('Y-m-d H:i:s'); 
                $insert['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_processing', $insert); 
                $id = $this->db->insert_id();
                $ProcessingNumber = $insert['ProcessingNumber'];
                $status = "insert";

                $userid = $_SESSION['userid'];
                $SID    = $_SESSION['SupplychainID'];

                $sql = "SELECT
                        $id AS ProcessingID,
                        '1' AS ObjTypeID,
                        std.SupplyTransID AS ObjID,
                        std.TotalCapacity AS ProcessingVolume,
                        'active' AS StatusCode,
                        $userid AS CreatedBy,
                        NOW() AS DateCreated
                    FROM
                        `ktv_tc_supplychain_transaction_detail` std
                        LEFT JOIN ktv_tc_processing_detail pd ON pd.ObjTypeID='1' AND pd.ObjID=std.SupplyTransID AND pd.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyTransID=std.SupplyTransID
                        LEFT JOIN ktv_tc_supplychain_delivery sd ON sd.DeliveryID=st.SupplyID AND st.SupplyType='Delivery'
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sd.SupplychainID
                    WHERE 1=1
                        AND pd.ProcessingTransactionID IS NULL AND sd.SupplyDestMillOrgID = '$SID'";
                $query = $this->db->query($sql, array($sql));

                if($query->num_rows() > 0){
                    $ProcessingVolume = 0;
                    foreach ($query->result_array() as $key => $value) {
                        $this->db->insert('ktv_tc_processing_detail', $value);
                        $ProcessingVolume = $ProcessingVolume + floatval($value['ProcessingVolume']);
                    }
                }

                $this->db->where('ProcessingID', $data['ProcessingID']);
                $this->db->update('ktv_tc_processing', array('ProductionCapacity' => @$ProcessingVolume));

                //Product
                $insert_product_1 = array(
                    'ProcessingID'  => $id,
                    'ProductID'     => '1',
                    'FlagOer'       => @$query1['FlagOer'],
                    'DateCreated'   => date('Y-m-d H:i:s'),
                    'CreatedBy'     => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1
                );
                $this->db->insert('ktc_tc_processing_product', $insert_product_1);
                $data['CPO_ProcessingProductID'] = $this->db->insert_id();

                $insert_product_2 = array(
                    'ProcessingID'  => $id,
                    'ProductID'     => '2',
                    'FlagOer'       => @$query1['FlagOer'],
                    'DateCreated'   => date('Y-m-d H:i:s'),
                    'CreatedBy'     => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1
                );
                $this->db->insert('ktc_tc_processing_product', $insert_product_2); 
                $data['PK_ProcessingProductID'] = $this->db->insert_id();
            }
            if($this->db->trans_status() == false || $remaining_status==false){
                $this->db->trans_rollback();
                $error = lang('Connection error');
                if($remaining_status==false){
                    $error = lang("Remaining product less than 0");
                }
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
        }

        //$this->db->trans_complete();

        if($result) {
            return array(
                'success'                   => $result, 
                'id'                        => $id, 
                'ProcessingNumber'          => $ProcessingNumber, 
                'ProductionCapacity'        => @$ProcessingVolume, 
                'CPO_ProcessingProductID'   => @$data['CPO_ProcessingProductID'], 
                'CPO_ProductPercentage'     => @$data['CPO_ProductPercentage'], 
                'CPO_ProductVolume'         => @$data['CPO_ProductVolume'], 
                'PK_ProcessingProductID'    => @$data['PK_ProcessingProductID'], 
                'PK_ProductPercentage'      => @$data['PK_ProductPercentage'], 
                'PK_ProductVolume'          => @$data['PK_ProductVolume'], 
                'FlagOer'                   => @$query1['FlagOer'], 
                'status'=> $status
            );
        }else{
            return array('success' => $result, 'error' => $error);
        }
    } 
     
    public function delete($id=0){
        if($id){
            $update1 = array(
                'StatusCode' => 'nullified',
                'LastModifiedBy' => $_SESSION['userid'],
                'DateUpdated' => date('Y-m-d H:i:s')
            );
            $this->db->where('ProcessingID', $id);
            $this->db->update('ktv_tc_processing', ['StatusCode' => 'nullified']);

            $update2 = array(
                'StatusCode' => 'nullified',
                'LastModifiedBy' => $_SESSION['userid'],
                'DateUpdated' => date('Y-m-d H:i:s'),
                'ObjID' => NULL
            );
            $this->db->where('ProcessingID', $id);
            $this->db->update('ktv_tc_processing_detail', $update2);

            $update2 = array(
                'StatusCode' => 'nullified',
                'LastModifiedBy' => $_SESSION['userid'],
                'DateUpdated' => date('Y-m-d H:i:s'),
                'RemainingVolume' => 0,
                'StatusCode' => 'nullified'
            );
            $this->db->where('ProcessingID', $id);
            $this->db->update('ktc_tc_processing_product', $update2);
            return true;
        }
        return array();
    }
    
    public function delprocess($id=0){
        if($id){
            $this->db->where('ProcessingProcessID', $id);
            $this->db->update('ktv_transaction_processing_process', ['StatusCode' => 'nullified']);
            // echo $this->db->last_query();die;
            return true;
        }
        return array();
    }
    
    public function basicdata($id){
        $SQL = "SELECT
                    p.ProcessingID,
                    p.ProcessingNumber,
                    p.SupplychainID,
                    p.ProductionCapacity
                FROM
                    ktv_tc_processing p
                WHERE
                    p.ProcessingID = ?";
        $query = $this->db->query($SQL, array($id));
        $data1 = $query->row_array(0);

        $sql = "SELECT
                    m.HaveOer AS FlagOer
                FROM 
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_mill m ON m.MillID=vso.ObjID AND vso.ObjType='mill'
                WHERE
                    vso.SupplychainID=?";
        $query1 = $this->db->query($sql, [@$data1['SupplychainID']])->row_array(0);

        $ProductionCapacity = $this->db->query("SELECT SUM(pcd.ProcessingVolume) AS total FROM ktv_tc_processing_detail pcd WHERE pcd.ProcessingID=? AND pcd.StatusCode='active'", [$id])->row()->total;

        $return = array(
            'ProcessingID' => $data1['ProcessingID'],
            'ProcessingNumber' => $data1['ProcessingNumber'],
            'ProductionCapacity' => $ProductionCapacity,
            'FlagOer' => $query1['FlagOer']
        );

        $SQL2 = "SELECT *
                FROM
                    ktc_tc_processing_product pp
                WHERE
                    pp.ProcessingID = ?";
        $query2 = $this->db->query($SQL2, array($id));
        if($query2->num_rows() > 0){
            foreach ($query2->result_array() as $key => $value) {
                if($value['ProductID']=='1'){
                    $return['CPO_ProcessingProductID'] = $value['ProcessingProductID'];
                    $return['CPO_ProductPercentage'] = $value['ProductPercentage'];
                    $return['CPO_ProductVolume'] = $value['ProductVolume'];
                }else if($value['ProductID']=='2'){
                    $return['PK_ProcessingProductID'] = $value['ProcessingProductID'];
                    $return['PK_ProductPercentage'] = $value['ProductPercentage'];
                    $return['PK_ProductVolume'] = $value['ProductVolume'];
                }
            }
        }

        if($query->num_rows()){
            return  array("success" => true, "data" => $return);
        }
        return array();
    }
     
    
    public function setNumberDigit($number, $digit){
        $number = (int)$number;
        $panjang = strlen($number);
        if($panjang >= $digit){
            return $number;
        }else{
            $batas = $digit - $panjang;
            for($i=0; $i < $batas; $i++){
                $number = "0".$number;
            }
        }
        return $number;
    }
    
    public function getProcessingCode(){
          $sql = "SELECT 
                    IFNULL((MAX(CAST(SUBSTRING_INDEX( ProcessingNumber, '-',- 1 ) AS UNSIGNED)) +1), 1) ProcessingNumber
                 FROM ktv_tc_processing WHERE StatusCode='active'";
        $query = $this->db->query($sql)->row();
        $Code = 'P-'.$this->setNumberDigit($query->ProcessingNumber, 6);
        return $Code;

    }
    
    
     
    public function cmbProcess($query){
          $sql = "SELECT SQL_CALC_FOUND_ROWS
                     KR.*, DATE_FORMAT(TransactionDate, '%Y-%m-%d') TransactionDate
                  FROM
                    ktv_transactions KR 
                  WHERE
                    KR.StatusCode = 'Active' and KR.TransactionTypeID = 1 
                    AND KR.TransactionRemaining > 0"; 


        $query = $this->db->query($sql); 
        //echo $this->db->last_query();die;
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

    public function Ref_Processing($query,$ProductID){
        $supplychain = $_SESSION['SupplychainID'];
        $supply = $supplychain!=""? "AND c.SupplychainID=$supplychain" : '';
        $where = !empty($ProductID) ? " AND a.ProductID=$ProductID" : '';
        $sql = "SELECT
            a.ProcessingID
            , a.ProcessingProductID
            , c.ProcessingNumber
            , c.ProcessingDate
            , FORMAT( a.RemainingVolume, 2 ) RemainingVolume
            , FORMAT( a.ProductVolume, 2 ) ProductVolume
            , tdd.DespatchVolume PickedVolume
            , a.ProductID
            , d.ProductName 
            , ktcp.ProductPercentage
            , c.StatusGenerate
        FROM
            ktc_tc_processing_product a
            LEFT JOIN ktv_tc_processing c ON c.ProcessingID = a.ProcessingID
            LEFT JOIN ref_tc_processing_product d ON d.ProductID = a.ProductID 
            LEFT JOIN ktv_tc_despatch_detail  tdd ON tdd.ProcessingProductID = a.ProcessingProductID
            LEFT JOIN ktv_tc_despatch td ON td.SupplychainID = c.SupplychainID
            LEFT JOIN ktv_tc_supplychain_product ktcp ON ktcp.SupplychainID = c.SupplychainID
        WHERE
            a.StatusCode = 'active'
            $supply
            $where
            AND 
            c.StatusGenerate IS NOT NULL
            AND td.DestpatchStatusID != '5'
        AND
            a.RemainingVolume > 0
        GROUP BY
            c.ProcessingNumber"; 
        $query = $this->db->query($sql); 
        // echo $this->db->last_query();die;
        
        
        // $CPO = 0;
        // $PKO = 0;
        $PkRemaining = 0;
        $CPORemaining = 0;
        $ProductPercentagePk  = 0;
        $ProductPercentageCpo = 0;
        $flagPk  = 0;
        $flagCpo = 0;
        
        $getProductMill = $this->db->select('d.ProductName, a.ProductPercentage')
                                   ->join('ref_tc_processing_product d', 'a.ProductID = d.ProductID')
                                   ->where('a.SupplychainID', (int) $_SESSION['SupplychainID'])
                                   ->where("a.StartDate <=", date('Y-m-d'))
                                   ->where("a.EndDate >=", date('Y-m-d'))
                                   ->get('ktv_tc_supplychain_product a')
                                   ->result();

        foreach ($getProductMill as $key => $value) {
            if (trim($value->ProductName) == "CPO") {
                $ProductPercentageCpo = $value->ProductPercentage;
                $flagCpo = 1;
            }

            if (trim($value->ProductName) == "PK") {
                $ProductPercentagePk  = $value->ProductPercentage;
                $flagPk  = 1;
            }
        }

        if($query->num_rows()>0){
            foreach($query->result() as $row){
                // if($row->ProductID == 1){
                //     $CPO = $row->RemainingVolume;
                // }
                // if($row->ProductID == 2){
                //     $PKO = $row->RemainingVolume;
                // }
            }
        }

      
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        $where = [
            "d.DeliveryStatusID" => '4'
        ];
            
        $getFFB = $this->db->select_sum('e.TotalCapacity')
                    ->join('ktv_tc_supplychain_delivery d', 'd.DeliveryID = b.DeliveryID','left')
                    ->join('ktv_tc_supplychain_transaction_detail e', 'e.DeliveryDetailID = b.DeliveryID','left')
                    ->join('ktv_tc_processing f', 'f.GenerateDelivery = e.DeliveryDetailID','left')
                    ->where($where)
                    ->where("f.GenerateDelivery IS NULL", NULL, TRUE)
                    ->where("d.SupplyDestMillOrgID",@$SupplychainID )
                    ->get('ktv_tc_supplychain_delivery_detail b')
                    ->result();

        if($getFFB){
            foreach ($getFFB as $key => $value) {
                $TotalCapacity = $value->TotalCapacity;
            }
        }
        
        $CPORemaining = 0;

        $sqlRemainingCpo = $this->db->query(
            "SELECT
                SUM(a.RemainingVolume) AS RemainingVolumeCpo
            FROM
                ktc_tc_processing_product a
                LEFT JOIN ktv_tc_processing c ON c.ProcessingID = a.ProcessingID
            WHERE
                a.StatusCode = 'active'
                $supply
                AND 
                c.StatusGenerate IS NOT NULL
                AND 
                a.ProductID = '1'
            AND
                a.RemainingVolume > 0
            ");
        
        $getRemainingCpo = $sqlRemainingCpo->result_array();
        
        if(!empty($getRemainingCpo)){
            foreach ($getRemainingCpo as $key => $value) {
                if($value['RemainingVolumeCpo'] !=''){
                    $CPORemaining = $value['RemainingVolumeCpo'];
                } else {
                    $CPORemaining = 0;
                }
            }
        }

        $sqlRemainingPk = $this->db->query(
            "SELECT
                SUM(a.RemainingVolume) AS RemainingVolumePk
            FROM
                ktc_tc_processing_product a
                LEFT JOIN ktv_tc_processing c ON c.ProcessingID = a.ProcessingID
            WHERE
                a.StatusCode = 'active'
                $supply
                AND 
                c.StatusGenerate IS NOT NULL
                AND 
                a.ProductID = '2'
            AND
                a.RemainingVolume > 0
            ");
        
        $getRemainingPk = $sqlRemainingPk->result_array();
        
        if(!empty($getRemainingPk)){
            foreach ($getRemainingPk as $key => $value) {
                if($value['RemainingVolumePk'] !=''){
                    $PkRemaining = $value['RemainingVolumePk'];
                } else {
                    $PkRemaining = 0;
                }
            }
        }

        $_SESSION["PKO_REMAINING"] = $PkRemaining;
        $_SESSION["CPO_REMAINING"] = $CPORemaining;
        $_SESSION["ProductPercentageCpo"] = $ProductPercentageCpo;
        $_SESSION["ProductPercentagePk"] = $ProductPercentagePk;
        $_SESSION["flagPk"] = $flagPk;
        $_SESSION["flagCpo"] = $flagCpo;
        $_SESSION["TotalCapacity"] = $TotalCapacity;

        $whereOer   = [
            'b.SupplychainID' => $_SESSION['SupplychainID'],
            'a.PartnerID'     => $_SESSION['PartnerID']
        ];

        if ($_SESSION['role'] == "Mill"){
            unset($whereOer['a.PartnerID']);
        } else {
            unset($whereOer['b.SupplychainID']);
        }

        $getHaveOer = $this->db->join('ktv_tc_supplychain_org b', 'b.ObjID = a.MillID', 'left')
                               ->where('b.ObjType', 'mill')
                               ->where('b.StatusCode', 'active')
                               ->where($whereOer)
                               ->get('ktv_mill a')->row()->HaveOer;

        $_SESSION["HaveOer"] = $getHaveOer;

        return array(
            'data'                 => $query->result_array(),
            'total'                => $query->num_rows(),
            'CPO'                  => $CPORemaining,
            'PKO'                  => $PKORemaining,
            'ProductPercentageCpo' => $ProductPercentageCpo,
            'ProductPercentagePk'  => $ProductPercentagePk,
            'HaveOer'              => $getHaveOer,
            'TotalCapacity'        => $TotalCapacity
        );
    }

    public function getBatchHaveNotOer($ProductID){
        $finalData      = [];
        $checkDataExist = 0;

        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        if($ProductID !=''){
            $sql = $this->db->query("SELECT
                ktsd.SupplyDestMillOrgID AS SupplychainID,
                ktsd.DeliveryID,
                ktp.ProcessingID,
                ktp.ProcessingNumber,
                ktr.status
            FROM
                ktv_tc_supplychain_delivery ktsd
            LEFT JOIN
                ktv_tc_processing ktp ON ktp.SupplychainID = ktsd.SupplyDestMillOrgID
            LEFT JOIN
                ktv_tc_reception ktr ON ktr.SupplychainID = ktsd.SupplyDestMillOrgID
            WHERE
                ktsd.DeliveryStatusID = '4'
            AND
                ktsd.StatusCode = 'active'
            AND
                (ktsd.SupplyDestMillOrgID IS NOT NULL AND ktsd.SupplyDestMillOrgID <> 0)
            AND 
                ktsd.SupplyDestMillOrgID = '$SupplychainID'
            ORDER BY ktp.ProcessingNumber DESC");
                
            $chekData = $sql->result_array();

            if(!empty($chekData)){
                    
                $ProcessingNumber = $chekData[0]['ProcessingNumber'];
                
                $id = $ProcessingNumber; 

                if($ProductID == '2'){
                    $sqlPk = $this->db->query(
                        "SELECT 
                            a.StatusGenerate,
                            a.ProcessingNumber,
                            a.GenerateFrom,
                            a.GenerateDelivery,
                            d.DestpatchStatusID
                        FROM 
                            ktv_tc_processing a
                        LEFT JOIN 
                            ktc_tc_processing_product b ON b.ProcessingID = a.ProcessingID
                        LEFT JOIN 
                            ktv_tc_despatch_detail c ON c.ProcessingProductID = b.ProcessingProductID
                        LEFT JOIN 
                            ktv_tc_despatch d ON d.ProcessingID = c.ProcessingID
                        WHERE
                            ProcessingNumber = '$id' AND StatusGenerate = '1'
                        AND
                            GenerateFrom = 'CPO'");
                    
                    $chekPk = $sqlPk->result_array();

                    if(!empty($chekPk)){
                        foreach($chekPk as $key => $data){
                            if($data['DestpatchStatusID'] == null){
                                $updatereception = array(
                                    'GenerateDelivery'=> $data['GenerateDelivery']
                                );
                                $this->UpdateReceptionGenerated($updatereception);
                            } else {
                                $getDataNew = $this->db->select_sum('b.TotalCapacity')
                                ->join('ktv_tc_supplychain_transaction_detail b', 'b.DeliveryDetailID = a.ReceiptDeliveryID',"left")
                                ->where("a.SupplychainID",@$SupplychainID)
                                ->where("a.Status !=", "1")
                                ->order_by("b.TransDetailID","DESC")
                                ->get('ktv_tc_reception a')
                                ->result();
                            }
                        }
                    } else {
                        $getDataNew = $this->db->select_sum('b.TotalCapacity')
                        ->join('ktv_tc_supplychain_transaction_detail b', 'b.DeliveryDetailID = a.ReceiptDeliveryID',"left")
                        ->where("a.SupplychainID",@$SupplychainID)
                        ->where("a.Status !=", "1")
                        ->order_by("b.TransDetailID","DESC")
                        ->get('ktv_tc_reception a')
                        ->result();
                    }
                } else {
                    $sqlcpo = $this->db->query(
                        "SELECT 
                            a.StatusGenerate,
                            a.ProcessingNumber,
                            a.GenerateFrom,
                            a.GenerateDelivery,
                            d.DestpatchStatusID
                        FROM 
                            ktv_tc_processing a
                        LEFT JOIN 
                            ktc_tc_processing_product b ON b.ProcessingID = a.ProcessingID
                        LEFT JOIN 
                            ktv_tc_despatch_detail c ON c.ProcessingProductID = b.ProcessingProductID
                        LEFT JOIN 
                            ktv_tc_despatch d ON d.ProcessingID = c.ProcessingID
                        WHERE
                            ProcessingNumber = '$id' AND StatusGenerate = '1'
                        AND
                            GenerateFrom = 'PK'");
                    
                    $checkCpo = $sqlcpo->result_array();
                    if(!empty($checkCpo)){
                        foreach($checkCpo as $key => $data){
                            if($data['DestpatchStatusID'] == null){
                                $updatereception = array(
                                    'GenerateDelivery'=> $data['GenerateDelivery']
                                );
                                $this->UpdateReceptionGenerated($updatereception);
                            } else {
                                $getDataNew = $this->db->select_sum('b.TotalCapacity')
                                ->join('ktv_tc_supplychain_transaction_detail b', 'b.DeliveryDetailID = a.ReceiptDeliveryID',"left")
                                ->where("a.SupplychainID",@$SupplychainID)
                                ->where("a.Status !=", "1")
                                ->order_by("b.TransDetailID","DESC")
                                ->get('ktv_tc_reception a')
                                ->result();
                            }
                        }
                    } else {
                        $getDataNew = $this->db->select_sum('b.TotalCapacity')
                        ->join('ktv_tc_supplychain_transaction_detail b', 'b.DeliveryDetailID = a.ReceiptDeliveryID',"left")
                        ->where("a.SupplychainID",@$SupplychainID)
                        ->where("a.Status !=", "1")
                        ->order_by("b.TransDetailID","DESC")
                        ->get('ktv_tc_reception a')
                        ->result();
                    }
                }
            }
        } 

        $getDataNew = $this->db->select_sum('b.TotalCapacity')
                        ->join('ktv_tc_supplychain_transaction_detail b', 'b.DeliveryDetailID = a.ReceiptDeliveryID',"left")
                        ->where("a.SupplychainID",@$SupplychainID)
                        ->where("a.Status !=", "1")
                        ->order_by("b.TransDetailID","DESC")
                        ->get('ktv_tc_reception a')
                        ->result();
        
        $getDataProduct = $this->db->select('d.ProductName, a.ProductID')
                        ->join('ref_tc_processing_product d', 'a.ProductID = d.ProductID')
                        ->where('a.SupplychainID', @$SupplychainID)
                        ->where("a.StartDate <=", date('Y-m-d'))
                        ->where("a.EndDate >=", date('Y-m-d'))
                        ->where("a.StatusCode", 'active')
                        ->get('ktv_tc_supplychain_product a')
                        ->result();

        if (!empty($ProductID)) {
            foreach ($getDataProduct as $key => $value) {
                if ((int) $value->ProductID != (int) $ProductID) {
                    unset($getDataProduct[$key]);
                }
            }
        }

        if (!empty($getDataNew[0]->TotalCapacity)) {
            $checkDataExist = 1;

            foreach ($getDataProduct as $key => $value) {
                $data = [
                    'date'          => date('Y-m-d'),
                    'nett'          => $getDataNew[0]->TotalCapacity,
                    'flag'          => $value->ProductName,
                    'setProduction' => ""
                ];
                
                array_push($finalData, $data);
            }
        }
        
        $_SESSION["checkDataExist"] = $checkDataExist;

        return array('data' => $finalData);
           
    }

    public function UpdateReceptionGenerated($updatereception){
        
        $update['Status'] = '0';
        $this->db->where('Status','1');
        $this->db->where_in('ReceiptDeliveryID', $updatereception);
        $this->db->update('ktv_tc_reception', $update);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        // exit;
        $return['data'] = array(
            'ReceiptDeliveryID' => @$updatereception,
        );
    }

    public function ProductType($ProductID){

        $where = "";
        if (!empty($ProductID))
            $where = "AND ProductID = {$ProductID}";

        $sql = "SELECT
            ProductID id
            , ProductName label
        FROM
            `ref_tc_processing_product`
        WHERE
            StatusCode = 'active'
        $where";


        $query = $this->db->query($sql)->result_array();

        foreach ($query as $key => $value) {
            if (trim($value['label']) == "Food Oil") {
                unset($query[$key]);
            }
        }

        return $query;
    }
    
    public function submit_process($params){
        $result = false;
        $error = 'error';
        $insid = 0; 
        $data=array();
        foreach ($params as $key => $value) { 
            $data[$key] = $value;
        }
        
        try{
            $this->db->trans_begin(); 
            
             
            $insert = array(
                'ProcessingID' => @$data['TransactionID'],
                'ProcessID' => @$data['ProcessID'],
                'ProcessingDate' => @$data['ProcessingDate'],
                'HangingHouseReference' => @$data['HangingHouseReference'], 
                'ProductionLotNumber' => @$data['ProductionLotNumber'],
                'Quantity' => @$data['Quantity'],
                'StuffingDate' => @$data['StuffingDate'],
                'COAReference' => @$data['COAReference'],
                'PONumber' => @$data['PONumber'] 
            );  
             
            $Cektrans = $this->db->query("SELECT * FROM ktv_transaction_processing_process WHERE ProcessingProcessID=?", array($data['ProcessingProcessID'])); 
            
            if($Cektrans->num_rows() > 0 ){ 
                $insert['DateUpdated'] = date('Y-m-d H:i:s');
                $insert['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                
                $this->db->where('ProcessingProcessID', $data['ProcessingProcessID']);
                $this->db->update('ktv_transaction_processing_process', $insert);
                $ProcessingProcessID = $data['ProcessingProcessID'];
            }else{   
                $insert['DateCreated'] = date('Y-m-d H:i:s'); 
                $insert['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_transaction_processing_process', $insert); 
                $ProcessingProcessID = $this->db->insert_id();
            }
              
              
            if($this->db->trans_status() == false){
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        //$this->db->trans_complete();

        if($result) {
            return array('success' => $result , 'ProcessingProcessID' => $ProcessingProcessID );
        }else{
            return array('success' => $result, 'error' => $error);
        }
    } 
    
    
    public function save_pick($data){
        $result = false;
        $insid = 0;
        $error = '';

        try{
            
            $this->db->trans_begin();
            $this->db->insert('ktv_tc_despatch_detail',array( 
                "ProcessingID"          =>  @$data['ProcessingID'],
                "ProcessingProductID"   =>  @$data['ProcessingProductID'],
                "DespatchVolume"        =>  @$data['PickedQty'],
                "ProductID"             =>  @$data['ProductID'],
                'StatusCode'            => 'active',
                'DateCreated'           => date('Y-m-d H:i:s'),
                'DateUpdated'           => date('Y-m-d H:i:s'),
                'CreatedBy'             => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1
            ));
            $insid = $this->db->insert_id();
           
            //update remaining akhir tabel  
            $this->db->where('ProcessingProductID', $data['ProcessingProductID']);
            $p = $this->db->update('ktc_tc_processing_product', array('RemainingVolume' => $data['Remaining']));

            $updateDespatchForm = $this->db->where('ProcessingID', $data['ProcessingID'])
                                           ->update('ktv_tc_despatch', ['ProductID' => $data['ProductID']]);
             
            if (($this->db->trans_status() == false)) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
                $result = false;
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        //$this->db->trans_complete();

        if($result) {
            return array('success' => $result, 'id' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed','error' => $error);
        }
    }
    
    public function del_vehicle($ProcessingProductID){
        if($ProcessingProductID !=''){
            $update['StatusCode'] = 'nullified';
            $update['DateUpdated'] = date('Y-m-d H:i:s');
            $update['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
            $this->db->where('ProcessingProductID', $ProcessingProductID);
            $this->db->update('ktc_tc_processing_product', $update);
            return true;
        }
        return array();
    }
    
    public function del_pick($DespatchDetailID){
        
        if($DespatchDetailID !=''){
            //Balikan data remaining terlebih dahulu
            $pick_remain = $this->db->query("SELECT DespatchVolume, ProcessingProductID FROM ktv_tc_despatch_detail WHERE DespatchDetailID=?", array($DespatchDetailID));
            if($pick_remain->num_rows()>0){
                $row_pick   = $pick_remain->row();
                $trans_remain = $this->db->query("SELECT RemainingVolume FROM ktc_tc_processing_product WHERE ProcessingProductID =? ", array($row_pick->ProcessingProductID))->row();

                
                $pick = $row_pick->DespatchVolume == '' ? 0 : $row_pick->DespatchVolume ;
                $old_remain = @$trans_remain->RemainingVolume == '' ? 0 : $trans_remain->RemainingVolume;
                $addRollBack_remain = $pick + $old_remain;
                $insert['RemainingVolume'] = $addRollBack_remain; 
            
                $this->db->where('ProcessingProductID', $row_pick->ProcessingProductID);
                $p = $this->db->update('ktc_tc_processing_product', $insert); 
                //echo $this->db->last_query();die;
                if($p){
                    //Delete selanjutnya
                    $this->db->where('DespatchDetailID', $DespatchDetailID);
                    $this->db->delete('ktv_tc_despatch_detail');
                }
                return true;
            }
            
            //echo $this->db->last_query();die;
        }
        return array();
    }

    public function OwnerStatus(){
        $query = [
            [
                'OwnerID'    => "1",
                'OwnerName'  => 'Internal'
            ],
            [
                'OwnerID'    => "2",
                'OwnerName'  => 'Eksternal'
            ],
        ];

        return $query;
    }

    public function list_transit($ProductID){

        $where = "";
        if (!empty($ProductID)) {
            if ((int) $ProductID == 1) {
                $KCPRole = "bulking";
            } else {
                $KCPRole = "kcp";
            }

            $where = " AND vso.ObjType = '$KCPRole'";
        }

        $sql = "SELECT
                vso.SupplychainID id
                , vso.`Alias` label
            FROM
                `ktv_tc_supplychain_org_rel` orel
            LEFT JOIN
                view_tc_supplychain_org vso on vso.SupplychainID = orel.ParentID
            WHERE
                orel.ChildID = ?
            AND
                orel.StatusCode = 'active'
            AND
                DATE(NOW()) BETWEEN orel.StartDate AND orel.EndDate
            $where";
        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        $result["data"]     = $query->result_array();
        $result["total"]    = $query->num_rows();
        $result["sql"]    = $this->db->last_query();

        return $result;
    }

    public function list_product(){

        $sql = "SELECT
                    ProductID AS id,
                    ProductName AS label
                FROM
                    ref_tc_processing_product
                WHERE StatusCode='active'";
        $query = $this->db->query($sql);
        $result["data"]     = $query->result_array();

        return $result;
    }
}
?>
