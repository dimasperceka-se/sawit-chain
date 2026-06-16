<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-03 15:29:40
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdboard extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function DisplayChartKsatriaSawit($lockdate, $ProgID, $chdistrict){
        $this->load->model("dboard/mpro_kpi");

        $sqlwhere = "";
        $sqlwheretarget = "";
        $sqlparam = array();
        $sqlparamtarget = array();
        // $ProgID = $this->mpro_kpi->GetProgIDKS($wave);

        if($lockdate != "" AND $ProgID != "all_wave"){
            $sqlwhere .= " AND DATE(a.HisDateCreated) = ?";
            $sqlparam[] = $lockdate;

            $sqlwheretarget .= " AND a.Year = ?";
            $sqlparamtarget[] = date("Y", strtotime($lockdate));
        }

        if($ProgID != "" AND $ProgID != "all_wave"){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;

            $sqlwheretarget .= " AND a.ProgID = ?";
            $sqlparamtarget[] = $ProgID;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.DistrictID = ?";
            $sqlparam[] = $chdistrict;

            $sqlwheretarget .= " AND a.DistrictID = ?";
            $sqlparamtarget[] = $chdistrict;
        }

        if($ProgID == "all_wave"){
            $slwhereall = ($chdistrict != "" AND $chdistrict != "all_cluster") ? " AND a.DistrictID = '{$chdistrict}'": "";
            $sql = "SELECT
                IFNULL(SUM(a.PalmOilMillsParticipant), 0) MillParticipant
                , IFNULL(SUM(a.SMEKelapaSawitTerpetakan), 0) SMEMapped
                , IFNULL(SUM(a.PalmOilFarmers), 0) Farmers
                , IFNULL(SUM(a.PalmOilPlantations), 0) Plantation
                , IFNULL(SUM(a.AreaPerkebunanKelapaSawitHa), 0) PlantationArea
                , IFNULL(SUM(a.FarmCloudUsers), 0) FarmCUser
                , IFNULL(SUM(a.ActiveFarmGateUsers), 0) FarmGUser
                , IFNULL(SUM(a.FarmGateMTTraceable), 0) FarmGMT
            FROM
                bi_view_ks_kpi_project_area_history_all a
            WHERE
                1=1
            $slwhereall";
        }else{
            $sql = "SELECT
                    IFNULL(SUM(a.MillParticipant) ,0) MillParticipant
                    ,IFNULL(SUM(a.SMEMapped) ,0) SMEMapped
                    ,IFNULL(SUM(a.Farmers) ,0) Farmers
                    ,IFNULL(SUM(a.Plantation) ,0) Plantation
                    ,IFNULL(SUM(a.PlantationArea) ,0) PlantationArea
                    ,IFNULL(SUM(a.FarmCUser) ,0) FarmCUser
                    ,IFNULL(SUM(a.FarmGUser) ,0) FarmGUser
                    ,IFNULL(SUM(a.FarmGMT) ,0) FarmGMT
                FROM
                    `ktv_ks_kpi_project_area_history` a
                WHERE
                    1=1
                $sqlwhere";
        }

        $return['data'] = $this->db->query($sql,$sqlparam)->row_array();
        $return['sql']  = $this->db->last_query();

        $sql = "SELECT
                IFNULL(SUM(a.MillParticipant) ,0) MillParticipant
                ,IFNULL(SUM(a.SMEMapped) ,0) SMEMapped
                ,IFNULL(SUM(a.Farmers) ,0) Farmers
                ,IFNULL(SUM(a.Plantation) ,0) Plantation
                ,IFNULL(SUM(a.PlantationArea) ,0) PlantationArea
                ,IFNULL(SUM(a.FarmCUser) ,0) FarmCUser
                ,IFNULL(SUM(a.FarmGUser) ,0) FarmGUser
                ,IFNULL(SUM(a.FarmGMT) ,0) FarmGMT
            FROM
                `ktv_ks_kpi_project_area_target` a
            WHERE
                1=1
            $sqlwheretarget
        ";

        $return['target'] = $this->db->query($sql,$sqlparamtarget)->row_array();

        // echo "<pre>";print_r($this->db->last_query());die;

        $return['success'] = true;
        return $return;
    }

    public function DisplayChartSawitTerampil($lockdate, $wave, $chdistrict){
        $sqlwhere = "";
        $sqlwheretarget = "";
        $sqlparam = array();
        $sqlparamtarget = array();

        if($lockdate != ""){
            $sqlwhere .= " AND DATE(a.HisDateCreated) = ?";
            $sqlparam[] = $lockdate;

            $sqlwheretarget .= " AND a.Year = ?";
            $sqlparamtarget[] = date("Y", strtotime($lockdate));
        }

        if($wave != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $wave;

            $sqlwheretarget .= " AND a.ProgID = ?";
            $sqlparamtarget[] = $wave;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $chdistrict;

            $sqlwheretarget .= " AND a.ClusterID = ?";
            $sqlparamtarget[] = $chdistrict;
        }

        $sql = "SELECT
                SUM(a.KsMill+a.StMill) palm_oil_mill
                , SUM(a.FarmerReg) farmers_registered
                , SUM(a.FarmReg) farm_registered
                , SUM(a.Ha) farm_ha
                , SUM(a.SocSel) soc_sel
                , SUM(a.FarmerSurveyBP) farmer_survey
                , SUM(a.FarmSurvey) farm_survey
                , SUM(a.Polygon) polygon_mapping
                , SUM(a.FarmerCoach) individual_farmer_coaching
                , SUM(a.CoachingSess) individual_farmer_coaching_session
                , SUM(a.Sms) broadcast_sms
                , SUM(a.IdCard) farmer_id_card
                , SUM(a.FarmX) farmx
                , SUM(a.FarmG) farmgate
                , SUM(a.FarmR) farmretail
                , SUM(a.FarmC) farmcloud
            FROM
                `ktv_certification_progress_st_ims_district_history` a
            WHERE
                1=1
            $sqlwhere";

        $return['data'] = $this->db->query($sql,$sqlparam)->row_array();
        $return['data']['farmers_registered_detail'] = $this->farmer_registered_detail($lockdate, $wave, $chdistrict);
        $return['data']['mill_detail']               = $this->mill_detail($lockdate, $wave, $chdistrict);
        $return['data']['farm_detail']               = $this->farm_detail($lockdate, $wave, $chdistrict);
        $return['sql']  = $this->db->last_query();

        $sql = "SELECT
                SUM(a.KsMill+a.StMill) palm_oil_mill
                , SUM(a.FarmerReg) farmers_registered
                , SUM(a.FarmReg) farm_registered
                , SUM(a.Ha) farm_ha
                , SUM(a.SocSel) soc_sel
                , SUM(a.FarmerSurveyBP) farmer_survey
                , SUM(a.FarmSurvey) farm_survey
                , SUM(a.Polygon) polygon_mapping
                , SUM(a.FarmerCoach) individual_farmer_coaching
                , SUM(a.CoachingSess) individual_farmer_coaching_session
                , SUM(a.Sms) broadcast_sms
                , SUM(a.IdCard) farmer_id_card
                , SUM(a.FarmX) farmx
                , SUM(a.FarmG) farmgate
                , SUM(a.FarmR) farmretail
                , SUM(a.FarmC) farmcloud
            FROM
                `ktv_kpi_st_certification_target_ims_district` a
            WHERE
                1=1
            $sqlwheretarget
        ";

        $return['target'] = $this->db->query($sql,$sqlparamtarget)->row_array();

        $return['success'] = true;
        return $return;
    }

    public function farm_detail($lockdate, $wave, $chdistrict){
        $sqlwhere = "";
        $sqlparam = array();

        if($lockdate != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $lockdate;
        }

        if($wave != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $wave;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $chdistrict;
        }

        $return = array();
        $object[1] = lang("Farm From New Farmer");
        $object[2] = lang("Farm From Existing Farmer");

        for($i=1;$i<=count($object);$i++){
            $sql = "SELECT
                    count(a.id) total
                    , '$object[$i]' objtype
                    , IFNULL(SUM(a.GardenAreaHa),0) total_ha
                FROM
                    `ktv_st_kpi_summary_farm_register` a 
                WHERE
                    1 =1
                $sqlwhere
                AND a.ObjType = $i
                ORDER BY ObjType DESC";
            $data = $this->db->query($sql,$sqlparam)->row_array();

            array_push($return, $data);
        }

        return $return;
    }

    public function farmer_registered_detail($lockdate, $wave, $chdistrict){
        $sqlwhere = "";
        $sqlparam = array();

        if($lockdate != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $lockdate;
        }

        if($wave != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $wave;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $chdistrict;
        }

        $return = array();
        $object[1] = lang("New Farmer");
        $object[2] = lang("Existing Farmer");

        for($i=1;$i<=count($object);$i++){
            $sql = "SELECT
                    count(a.id) total
                    , '$object[$i]' objtype
                FROM
                    `ktv_st_kpi_summary_farmer_register` a 
                WHERE
                    1 =1
                $sqlwhere
                AND a.ObjType = $i
                ORDER BY ObjType DESC";
            $data = $this->db->query($sql,$sqlparam)->row_array();

            array_push($return, $data);
        }

        return $return;
    }

    public function mill_detail($lockdate, $wave, $chdistrict){
        $sqlwhere = "";
        $sqlparam = array();

        if($lockdate != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $lockdate;
        }

        if($wave != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $wave;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $chdistrict;
        }

        $return = array();
        $object[1] = lang("Ksatria Sawit");
        $object[2] = lang("Sawit Terampil");

        for($i=1;$i<=count($object);$i++){
            $sql = "SELECT
                    count(a.id) total
                    , '$object[$i]' objtype
                FROM
                    `ktv_st_kpi_summary_mills` a 
                WHERE
                    1 =1
                $sqlwhere
                AND a.ObjType = $i
                ORDER BY ObjType DESC";
            $data = $this->db->query($sql,$sqlparam)->row_array();

            array_push($return, $data);
        }

        return $return;
    }

    public function DisplayChartKpiKoltiva($ProvinceID = NULL, $PartnerID, $Year){
        // original script

        // $sql = "SELECT
        //             SUM(RegisteredFarmer) RegisteredFarmer
        //             , SUM(TrainOrCoachFarmers) TrainOrCoachFarmers
        //             , SUM(RegisteredPlantation) RegisteredPlantation
        //             , SUM(RegisteredPlantationHectares) RegisteredPlantationHectares
        //             , SUM(ResponSourcingFarmers) ResponSourcingFarmers
        //             , SUM(TraceTransaction) TraceTransaction
        //             , SUM(PlatformUsers) PlatformUsers
        //             , SUM(RegisteredSME) RegisteredSME
        //             , SUM(FarmXUsers) FarmXUsers
        //             , SUM(FarmGateUsers) FarmGateUsers
        //             , SUM(FarmRetailUsers) FarmRetailUsers
        //             , SUM(FarmCloudUsers) FarmCloudUsers
        //         FROM
        //             dash_kpi_koltiva_target a
        //         WHERE 1=1
        //         ";

        // modified 14-4-2021

        if ($Year == null) {
            $getLastKPI = $this->db->select('MAX(Year) AS Year', FALSE)
                               ->get('dash_pro_kpi')
                               ->result()[0]->Year;

            $Year = $getLastKPI;
        }

        $sqldWherePartner = " AND a.PartnerID = '$PartnerID' ";
        $sqldWhereYear    = " AND a.Year = '$Year' ";

        $sql = "SELECT
                    SUM(farmer_registered) RegisteredFarmer
                    , SUM(farmers_trained) TrainOrCoachFarmers
                    , SUM(farmers_plantation_registered) RegisteredPlantation
                    , SUM(farmers_plantation) RegisteredPlantationHectares
                    , SUM(farmers_active_responsible) ResponSourcingFarmers
                    , SUM(traceability_transactions) TraceTransaction
                    , SUM(platform_users) PlatformUsers
                    , SUM(small_medium_enterprise) RegisteredSME
                    , SUM(farm_extensions_user) FarmXUsers
                    , SUM(farm_gate_users) FarmGateUsers
                    , SUM(farm_retail_users) FarmRetailUsers
                    , SUM(farm_cloud_users) FarmCloudUsers
                FROM
                    dash_pro_kpi_target a
                WHERE 1=1
                    $sqldWherePartner
                    $sqldWhereYear
                ";

        $return['target'] = $this->db->query($sql,array())->row_array();

        $sql = "SELECT
                    SUM(RegisteredFarmer) RegisteredFarmer
                    , SUM(TrainOrCoachFarmers) TrainOrCoachFarmers
                    , SUM(RegisteredPlantation) RegisteredPlantation
                    , SUM(RegisteredPlantationHectares) RegisteredPlantationHectares
                    , SUM(ResponSourcingFarmers) ResponSourcingFarmers
                    , SUM(TraceTransaction) TraceTransaction
                    , SUM(PlatformUsers) PlatformUsers
                    , SUM(RegisteredSME) RegisteredSME
                    , SUM(FarmXUsers) FarmXUsers
                    , SUM(FarmGateUsers) FarmGateUsers
                    , SUM(FarmRetailUsers) FarmRetailUsers
                    , SUM(FarmCloudUsers) FarmCloudUsers
                FROM
                    dash_kpi_koltiva a
                WHERE 1=1
                ";
        $return['single'] = $this->db->query($sql,array())->row_array();

        $return['success'] = true;
        return $return;
    }

    public function getDataAchieveFA($awal,$akhir,$mill,$type){        
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND mill.PartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
            
            if($_SESSION['group'] == "Field Agent"){
                $sqlHakAksesPartner = " AND mill.MillID = '$_SESSION[MillID]' AND m.CreatedBy = '$_SESSION[userid]'";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }

        $sqlwhere = "";

        if($mill != ''){
            $sqlwhere = " AND mill.MillID = '$mill'";
        }

        $sql = "SELECT
                COUNT(m.MemberID) jml_petani
                , p.PersonNm Enumerator
                , p.UserID
                , s.MillID
                , mill.PartnerID
            FROM
                ktv_members m
            LEFT JOIN
                ktv_member_role mr on mr.MemberID = m.MemberID
            LEFT JOIN
                ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
            LEFT JOIN
                ktv_persons p on p.UserID = m.CreatedBy
            LEFT JOIN
                ktv_staffs s on s.PersonID = p.PersonID
            LEFT JOIN
                ktv_mill mill on mill.MillID = s.MillID
            LEFT JOIN
            	view_tc_supplychain_org vso on vso.ObjID = mill.MillID AND vso.ObjType = 'mill'
            WHERE
                m.StatusCode = 'active'
            AND
                m.CreatedBy IS NOT NULL
            AND
                rmr.MRoleType = 'Farmer'
            AND
                DATE(m.DateCreated) BETWEEN '$awal' AND '$akhir'
                $sqlHakAksesPartner
                $sqlwhere
            GROUP BY
                m.CreatedBy
            ORDER BY
                m.CreatedBy ASC";
        $query  = $this->db->query($sql);
        $return = array();
        $categories = array();
        $farmer     = array();
        $garden     = array();
        if($query->num_rows()>0){
            foreach($query->result() as $key => $row){
                $return["categories"][$row->UserID] = $row->Enumerator;
                $return["farmer"][$row->UserID] = (int)$row->jml_petani;
                // array_push($data_farmer,(int)$row->jml_petani);
            }
        }

        $sql_kebun = "SELECT
                COUNT(*) jml_kebun
                , p.PersonNm Enumerator
                , p.UserID
                , s.MillID
                , mill.PartnerID
            FROM
                ktv_survey_plot sp
            INNER JOIN
                ktv_members m on m.MemberID = sp.MemberID
            LEFT JOIN
                    ktv_member_role mr on mr.MemberID = m.MemberID
            LEFT JOIN
                    ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
            LEFT JOIN
                    ktv_persons p on p.UserID = sp.CreatedBy
            LEFT JOIN
                    ktv_staffs s on s.PersonID = p.PersonID
            LEFT JOIN
                    ktv_mill mill on mill.MillID = s.MillID
            LEFT JOIN
                view_tc_supplychain_org vso on vso.ObjID = mill.MillID AND vso.ObjType = 'mill'
            WHERE
                sp.StatusCode = 'active'
            AND
                sp.SurveyNr = 0
            AND
                m.StatusCode = 'active'
            AND
                sp.CreatedBy IS NOT NULL
            AND
                rmr.MRoleType = 'Farmer'
            AND
                DATE(sp.DateCreated) BETWEEN '$awal' AND '$akhir'
                $sqlHakAksesPartner
                $sqlwhere
            GROUP BY
                sp.CreatedBy
            ORDER BY
                sp.CreatedBy ASC";
        
        $query2  = $this->db->query($sql_kebun);
        if($query2->num_rows()>0){
            foreach($query2->result() as $key => $row){
                $return["categories"][$row->UserID] = $row->Enumerator;
                $return["kebun"][$row->UserID] = (int)$row->jml_kebun;
                // array_push($data_farmer,(int)$row->jml_petani);
            }
        }
        
        if($return["categories"]){
            foreach($return["categories"] as $key => $row){
                array_push($categories,$row);
            }
        }
        
        if($return["farmer"]){
            foreach($return["categories"] as $key => $row){
                if(isset($return["farmer"][$key])){
                    array_push($farmer,$return["farmer"][$key]);
                }else{
                    array_push($farmer,0);
                }
            }
        }
        
        if($return["kebun"]){
            foreach($return["categories"] as $key => $row){
                if(isset($return["kebun"][$key])){
                    array_push($garden,$return["kebun"][$key]);
                }else{
                    array_push($garden,0);
                }
            }
        }
        
        if($type == ""){
            $return = array(
                "categories" => $categories,
                "color" => array('#ED7D31','#a9d08e'),
                "data" => [array(
                            "name"=>lang("Farmer Population"),
                            "data"=>$farmer
                        ),array(
                            "name"=>lang("Garden Population"),
                            "data"=>$garden
                        )]
            );
        }else if($type == "farmer"){
            $return = array(
                "categories" => $categories,
                "color" => array('#ED7D31'),
                "data" => [array(
                            "name"=>lang("Farmer Population"),
                            "data"=>$farmer
                        )]
            );
        }else if($type == "garden"){
            $return = array(
                "categories" => $categories,
                "color" => array('#a9d08e'),
                "data" => [array(
                            "name"=>lang("Garden Population"),
                            "data"=>$garden
                        )]
            );
        }

        return $return;
    }

    public function generateDashDemographic(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_demographic');

        //ambil data village nya yg ada
        $sql="SELECT
                kp.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
            FROM
                ktv_members a
                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` IS NOT NULL
                AND a.VillageID != ''
                AND a.VillageID != '0'
            GROUP BY a.`VillageID`";
        $query = $this->db->query($sql);
        $dataRegion = $query->result_array();

        //ambil data Partner yg ada
        $sql="SELECT
                a.`PartnerID`
                , a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
                AND a.IsGenDashboard = 'Yes'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartner = $query->result_array();

        for ($i=0; $i < count($dataRegion); $i++) {
            for ($j=0; $j < count($dataPartner); $j++) {

                $sql="SELECT
                        '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                        , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                        , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                        , '{$dataRegion[$i]['VillageID']}' AS `VillageID`
                        , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                        , COUNT(a.`MemberID`) AS Total_Farmer
                        , IFNULL(SUM(IF(a.`Gender` = 'm',1,0)),0) AS Male_Farmer
                        , IFNULL(SUM(IF(a.`Gender` = 'f',1,0)),0) AS Female_Farmer
                        , IFNULL(SUM(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25)),0) AS Sum_Age
                        , SUM(IF((a.`DateOfBirth` IS NOT NULL) AND (a.`DateOfBirth` != '0000-00-00'),1,0)) AS Total_Farmer_Age_Divider
                        , IFNULL(SUM(IF(a.Education > 2,1,0)),0) AS Completed_Primary_School
                        , SUM(IF(a.Education IS NOT NULL,1,0)) AS Completed_Primary_School_Pembagi
                        , IFNULL((SUM(IF(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) < 35,1,0))),0) AS Below_35_Age
                        , IFNULL(SUM(tabel_family.Jumlah_HH),0) + COUNT(a.`MemberID`) AS Sum_HH_Members
                        , COUNT(a.`MemberID`) AS Total_Household
                        , IFNULL(SUM(tabel_family.Jumlah_HH),0) + COUNT(a.`MemberID`) AS Total_Household_Count
                        , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) BETWEEN 15 AND 24,1,0)),0) AS Age_15to24
                        , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) BETWEEN 25 AND 34,1,0)),0) AS Age_25to34
                        , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) BETWEEN 35 AND 44,1,0)),0) AS Age_35to44
                        , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) BETWEEN 45 AND 54,1,0)),0) AS Age_45to54
                        , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) > 54,1,0)),0) AS Age_MoreThan55
                        , IFNULL(SUM(IF(a.Education = '1',1,0)),0) AS Edu_NoEducation
                        , IFNULL(SUM(IF(a.Education = '2',1,0)),0) AS Edu_PrimarySchoolIncompleted
                        , IFNULL(SUM(IF(a.Education = '3',1,0)),0) AS Edu_PrimarySchoolCompleted
                        , IFNULL(SUM(IF(a.Education = '4',1,0)),0) AS Edu_GraduatedMiddleSchool
                        , IFNULL(SUM(IF(a.Education = '5',1,0)),0) AS Edu_GraduatedHighSchool
                        , IFNULL(SUM(IF(a.Education = '6',1,0)),0) AS Edu_GraduatedCollege
                        , IFNULL(SUM(IF(a.MaritalStatus = '1',1,0)),0) AS MStatus_Married
                        , IFNULL(SUM(IF(a.MaritalStatus = '2',1,0)),0) AS MStatus_Single
                        , IFNULL(SUM(IF(a.MaritalStatus = '3',1,0)),0) AS MStatus_Widow
                        , SUM(IF(hh.`Score` IS NOT NULL,1,0)) AS Count_farmer_survey_hh
                        , SUM(IF(hh.`Score` IS NOT NULL,hh.`1.25/day`,0)) AS Total_125_index
                        , SUM(IF(hh.`Score` IS NOT NULL,hh.`2.5/day`,0)) AS Total_25_index

                        , IFNULL(SUM(IF(a.HandphoneType = '1',1,0)),0) AS Hp_Smart
                        , IFNULL(SUM(IF(a.HandphoneType = '1' OR a.`AccessToSmartphone`='1',1,0)),0) AS Hp_Smart_Access
                        , IFNULL(SUM(IF(a.HandphoneType = '2',1,0)),0) AS Hp_Feature
                        , IFNULL(SUM(IF(a.HandphoneType = '3' OR a.HandphoneType IS NULL OR a.HandphoneType = '0',1,0)),0) AS Hp_NoHp

                        , NOW() AS DateGenerated
                    FROM
                        ktv_members a
                        JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                        LEFT JOIN (
                            SELECT
                                sub_b.MemberID
                                , COUNT(sub_b.FamLabID) AS Jumlah_HH
                            FROM
                                ktv_member_family_labour sub_b
                            WHERE
                                sub_b.StatusCode = 'active'
                            GROUP BY sub_b.MemberID
                        ) AS tabel_family ON a.MemberID = tabel_family.MemberID
                        INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'

                        LEFT JOIN (
                            SELECT
                                sub_a.`MemberID`, MAX(sub_a.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_household sub_a
                            WHERE
                                sub_a.`StatusCode` = 'active'
                            GROUP BY sub_a.`MemberID`
                        ) AS hh_latest ON 1=1
                            AND hh_latest.MemberID = a.`MemberID`
                        LEFT JOIN ktv_survey_household hh ON 1=1
                            AND hh_latest.MemberID = hh.`MemberID`
                            AND hh_latest.SurveyNr = hh.`SurveyNr`
                    WHERE
                        a.`StatusCode` = 'active'
                        AND a.VillageID = '{$dataRegion[$i]['VillageID']}'";
                $query = $this->db->query($sql);
                $dataDash = $query->row_array();

                $sql="INSERT INTO `dash_det_demographic` (
                        `ProvinceID`,
                        `DistrictID`,
                        `SubDistrictID`,
                        `VillageID`,
                        `PartnerID`,
                        `Total_Farmer`,
                        `Male_Farmer`,
                        `Female_Farmer`,
                        `Sum_Age`,
                        Total_Farmer_Age_Divider,
                        `Completed_Primary_School`,
                        Completed_Primary_School_Pembagi,
                        `Below_35_Age`,
                        `Sum_HH_Members`,
                        `Total_Household`,
                        `Total_Household_Count`,
                        `Age_15to24`,
                        `Age_25to34`,
                        `Age_35to44`,
                        `Age_45to54`,
                        `Age_MoreThan55`,
                        `Edu_NoEducation`,
                        `Edu_PrimarySchoolIncompleted`,
                        `Edu_PrimarySchoolCompleted`,
                        `Edu_GraduatedMiddleSchool`,
                        `Edu_GraduatedHighSchool`,
                        `Edu_GraduatedCollege`,
                        `MStatus_Married`,
                        `MStatus_Single`,
                        `MStatus_Widow`,
                        Count_farmer_survey_hh,
                        Total_125_index,
                        Total_25_index,
                        Hp_Smart,
                        Hp_Smart_Access,
                        Hp_Feature,
                        Hp_NoHp,
                        `DateGenerated`
                    )
                    VALUES
                        (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            NOW()
                        )
                    ON DUPLICATE KEY UPDATE
                        `Total_Farmer` = ?,
                        `Male_Farmer` = ?,
                        `Female_Farmer` = ?,
                        `Sum_Age` = ?,
                        Total_Farmer_Age_Divider = ?,
                        `Completed_Primary_School` = ?,
                        `Completed_Primary_School_Pembagi` = ?,
                        `Below_35_Age` = ?,
                        `Sum_HH_Members` = ?,
                        `Total_Household` = ?,
                        `Total_Household_Count` = ?,
                        `Age_15to24` = ?,
                        `Age_25to34` = ?,
                        `Age_35to44` = ?,
                        `Age_45to54` = ?,
                        `Age_MoreThan55` = ?,
                        `Edu_NoEducation` = ?,
                        `Edu_PrimarySchoolIncompleted` = ?,
                        `Edu_PrimarySchoolCompleted` = ?,
                        `Edu_GraduatedMiddleSchool` = ?,
                        `Edu_GraduatedHighSchool` = ?,
                        `Edu_GraduatedCollege` = ?,
                        `MStatus_Married` = ?,
                        `MStatus_Single` = ?,
                        `MStatus_Widow` = ?,
                        Count_farmer_survey_hh = ?,
                        Total_125_index = ?,
                        Total_25_index = ?,
                        Hp_Smart = ?,
                        Hp_Smart_Access = ?,
                        Hp_Feature = ?,
                        Hp_NoHp = ?,
                        DateGenerated = NOW()
                    ";
                $p = array(
                    //insert
                    $dataDash['ProvinceID'],
                    $dataDash['DistrictID'],
                    $dataDash['SubDistrictID'],
                    $dataDash['VillageID'],
                    $dataDash['PartnerID'],
                    $dataDash['Total_Farmer'],
                    $dataDash['Male_Farmer'],
                    $dataDash['Female_Farmer'],
                    $dataDash['Sum_Age'],
                    $dataDash['Total_Farmer_Age_Divider'],
                    $dataDash['Completed_Primary_School'],
                    $dataDash['Completed_Primary_School_Pembagi'],
                    $dataDash['Below_35_Age'],
                    $dataDash['Sum_HH_Members'],
                    $dataDash['Total_Household'],
                    $dataDash['Total_Household_Count'],
                    $dataDash['Age_15to24'],
                    $dataDash['Age_25to34'],
                    $dataDash['Age_35to44'],
                    $dataDash['Age_45to54'],
                    $dataDash['Age_MoreThan55'],
                    $dataDash['Edu_NoEducation'],
                    $dataDash['Edu_PrimarySchoolIncompleted'],
                    $dataDash['Edu_PrimarySchoolCompleted'],
                    $dataDash['Edu_GraduatedMiddleSchool'],
                    $dataDash['Edu_GraduatedHighSchool'],
                    $dataDash['Edu_GraduatedCollege'],
                    $dataDash['MStatus_Married'],
                    $dataDash['MStatus_Single'],
                    $dataDash['MStatus_Widow'],
                    $dataDash['Count_farmer_survey_hh'],
                    $dataDash['Total_125_index'],
                    $dataDash['Total_25_index'],
                    $dataDash['Hp_Smart'],
                    $dataDash['Hp_Smart_Access'],
                    $dataDash['Hp_Feature'],
                    $dataDash['Hp_NoHp'],
                    //update
                    $dataDash['Total_Farmer'],
                    $dataDash['Male_Farmer'],
                    $dataDash['Female_Farmer'],
                    $dataDash['Sum_Age'],
                    $dataDash['Total_Farmer_Age_Divider'],
                    $dataDash['Completed_Primary_School'],
                    $dataDash['Completed_Primary_School_Pembagi'],
                    $dataDash['Below_35_Age'],
                    $dataDash['Sum_HH_Members'],
                    $dataDash['Total_Household'],
                    $dataDash['Total_Household_Count'],
                    $dataDash['Age_15to24'],
                    $dataDash['Age_25to34'],
                    $dataDash['Age_35to44'],
                    $dataDash['Age_45to54'],
                    $dataDash['Age_MoreThan55'],
                    $dataDash['Edu_NoEducation'],
                    $dataDash['Edu_PrimarySchoolIncompleted'],
                    $dataDash['Edu_PrimarySchoolCompleted'],
                    $dataDash['Edu_GraduatedMiddleSchool'],
                    $dataDash['Edu_GraduatedHighSchool'],
                    $dataDash['Edu_GraduatedCollege'],
                    $dataDash['MStatus_Married'],
                    $dataDash['MStatus_Single'],
                    $dataDash['MStatus_Widow'],
                    $dataDash['Count_farmer_survey_hh'],
                    $dataDash['Total_125_index'],
                    $dataDash['Total_25_index'],
                    $dataDash['Hp_Smart'],
                    $dataDash['Hp_Smart_Access'],
                    $dataDash['Hp_Feature'],
                    $dataDash['Hp_NoHp']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function generateDashDemographicOptimize(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_demographic');

        $sql="INSERT INTO dash_det_demographic (
                `ProvinceID`,
                `DistrictID`,
                `SubDistrictID`,
                `VillageID`,
                `PartnerID`,
                `Total_Farmer`,
                `Male_Farmer`,
                `Female_Farmer`,
                `Sum_Age`,
                Total_Farmer_Age_Divider,
                `Completed_Primary_School`,
                Completed_Primary_School_Pembagi,
                `Below_35_Age`,
                `Sum_HH_Members`,
                `Total_Household`,
                `Total_Household_Count`,
                `Age_15to24`,
                `Age_25to34`,
                `Age_35to44`,
                `Age_45to54`,
                `Age_MoreThan55`,
                `Edu_NoEducation`,
                `Edu_PrimarySchoolIncompleted`,
                `Edu_PrimarySchoolCompleted`,
                `Edu_GraduatedMiddleSchool`,
                `Edu_GraduatedHighSchool`,
                `Edu_GraduatedCollege`,
                `MStatus_Married`,
                `MStatus_Single`,
                `MStatus_Widow`,
                Count_farmer_survey_hh,
                Total_125_index,
                Total_25_index,
                Hp_Smart,
                Hp_Smart_Access,
                Hp_Feature,
                Hp_NoHp,
                `DateGenerated`)
            SELECT
                p.ProvinceID AS ProvinceID
                , d.DistrictID AS DistrictID
                , sd.SubDistrictID AS SubDistrictID
                , v.VillageID AS VillageID
                , pp.PartnerID AS PartnerID
                , COUNT(m.`MemberID`) AS Total_Farmer
                , IFNULL(SUM(IF(m.`Gender` = 'm',1,0)),0) AS Male_Farmer
                , IFNULL(SUM(IF(m.`Gender` = 'f',1,0)),0) AS Female_Farmer
                , IFNULL(SUM(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25)),0) AS Sum_Age
                , SUM(IF((m.`DateOfBirth` IS NOT NULL) AND (m.`DateOfBirth` != '0000-00-00'),1,0)) AS Total_Farmer_Age_Divider
                , IFNULL(SUM(IF(m.Education > 2,1,0)),0) AS Completed_Primary_School
                , SUM(IF(m.Education IS NOT NULL,1,0)) AS Completed_Primary_School_Pembagi
                , IFNULL((SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) < 35,1,0))),0) AS Below_35_Age
                , IFNULL(SUM(tabel_family.Jumlah_HH),0) + COUNT(m.`MemberID`) AS Sum_HH_Members
                , COUNT(m.`MemberID`) AS Total_Household
                , IFNULL(SUM(tabel_family.Jumlah_HH),0) + COUNT(m.`MemberID`) AS Total_Household_Count
                , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 15 AND 24,1,0)),0) AS Age_15to24
                , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 25 AND 34,1,0)),0) AS Age_25to34
                , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 35 AND 44,1,0)),0) AS Age_35to44
                , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 45 AND 54,1,0)),0) AS Age_45to54
                , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) > 54,1,0)),0) AS Age_MoreThan55
                , IFNULL(SUM(IF(m.Education = '1',1,0)),0) AS Edu_NoEducation
                , IFNULL(SUM(IF(m.Education = '2',1,0)),0) AS Edu_PrimarySchoolIncompleted
                , IFNULL(SUM(IF(m.Education = '3',1,0)),0) AS Edu_PrimarySchoolCompleted
                , IFNULL(SUM(IF(m.Education = '4',1,0)),0) AS Edu_GraduatedMiddleSchool
                , IFNULL(SUM(IF(m.Education = '5',1,0)),0) AS Edu_GraduatedHighSchool
                , IFNULL(SUM(IF(m.Education = '6',1,0)),0) AS Edu_GraduatedCollege
                , IFNULL(SUM(IF(m.MaritalStatus = '1',1,0)),0) AS MStatus_Married
                , IFNULL(SUM(IF(m.MaritalStatus = '2',1,0)),0) AS MStatus_Single
                , IFNULL(SUM(IF(m.MaritalStatus = '3',1,0)),0) AS MStatus_Widow
                , SUM(IF(hh.`Score` IS NOT NULL,1,0)) AS Count_farmer_survey_hh
                , SUM(IF(hh.`Score` IS NOT NULL,hh.`1.25/day`,0)) AS Total_125_index
                , SUM(IF(hh.`Score` IS NOT NULL,hh.`2.5/day`,0)) AS Total_25_index
                , IFNULL(SUM(IF(m.HandphoneType = '1',1,0)),0) AS Hp_Smart
                , IFNULL(SUM(IF(m.HandphoneType = '1' OR m.`AccessToSmartphone`='1',1,0)),0) AS Hp_Smart_Access
                , IFNULL(SUM(IF(m.HandphoneType = '2',1,0)),0) AS Hp_Feature
                , IFNULL(SUM(IF(m.HandphoneType = '3' OR m.HandphoneType IS NULL OR m.HandphoneType = '0',1,0)),0) AS Hp_NoHp
                , NOW() AS DateGenerated
            FROM
                ktv_members m
                JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID
                JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                LEFT JOIN (
                    SELECT
                        sub_a.`MemberID`, MAX(sub_a.`SurveyNr`) AS SurveyNr
                    FROM
                        ktv_survey_household sub_a
                    WHERE
                        sub_a.`StatusCode` = 'active'
                    GROUP BY sub_a.`MemberID`
                ) AS hh_latest ON 1=1
                    AND hh_latest.MemberID = m.`MemberID`
                LEFT JOIN ktv_survey_household hh ON 1=1
                    AND hh_latest.MemberID = hh.`MemberID`
                    AND hh_latest.SurveyNr = hh.`SurveyNr`
                LEFT JOIN (
                    SELECT
                        sub_b.MemberID
                        , COUNT(sub_b.FamLabID) AS Jumlah_HH
                    FROM
                        ktv_member_family_labour sub_b
                    WHERE
                        sub_b.StatusCode = 'active'
                    GROUP BY sub_b.MemberID
                ) AS tabel_family ON m.MemberID = tabel_family.MemberID
            WHERE
                m.`StatusCode` = 'active'
                AND
                pp.`StatusCode` = 'active'
                AND
                pp.IsGenDashboard = 'Yes'
                AND 
                m.`VillageID` IS NOT NULL
                AND
                m.VillageID != 0
            GROUP BY p.ProvinceID
                    , d.DistrictID
                    , sd.SubDistrictID
                    , v.VillageID
                    , pp.PartnerID
            ORDER BY m.VillageID";

        $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function getDisplayDemographic($ProvinceID,$DistrictID){
        $sqlcHakAkses = "";
        $sqlHakAkses = "";
        $sqlHakAksesPartnerGroup = "";
        //data display langsung ================================================== (begin)
        if($ProvinceID != ""){
            $sqldWherePropinsi = " AND kp.ProvinceID = '$ProvinceID' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrict = " AND ksd.DistrictID = '$DistrictID' ";
        }else{
            $sqldWhereDistrict = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = " AND a.PartnerID = '1' #Partner Koltiva ";
            $sqlHakAksesPartnerGroup .= " AND apm.apmPartnerID = '1'";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAksesPartner .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
            $sqlHakAksesPartnerGroup .= " AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAksesPartner .= " AND a.PartnerID = '1' #Partner Koltiva ";
            $sqlHakAksesPartnerGroup .= " AND apm.apmPartnerID = '1'";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.Total_Farmer) AS Total_Farmer
                , (SUM(a.Male_Farmer) / SUM(a.Total_Farmer)) * 100 AS Male_Farmer
                , (SUM(a.`Female_Farmer`) / SUM(a.Total_Farmer)) * 100 AS Female_Farmer
                , SUM(a.`Sum_Age`) / SUM(a.Total_Farmer_Age_Divider) AS Average_Age
                , SUM(a.Completed_Primary_School) / SUM(a.Completed_Primary_School_Pembagi) * 100 AS Completed_Primary_School
                , SUM(a.Below_35_Age) / SUM(a.Total_Farmer_Age_Divider) * 100 AS Below_35_Age
                , SUM(a.Sum_HH_Members) / SUM(a.Total_Farmer) AS Average_HH_Members
                , COUNT(DISTINCT kp.ProvinceID) AS Province
                , COUNT(DISTINCT kd.DistrictID) AS District
                , COUNT(DISTINCT ksd.SubDistrictID) AS SubDistrict
                , COUNT(DISTINCT kv.VillageID) AS Village
                , SUM(Total_125_index) / SUM(Count_farmer_survey_hh) AS Total_125_index
                , SUM(Total_25_index) / SUM(Count_farmer_survey_hh) AS Total_25_index

                , IFNULL(SUM(a.Hp_Smart),0) AS Hp_Smart
                , IFNULL(SUM(a.Hp_Smart_Access),0) AS Hp_Smart_Access
                , IFNULL(SUM(a.Hp_Feature),0) AS Hp_Feature
                , IFNULL(SUM(a.Hp_NoHp),0) AS Hp_NoHp

                , DateGenerated
            FROM
                dash_det_demographic a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
                $sqlHakAksesPartner
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();

        $sql_group = "
        SELECT
            IFNULL(COUNT(DISTINCT IF(a.inGroup = 1, a.groupName, NULL)),0) AS Farmer_Group
            , IFNULL(COUNT(DISTINCT IF(a.inCoop = 1, a.CoopName, NULL)),0) AS Cooperative
            , IFNULL(COUNT(DISTINCT IF(a.inGapoktan = 1, a.GapoktanName, NULL)),0) AS Gapoktan
        FROM ktv_members a
            LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            LEFT JOIN ktv_access_partner_member apm on apm.apmMemberID = a.MemberID
        WHERE
            a.`StatusCode` = 'active'
            $sqldWherePropinsi
            $sqldWhereDistrict
            $sqlHakAkses
            $sqlHakAksesPartnerGroup

        ";
        $query = $this->db->query($sql_group,array());
        $result['group'] = $query->row_array(0);
        $result['sql_group'] = $this->db->last_query();


        //data display langsung ================================================== (end)

        //data group by wilayah (untuk chart) ============================================= (begin)
        if($ProvinceID == ""){
            $sqlcLabel = "Province";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                        LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID";
            $sqlcWhere = "";
        } elseif($DistrictID == "") {
            $sqlcLabel = "District";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                        LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID";
            $sqlcWhere = "AND kd.ProvinceID = '$ProvinceID'";
        } else {
            $sqlcLabel = "SubDistrict";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                        LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID";
            $sqlcWhere = "AND ksd.DistrictID = '$DistrictID'";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAksesPartner = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND ksd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAksesPartner .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND ksd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAksesPartner .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                $sqlcLabel AS label
                , SUM(a.Total_Farmer) AS Total_Farmer
                , SUM(a.Total_Household_Count) AS Total_Household_Count
                , SUM(a.Male_Farmer) AS Male_Farmer
                , SUM(a.Female_Farmer) AS Female_Farmer
                , SUM(a.`Sum_Age`) / SUM(a.Total_Farmer_Age_Divider) AS Average_Age
                , SUM(a.Age_15to24) AS Age_15to24
                , SUM(a.Age_25to34) AS Age_25to34
                , SUM(a.Age_35to44) AS Age_35to44
                , SUM(a.Age_45to54) AS Age_45to54
                , SUM(a.Age_MoreThan55) AS Age_MoreThan55
                , SUM(a.Edu_NoEducation) AS Edu_NoEducation
                , SUM(a.Edu_PrimarySchoolIncompleted) AS Edu_PrimarySchoolIncompleted
                , SUM(a.Edu_PrimarySchoolCompleted) AS Edu_PrimarySchoolCompleted
                , SUM(a.Edu_GraduatedMiddleSchool) AS Edu_GraduatedMiddleSchool
                , SUM(a.Edu_GraduatedHighSchool) AS Edu_GraduatedHighSchool
                , SUM(a.Edu_GraduatedCollege) AS Edu_GraduatedCollege
                , SUM(a.MStatus_Married) AS MStatus_Married
                , SUM(a.MStatus_Single) AS MStatus_Single
                , SUM(a.MStatus_Widow) AS MStatus_Widow
                , SUM(a.Sum_HH_Members) / SUM(a.Total_Farmer) AS Average_HH_Members
                , SUM(Total_125_index) / SUM(Count_farmer_survey_hh) AS Ave_125_index
                , SUM(Total_25_index) / SUM(Count_farmer_survey_hh) AS Ave_25_index

                , IFNULL(SUM(a.Hp_Smart),0) AS Hp_Smart
                , IFNULL(SUM(a.Hp_Smart_Access),0) AS Hp_Smart_Access
                , IFNULL(SUM(a.Hp_Feature),0) AS Hp_Feature
                , IFNULL(SUM(a.Hp_NoHp),0) AS Hp_NoHp
            FROM
                dash_det_demographic a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
                $sqlcHakAksesPartner
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $result['dataChart'] = $query->result_array();

        $sql_group = "
        SELECT
            $sqlcLabel AS label
            , IFNULL(COUNT(DISTINCT IF(a.inGroup = 1, a.groupName, NULL)),0) AS Farmer_Group
            , IFNULL(COUNT(DISTINCT IF(a.inCoop = 1, a.CoopName, NULL)),0) AS Cooperative
            , IFNULL(COUNT(DISTINCT IF(a.inGapoktan = 1, a.GapoktanName, NULL)),0) AS Gapoktan
        FROM ktv_members a
        LEFT JOIN ktv_access_partner_member apm on apm.apmMemberID = a.MemberID
        $sqlcJoin
        WHERE
            a.StatusCode = 'active'
            $sqldWherePropinsi
            $sqldWhereDistrict
            $sqlHakAkses
            $sqlHakAksesPartnerGroup
        GROUP BY label
        HAVING label IS NOT NULL
        ";
        $query = $this->db->query($sql_group,array());
        $result['group']['detail'] = $query->result_array();
        $result['group']['sql'] = $this->db->last_query();
        //data group by wilayah (untuk chart) ============================================= (end)

        return $result;
    }

    public function generateDashAgentDemographic(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_agent_demographic');

        //ambil data village nya yg ada
        $sql="SELECT
                kp.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
            FROM
                ktv_members a
                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID IN (5,6,7,8,9,10) #ROLE AGENT
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` IS NOT NULL
                AND a.VillageID != ''
                AND a.VillageID != '0'
            GROUP BY a.`VillageID`";
        $query = $this->db->query($sql);
        $dataRegion = $query->result_array();

        //ambil data Partner yg ada
        $sql="SELECT
                a.`PartnerID`
                , a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartner = $query->result_array();

        for ($i=0; $i < count($dataRegion); $i++) {
            for ($j=0; $j < count($dataPartner); $j++) {

                $sql="
                INSERT INTO dash_det_agent_demographic
                SELECT
                    '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                    , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                    , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                    , '{$dataRegion[$i]['VillageID']}' AS `VillageID`
                    , '{$dataPartner[$j]['PartnerID']}' AS PartnerID

                    , COUNT(tbl_agent.`MemberID`) AS TotalAgent
                    , IFNULL(SUM(IF(tbl_agent.`Gender` = 'm',1,0)),0) AS TotalAgentMale
                    , IFNULL(SUM(IF(tbl_agent.`Gender` = 'f',1,0)),0) AS TotalAgentFemale

                    , IFNULL(SUM(tbl_staff.TotalAgentStaff),0) AS TotalAgentStaff
                    , IFNULL(SUM(tbl_staff.TotalAgentStaffMale),0) AS TotalAgentStaffMale
                    , IFNULL(SUM(tbl_staff.TotalAgentStaffFemale),0) AS TotalAgentStaffFemale

                    , IFNULL(SUM(FLOOR(DATEDIFF(CURDATE(), tbl_agent.DateOfBirth) / 365.25)),0) AS SumAgeAgent
                    , IFNULL(SUM(IF((tbl_agent.`DateOfBirth` IS NOT NULL) AND (tbl_agent.`DateOfBirth` != '0000-00-00'),1,0)),0) AS AgeAgentDivider

                    , IFNULL(SUM(IF(tbl_agent.Education > 2,1,0)),0) AS GraduatedPrimarySchoolAgent
                    , IFNULL(SUM(IF(tbl_agent.Education IS NOT NULL,1,0)),0) AS GraduatedPrimarySchoolAgentDivider

                    , IFNULL(SUM(tbl_staff.SumAgeAgentStaff),0) AS SumAgeAgentStaff
                    , IFNULL(SUM(tbl_staff.AgeAgentStaffDivier),0) AS AgeAgentStaffDivier

                    , IFNULL(COUNT(veh.`VehID`),0) AS AgentVehicle
                    , IFNULL(COUNT(DISTINCT veh.MemberID),0) AS AgentVehicleDivider

                    , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), tbl_agent.DateOfBirth) / 365.25) BETWEEN 15 AND 24,1,0)),0) AS AgentAge15To24
                    , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), tbl_agent.DateOfBirth) / 365.25) BETWEEN 25 AND 34,1,0)),0) AS AgentAge25To34
                    , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), tbl_agent.DateOfBirth) / 365.25) BETWEEN 35 AND 44,1,0)),0) AS AgentAge35To44
                    , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), tbl_agent.DateOfBirth) / 365.25) BETWEEN 45 AND 54,1,0)),0) AS AgentAge45To54
                    , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), tbl_agent.DateOfBirth) / 365.25) > 54,1,0)),0) AS AgentAge55More

                    , IFNULL(SUM(tbl_staff.AgentStaffAge15To24),0) AS AgentStaffAge15To24
                    , IFNULL(SUM(tbl_staff.AgentStaffAge25To34),0) AS AgentStaffAge25To34
                    , IFNULL(SUM(tbl_staff.AgentStaffAge35To44),0) AS AgentStaffAge35To44
                    , IFNULL(SUM(tbl_staff.AgentStaffAge45To54),0) AS AgentStaffAge45To54
                    , IFNULL(SUM(tbl_staff.AgentStaffAge55More),0) AS AgentStaffAge55More

                    , NOW() AS DateGenerated
                FROM
                    (
                        SELECT
                            sa.`MemberID`
                            , sa.`VillageID`
                            , sa.`Gender`
                            , sa.DateOfBirth
                            , sa.Education
                        FROM
                            ktv_members sa
                            JOIN ktv_member_role sa_role ON sa.MemberID = sa_role.MemberID AND sa_role.MRoleID IN (5,6,7,8,9,10) #ROLE AGENT
                        WHERE
                            sa.`StatusCode` = 'active'
                            AND sa.VillageID = '{$dataRegion[$i]['VillageID']}'
                        GROUP BY sa.`MemberID`
                    ) AS tbl_agent
                    INNER JOIN ktv_access_partner_member acc_pm ON tbl_agent.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'

                    LEFT JOIN ktv_member_vehicle veh ON tbl_agent.MemberID = veh.`MemberID`

                    LEFT JOIN (
                        SELECT
                            suba.`ObjID` AS MemberID
                            , COUNT(suba.`StaffID`) AS TotalAgentStaff
                            , IFNULL(SUM(IF(subb.`Gender` = 'm',1,0)),0) AS TotalAgentStaffMale
                            , IFNULL(SUM(IF(subb.`Gender` = 'f',1,0)),0) AS TotalAgentStaffFemale

                            , IFNULL(SUM(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25)),0) AS SumAgeAgentStaff
                            , IFNULL(SUM(IF((subb.`BirthDate` IS NOT NULL) AND (subb.`BirthDate` != '0000-00-00'),1,0)),0) AS AgeAgentStaffDivier

                            , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 15 AND 24,1,0)),0) AS AgentStaffAge15To24
                            , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 25 AND 34,1,0)),0) AS AgentStaffAge25To34
                            , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 35 AND 44,1,0)),0) AS AgentStaffAge35To44
                            , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 45 AND 54,1,0)),0) AS AgentStaffAge45To54
                            , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) > 54,1,0)),0) AS AgentStaffAge55More
                        FROM
                            ktv_staffs suba
                            INNER JOIN ktv_persons subb ON 1=1
                                AND suba.`PersonID` = subb.`PersonID`
                        WHERE 1=1
                            AND suba.`StatusCode` = 'active'
                            AND subb.`StatusCd` = 'active'
                            AND suba.`ObjType` = 'agent'
                        GROUP BY suba.`ObjID`
                    ) AS tbl_staff ON 1=1
                        AND tbl_agent.`MemberID` = tbl_staff.MemberID
                ";
                $query = $this->db->query($sql);

            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function generateDashAgentDemographicOptimize(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_agent_demographic');

        $sql="INSERT INTO dash_det_agent_demographic
                SELECT
                   p.ProvinceID AS ProvinceID
                   , d.DistrictID AS DistrictID
                   , sd.SubDistrictID AS SubDistrictID
                   , v.VillageID AS VillageID
                   , pp.PartnerID AS PartnerID

                   , COUNT(m.`MemberID`) AS TotalAgent
                   , IFNULL(SUM(IF(m.`Gender` = 'm',1,0)),0) AS TotalAgentMale
                   , IFNULL(SUM(IF(m.`Gender` = 'f',1,0)),0) AS TotalAgentFemale

                   , IFNULL(SUM(tbl_staff.TotalAgentStaff),0) AS TotalAgentStaff
                   , IFNULL(SUM(tbl_staff.TotalAgentStaffMale),0) AS TotalAgentStaffMale
                   , IFNULL(SUM(tbl_staff.TotalAgentStaffFemale),0) AS TotalAgentStaffFemale

                   , IFNULL(SUM(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25)),0) AS SumAgeAgent
                   , IFNULL(SUM(IF((m.`DateOfBirth` IS NOT NULL) AND (m.`DateOfBirth` != '0000-00-00'),1,0)),0) AS AgeAgentDivider

                   , IFNULL(SUM(IF(m.Education > 2,1,0)),0) AS GraduatedPrimarySchoolAgent
                   , IFNULL(SUM(IF(m.Education IS NOT NULL,1,0)),0) AS GraduatedPrimarySchoolAgentDivider

                   , IFNULL(SUM(tbl_staff.SumAgeAgentStaff),0) AS SumAgeAgentStaff
                   , IFNULL(SUM(tbl_staff.AgeAgentStaffDivier),0) AS AgeAgentStaffDivier

                   , IFNULL(SUM(veh.`AgentVehicle`),0) AS AgentVehicle
                   , IFNULL(COUNT(m.`MemberID`),0) AS AgentVehicleDivider

                   , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 15 AND 24,1,0)),0) AS AgentAge15To24
                   , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 25 AND 34,1,0)),0) AS AgentAge25To34
                   , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 35 AND 44,1,0)),0) AS AgentAge35To44
                   , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) BETWEEN 45 AND 54,1,0)),0) AS AgentAge45To54
                   , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), m.DateOfBirth) / 365.25) > 54,1,0)),0) AS AgentAge55More

                   , IFNULL(SUM(tbl_staff.AgentStaffAge15To24),0) AS AgentStaffAge15To24
                   , IFNULL(SUM(tbl_staff.AgentStaffAge25To34),0) AS AgentStaffAge25To34
                   , IFNULL(SUM(tbl_staff.AgentStaffAge35To44),0) AS AgentStaffAge35To44
                   , IFNULL(SUM(tbl_staff.AgentStaffAge45To54),0) AS AgentStaffAge45To54
                   , IFNULL(SUM(tbl_staff.AgentStaffAge55More),0) AS AgentStaffAge55More

                   , NOW() AS DateGenerated
                FROM
                   (
                        SELECT
                            sa.`MemberID`
                            , sa.`VillageID`
                            , sa.`Gender`
                            , sa.DateOfBirth
                            , sa.Education
                            , sa.StatusCode
                        FROM
                            ktv_members sa
                            JOIN ktv_member_role sa_role ON sa.MemberID = sa_role.MemberID AND sa_role.MRoleID IN (5,6,7,8,9,10,12,13,14) #ROLE AGENT
                        WHERE
                            sa.`StatusCode` = 'active'
                        GROUP BY sa.`MemberID`
                    ) AS m
                   LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
                   LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                   LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                   LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                   JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                   JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                   LEFT JOIN (
						SELECT
							sveh.`MemberID`
							, COUNT(sveh.`VehID`) AS AgentVehicle
						FROM
							ktv_member_vehicle sveh
						WHERE 1=1
							AND sveh.`StatusCode` = 'active'
						GROUP BY sveh.`MemberID`
					) AS veh ON m.MemberID = veh.`MemberID`
                   LEFT JOIN (
                      SELECT
                          suba.`ObjID` AS MemberID
                          , COUNT(suba.`StaffID`) AS TotalAgentStaff
                          , IFNULL(SUM(IF(subb.`Gender` = 'm',1,0)),0) AS TotalAgentStaffMale
                          , IFNULL(SUM(IF(subb.`Gender` = 'f',1,0)),0) AS TotalAgentStaffFemale

                          , IFNULL(SUM(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25)),0) AS SumAgeAgentStaff
                          , IFNULL(SUM(IF((subb.`BirthDate` IS NOT NULL) AND (subb.`BirthDate` != '0000-00-00'),1,0)),0) AS AgeAgentStaffDivier

                          , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 15 AND 24,1,0)),0) AS AgentStaffAge15To24
                          , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 25 AND 34,1,0)),0) AS AgentStaffAge25To34
                          , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 35 AND 44,1,0)),0) AS AgentStaffAge35To44
                          , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) BETWEEN 45 AND 54,1,0)),0) AS AgentStaffAge45To54
                          , IFNULL(SUM(IF(FLOOR(DATEDIFF(CURDATE(), subb.BirthDate) / 365.25) > 54,1,0)),0) AS AgentStaffAge55More
                      FROM
                          ktv_staffs suba
                          INNER JOIN ktv_persons subb ON 1=1
                              AND suba.`PersonID` = subb.`PersonID`
                      WHERE 1=1
                          AND suba.`StatusCode` = 'active'
                          AND subb.`StatusCd` = 'active'
                          AND suba.`ObjType` = 'agent'
                      GROUP BY suba.`ObjID`
                   ) AS tbl_staff ON 1=1
                      AND m.`MemberID` = tbl_staff.MemberID
                WHERE
                   m.`VillageID` IS NOT NULL
                   AND 
                   m.VillageID != ''
                   AND 
                   m.VillageID != '0'
                   AND
                   pp.`StatusCode` = 'active'
                   AND
                   pp.IsGenDashboard = 'Yes'
                GROUP BY
                  p.ProvinceID
                  , d.DistrictID
                  , sd.SubDistrictID
                  , v.VillageID
                  , pp.PartnerID
                ORDER BY m.VillageID";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function getDisplayAgentDemographic($ProvinceID,$DistrictID){
        //data display langsung ================================================== (begin)
        if($ProvinceID != ""){
            $sqldWherePropinsi = " AND kd.ProvinceID = '$ProvinceID' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrict = " AND kd.DistrictID = '$DistrictID' ";
        }else{
            $sqldWhereDistrict = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.TotalAgent) AS TotalAgent
                , (SUM(a.`TotalAgentFemale`) / SUM(a.TotalAgent)) * 100 AS TotalAgentFemale

                , SUM(a.TotalAgentStaff) AS TotalAgentStaff
                , (SUM(a.`TotalAgentStaffFemale`) / SUM(a.TotalAgentStaff)) * 100 AS TotalAgentStaffFemale

                , SUM(a.`SumAgeAgent`) / SUM(a.AgeAgentDivider) AS AvgAgeAgent

                , (SUM(a.`GraduatedPrimarySchoolAgent`) / SUM(a.GraduatedPrimarySchoolAgentDivider)) * 100 AS GraduatedPrimarySchoolAgent

                , SUM(a.`SumAgeAgentStaff`) / SUM(a.AgeAgentStaffDivier) AS AvgAgeAgentStaff

                , SUM(a.AgentVehicle) / SUM(a.AgentVehicleDivider) AS AvgAgentVehicle

                , DateGenerated
            FROM
                dash_det_agent_demographic a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();

        //data group by wilayah (untuk chart) ============================================= (begin)
        if($ProvinceID == ""){
            $sqlcLabel = "Province";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                        LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID";
            $sqlcWhere = "";
        } elseif($DistrictID == "") {
            $sqlcLabel = "District";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID";
            $sqlcWhere = "AND kd.ProvinceID = '$ProvinceID'";
        } else {
            $sqlcLabel = "SubDistrict";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID";
            $sqlcWhere = "AND ksd.DistrictID = '$DistrictID'";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND ksd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND ksd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                $sqlcLabel AS label

                , IFNULL(SUM(a.TotalAgent),0) AS TotalAgent
                , IFNULL(SUM(a.TotalAgentMale),0) AS TotalAgentMale
                , IFNULL(SUM(a.TotalAgentFemale),0) AS TotalAgentFemale

                , IFNULL(SUM(a.TotalAgentStaff),0) AS TotalAgentStaff
                , IFNULL(SUM(a.TotalAgentStaffMale),0) AS TotalAgentStaffMale
                , IFNULL(SUM(a.TotalAgentStaffFemale),0) AS TotalAgentStaffFemale

                , SUM(a.`SumAgeAgent`) / SUM(a.AgeAgentDivider) AS AvgAgeAgent
                , SUM(a.`SumAgeAgentStaff`) / SUM(a.AgeAgentStaffDivier) AS AvgAgeAgentStaff

                , SUM(AgentAge15To24) AS AgentAge15To24
                , SUM(AgentAge25To34) AS AgentAge25To34
                , SUM(AgentAge35To44) AS AgentAge35To44
                , SUM(AgentAge45To54) AS AgentAge45To54
                , SUM(AgentAge55More) AS AgentAge55More

                , SUM(AgentStaffAge15To24) AS AgentStaffAge15To24
                , SUM(AgentStaffAge25To34) AS AgentStaffAge25To34
                , SUM(AgentStaffAge35To44) AS AgentStaffAge35To44
                , SUM(AgentStaffAge45To54) AS AgentStaffAge45To54
                , SUM(AgentStaffAge55More) AS AgentStaffAge55More

                , SUM(a.AgentVehicle) AS AgentVehicle

            FROM
                dash_det_agent_demographic a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $result['dataChart'] = $query->result_array();
        //data group by wilayah (untuk chart) ============================================= (end)


        return $result;
    }
}
?>