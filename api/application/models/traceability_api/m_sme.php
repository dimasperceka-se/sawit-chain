<?php

class M_sme extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function add_sme_post($data){
       
        $this->db->trans_begin();

        $return = array();

        //generate MemberID dan MemberDisplayID
        $this->load->model('grower/mgrower');
        $id = $this->mgrower->genMemberID($data['MemberUID'],'M');
        
        $MemberID =  $id['MemberID'];
        
        if($MemberID != ''){
            $this->db->select('*');
            $this->db->from('view_tc_supplychain_org kvso');
            $this->db->where('kvso.ObjType', 'agent');
            $this->db->where('kvso.ObjID', $MemberID);
            
            $Q = $this->db->get();
            
            if($Q->num_rows() == 0) {
                
                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkMember = $this->db->query("SELECT MemberID FROM ktv_members WHERE MemberID = '$MemberID'");
                } else {
                    $ID = $data['MemberID']; 
                    $checkMember = $this->db->query("SELECT MemberID FROM ktv_members WHERE MemberID = '$ID'");
                }
               
                if($checkMember->num_rows() > 0) {
                   
                    $dataUpdate = array(
                        "MemberName"     => $data['MemberName'],
                        "DateOfBirth"    => $data['DateOfBirth'],
                        "Gender"         => $data['Gender'],
                        "VillageID"      => $data['VillageID'],
                        "Address"        => $data['Address'],
                        "Handphone"      => $data['Handphone'],
                        "StatusCode"     => $data['StatusCode'],
                        "Nin"            => $data['Nin'],
                        "Email"          => $data['Email'],
                        "Latitude"       => $data['Latitude'],
                        "Longitude"      => $data['Longitude'],
                        "Education"      => $data['Education'],
                        "DateUpdated"    => $data['DateUpdated'],
                        "CreatedBy"      => $data['CreatedBy']
                    );

                    $this->db->where('MemberID', $data['MemberID']);
                    $query = $this->db->update('ktv_members', $dataUpdate);
                    $MemberID = $MemberID['MemberID'];
                    
                    $return['success'] = true;
                    $return['message'] = lang("Data Updated");
                } else {
                    $dataMember = array(
                        $id['MemberID'],
                        $id['MemberUid'],
                        $id['MemberDisplayID'],
                        $data['MemberName'],
                        $data['DateOfBirth'],
                        $data['Gender'],
                        $data['VillageID'],
                        $data['Address'],
                        $data['Handphone'],
                        'active',
                        $data['Nin'],
                        $data['Email'],
                        $data['Latitude'],
                        $data['Longitude'],
                        $data['Education'],
                        $data['DateCreated'],
                        $data['CreatedBy']
                    );
                    
                    //ktv_members
                    $sql="INSERT INTO `ktv_members` SET
                        `MemberID` = ?,
                        `MemberUID` = ?,
                        `MemberDisplayID` = ?,
                        `MemberName` = ?,
                        `DateOfBirth` = ?,
                        `Gender` = ?,
                        `VillageID` = ?,
                        `Address` = ?,
                        `Handphone` = ?,
                        `StatusCode` = ?,
                        `Nin` = ?,
                        `Email` = ?,
                        `Latitude` = ?,
                        `Longitude` = ?,
                        `Education` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                    
                    $query = $this->db->query($sql,$dataMember);
                }

                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkMemberExtention = $this->db->query("SELECT MemberID FROM ktv_members_extension WHERE MemberID = '$MemberID'");
                } else {
                    $IdMemberExt = $data['MemberID']; 
                    $checkMemberExtention = $this->db->query("SELECT MemberID FROM ktv_members_extension WHERE MemberID = '$IdMemberExt'");
                }

                if($checkMemberExtention->num_rows() > 0) {

                    $dataUpdate = array(
                        "agLegalStatusCompany"     => $data['agLegalStatusCompany'],
                        "agCompanyName"            => $data['agCompanyName'],
                        "agYearEstablished"        => $data['agYearEstablished'],
                        "agEmail"                  => !empty(@$data['CompanyEmail']) ? @$data['CompanyEmail']: null,
                        "DateUpdated"              => $data['DateUpdated'],
                        "LastModifiedBy"           => $data['LastModifiedBy'],
                    );
                    
                     $this->db->where('MemberID', $data['MemberID']);
                     $query = $this->db->update('ktv_members_extension', $dataUpdate);
                     $MemberID = $MemberID['MemberID'];
                     
                     $return['success'] = true;
                     $return['message'] = lang("Data Updated");
                   
                } else {
                   
                    $DataCompany = array(
                        $id['MemberID'],
                        $data['agLegalStatusCompany'],
                        $data['agCompanyName'],
                        $data['agYearEstablished'],
                        !empty(@$data['CompanyEmail']) ? @$data['CompanyEmail']: null,
                        $data['DateCreated'],
                        $data['CreatedBy']
                    );

                    //Member extension
                    $sql="INSERT INTO `ktv_members_extension` SET
                        `MemberID` = ?,
                        agLegalStatusCompany = ?,
                        agCompanyName = ?,
                        agYearEstablished = ?,
                        agEmail = ?,
                        DateCreated = ?,
                        CreatedBy = ?";
            
                    $query = $this->db->query($sql,$DataCompany);
                }

                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkMemberSupplyChain = $this->db->query("SELECT FarmerID FROM ktv_tc_supplychain_farmer WHERE FarmerID = '$MemberID'");
                } else {
                    $ID = $data['MemberID']; 
                    $checkMemberSupplyChain = $this->db->query("SELECT FarmerID FROM ktv_tc_supplychain_farmer WHERE FarmerID = '$ID'");
                }
                
                if($checkMemberSupplyChain->num_rows() > 0) {
                   
                    $dataUpdate = array(
                        "FarmerID"       => $data['MemberID'],
                        "SupplychainID"  => $data['userid'],
                        "StatusCode"     => $data['StatusCode'],
                        "DateUpdated"    => $data['DateUpdated'],
                    );

                    $this->db->where('FarmerID', $data['MemberID']);
                    $query = $this->db->update('ktv_tc_supplychain_farmer', $dataUpdate);
                    $MemberID = $MemberID['FarmerID'];
                    
                    $return['success'] = true;
                    $return['message'] = lang("Data Updated");
                } else {

                    $dataMemberSupplyChain = array(
                        $id['MemberID'],
                        $data['userid'],
                        $data['DateCreated'],
                        $data['CreatedBy']
                    );
                    
                    //ktv_tc_supplychain_farmer
                    $sql="INSERT INTO `ktv_tc_supplychain_farmer` SET
                        `FarmerID` = ?,
                        `SupplychainID` = ?,
                        `DateStart` = '',
                        `DateEnd` = '',
                        `StatusCode` = 'active',
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";

                        $query = $this->db->query($sql,$dataMemberSupplyChain);
                }

                //ktv_access_partner_member
                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkMemberPartner = $this->db->query("SELECT apmMemberID FROM ktv_access_partner_member WHERE apmMemberID = '$MemberID'");
                } else {
                    $ID = $data['MemberID']; 
                    $checkMemberPartner = $this->db->query("SELECT apmMemberID FROM ktv_access_partner_member WHERE apmMemberID = '$ID'");
                }
               
                if($checkMemberPartner->num_rows() > 0) {
                   
                    $dataUpdate = array(
                        "apmPartnerID"       => '1',
                        "CreatedBy"          => $data['CreatedBy'],
                        "uid"                => $data['MemberUID'],
                        "DateCreated"        => $data['DateCreated'],
                        "DateUpdated"        => $data['DateUpdated'],
                        "LastModifiedBy"     => $data['LastModifiedBy']
                    );

                    $this->db->where('apmMemberID', $data['MemberID']);
                    $query = $this->db->update('ktv_access_partner_member', $dataUpdate);
                    $MemberID = $MemberID['apmMemberID'];
                    
                    $return['success'] = true;
                    $return['message'] = lang("Data Updated");
                } else {

                    $dataMemberPartner = array(
                        $id['MemberID'],
                        $data['CreatedBy'],
                        $data['MemberUID'],
                        $data['DateCreated'],
                        $data['DateUpdated'],
                        $data['DateCreated'],
                        $data['LastModifiedBy']
                    );
                    
                    //ktv_access_partner_member
                    $sql="INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = '1',
                        `apmMemberID` = ?,
                        `CreatedBy` = ?,
                        `DateCreated` = ?,
                        `DateUpdated` = ?,
                        `DateSync` = ?,
                        `LastModifiedBy` = ?";
                    
                    $query = $this->db->query($sql,$dataMemberPartner);
                }

                //ktv_member_sme_type
                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkMemberSmeType = $this->db->query("SELECT MemberID FROM ktv_member_sme_type WHERE MemberID = '$MemberID'");
                } else {
                    $ID = $data['MemberID']; 
                    $checkMemberSmeType = $this->db->query("SELECT MemberID FROM ktv_member_sme_type WHERE MemberID = '$ID'");
                }
               
                if($checkMemberSmeType->num_rows() > 0) {
                   
                    $dataUpdate = array(
                        "SMETypeID"          => $data['MRoleID'],
                        "DateCreated"        => $data['DateCreated'],
                        "CreatedBy"          => $data['CreatedBy'],
                        "DateUpdated"        => $data['DateUpdated'],
                        "LastModifiedBy"     => $data['LastModifiedBy']
                    );

                    $this->db->where('MemberID', $data['MemberID']);
                    $query = $this->db->update('ktv_member_sme_type', $dataUpdate);
                    $MemberID = $MemberID['MemberID'];
                    
                    $return['success'] = true;
                    $return['message'] = lang("Data Updated");
                } else {

                    $dataMemberSmeType = array(
                        $id['MemberID'],
                        $data['MRoleID'],
                        $data['CreatedBy'],
                        $data['DateUpdated'],
                        $data['LastModifiedBy']
                    );
                    
                    //ktv_member_sme_type
                    $sql="INSERT INTO `ktv_member_sme_type` SET
                        `MemberID` = ?,
                        `SMETypeID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?,
                        `DateUpdated` = ?,
                        `LastModifiedBy` = ?";
                    
                    $query = $this->db->query($sql,$dataMemberSmeType);
                }

                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkMemberRole = $this->db->query("SELECT MemberID FROM ktv_member_role WHERE MemberID = '$MemberID'");
                } else{
                    $IdMemberRole = $data['MemberID']; 
                    $checkMemberRole = $this->db->query("SELECT MemberID FROM ktv_member_role WHERE MemberID = '$IdMemberRole'");
                }

                if($checkMemberRole->num_rows() > 0) {
                   
                    $dataUpdate = array(
                        "MRoleID"     => $data['MRoleID'],
                        "DateCreated"  => $data['DateCreated'],
                        "CreatedBy"    => $data['CreatedBy'],
                        "DateUpdated"  => $data['DateUpdated'],
                        "LastModifiedBy" => $data['LastModifiedBy']
                    );
                    
                    $this->db->where('MemberID', $IdMemberRole);
                    $query = $this->db->update('ktv_member_role', $dataUpdate);
                    $MemberID = $MemberID['MemberID'];
                    
                    $return['success'] = true;
                    $return['message'] = lang("Data Updated");
                    
                } else {

                    $dataMemberRole = array(
                        $id['MemberID'],
                        $data['MRoleID'],
                        $data['DateCreated'],
                        $data['CreatedBy'],
                        $data['DateUpdated'],
                        $data['LastModifiedBy'],
                        $data['MemberUID']
                    );
                    
                    //Member role
                    $sql="INSERT INTO `ktv_member_role` SET
                        `MemberID` = ?,
                        `MRoleID` = ?,
                        `DateCreated` = ?,
                        `CreatedBy` = ?,
                        `DateUpdated` = ?,
                        `LastModifiedBy` = ?,
                        `uid` = ?";
        
                    $query = $this->db->query($sql,$dataMemberRole);
                }

                if($data['MemberID'] == '0' ||$data['MemberID'] == ''){
                    $checkSpCode = $this->db->query("SELECT MemberID FROM ktv_sme_sp_code WHERE MemberID = '$MemberID'");
                } else {
                    $IdMemberSp = $data['MemberID']; 
                    $checkSpCode = $this->db->query("SELECT MemberID FROM ktv_sme_sp_code WHERE MemberID = '$IdMemberSp'");
                }

                if($checkSpCode->num_rows() > 0) {

                    $dataUpdate = array(
                        "SPCodeID"     => $data['SPCodeID']
                    );

                     $this->db->where('MemberID', $data['MemberID']);
                     $query = $this->db->update('ktv_sme_sp_code', $dataUpdate);
                     $MemberID = $MemberID['MemberID'];
                     
                     $return['success'] = true;
                     $return['message'] = lang("Data Updated");
            
                } else {

                    $dataSpCode = array(
                        $data['SPCodeID'],
                        $id['MemberID'],
                        $data['CreatedBy']
                    );
                    
                    //ktv_sme_sp_code
                    $sql="INSERT INTO `ktv_sme_sp_code` SET
                        `SPCodeID` = ?,
                        `MemberID` = ?,
                        CreatedBy = ?";
        
                    $query = $this->db->query($sql,$dataSpCode);
                }

                $dataSupplychainOrg = array(
                    $id['MemberID'],
                    $data['StatusCode'],
                    $data['DateCreated'],
                    $data['DateUpdated'],
                    $data['LastModifiedBy'],
                    $data['CreatedBy']
                );
                
                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $checkOrg = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE SupplychainID = '$MemberID'");
                } else {
                    $SID = $data['userid']; 
                    $checkOrg = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE SupplychainID = '$SID'");
                }
                
                if($checkOrg->num_rows() > 0) {

                    $dataUpdate = array(
                        "DateUpdated"     => !empty($data['DateUpdated']) ? $data['DateUpdated'] : null,
                    );

                     $this->db->where('SupplychainID', $data['userid']);
                     $query = $this->db->update('ktv_tc_supplychain_org', $dataUpdate);
                     $MemberID = $MemberID['MemberID'];
                     
                     $return['success'] = true;
                     $return['message'] = lang("Data Updated");

                } else {
                    //ktv_tc_supplychain_org
                    $sql="INSERT INTO `ktv_tc_supplychain_org` SET
                    `ObjID`         = ?,
                    `StatusCode`    = ?,
                    `ObjType`       = 'Agent',
                    `PartnerID`     = '1',
                    `IsFarmer`      = '1',
                    `IsNonFarmer`   = '0',
                    `IsBatch`       = '1',
                    `IsSent`        = '1',
                    `IsCompany`     = '0',
                    `AccessBy`      = 'farmer',
                    `CurrID`        = '1',
                    `ProductionCapacity` = '0',
                    `WorkHour`      = '0',
                    `DateCreated`   = ?,
                    `DateUpdated`   = ?,
                    `LastModifiedBy`  = ?,
                    `CreatedBy`       = ?";
    
                    $query = $this->db->query($sql,$dataSupplychainOrg);

                    $SupplychainID = $this->db->insert_id();

                    $this->db->where('SupplychainID', $SupplychainID);
                    $this->db->select('SupplychainID',false);
                    $this->db->from('ktv_tc_supplychain_org'); 
                    $queryOrg = $this->db->get();
                    
                    $MemberID = $MemberID['ObjID'];

                    $cekOrg = $queryOrg->result_array();
                
                    if(!empty($cekOrg)){

                        foreach ($cekOrg as $key => $value) {
                            $SupplychainID  = $value['SupplychainID'];
                            $StartDate      = date('Y-m-d');
                            $EndDate        = date('Y-m-d');
                            $CreatedBy      = $data['CreatedBy'];
                        }
                    
                        //ktv_tc_supplychain_org_rel
                        $sql="INSERT INTO `ktv_tc_supplychain_org_rel` SET
                            `ParentID`      = '$SupplychainID',
                            `ChildID`       = '$SupplychainID',
                            `StartDate`     = '$StartDate',
                            `EndDate`       = '$EndDate',
                            `StatusCode`    = 'active',
                            `CreatedBy`     = '$CreatedBy'";
            
                        $query = $this->db->query($sql);
                    }
                }
                
                if($data['MemberID'] == '0' || $data['MemberID'] == ''){
                    $MemberID        = $id['MemberID'];
                    $SID             = $SupplychainID;
                    $MemberDisplayID = $id['MemberDisplayID'];
                } else {
                    $MemberID        = $data['MemberID'];
                    $SID             = $data['userid'];
                    $MemberDisplayID = $data['MemberDisplayID'];
                }    

                $this->db->trans_commit();

                if($this->db->trans_status() === true) {
                    $results['success']          = true;
                    $results['message']          = "Data saved";
                    $results['MemberID']         = $MemberID;
                    $results['MemberDisplayID']  = $MemberDisplayID;
                    $results['SupplyChainID']    = $SID;
                } else {
                    $results['success']         = false;
                    $results['message']         = "Save Data Failed";
                }
    
                return $results;
            } else {
                $results['success']         = false;
                $results['message']         = "Save Data Failed, MemberID already exist";
            }
        } 
    }

    public function GetSme($userid){
        
        $this->db->select('ktv_tc_supplychain_org.CreatedBy,
                view_tc_supplychain_org.SupplychainID,
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
        $this->db->where('sys_user.UserID', $userid);
        $this->db->where('ktv_tc_supplychain_staff_rel.StatusCode = "active"', null, false);
        $this->db->where('sys_user.StatusCode = "active"', null, false);

        $Q = $this->db->get();
        
        if ($Q->num_rows()) {
            $row = $Q->row();

            if($row->CreatedBy == 0){
                $data['CreatedBy'] = is_null($row->UserID) ? "" : $row->UserID;
                $data['SupplychainID'] = is_null($row->SupplychainID) ? "" : $row->SupplychainID;
     
                $dataUpdate = array(
                    "CreatedBy"     => $row->UserID,
                );
             
                 $this->db->where('SupplychainID',$data['SupplychainID']);
                 $query = $this->db->update('ktv_tc_supplychain_org', $dataUpdate);
                 $SupplychainID = $SupplychainID['SupplychainID'];
                 
                 $return['success'] = true;
            }
        }
    
        $sql="SELECT
                a.MemberID AS SmeID
                , ktso.SupplychainID
                , a.MemberUid
                , a.MemberDisplayID
                , a.MemberName
                , a.DateOfBirth
                , a.Gender
                , a.Address
                , a.HandPhone AS Handphone
                , a.Nin
                , a.Email
                , a.Latitude
                , a.Longitude
                , a.Education AS EducationID
                , CASE
                    WHEN a.Education = '1' THEN '".lang('No Education')."'
                    WHEN a.Education = '2' THEN '".lang('Primary School Incompleted')."'
                    WHEN a.Education = '3' THEN '".lang('Primary School Completed')."'
                    WHEN a.Education = '4' THEN '".lang('Graduated Middle School')."'
                    WHEN a.Education = '5' THEN '".lang('Graduated High School')."'
                    WHEN a.Education = '6' THEN '".lang('Graduated College')."'
                    WHEN a.Education = '7' THEN '".lang('Magister/S2')."'
                    WHEN a.Education = '8' THEN '".lang('Doctor/S3')."'
                END AS Education
                , kv.VillageID
                , kv.Village
                , ksd.SubDistrictID
                , ksd.SubDistrict
                , kp.ProvinceID
                , kp.Province
                , kd.DistrictID
                , kd.District
                , x.agLegalStatusCompany AS agLegalStatusCompanyID
                , x.agEmail AS CompanyEmail
                , CASE
                    WHEN x.agLegalStatusCompany = '1' THEN '".lang('Sole Proprietorship')."'
                    WHEN x.agLegalStatusCompany = '2' THEN '".lang('Partnership')."'
                    WHEN x.agLegalStatusCompany = '3' THEN '".lang('Limited Partnership')."'
                    WHEN x.agLegalStatusCompany = '4' THEN '".lang('Limited Liability Company')."'
                    WHEN x.agLegalStatusCompany = '5' THEN '".lang('Corporation')."'
                    WHEN x.agLegalStatusCompany = '6' THEN '".lang('Cooperative')."'
                    WHEN x.agLegalStatusCompany = '7' THEN '".lang('Foundation')."'
                    WHEN x.agLegalStatusCompany = '8' THEN '".lang('Association')."'
                    WHEN x.agLegalStatusCompany = '9' THEN '".lang('State Owned')."'
                END AS agLegalStatusCompany
                , x.agCompanyName  
                , x.agYearEstablished
                , mrole.MRoleID
                , CASE
                    WHEN mrole.MRoleID = '5' THEN '".lang('Trader')."'
                    WHEN mrole.MRoleID = '8' THEN '".lang('Ramp')."'
                    WHEN mrole.MRoleID = '9' THEN '".lang('Delivery Order Holder')."'
                    WHEN mrole.MRoleID = '10' THEN '".lang('Other')."'
                    WHEN mrole.MRoleID = '11' THEN '".lang('Kebun Inti')."'
                    WHEN mrole.MRoleID = '12' THEN '".lang('External Estates')."'
                    WHEN mrole.MRoleID = '13' THEN '".lang('Agent')."'
                    WHEN mrole.MRoleID = '14' THEN '".lang('Plasma')."'
                    WHEN mrole.MRoleID = '15' THEN '".lang('Retailer')."'
                END AS RoleName
                , ssc.SPCodeID
                , x.CreatedBy
                , x.LastModifiedBy
                , x.DateCreated
                , x.DateUpdated
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_member_sme_type mtype ON a.MemberID = mtype.MemberID
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                LEFT JOIN ktv_members_extension x ON a.MemberID = x.MemberID
                LEFT JOIN ktv_sme_sp_code ssc ON ssc.MemberID = a.MemberID
                LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.ObjID = a.MemberID
            WHERE
                ktso.`CreatedBy` = ?
            GROUP BY a.MemberID
            ";

        $query = $this->db->query($sql,$userid);
        
        foreach ($query->result_array() as $row) {
            
            $plantation    = $this->plantation($row['SmeID']);
            
            $data = array(
                "plantation" => $plantation
            );

            $result[] = array_merge($row,$data);
        }   
        
        return $result;
    }

    private function plantation($MemberID)
    {
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

        $query = $this->db->query($sql, array($MemberID));
        
        if ($query->num_rows()) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;
    }
}