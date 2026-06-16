<?php

/**
 * Authentication Model for Mobile
 *
 * @author Ardi <ardiantoro@koltiva.com>
 */
class Msupplychain extends CI_Model {

    function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
    }

    public function getListSPB($SID){
        $sql = "SELECT
                ssp.SMESPCodeID,
                a1.SupplychainID 'DOID',
                vorg.SupplychainID MillID,
                CONCAT(msp.SuratNr,' - ',m.MemberName) 'SPCode',
                msp.Note
            FROM
                ktv_tc_supplychain_org a
                LEFT JOIN `ktv_tc_supplychain_org_rel` b ON a.SupplyChainID = b.ChildID
                LEFT JOIN `ktv_tc_supplychain_org` a1 ON a1.SupplyChainID = b.ParentID
                LEFT JOIN ktv_members m ON m.MemberID = a1.ObjID
                LEFT JOIN `ktv_sme_sp_code` ssp ON ssp.MemberID = m.MemberID
                LEFT JOIN `ktv_mill_sp_code` msp ON msp.SPCodeID = ssp.SPCodeID
                LEFT JOIN view_tc_supplychain_org vorg ON vorg.ObjID = msp.MillID
            WHERE
                a.SupplyChainID = ?
            AND
                ssp.SMESPCodeID IS NOT NULL
            UNION
            SELECT
                b.SMESPCodeID
                , '' DOID
                , vorg.SupplychainID MillID
                , CONCAT(c.SuratNr,' - ',d.MillName) as SPCode
                , c.Note
            FROM
                ktv_tc_supplychain_org a
            LEFT JOIN
                ktv_sme_sp_code b on b.MemberID = a.ObjID
            LEFT JOIN
                ktv_mill_sp_code c on c.SPCodeID = b.SPCodeID
            LEFT JOIN
                ktv_mill d on d.MillID = c.MillID
            LEFT JOIN 
                view_tc_supplychain_org vorg ON vorg.ObjID = d.MillID
            WHERE
                a.SupplychainID = ?
            AND NOW() BETWEEN b.DateStart AND b.DateEnd
            OR a.SupplychainID = ? AND DATE(b.DateEnd) = DATE(NOW())
            ORDER BY SPCode ASC
        ";

        $query = $this->db->query($sql,array($SID,$SID,$SID));

        
        $data['success'] = true;
        $data['message'] = 'Data Berhasil Ditampilkan';
        $data['data'] = $query->result_array();
        $data['total'] = $query->num_rows();
        return $data;
    }

    public function _submitTransaction($SupplyBatchID, $data, $db_transaction=false, $DateUpdated=false) {
        $return['ErrorCode'] = '';
        if($db_transaction){
            $this->db->trans_begin();
        }

        $return['status'] = false;
        $sql = "SELECT * FROM ktv_tc_supplychain_transaction WHERE /*(SupplyTransID=? AND SupplyTransID!='') OR*/ TransNumber=? AND SupplyTransID > 0 AND SupplyID=? AND SupplyType=?";
        $ct = $this->db->query($sql, array($data['SupplyTransID'], $data['TransNumber'], $data['SupplyID'], $data['SupplyType']));
        if($ct->num_rows() > 0){
            $data['SupplyTransID'] = $ct->row()->SupplyTransID;
            $trans = $this->_updateTransaction($SupplyBatchID, $data);    
            $return['status'] = true;
        }else{
            $trans = $this->_insertTransaction($SupplyBatchID, $data);
            $return['status'] = true;
        }

        if($return['status']==true){
            $return['SupplyTransID'] = $trans['id'];
            $return['SupplyTransIDApp'] = $data['SupplyTransIDApp'];
            $return['Quality'] = $trans['quality'];
        }
        

        if($return['ErrorCode'] != ''){
            $return['status'] = true;
        }else{
            unset($return['ErrorCode']);
        }

        if($db_transaction){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
            }
        }
        return $return;
    }

    private function _insertTransactionQuality($id, $data){
        $this->db->where('SupplyTransID', $id);
        $this->db->delete('ktv_tc_supplychain_transaction_quality');
        if(count($data) > 0){
            foreach($data as $k=>$v){
                $insert = array(
                    'SupplyTransID' => $id,
                    'QualityID' => $v['QualityID'],
                    'Value' => $v['Value']
                );
                $query = $this->db->insert('ktv_tc_supplychain_transaction_quality',$insert);
            }
            $return['status'] = true;
        }else{
            $return['status'] = false;
        }
        return $return;
    }

    private function _insertTransaction($SupplyBatchID, $data) {
        
        $insert = array(
            'SupplyBatchID' => $SupplyBatchID,
            'SupplychainID' => $data['SupplychainID'],
            'TransNumber' => $data['TransNumber'],
            'DateTransaction' => $data['DateTransaction'],
            'SupplyType' => $data['SupplyType'],
            'SupplyID' => $data['SupplyID'],
            'PlantationNr' => $data['PlantationNr'],
            'CollectpointID' => $data['CollectpointID'],
            'VolumeBruto' => $data['VolumeBruto'],
            'VolumeNetto' => $data['VolumeNetto'],
            'VolumeCutting' => $data['VolumeCutting'],
            'PackageID' => $data['PackageID'],
            'PackageNumber' => $data['PackageNumber'],
            'PackageWeight' => $data['PackageWeight'],
            'TransStatusID' => $data['TransStatusID'],
            'ContractPrice' => $data['ContractPrice'],
            'NetPrice' => $data['NetPrice'],
            'InvoiceNumber' => $data['InvoiceNumber'],
            'DiscountPrice' => $data['DiscountPrice'],
            'TotalPayment' => $data['TotalPayment'],
            'Notes' => $data['Notes'],
            'DateCreated' => $data['DateCreated'],
            'CreatedBy' => $data['CreatedBy'],
            'DateUpdated' => $data['DateUpdated'],
            'LastModifiedBy' => $data['LastModifiedBy'],
            'DeductionPercentage' => @$data['DeductionPercentage'],
            'DeductionWeight' => $data['DeductionWeight']
        );
        $this->db->insert('ktv_tc_supplychain_transaction', $insert);
        $id = $this->db->insert_id();

        //$quality = $this->_insertTransactionQuality($id, $data['quality']);
        $quality = array();

        return array( 'id' => $id, 'quality' => $quality);
    }

    private function _updateTransaction($SupplyBatchID, $data) {
        $id = $data['SupplyTransID'];
        $update = array(
            'SupplyBatchID' => $SupplyBatchID,
            'SupplychainID' => $data['SupplychainID'],
            'TransNumber' => $data['TransNumber'],
            'DateTransaction' => $data['DateTransaction'],
            'SupplyType' => $data['SupplyType'],
            'SupplyID' => $data['SupplyID'],
            'PlantationNr' => $data['PlantationNr'],
            'CollectpointID' => $data['CollectpointID'],
            'VolumeBruto' => $data['VolumeBruto'],
            'VolumeNetto' => $data['VolumeNetto'],
            'VolumeCutting' => $data['VolumeCutting'],
            'PackageID' => $data['PackageID'],
            'PackageNumber' => $data['PackageNumber'],
            'PackageWeight' => $data['PackageWeight'],
            'TransStatusID' => $data['TransStatusID'],
            'ContractPrice' => $data['ContractPrice'],
            'NetPrice' => $data['NetPrice'],
            'InvoiceNumber' => $data['InvoiceNumber'],
            'DiscountPrice' => $data['DiscountPrice'],
            'TotalPayment' => $data['TotalPayment'],
            'Notes' => $data['Notes'],
            'DateCreated' => $data['DateCreated'],
            'CreatedBy' => $data['CreatedBy'],
            'DateUpdated' => $data['DateUpdated'],
            'LastModifiedBy' => $data['LastModifiedBy'],
            'DeductionPercentage' => @$data['DeductionPercentage'],
            'DeductionWeight' => $data['DeductionWeight']
        );
        $this->db->where('SupplyTransID', $id);
        $this->db->update('ktv_tc_supplychain_transaction', $update);

        //$quality = $this->_insertTransactionQuality($id, $data['quality']);
        $quality = array();

        return array( 'id' => $id, 'quality' => $quality);
    }

    public function _submitBatch($data){
        $return['ErrorCode'] = '';
        $this->db->trans_begin();
        $return = array();
        $sql = "SELECT * FROM ktv_tc_supplychain_batch WHERE /*SupplyBatchID=? OR */ SupplyBatchNumber=? AND SupplyBatchID > 0";
        $check_batch = $this->db->query($sql, array($data['SupplyBatchID'], $data['SupplyBatchNumber']));
        
        if($data['LastModifiedBy']==""){$LastModifiedBy=NULL;}else{$LastModifiedBy=$data['LastModifiedBy'];}
        //if(@$data['TransTypeID']==""){$data['TransTypeID']=NULL;}
        if($data['SupplyDestDoOrgID']=='0' && $data['SupplyDestMillOrgID']!='' && $data['SupplyDestMillOrgID']!='0'){
            $SupplyDestOrgID = $data['SupplyDestMillOrgID'];
        }else{
            $SupplyDestOrgID = $data['SupplyDestDoOrgID'];
        }
        $batch = array(
            'SupplyBatchDate' => $data['SupplyBatchDate'],
            'SupplyOrgID' => $data['SupplyOrgID'],
            'SupplyDestOrgID' => $SupplyDestOrgID,
            'SupplyDestDoOrgID' => $data['SupplyDestDoOrgID'],
            'SupplyDestMillOrgID' => $data['SupplyDestMillOrgID'],
            "SupplyDestMillOtherName" => @$data['SupplyDestMillOtherName']=='' ? NULL : $data['SupplyDestMillOtherName'],
            'SupplyBatchNumber' => $data['SupplyBatchNumber'],
            'SupplyBatchStatus' => $data['SupplyBatchStatus'],
            'DeliveryDate' => $data['DeliveryDate'],
            'DestPO' => $data['DestPO'],
            'DestWeight' => $data['DestWeight'],
            'DestNumberPackage' => @$data['DestNumberPackage']=='' ? NULL : $data['DestNumberPackage'],
            'DestDriver' => $data['DestDriver'],
            'DestTransportID' => $data['DestTransportID'],
            'DestContainerNumber' => $data['DestContainerNumber']=='' ? NULL : $data['DestContainerNumber'],
            'DestTransportNumber' => $data['DestTransportNumber']=='' ? NULL : $data['DestTransportNumber'],
            'Notes' => @$data['Notes']=='' ? NULL : $data['Notes'],
            'CreatedBy' => $data['CreatedBy'],
            'StatusCode' => @$data['StatusCode']=='' ? 'active' : $data['StatusCode'],
            'DateCreated' => $data['DateCreated'],
            'DateUpdated' => $data['DateUpdated'],
            'LastModifiedBy' => $LastModifiedBy,
            'SupplyDestType' => $data['SupplyDestType'],
            'SupplyDestProcessType' =>$data['SupplyDestProcessType']
        );
        
        if($check_batch->num_rows() > 0){
            $SupplyBatchID = $check_batch->row()->SupplyBatchID;
            $this->db->where('SupplyBatchID', $SupplyBatchID);
            $this->db->update('ktv_tc_supplychain_batch', $batch);
            $return['status'] = true;
        }else{
            $this->db->insert('ktv_tc_supplychain_batch', $batch);
            $SupplyBatchID = $this->db->insert_id();
            $return['status'] = true;
        }

        if($return['status']==true){
            $return['SupplyBatchIDApp'] = $data['SupplyBatchIDApp'];
            $return['SupplyBatchID'] = $SupplyBatchID;
            $sql = "UPDATE ktv_tc_supplychain_transaction SET SupplyBatchID=NULL, ChangeLog=CONCAT('Last BatchID=', SupplyBatchID) WHERE SupplyBatchID=?";
            $this->db->query($sql, $SupplyBatchID);
            $i = 0;
            if(count($data['trans']) > 0){
                foreach ($data['trans'] as $key => $value) {
                    $value['SupplyBatchID'] = $SupplyBatchID;
                    $return['trans'][$i] = $this->_submitTransaction($SupplyBatchID, $value, FALSE, TRUE);
                    $i++;
                }
                //if(@$data['SupplyDestMillOrgID']!='' && @$data['SupplyDestMillOrgID']!='0' && @$data['SupplyDestMillOrgID']!='null' && @$data['SupplyDestMillOrgID']!=null && ($data['SupplyDestMillOrgID']=='9999' || $data['SupplyDestMillOrgID']!=9999) ){
                if($data['SupplyDestType']=='do' && $data['SupplyDestProcessType']=='mill'){
                    $this->_autoCreateBatchDO($SupplyBatchID, $data);
                }
            }else{
                $return['Transaction'] = array();
            }
        }
        if(@$return['ErrorCode']=='2'){
            $return['status'] = true;
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        } 
        return $return;
    }

    public function _notification($SupplyTransID, $data, $quality){
        //$sql_check1 = $this->db->query("SELECT * FROM sys_messagebird_log WHERE SupplyTransID=? OR (Request LIKE CONCAT('%',?,'%') AND ?!='')", array($SupplyTransID, $data['FakturNumber'], $data['FakturNumber']));
        $sql_check2 = $this->db->query("SELECT * FROM ktv_mobile_notification WHERE OrgID=? AND OrgType='Transaction'", array($SupplyTransID));

        $sql = "SELECT IFNULL(f.HandphoneType, sf.HandphoneType) HandphoneType, IFNULL(f.HandPhone, sf.HandPhone) Handphone, f.FarmerName FROM ktv_cocoa_farmer f LEFT JOIN ktv_tc_supplychain_farmer sf ON sf.FarmerID=f.FarmerID WHERE f.FarmerID=?";
        $farmer = $this->db->query($sql, array($data['SupplyID']))->result_array();

        $sql_check1 = $this->db->query("SELECT * FROM sys_log_nexmo WHERE SupplyTransID=? OR (request LIKE CONCAT('%',?,'%') AND request LIKE ?)", array($SupplyTransID, $data['FakturNumber'], $data['FakturNumber'], "%".@$farmer[0]['Handphone']."%"));
        if(@$data['BeanTypeID']=='1'){
            $BeanType = ' biji kering';
        }else if(@$data['BeanTypeID']=='1'){
            $BeanType = ' biji basah';
        }else{
            $BeanType = '';
        }
        $sql = "SELECT Name FROM view_supplychain_org WHERE SupplychainID=?";
        $collector = @$this->db->query($sql, array($data['SupplychainID']))->row()->Name;
        $collector = substr($collector, 0,9);
        $DateTransaction = date('H.i d-m-y', strtotime($data['DateTransaction']));
        $FakturNumber = $data['FakturNumber'];
        $Netto = $data['VolumeNetto'];
        $Harga = round(intval($data['ContractPrice']), 0);
        $TotalPayment = number_format(round($data['TotalPayment'], 0),0,'.',',');
        if(@$quality['Brix']==''){
            $Brix = '';
        }else{
            $Brix = round(floatval(@$quality['Brix']), 1);
        }
        if(@$quality['Waste']==''){
            $Waste = '';
        }else{
            $wst = floatval($quality['Waste'])/10;
            $Waste = $wst.'%';
        }
        $FarmerName = '';
        $fname = explode(' ', @$farmer[0]['FarmerName']);
        if(strlen($fname[0]) > 3){
            $FarmerName .= @$fname[0];
        }else{
            $FarmerName .= @$fname[0].' '.@$fname[1];
        }

        $insentif = round((floatval($data['NetPrice']) - floatval($data['ContractPrice']))*floatval($Netto), 0);
        $NettoKG = $Netto."KG";
        $MessageBody = "Yth,kakao yg dijual di CP $collector dg Reg.$FakturNumber,Berat $NettoKG,Waste $Waste,Brix $Brix,Harga Rp $Harga/KG dg insentif Rp $insentif. Ttl bayar Rp $TotalPayment";
        if($sql_check2->num_rows() == 0){
            if(@$farmer[0]['HandphoneType']=='1'){
                $sql = "SELECT * FROM ktv_mobile_notification WHERE OrgType='Transaction' AND OrgID=?";
                $check = $this->db->query($sql, array($SupplyTransID));
                if($check->num_rows() > 0){
                    return true;
                }
                $mobile_notification = array(
                    'PartnerID' => 9,
                    'FarmerID' => $data['SupplyID'],
                    'OrgID' => $SupplyTransID,
                    'OrgType' => 'Transaction',
                    'NotifTitle' => 'Farmer Transaction',
                    'NotifMessage' => $MessageBody,
                    'StatusCode' => 'active',
                    'DateCreated' => date('Y-m-d H:i:s')
                );
                $this->db->insert('ktv_mobile_notification', $mobile_notification);
            }
        }
        if($sql_check1->num_rows() == 0){
            if(@$farmer[0]['HandphoneType']=='2'){
                $phone = @$farmer[0]['Handphone'];
                if($phone!='' && $phone!='null' && $phone!='undefined' && strlen($phone) > 9){
                    if(preg_match("/^0/", $phone)) {
                        $phone = preg_replace('/^0/', '62', $phone);
                    } else {
                        if(!preg_match('/^(\+62|62)/',$phone)){
                            $phone = '62'. $phone;
                        } 
                    }
                    
                    if(preg_match("/^0/", $phone)) {
                        $phone = preg_replace('/^0/', '62', $phone);
                    } else {
                        if(!preg_match('/^(\+62|62)/',$phone)){
                            $phone = '62'. $phone;
                        } 
                    }
                    $phone = str_replace(' ', '', $phone);
                    //MESSAGE BIRD
                    /*$sql = "SELECT * FROM sys_messagebird_log WHERE SupplyTransID=?";
                    $check = $this->db->query($sql, array($SupplyTransID));
                    if($check->num_rows() > 0){
                        return true;
                    }
                    $method = 'POST';
                    $post = array(
                        'recipients' => $phone,
                        'originator' => 'FarmCloud',
                        'body' => $MessageBody
                    );
                    $url = 'https://rest.messagebird.com/messages';
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: AccessKey nz6Jz5M8um4rLEr95h8vgxiFv'
                    ));

                    curl_setopt($ch, CURLOPT_POSTFIELDS, (json_encode($post)));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $result = curl_exec($ch);
                    //require_once 'application/libraries/php-rest-api-master/autoload.php';
                    //$MessageBird = new \MessageBird\Client('nz6Jz5M8um4rLEr95h8vgxiFv');
                    //$Message = new \MessageBird\Objects\Message();
                    //$Message->originator = 'FarmCloud';
                    //$Message->recipients = array($farmer[0]['HandPhone']);
                    //$Message->body = $MessageBody;
                    //$r = $MessageBird->messages->create($Message);
                    //$result = json_encode($r);

                    $log = array(
                        'Method' => $method,
                        'Url' => $url,
                        'SupplyTransID' => $SupplyTransID,
                        'Request' => json_encode($post),
                        'Response' => $result
                    );
                    $this->db->insert('sys_messagebird_log', $log);*/

                    //NEXMO
                    $sql = "SELECT * FROM sys_log_nexmo WHERE SupplyTransID=?";
                    $check = $this->db->query($sql, array($SupplyTransID));
                    if($check->num_rows() > 0){
                        return true;
                    }
                    $this->send_sms_nexmo($SupplyTransID, $phone, $MessageBody);
                }
            }
        }
        return true;
    }

    public function _deleteTransaction($data) {
        $sql = "SELECT * FROM ktv_tc_supplychain_transaction WHERE SupplyTransID=?";
        $ct = $this->db->query($sql, array($data['SupplyTransID']));
        $return['status'] = true;
        $return['SupplyTransID'] = $data['SupplyTransID'];
        $return['SupplyTransIDApp'] = $data['SupplyTransIDApp'];
        if($ct->num_rows() > 0){
            $SupplyBatchID = $ct->row()->SupplyBatchID;
            $SupplyID = $ct->row()->SupplyID;
            $sql = "UPDATE ktv_tc_supplychain_transaction SET SupplyBatchID=NULL, SupplyID=NULL, LastModifiedBy=?, DateUpdated=?, ChangeLog=? WHERE SupplyTransID=?";
            $ChangeLog = "SupplyBatchID=$SupplyBatchID, SupplyID=$SupplyID";
            $DateUpdated = $data['DateUpdated']=='' ? date('Y-m-d H:i:s') : $data['DateUpdated'];
            $query = $this->db->query($sql, array($data['LastModifiedBy'], $DateUpdated, $ChangeLog, $data['SupplyTransID']));
            if($query){
                $insert = array(
                    'SupplyTransID' => $data['SupplyTransID'],
                    'SupplyBatchID' => $SupplyBatchID=='' ? NULL : $SupplyBatchID,
                    'SupplyID' => $SupplyID,
                    'DeleteReason' => isset($data['DeleteReason']) ? "" : $data['DeleteReason'],
                    'DeleteTime' => date('Y-m-d H:i:s'),
                    'DeleteBy' => isset($data['LastModifiedBy'])=='' ? $data['CreatedBy'] : $data['LastModifiedBy']
                );
                $this->db->insert('sys_log_delete_transaction', $insert);
            }else{
                $return['ErrorCode'] = 1; //Process Gagal
            }
        }else{
            $return['ErrorCode'] = 404;
        }
        $this->load->model('traceability/api/mfarmer');
        $SalesQuota = $this->mfarmer->_salesQuota($data['Farmer']['FarmerID'], $data['PartnerID']);
        $return['Farmer'] = array(
            'FarmerID' => $data['Farmer']['FarmerID'],
            'FarmerIDApp' => isset($data['Farmer']['FarmerIDApp'])=='' ? '' : $data['Farmer']['FarmerIDApp'],
            'Quota' => $SalesQuota['Quota'],
            'SalesQuota' => $SalesQuota['SalesQuota']
        );
        
        return $return;
    }

    public function _updateFarmer($farmer){
        $check_farmer = $this->db->query("SELECT * FROM ktv_cocoa_farmer WHERE FarmerID=?", array($farmer['FarmerID']));
        if($check_farmer->num_rows() > 0){
            //if($check->num_rows() > 0){
                $update['FarmerID'] = $farmer['FarmerID'];
                if($farmer['Handphone']!='' && $farmer['Handphone']!='null' && $farmer['Handphone']!='undefined'){
                    $update['HandPhone'] = $farmer['Handphone'];
                    if($farmer['HandphoneType']!='' && $farmer['HandphoneType']!='null' && $farmer['HandphoneType']!='undefined'){
                        $update['HandphoneType'] = $farmer['HandphoneType'];
                    }else{
                        $update['HandphoneType'] = 2;
                    }
                }
                if(@$farmer['PartnerID']=='9'){
                    $this->db->where('FarmerID', $farmer['FarmerID']);
                    $this->db->update('ktv_tc_supplychain_farmer', $update);
                }
                $this->db->where('FarmerID', $farmer['FarmerID']);
                $this->db->update('ktv_cocoa_farmer', $update);
            //}
            $FarmerID = $farmer['FarmerID'];
        }else{
            $this->load->model('traceability/api/mfarmer');
            $mfarmer = $this->mfarmer->_submitFarmer($farmer);
            $FarmerID = $mfarmer['FarmerID'];
        }
        return $FarmerID;
    }

    public function _getTransaction($get){
        $SupplychainID = @$get['sid'];
        $SupplyBatchID = @$get['SupplyBatchID'];
        if($SupplychainID==''){
            $a1 = '/*'; $a2 = '*/';
        }else{
            $a1 = ''; $a2 = '';
        }
        if($SupplyBatchID==''){
            $a3 = '/*'; $a4 = '*/';
        }else{
            $a3 = ''; $a4 = '';
        }
        $sql = "SELECT
                    st.SupplyTransID,
                    0 AS SupplyTransIDApp,
                    IFNULL(st.SupplychainID, '') SupplychainID,
                    IFNULL(st.SupplyBatchID, 0) SupplyBatchID,
                    st.DateTransaction,
                    st.SupplyType,
                    IFNULL(rft.TransTypeName,'') AS CertificationLabel,
                    st.SupplyID,
                    IFNULL(f.FarmerName, vso.Name)  SupplyName,
                    IFNULL(st.IsCetak, 0) IsCetak,
                    st.VolumeBruto AS VolumeBruto,
                    IFNULL(dtl.PackageID, 0) PackageID,
                    (IFNULL(st.FAQVolumePackage,0)*IFNULL(FAQNumberPackage,0)) AS PackageWeight,
                    st.FAQNumberPackage AS PackageNumber,
                    st.FAQVolumeNetto AS VolumeNetto,
                    st.FAQContractPrice AS ContractPrice,
                    st.TransportID,
                    st.FAQTransportFee AS TransportPrice,
                    st.FAQNetPrice AS NetPrice,
                    st.FAQTotalPayment AS TotalPayment,
                    st.FakturNumber,
                    st.InvoiceNumber,
                    st.DateCreated,
                    st.CreatedBy,
                    IFNULL(st.DateUpdated, st.DateCreated) AS DateUpdated,
                    IFNULL(st.LastModifiedBy, st.DateCreated) LastModifiedBy,
                    st.RegBy,
                    st.QualityBy,
                    st.PaymentBy,
                    st.QualityTime,
                    st.PaymentTime,
                    (st.WHTRate*st.FAQTotalPayment/100) Tax,
                    st.WHTRate,
                    st.BeanTypeID,
                    st.TransTypeID,
                    st.TransLabelID,
                    IFNULL(rtl.TransLabelName, '') TransLabelName,
                    st.DeductionPercentage,
                    st.DeductionWeight,
                    st.AutoTransNumber,
                    st.FFBCount
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_transaction_dtl dtl ON dtl.SupplyTransID=st.SupplyTransID
                    LEFT JOIN ref_trans_type rft ON rft.TransTypeID=st.TransTypeID
                    LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=st.SupplyID AND st.SupplyType!='Batch'
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN ref_trans_label rtl ON rtl.TransLabelID=st.TransLabelID
                WHERE 1=1
                    $a1 AND (st.SupplyBatchID IS NULL OR st.SupplyBatchID=0) AND st.SupplychainID=? $a2
                    $a3 AND st.SupplyBatchID=? $a4 
                    AND st.SupplyID IS NOT NULL AND st.SupplyID!=''
                GROUP BY st.SupplyTransID";
        $Q = $this->db->query($sql, array($SupplychainID, $SupplyBatchID));
        
        if($Q->num_rows() > 0){
            $data = $Q->result_array();
            $i = 0;
            foreach($data as $k=>$v){
                $sql = "SELECT 
                            stq.DetailID AS QualityID,
                            ref.`Name` AS QualityName,
                            ref.`Order` AS QualityOrder,
                            IFNULL(ref.MinValue, 0) AS QualityMin,
                            IFNULL(ref.`MaxValue`, 1000) AS QualityMax,
                            ref.FAQValue AS QualityStd,
                            ref.FAQFormula AS QualityFormula,
                            IFNULL(ref.IsFormVisible,1) AS Formvisible,
                            IFNULL(ref.IsPrintVisible,1) AS Printvisible,
                            IFNULL(ref.BeanTypeID, 2) AS BeanTypeID,
                            'decimal' AS QualityType,
                            IF(ref.FAQStatus IS NULL OR ref.FAQStatus='', 0, ref.FAQStatus) AS IsStatus,
                            IFNULL(ref.IsSample,0) AS IsSample,
                            IFNULL(ref.IsMandatory,0) AS IsMandatory,
                            stq.FAQResult AS `Value`,
                            stq.FAQReward AS Incentive,
                            IFNULL(stq.FAQStatus,'') AS `Status`
                        FROM ktv_tc_supplychain_transaction_quality stq 
                            LEFT JOIN ktv_tc_supplychain_quality_standard_detail ref ON ref.DetailID=stq.DetailID
                        WHERE stq.SupplyTransID=?";
                $B = $this->db->query($sql,array($v['SupplyTransID']));
                if($B->num_rows()){
                    $data[$i]['Quality'] = $B->result_array();
                } else {
                    $data[$i]['Quality'] = array();
                }
                $i++;
            }
        }else{
            $data = array();
        }
        return $data; 
    }

    public function _getBatch($get){
        $SupplychainID = @$get['sid'];
        if($SupplychainID==''){
            $a1 = '/*'; $a2 = '*/';
        }else{
            $a1 = ''; $a2 = '';
        }
        $SupplyDestOrgID = @$get['SupplyDestOrgID'];
        if($SupplyDestOrgID==''){
            $a3 = '/*'; $a4 = '*/';
        }else{
            $a3 = ''; $a4 = '';
        }
        $sql = "SELECT
                    sb.SupplyOrgID SupplychainID,
                    vso.Name SupplychainName,
                    sb.SupplyBatchID,
                    IFNULL(sb.BeanTypeID, '') AS BeanTypeID,
                    sb.VolumeBruto,
                    sb.VolumeNetto,
                    sb.DateCreated,
                    sb.CreatedBy,
                    IFNULL(sb.DateUpdated, sb.DateCreated) DateUpdated,
                    IFNULL(sb.LastModifiedBy, sb.CreatedBy) LastModifiedBy,
                    0 SupplyBatchIDApp,
                    sb.SupplyDestOrgID,
                    IFNULL(sb.SupplyBatchStatus, '')  SupplyBatchStatus,
                    sb.SupplyBatchNumber,
                    IFNULL(sb.TransTypeID, '') AS TransTypeID,
                    sb.TransLabelID,
                    sb.SupplyBatchDate,
                    sb.DeliveryDate,
                    sb.ArrivalEstimation,
                    sb.DestPO,
                    sb.DestWeight,
                    IFNULL(sb.DestJumlahKarung, 0) AS DestJumlahKarung,
                    IFNULL(sb.DestDriver, '') AS DestDriver,
                    IFNULL(sb.DestDriverAddress, '') AS DestDriverAddress,
                    IFNULL(sb.DestDriverJabatan, '') AS DestDriverJabatan,
                    IFNULL(sb.DestNoPolisi, '') AS DestNoPolisi,
                    IFNULL(sb.DestTransport, '') AS DestTransport,
                    IFNULL(sb.DestDriverHp, '') AS DestDriverHp,
                    sb.BeanTypeID,
                    sb.AutoBatchNumber
                FROM
                    ktv_tc_supplychain_batch sb
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=sb.SupplyOrgID
                WHERE
                    $a1 (sb.SupplyBatchStatus NOT IN ('Sent', 'Delivered') OR sb.SupplyBatchStatus IS NULL) AND sb.SupplyOrgID=? $a2 
                    $a3 (sb.SupplyBatchStatus IN ('Sent')) AND sb.SupplyDestOrgID=? $a4
                ";
        $Q = $this->db->query($sql, array($SupplychainID, $SupplyDestOrgID));
        //echo '<pre>'.$this->db->last_query();die;
        if($Q->num_rows() > 0){
            $data = $Q->result_array();
            $i = 0;
            foreach($data as $k=>$v){
                $driver1 = explode('|', $v['DestDriver']);
                $driver2 = explode('|', $v['DestDriverAddress']);
                $driver3 = explode('|', $v['DestDriverJabatan']);
                $driver4 = explode('|', $v['DestNoPolisi']);
                $driver5 = explode('|', $v['DestTransport']);
                $driver6 = explode('|', $v['DestDriverHp']);
                
                for($j=0; $j < count($driver1); $j++){
                    $data[$i]['Driver'][$j]['DriverName'] = $driver1[$j];
                    $data[$i]['Driver'][$j]['DriverAddress'] = $driver2[$j];
                    $data[$i]['Driver'][$j]['DriverPos'] = $driver3[$j];
                    $data[$i]['Driver'][$j]['PoliceNumber'] = $driver4[$j];
                    $data[$i]['Driver'][$j]['VehicleType'] = $driver5[$j];
                    $data[$i]['Driver'][$j]['DriverPhoneNumber'] = $driver6[$j];
                }
                
                unset($data[$i]['DestDriver']);
                unset($data[$i]['DestDriverAddress']);
                unset($data[$i]['DestDriverJabatan']);
                unset($data[$i]['DestNoPolisi']);
                unset($data[$i]['DestTransport']);
                unset($data[$i]['DestDriverHp']);
                
                $trans = array('SupplyBatchID'=>$v['SupplyBatchID']);
                $data[$i]['Transaction'] = $this->_getTransaction($trans);
                
                $i++;
            }
        }else{
            $data = array();
        }
        return $data; 
    }

    function _generateTransNumber($SupplychainID, $date){
        $date = date("Y-m-d",strtotime($date));
        $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_transaction WHERE SupplychainID=? AND DATE_FORMAT(DateTransaction, '%Y-%m-%d') = ?";
        $query = $this->db->query($sql, array($SupplychainID, "$date"));
        
        $total = intval(@$query->row()->total)+1;

        $trans_number = 'X'.sprintf("%04d", $SupplychainID).date('Ymd', strtotime($date)).sprintf("%06d", $total);
        return $trans_number;
    }

    function _generateTransNumberNonFarmer($TransNumber, $SupplychainID, $date){
        $date = date("Y-m-d",strtotime($date));
        $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_transaction WHERE TransNumber =? AND SupplychainID=? AND DATE_FORMAT(DateTransaction, '%Y-%m-%d') = ?";
        $query = $this->db->query($sql, array($TransNumber, $SupplychainID, "$date"));
        
        $total = intval(@$query->row()->total)+1;

        $trans_number = 'X'.sprintf("%04d", $SupplychainID).date('Ymd', strtotime($date)).sprintf("%06d", $total);
        return $trans_number;
    }

    function _generateBatchNumber($SupplyOrgID, $SupplyBatchDate){
        //komen 4-8-2021 code asli
        
        // $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_batch WHERE SupplyOrgID=? AND SupplyBatchDate LIKE ?";
        // $query = $this->db->query($sql, array($SupplyOrgID, "%$SupplyBatchDate%"));

        // komen 4-8-2021 revisi

        $SupplyBatchDate = date('Y-m-d', strtotime($SupplyBatchDate));
        $sql   = "SELECT COUNT(*) total FROM ktv_tc_supplychain_batch WHERE SupplyOrgID = ? AND DATE(SupplyBatchDate) = ?";
        $query = $this->db->query($sql, array($SupplyOrgID, $SupplyBatchDate));
        $total = intval(@$query->row()->total)+1;

        $batch_number = 'XB'.sprintf("%04d", $SupplyOrgID).date('Ymd', strtotime($SupplyBatchDate)).sprintf("%06d", $total);


        return $batch_number;
    }

    private function _autoCreateBatchDO($SupplyBatchID, $data){
        $batch = $this->db->query("SELECT * FROM ktv_tc_supplychain_batch WHERE SupplyBatchID=?", array($SupplyBatchID))->result_array();
        $batch[0]['SupplyOrgID'] = $batch[0]['SupplyDestDoOrgID'];
        $batch[0]['SupplyDestOrgID'] = $batch[0]['SupplyDestMillOrgID'];
        unset($batch[0]['SupplyBatchID']);
        unset($batch[0]['SupplyDestDoOrgID']);
        unset($batch[0]['SupplyDestMillOrgID']);
        unset($batch[0]['SupplyBatchStatus']);

        $check_batch = $this->db->query("SELECT SupplyTransID, SupplyBatchID, SUM(VolumeBruto) VolumeBruto, SUM(VolumeNetto) VolumeNetto FROM ktv_tc_supplychain_transaction WHERE SupplyID=? AND SupplyType='Batch'", array($SupplyBatchID));
        if(@$check_batch->row()->SupplyTransID!=''){
            $batch_update = $batch[0];
            $this->db->where('SupplyBatchID', $check_batch->row()->SupplyBatchID);
            $this->db->update('ktv_tc_supplychain_batch', $batch_update);

            $update_trans = array(
                'VolumeBruto' => $batch[0]['DestWeight'],//$check_batch->row()->VolumeBruto,
                'VolumeNetto' => $batch[0]['DestWeight'],//$check_batch->row()->VolumeNetto,
                'LastModifiedBy' => $data['CreatedBy'],
                'DateUpdated' => date('Y-m-d H:i:s')
            );
            $this->db->where('SupplyTransID', $check_batch->row()->SupplyTransID);
            $this->db->update('ktv_tc_supplychain_transaction', $update_trans);
        }else{
            $batch[0]['SupplyBatchStatus'] = 'Sent';
            $this->db->insert('ktv_tc_supplychain_batch', $batch[0]);
            $SupplyBatchID_DO = $this->db->insert_id();

            $insert_trans = array(
                'SupplyBatchID' => $SupplyBatchID_DO,
                'SupplychainID' => $data['SupplyDestDoOrgID'],
                'VolumeBruto' => $batch[0]['DestWeight'],//$check_batch->row()->VolumeBruto,
                'VolumeNetto' => $batch[0]['DestWeight'],//$check_batch->row()->VolumeNetto,
                'TransNumber' => $this->_generateTransNumber($data['SupplyDestDoOrgID'], $data['DeliveryDate']),
                'DateTransaction' => $data['DeliveryDate'],
                'SupplyType' => 'Batch',
                'SupplyID' => $SupplyBatchID,
                'StatusCode' => 'active',
                'CreatedBy' => $data['CreatedBy'],
                'DateCreated' => date('Y-m-d H:i:s')
            );
            $this->db->insert('ktv_tc_supplychain_transaction', $insert_trans);

            $this->db->query("UPDATE ktv_tc_supplychain_batch SET SupplyBatchStatus='Delivered' WHERE SupplyBatchID=?", array($SupplyBatchID));
        }
        return true;
    }

    //--NEW--//
    public function _submitBatchNew($data){
        $return['ErrorCode'] = '';
        /*if($data['SupplyBatchNumber']==null || $data['SupplyBatchNumber']=='null'){
            $data['SupplyBatchNumber'] = '';
        }*/

        //di komen 4-8-2021

        // if(@$data['AutoBatchNumber']!=''){
        //     $where = "AND (
        //                     /*(SupplyBatchID=? OR (SupplyBatchNumber=?))*/
        //                      (AutoBatchNumber!='' AND AutoBatchNumber=?)
        //                 )";
        // }else{
        //     $where = "AND (
        //                     (SupplyBatchID=? OR (SupplyBatchNumber=?))
        //                     /* (AutoBatchNumber!='' AND AutoBatchNumber=?) */
        //                 )";
        // }

        $where = "AND (
                            (SupplyBatchID=? OR (SupplyBatchNumber=?))
                            /* (AutoBatchNumber!='' AND AutoBatchNumber=?) */
                        )";

        $this->db->trans_begin();
        $return = array();
        $sql = "SELECT * FROM ktv_tc_supplychain_batch 
                WHERE SupplyBatchID > 0 $where";
        $check_batch = $this->db->query($sql, array(@$data['SupplyBatchID'], @$data['SupplyBatchNumber'], @$data['AutoBatchNumber']));
        
        if($data['LastModifiedBy']==""){$LastModifiedBy=NULL;}else{$LastModifiedBy=$data['LastModifiedBy'];}
        //if(@$data['TransTypeID']==""){$data['TransTypeID']=NULL;}
        if($data['SupplyDestDoOrgID']=='0' && $data['SupplyDestMillOrgID']!='' && $data['SupplyDestMillOrgID']!='0'){
            $SupplyDestOrgID = $data['SupplyDestMillOrgID'];
        }else{
            $SupplyDestOrgID = $data['SupplyDestDoOrgID'];
        }

        $batch = array(
            'SupplyBatchDate' => $data['SupplyBatchDate'],
            'SupplyOrgID' => $data['SupplyOrgID'],
            'SupplyDestOrgID' => $SupplyDestOrgID,
            'SupplyDestDoOrgID' => $data['SupplyDestDoOrgID'],
            'SupplyDestMillOrgID' => $data['SupplyDestMillOrgID'],
            "SupplyDestMillOtherName" => @$data['SupplyDestMillOtherName']=='' ? NULL : $data['SupplyDestMillOtherName'],
            'SupplyBatchStatus' => $data['SupplyBatchStatus'],
            'SupplyBatchCategory' => $data['SupplyBatchCategory'],
            'IsStorage' => !empty(@$data['IsStorage']) ? @$data['IsStorage']: null,
            'RemainingFFBWeight' => !empty(@$data['RemainingFFBWeight']) ? @$data['RemainingFFBWeight']: null,
            'RemainingBrondolanWeight' => !empty(@$data['RemainingBrondolanWeight']) ? @$data['RemainingBrondolanWeight']: null,
            'RemainingWeight' => $data['RemainingWeight'],
            'DeliveryDate' => $data['DeliveryDate'],
            'ReceiveWeight' => $data['ReceivedWeight'],
            'ReceivedDate' => $data['ReceivedDate'],
            'DestPO' => $data['DestPO'],
            'Weather' => $data['Weather'],
            'DestWeight' => $data['DestWeight'],
            'DestWeight' =>  @$data['DestWeight']=='' ? NULL : $data['DestWeight'],
            'DestNumberPackage' => @$data['DestNumberPackage']=='' ? NULL : $data['DestNumberPackage'],
            'DestDriver' => $data['DestDriver'],
            'SMESPCodeID' => $data['SMESPCodeID'],
            'DestTransportID' => $data['DestTransportID'],
            'DestContainerNumber' => $data['DestContainerNumber']=='' ? NULL : $data['DestContainerNumber'],
            'DestTransportNumber' => $data['DestTransportNumber']=='' ? NULL : $data['DestTransportNumber'],
            'Notes' => @$data['Notes']=='' ? NULL : $data['Notes'],
            'CreatedBy' => $data['CreatedBy'],
            'StatusCode' => @$data['StatusCode']=='' ? 'active' : $data['StatusCode'],
            'DateCreated' => $data['DateCreated'],
            'DateUpdated' => $data['DateUpdated'],
            'LastModifiedBy' => $LastModifiedBy,
            'SupplyDestType' => $data['SupplyDestType'],
            'SupplyDestProcessType' =>$data['SupplyDestProcessType'],
            'AutoBatchNumber' =>@$data['AutoBatchNumber']
        );

       
        // if($data['DestWeight'] > 0 AND $data['DestWeight'] != '' AND $data['SupplyDestMillOrgID'] == 0){
        //     $persen20       = $data['VolumeNetto']-((20/100)*$data['VolumeNetto']);
        //     $persen20Plus   = $data['VolumeNetto']+((20/100)*$data['VolumeNetto']);

        //     if($data['DestWeight'] < $persen20){ // Jika DestWeight Lebih Kecil 20% dari VolumeNetto
        //         $return['status'] = false;
        //         $return['ErrorCode'] = "3";
        //         return $return;
        //     }elseif($data['DestWeight'] > $persen20Plus){ // Jika DestWeight Lebih Besar 20% dari VolumeNetto
        //         $return['status'] = false;
        //         $return['ErrorCode'] = "4";
        //         return $return;
        //     }            
        // }
        
        if($check_batch->num_rows() > 0){
            //Di bongkar dulu jika statusnya masih Open
            if($check_batch->row()->SupplyBatchStatus=='Open'){
                $this->db->query("UPDATE ktv_tc_supplychain_transaction SET SupplyBatchID=NULL WHERE SupplyBatchID=?", array($check_batch->row()->SupplyBatchID));
                // $batch["SupplyBatchNumber"]  = $this->_generateBatchNumber($data['SupplyOrgID'],$data['SupplyBatchDate']);
                
            }

            //Jika masalah tanggal sudah beres dari aplikasinya, maka bisa hilangkan status Open
            if($check_batch->row()->SupplyBatchStatus=='Open' || ($data['DateUpdated'] > $check_batch->row()->DateUpdated && $check_batch->row()->SupplyBatchStatus!='Sent' && $check_batch->row()->SupplyBatchStatus!='Delivered' && $check_batch->row()->SupplyBatchStatus!='') || (($check_batch->row()->SupplyBatchStatus!='Sent' && $check_batch->row()->SupplyBatchStatus!='Delivered' && $check_batch->row()->SupplyBatchStatus!='') && $data['SupplyBatchStatus']=='Sent') ){
                $SupplyBatchID = $check_batch->row()->SupplyBatchID;
                // $batch["SupplyBatchNumber"]  = $this->_generateBatchNumber($data['SupplyOrgID'],$data['SupplyBatchDate']);
                    
                $this->db->where('SupplyBatchID', $SupplyBatchID);
                $this->db->update('ktv_tc_supplychain_batch', $batch);
                $return['status'] = true;
            } elseif($check_batch->row()->SupplyBatchStatus=='Closed'){
                $SupplyBatchID = $check_batch->row()->SupplyBatchID;
                // $batch["SupplyBatchNumber"]  = $this->_generateBatchNumber($data['SupplyOrgID'],$data['SupplyBatchDate']);
                $SBID  = $SupplyBatchID;
                
                $this->db->where('SupplyBatchID', $SBID);
                $this->db->update('ktv_tc_supplychain_batch', $batch);
                $return['status'] = true;
            } else{
                $checkBatchUpdate = $this->_checkBatchUpdate($check_batch->row()->SupplyBatchID, $data);
                //echo "if($checkBatchUpdate==2 && ".$data['DateUpdated']." > ".$check_batch->row()->DateUpdated."){ ";die;
                if($checkBatchUpdate==2 && $data['DateUpdated'] <= $check_batch->row()->DateUpdated){ //batch gk ada update
                    $SupplyBatchID = $check_batch->row()->SupplyBatchID;
                    $SupplyBatchIDApp = $data['SupplyBatchIDApp'];
                    $return['SupplyBatchIDApp'] = $data['SupplyBatchIDApp'];
                    $return['SupplyBatchID'] = $SupplyBatchID;
                    $return['ErrorCode'] = 2;
                    $return['status'] = false;
                }else{
                    //batch ada yg ketinggalan
                    $SupplyBatchID = $check_batch->row()->SupplyBatchID;
                    $return['status'] = true;
                }
            }

            $return['SupplyBatchNumber'] = $data['SupplyBatchNumber'];
        }else{
            unset($data['SupplyBatchNumber']);

            $batch["SupplyBatchDate"];

            $batch["SupplyBatchNumber"]  = $this->_generateBatchNumber($data['SupplyOrgID'],$data['SupplyBatchDate']);
            $this->db->insert('ktv_tc_supplychain_batch', $batch);
            $SupplyBatchID = $this->db->insert_id();
            $return['status'] = true;
        }
        
        if($return['status']==true){
            $return['SupplyBatchIDApp'] = $data['SupplyBatchIDApp'];
            $return['SupplyBatchNumber'] = ($batch["SupplyBatchNumber"] == '' ? $data["SupplyBatchNumber"] : $batch["SupplyBatchNumber"]);
            $return['SupplyBatchID'] = $SupplyBatchID;
            //$sql = "UPDATE ktv_tc_supplychain_transaction SET SupplyBatchID=NULL, ChangeLog=CONCAT('Last BatchID=', IFNULL(SupplyBatchID, '')) WHERE SupplyBatchID=?";
            //$this->db->query($sql, $SupplyBatchID);
            $i = 0;
            
            $checkTrans = $this->_checkTrans($SupplyBatchID, @$data['VolumeNetto'], @$data['DestWeight'], @$data['AutoBatchNumber']);
            if(count($data['trans']) > 0 && ($checkTrans==true OR $data['SupplyBatchStatus']=='Open' OR $data['SupplyBatchStatus']=='Closed')){
                foreach ($data['trans'] as $key => $value) {

                    $value['SupplyBatchID'] = $SupplyBatchID;
                    
                    $return['Transaction'][$i] = $this->_submitTransactionNew($value, FALSE, FALSE, $SupplyBatchID);
                    $i++;
                }
            }else{
                $return['Transaction'] = array();
            }
        }
        if(@$return['ErrorCode']=='2'){
            $return['status'] = true;
        }else{
            if(@$data['SupplyDestType']=='do' && @$data['SupplyDestProcessType']=='mill'){
                $dataAuto = $data;
                $dataAuto['AutoBatchNumber'] = 'Auto-'.@$data['AutoBatchNumber'];
                // $this->_autoCreateBatchDO($SupplyBatchID, $dataAuto);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        }

        return $return;
    }

    function _checkBatchUpdate($SupplyBatchID, $data){
        $total_netto = $this->db->query("SELECT IFNULL(SUM(VolumeNetto),0) VolumeNetto FROM ktv_tc_supplychain_transaction WHERE SupplyBatchID=?", array($SupplyBatchID))->row()->VolumeNetto;
        if(floatval($total_netto)!= floatval(@$data['DestWeight'])){
            $ret = 1;
        }else{
            $ret = 2;
        }
        return $ret;
    }

    public function _submitTransactionNew($data, $db_transaction=false, $DateUpdated=false, $SupplyBatchID=NULL) {
        $data['SupplybaseCategoryID'] = $this->_getSupplybaseCategory($data);
        $return['ErrorCode'] = ''; 
        if($db_transaction){
            $this->db->trans_begin();
        }
        $return['status'] = false;
        //$sql = "SELECT * FROM ktv_tc_supplychain_transaction WHERE ((SupplyTransID=? AND SupplyTransID > 0) OR (AutoTransNumber=? AND AutoTransNumber!='')) AND SupplyID IS NOT NULL";
        //$ct = $this->db->query($sql, array($data['SupplyTransID'], @$data['AutoTransNumber']));
        
        if(@$data['AutoTransNumber']==''){
            $a1 = ''; $a2 = '';
            $a3 = '/*'; $a4 = '*/';
        }else{
            $a1 = '/*'; $a2 = '*/';
            $a3 = ''; $a4 = '';
        }
        
        if($data['SupplyType'] == 'Farmer'){
            $sql = "SELECT * 
            FROM 
                ktv_tc_supplychain_transaction 
            WHERE 1=1
                AND TransNumber = '$data[TransNumber]'
                AND SupplyTransID = '$data[SupplyTransID]'
                AND SupplyID = '$data[SupplyID]' AND SupplyType= '$data[SupplyType]'";
            $ct = $this->db->query($sql);
            
        } else {
            $sql = "SELECT * 
            FROM 
                ktv_tc_supplychain_transaction 
            WHERE 1=1
                AND TransNumber = '$data[TransNumber]'
                AND SupplyTransID = '$data[SupplyTransID]' AND SupplychainID = '$data[SupplychainID]' AND SupplyType= '$data[SupplyType]'";
            $ct = $this->db->query($sql);
            // echo '<pre>'.$this->db->last_query();die;
        }
       
        if($ct->num_rows() > 0){
            //Jika masalah tanggal sudah beres dari aplikasinya, maka bisa hilangkan status Open
            $SupplyBatchStatus = @$this->db->query("SELECT SupplyBatchStatus FROM ktv_tc_supplychain_batch WHERE SupplyBatchID > 0 AND SupplyBatchID=?", array($ct->row()->SupplyBatchID))->row()->SupplyBatchStatus;
            if($SupplyBatchStatus=='Open' || $SupplyBatchStatus=='' || $SupplyBatchStatus=='Closed'){
                $DateUpdated = TRUE;
            }
           
            if(strtotime($data['DateUpdated']) > strtotime($ct->row()->DateUpdated) OR $DateUpdated==TRUE OR (floatval($data['VolumeBruto']) > 0 && floatval($ct->row()->VolumeBruto)==0) ){
                $data['SupplyTransID'] = $ct->row()->SupplyTransID;
                //if($ct->row()->IsCetak!='1'){
                
                $sql = "SELECT b.MemberID, b.FarmerCategory, b.Latitude, b.Longitude
                        FROM 
                            ktv_members b
                        LEFT JOIN
                            ktv_tc_supplychain_farmer sf on sf.FarmerID = b.MemberID
                        WHERE 
                            b.MemberID = '$data[SupplyID]'";
        
                $query = $this->db->query($sql);
                
                $Untraceable['MemberID'] = $query->result_array();
                
                if(!empty($Untraceable['MemberID'])){
                    foreach($Untraceable['MemberID'] as $k =>$v){
                        $cekFarmUntraceable = array(
                            'FarmerCategory' => $v['FarmerCategory'],
                            'Latitude' => $v['Latitude'],
                            'Longitude' => $v['Longitude'],
                        );
                    }
                }
                
                $dataUntraceable = $cekFarmUntraceable;
                
                if($dataUntraceable['FarmerCategory'] == 'Unmapped' && $dataUntraceable['Latitude'] == '' && $dataUntraceable['Longitude'] == ''){
                    $data['isTraceable'] = 'NO'; 
                }elseif($dataUntraceable['FarmerCategory'] == 'Mapped' && $dataUntraceable['Latitude'] == '' && $dataUntraceable['Longitude'] == ''){
                    $data['isTraceable'] = 'NO'; 
                }elseif($dataUntraceable['FarmerCategory'] == 'Mapped' && $dataUntraceable['Latitude'] != '' && $dataUntraceable['Longitude'] != ''){
                    $data['isTraceable'] = 'YES';
                } else {
                    $data['isTraceable'] = 'YES';
                }
            
                // if($data['SupplyType'] == 'NonFarmer'){
                //     $data['TransNumber'] = $this->_generateTransNumberNonFarmer($data['TransNumber'],$data['SupplychainID'],$data['DateTransaction']); 
                // }
                
                // $data['TransNumber'] = $this->_generateTransNumber($data['SupplychainID'],$data['DateTransaction']); 

                $trans = $this->_updateTransactionNew($data);
                //}else{
                    //$trans['id'] = $ct->row()->SupplyTransID;
                    //$trans['quality'] = array();
                //}
                $return['status'] = true;
            }else{
                $return['status'] = false;
                $return['SupplyTransID'] = $ct->row()->SupplyTransID;
                $return['SupplyTransIDApp'] = $data['SupplyTransIDApp'];
                $return['ErrorCode'] = 2;
            }
            $balance = false;
        }else{  
            // echo "add";exit;
            unset($data['TransNumber']);

            if($data['SupplyType'] == 'NonFarmer'){
                $data['TransNumber'] = $this->_generateTransNumberNonFarmer($data['TransNumber'],$data['SupplychainID'],$data['DateTransaction']); 
            }

            $data['TransNumber'] = $this->_generateTransNumber($data['SupplychainID'],$data['DateTransaction']); 

            $sql = "SELECT b.MemberID, b.FarmerCategory, b.Latitude, b.Longitude
                    FROM 
                        ktv_members b
                    LEFT JOIN
                        ktv_tc_supplychain_farmer sf on sf.FarmerID = b.MemberID
                    WHERE 
                        b.MemberID = '$data[SupplyID]'";
            
            $query = $this->db->query($sql);
            
            $Untraceable['MemberID'] = $query->result_array();
            
            if(!empty($Untraceable['MemberID'])){
                foreach($Untraceable['MemberID'] as $k =>$v){
                    $cekFarmUntraceable = array(
                        'FarmerCategory' => $v['FarmerCategory'],
                        'Latitude' => $v['Latitude'],
                        'Longitude' => $v['Longitude'],
                    );
                }
            }
            
            $dataUntraceable = $cekFarmUntraceable;
            
            if($dataUntraceable['FarmerCategory'] == 'Unmapped' && $dataUntraceable['Latitude'] == '' && $dataUntraceable['Longitude'] == ''){
                $data['isTraceable'] = 'NO'; 
            }elseif($dataUntraceable['FarmerCategory'] == 'Mapped' && $dataUntraceable['Latitude'] == '' && $dataUntraceable['Longitude'] == ''){
                $data['isTraceable'] = 'NO'; 
            }elseif($dataUntraceable['FarmerCategory'] == 'Mapped' && $dataUntraceable['Latitude'] != '' && $dataUntraceable['Longitude'] != ''){
                $data['isTraceable'] = 'YES';
            } else {
                $data['isTraceable'] = 'YES';
            }
            
            $trans = $this->_insertTransactionNew($data);
            $return['status'] = true;
            $balance = false;
        }

        if($return['status']==true){
            
            $return['SupplyTransID'] = $trans['id'];
            $return['SupplyTransIDApp'] = $data['SupplyTransIDApp'];
            $return['TransNumber'] = ($trans["TransNumber"] == '' ? $data["TransNumber"] : $batch["TransNumber"]);
            $return['Quality'] = $trans['quality'];
            //if($data['SupplyType']!='' && $data['SupplyType']!='Batch' && (float)$data['VolumeNetto'] > 0){
                //$notif = $this->_notification($trans['id'], $data, $trans['quality']);
            //}
        }
        // if($data['SupplyType']!='Batch'){
        //     $this->load->model('traceability/api/mfarmer');
        //     $SalesQuota = $this->mfarmer->_salesQuota($FarmerID, $data['PartnerID']);
        //     $return['Farmer'] = array(
        //         'FarmerID' => $FarmerID,
        //         'FarmerIDApp' => @$data['Farmer']['FarmerIDApp']=='' ? '' : $data['Farmer']['FarmerIDApp'],
        //         'Quota' => $SalesQuota['Quota'],
        //         'SalesQuota' => $SalesQuota['SalesQuota']
        //     );

        //     if($balance==true){
        //         $this->db->query("UPDATE ktv_tc_supplychain_transaction SET FAQVolumeBalance=? WHERE SupplyTransID=?", array($SalesQuota['SalesQuota'], $trans['id']));  
        //     }
        // }

        if($return['ErrorCode'] != ''){
            $return['status'] = true;
        }else{
            unset($return['ErrorCode']);
        }
        
        if($SupplyBatchID!='' && $SupplyBatchID!='0' && $SupplyBatchID!=0 && $SupplyBatchID!=NULL){
            $update_batchid = array('SupplyBatchID' => $SupplyBatchID);
            $this->db->where('SupplyTransID', $return['SupplyTransID']);
            $this->db->update('ktv_tc_supplychain_transaction', $update_batchid);
        }

        if($db_transaction){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
            }
        }
        return $return;
    }

    private function _insertTransactionNew($data) {
        
        if($data['SupplyType'] == 'Batch'){ 
            $MillID = $data['MillID'];
            $MillOther = $data['MillOther'];
            $DOID = $data['DOID'];
            $DOOther = $data['DOOther'];
            $AgentID = $data['AgentID'];
            $AgentOther = $data['AgentOther'];
            $AgentOtherNik = $data['AgentOtherNik'];
            $AgentOtherSurvey = $data['AgentOtherSurvey'];
        } else {
            $MillID     = NULL;
            $MillOther  = NULL;
            $DOID       = NULL;
            $DOOther    = NULL;
            $AgentID    = NULL;
            $AgentOther = NULL;
            $AgentOtherNik = NULL;
            $AgentOtherSurvey = NULL;
        }

        if($data['SupplyType '] == 'Delivery'){
            
            $id = $data['SupplyID'];

            $update = array(
                'DeliveryStatusID'      => '4',
                'LastModifiedBy'        => $data['LastModifiedBy'],
                'DateUpdated'           => $data['DateUpdated']
            );

            $this->db->where('DeliveryID', $id);
            $this->db->update('ktv_tc_supplychain_delivery', $update);
            
            //ada insert ke transaction detail dan reception di bawah sini nanti
        }

        $insert = array(
            'SupplyBatchID' => @$data['SupplyBatchID']=='' || @$data['SupplyBatchID']=='0' ? NULL : $data['SupplyBatchID'],
            'SupplychainID' => $data['SupplychainID'],
            'TransNumber' => $data['TransNumber'],
            'DateTransaction' => $data['DateTransaction'],
            'SupplyType' => $data['SupplyType'],
            'SupplyID' => $data['SupplyID'],
            'MillID' => $MillID,
            'MillOther'=> $MillOther,
            'DOID' => $DOID,
            'DOOther' => $DOOther,
            'AgentID' => $AgentID,
            'AgentOther' => $AgentOther,
            'AgentOtherNik' => $AgentOtherNik,
            'AgentOtherSurvey' => $AgentOtherSurvey,
            'Palmtypeid' => @$data['Palmtypeid']=='' || @$data['Palmtypeid']=='0' ? NULL : $data['Palmtypeid'],
            'FFBGrossWeight' => @$data['FFBGrossWeight']=='' || @$data['FFBGrossWeight']=='0' ? NULL : $data['FFBGrossWeight'],
            'FFBNettWeight' => @$data['FFBNettWeight']=='' || @$data['FFBNettWeight']=='0' ? NULL : $data['FFBNettWeight'],
            'FFBPrice' => @$data['FFBPrice']=='' || @$data['FFBPrice']=='0' ? NULL : $data['FFBPrice'],
            'FFBTotalPayment' => @$data['FFBTotalPayment']=='' || @$data['FFBTotalPayment']=='0' ? NULL : $data['FFBTotalPayment'],
            'BrondolanNetWeight' => @$data['BrondolanNetWeight']=='' || @$data['BrondolanNetWeight']=='0' ? NULL : $data['BrondolanNetWeight'],
            'BrondolanGrossWeight' => @$data['BrondolanGrossWeight']=='' || @$data['BrondolanGrossWeight']=='0' ? NULL : $data['BrondolanGrossWeight'],
            'BrondolanPrice' => @$data['BrondolanPrice']=='' || @$data['BrondolanPrice']=='0' ? NULL : $data['BrondolanPrice'],
            'BrondolanTotalPayment' => @$data['BrondolanTotalPayment']=='' || @$data['BrondolanTotalPayment']=='0' ? NULL : $data['BrondolanTotalPayment'],
            'PlantationNr' => $data['PlantationNr'],
            'CollectpointID' => $data['CollectpointID'],
            'Bunches' => $data['Bunches'],
            'VolumeBruto' => $data['VolumeBruto'],
            'VolumeNetto' => $data['VolumeNetto'],
            'VolumeCutting' => $data['VolumeCutting'],
            'PackageID' => $data['PackageID'],
            'PackageNumber' => $data['PackageNumber'],
            'PackageWeight' => $data['PackageWeight'],
            'TransStatusID' => $data['TransStatusID'],
            'ContractPrice' => $data['ContractPrice'],
            'NetPrice' => $data['NetPrice'],
            'InvoiceNumber' => $data['InvoiceNumber'],
            'DiscountPrice' => $data['DiscountPrice'],
            'TotalPayment' => $data['TotalPayment'],
            'PaymentReduction' => @$data['PaymentReduction']=='' || @$data['PaymentReduction']=='0' ? NULL : $data['PaymentReduction'],
            'PaymentPaid' => @$data['PaymentPaid']=='' || @$data['PaymentPaid']=='0' ? NULL : $data['PaymentPaid'],
            'Longitude' => @$data['Longitude']=='' || @$data['Longitude']=='0' ? NULL : $data['Longitude'],
            'Latitude' => @$data['Latitude']=='' || @$data['Latitude']=='0' ? NULL : $data['Latitude'],
            'Notes' => $data['Notes'],
            'DateCreated' => $data['DateCreated'],
            'CreatedBy' => $data['CreatedBy'],
            'DateUpdated' => $data['DateUpdated'],
            'LastModifiedBy' => $data['LastModifiedBy'],
            'DeductionPercentage' => @$data['DeductionPercentage'],
            'DeductionWeight' => $data['DeductionWeight'],
            'AutoTransNumber' => @$data['AutoTransNumber'],
            'FFBCount' => @$data['FFBCount'],
            'Bunches' => @$data['Bunches'],
            'SupplybaseCategoryID' => $data['SupplybaseCategoryID'],
            'SupplyBatchSourceType' => $data['SupplyBatchSourceType'],
            'SupplyBatchType' => $data['SupplyBatchType'],
            'isTraceable' => $data['isTraceable']
        );
        
        $this->db->insert('ktv_tc_supplychain_transaction', $insert);
        $id = $this->db->insert_id();

        //$quality = $this->_insertTransactionQuality($id, $data['Quality'], @$data['SupplychainID'], $DateTransaction);
        $quality = array();

        return array( 'id' => $id, 'quality' => $quality);
    }

    private function _updateTransactionNew($data) {
        $id = $data['SupplyTransID'];

        if($data['SupplyType'] == 'Batch'){ 
            $MillID = $data['MillID'];
            $MillOther = $data['MillOther'];
            $DOID = $data['DOID'];
            $DOOther = $data['DOOther'];
            $AgentID = $data['AgentID'];
            $AgentOther = $data['AgentOther'];
            $AgentOtherNik = $data['AgentOtherNik'];
            $AgentOtherSurvey = $data['AgentOtherSurvey'];
        } else {
            $MillID     = NULL;
            $MillOther  = NULL;
            $DOID       = NULL;
            $DOOther    = NULL;
            $AgentID    = NULL;
            $AgentOther = NULL;
            $AgentOtherNik = NULL;
            $AgentOtherSurvey = NULL;
        }

        $update = array(
            'SupplyBatchID' => @$data['SupplyBatchID']=='' || @$data['SupplyBatchID']=='0' ? NULL : $data['SupplyBatchID'],
            'SupplychainID' => $data['SupplychainID'],
            'TransNumber' => $data['TransNumber'],
            'DateTransaction' => $data['DateTransaction'],
            'SupplyType' => $data['SupplyType'],
            'SupplyID' => $data['SupplyID'],
            'MillID' => $MillID,
            'MillOther'=> $MillOther,
            'DOID' => $DOID,
            'DOOther' => $DOOther,
            'AgentID' => $AgentID,
            'AgentOther' => $AgentOther,
            'AgentOtherNik' => $AgentOtherNik,
            'AgentOtherSurvey' => $AgentOtherSurvey,
            'Palmtypeid' => @$data['Palmtypeid']=='' || @$data['Palmtypeid']=='0' ? NULL : $data['Palmtypeid'],
            'FFBGrossWeight' => @$data['FFBGrossWeight']=='' || @$data['FFBGrossWeight']=='0' ? NULL : $data['FFBGrossWeight'],
            'FFBNettWeight' => @$data['FFBNettWeight']=='' || @$data['FFBNettWeight']=='0' ? NULL : $data['FFBNettWeight'],
            'FFBPrice' => @$data['FFBPrice']=='' || @$data['FFBPrice']=='0' ? NULL : $data['FFBPrice'],
            'FFBTotalPayment' => @$data['FFBTotalPayment']=='' || @$data['FFBTotalPayment']=='0' ? NULL : $data['FFBTotalPayment'],
            'BrondolanNetWeight' => @$data['BrondolanNetWeight']=='' || @$data['BrondolanNetWeight']=='0' ? NULL : $data['BrondolanNetWeight'],
            'BrondolanGrossWeight' => @$data['BrondolanGrossWeight']=='' || @$data['BrondolanGrossWeight']=='0' ? NULL : $data['BrondolanGrossWeight'],
            'BrondolanPrice' => @$data['BrondolanPrice']=='' || @$data['BrondolanPrice']=='0' ? NULL : $data['BrondolanPrice'],
            'BrondolanTotalPayment' => @$data['BrondolanTotalPayment']=='' || @$data['BrondolanTotalPayment']=='0' ? NULL : $data['BrondolanTotalPayment'],
            'PlantationNr' => $data['PlantationNr'],
            'CollectpointID' => $data['CollectpointID'],
            'Bunches' => $data['Bunches'],
            'VolumeBruto' => $data['VolumeBruto'],
            'VolumeNetto' => $data['VolumeNetto'],
            'VolumeCutting' => $data['VolumeCutting'],
            'PackageID' => $data['PackageID'],
            'PackageNumber' => $data['PackageNumber'],
            'PackageWeight' => $data['PackageWeight'],
            'TransStatusID' => $data['TransStatusID'],
            'ContractPrice' => $data['ContractPrice'],
            'NetPrice' => $data['NetPrice'],
            'InvoiceNumber' => $data['InvoiceNumber'],
            'DiscountPrice' => $data['DiscountPrice'],
            'TotalPayment' => $data['TotalPayment'],
            'PaymentReduction' => @$data['PaymentReduction']=='' || @$data['PaymentReduction']=='0' ? NULL : $data['PaymentReduction'],
            'PaymentPaid' => @$data['PaymentPaid']=='' || @$data['PaymentPaid']=='0' ? NULL : $data['PaymentPaid'],
            'Longitude' => @$data['Longitude']=='' || @$data['Longitude']=='0' ? NULL : $data['Longitude'],
            'Latitude' => @$data['Latitude']=='' || @$data['Latitude']=='0' ? NULL : $data['Latitude'],
            'Notes' => $data['Notes'],
            'DateCreated' => $data['DateCreated'],
            'CreatedBy' => $data['CreatedBy'],
            'DateUpdated' => $data['DateUpdated'],
            'LastModifiedBy' => $data['LastModifiedBy'],
            'DeductionPercentage' => @$data['DeductionPercentage'],
            'DeductionWeight' => $data['DeductionWeight'],
            'AutoTransNumber' => @$data['AutoTransNumber'],
            'FFBCount' => @$data['FFBCount'],
            'Bunches' => @$data['Bunches'],
            'SupplybaseCategoryID' => $data['SupplybaseCategoryID'],
            'SupplyBatchSourceType' => $data['SupplyBatchSourceType'],
            'SupplyBatchType' => $data['SupplyBatchType'],
            'isTraceable' => $data['isTraceable']
        );

        $this->db->where('SupplyTransID', $id);
        $this->db->update('ktv_tc_supplychain_transaction', $update);
        
        //$quality = $this->_insertTransactionQuality($id, $data['Quality'], @$data['SupplychainID'], $DateTransaction);
        $quality = array();

        return array( 'id' => $id, 'quality' => $quality);
    }

    private function _checkTrans($id, $VolumeNetto, $DestWeight, $AutoBatchNumber) {
        if($VolumeNetto==''){
            $VolumeNetto == $DestWeight;
        }
        $VolumeNetto = round($VolumeNetto, 2);
        /*$this->db->select('SupplyTransID');
        $this->db->from('ktv_tc_supplychain_transaction');
        $this->db->where('SupplyBatchID',$id);
        $Q = $this->db->get();
        return $Q->num_rows();*/
        $sql = "SELECT SUM( VolumeNetto ) VolumeNetto FROM ktv_tc_supplychain_transaction  WHERE SupplyBatchID = ?";
        $TransNetto = $this->db->query($sql, array($id))->row()->VolumeNetto;
        if(floatval($VolumeNetto) > 0 && floatval($VolumeNetto)!=floatval($TransNetto)){
            return true;
        }else{
            return false;
        }
    }

    function _getSupplybaseCategory($data){
        $SupplybaseCategoryID = NULL;
        if($data['SupplyType']=='Batch'){
            $sql = "SELECT *
                    FROM
                        ktv_tc_supplychain_batch sb 
                    WHERE
                        sb.SupplyBatchID = ? AND sb.SupplyBatchID > 0";
            $check = $this->db->query($sql, array($data['SupplyID']));
            if($check->num_rows() > 0 && $data['SupplyID'] > 0 && $data['SupplyBatchType']!='Untraceable'){
                $SupplybaseCategoryID = 3;
            }
        }else if($data['SupplyType']=='Farmer'){
            //SupplybaseType
            $org = $this->db->query("SELECT * FROM ktv_tc_supplychain_org WHERE SupplychainID=?", array($data['SupplychainID']))->result_array();
            if($data['SupplyType']=='Farmer'){
                $supply = $this->db->query("SELECT * FROM ktv_members WHERE MemberID=?", array($data['SupplyID']))->result_array();
                $SupplybaseType = $supply[0]['SupplybaseType'];
            }else{
                $SupplybaseType = '';
            }
            if($org[0]['ObjType']=='mill' && $SupplybaseType=='plasma'){
                $SupplybaseCategoryID = 1; //Farmer Plasma
            }else if($org[0]['ObjType']=='mill' && $SupplybaseType=='direct'){
                $SupplybaseCategoryID = 2; //Direct Smallholder
            }else if($org[0]['ObjType']!='mill'){
                $SupplybaseCategoryID = 3; //Agent / Dealer / Vendor
            }
        }else if($data['SupplyType']=='Nonfarmer'){
            if($org[0]['ObjType']=='mill'){
                $SupplybaseCategoryID = 4; //Owned Estate
            }else{
                $check = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SMETypeID) id FROM ktv_member_sme_type WHERE MemberID=?", array($org[0]['ObjID']))->row()->id;
                if($id=='5'){
                    $SupplybaseCategoryID = 5; //External Estate
                }else{
                    $SupplybaseCategoryID = 3; //Agent / Dealer / Vendor
                }
            }
            
        }
        return $SupplybaseCategoryID;
    }
}

?>
