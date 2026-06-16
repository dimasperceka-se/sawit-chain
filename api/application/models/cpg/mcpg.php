<?php

class Mcpg extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }

    function readRegionIDs($char, $start, $limit) {
        $sql = "
            select %s
            from ktv_village w
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID
            WHERE concat(Province,' ',ifnull(District,''),' ',ifnull(SubDistrict,''),' ',ifnull(Village,'')) like ?
            ORDER BY Province %s";
        $query = $this->db->query(sprintf($sql, "VillageId as id,
            concat(Province,', ',ifnull(District,''),', ',ifnull(SubDistrict,''),', ',ifnull(Village,'')) as label", 'LIMIT ?,?'), array("%$char%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array("%$char%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function getBatchTrainingCombo() {
        $sql = "SELECT
                    cpg.CpgBatchID AS id,
                    CONCAT(cpg.BatchNumber,' - ',prog.`PartnerName`) AS label
                FROM
                    ktv_cpg_batch cpg
                    LEFT JOIN ktv_program_partner prog ON cpg.PartnerID = prog.PartnerID
                WHERE
                    cpg.StatusCode = 'active'
                ORDER BY cpg.CpgBatchID DESC";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readCpgAdvancedSearch($param) {
        $sort = json_decode($sort);
        $order = ($sort[0]->property == '' ? 'GroupName' : $sort[0]->property);
        $by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);

        if ($param['parAdvDistrict'] == "not_set" || $param['parAdvDistrict'] == "") {
            //jika kab kosong maka harusnya tampilkan data sesuai hak akses distrct user tsb
            $rangeHakAksesDistrictId = generateHakAksesDistrictId($_SESSION['daerah'], $param['prov']);
            if ($_SESSION['userid'] != "1") {
                $sqlWhereDistrict = " AND y.DistrictID IN (" . implode(",", $rangeHakAksesDistrictId) . ") ";
            } else {
                $sqlWhereDistrict = "";
            }
        } else {
            $paramTemp = str_replace("::", ",", $param['parAdvDistrict']);
            $sqlWhereDistrict = " AND y.DistrictID IN ($paramTemp) ";
        }

        if ($param['parAdvBatch'] == "not_set" || $param['parAdvBatch'] == "") {
            $sqlWhereBatch = "";
        } else {
            $sqlWhereBatch = " AND b.`CpgBatchID` = " . $param['parAdvBatch'];
        }

        if ($param['parAdvNursery'] == "not_set" || $param['parAdvNursery'] == "") {
            $sqlJoinNursery = "";
            $sqlWhereNursery = "";
        } else {
            $sqlJoinNursery = "LEFT JOIN ktv_nursery nur ON a.`CPGid` = nur.`ObjID` AND nur.`ObjType` = 'cpg'";
            if ($param['parAdvNursery'] == "1") {
                $sqlWhereNursery = "AND nur.`NurseryID` IS NOT NULL";
            } else {
                $sqlWhereNursery = "AND nur.`NurseryID` IS NULL";
            }
        }

        if ($param['parAdvNamaId'] == "not_set")
            $param['parAdvNamaId'] = "";

        if ($param['no_limit'] == "yes") {
            $sqlLimit = "";
        } else {
            $sqlLimit = "LIMIT " . $param['start'] . "," . $param['limit'];
        }
        $where = '';
        if ($param['prov']) {
            $where .= " AND y.ProvinceID = {$param['prov']}";
        }
        if ($param['kab']) {
            $where .= " AND y.District = '{$param['kab']}'";
        }
        if ($param['subdist']) {
            $where .= " AND x.SubDistrictID = {$param['subdist']}";
        }

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                a.CPGid AS id
                ,a.OldCPGid
                ,GroupName
                ,a.Address
                ,a.VillageID AS RegionID
                ,TahunTerbentuk
                ,c.`BatchNumber`
                ,d.`PartnerName`
                ,CONCAT (
                    IFNULL(SubDistrict, '')
                    ,', '
                    ,IFNULL(Village, '')
                    ) AS RegionName
                ,COUNT(DISTINCT kcf.FarmerID) AS Anggota
                ,a.Latitude
                ,a.Longitude
                ,a.Elevation
                ,a.STATUS
                ,CONCAT (
                    SUM(totalLandSize)
                    ,' Ha'
                    ) AS totalLandSize
                ,COUNT(totalGarden) AS totalGarden
            FROM
                ktv_cpg a
                LEFT JOIN `ktv_cpg_batch_trainings` b ON a.`CPGid` = b.`CPGid`
                LEFT JOIN `ktv_cpg_batch` c ON b.`CpgBatchID` = c.`CpgBatchID`
                LEFT JOIN `ktv_program_partner` d ON c.`PartnerID` = d.`PartnerID`
                LEFT JOIN ktv_village w ON a.VillageID = w.VillageID
                LEFT JOIN ktv_subdistrict x ON w.SubDistrictID = x.SubDistrictID
                LEFT JOIN ktv_district y ON x.DistrictID = y.DistrictID
                LEFT JOIN ktv_province z ON y.ProvinceID = z.ProvinceID
                LEFT JOIN ktv_farmer kcf ON kcf.CPGid = a.CPGid
                $sqlJoinNursery
            LEFT JOIN (
                SELECT FarmerID
                    ,SUM(GardenHaUnCertified) AS totalLandSize
                    ,COUNT(GardenNr) AS totalGarden
                FROM (
                    SELECT FarmerID
                        ,GardenNr
                        ,MAX(SurveyNr) AS SurveyNr
                        ,GardenHaUnCertified
                    FROM ktv_farmer_garden
                    GROUP BY FarmerID
                        ,GardenNr
                    ) AS temp_sum_garden_survey
                GROUP BY FarmerID
                ORDER BY FarmerID
                ) AS tbl_info_garden ON kcf.`FarmerID` = tbl_info_garden.FarmerID
            WHERE
                1 = 1
                $where
                $sqlWhereDistrict
                AND (
                    GroupName LIKE '%" . $param['parAdvNamaId'] . "%'
                    OR a.CPGid = '" . $param['parAdvNamaId'] . "'
                    OR a.OldCPGid = '" . $param['parAdvNamaId'] . "'
                    )
                $sqlWhereBatch
                $sqlWhereNursery
                AND a.STATUS != 'nullified'
            GROUP BY a.CPGid
            ORDER BY $order $by
            $sqlLimit";
        $p = array(
            $param['prov']
        );
        //echo '<pre>'; print_r($sql); exit;
        $query = $this->db->query($sql, $p);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    function readCpgs($prov, $kab, $key, $userId, $partnerId, $flagAccess, $sort, $start, $limit, $SubDistrictID = '') {
        $sort = json_decode($sort);
        $order = ($sort[0]->property == '' ? 'GroupName' : $sort[0]->property);
        $by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);

        // if ($flagAccess == 0) {
        // if (TRUE) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS
            a.CPGid as id,a.OldCPGid,GroupName,a.Address,a.VillageID as RegionID,TahunTerbentuk, c.`BatchNumber`, d.`PartnerName`,
            Village as RegionName,
            Anggota,
            a.Latitude,a.Longitude,a.Elevation,a.Status,totalLandSize AS totalLandSize,totalGarden AS totalGarden
            from (
                SELECT
                    a.*
                    ,COUNT(DISTINCT kcf.FarmerID) as Anggota
                    ,SUM(totalGarden) AS totalGarden
                    ,SUM(totalLandSize) AS totalLandSize
                FROM ktv_cpg a
                left join (
                    SELECT
                        kcf.FarmerID, kcf.CPGid, totalGarden, totalLandSize
                    FROM ktv_farmer kcf
                    LEFT JOIN (
                    SELECT
                        g.FarmerID,
                        COUNT(g.FarmerID) AS totalGarden,
                        SUM(g.GardenHaUnCertified) AS totalLandSize
                    FROM ktv_farmer_garden g
                    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
                    GROUP BY FarmerID
                    ) AS g ON kcf.`FarmerID` = g.FarmerID
                    WHERE
                        kcf.StatusCode = 'active'
                ) kcf on kcf.CPGid=a.CPGid
                GROUP BY a.CPGid
            ) a

            LEFT JOIN `ktv_cpg_batch_trainings` b ON a.`CPGid`=b.`CPGid` AND b.CPGtrainingsID = 1
            LEFT JOIN `ktv_cpg_batch` c ON b.`CpgBatchID`=c.`CpgBatchID`
            LEFT JOIN `ktv_program_partner` d ON c.`PartnerID`=d.`PartnerID`
            left join ktv_village w on a.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID

            WHERE
                1 = 1
                AND (GroupName like ? OR a.CPGid=? OR a.OldCPGid=?) AND a.Status != 'nullified'
            %s";
            // echo '<pre>'; print_r($_SESSION); echo '</pre>';
            $where = '';
            // if ($_SESSION['is_admin'] != 1) {
            //     $where .= " AND y.DistrictID IN (SELECT DistrictID FROM ktv_access_staff WHERE UserId = {$_SESSION['userid']})";
            // }
            if (!empty($prov)) {
                $where .= " AND y.ProvinceID = {$prov}";
            }
            if (!empty($kab)) {
                $where .= " AND y.District = '{$kab}'";
            }
            if (!empty($kec)) {
                $where .= " AND x.SubDistrictID = {$kec}";
            }
            if (!empty($this->user['district_access'])) {
                $where .= " AND y.DistrictID IN ({$this->user['district_access']})";
            }
            if ($_SESSION['FlagAccess'] == 1) {
                $where .= " AND a.CPGid IN (SELECT CPGid FROM ktv_cpg_partner WHERE PartnerID = {$_SESSION['PartnerID']})";
            }
            $query = $this->db->query(sprintf($sql, $where.' GROUP BY a.CPGid ORDER BY ' . $order . ' ' . $by . ' LIMIT ?,?'), array("%$key%", $key, $key, (int) $start, (int) $limit));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            // $queryTotal = $this->db->query(sprintf($sql, 'count(distinct a.CPGid) as total', $where.''), array($prov, $prov, $kab, $kab, $SubDistrictID, $SubDistrictID, "%$key%", $key, $key));
            $queryTotal = $this->db->query('SELECT FOUND_ROWS() AS total');
        // } else {
        //     $sql = "SELECT %s
        //         FROM ktv_cpg a
        //             LEFT JOIN `ktv_cpg_batch_trainings` b ON a.`CPGid`=b.`CPGid`
        //             LEFT JOIN `ktv_cpg_batch` c ON b.`CpgBatchID`=c.`CpgBatchID`
        //             LEFT JOIN `ktv_program_partner` d ON c.`PartnerID`=d.`PartnerID`
        //             left join ktv_village w on a.VillageID=w.VillageID
        //             left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
        //             left join ktv_district f on x.DistrictID = f.DistrictID
        //             left join ktv_district_partner y on f.DistrictID=y.DistrictID
        //             left join ktv_province z on SUBSTR(y.DistrictID,1,2)=z.ProvinceID
        //             left join ktv_farmer kcf on kcf.CPGid=a.CPGid
        //             left join ktv_program_staff prs on y.PartnerID = prs.PartnerID and prs.UserId = ?
        //             left JOIN ktv_private_staff pvs on y.PartnerID = pvs.PartnerID and pvs.UserID = ?
        //             inner join ktv_cpg_partner e on a.CPGid = e.CPGid AND e.PartnerID= IFNULL(prs.PartnerID,pvs.PartnerID)

        //             LEFT JOIN (
        //                 SELECT
        //                     FarmerID,
        //                     SUM(GardenHaUnCertified) AS totalLandSize,
        //                     COUNT(GardenNr) AS totalGarden
        //                 FROM
        //                 (
        //                 SELECT
        //                     FarmerID,
        //                     GardenNr,
        //                     MAX(SurveyNr) AS SurveyNr,
        //                     GardenHaUnCertified
        //                 FROM
        //                     ktv_farmer_garden
        //                 GROUP BY FarmerID ,GardenNr
        //                 ) AS temp_sum_garden_survey
        //                 GROUP BY FarmerID
        //                 ORDER BY FarmerID
        //             ) AS tbl_info_garden ON kcf.`FarmerID` = tbl_info_garden.FarmerID

        //             -- and (e.PartnerID =b.PartnerID OR e.PartnerID = c.PartnerID)
        //         WHERE
        //             -- e.PartnerID = 13 and
        //             -- y.ProvinceID= 72 and
        //             -- f.District = 'Poso'
        //             District=? and (GroupName like ? OR a.CPGid=? OR a.OldCPGid=?) AND a.Status != 'nullified'
        //         %s";

        //     $query = $this->db->query(sprintf($sql, "a.CPGid as id,a.OldCPGid,GroupName,a.Address,a.VillageID as RegionID,TahunTerbentuk, c.`BatchNumber`, d.`PartnerName`,
        //     concat(ifnull(SubDistrict,''),', ',ifnull(Village,'')) as RegionName,
        //     count(DISTINCT kcf.FarmerID) as Anggota,
        //     a.Latitude,a.Longitude,a.Elevation,a.Status,CONCAT(SUM(totalLandSize),' Ha') AS totalLandSize,COUNT(totalGarden) AS totalGarden", 'GROUP BY a.CPGid ORDER BY ' . $order . ' ' . $by . ' LIMIT ?,?'), array($userId, $userId, $kab, "%$key%", $key, $key, (int) $start, (int) $limit));
        //     $queryTotal = $this->db->query(sprintf($sql, 'count(distinct a.CPGid) as total', ''), array($userId, $userId, $kab, "%$key%", $key, $key));
        // }
        $result['data'] = $query->result_array();
        $total = $queryTotal->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }

    function readCpg($id, $NurseryNr, $opsiCall) {
        if ($opsiCall == 'form') {
            $sql = "
            SELECT a.CPGid as id, GroupName,Address,a.VillageID as RegionID,TahunTerbentuk,
               Province as Provinsi,District as Kabupaten,SubDistrict as Kecamatan,Village as Desa,
               concat(Province,', ',ifnull(District,''),', ',ifnull(SubDistrict,''),', ',ifnull(Village,'')) as RegionName,
               a.Latitude,a.Longitude,Elevation,a.Status,
               CompostID,kcc.Established,MesinChooper,RumahKompos,kcc.Latitude AS CompostLatitude, kcc.Longitude AS CompostLongitude,
               NurseryID,NurseryNr,Responsible,kcn.Established as nEstablished,Panjang,Lebar,Kapasitas,kcn.Latitude,LatitudeDeg1,LatitudeDeg2,LatitudeDeg3,
               kcn.Longitude,LongitudeDeg1,LongitudeDeg2,LongitudeDeg3,kcn.CertificationStatus,kcn.DateCertification,kcn.DateAppliedCertification,
               AdaPengurus,Ketua,Sekretaris,Bendahara,PertemuanLatitude,PertemuanLongitude,IF(SupplychainID is not null,1,2) bu,
                  `LocationCloseToCommunity`,
                  `LocationCloseToCommunityNo`,
                  `GoodLandArea`,
                  `GoodLandAreaNo`,
                  `LocationNearCocoaFarm`,
                  `LocationNearCocoaFarmNo`,
                  `ContinuousWaterSupply`,
                  `ContinuousWaterSupplyNo`,
                  `IrrigationInstalled`,
                  `IrrigationInstalledNo`,
                  `UseShadingNet`,
                  `UseShadingNetNo`,
                  `AdequateSupplyTopSoil`,
                  `AdequateSupplyTopSoilNo`,
                  `ImprovedVariety`,
                  `ImprovedVarietyNo`,
                  `ConstructStoring`,
                  `ConstructStoringNo`,
                  `CorrectEquipment`,
                  `CorrectEquipmentNo`,
                  `WindBreakInstalled`,
                  `WindBreakInstalledNo`,
                  `SecurityFenceInstalled`,
                  `SecurityFenceInstalledNo`,
                  `FertilizerUsed`,
                  `FertilizerUsedNo`,
                  `OperatorAdequateTraining`,
                  `OperatorAdequateTrainingNo`,
                  `AdequateFacility`,
                  `AdequateFacilityNo`,
                  `SustainablePestDisease`,
                  `SustainablePestDiseaseNo`,
                  `CloneGrading`,
                  `CloneGradingNo`,
                  `SeedlingCullingDone`,
                  `SeedlingCullingDoneNo`,
                  `ProperInputSalesRecord`,
                  `ProperInputSalesRecordNo`,
                  `SeedsPreGerminated`,
                  `SeedsPreGerminatedNo`,
                  kcn.Photo,
                  ResponsibleType AS nurResponsibleType,
                  ResponsibleName AS nurResponsibleName,
                  ResponsibleBirthday AS nurResponsibleBirthday,
                  ResponsiblePhone AS nurResponsiblePhone,
                  ResponsibleGender AS nurResponsibleGender,
                  ResponsiblePhoto
            from ktv_cpg a
            left join ktv_village w on a.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID
            left join ktv_compost kcc on a.CPGid=kcc.ObjID and kcc.ObjType='cpg'
            left join ktv_nursery kcn on a.CPGid=kcn.ObjID and kcn.ObjType='cpg'
            left join ktv_supplychain_org kso on kso.OrgType='cpg' and kso.OrgID=a.CPGid
            WHERE a.CPGid=? AND kcn.NurseryNr = ? LIMIT 1";
            $query = $this->db->query($sql, array($id, $NurseryNr));
            $data = $query->result_array();

            $return['success'] = true;
            $return['data'] = $data[0];
            return $return;
        } else {
            $sql = "
            SELECT a.CPGid as id, GroupName,Address,a.VillageID as RegionID,TahunTerbentuk,
               Province as Provinsi,District as Kabupaten,SubDistrict as Kecamatan,Village as Desa,
               concat(Province,', ',ifnull(District,''),', ',ifnull(SubDistrict,''),', ',ifnull(Village,'')) as RegionName,
               a.Latitude,a.Longitude,Elevation,a.Status,
               CompostID,kcc.Established,MesinChooper,RumahKompos,kcc.Latitude AS CompostLatitude, kcc.Longitude AS CompostLongitude,
               NurseryID,NurseryNr,Responsible,kcn.Established as nEstablished,Panjang,Lebar,Kapasitas,kcn.Latitude,LatitudeDeg1,LatitudeDeg2,LatitudeDeg3,
               kcn.Longitude,LongitudeDeg1,LongitudeDeg2,LongitudeDeg3,kcn.CertificationStatus,kcn.DateCertification,kcn.DateAppliedCertification,
               AdaPengurus,Ketua,Sekretaris,Bendahara,PertemuanLatitude,PertemuanLongitude,IF(SupplychainID is not null,1,2) bu
            from ktv_cpg a
            left join ktv_village w on a.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID
            left join ktv_compost kcc on a.CPGid=kcc.ObjID and kcc.ObjType='cpg'
            left join ktv_nursery kcn on a.CPGid=kcn.ObjID and kcn.ObjType='cpg'
            left join ktv_supplychain_org kso on kso.OrgType='cpg' and kso.OrgID=a.CPGid
            WHERE a.CPGid=?";
            $query = $this->db->query($sql, array($id));
        }

        $result = $query->result_array();
        return $result[0];
    }

    function _generateCPGID($desa) {
        $sql = "
            SELECT IFNULL(IF(length(max(CPGid))!=8,concat(kd.DistrictID,LPAD(substr(max(CPGid)+1,5,4),4,'0')),max(CPGid)+1),
               concat(substr(?,1,4),'0001')) as id
            FROM ktv_cpg
            LEFT JOIN ktv_village kv ON kv.VillageID = ktv_cpg.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE substr(CPGid,1,4)=substr(?,1,4)";
        $query = $this->db->query($sql, array($desa, $desa));
        $result = $query->result_array();
        return $result[0]['id'];
    }

    function readBatchs() {
        $sql = "
            select CpgBatchID as id, concat(BatchNumber,' - ',PartnerName) as label
            from ktv_cpg_batch a
            left join ktv_program_partner b on a.PartnerID=b.PartnerID";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    function createCpg($GroupName, $Address, $TahunTerbentuk, $RegionID, $lat, $long, $ele, $status, $AdaPengurus, $Ketua, $Sekretaris, $Bendahara, $PertemuanLatitude, $PertemuanLongitude, $userid, $OwnerClientID) {
        $this->db->trans_start();

        $sql = "INSERT INTO ktv_cpg(CPGid,OwnerClientID,GroupName,Address, TahunTerbentuk, VillageID, Latitude, Longitude,
               Elevation, Status,
               AdaPengurus,Ketua,Sekretaris,Bendahara,PertemuanLatitude,PertemuanLongitude,
               DateCreated,DateUpdated,CreatedBy,LastModifiedBy)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now(),?,?)";
        $query = $this->db->query($sql, array($this->_generateCPGID($RegionID), $OwnerClientID, $GroupName, $Address, $TahunTerbentuk,
            $RegionID, $lat, $long, $ele, $status, $AdaPengurus, $Ketua, $Sekretaris, $Bendahara, $PertemuanLatitude, $PertemuanLongitude,
            $userid, $userid));

        //insert kan ke cpg_partner
        $CPGid = $this->db->insert_id();

        $sql="INSERT INTO `ktv_cpg_partner` (`CPGid`, `PartnerID`) VALUES (?, ?)";
        $query = $this->db->query($sql,array($CPGid,$OwnerClientID));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['id'] = $CPGid;
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to saved data";
        }
        return $results;
    }

    function createTraining($CPGID, $CPGtrainingsID, $ProgramStaffID, $ExtensionStaffID, $KeyFarmerID, $DemoplotOwnerID, $TrainingStart, $TrainingEnd, $PetaniKakao, $FamilyID, $CpgBatchID, $TrainingDays, $userid, $TrainingDayStatus, $CpgTrainingsIDSubTopic) {

        if($FamilyID != ""){
            $arrTmp = explode('-',$FamilyID);
            $FamilyID = $arrTmp[0];
            $FamilyName = $arrTmp[1];
        }else{
            $FamilyID = null;
            $FamilyName = null;
        }

        //defaultnya
        if($TrainingDayStatus == "") $TrainingDayStatus = 'full';

        $p = array($CPGID, $CPGtrainingsID, $ProgramStaffID, $ExtensionStaffID, $KeyFarmerID == '' ? NULL : $KeyFarmerID,
            $DemoplotOwnerID, $TrainingStart, $TrainingEnd, $PetaniKakao, $FamilyID,$FamilyName, $CpgBatchID, $TrainingDays,$TrainingDayStatus,
            $userid, $userid);
        foreach ($p as $key => $value) {
            if($p[$key] == "")
                $p[$key] = null;
        }

        $sql = "
            INSERT INTO ktv_cpg_batch_trainings(CPGid,CPGtrainingsID,ProgramStaffID,ExtensionStaffID,KeyFarmerID,
	              DemoplotOwnerID,TrainingStart,TrainingEnd,PetaniKakao,FamilyID,FamilyName,CpgBatchID,TrainingDays,TrainingDayStatus,
                 DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),?,now(),?)";
        $query = $this->db->query($sql, $p);
        if ($query) {
            $CpgBatchTrainingID = $this->db->insert_id();

            //insert sub topic (begin)
            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_cpg_batch_trainings_sub_topics` SET
                          `CpgBatchTrainingID` = ?,
                          `SubCpgTrainingsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $CpgBatchTrainingID,
                        $value,
                        $userid
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            //insert sub topic (end)

            $results['idt'] = $CpgBatchTrainingID;
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateTraining($CPGtrainingsID, $ProgramStaffID, $ExtensionStaffID, $KeyFarmerID, $DemoplotOwnerID, $TrainingStart, $TrainingEnd, $PetaniKakao, $FamilyID, $CpgBatchID, $TrainingDays, $id, $userid, $TrainingDayStatus, $CpgTrainingsIDSubTopic) {
        $sql1 = "SELECT PersonID FROM ktv_extension_staff WHERE ExtensionID=?";
        $InstructorPersonID = @$this->db->query($sql1, array($ExtensionStaffID))->row()->PersonID;
        $sql2 = "SELECT * FROM (
	               	SELECT StaffID as id,CONCAT(b.PersonNm,' (PR)') as label, b.PersonID
	               	FROM ktv_program_staff a
	               	INNER JOIN ktv_persons b on a.PersonID=b.PersonID
	                             LEFT JOIN sys_user x ON x.UserId=b.UserID
	                             WHERE a.StatusCd='active' AND x.StatusCode='active'
	               	UNION
	               	SELECT PrivateStaffID as id,CONCAT(d.PersonNm,' (PS)') as label, d.PersonID
	               	FROM ktv_private_staff c
	                             INNER JOIN ktv_persons d ON c.PersonID=d.PersonID
	                             LEFT JOIN sys_user y ON y.UserId=d.UserID
	                             WHERE c.StatusCode='active' AND y.StatusCode='active'
	            	) a WHERE a.id=?";
        $FacilitatorPersonID = @$this->db->query($sql2, array($ProgramStaffID))->row()->PersonID;

        if($FamilyID != ""){
            $arrTmp = explode('-',$FamilyID);
            $FamilyID = $arrTmp[0];
            $FamilyName = $arrTmp[1];
        }else{
            $FamilyID = null;
            $FamilyName = null;
        }

        $sql = "
            UPDATE ktv_cpg_batch_trainings
            SET CPGtrainingsID=?,ProgramStaffID=?,ExtensionStaffID=?,FacilitatorPersonID=?,InstructorPersonID=?,KeyFarmerID=?,DemoplotOwnerID=?,TrainingStart=?,
                TrainingEnd=?,PetaniKakao=?,FamilyID=?, FamilyName = ?,CpgBatchID=?,TrainingDays=?, TrainingDayStatus = ?,DateUpdated=now(),LastModifiedBy=?
            WHERE CpgBatchTrainingID=?";
        $query = $this->db->query($sql, array($CPGtrainingsID, $ProgramStaffID, $ExtensionStaffID, $FacilitatorPersonID, $InstructorPersonID, $KeyFarmerID == '' ? NULL : $KeyFarmerID,
            $DemoplotOwnerID, $TrainingStart, $TrainingEnd, $PetaniKakao, $FamilyID, $FamilyName, $CpgBatchID, $TrainingDays, $TrainingDayStatus, $userid, $id));
        if ($query) {

            //sub topic (begin)
            $sql="DELETE FROM ktv_cpg_batch_trainings_sub_topics WHERE CpgBatchTrainingID = ?";
            $query = $this->db->query($sql,array($id));

            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_cpg_batch_trainings_sub_topics` SET
                          `CpgBatchTrainingID` = ?,
                          `SubCpgTrainingsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $id,
                        $value,
                        $userid
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            //sub topic (end)

            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    /* old
      function createParticipant($CpgBatchTrainingID,$FarmerID,$PetaniKakao,$FamilyID,$WritingAwal,$WritingAkhir,
      $BallotAwal,$BallotAkhir,$userid){
      $sql = "
      INSERT INTO ktv_cpg_batch_trainings_farmers(CpgBatchTrainingID, FarmerID, PetaniKakao, FamilyID, WritingAwal,
      WritingAkhir, BallotAwal, BallotAkhir,DateCreated,DateUpdated,CreatedBy,LastModifiedBy)
      VALUES (?,?,?,?,?,   ?,?,?,now(),now(),?,?)";
      $query = $this->db->query($sql, array($CpgBatchTrainingID,$FarmerID,$PetaniKakao,$FamilyID,$WritingAwal,
      $WritingAkhir,$BallotAwal,$BallotAkhir,$userid,$userid));
      if ($query) {
      $results['success'] = true;
      $results['message'] = "record created.";
      } else {
      $results['success'] = false;
      $results['message'] = "Failed to create record";
      }
      return $results;
      }
     */

    function createParticipant($CpgBatchTrainingID, $participants, $PetaniKakao, $userid) {

        $record = array();
        foreach ($participants as $participant) {
            $record[] = array(
                'CpgBatchTrainingID' => $CpgBatchTrainingID,
                'FarmerID' => $participant,
                'PetaniKakao' => 1,
                'FamilyID' => 0,
                'FamilyName' => null,
                'WritingAwal' => 0,
                'WritingAkhir' => 0,
                'BallotAwal' => 0,
                'BallotAkhir' => 0,
                'DateCreated' => date("Y-m-d H:i:s"),
                'DateUpdated' => date("Y-m-d H:i:s"),
                'CreatedBy' => $userid,
                'LastModifiedBy' => $userid,
                'DateSynced' => '9999-12-31 23:59:59'
            );
        }

        if (!$this->db->insert_batch('ktv_cpg_batch_trainings_farmers', $record)) {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        } else {
            $results['success'] = true;
            $results['message'] = "record created.";
        }
        //$results['success'] = true;
        //$results['message'] = "record created.";
        return $results;
    }

    function updateParticipant($CpgBatchTrainingID, $FarmerID, $PetaniKakao, $FamilyID, $WritingAwal, $WritingAkhir, $BallotAwal, $BallotAkhir, $id, $userid) {
        if($FamilyID != ""){
            $arrTmp = explode("-",$FamilyID);
            $FamilyPartID = $arrTmp[0];
            $FamilyPartName = $arrTmp[1];
        }else{
            $FamilyPartID = null;
            $FamilyPartName = null;
        }

        $sql = "
            UPDATE ktv_cpg_batch_trainings_farmers
            SET CpgBatchTrainingID=?, FarmerID=?, PetaniKakao=?, FamilyID=?,FamilyName=?, WritingAwal=?, WritingAkhir=?, BallotAwal=?,
                BallotAkhir=?,DateUpdated=now(),LastModifiedBy=?
            WHERE CpgBatchTrainingsFarmerID=?";
        $query = $this->db->query($sql, array($CpgBatchTrainingID, $FarmerID, $PetaniKakao, $FamilyPartID, $FamilyPartName, $WritingAwal, $WritingAkhir,
            $BallotAwal, $BallotAkhir, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateCpg($GroupName, $Address, $TahunTerbentuk, $RegionID, $lat, $long, $ele, $status, $AdaPengurus, $Ketua, $Sekretaris, $Bendahara, $PertemuanLatitude, $PertemuanLongitude, $id, $userid) {
        $sql = "
            UPDATE ktv_cpg
            SET GroupName=?,Address=?, TahunTerbentuk=?, VillageID=?, Latitude=?, Longitude=?, Elevation=?,
               AdaPengurus=?,Ketua=?,Sekretaris=?,Bendahara=?,PertemuanLatitude=?,PertemuanLongitude=?,
               DateUpdated=now(),LastModifiedBy=?
            WHERE CPGid=?";
        $query = $this->db->query($sql, array($GroupName, $Address, $TahunTerbentuk, $RegionID, $lat, $long, $ele,
            $AdaPengurus, $Ketua, $Sekretaris, $Bendahara, $PertemuanLatitude, $PertemuanLongitude, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteCpg($id) {
        $sql_cek = "SELECT count(FarmerId) as jumlah FROM ktv_farmer WHERE StatusCode='active' and CPGid=?";
        $query = $this->db->query($sql_cek, array($id));
        $result = $query->result_array();
        if ($result[0]['jumlah'] > 0) {
            $results['success'] = false;
            $results['message'] = "Failed to delete record, because CPG have farmer data";
            return $results;
        }

        //$sql = "DELETE FROM ktv_cpg WHERE CPGid=?";
        $sql = "UPDATE ktv_cpg SET Status='nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE CPGid = ? LIMIT 1";

        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function deleteTraining($id) {
        //$sql = "DELETE FROM ktv_cpg_batch_trainings WHERE CpgBatchTrainingID=?";
        $sql = "UPDATE ktv_cpg_batch_trainings SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE CpgBatchTrainingID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function deleteParticipant($id) {
        //$sql = "DELETE FROM ktv_cpg_batch_trainings_farmers WHERE CpgBatchTrainingsFarmerID=?";
        $sql = "UPDATE ktv_cpg_batch_trainings_farmers SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE CpgBatchTrainingsFarmerID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readTrainings($key) {
        /* $sql = "
          SELECT %s
          FROM ktv_cpg_batch_trainings a
          LEFT JOIN ktv_cpg_trainings b ON a.CPGtrainingsID=b.CpgTrainingsID
          LEFT JOIN ktv_cpg_batch_trainings_farmers c ON a.CpgBatchTrainingID=c.CpgBatchTrainingID
          WHERE CPGid=? AND a.StatusCode != 'nullified' AND c.StatusCode!='nullified'
          GROUP BY a.CpgBatchTrainingID
          ORDER BY CpgTrainings %s";
          $query = $this->db->query(sprintf($sql, "a.CpgBatchTrainingID as id,CpgTrainings as label,CpgTrainings,
          DATE_FORMAT(TrainingStart, '%m/%d/%Y') as TrainingStart,DATE_FORMAT(TrainingEnd, '%m/%d/%Y') as TrainingEnd,
          count(CpgBatchTrainingsFarmerID) as participant,a.CPGID,a.CpgTrainingsID,
          ProgramStaffID,ExtensionStaffID,KeyFarmerID,DemoplotOwnerID,a.PetaniKakao,a.FamilyID,a.CpgBatchID,
          a.TrainingDays", ''), array($key));
         */
        $sql = "
            SELECT %s
            FROM ktv_cpg_batch_trainings a
            LEFT JOIN ktv_cpg c ON c.CPGid = a.CPGid
            LEFT JOIN ktv_cpg_batch cb ON cb.CpgBatchID = a.CpgBatchID
            LEFT JOIN ktv_cpg_trainings b ON a.CPGtrainingsID=b.CpgTrainingsID
            LEFT JOIN ktv_family fam ON a.FamilyID = fam.FamilyID AND a.FamilyName = fam.AnggotaName
            LEFT JOIN ktv_cpg_batch_trainings_sub_topics subtop ON a.`CpgBatchTrainingID` = subtop.CpgBatchTrainingID
            WHERE a.CPGid=? AND a.StatusCode != 'nullified'
            GROUP BY a.CpgBatchTrainingID
            ORDER BY CpgTrainings %s";
        $query = $this->db->query(sprintf($sql, "a.CpgBatchTrainingID as id, c.GroupName, cb.BatchNumber,CpgTrainings as label,CpgTrainings,
            DATE_FORMAT(TrainingStart, '%m/%d/%Y') as TrainingStart,DATE_FORMAT(TrainingEnd, '%m/%d/%Y') as TrainingEnd,
            (SELECT COUNT(*) FROM ktv_cpg_batch_trainings_farmers aa WHERE aa.CpgBatchTrainingID=a.CpgBatchTrainingID AND aa.StatusCode != 'nullified') as participant,a.CPGID,a.CpgTrainingsID,
            ProgramStaffID,ExtensionStaffID,KeyFarmerID,DemoplotOwnerID,a.PetaniKakao,CONCAT(a.FamilyID,'-',fam.AnggotaName) AS FamilyID,a.CpgBatchID,
            a.TrainingDays, a.TrainingDayStatus, GROUP_CONCAT(SubCpgTrainingsID SEPARATOR '@') AS subtopic", ''), array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(distinct a.CpgBatchTrainingID) as total', ''), array($key));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readTraining($id) {
        $sql = "
            SELECT a.*,
              batch.BatchNumber,
              b.CpgTrainings,d.GroupName,g.PersonNm AS koordinator,
              h.FarmerName AS pemandu,
              jp.PersonNm AS penyuluh,
               w.Village as Desa,x.SubDistrict as Kecamatan, y.District as Kabupaten,z.Province as Provinsi,
               DATE_FORMAT(TrainingStart,'%d - %b - %Y') as TrainingStart, DATE_FORMAT(TrainingEnd,'%d - %b - %Y') as TrainingEnd, d.OldCPGid,kp.PersonNm AS PrivateStaffName, l.AnggotaName AS KeyFarmerFamily, d.CPGid,
               GROUP_CONCAT(DISTINCT sub_train.CpgTrainings SEPARATOR ', ') AS CpgTrainingsSubTopic
            FROM ktv_cpg_batch_trainings a
            LEFT JOIN ktv_cpg_batch batch ON batch.CpgBatchID = a.CpgBatchID
            LEFT JOIN ktv_cpg_trainings b ON a.CPGtrainingsID=b.CpgTrainingsID
            LEFT JOIN ktv_cpg_batch_trainings_farmers c ON a.CpgBatchTrainingID=c.CpgBatchTrainingID
            LEFT JOIN ktv_cpg d ON a.CPGID=d.CPGid
            left join ktv_village w on d.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID
            LEFT JOIN ktv_program_staff f ON a.ProgramStaffID=f.StaffID
            LEFT JOIN ktv_persons g ON f.PersonId=g.PersonID
            LEFT JOIN ktv_farmer h ON a.KeyFarmerID=h.FarmerId and h.StatusCode='active'
            LEFT JOIN ktv_extension_staff j ON a.ExtensionStaffID=j.ExtensionID
            LEFT JOIN ktv_persons jp ON jp.PersonID = j.PersonID
            LEFT JOIN ktv_private_staff k ON a.ProgramStaffID=k.PrivateStaffID
            LEFT JOIN ktv_persons kp ON kp.PersonID = k.PersonID
            LEFT JOIN ktv_family l ON a.FamilyID=l.FamilyID
            LEFT JOIN ktv_cpg_batch_trainings_sub_topics sub ON a.CpgBatchTrainingID = sub.CpgBatchTrainingID
            LEFT JOIN ktv_cpg_trainings sub_train ON sub.SubCpgTrainingsID = sub_train.CpgTrainingsID
            WHERE a.CpgBatchTrainingID=?
            GROUP BY a.CpgBatchTrainingID
            LIMIT 1";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();

        if($result[0]['CpgTrainingsSubTopic'] != "") $result[0]['CpgTrainingsSubTopic'] = '('.$result[0]['CpgTrainingsSubTopic'].')';
        return $result[0];
    }

    function readFamilyTrainings($id) {
        $sql = "
            SELECT CONCAT(FamilyID,'-',AnggotaName) as id,AnggotaName as label
            FROM ktv_family
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readTrainingNames() {
        $sql = "
            SELECT CpgTrainingsID as id,CpgTrainings as label
            FROM ktv_cpg_trainings WHERE StatusCode='active' AND ParentID = '0'";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getTrainingSubtopic($CpgTrainingsID){
        $sql="SELECT CpgTrainingsID as id,CpgTrainings as label
            FROM ktv_cpg_trainings WHERE StatusCode='active' AND ParentID = ?";
        $query = $this->db->query($sql,array($CpgTrainingsID));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFamily($key) {
        $sql = "
            SELECT CONCAT(FamilyID,'-',AnggotaName) as id,AnggotaName as label
            FROM ktv_family
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($key));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readParticipants($key, $dayNumber = '', $type = '', $FarmerID = '') {
        if ($dayNumber != '') {
            $sql = "
				SELECT %s
				FROM ktv_cpg_batch_trainings_farmers a
				left join ktv_cpg_batch_trainings c on a.CpgBatchTrainingID=c.CpgBatchTrainingID
				left join ktv_cpg_batch e on e.CpgBatchID=c.CpgBatchID
				left join ktv_farmer b on b.StatusCode='active' and a.FarmerID=b.FarmerID
				left join ktv_family d on a.FamilyID=d.FamilyID and a.FamilyName = d.AnggotaName
				left join ktv_village w on b.VillageID=w.VillageID
				left join ktv_cpg_batch_trainings_attendance cbta on cbta.CpgBatchTrainingID=a.CpgBatchTrainingID AND cbta.FarmerID=a.FarmerID AND cbta.DayNumber=?
				WHERE a.CpgBatchTrainingID=? AND a.StatusCode != 'nullified' %s
				ORDER BY FarmerName ASC";
            $query = $this->db->query(sprintf($sql, "a.CpgBatchTrainingsFarmerID,a.CpgBatchTrainingID,a.FarmerID as pFarmerID,
				a.PetaniKakao,a.FamilyID,a.FamilyName AS AnggotaName,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,FarmerName as PersonNm,
				IF(a.PetaniKakao='1','Ya','Tidak') as partisipan,Village as Desa,Gender,b.OldFarmerID,b.LearningContractSign,e.PartnerID,cbta.SignAttendance1,cbta.SignAttendance2,cbta.Attendance1,cbta.Attendance2

				", ''), array($dayNumber, $key));
            $result['data'] = $query->result_array();
            $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($dayNumber, $key));
            $result['total'] = $query->row()->total;
        } else {
            if ($type != "") {
                if (strpos($FarmerID, '::')) {
                    $in = "IN";
                    $FarmerID = str_replace('::', ',', $FarmerID);
                } else {
                    if ($FarmerID != "") {
                        $in = "IN";
                    } else {
                        $in = "NOT IN";
                    }
                }
                $sql = "
					SELECT %s
					FROM ktv_cpg_batch_trainings_farmers a
					left join ktv_cpg_batch_trainings c on a.CpgBatchTrainingID=c.CpgBatchTrainingID
					left join ktv_cpg_batch e on e.CpgBatchID=c.CpgBatchID
					left join ktv_farmer b on b.StatusCode='active' and a.FarmerID=b.FarmerID
					left join ktv_family d on a.FamilyID=d.FamilyID and a.FamilyName = d.AnggotaName
					left join ktv_village w on b.VillageID=w.VillageID
					WHERE c.CPGid=?
					AND a.FarmerID $in ($FarmerID)
					AND a.StatusCode != 'nullified' %s
					ORDER BY FarmerName ASC";
                $query = $this->db->query(sprintf($sql, "a.CpgBatchTrainingsFarmerID,a.CpgBatchTrainingID,a.FarmerID as pFarmerID,
					a.PetaniKakao,a.FamilyID,a.FamilyName AS AnggotaName,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,FarmerName as PersonNm,
					IF(a.PetaniKakao='1','Ya','Tidak') as partisipan,Village as Desa,Gender,b.OldFarmerID,b.LearningContractSign,e.PartnerID, '' as 'SignAttendance1'
					", 'GROUP BY a.FarmerID'), array($key));
                $result['data'] = $query->result_array();
                $query = $this->db->query(sprintf($sql, 'count( a.FarmerID) as total', ''), array($key));
                $result['total'] = $query->row()->total;
            } else {
                $sql = "
					SELECT %s
					FROM ktv_cpg_batch_trainings_farmers a
					left join ktv_cpg_batch_trainings c on a.CpgBatchTrainingID=c.CpgBatchTrainingID
					left join ktv_cpg_batch e on e.CpgBatchID=c.CpgBatchID
					left join ktv_farmer b on b.StatusCode='active' and a.FarmerID=b.FarmerID
					left join ktv_family d on a.FamilyID=d.FamilyID and a.FamilyName = d.AnggotaName
					left join ktv_village w on b.VillageID=w.VillageID
					WHERE a.CpgBatchTrainingID=? AND a.StatusCode != 'nullified' %s
					ORDER BY FarmerName ASC";
                $query = $this->db->query(sprintf($sql, "a.CpgBatchTrainingsFarmerID,a.CpgBatchTrainingID,a.FarmerID as pFarmerID,
					a.PetaniKakao,a.FamilyID,a.FamilyName AS AnggotaName,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,FarmerName as PersonNm,
					IF(a.PetaniKakao='1','Ya','Tidak') as partisipan,Village as Desa,Gender,b.OldFarmerID,b.LearningContractSign,e.PartnerID, '' as 'SignAttendance1'
					", ''), array($key));
                $result['data'] = $query->result_array();
                $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($key));
                $result['total'] = $query->row()->total;
            }
        }
        return $result;
    }

    function readParticipantsAdd($CpgBatchTrainingID, $cpgID, $key) {
        $sql = "SELECT
                    %s
                FROM ktv_farmer
                WHERE
                    CPGid = ? AND
                    FarmerID NOT IN (
                        SELECT
                            FarmerID
                        FROM ktv_cpg_batch_trainings_farmers
                        WHERE
                            CpgBatchTrainingID = ? AND StatusCode='active'
                    ) AND
                    (FarmerName like ? OR FarmerID=? OR OldFarmerID=?)";
        $query = $this->db->query(sprintf($sql, "FarmerID as addFarmerID,FarmerName as addFarmerName"), array($cpgID, $CpgBatchTrainingID, "%$key%", $key, $key));
        $result['data'] = $query->result_array();
        //$query = $this->db->query($sql, array($cpgID,$CpgBatchTrainingID));

        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($cpgID, $CpgBatchTrainingID, "%$key%", $key, $key));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readFarmers($cpg) {
        $sql = "
            SELECT %s
            FROM ktv_farmer a
            WHERE StatusCode='active' and CPGid=? %s";
        $query = $this->db->query(sprintf($sql, "FarmerID as id,FarmerID as label", ''), array($cpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readKeyFarmers($district, $key = '') {
        $add = ($key != '') ? "AND (a.OldFarmerID like '%$key%' OR a.FarmerName like '%$key%' OR a.FarmerID like '%$key%')" : '';
        $sql = "
            SELECT a.FarmerID as id,concat(a.FarmerID,' - ',a.FarmerName,' (',IFNULL(OldFarmerID,''),')') as label
            FROM ktv_farmer a
            LEFT JOIN ktv_village v ON v.`VillageID` = a.`VillageID`
            LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
            left join ktv_district c ON c.`DistrictID` = sd.`DistrictID`
            WHERE a.StatusCode='active' and c.District=? $add";
        $query = $this->db->query($sql, array($district));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readDemoPlots($cpg) {
        $sql = "
            SELECT a.FarmerID as id,concat(a.FarmerID,' - ',a.FarmerName,' (',IFNULL(a.OldFarmerID,''),')') as label
            from ktv_farmer a
            WHERE StatusCode='active' and CPGid=?";
        $query = $this->db->query($sql, array($cpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readDemoplotGrids($cpg) {
        $sql = "
            SELECT
                a.`CpgDemoplotID` AS `id`,
                a.`CPGid`,
                a.`CpgBatchTrainingID`,
                CONCAT(d.`CpgTrainings`,' (',DATE_FORMAT(DATE(c.`TrainingStart`),'%d %M %Y'),')') AS CpgBatchTrainingName,
                a.`TrainingDate`,
                DATE_FORMAT(a.`TrainingDate`, '%d %M %Y') AS TrainingDateVal,
                UPPER(a.`ObjType`) AS ObjType,
                CASE
                    WHEN a.ObjType = 'cpg'
                        THEN (
                            SELECT
                                CONCAT(sub_a.CPGid,' - ',sub_a.GroupName) AS label
                            FROM
                                ktv_cpg sub_a
                            WHERE
                                sub_a.CPGid = a.ObjID
                        )
                    WHEN a.ObjType = 'farmer'
                        THEN (
                            SELECT
                                CONCAT(sub_a.FarmerID,' - ',sub_a.FarmerName) AS label
                            FROM
                                ktv_farmer sub_a
                            WHERE
                                sub_a.FarmerID = a.ObjID
                        )
                END AS OwnerLabel
            FROM
                `ktv_cpg_demoplot` a
            LEFT JOIN ktv_cpg_batch_trainings c ON a.CpgBatchTrainingID = c.CpgBatchTrainingID
            LEFT JOIN ktv_cpg_trainings d ON d.`CpgTrainingsID` = c.`CPGtrainingsID`
            WHERE
                a.CPGid = ?
                AND a.StatusCode != 'nullified'
        ";
        $query = $this->db->query($sql, array($cpg));
        $result['data'] = $query->result_array();

        return $result;
    }

    function readDemoplotDetail($id) {
        $sql = "
            SELECT
                  `CpgDemoplotID` AS `id`,
                  `CPGid`,
                  `CpgBatchTrainingID`,
                  `TrainingDate`,
                  `ObjType`,
                  `ObjID`,
                  `GardenNr`,
                  `GardenHa`,
                  `KebunPanjang`,
                  `KebunLebar`,
                  `KbBayam`,
                  `KbTomat`,
                  `KbKangkung`,
                  `KbKelor`,
                  `KbKacangPanjang`,
                  `KbUbi`,
                  `KbCabe`,
                  `KbLabu`,
                  `KbTerong`,
                  `KbKatuk`,
                  `KbSawi`,
                  `HaveFishPond`,
                  `FpPanjang`,
                  `FpLebar`,
                  `FpNila`,
                  `FpIkanMas`,
                  `FpLele`,
                  `FpMujair`,
                  `FpLainnya`,
                  `Comment`
            FROM
                `ktv_cpg_demoplot`
            WHERE
                CpgDemoplotID LIKE ?
        ";
        $query = $this->db->query($sql, array("%$id%"));
        $result = $query->result_array();

        return $result[0];
    }

    function createDemoplot($varPro) {
        if($varPro['demoplot_ObjType'] == "cpg"){
            $varPro['demoplot_ObjID'] = $varPro['demoplot_cpg_id'];
            $varPro['demoplot_farmer_garden_number'] = null;
            $varPro['demoplot_farmer_garden_ha'] = null;
        }
        if($varPro['demoplot_ObjType'] == "farmer"){
            $varPro['demoplot_ObjID'] = $varPro['demoplot_farmer_owner_id'];
            $varPro['demoplot_cpg_owner_panjang'] = null;
            $varPro['demoplot_cpg_owner_lebar'] = null;
        }

        $sql="INSERT INTO `ktv_cpg_demoplot` SET
              `CPGid` = ?,
              `CpgBatchTrainingID` = ?,
              `TrainingDate` = ?,
              `ObjType` = ?,
              `ObjID` = ?,
              `GardenNr` = ?,
              `GardenHa` = ?,
              `KebunPanjang` = ?,
              `KebunLebar` = ?,
              `KbBayam` = ?,
              `KbTomat` = ?,
              `KbKangkung` = ?,
              `KbKelor` = ?,
              `KbKacangPanjang` = ?,
              `KbUbi` = ?,
              `KbCabe` = ?,
              `KbLabu` = ?,
              `KbTerong` = ?,
              `KbKatuk` = ?,
              `KbSawi` = ?,
              `HaveFishPond` = ?,
              `FpPanjang` = ?,
              `FpLebar` = ?,
              `FpNila` = ?,
              `FpIkanMas` = ?,
              `FpLele` = ?,
              `FpMujair` = ?,
              `FpLainnya` = ?,
              `Comment` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $varPro['demoplot_cpg_id'],
            $varPro['demoplot_batch_training_id'],
            $varPro['demoplot_training_date'],
            $varPro['demoplot_ObjType'],
            $varPro['demoplot_ObjID'],
            $varPro['demoplot_farmer_garden_number'],
            $varPro['demoplot_farmer_garden_ha'],
            $varPro['demoplot_cpg_owner_panjang'],
            $varPro['demoplot_cpg_owner_lebar'],
            $varPro['demoplot_KbBayam'],
            $varPro['demoplot_KbTomat'],
            $varPro['demoplot_KbKangkung'],
            $varPro['demoplot_KbKelor'],
            $varPro['demoplot_KbKacangPanjang'],
            $varPro['demoplot_KbSingkong'],
            $varPro['demoplot_KbCabai'],
            $varPro['demoplot_KbLabu'],
            $varPro['demoplot_KbTerong'],
            $varPro['demoplot_KbKatuk'],
            $varPro['demoplot_KbSawi'],
            $varPro['demoplot_HaveFishPond'],
            $varPro['demoplot_FpPanjang'],
            $varPro['demoplot_FpLebar'],
            $varPro['demoplot_FpNila'],
            $varPro['demoplot_FpCarp'],
            $varPro['demoplot_FpCatfish'],
            $varPro['demoplot_FpTilapia'],
            $varPro['demoplot_FpOthers'],
            $varPro['demoplot_comment'],
            $varPro['userid']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateDemoplot($varPro) {
        if($varPro['demoplot_ObjType'] == "cpg"){
            $varPro['demoplot_ObjID'] = $varPro['demoplot_cpg_id'];
            $varPro['demoplot_farmer_garden_number'] = null;
            $varPro['demoplot_farmer_garden_ha'] = null;
        }
        if($varPro['demoplot_ObjType'] == "farmer"){
            $varPro['demoplot_ObjID'] = $varPro['demoplot_farmer_owner_id'];
            $varPro['demoplot_cpg_owner_panjang'] = null;
            $varPro['demoplot_cpg_owner_lebar'] = null;
        }

        $sql = "
        UPDATE
            `ktv_cpg_demoplot`
        SET
            `CPGid` = ?,
            `CpgBatchTrainingID` = ?,
            `TrainingDate` = ?,
            `ObjType` = ?,
              `ObjID` = ?,
              `GardenNr` = ?,
              `GardenHa` = ?,
              `KebunPanjang` = ?,
              `KebunLebar` = ?,
              `KbBayam` = ?,
              `KbTomat` = ?,
              `KbKangkung` = ?,
              `KbKelor` = ?,
              `KbKacangPanjang` = ?,
              `KbUbi` = ?,
              `KbCabe` = ?,
              `KbLabu` = ?,
              `KbTerong` = ?,
              `KbKatuk` = ?,
              `KbSawi` = ?,
              `HaveFishPond` = ?,
              `FpPanjang` = ?,
              `FpLebar` = ?,
              `FpNila` = ?,
              `FpIkanMas` = ?,
              `FpLele` = ?,
              `FpMujair` = ?,
              `FpLainnya` = ?,
              `Comment` = ?,
            `DateUpdated` = NOW(),
            `UpdatedBy` = ?
        WHERE `CpgDemoplotID` = ?
        ";
        $p = array(
            $varPro['demoplot_cpg_id'],
            $varPro['demoplot_batch_training_id'],
            $varPro['demoplot_training_date'],
            $varPro['demoplot_ObjType'],
            $varPro['demoplot_ObjID'],
            $varPro['demoplot_farmer_garden_number'],
            $varPro['demoplot_farmer_garden_ha'],
            $varPro['demoplot_cpg_owner_panjang'],
            $varPro['demoplot_cpg_owner_lebar'],
            $varPro['demoplot_KbBayam'],
            $varPro['demoplot_KbTomat'],
            $varPro['demoplot_KbKangkung'],
            $varPro['demoplot_KbKelor'],
            $varPro['demoplot_KbKacangPanjang'],
            $varPro['demoplot_KbSingkong'],
            $varPro['demoplot_KbCabai'],
            $varPro['demoplot_KbLabu'],
            $varPro['demoplot_KbTerong'],
            $varPro['demoplot_KbKatuk'],
            $varPro['demoplot_KbSawi'],
            $varPro['demoplot_HaveFishPond'],
            $varPro['demoplot_FpPanjang'],
            $varPro['demoplot_FpLebar'],
            $varPro['demoplot_FpNila'],
            $varPro['demoplot_FpCarp'],
            $varPro['demoplot_FpCatfish'],
            $varPro['demoplot_FpTilapia'],
            $varPro['demoplot_FpOthers'],
            $varPro['demoplot_comment'],
            $varPro['userid'],
            $varPro['demoplot_id']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        return $results;
    }

    function deleteDemoplot($id) {
        /*
          $sql = "
          DELETE
          FROM
          `ktv_cpg_demoplot`
          WHERE
          `CpgDemoplotID` = ?
          ";
         */
        $sql = "UPDATE ktv_cpg_demoplot SET StatusCode = 'nullified',UpdatedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE CpgDemoplotID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    function getComboBatchTraining($cpg_id) {
        $sql = "
            SELECT
                a.`CpgBatchTrainingID` AS id,
                CONCAT(b.`CpgTrainings`,' (',DATE_FORMAT(DATE(a.`TrainingStart`),'%d %M %Y'),')') AS label
            FROM
                ktv_cpg_batch_trainings a
            LEFT JOIN ktv_cpg_trainings b ON b.`CpgTrainingsID` = a.`CPGtrainingsID`
            WHERE a.`CPGid` = ?
            ORDER BY a.TrainingStart DESC
            ";
        $query = $this->db->query($sql, array($cpg_id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboDemoplotOwner($cpg_id) {
        $sql = "
            SELECT
                a.`FarmerID` AS `id`,
                a.`FarmerName` AS label
            FROM
                `ktv_farmer` a
            WHERE
                a.CPGid = ?
                AND a.StatusCode = 'active'
            ORDER BY a.FarmerName ASC
            ";
        $query = $this->db->query($sql, array($cpg_id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboGardenNumber($farmer_id) {
        $sql = "
            SELECT
                `GardenNr` AS `id`,
                `GardenNr` AS `label`
            FROM
                `ktv_farmer_garden`
            WHERE FarmerID = ? AND StatusCode = 'active'
            GROUP BY GardenNr
        ";
        $query = $this->db->query($sql, array($farmer_id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getFarmerGardenDetail($GardenNr,$FarmerID){
        $sql="SELECT
                a.GardenHaUnCertified AS GardenHa
            FROM
                ktv_farmer_garden a
            WHERE
                a.`FarmerID` = ?
                AND a.`GardenNr` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $FarmerID, (int) $GardenNr));
        return $query->row_array();
    }

    function readFasilitators($workarea) {
        $sql = "
            SELECT * FROM (

               SELECT StaffID AS id,CONCAT(b.PersonNm,' (PR)') AS label
               FROM ktv_program_staff a
               LEFT JOIN ktv_persons b ON a.PersonID=b.PersonID
               LEFT JOIN sys_user `x` ON x.UserId=b.UserID
               WHERE a.StatusCd='active' AND b.`StatusCd` = 'active'

               UNION

               SELECT PrivateStaffID AS id,CONCAT(d.PersonNm,' (PS)') AS label
               FROM ktv_private_staff c
               INNER JOIN ktv_persons d ON c.PersonID=d.PersonID
               LEFT JOIN sys_user `y` ON y.UserId=d.UserID
               WHERE c.StatusCode='active' AND d.StatusCd = 'active'

            ) a
            ORDER BY label";
        //WHERE WorkArea=?";
        $query = $this->db->query($sql, array($workarea));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFasilitatorAlls() {
        $sql = "
            SELECT * FROM (
               SELECT StaffID as id,b.PersonNm as label #, 'Program' AS stat, x.StatusCode, x.UserName, x.UserId
               FROM ktv_program_staff a
               INNER JOIN ktv_persons b on a.PersonID=b.PersonID
                             LEFT JOIN sys_user x ON x.UserId=b.UserID
                             WHERE a.StatusCd='active' AND x.StatusCode='active'
               UNION
               SELECT PrivateStaffID  as id,d.PersonNm as label #, 'Private' AS stat, y.StatusCode, y.UserName, y.UserId
               FROM ktv_private_staff c
                             INNER JOIN ktv_persons d ON c.PersonID=d.PersonID
                             LEFT JOIN sys_user y ON y.UserId=d.UserID
                             WHERE c.StatusCode='active' AND y.StatusCode='active'
            ) a ORDER BY label";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFasilitatorMitras($workarea) {
        $sql = "
            SELECT %s
            FROM ktv_private_staff a
            JOIN ktv_persons p ON p.PersonID = a.PersonID
            %s";
        //WHERE WorkArea=?";
        $query = $this->db->query(sprintf($sql, "PrivateStaffID as id,PersonNm as label", ''), array($workarea));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readPenyuluhs($kab) {
        //per provinsi
        $sql = "
            SELECT ExtensionID AS id, PersonNm AS label
            FROM ktv_extension_staff a
            #left join ktv_province c on substr(VillageID,1,2)=c.ProvinceID
            LEFT JOIN ktv_persons d ON d.PersonID=a.PersonID ORDER BY label";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function readStaffAccess($id) {
        $sql = "
            select CPGid as id, PersonNm as label
            from ktv_access_cpg a
            left join ktv_program_staff b on a.StaffID=b.StaffID
            left join ktv_persons c ON b.PersonId=c.PersonID
            WHERE CPGid=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readStaff($prov, $cpg) {
        $sql = "
            select b.StaffID as id, PersonNm as label
            from ktv_program_staff b
            left join ktv_access_cpg a on b.StaffID=a.StaffID and CPGid=?
            left join ktv_persons c on b.PersonId=c.PersonID
            WHERE WorkArea=? and CPGid is null";
        $query = $this->db->query($sql, array($cpg, $prov));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createAccess($cpg, $staff) {
        $sql = "
            INSERT INTO ktv_access_cpg(StaffID,CPGid)
            VALUES (?,?)";
        $query = $this->db->query($sql, array($cpg, $staff));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteAccess($staff, $cpg) {
        $sql = "
            DELETE FROM ktv_access_cpg
            WHERE StaffID=(
               SELECT StaffID FROM ktv_program_staff b
               LEFT JOIN ktv_persons c ON b.PersonId=c.PersonID
               WHERE PersonNm=?) and CPGid=?";
        $query = $this->db->query($sql, array($staff, $cpg));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function checkFarmer($training, $farmer) {
        $sql = "
            SELECT FarmerId as id FROM ktv_cpg_batch_trainings_farmers WHERE CpgBatchTrainingID=? and FarmerID=?";
        $query = $this->db->query($sql, array($training, $farmer));
        $result = $query->result_array();
        if ($result[0]['id'] != '')
            $res['data'] = FALSE;
        else
            $res['data'] = TRUE;
        return $res;
    }

    function readPartnerLogo($id) {
        $sql = "
            select a.CpgBatchTrainingID id, c.Photo
            from ktv_cpg_batch_trainings a,
            ktv_cpg_batch b,
            ktv_program_partner c
            where a.CpgBatchID = b.CpgBatchID
            and b.PartnerID = c.PartnerID
            and a.CpgBatchTrainingID =? ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    //compost
    function readComposts($idCpg) {
        //tidak terpakai
        $sql = "
            select CompostID as id,FarmerName as FarmerPIC,Volume,DateStarted
            from ktv_cpg_compost kcc
            left join ktv_farmer kcf on kcc.FarmerID=kcf.FarmerID
            WHERE kcc.CPGid=?
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($idCpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCompostPenjualans($idCompos) {
        $sql = "
            select CompostTransactionID as id,Buyer,Volume,Price,Volume*Price as Total,date(DateTransaction) as DateTransaction
            from ktv_compost_transaction kcct
            WHERE CompostID=? AND kcct.StatusCode != 'nullified'
            ORDER BY Buyer";
        $query = $this->db->query($sql, array($idCompos));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCompostPetani($idCpg) {
        $sql = "
            select FarmerID as id,FarmerName as label
            from ktv_farmer
            WHERE CPGid=?
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($idCpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createCompost($ObjType, $ObjID, $Established, $MesinChooper, $RumahKompos, $latitude, $longitude, $userid) {
        $sql = "
            insert into ktv_compost (ObjType,ObjID, Established, MesinChooper, RumahKompos, Latitude, Longitude, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($ObjType, $ObjID, $Established, $MesinChooper, $RumahKompos, $latitude, $longitude, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function readCompost($type, $id) {
        $sql = "
            select kcc.*,FarmerName as FarmerPIC
            from ktv_compost kcc
            left join ktv_farmer kcf on kcc.FarmerID=kcf.FarmerID
            WHERE kcc.CompostID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function updateCompost($ObjType, $ObjID, $Established, $MesinChooper, $RumahKompos, $latitude, $longitude, $userid, $id) {
        $sql = "
            update ktv_compost
            set ObjType=?,ObjID=?, Established=?, MesinChooper=?, RumahKompos=?, Latitude=?, Longitude=?, DateUpdated=now(), LastModifiedBy=?
            where CompostID=?";
        $query = $this->db->query($sql, array($ObjType, $ObjID, $Established, $MesinChooper, $RumahKompos, $latitude, $longitude, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteCompost($id) {
        $sql = "
            delete from ktv_compost where CompostID=?";
        $sql_penjualan = "
            delete from ktv_compost_transaction where CompostID=?";
        $query = $this->db->query($sql_penjualan, array($id));
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function createCompostPenjualan($CompostID, $Buyer, $Volume, $Price, $DateTransaction, $userid) {
        $sql = "
            insert into ktv_compost_transaction (CompostID, Buyer, Volume, Price, DateTransaction, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($CompostID, $Buyer, $Volume, $Price, $DateTransaction, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateCompostPenjualan($CompostID, $Buyer, $Volume, $Price, $DateTransaction, $userid, $id) {
        $tgl = explode('T', $DateTransaction);
        $sql = "
            update ktv_compost_transaction
            set CompostID=?, Buyer=?, Volume=?, Price=?, DateTransaction=?, DateUpdated=now(), LastModifiedBy=?
            where CompostTransactionID=?";
        $query = $this->db->query($sql, array($CompostID, $Buyer, $Volume, $Price, $tgl[0], $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteCompostPenjualan($id) {
        //$sql = "delete from ktv_compost_transaction where CompostTransactionID=?";
        $sql = "UPDATE ktv_compost_transaction SET StatusCode = 'nullified',LastModifiedBy='" . $_SESSION['userId'] . "', DateUpdated = NOW() WHERE CompostTransactionID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    //end compost
    //nursey

    function getNurseryNrCombo($CPGId) {
        $sql = "SELECT
                    NurseryNr AS id,
                    NurseryNr AS label
                FROM
                    ktv_nursery
                WHERE
                    StatusCode != 'nullified' AND
                    ObjType = 'cpg' AND
                    ObjID = ?
                ORDER BY NurseryNr ASC";
        $query = $this->db->query($sql, array($CPGId));
        return $query->result_array();
    }

    function readNurseys($idCpg) {
        // tidak terpakai
        $sql = "
            select NurseryID as id,FarmerName as FarmerPIC,Volume,DateStarted
            from ktv_nursery kcc
            left join ktv_farmer kcf on kcc.FarmerID=kcf.FarmerID
            WHERE kcc.CPGid=?
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($idCpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readNurseyPetani($idCpg) {
        $sql = "
            select FarmerID as id,FarmerName as label
            from ktv_farmer
            WHERE CPGid=?
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($idCpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createNursey($CPGId, $ObjType, $ObjID, $Responsible, $Established, $Panjang, $Lebar, $Kapasitas, $Latitude, $Longitude, $LatitudeDeg1, $LatitudeDeg2, $LatitudeDeg3, $LongitudeDeg1, $LongitudeDeg2, $LongitudeDeg3, $NursCertBp2YaTidak, $tglCertificate, $DateAppliedCertification, $Photo, $userid, $varNurseryCeklist, $paramResponsible) {
        //generate NurseryNr
        $sql = "SELECT
                    NurseryNr
                FROM
                    ktv_nursery
                WHERE
                    ObjType = 'cpg' AND
                    ObjID = ?
                ORDER BY NurseryNr DESC LIMIT 1";
        $query = $this->db->query($sql, array($CPGId));
        $data = $query->row_array();
        if ($data['NurseryNr'] == "") {
            $NurseryNr = 1;
        } else {
            $NurseryNr = $data['NurseryNr'] + 1;
        }

        $DistrictID = substr($CPGId, 0, 4);

        $sql = "
            INSERT into ktv_nursery (ObjType,ObjID, NurseryNr, Established, Panjang,Lebar, Kapasitas,Latitude,Longitude,
            LatitudeDeg1,LatitudeDeg2,LatitudeDeg3,LongitudeDeg1,LongitudeDeg2,LongitudeDeg3, CertificationStatus, DateCertification, DateAppliedCertification, Photo, DateCreated, CreatedBy,
                ResponsibleType,
                Responsible,
                ResponsibleName,
                ResponsibleBirthday,
                ResponsiblePhone,
                ResponsibleGender,
                ResponsiblePhoto,
                DistrictID
            )
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,IF(?='',NULL,?), IF(?='',NULL,?), IF(?='',NULL,?),IF(?='',NULL,?),now(),?,?,?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($ObjType, $CPGId, $NurseryNr, $Established, $Panjang, $Lebar, $Kapasitas, $Latitude, $Longitude,
            $LatitudeDeg1, $LatitudeDeg2, $LatitudeDeg3, $LongitudeDeg1, $LongitudeDeg2, $LongitudeDeg3, $NursCertBp2YaTidak, $NursCertBp2YaTidak, $tglCertificate, $tglCertificate, $DateAppliedCertification, $DateAppliedCertification,$Photo,$Photo,$userid,
            $paramResponsible['nurResponsibleType'],
            $paramResponsible['Responsible'],
            $paramResponsible['nurResponsibleName'],
            $paramResponsible['nurResponsibleBirthday'],
            $paramResponsible['nurResponsiblePhone'],
            $paramResponsible['nurResponsibleGender'],
            $paramResponsible['Photo_old_responsible'],
            $DistrictID
            ));

        if ($query) {

            //update nursery ceklist (begin)
            $NurseryID = $this->db->insert_id();

            $sql="UPDATE `ktv_nursery` SET
              `LocationCloseToCommunity` = ?,
              `LocationCloseToCommunityNo` = ?,
              `GoodLandArea` = ?,
              `GoodLandAreaNo` = ?,
              `LocationNearCocoaFarm` = ?,
              `LocationNearCocoaFarmNo` = ?,
              `ContinuousWaterSupply` = ?,
              `ContinuousWaterSupplyNo` = ?,
              `IrrigationInstalled` = ?,
              `IrrigationInstalledNo` = ?,
              `UseShadingNet` = ?,
              `UseShadingNetNo` = ?,
              `AdequateSupplyTopSoil` = ?,
              `AdequateSupplyTopSoilNo` = ?,
              `ImprovedVariety` = ?,
              `ImprovedVarietyNo` = ?,
              `ConstructStoring` = ?,
              `ConstructStoringNo` = ?,
              `CorrectEquipment` = ?,
              `CorrectEquipmentNo` = ?,
              `WindBreakInstalled` = ?,
              `WindBreakInstalledNo` = ?,
              `SecurityFenceInstalled` = ?,
              `SecurityFenceInstalledNo` = ?,
              `FertilizerUsed` = ?,
              `FertilizerUsedNo` = ?,
              `OperatorAdequateTraining` = ?,
              `OperatorAdequateTrainingNo` = ?,
              `AdequateFacility` = ?,
              `AdequateFacilityNo` = ?,
              `SustainablePestDisease` = ?,
              `SustainablePestDiseaseNo` = ?,
              `CloneGrading` = ?,
              `CloneGradingNo` = ?,
              `SeedlingCullingDone` = ?,
              `SeedlingCullingDoneNo` = ?,
              `ProperInputSalesRecord` = ?,
              `ProperInputSalesRecordNo` = ?,
              `SeedsPreGerminated` = ?,
              `SeedsPreGerminatedNo` = ?
            WHERE
                `NurseryID` = ?
            LIMIT 1";
            $p = array(
                  $varNurseryCeklist['LocationCloseToCommunity'],
                  $varNurseryCeklist['LocationCloseToCommunityNo'],
                  $varNurseryCeklist['GoodLandArea'],
                  $varNurseryCeklist['GoodLandAreaNo'],
                  $varNurseryCeklist['LocationNearCocoaFarm'],
                  $varNurseryCeklist['LocationNearCocoaFarmNo'],
                  $varNurseryCeklist['ContinuousWaterSupply'],
                  $varNurseryCeklist['ContinuousWaterSupplyNo'],
                  $varNurseryCeklist['IrrigationInstalled'],
                  $varNurseryCeklist['IrrigationInstalledNo'],
                  $varNurseryCeklist['UseShadingNet'],
                  $varNurseryCeklist['UseShadingNetNo'],
                  $varNurseryCeklist['AdequateSupplyTopSoil'],
                  $varNurseryCeklist['AdequateSupplyTopSoilNo'],
                  $varNurseryCeklist['ImprovedVariety'],
                  $varNurseryCeklist['ImprovedVarietyNo'],
                  $varNurseryCeklist['ConstructStoring'],
                  $varNurseryCeklist['ConstructStoringNo'],
                  $varNurseryCeklist['CorrectEquipment'],
                  $varNurseryCeklist['CorrectEquipmentNo'],
                  $varNurseryCeklist['WindBreakInstalled'],
                  $varNurseryCeklist['WindBreakInstalledNo'],
                  $varNurseryCeklist['SecurityFenceInstalled'],
                  $varNurseryCeklist['SecurityFenceInstalledNo'],
                  $varNurseryCeklist['FertilizerUsed'],
                  $varNurseryCeklist['FertilizerUsedNo'],
                  $varNurseryCeklist['OperatorAdequateTraining'],
                  $varNurseryCeklist['OperatorAdequateTrainingNo'],
                  $varNurseryCeklist['AdequateFacility'],
                  $varNurseryCeklist['AdequateFacilityNo'],
                  $varNurseryCeklist['SustainablePestDisease'],
                  $varNurseryCeklist['SustainablePestDiseaseNo'],
                  $varNurseryCeklist['CloneGrading'],
                  $varNurseryCeklist['CloneGradingNo'],
                  $varNurseryCeklist['SeedlingCullingDone'],
                  $varNurseryCeklist['SeedlingCullingDoneNo'],
                  $varNurseryCeklist['ProperInputSalesRecord'],
                  $varNurseryCeklist['ProperInputSalesRecordNo'],
                  $varNurseryCeklist['SeedsPreGerminated'],
                  $varNurseryCeklist['SeedsPreGerminatedNo'],
                  $NurseryID
            );
            $query = $this->db->query($sql,$p);
            //update nursery ceklist (end)

            $results['success'] = true;
            $results['message'] = "Record created";
            $results['NurseryNr'] = $NurseryNr;
            $results['prosesnya'] = 'insert';
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function readNursey($id) {
        //tidak terpakai
        $sql = "
            select kcc.*,FarmerName as FarmerPIC
            from ktv_cpg_nursery kcc
            left join ktv_farmer kcf on kcc.FarmerID=kcf.FarmerID
            WHERE kcc.NurseryID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function updateNursey($ObjType, $ObjID, $Responsible, $Established, $Panjang, $Lebar, $Kapasitas, $Latitude, $Longitude, $LatitudeDeg1, $LatitudeDeg2, $LatitudeDeg3, $LongitudeDeg1, $LongitudeDeg2, $LongitudeDeg3, $NursCertBp2YaTidak, $tglCertificate, $userid, $id, $Photo, $varNurseryCeklist, $paramResponsible) {

        $DistrictID = substr($ObjID, 0, 4);

        $sql = "
            update ktv_nursery
            set ObjType=?,ObjID=?, Established=?, Panjang=?,Lebar=?, Kapasitas=?, Latitude=?, Longitude=?,
            LatitudeDeg1=?, LatitudeDeg2=?, LatitudeDeg3=?, LongitudeDeg1=?, LongitudeDeg2=?, LongitudeDeg3=?, CertificationStatus=IF(?='',NULL,?), DateCertification = IF(?='',NULL,?), Photo = IF(?='',NULL,?), DateUpdated=now(), LastModifiedBy=?,
                ResponsibleType = ?,
                Responsible = ?,
                ResponsibleName = ?,
                ResponsibleBirthday = ?,
                ResponsiblePhone = ?,
                ResponsibleGender = ?,
                ResponsiblePhoto = ?,
                DistrictID = ?
            where NurseryID=?";
        $query = $this->db->query($sql, array($ObjType, $ObjID, $Established, $Panjang, $Lebar, $Kapasitas, $Latitude, $Longitude,
            $LatitudeDeg1, $LatitudeDeg2, $LatitudeDeg3, $LongitudeDeg1, $LongitudeDeg2, $LongitudeDeg3, $NursCertBp2YaTidak, $NursCertBp2YaTidak, $tglCertificate, $tglCertificate, $Photo, $Photo, $userid,
                $paramResponsible['nurResponsibleType'],
                $paramResponsible['Responsible'],
                $paramResponsible['nurResponsibleName'],
                $paramResponsible['nurResponsibleBirthday'],
                $paramResponsible['nurResponsiblePhone'],
                $paramResponsible['nurResponsibleGender'],
                $paramResponsible['Photo_old_responsible'],
                $DistrictID,
            $id));

        if ($query) {
            $sql="UPDATE `ktv_nursery` SET
              `LocationCloseToCommunity` = ?,
              `LocationCloseToCommunityNo` = ?,
              `GoodLandArea` = ?,
              `GoodLandAreaNo` = ?,
              `LocationNearCocoaFarm` = ?,
              `LocationNearCocoaFarmNo` = ?,
              `ContinuousWaterSupply` = ?,
              `ContinuousWaterSupplyNo` = ?,
              `IrrigationInstalled` = ?,
              `IrrigationInstalledNo` = ?,
              `UseShadingNet` = ?,
              `UseShadingNetNo` = ?,
              `AdequateSupplyTopSoil` = ?,
              `AdequateSupplyTopSoilNo` = ?,
              `ImprovedVariety` = ?,
              `ImprovedVarietyNo` = ?,
              `ConstructStoring` = ?,
              `ConstructStoringNo` = ?,
              `CorrectEquipment` = ?,
              `CorrectEquipmentNo` = ?,
              `WindBreakInstalled` = ?,
              `WindBreakInstalledNo` = ?,
              `SecurityFenceInstalled` = ?,
              `SecurityFenceInstalledNo` = ?,
              `FertilizerUsed` = ?,
              `FertilizerUsedNo` = ?,
              `OperatorAdequateTraining` = ?,
              `OperatorAdequateTrainingNo` = ?,
              `AdequateFacility` = ?,
              `AdequateFacilityNo` = ?,
              `SustainablePestDisease` = ?,
              `SustainablePestDiseaseNo` = ?,
              `CloneGrading` = ?,
              `CloneGradingNo` = ?,
              `SeedlingCullingDone` = ?,
              `SeedlingCullingDoneNo` = ?,
              `ProperInputSalesRecord` = ?,
              `ProperInputSalesRecordNo` = ?,
              `SeedsPreGerminated` = ?,
              `SeedsPreGerminatedNo` = ?
            WHERE
                `NurseryID` = ?
            LIMIT 1";
            $p = array(
                  $varNurseryCeklist['LocationCloseToCommunity'],
                  $varNurseryCeklist['LocationCloseToCommunityNo'],
                  $varNurseryCeklist['GoodLandArea'],
                  $varNurseryCeklist['GoodLandAreaNo'],
                  $varNurseryCeklist['LocationNearCocoaFarm'],
                  $varNurseryCeklist['LocationNearCocoaFarmNo'],
                  $varNurseryCeklist['ContinuousWaterSupply'],
                  $varNurseryCeklist['ContinuousWaterSupplyNo'],
                  $varNurseryCeklist['IrrigationInstalled'],
                  $varNurseryCeklist['IrrigationInstalledNo'],
                  $varNurseryCeklist['UseShadingNet'],
                  $varNurseryCeklist['UseShadingNetNo'],
                  $varNurseryCeklist['AdequateSupplyTopSoil'],
                  $varNurseryCeklist['AdequateSupplyTopSoilNo'],
                  $varNurseryCeklist['ImprovedVariety'],
                  $varNurseryCeklist['ImprovedVarietyNo'],
                  $varNurseryCeklist['ConstructStoring'],
                  $varNurseryCeklist['ConstructStoringNo'],
                  $varNurseryCeklist['CorrectEquipment'],
                  $varNurseryCeklist['CorrectEquipmentNo'],
                  $varNurseryCeklist['WindBreakInstalled'],
                  $varNurseryCeklist['WindBreakInstalledNo'],
                  $varNurseryCeklist['SecurityFenceInstalled'],
                  $varNurseryCeklist['SecurityFenceInstalledNo'],
                  $varNurseryCeklist['FertilizerUsed'],
                  $varNurseryCeklist['FertilizerUsedNo'],
                  $varNurseryCeklist['OperatorAdequateTraining'],
                  $varNurseryCeklist['OperatorAdequateTrainingNo'],
                  $varNurseryCeklist['AdequateFacility'],
                  $varNurseryCeklist['AdequateFacilityNo'],
                  $varNurseryCeklist['SustainablePestDisease'],
                  $varNurseryCeklist['SustainablePestDiseaseNo'],
                  $varNurseryCeklist['CloneGrading'],
                  $varNurseryCeklist['CloneGradingNo'],
                  $varNurseryCeklist['SeedlingCullingDone'],
                  $varNurseryCeklist['SeedlingCullingDoneNo'],
                  $varNurseryCeklist['ProperInputSalesRecord'],
                  $varNurseryCeklist['ProperInputSalesRecordNo'],
                  $varNurseryCeklist['SeedsPreGerminated'],
                  $varNurseryCeklist['SeedsPreGerminatedNo'],
                  $id
            );
            $query = $this->db->query($sql,$p);
            //update nursery ceklist (end)

            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteNursey($id) {
        $sql = "delete from ktv_nursery where NurseryID=?";
        $sql_penjualan = "
            delete from ktv_nursery_transaction where NurseryID=?";
        $query = $this->db->query($sql_penjualan, array($id));
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function getNurseryPolygonArea($NurseryID, $latitude, $longitude) {
        $sql = "SELECT
                Latitude,
                Longitude
            FROM
                ktv_nursery_area
            WHERE
                NurseryID = ?
            ORDER BY OrderNr ASC";
        $query = $this->db->query($sql, array($NurseryID));

        if ($query->num_rows() > 0) {
            $result = "[";
            $no = 0;
            foreach ($query->result() as $row) {
                if ($no != 0) {
                    $result .= ",";
                }
                $result .= "[";
                $result .= $row->Latitude;
                $result .= ",";
                $result .= $row->Longitude;
                $result .= "]";
                $no++;
            }
            $result .= "]";
            return $result;
        } else {
            if (($latitude != '0.000000' || $longitude != '0.000000') && ($latitude != '' || $longitude != '')) {
                return "[[$latitude,$longitude]]";
            } else {
                return "[[-1.2674336,113.6939433]]";
            }
        }
    }

    public function updateNurseryPolygon($NurseryID, $NurseryNr, $area, $luas, $lat, $long) {
        $result = false;

        if ($luas == '0.00') {
            $lat = null;
            $long = null;
        }

        $this->db->trans_start(FALSE);

        //hapus datanya terlebih dahulu
        $sql = "DELETE FROM ktv_nursery_area WHERE NurseryID = ?";
        $query = $this->db->query($sql, array($NurseryID));

        // insert new area
        if (is_array($area)) {
            $no = 1;
            $data = array();
            foreach ($area as $val) {
                $data[] = array(
                    'NurseryID' => $NurseryID,
                    'NurseryNr' => $NurseryNr,
                    'OrderNr' => $no,
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'CreatedBy' => $_SESSION['userid'],
                    'Latitude' => $val[0],
                    'Longitude' => $val[1]
                );
                $no++;
            }
            $this->db->insert_batch('ktv_nursery_area', $data);
            $sql = "UPDATE ktv_nursery SET Area=?,Latitude=?,Longitude=? WHERE NurseryID=? AND NurseryNr=?";
            $query = $this->db->query($sql, array($luas, $lat, $long, $NurseryID, $NurseryNr));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Success.";
        } else {
            $results['success'] = false;
            $results['message'] = "Error. Please reload page and try again.";
        }
        return $results;
    }

    public function updateNurseryPolygonCenter($NurseryID, $NurseryNr, $lat, $long) {
        $sql = "SELECT * FROM ktv_nursery WHERE NurseryID=? AND NurseryNr=?";
        $query = $this->db->query($sql, array($NurseryID, $NurseryNr));

        if ($lat)
            if ((@$query->row()->Latitude == '' || @$query->row()->Latitude == '0.000000') && (@$query->row()->Longitude == '' || @$query->row()->Longitude == '0.000000')) {
                $sql = "UPDATE ktv_nursery SET Latitude=?, Longitude=? WHERE NurseryID=? AND NurseryNr=?";
                $query = $this->db->query($sql, array($lat, $long, $NurseryID, $NurseryNr));
                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "Success.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Error. Please reload page and try again.";
                }
                return $results;
            } else {
                $results['success'] = true;
                $results['message'] = "No Update";
                return $results;
            }
    }

    public function updateNurseryAreaGet($NurseryID) {
        $sql = "SELECT Area,Latitude,Longitude FROM ktv_nursery WHERE NurseryID=? LIMIT 1";
        $query = $this->db->query($sql, array($NurseryID));
        $result = $query->result_array();
        return $result[0];
    }

    function readNurseyPenjualans($idCompos) {
        $sql = "select NurseryTransactionID as id,Buyer,Volume,Price,Volume*Price as Total,date(DateTransaction) as DateTransaction,kct.CloneTypeName AS CloneTypeID
            from ktv_nursery_transaction kcct
            LEFT JOIN ktv_clone_type kct ON kcct.CloneTypeID = kct.CloneTypeID
            WHERE NurseryID=? AND kcct.StatusCode != 'nullified'
            ORDER BY Buyer";
        $query = $this->db->query($sql, array($idCompos));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createNurseyPenjualan($NurseyID, $Buyer, $CloneTypeID, $Volume, $Price, $DateTransaction, $userid) {
        $sql = "
            insert into ktv_nursery_transaction (NurseryID, Buyer, CloneTypeID, Volume, Price, DateTransaction, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($NurseyID, $Buyer, $CloneTypeID, $Volume, $Price, $DateTransaction, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateNurseyPenjualan($NurseyID, $Buyer, $CloneTypeID, $Volume, $Price, $DateTransaction, $userid, $id) {
        $tgl = explode('T', $DateTransaction);
        $sql = "
            update ktv_nursery_transaction
            set NurseryID=?, Buyer=?, CloneTypeID=?, Volume=?, Price=?, DateTransaction=?, DateUpdated=now(), LastModifiedBy=?
            where NurseryTransactionID=?";
        $query = $this->db->query($sql, array($NurseyID, $Buyer, $CloneTypeID, $Volume, $Price, $tgl[0], $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteNurseyPenjualan($id) {
        //$sql = "delete from ktv_nursery_transaction where NurseryTransactionID=?";
        $sql = "UPDATE ktv_nursery_transaction SET StatusCode = 'nullified', LastModifiedBy='" . $_SESSION['userid'] . "', DateUpdated = NOW() WHERE NurseryTransactionID = ? LIMIT 1";

        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function readNurseyMonitorings($id) {
        $sql = "SELECT NurseryMonitoringID as id,MonitoringDate,MonitoringStatus,Description
                FROM ktv_nursery_monitoring
                WHERE NurseryID=? AND StatusCode != 'nullified'
                ORDER BY MonitoringDate";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createNurseyMonitoring($NurseyID, $MonitoringDate, $MonitoringStatus, $Description, $userid) {
        $sql = "INSERT INTO ktv_nursery_monitoring
                (NurseryID,MonitoringDate,MonitoringStatus,Description,DateCreated,CreatedBy)
               VALUES (?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($NurseyID, $MonitoringDate, $MonitoringStatus, $Description, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateNurseyMonitoring($id, $NurseyID, $MonitoringDate, $MonitoringStatus, $Description, $userid) {
        $tgl = explode('T', $MonitoringDate);
        $sql = "UPDATE ktv_nursery_monitoring
                SET
                    NurseryID=?,
                    MonitoringDate=?,
                    MonitoringStatus=?,
                    Description=?,
                    DateUpdated=now(),
                    LastModifiedBy=?
                WHERE NurseryMonitoringID=?";
        $query = $this->db->query($sql, array($NurseyID, $tgl[0], $MonitoringStatus, $Description, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteNurseyMonitoring($id) {
        //$sql = "DELETE FROM ktv_nursery_monitoring WHERE NurseryMonitoringID=?";
        $sql = "UPDATE ktv_nursery_monitoring SET StatusCode = 'nullified', LastModifiedBy = '" . $_SESSION['userid'] . "',DateUpdated = NOW() WHERE NurseryMonitoringID = ? LIMIT 1";

        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end nursey
    //staff
    function readStaffCpg($cpg) {
        $sql = "
            select StaffID,a.CPGid,IF(a.FarmerID<1,'Non Farmer','Farmer') Status,a.FarmerID,
               IF(a.FarmerID<1,a.StaffName,concat('[',a.FarmerID,'] ',FarmerName)) StaffName,
               Position,IF(a.FarmerID<1,Phone,HandPhone) Phone,Email,StaffBirthday,IF(a.StaffGender='1','Laki-laki','Perempuan') StaffGender
            from ktv_cpg_staff a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            WHERE a.CPGid=? AND a.StatusCode != 'nullified'";
        $query = $this->db->query($sql, array($cpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readStaffCpgFarmer($cpg, $query) {
        $sql = "
            select FarmerID id,FarmerName name,HandPhone handphone,'' email,Birthdate birthdate,Gender kelamin
            from ktv_farmer a
            WHERE a.CPGid=? and FarmerName like ?";
        $query = $this->db->query($sql, array($cpg, "%$query%"));
        $result['data'] = $query->result_array();
        return $result;
    }

    function deleteStaffCpg($id) {
        //$sql = "delete from ktv_cpg_staff where StaffID=?";
        $sql = "UPDATE ktv_cpg_staff SET StatusCode='nullified',LastModifiedBy='" . $_SESSION['userid'] . "',DateUpdated=NOW() WHERE StaffID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function createStaffCpg($CPGid, $StaffName, $FarmerID, $Status, $Position, $Phone, $Email, $StaffBirthday, $StaffGender, $userid) {
        $this->db->trans_start();
        $sql_user = "
             INSERT INTO sys_user(UserRealName,UserName,UserActive)
             VALUES (?,?,?)";
        $query = $this->db->query($sql_user, array($StaffName, $Email, 'No'));
        $UserId = $this->db->insert_id();
        $sql = "
            insert into ktv_cpg_staff (CPGid, StaffName, FarmerID, Status, UserId, Position, Phone, Email,
	            StaffBirthday, StaffGender, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,?,   ?,?,now(),?)";
        $tgl = explode('T', $StaffBirthday);
        $query = $this->db->query($sql, array($CPGid, $StaffName, $FarmerID, $Status, $UserId, $Position, $Phone, $Email,
            $tgl[0], $StaffGender, $userid));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateStaffCpg($CPGid, $StaffName, $FarmerID, $Status, $Position, $Phone, $Email, $StaffBirthday, $StaffGender, $userid, $id) {
        /* if ($Status=='Farmer') {
          if (strpos($StaffName,'[') and $FarmerID=='') {
          $StaffNam = explode('[',$StaffName);
          $StaffNa = explode(']',$StaffNam[1]);
          $FarmerID = '12121';//$StaffNa[0];
          }
          } */
        $sql = "
            update ktv_cpg_staff
            set CPGid=?, StaffName=?, FarmerID=?, Status=?, Position=?, Phone=?, Email=?,
	            StaffBirthday=?, StaffGender=?, DateUpdated=now(), LastModifiedBy=?
            where StaffID=?";
        $tgl = explode('T', $StaffBirthday);
        $query = $this->db->query($sql, array($CPGid, $StaffName, $FarmerID, $Status, $Position, $Phone, $Email,
            $tgl[0], $StaffGender, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function readCpgMember($id) {
        $sql = "SELECT
                    a.FarmerID,
                    a.FarmerName,
                    IF(a.Gender=1,'Laki-laki','Perempuan') as cpgMemberGender,
                    b.Village as cpgMemberVillage,
                    YEAR(CURRENT_TIMESTAMP) - YEAR(a.Birthdate) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(a.Birthdate, 5)) as cpgMemberAge
                    ,g.garden_count
                    ,g.garden_ha
                FROM ktv_farmer a
                JOIN ktv_village b ON a.VillageID = b.VillageID
                LEFT JOIN (
                SELECT
    g.FarmerID,
    COUNT(g.FarmerID) AS garden_count,
    SUM(g.GardenHaUnCertified) AS garden_ha
FROM ktv_farmer_garden g
JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
GROUP BY FarmerID
                ) g ON g.FarmerID = a.FarmerID
                WHERE 1 = 1
                    AND a.CPGid=?";

        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getParticipantDetail($CpgBatchTrainingsFarmerID) {
        $sql = "SELECT
    btf.`FarmerID`,
    f.`FarmerName`,
    cpg.`GroupName`,
    bt.TrainingDays,
    DATE(bt.TrainingStart) AS TrainingStart,
    DATE(bt.TrainingEnd) AS TrainingEnd,
    bt.TrainingDayStatus
FROM `ktv_cpg_batch_trainings_farmers` AS btf
JOIN ktv_farmer f ON f.`FarmerID` = btf.`FarmerID`
JOIN ktv_cpg cpg ON cpg.`CPGid` = f.`CPGid`
JOIN `ktv_cpg_batch_trainings` bt ON bt.CpgBatchTrainingID = btf.`CpgBatchTrainingID`
WHERE
    btf.`CpgBatchTrainingsFarmerID` = ?
        ";
        $query = $this->db->query($sql, array($CpgBatchTrainingsFarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function generateFarmerAttendance($CpgBatchTrainingID, $FarmerID) {
        $query = $this->db->get_where('ktv_cpg_batch_trainings', array('CpgBatchTrainingID' => $CpgBatchTrainingID));
        $detail = $query->row_array(0);

        $attendance = array();
        for ($i = 1; $i <= $detail['TrainingDays']; $i++) {
            $attendance[] = array(
                'CpgBatchTrainingID' => $CpgBatchTrainingID,
                'FarmerID' => $FarmerID,
                'DayNumber' => $i,
                'Attendance1' => 0,
                'Attendance2' => 0,
            );
        }
        return $this->db->insert_batch('ktv_cpg_batch_trainings_attendance', $attendance);
    }

    public function getFarmerAttendance($CpgBatchTrainingID, $FarmerID, $DayNumber = '') {
        // $this->db->order_by('DayNumber', 'asc');
        // $query = $this->db->get_where('ktv_cpg_batch_trainings_attendance', array(
        //     'CpgBatchTrainingID' => $CpgBatchTrainingID,
        //     'FarmerID' => $FarmerID,
        // ));
        if ($DayNumber != '') {
            $sql = "
				SELECT
					a.`DayNumber`, a.`SignAttendance1`, IF(a.TrainingDate = '0000-00-00',null,a.TrainingDate) AS TrainingDate,
					IF(a.`Attendance1` = 0 OR a.`Attendance1` IS NULL, '',1)`Attendance1`,
					IF(a.`Attendance2` = 0 OR a.`Attendance2` IS NULL, '',1)`Attendance2`,
					b.LearningContractSign
				FROM
					`ktv_cpg_batch_trainings_attendance` a
					LEFT JOIN ktv_farmer b ON a.FarmerID=b.FarmerID
				WHERE
					a.`CpgBatchTrainingID` = ?
				AND a.`FarmerID` = ? AND a.`DayNumber` = ?";
            $query = $this->db->query($sql, array($CpgBatchTrainingID, $FarmerID, $DayNumber));
        } else {
            $sql = "
				SELECT
					a.`DayNumber`, a.`SignAttendance1`, IF(a.TrainingDate = '0000-00-00',null,a.TrainingDate) AS TrainingDate,
					IF(a.`Attendance1` = 0 OR a.`Attendance1` IS NULL, '',1)`Attendance1`,
					IF(a.`Attendance2` = 0 OR a.`Attendance2` IS NULL, '',1)`Attendance2`,
					b.LearningContractSign
				FROM
					`ktv_cpg_batch_trainings_attendance` a
					LEFT JOIN ktv_farmer b ON a.FarmerID=b.FarmerID
				WHERE
					a.`CpgBatchTrainingID` = ?
				AND a.`FarmerID` = ?";
            $query = $this->db->query($sql, array($CpgBatchTrainingID, $FarmerID));
        }
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            $this->generateFarmerAttendance($CpgBatchTrainingID, $FarmerID);
            return $this->getFarmerAttendance($CpgBatchTrainingID, $FarmerID);
        }
    }

    public function getFarmerAttendanceDay($CpgBatchTrainingID, $DayNumber) {
        $sql = "SELECT
    btf.FarmerID,
    btf.FamilyID,
    f.FarmerName,
    fm.AnggotaName,
    IF(bta.Attendance1 = 0,'',bta.Attendance1) AS Attendance1,
    IF(bta.Attendance2 = 0,'',bta.Attendance2) AS Attendance2
FROM ktv_cpg_batch_trainings_farmers btf
LEFT JOIN ktv_farmer f ON f.FarmerID = btf.FarmerID
LEFT JOIN ktv_cpg_batch_trainings_attendance bta ON bta.CpgBatchTrainingID = btf.`CpgBatchTrainingID` AND bta.FarmerID = btf.FarmerID AND DayNumber = ?
LEFT JOIN ktv_family fm ON fm.FamilyID = bta.FamilyID AND bta.FamilyName = fm.AnggotaName
WHERE
    btf.CpgBatchTrainingID = ?
GROUP BY btf.FarmerID
        ";
        $query = $this->db->query($sql, array($DayNumber, $CpgBatchTrainingID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateFarmerAttendance($CpgBatchTrainingID, $FarmerID, $DayNumber, $Attendance1, $Attendance2, $TrainingDate, $FamilyID = 0, $FamilyName=null) {

        //cek apakah insert / update
        $sql="SELECT
                COUNT(*) AS BANYAK
            FROM
                ktv_cpg_batch_trainings_attendance
            WHERE
                CpgBatchTrainingID = ?
                AND FarmerID = ?
                AND DayNumber = ?";
        $query = $this->db->query($sql,array($CpgBatchTrainingID,$FarmerID,$DayNumber));
        $data = $query->row_array();

        if($data['BANYAK'] > 0){
            //update
            return $this->db->update('ktv_cpg_batch_trainings_attendance', array(
                    'Attendance1' => $Attendance1,
                    'Attendance2' => $Attendance2,
                    'TrainingDate' => $TrainingDate,
                    'FamilyID' => $FamilyID,
                    'FamilyName' => $FamilyName
                        ), array(
                    'CpgBatchTrainingID' => $CpgBatchTrainingID,
                    'FarmerID' => $FarmerID,
                    'DayNumber' => $DayNumber
                        )
            );
        }else{
            //insert
            $sql="INSERT INTO ktv_cpg_batch_trainings_attendance SET
                    CpgBatchTrainingID = ?,
                    FarmerID = ?,
                    DayNumber = ?,
                    Attendance1 = ?,
                    Attendance2 = ?,
                    TrainingDate = ?,
                    FamilyID = ?,
                    FamilyName = ?
                ";
            $p = array(
                $CpgBatchTrainingID,
                $FarmerID,
                $DayNumber,
                $Attendance1,
                $Attendance2,
                $TrainingDate,
                $FamilyID,
                $FamilyName
            );
            return $this->db->query($sql,$p);
        }
    }

    function readCloneRefCombo() {
        $sql = "SELECT
               CloneTypeID AS id,
               CloneTypeName AS label
            FROM
               ktv_clone_type
            ORDER BY CloneTypeID ASC";
        $query = $this->db->query($sql, array($idCpg));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readClonalPenjualans($ClonalID) {
        $sql = "SELECT a.*, b.CloneTypeName, b.CloneTypeID, (Volume*Price) AS Total , ClonalTransactionID AS id, SUBSTR(a.DateTransaction,1,10) AS DateTransaction FROM ktv_clonal_garden_transaction a
				LEFT JOIN ktv_clone_type b ON a.CloneTypeID=b.CloneTypeID WHERE a.ClonalID=? AND a.StatusCode='active'";
        $query = $this->db->query($sql, array($ClonalID));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createClonalPenjualan($ClonalID, $Buyer, $CloneTypeID, $Volume, $Price, $DateTransaction, $userid) {
        $sql = "
            insert into ktv_clonal_garden_transaction (ClonalID, Buyer, CloneTypeID, Volume, Price, DateTransaction, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($ClonalID, $Buyer, $CloneTypeID, $Volume, $Price, $DateTransaction, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateClonalPenjualan($ClonalID, $Buyer, $CloneTypeID, $Volume, $Price, $DateTransaction, $userid, $id) {
        $tgl = explode('T', $DateTransaction);
        $sql = "
            update ktv_clonal_garden_transaction
            set ClonalID=?, Buyer=?, CloneTypeID=?, Volume=?, Price=?, DateTransaction=?, DateUpdated=now(), LastModifiedBy=?
            where ClonalTransactionID=?";
        $query = $this->db->query($sql, array($ClonalID, $Buyer, $CloneTypeID, $Volume, $Price, $tgl[0], $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteClonalPenjualan($id) {
        $sql = "UPDATE ktv_clonal_garden_transaction SET StatusCode = 'nullified', LastModifiedBy='" . $_SESSION['userid'] . "', DateUpdated = NOW() WHERE ClonalTransactionID = ? LIMIT 1";

        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    //**Clonal Garden Monitoring**//
    function readClonalMonitorings($id) {
        $sql = "SELECT ClonalMonitoringID as id,MonitoringDate,MonitoringStatus,Description
                FROM ktv_clonal_garden_monitoring
                WHERE ClonalID=? AND StatusCode != 'nullified'
                ORDER BY MonitoringDate";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createClonalMonitoring($ClonalID, $MonitoringDate, $MonitoringStatus, $Description, $userid) {
        $sql = "INSERT INTO ktv_clonal_garden_monitoring
                (ClonalID,MonitoringDate,MonitoringStatus,Description,DateCreated,CreatedBy)
               VALUES (?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($ClonalID, $MonitoringDate, $MonitoringStatus, $Description, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateClonalMonitoring($id, $ClonalID, $MonitoringDate, $MonitoringStatus, $Description, $userid) {
        $tgl = explode('T', $MonitoringDate);
        $sql = "UPDATE ktv_clonal_garden_monitoring
                SET
                    ClonalID=?,
                    MonitoringDate=?,
                    MonitoringStatus=?,
                    Description=?,
                    DateUpdated=now(),
                    LastModifiedBy=?
                WHERE ClonalMonitoringID=?";
        $query = $this->db->query($sql, array($ClonalID, $tgl[0], $MonitoringStatus, $Description, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteClonalMonitoring($id) {
        //$sql = "DELETE FROM ktv_nursery_monitoring WHERE NurseryMonitoringID=?";
        $sql = "UPDATE ktv_clonal_garden_monitoring SET StatusCode = 'nullified', LastModifiedBy = '" . $_SESSION['userid'] . "',DateUpdated = NOW() WHERE ClonalMonitoringID = ? LIMIT 1";

        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function updateClonalPolygon($ClonalID, $GardenNr, $GardenNr_default, $StatusCode, $area, $luas, $lat, $long) {
        $result = false;
        if ($GardenNr_default != '' && $GardenNr != $GardenNr_default) {
            $sql_check = "SELECT * FROM ktv_clonal_garden_area WHERE ClonalID=? AND GardenNr=?";
            $query = $this->db->query($sql_check, array($ClonalID, $GardenNr));
            if ($query->num_rows() > 0) {
                $results['success'] = 'duplicated';
                $results['message'] = "Error! GardenNr duplicated.";
                return $results;
            }
        }

        if ($luas == '0.00') {
            $lat = null;
            $long = null;
        }

        $this->db->trans_start(FALSE);
        $this->db->where('ClonalID', $ClonalID);
        $this->db->where('GardenNr', $GardenNr);
        $this->db->delete('ktv_clonal_garden_area');

        // insert new area
        if (is_array($area)) {
            $no = 1;
            $data = array();
            foreach ($area as $val) {
                $data[] = array(
                    'ClonalID' => $ClonalID,
                    'GardenNr' => $GardenNr,
                    'OrderNr' => $no,
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'CreatedBy' => $_SESSION['userid'],
                    'Latitude' => $val[0],
                    'Longitude' => $val[1]
                );
                $no++;
            }
            $this->db->insert_batch('ktv_clonal_garden_area', $data);
            $sql = "UPDATE ktv_clonal_garden SET Area=? WHERE ClonalID=? AND GardenNr=?";
            $query = $this->db->query($sql, array($luas, $ClonalID, $GardenNr));
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Success.";
        } else {
            $results['success'] = false;
            $results['message'] = "Error. Please reload page and try again.";
        }
        return $results;
    }

    function updateClonalPolygonCenter($ClonalID, $GardenNr, $lat, $long) {
        $sql = "SELECT * FROM ktv_clonal_garden WHERE ClonalID=? AND GardenNr=?";
        $query = $this->db->query($sql, array($ClonalID, $GardenNr));
        if ($lat)
            if ((@$query->row()->Latitude == '' || @$query->row()->Latitude == '0.000000') && (@$query->row()->Longitude == '' || @$query->row()->Longitude == '0.000000')) {
                $sql = "UPDATE ktv_clonal_garden SET Latitude=?, Longitude=? WHERE ClonalID=? AND GardenNr=?";
                $query = $this->db->query($sql, array($lat, $long, $ClonalID, $GardenNr));
                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "Success.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Error. Please reload page and try again.";
                }
                return $results;
            } else {
                $results['success'] = true;
                $results['message'] = "No Update";
                return $results;
            }
    }

    function getClonalPolygon($ClonalID, $GardenNr, $lat, $long, $cooplat, $cooplong) {
        if ($GardenNr != '') {
            $sql = "SELECT Latitude, Longitude FROM ktv_clonal_garden_area WHERE ClonalID=? AND GardenNr=?";
            $query = $this->db->query($sql, array($ClonalID, $GardenNr));
            //$result = $query->result_array();
            if ($query->num_rows() > 0) {
                $result = "[";
                $no = 0;
                foreach ($query->result() as $row) {
                    if ($no != 0) {
                        $result .= ",";
                    }
                    $result .= "[";
                    $result .= $row->Latitude;
                    $result .= ",";
                    $result .= $row->Longitude;
                    $result .= "]";
                    $no++;
                }
                $result .= "]";
                return $result;
            } else {
                if (($lat != '0.000000' || $long != '0.000000') && ($lat != '' || $long != '')) {
                    return "[[$lat,$long]]";
                } else {
                    if (($cooplat != '0.000000' || $cooplong != '0.000000') && ($cooplat != '' || $cooplong != '')) {
                        return "[[$cooplat,$cooplong]]";
                    } else {
                        return "[[-6.1978097,106.8200402]]";
                    }
                }
            }
        } else {
            return "''";
        }
    }

    function readClonalPolygons($ObjType, $ObjID) {
        $sql = "SELECT ClonalID, GardenNr,StatusCode,Area FROM ktv_clonal_garden WHERE ObjType=? AND ObjID=? AND StatusCode!='nullified' ORDER BY GardenNr";
        $query = $this->db->query($sql, array($ObjType, $ObjID));
        $result['data'] = $query->result_array();
        return $result;
    }

    function deleteClonalPolygon($ObjType, $ObjID, $ClonalID, $GardenNr) {
        $this->db->trans_start();

        $sql = "UPDATE ktv_clonal_garden SET StatusCode='nullified', DateUpdated=NOW(), LastModifiedBy=? WHERE ObjType=? AND ObjID=? AND ClonalID=? AND GardenNr=?";
        $query = $this->db->query($sql, array($_SESSION['userid'], $ObjType, $ObjID, $ClonalID, $GardenNr));

        //$sql = "DELETE FROM ktv_clonal_garden_area WHERE ClonalID=? AND GardenNr=?";
        //$query = $this->db->query($sql, array($ClonalID, $GardenNr ));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function listProvinces() {
        $sql = "SELECT
    p.ProvinceID AS id
    , p.Province AS label
FROM
    ktv_province p
JOIN ktv_district d ON d.ProvinceID = p.ProvinceID
WHERE
    p.StatusCode = 'active'
    --where--
GROUP BY label
        ";
        $where = '';
        if ($_SESSION['is_admin'] != 1) {
            $where = " AND d.DistrictID IN (SELECT DistrictID FROM ktv_access_staff WHERE UserId = {$_SESSION['userid']})";
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function listDistricts($ProvinceID) {
        $sql = "SELECT
    d.DistrictID AS id
    , d.District AS label
FROM ktv_district d
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
WHERE
    d.StatusCode = 'active'
    --where--
GROUP BY label
        ";
        $where = '';
        if ($_SESSION['is_admin'] != 1) {
            $where .= " AND d.DistrictID IN (SELECT DistrictID FROM ktv_access_staff WHERE UserId = {$_SESSION['userid']})";
        }
        if (!empty($ProvinceID)) {
            $where .= " AND (p.ProvinceID = '{$ProvinceID}' OR p.Province = '{$ProvinceID}')";
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getFamilyID($FarmerID, $AnggotaName) {
        $sql = "SELECT
    FamilyID
FROM
    ktv_family
WHERE
    FarmerID = ?
    AND AnggotaName = ?
        ";
        $query = $this->db->query($sql, array($FarmerID, $AnggotaName));
        if ($query->num_rows() > 0) {
            $result = $query->row_array(0);
            return $result['FamilyID'];
        }
        return false;
    }

    public function getMasterFarmerGroups($PartnerID) {
        $sql = "
              SELECT
                a.CPGid,
                a.GroupName,
                c.SubDistrict,
                d.District
            FROM
                ktv_cpg a
            LEFT JOIN ktv_village b ON a.VillageID = b.VillageID
            LEFT JOIN ktv_subdistrict c ON c.SubDistrictID = b.SubDistrictID
            LEFT JOIN ktv_district d ON d.DistrictID = c.DistrictID
            INNER JOIN ktv_cpg_partner e ON a.`CPGid` = e.`CPGid`
            WHERE
                a.`Status` = 'active'
                AND e.`PartnerID` = ?
                AND a.OwnerClientID = ?
        ";
        $query = $this->db->query($sql, array($PartnerID,$PartnerID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getResponByType($responsibleType,$CPGid){
        if($responsibleType == 'farmer'){
            $sql="SELECT
                    a.`FarmerID` AS id,
                    CONCAT(a.`FarmerID`,' - ',a.`FarmerName`) AS label
                FROM
                    ktv_farmer a
                WHERE
                    a.`CPGid` = ? AND
                    a.`StatusCode` = 'active'
                ORDER BY a.`FarmerID` ASC";
            $query = $this->db->query($sql,array($CPGid));
        }

        if($responsibleType == 'staff'){
            $sql="SELECT
                    a.`StaffID` AS id,
                    CONCAT('[',a.`StaffID`,'] ',b.`PersonNm`,' - ',IFNULL(d.`PositionName`,'No Position')) AS label
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                    LEFT JOIN `ktv_staff_positions` c ON a.`StaffID` = c.StaffPosStaffID
                        AND CURDATE() BETWEEN c.`StaffPostStart` AND c.`StaffPostEnd`
                        AND c.StatusCode = 'active'
                    LEFT JOIN `ktv_ref_position_type` d ON c.`StaffPosPositionID` = d.`PositionID`
                WHERE
                    a.`ObjType` = 'farmergroup'
                    AND a.`StatusCode` = 'active'
                    AND b.`PersonNm` != ''
                    AND a.ObjID = ?
                ORDER BY b.`PersonNm` ASC";
            $query = $this->db->query($sql,array($CPGid));
        }

        $data = $query->result_array();
        $return['data'] = $data;
        return $return;
    }

    public function getAssPartnerList($CPGid){
        $sql="SELECT
                a.`PartnerID`,
                a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`PartnerID` NOT IN (20,30)
                AND a.`PartnerID` != (
                    SELECT
                        suba.`OwnerClientID`
                    FROM
                        ktv_cpg suba
                    WHERE
                        suba.`CPGid` = ?
                    LIMIT 1
                )";
        $query = $this->db->query($sql,array($CPGid));
        $data = $query->result_array();

        $return['data'] = $data;
        return $return;
    }

    public function getAssPartnerFormByCpg($CPGid){
        $sql="SELECT
                b.PartnerID,
                b.`PartnerName` AS LabelSubmittedBy
            FROM
                ktv_cpg a
                INNER JOIN ktv_program_partner b ON a.`OwnerClientID` = b.`PartnerID`
            WHERE
                a.`CPGid` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($CPGid));
        $data = $query->row_array();
        $dataReturn[0]['LabelSubmittedBy'] = $data['LabelSubmittedBy'];
        $dataReturn[0]['AssPartPartnerID'] = $data['PartnerID'];
        $PartnerID = $data['PartnerID'];

        //cpg_partner
        $sql="SELECT
                a.`PartnerID`
            FROM
                ktv_cpg_partner a
            WHERE
                a.`CPGid` = ? AND
                a.`PartnerID` != ?";
        $query = $this->db->query($sql,array($CPGid,$PartnerID));
        $data = $query->result_array();

        $arrTmp = array();
        foreach ($data as $key => $value) {
            $arrTmp[] = $value['PartnerID'];
        }
        $dataReturn[0]['cmbAssignPartner'] = $arrTmp;

        $return['success'] = true;
        $return['data'] = $dataReturn[0];
        return $return;
    }

    public function saveAssPartner($varPost){
        $this->db->trans_start();

        //hapus dl datanya
        $sql="DELETE FROM ktv_cpg_partner
            WHERE
                CPGid = ? AND
                PartnerID != ?";
        $query = $this->db->query($sql,array($varPost['AssPartCPGid'],$varPost['AssPartPartnerID']));

        //insert kembali
        if($varPost['cmbAssignPartner'] != ""){
            $arrTmp = explode(",",$varPost['cmbAssignPartner']);
            foreach ($arrTmp as $key => $value) {
                $sql="INSERT INTO `ktv_cpg_partner` (`CPGid`, `PartnerID`) VALUES (?,?)";
                $query = $this->db->query($sql,array($varPost['AssPartCPGid'],$value));
            }
        }

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
}
?>
