<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mdispatch extends CI_Model {
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
                a.DespatchNumber DispatchNumber
                , a.ShippingDate
                , IFNULL(vso2.`Name`, vso2.Alias ) Mill
                , IFNULL(vso.`Name`, vso.Alias) Destination
                , IFNULL( COUNT( DISTINCT b.DespatchID ), 0 ) ContainerTotal
                , ts.TransactionStatusName DispatchStatusName
                , SUM(dd.DespatchVolume) DispatchVolume
                , a.PackingDate
                , a.DateCreated
            FROM
                `ktv_tc_despatch` a
                LEFT JOIN 
                    ktv_tc_despatch_vehicle b ON b.DespatchID = a.DespatchID
                LEFT JOIN 
                    ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                LEFT JOIN 
                    view_tc_supplychain_org vso on vso.SupplychainID = a.DestinationID
                LEFT JOIN 
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = a.SupplychainID
                LEFT JOIN 
                    ktv_tc_despatch_detail dd on dd.DespatchID = a.DespatchID
            WHERE
                a.SupplychainID = ?
                AND a.StatusCode = 'active'
            GROUP BY
                a.DespatchID
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
                a.DespatchNumber DispatchNumber
                , dd.DespatchVolume DispatchVolume
                , pp.ProductName
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
                LEFT JOIN 
                    ktv_tc_despatch_detail dd on dd.DespatchID = a.DespatchID
                LEFT JOIN
                    ref_tc_processing_product pp on pp.ProductID = dd.ProductID
            WHERE
                a.SupplychainID = ?
                AND a.StatusCode = 'active'
            ORDER BY
                a.DespatchNumber ASC
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
                a.DespatchNumber DispatchNumber
                , IFNULL(vso2.`Name`, vso2.Alias ) Mill
                , IFNULL(vso.`Name`, vso.Alias) Destination
                , b.DeliveryOrderNumber
                , b.DriverName
                , tt.VehicleTypeName
                , b.VehicleNumber
                , b.ContainerNumber
                , a.ShippingDate
                , a.PackingDate
            FROM
                `ktv_tc_despatch` a
                LEFT JOIN 
                    ktv_tc_despatch_vehicle b ON b.DespatchID = a.DespatchID
                LEFT JOIN 
                    ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                LEFT JOIN 
                    view_tc_supplychain_org vso on vso.SupplychainID = a.DestinationID
                LEFT JOIN 
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = a.SupplychainID
                LEFT JOIN
                    ref_tc_despatch_vehicle_type tt on tt.VehicleTypeID = b.DespatchVehicleID
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
            $sortingField = 'a.DespatchID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

         $start = (int) $start;
		 $limit = (int) $limit;
		 $sqlLimit = " LIMIT {$start},{$limit}";
		 
		$sql = "SELECT
                    a.*
                    , IFNULL(COUNT(b.DespatchID),0) AS ContainerTotal
                    , SUM(c.DespatchVolume) AS DespatchVolume
                    , CASE
						WHEN ts.TransactionStatusName = 'Sent' THEN '" . lang('Sent') . "'
						WHEN ts.TransactionStatusName = 'New' THEN '" . lang('New') . "'
                        WHEN ts.TransactionStatusName = 'Complete' THEN '" . lang('Completed') . "'
						ELSE '-'
					END AS DestpatchStatusName
                FROM
                    `ktv_tc_despatch` a
                LEFT JOIN
                    ktv_tc_despatch_vehicle b on b.DespatchID = a.DespatchID
                LEFT JOIN
                    ktv_tc_despatch_detail c on c.DespatchID = a.DespatchID
                LEFT JOIN                    
				    ref_transaction_status ts on ts.TransactionStatusID = a.DestpatchStatusID
                WHERE
                    a.SupplychainID = ?
                AND
                    a.StatusCode = 'active'
                $sqlFilter
                GROUP BY
                    a.DespatchID
                ORDER BY $sortingField $sortingDir
                $sqlLimit 
		"; 
					 
		$query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        $result['data'] = $query->result_array();
        // echo $this->db->last_query();die;
        
        $result['total'] = $query->num_rows();
		return $result;
    }

    public function proses_batch($DespatchID){
        
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
            a.DespatchID = 0
        AND 
            c.DestpatchStatusID = '1'";

        $query = $this->db->query($sql,array($_SESSION["SupplychainID"]));
        
        $CheckDeliveryID = $query->result_array();
        if($CheckDeliveryID){
            foreach ($CheckDeliveryID as $key => $value) {
                $update["DespatchID"]       = $DespatchID;
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
            DespatchID = ?";
        $query = $this->db->query($sql,array($DespatchID));

        if($query->num_rows()>0){
            $update["DestpatchStatusID"] = "4";
            
            $this->db->where("DespatchID",$DespatchID);
            $this->db->update("ktv_tc_despatch",$update);

            return true;
        }

        return false;
    }

    public function sent_batch($DespatchID){
        $sql = "SELECT
            *
        FROM
            ktv_tc_despatch_vehicle
        WHERE
            DespatchID = ?";
        $query = $this->db->query($sql,array($DespatchID));
        
        if($query->num_rows()>0){
            $update["DestpatchStatusID"] = "5";

            $this->db->where("DespatchID",$DespatchID);
            $query = $this->db->update("ktv_tc_despatch",$update);

            if($query){
                $this->insertReception($DespatchID);
            }

            return true;
        }
        return false;
    }

    public function insertReception($DespatchID){
        $sql = "SELECT
                a.DestinationID
                , b.UserID
            FROM
                ktv_tc_despatch a
            LEFT JOIN
                view_tc_supplychain_staff b on b.SupplychainID = a.DestinationID
            WHERE
                a.DespatchID = ?";
        $query = $this->db->query($sql,array($DespatchID));

        if($query->num_rows()>0){
            $rows = $query->row();
            
            $dataPost["SupplychainID"]      = $rows->DestinationID;
            $dataPost["ReceptionNumber"]    = time();
            $dataPost["ReceptionDate"]      = date("Y-m-d");
            $dataPost["DespatchID"] 	    = $DespatchID;
            $dataPost["StatusCode"] 	    = "active";
            $dataPost["CreatedBy"] 	        = $rows->UserID;
            $dataPost["DateCreated"] 	    = date("Y-m-d");

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
                        ktv_tc_despatch d on d.DespatchID = a.DespatchID
                    LEFT JOIN
                        ref_tc_processing_product pp on pp.ProductID= a.ProductID
                    LEFT JOIN
                        ktv_tc_supplychain_product sp on sp.OilType = pp.ProductName AND sp.SupplychainID = d.DestinationID
                    WHERE
                        a.DespatchID = ?";
            $query = $this->db->query($sql,array($DespatchID));

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
	
    public function fetchvehicle($DispatchID, $start, $limit, $sortingField, $sortingDir, $ProductID){
        if ($sortingField == "")
            $sortingField = 'DespatchVehicleID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        $start = (int) $start;
        $limit = (int) $limit;
        $sqlLimit = " LIMIT {$start},{$limit}";
        
        $sql = "SELECT
            a.DespatchVehicleID
            , a.DriverName
            , CASE
                WHEN b.VehicleTypeName = 'Truck' THEN '" . lang('Truck') . "'
                WHEN b.VehicleTypeName = 'Mini Truck' THEN '" . lang('Mini Truck') . "'
                WHEN b.VehicleTypeName = 'Pick Up' THEN '" . lang('Pick Up') . "'
                WHEN b.VehicleTypeName = 'Truck Cold Diesel' THEN '" . lang('Truck Cold Diesel') . "'
                WHEN b.VehicleTypeName = 'Dump Truck' THEN '" . lang('Dump Truck') . "'
                WHEN b.VehicleTypeName = 'Other' THEN '" . lang('Other') . "'
                ELSE '-'
            END AS VehicleTypeName
            , a.DeliveryOrderNumber
            , a.ContainerNumber
            , a.VehicleNumber
            , IFNULL(a.VehicleWeight, '') VehicleWeight
            , IFNULL(c.ProductName, '') ProductName
            , a.ProductID
            , IF(a.OwnerID IS NULL, '', IF(a.OwnerID = 1, 'Internal', 'Eksternal')) OwnerStatusName
            , a.DespatchID
        FROM
            `ktv_tc_despatch_vehicle` a
        LEFT JOIN
            ref_tc_despatch_vehicle_type b on b.VehicleTypeID = a.VehicleTypeID
        LEFT JOIN
            ref_tc_processing_product c on a.ProductID = c.ProductID
        WHERE
            a.DespatchID = ?
        AND
            a.StatusCode = 'active'
        $where
        ORDER BY $sortingField $sortingDir $sqlLimit"; 
                    
        $query = $this->db->query($sql, array( $DispatchID ) );

        $getData = $query->result_array();

        if (!empty($ProductID)) {
            foreach ($getData as $key => $value) {
                if ((int) $value['ProductID'] != (int) $ProductID) {
                    unset($getData[$key]);
                }
            }
        }

        $result['data']  = $getData;
        $result['total'] = count($getData);

        return $result;
     }

    public function fetchvehiclebyID($DispatchID, $DespatchVehicleID){
        $where = [
            'DespatchID'        => $DispatchID,
            'DespatchVehicleID' => $DespatchVehicleID,
            'StatusCode'        => 'active'
        ];

        $this->db->where($where);
        $data = $this->db->get('ktv_tc_despatch_vehicle')->row_array();

        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Dispatch.win.FormWinVehicle-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;

        return $return;
    }
	
	public function fetchpick($DispatchID, $start, $limit, $sortingField, $sortingDir, $ProductID){
        
		if ($sortingField == "")
            $sortingField = 'DespatchDetailID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

         $start = (int) $start;
		 $limit = (int) $limit;
		 $sqlLimit = " LIMIT {$start},{$limit}";
		  
		$sql = "SELECT
            a.DespatchDetailID
            , c.ProcessingNumber
            , b.RemainingVolume
            , a.DespatchVolume
            , pp.ProductName ProductType
            , a.ProductID
        FROM
            ktv_tc_despatch_detail a
        LEFT JOIN
            ktc_tc_processing_product b on b.ProcessingProductID = a.ProcessingProductID
        LEFT JOIN
            ktv_tc_processing c on c.ProcessingID = b.ProcessingID
        LEFT JOIN
            ref_tc_processing_product pp on pp.ProductID = a.ProductID
        WHERE
            a.DespatchID = ?
        AND
            a.StatusCode = 'active'

        ORDER BY $sortingField $sortingDir $sqlLimit"; 
					 
        $query = $this->db->query($sql, array( $DispatchID) );

        $data = array();

        if($query->num_rows()>0){
            $getData = $query->result();

            foreach ($getData as $key => $value) {
                if (!empty($ProductID)) {
                   if ((int) $value->ProductID != (int) $ProductID) {
                       unset($getData[$key]);
                   }
                }
            }

            foreach($getData as $key => $row){
                $data[$key]["DespatchDetailID"] = $row->DespatchDetailID;
                $data[$key]["ProcessingNumber"] = $row->ProcessingNumber;
                $data[$key]["ProductType"]      = $row->ProductType;
                $data[$key]["RemainingVolume"]  = (float)$row->RemainingVolume;
                $data[$key]["DespatchVolume"]   = (float)$row->DespatchVolume;
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
                'DespatchID'            => @$data['DespatchID'],
                'DriverName'            => @$data['DriverName'],
                'VehicleTypeID'         => @$data['VehicleTypeID'], 
                'ContainerNumber'       => @$data['ContainerNumber'],
                'VehicleNumber'         => @$data['VehicleNumber'], 
                'VehicleNote'           => @$data['VehicleNote'],
                'ProductID'             => $data['ProductID'],
                'VehicleWeight'         => (float) $data['VehicleWeight'],
                'OwnerID'               => $data['OwnerID'],
                'StatusCode'            => 'active'
            );
			 
			$Cektrans = $this->db->query("SELECT * FROM ktv_tc_despatch_vehicle WHERE DespatchVehicleID = ?", array($data['DespatchVehicleID'])); 
			
			if($Cektrans->num_rows() > 0 ){ 
				$insert['DateUpdated'] = date('Y-m-d H:i:s');
                $insert['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				
                $this->db->where('DespatchVehicleID', $data['DespatchVehicleID']);
                $this->db->update('ktv_tc_despatch_vehicle', $insert);
            }else{
                $insert['DateCreated'] = date('Y-m-d H:i:s'); 
                $insert["DeliveryOrderNumber"] = $this->generateDoNumber();
                $insert['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_despatch_vehicle', $insert);
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
            ktv_tc_despatch b on b.DespatchID = a.DespatchID
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
        $result = false;
        $error = 'error';
        $insid = 0; 
		$data=array();
		foreach ($params as $key => $value) { 
			$data[$key] = $value;
		}

        $TransitID = ((int) $data['DespatchType'] == 1  ? NULL : $data['TransitID']);

        if (!empty($data['DespatchType'])) {
            if ((int) $data['DespatchType'] == 1) {
                $TransitID = NULL;
            }
        }

        if($data['DestpatchStatusID'] == 'Lengkap' || $data['DestpatchStatusID'] == 'Completed'){
            $DestpatchStatusID = '4';
        } 
        
        try{
            $this->db->trans_begin();
            $insert = array(
                'SupplychainID'     => $_SESSION['SupplychainID'],
                'DespatchCode'      => @$data['DespatchCode'],
                'DestpatchStatusID' => @$DestpatchStatusID, 
                'ShippingDate'      => $data['ShippingDate'],
                'DestinationID'     => @$data['DestinationID'],
                'DestpatchNetto'    => @$data['DestpatchNetto'], 
                'PackingDate'       => @$data['PackingDate'],
                'DespatchNote'      => @$data['DespatchNote'], 
                'ProductID'         => @$data['ProductID'],
                'DespatchType'      => @$data['DespatchType'],
                'TransitID'         => $TransitID,
                'StatusCode'        => 'active',
                'CreatedBy'         => $_SESSION['userid'],
                'DateCreated'       => date("Y-m-d H:i:s")
            );
			 
			$Cektrans = $this->db->query("SELECT * FROM ktv_tc_despatch WHERE DespatchID = ?", array($data['DespatchID'])); 
			
			if($Cektrans->num_rows() > 0 ){ 
				$insert['DateUpdated'] = date('Y-m-d H:i:s');
                $insert['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->where('DespatchID', $data['DespatchID']);
                $this->db->update('ktv_tc_despatch', $insert); 
				$id = $data['DespatchID'];
				$DespatchNumber = $Cektrans->row()->DespatchNumber;
                $status = "update";
            }else{  
				$insert['DespatchNumber'] = $this->getDespatchCode();
				$insert['DateCreated'] = date('Y-m-d H:i:s'); 
                $insert['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_despatch', $insert); 
				$id = $this->db->insert_id();
                $DespatchNumber = $insert['DespatchNumber'];
                $status = "insert";
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
            return array('success' => $result, 'id' => $id, 'DespatchNumber' => $DespatchNumber, 'status'=> $status, 'ProductID' => $data['ProductID'], 'DespatchType' => $data['DespatchType'] );
        }else{
            return array('success' => $result, 'error' => $error);
        }
    } 
	 
    public function delete($id=0){
        if($id){
            $this->db->where('DespatchID', $id);
            $this->db->update('ktv_tc_despatch', ['StatusCode' => 'nullified']);
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
        $SQL ="SELECT
            a.DespatchID
            , a.DespatchNumber
            , a.ShippingDate
            , CASE
                WHEN a.DestpatchStatusID = '1' THEN '" . lang('New') . "'
                WHEN a.DestpatchStatusID = '5' THEN '" . lang('Sent') . "'
                WHEN a.DestpatchStatusID = '4' THEN '" . lang('Completed') . "'
                ELSE '-'
            END AS DestpatchStatusID
            , a.DespatchCode
            , a.DestinationID
            , a.DestpatchNetto
            , a.DespatchNote
	        , a.PackingDate
            , a.DespatchType
            , a.ProductID
            , a.TransitID
        FROM
            ktv_tc_despatch a
        WHERE
            a.DespatchID = ?";
		$query = $this->db->query($SQL, array($id));
		// echo $this->db->last_query();die;
        if($query->num_rows()){
            return  array("success" => true, "data" => $query->row());
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
	
	public function getDespatchCode(){
          $sql = "SELECT 
                    IFNULL((MAX(CAST(SUBSTRING_INDEX( DespatchNumber, '-',- 1 ) AS UNSIGNED)) +1), 1) DespatchNumber
                 FROM ktv_tc_despatch";
        $query = $this->db->query($sql)->row();
        $Code = 'D-'.$this->setNumberDigit($query->DespatchNumber, 6);
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
        // $_SESSION["TotalCapacity"] = $TotalCapacity;

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
                            ktv_tc_despatch d ON d.DespatchID = c.DespatchID
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
                            ktv_tc_despatch d ON d.DespatchID = c.DespatchID
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
				"DespatchID"   		    =>  @$data['DispatchID'],
				"ProcessingProductID"  	=>  @$data['ProcessingProductID'],
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

            $updateDespatchForm = $this->db->where('DespatchID', $data['DispatchID'])
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
	
    public function del_vehicle($DespatchVehicleID){
        if($DespatchVehicleID !=''){
            //Delete selanjutnya
            $this->db->where('DespatchVehicleID', $DespatchVehicleID);
            $this->db->delete('ktv_tc_despatch_vehicle');
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
}
?>
