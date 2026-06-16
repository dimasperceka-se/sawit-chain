<?php

use mikehaertl\wkhtmlto\Pdf;

class Mfarmer extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }

    public function getApplicantInfo($ApplicantID){
        $sql="SELECT
                a.`DisplayID`
                , a.`Fullname`
                , a.`Address`
                , a.NIN AS NoKTP
                , b.`GroupName`
                , prov.`Province`
                , dis.`District`
                , subd.`SubDistrict`
                , vil.`Village`
                , DATE_FORMAT(a.DateOfBirth,'%d %b %Y') AS DateOfBirth
                , a.Gender
                , a.PhoneNumber
            FROM
                ktv_applicant_farmers a
                LEFT JOIN ktv_cpg b ON 1=1
                    AND a.`CPGid` = b.`CPGid`
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
            WHERE
                a.`ApplicantID` = ?
            LIMIT 1";
        return $this->db->query($sql,array($ApplicantID))->row_array();
    }

    public function getFilterAdvSubdistrict($district) {
        if ($district != "") {
            $sqlDistrict = str_replace("::", ",", $district);
        } else {
            $sqlDistrict = "";
        }

        $sql = "SELECT
            SubDistrictID AS id,
            SubDistrict AS label
        FROM
            ktv_subdistrict
        WHERE
            DistrictID IN ($sqlDistrict) AND
            StatusCode = 'active'
        ORDER BY SubDistrictID ASC";

        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function getFilterAdvVillage($subdistrict) {
        $sql = "SELECT
                    VillageID AS id,
                    Village AS label
                FROM
                    ktv_village
                WHERE
                    SubDistrictID = ?
                ORDER BY VillageID ASC";
        $query = $this->db->query($sql, array($subdistrict));
        return $query->result_array();
    }

    public function readFarmersAdvanced($param) {
        //echo '<pre>'; print_r($param); exit;
        //cek paramater filter (begin)
        if ($param['parAdvNama'] == "not_set") {
            $param['parAdvNama'] = "";
        }

        if ($param['parAdvStatus'] == "not_set" || $param['parAdvStatus'] == "") {
            $sqlWhereStatus = "";
        } else {
            $sqlWhereStatus = " AND a.StatusCode = '" . $param['parAdvStatus'] . "'";
        }

        // if($param['parAdvDistrict'] == "not_set" || $param['parAdvDistrict'] == ""){
        //     //jika kab kosong maka harusnya tampilkan data sesuai hak akses distrct user tsb
        //     $rangeHakAksesDistrictId = generateHakAksesDistrictId($_SESSION['daerah'],$param['prov']);
        //     if($_SESSION['userid'] != "1"){
        //         $sqlWhereDistrict = " AND f.`DistrictID` IN (".implode(",",$rangeHakAksesDistrictId).") ";
        //     }else{
        //         $sqlWhereDistrict = "";
        //     }
        // }else{
        //     $paramTemp = str_replace("::",",",$param['parAdvDistrict']);
        //     $sqlWhereDistrict = " AND f.`DistrictID` IN ($paramTemp) ";
        // }

        if ($param['parAdvSubDistrict'] == "not_set" || $param['parAdvSubDistrict'] == "") {
            $sqlWhereSubDistrict = "";
        } else {
            $sqlWhereSubDistrict = " AND e.SubDistrictID = " . $param['parAdvSubDistrict'];
            $sqlWhereDistrict = "";
        }

        if ($param['parAdvVillage'] == "not_set" || $param['parAdvVillage'] == "") {
            $sqlWhereVillage = "";
        } else {
            $sqlWhereVillage = " AND d.VillageID = " . $param['parAdvVillage'];
            $sqlWhereDistrict = "";
        }

        if ($param['parAdvOpAge'] == "not_set" || $param['parAdvAge'] == "") {
            $sqlWhereAge = "";
        } else {
            $sqlWhereAge = "AND (a.`Birthdate` IS NOT NULL AND a.`Birthdate` != '0000-00-00')
                            AND TIMESTAMPDIFF(YEAR, a.Birthdate, CURDATE()) " . $param['parAdvOpAge'] . " " . $param['parAdvAge'];
        }

        if ($param['parAdvOpJumlahKebun'] == "not_set" || $param['parAdvJumlahKebun'] == "") {
            $sqlWhereJumlahKebun = "";
            $sqlJoinJumlahKebun = "";
        } else {
            $sqlJoinJumlahKebun = "LEFT JOIN (
                                        SELECT
                                            FarmerID,
                                            COUNT(DISTINCT GardenNr) AS jumlahGarden
                                        FROM
                                            ktv_farmer_garden
                                        WHERE
                                            StatusCode != 'nullified'
                                        GROUP BY FarmerID
                                        ORDER BY FarmerID
                                    ) AS farmerj_garden ON a.`FarmerID` = farmerj_garden.FarmerID";

            //cek apakah cari petani yg tidak punya garden
            if ($param['parAdvOpJumlahKebun'] == "=" && $param['parAdvJumlahKebun'] == "0") {
                $sqlWhereJumlahKebun = "AND farmerj_garden.jumlahGarden IS NULL";
            } else {
                $sqlWhereJumlahKebun = "AND farmerj_garden.jumlahGarden " . $param['parAdvOpJumlahKebun'] . " " . $param['parAdvJumlahKebun'];
            }
        }

        if (
                ($param['parAdvOpUkuranKebun'] != "not_set") ||
                ($param['parAdvOpProduksi'] != "not_set")
        ) {
            if ($param['parAdvSurvey'] != "" || $param['parAdvSurvey'] != "not_set") {
                $temp = explode("|", $param['parAdvSurvey']);
                $filterSurveyNr = $temp[0];

                if($filterSurveyNr != "postline"){
                    $queryParamSurvey = "= '$filterSurveyNr'";
                }else{
                    $queryParamSurvey = "!= 0";
                }

                $sqlJoinGardenSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                SUM(GardenHaUnCertified) AS totalLandSize,
                                                SUM(Production) AS totalProduction
                                            FROM
                                            (
                                                SELECT
                                                    FarmerID,
                                                    GardenNr,
                                                    SurveyNr,
                                                    GardenHaUnCertified,
                                                    SUM(
                                                        (
                                                            PanenTrekMonths * PanenTrekPanenMonth * PanenTrekKg
                                                        ) +(
                                                            PanenBiasaMonths * PanenBiasaPanenMonth * PanenBiasaKg
                                                        ) +(
                                                            PanenRayaMonths * PanenRayaPanenMonth * PanenRayaKg
                                                        )
                                                    ) AS Production
                                                FROM
                                                    ktv_farmer_garden
                                                WHERE
                                                    SurveyNr $queryParamSurvey
                                                GROUP BY FarmerID ,GardenNr
                                            ) AS temp_sum_garden_survey
                                            GROUP BY FarmerID
                                            ORDER BY FarmerID
                                        ) AS farmerg_garden_survey ON a.`FarmerID` = farmerg_garden_survey.FarmerID";
            }
        } else {
            $sqlJoinGardenSurvey = "";
        }

        //untuk ngecek kalau yg disearch cuman survey, tanpa search ukuran kebun dan produksi (begin)
        if ($param['parAdvSurvey'] != "" || $param['parAdvSurvey'] != "not_set") {
            if($sqlJoinGardenSurvey == ""){
                $temp = explode("|", $param['parAdvSurvey']);
                $filterSurveyNr = $temp[0];

                if($filterSurveyNr != "postline"){
                    $queryParamSurvey = "= '$filterSurveyNr'";
                }else{
                    $queryParamSurvey = "!= 0";
                }

                $sqlJoinGardenSurvey = "INNER JOIN (
                                            SELECT
                                                FarmerID,
                                                SUM(GardenHaUnCertified) AS totalLandSize,
                                                SUM(Production) AS totalProduction
                                            FROM
                                            (
                                                SELECT
                                                    FarmerID,
                                                    GardenNr,
                                                    SurveyNr,
                                                    GardenHaUnCertified,
                                                    SUM(
                                                        (
                                                            PanenTrekMonths * PanenTrekPanenMonth * PanenTrekKg
                                                        ) +(
                                                            PanenBiasaMonths * PanenBiasaPanenMonth * PanenBiasaKg
                                                        ) +(
                                                            PanenRayaMonths * PanenRayaPanenMonth * PanenRayaKg
                                                        )
                                                    ) AS Production
                                                FROM
                                                    ktv_farmer_garden
                                                WHERE
                                                    SurveyNr $queryParamSurvey
                                                GROUP BY FarmerID ,GardenNr
                                            ) AS temp_sum_garden_survey
                                            GROUP BY FarmerID
                                            ORDER BY FarmerID
                                        ) AS farmerg_garden_survey ON a.`FarmerID` = farmerg_garden_survey.FarmerID";

            }
        }

        //untuk ngecek kalau yg disearch cuman survey, tanpa search ukuran kebun dan produksi (end)

        if ($param['parAdvOpUkuranKebun'] == "not_set" || $param['parAdvUkuranKebun'] == "") {
            $sqlWhereUkuranKebun = "";
        } else {
            $sqlWhereUkuranKebun = " AND farmerg_garden_survey.totalLandSize " . $param['parAdvOpUkuranKebun'] . " " . $param['parAdvUkuranKebun'];
        }

        if ($param['parAdvLandCertificate'] == "not_set" || $param['parAdvLandCertificate'] == "") {
            $sqlJoinLandCert = "";
            $sqlWhereLandCert = "";
        } else {
            $paramTemp = str_replace("::", ",", $param['parAdvLandCertificate']);

            $sqlJoinLandCert = "LEFT JOIN `ktv_farmer_garden` kcfg_landsert ON a.`FarmerID` = kcfg_landsert.`FarmerID`";
            $sqlWhereLandCert = "AND kcfg_landsert.`LandCertificate` IN ($paramTemp)";
        }

        if ($param['parAdvOpProduksi'] == "not_set" || $param['parAdvProduksi'] == "") {
            $sqlWhereTotalProduksi = "";
        } else {
            $sqlWhereTotalProduksi = " AND farmerg_garden_survey.totalProduction " . $param['parAdvOpProduksi'] . " " . $param['parAdvProduksi'];
        }

        if ($param['parAdvCertified'] == "not_set" || $param['parAdvCertified'] == "") {
            $sqlWhereFarmerCertified = "";
            $sqlJoinFarmerCertified = "";
        } else {
            $sqlJoinFarmerCertified = " LEFT JOIN ktv_certification ce ON a.FarmerID = ce.FarmerID ";

            if ($param['parAdvCertified'] == "1") {
                $sqlWhereFarmerCertified = " AND (ce.ExternalDate IS NOT NULL OR ce.ExternalDate != '0000-00-00' ) ";
            } else {
                $sqlWhereFarmerCertified = " AND (ce.FarmerID IS NULL OR ce.ExternalDate IS NULL OR ce.ExternalDate = '0000-00-00' ) ";
            }

            //cek filter year
            if ($param['parAdvCertifiedYear'] != "not_set" || $param['parAdvCertifiedYear'] != "") {
                $sqlWhereFarmerCertifiedYear = " AND YEAR(ce.ExternalDate) = '" . $param['parAdvCertifiedYear'] . "' ";
            } else {
                $sqlWhereFarmerCertifiedYear = "";
            }
        }

        if ($param['parAdvNursery'] == "not_set" || $param['parAdvNursery'] == "") {
            $sqlWhereFarmerNursery = "";
            $sqlJoinFarmerNursery = "";
        } else {
            $sqlJoinFarmerNursery = "LEFT JOIN ktv_nursery nur ON a.`FarmerID` = nur.`ObjID` AND nur.`ObjType` = 'farmer'";

            if ($param['parAdvNursery'] == "1") {
                $sqlWhereFarmerNursery = " AND nur.`NurseryID` IS NOT NULL ";
            } else {
                $sqlWhereFarmerNursery = " AND nur.`NurseryID` IS NULL ";
            }
        }

        if ($param['parAdvSCE'] == "not_set" || $param['parAdvSCE'] == "") {
            $sqlWhereFarmerSCE = "";
            $sqlJoinFarmerSCE = "";
        } else {
            $sqlJoinFarmerSCE = "LEFT JOIN sce_farmer sce ON a.`FarmerID` = sce.`FarmerID`";
            if ($param['parAdvSCE'] == "1") {
                $sqlWhereFarmerSCE = " AND sce.`SceID` IS NOT NULL ";
            } else {
                $sqlWhereFarmerSCE = " AND sce.`SceID` IS NULL ";
            }
        }

        if (
                ($param['parAdvJoinGNP'] != "not_set" || $param['parAdvJoinGFP'] != "not_set")
        ) {
            $sqlJoinTraining = "LEFT JOIN ktv_cpg_batch_trainings_farmers ctrain_farmer ON a.`FarmerID` = ctrain_farmer.`FarmerID`
                                LEFT JOIN ktv_cpg_batch_trainings ctrain ON ctrain_farmer.CpgBatchTrainingID = ctrain.CpgBatchTrainingID";

            $isGNPGFPYes = false;
            $arrTmp = array();

            if ($param['parAdvJoinGNP'] == "1") {
                $arrTmp[] = 2;
                $isGNPGFPYes = true;
            } elseif ($param['parAdvJoinGNP'] == "2") {
                $sqlWhereTraining .= " AND ctrain.CPGtrainingsID != 2 ";
            }

            if ($param['parAdvJoinGFP'] == "1") {
                $arrTmp[] = 8;
                $isGNPGFPYes = true;
            } elseif ($param['parAdvJoinGFP'] == "2") {
                $sqlWhereTraining .= " AND ctrain.CPGtrainingsID != 8 ";
            }

            if ($isGNPGFPYes == true) {
                $sqlWhereTraining .= " AND ctrain.CPGtrainingsID IN (" . implode(",", $arrTmp) . ") ";
            }
        } else {
            $sqlJoinTraining = "";
            $sqlWhereTraining = "";
        }

        if ($param['parAdvBank'] != "not_set") {
            $sqlJoinBank = "LEFT JOIN (
                                SELECT
                                    FarmerID,
                                    MAX(SurveyNr),
                                    Account
                                FROM
                                    ktv_farmer_financial
                                GROUP BY FarmerID
                            ) AS farmer_bank ON a.`FarmerID` = farmer_bank.FarmerID";

            if ($param['parAdvBank'] == "1") {
                $parAdvBank = $param['parAdvBank'];
            } elseif ($param['parAdvBank'] == "2") {
                $parAdvBank = $param['parAdvBank'];
            }
            $sqlWhereBank = "AND farmer_bank.Account = '$parAdvBank'";
        } else {
            $sqlJoinBank = "";
            $sqlWhereBank = "";
        }

        if($param['parAdvJoinGAP'] == "1"){
            $sqlWhereJoinGAP = "AND a.isTrained = '1'";
        }else{
            $sqlWhereJoinGAP = "";
        }

        if ($param['no_limit'] == "yes") {
            $sqlLimit = "";
        } else {
            $sqlLimit = "LIMIT " . $param['start'] . "," . $param['limit'];
        }

        if (!empty($param['prov'])) {
            $sqlWhereProvince = "AND f.ProvinceID = {$param['prov']}";
        }
        if (!empty($param['kab'])) {
            $sqlWhereProvince = "AND f.DistrictID = {$param['kab']}";
        }
        if (!empty($param['kec'])) {
            $sqlWhereProvince = "AND e.SubDistrictID = {$param['kec']}";
        }
        if (!empty($this->user['district_access'])) {
            $sqlWhereProvince .= " AND f.DistrictID IN ({$this->user['district_access']})";
        }
        if (!empty($_SESSION['FlagAccess'] == 1)) {
            $sqlWhereProvince .= " AND a.CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$_SESSION['PartnerID']})";
        }

        //khusus untuk yg manggil dari farmer-mars.js (revision) - BEGIN
            // if($param['farSpecific'] == "mars"){
            //     $where .= " AND c.OwnerClientID = 9";
            // }
            switch ($_SESSION['ProjID']) {
                case '1':
                    $where .= "";
                break;
                default:
                    $where .= " AND c.OwnerClientID = ".$_SESSION['PartnerID'];
                break;
            }
        //khusus untuk yg manggil dari farmer-mars.js (revision) - END

        //cek paramater filter (end)

        //khusus jika survey yg dipanggil survey latest / tidak di set survey nya berarti latest
        if($param['parAdvSurvey'] == "latest" || $param['parAdvSurvey'] == "not_set"){

            if($param['parAdvSurvey'] == "latest"){
                $sqlJoinGardenSurveyOpsiJoin = 'INNER JOIN';
            }
            if($param['parAdvSurvey'] == "not_set"){
                $sqlJoinGardenSurveyOpsiJoin = 'LEFT JOIN';
            }

            $sqlJoinGardenSurvey = "$sqlJoinGardenSurveyOpsiJoin (
                                    SELECT
                                        FarmerID,
                                        SUM(GardenHaUnCertified) AS totalLandSize,
                                        SUM(Production) AS totalProduction
                                    FROM
                                    (
                                        SELECT
                                            sub_gar.FarmerID,
                                            sub_gar.GardenNr,
                                            sub_gar.SurveyNr,
                                            sub_gar.GardenHaUnCertified,
                                            SUM(
                                                (
                                                    sub_gar.PanenTrekMonths * sub_gar.PanenTrekPanenMonth * sub_gar.PanenTrekKg
                                                ) +(
                                                    sub_gar.PanenBiasaMonths * sub_gar.PanenBiasaPanenMonth * sub_gar.PanenBiasaKg
                                                ) +(
                                                    sub_gar.PanenRayaMonths * sub_gar.PanenRayaPanenMonth * sub_gar.PanenRayaKg
                                                )
                                            ) AS Production
                                        FROM
                                            ktv_farmer_garden sub_gar
                                            INNER JOIN (
                                                SELECT
                                                    lat_sur_g.`FarmerID`
                                                    , lat_sur_g.`GardenNr`
                                                    , MAX(lat_sur_g.`SurveyNr`) AS SurveyNr
                                                FROM
                                                    ktv_farmer_garden lat_sur_g
                                                GROUP BY lat_sur_g.`FarmerID`, lat_sur_g.`GardenNr`
                                            ) AS sub_gar_lat ON
                                                sub_gar.`FarmerID` = sub_gar_lat.FarmerID
                                                AND sub_gar.`GardenNr` = sub_gar_lat.GardenNr
                                                AND sub_gar.`SurveyNr` = sub_gar_lat.SurveyNr
                                        GROUP BY sub_gar.FarmerID , sub_gar.GardenNr
                                    ) AS temp_sum_garden_survey
                                    GROUP BY FarmerID
                                    ORDER BY FarmerID
                                ) AS farmerg_garden_survey ON a.`FarmerID` = farmerg_garden_survey.FarmerID";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.FarmerID AS id,
                a.OldFarmerID,
                a.FarmerName AS PersonNm,
                c.GroupName,
                d.Village AS Desa,
                e.SubDistrict AS Kecamatan,
                a.DateUpdated,
                a.DateSurvey,
                IF(a.isValidGarden='valid' && a.isValidPostHarvest='valid' && a.isValidNutrition='valid' &&
                    a.isValidPPIScore='valid','valid',IF(a.isValidGarden='invalid' || a.isValidPostHarvest='invalid' || a.isValidNutrition='invalid' ||
                    a.isValidPPIScore='invalid','invalid','new')) AS `status`
                , a.`CPGid` AS CPGid
                , g.Province
                , g.`ProvinceID` AS ProvinceID
                , f.`District`
                , f.`DistrictID` AS DistrictID
                , e.`SubDistrictID` AS SubdistrictID
                , d.`VillageID`
                , a.`Address` AS Alamat
                , a.`DateCollection`
                , CASE
                    WHEN a.Gender = '1' THEN 'Male'
                    WHEN a.Gender = '2' THEN 'Female'
                END AS Gender
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.Education = '1' THEN '".lang('No Schooling')."'
                    WHEN a.Education = '2' THEN '".lang('Primary School Incomplete')."'
                    WHEN a.Education = '3' THEN '".lang('Primary School Completed')."'
                    WHEN a.Education = '4' THEN '".lang('Tamat SMP')."'
                    WHEN a.Education = '5' THEN '".lang('Tamat SMA')."'
                    WHEN a.Education = '6' THEN '".lang('Tertiary Degree')."'
                END AS Education
                , a.Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.Birthdate) / 365.25) AS Age
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '".lang('Menikah')."'
                    WHEN a.MaritalStatus = '2' THEN '".lang('Single')."'
                    WHEN a.MaritalStatus = '3' THEN '".lang('Janda/Duda')."'
                END AS MaritalStatus
                , a.Photo
                , IFNULL(farmerg_garden_survey.totalLandSize,'0') AS TotalLandSizeGarden
                , IFNULL(farmerg_garden_survey.totalProduction,'0') AS TotalProductionGarden
            FROM
                ktv_farmer a
                LEFT JOIN ktv_cpg c ON a.CPGid = c.CPGid
                LEFT JOIN ktv_village d ON a.VillageID = d.VillageID
                LEFT JOIN ktv_subdistrict e ON d.SubDistrictID = e.SubDistrictID
                LEFT JOIN ktv_district f ON e.DistrictID = f.DistrictID
                LEFT JOIN ktv_province g ON g.ProvinceID = f.ProvinceID
                $sqlJoinBank
                $sqlJoinFarmerSCE
                $sqlJoinFarmerNursery
                $sqlJoinFarmerCertified
                $sqlJoinJumlahKebun
                $sqlJoinGardenSurvey
                $sqlJoinLandCert
                $sqlJoinTraining
            WHERE
                a.StatusCode != 'nullified'
                $sqlWhereStatus
                $sqlWhereProvince
                $sqlWhereDistrict
                $sqlWhereSubDistrict
                AND (FarmerName LIKE ? OR a.FarmerID LIKE ? OR a.OldFarmerID LIKE ?)
                $sqlWhereDistrict
                $sqlWhereSubDistrict
                $sqlWhereVillage
                $sqlWhereBank
                $sqlWhereAge
                $sqlWhereJumlahKebun
                $sqlWhereUkuranKebun
                $sqlWhereLandCert
                $sqlWhereTotalProduksi
                $sqlWhereFarmerCertified
                $sqlWhereFarmerCertifiedYear
                $sqlWhereFarmerNursery
                $sqlWhereTraining
                $sqlWhereFarmerSCE
                $sqlWhereJoinGAP
                AND
                IF(isValidGarden='valid' && isValidPostHarvest='valid' && isValidNutrition='valid' &&
                isValidPPIScore='valid','valid',IF(isValidGarden='invalid' || isValidPostHarvest='invalid' ||
                isValidNutrition='invalid' || isValidPPIScore='invalid','invalid','new')) like '%%'
            GROUP BY a.`FarmerID`
            ORDER BY a.DateUpdated DESC,FarmerName ASC
            $sqlLimit";
        $p = array(
            '%' . $param['parAdvNama'] . '%', $param['parAdvNama'], $param['parAdvNama']
        );
        $query = $this->db->query($sql, $p);

        //cek foto tersedia tidak
        $dataList = $query->result_array();
        for ($i=0; $i < count($dataList); $i++) {
            if(file_exists('images/Photo/'.$dataList[$i]['Photo'])){
                $dataList[$i]['PhotoAvailable'] = lang('Ya');
            }else{
                $dataList[$i]['PhotoAvailable'] = lang('Tidak');
            }
        }
        $result['data'] = $dataList;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function readFarmers($farSpecific,$sert, $prov, $kab, $kec, $key, $userId, $partnerId, $flagAccess, $status, $sort, $start, $limit) {
        $sertq = null;
        if ($status == 'All') {
            $status = '%%';
        }
        $status = '%%';

        $where = '';
        if (!empty($prov)) {
            $where .= " AND f.ProvinceID = {$prov}";
        }
        if (!empty($kab)) {
            $where .= " AND f.DistrictID = {$kab}";
        }
        if (!empty($kec)) {
            $where .= " AND e.SubDistrictID = {$kec}";
        }
        if (!empty($this->user['district_access'])) {
            $where .= " AND f.DistrictID IN ({$this->user['district_access']})";
        }
        if (!empty($_SESSION['FlagAccess'])) {
            $where .= " AND a.CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$_SESSION['PartnerID']})";
        }

        //khusus untuk yg manggil dari farmer-mars.js (revision) - BEGIN
            // if($param['farSpecific'] == "mars"){
            //     $where .= " AND c.OwnerClientID = 9";
            // }
            // switch ($_SESSION['ProjID']) {
            //     case '1':
            //         $where .= "";
            //     break;
            //     default:
            //         $where .= " AND c.OwnerClientID = ".$_SESSION['PartnerID'];
            //     break;
            // }
        //khusus untuk yg manggil dari farmer-mars.js (revision) - END

        if ($sert == 'true') {
            $sertq = ' and ce.FarmerID is not null ';
        }

        $sort = json_decode($sort);
        $order = ($sort[0]->property == '' ? 'a.DateUpdated desc,FarmerName' : $sort[0]->property);
        $by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                a.FarmerID as id, a.OldFarmerID,FarmerName as PersonNm,GroupName,d.Village as Desa,
                SubDistrict as Kecamatan,
                a.DateUpdated,a.DateSurvey,IF(isValidGarden='valid' && isValidPostHarvest='valid' && isValidNutrition='valid' &&
                isValidPPIScore='valid','valid',IF(isValidGarden='invalid' || isValidPostHarvest='invalid' || isValidNutrition='invalid' ||
                isValidPPIScore='invalid','invalid','new')) as status
                , a.`CPGid` AS CPGid
                , g.Province
                , g.`ProvinceID` AS ProvinceID
                , f.`District`
                , f.`DistrictID` AS DistrictID
                , e.`SubDistrictID` AS SubdistrictID
                , d.`VillageID`
                , a.`Address` AS Alamat
                , a.`DateCollection`
                , CASE
                    WHEN a.Gender = '1' THEN 'Male'
                    WHEN a.Gender = '2' THEN 'Female'
                END AS Gender
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.Education = '1' THEN '".lang('No Schooling')."'
                    WHEN a.Education = '2' THEN '".lang('Primary School Incomplete')."'
                    WHEN a.Education = '3' THEN '".lang('Primary School Completed')."'
                    WHEN a.Education = '4' THEN '".lang('Tamat SMP')."'
                    WHEN a.Education = '5' THEN '".lang('Tamat SMA')."'
                    WHEN a.Education = '6' THEN '".lang('Tertiary Degree')."'
                END AS Education
                , a.Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.Birthdate) / 365.25) AS Age
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '".lang('Menikah')."'
                    WHEN a.MaritalStatus = '2' THEN '".lang('Single')."'
                    WHEN a.MaritalStatus = '3' THEN '".lang('Janda/Duda')."'
                END AS MaritalStatus
                , a.Photo
                , IFNULL(tbl_gar_sur_lat.totalLandSize,'0') AS TotalLandSizeGarden
                , IFNULL(tbl_gar_sur_lat.totalProduction,'0') AS TotalProductionGarden
            from ktv_farmer a
            left join ktv_cpg c on a.CPGid=c.CPGid
            left join ktv_village d on a.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on g.ProvinceID=f.ProvinceID
            LEFT JOIN (SELECT FarmerID FROM ktv_certification GROUP BY FarmerID) ce ON a.FarmerID=ce.FarmerID
            LEFT JOIN (
                SELECT
                    FarmerID,
                    SUM(GardenHaUnCertified) AS totalLandSize,
                    SUM(Production) AS totalProduction
                FROM
                (
                    SELECT
                        sub_gar.FarmerID,
                        sub_gar.GardenNr,
                        sub_gar.SurveyNr,
                        sub_gar.GardenHaUnCertified,
                        SUM(
                            (
                                sub_gar.PanenTrekMonths * sub_gar.PanenTrekPanenMonth * sub_gar.PanenTrekKg
                            ) +(
                                sub_gar.PanenBiasaMonths * sub_gar.PanenBiasaPanenMonth * sub_gar.PanenBiasaKg
                            ) +(
                                sub_gar.PanenRayaMonths * sub_gar.PanenRayaPanenMonth * sub_gar.PanenRayaKg
                            )
                        ) AS Production
                    FROM
                        ktv_farmer_garden sub_gar
                        INNER JOIN (
                            SELECT
                                lat_sur_g.`FarmerID`
                                , lat_sur_g.`GardenNr`
                                , MAX(lat_sur_g.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_farmer_garden lat_sur_g
                            GROUP BY lat_sur_g.`FarmerID`, lat_sur_g.`GardenNr`
                        ) AS sub_gar_lat ON
                            sub_gar.`FarmerID` = sub_gar_lat.FarmerID
                            AND sub_gar.`GardenNr` = sub_gar_lat.GardenNr
                            AND sub_gar.`SurveyNr` = sub_gar_lat.SurveyNr
                    GROUP BY sub_gar.FarmerID , sub_gar.GardenNr
                ) AS temp_sum_garden_survey
                GROUP BY FarmerID
                ORDER BY FarmerID
            ) AS tbl_gar_sur_lat ON a.FarmerID = tbl_gar_sur_lat.FarmerID
            $joinPerCpg
            WHERE
                a.StatusCode != 'nullified'
                %s
               AND (FarmerName like ? OR a.FarmerID like ? OR a.OldFarmerID like ?) and
               IF(isValidGarden='valid' && isValidPostHarvest='valid' && isValidNutrition='valid' &&
               isValidPPIScore='valid','valid',IF(isValidGarden='invalid' || isValidPostHarvest='invalid' ||
               isValidNutrition='invalid' || isValidPPIScore='invalid','invalid','new')) like ? $sertq
            %s";
        $query = $this->db->query(sprintf($sql, $where, 'ORDER BY ' . $order . ' ' . $by . ' LIMIT ?,?'), array("%$key%", $key, $key, $status, (int) $start, (int) $limit));
        $queryTotal = $this->db->query('SELECT FOUND_ROWS() AS total');

        //cek foto tersedia tidak
        $dataList = $query->result_array();
        for ($i=0; $i < count($dataList); $i++) {
            if(file_exists('images/Photo/'.$dataList[$i]['Photo'])){
                $dataList[$i]['PhotoAvailable'] = lang('Ya');
            }else{
                $dataList[$i]['PhotoAvailable'] = lang('Tidak');
            }
        }

        $result['data'] = $dataList;
        $result['total'] = $queryTotal->row()->total;


        return $result;
    }

    public function getPhotoFarmerById($FarmerID) {
        $sql = "SELECT `Photo` FROM ktv_farmer WHERE FarmerID = ? LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID));
        $result = $query->result_array();
        return $result[0]['Photo'];
    }

    public function readProvinsiNama($prov) {
        $sql = "
            select Province
            from ktv_province
            WHERE ProvinceID=?";
        $query = $this->db->query($sql, array($prov));
        $result = $query->result_array();
        return $result[0]['Province'];
    }

    public function readFarmer($id) {
        $sql = "SELECT
            a.*, SUBSTR(a.CPGid,1,4) AS DistrictID,d.Village as Desa,e.SubDistrict as Kecamatan, f.District as Kabupaten,g.Province as Provinsi,
               a.Address as alamat,FarmerName as PersonNm, d.VillageID as RegionalCd,DATE_FORMAT(Birthdate,'%d - %b - %Y') as BirthDttm,DATE_FORMAT(Birthdate,'%Y') as BirthDttmYear,a.MaritalStatus as MaritalSt,a.DateCollection,a.DateUpdated,a.CPGid as FarmerGroupID,i.Established nEstablished, StatusFarmer,
               IF(COUNT(cert.FarmerID) > 0,1,0) AS certified,
               IF(SceID,1,0) AS is_trader,
               c.CPGid, c.GroupName
               ,IF(j.FarmerID=a.FarmerID, 1, 0) AS LearningContract, IF(a.LearningContractStatus IS NULL, 0, a.LearningContractStatus) as LCStatus,
               IF(a.LearningContractFile IS NULL, 0, 1) as LCFile
               ,IF(j.FarmerID=a.FarmerID, 1, 0) AS CertContract, IF(a.CertContractStatus IS NULL, 0, a.CertContractStatus) as CCStatus,
               IF(a.CertContractFile IS NULL, 0, 1) as CCFile
               ,i.NurseryID,i.Panjang,i.Lebar,i.Kapasitas,i.Latitude,i.Longitude,i.LatitudeDeg1,i.LatitudeDeg2,i.LatitudeDeg3,i.LongitudeDeg1,i.LongitudeDeg2,i.LongitudeDeg3,
               kcoop.CoopName,
               ps_coop.`CoopName` AS CoopName_Staff
            from ktv_farmer a
            left join ktv_cpg c on a.CPGid=c.CPGid
            left join ktv_village d on a.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            left join ktv_compost h on a.FarmerID=h.ObjID and h.ObjType='farmer'
            left join ktv_nursery i on a.FarmerID=i.ObjID and i.ObjType='farmer'
            LEFT JOIN `ktv_certification` cert ON cert.`FarmerID` = a.`FarmerID`
            LEFT JOIN `sce_farmer` ON sce_farmer.`FarmerID` = a.`FarmerID`
            LEFT JOIN ktv_cpg_batch_trainings_farmers j ON a.FarmerID = j.FarmerID
            LEFT JOIN coop_member cm ON a.FarmerID = cm.farmerID
            LEFT JOIN ktv_cooperatives kcoop ON cm.CoopID = kcoop.CoopID

            LEFT JOIN ktv_person_farmers ps_f ON a.`FarmerID` = ps_f.`FarmerID`
            LEFT JOIN ktv_staffs ps_staff ON ps_f.`PersonID` = ps_staff.`PersonID` AND ps_staff.`ObjType` = 'cooperative'
            LEFT JOIN ktv_cooperatives ps_coop ON ps_staff.`ObjID` = ps_coop.`CoopID`

            WHERE a.FarmerID=?
            GROUP BY a.`FarmerID`";
        $query = $this->db->query($sql, array($id));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result = $query->result_array();

        //cek koperasi farmer
        if ($result[0]['CoopName'] != "") {
            $result[0]['CoopName_display'] = $result[0]['CoopName'];
        } else {
            $result[0]['CoopName_display'] = $result[0]['CoopName_Staff'];
        }

        return $result[0];
    }

    public function readFarmerNursery($id) {
        $sql = "SELECT
               i.NurseryID,i.Panjang,i.Lebar,i.Kapasitas,i.Latitude,i.Longitude,i.LatitudeDeg1,i.LatitudeDeg2,i.LatitudeDeg3,i.LongitudeDeg1,i.LongitudeDeg2,i.LongitudeDeg3
               ,i.Established AS nEstablished, i.CertificationStatus, i.DateCertification
            from ktv_farmer a
            left join ktv_nursery i on a.FarmerID=i.ObjID and i.ObjType='farmer'
            WHERE a.FarmerID=?
            GROUP BY a.`FarmerID`";
        $query = $this->db->query($sql, array($id));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result = $query->result_array();
        return $result[0];
    }

    public function readFarmerGarden($FarmerID, $GardenNr) {
        $sql = "
            select Latitude,Longitude,LatDeg,LatMin,LatSec,LongDeg,LongMin,LongSec,GardenHaUnCertified,GardenDistance
            from ktv_farmer_garden
            where FarmerID=? and GardenNr=?
            order by SurveyNr desc
            limit 1";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr));
        $result = $query->result_array();
        return $result[0];
    }

    public function readDataBank($id) {
        $sql = "SELECT AccountNumber,AccountHolderFarmer,AccountBankName,AccountBankBranch
            FROM ktv_farmer_financial
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    public function readSurvey($id) {
        $sql = "
            SELECT SurveyNr as id, concat(SurveyNr,' - ',SurveyTxt) as surveya
            FROM ktv_survey
            WHERE SurveyNr=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    public function readFarmerHarvest($id, $nr) {
        $sql = "
            select a.*,FarmerName as PersonNm, DATE_FORMAT(a.DateCollection,'%Y-%m-%d') as DateCollection,DATE_FORMAT(a.DateCollection,'%d - %b - %Y') as DateCollection2
            from ktv_farmer_post_harvest a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql, array($id, $nr));
        $result = $query->result_array();
        return $result[0];
    }

    public function readFarmerSavingPilot($id, $nr) {
        $sql = "
            select a.*,FarmerName as PersonNm, DATE_FORMAT(a.InterviewDate,'%Y-%m-%d') as DateCollection,DATE_FORMAT(a.InterviewDate,'%d - %b - %Y') as DateCollection2
            from ktv_saving_pilot a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql, array($id, $nr));
        $result = $query->result_array();
        return $result[0];
    }

    public function readFarmerCertification($FarmerID, $surveyNr) {
        $query = $this->db->get_where('ktv_certification', compact('FarmerID', 'SurveyNr'), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function _generateFarmerID($district) {
        $sql = "
            SELECT IFNULL(IF(length(max(FarmerId))!=9,concat(substr(FarmerId,1,4),LPAD(substr(max(FarmerId)+1,5,5),5,'0')),max(FarmerID)+1),
               concat(?,'00001')) as id
            FROM ktv_farmer
            WHERE substr(FarmerId,1,4)=substr(?,1,4)";
        $query = $this->db->query($sql, array($district, $district));
        $result = $query->result_array();
        return $result[0]['id'];
    }

    public function readGarden($id, $garden, $survey, $lite = '') {
        $sql = "
SELECT a.*,
       c.*,
       ca.*,
       z.*,
       a.FarmerID,
       FarmerName AS PersonNm,
       DATE_FORMAT(c.DateCollection,'%d - %b - %Y') AS DateCollection2,
       c.LatDeg,
       c.LatMin,
       c.LatSec,
       c.LongDeg,
       c.LongMin,
       LongSec,
       d.Year,
       d.Certification,
       d.CertificationHolderJenis,
       d.CandidateSelection,
       d.ExternalDate,
       d.DateRevisionAudit,
       d.CommentAudit,
       d.RecommendationAudit,
       d.InspectorID,
       d.StatusAudit,
       b.StaffName AS InspektorName,
       d.CertificationHolder AS CertificationHolderID,
       e.label AS CertificationHolderName,
       c.GardenNr,
       c.SurveyNr,

        c.`ParticipateChildEducation`,
        c.`CutWageForDisciplinary`,
        c.`DoCutWageForWorker`,
        c.`WagePaidByPerformance`,
        c.`PayingWorkerWageByPerformance`,
        c.`HandlingFirstAidInGarden`,
        c.`FirstAidKitLocation`,
        c.`WorkerNotHandlePesticide`,
        c.`WorkerAccessSafeDrinkingWater`,
        c.`BufferZoneGarden`,
        c.`LandOpeningForest`,
        c.`LandOpeningForestCertificate`,
        c.`IdentifyProtectRareSpecies`
FROM ktv_farmer a
LEFT JOIN ktv_farmer_garden c ON a.FarmerID=c.FarmerID
LEFT JOIN ktv_certification d ON a.FarmerID=d.FarmerID AND c.GardenNr=d.GardenNr AND c.SurveyNr=d.SurveyNr
LEFT JOIN (
    SELECT
        al.*
    FROM ktv_certification_audit_log al
    JOIN (
    SELECT
        al.FarmerID, al.GardenNr, al.SurveyNr, MAX(al.ICSDate) AS ICSDate
    FROM ktv_certification_audit_log al
    GROUP BY al.FarmerID, al.GardenNr, al.SurveyNr
    ) az ON al.FarmerID = az.FarmerID AND al.GardenNr = az.GardenNr AND al.SurveyNr = az.SurveyNr
) ca ON a.FarmerID=ca.FarmerID AND c.GardenNr=ca.GardenNr AND c.SurveyNr=ca.SurveyNr
LEFT JOIN ktv_certification_signature z ON a.FarmerID=z.FarmerID AND c.GardenNr=z.GardenNr AND c.SurveyNr=z.SurveyNr AND z.ICSDate=d.ICSDate
LEFT JOIN
    (SELECT ExtensionID AS staffid,
            StaffName
     FROM ktv_extension_staff
     UNION ALL SELECT b.PersonID,
                      PersonNm
     FROM ktv_program_staff a
     JOIN ktv_persons b ON a.PersonID = b.PersonID) b ON d.InspectorID = b.staffid
LEFT JOIN
    (SELECT *
     FROM
         (SELECT concat('',WarehouseID) id,
                 concat('',WarehouseName) label,
                 VillageID village
          FROM ktv_warehouse
          UNION ALL SELECT concat('',TraderID) id,
                           concat('',Company) label,
                           VillageID village
          FROM ktv_traders
          UNION ALL SELECT concat('',CoopID) id,
                           concat('',CoopName) label,
                           VillageID village
          FROM ktv_cooperatives) a) e ON d.CertificationHolder = e.id
WHERE a.FarmerID=?
    AND c.GardenNr=?
    AND c.SurveyNr=? LIMIT 1";
        $query = $this->db->query($sql, array($id, $garden, $survey));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result = $query->result_array();
        if ($lite == '') {
            return $result[0];
        } else {
            return array('success' => true, 'data' => $result[0]);
        }
    }

    //FarmerID`,`GardenNr`,`SurveyNr`,`Certification
    public function readGardenForCetak($id, $survey) {
        $sql = "
            select *,a.FarmerID,FarmerName as PersonNm, DATE_FORMAT(c.DateCollection,'%d - %b - %Y') as DateCollection,
            c.LatDeg, c.LatMin, c.LatSec, c.LongDeg, c.LongMin, LongSec
            from ktv_farmer a
            left join ktv_farmer_garden c on a.FarmerID=c.FarmerID and c.SurveyNr=?
            WHERE a.FarmerID=?";
        $query = $this->db->query($sql, array($survey, $id));
        $result = $query->result_array();
        return $result;
    }

    //FarmerID`,`GardenNr`,`SurveyNr`,`Certification
    public function readGardenForCetakLatest($id, $garden, $survey) {
        $sql = "SELECT *,a.FarmerID,FarmerName as PersonNm, DATE_FORMAT(c.DateCollection,'%d - %b - %Y') as DateCollection,
            c.LatDeg, c.LatMin, c.LatSec, c.LongDeg, c.LongMin, LongSec,
            (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Production
            from ktv_farmer a
            left join ktv_farmer_garden c on a.FarmerID=c.FarmerID and c.SurveyNr=? AND c.GardenNr = ?
            WHERE a.FarmerID=?";
        $query = $this->db->query($sql, array($survey, $garden, $id));
        $result = $query->result_array();
        return $result;
    }

    public function updateGarden($FarmerID, $GardenNr, $DateCollection, $Latitude, $LatMinTmp, $LatSecTmp, $Longitude, $LongMinTmp, $LongSecTmp, $Elevation, $OwnershipCocoa, $TahunTanamanCocoa, $GardenDistance, $GardenHaUnCertified, $ProductionTmp, $PanenBiasaMonths, $PanenBiasaPanenMonth, $PanenBiasaKg, $PanenTrekMonths, $PanenTrekPanenMonth, $PanenTrekKg, $PanenRayaMonths, $PanenRayaPanenMonth, $PanenRayaKg, $TimeHarvestBiasa, $TimeHarvestTrek, $TimeHarvestRaya, $LandOwner, $LandCertificate, $PohonTBM, $PohonTM, $PohonRehab, $GraftedTrees, $ReplantedTrees, $RoadCondition, $Comment, $TSH858, $RCC70, $RCC71, $RCC72, $RCC73, $Lokal, $S1, $S2, $S3, $ICRRI3, $ICRRI4, $ICRRI5, $CloneLain, $Gamal, $Kelapa, $Durian, $Pinang, $Karet, $JackFruit, $Lamtoro, $Mahoni, $Pisang, $Rambutan, $Mangga, $Langsat, $ShadeLain, $ShadeTreesNr, $TimeHarvest, $HarvestAwal, $HarvestMasak, $HarvestHama, $PruningPlants, $FrequentPruning, $HighPruning, $PruningProtectPlants, $FrequentPruningProtect, $CleanSkin, $HowToCleanSkin, $OrganicKotoran, $OrganicResidu, $OrganicMembeli, $TidakMemakaiOrganic, $Urea, $TSP, $NPK, $KCL, $TidakMemakaiKimia, $FrequentFertilizationOrganic, $DoseFertilizerOrganic, $FrequentFertilizationKimia, $DoseFertilizerKimia, $PakaiKompos, $FrequentFertilizationKompos, $DoseFertilizerKompos, $FrUrea, $FrTsp, $FrNpk, $FrKcl, $DpUrea, $DoTsp, $DoNpk, $DoKcl, $FrLain, $DoLain, $FrZa, $DoZa, $KimiaDana, $KimiaSupplier, $KimiaDilatih, $KimiaTidakSuka, $KimiaTidakTersedia, $KimiaLain, $HamaBPK, $HamaHelopeltis, $HamaBatang, $PenyakitKanker, $PenyakitBusuk, $PenyakitUpas, $PenyakitAkar, $PenyakitVSD, $PenyakitAntraknose, $Herbisida, $MerekHerbisida, $FrequentHerbisida, $DoseHerbisida, $Herbisida1, $Herbisida2, $Herbisida3, $Herbisida4, $Herbisida5, $Herbisida6, $Herbisida7, $Herbisida8, $Herbisida9, $Herbisida10, $Herbisida11, $Herbisida12, $Herbisida13, $Herbisida14, $Herbisida15, $Herbisida16, $Herbisida17, $Herbisida18, $Herbisida19, $Herbisida20, $Herbisida21, $Herbisida22, $Herbisida23, $Herbisida24, $Herbisida25, $Herbisida26, $Herbisida27, $Herbisida28, $Herbisida29, $Insectisida, $MerekInsectisida, $FrequentInsectisida, $DoseInsectisida, $Insectisida1, $Insectisida2, $Insectisida3, $Insectisida4, $Insectisida5, $Insectisida6, $Insectisida7, $Insectisida8, $Insectisida9, $Insectisida10, $Insectisida11, $Insectisida12, $Insectisida13, $Insectisida14, $Insectisida15, $Insectisida16, $Insectisida17, $Insectisida18, $Insectisida19, $Insectisida20, $Insectisida21, $Insectisida22, $Insectisida23, $Fungisida, $MerekFungisida, $FrequentFungisida, $DoseFungisida, $Fungisida1, $Fungisida2, $Fungisida3, $Fungisida4, $Fungisida5, $Fungisida6, $Fungisida7, $Fungisida8, $Fungisida9, $Fungisida10, $Fungisida11, $Fungisida12, $Fungisida13, $APD, $TempatSimpanPestisida, $BuangKemasanPestisida, $TopGraftedTrees, $BeanGraftedTrees, $GraftedTreesTahun, $TopGraftedTreesTahun, $BeanGraftedTreesTahun, $ReplantedTreesTahun, $M01, $M06Temp, $THR, $RCL, $J45, $SurveyNr, $LatDeg, $LatMin, $LatSec, $LongDeg, $LongMin, $LongSec, $userid, $FrKomposKandang, $FrKomposCair, $FrKomposGranula, $DoKomposKandang, $DoKomposCair, $DoKomposGranula, $kTBM, $kTM, $kTR, $pTBM, $pTM, $pTR, $TSH858Nr, $RCC70Nr, $RCC71Nr, $RCC72Nr, $RCC73Nr, $LokalNr, $S1Nr, $S2Nr, $S3Nr, $ICRRI3Nr, $ICRRI4Nr, $ICRRI5Nr, $M01Nr, $M06NrTemp, $THRNr, $RCLNr, $J45Nr, $CloneLainNr, $Cengkeh, $Sawit, $Aren, $Pala, $Kemiri, $Alpukat, $Sukun, $Pepaya, $Manggis, $Jeruk, $Jati, $Biti, $Uru, $Jabon, $Petai, $Jengkol, $KelapaNr, $PinangNr, $KaretNr, $JackFruitNr, $PisangNr, $RambutanNr, $ManggaNr, $LangsatNr, $DurianNr, $MahoniNr, $GamalNr, $LamtoroNr, $CengkehNr, $SawitNr, $ArenNr, $PalaNr, $KemiriNr, $AlpukatNr, $SukunNr, $PepayaNr, $ManggisNr, $JerukNr, $JatiNr, $BitiNr, $UruNr, $JabonNr, $PetaiNr, $JengkolNr, $ShadeLainNr, $jambuMente, $Kapok, $Jambu, $Kedondong, $Cempedak, $Sengon, $jambuMenteNr, $KapokNr, $JambuNr, $KedondongNr, $CempedakNr, $SengonNr,
    //$isCertification,
            $Year, $CandidateSelection, $ICSDate, $ExternalDate, $Certification, $CertificationHolderJenis, $CertificationHolderID, $StatusAudit, $DateRevisionAudit, $CommentAudit, $RecommendationAudit, $RACertQuestion1, $RACertQuestion2, $RACertQuestion3, $RACertQuestion4, $RACertQuestion5, $RACertQuestion6, $RACertQuestion7, $RACertQuestion8, $RACertQuestion9, $RACertQuestion10, $RACertQuestion11, $RACertQuestion12, $RACertQuestion13, $RACertQuestion14A, $RACertQuestion14B, $RACertQuestion14C, $RACertQuestion14D, $RACertQuestion15, $RACertQuestion16, $RACertQuestion17, $RACertQuestion18, $RACertQuestion19, $RACertQuestion20, $RACertQuestion21, $RACertQuestion22, $RACertQuestion23A, $RACertQuestion23B, $RACertQuestion23C, $RACertQuestion23D, $RACertQuestion23DText, $RACertQuestion23E, $RehabTrees, $RehabTreesTahun, $InsetTrees, $InsetTreesTahun, $FrFoliar, $DoFoliar, $FarmerSignature, $InspectorSignature, $AuditCommiteeSignature, $CertificationStart, $CertificationEnd, $AP, $APNr, $PR, $PRNr, $Scavina, $ScavinaNr, $MT, $MTNr, $M02, $M02Nr, $M04, $M04Nr, $M06, $M06Nr, $MHP03, $MHP03Nr, $MHP04, $MHP04Nr, $BB01, $BB01Nr, $BLB, $BLBNr, $BRT, $BRTNr, $ShadeTreesIncProductivity, $ShadeTreesExtraIncome, $ShadeTreesProtectSoil, $ShadeTreesReducePests, $ShadeTreesReduceHeat, $ShadeTreesIncLandValue, $ShadeTreesAddFirewood, $ShadeTreesAddFodder, $ShadeTreesDoNotKnow, $ShadeTreesOthers, $ShadeTreesSpreadEvently, $ShadeTreesObtainSeeds, $Nuts, $Tubers, $Patchouli, $CoverCropOthers, $NoCoverCrop, $ObtainSeedsToday, $SeedsFreeFromPests, $SeedsFillRoutineMaintenance, $AfterCertSaveRecordOriginSeeds, $Production, $ProductionNext, $SalesLastyear, $HowToDealOrganicAnorganicWaste, $PruningOptStructure, $FrequentPruningOptStructure, $HeightPruningOptStructure, $PruningBudInfected, $FrequentPruningBudInfected, $HeightPruningBudInfected, $PruningNotProductive, $FrequentPruningNotProductive, $HeightPruningNotProductive, $DisinfectedTools, $AvailableOrganicFertilizer, $RoutineWatchSoilFertility, $ImprovePlantFixNitrogenInSoil, $ImproveApplyPracticeAgroforestry, $ImproveFertilizingWithOrganic, $ImproveFertilizingWithAnorganic, $ImproveMakeBiopori, $ImprovePlantingShadeTrees, $ImproveUseCoverCrop, $ImproveTerracing, $ImproveDoNothing, $RoutineMonitorPestInGarden, $UseChemicalPesticideDosage, $ApplyAltNonChemicalControlPests, $UseOrganicControlPests, $UseChemicalLowestToxicity, $UseChemicalLastChoice, $ApplyRotationStrategy, $NoticeUseInorganicFertilizer, $TrainedUseProperly, $MixPesticideLiquidFertilizer, $ExcessPesticideDisposedSafely, $GiveNoEntrySignAfterSpraying, $AdherePreHarvestInterval, $EquipmentGoodCondition, $StoreAccordanceOnLabel, $StoreOriginalPackaging, $StoreIndicationSuitablePlants, $StoreAvoidPossibleSpill, $StoreSecuredPlace, $StoreFarFromProducts, $HandlingCleanDry, $HandlingEnoughVentilationLight, $HandlingStructurallySafe, $HandlingAntiAbsorptive, $HandlingLeakproofedFloor, $HandlingFireproofMaterial, $HandlingCollectSpillage, $HandlingClearWarningSign, $HandlingFirstAidInfo, $HandlingProcedureEmergency, $HandlingAreaCleanEye, $HandlingAccommodateLiquidStored, $UsePesticideInorganicFertilizer, $ParticipateChildEducation, $CutWageForDisciplinary, $DoCutWageForWorker, $WagePaidByPerformance, $PayingWorkerWageByPerformance, $HandlingFirstAidInGarden, $FirstAidKitLocation, $WorkerNotHandlePesticide, $WorkerAccessSafeDrinkingWater, $BufferZoneGarden, $LandOpeningForest, $LandOpeningForestCertificate, $IdentifyProtectRareSpecies
    , $FrDolomiteLime, $FrCocoaSpecific, $DoDolomiteLime, $DoCocoaSpecific, $c04,$c04Nr,$c07,$c07Nr,$BB,$BBNr,$ShadeTreesReason,$ObtainSeedsTodayNr
    , $FrPengapuran, $DosePengapuran, $FrFertiliaKakao,$DoFertiliaKakao,$FrNitrabor,$DoNitrabor,$Insectisida24
    ) {

        $sql_cek = "SELECT FarmerID FROM ktv_farmer_garden WHERE FarmerID=? and GardenNr=? and SurveyNr=?";
        $query = $this->db->query($sql_cek, array($FarmerID, $GardenNr, $SurveyNr));
        $result = $query->result_array();
        if ($result[0]['FarmerID'] == '') {
            $this->addSurveyGarden($FarmerID, $GardenNr, $SurveyNr, $userid);
        } else {
            $add = "LastModifiedBy=$userid,DateUpdated=now(),";
        }

        $sql = "UPDATE ktv_farmer_garden
            SET GardenNr=?,DateCollection=?,Latitude=?,LatMin=?,LatSec=?,Longitude=?,LongMin=?,LongSec=?,Elevation=?,
            OwnershipCocoa=?,TahunTanamanCocoa=?,GardenDistance=?,GardenHaUnCertified=?,Production=?,PanenBiasaMonths=?,
            PanenBiasaPanenMonth=?,PanenBiasaKg=?,PanenTrekMonths=?,PanenTrekPanenMonth=?,PanenTrekKg=?,PanenRayaMonths=?,
            PanenRayaPanenMonth=?,PanenRayaKg=?,TimeHarvestBiasa=?,TimeHarvestTrek=?,TimeHarvestRaya=?,LandOwner=?,LandCertificate=?,
            PohonTBM=?,PohonTM=?,PohonRehab=?,GraftedTrees=?,ReplantedTrees=?,RoadCondition=?,Comment=?,TSH858=?,RCC70=?,
            RCC71=?,RCC72=?,RCC73=?,Hybrid=?,S1=?,S2=?,S3=?,ICRRI3=?,ICRRI4=?,ICRRI5=?,CloneLain=?,Gamal=?,Kelapa=?,Durian=?,
            Pinang=?,Karet=?,JackFruit=?,Lamtoro=?,Mahoni=?,Pisang=?,Rambutan=?,Mangga=?,Langsat=?,ShadeLain=?,ShadeTreesNr=?,
            TimeHarvest=?,HarvestAwal=?,HarvestMasak=?,HarvestHama=?,PruningPlants=?,FrequentPruning=?,HighPruning=?,
            PruningProtectPlants=?,FrequentPruningProtect=?,CleanSkin=?,HowToCleanSkin=?,OrganicKotoran=?,OrganicResidu=?,
            OrganicMembeli=?,TidakMemakaiOrganic=?,Urea=?,TSP=?,NPK=?,KCL=?,TidakMemakaiKimia=?,FrequentFertilizationOrganic=?,
            DoseFertilizerOrganic=?,FrequentFertilizationKimia=?,DoseFertilizerKimia=?,PakaiKompos=?,FrequentFertilizationKompos=?,
            DoseFertilizerKompos=?,FrUrea=?,FrTsp=?,FrNpk=?,FrKcl=?,DoUrea=?,DoTsp=?,DoNpk=?,DoKcl=?,FrLain=?,DoLain=?,
            FrZa=?,DoZa=?,KimiaDana=?,KimiaSupplier=?,
            KimiaDilatih=?,KimiaTidakSuka=?,KimiaTidakTersedia=?,KimiaLain=?,HamaBPK=?,HamaHelopeltis=?,HamaBatang=?,PenyakitKanker=?,PenyakitBusuk=?,PenyakitUpas=?,
            PenyakitAkar=?,PenyakitVSD=?,PenyakitAntraknose=?,Herbisida=?,MerekHerbisida=?,FrequentHerbisida=?,DoseHerbisida=?,
            Herbisida1=?,Herbisida2=?,Herbisida3=?,Herbisida4=?,Herbisida5=?,Herbisida6=?,Herbisida7=?,Herbisida8=?,Herbisida9=?,
            Herbisida10=?,Herbisida11=?,Herbisida12=?,Herbisida13=?,
            Herbisida14 = ?, Herbisida15 = ?, Herbisida16 = ?, Herbisida17 = ?, Herbisida18 = ?, Herbisida19 = ?, Herbisida20 = ?, Herbisida21 = ?, Herbisida22 = ?, Herbisida23 = ?, Herbisida24 = ?,
            Herbisida25 = ?, Herbisida26 = ?, Herbisida27 = ?, Herbisida28 = ?, Herbisida29 = ?,
            Insectisida=?,MerekInsectisida=?,FrequentInsectisida=?,DoseInsectisida=?,Insectisida1=?,Insectisida2=?,
            Insectisida3=?,Insectisida4=?,Insectisida5=?,Insectisida6=?,Insectisida7=?,Insectisida8=?,Insectisida9=?,Insectisida10=?,
            Insectisida11=?,
            Insectisida12 = ?, Insectisida13 = ?, Insectisida14 = ?, Insectisida15 = ?, Insectisida16 = ?, Insectisida17 = ?, Insectisida18 = ?, Insectisida19 = ?, Insectisida20 = ?,
            Insectisida21 = ?, Insectisida22 = ?, Insectisida23 = ?,
            Fungisida=?,MerekFungisida=?,FrequentFungisida=?,DoseFungisida=?,Fungisida1=?,Fungisida2=?,Fungisida3=?,Fungisida4=?,
            Fungisida5=?,Fungisida6=?,Fungisida7=?,Fungisida8=?,Fungisida9=?,Fungisida10=?,Fungisida11=?,
            Fungisida12 = ?, Fungisida13 = ?,
            APD=?,TempatSimpanPestisida=?,
            BuangKemasanPestisida=?,
            TopGraftedTrees=?,BeanGraftedTrees=?,GraftedTreesTahun=?,TopGraftedTreesTahun=?,BeanGraftedTreesTahun=?,ReplantedTreesTahun=?,
            M01=?,M06=?,THR=?,RCL=?,J45=?,LatDeg=?,LatMin=?,LatSec=?,LongDeg=?,LongMin=?,LongSec=?,
            $add
            FrKomposKandang=?,FrKomposCair=?,FrKomposGranula=?,DoseKomposKandang=?,DoseKomposCair=?,DoseKomposGranula=?,
            KomposTBM=?,KomposTM=?,KomposTR=?,PupukTBM=?,PupukTM=?,PupukTR=?,
            TSH858Nr=?,RCC70Nr=?,RCC71Nr=?, RCC72Nr=?, RCC73Nr=?, LokalNr=?, S1Nr=?, S2Nr=?, S3Nr=?, ICRRI3Nr=?,
            ICRRI4Nr=?, ICRRI5Nr=?, M01Nr=?, M06Nr=?, THRNr=?, RCLNr=?, J45Nr=?,
            AP=?,APNr=?,PR =?,PRNr=?, Scavina=?,ScavinaNr=?,MT=?,MTNr=?,M02=?,M02Nr=?,M04=?,M04Nr=?,M06=?,M06Nr=?,MHP03=?,MHP03Nr=?,MHP04=?,MHP04Nr=?,BB01=?,BB01Nr=?,BLB=?,BLBNr=?,BRT=?,BRTNr=?,
            CloneLainNr=?,
            Cengkeh=?,Sawit=?,Aren=?,Pala=?,Kemiri=?,Alpukat=?,Sukun=?,Pepaya=?,Manggis=?,Jeruk=?,Jati=?,Biti=?,Uru=?,
            Jabon=?,Petai=?,Jengkol=?,KelapaNr=?,PinangNr=?,KaretNr=?,JackFruitNr=?,PisangNr=?,RambutanNr=?,ManggaNr=?,
            LangsatNr=?,DurianNr=?,MahoniNr=?,GamalNr=?,LamtoroNr=?,CengkehNr=?,SawitNr=?,ArenNr=?,PalaNr=?,KemiriNr=?,
            AlpukatNr=?,SukunNr=?,PepayaNr=?,ManggisNr=?,JerukNr=?,JatiNr=?,BitiNr=?,UruNr=?,JabonNr=?,PetaiNr=?,JengkolNr=?,
            ShadeLainNr=?,
            jambuMente=?,Kapok=?,Jambu=?,Kedondong=?,Cempedak=?,Sengon=?,jambuMenteNr=?,KapokNr=?,JambuNr=?,KedondongNr=?,CempedakNr=?,SengonNr=?,
            RehabTrees=?,RehabTreesTahun=?,InsetTrees=?,InsetTreesTahun=?,FrFoliar=?,DoFoliar=?,
            ShadeTreesIncProductivity = ?, ShadeTreesExtraIncome = ?, ShadeTreesProtectSoil = ?, ShadeTreesReducePests = ?, ShadeTreesReduceHeat = ?, ShadeTreesIncLandValue = ?, ShadeTreesAddFirewood = ?, ShadeTreesAddFodder = ?, ShadeTreesDoNotKnow = ?, ShadeTreesOthers = ?, ShadeTreesSpreadEvently = ?, ShadeTreesObtainSeeds = ?, Nuts = ?, Tubers = ?, Patchouli = ?, CoverCropOthers = ?, NoCoverCrop = ?, ObtainSeedsToday = ?, SeedsFreeFromPests = ?, SeedsFillRoutineMaintenance = ?, AfterCertSaveRecordOriginSeeds = ?, Production = ?, ProductionNext = ?, SalesLastyear = ?, HowToDealOrganicAnorganicWaste = ?, PruningOptStructure = ?, FrequentPruningOptStructure = ?, HeightPruningOptStructure = ?, PruningBudInfected = ?, FrequentPruningBudInfected = ?, HeightPruningBudInfected = ?, PruningNotProductive = ?, FrequentPruningNotProductive = ?, HeightPruningNotProductive = ?, DisinfectedTools = ?, AvailableOrganicFertilizer = ?, RoutineWatchSoilFertility = ?, ImprovePlantFixNitrogenInSoil = ?, ImproveApplyPracticeAgroforestry = ?, ImproveFertilizingWithOrganic = ?, ImproveFertilizingWithAnorganic = ?, ImproveMakeBiopori = ?, ImprovePlantingShadeTrees = ?, ImproveUseCoverCrop = ?, ImproveTerracing = ?, ImproveDoNothing = ?, RoutineMonitorPestInGarden = ?, UseChemicalPesticideDosage = ?, ApplyAltNonChemicalControlPests = ?, UseOrganicControlPests = ?, UseChemicalLowestToxicity = ?, UseChemicalLastChoice = ?, ApplyRotationStrategy = ?, NoticeUseInorganicFertilizer = ?, TrainedUseProperly = ?, MixPesticideLiquidFertilizer = ?, ExcessPesticideDisposedSafely = ?, GiveNoEntrySignAfterSpraying = ?, AdherePreHarvestInterval = ?, EquipmentGoodCondition = ?, StoreAccordanceOnLabel = ?, StoreOriginalPackaging = ?, StoreIndicationSuitablePlants = ?, StoreAvoidPossibleSpill = ?, StoreSecuredPlace = ?, StoreFarFromProducts = ?, HandlingCleanDry = ?, HandlingEnoughVentilationLight = ?, HandlingStructurallySafe = ?, HandlingAntiAbsorptive = ?, HandlingLeakproofedFloor = ?, HandlingFireproofMaterial = ?, HandlingCollectSpillage = ?, HandlingClearWarningSign = ?, HandlingFirstAidInfo = ?, HandlingProcedureEmergency = ?, HandlingAreaCleanEye = ?, HandlingAccommodateLiquidStored = ?, UsePesticideInorganicFertilizer = ?
            , FrDolomiteLime = ?, FrCocoaSpecific = ?, DoDolomiteLime = ?, DoCocoaSpecific = ?,`c04`=?,`c04Nr`=?,`c07`=?,`c07Nr`=?,BB=?,BBNr=?,ShadeTreesReason=?,ObtainSeedsTodayNr=?,
            FrPengapuran=?, DosePengapuran=?,FrFertiliaKakao=?,DoFertiliaKakao=?,FrNitrabor=?,DoNitrabor=?,Insectisida24=?,
            ParticipateChildEducation=?, CutWageForDisciplinary=?, DoCutWageForWorker=?, WagePaidByPerformance=?, PayingWorkerWageByPerformance=?, HandlingFirstAidInGarden=?, FirstAidKitLocation=?, WorkerNotHandlePesticide=?, WorkerAccessSafeDrinkingWater=?, BufferZoneGarden=?, LandOpeningForest=?, LandOpeningForestCertificate=?, IdentifyProtectRareSpecies=?
         WHERE FarmerID=? and GardenNr=? and SurveyNr=?";
        $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        $query = $this->db->query($sql_date_survey, array($FarmerID));

        $query = $this->db->query($sql, array($GardenNr, $DateCollection, $Latitude, $LatMin, $LatSec, $Longitude, $LongMin, $LongSec,
            $Elevation, $OwnershipCocoa, $TahunTanamanCocoa, $GardenDistance, $GardenHaUnCertified, $Production, $PanenBiasaMonths,
            $PanenBiasaPanenMonth, $PanenBiasaKg, $PanenTrekMonths, $PanenTrekPanenMonth, $PanenTrekKg, $PanenRayaMonths,
            $PanenRayaPanenMonth, $PanenRayaKg, $TimeHarvestBiasa, $TimeHarvestTrek, $TimeHarvestRaya, $LandOwner, $LandCertificate, $PohonTBM,
            $PohonTM, $PohonRehab, $GraftedTrees, $ReplantedTrees, $RoadCondition, $Comment, $TSH858, $RCC70, $RCC71, $RCC72, $RCC73,
            $Lokal, $S1, $S2, $S3, $ICRRI3, $ICRRI4, $ICRRI5, $CloneLain, $Gamal, $Kelapa, $Durian, $Pinang, $Karet, $JackFruit, $Lamtoro, $Mahoni,
            $Pisang, $Rambutan, $Mangga, $Langsat, $ShadeLain, $ShadeTreesNr, $TimeHarvest, $HarvestAwal, $HarvestMasak, $HarvestHama,
            $PruningPlants, $FrequentPruning, $HighPruning, $PruningProtectPlants, $FrequentPruningProtect, $CleanSkin, $HowToCleanSkin,
            $OrganicKotoran, $OrganicResidu, $OrganicMembeli, $TidakMemakaiOrganic, $Urea, $TSP, $NPK, $KCL, $TidakMemakaiKimia,
            $FrequentFertilizationOrganic, $DoseFertilizerOrganic, $FrequentFertilizationKimia, $DoseFertilizerKimia, $PakaiKompos,
            $FrequentFertilizationKompos, $DoseFertilizerKompos, $FrUrea, $FrTsp, $FrNpk, $FrKcl, $DpUrea, $DoTsp, $DoNpk, $DoKcl, $FrLain, $DoLain,
            $FrZa, $DoZa,
            $KimiaDana, $KimiaSupplier, $KimiaDilatih, $KimiaTidakSuka, $KimiaTidakTersedia, $KimiaLain, $HamaBPK, $HamaHelopeltis, $HamaBatang, $PenyakitKanker, $PenyakitBusuk,
            $PenyakitUpas, $PenyakitAkar, $PenyakitVSD, $PenyakitAntraknose, $Herbisida, $MerekHerbisida, $FrequentHerbisida,
            $DoseHerbisida, $Herbisida1, $Herbisida2, $Herbisida3, $Herbisida4, $Herbisida5, $Herbisida6, $Herbisida7, $Herbisida8,
            $Herbisida9, $Herbisida10, $Herbisida11, $Herbisida12, $Herbisida13,
            $Herbisida14, $Herbisida15, $Herbisida16, $Herbisida17, $Herbisida18, $Herbisida19, $Herbisida20, $Herbisida21, $Herbisida22, $Herbisida23, $Herbisida24,
            $Herbisida25, $Herbisida26, $Herbisida27, $Herbisida28, $Herbisida29,
            $Insectisida, $MerekInsectisida, $FrequentInsectisida, $DoseInsectisida, $Insectisida1,
            $Insectisida2, $Insectisida3, $Insectisida4, $Insectisida5, $Insectisida6, $Insectisida7, $Insectisida8, $Insectisida9,
            $Insectisida10, $Insectisida11,
            $Insectisida12, $Insectisida13, $Insectisida14, $Insectisida15, $Insectisida16, $Insectisida17, $Insectisida18, $Insectisida19, $Insectisida20,
            $Insectisida21, $Insectisida22, $Insectisida23,
            $Fungisida, $MerekFungisida, $FrequentFungisida, $DoseFungisida, $Fungisida1, $Fungisida2, $Fungisida3,
            $Fungisida4, $Fungisida5, $Fungisida6, $Fungisida7, $Fungisida8, $Fungisida9, $Fungisida10, $Fungisida11,
            $Fungisida12, $Fungisida13,
            $APD, $TempatSimpanPestisida,
            $BuangKemasanPestisida,
            $TopGraftedTrees,$BeanGraftedTrees, $GraftedTreesTahun, $TopGraftedTreesTahun, $BeanGraftedTreesTahun, $ReplantedTreesTahun,
            $M01, $M06, $THR, $RCL, $J45, $LatDeg, $LatMin, $LatSec, $LongDeg, $LongMin, $LongSec,
            $FrKomposKandang, $FrKomposCair, $FrKomposGranula, $DoKomposKandang, $DoKomposCair, $DoKomposGranula,
            $kTBM, $kTM, $kTR, $pTBM, $pTM, $pTR,
            $TSH858Nr, $RCC70Nr, $RCC71Nr, $RCC72Nr, $RCC73Nr, $LokalNr, $S1Nr, $S2Nr, $S3Nr, $ICRRI3Nr, $ICRRI4Nr, $ICRRI5Nr,
            $M01Nr, $M06Nr, $THRNr, $RCLNr, $J45Nr,
            $AP, $APNr, $PR, $PRNr, $Scavina, $ScavinaNr, $MT, $MTNr, $M02, $M02Nr, $M04, $M04Nr, $M06, $M06Nr, $MHP03, $MHP03Nr, $MHP04, $MHP04Nr, $BB01, $BB01Nr, $BLB, $BLBNr, $BRT, $BRTNr,
            $CloneLainNr,
            $Cengkeh, $Sawit, $Aren, $Pala, $Kemiri, $Alpukat, $Sukun, $Pepaya, $Manggis, $Jeruk, $Jati, $Biti, $Uru, $Jabon, $Petai,
            $Jengkol, $KelapaNr, $PinangNr, $KaretNr, $JackFruitNr, $PisangNr, $RambutanNr, $ManggaNr, $LangsatNr, $DurianNr,
            $MahoniNr, $GamalNr, $LamtoroNr, $CengkehNr, $SawitNr, $ArenNr, $PalaNr, $KemiriNr, $AlpukatNr, $SukunNr, $PepayaNr,
            $ManggisNr, $JerukNr, $JatiNr, $BitiNr, $UruNr, $JabonNr, $PetaiNr, $JengkolNr, $ShadeLainNr,
            $jambuMente, $Kapok, $Jambu, $Kedondong, $Cempedak, $Sengon, $jambuMenteNr, $KapokNr, $JambuNr, $KedondongNr, $CempedakNr, $SengonNr,
            $RehabTrees, $RehabTreesTahun, $InsetTrees, $InsetTreesTahun, $FrFoliar, $DoFoliar,
            $ShadeTreesIncProductivity, $ShadeTreesExtraIncome, $ShadeTreesProtectSoil, $ShadeTreesReducePests, $ShadeTreesReduceHeat, $ShadeTreesIncLandValue, $ShadeTreesAddFirewood, $ShadeTreesAddFodder, $ShadeTreesDoNotKnow, $ShadeTreesOthers, $ShadeTreesSpreadEvently, $ShadeTreesObtainSeeds, $Nuts, $Tubers, $Patchouli, $CoverCropOthers, $NoCoverCrop, $ObtainSeedsToday, $SeedsFreeFromPests, $SeedsFillRoutineMaintenance, $AfterCertSaveRecordOriginSeeds, $Production, $ProductionNext, $SalesLastyear, $HowToDealOrganicAnorganicWaste, $PruningOptStructure, $FrequentPruningOptStructure, $HeightPruningOptStructure, $PruningBudInfected, $FrequentPruningBudInfected, $HeightPruningBudInfected, $PruningNotProductive, $FrequentPruningNotProductive, $HeightPruningNotProductive, $DisinfectedTools, $AvailableOrganicFertilizer, $RoutineWatchSoilFertility, $ImprovePlantFixNitrogenInSoil, $ImproveApplyPracticeAgroforestry, $ImproveFertilizingWithOrganic, $ImproveFertilizingWithAnorganic, $ImproveMakeBiopori, $ImprovePlantingShadeTrees, $ImproveUseCoverCrop, $ImproveTerracing, $ImproveDoNothing, $RoutineMonitorPestInGarden, $UseChemicalPesticideDosage, $ApplyAltNonChemicalControlPests, $UseOrganicControlPests, $UseChemicalLowestToxicity, $UseChemicalLastChoice, $ApplyRotationStrategy, $NoticeUseInorganicFertilizer, $TrainedUseProperly, $MixPesticideLiquidFertilizer, $ExcessPesticideDisposedSafely, $GiveNoEntrySignAfterSpraying, $AdherePreHarvestInterval, $EquipmentGoodCondition, $StoreAccordanceOnLabel, $StoreOriginalPackaging, $StoreIndicationSuitablePlants, $StoreAvoidPossibleSpill, $StoreSecuredPlace, $StoreFarFromProducts, $HandlingCleanDry, $HandlingEnoughVentilationLight, $HandlingStructurallySafe, $HandlingAntiAbsorptive, $HandlingLeakproofedFloor, $HandlingFireproofMaterial, $HandlingCollectSpillage, $HandlingClearWarningSign, $HandlingFirstAidInfo, $HandlingProcedureEmergency, $HandlingAreaCleanEye, $HandlingAccommodateLiquidStored, $UsePesticideInorganicFertilizer,
            $FrDolomiteLime, $FrCocoaSpecific, $DoDolomiteLime, $DoCocoaSpecific,$c04,$c04Nr,$c07,$c07Nr,$BB,$BBNr,$ShadeTreesReason,$ObtainSeedsTodayNr,$FrPengapuran,$DosePengapuran,
            $FrFertiliaKakao,$DoFertiliaKakao,$FrNitrabor,$DoNitrabor,$Insectisida24,
            $ParticipateChildEducation, $CutWageForDisciplinary, $DoCutWageForWorker, $WagePaidByPerformance, $PayingWorkerWageByPerformance, $HandlingFirstAidInGarden, $FirstAidKitLocation, $WorkerNotHandlePesticide, $WorkerAccessSafeDrinkingWater, $BufferZoneGarden, $LandOpeningForest, $LandOpeningForestCertificate, $IdentifyProtectRareSpecies,
            $FarmerID, $GardenNr, $SurveyNr));

        if ($CandidateSelection != '' || $CandidateSelection != null) {
            $snr = explode(" - ", $SurveyNr);

            $arrwer = array('FarmerID' => $FarmerID, 'GardenNr' => $GardenNr, 'SurveyNr' => $snr[0]);
            $qcek = $this->db->get_where('ktv_certification', $arrwer);

            $holder = explode("|", $CertificationHolder);
            $d = array(
                'FarmerID' => $FarmerID,
                'GardenNr' => $GardenNr,
                'SurveyNr' => $snr[0],
                'Certification' => $Certification,
                'CertificationHolderJenis' => $CertificationHolderJenis,
                //'CertificationHolder' => $holder[1],
                'CertificationHolder' => $CertificationHolderID,
                'Year' => $Year,
                'CandidateSelection' => $CandidateSelection,
                'ICSDate' => $ICSDate,
                'ExternalDate' => $ExternalDate,
                'DateRevisionAudit' => $DateRevisionAudit,
                'CommentAudit' => $CommentAudit,
                'RecommendationAudit' => $RecommendationAudit,
                //'InpectorID' int(10) DEFAULT NULL,
                'StatusAudit' => $StatusAudit,
                'RACertQuestion1' => $RACertQuestion1,
                'RACertQuestion2' => $RACertQuestion2,
                'RACertQuestion3' => $RACertQuestion3,
                'RACertQuestion4' => $RACertQuestion4,
                'RACertQuestion5' => $RACertQuestion5,
                'RACertQuestion6' => $RACertQuestion6,
                'RACertQuestion7' => $RACertQuestion7,
                'RACertQuestion8' => $RACertQuestion8,
                'RACertQuestion9' => $RACertQuestion9,
                'RACertQuestion10' => $RACertQuestion10,
                'RACertQuestion11' => $RACertQuestion11,
                'RACertQuestion12' => $RACertQuestion12,
                'RACertQuestion13' => $RACertQuestion13,
                'RACertQuestion14A' => $RACertQuestion14A,
                'RACertQuestion14B' => $RACertQuestion14B,
                'RACertQuestion14C' => $RACertQuestion14C,
                'RACertQuestion14D' => $RACertQuestion14D,
                'RACertQuestion15' => $RACertQuestion15,
                'RACertQuestion16' => $RACertQuestion16,
                'RACertQuestion17' => $RACertQuestion17,
                'RACertQuestion18' => $RACertQuestion18,
                'RACertQuestion19' => $RACertQuestion19,
                'RACertQuestion20' => $RACertQuestion20,
                'RACertQuestion21' => $RACertQuestion21,
                'RACertQuestion22' => $RACertQuestion22,
                'RACertQuestion23A' => $RACertQuestion23A,
                'RACertQuestion23B' => $RACertQuestion23B,
                'RACertQuestion23C' => $RACertQuestion23C,
                'RACertQuestion23D' => $RACertQuestion23D,
                'RACertQuestion23DText' => $RACertQuestion23DText,
                'RACertQuestion23E' => $RACertQuestion23E,
                'CertificationStart' => $CertificationStart,
                'CertificationEnd' => $CertificationEnd,
                'ParticipateChildEducation' => $ParticipateChildEducation,
                'CutWageForDisciplinary' => $CutWageForDisciplinary,
                'DoCutWageForWorker' => $DoCutWageForWorker,
                'WagePaidByPerformance' => $WagePaidByPerformance,
                'PayingWorkerWageByPerformance' => $PayingWorkerWageByPerformance,
                'HandlingFirstAidInGarden' => $HandlingFirstAidInGarden,
                'FirstAidKitLocation' => $FirstAidKitLocation,
                'WorkerNotHandlePesticide' => $WorkerNotHandlePesticide,
                'WorkerAccessSafeDrinkingWater' => $WorkerAccessSafeDrinkingWater,
                'BufferZoneGarden' => $BufferZoneGarden,
                'LandOpeningForest' => $LandOpeningForest,
                'LandOpeningForestCertificate' => $LandOpeningForestCertificate,
                'IdentifyProtectRareSpecies' => $IdentifyProtectRareSpecies,
            );

            if ($qcek->num_rows() > 0) {
                $d['DateUpdated'] = date('Y-m-d H:m:s');
                $d['LastModifiedBy'] = $userid;
                $this->db->where($arrwer);
                $this->db->update('ktv_certification', $d);
            } else {
                $d['DateCreated'] = date('Y-m-d H:m:s');
                $d['CreatedBy'] = $userid;
                $this->db->insert('ktv_certification', $d);
            }
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        }

//         $FarmerSignature,$InspectorSignature,$AuditCommiteeSignature
        if (false) {
            $snr = explode(" - ", $SurveyNr);
            $arrwer = array('FarmerID' => $FarmerID, 'GardenNr' => $GardenNr, 'SurveyNr' => $snr[0]);
            $qcek = $this->db->get_where('ktv_certification_signature', $arrwer);

            $holder = explode("|", $CertificationHolder);
            $d = array(
                'FarmerID' => $FarmerID,
                'GardenNr' => $GardenNr,
                'SurveyNr' => $snr[0],
                'ICSDate' => $ICSDate,
                'Certification' => $Certification,
                'FarmerSignature' => $FarmerSignature,
                'InspectorSignature' => $InspectorSignature,
                'AuditCommiteeSignature' => $AuditCommiteeSignature,
            );

            if ($qcek->num_rows() > 0) {
                $d['DateUpdated'] = date('Y-m-d H:m:s');
                $d['LastModifiedBy'] = $userid;
                $this->db->where($arrwer);
                $this->db->update('ktv_certification_signature', $d);
            } else {
                $d['DateCreated'] = date('Y-m-d H:m:s');
                $d['CreatedBy'] = $userid;
                $this->db->insert('ktv_certification_signature', $d);
            }
        }

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        // exit('exit');
        return $results;
    }

    public function readFarmerKeluargas($id, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                FamilyID
                , FarmerID
                , AnggotaName
                , HubunganKeluarga
                , WorkGardenStatus
                , ActivityType
                , TotalWorkingHrsPerDay
                , TotalWorkingHrs
                , WageAmount
                , AnggotaAge AS DateOfBirth
                , (YEAR(CURDATE()) - AnggotaAge) AS AnggotaAge
                , YEAR(DateOfBirth) AS DateOfBirthRaw
                , (YEAR(CURDATE()) - YEAR(DateOfBirth)) AS AnggotaAgeRaw
                , AnggotaGender
                , StatusSekolah
            from ktv_family
            WHERE FarmerID=?
            AND ((FamilyStatus <> 'inactive') or (FamilyStatus is null))
            ORDER BY AnggotaName %s";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array($id, (int) $start, (int) $limit));

        // $query = $this->db->query(sprintf($sql, 'FamilyID, FarmerID, AnggotaName, HubunganKeluarga, AnggotaAge,
        //     AnggotaGender, StatusSekolah,IF(HubunganKeluarga="1","' . lang('Suami/Istri') . '",IF(HubunganKeluarga="2","' . lang('Anak') . '",
        //     IF(HubunganKeluarga="3","' . lang('Dll') . '",""))) as hubungan,IF(AnggotaGender="1","' . lang('Laki-laki') . '",IF(AnggotaGender="2",
        //     "' . lang('Female') . '","")) as kelamin,IF(StatusSekolah="1","' . lang('Ya') . '",IF(StatusSekolah="2","' . lang('Tidak') . '","")) as sekolah', 'LIMIT ?,?'),
        //     array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        // $query           = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($id));
        //petakan data tahun lahir
        for ($i = 0; $i < count($result['data']); $i++) {
            if ($result['data'][$i]['DateOfBirthRaw'] != "" && $result['data'][$i]['DateOfBirthRaw'] != "0") {
                $result['data'][$i]['DateOfBirth'] = $result['data'][$i]['DateOfBirthRaw'];
                $result['data'][$i]['AnggotaAge'] = $result['data'][$i]['AnggotaAgeRaw'];
            }
        }

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function createFarmer($Ssn, $PersonNm, $BirthDttm, $BirthPlace, $gambar, $Gender, $Address, $RegionalCd, $ZipCd, $Email, $BloodT, $MaritalSt, $Education, $NationalityNm, $Handphone, $FarmerGroupID, $LahanKosong, $Muge, $ActiveMemberCooperation, $KeyFarmer, $DemoPlot, $OtherTraining, $CPGmembership, $OtherTrainingSiapa, $OtherTrainingTahun, $OtherTrainingLama, $DemoPlotLama, $DemoPlotRehab, $FarmerGroupFunctionsID, $date, $Kabupaten, $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $dataUpdated, $userid, $StatusFarmer, $rgDeceased, $FamilyMemberID, $MovedLeftArea, $SwitchOtherCrop, $Photo_old, $ExtFarmerID, $RtRw) {

        $idf = $this->_generateFarmerID(substr($RegionalCd, 0, 4));

        //cek foto
        if ($Photo_old != "") {
            //rename foto yg sudah terupload
            if (file_exists('images/Photo/' . $Photo_old)) {
                $tempArr = explode('.', $Photo_old);
                $extPoto = end($tempArr);
                $gambarBaru = 'frm_' . $idf . '_' . date('Ymdhis') . '.' . strtolower($extPoto);

                //ambil nama propinsi
                $ProvinceID = substr($idf, 0, 2);
                $namaProvince = $this->readProvinsiNama($ProvinceID);

                if (rename('images/Photo/' . $Photo_old, 'images/Photo/' . $namaProvince . '/' . $gambarBaru)) {
                    $farmerPhoto = $namaProvince . '/' . $gambarBaru;
                } else {
                    $farmerPhoto = null;
                }
            } else {
                $farmerPhoto = null;
            }
        } else {
            $farmerPhoto = null;
        }

        $BirthDttm = ($BirthDttm == '' ? null : $BirthDttm);
        $sql_farmer = "
            INSERT INTO ktv_farmer(
                FarmerID,CPGid,LahanKosong,Muge,ActiveMemberCooperation,
                KeyFarmer,DemoPlot,OtherTraining,CPGmembership,OtherTrainingSiapa,
                OtherTrainingTahun,OtherTrainingLama,DemoPlotLama,DemoPlotRehab,FarmerGroupFunctionsID,
                DateCollection,FarmerName,Birthdate,Photo,Gender,
                Address,VillageID,MaritalStatus,Education,HandPhone,
                LahanKakao,LahanProduksiLain,TotalLahan,KebunKakao,DateCreated,
                CreatedBy,DateUpdated,LastModifiedBy,StatusFarmer,DeceasedStatus,
                FamilyMemberID,MovedLeftArea,SwitchOtherCrop,ExtFarmerID,RtRw)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),?,now(),?,?,?,?,?,?,?,?)";
        $sql_harvest = "
            INSERT INTO ktv_farmer_post_harvest(FarmerID,SurveyNr)
            VALUES (?,0)";
        $sql_garden = "
            INSERT INTO ktv_farmer_garden(FarmerID,GardenNr,SurveyNr)
            VALUES (?,1,0)";
        $sql_ppi = "
            INSERT INTO ktv_ppiscore(FarmerID,SurveyNr)
            VALUES (?,0)";
        $sql_ppi_2012 = "
            INSERT INTO ktv_ppiscore2012(FarmerID,SurveyNr)
            VALUES (?,0)";
        $sql_nutrition = "
            INSERT INTO ktv_nutrition(FarmerID,SurveyNr)
            VALUES (?,0)";
        $this->db->trans_start();
        if ($FarmerGroupID == '') {
            $FarmerGroupID = null;
        }

        $query = $this->db->query($sql_farmer, array(
            $idf, $FarmerGroupID, $LahanKosong, $Muge, $ActiveMemberCooperation,
            $KeyFarmer, $DemoPlot, $OtherTraining, $CPGmembership, $OtherTrainingSiapa,
            $OtherTrainingTahun, $OtherTrainingLama, $DemoPlotLama, $DemoPlotRehab, $FarmerGroupFunctionsID,
            $date, $PersonNm, $BirthDttm, $farmerPhoto, $Gender,
            $Address, $RegionalCd, $MaritalSt, $Education, $Handphone,
            $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $userid,
            $userid, $StatusFarmer, $rgDeceased, $FamilyMemberID, $MovedLeftArea,
            $SwitchOtherCrop,$ExtFarmerID,$RtRw));
        //$this->db->query($sql_harvest, array($idf));
        //$this->db->query($sql_garden, array($idf));
        //$this->db->query($sql_ppi, array($idf));
        //$this->db->query($sql_ppi_2012, array($idf));
        //$this->db->query($sql_nutrition, array($idf));
        //update ke cek_photo dan ktv_photo_history (begin)
        if ($farmerPhoto != "") {
            $this->updateCekPhoto($idf, $farmerPhoto);
            $this->updatePhotoHistory($idf, $farmerPhoto);
        }
        //update ke cek_photo dan ktv_photo_history (end)

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record created.";
            $results['id'] = $idf;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateCekPhoto($farmerId, $photo) {
        $pathPhoto = base_url() . '/images/Photo/' . $photo;

        $sql = "INSERT INTO
               cek_photo (`FarmerID`,`Path`,`Status`)
               VALUES (?,?,'ada')
            ON DUPLICATE KEY UPDATE
               `Path` = ?,
               `Status` = 'ada'";
        $query = $this->db->query($sql, array($farmerId, $pathPhoto, $pathPhoto));
    }

    public function updatePhotoHistory($farmerId, $photo) {
        $pathPhoto = base_url() . '/images/Photo/' . $photo;

        $sql = "UPDATE ktv_photo_history SET
               `IsActive` = '0'
            WHERE
               `FarmerID` = ?";
        $query = $this->db->query($sql, array($farmerId));

        $sql = "INSERT INTO `ktv_photo_history` SET
               `FarmerID` = ?,
               `Photo` = ?,
               `IsActive` = '1',
               `DateCreated` = NOW()";
        $query = $this->db->query($sql, array($farmerId, $pathPhoto));
    }

    public function createFamily($farmerId, $AnggotaName, $HubunganKeluarga, $WorkGardenStatus, $ActivityType, $TotalWorkingHrsPerDay, $TotalWorkingHrs, $WageAmount, $DateOfBirth, $AnggotaAge, $AnggotaGender, $StatusSekolah, $userId) {

        //AnggotaAge
        $arrTmp = explode('-', $DateOfBirth);
        $AnggotaAge = $arrTmp[0];

        $sql_family = "
         INSERT INTO ktv_family (FamilyID,FarmerID,
    AnggotaName, HubunganKeluarga, WorkGardenStatus, ActivityType, TotalWorkingHrsPerDay, TotalWorkingHrs, WageAmount, DateOfBirth, AnggotaAge, AnggotaGender, StatusSekolah,
        CreatedBy,LastModifiedBy,DateCreated,DateUpdated)
         select max(FamilyID)+1,?,
         ?,?,?,?,?,?,?,?,?,?,?,
         ?,?,now(),now() from ktv_family";
        $query = $this->db->query($sql_family, array($farmerId, $AnggotaName, $HubunganKeluarga, $WorkGardenStatus, $ActivityType, $TotalWorkingHrsPerDay, $TotalWorkingHrs, $WageAmount, null, $AnggotaAge, $AnggotaGender, $StatusSekolah, $userId, $userId));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateHarvest($DateCollection, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhSeasonalRupiah, $BuruhSeasonalPersen, $BuruhFulltime, $BuruhFulltimeRupiah, $BuruhFulltimePersen, $Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $DryingDaysSellPrice, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $surveyNr, $AntarSendiri, $Distance, $Comment, $DryMoistureStandard, $ImplementBeanRemainDry, $BeanDryHygienic, $id, $userid) {
        $survey = explode('|', $surveyNr);
        if ($survey[1] == 'Tambah Baru') {
            $this->addSurveyHarvests($id, $survey[0], $userid);
        } else {
            $add = ",LastModifiedBy=$userid,DateUpdated=now()";
        }

        $sql = "
            UPDATE ktv_farmer_post_harvest
            SET DateCollection=?,AnggotaKerjaKebun=?,BuruhSeasonal=?,BuruhSeasonalRupiah=?,BuruhSeasonalPersen=?,
               BuruhFulltime=?,BuruhFulltimeRupiah=?,BuruhFulltimePersen=?,Fermentation=?,FermentationDays=?,
               SunDryingSemen=?,DryingAlat=?,DryingDays=?,DryingDaysSellPrice=?,CocoaBuyers=?,NoFermentation=?,Sortasi=?,NoSortasi=?,
               SunDryingAspal=?,JemurYesNo=?,TidakJemur=?,SunDryingAlas=?,SurveyNr=?,AntarSendiri=?,Distance=?,Comment=?,DryMoistureStandard=?,ImplementBeanRemainDry=?,BeanDryHygienic=?
               $add
            WHERE FarmerID=? and SurveyNr=?";
        $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        $query = $this->db->query($sql_date_survey, array($id));
        $query = $this->db->query($sql, array($DateCollection, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhSeasonalRupiah,
            $BuruhSeasonalPersen, $BuruhFulltime, $BuruhFulltimeRupiah, $BuruhFulltimePersen,
            $Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $DryingDaysSellPrice, $CocoaBuyers, $NoFermentation, $Sortasi,
            $NoSortasi, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $surveyNr, $AntarSendiri, $Distance, $Comment, $DryMoistureStandard, $ImplementBeanRemainDry, $BeanDryHygienic,
            $id, $surveyNr));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function updateSavingPilot($FarmerID, $SurveyNr, $FamilyMembers, $LandSizeHa, $AmountCocoaIncome, $AmountOtherIncome, $SavingYesNo, $AmountSaving, $LoanYesNo, $AccountNumber, $Age, $MarriedYesNo, $InterviewDate, $userid) {
        $survey = explode('|', $SurveyNr);
        if ($survey[1] == 'Tambah Baru') {
            $this->addSurveySavingPilot($FarmerID, $survey[0], $userid);
        } else {
            $add = ",LastModifiedBy=$userid,DateUpdated=now()";
        }

        if ($SavingYesNo != '1') {
            $AmountSaving = null;
        }

        $sql = "
            UPDATE ktv_saving_pilot
            SET FamilyMembers=?,LandSizeHa=?,AmountCocoaIncome=?,AmountOtherIncome=?,SavingYesNo=?,AmountSaving=?,LoanYesNo=?,AccountNumber=?,Age=?,MarriedYesNo=?,InterviewDate=? $add
            WHERE FarmerID=? and SurveyNr=?";
        $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        $query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql, array($FamilyMembers, $LandSizeHa, $AmountCocoaIncome, $AmountOtherIncome, $SavingYesNo, $AmountSaving, $LoanYesNo, $AccountNumber, $Age, $MarriedYesNo, $InterviewDate, $FarmerID, $SurveyNr));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function updateFarmer($Ssn, $PersonNm, $BirthDttm, $BirthPlace, $gambar, $Gender, $Address, $RegionalCd, $ZipCd, $Email, $BloodT, $MaritalSt, $Education, $NationalityNm, $Handphone, $FarmerGroupID, $LahanKosong, $Muge, $ActiveMemberCooperation, $KeyFarmer, $DemoPlot, $OtherTraining, $CPGmembership, $OtherTrainingSiapa, $OtherTrainingTahun, $OtherTrainingLama, $DemoPlotLama, $DemoPlotRehab, $FarmerGroupFunctionsID, $personId, $date, $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $DateUpdated, $farmerId, $userid, $StatusFarmer, $ReasonStatusFarmer, $rgDeceased, $FamilyMemberID, $MovedLeftArea, $SwitchOtherCrop, $AccountBeneficiary, $BankID, $BankBranch, $AccountNumber, $LearningContractFile_old, $LearningContractFile_new, $CertContractFile_old, $CertContractFile_new, $ExtFarmerID) {

        ini_set('display_errors', true);
        error_reporting('E_ALL');

        if ($LearningContractFile_new != "") {
            $LearningContractFile = $LearningContractFile_new;
        } else {
            $LearningContractFile = $LearningContractFile_old;
        }
        if ($CertContractFile_new != "") {
            $CertContractFile = $CertContractFile_new;
        } else {
            $CertContractFile = $CertContractFile_old;
        }
        $BirthDttm = ($BirthDttm == '' ? null : $BirthDttm);
        $sql_staff = "
         UPDATE ktv_farmer
            SET
                CPGid=?,LahanKosong=?,Muge=?,ActiveMemberCooperation=?,KeyFarmer=?,
                DemoPlot=?,OtherTraining=?,CPGmembership=?,OtherTrainingSiapa=?,OtherTrainingTahun=?,
                OtherTrainingLama=?,DemoPlotLama=?,DemoPlotRehab=?,FarmerGroupFunctionsID=?,DateUpdated=now(),
                DateCollection=?,FarmerName=?,Birthdate=?,Photo=?,Gender=?,
                Address=?,VillageID=?,MaritalStatus=?,Education=?,HandPhone=?,
                LahanKakao=?,LahanProduksiLain=?,TotalLahan=?,KebunKakao=?,LastModifiedBy=?,
                StatusFarmer=?,ReasonStatusFarmer=?,DeceasedStatus=?,FamilyMemberID=?,MovedLeftArea=?,SwitchOtherCrop=?,
                AccountBeneficiary = ?,BankID = ?,BankBranch = ?,AccountNumber = ?,LearningContractFile=?,CertContractFile=?,ExtFarmerID=?
            WHERE FarmerID=?";
        $this->db->trans_start();
        if ($FarmerGroupID == '') {
            $FarmerGroupID = null;
        }

        $query = $this->db->query($sql_staff, array(
            $FarmerGroupID, $LahanKosong, $Muge, $ActiveMemberCooperation, $KeyFarmer,
            $DemoPlot, $OtherTraining, $CPGmembership, $OtherTrainingSiapa, $OtherTrainingTahun,
            $OtherTrainingLama, $DemoPlotLama, $DemoPlotRehab, $FarmerGroupFunctionsID,
            $date, $PersonNm, $BirthDttm, $gambar, $Gender,
            $Address, $RegionalCd, $MaritalSt, $Education, $Handphone,
            $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $userid,
            $StatusFarmer, $ReasonStatusFarmer, $rgDeceased, $FamilyMemberID, $MovedLeftArea, $SwitchOtherCrop,
            $AccountBeneficiary, $BankID, $BankBranch, $AccountNumber, $LearningContractFile, $CertContractFile, $ExtFarmerID,
            $farmerId,
        ));

        //update ke cek_photo dan ktv_photo_history (begin)
        if ($gambar != "") {
            $this->updateCekPhoto($farmerId, $gambar);
            $this->updatePhotoHistory($farmerId, $gambar);
        }
        //update ke cek_photo dan ktv_photo_history (end)
        //insert data untuk coop sync local
        // $this->load->model('cooperatives/mcooperatives','coop');
        // $this->coop->insert_sync_farmer($farmerId,0);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function updateFamily($AnggotaName, $HubunganKeluarga, $WorkGardenStatus, $ActivityType, $TotalWorkingHrsPerDay, $TotalWorkingHrs, $WageAmount, $DateOfBirth, $AnggotaAge, $AnggotaGender, $StatusSekolah, $userId, $id) {

        //AnggotaAge
        $arrTmp = explode('-', $DateOfBirth);
        $AnggotaAge = $arrTmp[0];

        $sql = "
         UPDATE ktv_family
         SET
            AnggotaName = ?,
            HubunganKeluarga = ?,
            WorkGardenStatus = ?,
            ActivityType = ?,
            TotalWorkingHrsPerDay = ?,
            TotalWorkingHrs = ?,
            WageAmount = ?,
            DateOfBirth = ?,
            AnggotaAge = ?,
            AnggotaGender = ?,
            StatusSekolah = ?,
            DateUpdated=now(),
            LastModifiedBy=?
         WHERE FamilyId=?";
        $query = $this->db->query($sql, array($AnggotaName, $HubunganKeluarga, $WorkGardenStatus, $ActivityType, $TotalWorkingHrsPerDay, $TotalWorkingHrs, $WageAmount, null, $AnggotaAge, $AnggotaGender, $StatusSekolah,
            $userId, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteFarmer($reason, $farmerId, $user) {
        $sql = "
         UPDATE ktv_farmer
         SET StatusCode='nullified',DeleteReason=?,LastModifiedBy=?,DateUpdated=now()
         WHERE FarmerID=?";
        $query = $this->db->query($sql, array($reason, $user, $farmerId));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function deleteFamily($id, $reason) {
        $sql = "
         UPDATE ktv_family
            SET
                FamilyStatus='inactive',
                DeleteReason=?
         WHERE FamilyId=?";
        $query = $this->db->query($sql, array($reason, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function readGroupIDs($id, $kab) {
        $sql = "
            select %s
            from ktv_cpg a
            left join ktv_village w on a.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID
            WHERE y.ProvinceID=? and y.District=?
            ORDER BY GroupName %s";
        $query = $this->db->query(sprintf($sql, 'CPGid as id, concat(CPGid," - ",GroupName,
            IF(OldCPGid is null,"",concat(" [",OldCPGid,"]"))) as label', ''), array($id, $kab));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($id, $kab));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readInstitutionIDs() {
        $sql = "
            select %s
            from ktv_institution
            ORDER BY InstitutionID %s";
        $query = $this->db->query(sprintf($sql, 'InstitutionID as id, InstitutionName as label', ''), array((int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readPositionIDs() {
        $sql = "
            select %s
            from ktv_position
            ORDER BY PositionID %s";
        $query = $this->db->query(sprintf($sql, 'PositionID as id, PositionName as label', ''), array((int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readProvinsis() {
        /*
        GAK JELAS BUAT APA INI, Comment by Niko

        $sql_where = null;

        if ($_SESSION['daerah'] != '') {
            $sql_where = " and ProvinceID=" . substr($_SESSION['daerah'], 0, 2);
        }

        //cek relasi
        $sql_relasi = "
            select kdp.*
            from ktv_supplychain_staff kss
            left join ktv_supplychain_org_rel ksor on StaffSupplychainID=ChildOrgId
            left join ktv_supplychain_org_view ksov on SupplychainID=ParentOrgId
            left join ktv_warehouse kw on WarehouseID=OrgID and OrgType='Gudang'
            left join ktv_district_partner kdp on kdp.PartnerID=kw.PartnerID
            where UserID=?";
        $query_relasi = $this->db->query($sql_relasi, array($_SESSION['userid']));
        $relasi = $query_relasi->result_array();
        //echo $_SESSION['userid'];
        //print_r($relasi);exit;
        if (isset($relasi[0]['DistrictID'])) {
            if ($relasi[0]['DistrictID'] != '') {
                $sql_where = " and ProvinceID=" . substr($relasi[0]['DistrictID'], 0, 2);
            }
        }
        */

        $sql = "
            SELECT distinct Province as label,ProvinceID as id
            FROM ktv_province
            WHERE ProvinceID>0
            AND `active` = 1
            ORDER BY Province";
        $query = $this->db->query($sql);

        $result['data'] = $query->result_array();
        return $result;
    }

    public function readAllProvinsis() {
        $sql = "
            SELECT distinct Province as label,ProvinceID as id
            FROM ktv_province
            WHERE active='1'
            ORDER BY Province";
        $query = $this->db->query($sql);

        $result['data'] = $query->result_array();
        return $result;
    }

    public function readKabupatensStaff($ProvinceID){
        $SqlWhereAccess = " AND a.DistrictID IN ({$_SESSION['daerah_access']}) ";

        $sql = "SELECT distinct a.District as label, a.DistrictID as id
            FROM ktv_district a
            LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
            WHERE 
                a.active = '1'
                AND b.ProvinceID = ?
                $SqlWhereAccess
            ORDER BY District";
        $query = $this->db->query($sql,array($ProvinceID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readKabupatens($key, $kabId = '', $suppid = '') {
        if ($suppid != '') {
            $sql = "
                SELECT distinct a.District as label
                FROM ktv_district a
                LEFT JOIN ktv_supplychain_org_view b ON a.ProvinceID=substr(b.VillageID,1,2)
                WHERE b.SupplychainID = ?
                ORDER BY District";
            $query = $this->db->query($sql, array($suppid));
            $result['data'] = $query->result_array();
            return $result;
        }
        $sql_where = null;
        if ($_SESSION['daerah'] != '') {
            $dae = explode(',', $_SESSION['daerah']);
            for ($i = 0; $i < sizeof($dae); $i++) {
                $da = explode('##', $dae[$i]);
                $d[] = $da[0];
            }
            $sql_where = " and (DistrictID in (" . implode(',', $d) . ") OR DistrictID is null)";
        } elseif ($_SESSION['daerah_access'] != '') {
            // $dae = explode(',', $_SESSION['daerah_access']);
            // for ($i = 0; $i < sizeof($dae); $i++) {
            //     $da = explode('##', $dae[$i]);
            //     $d[] = $da[0];
            // }
            // $sql_where = " and (DistrictID in (" . implode(',', $d) . ") OR DistrictID is null)";
            $sql_where = " and (DistrictID in (" . $_SESSION['daerah_access'] . ") OR DistrictID is null)";
        }
        //cek relasi
        $sql_relasi = "
            select kdp.*
            from ktv_supplychain_staff kss
            left join ktv_supplychain_org_rel ksor on StaffSupplychainID=ChildOrgId
            left join ktv_supplychain_org_view ksov on SupplychainID=ParentOrgId
            left join ktv_warehouse kw on WarehouseID=OrgID and OrgType='Gudang'
            left join ktv_district_partner kdp on kdp.PartnerID=kw.PartnerID
            where UserID=?";
        $query_relasi = $this->db->query($sql_relasi, array($_SESSION['userid']));
        $relasi = $query_relasi->result_array();
        if (@$relasi[0]['DistrictID'] != '') {
            for ($i = 0; $i < sizeof($relasi); $i++) {
                if ($relasi[$i]['DistrictID'] != '') {
                    $d[] = $relasi[$i]['DistrictID'];
                }
            }
            $sql_where = " and DistrictID in (" . implode(',', $d) . ")";
        }
        if ($sql_where == null) {
            $sql_where = " and DistrictID not in (1171,7373,7271,1377)";
        } else {
            $sql_where .= " and DistrictID not in (1171,7373,7271,1377)";
        }
        if ($kabId == '72') {
            $sql_where .= " and DistrictID in (7201,7205,7204,7208,7202)";
        }

        if ($kabId == '') {
            $sql = "
                SELECT distinct District as label, a.DistrictID as id
                FROM ktv_district a
                LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                WHERE (Province = ?  OR a.ProvinceID=?) %s
                ORDER BY District";
            $query = $this->db->query(sprintf($sql, $sql_where), array($key, $key));
//            printf($sql,$sql_where);exit;
        } else {
            $sql = "
                SELECT distinct a.District as label, a.DistrictID as id
                FROM ktv_district a
                LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                WHERE b.ProvinceID = ? %s
                ORDER BY District";
            $query = $this->db->query(sprintf($sql, $sql_where), array($kabId));
        }

        $result['data'] = $query->result_array();
        return $result;
    }

    public function readKecamatans($key) {
        $sql = "
            SELECT distinct SubDistrict as label
            FROM ktv_subdistrict a
            LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
            WHERE District = ?
            ORDER BY SubDistrict";
        $query = $this->db->query($sql, array($key));
        $return['data'] = $query->result_array();
        return $return;
    }

    public function readDesas($key) {
        $keys = explode('::', $key);
        $key = $keys[0];
        $kab = $keys[1];
        $sup = $keys[2];
        $sql = "
            SELECT distinct %s as label,VillageID as id
            FROM ktv_village a
            LEFT JOIN ktv_subdistrict b ON a.SubDistrictID=b.SubDistrictID
            LEFT JOIN ktv_district c ON b.DistrictID=c.DistrictID
            LEFT JOIN ktv_supplychain_area d ON c.DistrictID in (d.DistrictID)
            WHERE %s
            ORDER BY District,SubDistrict,Village";
        if ($sup != '') {
            $query = $this->db->query(sprintf($sql, "concat(District,' - ',SubDistrict,' - ',Village)", 'SupplychainID = ?'), array($sup));
        } elseif ($kab != '') {
            $query = $this->db->query(sprintf($sql, "concat(SubDistrict,' - ',Village)", 'District IN (?)'), array($kab));
        } elseif ($key != '') {
            $query = $this->db->query(sprintf($sql, 'Village', 'SubDistrict = ?'), array($key));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function readArea($key) {
        $sql = "
            SELECT *,d.Village as Desa,e.SubDistrict as Kecamatan, f.District as Kabupaten,g.Province as Provinsi
            FROM ktv_village d
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            WHERE f.ProvinceID = ?";
        $query = $this->db->query($sql, array($key));
        $result = $query->result_array();
        return $result[0];
    }

    public function readGardens($id) {
        $sql = "SELECT GardenNr as id
            FROM ktv_farmer_garden
            WHERE FarmerID = ?
            ORDER BY GardenNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function readSurveys($id, $gardenNr, $jenis, $isAddLatest = "no", $isPostline="no") {
        if ($isAddLatest == "yes") {
            $sql_latest_survey = "SELECT 'latest' AS id, 'Latest Survey' AS label UNION ";
        } else {
            $sql_latest_survey = "";
        }

        if($isPostline == "yes"){
            $sql_latest_survey .= "SELECT 'postline' AS id, 'Postline Survey' AS label UNION ";
        } else {
            $sql_latest_survey .= "";
        }

        $sql = "
            $sql_latest_survey
            (SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_farmer_garden b ON a.SurveyNr=b.SurveyNr and FarmerID=? and GardenNr=?
            WHERE b.SurveyNr IS NULL
            ORDER BY a.SurveyNr)";
        $query = $this->db->query($sql, array($id, $gardenNr));
        return $query->result_array();
    }

    public function readSurveyHarvests($id, $jenis) {
        if ($jenis == 'add') {
            $where = 'WHERE b.SurveyNr is null';
        }

        $sql = "
            SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_farmer_post_harvest b ON a.SurveyNr=b.SurveyNr and FarmerID=?
            $where
            ORDER BY b.SurveyNr desc,a.SurveyNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function readSurveySavingPilots($id, $jenis) {
        if ($jenis == 'add') {
            $where = 'WHERE b.SurveyNr is null';
        }

        $sql = "
            SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_saving_pilot b ON a.SurveyNr=b.SurveyNr and FarmerID=?
            $where
            ORDER BY b.SurveyNr desc,a.SurveyNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function addSurveyHarvests($farmerId, $nr, $userid) {
        $sql_add = "
         insert into ktv_farmer_post_harvest (FarmerID, SurveyNr,DateCreated,CreatedBy)
         values(?,?,now(),?)";
        $query = $this->db->query($sql_add, array($farmerId, $nr, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function addSurveySavingPilot($farmerId, $nr, $userid) {
        $sql_add = "
         insert into ktv_saving_pilot (FarmerID, SurveyNr,DateCreated,CreatedBy)
         values(?,?,now(),?)";
        $query = $this->db->query($sql_add, array($farmerId, $nr, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function addSurveyGarden($farmerId, $gardenNr, $nr, $userid) {
        $sql_add = "
         insert into ktv_farmer_garden (FarmerID, GardenNr, SurveyNr,DateCreated,CreatedBy)
         values(?,?,?,now(),?)";
        $query = $this->db->query($sql_add, array($farmerId, $gardenNr, $nr, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function addGarden($farmerId, $userid) {
        $sql_add = "
         insert into ktv_farmer_garden (FarmerID, GardenNr, SurveyNr,DateCreated,CreatedBy)
         values(?,?,?,now(),?)";
        $sql_get = "
         select IFNULL(max(GardenNr)+1,0) as nr
         from ktv_farmer a
         left join ktv_farmer_garden b on a.FarmerID=b.FarmerID
         where a.FarmerID=?";
        $query = $this->db->query($sql_get, array($farmerId));
        $result = $query->result_array();
        $query = $this->db->query($sql_add, array($farmerId, $result[0]['nr'], 0, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function readSurveyPpis($id, $jenis) {
        if ($jenis == 'add') {
            $where = 'WHERE b.SurveyNr is null';
        }

        $sql = "
            SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_ppiscore b ON a.SurveyNr=b.SurveyNr and FarmerID=?
            $where
            ORDER BY b.SurveyNr desc,a.SurveyNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function readSurveyPpi2012s($id, $jenis) {
        if ($jenis == 'add') {
            $where = 'WHERE b.SurveyNr is null';
        }

        $sql = "
            SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_ppiscore2012 b ON a.SurveyNr=b.SurveyNr and FarmerID=?
            $where
            ORDER BY b.SurveyNr desc,a.SurveyNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function readSurveyNutritions($id, $jenis) {
        if ($jenis == 'add') {
            $where = 'WHERE b.SurveyNr is null';
        }

        $sql = "
            SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_nutrition b ON a.SurveyNr=b.SurveyNr and FarmerID=?
            $where
            ORDER BY b.SurveyNr desc,a.SurveyNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function readFarmerPpi($id, $nr) {
        $sql = "
            select a.*,concat(c.SurveyNr, ' - ', c.SurveyTxt) AS label_survey,a.InterviewDate,FarmerName as PersonNm,
              DATE_FORMAT(a.InterviewDate,'%d - %b - %Y') as DateInterview
            from ktv_ppiscore a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            LEFT JOIN ktv_survey c ON a.SurveyNr = c.SurveyNr
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql, array($id, $nr));
        $result = $query->result_array();
        return $result[0];
    }

    public function readFarmerPpi2012($id, $nr) {
        $sql = "
            select a.*,concat(c.SurveyNr, ' - ', c.SurveyTxt) AS label_survey,a.InterviewDate,FarmerName as PersonNm, DATE_FORMAT(a.InterviewDate,'%d - %b - %Y') as DateInterview
            from ktv_ppiscore2012 a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            LEFT JOIN ktv_survey c ON a.SurveyNr = c.SurveyNr
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql, array($id, $nr));
        $result = $query->result_array();
        return $result[0];
    }

    public function updatePpi($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $Working, $DrinkingWater, $ToiletFacility, $HouseFloor, $HouseCeiling, $Refrigerator, $Motorcycle, $Television, $farmerid, $userid) {
        $survey = explode('|', $SurveyNr);
        if ($survey[1] == 'Tambah Baru') {
            $this->addSurveyPpi($farmerid, $survey[0], $userid);
        } else {
            $add = ",LastModifiedBy=$userid,DateUpdated=now()";
        }

        $sql = "
            UPDATE ktv_ppiscore
            SET SurveyNr=?,InterviewDate=?,Householdmembers=?,Schooling=?,Working=?,DrinkingWater=?,ToiletFacility=?,
               HouseFloor=?,HouseCeiling=?,Refrigerator=?,Motorcycle=?,Television=?$add
            WHERE SurveyNr=? and FarmerID=?";
        $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        $sql_score = "
            update ktv_ppiscore a left join
            (SELECT
                FarmerID,SurveyNr,
                CASE Householdmembers
                    WHEN 1 THEN 0
                    WHEN 2 THEN 7
                    WHEN 3 THEN 13
                    WHEN 4 THEN 21
                    WHEN 5 THEN 26
                    WHEN 6 THEN 37
                END + CASE Schooling
                    WHEN 1 THEN 0
                    WHEN 2 THEN 3
                END + CASE Working
                    WHEN 1 THEN 0
                    WHEN 2 THEN 6
                    WHEN 3 THEN 7
                    WHEN 4 THEN 10
                END + CASE DrinkingWater
                    WHEN 1 THEN 0
                    WHEN 2 THEN 4
                    WHEN 3 THEN 9
                END + CASE ToiletFacility
                    WHEN 1 THEN 0
                    WHEN 2 THEN 5
                END + CASE HouseFloor
                    WHEN 1 THEN 0
                    WHEN 2 THEN 6
                END + CASE HouseCeiling
                    WHEN 1 THEN 0
                    WHEN 2 THEN 4
                END + CASE Refrigerator
                    WHEN 1 THEN 0
                    WHEN 2 THEN 12
                END + CASE Television
                    WHEN 1 THEN 0
                    WHEN 2 THEN 5
                END + CASE Motorcycle
                    WHEN 1 THEN 0
                    WHEN 2 THEN 9
                END AS TotalScore
            FROM
                ktv_ppiscore
            WHERE FarmerID=? and SurveyNr=?) b on a.FarmerID=b.FarmerID and a.SurveyNr=b.SurveyNr
            left join ktv_ppi_calculation c on Type='PPI 2010' and (TotalScore between ScoreMin and ScoreMax)
            set Score=TotalScore,a.National=c.National,`1.25/day`=`\$1.25/day`,`2.5/day`=`\$2.5/day`
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql_date_survey, array($farmerid));
        $query = $this->db->query($sql, array($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $Working,
            $DrinkingWater, $ToiletFacility, $HouseFloor, $HouseCeiling, $Refrigerator, $Motorcycle, $Television,
            $SurveyNr, $farmerid));
        $query = $this->db->query($sql_score, array($farmerid, $SurveyNr, $farmerid, $SurveyNr));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function updatePpi2012($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $pEducation, $pEmployment, $pHouseFloor, $pToiletFacility, $pCookingFuel, $pGasCylinder, $pRefrigerator, $pMotorcycle, $farmerid, $userid) {
        $survey = explode('|', $SurveyNr);
        if ($survey[1] == 'Tambah Baru') {
            $this->addSurveyPpi2012($farmerid, $survey[0], $userid);
        } else {
            $add = ",LastModifiedBy=$userid,DateUpdated=now()";
        }

        if ($Householdmembers == '') {
            $Householdmembers = null;
        }

        if ($Schooling == '') {
            $Schooling = null;
        }

        if ($pEducation == '') {
            $pEducation = null;
        }

        if ($pEmployment == '') {
            $pEmployment = null;
        }

        if ($pHouseFloor == '') {
            $pHouseFloor = null;
        }

        if ($pToiletFacility == '') {
            $pToiletFacility = null;
        }

        if ($pCookingFuel == '') {
            $pCookingFuel = null;
        }

        if ($pGasCylinder == '') {
            $pGasCylinder = null;
        }

        if ($pRefrigerator == '') {
            $pRefrigerator = null;
        }

        if ($pMotorcycle == '') {
            $pMotorcycle = null;
        }

        $sql = "
            UPDATE ktv_ppiscore2012
            SET SurveyNr=?,InterviewDate=?,Householdmembers=?,Schooling=?,Education=?,Employment=?,HouseFloor=?,
               ToiletFacility=?,CookingFuel=?,GasCylinder=?,Refrigerator=?,Motorcycle=?$add
            WHERE SurveyNr=? and FarmerID=?";
        $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        $sql_score = "
            update ktv_ppiscore2012 a left join
            (SELECT
                FarmerID,SurveyNr,
                CASE Householdmembers
                    when 1 THEN 0
                    WHEN 2 THEN 6
                    WHEN 3 THEN 12
                    WHEN 4 THEN 18
                    WHEN 5 THEN 24
                    WHEN 6 THEN 35
                END + CASE Schooling
                    WHEN 1 THEN 0
                    WHEN 2 THEN 0
                    WHEN 3 THEN 2
                END + CASE Education
                    WHEN 1 THEN 0
                    WHEN 2 THEN 3
                    WHEN 3 THEN 4
                    WHEN 4 THEN 4
                    WHEN 5 THEN 5
                    WHEN 6 THEN 7
                    WHEN 7 THEN 18
                END + CASE Employment
                    WHEN 1 THEN 0
                    WHEN 2 THEN 0
                    WHEN 3 THEN 1
                    WHEN 4 THEN 3
                    WHEN 5 THEN 4
                    WHEN 6 THEN 6
                END + CASE ToiletFacility
                    WHEN 1 THEN 0
                    WHEN 2 THEN 2
                    WHEN 3 THEN 4
                END + CASE HouseFloor
                    WHEN 1 THEN 0
                    WHEN 2 THEN 5
                END + CASE CookingFuel
                    WHEN 1 THEN 0
                    WHEN 2 THEN 5
                END + CASE Refrigerator
                    WHEN 1 THEN 0
                    WHEN 2 THEN 9
                END + CASE GasCylinder
                    WHEN 1 THEN 0
                    WHEN 2 THEN 7
                END + CASE Motorcycle
                    WHEN 1 THEN 0
                    WHEN 2 THEN 9
                END as TotalScore
            FROM
                ktv_ppiscore2012
            WHERE FarmerID=? and SurveyNr=?) b on a.FarmerID=b.FarmerID and a.SurveyNr=b.SurveyNr
            left join ktv_ppi_calculation c on Type='PPI 2012' and (TotalScore between ScoreMin and ScoreMax)
            set Score=TotalScore,a.National=c.National,`1.25/day`=`\$1.25/day`,`2.5/day`=`\$2.5/day`
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql_date_survey, array($farmerid));
        $query = $this->db->query($sql, array($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $pEducation, $pEmployment,
            $pHouseFloor, $pToiletFacility, $pCookingFuel, $pGasCylinder, $pRefrigerator, $pMotorcycle, $SurveyNr, $farmerid));
        $query = $this->db->query($sql_score, array($farmerid, $SurveyNr, $farmerid, $SurveyNr));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function addSurveyPpi($farmerId, $nr, $userid) {
        $sql_add = "
         insert into ktv_ppiscore (FarmerID, SurveyNr,DateCreated,CreatedBy)
         values(?,?,now(),?)";
        $query = $this->db->query($sql_add, array($farmerId, $nr, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function addSurveyPpi2012($farmerId, $nr, $userid) {
        $sql_add = "
         insert into ktv_ppiscore2012 (FarmerID, SurveyNr,DateCreated,CreatedBy)
         values(?,?,now(),?)";
        /* $sql_get = "
          select IFNULL(max(SurveyNr)+1,0) as nr
          from ktv_farmer a
          left join ktv_ppiscore2012 b on a.FarmerID=b.FarmerID
          where a.FarmerID=?";
          $query = $this->db->query($sql_get, array($farmerId));
          $result = $query->result_array();
          $query = $this->db->query($sql_add, array($farmerId,$result[0]['nr'])); */
        $query = $this->db->query($sql_add, array($farmerId, $nr, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function readFarmerNutrition($id, $nr) {
        $sql = "
            select a.*,FarmerName as PersonNm, DATE_FORMAT(a.InterviewDate,'%d - %b - %Y') as DateInterview, DATE_FORMAT(a.InterviewDate,'%Y-%m-%d') AS tglInterview
            from ktv_nutrition a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql, array($id, $nr));
        $result = $query->result_array();
        return $result[0];
    }

    public function updateNutrition($SurveyNr, $InterviewDate, $KebunPanjang, $KebunLebar, $KbBayam, $KbCabai, $KbKacangPanjang, $KbKangkung, $KbSawi, $KbTerong, $KbTomat, $KbKambing, $KbSapi, $KbBebek, $KbAyam, $KbIkan, $aSagu, $aNasi, $aMie, $aJagung, $aRoti, $bUbiJalarKuning, $bSingkongKuning, $bWortel, $bLabu, $cUbiJalarPutih, $cSingkongPutih, $cTalas, $cKentang, $dBayam, $dDaunMelinjo, $dDaunPepaya, $dDaunSingkong, $dKangkung, $dSawi, $eKacangPanjang, $eTomat, $eTerong, $fJambuMerah, $fMangga, $fPepaya, $gJambuAir, $gKelapa, $gPisang, $gRambutan, $gSemangka, $gSalak, $hJeroan, $hHati, $iAyam, $iBebek, $iKambing, $iKerbau, $iSapi, $iLainnya, $jAyam, $jBebek, $jEntok, $jPuyuh, $kCumiCumi, $kIkan, $kIkanTeri, $kKepiting, $kKerang, $kUdang, $lAirTahuSusuKedelai, $lSausKacang, $lTahu, $lTempe, $lKacang, $lKwaci, $mKeju, $mSusu, $nMinyakGoreng, $nMentega, $nSantan, $Score, $farmerid, $ComKebunPanjang, $ComKebunLebar, $ComKbBayam, $ComKbCabai, $ComKbKacangPanjang, $ComKbKangkung, $ComKbSawi, $ComKbTerong, $ComKbTomat, $HaveChildren, $ChildrenMeal, $ChildrenASI, $Children3MonthASI, $ChildrenNrGiveASI, $ChildrenNrGiveMeal, $ChildrenGiveKolestrum, $MotherPregnant2Years, $MotherPregnantEat, $userid) {
        $survey = explode('|', $SurveyNr);
        if ($survey[1] == 'Tambah Baru') {
            $this->addSurveyNutrition($farmerid, $survey[0], $userid);
        } else {
            $add = ",LastModifiedBy=$userid,DateUpdated=now()";
        }

        $sql = "
            UPDATE ktv_nutrition
            SET SurveyNr=?,InterviewDate=?,KebunPanjang=?,KebunLebar=?,KbBayam=?,KbCabai=?,KbKacangPanjang=?,KbKangkung=?,
               KbSawi=?,KbTerong=?,KbTomat=?,KbKambing=?,KbSapi=?,KbBebek=?,KbAyam=?,KbIkan=?,aSagu=?,aNasi=?,aMie=?,aJagung=?,
               aRoti=?,bUbiJalarKuning=?,bSingkongKuning=?,bWortel=?,bLabu=?,cUbiJalarPutih=?,cSingkongPutih=?,cTalas=?,
               cKentang=?,dBayam=?,dDaunMelinjo=?,dDaunPepaya=?,dDaunSingkong=?,dKangkung=?,dSawi=?,eKacangPanjang=?,
               eTomat=?,eTerong=?,fJambuMerah=?,fMangga=?,fPepaya=?,gJambuAir=?,gKelapa=?,gPisang=?,gRambutan=?,gSemangka=?,
               gSalak=?,hJeroan=?,hHati=?,iAyam=?,iBebek=?,iKambing=?,iKerbau=?,iSapi=?,iLainnya=?,jAyam=?,jBebek=?,jEntok=?,
               jPuyuh=?,kCumiCumi=?,kIkan=?,kIkanTeri=?,kKepiting=?,kKerang=?,kUdang=?,lAirTahuSusuKedelai=?,lSausKacang=?,
               lTahu=?,lTempe=?,lKacang=?,lKwaci=?,mKeju=?,mSusu=?,nMinyakGoreng=?,nMentega=?,nSantan=?,Score=?,
               ComKebunPanjang=?,ComKebunLebar=?,ComKbBayam=?,ComKbCabai=?,ComKbKacangPanjang=?,ComKbKangkung=?,ComKbSawi=?,ComKbTerong=?,ComKbTomat=?,
               HaveChildren=?,ChildrenMeal=?,ChildrenASI=?,Children3MonthASI=?,ChildrenNrGiveASI=?,ChildrenNrGiveMeal=?,ChildrenGiveKolestrum=?,MotherPregnant2Years=?,MotherPregnantEat=?
               $add
            WHERE SurveyNr=? and FarmerID=?";
        $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        $query = $this->db->query($sql_date_survey, array($farmerid));
        $query = $this->db->query($sql, array($survey[0], $InterviewDate, $KebunPanjang, $KebunLebar, $KbBayam, $KbCabai,
            $KbKacangPanjang, $KbKangkung, $KbSawi, $KbTerong, $KbTomat, $KbKambing, $KbSapi, $KbBebek, $KbAyam, $KbIkan, $aSagu,
            $aNasi, $aMie, $aJagung, $aRoti, $bUbiJalarKuning, $bSingkongKuning, $bWortel, $bLabu, $cUbiJalarPutih, $cSingkongPutih,
            $cTalas, $cKentang, $dBayam, $dDaunMelinjo, $dDaunPepaya, $dDaunSingkong, $dKangkung, $dSawi, $eKacangPanjang, $eTomat,
            $eTerong, $fJambuMerah, $fMangga, $fPepaya, $gJambuAir, $gKelapa, $gPisang, $gRambutan, $gSemangka, $gSalak, $hJeroan,
            $hHati, $iAyam, $iBebek, $iKambing, $iKerbau, $iSapi, $iLainnya, $jAyam, $jBebek, $jEntok, $jPuyuh, $kCumiCumi, $kIkan,
            $kIkanTeri, $kKepiting, $kKerang, $kUdang, $lAirTahuSusuKedelai, $lSausKacang, $lTahu, $lTempe, $lKacang, $lKwaci, $mKeju, $mSusu,
            $nMinyakGoreng, $nMentega, $nSantan, $Score,
            $ComKebunPanjang, $ComKebunLebar, $ComKbBayam, $ComKbCabai, $ComKbKacangPanjang, $ComKbKangkung, $ComKbSawi, $ComKbTerong, $ComKbTomat,
            $HaveChildren, $ChildrenMeal, $ChildrenASI, $Children3MonthASI, $ChildrenNrGiveASI, $ChildrenNrGiveMeal, $ChildrenGiveKolestrum, $MotherPregnant2Years, $MotherPregnantEat,
            $SurveyNr, $farmerid));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre"; exit;
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function addSurveyNutrition($farmerId, $nr, $userid) {
        $sql_add = "
         insert into ktv_nutrition (FarmerID, SurveyNr,DateCreated,CreatedBy)
         values(?,?,now(),?)";
        $query = $this->db->query($sql_add, array($farmerId, $nr, $userid));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

//VALIDATION
    public function validationGarden($farmer, $garden, $survey, $validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC) {
        $sql = "
         UPDATE ktv_farmer_garden
         SET isValid=?,CommentValid=?,ApprovedByME=?,ApprovedByGO=?,ApprovedByDC=?
         WHERE FarmerID=? and GardenNr=? and SurveyNr=?";
        $sql_farmer = "
         UPDATE ktv_farmer
         SET isValidGarden=?
         WHERE FarmerID=?";
        $query = $this->db->query($sql, array($validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC,
            $farmer, $garden, $survey));
        $query = $this->db->query($sql_farmer, array($validation, $farmer));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function validationHarvest($farmer, $survey, $validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC) {
        $sql = "
         UPDATE ktv_farmer_post_harvest
         SET isValid=?,CommentValid=?,ApprovedByME=?,ApprovedByGO=?,ApprovedByDC=?
         WHERE FarmerID=? and SurveyNr=?";
        $sql_farmer = "
         UPDATE ktv_farmer
         SET isValidPostHarvest=?
         WHERE FarmerID=?";
        $query = $this->db->query($sql, array($validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC, $farmer, $survey));
        $query = $this->db->query($sql_farmer, array($validation, $farmer));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    /* function validationSavingPilot($farmer,$survey,$validation,$comment,$ApprovedByME,$ApprovedByGO,$ApprovedByDC){
      $sql = "
      UPDATE ktv_farmer_post_harvest
      SET isValid=?,CommentValid=?,ApprovedByME=?,ApprovedByGO=?,ApprovedByDC=?
      WHERE FarmerID=? and SurveyNr=?";
      $sql_farmer = "
      UPDATE ktv_farmer
      SET isValidPostHarvest=?
      WHERE FarmerID=?";
      $query = $this->db->query($sql, array($validation,$comment,$ApprovedByME,$ApprovedByGO,$ApprovedByDC,$farmer,$survey));
      $query = $this->db->query($sql_farmer, array($validation,$farmer));
      if ($query) {
      $results['success'] = true;
      $results['message'] = "record created.";
      } else {
      $results['success'] = false;
      $results['message'] = "Failed to create record";
      }
      return $results;
      } */

    public function validationPpi($farmer, $survey, $validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC) {
        $sql = "
         UPDATE ktv_ppiscore
         SET isValid=?,CommentValid=?,ApprovedByME=?,ApprovedByGO=?,ApprovedByDC=?
         WHERE FarmerID=? and SurveyNr=?";
        $sql_farmer = "
         UPDATE ktv_farmer
         SET isValidPPIScore=?
         WHERE FarmerID=?";
        $query = $this->db->query($sql, array($validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC, $farmer, $survey));
        $query = $this->db->query($sql_farmer, array($validation, $farmer));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function validationPpi2012($farmer, $survey, $validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC) {
        $sql = "
         UPDATE ktv_ppiscore2012
         SET isValid=?,CommentValid=?,ApprovedByME=?,ApprovedByGO=?,ApprovedByDC=?
         WHERE FarmerID=? and SurveyNr=?";
        $query = $this->db->query($sql, array($validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC, $farmer, $survey));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function validationNutrition($farmer, $survey, $validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC) {
        $sql = "
         UPDATE ktv_nutrition
         SET isValid=?,CommentValid=?,ApprovedByME=?,ApprovedByGO=?,ApprovedByDC=?
         WHERE FarmerID=? and SurveyNr=?";
        $sql_farmer = "
         UPDATE ktv_farmer
         SET isValidNutrition=?
         WHERE FarmerID=?";
        $query = $this->db->query($sql, array($validation, $comment, $ApprovedByME, $ApprovedByGO, $ApprovedByDC, $farmer, $survey));
        $query = $this->db->query($sql_farmer, array($validation, $farmer));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function readSumGarden($id) {

        $sql = "SELECT
                    GardenNr,
                    concat(a.SurveyNr,'-',SurveyTxt) as Survey,
                    DateCollection as DateInterview,
                    a.DateUpdated as DateValid,
                    isValid as StatusValid,
                    a.FarmerID as farmerid,
                    a.SurveyNr as id,
                    concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya,
                    c.UserRealName as UserCreated,
                    d.UserRealName as LastUpdatedBy
                FROM ktv_farmer_garden a
                    LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
                    LEFT JOIN sys_user c ON a.CreatedBy = c.UserId
                    LEFT JOIN sys_user d ON a.LastModifiedBy = d.UserId
                WHERE FarmerID=?";
        /*
          $sql = "
          SELECT GardenNr,concat(a.SurveyNr,'-',SurveyTxt) as Survey,DateCollection as DateInterview,
          a.DateUpdated as DateValid,isValid as StatusValid,a.FarmerID as farmerid,
          CONCAT(GardenNr,'-',a.SurveyNr) as id, concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya
          FROM ktv_farmer_garden a
          LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
          WHERE FarmerID=?";
         */
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readSumPost($id) {
        $sql = "SELECT concat(a.SurveyNr,'-',SurveyTxt) as Survey,DateCollection as DateInterview,
               a.DateUpdated as DateValid,isValid as StatusValid,a.FarmerID as farmerid,
               c.UserRealName as UserCreated,
               d.UserRealName as LastUpdatedBy
            FROM ktv_farmer_post_harvest a
            LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
            LEFT JOIN sys_user c ON a.CreatedBy = c.UserId
            LEFT JOIN sys_user d ON a.LastModifiedBy = d.UserId
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readSumNutrition($id) {
        $sql = "SELECT concat(a.SurveyNr,'-',SurveyTxt) as Survey,InterviewDate as DateInterview,
               a.DateUpdated as DateValid,isValid as StatusValid,a.FarmerID as farmerid,
               a.SurveyNr as id, concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya,
               c.UserRealName as UserCreated,
               d.UserRealName as LastUpdatedBy
            FROM ktv_nutrition a
            LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
            LEFT JOIN sys_user c ON a.CreatedBy = c.UserId
            LEFT JOIN sys_user d ON a.LastModifiedBy = d.UserId
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readSumPpi($id) {
        $sql = "SELECT concat(a.SurveyNr,'-',SurveyTxt) as Survey,InterviewDate as DateInterview,
               a.DateUpdated as DateValid,isValid as StatusValid,a.FarmerID as farmerid,
               a.SurveyNr as id, concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya,
               c.UserRealName as UserCreated,
               d.UserRealName as LastUpdatedBy
            FROM ktv_ppiscore2012 a
            LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
            LEFT JOIN sys_user c ON a.CreatedBy = c.UserId
            LEFT JOIN sys_user d ON a.LastModifiedBy = d.UserId
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readSumSP($id) {
        $sql = "SELECT concat(a.SurveyNr,'-',SurveyTxt) as Survey,InterviewDate as DateInterview,
               a.DateUpdated as DateValid,a.FarmerID as farmerid,
               a.SurveyNr as id, concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya,
               c.UserRealName as UserCreated,
               d.UserRealName as LastUpdatedBy
            FROM ktv_saving_pilot a
            LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
            LEFT JOIN sys_user c ON a.CreatedBy = c.UserId
            LEFT JOIN sys_user d ON a.LastModifiedBy = d.UserId
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readPartnerLogo($id, $partnerId = '') {
        $i = 0;
        $sql = "SELECT
                    p.PartnerID,
                    p.PartnerName,
                    p.Photo,
                    p.PhotoProgram,
                    p.FlagAccess,
                    cp.PartnerID AS PartnerID2
                FROM ktv_farmer f
                JOIN ktv_village v ON v.VillageID = f.VillageID
                JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                JOIN ktv_district_partner dp ON dp.DistrictID = sd.DistrictID
                JOIN ktv_program_partner p ON p.PartnerID = dp.PartnerID AND PartnerIndustry = 1
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                WHERE
                    f.FarmerID = ?
                    AND p.PartnerID <> 1 AND p.PartnerID <> 2 AND p.PartnerID <> 23
                    #AND p.PartnerIndustry <> 1
                    AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
               AND p.`StatusCode` = 'active'
                LIMIT 1";
        $query = $this->db->query($sql, array($id));

        if ($query->num_rows() > 0) {
            $data[$i]['Photo'] = $query->row()->Photo;
            $i++;
        } else {
            $sql = "SELECT
                        p.PartnerID,
                        p.PartnerName,
                        p.Photo,
                        p.PhotoProgram,
                        p.FlagAccess,
                        cp.PartnerID AS PartnerID2
                    FROM ktv_farmer f
                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                    JOIN ktv_program_partner p ON p.PartnerID = cp.PartnerID AND PartnerIndustry = 1
                    WHERE
                        f.FarmerID = ?
                        AND p.PartnerID <> 1 AND p.PartnerID <> 2 AND p.PartnerID <> 23
                        #AND p.PartnerIndustry <> 1
                        AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                  AND p.`StatusCode` = 'active'
                    LIMIT 1";
            $query = $this->db->query($sql, array($id));
            if ($query->num_rows() > 0) {
                $data[$i]['Photo'] = $query->row()->Photo;
                $i++;
            }
        }

        $sql = "SELECT
                    p.PartnerID,
                    p.PartnerName,
                    p.Photo,
                    p.PhotoProgram,
                    p.FlagAccess,
                    cp.PartnerID AS PartnerID2
                FROM ktv_farmer f
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                JOIN ktv_program_partner p ON p.PartnerID = cp.PartnerID AND PartnerIndustry != 1
                WHERE
                    f.FarmerID = ?
                    AND p.PartnerIndustry <> '0' AND p.PartnerIndustry <> '1' AND p.PartnerIndustry <> '6'
                    AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                AND p.`StatusCode` = 'active'
                LIMIT 0,2";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            if ($query->num_rows() == 1) {
                $data[$i]['Photo'] = $query->row()->Photo;
                $i++;
                if ($query->row()->PhotoProgram != '') {
                    $data[$i]['Photo'] = $query->row()->PhotoProgram;
                    $i++;
                }
                $program = $query->row()->PhotoProgram;
            } else {
                foreach ($query->result() as $row) {
                    $data[$i]['Photo'] = $row->Photo;
                    $i++;
                }
            }
        } else {
            $limit = 2; // - $query->num_rows();
            if ($limit > 0 && $i < 3) {
                $sql = "SELECT
                        p.PartnerID,
                        p.PartnerName,
                        p.Photo,
                        p.PhotoProgram,
                        p.FlagAccess,
                        cp.PartnerID AS PartnerID2
                    FROM ktv_farmer f
                    JOIN ktv_village v ON v.VillageID = f.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district_partner dp ON dp.DistrictID = sd.DistrictID
                    JOIN ktv_program_partner p ON p.PartnerID = dp.PartnerID AND PartnerIndustry != 1
                    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                    WHERE
                        f.FarmerID = ?
                        AND p.PartnerID <> 1 AND p.PartnerID <> 2 AND p.PartnerID <> 23
                        AND p.PartnerIndustry <> '0' AND p.PartnerIndustry <> '1' AND p.PartnerIndustry <> '6'
                        AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                   AND p.`StatusCode` = 'active'
                    LIMIT 0,2";

                $query = $this->db->query($sql, array($id));
                if ($query->num_rows() > 0) {
                    if ($query->num_rows() == 1) {
                        if ($data[$i - 1]['Photo'] == $query->row()->Photo) {
                            $i--;
                        }
                        $data[$i]['Photo'] = $query->row()->Photo;
                        $i++;
                        if ($i == 2) {
                            $data[$i]['Photo'] = $query->row()->PhotoProgram;
                        }
                    } else {
                        foreach ($query->result() as $row) {
                            if ($data[$i - 1]['Photo'] == $query->row()->Photo) {
                                $i--;
                            }
                            $data[$i]['Photo'] = $row->Photo;
                            $i++;
                        }
                    }
                } else {
                    if (@$program != '') {
                        $data[$i]['Photo'] = $program;
                        $i++;
                    }
                }
            }
        }

        /* echo "<pre>".print_r($data,1)."</pre>";
          for($i=0;$i<count($data);$i++){
          echo $data[$i]['Photo'].'<br>';
          }exit; */
        return $data;

        /* return array(
          'private_sector' => $this->getPSLogo($id),
          'donor' => $this->getDonorLogo($id),
          );
         */
    }

    public function getPSLogo($farmer_id) {
        // get partner from cpg_partner
        $sql = "SELECT
                    p.PartnerID,
                    p.PartnerName,
                    p.Photo,
                    p.PhotoProgram,
                    p.FlagAccess,
                    cp.PartnerID AS PartnerID2
                FROM ktv_farmer f
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                JOIN ktv_program_partner p ON p.PartnerID = cp.PartnerID AND PartnerIndustry != 1
                WHERE
                    f.FarmerID = ?
                    AND p.PartnerIndustry <> '1'
                    AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                LIMIT 0,2
    ";
        $query = $this->db->query($sql, array($farmer_id));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        // IF above doesn't work
        // get partner from district_partner
        $sql = "SELECT
                    p.PartnerID,
                    p.PartnerName,
                    p.Photo,
                    p.PhotoProgram,
                    p.FlagAccess,
                    cp.PartnerID AS PartnerID2
                FROM ktv_farmer f
                JOIN ktv_village v ON v.VillageID = f.VillageID
                JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                JOIN ktv_district_partner dp ON dp.DistrictID = sd.DistrictID
                JOIN ktv_program_partner p ON p.PartnerID = dp.PartnerID AND PartnerIndustry != 1
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                WHERE
                    f.FarmerID = ?
                    AND p.PartnerIndustry <> '1'
                    AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                LIMIT 0,2
    ";
        $query = $this->db->query($sql, array($farmer_id));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getDonorLogo($farmer_id) {
        // get partner from cpg_partner
        $sql = "SELECT
                    p.PartnerID,
                    p.PartnerName,
                    p.Photo,
                    p.PhotoProgram,
                    p.FlagAccess,
                    cp.PartnerID AS PartnerID2
                FROM ktv_farmer f
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                JOIN ktv_program_partner p ON p.PartnerID = cp.PartnerID AND PartnerIndustry = 1
                WHERE
                    f.FarmerID = ?
                    AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                LIMIT 0,4
    ";
        $query = $this->db->query($sql, array($farmer_id));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        // IF above doesn't work
        // get partner from district_partner
        $sql = "SELECT
                    p.PartnerID,
                    p.PartnerName,
                    p.Photo,
                    p.PhotoProgram,
                    p.FlagAccess,
                    cp.PartnerID AS PartnerID2
                FROM ktv_farmer f
                JOIN ktv_village v ON v.VillageID = f.VillageID
                JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                JOIN ktv_district_partner dp ON dp.DistrictID = sd.DistrictID
                JOIN ktv_program_partner p ON p.PartnerID = dp.PartnerID AND PartnerIndustry = 1
                LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                WHERE
                    f.FarmerID = ?
                    AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                LIMIT 0,4
    ";
        $query = $this->db->query($sql, array($farmer_id));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    /*
      function readCekGarden($FarmerID){
      $sql = "
      select GardenNr as id, GardenNr as garden
      from ktv_farmer_garden
      where FarmerID=?";
      $query = $this->db->query($sql,array($FarmerID));
      $result['data'] = $query->result_array();
      return $result;
      }
     */

    public function readCekSurvey($jenis, $FarmerID) {
        if ($jenis == 'F1') {
            return $this->readSumAff($FarmerID);
        } elseif ($jenis == 'P1') {
            return $this->readSumGarden($FarmerID);
        } elseif ($jenis == 'N1') {
            return $this->readSumNutrition($FarmerID);
        } elseif ($jenis == 'PPI') {
            return $this->readSumPpi($FarmerID);
        } elseif ($jenis == 'SP') {
            return $this->readSumSP($FarmerID);
        } elseif ($FarmerID) {
            $sql = "SELECT DISTINCT g.SurveyNr AS id, CONCAT(s.SurveyNr,' - ',s.SurveyTxt) AS surveya
FROM ktv_farmer_garden g
JOIN ktv_survey s ON s.SurveyNr = g.SurveyNr
WHERE
    g.FarmerID = ?
";
            $query = $this->db->query($sql, array($FarmerID));
            $result['data'] = $query->result_array();
            return $result;
        } else {
            $sql = "
               SELECT SurveyNr as id, concat(SurveyNr,' - ',SurveyTxt) as surveya
               FROM ktv_survey
               ORDER BY SurveyNr";
            $query = $this->db->query($sql, array());
            $result['data'] = $query->result_array();
            return $result;
        }
    }

    public function readPartner($FarmerID, $district = '') {
        if ($FarmerID != '') {
            $left = "LEFT JOIN ktv_district_partner kdpp ON kdpp.PartnerID=kpp.PartnerID
               LEFT JOIN ktv_farmer kcf ON substr(kcf.VillageID,1,4)=kdpp.DistrictID";
            $add = "and FarmerID='$FarmerID'";
        }
        if ($district != '') {
            $left = 'LEFT JOIN ktv_district_partner kdpp ON kdpp.PartnerID=kpp.PartnerID
               LEFT JOIN ktv_district kd ON kdpp.DistrictID=kd.DistrictID';
            $add = "and District='$district'";
        }
        $sql = "
            select kpp.PartnerID as id, kpp.PartnerName as label
            from ktv_program_partner kpp
            $left
            where kpp.PartnerID>0 $add
            GROUP BY kpp.PartnerID";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getCPGPartner($FarmerID) {
        $sql = "SELECT
    f.`FarmerID`,
    f.`CPGid`,
    cp.`PartnerID`,
    pp.`PartnerFullName`
FROM `ktv_farmer` f
LEFT JOIN `ktv_cpg_partner`  cp ON cp.`CPGid` = f.`CPGid`
LEFT JOIN `ktv_program_partner` pp ON pp.`PartnerID` = cp.`PartnerID`
WHERE f.`FarmerID` = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function readFarmerFamilyNutrition($id) {
        // $sql = "
        //     select a.FamilyID, b.AnggotaName, b.AnggotaAge, b.AnggotaGender,b.HubunganKeluarga
        //     from ktv_cpg_batch_trainings_farmers a
        //     left join ktv_family b on a.FamilyID=b.FamilyID
        //     WHERE a.FarmerID=? AND a.FamilyID IS NOT NULL";
        $sql = "SELECT
                a.FamilyID,
                b.AnggotaName,
                b.AnggotaAge,
                b.AnggotaGender,
                b.HubunganKeluarga
              FROM
                ktv_cpg_batch_trainings_farmers a,
                ktv_family b
              WHERE a.FamilyID = b.FamilyID
                AND a.FarmerID = ?
                AND a.FamilyID IS NOT NULL
              UNION
              SELECT
                a.FamilyID,
                b.AnggotaName,
                b.AnggotaAge,
                b.AnggotaGender,
                b.HubunganKeluarga
              FROM
                ktv_kader_trainings_participants a,
                ktv_family b
              WHERE a.FamilyID = b.FamilyID
                AND a.FarmerID = ?
                AND a.FamilyID IS NOT NULL";
        $query = $this->db->query($sql, array($id, $id));
        $result = $query->result_array();
        return $result[0];
    }

    public function readCert($id, $garden) {
        $sql = "
            select *,kcf.FarmerID,? as GardenNr
            from ktv_farmer kcf
            left join ktv_certification kcc on kcc.FarmerID=kcf.FarmerID and kcc.GardenNr=?
            WHERE kcf.FarmerID=?";
        $query = $this->db->query($sql, array($garden, $garden, $id));
        $result = $query->result_array();
        return $result[0];
    }

    public function updateCert($farmer, $garden, $Certification, $CandidateSelection, $CertificationHolder, $first, $second, $third, $fourth, $firstICS, $secondICS, $thirdICS, $fourthICS) {
        $sql = "
         SELECT FarmerID FROM ktv_certification WHERE FarmerID=? and GardenNr=?";
        $sql_add = "
         INSERT INTO ktv_certification(FarmerID, GardenNr, Certification, CandidateSelection, CertificationHolder,FirstYear,
            SecondYear, ThirdYear, FourthYear, FirstYear_ICS, SecondYear_ICS, ThirdYear_ICS, FourthYear_ICS, DateCreated, DateUpdated, CreatedBy,LastModifiedBy)
         VALUES (?,?,?,?,?,?,?,?,?, ?,?,?,?,now(),now(),?,?)";
        $sql_update = "
         UPDATE ktv_certification SET Certification=?, CandidateSelection=?, CertificationHolderJenis=?, CertificationHolder=?,FirstYear=?, SecondYear=?,
            ThirdYear=?, FourthYear=?, FirstYear_ICS=?, SecondYear_ICS=?, ThirdYear_ICS=?, FourthYear_ICS=?, DateUpdated=now(), LastModifiedBy=?
         WHERE FarmerID=? and GardenNr=?";
        $query = $this->db->query($sql, array($farmer, $garden));
        $result = $query->result_array();
        if ($CertificationHolder != '') {
            $Holder = explode('|', $CertificationHolder);
        }

        if (empty($result)) {
            $query = $this->db->query($sql_add, array($farmer, $garden, $Certification, $CandidateSelection,
                $CertificationHolder, $first, $second, $third, $fourth, $firstICS, $secondICS, $thirdICS, $fourthICS, $_SESSION['userid'], $_SESSION['userid']));
        } else {
            $query = $this->db->query($sql_update, array($Certification, $CandidateSelection, $Holder[0], $Holder[1],
                $first, $second, $third, $fourth, $firstICS, $secondICS, $thirdICS, $fourthICS, $_SESSION['userid'], $farmer, $garden));
        }

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function readTrainings($id) {
        $sql = "SELECT
                    a.Batch,
                    a.CpgTrainings,
                    a.TrainingStart,
                    a.TrainingEnd,
                    a.TrainingDays,
                    a.TYPE,
                    detail,
                    a.UserCreated,
                    a.LastUpdatedBy
                FROM
                    (SELECT
                        kcf.FarmerID,kcf.FarmerName,kcf.Gender,
                        kcbt.CPGtrainingsID,kct.CpgTrainings,
                        kcbt.CpgBatchID AS Batch, kcbt.TrainingStart,kcbt.TrainingEnd, kcbt.TrainingDays, 'FFS' AS TYPE,
                        concat('/cpg/cpg/index/',substr(kcbt.CPGid,1,2),'/',District,'-',kcbt.CPGid,'-',kcbt.CpgBatchTrainingID) as detail,
                        s.UserRealName as UserCreated,
                        u.UserRealName as LastUpdatedBy
                    FROM
                        ktv_cpg_batch_trainings kcbt
                        INNER JOIN ktv_cpg_trainings kct ON kcbt.CPGtrainingsID=kct.CpgTrainingsID
                        INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID = kcbtf.CpgBatchTrainingID
                        LEFT JOIN ktv_farmer kcf ON kcbtf.FarmerID = kcf.FarmerID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = substr(kcbt.CPGid,1,4)
                        LEFT JOIN sys_user s ON kcbt.CreatedBy = s.UserId
                        LEFT JOIN sys_user u ON kcbt.LastModifiedBy = u.UserId
                    UNION ALL
                    SELECT
                        kcf.FarmerID, kcf.FarmerName,kcf.Gender,
                        kkt.CPGtrainingsID,kct.CpgTrainings,
                        kkt.CpgBatchID AS Batch, kkt.TrainingStart,kkt.TrainingEnd, kkt.TrainingDays,'PK' AS TYPE,
                        concat('/training/kader/index/',TrainingProvince,'/',kkt.CpgKaderTrainingID) as detail,
                        s.UserRealName as UserCreated,
                        u.UserRealName as LastUpdatedBy
                    FROM
                        ktv_kader_trainings kkt
                        INNER JOIN ktv_cpg_trainings kct ON kkt.CPGtrainingsID=kct.CpgTrainingsID
                        INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
                        LEFT JOIN ktv_farmer kcf ON kktp.FarmerID = kcf.FarmerID
                        LEFT JOIN sys_user s ON kkt.CreatedBy = s.UserId
                        LEFT JOIN sys_user u ON kkt.LastModifiedBy = u.UserId
                ) a
                WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result;
    }

    //antara
    public function readAntara($jenis, $farmerId) {
        if ($jenis == 'Kebun') {
            $sql = "
            select GardenNr as garden,concat(a.SurveyNr,' - ',SurveyTxt) as survey,date(DateCollection) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_farmer_garden a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY GardenNr,a.SurveyNr";
        } elseif ($jenis == 'Paska Panen') {
            $sql = "
            select concat(a.SurveyNr,'-',SurveyTxt) as survey,date(DateCollection) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_farmer_post_harvest a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY a.SurveyNr";
        } elseif ($jenis == 'Saving Pilot') {
            $sql = "
            select concat(a.SurveyNr,'-',SurveyTxt) as survey,date(a.InterviewDate) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_saving_pilot a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY a.SurveyNr";
        } elseif ($jenis == 'PPI 2010') {
            $sql = "
            select concat(a.SurveyNr,'-',b.SurveyTxt) as survey,date(InterviewDate) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_ppiscore a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY a.SurveyNr";
        } elseif ($jenis == 'PPI 2012') {
            $sql = "
            select concat(a.SurveyNr,'-',SurveyTxt) as survey,date(InterviewDate) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_ppiscore2012 a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY a.SurveyNr";
        } elseif ($jenis == 'Nutrisi') {
            $sql = "
            select concat(a.SurveyNr,'-',SurveyTxt) as survey,date(InterviewDate) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_nutrition a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY a.SurveyNr";
        } elseif ($jenis == 'AFF') {
            $sql = "
            select a.SurveyNr,concat(a.SurveyNr,'-',SurveyTxt) as survey,date(InterviewDate) as date,concat(a.SurveyNr,'|',a.SurveyNr) as surveyid
            from ktv_farmer_financial a
            left join ktv_survey b ON a.SurveyNr=b.SurveyNr
            WHERE FarmerID = ? AND (a.StatusCode != 'nullified' OR a.StatusCode IS NULL)
            ORDER BY a.SurveyNr";
        }

        $query = $this->db->query($sql, array($farmerId));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function isGardenCertified($FarmerID, $GardenNr, $SurveyNr) {
        $query = $this->db->get_where('ktv_certification', compact('FarmerID', 'GardenNr', 'SurveyNr'));
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function deleteSurveyFarmer($garden, $survey, $jenis, $farmerId, $user) {
        if ($jenis == 'Kebun') {
            if ($this->isGardenCertified($farmerId, $garden, $survey) === false) {

                // $sql_update = "UPDATE ktv_farmer_garden SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE FarmerId=? AND GardenNr = ? AND SurveyNr=?";
                // $this->db->query($sql_update, array($farmerId, $garden, $survey));
                // copy to his_table
                $sql_copy = "INSERT INTO his_ktv_farmer_garden (DateHistory, DeleteBy, FarmerID, OldFarmerID, GardenNr, SurveyNr, DateCollection, Latitude, LatDeg, LatMin, LatSec, Longitude, LongDeg, LongMin, LongSec, Elevation, OwnershipCocoa, TahunTanamanCocoa, GardenDistance, GardenHaUnCertified, GardenHaPolygon, Production, PanenBiasaMonths, PanenBiasaPanenMonth, PanenBiasaKg, PanenTrekMonths, PanenTrekPanenMonth, PanenTrekKg, PanenRayaMonths, PanenRayaPanenMonth, PanenRayaKg, TimeHarvestBiasa, TimeHarvestTrek, TimeHarvestRaya, LandOwner, LandCertificate, PohonTBM, PohonTM, PohonRehab, GraftedTrees, ReplantedTrees, RoadCondition, `Comment`, TSH858, RCC70, RCC71, RCC72, RCC73, Hybrid, S1, S2, S3, ICRRI3, ICRRI4, ICRRI5, M01, M06, THR, RCL, J45, CloneLain, TSH858Nr, RCC70Nr, RCC71Nr, RCC72Nr, RCC73Nr, LokalNr, S1Nr, S2Nr, S3Nr, ICRRI3Nr, ICRRI4Nr, ICRRI5Nr, M01Nr, M06Nr, THRNr, RCLNr, J45Nr, CloneLainNr, Gamal, Kelapa, Durian, Pinang, Karet, JackFruit, Lamtoro, Mahoni, Pisang, Rambutan, Sukun, Jengkol, Sengon, Petai, Jabon, Uru, Biti, Jati, Jeruk, Jambu, Kedondong, Cempedak, Manggis, Pepaya, Alpukat, Kemiri, JambuMente, Kapok, Pala, Aren, Sawit, Cengkeh, Mangga, Langsat, ShadeLain, GamalNr, KelapaNr, DurianNr, PinangNr, KaretNr, JackFruitNr, LamtoroNr, MahoniNr, PisangNr, RambutanNr, CengkehNr, SawitNr, ArenNr, ManggaNr, LangsatNr, PalaNr, KemiriNr, JambuMenteNr, KapokNr, AlpukatNr, SukunNr, PepayaNr, ManggisNr, JerukNr, JambuNr, KedondongNr, CempedakNr, JatiNr, BitiNr, UruNr, JabonNr, PetaiNr, JengkolNr, SengonNr, ShadeLainNr, ShadeTreesNr, TimeHarvest, HarvestAwal, HarvestMasak, HarvestHama, PruningPlants, FrequentPruning, HighPruning, PruningProtectPlants, FrequentPruningProtect, CleanSkin, HowToCleanSkin, OrganicKotoran, OrganicResidu, OrganicMembeli, TidakMemakaiOrganic, Urea, TSP, NPK, KCL, TidakMemakaiKimia, FrequentFertilizationOrganic, DoseFertilizerOrganic, FrequentFertilizationKimia, DoseFertilizerKimia, PakaiKompos, FrequentFertilizationKompos, DoseFertilizerKompos, FrKomposKandang, FrKomposCair, FrKomposGranula, DoseKomposKandang, DoseKomposCair, DoseKomposGranula, FrUrea, FrZa, FrTsp, FrNpk, FrKcl, DoUrea, DoZa, DoTsp, DoNpk, DoKcl, FrLain, DoLain, KomposTBM, KomposTM, KomposTR, PupukTBM, PupukTM, PupukTR, KimiaDana, KimiaSupplier, KimiaDilatih, KimiaTidakSuka, KimiaTidakTersedia, KimiaLain, HamaBPK, HamaHelopeltis, HamaBatang, PenyakitKanker, PenyakitBusuk, PenyakitUpas, PenyakitAkar, PenyakitVSD, PenyakitAntraknose, Herbisida, MerekHerbisida, FrequentHerbisida, DoseHerbisida, Herbisida1, Herbisida2, Herbisida3, Herbisida4, Herbisida5, Herbisida6, Herbisida7, Herbisida8, Herbisida9, Herbisida10, Herbisida11, Herbisida12, Herbisida13, Herbisida14, Herbisida15, Herbisida16, Herbisida17, Herbisida18, Herbisida19, Herbisida20, Herbisida21, Herbisida22, Herbisida23, Herbisida24, Herbisida25, Herbisida26, Herbisida27, Herbisida28, Herbisida29, Insectisida, MerekInsectisida, FrequentInsectisida, DoseInsectisida, Insectisida1, Insectisida2, Insectisida3, Insectisida4, Insectisida5, Insectisida6, Insectisida7, Insectisida8, Insectisida9, Insectisida10, Insectisida11, Insectisida12, Insectisida13, Insectisida14, Insectisida15, Insectisida16, Insectisida17, Insectisida18, Insectisida19, Insectisida20, Insectisida21, Insectisida22, Insectisida23, Fungisida, MerekFungisida, FrequentFungisida, DoseFungisida, Fungisida1, Fungisida2, Fungisida3, Fungisida4, Fungisida5, Fungisida6, Fungisida7, Fungisida8, Fungisida9, Fungisida10, Fungisida11, Fungisida12, Fungisida13, APD, TempatSimpanPestisida, BuangKemasanPestisida, DateCreated, CreatedBy, DateUpdated, DateSynced, LastModifiedBy, StatusCode, isValid, StatusGPS, ApprovedByME, ApprovedByGO, ApprovedByDC, CommentValid, TopGraftedTrees, GraftedTreesTahun, TopGraftedTreesTahun, ReplantedTreesTahun, isCertification, Certification, StatusAudit, DateRevisionAudit, CommentAudit, RecommendationAudit, RACertQuestion1, RACertQuestion2, RACertQuestion3, RACertQuestion4, RACertQuestion5, RACertQuestion6, RACertQuestion7, RACertQuestion8, RACertQuestion9, RACertQuestion10, RehabTrees, RehabTreesTahun, InsetTrees, InsetTreesTahun, FrFoliar, DoFoliar, DateSync
            )
            SELECT NOW(), ?, g.* FROM ktv_farmer_garden g
            WHERE
                g.FarmerID = ? AND g.GardenNr = ? AND g.SurveyNr = ?";
                $this->db->query($sql_copy, array($user, $farmerId, $garden, $survey));
                $sql = "DELETE FROM ktv_farmer_garden WHERE GardenNr=$garden and SurveyNr=? and FarmerId=?";
            } else {
                $results['success'] = false;
                $results['message'] = "Can not delete certified garden";
                return $results;
            }
        } elseif ($jenis == 'Paska Panen') {
            //$sql = "DELETE FROM ktv_farmer_post_harvest WHERE SurveyNr=? and FarmerId=?";
            //$sql = "UPDATE ktv_farmer_post_harvest SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE SurveyNr=? and FarmerId=? LIMIT 1";

            $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_farmer_post_harvest` (
                      `DateHistory`,
                      `DeleteBy`,
                      `FarmerID`,
                      `OldFarmerID`,
                      `SurveyNr`,
                      `DateCollection`,
                      `AnggotaKerjaKebun`,
                      `BuruhSeasonal`,
                      `BuruhSeasonalRupiah`,
                      `BuruhSeasonalPersen`,
                      `BuruhFulltime`,
                      `BuruhFulltimeRupiah`,
                      `BuruhFulltimePersen`,
                      `Fermentation`,
                      `FermentationDays`,
                      `SunDryingSemen`,
                      `DryingAlat`,
                      `DryingDays`,
                      `CocoaBuyers`,
                      `NoFermentation`,
                      `Sortasi`,
                      `NoSortasi`,
                      `SunDryingAspal`,
                      `JemurYesNo`,
                      `TidakJemur`,
                      `SunDryingAlas`,
                      `BeanDryHygienic`,
                      `DryMoistureStandard`,
                      `ImplementBeanRemainDry`,
                      `DateCreated`,
                      `CreatedBy`,
                      `DateUpdated`,
                      `LastModifiedBy`,
                      `StatusCode`,
                      `isValid`,
                      `ApprovedByME`,
                      `ApprovedByGO`,
                      `ApprovedByDC`,
                      `CommentValid`,
                      `DateSynced`,
                      `Distance`,
                      `Comment`,
                      `AdaProduksi`,
                      `AntarSendiri`,
                      `DateSync`,
                      `uid`
                    )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_farmer_post_harvest a
                WHERE
                    a.FarmerID = ?
                    AND a.SurveyNr = ?
                LIMIT 1
                ";
                $this->db->query($sql, array($_SESSION['userid'], $farmerId, $survey));

                $sql = "DELETE FROM ktv_farmer_post_harvest WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
                $this->db->query($sql, array($farmerId, $survey));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "DELETED";
            }
            return $results;

        } elseif ($jenis == 'Saving Pilot') {
            //$sql = "DELETE FROM ktv_saving_pilot WHERE SurveyNr=? and FarmerID=?";
            //$sql = "UPDATE ktv_saving_pilot SET StatusCode = 'nullified' WHERE SurveyNr=? and FarmerID=? LIMIT 1";

            $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_saving_pilot` (
                      `DateHistory`,
                      `DeleteBy`,
                      `FarmerID`,
                      `SurveyNr`,
                      `InterviewDate`,
                      `Age`,
                      `MarriedYesNo`,
                      `FamilyMembers`,
                      `LandSizeHa`,
                      `AmountCocoaIncome`,
                      `AmountOtherIncome`,
                      `SavingYesNo`,
                      `AmountSaving`,
                      `LoanYesNo`,
                      `AccountNumber`,
                      `DateSync`,
                      `StatusCode`,
                      `DateCreated`,
                      `CreatedBy`,
                      `DateUpdated`,
                      `LastModifiedBy`
                    )
                    SELECT
                        NOW(), ?, a.*
                    FROM
                        ktv_saving_pilot a
                    WHERE
                        a.FarmerID = ?
                        AND a.SurveyNr = ?
                    LIMIT 1";
            $this->db->query($sql, array($_SESSION['userid'], $farmerId, $survey));

            $sql = "DELETE FROM ktv_saving_pilot WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
            $this->db->query($sql, array($farmerId, $survey));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "DELETED";
            }
            return $results;

        } elseif ($jenis == 'PPI 2010') {
            //$sql = "DELETE FROM ktv_ppiscore WHERE SurveyNr=? and FarmerId=?";
            //$sql = "UPDATE ktv_ppiscore SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE SurveyNr=? and FarmerId=? LIMIT 1";

            $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_ppiscore` (
                  `DateHistory`,
                  `DeleteBy`,
                  `FarmerID`,
                  `OldFarmerID`,
                  `SurveyNr`,
                  `PrePostSurvey`,
                  `InterviewDate`,
                  `Householdmembers`,
                  `Schooling`,
                  `Working`,
                  `DrinkingWater`,
                  `ToiletFacility`,
                  `HouseFloor`,
                  `HouseCeiling`,
                  `Refrigerator`,
                  `Motorcycle`,
                  `Television`,
                  `Score`,
                  `National`,
                  `1.25/day`,
                  `2.5/day`,
                  `DateCreated`,
                  `CreatedBy`,
                  `DateUpdated`,
                  `DateSynced`,
                  `LastModifiedBy`,
                  `StatusCode`,
                  `isValid`,
                  `ApprovedByME`,
                  `ApprovedByGO`,
                  `ApprovedByDC`,
                  `CommentValid`
                )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_ppiscore a
                WHERE
                    a.FarmerID = ?
                    AND a.SurveyNr = ?
                LIMIT 1
                ";
            $this->db->query($sql, array($_SESSION['userid'], $farmerId, $survey));

            $sql = "DELETE FROM ktv_ppiscore WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
            $this->db->query($sql, array($farmerId, $survey));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "DELETED";
            }
            return $results;

        } elseif ($jenis == 'PPI 2012') {
            //$sql = "DELETE FROM ktv_ppiscore2012 WHERE SurveyNr=? and FarmerId=?";

            $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_ppiscore2012` (
                      `DateHistory`,
                      `DeleteBy`,
                      `FarmerID`,
                      `OldFarmerID`,
                      `SurveyNr`,
                      `InterviewDate`,
                      `Householdmembers`,
                      `Schooling`,
                      `Education`,
                      `Employment`,
                      `HouseFloor`,
                      `ToiletFacility`,
                      `CookingFuel`,
                      `GasCylinder`,
                      `Refrigerator`,
                      `Motorcycle`,
                      `Score`,
                      `National`,
                      `1.25/day`,
                      `2.5/day`,
                      `DateCreated`,
                      `CreatedBy`,
                      `DateUpdated`,
                      `DateSynced`,
                      `LastModifiedBy`,
                      `StatusCode`,
                      `isValid`,
                      `ApprovedByME`,
                      `ApprovedByGO`,
                      `ApprovedByDC`,
                      `CommentValid`,
                      `DateSync`,
                      `uid`
                    )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_ppiscore2012 a
                WHERE
                    a.FarmerID = ?
                    AND a.SurveyNr = ?
                LIMIT 1
            ";
            $this->db->query($sql, array($_SESSION['userid'], $farmerId, $survey));

            $sql = "DELETE FROM ktv_ppiscore2012 WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
            $this->db->query($sql, array($farmerId, $survey));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "DELETED";
            }
            return $results;

            //$sql = "UPDATE ktv_ppiscore2012 SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE SurveyNr=? and FarmerId=? LIMIT 1";
        } elseif ($jenis == 'Nutrisi') {
            //$sql = "DELETE FROM ktv_nutrition WHERE SurveyNr=? and FarmerId=?";

            $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_nutrition` (
                  `DateHistory`,
                  `DeleteBy`,
                  `FarmerID`,
                  `OldFarmerID`,
                  `InterviewDate`,
                  `SurveyNr`,
                  `Season`,
                  `HaveVegetableGarden`,
                  `IsFamilyGarden`,
                  `IsCommmercialGarden`,
                  `KebunPanjang`,
                  `KebunLebar`,
                  `KebunArea`,
                  `KbBayam`,
                  `KbCabai`,
                  `KbKacangPanjang`,
                  `KbKangkung`,
                  `KbSawi`,
                  `KbTerong`,
                  `KbTomat`,
                  `KbKelor`,
                  `KbSingkong`,
                  `KbLabu`,
                  `KbKatuk`,
                  `KbKambing`,
                  `KbSapi`,
                  `KbBebek`,
                  `KbAyam`,
                  `KbIkan`,
                  `KbDomba`,
                  `KbKerbau`,
                  `KbBabi`,
                  `ComKebunPanjang`,
                  `ComKebunLebar`,
                  `ComKebunArea`,
                  `ComKbBayam`,
                  `ComKbCabai`,
                  `ComKbKacangPanjang`,
                  `ComKbKangkung`,
                  `ComKbSawi`,
                  `ComKbTerong`,
                  `ComKbTomat`,
                  `ComKbKelor`,
                  `ComKbSingkong`,
                  `ComKbLabu`,
                  `ComKbKatuk`,
                  `ComKbKambing`,
                  `ComKbSapi`,
                  `ComKbBebek`,
                  `ComKbAyam`,
                  `ComKbIkan`,
                  `ComKbDomba`,
                  `ComKbKerbau`,
                  `ComKbBabi`,
                  `VegetableUtilization`,
                  `ComVegetableUtilization`,
                  `HaveFishPond`,
                  `FishPondLength`,
                  `FishPondWidth`,
                  `FishPondArea`,
                  `fsNila`,
                  `fsCarp`,
                  `fsCatfish`,
                  `fsTilapia`,
                  `fsOthers`,
                  `FishUtilization`,
                  `EatRaisedFish`,
                  `aSagu`,
                  `aNasi`,
                  `aMie`,
                  `aJagung`,
                  `aRoti`,
                  `bUbiJalarKuning`,
                  `bSingkongKuning`,
                  `bWortel`,
                  `bLabu`,
                  `cUbiJalarPutih`,
                  `cSingkongPutih`,
                  `cTalas`,
                  `cKentang`,
                  `dBayam`,
                  `dDaunMelinjo`,
                  `dDaunPepaya`,
                  `dDaunSingkong`,
                  `dKangkung`,
                  `dSawi`,
                  `eKacangPanjang`,
                  `eTomat`,
                  `eTerong`,
                  `fJambuMerah`,
                  `fMangga`,
                  `fPepaya`,
                  `gJambuAir`,
                  `gKelapa`,
                  `gPisang`,
                  `gRambutan`,
                  `gSemangka`,
                  `gSalak`,
                  `hJeroan`,
                  `hHati`,
                  `iAyam`,
                  `iBebek`,
                  `iKambing`,
                  `iKerbau`,
                  `iSapi`,
                  `iLainnya`,
                  `jAyam`,
                  `jBebek`,
                  `jEntok`,
                  `jPuyuh`,
                  `kCumiCumi`,
                  `kIkan`,
                  `kIkanTeri`,
                  `kKepiting`,
                  `kKerang`,
                  `kUdang`,
                  `lAirTahuSusuKedelai`,
                  `lSausKacang`,
                  `lTahu`,
                  `lTempe`,
                  `lKacang`,
                  `lKwaci`,
                  `mKeju`,
                  `mSusu`,
                  `nMinyakGoreng`,
                  `nMentega`,
                  `nSantan`,
                  `Score`,
                  `WDDSRespondent`,
                  `cerRice`,
                  `cerNoodles`,
                  `cerCorn`,
                  `cerCerealBubur`,
                  `cerBread`,
                  `cerWheatFlour`,
                  `cerSorghum`,
                  `cerMillet`,
                  `wtrWhiteCassava`,
                  `wtrTaro`,
                  `wtrPotato`,
                  `wtrSago`,
                  `wtrSweetPotato`,
                  `wtrPlantain`,
                  `wtrYam`,
                  `dgSpinach`,
                  `dgCassavaLeaf`,
                  `dgWaterSpinach`,
                  `dgMelinjoLeaf`,
                  `dgPapayaLeaf`,
                  `dgPumpkinLeaf`,
                  `dgLongBeansLeaf`,
                  `dgMoringaLeaf`,
                  `dgLeafMustard`,
                  `dgSweetPotatoLeaf`,
                  `dgPakis`,
                  `dgKatukLeaf`,
                  `dgTaroLeaf`,
                  `dgOthers`,
                  `rfMangoRipe`,
                  `rfPapayaRipe`,
                  `rfPassionFruit`,
                  `rfEggplant`,
                  `rfOrangeBanana`,
                  `rvtSweetPotato`,
                  `rvtYellowCassava`,
                  `rvtCarrot`,
                  `rvtOrangeSquash`,
                  `rvtPumpkin`,
                  `rvtRedPaprika`,
                  `ofBanana`,
                  `ofGuava`,
                  `ofCoconut`,
                  `ofLemon`,
                  `ofWaterApple`,
                  `ofDurian`,
                  `ofAvocado`,
                  `ofPineapple`,
                  `ofSoursop`,
                  `ofKedondong`,
                  `ofSawo`,
                  `ofWatermelon`,
                  `ofLangsat`,
                  `ofMangosteen`,
                  `ofOthers`,
                  `ovLongbeans`,
                  `ovEggplant`,
                  `ovBreadfruit`,
                  `ovJackfruit`,
                  `ovTomato`,
                  `ovWhiteCabbage`,
                  `ovCucumber`,
                  `ovChayote`,
                  `ovOnion`,
                  `ovBambooShoots`,
                  `ovLuffa`,
                  `ovBitterMelon`,
                  `ovPapaya`,
                  `ovMushrooms`,
                  `ovOthers`,
                  `omOffal`,
                  `omLiver`,
                  `omLungs`,
                  `omKidney`,
                  `omHeart`,
                  `meChicken`,
                  `meDuck`,
                  `meWildDuck`,
                  `meQuail`,
                  `meBeef`,
                  `meLamb`,
                  `meGoat`,
                  `meBuffalo`,
                  `mePork`,
                  `fasFish`,
                  `fasSquid`,
                  `fasCrab`,
                  `fasShellfish`,
                  `fasShrimp`,
                  `fasOctopus`,
                  `egChickenEgg`,
                  `egDuckEgg`,
                  `egQuailEgg`,
                  `eWildDuck`,
                  `lnsTofu`,
                  `lnsTempe`,
                  `lnsTofuWater`,
                  `lnsPeanutSauce`,
                  `lnsMungBean`,
                  `lnsSoybean`,
                  `lnsJengkol`,
                  `lnsPetai`,
                  `lnsCowpea`,
                  `lnsCashew`,
                  `mdpCheese`,
                  `mdpMilk`,
                  `mdpYoghurt`,
                  `mdpOthers`,
                  `WDDSScore`,
                  `MothersRespondent`,
                  `HaveChildren`,
                  `NrOfChildren`,
                  `ChildAgeYear`,
                  `ChildAgeMonth`,
                  `GivenBreastfeed`,
                  `StartGivenBreastfeed`,
                  `TreatmentColustrum`,
                  `GivenFoodBesidesASI`,
                  `fwFormulaMilk`,
                  `fwNonFormulaMilk`,
                  `fwWater`,
                  `fwSugarWater`,
                  `fwStarchWater`,
                  `fwCoconutWater`,
                  `fwFruitJuice`,
                  `fwSweetTea`,
                  `fwHoney`,
                  `fwMashedBanana`,
                  `fwMashedRice`,
                  `fwOthers`,
                  `WhenGivenFoodBesidesASI`,
                  `ChildrenMeal`,
                  `ChildrenASI`,
                  `Children3MonthASI`,
                  `ChildrenNrGiveASI`,
                  `ChildrenNrGiveMeal`,
                  `ChildrenGiveKolestrum`,
                  `MotherPregnant2Years`,
                  `MotherPregnantEat`,
                  `DateCreated`,
                  `CreatedBy`,
                  `DateUpdated`,
                  `LastModifiedBy`,
                  `DateSynced`,
                  `StatusCode`,
                  `isValid`,
                  `ApprovedByME`,
                  `ApprovedByGO`,
                  `ApprovedByDC`,
                  `CommentValid`,
                  `DateSync`,
                  `uid`
                )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_nutrition a
                WHERE
                    a.FarmerID = ?
                    AND a.SurveyNr = ?
                LIMIT 1
            ";
            $this->db->query($sql, array($_SESSION['userid'], $farmerId, $survey));

            $sql = "DELETE FROM ktv_nutrition WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
            $this->db->query($sql, array($farmerId, $survey));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "DELETED";
            }
            return $results;

            //$sql = "UPDATE ktv_nutrition SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE SurveyNr=? and FarmerId=? LIMIT 1";
        } elseif ($jenis == 'AFF') {
            //$sql = "DELETE FROM ktv_farmer_financial WHERE SurveyNr=? and FarmerId=?";
            $arrTmp = explode("|", $survey);
            $survey = $arrTmp[0];

            $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_farmer_financial` (
                  `DateHistory`,
                  `DeleteBy`,
                  `FarmerID`,
                  `SurveyNr`,
                  `InterviewDate`,
                  `isValid`,
                  `Account`,
                  `FamilyAccount`,
                  `AccountTypeTabungan`,
                  `AccountTypeDeposito`,
                  `AccountTypeKoran`,
                  `AccountTypeLainnya`,
                  `AccountHolderFarmer`,
                  `AccountHolderName`,
                  `AccountBankID`,
                  `AccountBankName`,
                  `AccountBankBranch`,
                  `AccountNumber`,
                  `AccountNoDetails`,
                  `SavingInCooperatives`,
                  `DepositWithdrawnMoneyLast12m`,
                  `AccountFeesToPay`,
                  `AccountInterestRate`,
                  `MoneyUsageHarian`,
                  `MoneyUsageTabung`,
                  `MoneyUsageInvestasi`,
                  `MoneyUsageEmas`,
                  `MoneyUsageKonsumsi`,
                  `NotSavingJauh`,
                  `NotSavingTidakBeruang`,
                  `NotSavingBiayaTinggi`,
                  `NotSavingTidakPercaya`,
                  `NotSavingAdaMenabung`,
                  `NotSavingLainnya`,
                  `SavingUnitRumah`,
                  `SavingUnitBank`,
                  `SavingUnitKoperasi`,
                  `SavingUnitPedagang`,
                  `SavingUnitArisan`,
                  `SavingUnitOrang`,
                  `SavingUnitLembaga`,
                  `SavingUnitMeminjamkan`,
                  `DistanceSavingLocation`,
                  `AmountSaving`,
                  `AfterGFPOpenAccount`,
                  `AfterGFPStartSaving`,
                  `AfterGFPRecordRevenue`,
                  `AfterGFPFinancialSituation`,
                  `AfterGFPControlFinancial`,
                  `AfterGFPNewLoans`,
                  `AfterGFPDidNotChange`,
                  `FutureReasonSekolah`,
                  `FutureReasonRumahTangga`,
                  `FutureReasonSumbangan`,
                  `FutureReasonDarurat`,
                  `FutureReasonKesehatan`,
                  `FutureReasonInvestasiKebun`,
                  `FutureReasonInvestasiLain`,
                  `FutureReasonRumah`,
                  `FutureReasonLahan`,
                  `FutureReasonKendaraan`,
                  `FutureReasonHaji`,
                  `FutureReasonPensiun`,
                  `FutureReasonLain`,
                  `ImportantFactorKemanan`,
                  `ImportantFactorLikuiditas`,
                  `ImportantFactorAksesibilitas`,
                  `ImportantFactorKepercayaan`,
                  `ImportantFactorBiaya`,
                  `ImportantFactorBunga`,
                  `ImportantFactorLain`,
                  `LoanYesNo`,
                  `AssistanceFromBU`,
                  `AmountCurrentLoan`,
                  `AmountOutsCurrentLoan`,
                  `LoanUnitTengkulak`,
                  `LoanUnitKeluarga`,
                  `LoanUnitRentenir`,
                  `LoanUnitBank`,
                  `LoanUnitKoperasi`,
                  `LoanUnitMasjid`,
                  `LoanUnitLainnya`,
                  `PreviousLoan`,
                  `CollateralCurrentLoan`,
                  `EasyCurrentLoan`,
                  `DisburseIntervalCurrentLoan`,
                  `RepaymentScheduleCurrentLoan`,
                  `EasyGetNewLoan`,
                  `UsageCurrentLoanHarian`,
                  `UsageCurrentLoanSekolah`,
                  `UsageCurrentLoanRumahTangga`,
                  `UsageCurrentLoanSumbangan`,
                  `UsageCurrentLoanHutang`,
                  `UsageCurrentLoanDarurat`,
                  `UsageCurrentLoanKesehatan`,
                  `UsageCurrentLoanInvestasiKebun`,
                  `UsageCurrentLoanInvestasiLain`,
                  `UsageCurrentLoanRumah`,
                  `UsageCurrentLoanLahan`,
                  `UsageCurrentLoanKendaraan`,
                  `UsageCurrentLoanHaji`,
                  `UsageCurrentLoanPensiun`,
                  `UsageCurrentLoanLainnya`,
                  `TerminatedLoan`,
                  `MoneyToRepayLoanPenghasilan`,
                  `MoneyToRepayLoanPinjaman`,
                  `MoneyToRepayLoanTanah`,
                  `MoneyToRepayLoanTernak`,
                  `MoneyToRepayLoanDeposito`,
                  `MoneyToRepayLoanLainnya`,
                  `ProfitSharingLoan`,
                  `ResponsibilityLoan`,
                  `WorryToRepayLoan`,
                  `DifficultCoverExpenses`,
                  `PostponeExpensesSewaRumah`,
                  `PostponeExpensesKebun`,
                  `PostponeExpensesMakanan`,
                  `PostponeExpensesKesehatan`,
                  `PostponeExpensesSosial`,
                  `PostponeExpensesListrik`,
                  `PostponeExpensesPendidikan`,
                  `PostponeExpensesSandang`,
                  `PostponeExpensesAngsuran`,
                  `PostponeExpensesLainnya`,
                  `DifficultSocialContributions`,
                  `MoneyUrgentExpensesTabungan`,
                  `MoneyUrgentExpensesMeminjamKeluarga`,
                  `MoneyUrgentExpensesMeminjamTengkulak`,
                  `MoneyUrgentExpensesMenjual`,
                  `MoneyUrgentExpensesLainnya`,
                  `CostUnsubsidizedFertilizer`,
                  `OtherIncome`,
                  `PensionPlan`,
                  `OtherIncomeRegular`,
                  `SourceOtherIncomeGajiTetap`,
                  `SourceOtherIncomeGajiPasangan`,
                  `SourceOtherIncomeUsaha`,
                  `SourceOtherIncomeFamily`,
                  `SourceOtherIncomeLainnya`,
                  `AmountOtherIncome`,
                  `CocoaProfitableBusiness`,
                  `LoanBetterThanSaving`,
                  `UnsubsidizedFertilizerProfitable`,
                  `HighInterestRate`,
                  `LoanWithTrader`,
                  `BetterWetDriedBeans`,
                  `GoodLoanClient`,
                  `TrustGroupMembers`,
                  `RepayLoanGroupMember`,
                  `TrustBank`,
                  `CocoaFarmPayExpenses`,
                  `DiscipilinedSaveMoney`,
                  `TradersRich`,
                  `CollateralOfferedBank`,
                  `ManyCocoaFarmSale`,
                  `SatisfiedCocoaBusiness`,
                  `PayCocoaBetterInterest`,
                  `NeedLoan`,
                  `MobilePhone`,
                  `LoanAnalysisKnowledge`,
                  `IslamicFinancialAwareness`,
                  `LearnToSaveMoney`,
                  `CocoaPriceTodayInfo`,
                  `CocoaPriceToday`,
                  `ReasonNotHavePhoneTidakButuh`,
                  `ReasonNotHavePhoneMahal`,
                  `ReasonNotHavePhoneSinyal`,
                  `ReasonNotHavePhoneLainnya`,
                  `ValueCocoaFarm`,
                  `InsuranceKnowledge`,
                  `PastNowInsurance`,
                  `InsuranceTypeMotor`,
                  `InsuranceTypePanen`,
                  `InsuranceTypeBanjir`,
                  `InsuranceTypeKemarau`,
                  `InsuranceTypeMobil`,
                  `InsuranceTypeKesehatan`,
                  `InsuranceTypeJiwa`,
                  `InsuranceTypeLainnya`,
                  `GetLoanToBuyLand`,
                  `GetLoanInvestCocoa`,
                  `GetLoanInvestOtherBusiness`,
                  `GetLoanUsedOthers`,
                  `SellCocoaMoneyUsage`,
                  `GiveCocoaLandWhenOlder`,
                  `NeedFinancialTraining`,
                  `FinancialTrainingDeposit`,
                  `FinancialTrainingLoan`,
                  `FinancialTrainingRecording`,
                  `FinancialTrainingCashFlowPlan`,
                  `FinancialTrainingPersonalRecom`,
                  `FinancialTrainingLoanSchemeGov`,
                  `InfoFinancialTrainingChairmanFarmerGroup`,
                  `InfoFinancialTrainingScppStaff`,
                  `InfoFinancialTrainingFarmerCoop`,
                  `InfoFinancialTrainingBank`,
                  `InfoFinancialTrainingOthers`,
                  `StatusCode`,
                  `DateCreated`,
                  `CreatedBy`,
                  `DateUpdated`,
                  `LastModifiedBy`,
                  `DateSync`,
                  `uid`
                )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_farmer_financial a
                WHERE
                    a.FarmerID = ?
                    AND a.SurveyNr = ?
                LIMIT 1
            ";
            $this->db->query($sql, array($_SESSION['userid'], $farmerId, $survey));

            $sql = "DELETE FROM ktv_farmer_financial WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
            $this->db->query($sql, array($farmerId, $survey));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete record";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "DELETED";
            }
            return $results;

            //$sql = "UPDATE ktv_farmer_financial SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE SurveyNr=? and FarmerId=? LIMIT 1";
        }
        $query = $this->db->query($sql, array($survey, $farmerId));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

//AFF
    public function readSurveyAffs($id, $jenis) {
        if ($jenis == 'add') {
            $where = 'WHERE b.SurveyNr is null';
        }

        $sql = "SELECT concat(a.SurveyNr,'|',IFNULL(b.SurveyNr,'Tambah Baru')) as id,concat(a.SurveyNr,' - ',a.SurveyTxt) as label
            FROM ktv_survey a
            LEFT JOIN ktv_farmer_financial b ON a.SurveyNr=b.SurveyNr and FarmerID=?
            $where
            ORDER BY b.SurveyNr desc,a.SurveyNr";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function readFarmerAff($id, $nr) {
//        DATE_FORMAT(a.InterviewDate,'%Y-%m-%d')
        $sql = "select a.*,concat(c.SurveyNr, ' - ', c.SurveyTxt) AS label_survey,a.InterviewDate,FarmerName as PersonNm,
              a.InterviewDate
            from ktv_farmer_financial a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            LEFT JOIN ktv_survey c ON a.SurveyNr = c.SurveyNr
            WHERE a.FarmerID=? and a.SurveyNr=?";
        $query = $this->db->query($sql, array($id, $nr));
        $result = $query->result_array();
        //return $result[0];
        return array('success' => true, 'data' => $result[0]);
    }

    public function readFarmerAffPrevData($id, $nr) {
        $xplod = explode("|", $nr);
        $SurveyNr = (int) $xplod[0];
        $SurveyNr--;
        //cek apa sudah melakkan input baselan sebelumnya
        $qc = $this->db->get_where('ktv_farmer_financial', array('FarmerID' => $id, 'SurveyNr' => $xplod[0]));
//        $lq = $this->db->last_query();
        if ($qc->num_rows() > 0) {
            $d = array('success' => false, 'data' => null, 'message' => 'Survey ' . $nr . ' sudah dilakukan sebelumnya');
        } else {
            $prevNr = $xplod[0] - 1;
            $sql = "select a.*,concat(c.SurveyNr, ' - ', c.SurveyTxt) AS label_survey,a.InterviewDate,FarmerName as PersonNm,
              DATE_FORMAT(a.InterviewDate,'%d - %b - %Y') as DateInterview
                from ktv_farmer_financial a
                left join ktv_farmer b on a.FarmerID=b.FarmerID
                            LEFT JOIN ktv_survey c ON a.SurveyNr = c.SurveyNr
                WHERE a.FarmerID=? and a.SurveyNr=?";
            $query = $this->db->query($sql, array($id, $prevNr));
            $result = $query->result_array();
            $d = array('success' => true, 'data' => $result[0], 'message' => null);
        }
        return $d;
    }

    public function addSurveyAff($farmerId, $nr, $userid, $InterviewDate) {
        $sql_add = "
         insert into ktv_farmer_financials (FarmerID, SurveyNr,DateCreated,CreatedBy,InterviewDate)
         values(?,?,now(),?,?)";
        $query = $this->db->query($sql_add, array($farmerId, $nr, $userid, $InterviewDate));
        $results['success'] = true;
        $results['nr'] = $result[0]['nr'];
        return $results;
    }

    public function fillNulAff($farmerid, $nsurvey, $field) {
        $nsurvey = $nsurvey - 1;
        $this->db->select($field);
        $q = $this->db->get_where('ktv_farmer_financial', array('FarmerID' => $farmderid, 'SurveyNr' => $nsurvey))->row();
        return $q->$field;
    }

    public function updateAff($FarmerID, $SurveyNr, $InterviewDate, $isValid, $Account, $AccountTypeTabungan, $AccountTypeDeposito, $AccountTypeKoran, $AccountTypeLainnya, $AccountHolderFarmer, $AccountHolderName, $AccountBankID, $AccountBankBranch, $AccountNumber, $AccountNoDetails, $DepositWithdrawnMoneyLast12m, $AccountFeesToPay, $AccountInterestRate, $MoneyUsageHarian, $MoneyUsageTabung, $MoneyUsageInvestasi, $MoneyUsageEmas, $MoneyUsageKonsumsi, $NotSavingJauh, $NotSavingTidakBeruang, $NotSavingBiayaTinggi, $NotSavingTidakPercaya, $NotSavingAdaMenabung, $NotSavingLainnya, $SavingUnitRumah, $SavingUnitBank, $SavingUnitKoperasi, $SavingUnitPedagang, $SavingUnitArisan, $SavingUnitOrang, $SavingUnitLembaga, $SavingUnitMeminjamkan, $DistanceSavingLocation, $AmountSaving, $FutureReasonSekolah, $FutureReasonRumahTangga, $FutureReasonSumbangan, $FutureReasonDarurat, $FutureReasonKesehatan, $FutureReasonInvestasiKebun, $FutureReasonInvestasiLain, $FutureReasonRumah, $FutureReasonLahan, $FutureReasonKendaraan, $FutureReasonHaji, $FutureReasonPensiun, $FutureReasonLain, $ImportantFactorKemanan, $ImportantFactorLikuiditas, $ImportantFactorAksesibilitas, $ImportantFactorKepercayaan, $ImportantFactorBiaya, $ImportantFactorBunga, $ImportantFactorLain, $LoanYesNo, $AmountCurrentLoan, $AmountOutsCurrentLoan, $LoanUnitTengkulak, $LoanUnitKeluarga, $LoanUnitRentenir, $LoanUnitBank, $LoanUnitKoperasi, $LoanUnitMasjid, $LoanUnitLainnya, $PreviousLoan, $CollateralCurrentLoan, $EasyCurrentLoan, $DisburseIntervalCurrentLoan, $RepaymentScheduleCurrentLoan, $EasyGetNewLoan, $UsageCurrentLoanHarian, $UsageCurrentLoanSekolah, $UsageCurrentLoanRumahTangga, $UsageCurrentLoanSumbangan, $UsageCurrentLoanHutang, $UsageCurrentLoanDarurat, $UsageCurrentLoanKesehatan, $UsageCurrentLoanInvestasiKebun, $UsageCurrentLoanInvestasiLain, $UsageCurrentLoanRumah, $UsageCurrentLoanLahan, $UsageCurrentLoanKendaraan, $UsageCurrentLoanHaji, $UsageCurrentLoanPensiun, $UsageCurrentLoanLainnya, $TerminatedLoan, $MoneyToRepayLoanPenghasilan, $MoneyToRepayLoanPinjaman, $MoneyToRepayLoanTanah, $MoneyToRepayLoanTernak, $MoneyToRepayLoanDeposito, $MoneyToRepayLoanLainnya, $ProfitSharingLoan, $ResponsibilityLoan, $WorryToRepayLoan, $DifficultCoverExpenses, $PostponeExpensesSewaRumah, $PostponeExpensesKebun, $PostponeExpensesMakanan, $PostponeExpensesKesehatan, $PostponeExpensesSosial, $PostponeExpensesListrik, $PostponeExpensesPendidikan, $PostponeExpensesSandang, $PostponeExpensesAngsuran, $PostponeExpensesLainnya, $DifficultSocialContributions, $MoneyUrgentExpensesTabungan, $MoneyUrgentExpensesMeminjamKeluarga, $MoneyUrgentExpensesMeminjamTengkulak, $MoneyUrgentExpensesMenjual, $MoneyUrgentExpensesLainnya, $CostUnsubsidizedFertilizer, $OtherIncome, $PensionPlan, $OtherIncomeRegular, $SourceOtherIncomeGajiTetap, $SourceOtherIncomeGajiPasangan, $SourceOtherIncomeUsaha, $SourceOtherIncomeFamily, $SourceOtherIncomeLainnya, $AmountOtherIncome, $CocoaProfitableBusiness, $LoanBetterThanSaving, $UnsubsidizedFertilizerProfitable, $HighInterestRate, $LoanWithTrader, $BetterWetDriedBeans, $GoodLoanClient, $TrustGroupMembers, $RepayLoanGroupMember, $TrustBank, $CocoaFarmPayExpenses, $DiscipilinedSaveMoney, $TradersRich, $CollateralOfferedBank, $ManyCocoaFarmSale, $SatisfiedCocoaBusiness, $PayCocoaBetterInterest, $NeedLoan, $MobilePhone, $LoanAnalysisKnowledge, $IslamicFinancialAwareness, $LearnToSaveMoney, $CocoaPriceToday, $CocoaPriceTodayInfo, $ReasonNotHavePhoneTidakButuh, $ReasonNotHavePhoneMahal, $ReasonNotHavePhoneSinyal, $ReasonNotHavePhoneLainnya, $ValueCocoaFarm, $InsuranceKnowledge, $PastNowInsurance, $InsuranceTypeMotor, $InsuranceTypePanen, $InsuranceTypeBanjir, $InsuranceTypeKemarau, $InsuranceTypeMobil, $InsuranceTypeKesehatan, $InsuranceTypeJiwa, $InsuranceTypeLainnya, $DateCreated, $CreatedBy) {

        $this->db->trans_begin();

        $survey = explode('-', $SurveyNr);
//      echo $survey[0];
        //cek
        //      $qff = $this->db->get_where('ktv_farmer_financial',array('FarmerID'=>$FarmerID,'SurveyNr'=>$survey[0]));
        if ($survey[0] == 0) {
            //baseline
            $SurveyNr = $survey[0];

            $dupdate = array(
                'FarmerID' => $FarmerID,
                'SurveyNr' => $survey[0],
                'InterviewDate' => $InterviewDate,
                /* 'isValid'=>$isValid, */
                'Account' => $Account,
                'AccountTypeTabungan' => $AccountTypeTabungan,
                'AccountTypeDeposito' => $AccountTypeDeposito,
                'AccountTypeKoran' => $AccountTypeKoran,
                'AccountTypeLainnya' => $AccountTypeLainnya,
                'AccountHolderFarmer' => $AccountHolderFarmer,
                'AccountHolderName' => $AccountHolderName,
                'AccountBankID' => $AccountBankID,
                'AccountBankBranch' => $AccountBankBranch,
                'AccountNumber' => $AccountNumber,
                'AccountNoDetails' => $AccountNoDetails,
                'DepositWithdrawnMoneyLast12m' => $DepositWithdrawnMoneyLast12m,
                'AccountFeesToPay' => $AccountFeesToPay,
                'AccountInterestRate' => $AccountInterestRate,
                'MoneyUsageHarian' => $MoneyUsageHarian,
                'MoneyUsageTabung' => $MoneyUsageTabung,
                'MoneyUsageInvestasi' => $MoneyUsageInvestasi,
                'MoneyUsageEmas' => $MoneyUsageEmas,
                'MoneyUsageKonsumsi' => $MoneyUsageKonsumsi,
                'NotSavingJauh' => $NotSavingJauh,
                'NotSavingTidakBeruang' => $NotSavingTidakBeruang,
                'NotSavingBiayaTinggi' => $NotSavingBiayaTinggi,
                'NotSavingTidakPercaya' => $NotSavingTidakPercaya,
                'NotSavingAdaMenabung' => $NotSavingAdaMenabung,
                'NotSavingLainnya' => $NotSavingLainnya,
                'SavingUnitRumah' => $SavingUnitRumah,
                'SavingUnitBank' => $SavingUnitBank,
                'SavingUnitKoperasi' => $SavingUnitKoperasi,
                'SavingUnitPedagang' => $SavingUnitPedagang,
                'SavingUnitArisan' => $SavingUnitArisan,
                'SavingUnitOrang' => $SavingUnitOrang,
                'SavingUnitLembaga' => $SavingUnitLembaga,
                'SavingUnitMeminjamkan' => $SavingUnitMeminjamkan,
                'DistanceSavingLocation' => $DistanceSavingLocation,
                'AmountSaving' => $AmountSaving,
                'FutureReasonSekolah' => $FutureReasonSekolah,
                'FutureReasonRumahTangga' => $FutureReasonRumahTangga,
                'FutureReasonSumbangan' => $FutureReasonSumbangan,
                'FutureReasonDarurat' => $FutureReasonDarurat,
                'FutureReasonKesehatan' => $FutureReasonKesehatan,
                'FutureReasonInvestasiKebun' => $FutureReasonInvestasiKebun,
                'FutureReasonInvestasiLain' => $FutureReasonInvestasiLain,
                'FutureReasonRumah' => $FutureReasonRumah,
                'FutureReasonLahan' => $FutureReasonLahan,
                'FutureReasonKendaraan' => $FutureReasonKendaraan,
                'FutureReasonHaji' => $FutureReasonHaji,
                'FutureReasonPensiun' => $FutureReasonPensiun,
                'FutureReasonLain' => $FutureReasonLain,
                'ImportantFactorKemanan' => $ImportantFactorKemanan,
                'ImportantFactorLikuiditas' => $ImportantFactorLikuiditas,
                'ImportantFactorAksesibilitas' => $ImportantFactorAksesibilitas,
                'ImportantFactorKepercayaan' => $ImportantFactorKepercayaan,
                'ImportantFactorBiaya' => $ImportantFactorBiaya,
                'ImportantFactorBunga' => $ImportantFactorBunga,
                'ImportantFactorLain' => $ImportantFactorLain,
                'LoanYesNo' => $LoanYesNo,
                'AmountCurrentLoan' => $AmountCurrentLoan,
                'AmountOutsCurrentLoan' => $AmountOutsCurrentLoan,
                'LoanUnitTengkulak' => $LoanUnitTengkulak,
                'LoanUnitKeluarga' => $LoanUnitKeluarga,
                'LoanUnitRentenir' => $LoanUnitRentenir,
                'LoanUnitBank' => $LoanUnitBank,
                'LoanUnitKoperasi' => $LoanUnitKoperasi,
                'LoanUnitMasjid' => $LoanUnitMasjid,
                'LoanUnitLainnya' => $LoanUnitLainnya,
                'PreviousLoan' => $PreviousLoan,
                'CollateralCurrentLoan' => $CollateralCurrentLoan,
                'EasyCurrentLoan' => $EasyCurrentLoan,
                'DisburseIntervalCurrentLoan' => $DisburseIntervalCurrentLoan,
                'RepaymentScheduleCurrentLoan' => $RepaymentScheduleCurrentLoan,
                'EasyGetNewLoan' => $EasyGetNewLoan,
                'UsageCurrentLoanHarian' => $UsageCurrentLoanHarian,
                'UsageCurrentLoanSekolah' => $UsageCurrentLoanSekolah,
                'UsageCurrentLoanRumahTangga' => $UsageCurrentLoanRumahTangga,
                'UsageCurrentLoanSumbangan' => $UsageCurrentLoanSumbangan,
                'UsageCurrentLoanHutang' => $UsageCurrentLoanHutang,
                'UsageCurrentLoanDarurat' => $UsageCurrentLoanDarurat,
                'UsageCurrentLoanKesehatan' => $UsageCurrentLoanKesehatan,
                'UsageCurrentLoanInvestasiKebun' => $UsageCurrentLoanInvestasiKebun,
                'UsageCurrentLoanInvestasiLain' => $UsageCurrentLoanInvestasiLain,
                'UsageCurrentLoanRumah' => $UsageCurrentLoanRumah,
                'UsageCurrentLoanLahan' => $UsageCurrentLoanLahan,
                'UsageCurrentLoanKendaraan' => $UsageCurrentLoanKendaraan,
                'UsageCurrentLoanHaji' => $UsageCurrentLoanHaji,
                'UsageCurrentLoanPensiun' => $UsageCurrentLoanPensiun,
                'UsageCurrentLoanLainnya' => $UsageCurrentLoanLainnya,
                'TerminatedLoan' => $TerminatedLoan,
                'MoneyToRepayLoanPenghasilan' => $MoneyToRepayLoanPenghasilan,
                'MoneyToRepayLoanPinjaman' => $MoneyToRepayLoanPinjaman,
                'MoneyToRepayLoanTanah' => $MoneyToRepayLoanTanah,
                'MoneyToRepayLoanTernak' => $MoneyToRepayLoanTernak,
                'MoneyToRepayLoanDeposito' => $MoneyToRepayLoanDeposito,
                'MoneyToRepayLoanLainnya' => $MoneyToRepayLoanLainnya,
                'ProfitSharingLoan' => $ProfitSharingLoan,
                'ResponsibilityLoan' => $ResponsibilityLoan,
                'WorryToRepayLoan' => $WorryToRepayLoan,
                'DifficultCoverExpenses' => $DifficultCoverExpenses,
                'PostponeExpensesSewaRumah' => $PostponeExpensesSewaRumah,
                'PostponeExpensesKebun' => $PostponeExpensesKebun,
                'PostponeExpensesMakanan' => $PostponeExpensesMakanan,
                'PostponeExpensesKesehatan' => $PostponeExpensesKesehatan,
                'PostponeExpensesSosial' => $PostponeExpensesSosial,
                'PostponeExpensesListrik' => $PostponeExpensesListrik,
                'PostponeExpensesPendidikan' => $PostponeExpensesPendidikan,
                'PostponeExpensesSandang' => $PostponeExpensesSandang,
                'PostponeExpensesAngsuran' => $PostponeExpensesAngsuran,
                'PostponeExpensesLainnya' => $PostponeExpensesLainnya,
                'DifficultSocialContributions' => $DifficultSocialContributions,
                'MoneyUrgentExpensesTabungan' => $MoneyUrgentExpensesTabungan,
                'MoneyUrgentExpensesMeminjamKeluarga' => $MoneyUrgentExpensesMeminjamKeluarga,
                'MoneyUrgentExpensesMeminjamTengkulak' => $MoneyUrgentExpensesMeminjamTengkulak,
                'MoneyUrgentExpensesMenjual' => $MoneyUrgentExpensesMenjual,
                'MoneyUrgentExpensesLainnya' => $MoneyUrgentExpensesLainnya,
                'CostUnsubsidizedFertilizer' => $CostUnsubsidizedFertilizer,
                'OtherIncome' => $OtherIncome,
                'PensionPlan' => $PensionPlan,
                'OtherIncomeRegular' => $OtherIncomeRegular,
                'SourceOtherIncomeGajiTetap' => $SourceOtherIncomeGajiTetap,
                'SourceOtherIncomeGajiPasangan' => $SourceOtherIncomeGajiPasangan,
                'SourceOtherIncomeUsaha' => $SourceOtherIncomeUsaha,
                'SourceOtherIncomeFamily' => $SourceOtherIncomeFamily,
                'SourceOtherIncomeLainnya' => $SourceOtherIncomeLainnya,
                'AmountOtherIncome' => $AmountOtherIncome,
                'CocoaProfitableBusiness' => $CocoaProfitableBusiness,
                'LoanBetterThanSaving' => $LoanBetterThanSaving,
                'UnsubsidizedFertilizerProfitable' => $UnsubsidizedFertilizerProfitable,
                'HighInterestRate' => $HighInterestRate,
                'LoanWithTrader' => $LoanWithTrader,
                'BetterWetDriedBeans' => $BetterWetDriedBeans,
                'GoodLoanClient' => $GoodLoanClient,
                'TrustGroupMembers' => $TrustGroupMembers,
                'RepayLoanGroupMember' => $RepayLoanGroupMember,
                'TrustBank' => $TrustBank,
                'CocoaFarmPayExpenses' => $CocoaFarmPayExpenses,
                'DiscipilinedSaveMoney' => $DiscipilinedSaveMoney,
                'TradersRich' => $TradersRich,
                'CollateralOfferedBank' => $CollateralOfferedBank,
                'ManyCocoaFarmSale' => $ManyCocoaFarmSale,
                'SatisfiedCocoaBusiness' => $SatisfiedCocoaBusiness,
                'PayCocoaBetterInterest' => $PayCocoaBetterInterest,
                'NeedLoan' => $NeedLoan,
                'MobilePhone' => $MobilePhone,
                'LoanAnalysisKnowledge' => $LoanAnalysisKnowledge,
                'IslamicFinancialAwareness' => $IslamicFinancialAwareness,
                'LearnToSaveMoney' => $LearnToSaveMoney,
                'CocoaPriceToday' => $CocoaPriceToday,
                'CocoaPriceTodayInfo' => $CocoaPriceTodayInfo,
                'ReasonNotHavePhoneTidakButuh' => $ReasonNotHavePhoneTidakButuh,
                'ReasonNotHavePhoneMahal' => $ReasonNotHavePhoneMahal,
                'ReasonNotHavePhoneSinyal' => $ReasonNotHavePhoneSinyal,
                'ReasonNotHavePhoneLainnya' => $ReasonNotHavePhoneLainnya,
                'ValueCocoaFarm' => $ValueCocoaFarm,
                'InsuranceKnowledge' => $InsuranceKnowledge,
                'PastNowInsurance' => $PastNowInsurance,
                'InsuranceTypeMotor' => $InsuranceTypeMotor,
                'InsuranceTypePanen' => $InsuranceTypePanen,
                'InsuranceTypeBanjir' => $InsuranceTypeBanjir,
                'InsuranceTypeKemarau' => $InsuranceTypeKemarau,
                'InsuranceTypeMobil' => $InsuranceTypeMobil,
                'InsuranceTypeKesehatan' => $InsuranceTypeKesehatan,
                'InsuranceTypeJiwa' => $InsuranceTypeJiwa,
                'InsuranceTypeLainnya' => $InsuranceTypeLainnya,
                    //'DateCreated'=>$DateCreated,
                    // 'CreatedBy'=>$CreatedBy,
                    //                        'DateUpdated'=>date('Y-m-d H:m:s'),
                    //                        'LastModifiedBy'=>$CreatedBy
            );

            foreach ($dupdate as $key => $value) {
                if ($value[0] == '') {
                    $dupdate[$key] = $this->fillNulAff($FarmerID, $survey[0], $key);
                }
            }

            //cek datanya udah ada apa belum
            $this->db->where('SurveyNr', $SurveyNr);
            $this->db->where('FarmerID', $FarmerID);
            $qcekdata = $this->db->get('ktv_farmer_financial');
            //        echo $this->db->last_query();
            if ($qcekdata->num_rows() > 0) {
                $dupdate['DateUpdated'] = date('Y-m-d H:m:s');
                $dupdate['LastModifiedBy'] = $CreatedBy;

                $this->db->where('SurveyNr', $SurveyNr);
                $this->db->where('FarmerID', $FarmerID);
                $this->db->update('ktv_farmer_financial', $dupdate);

                $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
                $query = $this->db->query($sql_date_survey, array($FarmerID));
            } else {
                $dupdate['DateCreated'] = date('Y-m-d H:m:s');
                $dupdate['CreatedBy'] = $CreatedBy;
                $this->db->insert('ktv_farmer_financial', $dupdate);
            }
        } else {
//          echo 'postline<br>';
            //postline
            //cek datanya udah ada apa belum
            $this->db->where('SurveyNr', intval($survey[0]));
            $this->db->where('FarmerID', $FarmerID);
            $qcekdata = $this->db->get('ktv_farmer_financial');
//        echo $this->db->last_query();
            if ($qcekdata->num_rows() > 0) {
                $qbaseline = $qcekdata->row();
//            $SurveyNr = intval($survey[0]);

                $dupdate = array(
                    'FarmerID' => $FarmerID,
                    'SurveyNr' => $survey[0],
                    'InterviewDate' => $InterviewDate,
                    /* 'isValid'=>$isValid, */
                    'Account' => $Account,
                    'AccountTypeTabungan' => $AccountTypeTabungan,
                    'AccountTypeDeposito' => $AccountTypeDeposito,
                    'AccountTypeKoran' => $AccountTypeKoran,
                    'AccountTypeLainnya' => $AccountTypeLainnya,
                    'AccountHolderFarmer' => $qbaseline->AccountHolderFarmer,
                    'AccountHolderName' => $qbaseline->AccountHolderName,
                    'AccountBankID' => $qbaseline->AccountBankID,
                    'AccountBankBranch' => $qbaseline->AccountBankBranch,
                    'AccountNumber' => $qbaseline->AccountNumber,
                    'AccountNoDetails' => $AccountNoDetails,
                    'DepositWithdrawnMoneyLast12m' => $DepositWithdrawnMoneyLast12m,
                    'AccountFeesToPay' => $qbaseline->AccountFeesToPay,
                    'AccountInterestRate' => $qbaseline->AccountInterestRate,
                    'MoneyUsageHarian' => $MoneyUsageHarian,
                    'MoneyUsageTabung' => $MoneyUsageTabung,
                    'MoneyUsageInvestasi' => $MoneyUsageInvestasi,
                    'MoneyUsageEmas' => $MoneyUsageEmas,
                    'MoneyUsageKonsumsi' => $MoneyUsageKonsumsi,
                    'NotSavingJauh' => $qbaseline->NotSavingJauh,
                    'NotSavingTidakBeruang' => $qbaseline->NotSavingTidakBeruang,
                    'NotSavingBiayaTinggi' => $qbaseline->NotSavingBiayaTinggi,
                    'NotSavingTidakPercaya' => $qbaseline->NotSavingTidakPercaya,
                    'NotSavingAdaMenabung' => $qbaseline->NotSavingAdaMenabung,
                    'NotSavingLainnya' => $qbaseline->NotSavingLainnya,
                    'SavingUnitRumah' => $SavingUnitRumah,
                    'SavingUnitBank' => $SavingUnitBank,
                    'SavingUnitKoperasi' => $SavingUnitKoperasi,
                    'SavingUnitPedagang' => $SavingUnitPedagang,
                    'SavingUnitArisan' => $SavingUnitArisan,
                    'SavingUnitOrang' => $SavingUnitOrang,
                    'SavingUnitLembaga' => $SavingUnitLembaga,
                    'SavingUnitMeminjamkan' => $SavingUnitMeminjamkan,
                    'DistanceSavingLocation' => $qbaseline->DistanceSavingLocation,
                    'AmountSaving' => $AmountSaving,
                    'FutureReasonSekolah' => $qbaseline->FutureReasonSekolah,
                    'FutureReasonRumahTangga' => $qbaseline->FutureReasonRumahTangga,
                    'FutureReasonSumbangan' => $qbaseline->FutureReasonSumbangan,
                    'FutureReasonDarurat' => $qbaseline->FutureReasonDarurat,
                    'FutureReasonKesehatan' => $qbaseline->FutureReasonKesehatan,
                    'FutureReasonInvestasiKebun' => $qbaseline->FutureReasonInvestasiKebun,
                    'FutureReasonInvestasiLain' => $qbaseline->FutureReasonInvestasiLain,
                    'FutureReasonRumah' => $qbaseline->FutureReasonRumah,
                    'FutureReasonLahan' => $qbaseline->FutureReasonLahan,
                    'FutureReasonKendaraan' => $qbaseline->FutureReasonKendaraan,
                    'FutureReasonHaji' => $qbaseline->FutureReasonHaji,
                    'FutureReasonPensiun' => $qbaseline->FutureReasonPensiun,
                    'FutureReasonLain' => $qbaseline->FutureReasonLain,
                    'ImportantFactorKemanan' => $qbaseline->ImportantFactorKemanan,
                    'ImportantFactorLikuiditas' => $qbaseline->ImportantFactorLikuiditas,
                    'ImportantFactorAksesibilitas' => $qbaseline->ImportantFactorAksesibilitas,
                    'ImportantFactorKepercayaan' => $qbaseline->ImportantFactorKepercayaan,
                    'ImportantFactorBiaya' => $qbaseline->ImportantFactorBiaya,
                    'ImportantFactorBunga' => $qbaseline->ImportantFactorBunga,
                    'ImportantFactorLain' => $qbaseline->ImportantFactorLain,
                    'LoanYesNo' => $LoanYesNo,
                    'AmountCurrentLoan' => $qbaseline->AmountCurrentLoan,
                    'AmountOutsCurrentLoan' => $qbaseline->AmountOutsCurrentLoan,
                    'LoanUnitTengkulak' => $qbaseline->LoanUnitTengkulak,
                    'LoanUnitKeluarga' => $qbaseline->LoanUnitKeluarga,
                    'LoanUnitRentenir' => $qbaseline->LoanUnitRentenir,
                    'LoanUnitBank' => $qbaseline->LoanUnitBank,
                    'LoanUnitKoperasi' => $qbaseline->LoanUnitKoperasi,
                    'LoanUnitMasjid' => $qbaseline->LoanUnitMasjid,
                    'LoanUnitLainnya' => $qbaseline->LoanUnitLainnya,
                    'PreviousLoan' => $qbaseline->PreviousLoan,
                    'CollateralCurrentLoan' => $qbaseline->CollateralCurrentLoan,
                    'EasyCurrentLoan' => $qbaseline->EasyCurrentLoan,
                    'DisburseIntervalCurrentLoan' => $qbaseline->DisburseIntervalCurrentLoan,
                    'RepaymentScheduleCurrentLoan' => $qbaseline->RepaymentScheduleCurrentLoan,
                    'EasyGetNewLoan' => $qbaseline->EasyGetNewLoan,
                    'UsageCurrentLoanHarian' => $qbaseline->UsageCurrentLoanHarian,
                    'UsageCurrentLoanSekolah' => $qbaseline->UsageCurrentLoanSekolah,
                    'UsageCurrentLoanRumahTangga' => $qbaseline->UsageCurrentLoanRumahTangga,
                    'UsageCurrentLoanSumbangan' => $qbaseline->UsageCurrentLoanSumbangan,
                    'UsageCurrentLoanHutang' => $qbaseline->UsageCurrentLoanHutang,
                    'UsageCurrentLoanDarurat' => $qbaseline->UsageCurrentLoanDarurat,
                    'UsageCurrentLoanKesehatan' => $qbaseline->UsageCurrentLoanKesehatan,
                    'UsageCurrentLoanInvestasiKebun' => $qbaseline->UsageCurrentLoanInvestasiKebun,
                    'UsageCurrentLoanInvestasiLain' => $qbaseline->UsageCurrentLoanInvestasiLain,
                    'UsageCurrentLoanRumah' => $qbaseline->UsageCurrentLoanRumah,
                    'UsageCurrentLoanLahan' => $qbaseline->UsageCurrentLoanLahan,
                    'UsageCurrentLoanKendaraan' => $qbaseline->UsageCurrentLoanKendaraan,
                    'UsageCurrentLoanHaji' => $qbaseline->UsageCurrentLoanHaji,
                    'UsageCurrentLoanPensiun' => $qbaseline->UsageCurrentLoanPensiun,
                    'UsageCurrentLoanLainnya' => $qbaseline->UsageCurrentLoanLainnya,
                    'TerminatedLoan' => $qbaseline->TerminatedLoan,
                    'MoneyToRepayLoanPenghasilan' => $qbaseline->MoneyToRepayLoanPenghasilan,
                    'MoneyToRepayLoanPinjaman' => $qbaseline->MoneyToRepayLoanPinjaman,
                    'MoneyToRepayLoanTanah' => $qbaseline->MoneyToRepayLoanTanah,
                    'MoneyToRepayLoanTernak' => $qbaseline->MoneyToRepayLoanTernak,
                    'MoneyToRepayLoanDeposito' => $qbaseline->MoneyToRepayLoanDeposito,
                    'MoneyToRepayLoanLainnya' => $qbaseline->MoneyToRepayLoanLainnya,
                    'ProfitSharingLoan' => $qbaseline->ProfitSharingLoan,
                    'ResponsibilityLoan' => $qbaseline->ResponsibilityLoan,
                    'WorryToRepayLoan' => $qbaseline->WorryToRepayLoan,
                    'DifficultCoverExpenses' => $qbaseline->DifficultCoverExpenses,
                    'PostponeExpensesSewaRumah' => $qbaseline->PostponeExpensesSewaRumah,
                    'PostponeExpensesKebun' => $qbaseline->PostponeExpensesKebun,
                    'PostponeExpensesMakanan' => $qbaseline->PostponeExpensesMakanan,
                    'PostponeExpensesKesehatan' => $qbaseline->PostponeExpensesKesehatan,
                    'PostponeExpensesSosial' => $qbaseline->PostponeExpensesSosial,
                    'PostponeExpensesListrik' => $qbaseline->PostponeExpensesListrik,
                    'PostponeExpensesPendidikan' => $qbaseline->PostponeExpensesPendidikan,
                    'PostponeExpensesSandang' => $qbaseline->PostponeExpensesSandang,
                    'PostponeExpensesAngsuran' => $qbaseline->PostponeExpensesAngsuran,
                    'PostponeExpensesLainnya' => $qbaseline->PostponeExpensesLainnya,
                    'DifficultSocialContributions' => $qbaseline->DifficultSocialContributions,
                    'MoneyUrgentExpensesTabungan' => $MoneyUrgentExpensesTabungan,
                    'MoneyUrgentExpensesMeminjamKeluarga' => $MoneyUrgentExpensesMeminjamKeluarga,
                    'MoneyUrgentExpensesMeminjamTengkulak' => $MoneyUrgentExpensesMeminjamTengkulak,
                    'MoneyUrgentExpensesMenjual' => $MoneyUrgentExpensesMenjual,
                    'MoneyUrgentExpensesLainnya' => $MoneyUrgentExpensesLainnya,
                    'CostUnsubsidizedFertilizer' => $qbaseline->CostUnsubsidizedFertilizer,
                    'OtherIncome' => $qbaseline->OtherIncome,
                    'PensionPlan' => $qbaseline->PensionPlan,
                    'OtherIncomeRegular' => $qbaseline->OtherIncomeRegular,
                    'SourceOtherIncomeGajiTetap' => $qbaseline->SourceOtherIncomeGajiTetap,
                    'SourceOtherIncomeGajiPasangan' => $qbaseline->SourceOtherIncomeGajiPasangan,
                    'SourceOtherIncomeUsaha' => $qbaseline->SourceOtherIncomeUsaha,
                    'SourceOtherIncomeFamily' => $qbaseline->SourceOtherIncomeFamily,
                    'SourceOtherIncomeLainnya' => $qbaseline->SourceOtherIncomeLainnya,
                    'AmountOtherIncome' => $qbaseline->AmountOtherIncome,
                    'CocoaProfitableBusiness' => $CocoaProfitableBusiness,
                    'LoanBetterThanSaving' => $LoanBetterThanSaving,
                    'UnsubsidizedFertilizerProfitable' => $qbaseline->UnsubsidizedFertilizerProfitable,
                    'HighInterestRate' => $qbaseline->HighInterestRate,
                    'LoanWithTrader' => $qbaseline->LoanWithTrader,
                    'BetterWetDriedBeans' => $BetterWetDriedBeans,
                    'GoodLoanClient' => $GoodLoanClient,
                    'TrustGroupMembers' => $qbaseline->TrustGroupMembers,
                    'RepayLoanGroupMember' => $qbaseline->RepayLoanGroupMember,
                    'TrustBank' => $TrustBank,
                    'CocoaFarmPayExpenses' => $qbaseline->CocoaFarmPayExpenses,
                    'DiscipilinedSaveMoney' => $DiscipilinedSaveMoney,
                    'TradersRich' => $qbaseline->TradersRich,
                    'CollateralOfferedBank' => $CollateralOfferedBank,
                    'ManyCocoaFarmSale' => $ManyCocoaFarmSale,
                    'SatisfiedCocoaBusiness' => $qbaseline->SatisfiedCocoaBusiness,
                    'PayCocoaBetterInterest' => $qbaseline->PayCocoaBetterInterest,
                    'NeedLoan' => $qbaseline->NeedLoan,
                    'MobilePhone' => $qbaseline->MobilePhone,
                    'LoanAnalysisKnowledge' => $qbaseline->LoanAnalysisKnowledge,
                    'IslamicFinancialAwareness' => $qbaseline->IslamicFinancialAwareness,
                    'LearnToSaveMoney' => $qbaseline->LearnToSaveMoney,
                    'CocoaPriceToday' => $CocoaPriceToday,
                    'CocoaPriceTodayInfo' => $CocoaPriceTodayInfo,
                    'ReasonNotHavePhoneTidakButuh' => $ReasonNotHavePhoneTidakButuh,
                    'ReasonNotHavePhoneMahal' => $ReasonNotHavePhoneMahal,
                    'ReasonNotHavePhoneSinyal' => $ReasonNotHavePhoneSinyal,
                    'ReasonNotHavePhoneLainnya' => $ReasonNotHavePhoneLainnya,
                    'ValueCocoaFarm' => $ValueCocoaFarm,
                    'InsuranceKnowledge' => $qbaseline->InsuranceKnowledge,
                    'PastNowInsurance' => $qbaseline->PastNowInsurance,
                    'InsuranceTypeMotor' => $InsuranceTypeMotor,
                    'InsuranceTypePanen' => $InsuranceTypePanen,
                    'InsuranceTypeBanjir' => $InsuranceTypeBanjir,
                    'InsuranceTypeKemarau' => $InsuranceTypeKemarau,
                    'InsuranceTypeMobil' => $InsuranceTypeMobil,
                    'InsuranceTypeKesehatan' => $InsuranceTypeKesehatan,
                    'InsuranceTypeJiwa' => $InsuranceTypeJiwa,
                    'InsuranceTypeLainnya' => $InsuranceTypeLainnya,
                        //'DateCreated'=>$DateCreated,
                        // 'CreatedBy'=>$CreatedBy,
                        //                        'DateUpdated'=>date('Y-m-d H:m:s'),
                        //                        'LastModifiedBy'=>$CreatedBy
                );

                $dupdate['DateUpdated'] = date('Y-m-d H:m:s');
                $dupdate['LastModifiedBy'] = $CreatedBy;

                $this->db->where('SurveyNr', $SurveyNr);
                $this->db->where('FarmerID', $FarmerID);
                $this->db->update('ktv_farmer_financial', $dupdate);

                $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
                $query = $this->db->query($sql_date_survey, array($FarmerID));
            } else {

                $SurveyNrPrev = intval($survey[0]) - 1;

                $qbaseline = $this->db->get_where('ktv_farmer_financial', array('FarmerID' => $FarmerID, 'SurveyNr' => $SurveyNrPrev))->row();
//            $SurveyNr = intval($survey[0]);

                $dupdate = array(
                    'FarmerID' => $FarmerID,
                    'SurveyNr' => $survey[0],
                    'InterviewDate' => $InterviewDate,
                    /* 'isValid'=>$isValid, */
                    'Account' => $Account,
                    'AccountTypeTabungan' => $AccountTypeTabungan,
                    'AccountTypeDeposito' => $AccountTypeDeposito,
                    'AccountTypeKoran' => $AccountTypeKoran,
                    'AccountTypeLainnya' => $AccountTypeLainnya,
                    'AccountHolderFarmer' => $qbaseline->AccountHolderFarmer,
                    'AccountHolderName' => $qbaseline->AccountHolderName,
                    'AccountBankID' => $qbaseline->AccountBankID,
                    'AccountBankBranch' => $qbaseline->AccountBankBranch,
                    'AccountNumber' => $qbaseline->AccountNumber,
                    'AccountNoDetails' => $AccountNoDetails,
                    'DepositWithdrawnMoneyLast12m' => $DepositWithdrawnMoneyLast12m,
                    'AccountFeesToPay' => $qbaseline->AccountFeesToPay,
                    'AccountInterestRate' => $qbaseline->AccountInterestRate,
                    'MoneyUsageHarian' => $MoneyUsageHarian,
                    'MoneyUsageTabung' => $MoneyUsageTabung,
                    'MoneyUsageInvestasi' => $MoneyUsageInvestasi,
                    'MoneyUsageEmas' => $MoneyUsageEmas,
                    'MoneyUsageKonsumsi' => $MoneyUsageKonsumsi,
                    'NotSavingJauh' => $qbaseline->NotSavingJauh,
                    'NotSavingTidakBeruang' => $qbaseline->NotSavingTidakBeruang,
                    'NotSavingBiayaTinggi' => $qbaseline->NotSavingBiayaTinggi,
                    'NotSavingTidakPercaya' => $qbaseline->NotSavingTidakPercaya,
                    'NotSavingAdaMenabung' => $qbaseline->NotSavingAdaMenabung,
                    'NotSavingLainnya' => $qbaseline->NotSavingLainnya,
                    'SavingUnitRumah' => $SavingUnitRumah,
                    'SavingUnitBank' => $SavingUnitBank,
                    'SavingUnitKoperasi' => $SavingUnitKoperasi,
                    'SavingUnitPedagang' => $SavingUnitPedagang,
                    'SavingUnitArisan' => $SavingUnitArisan,
                    'SavingUnitOrang' => $SavingUnitOrang,
                    'SavingUnitLembaga' => $SavingUnitLembaga,
                    'SavingUnitMeminjamkan' => $SavingUnitMeminjamkan,
                    'DistanceSavingLocation' => $qbaseline->DistanceSavingLocation,
                    'AmountSaving' => $AmountSaving,
                    'FutureReasonSekolah' => $qbaseline->FutureReasonSekolah,
                    'FutureReasonRumahTangga' => $qbaseline->FutureReasonRumahTangga,
                    'FutureReasonSumbangan' => $qbaseline->FutureReasonSumbangan,
                    'FutureReasonDarurat' => $qbaseline->FutureReasonDarurat,
                    'FutureReasonKesehatan' => $qbaseline->FutureReasonKesehatan,
                    'FutureReasonInvestasiKebun' => $qbaseline->FutureReasonInvestasiKebun,
                    'FutureReasonInvestasiLain' => $qbaseline->FutureReasonInvestasiLain,
                    'FutureReasonRumah' => $qbaseline->FutureReasonRumah,
                    'FutureReasonLahan' => $qbaseline->FutureReasonLahan,
                    'FutureReasonKendaraan' => $qbaseline->FutureReasonKendaraan,
                    'FutureReasonHaji' => $qbaseline->FutureReasonHaji,
                    'FutureReasonPensiun' => $qbaseline->FutureReasonPensiun,
                    'FutureReasonLain' => $qbaseline->FutureReasonLain,
                    'ImportantFactorKemanan' => $qbaseline->ImportantFactorKemanan,
                    'ImportantFactorLikuiditas' => $qbaseline->ImportantFactorLikuiditas,
                    'ImportantFactorAksesibilitas' => $qbaseline->ImportantFactorAksesibilitas,
                    'ImportantFactorKepercayaan' => $qbaseline->ImportantFactorKepercayaan,
                    'ImportantFactorBiaya' => $qbaseline->ImportantFactorBiaya,
                    'ImportantFactorBunga' => $qbaseline->ImportantFactorBunga,
                    'ImportantFactorLain' => $qbaseline->ImportantFactorLain,
                    'LoanYesNo' => $LoanYesNo,
                    'AmountCurrentLoan' => $qbaseline->AmountCurrentLoan,
                    'AmountOutsCurrentLoan' => $qbaseline->AmountOutsCurrentLoan,
                    'LoanUnitTengkulak' => $qbaseline->LoanUnitTengkulak,
                    'LoanUnitKeluarga' => $qbaseline->LoanUnitKeluarga,
                    'LoanUnitRentenir' => $qbaseline->LoanUnitRentenir,
                    'LoanUnitBank' => $qbaseline->LoanUnitBank,
                    'LoanUnitKoperasi' => $qbaseline->LoanUnitKoperasi,
                    'LoanUnitMasjid' => $qbaseline->LoanUnitMasjid,
                    'LoanUnitLainnya' => $qbaseline->LoanUnitLainnya,
                    'PreviousLoan' => $qbaseline->PreviousLoan,
                    'CollateralCurrentLoan' => $qbaseline->CollateralCurrentLoan,
                    'EasyCurrentLoan' => $qbaseline->EasyCurrentLoan,
                    'DisburseIntervalCurrentLoan' => $qbaseline->DisburseIntervalCurrentLoan,
                    'RepaymentScheduleCurrentLoan' => $qbaseline->RepaymentScheduleCurrentLoan,
                    'EasyGetNewLoan' => $qbaseline->EasyGetNewLoan,
                    'UsageCurrentLoanHarian' => $qbaseline->UsageCurrentLoanHarian,
                    'UsageCurrentLoanSekolah' => $qbaseline->UsageCurrentLoanSekolah,
                    'UsageCurrentLoanRumahTangga' => $qbaseline->UsageCurrentLoanRumahTangga,
                    'UsageCurrentLoanSumbangan' => $qbaseline->UsageCurrentLoanSumbangan,
                    'UsageCurrentLoanHutang' => $qbaseline->UsageCurrentLoanHutang,
                    'UsageCurrentLoanDarurat' => $qbaseline->UsageCurrentLoanDarurat,
                    'UsageCurrentLoanKesehatan' => $qbaseline->UsageCurrentLoanKesehatan,
                    'UsageCurrentLoanInvestasiKebun' => $qbaseline->UsageCurrentLoanInvestasiKebun,
                    'UsageCurrentLoanInvestasiLain' => $qbaseline->UsageCurrentLoanInvestasiLain,
                    'UsageCurrentLoanRumah' => $qbaseline->UsageCurrentLoanRumah,
                    'UsageCurrentLoanLahan' => $qbaseline->UsageCurrentLoanLahan,
                    'UsageCurrentLoanKendaraan' => $qbaseline->UsageCurrentLoanKendaraan,
                    'UsageCurrentLoanHaji' => $qbaseline->UsageCurrentLoanHaji,
                    'UsageCurrentLoanPensiun' => $qbaseline->UsageCurrentLoanPensiun,
                    'UsageCurrentLoanLainnya' => $qbaseline->UsageCurrentLoanLainnya,
                    'TerminatedLoan' => $qbaseline->TerminatedLoan,
                    'MoneyToRepayLoanPenghasilan' => $qbaseline->MoneyToRepayLoanPenghasilan,
                    'MoneyToRepayLoanPinjaman' => $qbaseline->MoneyToRepayLoanPinjaman,
                    'MoneyToRepayLoanTanah' => $qbaseline->MoneyToRepayLoanTanah,
                    'MoneyToRepayLoanTernak' => $qbaseline->MoneyToRepayLoanTernak,
                    'MoneyToRepayLoanDeposito' => $qbaseline->MoneyToRepayLoanDeposito,
                    'MoneyToRepayLoanLainnya' => $qbaseline->MoneyToRepayLoanLainnya,
                    'ProfitSharingLoan' => $qbaseline->ProfitSharingLoan,
                    'ResponsibilityLoan' => $qbaseline->ResponsibilityLoan,
                    'WorryToRepayLoan' => $qbaseline->WorryToRepayLoan,
                    'DifficultCoverExpenses' => $qbaseline->DifficultCoverExpenses,
                    'PostponeExpensesSewaRumah' => $qbaseline->PostponeExpensesSewaRumah,
                    'PostponeExpensesKebun' => $qbaseline->PostponeExpensesKebun,
                    'PostponeExpensesMakanan' => $qbaseline->PostponeExpensesMakanan,
                    'PostponeExpensesKesehatan' => $qbaseline->PostponeExpensesKesehatan,
                    'PostponeExpensesSosial' => $qbaseline->PostponeExpensesSosial,
                    'PostponeExpensesListrik' => $qbaseline->PostponeExpensesListrik,
                    'PostponeExpensesPendidikan' => $qbaseline->PostponeExpensesPendidikan,
                    'PostponeExpensesSandang' => $qbaseline->PostponeExpensesSandang,
                    'PostponeExpensesAngsuran' => $qbaseline->PostponeExpensesAngsuran,
                    'PostponeExpensesLainnya' => $qbaseline->PostponeExpensesLainnya,
                    'DifficultSocialContributions' => $qbaseline->DifficultSocialContributions,
                    'MoneyUrgentExpensesTabungan' => $MoneyUrgentExpensesTabungan,
                    'MoneyUrgentExpensesMeminjamKeluarga' => $MoneyUrgentExpensesMeminjamKeluarga,
                    'MoneyUrgentExpensesMeminjamTengkulak' => $MoneyUrgentExpensesMeminjamTengkulak,
                    'MoneyUrgentExpensesMenjual' => $MoneyUrgentExpensesMenjual,
                    'MoneyUrgentExpensesLainnya' => $MoneyUrgentExpensesLainnya,
                    'CostUnsubsidizedFertilizer' => $qbaseline->CostUnsubsidizedFertilizer,
                    'OtherIncome' => $qbaseline->OtherIncome,
                    'PensionPlan' => $qbaseline->PensionPlan,
                    'OtherIncomeRegular' => $qbaseline->OtherIncomeRegular,
                    'SourceOtherIncomeGajiTetap' => $qbaseline->SourceOtherIncomeGajiTetap,
                    'SourceOtherIncomeGajiPasangan' => $qbaseline->SourceOtherIncomeGajiPasangan,
                    'SourceOtherIncomeUsaha' => $qbaseline->SourceOtherIncomeUsaha,
                    'SourceOtherIncomeFamily' => $qbaseline->SourceOtherIncomeFamily,
                    'SourceOtherIncomeLainnya' => $qbaseline->SourceOtherIncomeLainnya,
                    'AmountOtherIncome' => $qbaseline->AmountOtherIncome,
                    'CocoaProfitableBusiness' => $CocoaProfitableBusiness,
                    'LoanBetterThanSaving' => $LoanBetterThanSaving,
                    'UnsubsidizedFertilizerProfitable' => $qbaseline->UnsubsidizedFertilizerProfitable,
                    'HighInterestRate' => $qbaseline->HighInterestRate,
                    'LoanWithTrader' => $qbaseline->LoanWithTrader,
                    'BetterWetDriedBeans' => $BetterWetDriedBeans,
                    'GoodLoanClient' => $GoodLoanClient,
                    'TrustGroupMembers' => $qbaseline->TrustGroupMembers,
                    'RepayLoanGroupMember' => $qbaseline->RepayLoanGroupMember,
                    'TrustBank' => $TrustBank,
                    'CocoaFarmPayExpenses' => $qbaseline->CocoaFarmPayExpenses,
                    'DiscipilinedSaveMoney' => $DiscipilinedSaveMoney,
                    'TradersRich' => $qbaseline->TradersRich,
                    'CollateralOfferedBank' => $CollateralOfferedBank,
                    'ManyCocoaFarmSale' => $ManyCocoaFarmSale,
                    'SatisfiedCocoaBusiness' => $qbaseline->SatisfiedCocoaBusiness,
                    'PayCocoaBetterInterest' => $qbaseline->PayCocoaBetterInterest,
                    'NeedLoan' => $qbaseline->NeedLoan,
                    'MobilePhone' => $qbaseline->MobilePhone,
                    'LoanAnalysisKnowledge' => $qbaseline->LoanAnalysisKnowledge,
                    'IslamicFinancialAwareness' => $qbaseline->IslamicFinancialAwareness,
                    'LearnToSaveMoney' => $qbaseline->LearnToSaveMoney,
                    'CocoaPriceToday' => $CocoaPriceToday,
                    'CocoaPriceTodayInfo' => $CocoaPriceTodayInfo,
                    'ReasonNotHavePhoneTidakButuh' => $ReasonNotHavePhoneTidakButuh,
                    'ReasonNotHavePhoneMahal' => $ReasonNotHavePhoneMahal,
                    'ReasonNotHavePhoneSinyal' => $ReasonNotHavePhoneSinyal,
                    'ReasonNotHavePhoneLainnya' => $ReasonNotHavePhoneLainnya,
                    'ValueCocoaFarm' => $ValueCocoaFarm,
                    'InsuranceKnowledge' => $qbaseline->InsuranceKnowledge,
                    'PastNowInsurance' => $qbaseline->PastNowInsurance,
                    'InsuranceTypeMotor' => $InsuranceTypeMotor,
                    'InsuranceTypePanen' => $InsuranceTypePanen,
                    'InsuranceTypeBanjir' => $InsuranceTypeBanjir,
                    'InsuranceTypeKemarau' => $InsuranceTypeKemarau,
                    'InsuranceTypeMobil' => $InsuranceTypeMobil,
                    'InsuranceTypeKesehatan' => $InsuranceTypeKesehatan,
                    'InsuranceTypeJiwa' => $InsuranceTypeJiwa,
                    'InsuranceTypeLainnya' => $InsuranceTypeLainnya,
                        //'DateCreated'=>$DateCreated,
                        // 'CreatedBy'=>$CreatedBy,
                        //                        'DateUpdated'=>date('Y-m-d H:m:s'),
                        //                        'LastModifiedBy'=>$CreatedBy
                );

                $dupdate['DateCreated'] = date('Y-m-d H:m:s');
                $dupdate['CreatedBy'] = $CreatedBy;
                $this->db->insert('ktv_farmer_financial', $dupdate);
            }
        }

//      if ($survey[0]==0)
        //        {
        //            //baseline
        ////            $this->addSurveyAff($FarmerID,$survey[0],$CreatedBy,$InterviewDate);
        //          $dupdate['DateCreated'] = date('Y-m-d H:m:s');
        //          $dupdate['CreatedBy'] = $CreatedBy;
        //          $this->db->insert('ktv_farmer_financials',$dupdate);
        //        } else
        //            {
        //                $add = ",LastModifiedBy=$userid,DateUpdated=now()";
        //                $dupdate['DateUpdated'] = date('Y-m-d H:m:s');
        //                $dupdate['LastModifiedBy'] = $CreatedBy;
        //
        //                $this->db->where('SurveyNr',$SurveyNr);
        //                $this->db->where('FarmerID',$FarmerID);
        //                $this->db->update('ktv_farmer_financialx',$dupdate);
        //
        //                $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //                $query = $this->db->query($sql_date_survey, array($FarmerID));
        //            }

        /* $sql = "
          UPDATE ktv_farmer_financial
          SET SurveyNr=?,InterviewDate=?,Account=?,AccountTypeTabungan=?,AccountTypeDeposito=?,AccountTypeKoran=?,
          AccountTypeLainnya=?,AccountHolderFarmer=?,AccountHolderName=?,AccountBankName=?,AccountBankBranch=?,
          AccountNumber=?,AccountNoDetails=?,DepositWithdrawnMoneyLast12m=?,AccountFeesToPay=?,AccountInterestRate=?,
          MoneyUsageHarian=?,MoneyUsageTabung=?,MoneyUsageInvestasi=?,MoneyUsageEmas=?,MoneyUsageKonsumsi=?,
          NotSavingJauh=?,NotSavingTidakBeruang=?,NotSavingBiayaTinggi=?,NotSavingTidakPercaya=?,NotSavingAdaMenabung=?,
          NotSavingLainnya=?,SavingUnitRumah=?,SavingUnitBank=?,SavingUnitKoperasi=?,SavingUnitPedagang=?,
          SavingUnitArisan=?,SavingUnitOrang=?,SavingUnitLembaga=?,SavingUnitMeminjamkan=?,DistanceSavingLocation=?,
          AmountSaving=?,FutureReasonSekolah=?,FutureReasonRumahTangga=?,FutureReasonSumbangan=?,FutureReasonDarurat=?,
          FutureReasonKesehatan=?,FutureReasonInvestasiKebun=?,FutureReasonInvestasiLain=?,FutureReasonRumah=?,
          FutureReasonLahan=?,FutureReasonKendaraan=?,FutureReasonHaji=?,FutureReasonPensiun=?,FutureReasonLain=?,
          ImportantFactorKemanan=?,ImportantFactorLikuiditas=?,ImportantFactorAksesibilitas=?,ImportantFactorKepercayaan=?,
          ImportantFactorBiaya=?,ImportantFactorBunga=?,ImportantFactorLain=?,LoanYesNo=?,AmountCurrentLoan=?,
          AmountOutsCurrentLoan=?,LoanUnitTengkulak=?,LoanUnitKeluarga=?,LoanUnitRentenir=?,LoanUnitBank=?,
          LoanUnitKoperasi=?,LoanUnitMasjid=?,LoanUnitLainnya=?,PreviousLoan=?,CollateralCurrentLoan=?,EasyCurrentLoan=?,
          DisburseIntervalCurrentLoan=?,RepaymentScheduleCurrentLoan=?,EasyGetNewLoan=?,UsageCurrentLoanHarian=?,
          UsageCurrentLoanSekolah=?,UsageCurrentLoanRumahTangga=?,UsageCurrentLoanSumbangan=?,UsageCurrentLoanHutang=?,
          UsageCurrentLoanDarurat=?,UsageCurrentLoanKesehatan=?,UsageCurrentLoanInvestasiKebun=?,UsageCurrentLoanInvestasiLain=?,
          UsageCurrentLoanRumah=?,UsageCurrentLoanLahan=?,UsageCurrentLoanKendaraan=?,UsageCurrentLoanHaji=?,
          UsageCurrentLoanPensiun=?,UsageCurrentLoanLainnya=?,TerminatedLoan=?,MoneyToRepayLoanPenghasilan=?,
          MoneyToRepayLoanPinjaman=?,MoneyToRepayLoanTanah=?,MoneyToRepayLoanTernak=?,MoneyToRepayLoanDeposito=?,
          MoneyToRepayLoanLainnya=?,ProfitSharingLoan=?,ResponsibilityLoan=?,WorryToRepayLoan=?,DifficultCoverExpenses=?,
          PostponeExpensesSewaRumah=?,PostponeExpensesKebun=?,PostponeExpensesMakanan=?,PostponeExpensesKesehatan=?,
          PostponeExpensesSosial=?,PostponeExpensesListrik=?,PostponeExpensesPendidikan=?,PostponeExpensesSandang=?,
          PostponeExpensesAngsuran=?,PostponeExpensesLainnya=?,DifficultSocialContributions=?,MoneyUrgentExpensesTabungan=?,
          MoneyUrgentExpensesMeminjamKeluarga=?,MoneyUrgentExpensesMeminjamTengkulak=?,MoneyUrgentExpensesMenjual=?,
          MoneyUrgentExpensesLainnya=?,CostUnsubsidizedFertilizer=?,OtherIncome=?,PensionPlan=?,OtherIncomeRegular=?,
          SourceOtherIncomeGajiTetap=?,SourceOtherIncomeGajiPasangan=?,SourceOtherIncomeUsaha=?,SourceOtherIncomeFamily=?,
          SourceOtherIncomeLainnya=?,AmountOtherIncome=?,CocoaProfitableBusiness=?,LoanBetterThanSaving=?,
          UnsubsidizedFertilizerProfitable=?,HighInterestRate=?,LoanWithTrader=?,BetterWetDriedBeans=?,GoodLoanClient=?,
          TrustGroupMembers=?,RepayLoanGroupMember=?,TrustBank=?,CocoaFarmPayExpenses=?,DiscipilinedSaveMoney=?,
          TradersRich=?,CollateralOfferedBank=?,ManyCocoaFarmSale=?,SatisfiedCocoaBusiness=?,PayCocoaBetterInterest=?,
          NeedLoan=?,MobilePhone=?,LoanAnalysisKnowledge=?,IslamicFinancialAwareness=?,LearnToSaveMoney=?,
          CocoaPriceToday=?,ReasonNotHavePhoneTidakButuh=?,ReasonNotHavePhoneMahal=?,ReasonNotHavePhoneSinyal=?,
          ReasonNotHavePhoneLainnya=?,ValueCocoaFarm=?,InsuranceKnowledge=?,PastNowInsurance=?,InsuranceTypeMotor=?,
          InsuranceTypePanen=?,InsuranceTypeBanjir=?,InsuranceTypeKemarau=?,InsuranceTypeMobil=?,InsuranceTypeKesehatan=?,
          InsuranceTypeJiwa=?,InsuranceTypeLainnya=?$add
          WHERE SurveyNr=? and FarmerID=?"; */

        /* $query = $this->db->query($sql, array($survey[0],$InterviewDate,$Account,$AccountTypeTabungan,$AccountTypeDeposito,$AccountTypeKoran,
          $AccountTypeLainnya,$AccountHolderFarmer,$AccountHolderName,$AccountBankName,$AccountBankBranch,
          $AccountNumber,$AccountNoDetails,$DepositWithdrawnMoneyLast12m,$AccountFeesToPay,$AccountInterestRate,
          $MoneyUsageHarian,$MoneyUsageTabung,$MoneyUsageInvestasi,$MoneyUsageEmas,$MoneyUsageKonsumsi,
          $NotSavingJauh,$NotSavingTidakBeruang,$NotSavingBiayaTinggi,$NotSavingTidakPercaya,$NotSavingAdaMenabung,
          $NotSavingLainnya,$SavingUnitRumah,$SavingUnitBank,$SavingUnitKoperasi,$SavingUnitPedagang,
          $SavingUnitArisan,$SavingUnitOrang,$SavingUnitLembaga,$SavingUnitMeminjamkan,$DistanceSavingLocation,
          $AmountSaving,$FutureReasonSekolah,$FutureReasonRumahTangga,$FutureReasonSumbangan,$FutureReasonDarurat,
          $FutureReasonKesehatan,$FutureReasonInvestasiKebun,$FutureReasonInvestasiLain,$FutureReasonRumah,
          $FutureReasonLahan,$FutureReasonKendaraan,$FutureReasonHaji,$FutureReasonPensiun,$FutureReasonLain,
          $ImportantFactorKemanan,$ImportantFactorLikuiditas,$ImportantFactorAksesibilitas,$ImportantFactorKepercayaan,
          $ImportantFactorBiaya,$ImportantFactorBunga,$ImportantFactorLain,$LoanYesNo,$AmountCurrentLoan,
          $AmountOutsCurrentLoan,$LoanUnitTengkulak,$LoanUnitKeluarga,$LoanUnitRentenir,$LoanUnitBank,
          $LoanUnitKoperasi,$LoanUnitMasjid,$LoanUnitLainnya,$PreviousLoan,$CollateralCurrentLoan,$EasyCurrentLoan,
          $DisburseIntervalCurrentLoan,$RepaymentScheduleCurrentLoan,$EasyGetNewLoan,$UsageCurrentLoanHarian,
          $UsageCurrentLoanSekolah,$UsageCurrentLoanRumahTangga,$UsageCurrentLoanSumbangan,$UsageCurrentLoanHutang,
          $UsageCurrentLoanDarurat,$UsageCurrentLoanKesehatan,$UsageCurrentLoanInvestasiKebun,$UsageCurrentLoanInvestasiLain,
          $UsageCurrentLoanRumah,$UsageCurrentLoanLahan,$UsageCurrentLoanKendaraan,$UsageCurrentLoanHaji,
          $UsageCurrentLoanPensiun,$UsageCurrentLoanLainnya,$TerminatedLoan,$MoneyToRepayLoanPenghasilan,
          $MoneyToRepayLoanPinjaman,$MoneyToRepayLoanTanah,$MoneyToRepayLoanTernak,$MoneyToRepayLoanDeposito,
          $MoneyToRepayLoanLainnya,$ProfitSharingLoan,$ResponsibilityLoan,$WorryToRepayLoan,$DifficultCoverExpenses,
          $PostponeExpensesSewaRumah,$PostponeExpensesKebun,$PostponeExpensesMakanan,$PostponeExpensesKesehatan,
          $PostponeExpensesSosial,$PostponeExpensesListrik,$PostponeExpensesPendidikan,$PostponeExpensesSandang,
          $PostponeExpensesAngsuran,$PostponeExpensesLainnya,$DifficultSocialContributions,$MoneyUrgentExpensesTabungan,
          $MoneyUrgentExpensesMeminjamKeluarga,$MoneyUrgentExpensesMeminjamTengkulak,$MoneyUrgentExpensesMenjual,
          $MoneyUrgentExpensesLainnya,$CostUnsubsidizedFertilizer,$OtherIncome,$PensionPlan,$OtherIncomeRegular,
          $SourceOtherIncomeGajiTetap,$SourceOtherIncomeGajiPasangan,$SourceOtherIncomeUsaha,$SourceOtherIncomeFamily,
          $SourceOtherIncomeLainnya,$AmountOtherIncome,$CocoaProfitableBusiness,$LoanBetterThanSaving,
          $UnsubsidizedFertilizerProfitable,$HighInterestRate,$LoanWithTrader,$BetterWetDriedBeans,$GoodLoanClient,
          $TrustGroupMembers,$RepayLoanGroupMember,$TrustBank,$CocoaFarmPayExpenses,$DiscipilinedSaveMoney,
          $TradersRich,$CollateralOfferedBank,$ManyCocoaFarmSale,$SatisfiedCocoaBusiness,$PayCocoaBetterInterest,
          $NeedLoan,$MobilePhone,$LoanAnalysisKnowledge,$IslamicFinancialAwareness,$LearnToSaveMoney,
          $CocoaPriceToday,$ReasonNotHavePhoneTidakButuh,$ReasonNotHavePhoneMahal,$ReasonNotHavePhoneSinyal,
          $ReasonNotHavePhoneLainnya,$ValueCocoaFarm,$InsuranceKnowledge,$PastNowInsurance,$InsuranceTypeMotor,
          $InsuranceTypePanen,$InsuranceTypeBanjir,$InsuranceTypeKemarau,$InsuranceTypeMobil,$InsuranceTypeKesehatan,
          $InsuranceTypeJiwa,$InsuranceTypeLainnya,
          $SurveyNr,$farmerid)); */

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "record updated.";
        }

        /*  if ($query) {
          $results['success'] = true;
          $results['message'] = "record updated.";
          } else {
          $results['success'] = false;
          $results['message'] = "Failed to update record";
          } */
        return $results;
    }

    public function readSumAff($id) {
        $sql = "SELECT concat(a.SurveyNr,'-',SurveyTxt) as Survey,InterviewDate as DateInterview,
               a.DateUpdated as DateValid,isValid as StatusValid,a.FarmerID as farmerid,
               a.SurveyNr as id, concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya,
               c.UserRealName as UserCreated,
               d.UserRealName as LastUpdatedBy
            FROM ktv_farmer_financial a
            LEFT JOIN ktv_survey b ON a.SurveyNr=b.SurveyNr
            LEFT JOIN sys_user c ON a.CreatedBy = c.UserId
            LEFT JOIN sys_user d ON a.LastModifiedBy = d.UserId
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getFarmerDetail($id) {
        /* $sql = "SELECT
          a.FarmerID,
          a.FarmerName,
          b.GroupName,
          ( CASE WHEN #a.Photo REGEXP '[]/[]'=1 AND
          a.Photo<>'' THEN CONCAT('images/Photo/',a.Photo)
          #WHEN a.Photo REGEXP '[]/[]'=0 AND a.Photo<>'' THEN CONCAT('images/Photo/',kp.Province,a.Photo)
          ELSE ''
          END
          ) FarmerPhoto

          ,IF(e.FlagAccess = 1, e.PartnerName, d.PartnerName) AS PartnerName
          ,CONCAT('images/Photo/',IF(e.FlagAccess = 1, e.Photo, d.Photo))  PartnerPhoto
          ,CONCAT('images/Photo/Program/',IF(e.FlagAccess = 1, e.Logo, d.Logo))  PartnerLogo
          FROM
          ktv_farmer a
          LEFT JOIN ktv_cpg b ON a.CPGid = b.CPGid
          LEFT JOIN ktv_district c ON SUBSTR(a.VillageID, 1, 4) = c.DistrictID
          LEFT JOIN ktv_province kp ON SUBSTR(a.VillageID, 1, 2) = kp.ProvinceID
          LEFT JOIN ktv_program_partner d ON c.PartnerID = d.PartnerID AND a.StatusCode='active'
          LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = a.CPGid
          LEFT JOIN ktv_program_partner e ON cp.PartnerID = e.PartnerID AND a.StatusCode='active'
          WHERE
          1 = 1
          AND a.FarmerID = ?
          ";
          return $this->db->query($sql, array($id))->first_row('array'); */
        //*Update tgl 2016-04-29*//
        $sql = "SELECT
                    a.FarmerID,
                    a.FarmerName,
                    b.GroupName,
                    ( CASE WHEN a.Photo<>'' THEN CONCAT('images/Photo/',a.Photo) ELSE '' END ) FarmerPhoto,
                    GROUP_CONCAT(d.PartnerName) PartnerName,
                    GROUP_CONCAT(CONCAT('images/Photo/',d.Photo)) PartnerPhoto,
                    IF(d.PhotoProgram='','',GROUP_CONCAT(CONCAT('images/Photo/',d.PhotoProgram))) ProgramPhoto,
                    GROUP_CONCAT(d.PartnerID) PartnerID,
                    COUNT(d.PartnerID) AS TotalPartner,
                    IFNULL(dt.tahun,dt2.tahun) tahun
                FROM
                    ktv_farmer a
                    LEFT JOIN ktv_cpg b ON a.CPGid = b.CPGid
                    LEFT JOIN ktv_cpg_partner c ON a.CPGid=c.CPGid
                    LEFT JOIN ktv_program_partner d ON c.PartnerID=d.PartnerID
                    LEFT JOIN (
                        SELECT
                            a.FarmerID, YEAR(MIN(b.TrainingStart)) tahun
                        FROM
                            ktv_cpg_batch_trainings_farmers a
                            LEFT JOIN ktv_cpg_batch_trainings b ON b.CpgBatchTrainingID=a.CpgBatchTrainingID
                        GROUP BY a.FarmerID
                    ) dt ON dt.FarmerID=a.FarmerID
                    LEFT JOIN (
                        SELECT
                            a.FarmerID, YEAR(MIN(b.TrainingStart)) tahun
                        FROM
                            ktv_kader_trainings_participants a
                            LEFT JOIN ktv_kader_trainings b ON a.CpgKaderTrainingID=b.CpgKaderTrainingID
                        GROUP BY a.FarmerID
                    ) dt2 ON dt2.FarmerID=a.FarmerID
                WHERE a.FarmerID=? AND d.PartnerIndustry!=1 AND (d.PhotoProgram IS NOT NULL AND d.PhotoProgram!='')";
        $query = $this->db->query($sql, array($id));
        if ($query->row()->TotalPartner != "0") {
            return $query->first_row('array');
        } else {
            $sql = "SELECT
                        f.FarmerID,
                        f.FarmerName,
                        c.GroupName,
                        ( CASE WHEN f.Photo<>'' THEN CONCAT('images/Photo/',f.Photo) ELSE '' END ) FarmerPhoto,
                        GROUP_CONCAT(p.PartnerName) PartnerName,
                        GROUP_CONCAT(CONCAT('images/Photo/',p.Photo)) PartnerPhoto,
                        IF(p.PhotoProgram='','',GROUP_CONCAT(CONCAT('images/Photo/',p.PhotoProgram))) ProgramPhoto,
                        GROUP_CONCAT(p.PartnerID) PartnerID,
                        COUNT(p.PartnerID) AS TotalPartner
                    FROM
                        ktv_farmer f
                        JOIN ktv_village v ON v.VillageID = f.VillageID
                        JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                        JOIN ktv_district_partner dp ON dp.DistrictID = sd.DistrictID
                        JOIN ktv_program_partner p ON p.PartnerID = dp.PartnerID AND PartnerIndustry != 1
                        LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                        LEFT JOIN ktv_cpg c ON f.CPGid=c.CPGid
                    WHERE
                        f.FarmerID = ?
                        AND p.PartnerID <> 1 AND p.PartnerID <> 2 AND p.PartnerID <> 23
                        AND p.PartnerIndustry <> '0' AND p.PartnerIndustry <> '1' AND p.PartnerIndustry <> '6'
                        AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                    LIMIT 1";
            $query = $this->db->query($sql, array($id));
            if ($query->row()->TotalPartner != "0") {
                return $query->first_row('array');
            } else {
                $sql = "SELECT
                            f.FarmerID,
                            f.FarmerName,
                            c.GroupName,
                            ( CASE WHEN f.Photo<>'' THEN CONCAT('images/Photo/',f.Photo) ELSE '' END ) FarmerPhoto,
                            GROUP_CONCAT(p.PartnerName) PartnerName,
                            GROUP_CONCAT(CONCAT('images/Photo/',p.Photo)) PartnerPhoto,
                            IF(p.PhotoProgram='','',GROUP_CONCAT(CONCAT('images/Photo/',p.PhotoProgram))) ProgramPhoto,
                            GROUP_CONCAT(p.PartnerID) PartnerID,
                            COUNT(p.PartnerID) AS TotalPartner
                        FROM
                            ktv_farmer f
                            JOIN ktv_village v ON v.VillageID = f.VillageID
                            JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                            JOIN ktv_district_partner dp ON dp.DistrictID = sd.DistrictID
                            JOIN ktv_program_partner p ON p.PartnerID = dp.PartnerID AND PartnerIndustry != 1
                            LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = f.CPGid
                            LEFT JOIN ktv_cpg c ON f.CPGid=c.CPGid
                        WHERE
                            f.FarmerID = ?
                            AND p.PartnerID <> 1 AND p.PartnerID <> 2 AND p.PartnerID <> 23
                            AND p.PartnerIndustry <> '0' AND p.PartnerIndustry <> '1' AND p.PartnerIndustry <> '6'
                            AND IF(FlagAccess='1',p.PartnerID,'') = IF(FlagAccess='1',cp.PartnerID,'')
                        LIMIT 1";
                $query = $this->db->query($sql, array($id));
                return $query->first_row('array');
            }
        }
    }

    public function readCertHolders($prov = '%%') {
        if ($prov == '') {
            $prov = '%%';
        }
        $sql = "
            SELECT * from (
            SELECT concat('warehouse|',WarehouseID) id,concat('[Gudang] ',WarehouseName) label,VillageID village
            FROM ktv_warehouse
            UNION ALL
            SELECT concat('trader|',TraderID) id,concat('[Pedagang] ',IFNULL(TraderName,Company)) label,VillageID village
            FROM ktv_traders
            UNION ALL
            SELECT concat('koperasi|',CoopID) id,concat('[Organisasi Petani] ',CoopName) label,VillageID village
            FROM ktv_cooperatives
            ) a where substr(village,1,2) like ?";
        $query = $this->db->query($sql, array($prov));
        return $query->result_array();
    }

    public function readCertHolders2($prov = '%%', $jenis) {
        if ($jenis == 'Gudang') {
            $sql = "SELECT WarehouseID AS id,WarehouseName AS label,ktv_warehouse.VillageID
                    FROM ktv_warehouse
                    LEFT JOIN ktv_village v ON v.`VillageID` = ktv_warehouse.`VillageID`
                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                    LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`";
        }
        if ($jenis == 'Pedagang') {
            $sql = "SELECT TraderID as id,Company as label,ktv_traders.VillageID
                    FROM ktv_traders
                    LEFT JOIN ktv_village v ON v.`VillageID` = ktv_traders.`VillageID`
                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                    LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`";
        }
        if ($jenis == 'Organisasi Petani') {
            $sql = "SELECT CoopID as id,CoopName as label,ktv_cooperatives.VillageID
                    FROM ktv_cooperatives
                    LEFT JOIN ktv_village v ON v.`VillageID` = ktv_cooperatives.`VillageID`
                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                    LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`";
        }
        $sql .= " where p.`ProvinceID` like ?";
        if($prov=='') $prov = '%%';
        $query = $this->db->query($sql, array($prov));
        return $query->result_array();
    }

    public function readCertStaffs($prov, $certHolder) {
        /*
          $sql = "SELECT
          ExtensionID as staffid,
          StaffName
          FROM
          ktv_extension_staff
          UNION ALL
          SELECT
          b.PersonID,PersonNm
          FROM
          ktv_program_staff a
          join ktv_persons b ON a.PersonID = b.PersonID
          ORDER BY StaffName";
         *
         */
        /*
          $sql = "SELECT
          a.icsMemberID as staffid,
          CONCAT('[',a.FarmerID,'] ',b.FarmerName) as StaffName
          FROM
          ktv_ics_members a
          LEFT JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
          WHERE
          SUBSTR(a.FarmerID,1,2) = {$prov}
          ORDER BY b.FarmerName";
         *
         */
        $sql = "SELECT
                    z.icsid as staffid,
                    concat('[',z.id,'] ',z.`name`) as StaffName
                FROM
                (
                    SELECT
            v.IcsMemberID as icsid,
            a.PrivateStaffID as id,
            a.StaffName as `name`,
            IF(a.StaffGender = '1','Pria','Wanita') as gender,
            c.District as district,
            CONCAT('') as subdistrict,
            CONCAT('private staff') as tipe
                    FROM
            ktv_private_staff a
            LEFT JOIN ktv_province b ON SUBSTR(a.Location,1,2) = b.ProvinceID
            LEFT JOIN ktv_district c ON a.Location = c.DistrictID
            LEFT JOIN ktv_ics_members v ON a.PrivateStaffID = v.FarmerID AND v.StatusCode = 'active'
            LEFT JOIN ktv_ics ki ON v.IcsID = ki.IcsID
            LEFT JOIN ktv_cooperatives kc ON ki.ObjID = kc.CoopID
                    WHERE
            kc.CoopName = '{$certHolder}'
                    UNION
                    SELECT
            w.IcsMemberID as icsid,
            d.PersonID as id,
            e.PersonNm as `name`,
            IF(e.Gender = 'm','Pria','Wanita') as gender,
            g.District as district,
            CONCAT('') as subdistrict,
            CONCAT('program staff') as tipe
                    FROM
            ktv_program_staff d
            JOIN ktv_persons e ON d.PersonID = e.PersonID
            LEFT JOIN ktv_province f ON SUBSTR(d.WorkArea,1,2) = f.ProvinceID
            LEFT JOIN ktv_district g ON d.WorkArea = g.DistrictID
            LEFT JOIN ktv_ics_members w ON d.PersonID = w.FarmerID AND w.StatusCode = 'active'
            LEFT JOIN ktv_ics ki2 ON w.IcsID = ki2.IcsID
            LEFT JOIN ktv_cooperatives kc2 ON ki2.ObjID = kc2.CoopID
                    WHERE
            kc2.CoopName = '{$certHolder}'
                    UNION
                    SELECT
            x.IcsMemberID as icsid,
            h.FarmerID as id,
            h.FarmerName as `name`,
            IF(h.Gender = '1','Pria','Wanita') as gender,
            j.District as district,
            l.SubDistrict as subdistrict,
            CONCAT('farmer') as tipe
                    FROM
            ktv_farmer h
            LEFT JOIN ktv_province i ON SUBSTR(h.FarmerID,1,2) = i.ProvinceID
            LEFT JOIN ktv_district j ON SUBSTR(h.FarmerID,1,4) = j.DistrictID
            LEFT JOIN ktv_village k ON h.VillageID = k.VillageID
            LEFT JOIN ktv_subdistrict l ON k.SubDistrictID = l.SubDistrictID
            LEFT JOIN ktv_ics_members x ON h.FarmerID = x.FarmerID AND x.StatusCode = 'active'
            LEFT JOIN ktv_ics ki3 ON x.IcsID = ki3.IcsID
            LEFT JOIN ktv_cooperatives kc3 ON ki3.ObjID = kc3.CoopID
                    WHERE
            kc3.CoopName = '{$certHolder}'
                    ) z";
        $q = $this->db->query($sql);
        return $q->result_array();
    }

    public function addSertifikasiLog($ICSDate, $ICSDateOld, $Certification, $StatusAudit, $DateRevisionAudit, $CommentAudit, $RecommendationAudit, $InpectorID, $AuditCommiteeID, $IMSManagerID, $FarmerID, $GardenNr, $SurveyNr, $userid, $jenis, $FarmerSignature, $InspectorSignature, $AuditCommiteeSignature, $IMSManagerSignature) {
        $d = array(
            'FarmerID' => $FarmerID,
            'GardenNr' => $GardenNr,
            'SurveyNr' => $SurveyNr,
            'Certification' => $Certification,
            'ICSDate' => $ICSDate,
            'StatusAudit' => $StatusAudit,
            'DateRevisionAudit' => $DateRevisionAudit,
            'CommentAudit' => $CommentAudit,
            'RecommendationAudit' => $RecommendationAudit,
            'InspectorID' => $InpectorID,
            'AuditCommiteeID' => $AuditCommiteeID,
            'IMSManagerID' => $IMSManagerID,
        );
        $s = array(
            'FarmerID' => $FarmerID,
            'GardenNr' => $GardenNr,
            'SurveyNr' => $SurveyNr,
            'Certification' => $Certification,
            'ICSDate' => $ICSDate,
            'FarmerSignature' => $FarmerSignature,
            'InspectorSignature' => $InspectorSignature,
            'AuditCommiteeSignature' => $AuditCommiteeSignature,
            'IMSManagerSignature' => $IMSManagerSignature,
        );

        $wer = array(
            'FarmerID' => $FarmerID,
            'GardenNr' => $GardenNr,
            'SurveyNr' => $SurveyNr,
            'Certification' => $Certification,
            'ICSDate' => $ICSDateOld,
        );
        $q = $this->db->get_where('ktv_certification_audit_log', $wer);
        if ($jenis == '1') {
            $a = array('LastModifiedBy' => $userid, 'DateUpdated' => date('Y-m-d H:m:s'));
            $this->db->where($wer);
            //$this->db->update('ktv_certification_signature',array_merge($s,$a));
            $this->db->update('ktv_certification_audit_log', array_merge($d, $a));
//            $this->db->where($wer);
            //          echo $this->db->last_query();
        } else {
            $u = array('CreatedBy' => $userid, 'DateCreated' => date('Y-m-d H:m:s'));
            $this->db->insert('ktv_certification_signature', array_merge($s, $u));
            $this->db->insert('ktv_certification_audit_log', array_merge($d, $u));
        }

        if ($this->db->affected_rows() > 0) {
            $results['success'] = true;
            $results['message'] = "record updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function readLastAuditLog($farmer_id, $GardenNr, $gSurveyNr, $certification) {
        if ($certification != null) {
            $svnr = explode(" - ", $gSurveyNr);
            $sql = "SELECT
                        c.*,
                        a.Certification,
                        a.ICSDate,
                        CASE WHEN a.StatusAudit=1 THEN 'Lolos'
                            WHEN a.StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAuditName,
                        a.StatusAudit,
                        a.DateRevisionAudit,
                        a.CommentAudit,
                        a.RecommendationAudit,
                        x.StaffName as CertificationHolder
                    FROM
                        ktv_certification_audit_log a
                        LEFT JOIN ktv_ics_members b ON a.InspectorID = b.IcsMemberID
                        LEFT JOIN (
                            SELECT
                                PrivateStaffID as staffid,
                                StaffName as StaffName
                            FROM
                                ktv_private_staff
                            UNION ALL
                            SELECT
                                b.PersonID as staffid,
                                PersonNm as StaffName
                            FROM
                                ktv_program_staff a
                                join ktv_persons b ON a.PersonID = b.PersonID
                            UNION ALL
                            SELECT
                                FarmerID as staffid,
                                FarmerName as StaffName
                            FROM
                                ktv_farmer
                        ) x ON b.FarmerID = x.staffid
                        LEFT JOIN ktv_certification_signature c on a.FarmerID = c.FarmerID AND
                            a.GardenNr = c.GardenNr and
                            a.SurveyNr = c.SurveyNr and
                            a.Certification=c.Certification
                    WHERE
                        a.FarmerID =  $farmer_id
                        AND a.GardenNr =  $GardenNr
                        AND a.SurveyNr = $svnr[0]
                        AND a.Certification =  $certification
                    ORDER BY a.DateCreated desc
                    LIMIT 1";
            //        $wer = array(
            //            'FarmerID'=>$farmer_id,
            //            'GardenNr'=>$GardenNr,
            //            'SurveyNr'=>$gSurveyNr,
            //            'Certification'=>$certification
            //        );
            //        $this->db->limit(1);
            //        $this->db->order_by('DateRevisionAudit','asc');
            //        $q = $this->db->get_where('ktv_certification_audit_logs',$wer);
            $q = $this->db->query($sql);
            if ($q->num_rows() > 0) {
                $result = $q->result_array();
                $d = array('success' => true, 'data' => $result[0], 'message' => null);
            } else {
                $d = array('success' => false, 'data' => null, 'message' => null);
            }
        } else {
            $d = array('success' => false, 'data' => null, 'message' => null);
        }

        return $d;
    }

    public function readAuditLogs($farmer_id, $GardenNr, $gSurveyNr, $certification) {
        /*
         * $sql = "SELECT
          c.*,
          a.*,
          b.StaffName,
          CASE WHEN a.StatusAudit=1 THEN 'Lolos' WHEN a.StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAuditName
          FROM
          ktv_certification_audit_log a
          left join (
          SELECT
          ExtensionID as staffid,
          StaffName
          FROM
          ktv_extension_staff
          UNION ALL
          SELECT
          b.PersonID,
          PersonNm
          FROM
          ktv_program_staff a
          join ktv_persons b ON a.PersonID = b.PersonID
          ) b ON a.InspectorID = b.staffid
          left join ktv_certification_signature c on a.FarmerID = c.FarmerID AND a.GardenNr = c.GardenNr
          AND a.SurveyNr = c.SurveyNr and a.ICSDate=c.ICSDate
          WHERE
          a.FarmerID = $farmer_id
          AND a.GardenNr = $GardenNr
          AND a.SurveyNr = $svnr[0]
          AND a.Certification = $certification
          ORDER BY
          a.DateCreated DESC";
         */
        $svnr = explode(" - ", $gSurveyNr);
        /*
          $sql = "SELECT
          c.*,
          a.*,
          d.FarmerName as StaffName,
          CASE WHEN a.StatusAudit=1 THEN 'Lolos' WHEN a.StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAuditName
          FROM
          ktv_certification_audit_log a
          LEFT JOIN ktv_ics_members b ON b.IcsMemberID = a.InspectorID
          LEFT JOIN ktv_farmer d ON b.FarmerID = d.FarmerID
          left join ktv_certification_signature c on a.FarmerID = c.FarmerID AND a.GardenNr = c.GardenNr
          AND a.SurveyNr = c.SurveyNr and a.ICSDate=c.ICSDate
          WHERE
          a.FarmerID = $farmer_id
          AND a.GardenNr = $GardenNr
          AND a.SurveyNr = $svnr[0]
          AND a.Certification = $certification
          ORDER BY
          a.DateCreated DESC";
         *
         */
        $sql = "SELECT
                    c.*,
                    a.*,

                    x.StaffName as StaffName,
                    CASE WHEN a.StatusAudit=1 THEN 'Lolos' WHEN a.StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAuditName
                FROM
                    ktv_certification_audit_log a
                    LEFT JOIN ktv_ics_members b ON a.InspectorID = b.IcsMemberID
                    LEFT JOIN (
                        SELECT
                            PrivateStaffID as staffid,
                            StaffName as StaffName
                        FROM
                            ktv_private_staff
                        UNION ALL
                        SELECT
                            b.PersonID as staffid,
                            PersonNm as StaffName
                        FROM
                            ktv_program_staff a
                            join ktv_persons b ON a.PersonID = b.PersonID
                        UNION ALL
                        SELECT
                            FarmerID as staffid,
                            FarmerName as StaffName
                        FROM
                            ktv_farmer
                    ) x ON b.FarmerID = x.staffid
                    LEFT JOIN ktv_certification_signature c on a.FarmerID = c.FarmerID AND
                        a.GardenNr = c.GardenNr and
                        a.SurveyNr = c.SurveyNr and
                        a.Certification=c.Certification
                WHERE
                    a.FarmerID = $farmer_id
                    AND a.GardenNr = $GardenNr
                    AND a.SurveyNr = $svnr[0]
                    AND a.Certification = $certification
                GROUP BY a.FarmerID,a.GardenNr,a.SurveyNr,a.Certification,a.ICSDate
                ORDER BY
                    a.DateCreated DESC";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readAuditLog($farmer_id, $GardenNr, $gSurveyNr, $certification, $ICSDate) {
        $sql = "select
                    b.*,a.*,a.ICSDate ICSDateOld
                from
                    ktv_certification_audit_log a
                    left join ktv_certification_signature b on a.FarmerID=b.FarmerID and a.GardenNr=b.GardenNr and
                        a.SurveyNr=b.SurveyNr and a.Certification=b.Certification and a.ICSDate=b.ICSDate
                where
                    a.FarmerID=? and
                    a.GardenNr=? and
                    a.SurveyNr=? and
                    a.Certification=? and
                    a.ICSDate=?";
        $query = $this->db->query($sql, array($farmer_id, $GardenNr, $gSurveyNr, $certification, $ICSDate));
        $result = $query->result_array();
        return array('success' => true, 'data' => $result[0], 'message' => null);
    }

    public function readCertificate($FarmerID, $SurveyID) {
        //$query = $this->db->get_where('ktv_certification',array('FarmerID'=>$FarmerID,'SurveyNr'=>$SurveyID));
        $sql = "SELECT kcc.*,IF(CertificationHolderJenis='trader',TraderName,
               IF(CertificationHolderJenis='Organisasi Petani',CoopName,IF(CertificationHolderJenis='warehouse',WarehouseName,''))) holder,
               kccs.FarmerSignature,kccs.InspectorSignature,kccs.AuditCommiteeSignature,kccs.IMSManagerSignature
            from ktv_certification kcc
            left join ktv_traders kt on kt.TraderID=kcc.CertificationHolder and CertificationHolderJenis='trader'
            left join ktv_cooperatives kc on kc.CoopID=kcc.CertificationHolder and CertificationHolderJenis='Organisasi Petani'
            left join ktv_warehouse kw on kw.WarehouseID=kcc.CertificationHolder and CertificationHolderJenis='warehouse'
            left join ktv_certification_signature kccs on kcc.FarmerID=kccs.FarmerID AND kcc.GardenNr=kccs.GardenNr AND kcc.SurveyNr=kccs.SurveyNr AND kcc.ICSDate=kccs.ICSDate AND kcc.Certification=kccs.Certification
            where kcc.FarmerID=? and kcc.SurveyNr=?";
        $query = $this->db->query($sql, array($FarmerID, $SurveyID));
        $result = $query->result_array();
        return $result;
    }

    public function readCertificateGarden($FarmerID, $SurveyID, $GardenNr) {
        //$query = $this->db->get_where('ktv_certification',array('FarmerID'=>$FarmerID,'SurveyNr'=>$SurveyID));
        $sql = "SELECT kcc.*,IF(CertificationHolderJenis='trader',TraderName,
               IF(CertificationHolderJenis='Organisasi Petani',CoopName,IF(CertificationHolderJenis='warehouse',WarehouseName,''))) holder
            FROM ktv_certification kcc
            LEFT JOIN ktv_traders kt ON kt.TraderID=kcc.CertificationHolder AND CertificationHolderJenis='trader'
            LEFT JOIN ktv_cooperatives kc ON kc.CoopID=kcc.CertificationHolder AND CertificationHolderJenis='Organisasi Petani'
            LEFT JOIN ktv_warehouse kw ON kw.WarehouseID=kcc.CertificationHolder AND CertificationHolderJenis='warehouse'
            WHERE
                FarmerID =?
                AND SurveyNr = ?
                AND GardenNr = ?
            LIMIT 1
        ";
        $query = $this->db->query($sql, array($FarmerID, $SurveyID, $GardenNr));
        $result = $query->row_array(0);
        return $result;
    }

    public function readCertLastAuditCetak($FarmerID, $SurveyID, $GardenNr, $certification) {
        // $wer = array(
        //     'FarmerID'=>$FarmerID,
        //     'GardenNr'=>$GardenNr,
        //     'SurveyNr'=>$SurveyID,
        //     'Certification'=>$certification
        // );
        // $this->db->limit(1);
        // $this->db->order_by('DateRevisionAudit','asc');
        // $q = $this->db->get_where('ktv_certification_audit_log',$wer);

        if ($certification != null) {
            $sql = "SELECT
                        a.*,
                        d.FarmerName as StaffName
                    FROM
                        ktv_certification_audit_log a
                        LEFT JOIN ktv_ics_members b ON b.IcsMemberID = a.InspectorID
                        LEFT JOIN ktv_farmer d ON b.FarmerID = d.FarmerID
                    WHERE FarmerID =  '$FarmerID'
                        AND SurveyNr =  $SurveyID
                        AND Certification =  $certification
                    ORDER BY DateCreated asc
                    limit 1";
            /*
              $sql = "SELECT
              a.*,b.StaffName
              FROM
              ktv_certification_audit_log a
              left join (
              select ExtensionID as staffid,StaffName from ktv_extension_staff
              UNION ALL
              select b.PersonID,PersonNm
              from ktv_program_staff a
              join ktv_persons b ON a.PersonID = b.PersonID
              ) b ON a.InspectorID = b.staffid
              WHERE FarmerID =  '$FarmerID'
              AND SurveyNr =  $SurveyID
              AND Certification =  $certification
              ORDER BY DateCreated asc
              limit 1";
             *
             */
            $q = $this->db->query($sql);
            if ($q->num_rows() > 0) {
                $result = $q->result_array();
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function readAuditLogsCetak($FarmerID, $SurveyID, $GardenNr, $certification) {
        if ($certification != null) {
            /*
              $sql = "SELECT
              a.*,
              CASE WHEN StatusAudit=1 THEN 'Lolos'
              WHEN StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAudit,b.StaffName
              FROM
              ktv_certification_audit_log a
              left join (
              select ExtensionID as staffid,StaffName from ktv_extension_staff
              UNION ALL
              select b.PersonID,PersonNm
              from ktv_program_staff a
              join ktv_persons b ON a.PersonID = b.PersonID) b ON a.InspectorID = b.staffid
              WHERE FarmerID =  '$FarmerID'
              AND GardenNr =  $GardenNr
              AND SurveyNr =  $SurveyID
              AND Certification =  $certification
              ORDER BY DateCreated asc
              limit 1";
             *
             */
            /*
              $sql = "SELECT
              a.*,
              CASE WHEN StatusAudit=1 THEN 'Lolos' WHEN StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAudit,
              d.FarmerName as StaffName
              FROM
              ktv_certification_audit_log a
              LEFT JOIN ktv_ics_members b ON b.IcsMemberID = a.InspectorID
              LEFT JOIN ktv_farmer d ON b.FarmerID = d.FarmerID
              WHERE
              a.FarmerID =  '$FarmerID'
              AND GardenNr =  $GardenNr
              AND SurveyNr =  $SurveyID
              AND Certification =  $certification
              ORDER BY DateCreated asc
              limit 1";
             */
            $sql = "SELECT
                        a.*,
                        CASE WHEN StatusAudit=1 THEN 'Lolos' WHEN StatusAudit=2 THEN 'Tidak Lolos' ELSE '' END as StatusAudit,
                        x.StaffName
                    FROM
                        ktv_certification_audit_log a
                        LEFT JOIN ktv_ics_members b ON a.InspectorID = b.IcsMemberID
                        LEFT JOIN (
                            SELECT
                                PrivateStaffID as staffid,
                                StaffName as StaffName
                            FROM
                                ktv_private_staff
                            UNION ALL
                            SELECT
                                b.PersonID as staffid,
                                PersonNm as StaffName
                            FROM
                                ktv_program_staff a
                                join ktv_persons b ON a.PersonID = b.PersonID
                            UNION ALL
                            SELECT
                                FarmerID as staffid,
                                FarmerName as StaffName
                            FROM
                                ktv_farmer
                        ) x ON b.FarmerID = x.staffid
                    WHERE
                        a.FarmerID =  '$FarmerID'
                        AND GardenNr =  $GardenNr
                        AND SurveyNr =  $SurveyID
                        AND Certification =  $certification
                    ORDER BY DateCreated asc
                        limit 1";
            $q = $this->db->query($sql);
            //$q->result_array();
            return $q->result_array();
        } else {
            return false;
        }
    }

    public function deleteAuditLog($farmer_id, $GardenNr, $gSurveyNr, $certification, $ICSDate) {
        $svnr = explode(" - ", $gSurveyNr);
        $sql = "DELETE FROM `ktv_certification_signature`
                WHERE
                    `FarmerID` =  '{$farmer_id}'
                    AND `GardenNr` =  '{$GardenNr}'
                    AND `SurveyNr` =  '{$svnr[0]}'
                    AND `Certification` =  '{$certification}'
                    AND `ICSDate` =  '{$ICSDate}'";

        $sql2 = "DELETE FROM `ktv_certification_audit_log`
                WHERE
                    `FarmerID` =  '{$farmer_id}'
                    AND `GardenNr` =  '{$GardenNr}'
                    AND `SurveyNr` =  '{$svnr[0]}'
                    AND `Certification` =  '{$certification}'
                    AND `ICSDate` =  '{$ICSDate}'";
        $q1 = $this->db->query($sql);
        $q2 = $this->db->query($sql2);

        if ($this->db->affected_rows() > 0) {
            $d = array('success' => true);
        } else {
            $d = array('success' => false);
        }
        /*
          $wer = array(
          'FarmerID,'=>$farmer_id,
          'GardenNr'=>$GardenNr,
          'SurveyNr'=>$svnr[0],
          'Certification'=>$certification,
          'ICSDate'=>$ICSDate
          );
          $q = $this->db->where($wer);
          //$this->db->delete('ktv_certification_audit_log');
          $this->db->delete('ktv_certification_signature');
          if($this->db->affected_rows()>0)
          {
          $d = array('success'=>true);
          } else {
          $d = array('success'=>false);
          }
         *
         */
        return $d;
    }

    public function readFinanceCetak($FarmerID, $SurveyID) {
        $dt = array();
        for ($i = 0; $i <= $SurveyID; $i++) {
//            SELECT SurveyNr as id, concat(SurveyNr,' - ',SurveyTxt) as surveya
            //            FROM ktv_survey
            $sql = "select a.*,concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya "
                    . "from ktv_farmer_financial a "
                    . "join ktv_survey b ON a.SurveyNr = b.SurveyNr "
                    . "where a.SurveyNr=$i AND FarmerID=$FarmerID";
            $q = $this->db->query($sql);
//            $q = $this->db->get_where('ktv_farmer_financial',array('SurveyNr'=>$i,'FarmerID'=>$FarmerID));
            $dt[] = $q->result_array();
//            $dt[] = $this->db->last_query();
        }
        return $dt;
    }

    public function readFinanceCetakIsi($FarmerID, $SurveyID) {
        $sql = "select a.*,concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya "
                . "from ktv_farmer_financial a "
                . "join ktv_survey b ON a.SurveyNr = b.SurveyNr "
                . "where a.SurveyNr=$SurveyID AND FarmerID=$FarmerID";
        $q = $this->db->query($sql);
        return array(0 => $q->result_array());
    }

    public function getFinanceLatest($FarmerID) {
        $sql = "SELECT
    f.*,
    b.`BankName`
FROM
    `ktv_farmer_financial` f
    LEFT JOIN ktv_bank b ON f.`AccountBankID` = b.`BankID`
JOIN (
    SELECT
        f.`FarmerID`,
        MAX(f.`SurveyNr`) AS SurveyNr
    FROM `ktv_farmer_financial` f
    WHERE f.`FarmerID` = ?
    GROUP BY f.`FarmerID`
) z ON f.`FarmerID` = z.`FarmerID` AND f.`SurveyNr` = z.`SurveyNr`
WHERE f.`FarmerID` = ?
";
        $query = $this->db->query($sql, array($FarmerID, $FarmerID));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    // family relationship
    public function readFamRelation($id = '') {
        $sql = "SELECT HubunganKeluarga FROM ktv_family WHERE FamilyID={$id}";
        $query = $this->db->query($sql);
        return $query->row()->HubunganKeluarga;
    }

    public function getBatchTrainingsFarmerDetail($CpgBatchTrainingsFarmerID) {
        $sql = "SELECT
  f.`FarmerID`,
  tf.`CpgBatchTrainingID`,
  f.`FarmerName`,
  r.`CpgTrainings`,
  r.`AltName` AS training_name,
  c.`GroupName`,
  c.`CPGid`,
  t.`TrainingStart`,
  t.`TrainingEnd`,
  d.`District`,
  d.`DistrictID`,
  SUBSTR(f.`VillageID`, 1,2) AS ProvinceID
FROM `ktv_cpg_batch_trainings_farmers` tf
JOIN `ktv_cpg_batch_trainings` t ON t.`CpgBatchTrainingID` = tf.`CpgBatchTrainingID`
JOIN `ktv_cpg_trainings` r ON r.`CpgTrainingsID` = t.`CPGtrainingsID`
JOIN `ktv_farmer` f ON f.`FarmerID` = tf.`FarmerID`
JOIN `ktv_cpg` c ON c.`CPGid` = f.`CPGid`
LEFT JOIN ktv_district d ON d.`DistrictID` = SUBSTR(f.`VillageID`, 1,4)
WHERE
  1 = 1
  AND tf.`CpgBatchTrainingsFarmerID` = ?
        ";
        $query = $this->db->query($sql, array($CpgBatchTrainingsFarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getTrainingFarmerList($CpgBatchTrainingID) {
        $this->db->where('CpgBatchTrainingID', $CpgBatchTrainingID);
        $query = $this->db->get('ktv_cpg_batch_trainings_farmers');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function generateCertificateFiles($CpgBatchTrainingID) {
        $farmers = $this->getTrainingFarmerList($CpgBatchTrainingID);
        $list_files = array();
        if (!empty($farmers)) {
            foreach ($farmers as $key => $farmer) {
                $list_files[] = $this->generateCertificateFile($farmer['CpgBatchTrainingsFarmerID']);
            }
        }
        return $list_files;
    }

    public function generateCertificateFile($CpgBatchTrainingsFarmerID) {
        $detail = $this->mfarmer->getBatchTrainingsFarmerDetail($CpgBatchTrainingsFarmerID);
        // echo '<pre>'; print_r($detail); echo '</pre>';exit;
        $dir = "images/certificate/{$detail['CpgBatchTrainingID']}";
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $file_path = "images/certificate/{$detail['CpgBatchTrainingID']}/{$detail['FarmerID']}.jpg";

        // if (file_exists($file_path)) {
        //     return $file_path;
        // }

        $fileHandle = fopen($file_path, "wb");

        $this->load->helper('date');
        if ($debug) {
            echo '<pre>';
            print_r($this->db->last_query());
            echo '</pre>';
            exit;
        }

        $template = 'images/certificate/blank.jpg';
        if (file_exists("images/certificate/blank_{$detail['DistrictID']}.jpg")) {
            $template = "images/certificate/blank_{$detail['DistrictID']}.jpg";
        }
        $font_1 = 'images/certificate/commercialscrd-46771.ttf';
        $font_2 = 'images/certificate/DaxRegular.ttf';

        $size = getimagesize($template);
        $width = $size[0];
        $height = $size[1];

        // font size
        $text_name_multiplier = 0.06;
        $text_group_multiplier = 0.02;
        $text_normal_multiplier = 0.02;
        $text_nomor_multiplier = 0.014;

        $image = new Imagick($template);

        if (!empty($detail)) {
            define("LEFT", 1);
            define("CENTER", 2);
            define("RIGHT", 3);

            $text_name = new ImagickDraw();
            $text_name->setFillColor('#7B4900');
            $text_name->setFont($font_1);
            $text_name->setFontSize($width * $text_name_multiplier);
            $text_name->setTextAlignment(CENTER);

            $text_group = new ImagickDraw();
            $text_group->setFillColor('#7B4900');
            $text_group->setFont($font_2);
            $text_group->setFontSize($width * $text_group_multiplier);
            $text_group->setTextAlignment(CENTER);

            $text_normal = new ImagickDraw();
            $text_normal->setFillColor('#000000');
            $text_normal->setFont($font_2);
            $text_normal->setFontSize($width * $text_normal_multiplier);
            $text_normal->setTextAlignment(CENTER);

            $text_nomor = new ImagickDraw();
            $text_nomor->setFillColor('#000000');
            $text_nomor->setFont($font_2);
            $text_nomor->setFontSize($width * $text_nomor_multiplier);
            $text_nomor->setTextAlignment(RIGHT);

            // /* Create text */
            // center text
            $x = $width / 2;
            $y = $height / 2;

            $text_name->annotation($x, $y, $detail['FarmerName']);
            $y += $width * $text_name_multiplier - $width / 45;
            $text_group->annotation($x, $y, '(Kelompok Tani : ' . $detail['GroupName'] . ' - ' . $detail['CPGid'] . ')');
            $y += $width * $text_group_multiplier;
            $y += $width / 70;
            $text_normal->annotation($x, $y, 'Telah menyelesaikan Sekolah Lapang (Farmer Field School) ' . $detail['training_name'] . '');
            $y += $width * $text_normal_multiplier;
            $text_normal->annotation($x, $y, 'Bagi Petani Kakao Kabupaten ' . $detail['District'] . ' pada tanggal ' . indonesian_date($detail['TrainingStart'], 'd F Y', '') . ' - ' . indonesian_date($detail['TrainingEnd'], 'd F Y', ''));
            $y += $width * $text_normal_multiplier;
            $y += $width * $text_normal_multiplier;
            $text_normal->annotation($x, $y, $detail['District'] . ', ' . indonesian_date($detail['TrainingEnd'], 'd F Y', ''));

            // nomor text
            $x = $width - $width * 0.015;
            $y = $height * 0.184;

            switch ($detail['ProvinceID']) {
                case '11':$kode_provinsi = 'A';
                    break;
                case '12':$kode_provinsi = 'NS';
                    break;
                case '13':$kode_provinsi = 'WS';
                    break;
                case '31':$kode_provinsi = 'J';
                    break;
                case '53':$kode_provinsi = 'ENT';
                    break;
                case '72':$kode_provinsi = 'CS';
                    break;
                case '73':$kode_provinsi = 'SS';
                    break;
                case '74':$kode_provinsi = 'SES';
                    break;
                case '76':$kode_provinsi = 'WS';
                    break;

                default:
                    $kode_provinsi = '0';
                    break;
            }

            $text_nomor->annotation($x, $y, 'No : ' . $detail['FarmerID'] . '/FFS/SCPP-' . $kode_provinsi . '/' . romawi(date('m', strtotime($detail['TrainingEnd']))) . '/' . date('Y', strtotime($detail['TrainingEnd'])));

            $image->drawImage($text_name);
            $image->drawImage($text_group);
            $image->drawImage($text_normal);
            $image->drawImage($text_nomor);
            // $image->setImageFormat ("jpeg");
            // $im->imageWriteFile ($fileHandle);
            file_put_contents($file_path, $image);
            return $file_path;
        }
    }

    public function getLatestSurveyId($farmer_id) {
        $sql = "
SELECT
    DISTINCT MAX(g.`SurveyNr`) AS SurveyNr, GardenNr
FROM `ktv_farmer_garden` g
WHERE
    g.`FarmerID` = ?
GROUP BY g.`GardenNr`
        ";
        $query = $this->db->query($sql, array($farmer_id));
        return $query->result_array();
    }

    public function getFarmerOtherLand($farmer_id) {
        $sql = "SELECT
    `FarmerID`,
    `Commodity`,
    `GardenHa`
    ,CASE Commodity
        WHEN 1 THEN 'Jagung'
        WHEN 2 THEN 'Sawit'
        WHEN 3 THEN 'Karet'
        WHEN 4 THEN 'Cengkeh'
        WHEN 5 THEN 'Padi'
        WHEN 6 THEN 'Kosong'
        WHEN 7 THEN 'Dll'
    END AS Commodity_label
    ,Remark
FROM
    `ktv_farmer_other_land`
WHERE
    FarmerID = ? AND
    StatusCode != 'nullified'
        ";
        $query = $this->db->query($sql, array($farmer_id));
        return $query->result_array();
    }

    public function createOtherLand($FarmerID, $Commodity, $GardenHa, $Remark) {
        $sql = "INSERT INTO `ktv_farmer_other_land` (
    `FarmerID`,
    `Commodity`,
    `GardenHa`,
    Remark,
    DateCreated,
    CreatedBy
)
VALUES
    (
        ?,
        ?,
        ?,
        ?,
        NOW(),
        ?
    )
ON DUPLICATE KEY UPDATE
  Commodity = VALUES(Commodity),
  GardenHa = VALUES(GardenHa),
  Remark = VALUES(Remark),
  DateUpdated = NOW(),
  LastModifiedBy = VALUES(CreatedBy)
        ";
        return $this->db->query($sql, array($FarmerID, $Commodity, $GardenHa,$Remark, $_SESSION['userid']));
    }

    public function deleteOtherLand($FarmerID, $Commodity) {
        /*
          $sql = "DELETE FROM `ktv_farmer_other_land`
          WHERE
          `FarmerID` = ?
          AND Commodity = ?
          "; */
        $sql = "UPDATE ktv_farmer_other_land SET
            StatusCode = 'nullified',
            LastModifiedBy = '" . $_SESSION['userid'] . "',
            DateUpdated = NOW()
         WHERE
            `FarmerID` = ? AND Commodity = ?
         LIMIT 1
        ";
        return $this->db->query($sql, array($FarmerID, $Commodity));
    }

    public function getFarmerGardenStatus($FarmerID) {
        $sql = "
SELECT
    g.`FarmerID`,
    g.`GardenNr`,
    g.`GardenHaUnCertified`,
    IF(f.StatusFarmer = 2,2,IFNULL(gs.ActiveStatus,1)) AS ActiveStatus,
    IF(c.FarmerID IS NOT NULL,1,0) AS CertificationStatus,
    gs.GardenStatus,
    gs.`Commodity`,
    gs.`Remarks`,
    gs.CommodityHa
FROM `ktv_farmer_garden` g
LEFT JOIN ktv_certification c ON c.FarmerID = g.FarmerID AND c.GardenNr = g.GardenNr AND c.ExternalDate > '0000-00-00' AND c.StatusAudit = 1
LEFT JOIN ktv_farmer f ON f.FarmerID = g.FarmerID
LEFT JOIN `ktv_farmer_garden_status` gs ON g.`FarmerID` = gs.`FarmerID` AND g.`GardenNr` = gs.`GardenNr`
WHERE
    g.`FarmerID` = ?
GROUP BY g.`FarmerID`, g.`GardenNr`
        ";
        $query = $this->db->query($sql, array($FarmerID));
        return $query->result_array();
    }

    public function createGardenStatus($FarmerID, $GardenNr, $ActiveStatus, $GardenStatus, $Commodity, $Remarks, $CommodityHa) {
        $sql_check = "SELECT * FROM ktv_farmer_garden_status WHERE FarmerID=? AND GardenNr=?";
        $check = $this->db->query($sql_check,array($FarmerID,$GardenNr));
        if($check->num_rows() > 0){
            $sql = "UPDATE `ktv_farmer_garden_status` SET `ActiveStatus`=?,`GardenStatus`=?,`Commodity`=?,`Remarks`=?,CommodityHa=?,DateUpdated=NOW(),LastModifiedBy=? WHERE FarmerID=? AND GardenNr=?";
            return $this->db->query($sql, array($ActiveStatus, $GardenStatus, $Commodity, $Remarks, $CommodityHa, $_SESSION['userid'], $FarmerID, $GardenNr));
        }else{
            $sql = "INSERT INTO `ktv_farmer_garden_status` (`FarmerID`,`GardenNr`,`ActiveStatus`,`GardenStatus`,`Commodity`,`Remarks`,`CommodityHa`,DateCreated,CreatedBy) VALUES(?,?,?,?,?,?,?,NOW(),?)";
            return $this->db->query($sql, array($FarmerID, $GardenNr, $ActiveStatus, $GardenStatus, $Commodity, $Remarks, $CommodityHa, $_SESSION['userid']));
        }
    }

    public function updateGardenStatus($GardenStatus, $Commodity, $Remarks, $GardenStatusID) {
        $sql = "UPDATE `ktv_farmer_garden_status`
SET
    `GardenStatus` = ?,
    `Commodity` = ?,
    `Remarks` = ?,
    `DateUpdated` = NOW(),
    `LastModifiedBy` = ?
WHERE
    `GardenStatusID` = ?
        ";
        return $this->db->query($sql, array($GardenStatus, $Commodity, $Remarks, $_SESSION['userid'], $GardenStatusID));
    }

    public function getGardenSize($FarmerID) {
        $sql = "SELECT
    SUM(g.`GardenHaUnCertified`) AS GardenHaUnCertified,
     (CASE WHEN SUM(g.GardenHaUncertified)<1 THEN 'Small'
    WHEN (SUM(g.GardenHaUncertified)>=1 AND SUM(g.GardenHaUncertified)<2) THEN 'Medium'
    WHEN SUM(g.GardenHaUncertified)>=2 THEN 'Large' END) AS 'LandSize',
    SUM(IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    ((IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS Production,
    SUM(PohonTM) AS PohonTM
FROM `ktv_farmer_garden` g
JOIN (
SELECT
    g.`FarmerID`,
    g.`GardenNr`,
    MAX(g.`SurveyNr`) AS SurveyNr
FROM `ktv_farmer_garden` g
GROUP BY
    g.`FarmerID`,
    g.`GardenNr`
) z ON g.`FarmerID` = z.FarmerID AND g.`GardenNr` = z.GardenNr AND g.`SurveyNr` = z.SurveyNr
WHERE g.`FarmerID` = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getPPIScore2012($FarmerID) {
        $sql = "SELECT
*
FROM `ktv_ppiscore2012` p
JOIN (
SELECT
    p.`FarmerID`,
    MAX(p.`SurveyNr`) AS SurveyNr
FROM `ktv_ppiscore2012` p
GROUP BY p.`FarmerID`
) z ON p.`FarmerID` = z.FarmerID AND p.`SurveyNr` = z.SurveyNr
WHERE
    p.`FarmerID` = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getTrainingFarmer($FarmerID) {
        $sql = "SELECT
    DISTINCT t.`CpgTrainingsID`,
    t.`CpgTrainings`,
    t.`AltName`
FROM `ktv_cpg_batch_trainings_farmers` f
JOIN `ktv_cpg_batch_trainings` bt ON bt.`CpgBatchTrainingID` = f.`CpgBatchTrainingID`
JOIN `ktv_cpg_trainings` t ON t.`CpgTrainingsID` = bt.`CPGtrainingsID`
WHERE
    f.`FarmerID` = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readFarmersLearningContract() {
        $sql = "SELECT FarmerID, LearningContractStatus, LearningContractSign, LearningContractFile FROM ktv_farmer
                WHERE LearningContractStatus='1' AND LearningContractSign!='' AND (LearningContractFile='' OR LearningContractFile IS NULL)";
        $query = $this->db->query($sql);
        return $query;
    }

    public function updateLearningContract($FarmerID, $LearningContractFile) {
        $sql = "UPDATE ktv_farmer SET LearningContractFile=? WHERE FarmerID=?";
        $query = $this->db->query($sql, array($LearningContractFile, $FarmerID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function getNearestBank($lat, $lng) {
        $sql = "SELECT
    *
FROM
(
SELECT
    b.`BranchName` AS label,
    ( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( b.`BranchLatitude`) )
    * COS( RADIANS(b.`BranchLongitude`) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(b.`BranchLatitude`)))) AS distance
FROM `ktv_bank_branch` b

UNION

SELECT
    a.`CoopName` AS label
    ,( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( a.`Latitude`) )
    * COS( RADIANS(a.`Longitude`) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(a.`Latitude`)))) AS distance
FROM
    ktv_cooperatives a
WHERE
    a.`Status` IN ('Koperasi','Gapoktan')
    AND a.StatusCode = 'active'
) AS tbl_union
ORDER BY distance ASC
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $lat, $lng, $lat));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    /**
     * Kalau mau mengaktifkan fungsi ini uncomment "use" di line paling atas
     * @param  [type] $FarmerID  [description]
     * @param  [type] $PartnerID [description]
     * @return [type]            [description]
     */
    public function pdf_farmer_summary_loan($FarmerID, $PartnerID = null) {
        $path = 'pdf/' . $FarmerID . '.pdf';
        if (!file_exists($path)) {
            $options = array(
                'javascript-delay' => 500,
            );
            // You can pass a filename, a HTML string or an URL to the constructor
            // $pdf = new Pdf('http://cocoatrace.dev/api/index.php/farmer/cetak_farmer_summary_loan/FarmerID/732201362/PartnerID/4');
            $url = base_url() . 'farmer/cetak_farmer_summary_loan/FarmerID/' . $FarmerID;

            $pdf = new Pdf($url);

            // $pdf->send($FarmerID.'.pdf');

            $pdf->saveAs($path);
            // if (!$pdf->saveAs($path.$FarmerID.'.pdf')) {
            //     echo $pdf->getError();
            // }
        }
        return $path;
    }

    public function rotateImage($FarmerID, $degree) {
        $sql = "SELECT Photo FROM ktv_farmer WHERE FarmerID=?";
        $farmer = $this->db->query($sql, array($FarmerID));
        $Photo = @$farmer->row()->Photo;
        $expPhoto = explode('/', $Photo);
        $countexpPhoto = count($expPhoto);
        $filename = $expPhoto[$countexpPhoto - 1]; //die();
        $path = str_replace($filename, '', $Photo);
        //$filename_new = date('YmdHis').'_'.$FarmerID.'.jpg';
        $filename_new = $filename;
        $proses = $this->processRotateImage($filename, $path, $degree, $filename_new);
        if ($proses == 1) {
            //$sql_rotate_photo = "UPDATE ktv_farmer SET Photo=? WHERE FarmerID=?";
            //$query = $this->db->query($sql_rotate_photo, array($path.$filename_new, $FarmerID));
            $query = true;
            if ($query) {
                $data['status'] = true;
                $data['Photo'] = $path . $filename_new . '?random=' . date('YmdHis');
            } else {
                $data['status'] = 10;
            }
        } else {
            $data['status'] = 20;
            $data['Photo'] = $Photo . '?random=' . date('YmdHis');
        }

        return $data;
    }

    public function processRotateImage($filename = '', $path = '', $degree = 0, $savename = false) {
        $this->load->library('image_lib');
        $this->image_lib->clear();
        $config['image_library'] = 'gd2';
        $config['new_image'] = 'images/Photo/' . $path . $savename;
        $config['maintain_ratio'] = true;
        $config['create_thumb'] = false;
        $config['source_image'] = 'images/Photo/' . $path . $filename;
        $config['rotation_angle'] = "$degree";
        $this->image_lib->initialize($config);
        if (!$this->image_lib->rotate()) {
            //echo './images/Photo/'.$path.$savename;
            //echo 'images/Photo/'.$path.$filename;
            //echo '<Br>';
            //echo $this->image_lib->display_errors(); die();
            return 0;
        } else {
            return 1;
        }
    }

    public function readProvinces() {
        $this->db->select('ProvinceID AS id, Province AS name', false);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('ktv_province');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readDitricts($ProvinceID = null) {
        $this->db->select('DistrictID AS id, District AS name', false);
        if (!empty($ProvinceID)) {
            $this->db->where('ProvinceID', $ProvinceID, false);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('ktv_district');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readAccessStaffs($ProvinceID = null) {
        $this->db->select("d.DistrictID AS id, CONCAT(Province,' / ',District) AS name", false);
        $this->db->from('ktv_district d');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID', 'INNER');
        $this->db->where('p.active', 1, false);
        $this->db->where('d.active', 1, false);
        //$this->db->where('d.active', 0, false);
        if (!empty($ProvinceID)) {
            $this->db->where('d.ProvinceID', $ProvinceID, false);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readWorkareas($ProvinceID = null) {
        $this->db->select('WorkAreaID AS id, WorkAreaName AS name', false);
        if (!empty($ProvinceID)) {
            $this->db->where('ProvinceID', $ProvinceID, false);
        }
        $this->db->where('StatusCode', 'active');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('ktv_ref_work_area');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readSubDistricts($DistrictID = null) {
        $this->db->select('SubDistrictID AS id, SubDistrict AS name', false);
        if (!empty($DistrictID)) {
            $this->db->where('DistrictID', $DistrictID, false);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('ktv_subdistrict');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readVillages($SubDistrictID = null) {
        $this->db->select('VillageID AS id, Village AS name', false);
        if (!empty($SubDistrictID)) {
            $this->db->where('SubDistrictID', $SubDistrictID, false);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('ktv_village');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function readCPG($province = null, $district = null, $subdistrict = null) {
        $this->db->select('c.CPGid AS id, c.GroupName AS name');
        $this->db->from('ktv_cpg c');
        $this->db->join('ktv_village v', 'v.VillageID = c.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        if (!empty($province)) {
            $this->db->where('p.ProvinceID', $province, false);
        }
        if (!empty($district)) {
            $this->db->where('d.DistrictID', $district, false);
        }
        if (!empty($subdistrict)) {
            $this->db->where('sd.SubDistrictID', $subdistrict, false);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function listFarmer($province = null, $district = null, $subdistrict = null) {
        $sql = "SELECT c.FarmerID AS id, CONCAT('[',c.FarmerID,'] ',c.FarmerName) AS name
FROM (`ktv_farmer` c)
JOIN `ktv_village` v ON `v`.`VillageID` = `c`.`VillageID`
JOIN `ktv_subdistrict` sd ON `sd`.`SubDistrictID` = `v`.`SubDistrictID`
JOIN `ktv_district` d ON `d`.`DistrictID` = `sd`.`DistrictID`
JOIN `ktv_province` p ON `p`.`ProvinceID` = `d`.`ProvinceID`
WHERE 1 = 1
    --filter--
ORDER BY `name` asc
        ";
        $filter = '';
        $params = array();
        if (!empty($province)) {
            $filter .= ' AND p.ProvinceID = ?';
            $params[] = $province;
        }
        if (!empty($district)) {
            // $this->db->where('d.DistrictID', $district, FALSE);
            $filter .= ' AND d.DistrictID = ?';
            $params[] = $district;
        }
        if (!empty($subdistrict)) {
            // $this->db->where('sd.SubDistrictID', $subdistrict, FALSE);
            $filter .= ' AND sd.SubDistrictID = ?';
            $params[] = $subdistrict;
        }
        $sql = str_replace('--filter--', $filter, $sql);
        // $this->db->order_by('name', 'asc');
        // $query = $this->db->get();
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getFarmerCertification($FarmerID) {
        $sql = "SELECT
    f.FarmerID
    , c.GardenNr
    , c.SurveyNr
    , f.FarmerName
    , f.Address
    , cpg.GroupName
    , d.District
    , CASE c.Certification
        WHEN 1 THEN 'UTZ'
        WHEN 2 THEN 'Rainforest'
        WHEN 3 THEN 'Fairtrade'
        WHEN 4 THEN 'Organik'
    END AS Certification
    , CASE c.CertificationHolderJenis
       WHEN 'Pedagang' THEN trad.TraderName
       WHEN 'Organisasi Petani' THEN coop.CoopName
       WHEN 'Gudang' THEN wh.WarehouseName
    END AS CertificationHolder
FROM ktv_farmer f
LEFT JOIN ktv_certification c ON f.FarmerID = c.FarmerID
LEFT JOIN (SELECT c.FarmerID, MAX(c.SurveyNr) AS SurveyNr FROM ktv_certification c WHERE c.ICSDate GROUP BY c.FarmerID) z ON c.FarmerID = z.FarmerID
LEFT JOIN ktv_cpg cpg ON cpg.CPGid = f.CPGid
LEFT JOIN ktv_district d ON SUBSTR(f.FarmerID,1,4) = d.DistrictID
lEFT JOIN ktv_traders trad ON trad.TraderID = c.CertificationHolder
lEFT JOIN ktv_cooperatives coop ON coop.CoopID = c.CertificationHolder
lEFT JOIN ktv_warehouse wh ON wh.WarehouseID = c.CertificationHolder
WHERE
    1 = 1
    AND f.FarmerID = ?
GROUP BY f.FarmerID
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getIMSManager($FarmerID, $GardenNr, $SurveyNr) {
        $sql = "SELECT
    a.ICSDate,
    z.name,
    z.Address AS address,
    z.Province
FROM ktv_certification_audit_log a
LEFT JOIN (

                    SELECT
            v.IcsMemberID AS icsid,
            kc.CoopName,
            a.StaffName AS `name`,
            '' AS Address,
            b.Province
                    FROM
            ktv_private_staff a
            JOIN ktv_ics_members v ON a.PrivateStaffID = v.FarmerID
            LEFT JOIN ktv_ics ki ON v.IcsID = ki.IcsID
            LEFT JOIN ktv_cooperatives kc ON ki.ObjID = kc.CoopID
            LEFT JOIN ktv_province b ON SUBSTR(a.Location,1,2) = b.ProvinceID
                    UNION
                    SELECT
            w.IcsMemberID AS icsid,
            kc.CoopName,
            e.PersonNm AS `name`,
            e.Address,
            f.Province
                    FROM
            ktv_program_staff d
            JOIN ktv_persons e ON d.PersonID = e.PersonID
            JOIN ktv_ics_members w ON d.PersonID = w.FarmerID
            LEFT JOIN ktv_ics ki ON w.IcsID = ki.IcsID
            LEFT JOIN ktv_cooperatives kc ON ki.ObjID = kc.CoopID
            LEFT JOIN ktv_province f ON SUBSTR(d.WorkArea,1,2) = f.ProvinceID
                    UNION
                    SELECT
            x.IcsMemberID AS icsid,
            kc.CoopName,
            h.FarmerName AS `name`,
            h.Address,
            i.Province
                    FROM
            ktv_farmer h
            JOIN ktv_ics_members `x` ON h.FarmerID = x.FarmerID
            LEFT JOIN ktv_ics ki ON x.IcsID = ki.IcsID
            LEFT JOIN ktv_cooperatives kc ON ki.ObjID = kc.CoopID
            LEFT JOIN ktv_province i ON SUBSTR(h.FarmerID,1,2) = i.ProvinceID
) z ON z.icsid = a.IMSManagerID
WHERE
    a.FarmerID = ?
    AND a.GardenNr = ?
    AND a.SurveyNr = ?
        ";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr, $SurveyNr));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getFarmerBank($FarmerID) {
        $sql = "SELECT
    IF(f.BankName, f.BankName, IF(z.FarmerID,z.AccountBankName, '')) AS BankName,
    IF(f.BankName, f.AccountNumber, IF(z.FarmerID,z.AccountNumber, '')) AS AccountNumber,
    IF(f.BankName, f.AccountBeneficiary, IF(z.FarmerID,f.FarmerName, '')) AS AccountBeneficiary
FROM ktv_farmer f
LEFT JOIN (
SELECT
    ff.FarmerID,
    ff.AccountBankName,
    ff.AccountNumber
FROM ktv_farmer_financial ff
JOIN (SELECT ff.FarmerID, MAX(ff.SurveyNr) AS SurveyNr FROM ktv_farmer_financial ff GROUP BY ff.FarmerID) z ON z.FarmerID = ff.FarmerID AND z.SurveyNr = ff.SurveyNr
) z ON z.FarmerID = f.FarmerID
WHERE
f.FarmerID = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getCoopName($id) {
        $query = $this->db->get_where('ktv_cooperatives', array('CoopID' => $id), 1);
        if ($query->num_rows() > 0) {
            $coop = $query->row_array(0);
            return $coop['CoopName'];
        }
        return '';
    }

    public function readCertificateSignature($FarmerID, $GardenNr, $SurveyNr, $ICSDate, $Certification) {
        $sql = "SELECT FarmerSignature,InspectorSignature,AuditCommiteeSignature,IMSManagerSignature FROM ktv_certification_signature WHERE FarmerID=? AND GardenNr=? AND SurveyNr=? AND ICSDate=? AND Certification=?";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr, $SurveyNr, $ICSDate, $Certification));
        return $query->result_array(0);
    }

    public function readSavingPilotInfo($key) {
        $sql = "SELECT
                    SUM(b.GardenHaUnCertified) AS LuasLahan, COUNT(DISTINCT(FamilyID)) AS Tanggungan
                FROM
                    (SELECT
                        MAX(SurveyNr) AS SurveyNr, GardenNr, FarmerID
                    FROM
                        ktv_farmer_garden
                    WHERE
                        FarmerID=110400006
                    GROUP BY FarmerID, GardenNr) a
                    LEFT JOIN ktv_farmer_garden b ON a.SurveyNr=b.SurveyNr AND a.GardenNr=b.GardenNr AND a.FarmerID=b.FarmerID
                    LEFT JOIN ktv_family c ON a.FarmerID=c.FarmerID
                GROUP BY
                    a.FarmerID";
        $query = $this->db->query($sql, array($key));
        $result = $query->result_array();
        return $result[0];
    }

    public function readSavingPilotCetak($FarmerID, $SurveyID) {
        $dt = array();
        for ($i = 0; $i <= $SurveyID; $i++) {
//            SELECT SurveyNr as id, concat(SurveyNr,' - ',SurveyTxt) as surveya
            //            FROM ktv_survey
            $sql = "select a.*,concat(b.SurveyNr,' - ',b.SurveyTxt) as surveya "
                    . "from ktv_saving_pilot a "
                    . "join ktv_survey b ON a.SurveyNr = b.SurveyNr "
                    . "where a.SurveyNr=$i AND FarmerID=$FarmerID";
            $q = $this->db->query($sql);
//            $q = $this->db->get_where('ktv_farmer_financial',array('SurveyNr'=>$i,'FarmerID'=>$FarmerID));
            $dt[] = $q->result_array();
//            $dt[] = $this->db->last_query();
        }
        return $dt;
    }

    public function getFarmerFamily($FarmerID) {
        $query = $this->db->get_where('ktv_family', compact('FarmerID'));
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
    }

    public function getFarmerGardens($FarmerID) {
        $sql = "SELECT
    g.FarmerID,
    g.GardenNr,
    g.SurveyNr,
    g.Latitude,
    g.Longitude,
    g.OwnershipCocoa,
    g.LandCertificate,
    g.GardenLandUse,
    (
        SELECT
            CASE
                WHEN ccert.Certification = '1' THEN 'UTZ'
                WHEN ccert.Certification = '2' THEN 'Rainforest'
                WHEN ccert.Certification = '3' THEN 'Fairtrade'
                WHEN ccert.Certification = '4' THEN 'Organic'
                ELSE 'None'
            END AS Cert
        FROM
            ktv_certification ccert
        WHERE
            ccert.FarmerID = g.FarmerID
            AND ccert.GardenNr = g.`GardenNr`
            AND CURRENT_DATE() BETWEEN ccert.CertificationStart AND ccert.CertificationEnd
        ORDER BY ccert.CertificationEnd DESC
        LIMIT 1
    ) AS Cert
FROM
    ktv_farmer_garden g
    JOIN
        (SELECT g.FarmerID, g.GardenNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
WHERE
    g.FarmerID = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGardenBaseline($FarmerID) {
        $sql = "SELECT
            SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
            (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
            (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS Production,
            SUM(g.GardenHaUncertified) AS Hectare,
            #SUM(PohonTBM+PohonTM+PohonRehab) AS Tree,
            SUM(PohonTM) AS Tree,
            SUM(ShadeTreesNr) AS Shade_Tree,
            SUM(
                (
                  IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
                ) + (
                  IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
                ) + (
                  IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
                )
            )/SUM(PohonTM) AS Yield_Tree,
            YEAR(DateCollection) AS tahunSurvey
    FROM
      ktv_farmer_garden g
    WHERE
        g.SurveyNr = 0
        AND g.FarmerID = ?
        AND g.StatusCode = 'active'
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            $dataReturn = $query->row_array(0);

            //get jumlah garden
            $sql = "SELECT
                        COUNT(GardenNr) AS jumlahKebun
                    FROM
                    (
                    SELECT
                        a.`GardenNr`
                    FROM
                        `ktv_farmer_garden` a
                    WHERE
                        a.FarmerID = ? AND
                        a.`StatusCode` = 'active' AND
                        a.SurveyNr = 0
                    GROUP BY a.GardenNr
                    ) AS tbl_grouped";
            $query = $this->db->query($sql, array($FarmerID));
            $data = $query->row_array();

            $dataReturn['jumlahKebun'] = $data['jumlahKebun'];
            return $dataReturn;
        }
    }

    public function getGardenPostline($FarmerID) {
        $sql = "SELECT
    SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS Production,
    SUM(g.GardenHaUncertified) AS Hectare,
    #SUM(PohonTBM+PohonTM+PohonRehab) AS Tree,
    SUM(PohonTM) AS Tree,
    SUM(ShadeTreesNr) AS Shade_Tree,
    SUM(
                (
                  IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
                ) + (
                  IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
                ) + (
                  IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
                )
            )/SUM(PohonTM) AS Yield_Tree,
    OwnershipCocoa,
    LandCertificate,
    tahunSurvey
    FROM ktv_farmer_garden g
    JOIN (SELECT g.FarmerID, g.GardenNr, MAX(g.SurveyNr) AS SurveyNr, YEAR(MAX(g.DateCollection)) AS tahunSurvey FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
    WHERE
        g.SurveyNr != 0
        AND g.`StatusCode` = 'active'
        AND g.FarmerID = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            $dataReturn = $query->row_array(0);

            //get jumlah garden
            $sql = "SELECT
                        COUNT(GardenNr) AS jumlahKebun
                    FROM
                    (
                    SELECT
                        g.`GardenNr`
                    FROM
                        `ktv_farmer_garden` g
                        JOIN (SELECT g.FarmerID, g.GardenNr, MAX(g.SurveyNr) AS SurveyNr, YEAR(MAX(g.DateCollection)) AS tahunSurvey FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
                    WHERE
                        g.FarmerID = ? AND
                        g.`StatusCode` = 'active' AND
                        g.SurveyNr != 0
                    GROUP BY g.GardenNr
                    ) AS tbl_grouped";
            $query = $this->db->query($sql, array($FarmerID));
            $data = $query->row_array();
            $dataReturn['jumlahKebun'] = $data['jumlahKebun'];

            return $dataReturn;
        }
    }

    public function getFarmerTrainings($FarmerID) {
        $sql = "SELECT
    b.BatchNumber,
    t.CpgTrainings,
    t.`CpgAbbre`,
    GROUP_CONCAT(st.CpgTrainings) AS sub_topic,
    t.AltName,
    bt.TrainingStart,
    bt.TrainingEnd,
    bt.TrainingDays,
    'FFS' AS `type`
FROM ktv_cpg_batch_trainings_farmers tf
JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID = tf.CpgBatchTrainingID
JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = bt.CPGtrainingsID
JOIN ktv_cpg_batch b ON b.CpgBatchID = bt.CpgBatchID
LEFT JOIN (
SELECT
    st.CpgBatchTrainingID,
    t.CpgTrainingsID,
    t.CpgTrainings
FROM ktv_cpg_batch_trainings_sub_topics st
JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = st.SubCpgTrainingsID
) st ON st.CpgBatchTrainingID = bt.CpgBatchTrainingID AND st.CpgTrainingsID != t.CPGtrainingsID
WHERE
    tf.FarmerID = ?
    AND bt.`StatusCode` = 'active'
GROUP BY tf.CpgBatchTrainingsFarmerID
UNION ALL
SELECT
    b.BatchNumber,
    t.CpgTrainings,
    t.`CpgAbbre`,
    GROUP_CONCAT(st.CpgTrainings) AS sub_topic,
    t.AltName,
    kt.TrainingStart,
    kt.TrainingEnd,
    kt.TrainingDays,
    'ToT' AS `type`
FROM ktv_kader_trainings_participants tf
JOIN ktv_kader_trainings kt ON kt.CpgKaderTrainingID = tf.CpgKaderTrainingID
JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = kt.CPGtrainingsID
JOIN ktv_cpg_batch b ON b.CpgBatchID = kt.CpgBatchID
LEFT JOIN (
SELECT
    st.CpgKaderTrainingID,
    t.CpgTrainingsID,
    t.CpgTrainings
FROM ktv_kader_trainings_sub_topics st
JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = st.SubCpgTrainingsID
) st ON st.CpgKaderTrainingID = kt.CpgKaderTrainingID AND st.CpgTrainingsID != t.CPGtrainingsID
WHERE
    tf.FarmerID = ?
    AND kt.StatusCode = 'active'
GROUP BY tf.CpgKaderTrainingID
        ";
        $query = $this->db->query($sql, array(intval($FarmerID), intval($FarmerID)));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getFarmerCooperative($FarmerID) {
        $sql = "SELECT
    kc.CoopName
FROM ktv_cooperatives kc
JOIN coop_member_type mt ON mt.coopID = kc.CoopID
JOIN coop_member m ON m.typeID = mt.typeID
WHERE
    m.farmerID = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function updateFarmerIsTraining($FarmerID) {
        $result = $this->db->update('ktv_farmer', array('isTrained' => 1), array('FarmerID' => $FarmerID));
        return $result;
    }

    public function readFarmerCLonalGardens($ObjType, $ObjID) {
        $sql = "SELECT ClonalID, GardenNr,StatusCode,Area FROM ktv_clonal_garden WHERE ObjType=? AND ObjID=? AND StatusCode!='nullified' ORDER BY GardenNr";
        $query = $this->db->query($sql, array($ObjType, $ObjID));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFarmerGardenClonal($FarmerID, $GardenNr) {
        $sql = "SELECT *, Hybrid AS Local, LokalNr AS LocalNr, J45Nr AS CG45Nr, J45 AS CG45, CloneLain AS OtherClones, CloneLainNr AS OtherClonesNr, ICRRI3 AS ICCRI3,  ICRRI4 AS ICCRI4, ICRRI5 AS ICCRI5, ICRRI3Nr AS ICCRI3Nr,  ICRRI4Nr AS ICCRI4Nr,  ICRRI5Nr AS ICCRI5Nr,
        Kelapa AS Coconut, KelapaNr AS CoconutNr, Gamal AS Gliricidia, GamalNr AS GliricidiaNr, Pinang AS ArecaPalm, PinangNr AS ArecaPalmNr, Karet AS Rubber, KaretNr AS RubberNr, JackFruit AS Jackfruit, JackFruitNr AS JackfruitNr,
        Lamtoro AS Leucaena, LamtoroNr AS LeucaenaNr, Mahoni AS Mahagony, MahoniNr AS MahagonyNr, Pisang AS Banana, PisangNr AS BananaNr, Sukun AS Breadfruit, SukunNr AS BreadfruitNr, Jengkol AS Archidendron, JengkolNr AS ArchidendronNr,
        Sengon AS Albizia, SengonNr AS AlbiziaNr, Petai AS Parkia, PetaiNr AS ParkiaNr, Jabon  AS Anthocephalus, JabonNr AS AnthocephalusNr, Uru AS Ermerilla, UruNr AS ErmerillaNr, Biti AS Vitex, BitiNr AS VitexNr,
        Jati AS Teak, JatiNr AS TeakNr, Jeruk AS Citrus, JerukNr AS CitrusNr, Jambu AS Guava, JambuNr AS GuavaNr, Kedondong AS SpondiasDulcis, KedondongNr AS SpondiasDulcisNr, Manggis AS Mangosteen, ManggisNr AS MangosteenNr,
        Pepaya AS Papaya, PepayaNr AS PapayaNr, Alpukat AS Avocado, AlpukatNr AS AvocadoNr, Kemiri AS Hazelnut, KemiriNr AS HazelnutNr, JambuMente AS Cashew, JambuMenteNr AS CashewNr, Pala AS Nutmeg, PalaNr AS NutmegNr,
        Aren AS SugarPalm, ArenNr AS SugarPalmNr, Sawit AS OilPalm, SawitNr AS OilPalmNr, Cengkeh AS Clove, CengkehNr AS CloveNr, Mangga AS Mango, ManggaNr AS MangoNr FROM ktv_farmer_garden WHERE FarmerID=? AND GardenNr=? ORDER BY SurveyNr DESC LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr));
        $result = $query->result_array();
        return $result[0];
    }

    function readFarmerLogoCert($FarmerID) {
        $sql = "SELECT CASE Certification WHEN 1 THEN 'utz.jpg' WHEN 2 THEN '20160106105232_logo-ra-transparent.png' ELSE '20160315105236_SCPP 2015.jpg' END AS logo FROm ktv_certification WHERE FarmerID=? AND CertificationStart <= NOW() AND CertificationEnd >= NOW()";
        $query = $this->db->query($sql, array($FarmerID));
        $result = $query->result_array();
        return @$result[0]['logo'];
    }

    public function getFarmerAdoptObsList($FarmerID) {
        $sql = "SELECT
                FarmerID,
                GardenNr,
                SurveyYear,
                DateCollection
            FROM
                ktv_adoption_observations a
            WHERE
                StatusCode != 'nullified' AND
                a.FarmerID = ?
            ORDER BY GardenNr, SurveyYear";
        $query = $this->db->query($sql, array($FarmerID));
        return $query->result_array();
    }

    public function getFarmerAdoptObsComboGarden($FarmerID) {
        $sql = "SELECT
                    GardenNr AS id,
                    GardenNr AS label
                FROM
                    ktv_farmer_garden
                WHERE
                    FarmerID = ? AND
                    StatusCode != 'nullified'
                ORDER BY GardenNr ASC";
        $query = $this->db->query($sql, array($FarmerID));
        return $query->result_array();
    }

    public function adoptObsCekExist($post) {
        $sql = "SELECT
                COUNT(*) AS BANYAK
            FROM
                ktv_adoption_observations a
            WHERE
                a.`FarmerID` = ? AND
                a.`GardenNr` = ? AND
                a.`SurveyYear` = ?";
        $query = $this->db->query($sql, array($post['adoptObsFarmerID'], $post['adoptObsGardenNr'], $post['adoptObsSurveyYear']));
        $data = $query->row_array();

        if ($data['BANYAK'] > 0) {
            $results['success'] = false;
            $results['message'] = "Data already exist";
        } else {
            $results['success'] = true;
        }

        return $results;
    }

    public function getFormAdoptObs($FarmerID, $GardenNr, $SurveyYear) {
        $sql = "SELECT
               a.`FarmerID` AS adoptObsFarmerID,
               b.`FarmerName` AS adoptObsFarmerName,
               a.`GardenNr` AS adoptObsGardenNr,
               a.`SurveyYear` AS adoptObsSurveyYear,
               a.`DateCollection` AS adoptObsDateCollection,
               a.`PlantingMaterial`,
               a.`FarmCondTreeDensity`,
               a.`FarmCondTreeAge`,
               a.`FarmCondTreeHealth`,
               a.`DebilitatingDisease`,
               a.`Pruning`,
               a.`PestDiseaseSanitation`,
               a.`Weeding`,
               a.`Harvesting`,
               a.`ShadeManagement`,
               a.`SoilCondition`,
               a.`OrganicMatter`,
               a.`FertilizerFormulation`,
               a.`FertilizerApplication`
            FROM
                `ktv_adoption_observations` a
                INNER JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
            WHERE
                a.`FarmerID` = ? AND
                a.`GardenNr` = ? AND
                a.`SurveyYear` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr, $SurveyYear));
        $dataReturn = $query->row_array();

        $return['success'] = true;
        $return['data'] = $dataReturn;
        return $return;
    }

    public function insertAdoptObs($post) {
        //yg tidak diperlukan untuk insert
        unset($post['methodPost']);
        unset($post['adoptObsFarmerName']);

        foreach ($post as $k => $v) {
            $k = str_replace("adoptObs", "", $k);
            $insert[$k] = $v;
        }

        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];
        $query = $this->db->insert('ktv_adoption_observations', $insert);

        if ($query == true) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateAdoptObs($post) {
        $this->db->trans_start();

        $FarmerID = $post['adoptObsFarmerID'];
        $GardenNr = $post['adoptObsGardenNr'];
        $SurveyYear = $post['adoptObsSurveyYear'];

        //yg tidak diperlukan
        unset($post['methodPost']);
        unset($post['adoptObsFarmerName']);
        unset($post['adoptObsFarmerID']);
        unset($post['adoptObsGardenNr']);
        unset($post['adoptObsSurveyYear']);

        //reset semuanya dl
        $sql = "UPDATE `ktv_adoption_observations` SET
               `DateCollection` = NULL,
               `PlantingMaterial` = NULL,
               `FarmCondTreeDensity` = NULL,
               `FarmCondTreeAge` = NULL,
               `FarmCondTreeHealth` = NULL,
               `DebilitatingDisease` = NULL,
               `Pruning` = NULL,
               `PestDiseaseSanitation` = NULL,
               `Weeding` = NULL,
               `Harvesting` = NULL,
               `ShadeManagement` = NULL,
               `SoilCondition` = NULL,
               `OrganicMatter` = NULL,
               `FertilizerFormulation` = NULL,
               `FertilizerApplication` = NULL
            WHERE
                `FarmerID` = ?
                 AND `GardenNr` = ?
                 AND `SurveyYear` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr, $SurveyYear));

        foreach ($post as $k => $v) {
            $k = str_replace("adoptObs", "", $k);
            $update[$k] = $v;
        }
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        $this->db->where('FarmerID', $FarmerID);
        $this->db->where('GardenNr', $GardenNr);
        $this->db->where('SurveyYear', $SurveyYear);
        $query = $this->db->update('ktv_adoption_observations', $update);

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

    public function deleteAdoptObs($FarmerID, $GardenNr, $SurveyYear) {
        $this->db->trans_start();

        $sql="INSERT INTO `his_ktv_adoption_observations` (
              `DateHistory`,
              `DeleteBy`,
              `FarmerID`,
              `GardenNr`,
              `SurveyYear`,
              `DateCollection`,
              `PlantingMaterial`,
              `FarmCondTreeDensity`,
              `FarmCondTreeAge`,
              `FarmCondTreeHealth`,
              `DebilitatingDisease`,
              `Pruning`,
              `PestDiseaseSanitation`,
              `Weeding`,
              `Harvesting`,
              `ShadeManagement`,
              `SoilCondition`,
              `OrganicMatter`,
              `FertilizerFormulation`,
              `FertilizerApplication`,
              `StatusCode`,
              `DateSync`,
              `DateSynced`,
              `DateCreated`,
              `CreatedBy`,
              `DateUpdated`,
              `LastModifiedBy`,
              `uid`
            )
            SELECT
                NOW(), ?, a.*
            FROM
                ktv_adoption_observations a
            WHERE FarmerID = ? AND GardenNr = ? AND SurveyYear = ? LIMIT 1
        ";
        $proses = $this->db->query($sql, array($_SESSION['userid'],$FarmerID, $GardenNr, $SurveyYear));

        $sql = "DELETE FROM ktv_adoption_observations WHERE FarmerID = ? AND GardenNr = ? AND SurveyYear = ? LIMIT 1";
        $proses = $this->db->query($sql, array($FarmerID, $GardenNr, $SurveyYear));

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

    public function getFarmerEnviSurvey($FarmerID) {
        $sql = "SELECT
                    a.SurveyNr,
                    CONCAT(a.SurveyNr,' - ',b.SurveyTxt) AS SurveyTxt,
                    DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                FROM
                    ktv_environment a
                    LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
                WHERE
                    FarmerID = ?
                ORDER BY SurveyNr ASC";
        $query = $this->db->query($sql, array($FarmerID));
        return $query->result_array();
    }

    public function getFarmerEnviRefSurvey() {
        $sql = "SELECT
                    SurveyNr AS id,
                    CONCAT(SurveyNr,' - ',SurveyTxt) AS label
                FROM
                    ktv_survey
                ORDER BY SurveyNr ASC";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function getFarmerEnviMethod($enviFarmerID, $enviSurverNr) {
        $sql = "SELECT
                    FarmerID
                FROM
                    ktv_environment
                WHERE
                    FarmerID = ? AND
                    SurveyNr = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($enviFarmerID, $enviSurverNr));
        $data = $query->row_array();

        if ($data['FarmerID'] != "") {
            return 'update';
        } else {
            return 'insert';
        }
    }

    public function insertFarmerEnvironment($post) {
        $this->db->trans_start();

        //yg tidak diperlukan untuk insert
        unset($post['enviFarmerName']);

        foreach ($post as $k => $v) {
            $k = str_replace("envi", "", $k);
            $insert[$k] = $v;
        }

        $insert['DateCollection'] = $insert['InterviewDate'];
        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_environment', $insert);

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

    public function updateFarmerEnvironment($post) {
        $this->db->trans_start();
        $FarmerID = $post['enviFarmerID'];
        $SurveyNr = $post['enviSurveyNr'];

        //reset semuanya dulu..
        $sql = "UPDATE `ktv_environment` SET
              `ForestWithinGarden` = NULL,
              `ForestIndigenous` = NULL,
              `ForestIndustrial` = NULL,
              `ForestCommunity` = NULL,
              `ForestNationalParks` = NULL,
              `ForestProtected` = NULL,
              `ForestOthers` = NULL,
              `ForestOthersText` = NULL,
              `ForestBoundaryDistance` = NULL,
              `ForestBoundaryForms` = NULL,
              `ForestBoundaryFormsOthers` = NULL,
              `ForestBoundaryInfoOtherFarmers` = NULL,
              `ForestBoundaryInfoGovernment` = NULL,
              `ForestBoundaryInfoVillageStaff` = NULL,
              `ForestBoundaryInfoOthers` = NULL,
              `ForestBoundaryInfoOthersText` = NULL,
              `WaterMgmtShortageLastYear` = NULL,
              `WaterMgmtShortagePlace` = NULL,
              `WaterMgmtShortagePreventReservoir` = NULL,
              `WaterMgmtShortagePreventNothing` = NULL,
              `WaterMgmtShortagePreventSaving` = NULL,
              `WaterMgmtShortagePreventPlantTrees` = NULL,
              `WaterMgmtShortagePreventReuse` = NULL,
              `SoilMgmtGardenAtMountainSlope` = NULL,
              `SoilMgmtMountainGrade` = NULL,
              `SoilMgmtPreventErosionTerracing` = NULL,
              `SoilMgmtPreventErosionOrganic` = NULL,
              `SoilMgmtPreventErosionCoverCrop` = NULL,
              `SoilMgmtPreventErosionPlantTrees` = NULL,
              `SoilMgmtPreventErosionNothing` = NULL,
              `ClimateChangeImpact` = NULL,
              `DisasterFrequentFlood` = NULL,
              `DisasterProlongedDrought` = NULL,
              `DisasterPestDisease` = NULL,
              `DisasterHarvestSeasonChanged` = NULL,
              `DisasterYieldsDeclined` = NULL,
              `DisasterYieldsHigher` = NULL,
              `DisasterTreesDieback` = NULL,
              `DisasterOthers` = NULL,
              `DisasterOthersText` = NULL,
              `FloodPreventNothing` = NULL,
              `FloodPreventPlantTrees` = NULL,
              `FloodPreventDrainage` = NULL,
              `FloodPreventClearGarbage` = NULL,
              `FloodPreventOthers` = NULL,
              `FloodPreventOthersText` = NULL,
              `ProlongedDroughtPreventNothing` = NULL,
              `ProlongedDroughtPreventPlantTrees` = NULL,
              `ProlongedDroughtPreventHerbicide` = NULL,
              `ProlongedDroughtPreventEmptyGround` = NULL,
              `ProlongedDroughtPreventIrrigation` = NULL,
              `ProlongedDroughtPreventOrganic` = NULL,
              `ProlongedDroughtPreventTrimMore` = NULL,
              `ProlongedDroughtPreventTrimLess` = NULL,
              `ProlongedDroughtPreventOthers` = NULL,
              `ProlongedDroughtPreventOthersText` = NULL,
              `WeatherInfo` = NULL,
              `WeatherInfoTV` = NULL,
              `WeatherInfoWeatherService` = NULL,
              `WeatherInfoOtherStaff` = NULL,
              `WeatherInfoOthers` = NULL,
              `WeatherInfoOthersText` = NULL,
              `DamageActInfo` = NULL,
              `DamageActInfoIllegalLogging` = NULL,
              `DamageActInfoSandQuary` = NULL,
              `DamageActInfoForestFires` = NULL,
              `DamageActInfoWaterPollution` = NULL,
              `DamageActInfoIllegalHunting` = NULL,
              `DamageActInfoOthers` = NULL,
              `DamageActInfoOthersText` = NULL,
              `DamageActInfoDistance` = NULL,
              `PlanMitigateClimateChange` = NULL,
              `PlanMitigateCommunalActPlans` = NULL,
              `PlanMitigateVillageStrategy` = NULL,
              `PlanMitigateGovernmentStrategy` = NULL,
              `PlanMitigateOthers` = NULL,
              `PlanMitigateOthersText` = NULL,
              `ConcernedPlan` = NULL,
              `WasteDisposal` = NULL
            WHERE `FarmerID` = ?
              AND `SurveyNr` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));

        //yg tidak diperlukan untuk insert
        unset($post['enviFarmerName']);
        unset($post['enviFarmerID']);
        unset($post['enviSurveyNr']);

        foreach ($post as $k => $v) {
            $k = str_replace("envi", "", $k);
            $update[$k] = $v;
        }
        $update['DateCollection'] = $update['InterviewDate'];
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        $this->db->where('FarmerID', $FarmerID);
        $this->db->where('SurveyNr', $SurveyNr);
        $query = $this->db->update('ktv_environment', $update);

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

    public function deleteFarmerEnvironment($FarmerID, $SurveyNr) {
        $this->db->trans_start();

        $sql="INSERT INTO `his_ktv_environment` (
              `DateHistory`,
              `DeleteBy`,
              `FarmerID`,
              `SurveyNr`,
              `InterviewDate`,
              `DateCollection`,
              `ForestWithinGarden`,
              `ForestIndigenous`,
              `ForestIndustrial`,
              `ForestCommunity`,
              `ForestNationalParks`,
              `ForestProtected`,
              `ForestOthers`,
              `ForestOthersText`,
              `ForestBoundaryDistance`,
              `ForestBoundaryForms`,
              `ForestBoundaryFormsOthers`,
              `ForestBoundaryInfoOtherFarmers`,
              `ForestBoundaryInfoGovernment`,
              `ForestBoundaryInfoVillageStaff`,
              `ForestBoundaryInfoOthers`,
              `ForestBoundaryInfoOthersText`,
              `WaterMgmtShortageLastYear`,
              `WaterMgmtShortagePlace`,
              `WaterMgmtShortagePreventReservoir`,
              `WaterMgmtShortagePreventNothing`,
              `WaterMgmtShortagePreventSaving`,
              `WaterMgmtShortagePreventPlantTrees`,
              `WaterMgmtShortagePreventReuse`,
              `SoilMgmtGardenAtMountainSlope`,
              `SoilMgmtMountainGrade`,
              `SoilMgmtPreventErosionTerracing`,
              `SoilMgmtPreventErosionOrganic`,
              `SoilMgmtPreventErosionCoverCrop`,
              `SoilMgmtPreventErosionPlantTrees`,
              `SoilMgmtPreventErosionNothing`,
              `ClimateChangeImpact`,
              `DisasterFrequentFlood`,
              `DisasterProlongedDrought`,
              `DisasterPestDisease`,
              `DisasterHarvestSeasonChanged`,
              `DisasterYieldsDeclined`,
              `DisasterYieldsHigher`,
              `DisasterTreesDieback`,
              `DisasterOthers`,
              `DisasterOthersText`,
              `FloodPreventNothing`,
              `FloodPreventPlantTrees`,
              `FloodPreventDrainage`,
              `FloodPreventClearGarbage`,
              `FloodPreventOthers`,
              `FloodPreventOthersText`,
              `ProlongedDroughtPreventNothing`,
              `ProlongedDroughtPreventPlantTrees`,
              `ProlongedDroughtPreventHerbicide`,
              `ProlongedDroughtPreventEmptyGround`,
              `ProlongedDroughtPreventIrrigation`,
              `ProlongedDroughtPreventOrganic`,
              `ProlongedDroughtPreventTrimMore`,
              `ProlongedDroughtPreventTrimLess`,
              `ProlongedDroughtPreventOthers`,
              `ProlongedDroughtPreventOthersText`,
              `WeatherInfo`,
              `WeatherInfoTV`,
              `WeatherInfoWeatherService`,
              `WeatherInfoOtherStaff`,
              `WeatherInfoOthers`,
              `WeatherInfoOthersText`,
              `DamageActInfo`,
              `DamageActInfoIllegalLogging`,
              `DamageActInfoSandQuary`,
              `DamageActInfoForestFires`,
              `DamageActInfoWaterPollution`,
              `DamageActInfoIllegalHunting`,
              `DamageActInfoOthers`,
              `DamageActInfoOthersText`,
              `DamageActInfoDistance`,
              `PlanMitigateClimateChange`,
              `PlanMitigateCommunalActPlans`,
              `PlanMitigateVillageStrategy`,
              `PlanMitigateGovernmentStrategy`,
              `PlanMitigateOthers`,
              `PlanMitigateOthersText`,
              `ConcernedPlan`,
              `ShadeTrees3Yrs`,
              `ShadeTreesIncProductivity`,
              `ShadeTreesAddIncome`,
              `ShadeTreesProtectSoil`,
              `ShadeTreesReducePest`,
              `ShadeTreesReduceTemp`,
              `ShadeTreesIncLandValue`,
              `ShadeTreesSourceFirewood`,
              `ShadeTreesSourceForageAnimals`,
              `ShadeTreesDoNotKnow`,
              `ShadeTreesOthers`,
              `WasteDisposal`,
              `StatusCode`,
              `DateSynced`,
              `DateCreated`,
              `CreatedBy`,
              `DateUpdated`,
              `LastModifiedBy`,
              `uid`
            )
            SELECT
                NOW(), ?, a.*
            FROM
                ktv_environment a
            WHERE
                a.FarmerID = ?
                AND a.SurveyNr = ?
            LIMIT 1
        ";
        $proses = $this->db->query($sql, array($_SESSION['userid'],$FarmerID, $SurveyNr));

        $sql = "DELETE FROM ktv_environment WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
        $proses = $this->db->query($sql, array($FarmerID, $SurveyNr));

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

    public function farmerEnviFormEditGet($FarmerID, $SurveyNr) {
        $sql = "SELECT
                a.`FarmerID`,
                b.`FarmerName`,
                a.`SurveyNr`,
                a.`InterviewDate`,
                a.`DateCollection`,
                a.`ForestWithinGarden`,
                a.`ForestIndigenous`,
                a.`ForestIndustrial`,
                a.`ForestCommunity`,
                a.`ForestNationalParks`,
                a.`ForestProtected`,
                a.`ForestOthers`,
                a.`ForestOthersText`,
                a.`ForestBoundaryDistance`,
                a.`ForestBoundaryForms`,
                a.`ForestBoundaryFormsOthers`,
                a.`ForestBoundaryInfoOtherFarmers`,
                a.`ForestBoundaryInfoGovernment`,
                a.`ForestBoundaryInfoVillageStaff`,
                a.`ForestBoundaryInfoOthers`,
                a.`ForestBoundaryInfoOthersText`,
                a.`WaterMgmtShortageLastYear`,
                a.`WaterMgmtShortagePlace`,
                a.`WaterMgmtShortagePreventReservoir`,
                a.`WaterMgmtShortagePreventNothing`,
                a.`WaterMgmtShortagePreventSaving`,
                a.`WaterMgmtShortagePreventPlantTrees`,
                a.`WaterMgmtShortagePreventReuse`,
                a.`SoilMgmtGardenAtMountainSlope`,
                a.`SoilMgmtMountainGrade`,
                a.`SoilMgmtPreventErosionTerracing`,
                a.`SoilMgmtPreventErosionOrganic`,
                a.`SoilMgmtPreventErosionCoverCrop`,
                a.`SoilMgmtPreventErosionPlantTrees`,
                a.`SoilMgmtPreventErosionNothing`,
                a.`ClimateChangeImpact`,
                a.`DisasterFrequentFlood`,
                a.`DisasterProlongedDrought`,
                a.`DisasterPestDisease`,
                a.`DisasterHarvestSeasonChanged`,
                a.`DisasterYieldsDeclined`,
                a.`DisasterYieldsHigher`,
                a.`DisasterTreesDieback`,
                a.`DisasterOthers`,
                a.`DisasterOthersText`,
                a.`FloodPreventNothing`,
                a.`FloodPreventPlantTrees`,
                a.`FloodPreventDrainage`,
                a.`FloodPreventClearGarbage`,
                a.`FloodPreventOthers`,
                a.`FloodPreventOthersText`,
                a.`ProlongedDroughtPreventNothing`,
                a.`ProlongedDroughtPreventPlantTrees`,
                a.`ProlongedDroughtPreventHerbicide`,
                a.`ProlongedDroughtPreventEmptyGround`,
                a.`ProlongedDroughtPreventIrrigation`,
                a.`ProlongedDroughtPreventOrganic`,
                a.`ProlongedDroughtPreventTrimMore`,
                a.`ProlongedDroughtPreventTrimLess`,
                a.`ProlongedDroughtPreventOthers`,
                a.`ProlongedDroughtPreventOthersText`,
                a.`WeatherInfo`,
                a.`WeatherInfoTV`,
                a.`WeatherInfoWeatherService`,
                a.`WeatherInfoOtherStaff`,
                a.`WeatherInfoOthers`,
                a.`WeatherInfoOthersText`,
                a.`DamageActInfo`,
                a.`DamageActInfoIllegalLogging`,
                a.`DamageActInfoSandQuary`,
                a.`DamageActInfoForestFires`,
                a.`DamageActInfoWaterPollution`,
                a.`DamageActInfoIllegalHunting`,
                a.`DamageActInfoOthers`,
                a.`DamageActInfoOthersText`,
                a.`DamageActInfoDistance`,
                a.`PlanMitigateClimateChange`,
                a.`PlanMitigateCommunalActPlans`,
                a.`PlanMitigateVillageStrategy`,
                a.`PlanMitigateGovernmentStrategy`,
                a.`PlanMitigateOthers`,
                a.`PlanMitigateOthersText`,
                a.`ConcernedPlan`,
                a.`ShadeTrees3Yrs`,
                a.`ShadeTreesIncProductivity`,
                a.`ShadeTreesAddIncome`,
                a.`ShadeTreesProtectSoil`,
                a.`ShadeTreesReducePest`,
                a.`ShadeTreesReduceTemp`,
                a.`ShadeTreesIncLandValue`,
                a.`ShadeTreesSourceFirewood`,
                a.`ShadeTreesSourceForageAnimals`,
                a.`ShadeTreesDoNotKnow`,
                a.`ShadeTreesOthers`,
                a.`WasteDisposal`,
                a.`StatusCode`
            FROM
              `ktv_environment` a
              LEFT JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
            WHERE
                a.`FarmerID` = ? AND
                a.`SurveyNr` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));
        return $query->row_array();
    }

    public function getFarmerSinutriSurvey($FarmerID) {
        $sql = "SELECT
                    a.SurveyNr,
                    CONCAT(a.SurveyNr,' - ',b.SurveyTxt) AS SurveyTxt,
                    DATE_FORMAT(a.`InterviewDate`,'%Y-%m-%d') AS InterviewDate
                FROM
                    ktv_nutrition a
                    LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
                WHERE
                    a.FarmerID = ? AND
                    a.StatusCode = 'active'
                ORDER BY a.SurveyNr ASC";
        $query = $this->db->query($sql, array($FarmerID));
        return $query->result_array();
    }

    public function getFarmerSinutriMethod($sinutriFarmerID, $sinutriSurveyNr) {
        $sql = "SELECT
                    FarmerID
                FROM
                    ktv_nutrition
                WHERE
                    FarmerID = ? AND
                    SurveyNr = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($sinutriFarmerID, $sinutriSurveyNr));
        $data = $query->row_array();

        if ($data['FarmerID'] != "") {
            return 'update';
        } else {
            return 'insert';
        }
    }

    public function insertFarmerSinutri($post) {
        $this->db->trans_start();

        //yg tidak diperlukan untuk insert
        unset($post['sinutriFarmerName']);

        //replace datanya jika ada koma
        $post['sinutriKebunPanjang'] = str_replace(",", "", $post['sinutriKebunPanjang']);
        $post['sinutriKebunLebar'] = str_replace(",", "", $post['sinutriKebunLebar']);
        $post['sinutriKebunArea'] = str_replace(",", "", $post['sinutriKebunArea']);
        $post['sinutriComKebunPanjang'] = str_replace(",", "", $post['sinutriComKebunPanjang']);
        $post['sinutriComKebunLebar'] = str_replace(",", "", $post['sinutriComKebunLebar']);
        $post['sinutriComKebunArea'] = str_replace(",", "", $post['sinutriComKebunArea']);
        $post['sinutriFishPondLength'] = str_replace(",", "", $post['sinutriFishPondLength']);
        $post['sinutriFishPondWidth'] = str_replace(",", "", $post['sinutriFishPondWidth']);
        $post['sinutriFishPondArea'] = str_replace(",", "", $post['sinutriFishPondArea']);

        foreach ($post as $k => $v) {
            $k = str_replace("sinutri", "", $k);
            $insert[$k] = $v;

            //cek yg perlu default value
            if ($insert[$k] == "")
                $insert[$k] = NULL;
        }

        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_nutrition', $insert);

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

    public function updateFarmerSinutri($post) {
        $this->db->trans_start();
        $FarmerID = $post['sinutriFarmerID'];
        $SurveyNr = $post['sinutriSurveyNr'];

        //replace datanya jika ada koma
        $post['sinutriKebunPanjang'] = str_replace(",", "", $post['sinutriKebunPanjang']);
        $post['sinutriKebunLebar'] = str_replace(",", "", $post['sinutriKebunLebar']);
        $post['sinutriKebunArea'] = str_replace(",", "", $post['sinutriKebunArea']);
        $post['sinutriComKebunPanjang'] = str_replace(",", "", $post['sinutriComKebunPanjang']);
        $post['sinutriComKebunLebar'] = str_replace(",", "", $post['sinutriComKebunLebar']);
        $post['sinutriComKebunArea'] = str_replace(",", "", $post['sinutriComKebunArea']);
        $post['sinutriFishPondLength'] = str_replace(",", "", $post['sinutriFishPondLength']);
        $post['sinutriFishPondWidth'] = str_replace(",", "", $post['sinutriFishPondWidth']);
        $post['sinutriFishPondArea'] = str_replace(",", "", $post['sinutriFishPondArea']);

        if ($post['sinutriScore'] == "")
            $post['sinutriScore'] = 0;
        if ($post['sinutriWDDSScore'] == "")
            $post['sinutriWDDSScore'] = 0;

        //reset semuanya dulu..
        $sql = "UPDATE `ktv_nutrition` SET
              `Season` = NULL,
              `HaveVegetableGarden` = NULL,
              `IsFamilyGarden` = NULL,
              `IsCommmercialGarden` = NULL,
              `KebunPanjang` = NULL,
              `KebunLebar` = NULL,
              `KebunArea` = NULL,
              `KbBayam` = NULL,
              `KbCabai` = NULL,
              `KbKacangPanjang` = NULL,
              `KbKangkung` = NULL,
              `KbSawi` = NULL,
              `KbTerong` = NULL,
              `KbTomat` = NULL,
              `KbKelor` = NULL,
              `KbSingkong` = NULL,
              `KbLabu` = NULL,
              `KbKatuk` = NULL,
              `KbKambing` = NULL,
              `KbSapi` = NULL,
              `KbBebek` = NULL,
              `KbAyam` = NULL,
              `KbIkan` = NULL,
              `KbDomba` = NULL,
              `KbKerbau` = NULL,
              `KbBabi` = NULL,
              `ComKebunPanjang` = NULL,
              `ComKebunLebar` = NULL,
              `ComKebunArea` = NULL,
              `ComKbBayam` = NULL,
              `ComKbCabai` = NULL,
              `ComKbKacangPanjang` = NULL,
              `ComKbKangkung` = NULL,
              `ComKbSawi` = NULL,
              `ComKbTerong` = NULL,
              `ComKbTomat` = NULL,
              `ComKbKelor` = NULL,
              `ComKbSingkong` = NULL,
              `ComKbLabu` = NULL,
              `ComKbKatuk` = NULL,
              `ComKbKambing` = NULL,
              `ComKbSapi` = NULL,
              `ComKbBebek` = NULL,
              `ComKbAyam` = NULL,
              `ComKbIkan` = NULL,
              `ComKbDomba` = NULL,
              `ComKbKerbau` = NULL,
              `ComKbBabi` = NULL,
              `VegetableUtilization` = NULL,
              `HaveFishPond` = NULL,
              `FishPondLength` = NULL,
              `FishPondWidth` = NULL,
              `FishPondArea` = NULL,
              `fsNila` = NULL,
              `fsCarp` = NULL,
              `fsCatfish` = NULL,
              `fsTilapia` = NULL,
              `fsOthers` = NULL,
              `FishUtilization` = NULL,
              `EatRaisedFish` = NULL,
              `aSagu` = NULL,
              `aNasi` = NULL,
              `aMie` = NULL,
              `aJagung` = NULL,
              `aRoti` = NULL,
              `bUbiJalarKuning` = NULL,
              `bSingkongKuning` = NULL,
              `bWortel` = NULL,
              `bLabu` = NULL,
              `cUbiJalarPutih` = NULL,
              `cSingkongPutih` = NULL,
              `cTalas` = NULL,
              `cKentang` = NULL,
              `dBayam` = NULL,
              `dDaunMelinjo` = NULL,
              `dDaunPepaya` = NULL,
              `dDaunSingkong` = NULL,
              `dKangkung` = NULL,
              `dSawi` = NULL,
              `eKacangPanjang` = NULL,
              `eTomat` = NULL,
              `eTerong` = NULL,
              `fJambuMerah` = NULL,
              `fMangga` = NULL,
              `fPepaya` = NULL,
              `gJambuAir` = NULL,
              `gKelapa` = NULL,
              `gPisang` = NULL,
              `gRambutan` = NULL,
              `gSemangka` = NULL,
              `gSalak` = NULL,
              `hJeroan` = NULL,
              `hHati` = NULL,
              `iAyam` = NULL,
              `iBebek` = NULL,
              `iKambing` = NULL,
              `iKerbau` = NULL,
              `iSapi` = NULL,
              `iLainnya` = NULL,
              `jAyam` = NULL,
              `jBebek` = NULL,
              `jEntok` = NULL,
              `jPuyuh` = NULL,
              `kCumiCumi` = NULL,
              `kIkan` = NULL,
              `kIkanTeri` = NULL,
              `kKepiting` = NULL,
              `kKerang` = NULL,
              `kUdang` = NULL,
              `lAirTahuSusuKedelai` = NULL,
              `lSausKacang` = NULL,
              `lTahu` = NULL,
              `lTempe` = NULL,
              `lKacang` = NULL,
              `lKwaci` = NULL,
              `mKeju` = NULL,
              `mSusu` = NULL,
              `nMinyakGoreng` = NULL,
              `nMentega` = NULL,
              `nSantan` = NULL,
              `Score` = NULL,
              `cerRice` = NULL,
              `cerNoodles` = NULL,
              `cerCorn` = NULL,
              `cerCerealBubur` = NULL,
              `cerBread` = NULL,
              `cerWheatFlour` = NULL,
              `cerSorghum` = NULL,
              `cerMillet` = NULL,
              `wtrWhiteCassava` = NULL,
              `wtrTaro` = NULL,
              `wtrPotato` = NULL,
              `wtrSago` = NULL,
              `wtrSweetPotato` = NULL,
              `wtrPlantain` = NULL,
              `wtrYam` = NULL,
              `dgSpinach` = NULL,
              `dgCassavaLeaf` = NULL,
              `dgWaterSpinach` = NULL,
              `dgMelinjoLeaf` = NULL,
              `dgPapayaLeaf` = NULL,
              `dgPumpkinLeaf` = NULL,
              `dgLongBeansLeaf` = NULL,
              `dgMoringaLeaf` = NULL,
              `dgLeafMustard` = NULL,
              `dgSweetPotatoLeaf` = NULL,
              `dgPakis` = NULL,
              `dgKatukLeaf` = NULL,
              `dgTaroLeaf` = NULL,
              `dgOthers` = NULL,
              `rfMangoRipe` = NULL,
              `rfPapayaRipe` = NULL,
              `rfPassionFruit` = NULL,
              `rfEggplant` = NULL,
              `rfOrangeBanana` = NULL,
              `rvtSweetPotato` = NULL,
              `rvtYellowCassava` = NULL,
              `rvtCarrot` = NULL,
              `rvtOrangeSquash` = NULL,
              `rvtPumpkin` = NULL,
              `rvtRedPaprika` = NULL,
              `ofBanana` = NULL,
              `ofGuava` = NULL,
              `ofCoconut` = NULL,
              `ofLemon` = NULL,
              `ofWaterApple` = NULL,
              `ofDurian` = NULL,
              `ofAvocado` = NULL,
              `ofPineapple` = NULL,
              `ofSoursop` = NULL,
              `ofKedondong` = NULL,
              `ofSawo` = NULL,
              `ofWatermelon` = NULL,
              `ofLangsat` = NULL,
              `ofMangosteen` = NULL,
              `ofOthers` = NULL,
              `ovLongbeans` = NULL,
              `ovEggplant` = NULL,
              `ovBreadfruit` = NULL,
              `ovJackfruit` = NULL,
              `ovTomato` = NULL,
              `ovWhiteCabbage` = NULL,
              `ovCucumber` = NULL,
              `ovChayote` = NULL,
              `ovOnion` = NULL,
              `ovBambooShoots` = NULL,
              `ovLuffa` = NULL,
              `ovBitterMelon` = NULL,
              `ovPapaya` = NULL,
              `ovMushrooms` = NULL,
              `ovOthers` = NULL,
              `omOffal` = NULL,
              `omLiver` = NULL,
              `omLungs` = NULL,
              `omKidney` = NULL,
              `omHeart` = NULL,
              `meChicken` = NULL,
              `meDuck` = NULL,
              `meWildDuck` = NULL,
              `meQuail` = NULL,
              `meBeef` = NULL,
              `meLamb` = NULL,
              `meGoat` = NULL,
              `meBuffalo` = NULL,
              `mePork` = NULL,
              `fasFish` = NULL,
              `fasSquid` = NULL,
              `fasCrab` = NULL,
              `fasShellfish` = NULL,
              `fasShrimp` = NULL,
              `fasOctopus` = NULL,
              `egChickenEgg` = NULL,
              `egDuckEgg` = NULL,
              `egQuailEgg` = NULL,
              eWildDuck = NULL,
              `lnsTofu` = NULL,
              `lnsTempe` = NULL,
              `lnsTofuWater` = NULL,
              `lnsPeanutSauce` = NULL,
              `lnsMungBean` = NULL,
              `lnsSoybean` = NULL,
              `lnsJengkol` = NULL,
              `lnsPetai` = NULL,
              `lnsCowpea` = NULL,
              `lnsCashew` = NULL,
              `mdpCheese` = NULL,
              `mdpMilk` = NULL,
              `mdpYoghurt` = NULL,
              `mdpOthers` = NULL,
              `WDDSScore` = NULL,
              `HaveChildren` = NULL,
              `NrOfChildren` = NULL,
              `ChildAgeYear` = NULL,
              `ChildAgeMonth` = NULL,
              `GivenBreastfeed` = NULL,
              `StartGivenBreastfeed` = NULL,
              `TreatmentColustrum` = NULL,
              `GivenFoodBesidesASI` = NULL,
              `fwFormulaMilk` = NULL,
              `fwNonFormulaMilk` = NULL,
              `fwWater` = NULL,
              `fwSugarWater` = NULL,
              `fwStarchWater` = NULL,
              `fwCoconutWater` = NULL,
              `fwFruitJuice` = NULL,
              `fwSweetTea` = NULL,
              `fwHoney` = NULL,
              `fwMashedBanana` = NULL,
              `fwMashedRice` = NULL,
              `fwOthers` = NULL,
              `WhenGivenFoodBesidesASI` = NULL,
              `ChildrenMeal` = NULL,
              `ChildrenASI` = NULL,
              `Children3MonthASI` = NULL,
              `ChildrenNrGiveASI` = NULL,
              `ChildrenNrGiveMeal` = NULL,
              `ChildrenGiveKolestrum` = NULL,
              `MotherPregnant2Years` = NULL,
              `MotherPregnantEat` = NULL,
              WDDSRespondent = NULL,
              MothersRespondent = NULL
            WHERE
                `FarmerID` = ?
                AND `SurveyNr` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));

        //yg tidak diperlukan untuk update
        unset($post['sinutriFarmerName']);
        unset($post['sinutriFarmerID']);
        unset($post['sinutriSurveyNr']);

        foreach ($post as $k => $v) {
            $k = str_replace("sinutri", "", $k);
            $update[$k] = $v;
            if ($update[$k] == "")
                $update[$k] = null;
        }
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        $this->db->where('FarmerID', $FarmerID);
        $this->db->where('SurveyNr', $SurveyNr);
        $query = $this->db->update('ktv_nutrition', $update);

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

    public function deleteFarmerSinutri($FarmerID, $SurveyNr) {
        $this->db->trans_begin();

            $sql="INSERT INTO `his_ktv_nutrition` (
                  `DateHistory`,
                  `DeleteBy`,
                  `FarmerID`,
                  `OldFarmerID`,
                  `InterviewDate`,
                  `SurveyNr`,
                  `Season`,
                  `HaveVegetableGarden`,
                  `IsFamilyGarden`,
                  `IsCommmercialGarden`,
                  `KebunPanjang`,
                  `KebunLebar`,
                  `KebunArea`,
                  `KbBayam`,
                  `KbCabai`,
                  `KbKacangPanjang`,
                  `KbKangkung`,
                  `KbSawi`,
                  `KbTerong`,
                  `KbTomat`,
                  `KbKelor`,
                  `KbSingkong`,
                  `KbLabu`,
                  `KbKatuk`,
                  `KbKambing`,
                  `KbSapi`,
                  `KbBebek`,
                  `KbAyam`,
                  `KbIkan`,
                  `KbDomba`,
                  `KbKerbau`,
                  `KbBabi`,
                  `ComKebunPanjang`,
                  `ComKebunLebar`,
                  `ComKebunArea`,
                  `ComKbBayam`,
                  `ComKbCabai`,
                  `ComKbKacangPanjang`,
                  `ComKbKangkung`,
                  `ComKbSawi`,
                  `ComKbTerong`,
                  `ComKbTomat`,
                  `ComKbKelor`,
                  `ComKbSingkong`,
                  `ComKbLabu`,
                  `ComKbKatuk`,
                  `ComKbKambing`,
                  `ComKbSapi`,
                  `ComKbBebek`,
                  `ComKbAyam`,
                  `ComKbIkan`,
                  `ComKbDomba`,
                  `ComKbKerbau`,
                  `ComKbBabi`,
                  `VegetableUtilization`,
                  `ComVegetableUtilization`,
                  `HaveFishPond`,
                  `FishPondLength`,
                  `FishPondWidth`,
                  `FishPondArea`,
                  `fsNila`,
                  `fsCarp`,
                  `fsCatfish`,
                  `fsTilapia`,
                  `fsOthers`,
                  `FishUtilization`,
                  `EatRaisedFish`,
                  `aSagu`,
                  `aNasi`,
                  `aMie`,
                  `aJagung`,
                  `aRoti`,
                  `bUbiJalarKuning`,
                  `bSingkongKuning`,
                  `bWortel`,
                  `bLabu`,
                  `cUbiJalarPutih`,
                  `cSingkongPutih`,
                  `cTalas`,
                  `cKentang`,
                  `dBayam`,
                  `dDaunMelinjo`,
                  `dDaunPepaya`,
                  `dDaunSingkong`,
                  `dKangkung`,
                  `dSawi`,
                  `eKacangPanjang`,
                  `eTomat`,
                  `eTerong`,
                  `fJambuMerah`,
                  `fMangga`,
                  `fPepaya`,
                  `gJambuAir`,
                  `gKelapa`,
                  `gPisang`,
                  `gRambutan`,
                  `gSemangka`,
                  `gSalak`,
                  `hJeroan`,
                  `hHati`,
                  `iAyam`,
                  `iBebek`,
                  `iKambing`,
                  `iKerbau`,
                  `iSapi`,
                  `iLainnya`,
                  `jAyam`,
                  `jBebek`,
                  `jEntok`,
                  `jPuyuh`,
                  `kCumiCumi`,
                  `kIkan`,
                  `kIkanTeri`,
                  `kKepiting`,
                  `kKerang`,
                  `kUdang`,
                  `lAirTahuSusuKedelai`,
                  `lSausKacang`,
                  `lTahu`,
                  `lTempe`,
                  `lKacang`,
                  `lKwaci`,
                  `mKeju`,
                  `mSusu`,
                  `nMinyakGoreng`,
                  `nMentega`,
                  `nSantan`,
                  `Score`,
                  `WDDSRespondent`,
                  `cerRice`,
                  `cerNoodles`,
                  `cerCorn`,
                  `cerCerealBubur`,
                  `cerBread`,
                  `cerWheatFlour`,
                  `cerSorghum`,
                  `cerMillet`,
                  `wtrWhiteCassava`,
                  `wtrTaro`,
                  `wtrPotato`,
                  `wtrSago`,
                  `wtrSweetPotato`,
                  `wtrPlantain`,
                  `wtrYam`,
                  `dgSpinach`,
                  `dgCassavaLeaf`,
                  `dgWaterSpinach`,
                  `dgMelinjoLeaf`,
                  `dgPapayaLeaf`,
                  `dgPumpkinLeaf`,
                  `dgLongBeansLeaf`,
                  `dgMoringaLeaf`,
                  `dgLeafMustard`,
                  `dgSweetPotatoLeaf`,
                  `dgPakis`,
                  `dgKatukLeaf`,
                  `dgTaroLeaf`,
                  `dgOthers`,
                  `rfMangoRipe`,
                  `rfPapayaRipe`,
                  `rfPassionFruit`,
                  `rfEggplant`,
                  `rfOrangeBanana`,
                  `rvtSweetPotato`,
                  `rvtYellowCassava`,
                  `rvtCarrot`,
                  `rvtOrangeSquash`,
                  `rvtPumpkin`,
                  `rvtRedPaprika`,
                  `ofBanana`,
                  `ofGuava`,
                  `ofCoconut`,
                  `ofLemon`,
                  `ofWaterApple`,
                  `ofDurian`,
                  `ofAvocado`,
                  `ofPineapple`,
                  `ofSoursop`,
                  `ofKedondong`,
                  `ofSawo`,
                  `ofWatermelon`,
                  `ofLangsat`,
                  `ofMangosteen`,
                  `ofOthers`,
                  `ovLongbeans`,
                  `ovEggplant`,
                  `ovBreadfruit`,
                  `ovJackfruit`,
                  `ovTomato`,
                  `ovWhiteCabbage`,
                  `ovCucumber`,
                  `ovChayote`,
                  `ovOnion`,
                  `ovBambooShoots`,
                  `ovLuffa`,
                  `ovBitterMelon`,
                  `ovPapaya`,
                  `ovMushrooms`,
                  `ovOthers`,
                  `omOffal`,
                  `omLiver`,
                  `omLungs`,
                  `omKidney`,
                  `omHeart`,
                  `meChicken`,
                  `meDuck`,
                  `meWildDuck`,
                  `meQuail`,
                  `meBeef`,
                  `meLamb`,
                  `meGoat`,
                  `meBuffalo`,
                  `mePork`,
                  `fasFish`,
                  `fasSquid`,
                  `fasCrab`,
                  `fasShellfish`,
                  `fasShrimp`,
                  `fasOctopus`,
                  `egChickenEgg`,
                  `egDuckEgg`,
                  `egQuailEgg`,
                  `eWildDuck`,
                  `lnsTofu`,
                  `lnsTempe`,
                  `lnsTofuWater`,
                  `lnsPeanutSauce`,
                  `lnsMungBean`,
                  `lnsSoybean`,
                  `lnsJengkol`,
                  `lnsPetai`,
                  `lnsCowpea`,
                  `lnsCashew`,
                  `mdpCheese`,
                  `mdpMilk`,
                  `mdpYoghurt`,
                  `mdpOthers`,
                  `WDDSScore`,
                  `MothersRespondent`,
                  `HaveChildren`,
                  `NrOfChildren`,
                  `ChildAgeYear`,
                  `ChildAgeMonth`,
                  `GivenBreastfeed`,
                  `StartGivenBreastfeed`,
                  `TreatmentColustrum`,
                  `GivenFoodBesidesASI`,
                  `fwFormulaMilk`,
                  `fwNonFormulaMilk`,
                  `fwWater`,
                  `fwSugarWater`,
                  `fwStarchWater`,
                  `fwCoconutWater`,
                  `fwFruitJuice`,
                  `fwSweetTea`,
                  `fwHoney`,
                  `fwMashedBanana`,
                  `fwMashedRice`,
                  `fwOthers`,
                  `WhenGivenFoodBesidesASI`,
                  `ChildrenMeal`,
                  `ChildrenASI`,
                  `Children3MonthASI`,
                  `ChildrenNrGiveASI`,
                  `ChildrenNrGiveMeal`,
                  `ChildrenGiveKolestrum`,
                  `MotherPregnant2Years`,
                  `MotherPregnantEat`,
                  `DateCreated`,
                  `CreatedBy`,
                  `DateUpdated`,
                  `LastModifiedBy`,
                  `DateSynced`,
                  `StatusCode`,
                  `isValid`,
                  `ApprovedByME`,
                  `ApprovedByGO`,
                  `ApprovedByDC`,
                  `CommentValid`,
                  `DateSync`,
                  `uid`
                )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_nutrition a
                WHERE
                    a.FarmerID = ?
                    AND a.SurveyNr = ?
                LIMIT 1
            ";
            $this->db->query($sql, array($_SESSION['userid'], $FarmerID, $SurveyNr));

            $sql = "DELETE FROM ktv_nutrition WHERE FarmerID = ? AND SurveyNr = ? LIMIT 1";
            $this->db->query($sql, array($FarmerID, $SurveyNr));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to delete data";
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "Data deleted";
            }
            return $results;
    }

    public function farmerSinutriFormEditGet($FarmerID, $SurveyNr) {
        $sql = "SELECT
                   a.`FarmerID`,
                   b.`FarmerName`,
                  a.`OldFarmerID`,
                  DATE_FORMAT(a.`InterviewDate`,'%Y-%m-%d') AS InterviewDate,
                  a.`SurveyNr`,
                  a.`Season`,
                  a.`HaveVegetableGarden`,
                  a.`IsFamilyGarden`,
                  a.`IsCommmercialGarden`,
                  a.`KebunPanjang`,
                  a.`KebunLebar`,
                  a.`KebunArea`,
                  a.`KbBayam`,
                  a.`KbCabai`,
                  a.`KbKacangPanjang`,
                  a.`KbKangkung`,
                  a.`KbSawi`,
                  a.`KbTerong`,
                  a.`KbTomat`,
                  a.`KbKelor`,
                  a.`KbSingkong`,
                  a.`KbLabu`,
                  a.`KbKatuk`,
                  a.`KbKambing`,
                  a.`KbSapi`,
                  a.`KbBebek`,
                  a.`KbAyam`,
                  a.`KbIkan`,
                  a.`KbDomba`,
                  a.`KbKerbau`,
                  a.`KbBabi`,
                  a.`ComKebunPanjang`,
                  a.`ComKebunLebar`,
                  a.`ComKebunArea`,
                  a.`ComKbBayam`,
                  a.`ComKbCabai`,
                  a.`ComKbKacangPanjang`,
                  a.`ComKbKangkung`,
                  a.`ComKbSawi`,
                  a.`ComKbTerong`,
                  a.`ComKbTomat`,
                  a.`ComKbKelor`,
                  a.`ComKbSingkong`,
                  a.`ComKbLabu`,
                  a.`ComKbKatuk`,
                  a.`ComKbKambing`,
                  a.`ComKbSapi`,
                  a.`ComKbBebek`,
                  a.`ComKbAyam`,
                  a.`ComKbIkan`,
                  a.`ComKbDomba`,
                  a.`ComKbKerbau`,
                  a.`ComKbBabi`,
                  a.`VegetableUtilization`,
                  a.`ComVegetableUtilization`,
                  a.`HaveFishPond`,
                  a.`FishPondLength`,
                  a.`FishPondWidth`,
                  a.`FishPondArea`,
                  a.`fsNila`,
                  a.`fsCarp`,
                  a.`fsCatfish`,
                  a.`fsTilapia`,
                  a.`fsOthers`,
                  a.`FishUtilization`,
                  a.`EatRaisedFish`,
                  a.`aSagu`,
                  a.`aNasi`,
                  a.`aMie`,
                  a.`aJagung`,
                  a.`aRoti`,
                  a.`bUbiJalarKuning`,
                  a.`bSingkongKuning`,
                  a.`bWortel`,
                  a.`bLabu`,
                  a.`cUbiJalarPutih`,
                  a.`cSingkongPutih`,
                  a.`cTalas`,
                  a.`cKentang`,
                  a.`dBayam`,
                  a.`dDaunMelinjo`,
                  a.`dDaunPepaya`,
                  a.`dDaunSingkong`,
                  a.`dKangkung`,
                  a.`dSawi`,
                  a.`eKacangPanjang`,
                  a.`eTomat`,
                  a.`eTerong`,
                  a.`fJambuMerah`,
                  a.`fMangga`,
                  a.`fPepaya`,
                  a.`gJambuAir`,
                  a.`gKelapa`,
                  a.`gPisang`,
                  a.`gRambutan`,
                  a.`gSemangka`,
                  a.`gSalak`,
                  a.`hJeroan`,
                  a.`hHati`,
                  a.`iAyam`,
                  a.`iBebek`,
                  a.`iKambing`,
                  a.`iKerbau`,
                  a.`iSapi`,
                  a.`iLainnya`,
                  a.`jAyam`,
                  a.`jBebek`,
                  a.`jEntok`,
                  a.`jPuyuh`,
                  a.`kCumiCumi`,
                  a.`kIkan`,
                  a.`kIkanTeri`,
                  a.`kKepiting`,
                  a.`kKerang`,
                  a.`kUdang`,
                  a.`lAirTahuSusuKedelai`,
                  a.`lSausKacang`,
                  a.`lTahu`,
                  a.`lTempe`,
                  a.`lKacang`,
                  a.`lKwaci`,
                  a.`mKeju`,
                  a.`mSusu`,
                  a.`nMinyakGoreng`,
                  a.`nMentega`,
                  a.`nSantan`,
                  a.`Score`,
                  a.`cerRice`,
                  a.`cerNoodles`,
                  a.`cerCorn`,
                  a.`cerCerealBubur`,
                  a.`cerBread`,
                  a.`cerWheatFlour`,
                  a.`cerSorghum`,
                  a.`cerMillet`,
                  a.`wtrWhiteCassava`,
                  a.`wtrTaro`,
                  a.`wtrPotato`,
                  a.`wtrSago`,
                  a.`wtrSweetPotato`,
                  a.`wtrPlantain`,
                  a.`wtrYam`,
                  a.`dgSpinach`,
                  a.`dgCassavaLeaf`,
                  a.`dgWaterSpinach`,
                  a.`dgMelinjoLeaf`,
                  a.`dgPapayaLeaf`,
                  a.`dgPumpkinLeaf`,
                  a.`dgLongBeansLeaf`,
                  a.`dgMoringaLeaf`,
                  a.`dgLeafMustard`,
                  a.`dgSweetPotatoLeaf`,
                  a.`dgPakis`,
                  a.`dgKatukLeaf`,
                  a.`dgTaroLeaf`,
                  a.`dgOthers`,
                  a.`rfMangoRipe`,
                  a.`rfPapayaRipe`,
                  a.`rfPassionFruit`,
                  a.`rfEggplant`,
                  a.`rfOrangeBanana`,
                  a.`rvtSweetPotato`,
                  a.`rvtYellowCassava`,
                  a.`rvtCarrot`,
                  a.`rvtOrangeSquash`,
                  a.`rvtPumpkin`,
                  a.`rvtRedPaprika`,
                  a.`ofBanana`,
                  a.`ofGuava`,
                  a.`ofCoconut`,
                  a.`ofLemon`,
                  a.`ofWaterApple`,
                  a.`ofDurian`,
                  a.`ofAvocado`,
                  a.`ofPineapple`,
                  a.`ofSoursop`,
                  a.`ofKedondong`,
                  a.`ofSawo`,
                  a.`ofWatermelon`,
                  a.`ofLangsat`,
                  a.`ofMangosteen`,
                  a.`ofOthers`,
                  a.`ovLongbeans`,
                  a.`ovEggplant`,
                  a.`ovBreadfruit`,
                  a.`ovJackfruit`,
                  a.`ovTomato`,
                  a.`ovWhiteCabbage`,
                  a.`ovCucumber`,
                  a.`ovChayote`,
                  a.`ovOnion`,
                  a.`ovBambooShoots`,
                  a.`ovLuffa`,
                  a.`ovBitterMelon`,
                  a.`ovPapaya`,
                  a.`ovMushrooms`,
                  a.`ovOthers`,
                  a.`omOffal`,
                  a.`omLiver`,
                  a.`omLungs`,
                  a.`omKidney`,
                  a.`omHeart`,
                  a.`meChicken`,
                  a.`meDuck`,
                  a.`meWildDuck`,
                  a.`meQuail`,
                  a.`meBeef`,
                  a.`meLamb`,
                  a.`meGoat`,
                  a.`meBuffalo`,
                  a.`mePork`,
                  a.`fasFish`,
                  a.`fasSquid`,
                  a.`fasCrab`,
                  a.`fasShellfish`,
                  a.`fasShrimp`,
                  a.`fasOctopus`,
                  a.`egChickenEgg`,
                  a.`egDuckEgg`,
                  a.`egQuailEgg`,
                  a.eWildDuck,
                  a.`lnsTofu`,
                  a.`lnsTempe`,
                  a.`lnsTofuWater`,
                  a.`lnsPeanutSauce`,
                  a.`lnsMungBean`,
                  a.`lnsSoybean`,
                  a.`lnsJengkol`,
                  a.`lnsPetai`,
                  a.`lnsCowpea`,
                  a.`lnsCashew`,
                  a.`mdpCheese`,
                  a.`mdpMilk`,
                  a.`mdpYoghurt`,
                  a.`mdpOthers`,
                  a.`WDDSScore`,
                  a.`HaveChildren`,
                  a.`NrOfChildren`,
                  a.`ChildAgeYear`,
                  a.`ChildAgeMonth`,
                  a.`GivenBreastfeed`,
                  a.`StartGivenBreastfeed`,
                  a.`TreatmentColustrum`,
                  a.`GivenFoodBesidesASI`,
                  a.`fwFormulaMilk`,
                  a.`fwNonFormulaMilk`,
                  a.`fwWater`,
                  a.`fwSugarWater`,
                  a.`fwStarchWater`,
                  a.`fwCoconutWater`,
                  a.`fwFruitJuice`,
                  a.`fwSweetTea`,
                  a.`fwHoney`,
                  a.`fwMashedBanana`,
                  a.`fwMashedRice`,
                  a.`fwOthers`,
                  a.`WhenGivenFoodBesidesASI`,
                  a.`ChildrenMeal`,
                  a.`ChildrenASI`,
                  a.`Children3MonthASI`,
                  a.`ChildrenNrGiveASI`,
                  a.`ChildrenNrGiveMeal`,
                  a.`ChildrenGiveKolestrum`,
                  a.`MotherPregnant2Years`,
                  a.`MotherPregnantEat`,
                  a.WDDSRespondent,
                  a.MothersRespondent
                FROM
                    `ktv_nutrition` a
                    LEFT JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
                WHERE
                    a.`FarmerID` = ? AND
                    a.`SurveyNr` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));
        return $query->row_array();
    }

    public function farmerSinaffCekSurvey($FarmerID) {
        $sql = "SELECT
                SurveyNr
            FROM
                ktv_farmer_financial
            WHERE
                FarmerID = ?
            ORDER BY SurveyNr DESC
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID));
        $data = $query->row_array();
        if ($data['SurveyNr'] != "") {
            return 'postline';
        } else {
            return 'baseline';
        }
    }

    public function farmerSinaffRefSurvey() {
        $sql = "SELECT
                    SurveyNr AS id,
                    CONCAT(SurveyNr,' - ',SurveyTxt) AS label
                FROM
                    ktv_survey
                WHERE
                    SurveyNr != '0'
                ORDER BY SurveyNr ASC";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function farmerRefBankGet() {
        $sql = "SELECT
                    BankID AS `id`,
                    BankName AS label
                FROM
                    ktv_bank
                WHERE
                    StatusCode = 'active'
                ORDER BY BankName ASC";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function getFarmerSinaffMethod($FarmerID, $SurveyNr) {
        $sql = "SELECT
                    FarmerID
                FROM
                    ktv_farmer_financial
                WHERE
                    FarmerID = ? AND
                    SurveyNr = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));
        $data = $query->row_array();

        if ($data['FarmerID'] != "") {
            return 'update';
        } else {
            return 'insert';
        }
    }

    public function insertFarmerSinaff($post) {
        $this->db->trans_start();

        //yg tidak diperlukan untuk insert
        unset($post['sinaffFarmerName']);

        //replace datanya jika ada koma
        $post['sinaffAmountCurrentLoan'] = str_replace(",", "", $post['sinaffAmountCurrentLoan']);
        if ($post['sinaffAmountCurrentLoan'] == "")
            $post['sinaffAmountCurrentLoan'] = 0;
        $post['sinaffAmountOutsCurrentLoan'] = str_replace(",", "", $post['sinaffAmountOutsCurrentLoan']);
        if ($post['sinaffAmountOutsCurrentLoan'] == "")
            $post['sinaffAmountOutsCurrentLoan'] = 0;
        $post['sinaffCocoaPriceToday'] = str_replace(",", "", $post['sinaffCocoaPriceToday']);
        if ($post['sinaffCocoaPriceToday'] == "")
            $post['sinaffCocoaPriceToday'] = 0;

        foreach ($post as $k => $v) {
            $k = str_replace("sinaff", "", $k);
            $insert[$k] = $v;

            //cek yg perlu default value
            if ($insert[$k] == "")
                $insert[$k] = NULL;
        }

        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_farmer_financial', $insert);

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

    public function updateFarmerSinaff($post) {
        $this->db->trans_start();
        $FarmerID = $post['sinaffFarmerID'];
        $SurveyNr = $post['sinaffSurveyNr'];

        //replace datanya jika ada koma
        $post['sinaffAmountCurrentLoan'] = str_replace(",", "", $post['sinaffAmountCurrentLoan']);
        if ($post['sinaffAmountCurrentLoan'] == "")
            $post['sinaffAmountCurrentLoan'] = 0;
        $post['sinaffAmountOutsCurrentLoan'] = str_replace(",", "", $post['sinaffAmountOutsCurrentLoan']);
        if ($post['sinaffAmountOutsCurrentLoan'] == "")
            $post['sinaffAmountOutsCurrentLoan'] = 0;
        $post['sinaffCocoaPriceToday'] = str_replace(",", "", $post['sinaffCocoaPriceToday']);
        if ($post['sinaffCocoaPriceToday'] == "")
            $post['sinaffCocoaPriceToday'] = 0;

        //reset semuanya dulu..
        $sql = "UPDATE `ktv_farmer_financial` SET
                  `InterviewDate` = NULL,
                  `Account` = NULL,
                  `FamilyAccount` = NULL,
                  `AccountTypeTabungan` = NULL,
                  `AccountTypeDeposito` = NULL,
                  `AccountTypeKoran` = NULL,
                  `AccountTypeLainnya` = NULL,
                  `AccountHolderFarmer` = NULL,
                  `AccountHolderName` = NULL,
                  `AccountBankID` = NULL,
                  `AccountBankName` = NULL,
                  `AccountBankBranch` = NULL,
                  `AccountNumber` = NULL,
                  `AccountNoDetails` = NULL,
                  `SavingInCooperatives` = NULL,
                  `DepositWithdrawnMoneyLast12m` = NULL,
                  `AccountFeesToPay` = NULL,
                  `AccountInterestRate` = NULL,
                  `MoneyUsageHarian` = NULL,
                  `MoneyUsageTabung` = NULL,
                  `MoneyUsageInvestasi` = NULL,
                  `MoneyUsageEmas` = NULL,
                  `MoneyUsageKonsumsi` = NULL,
                  `NotSavingJauh` = NULL,
                  `NotSavingTidakBeruang` = NULL,
                  `NotSavingBiayaTinggi` = NULL,
                  `NotSavingTidakPercaya` = NULL,
                  `NotSavingAdaMenabung` = NULL,
                  `NotSavingLainnya` = NULL,
                  `SavingUnitRumah` = NULL,
                  `SavingUnitBank` = NULL,
                  `SavingUnitKoperasi` = NULL,
                  `SavingUnitPedagang` = NULL,
                  `SavingUnitArisan` = NULL,
                  `SavingUnitOrang` = NULL,
                  `SavingUnitLembaga` = NULL,
                  `SavingUnitMeminjamkan` = NULL,
                  `DistanceSavingLocation` = NULL,
                  `AmountSaving` = NULL,
                  `AfterGFPOpenAccount` = NULL,
                  `AfterGFPStartSaving` = NULL,
                  `AfterGFPRecordRevenue` = NULL,
                  `AfterGFPFinancialSituation` = NULL,
                  `AfterGFPControlFinancial` = NULL,
                  `AfterGFPNewLoans` = NULL,
                  `AfterGFPDidNotChange` = NULL,
                  `FutureReasonSekolah` = NULL,
                  `FutureReasonRumahTangga` = NULL,
                  `FutureReasonSumbangan` = NULL,
                  `FutureReasonDarurat` = NULL,
                  `FutureReasonKesehatan` = NULL,
                  `FutureReasonInvestasiKebun` = NULL,
                  `FutureReasonInvestasiLain` = NULL,
                  `FutureReasonRumah` = NULL,
                  `FutureReasonLahan` = NULL,
                  `FutureReasonKendaraan` = NULL,
                  `FutureReasonHaji` = NULL,
                  `FutureReasonPensiun` = NULL,
                  `FutureReasonLain` = NULL,
                  `ImportantFactorKemanan` = NULL,
                  `ImportantFactorLikuiditas` = NULL,
                  `ImportantFactorAksesibilitas` = NULL,
                  `ImportantFactorKepercayaan` = NULL,
                  `ImportantFactorBiaya` = NULL,
                  `ImportantFactorBunga` = NULL,
                  `ImportantFactorLain` = NULL,
                  `LoanYesNo` = NULL,
                  `AssistanceFromBU` = NULL,
                  `AmountCurrentLoan` = NULL,
                  `AmountOutsCurrentLoan` = NULL,
                  `LoanUnitTengkulak` = NULL,
                  `LoanUnitKeluarga` = NULL,
                  `LoanUnitRentenir` = NULL,
                  `LoanUnitBank` = NULL,
                  `LoanUnitKoperasi` = NULL,
                  `LoanUnitMasjid` = NULL,
                  `LoanUnitLainnya` = NULL,
                  `PreviousLoan` = NULL,
                  `CollateralCurrentLoan` = NULL,
                  `EasyCurrentLoan` = NULL,
                  `DisburseIntervalCurrentLoan` = NULL,
                  `RepaymentScheduleCurrentLoan` = NULL,
                  `EasyGetNewLoan` = NULL,
                  `UsageCurrentLoanHarian` = NULL,
                  `UsageCurrentLoanSekolah` = NULL,
                  `UsageCurrentLoanRumahTangga` = NULL,
                  `UsageCurrentLoanSumbangan` = NULL,
                  `UsageCurrentLoanHutang` = NULL,
                  `UsageCurrentLoanDarurat` = NULL,
                  `UsageCurrentLoanKesehatan` = NULL,
                  `UsageCurrentLoanInvestasiKebun` = NULL,
                  `UsageCurrentLoanInvestasiLain` = NULL,
                  `UsageCurrentLoanRumah` = NULL,
                  `UsageCurrentLoanLahan` = NULL,
                  `UsageCurrentLoanKendaraan` = NULL,
                  `UsageCurrentLoanHaji` = NULL,
                  `UsageCurrentLoanPensiun` = NULL,
                  `UsageCurrentLoanLainnya` = NULL,
                  `TerminatedLoan` = NULL,
                  `MoneyToRepayLoanPenghasilan` = NULL,
                  `MoneyToRepayLoanPinjaman` = NULL,
                  `MoneyToRepayLoanTanah` = NULL,
                  `MoneyToRepayLoanTernak` = NULL,
                  `MoneyToRepayLoanDeposito` = NULL,
                  `MoneyToRepayLoanLainnya` = NULL,
                  `ProfitSharingLoan` = NULL,
                  `ResponsibilityLoan` = NULL,
                  `WorryToRepayLoan` = NULL,
                  `DifficultCoverExpenses` = NULL,
                  `PostponeExpensesSewaRumah` = NULL,
                  `PostponeExpensesKebun` = NULL,
                  `PostponeExpensesMakanan` = NULL,
                  `PostponeExpensesKesehatan` = NULL,
                  `PostponeExpensesSosial` = NULL,
                  `PostponeExpensesListrik` = NULL,
                  `PostponeExpensesPendidikan` = NULL,
                  `PostponeExpensesSandang` = NULL,
                  `PostponeExpensesAngsuran` = NULL,
                  `PostponeExpensesLainnya` = NULL,
                  `DifficultSocialContributions` = NULL,
                  `MoneyUrgentExpensesTabungan` = NULL,
                  `MoneyUrgentExpensesMeminjamKeluarga` = NULL,
                  `MoneyUrgentExpensesMeminjamTengkulak` = NULL,
                  `MoneyUrgentExpensesMenjual` = NULL,
                  `MoneyUrgentExpensesLainnya` = NULL,
                  `CostUnsubsidizedFertilizer` = NULL,
                  `OtherIncome` = NULL,
                  `PensionPlan` = NULL,
                  `OtherIncomeRegular` = NULL,
                  `SourceOtherIncomeGajiTetap` = NULL,
                  `SourceOtherIncomeGajiPasangan` = NULL,
                  `SourceOtherIncomeUsaha` = NULL,
                  `SourceOtherIncomeFamily` = NULL,
                  `SourceOtherIncomeLainnya` = NULL,
                  `AmountOtherIncome` = NULL,
                  `CocoaProfitableBusiness` = NULL,
                  `LoanBetterThanSaving` = NULL,
                  `UnsubsidizedFertilizerProfitable` = NULL,
                  `HighInterestRate` = NULL,
                  `LoanWithTrader` = NULL,
                  `BetterWetDriedBeans` = NULL,
                  `GoodLoanClient` = NULL,
                  `TrustGroupMembers` = NULL,
                  `RepayLoanGroupMember` = NULL,
                  `TrustBank` = NULL,
                  `CocoaFarmPayExpenses` = NULL,
                  `DiscipilinedSaveMoney` = NULL,
                  `TradersRich` = NULL,
                  `CollateralOfferedBank` = NULL,
                  `ManyCocoaFarmSale` = NULL,
                  `SatisfiedCocoaBusiness` = NULL,
                  `PayCocoaBetterInterest` = NULL,
                  `NeedLoan` = NULL,
                  `MobilePhone` = NULL,
                  `LoanAnalysisKnowledge` = NULL,
                  `IslamicFinancialAwareness` = NULL,
                  `LearnToSaveMoney` = NULL,
                  `CocoaPriceTodayInfo` = NULL,
                  `CocoaPriceToday` = NULL,
                  `ReasonNotHavePhoneTidakButuh` = NULL,
                  `ReasonNotHavePhoneMahal` = NULL,
                  `ReasonNotHavePhoneSinyal` = NULL,
                  `ReasonNotHavePhoneLainnya` = NULL,
                  `ValueCocoaFarm` = NULL,
                  `InsuranceKnowledge` = NULL,
                  `PastNowInsurance` = NULL,
                  `InsuranceTypeMotor` = NULL,
                  `InsuranceTypePanen` = NULL,
                  `InsuranceTypeBanjir` = NULL,
                  `InsuranceTypeKemarau` = NULL,
                  `InsuranceTypeMobil` = NULL,
                  `InsuranceTypeKesehatan` = NULL,
                  `InsuranceTypeJiwa` = NULL,
                  `InsuranceTypeLainnya` = NULL,
                  `GetLoanToBuyLand` = NULL,
                  `GetLoanInvestCocoa` = NULL,
                  `GetLoanInvestOtherBusiness` = NULL,
                  `GetLoanUsedOthers` = NULL,
                  `SellCocoaMoneyUsage` = NULL,
                   GiveCocoaLandWhenOlder = NULL,
                  `NeedFinancialTraining` = NULL,
                  `FinancialTrainingDeposit` = NULL,
                  `FinancialTrainingLoan` = NULL,
                  `FinancialTrainingRecording` = NULL,
                  `FinancialTrainingCashFlowPlan` = NULL,
                  `FinancialTrainingPersonalRecom` = NULL,
                  `FinancialTrainingLoanSchemeGov` = NULL,
                  `InfoFinancialTrainingChairmanFarmerGroup` = NULL,
                  `InfoFinancialTrainingScppStaff` = NULL,
                  `InfoFinancialTrainingFarmerCoop` = NULL,
                  `InfoFinancialTrainingBank` = NULL,
                  `InfoFinancialTrainingOthers` = NULL
                WHERE
                    `FarmerID` = ?
                    AND `SurveyNr` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));

        //yg tidak diperlukan untuk update
        unset($post['sinaffFarmerName']);
        unset($post['sinaffFarmerID']);
        unset($post['sinaffSurveyNr']);

        foreach ($post as $k => $v) {
            $k = str_replace("sinaff", "", $k);
            $update[$k] = $v;
        }
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        $this->db->where('FarmerID', $FarmerID);
        $this->db->where('SurveyNr', $SurveyNr);
        $query = $this->db->update('ktv_farmer_financial', $update);

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

    public function farmerSinaffFormEditGet($FarmerID, $SurveyNr) {
        $sql = "SELECT
                  a.`FarmerID`,
                  b.FarmerName,
                  a.`SurveyNr`,
                  DATE_FORMAT(a.`InterviewDate`,'%Y-%m-%d') AS InterviewDate,
                  a.`isValid`,
                  a.`Account`,
                  a.`FamilyAccount`,
                  a.`AccountTypeTabungan`,
                  a.`AccountTypeDeposito`,
                  a.`AccountTypeKoran`,
                  a.`AccountTypeLainnya`,
                  a.`AccountHolderFarmer`,
                  a.`AccountHolderName`,
                  a.`AccountBankID`,
                  a.`AccountBankName`,
                  a.`AccountBankBranch`,
                  a.`AccountNumber`,
                  a.`AccountNoDetails`,
                  a.`SavingInCooperatives`,
                  a.`DepositWithdrawnMoneyLast12m`,
                  a.`AccountFeesToPay`,
                  a.`AccountInterestRate`,
                  a.`MoneyUsageHarian`,
                  a.`MoneyUsageTabung`,
                  a.`MoneyUsageInvestasi`,
                  a.`MoneyUsageEmas`,
                  a.`MoneyUsageKonsumsi`,
                  a.`NotSavingJauh`,
                  a.`NotSavingTidakBeruang`,
                  a.`NotSavingBiayaTinggi`,
                  a.`NotSavingTidakPercaya`,
                  a.`NotSavingAdaMenabung`,
                  a.`NotSavingLainnya`,
                  a.`SavingUnitRumah`,
                  a.`SavingUnitBank`,
                  a.`SavingUnitKoperasi`,
                  a.`SavingUnitPedagang`,
                  a.`SavingUnitArisan`,
                  a.`SavingUnitOrang`,
                  a.`SavingUnitLembaga`,
                  a.`SavingUnitMeminjamkan`,
                  a.`DistanceSavingLocation`,
                  a.`AmountSaving`,
                  a.`AfterGFPOpenAccount`,
                  a.`AfterGFPStartSaving`,
                  a.`AfterGFPRecordRevenue`,
                  a.`AfterGFPFinancialSituation`,
                  a.`AfterGFPControlFinancial`,
                  a.`AfterGFPNewLoans`,
                  a.`AfterGFPDidNotChange`,
                  a.`FutureReasonSekolah`,
                  a.`FutureReasonRumahTangga`,
                  a.`FutureReasonSumbangan`,
                  a.`FutureReasonDarurat`,
                  a.`FutureReasonKesehatan`,
                  a.`FutureReasonInvestasiKebun`,
                  a.`FutureReasonInvestasiLain`,
                  a.`FutureReasonRumah`,
                  a.`FutureReasonLahan`,
                  a.`FutureReasonKendaraan`,
                  a.`FutureReasonHaji`,
                  a.`FutureReasonPensiun`,
                  a.`FutureReasonLain`,
                  a.`ImportantFactorKemanan`,
                  a.`ImportantFactorLikuiditas`,
                  a.`ImportantFactorAksesibilitas`,
                  a.`ImportantFactorKepercayaan`,
                  a.`ImportantFactorBiaya`,
                  a.`ImportantFactorBunga`,
                  a.`ImportantFactorLain`,
                  a.`LoanYesNo`,
                  a.`AssistanceFromBU`,
                  a.`AmountCurrentLoan`,
                  a.`AmountOutsCurrentLoan`,
                  a.`LoanUnitTengkulak`,
                  a.`LoanUnitKeluarga`,
                  a.`LoanUnitRentenir`,
                  a.`LoanUnitBank`,
                  a.`LoanUnitKoperasi`,
                  a.`LoanUnitMasjid`,
                  a.`LoanUnitLainnya`,
                  a.`PreviousLoan`,
                  a.`CollateralCurrentLoan`,
                  a.`EasyCurrentLoan`,
                  a.`DisburseIntervalCurrentLoan`,
                  a.`RepaymentScheduleCurrentLoan`,
                  a.`EasyGetNewLoan`,
                  a.`UsageCurrentLoanHarian`,
                  a.`UsageCurrentLoanSekolah`,
                  a.`UsageCurrentLoanRumahTangga`,
                  a.`UsageCurrentLoanSumbangan`,
                  a.`UsageCurrentLoanHutang`,
                  a.`UsageCurrentLoanDarurat`,
                  a.`UsageCurrentLoanKesehatan`,
                  a.`UsageCurrentLoanInvestasiKebun`,
                  a.`UsageCurrentLoanInvestasiLain`,
                  a.`UsageCurrentLoanRumah`,
                  a.`UsageCurrentLoanLahan`,
                  a.`UsageCurrentLoanKendaraan`,
                  a.`UsageCurrentLoanHaji`,
                  a.`UsageCurrentLoanPensiun`,
                  a.`UsageCurrentLoanLainnya`,
                  a.`TerminatedLoan`,
                  a.`MoneyToRepayLoanPenghasilan`,
                  a.`MoneyToRepayLoanPinjaman`,
                  a.`MoneyToRepayLoanTanah`,
                  a.`MoneyToRepayLoanTernak`,
                  a.`MoneyToRepayLoanDeposito`,
                  a.`MoneyToRepayLoanLainnya`,
                  a.`ProfitSharingLoan`,
                  a.`ResponsibilityLoan`,
                  a.`WorryToRepayLoan`,
                  a.`DifficultCoverExpenses`,
                  a.`PostponeExpensesSewaRumah`,
                  a.`PostponeExpensesKebun`,
                  a.`PostponeExpensesMakanan`,
                  a.`PostponeExpensesKesehatan`,
                  a.`PostponeExpensesSosial`,
                  a.`PostponeExpensesListrik`,
                  a.`PostponeExpensesPendidikan`,
                  a.`PostponeExpensesSandang`,
                  a.`PostponeExpensesAngsuran`,
                  a.`PostponeExpensesLainnya`,
                  a.`DifficultSocialContributions`,
                  a.`MoneyUrgentExpensesTabungan`,
                  a.`MoneyUrgentExpensesMeminjamKeluarga`,
                  a.`MoneyUrgentExpensesMeminjamTengkulak`,
                  a.`MoneyUrgentExpensesMenjual`,
                  a.`MoneyUrgentExpensesLainnya`,
                  a.`CostUnsubsidizedFertilizer`,
                  a.`OtherIncome`,
                  a.`PensionPlan`,
                  a.`OtherIncomeRegular`,
                  a.`SourceOtherIncomeGajiTetap`,
                  a.`SourceOtherIncomeGajiPasangan`,
                  a.`SourceOtherIncomeUsaha`,
                  a.`SourceOtherIncomeFamily`,
                  a.`SourceOtherIncomeLainnya`,
                  a.`AmountOtherIncome`,
                  a.`CocoaProfitableBusiness`,
                  a.`LoanBetterThanSaving`,
                  a.`UnsubsidizedFertilizerProfitable`,
                  a.`HighInterestRate`,
                  a.`LoanWithTrader`,
                  a.`BetterWetDriedBeans`,
                  a.`GoodLoanClient`,
                  a.`TrustGroupMembers`,
                  a.`RepayLoanGroupMember`,
                  a.`TrustBank`,
                  a.`CocoaFarmPayExpenses`,
                  a.`DiscipilinedSaveMoney`,
                  a.`TradersRich`,
                  a.`CollateralOfferedBank`,
                  a.`ManyCocoaFarmSale`,
                  a.`SatisfiedCocoaBusiness`,
                  a.`PayCocoaBetterInterest`,
                  a.`NeedLoan`,
                  a.`MobilePhone`,
                  a.`LoanAnalysisKnowledge`,
                  a.`IslamicFinancialAwareness`,
                  a.`LearnToSaveMoney`,
                  a.`CocoaPriceTodayInfo`,
                  a.`CocoaPriceToday`,
                  a.`ReasonNotHavePhoneTidakButuh`,
                  a.`ReasonNotHavePhoneMahal`,
                  a.`ReasonNotHavePhoneSinyal`,
                  a.`ReasonNotHavePhoneLainnya`,
                  a.`ValueCocoaFarm`,
                  a.`InsuranceKnowledge`,
                  a.`PastNowInsurance`,
                  a.`InsuranceTypeMotor`,
                  a.`InsuranceTypePanen`,
                  a.`InsuranceTypeBanjir`,
                  a.`InsuranceTypeKemarau`,
                  a.`InsuranceTypeMobil`,
                  a.`InsuranceTypeKesehatan`,
                  a.`InsuranceTypeJiwa`,
                  a.`InsuranceTypeLainnya`,
                  a.`GetLoanToBuyLand`,
                  a.`GetLoanInvestCocoa`,
                  a.`GetLoanInvestOtherBusiness`,
                  a.`GetLoanUsedOthers`,
                  a.`SellCocoaMoneyUsage`,
                  a.GiveCocoaLandWhenOlder,
                  a.`NeedFinancialTraining`,
                  a.`FinancialTrainingDeposit`,
                  a.`FinancialTrainingLoan`,
                  a.`FinancialTrainingRecording`,
                  a.`FinancialTrainingCashFlowPlan`,
                  a.`FinancialTrainingPersonalRecom`,
                  a.`FinancialTrainingLoanSchemeGov`,
                  a.`InfoFinancialTrainingChairmanFarmerGroup`,
                  a.`InfoFinancialTrainingScppStaff`,
                  a.`InfoFinancialTrainingFarmerCoop`,
                  a.`InfoFinancialTrainingBank`,
                  a.`InfoFinancialTrainingOthers`
                FROM
                    `ktv_farmer_financial` a
                     LEFT JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
                WHERE
                    a.FarmerID = ? AND
                    a.SurveyNr = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr));
        return $query->row_array();
    }

    public function mwFarmerPhoto($farmer_id) {
        $sql = "SELECT
                    b.Province,
                    a.Photo
                  FROM
                    ktv_farmer a
                  LEFT JOIN ktv_province b ON SUBSTRING(a.FarmerID,1,2) = b.ProvinceID
                  WHERE FarmerID = ?";
        $query = $this->db->query($sql, array($farmer_id));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;
    }

    public function getCoordinates($farmer_id, $gardenNr, $survey_nr) {
        $sql = "
SELECT a.FarmerID,
       b.FarmerName,
       a.GardenNr,
       d.SurveyNr,
       a.OrderNr,
       a.Latitude,
       a.Longitude,
       c.GardenHaUnCertified
FROM ktv_farmer_garden_area a
JOIN (
  SELECT
    ga.FarmerID, ga.GardenNr, ga.SurveyNr, MAX(Revision) AS Revision
  FROM ktv_farmer_garden_area ga
  WHERE
    ga.Status = 'verified'
  GROUP BY ga.FarmerID, ga.GardenNr, ga.SurveyNr
) r ON a.FarmerID = r.FarmerID AND a.GardenNr = r.GardenNr AND a.SurveyNr = r.SurveyNr AND a.Revision = r.Revision
LEFT JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
LEFT JOIN ktv_farmer_garden c ON c.FarmerID = a.FarmerID AND c.GardenNr = a.GardenNr AND a.SurveyNr = c.SurveyNr
LEFT JOIN
    (SELECT FarmerID,
            GardenNr,
            MAX(SurveyNr) AS SurveyNr
     FROM ktv_farmer_garden_area
     GROUP BY GardenNr,
              FarmerID) AS d ON a.FarmerID = d.FarmerID
WHERE a.FarmerID = ?
    AND a.GardenNr = ?
    AND a.SurveyNr = ?
            ";

        $query = $this->db->query($sql, array($farmer_id, $gardenNr, $survey_nr));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function getLatestSurvey($farmer_id, $survey_nr=null, $garden_nr=null) {
        $where = '';
        $params[] = $farmer_id;
        $sql = "
SELECT a.FarmerID,
       b.FarmerName,
       a.GardenNr,
       c.Longitude,
       c.Latitude,
       c.GardenHaUnCertified,
       MAX(a.SurveyNr) AS SurveyNr
FROM ktv_farmer_garden_area a
JOIN (
  SELECT
    ga.FarmerID, ga.GardenNr, ga.SurveyNr, MAX(Revision) AS Revision
  FROM ktv_farmer_garden_area ga
  WHERE
    ga.Status = 'verified'
  GROUP BY ga.FarmerID, ga.GardenNr, ga.SurveyNr
) r ON a.FarmerID = r.FarmerID AND a.GardenNr = r.GardenNr AND a.SurveyNr = r.SurveyNr AND a.Revision = r.Revision
LEFT JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
LEFT JOIN ktv_farmer_garden c ON c.GardenNr = a.GardenNr
AND c.SurveyNr = a.SurveyNr
AND c.FarmerID = a.FarmerID
WHERE a.FarmerID = ? --where--
GROUP BY a.GardenNr,
         a.FarmerID
            ";
        if (!empty($garden_nr)) {
            $where .= ' AND a.GardenNr = ?';
            $params[] = $garden_nr;
        }
        if (!empty($survey_nr)) {
            $where .= ' AND a.SurveyNr = ?';
            $params[] = $survey_nr;
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function getKMLDetail($farmer_id, $survey_nr) {
        $sql = "SELECT
            b.FarmerID,
            b.FarmerName,
            a.GardenNr,
            a.SurveyNr,
            a.Longitude,
            a.Latitude,
            a.GardenHaUnCertified
          FROM
            ktv_farmer_garden a
            LEFT JOIN ktv_farmer b
              ON b.FarmerID = a.FarmerID
          WHERE a.farmerID = ?
            AND a.SurveyNr = ?
        ";
        $query = $this->db->query($sql, array($farmer_id, $survey_nr));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function readPolygon($farmer, $garden, $survey = '')
    {
      $sql = "
SELECT
    a.FarmerID
    , a.GardenNr
    , a.SurveyNr
    , a.OrderNr
    , a.Latitude
    , a.Longitude
    , s.SurveyTxt
    , g.Latitude AS garden_latitude
    , g.Longitude AS garden_longitude
FROM `ktv_farmer_garden_area` a
JOIN (
  SELECT
    ga.FarmerID, ga.GardenNr, ga.SurveyNr, MAX(Revision) AS Revision
  FROM ktv_farmer_garden_area ga
  WHERE
    ga.Status = 'verified'
  GROUP BY ga.FarmerID, ga.GardenNr, ga.SurveyNr
) r ON a.FarmerID = r.FarmerID AND a.GardenNr = r.GardenNr AND a.SurveyNr = r.SurveyNr AND a.Revision = r.Revision
LEFT JOIN ktv_farmer_garden g ON a.FarmerID = g.FarmerID AND a.GardenNr = g.GardenNr AND a.SurveyNr = g.SurveyNr
LEFT JOIN ktv_survey s ON s.SurveyNr = a.SurveyNr
WHERE
  a.FarmerID = ?
  AND a.GardenNr = ?
  AND a.SurveyNr = ?
ORDER BY OrderNr
      ";
      $query = $this->db->query($sql, array($farmer, $garden, $survey));
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      $sql = "
SELECT
    a.FarmerID
    , a.GardenNr
    , a.SurveyNr
    , a.Latitude
    , a.Longitude
    , s.SurveyTxt
    , g.Latitude AS garden_latitude
    , g.Longitude AS garden_longitude
FROM `ktv_farmer_garden_area` a
JOIN (
    SELECT
        ga.FarmerID, ga.GardenNr, ga.SurveyNr, MAX(Revision) AS Revision
    FROM ktv_farmer_garden_area ga
    JOIN (
        SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
        FROM ktv_farmer_garden_area
        GROUP BY FarmerID, GardenNr
    ) z ON ga.FarmerID = z.FarmerID AND ga.GardenNr = z.GardenNr AND ga.SurveyNr = z.SurveyNr
    WHERE
        ga.Status = 'verified'
    GROUP BY ga.FarmerID, ga.GardenNr, ga.SurveyNr
) z ON z.FarmerID = a.FarmerID AND z.GardenNr = a.GardenNr AND z.SurveyNr = a.SurveyNr
LEFT JOIN ktv_farmer_garden g ON z.FarmerID = g.FarmerID AND z.GardenNr = g.GardenNr AND z.SurveyNr = g.SurveyNr
LEFT JOIN ktv_survey s ON s.SurveyNr = a.SurveyNr
WHERE
  a.FarmerID = ?
  AND a.GardenNr = ?
ORDER BY a.OrderNr
      ";
      $query = $this->db->query($sql, array($farmer, $garden, $survey));
      if ($query->num_rows() > 0) {
          return $query->result_array();
      }
    }

    public function updateGardenPolygon($area, $farmer_id, $garden_nr, $survey_nr)
    {
      $result = false;
      if (is_array($area)) {
        $this->db->trans_start(FALSE);
        // delete old area
        $this->db->where('FarmerID', $farmer_id);
        $this->db->where('GardenNr', $garden_nr);
        $this->db->where('SurveyNr', $survey_nr);
        $this->db->delete('ktv_farmer_garden_area');
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // insert new area
        $no = 1;
        $data = array();
        foreach ($area as $val) {
          $data[] = array(
            'FarmerID'    => $farmer_id,
            'GardenNr'    => $garden_nr,
            'OrderNr'     => $no,
            'Latitude'    => $val[0],
            'Longitude'   => $val[1]
           );
          $no++;
        }
        $this->db->insert_batch('ktv_farmer_garden_area', $data);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $this->db->trans_complete();
        $result = $this->db->trans_status();
      }
      return $result;
    }

    public function updateGardenHaPolygon($area, $farmer_id, $garden_nr, $survey_nr = '')
    {
        $data = array('GardenHaPolygon' => $area);
        $condition = array('FarmerID'=>$farmer_id, 'GardenNr'=>$garden_nr);
        return $this->db->update('ktv_farmer_garden', $data, $condition);
    }

    public function getSurveyDetail($SurveyNr)
    {
        $query = $this->db->get_where('ktv_survey', compact('SurveyNr'), 1);
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getGardenStatusCetak($FarmerID, $SurveyNr){
        /*$sql="SELECT
                a.GardenNr AS GardenNr
                , b.GardenHaUnCertified AS luasGarden
                , a.ActiveStatus AS gardenStatus
                , a.GardenStatus AS gardenStatusNotActive
                , c.Certification
                , a.Commodity
                , a.CommodityHa
            FROM
                ktv_farmer_garden_status a
                LEFT JOIN ktv_farmer_garden b ON
                    a.`FarmerID` = b.`FarmerID`
                    AND a.`GardenNr` = b.`GardenNr`
                    AND b.`SurveyNr` = ?
                LEFT JOIN ktv_certification c ON
                    a.`FarmerID` = c.`FarmerID`
                    AND a.`GardenNr` = c.`GardenNr`
                    AND c.`SurveyNr` = ?
                    AND CURDATE() BETWEEN DATE(c.`CertificationStart`) AND DATE(c.`CertificationEnd`)
            WHERE
                a.`FarmerID` = ?
                AND b.`StatusCode` = 'active'
            ORDER BY a.`GardenNr` ASC";*/
        $sql = "SELECT
                    a.GardenNr,
                    a.GardenHaUnCertified luasGarden,
                    IFNULL(b.ActiveStatus,1) gardenStatus,
                    b.GardenStatus gardenStatusNotActive,
                    c.Certification,
                    b.Commodity,
                    b.CommodityHa
                FROM
                    ktv_farmer_garden a
                    LEFT JOIN ktv_farmer_garden_status b ON a.GardenNr=b.GardenNr AND a.FarmerID=b.FarmerID
                    LEFT JOIN ktv_certification c ON a.FarmerID=c.FarmerID AND a.GardenNr=c.GardenNr AND a.SurveyNr=c.SurveyNr AND c.ExternalDate!='0000-00-00'
                WHERE
                    a.FarmerID = ? AND a.StatusCode='active' AND a.SurveyNr=? AND b.FarmerID IS NOT NULL";
        $query = $this->db->query($sql,array($FarmerID,$SurveyNr));
        return $query->result_array();

    }

    public function getOtherLandCetak($FarmerID){
        $sql="SELECT
                a.Commodity
                , a.GardenHa
            FROM
                ktv_farmer_other_land a
            WHERE
                a.`FarmerID` = ?
                AND a.Commodity != ''
                AND a.StatusCode = 'active'
            ORDER BY a.`DateCreated` ASC";
        $query = $this->db->query($sql,array($FarmerID));
        return $query->result_array();
    }

    public function getAdoptObsForCetak($FarmerID,$SurveyYear,$jenis_form){
        if($jenis_form == "Form Hasil"){
            $displayHasil = 'hasil';
        }else{
            $displayHasil = 'kosong';
        }

        $sql="SELECT
                gar.`FarmerID`,
                gar.`GardenNr`,
                a.`SurveyYear`,
                a.`DateCollection`,
                a.`PlantingMaterial`,
                a.`FarmCondTreeDensity`,
                a.`FarmCondTreeAge`,
                a.`FarmCondTreeHealth`,
                a.`DebilitatingDisease`,
                a.`Pruning`,
                a.`PestDiseaseSanitation`,
                a.`Weeding`,
                a.`Harvesting`,
                a.`ShadeManagement`,
                a.`SoilCondition`,
                a.`OrganicMatter`,
                a.`FertilizerFormulation`,
                a.`FertilizerApplication`,
                a.`CreatedBy`
                , b.`UserRealName` AS Pewawancara
            FROM
                ktv_farmer_garden gar
                LEFT JOIN ktv_adoption_observations a ON
                    gar.`FarmerID` = a.`FarmerID`
                    AND gar.`GardenNr` = a.`GardenNr`
                    AND a.SurveyYear = ?
                    AND a.`StatusCode` = 'active'
                    AND 'hasil' = '$displayHasil'
                LEFT JOIN sys_user b ON
                    a.`CreatedBy` = b.`UserId`
            WHERE
                gar.FarmerID = ?
            GROUP BY gar.`GardenNr`
            ORDER BY gar.`GardenNr` ASC";
        $query = $this->db->query($sql,array($SurveyYear,$FarmerID));
        return $query->result_array();
    }

    public function getListGSP($FarmerID){
        $sql="SELECT
                a.`SurveyNr`
                , b.`SurveyTxt` AS Survey
                , a.InterviewDate
            FROM
                ktv_social a
                INNER JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.ObjID = ?
                AND a.`ObjType` = 'farmer'
                AND a.`StatusCode` = 'active'
            ORDER BY a.`SurveyNr` ASC";
        $query = $this->db->query($sql,array((int) $FarmerID));

        $result['data'] = $query->result_array();
        return $result;
    }

    public function getFormGsp($FarmerID, $SurveyNr){
        $sql="SELECT
                  a.`ObjType`,
                  a.`ObjID` AS gspFarmerID,
                  b.`FarmerName` AS gspFarmerName,
                  a.`SurveyNr` AS gspSurveyNr,
                  a.`InterviewDate` AS gspInterviewDate,
                  a.`Season` AS gspSeason,
                  a.`GenderTimeSpendProductive` AS gspGenderTimeSpendProductive,
                  a.`GenderTimeSpendReproductive` AS gspGenderTimeSpendReproductive,
                  a.`GenderTimeSpendSocial` AS gspGenderTimeSpendSocial,
                  a.`GenderTimeSpendRecreation` AS gspGenderTimeSpendRecreation,
                  a.`GenderLandPreparationMale` AS gspGenderLandPreparationMale,
                  a.`GenderLandPreparationFemale` AS gspGenderLandPreparationFemale,
                  a.`GenderNurseryMale` AS gspGenderNurseryMale,
                  a.`GenderNurseryFemale` AS gspGenderNurseryFemale,
                  a.`GenderPlantingWeedingMale` AS gspGenderPlantingWeedingMale,
                  a.`GenderPlantingWeedingFemale` AS gspGenderPlantingWeedingFemale,
                  a.`GenderPesticidesFertilizingMale` AS gspGenderPesticidesFertilizingMale,
                  a.`GenderPesticidesFertilizingFemale` AS gspGenderPesticidesFertilizingFemale,
                  a.`GenderPruningMale` AS gspGenderPruningMale,
                  a.`GenderPruningFemale` AS gspGenderPruningFemale,
                  a.`GenderHarvestingMale` AS gspGenderHarvestingMale,
                  a.`GenderHarvestingFemale` AS gspGenderHarvestingFemale,
                  a.`GenderTransportingMale` AS gspGenderTransportingMale,
                  a.`GenderTransportingFemale` AS gspGenderTransportingFemale,
                  a.`GenderMakingDecisionMale` AS gspGenderMakingDecisionMale,
                  a.`GenderMakingDecisionFemale` AS gspGenderMakingDecisionFemale,
                  a.`GenderResourceOwnershipMale` AS gspGenderResourceOwnershipMale,
                  a.`GenderAccessResourcesMale` AS gspGenderAccessResourcesMale,
                  a.`GenderAccessResourcesFemale` AS gspGenderAccessResourcesFemale,
                  a.`GenderProductiveDecisionsMale` AS gspGenderProductiveDecisionsMale,
                  a.`GenderProductiveDecisionsFemale` AS gspGenderProductiveDecisionsFemale,
                  a.`GenderHouseholdsExpenditureMale` AS gspGenderHouseholdsExpenditureMale,
                  a.`GenderHouseholdsExpenditureFemale` AS gspGenderHouseholdsExpenditureFemale,
                  a.`GenderFarmingExpenditureMale` AS gspGenderFarmingExpenditureMale,
                  a.`GenderFarmingExpenditureFemale` AS gspGenderFarmingExpenditureFemale,
                  a.`GenderOtherRoleMale` AS gspGenderOtherRoleMale,
                  a.`GenderOtherRoleFemale` AS gspGenderOtherRoleFemale,
                  a.`GenderOtherAreaMale` AS gspGenderOtherAreaMale,
                  a.`GenderOtherAreaFemale` AS gspGenderOtherAreaFemale,
                  a.`GenderWomanParticipate` AS gspGenderWomanParticipate,
                  a.`GenderWomanTakeDecision` AS gspGenderWomanTakeDecision,
                  a.`ChildLabYouthInvolve` AS gspChildLabYouthInvolve,
                  a.`ChildLabYouthInvolveOther` AS gspChildLabYouthInvolveOther,
                  a.`ChildLabCurrentlyworking` AS gspChildLabCurrentlyworking,
                  a.`ChildLabPlanningToWork` AS gspChildLabPlanningToWork,
                  a.`ChildLabWorkMainReason` AS gspChildLabWorkMainReason,
                  a.`ChildLabWorkMainReasonOther` AS gspChildLabWorkMainReasonOther,
                  a.`ChildLabWorkWhatActivity` AS gspChildLabWorkWhatActivity,
                  a.`ChildLabWorkWhatActivityOther` AS gspChildLabWorkWhatActivityOther,
                  a.`ChildLabNotWorkMainReason` AS gspChildLabNotWorkMainReason,
                  a.`ChildLabNotWorkMainReasonOther` AS gspChildLabNotWorkMainReasonOther,
                  a.`ChildLabNotWorkSectorWorkPlan` AS gspChildLabNotWorkSectorWorkPlan,
                  a.`ChildLabNotWorkSectorWorkPlanOther` AS gspChildLabNotWorkSectorWorkPlanOther,
                  a.`ChildLabworkUnderAge` AS gspChildLabworkUnderAge,
                  a.`ChildLabworkUnderAgeOther` AS gspChildLabworkUnderAgeOther,
                  a.`ChildLabworkUnderAgeReason` AS gspChildLabworkUnderAgeReason,
                  a.`ChildLabworkUnderAgeReasonOther` AS gspChildLabworkUnderAgeReasonOther,
                  a.`ComMemberFarmerGroup` AS gspComMemberFarmerGroup,
                  a.`ComMemberFarmerGroupNonCacao` AS gspComMemberFarmerGroupNonCacao,
                  a.`ComFarmerGroupRole` AS gspComFarmerGroupRole,
                  a.`ComFarmerGroupRoleOther` AS gspComFarmerGroupRoleOther,
                  a.`ComParticipateFarmerGroup` AS gspComParticipateFarmerGroup,
                  a.`ComParticipateMusrenbangdes` AS gspComParticipateMusrenbangdes,
                  a.`ComParticipateMajlisTaklim` AS gspComParticipateMajlisTaklim,
                  a.`ComParticipatePosyandu` AS gspComParticipatePosyandu,
                  a.`ComParticipateRole` AS gspComParticipateRole,
                  a.`ComParticipateRolePosition` AS gspComParticipateRolePosition
                FROM
                    `ktv_social`  a
                    LEFT JOIN ktv_farmer b ON a.`ObjID` = b.`FarmerID`
                WHERE
                    a.`ObjID` = ?
                    AND a.`ObjType` = 'farmer'
                    AND a.`SurveyNr` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array((int) $FarmerID, (int) $SurveyNr));

        $return['success'] = true;
        $return['data'] = $query->row_array();
        return $return;
    }

    public function insertGsp($varPost){
        $sql="INSERT INTO `ktv_social` SET
              `ObjType` = 'farmer',
              `ObjID` = ?,
              `SurveyNr` = ?,
              `InterviewDate` = ?,
              `Season` = ?,
              `GenderTimeSpendProductive` = ?,
              `GenderTimeSpendReproductive` = ?,
              `GenderTimeSpendSocial` = ?,
              `GenderTimeSpendRecreation` = ?,
              `GenderLandPreparationMale` = ?,
              `GenderLandPreparationFemale` = ?,
              `GenderNurseryMale` = ?,
              `GenderNurseryFemale` = ?,
              `GenderPlantingWeedingMale` = ?,
              `GenderPlantingWeedingFemale` = ?,
              `GenderPesticidesFertilizingMale` = ?,
              `GenderPesticidesFertilizingFemale` = ?,
              `GenderPruningMale` = ?,
              `GenderPruningFemale` = ?,
              `GenderHarvestingMale` = ?,
              `GenderHarvestingFemale` = ?,
              `GenderTransportingMale` = ?,
              `GenderTransportingFemale` = ?,
              `GenderMakingDecisionMale` = ?,
              `GenderMakingDecisionFemale` = ?,
              `GenderResourceOwnershipMale` = ?,
              `GenderAccessResourcesMale` = ?,
              `GenderAccessResourcesFemale` = ?,
              `GenderProductiveDecisionsMale` = ?,
              `GenderProductiveDecisionsFemale` = ?,
              `GenderHouseholdsExpenditureMale` = ?,
              `GenderHouseholdsExpenditureFemale` = ?,
              `GenderFarmingExpenditureMale` = ?,
              `GenderFarmingExpenditureFemale` = ?,
              `GenderOtherRoleMale` = ?,
              `GenderOtherRoleFemale` = ?,
              `GenderOtherAreaMale` = ?,
              `GenderOtherAreaFemale` = ?,
              `GenderWomanParticipate` = ?,
              `GenderWomanTakeDecision` = ?,
              `ChildLabYouthInvolve` = ?,
              `ChildLabYouthInvolveOther` = ?,
              `ChildLabCurrentlyworking` = ?,
              `ChildLabPlanningToWork` = ?,
              `ChildLabWorkMainReason` = ?,
              `ChildLabWorkMainReasonOther` = ?,
              `ChildLabWorkWhatActivity` = ?,
              `ChildLabWorkWhatActivityOther` = ?,
              `ChildLabNotWorkMainReason` = ?,
              `ChildLabNotWorkMainReasonOther` = ?,
              `ChildLabNotWorkSectorWorkPlan` = ?,
              `ChildLabNotWorkSectorWorkPlanOther` = ?,
              `ChildLabworkUnderAge` = ?,
              `ChildLabworkUnderAgeOther` = ?,
              `ChildLabworkUnderAgeReason` = ?,
              `ChildLabworkUnderAgeReasonOther` = ?,
              `ComMemberFarmerGroup` = ?,
              `ComMemberFarmerGroupNonCacao` = ?,
              `ComFarmerGroupRole` = ?,
              `ComFarmerGroupRoleOther` = ?,
              `ComParticipateFarmerGroup` = ?,
              `ComParticipateMusrenbangdes` = ?,
              `ComParticipateMajlisTaklim` = ?,
              `ComParticipatePosyandu` = ?,
              `ComParticipateRole` = ?,
              `ComParticipateRolePosition` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $varPost['gspFarmerID'],
            $varPost['gspSurveyNr'],
            $varPost['gspInterviewDate'],
            $varPost['gspSeason'],
            $varPost['gspGenderTimeSpendProductive'],
            $varPost['gspGenderTimeSpendReproductive'],
            $varPost['gspGenderTimeSpendSocial'],
            $varPost['gspGenderTimeSpendRecreation'],
            $varPost['gspGenderLandPreparationMale'],
            $varPost['gspGenderLandPreparationFemale'],
            $varPost['gspGenderNurseryMale'],
            $varPost['gspGenderNurseryFemale'],
            $varPost['gspGenderPlantingWeedingMale'],
            $varPost['gspGenderPlantingWeedingFemale'],
            $varPost['gspGenderPesticidesFertilizingMale'],
            $varPost['gspGenderPesticidesFertilizingFemale'],
            $varPost['gspGenderPruningMale'],
            $varPost['gspGenderPruningFemale'],
            $varPost['gspGenderHarvestingMale'],
            $varPost['gspGenderHarvestingFemale'],
            $varPost['gspGenderTransportingMale'],
            $varPost['gspGenderTransportingFemale'],
            $varPost['gspGenderMakingDecisionMale'],
            $varPost['gspGenderMakingDecisionFemale'],
            $varPost['gspGenderResourceOwnershipMale'],
            $varPost['gspGenderAccessResourcesMale'],
            $varPost['gspGenderAccessResourcesFemale'],
            $varPost['gspGenderProductiveDecisionsMale'],
            $varPost['gspGenderProductiveDecisionsFemale'],
            $varPost['gspGenderHouseholdsExpenditureMale'],
            $varPost['gspGenderHouseholdsExpenditureFemale'],
            $varPost['gspGenderFarmingExpenditureMale'],
            $varPost['gspGenderFarmingExpenditureFemale'],
            $varPost['gspGenderOtherRoleMale'],
            $varPost['gspGenderOtherRoleFemale'],
            $varPost['gspGenderOtherAreaMale'],
            $varPost['gspGenderOtherAreaFemale'],
            $varPost['gspGenderWomanParticipate'],
            $varPost['gspGenderWomanTakeDecision'],
            $varPost['gspChildLabYouthInvolve'],
            $varPost['gspChildLabYouthInvolveOther'],
            $varPost['gspChildLabCurrentlyworking'],
            $varPost['gspChildLabPlanningToWork'],
            $varPost['gspChildLabWorkMainReason'],
            $varPost['gspChildLabWorkMainReasonOther'],
            $varPost['gspChildLabWorkWhatActivity'],
            $varPost['gspChildLabWorkWhatActivityOther'],
            $varPost['gspChildLabNotWorkMainReason'],
            $varPost['gspChildLabNotWorkMainReasonOther'],
            $varPost['gspChildLabNotWorkSectorWorkPlan'],
            $varPost['gspChildLabNotWorkSectorWorkPlanOther'],
            $varPost['gspChildLabworkUnderAge'],
            $varPost['gspChildLabworkUnderAgeOther'],
            $varPost['gspChildLabworkUnderAgeReason'],
            $varPost['gspChildLabworkUnderAgeReasonOther'],
            $varPost['gspComMemberFarmerGroup'],
            $varPost['gspComMemberFarmerGroupNonCacao'],
            $varPost['gspComFarmerGroupRole'],
            $varPost['gspComFarmerGroupRoleOther'],
            $varPost['gspComParticipateFarmerGroup'],
            $varPost['gspComParticipateMusrenbangdes'],
            $varPost['gspComParticipateMajlisTaklim'],
            $varPost['gspComParticipatePosyandu'],
            $varPost['gspComParticipateRole'],
            $varPost['gspComParticipateRolePosition'],
            $varPost['userid']
        );
        $query = $this->db->query($sql,$p);

        if($query){
            $return['success'] = true;
        }else{
            $return['success'] = false;
            $return['message'] = "Insert Failed!";
        }
        return $return;
    }

    public function updateGsp($varPost){
        $sql="UPDATE `ktv_social` SET
              `InterviewDate` = ?,
              `Season` = ?,
              `GenderTimeSpendProductive` = ?,
              `GenderTimeSpendReproductive` = ?,
              `GenderTimeSpendSocial` = ?,
              `GenderTimeSpendRecreation` = ?,
              `GenderLandPreparationMale` = ?,
              `GenderLandPreparationFemale` = ?,
              `GenderNurseryMale` = ?,
              `GenderNurseryFemale` = ?,
              `GenderPlantingWeedingMale` = ?,
              `GenderPlantingWeedingFemale` = ?,
              `GenderPesticidesFertilizingMale` = ?,
              `GenderPesticidesFertilizingFemale` = ?,
              `GenderPruningMale` = ?,
              `GenderPruningFemale` = ?,
              `GenderHarvestingMale` = ?,
              `GenderHarvestingFemale` = ?,
              `GenderTransportingMale` = ?,
              `GenderTransportingFemale` = ?,
              `GenderMakingDecisionMale` = ?,
              `GenderMakingDecisionFemale` = ?,
              `GenderResourceOwnershipMale` = ?,
              `GenderAccessResourcesMale` = ?,
              `GenderAccessResourcesFemale` = ?,
              `GenderProductiveDecisionsMale` = ?,
              `GenderProductiveDecisionsFemale` = ?,
              `GenderHouseholdsExpenditureMale` = ?,
              `GenderHouseholdsExpenditureFemale` = ?,
              `GenderFarmingExpenditureMale` = ?,
              `GenderFarmingExpenditureFemale` = ?,
              `GenderOtherRoleMale` = ?,
              `GenderOtherRoleFemale` = ?,
              `GenderOtherAreaMale` = ?,
              `GenderOtherAreaFemale` = ?,
              `GenderWomanParticipate` = ?,
              `GenderWomanTakeDecision` = ?,
              `ChildLabYouthInvolve` = ?,
              `ChildLabYouthInvolveOther` = ?,
              `ChildLabCurrentlyworking` = ?,
              `ChildLabPlanningToWork` = ?,
              `ChildLabWorkMainReason` = ?,
              `ChildLabWorkMainReasonOther` = ?,
              `ChildLabWorkWhatActivity` = ?,
              `ChildLabWorkWhatActivityOther` = ?,
              `ChildLabNotWorkMainReason` = ?,
              `ChildLabNotWorkMainReasonOther` = ?,
              `ChildLabNotWorkSectorWorkPlan` = ?,
              `ChildLabNotWorkSectorWorkPlanOther` = ?,
              `ChildLabworkUnderAge` = ?,
              `ChildLabworkUnderAgeOther` = ?,
              `ChildLabworkUnderAgeReason` = ?,
              `ChildLabworkUnderAgeReasonOther` = ?,
              `ComMemberFarmerGroup` = ?,
              `ComMemberFarmerGroupNonCacao` = ?,
              `ComFarmerGroupRole` = ?,
              `ComFarmerGroupRoleOther` = ?,
              `ComParticipateFarmerGroup` = ?,
              `ComParticipateMusrenbangdes` = ?,
              `ComParticipateMajlisTaklim` = ?,
              `ComParticipatePosyandu` = ?,
              `ComParticipateRole` = ?,
              `ComParticipateRolePosition` = ?,
              `DateUpdated` = NOW(),
              `LastModifiedBy` = ?
            WHERE
                `ObjType` = 'farmer'
                AND `ObjID` = ?
                AND `SurveyNr` = ?
            LIMIT 1";
        $p = array(
            $varPost['gspInterviewDate'],
            $varPost['gspSeason'],
            $varPost['gspGenderTimeSpendProductive'],
            $varPost['gspGenderTimeSpendReproductive'],
            $varPost['gspGenderTimeSpendSocial'],
            $varPost['gspGenderTimeSpendRecreation'],
            $varPost['gspGenderLandPreparationMale'],
            $varPost['gspGenderLandPreparationFemale'],
            $varPost['gspGenderNurseryMale'],
            $varPost['gspGenderNurseryFemale'],
            $varPost['gspGenderPlantingWeedingMale'],
            $varPost['gspGenderPlantingWeedingFemale'],
            $varPost['gspGenderPesticidesFertilizingMale'],
            $varPost['gspGenderPesticidesFertilizingFemale'],
            $varPost['gspGenderPruningMale'],
            $varPost['gspGenderPruningFemale'],
            $varPost['gspGenderHarvestingMale'],
            $varPost['gspGenderHarvestingFemale'],
            $varPost['gspGenderTransportingMale'],
            $varPost['gspGenderTransportingFemale'],
            $varPost['gspGenderMakingDecisionMale'],
            $varPost['gspGenderMakingDecisionFemale'],
            $varPost['gspGenderResourceOwnershipMale'],
            $varPost['gspGenderAccessResourcesMale'],
            $varPost['gspGenderAccessResourcesFemale'],
            $varPost['gspGenderProductiveDecisionsMale'],
            $varPost['gspGenderProductiveDecisionsFemale'],
            $varPost['gspGenderHouseholdsExpenditureMale'],
            $varPost['gspGenderHouseholdsExpenditureFemale'],
            $varPost['gspGenderFarmingExpenditureMale'],
            $varPost['gspGenderFarmingExpenditureFemale'],
            $varPost['gspGenderOtherRoleMale'],
            $varPost['gspGenderOtherRoleFemale'],
            $varPost['gspGenderOtherAreaMale'],
            $varPost['gspGenderOtherAreaFemale'],
            $varPost['gspGenderWomanParticipate'],
            $varPost['gspGenderWomanTakeDecision'],
            $varPost['gspChildLabYouthInvolve'],
            $varPost['gspChildLabYouthInvolveOther'],
            $varPost['gspChildLabCurrentlyworking'],
            $varPost['gspChildLabPlanningToWork'],
            $varPost['gspChildLabWorkMainReason'],
            $varPost['gspChildLabWorkMainReasonOther'],
            $varPost['gspChildLabWorkWhatActivity'],
            $varPost['gspChildLabWorkWhatActivityOther'],
            $varPost['gspChildLabNotWorkMainReason'],
            $varPost['gspChildLabNotWorkMainReasonOther'],
            $varPost['gspChildLabNotWorkSectorWorkPlan'],
            $varPost['gspChildLabNotWorkSectorWorkPlanOther'],
            $varPost['gspChildLabworkUnderAge'],
            $varPost['gspChildLabworkUnderAgeOther'],
            $varPost['gspChildLabworkUnderAgeReason'],
            $varPost['gspChildLabworkUnderAgeReasonOther'],
            $varPost['gspComMemberFarmerGroup'],
            $varPost['gspComMemberFarmerGroupNonCacao'],
            $varPost['gspComFarmerGroupRole'],
            $varPost['gspComFarmerGroupRoleOther'],
            $varPost['gspComParticipateFarmerGroup'],
            $varPost['gspComParticipateMusrenbangdes'],
            $varPost['gspComParticipateMajlisTaklim'],
            $varPost['gspComParticipatePosyandu'],
            $varPost['gspComParticipateRole'],
            $varPost['gspComParticipateRolePosition'],
            $varPost['userid'],
            $varPost['gspFarmerID'],
            $varPost['gspSurveyNr']
        );
        $query = $this->db->query($sql,$p);

        if($query){
            $return['success'] = true;
        }else{
            $return['success'] = false;
            $return['message'] = "Update Failed!";
        }
        return $return;
    }

    public function deleteGsp($FarmerID, $SurveyNr){
        $this->db->trans_start();

        $sql="
            INSERT INTO `his_ktv_social` (
              `DateHistory`,
              `DeleteBy`,
              `ObjType`,
              `ObjID`,
              `SurveyNr`,
              `InterviewDate`,
              `Season`,
              `GenderTimeSpendProductive`,
              `GenderTimeSpendReproductive`,
              `GenderTimeSpendSocial`,
              `GenderTimeSpendRecreation`,
              `GenderLandPreparationMale`,
              `GenderLandPreparationFemale`,
              `GenderNurseryMale`,
              `GenderNurseryFemale`,
              `GenderPlantingWeedingMale`,
              `GenderPlantingWeedingFemale`,
              `GenderPesticidesFertilizingMale`,
              `GenderPesticidesFertilizingFemale`,
              `GenderPruningMale`,
              `GenderPruningFemale`,
              `GenderHarvestingMale`,
              `GenderHarvestingFemale`,
              `GenderTransportingMale`,
              `GenderTransportingFemale`,
              `GenderMakingDecisionMale`,
              `GenderMakingDecisionFemale`,
              `GenderResourceOwnershipMale`,
              `GenderAccessResourcesMale`,
              `GenderAccessResourcesFemale`,
              `GenderProductiveDecisionsMale`,
              `GenderProductiveDecisionsFemale`,
              `GenderHouseholdsExpenditureMale`,
              `GenderHouseholdsExpenditureFemale`,
              `GenderFarmingExpenditureMale`,
              `GenderFarmingExpenditureFemale`,
              `GenderOtherRoleMale`,
              `GenderOtherRoleFemale`,
              `GenderOtherAreaMale`,
              `GenderOtherAreaFemale`,
              `GenderWomanParticipate`,
              `GenderWomanTakeDecision`,
              `ChildLabYouthInvolve`,
              `ChildLabYouthInvolveOther`,
              `ChildLabCurrentlyworking`,
              `ChildLabPlanningToWork`,
              `ChildLabWorkMainReason`,
              `ChildLabWorkMainReasonOther`,
              `ChildLabWorkWhatActivity`,
              `ChildLabWorkWhatActivityOther`,
              `ChildLabNotWorkMainReason`,
              `ChildLabNotWorkMainReasonOther`,
              `ChildLabNotWorkSectorWorkPlan`,
              `ChildLabNotWorkSectorWorkPlanOther`,
              `ChildLabworkUnderAge`,
              `ChildLabworkUnderAgeOther`,
              `ChildLabworkUnderAgeReason`,
              `ChildLabworkUnderAgeReasonOther`,
              `ComMemberFarmerGroup`,
              `ComMemberFarmerGroupNonCacao`,
              `ComFarmerGroupRole`,
              `ComFarmerGroupRoleOther`,
              `ComParticipateFarmerGroup`,
              `ComParticipateMusrenbangdes`,
              `ComParticipateMajlisTaklim`,
              `ComParticipatePosyandu`,
              `ComParticipateRole`,
              `ComParticipateRolePosition`,
              `DateCreated`,
              `CreatedBy`,
              `DateUpdated`,
              `LastModifiedBy`,
              `DateSynced`,
              `StatusCode`,
              `uid`
            )
            SELECT
                NOW(), ?, a.*
            FROM
                ktv_social a
            WHERE
                a.ObjType = 'farmer'
                AND a.ObjID = ?
                AND a.SurveyNr = ?
            LIMIT 1
        ";
        $proses = $this->db->query($sql, array($_SESSION['userid'],$FarmerID, $SurveyNr));

        $sql = "DELETE FROM ktv_social WHERE ObjType = 'farmer' AND ObjID = ? AND SurveyNr = ? LIMIT 1";
        $proses = $this->db->query($sql, array($FarmerID, $SurveyNr));

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

    public function getLatestSurveyNutrition($FarmerID){
        $sql="SELECT
                a.`SurveyNr`
            FROM
                ktv_nutrition a
            WHERE
                a.`FarmerID` = '111200009'
                AND a.`StatusCode` = 'active'
            ORDER BY a.`SurveyNr` DESC
            LIMIT 1";
        $query = $this->db->query($sql,array($FarmerID));
        $data = $query->row_array();
        return $data['SurveyNr'];
    }

    public function getLatestSurveyPPI($FarmerID){
        $sql="SELECT
                a.`SurveyNr`
            FROM
                ktv_ppiscore2012 a
            WHERE
                a.`FarmerID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.`SurveyNr` DESC
            LIMIT 1";
        $query = $this->db->query($sql,array($FarmerID));
        $data = $query->row_array();
        return $data['SurveyNr'];
    }

    public function getDetailGarden($FarmerID, $SurveyNr){
        $sql = "SELECT a.*, b.*, audit.*, b.CandidateSelection, audit.ICSDate, b.ExternalDate, b.CertificationStart, b.CertificationEnd, b.Year, b.Certification, b.CertificationHolderJenis, e.label AS CertificationHolderName, audit.StatusAudit StatusAuditCertification,
                    audit.DateRevisionAudit, audit.CommentAudit, audit.RecommendationAudit, c.PersonNm Inspector, u.UserRealName, ph.*,
                    a.FarmerID,a.`GardenNr`,a.`SurveyNr`
                FROM
                    ktv_farmer_garden a
                    LEFT JOIN ktv_certification b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND b.Certification!=0#AND b.ExternalDate!='0000-00-00'
                    LEFT JOIN(
                        SELECT *
                         FROM
                             (SELECT concat('',WarehouseID) id,
                                     concat('',WarehouseName) label,
                                     VillageID village
                              FROM ktv_warehouse
                              UNION ALL SELECT concat('',TraderID) id,
                                               concat('',Company) label,
                                               VillageID village
                              FROM ktv_traders
                              UNION ALL SELECT concat('',CoopID) id,
                                               concat('',CoopName) label,
                                               VillageID village
                              FROM ktv_cooperatives) a
                    ) e ON b.CertificationHolder = e.id
                    LEFT JOIN (
                        SELECT au.*
                        FROM
                            (SELECT FarmerID, GardenNr, SurveyNr, Certification, MAX(ICSDate) ICSDate FROM ktv_certification_audit_log WHERE Certification!=0 AND FarmerID=? AND SurveyNr=? GROUP BY FarmerID, GardenNr, SurveyNr) dt
                            LEFT JOIN ktv_certification_audit_log au ON au.FarmerID=dt.FarmerID AND au.GardenNr=dt.GardenNr AND au.SurveyNr=dt.SurveyNr AND au.ICSDate=dt.ICSDate AND au.Certification=dt.Certification
                    ) audit ON audit.FarmerID=a.FarmerID AND audit.GardenNr=a.GardenNr AND audit.SurveyNr=a.SurveyNr
                    LEFT JOIN (
                        SELECT dt.staffid id, b.PersonNm
                        FROM
                            (SELECT ExtensionID AS staffid, PersonID FROM ktv_extension_staff UNION ALL SELECT StaffID, PersonID FROM ktv_program_staff a) dt
                            LEFT JOIN ktv_persons b ON dt.PersonID=b.PersonID
                    ) c ON audit.InspectorID = c.id
                    LEFT JOIN sys_user u ON u.UserId=a.CreatedBy
                    LEFT JOIN ktv_farmer_post_harvest ph ON ph.FarmerID=a.FarmerID AND ph.SurveyNr=a.SurveyNr
                WHERE a.FarmerID=? AND a.SurveyNr=? AND a.StatusCode='active'";
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr, $FarmerID, $SurveyNr));
        if($query->num_rows() > 0){
            $return = $query->result_array();
        }else{
            $return[0] = array();
        }
        return $return;
    }

    public function getLatestSurveyFinance($FarmerID)
    {
        $sql = "
SELECT
    MAX(kff.SurveyNr) AS SurveyNr
FROM
    ktv_farmer_financial kff
WHERE
    kff.FarmerID = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows()>0) {
            $row = $query->row_array(0);
            return $row['SurveyNr'];
        }
        return false;
    }

    public function getMemberP1P2($MemberID){
        $sql = "SELECT *
                FROM
                    ktv_members a
                    LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                    LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                    LEFT JOIN ktv_village d ON a.VillageID = d.VillageID
                    LEFT JOIN ktv_subdistrict e ON e.SubDistrictID = d.SubDistrictID
                    LEFT JOIN ktv_district f ON f.DistrictID = e.DistrictID
                    LEFT JOIN ktv_province g ON g.ProvinceID = f.ProvinceID
                    LEFT JOIN (
                        SELECT
                            suba.`MemberID`
                            , SUM(suba.GardenAreaHa) AS TotalHectare
                        FROM
                            ktv_survey_plot suba
                            JOIN (SELECT
                                        p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                    FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                    GROUP BY p.MemberID, p.PlotNr) suba_lat
                                        ON suba.MemberID = suba_lat.MemberID 
                                        AND suba.PlotNr = suba_lat.PlotNr 
                                        AND suba.SurveyNr = suba_lat.SurveyNr
                        WHERE
                            suba.`MemberID` = ?
                    ) AS igar ON 1=1
                        AND a.`MemberID` = igar.MemberID
                WHERE
                    a.`MemberID` = ?
                GROUP BY a.`MemberID`
                LIMIT 1";
        $query = $this->db->query($sql, array((int) $MemberID, (int) $MemberID));
        $data = $query->row();

        return $data;
    }

    public function getFamilyP1P2($MemberID) {
        $sql = "SELECT
                *
                , CASE
                    WHEN a.FamLabRelation = '1' THEN '" . lang('Spouse') . "'
                    WHEN a.FamLabRelation = '2' THEN '" . lang('Child') . "'
                    WHEN a.FamLabRelation = '3' THEN '" . lang('Worker') . "'
                    WHEN a.FamLabRelation = '4' THEN '" . lang('Other') . "'
                END AS FamLabRelation
                , CASE
                    WHEN a.ReasonFamilyWork = '1' THEN '" . lang('Not going to scholl') . "'
                    WHEN a.ReasonFamilyWork = '2' THEN '" . lang('Lack of labour') . "'
                    WHEN a.ReasonFamilyWork = '3' THEN '" . lang('Helping parents') . "'
                    WHEN a.ReasonFamilyWork = '4' THEN '" . lang('I dont have to pay them') . "'
                    WHEN a.ReasonFamilyWork = '5' THEN '" . lang('Lainnya') . "'
                END AS ReasonFamilyWork
                , YEAR(CURDATE()) - a.`YearOfBirth` AS Age
                , CASE
                    WHEN a.Gender = 'm' THEN '" . lang('Male') . "'
                    WHEN a.Gender = 'f' THEN '" . lang('Female') . "'
                END AS Gender
            FROM
                `ktv_member_family_labour` a
            WHERE
                a.`MemberID` = ?
                AND a.StatusCode = 'active'
            ORDER BY a.`FamLabName` ASC, a.`FamLabName` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();

        return $data;
    }

    public function getLabourP1P2($MemberID){
        $this->db->select("
                *,
                YEAR(CURDATE()) - YearOfBirth AS Age
            ");
        $this->db->from('ktv_member_labour');
        $this->db->where('MemberID', $MemberID);
        $this->db->where('StatusCode', 'active');

        return $this->db->get()->result_array();
    }

    public function getOtherLandP1P2($MemberID){
        $sql = "SELECT
                *,
                CASE
                    WHEN commodity = '1' THEN '" . lang('Jagung') . "'
                    WHEN commodity = '2' THEN '" . lang('Padi') . "'
                    WHEN commodity = '3' THEN '" . lang('Cengkeh') . "'
                    WHEN commodity = '4' THEN '" . lang('Karet') . "'
                    WHEN commodity = '5' THEN '" . lang('Cocoa') . "'
                    WHEN commodity = '6' THEN '" . lang('Buah') . "'
                    WHEN commodity = '7' THEN '" . lang('Kayu') . "'
                    WHEN commodity = '8' THEN '" . lang('Other') . "'
                END AS Commodity
                FROM ktv_member_other_land
                WHERE
                    StatusCode = 'active'
                    AND
                    MemberID = ?
                ";
        $query = $this->db->query($sql, array((int) $MemberID));
        return $query->result_array();
    }

    public function getMemberExtensionP1P2($MemberID){
        $this->db->from('ktv_members_extension');
        $this->db->where('MemberID', $MemberID);

        return $this->db->get()->row();
    }

    public function getSurveyHouseholdP1P2($MemberID){
        $this->db->from('ktv_survey_household');
        $this->db->where('MemberID', $MemberID);
        $this->db->order_by('SurveyNr', 'asc');

        return $this->db->get();
    }

    public function getSurveyPlotP1P2($MemberID){
        // $this->db->select('SurveyNr');
        // $this->db->from('ktv_survey_plot');
        // $this->db->where('MemberID', $MemberID);
        // $this->db->order_by('SurveyNr', 'asc');
        // $this->db->limit(1);
        // $lastSurvey = $this->db->get()->row();

        $this->db->select('*');
        $this->db->from('ktv_survey_plot a');
        $this->db->join('ktv_village b', 'b.VillageID = a.VillageID', 'left');
        $this->db->join('ktv_subdistrict c', 'c.SubDistrictID = b.SubDistrictID', 'left');
        $this->db->join('ktv_district d', 'd.DistrictID = c.DistrictID', 'left');
        $this->db->join('ktv_province e', 'e.ProvinceID = d.ProvinceID', 'left');
        $this->db->where('MemberID', $MemberID);
        // $this->db->where('SurveyNr', $lastSurvey->SurveyNr);
        $this->db->order_by('plotNr', 'asc');

        return $this->db->get();
    }

    public function getMemberID($MemberID){
        $sql    = "SELECT 
                MemberID 
            FROM ktv_members WHERE 
                (MemberID = ? 
                OR
                    MemberDisplayID = ?) 
                AND MemberID != 0";
        $query  = $this->db->query($sql, array($MemberID, $MemberID))->row_array();

        return $query["MemberID"];
    }

    public function GetTrainingMainGrid($MemberID) {
        $return = array();

        // $sql = "SELECT 
        //             tf.TrainFarmerID
        //             , tf.BatchID
        //             , bat.BatchName
        //             , top.Topic
        //             , CONCAT(tf.`TrainStart`,' ".lang('to')." ',tf.`TrainEnd`) AS TrainDate
        //             , tf.ActivityType
        //             , tf.IsCert
        //             , tf.TrainingEventStatus
        //         FROM ktv_training_farmer tf
        //         INNER JOIN ktv_batch bat ON tf.`BatchID` = bat.`BatchID`
        //         INNER JOIN ktv_partner p ON bat.`PartnerID` = p.`PartnerID`
        //         INNER JOIN ktv_training_topic top ON tf.`TopicID` = top.`TopicID`
        //         LEFT JOIN ktv_training_farmer_participants tfp ON tfp.TrainFarmerID = tf.TrainFarmerID
        //         WHERE
        //             tfp.SupplierID = ?
        //         ORDER BY tf.TrainFarmerID ASC";
        // $data = $this->db->query($sql,array($SupplierID))->result_array();

        $sql  = "SELECT
                    c.`CpgTrainingsID`
                    , c.`CpgTrainings`
                    , c.`CpgAbbre`
                    , date(b.`TrainingStart`) as TrainingStart
                    , date(b.`TrainingEnd`) as TrainingEnd
                    , b.`TrainingDays`
                    , GROUP_CONCAT(st.CpgTrainings) AS sub_topic
                    , i.BatchNumber
                    , CASE 
                        WHEN b.TrainingStatus = 1 THEN 'Complete'
                        WHEN b.TrainingStatus = 2 THEN 'On Going'
                        ELSE '-'
                       END TrainingStatus
                FROM
                    ktv_farmer_trainings_participants a
                    INNER JOIN ktv_farmer_trainings b ON a.FarmerTrainingID = b.`FarmerTrainingID`
                    LEFT JOIN ktv_cpg_batch i ON b.CpgBatchID = i.CpgBatchID
                    INNER JOIN ktv_cpg_trainings c ON b.`CPGtrainingsID` = c.`CpgTrainingsID`
                    LEFT JOIN (
                        SELECT
                            st.FarmerTrainingID,
                            t.CpgTrainingsID,
                            t.CpgTrainings
                        FROM ktv_farmer_trainings_sub_topics st
                        JOIN ktv_cpg_trainings t ON t.CPGtrainingsID = st.SubCpgTrainingsID
                    ) st ON st.FarmerTrainingID = b.FarmerTrainingID AND st.CpgTrainingsID != c.CPGtrainingsID
                WHERE
                    a.`FarmerID` = ?
                    AND a.`StatusCode` = 'active'
                    AND b.`StatusCode` = 'active'
                ORDER BY b.`TrainingEnd` DESC";

        $data = $this->db->query($sql, array($MemberID))->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }
    
    public function GetCoachingMainGrid($MemberID) {
        $return = array();

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    IFNULL(fca.FarmerWorkerName, s.MemberName) CoachingRecipientName
                    , IF(fca.CoachingRecipient = 1, 'Registered Farmer',
                            IF(fca.CoachingRecipient = 2, 'Farmer Worker',
                                    IF(fca.CoachingRecipient = 3, 'Household Member', '-')
                                    )
                            ) AS CoachingRecipient
                    , fca.EventDate CoachingDate
                    , fca.TimeStart
                    , fca.TimeEnd
                FROM
                    ktv_ims_farmer_coaching fc
                LEFT JOIN
                    ktv_ims_farmer_coaching_activity fca on fca.CoachingID = fc.CoachingID
                INNER JOIN 
                    ktv_members s ON fc.FarmerID = s.`MemberID`
                LEFT JOIN
                    ktv_farmer_group fg on fg.FarmerGroupID = s.FarmerGroupID
                INNER JOIN 
                    sys_user u ON u.UserId = fc.UserID
                INNER JOIN
                    ktv_persons p on p.UserID = u.UserID
                WHERE 1=1
                    AND fc.StatusCode = 'active'
                    AND fc.FarmerID = ?
                GROUP BY
                    fc.CoachingID
                ORDER BY fca.EventDate DESC";
        $data = $this->db->query($sql, array($MemberID))->result_array();
        
        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

}