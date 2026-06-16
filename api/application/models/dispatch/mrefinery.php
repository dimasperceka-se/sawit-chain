<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mrefinery extends CI_Model {
    protected $_table = 'ktv_receipts';

    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function grid_product_list($ShippingDate = null){
        $sql ="SELECT
                sp.ProductPercentage
                , de.ReceptionVolume
                , de.ProductName OilType
                , pp2.ProductID
                , pp2.ProductName
                , (de.ReceptionVolume * sp.ProductPercentage)/100 ProductVolume
            FROM
                ktv_tc_supplychain_product sp
            LEFT JOIN
                (		
                    SELECT
                        tr.SupplychainID,
                        b.ProductID,
                        b.ProductName,
                        SUM(a.DespatchVolume) ReceptionVolume
                    FROM
                        `ktv_tc_despatch_detail` a
                        LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                        LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                        LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                        LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                        INNER JOIN ktv_tc_reception tr on tr.DespatchID = a.DespatchID
                    WHERE
                        dp.ShippingDate = ?
                    AND 
                        dp.DestinationID = ?
                    GROUP BY
                        b.ProductID
                ) de on de.ProductName = sp.OilType AND de.SupplychainID = sp.SupplychainID
            LEFT JOIN
                ref_tc_processing_product pp2 on pp2.ProductID = sp.ProductID
            WHERE
                sp.SupplychainID = ?
            AND
                sp.StatusCode = 'active'
            AND
                NOW() BETWEEN sp.StartDate AND sp.EndDate";

        $query = $this->db->query($sql,array($ShippingDate,$_SESSION["SupplychainID"],$_SESSION["SupplychainID"]));
        // echo '<pre>'.$this->db->last_query();die;
        $tmproduct  = 0;
        $total      = 0;
        $ProductVolume = 0;  
        $result = array();
        if($query->num_rows()>0){
            foreach($query->result() as $num => $row){
                $result[$num]["ProductName"]        = $row->ProductName;
                $result[$num]["OilType"]            = $row->OilType;
                $result[$num]["ProductPercentage"]  = $row->ProductPercentage;
                $result[$num]["ProductNetto"]       = number_format($row->ProductVolume,2);
            }
        }

        return array(
            'data'      => $result,
            'total'     => $total,
            'sql'       => $this->db->last_query()
        );
    }

    public function close_dispatch($params){
        $data=array();
        foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Reception_FormPenerimaan-Form-", '', $key);
            if($value == "") $value = null;
            $data[$keyNew] = $value;
        }
        
        $dataPost["SupplychainID"]      = $_SESSION["SupplychainID"];
        $dataPost["ReceptionNumber"]    = time();
        $dataPost["ReceptionDate"]      = $data["ReceptionDate"];
        $dataPost["DespatchID"] 	    = $data["DespatchID"];
        $dataPost["StatusCode"] 	    = "active";
        $dataPost["CreatedBy"] 	        = $_SESSION["userid"];
        $dataPost["DateCreated"] 	    = date("Y-m-d");

        $insert = $this->db->insert("ktv_tc_reception",$dataPost);

        $ReceptionID =  $this->db->insert_id();

        $sql = "SELECT
                a.DespatchDetailID
                , a.ProductID
                , a.DespatchVolume
            FROM
                `ktv_tc_despatch_detail` a
            WHERE
                a.DespatchID = ?";
        $query = $this->db->query($sql,array($data["DespatchID"]));

        if($query->num_rows()>0){
            $dataPostDetail = array();
            foreach($query->result() as $key => $row){
                $dataPostDetail[$key]["ReceptionID"] = $ReceptionID;
                $dataPostDetail[$key]["DespatchDetailID"] = $row->DespatchDetailID;
                $dataPostDetail[$key]["ProductID"] = $row->ProductID;
                $dataPostDetail[$key]["ReceptionVolume"] = $row->DespatchVolume;
                $dataPostDetail[$key]["RemainingVolume"] = 0;
                $dataPostDetail[$key]["StatusCode"] = 'active';
                $dataPostDetail[$key]["CreatedBy"] = $_SESSION["userid"];
                $dataPostDetail[$key]["DateCreated"] = date("Y-m-d");
            }
            $insert = $this->db->insert_batch("ktv_tc_reception_detail",$dataPostDetail);
        }

        return $insert;
    }

    public function grid_dispatch_list($ShippingDate = null){
        $sql = "SELECT
                    dp.DespatchID,
                    dp.DespatchNumber,
                    vso.`Name` AS CompanyName,
                    b.ProductName,
                    a.DespatchVolume AS DespatchVolume
                    , dp.ShippingDate
                FROM
                    `ktv_tc_despatch_detail` a
                    LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                    LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                    LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                    LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                    LEFT JOIN ktv_tc_reception tr on tr.DespatchID = a.DespatchID
                WHERE
                dp.ShippingDate = ?
                AND
                dp.DestinationID = ?
                AND 
                dp.DestpatchStatusID = '5'
                ";
        $query = $this->db->query($sql,array($ShippingDate, $_SESSION["SupplychainID"]));
        
        return array(
            'data'      => $query->result_array(),
            'total'     => $query->num_rows(),
            'sql'       => $this->db->last_query()
        );
    }

    public function get_data_edit($ShippingDate, $SID){
        /*$sql ="SELECT
                td.DespatchID,
                td.DespatchNumber,
                vso.`Name`,
                td.PackingDate,
                td.DespatchCode,
                td.ShippingDate,
                tr.ReceptionDate,
                sum(tdd.DespatchVolume) AS DespatchVolume,
            IF
                ( tr.DespatchID IS NULL, 'Sent', 'Received' ) AS STATUS 
            FROM
                ktv_tc_despatch td
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = td.SupplychainID
                LEFT JOIN ktv_tc_reception tr ON tr.DespatchID = td.DespatchID
                LEFT JOIN ktv_tc_despatch_detail tdd ON tdd.DespatchID = td.DespatchID
                LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = td.DestinationID 
            WHERE
                td.ShippingDate = ?
                AND
                td.DestinationID = ?
                ";*/

            $sql="SELECT 
                    dt.DespatchID
                    , dt.DespatchNumber
                    , dt.Name
                    , dt.PackingDate
                    , dt.ShippingDate
                    , dt.DespatchCode
                    , max(dt.ReceptionDate) AS ReceptionDate
                    , dt.DespatchVolume AS DespatchVolume
                    , dt.STATUS
                FROM(
                    SELECT
                        td.DespatchID,
                        td.DespatchNumber,
                        vso.`Name`,
                        td.PackingDate,
                        td.DespatchCode,
                        td.ShippingDate,
                        tr.ReceptionDate,
                        SUM(tdd.DespatchVolume) AS DespatchVolume,
                    IF
                        ( tr.DespatchID IS NULL, 'Sent', 'Received' ) AS STATUS
                    FROM
                        ktv_tc_despatch td
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = td.SupplychainID
                        LEFT JOIN ktv_tc_reception tr ON tr.DespatchID = td.DespatchID
                        LEFT JOIN ktv_tc_despatch_detail tdd ON tdd.DespatchID = td.DespatchID
                        LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = td.DestinationID 
                    WHERE
                        td.ShippingDate = ? 
                        AND td.DestinationID = ?
                        AND td.DestpatchStatusID = '5'
                        GROUP BY td.ShippingDate
                ) dt";

        $query = $this->db->query($sql,array($ShippingDate,$SID));
        // echo '<pre>'.$this->db->last_query();die;
        $data['data']  = $query->result_array();
        return $data;
    }

    public function get_data_dispatch($SID, $PID, $pSearch, $start = 0, $limit = 50,$sortingField=null, $sortingDir=null){
    
        $sqlFilter = "";
        if($SID != ''){
            $sqlFilter .= " AND td.DestinationID = '$SID'";
        }else{
            $sqlFilter .= " AND vso2.PartnerID = '$_SESSION[PartnerID]'";
        }

        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= "  AND vso.`Name` LIKE '%{$pSearch['textSearch']}%' OR td.DespatchNumber = '{$pSearch['textSearch']}'";
        }

        if ($pSearch['statusSearch'] != "") {
            $sqlFilter .= " AND IF(tr.DespatchID IS NULL, 'Sent', IF(tr.DespatchID IS NOT NULL, 'Received', 'Sent')) = '{$pSearch['statusSearch']}'";
        }

        if ($sortingField == "")
            $sortingField = "dt.DespatchNumber";
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        /*$sql = "SELECT
            td.DespatchID
            , td.DespatchNumber
            , vso.`Name`
            , td.PackingDate
            , td.ShippingDate
            , tr.ReceptionDate
            , SUM(tdd.DespatchVolume) AS DespatchVolume
            , IF(tr.DespatchID IS NULL, 'Sent', 'Received') AS Status
            , td.DestinationID
        FROM
            ktv_tc_despatch td
        LEFT JOIN
            view_tc_supplychain_org vso on vso.SupplychainID = td.SupplychainID
        LEFT JOIN
            ktv_tc_reception tr on tr.DespatchID = td.DespatchID
        LEFT JOIN
            ktv_tc_despatch_detail tdd on tdd.DespatchID = td.DespatchID
        LEFT JOIN
            view_tc_supplychain_org vso2 on vso2.SupplychainID = td.DestinationID
        WHERE
            1=1
            AND td.StatusCode = 'active'
            AND td.DestpatchStatusID IN ('4','5')
            AND td.DestinationID = '$SID'
        GROUP BY td.ShippingDate
        ORDER BY $sortingField $sortingDir LIMIT ?, ?";*/
        $sql = "SELECT 
                DespatchID
                , DespatchNumber
                , Name
                , PackingDate
                , ShippingDate
                , max(ReceptionDate) AS ReceptionDate
                , DespatchVolume AS DespatchVolume
                , Status
                , CASE
                    WHEN Status = 'Sent' THEN '" . lang('Sent') . "'
                    WHEN Status = 'Received' THEN '" . lang('Received') . "'
                    ELSE '-'
                END AS Status
            FROM(
                SELECT
                    td.DespatchID,
                    td.DespatchNumber,
                    vso.`Name`,
                    td.PackingDate,
                    td.ShippingDate,
                    tr.ReceptionDate,
                    sum(tdd.DespatchVolume) AS DespatchVolume,
                IF
                    ( tr.DespatchID IS NULL, 'Sent', 'Received' ) AS Status,
                    td.DestinationID 
                FROM
                    ktv_tc_despatch td
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = td.SupplychainID
                    LEFT JOIN ktv_tc_reception tr ON tr.DespatchID = td.DespatchID
                    LEFT JOIN ktv_tc_despatch_detail tdd ON tdd.DespatchID = td.DespatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = td.DestinationID 
                WHERE
                    1 = 1 
                    AND td.StatusCode = 'active' 
                    AND td.DestpatchStatusID = '5'
                    AND td.DestinationID = '$SID' 
                    $sqlFilter
                    GROUP BY td.ShippingDate
            ) dt
            GROUP BY
                dt.ShippingDate 
            ORDER BY $sortingField $sortingDir 
            LIMIT ?, ?";
        $params = array((int)$start, (int)$limit);
        $query = $this->db->query($sql,$params);
        // echo '<pre>'.$this->db->last_query();die;
        
        $result = array("data"=>$query->result_array(),"total"=>$query->num_rows(), "sql" => $this->db->last_query());

        return $result;
    }

    // public function getReceptionList($get){
    public function getReceptionList(){
        // $sqlFilter = "";
        // if($_SESSION["SupplychainID"] != ''){
        //     $sqlFilter .= " AND tr.SupplychainID = '$_SESSION[SupplychainID]'";
        // }else{
        //     $sqlFilter .= " AND vso2.PartnerID = '$_SESSION[PartnerID]'";
        // }

        // if ($get['searchstatus'] != "" AND $get['searchstatus'] != 'null') {
        //     $sqlFilter .= " AND IF(tr.DespatchID IS NULL, 'Sent', IF(tr.DespatchID IS NOT NULL, 'Received', 'Sent')) = '{$get['searchstatus']}'";
        // }

        if ($sortingField == "")
            $sortingField = "td.DespatchNumber";
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        $sql = "SELECT
                    tr.ReceptionNumber,
                    DATE(tr.ReceptionDate) AS ReceptionDate,
                    dp.DespatchNumber,
                    vso.`Name` AS CompanyName,
                    b.ProductName,
                    SUM(a.DespatchVolume) AS ReceptionVolume,
                    dp.ShippingDate 
                FROM
                    `ktv_tc_despatch_detail` a
                    LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                    LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                    LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                    LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID
                    LEFT JOIN ktv_tc_reception tr ON tr.DespatchID = a.DespatchID 
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = tr.SupplychainID
                WHERE
                    1=1
                -- $sqlFilter
                GROUP BY dp.ShippingDate
                ORDER BY
                    dp.DespatchNumber ASC";
        $params = array((int)$start, (int)$limit);
        $query = $this->db->query($sql,$params);

        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }

    public function getReceptionListDetail($ShippingDate,$SID){

        $sql = "SELECT
                tr.ReceptionNumber
                ,dp.DespatchNumber
                ,vso.name AS CompanyName
                ,vso3.name AS Dealer
                ,b.ProductName
                ,SUM(a.DespatchVolume) AS ReceptionVolume
                ,dp.ShippingDate
                ,ktsd.DeliveryDate AS DateSelling
                ,ktst.DateTransaction AS DateBuying
                ,ktsb.SupplyBatchNumber
                ,IF(
                km.MemberName IS NULL OR km.MemberName = '',
                IF(
                    kml.MillName IS NULL OR kml.MillName = '',
                    IF(
                        ktst.MillOther IS NULL OR ktst.MillOther = '',
                        IF(
                            mem.Name IS NULL OR mem.Name = '',
                            IF(
                                ktst.DOOther IS NULL OR ktst.DOOther = '',
                                IF(
                                    ktst.AgentOther IS NULL OR ktst.AgentOther = '',
                                    'Nonfarmer',
                                    ktst.AgentOther
                                ),
                                ktst.DOOther
                            ),
                            mem.Name
                        ),
                        ktst.MillOther
                    ),
                    kml.MillName
                ),
                km.MemberName
            ) AS SupplierName
            FROM
                ktv_tc_despatch_detail a
            LEFT JOIN 
                ref_tc_processing_product b ON b.ProductID = a.ProductID
            LEFT JOIN 
                ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
            LEFT JOIN 
                ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
            LEFT JOIN 
                ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
            LEFT JOIN 
                view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID
            LEFT JOIN 
                ktv_tc_reception tr ON tr.DespatchID = a.DespatchID 
            LEFT JOIN 
                view_tc_supplychain_org vso2 ON vso2.SupplychainID = tr.SupplychainID
            LEFT JOIN 
                ktv_tc_supplychain_delivery ktsd ON ktsd.SupplyDestMIllorgID = tp.supplychainid
            LEFT JOIN 
                view_tc_supplychain_org vso3 ON vso3.SupplychainID = IFNULL(ktsd.SupplyDestDoOrgID,ktsd.SupplychainID)
            LEFT JOIN  
                ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID 
            LEFT JOIN 
                ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID
            LEFT JOIN 
                ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID
            LEFT JOIN
                ktv_members km ON km.MemberID = ktst.SupplyID
            LEFT JOIN
                ktv_mill kml ON kml.MillID = ktst.MillID
            LEFT JOIN 
                view_tc_supplychain_org mem ON mem.SupplychainID = ktst.SupplyID
            WHERE
                1=1
            AND
                dp.DestinationID = '$SID'
            AND
                dp.ShippingDate = '$ShippingDate'
            AND 
                dp.DestpatchStatusID = '5'
            AND 
                ktsd.DeliveryStatusID = '4'
            GROUP BY 
                ktst.TransNumber
            ORDER BY
                dp.DespatchNumber ASC";
            $params = array((int)$start, (int)$limit);

            $query = $this->db->query($sql,$params);
            
            if($query->num_rows()>0){
                return $query->result_array();
            } else {
            return false;
        }
    }

    public function getViewReceptionDetail($ShippingDate, $SID){

        $sql = "SELECT
                    tr.ReceptionNumber
                    ,dp.DestpatchStatusID
                    ,dp.DespatchNumber
                    ,vso.name AS CompanyName
                    ,vso3.name AS Dealer
                    ,b.ProductName
                    ,SUM(a.DespatchVolume) AS ReceptionVolume
                    ,dp.ShippingDate
                    ,tr.SupplychainID
                    ,ktsd.SupplyDestMIllorgID
                    ,ktsd.DeliveryDate AS DateSelling
                    ,ktst.DateTransaction AS DateBuying
                    ,ktsb.SupplyBatchNumber
                    ,ktst.SupplyType
                    ,IF(
                    km.MemberName IS NULL OR km.MemberName = '',
                    IF(
                        kml.MillName IS NULL OR kml.MillName = '',
                        IF(
                            ktst.MillOther IS NULL OR ktst.MillOther = '',
                            IF(
                                mem.Name IS NULL OR mem.Name = '',
                                IF(
                                    ktst.DOOther IS NULL OR ktst.DOOther = '',
                                    IF(
                                        ktst.AgentOther IS NULL OR ktst.AgentOther = '',
                                        'Nonfarmer',
                                        ktst.AgentOther
                                    ),
                                    ktst.DOOther
                                ),
                                mem.Name
                            ),
                            ktst.MillOther
                        ),
                        kml.MillName
                    ),
                    km.MemberName
                ) AS SupplierName
                FROM
                    ktv_tc_despatch_detail a
                LEFT JOIN 
                    ref_tc_processing_product b ON b.ProductID = a.ProductID
                LEFT JOIN 
                    ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                LEFT JOIN 
                    ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                LEFT JOIN 
                    ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                LEFT JOIN 
                    view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID
                LEFT JOIN 
                    ktv_tc_reception tr ON tr.DespatchID = a.DespatchID 
                LEFT JOIN 
                    view_tc_supplychain_org vso2 ON vso2.SupplychainID = tr.SupplychainID
                LEFT JOIN 
                    ktv_tc_supplychain_delivery ktsd ON ktsd.SupplyDestMIllorgID = tp.supplychainid
                LEFT JOIN 
                    view_tc_supplychain_org vso3 ON vso3.SupplychainID = IFNULL(ktsd.SupplyDestDoOrgID,ktsd.SupplychainID)
                LEFT JOIN  
                    ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID 
                LEFT JOIN 
                    ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID
                LEFT JOIN
                    ktv_members km ON km.MemberID = ktst.SupplyID
                LEFT JOIN
                    ktv_mill kml ON kml.MillID = ktst.MillID
                LEFT JOIN 
                    view_tc_supplychain_org mem ON mem.SupplychainID = ktst.SupplyID
                WHERE
                    1=1
                AND
                    dp.DestinationID = '$SID'
                AND
                    dp.ShippingDate = '$ShippingDate'
                AND 
                	dp.DestpatchStatusID = '5'
                AND 
                    ktsd.DeliveryStatusID = '4'
                GROUP BY 
                	ktst.TransNumber
                ORDER BY
                    dp.DespatchNumber ASC";
        $params = array((int)$start, (int)$limit);

        $query = $this->db->query($sql,$params);
        // echo '<pre>'.$this->db->last_query();die;
        
        if($query->num_rows()){
            $result = $query->result();
            
            $data['data'] = $result;
            $data['total'] = $query->num_rows();
            return $data;
        }
    }

    public function getSupplyChainID($UserID){
        
        $sql = "SELECT
                   SupplychainID
                FROM
                    view_tc_supplychain_staff 
                WHERE
                    UserID = '$UserID'
                ";
        $params = array((int)$start, (int)$limit);
        $query = $this->db->query($sql,$params);

        if($query->num_rows()){
            $result = $query->result();
            foreach($query->result() as $sid => $row){
                $result        = $row->SupplychainID;
            }
           
            return $result;
        }
    }
}
?>
