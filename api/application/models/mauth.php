<?php

/**
 * Authentication Model for Mobile
 *
 * @author Ardi <ardiantoro@koltiva.com>
 */
class Mauth extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function doLogin($username = false, $password = false) {
        
        $this->db->select('sys_user.UserId,UserPassword,UserName,UserRealName,UserLanguage,IFNULL(a.PartnerID,b.PartnerID) AS PartnerID,PartnerName',FALSE);
        $this->db->from('sys_user');
        $this->db->join('ktv_private_staff a','a.UserId = sys_user.UserId','LEFT');
        $this->db->join('ktv_program_staff b','b.UserId = sys_user.UserId','LEFT');
        $this->db->join('ktv_program_partner c','c.PartnerID = IFNULL(a.PartnerID, b.PartnerID)','LEFT');
        $this->db->where('UserName',$username);
        $this->db->where('sys_user.StatusCode = "active"',null,false);
        $Q = $this->db->get();
        
        if($Q->num_rows()){
            $row = $Q->row();
            if($row->UserPassword === md5($password)){
                $token = $this->getToken($username);
                
                $update = array(
                    'UserMobileToken' => $token
                );
                
                $this->db->where('UserName',$username);
                $this->db->update('sys_user',$update);
                
                if(!$this->db->_error_number()){
                    
                    return array(
                        'Name'=> $row->UserName, 
                        'RealName'=> $row->UserRealName, 
                        'UserID' => $row->UserId,
                        'PartnerID' => $row->PartnerID,
                        'SupplyChainID' => $row->SupplychainID,
                        'OrgName'=> $row->UnitName,
                        'OrgID'=> $row->UnitID,
                        'OrgType'=> $row->OrgType,
                        'Area' => $this->getUserDistrict($username,true),
                        'Token' => $token
                    );
                }
            }
        }
        
        return false;
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
            
            //ambil area
            $this->_getOrgArea($org,$output);
            
            //ambil tujuan pengiriman
            $this->_getOrgDestination($org,$output);

            //ambil token
            $token = $this->_getToken($id,$output);
            
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
    
    public function doLogout($token = FALSE) {
        return true;
        /*$salt = 'bismillah';
        $username = (string) $this->encrypt->decode($token,$salt);
        
        $update = array(
            'UserMobileToken' => NULL
        );

        $this->db->where('UserName',$username);
        $this->db->where('UserMobileToken',$token);
        $this->db->update('sys_user',$update);
        
        if(!$this->db->_error_number()){
            return true;
        }
        
        return false;*/
    }
    
    public function _getToken($data,&$output) {
        
        $salt = 'bismillah';
        //$this->load->library('encrypt');
        //$token = $this->encrypt->encode($data,$salt);
        $token = hash_pbkdf2("sha512",$salt.$data.date('Y-m-d H:i:s'),'NIKOSB_VC',10,32);
        $output['token'] = $token;
        return $token;
    }
    
    public function checkToken($token,$user) {
        
        $this->db->select('StatusCode');
        $this->db->where('UserId',$user);
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
    
    public function getUserDistrict($user,$return = FALSE) {
        $output = array();
        
        $this->db->select('ktv_access_staff.DistrictID,District');
        $this->db->from('ktv_access_staff');
        $this->db->where('`UserId` = (SELECT `UserId` FROM `sys_user` WHERE `UserName` = "'.$user.'")',NULL,FALSE);
        $this->db->join('ktv_district','ktv_district.DistrictID = ktv_access_staff.DistrictID','LEFT');
        //$this->db->where('`UserId` = (SELECT `UserId` FROM `sys_user` WHERE `UserName` = "admin")',NULL,FALSE);
        $Q = $this->db->get();
                //var_dump($this->db->last_query());die;
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
    
    public function getAuthSession($id = false, $user = false, $pwd = false) {
        
        $this->db->select('StatusCode');
        
        if($id){
            $this->db->where('UserID',$id);
        } else {
            $this->db->where('UserName',$user);
            $this->db->where('UserPassword',md5($pwd));
        }

        $Q = $this->db->get('sys_user');
        if($Q->num_rows() > 0){
            $row = $Q->row();
            if($row->StatusCode === 'active'){
                return true;
            }
        }
        
        return false;
    }

    public function getSessionPartnerID($username) {
        $sql = "SELECT
                    c.`ObjID` AS PartnerID
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                    INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                WHERE
                    a.`UserName` = ?
                    AND c.`ObjType` IN ('private','program')
                LIMIT 1";
        $p = array(
            $username
        );
        $Data = $this->db->query($sql,$p)->row_array();
        return $Data['PartnerID'];
    }
    
    /**
     * Perbaikan auth untuk mobile
     */
    
    private function _getUserDetail($username,$password,&$output) {
        
        $this->db->select('UserId,UserName,UserRealName');
        $this->db->from('sys_user');
        $this->db->where('UserName',$username);
        $this->db->where('UserPassword',md5($password));
        $this->db->where('UserActive','Yes');
        $Q = $this->db->get(); //echo $this->db->last_query();
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            
            $output['userid'] = $row->UserId;
            $output['name'] = $row->UserName;
            $output['realname'] = $row->UserRealName;
            
            return $row->UserId;
        }
        
        return false;
    }
    
    private function _getOrgDetail($userid, &$output) {
        
        //ambil orgtype
        $sql = "SELECT
            `org`.`SupplychainID`,
            `org`.`OrgType`,
            `OrgID`,
            `MemberName`,
            partner.`PartnerID`
        FROM
            ktv_persons prs
        INNER JOIN `ktv_staffs` st ON `st`.`PersonID` = `prs`.`PersonID`
        INNER JOIN `ktv_supplychain_org` org ON `org`.`OrgID` = `st`.`ObjID` AND `org`.`OrgType` = 'agent'
        INNER JOIN `ktv_supplychain_org_partner` partner ON `partner`.`SupplychainID` = `org`.`SupplychainID`
        INNER JOIN `ktv_members` members ON `members`.`MemberID` = `org`.`OrgID`
        WHERE
            `prs`.`UserId` = '".$userid."'";

        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0){
            $row = $Q->row();
            
            $output['partnerid'] = $row->PartnerID;
            $output['supplychainid'] = $row->SupplychainID;
            $output['orgname'] = $row->MemberName;
            $output['orgid'] = $row->OrgID;
            $output['orgtype'] = $row->OrgType;
            $output['orgcode'] = '';

            return $row->SupplychainID;
        }
        
        return false;
    }
    
    private function _getOrgArea($orgid, &$output) {
        
        $this->db->select('ktv_supplychain_area.DistrictID districtid,District district');
        $this->db->from('ktv_supplychain_area');
        $this->db->join('ktv_district','ktv_district.DistrictID = ktv_supplychain_area.DistrictID','LEFT');
        $this->db->where('SupplychainID',$orgid);
        $Q = $this->db->get(); //echo $this->db->last_query();
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['area'] = $result;
            
            return $result;
        }
        
        return array();
    }

    private function _getOrgDestination($orgid, &$output = array()) {
        
        $Q = $this->db->query("SELECT DISTINCT
            (
                SELECT
                    SupplychainID
                FROM
                    ktv_supplychain_org
                WHERE
                    OrgID = ifnull(MillID, ifnull(b.MemberID, ''))
            ) AS id,
            ifnull(
                MillName,
                ifnull(agCompanyName, '')
            ) AS name
        FROM
            ktv_supplychain_org_rel
        LEFT JOIN ktv_supplychain_org a ON a.SupplychainID = ktv_supplychain_org_rel.ParentOrgId
        LEFT JOIN ktv_members b ON b.MemberID = a.OrgID
        LEFT JOIN ktv_members_extension e ON e.MemberID = b.MemberID
        AND a.OrgType = 'agent'
        LEFT JOIN ktv_mill c ON c.MillID = a.OrgID
        AND a.OrgType = 'mill' WHERE ChildOrgId = '" . $orgid . "'");

        //echo $this->db->last_query();
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            $output['destination'] = $result;
            
            return $result;
        }
        
        return array();
    }

    public function getDestinationBySupplychainID($id) {
        return $this->_getOrgDestination($id);
    }
}

?>
