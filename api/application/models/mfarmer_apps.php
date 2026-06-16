<?php
require 'vendor/autoload.php';
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;
/**
 * Authentication Model for Mobile
 *
 * @author Ardi <ardiantoro@koltiva.com>
 */
class Mfarmer_apps extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function angka($a) {
        $b = number_format($a, 2, '.', ',');
        $b = str_replace(".00", "", $b);
        return $b;
    }

    function getFarmerID($farmerid, $partnerid) {
        $sql = "SELECT f.MemberID id
                FROM ktv_members f 
                WHERE f.MemberDisplayID=? AND f.PartnerID = ?";
        $query = $this->db->query($sql, array($farmerid, $partnerid));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['id'];
        } else {
            return false;
        }
    }

    function getNewFarmerID($farmerid, $phone) {
        $sql = "SELECT f.FarmerID id
                FROM ktv_farmer f 
                    LEFT JOIN ktv_cpg c ON c.CpgID=f.CpgID
                    LEFT JOIN ktv_cpg_partner cp ON cp.CpgID=c.CpgID AND cp.PartnerID IN (8,9)
                WHERE f.FarmerID=? AND f.HandPhone=?";
        $query = $this->db->query($sql, array($farmerid, $phone));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['id'];
        } else {
            return false;
        }
    }

    function FarmerUserValidation($id) {
        $sql = "SELECT f.UserName id 
                FROM sys_user f 
                WHERE f.UserName=?";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['id'];
        } else {
            return false;
        }
    }

    function doLogin($data) {
        $username = @$data['attributes']['username'];
        $kunci = @$data['attributes']['password'];
        $type = @$data['type'];
        if ($type == 'farmer') {
            $sql = "SELECT
                        f.FarmerID, f.FarmerName, f.HandPhone, cp.PartnerID, IF(
                         f.Photo != ''
                         , CONCAT(
                              'https://app.palmoiltrace.com/api/images/Photo/'
                              , f.Photo
                         )
                         , ''
                    ) picture 
                    FROM ktv_farmer f 
                        LEFT JOIN ktv_cpg c ON c.CpgID=f.CpgID
                        LEFT JOIN ktv_cpg_partner cp ON cp.CpgID=c.CpgID AND cp.PartnerID IN (8,9)
                    WHERE f.FarmerID=?";
            $user = $this->db->query($sql, array($username))->row();
            if (@$user->FarmerID != '') {
                $return['data'] = array(
                    'type' => "user",
                    'id' => $user->FarmerID,
                    'attributes' => array(
                        'farmerid' => $user->FarmerID,
                        'fullname' => $user->FarmerName,
                        'email' => '',
                        'handphone' => $user->HandPhone,
                        'partnerid' => $user->PartnerID,
                        'picture' => $user->picture
                    )
                );
            } else {
                ////Sementara////
                $sql = "SELECT
                            u.UserId id, u.UserName username, p.PersonNm name, s.OfficialEmail email, s.OfficialPhone phone, s.ObjID partnerid
                        FROM
                            sys_user u 
                            LEFT JOIN ktv_persons p ON p.UserID=u.UserId
                            LEFT JOIN ktv_staffs s ON s.PersonID=p.PersonID
                        WHERE u.Username=? AND u.UserPassword=md5(?)";
                $user = $this->db->query($sql, array($username, $kunci))->row();
                if (@$user->id != '') {
                    $return['data'] = array(
                        'type' => "user",
                        'id' => $user->id,
                        'attributes' => array(
                            'farmerid' => "",
                            'fullname' => $user->name,
                            'email' => $user->email,
                            'handphone' => $user->phone,
                            'partnerid' => $user->partnerid,
                            'picture' => ''
                        )
                    );
                } else {
                    $return['errors'] = array(
                        'status' => "003",
                        'title' => "Invalid Username / Password",
                        'detail' => "User not exists."
                    );
                }
                ////Sementara////
                /* $return['errors'] = array(
                  'status' => "003",
                  'title'  => "Invalid Username",
                  'detail' => "User not exists."
                  ); */
            }
        } else {
            $sql = "SELECT
                        u.UserId id, u.UserName username, p.PersonNm name, s.OfficialEmail email, s.OfficialPhone phone, s.ObjID partnerid
                    FROM
                        sys_user u 
                        LEFT JOIN ktv_persons p ON p.UserID=u.UserId
                        LEFT JOIN ktv_staffs s ON s.PersonID=p.PersonID
                    WHERE u.Username=? AND u.UserPassword=md5(?)";
            $user = $this->db->query($sql, array($username, $kunci))->row();
            if (@$user->id != '') {
                $return['data'] = array(
                    'type' => "user",
                    'id' => $user->id,
                    'attributes' => array(
                        'farmerid' => "",
                        'fullname' => $user->name,
                        'email' => $user->email,
                        'handphone' => $user->phone,
                        'partnerid' => $user->partnerid,
                        'picture' => ''
                    )
                );
            } else {
                $return['errors'] = array(
                    'status' => "003",
                    'title' => "Invalid Username / Password",
                    'detail' => "User not exists."
                );
            }
        }

        return $return;
    }

    function getFarmerProfile($data) {

        $handphone = $data['attributes']['handphone'] ?? ''; //handphone indicator
        $memberid = $data['attributes']['farmerid'] ?? '';
        $partnerid =  $data['attributes']['partnerid'] ?? '';
        $queryParams = [];

        $return = [];
        $urlAWS = $this->config->item('CTCDN')."/";
        if (strlen($handphone) > 0 || strlen($memberid) > 0) { //handphone exists

            //getter farmer profile
            $sql = "SELECT
                f.MemberDisplayID id,
                f.MemberID AS `farmerid`,
                f.MemberName nama,
                TIMESTAMPDIFF ( YEAR,
                f.DateOfBirth,
                CURDATE()) AS Age,
                DATE_FORMAT(f.DateOfBirth, '%d %M %Y') as Birthdate,
                f.HandPhone handphone,
                CASE f.Gender WHEN 'm' THEN 'Male'
                WHEN 'f' THEN 'Female'
                ELSE '' END gender,
                c.GroupName farmerGroup,
                'None' farmerCooperative,
                p.Province province,
                d.District district,
                sd.SubDistrict subDistrict,
                v.Village village,
                f.Address address,
                IF ( f.MaritalStatus = 1,
                'Married',
                IF ( f.MaritalStatus = 2,
                'Single',
                IF ( f.MaritalStatus = 3,
                'Widower',
                NULL ) ) ) familyStatus,
                NULL zipCode,
                f.Nin NIK,
                f.BankHolderName bankAccountHolder,
                b.BankName bankName,
                f.BankBranchName bankBranch,
                f.BankAccNumber bankAccountNumber,
                f.AccountHolderRelation bankAccountHolderRelation,
                f.Email as email,
                IF ( f.AccountHolderRelation = 1,
                'Registered Farmer',
                IF ( f.AccountHolderRelation = 2,
                'Spouse',
                IF ( f.AccountHolderRelation = 3,
                'Child',
                IF ( f.AccountHolderRelation = 4,
                'Other Household',
                NULL ) ) ) ) bankAcountHolderRelation,
                (
                    SELECT 
                        GROUP_CONCAT(DISTINCT vso.Name SEPARATOR '\n') Collector
                    FROM
                        ktv_tc_supplychain_transaction st
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( sb.SupplyOrgID, st.SupplychainID )
                    WHERE
                        SupplyType != 'Batch' 
                    AND vso.Name IS NOT NULL 
                    AND vso.ObjType = 'agent' 
                    AND st.SupplyID = f.MemberID
                ) collector,
                (
                    SELECT 
                        GROUP_CONCAT(DISTINCT vso.Name SEPARATOR '\n') Collector
                    FROM
                        ktv_tc_supplychain_transaction st
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( sb.SupplyOrgID, st.SupplychainID )
                    WHERE
                        SupplyType != 'Batch' 
                    AND vso.Name IS NOT NULL 
                    AND vso.ObjType = 'mill' 
                    AND st.SupplyID = f.MemberID
                ) mill,
                IF ( f.Photo != '',
                CONCAT ( '".$urlAWS."',
                f.Photo ),
                '' ) picture,
                f.PartnerID,
                IF(f.SupplybaseType = 'farmer','SmallHolder',IF(f.SupplybaseType = 'direct','DirectSmallHolder','-')) farmerType
            FROM
                ktv_members f
            LEFT JOIN ktv_farmer_group c ON
                c.FarmerGroupID = f.FarmerGroupID
            LEFT JOIN ktv_village v ON
                v.VillageID = f.VillageID
            LEFT JOIN ktv_subdistrict sd ON
                sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d ON
                d.DistrictID = sd.DistrictID
            LEFT JOIN ktv_province p ON
                p.ProvinceID = d.ProvinceID
            LEFT JOIN ktv_bank b ON
                b.BankID = f.BankID
            WHERE
                f.MemberDisplayID IS NOT NULL";

            if(strlen($handphone) > 0) {
                $sql .= ' AND f.HandPhone = ?';
                array_push($queryParams,$handphone);
            }

            if(strlen($memberid) > 0) {
                $sql .= ' AND f.MemberDisplayID = ?';
                array_push($queryParams,$memberid);
            }

            $sql .= ' LIMIT 1';

            $user = $this->db->query($sql, $queryParams);
            if($user->num_rows() > 0) { //farmer found
                $user = $user->result();
                $id = $user[0]->id;

                $ret[0] = array(
                    'type' => "user",
                    'id' => $id,
                    'attributes' => convert_language($user[0], 'in') // $user[0]
                );

                //getter surveys
                $sql = "SELECT 
                    IF (
                            b.SurveyNr = a.SurveyNr
                            , 'baseline'
                            , 'postline'
                    ) AS `name`
                    , SUM(a.GardenAreaHa) AS `farmSize`
                    , (SUM(a.AverageProdLowSeason)) AS `averageYield`
                    , (SUM(a.AnnualProduction)) AS `production`
                    , COUNT(a.PlotNr) AS `noOfGarden`
                FROM
                    ktv_survey_plot a 
                    JOIN 
                            (SELECT 
                                a.MemberID
                                , a.PlotNr
                                , a.SurveyNr 
                            FROM
                                ktv_survey_plot a 
                                JOIN 
                                    (SELECT 
                                        a.MemberID
                                        , a.PlotNr
                                        , MAX(a.SurveyNr) AS SurveyNr 
                                    FROM
                                        ktv_survey_plot a 
                                    GROUP BY a.MemberID
                                        , a.PlotNr) b 
                                    ON a.MemberID = b.MemberID 
                                    AND a.SurveyNr = b.SurveyNr 
                                    AND a.PlotNr = b.PlotNr) b 
                            ON a.MemberID = b.MemberID 
                            AND a.PlotNr = b.PlotNr 
                            AND a.SurveyNr = b.SurveyNr 
                    JOIN ktv_members km ON a.MemberID = km.MemberID
                WHERE km.MemberID = ?
                GROUP BY a.MemberID";

                $survey = $this->db->query($sql, array($id));
                $i = 1;

                if ($survey->num_rows() > 0) {

                    $data_convert = convert_language($survey->result(), 'in');

                    foreach ($data_convert as $row) {
                        $row->farmSize['value'] = $row->farmSize['value'] . ' Ha';
                        $row->averageYield['value'] = $row->averageYield['value']. ' Ton/Ha';
                        $row->production['value'] = $row->production['value'] . ' Ton';
                        $ret[$i] = array(
                            'type' => "survey",
                            'id' => $i + 1,
                            'attributes' => array(
                                'name' => $row->name,
                                'farmSize' => $row->farmSize, // . ' Ha',
                                'averageYield' => $row->averageYield, // . ' Kg/Ha',
                                'production' => $row->production, // . ' Kg',
                                'noOfGarden' => $row->noOfGarden
                            )
                        );
                        $i++;
                    }
                }

                $j = $i+1;
                $ret[$i] = array(
                    'type' => "notification",
                    'id' => $j,
                    'attributes' => array(
                        "TotalNotif" => array(
                            "label" => "TotalNotif",
                            "value" => $this->_getUnreadNotif($id, $partnerid)
                        )
                    )
                );

                $return['data'] = $ret;
            }
        }

        return $return;
    }

    function _getUnreadNotif($farmerID, $partnerid){
        //Check Unread Notif Transaction
        $unreadNotif = 0;
        $sql = "
            SELECT
                count(*) AS TotalNotif
            FROM 
                ktv_mobile_notification
            WHERE
                FarmerID = ?
            AND
                StatusRead = 0
        ";
        $query = $this->db->query($sql, array($farmerID));
        if($query->row()->TotalNotif > 0){
            $unreadNotif += $query->row()->TotalNotif;
        }

        //Check all notif by partner id -- type broadcasr
        $sql = "
            SELECT
                NotifID
            FROM 
                ktv_mobile_notification
            WHERE
                PartnerID = ?
            AND
                OrgType = 'broadcast'
        ";
        $query = $this->db->query($sql, array($partnerid));
        //If notif is exist
        if($query->num_rows() > 0){
            foreach($query->result() as $key => $value){
                //Check if not read on notification status table
                if(!$this->_checkNotificatonStatusByFarmerID($value->NotifID,$farmerID)){
                    $unreadNotif += 1;
                }
            }
        }

        return $unreadNotif;
    }

    function confirmTransaction($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $transid = @$data['attributes']['transid'];
        $status = @$data['attributes']['status'] == '1' ? 'confirmed' : 'rejected';
        $description = @$data['attributes']['description'];
        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "INSERT INTO ktv_supplychain_transaction_confirmation (
                        SupplyTransID,
                        FarmerID,
                        STATUS,
                        description_confirmation,
                        DateCreated
                   ) 
                   VALUES
                        (?, ?, ?, ?, NOW())";
            $query = $this->db->query($sql, array($transid, $farmerid, $status, $description));

            if ($this->db->affected_rows() > 0) {
                $return['data'] = array(
                    'type' => "confirmation",
                    'id' => $transid,
                    'attributes' => array(
                        'farmerid' => $farmerid,
                        'transid' => $transid,
                        'message' => 'confirmation successful'
                    )
                );
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "confirmation failed"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    function getFarmerGarden($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);

        if ($id != '') {
            $sql = "SELECT
                        g.MemberID,
                        g.PlotNr GardenNr,
                        CONCAT(s.SurveyNr,'-',s.SurveyTxt) as SurveyNr,
                        g.DateUpdated,
                        (SELECT su.UserRealName FROM sys_user su WHERE UserId = g.LastModifiedBy) LastModifiedBy,
                        g.PlantingType,
                    CASE g.SoilType 
                        WHEN '1' THEN 'Mineral' WHEN '2' THEN 'Peat' WHEN '3' THEN 'Sandy' ELSE '-' END SoilType,
                        CONCAT(IF(WagsCertStandardRSPO = 1,'RSPO',''),'|',IF(WagsCertStandardMSPO = 1,'MSPO','')) as certification,
                    CASE
                        g.LandOwnershipType 
                        WHEN '1' THEN
                        'Owned' 
                        WHEN '2' THEN
                        'Profil sharing' 
                        WHEN '3' THEN
                        'Rented' 
                        WHEN '4' THEN
                        'Other' ELSE '-' 
                        END landOwnerShip,
                        
                        g.GardenAreaHa size,
                    CASE
                        g.OwnershipDoc 
                        WHEN '1' THEN
                        'No Document' 
                        WHEN '2' THEN
                        'SKT' 
                        WHEN '3' THEN
                        'SHM/Sertifikat' 
                        WHEN '4' THEN
                        'HGU' 
                        WHEN '5' THEN
                        'SKGR' 
                        WHEN '6' THEN 
                        'Other' ELSE '-' 
                        END landCerticate,
                        (g.AnnualProduction) as production,
                        CONCAT(CAST(((g.AnnualProduction)/(IFNULL(g.GardenAreaHa,0))) AS DECIMAL(10,2)),'') AS productivity,
                        (g.AverageProdLowSeason) AS averageYield,
                        IFNULL(g.TreeTBM,0) AS productiveTree,
                        '0' AS totalOtherTree,
                        (IFNULL(g.TreeTBM,0) + IFNULL(g.TreeTM,0) + IFNULL(g.TreeTR,0)) AS totalTree,
                        CONCAT(
                            IF(g.TypePlantMateMarihat = 1, 'Marihat', ''),
                            IF(g.TypePlantMateDanimas = 1, ',Danimas', ''),
                            IF(g.TypePlantMateDumpy = 1, ',Dumpy', ''),
                            IF(g.TypePlantMateLonsum = 1, ',Lonsum', ''),
                            IF(g.TypePlantMateSimalungun = 1, ',Simalungun', ''),
                            IF(g.TypePlantMateSocfin = 1, ',Socfin', ''),
                            IF(g.TypePlantMateSriwijaya = 1, ',Sriwijaya', '')
                        ) treeSeed,
                        '' AS BatasUtara,-- d.BatasUtara,
                        '' AS BatasSelatan,-- d.BatasSelatan,
                        '' AS BatasTimur,-- d.BatasTimur,
                        '' AS BatasBarat,-- d.BatasBarat,
                        ( CASE WHEN g.Latitude IS NULL OR g.Latitude = 0.000000 THEN dt.Latitude ELSE g.Latitude END ) latitude,
                        ( CASE WHEN g.Longitude IS NULL OR g.Longitude = 0.000000 THEN dt.Longitude ELSE g.Longitude END ) longitude 
                        
                    FROM
                        (
                            SELECT
                                ksp.MemberID,
                                ksp.PlotNr,
                                MAX( ksp.Latitude ) Latitude,
                                MAX( ksp.Longitude ) Longitude,
                                MAX( ksp.SurveyNr ) SurveyNr 
                            FROM
                                ktv_survey_plot ksp
                                left join ktv_members km ON ksp.MemberID = km.MemberID
                            WHERE
                                km.MemberID = ? 
                            GROUP BY
                                MemberID,
                                PlotNr 
                                ) dt
                        LEFT JOIN ktv_survey_plot g ON g.MemberID = dt.MemberID 
                        LEFT JOIN ktv_survey s on g.SurveyNr = s.SurveyNr
                        AND g.PlotNr = dt.PlotNr 
                        AND g.SurveyNr = dt.SurveyNr 
                        LEFT JOIN ktv_survey_plot_status d ON d.MemberID = g.MemberID 
                        AND d.PlotNr = g.PlotNr AND s.StatusCode = 'active' GROUP BY g.PlotNr ORDER BY g.SurveyNr DESC";
            $g = $this->db->query($sql, array($id));
            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $sql="SELECT
                        a.`latitude`
                        , a.`longitude`
                    FROM
                        ktv_survey_plot_polygon a
                    WHERE
                        a.`MemberID` = ?
                        AND a.`PlotNr` = ?
                        AND a.`SurveyNr` = ?
                        AND a.`StatusCheck` in ('verified','new')
                        AND a.`Revision` = (
                            SELECT
                                sub.Revision
                            FROM
                                ktv_survey_plot_polygon sub
                            WHERE
                                sub.`MemberID` = ?
                                AND sub.`PlotNr` = ?
                                AND sub.`SurveyNr` = ?
                                AND sub.`StatusCheck` in ('verified','new')
                            ORDER BY sub.`Revision` DESC
                            LIMIT 1
                        )
                    ORDER BY a.`Revision` ASC, a.`OrderNr` ASC";
                    $p = array(
                        $row->MemberID['value'],
                        $row->GardenNr['value'],
                        $row->SurveyNr['value'],
                        $row->MemberID['value'],
                        $row->GardenNr['value'],
                        $row->SurveyNr['value']
                    );
                    $query = $this->db->query($sql,$p);
                    $no = 0;
                    $poly = [];
                    if ($query->num_rows() > 0) {
                        $poly = $query->result_array();
                    }

                    $row->size['value'] = $this->angka($row->size['value']) . " Ha";

                    //Production
                    $production = $row->production['value'];
                    if($production){
                        $production = $this->angka($production);
                    }else{
                        $production = 0;
                    }
                    $row->production['value'] = $production . " Ton";
                    //$row->production['value'] = is_numeric($row->production['value']) ? number_format($this->angka($row->production['value']), 0) . " Kg" : "0 Kg";

                    // average Yield
                    $averageYield = $row->averageYield['value'];
                    if($averageYield){
                        $averageYield = $this->angka($averageYield);
                    }else{
                        $averageYield = 0;
                    }
                    $row->averageYield['value'] = $averageYield . " Ton/Ha";


                    // Production Tree
                    $productiveTree = $row->productiveTree['value'];
                    if($productiveTree){
                        $productiveTree = $this->angka($productiveTree);
                    }else{
                        $productiveTree = 0;
                    }
                    $row->productiveTree['value'] = $productiveTree;

                    //$row->productiveTree['value'] = is_numeric($row->productiveTree['value']) ? number_format($this->angka($row->productiveTree['value']), 0)  : "0";

                    $ret[$i] = array(
                        'type' => "kebun",
                        'id' => $i + 1,
                        'attributes' => array(
                            'certification'     => $row->certification,
                            'gardenNr'          => $row->GardenNr,
                            'lastModifiedBy'    => $row->LastModifiedBy,
                            'DateUpdated'       => $row->DateUpdated,
                            'landOwnerShip'     => $row->landOwnerShip,
                            'size'              => $row->size,
                            'landCerticate'     => $row->landCerticate,
                            'landUse'           => $row->landUse,
                            'production'        => $row->production,
                            'productivity'      => $row->productivity,
                            'averageYield'      => $row->averageYield,
                            'productiveTree'    => $row->productiveTree,
                            'SoilType'          => $row->SoilType,
                            'treeSeed'          => $row->treeSeed,
                            'BatasUtara'        => $row->BatasUtara,
                            'BatasSelatan'      => $row->BatasSelatan,
                            'BatasTimur'        => $row->BatasTimur,
                            'BatasBarat'        => $row->BatasBarat,
                            'latitude'          => $row->latitude,
                            'longitude'         => $row->longitude,
                            'SurveyNr'          => $row->SurveyNr,
                            'polygon'           => $poly,
                            'totalOtherTree'    => $row->totalOtherTree
                        )
                    );
                    $i++;
                }

                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no garden found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }

        return $return;
    }

    function getFarmerTransactionDetailSummary($post, $data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];

        $startDate = @$data['attributes']['startDate'];
        $endDate = @$data['attributes']['endDate'];

        $where_date_sql = "";
        if ($startDate != null && $startDate != "" && $endDate != null && $endDate != "") {
            $startDate = $startDate." 00:00:00";
            $endDate = $endDate." 23:59:59";
            $where_date_sql = " AND st.DateTransaction >= ? AND st.DateTransaction <= ?";
        }

        $sql = "SELECT 
            SUM(st.BrondolanNetWeight) AS Nett,
            null as InsentifeWaste,
            SUM(st.TotalPayment) TotalPayment,
            COUNT(st.SupplyTransID) as TransactionCount,
            '0' DisburseServiceCharge
        FROM
            ktv_tc_supplychain_transaction st
            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( sb.SupplyOrgID, st.SupplychainID )
            LEFT JOIN ktv_members m ON m.MemberID = st.SupplyID
            LEFT JOIN ktv_supplychain_transaction_confirmation ksp on ksp.SupplyTransID = st.SupplyTransID
        WHERE
            SupplyType != 'Batch' AND m.MemberDisplayID = ? ".$where_date_sql;

        if ($where_date_sql == "") {
            $g = $this->db->query($sql,array($farmerid));
        }
        else{
            $g = $this->db->query($sql,array($farmerid, $startDate, $endDate));
        }
        
        if ($g->num_rows() > 0) {
            $i = 0;
            $data_convert = convert_languagetransaction($g->result(), 'in');
            foreach ($data_convert as $row) {
                if ($row->Nett["value"] == null) {
                    $row->Nett["value"] = "0";
                }
                if ($row->TotalPayment["value"] == null) {
                    $row->TotalPayment["value"] = "0";
                }
                if ($row->InsentifeWaste["value"] == null) {
                    $row->InsentifeWaste["value"] = "0";
                }
                if ($row->TransactionCount["value"] == null) {
                    $row->TransactionCount["value"] = "0";
                }
                if ($row->DisburseServiceCharge["value"] == null) {
                    $row->DisburseServiceCharge["value"] = "0";
                }
                $ret[$i] = array(
                    'type' => "transaction",
                    'id' => $i + 1,
                    'attributes' => array(
                        'Nett' => $row->Nett,
                        'TotalPayment' => $row->TotalPayment,
                        'InsentifeWaste' => $row->InsentifeWaste,
                        'TransactionCount' => $row->TransactionCount,
                        'DisburseServiceCharge' => $row->DisburseServiceCharge,
                        'value' => ""
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no transaction found"
            );
        }

        return $return;
    }

    function getFarmerTransaction($data) {

        $header = $this->input->request_headers();
        $start = (int) $header['Offset'] ?? 0;
        $limit = (int) $header['Limit'] ?? 5;

        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];

        $startDate = @$data['attributes']['startDate'];
        $endDate = @$data['attributes']['endDate'];

        $where_date_sql = "";
        if ($startDate != null && $startDate != "" && $endDate != null && $endDate != "") {
            $startDate = $startDate." 00:00:00";
            $endDate = $endDate." 23:59:59";
            $where_date_sql = " AND a.DateTransaction >= ? AND a.DateTransaction <= ?";
        }

        $sql = "
        SELECT
            a.`SupplyTransID`,
            a.`SupplychainID`,
            a.`SupplyBatchID`,
            a.`TransNumber` AS SupplyBatchNumber,
            a.`TransNumber` as InvoiceNumber,
            a.`DateTransaction` AS TransactionDate,
            'PalmOil' AS CommodityName,
            '' AS CommoditySpecies,
            pt.PalmTypeName AS CommodityType,
        IF
            ( a.SupplyType = 'Farmer', 'Certified', 'Non Certified' ) AS TransactionType,
        CASE
                
                WHEN a.SupplybaseCategoryID = 1 THEN
                'Farmer Plasma' 
                WHEN a.SupplybaseCategoryID = 2 THEN
                'Direct Smallholder' 
                WHEN a.SupplybaseCategoryID = 3 THEN
                'Agent / Dealer / Vendor' 
                WHEN a.SupplybaseCategoryID = 4 THEN
                'Owner Estate' 
                WHEN a.SupplybaseCategoryID = 5 THEN
                'External Estate' ELSE 'Agent / Dealer / Vendor' 
            END AS SupplyType,
            a.`PlantationNr`,
            a.`PlantationNr` FarmNumber,
            a.`VolumeBruto`,
            a.`VolumeNetto`,
            a.`VolumeCutting`,
            a.`PackageID`,
            a.`Bunches`,
            a.`FFBCount` PackageNumber,
            a.`PackageWeight`,
            a.`DetailTypeID`,
            a.`TransStatusID`,
            a.`FarmingTypeID`,
            a.ContractPrice,
            a.NetPrice,
            a.DiscountPrice,
            a.TotalPayment,
            a.PaymentReduction,
            a.PaymentPaid AS PaymentAmount,
            a.isTraceable,
            a.`Notes`,
            a.`ChangeLog`,
            a.`ChangeBy`,
            a.`DateCreated`,
            a.`CreatedBy`,
            a.`DateUpdated`,
            a.`LastModifiedBy`,
            IFNULL(a.`PaymentStatusID`,0),
            IFNULL(pmstts.`PaymentStatus`, 'Not yet paid') AS PaymentStatus,
            a.`PaymentMethodID`,
            a.`uid`,
            b.`Latitude`,
            b.`Longitude`,
            c.PackageType,
        IF
            (
                b.MemberName IS NULL 
                OR b.MemberName = '',
            IF
                (
                    m2.MillName IS NULL 
                    OR m2.MillName = '',
                IF
                    (
                        a.MillOther IS NULL 
                        OR a.MillOther = '',
                    IF
                        (
                            mem.NAME IS NULL 
                            OR mem.NAME = '',
                        IF
                            (
                                kms.agCompanyName IS NULL 
                                OR kms.agCompanyName = '',
                            IF
                                (
                                    a.DOOther IS NULL 
                                    OR a.DOOther = '',
                                IF
                                    ( a.AgentOther IS NULL OR a.AgentOther = '', 'Nonfarmer', a.AgentOther ),
                                    a.DOOther 
                                ),
                                kms.agCompanyName 
                            ),
                            mem.NAME 
                        ),
                        a.MillOther 
                    ),
                    m2.MillName 
                ),
                b.MemberName 
            ) SupplierName,
            e.Name as Collector,
            IFNULL(
                b.MemberDisplayID,
            IFNULL( bb.MemberID, '-' )) AS MemberDisplayID,
            IFNULL(
            IF
                (
                    b.MemberID <> 0,
                    b.MemberID,
                IF
                    (
                        a.MillID <> 0 
                        AND ( a.MillOther IS NULL OR a.MillOther = '' ),
                        a.MillID,
                    IF
                        (
                            a.DOID <> 0 
                            AND ( a.DOOther IS NULL OR a.DOOther = '' ),
                            a.DOID,
                        IF
                            (
                                a.AgentID <> 0 
                                AND ( a.AgentOther IS NULL OR a.AgentOther = '' ),
                                a.AgentID,
                            IF
                                ( a.SupplyID <> 0 AND ( a.MillOther IS NULL OR a.MillOther = '' ), a.SupplyID, 'Unregistered Supplier' ) 
                            ) 
                        ) 
                    ) 
                ),
                'Unregistered Supplier' 
            ) MemberID,
            IFNULL(
            IF
                (
                    a.MillID IS NOT NULL 
                    OR a.MillID <> '',
                    'external',
                IF
                    ( a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other' ) 
                ),
                'other' 
            ) SellerType,
            e.ObjID,
            a.MillID,
            a.MillOther,
        IF
            ( a.MillOther IS NULL OR a.MillOther = '', '', TRUE ) OtherMill,
            a.DOID,
            a.DOOther,
        IF
            ( a.DOOther IS NULL OR a.DOOther = '', '', TRUE ) OtherDO,
            a.AgentID,
            a.AgentOther,
        IF
            ( a.AgentOther IS NULL OR a.AgentOther = '', '', TRUE ) OtherAgent,
            a.AgentOtherNIK,
            a.AgentOtherSurvey,
        IF
            ( b.isCertified != '', cp.CertProgName, 'Not Certified' ) Certified,
            a.SupplyType AS SalesType,
        IF
            ( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
        CASE
                
                WHEN a.SupplyBatchID IS NULL THEN
                '-' ELSE IFNULL( vso2.`Name`, b.MemberName ) 
            END AS BatchFrom,
            a.PackageID,
            a.PackageNumber,
            a.PackageWeight,
            a.VolumeCutting,
            d.SupplyBatchDate,
            a.ContractPrice TotalPrice,
            a.`BrondolanGrossWeight` AS Gross,
            a.`FFBNettWeight` AS Nett,
            a.uid,
            a.BrondolanPrice AS TotalPrice,
            pymtd.PaymentMethod,
            pymtd.PaymentLabel,
            '' PaymentDetailID,
            '' ServiceCharge,
            '' Transport,
            ktspm.TotalPaid,
            ktspm.TotalPaidCash,
            ktspm.TotalPaidPayment,
            ktspm.FeeDisburse,
            ktspm.TotalDisburse,
            ktspm.DisburseDetailStatusID,
            ktspm.DisburseDetailStatusName
        FROM
            ktv_tc_supplychain_transaction a
            LEFT JOIN ktv_members b ON a.SupplyID = b.MemberID 
            AND a.SupplyType != 'Batch'
            LEFT JOIN ktv_members bb ON a.SupplyID = bb.MemberID
            LEFT JOIN ktv_ref_certification_program cp ON cp.CertProgID = b.isCertified
            LEFT JOIN ktv_tc_supplychain_batch d ON a.SupplyID = d.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org e ON e.SupplychainID = a.SupplychainID
            LEFT JOIN ktv_trace_package c ON a.PackageID = c.PackageID
            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = a.SupplyID 
            AND a.SupplyType = 'Batch'
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = sb2.SupplyOrgID
            LEFT JOIN ktv_mill m2 ON m2.MillID = a.MillID
            LEFT JOIN view_tc_supplychain_org mem ON mem.SupplychainID = a.MillID
            LEFT JOIN ktv_members mem2 ON mem2.MemberID = a.AgentID
            LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = a.SupplyID 
            AND a.SupplyType = 'NonFarmer'
            LEFT JOIN ktv_members_extension kms ON kms.MemberID = vso3.ObjID
            LEFT JOIN ref_tc_palm_type pt ON pt.PalmTypeID = a.PalmTypeID

            LEFT JOIN ktv_tc_supplychain_payment_method ktspm ON ktspm.SupplyTransID = a.SupplyTransID
            LEFT JOIN ktv_tc_supplychain_payment_method_detail_expired exp ON exp.PaymentDetailID=ktspm.uid AND ktspm.PaymentStatusID=88
            LEFT JOIN ktv_tc_supplychain_payment_method_bulky pmb ON pmb.PaymentBulkyID=ktspm.PaymentBulkyID

            LEFT JOIN ref_tc_payment_method_status pmstts ON pmstts.PaymentStatusID=pmb.PaymentStatusID
            LEFT JOIN ref_tc_payment_method pymtd ON pymtd.PaymentMethodID = ktspm.PaymentMethodID
        WHERE
            a.StatusCode = 'active' 
            AND b.MemberDisplayID = ?
            AND d.SupplyBatchID IS NULL ".$where_date_sql."
        ORDER BY
            a.DateTransaction DESC
        LIMIT ".$limit." OFFSET ".$start;
        if ($where_date_sql == "") {
            $g = $this->db->query($sql,array($farmerid));
        }
        else{
            $g = $this->db->query($sql,array($farmerid, $startDate, $endDate));
        }
        $total = $this->db->query('SELECT FOUND_ROWS() totalrec')->row()->totalrec;
        // $PaymentDetail = [];
        $PurchasedItem = [];

        if ($g->num_rows() > 0) {
            $i = 0;
            $data_convert = convert_language($g->result(), 'in');
            foreach ($data_convert as $row) {
//                $sqlq = "SELECT  '' QualityID, '' FAQResult, '' FAQReward, '' `Name`";
//                $q = $this->db->query($sqlq, array($row->SupplyTransID['value']));
//                $q_convert = convert_language($q->result(), 'in');
//                if($row->Status['value'] == 'confirmed'){
//                    $status = 1;
//                }else if($row->Status['value'] == 'rejected'){
//                    $status = 2;
//                }else{
//                    $status = 0;
//                }

                $row->TotalPaidCash['value'] = ($row->TotalPaidCash['value'] != "") ? number_format($row->TotalPaidCash['value']) : "0";
                $row->TotalPaidPayment['value'] = ($row->TotalPaidPayment['value'] != "") ? number_format($row->TotalPaidPayment['value']) : "0";
                $row->TotalPrice['value'] = ($row->TotalPrice['value'] != "") ? number_format($row->TotalPrice['value']) : "0";
                $row->TotalPayment['value'] = ($row->TotalPayment['value'] != "") ? number_format($row->TotalPayment['value']) : "0";
                $row->VolumeNetto['value'] = ($row->VolumeNetto['value'] != "") ? number_format($row->VolumeNetto['value']) : "0";
                $row->ContractPrice['value'] = ($row->ContractPrice['value'] != "") ? number_format($row->ContractPrice['value']) : "0";

                $PaymentDetail = [];

                if ($row->TotalPaidCash['value'] != 0) {
                    $PaymentDetail[] = [
                        "PaymentDetailID" => (object) [
                            "label" =>"PaymentDetailID",
                            "value" =>"",
                        ],
                        "PaymentMethod" => (object) [
                            "label" =>"PaymentMethod",
                            "value" =>"Cash",
                        ],
                        "PaymentLabel" => (object) [
                            "label" =>"PaymentMethod",
                            "value" =>"Cash",
                        ],
                        "DisburseAmount" => $row->TotalPaidCash,
                        "ServiceCharge" =>(object) [
                            "label" =>"ServiceCharge",
                            "value" =>"",
                        ],
                        "TotalDisburse" => (object) [
                            "label" =>"TotalDisburse",
                            "value" =>"",
                        ],
                    ]; 
                }

                if ($row->TotalPaidPayment['value'] != 0) {
                    $PaymentDetail[] = [
                        "PaymentDetailID" => $row->PaymentDetailID,
                        "PaymentMethod" => $row->PaymentMethod,
                        "PaymentLabel" => $row->PaymentMethod,
                        "DisburseAmount" => $row->TotalPaidPayment,
                        "ServiceCharge" => $row->ServiceCharge,
                        "TotalDisburse" => $row->TotalDisburse
                    ]; 
                }
                
                $PurchasedItem = [
                    array(
                        "CommodityName" => $row->CommodityName,
                        "CommoditySpecies" => $row->CommoditySpecies,
                        "CommodityType" => $row->CommodityType,
                        "Qty" => $row->VolumeNetto,
                        "SatuanBerat" => array('label' => "SatuanBerat", 'value' => "Kg"),
                        "PricePerunit" => $row->ContractPrice,
                        // "TotalAmount" => array('label' => "TotalAmount", 'value' => number_format($row->Nett['value'] * $PricePerunit)),
                        "TotalAmount" => $row->TotalPayment,
                        "Currency" => array('label' => "Currency", 'value' => "Rp")
                    )];


                $ret[$i] = array(
                    'type' => "transaction",
                    'id' => $i + 1,
                    'attributes' => array(
                        'TransactionId'         => $row->SupplyTransID,
                        'CommodityName'         => $row->CommodityName,
//                        'Status'                => $status,
                        'TransactionDate'       => $row->TransactionDate,
                        'SupplyBatchNumber'     => $row->SupplyBatchNumber,
                        'SupplyBatchDate'       => $row->SupplyBatchDate,
//                        'VolumeCutting'         => $row->VolumeCutting,
                        'VolumeCutting'         => array("label"=>"VolumeCutting","value"=>0),
                        'Gross'                 => $row->Gross,
//                        'PackageWeight'         => array("label"=>"PackageWeight","value"=>0),
                        'PackageWeight'         => $row->PackageWeight,
                        'PackageNumber'         => $row->PackageNumber,
                        'Nett'                  => $row->VolumeNetto,
                        'InvoiceNumber'         => $row->InvoiceNumber,
                        'ContractPrice'         => $row->ContractPrice,
//                        'InsentifeWaste'        => $row->InsentifeWaste,
//                        'InsentifeBrix'         => $row->InsentifeBrix,
                        'TotalPrice'            => $row->TotalPayment,
                       'Transport'             => $row->Transport,
//                        'PremiumFarmer'         => $row->PremiumFarmer,
                       'TotalPaymentNoTax'     => $row->TotalPayment,
                        'Tax'                   => array('label' => "Tax", 'value' => 0),
                        'TotalPayment'          => $row->TotalPayment,
                        'PaymentStatus'          => $row->PaymentStatus,
                        'uid'          => $row->uid,
                        'CommoditySpecies' => $row->CommoditySpecies,
                        'CommodityType' => $row->CommodityType,
                        'TransactionType' => $row->TransactionType,
                        'Collector'             => $row->Collector,
                        'Quality'               => array(),
                        'PaymentDetail' => $PaymentDetail,
                        'PurchasedItem' => $PurchasedItem,
                        'TotalAmount' => $row->TotalPayment,
                        'Dicsount' => array('label' => "Dicsount", 'value' => 0),
                        'TaxItem' => array('label' => "TaxItem", 'value' => 0),
                        'TotalTransportFee' => array('label' => "TotalTransportFee", 'value' => 0),
                        'TotalServiceCharge' => array('label' => "TotalServiceCharge", 'value' => 0),
                        "Currency" => array('label' => "Currency", 'value' => "Rp"),
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no transaction found"
            );
        }

        return $return;
    }

    function getFarmerPremium($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "SELECT
                i.IMSID,
                rcp.CertProgName AS certStandar,
                ch.CertHolderOrgName AS certHolder,
                i.CertificationStart,
                i.CertificationEnd,
                i.ValidityStart,
                i.ValidityEnd,
                SUM(IFNULL(premi.FAQVolumeNetto, 0)) AS Netto,
                SUM(IFNULL(premi.PremiumFarmer, 0)) AS PremiumFarmer
            FROM
                ktv_cocoa_certification_certified_farmer ccf
                LEFT JOIN ktv_ims i ON i.IMSID=ccf.IMSID
                LEFT JOIN ktv_ims_master im ON im.IMSMasterID=i.IMSMasterID
                LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=im.CertHolderID
                LEFT JOIN ktv_ref_certification_program rcp ON rcp.CertProgID=ch.CertProgID
                LEFT JOIN (
                    SELECT
                    SUBSTR(IF(st2.SupplyTransID IS NULL, st.DateTransaction, st2.DateTransaction) , 1, 10) DateTransaction, 
            IF(st2.SupplyTransID IS NULL, st.FAQVolumeNetto, st2.FAQVolumeNetto) AS FAQVolumeNetto,
                            clr.SupplychainID AS CollectorID,
                            tdr.TraderName AS Collector,
                            IFNULL
            (f.FarmerID, nf.FarmerID) AS FarmerID,
                            IFNULL
            (f.FarmerName, nf.FarmerName) AS FarmerName,
                            v.Village, sd.SubDistrict, d.District,
                            c.GroupName,
            IF(sf.IsCertified='1',
            IF(sf.CertificationHolder IS NOT NULL && sf.CertificationHolder!='', sf.CertificationHolder, 'MARS'), '') CertificationHolder,
            (
            IF(IF(st2.SupplyTransID IS NULL, st.SupplyType, st2.SupplyType)='Farmer',
            (IFNULL
            (pre.PremiumFarmer, 0) * cal.DailyRate / 1000 * cal.RecoveryPercentage / 100 *
            IF(st2.SupplyTransID IS NULL, st.FAQVolumeNetto, st2.FAQVolumeNetto)), 0)) AS PremiumFarmer,
            (
            IF(IF(st2.SupplyTransID IS NULL, st.SupplyType, st2.SupplyType)='Farmer' AND sf.IsCertified='1' AND sf.CertificationHolder!='',
            (IFNULL
            (pre.PremiumFO, 0) * cal.DailyRate / 1000 * cal.RecoveryPercentage / 100 *
            IF(st2.SupplyTransID IS NULL, st.FAQVolumeNetto, st2.FAQVolumeNetto)), 0)) AS PremiumFO,
            (
            IF(IF(st2.SupplyTransID IS NULL, st.SupplyType, st2.SupplyType)='Farmer',
            (IFNULL
            (pre.PremiumCollector, 0) * cal.DailyRate / 1000 * cal.RecoveryPercentage / 100 *
            IF(st2.SupplyTransID IS NULL, st.FAQVolumeNetto, st2.FAQVolumeNetto)), 0)) AS PremiumCollector
                    FROM
                            ktv_tc_supplychain_batch sb
                            LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyBatchID = sb.SupplyBatchID
                            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                            LEFT JOIN ktv_farmer f ON f.FarmerID=
            IF(st2.SupplyTransID IS NULL, st.SupplyID, st2.SupplyID) AND
            IF(st2.SupplyTransID IS NULL, st.SupplyType, st2.SupplyType) NOT IN
            ('Batch', 'NonFarmer')
                            LEFT JOIN ktv_supplychain_farmer sf ON sf.FarmerID=f.FarmerID
                            LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=
            IF(st2.SupplyTransID IS NULL, st.SupplyID, st2.SupplyID) AND
            IF(st2.SupplyTransID IS NULL, st.SupplyType, st2.SupplyType)='NonFarmer'
                            LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                            LEFT JOIN ktv_village v ON v.VillageID=IFNULL(f.VillageID, nf.FarmerVillageID)
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                            LEFT JOIN ktv_supplychain_org clr ON clr.SupplychainID=
            IF(st2.SupplyTransID IS NOT NULL, sb2.SupplyOrgID,
            IF(st.DDCollector IS NOT NULL AND st.DDCollector!=0, st.DDCollector, IFNULL
            (sf.SupplychainID, nf.FarmerCollectorID)))
                            LEFT JOIN ktv_traders tdr ON tdr.TraderID=clr.OrgID
                            LEFT JOIN ktv_supplychain_org ste ON ste.SupplychainID=sb.SupplyOrgID
                            LEFT JOIN ktv_warehouse wh ON wh.WarehouseID=ste.OrgID
                            LEFT JOIN ref_hub_area_rel arl ON arl.SupplyChainID=
            IF(st2.SupplyTransID IS NULL,
            IF(st.DDStatus='1' AND st.DDCollector > 0 , st.DDCollector, sb.SupplyOrgID), sb2.SupplyOrgID)
                            LEFT JOIN ref_hub_area ar ON ar.AreaID=arl.AreaID
                            LEFT JOIN ref_hub_sub_area_rel sar ON sar.AreaID=ar.AreaID AND sar.SupplychainID=arl.SupplychainID
                            LEFT JOIN ref_area_calendar cal ON cal.AreaID=ar.AreaID AND SUBSTR(cal.ProductionCode, 1, 5)=SUBSTR(sb.DestPO, 1, 5) AND cal.StatusCode='active'
                            LEFT JOIN ktv_ref_supplychain_slot_mars slot ON SUBSTR(IF(st2.SupplyTransID IS NULL, st.DateTransaction, st2.DateTransaction),12,8) BETWEEN slot.TimeStart AND slot.TimeEnd AND slot.StatusCode='active'
                            LEFT JOIN ktv_ref_supplychain_slot_mars ss ON st2.SupplyTransID IS NOT NULL AND SUBSTR(st.DateTransaction,12,8) BETWEEN ss.TimeStart AND ss.TimeEnd AND ss.StatusCode='active'
                            LEFT JOIN ktv_ref_premium_mars pre ON pre.StatusCode='active' AND SUBSTR(IF(st2.SupplyTransID IS NULL, st.DateTransaction, st2.DateTransaction), 1, 10) BETWEEN pre.PremiumStart AND pre.PremiumEnd AND pre.AreaID=ar.AreaID
                            LEFT JOIN ktv_supplychain_quota_detail_mars q ON q.SupplychainID=clr.SupplychainID AND SUBSTR(st2.DateTransaction,1,10)=q.QuotaDate
                            LEFT JOIN ktv_standard_incentive_collector_mars inc ON inc.AreaID=ar.AreaID AND inc.SubAreaID=IFNULL
            (sar.SubAreaID,0)
                                    AND SUBSTR(st.DateTransaction, 1, 10) BETWEEN inc.DateStart AND inc.DateEnd
                                    AND SUBSTR(st.DateTransaction, 12, 8) BETWEEN inc.TimeStart AND inc.TimeEnd
                                    AND st.FAQQualityBrix >= inc.StandardBrix 
                                    AND st2.FAQQualityBrix >= inc.StandardBrix
                                    AND st.SupplyType='Batch'
                                    AND q.SlotID IS NOT NULL
                            LEFT JOIN ktv_tc_supplychain_transaction_quality med ON med.SupplyTransID=st.SupplyTransID AND med.DetailID IN
            (1227, 1232)
                    WHERE
                            sb.SupplyOrgID IN
            (291, 293)
                            AND
            IF(st2.SupplyTransID IS NULL, st.IsCetak, st2.IsCetak)='1'
                            AND f.FarmerID=?
                    GROUP BY IFNULL
            (st2.SupplyTransID, st.SupplyTransID)
                    ORDER BY
            IF(st2.SupplyTransID IS NULL, st.DateTransaction, st2.DateTransaction) DESC
                ) premi ON premi.FarmerID=ccf.FarmerID AND premi.DateTransaction BETWEEN i.ValidityStart AND i.ValidityEnd
            WHERE
                ccf.FarmerID=?
            GROUP BY i.IMSID";
            $g = $this->db->query($sql, array($id, $id));

            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "premium",
                        'id' => $i + 1,
                        'attributes' => array(
                            'imsid' => $row->IMSID,
                            'certStandar' => $row->certStandar,
                            'certHolder' => $row->certHolder,
                            'certificationStart' => $row->CertificationStart,
                            'certificationEnd' => $row->CertificationEnd,
                            'validityStart' => $row->ValidityStart,
                            'validityEnd' => $row->ValidityEnd,
                            'netto' => $row->Netto,
                            'premiumFarmer' => $row->PremiumFarmer
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no premium found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }

        return $return;
    }

    function getTrader($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "SELECT 
                        vso.SupplychainID,
                        vso.OrgType,
                        vso.OrgID,
                        vso.`Name` AS `name`,
                        vso.`Name` AS `ownerName`,
                        vso.Longitude AS `longitude`,
                        vso.Latitude AS `latitude`,
                        vso.Address AS `address`,
                        f.Handphone AS `phone`,
                        '' AS `picture`,
                        v.Village,
                        sd2.SubDistrict,
                        d2.District AS `district`,
                        e.Province AS `province` 

                        FROM
                            ktv_farmer f 
                            LEFT JOIN ktv_supplychain_farmer sf ON sf.FarmerID=f.FarmerID
                            LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.FarmerID=f.FarmerID AND sf.FarmerID IS NULL
                            LEFT JOIN ktv_ims i ON i.IMSID=ccf.IMSID AND SUBSTR(NOW(), 1, 10) BETWEEN i.ValidityStart AND i.ValidityEnd
                            LEFT JOIN ktv_ims_master im ON im.IMSMasterID=i.IMSMasterID
                            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=im.CertHolderID
                            LEFT JOIN ktv_ims_buying_unit ib ON ib.IMSID=i.IMSID
                            LEFT JOIN view_supplychain_org vso ON (vso.SupplychainID=IFNULL(sf.SupplychainID, ch.SupplychainID) OR vso.SupplychainID=ib.SupplychainID)
                            LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                            LEFT JOIN ktv_village v2 ON v2.VillageID=f.VillageID
                            LEFT JOIN ktv_subdistrict sd2 ON sd2.SubDistrictID=v2.SubDistrictID
                            LEFT JOIN ktv_district d2 ON d2.DistrictID=sd2.DistrictID
                            LEFT JOIN ktv_traders t ON t.TraderID=vso.OrgID
                            LEFT JOIN ktv_province e  ON e.ProvinceID = d2.ProvinceID 
                        WHERE
                            f.FarmerID = ?
                            -- 731200016 Cargill
                            -- 732211493 Mars
                            AND IF(sf.FarmerID IS NOT NULL, 1, IF(d.DistrictID=d2.DistrictID, 1, 0))=1
                            AND vso.OrgType!='Gudang'
                        GROUP BY vso.SupplychainID";

            $g = $this->db->query($sql, array($farmerid));
            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "trader",
                        'id' => $i + 1,
                        'attributes' => array(
                            'name' => $row->name,
                            'ownerName' => $row->name,
                            'latitude' => $row->latitude,
                            'longitude' => $row->longitude,
                            'phone' => $row->phone,
                            'picture' => $row->picture,
                            'district' => $row->district,
                            'province' => $row->province,
                            'address' => $row->address,
                            'SupplychainID' => $row->SupplychainID
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no trader found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    public function getCollectorQuota($data){
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $sql = "SELECT
                    vso.SupplychainID,
                    vso.OrgType,
                    vso.OrgID,
                    vso.`Name` AS `name`,
                    vso.`Name` AS `ownerName`,
                    vso.Longitude AS `longitude`,
                    vso.Latitude AS `latitude`,
                    vso.Address AS `address`,
                    m.Handphone AS `phone`,
                    '' AS `picture`,
                    v.Village,
                    sd.SubDistrict,
                    d.District AS `district`,
                    p.Province AS `province` 
                FROM
                    view_supplychain_org vso 
                    LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
                    LEFT JOIN ktv_members m ON m.MemberID=vso.OrgID
                WHERE 
                    vso.SupplychainID= 25"; //what?

        $g = $this->db->query($sql);
        if ($g->num_rows() > 0) {
            $i = 0;
            $data_convert = convert_language($g->result(), 'in');
            foreach ($data_convert as $row) {
                $ret[$i] = array(
                    'type' => "trader",
                    'id' => $i + 1,
                    'attributes' => array(
                        'SupplychainID' => 25,
                        'QuotaDate' => date('Y-m-d'),
                        'Quota' => 10000
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no Quota found"
            );
        }

        return $return;
    }

    public function getFarmerIncentive($data){
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $SupplychainID = @$data['attributes']['SupplychainID'];
        if ($SupplychainID) {
            $sql = "SELECT
                        inc.StandardIncentiveID, SlotNr, DATE_FORMAT(TimeStart, '%H:%i') TimeStart,
                        DATE_FORMAT(TimeEnd, '%H:%i') TimeEnd, StandardBrix, StandardIncentive
                    FROM
                        ktv_standard_incentive_farmer_mars inc
                        LEFT JOIN ref_hub_area_rel rel ON rel.AreaID=inc.AreaID
                    WHERE
                        SUBSTR( NOW( ), 1, 10 ) BETWEEN inc.DateStart AND inc.DateEnd
                        AND rel.SupplyChainID=?";

            $g = $this->db->query($sql, array($SupplychainID));
            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "trader",
                        'id' => $i + 1,
                        'attributes' => array(
                            'StandardIncentiveID' => $row->StandardIncentiveID,
                            'SlotNr' => $row->SlotNr,
                            'TimeStart' => $row->TimeStart,
                            'TimeEnd' => $row->TimeEnd,
                            'StandardBrix' => $row->StandardBrix,
                            'StandardIncentive' => $row->StandardIncentive
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no Farmer Incentive found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no Quota found"
            );
        }
        return $return;
    }

    public function getDailyPrice($data){
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $SupplychainID = @$data['attributes']['SupplychainID'];
        if ($SupplychainID) {
            $sql = "SELECT
                        p.PriceID, p.PriceDateStart, p.PriceDateEnd, p.FAQPrice Price
                    FROM
                        ktv_supplychain_price p
                    WHERE
                        SUBSTR( NOW( ), 1, 10 ) BETWEEN p.PriceDateStart AND p.PriceDateEnd
                        AND p.PriceSupplychainID=?";

            $g = $this->db->query($sql, array($SupplychainID));
            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "trader",
                        'id' => $i + 1,
                        'attributes' => array(
                            'PriceID' => $row->PriceID,
                            'PriceDateStart' => $row->PriceDateStart,
                            'PriceDateEnd' => $row->PriceDateEnd,
                            'Price' => $row->Price,
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no Daily Price found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no Daily Price found"
            );
        }
        return $return;
    }

    function getTraining($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);
        //if ($id != '') {

        /*
        $sql = "SELECT
                    btf.CpgBatchTrainingsFarmerID id,
                    t.CpgTrainings name,
                    SUBSTR(bt.TrainingStart, 1,10) start,
                    SUBSTR(bt.TrainingEnd, 1,10) end,
                    bt.TrainingDays days,
                    IFNULL(t2.CpgAbbre,t.CpgAbbre) trainingType
                FROM
                    ktv_cpg_batch_trainings_farmers btf
                    LEFT JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID=btf.CpgBatchTrainingID
                    LEFT JOIN ktv_cpg_batch b ON b.CpgBatchID=bt.CpgBatchID
                    LEFT JOIN ktv_cpg_trainings t ON t.CpgTrainingsID=bt.CPGtrainingsID
                    LEFT JOIN ktv_cpg_batch_trainings_sub_topics tst ON tst.CpgBatchTrainingID=bt.CpgBatchTrainingID
                    LEFT JOIN ktv_cpg_trainings t2 ON t2.CpgTrainingsID=tst.SubCpgTrainingsID
                WHERE btf.FarmerID=? AND btf.StatusCode='active'";
        */

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                        a.FarmerTrainingID as id,
                        CpgTrainings as name,
                        District as tot,
                        count(FarmerTrainingsFarmerID) as participant,
                        date(TrainingStart) as start,
                        date(TrainingEnd) as end,
                        TrainingDays as days,
                        IFNULL( b.CpgAbbre, b.CpgAbbre ) trainingType 
                                    
                        from ktv_farmer_trainings a
                        left join ktv_district kd on a.TrainingDistrict=kd.DistrictID
                        left join ktv_cpg_trainings b on a.CPGtrainingsID=b.CpgtrainingsID
                        left join ktv_farmer_trainings_participants d on a.FarmerTrainingID=d.FarmerTrainingID AND d.`StatusCode` = 'active'
                    where d.FarmerID=?";

        $t = $this->db->query($sql, $id);
        if ($t->num_rows() > 0) {
            $i = 0;
            $data_convert = convert_language($t->result(), 'in');
            foreach ($data_convert as $row) {
                $ret[$i] = array(
                    'type' => "training",
                    'id' => $i + 1,
                    'attributes' => array(
                        'name' => $row->name,
                        'start' => $row->start,
                        'end' => $row->end,
                        'days' => $row->days,
                        'trainingType' => $row->trainingType
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no training found"
            );
        }
        /*} else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }*/
        return $return;
    }

    function getCertification($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);

        if ($id != '') {
            $sql = "
                SELECT
                    '' certStandar,
                    '' certHolder,
                    '' certChAddress,
                    '' certValidFromToDate,
                    '' address,
                    '' district,
                    '' province,
                    '' totalCertArea,
                    '' certVolume,
                    '' totalProductivity,
                    '' numberCertSalesToDate,
                    '' totalCertSales
                FROM
                ktv_members
                WHERE
                MemberID = ?
            ";
            $t = $this->db->query($sql, array($id, $id));
            if ($t->num_rows() > 0) {
                $data_convert = convert_language($t->result(), 'in');
                foreach ($data_convert as $row) {
                    $certValidFromToDate = date('M Y', strtotime($row->start['value'])) . ' - ' . date('M Y', strtotime($row->end['value']));
                    $ret[0] = array(
                        'type' => "certification",
                        'id' => 1,
                        'attributes' => array(
                            'certStandar' => $row->certStandar,
                            'certHolder' => $row->certHolder,
                            'certChAddress' => $row->certChAddress,
                            'certValidFromToDate' => $certValidFromToDate,
                            'adress' => $row->address,
                            'district' => $row->district,
                            'province' => $row->province
                        )
                    );
                    $ret[1] = array(
                        'type' => "production",
                        'id' => 1,
                        'attributes' => array(
                            'totalCertArea' => $row->totalCertArea,
                            'certVolume' => $row->certVolume,
                            'totalProductivity' => $row->totalProductivity,
                            'numberCertSalesToDate' => $row->numberCertSalesToDate,
                            'totalCertSales' => $row->totalCertSales
                        )
                    );
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no certification found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    function getCertification_ics_history($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "SELECT
                      a.FarmerID,
                      a.FarmerName,
                      c.GardenNr,
                      c.SurveyNr,
                      s.SurveyTxt,
                      c.ICSDate,
                      (
                      CASE
                          
                          WHEN c.StatusAudit = 1 THEN
                          'Lolos Audit' 
                          WHEN c.StatusAudit = 2 THEN
                          'Tidak Lolos Audit' 
                          WHEN c.StatusAudit = 3 THEN
                          'Lolos Audit dengan syarat' 
                        END 
                        ) StatusAudit,
                        c.DateRevisionAudit,
                        c.RecommendationAudit,
                        c.DateCreated 
                      FROM
                        ktv_farmer a
                        INNER JOIN ktv_cocoa_certification_audit_log c ON c.FarmerID = a.FarmerID
                        LEFT JOIN ktv_survey s ON s.SurveyNr = c.SurveyNr 
                    WHERE
                      a.FarmerID = ?";
            $t = $this->db->query($sql, array($id));
            if ($t->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($t->result(), 'in');

                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "certification ics history",
                        'id' => $row->FarmerID,
                        'attributes' => array(
                            "FarmerID" => $row->FarmerID,
                            "FarmerName" => $row->FarmerName,
                            "GardenNr" => $row->GardenNr,
                            "SurveyNr" => $row->SurveyNr,
                            "SurveyTxt" => $row->SurveyTxt,
                            "ICSDate" => $row->ICSDate,
                            "StatusAudit" => $row->StatusAudit,
                            "DateRevisionAudit" => $row->DateRevisionAudit,
                            "RecommendationAudit" => $row->RecommendationAudit,
                            "DateCreated" => $row->DateCreated

                        )
                    );

                    $i++;
                }
                //var_dump($ret);
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no certification ics history found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    function getFarmerNotification($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $supplychainid = @$data['attributes']['supplychainid'];
        $traderid = @$data['attributes']['traderid'];
        $partnerid = @$data['attributes']['partnerid'];
        $languagecode = @$data['attributes']['languagecode'];//@$data['attributes']['languagecode'] == '' ? 'en' : @$data['attributes']['languagecode'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        if(isset($languagecode)){
            if($languagecode == 'id'){
                $language = 'indonesia';
            } else if($languagecode == 'es'){
                $language = 'spanish';
            } else if($languagecode == 'fr'){
                $language = 'french';
            } else {
                $language = 'english';
            }
        }

        $type = @$data['type'];
        $partnerid = $partnerid == '' ? 9 : $partnerid;

        if(!isset($supplychainid)){
            $id = $this->getFarmerID($farmerid, $partnerid);
        } else {
            $id = $traderid;//$this->getFarmerID($farmerid, $partnerid);
        }

        if(!isset($partnerid) || empty($partnerid)){
            /*if appname == 'farmextension'
            $partnerid */
            $partnerid = 37;
        }

        if ($id != '') {
            if(!isset($supplychainid)){
                $sql = "
                    SELECT
                        a.DateCreated,
                        a.FarmerID,
                        a.NotifID,
                        a.OrgType,
                        a.OrgID,
                        a.NotifMessage,
                        a.StatusRead
                    FROM
                        ktv_mobile_notification a 
                    WHERE
                        FarmerID = ? OR (PartnerID = ? AND FarmerID IS NULL ) 
                ";
            } else {
                $sql = "
                    SELECT
                        a.DateCreated,
                        '' AS FarmerID,
                        a.AnnID AS NotifID,
                        '' AS OrgType,
                        '' AS OrgID,
                        '' AS NotifMessage 
                    FROM
                        cms_announcement a
                        LEFT JOIN cms_access b ON a.AnnID = b.ObjID AND b.ObjType = 'announcement' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                            AND b.RoleAccessTrader = 1
                        ) 
                        OR a.StatusType = 'public'
                        AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active'
                ";
            }
            $g = $this->db->query($sql, array($farmerid, $partnerid, $partnerid));

            foreach($g->result() as $key => $res){
                if($res->FarmerID == '' && $res->OrgType != 'broadcast'){
                    $g->result()[$key]->NotifMessage = $this->getContentLanguageValue('Announcement', $res->NotifID, 'Content', ucwords($language));
                }
                if($res->OrgType == 'Transaction'){
                    $g->result()[$key]->OrgType = 'Transaction';
                }
                if($res->StatusRead == 0){
                    $g->result()[$key]->StatusRead = 'New';
                } else {
                    $g->result()[$key]->StatusRead = 'Read';
                }

                if($res->OrgType == 'broadcast'){
                    if($this->_checkNotificatonStatusByFarmerID($res->NotifID, $id)){
                        $g->result()[$key]->StatusRead = 'Read';
                    } else {
                        $g->result()[$key]->StatusRead = 'New';
                    }
                }
            }

            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), $language);
                foreach ($data_convert as $row) {
                    $orgType = '';
                    if($row->OrgType == 'Transaction'){
                        $orgType = 'Transaction ';
                    } else {
                        $orgType = $row->OrgType;
                    }
                    $ret[$i] = array(
                        'type' => "notification",
                        'id' => $i + 1,
                        'attributes' => array(
                            'date' => $row->DateCreated,
                            'FarmerID' => $row->FarmerID,
                            'NotifID' => $row->NotifID,
                            'OrgType' => $row->OrgType,
                            'OrgID' => $row->OrgID,
                            'NotifMessage' => $row->NotifMessage,
                            'StatusRead' => $row->StatusRead
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no notification found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }

        return $return;
    }

    function getFarmerAgent($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            /*
            $sql = "SELECT
                        vso.SupplychainID id_1,
                        vso.OrgType type_1,
                        vso.`Name` name_1,
                        vso.Latitude latitude_1,
                        vso.Longitude longitude_1,
                        vso2.SupplychainID id_2,
                        vso2.OrgType type_2,
                        vso2.`Name` name_2,
                        vso2.Latitude latitude_2,
                        vso2.Longitude longitude_2,
                        vso3.SupplychainID id_3,
                        vso3.OrgType type_3,
                        vso3.`Name` name_3,
                        vso3.Latitude latitude_3,
                        vso3.Longitude longitude_3
                FROM
                        ktv_cocoa_certification_certified_farmer ccf
                        LEFT JOIN ktv_ims i ON i.IMSID=ccf.IMSID
                        LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=i.CertHolderID
                        LEFT JOIN ktv_ims_buying_unit ibu ON ibu.IMSID=ccf.IMSID
                        LEFT JOIN ktv_first_buyer fb ON fb.FirstBuyerID=i.FirstBuyerID
                        LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=ibu.SupplychainID
                        LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=ch.SupplychainID
                        LEFT JOIN ktv_supplychain_org_rel rel ON rel.ChildOrgId=vso2.SupplychainID
                        LEFT JOIN view_supplychain_org vso3 ON vso3.SupplychainID=rel.ParentOrgId
                WHERE
                        ccf.FarmerID = ?";
            */
            try {
                $g = $this->db->query($sql, array($id));
                var_dump($this->db->_error_message());

            } catch (Exception $exc) {
                var_dump('kampret error');

            }
            die;
            $g = $this->db->query($sql, array($id));
            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "agent",
                        'id' => $row->id_1,
                        'attributes' => array(
                            'id_1' => $row->id_1,
                            'type_1' => $row->type_1,
                            'name_1' => $row->name_1,
                            'latitude_1' => $row->latitude_1,
                            'longitude_1' => $row->longitude_1,
                            'id_2' => $row->id_2,
                            'type_2' => $row->type_2,
                            'name_2' => $row->name_2,
                            'latitude_2' => $row->latitude_2,
                            'longitude_2' => $row->longitude_2,
                            'id_3' => $row->id_3,
                            'type_3' => $row->type_3,
                            'name_3' => $row->name_3,
                            'latitude_3' => $row->latitude_3,
                            'longitude_3' => $row->longitude_3
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no SME found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }

        return $return;
    }

    function getFieldAgent($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $supplychainid = @$data['attributes']['supplychainid'];
        $partnerid = @$data['attributes']['partnerid'];
        $type = @$data['type'];

        if ($type == 'user') {
            $DistrictID = $this->db->query("SELECT d.DistrictID AS DistrictID FROM ktv_members a
                                            LEFT JOIN ktv_village v ON v.`VillageID` = a.`VillageID`
                                            LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                            LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID` WHERE a.MemberDisplayID=?"
                , array($farmerid))->row()->DistrictID;
        } else if ($type == 'trader') {
            $DistrictID = $this->db->query("SELECT d.DistrictID AS DistrictID FROM view_supplychain_org a
                                            LEFT JOIN ktv_village v ON v.`VillageID` = a.`VillageID`
                                            LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                            LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID` WHERE SupplychainID=?"
                , array($supplychainid))->row()->DistrictID;
        } else {
            $DistrictID = '';
        }

        $urlAWS = $this->config->item('CTCDN')."/";
        $d1 = $DistrictID == '' ? '/*' : '';
        $d2 = $DistrictID == '' ? '*/' : '';
        $sql = "SELECT
                    s.StaffID, u.UserId,p.PersonID, u.UserName, p.PersonNm, IF(p.Gender='m', 'Male', IF(p.Gender='f', 'Female', '-')) Gender, 
                    IF(p.OfficialCellPhone IS NOT NULL && p.OfficialCellPhone!=0 && p.OfficialCellPhone!='', p.OfficialCellPhone, p.PrivateCellPhone) Phone,
                    p.Email, p.Address, p.EmpNr, IFNULL(sa.Latitude, 0) Latitude, IFNULL(sa.Longitude,0) Longitude, CONCAT('$urlAWS',p.Photo) as PhotoUrl
                FROM
                    ktv_staffs s
                    LEFT JOIN ktv_staff_positions sp ON sp.StaffPosStaffID=s.StaffID
                    LEFT JOIN ktv_ref_position_type rpt ON rpt.PositionID=sp.StaffPosPositionID
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN sys_user u ON u.UserId=p.UserID
                    LEFT JOIN ktv_access_staff kas ON kas.UserId=u.UserId
                    LEFT JOIN (SElECT UserId, Latitude, Longitude, MAX(ActDateTime) ActDateTime FROM ktv_staff_activity GROUP BY UserId,Latitude,Longitude)
                    sa ON sa.UserId = u.UserId
                WHERE
                    s.StatusCode='active'
                    $d1 AND kas.DistrictID= ? $d2
                GROUP BY p.PersonID";
        $fa = $this->db->query($sql, array($DistrictID));
        if ($fa->num_rows() > 0) {
            $i = 0;
            $data_convert = convert_language($fa->result(), 'in');
            foreach ($data_convert as $row) {
                $ret[$i] = array(
                    'type' => "field_agent",
                    'id' => $row->StaffID,
                    'attributes' => array(
                        "StaffID" => $row->StaffID,
                        "user_name" => $row->UserName,
                        "PersonID" => $row->PersonID,
                        "PhotoUrl" => $row->PhotoUrl,
                        "EmpNr" => $row->EmpNr,
                        "name" => $row->PersonNm,
                        "Gender" => $row->Gender,
                        "phone" => $row->Phone,
                        "email" => $row->Email,
                        "Address" => $row->Address,
                        "Latitude" => $row->Latitude,
                        "Longitude" => $row->Longitude,
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No FA found in District"
            );
        }
        return $return;
    }

    function getNews_old($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $supplychainid = @$data['attributes']['supplychainid'];
        $partnerid = @$data['attributes']['partnerid'];
        $type = @$data['type'];

        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "SELECT 
                        NewsID,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                        IF(a.PhotoFile IS NOT NULL,CONCAT('".base_url()."/api/uploads/news/', a.PhotoFile),'') AS ImagePath,
                        Title,
                        Content,
                        tag
                   FROM
                        cms_news a 
                        LEFT JOIN cms_access b 
                             ON a.NewsID = b.ObjID 
                             AND b.ObjType = 'news' 
                   WHERE (
                             (
                                  FIND_IN_SET(?, b.PartnerIDImplode) 
                                  AND b.RoleAccessFarmer = 1
                             ) 
                             OR a.StatusType = 'public'
                        ) 
                        AND a.StatusCode = 'active'";
            $news = $this->db->query($sql, array($partnerid));
            if ($news->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($news->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "news",
                        'id' => $row->NewsID,
                        'attributes' => array(
                            "date" => $row->PublishDate,
                            "image" => $row->ImagePath,
                            "title" => $row->Title,
                            "content" => $row->Content,
                            "tag" => $row->tag
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no news found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    function getSeedlings($data) {

        $sql = "SELECT 
                        a.NurseryID,
                        a.Kapasitas AS 'Kapasitas',
                        a.ObjName,
                        DATE_FORMAT(a.Established, '%M %d, %Y') AS Established,
                        CONCAT(a.Panjang * a.Lebar, ' m2') AS 'Ukuran',
                        e.District,
                        d.SubDistrict,
                        c.Village,
                        a.Latitude,
                        a.Longitude 
                   FROM
                        ktv_nursery a 
                        LEFT JOIN 
                             (SELECT 
                                  a.NurseryID,
                                  a.ObjType,
                                  a.ObjID,
                                  IFNULL(
                                       b.GroupName,
                                       IFNULL(
                                            c.FarmerName,
                                            IFNULL(
                                                 d.TraderName,
                                                 IFNULL(e.CoopName, '')
                                            )
                                       )
                                  ) AS ObjName,
                                  IFNULL(
                                       b.VillageID,
                                       IFNULL(
                                            c.VillageID,
                                            IFNULL(
                                                 d.VillageID,
                                                 IFNULL(e.VillageID, '')
                                            )
                                       )
                                  ) AS ObjVillageID 
                             FROM
                                  ktv_nursery a 
                                  LEFT JOIN ktv_cpg b 
                                       ON a.ObjID = b.CPGid 
                                       AND a.ObjType = 'cpg' 
                                  LEFT JOIN ktv_farmer c 
                                       ON a.ObjID = c.FarmerID 
                                       AND a.ObjType = 'farmer' 
                                  LEFT JOIN ktv_traders d 
                                       ON d.TraderID = a.ObjID 
                                       AND a.ObjType = 'trader' 
                                  LEFT JOIN ktv_cooperatives e 
                                       ON e.CoopID = a.ObjID 
                                       AND a.ObjType = 'koperasi') b 
                             ON a.NurseryID = b.NurseryID 
                        LEFT JOIN ktv_village c 
                             ON c.VillageID = b.ObjVillageID 
                        LEFT JOIN ktv_subdistrict d 
                             ON d.SubDistrictID = c.SubDistrictID 
                        LEFT JOIN ktv_district e 
                             ON e.DistrictID = d.DistrictID 
                   WHERE a.StatusCode = 'active'";
        $news = $this->db->query($sql, array());
        if ($news->num_rows() > 0) {
            $i = 0;
            $data_convert = convert_language($news->result(), 'in');
            foreach ($data_convert as $row) {
                $ret[$i] = array(
                    'type' => "nursery",
                    'id' => $row->NurseryID,
                    'attributes' => array(
                        "NurseryID" => $row->NurseryID,
                        "Company Name" => $row->ObjName,
                        "Nursery Name" => $row->ObjName,
                        "Kapasitas" => $row->Kapasitas,
                        "Established" => $row->Established,
                        "Ukuran" => $row->Ukuran,
                        "District" => $row->District,
                        "SubDistrict" => $row->SubDistrict,
                        "Village" => $row->Village,
                        "Latitude" => $row->Latitude,
                        "Longitude" => $row->Longitude
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no seedlings found"
            );
        }

        return $return;
    }

    function postRegistration($data) {
        $partnerid = @$data['attributes']['partnerid'];
        $farmerid = @$data['attributes']['farmerid'];
        $full_name = @$data['attributes']['fullname'];
        $phone = @$data['attributes']['phone'];
        $email = @$data['attributes']['email'];
        $type = @$data['type'];

        $password = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 6)), 0, 6);

        if ($farmerid != '') {
            $id = $this->getNewFarmerID($farmerid, $phone);
            if ($id) {
                $exist = $this->FarmerUserValidation($id);
                if (!$exist) {
                    $sql = "INSERT INTO sys_user (
                                UserRealName,
                                UserName,
                                UserPassword,
                                UserEmail,
                                UserActive,
                                UserLanguage,
                                StatusCode
                           ) 
                           VALUES
                                (
                                ?,
                                ?,
                                ?,
                                ?,
                                'Yes',
                                'Indonesia',
                                'active') 
                   ";

                    $registration = $this->db->query($sql, array($full_name, $farmerid, do_hash($password, 'md5'), $email));

                    if ($this->db->affected_rows() > 0) {
                        $insert_id = $this->db->insert_id();
                        $return['data'] = array(
                            'type' => "registration",
                            'id' => $farmerid,
                            'attributes' => array(
                                "full_name" => $full_name,
                                "password" => $password,
                                "phone" => $phone,
                                "email" => $email
                            )
                        );
                        if ($email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $this->load->config('email');
                                // $this->load->library('email');
                                require_once 'application/third_party/phpmailer-hr/class.phpmailer.php';
                                $ObjMail = new PHPMailer();
                                $ObjMail->IsSMTP();

                                $ObjMail->SMTPSecure = 'tls';
                                $ObjMail->SMTPAuth = true;
                                $ObjMail->Host     = $this->config->item('email_Host');
                                $ObjMail->Port     = $this->config->item('email_Port');
                                $ObjMail->Username = $this->config->item('email_Username');
                                $ObjMail->Password = $this->config->item('email_Password');

                                $ObjMail->Priority = 0;
                                $ObjMail->SetFrom($this->config->item('email_from'), 'Koltiva - PalmoilTrace Support');

                                $ObjMail->Subject = 'Farm Cloud Registration';

                                $body = 'Register Successful, your password is ' . $password;

                                $tpl = file_get_contents('files/email/registration.html');
                                $root_created = $full_name;

                                $opening = "Proses registrasi berhasil dengan detail sebagai berikut : ";
                                $body = '
                                    <table width="100%">
                                        <tr>
                                            <td width="25%">Username</td>
                                            <td width="10px">:</td>
                                            <td>'.$farmerid.'</td>
                                        </tr>
                                        <tr>
                                            <td width="25%">Password</td>
                                            <td width="10px">:</td>
                                            <td>'.$password.'</td>
                                        </tr>
                                    </table>';
                                $tpl = str_replace('{{root_created}}', $root_created, $tpl);
                                $tpl = str_replace('{{opening}}', $opening, $tpl);
                                $tpl = str_replace('{{body}}', $body, $tpl);

                                $ObjMail->Body = $tpl;
                                $ObjMail->IsHTML(true);

                                $ObjMail->AddAddress($email);
                                $result = $ObjMail->Send();

                                $ObjMail->ClearAddresses();
                                $ObjMail->ClearAllRecipients();
                                $ObjMail->IsHTML(false);
                            }
                        }
                        $return['data'] = array(
                            'type' => "registration",
                            'id' => $farmerid,
                            'attributes' => array(
                                'farmerid' => $farmerid,
                                'message' => 'registration successful'
                            )
                        );
                    } else {
                        $return['errors'] = array(
                            'status' => "001",
                            'title' => "request failed",
                            'detail' => "registration failed"
                        );
                    }
                } else {
                    $return['errors'] = array(
                        'status' => "001",
                        'title' => "request failed",
                        'detail' => "User already exist!"
                    );
                }
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no farmer found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "Farmer ID not found"
            );
        }
        return $return;
    }

    function getVideo_old($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $supplychainid = @$data['attributes']['supplychainid'];
        $partnerid = @$data['attributes']['partnerid'];
        $type = @$data['type'];

        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "SELECT 
                        VidID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        Title AS title,
                        PicThumb AS image,
                        VideoUrl AS content 
                   FROM
                        cms_video a 
                        LEFT JOIN cms_access b 
                             ON a.VidID = b.ObjID 
                             AND b.ObjType = 'video' 
                   WHERE (
                            (
                                 FIND_IN_SET(?, b.PartnerIDImplode) 
                                 AND b.RoleAccessFarmer = 1
                            ) 
                            OR a.StatusType = 'public'
                       ) 
                       AND a.StatusCode = 'active' ";
            $video = $this->db->query($sql, array($partnerid));
            if ($video->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($video->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "video",
                        'id' => $row->id,
                        'attributes' => array(
                            "date" => $row->date,
                            "image" => $row->image,
                            "title" => $row->title,
                            "content" => $row->content
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no video found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    // function getFarmerTraining($data) {
    //     $farmerid = @$data['attributes']['farmerid'];
    //     $partnerid = @$data['attributes']['partnerid'];
    //     $id = $this->getFarmerID($farmerid, $partnerid);
    //     if ($id != '') {
    //         $sql = "SELECT
    //                     btf.CpgBatchTrainingsFarmerID TrainingID,
    //                     t.CpgTrainings training,
    //                     SUBSTR( bt.TrainingStart, 1, 10 ) TrainingStart,
    //                     SUBSTR( bt.TrainingEnd, 1, 10 ) TrainingEnd,
    //                     TrainingDays,
    //                     t.CpgAbbre,
    //                     cpg.GroupName,
    //                     kd.District AS DistrictLocation
    //                 FROM
    //                     ktv_cpg_batch_trainings_farmers btf
    //                     LEFT JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID = btf.CpgBatchTrainingID
    //                     LEFT JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = bt.CPGtrainingsID
    //                     LEFT JOIN ktv_cpg cpg ON bt.CPGid = cpg.CPGid
    //                     LEFT JOIN ktv_village kv ON kv.VillageID = cpg.VillageID
    //                     LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
    //                     LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
    //                     LEFT JOIN ktv_members km ON km.MemberID = btf.FarmerID
    //                 WHERE
    //                     km.MemberID =? 
    //                     AND btf.StatusCode = 'active' 
    //                     UNION
    //                 SELECT
    //                     tp.CpgKaderTrainingsFarmerID TrainingID,
    //                     t.CpgTrainings training,
    //                     SUBSTR( kt.TrainingStart, 1, 10 ) TrainingStart,
    //                     SUBSTR( kt.TrainingEnd, 1, 10 ) TrainingEnd,
    //                     TrainingDays,
    //                     t.CpgAbbre,
    //                     cpg.GroupName,
    //                     kd.District AS DistrictLocation
    //                 FROM
    //                     ktv_kader_trainings_participants tp
    //                     LEFT JOIN ktv_kader_trainings kt ON kt.CpgKaderTrainingID = tp.CpgKaderTrainingID
    //                     LEFT JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = kt.CPGtrainingsID
    //                     LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = tp.FarmerID
    //                     LEFT JOIN ktv_cpg cpg ON cpg.CPGid = kcf.CPGid
    //                     LEFT JOIN ktv_village kv ON kv.VillageID = cpg.VillageID
    //                     LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
    //                     LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
    //                     LEFT JOIN ktv_members km ON km.MemberID = tp.FarmerID
    //                 WHERE
    //                     km.MemberID =? 
    //                     AND kt.StatusCode = 'active'
    //                 ORDER BY TrainingStart, TrainingEnd";
    //         $g = $this->db->query($sql, array($id, $id));
    //         if ($g->num_rows() > 0) {
    //             $i = 0;
    //             $data_convert = convert_language($g->result(), 'in');
    //             foreach ($data_convert as $row) {
    //                 $ret[$i] = array(
    //                     'type' => "training",
    //                     'id' => $row->TrainingID,
    //                     'attributes' => array(
    //                         "name" => $row->training,
    //                         "start" => $row->TrainingStart,
    //                         "end" => $row->TrainingEnd,
    //                         "days" => $row->TrainingDays,
    //                         "trainingType" => $row->CpgAbbre,
    //                         "GroupName" => $row->GroupName,
    //                         "DistrictLocation" => $row->DistrictLocation
    //                     )
    //                 );
    //                 $i++;
    //             }
    //             $return['data'] = $ret;
    //         } else {
    //             $return['errors'] = array(
    //                 'status' => "001",
    //                 'title' => "request failed",
    //                 'detail' => "no training found"
    //             );
    //         }
    //     } else {
    //         $return['errors'] = array(
    //             'status' => "001",
    //             'title' => "request failed",
    //             'detail' => "no farmer found"
    //         );
    //     }

    //     return $return;
    // }

    function getFarmerTraining($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            $sql = "
            SELECT
                tf.FarmerTrainingID as TrainingID,
                tt.CpgTrainings as TrainingTopic,
                tt.CpgAbbre as CpgAbbre,
                DATE_FORMAT( tf.TrainingStart, '%W, %d %M %Y' ) AS TrainingStart,
                DATE_FORMAT( tf.TrainingEnd, '%W, %d %M %Y' ) AS TrainingEnd,
                tf.TrainingDays as TrainingDays,
                CASE
                    tf.TrainingStatus 
                    WHEN '1' THEN
                    'Completed' 
                    WHEN '2' THEN
                    'Family member'
                    WHEN '3' THEN
                    'Canceled'
                    ELSE 
                    ''
                END as training_status,
                '' as certificate_program_name,
                '' as certificate_holder_org_name,
                kp.PersonNm as GroupName,
                rd.District as DistrictLocation
            FROM
                ktv_farmer_trainings tf
            LEFT JOIN
                ktv_persons kp ON kp.PersonID = tf.FacProgramPersonID
            LEFT JOIN
                ktv_cpg_trainings tt ON tt.CpgTrainingsID = tf.CPGtrainingsID
            LEFT JOIN
                ktv_farmer_trainings_participants tfp ON tfp.FarmerTrainingID = tf.FarmerTrainingID
            LEFT JOIN 
                ktv_district rd ON tf.TrainingDistrict = rd.DistrictID
            WHERE
                tfp.FarmerID = ?
            ";
            $g = $this->db->query($sql, array($id));
            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), $language);
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "training",
                        'id' => $row->TrainingID,
                        'attributes' => array(
                            "name" => $row->TrainingTopic,
                            "start" => $row->TrainingStart,
                            "end" => $row->TrainingEnd,
                            "days" => $row->TrainingDays,
                            "trainingType" => $row->CpgAbbre,
                            "GroupName" => $row->GroupName,
                            "DistrictLocation" => $row->DistrictLocation
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no training found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }

        return $return;
    }

    function getFarmerTrader($data) {

        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $id = $this->getFarmerID($farmerid, $partnerid);

        $sql = "SELECT
    vso.SupplychainID
    , vso.ObjType OrgType
    , vso.Name TraderName
    , vso.ObjID OrgID
    , me.agCompanyName Company
    , m.MemberName UserName
    , m.Latitude
    , m.Longitude
    , m.Address
    , m.Phone
    , v.Village
    , sd.SubDistrict
    , d.District
    , p.Province
FROM
    ktv_tc_supplychain_farmer sf /*Table Relasi*/
    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sf.SupplychainID
    LEFT JOIN ktv_members m ON m.MemberID=vso.ObjID AND vso.ObjType='agent' /*Table Agent atau SME*/
    LEFT JOIN ktv_members_extension me ON me.MemberID=m.MemberID
    LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
    LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
WHERE
    sf.StatusCode = 'active'
    AND sf.FarmerID = ? /*FarmerID yg login FC*/
    AND vso.SupplychainID IS NOT NULL
GROUP BY sf.SupplychainID
";

        /* Commented for development purpose only
        $sql .= " WHERE
        vso.VillageID IN(
            SELECT VillageID FROM ktv_members mm WHERE MemberID = 45861
        )";
        */

//            $sql .= " HAVING UserName IS NOT NULL";

        $trader = $this->db->query($sql,[$id]);

        if ($trader->num_rows() > 0) {
            $res = $trader->result_array();
            $i = 0;
            $data_convert = convert_language($trader->result(), 'in');
            foreach ($data_convert as $row) {
                $ret[$i] = array(
                    'type' => "partner",
                    'id' => $row->OrgID,
                    'attributes' => array(
                        "SupplychainID" => $row->SupplychainID,
                        "TraderID" => $row->OrgID,
                        "UserName" => $row->UserName,
                        "Trader" => $row->TraderName,
                        "Gender" => $row->Gender,
                        "Education" => $row->Education,
                        "Company Name" => $row->Company,
                        "Address" => $row->Address,
                        "Phone" => $row->Phone,
                        "Status" => "Certified",
                        "Company Established" => $row->CompanyYear,
                        "District" => $row->District,
                        "SubDistrict" => $row->SubDistrict,
                        "Village" => $row->Village,
                        "Latitude" => $row->Latitude,
                        "Longitude" => $row->Longitude
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no trader found"
            );
        }
        /*} else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }*/

        return $return;
    }

    function getFarmerManual($data) {
        $appname = @$data['attributes']['appname'];
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $traderid = @$data['attributes']['traderid'];
        $supplychainid = @$data['attributes']['supplychainid'];
        $languagecode = @$data['attributes']['languagecode'];//@$data['attributes']['languagecode'] == '' ? 'en' : @$data['attributes']['languagecode'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        if(isset($languagecode)){
            if($languagecode == 'id'){
                $language = 'indonesia';
            } else if($languagecode == 'es'){
                $language = 'spanish';
            } else if($languagecode == 'fr'){
                $language = 'french';
            } else {
                $language = 'english';
            }
        }

        if(!isset($appname)){
            if(isset($supplychainid)){
                $appname = 'farmgate';
            } else {
                $appname = 'farmcloud';
            }
        }

        $type = @$data['type'];

        if($appname == 'farmcloud'){
            $id = $this->getFarmerID($farmerid, $partnerid);
        } else {
            if(isset($supplychainid)){
                $id = $traderid;
            } else {
                $id = 1; //temporary no using id
            }
        }

        if(!isset($partnerid)){
            /*if appname == 'farmextension'
            $partnerid */
            $partnerId = 37;
        }else{
            $partnerId= $partnerid;

        }

        $urlAWS = $this->config->item('CTCDN')."/";
        if ($id != '') {
//            die();
            /*$sql = "
                SELECT
                    ManualBookID id, DATE_FORMAT(DateCreated, '%M %d, %Y') date, Image, Title, Content
                FROM
                    ktv_manual_book
                WHERE
                    StatusCode='active' AND (PartnerID=0 OR PartnerID=?)
                ORDER BY
                    DateCreated DESC
            ";*/
            if($appname == 'farmcloud'){
                $sql = "
                    SELECT 
                        DocID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        'https://s3-ap-southeast-1.amazonaws.com/farmcloud/documents/Apps-Pdf-icon.png' AS Image,
                        DocUrl AS Content,
                        StatusPublish
                    FROM
                        cms_document a
                        LEFT JOIN cms_access b 
                            ON a.DocID = b.ObjID 
                            AND b.ObjType = 'document' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                                AND b.RoleAccessFarmer = 1
                        ) 
                        OR a.StatusType = 'public'
					    AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active' 
                    ORDER BY
                        a.DateCreated DESC
                ";
            } else if($appname == 'farmgate'){
                $sql = "
                    SELECT 
                        DocID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        'https://s3-ap-southeast-1.amazonaws.com/farmcloud/documents/Apps-Pdf-icon.png' AS Image,
                        DocUrl AS Content,
                        StatusPublish 
                    FROM
                        cms_document a
                        LEFT JOIN cms_access b 
                            ON a.DocID = b.ObjID 
                            AND b.ObjType = 'document' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                                AND b.RoleAccessTrader = 1
                        ) 
                        OR a.StatusType = 'public'
					    AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active' 
                    ORDER BY
                        a.DateCreated DESC
                ";
            } else if($appname == 'farmxtension'){
                $sql = "
                    SELECT 
                        DocID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        'https://s3-ap-southeast-1.amazonaws.com/farmcloud/documents/Apps-Pdf-icon.png' AS Image,
                        DocUrl AS Content,
                        StatusPublish 
                    FROM
                        cms_document a
                        LEFT JOIN cms_access b 
                            ON a.DocID = b.ObjID 
                            AND b.ObjType = 'document' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                                AND b.RoleAccessStaff = 1
                        ) 
                        OR a.StatusType = 'public'
					    AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active' 
                    ORDER BY
                        a.DateCreated DESC
                ";
            } else {
                $sql = "
                    SELECT 
                        DocID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        'https://s3-ap-southeast-1.amazonaws.com/farmcloud/documents/Apps-Pdf-icon.png' AS Image,
                        DocUrl AS Content,
                        StatusPublish 
                    FROM
                        cms_document a
                        LEFT JOIN cms_access b 
                            ON a.DocID = b.ObjID 
                            AND b.ObjType = 'document' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                                AND b.RoleAccessRetailer = 1
                        ) 
                        OR a.StatusType = 'public'
					    AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active' 
                    ORDER BY
                        a.DateCreated DESC
                ";
            }

            $manual = $this->db->query($sql, array($partnerId));
            foreach($manual->result() as $key => $res){
                $manual->result()[$key]->Title = $this->getContentLanguageValue('Document', $res->id, 'Name', ucwords($language));
                $manual->result()[$key]->Content = (!empty($res->Content)) ? $this->config->item('CTCDN') .'/'. $res->Content : '';
            }

            //$manual = $this->db->query($sql, array($partnerid));

            if ($manual->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($manual->result(), $language);
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "manual",
                        'id' => $row->id,
                        'attributes' => array(
                            "date" => $row->date,
                            "image" => $row->Image,
                            "title" => $row->Title,
                            "content" => $row->Content,
                            "status" => $row->StatusPublish
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no document found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;

    }

    private function getFarmerDistrictID($farmerID){
        $sql = "
            SELECT 
            ktv_district.DistrictID
            FROM
                ktv_members
            INNER JOIN ktv_village ON ktv_village.VillageID = ktv_members.VillageID
            INNER JOIN ktv_subdistrict ON ktv_village.SubDistrictID = ktv_subdistrict.SubDistrictID
            INNER JOIN ktv_district ON ktv_subdistrict.DistrictID = ktv_district.DistrictID
            WHERE
                MemberID = ?
        ";
        $query = $this->db->query($sql, array($farmerID));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['DistrictID'];
        } else {
            return false;
        }
    }

    /* GET KiosK */
    public function getKiosk($data){
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        $id = $this->getFarmerID($farmerid, $partnerid);
        if ($id) {
            $farmerDistrictID = $this->getFarmerDistrictID($id);
            $sql = "SELECT SQL_CALC_FOUND_ROWS
            x.agCompanyName AS Company,
            a.MemberID AS TraderID,
            a.Address AS Address,
            a.`MemberDisplayID` AS id,
            a.`MemberName` AS OwnerName,
            rrole.MRoleName AS Category,
            sub_mill.MillName,
            sub_mill.MillName2,
            a.Latitude,
            a.Longitude,
            a.Alias,
            kv.`Village` AS VillageName,
            ksd.`SubDistrict` AS SubDistrictName,
            kd.`District` AS DistrictName,
            kp.`Province` AS ProvinceName,
            a.DateUpdated AS LastUpdated,
            ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` ) AS Enumerator,
            kp.Province,
            kd.District,
            a.`DateOfBirth` AS Birthdate,
            FLOOR( DATEDIFF( CURDATE(), a.DateOfBirth ) / 365.25 ) AS Age,
            DATE_FORMAT( a.`DateCollection`, '%Y-%m-%d' ) AS DateCollection,
            a.HandPhone AS Handphone,
            CASE
            
            WHEN a.MaritalStatus = '1' THEN
            'Married'
            WHEN a.MaritalStatus = '2' THEN
            'Single'
            WHEN a.MaritalStatus = '3' THEN
            'Widow/widower'
            END AS MaritalStatus,
            GROUP_CONCAT( rrole.MRoleName SEPARATOR ', ' ) AS MemberRole,
            GROUP_CONCAT( DISTINCT rrole.MRoleName SEPARATOR ', ' ) AS StatusSME,
            GROUP_CONCAT( mtype.SMETypeID SEPARATOR ',' ) AS MemberTypeID,
            GROUP_CONCAT( DISTINCT a.Latitude, ',', a.Longitude ) AS GPS,
            sub_farmer.NrFarmer AS NrFarmer,
            GROUP_CONCAT( sub_mill.MillName SEPARATOR ', ' ) AS MillName -- , IF(GROUP_CONCAT(sub_mill.MillName SEPARATOR ', ') <> '','Vendor','Agent') StatusSME
            
            FROM
            ktv_members a
            INNER JOIN (
            SELECT
            sub_a.MemberID
            FROM
            ktv_members sub_a
            LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
            WHERE
            sub_a.StatusCode = 'active'
            AND sub_b.MRoleID IN ( 5, 6, 7, 8, 9, 10, 11, 12, 13, 14 )
            GROUP BY
            sub_a.MemberID
            ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
            LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
            LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
            LEFT JOIN ktv_member_sme_type mtype ON a.MemberID = mtype.MemberID
            LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            LEFT JOIN ktv_members_extension x ON a.MemberID = x.MemberID
            LEFT JOIN (
            SELECT
            sub_a.`MemberID` AS MemberID,
            COUNT( DISTINCT ktsf.`FarmerID` ) AS NrFarmer
            FROM
            ktv_members sub_a
            LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`ObjID` = sub_a.`MemberID`
            AND ktso.ObjType = 'agent'
            LEFT JOIN ktv_tc_supplychain_farmer ktsf ON ktsf.`SupplychainID` = ktso.`SupplychainID`
            LEFT JOIN ktv_members sub_farmer ON sub_farmer.MemberID = ktsf.FarmerID
            LEFT JOIN ktv_member_role kmr ON sub_a.MemberID = kmr.MemberID
            LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID` = kmr.`MRoleID`
            WHERE
            sub_a.StatusCode = 'active'
            AND sub_farmer.StatusCode = 'active'
            AND rm.`MRoleType` = 'Agent'
            GROUP BY
            sub_a.`MemberID`
            ) sub_farmer ON sub_farmer.MemberID = a.`MemberID`
            LEFT JOIN (
            SELECT
            kmember.`MemberID` AS MemberID,
            group_CONCAT( DISTINCT km.`MillName` ) AS MillName,
            GROUP_CONCAT(
            DISTINCT REPLACE ( km.MillName, ' ', '' )) MillName2
            FROM
            ktv_members kmember
            LEFT JOIN ktv_tc_supplychain_org ktso2 ON ktso2.`ObjID` = kmember.`MemberID`
            AND ktso2.`ObjType` = 'agent'
            LEFT JOIN ktv_tc_supplychain_org_rel ktsor ON ktsor.`ChildID` = ktso2.`SupplychainID`
            LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`SupplychainID` = ktsor.`ParentID`
            LEFT JOIN ktv_mill km ON km.`MillID` = ktso.`ObjID`
            AND ktso.`ObjType` = 'mill'
            LEFT JOIN ktv_member_role kmr ON kmember.MemberID = kmr.MemberID
            LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID` = kmr.`MRoleID`
            WHERE
            kmember.`StatusCode` = 'active'
            AND ktsor.`StatusCode` = 'active'
            AND rm.`MRoleType` = 'Agent'
            AND ktso.PartnerID = '1'
            GROUP BY
            kmember.`MemberID`
            ) sub_mill ON sub_mill.MemberID = a.`MemberID`
            INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID
            AND acc_pm.apmPartnerID = '1'
            WHERE
            a.`StatusCode` = 'active'
            AND kd.DistrictID IN (?)
            GROUP BY
            a.MemberID
            ORDER BY
            OwnerName ASC";

            $g = $this->db->query($sql, $farmerDistrictID);

            if ($g->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($g->result(), 'in');
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "KiosK",
                        'id' => $row->TraderID,
                        'attributes' => array(
                            'TraderID' => $row->TraderID,
                            "OwnerName" => $row->OwnerName,
                            "Company" => $row->Company,
                            "Category" => $row->Category,
                            "Handphone" => $row->Handphone,
                            "Latitude" => $row->Latitude,
                            "Longitude" => $row->Longitude,
                            "Address" => $row->Address,
                            "VillageName" => $row->VillageName,
                            "SubDistrictName" => $row->SubDistrictName,
                            "DistrictName" => $row->DistrictName,
                            "ProvinceName" => $row->ProvinceName
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no kios found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    /* Registrasi Cognito */
    // Cek Farmer
    public function checkFarmer($phone=''){
        $SQL = "SELECT
            MemberID
        FROM
            `ktv_members` kcf 
        WHERE
            kcf.HandPhone = ?
            AND NOT EXISTS ( SELECT FarmerID FROM sys_farmer_user WHERE FarmerID = kcf.MemberID ) 
        GROUP BY
            kcf.MemberID";

        /* kcf.FarmerID = ? */

        $Query = $this->db->query($SQL, array($phone));

        if ($Query->num_rows()) {
            if($Query->num_rows() > 1){
                $return = array(
                    'successs' => false,
                    'message' => "Phone Number ".$phone." duplicate, please contact administrator !",
                    'data' => $Query->row()
                );
            }else{
                $return = array(
                    'successs' => true,
                    'message' => "Farmer Found !",
                    'data' => $Query->row()
                );
            }
        } else {
            $return = array(
                'successs' => false,
                'message' => "FarmerID with Phone Number ".$phone." Not Found or Registered !",
            );
        }

        return $return;
    }

    // Registrasi ke wab dari cognito
    public function regUserCognito($farmerid='', $email='', $username='', $fcmid='' , $phone=''){
        $Quser = $this->db->get_where('sys_farmer_user', ['FarmerID'=>$farmerid]);

        $return = array(
            'successs' => false,
            'message' => "Error, saving data error !",
        );

        //cek siapa tau sudah registered
        if($Quser->num_rows()){
            $return = array(
                'successs' => false,
                'message' => "Error, FarmerID ".$farmerid." Registered already !",
            );
        }else{
            $data['FarmerID'] = $farmerid;
            $data['Username'] = $username;
            $data['Email'] = $email;
            $data['FCMID'] = $fcmid;
            $data['SecondaryHandphone'] = $phone;
            $data['StatusUser'] = 'Active';

            if($this->db->insert('sys_farmer_user', $data)){
                $return = array(
                    'successs' => true,
                    'message' => "Success, Register FarmerID ".$farmerid." successfully !",
                );
            }
        }

        return $return;
    }

    // Update FCMID setiap login
    public function updateFCM($farmerid, $fcmid){
        $data['FCMID'] = $fcmid;

        $return = array(
            'successs' => false,
            'message' => "Error, saving data error !",
        );

        $this->db->where('FarmerID', $farmerid);
        if($this->db->update('sys_farmer_user', $data)){
            $return = array(
                'successs' => true,
                'message' => "Success, update FCM ID for FarmerID ".$farmerid." successfully !",
            );
        }

        return $return;
    }

    function getFarmerIDbyPhone($phone) {
        $sql = "SELECT f.MemberID id
                FROM ktv_members f 
                WHERE f.HandPhone = ?";
        $query = $this->db->query($sql, array($phone));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['id'];
        } else {
            return false;
        }
    }

    function getSupplychainHistoryByFarmer($farmerid = '') {
        $supplychains = [];

        if(strlen($farmerid) > 0) {

            $sql = "SELECT DISTINCT 
                st.SupplychainID,
                sb.SupplyOrgID,
                vso.NAME Collector 
            FROM
                ktv_tc_supplychain_transaction st
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( sb.SupplyOrgID, st.SupplychainID )
                LEFT JOIN ktv_members m ON m.MemberID = st.SupplyID 
            WHERE
                SupplyType != 'Batch' 
            AND sb.SupplyOrgID IS NOT NULL 
            AND vso.Name IS NOT NULL 
            AND m.MemberID = ?";

            $exec = $this->db->query($sql,[$farmerid]);
            if($exec->num_rows() > 0) {
                $supplychains = $exec->result_array();
            }
        }

        return $supplychains;
    }

    function getNews($data) {
        //https://demo.palmoiltrace.com/api/index.php/farmer-apps/news
        $appname = @$data['attributes']['appname'];
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $traderid = @$data['attributes']['traderid'];
        $limit = @$data['attributes']['limit'];
        $offset = @$data['attributes']['offset'];

        $supplychainid = @$data['attributes']['supplychainid'];
        $languagecode = @$data['attributes']['languagecode'];//@$data['attributes']['languagecode'] == '' ? 'en' : @$data['attributes']['languagecode'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        @$appname = strtolower($appname);

        if(isset($languagecode)){
            if($languagecode == 'id'){
                $language = 'indonesia';
            } else if($languagecode == 'es'){
                $language = 'spanish';
            } else if($languagecode == 'fr'){
                $language = 'french';
            } else {
                $language = 'english';
            }
        }

        if(!isset($appname)){
            if(isset($supplychainid)){
                $appname = 'farmgate';
            } else {
                $appname = 'farmcloud';
            }
        }

        $type = @$data['type'];

        if($appname == 'farmcloud'){
            $id = $this->getFarmerID($farmerid, $partnerid);
        } else {
            if(isset($supplychainid)){
                $id = $traderid;
            } else {
                $id = 1; //temporary no using id
            }
        }

        if(!isset($partnerid)){
            /*if appname == 'farmextension'
            $partnerid */
            $partnerId = 37;
        }else{
            $partnerId= $partnerid;

        }
        $urlAWS = $this->config->item('CTCDN')."/";
        if ($id != '') {
            if($appname == 'farmcloud'){
                if ($limit !== null && $offset !== null) {
                    $sql = "
                        SELECT 
                            NewsID,
                            DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                            IF(a.PhotoFile IS NOT NULL,CONCAT('$urlAWS', a.PhotoFile),'') AS ImagePath,
                            tag,
                            StatusPublish
                        FROM
                            cms_news a 
                            LEFT JOIN cms_access b 
                                ON a.NewsID = b.ObjID 
                                AND b.ObjType = 'news' 
                        WHERE (
                                (
                                    FIND_IN_SET(?, b.PartnerIDImplode) 
                                    AND b.RoleAccessFarmer = 1
                                ) 
                                OR a.StatusType = 'public'
                                AND a.StatusPublish = 'publish'
                            ) 
                        AND 
                            a.StatusCode = 'active'
                        LIMIT ? OFFSET ?
                    ";
                }
                else{
                    $sql = "
                        SELECT 
                            NewsID,
                            DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                            IF(a.PhotoFile IS NOT NULL,CONCAT('$urlAWS', a.PhotoFile),'') AS ImagePath,
                            tag,
                            StatusPublish
                        FROM
                            cms_news a 
                            LEFT JOIN cms_access b 
                                ON a.NewsID = b.ObjID 
                                AND b.ObjType = 'news' 
                        WHERE (
                                (
                                    FIND_IN_SET(?, b.PartnerIDImplode) 
                                    AND b.RoleAccessFarmer = 1
                                ) 
                                OR a.StatusType = 'public'
                                AND a.StatusPublish = 'publish'
                            ) 
                        AND 
                            a.StatusCode = 'active'
                    ";
                }
               
            } else if($appname == 'farmgate') {
                $sql = "
                    SELECT 
                        NewsID,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                        IF(a.PhotoFile IS NOT NULL,CONCAT('$urlAWS', a.PhotoFile),'') AS ImagePath,
                        tag,
                        StatusPublish
                    FROM
                        cms_news a 
                        LEFT JOIN cms_access b 
                            ON a.NewsID = b.ObjID 
                            AND b.ObjType = 'news' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                            AND b.RoleAccessTrader = 1
                        ) 
                        OR a.StatusType = 'public'
                        AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active'
                ";
            } else if($appname == 'farmxtension') {
                $sql = "
                    SELECT 
                        NewsID,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                        IF(a.PhotoFile IS NOT NULL,CONCAT('$urlAWS', a.PhotoFile),'') AS ImagePath,
                        tag,
                        StatusPublish
                    FROM
                        cms_news a 
                        LEFT JOIN cms_access b 
                            ON a.NewsID = b.ObjID 
                            AND b.ObjType = 'news' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                            AND b.RoleAccessStaff = 1
                        ) 
                        OR a.StatusType = 'public'
                        AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active'
                ";
            } else {
                $sql = "
                    SELECT 
                        NewsID,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                        IF(a.PhotoFile IS NOT NULL,CONCAT('$urlAWS', a.PhotoFile),'') AS ImagePath,
                        tag,
                        StatusPublish
                    FROM
                        cms_news a 
                        LEFT JOIN cms_access b 
                            ON a.NewsID = b.ObjID 
                            AND b.ObjType = 'news' 
                    WHERE (
                        (
                            FIND_IN_SET(?, b.PartnerIDImplode) 
                            AND b.RoleAccessRetailer = 1
                        ) 
                        OR a.StatusType = 'public'
                        AND a.StatusPublish = 'publish'
                    ) 
                    AND a.StatusCode = 'active'
                ";
            }

            if ($limit !== null && $offset !== null) {
                $news = $this->db->query($sql, array($partnerid, $limit, $offset));
            }
            else{
                $news = $this->db->query($sql, array($partnerid));
            }

            foreach($news->result() as $key => $res){
                $newsTitle = $this->getContentLanguageValue('News', $res->NewsID, 'Title', ucwords($language));
                $news->result()[$key]->Title = $newsTitle;

                if($appname != 'farmcloud') {
                    $news->result()[$key]->Summary = $this->getContentLanguageValue('News', $res->NewsID, 'Summary', ucwords($language));
                    $newsContent = $this->getContentLanguageValue('News', $res->NewsID, 'Content', ucwords($language));
                    if(isset($supplychainid)){
                        //$row->Content = $row->Content;
                        $news->result()[$key]->Content = '<h1>'.$newsTitle.'<h1><h5>'.$res->PublishDate.'</h5>'.$newsContent;
                    } else {
                        $news->result()[$key]->Content = $newsContent;
                    }
                }
                else{
                    $news->result()[$key]->Content = "";
                }
            }

            if ($news->num_rows() > 0) {
                $i = 0;
                $data_convert = nonconvert_language($news->result(), $language);//$news->result();
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "news",
                        'id' => $row->NewsID,
                        'attributes' => array(
                            "date" => $row->PublishDate,
                            "image" => $row->ImagePath,
                            "title" => $row->Title,
                            "content" => $row->Content,
                            "tag" => $row->tag,
                            "status" => $row->StatusPublish,
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no news found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    function getNewsDetail($data) {
        $newsId = @$data['attributes']['NewsID'];
        $languagecode = @$data['attributes']['languagecode'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        if(isset($languagecode)){
            if($languagecode == 'id'){
                $language = 'indonesia';
            } else if($languagecode == 'es'){
                $language = 'spanish';
            } else if($languagecode == 'fr'){
                $language = 'french';
            } else {
                $language = 'english';
            }
        }

        if(!isset($appname) || $appname == ""){
            if(isset($supplychainid)){
                $appname = 'farmgate';
            } else {
                $appname = 'farmcloud';
            }
        }
        
        $type = @$data['type'];
       
        $urlAWS = $this->config->item('CTCDN')."/";
        
        $sql = "
            SELECT 
                NewsID,
                DATE_FORMAT(DateCreated, '%M %d, %Y') PublishDate,
                IF(a.PhotoFile IS NOT NULL,CONCAT('$urlAWS', a.PhotoFile),'') AS ImagePath,
                tag,
                StatusPublish
            FROM
                cms_news a 
                LEFT JOIN cms_access b 
                    ON a.NewsID = b.ObjID 
                    AND b.ObjType = 'news' 
            WHERE a.NewsID = ?
            AND 
                a.StatusCode = 'active'
            LIMIT 1
        ";

        $news = $this->db->query($sql, array($newsId));

        foreach($news->result() as $key => $res){
          
            $newsTitle = $this->getContentLanguageValue('News', $res->NewsID, 'Title', ucwords($language));
            $news->result()[$key]->Title = $newsTitle;

            $news->result()[$key]->Summary = $this->getContentLanguageValue('News', $res->NewsID, 'Summary', ucwords($language));
            $newsContent = $this->getContentLanguageValue('News', $res->NewsID, 'Content', ucwords($language));
            if(isset($supplychainid)){
                //$row->Content = $row->Content;
                $news->result()[$key]->Content = '<h1>'.$newsTitle.'<h1><h5>'.$res->PublishDate.'</h5>'.$newsContent;
            } else {
                $news->result()[$key]->Content = $newsContent;
            }
        }

        if ($news->num_rows() > 0) {
            $i = 0;
            $data_convert = nonconvert_language($news->result(), $language);//$news->result();
            foreach ($data_convert as $row) {
                $ret[$i] = array(
                    'type' => "news",
                    'id' => $row->NewsID,
                    'attributes' => array(
                        "date" => $row->PublishDate,
                        "image" => $row->ImagePath,
                        "title" => $row->Title,
                        "content" => $row->Content,
                        "tag" => $row->tag,
                        "status" => $row->StatusPublish
                    )
                );
                $i++;
            }
            $return['data'] = $ret;
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no news found"
            );
        }

        return $return;
    }

    function getVideo($data) {
        $appname = @$data['attributes']['appname'];
        $farmerid = @$data['attributes']['farmerid'];
        $supplychainid = @$data['attributes']['supplychainid'];
        $traderid = @$data['attributes']['traderid'];
        $partnerid = @$data['attributes']['partnerid'];
        $languagecode = @$data['attributes']['languagecode'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];

        $limit = @$data['attributes']['limit'];
        $offset = @$data['attributes']['offset'];

        if(isset($languagecode)){
            if($languagecode == 'id'){
                $language = 'indonesia';
            } else if($languagecode == 'es'){
                $language = 'spanish';
            } else if($languagecode == 'fr'){
                $language = 'french';
            } else {
                $language = 'english';
            }
        }

        if(!isset($appname)){
            if(isset($supplychainid)){
                $appname = 'farmgate';
            } else {
                $appname = 'farmcloud';
            }
        }

        if($appname == 'farmcloud'){
            $id = $this->getFarmerID($farmerid, $partnerid);
        } else {
            if(isset($supplychainid)){
                $id = $traderid;
            } else {
                $id = 1; //temporary no using id
            }
        }

        if(!isset($partnerid)){
            /*if appname == 'farmextension'
            $partnerid */
            $partnerId = 37;
        }else{
            $partnerId= $partnerid;

        }

        //$id = $this->getFarmerID($farmerid, $partnerid);
        if ($id != '') {
            if($appname == 'farmcloud'){
                if ($limit !== null && $offset !== null) {
                    $sql = "
                            SELECT 
                                VidID AS id,
                                DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                                PicThumb AS image,
                                VideoUrl AS content,
                                StatusPublish 
                            FROM
                                cms_video a 
                                LEFT JOIN cms_access b 
                                    ON a.VidID = b.ObjID 
                                    AND b.ObjType = 'video' 
                            WHERE (
                                    (
                                        FIND_IN_SET(?, b.PartnerIDImplode) 
                                        AND b.RoleAccessFarmer = 1
                                    ) 
                                    OR a.StatusType = 'public'
                                    AND a.StatusPublish = 'publish'
                            ) 
                            AND a.StatusCode = 'active' 
                            LIMIT ? OFFSET ?
                        ";
                    }
                else{
                    $sql = "
                        SELECT 
                            VidID AS id,
                            DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                            PicThumb AS image,
                            VideoUrl AS content,
                            StatusPublish 
                    FROM
                            cms_video a 
                            LEFT JOIN cms_access b 
                                ON a.VidID = b.ObjID 
                                AND b.ObjType = 'video' 
                    WHERE (
                                (
                                    FIND_IN_SET(?, b.PartnerIDImplode) 
                                    AND b.RoleAccessFarmer = 1
                                ) 
                                OR a.StatusType = 'public'
                                AND a.StatusPublish = 'publish'
                        ) 
                        AND a.StatusCode = 'active' 
                    ";
                }
              
            } else if($appname == 'farmgate') {
                $sql = "
                    SELECT 
                        VidID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        PicThumb AS image,
                        VideoUrl AS content,
                        StatusPublish 
                   FROM
                        cms_video a 
                        LEFT JOIN cms_access b 
                             ON a.VidID = b.ObjID 
                             AND b.ObjType = 'video' 
                   WHERE (
                            (
                                 FIND_IN_SET(?, b.PartnerIDImplode) 
                                 AND b.RoleAccessTrader = 1
                            ) 
                            OR a.StatusType = 'public'
                            AND a.StatusPublish = 'publish'
                       ) 
                    AND a.StatusCode = 'active' 
                ";
            } else if($appname == 'farmxtension') {
                $sql = "
                    SELECT 
                        VidID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        PicThumb AS image,
                        VideoUrl AS content,
                        StatusPublish 
                   FROM
                        cms_video a 
                        LEFT JOIN cms_access b 
                             ON a.VidID = b.ObjID 
                             AND b.ObjType = 'video' 
                   WHERE (
                            (
                                 FIND_IN_SET(?, b.PartnerIDImplode) 
                                 AND b.RoleAccessStaff = 1
                            ) 
                            OR a.StatusType = 'public'
                            AND a.StatusPublish = 'publish'
                       ) 
                    AND a.StatusCode = 'active' 
                ";
            } else {
                $sql = "
                    SELECT 
                        VidID AS id,
                        DATE_FORMAT(DateCreated, '%M %d, %Y') `date`,
                        PicThumb AS image,
                        VideoUrl AS content,
                        StatusPublish 
                   FROM
                        cms_video a 
                        LEFT JOIN cms_access b 
                             ON a.VidID = b.ObjID 
                             AND b.ObjType = 'video' 
                   WHERE (
                            (
                                 FIND_IN_SET(?, b.PartnerIDImplode) 
                                 AND b.RoleAccessRetailer = 1
                            ) 
                            OR a.StatusType = 'public'
                            AND a.StatusPublish = 'publish'
                       ) 
                    AND a.StatusCode = 'active' 
                ";
            }
            if ($limit !== null && $offset !== null) {
                $video = $this->db->query($sql, array($partnerid, $limit, $offset));
            }
            else{
                $video = $this->db->query($sql, array($partnerid));
            }
            foreach($video->result() as $key => $res){
                $video->result()[$key]->Title = $this->getContentLanguageValue('Video', $res->id, 'Title', ucwords($language));
                $video->result()[$key]->Summary = $this->getContentLanguageValue('Video', $res->id, 'Summary', ucwords($language));
                $video->result()[$key]->Description = $this->getContentLanguageValue('Video', $res->id, 'Description', ucwords($language));
            }
            if ($video->num_rows() > 0) {
                $i = 0;
                $data_convert = convert_language($video->result(), $language);
                foreach ($data_convert as $row) {
                    $ret[$i] = array(
                        'type' => "video",
                        'id' => $row->id,
                        'attributes' => array(
                            "date" => $row->date,
                            "image" => $row->image,
                            "title" => $row->Title,
                            "content" => $row->content,
                            "summary" => $row->Summary,
                            "description" => $row->Description
                        )
                    );
                    $i++;
                }
                $return['data'] = $ret;
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "no video found"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    //New for cms with multilang
    function getContentLanguageValue($objectType, $objectID, $type, $lang){
        $this->db->select('Value');
        $this->db->where('ObjectType', $objectType);
        $this->db->where('ObjectID', $objectID);
        $this->db->where('Type', $type);
        $this->db->where('Language', $lang);
        $query = $this->db->get('cms_content');
        if($query->num_rows() == 0){
            return '';
        }
        return $query->row()->Value;
    }

    function getFarmerProfilebyFarmerId($data) {

        $memberid = @$data['attributes']['farmerid'];
        $language = @$data['attributes']['language'] == '' ? 'english' : @$data['attributes']['language'];
        $queryParams = [];

        $partnerId = $this->getPartnerIDByFarmerID($memberid);

        $urlAWS = $this->config->item('CTCDN')."/";

        if ($partnerId != '' && strlen($memberid) > 0) {
            //getter farmer profile
            $sql = "SELECT
                f.MemberID id,
                f.MemberID AS `farmerid`,
                f.MemberName nama,
                TIMESTAMPDIFF ( YEAR,
                f.DateOfBirth,
                CURDATE()) AS Age,
                DATE_FORMAT(f.DateOfBirth, '%d %M %Y') as Birthdate,
                f.HandPhone handphone,
                CASE f.Gender WHEN 'm' THEN 'Male'
                WHEN 'f' THEN 'Female'
                ELSE '' END gender,
                c.GroupName farmerGroup,
                'None' farmerCooperative,
                p.Province province,
                d.District district,
                sd.SubDistrict subDistrict,
                v.Village village,
                f.Address address,
                IF ( f.MaritalStatus = 1,
                'Married',
                IF ( f.MaritalStatus = 2,
                'Single',
                IF ( f.MaritalStatus = 3,
                'Widower',
                NULL ) ) ) familyStatus,
                NULL zipCode,
                f.Nin NIK,
                f.BankBeneficiary bankAccountHolder,
                b.BankName bankName,
                f.BankBranchName bankBranch,
                f.BankAccNumber bankAccountNumber,
                '' bankAcountHolderRelation,
                (
                    SELECT 
                        GROUP_CONCAT(DISTINCT vso.Name SEPARATOR '\n') Collector
                    FROM
                        ktv_tc_supplychain_transaction st
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( sb.SupplyOrgID, st.SupplychainID )
                    WHERE
                        SupplyType != 'Batch' 
                    AND vso.Name IS NOT NULL 
                    AND vso.ObjType = 'agent' 
                    AND st.SupplyID = f.MemberID
                ) collector,
                (
                    SELECT 
                        GROUP_CONCAT(DISTINCT vso.Name SEPARATOR '\n') Collector
                    FROM
                        ktv_tc_supplychain_transaction st
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL( sb.SupplyOrgID, st.SupplychainID )
                    WHERE
                        SupplyType != 'Batch' 
                    AND vso.Name IS NOT NULL 
                    AND vso.ObjType = 'mill' 
                    AND st.SupplyID = f.MemberID
                ) mill,
                IF ( f.Photo != '',
                CONCAT ( '".base_url()."/images/member/',
                p.ProvinceID,
                '/',
                f.Photo ),
                '' ) picture,
                f.PartnerID,
                IF(f.SupplybaseType = 'farmer','SmallHolder',IF(f.SupplybaseType = 'direct','DirectSmallHolder','-')) farmerType
            FROM
                ktv_members f
            LEFT JOIN ktv_cpg c ON
                c.CPGid = f.FarmerGroupID
            LEFT JOIN ktv_village v ON
                v.VillageID = f.VillageID
            LEFT JOIN ktv_subdistrict sd ON
                sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d ON
                d.DistrictID = sd.DistrictID
            LEFT JOIN ktv_province p ON
                p.ProvinceID = d.ProvinceID
            LEFT JOIN ktv_bank b ON
                b.BankID = f.BankID
            WHERE
                f.MemberDisplayID = ?";

            array_push($queryParams,$memberid);

            $sql .= ' LIMIT 1';

            $user = $this->db->query($sql, $queryParams);

            if($user->num_rows() > 0) { //farmer found
                $user = $user->result();
                $id = $user[0]->id;

                $ret[0] = array(
                    'type' => "user",
                    'id' => $id,
                    'attributes' => convert_language($user[0], 'in') // $user[0]
                );

                //getter surveys
                $sql = "SELECT 
                    IF (
                            b.SurveyNr = a.SurveyNr
                            , 'baseline'
                            , 'postline'
                    ) AS `name`
                    , SUM(a.GardenAreaHa) AS `farmSize`
                    , (SUM(a.AverageProdLowSeason)) AS `averageYield`
                    , (SUM(a.AnnualProduction)) AS `production`
                    , COUNT(a.PlotNr) AS `noOfGarden`
                FROM
                    ktv_survey_plot a 
                    JOIN 
                            (SELECT 
                                a.MemberID
                                , a.PlotNr
                                , a.SurveyNr 
                            FROM
                                ktv_survey_plot a 
                                JOIN 
                                    (SELECT 
                                        a.MemberID
                                        , a.PlotNr
                                        , MAX(a.SurveyNr) AS SurveyNr 
                                    FROM
                                        ktv_survey_plot a 
                                    GROUP BY a.MemberID
                                        , a.PlotNr) b 
                                    ON a.MemberID = b.MemberID 
                                    AND a.SurveyNr = b.SurveyNr 
                                    AND a.PlotNr = b.PlotNr) b 
                            ON a.MemberID = b.MemberID 
                            AND a.PlotNr = b.PlotNr 
                            AND a.SurveyNr = b.SurveyNr 
                    JOIN ktv_members km ON a.MemberID = km.MemberID
                WHERE km.MemberDisplayID = ?
                GROUP BY a.MemberID";

                $survey = $this->db->query($sql, array($memberid));
                $i = 1;

                if ($survey->num_rows() > 0) {

                    $data_convert = convert_language($survey->result(), 'in');

                    foreach ($data_convert as $row) {
                        $row->farmSize['value'] = $row->farmSize['value'] . ' Ha';
                        $row->averageYield['value'] = $row->averageYield['value']. ' Ton/Ha';
                        $row->production['value'] = $row->production['value'] . ' Ton';
                        $ret[$i] = array(
                            'type' => "survey",
                            'id' => $i + 1,
                            'attributes' => array(
                                'name' => $row->name,
                                'farmSize' => $row->farmSize, // . ' Ha',
                                'averageYield' => $row->averageYield, // . ' Kg/Ha',
                                'production' => $row->production, // . ' Kg',
                                'noOfGarden' => $row->noOfGarden
                            )
                        );
                        $i++;
                    }
                }

                $return['data'] = $ret;
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }

        return $return;
    }

    function getPartnerIDByFarmerID($farmerid) {
        $sql = "SELECT f.PartnerID id
            FROM ktv_members f 
            WHERE f.MemberID=?";
        $query = $this->db->query($sql, array($farmerid));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['id'];
        } else {
            return false;
        }
    }

    function cekForPhoneRegistered($data){

        $handphone = @$data['attributes']['handphone'] ?? '';

        if(strpos($handphone, ' ')){
            return ['success' => false, 'status' => 2, 'message' => 'invalid phone number format'];
        }

        if(strpos($handphone, '+') === false ){
            return ['success' => false, 'status' => 2, 'message' => 'invalid phone number format'];
        }

        if(strlen($handphone) > 0) {
            $sql = 'SELECT StatusUser FROM sys_farmer_user WHERE SecondaryHandphone = ?';
            $Q = $this->db->query($sql,[$handphone]);
            if($Q->num_rows() > 0) {
                $row = $Q->row();
                if($row->StatusUser == 'Active') { return ['success' => true, 'status' => 1, 'message' => 'phone number already registered']; }
            }
        }

        return ['success' => false, 'status' => 0, 'message' => 'phone number not registered on cognito'];
    }

    private function _getTypeByNotifID($notifID){
        $sql = "
            SELECT
                OrgType
            FROM 
                ktv_mobile_notification
            WHERE
                NotifID = ?
        ";
        $query = $this->db->query($sql, array($notifID));
        return $query->row()->OrgType;
    }

    private function _checkNotificatonStatusByFarmerID($notifID, $farmerID){
        $sql = "
            SELECT
                *
            FROM 
                ktv_mobile_notification_status
            WHERE
                NotifID = ?
            AND
                FarmerID = ?
        ";
        $query = $this->db->query($sql, array($notifID, $farmerID));
        if($query->num_rows() > 0){
            return TRUE;
        }
        return FALSE;
    }

    function updateFarmerNotification($data) {
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $notifid = @$data['attributes']['notifid'];

        $id = $this->getFarmerID($farmerid, $partnerid);

        if ($id != '') {
            if($this->_getTypeByNotifID($notifid) != 'broadcast'){
                $sql = "
                    UPDATE  
                        ktv_mobile_notification
                    SET
                        StatusRead = 1
                    WHERE
                        NotifID = ?
                ";
                $query = $this->db->query($sql, $notifid);
            } else {
                if(!$this->_checkNotificatonStatusByFarmerID($notifid, $id)){
                    $sql = "
                        INSERT INTO  
                            ktv_mobile_notification_status
                        SET
                            NotifID = ?,
                            FarmerID = ?,
                            StatusRead = 1
                    ";
                    $query = $this->db->query($sql, array($notifid, $id));
                } else {
                    $sql = "
                        UPDATE  
                            ktv_mobile_notification_status
                        SET
                            StatusRead = 1
                        WHERE
                            NotifID = ?
                        AND
                            FarmerID = ?
                    ";
                    $query = $this->db->query($sql, array($notifid, $id));
                }
            }

            /* $sql = "
                UPDATE  
                    ktv_mobile_notification
                SET
                    StatusRead = 1
                WHERE
                    NotifID = ?
            ";
            $query = $this->db->query($sql, $notifid); */

            if ($this->db->affected_rows() > 0) {
                $return['data'] = array(
                    'type' => "analytics",
                    'id' => $notifid,
                    'attributes' => array(
                        'farmerid' => $id,
                        'notifid' => $notifid,
                        'message' => 'Update notification successful'
                    )
                );
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "Update notification failed"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    function checkPhoneNumberRegistration($phoneNumber, $username) {
        $CI = & get_instance();
        $CI->config->load('awscognito');
        $config = $CI->config->item('awscog');
        $userPoolId = $config["user_pool_id"];

        $client = new CognitoIdentityProviderClient([
            'region' => $config["region"],
            'version' => $config["version"],
            'credentials' => [
                'key' => $config["credentials"]["key"],
                'secret' =>  $config["credentials"]["secret"],
            ]
        ]);

        try {
            $user_exist_in_cognito = FALSE;
            $phone_number_with_requested_user_exist = FALSE;
            $phone_number_duplicate = FALSE;
            $phone_number_duplicate_counter = 0;

            $username_check_res = $client->listUsers([
                'UserPoolId' => $userPoolId,
                'Filter' => "username = \"".$username."\""
            ]);
            foreach ($username_check_res as $row) {
                if (count($row) > 0) {
                    $user_exist_in_cognito = TRUE;
                }
                break;
            }

            $result = $client->listUsers([
                'UserPoolId' => $userPoolId,
                'Filter' => "phone_number = \"".$phoneNumber."\""
            ]);
            $result = (array) $result;

            foreach ($result as $row) {
                if (count($row["Users"]) > 1) {
                    $phone_number_duplicate = true;
                    $phone_number_duplicate_counter = count($row["Users"]);
                }
                foreach ($row["Users"] as $user) {
                    $temp_username = $user["Username"];
                    $temp_phone_num = NULL;
                    foreach ($user["Attributes"] as $field) {

                        if ($field["Name"] == "phone_number") {
                            $temp_phone_num = $field["Value"];
                        }
                    }
                    if ($temp_username == $username && $temp_phone_num == $phoneNumber) {

                        $phone_number_with_requested_user_exist = true;
                    }
                }
                break;
            }
            $return['data'][0] = [
                'type' => "user",
                'id' => 1,
                'attributes' => [
                    "user_exist_in_cognito" => $user_exist_in_cognito,
                    "phone_number_with_requested_user_exist" => $phone_number_with_requested_user_exist,
                    "phone_number_duplicate" => $phone_number_duplicate,
                    "phone_number_duplicate_counter" => $phone_number_duplicate_counter,
                ]
            ];
            return $return;
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage() . "\n";
            error_log($e->getMessage());
        }
    }

    public function validateOtpByFarmerPhone($data) {
        date_default_timezone_set('Asia/Jakarta');
        $phone = @$data['attributes']['handphone'];
        $otp = @$data['attributes']['otp'];

        $sql = "SELECT
                    * 
                FROM
                    `ktv_fc_otp` 
                WHERE
                    Phone = ?
                ORDER BY
                    OtpID DESC
                    LIMIT 1";
        $query = $this->db->query($sql, array($phone));
        $result = $query->row();

        // $expiredTime = date("Y-m-d H:i:s",strtotime($result->CreatedDate." +15 minutes"));

        if($result){
            //check expired otp
            // if($expiredTime < date("Y-m-d H:i:s") ){
            //     $return['data'] = array(
            //         'type' => "otp expired",
            //         'id' => 1,
            //         'attributes' => array(
            //             'message' => 'otp expired, on: '.$expiredTime
            //         )
            //     );
            // }
            // else{

            // }

            if ($result->OtpNumber == $otp && $result->Status == 0) {
                $updateStatus = $this->_updateStatusOtp($result);
                $return['data'] = array(
                    'type' => "verification",
                    'id' => 1,
                    'attributes' => array(
                        'farmerid' => $result->FarmerID,
                        'otpid' => $result->OtpID,
                        'message' => 'verification successful'
                    )
                );
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "verification failed"
                );
            }

        }
        else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "verification failed"
            );
        }

        return $return;
    }

    private function _updateStatusOtp($data){
        $OtpID = $data->OtpID;
        $sql = "
            UPDATE  
                ktv_fc_otp
            SET
                Status = 1
            WHERE
                OtpID = ?
        ";
        $query = $this->db->query($sql, $OtpID);

        if ($this->db->affected_rows() > 0) {
            $return['data'] = array(
                'type' => "verification",
                'id' => 1,
                'attributes' => array(
                    'farmerid' => $data->FarmerID,
                    'otpid' => $data->OtpID,
                    'message' => 'verified'
                )
            );
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "verification failed"
            );
        }
        return $return;
    }

    function convertPhoneNumberToGeneral($phone){
        $phone = str_replace(' ', '', $phone);
        if (substr($phone, 0, 3) == "+62") {
            $phone = substr_replace($phone,"08",0,4);
        }
        return $phone;
    }

    function convertPhoneNumberToCountryCode($phone){
        $phone = str_replace(' ', '', $phone);
        if (substr($phone, 0, 2) == "08") {
            $phone = substr_replace($phone,"+628",0,2);
        }
        return $phone;
    }

    function getFarmerIDbyPhoneRegister($phone) {
        $sql = "SELECT f.MemberID id
                FROM ktv_members f 
                WHERE f.Handphone = ? OR f.Handphone = ? OR REPLACE(f.Handphone, ' ', '') = ?";
        $phone_general = $this->convertPhoneNumberToGeneral($phone);
        $phone_with_country = $this->convertPhoneNumberToCountryCode($phone);
        $query = $this->db->query($sql, array($phone_general, $phone_with_country, $phone_with_country));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['id'];
        } else {
            return false;
        }
    }

    function getUsernameByPhoneRegister($phone) {
        $sql = "SELECT f.MemberDisplayID username
                FROM ktv_members f 
                WHERE f.Handphone = ? OR f.Handphone = ? OR REPLACE(f.Handphone, ' ', '') = ?";
        $phone_general = $this->convertPhoneNumberToGeneral($phone);
        $phone_with_country = $this->convertPhoneNumberToCountryCode($phone);
        $query = $this->db->query($sql, array($phone_general, $phone_with_country, $phone_with_country));
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['username'];
        } else {
            return false;
        }
    }

    public function saveLogOTP($data){
        date_default_timezone_set('Asia/Jakarta');

        $farmerid = @$data['attributes']['farmerid'];
        $phone = @$data['attributes']['handphone'];
        $opt = $data['opt'];
        // $id = $farmerid;//$this->getFarmerID($farmerid, $partnerid);

        // case forgot password
        if($farmerid == ""){
            $farmerid =  $this->getFarmerIDbyPhoneRegister($phone);

            if($farmerid == ""){
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "phone number is not registered with any user"
                );

                return $return;
            }
        }
        // end case forgot password

        $data = array('FarmerID' => $farmerid,
            "Phone" => $phone,
            "OtpNumber" => $opt,
            "Status" => 0,
            "CreatedDate" => date('Y-m-d H:i:s')
        );

        if($this->db->insert('ktv_fc_otp', $data)){
            $return['data'] = array(
                'type' => "verification",
                'id' => 1,
                'attributes' => array(
                    'farmerid' => $farmerid,
                    'otpid' => $opt,
                    'message' => 'Otp Send Successful'
                )
            );
        }else{
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "Otp Send failed"
            );
        }

        return $return;
    }

    private function _cognitoSecretHash($key, $username, $client_id) {
        $hash = hash_hmac('sha256', $username.$client_id, $key, true);

        return base64_encode($hash);
    }

    function cognitoResetPassword($username) {
        $CI = & get_instance();
        $CI->config->load('awscognito');
        $config = $CI->config->item('awscog');
        $userPoolId = $config["user_pool_id"];

        $secretHash = $this->_cognitoSecretHash($config["app_client_secret"], $username, $config["app_client_id"]);

        $client = new CognitoIdentityProviderClient([
            'region' => $config["region"],
            'version' => $config["version"],
            'credentials' => [
                'key' => $config["credentials"]["key"],
                'secret' =>  $config["credentials"]["secret"],
            ]
        ]);

        try {

            $reset_password = $client->ForgotPassword([
                'ClientId' => $config["app_client_id"],
                'Username' => $username,
                'SecretHash' => $secretHash
            ]);

            $return['data'][0] = [
                'type' => "reset password",
                'id' => 1,
                'attributes' => [
                    'farmerid' => $username,
                    'message' => 'Request Reset Password Successful, Please Check Your Email'
                ],
                'CodeDeliveryDetails' => $reset_password['CodeDeliveryDetails']
            ];
            return $return;
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage() . "\n";
            error_log($e->getMessage());
        }
    }

    function cognitoConfirmResetPassword($username, $new_password, $code_verification) {
        $CI = & get_instance();
        $CI->config->load('awscognito');
        $config = $CI->config->item('awscog');
        $userPoolId = $config["user_pool_id"];

        $secretHash = $this->_cognitoSecretHash($config["app_client_secret"], $username, $config["app_client_id"]);

        $client = new CognitoIdentityProviderClient([
            'region' => $config["region"],
            'version' => $config["version"],
            'credentials' => [
                'key' => $config["credentials"]["key"],
                'secret' =>  $config["credentials"]["secret"],
            ]
        ]);

        try {

            $confirm_reset_password = $client->ConfirmForgotPassword([
                'ClientId' => $config["app_client_id"],
                'ConfirmationCode' => $code_verification,
                'Password' => $new_password,
                'Username' => $username,
                'SecretHash' => $secretHash
            ]);

            $return['data'][0] = [
                'type' => "forgot username",
                'id' => 1,
                'attributes' => [
                    'farmerid' => $username,
                    'message' => 'Reset Password Successful'
                ]
            ];
            return $return;
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage() . "\n";
            error_log($e->getMessage());
        }
    }

    public function forgotUsername($handphone){
        $sql = "
            SELECT
                FarmerID
            FROM 
                sys_farmer_user
            WHERE
                SecondaryHandphone = ?
        ";
        $query = $this->db->query($sql, array($handphone));
        if($query->num_rows() > 0){
            $data  = $query->result_array();
            $return['data'][0] = [
                'type' => "forgot username",
                'id' => 1,
                'attributes' => [
                    'farmerid' => $data[0]['FarmerID'],
                    'message' => 'FarmerID was found'
                ]
            ];
            return $return;
        }else{
            $return['data'][0] = [
                'type' => "forgot username",
                'id' => 1,
                'attributes' => [
                    'message' => 'Username/FarmerID not found'
                ]
            ];
            return $return;
        }
    }

    public function cognitoAdminSetUserPassword($phone, $new_password){
        $CI = & get_instance();
        $CI->config->load('awscognito');
        $config = $CI->config->item('awscog');
        $userPoolId = $config["user_pool_id"];

        $username =  $this->getUsernameByPhoneRegister($phone);

        if($username != ""){
            $secretHash = $this->_cognitoSecretHash($config["app_client_secret"], $username, $config["app_client_id"]);

            $client = new CognitoIdentityProviderClient([
                'region' => $config["region"],
                'version' => $config["version"],
                'credentials' => [
                    'key' => $config["credentials"]["key"],
                    'secret' =>  $config["credentials"]["secret"],
                ]
            ]);

            try {

                $reset_password = $client->AdminSetUserPassword([
                    'Password' => $new_password,
                    'Permanent' => true,
                    'Username' => $username,
                    'UserPoolId' => $config["user_pool_id"]
                ]);

                $return['data'][0] = [
                    'type' => "reset password",
                    'id' => 1,
                    'attributes' => [
                        'farmerid' => $username,
                        'message' => 'Reset Password Successful'
                    ]
                ];
            } catch (AwsException $e) {
                // output error message if fails
                $return['data'][0] = [
                    'type' => "reset password",
                    'id' => 1,
                    'attributes' => [
                        'farmerid' => $username,
                        'message' => 'Reset Password Failed'
                    ],
                    'aws' => [
                        'error_code' => $e->getAwsErrorCode(),
                        'error_message' => $e->getAwsErrorMessage()
                    ]
                ];
                error_log($e->getMessage());
            }
        }else{
            $return['data'][0] = [
                'type' => "reset password",
                'id' => 1,
                'attributes' => [
                    'message' => 'Username/FarmerID not found'
                ]
            ];
        }

        return $return;
    }

    public function changePhoneCognito($phone, $new_phone, $farmerId, $country_code){
        $CI = & get_instance();
        $CI->config->load('awscognito');
        $config = $CI->config->item('awscog');
        $userPoolId = $config["user_pool_id"];

        $new_phone_full = $country_code.$new_phone;

        // $username = $this->getUsernameByPhoneRegister($phone);
        // $farmerID = $this->getFarmerIDbyPhoneRegister($phone);

        if($farmerId != ""){
            $secretHash = $this->_cognitoSecretHash($config["app_client_secret"], $farmerId, $config["app_client_id"]);

            $client = new CognitoIdentityProviderClient([
                'region' => $config["region"],
                'version' => $config["version"],
                'credentials' => [
                    'key' => $config["credentials"]["key"],
                    'secret' =>  $config["credentials"]["secret"],
                ]
            ]);

            try {
                $reset_password = $client->AdminUpdateUserAttributes([
                    'UserAttributes' => [
                        [
                            'Name' => 'phone_number',
                            'Value' => $new_phone_full
                        ]
                    ],
                    'Username' => $farmerId,
                    'UserPoolId' => $config["user_pool_id"]
                ]);

                $return['data'] = [
                    'type' => "change phone number",
                    'id' => 1,
                    'attributes' => [
                        'farmerid' => $farmerId,
                        'message' => 'Change Phone Number Successful'
                    ]
                ];

                $this->_updatePhoneNumber($farmerId, $country_code, $new_phone);
            } catch (AwsException $e) {
                // output error message if fails
                $return['data'] = [
                    'type' => "change phone number",
                    'id' => 1,
                    'attributes' => [
                        'farmerid' => $farmerID,
                        'message' => 'Change Phone Number Failed'
                    ],
                    'aws' => [
                        'error_code' => $e->getAwsErrorCode(),
                        'error_message' => $e->getAwsErrorMessage()
                    ]
                ];
                error_log($e->getMessage());
            }
        }else{
            $return['data'] = [
                'type' => "change phone number",
                'id' => 1,
                'attributes' => [
                    'message' => 'Phone Number Is Not Registered'
                ]
            ];
        }

        return $return;
    }

    private function _updatePhoneNumber($farmerid, $country_code, $new_handphone){
        try{
            $sql = "
                UPDATE  
                    ktv_members sys
                SET
                    Handphone = ?, HandphoneType = 1
                WHERE
                    MemberDisplayID = ?
                ";
            $query = $this->db->query($sql, array($country_code.$new_handphone, $farmerid));

            if ($this->db->affected_rows() > 0) {
                $return['data'] = array(
                    'type' => "update handphone",
                    'id' => 1,
                    'attributes' => array(
                        'farmerid' => $farmerid,
                        'message' => 'Handphone Has Been Changed Successfully'
                    )
                );
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "update failed"
                );
            }

            return $return;
        }catch(Exeption $e){
            echo "message: " .$e->getMessage();
        }
    }

    public function changePhonefromcentral($new_phone, $farmerId){
        if($farmerId != ""){
            $return= $this->_updatePhoneNumberCentral($farmerId, $new_phone);
        }
        else{
            $return['data'] = [
                'type' => "change phone number",
                'id' => 1,
                'attributes' => [
                    'message' => 'Phone Number Is Not Registered'
                ]
            ];
        }
        return $return;
    }

    private function _updatePhoneNumberCentral($farmerid, $new_handphone){
        date_default_timezone_set('Asia/Jakarta');

        try{
            $sql = "
                UPDATE  
                    sys_farmer_user sys
                SET
                    SecondaryHandphone = ?
                WHERE
                    FarmerID = ?
                ";
            $query = $this->db->query($sql, array($new_handphone, $farmerid));

            $sql2 = "
                UPDATE  
                    ktv_members f
                SET
                    Handphone = ?
                WHERE
                    MemberDisplayID = ?
            ";
            $query2 = $this->db->query($sql2, array($new_handphone, $farmerid));

            if ($this->db->affected_rows() > 0) {
                $return = array(
                    'statusCode' => "00",
                    'status' => "success"
                );

            } else {
                $return = array(
                    'statusCode' => "001",
                    'status' => "failed"
                );
            }

            return $return;
        }catch(Exeption $e){
            echo "message: " .$e->getMessage();
        }
    }
    function createAnalyticsData($data) {
        $username = @$data['attributes']['UserName'];
        $email = @$data['attributes']['Email'];
        $farmerid = @$data['attributes']['farmerid'];
        $partnerid = @$data['attributes']['partnerid'];
        $sessionid = @$data['attributes']['SessionID'];
        $screenname = @$data['attributes']['ScreenName'];
        $additionalinfo = @$data['attributes']['AdditionalInfo'];
        $appname = @$data['attributes']['AppName'];
        $activitydate = @$data['attributes']['ActivityDate'];

        /** New data for analytics */
        $androidVersion = @$data['attributes']['AndroidVersion'];
        $appVersion = @$data['attributes']['AppVersion'];
        $totalMemory = @$data['attributes']['TotalMemory'];
        $totalStorage = @$data['attributes']['TotalStorage'];
        $mobileBrand = @$data['attributes']['MobileBrand'];
        $firebaseInstallID = @$data['attributes']['FirebaseInstallID'];
        $pseudoUniqueID = @$data['attributes']['PseudoUniqueID'];

        if($appname == 'FarmCloud'){
            $id = $this->getFarmerID($farmerid, $partnerid);
        } else {
            $id = $farmerid;
        }


        if ($id != '') {
            //Check data by Session ID
            $sql = "
                SELECT 
                    COUNT(*) AS Total 
                FROM 
                    ktv_analytics
                WHERE
                    UserName = ?
                AND
                    ScreenName = ?
                AND
                    AppName = ?
                AND
                    SessionID = ?
            ";
            $checkQuery = $this->db->query($sql, array(
                $username,
                $screenname,
                $appname,
                $sessionid
            ));

            $check = $checkQuery->row_array();

            if($check['Total'] == 0){
                $sql = "
                    INSERT INTO 
                        ktv_analytics (
                            UserName,
                            Email,
                            FarmerID,
                            SessionID,
                            ScreenName,
                            AdditionalInfo,
                            AppName,
                            AndroidVersion,
                            AppVersion,
                            TotalMemory,
                            TotalStorage,
                            MobileBrand,
                            FirebaseInstallID,
                            PseudoUniqueID,
                            ActivityDate,
                            DateCreated
                        ) 
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $query = $this->db->query($sql, array($username, $email, $farmerid, $sessionid, $screenname, $additionalinfo, $appname, $androidVersion, $appVersion, $totalMemory, $totalStorage, $mobileBrand, $firebaseInstallID, $pseudoUniqueID, $activitydate ));

                if ($this->db->affected_rows() > 0) {
                    $return['data'] = array(
                        'type' => "analytics",
                        'id' => $this->db->insert_id(),
                        'attributes' => array(
                            'farmerid' => $farmerid,
                            'analyticsid' => $this->db->insert_id(),
                            'message' => 'add analytics successful'
                        )
                    );
                } else {
                    $return['errors'] = array(
                        'status' => "001",
                        'title' => "request failed",
                        'detail' => "confirmation failed"
                    );
                }
            } else {
                $return['errors'] = array(
                    'status' => "001",
                    'title' => "request failed",
                    'detail' => "analytics with this screenname on this session is registered"
                );
            }
        } else {
            $return['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "no farmer found"
            );
        }
        return $return;
    }

    public function getStaffID($req)
    {
        
        $return['data'] = [
            'type' => "get staffid",
            'id' => 1,
            'attributes' => [
                'message' => 'staffID Not Found',
                'fullName' => ""
            ]
        ];

        return $return;
    }
}
