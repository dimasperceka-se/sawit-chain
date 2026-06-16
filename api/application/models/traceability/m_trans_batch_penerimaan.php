<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_trans_batch_penerimaan extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function get_data_trans($SID, $PID){
        date_default_timezone_set('Asia/Jakarta');
        
        $data = array('data' => array(), 'total' => 0);

        $this->db->select('a.*, vso.Name AS SupplyOrgName');
        $this->db->from('ktv_tc_supplychain_batch a');
        $this->db->join('view_tc_supplychain_org vso','vso.SupplychainID = a.SupplyOrgID','left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplyDestOrgID', $SID);
        $this->db->where('a.SupplyBatchStatus', 'Sent');

        $this->db->order_by('a.SupplyBatchID', 'DESC');

        $Q = $this->db->get();
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                $val->trans = array();
                $dt_trans = $this->db->select('*')
                                ->from('ktv_tc_supplychain_transaction')
                                ->where('StatusCode', 'active')
                                ->where('SupplyBatchID', $val->SupplyBatchID)
                                ->get();

                if($dt_trans->num_rows()){
                    $r_detail = $dt_trans->result();
                    foreach($r_detail as $value){
                        $value = $this->check_isNull($value);
                        $value->quality = array();
                        /*$getQuality = $this->db->select(' 
                                            TransQualityID,
                                            QualityID,
                                            Value,
                                            CreatedBy,
                                            DateCreated,
                                            DateUpdated,
                                            LastModifiedBy
                                        ')
                        ->from('ktv_tc_supplychain_transaction_quality a')
                        ->where('a.StatusCode', 'active')
                        ->where('a.SupplyTransID', $value->SupplyTransID)
                        ->get();

                        if($getQuality->num_rows()){
                            $d_quality = $getQuality->result();
                            foreach($d_quality as $k => $v){
                                $v = $this->check_isNull($v);
                            }
                            $value->quality = $d_quality;
                        }*/
                        $sql = "SELECT 
                                    a.TransQualityID,
                                    b.QualityID,
                                    b.SupplychainID,
                                    b.Name,
                                    IFNULL(b.Formula,'') AS Formula,
                                    IFNULL(b.Order,'') AS `Order`,
                                    IFNULL(b.Type,'') AS `Type`,
                                    IFNULL(b.MinValue,'') AS `MinValue`,
                                    IFNULL(b.MaxValue,'') AS `MaxValue`,
                                    IFNULL(b.StandardValue,'') AS StandardValue,
                                    IFNULL(b.IsPrintVisible,'') AS IsPrintVisible,
                                    IFNULL(a.Value,'') AS InputValue,
                                    a.CreatedBy,
                                    a.DateCreated,
                                    a.DateUpdated,
                                    IFNULL(a.LastModifiedBy,'') AS LastModifiedBy
                                FROM 
                                    ktv_tc_supplychain_transaction_quality a
                                    LEFT JOIN ktv_tc_supplychain_quality b ON a.QualityID=b.QualityID
                                WHERE a.StatusCode='active' AND a.SupplyTransID=?";
                        $getQuality = $this->db->query($sql, array($value->SupplyTransID));
                        if($getQuality->num_rows()){
                            $d_quality = $getQuality->result_array();
                            foreach($d_quality as $kq => $vq){
                                //$vq = $this->check_isNull($vq);
                                $query_value = $this->db->select('ValueQualityID, Value')
                                ->from('ktv_tc_supplychain_quality_value')
                                ->where('QualityID', $vq['QualityID'])
                                ->where('StatusCode', 'active')
                                ->get();
                                $d_quality[$kq]['Value'] = array();
                                if($query_value->num_rows()){
                                    $d_quality[$kq]['Value'] = $query_value->result();
                                }
                            }
                            $value->quality = $d_quality;
                        }
                    }
                    $val->trans = $r_detail;
                }
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
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
    public function submit($param){
        $result = false;
        $insid = 0;
        $error = '';

        $data = $param['data'];

        try{
            $this->db->trans_begin();
            
            $cekSIP = $this->db->select('*')
                ->from('ktv_tc_supplychain_batch')
                ->where('StatusCode', 'active')
                ->where('SupplyBatchID', $data['SupplyBatchID'])
                ->get();

            $content = array(
                "SupplyBatchDate" => @$data['SupplyBatchDate'].' '.date('H:i:s'),
                "SupplyOrgID" => @$data['SupplyOrgID'],
                "SupplyDestOrgID" => @$data['SupplyDestDoOrgID'],
                "SupplyBatchNumber" => @$data['SupplyBatchNumber'],
                "SupplyBatchStatus" => @$data['SupplyBatchStatus'],
                //"SupplyBatchTypeID" => $data['SupplyBatchTypeID'],
                "DeliveryDate" => @$data['DeliveryDate'],
                "DestPO" => @$data['DestPO'],
                "DestWeight" => @$data['DestWeight'],
                "DestNumberPackage" => @$data['DestNumberPackage'],
                "DestDriver" => @$data['DestDriver'],
                "DestTransportID" => @$data['DestTransportID'],
                "DestTransportNumber" => @$data['DestTransportNumber'],
                "Notes" => @$data['Notes'],
                "ChangeLog" => @$data['ChangeLog'],
                "ChangeBy" => @$data['ChangeBy'],
                "StatusCode" => 'active'
            );

            if($cekSIP->num_rows()){
                $dataSIP = $cekSIP->row();
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->where('SupplyBatchID', $data['SupplyBatchID']);
                $this->db->update('ktv_tc_supplychain_batch', $content);
                $insid = $data['SupplyBatchID'];
            }else{
                $content['DateCreated'] = $data['DateCreated'];
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['CreatedBy'] = $data['CreatedBy'];
                $this->db->insert('ktv_tc_supplychain_batch', $content);
                $insid = $this->db->insert_id();
            }
            /* Transaksi */
            $transaksi = array();
            for($i=0; $i < count($data['trans']); $i++){
                $content_tr = array(
                    "SupplychainID"=> $data['trans'][$i]["SupplychainID"],
                    "SupplyBatchID" => $insid,
                    "DateTransaction"=> $data['trans'][$i]["DateTransaction"].' '.date('H:i:s'),
                    "SupplyType"=> $data['trans'][$i]["SupplyType"], //Farmer, Batch
                    "SupplyID"=> $data['trans'][$i]["SupplyID"],
                    "PlantationNr"=> $data['trans'][$i]["PlantationNr"],
                    "VolumeBruto"=> $data['trans'][$i]["VolumeBruto"],
                    "VolumeNetto"=> $data['trans'][$i]["VolumeNetto"],
                    "VolumeCutting"=> $data['trans'][$i]["VolumeCutting"],
                    "PackageID"=> $data['trans'][$i]["PackageID"],
                    "PackageNumber"=> $data['trans'][$i]["PackageNumber"],
                    "PackageWeight"=> $data['trans'][$i]["PackageWeight"],
                    "DetailTypeID"=> $data['trans'][$i]["DetailTypeID"],
                    "TransStatusID"=> $data['trans'][$i]["TransStatusID"],
                    "ContractPrice"=> $data['trans'][$i]['ContractPrice'],
                    "NetPrice"=> $data['trans'][$i]['NetPrice'],
                    "DiscountPrice"=> $data['trans'][$i]['DiscountPrice'],
                    "TotalPayment"=> $data['trans'][$i]['TotalPayment'],
                    "Notes"=> $data['trans'][$i]["Notes"],
                    "TransNumber" => $data['trans'][$i]['TransNumber'],
                    "InvoiceNumber" => "",
                    "StatusCode" => 'active'
                );

                $cekTR = $this->db->select('*')
                ->from('ktv_tc_supplychain_transaction')
                ->where('StatusCode', 'active')
                ->where('SupplyTransID', $data['trans'][$i]['SupplyTransID'])
                ->get();

                if($cekTR->num_rows()){
                    $d_TR = $cekTR->row();
                    $transaksi[$i]['id'] = $d_TR->SupplyTransID;
                    if((int)$d_TR->SupplyBatchID == 0 || (int)$d_TR->SupplyBatchID == $insid){
                        $transaksi[$i]['msg'] = 'update';
                        $this->db->where('SupplyTransID', $d_TR->SupplyTransID);
                        $content_tr['DateUpdated'] = $data['trans'][$i]['DateUpdated'];
                        $content_tr['LastModifiedBy'] = $data['trans'][$i]['LastModifiedBy'];
                        $this->db->update('ktv_tc_supplychain_transaction', $content_tr);
                    }else{
                        $transaksi[$i]['msg'] = 'reject';
                    }
                }else{
                    $content_tr['DateCreated'] = $data['trans'][$i]['DateCreated'];
                    $content_tr['DateUpdated'] = $data['trans'][$i]['DateUpdated'];
                    $content_tr['CreatedBy'] = $data['CreatedBy'];
                    $this->db->insert('ktv_tc_supplychain_transaction', $content_tr);
                    $transaksi[$i]['id'] = $this->db->insert_id();
                    $transaksi[$i]['msg'] = 'insert';
                }

                /* Quality */
                if($transaksi[$i]['id'] && $transaksi[$i]['msg'] != 'reject'){
                    if(count($data['trans'][$i]['quality'])){
                        $this->db->where('SupplyTransID', $transaksi[$i]['id']);
                        $this->db->delete('ktv_tc_supplychain_transaction_quality');
                        foreach($data['trans'][$i]['quality'] as $quality){
                            $content_quality = array(
                                'SupplyTransID' => $transaksi[$i]['id'],
                                'QualityID' => $quality['QualityID'],
                                'Value' => $quality['Value'],
                                'StatusCode' => 'active'
                            );
                            $content_quality['DateCreated'] = $data['trans'][$i]['DateCreated'];
                            $content_quality['DateUpdated'] = $data['trans'][$i]['DateUpdated'];
                            $content_quality['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                            $this->db->insert('ktv_tc_supplychain_transaction_quality', $content_quality);
                        }
                    }
                }            
            }

            $id = '';
            $msg = 'rejected. SupplyBatchID on transaction not null !';
            for($x=0; $x < count($transaksi); $x++){
                if(@$transaksi[$x]['msg'] == 'reject'){
                    $id .= $transaksi[$x]['id'].', ';
                }
            }

            if($id){
                //throw new Exception('no data returned');
                $this->db->trans_rollback();
                $error = 'Transaction ID '.$id.' '.$msg;
            }else if ($this->db->trans_status() == false) {
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
            return array('success' => $result, 'SupplyOrgID' => $insid, 'SupplyBatchIDApp' => $data['SupplyBatchIDApp']);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
}

?>
