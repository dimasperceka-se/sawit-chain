<?php

/**
 * Authentication Model for Mobile
 *
 * @author Ardi <ardiantoro@koltiva.com>
 */
class m_auth extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function doLogin($username = false, $password = false)
    {
        $this->db->select('sys_user.UserRealName, 
                           sys_user.UserPassword,
                           view_tc_supplychain_staff.*, 
                           ktv_tc_supplychain_org.IsFarmer, 
                           ktv_tc_supplychain_org.IsNonFarmer, 
                           ktv_tc_supplychain_org.IsCompany, 
                           ktv_tc_supplychain_org.IsBatch,
                           ktv_tc_supplychain_org.AccessBy,  
                           ktv_tc_supplychain_org.CurrID, 
                           ktv_tc_supplychain_org.IsSent', false);
        $this->db->from('sys_user');
        $this->db->join('view_tc_supplychain_staff', 'sys_user.UserId=view_tc_supplychain_staff.UserId', 'LEFT');
        $this->db->join('ktv_tc_supplychain_org', 'view_tc_supplychain_staff.SupplychainID=ktv_tc_supplychain_org.SupplychainID', 'LEFT');
        $this->db->where('sys_user.UserName', $username);
        $this->db->where('sys_user.StatusCode = "active"', null, false);
        $Q = $this->db->get();
       
        if ($Q->num_rows()) {
            $row = $Q->row();
            if ($row->UserPassword === md5($password)) {
                $token = $this->_getToken($username);
                $update = array('UserMobileToken' => $token);
                $this->db->where('UserName', $username);
                $this->db->update('sys_user', $update);

                if($row->CurrID == 1){
                    $ActiveCurrency = "IDR";
                }else if($row->CurrID == 2){
                    $ActiveCurrency = "MYR";
                }else{
                    $ActiveCurrency = "";
                }

                if (!$this->db->_error_number()) {
                    $data = array(
                        'Name' => $username, 
                        'RealName' => $row->UserRealName, 
                        'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                        'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                    );
                    $data['SupplychainID'] = is_null($row->SupplychainID) ? "" : $row->SupplychainID;
                    $data['PartnerID'] = is_null($row->PartnerID) ? "" : $row->PartnerID;
                    $data['IsFarmer'] = is_null($row->IsFarmer) ? "0" : $row->IsFarmer;
                    $data['IsNonFarmer'] = is_null($row->IsNonFarmer) ? "0" : $row->IsNonFarmer;
                    $data['IsCompany'] = is_null($row->IsCompany) ? "0" : $row->IsCompany;
                    $data['IsBatch'] = is_null($row->IsBatch) ? "0" : $row->IsBatch;
                    $data['AccessBy'] = is_null($row->AccessBy) ? "0" : $row->AccessBy;
                    $data['CurrID'] = is_null($row->CurrID) ? "0" : $row->CurrID;
                    $data['ActiveCurrency'] = $ActiveCurrency;
                    $data['IsSent'] = is_null($row->IsSent) ? "0" : $row->IsSent;
                    $data["relation"] = $this->relation($row->SupplychainID);
                    $data["plantation"] = $this->plantation($row->ObjID, $row->ObjType);
                    $data["package"] = $this->package($row->SupplychainID);
                    $data["quality"] = $this->quality($row->SupplychainID);
                    $data["Province"] = $this->provinceFA($row->SupplychainID,$row->UserID,$row->AccessBy);
                    $data["District"] = $this->districtNew($row->SupplychainID,$row->UserID);
                    $data["SubDistrict"] = $this->subdistrictNew($row->SupplychainID,$row->UserID);
                    $data["Village"] = $this->villageNew($row->SupplychainID,$row->UserID);
                    $sid = $this->get_all_relation_id($row->SupplychainID);
                    $data['org'] = $this->org($sid);
                    $data['orgRel'] = $this->orgRel($sid);

                    /* Referensi */
                    //$this->load->model('traceability/m_farming_type','m_farmer_type');
                    //$this->load->model('traceability/m_seaweed_type','m_seaweed_type');
                    //$this->load->model('traceability/m_seaweed_type_detail','m_seaweed_type_detail');
                    $this->load->model('traceability/m_transport', 'm_transport');
                    // $this->load->model('traceability/m_batch_status', 'm_batch_status');
                    // $this->load->model('traceability/m_batch_type', 'm_batch_type');
                    $this->load->model('traceability/m_batch_status', 'm_batch_status');
                    $this->load->model('traceability/m_batch_type', 'm_batch_type');
                    $this->load->model('traceability_api/m_supplychain_staff','m_supplychain_staff');
                                        
                    //$data['farming_type'] = $this->m_farmer_type->get_data_farmer_type()['data'];
                    //$data['seaweed_type'] = $this->m_seaweed_type->get_data()['data'];
                    //$data['farming_seaweed_type'] = $this->m_seaweed_type_detail->get_data_mobile()['data'];
                    $data['sme'] = $this->get_profile_sme($row->UserID, $row->SupplychainID, $row->ObjID, $row->ObjType);
                    $data['transport'] = $this->m_transport->get_data()['data'];
                    // $data['batch_status'] = $this->m_batch_status->get_data()['data'];
                    // $data['batch_type'] = $this->m_batch_type->get_data()['data'];
                    $data['lastbatch'] = $this->get_last_batch($row->SupplychainID);
                    $data['lasttrans'] = $this->get_last_trans($row->SupplychainID);
                    $data['Token'] = $token;
                    return $data;
                }
            }
        }

        return false;
    }

    public function doSme($UserID,$SID,$PID)
    {
        $this->db->select('view_tc_supplychain_org.SupplychainID, 
                view_tc_supplychain_org.PartnerID,
                ktv_tc_supplychain_org.IsSent,
                ktv_tc_supplychain_org.IsFarmer, 
                ktv_tc_supplychain_org.IsNonFarmer, 
                ktv_tc_supplychain_org.IsCompany, 
                ktv_tc_supplychain_org.IsBatch, 
                ktv_tc_supplychain_org.IsStorage, 
                ktv_tc_supplychain_org.CurrID, 
                ktv_tc_supplychain_org.AccessBy,
                ktv_members.MemberID,
                sys_user.UserID,
                sys_user.UserName,
                sys_user.UserRealName,
                sys_user.UserPassword', false);
        $this->db->from('ktv_tc_supplychain_staff_rel');
        $this->db->join('ktv_staffs', 'ktv_staffs.StaffID = ktv_tc_supplychain_staff_rel.StaffID', 'LEFT');
        $this->db->join('view_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID = ktv_tc_supplychain_staff_rel.SupplychainID', 'LEFT');
        $this->db->join('ktv_members', 'ktv_members.MemberID = view_tc_supplychain_org.ObjID', 'LEFT');
        $this->db->join('ktv_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID=ktv_tc_supplychain_org.SupplychainID', 'LEFT');
        $this->db->join('ktv_persons', 'ktv_persons.PersonID = ktv_staffs.PersonID', 'LEFT');
        $this->db->join('sys_user', 'sys_user.UserID = ktv_persons.UserID', 'LEFT');
        $this->db->where('sys_user.UserID', $UserID);
        $this->db->where('ktv_tc_supplychain_staff_rel.StatusCode = "active"', null, false);
        $this->db->where('sys_user.StatusCode = "active"', null, false);

        $Q = $this->db->get();
       
        if ($Q->num_rows()) {
            $row = $Q->row();
            $token = $this->_getToken($userid);
            $update = array('UserMobileToken' => $token);
            $this->db->where('UserID', $userid);
            $this->db->update('sys_user', $update);

            if($row->CurrID == 1){
                $ActiveCurrency = "IDR";
            }else if($row->CurrID == 2){
                $ActiveCurrency = "MYR";
            }else{
                $ActiveCurrency = "";
            }

            if (!$this->db->_error_number()) {
                $data = array(
                    'RealName' => $row->UserRealName, 
                    'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                    'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                );
                $data['SupplychainID'] = is_null($row->SupplychainID) ? "" : $row->SupplychainID;
                $data['PartnerID'] = is_null($row->PartnerID) ? "" : $row->PartnerID;
                $data['IsFarmer'] = is_null($row->IsFarmer) ? "0" : $row->IsFarmer;
                $data['IsNonFarmer'] = is_null($row->IsNonFarmer) ? "0" : $row->IsNonFarmer;
                $data['IsCompany'] = is_null($row->IsCompany) ? "0" : $row->IsCompany;
                $data['IsBatch'] = is_null($row->IsBatch) ? "0" : $row->IsBatch;
                $data['IsStorage'] = is_null($row->IsStorage) ? "0" : $row->IsStorage;
                $data['CurrID'] = is_null($row->CurrID) ? "0" : $row->CurrID;
                $data['AccessBy'] = is_null($row->AccessBy) ? "0" : $row->AccessBy;
                $data['ActiveCurrency'] = $ActiveCurrency;
                $data['IsSent'] = is_null($row->IsSent) ? "0" : $row->IsSent;
                $data["relation"] = $this->relation($row->SupplychainID);
                $data["plantation"] = $this->plantation($row->MemberID, $row->ObjType);
                $data["package"] = $this->package($row->SupplychainID);
                $data["quality"] = $this->quality($row->SupplychainID);
                $data["Province"] = $this->provinceFA($row->SupplychainID,$row->UserID,$row->AccessBy);
                $data["District"] = $this->districtNew($row->SupplychainID,$row->UserID);
                $data["SubDistrict"] = $this->subdistrictNew($row->SupplychainID,$row->UserID);
                $data["Village"] = $this->villageNew($row->SupplychainID,$row->UserID);
                $sid = $this->get_all_relation_id($row->SupplychainID);
                $data['org'] = $this->org($sid);
                $data['orgRel'] = $this->orgRel($sid);

                /* Referensi */
                $this->load->model('traceability/m_transport', 'm_transport');
                $this->load->model('traceability/m_batch_status', 'm_batch_status');
                $this->load->model('traceability/m_batch_type', 'm_batch_type');
                $this->load->model('traceability_api/m_supplychain_staff','m_supplychain_staff');
                                    
                $data['transport'] = $this->m_transport->get_data()['data'];
                
                $data['lastbatch'] = $this->get_last_batch($row->SupplychainID);
                $data['lasttrans'] = $this->get_last_trans($row->SupplychainID);
                $data['Token'] = $token;
                return $data;
            }
        }

        return false;
    }

    public function getDataUser($username = false)
    {
        // $this->db->select(' b.SupplychainID, 
        //                     b.PartnerID, 
        //                     b.IsSent, 
        //                     b.IsFarmer, 
        //                     b.IsNonFarmer, 
        //                     b.IsCompany, 
        //                     b.IsBatch,
        //                     b.CurrID,
        //                     e.UserID, 
        //                     e.UserName,
        //                     e.UserRealName,
        //                     e.UserPassword', false);
        // $this->db->from('ktv_tc_supplychain_org b');
        // $this->db->join('ktv_persons d', 'd.PersonID = c.PersonID', 'LEFT');
        // $this->db->join('sys_user e', 'e.UserID = d.UserID', 'LEFT');
        // $this->db->join('view_tc_supplychain_staff f', 'f.UserId = e.UserId', 'LEFT');
        // $this->db->where('f.UserName', $username);
        // $this->db->where('e.StatusCode = "active"', null, false);

        $this->db->select('view_tc_supplychain_org.SupplychainID, 
                           view_tc_supplychain_org.PartnerID,
                           ktv_tc_supplychain_org.IsFarmer, 
                           ktv_tc_supplychain_org.IsNonFarmer, 
                           ktv_tc_supplychain_org.IsCompany, 
                           ktv_tc_supplychain_org.IsBatch, 
                           ktv_tc_supplychain_org.CurrID,
                           ktv_tc_supplychain_org.AccessBy, 
                           ktv_tc_supplychain_org.IsSent,
                           view_tc_supplychain_org.Name,
                           sys_user.UserID,
                           sys_user.UserName,
                           sys_user.UserRealName,
                           sys_user.UserPassword', false);
        $this->db->from('ktv_tc_supplychain_staff_rel');
        $this->db->join('ktv_staffs', 'ktv_staffs.StaffID = ktv_tc_supplychain_staff_rel.StaffID', 'LEFT');
        $this->db->join('view_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID = ktv_tc_supplychain_staff_rel.SupplychainID', 'LEFT');
        $this->db->join('ktv_members', 'ktv_members.MemberID = view_tc_supplychain_org.ObjID', 'LEFT');
        $this->db->join('ktv_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID=ktv_tc_supplychain_org.SupplychainID', 'LEFT');
        $this->db->join('ktv_persons', 'ktv_persons.PersonID = ktv_staffs.PersonID', 'LEFT');
        $this->db->join('sys_user', 'sys_user.UserID = ktv_persons.UserID', 'LEFT');
        $this->db->where('sys_user.UserName', $username);
        $this->db->where('ktv_tc_supplychain_staff_rel.StatusCode = "active"', null, false);
        $this->db->where('sys_user.StatusCode = "active"', null, false);
        $Q = $this->db->get();
       
        if ($Q->num_rows()) {
            $row = $Q->row();
                $token = $this->_getToken($username);
                $update = array('UserMobileToken' => $token);
                $this->db->where('UserName', $username);
                $this->db->update('sys_user', $update);
                if($row->CurrID == 1){
                    $ActiveCurrency = "IDR";
                }else if($row->CurrID == 2){
                    $ActiveCurrency = "MYR";
                }else{
                    $ActiveCurrency = "";
                }
                
                if (!$this->db->_error_number()) {
                    $data = array(
                        'Name' => $username, 
                        'RealName' => $row->UserRealName, 
                        'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                        'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                    );
                    $data['SupplychainID'] = is_null($row->SupplychainID) ? "" : $row->SupplychainID;
                    $data['PartnerID'] = is_null($row->PartnerID) ? "" : $row->PartnerID;
                    $data['IsFarmer'] = is_null($row->IsFarmer) ? "0" : $row->IsFarmer;
                    $data['IsNonFarmer'] = is_null($row->IsNonFarmer) ? "0" : $row->IsNonFarmer;
                    $data['IsCompany'] = is_null($row->IsCompany) ? "0" : $row->IsCompany;
                    $data['IsBatch'] = is_null($row->IsBatch) ? "0" : $row->IsBatch;
                    $data['AccessBy'] = is_null($row->AccessBy) ? "0" : $row->AccessBy;
                    $data['CurrID'] = is_null($row->CurrID) ? "0" : $row->CurrID;
                    $data['PartnerID'] = is_null($row->PartnerID) ? "" : $row->PartnerID;
                    $data['ActiveCurrency'] = $ActiveCurrency;
                    $data['IsSent'] = is_null($row->IsSent) ? "0" : $row->IsSent;
                    $data["relation"] = $this->relation($row->SupplychainID);
                    $data["plantation"] = $this->plantation($row->ObjID, $row->ObjType);
                    $data["package"] = $this->package($row->SupplychainID);
                    $data["quality"] = $this->quality($row->SupplychainID);
                    $data["Province"] = $this->provinceFA($row->SupplychainID,$row->UserID,$row->AccessBy);
                    $data["District"] = $this->districtNew($row->SupplychainID,$row->UserID);
                    $data["SubDistrict"] = $this->subdistrictNew($row->SupplychainID,$row->UserID);
                    $data["Village"] = $this->villageNew($row->SupplychainID,$row->UserID);
                    $sid = $this->get_all_relation_id($row->SupplychainID);
                    $data['org'] = $this->org($sid);
                    $data['orgRel'] = $this->orgRel($sid);
                    
                    
                    /* Referensi */
                    $this->load->model('traceability/m_transport', 'm_transport');
                    $this->load->model('traceability/m_batch_status', 'm_batch_status');
                    $this->load->model('traceability/m_batch_type', 'm_batch_type');
                    $this->load->model('traceability_api/m_supplychain_staff','m_supplychain_staff');
                                        
                    $data['sme'] = $this->get_profile_sme($row->UserID, $row->SupplychainID, $row->ObjID, $row->ObjType);
                    $data['transport']  = $this->m_transport->get_data()['data'];
                    $data['lastbatch']  = $this->get_last_batch($row->SupplychainID);
                    $data['lasttrans']  = $this->get_last_trans($row->SupplychainID);
                    $data['Token'] = $token;
                    return $data;
                }
        }

        return false;
    }
    
    public function getDataUserFarmgateNew($username = false)
    {
       //get
       $this->db->select('f.SupplychainID, 
                        f.PartnerID,
                        e.UserID, 
                        e.UserName,
                        e.UserRealName,
                        e.UserPassword', false);
        $this->db->from('sys_user e');
        $this->db->join('ktv_persons d', 'd.userid = e.userid', 'LEFT');
        $this->db->join('ktv_staffs c', 'c.PersonID = d.PersonID', 'LEFT');
        $this->db->join('view_tc_supplychain_staff f', 'f.PersonID = d.PersonID', 'LEFT');
        $this->db->join('ktv_tc_supplychain_staff_rel a', 'a.SupplychainID = f.SupplychainID', 'LEFT');
        $this->db->where('e.UserName', $username);
        $this->db->where('e.StatusCode = "active"', null, false);
        $Q = $this->db->get();
        // echo '<pre>';
        // echo $this->db->last_query();die;
        if ($Q->num_rows()) {
            $row = $Q->row();
                $token = $this->_getToken($username);
                $update = array('UserMobileToken' => $token);
                $this->db->where('UserName', $username);
                $this->db->update('sys_user', $update);
                if($row->CurrID == 1){
                    $ActiveCurrency = "IDR";
                }else if($row->CurrID == 2){
                    $ActiveCurrency = "MYR";
                }else{
                    $ActiveCurrency = "";
                }
                
                if (!$this->db->_error_number()) {
                    $data = array(
                        'Name' => $username, 
                        'RealName' => $row->UserRealName, 
                        'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                        'UserID' => is_null($row->UserID) ? "" : $row->UserID,
                    );
                    $data['SupplychainID'] = is_null($row->SupplychainID) ? "" : $row->SupplychainID;
                   
                    $data['PartnerID'] = is_null($row->PartnerID) ? "" : $row->PartnerID;
                    $data['ActiveCurrency'] = $ActiveCurrency;
                    
                    /* Referensi */
                    $this->load->model('traceability/m_transport', 'm_transport');
                    $this->load->model('traceability/m_batch_status', 'm_batch_status');
                    $this->load->model('traceability/m_batch_type', 'm_batch_type');
                    $this->load->model('traceability_api/m_supplychain_staff','m_supplychain_staff');
                                        
                    $data['sme'] = $this->get_profile_sme_farmgate_new($row->UserID, $row->SupplychainID, $row->ObjID, $row->ObjType);
                    
                    $data['transport']  = $this->m_transport->get_data()['data'];
                    $data['lastbatch']  = $this->get_last_batch($row->SupplychainID);
                    $data['lasttrans']  = $this->get_last_trans($row->SupplychainID);
                    $data['Token'] = $token;
                    return $data;
                }
        }

        return false;
    }

    public function getRelation($get)
    {
        $data = array();
        $data['org'] = $this->org('');
        $data['orgRel'] = $this->orgRel('');
        return $data;
    }

    public function _getToken($data, &$output = 0)
    {
        $salt = 'bismillah';
        //$this->load->library('encrypt');
        //$token = $this->encrypt->encode($data,$salt);
        $token = hash_pbkdf2("sha512", $salt . $data . date('Y-m-d H:i:s'), 'NIKOSB_VC', 10, 32);
        return $token;
    }
    public function getUserDistrict($user, $return = false)
    {
        $output = array();

        $this->db->select('ktv_access_staff.DistrictID,District');
        $this->db->from('ktv_access_staff');
        $this->db->where('`UserId` = (SELECT `UserId` FROM `sys_user` WHERE `UserName` = "' . $user . '")', null, false);
        $this->db->join('ktv_district', 'ktv_district.DistrictID = ktv_access_staff.DistrictID', 'LEFT');
        $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();
            if ($return) {
                return $result;
            }

            foreach ($result as $key => $value) {
                array_push($output, $value['DistrictID']);
            }
        }
        return $output;
    }
    private function relation($SupplychainID)
    {
        $query = $this->db->select('a.statusCode, 
                b.SupplychainID,
                b.PartnerID,
                b.ObjID, 
                b.ObjType,
                b.DisplayID,
                b.Alias,
                b.Name,
                b.Address,
                b.VillageID,
                b.Longitude,
                b.Latitude,
                mr.MroleID AS MRoleID')
            ->from('ktv_tc_supplychain_org_rel a ')
            ->join('view_tc_supplychain_org b', 'b.SupplychainID=a.ParentID', 'left')
            ->join('ktv_members m', 'm.MemberID = b.ObjID', 'left')
            ->join('ktv_member_role mr', 'mr.MemberID = m.MemberID', 'left')
            ->join('ktv_tc_supplychain_staff_rel c', 'c.SupplychainID = b.SupplyChainID','left')
            ->join('ktv_staffs ks','ks.StaffID = c.StaffID','left')
            ->join('ktv_persons kp','kp.PersonID = ks.PersonID','left')
            ->join('sys_user su','su.UserID = kp.UserID','left')
            ->where('a.ChildID', $SupplychainID)
            ->where('a.StatusCode', 'active')
            ->group_by("b.ObjID")
            ->get();
            // echo '<pre>';
            // echo $this->db->last_query();die;
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }
    private function package($SupplychainID)
    {
        $query = $this->db->select('PackageID, PackageType, PackageWeight, PackageCapacity')
            ->from('ktv_tc_supplychain_package')
            ->where('SupplychainID', $SupplychainID)
            ->where('StatusCode', 'active')
            ->get();
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }
    private function quality($SupplychainID)
    {
        $query = $this->db->select('`QualityID`, `SupplychainID`, `Name`, `Formula`, `Order`, `Type`, `MinValue`, `MaxValue`, `StandardValue`, `IsPrintVisible`')
            ->from('ktv_tc_supplychain_quality')
            ->where('SupplychainID', $SupplychainID)
            ->where('StatusCode', 'active')
            ->get();
        $result = array();
        if ($query->num_rows()) {
            $data = $query->result();
            foreach ($data as $key => $val) {
                $val->Value = array();
                $val->Formula = is_null($val->Formula) ? "" : $val->Formula;
                $val->MinValue = is_null($val->MinValue) ? "" : $val->MinValue;
                $val->MaxValue = is_null($val->MaxValue) ? "" : $val->MaxValue;
                $val->StandardValue = is_null($val->StandardValue) ? "" : $val->StandardValue;
                if ($val->Type == 'combo') {
                    $val->Value = $this->quality_value($val->QualityID);
                }
            }
            $result = $data;
        }
        return $result;
    }

    private function quality_value($id)
    {
        $query = $this->db->select('ValueQualityID, Value')
            ->from('ktv_tc_supplychain_quality_value')
            ->where('QualityID', $id)
            ->where('StatusCode', 'active')
            ->get();
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    private function provinceNew($SupplychainID,$UserID,$AccessBy){

        $sql = "SELECT SQL_CALC_FOUND_ROWS
            e.ProvinceID
            , e.Province
        FROM
            ktv_tc_supplychain_farmer xc
            LEFT JOIN ktv_members a ON a.MemberID = xc.FarmerID
            LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
            LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
            LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
            LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
            LEFT JOIN ktv_district f ON f.DistrictID = d.DistrictID
            LEFT JOIN ktv_province e ON e.ProvinceID = f.ProvinceID
        WHERE
            a.StatusCode = 'active' 
            AND xc.SupplychainID = ? 
            AND a.`StatusCode` = 'active'
            AND e.ProvinceID IS NOT NULL
        GROUP BY
            e.ProvinceID
        ";
       
        $query = $this->db->query($sql, array($UserID));

        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        $sql2 = "SELECT c.ProvinceID, c.Province
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_province c ON c.ProvinceID = b.ProvinceID
                WHERE a.UserId = ?
                GROUP BY c.ProvinceID";
        $query2 = $this->db->query($sql2, array($UserID));
        $result2 = array();
        if ($query2->num_rows()) {
            $result2 = $query2->result();
        }

        array_merge($result,$result2);
        return $result;
    }

    private function provinceFA($SupplychainID,$UserID,$AccessBy){
    
        $sql = "SELECT e.ProvinceID, e.Province
            FROM (`ktv_tc_supplychain_staff_rel`)
            LEFT JOIN `ktv_staffs` ON `ktv_staffs`.`StaffID` = `ktv_tc_supplychain_staff_rel`.`StaffID`
            LEFT JOIN `view_tc_supplychain_org` ON `view_tc_supplychain_org`.`SupplychainID` = `ktv_tc_supplychain_staff_rel`.`SupplychainID`
            LEFT JOIN `ktv_members` ON `ktv_members`.`MemberID` = `view_tc_supplychain_org`.`ObjID`
            LEFT JOIN `ktv_tc_supplychain_org` ON `view_tc_supplychain_org`.`SupplychainID`=`ktv_tc_supplychain_org`.`SupplychainID`
            LEFT JOIN `ktv_persons` ON `ktv_persons`.`PersonID` = `ktv_staffs`.`PersonID`
            LEFT JOIN `sys_user` ON `sys_user`.`UserID` = `ktv_persons`.`UserID`
            LEFT JOIN ktv_village c ON ktv_members.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
            LEFT JOIN ktv_district f ON f.DistrictID = d.DistrictID
            LEFT JOIN ktv_province e ON e.ProvinceID = f.ProvinceID
            WHERE `sys_user`.`UserID` =  ?
            AND ktv_tc_supplychain_staff_rel.StatusCode = 'active'
            AND sys_user.StatusCode = 'active'
        ";
       
        $query = $this->db->query($sql, array($UserID));

        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        $sql2 = "SELECT c.ProvinceID, c.Province
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_province c ON c.ProvinceID = b.ProvinceID
                WHERE a.UserId = ?
                GROUP BY c.ProvinceID";
        $query2 = $this->db->query($sql2, array($UserID));
        $result2 = array();
        if ($query2->num_rows()) {
            $result2 = $query2->result();
        }

        $resultMerge = array_merge($result,$result2);
        
        return $result2;
    }

    private function province($UserID)
    {
        $sql = "SELECT c.ProvinceID, c.Province
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_province c ON c.ProvinceID = b.ProvinceID
                WHERE a.UserId = ?
                GROUP BY c.ProvinceID";
        $query = $this->db->query($sql, array($UserID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    private function district($UserID)
    {
        $sql = "SELECT b.DistrictID, b.District, b.ProvinceID
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                WHERE a.UserId = ?";
        $query = $this->db->query($sql, array($UserID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    private function districtNew($SupplychainID,$UserID){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                e.ProvinceID
                , f.DistrictID
                , f.District
            FROM
                ktv_tc_supplychain_farmer xc
                LEFT JOIN ktv_members a ON a.MemberID = xc.FarmerID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
                LEFT JOIN ktv_district f ON f.DistrictID = d.DistrictID
                LEFT JOIN ktv_province e ON e.ProvinceID = f.ProvinceID
            WHERE
                a.StatusCode = 'active' 
                AND xc.SupplychainID = ? 
                AND a.`StatusCode` = 'active' 
                AND f.DistrictID IS NOT NULL
            GROUP BY
                f.DistrictID
        ";

        $query = $this->db->query($sql, array($SupplychainID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        $sql2 = "SELECT b.ProvinceID, b.DistrictID, b.District
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                WHERE a.UserId = ?";
        $query2 = $this->db->query($sql2, array($UserID));
        $result2 = array();
        if ($query2->num_rows()) {
            $result2 = $query2->result();
        }
        
        $results = array_merge( $result, $result2 );
        $results = array_map("unserialize", array_unique(array_map("serialize", $results)));
        //array is sorted on the bases of id
        sort( $results );
        return $results;
    }

    private function subdistrictNew($SupplychainID,$UserID){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                f.DistrictID
                , d.SubDistrictID
                , d.SubDistrict
            FROM
                ktv_tc_supplychain_farmer xc
                LEFT JOIN ktv_members a ON a.MemberID = xc.FarmerID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
                LEFT JOIN ktv_district f ON f.DistrictID = d.DistrictID
                LEFT JOIN ktv_province e ON e.ProvinceID = f.ProvinceID
            WHERE
                a.StatusCode = 'active' 
                AND xc.SupplychainID = ? 
                AND a.`StatusCode` = 'active' 
                AND d.SubDistrictID IS NOT NULL
            GROUP BY
                d.SubDistrictID
        ";

        $query = $this->db->query($sql, array($SupplychainID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        $sql = "SELECT b.DistrictID, c.SubDistrictID, c.SubDistrict
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID AND c.StatusCode='active'
                WHERE a.UserId = ? AND c.SubDistrictID IS NOT NULL";
        $query = $this->db->query($sql, array($UserID));
        $result2 = array();
        if ($query->num_rows()) {
            $result2 = $query->result();
        }
        
        $results = array_merge( $result, $result2 );
        $results = array_map("unserialize", array_unique(array_map("serialize", $results)));
        //array is sorted on the bases of id
        sort( $results );
        return $results;
    }

    private function subdistrict($UserID)
    {
        $sql = "SELECT b.DistrictID, c.SubDistrictID, c.SubDistrict
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID AND c.StatusCode='active'
                WHERE a.UserId = ? AND c.SubDistrictID IS NOT NULL";
        $query = $this->db->query($sql, array($UserID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    private function villageNew($SupplychainID,$UserID){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                d.SubDistrictID
                , c.VillageID
                , c.Village
            FROM
                ktv_tc_supplychain_farmer xc
                LEFT JOIN ktv_members a ON a.MemberID = xc.FarmerID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
                LEFT JOIN ktv_district f ON f.DistrictID = d.DistrictID
                LEFT JOIN ktv_province e ON e.ProvinceID = f.ProvinceID
            WHERE
                a.StatusCode = 'active' 
                AND xc.SupplychainID = ? 
                AND a.`StatusCode` = 'active' 
                AND c.VillageID IS NOT NULL
            GROUP BY
                c.VillageID
        ";

        $query = $this->db->query($sql, array($SupplychainID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        $sql = "SELECT c.SubDistrictID, d.VillageID, d.Village
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID AND c.StatusCode='active'
                    LEFT JOIN ktv_village d ON d.SubDistrictID=c.SubDistrictID AND d.StatusCode='active'
                WHERE a.UserId = ? AND d.VillageID IS NOT NULL";
        $query = $this->db->query($sql, array($UserID));
        $result2 = array();
        if ($query->num_rows()) {
            $result2 = $query->result();
        }
        $results = array_merge( $result, $result2 );
        $results = array_map("unserialize", array_unique(array_map("serialize", $results)));
        //array is sorted on the bases of id
        sort( $results );
        return $results;
    }
    
    private function village($UserID)
    {
        $sql = "SELECT c.SubDistrictID, d.VillageID, d.Village
                FROM ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID AND c.StatusCode='active'
                    LEFT JOIN ktv_village d ON d.SubDistrictID=c.SubDistrictID AND d.StatusCode='active'
                WHERE a.UserId = ? AND d.VillageID IS NOT NULL";
        $query = $this->db->query($sql, array($UserID));
        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    public function get_all_relation_id($SupplychainID){
        
        $sid[0] = $SupplychainID;
        $i = 0;
        $sql = "SELECT ParentID FROM ktv_tc_supplychain_org_rel WHERE ChildID=? AND StatusCode='active' AND ParentID!=0";
        $query1 = $this->db->query($sql, array($SupplychainID));
        if($query1->num_rows() > 0){
            foreach($query1->result() as $row1){
                $i++;
                $sid[$i] = $row1->ParentID;
                $query2 = $this->db->query($sql, array($row1->ParentID));
                if($query2->num_rows() > 0){
                    foreach($query2->result() as $row2){
                        $i++;
                        $sid[$i] = $row2->ParentID;
                        $query3 = $this->db->query($sql, array($row2->ParentID));
                        if($query3->num_rows() > 0){
                            foreach($query3->result() as $row3){
                                $i++;
                                $sid[$i] = $row3->ParentID;
                                
                            }
                        }
                        
                    }
                }
                
            }
        }
        $sql = "SELECT ChildID FROM ktv_tc_supplychain_org_rel WHERE ParentID=? AND StatusCode='active' AND ChildID!=0";
        $query1 = $this->db->query($sql, array($SupplychainID));
        if($query1->num_rows() > 0){
            foreach($query1->result() as $row1){
                $i++;
                $sid[$i] = $row1->ChildID;
                $query2 = $this->db->query($sql, array($row1->ChildID));
                if($query2->num_rows() > 0){
                    foreach($query2->result() as $row2){
                        $i++;
                        $sid[$i] = $row2->ChildID;
                        $query3 = $this->db->query($sql, array($row2->ChildID));
                        if($query3->num_rows() > 0){
                            foreach($query3->result() as $row3){
                                $i++;
                                $sid[$i] = $row3->ChildID;
                                
                            }
                        }
                        
                    }
                }
                
            }
        }
        return $sid;
    }

    public function org($sid)
    {
        
        if($sid!=''){
            $SupplychainID = implode(", ",$sid);
        }else{
            $SupplychainID = '';
        }
        // $sql = "SELECT * FROM view_tc_supplychain_org vso WHERE SupplychainID IN ($SupplychainID)";
        $sql = "SELECT
                    vso.* 
                FROM
                    view_tc_supplychain_org vso 
                    LEFT JOIN ktv_tc_supplychain_org kso ON kso.SupplychainID=vso.SupplychainID
                WHERE
                    vso.ObjID !=0 AND kso.StatusCode='active'";
        
        // query org baru di comment dulu
        // if($sid!=''){
        //     foreach($sid as $val){
        //         $SupplychainID = $val;
        //     }
        // }else{
        //     $SupplychainID = '';
        // }

        // $sql = "SELECT
		// 		  `z`.`SupplychainID` AS `SupplychainID`, 
        //           `z`.`PartnerID` AS `PartnerID`, 
        //           `z`.`ObjID` AS `ObjID`, 
        //           convert(`z`.`ObjType` using utf8) AS `ObjType`, 
        //           `z`.`MRoleID` AS `MRoleID`,
        //           (
        //             case `z`.`ObjType` when 'mill' then convert(
        //               `kmill`.`MillDisplayID` using utf8mb4
        //             ) when 'agent' then `a`.`Nin` when 'refinery' then convert(
        //               `kref`.`RefineryDisplayID` using utf8mb4
        //             ) else convert(
        //               `kcp`.`KCPDisplayID` using utf8mb4
        //             ) end
        //           ) AS `DisplayID`, 
        //           (
        //             case `z`.`ObjType` when 'mill' then convert(`kmill`.`Alias` using utf8mb4) when 'agent' then `a`.`CoopName` when 'refinery' then convert(
        //               `kref`.`CompanyName` using utf8mb4
        //             ) else convert(
        //               `kcp`.`CompanyName` using utf8mb4
        //             ) end
        //           ) AS `Alias`, 
        //           (
        //             case `z`.`ObjType` when 'mill' then convert(`kmill`.`MillName` using utf8mb4) when 'agent' then `a`.`MemberName` when 'refinery' then convert(
        //               `kref`.`RefineryName` using utf8mb4
        //             ) else convert(
        //               `kcp`.`KCPName` using utf8mb4
        //             ) end
        //           ) AS `Name`, 
        //           (
        //             case `z`.`ObjType` when 'mill' then convert(`kmill`.`Address` using utf8mb4) when 'agent' then `a`.`Address` when 'refinery' then convert(`kref`.`Address` using utf8mb4) else convert(
        //               `kcp`.`Address` using utf8mb4
        //             ) end
        //           ) AS `Address`, 
        //           (
        //             case `z`.`ObjType` when 'mill' then `kmill`.`VillageID` when 'agent' then `a`.`VillageID` when 'refinery' then `kref`.`VillageID` else `kcp`.`VillageID` end
        //           ) AS `VillageID`, 
        //           (
        //             case `z`.`ObjType` when 'mill' then `kmill`.`Longitude` when 'agent' then `a`.`Longitude` when 'refinery' then `kref`.`Longitude` else `kcp`.`Longitude` end
        //           ) AS `Longitude`, 
        //           (
        //             case `z`.`ObjType` when 'mill' then `kmill`.`Latitude` when 'agent' then `a`.`Latitude` when 'refinery' then `kref`.`Latitude` else `kcp`.`Latitude` end
        //           ) AS `Latitude`, 
        //           `a`.`MemberID` AS `MemberUID` 
        // FROM
        // 	 ktv_tc_supplychain_farmer xc
        //     LEFT JOIN ktv_members a ON a.MemberID = xc.FarmerID
        //     LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
        //     LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
        //     LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
        //     LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
        //     LEFT JOIN ktv_subdistrict d ON SUBSTR(a.VillageID,1,7) = d.SubDistrictID
        //     LEFT JOIN ktv_province e ON SUBSTR(a.VillageID,1,2) = e.ProvinceID
        //     LEFT JOIN ktv_district f ON SUBSTR(a.VillageID,1,4) = f.DistrictID 
        //     LEFT JOIN ktv_tc_supplychain_org z ON z.SupplychainID = xc.SupplychainID
        //     LEFT JOIN ktv_mill `kmill` on `kmill`.`MillID` = `z`.`ObjID` and `z`.`ObjType` = 'mill'
        //     LEFT JOIN ktv_tc_supplychain_org z2 ON a.MemberID = z2.ObjID and z.ObjType = 'agent'
        //     LEFT JOIN ktv_refinery kref ON kref.RefineryID = z.ObjID and z.ObjType = 'refinery'
        //     LEFT JOIN ktv_kcp_bulking kcp ON kcp.KCPID = z.ObjID
        // WHERE
        //     a.StatusCode = 'active' and z.SupplychainID = '$SupplychainID'
        // 	GROUP BY a.MemberID
        // ";
        // end
        
        $query = $this->db->query($sql);
    
        if ($query->num_rows() > 0) {
            $result = $query->result();
        }else{
            $result = array();
        }
        return $result;
    }

    private function orgRel($sid)
    {
        if($sid!=''){
            $SupplychainID = implode(", ",$sid);
        }else{
            $SupplychainID = '';
        }
        $sql = "SELECT * from ktv_tc_supplychain_org_rel WHERE /* (ChildID IN ($SupplychainID) OR ParentID IN ($SupplychainID)) AND */ StatusCode='active' AND DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN StartDate AND EndDate";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
        }else{
            $result = array();
        }
        return $result;
    }

    private function plantation($MemberID, $ObjType='')
    {
        if($ObjType=='mill'){
            $sql = "SELECT
                        p.PlotNr PlantationNr,
                        0 SurveyNr,
                        IFNULL(v.VillageID,'') VillageID,
                        p.Latitude,
                        p.Longitude,
                        IFNULL(p.GardenAreaHa, 0) GardenAreaHa,
                        IFNULL(v.Village,'') Village
                    FROM
                        ktv_survey_plot_status_mill p
                        LEFT JOIN ktv_mill m ON m.MIllID=p.MillID
                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                    WHERE
                        p.MillID = ?
                        AND p.StatusCode='active'
                    GROUP BY p.MillID, p.PlotNr";
        }else{
            $sql = "SELECT
                        p.PlotNr PlantationNr,
                        MAX(p.SurveyNr) SurveyNr,
                        IFNULL(p.VillageID,'') VillageID,
                        p.Latitude,
                        p.Longitude,
                        IFNULL(p.GardenAreaHa, 0) GardenAreaHa,
                        IFNULL(v.Village,'') Village
                    FROM
                        ktv_survey_plot p
                        LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID=p.MemberID AND ps.PlotNr=p.PlotNr
                        LEFT JOIN ktv_village v ON v.VillageID=p.VillageID
                    WHERE
                        p.MemberID = ?
                        AND ps.ActiveStatus='1'
                    GROUP BY
                        p.MemberID, p.PlotNr";
        }
        $query = $this->db->query($sql, array($MemberID));
        if ($query->num_rows()) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;
    }

    function get_profile_sme($UserID, $SupplychainID, $ObjID, $ObjType){
        
        $this->db->select('view_tc_supplychain_org.ObjID, 
                           view_tc_supplychain_org.SupplychainID, 
                           view_tc_supplychain_org.PartnerID,
                           ktv_tc_supplychain_org.IsFarmer, 
                           ktv_tc_supplychain_org.IsNonFarmer, 
                           ktv_tc_supplychain_org.IsCompany, 
                           ktv_tc_supplychain_org.IsBatch,
                           ktv_tc_supplychain_org.AccessBy, 
                           ktv_tc_supplychain_org.CurrID, 
                           ktv_tc_supplychain_org.IsSent,
                           view_tc_supplychain_org.Name', false);
        $this->db->from('ktv_tc_supplychain_staff_rel');
        $this->db->join('ktv_staffs', 'ktv_staffs.StaffID = ktv_tc_supplychain_staff_rel.StaffID', 'LEFT');
        $this->db->join('view_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID = ktv_tc_supplychain_staff_rel.SupplychainID', 'LEFT');
        $this->db->join('ktv_members', 'ktv_members.MemberID = view_tc_supplychain_org.ObjID', 'LEFT');
        $this->db->join('ktv_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID=ktv_tc_supplychain_org.SupplychainID', 'LEFT');
        $this->db->join('ktv_persons', 'ktv_persons.PersonID = ktv_staffs.PersonID', 'LEFT');
        $this->db->join('sys_user', 'sys_user.UserID = ktv_persons.UserID', 'LEFT');
        $this->db->where('sys_user.UserID', $UserID);
        $this->db->where('ktv_tc_supplychain_staff_rel.StatusCode = "active"', null, false);
        $this->db->where('sys_user.StatusCode = "active"', null, false);

        $data  = $this->db->get();
        
        if ($data->num_rows() > 0) {
            $row = $data->row();

            $result         = $data->result_array();

            $relationSme    = $this->relation($SupplychainID);
            $plantationSme  = $this->plantation($ObjID, $ObjType);
            $packageSme     = $this->package($SupplychainID);
            $qualitySme     = $this->quality($SupplychainID);
            $provinceSme    = $this->provinceFA($SupplychainID,$UserID,$AccessBy);
            $DistrictSme    = $this->districtNew($SupplychainID,$UserID);
            $SubDistrictSme = $this->subdistrictNew($SupplychainID,$UserID);
            $VillageSme     = $this->villageNew($SupplychainID,$UserID);

            $result = array();
            foreach ($data->result_array() as $row) {

                $data = array(
                    "RelationSme" => $relationSme,
                    "PlantatioSme" => $plantationSme,
                    "PackageSme" => $packageSme,
                    "QualitySme" => $qualitySme,
                    "ProvinceSme" => $ProvinceSme,
                    "ProvinceSme" => $provinceSme,
                    "DistrictSme" => $DistrictSme,
                    "SubDistrictSme" => $SubDistrictSme,
                    "VillageSme" => $VillageSme
                );
                
                $result[] = array_merge($row,$data);
            }   
            
            return $result;

        }else{
            $result = array();
        }
        
        return $result;
    }

    function get_profile_sme_farmgate_new($UserID, $SupplychainID, $ObjID, $ObjType){
        
        $this->db->select('view_tc_supplychain_org.ObjID, 
                           view_tc_supplychain_org.SupplychainID, 
                           view_tc_supplychain_org.PartnerID,
                           view_tc_supplychain_org.Name', false);
        $this->db->from('ktv_tc_supplychain_staff_rel');
        $this->db->join('ktv_staffs', 'ktv_staffs.StaffID = ktv_tc_supplychain_staff_rel.StaffID', 'LEFT');
        $this->db->join('view_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID = ktv_tc_supplychain_staff_rel.SupplychainID', 'LEFT');
        $this->db->join('ktv_members', 'ktv_members.MemberID = view_tc_supplychain_org.ObjID', 'LEFT');
        $this->db->join('ktv_tc_supplychain_org', 'view_tc_supplychain_org.SupplychainID=ktv_tc_supplychain_org.SupplychainID', 'LEFT');
        $this->db->join('ktv_persons', 'ktv_persons.PersonID = ktv_staffs.PersonID', 'LEFT');
        $this->db->join('sys_user', 'sys_user.UserID = ktv_persons.UserID', 'LEFT');
        $this->db->join('ktv_member_role', 'ktv_member_role.MemberID = ktv_members.MemberID', 'LEFT');
        $this->db->where('sys_user.UserID', $UserID);
        $this->db->where('ktv_tc_supplychain_staff_rel.StatusCode = "active"', null, false);
        $this->db->where('sys_user.StatusCode = "active"', null, false);
        $this->db->group_by('ktv_members.MemberID', $ObjID);

        $data  = $this->db->get();
        
        if ($data->num_rows() > 0) {
            $row = $data->row();

            $result         = $data->result_array();

            $role['RoleID']   = $this->get_role($row->ObjID);
            
            $result = array();
            foreach ($data->result_array() as $row) {

                $data = array();
                
                $result[] = array_merge($row,$data,$role);
            }   
            
            return $result;

        }else{
            $result = array();
        }
        
        return $result;
    }

    function get_last_batch($SupplychainID){
        $tgl = date('Ymd');
        $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_batch WHERE SupplyBatchNumber LIKE ? AND SupplyOrgID=?";
        $query = $this->db->query($sql, array("%$tgl%", $SupplychainID));
        return $query->row()->total;
    }

    function get_last_trans($SupplychainID){
        $tgl = date('Ymd');
        $sql = "SELECT COUNT(*) total FROM ktv_tc_supplychain_transaction WHERE TransNumber LIKE ? AND SupplychainID=?";
        $query = $this->db->query($sql, array("%$tgl%", $SupplychainID));
        return $query->row()->total;
    }

    function getPlantation($get){
        $org = $this->db->query("SELECT * FROM ktv_tc_supplychain_org WHERE SupplychainID=?", array($get['SID']))->result_array();
        $data['plantation'] = $this->plantation($org[0]['ObjID'], $org[0]['ObjType']);
        return $data;
    }

    private function get_role($ObjID)
    {
        $sql = "SELECT
                b.MRoleID
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.MemberID=b.MemberID
            WHERE
                a.MemberID = ?";
        $query = $this->db->query($sql, array($ObjID));
        if ($query->num_rows()) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;
    }
}
