<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf Sutana 
 */
class m_webpengiriman extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
    }
    public function get_data_pengiriman($SupplychainID,$type=""){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $PID = $this->input->get('PID');

        $data = array('data' => array(), 'total' => 0);

        /* pencarian */ 
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');
        $where = "";
 
        if($SupplyStatus){
            $where .= "AND a.SupplyBatchStatus = '$SupplyStatus'";
        }

        if($SupplyKey){
            $where .= "AND IFNULL(a.SupplyDestMillOtherName,`b`.`Name`) like '%$SupplyKey%'";
        }

		if($this->input->get('SBID')){
            $SBID = $this->input->get('SBID');
			$where .= " AND a.SupplyBatchID =  '$SBID'";
        }
        
        if($type!="export_excel"){
            $limit = "LIMIT $start, $limit";
        }


        $sql = "SELECT
                    a.`SupplyBatchID`,
                    a.`SupplyBatchDate`,
                    a.`SupplyOrgID`,
                    IFNULL(a.`SupplyDestMillOrgID`,a.`SupplyDestOrgID`) Mill,
                    a.`SupplyDestMillOtherName`,
                    IFNULL(a.SupplyDestMillOtherName,`b`.`Name`) AS SupplyDestOrgName,
                    IF(
                        ((a.`SupplyDestProcessType` IS NOT NULL AND a.`SupplyDestProcessType` <> 0) OR (a.`SupplyDestDoOrgID` IS NOT NULL AND a.`SupplyDestDoOrgID` <> 0)) AND (a.`SupplyDestOrgID` IS NOT NULL OR a.`SupplyDestMillOrgID` IS NOT NULL), 'do', 'mill'
                    ) SupplyType,
                    a.`SupplyDestDoOrgID`,
                    a.`SupplyDestProcessType`,
                    a.`SMESPCodeID`,
                    a.`SupplyBatchNumber`,
                    a.`SupplyBatchStatus`,
                    DATE(a.`DeliveryDate`) DeliveryDate,
                    a.`DestPO`,
                    a.`DestWeight`,
                    a.`DestNumberPackage`,
                    a.`DestDriver`,
                    a.`DestTransportID`,
                    a.`DestTransportNumber`,
                    a.`DestContainerNumber`,
                    a.`Notes`,
                    a.`ChangeLog`,
                    a.`ChangeBy`,
                    a.`StatusCode`,
                    a.`DateCreated`,
                    a.`CreatedBy`,
                    a.`DateUpdated`,
                    a.`LastModifiedBy`
                FROM
                    ktv_tc_supplychain_batch a
                LEFT JOIN
                    view_tc_supplychain_org b on a.SupplyDestOrgID=b.SupplychainID
                WHERE
                    a.StatusCode = 'active'
                AND
                    a.SupplyOrgID = ?
                $where
                ORDER BY a.SupplyBatchID DESC
                $limit
        ";

        $Q = $this->db->query($sql,array($SupplychainID));

        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }

    public function get_data_delivery($SupplychainID,$type=""){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $PID = $this->input->get('PID');

        $data = array('data' => array(), 'total' => 0);

        /* pencarian */ 
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');
        $where = "";
 
        if($SupplyStatus){
            $where .= "AND a.SupplyBatchStatus = '$SupplyStatus'";
        }

        // if($SupplyKey){
        //     $where .= "AND IFNULL(a.SupplyDestMillOtherName,`b`.`Name`) like '%$SupplyKey%'";
        // }

		if($this->input->get('SBID')){
            $SBID = $this->input->get('SBID');
			$where .= " AND a.SupplyBatchID =  '$SBID'";
        }
        
        if($type!="export_excel"){
            $limit = "LIMIT $start, $limit";
        }
        
        $sql = "SELECT
                    ktsd.DeliveryID
                    ,ktsd.SupplyChainID
                    ,ktsd.ExternalCode
                    ,ktsd.DeliveryNumber
                    ,ktsd.DeliveryDate
                    ,ktsd.TotalWeight
                    ,CASE
                    WHEN ktsd.DeliveryStatusID = 1 THEN 'Open'
                    WHEN ktsd.DeliveryStatusID = 2 THEN 'Close'
                    WHEN ktsd.DeliveryStatusID = 3 THEN 'Sent'
                    WHEN ktsd.DeliveryStatusID = 4 THEN 'Delivered'
                    WHEN ktsd.DeliveryStatusID = 5 THEN 'Finish'
                    ELSE 'Received'
                    END AS Status
                    ,ktsd.ArrivalEstimation
                    ,ktsd.DateCreated
                    ,a.SupplyBatchID
                    ,a.SupplyBatchNumber
                FROM
                    ktv_tc_supplychain_delivery ktsd
                LEFT JOIN
                    ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID
                LEFT JOIN 
                    ktv_tc_supplychain_batch a ON a.SupplyBatchID = ktsdd.SupplyBatchID
                WHERE
                    ktsd.StatusCode = 'active'
                AND
                    ktsd.SupplychainID = ?
                $where
                OR
                    ktsd.SupplyDestDoOrgID = ?
                GROUP BY
                    ktsd.DeliveryID
                ORDER BY 
                    ktsd.DeliveryID DESC
                $limit
        ";

        $Q = $this->db->query($sql,array($SupplychainID,$SupplychainID));

        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }

    public function get_data_transaction(){
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
		
        $data = array('data' => array(), 'total' => 0); 
        $this->db->select('
                            a.`SupplyTransID`,
                            a.`SupplychainID`,
                            a.`SupplyBatchID`,
                            a.`TransNumber`,
                            a.`InvoiceNumber`,
                            a.`DateTransaction`,
                            a.`SupplyType`,
                            IFNULL(e.ObjID, a.`SupplyID`) SupplyID,
                            a.`PlantationNr`,
                            a.`VolumeBruto`,
                            a.`VolumeNetto`,
                            a.`VolumeCutting`,
                            a.`PackageID`,
                            a.`PackageNumber`,
                            a.`PackageWeight`,
                            a.`DetailTypeID`,
                            a.`TransStatusID`,
                            a.ContractPrice,
                            a.NetPrice,
                            a.DiscountPrice,
                            a.TotalPayment,
                            a.`Notes`,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`,
                            IF(b.isCertified != "", cp.CertProgName,"Not Certified") Certified,
                            IF(
                                b.MemberName IS NULL OR b.MemberName = "",
                                mem.Name,
                                b.MemberName
                            ) SupplierName,
                            a.AgentOtherSurvey,
                            a.AgentOther,
                            IF(b.isCertified = 1,"Yes","No")  Certified,
                            c.PackageType
                        ', false);

                            //--x.Status,
                            //--x.SupplyUserID,

        //$this->db->from('ktv_tc_supplychain_transaction_pengiriman x');
        $this->db->from('ktv_tc_supplychain_transaction a');
        $this->db->join('ktv_members b',"a.SupplyID=b.MemberID AND a.SupplyType != 'Nonfarmer'", 'left');
        $this->db->join('ktv_ref_certification_program cp', 'cp.CertProgID = b.isCertified', 'left');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		$this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'", 'left');
		$this->db->join('view_tc_supplychain_org e', 'e.SupplychainID=d.SupplyOrgID', 'left');
		$this->db->join('view_tc_supplychain_org mem', 'mem.SupplychainID = a.SupplyID', 'left');
        $this->db->where('a.StatusCode', 'active');
        //$this->db->where('x.Status', $status);
	 
		if($this->input->get('SBID')!=''){
			$this->db->where('a.SupplyBatchID', $this->input->get('SBID'));
		} 
        $this->db->order_by('a.TransNumber', 'DESC');
        $this->db->limit($limit, $start);
        $Q = $this->db->get();
        //echo $this->db->last_query();die;
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

        return $data;
    }
    public function get_do(){
        $SID    = $this->input->get('MillID');
        $sql = "SELECT
            c.SupplychainID id,
            c.`Name` label
            FROM
                view_tc_supplychain_org a
            LEFT JOIN
                ktv_tc_supplychain_org_rel b on b.ParentID = a.SupplychainID
            LEFT JOIN
                view_tc_supplychain_org c on c.SupplychainID = b.ChildID
            WHERE
                a.SupplychainID = ?
        ";

        $query = $this->db->query($sql,array($SID));

        return array(
            "success"=>true,
            "message"=>"Data Berhasil Ditampilkan",
            "total" =>$query->num_rows(),
            "data" => $query->result()
        );
    }
    public function get_sp_code(){
        $SID    = $this->input->get('SID');
        $MillID = $this->input->get('MillID');
        $type   = $this->input->get('type');

        if($type == "mill"){
            $sql = "
                SELECT
                    b.SMESPCodeID id
                    , c.SuratNr as label
                    , c.Note
                    , d.MillID
                    , d.MillName
                FROM
                        ktv_tc_supplychain_org a
                LEFT JOIN
                        ktv_sme_sp_code b on b.MemberID = a.ObjID
                LEFT JOIN
                        ktv_mill_sp_code c on c.SPCodeID = b.SPCodeID
                LEFT JOIN
                        ktv_mill d on d.MillID = c.MillID
                LEFT JOIN
                    ktv_tc_supplychain_org org2 on org2.ObjID = c.MillID AND org2.ObjType = 'mill'
                WHERE
                a.SupplychainID = ?
                AND org2.SupplychainID = ?
                AND NOW() BETWEEN b.DateStart AND b.DateEnd
                OR a.SupplychainID = ? AND DATE(b.DateEnd) = DATE(NOW())
                ORDER BY c.SuratNr ASC
            ";

            $query = $this->db->query($sql,array($SID,$MillID,$SID));
        }else{
            $sql = "
            SELECT
                b.SMESPCodeID id
                , c.SuratNr label
                FROM
                        ktv_tc_supplychain_org a
                LEFT JOIN
                        ktv_sme_sp_code b on b.MemberID = a.ObjID
                LEFT JOIN
                        ktv_mill_sp_code c on c.SPCodeID = b.SPCodeID
                LEFT JOIN
                        ktv_mill d on d.MillID = c.MillID
                LEFT JOIN
                    ktv_tc_supplychain_org org2 on org2.ObjID = c.MillID AND org2.ObjType = 'mill'
                WHERE
                        a.SupplychainID = ?
                        AND org2.SupplychainID = ?
                AND NOW() BETWEEN b.DateStart AND b.DateEnd
                OR a.SupplychainID = ? AND DATE(b.DateEnd) = DATE(NOW())
                UNION
                SELECT
                    ssp.SMESPCodeID id,
                    msp.SuratNr label
                FROM
                    ktv_tc_supplychain_org a
                    LEFT JOIN `ktv_tc_supplychain_org_rel` b ON a.SupplyChainID = b.ChildID
                    LEFT JOIN `ktv_tc_supplychain_org` a1 ON a1.SupplyChainID = b.ParentID
                    LEFT JOIN ktv_members m ON m.MemberID = a1.ObjID
                    LEFT JOIN `ktv_sme_sp_code` ssp ON ssp.MemberID = m.MemberID
                    LEFT JOIN `ktv_mill_sp_code` msp ON msp.SPCodeID = ssp.SPCodeID 
                WHERE
                    a.SupplyChainID = ?
                ORDER BY label DESC
            ";

            $query = $this->db->query($sql,array($SID,$MillID,$SID,$SID));
        }
        
        return array(
            "success"=>true,
            "message"=>"Data Berhasil Ditampilkan",
            "total" =>$query->num_rows(),
            "data" => $query->result()
        );
    }
    public function get_data_transaction_window(){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        $PID = $this->input->get('PID');
        $SID = $this->input->get('SID');
        $STID = $this->input->get('STID');
        $SBID = $this->input->get('SBID');

        $data = array('data' => array(), 'total' => 0);

        $where = array('Status' => 2, 'SupplyUserID' => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1); 

        $getTemp = array();

        // ini di komen dulu tabel nya gak nemu
        /*
        $x = $this->db->select('SupplyTransID')
                ->from('ktv_tc_supplychain_transaction_pengiriman')
                ->where($where)
                ->get();
	   echo $this->db->last_query(); die;
        if($x->num_rows()){        
            foreach($x->result_array() as $k => $v){
                $getTemp[] = $v['SupplyTransID'];
            } 
            $this->db->where_not_in('a.SupplyTransID', $getTemp);
        }
        */

        // $this->db->select('
        //                     a.`SupplyTransID`,
        //                     a.`SupplychainID`,
        //                     a.`SupplyBatchID`,
        //                     a.`TransNumber`,
        //                     a.`InvoiceNumber`,
        //                     a.`DateTransaction`,
        //                     a.`SupplyType`,
        //                     IFNULL(e.ObjID, a.`SupplyID`) SupplyID,
        //                     a.`PlantationNr`,
        //                     a.`VolumeBruto`,
        //                     a.`VolumeNetto`,
        //                     a.`VolumeCutting`,
        //                     a.`PackageID`,
        //                     a.`PackageNumber`,
        //                     a.`PackageWeight`,
        //                     a.`DetailTypeID`,
        //                     a.`TransStatusID`,
        //                     a.`FarmingTypeID`,
        //                     a.ContractPrice,
        //                     a.NetPrice,
        //                     a.DiscountPrice,
        //                     a.TotalPayment,
        //                     a.`Notes`,
        //                     a.`ChangeLog`,
        //                     a.`ChangeBy`,
        //                     a.`DateCreated`,
        //                     a.`CreatedBy`,
        //                     a.`DateUpdated`,
        //                     a.`LastModifiedBy`,
        //                     IFNULL(e.Name,b.MemberName) as SupplierName,
        //                     c.PackageType
        //                 ', false);
        // $this->db->from('ktv_tc_supplychain_transaction a');
        // $this->db->join('ktv_members b', 'a.SupplyID=b.MemberID', 'left');
        // $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		// $this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'", 'left');
		// $this->db->join('view_tc_supplychain_org e', 'e.SupplychainID=d.SupplyOrgID', 'left');
        // $this->db->where('a.StatusCode', 'active');
        // $this->db->where('a.SupplychainID', $SID);

        if(@$SBID == 0){
            // $this->db->or_where('a.SupplyBatchID IS NULL');
        }
        $where = "";
        if($STID){
            $where .= "AND SupplyTransID = '$STID'";
        }
        //echo $this->db->last_query();die;

        $sql = "SELECT
                    a.`SupplyTransID`,
                    a.`SupplychainID`,
                    a.`SupplyBatchID`,
                    a.`TransNumber`,
                    a.`InvoiceNumber`,
                    a.`DateTransaction`,
                    a.`SupplyType`,
                    IFNULL( e.ObjID, a.`SupplyID` ) SupplyID,
                    a.`PlantationNr`,
                    a.`VolumeBruto`,
                    a.`VolumeNetto`,
                    a.`VolumeCutting`,
                    a.`PackageID`,
                    a.`PackageNumber`,
                    a.`PackageWeight`,
                    a.`DetailTypeID`,
                    a.`TransStatusID`,
                    a.`FarmingTypeID`,
                    a.ContractPrice,
                    a.NetPrice,
                    a.DiscountPrice,
                    a.TotalPayment,
                    a.`Notes`,
                    a.`ChangeLog`,
                    a.`ChangeBy`,
                    a.`DateCreated`,
                    a.`CreatedBy`,
                    a.`DateUpdated`,
                    a.`LastModifiedBy`,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        mem.Name,
                        b.MemberName
                    ) SupplierName,
                    a.MillID,
                    IF(b.isCertified != '', cp.CertProgName,'Not Certified') Certified,
                    c.PackageType 
                FROM
                    ( `ktv_tc_supplychain_transaction` a )
                    LEFT JOIN ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Nonfarmer'
                    LEFT JOIN ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                    LEFT JOIN `ktv_trace_package` c ON `a`.`PackageID` = `c`.`PackageID`
                    LEFT JOIN `ktv_tc_supplychain_batch` d ON `d`.`SupplyBatchID` = `a`.`SupplyID` 
                    AND a.SupplyType = 'Batch'
                    LEFT JOIN `view_tc_supplychain_org` e ON `e`.`SupplychainID` = `d`.`SupplyOrgID`
                    LEFT JOIN
                        view_tc_supplychain_org mem on mem.SupplychainID = a.SupplyID
                WHERE
                    `a`.`StatusCode` = 'active' 
                    AND `a`.`SupplychainID` = ? 
                    AND a.SupplyType <> 'Batch'
                    $where
                ORDER BY
                    `a`.`SupplyTransID` DESC 
                    LIMIT $start,$limit";
        $Q = $this->db->query($sql,array($SID));
                   
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->FarmingTypeName = @$val->FarmingTypeID != '' ? $this->getFarming(@$val->FarmingTypeID) : '';
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }
    public function delete_transaction($STID, $SBID){
        $result = false;
        $error = 'error';
		 
        try{
            $this->db->trans_begin();
 
			//echo $this->db->last_query();die;
			// Rollback SupllyBatchID ke NULL
			$this->db->where('SupplyTransID', $STID);
			$this->db->update('ktv_tc_supplychain_transaction', array('SupplyBatchID' => NULL));
			
			$this->db->where('SupplyTransID', $STID);
			$this->db->where('SupplyBatchID', $SBID);
            $this->db->where('Status', 2);
            $this->db->where('SupplyUserID', array_key_exists('userid', $_SESSION) ? $_SESSION['userid']:1);
            $this->db->delete('ktv_tc_supplychain_transaction_pengiriman');
			
            if ($this->db->trans_status() == false) {
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

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result, 'error' => $error);
        }
    }
    private function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }
    public function submit_transaction($param){
        $result = false;
        $error = 'error';

        try{
            $this->db->trans_begin();
            $content = array(
                'SupplyTransID' => $param['STID'],
				'SupplyBatchID' => $param['SBID'],
                'SupplyUserID' => array_key_exists('userid', $_SESSION) ? $_SESSION['userid']:1,
                'Status' => 2
            );
            $this->db->insert('ktv_tc_supplychain_transaction_pengiriman', $content);
			//echo $this->db->last_query();die;
            if($param['SBID']){
                $this->db->where('SupplyTransID', $param['STID']);
                $this->db->update('ktv_tc_supplychain_transaction', array('SupplyBatchID' => $param['SBID']));  
            }
			
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                //$error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result, 'error' => $error);
        }
    }
    public function submit($params){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';

        $data=array();
        foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Transaction_FormPengiriman-Form-", '', $key);
            if($value == "") $value = null;
            $data[$keyNew] = $value;
        }

        try{
            $this->db->trans_begin();

            if($data['SupplyDestType']=='do' && $data['SupplyDestProcessType']=='do'){
                $SupplyDestOrgID = $data['DO'];
            }else{
                $SupplyDestOrgID = $data['Mill'];
            }

            $content = array(
                "SupplyBatchDate" => date('Y-m-d H:i:s'),
                "SupplyOrgID" => $this->input->request_headers()['Sid'],
                "SupplyDestOrgID" => $SupplyDestOrgID,
                "SupplyBatchStatus" => 'Open',
                "DeliveryDate" => @$data['DeliveryDate'],
                "DestPO" => @$data['DestPO'],
                "DestWeight" => @$this->replacestr($data['DestWeight']),
                "DestNumberPackage" => @$this->replacestr($data['DestNumberPackage']),
                "DestDriver" => @$data['DestDriver'],
                "DestTransportID" => @$data['DestTransportID'],
                "DestTransportNumber" => @$data['DestTransportNumber'],
				"DestContainerNumber" => @$data['DestContainerNumber'],
                "StatusCode" => 'active'
            );
            $content['SupplyDestMillOrgID'] = (isset($data["Mill"])) ? $data["Mill"] : null;
            $content['SupplyDestMillOtherName'] = (isset($data["OtherMillName"])) ? $data["OtherMillName"] : null;
            $content['SMESPCodeID'] = (isset($data["SPB"])) ? $data["SPB"] : null;
            $content['SupplyDestDoOrgID'] = (isset($data["DO"])) ? $data["DO"] : null;
            $content['SupplyDestProcessType'] = (isset($data["SupplyDestProcessType"])) ? $data["SupplyDestProcessType"] : null;
			 
            if(@$this->input->request_headers()['Sbid']){  
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->where('SupplyBatchID', $this->input->request_headers()['Sbid']);
                $this->db->update('ktv_tc_supplychain_batch', $content);
				//echo $this->db->last_query();die;
                $insid = $this->input->request_headers()['Sbid'];
			    $bath_number = $data['SupplyBatchNumber'];
            }else{
                $content["SupplyBatchNumber"] = generateBatchTraceabilityNumber($this->input->request_headers()['Sid']);
                $content['DateCreated'] = date('Y-m-d H:i:s');
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_batch', $content);
				//echo $this->db->last_query();die;
                $insid = $this->db->insert_id();
				$bath_number = $content['SupplyBatchNumber'];
            }
			/*
			// Update status pengiriman temporary
			$getTemp = array();
			$x = $this->db->select('SupplyTransID,SupplyBatchID')
					->from('ktv_tc_supplychain_transaction')
					->where('SupplyBatchID', $insid)
					->get();

			if($x->num_rows()){        
				foreach($x->result_array() as $k => $v){
					$this->db->where('SupplyBatchID', $v['SupplyBatchID']);
					$this->db->where('SupplyTransID', $v['SupplyTransID']);
					$this->db->where('SupplyUserID', array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1);
					$this->db->update('ktv_tc_supplychain_transaction_pengiriman', array('Status' => 1));
				}
			}
			*/
            /***********************************************/
			
            if ($this->db->trans_status() == false) {
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

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result, 'SBID' => $insid, 'SupplyBatchNumber' => $bath_number);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
	
	function replacestr($form)
	{
		return str_replace(',','',$form);
	}
	
    public function get_relation($SID,$ObjType){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('b.*')
          ->from('ktv_tc_supplychain_org_rel a ')
          ->join('view_tc_supplychain_org b', 'b.SupplychainID=a.ParentID', 'left')
          ->where('ChildID', $SID)
          ->where('b.ObjType', $ObjType)
          ->where('a.StatusCode', 'active')
          ->get();

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
    private function getFarming($id=''){
        $Q = $this->db->get_where('ref_tc_farming_type', array('FarmingTypeID' => $id));

        if($Q->num_rows()){
            return $Q->row()->FarmingTypeName;
        }else{
            return '';
        }
    }
    public function close_batch(){
        $result = false;
        try{
            $this->db->trans_begin();

            $this->db->where('SupplyBatchID', $this->input->post('SBID'));
            $this->db->update('ktv_tc_supplychain_batch', array('SupplyBatchStatus' => 'Closed'));
			
			/*
			// Update status pengiriman temporary
			$getTemp = array();
			$x = $this->db->select('SupplyBatchID')
					->from('ktv_tc_supplychain_transaction')
					->where('SupplyBatchID', $this->input->post('SBID'))
					->get();

			if($x->num_rows()){        
				foreach($x->result_array() as $k => $v){
					$this->db->where('SupplyBatchID', $v['SupplyBatchID']);
					$this->db->where('SupplyUserID', array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1);
					$this->db->update('ktv_tc_supplychain_transaction_pengiriman', array('Status' => 1));
				}
			}
			*/
            /***********************************************/
			
            if ($this->db->trans_status() == false) {
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

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result);
        }
    }
    public function sent_batch(){
        $result = false;
        try{
            $this->db->trans_begin();

            $this->db->where('SupplyBatchID', $this->input->post('SBID'));
            $this->db->update('ktv_tc_supplychain_batch', array('SupplyBatchStatus' => 'Sent'));

            if ($this->db->trans_status() == false) {
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

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result);
        }
    }
	
	function  getTransaksi($SupplyBatchID,$SID)
	{
		$SQL ="SELECT  
				a.SupplyBatchDate, 
				( SELECT  Name from view_tc_supplychain_org where  SupplychainID = ? limit 1) as PengirimName,
				( 
					
					SELECT District 
					from ktv_district A, view_tc_supplychain_org B
					WHERE SUBSTR(B.VillageID,1,4) = A.DistrictID
					AND B.SupplychainID = ?
				)
				as DistrictPengirim,				
				b.Name as SupplyDestOrgName,
				b.Address  SupplyDestOrgAddress,
				a.SupplyBatchNumber, 
				a.SupplyBatchStatus, 
				a.DeliveryDate, 
				a.DestPO, 
				a.DestWeight, 
				a.DestNumberPackage, 
                d.VolumeBruto,
                d.VolumeNetto,
                d.PackageNumber,
				a.DestDriver as Driver,  
				a.DestTransportNumber as PlatNomor, 
				a.DestContainerNumber,
                c.DestTransportName,
				a.Notes, 
				a.ChangeLog, 
				a.ChangeBy, 
				a.StatusCode, 
				a.DateCreated, 
				a.CreatedBy,
				 a.DateUpdated, 
				 a.LastModifiedBy
				FROM (ktv_tc_supplychain_batch a)
				LEFT JOIN view_tc_supplychain_org b ON a.SupplyDestOrgID=b.SupplychainID
                LEFT JOIN ref_tc_transport c ON a.DestTransportID=c.DestTransportID
                LEFT JOIN ktv_tc_supplychain_transaction d ON d.SupplychainID=a.SupplyOrgID
				WHERE a.StatusCode =  'active'
				AND a.SupplyBatchID = ? ";
				 
		$t = $this->db->query($SQL, array($SID,$SID, $SupplyBatchID));
		//echo $this->db->last_query();die;
		return $t->row_array();
    }

    function getDeliveyOrder($DeliveryID, $SID)
	{
        $getDeliveryProcessing = $this->db->select('SupplyDestDoOrgID')
                                ->where('SupplyDestProcessType', 'mill')
                                ->where('DeliveryID', $DeliveryID)
                                ->where('SupplychainID', $SID)
                                ->or_where('SupplyDestDoOrgID', $SID)
                                ->group_by('SupplychainID')
                                ->get('ktv_tc_supplychain_delivery')
                                ->row();
        
        $proccess['SupplyDestDoOrgID'] = (array) $getDeliveryProcessing;
        
        $SQL = "SELECT 
                    a.*, 
                    b.Name AS DestinationMill,
                    (SELECT 
                        b.Name AS DestinationDo
                    FROM 
                        ktv_tc_supplychain_delivery a 
                    LEFT JOIN 
                        view_tc_supplychain_org b ON a.SupplyDestDoOrgID = b.SupplychainID 
                    WHERE 
                        a.SupplyDestDoOrgID = '$getDeliveryProcessing->SupplyDestDoOrgID'
                    GROUP BY 
                        a.SupplyDestDoOrgID
                    )
                    AS DestinationDo,				
                    c.SupplyBatchID,
                    d.SupplyBatchNumber,
                    d.SupplyBatchDate,
                    e.DestTransportName,
                    f.Name,
                    g.VolumeNetto,
                    g.VolumeBruto,
                    h.Name AS DestinationOtherMill
                FROM 
                    ktv_tc_supplychain_delivery a 
                LEFT JOIN 
                    view_tc_supplychain_org b ON a.SupplyDestMillOrgID = b.SupplychainID 
                LEFT JOIN 
                    ktv_tc_supplychain_delivery_detail c ON c.DeliveryID = a.DeliveryID
                LEFT JOIN 
                    ktv_tc_supplychain_batch d ON d.SupplyBatchID = c.SupplyBatchID
                LEFT JOIN 
                    ref_tc_transport e ON e.DestTransportID = a.DestTransportID
                LEFT JOIN 
                    view_tc_supplychain_org f ON f.SupplychainID = a.SupplychainID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction g ON g.SupplychainID = f.SupplychainID
                LEFT JOIN 
                    view_tc_supplychain_org h ON h.SupplychainID = a.SupplyDestMillOtherName
                WHERE 
                    a.DeliveryID = ? 
                GROUP BY 
                    a.SupplychainID";
		$t = $this->db->query($SQL, array($DeliveryID));
        // echo $this->db->last_query();die;
		
		return $t->row_array();
    }
    
    public function get__pengiriman($SID){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $PID = $this->input->get('PID');

        /* pencarian */ 
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');
 
        if($SupplyStatus){
            $this->db->where('a.SupplyBatchStatus', $SupplyStatus);
        }

        if($SupplyKey){
            $this->db->like('b.`Name`', $SupplyKey);
        }

        $data = array('data' => array(), 'total' => 0);
        $this->db->select(' a.`SupplyBatchID`,
                            a.`SupplyBatchDate`,
                            a.`SupplyOrgID`,
                            a.`SupplyDestOrgID`,
                            b.`Name` as SupplyDestOrgName,
                            a.`SupplyBatchNumber`,
                            a.`SupplyBatchStatus`,
                            a.`DeliveryDate`,
                            a.`DestPO`,
                            a.`DestWeight`,
                            a.`DestNumberPackage`,
                            a.`DestDriver`,
                            a.`DestTransportID`,
                            a.`DestTransportNumber`,
							a.DestContainerNumber,
                            a.`Notes`,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`StatusCode`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`');
        $this->db->from('ktv_tc_supplychain_batch a');
        $this->db->join('view_tc_supplychain_org b', 'a.SupplyDestOrgID=b.SupplychainID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplyOrgID', $SID);
		if($this->input->get('SBID')){
			$this->db->where('a.SupplyBatchID', $this->input->get('SBID'));
		}
        $this->db->order_by('a.SupplyBatchID', 'DESC');
        $this->db->limit($limit, $start);

		
        $Q = $this->db->get();

        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }

    public function get_data_excel_transaction_detail($DeliveryID,$type=""){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $PID = $this->input->get('PID');

        $data = array('data' => array(), 'total' => 0);

        if($type!="export_excel"){
            $limit = "LIMIT $start, $limit";
        }
       
        $sql = "SELECT
                    d.`SupplyTransID`, 
                    d.`SupplychainID`, 
                    d.`SupplyBatchID` AS SupplyStorageID , 
                    d.`TransNumber`, 
                    d.`InvoiceNumber`, 
                    d.`DateTransaction`, 
                    d.`SupplyType`, 
                    IFNULL(mem.ObjID, d.`SupplyID`) AS SupplyID, 
                    IFNULL(e.MemberDisplayID, d.`SupplyID`) AS SupplierID,
                    c.SupplyBatchNumber AS SupplyStorageNumber,
                    d.`PlantationNr`, 
                    CASE
                        WHEN d.DeductionPercentage IS NULL THEN '-'
                        ELSE d.DeductionPercentage
                    END AS DeductionPercentage,
                    d.`VolumeBruto`, 
                    d.`VolumeNetto`,
                    d.ContractPrice, 
                    d.TotalPayment, 
                    CASE
                        WHEN d.TransStatusID IS NULL THEN '-'
                        ELSE d.TransStatusID
                    END AS TransStatusID,
                    d.TotalPayment, 
                    CASE
                        WHEN d.Notes IS NULL THEN '-'
                        ELSE d.Notes
                    END AS Notes,
                    CASE
                        WHEN d.ChangeBy IS NULL THEN '-'
                        ELSE d.ChangeBy
                    END AS ChangeBy,
                    d.`DateCreated`, 
                    d.`CreatedBy`, 
                    d.`DateUpdated`, 
                    d.`LastModifiedBy`, 
                    IF(e.isCertified != '', cp.CertProgName, Not 'Certified') AS Certified,
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
                ) AS SupplierName, 
                IF(e.isCertified = 1, 'Yes', 'No') AS Certified
                FROM 
                    `ktv_tc_supplychain_delivery` a 
                LEFT JOIN `ktv_tc_supplychain_delivery_detail` b ON `a`.`DeliveryID`=`b`.`DeliveryID`
                LEFT JOIN `ktv_tc_supplychain_batch` c ON `c`.`SupplyBatchID`=`b`.`SupplyBatchID`
                LEFT JOIN `ktv_tc_supplychain_transaction` d ON `d`.`SupplyBatchID`=`c`.`SupplyBatchID`
                LEFT JOIN `ktv_members` e ON `e`.`MemberID`=`d`.`SupplyID` AND d.SupplyType != 'Batch' 
                LEFT JOIN `view_tc_supplychain_org` mem ON `mem`.`SupplychainID` = `a`.`SupplychainID`
                LEFT JOIN `ktv_ref_certification_program` cp ON `cp`.`CertProgID` = `e`.`isCertified`
                LEFT JOIN `view_tc_supplychain_org` vso3 ON `vso3`.`SupplychainID` = `d`.`SupplyID` AND d.SupplyType = 'NonFarmer'
                LEFT JOIN `view_tc_supplychain_staff` vss ON `vss`.`SupplychainID` = `vso3`.`SupplychainID`
                LEFT JOIN `ktv_members_extension` kms on `kms`.`MemberID` = `vso3`.`ObjID`
                WHERE
                    a.StatusCode = 'active'
                AND
                    a.DeliveryID = '$DeliveryID'
                ORDER BY d.TransNumber DESC
                $limit
        ";

        $Q = $this->db->query($sql,array($DeliveryID));
        
        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }

    public function get_data_excel_transaction_reception_detail($DeliveryID,$type=""){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $PID = $this->input->get('PID');

        $data = array('data' => array(), 'total' => 0);

        if($type!="export_excel"){
            $limit = "LIMIT $start, $limit";
        }
        
        $sql = "SELECT
                    d.`SupplyTransID`, 
                    d.`SupplychainID`, 
                    d.`SupplyBatchID`, 
                    d.`TransNumber`, 
                    d.`InvoiceNumber`, 
                    d.`DateTransaction`, 
                    d.`SupplyType`, 
                    IFNULL(mem.ObjID, d.`SupplyID`) AS SupplyID, 
                    IFNULL(e.MemberDisplayID, d.`supplyID`) AS SupplierID, 
                    c.SupplyBatchNumber,
                    d.`PlantationNr`, 
                    CASE
                        WHEN d.TransStatusID IS NULL THEN '-'
                        ELSE d.TransStatusID
                    END AS TransStatusID,
                    CASE
                        WHEN d.Notes IS NULL THEN '-'
                        ELSE d.Notes
                    END AS Notes,
                    CASE
                        WHEN d.ChangeBy IS NULL THEN '-'
                        ELSE d.ChangeBy
                    END AS ChangeBy,
                    d.`DateCreated`, 
                    d.`DateUpdated`, 
                    IF(e.isCertified != '', cp.CertProgName, Not 'Certified') AS Certified,
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
                ) AS SupplierName, 
                IF(e.isCertified = 1, 'Yes', 'No') AS Certified
                FROM 
                    `ktv_tc_supplychain_delivery` a 
                LEFT JOIN `ktv_tc_supplychain_delivery_detail` b ON `a`.`DeliveryID`=`b`.`DeliveryID`
                LEFT JOIN `ktv_tc_supplychain_batch` c ON `c`.`SupplyBatchID`=`b`.`SupplyBatchID`
                LEFT JOIN `ktv_tc_supplychain_transaction` d ON `d`.`SupplyBatchID`=`c`.`SupplyBatchID`
                LEFT JOIN `ktv_members` e ON `e`.`MemberID`=`d`.`SupplyID` AND d.SupplyType != 'Batch'
                LEFT JOIN `view_tc_supplychain_org` mem ON `mem`.`SupplychainID` = `a`.`SupplychainID`
                LEFT JOIN `ktv_ref_certification_program` cp ON `cp`.`CertProgID` = `e`.`isCertified`
                LEFT JOIN `view_tc_supplychain_org` vso3 ON `vso3`.`SupplychainID` = `d`.`SupplyID` AND d.SupplyType = 'NonFarmer'
                LEFT JOIN `ktv_members_extension` kms ON `kms`.`MemberID` = `vso3`.`ObjID`
                WHERE
                    a.StatusCode = 'active'
                AND
                    a.DeliveryID = '$DeliveryID'
                ORDER BY d.TransNumber DESC
                $limit
        ";

        $Q = $this->db->query($sql,array($DeliveryID));
        
        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }
}

?>
