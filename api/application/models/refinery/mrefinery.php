<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mrefinery extends CI_Model {

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
                        LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.RefineryID = op.ObjID
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

    public function get_category_traceability($PID){    
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

        $jml_kebun_total = 0;
        $jml_supplier_total = 0;

        $result = array();
        if($query->num_rows()>0){
            $result = $query->result_array();
            foreach($result as $key => $row){
                $jml_supplier   = $this->getJmlSupplier($row['MRoleID'],$PID);
                $jml_kebun      = $this->getJmlKebun($row['MRoleID'],$PID);

                $ttp = 0;
                if($jml_kebun > 0 ){
                    $ttp = ($jml_kebun/$jml_supplier)*100;
                }

                $result[$key]['MRoleNames'] = lang($row["MRoleNames"]);
                $result[$key]['jml_supplier'] = $jml_supplier;
                $result[$key]['ttp'] = $ttp;

                $jml_kebun_total += $jml_kebun;
                $jml_supplier_total += $jml_supplier;
            }
        }
        
        $jml_supplier_direct    = $this->getJmlSupplierDirect($PID);
        $jml_kebun_direct       = $this->getJmlSupplierDirect($PID,"kebun");
        $ttp = 0;
        if($jml_kebun_direct>0){
            $ttp = ($jml_kebun_direct/$jml_supplier_direct)*100;
        }
        $direct = array(
            "MroleID" => "direct",
            "MRoleNames" => lang("Direct Smallholder"),
            "jml_supplier" => $jml_supplier_direct,
            "ttp" => $ttp
        );

        array_push($result,$direct);

        $kebun_total = $jml_kebun_total + $jml_kebun_direct;
        $supplier_total = $jml_supplier_total + $jml_supplier_direct;

        $ttp_total = ($kebun_total/$supplier_total)*100;
        
        return array($result,$ttp_total);
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
                AND vso.ObjType = 'refinery'
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
            AND vso.ObjType = 'refinery'
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
        //         AND vso2.ObjType = 'refinery'
        //         AND YEAR ( a.DateTransaction ) >= '$start' 
        //         AND YEAR ( a.DateTransaction ) <= '$end' 
        //         AND mr.MRoleID IN ($roleID)";
        $sql = "SELECT
        -- 	a.SupplybaseCategoryID,
        -- 	b.CategoryName,
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
            -- 	a.SupplybaseCategoryID,
            -- 	b.CategoryName,
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

    public function getRefineryBasicDataFormProfile($PID){
    
        $SID = $_SESSION['SupplychainID'];

        $sql="SELECT
                a.`RefineryID`,
                a.`RefineryID` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryID\",
                a.`RefineryDisplayID` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryDisplayID\",
                a.`RefineryName` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryName\",
                a.`CompanyName` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-CompanyName\",
                a.`Address` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Address\",
                SUBSTR(a.`VillageID`,1,2) AS \"Province\",
                SUBSTR(a.`VillageID`,1,4) AS \"District\",
                SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\",
                a.`VillageID` AS \"Village\",
                a.`Status` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Status\",
                a.`Year` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Year\",
                a.`Alias` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Alias\",
                a.`Phone` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Phone\",
                a.`Latitude` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Latitude\",
                a.`Longitude` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Longitude\",
                a.`Photo` AS PhotoSrc,
                a.LocationPhoto,
                a.PartnerID,
                a.`HeadQuarterAddress` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-HeadQuarterAddress\",
                b.`SupplychainID` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-SupplychainID\",
                b.`WorkHour` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-WorkHour\",
                b.`ProductionCapacity` AS \"Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-ProductionCapacity\"
            FROM
                `ktv_refinery` a
                LEFT JOIN
                `ktv_tc_supplychain_org` b ON b.ObjType='refinery' AND b.ObjID=a.RefineryID  AND b.StatusCode = 'active' AND b.ObjType = 'refinery'
            WHERE
               b.SupplychainID = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $SID));
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

    public function get_basic_data_refinery($PID){
        $sql = "SELECT
                m.RefineryDisplayID
                , m.RefineryName
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
                , m.Latitude
                , m.Longitude
                , CONCAT('api/images/mill/',d.ProvinceID,'/',m.Photo) Logo
            FROM
                view_tc_supplychain_org org
            LEFT JOIN
                ktv_refinery  m on m.RefineryID = org.ObjID
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
                m.RefineryID";
            
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
                INNER JOIN ktv_refinery  s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.RefineryID
                LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
            WHERE
            -- 	s_par.PartnerIndustry = 3 
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

    public function get_farmer_refinery($PID){
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
            INNER JOIN ktv_refinery  s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
            INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
            INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.RefineryID
            LEFT JOIN ktv_members m ON m.MemberID = s_ma.apmMemberID
            LEFT JOIN ktv_survey_plot sp on sp.MemberID = m.MemberID
            WHERE-- 	s_par.PartnerIndustry = 3
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
            INNER JOIN ktv_refinery  s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
            INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
            INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.RefineryID
            LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
        WHERE
        -- 	s_par.PartnerIndustry = 3 
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
            INNER JOIN ktv_refinery  s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
            INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
            INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.RefineryID
            LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
        WHERE
        -- 	s_par.PartnerIndustry = 3 
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
                INNER JOIN ktv_refinery  s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.RefineryID
                LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
            WHERE
            -- 	s_par.PartnerIndustry = 3 
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

    public function generateDeclaration($RefineryID){
        $sql = "
        SELECT
            tcorg2.ObjID RefineryID
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

        $query = $this->db->query($sql,array($RefineryID));

        if($query->num_rows()>0){
            $RefineryTCEventID = $this->getRefineryTCEventID($RefineryID);
            $transaction = array();
            foreach($query->result() as $row){
                list($SourceName,$TCPercentage) = $this->getSupplierData($row->SupplyID,$row->SupplybaseCategoryID);

                $transaction[] = array(
                    "RefineryTCEventID" => $RefineryTCEventID,
                    "RefineryID" => $RefineryID,
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

    function getRefineryTCEventID($RefineryID){
        $data = array(
            "RefineryID"    => $RefineryID,
            "LockDate"  => date("Y-m-d H:i:s"),
            "LockStatus" => 1,
            "LockComment" => "lock",
            "DateCreated"  => date("Y-m-d H:i:s"),
            "CreatedBy"  => $_SESSION["userid"],
        );

        $query = $this->db->insert("ktv_mill_tc_event",$data);

        return $this->db->insert_id();
    }

    public function getTracablePrint($PartnerID,$Year,$RefineryTCDID,$Period){
        $start  = $Year."-01-01 00:00:00";
        if($Period == "half"){
            $end = $Year."-06-30 23:59:59";
        }else if($Period == "full"){
            $end = $Year."-12-31 23:59:59";
        }else{
            $end = "";
        }

        if($RefineryTCDID == ""){
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
                    , b.TotalTrace/b.TotalRefinery TotalTrace
                    , IF(a.TCPercentage > 0,'Yes','No') Tracebility
                FROM ktv_mill_tc a
                LEFT JOIN (
                    SELECT
                        a.SourceType
                        , SUM(a.TCPercentage) as TotalTrace
                        , COUNT(a.RefineryID) TotalRefinery
                        , a.RefineryID
                    FROM ktv_mill_tc a
                    WHERE 1=1
                    AND a.DeliveryDate >= ?
                    AND a.DeliveryDate <= ?
                    GROUP BY a.SourceType,RefineryID
                ) b on b.RefineryID = a.RefineryID AND b.SourceType = a.SourceType
                LEFT JOIN
                    ktv_refinery  m on m.RefineryID = a.RefineryID
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
                    , b.TotalTrace/b.TotalRefinery TotalTrace
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
                        , COUNT(a.RefineryID) TotalRefinery
                        , a.RefineryTCDID
                    FROM ktv_mill_tc_declaration_detail a
                    WHERE 1=1
                    GROUP BY SourceType,RefineryID
                ) b on b.RefineryTCDID = a.RefineryTCDID AND b.SourceType = SourceType
                WHERE
                1=1
                $where
                AND a.RefineryTCDID = ?
            ";

            $query = $this->db->query($sql,array($RefineryTCDID));
        }

        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;

        return $query->result_array();
    }

    function UpdateImageRefinery($RefineryID,$path,$type){
        if($type == "Logo"){
            $sqlupdate = "Photo = '$path'";
        }
        if($type == "Location"){
            $sqlupdate = "LocationPhoto = '$path'";
        }
        $sql = "
            UPDATE
                ktv_refinery
            SET
                $sqlupdate
            WHERE RefineryID = '$RefineryID'
        ";

        $query = $this->db->query($sql);
    }

    public function getRefinerytracebilityDeclaration($PartnerID,$Year,$Period){
        $start  = $Year."-01-01 00:00:00";
        if($Period == "half"){
            $end = $Year."-06-30 23:59:59";
        }else if($Period == "full"){
            $end = $Year."-12-31 23:59:59";
        }else{
            $end = "";
        }

        $sql = "
        SELECT
            a.SourceType
            , CASE
                    WHEN a.SourceType = 1 THEN 'Plasma'
                    WHEN a.SourceType = 2 THEN 'OwnedEstate'
                    WHEN a.SourceType = 3 THEN 'ExternalEstate'
                    WHEN a.SourceType = 4 THEN 'OtherSupplier'
                END SourcTypeName
            , SUM(a.FFBSupply) TotalFFB
            , SUM(a.TCPercentage) TotalTrace
            , (SUM(a.FFBSupply)/b.TotalFFBAll) * 100 ProportionFFB
            , (SUM(IF(a.TCPercentage > 0,a.FFBSupply,0))/b.TotalFFBAll) * 100 TTPMILL
            , SUM(IF(a.TCPercentage > 0,a.FFBSupply,0)) TPP
            , b.TotalFFBAll
            , a.Approved
        FROM
            `ktv_mill_tc` a
        LEFT JOIN (
            SELECT 
                a.RefineryID
                , SUM(a.FFBSupply) AS TotalFFBAll
            FROM 
                ktv_mill_tc a
            WHERE 1=1
            AND a.DeliveryDate >= ?
            AND a.DeliveryDate <= ?
            GROUP BY a.RefineryID
        ) b on b.RefineryID = a.RefineryID
        LEFT JOIN
            ktv_refinery  m on m.RefineryID = a.RefineryID
        WHERE 1=1
            AND m.PartnerID = ?
            AND a.DeliveryDate >= ?
            AND a.DeliveryDate <= ?
        GROUP BY a.SourceType
        ORDER BY a.SourceType ASC
        ";
        $query = $this->db->query($sql,array($start,$end,$PartnerID,$start,$end));

        $dataArray      = array();
        $dataApproved   = array();
        
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredOwnedEstate"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceOwnedEstate"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOwnedEstate"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryOwnedEstate"] = 0;
        
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredPlasma"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTracePlasma"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionPlasma"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryPlasma"] = 0;
        
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredExternalEstate"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceExternalEstate"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionExternalEstate"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryExternalEstate"] = 0;

        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredOtherSupplier"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceOtherSupplier"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOtherSupplier"] = 0;
        $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryOtherSupplier"] = 0;
        if($query->num_rows()>0){
            $tmp = "";
            $TotalFFB = 0;
            $approved = 0;
            foreach($query->result() as $row){
                if($row->SourceType <> $tmp){
                    $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcured".$row->SourcTypeName]    = number_format($row->TotalFFB,2);
                    $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTrace".$row->SourcTypeName]     = number_format($row->TotalTrace,2);
                    $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportion".$row->SourcTypeName] = number_format($row->ProportionFFB,2);
                    $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefinery".$row->SourcTypeName] = number_format($row->TTPMILL,2);

                    $tmp = $row->SourceType;
                }
                $TotalFFB += $row->TotalFFB;

                if($row->Approved == 1){
                    $approved = 1;
                }
                $dataArray["Approved"] = $approved;
            }
            $dataArray["Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalFFB"] = number_format($TotalFFB,2);
        }
        
        $data["success"] = true;
        $data["data"]    = $dataArray;

        return $data;
    }

    public function getGridTracebilityDeclaration($PartnerID,$Year,$RefineryTCDID,$Period,$Category){
        $start  = $Year."-01-01 00:00:00";
        if($Period == "half"){
            $end = $Year."-06-30 23:59:59";
        }else if($Period == "full"){
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
        if($RefineryTCDID == ""){
            if($Category == 2){
                $sql = "SELECT
                    a.SourceName
                    , a.RefineryTCID
                    , a.RefineryID
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
                    , IF(a.TCPercentage > 0,'Yes','No') Tracebility
                    , a.Generated
                    , IFNULL(sps.AnnualProduction,0) AnnualProduction
                    , IFNULL(sps.GardenAreaHa,0) GardenAreaHa
                    , SourceID
                FROM ktv_mill_tc a
                LEFT JOIN
                    ktv_refinery  m on m.RefineryID = a.RefineryID
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = a.SourceID
                LEFT JOIN
                    ktv_survey_plot_sme sps on sps.MemberID = vso.ObjID
                WHERE 1=1
                    AND a.SourceType = ?
                    AND m.PartnerID = ?
                    AND a.DeliveryDate >= ?
                    AND a.DeliveryDate <= ?
                GROUP BY a.RefineryTCID
                ";
            }else{
                $sql = "SELECT
                    a.SourceName
                    , a.RefineryTCID
                    , a.RefineryID
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
                    , IFNULL(SUM(b.AnnualProduction),SUM(sp.AnnualProduction)) AnnualProduction
                    , IFNULL(SUM(b.GardenAreaHa),SUM(sp.GardenAreaHa)) GardenAreaHa
                    , SourceID
                FROM ktv_mill_tc a
                LEFT JOIN
                    ktv_refinery  m on m.RefineryID = a.RefineryID
                LEFT JOIN
                    ktv_tc_supplychain_farmer vso on vso.SupplychainID = a.SourceID
                LEFT JOIN
                    ktv_survey_plot b on b.MemberID = vso.FarmerID
                LEFT JOIN
                    ktv_survey_plot sp on sp.MemberID = a.SourceID
                WHERE 1=1
                    AND a.SourceType = ?
                    AND m.PartnerID = ?
                    AND a.DeliveryDate >= ?
                    AND a.DeliveryDate <= ?
                GROUP BY a.RefineryTCID
                ";
            }

            $query = $this->db->query($sql,array($Category,$PartnerID,$start,$end));

            if($query->num_rows()>0){
                // $i = 1;
                foreach($query->result() as $row){
                    if($Period == "half"){
                        $AnnualProduction = $row->AnnualProduction/2;
                        if($row->SourceCategoryID == 3){
                            $row->Tracebility = ($AnnualProduction/$row->FFBSupply)*100;
                        }
                    }else{
                        $AnnualProduction = $row->AnnualProduction;
                        if($row->SourceCategoryID == 3){
                            $row->Tracebility = ($AnnualProduction/$row->FFBSupply)*100;
                        }
                    }
                    if($row->Tracebility > 100){
                        $row->Tracebility = 100;
                    }
                    $dataArray[] = array(
                        "SupplierName" => $row->SourceName
                        ,"RefineryTCID" => $row->RefineryTCID
                        ,"RefineryID" => $row->RefineryID
                        ,"GardenType" => $row->SourceCategory
                        ,"FFBSupply" => $row->FFBSupply
                        ,"Tracebility" => ($row->Tracebility !=0 ? number_format($row->Tracebility,2) : 0 )." %"
                        ,"Generated"    => $row->Generated
                        ,"AnnualProduction" => $AnnualProduction
                        ,"GardenAreaHa" => $row->GardenAreaHa
                    );
                }
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
                    , a.RefineryTCID
                    , a.RefineryID
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
                AND a.RefineryTCDID = ?
            ";

            $query = $this->db->query($sql,array($RefineryTCDID));

            if($query->num_rows()>0){
                // $i = 1;
                foreach($query->result() as $row){
                    if($Category == 4 ){
                        $row->Tracebility = $row->TCPercentage." %";
                    }
                    $dataArray[] = array(
                        "SupplierName" => $row->SourceName
                        ,"RefineryTCID" => $row->RefineryTCID
                        ,"RefineryID" => $row->RefineryID
                        ,"GardenType" => $row->SourceCategory
                        ,"FFBSupply" => $row->FFBSupply
                        ,"Tracebility" => $row->Tracebility
                        ,"Generated"    => $row->Generated
                    );
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
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_mill acc_pmi ON acc_pmi.apmiPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
        }

        return $sqlHakAkses;
    }

    public function setPartnerRefinery($paramPost){
        $this->db->from('ktv_refinery');
        $this->db->where('PartnerID', $paramPost["PartnerID"]);
        $this->db->where('RefineryID !=', $paramPost["RefineryID"]);

        if ($this->db->get()->num_rows() > 0) {
            # code...
            $results['success'] = false;
            $results['message'] = "Partner ini sudah ada di refinery";
        } else {
            # code...
            $sql="UPDATE `ktv_refinery` SET
                    PartnerID = ?,
                    DateUpdated = NOW(),
                    LastModifiedBy = ?
                WHERE
                    `RefineryID` = ?";
            $p = array(
                $paramPost["PartnerID"],
                $_SESSION['userid'],
                $paramPost["RefineryID"],
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

    function refinery_as_partner($paramPost){
        $this->db->trans_begin();

        $sql="INSERT INTO `ktv_program_partner` SET
                `PartnerName` = ?,
                `PartnerFullName` = ?,
                `PartnerProgramName` = ?,
                `StatusCode` = 'active',
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            $paramPost["RefineryName"],
            $paramPost["RefineryName"],
            $paramPost["RefineryName"],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $partnerID = $this->db->insert_id();

        $sql="UPDATE `ktv_refinery` SET
                `SetAsPartner` = 'Yes',
                `PartnerID` = ?
              WHERE
              RefineryID = ?";
        $p = array(
            $partnerID,
            $paramPost["RefineryID"]
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

    public function getGridReportLocked($RefineryID,$Year,$start,$limit){
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

        $query = $this->db->query($sql,array($RefineryID,$Year,(int)$start,(int)$limit));
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

    public function getGridMainRefineryTCDeclarationManual($pSearch,$start,$limit,$sortingField,$sortingDir){
        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.RefineryTCDName like '%{$pSearch['textSearch']}%') ";
        }

        if($sortingField == "") $sortingField = 'RefineryTCDName';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "
            SELECT
                RefineryTCDID
                , RefineryTCDName 
                , DateCreated
                , RefineryID                 
            FROM
                `ktv_mill_tc_declaration` a
            WHERE 1=1
                AND a.RefineryID = '$pSearch[RefineryID]'
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

    function form_tc_declaration_new($RefineryTCDID){
        $sql = "SELECT
            tc.SourceCategory AS 'Koltiva.view.Refinery.FormAddData-SourceCategory'
            , tc.SourceID AS 'Koltiva.view.Refinery.FormAddData-SourceName'
            , tc.FFBSupply AS 'Koltiva.view.Refinery.FormAddData-FFBSupply'
            FROM
                `ktv_mill_tc` tc
            WHERE
            tc.RefineryTCID = ?";
        
        $query = $this->db->query($sql,array($RefineryTCDID));

        $result["success"]  = true;
        $result["data"]     = $query->row();

        return $result;
    }

    function form_tc_declaration($RefineryID,$RefineryTCDID){
        $sql = "
            SELECT
                RefineryTCDID
                , RefineryTCDName 
                , DateCreated
                , RefineryID                 
            FROM
                `ktv_mill_tc_declaration` a
            WHERE 
                RefineryID = ?
                AND RefineryTCDID = ?
        ";
        $query = $this->db->query($sql,array($RefineryID,$RefineryTCDID));

        $data = $query->row();

        $sql = "
            SELECT
                a.RefineryTCDID
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
                    a.RefineryTCDID
                    , SUM(a.FFBSupply) AS TotalFFBAll
                FROM 
                    ktv_mill_tc_declaration_detail a
                GROUP BY a.RefineryTCDID
            ) b on b.RefineryTCDID = a.RefineryTCDID
            WHERE a.RefineryTCDID = ?
            GROUP BY a.KategoriKebun, RefineryTCDID
        ";
        $query = $this->db->query($sql,array($RefineryTCDID));
        $dataGrid = (array)$data;
        $TotalFFB = 0;
        if($query->num_rows()>0){
            foreach($query->result() as $row){
                $kategori = $row->KategoriKebun;
                $dataGrid["FFBProcured".$kategori]  = $row->FFBSupply;
                $dataGrid["TotalTrace".$kategori]   = $row->TotalTrace;
                $dataGrid["FFBProcuredProportion".$kategori]   = number_format($row->ProportionFFB,2);
                $dataGrid["TtpRefinery".$kategori]      = number_format($row->TTPMILL,2);

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
        $RefineryID = $this->getRefineryID($post["PartnerID"]);


        if($post["Period"] == "full"){
            $month = "12";
            if($post["SourceCategory"] == 3){
                $TCPercentage = ($AnnualProduction/$post["FFBSupply"])*100;
            }
        }else{
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
        $post["RefineryID"]             = $RefineryID;
        $post["TCPercentage"]       = $TCPercentage;
        $post["LockStatus"]         = 1;
        $post["Generated"]          = 'No';

        unset($post["Year"]);
        unset($post["PartnerID"]);
        unset($post["Period"]);

        if($post["RefineryTCID"] == ""){
            $query = $this->db->insert("ktv_mill_tc",$post);
        }else{
            $this->db->where("RefineryTCID",$post["RefineryTCID"]);
            $query = $this->db->update("ktv_mill_tc",$post);
        }        

        return true;
    }

    function getRefineryID($PartnerID){
        $sql = "SELECT
            RefineryID
        FROM
            ktv_refinery  m
        WHERE
            m.PartnerID = ?
        ";

        $query = $this->db->query($sql,array($PartnerID));

        return $query->row()->RefineryID;
    }

    function getSupplierData($SID,$SourceCategory){
        if($SourceCategory == 2){
            $table = "ktv_survey_plot_sme";
            $sql = "SELECT
                m.MemberName MemberName
                , m.MemberID SupplychainID
                , a.total_kebun
                , a.AnnualProduction
                , b.AnnualProductionTrace
                , b.total_kebun_trace
                , (b.total_kebun_trace/a.total_kebun) * 100 TTPTrace
            FROM
                ktv_members m 
            LEFT JOIN
                (
                    SELECT
                    sp.MemberID
                    , count(sp.MemberID) total_kebun
                    , SUM(sp.AnnualProduction) AnnualProduction
                    FROM
                    $table sp
                    WHERE
                        sp.MemberID = $SID
                    GROUP BY sp.MemberID
                ) a on a.MemberID = m.MemberID
            LEFT JOIN
                (
                    SELECT
                    sp.MemberID
                    , count(sp.MemberID) total_kebun_trace
                    , SUM(sp.AnnualProduction) AnnualProductionTrace
                    FROM
                    $table sp
                    WHERE
                        sp.MemberID = $SID
                        AND sp.Latitude IS NOT NULL 
                    GROUP BY sp.MemberID
                ) b on b.MemberID = m.MemberID
            WHERE
                m.MemberID = ?";
        }else{
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
                        count( b.MemberID ) total_kebun,
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
                    count( b.MemberID ) total_kebun_trace,
                    SUM( b.AnnualProduction ) AnnualProductionTrace 
                FROM
                    view_tc_supplychain_org a
                    LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.SupplychainID = a.SupplychainID
                    LEFT JOIN ktv_survey_plot b ON b.MemberID = tsf.FarmerID 
                WHERE
                    a.Latitude IS NOT NULL  
                    AND a.SupplychainID = $SID
                    OR a.Longitude IS NOT NULL 
                    AND a.SupplychainID = $SID
                GROUP BY
                    b.MemberID 
                ) b ON b.SupplychainID = a.SupplychainID 
            WHERE
                c.SupplychainID = ?
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
                "RefineryTCDName"   => $post["RefineryTCDName"],
                "RefineryID"        => $post["RefineryID"],
                "CreatedBy"     => $_SESSION["userid"]
            );
            $this->db->insert("ktv_mill_tc_declaration",$post_header);
            $id = $this->db->insert_id();
            $detail = json_decode($post["ContactID"]);
            $dataDetail = array();
            if(count($detail)>0){
                foreach($detail as $row){
                    $value = array(
                        "RefineryTCDID" => $id,
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
        if($post["opsiDisplay"] == "update" AND $post["RefineryTCDID"] != ""){
            $post_header = array(
                "RefineryTCDName"   => $post["RefineryTCDName"],
                "RefineryID"        => $post["RefineryID"],
                "UpdatedBy"     => $_SESSION["userid"],
                "DateUpdated"   => date("Y-m-d H:i:s")
            );
            $this->db->where("RefineryTCDID",$post["RefineryTCDID"]);
            $this->db->update("ktv_mill_tc_declaration",$post_header);

            $this->db->where("RefineryTCDID",$post["RefineryTCDID"]);
            $this->db->delete("ktv_mill_tc_declaration_detail");
            $detail = json_decode($post["ContactID"]);
            $dataDetail = array();
            if(count($detail)>0){
                foreach($detail as $row){
                    $value = array(
                        "RefineryTCDID" => $post["RefineryTCDID"],
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

    function getSupplierList($RefineryTCDID){
        $sql = "
        SELECT
            id
            , RefineryTCDID
            , SupplierName
            , KategoriKebun
            , CategoryName KategoriKebunName
            , FFBSupply
            , Tracebility        
        FROM
            `ktv_mill_tc_declaration_detail` a
        LEFT JOIN
            ref_tc_supplybase_category b on b.SupplybaseCategoryID = a.KategoriKebun
        WHERE RefineryTCDID = ?
        ";
        $data = $this->db->query($sql,array($RefineryTCDID))->result_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $DataForm[$key] = $value;
        }        

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function getListAllRefinery(){
        $sql = "SELECT
                RefineryID id
                , RefineryName label
            FROM
                ktv_refinery
            WHERE StatusCode = 'active'
        ";

        $query = $this->db->query($sql);
        return array("data"=>$query->result_array());
    }

    public function getListAllSME($RefineryID = null){
        $sql = "SELECT
            a.MemberID id,
            mx.agCompanyName label
            FROM
                ktv_members a
                LEFT JOIN ktv_tc_supplychain_org o on a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel on orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op on orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_refinery  m on m.RefineryID = op.ObjID
                                LEFT JOIN ktv_members_extension mx on mx.MemberID = a.MemberID
            WHERE
            m.RefineryID = ?
            AND o. ObjType = 'agent'
            AND m.StatusCode = 'active'
        ";

        $query = $this->db->query($sql,array($RefineryID));
        return array("data"=>$query->result_array());
    }

    public function getGridMainRefinery($pSearch,$start,$limit,$sortingField,$sortingDir){
        $sqlFilter = "";

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
            $sqlFilter .= " AND (a.RefineryName like '%{$pSearch['textSearch']}%' OR a.RefineryDisplayID like '%{$pSearch['textSearch']}%' ) ";
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
            $sqlFilter .= " {$pSearch['cmbOpTotalPermanentEmployee']} '{$pSearch['textTotalPermanentEmployee']}' ";
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        if($sortingField == "") $sortingField = 'Name';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`RefineryID` AS id
                , a.`RefineryDisplayID`
                , a.`RefineryName` AS `Name`
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
                , a.CompanyName
                , GROUP_CONCAT(sub_sme.MemberName, ',') AS SMEName
                , sub_farmer.NrFarmer AS NrFarmer
                , GROUP_CONCAT(DISTINCT a.Latitude, ',', a.Longitude) AS GPS
            FROM
                ktv_refinery a
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID

                LEFT JOIN(
                    SELECT
                        km.`RefineryID`
                        , COUNT(distinct kmember.`MemberID`) AS NrFarmer
                    FROM
                        ktv_refinery  km
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
                        km.`RefineryID`
                ) sub_farmer ON sub_farmer.RefineryID = a.`RefineryID`

                LEFT JOIN(
                    SELECT
                        km.`RefineryID`
                        , GROUP_CONCAT(DISTINCT kmember.`MemberName` ) AS MemberName
                    FROM ktv_refinery  km
                    LEFT JOIN ktv_tc_supplychain_org ktso2 ON ktso2.`ObjID` =  km.`RefineryID` AND ktso2.`ObjType` = 'refinery'
                    LEFT JOIN ktv_tc_supplychain_org_rel ktsor ON ktsor.`ParentID` = ktso2.`SupplychainID`
                    LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`SupplychainID` = ktsor.`ChildID`
                    LEFT JOIN ktv_members kmember ON kmember.`MemberID` = ktso.`ObjID` AND ktso.`ObjType` = 'agent' AND kmember.`StatusCode` = 'active'
                    LEFT JOIN ktv_member_role kmr ON kmember.MemberID = kmr.MemberID 
                    LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID`= kmr.`MRoleID` AND rm.`MRoleType` = 'Agent'
                    WHERE
                        km.`StatusCode` = 'active'
                        AND ktsor.`StatusCode` = 'active'    
                    GROUP BY
                        km.`RefineryID`
                ) sub_sme ON sub_sme.RefineryID = a.`RefineryID`

                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.RefineryID
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

    public function getRefineryBasicDataForm($RefineryID){
        $sql="SELECT
                a.`RefineryID` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID\",
                a.`RefineryDisplayID` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryDisplayID\",
                a.`RefineryName` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryName\",
                a.`CompanyName` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-CompanyName\",
                a.`Address` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Address\",
                SUBSTR(a.`VillageID`,1,2) AS \"Province\",
                SUBSTR(a.`VillageID`,1,4) AS \"District\",
                SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\",
                a.`VillageID` AS \"Village\",
                a.`Status` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Status\",
                a.`Year` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Year\",
                a.`Alias` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Alias\",
                a.`Phone` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Phone\",
                a.`Latitude` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Latitude\",
                a.`Longitude` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Longitude\",
                a.`Photo` AS PhotoSrc,
                a.LocationPhoto,
                a.PartnerID,
                a.`HeadQuarterAddress` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-HeadQuarterAddress\",
                b.`SupplychainID` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-SupplychainID\",
                b.`WorkHour` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-WorkHour\",
                b.`ProductionCapacity` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-ProductionCapacity\"
            FROM
                `ktv_refinery` a
            LEFT JOIN
                `ktv_tc_supplychain_org` b ON b.ObjType='refinery' AND b.ObjID=a.RefineryID  AND b.StatusCode = 'active'  AND b.ObjType = 'refinery'
            WHERE
                a.`RefineryID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $RefineryID));

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

    public function getRefineryBasicDataFormNew($PartnerID){
        $sql="SELECT
                a.`RefineryID` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID\",
                a.`RefineryDisplayID` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryDisplayID\",
                a.`RefineryName` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryName\",
                a.`CompanyName` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-CompanyName\",
                a.`Address` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Address\",
                SUBSTR(a.`VillageID`,1,2) AS \"Province\",
                SUBSTR(a.`VillageID`,1,4) AS \"District\",
                SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\",
                a.`VillageID` AS \"Village\",
                a.`Status` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Status\",
                a.`Year` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Year\",
                a.`Alias` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Alias\",
                a.`Phone` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Phone\",
                a.`Latitude` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Latitude\",
                a.`Longitude` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Longitude\",
                a.`Photo` AS PhotoSrc,
                a.LocationPhoto,
                a.PartnerID,
                a.`HeadQuarterAddress` AS \"Koltiva.view.Refinery.FormMainRefinery-FormBasicData-HeadQuarterAddress\"
            FROM
                `ktv_refinery` a
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

    public function getRefineryDetailPrint($RefineryID){
        $sql="SELECT
                a.`RefineryID`,
                a.`RefineryDisplayID`,
                a.`RefineryName`,
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
                a.`Latitude`,
                a.`Longitude`,
                a.`Photo`,
                a.`PartnerID`,
                a.`LocationPhoto`
            FROM
                `ktv_refinery` a
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.`VillageID`,1,7) = d.`SubDistrictID`
                LEFT JOIN ktv_province e ON SUBSTR(a.`VillageID`,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.`VillageID`,1,4) = f.DistrictID
            WHERE
                a.`RefineryID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $RefineryID));
        return $query->row_array();
    }

    public function getRefineryNrOfStaff($RefineryID){
        $sql="SELECT
                COUNT(a.`StaffID`) AS BANYAK
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
            WHERE
                a.`ObjType` = 'refinery'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql, array((int) $RefineryID));
        $data = $query->row_array();
        return $data['BANYAK'];
    }

    public function genRefineryID($VillageID,$prefixId='MI'){
        //RefineryID
        $sql="SELECT
                a.`RefineryID`
            FROM
                ktv_refinery  a
            ORDER BY a.`RefineryID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if($data['RefineryID'] != ""){
            $return['RefineryID'] = $data['RefineryID'] + 1;
        }else{
            $return['RefineryID'] = 1;
        }

        //RefineryDisplayID
        $awalan = $prefixId.substr($VillageID,0,7);
        $sql="SELECT
                a.`RefineryDisplayID`
            FROM
                ktv_refinery  a
            WHERE
                a.`RefineryDisplayID` LIKE '$awalan%'
            ORDER BY a.`RefineryDisplayID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if($data['RefineryDisplayID'] != ""){
            $temp = (int) substr($data['RefineryDisplayID'],-4);
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
            $return['RefineryDisplayID'] = $temp;
        }else{
            $return['RefineryDisplayID'] = $awalan."0001";
        }

        return $return;
    }

    public function insertRefinery($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }

        //generate MemberID dan MemberDisplayID
        $id = $this->genRefineryID($varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Village'],'MI');

        $p = array(
            $id['RefineryID'],
            $id['RefineryDisplayID'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-RefineryName'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-CompanyName'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Address'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Village'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Status'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Year'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Alias'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Phone'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Latitude'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Longitude'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-HeadQuarterAddress'],
            $_SESSION['userid'],
        );
        $sql="INSERT INTO `ktv_refinery` SET
                `RefineryID` = ?,
                `RefineryDisplayID` = ?,
                `RefineryName` = ?,
                `CompanyName` = ?,
                `Address` = ?,
                `VillageID` = ?,
                `Status` = ?,
                `Year` = ?,
                `Alias` = ?,
                `Phone` = ?,
                `Latitude` = ?,
                `Longitude` = ?,
                `HeadQuarterAddress` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $query = $this->db->query($sql,$p);

        //insert hak akses data control (Begin)
        if($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sql="INSERT INTO `ktv_access_partner_mill` SET
                    `apmiPartnerID` = ?,
                    `apmiMillID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $_SESSION['PartnerID'],
                $id['RefineryID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);

            //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
            if($_SESSION['PartnerID'] != "1"){
               
                //insertkan ke Koltiva
                $sql="INSERT IGNORE INTO `ktv_access_partner_mill` SET
                        `apmiPartnerID` = ?,
                        `apmiMillID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $_SESSION['PartnerID'],
                    $id['RefineryID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }else{
            //insertkan ke Koltiva
            $sql="INSERT IGNORE INTO `ktv_access_partner_mill` SET
                    `apmiPartnerID` = ?,
                    `apmiMillID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $_SESSION['PartnerID'],
                $id['RefineryID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
        }
        // //insert hak akses data control (End)

        if($varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-SupplychainID'] != ""){
            $sql="UPDATE `ktv_tc_supplychain_org` SET
            `ProductionCapacity` = ?,
            `WorkHour` = ?,
            `DateUpdated` = NOW(),
            `LastModifiedBy` = ?
            WHERE
                `SupplychainID` = ".$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-SupplychainID']."
            LIMIT 1";
            
            $p = array(
                $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-ProductionCapacity'],
                $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-WorkHour'],
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
            $results['RefineryID'] = $id['RefineryID'];

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-PhotoOld'] != ""){
                $file = explode("images/mill/temp/",$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-PhotoOld']);
                // //Insert ada photonya pakai aws
                if(file_exists('images/mill/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/mill/temp/'.$file[1],$file[1],AWSS3_REFINERY_LOGO_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file("/".$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-PhotoOld']);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                }

                $sql="UPDATE ktv_refinery a SET
                        a.`Photo` = ?
                    WHERE
                        a.`RefineryID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['RefineryID']
                );
                $query = $this->db->query($sql,$p);
            }

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoOld'] != ""){
                $file = explode("images/mill_location/temp/",$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoOld']);
                // //Insert ada photonya pakai aws
                if(file_exists('images/mill_location/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/mill_location/temp/'.$file[1],$file[1],AWSS3_REFINERY_LOCATION_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file("/".$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoOld']);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                }

                $sql="UPDATE ktv_refinery  a SET
                        a.`LocationPhoto` = ?
                    WHERE
                        a.`RefineryID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['RefineryID']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        return $results;
    }

    public function updateRefinery($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }

        $sql="UPDATE `ktv_refinery` SET
                `RefineryName` = ?,
                `CompanyName` = ?,
                `Address` = ?,
                `VillageID` = ?,
                `Status` = ?,
                `Year` = ?,
                `Alias` = ?,
                `Phone` = ?,
                `Latitude` = ?,
                `Longitude` = ?,
                `HeadQuarterAddress` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `RefineryID` = ".$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-RefineryID']."
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-RefineryName'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-CompanyName'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Address'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Village'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Status'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Year'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Alias'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Phone'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Latitude'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-Longitude'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-HeadQuarterAddress'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-RefineryID'],
        );
        $query = $this->db->query($sql,$p);

        if($varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-SupplychainID'] != ""){
            $sql="UPDATE `ktv_tc_supplychain_org` SET
            `ProductionCapacity` = ?,
            `WorkHour` = ?,
            `DateUpdated` = NOW(),
            `LastModifiedBy` = ?
            WHERE
                `SupplychainID` = ".$varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-SupplychainID']."
            LIMIT 1";
            
            $p = array(
                $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-ProductionCapacity'],
                $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-WorkHour'],
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
            $results['RefineryID'] = $varPost['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-RefineryID'];

           
        }

        return $results;
    }

    public function updateRefineryProfile($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }
        
        $sql="UPDATE `ktv_refinery` SET
                `RefineryName` = ?,
                `CompanyName` = ?,
                `Address` = ?,
                `VillageID` = ?,
                `Status` = ?,
                `Year` = ?,
                `Alias` = ?,
                `Phone` = ?,
                `Latitude` = ?,
                `Longitude` = ?,
                `HeadQuarterAddress` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `RefineryID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-RefineryName'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-CompanyName'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Address'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Village'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Status'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Year'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Alias'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Phone'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Latitude'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-Longitude'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-HeadQuarterAddress'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-RefineryID'],
        );
        $query = $this->db->query($sql,$p);

        if($varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-SupplychainID'] != ""){
            $sql="UPDATE `ktv_tc_supplychain_org` SET
            `ProductionCapacity` = ?,
            `WorkHour` = ?,
            `DateUpdated` = NOW(),
            `LastModifiedBy` = ?
            WHERE
                `SupplychainID` = ".$varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-SupplychainID']."
            LIMIT 1";
            
            $p = array(
                $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-ProductionCapacity'],
                $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-WorkHour'],
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
            $results['RefineryID'] = $varPost['Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-RefineryID'];
        }
        return $results;
    }

    public function deleteRefinery($RefineryID){
        $sql="UPDATE `ktv_refinery` SET
                StatusCode = 'nullified'
            WHERE
                `RefineryID` = ?
            LIMIT 1";
        $p = array(
            $RefineryID
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

    public function getGridRefineryStaff($RefineryID){
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
                a.`ObjType` = 'refinery'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql, array((int) $RefineryID));
        $data = $query->result_array();
        if($data[0]['StaffID'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getTrainingDataRefinery($RefineryID){
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
                a.`ObjType` = 'refinery'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
                AND tpar.`StatusCode` = 'active'
                AND t.`StatusCode` = 'active'
            GROUP BY t.`MasterTrainingID`
            ORDER BY t.`TrainingEnd` DESC
            LIMIT 10
            ";

        $query = $this->db->query($sql, array((int) $RefineryID));
        return $query->result_array();
    }
    
    public function getTraceabilityDataRefinery($RefineryID){
        $sql="SELECT
                    COUNT(DISTINCT st.SupplyTransID) batch_count,
                    COUNT(DISTINCT st.SupplyTransID) trans_count,
                    FORMAT((SUM(st.VolumeNetto)/1000),2) netto
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplychainID=vso.SupplychainID
                WHERE
                    vso.OrgType='refinery' AND vso.OrgID=?";

        $query = $this->db->query($sql, array((int) $RefineryID))->result_array();
        
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
                    vso.OrgType='refinery' AND vso.OrgID=?";
        $farmer = $this->db->query($sql, array((int) $RefineryID))->result_array();
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
                LEFT JOIN view_tc_supplychain_org vso_1 ON vso_1.SupplychainID = IF(st.DOID > 0 , st.DOID, IF(st.AgentID > 0, st.AgentID, IF(st.RefineryID > 0, st.RefineryID, NULL)))
                
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
                /*Untuk Refinery*/
                ( (objtype_1='refinery' AND objid_1='$MemberID') OR (objtype_2='refinery' AND objid_2='$MemberID') OR (objtype_3='refinery' AND objid_3='$MemberID') )
                /* 10016 = RefineryID  */"; 
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
    public function getRefineryGroups(){
        $sql="SELECT
            a.RefineryGroupID AS id,
            a.GroupName AS label 
        FROM
            ktv_refinery_group AS a 
        WHERE
            a.StatusCode = 'active'
        ORDER BY a.GroupName";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getStaffFAAssignment($RefineryID) {
        $sql = "SELECT kmfa.StaffID value, kp.PersonNm text
                FROM ktv_mill_fa_assignment kmfa
                LEFT JOIN ktv_staffs ks ON ks.PersonID=kmfa.StaffID
                LEFT JOIN ktv_persons kp ON ks.PersonID=kp.PersonID
                WHERE kmfa.RefineryID = ?
                GROUP BY kmfa.StaffID";
        $query = $this->db->query($sql, array($RefineryID));
        $result = $query->result_array();
        if (count($result) < 1) {
            $result = array(
                'value' => '',
                'text' => ''
            );
        }
        return $result;
    }

    public function getListStaffFAAssignment($RefineryID) {
        $sql = "SELECT ks.StaffID value, kp.PersonNm text
                FROM ktv_staffs ks
                LEFT JOIN ktv_persons kp ON ks.PersonID=kp.PersonID
                WHERE ks.StatusCode = 'active'
                ORDER BY kp.PersonNm";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    public function setStaffFAAssignment($RefineryID, $item_staffs) {
        $this->db->trans_start();
        $staff = explode(',', $item_staffs);
        $sql = "SELECT StaffID FROM ktv_mill_fa_assignment kmfa 
        WHERE 
        RefineryID = ? 
        GROUP BY StaffID";
        $query = $this->db->query($sql, array($RefineryID));
        $sel = $query->result();
        $OldStaff = array();
        foreach ($sel as $k => $v) {
            if (in_array($v->StaffID, $staff)) {
            } else {
                $sql_delete = "DELETE FROM ktv_mill_fa_assignment 
                WHERE RefineryID = ? 
                AND StaffID = ?";
                $query = $this->db->query($sql_delete, array($RefineryID, $v->StaffID));
            }
            $OldStaff[] = $v->StaffID;
        }
        for ($i = 0; $i < sizeof($staff); $i++) {
            if (!in_array($staff[$i], $OldStaff)) {
                $sql_insert = "INSERT INTO ktv_mill_fa_assignment(RefineryID, StaffID, UserID, DateCreated, CreatedBy)
                                SELECT {$RefineryID} RefineryID, {$staff[$i]} StaffID, u.UserId UserID, NOW() DateCreated, {$_SESSION['userid']} CreatedBy
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

    public function getSPCode($RefineryID){
        $sql = "
        SELECT
            a.SPCodeID
            , a.RefineryID
            , a.Note
            , a.SuratNr
        FROM
            `ktv_refinery_sp_code` a
        WHERE
            RefineryID = ?
        ";
        
        $query = $this->db->query($sql,array($RefineryID));

        return $query->result_array();
    }
}
?>