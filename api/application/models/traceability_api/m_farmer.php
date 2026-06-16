<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf Sutana
 */
class m_farmer extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('grower/mgrower');
    }

    public function get_certification($PartnerID, $SupplychainID){
        $sql = "SELECT
            a.CertProgID
            , a.CertProgName
        FROM
            `ktv_ref_certification_program` a
        WHERE
            a.StatusCode = 'active'
            AND a.CertProgID IN (5,6,7,8,9,10)
        ";

        $query = $this->db->query($sql);

        
        $data['data'] = $query->result();
        $data['total'] = $query->num_rows();
        return $data;
    }

    public function get_data_farmer($PartnerID, $SupplychainID)
    {
        $return = array('data' => array(), 'total' => 0);

        $AccessBy = $this->db->query("SELECT AccessBy FROM ktv_tc_supplychain_org WHERE SupplychainID=?", array($SupplychainID))->row()->AccessBy;
        if($AccessBy=='farmer'){
            $f1 = ''; $f2 = '';
            $d1 = '/*'; $d2 = '*/';
        }else{
            $DistrictID = $this->db->query("SELECT GROUP_CONCAT(DISTINCT DistrictID) DistrictID FROM ktv_tc_supplychain_area WHERE SupplychainID=? AND StatusCode='active' AND SUBSTR(NOW(), 1, 10) BETWEEN DateStart AND DateEnd", array($SupplychainID))->row()->DistrictID;
            $d1 = $DistrictID=='' ? '/*' : '';
            $d2 = $DistrictID=='' ? '*/' : '';
            $f1 = '/*'; $f2 = '*/';
        }
        
        $sql = "SELECT
                `a`.`MemberID`,
                `a`.`MemberDisplayID`,
                `a`.`MemberName`,
                `a`.`Nin`,
                `a`.`DateOfBirth`,
                `a`.`Gender`,
                `a`.`Address`,
                `a`.`Handphone`,
                `a`.`Photo`,
                `a`.`GapoktanID`,
                `a`.`GapoktanName`,
                `a`.`FarmerGroupID`,
                `a`.`GroupName`,
                `i`.`ProvinceID`,
                `e`.`Province`,
                `i`.`DistrictID`,
                `i`.`District`,
                `d`.`SubDistrictID`,
                `d`.`SubDistrict`,
                `a`.`VillageID`,
                `c`.`Village`,
                `cp`.`CertProgName`,
                `a`.`AliasSupplyBase` AS Alias,
                `a`.`StatusCode` AS FarmerStatus,
                `a`.`PlotOwner`,
                `a`.`PlotManager`,
                `a`.`CollectingLocation`,
                `a`.`CollectingID`,
                `a`.`DateCreated`,
                `a`.`DateUpdated`
            FROM
            	ktv_tc_supplychain_farmer xc
            LEFT JOIN 
                ktv_members a ON a.MemberID = xc.FarmerID
            LEFT JOIN 
                ktv_ref_certification_program cp ON cp.CertProgID = a.isCertified
            LEFT JOIN 
                ktv_access_partner_member ab ON a.MemberID = ab.apmMemberID
            LEFT JOIN 
                ktv_member_role mrole ON a.MemberID = mrole.MemberID
            LEFT JOIN 
                ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
            LEFT JOIN 
                ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
            LEFT JOIN 
                ktv_village c ON a.VillageID = c.VillageID
            LEFT JOIN 
                ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
            LEFT JOIN 
                ktv_district i ON i.DistrictID = d.DistrictID
            LEFT JOIN 
                ktv_province e ON e.ProvinceID = i.ProvinceID
            LEFT JOIN 
                ktv_district f ON f.DistrictID  = d.DistrictID 
            LEFT JOIN 
                ktv_gapoktan g ON g.GapoktanID = a.GapoktanID
            LEFT JOIN 
                ktv_farmer_group h ON h.FarmerGroupID = a.FarmerGroupID
            LEFT JOIN 
                ktv_tc_supplychain_org z ON z.SupplychainID = xc.SupplychainID
            LEFT JOIN 
                ktv_mill `kmill` on `kmill`.`MillID` = `z`.`ObjID` and `z`.`ObjType` = 'mill'
            LEFT JOIN 
                ktv_tc_supplychain_org z2 ON a.MemberID = z2.ObjID and z.ObjType = 'agent'
            LEFT JOIN 
                ktv_refinery kref ON kref.RefineryID = z.ObjID and z.ObjType = 'refinery'
            LEFT JOIN 
                ktv_kcp_bulking kcp ON kcp.KCPID = z.ObjID
            WHERE
                a.StatusCode IN ('active','inactive') 
            AND 
                z.SupplychainID = '$SupplychainID'
            AND 
            	mrole.MRoleID = '1'
            GROUP BY 
                a.MemberID";
        $Q = $this->db->query($sql, array($SupplychainID));
        
        if ($Q->num_rows() > 0) {
            $result = $Q->result();
            foreach ($result as $key => $val) {
                $val->Photo = is_null($val->Photo) ? "" : 'api/images/member/' . $val->ProvinceID . '/' . $val->Photo;
                $val = $this->check_isNull($val);
                $val->plantation = array();

                $MemberID =  $val->MemberID;

                $sql2 = "SELECT
                        a.PlotNr AS PlantationNr,
                        a.SurveyNr,
                        a.VillageID,
                        IFNULL(a.Latitude,ST_Latitude(a.LatLong)) AS Latitude,
                        IFNULL(a.Longitude,ST_Latitude(a.LatLong)) AS Longitude,
                        a.GardenAreaHa,
                        a.SoilType,
                        a.FirstPlantingYear AS PlantingYear,
                        a.Address,
                        a.AnnualProduction AS Production,
                        a.OwnershipDoc AS LandLegality,
                        a.RecipientDealer,
                        a.TreeTBM AS Tbm,
                        a.TreeTM AS Tm,
                        a.TreeTR AS Tr,
                        a.DateCreated,
                        a.DateUpdated,
                        c.Village
                    FROM
                        ktv_survey_plot a
                    LEFT JOIN 
                        ktv_village c ON a.VillageID=c.VillageID
                    WHERE
                        a.StatusCode = 'active'
                    AND
                        a.MemberID = '$MemberID'
                    ";
                $getPlan = $this->db->query($sql2);
                
                if ($getPlan->num_rows()) {
                    $d_plan = $getPlan->result();
                    foreach ($d_plan as $k => $v) {
                        $v = $this->check_isNull($v);

                    }
                    $val->plantation = $d_plan;
                }
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        return $data;
    }

    private function check_isNull($v)
    {
        foreach ($v as $key => $value) {
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }

    public function submit($data)
    {
        $result = false;
        $insid = 0;
        $display_id = '';
        $error = '';
        $params = array();
        
        try {
            $this->db->trans_begin();

            $groupname = @$data['groupName'];

            if($groupname == ''){
                $groupname = @$data['GroupName'];
            }

            $FarmerGroupID = @$data['FarmerGroupID'];

            if($FarmerGroupID == ''){
                $FarmerGroupID = @$data['FarmerGroupID'];
            }
            
            $content = array(
                "MemberID" => $data["MemberID"],
                "MemberName" => $data["MemberName"],
                "Nin" => $data["Nin"],
                "DateOfBirth" => $data["DateOfBirth"],
                "Gender" => $data["Gender"],
                "Address" => $data["Address"],
                "Handphone" => $data["Handphone"],
                "GapoktanID" => (!isset($data["GapoktanID"]))?'':$data["GapoktanID"],
                "GapoktanName" => $data["GapoktanName"],
                "FarmerGroupID " => $FarmerGroupID,
                "groupName" => $groupname,
                "VillageID" => $data["VillageID"],
                "PlotOwner" => $data["PlotOwner"],
                "PlotManager" => $data["PlotManager"],
                "StatusCode" => 'active',
                "isCertified" => (!isset($data["CertProgID"]))?'':$data["CertProgID"],
                "CollectingLocation" => $data["CollectingLocation"], // 1=plot, 2=collectingpoint
                "CollectingID" => $data["CollectingID"],
                "AliasSupplyBase" => (!isset($data["Alias"]))?'':$data["Alias"],
                "PartnerID" => "1"
            );
            
            $farmerData = false;
            if ($data['MemberID'] != 0) {
                $farmerData = $this->checkExistingFarmer($data['MemberID']);
            }
            
            if ($data['VillageID'] == '' || $data['VillageID'] == 0) {
                return array(
                    'success' => false,
                    'message' => 'Save data failed',
                    'error' => 'Village is null!',
                );
            }

            if ($farmerData) {
                if (strtotime($data['DateUpdated']) < strtotime($farmerData['DateUpdated'])) {
                    return array(
                        'success' => false,
                        'message' => 'Save data failed',
                        'error' => 'DateUpdated < DateUpdated WEB',
                    );
                }
                
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                
                $this->db->where("MemberID",$content["MemberID"]);
                $this->db->update('ktv_members', $content);
                $insid = $data['MemberID'];
            } else {
                $member = $this->mgrower->genMemberID($data['VillageID'], 'F');
                
                //getuids
                $uid = $this->getUID();
            
                $date = $data['DateCreated'];

                $date = date("Y-m-d H:i:s"); 
            
                $content['MemberID'] = $member['MemberID'];
                $content['MemberDisplayID'] = $member['MemberDisplayID'];
                $content['uid'] = $uid;
                $content['DateCollection'] = $date;
                $content['DateCreated'] = $date;
                $content['DateUpdated'] = $date;
                $content['CreatedBy'] = $data['CreatedBy'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
               
                $this->db->insert('ktv_members', $content);

                $insid = $member['MemberID'];
                $display_id = $member['MemberDisplayID'];

                $params['MemberID'] = $insid;
                $params['MRoleID'] = 1;
                $params['DateCreated'] = $data['DateCreated'];
                $params['DateUpdated'] = $data['DateUpdated'];
                $params['CreatedBy'] = $data['CreatedBy'];
                $params['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->insert('ktv_member_role', $params);

                $EndDate    = date("Y-m-d", strtotime(date("Y-m-d", strtotime($data['DateCreated'])) . " + 365 day"));
                $paramssf['FarmerID']       = $insid;
                $paramssf['SupplychainID']  = $this->getSupplyID($data['CreatedBy']);
                $paramssf['DateCreated']    = $data['DateCreated'];
                $paramssf['DateUpdated']    = $data['DateUpdated'];
                $paramssf['CreatedBy']      = $data['CreatedBy'];
                $paramssf['DateStart']      = $data['DateCreated'];
                $paramssf['DateEnd']        = $EndDate;
                $this->db->insert('ktv_tc_supplychain_farmer', $paramssf);

                $this->insertMemberPartner($member, $data['VillageID'], $data['CreatedBy']);
            }

            $result = $this->db->trans_complete();
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        if ($result) {
            return array('success' => $result, 'FarmerID' => $insid, 'FarmerDisplayID' => $display_id);
        } else {
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }

    }

    private function getUID($length = 11) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // private function getUID(){        
    //     $Q = $this->db->query('SELECT random_string(11,null) AS uid');
    //     if($Q->num_rows() > 0) {
    //         $row = $Q->row();
    //         return $row->uid;
    //     }else{
    //         return '';
    //     }
    // }

    function getSupplyID($UserID){
        $sql = "
            SELECT
                SupplychainID
            FROM
                `view_tc_supplychain_staff`
            WHERE UserID = ?
        ";

        $query = $this->db->query($sql,array($UserID));

        if($query->num_rows()>0){
            $row = $query->row();

            return $row->SupplychainID;
        }else{
            return false;
        }
    }

    private function insertMemberPartner($member, $VillageID, $UserID=null)
    {
        $result = false;
        if ($VillageID) {

            $partner_id = array();
            $partner_id = $this->getPartnerIDbyUser($UserID);

            if($partner_id){
                foreach ($partner_id as $key => $val) {
                    $result = $this->insertPartnerMember($member['MemberID'], $val['PartnerID']);
                }
            }

            if ($result) {
                return true;
            }
        }
        return $result;
    }

    private function getPartnerIDbyUser($UserID)
    {
        $sql = "SELECT
            apmPartnerID PartnerID
            , pp.PartnerName
            , s.UserId
            , m.MemberID
        FROM
            sys_user s
        LEFT JOIN
            ktv_persons p on p.UserID = s.UserID
        LEFT JOIN
            ktv_staffs st on st.PersonID = p.PersonID
        LEFT JOIN
            ktv_members m on m.MemberID = st.ObjID
        LEFT JOIN
            ktv_access_partner_member apm on apm.apmMemberID = m.MemberID
        INNER JOIN
            ktv_program_partner pp on pp.PartnerID = apm.apmPartnerID AND pp.StatusCode = 'active'
        WHERE
            s.UserId = ?";
        $query = $this->db->query($sql,array($UserID));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
    }

    private function getPartnerID($village_id)
    {
        $this->db->select('a.DistrictID,a.PartnerID');
        $this->db->from('ktv_district_partner a');
        $this->db->join('ktv_district b', 'a.DistrictID = b.DistrictID', 'left');
        $this->db->join('ktv_subdistrict c', 'c.DistrictID = b.DistrictID', 'left');
        $this->db->join('ktv_village d', 'd.SubDistrictID = c.SubDistrictID', 'left');
        $this->db->where("d.VillageID = $village_id", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
    }

    private function InsertPartnerMember($member_id, $partner_id)
    {
        $this->db->set('apmPartnerID', $partner_id);
        $this->db->set('apmMemberID', $member_id);
        $this->db->set('CreatedBy', 1);
        $this->db->set('DateCreated', date("Y-m-d H:i:s"));
        $result = $this->db->replace('ktv_access_partner_member');

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    private function checkExistingNIN($Nin){
        $this->db->select('*', false);
        $this->db->from('ktv_members a');
        $this->db->where("Nin = '$Nin'", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;
    }

    private function checkExistingFarmer($farmer_id)
    {
        $this->db->select('*', false);
        $this->db->from('ktv_members a');
        $this->db->where("MemberID = '$farmer_id'", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;

    }

    private function checkExistingPlot($member_id, $survey_nr, $plot_nr)
    {
        $this->db->select('*', false);
        $this->db->from('ktv_survey_plot a');
        $this->db->where("MemberID = '$member_id'", null, false);
        $this->db->where("SurveyNr = '$survey_nr'", null, false);
        $this->db->where("PlotNr = '$plot_nr'", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;

    }

    public function submit_plot($data)
    {
        $result = false;
        $insid = 0;
        $display_id = '';
        $error = '';
       
        try {
            $this->db->trans_begin();

            //getuids
            $uid = $this->getUID();
            
            $content = array(
                "MemberID" => $data["MemberID"],
                "SurveyNr" => $data['SurveyNr'],
                "PlotNr" => $data['PlotNr'],
                "Latitude" => $data['Latitude'],
                "Longitude" => $data['Longitude'],
                "OwnershipDoc" => $data['LandLegality'],
                "AnnualProduction" => $data['Production'],
                "SoilType" => $data['SoilType'],
                "FirstPlantingYear" => (!isset($data['PlantingYear']))?'':$data['PlantingYear'],
                "RecipientDealer" => $data['RecipientDealer'],
                "Address" => (!isset($data['Address']))?'':$data['Address'],
                "TreeTBM" => (!isset($data['Tbm']))?'':$data['Tbm'],
                "TreeTM" => (!isset($data['Tm']))?'':$data['Tm'],
                "TreeTR" => (!isset($data['Tr']))?'':$data['Tr'],
                "StatusCode" => 'active',
                "VillageID" => @$data['VillageID'],
                "GardenAreaHa" => @$data['GardenAreaHa'],
                "uid" => $uid
            );
            
            $name = $data["MemberID"] . '-' . strtotime(date('YmdHis')). '-Plot-' . $data['SurveyNr'];
            $dir = FCPATH . 'backup_traceability_sync';
            if(!is_dir($dir)) {
            make_directory($dir, 0777, true);
            }
            if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}

            $plotData = $this->checkExistingPlot($content['MemberID'], $content['SurveyNr'], $content['PlotNr']);
            
            if ($plotData) {
                if (strtotime($data['DateUpdated']) < strtotime($plotData['DateUpdated'])) {
                    return array(
                        'success' => false,
                        'message' => 'Save data failed',
                        'error' => 'DateUpdated < DateUpdated WEB',
                    );
                }
                
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->where("MemberID",$content["MemberID"]);
                $this->db->where("SurveyNr",$content["SurveyNr"]);
                $this->db->where("PlotNr",$content["PlotNr"]);
                $this->db->update('ktv_survey_plot', $content);
                
                $insid = $data['MemberID'];

                $contentPlotStatus = array(
                    "MemberID" => $data["MemberID"],
                    "PlotNr" => $data['PlotNr'],
                    "ActiveStatus" => '1',
                    "GardenAreaHa" => @$data['GardenAreaHa'],
                    "Latitude" => $data['Latitude'],
                    "Longitude" => $data['Longitude'],
                    "AnnualProduction" => $data['Production']
                );

                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->where("MemberID",$content["MemberID"]);
                $this->db->update('ktv_survey_plot_status', $contentPlotStatus);
                $insid = $data['MemberID'];

            } else {
                $farmerData = $this->checkExistingFarmer($content['MemberID']);
                
                if ($farmerData) {
                    
                    $content['DateCollection'] = $data['DateCreated'];
                    $content['DateCreated'] = $data['DateCreated'];
                    $content['DateUpdated'] = $data['DateUpdated'];
                    $content['CreatedBy'] = $data['CreatedBy'];
                    $content['LastModifiedBy'] = $data['LastModifiedBy'];

                    $this->db->insert('ktv_survey_plot', $content);

                    $contentPlotStatus = array(
                        "MemberID" => $data["MemberID"],
                        "PlotNr" => $data['PlotNr'],
                        "ActiveStatus" => '1',
                        "GardenAreaHa" => @$data['GardenAreaHa'],
                        "Latitude" => $data['Latitude'],
                        "Longitude" => $data['Longitude'],
                        "AnnualProduction" => $data['Production']
                    );
                    
                    $contentPlotStatus['DateCreated'] = $data['DateCreated'];
                    $contentPlotStatus['DateUpdated'] = $data['DateUpdated'];
                    $contentPlotStatus['CreatedBy'] = $data['CreatedBy'];
                    $contentPlotStatus['LastModifiedBy'] = $data['LastModifiedBy'];
                    $this->db->insert('ktv_survey_plot_status', $contentPlotStatus);
                    
                }
            }

        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $result = $this->db->trans_complete();

        if ($result) {
            return array('success' => $result);
        } else {
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
}
