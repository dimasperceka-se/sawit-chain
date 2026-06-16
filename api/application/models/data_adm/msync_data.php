<?php
/*
* @Author: Gitandi Nadzari
* @Date: 2020-02-10 14:11:10
*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Msync_data extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('curl');
        // $this->load->model('tools/msyn');
        // $this->load->helper('common_helper');
    }

    public function sqlViewToMobile($args){
        list($sqlViewName, $UserName) = $args;
        $result["success"] = 1;
        $usemysqlview = 0;
        $usemysqlstoreprocedure = 0;
        $sql = "SELECT a.* FROM mw_sqlview_to_mobile as a where a.mysql_view_name <> '' and sqlview_name = ?";
        $query = $this->db->query($sql, array($sqlViewName));
        if($query->num_rows() > 0){
            $resquery = $query->result_array();
            foreach($resquery as $rows => $cols){
                $referenceView = $cols["mysql_view_name"];
                $usemysqlview = 1;
                $sqlRef = "SELECT a.* FROM ".$referenceView." as a";
                $queryRef = $this->db->query($sqlRef);
                if($queryRef->num_rows() == 0){
                    $result["success"] = 0;
                    $result['data']["error"] = "Data not found";
                    return $result;
                } else {
                    $result['rows'] = $queryRef->num_rows();
                    $result['cols'] = $queryRef->num_fields();
                    $result['data'] = $queryRef->result_array();
                }
            }
        }
        $sql = "SELECT a.* FROM mw_sqlview_to_mobile as a where a.mysql_store_procedure <> '' and sqlview_name = ?";
        $query = $this->db->query($sql, array($sqlViewName));
        if($query->num_rows() > 0){
            $resquery = $query->result_array();
            foreach($resquery as $rows => $cols){
                $referenceSP = $cols["mysql_store_procedure"];
                $usemysqlstoreprocedure = 1;
                if($sqlViewName=='getCollectivaSetting' || $sqlViewName=='getUserAccessPolicy'){
                    $sqlRef = "call ".$referenceSP."(?)"; // saat ini parameternya hanya username. jika ada penambahan parameter di mysql store procedure, silahkan tambah disini
                    $queryRef = $this->db->query($sqlRef,array($UserName));
                    if($queryRef->num_rows() == 0){
                        $result["success"] = 0;
                        $result['data']["error"] = "Data not found";
                        return $result;
                    } else {
                        $result['rows'] = $queryRef->num_rows();
                        $result['cols'] = $queryRef->num_fields();
                        $result['data'] = $queryRef->result_array();
                    }
                } else {
                    $result["success"] = 0;
                }
            }
        }
        if($usemysqlview == 0 && $usemysqlstoreprocedure == 0){
            $result["success"] = 0;
            return $result;
        }
        return $result;
    }
    public function sendToMobile($args){

        list($ProgramUid, $DateTimeFilter, $UserName, $ExtUid) = $args;
        
        $arrExtUid = [];
        if(strlen($ExtUid) > 0) {
            $arrExtUid = array_filter(explode(';',$ExtUid));
        }

        $referenceTable = "";
        $result["success"] = 1;
        $filter = "";
        if($ProgramUid != '') {
            $arrProgramUid = explode(";",$ProgramUid);
            foreach($arrProgramUid as $val){
                //check for _HIDE sub program
                $subprograms = $this->getSubProgram($val);
                if(count($subprograms) > 0) {
                    foreach($subprograms as $sval) {
                        array_push($arrProgramUid,$sval);
                    }
                }
            }
            $filter .= " AND a.uid IN ('".implode("','",$arrProgramUid)."')";
        }

        $sqlgetdatetime = "SELECT UNIX_TIMESTAMP(now()) as lastTimestamp, now() as lastDttm, FROM_UNIXTIME(?) as DttmFilter";
        $querygetdatetime = $this->db->query($sqlgetdatetime, array($DateTimeFilter));
        $resgetdatetime = $querygetdatetime->result_array();

        $sqlprograms = "SELECT
            DISTINCT a.*
        FROM
            mw_program as a
        where
            a.Status = 1
            --filter--";
        $sqlprograms = str_replace('--filter--',$filter,$sqlprograms);
        $queryprograms = $this->db->query($sqlprograms);
        if($queryprograms->num_rows() > 0){
            $resprograms = $queryprograms->result_array();
            $result["DateTimeFilter"] = $DateTimeFilter;
            $result["DttmFilter"] = $resgetdatetime[0]["DttmFilter"];
            $result["UserName"] = $UserName;
            $result["lastTimestamp"] = $resgetdatetime[0]["lastTimestamp"];
            $result["lastDttm"] = $resgetdatetime[0]["lastDttm"];
            $records = [];
            foreach($resprograms as $rows => $cols){

                //add program keys
                $result['rows'][$cols["uid"]] = [];
                $ids = $this->getIdByUserName($cols["uid"],$UserName);
                //$ids = $this->getFarmerIdByAgronomist($UserName);
                
                //var_dump($ids);die;

                $referenceTable = $cols["send_to_mobile_mapping"];
                if(strlen($referenceTable) > 0 && count($ids) > 0) {

                    $sql = "SELECT a.* FROM ".$referenceTable." as a 
                    WHERE a.id IN('".implode("','",$ids)."')";

                    if(count($arrExtUid) > 0) {
                        $sql = " SELECT a.*
                        FROM ".$referenceTable." a 
                        WHERE a.uid in('".implode("','",$arrExtUid)."')";
                    }
                    
                    $query = $this->db->query($sql);
                    
                    if($query->num_rows() > 0){

                        foreach($query->result_array() as $key => $argval){

                            $result["rows"][$cols["uid"]][$key] = array_filter($argval, function($a) {
                                return trim($a) !== "";
                            }); // remove null values

                            //escaping single quote
                            foreach($argval as $k => $arg) {
                                $result["rows"][$cols["uid"]][$key][$k] = str_replace("'","\'",$arg);
                            }

                            $result["rows"][$cols["uid"]][$key]['username'] = $UserName;
                        }
                    }
                } else {
                    if(count($arrExtUid) > 0) {
                        $sql = " SELECT a.*
                        FROM ".$referenceTable." a 
                        WHERE a.uid in('".implode("','",$arrExtUid)."')";

                        $query = $this->db->query($sql);
                    
                        if($query->num_rows() > 0){

                            foreach($query->result_array() as $key => $argval){

                                $result["rows"][$cols["uid"]][$key] = array_filter($argval, function($a) {
                                    return trim($a) !== "";
                                }); // remove null values

                                //escaping single quote
                                foreach($argval as $k => $arg) {
                                    $result["rows"][$cols["uid"]][$key][$k] = str_replace("'","\'",$arg);
                                }

                                $result["rows"][$cols["uid"]][$key]['username'] = $UserName;
                            }
                        }
                    }
                }
            }
        }
        
        return $result;
    }

    public function getSubProgram($programuid) {
        
        $output = [];

        $sql = "SELECT uid FROM mw_program WHERE parentuid = ? AND name LIKE '%_HIDE'";

        $Q = $this->db->query($sql,[$programuid]);
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            foreach($result as $key => $value) {
                array_push($output,$value['uid']);
            }
        }

        return $output;
    }

    public function getTrainingStatus($data) {

        $output = [
            'training' => []
        ];

        foreach($data['training'] as $key => $training) {
            
            $o = [
                'training_id' => $training['training_id'],
                'sync_time'   => $training['sync_time'],
                'status'      => $this->getTrainingSyncStatusByTimestamp($training['training_id'],$training['sync_time'])
            ];

            array_push($output['training'],$o);
        }

        return $output;
    }

    public function getTrainingSyncStatusByTimestamp($trainingid,$synctime) {
        
        $sql = 'SELECT MobileSyncFileTimestamp synctime FROM ktv_training_farmer WHERE TrainFarmerID = ?';

        $Q = $this->db->query($sql,[$trainingid]);
        if($Q->num_rows() > 0) {
            $row = $Q->row();

            if($row->synctime == $synctime) {
                return 'synced';
            }
        }

        return 'not_available';
    }

    public function getIdByUserName($ProgramUid,$username) {

        $count = 0;
        $farmers = [];

        //check for assigned farmers
        $sql = "SELECT DISTINCT
            a.apmMemberID id
        FROM
            ktv_access_partner_member a
            INNER JOIN ktv_member_role mr ON mr.MemberID = a.apmMemberID
            INNER JOIN ktv_members m ON m.MemberID = a.apmMemberID
            INNER JOIN ktv_village v ON v.VillageID = m.VillageID
            INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
        WHERE
            mr.MRoleID = 1
            AND m.StatusCode = 'active'
            AND sd.DistrictID IN (
            SELECT
                kas.DistrictID
            FROM
                ktv_access_staff kas
                LEFT JOIN sys_user u ON u.UserId = kas.UserID
            WHERE
            u.UserName = ?
            )
        AND
            a.apmPartnerID REGEXP (
                SELECT IF(a.ObjType IN('program','private'),a.ObjID,IFNULL(CONCAT(GROUP_CONCAT( c.apmiPartnerID SEPARATOR '|' ), ','| b.PartnerID), a.ObjID)) PartnerID
                FROM
                    ktv_staffs a
                LEFT JOIN ktv_mill b ON b.MillID = a.MillID
                LEFT JOIN ktv_access_partner_mill c ON c.apmiPartnerID = b.PartnerID
                LEFT JOIN ktv_persons d ON d.PersonID = a.PersonID
                LEFT JOIN sys_user e ON e.UserId = d.UserID
                WHERE e.UserName = ?
            )";
        
        //override, di ubah default nya berdasarkan farmer assignment
        $sql = "SELECT DISTINCT ksas.MemberID id FROM ktv_staffs_assignment_member ksas  
        INNER JOIN ktv_staffs_assignment ksa ON ksa.StaffAssignmentID = ksas.StaffAssignmentID 
        INNER JOIN ktv_staffs ks ON ks.StaffID = ksa.StaffID 
        INNER JOIN ktv_persons p ON p.PersonID = ks.PersonID 
        INNER JOIN sys_user u ON u.UserId = p.UserID 
        WHERE ksa.StatusCode = 'active' AND u.UserName = ?";
        
        if($ProgramUid == 'HBoMDtZ4PlN'){ // sme
            $sql = "
            SELECT DISTINCT
                a.apmMemberID id
            FROM
                ktv_access_partner_member a
                INNER JOIN ktv_member_role mr ON mr.MemberID = a.apmMemberID
                INNER JOIN ktv_members m ON m.MemberID = a.apmMemberID
                INNER JOIN ktv_village v ON v.VillageID = m.VillageID
                INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
            WHERE
                mr.MRoleID = 7
                AND m.StatusCode = 'active'
                AND sd.DistrictID IN (
                SELECT
                    kas.DistrictID
                FROM
                    ktv_access_staff kas
                    LEFT JOIN sys_user u ON u.UserId = kas.UserID
                WHERE
                u.UserName = ?
                )
            AND
                a.apmPartnerID REGEXP (
                    SELECT IF(a.ObjType IN('program','private'),a.ObjID,IFNULL(CONCAT(GROUP_CONCAT( c.apmiPartnerID SEPARATOR '|'), ','| b.PartnerID), a.ObjID)) PartnerID
                    FROM
                        ktv_staffs a
                    LEFT JOIN ktv_mill b ON b.MillID = a.MillID
                    LEFT JOIN ktv_access_partner_mill c ON c.apmiPartnerID = b.PartnerID
                    LEFT JOIN ktv_persons d ON d.PersonID = a.PersonID
                    LEFT JOIN sys_user e ON e.UserId = d.UserID
                    WHERE
                        a.MillID IS NOT NULL
                        AND e.UserName = ?
                )";
        }

        if($ProgramUid == 'uGuXqNH4LMd'){ // mill
            $sql = "
            SELECT DISTINCT a.MillID id FROM ktv_mill a 
            LEFT JOIN ktv_village v ON v.VillageID = a.VillageID 
            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID 
            WHERE a.StatusCode = 'active' AND sd.DistrictID IN(
                SELECT kas.DistrictID FROM ktv_access_staff kas
                LEFT JOIN sys_user u ON u.UserId = kas.UserID 
                WHERE u.UserName = ?
            ) AND a.MillID IN (
                SELECT b.MillID
                FROM
                    ktv_staffs a
                LEFT JOIN ktv_mill b ON b.MillID = a.MillID
                LEFT JOIN ktv_access_partner_mill c ON c.apmiPartnerID = b.PartnerID
                LEFT JOIN ktv_persons d ON d.PersonID = a.PersonID
                LEFT JOIN sys_user e ON e.UserId = d.UserID
                WHERE
                    a.MillID IS NOT NULL
                    AND e.UserName = ?
            )";
        }

        if($ProgramUid == 'eBCX1KfaDmA'){ // farmer applicant
            $sql = "
            SELECT DISTINCT a.ApplicantID id FROM ktv_applicant_farmers a 
            INNER JOIN ktv_village v ON v.VillageID = a.VillageID 
            INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID 
            WHERE a.StatusCode = 'active' AND sd.DistrictID IN(
                SELECT kas.DistrictID FROM ktv_access_staff kas
                LEFT JOIN sys_user u ON u.UserId = kas.UserID 
                WHERE u.UserName = ?
            )";
        }

        /* di lepas, karena sudah mengikuti farmer nya
        if(in_array($ProgramUid,['DsUfjT3cnxF','vcYkwFJe9cU','EwhWOGzvXm7'])){ // farmer coaching
            $sql = "
            SELECT DISTINCT a.FarmerID id FROM ktv_ims_farmer_coaching a 
            INNER JOIN ktv_members m ON m.MemberID = a.FarmerID 
            WHERE m.StatusCode = 'active' AND a.UserName = ?";
        }
        */

        if($ProgramUid == 'DHXgdzq7txw'){ // farmer group
            $sql = "
            SELECT DISTINCT a.FarmerGroupID id FROM ktv_farmer_group a 
            INNER JOIN ktv_village v ON v.VillageID = a.VillageID 
            INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID 
            WHERE a.StatusCode = 'active' AND sd.DistrictID IN(
                SELECT kas.DistrictID FROM ktv_access_staff kas
                LEFT JOIN sys_user u ON u.UserId = kas.UserID 
                WHERE u.UserName = ?
            )";
        }

        if($ProgramUid == 'IWRV1QwD9v0'){ // mill staff
            $sql = "
            SELECT DISTINCT a.PersonID id FROM ktv_persons a 
            LEFT JOIN ktv_staffs s ON s.PersonID = a.PersonID
            LEFT JOIN ktv_village v ON v.VillageID = a.VillageID 
            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID 
            WHERE a.StatusCd = 'active' AND s.MillID IN(
                SELECT b.MillID
                FROM
                    ktv_staffs a
                LEFT JOIN ktv_mill b ON b.MillID = a.MillID
                LEFT JOIN ktv_access_partner_mill c ON c.apmiPartnerID = b.PartnerID
                LEFT JOIN ktv_persons d ON d.PersonID = a.PersonID
                LEFT JOIN sys_user e ON e.UserId = d.UserID
                WHERE
                    a.MillID IS NOT NULL
                    AND e.UserName = ?
            )";
        }

        if($ProgramUid == 'ghuHYjpoPQn'){ // Collection Point neo_HIDE
            $sql = "
            SELECT DISTINCT a.CollectpointID id FROM ktv_collecting_point a 
            INNER JOIN ktv_village v ON v.VillageID = a.VillageID 
            INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID 
            WHERE a.StatusCode = 'active' AND sd.DistrictID IN(
                SELECT kas.DistrictID FROM ktv_access_staff kas
                LEFT JOIN sys_user u ON u.UserId = kas.UserID 
                WHERE u.UserName = ?
            )";
        }

        if($ProgramUid == 'eOXLyd9bQOK'){ // Vehicle Form NEO_HIDE
            $sql = "
            SELECT DISTINCT a.VehID id FROM ktv_member_vehicle a 
            INNER JOIN ktv_members m ON m.MemberID = a.MemberID
            INNER JOIN ktv_village v ON v.VillageID = m.VillageID 
            INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID 
            WHERE a.StatusCode = 'active' AND sd.DistrictID IN(
                SELECT kas.DistrictID FROM ktv_access_staff kas
                LEFT JOIN sys_user u ON u.UserId = kas.UserID 
                WHERE u.UserName = ?
            )";
        }

        if($ProgramUid == 'ZNjJE4U6rqU'){ // Staff SME Neo_HIDE
            $sql = "
            SELECT DISTINCT a.PersonID id FROM ktv_persons a 
            INNER JOIN ktv_staffs s ON s.PersonID = a.PersonID
            WHERE a.StatusCd = 'active' AND ( s.ObjID IN(
                SELECT DISTINCT
                    a.apmMemberID id
                FROM
                    ktv_access_partner_member a
                    INNER JOIN ktv_member_role mr ON mr.MemberID = a.apmMemberID
                    INNER JOIN ktv_members m ON m.MemberID = a.apmMemberID
                    INNER JOIN ktv_village v ON v.VillageID = m.VillageID
                    INNER JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                WHERE
                    mr.MRoleID IN (5,6,7,8,9,10,11,12,13,14)
                    AND m.StatusCode = 'active'
                    AND sd.DistrictID IN (
                    SELECT
                        kas.DistrictID
                    FROM
                        ktv_access_staff kas
                        LEFT JOIN sys_user u ON u.UserId = kas.UserID
                    WHERE
                    u.UserName = ?
                    )
                AND
                    a.apmPartnerID IN (
                        SELECT CONCAT( a.ObjID, ',', GROUP_CONCAT( c.PartnerID ), ',', b.PartnerID) PartnerID
                        FROM
                            ktv_staffs a
                        LEFT JOIN ktv_mill b ON b.MillID = a.MillID
                        LEFT JOIN ktv_affiliate_partner c ON c.PartnerAffiliateID = b.PartnerID
                        LEFT JOIN ktv_persons d ON d.PersonID = a.PersonID
                        LEFT JOIN sys_user e ON e.UserId = d.UserID
                        WHERE
                            a.MillID IS NOT NULL
                            AND e.UserName = ?
                    )
            )
						OR s.ObjID IN (
							SELECT
								st.SmeID
							FROM
								sys_user su
							LEFT JOIN
								ktv_persons kp on kp.UserID = su.UserId
							LEFT JOIN
								ktv_staffs st on st.PersonID = kp.PersonID
							WHERE
								su.UserName = ?
						)
            )";
        }


        $exec = $this->db->query($sql,[$username,$username,$username]);

        $count = $exec->num_rows();

        if($count > 0) {
            $results = $exec->result_array();
            foreach($results as $key => $values) {
                array_push($farmers,$values['id']);
            }
        }

        return $farmers;
    }

    public function getFarmerIdByAgronomist($username) {
        $count = 0;
        $farmers = [];
        $sql = "SELECT DISTINCT ksas.MemberID id FROM ktv_staffs_assignment_member ksas  
            INNER JOIN ktv_staffs_assignment ksa ON ksa.StaffAssignmentID = ksas.StaffAssignmentID 
            INNER JOIN ktv_staffs ks ON ks.StaffID = ksa.StaffID 
            INNER JOIN ktv_persons p ON p.PersonID = ks.PersonID 
            INNER JOIN sys_user u ON u.UserId = p.UserID 
            WHERE u.UserName = ?
        ";
        $exec = $this->db->query($sql,[$username]);
        $count = $exec->num_rows();

        if($count > 0) {
            $results = $exec->result_array();
            foreach($results as $key => $values) {
                array_push($farmers,$values['id']);
            }
        }

        return $farmers;
    }
}
?>