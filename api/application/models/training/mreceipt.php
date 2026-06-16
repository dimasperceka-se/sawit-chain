<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-12 14:20:05
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Mreceipt extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
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
    ReceiptID
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
    tt.ReceiptID,
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
    INNER JOIN ktv_training_receipt tt ON a.ReceiptSetID = tt.ReceiptSetID
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
    tt.ReceiptID,
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
    INNER JOIN ktv_training_receipt tt ON a.ReceiptSetID = tt.ReceiptSetID
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
    tt.ReceiptID,
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
    INNER JOIN ktv_training_receipt tt ON a.ReceiptSetID = tt.ReceiptSetID
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

    public function getFillFormReceipt($ReceiptID){
        $sql="SELECT
                b.ObjType
                , CASE
                    WHEN b.ObjType = 'farmergroup' THEN 'CPG Training'
                    WHEN b.ObjType = 'cadre' THEN 'Cadre Training'
                    WHEN b.ObjType = 'master' THEN 'Master Training'
                END AS ObjTypeLabel
                , a.TrainingDate
                , a.PartReceiptDate
                , a.ActReceiptDate
                , a.ReceiptSetID
                , a.ReceiptID
                , a.Location
            FROM
                ktv_training_receipt a
                INNER JOIN ktv_training_receipt_settings b ON a.`ReceiptSetID` = b.`ReceiptSetID`
            WHERE
                a.`ReceiptID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ReceiptID));
        $data1 = $query->row_array();

        switch ($data1['ObjType']) {
            case 'farmergroup':
                $sql="SELECT
                        c.`CpgTrainings` AS TrainingTopic
                        , CONCAT('[',b.`CPGid`,' - ',f.`GroupName`,'] Batch : ',d.`BatchNumber`,' - ',e.`PartnerName`) AS ObjIDLabel
                        , DATE_FORMAT(b.TrainingStart,'%Y-%m-%d') AS TrainingStart
                        , DATE_FORMAT(b.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
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
                $query = $this->db->query($sql,array($data1['ReceiptSetID']));
                $data2 = $query->row_array();
            break;
            case 'cadre':
                $sql="SELECT
                        b.`CpgTrainings` AS TrainingTopic
                        , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS ObjIDLabel
                        , DATE_FORMAT(a.TrainingStart,'%Y-%m-%d') AS TrainingStart
                        , DATE_FORMAT(a.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_kader_trainings a ON sb.ObjID = a.CpgKaderTrainingID
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($data1['ReceiptSetID']));
                $data2 = $query->row_array();
            break;
            case 'master':
                $sql="SELECT
                        b.`CpgTrainings` AS TrainingTopic
                        , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS ObjIDLabel
                        , DATE_FORMAT(a.TrainingStart,'%Y-%m-%d') AS TrainingStart
                        , DATE_FORMAT(a.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_master_trainings a ON sb.ObjID = a.`MasterTrainingID`
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($data1['ReceiptSetID']));
                $data2 = $query->row_array();
            break;
        }

        $return['success'] = true;
        $dataReturn = array_merge($data1,$data2);
        $return['data'] = $dataReturn;
        return $return;
    }

    public function getActGoodsList($ReceiptID,$start,$limit,$sortingField,$sortingDir){
        //sorting
        if($sortingField == "") $sortingField = 'GoodsCode';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.ReceiptActID
                , b.`GoodsID`
                , b.`GoodsCode`
                , b.`GoodsName`
                , a.`ActGoodsQty`
                , a.`ActRemarks`
            FROM
                ktv_training_receipt_activity a
                INNER JOIN ktv_goods b ON a.`ActGoodsID` = b.`GoodsID`
            WHERE
                ReceiptID = ?
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql,array($ReceiptID,(int) $start, (int) $limit));
        $data = $query->result_array();

        //$data = array_merge($data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data);
        //$data = array_slice($data, 0, 10);
        $return['data'] = $data;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;
        return $return;
    }

    public function updateReceipt($varPost){
        $sql="UPDATE ktv_training_receipt SET
                TrainingDate = ?,
                PartReceiptDate = ?,
                ActReceiptDate = ?,
                Location = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                ReceiptID = ?
            LIMIT 1";
        $p = array(
            $varPost['TrainingDate'],
            $varPost['PartReceiptDate'],
            $varPost['ActReceiptDate'],
            $varPost['Location'],
            $varPost['userid'],
            $varPost['ReceiptID']
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success']    = true;
        } else {
            $results['success'] = false;
        }
        return $results;
    }

    public function updateReceiptActivity($varPost){
        $sql="UPDATE `ktv_training_receipt_activity` SET
              `ActGoodsQty` = ?,
              `ActRemarks` = ?,
              `DateUpdated` = NOW(),
              `LastModifiedBy` = ?
            WHERE
                `ReceiptActID` = ?
            LIMIT 1";
        $p = array(
            $varPost['ActGoodsQty'],
            $varPost['ActRemarks'],
            $varPost['userid'],
            $varPost['ReceiptActID']
        );
        $query = $this->db->query($sql,$p);

        if ($query === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Updated";
        }
        return $results;
    }

    public function deleteReceipt($ReceiptID){
        $this->db->trans_begin();

        //get data first
        $sql="SELECT
                GROUP_CONCAT(ReceiptPartID SEPARATOR ',') AS idNya
            FROM
                ktv_training_receipt_participant
            WHERE
                ReceiptID = ?
            ";
        $query = $this->db->query($sql,array($ReceiptID));
        $dataPart = $query->row_array();

        $sql="DELETE FROM `ktv_training_receipt_participant_items` WHERE ReceiptPartID IN (".$dataPart['idNya'].")";
        $query = $this->db->query($sql);

        $sql="DELETE FROM ktv_training_receipt_participant WHERE ReceiptID = ?";
        $query = $this->db->query($sql,array($ReceiptID));

        $sql="DELETE FROM ktv_training_receipt_activity WHERE ReceiptID = ?";
        $query = $this->db->query($sql,array($ReceiptID));

        $sql="DELETE FROM `ktv_training_receipt` WHERE ReceiptID = ?";
        $query = $this->db->query($sql,array($ReceiptID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to Delete Data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Deleted";
        }
        return $results;
    }

    public function getReceiptPartFieldModel($ReceiptID){
        $sql="SELECT
                CONCAT('GoodsID',c.`ReceiptGoodsID`) AS namaField
                , CONCAT(d.`GoodsName` ,' (',e.`UnitsName`,')') AS namaKolom
            FROM
                ktv_training_receipt a
                INNER JOIN ktv_training_receipt_settings b ON a.`ReceiptSetID` = b.`ReceiptSetID`
                LEFT JOIN ktv_training_receipt_settings_participants c ON b.`ReceiptSetID` = c.`ReceiptSetID`
                LEFT JOIN ktv_goods d ON c.`ReceiptGoodsID` = d.`GoodsID`
                LEFT JOIN ktv_ref_units e ON d.`GoodsUnitsID` = e.`UnitsID`
            WHERE
                a.`ReceiptID` = ?";
        $query = $this->db->query($sql,array($ReceiptID));
        $data = $query->result_array();

        //susun urutan field
        $dataKolom[0]['name'] = "ReceiptPartID";
        $dataKolom[1]['name'] = "Peserta";
        $noIncre = 2;
        foreach ($data as $key => $value) {
            $dataKolom[$noIncre]['name'] = $value['namaField'];
            $noIncre++;
        }
        $dataKolom[$noIncre]['name'] = "Remark";

        //susun grid kolom (begin)
        $dataParamGridColumn[0]['dataIndex'] = 'ReceiptPartID';
        $dataParamGridColumn[0]['hidden'] = true;

        $dataParamGridColumn[1]['text'] = lang('No');
        $dataParamGridColumn[1]['xtype'] = 'rownumberer';
        $dataParamGridColumn[1]['width'] = '4%';

        $dataParamGridColumn[2]['text'] = lang('Participant');
        $dataParamGridColumn[2]['dataIndex'] = 'Peserta';
        $dataParamGridColumn[2]['width'] = '30%';

        $noIncre = 3;
        foreach ($data as $key => $value) {
            $dataParamGridColumn[$noIncre]['text'] = $value['namaKolom'];
            $dataParamGridColumn[$noIncre]['xtype'] = 'checkcolumn';
            $dataParamGridColumn[$noIncre]['dataIndex'] = $value['namaField'];
            $dataParamGridColumn[$noIncre]['width'] = '15%';
            $noIncre++;
        }

        $dataParamGridColumn[$noIncre]['text'] = lang('Remark');
        $dataParamGridColumn[$noIncre]['dataIndex'] = 'Remark';
        $dataParamGridColumn[$noIncre]['width'] = '30%';
        $dataParamGridColumn[$noIncre]['editor'] = array(
                                                        "allowBlank" => true
                                                    );
        //susun grid kolom (end)

        $return['success'] = true;
        $return['fieldNya'] = $dataKolom;
        $return['gridColumnNya'] = $dataParamGridColumn;
        return $return;
    }

    public function getPartGoodsItem($ReceiptID){
        $sql="SELECT
                a.ReceiptPartID
                , CASE
                    WHEN a.PartObjType = 'farmer' THEN
                        (
                            SELECT
                                sub_a.FarmerName
                            FROM
                                ktv_farmer sub_a
                            WHERE
                                sub_a.FarmerID = a.PartObjID
                            LIMIT 1
                        )
                    WHEN a.PartObjType = 'staff' THEN
                        (
                            SELECT
                                sub_b.PersonNm
                            FROM
                                ktv_staffs sub_a
                                INNER JOIN ktv_persons sub_b ON sub_a.PersonID = sub_b.PersonID
                            WHERE
                                sub_a.StaffID = a.PartObjID
                            LIMIT 1
                        )
                END AS Peserta
                , a.PartRemarks AS Remark
            FROM
                `ktv_training_receipt_participant` a
            WHERE
                a.ReceiptID = ?
            ORDER BY Peserta ASC";
        $query = $this->db->query($sql,array($ReceiptID));
        $data = $query->result_array();

        //get data participant items
        for ($i=0; $i < count($data); $i++) {
            $sql="SELECT
                    a.ReceiptPartItemID
                    , a.ReceiptGoodsID
                    , a.ReceivedStatus
                FROM
                    `ktv_training_receipt_participant_items` a
                WHERE
                    a.`ReceiptPartID` = ?";
            $query = $this->db->query($sql,array($data[$i]['ReceiptPartID']));
            $data1 = $query->result_array();

            for ($j=0; $j < count($data1); $j++) {
                if($data1[$j]['ReceivedStatus'] == "1"){
                    $data[$i]['GoodsID'.$data1[$j]['ReceiptGoodsID']] = true;
                }else{
                    $data[$i]['GoodsID'.$data1[$j]['ReceiptGoodsID']] = false;
                }
            }
        }

        $return['data'] = $data;
        return $return;
    }

    public function updateReceiptPartGoods($paramKirim){
        $this->db->trans_begin();

        foreach ($paramKirim as $key => $value) {
            //prep variabel "partItem"
            $tmpData = array();
            foreach ($value as $key1 => $value1) {
                if (preg_match('/GoodsID/',$key1)){
                    $tmpGoodsID = str_replace("GoodsID","",$key1);
                    if($value1 == "1"){
                        $tmpData[] = $tmpGoodsID."@1";
                    }else{
                        $tmpData[] = $tmpGoodsID."@0";
                    }
                }
            }
            $paramKirim[$key]['partItem'] = $tmpData;

            //update participant
            $sql="UPDATE `ktv_training_receipt_participant` SET
                  `PartRemarks` = ?,
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    `ReceiptPartID` = ?
                LIMIT 1";
            $p = array(
                $value['Remark'],
                $_SESSION['userid'],
                $value['ReceiptPartID']
            );
            $query = $this->db->query($sql,$p);

            for ($i=0; $i < count($paramKirim[$key]['partItem']); $i++) {
                $arrTmp = explode("@",$paramKirim[$key]['partItem'][$i]);
                $GoodsID = $arrTmp[0];
                $ReceivedStatus = $arrTmp[1];

                $sql="UPDATE `ktv_training_receipt_participant_items` SET
                      `ReceivedStatus` = ?,
                      `DateUpdated` = NOW(),
                      `LastModifiedBy` = ?
                    WHERE
                        ReceiptPartID = ?
                        AND ReceiptGoodsID = ?
                    LIMIT 1";
                $p = array(
                    $ReceivedStatus,
                    $_SESSION['userid'],
                    $value['ReceiptPartID'],
                    $GoodsID
                );
                $query = $this->db->query($sql,$p);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Saved";
        }
        return $results;
    }

    public function getInfoSignature($type,$id){
        if($type == "staff"){
            $sql="SELECT
                    b.`PersonNm` AS nama
                    , IFNULL(d.`PositionName`,'-') AS posisi
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                    LEFT JOIN ktv_staff_positions c ON a.`StaffID` = c.StaffPosStaffID
                    LEFT JOIN ktv_ref_position_type d ON c.`StaffPosPositionID` = d.`PositionID`
                        AND CURDATE() BETWEEN DATE(c.StaffPostStart) AND DATE(c.StaffPostEnd)
                        AND c.StatusCode = 'active'
                WHERE
                    a.`StaffID` = ?
                LIMIT 1";
            $query = $this->db->query($sql,array($id));
            return $query->row_array();
        }

        if($type == "farmer"){
            $sql="SELECT
                    a.`FarmerName` AS nama
                    , '-' AS posisi
                FROM
                    ktv_farmer a
                WHERE
                    a.`FarmerID` = ?
                LIMIT 1";
            $query = $this->db->query($sql,array($id));
            return $query->row_array();
        }
    }

    public function getDataReceiptPrint($ReceiptID){
        $sql="SELECT
                a.TrainingDistrictID
                , b.ObjType
                , e.`Province` AS ProvinceLabel
                , c.`District`
                , c.`District` AS DistrictLabel
                , c.`DistrictID`
                , d.`SubDistrict`
                , a.ReceiptSetID
                , b.ActGiverType
                , b.ActGiverID
                , b.ActReceiverType
                , b.ActReceiverID
                , b.ActKnownByType
                , b.ActKnownByID
                , b.ActKnownByType2
                , b.ActKnownByID2
                , b.PartGiverType
                , b.PartGiverID
                , b.PartReceiverType
                , b.PartReceiverID
                , b.PartKnownByType
                , b.PartKnownByID
                , b.PartKnownByType2
                , b.PartKnownByID2
                , a.Location
            FROM
                ktv_training_receipt a
                INNER JOIN ktv_training_receipt_settings b ON a.`ReceiptSetID` = b.`ReceiptSetID`
                LEFT JOIN ktv_district c ON a.`TrainingDistrictID` = c.`DistrictID`
                LEFT JOIN ktv_subdistrict d ON a.`TrainingSubDistrictID` = d.`SubDistrictID`
                LEFT JOIN ktv_province e ON a.TrainingProvinceID = e.ProvinceID
            WHERE
                a.ReceiptID = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ReceiptID));
        $dataReturn = $query->row_array();

        $dataReturn['LabelGiver'] = $this->getInfoSignature($dataReturn['ActGiverType'],$dataReturn['ActGiverID']);
        $dataReturn['LabelReceiver'] = $this->getInfoSignature($dataReturn['ActReceiverType'],$dataReturn['ActReceiverID']);
        $dataReturn['LabelKnownBy'] = $this->getInfoSignature($dataReturn['ActKnownByType'],$dataReturn['ActKnownByID']);
        $dataReturn['LabelKnownBy2'] = $this->getInfoSignature($dataReturn['ActKnownByType2'],$dataReturn['ActKnownByID2']);
        $dataReturn['LabelPartGiver'] = $this->getInfoSignature($dataReturn['PartGiverType'],$dataReturn['PartGiverID']);
        $dataReturn['LabelPartReceiver'] = $this->getInfoSignature($dataReturn['PartReceiverType'],$dataReturn['PartReceiverID']);
        $dataReturn['LabelPartKnownBy'] = $this->getInfoSignature($dataReturn['PartKnownByType'],$dataReturn['PartKnownByID']);
        $dataReturn['LabelPartKnownBy2'] = $this->getInfoSignature($dataReturn['PartKnownByType2'],$dataReturn['PartKnownByID2']);

        switch ($dataReturn['ObjType']) {
            case 'farmergroup':
                $sql="SELECT
                        c.`CpgTrainings` AS TrainingTopic
                        , CONCAT('[',b.`CPGid`,' - ',f.`GroupName`,'] Batch : ',d.`BatchNumber`,' - ',e.`PartnerName`) AS ObjIDLabel
                        , CONCAT('[',b.`CPGid`,' - ',f.`GroupName`,']') AS CPGLabel
                        , CONCAT(d.`BatchNumber`,' - ',e.`PartnerName`) AS BatchLabel
                        , b.`CPGid`
                        , f.GroupName
                        , g.Village
                        , DATE_FORMAT(b.TrainingStart,'%Y-%m-%d') AS TrainingStart
                        , DATE_FORMAT(b.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
                    FROM
                        ktv_training_receipt_settings a
                        INNER JOIN ktv_cpg_batch_trainings b ON a.`ObjID` = b.`CpgBatchTrainingID`
                        INNER JOIN ktv_cpg_trainings c ON b.`CPGtrainingsID` = c.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch d ON b.`CpgBatchID` = d.`CpgBatchID`
                        LEFT JOIN ktv_program_partner e ON d.`PartnerID` = e.`PartnerID`
                        LEFT JOIN ktv_cpg f ON b.`CPGid` = f.`CPGid`
                        LEFT JOIN ktv_village g ON g.VillageID = f.VillageID
                    WHERE
                        a.`ReceiptSetID` = ?
                        AND a.`ObjType` = 'farmergroup'
                    LIMIT 1";
                $query = $this->db->query($sql,array($dataReturn['ReceiptSetID']));
                $data2 = $query->row_array();

                $dataReturn['CPGLabel'] = $data2['CPGLabel'];
                $dataReturn['BatchLabel'] = $data2['BatchLabel'];
                $dataReturn['CPGid'] = $data2['CPGid'];
                $dataReturn['GroupName'] = $data2['GroupName'];
                $dataReturn['VillageName'] = $data2['Village'];
                $dataReturn['TrainingStart'] = $data2['TrainingStart'];
                $dataReturn['TrainingEnd'] = $data2['TrainingEnd'];
            break;
            case 'cadre':
                $sql="SELECT
                        b.`CpgTrainings` AS TrainingTopic
                        , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS BatchLabel
                        , DATE_FORMAT(a.TrainingStart,'%Y-%m-%d') AS TrainingStart
                        , DATE_FORMAT(a.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_kader_trainings a ON sb.ObjID = a.CpgKaderTrainingID
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($dataReturn['ReceiptSetID']));
                $data2 = $query->row_array();

                $dataReturn['TrainingStart'] = $data2['TrainingStart'];
                $dataReturn['TrainingEnd'] = $data2['TrainingEnd'];
                $dataReturn['BatchLabel'] = $data2['BatchLabel'];
            break;
            case 'master':
                $sql="SELECT
                        b.`CpgTrainings` AS TrainingTopic
                        , CONCAT(c.BatchNumber,' - ',d.`PartnerName`) AS BatchLabel
                        , DATE_FORMAT(a.TrainingStart,'%Y-%m-%d') AS TrainingStart
                        , DATE_FORMAT(a.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
                    FROM
                        ktv_training_receipt_settings sb
                        LEFT JOIN ktv_master_trainings a ON sb.ObjID = a.`MasterTrainingID`
                        LEFT JOIN ktv_cpg_trainings b ON a.`CPGtrainingsID` = b.`CpgTrainingsID`
                        LEFT JOIN ktv_cpg_batch c ON a.`CpgBatchID` = c.`CpgBatchID`
                        LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
                    WHERE
                        sb.`ReceiptSetID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($dataReturn['ReceiptSetID']));
                $data2 = $query->row_array();

                $dataReturn['TrainingStart'] = $data2['TrainingStart'];
                $dataReturn['TrainingEnd'] = $data2['TrainingEnd'];
                $dataReturn['BatchLabel'] = $data2['BatchLabel'];
            break;
        }

        return $dataReturn;
    }

    public function getDataActivityGoodsPrint($ReceiptID){
        $sql="SELECT
                b.`GoodsName` AS Barang
                , a.`ActGoodsQty` AS Qty
                , c.`UnitsName` AS Unit
                , a.`ActRemarks` AS Remark
            FROM
                `ktv_training_receipt_activity` a
                LEFT JOIN ktv_goods b ON a.`ActGoodsID` = b.`GoodsID`
                LEFT JOIN `ktv_ref_units` c ON b.`GoodsUnitsID` = c.`UnitsID`
            WHERE
                a.`ReceiptID` = ?
            ORDER BY b.`GoodsName` ASC";
        $query = $this->db->query($sql,array($ReceiptID));
        $data = $query->result_array();

        //$data = array_merge($data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data,$data);
        //array_splice($data, 88);
        return $data;
    }

    public function getDataListParticipantPrint($ReceiptID){
        //data header
        $sql="SELECT
                a.`ReceiptID`
                , b.`ReceiptSetID`
                , c.`ReceiptGoodsID`
                , CONCAT(d.`GoodsName`,'<br>(',FLOOR(c.GoodsQty),' ',e.UnitsName,')') AS labelHeader
            FROM
                ktv_training_receipt a
                INNER JOIN ktv_training_receipt_settings b ON a.`ReceiptSetID` = b.`ReceiptSetID`
                LEFT JOIN ktv_training_receipt_settings_participants c ON b.`ReceiptSetID` = c.`ReceiptSetID`
                LEFT JOIN ktv_goods d ON c.`ReceiptGoodsID` = d.`GoodsID`
                LEFT JOIN ktv_ref_units e ON d.`GoodsUnitsID` = e.`UnitsID`
            WHERE
                a.`ReceiptID` = ?
            ORDER BY c.ReceiptGoodsID ASC";
        $query = $this->db->query($sql,array($ReceiptID));
        $dataHeader = $query->result_array();

        //data list (begin)
        $sql="SELECT
                a.ReceiptPartID
                , CASE
                    WHEN a.PartObjType = 'farmer' THEN
                        (
                            SELECT
                                sub_a.FarmerName
                            FROM
                                ktv_farmer sub_a
                            WHERE
                                sub_a.FarmerID = a.PartObjID
                            LIMIT 1
                        )
                    WHEN a.PartObjType = 'staff' THEN
                        (
                            SELECT
                                sub_b.PersonNm
                            FROM
                                ktv_staffs sub_a
                                INNER JOIN ktv_persons sub_b ON sub_a.PersonID = sub_b.PersonID
                            WHERE
                                sub_a.StaffID = a.PartObjID
                            LIMIT 1
                        )
                END AS Peserta
            FROM
                `ktv_training_receipt_participant` a
            WHERE
                a.ReceiptID = ?
            ORDER BY Peserta ASC";
        $query = $this->db->query($sql,array($ReceiptID));
        $dataList = $query->result_array();

        for ($i=0; $i < count($dataList); $i++) {
            $sql="SELECT
                    b.`ReceiptGoodsID`
                    , b.`ReceivedStatus`
                FROM
                    `ktv_training_receipt_participant` a
                    INNER JOIN `ktv_training_receipt_participant_items` b ON a.`ReceiptPartID` = b.`ReceiptPartID`
                WHERE
                    a.ReceiptPartID = ?
                ORDER BY  b.`ReceiptGoodsID` ASC";
            $query = $this->db->query($sql,array($dataList[$i]['ReceiptPartID']));
            $data = $query->result_array();

            for ($j=0; $j < count($data); $j++) {
                $dataList[$i]['GoodsID'.$data[$j]['ReceiptGoodsID']] = $data[$j]['ReceivedStatus'];
            }
        }
        //data list (end)

        /*
        $dataList = array_merge($dataList,$dataList,$dataList,$dataList,$dataList,$dataList,$dataList);
        array_splice($dataList, 13);
        */
        $return['dataHeader'] = $dataHeader;
        $return['dataList'] = $dataList;
        return $return;
    }

}
?>