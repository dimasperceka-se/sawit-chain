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
        date_default_timezone_set('UTC');
    }
    
    public function get_data_trans($SID, $PID){
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
		
		$SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');
 
        
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS
					st2.SupplyTransID,
                    sb1.SupplyBatchID,
                    sb1.SupplyBatchDate,
                    sb1.SupplyOrgID,
                    sb1.SupplyDestOrgID,
                    sb1.SupplyBatchNumber,
                    sb1.DeliveryDate,
                    sb1.DestPO,
                    sb1.DestWeight,
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
                    st2.VolumeBruto Bruto, 
                    st2.VolumeNetto Net,
                    st2.PackageNumber,
                    IF(st2.SupplyTransID IS NULL, 'Pending', IF(st2.SupplyBatchID IS NULL, 'Received', 'Sent')) SupplyBatchStatus,
                    vso1.ObjID, vso1.Name as SupplierName
                FROM 
                    ktv_tc_supplychain_batch sb1
                    LEFT JOIN ktv_tc_supplychain_transaction st1 ON st1.SupplyBatchID=sb1.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb1.SupplyBatchID AND st2.SupplyType='Batch'
                    LEFT JOIN view_tc_supplychain_org vso1 ON vso1.SupplychainID=sb1.SupplyOrgID
                WHERE 
                    sb1.SupplyDestOrgID=?
                    $sb1 AND sb1.SupplyBatchID=? $sb2
                ";
		
		if($SupplyStatus){
            $sql .=" AND IF(st2.SupplyTransID IS NULL, 'Pending', IF(st2.SupplyBatchID IS NULL, 'Received', 'Sent')) = '{$SupplyStatus}' "; 
        }

        if($SupplyKey){
		   $sql .=" AND vso1.Name LIKE '%{$SupplyKey}%' "; 
        }
		
        $sql .= " GROUP BY sb1.SupplyBatchID ORDER BY ? ? LIMIT ?, ?"; 
        $params = array($SID, $this->input->get('SBID'),'a.SupplyBatchID', 'DESC', $start, $limit);
        $Q = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;
        if($Q->num_rows() > 0) {
            $data['data']  = $Q->result_array();
            $data['total'] = $this->db->query('SELECT FOUND_ROWS() total')->row()->total;
            return $data;
        }

        return $data;
    }
    public function get_data_edit($SID, $SBID){
        $data = array('data' => array());

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                sb1.SupplyBatchID,
                sb1.SupplyBatchDate,
                sb1.SupplyOrgID,
                sb1.SupplyDestOrgID,
                sb1.SupplyBatchNumber,
                sb1.DeliveryDate,
                sb1.DestPO,
                sb1.DestWeight,
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
                sb1.SupplyDestOrgID=?
                AND sb1.SupplyBatchID=? 
            GROUP BY sb1.SupplyBatchID";

        $params = array($SID, $SBID);
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
            $keyNew = str_replace("Koltiva_view_Traceability_Transaction_FormPenerimaan-Form-", '', $key);
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
                "VolumeBruto"=> $this->replacestr($param['VolumeBruto']),
                "VolumeNetto"=> $this->replacestr($param['VolumeNetto']),
                "VolumeCutting"=> $this->replacestr($param['VolumeCutting']),
                "PackageID"=> $param['PackageID'], 
                "PackageNumber"=> $this->replacestr(@$param['PackageNumber']),
				"PackageWeight"=> $this->replacestr(@$param['PackageWeight']),
                "DetailTypeID"=> $param['DetailTypeID'], 
                "NetPrice"=> $this->replacestr($param['NetPrice']), 
                "TotalPayment"=> $this->replacestr($param['TotalPayment']),
                "StatusCode" => 'active'
            );
			//print_r($content_tr);die; 
            if($param['STID']){
                /* Update data Transaction */
                $this->db->where('SupplyTransID', $param['STID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_transaction', $content_tr);
				//echo $this->db->last_query();die;
                $insid = $this->input->request_headers()['Sid'];
            }else{
                $content_tr['TransNumber'] = generateTransTraceabilityNumber($content_tr['SupplychainID']); 
                $content_tr['InvoiceNumber'] = ""; 
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_transaction', $content_tr);
                $insid = $this->db->insert_id();
            }

            /* Quality */ 
            if($param['quality']){
                $this->db->where('SupplyTransID', $insid);
                $this->db->delete('ktv_tc_supplychain_transaction_quality');
                $quality = $param['quality'];
				
                $dt = json_decode($quality);
                foreach($dt as $k => $quality){
                      $QS = $this->db->select('ValueQualityID')
							  ->from('ktv_tc_supplychain_quality_value')
							  ->where('QualityID', $quality->QualityID)
							  ->where('Value', $quality->Value)
							  ->where('StatusCode', 'active')
							  ->get()->row();
							  
					$content_quality = array(
                        'SupplyTransID' => $insid,
                        'QualityID' => $quality->QualityID,
                        'Value' => $QS->ValueQualityID,
                        'StatusCode' => 'active'
                    );
                    
                    $content_quality['DateCreated'] = date('Y-m-d H:i:s');
                    $content_quality['DateUpdated'] = date('Y-m-d H:i:s');
                    $content_quality['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                    $this->db->insert('ktv_tc_supplychain_transaction_quality', $content_quality);
                }
            }

            $this->db->where('SupplyBatchID', $this->input->request_headers()['Sbid']);
            $this->db->update('ktv_tc_supplychain_batch', array('SupplyBatchStatus' => 'Delivered'));

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

	function replacestr($form)
	{
		return str_replace(',','',$form);
	}
	
	function  getTransaksi($SupplyTransID)
	{
		$SQL ='SELECT
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
					st.SupplyTransID= ? ';
		$t = $this->db->query($SQL, array($SupplyTransID));
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
}

?>
