<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-30 16:53:25
 */
class Mreceipt_setting extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }

    public function getPropinsi($filter_prov){
        //hak akses data
        if($_SESSION['is_admin'] == "1"){
            //no filter
        }else{
            //add filter
            if (!empty($this->user['district_access'])) {
                $sqlWhereAccess .= " AND b.DistrictID IN ({$this->user['district_access']})";
            }
        }

        $sql = "SELECT
                    DISTINCT a.Province AS label, a.ProvinceID AS id
                FROM
                    ktv_province a
                    LEFT JOIN ktv_district b ON a.`ProvinceID` = b.`ProvinceID`
                WHERE
                    a.active = '1'
                    AND ((a.ProvinceID = ?) OR (? = ''))
                    $sqlWhereAccess
                GROUP BY a.`ProvinceID`
                ORDER BY a.Province";
        $query = $this->db->query($sql,array($filter_prov,$filter_prov));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getDistrict($filter_district,$prov){
        //hak akses data
        if($_SESSION['is_admin'] == "1"){
            //no filter
        }else{
            //add filter
            if (!empty($this->user['district_access'])) {
                $sqlWhereAccess .= " AND a.DistrictID IN ({$this->user['district_access']})";
            }
        }

        $sql="SELECT
                a.`DistrictID` AS id,
                a.`District` AS label
            FROM
                ktv_district a
            WHERE
                a.`active` = '1'
                $sqlWhereAccess
                AND a.`ProvinceID` = ?
                AND ((a.DistrictID = ?) OR (? = ''))
            ORDER BY a.`District` ASC";
        $query = $this->db->query($sql,array($prov,$filter_district,$filter_district));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getTraining(){
        $sql="SELECT
                a.`CpgTrainingsID` AS id,
                a.`CpgTrainings` AS label
            FROM
                ktv_cpg_trainings a
            WHERE
                a.`ParentID` = '0'
                AND a.`StatusCode` = 'active'
            ORDER BY a.`CpgTrainings` ASC";
        $query = $this->db->query($sql,array());
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getMainList($sTrainingId,$sObjType,$prov,$dist,$sub_dist,$start,$limit,$sortingField,$sortingDir){
        //hak akses data
        if($_SESSION['is_admin'] == "1"){
            //no filter
        }else{
            //add filter
            if (!empty($this->user['district_access'])) {
                $sqlWhereAccess .= " AND h.DistrictID IN ({$this->user['district_access']})";
            }

            if (!empty($_SESSION['FlagAccess'] == 1)) {
                $sqlWhereAccessCpg .= " AND c.CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$_SESSION['PartnerID']})";
            }
        }

        //sorting
        if($sortingField == "") $sortingField = 'Type';
        if($sortingDir == "") $sortingDir = 'ASC';

        //sub_dist
        if($sub_dist == "") $no_sub_dist = "nodisplay";

        $sql="SELECT
    SQL_CALC_FOUND_ROWS
    ReceiptSetID
    , ObjType
    , `Type`
    , Training
    , CPGtrainingsID
    , Label
    , DATE_FORMAT(TrainingStart,'%Y-%m-%d') AS TrainingStart
    , DATE_FORMAT(TrainingEnd,'%Y-%m-%d') AS TrainingEnd
    , Province
    , District
    , LastModifiedDate
    , ReceiptCreated
    , ReceiptCreatedValue
FROM
(
SELECT
    a.ReceiptSetID,
    a.ObjType,
    'CPG Training' AS `Type`,
    e.`CpgTrainings` AS Training,
    CONCAT('[',b.`CPGid`,' - ',c.`GroupName`,'] Batch : ',d.`BatchNumber`,' - ',f.`PartnerName`) AS Label,
    b.TrainingStart,
    b.TrainingEnd,
    b.`CPGtrainingsID`,
    g.`Province` AS Province,
    h.`District`,
    IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastModifiedDate
    , IF(tt.ReceiptID IS NULL,'No','Yes') AS ReceiptCreated
    , IF(tt.ReceiptID IS NULL,'0','1') AS ReceiptCreatedValue
FROM
    `ktv_training_receipt_settings` a
    LEFT JOIN ktv_training_receipt tt ON a.ReceiptSetID = tt.ReceiptSetID
    LEFT JOIN ktv_cpg_batch_trainings b ON a.`ObjID` = b.`CpgBatchTrainingID`
    LEFT JOIN ktv_cpg c ON b.`CPGid` = c.`CPGid`
    LEFT JOIN ktv_cpg_batch d ON b.`CpgBatchID` = d.`CpgBatchID`
    LEFT JOIN ktv_program_partner f ON d.`PartnerID` = f.`PartnerID`
    LEFT JOIN ktv_cpg_trainings e ON b.`CPGtrainingsID` = e.`CpgTrainingsID`
    LEFT JOIN ktv_province g ON g.`ProvinceID` = SUBSTR(b.`CPGid`,1,2)
    LEFT JOIN ktv_district h ON h.`DistrictID` = SUBSTR(b.`CPGid`,1,4)
WHERE
    a.ObjType = 'farmergroup'
    AND a.`StatusCode` = 'active'
    $sqlWhereAccess
    $sqlWhereAccessCpg
    AND ((SUBSTR(b.`CPGid`,1,2) = ?) OR (? = ''))
    AND ((SUBSTR(b.`CPGid`,1,4) = ?) OR (? = ''))
    AND ((SUBSTR(c.`VillageID`,1,7) = ?) OR (? = ''))

UNION

SELECT
    a.ReceiptSetID,
    a.ObjType
    , 'Cadre Training' AS `Type`
    , c.`CpgTrainings` AS Training
    , CONCAT('Batch : ',d.`BatchNumber`,' - ',f.`PartnerName`) AS Label
    , b.TrainingStart
    , b.TrainingEnd
    , b.`CPGtrainingsID`
    , g.`Province` AS Province
    , h.`District`
    , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastModifiedDate
    , IF(tt.ReceiptID IS NULL,'No','Yes') AS ReceiptCreated
    , IF(tt.ReceiptID IS NULL,'0','1') AS ReceiptCreatedValue
FROM
    `ktv_training_receipt_settings` a
    LEFT JOIN ktv_training_receipt tt ON a.ReceiptSetID = tt.ReceiptSetID
    LEFT JOIN ktv_kader_trainings b ON a.`ObjID` = b.`CpgKaderTrainingID`
    LEFT JOIN ktv_cpg_trainings c ON b.`CPGtrainingsID` = c.`CpgTrainingsID`
    LEFT JOIN ktv_cpg_batch d ON b.`CpgBatchID` = d.`CpgBatchID`
    LEFT JOIN ktv_program_partner f ON d.`PartnerID` = f.`PartnerID`
    LEFT JOIN ktv_province g ON g.`ProvinceID` = b.`TrainingProvince`
    LEFT JOIN ktv_district h ON h.`DistrictID` = b.`TrainingDistrict`
WHERE
    a.ObjType = 'cadre'
    AND a.`StatusCode` = 'active'
    $sqlWhereAccess
    AND ((b.`TrainingProvince` = ?) OR (? = ''))
    AND ((b.`TrainingDistrict` = ?) OR (? = ''))
    AND 'nodisplay' = ?

UNION

SELECT
    a.ReceiptSetID,
    a.ObjType
    , 'Master Training' AS `Type`
    , c.`CpgTrainings` AS Training
    , CONCAT('Batch : ',d.`BatchNumber`,' - ',f.`PartnerName`) AS Label
    , b.TrainingStart
    , b.TrainingEnd
    , b.`CPGtrainingsID`
    , g.`Province` AS Province
    , h.`District`
    , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastModifiedDate
    , IF(tt.ReceiptID IS NULL,'No','Yes') AS ReceiptCreated
    , IF(tt.ReceiptID IS NULL,'0','1') AS ReceiptCreatedValue
FROM
    `ktv_training_receipt_settings` a
    LEFT JOIN ktv_training_receipt tt ON a.ReceiptSetID = tt.ReceiptSetID
    LEFT JOIN `ktv_master_trainings` b ON a.`ObjID` = b.`MasterTrainingID`
    LEFT JOIN ktv_cpg_trainings c ON b.`CPGtrainingsID` = c.`CpgTrainingsID`
    LEFT JOIN ktv_cpg_batch d ON b.`CpgBatchID` = d.`CpgBatchID`
    LEFT JOIN ktv_program_partner f ON d.`PartnerID` = f.`PartnerID`
    LEFT JOIN ktv_province g ON g.`ProvinceID` = b.`TrainingProvince`
    LEFT JOIN ktv_district h ON h.`DistrictID` = b.`DistrictID`
WHERE
    a.ObjType = 'master'
    AND a.`StatusCode` = 'active'
    $sqlWhereAccess
    AND ((b.`TrainingProvince` = ?) OR (? = ''))
    AND ((b.`DistrictID` = ?) OR (? = ''))
    AND 'nodisplay' = ?
) AS tbl_list
WHERE
    ((CPGtrainingsID = ?) OR (? = ''))
    AND ((ObjType = ?) OR (? = ''))
ORDER BY $sortingField $sortingDir
LIMIT ?,?";

        $p = array(
            $prov,$prov,
            $dist,$dist,
            $sub_dist,$sub_dist,
            $prov,$prov,
            $dist,$dist,
            $no_sub_dist,
            $prov,$prov,
            $dist,$dist,
            $no_sub_dist,
            $sTrainingId,$sTrainingId,
            $sObjType,$sObjType,
            (int) $start,(int) $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getSeltrainCpg($seltrainProvince,$seltrainDistrict,$seltrainTraining,$seltrainTrainingDateRange,$start,$limit,$sortingField,$sortingDir){
        //sorting
        if($sortingField == "") $sortingField = 'Training';
        if($sortingDir == "") $sortingDir = 'ASC';

        $temp = explode("T",$seltrainTrainingDateRange);
        $seltrainTrainingDateRange = $temp[0];

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                CpgBatchTrainingID AS id
                , b.`CpgTrainings` AS Training
                , CONCAT(c.`CPGid`,' - ',c.`GroupName`) AS CPGLabel
                , CONCAT(d.BatchNumber,' - ',e.`PartnerName`) AS Batch
                , DATE_FORMAT(a.`TrainingStart`,'%Y-%m-%d') AS TrainingStart
                , DATE_FORMAT(a.`TrainingEnd`,'%Y-%m-%d') AS TrainingEnd
            FROM
                ktv_cpg_batch_trainings a
                LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                LEFT JOIN ktv_cpg c ON a.`CPGid` = c.`CPGid`
                LEFT JOIN ktv_cpg_batch d ON a.`CpgBatchID` = d.`CpgBatchID`
                LEFT JOIN ktv_program_partner e ON d.`PartnerID` = e.`PartnerID`
            WHERE
                a.`StatusCode` = 'active'
                AND SUBSTR(c.`VillageID`,1,2) = ?
                AND SUBSTR(c.`VillageID`,1,4) = ?
                AND a.`CPGtrainingsID` = ?
                AND DATE(?) BETWEEN DATE(a.`TrainingStart`) AND DATE(a.`TrainingEnd`)
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
        ";
        $p = array(
            $seltrainProvince,
            $seltrainDistrict,
            $seltrainTraining,
            $seltrainTrainingDateRange,
            (int) $start,(int) $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getSeltrainCadre($seltrainProvince,$seltrainDistrict,$seltrainTraining,$seltrainTrainingDateRange,$start,$limit,$sortingField,$sortingDir){
        //sorting
        if($sortingField == "") $sortingField = 'Training';
        if($sortingDir == "") $sortingDir = 'ASC';

        $temp = explode("T",$seltrainTrainingDateRange);
        $seltrainTrainingDateRange = $temp[0];

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.CpgKaderTrainingID AS id
                , b.`CpgTrainings` AS Training
                , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS Batch
                , DATE_FORMAT(a.`TrainingStart`,'%Y-%m-%d') AS TrainingStart
                , DATE_FORMAT(a.`TrainingEnd`,'%Y-%m-%d') AS TrainingEnd
            FROM
                ktv_kader_trainings a
                LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.TrainingProvince = ?
                AND a.  TrainingDistrict = ?
                AND a.`CPGtrainingsID` = ?
                AND DATE(?) BETWEEN DATE(a.`TrainingStart`) AND DATE(a.`TrainingEnd`)
            ORDER BY Training ASC
            LIMIT ?,?";
        $p = array(
            $seltrainProvince,
            $seltrainDistrict,
            $seltrainTraining,
            $seltrainTrainingDateRange,
            (int) $start,(int) $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getSeltrainMaster($seltrainProvince,$seltrainDistrict,$seltrainTraining,$seltrainTrainingDateRange,$start,$limit,$sortingField,$sortingDir){
        //sorting
        if($sortingField == "") $sortingField = 'Training';
        if($sortingDir == "") $sortingDir = 'ASC';

        $temp = explode("T",$seltrainTrainingDateRange);
        $seltrainTrainingDateRange = $temp[0];

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.MasterTrainingID AS id
                , b.`CpgTrainings` AS Training
                , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS Batch
                , DATE_FORMAT(a.`TrainingStart`,'%Y-%m-%d') AS TrainingStart
                , DATE_FORMAT(a.`TrainingEnd`,'%Y-%m-%d') AS TrainingEnd
            FROM
                ktv_master_trainings a
                LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`TrainingProvince` = ?
                AND a.`DistrictID` = ?
                AND a.`CPGtrainingsID` = ?
                AND DATE(?) BETWEEN DATE(a.`TrainingStart`) AND DATE(a.`TrainingEnd`)
            ORDER BY Training ASC
            LIMIT ?,?";
        $p = array(
            $seltrainProvince,
            $seltrainDistrict,
            $seltrainTraining,
            $seltrainTrainingDateRange,
            (int) $start,(int) $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getStaffFarmerAutocom(
        $query,
        $isStaff,
        $isFarmer,
        $prov,
        $dist,
        $sub_dist,
        $start,$limit
    ){
        if($isStaff == 'true'){
            $sql="SELECT
    SQL_CALC_FOUND_ROWS
    StaffID AS id,
    CONCAT(roleLabel,' ',nama,' (',objLabel,')') AS label
FROM
(
SELECT
    a.StaffID,
    CASE
        WHEN a.ObjType = 'program' THEN '[PR]'
        WHEN a.ObjType = 'private' THEN '[PV]'
        WHEN a.ObjType = 'extension' THEN '[EX]'
        WHEN a.ObjType = 'sce' THEN '[SCE]'
        WHEN a.ObjType = 'trader' THEN '[TR]'
        WHEN a.ObjType = 'cooperative' THEN '[COOP]'
        WHEN a.ObjType = 'warehouse' THEN '[WH]'
        WHEN a.ObjType = 'bank' THEN '[B]'
        WHEN a.ObjType = 'farmergroup' THEN '[CPG]'
    END AS roleLabel,
    b.PersonNm AS nama,
    IFNULL(CASE
        WHEN a.ObjType = 'program' THEN
            (
                SELECT
                    sub_a.PartnerName
                FROM
                    ktv_program_partner sub_a
                WHERE
                    sub_a.PartnerID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'private' THEN
            (
                SELECT
                    sub_a.PartnerName
                FROM
                    ktv_program_partner sub_a
                WHERE
                    sub_a.PartnerID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'extension' THEN
            (
                SELECT
                    InstiName
                FROM
                    ktv_ref_institution sub_a
                WHERE
                    sub_a.InstiId = a.ObjID
            )
        WHEN a.ObjType = 'sce' THEN
            (
                SELECT
                    sub_b.FarmerName
                FROM
                    sce_farmer sub_a
                    INNER JOIN ktv_farmer sub_b ON sub_a.FarmerID = sub_b.FarmerID
                WHERE
                    sub_a.SceID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'trader' THEN
            (
                SELECT
                    sub_a.TraderName
                FROM
                    ktv_traders sub_a
                WHERE
                    sub_a.TraderID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'cooperative' THEN
            (
                SELECT
                    sub_a.CoopName
                FROM
                    ktv_cooperatives sub_a
                WHERE
                    sub_a.CoopID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'warehouse' THEN
            (
                SELECT
                    sub_a.WarehouseName
                FROM
                    ktv_warehouse sub_a
                WHERE
                    sub_a.WarehouseID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'bank' THEN
            (
                SELECT
                    sub_a.BranchName
                FROM
                    ktv_bank_branch sub_a
                WHERE
                    sub_a.BranchID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'farmergroup' THEN
            (
                SELECT
                    sub_a.GroupName
                FROM
                    ktv_cpg sub_a
                WHERE
                    sub_a.CPGid = a.ObjID
                LIMIT 1
            )
    END,'-') AS objLabel
FROM
    ktv_staffs a
    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
WHERE
    b.`StatusCd` = 'active'
    AND b.PersonNm IS NOT NULL
    AND b.PersonNm != ''
) AS tbl_grouped_1
HAVING label LIKE ?
ORDER BY label ASC
LIMIT ?,?
";
            $p = array(
                '%'.$query.'%',
                (int) $start,(int) $limit
            );
            $query = $this->db->query($sql, $p);
            $result['data'] = $query->result_array();

            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $result['total'] = $query->row()->total;

            return $result;
        }

        if($isFarmer == 'true'){
            $sql="SELECT
    FarmerID AS id,
    CONCAT('[',FarmerID,'] ',FarmerName) AS label
FROM
(
SELECT
    a.`FarmerID`,
    a.`FarmerName`
FROM
    ktv_farmer a
WHERE
    ((SUBSTR(a.`VillageID`,1,2) = ?) OR ('' = ?))
    AND ((SUBSTR(a.`VillageID`,1,4) = ?) OR ('' = ?))
    AND ((SUBSTR(a.`VillageID`,1,7) = ?) OR ('' = ?))
) AS tbl_grouped_1
HAVING label LIKE ?
ORDER BY  label ASC
LIMIT ?,?";
            $p = array(
                $prov,$prov,
                $dist,$dist,
                $sub_dist,$sub_dist,
                '%'.$query.'%',
                (int) $start,(int) $limit
            );
            $query = $this->db->query($sql, $p);
            $result['data'] = $query->result_array();

            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $result['total'] = $query->row()->total;

            return $result;
        }

        $result['data'] = array();
        $result['total'] = '0';
        return $result;
    }

    public function getGoodsList($callFrom,$filter_name,$start,$limit,$sortingField,$sortingDir){
        //sorting
        if($sortingField == "") $sortingField = 'code';
        if($sortingDir == "") $sortingDir = 'ASC';

        if($callFrom == "participant"){
            $usageID = "1,3";
        }else{
            $usageID = "2,3";
        }

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                b.`GoodsID` AS id
                , b.`GoodsCode` AS `code`
                , b.`GoodsName` AS `name`
                , c.`UnitsName` AS `unit`
            FROM
                ktv_goods b
                INNER JOIN ktv_ref_units c ON b.`GoodsUnitsID` = c.`UnitsID`
            WHERE
                b.GoodsUsage IN ($usageID)
                AND b.GoodsName LIKE ?
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
            ";
        $query = $this->db->query($sql,array('%'.$filter_name.'%',(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getGoodsListRSet($goods_tipe,$ReceiptSetID){
        if($goods_tipe == "participant"){
            $sql="SELECT
                a.ReceiptSetPartID AS id
                , b.`GoodsCode` AS `code`
                , b.`GoodsName` AS `name`
                , c.`UnitsName` AS `unit`
            FROM
                `ktv_training_receipt_settings_participants` a
                INNER JOIN ktv_goods b ON a.`ReceiptGoodsID` = b.`GoodsID`
                INNER JOIN ktv_ref_units c ON b.`GoodsUnitsID` = c.`UnitsID`
            WHERE
                a.`ReceiptSetID` = ?
            ORDER BY b.`GoodsCode` ASC";
            $query = $this->db->query($sql, array($ReceiptSetID));
        }

        if($goods_tipe == "activity"){
            $sql="SELECT
                    a.ReceiptSetActID AS id
                    , b.`GoodsCode` AS `code`
                    , b.`GoodsName` AS `name`
                    , c.`UnitsName` AS `unit`
                FROM
                    `ktv_training_receipt_settings_activity` a
                    INNER JOIN ktv_goods b ON a.`ReceiptGoodsID` = b.`GoodsID`
                    INNER JOIN ktv_ref_units c ON b.`GoodsUnitsID` = c.`UnitsID`
                WHERE
                    a.`ReceiptSetID` = ?
                ORDER BY b.`GoodsCode` ASC";
            $query = $this->db->query($sql, array($ReceiptSetID));
        }

        $result['data'] = $query->result_array();
        return $result;
    }

    public function getFormSetting($ReceiptSetID){
        $sql="SELECT
                a.`ObjID`
                , a.`ReceiptSetID`
                , a.`ObjType`,
                a.`PartGiverType`,
                a.`PartGiverID`,
                a.`PartReceiverType`,
                a.`PartReceiverID`,
                a.`PartKnownByType`,
                a.`PartKnownByID`,
                a.`PartKnownByType2`,
                a.`PartKnownByID2`,
                a.`ActGiverType`,
                a.`ActGiverID`,
                a.`ActReceiverType`,
                a.`ActReceiverID`,
                a.`ActKnownByType`,
                a.`ActKnownByID`,
                a.`ActKnownByType2`,
                a.`ActKnownByID2`
            FROM
                ktv_training_receipt_settings a
            WHERE
                a.`ReceiptSetID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ReceiptSetID));
        $data1 = $query->row_array();

        switch ($data1['ObjType']) {
            case 'farmergroup':
                $sql="SELECT
                        c.`CpgTrainings` AS TrainingTopic
                        , CONCAT('[',b.`CPGid`,' - ',f.`GroupName`,'] Batch : ',d.`BatchNumber`,' - ',e.`PartnerName`) AS ObjIDLabel
                    FROM
                        ktv_training_receipt_settings a
                        INNER JOIN ktv_cpg_batch_trainings b ON a.`ObjID` = b.`CpgBatchTrainingID`
                        INNER JOIN ktv_cpg_trainings c ON b.`CPGtrainingsID` = c.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch d ON b.`CpgBatchID` = d.`CpgBatchID`
                        LEFT JOIN ktv_program_partner e ON d.`PartnerID` = e.`PartnerID`
                        LEFT JOIN ktv_cpg f ON b.`CPGid` = f.`CPGid`
                    WHERE
                        a.`ReceiptSetID` = ?
                        AND a.`ObjType` = 'farmergroup'
                    LIMIT 1";
                $query = $this->db->query($sql,array($ReceiptSetID));
                $data2 = $query->row_array();
            break;
            case 'cadre':
                $sql="SELECT
                        b.`CpgTrainings` AS TrainingTopic
                        , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS ObjIDLabel
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_kader_trainings a ON sb.ObjID = a.CpgKaderTrainingID
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($ReceiptSetID));
                $data2 = $query->row_array();
            break;
            case 'master':
                $sql="SELECT
                        b.`CpgTrainings` AS TrainingTopic
                        , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS ObjIDLabel
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_master_trainings a ON sb.ObjID = a.`MasterTrainingID`
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($ReceiptSetID));
                $data2 = $query->row_array();
            break;
        }

        //data label signature (begin)
        $data1['PartGiverIDLabel'] = $this->getLabelSignatureReceipt($data1['PartGiverType'],$data1['PartGiverID']);
        $data1['PartReceiverIDLabel'] = $this->getLabelSignatureReceipt($data1['PartReceiverType'],$data1['PartReceiverID']);
        $data1['PartKnownByIDLabel'] = $this->getLabelSignatureReceipt($data1['PartKnownByType'],$data1['PartKnownByID']);
        $data1['PartKnownByID2Label'] = $this->getLabelSignatureReceipt($data1['PartKnownByType2'],$data1['PartKnownByID2']);
        $data1['ActGiverIDLabel'] = $this->getLabelSignatureReceipt($data1['ActGiverType'],$data1['ActGiverID']);
        $data1['ActReceiverIDLabel'] = $this->getLabelSignatureReceipt($data1['ActReceiverType'],$data1['ActReceiverID']);
        $data1['ActKnownByIDLabel'] = $this->getLabelSignatureReceipt($data1['ActKnownByType'],$data1['ActKnownByID']);
        $data1['ActKnownByID2Label'] = $this->getLabelSignatureReceipt($data1['ActKnownByType2'],$data1['ActKnownByID2']);
        //data label signature (end)

        $dataReturn = array_merge($data1,$data2);
        $return['success'] = true;
        $return['data'] = $dataReturn;
        return $return;
    }

    public function getLabelSignatureReceipt($opsiCall,$id){
        if($opsiCall == "staff"){
            $sql="SELECT
    CASE
        WHEN a.ObjType = 'program' THEN '[PR]'
        WHEN a.ObjType = 'private' THEN '[PV]'
        WHEN a.ObjType = 'extension' THEN '[EX]'
        WHEN a.ObjType = 'sce' THEN '[SCE]'
        WHEN a.ObjType = 'trader' THEN '[TR]'
        WHEN a.ObjType = 'cooperative' THEN '[COOP]'
        WHEN a.ObjType = 'warehouse' THEN '[WH]'
        WHEN a.ObjType = 'bank' THEN '[B]'
        WHEN a.ObjType = 'farmergroup' THEN '[CPG]'
    END AS roleLabel,
    b.PersonNm AS nama,
    IFNULL(CASE
        WHEN a.ObjType = 'program' THEN
            (
                SELECT
                    sub_a.PartnerName
                FROM
                    ktv_program_partner sub_a
                WHERE
                    sub_a.PartnerID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'private' THEN
            (
                SELECT
                    sub_a.PartnerName
                FROM
                    ktv_program_partner sub_a
                WHERE
                    sub_a.PartnerID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'extension' THEN
            (
                SELECT
                    InstiName
                FROM
                    ktv_ref_institution sub_a
                WHERE
                    sub_a.InstiId = a.ObjID
            )
        WHEN a.ObjType = 'sce' THEN
            (
                SELECT
                    sub_b.FarmerName
                FROM
                    sce_farmer sub_a
                    INNER JOIN ktv_farmer sub_b ON sub_a.FarmerID = sub_b.FarmerID
                WHERE
                    sub_a.SceID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'trader' THEN
            (
                SELECT
                    sub_a.TraderName
                FROM
                    ktv_traders sub_a
                WHERE
                    sub_a.TraderID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'cooperative' THEN
            (
                SELECT
                    sub_a.CoopName
                FROM
                    ktv_cooperatives sub_a
                WHERE
                    sub_a.CoopID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'warehouse' THEN
            (
                SELECT
                    sub_a.WarehouseName
                FROM
                    ktv_warehouse sub_a
                WHERE
                    sub_a.WarehouseID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'bank' THEN
            (
                SELECT
                    sub_a.BranchName
                FROM
                    ktv_bank_branch sub_a
                WHERE
                    sub_a.BranchID = a.ObjID
                LIMIT 1
            )
        WHEN a.ObjType = 'farmergroup' THEN
            (
                SELECT
                    sub_a.GroupName
                FROM
                    ktv_cpg sub_a
                WHERE
                    sub_a.CPGid = a.ObjID
                LIMIT 1
            )
    END,'-') AS objLabel
FROM
    ktv_staffs a
    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
WHERE
    a.`StaffID` = ?
LIMIT 1";
            $query = $this->db->query($sql,array($id));
            $data = $query->row_array();

            return $data['roleLabel']." ".$data['nama']. " (".$data['objLabel'].")";
        }

        if($opsiCall == "farmer"){
            $sql="SELECT
                    CONCAT('[',FarmerID,'] ',FarmerName) AS label
                FROM
                    ktv_farmer a
                WHERE
                    a.`FarmerID` = ?
                LIMIT 1";
            $query = $this->db->query($sql,array($id));
            $data = $query->row_array();
            return $data['label'];
        }

        return '';
    }

    public function insertSetting($varPost){
        $sql="INSERT INTO `ktv_training_receipt_settings` SET
              `ObjType` = ?,
              `ObjID` = ?,
              `PartGiverType` = ?,
              `PartGiverID` = ?,
              `PartReceiverType` = ?,
              `PartReceiverID` = ?,
              `PartKnownByType` = ?,
              `PartKnownByID` = ?,
              `PartKnownByType2` = ?,
              `PartKnownByID2` = ?,
              `ActGiverType` = ?,
              `ActGiverID` = ?,
              `ActReceiverType` = ?,
              `ActReceiverID` = ?,
              `ActKnownByType` = ?,
              `ActKnownByID` = ?,
              `ActKnownByType2` = ?,
              `ActKnownByID2` = ?,
              `StatusCode` = 'active',
              `DateCreated` = NOW(),
              `CreatedBy` = ?
            ";
        $p = array(
            $varPost['ObjType'],
            $varPost['ObjID'],
            $varPost['PartGiverType'],
            $varPost['PartGiverID'],
            $varPost['PartReceiverType'],
            $varPost['PartReceiverID'],
            $varPost['PartKnownByType'],
            $varPost['PartKnownByID'],
            $varPost['PartKnownByType2'],
            $varPost['PartKnownByID2'],
            $varPost['ActGiverType'],
            $varPost['ActGiverID'],
            $varPost['ActReceiverType'],
            $varPost['ActReceiverID'],
            $varPost['ActKnownByType'],
            $varPost['ActKnownByID'],
            $varPost['ActKnownByType2'],
            $varPost['ActKnownByID2'],
            $varPost['userid']
        );
        $query = $this->db->query($sql,$p);
        $id = $this->db->insert_id();

        if ($query) {
            $results['id'] = (string) $id;
            $results['prosesnya'] = 'insert';
            $results['success']    = true;
        } else {
            $results['success'] = false;
        }
        return $results;
    }

    public function updateSetting($varPost){
        $sql="UPDATE `ktv_training_receipt_settings` SET
              `PartGiverType` = ?,
              `PartGiverID` = ?,
              `PartReceiverType` = ?,
              `PartReceiverID` = ?,
              `PartKnownByType` = ?,
              `PartKnownByID` = ?,
              `PartKnownByType2` = ?,
              `PartKnownByID2` = ?,
              `ActGiverType` = ?,
              `ActGiverID` = ?,
              `ActReceiverType` = ?,
              `ActReceiverID` = ?,
              `ActKnownByType` = ?,
              `ActKnownByID` = ?,
              `ActKnownByType2` = ?,
              `ActKnownByID2` = ?,
              `DateUpdated` = NOW(),
              `LastModifiedBy` = ?
            WHERE
                `ReceiptSetID` = ?
            LIMIT 1";
        $p = array(
            $varPost['PartGiverType'],
            $varPost['PartGiverID'],
            $varPost['PartReceiverType'],
            $varPost['PartReceiverID'],
            $varPost['PartKnownByType'],
            $varPost['PartKnownByID'],
            $varPost['PartKnownByType2'],
            $varPost['PartKnownByID2'],
            $varPost['ActGiverType'],
            $varPost['ActGiverID'],
            $varPost['ActReceiverType'],
            $varPost['ActReceiverID'],
            $varPost['ActKnownByType'],
            $varPost['ActKnownByID'],
            $varPost['ActKnownByType2'],
            $varPost['ActKnownByID2'],
            $varPost['userid'],
            $varPost['ReceiptSetID']
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success']    = true;
        } else {
            $results['success'] = false;
        }
        return $results;
    }

    public function deleteSetting($ReceiptSetID){
        $sql="UPDATE ktv_training_receipt_settings SET
                StatusCode = 'nullified'
            WHERE
                ReceiptSetID = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ReceiptSetID));
        if ($query) {
            $results['success']    = true;
            $results['message']    = "Data Deleted";
        } else {
            $results['success'] = false;
            $results['message']    = "Process Failed";
        }
        return $results;
    }

    public function insertSettingGoods($varPost){
        $proses = true;

        if($varPost['callFrom'] == "participant"){
            if($varPost['GoodsID'][0] != ""){
                foreach ($varPost['GoodsID'] as $key => $value) {
                    $sql="INSERT INTO ktv_training_receipt_settings_participants (ReceiptSetID,ReceiptGoodsID,DateCreated,CreatedBy)
                    VALUES(?,?,NOW(),?)
                    ON DUPLICATE KEY UPDATE
                        DateUpdated = NOW(),
                        LastModifiedBy = ?";
                    $p = array(
                        $varPost['ReceiptSetID'],
                        $value,
                        $varPost['userid'],
                        $varPost['userid']
                    );
                    $query = $this->db->query($sql,$p);
                    if($query == false) $proses = false;
                }
            }
        }

        if($varPost['callFrom'] == "activity"){
            if($varPost['GoodsID'][0] != ""){
                foreach ($varPost['GoodsID'] as $key => $value) {
                    $sql="INSERT INTO ktv_training_receipt_settings_activity (ReceiptSetID,ReceiptGoodsID,DateCreated,CreatedBy)
                    VALUES(?,?,NOW(),?)
                    ON DUPLICATE KEY UPDATE
                        DateUpdated = NOW(),
                        LastModifiedBy = ?";
                    $p = array(
                        $varPost['ReceiptSetID'],
                        $value,
                        $varPost['userid'],
                        $varPost['userid']
                    );
                    $query = $this->db->query($sql,$p);
                    if($query == false) $proses = false;
                }
            }
        }

        if ($proses) {
            $results['success']    = true;
        } else {
            $results['success'] = false;
        }
        return $results;
    }

    public function deleteSettingGoods($id,$callFrom){
        if($callFrom == "participant"){
            $sql="DELETE FROM ktv_training_receipt_settings_participants WHERE ReceiptSetPartID = ? LIMIT 1";
            $proses = $this->db->query($sql,array($id));
        }

        if($callFrom == "activity"){
            $sql="DELETE FROM ktv_training_receipt_settings_activity WHERE ReceiptSetActID = ? LIMIT 1";
            $proses = $this->db->query($sql,array($id));
        }

        if ($proses) {
            $results['success']    = true;
            $results['message'] = 'Data Deleted';
        } else {
            $results['success'] = false;
            $results['message'] = 'Delete Failed';
        }
        return $results;
    }

    public function createReceipt($ReceiptSetID){
        $sql="SELECT
                a.`ReceiptSetID`
                , a.ObjType
                , a.ObjID
                , GROUP_CONCAT(DISTINCT b.`ReceiptGoodsID` SEPARATOR ',') AS act_goods
                , GROUP_CONCAT(DISTINCT c.`ReceiptGoodsID` SEPARATOR ',') AS part_goods
            FROM
                ktv_training_receipt_settings a
                LEFT JOIN ktv_training_receipt_settings_activity b ON a.`ReceiptSetID` = b.`ReceiptSetID`
                LEFT JOIN ktv_training_receipt_settings_participants c ON a.`ReceiptSetID` = b.`ReceiptSetID`
            WHERE
                a.`ReceiptSetID` = ?
            GROUP BY a.`ReceiptSetID`
            LIMIT 1";
        $query = $this->db->query($sql,array($ReceiptSetID));
        $dataRSet = $query->row_array();

        //validasi apakah ada part dan act goodsnya
        if($dataRSet['act_goods'] == "" || $dataRSet['part_goods'] == ""){
            $results['success'] = false;
            $results['message'] = lang("Activity / Participant Goods Still Empty !");
            return $results;
        }

        //get informasi training, peserta training
        switch ($dataRSet['ObjType']) {
            case 'farmergroup':
                $sql="SELECT
                        b.`CpgBatchID`
                        , SUBSTR(f.`VillageID`,1,2) AS ProvinceID
                        , SUBSTR(f.`VillageID`,1,4) AS DistrictID
                        , SUBSTR(f.`VillageID`,1,7) AS SubDistrictID
                        , f.`VillageID`
                    FROM
                        ktv_training_receipt_settings a
                        INNER JOIN ktv_cpg_batch_trainings b ON a.`ObjID` = b.`CpgBatchTrainingID`
                        INNER JOIN ktv_cpg_trainings c ON b.`CPGtrainingsID` = c.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch d ON b.`CpgBatchID` = d.`CpgBatchID`
                        LEFT JOIN ktv_program_partner e ON d.`PartnerID` = e.`PartnerID`
                        LEFT JOIN ktv_cpg f ON b.`CPGid` = f.`CPGid`
                    WHERE
                        a.`ReceiptSetID` = ?
                        AND a.`ObjType` = 'farmergroup'
                    LIMIT 1";
                $query = $this->db->query($sql,array($ReceiptSetID));
                $dataTraining = $query->row_array();

                $sql="SELECT
                        DISTINCT a.`FarmerID` AS PesertaID
                    FROM
                        ktv_cpg_batch_trainings_farmers a
                    WHERE
                        a.CpgBatchTrainingID = ?
                    ORDER BY a.`FarmerID` ASC";
                $query = $this->db->query($sql,array($dataRSet['ObjID']));
                $dataPeserta = $query->result_array();
                $PartObjType = 'farmer';
            break;
            case 'cadre':
                $sql="SELECT
                        a.`CpgBatchID`
                        , a.TrainingProvince AS ProvinceID
                        , a.TrainingDistrict AS DistrictID
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_kader_trainings a ON sb.ObjID = a.CpgKaderTrainingID
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($ReceiptSetID));
                $dataTraining = $query->row_array();

                $sql="SELECT
                        DISTINCT a.`FarmerID` AS PesertaID
                    FROM
                        ktv_kader_trainings_participants a
                    WHERE
                        a.CpgKaderTrainingID = ?
                    ORDER BY a.`FarmerID` ASC";
                $query = $this->db->query($sql,array($dataRSet['ObjID']));
                $dataPeserta = $query->result_array();
                $PartObjType = 'farmer';
            break;
            case 'master':
                $sql="SELECT
                        a.`CpgBatchID`
                        , a.`TrainingProvince` AS ProvinceID
                        , a.DistrictID
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_master_trainings a ON sb.ObjID = a.`MasterTrainingID`
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($ReceiptSetID));
                $dataTraining = $query->row_array();

                $sql="SELECT
                    DISTINCT c.`StaffID` AS PesertaID
                FROM
                    ktv_master_trainings_participants a
                    INNER JOIN ktv_persons b ON a.`ParticipantPersonID` = b.`PersonID`
                    INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                WHERE
                    a.MasterTrainingID = ?
                ORDER BY c.`StaffID` ASC";
                $query = $this->db->query($sql,array($dataRSet['ObjID']));
                $dataPeserta = $query->result_array();
                $PartObjType = 'staff';
            break;
        }

        $this->db->trans_begin();

        //insert receipt
        $sql="INSERT INTO `ktv_training_receipt` SET
              `ReceiptSetID` = ?,
              `TrainingCpgBatchID` = ?,
              `TrainingProvinceID` = ?,
              `TrainingDistrictID` = ?,
              `TrainingSubDistrictID` = If(?='',NULL,?),
              `TrainingVillageID` = If(?='',NULL,?),
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $ReceiptSetID,
            $dataTraining['CpgBatchID'],
            $dataTraining['ProvinceID'],
            $dataTraining['DistrictID'],
            $dataTraining['SubDistrictID'],$dataTraining['SubDistrictID'],
            $dataTraining['VillageID'],$dataTraining['VillageID'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $ReceiptID = $this->db->insert_id();

        //insert receipt activity
        if($dataRSet['act_goods'] != ""){
            $arrTmp = explode(",",$dataRSet['act_goods']);
            for ($i=0; $i < count($arrTmp); $i++) {
                $sql="INSERT INTO `ktv_training_receipt_activity` SET
                      `ReceiptID` = ?,
                      `ActGoodsID` = ?,
                      `ActGoodsQty` = '0',
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p = array(
                    $ReceiptID,
                    $arrTmp[$i],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        //insert participant
        $arrTmpPartGoods = explode(",",$dataRSet['part_goods']);
        if($dataPeserta[0]['PesertaID'] != ""){
            for ($i=0; $i < count($dataPeserta); $i++) {
                $sql="INSERT INTO `ktv_training_receipt_participant` SET
                      `ReceiptID` = ?,
                      `PartObjType` = ?,
                      `PartObjID` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p = array(
                    $ReceiptID,
                    $PartObjType,
                    $dataPeserta[$i]['PesertaID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
                $ReceiptPartID = $this->db->insert_id();

                foreach ($arrTmpPartGoods as $key => $value) {
                    $sql="INSERT INTO `ktv_training_receipt_participant_items` SET
                          `ReceiptPartID` = ?,
                          `ReceiptGoodsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $ReceiptPartID,
                        $value,
                        $_SESSION['userid']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to Create Receipt";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Receipt Created";
        }
        return $results;
    }

}
?>