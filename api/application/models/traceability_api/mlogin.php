<?php

/**
 * Authentication Model for Mobile
 *
 * @author Ardi <ardiantoro@koltiva.com>
 */
class Mlogin extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function doLoginTraceability($username = false, $password = false) {
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $output = array();

        //ambil keterangan user
        $id = $this->_getUserDetail($username,$password,$output);

        if($id){
            
            //ambil keterangan org
            $org = $this->_getOrgDetail($id,$output);
            $PartnerID = $this->db->query("SELECT PartnerID FROM ktv_supplychain_org_partner WHERE SupplychainID=?", array($org))->row()->PartnerID;
            //ambil area
            //$this->_getOrgArea($org,$output);
            //$this->_getSubDistrict($org,$output);
            //$this->_getVillage($org,$output);

            //ambil tujuan pengiriman
            //$this->_getOrgDestination($org,$output);
            $this->_getOrgRelation($org,$output);

            //ambil package
            $package = $this->_getPackage($org,$output);
            //$paket = $this->_getPaket($org,$output); // tidak dipakai lagi, sama degan package

            //ambil quality items
            $quality = $this->_getQuality($org,$output);

            $quality_status = $this->_getQualityStatus($org,$output);

            $transport = $this->_getTransport($org,$PartnerID,$output);

            $officer = $this->_getOfficerType($org,$output);

            $slot = $this->_getSlotReference($org,$output);

            $handphone_type = $this->_getHandPhoneType($org,$output);

            $first_buyer = $this->_getFirstBuyer($PartnerID,$output);

            $this->_getUnit($org,$output);

            $this->_getCurrency($org,$output);

            //ambil token
            $token = $this->_getToken($id,$output);

            //ambil nomor terakhir
            $number = $this->_getLastBatchNumber($org,$output);
            $number = $this->_getLastTransNumber($org,$output);

            $faktur = $this->_getLastFakturNumber($org,$output);

            $region = $this->_getRegion($org,$output);
            $region = $this->_getFarmerGroupNew($org, $PartnerID, $output);
            
            

            //ambil trans null
            //$transnull = $this->_getNullTransaction($org,$output);

            //ambil batch open
            //$batch = $this->_getOpenBatch($org,$output);

            $update = array(
                'UserMobileToken' => $token
            );

            $this->db->where('userid',$id);
            $this->db->update('sys_user',$update);

            if(!$this->db->_error_number()){
                return $output;
            }
        }

        return false;
    }

    public function doLoginTraceability_v2($username, $aws_token) {
        $output = array();
        $id = $this->_getUserDetail_v2($username);
        if($id){   
            $org = $this->_getOrgDetail($id,$output);
            $PartnerID = $this->db->query("SELECT PartnerID FROM ktv_supplychain_org_partner WHERE SupplychainID=?", array($org))->row()->PartnerID;
            
            $this->_getOrgRelation($org,$output,$PartnerID);
            
            $package = $this->_getPackage($org,$output);
            $quality = $this->_getQuality($org,$output);

            $quality_status = $this->_getQualityStatus($org,$output);

            $transport = $this->_getTransport($org,$PartnerID,$output);

            $officer = $this->_getOfficerType($org,$output);

            $slot = $this->_getSlotReference($org,$output);

            $handphone_type = $this->_getHandPhoneType($org,$output);

            $first_buyer = $this->_getFirstBuyer($PartnerID,$output);

            $this->_getUnit($org,$output);

            $this->_getCurrency($org,$output);

            $token = $aws_token;

            //ambil nomor terakhir
            $number = $this->_getLastBatchNumber($org,$output);
            $number = $this->_getLastTransNumber($org,$output);

            $faktur = $this->_getLastFakturNumber($org,$output);

            $region = $this->_getRegion($org,$output);
            $region = $this->_getFarmerGroupNew($org, $PartnerID, $output);

            $update = array(
                'UserMobileToken' => $token
            );

            $this->db->where('userid',$id);
            $this->db->update('sys_user',$update);

            if(!$this->db->_error_number()){
                $this->db->query("UPDATE sys_log_access_mobile SET StatusCode='inactive' WHERE StatusCode='active' AND UserID=?", array($id));
                $this->db->query("DELETE FROM sys_user_login_mobile WHERE UserID=?", array($id));
                $insert = array(
                    'UserName' => $username,
                    'UserID' => @$id,
                    'SupplychainID' => @$org,
                    'SessionIP' => $this->input->ip_address(),
                    'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
                    'AttempProcess' => 'Traceability - Success',
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'Token' => $aws_token
                );
                $this->db->insert('sys_log_access_mobile', $insert);
                $this->db->query("INSERT INTO sys_user_login_mobile(UserID, SupplychainID) VALUES ($id, $org)");
                return $output;
            }else{
                $insert = array(
                    'UserName' => $username,
                    'UserID' => @$id,
                    'SupplychainID' => @$org,
                    'SessionIP' => $this->input->ip_address(),
                    'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
                    'AttempProcess' => 'Traceability - Failed',
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'Token' => $aws_token
                );
                $this->db->insert('sys_log_access_mobile', $insert);
            }
        }else{
            $insert = array(
                'UserName' => $username,
                'UserID' => @$id,
                'SupplychainID' => @$org,
                'SessionIP' => $this->input->ip_address(),
                'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
                'AttempProcess' => 'Traceability - Not found',
                'DateCreated' => date('Y-m-d H:i:s'),
                'Token' => $aws_token
            );
            $this->db->insert('sys_log_access_mobile', $insert);
        }

        return false;
    }

    private function _getUserDetail($username,$password,&$output) {

        $this->db->select('UserId,UserName,(UserRealName) UserRealName');
        $this->db->from('sys_user');
        $this->db->where('UserName',$username);
        $this->db->where('UserPassword',do_hash($password, 'hash_vera'));
        $this->db->where('UserActive','Yes');
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            $output['UserID'] = $row->UserId;
            $output['UserRealName'] = $row->UserRealName;
            $return['success'] = true;
            $return['id'] = $row->UserId;
        }else{
            $return['success'] = false;
            $return['id'] = '';
            $return['pass'] = do_hash($password, 'hash_vera');
        }

        return $return;
    }

    private function _getUserDetail_v2($username) {
        $sql = "SELECT
                    u.UserId,
                    u.UserName,
                     ( u.UserRealName ) UserRealName,
                    vss.SupplychainID
                FROM
                    sys_user u
                    LEFT JOIN view_supplychain_staff vss ON vss.UserID=u.UserId
                WHERE
                    u.UserName = ? AND vss.SupplychainID IS NOT NULL AND u.UserActive='Yes'";
        $Q = $this->db->query($sql, array($username));
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            $output['UserID'] = $row->UserId;
            $output['UserRealName'] = $row->UserRealName;
            return $row->UserId;
        }

        return false;
    }

    private function _getOrgDetail($userid, &$output) {

        //ambil orgtype
        /*$this->db->select('org.SupplychainID,org.OrgType,st.OrgID,st.PartnerID, (vso.Name) Name, org.KodeArea, tr.NPWP');
        $this->db->from('view_supplychain_staff st');
        $this->db->join('ktv_supplychain_org org','org.SupplychainID = st.SupplychainID','LEFT');
        $this->db->join('view_supplychain_org vso','vso.SupplychainID = org.SupplychainID','LEFT');
        $this->db->join('ktv_traders tr','tr.TraderID = org.OrgID','LEFT');
        $this->db->join('ktv_supplychain_org_partner pr','pr.SupplychainID = org.SupplychainID','LEFT');
        $this->db->where('st.UserID',$userid);*/
        $sql = "SELECT
                    org.SupplychainID,
                    st.UserID,
                    su.UserRealName,
                    org.OrgType,
                    st.OrgID,
                    st.PartnerID,
                     ( vso.NAME ) Name,
                    org.KodeArea,
                    tr.NPWP,
                    IF(tr.NPWP IS NOT NULL && tr.NPWP!='', 0.25, 0.5) WHTRate,
                    org.UsePartnerID,
                    org.UseFermentation,
                    IFNULL(l.id,1) DefaultLanguage,
                    org.AllowNewFarmer,
                    org.AllowMixGrade,
                    org.IsCheckSampleDate,
                    org.AllowNonCertTransaction,
                    IFNULL(tr.Handphone, '') AS PhoneNumber,
                    org.UseKoltipay,
                    org.EnablePayment
                FROM
                    view_supplychain_staff st
                    LEFT JOIN ktv_supplychain_org org ON org.SupplychainID = st.SupplychainID
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID = org.SupplychainID
                    LEFT JOIN ktv_traders tr ON tr.TraderID = org.OrgID
                    LEFT JOIN ktv_supplychain_org_partner pr ON pr.SupplychainID = org.SupplychainID 
                    LEFT JOIN sys_user su ON su.UserId=st.UserID
					LEFT JOIN sys_language l ON l.`code`=LOWER(su.UserLanguage)
                WHERE
                    st.UserID = ?";
        $Q = $this->db->query($sql, array($userid)); //print_r($this->db->last_query());die;
        if($Q->num_rows() > 0){
            $row = $Q->row();
            $output['SupplychainID'] = $row->SupplychainID;
            $output['UserID'] = $row->UserID;
            $output['UserRealName'] = $row->UserRealName;
            $checkPartnerID = $row->PartnerID;
            if($checkPartnerID=='22'){
                $checkPartnerID = 8;
            }
            $output['PartnerID'] = $checkPartnerID;
            $output['OrgID'] = $row->OrgID;
            $output['OrgType'] = $row->OrgType;
            $output['OrgName'] = $row->Name;
            $output['KodeArea'] = $row->KodeArea;
            if($row->PartnerID=='9'){
                $output['WHTRate'] = $row->WHTRate;
            }else{
                $output['WHTRate'] = 0;
            }
            $output['NPWP'] = $row->NPWP;
            $output['UsePartnerID'] = $row->UsePartnerID;
            $output['UseFermentation'] = $row->UseFermentation;
            
            $output['BeanType'] = $this->_getBeanType($row->SupplychainID, $row->PartnerID);
            $output['DeliveryBeanType'] = $this->_getDeliveryBeanType($row->SupplychainID, $row->PartnerID);
            $output['TransType'] = $this->_getTransType($row->SupplychainID, $row->PartnerID);
            $output['PaymentMethod'] = $this->_getPaymentMethod($row->SupplychainID, $row->PartnerID);
            $output['PaymentGroup'] = $this->_getPaymentGroup($row->SupplychainID, $row->PartnerID);
            $output['PaymentStatus'] = $this->_getPaymentStatus($row->SupplychainID, $row->PartnerID);
            $output['TransLabel'] = $this->_getTransLabel($row->SupplychainID, $row->PartnerID);
            $output['IMS'] = $this->_getIMSDetail($row->SupplychainID);
            $output['AllowNewFarmer'] = $row->AllowNewFarmer;
            $output['AllowNonCertTransaction'] = $row->AllowNonCertTransaction;
            $output['AllowMixGrade'] = $row->AllowMixGrade;
            $output['IsCheckSampleDate'] = $row->IsCheckSampleDate;
            $output['SecurityCode'] = "Koltiva2017!";
            $output['DefaultLanguage'] = $row->DefaultLanguage;
            $output['UseKoltipay'] = $row->UseKoltipay;
            $output['EnablePayment'] = $row->EnablePayment;
            return $row->SupplychainID;
        }

        return false;
    }

    public function _getBeanType($sid, $PartnerID) {
        
        $output = array();
        $sql = "SELECT b.BeanTypeID, b.BeanTypeName, a.IsPriceEditable FROM ktv_supplychain_bean_type a LEFT JOIN ref_bean_type b ON a.BeanTypeID=b.BeanTypeID WHERE a.SupplychainID=?";
        $Q = $this->db->query($sql,array($sid));
        if($Q->num_rows()){
            $output = $Q->result_array();
        } else {
            if($PartnerID=='9'){
                $output = array(
                    array(
                        'BeanTypeID' => '2',
                        'BeanTypeName' => 'Wet Bean',
                        'IsPriceEditable' => '0'
                    )
                );
            }else if($PartnerID=='8'){
                $output = array(
                    array(
                        'BeanTypeID' => '1',
                        'BeanTypeName' => 'Dry Bean',
                        'IsPriceEditable' => '1'
                    )
                );
            }else{
                $output = array(
                    array(
                        'BeanTypeID' => '2',
                        'BeanTypeName' => 'Wet Bean',
                        'IsPriceEditable' => '1'
                    )
                );
            }
        }

        return $output;
    }

    public function _getDeliveryBeanType($sid, $PartnerID) {
        
        $output = array();
        $sql = "SELECT b.BeanTypeID DeliveryBeanTypeID, b.BeanTypeName DeliveryBeanTypeName FROM ktv_supplychain_delivery_bean_type a LEFT JOIN ref_bean_type b ON a.BeanTypeID=b.BeanTypeID WHERE a.SupplychainID=?";
        $Q = $this->db->query($sql,array($sid));
        if($Q->num_rows()){
            $output = $Q->result_array();
        } else {
            if($PartnerID=='9'){
                $output = array(
                    array(
                        'DeliveryBeanTypeID' => '2',
                        'DeliveryBeanTypeName' => 'Wet Bean'
                    )
                );
            }else if($PartnerID=='8'){
                $output = array(
                    array(
                        'DeliveryBeanTypeID' => '1',
                        'DeliveryBeanTypeName' => 'Dry Bean'
                    )
                );
            }else{
                $output = array(
                    array(
                        'DeliveryBeanTypeID' => '1',
                        'DeliveryBeanTypeName' => 'Dry Bean'
                    )
                );
            }
        }

        return $output;
    }

    function _getTransType($sid, $PartnerID) {
        
        $output = array();
        $sql = "SELECT b.TransTypeID, b.TransTypeName, b.SupplyType, b.IsCheckQuota, b.IsDeductQuota, b.AllowOverQuota FROM ktv_supplychain_trans_type a LEFT JOIN ref_trans_type b ON a.TransTypeID=b.TransTypeID WHERE a.SupplychainID=? AND b.SupplyType!='Batch'";
        $Q = $this->db->query($sql,array($sid));
        if($Q->num_rows()){
            $output = $Q->result_array();
        } else {
            if($PartnerID=='9'){
                $output = array(
                    array(
                        'TransTypeID' => '1',
                        'TransTypeName' => 'RA',
                        'SupplyType' => 'Farmer',
                        'IsCheckQuota' => '0',
                        'IsDeductQuota' => '1',
                        'AllowOverQuota' => '1'
                    ),
                    array(
                        'TransTypeID' => '2',
                        'TransTypeName' => 'NonCert',
                        'SupplyType' => 'FarmerNonCert',
                        'IsCheckQuota' => '0',
                        'IsDeductQuota' => '1',
                        'AllowOverQuota' => '1'
                    )
                );
            }else if($PartnerID=='8'){
                $output = array(
                    array(
                        'TransTypeID' => '3',
                        'TransTypeName' => 'UTZ',
                        'SupplyType' => 'Farmer',
                        'IsCheckQuota' => '1',
                        'IsDeductQuota' => '1',
                        'AllowOverQuota' => '0'
                    )
                );
            }
            
        }

        return $output;
    }

    private function _getOrgArea($orgid, &$output) {

        $this->db->select('ktv_supplychain_area.DistrictID, (District) District');
        $this->db->from('ktv_supplychain_area');
        $this->db->join('ktv_district','ktv_district.DistrictID = ktv_supplychain_area.DistrictID','LEFT');
        $this->db->where('SupplychainID',$orgid);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['District'] = $result;

            return $result;
        }else{
            $output['District'] = array();
        }

        return array();
    }

    private function _getSubDistrict($orgid, &$output) {
        $sql = "SELECT c.SubDistrictID, (c.SubDistrict) SubDistrict
                FROM
                    ktv_supplychain_area a
                    LEFT JOIN ktv_district b ON b.DistrictID=a.DistrictID
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID
                WHERE
                    a.SupplychainID = ? ORDER BY District, c.SubDistrictID";
        $Q = $this->db->query($sql, array($orgid));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['SubDistrict'] = $result;

            return $result;
        }else{
            $output['SubDistrict'] = array();
        }

        return array();
    }

    private function _getVillage($orgid, &$output) {
        $sql = "SELECT d.VillageID, (d.Village) Village 
                FROM
                    `ktv_supplychain_area` a 
                    LEFT JOIN ktv_district b ON b.DistrictID=a.DistrictID
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID
                    LEFT JOIN ktv_village d ON d.SubDistrictID=c.SubDistrictID
                WHERE
                    a.SupplychainID = ? ORDER BY District, c.SubDistrictID";
        $Q = $this->db->query($sql, array($orgid));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['Village'] = $result;

            return $result;
        }else{
            $output['Village'] = array();
        }

        return array();
    }

    private function _getOrgRelation($orgid, &$output, $PartnerID='') {
        $result = array();
        $sql = "SELECT b.SupplychainID, a.BeanTypeID, b.OrgType, b.OrgID, b.`Name`, b.Address, b.VillageID, b.Latitude, b.Longitude  FROM ktv_supplychain_org_rel a LEFT JOIN view_supplychain_org b ON b.SupplychainID=a.ParentOrgId 
                                WHERE ChildOrgId=? AND NOW() BETWEEN StartDate AND EndDate";
        $Q = $this->db->query($sql, array($orgid));
        //echo $this->db->last_query();die;
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $i = $Q->num_rows();
            $j = $i + 1;
            if($PartnerID=='8'){
                $result[$i]['SupplychainID'] = 0;
                $result[$i]['BeanTypeID'] = 1;
                $result[$i]['OrgType'] = 'Other';
                $result[$i]['OrgID'] = 0;
                $result[$i]['Name'] = 'Other';
                $result[$i]['Address'] = '';
                $result[$i]['VillageID'] = '';
                $result[$i]['Latitude'] = '';
                $result[$i]['Longitude'] = '';
            }
        }
        $output['Relation'] = $result;
        return $result;
    }

    public function _getQuality($orgid, &$output = array()) {
        $sql = "SELECT
                    b.DetailID QualityID,
                    b.`Name` QualityName,
                    b.`Order` QualityOrder,
                    IFNULL(b.MinValue, '') QualityMin,
                    IFNULL(b.`MaxValue`,'') QualityMax,
                    b.FAQValue QualityStd,
                    b.FAQFormula QualityFormula,
                    IFNULL(b.IsFormVisible, 0) Formvisible,
                    IFNULL(b.IsPrintVisible, 0) Printvisible,
                    b.BeanTypeID,
                    'decimal' QualityType,
                    IF(b.FAQStatus!='1', 0, 1) IsStatus,
                    IFNULL(b.IsSample, 0) IsSample,
                    IFNULL(b.IsMandatory, 0) IsMandatory
                FROM
                    ktv_supplychain_quality a
                    LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.StandardID=b.StandardID
                    LEFT JOIN ktv_supplychain_quality_standard c ON c.StandardID=b.StandardID
                WHERE
                    a.QualitySupplychainID = ? AND NOW() BETWEEN a.QualityDateStart AND a.QualityDateEnd
                    AND c.QualityType='general' AND (c.MobileQualityID IS NULL OR c.MobileQualityID IN (1,2))";
        $Q = $this->db->query($sql, array($orgid));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['Quality'] = $result;
            return $result;
        }else{
            $output['Quality'] = array();
        }

        return array();
    }

    private function _getQualityStatus($orgid, &$output = array()) {
        $result = array("OK", 'Reject', 'RC-Y');
        $output['QualityStatus'] = $result;
        return $result;
    }

    private function _getTransport($orgid, $PartnerID, &$output = array()) {
        if($PartnerID=='9'){
            $sql = "SELECT TransportTypeID, TransportTypeName, TransportPrice FROM ref_transport_type WHERE StatusCode='active'";
            $Q = $this->db->query($sql);
            if($Q->num_rows() > 0) {
                $result = $Q->result_array();
                $output['Transport'] = $result;
            }else{
                $result[0]['TransportTypeID'] = 3;
                $result[0]['TransportTypeName'] = 'Tidak Ada';
                $result[0]['TransportPrice'] = 0;
                $output['Transport'] = $result;
            }
        }else{
            $result[0]['TransportTypeID'] = 3;
            $result[0]['TransportTypeName'] = 'Tidak Ada';
            $result[0]['TransportPrice'] = 0;
            $output['Transport'] = $result;
        }
        return $result;
    }

    private function _getOfficerType($orgid, &$output = array()) {
        $sql = "SELECT OfficerTypeID, OfficerTypeName FROM ref_officer_type WHERE StatusCode='active'";
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['OfficerType'] = $result;
            return $result;
        }else{
            $output['OfficerType'] = array();
        }
        return array();
    }
    
    private function _getSlotReference($orgid, &$output = array()) {
        $sql = "SELECT SlotID, SlotNr, TimeStart, TimeEnd, StatusCode FROM ktv_ref_supplychain_slot_mars WHERE StatusCode!='nullified'";
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['SlotTime'] = $result;
            return $result;
        }else{
            $output['SlotTime'] = array();
        }
        return array();
    }


    //////////////////////////////////////////////////////////////////////////////////////////////

    public function doLogoutTraceability($get) {
        $this->db->query("UPDATE sys_log_access_mobile SET StatusCode='inactive' WHERE StatusCode='active' AND UserID=? AND SupplychainID=?", array($get['userid'], $get['sid']));
        $this->db->query("DELETE FROM sys_user_login_mobile WHERE UserID=? AND SupplychainID=?", array($get['userid'], $get['sid']));
        return true;
    }

    public function _getToken($data,&$output) {

        $salt = 'bismillah';
        //$this->load->library('encrypt');
        //$token = $this->encrypt->encode($data,$salt);
        $token = hash_pbkdf2("sha512",$salt.$data.date('Y-m-d H:i:s'),'NIKOSB_VC',10,32);
        $output['token'] = $token;
        return $token;
    }

    public function checkToken($token) {

        $salt = 'bismillah';

        //$this->load->library('encrypt');
        //$user = $this->encrypt->decode($token,$salt);

        $this->db->select('StatusCode');
        //$this->db->where('UserId',$user);
        $this->db->where('UserMobileToken',$token);
        $Q = $this->db->get('sys_user');
        if($Q->num_rows() > 0){
            $row = $Q->row();
            if($row->StatusCode === 'active'){
                return true;
            }
        }

        return false;
    }

    function _getPackage($orgid, &$output) {

        $Q = $this->db->query("SELECT PackageID, PackageType, PackageWeight, IsTransaction, IsBatch, DefaultPackage AS IsDefault FROM ktv_supplychain_package WHERE StatusCode='active' AND PackageSupplychainID = '" . $orgid . "'");
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['Package'] = $result;
            //$output['paket'] = $result;

            return $result;
        }

        return array();
    }

    private function _getLastBatchNumber($supplychainid,&$output) {

        $sql = "SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(sb.SupplyBatchNumber, REPLACE(SUBSTR(sb.SupplyBatchNumber, 1, 12), '-', ''), -1) AS UNSIGNED)), 0) number
                FROM ktv_supplychain_batch sb
                WHERE
                    sb.SupplyOrgID = ? 
                    AND (SupplyBatchDate = SUBSTR(NOW(), 1, 10) OR SupplyBatchNumber LIKE CONCAT( '%', REPLACE(SUBSTR(NOW(), 1, 10), '-', ''), '%') )";
        $Q2 = $this->db->query($sql, array($supplychainid)); //echo $this->db->last_query();die;
        $number = $Q2->row()->number;
        $output['lastbatch'] = $number;
    }

    private function _getLastTransNumber($supplychainid,&$output) {

        $number = 0;

        $sid = sprintf("%04d", $supplychainid);
        $rule = $sid . date('Ymd') . "[0-9]{3}";
        //get last number
        $this->db->select('(RIGHT(MAX(InvoiceNumber), 3)) AS LAST', FALSE);
        $this->db->from('ktv_supplychain_transaction');
        $this->db->where('InvoiceNumber REGEXP "' . $rule . '"', NULL, FALSE);
        $Q2 = $this->db->get(); //echo $this->db->last_query();die;
        if ($Q2->num_rows()) {
            $last = $Q2->row();
            if ((int) $last->LAST > 0) {
                $number = ((int) $last->LAST);
            }
        } 

        $output['lasttrans'] = $number;
    }

    private function _getLastFakturNumber($supplychainid,&$output) {

        $sql = "SELECT 
                    (IFNULL(MAX(dt.number), 0)) number
                FROM
                    (
                        SELECT
                            FakturNumber, 
                            ROUND (   
                                        (
                                                LENGTH(FakturNumber)
                                                - LENGTH( REPLACE ( FakturNumber, '-', '') ) 
                                        ) / LENGTH('-')        
                                ) AS total,
                            MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(FakturNumber,'-',-3), '-', '1') AS UNSIGNED)) number
                        FROM
                            ktv_supplychain_transaction st
                            LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        WHERE
                            YEAR(st.DateTransaction)=YEAR(NOW())
                            AND (sb.SupplyOrgID=? OR st.SupplychainID=?)
                        GROUP BY st.SupplyTransID
                        HAVING total = 3
                    ) dt";
        $Q2 = $this->db->query($sql, array($supplychainid, $supplychainid)); //echo $this->db->last_query();die;
        $number = $Q2->row()->number;

        if(intval($number)==0){
            $sql = "SELECT COUNT(*) number
                    FROM (
                        SELECT
                            FakturNumber, 
                            ROUND (   
                                        (
                                                LENGTH(FakturNumber)
                                                - LENGTH( REPLACE ( FakturNumber, '-', '') ) 
                                        ) / LENGTH('-')        
                                ) AS total,
                            MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(FakturNumber,'-',-3), '-', '1') AS UNSIGNED)) number
                        FROM
                            ktv_supplychain_transaction st
                            LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        WHERE
                            YEAR(st.DateTransaction)=YEAR(NOW())
                            AND (sb.SupplyOrgID=290 OR st.SupplychainID=290)
                        GROUP BY st.SupplyTransID
                    ) dt";
            $Q2 = $this->db->query($sql, array($supplychainid, $supplychainid)); //echo $this->db->last_query();die;
            $number = $Q2->row()->number;
        }
        $output['lastfakturnumber'] = $number;
    }

    public function _getNullTransaction($supplychainid,&$output, $b = false) {

        $out = [];

        if($b) { $batch = " = " . $b; } else { $batch = " IS NULL  AND SUBSTR(InvoiceNumber,1,4) = ? HAVING supplyid IS NOT NULL AND certificationlabel IN('UTZ','CocoaLife','batch') AND `type` IN('UTZ','CocoaLife','batch')"; }

        $sql = "SELECT
            a.SupplyTransID 'transid',
            SupplyBatchID 'batchid',
            DATE_FORMAT(DateTransaction,'%Y-%m-%d') 'date',
            IF(SupplyType = 'Farmer', IF(TransCertification = 'utz','UTZ',IF(TransCertification = 'Cocoalife', 'CocoaLife', '')),IF(SupplyType = 'batch','batch','')) 'type',
            IF(TransCertification = 'utz','UTZ',IF(TransCertification = 'Cocoalife', 'CocoaLife', TransCertification)) certificationlabel,
            SupplyID 'supplyid',
            FAQVolumeBruto 'bruto',
            FAQVolumeNetto 'netto',
            FAQNumberPackage 'sacks',
            NULL 'registrationtime',
            NULL 'qualitytime',
            NULL 'paymenttime',
            NULL 'registrationby',
            NULL 'qualityby',
            NULL 'paymentby',
            FAQQualityKA 'qualityka',
            FAQQualityBC 'qualitybc',
            FAQQualityWaste 'qualitywaste',
            FAQQualityMouldy 'qualitymouldy',
            FAQQualityInsect 'qualityinsect',
            FAQQualitySlaty 'qualityslaty',
            FAQContractPrice 'contractprice',
            FAQNetPrice 'netprice',
            FAQTransportFee 'transport',
            FAQTotalPayment 'totalpayment',
            FakturNumber 'fakturnumber',
            InvoiceNumber 'invoicenumber',
            a.DateCreated 'createddate',
            a.CreatedBy 'createdby',
            a.DateUpdated 'modifieddate',
            a.LastModifiedBy 'modifiedby',
            (SELECT DISTINCT PackageID FROM ktv_supplychain_transaction_dtl WHERE SupplyTransID = a.SupplyTransID LIMIT 1) 'packageid',
            FAQVolumePackage 'packagesize'
        FROM
            ktv_supplychain_transaction a
        LEFT JOIN ktv_supplychain_transaction_dtl dtl ON dtl.SupplyTransID = a.SupplyTransID
        WHERE (a.StatusCode NOT IN('cancel','nullified') OR a.StatusCode IS NULL) AND SupplyBatchID " . $batch;

        $Q = $this->db->query($sql,array(sprintf("%04d", $supplychainid)));

        if($Q->num_rows() > 0) {
            $out = $Q->result_array();
            foreach($out as $keys => $trans) {
                $out[$keys]['quality'] = $this->_getTransQuality($trans['transid'],$supplychainid);
            }
        }

        if(!$b) {
            $output['trans'] = $out;
        } else {
            return $out;
        }

    }

    private function _getTransQuality($transid,$org) {

        $output = [];

        //get std quality
        $quality = $this->_getQuality($org);
        foreach($quality as $key => $qval) {

            $item = array(
                'id'     => $qval['QualityID'],
                'result' => 0,
                'reward' => 0
            );

            //get value
            $sql = "SELECT DetailID 'id', FAQResult 'result', FAQReward 'reward' FROM ktv_supplychain_transaction_quality WHERE SupplyTransID = ? AND DetailID = ?";
            $Q = $this->db->query($sql,array($transid,$qval['QualityID']));

            if($Q->num_rows() > 0) {
                $Qv = $Q->row();
                $item['result'] = $Qv->result;
                $item['reward'] = $Qv->reward;
            }

            array_push($output,$item);
        }

        return $output;
    }

    public function _getHandPhoneType($supplychainid,&$output) {
        $return = array();
        $sql = "SELECT * FROM ref_handphone_type";
        $query = $this->db->query($sql);
        if($query->num_rows() > 0){
            $return = $query->result_array();
        }else{
            $return[0]['HandPhoneType'] = "1";
            $return[0]['HandPhoneTypeName'] = "Smartphone (Android/iPhone)";

            $return[1]['HandPhoneType'] = "2";
            $return[1]['HandPhoneTypeName'] = "Feature Phone (Basic Mobile Phone)";

            $return[2]['HandPhoneType'] = "3";
            $return[2]['HandPhoneTypeName'] = "No Handphone";    
        }
        $output['HandPhoneType'] = $return;
        return $return;
    }

    private function _getRegion($SupplychainID, &$output = array()) {
        $sql = "SELECT
                    d.DistrictID, d.District
                FROM
                    ktv_supplychain_area sa
                    LEFT JOIN ktv_district d ON d.DistrictID=sa.DistrictID
                WHERE
                    sa.SupplychainID = ?
                    AND d.StatusCode='active'";
        $Q = $this->db->query($sql, array($SupplychainID));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['District'] = $result;
            //return $result;
        }else{
            $output['District'] = array();
        }

        $sql = "SELECT
                    sd.SubDistrictID, sd.SubDistrict, sd.DistrictID
                FROM
                    ktv_supplychain_area sa
                    LEFT JOIN ktv_district d ON d.DistrictID=sa.DistrictID
                    LEFT JOIN ktv_subdistrict sd ON sd.DistrictID=d.DistrictID
                WHERE
                    sa.SupplychainID = ?
                    AND d.StatusCode='active' AND sd.StatusCode='active'";
        $Q = $this->db->query($sql, array($SupplychainID));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['SubDistrict'] = $result;
            //return $result;
        }else{
            $output['SubDistrict'] = array();
        }

        $sql = "SELECT
                    v.VillageID, v.Village, v.SubDistrictID
                FROM
                    ktv_supplychain_area sa
                    LEFT JOIN ktv_district d ON d.DistrictID=sa.DistrictID
                    LEFT JOIN ktv_subdistrict sd ON sd.DistrictID=d.DistrictID
                    LEFT JOIN ktv_village v ON v.SubDistrictID=sd.SubDistrictID
                WHERE
                    sa.SupplychainID = ?
                    AND d.StatusCode='active' AND sd.StatusCode='active' AND v.StatusCode='active'
                    ";
        $Q = $this->db->query($sql, array($SupplychainID));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['Village'] = $result;
            //return $result;
        }else{
            $output['Village'] = array();
        }
        return array();
    }

    public function _getFarmerGroup($SupplychainID, $PartnerID, &$output = array()){
        $this->load->model('traceability/api/mfarmer');
        $sql = "SELECT * FROM ktv_supplychain_org WHERE SupplychainID=?";
        $query = $this->db->query($sql, array($SupplychainID))->result_array();
        $org = $query[0];
        if($PartnerID=='9' && @$org['DownloadFarmer']!='district'){
            $sql = "SELECT
                        c.CPGid, c.GroupName, c.VillageID
                    FROM
                        ktv_supplychain_farmer sf
                        LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=sf.FarmerID
                        LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    WHERE
                        sf.StatusCode='active' AND sf.SupplychainID=? AND SUBSTR(NOW(),1,10) BETWEEN sf.DateStart AND sf.DateEnd
                        AND c.CPGid IS NOT NULL
                    GROUP BY c.CPGid";
            $Q = $this->db->query($sql, array($SupplychainID));
            //echo "<pre>".$this->db->last_query();
            if($Q->num_rows() > 0){
                $result = $Q->result_array();
                $output['FarmerGroup'] = $result;
            }else{
                $output['FarmerGroup'] = array();
            }
        }else if($PartnerID=='8' && @$org['DownloadFarmer']!='district'){
            $ims = array();

            //check ims id nya
            $bu = $this->mfarmer->_getIMSbySupplychainID($SupplychainID);
            $ch = $this->mfarmer->_getIMSbyCHSupplychainID($SupplychainID);
            
            if(count($bu) > 0) {
                foreach($bu as $key => $b) {
                    array_push($ims,$b['IMSID']);
                }
            }

            if(count($ch) > 0) {
                foreach($ch as $ckey => $c){
                    array_push($ims,$c['IMSID']);
                } 
            }
            
            $area = $this->mfarmer->_getSupplychainArea($SupplychainID);
            $ims = array_unique($ims);
            
            if(count($ims) > 0) {
                //$Q = $this->db->get();
                $IMSID = '';
                for($i=0; $i<count($ims); $i++){
                    if($i==0){
                        $koma = '';
                    }else{
                        $koma = ',';
                    }
                    $IMSID .= $koma.$ims[$i];
                }
                $DistrictID = '';
                for($i=0; $i<count($area); $i++){
                    if($i==0){
                        $koma = '';
                    }else{
                        $koma = ',';
                    }
                    $DistrictID .= $koma.$area[$i];
                }
                    
                $sql = "SELECT
                            c.CPGid, c.GroupName, c.VillageID
                        FROM
                            ktv_ims i
                            LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.IMSID=i.IMSID AND i.CertEventStatus='2'
                            LEFT JOIN ktv_cocoa_certification_afl_farmer afl ON afl.IMSID=i.IMSID  AND i.CertEventStatus='1' /*AND (i.CertEventStatus!='2' OR i.CertEventStatus IS NULL) AND afl.CertStatusAudit='Comply'*/
                            LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=IF(i.CertEventStatus='2', ccf.FarmerID, afl.FarmerID)
                            LEFT JOIN ktv_village v ON f.VillageID=v.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                            LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                        WHERE
                            i.StatusCode='active' AND i.ICSStatus='1'
                            AND i.IMSID IN ($IMSID)
                            AND sd.DistrictID IN ($DistrictID)
                            AND c.CPGid IS NOT NULL
                        GROUP BY f.CPGid";
                $Q = $this->db->query($sql);
                
                if($Q->num_rows() > 0){
                    $result = $Q->result_array();
                    $output['FarmerGroup'] = $result;
                }else{
                    $output['FarmerGroup'] = array();
                }
            }
        }else{
            if(@$org['DownloadFarmer']=='district'){
                $df1 = '/*'; $df2 = '*/';
            }else{
                $df1 = ''; $df2 = '';
            }

            $area = $this->mfarmer->_getSupplychainArea($SupplychainID);
            $DistrictID = '';
                for($i=0; $i<count($area); $i++){
                    if($i==0){
                        $koma = '';
                    }else{
                        $koma = ',';
                    }
                    $DistrictID .= $koma.$area[$i];
                }
            $sql = "SELECT DISTINCT
                        c.CPGid, c.GroupName, c.VillageID
                    FROM
                        ktv_cocoa_farmer f
                        LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
                        LEFT JOIN ktv_ref_farmer_type rft ON rft.FarmertypeID=ft.FarmertypeID
                        LEFT JOIN ktv_village v ON f.VillageID=v.VillageID
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    WHERE
                        f.StatusCode='active' $df1 AND rft.PartnerID=? $df2
                        AND sd.DistrictID IN ($DistrictID)";
            $Q = $this->db->query($sql, array($PartnerID));
            if($Q->num_rows() > 0){
                $result = $Q->result_array();
                $output['FarmerGroup'] = $result;
            }else{
                $output['FarmerGroup'] = array();
            }
        }
        
        return array();
        
    }

    public function _getFarmerGroupNew($SupplychainID, $PartnerID, &$output = array()){
        $this->load->model('traceability/api/mfarmer');
        $get['sid'] = $SupplychainID;
        $PartnerID = $this->db->query("SELECT PartnerID FROM ktv_supplychain_org_partner WHERE SupplychainID=?", array($get['sid']))->row()->PartnerID;
        $sql = "SELECT * FROM ktv_supplychain_org WHERE SupplychainID=?";
        $query = $this->db->query($sql, array($get['sid']))->result_array();
        $org = $query[0];
        $link = $this->config->item('CTCDN').'/';

        if(@$org['DownloadFarmerTypeID']=='' && $PartnerID=='9'){
            $DownloadFarmerTypeID = '1';
        }else if(@$org['DownloadFarmerTypeID']=='' && $PartnerID=='50'){
            $DownloadFarmerTypeID = '2';
        }else if(@$org['DownloadFarmerTypeID']=='' && $PartnerID=='49'){
            $DownloadFarmerTypeID = '3';
        }else if(@$org['DownloadFarmerTypeID']=='' && ($PartnerID=='8' || $PartnerID=='22') ){
            $DownloadFarmerTypeID = '4';
        }else{
            $DownloadFarmerTypeID = $org['DownloadFarmerTypeID'];
        }
        if($DownloadFarmerTypeID=='1'){
            $data = array();
            $sql = "SELECT
                        c.CPGid, 
                        (c.GroupName) GroupName,
                        c.VillageID
                    FROM
                        ktv_supplychain_farmer sf
                        LEFT JOIN ktv_supplychain_farmer_bean_type sfb ON sfb.FarmerID=sf.FarmerID
                        LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=sf.FarmerID
                        LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    WHERE
                        sf.StatusCode='active' AND sfb.SupplychainID=? AND SUBSTR(NOW(),1,10) BETWEEN sf.DateStart AND sf.DateEnd
                    GROUP BY c.CPGid";
            $Q = $this->db->query($sql, array($get['sid'])); 
            if($Q->num_rows() > 0){
                $result = $Q->result_array();
                $output['FarmerGroup'] = $result;
            }else{
                $output['FarmerGroup'] = array();
            }
        }else if($DownloadFarmerTypeID=='2' || $DownloadFarmerTypeID=='3'){
            if($DownloadFarmerTypeID=='3'){
                $df1 = '/*'; $df2 = '*/';
            }else{
                $df1 = ''; $df2 = '';
            }
            $area = $this->mfarmer->_getSupplychainArea($get['sid']);
            $DistrictID = '';
                for($i=0; $i<count($area); $i++){
                    if($i==0){
                        $koma = '';
                    }else{
                        $koma = ',';
                    }
                    $DistrictID .= $koma.$area[$i];
                }
            $sql = "SELECT
                        c.CPGid, 
                        (c.GroupName) GroupName, 
                        c.VillageID
                    FROM
                        ktv_cocoa_farmer f
                        LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
                        LEFT JOIN ktv_ref_farmer_type rft ON rft.FarmertypeID=ft.FarmertypeID
                        LEFT JOIN ktv_village v ON f.VillageID=v.VillageID
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    WHERE
                        f.StatusCode='active' $df1 AND rft.PartnerID=? $df2
                        AND sd.DistrictID IN ($DistrictID)
                    GROUP BY c.CPGid";
            $Q = $this->db->query($sql, array($get['sid'], $PartnerID));
            if($Q->num_rows() > 0){
                $result = $Q->result_array();
                $output['FarmerGroup'] = $result;
            }else{
                $output['FarmerGroup'] = array();
            }
        }else{
            $ims = array();

            //check ims id nya
            $bu = $this->mfarmer->_getIMSbySupplychainID($get['sid']);
            $ch = $this->mfarmer->_getIMSbyCHSupplychainID($get['sid']);
            
            if(count($bu) > 0) {
                foreach($bu as $key => $b) {
                    array_push($ims,$b['IMSID']);
                }
            }

            if(count($ch) > 0) {
                foreach($ch as $ckey => $c){
                    array_push($ims,$c['IMSID']);
                } 
            }
            
            $area = $this->mfarmer->_getSupplychainArea($get['sid']);
            $ims = array_unique($ims);
            
            if(count($ims) > 0) {
                //$Q = $this->db->get();
                $IMSID = '';
                for($i=0; $i<count($ims); $i++){
                    if($i==0){
                        $koma = '';
                    }else{
                        $koma = ',';
                    }
                    $IMSID .= $koma.$ims[$i];
                }
                $DistrictID = '';
                for($i=0; $i<count($area); $i++){
                    if($i==0){
                        $koma = '';
                    }else{
                        $koma = ',';
                    }
                    $DistrictID .= $koma.$area[$i];
                }

                if($DownloadFarmerTypeID=='5'){
                    $dfa = ''; $dfb = '';
                    $district1 = '/*'; $district2 = '*/';
                }else{
                    $dfa = '/*'; $dfb = '*/';
                    $district1 = ''; $district2 = '';
                }
                    
                $sql = "SELECT
                            c.CPGid, 
                            (c.GroupName) GroupName, 
                            c.VillageID
                        FROM
                            ktv_ims i
                            LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.IMSID=i.IMSID AND i.CertEventStatus='2'
                            LEFT JOIN ktv_cocoa_certification_afl_farmer afl ON afl.IMSID=i.IMSID  AND i.CertEventStatus='1' /*AND (i.CertEventStatus!='2' OR i.CertEventStatus IS NULL)*/
                            LEFT JOIN ktv_cocoa_certification_pre_afl preafl ON preafl.IMSID=afl.IMSID AND preafl.FarmerID=afl.FarmerID AND preafl.StatusCode='active'
                            LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=IF(i.CertEventStatus='2', ccf.FarmerID, afl.FarmerID)
                            LEFT JOIN ktv_village v ON f.VillageID=v.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                            LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                            LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
                            LEFT JOIN ktv_ref_farmer_type rft ON rft.FarmerTypeID=ft.FarmerTypeID
                            
                            LEFT JOIN ktv_ims_master im ON im.IMSMasterID=i.IMSMasterID
                            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=im.CertHolderID
                            LEFT JOIN ktv_ref_certification_program rcp ON rcp.CertProgID=ch.CertProgID
                            $dfa
                            LEFT JOIN ktv_ims_supplychain_farmer imf ON imf.IMSID=i.IMSID AND imf.SupplychainID=? AND imf.FarmerID=f.FarmerID
                            $dfb
                        WHERE
                            i.StatusCode='active' AND i.ICSStatus='1'
                            AND (ccf.FarmerID IS NOT NULL OR afl.CertStatusAudit='Comply')
                            AND i.IMSID IN ($IMSID)
                            $district1 AND sd.DistrictID IN ($DistrictID) $district2
                            $dfa AND imf.FarmerID IS NOT NULL $dfb
                        GROUP BY c.CPGid";

                $AllowNewFarmer = $this->db->query("SELECT AllowNewFarmer FROM ktv_supplychain_org WHERE SupplychainID=?", array($get['sid']))->row()->AllowNewFarmer;
                if($AllowNewFarmer=='true'){
                    if($DistrictID==''){
                        $DistrictID = "''";
                    }
                    
                    $sql2 = "SELECT
                                c.CPGid, 
                                (c.GroupName) GroupName, 
                                c.VillageID
                            FROM
                                ktv_applicant_farmers af
                                LEFT JOIN ktv_ref_farmer_type rft ON rft.FarmerTypeID=af.FarmertypeID
                                LEFT JOIN ktv_cpg c ON c.CPGid=af.CPGid
                                LEFT JOIN ktv_district d ON d.DistrictID=af.DistrictID
                                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=af.SubDistrictID
                                LEFT JOIN ktv_village v ON v.VillageID=af.VillageID
                            WHERE
                                af.StatusCode='active'
                                AND rft.PartnerID=? AND rft.Remarks='IsCandidateFarmer'
                                /* AND af.SupplychainID=? */
                                AND sd.DistrictID IN ($DistrictID)
                            GROUP BY c.CPGid 
                    ";//PartnerID, SupplychainID

                    $sql3 = "SELECT 
                                c.CPGid, 
                                (c.GroupName) GroupName, 
                                c.VillageID
                            FROM 
                                ktv_cocoa_farmer f
                                LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
                                LEFT JOIN ktv_ref_farmer_type rft ON rft.FarmertypeID=ft.FarmertypeID
                                LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                                LEFT JOIN ktv_village v ON v.VillageID=f.VillageID
                                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                            WHERE
                                f.StatusCode='active' AND f.isCertified!='1'
                                AND rft.PartnerID=?
                                AND sd.DistrictID IN ($DistrictID)
                            GROUP BY c.CPGid
                    "; //PartnerID

                    $sql_union = "SELECT * FROM ($sql UNION $sql2 UNION $sql3) dt GROUP BY dt.CPGid";
                    $Q = $this->db->query($sql_union, array($get['sid'], $PartnerID, $get['sid'], $PartnerID));    
                }else{
                    $Q = $this->db->query($sql, array($get['sid']));
                }     
                if($Q->num_rows() > 0){
                    $result = $Q->result_array();
                    $output['FarmerGroup'] = $result;
                }else{
                    $output['FarmerGroup'] = array();
                }
            }else{
                $output['FarmerGroup'] = array();
            }  
        }
        return array();
        
    }

    private function _getTransLabel($SupplychainID, $PartnerID) {
        $sql = "SELECT
                    TransLabelID, TransLabelName, MinPrice, MaxPrice, DefaultPrice
                FROM
                    ref_trans_label
                WHERE
                    StatusCode='active' AND SupplychainID=? AND PartnerID=?";
        $Q = $this->db->query($sql, array($SupplychainID, $PartnerID));
        if(@$Q->num_rows() > 0){
            $result = $Q->result_array();
        }else{
           $result = array();
        }
        return $result;
    }

    function _getIMSDetail($supplychainid) {
        $sql = "SELECT
                    i.IMSID,
                    i.CertEventName,
                    rcp.CertProgName,
                    i.CertificationStart,
                    i.CertificationEnd,
                    IF(i.ValidityStart IS NOT NULL AND i.ValidityStart!='0000-00-00', i.ValidityStart, i.AFLSalesStart) AS ValidityStart,
                    IF(i.ValidityEnd IS NOT NULL AND i.ValidityEnd!='0000-00-00', i.ValidityEnd, i.AFLSalesEnd) AS ValidityEnd
                FROM
                    ktv_ims i
                    LEFT JOIN ktv_ims_master im ON im.IMSMasterID=i.IMSMasterID
                    LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=im.CertHolderID AND ch.StatusCode='active'
                    LEFT JOIN ktv_ims_buying_unit bu ON bu.IMSID=i.IMSID AND bu.StatusCode='active'
                    LEFT JOIN ktv_ref_certification_program rcp ON rcp.CertProgID=ch.CertProgID
                WHERE
                    i.StatusCode='active' AND i.ICSStatus='1'
                    AND (
                        DATE( NOW( ) ) BETWEEN DATE( i.ValidityStart ) AND DATE( i.ValidityEnd )
                        OR DATE( NOW( ) ) BETWEEN DATE( i.AFLSalesStart ) AND DATE( i.AFLSalesEnd )
                    )
                    AND (ch.SupplychainID=? OR bu.SupplychainID=?) 
                GROUP BY i.IMSID";
        $Q = $this->db->query($sql, array($supplychainid, $supplychainid));

        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            return $result;
        }

        return '';
    }

    function checkUserLoginStatus($username, $Token){
        $sql = "SELECT kso.IsSingleLogin
                    FROM
                        view_supplychain_staff vss
                        LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=vss.SupplychainID
                    WHERE
                        vss.UserName=?";
        $IsSingleLogin = @$this->db->query($sql, array($username))->row()->IsSingleLogin;
        if($IsSingleLogin=='0'){
            return false;
        }else{
            $sql = "SELECT * FROM sys_log_access_mobile WHERE UserName=? AND StatusCode='active' AND Token!=?";
            $query = $this->db->query($sql, array($username, $Token));
            if($query->num_rows() > 0){
                $query = $this->db->query("INSERT INTO sys_mobile_invalid_token_log(Username, Token, LogType) VALUES(?, ?, ?)", array($username, $Token, 'checkUserLoginStatus-IsSingleLogin'));
                return true;
            }else{
                $query = $this->db->query("INSERT INTO sys_mobile_invalid_token_log(Username, Token, LogType) VALUES(?, ?, ?)", array($username, $Token, 'checkUserLoginStatus'));
                return false;
            }
        }
    }

    function checkUserLogoutStatus($username, $Token){
        $sql = "SELECT kso.IsSingleLogin
                FROM
                    view_supplychain_staff vss
                    LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=vss.SupplychainID
                WHERE
                    vss.UserName=?";
        $IsSingleLogin = @$this->db->query($sql, array($username))->row()->IsSingleLogin;
        if($IsSingleLogin=='0'){
            $data['auth'] = true;
        }else{
            $sql = "SELECT * FROM sys_log_access_mobile WHERE UserName=? AND Token=?";
            $query = $this->db->query($sql, array($username, $Token));
            if($query->num_rows() > 0){
                //$data['IsLoggedIn'] = true;
                $sql2 = "SELECT * FROM sys_log_access_mobile WHERE UserName=? AND Token=? AND StatusCode='active'";
                $query2 = $this->db->query($sql2, array($username, $Token));
                if($query2->num_rows() > 0){
                    $data['auth'] = true;
                }else{
                    $query = $this->db->query("INSERT INTO sys_mobile_invalid_token_log(Username, Token, LogType) VALUES(?, ?, ?)", array($username, $Token, 'checkUserLogoutStatus'));
                    $data['auth'] = false;
                }
            }else{
                $data['auth'] = true;
            }
        }
        return $data;
    }

    public function _getFirstBuyer($PartnerID,&$output) {
        $return = array();
        $sql = "SELECT 
                    fb.FirstBuyerPartnerID,
                    pp.PartnerName AS FirstBuyerName
                FROM
                    ktv_supplychain_first_buyer fb
                    LEFT JOIN ktv_program_partner pp ON pp.PartnerID=fb.FirstBuyerPartnerID
                WHERE
                    fb.StatusCode='active' AND fb.PartnerID=?";
        $query = $this->db->query($sql, array($PartnerID));
        if($query->num_rows() > 0){
            $return = $query->result_array();
        }else{
            $sql = "SELECT 
                    pp. PartnerID FirstBuyerPartnerID,
                    pp.PartnerName AS FirstBuyerName
                FROM
                    ktv_program_partner pp
                WHERE
                    pp.PartnerID=?";
            $query = $this->db->query($sql, array($PartnerID));
            $return = $query->result_array();
        }
        $output['FirstBuyer'] = $return;
        return $return;
    }

    public function doLoginTraceabilityManual($username = false, $password = false) {
        $output = array();
        $return['success'] = false;
        $id = $this->_getUserDetail($username,$password,$output);
        if($id['success']==true){
            $org = $this->_getOrgDetail($id['id'],$output);
            $PartnerID = $this->db->query("SELECT PartnerID FROM ktv_supplychain_org_partner WHERE SupplychainID=?", array($org))->row()->PartnerID;
            if(!$this->db->_error_number()){
                $this->load->helper('jwt_helper');
                $this->load->helper('authorization_helper');
                $tokenData = array();
                $tokenData['sid'] = $org;
                $tokenData['userid'] = $id['id'];
                $tokenData['username'] = $username;
                $tokenData['timestamp'] = date('Y-m-d H:i:s');
                $return['success'] = true;
                $return['IdToken'] = AUTHORIZATION::generateToken($tokenData);
            }
        }else{
            $return['success'] = false;
            $return['pass'] = $id['pass'];
            
        }
        return $return;
    }
    
    function _getUnit($orgid, &$output) {

        $Q = $this->db->query("SELECT r.UnitID, r.UnitName, r.Conversion, k.IsDefault
                                FROM ktv_supplychain_org_unit k 
                                    LEFT JOIN ref_supplychain_unit r ON r.UnitID=k.UnitID
                                WHERE r.StatusCode='active' AND k.StatusCode='active' AND k.SupplychainID = '" . $orgid . "'");
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['Unit'] = $result;
        }else{
            $output['Unit'][0]['UnitID'] = 1;
            $output['Unit'][0]['UnitName'] = 'kg';
            $output['Unit'][0]['Conversion'] = 1.0;
            $output['Unit'][0]['IsDefault'] = 1;
            $result = $output['Unit'];
        }
        return $result;
    }

    function _getCurrency($orgid, &$output) {
        $sql = "SELECT
                    r.CurrID AS CurrencyID,
                    'ID' AS CountryCode,
                    r.CurrCode AS CurrencyCode,
                    r.CurrSymbol AS CurrencySymbol,
                    r.Currname AS CurrencyName,
                    k.IsDefault 
                FROM
                    ktv_supplychain_org_currency k
                    LEFT JOIN ktv_ref_currency r ON r.CurrID = k.CurrencyID 
                WHERE
                    r.StatusCode = 'active' 
                    AND k.StatusCode = 'active' AND k.SupplychainID =?";
        $Q = $this->db->query($sql, array($orgid));
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['Currency'] = $result;
        }else{
            $output['Currency'][0]['CurrencyID'] = 1;
            $output['Currency'][0]['CountryCode'] = 'ID';
            $output['Currency'][0]['CurrencyCode'] = '001';
            $output['Currency'][0]['CurrencySymbol'] = 'IDR';
            $output['Currency'][0]['CurrencyName'] = 'Rupiah';
            $output['Currency'][0]['IsDefault'] = 1;
            $result = $output['Currency'];
        }
        return $result;
    }

    function _getPaymentMethod($sid='', $PartnerID='') {
        $sql = "SELECT pm.PaymentMethodID, pm.PaymentGroupID, pm.PaymentMethod, IFNULL(pm.Image, '') `Image` FROM ref_tc_payment_method pm WHERE pm.StatusCode='active'";
        $output = array();
        $Q = $this->db->query($sql);
        if($Q->num_rows()){
            $output = $Q->result_array();
        }
        return $output;
    }

    function _getPaymentStatus($sid='', $PartnerID='') {
        $sql = "SELECT PaymentStatusID, PaymentStatus FROM ref_tc_payment_method_status WHERE StatusCode='active'";
        $output = array();
        $Q = $this->db->query($sql);
        if($Q->num_rows()){
            $output = $Q->result_array();
        }
        return $output;
    }

    function _getPaymentGroup($sid='', $PartnerID='') {
        $sql = "SELECT PaymentGroupID, PaymentGroup FROM ref_tc_payment_method_group WHERE StatusCode='active'";
        $output = array();
        $Q = $this->db->query($sql);
        if($Q->num_rows()){
            $output = $Q->result_array();
        }
        return $output;
    }

}

?>
