<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-18 17:48:58
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtrader_mem extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    private function generateSqlHakAkses(){
        $sqlHakAkses = array();

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
        }

        return $sqlHakAkses;
    }

    public function getGridMainTrader($pSearch,$start,$limit,$sortingField,$sortingDir){
        $sqlFilter = "";
        $sqlFilterRole = "";

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
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
        }

        //filter role
        if($pSearch['roleSearch'] != ""){
            $sqlFilterRole .= " AND sub_b.MRoleID IN ({$pSearch['roleSearch']}) ";
        }else{
            $sqlFilterRole .= " AND sub_b.MRoleID IN (5,6,7,8,9,10) "; //semua role nya Agent
        }

        //advanced filter
        if($pSearch['AdvRowHandphone'] == "true"){
            $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%' ";
        }

        if($pSearch['AdvRowAge'] == "true"){
            if($pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != ""){
                $sqlFilter .= " AND (a.`DateOfBirth` IS NOT NULL AND a.`DateOfBirth` != '0000-00-00')
                                AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) " . $pSearch['AdvOpAge'] . " " . $pSearch['AdvTextAge'];
            }
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        if($sortingField == "") $sortingField = 'Name';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS Name
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , a.DateUpdated AS LastUpdated
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy`) AS Enumerator
                , e.Province
                , f.District
                , a.`DateOfBirth` AS Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                , DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '".lang('Married')."'
                    WHEN a.MaritalStatus = '2' THEN '".lang('Single')."'
                    WHEN a.MaritalStatus = '3' THEN '".lang('Janda/Duda')."'
                END AS MaritalStatus
                , GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole
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
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.`SubDistrictID` = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.`DistrictID` = f.DistrictID
                LEFT JOIN ktv_province e ON f.`ProvinceID` = e.ProvinceID
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID
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
                    case 'AdvRowHandphone':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('HandPhone').'</li>';
                    break;
                    case 'AdvRowAge':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Age').'</li>';
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

    public function getMemberBasicDataForm($MemberID){
        $sql="SELECT
                a.`MemberID` AS \"Koltiva.view.Trader.FormMainTrader-MemberID\"
                , a.`MemberDisplayID` AS \"Koltiva.view.Trader.FormMainTrader-MemberDisplayID\"
                , a.`DateCollection` AS \"Koltiva.view.Trader.FormMainTrader-DateCollection\"
                , a.`MemberName` AS \"Koltiva.view.Trader.FormMainTrader-Fullname\"
                , a.`DateOfBirth` AS \"Koltiva.view.Trader.FormMainTrader-DateOfBirth\"
                , a.`Gender` AS \"Koltiva.view.Trader.FormMainTrader-Gender\"
                , a.`Gender`
                , a.`MaritalStatus` AS \"Koltiva.view.Trader.FormMainTrader-MaritalStatus\"
                , a.`Education` AS \"Koltiva.view.Trader.FormMainTrader-Education\"
                , SUBSTR(a.`VillageID`,1,2) AS \"Province\"
                , SUBSTR(a.`VillageID`,1,4) AS \"District\"
                , SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\"
                , a.`VillageID` AS \"Village\"
                , a.`Address` AS \"Koltiva.view.Trader.FormMainTrader-Address\"
                , a.`RtRw` AS \"Koltiva.view.Trader.FormMainTrader-RtRw\"
                , a.`Handphone` AS \"Koltiva.view.Trader.FormMainTrader-Handphone\"
                #, a.`Photo` AS \"Koltiva.view.Trader.FormMainTrader-MemberPhotoOld\"
                , a.Photo AS PhotoSrc
                , a.`StatusMember` AS \"Koltiva.view.Trader.FormMainTrader-RbStatus\"
                , a.`InactiveReason` AS \"Koltiva.view.Trader.FormMainTrader-InactiveReason\"
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',') AS MemRole
                , a.Nin AS \"Koltiva.view.Trader.FormMainTrader-Nin\"
                , a.Email AS \"Koltiva.view.Trader.FormMainTrader-Email\"
                , a.Latitude AS \"Koltiva.view.Trader.FormMainTrader-Latitude\"
                , a.Longitude AS \"Koltiva.view.Trader.FormMainTrader-Longitude\"
                , a.inGroup AS \"Koltiva.view.Trader.FormMainTrader-inGroup\"
                , a.groupName AS \"Koltiva.view.Trader.FormMainTrader-groupName\"
                , a.inCoop AS \"Koltiva.view.Trader.FormMainTrader-inCoop\"
                , a.CoopName AS \"Koltiva.view.Trader.FormMainTrader-CoopName\"
                , a.inGapoktan AS \"Koltiva.view.Trader.FormMainTrader-inGapoktan\"
                , a.GapoktanName AS \"Koltiva.view.Trader.FormMainTrader-GapoktanName\"
                , a.HowManyPlantation AS \"Koltiva.view.Trader.FormMainTrader-HowManyPlantation\"
                , a.BankBeneficiary AS \"Koltiva.view.Trader.FormMainTrader-BankBeneficiary\"
                , a.BankID AS \"Koltiva.view.Trader.FormMainTrader-BankID\"
                , a.BankBranchName AS \"Koltiva.view.Trader.FormMainTrader-BankBranchName\"
                , a.BankAccNumber AS \"Koltiva.view.Trader.FormMainTrader-BankAccNumber\"
                , c.agLegalStatusCompany AS \"Koltiva.view.Trader.FormMainTrader-agLegalStatusCompany\"
                , c.agCompanyName AS \"Koltiva.view.Trader.FormMainTrader-agCompanyName\"
                , c.agYearEstablished AS \"Koltiva.view.Trader.FormMainTrader-agYearEstablished\"
                , c.agBusinessLocation AS PhotoBusinessLocation
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->row_array();

        $arrTmp = explode(",",$data['MemRole']);
        $arrSurvey = array();
        foreach ($arrTmp as $key => $value) {
            switch ($value) {
                case '5':
                    $data['Koltiva.view.Trader.FormMainTrader-CbRoleTrader'] = "1";
                    $arrSurvey[] = 5;
                break;
                case '6':
                    $data['Koltiva.view.Trader.FormMainTrader-CbRoleVilCol'] = "1";
                    $arrSurvey[] = 6;
                break;
                case '7':
                    $data['Koltiva.view.Trader.FormMainTrader-CbRoleDealer'] = "1";
                    $arrSurvey[] = 7;
                break;
                case '8':
                    $data['Koltiva.view.Trader.FormMainTrader-CbRoleRamp'] = "1";
                    $arrSurvey[] = 8;
                break;
                case '9':
                    $data['Koltiva.view.Trader.FormMainTrader-CbRoleDoHolder'] = "1";
                    $arrSurvey[] = 9;
                break;
            }
        }
        $data['ArrSurID'] = $arrSurvey;

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function insertMember($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }
        if($varPost['Koltiva_view_Trader_FormMainTrader-InactiveReason'] == "") $varPost['Koltiva_view_Trader_FormMainTrader-InactiveReason'] = null;
        //rapikan variable post (end)

        //generate MemberID dan MemberDisplayID
        $this->load->model('grower/mgrower');
        $id = $this->mgrower->genMemberID($varPost['Koltiva_view_Trader_FormMainTrader-Village'],'A');
        $uid = $this->mgrower->getUID();

        $p = array(
            $id['MemberID'],
            $uid,
            $uid,
            $id['MemberDisplayID'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Fullname'],
            $varPost['Koltiva_view_Trader_FormMainTrader-DateCollection'],
            $varPost['Koltiva_view_Trader_FormMainTrader-DateOfBirth'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Gender'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Village'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Address'],
            $varPost['Koltiva_view_Trader_FormMainTrader-RtRw'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Handphone'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Nin'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Email'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Latitude'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Longitude'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Education'],
            $_SESSION['userid']
        );
        $sql="INSERT INTO `ktv_members` SET
                `MemberID` = ?,
                MemberUID = ?,
                `uid` = ?,
                `MemberDisplayID` = ?,
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                Gender = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                `StatusMember` = 'Active',
                Nin = ?,
                Email = ?,
                Latitude = ?,
                Longitude = ?,
                Education = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $query = $this->db->query($sql,$p);

        //Member extension
        $sql="INSERT INTO `ktv_members_extension` SET
                `MemberID` = ?,
                `uid` = ?,
                agLegalStatusCompany = ?,
                agCompanyName = ?,
                agYearEstablished = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $id['MemberID'],
            $uid,
            $varPost['Koltiva_view_Trader_FormMainTrader-agLegalStatusCompany'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agCompanyName'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agYearEstablished'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);

        //insert member role ======================================================================== (begin)
        $arrRole = array();
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleTrader'] == "1") $arrRole[] = 5;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleVilCol'] == "1") $arrRole[] = 6;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleDealer'] == "1") $arrRole[] = 7;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleRamp'] == "1") $arrRole[] = 8;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleDoHolder'] == "1") $arrRole[] = 9;

        foreach ($arrRole as $key => $value) {
            $sql="INSERT INTO `ktv_member_role` SET
                `MemberID` = ?,
                `MRoleID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
            $p = array(
                $id['MemberID'],
                $value,
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
        }
        //insert member role ======================================================================== (end)

        //insert hak akses data control (Begin)
        if($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sql="INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $_SESSION['PartnerID'],
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);

            //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
            if($_SESSION['PartnerID'] != "1"){
                //insertkan ke Koltiva
                $sql="INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    '1',
                    $id['MemberID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }else{
            //insertkan ke Koltiva
            $sql="INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                '1',
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
        }
        //insert hak akses data control (End)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['MemberIDInc'] = $id['MemberID'];

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_Trader_FormMainTrader-MemberPhotoOld'] != ""){
                //get ext nya..
                $arrTemp = explode(".", $varPost['Koltiva_view_Trader_FormMainTrader-MemberPhotoOld']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = $id['MemberDisplayID'].".".$extNya;
                $ProvinceID = substr($varPost['Koltiva_view_Trader_FormMainTrader-Village'],0,2);

                //foto dipisah perdirectory ProvinceID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/trader/'.$ProvinceID)){
                    mkdir('images/trader/'.$ProvinceID, 0777, true);
                }
                $gambarTujuan = 'images/trader/'.$ProvinceID.'/'.$id['MemberDisplayID'].".".$extNya;

                rename('images/trader/'.$varPost['Koltiva_view_Trader_FormMainTrader-MemberPhotoOld'],$gambarTujuan);

                $sql="UPDATE ktv_members a SET
                        a.`Photo` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MemberID']
                );
                $query = $this->db->query($sql,$p);
            }

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_Trader_FormMainTrader-agBusinessLocationOld'] != ""){
                //get ext nya..
                $arrTemp = explode(".", $varPost['Koltiva_view_Trader_FormMainTrader-agBusinessLocationOld']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = $id['MemberDisplayID'].".".$extNya;
                $ProvinceID = substr($varPost['Koltiva_view_Trader_FormMainTrader-Village'],0,2);

                //foto dipisah perdirectory ProvinceID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/trader_business/'.$ProvinceID)){
                    mkdir('images/trader_business/'.$ProvinceID, 0777, true);
                }
                $gambarTujuan = 'images/trader_business/'.$ProvinceID.'/'.$id['MemberDisplayID'].".".$extNya;

                rename('images/trader/'.$varPost['Koltiva_view_Trader_FormMainTrader-agBusinessLocationOld'],$gambarTujuan);

                $sql="UPDATE ktv_members_extension a SET
                        a.`agBusinessLocation` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MemberID']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        return $results;
    }

    public function updateMember($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }
        if($varPost['Koltiva_view_Trader_FormMainTrader-InactiveReason'] == "") $varPost['Koltiva_view_Trader_FormMainTrader-InactiveReason'] = null;
        //rapikan variable post (end)

        //photo
        if($varPost['Koltiva_view_Trader_FormMainTrader-MemberPhotoOld'] != ""){
            $tmpGambar = $varPost['Koltiva_view_Trader_FormMainTrader-MemberPhotoOld'];
            $tmpGambar1 = substr($tmpGambar,3);
            $tmpGambar2 = explode("?",$tmpGambar1);
            $sqlPhoto = " `Photo` = '{$tmpGambar2[0]}', ";
        }else{
            $Photo = null;
            $sqlPhoto = "";
        }

        //photo business location
        if($varPost['Koltiva_view_Trader_FormMainTrader-agBusinessLocationOld'] != ""){
            $tmpGambar = $varPost['Koltiva_view_Trader_FormMainTrader-agBusinessLocationOld'];
            $tmpGambar1 = substr($tmpGambar,3);
            $tmpGambar2 = explode("?",$tmpGambar1);
            $sqlPhotoBusinessLocation = " `agBusinessLocation` = '{$tmpGambar2[0]}', ";
            $valAgBusinessLocation = $tmpGambar2[0];
        }else{
            $sqlPhotoBusinessLocation = "";
            $valAgBusinessLocation = '';
        }

        $sql="UPDATE `ktv_members` SET
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                `Gender` = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                $sqlPhoto
                Nin = ?,
                Email = ?,
                Latitude = ?,
                Longitude = ?,
                Education = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_Trader_FormMainTrader-Fullname'],
            $varPost['Koltiva_view_Trader_FormMainTrader-DateCollection'],
            $varPost['Koltiva_view_Trader_FormMainTrader-DateOfBirth'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Gender'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Village'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Address'],
            $varPost['Koltiva_view_Trader_FormMainTrader-RtRw'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Handphone'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Nin'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Email'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Latitude'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Longitude'],
            $varPost['Koltiva_view_Trader_FormMainTrader-Education'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Trader_FormMainTrader-MemberID']
        );
        $query = $this->db->query($sql,$p);

        //Member Extension
        $sql="INSERT INTO ktv_members_extension (
                MemberID,
                agLegalStatusCompany,
                agCompanyName,
                agYearEstablished,
                agBusinessLocation,
                `DateCreated`,
                `CreatedBy`
                )
            VALUES(
                ?,
                ?,
                ?,
                ?,
                '{$valAgBusinessLocation}',
                NOW(),
                ?
                )
            ON DUPLICATE KEY UPDATE
                `agLegalStatusCompany` = ?,
                `agCompanyName` = ?,
                `agYearEstablished` = ?,
                $sqlPhotoBusinessLocation
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?";
        $p = array(
            $varPost['Koltiva_view_Trader_FormMainTrader-MemberID'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agLegalStatusCompany'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agCompanyName'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agYearEstablished'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agLegalStatusCompany'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agCompanyName'],
            $varPost['Koltiva_view_Trader_FormMainTrader-agYearEstablished'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);


        //delete dl rolenya
        $sql="DELETE FROM ktv_member_role WHERE MemberID = ?";
        $p = array(
            (int) $varPost['Koltiva_view_Trader_FormMainTrader-MemberID']
        );
        $query = $this->db->query($sql,$p);

        //insert member role ======================================================================== (begin)
        $arrRole = array();
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleTrader'] == "1") $arrRole[] = 5;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleVilCol'] == "1") $arrRole[] = 6;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleDealer'] == "1") $arrRole[] = 7;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleRamp'] == "1") $arrRole[] = 8;
        if($varPost['Koltiva_view_Trader_FormMainTrader-CbRoleDoHolder'] == "1") $arrRole[] = 9;

        foreach ($arrRole as $key => $value) {
            $sql="INSERT INTO `ktv_member_role` SET
                `MemberID` = ?,
                `MRoleID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
            $p = array(
                $varPost['Koltiva_view_Trader_FormMainTrader-MemberID'],
                $value,
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
        }
        //insert member role ======================================================================== (end)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MemberIDInc'] = $varPost['Koltiva_view_Trader_FormMainTrader-MemberID'];
        }
        return $results;
    }

    public function deleteMember($MemberID){
        $sql="UPDATE `ktv_members` SET
                StatusCode = 'nullified'
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $MemberID
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

    public function getGridTraderStaff($MemberID){
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
                a.`ObjType` = 'agent'
                AND a.`ObjID` = ?
                AND a.`StatusCode` IN ('active','inactive')
                AND b.`StatusCd` IN ('active','inactive')
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['StaffID'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getGridTraderVehicle($MemberID){
        $sql="SELECT
                a.`VehID`
                , b.`BrandName`
                , CASE
                    WHEN a.VehName = '1' THEN '".lang('Truck')."'
                    WHEN a.VehName = '2' THEN '".lang('Mini Truck')."'
                    WHEN a.VehName = '3' THEN '".lang('Pick Up')."'
                    WHEN a.VehName = '4' THEN '".lang('Truck Colt Diesel')."'
                    WHEN a.VehName = '5' THEN '".lang('Dump Truck')."'
                    WHEN a.VehName = '6' THEN '".lang('Motorcycle')."'
                    WHEN a.VehName = '7' THEN '".lang('Other')."'
                END AS VehName
                , a.`VehPoliceNr`
                , CASE
                    WHEN a.VehCapacity = '1' THEN '".lang('Less than 1,000 kg')."'
                    WHEN a.VehCapacity = '2' THEN '".lang('1,000 - 3,500 kg')."'
                    WHEN a.VehCapacity = '3' THEN '".lang('3,500 - 8,500 kg')."'
                    WHEN a.VehCapacity = '4' THEN '".lang('Above 8,000 kg')."'
                END AS VehCapacity
                , IFNULL((SELECT
                    sub_b.`PersonNm`
                FROM
                    ktv_staffs sub_a
                    INNER JOIN ktv_persons sub_b ON sub_a.`PersonID` = sub_b.`PersonID`
                WHERE
                    sub_a.`StaffID` = a.`StaffID`
                LIMIT 1),'-') AS Driver
            FROM
                ktv_member_vehicle a
                LEFT JOIN ktv_ref_vehicle_brand b ON a.`BrandID` = b.`BrandID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.MemberID = ?
            ORDER BY a.`VehName` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['VehID'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getCmbBrandVehicle(){
        $sql="SELECT
                a.`BrandID` AS id
                , a.`BrandName` AS label
            FROM
                ktv_ref_vehicle_brand a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`BrandName` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getCmbTraderStaff($MemberID){
        $sql="SELECT
                a.`StaffID` AS id
                , CONCAT(b.`PersonNm`,' - ',IFNULL(rpos.PositionName,'-')) AS label
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_staff_positions f ON a.`StaffID` = f.`StaffPosStaffID`
                                    AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                                    AND f.StatusCode = 'active'
                LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
            WHERE
                a.`ObjType` = 'agent'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql,array((int) $MemberID));
        return $query->result_array();
    }

    public function getTraderVehicleFormData($VehID){
        $sql="SELECT
                a.`VehID`,
                a.`MemberID`,
                a.`StaffID`,
                a.`BrandID`,
                a.`VehName`,
                a.`VehPoliceNr`,
                a.`VehCapacity`,
                a.Remark,
                a.Ownership
            FROM
                `ktv_member_vehicle` a
            WHERE
                a.`VehID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $VehID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Trader.WinFormVehicle-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertVehicle($varPost){
        $this->db->trans_start();

        $sql="INSERT INTO `ktv_member_vehicle` SET
                `MemberID` = ?,
                `StaffID` = ?,
                `BrandID` = ?,
                `VehName` = ?,
                `VehPoliceNr` = ?,
                `VehCapacity` = ?,
                Remark = ?,
                Ownership = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $varPost['MemberID'],
            $varPost['StaffID'],
            $varPost['BrandID'],
            $varPost['VehName'],
            $varPost['VehPoliceNr'],
            $varPost['VehCapacity'],
            $varPost['Remark'],
            $varPost['Ownership'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateVehicle($varPost){
        $this->db->trans_start();

        $sql="UPDATE ktv_member_vehicle SET
                `StaffID` = ?,
                `BrandID` = ?,
                `VehName` = ?,
                `VehPoliceNr` = ?,
                `VehCapacity` = ?,
                Remark = ?,
                Ownership = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                VehID = ?
            LIMIT 1
            ";
        $p = array(
            $varPost['StaffID'],
            $varPost['BrandID'],
            $varPost['VehName'],
            $varPost['VehPoliceNr'],
            $varPost['VehCapacity'],
            $varPost['Remark'],
            $varPost['Ownership'],
            $_SESSION['userid'],
            $varPost['VehID']
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteVehicle($VehID){
        $this->db->trans_start();

        $sql="UPDATE ktv_member_vehicle SET
                StatusCode = 'nullified',
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                VehID = ?
            LIMIT 1
            ";
        $p = array(
            $_SESSION['userid'],
            $VehID
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

}
?>