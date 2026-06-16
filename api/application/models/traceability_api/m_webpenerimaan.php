<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_webpenerimaan extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
    }
    
    public function get_data_trans($SID, $PID, $pSearch, $start = 0, $limit = 50,$sortingField=null, $sortingDir=null){
        date_default_timezone_set('Asia/Jakarta');
        $page = $this->input->get('page');
        $start = (int)$this->input->get('start');
        $limit = (int)$this->input->get('limit');
        $data = array('data' => array(), 'total' => 0);

        if($this->input->get('SBID')){
            $sb1 = ''; $sb2 = '';
        }else{
            $sb1 = '/*'; $sb2 = '*/';
        }

        if ($sortingField == "")
            $sortingField = "sb1.SupplyBatchNumber";
        if ($sortingDir == "")
            $sortingDir = 'DESC';
		
		$SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');

        $sqlFilter = "";

        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= "  AND vso1.Name LIKE '%{$pSearch['textSearch']}%' OR sb1.SupplyBatchNumber = '{$pSearch['textSearch']}'";
        }

        if ($pSearch['statusSearch'] != "") {
            $sqlFilter .= " AND IF(st2.SupplyTransID IS NULL, 'Pending', IF(st2.SupplyBatchID IS NULL, 'Received', 'Sent')) = '{$pSearch['statusSearch']}'";
        }

        if($SID == ''){
            $sqlFilter .= "AND vso1.PartnerID = '$_SESSION[PartnerID]'";
        }else{
            $sqlFilter .= "AND (sb1.SupplyDestOrgID = '$SID' OR sb1.SupplyDestMillOrgID = '$SID' OR sb1.SupplyDestDOOrgID = '$SID')";
        }
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS
					st2.SupplyTransID,
                    sb1.SupplyBatchID,
                    DATE(sb1.SupplyBatchDate) SupplyBatchDate,
                    sb1.SupplyOrgID,
                    sb1.SupplyDestOrgID,
                    sb1.SupplyBatchNumber,
                    DATE(sb1.DeliveryDate) DeliveryDate,
                    sb1.DestPO,
                    IFNULL(sb1.DestWeight,SUM(st1.VolumeNetto)) DestWeight,
                    sb1.DestNumberPackage,
                    sb1.DestDriver,
                    sb1.DestTransportID,
                    sb1.DestTransportNumber,
                    sb1.Notes,
                    sb1.ChangeLog,
                    sb1.ChangeBy,
                    sb1.StatusCode,
                    sb1.DateCreated,
                    sb1.CreatedBy,
                    sb1.DateUpdated,
                    sb1.LastModifiedBy, 
                    SUM(st1.VolumeBruto) Bruto, 
                    SUM(st1.VolumeNetto) Net,
                    st2.PackageNumber,
                    CASE
                        WHEN sb1.SupplyBatchStatus = 'Delivered' THEN 'Received'
                        WHEN sb1.SupplyBatchStatus = 'Sent' THEN 'Pending'
                        ELSE sb1.SupplyBatchStatus
                    END SupplyBatchStatus,
                    vso1.ObjID,
                    kmee.agCompanyName,
                    #vso1.Name as SupplierName,
                    CASE
                        WHEN vso1.ObjType = 'mill' THEN IF(km.CompanyName != '' OR km.CompanyName IS NOT NULL, km.CompanyName, vso1.Name)
                        WHEN vso1.ObjType = 'trader' THEN IFNULL(vso3.Name,IF(kme.agCompanyName != '' OR kme.agCompanyName IS NOT NULL, kme.agCompanyName, vso1.Name))
                        ELSE IFNULL(vso3.Name, vso1.Name)
                    END AS SupplierName
                FROM 
                    ktv_tc_supplychain_batch sb1
                    LEFT JOIN ktv_tc_supplychain_transaction st1 ON st1.SupplyBatchID=sb1.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchID AND st2.SupplyType='Batch'
                    LEFT JOIN view_tc_supplychain_org vso1 ON vso1.SupplychainID=sb1.SupplyOrgID
                    LEFT JOIN ktv_mill km ON km.MillID = vso1.ObjID AND vso1.ObjType = 'mill'
                    LEFT JOIN ktv_members_extension kme ON kme.MemberID = vso1.ObjID AND vso1.ObjType = 'trader'
                    LEFT JOIN ktv_members_extension kmee ON kmee.MemberID = vso1.ObjID
	                LEFT JOIN view_tc_supplychain_org vso3 on vso3.SupplychainID = sb1.SupplyDestDoOrgID
                WHERE  
                    1=1
                    AND sb1.SupplyBatchStatus IN ('Delivered','Sent')
                    $sb1 AND sb1.SupplyBatchID=? $sb2
                    $sqlFilter
                ";
		
        $sql .= " GROUP BY sb1.SupplyBatchID ORDER BY $sortingField $sortingDir LIMIT ?, ?"; 
        $params = array($this->input->get('SBID'), (int)$start, (int)$limit);
        $Q = $this->db->query($sql, $params);
		// echo"<pre>";echo $this->db->last_query();die;
        if($Q->num_rows() > 0) {
            $data['data']  = $Q->result_array();
            $data['total'] = $this->db->query('SELECT FOUND_ROWS() total')->row()->total;
            return $data;
        }

        return $data;
    }

    public function get_data_user($UserID){ 
      
        $data = array('data' => array(), 'total' => 0);

        $sql = "SELECT
                   SupplychainID
                FROM
                    view_tc_supplychain_staff
                WHERE
                    UserID = ?
        ";

        $Q = $this->db->query($sql,array($UserID));
        
        if($Q->num_rows()){
            $result = $Q->result();

            foreach($result as $val){
                $data = $val->SupplychainID;
            }
        }
        
        return $data;
    }
    
    public function get_data_edit($SID, $SBID){
        $data = array('data' => array());

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                sb1.SupplyBatchID,
                DATE(sb1.SupplyBatchDate) SupplyBatchDate,
                sb1.SupplyOrgID,
                sb1.SupplyDestOrgID,
                sb1.SupplyBatchNumber,
                DATE(sb1.DeliveryDate) DeliveryDate,
                sb1.DestPO,
                IFNULL(sb1.DestWeight,SUM(st1.VolumeNetto)) DestWeight,
                sb1.DestNumberPackage,
                sb1.DestDriver,
                sb1.Weather,
                sb1.DestTransportID,
                sb1.DestTransportNumber,
                sb1.Notes,
                sb1.ChangeLog,
                sb1.ChangeBy,
                sb1.StatusCode,
                sb1.DateCreated,
                sb1.CreatedBy,
                sb1.DateUpdated,
                sb1.LastModifiedBy, 
                SUM(st1.VolumeBruto) Bruto, 
                SUM(st1.VolumeNetto) Net,
                IF(st2.SupplyTransID IS NULL, 'Pending', IF(st2.SupplyBatchID IS NULL, 'Received', 'Sent')) SupplyBatchStatus,
                vso1.ObjID, vso1.Name as SupplierName,

                st2.`SupplyTransID`,
                st2.`SupplychainID`,
                st2.`TransNumber`,
                st2.`InvoiceNumber`,
                st2.`DateTransaction`,
                st2.`SupplyType`,
                st2.`PlantationNr`,
                st2.`VolumeBruto`,
                st2.`VolumeNetto`,
                st2.`VolumeCutting`,
                st2.`PackageID`,
                st2.`PackageNumber`,
                st2.`PackageWeight`,
                st2.`DetailTypeID`,
                st2.`TransStatusID`,
                st2.`FarmingTypeID`,
				st2.DateTransaction,
                st2.ContractPrice,
                st2.NetPrice,
                st2.DiscountPrice,
                st2.TotalPayment
            FROM 
                ktv_tc_supplychain_batch sb1
                LEFT JOIN ktv_tc_supplychain_transaction st1 ON st1.SupplyBatchID=sb1.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchID AND st2.SupplyType='Batch'
                LEFT JOIN view_tc_supplychain_org vso1 ON vso1.SupplychainID=sb1.SupplyOrgID
            WHERE 
                sb1.SupplyBatchID=? 
            GROUP BY sb1.SupplyBatchID";

        $params = array($SBID);
        $Q = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;
        if($Q->num_rows() > 0) {
            $data['data']  = $Q->result_array();
            return $data;
        }

        return $data;
    }    
    private function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }
    public function submit($params){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';

        $data=array();
        foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Transaction_FormPenerimaan-Form-", '', $key);
            if($value == "") $value = null;
            $data[$keyNew] = $value;
        }
        $param = $data;

        try{
            $this->db->trans_begin();
            /* Transaksi */
            $content_tr = array(
                "SupplychainID"=> $this->input->request_headers()['Sid'],
                "SupplyBatchID"=> null,
                "DateTransaction"=> $param['DateTransaction'],
                "SupplyType"=> 'Batch',
                "SupplyID"=> $this->input->request_headers()['Sbid'],
                "VolumeBruto"=> $this->replacestr($param['VolumeNetto']),
                "VolumeNetto"=> $this->replacestr($param['VolumeNetto']),    
                "StatusCode" => 'active',
                "SupplybaseCategoryID"=>3 //klo sudah ada penerimaan dari Mill lain baru di query pengecekan dari agent/mill, sementara di hardcode dulu, karena penerimaan mill pati dari DO
            );
			 
			//print_r($content_tr);die; 
            if($param['STID']){
                /* Update data Transaction */
                $this->db->where('SupplyTransID', $param['STID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_transaction', $content_tr);
				//echo $this->db->last_query();die;
                $insid = $param['STID'];
            }else{
                $content_tr['TransNumber'] = generateTransTraceabilityNumber($content_tr['SupplychainID']); 
                $content_tr['InvoiceNumber'] = ""; 
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_transaction', $content_tr);
                $insid = $this->db->insert_id();
            }
			
            /* Quality *
            if($param['quality']){
                $this->db->where('SupplyTransID', $insid);
                $this->db->delete('ktv_tc_supplychain_transaction_quality');
                $quality = $param['quality'];
				
                $dt = json_decode($quality);
                foreach($dt as $k => $quality){
                  $type =  $this->cek_type($quality->QualityID); 
				  if($type == 'combo'){
					$QS = $this->db->select('ValueQualityID')
							  ->from('ktv_tc_supplychain_quality_value')
							  ->where('QualityID', $quality->QualityID)
							  ->where('Value', $quality->Value)
							  ->where('StatusCode', 'active')
							  ->get()->row();
				   
					$content_quality = array(
                        'SupplyTransID' => $insid,
                        'QualityID' => $quality->QualityID,
                        'Value' => @$QS->ValueQualityID,
                        'StatusCode' => 'active'
                    );
					}else{
						 $content_quality = array(
							'SupplyTransID' => $insid,
							'QualityID' => $quality->QualityID, 
							'Value'	=> $quality->Value,
							'StatusCode' => 'active'
						);
					 }
                    
                    $content_quality['DateCreated'] = date('Y-m-d H:i:s');
                    $content_quality['DateUpdated'] = date('Y-m-d H:i:s');
                    $content_quality['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                    $this->db->insert('ktv_tc_supplychain_transaction_quality', $content_quality);
                }
            }
			*/
            $this->db->where('SupplyBatchID', $this->input->request_headers()['Sbid']);
            $this->db->update('ktv_tc_supplychain_batch', 
                array(
                    'ReceiveWeight'=>$this->replacestr($param['VolumeNetto']),
                    'RemainingWeight'=>$this->replacestr($param['VolumeNetto']),
                    'SupplyBatchStatus' => 'Delivered',
                    'DateUpdated'=>date("Y-m-d H:i:s"),
                    'ReceivedDate'=>date("Y-m-d H:i:s")
                )
            );
			//echo '<pre>';
			//echo $this->db->last_query();die;
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
            return array('success' => $result, 'SupplyTransID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
	
	private function cek_type($QualityID)
	{ 
            $this->db->where('QualityID', $QualityID);
            $r= $this->db->select('Type')->from('ktv_tc_supplychain_quality')->get()->row();
            return $r->Type;
	}

	function replacestr($form)
	{
		return str_replace(',','',$form);
	}
	
	function  getTransaksi($SupplyTransID)
	{
		$SQL ="SELECT
					vso.Name,
					st.DateTransaction,
					st.TransNumber,
					vsofrom.ObjID,
					vsofrom.Name as fromName, 
					st.VolumeBruto,
					st.VolumeNetto,
					st.PackageNumber,
					st.ContractPrice,
					st.NetPrice,
					st.TotalPayment
				FROM
					ktv_tc_supplychain_transaction st
					LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
					LEFT JOIN ktv_tc_supplychain_batch sbfrom ON sbfrom.SupplyBatchID=st.SupplyID
					LEFT JOIN view_tc_supplychain_org vsofrom ON vsofrom.SupplychainID=sbfrom.SupplyOrgID
				WHERE
					st.SupplyTransID=$SupplyTransID";
		$t = $this->db->query($SQL);
		//echo $this->db->last_query();die;
		return $t->row_array();
	}
	function getTransaksiQuality($SupplyTransID)
	{
		$SQL ='select B.Name, A.Value from ktv_tc_supplychain_transaction_quality A, ktv_tc_supplychain_quality B
			   where A.QualityID = B.QualityID and A.SupplyTransID = ?';
		$t = $this->db->query($SQL, array($SupplyTransID));
		//echo $this->db->last_query();die;
		return $t->result();
    }
    
    function  fetch_detail_transaction($get)
	{
        if($sortingField == "") $sortingField = 'SupplyTransID';
        if($sortingDir == "") $sortingDir = 'ASC'; 
        
		$SQL ="SELECT SQL_CALC_FOUND_ROWS
            st.SupplyTransID,
            st.SupplyBatchID,
            st.DateTransaction,
            st.VolumeBruto,
            st.VolumeNetto,
            st.SupplyType,
            st.SupplyID,
            m.MemberDisplayID,
            IFNULL(m.MemberName, IFNULL(vso.Name, vso2.Name)) SupplierName
        FROM
            ktv_tc_supplychain_transaction st
            LEFT JOIN ktv_members m ON m.MemberID = st.SupplyID AND st.SupplyType='Farmer'
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = st.SupplyID AND st.SupplyType='Nonfarmer'
            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND (st.SupplyBatchType='Traceable' OR st.SupplyBatchType IS NULL)
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb2.SupplyOrgID
        WHERE
            st.SupplyBatchID =?
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
            ";
		$query = $this->db->query($SQL, array($get['SBID'],(int) $get['start'],(int) $get['limit']));
        //echo '<pre>'.$this->db->last_query();die;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);

        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);

            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return array(
                'data'      => array(),
                'total'     => 0
                );
        }
		
    }

    function  export_detail_transaction($get)
	{
        if($sortingField == "") $sortingField = 'SupplyTransID';
        if($sortingDir == "") $sortingDir = 'ASC'; 
        
		$SQL ="SELECT m.MemberDisplayID,
            IFNULL(m.MemberName, IFNULL(vso.Name, vso2.Name)) SupplierName,
            st.SupplyType,
            st.VolumeBruto,
            st.VolumeNetto,
            st.SupplyTransID,
            st.SupplyBatchID,
            st.DateTransaction,
            st.SupplyID
        FROM
            ktv_tc_supplychain_transaction st
            LEFT JOIN ktv_members m ON m.MemberID = st.SupplyID AND st.SupplyType='Farmer'
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = st.SupplyID AND st.SupplyType='Nonfarmer'
            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND (st.SupplyBatchType='Traceable' OR st.SupplyBatchType IS NULL)
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb2.SupplyOrgID
        WHERE
            st.SupplyBatchID =?
            ORDER BY $sortingField $sortingDir
            ";
		$query = $this->db->query($SQL, array($get['SBID']));
        //echo '<pre>'.$this->db->last_query();die;

        if ($query->num_rows() > 0) {
            return array(
                'data'      => $query->result_array()
                );
        }else{
            return array(
                'data'      => array()
                );
        }
		
    }

    function export_detail_batch() {

        $SupplychainID =  $_SESSION['SupplychainID'];
        
        $this->db->select(' IFNULL(b.MemberDisplayID, "-") AS MemberDisplayID,
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
                            a.SupplyType,
                            a.`VolumeBruto` AS GrossWeight,
                            a.`VolumeNetto` AS NettWeight
                        ', false);
        $this->db->join('ktv_members b',"a.SupplyID = b.MemberID AND a.SupplyType = 'Farmer'", 'left');
        $this->db->join('ktv_ref_certification_program cp', 'cp.CertProgID = b.isCertified', 'left');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		$this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyID", 'left');
		$this->db->join('ktv_mill m2', 'm2.MillID=a.MillID', 'left');
		$this->db->join('view_tc_supplychain_org mem', 'mem.SupplychainID = a.MillID', 'left');
        $this->db->join('view_tc_supplychain_org vso3', "vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'", 'left');
        $this->db->join('ktv_members_extension kms', 'kms.MemberID = vso3.ObjID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.isDelivery IS NULL', NULL, TRUE);
        $this->db->order_by('a.SupplyTransID', 'DESC');
    
        if ($is_admin != 1) {
            $this->db->where("a.SupplychainID", @$SupplychainID);
        }

        $query = $this->db->get('ktv_tc_supplychain_transaction a');
        
        if ($query->num_rows() > 0) {
            return array(
                'data'      => $query->result_array()
                );
        }else{
            return array(
                'data'      => array()
                );
        }
    }

    function export_batch($SID) 
    {
       
        $is_admin = $_SESSION['is_admin'];

        $this->db->select("SQL_CALC_FOUND_ROWS 
            sb.SupplyBatchNumber
            , vso.Name AS AgentName
            , sum(st.VolumeNetto) AS VolumeNetto
            , sb.SupplyBatchStatus AS Status
            , sb.DateCreated", FALSE);
        $this->db->from("ktv_tc_supplychain_batch sb");
        $this->db->join('view_tc_supplychain_org vso', 'vso.SupplychainID = sb.SupplyOrgID', 'left');
        $this->db->join('view_tc_supplychain_org vso1', 'vso1.SupplychainID = sb.SupplyDestMillOrgID', 'left');
        $this->db->join('ktv_tc_supplychain_transaction st', 'st.SupplyBatchID = sb.SupplyBatchID', 'left');
        $this->db->where("sb.StatusCode","active");
        $this->db->where('st.VolumeNetto is NOT NULL', NULL, FALSE);

        if ($is_admin != 1) {
            $this->db->where("sb.SupplyOrgID",@$SID);
        }

        $this->db->group_by('sb.SupplyBatchID');
        $this->db->order_by('sb.DateCreated', "DESC");

        $query = $this->db->get();
        $result['data'] = $query->result_array();
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');

        $result['total'] = $query->row()->total;

        return $result;
    }
    
    function  detail_transaction($get)
	{
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'm.MemberName' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? 'ASC' : $order[0]['direction'];

        $m1 = @$get['MemberName']=='' ? '/*' : '';
        $m2 = @$get['MemberName']=='' ? '*/' : '';

        $sql="SELECT SQL_CALC_FOUND_ROWS
                m.MemberID, m.MemberName,
                if(st3.SupplyType!='Batch',sb3.SupplyDestOrgID,if(st2.SupplyType!='Batch',sb2.SupplyDestOrgID,sb.SupplyDestOrgID)) SupplyDestOrgID,
                
                if(st3.SupplyType!='Batch',st3.SupplychainID,if(st2.SupplyType!='Batch',st2.SupplychainID,st.SupplychainID)) SupplychainID,
                
                if(st3.SupplyType!='Batch',SUBSTR(st3.DateTransaction,1,10),if(st2.SupplyType!='Batch',SUBSTR(st2.DateTransaction,1,10),SUBSTR(st.DateTransaction,1,10))) DateTransaction,
                if(st3.SupplyType!='Batch',st3.VolumeBruto,if(st2.SupplyType!='Batch',st2.VolumeBruto,st.VolumeBruto)) VolumeBruto,
                
                if(st3.SupplyType!='Batch',st3.VolumeNetto,if(st2.SupplyType!='Batch',st2.VolumeNetto,st.VolumeNetto)) VolumeNetto,
               
                if(st3.SupplyType!='Batch',st3.TotalPayment,if(st2.SupplyType!='Batch',st2.TotalPayment,st.TotalPayment)) TotalPayment,
                
                vso.name aggregator1,
                vso2.name aggregator2,
                vso3.name aggregator3
                
                from ktv_tc_supplychain_batch sb
                LEFT JOIN ktv_tc_supplychain_transaction st ON sb.SupplyBatchID=st.SupplyBatchID
                
                LEFT JOIN ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch'
                LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID 
                
                LEFT JOIN ktv_tc_supplychain_batch sb3 on sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
                LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID 
                
                LEFT JOIN ktv_members m ON m.MemberID=if(st3.SupplyType!='Batch',st3.SupplyID,if(st2.SupplyType!='Batch',st2.SupplyID,st.SupplyID))

                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL(st.SupplychainID,sb.SupplyOrgID)

                LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = IFNULL(st2.SupplychainID,sb.SupplyDestOrgID)
                LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = IFNULL(st3.SupplychainID,sb2.SupplyDestOrgID)
                
                where m.MemberID IS not null
                AND (st3.SupplyBatchID=? OR st2.SupplyBatchID=? OR st.SupplyBatchID=?)
                ORDER BY $order_by $sort
                LIMIT ?,?";
        $query = $this->db->query($sql, array(@$get['SBID'], @$get['SBID'], @$get['SBID'], intval(@$get['start']), intval(@$get['limit']) ));
        //echo $this->db->last_query();die;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
		
	}

    function export_grid_reception_detail($DateStart, $DateEnd) 
    {
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        $is_admin = $_SESSION['is_admin'];

        $this->db->select("a.DeliveryDate AS Tanggal, 
                            a.DestTransportNumber AS No_mobil, 
                            d.VolumeBruto AS Tonase, 
                            mem.Name AS DO, 
                            IFNULL(e.MemberDisplayID, IFNULL(e.MemberID, '-')) AS No_ID,
                            IF(
                            e.MemberName IS NULL OR e.MemberName = '', IF(
                            kms.agCompanyName IS NULL OR kms.agCompanyName = '', IF(
                            d.MillOther IS NULL OR d.MillOther = '', IF(
                            mem.Name IS NULL OR mem.Name = '', IF(
                            d.DOOther IS NULL OR d.DOOther = '', IF(
                            d.AgentOther IS NULL OR d.AgentOther = '', 'Nonfarmer', d.AgentOther
                            ), d.DOOther
                            ), mem.Name
                            ), d.MillOther
                            ), kms.agCompanyName
                            ), e.MemberName
                            ) as SupplierName, 
                            ksp.Latitude AS x, 
                            ksp.Longitude AS y, 
                            ksp.FirstPlantingYear AS Tahun_tanam 
                        ", FALSE);
        $this->db->join("ktv_tc_supplychain_delivery_detail b","b.DeliveryID= a.DeliveryID",'left');
        $this->db->join("ktv_tc_supplychain_batch ktsb","ktsb.SupplyBatchID= b.SupplyBatchID",'left');
        $this->db->join('ktv_tc_supplychain_transaction d', 'd.SupplyBatchID = ktsb.SupplyBatchID', 'left');
        $this->db->join("ktv_members e","e.MemberID = d.SupplyID AND d.SupplyType != 'Batch' AND d.SupplyType != 'Nonfarmer'",'left');
        $this->db->join("ktv_survey_plot ksp","ksp.MemberID = e.MemberID",'left');
        $this->db->join('view_tc_supplychain_org mem', 'mem.SupplychainID = IFNULL(a.SupplyDestDoOrgID,a.SupplychainID)', 'left');
        $this->db->join('ktv_members_extension kms', 'kms.MemberID = e.MemberID','left');
        
        $this->db->where('a.StatusCode', 'active');
        $this->db->where("a.SupplyDestProcessType", 'mill');
        $this->db->where_in('a.DeliveryStatusID', [3, 4, 5]);
        $this->db->group_by('a.DestTransportNumber');
        $this->db->group_by('e.MemberID');

        if ($is_admin != 1) {
            $this->db->where("a.SupplyDestMillOrgID", @$SupplychainID);
        }
        
        if ($DateStart != '' OR $DateEnd !='') {
            $this->db->where('DATE_FORMAT(a.DeliveryDate, "%Y-%m-%d") >=',@$DateStart);
            $this->db->where('DATE_FORMAT(a.DeliveryDate, "%Y-%m-%d") <=',@$DateEnd);
        }

        $query = $this->db->get("ktv_tc_supplychain_delivery a");
        
        if ($query->num_rows() > 0) {
            return array(
                'data'      => $query->result_array()
                );
        }else{
            return array(
                'data'      => array()
                );
        }
    }

    function export_grid_reception($DateStart, $DateEnd) 
    {
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }
        
        if ($DateStart != '' OR $DateEnd !='') {
            $whereFilter = "AND DATE_FORMAT(ktsd.DeliveryDate, '%Y-%m-%d') >= '$DateStart' AND DATE_FORMAT(ktsd.DeliveryDate, '%Y-%m-%d') <= '$DateEnd'";
        }

        $sql = "SELECT
                    ktsd.DeliveryDate AS Tanggal, 
                    ktsd.DestTransportNumber AS No_mobil,
                    ktstd.TotalCapacity AS Tonase_netto,
                    ktst.VolumeBruto AS Tonase_bruto,
                    vtso.Name 
                FROM 
                    ktv_tc_supplychain_delivery ktsd 
                LEFT JOIN 
                    ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID 
                LEFT JOIN 
                    ktv_tc_supplychain_transaction_detail ktstd ON ktstd.DeliveryDetailID = ktsdd.DeliveryID 
                LEFT JOIN 
                    ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID 
                LEFT JOIN 
                    ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID 
                LEFT JOIN 
                    view_tc_supplychain_org vtso ON vtso.SupplychainID = IFNULL(ktsd.SupplyDestDoOrgID, ktsd.SupplychainID)
                WHERE
                    ktsd.StatusCode = 'active'
                AND
                    ktsd.supplydestmillorgid = '$SupplychainID'
                AND
					ktsd.DeliveryStatusID IN (3,4,5)
                $whereFilter
                GROUP BY ktsd.DeliveryID
                ORDER BY ktst.TransNumber DESC
        ";

        $Q = $this->db->query($sql);
        // echo"<pre>";echo $this->db->last_query();die;
        if ($Q->num_rows() > 0) {
            return array(
                'data'      => $Q->result_array()
                );
        }else{
            return array(
                'data'      => array()
                );
        }
    }
}

?>
