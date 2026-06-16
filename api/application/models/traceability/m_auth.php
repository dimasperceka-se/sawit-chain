<?php

/**
 * Authentication Model for Mobile
 *
 * @author Ardi <ardiantoro@koltiva.com>
 */
class m_auth extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function doLogin($username = false, $password = false) {
        $this->db->select('sys_user.UserRealName, sys_user.UserPassword,view_tc_supplychain_staff.*', FALSE);
        $this->db->from('sys_user');
        $this->db->join('view_tc_supplychain_staff', 'sys_user.UserId=view_tc_supplychain_staff.UserId', 'LEFT');
        $this->db->where('UserName',$username);
        $this->db->where('sys_user.StatusCode = "active"', null, false);
        $Q = $this->db->get();

        if($Q->num_rows()){
            $row = $Q->row();
            if($row->UserPassword === md5($password)){
                $token = $this->_getToken($username);
                $update = array('UserMobileToken' => $token);
                $this->db->where('UserName',$username);
                $this->db->update('sys_user',$update);

                if(!$this->db->_error_number()){
                    $data =  array('Name'=> $username, 'RealName'=> $row->UserRealName, 'UserID' => is_null ($row->UserID) ? "" : $row->UserID); 
                    $data['SupplychainID'] = is_null ($row->SupplychainID) ? "" : $row->SupplychainID;
                    $data['PartnerID'] = is_null ($row->PartnerID) ? "" : $row->PartnerID;
                    $data["relation"] = $this->relation($row->SupplychainID);
                    $data["package"] = $this->package($row->SupplychainID);
                    $data["quality"] = $this->quality($row->SupplychainID);
                    $data["District"] = $this->district($row->UserID);
                    $data["SubDistrict"] = $this->subdistrict($row->UserID);
                   /* Referensi */
                    //$this->load->model('traceability/m_farming_type','m_farmer_type');
                    //$this->load->model('traceability/m_seaweed_type','m_seaweed_type');
                    //$this->load->model('traceability/m_seaweed_type_detail','m_seaweed_type_detail');
                    $this->load->model('traceability/m_transport','m_transport');
                    $this->load->model('traceability/m_batch_status','m_batch_status');
                    $this->load->model('traceability/m_batch_type','m_batch_type');

                    //$data['farming_type'] = $this->m_farmer_type->get_data_farmer_type()['data'];
                    //$data['seaweed_type'] = $this->m_seaweed_type->get_data()['data'];
                    //$data['farming_seaweed_type'] = $this->m_seaweed_type_detail->get_data_mobile()['data'];
                    $data['transport'] = $this->m_transport->get_data()['data'];
                    $data['batch_status'] = $this->m_batch_status->get_data()['data'];
                    $data['batch_type'] = $this->m_batch_type->get_data()['data'];
                    $data['Token'] = $token;
                    return $data;
                }
            }
        }
        
        return false;
    }
    public function _getToken($data,&$output=0) {
        $salt = 'bismillah';
        $this->load->library('encrypt');
        $token = $this->encrypt->encode($data,$salt);

        return $token;
    }
    public function getUserDistrict($user,$return = FALSE) {
        $output = array();
        
        $this->db->select('ktv_access_staff.DistrictID,District');
        $this->db->from('ktv_access_staff');
        $this->db->where('`UserId` = (SELECT `UserId` FROM `sys_user` WHERE `UserName` = "'.$user.'")',NULL,FALSE);
        $this->db->join('ktv_district','ktv_district.DistrictID = ktv_access_staff.DistrictID','LEFT');
        $Q = $this->db->get();

        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            if($return){
                return $result;
            }
            
            foreach($result as $key => $value) {
                array_push($output, $value['DistrictID']);
            }
        }
        return $output;
    }
    private function relation($SupplychainID){
        $query = $this->db->select('b.*')
          ->from('ktv_tc_supplychain_org_rel a ')
          ->join('view_tc_supplychain_org b', 'b.SupplychainID=a.ParentID', 'left')
          ->where('ChildID', $SupplychainID)
          ->where('a.StatusCode', 'active')
          ->get();
        $result = array();                  
        if($query->num_rows()){
            $result = $query->result();
        }
        return $result;
    }
    private function package($SupplychainID){
        $query = $this->db->select('PackageID, PackageType, PackageWeight, PackageCapacity')
          ->from('ktv_tc_supplychain_package')
          ->where('SupplychainID', $SupplychainID)
          ->where('StatusCode', 'active')
          ->get();
        $result = array();                  
        if($query->num_rows()){
            $result = $query->result();
        }
        return $result;
    }
    private function quality($SupplychainID){
        $query = $this->db->select('`QualityID`, `SupplychainID`, `Name`, `Formula`, `Order`, `Type`, `MinValue`, `MaxValue`, `StandardValue`, `IsPrintVisible`')
          ->from('ktv_tc_supplychain_quality')
          ->where('SupplychainID', $SupplychainID)
          ->where('StatusCode', 'active')
          ->get();
        $result = array();                  
        if($query->num_rows()){
            $data = $query->result();
            foreach($data as $key => $val){
                $val->Value = array();
                $val->Formula = is_null ($val->Formula) ? "" : $val->Formula;
                $val->MinValue = is_null($val->MinValue) ? "" : $val->MinValue;
                $val->MaxValue = is_null($val->MaxValue) ? "" : $val->MaxValue;
                $val->StandardValue = is_null($val->StandardValue) ? "" : $val->StandardValue;
                if($val->Type == 'combo'){
                    $val->Value = $this->quality_value($val->QualityID);
                }
            }
            $result = $data;
        }
        return $result;
    }
    private function quality_value($id){
        $query = $this->db->select('ValueQualityID, Value')
          ->from('ktv_tc_supplychain_quality_value')
          ->where('QualityID', $id)
          ->where('StatusCode', 'active')
          ->get();
        $result = array();                  
        if($query->num_rows()){
            $result = $query->result();
        }
        return $result;
    }
    private function district($UserID){
        $sql = "SELECT b.DistrictID, b.District
                FROM ktv_access_staff a 
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                WHERE a.UserId = ?";
        $query = $this->db->query($sql, array($UserID));
        $result = array();                  
        if($query->num_rows()){
            $result = $query->result();
        }
        return $result;
    }

    private function subdistrict($UserID){
        $sql = "SELECT b.DistrictID, c.SubDistrictID, c.SubDistrict
                FROM ktv_access_staff a 
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID AND b.StatusCode='active'
                    LEFT JOIN ktv_subdistrict c ON c.DistrictID=b.DistrictID AND c.StatusCode='active'
                WHERE a.UserId = ?";
        $query = $this->db->query($sql, array($UserID));
        $result = array();                  
        if($query->num_rows()){
            $result = $query->result();
        }
        return $result;
    }
}

?>
