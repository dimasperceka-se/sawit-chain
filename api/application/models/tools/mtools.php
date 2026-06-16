<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jan 10 2019
 *  File : mtools.php
 *******************************************/

class Mtools extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    public function GetDataCoaching($Username, $IMSID)
    {
        $sql = "SELECT
                    DISTINCT a.`FarmerID`
                    , a.`IMSID`
                FROM
                    `ktv_ims_farmer_coaching` a
                WHERE
                    a.`UserName` = ?
                    AND a.`IMSID` = ?";
        return $this->db->query($sql, array($Username, $IMSID))->result_array();
    }

    public function CoachingDownloadV2($DataCoaching, $Username)
    {
        $ArrJson = array();
        $ArrFarmerMain = array();
        $ArrJson['username'] = $Username;

        for ($i = 0; $i < count($DataCoaching); $i++) {
            $ArrFarmerMain[$i]['farmer_id'] = $DataCoaching[$i]['FarmerID'];
            $ArrFarmerMain[$i]['ims_id'] = $DataCoaching[$i]['IMSID'];
            $ArrActivity = array();

            //Lanjut sini, query pakai FarmerID dan IMSID ditabel ktv_ims_farmer_coaching_activity
            $sql = "SELECT
                        a.`ActivityID`,
                        a.`CoachingID`,
                        a.`IMSID`,
                        a.`FarmerID`,
                        a.`CoachingRecipient`,
                        a.`FarmerWorkerName`,
                        DATE_FORMAT(`EventDate`,'%Y/%m/%d') AS EventDate,
                        a.`TimeStart`,
                        a.`TimeEnd`,
                        a.`FarmerInPlace`,
                        a.`Reason`,
                        a.`Comment`,                        
                        ST_Latitude(a.LatLong) AS Latitude, 
                        ST_Longitude(a.LatLong) AS Longitude,
                        a.`Accuracy`,
                        a.`Sample_pH1`,
                        a.`Sample_pH2`,
                        a.`Sample_pH3`,
                        a.`PhotoActPath`
                    FROM
                        `ktv_ims_farmer_coaching_activity` a
                    WHERE
                        a.`FarmerID` = ?
                        AND a.`IMSID` = ?
                        AND a.`StatusCode` = 'active'
                    ORDER BY a.`CoachingID`";
            $p = array(
                $DataCoaching[$i]['FarmerID'], $DataCoaching[$i]['IMSID']
            );
            $DataAct = $this->db->query($sql, $p)->result_array();
            for ($j = 0; $j < count($DataAct); $j++) {
                $ArrNcs = array();
                $ArrActivity[$j]['activity_uid'] = $DataAct[$j]['ActivityID'];
                $ArrActivity[$j]['coaching_recipient'] = $DataAct[$j]['CoachingRecipient'];
                $ArrActivity[$j]['farmer_worker_name'] = $DataAct[$j]['FarmerWorkerName'];
                $ArrActivity[$j]['date'] = $DataAct[$j]['EventDate'];
                $ArrActivity[$j]['time_start'] = $DataAct[$j]['TimeStart'];
                $ArrActivity[$j]['time_stop'] = $DataAct[$j]['TimeEnd'];
                $ArrActivity[$j]['farmer_in_place'] = $DataAct[$j]['FarmerInPlace'];
                $ArrActivity[$j]['reason'] = $DataAct[$j]['Reason'];
                $ArrActivity[$j]['comment'] = $DataAct[$j]['Comment'];
                $ArrActivity[$j]['longitude'] = $DataAct[$j]['Longitude'];
                $ArrActivity[$j]['latitude'] = $DataAct[$j]['Latitude'];
                $ArrActivity[$j]['accuracy'] = $DataAct[$j]['Accuracy'];
                $ArrActivity[$j]['sample_1'] = $DataAct[$j]['Sample_pH1'];
                $ArrActivity[$j]['sample_2'] = $DataAct[$j]['Sample_pH2'];
                $ArrActivity[$j]['sample_3'] = $DataAct[$j]['Sample_pH3'];
                $ArrActivity[$j]['photo_path'] = base_url() . '/' . $DataAct[$j]['PhotoActPath'];

                $sql = "SELECT
                            a.`ActivityNCID`,
                            a.`ActivityID`,
                            a.`RefID`,
                            a.`Answer`,
                            a.`Topic`,
                            CASE 
                                WHEN a.`UrgentlyStatus` = 1 THEN 'Low'
                                WHEN a.`UrgentlyStatus` = 2 THEN 'Medium'
                                WHEN a.`UrgentlyStatus` = 3 THEN 'High'
                                ELSE ''
                            END AS UrgentlyStatus,
                            a.`Finding`,
                            a.`ActivityType`,
                            a.`Recommendation`,
                            a.`Target`,
                            a.`FollowupStatus`,
                            DATE_FORMAT(a.`Deadline`,'%Y/%m/%d') AS Deadline,
                            CASE 
                                WHEN a.`ActNCStatus` = 1 THEN 'Canceled'
                                WHEN a.`ActNCStatus` = 2 THEN 'Not Started'
                                WHEN a.`ActNCStatus` = 3 THEN 'In Progress'
                                WHEN a.`ActNCStatus` = 4 THEN 'Completed'
                                ELSE ''
                            END AS ActNCStatus,
                            a.`Explanation`
                        FROM
                            `ktv_ims_farmer_coaching_activity_nc` a
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`ActivityID` = ?";
                $DataNcs = $this->db->query($sql, array($DataAct[$j]['ActivityID']))->result_array();
                for ($k = 0; $k < count($DataNcs); $k++) {
                    $ArrNcs[$k]['activity_nc_uid'] = $DataNcs[$k]['ActivityNCID'];
                    $ArrNcs[$k]['ref_id'] = $DataNcs[$k]['RefID'];
                    $ArrNcs[$k]['answer'] = $DataNcs[$k]['Answer'];
                    $ArrNcs[$k]['topic'] = $DataNcs[$k]['Topic'];
                    $ArrNcs[$k]['urgentlyStatus'] = $DataNcs[$k]['UrgentlyStatus'];
                    $ArrNcs[$k]['finding'] = $DataNcs[$k]['Finding'];
                    $ArrNcs[$k]['activity_type'] = $DataNcs[$k]['ActivityType'];
                    $ArrNcs[$k]['recommendation'] = $DataNcs[$k]['Recommendation'];
                    $ArrNcs[$k]['target'] = $DataNcs[$k]['Target'];
                    $ArrNcs[$k]['followup_status'] = $DataNcs[$k]['FollowupStatus'];
                    $ArrNcs[$k]['deadline'] = $DataNcs[$k]['Deadline'];
                    $ArrNcs[$k]['status'] = $DataNcs[$k]['ActNCStatus'];
                    $ArrNcs[$k]['explanation'] = $DataNcs[$k]['Explanation'];
                }

                $ArrActivity[$j]['task'] = $ArrNcs;
            }

            $ArrFarmerMain[$i]['activity'] = $ArrActivity;
        }

        $ArrJson['farmer'] = $ArrFarmerMain;
        return $ArrJson;
    }

    public function FaFarmerMapping($Username)
    {
        //Ambil dulu IMSID nya yang paling baru
        $sql = "SELECT
                    DISTINCT a.`IMSID`
                    , ims.`SurveyNr`
                    , ch.`CertProgID` AS Certification
                FROM
                    ktv_fa_farmer_mapping a
                    INNER JOIN ktv_ims ims ON a.`IMSID` = ims.`IMSID`
                    LEFT JOIN ktv_certification_holders ch ON ims.`CertHolderID` = ch.`CertHolderID`
                WHERE
                    a.`UserName` = ?
                ORDER BY a.`IMSID` DESC
                LIMIT 1";
        $DataIms = $this->db->query($sql, array($Username))->row_array();

        $sql = "SELECT
                    a.`FarmerID`
                    , f.MemberName FarmerName
                    , CASE
                        WHEN f.Gender='1' THEN '" . lang('Male') . "'
                        WHEN f.Gender='2' THEN '" . lang('Female') . "'
                        ELSE '-'
                    END AS Gender
                    , FLOOR(DATEDIFF(CURDATE(), f.DateOfBirth) / 365.25) AS Age
                    , prov.`ProvinceID`
                    , prov.`Province`
                    , dis.`DistrictID`
                    , dis.`District`
                    , subd.`SubDistrictID`
                    , subd.`SubDistrict`
                    , vil.`VillageID`
                    , vil.`Village`
                    , cpg.FarmerGroupID CPGid
                    , cpg.GroupName GroupName
                FROM
                    `ktv_fa_farmer_mapping` a
                    LEFT JOIN ktv_members f ON a.`FarmerID` = f.`MemberID`
                    LEFT JOIN ktv_village vil ON f.`VillageID` = vil.VillageID
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                    LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                    LEFT JOIN ktv_farmer_group cpg on cpg.FarmerGroupID = f.FarmerGroupID
                WHERE
                    a.`UserName` = ?
                    AND a.IMSID = ?
                ORDER BY a.`DateGenerated`";
        $DataFarmer = $this->db->query($sql, array($Username, $DataIms['IMSID']))->result_array();

        $DataReturn['IMSID'] = $DataIms['IMSID'];
        $DataReturn['SurveyNr'] = $DataIms['SurveyNr'];
        $DataReturn['Certification'] = $DataIms['Certification'];
        $DataReturn['DataFarmer'] = $DataFarmer;
        return $DataReturn;
    }

    public function InitFarmerNcV2($DataFarmer, $IMSID, $SurveyNr, $Certification)
    {
        $DataReturn = array();
        $this->load->helpers('common');

        for ($i = 0; $i < count($DataFarmer); $i++) {
            $FarmerID = $DataFarmer[$i]['FarmerID'];
            $DataReturn[$i]['FarmerID'] = $FarmerID;
            $DataReturn[$i]['FarmerName'] = $DataFarmer[$i]['FarmerName'];
            $DataReturn[$i]['Gender'] = $DataFarmer[$i]['Gender'];
            $DataReturn[$i]['Age'] = $DataFarmer[$i]['Age'];
            $DataReturn[$i]['ProvinceID'] = $DataFarmer[$i]['ProvinceID'];
            $DataReturn[$i]['Province'] = $DataFarmer[$i]['Province'];
            $DataReturn[$i]['DistrictID'] = $DataFarmer[$i]['DistrictID'];
            $DataReturn[$i]['District'] = $DataFarmer[$i]['District'];
            $DataReturn[$i]['SubDistrictID'] = $DataFarmer[$i]['SubDistrictID'];
            $DataReturn[$i]['SubDistrict'] = $DataFarmer[$i]['SubDistrict'];
            $DataReturn[$i]['VillageID'] = $DataFarmer[$i]['VillageID'];
            $DataReturn[$i]['Village'] = $DataFarmer[$i]['Village'];
            $DataReturn[$i]['CPGid'] = $DataFarmer[$i]['CPGid'];
            $DataReturn[$i]['GroupName'] = $DataFarmer[$i]['GroupName'];
            $DataReturn[$i]['IMSID'] = $IMSID;

            //DATA NC ====================================== (Begin)
            // Data tidak diperlukan
            /*

            $sql = "SELECT
                        MAX(a.`DaconID`) AS DaconID
                        , a.`GardenNr`
                    FROM
                        ktv_cocoa_farmer_garden_datacontrol a
                    WHERE
                        a.`FarmerID` = ?
                        AND a.`SurveyNr` = ?
                        AND a.`Certification` = ?
                    GROUP BY a.`FarmerID`, a.`SurveyNr`, a.`GardenNr`";
            $DataNC = $this->db->query($sql,array($DataFarmer[$i]['FarmerID'], $SurveyNr, $Certification))->result_array();
            if(isset($DataNC[0]['DaconID'])) {

                //Dapatkan DaconIDImp == (Begin)
                $ArrTempNc = array();
                foreach ($DataNC as $key => $value) {
                    $ArrTempNc[$key] = $value['DaconID'];
                }
                $DaconIDImp = implode(",",$ArrTempNc);

                //Ambil kombinasi semua nc
                $sql = "SELECT
                            DISTINCT ref.RefID
                            , ref.`RefLabel`
                            , ref.ConKey
                            , ref.StatusControl
                        FROM
                            `ktv_ref_survey_datacontrol` ref
                            INNER JOIN `ktv_cocoa_farmer_garden_datacontrol_item` item ON ref.`RefID` = item.`RefID` AND item.`DaconID` IN ({$DaconIDImp})
                        WHERE
                            ref.`SurveyType` = 'Garden'
                            AND ref.StatusQuality = 'NC'
                            AND ref.StatusCode = 'active'
                        ";
                $ListNC = $this->db->query($sql)->result_array();
                if(isset($ListNC[0]['RefID'])) {
                    for ($j=0; $j < count($ListNC); $j++) {
                        $ListNcDetail = array();
                        $increK = 0;

                        //Get label NC
                        $ListNC[$j]['Label'] = $this->GetNCGardenQueryLabel($ListNC[$j]['ConKey'],'label',null);
                        if($ListNC[$j]['StatusControl'] == 'High') {
                            $ListNC[$j]['Label'] = '(MAJOR) '.$ListNC[$j]['Label'];
                        }

                        $NilaiNc = $this->GetNilaiNcByRefIDGarden($FarmerID,$SurveyNr,$DaconIDImp,$ListNC[$j]['RefID'],$ListNC[$j]['ConKey']);
                        if(isset($NilaiNc[0]['DaconItemID'])) {
                            for ($k=0; $k < count($NilaiNc); $k++) {
                                $ListNcDetail[$increK]['GardenNr'] = $NilaiNc[$k]['GardenNr'];
                                $ListNcDetail[$increK]['Nilainya'] = $NilaiNc[$k]['NilaiNc'];
                                $ListNcDetail[$increK]['DaconItemID'] = $NilaiNc[$k]['DaconItemID'];
                                $increK++;
                            }
                        }

                        $ListNC[$j]['Detail'] = $ListNcDetail;
                    }

                    $DataReturn[$i]['NC'] = $ListNC;
                }
            } else {
                $DataReturn[$i]['NC'] = array();
            }
            */
            //DATA NC ====================================== (End)
        }

        return $DataReturn;
    }

    private function ProsesDistrict($ProvinceID,$District){
        $DistrictID = null;

        //Cek District
        $sql = "SELECT
                    a.`DistrictID`
                FROM
                    ktv_district a
                WHERE
                    a.`District` = ?
                    AND a.`ProvinceID` = ?
                LIMIT 1";
        $Data = $this->db->query($sql,array($District,$ProvinceID))->row_array();
        if(!isset($Data['DistrictID'])){
            //Cari DistrictID nya dan insertkan
            $sql = "INSERT INTO `ktv_district` (
                `DistrictID`,
                `District`,
                `ProvinceID`,
                `active`,
                `StatusCode`
              )
              SELECT
                  (a.`DistrictID`+1),
                  '$District',
                  '$ProvinceID',
                  '1',
                  'active'
              FROM
                  ktv_district a
              WHERE
                  a.`ProvinceID` = '$ProvinceID'
              ORDER BY a.`DistrictID` DESC
              LIMIT 1";
            $query = $this->db->query($sql);
            $DistrictID = $this->db->insert_id();
        }else{
            $DistrictID = $Data['DistrictID'];
        }
        return $DistrictID;
    }

    private function ProsesSubDistrict($DistrictID,$District,$SubDistrict){
        $SubDistrictID = null;

        //Cek SubDistrict
        $sql = "SELECT
                    a.`SubDistrictID`
                FROM
                    ktv_subdistrict a
                WHERE
                    a.`SubDistrict` = ?
                    AND a.`DistrictID` = ?
                LIMIT 1";
        $Data = $this->db->query($sql,array($SubDistrict,$DistrictID))->row_array();
        if(!isset($Data['SubDistrictID'])){
            $sql = "INSERT INTO `ktv_subdistrict` (
                `SubDistrictID`,
                `SubDistrict`,
                `DistrictID`,
                `active`,
                `StatusCode`
              )
              SELECT
                  (a.`SubDistrictID`+1),
                  '$SubDistrict',
                  '$DistrictID',
                  '1',
                  'active'
              FROM
                  ktv_subdistrict a
              WHERE
                  a.`DistrictID` = '$DistrictID'
              ORDER BY a.`SubDistrictID` DESC
              LIMIT 1";
            $query = $this->db->query($sql);
            $SubDistrictID = $this->db->insert_id();
        }else{
            $SubDistrictID = $Data['SubDistrictID'];
        }

        return $SubDistrictID;
    }

    private function ProsesVillage($SubDistrictID,$Village){
        $ArrVillage = explode(",",$Village);
        for ($i=0; $i < count($ArrVillage); $i++) { 
            $VillageName = trim($ArrVillage[$i]);

            //Cek Village
            $sql = "SELECT
                        a.`VillageID`
                    FROM
                        ktv_village a
                    WHERE
                        a.`SubDistrictID` = ?
                        AND a.`Village` = ?
                    LIMIT 1";
            $Data = $this->db->query($sql,array($SubDistrictID,$VillageName))->row_array();
            if(!isset($Data['VillageID'])){
                $sql = "INSERT INTO `ktv_village` (
                            `VillageID`,
                            `Village`,
                            `SubDistrictID`,
                            `StatusCode`,
                            `CreatedBy`,
                            `DateCreated`
                        )
                        SELECT
                            (a.`VillageID`+1),
                            '$VillageName',
                            '$SubDistrictID',
                            'active',
                            '1',
                            NOW()
                        FROM
                            ktv_village a
                        WHERE
                            a.`SubDistrictID` = '$SubDistrictID'
                        ORDER BY a.`VillageID` DESC
                        LIMIT 1";
                $query = $this->db->query($sql);
            }
        }
    }

    private function ProsesBaruSubDistrict($ProvinceID,$District,$SubDistrict){
        //Ambil DistrictID
        $sql = "SELECT
                    a.`DistrictID`
                FROM
                    ktv_district a
                WHERE
                    a.`District` = ?
                    AND a.`ProvinceID` = ?
                LIMIT 1";
        $Data = $this->db->query($sql,array($District,$ProvinceID))->row_array();
        echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        if(isset($Data['DistrictID'])){
            $DistrictID = $Data['DistrictID'];

            //Ambil Increment
            $sql = "SELECT
                        (a.`SubDistrictID`+1) AS NewID
                    FROM
                        ktv_subdistrict a
                    WHERE
                        a.`DistrictID` = '$DistrictID'
                    ORDER BY a.`SubDistrictID` DESC
                    LIMIT 1";
            $DataID = $this->db->query($sql)->row_array();
            echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            $SubDistrictID = $DataID['NewID'];
            if($SubDistrictID == ""){
                $SubDistrictID = (int) $DistrictID."001";
            }

            $sql = "INSERT IGNORE INTO `ktv_subdistrict` (
                `SubDistrictID`,
                `SubDistrict`,
                `DistrictID`,
                `active`,
                `StatusCode`
              )
              VALUES ($SubDistrictID,
                    '$SubDistrict',
                    '$DistrictID',
                    '1',
                    'active')
              ";
            $query = $this->db->query($sql);
            echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        }
    }

    public function ProsesBaruVillage($ProvinceID,$District,$SubDistrict,$Village){
        $ArrVillage = explode(",",$Village);
        for ($i=0; $i < count($ArrVillage); $i++) { 
            $VillageName = trim($ArrVillage[$i]);

            //Ambil SubDistrictID
            $sql = "SELECT
                        a.`SubDistrictID`
                    FROM
                        ktv_subdistrict a
                        JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                        JOIN ktv_province c ON b.`ProvinceID` = c.`ProvinceID`
                    WHERE
                        a.`SubDistrict` = '$SubDistrict'
                        AND b.`District` = '$District'
                        AND c.`ProvinceID` = '$ProvinceID'
                    LIMIT 1";
            $Data = $this->db->query($sql,array($District,$ProvinceID))->row_array();
            //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            if(isset($Data['SubDistrictID'])){
                $SubDistrictID = $Data['SubDistrictID'];

                //Ambil Increment
                $sql = "SELECT
                            (a.`VillageID`+1) AS NewID
                        FROM
                            ktv_village a
                        WHERE
                            a.`SubDistrictID` = '$SubDistrictID'
                        ORDER BY a.`VillageID` DESC
                        LIMIT 1";
                $DataID = $this->db->query($sql)->row_array();
                //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';

                $VillageID = $DataID['NewID'];
                if($VillageID == ""){
                    $VillageID = (int) $SubDistrictID."001";
                }

                $sql = "INSERT IGNORE INTO `ktv_village` (
                    `VillageID`,
                    `Village`,
                    `SubDistrictID`,
                    `StatusCode`,
                    `CreatedBy`,
                    `DateCreated`
                )
                VALUES (
                    $VillageID,
                    '$VillageName',
                    '$SubDistrictID',
                    'active',
                    '1',
                    NOW()
                )
                ";
                $query = $this->db->query($sql);
                //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
        }
    }

    public function InsertDataRegionImport($DataInsert){
        $this->db->trans_begin();

        for ($i=0; $i < count($DataInsert); $i++) { 
            //District =========================== (Begin)

            //Cek District sudah ada belum
            //$DistrictID = $this->ProsesDistrict($DataInsert[$i]['ProvinceID'],$DataInsert[$i]['District']);
            //District =========================== (End)

            //SubDistrict =========================== (Begin)
            //$SubDistrictID = $this->ProsesSubDistrict($DistrictID,$DataInsert[$i]['District'],$DataInsert[$i]['SubDistrict']);
            //$this->ProsesBaruSubDistrict($DataInsert[$i]['ProvinceID'],$DataInsert[$i]['District'],$DataInsert[$i]['SubDistrict']);

            //SubDistrict =========================== (End)

            //Village =========================== (Begin)
            //$this->ProsesVillage($SubDistrictID,$DataInsert[$i]['Village']);
            $this->ProsesBaruVillage($DataInsert[$i]['ProvinceID'],$DataInsert[$i]['District'],$DataInsert[$i]['SubDistrict'],$DataInsert[$i]['Village']);
            //Village =========================== (End)
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Import Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Import Success";
        }

        return $results;
    }

    public function GetGridSelectMember($pSearch,$start,$limit,$sortingField,$sortingDir){
        //BENTUK QUERY FILTER =============================================== (BEGIN)
        $sqlFilter = "";

        if ($pSearch['TextSearch'] != "") {
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['TextSearch']}%' OR a.MemberDisplayID like '%{$pSearch['TextSearch']}%' ) ";
        }

        if($pSearch['ProvinceID'] != ""){
            $sqlFilter .= " AND prov.ProvinceID = ".$pSearch['ProvinceID'];
        }

        if($pSearch['DistrictID'] != ""){
            $sqlFilter .= " AND dis.DistrictID = ".$pSearch['DistrictID'];
        }

        if($pSearch['SubDistrictID'] != ""){
            $sqlFilter .= " AND subd.SubDistrictID = ".$pSearch['SubDistrictID'];
        }

        if($pSearch['VillageID'] != ""){
            $sqlFilter .= " AND vil.VillageID = ".$pSearch['VillageID'];
        }

        if($pSearch['ExceptionID'] != ""){
            $pSearch['ExceptionID'] = filter_var($pSearch['ExceptionID'],FILTER_SANITIZE_STRING);
            $sqlFilter .= " AND a.MemberID NOT IN (".$pSearch['ExceptionID'].") ";
        }
        //BENTUK QUERY FILTER =============================================== (End)

        //BENTUK QUERY Hak Akses =============================================== (Begin)
        $sqlHakAkses = array();

        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //BENTUK QUERY Hak Akses =============================================== (End)

        //fixed tampilkan petani
        $sqlFilterRole = "";
        switch($pSearch['ListType']){
            case 'agent':
                $sqlFilterRole = " AND mr.MRoleID IN (5,6,7,8,9,10) ";
            break;
            case 'farmer':
                $sqlFilterRole = " AND mr.MRoleID = 1 ";
            break;
            default:
                $sqlFilterRole = "";
            break;
        }
        
        if ($sortingField == "")
            $sortingField = 'MemberName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`MemberID`
                    , a.`MemberDisplayID`
                    , a.`MemberName`
                    , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                    , a.`Gender`
                    , prov.`Province`
                    , dis.`District`
                    , subd.`SubDistrict`
                    , vil.`Village`
                FROM
                    ktv_members a
                    LEFT JOIN ktv_member_role mr ON a.MemberID = mr.MemberID
                    LEFT JOIN ktv_village vil ON vil.`VillageID` = a.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                    {$sqlHakAkses['join']}
                WHERE
                    a.`StatusCode` = 'active'
                    $sqlFilterRole
                    {$sqlHakAkses['where']}
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

    public function InputUploadZip($filename){
        //Example filename: DataElementUid + "" + supplierUid + "" + eventUid + "_" + timestamp + ".zip"
        $ArrTmp = explode("_",$filename);
        $DataElementUid = $ArrTmp[0];
        $SupplierUid = $ArrTmp[1];
        $EventUid = $ArrTmp[2];

        //Cek DataElementUid
        $sql = "SELECT
                    a.SetKey
                FROM
                    sys_setting a
                WHERE
                    a.`SetValue` = ?
                    AND a.`SetName` = 'Data Element'
                LIMIT 1";
        $DataDatElem = $this->db->query($sql,array($DataElementUid))->row_array();
        
        if(isset($DataDatElem['SetKey'])){
            //Insert ke log
            $sql = "INSERT INTO `sys_log_uploadzip` SET  
                        `Filename` = ?,
                        `TypeProcess` = ?,
                        `DateGenerated` = NOW()
                    ";
            $p = array(
                $filename,
                $DataDatElem['SetKey']
            );
            $query = $this->db->query($sql,$p);
            if($query){
                $return['success'] = true;
                $return['message'] = "Success";
            }else{
                $return['success'] = false;
                $return['message'] = "Query insert sys_log_uploadzip failed";
            }
        }else{
            $return['success'] = false;
            $return['message'] = "DataElementUid not recognize";
        }

        return $return;
    }

    public function CleanUpUploadZip(){
        $sql = "SELECT 
                    `LogID`,
                    `Filename`,
                    `TypeProcess`,
                    `Remark`,
                    `StatusProcess`,
                    `StatusCode`,
                    `DateGenerated` 
                FROM
                    `sys_log_uploadzip` a
                WHERE
                    DATE(a.`DateGenerated`) <= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    AND a.StatusProcess = 'Doned'
                ";
        $DataList = $this->db->query($sql)->result_array();

        if(isset($DataList[0]['LogID'])){
            for ($i=0; $i < count($DataList); $i++) { 
                if(@unlink('files/uploadzip/'.$DataList[$i]['Filename'])){
                    $sql = "DELETE FROM sys_log_uploadzip WHERE LogID = {$DataList[$i]['LogID']} LIMIT 1";
                    $query = $this->db->query($sql);
                }
            }
        }
    }
    private function DeleteTempUploadZip(){
        $files = glob('files/uploadzip/temp/*'); // get all file names
        foreach($files as $file){ // iterate files
        if(is_file($file))
            @unlink($file); // delete file
        }
    }

    private function UpdateStatusProcessUploadZip($LogID){
        $sql = "UPDATE sys_log_uploadzip a SET
                    a.`StatusProcess` = 'Doned',
                    a.`Remark` = null
                WHERE
                    a.`LogID` = '{$LogID}'
                LIMIT 1";
        $query = $this->db->query($sql);
    }

    public function ProcessUploadZip($OpsiCall){
        $this->load->library('awsfileupload');
        $this->load->model('grower/mgrower');

        //Cleanup Data yg sudah 'Doned' dan lebih lama dari sebulan
        $cleanup = $this->CleanUpUploadZip();

        switch($OpsiCall){
            case 'CronNormal':
                $SqlCronType = " AND a.ProcessCount < 3 #Hanya jalankan yg belum diproses lebih dari 2 ";
            break;
            case 'CronDaily':
                $SqlCronType = " AND a.ProcessCount >= 3 #Hanya jalankan yg belum diproses lebih dari 2 ";
            break;
        }

        $sql = "SELECT
                    a.LogID,
                    a.`Filename`,
                    a.`TypeProcess`
                FROM
                    `sys_log_uploadzip` a
                WHERE
                    a.`StatusProcess` = 'Not Done'
                    AND a.`StatusCode` = 'active'
                    $SqlCronType
                ORDER BY a.`DateGenerated` DESC";
        $DataList = $this->db->query($sql)->result_array();
        // echo '<pre>'; print_r($DataList); exit;
        
        $ObjZip = new ZipArchive;

        if(isset($DataList[0]['LogID'])){
            for ($i=0; $i < count($DataList); $i++) {
                $this->db->trans_begin();
                $NoFileFound = false;

                //Hapus semua file di temp
                $this->DeleteTempUploadZip();

                switch($DataList[$i]['TypeProcess']){
                    case 'Farmer Photo':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])){
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();
                                
                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                    if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                        $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_PATH, 'images');
                                        if ($upload['success'] == true) {
                                            $FilenameProcess = $upload['filenamepath'];

                                            //Hapus semua file di temp
                                            $this->DeleteTempUploadZip();
                                        }
                                    }

                                    //Cari Membernya
                                    $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);
                                    if(isset($DataMember['MemberID'])){
                                        $sql = "UPDATE ktv_members a SET
                                                    a.Photo = ?
                                                WHERE
                                                    a.MemberUID = ?
                                                LIMIT 1";
                                        $p = array(
                                            $FilenameProcess,
                                            $MemberUid
                                        );
                                        $query = $this->db->query($sql,$p);
                                        // if($this->db->affected_rows() > 0){
                                        if($query){
                                            //Update Status Process
                                            $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                        }else{
                                            //Update Remark
                                            $sql = "UPDATE sys_log_uploadzip a SET
                                                        a.`Remark` = 'MemberUid not found'
                                                    WHERE
                                                        a.`LogID` = '{$DataList[$i]['LogID']}'
                                                    LIMIT 1";
                                            $query = $this->db->query($sql);
                                        }    
                                    }
                                }
                            }
                        }else{
                            $NoFileFound = true;
                        }
                    break;

                    case 'KTP Photo':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])){
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();
                                
                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                    if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                        $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_KTP_PATH, 'images');
                                        if ($upload['success'] == true) {
                                            $FilenameProcess = $upload['filenamepath'];

                                            //Hapus semua file di temp
                                            $this->DeleteTempUploadZip();
                                        }
                                    }

                                    //Cari Membernya
                                    $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);
                                    if(isset($DataMember['MemberID'])){
                                        $sql = "UPDATE ktv_members a SET
                                                    a.KTPFile = ?
                                                WHERE
                                                    a.MemberUID = ?
                                                LIMIT 1";
                                        $p = array(
                                            $FilenameProcess,
                                            $MemberUid
                                        );
                                        $query = $this->db->query($sql,$p);
                                        // if($this->db->affected_rows() > 0){
                                        if($query){
                                            //Update Status Process
                                            $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                        }else{
                                            //Update Remark
                                            $sql = "UPDATE sys_log_uploadzip a SET
                                                        a.`Remark` = 'MemberUid not found'
                                                    WHERE
                                                        a.`LogID` = '{$DataList[$i]['LogID']}'
                                                    LIMIT 1";
                                            $query = $this->db->query($sql);
                                        }    
                                    }
                                }
                            }
                        }else{
                            $NoFileFound = true;
                        }
                    break;

                    case 'Garden Photo':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])){
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {                         
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();
                                
                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    $sql2 = "SELECT
                                                a.LogID,
                                                a.`Filename`,
                                                a.`TypeProcess`
                                            FROM
                                                `sys_log_uploadzip` a
                                            WHERE
                                                a.`StatusProcess` = 'Not Done'
                                                AND a.`StatusCode` = 'active'
                                                AND a.LogID = ?
                                            ORDER BY a.`DateGenerated` DESC";
                                    $result2 = $this->db->query($sql2,array($DataList[$i]['LogID']));
                                    if($result2->num_rows()>0){
                                        //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                        if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                            $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_PLOT_PATH, 'images');
                                            if ($upload['success'] == true) {
                                                $FilenameProcess = $upload['filenamepath'];
    
                                                //Hapus semua file di temp
                                                $this->DeleteTempUploadZip();
                                            }
                                        }
    
                                        $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);
                                        if(isset($DataMember['MemberID'])){
                                            //Cari MemberUid dan EventUid
                                            $sql = "UPDATE `ktv_survey_plot` a SET
                                                        a.PhotoOfVisit = ?
                                                    WHERE
                                                        a.MemberUid = ?
                                                        AND a.uid = ?
                                                    LIMIT 1
                                                    ";
                                            $p = array(
                                                $FilenameProcess,
                                                // '/images/plot_visit/'.$FilenameProcess,
                                                // '/images/plot_visit/Farmer/'.$FilenameProcess,
                                                $MemberUid,
                                                $EventUid
                                            );
                                            $query = $this->db->query($sql,$p);
                                            // if($this->db->affected_rows() > 0){
                                            if($query){
                                                //Update Status Process
                                                $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                            }else{
                                                //Update Remark
                                                $sql = "UPDATE sys_log_uploadzip a SET
                                                            a.`Remark` = 'SupplierUid and EventUid not found'
                                                        WHERE
                                                            a.`LogID` = '{$DataList[$i]['LogID']}'
                                                        LIMIT 1";
                                                $query = $this->db->query($sql);
                                            }    
                                        }
                                    }
                                }
                            }
                        }else{
                            $NoFileFound = true;
                        }
                    break;

                    case 'Contract Photo':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])){
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {                         
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();
                                
                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    $sql2 = "SELECT
                                                a.LogID,
                                                a.`Filename`,
                                                a.`TypeProcess`
                                            FROM
                                                `sys_log_uploadzip` a
                                            WHERE
                                                a.`StatusProcess` = 'Not Done'
                                                AND a.`StatusCode` = 'active'
                                                AND a.LogID = ?
                                            ORDER BY a.`DateGenerated` DESC";
                                    $result2 = $this->db->query($sql2,array($DataList[$i]['LogID']));
                                    if($result2->num_rows()>0){
                                        //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                        if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                            $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_PLOT_CONTRACT_PATH, 'images');
                                            if ($upload['success'] == true) {
                                                $FilenameProcess = $upload['filenamepath'];
    
                                                //Hapus semua file di temp
                                                $this->DeleteTempUploadZip();
                                            }
                                        }
    
                                        $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);
                                        if(isset($DataMember['MemberID'])){
                                            //Cari MemberUid dan EventUid
                                            $sql = "UPDATE `ktv_survey_plot` a SET
                                                        a.ContractFile = ?
                                                    WHERE
                                                        a.MemberUid = ?
                                                        AND a.uid = ?
                                                    LIMIT 1
                                                    ";
                                            $p = array(
                                                $FilenameProcess,
                                                // '/images/plot_visit/'.$FilenameProcess,
                                                // '/images/plot_visit/Farmer/'.$FilenameProcess,
                                                $MemberUid,
                                                $EventUid
                                            );
                                            $query = $this->db->query($sql,$p);
                                            // if($this->db->affected_rows() > 0){
                                            if($query){
                                                //Update Status Process
                                                $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                            }else{
                                                //Update Remark
                                                $sql = "UPDATE sys_log_uploadzip a SET
                                                            a.`Remark` = 'SupplierUid and EventUid not found'
                                                        WHERE
                                                            a.`LogID` = '{$DataList[$i]['LogID']}'
                                                        LIMIT 1";
                                                $query = $this->db->query($sql);
                                            }    
                                        }
                                    }
                                }
                            }
                        }else{
                            $NoFileFound = true;
                        }
                    break;

                    case 'Farmer Signature':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])) {
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();

                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    $sql2 = "SELECT
                                                a.LogID,
                                                a.`Filename`,
                                                a.`TypeProcess`
                                            FROM
                                                `sys_log_uploadzip` a
                                            WHERE
                                                a.`StatusProcess` = 'Not Done'
                                                AND a.`StatusCode` = 'active'
                                                AND a.LogID = ?
                                            ORDER BY a.`DateGenerated` DESC";
                                    $result2 = $this->db->query($sql2,array($DataList[$i]['LogID']));
                                    if($result2->num_rows()>0){

                                        //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                        if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                            $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_SIGNATURE_PATH, 'images');
                                            if ($upload['success'] == true) {
                                                $FilenameProcess = $upload['filenamepath'];

                                                //Hapus semua file di temp
                                                $this->DeleteTempUploadZip();
                                            }
                                        }

                                        //get member data
                                        // $DataMember = $this->mgrower->getMemberDataByUID($MemberUid);
                                        $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);

                                        if(isset($DataMember['MemberID'])){
                                            //Update Signature dan set consent letter status
                                            $sql = "UPDATE ktv_members a SET
                                                        a.LearningContractStatus = '1',
                                                        a.FarmerSignature = ?
                                                    WHERE
                                                        a.MemberUID = ?
                                                    LIMIT 1";
                                            $p = array(
                                                $FilenameProcess,
                                                $MemberUid
                                            );
                                            $query = $this->db->query($sql,$p);

                                            //Update Status Process
                                            $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                        } else {
                                            //Update Remark
                                            $sql = "UPDATE sys_log_uploadzip a SET
                                                        a.`Remark` = 'MemberUid not found'
                                                    WHERE
                                                        a.`LogID` = '{$DataList[$i]['LogID']}'
                                                    LIMIT 1";
                                            $query = $this->db->query($sql);
                                        }
                                    }
                                }
                            }
                        }
                    break;

                    case 'Owned Document Photo':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])) {
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();

                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    $sql2 = "SELECT
                                                a.LogID,
                                                a.`Filename`,
                                                a.`TypeProcess`
                                            FROM
                                                `sys_log_uploadzip` a
                                            WHERE
                                                a.`StatusProcess` = 'Not Done'
                                                AND a.`StatusCode` = 'active'
                                                AND a.LogID = ?
                                            ORDER BY a.`DateGenerated` DESC";
                                    $result2 = $this->db->query($sql2,array($DataList[$i]['LogID']));
                                    if($result2->num_rows()>0){

                                        //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                        if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                            $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_PLOT_DOC_OWNED_PATH, 'images');
                                            if ($upload['success'] == true) {
                                                $FilenameProcess = $upload['filenamepath'];

                                                //Hapus semua file di temp
                                                $this->DeleteTempUploadZip();
                                            }
                                        }

                                        //get member data
                                        // $DataMember = $this->mgrower->getMemberDataByUID($MemberUid);
                                        $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);

                                        if(isset($DataMember['MemberID'])){
                                            //Update Signature dan set consent letter status
                                            $sql = "UPDATE ktv_survey_plot a SET
                                                        a.OwnerDocPhoto = ?
                                                    WHERE
                                                        a.MemberUID = ?,
                                                        AND a.uid = ?
                                                    LIMIT 1";
                                            $p = array(
                                                $FilenameProcess,
                                                $MemberUid,
                                                $EventUid
                                            );
                                            $query = $this->db->query($sql,$p);

                                            //Update Status Process
                                            $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                        } else {
                                            //Update Remark
                                            $sql = "UPDATE sys_log_uploadzip a SET
                                                        a.`Remark` = 'MemberUid not found'
                                                    WHERE
                                                        a.`LogID` = '{$DataList[$i]['LogID']}'
                                                    LIMIT 1";
                                            $query = $this->db->query($sql);
                                        }
                                    }
                                }
                            }
                        }
                    break;

                    case 'Soil Erotion':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])) {
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();

                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    $sql2 = "SELECT
                                                a.LogID,
                                                a.`Filename`,
                                                a.`TypeProcess`
                                            FROM
                                                `sys_log_uploadzip` a
                                            WHERE
                                                a.`StatusProcess` = 'Not Done'
                                                AND a.`StatusCode` = 'active'
                                                AND a.LogID = ?
                                            ORDER BY a.`DateGenerated` DESC";
                                    $result2 = $this->db->query($sql2,array($DataList[$i]['LogID']));
                                    if($result2->num_rows()>0){

                                        //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                        if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                            $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_PLOT_SOIL_EROTION_PATH, 'images');
                                            if ($upload['success'] == true) {
                                                $FilenameProcess = $upload['filenamepath'];

                                                //Hapus semua file di temp
                                                $this->DeleteTempUploadZip();
                                            }
                                        }

                                        //get member data
                                        // $DataMember = $this->mgrower->getMemberDataByUID($MemberUid);
                                        $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);

                                        if(isset($DataMember['MemberID'])){
                                            //Update Signature dan set consent letter status
                                            $sql = "UPDATE ktv_survey_plot a SET
                                                        a.SoilErotionFile = ?
                                                    WHERE
                                                        a.MemberUID = ?,
                                                        AND a.uid = ?
                                                    LIMIT 1";
                                            $p = array(
                                                $FilenameProcess,
                                                $MemberUid,
                                                $EventUid
                                            );
                                            $query = $this->db->query($sql,$p);

                                            //Update Status Process
                                            $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                        } else {
                                            //Update Remark
                                            $sql = "UPDATE sys_log_uploadzip a SET
                                                        a.`Remark` = 'MemberUid not found'
                                                    WHERE
                                                        a.`LogID` = '{$DataList[$i]['LogID']}'
                                                    LIMIT 1";
                                            $query = $this->db->query($sql);
                                        }
                                    }
                                }
                            }
                        }
                    break;

                    case 'Soil Accumulation':
                        if(file_exists('files/uploadzip/'.$DataList[$i]['Filename'])) {
                            $res = $ObjZip->open('files/uploadzip/'.$DataList[$i]['Filename']);
                            if ($res === TRUE) {
                                $ObjZip->extractTo('files/uploadzip/temp');
                                $ObjZip->close();

                                $PathTemp = 'files/uploadzip/temp';
                                $FilesTemp = array_diff(scandir($PathTemp), array('.', '..'));
                                foreach ($FilesTemp as $key => $FilenameProcess) {
                                    $ArrTmp = explode("_",$FilenameProcess);
                                    $DataElementUid = $ArrTmp[0];
                                    $MemberUid = $ArrTmp[1];
                                    $EventUid = $ArrTmp[2];

                                    $sql2 = "SELECT
                                                a.LogID,
                                                a.`Filename`,
                                                a.`TypeProcess`
                                            FROM
                                                `sys_log_uploadzip` a
                                            WHERE
                                                a.`StatusProcess` = 'Not Done'
                                                AND a.`StatusCode` = 'active'
                                                AND a.LogID = ?
                                            ORDER BY a.`DateGenerated` DESC";
                                    $result2 = $this->db->query($sql2,array($DataList[$i]['LogID']));
                                    if($result2->num_rows()>0){

                                        //Di cek Apakah File Exist, Kalo ada di upload ke AWS
                                        if(file_exists($PathTemp.'/'.$FilenameProcess)) {
                                            $upload = $this->awsfileupload->upload($PathTemp.'/'.$FilenameProcess,$FilenameProcess,AWSS3_FARMER_PLOT_SOIL_ACC_PATH, 'images');
                                            if ($upload['success'] == true) {
                                                $FilenameProcess = $upload['filenamepath'];

                                                //Hapus semua file di temp
                                                $this->DeleteTempUploadZip();
                                            }
                                        }

                                        //get member data
                                        // $DataMember = $this->mgrower->getMemberDataByUID($MemberUid);
                                        $DataMember = $this->mpetani->GetMemberIDByMemberUidId($MemberUid);

                                        if(isset($DataMember['MemberID'])){
                                            //Update Signature dan set consent letter status
                                            $sql = "UPDATE ktv_survey_plot a SET
                                                        a.SoilAccumulationFile = ?
                                                    WHERE
                                                        a.MemberUID = ?,
                                                        AND a.uid = ?
                                                    LIMIT 1";
                                            $p = array(
                                                $FilenameProcess,
                                                $MemberUid,
                                                $EventUid
                                            );
                                            $query = $this->db->query($sql,$p);

                                            //Update Status Process
                                            $this->UpdateStatusProcessUploadZip($DataList[$i]['LogID']);
                                        } else {
                                            //Update Remark
                                            $sql = "UPDATE sys_log_uploadzip a SET
                                                        a.`Remark` = 'MemberUid not found'
                                                    WHERE
                                                        a.`LogID` = '{$DataList[$i]['LogID']}'
                                                    LIMIT 1";
                                            $query = $this->db->query($sql);
                                        }
                                    }
                                }
                            }
                        }
                    break;
                }

                //Kasih increment Process
                $sql = "UPDATE sys_log_uploadzip a SET
                            a.`ProcessCount` = a.ProcessCount + 1
                        WHERE
                            a.`LogID` = '{$DataList[$i]['LogID']}'
                        LIMIT 1";
                $query = $this->db->query($sql);

                //No File
                if($NoFileFound == true){
                    $sql = "UPDATE sys_log_uploadzip a SET
                                a.`StatusCode` = 'nullified'
                            WHERE
                                a.`LogID` = '{$DataList[$i]['LogID']}'
                            LIMIT 1";
                    $query = $this->db->query($sql);
                }

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }
            }
        }

        $results['success'] = true;
        $results['message'] = "Process Finished";
        return $results;
    }

    public function ProcessStaffActData($filepath){
        $ObjZip = new ZipArchive;
        $Filepathinfo = pathinfo($filepath);
        $result = array();
        
        //hilangkan .json akhiran
        $Filepathinfo['FilenameMod'] = str_replace('-json','',$Filepathinfo['filename']);

        //Buat folder temp untuk process
        if (!file_exists($Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod'])) {
            make_directory($Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod'], 0777, true);
        }

        $res = $ObjZip->open($filepath);
        if ($res == true) {
            $ObjZip->extractTo($Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod']);
            $ObjZip->close();

            //Ambil file (Asumsi hanya single file saja)
            $PathTemp = $Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod'];
            $FilesTemp = scandir($PathTemp);
            $FirstFileZip = $FilesTemp[2];

            $JsonFile = file_get_contents($Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod'].'/'.$FirstFileZip);
            $JsonValue = json_decode($JsonFile, true);

            //Cek Data Staff
            $UsernameProcess = $JsonValue['username'];
            $sql = "SELECT
                        a.`UserId`
                        , b.`PersonID`
                    FROM
                        sys_user a
                        INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                    WHERE
                        a.`UserName` = ?
                    LIMIT 1";
            $DataUser = $this->db->query($sql,array($UsernameProcess))->row_array();
            if(isset($DataUser['UserId'])){
                $ActivityInserted = 0;
                for ($i=0; $i < count($JsonValue['activity']); $i++) {
                    $sql = "INSERT INTO ktv_staff_activity SET
                                `UserId` = ?,
                                `PersonID` = ?,
                                `Category` = ?,
                                `Description` = ?,
                                `ActDateTime` = ?,
                                `Latitude` = ?,
                                `Longitude` = ?,
                                `Accuracy` = ?,
                                `Altitude` = ?,
                                `DateGenerated` = NOW(),
                                `GeneratedBy` = ?,
                                `event_uid` = ?
                            ON DUPLICATE KEY UPDATE
                                `Category` = ?,
                                `Description` = ?,
                                `ActDateTime` = ?,
                                `Latitude` = ?,
                                `Longitude` = ?,
                                `Accuracy` = ?,
                                `Altitude` = ?,
                                `DateGenerated` = NOW(),
                                `GeneratedBy` = ?
                            ";
                    $p = array(
                        $DataUser['UserId'],
                        $DataUser['PersonID'],
                        $JsonValue['activity'][$i]['category'],
                        $JsonValue['activity'][$i]['activity_description'],
                        $JsonValue['activity'][$i]['datetime_activity'],
                        $JsonValue['activity'][$i]['latitude'],
                        $JsonValue['activity'][$i]['longitude'],
                        $JsonValue['activity'][$i]['accuracy'],
                        $JsonValue['activity'][$i]['altitude'],
                        '1',
                        $JsonValue['activity'][$i]['event_uid'],
                        //update
                        $JsonValue['activity'][$i]['category'],
                        $JsonValue['activity'][$i]['activity_description'],
                        $JsonValue['activity'][$i]['datetime_activity'],
                        $JsonValue['activity'][$i]['latitude'],
                        $JsonValue['activity'][$i]['longitude'],
                        $JsonValue['activity'][$i]['accuracy'],
                        $JsonValue['activity'][$i]['altitude'],
                        '1'
                    );
                    $query = $this->db->query($sql,$p);
                    if($this->db->affected_rows() > 0){
                        $ActivityInserted++;
                    }
                }

                if($ActivityInserted > 0){
                    $result['status'] = true;
                    $result['message'] = "Data Saved";
                }else{
                    $result['status'] = false;
                    $result['message'] = "No Activity Inserted/Updated";
                }
            }else{
                $result['status'] = false;
                $result['message'] = "Staff data not found";
            }

            //Hapus file yg diproses =========================== (Begin)
            delete_file($filepath);
            $files = glob($Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod'].'/*'); // get all file names
            foreach($files as $file){ // iterate files
            if(is_file($file))
                delete_file($file); // delete file
            }
            remove_directory($Filepathinfo['dirname'].'/'.$Filepathinfo['FilenameMod']);
            //Hapus file yg diproses =========================== (End)
        }else{
            $result['status'] = false;
            $result['message'] = "Failed to open zip file";
        }

        return $result;
    }

    public function InputApiFileuploadStaffActivity($filepath){
        $result = array();
        $Filepathinfo = pathinfo($filepath);

        //Cek Staff Activity Photo
        if (strpos($Filepathinfo['filename'], 'upload-staff_activity-photo') !== false) {
            //Insert ke log
            $sql = "INSERT INTO `sys_log_uploadzip` SET  
                        `Filename` = ?,
                        `TypeProcess` = ?,
                        `DateGenerated` = NOW()
                    ";
            $p = array(
                $Filepathinfo['basename'],
                'staff_activity_photo'
            );
            $query = $this->db->query($sql,$p);
        }

        $result['status'] = true;
        $result['message'] = 'File Saved';
        return $result;
    }

    public function FixAverageAgeTrees() {
        $this->db->trans_begin();

        $sql = "SELECT
                    a.`MemberID`
                    , a.`SurveyNr`
                    , a.`PlotNr`
                    , a.`FirstPlantingYear`
                    , a.YearPlantingCurrent
                    , a.`AverageAgeTree`
                FROM
                    ktv_survey_plot a";
        $DataList = $this->db->query($sql)->result_array();

        $DataUpdatedCount = 0;
        if($DataList[0]['MemberID']) {
            for ($i=0; $i < count($DataList); $i++) { 
                $TreeAge = false;
                $TahunSkr = (int) date('Y');

                if($DataList[$i]['YearPlantingCurrent'] != "") {
                    $ReplantingYear = (int) $DataList[$i]['YearPlantingCurrent'];
                    $TreeAge = $TahunSkr - $ReplantingYear;
                } else {
                    if($DataList[$i]['FirstPlantingYear'] != "") {
                        $FirstYear = (int) $DataList[$i]['FirstPlantingYear'];
                        $TreeAge = $TahunSkr - $FirstYear;
                    }
                }

                if($TreeAge != false) {
                    $sql = "UPDATE ktv_survey_plot a SET
                                a.`AverageAgeTree` = {$TreeAge}
                            WHERE
                                a.`MemberID` = '{$DataList[$i]['MemberID']}'
                                AND a.`SurveyNr` = '{$DataList[$i]['SurveyNr']}'
                                AND a.`PlotNr` = '{$DataList[$i]['PlotNr']}'
                            LIMIT 1";
                    $query = $this->db->query($sql);
                    $DataUpdatedCount++;
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['DataUpdatedCount'] = $DataUpdatedCount." data updated";
            $results['message'] = "Process Success";
        }

        return $results;
    }

    public function FixNameMemberDkk() {
        $this->db->trans_begin();

        //Data Members
        $sql = "SELECT
                    a.`MemberID`
                    , a.`MemberName`
                FROM
                    ktv_members a
                WHERE
                    a.`StatusCode` = 'active'";
        $data = $this->db->query($sql)->result_array();
        for ($i=0; $i < count($data); $i++) { 
            $Name = ucwords($data[$i]['MemberName']);
            $sql = "UPDATE ktv_members a SET
                        a.`MemberName` = ?
                    WHERE
                        a.`MemberID` = {$data[$i]['MemberID']}
                    LIMIT 1";
            $query = $this->db->query($sql, array($Name));
        }

        //Data Family
        $sql = "SELECT
                    a.`FamLabID`
                    , a.`FamLabName`
                FROM
                    `ktv_member_family_labour` a
                WHERE
                    a.`StatusCode` = 'active'";
        $data = $this->db->query($sql)->result_array();
        for ($i=0; $i < count($data); $i++) { 
            $Name = ucwords($data[$i]['FamLabName']);
            $sql = "UPDATE ktv_member_family_labour a SET
                        a.`FamLabName` = ?
                    WHERE
                        a.`FamLabID` = {$data[$i]['FamLabID']}
                    LIMIT 1";
            $query = $this->db->query($sql, array($Name));
        }

        //Data Labour
        $sql = "SELECT
                    a.`LaboID`
                    , a.`LaboName`
                FROM
                    `ktv_member_labour` a
                WHERE
                    a.`StatusCode` = 'active'";
        $data = $this->db->query($sql)->result_array();
        for ($i=0; $i < count($data); $i++) { 
            $Name = ucwords($data[$i]['LaboName']);
            $sql = "UPDATE ktv_member_labour a SET
                        a.`LaboName` = ?
                    WHERE
                        a.`LaboID` = {$data[$i]['LaboID']}
                    LIMIT 1";
            $query = $this->db->query($sql, array($Name));
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Process Success";
        }

        return $results;
    }

    public function AssignMemberMillByFa() {
        $this->db->trans_begin();

        $sql = "INSERT IGNORE INTO `ktv_access_partner_member` (
                    `apmID`,
                    `apmPartnerID`,
                    `apmMemberID`,
                    `DateCreated`,
                    `CreatedBy`
                )
                SELECT
                    NULL
                    , c.`PartnerID`
                    , a.`MemberID`
                    , NOW()
                    , '1'
                FROM
                    ktv_members a
                    INNER JOIN ktv_mill_fa_assignment b ON a.`CreatedBy` = b.`UserID`
                    INNER JOIN ktv_mill c ON b.`MillID` = c.`MillID`
                    INNER JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                WHERE
                    DATE(a.`DateCreated`) >= CURDATE()
                    AND a.`CreatedBy` != 1
                    AND d.`PartnerIndustry` = 3
                ";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Process Success";
        }

        return $results;
    }

    public function AssignMemberMillByFa2019() {
        $this->db->trans_begin();

        $sql = "INSERT IGNORE INTO `ktv_access_partner_member` (
                    `apmID`,
                    `apmPartnerID`,
                    `apmMemberID`,
                    `DateCreated`,
                    `CreatedBy`
                )
                SELECT
                    NULL
                    , c.`PartnerID`
                    , a.`MemberID`
                    , NOW()
                    , '1'
                FROM
                    ktv_members a
                    INNER JOIN ktv_mill_fa_assignment b ON a.`CreatedBy` = b.`UserID`
                    INNER JOIN ktv_mill c ON b.`MillID` = c.`MillID`
                    INNER JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                WHERE
                    YEAR(a.`DateCreated`) >= 2019
                    AND a.`CreatedBy` != 1
                    AND d.`PartnerIndustry` = 3
                ";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Process Success";
        }

        return $results;
    }

    public function FixDataHarvestMonthsData() {
        $this->db->trans_begin();

        $sql = "SELECT
                    a.`MemberID`
                    , a.`SurveyNr`
                    , a.`PlotNr`
                    , a.LeanHarvestSeasonJan
                    , a.LeanHarvestSeasonFeb
                    , a.LeanHarvestSeasonMar
                    , a.LeanHarvestSeasonApr
                    , a.LeanHarvestSeasonMay
                    , a.LeanHarvestSeasonJun
                    , a.LeanHarvestSeasonJul
                    , a.LeanHarvestSeasonAug
                    , a.LeanHarvestSeasonSep
                    , a.LeanHarvestSeasonOct
                    , a.LeanHarvestSeasonNov
                    , a.LeanHarvestSeasonDec
                    , IFNULL(CAST(a.`LeanHarvestSeasonJan` AS SIGNED),0) + 
                        IFNULL(CAST(a.`LeanHarvestSeasonFeb` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonMar` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonApr` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonMay` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonJun` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonJul` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonAug` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonSep` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonOct` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonNov` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonDec` AS SIGNED),0)
                    AS JumlahBulan
                FROM
                    ktv_survey_plot a
                WHERE 1=1
                    /*AND a.MemberID NOT IN (
                        SELECT
                            a.`MemberID`
                        FROM
                            ktv_survey_sdg a
                        WHERE
                            a.`StatusVerified` = 'Yes'
                        GROUP BY a.`MemberID`
                    )*/
                ";
        $DataList = $this->db->query($sql)->result_array();

        for ($i=0; $i < count($DataList); $i++) { 
            $JumlahBulan = (int) $DataList[$i]['JumlahBulan'];
            if($JumlahBulan > 6) {
                $LeanHarvestSeasonJan = (int) $DataList[$i]['LeanHarvestSeasonJan'];
                if($LeanHarvestSeasonJan == 1) {
                    $DbLeanHarvestSeasonJan = null;
                } else{
                    $DbLeanHarvestSeasonJan = '1';
                }

                $LeanHarvestSeasonFeb = (int) $DataList[$i]['LeanHarvestSeasonFeb'];
                if($LeanHarvestSeasonFeb == 1) {
                    $DbLeanHarvestSeasonFeb = null;
                } else{
                    $DbLeanHarvestSeasonFeb = '1';
                }

                $LeanHarvestSeasonMar = (int) $DataList[$i]['LeanHarvestSeasonMar'];
                if($LeanHarvestSeasonMar == 1) {
                    $DbLeanHarvestSeasonMar = null;
                } else{
                    $DbLeanHarvestSeasonMar = '1';
                }

                $LeanHarvestSeasonApr = (int) $DataList[$i]['LeanHarvestSeasonApr'];
                if($LeanHarvestSeasonApr == 1) {
                    $DbLeanHarvestSeasonApr = null;
                } else{
                    $DbLeanHarvestSeasonApr = '1';
                }

                $LeanHarvestSeasonMay = (int) $DataList[$i]['LeanHarvestSeasonMay'];
                if($LeanHarvestSeasonMay == 1) {
                    $DbLeanHarvestSeasonMay = null;
                } else{
                    $DbLeanHarvestSeasonMay = '1';
                }

                $LeanHarvestSeasonJun = (int) $DataList[$i]['LeanHarvestSeasonJun'];
                if($LeanHarvestSeasonJun == 1) {
                    $DbLeanHarvestSeasonJun = null;
                } else{
                    $DbLeanHarvestSeasonJun = '1';
                }

                $LeanHarvestSeasonJul = (int) $DataList[$i]['LeanHarvestSeasonJul'];
                if($LeanHarvestSeasonJul == 1) {
                    $DbLeanHarvestSeasonJul = null;
                } else{
                    $DbLeanHarvestSeasonJul = '1';
                }

                $LeanHarvestSeasonAug = (int) $DataList[$i]['LeanHarvestSeasonAug'];
                if($LeanHarvestSeasonAug == 1) {
                    $DbLeanHarvestSeasonAug = null;
                } else{
                    $DbLeanHarvestSeasonAug = '1';
                }

                $LeanHarvestSeasonSep = (int) $DataList[$i]['LeanHarvestSeasonSep'];
                if($LeanHarvestSeasonSep == 1) {
                    $DbLeanHarvestSeasonSep = null;
                } else{
                    $DbLeanHarvestSeasonSep = '1';
                }

                $LeanHarvestSeasonOct = (int) $DataList[$i]['LeanHarvestSeasonOct'];
                if($LeanHarvestSeasonOct == 1) {
                    $DbLeanHarvestSeasonOct = null;
                } else{
                    $DbLeanHarvestSeasonOct = '1';
                }

                $LeanHarvestSeasonNov = (int) $DataList[$i]['LeanHarvestSeasonNov'];
                if($LeanHarvestSeasonNov == 1) {
                    $DbLeanHarvestSeasonNov = null;
                } else{
                    $DbLeanHarvestSeasonNov = '1';
                }

                $LeanHarvestSeasonDec = (int) $DataList[$i]['LeanHarvestSeasonDec'];
                if($LeanHarvestSeasonDec == 1) {
                    $DbLeanHarvestSeasonDec = null;
                } else{
                    $DbLeanHarvestSeasonDec = '1';
                }

                $sql = "UPDATE `ktv_survey_plot` a SET
                            a.LeanHarvestSeasonJan = ?,
                            a.LeanHarvestSeasonFeb = ?,
                            a.LeanHarvestSeasonMar = ?,
                            a.LeanHarvestSeasonApr = ?,
                            a.LeanHarvestSeasonMay = ?,
                            a.LeanHarvestSeasonJun = ?,
                            a.LeanHarvestSeasonJul = ?,
                            a.LeanHarvestSeasonAug = ?,
                            a.LeanHarvestSeasonSep = ?,
                            a.LeanHarvestSeasonOct = ?,
                            a.LeanHarvestSeasonNov = ?,
                            a.LeanHarvestSeasonDec = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`SurveyNr` = ?
                            AND a.`PlotNr` = ?
                        LIMIT 1";
                $p = array(
                    $DbLeanHarvestSeasonJan,
                    $DbLeanHarvestSeasonFeb,
                    $DbLeanHarvestSeasonMar,
                    $DbLeanHarvestSeasonApr,
                    $DbLeanHarvestSeasonMay,
                    $DbLeanHarvestSeasonJun,
                    $DbLeanHarvestSeasonJul,
                    $DbLeanHarvestSeasonAug,
                    $DbLeanHarvestSeasonSep,
                    $DbLeanHarvestSeasonOct,
                    $DbLeanHarvestSeasonNov,
                    $DbLeanHarvestSeasonDec,
                    $DataList[$i]['MemberID'],
                    $DataList[$i]['SurveyNr'],
                    $DataList[$i]['PlotNr']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Process Success";
        }
        return $results;
    }

    public function RecalculateProductionData() {
        $this->db->trans_begin();

        $sql = "SELECT
                    g1.MemberName
                    , g1.MemberDisplayID
                    , g1.MemberUid
                    , g1.uid
                    , g1.MemberID
                    , g1.SurveyNr
                    , g1.PlotNr
                    , g1.GardenAreaHa
                    , g1.TreeTBM
                    , g1.TreeTM
                    , g1.TreeTR
                    , g1.AverageAgeTree
                    
                    , g1.NrHighSeasonMonthsNew
                    , g1.NrLowSeasonMonthsNew
                    
                    , (
                        (30/g1.HarvestRateDaysHighSeason) * g1.NrHighSeasonMonthsNew * g1.AverageProdHighSeason
                    ) AS HighSeasonProductionNew
                    , (
                        (30/g1.HarvestRateDaysLowSeason) * g1.NrLowSeasonMonthsNew * g1.AverageProdLowSeason
                    ) AS LowSeasonProductionNew
                    , (
                        ( (30/g1.HarvestRateDaysHighSeason) * g1.NrHighSeasonMonthsNew * g1.AverageProdHighSeason ) +
                        ( (30/g1.HarvestRateDaysLowSeason) * g1.NrLowSeasonMonthsNew * g1.AverageProdLowSeason )
                    ) AS AnnualProductionNew
                    
                    , g1.HarvestRateDaysHighSeason
                    , g1.NrHighSeasonMonths
                    , g1.`AverageProdHighSeason`
                    , g1.`HighSeasonProduction`	
                    , g1.HarvestRateDaysLowSeason
                    , g1.NrLowSeasonMonths
                    , g1.`AverageProdLowSeason`
                    , g1.`LowSeasonProduction`
                    , g1.`AnnualProduction`
                FROM
                (
                SELECT
                    b.`MemberName`
                    , b.MemberDisplayID
                    , b.MemberUid
                    , a.uid
                    , a.MemberID
                    , a.SurveyNr
                    , a.PlotNr
                    , a.GardenAreaHa
                    , a.TreeTBM
                    , a.TreeTM
                    , a.TreeTR
                    , a.AverageAgeTree
                    
                    , IFNULL(CAST(a.`LeanHarvestSeasonJan` AS SIGNED),0) + 
                        IFNULL(CAST(a.`LeanHarvestSeasonFeb` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonMar` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonApr` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonMay` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonJun` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonJul` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonAug` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonSep` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonOct` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonNov` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonDec` AS SIGNED),0)
                    AS NrHighSeasonMonthsNew
                    , 12 - (IFNULL(CAST(a.`LeanHarvestSeasonJan` AS SIGNED),0) + 
                        IFNULL(CAST(a.`LeanHarvestSeasonFeb` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonMar` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonApr` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonMay` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonJun` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonJul` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonAug` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonSep` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonOct` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonNov` AS SIGNED),0) +
                        IFNULL(CAST(a.`LeanHarvestSeasonDec` AS SIGNED),0))
                    AS NrLowSeasonMonthsNew
                    
                    , a.HarvestRateDaysHighSeason
                    , a.NrHighSeasonMonths
                    , a.`AverageProdHighSeason`
                    , a.`HighSeasonProduction`
                    
                    , a.HarvestRateDaysLowSeason
                    , a.NrLowSeasonMonths
                    , a.`AverageProdLowSeason`
                    , a.`LowSeasonProduction`
                    
                    , a.`AnnualProduction`
                FROM
                    ktv_survey_plot a
                    LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
                WHERE 1=1 AND
                    ( a.LeanHarvestSeasonJan IS NOT NULL OR
                    a.LeanHarvestSeasonFeb IS NOT NULL OR
                    a.LeanHarvestSeasonMar IS NOT NULL OR
                    a.LeanHarvestSeasonApr IS NOT NULL OR
                    a.LeanHarvestSeasonMay IS NOT NULL OR
                    a.LeanHarvestSeasonJun IS NOT NULL OR
                    a.LeanHarvestSeasonJul IS NOT NULL OR
                    a.LeanHarvestSeasonAug IS NOT NULL OR
                    a.LeanHarvestSeasonSep IS NOT NULL OR
                    a.LeanHarvestSeasonOct IS NOT NULL OR
                    a.LeanHarvestSeasonNov IS NOT NULL OR
                    a.LeanHarvestSeasonDec IS NOT NULL )
                    AND b.StatusCode = 'active'
                    AND a.GardenAreaHa > 0
                    /*AND a.MemberID NOT IN (
                        SELECT
                            a.`MemberID`
                        FROM
                            ktv_survey_sdg a
                        WHERE
                            a.`StatusVerified` = 'Yes'
                        GROUP BY a.`MemberID`
                    )*/
                ) AS g1";
        $DataList = $this->db->query($sql)->result_array();

        for ($i=0; $i < count($DataList); $i++) { 
            $sql = "UPDATE ktv_survey_plot a SET
                        a.NrHighSeasonMonths = ?,
                        a.NrLowSeasonMonths = ?,
                        a.HighSeasonProduction = ?,
                        a.LowSeasonProduction = ?,
                        a.AnnualProduction = ?,
                        a.PlantationProductivity = ?
                    WHERE
                        a.`MemberID` = ?
                        AND a.`SurveyNr` = ?
                        AND a.`PlotNr` = ?
                    LIMIT 1";
            $p = array(
                $DataList[$i]['NrHighSeasonMonthsNew'],
                $DataList[$i]['NrLowSeasonMonthsNew'],
                $DataList[$i]['HighSeasonProductionNew'],
                $DataList[$i]['LowSeasonProductionNew'],
                $DataList[$i]['AnnualProductionNew'],
                ( $DataList[$i]['AnnualProductionNew'] / $DataList[$i]['GardenAreaHa'] ),
                $DataList[$i]['MemberID'],
                $DataList[$i]['SurveyNr'],
                $DataList[$i]['PlotNr']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Process Success";
        }
        return $results;
    }

    public function GenNewMemberDisplayID() {
        $this->db->trans_begin();

        $sql = "SELECT
                    a.`MemberID`
                    , a.`MemberDisplayID`
                    , a.`VillageID`
                FROM
                    ktv_members a
                WHERE 1=1
                    AND a.`VillageID` IS NOT NULL
                    AND a.`StatusCode` = 'active'";
        $DataList = $this->db->query($sql)->result_array();

        for ($i=0; $i < count($DataList); $i++) { 
            $MemberID = $DataList[$i]['MemberID'];
            $Prefix = substr($DataList[$i]['MemberDisplayID'],0,1);
            $DistrictCode = substr($DataList[$i]['VillageID'],0,4);

            //Gen Increment
            switch (strlen($MemberID)) {
                case '1':
                    $IncMemberID = "00000000" . $MemberID;
                    break;
                case '2':
                    $IncMemberID = "0000000" . $MemberID;
                    break;
                case '3':
                    $IncMemberID = "000000" . $MemberID;
                    break;
                case '4':
                    $IncMemberID = "00000" . $MemberID;
                    break;
                case '5':
                    $IncMemberID = "0000" . $MemberID;
                    break;
                case '6':
                    $IncMemberID = "000" . $MemberID;
                    break;
                case '7':
                    $IncMemberID = "00" . $MemberID;
                    break;
                case '8':
                    $IncMemberID = "0" . $MemberID;
                    break;
                default:
                    $IncMemberID = $MemberID;
                    break;
            }

            $MemberDisplayIDNew = $Prefix.$DistrictCode.$IncMemberID;

            $sql = "UPDATE ktv_members a SET
                        a.`MemberDisplayID` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
            $p = array(
                $MemberDisplayIDNew,
                $MemberID
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Process Success";
        }
        return $results;
    }

    public function CoachingSubmitV2($Act, $data_img = array(), $folder = ''){
        $Username = $Act['username'];
        $ArrFarmerIDNotProcess = array();

        //Get StaffID
        $sql = "SELECT
                    su.UserId
                    , s.`StaffID`
                FROM
                    sys_user su
                    LEFT JOIN ktv_persons p ON su.`UserId` = p.`UserID`
                    LEFT JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID`
                WHERE
                    su.`UserName` = ?
                LIMIT 1";
        $DataStaff = $this->db->query($sql, array($Username))->row_array();
        $StaffID = $DataStaff['StaffID'];
        $UserId = $DataStaff['UserId'];

        $this->db->trans_begin();

        for ($i = 0; $i < count($Act['farmer']); $i++) {
            $FarmerID = $Act['farmer'][$i]['farmer_id'];
            $IMSID = $Act['farmer'][$i]['ims_id'];

            //Cek validasi data coachingnya ==================== (Begin)
            $sql = "SELECT
                        a.CoachingID
                        , a.`UserName`
                    FROM
                        ktv_ims_farmer_coaching a
                    WHERE
                        a.`FarmerID` = ?
                        AND a.`IMSID` = ?
                    LIMIT 1";
            $p = array(
                $FarmerID, $IMSID
            );
            $CekCoach = $this->db->query($sql, $p)->row_array();

            if (isset($CekCoach['UserName'])) {
                $CoachingID = $CekCoach['CoachingID'];
                $sql = "UPDATE ktv_ims_farmer_coaching a SET
                        a.`UserID` = ?
                        , a.`UserName` = ?
                        , a.`StaffID` = ?
                        , a.LastModifiedBy = ?
                        , a.DateUpdated = NOW()
                    WHERE
                        a.`CoachingID` = ?
                    LIMIT 1";
                $p = array(
                    $UserId,
                    $Username,
                    $StaffID,
                    $UserId,
                    $CoachingID
                );
                $query = $this->db->query($sql,$p);
            } else {
                //Insert ktv_ims_farmer_coaching
                $sql = "INSERT INTO `ktv_ims_farmer_coaching` SET
                        `IMSID` = ?,
                        `FarmerID` = ?,
                        `UserID` = ?,
                        `UserName` = ?,
                        `StaffID` = ?,
                        `StatusCode` = 'active',
                        `CreatedBy` = ?,
                        `DateCreated` = NOW()";
                $p = array(
                    $IMSID, $FarmerID, $UserId, $Username, $StaffID, $UserId
                );
                $query = $this->db->query($sql, $p);
                $CoachingID = $this->db->insert_id();
            }
            //Cek validasi data coachingnya ==================== (End)
            //echo '<pre>'; print_r($CoachingID); exit;

            $ForActivity = $Act['farmer'][$i]['activity'];
            if(isset($ForActivity[0]['activity_uid'])) {
                for ($j=0; $j < count($ForActivity); $j++) {
                    /*
                     * Jika upload zip disini juga pakai ini
                    $file_photo = '';
                    $file_ttd = '';
                    if (!empty($data_img)) {
                        $sql = "SELECT
                                    a.ActivityID
                                    , a.CoachingID
                                    , a.`FarmerSigActPath`
                                    , a.PhotoActPath
                                FROM
                                    ktv_ims_farmer_coaching_activity a
                                WHERE
                                    a.`ActivityID` = ?
                                    AND a.`CoachingID` = ?
                                LIMIT 1";
                        $p = array(
                            $ForActivity[0]['activity_uid'], $CoachingID
                        );
                        $rowImg = $this->db->query($sql, $p)->row_array();
                        if (isset($data_img['coachingactivity']) && $data_img['coachingactivity'] != ''){
                            $PhotoCoaching = explode('/', $data_img['coachingactivity']);
                            $file_name = end($PhotoCoaching);
                            if (file_exists('files/tmp/' . $folder . '/' . $file_name)) {
                                $pathFile = 'files/tmp/' . $folder . '/' . $file_name;
                                //upload ke aws s3
                                $this->load->library('awsfileupload');
                                $upload = $this->awsfileupload->upload($pathFile, $file_name, AWSS3_COACHING_ACTIVITY_PATH, 'images');
                                if ($upload['success'] == true) {
                                    $file_photo = $upload['filenamepath'];
                                    if ($rowImg){
                                        if($rowImg['PhotoActPath'] != ''){
                                            $this->awsfileupload->delete($rowImg['PhotoActPath']);
                                        }
                                    }
                                }
                                //hapus foto temporary
                                delete_file($pathFile);
                            }
                        }
                        if (isset($data_img['sig']) && $data_img['sig'] != ''){
                            $PhotoSig = explode('/', $data_img['sig']);
                            $file_name = end($PhotoSig);
                            if (file_exists('files/tmp/' . $folder . '/' . $file_name)) {
                                $pathFile = 'files/tmp/' . $folder . '/' . $file_name;
                                //upload ke aws s3
                                $this->load->library('awsfileupload');
                                $upload = $this->awsfileupload->upload($pathFile, $file_name, AWSS3_COACHING_ACTIVITY_SIG_PATH, 'images');
                                if ($upload['success'] == true) {
                                    $file_ttd = $upload['filenamepath'];
                                    if ($rowImg){
                                        if($rowImg['FarmerSigActPath'] != ''){
                                            $this->awsfileupload->delete($rowImg['FarmerSigActPath']);
                                        }
                                    }
                                }
                                //hapus foto temporary
                                delete_file($pathFile);
                            }
                        }
                    }
                    */
                    
                    $FarmerInPlace = $ForActivity[$j]['coaching_recipient'] == '1' ? 'Yes' : 'No';
                    $sql = "INSERT INTO `ktv_ims_farmer_coaching_activity` (
                                `ActivityID`,
                                `CoachingID`,
                                `IMSID`,
                                `FarmerID`,
                                `CoachingRecipient`,
                                `FarmerWorkerName`,
                                `FarmerInPlace`,
                                `EventDate`,
                                `TimeStart`,
                                `TimeEnd`,
                                `Reason`,
                                `Comment`,
                                `Longitude`,
                                `Latitude`,
                                `Accuracy`,
                                `Sample_pH1`,
                                `Sample_pH2`,
                                `Sample_pH3`,
                                `StatusCode`,
                                `CreatedBy`,
                                `DateCreated`
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
                                'active',
                                ?,
                                NOW()
                            )
                            ON DUPLICATE KEY UPDATE
                                `CoachingRecipient` = ?,
                                `FarmerWorkerName` = ?,
                                `FarmerInPlace` = ?,
                                `EventDate` = ?,
                                `TimeStart` = ?,
                                `TimeEnd` = ?,
                                `Reason` = ?,
                                `Comment` = ?,
                                `Longitude` = ?,
                                `Latitude` = ?,
                                `Accuracy` = ?,
                                `Sample_pH1` = ?,
                                `Sample_pH2` = ?,
                                `Sample_pH3` = ?,
                                DateUpdated = NOW(),
                                LastModifiedBy = ?";
                    $p = array(
                        $ForActivity[$j]['activity_uid'],
                        $CoachingID,
                        $IMSID,
                        $FarmerID,
                        $ForActivity[$j]['coaching_recipient'],
                        $ForActivity[$j]['farmer_worker_name'],
                        $FarmerInPlace,
                        str_replace('/','-',$ForActivity[$j]['date']),
                        $ForActivity[$j]['time_start'],
                        $ForActivity[$j]['time_stop'],
                        (@$ForActivity[$j]['reason']) ? $ForActivity[$j]['reason'] : NULL,
                        $ForActivity[$j]['comment'],
                        $ForActivity[$j]['longitude'],
                        $ForActivity[$j]['latitude'],
                        $ForActivity[$j]['accuracy'],
                        $ForActivity[$j]['sample_1'],
                        $ForActivity[$j]['sample_2'],
                        $ForActivity[$j]['sample_3'],
                        $UserId,
                        $ForActivity[$j]['coaching_recipient'],
                        $ForActivity[$j]['farmer_worker_name'],
                        $FarmerInPlace,
                        str_replace('/','-',$ForActivity[$j]['date']),
                        $ForActivity[$j]['time_start'],
                        $ForActivity[$j]['time_stop'],
                        (@$ForActivity[$j]['reason']) ? $ForActivity[$j]['reason'] : NULL,
                        $ForActivity[$j]['comment'],
                        $ForActivity[$j]['longitude'],
                        $ForActivity[$j]['latitude'],
                        $ForActivity[$j]['accuracy'],
                        $ForActivity[$j]['sample_1'],
                        $ForActivity[$j]['sample_2'],
                        $ForActivity[$j]['sample_3'],
                        $UserId
                    );
                    $query = $this->db->query($sql, $p);
                    $ActivityID = $ForActivity[$j]['activity_uid'];

                    //Koordinat Geometry ============= (Begin)
                    if ($ForActivity[$j]['latitude'] != "" && $ForActivity[$j]['longitude'] != "") {

                        $LatitudeProses = (float) $ForActivity[$j]['latitude'];
                        $LongitudeProses = (float) $ForActivity[$j]['longitude'];
                        //Check Latitude
                        if (($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180)) {
                            //Cek valid tidak koordinatnya
                            $sql = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                            $DataCekKoordinat = $this->db->query($sql)->row_array();
                            if ($DataCekKoordinat['HasilCek'] == "1") {
                                $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";
                                $sql = "UPDATE ktv_ims_farmer_coaching_activity a SET
                                            a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                        WHERE
                                            a.`ActivityID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $ActivityID
                                );
                                $query = $this->db->query($sql, $p);
                            }
                        }
                    }
                    //Koordinat Geometry ============= (End)

                    $ForNCS = $ForActivity[$j]['task'];
                    if (isset($ForNCS[0]['activity_nc_uid'])) {
                        for ($k = 0; $k < count($ForNCS); $k++) {

                            if (($ForNCS[$k]['activity_nc_uid']) && ($ActivityID)) {

                                $sql = "INSERT INTO `ktv_ims_farmer_coaching_activity_nc` (
                                        `ActivityNCID`,
                                        `ActivityID`,
                                        `Answer`,
                                        `Topic`,
                                        `UrgentlyStatus`,
                                        `Finding`,
                                        `ActivityType`,
                                        `Recommendation`,
                                        `Target`,
                                        `Deadline`,
                                        `ActNCStatus`,
                                        `Explanation`,
                                        `StatusCode`,
                                        `CreatedBy`,
                                        `DateCreated`
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
                                        'active',
                                        ?,
                                        NOW()
                                    )
                                    ON DUPLICATE KEY UPDATE
                                        `Answer` = ?,
                                        `Topic` = ?,
                                        `UrgentlyStatus` = ?,
                                        `Finding` = ?,
                                        `ActivityType` = ?,
                                        `Recommendation` = ?,
                                        `Target` = ?,
                                        `Deadline` = ?,
                                        `ActNCStatus` = ?,
                                        `Explanation` = ?,
                                        LastModifiedBy = ?,
                                        DateUpdated = NOW()";
                                $p = array(
                                    $ForNCS[$k]['activity_nc_uid'],
                                    $ActivityID,
                                    (@$ForNCS[$k]['answer']) ? $ForNCS[$k]['answer'] : NULL,
                                    $ForNCS[$k]['topic'],
                                    $ForNCS[$k]['urgentlyStatus'],
                                    $ForNCS[$k]['finding'],
                                    $ForNCS[$k]['activityType'],
                                    $ForNCS[$k]['recomendation'],
                                    $ForNCS[$k]['target'],
                                    str_replace('/', '-', $ForNCS[$k]['deadline']),
                                    $ForNCS[$k]['status'],
                                    $ForNCS[$k]['explanation'],
                                    $UserId,
                                    (@$ForNCS[$k]['answer']) ? $ForNCS[$k]['answer'] : NULL,
                                    $ForNCS[$k]['topic'],
                                    $ForNCS[$k]['urgentlyStatus'],
                                    $ForNCS[$k]['finding'],
                                    $ForNCS[$k]['activityType'],
                                    $ForNCS[$k]['recomendation'],
                                    $ForNCS[$k]['target'],
                                    str_replace('/', '-', $ForNCS[$k]['deadline']),
                                    $ForNCS[$k]['status'],
                                    $ForNCS[$k]['explanation'],
                                    $UserId
                                );
                                $query = $this->db->query($sql, $p);
                            }
                        }
                    }
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Process Failed";
        } else {
            $this->db->trans_commit();
            $CttTambahan = "";

            if (isset($ArrFarmerIDNotProcess[0])) {
                $ImpTemp = implode(", ", $ArrFarmerIDNotProcess);
                $CttTambahan = "[List FarmerID ($ImpTemp) tidak bisa diproses, karena sudah termapping ke Username lain]";
            }

            $results['success'] = true;
            $results['message'] = "Process Success $CttTambahan";
        }
        return $results;
    }
}