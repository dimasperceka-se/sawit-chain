<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:33:31
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmill extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('awsfileupload');
    }

    public function get_supplier_detail($PID){
        $sql = "SELECT
                    a.MemberDisplayID SupplierID
                    , IFNULL(me.agCompanyName, a.MemberName) SupplierName
                    , IFNULL(a.Alias,'-') Alias
                    , IFNULL(msc.SuratNr,'-') SPBCode
                    , IF(rmr.MRoleID = 5 OR rmr.MRoleID = 6 OR rmr.MRoleID = 7 OR rmr.MRoleID = 8 OR rmr.MRoleID = 9 OR rmr.MRoleID = 10 OR rmr.MRoleID = 13, 
                        'Agent/Dealer/Vendor',rmr.MRoleName) SupplierType
                    , v.Village
                    , sd.SubDistrict
                    , d.District
                    , IFNULL(a.Latitude, '-') Latitude
                    , IFNULL(a.Longitude, '-') Longitude
                    , IFNULL(sf.TotalFarmer, '-') TotalFarmer
                    , IFNULL(sf.LuasKebun,'-') LuasKebun
                    , IFNULL(sf.TotalKebun,'-') TotalKebun
                    FROM
                        ktv_members a
                        LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                        LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                        LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                        LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                        LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                        LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                        LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                        LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                        LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                        LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                        LEFT JOIN (
                            SELECT
                                COUNT(DISTINCT sf.FarmerID) TotalFarmer
                                , sf.SupplychainID
                                , SUM(sp.GardenAreaHa) LuasKebun
                                , COUNT(sp.MemberID) TotalKebun
                            FROM
                                ktv_tc_supplychain_farmer sf
                            LEFT JOIN
                                ktv_survey_plot sp on sp.MemberID = sf.FarmerID
                            GROUP BY
                                sf.SupplychainID
                        ) sf on sf.SupplychainID = o.SupplychainID
                    WHERE
                        op.PartnerID = ?
                        AND o.ObjType = 'agent' 
                        AND a.StatusCode = 'active'
                        AND mr.MRoleID IN (5,6,7,8,9,10,11,12,13,14)
                    GROUP BY a.MemberID";
        $query = $this->db->query($sql,array($PID));

        return $query->result_array();
    }

    public function get_category_traceability($PID,$year){
        
        $sql = "SELECT
                COUNT(mt.MillTCID) jml_supplier
                , mt.SourceCategory MRoleID
                , CASE
                    WHEN mt.SourceCategory = 1 THEN 'Plasma'
                    WHEN mt.SourceCategory = 2 THEN 'Direct Smallholder'
                    WHEN mt.SourceCategory = 3 THEN 'Agent/Dealer/Vendor'
                    WHEN mt.SourceCategory = 4 THEN 'Owned Estate'
                    WHEN mt.SourceCategory = 5 THEN 'External Estate'
                    ELSE '-'
                    END MRoleNames
                , (SUM(mt.TCPercentage)/COUNT(mt.MillTCID)) TCPercentage
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = mt.ApprovedBy LIMIT 1) AS Enumerator
            FROM
                ktv_mill_tc mt
            LEFT JOIN
                ktv_mill m on m.MillID = mt.MillID
            WHERE
                m.PartnerID = '$PID'
            -- AND
            --     YEAR(mt.SupplyBatchDate) = '$year'
            AND
                (mt.ApprovedBy IS NOT NULL OR mt.ApprovedBy <> '')
            GROUP BY
                mt.SourceCategory";
        $query = $this->db->query($sql);

        $jml_kebun_total = 0;
        $jml_supplier_total = 0;
        $ttp = 0;
        $approvedby = '';

        $result = array();
        if($query->num_rows()>0){
            $result = $query->result_array();
            foreach($result as $key => $row){
                $ttp  += $row["TCPercentage"];

                $result[$key]['MRoleNames'] = lang($row["MRoleNames"]);
                $result[$key]['jml_supplier'] = $row["jml_supplier"];
                $result[$key]['ttp'] = number_format($row["TCPercentage"],2);
                $approvedby = $row["Enumerator"];
            }
            $ttp = ($ttp/5);
        }
        $ttp_total = number_format($ttp,2);
        
        return array($result,$ttp_total,$approvedby);
    }
    
    public function getTTPSupplier(){
        return 0;
    }

    public function getSupTransaction($PID,$start,$end){
        $sql = "SELECT
                GROUP_CONCAT(MRoleID) MRoleID
                , IF(MRoleID >10, MRoleName, 'Agent') MRoleNames
            FROM
                `ktv_ref_member_role` mr
            WHERE
                mr.MRoleID IN (5, 6, 7, 8, 9, 10, 11,12,13,14)
            GROUP BY MRoleNames
            ORDER BY MRoleNames";
        $query = $this->db->query($sql);

        $result = array();
        $result_grafik = array();
        if($query->num_rows()>0){
            $result = $query->result_array();
            foreach($result as $key => $row){
                $arr_val = $this->getSupTransactionVal($row['MRoleID'],$PID,$start,$end);

                $result[$key]['MRoleNames']     = lang($row["MRoleNames"]);
                $result[$key]['JmlTransaksi']   = number_format( $arr_val[0],0);
                $result[$key]['JmlKebun']       = number_format( $arr_val[1],0);
                $result[$key]['LuasKebun']      = number_format( $arr_val[2],0);
                $result[$key]['Tonase']         = number_format( $arr_val[3],0);

                $result_grafik[$key]["name"] = lang($row["MRoleNames"]);
                $result_grafik[$key]["data"] = $this->getSupTransactionValGrafik($row['MRoleID'],$PID,$start,$end);
            }
        }
        $arr_val_direct = $this->getSupTransactionDirectVal($row['MRoleID'],$PID,$start,$end);
        $direct = array(
            "MroleID" => "direct",
            "MRoleNames" => lang("Direct Smallholder"),
            "JmlTransaksi" => $arr_val_direct[0],
            "JmlKebun" => $arr_val_direct[1],
            "LuasKebun" => $arr_val_direct[2],
            "Tonase" => number_format( $arr_val_direct[3],0),
        );

        array_push($result,$direct);

        //Grafik
        $direct_grafik = array(
            "name" => lang("Direct Smallholder"),
            "data" => $this->getSupTransactionDirectValGrafik('direct',$PID,$start,$end)
        );

        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $konten[date('n', $timestamp)] = date('F', $timestamp);
        }
        $header = array();
        for($k = 0;$k<count($konten);$k++){
            $datestart = $start."-".date("m",strtotime($konten[$k+1]))."-01";
            $dateend = date('Y-m-t', strtotime($datestart));
            array_push($header,$konten[$k+1]);
        }
        array_push($result_grafik,$direct_grafik);

        return array(
            "transaction"=> $result,
            "grafik" => $result_grafik,
            "header" => $header
        );
    }

    public function getSupTransactionDirectValGrafik($roleID,$PID,$start,$end){
        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $konten[date('n', $timestamp)] = date('F', $timestamp);
        }
        $supplyvolume = array();
        for($k = 0;$k<count($konten);$k++){
            $datestart = $start."-".date("m",strtotime($konten[$k+1]))."-01";
            $dateend = date('Y-m-t', strtotime($datestart));

            $sql = "SELECT
                count(
                DISTINCT ( a.SupplyID )) SumSupplyBase,
                count(a.PlantationNr) SumPlantationBase,
                count( a.SupplyID ) SumSupplyTransaction,
                count(
                    DISTINCT (
                    CONCAT( a.SupplyID, a.PlantationNr ))) SumSupplyPlot,
                IFNULL(SUM( a.VolumeNetto ),0) SumSupplyVolume,
                IFNULL(SUM(ps.GardenAreaHa),0) SumGardenHA
            FROM
                `ktv_tc_supplychain_transaction` a
                LEFT JOIN ref_tc_supplybase_category b ON b.SupplybaseCategoryID = a.SupplybaseCategoryID
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = a.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = a.SupplychainID
                LEFT JOIN ktv_members m on m.MemberID = vso.ObjID
                INNER JOIN ktv_survey_plot ps on ps.MemberID = m.MemberID AND ps.PlotNr = a.PlantationNr
            WHERE
                1 = 1
                AND vso.PartnerID = ?
                AND vso.ObjType = 'mill'
                AND a.DateTransaction >= '$datestart' 
                AND a.DateTransaction <= '$dateend'
                AND m.SupplybaseType = 'direct'
                AND a.SupplychainID IS NOT NULL";
            $query = $this->db->query($sql,array($PID));
            if($query->num_rows()>0){
                array_push($supplyvolume,(float)$query->row()->SumSupplyVolume);
            }else{
                array_push($supplyvolume,0);
            }
        }

        return $supplyvolume;
    }

    public function getSupTransactionDirectVal($roleID,$PID,$start,$end){
        $sql = "SELECT
            count(
            DISTINCT ( a.SupplyID )) SumSupplyBase,
            count(a.PlantationNr) SumPlantationBase,
            count( a.SupplyID ) SumSupplyTransaction,
            count(
                DISTINCT (
                CONCAT( a.SupplyID, a.PlantationNr ))) SumSupplyPlot,
            SUM( a.VolumeNetto ) SumSupplyVolume,
            IFNULL(SUM(ps.GardenAreaHa),0) SumGardenHA
        FROM
            `ktv_tc_supplychain_transaction` a
            LEFT JOIN ref_tc_supplybase_category b ON b.SupplybaseCategoryID = a.SupplybaseCategoryID
            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = a.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = a.SupplychainID
            LEFT JOIN ktv_members m on m.MemberID = vso.ObjID
            INNER JOIN ktv_survey_plot ps on ps.MemberID = m.MemberID AND ps.PlotNr = a.PlantationNr
        WHERE
            1 = 1
            AND vso.PartnerID = ?
            AND vso.ObjType = 'mill'
            AND YEAR ( a.DateTransaction ) >= '$start' 
            AND YEAR ( a.DateTransaction ) <= '$end'
            AND m.SupplybaseType = 'direct'
            AND a.SupplychainID IS NOT NULL";
        $query = $this->db->query($sql,array($PID));
        if($query->num_rows()>0){
            return array(
                $query->row()->SumSupplyBase
                , $query->row()->SumSupplyPlot
                ,$query->row()->SumGardenHA
                ,$query->row()->SumSupplyVolume
            );
        }
        return array(0,0,0);
    }

    public function getSupTransactionVal($roleID,$PID,$start,$end){
        // $sql = "SELECT
        //         count(
        //         DISTINCT ( a.SupplyID )) SumSupplyBase,
        //         count(a.PlantationNr) SumPlantationBase,
        //         count( a.SupplyID ) SumSupplyTransaction,
        //         count(
        //             DISTINCT (
        //             CONCAT( a.SupplyID, a.PlantationNr ))) SumSupplyPlot,
        //         SUM(ps.GardenAreaHa) SumGardenHA,
        //         SUM( a.VolumeNetto ) SumSupplyVolume
        //     FROM
        //         `ktv_tc_supplychain_transaction` a
        //         LEFT JOIN ref_tc_supplybase_category b ON b.SupplybaseCategoryID = a.SupplybaseCategoryID
        //         LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = a.SupplyBatchID
        //         LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = sb.SupplyDestOrgID
        //         LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = a.SupplychainID
        //         LEFT JOIN ktv_members m on m.MemberID = vso.ObjID
        //         LEFT JOIN ktv_member_role mr on mr.MemberID = m.MemberID
        //         LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
        //         INNER JOIN ktv_survey_plot_sme ps on ps.MemberID = m.MemberID AND ps.PlotNr = a.PlantationNr
        //     WHERE
        //         1 = 1
        //         AND vso2.PartnerID = ?
        //         AND vso2.ObjType = 'mill'
        //         AND YEAR ( a.DateTransaction ) >= '$start' 
        //         AND YEAR ( a.DateTransaction ) <= '$end' 
        //         AND mr.MRoleID IN ($roleID)";
        $sql = "SELECT
        --  a.SupplybaseCategoryID,
        --  b.CategoryName,
            count(
            DISTINCT ( a.SupplychainID )) SumSupplyBase,
            count( DISTINCT a.SupplyTransID ) SumSupplyTransaction,
            count(
                DISTINCT (
                CONCAT( a.SupplyID, a.PlantationNr ))) SumSupplyPlot,
            SUM(a.VolumeNetto)*count(DISTINCT a.SupplyTransID)/count(*) SumSupplyVolume,
            SUM(ps.GardenAreaHa)*count(DISTINCT a.SupplyTransID)/count(*) SumGardenHA
        --  a.SupplychainID
        --  , m.MemberName
        --  , a.InvoiceNumber
        FROM
            `ktv_tc_supplychain_transaction` a
            LEFT JOIN ref_tc_supplybase_category b ON b.SupplybaseCategoryID = a.SupplybaseCategoryID
            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = a.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( a.SupplychainID, sb.SupplyOrgID ) 
            LEFT JOIN ktv_members m on m.MemberID = vso.ObjID
            LEFT JOIN ktv_member_role mr on mr.MemberID = m.MemberID
            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
            LEFT JOIN ktv_survey_plot_sme ps on ps.MemberID = m.MemberID AND ps.PlotNr = a.PlantationNr
        WHERE
            1 = 1 
            AND vso.PartnerID = ?
            AND YEAR(a.DateTransaction) >= '$start'
            AND YEAR(a.DateTransaction) <= '$start'
            AND mr.MRoleID IN ($roleID)";
        $query = $this->db->query($sql,array($PID));
        if($query->num_rows()>0){
            return array(
                $query->row()->SumSupplyBase
                , $query->row()->SumSupplyPlot
                ,$query->row()->SumGardenHA
                ,$query->row()->SumSupplyVolume
            );
        }
        return array(0,0,0);
    }    

    public function getSupTransactionValGrafik($roleID,$PID,$start,$end){
        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $konten[date('n', $timestamp)] = date('F', $timestamp);
        }
        $supplyvolume = array();
        for($k = 0;$k<count($konten);$k++){
            $datestart = $start."-".date("m",strtotime($konten[$k+1]))."-01";
            $dateend = date('Y-m-t', strtotime($datestart));
            // $sql = "SELECT
            //         count(
            //         DISTINCT ( a.SupplyID )) SumSupplyBase,
            //         count(a.PlantationNr) SumPlantationBase,
            //         count( a.SupplyID ) SumSupplyTransaction,
            //         count(
            //             DISTINCT (
            //             CONCAT( a.SupplyID, a.PlantationNr ))) SumSupplyPlot,
            //         IFNULL(SUM( a.VolumeNetto ),0) SumSupplyVolume,
            //         SUM(ps.GardenAreaHa) SumGardenHA
            //     FROM
            //         `ktv_tc_supplychain_transaction` a
            //         LEFT JOIN ref_tc_supplybase_category b ON b.SupplybaseCategoryID = a.SupplybaseCategoryID
            //         LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = a.SupplyBatchID
            //         LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = sb.SupplyDestOrgID
            //         LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = a.SupplychainID
            //         LEFT JOIN ktv_members m on m.MemberID = vso.ObjID
            //         LEFT JOIN ktv_member_role mr on mr.MemberID = m.MemberID
            //         LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
            //         INNER JOIN ktv_survey_plot_sme ps on ps.MemberID = m.MemberID AND ps.PlotNr = a.PlantationNr
            //     WHERE
            //         1 = 1
            //         AND vso2.PartnerID = ?
            //         AND vso2.ObjType = 'mill'
            //         AND a.DateTransaction >= '$datestart' 
            //         AND a.DateTransaction <= '$dateend' 
            //         AND mr.MRoleID IN ($roleID)";
            $sql = "SELECT
            --  a.SupplybaseCategoryID,
            --  b.CategoryName,
                count(
                DISTINCT ( a.SupplychainID )) SumSupplyBase,
                count( DISTINCT a.SupplyTransID ) SumSupplyTransaction,
                count(
                    DISTINCT (
                    CONCAT( a.SupplyID, a.PlantationNr ))) SumSupplyPlot,
                SUM(a.VolumeNetto)*count(DISTINCT a.SupplyTransID)/count(*) SumSupplyVolume,
                SUM(ps.GardenAreaHa)*count(DISTINCT a.SupplyTransID)/count(*) SumGardenHA
            --  a.SupplychainID
            --  , m.MemberName
            --  , a.InvoiceNumber
            FROM
                `ktv_tc_supplychain_transaction` a
                LEFT JOIN ref_tc_supplybase_category b ON b.SupplybaseCategoryID = a.SupplybaseCategoryID
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = a.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( a.SupplychainID, sb.SupplyOrgID ) 
                LEFT JOIN ktv_members m on m.MemberID = vso.ObjID
                LEFT JOIN ktv_member_role mr on mr.MemberID = m.MemberID
                LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                LEFT JOIN ktv_survey_plot_sme ps on ps.MemberID = m.MemberID AND ps.PlotNr = a.PlantationNr
            WHERE
                1 = 1 
                AND vso.PartnerID = ?
                AND a.DateTransaction >= '$datestart'
                AND a.DateTransaction <= '$dateend'
                AND mr.MRoleID IN ($roleID)";
            $query = $this->db->query($sql,array($PID));
            if($query->num_rows()>0){
                array_push($supplyvolume,(float)$query->row()->SumSupplyVolume);
            }else{
                array_push($supplyvolume,0);
            }
        }

        return $supplyvolume;
    }

    public function getMillBasicDataFormProfile($PID){
        if($_SESSION['role'] == "Mill"){
            $where = " AND b.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            $where = " AND a.PartnerID = '$PID'";
        }

        $sql="SELECT
                a.`MillID`,
                a.`MillID` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillID\",
                a.`MillDisplayID` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillDisplayID\",
                a.`MillName` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillName\",
                a.`CompanyName` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-CompanyName\",
                a.`MillGroupID` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillGroup\",
                a.`Address` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Address\",
                SUBSTR(a.`VillageID`,1,2) AS \"Province\",
                SUBSTR(a.`VillageID`,1,4) AS \"District\",
                SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\",
                a.`VillageID` AS \"Village\",
                a.`Status` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Status\",
                a.`Year` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Year\",
                a.`Alias` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Alias\",
                a.`Phone` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Phone\",
                a.`PermanentEmployeeMale` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PermanentEmployeeMale\",
                a.`PermanentEmployeeFemale` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PermanentEmployeeFemale\",
                a.`TemporaryEmployeeMale` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TemporaryEmployeeMale\",
                a.`TemporaryEmployeeFemale` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TemporaryEmployeeFemale\",
                a.`Latitude` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Latitude\",
                a.`Longitude` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Longitude\",
                a.`Elevation` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Elevation\",
                a.`Photo` AS PhotoSrc,
                a.LocationPhoto,
                a.PartnerID,
                a.Capacity,
                a.`Capacity` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Capacity\",
                a.`PlasmaFarmer` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PlasmaFarmer\",
                a.`EstimatedSmallholderFarmer` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer\",
                a.`HaveOer` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer\",
                a.`SocializationStatus` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SocializationStatus\",
                a.`NDASent` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-NDASent\",
                a.`NDAAgree` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-NDAAgree\",
                a.`NDASigned` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-NDASigned\",
                a.`ParticipationStatus` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ParticipationStatus\",
                a.`VisitDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-VisitDate\",
                a.`RecruitDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-RecruitDate\",
                a.`TrainingDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TrainingDate\",
                a.`SurveyDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SurveyDate\",
                a.`HeadQuarterAddress` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HeadQuarterAddress\",
                a.`SocializationStatusDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SocializationStatusDate\",
                a.`NDASentDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-NDASentDate\",
                a.`NDAAgreeDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-NDAAgreeDate\",
                a.`NDASignedDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-NDASignedDate\",
                a.`ParticipationStatusDate` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ParticipationStatusDate\",
                b.`SupplychainID` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SupplychainID\",
                b.`WorkHour` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-WorkHour\",
                b.`ProductionCapacity` AS \"Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ProductionCapacity\"
            FROM
                `ktv_mill` a
            LEFT JOIN
                `ktv_tc_supplychain_org` b ON b.ObjType='mill' AND b.ObjID=a.MillID  AND b.StatusCode = 'active' AND b.ObjType = 'mill'
            WHERE
                1=1
               $where
            LIMIT 1";
        $query = $this->db->query($sql);
        
        $data = $query->row_array();

        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['PhotoSrcPath'] = 'images/mill/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'images/mill/'.$data["Province"].'/'.$data['PhotoSrc'];
        }

        if($this->awsfileupload->doesObjectExist($data['LocationPhoto']) == true) {
            $data['LocationPhotoPath'] = $data['LocationPhoto'];
            $data['LocationPhoto'] = $this->config->item('CTCDN')."/".$data['LocationPhoto'];
        }else{
            $data['LocationPhotoPath'] = 'images/mill_location/'.$data["Province"].'/'.$data['LocationPhoto'];
            $data['LocationPhoto'] = base_url().'images/mill_location/'.$data["Province"].'/'.$data['LocationPhoto'];
        }

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function get_basic_data_mill($PID){
        $sql = "SELECT
                m.MillDisplayID
                , m.MillName
                , m.`Year`
                , CASE
                    WHEN m.`Status` = 1 THEN 'Sole Proprietorship'
                    WHEN m.`Status` = 2 THEN 'Partnership'
                    WHEN m.`Status` = 3 THEN 'Limited Partnership'
                    WHEN m.`Status` = 4 THEN 'Limited Liability Company'
                    WHEN m.`Status` = 5 THEN 'Corporation'
                    WHEN m.`Status` = 6 THEN 'Cooperative'
                    WHEN m.`Status` = 7 THEN 'Foundation'
                    WHEN m.`Status` = 8 THEN 'Association'
                    WHEN m.`Status` = 9 THEN 'State Owned'
                    ELSE '-'
                    END Status
                , COUNT(s.StaffID) staffNr
                , v.Village
                , sd.SubDistrict
                , d.District
                , p.Province
                , m.Capacity
                , m.Latitude
                , m.Longitude
                , CONCAT('api/images/mill/',d.ProvinceID,'/',m.Photo) Logo
            FROM
                view_tc_supplychain_org org
            LEFT JOIN
                ktv_mill m on m.MillID = org.ObjID
            LEFT JOIN
                ktv_staffs s on s.ObjID = org.ObjID
            LEFT JOIN
                ktv_village v on v.VillageID = m.VillageID
            LEFT JOIN
                ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN
                ktv_district d on d.DistrictID = sd.DistrictID
            LEFT JOIN
                ktv_province p on d.ProvinceID = p.ProvinceID
            WHERE
                m.PartnerID = ?
            GROUP BY
                m.MillID";
            
            $query = $this->db->query($sql,array($PID));

            return $query->result_array()[0];
    }

    public function get_pemasok($PID){
        $sql = "SELECT
                SUM(sme.GardenAreaHa) luas_kebun
                , COUNT(sme.MemberID) jml_kebun
            FROM
                ktv_members a
                LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                INNER JOIN ktv_survey_plot_sme sme on sme.MemberID = a.MemberID
            WHERE
                op.PartnerID = ?
                AND o.ObjType = 'agent' 
                AND a.StatusCode = 'active'
                AND mr.MRoleID IN (5,6,7,8,9,10,11,12,13,14)
            GROUP BY sme.MemberID";
        $query = $this->db->query($sql,array($PID));

        $result = array();
        $luas_kebun_sme = 0;
        $jml_kebun_sme  = 0;
        if($query->num_rows()>0){
            $datas = $query->result();
            foreach($datas as $row){
                $luas_kebun_sme += $row->luas_kebun;
                $jml_kebun_sme  += $row->jml_kebun;
            }
        }

        $sql2 = "SELECT
                SUM(sp.GardenAreaHa) luas_kebun
                , COUNT(sp.MemberID) jml_kebun
            FROM
                ktv_access_partner_member s_ma
                INNER JOIN ktv_survey_plot sp on sp.MemberID = s_ma.apmMemberID AND sp.StatusCode = 'active'
                INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
                LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
            WHERE
            --  s_par.PartnerIndustry = 3 
            1=1
                AND s_mi.StatusCode = 'active'
                AND s_mi.PartnerID = ?
                AND m.SupplybaseType = 'direct'
            GROUP BY
                s_ma.apmMemberID";
        $query2 = $this->db->query($sql2,array($PID));

        $result = array();
        if($query2->num_rows()>0){
            $datas = $query2->result();
            foreach($datas as $row){
                $luas_kebun_sme += $row->luas_kebun;
                $jml_kebun_sme  += $row->jml_kebun;
            }
        }
        
        $result['luas_kebun_sme']    = number_format($luas_kebun_sme,0);
        $result['jml_kebun_sme']     = number_format($jml_kebun_sme,0);

        return $result;
    }

    public function get_farmer_mill($PID){
        $sql = "SELECT
            m.MemberID registered_farmer
            ,IF(m.FarmerCategory = 'Mapped',1,0) mapped_farmer
            ,IF(m.FarmerCategory = 'Unmapped',1,0) unmapped_farmer
            ,COUNT(sp.MemberID) garden_registered
            ,SUM(IF(sp.Latitude > 0,1,0)) garden_mapped
            ,SUM(sp.GardenAreaHa) garden_area_registered
            ,SUM(IF(sp.Latitude > 0,GardenAreaHa,0)) garden_area_mapped
        FROM
            ktv_access_partner_member s_ma
            INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
            INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
            INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
            LEFT JOIN ktv_members m ON m.MemberID = s_ma.apmMemberID
            LEFT JOIN ktv_survey_plot sp on sp.MemberID = m.MemberID
            WHERE--     s_par.PartnerIndustry = 3
            1 = 1 
            AND s_mi.StatusCode = 'active' 
            AND s_mi.PartnerID = ?
        GROUP BY
            m.MemberID";
        
        $query = $this->db->query($sql,array($PID));
        

        $result     = array();
        $registered_farmer  = 0;
        $mapped_farmer      = 0;
        $unmapped_farmer    = 0;
        $garden_registered  = 0;
        $garden_mapped      = 0;
        $garden_area_registered  = 0;
        $garden_area_mapped      = 0;
        if($query->num_rows()>0){
            $datas = $query->result();
            foreach($datas as $row){
                $mapped_farmer += $row->mapped_farmer;
                $unmapped_farmer  += $row->unmapped_farmer;

                $garden_registered  += $row->garden_registered;
                $garden_mapped  += $row->garden_mapped;

                $garden_area_registered  += $row->garden_area_registered;
                $garden_area_mapped  += $row->garden_area_mapped;
            }
        }
        $result['registered_farmer']    = number_format($query->num_rows(),0);
        $result['mapped_farmer']        = number_format($mapped_farmer,0);
        $result['unmapped_farmer']      = number_format($unmapped_farmer,0);
        $result['garden_registered']    = number_format($garden_registered,0);
        $result['garden_mapped']        = number_format($garden_mapped,0);
        $result['garden_unmapped']      = number_format($garden_registered - $garden_mapped,0);
        $result['garden_area_registered']    = number_format($garden_area_registered,0);
        $result['garden_area_mapped']        = number_format($garden_area_mapped,0);
        $result['garden_area_unmapped']      = number_format($garden_area_registered - $garden_area_mapped,0);

        return $result;
    }

    public function get_mapped_farmer($PID){
        $sql = "SELECT
            COUNT(s_ma.apmMemberID) petani_terpetakan
            , SUM(sp.GardenAreaHa) luas_kebun
            , s_ma.apmMemberID
        FROM
            ktv_access_partner_member s_ma
            LEFT JOIN ktv_survey_plot sp on sp.MemberID = s_ma.apmMemberID AND sp.StatusCode = 'active'
            INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
            INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
            INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
            LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
        WHERE
        --  s_par.PartnerIndustry = 3 
        1=1
            AND s_mi.StatusCode = 'active'
            AND s_mi.PartnerID = ?
            AND m.FarmerCategory = 'Mapped'
        GROUP BY
            s_ma.apmMemberID";

        $query = $this->db->query($sql,array($PID));
        $result     = array();
        $luas_kebun = 0;
        $jml_kebun  = 0;
        if($query->num_rows()>0){
            $datas = $query->result();
            foreach($datas as $row){
                $luas_kebun += $row->luas_kebun;
                $jml_kebun  += $row->petani_terpetakan;
            }
        }
        $result['mapped_farmer']    = number_format($query->num_rows(),0);
        $result['luas_kebun']       = number_format($luas_kebun,2);
        $result['jml_kebun']       = number_format($jml_kebun);

        return $result;
    }

    public function getJmlKebun($roleID,$PID){        
        $sql = "SELECT
            a.MemberID jml
        FROM
            ktv_members a
            INNER JOIN ktv_survey_plot_sme ps on ps.MemberID = a.MemberID
            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
        WHERE
            op.PartnerID = ? 
            AND o.ObjType = 'agent' 
            AND a.StatusCode = 'active'
            AND mr.MRoleID IN ($roleID)
        GROUP BY a.MemberID";

        $query = $this->db->query($sql,array($PID));

        if($query->num_rows()>0){
            return $query->num_rows();
        }
        return 0;
    }

    public function getJmlSupplierDirect($PID,$kebun=null){
        $join = "";
        if($kebun == "kebun"){
            $join = "INNER JOIN ktv_survey_plot sp on sp.MemberID = s_ma.apmMemberID AND sp.StatusCode = 'active'";
        }
        $sql = "SELECT
            s_ma.apmMemberID
        FROM
            ktv_access_partner_member s_ma
            $join
            INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
            INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
            INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
            LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
        WHERE
        --  s_par.PartnerIndustry = 3 
        1=1
            AND s_mi.StatusCode = 'active'
            AND s_mi.PartnerID = ?
            AND m.SupplybaseType = 'direct'
        GROUP BY
            s_ma.apmMemberID";

        $query = $this->db->query($sql,array($PID));

        return $query->num_rows();
    }

    public function getJmlSupplier($roleID,$PID){
        $sql = "SELECT
            a.MemberID
        FROM
            ktv_members a
            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
        WHERE
            op.PartnerID = ? 
            AND o.ObjType = 'agent' 
            AND a.StatusCode = 'active'
            AND mr.MRoleID IN ($roleID)
        GROUP BY a.MemberID";

        $query = $this->db->query($sql,array($PID));

        if($query->num_rows()>0){
            return $query->num_rows();
        }
        return 0;
    }

    public function getSPCode($MillID){
        $sql = "
        SELECT
            a.SPCodeID
            , a.MillID
            , a.Note
            , a.SuratNr
        FROM
            `ktv_mill_sp_code` a
        WHERE
            MillID = ?
        ";

        $query = $this->db->query($sql,array($MillID));

        return $query->result_array();
    }

    public function getSourceName($PartnerID,$SourceType,$KategoriKebun=null){
        $where = "";
        if($SourceType == 1){
            $where .= " AND mr.MroleID = 14";
        }
        if($SourceType == 4){
            $where .= " AND mr.MroleID IN (5,6,7,8,9,10,13)";
        }
        if($SourceType == 2){
            $where .= " AND mr.MroleID = 11";
        }
        if($SourceType == 3){
            $where .= " AND mr.MroleID = 12";
        }

        if($KategoriKebun AND $KategoriKebun == 2){
            $sql = "SELECT
                s_ma.apmMemberID id
                , m.MemberName label
            FROM
                ktv_access_partner_member s_ma
                INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
                LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
            WHERE
            --  s_par.PartnerIndustry = 3 
            1=1
                AND s_mi.StatusCode = 'active'
                AND s_mi.PartnerID = ?
                AND m.SupplybaseType = 'direct'
            GROUP BY
                s_ma.apmMemberID";

            $query = $this->db->query($sql,array($PartnerID));
        }else{
            $sql = "SELECT
                    o.SupplychainID id
                    , IFNULL(me.agCompanyName, a.MemberName) label
                FROM
                    ktv_members a
                LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                WHERE
                    op.PartnerID = ? 
                    AND o.ObjType = 'agent' 
                    AND a.StatusCode = 'active'
                    $where
                GROUP BY a.MemberID
                ORDER BY
                    label ASC";
    
            $query = $this->db->query($sql,array($PartnerID));
        }

        return $query->result_array();
    }

    public function generateDeclaration($MillID){
        $sql = "
        SELECT
            tcorg2.ObjID MillID
            , batch.SupplyBatchID
            , batch.SupplyBatchDate
            , batch.DeliveryDate
            , tc.SupplybaseCategoryID
            , CASE
                WHEN tc.SupplybaseCategoryID = 1 THEN 1
                WHEN tc.SupplybaseCategoryID = 2 THEN 4
                WHEN tc.SupplybaseCategoryID = 3 THEN 4
                WHEN tc.SupplybaseCategoryID = 4 THEN 2
                WHEN tc.SupplybaseCategoryID = 5 THEN 3
                ELSE ''
            END AS SourceType
            , tc.VolumeNetto FFB
            , tc.SupplychainID
            , tcorg.`Name`
            , tc.SupplyTransID SID 
            , tc.SupplyID
        FROM
            `ktv_tc_supplychain_transaction` tc
        LEFT JOIN
            ktv_tc_supplychain_batch batch ON tc.SupplyID = batch.SupplyBatchID
        LEFT JOIN
            view_tc_supplychain_org tcorg2 ON tcorg2.SupplychainID = tc.SupplychainID
        LEFT JOIN
            ktv_mill_tc mt ON mt.SupplyBatchID = tc.SupplyTransID
        LEFT JOIN
            view_tc_supplychain_org tcorg ON tcorg.SupplychainID = tc.SupplyID
        WHERE
                1 = 1
            AND tcorg2.ObjID = ?
            AND mt.SupplyBatchID IS NULL
            AND tc.StatusCode = 'active'
        ";

        $query = $this->db->query($sql,array($MillID));

        if($query->num_rows()>0){
            $MillTCEventID = $this->getMillTCEventID($MillID);
            $transaction = array();
            foreach($query->result() as $row){
                list($SourceName,$TCPercentage) = $this->getSupplierData($row->SupplyID,$row->SourceType);

                $transaction[] = array(
                    "MillTCEventID" => $MillTCEventID,
                    "MillID" => $MillID,
                    "SupplyBatchID" => $row->SupplyBatchID,
                    "SupplyBatchDate"   => $row->SupplyBatchDate,
                    "DeliveryDate"   => $row->DeliveryDate,
                    "SourceCategory"   => $row->SupplybaseCategoryID,
                    "FFBSupply"   => $row->FFB,
                    "LockDate"  => date("Y-m-d H:i:s"),
                    "DateCreated"  => date("Y-m-d H:i:s"),
                    "CreatedBy"  => $_SESSION["userid"],
                    "LockStatus"  => 1,
                    "SourceType"    => $row->SourceType,
                    "SupplyTransID"  => $row->SID,
                    "SourceName" => $row->Name,
                    "SourceID"  => $row->SupplyID,
                    "TCPercentage" => $TCPercentage,
                    "Generated" => "Yes"
                );
            }

            $this->db->insert_batch("ktv_mill_tc",$transaction);

            return true;
        }
    }

    function getMillTCEventID($MillID){
        $data = array(
            "MillID"    => $MillID,
            "LockDate"  => date("Y-m-d H:i:s"),
            "LockStatus" => 1,
            "LockComment" => "lock",
            "DateCreated"  => date("Y-m-d H:i:s"),
            "CreatedBy"  => $_SESSION["userid"],
        );

        $query = $this->db->insert("ktv_mill_tc_event",$data);

        return $this->db->insert_id();
    }

    public function getTracablePrint($PartnerID,$Year,$MillTCDID,$Period){
        $start  = $Year."-01-01 00:00:00";
        if($Period == "half"){
            $end = $Year."-06-30 23:59:59";
        } else if($Period == "half2"){
            $start  = $Year."-07-01 00:00:00";
            $end = $Year."-12-31 23:59:59";
        } else if($Period == "full"){
            $end = $Year."-12-31 23:59:59";
        }else{
            $end = "";
        }

        if($MillTCDID == ""){
            $sql = "
                SELECT
                    a.SourceType
                    , CASE
                            WHEN a.SourceType = 1 THEN 'Plasma'
                            WHEN a.SourceType = 2 THEN 'Owned Estate'
                            WHEN a.SourceType = 3 THEN 'External Estate'
                            WHEN a.SourceType = 4 THEN 'Other Supplier'
                        END SourcTypeName
                    , CASE
                        WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                        WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                        WHEN a.SourceCategory = 3 THEN 'Dealer/Agent/Vendor'
                        WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                        WHEN a.SourceCategory = 5 THEN 'External Estate'
                        ELSE '-'
                    END SourceCategory
                    , a.SourceName
                    , a.FFBSupply
                    , IFNULL(a.TCPercentage,0) TCPercentage
                    , b.TotalTrace/b.TotalMill TotalTrace
                    , IF(a.TCPercentage > 0,'Yes','No') Tracebility
                FROM ktv_mill_tc a
                LEFT JOIN (
                    SELECT
                        a.SourceType
                        , SUM(a.TCPercentage) as TotalTrace
                        , COUNT(a.MillID) TotalMill
                        , a.MillID
                    FROM ktv_mill_tc a
                    WHERE 1=1
                    AND a.DeliveryDate >= ?
                    AND a.DeliveryDate <= ?
                    GROUP BY a.SourceType,MillID
                ) b on b.MillID = a.MillID AND b.SourceType = a.SourceType
                LEFT JOIN
                    ktv_mill m on m.MillID = a.MillID
                WHERE 1=1
                    AND m.PartnerID = ?
                    AND a.DeliveryDate >= ?
                    AND a.DeliveryDate <= ?
                ORDER BY a.SourceType
            ";

            $query = $this->db->query($sql,array($start,$end,$PartnerID,$start,$end));
        }else{
            $sql = "
                SELECT
                    CASE 
                        WHEN a.KategoriKebun = 1 THEN 'Plasma'
                        WHEN a.KategoriKebun = 2 THEN 'Other Supplier'
                        WHEN a.KategoriKebun = 3 THEN 'Other Supplier'
                        WHEN a.KategoriKebun = 4 THEN 'Owned Estate'
                        WHEN a.KategoriKebun = 5 THEN 'External Estate'
                        ELSE '-'
                    END SourcTypeName
                    , CASE 
                        WHEN a.KategoriKebun = 1 THEN '2'
                        WHEN a.KategoriKebun = 2 THEN '4'
                        WHEN a.KategoriKebun = 3 THEN '4'
                        WHEN a.KategoriKebun = 4 THEN '1'
                        WHEN a.KategoriKebun = 5 THEN '3'
                        ELSE '-'
                    END SourceType
                    , a.SupplierName SourceName
                    , a.FFBSupply
                    , a.Tracebility TCPercentage
                    , IF(a.Tracebility > 0,'Yes','No') Tracebility
                    , b.TotalTrace/b.TotalMill TotalTrace
                    , CASE
                        WHEN a.KategoriKebun = 1 THEN 'Estate Plasma'
                        WHEN a.KategoriKebun = 2 THEN 'Direct Smallholder'
                        WHEN a.KategoriKebun = 3 THEN 'Agent / Dealer / Vendor'
                        WHEN a.KategoriKebun = 4 THEN 'Estate Inti'
                        WHEN a.KategoriKebun = 5 THEN 'External Estate'
                        ELSE '-'
                    END as SourceCategory
                FROM
                    `ktv_mill_tc_declaration_detail` a
                LEFT JOIN (
                    SELECT
                        CASE 
                            WHEN a.KategoriKebun = 1 THEN '2'
                            WHEN a.KategoriKebun = 2 THEN '4'
                            WHEN a.KategoriKebun = 3 THEN '4'
                            WHEN a.KategoriKebun = 4 THEN '1'
                            WHEN a.KategoriKebun = 5 THEN '3'
                            ELSE '-'
                        END SourceType
                        , SUM(a.Tracebility) as TotalTrace
                        , COUNT(a.MillID) TotalMill
                        , a.MillTCDID
                    FROM ktv_mill_tc_declaration_detail a
                    WHERE 1=1
                    GROUP BY SourceType,MillID
                ) b on b.MillTCDID = a.MillTCDID AND b.SourceType = SourceType
                WHERE
                1=1
                $where
                AND a.MillTCDID = ?
            ";

            $query = $this->db->query($sql,array($MillTCDID));
        }

        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;

        return $query->result_array();
    }

    function UpdateImageMill($MillID,$path,$type){
        if($type == "Logo"){
            $sqlupdate = "Photo = '$path'";
        }
        if($type == "Location"){
            $sqlupdate = "LocationPhoto = '$path'";
        }
        $sql = "
            UPDATE
                ktv_mill
            SET
                $sqlupdate
            WHERE MillID = '$MillID'
        ";

        $query = $this->db->query($sql);
    }

    public function getMilltracebilityDeclaration($PartnerID,$Year,$Period){
        
        $start  = $Year."-01-01 00:00:00";
        if($Period == "half"){
            $end = $Year."-06-30 23:59:59";
        } else if($Period == "half2"){
            $start  = $Year."-07-01 00:00:00";
            $end = $Year."-12-31 23:59:59";
        } else if($Period == "full"){
            $end = $Year."-12-31 23:59:59";
        }else{
            $end = "";
        }

        $sql = "SELECT
            a.SourceType
            , CASE
                    WHEN a.SourceType = 1 THEN 'Plasma'
                    WHEN a.SourceType = 2 THEN 'OwnedEstate'
                    WHEN a.SourceType = 3 THEN 'ExternalEstate'
                    WHEN a.SourceType = 4 THEN 'OtherSupplier'
                END SourcTypeName
            , SUM(a.FFBSupply) TotalFFB
            , SUM(IF(a.TCPercentage > 0,1,0)) TotalTrace
            , (SUM(a.FFBSupply)/b.TotalFFBAll) * 100 ProportionFFB
            , (SUM(IF(a.TCPercentage > 0,a.FFBSupply,0))/b.TotalFFBAll) * 100 TTPMILL
            , SUM(IF(a.TCPercentage > 0,a.FFBSupply,0)) TPP
            , b.TotalFFBAll
            , a.Approved
        FROM
            `ktv_mill_tc` a
        LEFT JOIN (
            SELECT 
                a.MillID
                , SUM(a.FFBSupply) AS TotalFFBAll
            FROM 
                ktv_mill_tc a
            WHERE 1=1
            AND a.DeliveryDate >= ?
            AND a.DeliveryDate <= ?
            GROUP BY a.MillID
        ) b on b.MillID = a.MillID
        LEFT JOIN
            ktv_mill m on m.MillID = a.MillID
        WHERE 1=1
            AND m.PartnerID = ?
            AND a.DeliveryDate >= ?
            AND a.DeliveryDate <= ?
        GROUP BY a.SourceType
        ORDER BY a.SourceType ASC
        ";
        $query = $this->db->query($sql,array($start,$end,$PartnerID,$start,$end));
        // echo "<pre>";
        // echo $this->db->last_query();
        // die;

        $dataArray      = array();
        $dataApproved   = array();
        
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredOwnedEstate"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceOwnedEstate"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOwnedEstate"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillOwnedEstate"] = 0;
        
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredPlasma"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTracePlasma"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionPlasma"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillPlasma"] = 0;
        
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredExternalEstate"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceExternalEstate"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionExternalEstate"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillExternalEstate"] = 0;

        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredOtherSupplier"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceOtherSupplier"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOtherSupplier"] = 0;
        $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillOtherSupplier"] = 0;
        if($query->num_rows()>0){
            $tmp = "";
            $TotalFFB = 0;
            $approved = 0;
            foreach($query->result() as $row){
                if($row->SourceType <> $tmp){
                    $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcured".$row->SourcTypeName]    = number_format($row->TotalFFB,2);
                    $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTrace".$row->SourcTypeName]     = number_format($row->TotalTrace,0);
                    $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportion".$row->SourcTypeName] = number_format($row->ProportionFFB,2);
                    $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMill".$row->SourcTypeName] = number_format($row->TTPMILL,2);

                    $tmp = $row->SourceType;
                }
                $TotalFFB += $row->TotalFFB;

                if($row->Approved == 1){
                    $approved = 1;
                }

                $dataArray["Approved"] = $approved;

                if($Period == 'full'){
                    $hidden = 1;
                } else {
                    $hidden = 0;
                }

                $dataArray["Hidden"] = $hidden;
            }
            $dataArray["Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalFFB"] = number_format($TotalFFB,2);
        } else {
            $dataArray["Unapproved"] = 1;
            $dataArray["Total"]   = $query->num_rows;
        }

        $data["success"] = true;
        $data["data"]    = $dataArray;
        
        return $data;
    }

    public function getGridTracebilityDeclaration($PartnerID,$Year,$MillTCDID,$Period,$Category){
        $start  = $Year."-01-01 00:00:00";
        if($Period == "half"){
            $end = $Year."-06-30 23:59:59";
        } else if($Period == "half2"){
            $start  = $Year."-07-01 00:00:00";
            $end = $Year."-12-31 23:59:59";
        } else if($Period == "full"){
            $end = $Year."-12-31 23:59:59";
        }else{
            $end = "";
        }
       
        if($Category == 2){
            $table = "ktv_survey_plot";
        }else{
            $table = "ktv_survey_plot_sme";
        }
        
        $dataArray = array();
       
        if($Period == "full"){
            if($MillTCDID == ""){
                if($Category != 4){
                   
                    // $sql = "SELECT
                    //     a.SourceName
                    //     , a.MillTCID
                    //     , a.MillID
                    //     , CASE
                    //         WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                    //         WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                    //         WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                    //         WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                    //         WHEN a.SourceCategory = 5 THEN 'External Estate'
                    //         ELSE '-'
                    //     END SourceCategory
                    //     , SUM(a.FFBSupply) AS FFBSupply
                    //     , IFNULL(a.TCPercentage,0) TCPercentage
                    //     , a.SourceCategory SourceCategoryID
                    //     , IF(a.TCPercentage > 0,'Yes','No') Tracebility
                    //     , a.Generated
                    //     , IFNULL(sps.AnnualProduction,0) AnnualProduction
                    //     , IFNULL(sps.GardenAreaHa,0) GardenAreaHa
                    //     , a.SourceID
                    // FROM ktv_mill_tc a
                    // LEFT JOIN
                    //     ktv_mill m on m.MillID = a.MillID
                    // LEFT JOIN
                    //     view_tc_supplychain_org vso on vso.SupplychainID = a.SourceID
                    // LEFT JOIN
                    //     ktv_survey_plot_sme sps on sps.MemberID = vso.ObjID AND sps.SurveyNr = 0
                    // /*LEFT JOIN
                    //     ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                    // LEFT JOIN
                    //     ktv_members km on km.MemberID = st.SupplyID AND st.SupplyType='Farmer'
                    // LEFT JOIN
                    //     ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID AND st.SupplyType='Batch'
                    // LEFT JOIN
                    //     view_tc_supplychain_org vso1 on vso1.SupplychainID = sb.SupplyOrgID*/
                    // WHERE 1=1
                    //     AND a.SourceType = ?
                    //     AND m.PartnerID = ?
                    //     AND a.DeliveryDate >= ?
                    //     AND a.DeliveryDate <= ?
                    // GROUP BY a.SourceID
                    // ";
                    $sql="SELECT 
                        dt.SourceName,
                        dt.SupplierID,
                        dt.SID,
                        dt.MillTCID,
                        dt.MillID,
                        dt.SourceCategory,
                        dt.SourceCategoryID,
                        dt.Generated,
                        SUM(FFBSupply) as FFBSupply,
                        AVG(dt.TCPercentage) as TCPercentage,
                        AVG(dt.Tracebility) as Tracebility,
                        dt.SourceID
                        FROM (
                            SELECT
                                IFNULL(a.SourceName,km.MemberName) as SourceName
                                , IFNULL(vso1.ObjID,km.MemberID) SupplierID
                                , IFNULL(vso1.SupplychainID,km.MemberID) SID
                                , a.MillTCID
                                , a.MillID
                                , CASE
                                        WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                                        WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                                        WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                                        WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                                        WHEN a.SourceCategory = 5 THEN 'External Estate'
                                        ELSE '-'
                                END SourceCategory
                                , a.FFBSupply
                                , IFNULL(a.TCPercentage,0) TCPercentage
                                , a.SourceCategory SourceCategoryID
                                , IFNULL(a.TCPercentage,0) Tracebility
                                , a.Generated
                                , a.SourceID
                            FROM ktv_mill_tc a
                            LEFT JOIN
                                ktv_mill m on m.MillID = a.MillID
                            LEFT JOIN
                                ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                            LEFT JOIN
                                ktv_members km on km.MemberID = st.SupplyID
                            LEFT JOIN
                                ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID 
                            LEFT JOIN
                                view_tc_supplychain_org vso1 on vso1.SupplychainID = IF(a.SupplyBatchID IS NULL,a.SourceID,sb.SupplyOrgID)
                            WHERE 1=1
                                AND a.SourceType = ?
                                AND m.PartnerID = ?
                                AND a.DeliveryDate >= ?
                                AND a.DeliveryDate <= ?
                            GROUP BY a.MillTCID
                        ) dt
                        WHERE 
                        1=1
                        GROUP BY dt.SourceID";
                }else{
                    // $sqlx = "SELECT
                    //     a.SourceName
                    //     , a.MillTCID
                    //     , a.MillID
                    //     , CASE
                    //         WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                    //         WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                    //         WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                    //         WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                    //         WHEN a.SourceCategory = 5 THEN 'External Estate'
                    //         ELSE '-'
                    //     END SourceCategory
                    //     , a.FFBSupply
                    //     , IFNULL(a.TCPercentage,0) TCPercentage
                    //     , a.SourceCategory SourceCategoryID
                    //     , IFNULL(a.TCPercentage,0) Tracebility
                    //     , a.Generated
                    //     , IFNULL(SUM(b.AnnualProduction),SUM(sp.AnnualProduction)) AnnualProduction
                    //     , IFNULL(SUM(b.GardenAreaHa),SUM(sp.GardenAreaHa)) GardenAreaHa
                    //     , SourceID
                    // FROM ktv_mill_tc a
                    // LEFT JOIN
                    //     ktv_mill m on m.MillID = a.MillID
                    // LEFT JOIN
                    //     ktv_tc_supplychain_farmer vso on vso.SupplychainID = a.SourceID
                    // LEFT JOIN
                    //     ktv_survey_plot b on b.MemberID = vso.FarmerID
                    // LEFT JOIN
                    //     ktv_survey_plot sp on sp.MemberID = a.SourceID
                    /*LEFT JOIN
                        ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                    LEFT JOIN
                        ktv_members km on km.MemberID = st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN
                        ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN
                        view_tc_supplychain_org vso1 on vso1.SupplychainID = IF(a.SupplyBatchID IS NULL,a.SourceID,sb.SupplyOrgID)*/
                    // WHERE 1=1
                    //     AND a.SourceType = ?
                    //     AND m.PartnerID = ?
                    //     AND a.DeliveryDate >= ?
                    //     AND a.DeliveryDate <= ?
                    // GROUP BY a.MillTCID
                    // ";
                    
                $sql="SELECT 
                        dt.SourceName,
                        dt.SupplierID,
                        dt.SID,
                        dt.MillTCID,
                        dt.MillID,
                        dt.SourceCategory,
                        dt.SourceCategoryID,
                        dt.Generated,
                        SUM(FFBSupply) as FFBSupply,
                        AVG(dt.TCPercentage) as TCPercentage,
                        AVG(dt.Tracebility) as Tracebility,
                        dt.SourceID
                        FROM (
                            SELECT
                                IFNULL(a.SourceName,km.MemberName) as SourceName
                                , IFNULL(vso1.ObjID,km.MemberID) SupplierID
                                , IFNULL(vso1.SupplychainID,km.MemberID) SID
                                , a.MillTCID
                                , a.MillID
                                , CASE
                                        WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                                        WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                                        WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                                        WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                                        WHEN a.SourceCategory = 5 THEN 'External Estate'
                                        ELSE '-'
                                END SourceCategory
                                , a.FFBSupply
                                , IFNULL(a.TCPercentage,0) TCPercentage
                                , a.SourceCategory SourceCategoryID
                                , IFNULL(a.TCPercentage,0) Tracebility
                                , a.Generated
                                , a.SourceID
                            FROM ktv_mill_tc a
                            LEFT JOIN
                                ktv_mill m on m.MillID = a.MillID
                            LEFT JOIN
                                ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                            LEFT JOIN
                                ktv_members km on km.MemberID = st.SupplyID
                            LEFT JOIN
                                ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID 
                            LEFT JOIN
                                view_tc_supplychain_org vso1 on vso1.SupplychainID = IF(a.SupplyBatchID IS NULL,a.SourceID,sb.SupplyOrgID)
                            WHERE 1=1
                                AND a.SourceType = ?
                                AND m.PartnerID = ?
                                AND a.DeliveryDate >= ?
                                AND a.DeliveryDate <= ?
                            GROUP BY a.MillTCID
                        ) dt
                        WHERE 
                        1=1
                        GROUP BY dt.SourceID";
                }
                
                /*if($PartnerID=='62'){
                    $query = $this->db->query($sql62,array($Category,$PartnerID,$start,$end));
                }else{
                    $query = $this->db->query($sql,array($Category,$PartnerID,$start,$end));
                }*/
                $query = $this->db->query($sql,array($Category,$PartnerID,$start,$end));
                // echo $this->db->last_query();die;
                if($query->num_rows()>0){
                    // $i = 1;
                    foreach($query->result() as $row){
    
                        if($Category==4){
                            $sqlF = "SELECT 
                                sf.FarmerID,
                                sf.SupplychainID,
                                SUM(sp.GardenAreaHa) as GardenAreaHa,
                                SUM(sp.AnnualProduction) as AnnualProduction
                                FROM
                                view_tc_supplychain_org vso 
                                LEFT JOIN ktv_tc_supplychain_farmer sf ON sf.SupplychainID=vso.SupplychainID
                                LEFT JOIN ktv_survey_plot sp ON sp.MemberID=sf.FarmerID
                                WHERE
                                vso.ObjID=?";
                            $queryF = $this->db->query($sqlF,array($row->SupplierID));  
                            $sme = $queryF->row(); 
                            $AnnualProduction = @$sme->AnnualProduction;
                            $GardenAreaHa = @$sme->GardenAreaHa;
                            $rowAnnualProduction = @$sme->AnnualProduction;
                        } else{
                            $AnnualProduction = $row->AnnualProduction;
                            $GardenAreaHa = $row->GardenAreaHa;
                            $rowAnnualProduction = @$row->AnnualProduction;
                        }
    
                        if($Period == "half"){
                            $AnnualProduction = $AnnualProduction/2;
                            if($row->SourceCategoryID == 3){
                                $row->Tracebility = ($AnnualProduction/$row->FFBSupply)*100;
                            }
                        } elseif($Period == "full"){
                            if($Category==4){
                                $calculate = ($AnnualProduction/$row->FFBSupply) * 100;
                                $row->TCPercentage = $calculate;
                            } else {
                                $row->TCPercentage = $row->Tracebility;
                            }
                        } else{
                            $AnnualProduction = $AnnualProduction;
                            if($row->SourceCategoryID == 3){
                                if($AnnualProduction > $row->FFBSupply){
                                    $row->Tracebility = ($row->FFBSupply /$AnnualProduction)*100;
                                } else {
                                    $row->Tracebility = ($AnnualProduction/$row->FFBSupply)*100;
                                }
                            }
                        }
                        if($row->TCPercentage > 100){
                            $row->TCPercentage = 100;
                        }

                        $dataArray[] = array(
                            "SupplierName" => $row->SourceName
                            ,"SupplierID" => $row->SupplierID
                            ,"MillTCID" => $row->MillTCID
                            ,"MillID" => $row->MillID
                            ,"GardenType" => $row->SourceCategory
                            ,"FFBSupply" => number_format($row->FFBSupply,2)
                            ,"Tracebility" => ($row->TCPercentage > 0 ? number_format($row->TCPercentage,2) : 0 )." %"
                            ,"Generated"    => $row->Generated
                            ,"AnnualProduction" => number_format($rowAnnualProduction,2)
                            ,"GardenAreaHa" => number_format($GardenAreaHa,2)
                        );
                    }
    
                    // echo "<pre>";
                    // print_r($dataArray);
                    // die;
                }
            }else{
                $where = "";
                if($Category == 4){
                    $where .= " AND a.KategoriKebun IN ('2','3') ";
                }
                if($Category == 1){
                    $where .= " AND a.KategoriKebun IN ('1') ";
                }
                if($Category == 2){
                    $where .= " AND a.KategoriKebun IN ('4') ";
                }
                if($Category == 3){
                    $where .= " AND a.KategoriKebun IN ('5') ";
                }
                $sql = "
                    SELECT
                        a.SupplierName SourceName
                        , a.MillTCID
                        , a.MillID
                        , a.FFBSupply
                        , a.Tracebility TCPercentage
                        , IF(a.Tracebility > 0,'Yes','No') Tracebility
                        , CASE
                            WHEN a.KategoriKebun = 1 THEN 'Estate Plasma'
                            WHEN a.KategoriKebun = 2 THEN 'Direct Smallholder'
                            WHEN a.KategoriKebun = 3 THEN 'Agent / Dealer / Vendor'
                            WHEN a.KategoriKebun = 4 THEN 'Estate Inti'
                            WHEN a.KategoriKebun = 5 THEN 'External Estate'
                            ELSE '-'
                        END as SourceCategory
                    FROM
                        `ktv_mill_tc_declaration_detail` a
                    WHERE
                    1=1
                    $where
                    AND a.MillTCDID = ?
                ";
    
                $query = $this->db->query($sql,array($MillTCDID));
                
                if($query->num_rows()>0){
                    // $i = 1;
                    foreach($query->result() as $row){
                        if($Category == 4 ){
                            $row->Tracebility = $row->TCPercentage." %";
                        }
                        $dataArray[] = array(
                            "SupplierName" => $row->SourceName
                            ,"MillTCID" => $row->MillTCID
                            ,"MillID" => $row->MillID
                            ,"GardenType" => $row->SourceCategory
                            ,"FFBSupply" => $row->FFBSupply
                            ,"Tracebility" => $row->Tracebility
                            ,"Generated"    => $row->Generated
                        );
                    }
                }
            }
        } else {
            if($MillTCDID == ""){
                if($Category != 4){
                    $sql = "SELECT
                        a.SourceName
                        , a.MillTCID
                        , a.MillID
                        , CASE
                            WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                            WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                            WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                            WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                            WHEN a.SourceCategory = 5 THEN 'External Estate'
                            ELSE '-'
                        END SourceCategory
                        , a.FFBSupply
                        , IFNULL(a.TCPercentage,0) TCPercentage
                        , a.SourceCategory SourceCategoryID
                        , IF(a.TCPercentage > 0,'Yes','No') Tracebility
                        , a.Generated
                        , IFNULL(sps.AnnualProduction,0) AnnualProduction
                        , IFNULL(sps.GardenAreaHa,0) GardenAreaHa
                        , SourceID
                    FROM ktv_mill_tc a
                    LEFT JOIN
                        ktv_mill m on m.MillID = a.MillID
                    LEFT JOIN
                        view_tc_supplychain_org vso on vso.SupplychainID = a.SourceID
                    LEFT JOIN
                        ktv_survey_plot_sme sps on sps.MemberID = vso.ObjID AND sps.SurveyNr = 0
                    /*LEFT JOIN
                        ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                    LEFT JOIN
                        ktv_members km on km.MemberID = st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN
                        ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN
                        view_tc_supplychain_org vso1 on vso1.SupplychainID = sb.SupplyOrgID*/
                    WHERE 1=1
                        AND a.SourceType = ?
                        AND m.PartnerID = ?
                        AND a.DeliveryDate >= ?
                        AND a.DeliveryDate <= ?
                    GROUP BY a.MillTCID
                    ";
                }else{
                    // $sqlx = "SELECT
                    //     a.SourceName
                    //     , a.MillTCID
                    //     , a.MillID
                    //     , CASE
                    //         WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                    //         WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                    //         WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                    //         WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                    //         WHEN a.SourceCategory = 5 THEN 'External Estate'
                    //         ELSE '-'
                    //     END SourceCategory
                    //     , a.FFBSupply
                    //     , IFNULL(a.TCPercentage,0) TCPercentage
                    //     , a.SourceCategory SourceCategoryID
                    //     , IFNULL(a.TCPercentage,0) Tracebility
                    //     , a.Generated
                    //     , IFNULL(SUM(b.AnnualProduction),SUM(sp.AnnualProduction)) AnnualProduction
                    //     , IFNULL(SUM(b.GardenAreaHa),SUM(sp.GardenAreaHa)) GardenAreaHa
                    //     , SourceID
                    // FROM ktv_mill_tc a
                    // LEFT JOIN
                    //     ktv_mill m on m.MillID = a.MillID
                    // LEFT JOIN
                    //     ktv_tc_supplychain_farmer vso on vso.SupplychainID = a.SourceID
                    // LEFT JOIN
                    //     ktv_survey_plot b on b.MemberID = vso.FarmerID
                    // LEFT JOIN
                    //     ktv_survey_plot sp on sp.MemberID = a.SourceID
                    /*LEFT JOIN
                        ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                    LEFT JOIN
                        ktv_members km on km.MemberID = st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN
                        ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN
                        view_tc_supplychain_org vso1 on vso1.SupplychainID = IF(a.SupplyBatchID IS NULL,a.SourceID,sb.SupplyOrgID)*/
                    // WHERE 1=1
                    //     AND a.SourceType = ?
                    //     AND m.PartnerID = ?
                    //     AND a.DeliveryDate >= ?
                    //     AND a.DeliveryDate <= ?
                    // GROUP BY a.MillTCID
                    // ";
    
                $sql="SELECT 
                        dt.SourceName,
                        dt.SupplierID,
                        dt.SID,
                        dt.MillTCID,
                        dt.MillID,
                        dt.SourceCategory,
                        dt.SourceCategoryID,
                        dt.Generated,
                        SUM(FFBSupply) as FFBSupply,
                        AVG(dt.TCPercentage) as TCPercentage,
                        AVG(dt.Tracebility) as Tracebility
                        
                        FROM (
                            SELECT
                                IFNULL(a.SourceName,km.MemberName) as SourceName
                                , IFNULL(vso1.ObjID,km.MemberID) SupplierID
                                , IFNULL(vso1.SupplychainID,km.MemberID) SID
                                , a.MillTCID
                                , a.MillID
                                , CASE
                                        WHEN a.SourceCategory = 1 THEN 'Estate Plasma'
                                        WHEN a.SourceCategory = 2 THEN 'Direct Smallholder'
                                        WHEN a.SourceCategory = 3 THEN 'Agent / Dealer / Vendor'
                                        WHEN a.SourceCategory = 4 THEN 'Estate Inti'
                                        WHEN a.SourceCategory = 5 THEN 'External Estate'
                                        ELSE '-'
                                END SourceCategory
                                , a.FFBSupply
                                , IFNULL(a.TCPercentage,0) TCPercentage
                                , a.SourceCategory SourceCategoryID
                                , IFNULL(a.TCPercentage,0) Tracebility
                                , a.Generated
                                , SourceID
                            FROM ktv_mill_tc a
                            LEFT JOIN
                                ktv_mill m on m.MillID = a.MillID
                            LEFT JOIN
                                ktv_tc_supplychain_transaction st on st.SupplyID = a.SourceID 
                            LEFT JOIN
                                ktv_members km on km.MemberID = st.SupplyID
                            LEFT JOIN
                                ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyID 
                            LEFT JOIN
                                view_tc_supplychain_org vso1 on vso1.SupplychainID = IF(a.SupplyBatchID IS NULL,a.SourceID,sb.SupplyOrgID)
                            WHERE 1=1
                                AND a.SourceType = ?
                                AND m.PartnerID = ?
                                AND a.DeliveryDate >= ?
                                AND a.DeliveryDate <= ?
                            GROUP BY a.MillTCID
                        ) dt
                        WHERE 
                        1=1
                        GROUP BY dt.MillTCID";
                }
                
                /*if($PartnerID=='62'){
                    $query = $this->db->query($sql62,array($Category,$PartnerID,$start,$end));
                }else{
                    $query = $this->db->query($sql,array($Category,$PartnerID,$start,$end));
                }*/
                $query = $this->db->query($sql,array($Category,$PartnerID,$start,$end));
               
                if($query->num_rows()>0){
                    // $i = 1;
                    foreach($query->result() as $row){
    
                        if($Category==4){
                            $sqlF = "SELECT 
                                sf.FarmerID,
                                sf.SupplychainID,
                                SUM(sp.GardenAreaHa) as GardenAreaHa,
                                SUM(sp.AnnualProduction) as AnnualProduction
                                FROM
                                view_tc_supplychain_org vso 
                                LEFT JOIN ktv_tc_supplychain_farmer sf ON sf.SupplychainID=vso.SupplychainID
                                LEFT JOIN ktv_survey_plot sp ON sp.MemberID=sf.FarmerID
                                WHERE
                                vso.ObjID=?";
                            $queryF = $this->db->query($sqlF,array($row->SupplierID));  
                            $sme = $queryF->row(); 
                            $AnnualProduction = @$sme->AnnualProduction;
                            $GardenAreaHa = @$sme->GardenAreaHa;
                            $rowAnnualProduction = @$sme->AnnualProduction;
                        }else{
                            $AnnualProduction = $row->AnnualProduction;
                            $GardenAreaHa = $row->GardenAreaHa;
                            $rowAnnualProduction = @$row->AnnualProduction;
                        }
    
                        if($Period == "half"){
                            $AnnualProduction = $AnnualProduction/2;
                            if($row->SourceCategoryID == 3){
                                $row->Tracebility = ($AnnualProduction/$row->FFBSupply)*100;
                            }
                        }else{
                            $AnnualProduction = $AnnualProduction;
                            if($row->SourceCategoryID == 3){
                                if($AnnualProduction > $row->FFBSupply){
                                    $row->Tracebility = ($row->FFBSupply /$AnnualProduction)*100;
                                } else {
                                    $row->Tracebility = ($AnnualProduction/$row->FFBSupply)*100;
                                }
                            }
                        }
                        if($row->TCPercentage > 100){
                            $row->TCPercentage = 100;
                        }
    
                        $dataArray[] = array(
                            "SupplierName" => $row->SourceName
                            ,"SupplierID" => $row->SupplierID
                            ,"MillTCID" => $row->MillTCID
                            ,"MillID" => $row->MillID
                            ,"GardenType" => $row->SourceCategory
                            ,"FFBSupply" => number_format($row->FFBSupply,2)
                            ,"Tracebility" => ($row->TCPercentage > 0 ? number_format($row->TCPercentage,2) : 0 )." %"
                            ,"Generated"    => $row->Generated
                            ,"AnnualProduction" => number_format($rowAnnualProduction,2)
                            ,"GardenAreaHa" => number_format($GardenAreaHa,2)
                        );
                    }
    
                    // echo "<pre>";
                    // print_r($dataArray);
                    // die;
                }
            }else{
                $where = "";
                if($Category == 4){
                    $where .= " AND a.KategoriKebun IN ('2','3') ";
                }
                if($Category == 1){
                    $where .= " AND a.KategoriKebun IN ('1') ";
                }
                if($Category == 2){
                    $where .= " AND a.KategoriKebun IN ('4') ";
                }
                if($Category == 3){
                    $where .= " AND a.KategoriKebun IN ('5') ";
                }
                $sql = "
                    SELECT
                        a.SupplierName SourceName
                        , a.MillTCID
                        , a.MillID
                        , a.FFBSupply
                        , a.Tracebility TCPercentage
                        , IF(a.Tracebility > 0,'Yes','No') Tracebility
                        , CASE
                            WHEN a.KategoriKebun = 1 THEN 'Estate Plasma'
                            WHEN a.KategoriKebun = 2 THEN 'Direct Smallholder'
                            WHEN a.KategoriKebun = 3 THEN 'Agent / Dealer / Vendor'
                            WHEN a.KategoriKebun = 4 THEN 'Estate Inti'
                            WHEN a.KategoriKebun = 5 THEN 'External Estate'
                            ELSE '-'
                        END as SourceCategory
                    FROM
                        `ktv_mill_tc_declaration_detail` a
                    WHERE
                    1=1
                    $where
                    AND a.MillTCDID = ?
                ";
    
                $query = $this->db->query($sql,array($MillTCDID));
    
                if($query->num_rows()>0){
                    // $i = 1;
                    foreach($query->result() as $row){
                        if($Category == 4 ){
                            $row->Tracebility = $row->TCPercentage." %";
                        }
                        $dataArray[] = array(
                            "SupplierName" => $row->SourceName
                            ,"MillTCID" => $row->MillTCID
                            ,"MillID" => $row->MillID
                            ,"GardenType" => $row->SourceCategory
                            ,"FFBSupply" => $row->FFBSupply
                            ,"Tracebility" => $row->Tracebility
                            ,"Generated"    => $row->Generated
                        );
                    }
                }
            }
        }
        
        $data["data"]    = $dataArray;

        return $data;
    }

    private function generateSqlHakAkses(){
        $sqlHakAkses = array();

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";

            //cek ktv_access_partner_mill
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_mill acc_pmi ON a.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
        }

        return $sqlHakAkses;
    }

    public function setPartnerMill($paramPost){
        $this->db->from('ktv_mill');
        $this->db->where('PartnerID', $paramPost["PartnerID"]);
        $this->db->where('MillID !=', $paramPost["MillID"]);

        if ($this->db->get()->num_rows() > 0) {
            # code...
            $results['success'] = false;
            $results['message'] = "Partner ini sudah ada di mill";
        } else {
            # code...
            $sql="UPDATE `ktv_mill` SET
                    PartnerID = ?,
                    DateUpdated = NOW(),
                    LastModifiedBy = ?
                WHERE
                    `MillID` = ?";
            $p = array(
                $paramPost["PartnerID"],
                $_SESSION['userid'],
                $paramPost["MillID"],
            );
            $query = $this->db->query($sql,$p);

            if ($query) {
                $results['success'] = true;
                $results['message'] = "Data updated";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to update data";
            }
        }
        
        return $results;
    }

    function mill_as_partner($paramPost){
        $this->db->trans_begin();

        $sql="INSERT INTO `ktv_program_partner` SET
                `PartnerName` = ?,
                `PartnerFullName` = ?,
                `PartnerProgramName` = ?,
                `StatusCode` = 'active',
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            $paramPost["MillName"],
            $paramPost["MillName"],
            $paramPost["MillName"],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $partnerID = $this->db->insert_id();

        $sql="UPDATE `ktv_mill` SET
                `SetAsPartner` = 'Yes',
                `PartnerID` = ?
              WHERE
              MillID = ?";
        $p = array(
            $partnerID,
            $paramPost["MillID"]
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
        }
    }

    public function getGridReportLocked($MillID,$Year,$start,$limit){
        $sql = "
        SELECT
            SQL_CALC_FOUND_ROWS
            sb1.SupplyBatchDate,
            sb1.SupplyOrgID,
            sb1.SupplyDestOrgID,
            sb1.SupplyBatchNumber,
            sb1.DeliveryDate,
            sb1.DestPO,
            sb1.DestWeight,
            sb1.DestDriver,
            sb1.DestTransportID,
            sb1.DestTransportNumber,
            sb1.Notes,
            st2.VolumeBruto,
            st2.VolumeNetto,
            st2.PackageNumber,             
            IF ( st2.SupplyBatchID IS NULL, 'Received', 'Sent' ) SupplyBatchStatus,
            vso1.ObjID,
            vso1.NAME AS SupplierName
        FROM
            ktv_tc_supplychain_batch sb1
            LEFT JOIN ktv_tc_supplychain_transaction st1 ON st1.SupplyBatchID = sb1.SupplyBatchID
            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID = sb1.SupplyBatchID
            AND st2.SupplyType = 'Batch'
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = sb1.SupplyDestOrgID
            LEFT JOIN view_tc_supplychain_org vso1 ON vso1.SupplychainID = sb1.SupplyOrgID
        WHERE
            vso2.`ObjID` = ?
            AND sb1.SupplyBatchStatus IN ( 'Delivered', 'Sent' )
            AND st2.SupplyTransID IS NOT NULL
            AND YEAR(sb1.DeliveryDate) = ?
        GROUP BY
            sb1.SupplyBatchID 
        ORDER BY
            'a.SupplyBatchID' 'DESC'
        LIMIT ?,?
        ";

        $query = $this->db->query($sql,array($MillID,$Year,(int)$start,(int)$limit));
        if($query->num_rows()>0){
            $data["data"]   = $query->result_array();            
            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $data['total'] = $query->row()->total;
            return $data;
        }else{
            $data["data"]   = array();
            $data["total"]  = $query->num_rows();
            return $data;
        }
    }

    public function getGridMainMillTCDeclarationManual($pSearch,$start,$limit,$sortingField,$sortingDir){
        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.MillTCDName like '%{$pSearch['textSearch']}%') ";
        }

        if($sortingField == "") $sortingField = 'MillTCDName';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "
            SELECT
                MillTCDID
                , MillTCDName 
                , DateCreated
                , MillID                 
            FROM
                `ktv_mill_tc_declaration` a
            WHERE 1=1
                AND a.MillID = '$pSearch[MillID]'
                AND a.StatusCode = 'active'
                $sqlFilter
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
        ";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function form_tc_declaration_new($MillTCDID){
        $sql = "SELECT
            tc.SourceCategory AS 'Koltiva.view.Mill.FormAddData-SourceCategory'
            , tc.SourceID AS 'Koltiva.view.Mill.FormAddData-SourceName'
            , tc.FFBSupply AS 'Koltiva.view.Mill.FormAddData-FFBSupply'
            FROM
                `ktv_mill_tc` tc
            WHERE
            tc.MillTCID = ?";
        
        $query = $this->db->query($sql,array($MillTCDID));

        $result["success"]  = true;
        $result["data"]     = $query->row();

        return $result;
    }

    function form_tc_declaration($MillID,$MillTCDID){
        $sql = "
            SELECT
                MillTCDID
                , MillTCDName 
                , DateCreated
                , MillID                 
            FROM
                `ktv_mill_tc_declaration` a
            WHERE 
                MillID = ?
                AND MillTCDID = ?
        ";
        $query = $this->db->query($sql,array($MillID,$MillTCDID));

        $data = $query->row();

        $sql = "
            SELECT
                a.MillTCDID
                , CASE
                        WHEN a.KategoriKebun = 1 THEN 'Plasma'
                        WHEN a.KategoriKebun = 2 THEN 'OtherSupplier'
                        WHEN a.KategoriKebun = 3 THEN 'OtherSupplier'
                        WHEN a.KategoriKebun = 4 THEN 'OwnedEstate'
                        WHEN a.KategoriKebun = 5 THEN 'ExternalEstate'
                        ELSE '-'
                    END as KategoriKebun
                , SUM(a.FFBSupply) FFBSupply
                , SUM(a.Tracebility) TotalTrace
                , (SUM(a.FFBSupply)/b.TotalFFBAll) * 100 ProportionFFB
                , (SUM(IF(a.Tracebility > 0,a.FFBSupply,0))/b.TotalFFBAll) * 100 TTPMILL
                , SUM(IF(a.Tracebility > 0,a.FFBSupply,0)) TPP
            FROM ktv_mill_tc_declaration_detail a
            LEFT JOIN (
                SELECT 
                    a.MillTCDID
                    , SUM(a.FFBSupply) AS TotalFFBAll
                FROM 
                    ktv_mill_tc_declaration_detail a
                GROUP BY a.MillTCDID
            ) b on b.MillTCDID = a.MillTCDID
            WHERE a.MillTCDID = ?
            GROUP BY a.KategoriKebun, MillTCDID
        ";
        $query = $this->db->query($sql,array($MillTCDID));
        $dataGrid = (array)$data;
        $TotalFFB = 0;
        if($query->num_rows()>0){
            foreach($query->result() as $row){
                $kategori = $row->KategoriKebun;
                $dataGrid["FFBProcured".$kategori]  = $row->FFBSupply;
                $dataGrid["TotalTrace".$kategori]   = $row->TotalTrace;
                $dataGrid["FFBProcuredProportion".$kategori]   = number_format($row->ProportionFFB,2);
                $dataGrid["TtpMill".$kategori]      = number_format($row->TTPMILL,2);

                $TotalFFB += $row->FFBSupply;
            }
        }
        $dataGrid["TotalFFB"] = number_format($TotalFFB,2);

        $result["success"]  = true;
        $result["data"]     = $dataGrid;

        return $result;
    }

    function submit_tc_declaration_new($post){
        if($post["SourceType"] == 1){
            $post["SourceCategory"] = 1;
        }
        if($post["SourceType"] == 2){
            $post["SourceCategory"] = 4;
        }
        if($post["SourceType"] == 3){
            $post["SourceCategory"] = 5;
        }
        if($post["SourceType"] == 4){
            $post["SourceCategory"] = $post["SourceCategory"];
        }

        list($SourceName,$TCPercentage,$AnnualProduction) = $this->getSupplierData($post["SourceName"],$post["SourceCategory"]);
        $MillID = $this->getMillID($post["PartnerID"]);
        
        if($post["Period"] == "full"){
            $month = "12";
            if($post["SourceCategory"] == 3){
                $TCPercentage = ($AnnualProduction/$post["FFBSupply"])*100;
            }
        } elseif($post["Period"] == "half2"){
            $month = "07";
            if($post["SourceCategory"] == 3){
                $AnnualProduction = $AnnualProduction/2;
                $TCPercentage = ($AnnualProduction/$post["FFBSupply"])*100;
            }
        } else{
            $month = "06";
            if($post["SourceCategory"] == 3){
                $AnnualProduction = $AnnualProduction/2;
                $TCPercentage = ($AnnualProduction/$post["FFBSupply"])*100;
            }
        }
        if($TCPercentage > 100){
            $TCPercentage = 100;
        }

        $new_date = $post["Year"]."-".$month."-30";
        $post["SupplyBatchDate"]    = date("Y-m-d H:i:s", strtotime($new_date));
        $post["DeliveryDate"]       = date("Y-m-d H:i:s", strtotime($new_date));
        $post["LockDate"]           = date("Y-m-d H:i:s");
        $post["DateCreated"]        = date("Y-m-d H:i:s");
        $post["CreatedBy"]          = $_SESSION["userid"];
        $post["SourceID"]           = $post["SourceName"];
        $post["SourceName"]         = $SourceName;
        $post["MillID"]             = $MillID;
        $post["TCPercentage"]       = $TCPercentage;
        $post["LockStatus"]         = 1;
        $post["Generated"]          = 'No';

        if($post["MillTCID"] == ""){
            // $sqlcheck = "SELECT
            //         *
            //     FROM
            //         ktv_mill_tc a
            //     WHERE
            //         YEAR(a.SupplyBatchDate) = ?
            //     AND
            //         a.MillID = ?
            //     AND
            //         a.SourceID = ?
            //     AND
            //         a.SourceType = ?
            // ";

            // $qcheck = $this->db->query($sqlcheck,array($post["Year"],$MillID,$post["SourceID"],$post["SourceType"]));
            // if($qcheck->num_rows()>0){
            //     return array("success"=>false,"message"=>lang("Data Already Exist"));
            // }

            $sqlcheck = "SELECT
                    *
                FROM
                    ktv_mill_tc a
                WHERE
                    a.SupplyBatchDate = ?
                AND
                    a.MillID = ?
                AND
                    a.SourceID = ?
                AND
                    a.SourceType = ?
            ";

            $qcheck = $this->db->query($sqlcheck,array($post["SupplyBatchDate"],$MillID,$post["SourceID"],$post["SourceType"]));
            if($qcheck->num_rows()>0){
                return array("success"=>false,"message"=>lang("Data Already Exist"));
            }

            unset($post["Year"]);
            unset($post["PartnerID"]);
            unset($post["Period"]);
            
            $query = $this->db->insert("ktv_mill_tc",$post);
        }else{
            unset($post["Year"]);
            unset($post["PartnerID"]);
            unset($post["Period"]);

            $this->db->where("MillTCID",$post["MillTCID"]);
            $query = $this->db->update("ktv_mill_tc",$post);
        }        

        return array("success"=>true,"message"=>lang("Data Saved"));
    }

    function getMillID($PartnerID){
        $sql = "SELECT
            MillID
        FROM
            ktv_mill m
        WHERE
            m.PartnerID = ?
        ";

        $query = $this->db->query($sql,array($PartnerID));

        return $query->row()->MillID;
    }

    function getSupplierData($SID,$SourceCategory){
        if($SourceCategory != 2 AND $SourceCategory != 3){
            $table = "ktv_survey_plot_sme";
            $sql = "SELECT
                        c.SupplychainID,
                        IFNULL( me.agCompanyName, c.`Name` ) MemberName,
                        SUM( ps.AnnualProduction ) AnnualProduction,
                    IF
                        ( ps.Latitude <> 0 AND ps.Longitude <> 0 OR m.Latitude <> 0 AND m.Longitude <> 0, 100, 0 ) TTPTrace 
                    FROM
                        view_tc_supplychain_org c
                        LEFT JOIN ktv_members_extension me ON me.MemberID = c.ObjID
                        LEFT JOIN ktv_members m ON m.MemberID = c.ObjID
                        LEFT JOIN ktv_survey_plot_sme ps ON ps.MemberID = m.MemberID AND ps.SurveyNr = 0
                    WHERE
                        c.SupplychainID = ?
                    GROUP BY
                        m.MemberID
                    ORDER BY ps.Latitude DESC";
        }else if($SourceCategory == 3){
            $table = "ktv_survey_plot";
            $sql = "SELECT
                c.SupplychainID,
                IFNULL( me.agCompanyName, c.`Name` ) MemberName,
                a.total_kebun,
                a.AnnualProduction,
                b.AnnualProductionTrace,
                b.total_kebun_trace,
                ( b.total_kebun_trace / a.total_kebun ) * 100 TTPTrace 
            FROM
                view_tc_supplychain_org c
                LEFT JOIN ktv_members_extension me ON me.MemberID = c.ObjID
                LEFT JOIN (
                    SELECT
                        a.ObjID,
                        a.SupplychainID,
                        a.`Name`,
                        count(DISTINCT CONCAT(b.PlotNr,b.MemberID)) total_kebun,
                        SUM( b.AnnualProduction ) AnnualProduction 
                    FROM
                        view_tc_supplychain_org a
                        LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.SupplychainID = a.SupplychainID
                        LEFT JOIN ktv_survey_plot b ON b.MemberID = tsf.FarmerID 
                    WHERE
                        a.SupplychainID = $SID
                    GROUP BY
                        tsf.SupplychainID
                ) a ON a.SupplychainID = c.SupplychainID
                LEFT JOIN (
                SELECT
                    a.SupplychainID,
                    a.ObjID,
                    a.`Name`,
                    count(DISTINCT CONCAT(b.PlotNr,b.MemberID)) total_kebun_trace,
                    SUM( b.AnnualProduction ) AnnualProductionTrace 
                FROM
                    view_tc_supplychain_org a
                    LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.SupplychainID = a.SupplychainID
                    LEFT JOIN ktv_survey_plot b ON b.MemberID = tsf.FarmerID 
                WHERE
                    b.Latitude IS NOT NULL  
                    AND a.SupplychainID = $SID
                    OR b.Longitude IS NOT NULL 
                    AND a.SupplychainID = $SID
                GROUP BY
                    b.MemberID 
                ) b ON b.SupplychainID = a.SupplychainID 
            WHERE
                c.SupplychainID = ?
            ";
        }else if($SourceCategory == 2){
            $table = "ktv_survey_plot";
            $sql = "SELECT
                        c.MemberID SupplychainID,
                        c.MemberName,
                        a.total_kebun,
                        a.AnnualProduction,
                        b.AnnualProductionTrace,
                        b.total_kebun_trace,
                        ( b.total_kebun_trace / a.total_kebun ) * 100 TTPTrace 
                    FROM
                        ktv_members c
                        LEFT JOIN (
                            SELECT
                                a.MemberID,
                                a.MemberName,
                                count(DISTINCT CONCAT(b.PlotNr,b.MemberID)) total_kebun,
                                SUM( b.AnnualProduction ) AnnualProduction 
                            FROM
                                ktv_members a
                                LEFT JOIN ktv_survey_plot b ON b.MemberID = a.MemberID 
                            WHERE
                                a.MemberID = '$SID'
                            GROUP BY
                                a.MemberID
                        ) a ON a.MemberID = c.MemberID
                        LEFT JOIN (
                            SELECT
                                a.MemberID,
                                a.MemberName,
                                count(DISTINCT CONCAT(b.PlotNr,b.MemberID)) total_kebun_trace,
                                SUM( b.AnnualProduction ) AnnualProductionTrace
                            FROM
                                ktv_members a
                                LEFT JOIN ktv_survey_plot b ON b.MemberID = a.MemberID 
                            WHERE
                                a.MemberID = '$SID'
                                AND b.Latitude IS NOT NULL  
                                OR
                                a.MemberID = '$SID'
                                AND b.Longitude IS NOT NULL 
                            GROUP BY
                                a.MemberID
                        ) b ON b.MemberID = a.MemberID 
                    WHERE
                        c.MemberID = ?
        
            ";
        }

        $query = $this->db->query($sql,array($SID));
        if($query->num_rows()>0){
            $row = $query->row();

            return array($row->MemberName,$row->TTPTrace,$row->AnnualProduction);
        }
        return array();
    }

    function submit_tc_declaration($post){
        $this->db->trans_start();
        if($post["opsiDisplay"] == "insert"){
            $post_header = array(
                "MillTCDName"   => $post["MillTCDName"],
                "MillID"        => $post["MillID"],
                "CreatedBy"     => $_SESSION["userid"]
            );
            $this->db->insert("ktv_mill_tc_declaration",$post_header);
            $id = $this->db->insert_id();
            $detail = json_decode($post["ContactID"]);
            $dataDetail = array();
            if(count($detail)>0){
                foreach($detail as $row){
                    $value = array(
                        "MillTCDID" => $id,
                        "SupplierName" => $row->SupplierName,
                        "KategoriKebun" => $row->KategoriKebun,
                        "FFBSupply" => $row->FFBSupply,
                        "Tracebility" => $row->Tracebility,
                        "CreatedBy" => $_SESSION["userid"]
                    );
                    array_push($dataDetail,$value);
                }
            }
            $this->db->insert_batch("ktv_mill_tc_declaration_detail",$dataDetail);
        }
        if($post["opsiDisplay"] == "update" AND $post["MillTCDID"] != ""){
            $post_header = array(
                "MillTCDName"   => $post["MillTCDName"],
                "MillID"        => $post["MillID"],
                "UpdatedBy"     => $_SESSION["userid"],
                "DateUpdated"   => date("Y-m-d H:i:s")
            );
            $this->db->where("MillTCDID",$post["MillTCDID"]);
            $this->db->update("ktv_mill_tc_declaration",$post_header);

            $this->db->where("MillTCDID",$post["MillTCDID"]);
            $this->db->delete("ktv_mill_tc_declaration_detail");
            $detail = json_decode($post["ContactID"]);
            $dataDetail = array();
            if(count($detail)>0){
                foreach($detail as $row){
                    $value = array(
                        "MillTCDID" => $post["MillTCDID"],
                        "SupplierName" => $row->SupplierName,
                        "KategoriKebun" => $row->KategoriKebun,
                        "FFBSupply" => $row->FFBSupply,
                        "Tracebility" => $row->Tracebility,
                        "CreatedBy" => $_SESSION["userid"]
                    );
                    array_push($dataDetail,$value);
                }
            }
            $this->db->insert_batch("ktv_mill_tc_declaration_detail",$dataDetail);
        }

        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE)
        {
            return true;  
        }
        return true;
    }

    function getSupplierList($MillTCDID){
        $sql = "
        SELECT
            id
            , MillTCDID
            , SupplierName
            , KategoriKebun
            , CategoryName KategoriKebunName
            , FFBSupply
            , Tracebility        
        FROM
            `ktv_mill_tc_declaration_detail` a
        LEFT JOIN
            ref_tc_supplybase_category b on b.SupplybaseCategoryID = a.KategoriKebun
        WHERE MillTCDID = ?
        ";
        $data = $this->db->query($sql,array($MillTCDID))->result_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $DataForm[$key] = $value;
        }        

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function getListAllMill(){
        $sql = "SELECT
                MillID id
                , MillName label
            FROM
                ktv_mill
            WHERE StatusCode = 'active'
        ";

        $query = $this->db->query($sql);
        return array("data"=>$query->result_array());
    }

    public function getListAllSME($MillID = null){
        $where = '';
        if($MillID != '' || $MillID != null){
            $where = " AND m.MillID = '$MillID'";
        }
        $sql = "SELECT
                a.MemberID id,
                mx.agCompanyName label
            FROM
                ktv_members a
            LEFT JOIN 
                ktv_tc_supplychain_org o on a.MemberID = o.ObjID
            LEFT JOIN 
                ktv_tc_supplychain_org_rel orel on orel.ChildID = o.SupplychainID
            LEFT JOIN 
                ktv_tc_supplychain_org op on orel.ParentID = op.SupplychainID
            LEFT JOIN 
                ktv_mill m on m.MillID = op.ObjID
            LEFT JOIN 
                ktv_members_extension mx on mx.MemberID = a.MemberID
            WHERE
            1=1
            $where
                AND o. ObjType = 'agent'
                AND m.StatusCode = 'active'
        ";

        $query = $this->db->query($sql);
        return array("data"=>$query->result_array());
    }

    public function getListAllSMEStaff($MillID = null){
        $sql = "SELECT
            vso.SupplychainID id
            , vso.`Name` label
        FROM
            `view_tc_supplychain_org` vso
        /*WHERE
            vso.ObjType = 'agent'*/
        ";

        $query = $this->db->query($sql);
        return array("data"=>$query->result_array());
    }

    public function getGridMainMill($pSearch,$start,$limit,$sortingField,$sortingDir){
        $sqlFilter = "";
        $sqlBu     = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if($pSearch['prov'] != ""){
            $sqlFilter .= " AND e.ProvinceID = ".$pSearch['prov'];
        }

        if($pSearch['kab'] != ""){
            $sqlFilter .= " AND f.DistrictID = ".$pSearch['kab'];
        }

        if($pSearch['kec'] != ""){
            $sqlFilter .= " AND d.SubDistrictID = ".$pSearch['kec'];
        }

        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.MillName like '%{$pSearch['textSearch']}%' OR a.MillDisplayID like '%{$pSearch['textSearch']}%' ) ";
        }

        if($pSearch['rowStatusPerusahaan'] == "true"){
            $sqlFilter .= " AND a.Status = '{$pSearch['cmbStatusPerusahaan']}' ";
        }

        if($pSearch['rowTahunTerbentuk'] == "true"){
            $sqlFilter .= " AND a.Year {$pSearch['cmbOpTahunTerbentuk']} '{$pSearch['textTahunTerbentuk']}' ";
        }

        if($pSearch['rowPhone'] == "true"){
            $sqlFilter .= " AND a.Phone LIKE '{$pSearch['textPhone']}' ";
        }

        if($pSearch['rowHavePhoto'] == "true"){
            if($pSearch['cmbHavePhoto'] == "Yes"){
                $sqlFilter .= " AND a.Photo != '' AND a.Photo IS NOT NULL ";
            }
            if($pSearch['cmbHavePhoto'] == "No"){
                $sqlFilter .= " AND (a.Photo = '' OR a.Photo IS NULL) ";
            }
        }

        if($pSearch['rowTotalPermanentEmployee'] == "true"){
            $sqlFilter .= " AND (a.`PermanentEmployeeMale` + a.`PermanentEmployeeFemale`) {$pSearch['cmbOpTotalPermanentEmployee']} '{$pSearch['textTotalPermanentEmployee']}' ";
        }

        if($pSearch['pAdvInternalProgram'] != ""){
            $sqlFilter .= " AND millbu.BusinessUnitID = '{$pSearch['pAdvInternalProgram']}' ";
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        if($sortingField == "") $sortingField = 'Name';
        if($sortingDir == "") $sortingDir = 'ASC';

        ($_SESSION['business_unit'] != "") ? $sqlBu = " AND millbu.BusinessUnitID IN ({$_SESSION['business_unit']}) " : $sqlBu = "";

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MillID` AS id
                , a.`MillDisplayID`
                , a.`MillName` AS `Name`
                , a.`Address`
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , IFNULL(a.DateUpdated,a.`DateCreated`) AS LastUpdated
                , e.Province
                , f.District
                , CASE
                    WHEN a.Status = '1' THEN '".lang('Sole Proprietorship')."'
                    WHEN a.Status = '2' THEN '".lang('Partnership')."'
                    WHEN a.Status = '3' THEN '".lang('Limited Partnership')."'
                    WHEN a.Status = '4' THEN '".lang('Limited Liability Company')."'
                    WHEN a.Status = '5' THEN '".lang('Corporation')."'
                    WHEN a.Status = '6' THEN '".lang('Cooperative')."'
                    WHEN a.Status = '7' THEN '".lang('Foundation')."'
                    WHEN a.Status = '8' THEN '".lang('Association')."'
                    WHEN a.Status = '9' THEN '".lang('State Owned')."'
                END AS StatusPerusahaan
                , a.`Year` AS TahunTerbentuk
                , a.`Alias`
                , a.`Phone`
                , (a.`PermanentEmployeeMale` + a.`PermanentEmployeeFemale`) AS TotalPermanentEmployee
                , g.GroupName
                , a.SetAsPartner
                , a.CompanyName
                , GROUP_CONCAT(sub_sme.MemberName, ',') AS SMEName
                , COUNT(DISTINCT kspsm.PlotNr) AS NrPlantation
                , sub_farmer.NrFarmer AS NrFarmer
                , GROUP_CONCAT(DISTINCT a.Latitude, ',', a.Longitude) AS GPS
            FROM
                ktv_mill a
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
                LEFT JOIN ktv_mill_group g ON g.MillGroupID = a.MillGroupID
                LEFT JOIN ktv_survey_plot_status_mill kspsm ON kspsm.MillID = a.MillID
                LEFT JOIN ktv_mill_business_unit millbu ON millbu.MillID = a.MillID

                LEFT JOIN(
                    SELECT
                        km.`MillID`
                        , COUNT(distinct kmember.`MemberID`) AS NrFarmer
                    FROM
                        ktv_mill km
                        LEFT JOIN ktv_program_partner kpp on kpp.`PartnerID` = km.`PartnerID`
                        LEFT JOIN ktv_access_partner_member kapm on kapm.`apmPartnerID` = kpp.`PartnerID`
                        LEFT JOIN ktv_members kmember on kmember.`MemberID` = kapm.`apmMemberID`
                        LEFT JOIN ktv_member_role kmr ON kmember.MemberID = kmr.MemberID 
                        LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID`= kmr.`MRoleID`
                    WHERE
                        rm.`MRoleType` = 'Farmer'
                        AND km.`StatusCode` = 'active'
                        AND kmember.`StatusCode` = 'active'
                    GROUP BY
                        km.`MillID`
                ) sub_farmer ON sub_farmer.MillID = a.`MillID`

                LEFT JOIN(
                    SELECT
                        km.`MillID`
                        , GROUP_CONCAT(DISTINCT kmember.`MemberName` ) AS MemberName
                    FROM ktv_mill km
                    LEFT JOIN ktv_tc_supplychain_org ktso2 ON ktso2.`ObjID` =  km.`MillID` AND ktso2.`ObjType` = 'mill'
                    LEFT JOIN ktv_tc_supplychain_org_rel ktsor ON ktsor.`ParentID` = ktso2.`SupplychainID`
                    LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`SupplychainID` = ktsor.`ChildID`
                    LEFT JOIN ktv_members kmember ON kmember.`MemberID` = ktso.`ObjID` AND ktso.`ObjType` = 'agent' AND kmember.`StatusCode` = 'active'
                    LEFT JOIN ktv_member_role kmr ON kmember.MemberID = kmr.MemberID 
                    LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID`= kmr.`MRoleID` AND rm.`MRoleType` = 'Agent'
                    WHERE
                        km.`StatusCode` = 'active'
                        AND ktsor.`StatusCode` = 'active'    
                    GROUP BY
                        km.`MillID`
                ) sub_sme ON sub_sme.MillID = a.`MillID`

                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlBu
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MillID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        //generate information grid result (begin)
        if($sortingDir == 'ASC'){
            $sortingInfo = 'ascending';
        }
        if($sortingDir == 'DESC'){
            $sortingInfo = 'descending';
        }

        $infoFilter = '';
        foreach ($pSearch as $key => $value) {
            if($value != ""){
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Province').'</li>';
                    break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('District').'</li>';
                    break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Kecamatan').'</li>';
                    break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('ID / Name').'</li>';
                    break;
                }
            }

            if($value == "true"){
                switch ($key) {
                    case 'rowStatusPerusahaan':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Status Perusahaan').'</li>';
                    break;
                    case 'rowTahunTerbentuk':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Tahun Terbentuk').'</li>';
                    break;
                    case 'rowPhone':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Phone').'</li>';
                    break;
                    case 'rowTotalPermanentEmployee':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Total Permanent Employee').'</li>';
                    break;
                    case 'rowHavePhoto':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Have Photo').'</li>';
                    break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>'.$query->row()->total.' '.lang('datas, Sorted by').' '.lang($sortingField).' '.$sortingInfo.'</li>
                                    '.$infoFilter.'
                                </ul>
                            </div>';
        //generate information grid result (end)

        return $result;
    }

    public function getMillBasicDataForm($MillID){
        $sql="SELECT
                a.`MillID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillID\",
                a.`MillDisplayID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillDisplayID\",
                a.`MillName` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillName\",
                a.`CompanyName` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-CompanyName\",
                a.`MillGroupID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillGroup\",
                a.`Address` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Address\",
                SUBSTR(a.`VillageID`,1,2) AS \"Province\",
                SUBSTR(a.`VillageID`,1,4) AS \"District\",
                SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\",
                a.`VillageID` AS \"Village\",
                a.`Status` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Status\",
                a.`Year` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Year\",
                a.`Alias` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Alias\",
                a.`Phone` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Phone\",
                a.`PermanentEmployeeMale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeMale\",
                a.`PermanentEmployeeFemale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeFemale\",
                a.`TemporaryEmployeeMale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeMale\",
                a.`TemporaryEmployeeFemale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeFemale\",
                IFNULL(a.Latitude, ST_Latitude(a.`LatLong`)) AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Latitude\",
                IFNULL(a.Longitude, ST_Longitude(a.`LatLong`)) AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Longitude\",
                a.`Elevation` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Elevation\",
                a.`Photo` AS PhotoSrc,
                a.LocationPhoto,
                a.PartnerID,
                a.Capacity,
                a.`Capacity` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Capacity\",
                a.`PlasmaFarmer` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-PlasmaFarmer\",
                a.`EstimatedSmallholderFarmer` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-EstimatedSmallholderFarmer\",
                a.`SocializationStatus` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatus\",
                a.`NDASent` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASent\",
                a.`NDAAgree` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgree\",
                a.`NDASigned` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASigned\",
                a.`ParticipationStatus` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatus\",
                a.`VisitDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-VisitDate\",
                a.`RecruitDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-RecruitDate\",
                a.`TrainingDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-TrainingDate\",
                a.`SurveyDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SurveyDate\",
                a.`HeadQuarterAddress` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-HeadQuarterAddress\",
                a.`SocializationStatusDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate\",
                a.`NDASentDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate\",
                a.`NDAAgreeDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate\",
                a.`NDASignedDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate\",
                a.`ParticipationStatusDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate\",
                b.`SupplychainID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID\",
                b.`WorkHour` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-WorkHour\",
                b.`ProductionCapacity` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-ProductionCapacity\"
            FROM
                `ktv_mill` a
            LEFT JOIN
                `ktv_tc_supplychain_org` b ON b.ObjType='mill' AND b.ObjID=a.MillID  AND b.StatusCode = 'active'  AND b.ObjType = 'mill'
            WHERE
                a.`MillID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $MillID));
        $data = $query->row_array();

        $sqlGetInternalProgram   = "SELECT a.BusinessUnitID 
                                    FROM ktv_mill_business_unit AS a
                                    LEFT JOIN ktv_ref_bu_internal_external AS b ON a.BusinessUnitID = b.BuInExID
                                    WHERE a.MillID = ? AND b.BuInExType = 'Internal'";
        $queryGetInternalProgram = $this->db->query($sqlGetInternalProgram, array((int) $MillID));
        $dataGetInternalProgram  = $queryGetInternalProgram->result_array();

        if(!empty($dataGetInternalProgram)){           
            foreach ($dataGetInternalProgram as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.Mill.FormMainMill-FormBasicData-CmbInternalProgram";
                $data[$keyNewx][] = $arrval["BusinessUnitID"];
            }
        }

        $sqlGetExternalProgram   = "SELECT a.BusinessUnitID 
                                    FROM ktv_mill_business_unit AS a
                                    LEFT JOIN ktv_ref_bu_internal_external AS b ON a.BusinessUnitID = b.BuInExID
                                    WHERE a.MillID = ? AND b.BuInExType = 'External'";
        $queryGetExternalProgram = $this->db->query($sqlGetExternalProgram, array((int) $MillID));
        $dataGetExternalProgram  = $queryGetExternalProgram->result_array();

        if(!empty($dataGetExternalProgram)){           
            foreach ($dataGetExternalProgram as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.Mill.FormMainMill-FormBasicData-CmbExternalProgram";
                $data[$keyNewx][] = $arrval["BusinessUnitID"];
            }
        }

        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['PhotoSrcPath'] = 'images/mill/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'images/mill/'.$data["Province"].'/'.$data['PhotoSrc'];
        }

        if($this->awsfileupload->doesObjectExist($data['LocationPhoto']) == true) {
            $data['LocationPhotoPath'] = $data['LocationPhoto'];
            $data['LocationPhoto'] = $this->config->item('CTCDN')."/".$data['LocationPhoto'];
        }else{
            $data['LocationPhotoPath'] = 'images/mill_location/'.$data["Province"].'/'.$data['LocationPhoto'];
            $data['LocationPhoto'] = base_url().'images/mill_location/'.$data["Province"].'/'.$data['LocationPhoto'];
        }

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getMillBasicDataFormNew($PartnerID){
        $sql="SELECT
                a.`MillID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillID\",
                a.`MillDisplayID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillDisplayID\",
                a.`MillName` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillName\",
                a.`CompanyName` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-CompanyName\",
                a.`MillGroupID` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-MillGroup\",
                a.`Address` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Address\",
                SUBSTR(a.`VillageID`,1,2) AS \"Province\",
                SUBSTR(a.`VillageID`,1,4) AS \"District\",
                SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\",
                a.`VillageID` AS \"Village\",
                a.`Status` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Status\",
                a.`Year` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Year\",
                a.`Alias` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Alias\",
                a.`Phone` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Phone\",
                a.`PermanentEmployeeMale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeMale\",
                a.`PermanentEmployeeFemale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeFemale\",
                a.`TemporaryEmployeeMale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeMale\",
                a.`TemporaryEmployeeFemale` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeFemale\",
                a.`Latitude` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Latitude\",
                a.`Longitude` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Longitude\",
                a.`Elevation` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Elevation\",
                a.`Photo` AS PhotoSrc,
                a.LocationPhoto,
                a.PartnerID,
                a.Capacity,
                a.`Capacity` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-Capacity\",
                a.`PlasmaFarmer` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-PlasmaFarmer\",
                a.`EstimatedSmallholderFarmer` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-EstimatedSmallholderFarmer\",
                a.`SocializationStatus` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatus\",
                a.`NDASent` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASent\",
                a.`NDAAgree` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgree\",
                a.`NDASigned` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASigned\",
                a.`ParticipationStatus` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatus\",
                a.`VisitDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-VisitDate\",
                a.`RecruitDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-RecruitDate\",
                a.`TrainingDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-TrainingDate\",
                a.`SurveyDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SurveyDate\",
                a.`HeadQuarterAddress` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-HeadQuarterAddress\",
                a.`SocializationStatusDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate\",
                a.`NDASentDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate\",
                a.`NDAAgreeDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate\",
                a.`NDASignedDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate\",
                a.`ParticipationStatusDate` AS \"Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate\",
                b.`ProductionCapacity`
            FROM
                `ktv_mill` a
            LEFT JOIN
                `ktv_tc_supplychain_org` b ON b.ObjType='mill' AND b.ObjID=a.MillID  AND b.StatusCode = 'active' AND b.ObjType = 'mill'
            WHERE
                a.`PartnerID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $PartnerID));
    
        $data = $query->row_array();

        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['PhotoSrcPath'] = 'images/mill/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'images/mill/'.$data["Province"].'/'.$data['PhotoSrc'];
        }

        if($this->awsfileupload->doesObjectExist($data['LocationPhoto']) == true) {
            $data['LocationPhotoPath'] = $data['LocationPhoto'];
            $data['LocationPhoto'] = $this->config->item('CTCDN')."/".$data['LocationPhoto'];
        }else{
            $data['LocationPhotoPath'] = 'images/mill_location/'.$data["Province"].'/'.$data['LocationPhoto'];
            $data['LocationPhoto'] = base_url().'images/mill_location/'.$data["Province"].'/'.$data['LocationPhoto'];
        }

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getMillDetailPrint($MillID){
        $sql="SELECT
                a.`MillID`,
                a.`MillDisplayID`,
                a.`MillName`,
                a.`Address`,
                a.`VillageID`,
                a.`Status`,
                a.`Year`,
                a.`Alias`,
                a.`Phone`,
                e.Province,
                f.District,
                d.SubDistrict,
                c.Village,
                a.`PermanentEmployeeMale`,
                a.`PermanentEmployeeFemale`,
                a.`TemporaryEmployeeMale`,
                a.`TemporaryEmployeeFemale`,
                a.`Latitude`,
                a.`Longitude`,
                a.`Elevation`,
                a.`Photo`,
                a.`PartnerID`,
                a.`Capacity`,
                a.`PlasmaFarmer`,
                a.`EstimatedSmallholderFarmer`,
                a.`LocationPhoto`
            FROM
                `ktv_mill` a
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.`VillageID`,1,7) = d.`SubDistrictID`
                LEFT JOIN ktv_province e ON SUBSTR(a.`VillageID`,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.`VillageID`,1,4) = f.DistrictID
            WHERE
                a.`MillID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $MillID));
        return $query->row_array();
    }

    public function getMillNrOfStaff($MillID){
        $sql="SELECT
                COUNT(a.`StaffID`) AS BANYAK
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
            WHERE
                a.`ObjType` = 'mill'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql, array((int) $MillID));
        $data = $query->row_array();
        return $data['BANYAK'];
    }

    public function genMillID($VillageID,$prefixId='MI'){
        //MillID
        $sql="SELECT
                a.`MillID`
            FROM
                ktv_mill a
            ORDER BY a.`MillID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if($data['MillID'] != ""){
            $return['MillID'] = $data['MillID'] + 1;
        }else{
            $return['MillID'] = 1;
        }

        //MillDisplayID
        $awalan = $prefixId.substr($VillageID,0,7);
        $sql="SELECT
                a.`MillDisplayID`
            FROM
                ktv_mill a
            WHERE
                a.`MillDisplayID` LIKE '$awalan%'
            ORDER BY a.`MillDisplayID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if($data['MillDisplayID'] != ""){
            $temp = (int) substr($data['MillDisplayID'],-4);
            $temp++;

            switch (strlen($temp)) {
                case '1':
                    $temp = $awalan."000".$temp;
                break;
                case '2':
                    $temp = $awalan."00".$temp;
                break;
                case '3':
                    $temp = $awalan."0".$temp;
                break;
                default:
                    $temp = $awalan.$temp;
                break;
            }
            $return['MillDisplayID'] = $temp;
        }else{
            $return['MillDisplayID'] = $awalan."0001";
        }

        return $return;
    }

    public function insertMill($varPost){
        $this->db->trans_begin();

        $this->load->model('grower/mgrower');

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }

        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeMale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeFemale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeFemale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeMale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeFemale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeFemale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Capacity'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Capacity']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PlasmaFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PlasmaFarmer']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-EstimatedSmallholderFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-EstimatedSmallholderFarmer']);
        //rapikan variable post (end)

        //generate MemberID dan MemberDisplayID
        $id = $this->genMillID($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Village'],'MI');

        $uid = $this->mgrower->getUID();

        $p = array(
            $id['MillID'],
            $id['MillDisplayID'],
            $uid,
            $uid,
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillName'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-CompanyName'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillGroup'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Address'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Village'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Status'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Year'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Alias'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Phone'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeMale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeFemale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeMale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeFemale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Latitude'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Longitude'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Elevation'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Capacity'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PlasmaFarmer'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-EstimatedSmallholderFarmer'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SocializationStatus'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASent'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDAAgree'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASigned'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-ParticipationStatus'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-VisitDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-RecruitDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TrainingDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SurveyDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-HeadQuarterAddress'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SocializationStatusDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASentDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDAAgreeDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASignedDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-ParticipationStatusDate']
        );
        $sql="INSERT INTO `ktv_mill` SET
                `MillID` = ?,
                `MillDisplayID` = ?,
                `MillUID` = ?,
                `uid` = ?,
                `MillName` = ?,
                `CompanyName` = ?,
                `MillGroupID` = ?,
                `Address` = ?,
                `VillageID` = ?,
                `Status` = ?,
                `Year` = ?,
                `Alias` = ?,
                `Phone` = ?,
                `PermanentEmployeeMale` = ?,
                `PermanentEmployeeFemale` = ?,
                `TemporaryEmployeeMale` = ?,
                `TemporaryEmployeeFemale` = ?,
                `Latitude` = ?,
                `Longitude` = ?,
                `Elevation` = ?,
                `Capacity` = ?,
                `PlasmaFarmer` = ?,
                `EstimatedSmallholderFarmer` = ?,
                `SocializationStatus` = ?,
                `NDASent` = ?,
                `NDAAgree` = ?,
                `NDASigned` = ?,
                `ParticipationStatus` = ?,
                `VisitDate` = ?,
                `RecruitDate` = ?,
                `TrainingDate` = ?,
                `SurveyDate` = ?,
                `HeadQuarterAddress` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?,
                `SocializationStatusDate` = ?,
                `NDASentDate` = ?,
                `NDAAgreeDate` = ?,
                `NDASignedDate` = ?,
                `ParticipationStatusDate` = ?";
        $query = $this->db->query($sql,$p);

        if($query){
            if($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Latitude'] != "" && $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Longitude'] != "") {

                $LatitudeProses = (float) $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Latitude'];
                $LongitudeProses = (float) $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Longitude'];
                
                //Check Latitude
                if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                    //Cek valid tidak koordinatnya
                    $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                    $DataCekKoordinat = $this->db->query($sql2)->row_array();
                    
                    if ($DataCekKoordinat['HasilCek'] == "1") {
                        $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                        $sql2 = "UPDATE ktv_mill a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`MillID` = ?
                                LIMIT 1";
                        $p = array(
                            $id['MillID']
                        );
                        $query = $this->db->query($sql2,$p);
                    }

                }
            }
        }

        //insert hak akses data control (Begin)
        $sql = "SELECT
                PartnerIDRef
        FROM
            ktv_partner_access_setting 
        WHERE
            PartnerIDCanView = ? AND PartnerIDRef <> 1
        GROUP BY
            PartnerIDRef";
        $query = $this->db->query($sql, array($_SESSION["PartnerID"]));

        if($query->num_rows()>0){
            foreach($query->result_array() as $rows){
                $sql = "INSERT INTO `ktv_access_partner_mill` SET
                        `apmiPartnerID` = ?,
                        `apmiMillID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $rows["PartnerIDRef"],
                    $id['MillID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            }
        }

        //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
        if ($_SESSION['PartnerID'] != "1") {
            //insertkan ke Koltiva
            $sql = "INSERT INTO `ktv_access_partner_mill` SET
                    `apmiPartnerID` = ?,
                    `apmiMillID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                '1',
                $id['MillID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }
        //insert hak akses data control (End)

        $arrMillInternalProgram = array();
        $arrMillInternalProgram = $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-CmbInternalProgram'];
        if(!empty($arrMillInternalProgram)){
            foreach($arrMillInternalProgram as $k => $MillInternalProgramID){
                $sqlInternalProgram="INSERT INTO `ktv_mill_business_unit` SET
                    `MillID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pInternalProgram = array(
                    $id['MillID'],
                    $MillInternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlInternalProgram,$pInternalProgram);
            }
           
        }
        //insert internal program ======================================================================== (end)

        $arrMillExternalProgram = array();
        $arrMillExternalProgram = $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-CmbExternalProgram'];
        if(!empty($arrMillExternalProgram)){
            foreach($arrMillExternalProgram as $k => $MillExternalProgramID){
                $sqlExternalProgram="INSERT INTO `ktv_mill_business_unit` SET
                    `MillID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pExternalProgram = array(
                    $id['MillID'],
                    $MillExternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlExternalProgram,$pExternalProgram);
            }
           
        }
        //insert external program ======================================================================== (end)

        if($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SupplychainID'] != ""){
            $sql="UPDATE `ktv_tc_supplychain_org` SET
            `ProductionCapacity` = ?,
            `WorkHour` = ?,
            `DateUpdated` = NOW(),
            `LastModifiedBy` = ?
            WHERE
                `SupplychainID` = ".$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SupplychainID']."
            LIMIT 1";
            
            $p = array(
                $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-ProductionCapacity'],
                $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-WorkHour'],
                $_SESSION['userid'],
            );
            
            $query = $this->db->query($sql,$p);
        }
    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['MillID'] = $id['MillID'];

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PhotoOld'] != ""){
                $file = explode("images/mill/temp/",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PhotoOld']);
                // //Insert ada photonya pakai aws
                if(file_exists('images/mill/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/mill/temp/'.$file[1],$file[1],AWSS3_MILL_LOGO_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file("/".$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PhotoOld']);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                }

                $sql="UPDATE ktv_mill a SET
                        a.`Photo` = ?
                    WHERE
                        a.`MillID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MillID']
                );
                $query = $this->db->query($sql,$p);
            }

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoOld'] != ""){
                $file = explode("images/mill_location/temp/",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoOld']);
                // //Insert ada photonya pakai aws
                if(file_exists('images/mill_location/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/mill_location/temp/'.$file[1],$file[1],AWSS3_MILL_LOCATION_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file("/".$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoOld']);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                }

                $sql="UPDATE ktv_mill a SET
                        a.`LocationPhoto` = ?
                    WHERE
                        a.`MillID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MillID']
                );
                $query = $this->db->query($sql,$p);
            }

        }

        return $results;
    }

    public function updateMill($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }

        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeMale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeFemale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeFemale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeMale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeFemale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeFemale']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Capacity'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Capacity']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PlasmaFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PlasmaFarmer']);
        $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-EstimatedSmallholderFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-EstimatedSmallholderFarmer']);
        //rapikan variable post (end)

        $sql="UPDATE `ktv_mill` SET
                `MillName` = ?,
                `CompanyName` = ?,
                `MillGroupID` = ?,
                `Address` = ?,
                `VillageID` = ?,
                `Status` = ?,
                `Year` = ?,
                `Alias` = ?,
                `Phone` = ?,
                `PermanentEmployeeMale` = ?,
                `PermanentEmployeeFemale` = ?,
                `TemporaryEmployeeMale` = ?,
                `TemporaryEmployeeFemale` = ?,
                `Latitude` = ?,
                `Longitude` = ?,
                `Elevation` = ?,
                `Capacity` = ?,
                `PlasmaFarmer` = ?,
                `EstimatedSmallholderFarmer` = ?,
                `SocializationStatus` = ?,
                `NDASent` = ?,
                `NDAAgree` = ?,
                `NDASigned` = ?,
                `ParticipationStatus` = ?,
                `VisitDate` = ?,
                `RecruitDate` = ?,
                `TrainingDate` = ?,
                `SurveyDate` = ?,
                `HeadQuarterAddress` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?,
                `SocializationStatusDate` = ?,
                `NDASentDate` = ?,
                `NDAAgreeDate` = ?,
                `NDASignedDate` = ?,
                `ParticipationStatusDate` = ?
            WHERE
                `MillID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillName'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-CompanyName'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillGroup'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Address'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Village'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Status'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Year'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Alias'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Phone'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeMale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PermanentEmployeeFemale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeMale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TemporaryEmployeeFemale'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Latitude'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Longitude'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Elevation'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Capacity'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-PlasmaFarmer'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-EstimatedSmallholderFarmer'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SocializationStatus'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASent'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDAAgree'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASigned'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-ParticipationStatus'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-VisitDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-RecruitDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-TrainingDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SurveyDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-HeadQuarterAddress'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SocializationStatusDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASentDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDAAgreeDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-NDASignedDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-ParticipationStatusDate'],
            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID'],
        );
        $query = $this->db->query($sql,$p);

        if($query){
            if($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Latitude'] != "" && $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Longitude'] != "") {

                $LatitudeProses = (float) $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Latitude'];
                $LongitudeProses = (float) $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-Longitude'];
                
                //Check Latitude
                if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                    //Cek valid tidak koordinatnya
                    $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                    $DataCekKoordinat = $this->db->query($sql2)->row_array();
                    
                    if ($DataCekKoordinat['HasilCek'] == "1") {
                        $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                        $sql2 = "UPDATE ktv_mill a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`MillID` = ?
                                LIMIT 1";
                        $p = array(
                            $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID']
                        );
                        $query = $this->db->query($sql2,$p);
                    }

                }
            }
        }

        $checkExistingInternalExternalProgram = $this->db->where('MillID', (int) $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID'])
                                                         ->get('ktv_mill_business_unit')->result();

        if (!empty($checkExistingInternalExternalProgram)) {
            $this->db->delete('ktv_mill_business_unit', ['MillID' => (int) $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID']]);
        }

        $arrMillInternalProgram = array();
        $arrMillInternalProgram = $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-CmbInternalProgram'];
        if(!empty($arrMillInternalProgram)){
            foreach($arrMillInternalProgram as $k => $MillInternalProgramID){
                $sqlInternalProgram="INSERT INTO `ktv_mill_business_unit` SET
                    `MillID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pInternalProgram = array(
                    $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID'],
                    $MillInternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlInternalProgram,$pInternalProgram);
            }
           
        }
        //insert internal program ======================================================================== (end)

        $arrMillExternalProgram = array();
        $arrMillExternalProgram = $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-CmbExternalProgram'];
        if(!empty($arrMillExternalProgram)){
            foreach($arrMillExternalProgram as $k => $MillExternalProgramID){
                $sqlExternalProgram="INSERT INTO `ktv_mill_business_unit` SET
                    `MillID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pExternalProgram = array(
                    $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID'],
                    $MillExternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlExternalProgram,$pExternalProgram);
            }
           
        }

        //insert external program ======================================================================== (end)

        if($varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SupplychainID'] != ""){
            $sql="UPDATE `ktv_tc_supplychain_org` SET
            `ProductionCapacity` = ?,
            `WorkHour` = ?,
            `DateUpdated` = NOW(),
            `LastModifiedBy` = ?
            WHERE
                `SupplychainID` = ".$varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-SupplychainID']."
            LIMIT 1";
            
            $p = array(
                $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-ProductionCapacity'],
                $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-WorkHour'],
                $_SESSION['userid'],
            );
            
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MillID'] = $varPost['Koltiva_view_Mill_FormMainMill-FormBasicData-MillID'];
        }
        return $results;
    }

    

    public function updateMillProfile($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }
        
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeMale']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeFemale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeFemale']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TemporaryEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TemporaryEmployeeMale']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TemporaryEmployeeFemale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TemporaryEmployeeFemale']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Capacity'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Capacity']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PlasmaFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PlasmaFarmer']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer']);
        $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeMale'] = str_replace(",","",$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeMale']);

        //rapikan variable post (end)

        $sql="UPDATE `ktv_mill` SET
                `MillName` = ?,
                `CompanyName` = ?,
                `MillGroupID` = ?,
                `Address` = ?,
                `VillageID` = ?,
                `Status` = ?,
                `Year` = ?,
                `Alias` = ?,
                `Phone` = ?,
                `PermanentEmployeeMale` = ?,
                `PermanentEmployeeFemale` = ?,
                `TemporaryEmployeeMale` = ?,
                `TemporaryEmployeeFemale` = ?,
                `Latitude` = ?,
                `Longitude` = ?,
                `Elevation` = ?,
                `Capacity` = ?,
                `PlasmaFarmer` = ?,
                `EstimatedSmallholderFarmer` = ?,
                `HaveOer` = ?,
                `SocializationStatus` = ?,
                `NDASent` = ?,
                `NDAAgree` = ?,
                `NDASigned` = ?,
                `ParticipationStatus` = ?,
                `VisitDate` = ?,
                `RecruitDate` = ?,
                `TrainingDate` = ?,
                `SurveyDate` = ?,
                `HeadQuarterAddress` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?,
                `SocializationStatusDate` = ?,
                `NDASentDate` = ?,
                `NDAAgreeDate` = ?,
                `NDASignedDate` = ?,
                `ParticipationStatusDate` = ?
            WHERE
                `MillID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-MillName'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-CompanyName'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-MillGroup'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Address'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Village'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Status'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Year'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Alias'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Phone'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeMale'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PermanentEmployeeFemale'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TemporaryEmployeeMale'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TemporaryEmployeeFemale'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Latitude'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Longitude'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Elevation'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-Capacity'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-PlasmaFarmer'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-HaveOer'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-SocializationStatus'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-NDASent'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-NDAAgree'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-NDASigned'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-ParticipationStatus'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-VisitDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-RecruitDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-TrainingDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-SurveyDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-HeadQuarterAddress'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-SocializationStatusDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-NDASentDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-NDAAgreeDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-NDASignedDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-ParticipationStatusDate'],
            $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-MillID'],
        );
        $query = $this->db->query($sql,$p);

        if($varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-SupplychainID'] != ""){
            $sql="UPDATE `ktv_tc_supplychain_org` SET
            `ProductionCapacity` = ?,
            `WorkHour` = ?,
            `DateUpdated` = NOW(),
            `LastModifiedBy` = ?
            WHERE
                `SupplychainID` = ".$varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-SupplychainID']."
            LIMIT 1";
            
            $p = array(
                $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-ProductionCapacity'],
                $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-WorkHour'],
                $_SESSION['userid'],
            );
            
            $query = $this->db->query($sql,$p);
        }


        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MillID'] = $varPost['Koltiva_view_Mill_FormMainMillProfile-FormBasicData-MillID'];
        }
        return $results;
    }

    public function deleteMill($MillID){
        $sql="UPDATE `ktv_mill` SET
                StatusCode = 'nullified'
            WHERE
                `MillID` = ?
            LIMIT 1";
        $p = array(
            $MillID
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getGridMillStaff($MillID){
        $sql="SELECT
                a.`StaffID`
                , b.`PersonID`
                , b.`PersonNm` AS 'Name'
                , FLOOR(DATEDIFF(CURDATE(), b.`BirthDate`) / 365.25) AS Age
                , IFNULL(rpos.PositionName,'-') AS `Position`
                , b.UserID
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_staff_positions f ON a.`StaffID` = f.`StaffPosStaffID`
                                    AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                                    AND f.StatusCode = 'active'
                LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
            WHERE
                a.`ObjType` = 'mill'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql, array((int) $MillID));
        $data = $query->result_array();
        if($data[0]['StaffID'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getTrainingDataMill($MillID){
        $sql="SELECT
                COUNT(tpar.`ParticipantNewStaffID`) AS NrOfStaff
                , tt.`CpgTrainings` AS Topic
                , DATE(t.`TrainingStart`) AS `Start`
                , DATE(t.`TrainingEnd`) AS `End`
                , t.`TrainingDays` AS `Days`
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`

                LEFT JOIN ktv_master_trainings_participants tpar ON a.`StaffID` = tpar.`ParticipantNewStaffID`
                LEFT JOIN ktv_master_trainings t ON tpar.`MasterTrainingID` = t.`MasterTrainingID`
                LEFT JOIN ktv_cpg_trainings tt ON t.`CPGtrainingsID` = tt.`CpgTrainingsID`
            WHERE
                a.`ObjType` = 'mill'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
                AND tpar.`StatusCode` = 'active'
                AND t.`StatusCode` = 'active'
            GROUP BY t.`MasterTrainingID`
            ORDER BY t.`TrainingEnd` DESC
            LIMIT 10
            ";

        $query = $this->db->query($sql, array((int) $MillID));
        return $query->result_array();
    }
    
    public function getTraceabilityDataMill($MillID){
        $sql="SELECT
                    COUNT(DISTINCT st.SupplyTransID) batch_count,
                    COUNT(DISTINCT st.SupplyTransID) trans_count,
                    FORMAT((SUM(st.VolumeNetto)/1000),2) netto
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplychainID=vso.SupplychainID
                WHERE
                    vso.OrgType='mill' AND vso.OrgID=?";

        $query = $this->db->query($sql, array((int) $MillID))->result_array();
        
        $sql="SELECT
                    COUNT(DISTINCT IFNULL(sb2.SupplyOrgID, sb3.SupplyOrgID)) agent_count,
                    COUNT(DISTINCT IFNULL(st3.SupplyID, st2.SupplyID)) farmer_count
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplychainID=vso.SupplychainID
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID AND st2.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                WHERE
                    vso.OrgType='mill' AND vso.OrgID=?";
        $farmer = $this->db->query($sql, array((int) $MillID))->result_array();
        $return = array(
            'batch_count' => $query[0]['batch_count'],
            'trans_count' => $query[0]['trans_count'],
            'agent_count' => $farmer[0]['agent_count'],
            'farmer_count' => $farmer[0]['farmer_count'],
            'netto' => $query[0]['netto']
        );
        return $return;
    }

    function getFFBSales($MemberID){
        $sql = "SELECT
                /*Quarter 1 Start*/
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                        batchid_1,
                        NULL
                    )
                ) Q1_batch,
                IFNULL(SUM(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                        ROUND(IFNULL(netto_1,0)/1000,2),
                        NULL
                    )
                ),0) Q1_ton,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                        IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                        NULL
                    )
                ) Q1_agent,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                        MemberID,
                        NULL
                    )
                ) Q1_farmer,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                        transid_1,
                        NULL
                    )
                ) Q1_transaction,
                /*Quarter 1 End*/
                
                /*Quarter 2 Start*/
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                        batchid_1,
                        NULL
                    )
                ) Q2_batch,
                IFNULL(SUM(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                        ROUND(IFNULL(netto_1,0)/1000,2),
                        NULL
                    )
                ),0) Q2_ton,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                        IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                        NULL
                    )
                ) Q2_agent,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                        MemberID,
                        NULL
                    )
                ) Q2_farmer,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                        transid_1,
                        NULL
                    )
                ) Q2_transaction,
                /*Quarter 2 End*/
                
                /*Quarter 3 Start*/
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                        batchid_1,
                        NULL
                    )
                ) Q3_batch,
                IFNULL(SUM(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                        ROUND(IFNULL(netto_1,0)/1000,2),
                        NULL
                    )
                ),0) Q3_ton,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                        IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                        NULL
                    )
                ) Q3_agent,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                        MemberID,
                        NULL
                    )
                ) Q3_farmer,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                        transid_1,
                        NULL
                    )
                ) Q3_transaction,
                /*Quarter 3 End*/
                
                /*Quarter 4 Start*/
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                        batchid_1,
                        NULL
                    )
                ) Q4_batch,
                IFNULL(SUM(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                        ROUND(IFNULL(netto_1,0)/1000,2),
                        NULL
                    )
                ),0) Q4_ton,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                        IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                        NULL
                    )
                ) Q4_agent,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                        MemberID,
                        NULL
                    )
                ) Q4_farmer,
                COUNT(DISTINCT
                    IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                        transid_1,
                        NULL
                    )
                ) Q4_transaction
                /*Quarter 4 End*/
            FROM 
            (
                SELECT
                st.SupplyTransID transid_1,
                st.TransNumber transnumber_1,
                st.SupplyType transtype_1,
                st.DateTransaction date_1,
                st.SupplyType supplytype_1,
                st.SupplyBatchType supplybatchtype_1,
                st.SupplyID supplyid_1,
                st.PlantationNr plot_1,
                m.MemberID,
                st.VolumeBruto bruto_1,
                st.VolumeNetto netto_1,
                IFNULL(m.MemberName, IFNULL(vso_1.`Name`, '-')) supplier_1,
                vso.`Name` name_1,
                vso.SupplychainID supplychainid_1,
                vso.ObjType objtype_1,
                vso.ObjID objid_1,
                st.SupplyBatchID batchid_1,
                sb.DeliveryDate deliverydate_1,
                
                st2.SupplyTransID transid_2,
                st2.TransNumber transnumber_2,
                st2.DateTransaction date_2,
                st2.SupplyType supplytype_2,
                st2.SupplyID supplyid_2,
                vso2.`Name` name_2,
                vso2.SupplychainID supplychainid_2,
                vso2.ObjType objtype_2,
                vso2.ObjID objid_2,
                st2.SupplyBatchID batchid_2,
                sb2.DeliveryDate deliverydate_2,
                
                st3.SupplyTransID transid_3,
                st3.TransNumber transnumber_3,
                st3.DateTransaction date_3,
                st3.SupplyType supplytype_3,
                st3.SupplyID supplyid_3,
                vso3.`Name` name_3,
                vso3.SupplychainID supplychainid_3,
                vso3.ObjType objtype_3,
                vso3.ObjID objid_3
            FROM
                ktv_tc_supplychain_transaction st
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL(st.SupplychainID, sb.SupplyOrgID)
                LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                LEFT JOIN view_tc_supplychain_org vso_1 ON vso_1.SupplychainID = IF(st.DOID > 0 , st.DOID, IF(st.AgentID > 0, st.AgentID, IF(st.MillID > 0, st.MillID, NULL)))
                
                LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.StatusCode='active' AND st2.SupplyType='Batch' AND (st2.SupplyBatchType IS NULL OR st2.SupplyBatchType='Traceable') AND st2.SupplyID > 0 AND st2.SupplyID!=st2.SupplychainID
                LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = IFNULL(st2.SupplychainID, sb2.SupplyOrgID)
                
                LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.StatusCode='active' AND st3.SupplyType='Batch' AND (st3.SupplyBatchType IS NULL OR st3.SupplyBatchType='Traceable') AND st3.SupplyID > 0 AND st3.SupplyID!=st3.SupplychainID
                LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = IFNULL(st3.SupplychainID, sb3.SupplyOrgID)
                
                LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
                
            WHERE 1=1
                AND st.StatusCode='active'
                AND (st.SupplyType IN ('Farmer', 'Nonfarmer') OR (st.SupplyType='Batch' AND st.SupplyBatchType='Untraceable'))
                AND st.SupplyID > 0
            GROUP BY st.SupplyTransID
            ) dt
            WHERE 
                /*Untuk Mill*/
                ( (objtype_1='mill' AND objid_1='$MemberID') OR (objtype_2='mill' AND objid_2='$MemberID') OR (objtype_3='mill' AND objid_3='$MemberID') )
                /* 10016 = MillID  */"; 
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function getTraceabilityDetails($MemberID){
        $sql="SELECT
                    IFNULL(st.SupplyBatchID, st.SupplyTransID) BatchID,
                    SUBSTR(IFNULL(sb.SupplyBatchDate, st.DateTransaction),1,10) DateTransaction,
                    SUM(st.VolumeNetto) VolumeNetto,
                    IFNULL(SUM(IF(st.Bjr>0, ROUND(st.VolumeBruto1 / st.Bjr), IF(st.NumberPackage > 0, st.NumberPackage, NULL))), '-') FFB,
                    IFNULL(vso2.`Name`, '-') Delivered
                FROM
                    ktv_supplychain_transaction st
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchNumber=st.SupplyID
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyOrgID
                WHERE vso.OrgID = ? AND YEAR(st.DateTransaction)=YEAR(NOW())
                GROUP BY IFNULL(st.SupplyBatchID, st.SupplyTransID)";
        $query = $this->db->query($sql, array((int) $MemberID));
        //echo "<pre>".$this->db->last_query();die;
        return $query->result_array();
    }
    public function getMillGroups(){
        $sql="SELECT
            a.MillGroupID AS id,
            a.GroupName AS label 
        FROM
            ktv_mill_group AS a 
        WHERE
            a.StatusCode = 'active'
        ORDER BY a.GroupName";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getStaffFAAssignment($MillID) {
        $sql = "SELECT kmfa.StaffID value, kp.PersonNm text
                FROM ktv_mill_fa_assignment kmfa
                LEFT JOIN ktv_staffs ks ON ks.PersonID=kmfa.StaffID
                LEFT JOIN ktv_persons kp ON ks.PersonID=kp.PersonID
                WHERE kmfa.MillID = ?
                GROUP BY kmfa.StaffID";
        $query = $this->db->query($sql, array($MillID));
        $result = $query->result_array();
        if (count($result) < 1) {
            $result = array(
                'value' => '',
                'text' => ''
            );
        }
        return $result;
    }

    public function getListStaffFAAssignment($MillID) {
        $sql = "SELECT ks.StaffID value, kp.PersonNm text
                FROM ktv_staffs ks
                LEFT JOIN ktv_persons kp ON ks.PersonID=kp.PersonID
                WHERE ks.StatusCode = 'active'
                ORDER BY kp.PersonNm";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function setStaffFAAssignment($MillID, $item_staffs) {
        $this->db->trans_start();
        $staff = explode(',', $item_staffs);
        $sql = "SELECT StaffID FROM ktv_mill_fa_assignment kmfa WHERE MillID = ? GROUP BY StaffID";
        $query = $this->db->query($sql, array($MillID));
        $sel = $query->result();
        $OldStaff = array();
        foreach ($sel as $k => $v) {
            if (in_array($v->StaffID, $staff)) {
            } else {
                $sql_delete = "DELETE FROM ktv_mill_fa_assignment WHERE MillID = ? AND StaffID = ?";
                $query = $this->db->query($sql_delete, array($MillID, $v->StaffID));
            }
            $OldStaff[] = $v->StaffID;
        }
        for ($i = 0; $i < sizeof($staff); $i++) {
            if (!in_array($staff[$i], $OldStaff)) {
                $sql_insert = "INSERT INTO ktv_mill_fa_assignment(MillID, StaffID, UserID, DateCreated, CreatedBy)
                                SELECT {$MillID} MillID, {$staff[$i]} StaffID, u.UserId UserID, NOW() DateCreated, {$_SESSION['userid']} CreatedBy
                                FROM ktv_staffs ks
                                LEFT JOIN ktv_persons kp ON kp.PersonID=ks.StaffID
                                LEFT JOIN sys_user u ON u.UserId=kp.UserID
                                WHERE ks.StaffID = ?";
                $query = $this->db->query($sql_insert, array($staff[$i]));
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data Saved");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }
        return $results;
    }

    public function getGridMainMillSupplierExcel($MillID){
        $sql = "SELECT
            km.MillDisplayID
            , km.MillName
            , kmember.`MemberDisplayID` SupplierID
            , kmember.`MemberName` SupplierName
            , vil.Village
            , subd.SubDistrict
            , dis.District
            , prov.Province
        FROM
            ktv_mill km
        LEFT JOIN 
            ktv_tc_supplychain_org ktso2 ON ktso2.`ObjID` = km.`MillID` 
        AND ktso2.`ObjType` = 'mill'
        LEFT JOIN 
            ktv_tc_supplychain_org_rel ktsor ON ktsor.`ParentID` = ktso2.`SupplychainID`
        LEFT JOIN 
            ktv_tc_supplychain_org ktso ON ktso.`SupplychainID` = ktsor.`ChildID`
        INNER JOIN 
            ktv_members kmember ON kmember.`MemberID` = ktso.`ObjID` 
            AND ktso.`ObjType` = 'agent' 
            AND kmember.`StatusCode` = 'active'
        LEFT JOIN ktv_village vil ON kmember.VillageID = vil.VillageID
        LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
        LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
        LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
        LEFT JOIN 
            ktv_member_role kmr ON kmember.MemberID = kmr.MemberID
        LEFT JOIN 
            ktv_ref_member_role rm ON rm.`MRoleID` = kmr.`MRoleID` 
            AND rm.`MRoleType` = 'Agent' 
        WHERE
            km.`StatusCode` = 'active' 
        AND ktsor.`StatusCode` = 'active' 
        AND km.MillID = ?
        GROUP BY
            kmember.`MemberID`";

        $query = $this->db->query($sql,array($MillID));
        $result = $query->result_array();

        return $result;
    }

    public function getGridMainMillExcel($pSearch){
        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if($pSearch['prov'] != ""){
            $sqlFilter .= " AND e.ProvinceID = ".$pSearch['prov'];
        }

        if($pSearch['kab'] != ""){
            $sqlFilter .= " AND f.DistrictID = ".$pSearch['kab'];
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MillID` AS id
                , a.`MillDisplayID`
                , a.`MillName` AS `Name`
                , a.`Address`
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , IFNULL(a.DateUpdated,a.`DateCreated`) AS LastUpdated
                , e.Province
                , f.District
                , CASE
                    WHEN a.Status = '1' THEN '".lang('Sole Proprietorship')."'
                    WHEN a.Status = '2' THEN '".lang('Partnership')."'
                    WHEN a.Status = '3' THEN '".lang('Limited Partnership')."'
                    WHEN a.Status = '4' THEN '".lang('Limited Liability Company')."'
                    WHEN a.Status = '5' THEN '".lang('Corporation')."'
                    WHEN a.Status = '6' THEN '".lang('Cooperative')."'
                    WHEN a.Status = '7' THEN '".lang('Foundation')."'
                    WHEN a.Status = '8' THEN '".lang('Association')."'
                    WHEN a.Status = '9' THEN '".lang('State Owned')."'
                END AS StatusPerusahaan
                , a.`Year` AS TahunTerbentuk
                , a.`Alias`
                , a.`Phone`
                , (a.`PermanentEmployeeMale` + a.`PermanentEmployeeFemale`) AS TotalPermanentEmployee
                , g.GroupName
                , a.SetAsPartner
                , a.CompanyName
                , substring_index(GROUP_CONCAT(sub_sme.MemberName SEPARATOR ','), ',', 10) as SMEName
                , COUNT(DISTINCT kspsm.PlotNr) AS NrPlantation
                , sub_farmer.NrFarmer AS NrFarmer
                , substring_index(GROUP_CONCAT(DISTINCT a.Latitude, ',', a.Longitude), ',', 10) as GPS
            FROM
                ktv_mill a
                INNER JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                INNER JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                INNER JOIN ktv_district f ON d.DistrictID = f.DistrictID
                INNER JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
                LEFT JOIN ktv_mill_group g ON g.MillGroupID = a.MillGroupID
                LEFT JOIN ktv_survey_plot_status_mill kspsm ON kspsm.MillID = a.MillID

                LEFT JOIN(
                    SELECT
                        km.`MillID`
                        , COUNT(distinct kmember.`MemberID`) AS NrFarmer
                    FROM
                        ktv_mill km
                        INNER JOIN ktv_program_partner kpp on kpp.`PartnerID` = km.`PartnerID`
                        INNER JOIN ktv_access_partner_member kapm on kapm.`apmPartnerID` = kpp.`PartnerID`
                        INNER JOIN ktv_members kmember on kmember.`MemberID` = kapm.`apmMemberID`
                        INNER JOIN ktv_member_role kmr ON kmember.MemberID = kmr.MemberID AND kmr.`MRoleID` = 1
                    WHERE
                        1=1
                        AND km.`StatusCode` = 'active'
                        AND kmember.`StatusCode` = 'active'
                    GROUP BY
                        km.`MillID`
                ) sub_farmer ON sub_farmer.MillID = a.`MillID`

                LEFT JOIN(
                    SELECT
                        km.`MillID`
                        , substring_index(GROUP_CONCAT(kmember.`MemberName` SEPARATOR ','), ',', 5) as MemberName
                    FROM ktv_mill km
                    LEFT JOIN ktv_tc_supplychain_org ktso2 ON ktso2.`ObjID` =  km.`MillID` AND ktso2.`ObjType` = 'mill'
                    LEFT JOIN ktv_tc_supplychain_org_rel ktsor ON ktsor.`ParentID` = ktso2.`SupplychainID`
                    LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`SupplychainID` = ktsor.`ChildID`
                    LEFT JOIN ktv_members kmember ON kmember.`MemberID` = ktso.`ObjID` AND ktso.`ObjType` = 'agent' AND kmember.`StatusCode` = 'active'
                    WHERE
                        km.`StatusCode` = 'active'
                        AND ktsor.`StatusCode` = 'active'    
                    GROUP BY
                        km.`MillID`
                ) sub_sme ON sub_sme.MillID = a.`MillID`

                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MillID";

        $query = $this->db->query($sql);
        $result = $query->result_array();

        // echo "<pre>";print_r($this->db->last_query());die;

        return $result;
    }
}
?>