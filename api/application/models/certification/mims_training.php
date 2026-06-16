<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : mims_training.php
 *******************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mims_training extends CI_Model{

    public function GetCmbByImsIdSingle($IMSID){
        $sql = "SELECT
                    DISTINCT cpg.`CPGid` AS id,
                    CONCAT(cpg.`CPGid`,' - ',cpg.`GroupName`, ' [',dis.`District`,' - ',subd.`SubDistrict`,' - ','(',vil.`Village`,' - ',vil.`VillageID`,')',']') AS label
                FROM
                    ktv_ims_soc_sel a
                    LEFT JOIN ktv_cocoa_farmer far ON a.`DestObjID` = far.`FarmerID`
                    LEFT JOIN ktv_cpg cpg ON far.`CPGid` = cpg.`CPGid`
                    LEFT JOIN ktv_village vil ON cpg.`VillageID` = vil.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                WHERE
                    a.ApprovalBy IS NOT NULL #Sudah di approve di SocSel
                    AND a.`IMSID` = ?
                ORDER BY cpg.`CPGid` ASC";
        $Data = $this->db->query($sql,array($IMSID))->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetCmbParticipantType($IMSID,$CPGid){
        $sql = "SELECT
                    DISTINCT a.`ObjType` AS id,
                    a.`ObjType` AS label
                FROM
                    ktv_ims_soc_sel a
                    LEFT JOIN ktv_cocoa_farmer far ON a.`DestObjID` = far.`FarmerID`
                WHERE
                    a.ApprovalBy IS NOT NULL #Sudah di approve di SocSel
                    AND a.`IMSID` = ?
                    AND far.`CPGid` = ?
                ORDER BY a.`ObjType` ASC";
        $Data = $this->db->query($sql,array($IMSID,$CPGid))->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetCmbFasilitator(){
        $sql = "SELECT
                    st.`StaffID` AS id
                    , CONCAT(ps.`PersonNm`,CASE
                        WHEN st.`ObjType`='program' THEN CONCAT(' (PR/',part.PartnerName,')')
                        WHEN st.`ObjType`='private' THEN CONCAT(' (PS/',part.PartnerName,')')
                    END,' [',IFNULL(rpos.PositionName,'-'),']') AS label
                FROM
                    ktv_staffs st
                    INNER JOIN ktv_persons ps ON st.`PersonID` = ps.`PersonID`
                    LEFT JOIN ktv_program_partner part ON st.`ObjID` = part.`PartnerID`
                    
                    LEFT JOIN ktv_staff_positions f ON st.`StaffID` = f.`StaffPosStaffID`
                        AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                        AND f.StatusCode = 'active'
                    LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
                WHERE
                    st.`StatusCode` = 'active'
                    AND ps.`StatusCd` = 'active'
                    AND st.`ObjType` IN ('program','private')
                    AND st.`StaffID` NOT IN ('1','2804')
                ORDER BY label";
        $Data = $this->db->query($sql,array())->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetCmbPenyuluh(){
        $sql = "SELECT ExtensionID AS id, PersonNm AS label
                FROM ktv_extension_staff a
                LEFT JOIN ktv_persons d ON d.PersonID=a.PersonID 
                WHERE d.StatusCd = 'active'
                ORDER BY label";
        $Data = $this->db->query($sql,array())->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    /* ===================================================================================================================== */

    public function GetImsTrainingForm($IMSID){
        $sql = "SELECT
                a.`IMSID`
                , a.`CertEventName`
                , a.`Year`
                , part.`PartnerName` AS FirstBuyer
                , CONCAT('[',(
                CASE
                    WHEN b.ObjType = 'farmer_group' THEN 'Farmer Group'
                    WHEN b.ObjType = 'cooperative' THEN 'Cooperative'
                    ELSE '-' 
                END
                ),'] ',b.CertHolderOrgName) AS CertificateHolder
                , d.`CertProgName` AS ProgramName
                , cbody.`CertBodyName` AS CertificationBody
                , a.Location
                , a.TrainStatus
                , a.SocStart
                , a.SocEnd
                , a.TrainingStart
                , a.TrainingEnd

                , a.SigningLockSocSelBy
                , a.SigningLockSocSelRemark
                , a.SigningLockSocSelDatetime
                , a.SigningLockGapCocBy
                , a.SigningLockGapCocRemark
                , a.SigningLockGapCocDatetime
            FROM
                ktv_ims a
                LEFT JOIN ktv_first_buyer fb ON a.`FirstBuyerID` = fb.`FirstBuyerID`
                LEFT JOIN ktv_program_partner part ON fb.`FirstBuyerPartnerID` = part.`PartnerID`

                LEFT JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
                LEFT JOIN ktv_ref_certification_program d ON b.`CertProgID` = d.`CertProgID`

                LEFT JOIN ktv_certification_body cbody ON a.`CertBodyID` = cbody.`CertBodyID`
            WHERE
                a.`IMSID` = ?
            LIMIT 1";
        $p = array(
            $IMSID,
        );
        $query       = $this->db->query($sql, $p);
        $data        = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew           = "Koltiva.view.IMS.WinImsTraining-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data']    = $dataRow;
        return $return;
    }

    public function ConvertDataListPeserta($DataList){
        $ArrReturn = array();

        if(count($DataList) > 0 && $DataList[0]['FarmerID']){
            for ($i=0; $i < count($DataList); $i++) { 
                $ArrReturn[$i] = $DataList[$i]['FarmerID'];
            }
        }

        return $ArrReturn;
    }

    public function ProsesPesertaInsertTraining($ArrPesertaProses,$ArrPesertaPernahIkut){
        $ArrReturn = array();

        if(!isset($ArrPesertaPernahIkut[0])){
            //Proses Semuanya
            return $ArrPesertaProses;
        }

        if(isset($ArrPesertaProses[0])){
            for ($i=0; $i < count($ArrPesertaProses); $i++) { 
                if(!in_array($ArrPesertaProses[$i],$ArrPesertaPernahIkut)){
                    $ArrReturn[] = $ArrPesertaProses[$i];
                }
            }
        }

        return $ArrReturn;
    }

    public function GenerateCpgTraining($ParamPost){
        //Param
        $CpgBatchTrainingIDGAP = null;
        $CpgBatchTrainingIDCOC = null;
        $RemarkGenerated = null;
        if($ParamPost['EventType'] == 'Single CPG'){
            $ParticipantType = $ParamPost['SingleParticipantType'];
        }else{
            //Multiple CPG
            $ParticipantType = $ParamPost['MultipleParticipantType'];

            if($ParamPost['ActivityType'] == 'Full'){
                $TrainingRemidialType = 'no';
            }else{
                $TrainingRemidialType = 'yes';
            }
        }

        //Info IMS
        $sql = "SELECT
                    a.`IMSID`,
                    a.`IMSMasterID`,
                    a.`CertHolderID`,
                    a.`CpgBatchID`,
                    d.`CertProgID`
                FROM
                    ktv_ims a
                    LEFT JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
                    LEFT JOIN ktv_ref_certification_program d ON b.`CertProgID` = d.`CertProgID`
                WHERE
                    a.`IMSID` = ?
                GROUP BY a.`IMSID`
                LIMIT 1";
        $DataIMS = $this->db->query($sql,array($ParamPost['IMSID']))->row_array();
        if(
            $DataIMS['IMSMasterID'] == "" ||
            $DataIMS['CpgBatchID'] == "" ||
            $DataIMS['CertHolderID'] == "" ||
            $DataIMS['IMSMasterID'] == "0" ||
            $DataIMS['CpgBatchID'] == "0"  ||
            $DataIMS['CertHolderID'] == "0" 
        ){
            $results['success'] = false;
            $results['message'] = lang("IMSMasterID, CpgBatchID, CertHolderID is required");
            return $results;
        }

        //Cek dl apakah data mappingnya
        $sql = "SELECT
                    a.IMSID
                    , a.`TopikGAP`
                    , a.`TopikCOC`
                FROM
                    `ktv_ims_training_event_mapping` a
                WHERE
                    a.`IMSID` = ?
                    AND a.TrainingType = 'CPG Training'
                    AND a.`ActivityType` = ?
                    AND a.`ParticipantType` = ?
                LIMIT 1";
        $DataMapping = $this->db->query($sql,array($DataIMS['IMSID'],$ParamPost['ActivityType'],$ParticipantType))->row_array();
        if(isset($DataMapping['IMSID'])){
            $this->db->trans_begin();

            switch($ParamPost['EventType']){
                case 'Single CPG':
                    //Single CPG itu sudah pasti "Full" bukan "Remedial"
                    $RemarkGenerated = "CPGid: {$ParamPost['SingleCpg']}\nParticipant Type: {$ParamPost['SingleParticipantType']}";

                    //ActivityType untuk Form CPG Training (ktv_cpg_batch_trainings) ====================== (Begin)
                    switch($ParamPost['SingleParticipantType']){
                        case 'Applicant':
                            $ActivityTypeCPGTraining = 'full';
                        break;
                        case 'Existing Farmer':
                        case 'Existing Certified Farmer':
                            $ActivityTypeCPGTraining = 'refresh';
                        break;
                    }
                    //ActivityType untuk Form CPG Training (ktv_cpg_batch_trainings) ====================== (End)

                    //Topik GAP
                    if($DataMapping['TopikGAP'] > 0){
                        //Cari & Filter Peserta ================ (Begin)
                        
                        //Peserta Proses
                        $sql = "SELECT
                                    a.`DestObjID` AS FarmerID
                                FROM
                                    `ktv_ims_soc_sel` a
                                    INNER JOIN ktv_cocoa_farmer far ON a.`DestObjID` = far.`FarmerID`
                                WHERE
                                    a.`ApprovalBy` IS NOT NULL
                                    AND a.`ApprovalBy` != '0'
                                    AND a.`IMSID` = ?
                                    AND far.`CPGid` = ?
                                    AND a.ObjType = ?";
                        $p = array(
                            $ParamPost['IMSID'],
                            $ParamPost['SingleCpg'],
                            $ParamPost['SingleParticipantType']
                        );
                        $DataPesertaProses = $this->db->query($sql,$p)->result_array();
                        $ArrPesertaProses = $this->ConvertDataListPeserta($DataPesertaProses);

                        //Peserta yang sudah pernah ikut
                        $sql = "SELECT
                                    DISTINCT tfar.`FarmerID`
                                FROM
                                    `ktv_ims_training_generated` a
                                    INNER JOIN ktv_ims_training_generated_id b ON 1=1
                                        AND a.`ObjID` = b.`ObjID`
                                        AND b.`TrainingTopik` = 'TopikGAP'
                                    
                                    LEFT JOIN `ktv_cpg_batch_trainings_farmers` tfar ON 1=1
                                        AND b.`TrainingID` = tfar.`CpgBatchTrainingID`
                                        AND tfar.`StatusCode` = 'active'

                                    JOIN ktv_cpg_batch_trainings kcbt ON 1=1
                                        AND kcbt.`CpgBatchTrainingID` = tfar.`CpgBatchTrainingID`
                                        AND kcbt.`TrainingStatus` != '3' # bukan canceled
                                        
                                    LEFT JOIN ktv_cocoa_farmer far ON 1=1
                                        AND tfar.`FarmerID` = far.`FarmerID`
                                WHERE
                                    a.`ObjType` = 'CPG Training'
                                    AND a.`IMSID` = ?
                                    AND a.`ActivityType` = ?
                                    AND far.`CPGid` = ?";
                        $p = array(
                            $ParamPost['IMSID'],
                            $ParamPost['ActivityType'],
                            $ParamPost['SingleCpg']
                        );
                        $DataPesertaPernahIkut = $this->db->query($sql,$p)->result_array();
                        $ArrPesertaPernahIkut = $this->ConvertDataListPeserta($DataPesertaPernahIkut);
                        //Cari & Filter Peserta ================ (End)

                        //Bandingkan untuk cari $ArrPesertaInsert
                        $ArrPesertaInsert = $this->ProsesPesertaInsertTraining($ArrPesertaProses,$ArrPesertaPernahIkut);
                        if(isset($ArrPesertaInsert[0])){
                            //Proses Generate CPG Training =============================== (Begin)
                            $sql = "INSERT INTO `ktv_cpg_batch_trainings` SET
                                    `CPGid` = ?,
                                    `CPGtrainingsID` = '1',
                                    `CpgBatchID` = ?,
                                    `TrainingDays` = ?,
                                    `TrainingDayStatus` = 'full',
                                    `CertProgID` = ?,
                                    `CertHolderID` = ?,
                                    `IMSMasterID` = ?,
                                    `IMSID` = ?,
                                    `ActivityType` = ?, #full, refresh
                                    `RemidialType` = 'no', #yes, no
                                    `TrainingStatus` = '2',
                                    `RemarkProcess` = 'Generated by IMS Training',
                                    `DateCreated` = NOW(),
                                    `CreatedBy` = ?";
                            $p = array(
                                $ParamPost['SingleCpg'],
                                $DataIMS['CpgBatchID'],
                                $DataMapping['TopikGAP'],
                                $DataIMS['CertProgID'],
                                $DataIMS['CertHolderID'],
                                $DataIMS['IMSMasterID'],
                                $DataIMS['IMSID'],
                                $ActivityTypeCPGTraining,
                                $_SESSION['userid']
                            );
                            $query = $this->db->query($sql,$p);
                            $CpgBatchTrainingIDGAP = $this->db->insert_id();
                            //Proses Generate CPG Training =============================== (End)

                            //Proses Insert Peserta =============================== (Begin)
                            $NrOfParticipants = count($ArrPesertaInsert);
                            for ($iPeserta=0; $iPeserta < count($ArrPesertaInsert); $iPeserta++) { 
                                $sql = "INSERT INTO `ktv_cpg_batch_trainings_farmers` SET
                                        `CpgBatchTrainingID` = ?,
                                        `FarmerID` = ?,
                                        `PetaniKakao` = 1,
                                        `WritingAwal` = 0,
                                        `WritingAkhir` = 0,
                                        `BallotAwal` = 0,
                                        `BallotAkhir` = 0,
                                        `DateCreated` = NOW(),
                                        `CreatedBy` = ?";
                                $p = array(
                                    $CpgBatchTrainingIDGAP,
                                    $ArrPesertaInsert[$iPeserta],
                                    $_SESSION['userid']
                                );
                                $query = $this->db->query($sql,$p);
                            }
                            //Proses Insert Peserta =============================== (End)

                            $RemarkGenerated = $RemarkGenerated."\n\nTraining Event GAP ID: $CpgBatchTrainingIDGAP\nNumber of Participants: $NrOfParticipants";
                        }else{
                            $RemarkGenerated = $RemarkGenerated."\n\nTraining Event GAP not generated [No Participant Eligible]";
                        }
                    }else{
                        $RemarkGenerated = $RemarkGenerated."\n\nTraining Event GAP not generated [Mapping = 0]";
                    }

                    //Topik COC
                    if($DataMapping['TopikCOC'] > 0){
                        //Cari & Filter Peserta ================ (Begin)

                        //Peserta Proses
                        $sql = "SELECT
                                    a.`DestObjID` AS FarmerID
                                FROM
                                    `ktv_ims_soc_sel` a
                                    INNER JOIN ktv_cocoa_farmer far ON a.`DestObjID` = far.`FarmerID`
                                WHERE
                                    a.`ApprovalBy` IS NOT NULL
                                    AND a.`ApprovalBy` != '0'
                                    AND a.`IMSID` = ?
                                    AND far.`CPGid` = ?
                                    AND a.ObjType = ?";
                        $p = array(
                            $ParamPost['IMSID'],
                            $ParamPost['SingleCpg'],
                            $ParamPost['SingleParticipantType']
                        );
                        $DataPesertaProses = $this->db->query($sql,$p)->result_array();
                        $ArrPesertaProses = $this->ConvertDataListPeserta($DataPesertaProses);

                        //Peserta yang sudah pernah ikut
                        $sql = "SELECT
                                    DISTINCT tfar.`FarmerID`
                                FROM
                                    `ktv_ims_training_generated` a
                                    INNER JOIN ktv_ims_training_generated_id b ON 1=1
                                        AND a.`ObjID` = b.`ObjID`
                                        AND b.`TrainingTopik` = 'TopikCOC'
                                    
                                    LEFT JOIN `ktv_cpg_batch_trainings_farmers` tfar ON 1=1
                                        AND b.`TrainingID` = tfar.`CpgBatchTrainingID`
                                        AND tfar.`StatusCode` = 'active'

                                    JOIN ktv_cpg_batch_trainings kcbt ON 1=1
                                        AND kcbt.`CpgBatchTrainingID` = tfar.`CpgBatchTrainingID`
                                        AND kcbt.`TrainingStatus` != '3' # bukan canceled
                                        
                                    LEFT JOIN ktv_cocoa_farmer far ON 1=1
                                        AND tfar.`FarmerID` = far.`FarmerID`
                                WHERE
                                    a.`ObjType` = 'CPG Training'
                                    AND a.`IMSID` = ?
                                    AND a.`ActivityType` = ?
                                    AND far.`CPGid` = ?";
                        $p = array(
                            $ParamPost['IMSID'],
                            $ParamPost['ActivityType'],
                            $ParamPost['SingleCpg']
                        );
                        $DataPesertaPernahIkut = $this->db->query($sql,$p)->result_array();
                        $ArrPesertaPernahIkut = $this->ConvertDataListPeserta($DataPesertaPernahIkut);
                        //Cari & Filter Peserta ================ (End)

                        //Bandingkan untuk cari $ArrPesertaInsert
                        $ArrPesertaInsert = $this->ProsesPesertaInsertTraining($ArrPesertaProses,$ArrPesertaPernahIkut);
                        if(isset($ArrPesertaInsert[0])){
                            //Proses Generate CPG Training =============================== (Begin)
                            $sql = "INSERT INTO `ktv_cpg_batch_trainings` SET
                                    `CPGid` = ?,
                                    `CPGtrainingsID` = '14', #GBP
                                    `CpgBatchID` = ?,
                                    `TrainingDays` = ?,
                                    `TrainingDayStatus` = 'full',
                                    `CertProgID` = ?,
                                    `CertHolderID` = ?,
                                    `IMSMasterID` = ?,
                                    `IMSID` = ?,
                                    `ActivityType` = ?, #full, refresh
                                    `RemidialType` = 'no', #yes, no
                                    `TrainingStatus` = '2',
                                    `RemarkProcess` = 'Generated by IMS Training',
                                    `DateCreated` = NOW(),
                                    `CreatedBy` = ?";
                            $p = array(
                                $ParamPost['SingleCpg'],
                                $DataIMS['CpgBatchID'],
                                $DataMapping['TopikCOC'],
                                $DataIMS['CertProgID'],
                                $DataIMS['CertHolderID'],
                                $DataIMS['IMSMasterID'],
                                $DataIMS['IMSID'],
                                $ActivityTypeCPGTraining,
                                $_SESSION['userid']
                            );
                            $query = $this->db->query($sql,$p);
                            $CpgBatchTrainingIDCOC = $this->db->insert_id();
                            //Proses Generate CPG Training =============================== (End)

                            //Proses Generate CPG Training - Subtopic =============================== (Begin)
                            $sql="INSERT INTO `ktv_cpg_batch_trainings_sub_topics` SET
                                `CpgBatchTrainingID` = ?,
                                `SubCpgTrainingsID` = '53', #COC
                                `DateCreated` = NOW(),
                                `CreatedBy` = ?";
                            $p = array(
                                $CpgBatchTrainingIDCOC,
                                $_SESSION['userid']
                            );
                            $query = $this->db->query($sql,$p);
                            //Proses Generate CPG Training - Subtopic =============================== (End)

                            //Proses Insert Peserta =============================== (Begin)
                            $NrOfParticipants = count($ArrPesertaInsert);
                            for ($iPeserta=0; $iPeserta < count($ArrPesertaInsert); $iPeserta++) { 
                                $sql = "INSERT INTO `ktv_cpg_batch_trainings_farmers` SET
                                        `CpgBatchTrainingID` = ?,
                                        `FarmerID` = ?,
                                        `PetaniKakao` = 1,
                                        `WritingAwal` = 0,
                                        `WritingAkhir` = 0,
                                        `BallotAwal` = 0,
                                        `BallotAkhir` = 0,
                                        `DateCreated` = NOW(),
                                        `CreatedBy` = ?";
                                $p = array(
                                    $CpgBatchTrainingIDCOC,
                                    $ArrPesertaInsert[$iPeserta],
                                    $_SESSION['userid']
                                );
                                $query = $this->db->query($sql,$p);
                            }
                            //Proses Insert Peserta =============================== (End)

                            $RemarkGenerated = $RemarkGenerated."\n\nTraining Event COC ID: $CpgBatchTrainingIDCOC\nNumber of Participants: $NrOfParticipants";
                        }else{
                            $RemarkGenerated = $RemarkGenerated."\n\nTraining Event COC not generated [No Participant Eligible]";
                        }
                    }else{
                        $RemarkGenerated = $RemarkGenerated."\n\nTraining Event COC not generated [Mapping = 0]";
                    }

                break;
                case 'Multiple CPG':
                    $RemarkGenerated = "Multiple CPG\nParticipant Type: {$ParamPost['MultipleParticipantType']}";

                    //ActivityType untuk Form CPG Training (ktv_cpg_batch_trainings) ====================== (Begin)
                    switch($ParamPost['MultipleParticipantType']){
                        case 'Applicant':
                            $ActivityTypeCPGTraining = 'full';
                        break;
                        case 'Existing Farmer':
                        case 'Existing Certified Farmer':
                            $ActivityTypeCPGTraining = 'refresh';
                        break;
                    }
                    //ActivityType untuk Form CPG Training (ktv_cpg_batch_trainings) ====================== (End)

                    //Topik GAP
                    if($DataMapping['TopikGAP'] > 0){
                        //Proses Generate CPG Training =============================== (Begin)
                        $sql = "INSERT INTO `ktv_cpg_batch_trainings` SET
                                `CPGid` = null,
                                `CPGtrainingsID` = '1',
                                `CpgBatchID` = ?,
                                `TrainingDays` = ?,
                                `TrainingDayStatus` = 'full',
                                `CertProgID` = ?,
                                `CertHolderID` = ?,
                                `IMSMasterID` = ?,
                                `IMSID` = ?,
                                `ActivityType` = ?, #full, refresh
                                `RemidialType` = ?, #yes, no
                                `TrainingStatus` = '2',
                                `RemarkProcess` = 'Generated by IMS Training',
                                `DateCreated` = NOW(),
                                `CreatedBy` = ?";
                        $p = array(
                            $DataIMS['CpgBatchID'],
                            $DataMapping['TopikGAP'],
                            $DataIMS['CertProgID'],
                            $DataIMS['CertHolderID'],
                            $DataIMS['IMSMasterID'],
                            $DataIMS['IMSID'],
                            $ActivityTypeCPGTraining,
                            $TrainingRemidialType,
                            $_SESSION['userid']
                        );
                        $query = $this->db->query($sql,$p);
                        $CpgBatchTrainingIDGAP = $this->db->insert_id();

                        $RemarkGenerated = $RemarkGenerated."\n\nTraining Event GAP ID: $CpgBatchTrainingIDGAP";
                        //Proses Generate CPG Training =============================== (End)
                    }else{
                        $RemarkGenerated = $RemarkGenerated."\n\nTraining Event GAP not generated [Mapping = 0]";
                    }

                    //Topik COC
                    if($DataMapping['TopikCOC'] > 0){
                        //Proses Generate CPG Training =============================== (Begin)
                        $sql = "INSERT INTO `ktv_cpg_batch_trainings` SET
                                `CPGid` = null,
                                `CPGtrainingsID` = '14', #GBP
                                `CpgBatchID` = ?,
                                `TrainingDays` = ?,
                                `TrainingDayStatus` = 'full',
                                `CertProgID` = ?,
                                `CertHolderID` = ?,
                                `IMSMasterID` = ?,
                                `IMSID` = ?,
                                `ActivityType` = ?, #full, refresh
                                `RemidialType` = ?, #yes, no
                                `TrainingStatus` = '2',
                                `RemarkProcess` = 'Generated by IMS Training',
                                `DateCreated` = NOW(),
                                `CreatedBy` = ?";
                        $p = array(
                            $DataIMS['CpgBatchID'],
                            $DataMapping['TopikCOC'],
                            $DataIMS['CertProgID'],
                            $DataIMS['CertHolderID'],
                            $DataIMS['IMSMasterID'],
                            $DataIMS['IMSID'],
                            $ActivityTypeCPGTraining,
                            $TrainingRemidialType,
                            $_SESSION['userid']
                        );
                        $query = $this->db->query($sql,$p);
                        $CpgBatchTrainingIDCOC = $this->db->insert_id();

                        $RemarkGenerated = $RemarkGenerated."\n\nTraining Event COC ID: $CpgBatchTrainingIDCOC";
                        //Proses Generate CPG Training =============================== (End)
                    }else{
                        $RemarkGenerated = $RemarkGenerated."\n\nTraining Event COC not generated [Mapping = 0]";
                    }
                break;
            }

            //Insert ke ktv_ims_training_generated dan ktv_ims_training_generated_id ======== (Begin)
            if($CpgBatchTrainingIDGAP != null || $CpgBatchTrainingIDCOC != null){
                $sql = "INSERT INTO `ktv_ims_training_generated` SET
                        `ObjType` = 'CPG Training',
                        IMSID = ?,
                        `EventType` = ?,
                        `ActivityType` = ?,
                        `Remark` = ?,
                        `DateGenerated` = NOW(),
                        `GeneratedBy` = ?
                ";
                $p = array(
                    $ParamPost['IMSID'],
                    $ParamPost['EventType'],
                    $ParamPost['ActivityType'],
                    $RemarkGenerated,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
                $ObjID = $this->db->insert_id();
            }

            if($CpgBatchTrainingIDGAP != null){
                $sql = "INSERT INTO `ktv_ims_training_generated_id` SET
                        `ObjID` = ?,
                        `TrainingID` = ?,
                        `TrainingTopik` = 'TopikGAP',
                        ParticipantType = ?
                        ";
                $p = array(
                    $ObjID,
                    $CpgBatchTrainingIDGAP,
                    $ParticipantType
                );
                $query = $this->db->query($sql,$p);
            }

            if($CpgBatchTrainingIDCOC != null){
                $sql = "INSERT INTO `ktv_ims_training_generated_id` SET
                        `ObjID` = ?,
                        `TrainingID` = ?,
                        `TrainingTopik` = 'TopikCOC',
                        ParticipantType = ?
                        ";
                $p = array(
                    $ObjID,
                    $CpgBatchTrainingIDCOC,
                    $ParticipantType
                );
                $query = $this->db->query($sql,$p);
            }
            //Insert ke ktv_ims_training_generated dan ktv_ims_training_generated_id ======== (End)

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = lang("Failed to generate training");
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = nl2br($RemarkGenerated);
            }
            return $results;
        }else{
            $results['success'] = false;
            $results['message'] = lang("No Data on Training Mapping");
            return $results;
        }
    }

    public function GetEventCpgTrainingMainGrid($IMSID){
        $sql = "SELECT
                    t.CpgBatchTrainingID
                    , IF(a.`EventType` = 'Single CPG',t.`CPGid`,'Multiple CPG') AS CPGid
                    , IF(a.`EventType` = 'Single CPG',
                        (SELECT subcpg.Groupname FROM ktv_cpg subcpg WHERE subcpg.CPGid = t.`CPGid` LIMIT 1)
                    ,'-') AS Groupname
                    , b.TrainingTopik AS TopicTraining
                    , COUNT(tp.FarmerID) AS JumlahPeserta
                    , IFNULL(DATE_FORMAT(t.TrainingStart,'%Y-%m-%d'),'-') AS TrainingStart
                    , t.TrainingDays AS JumlahPertemuan
                    , t.`TrainingStatus` AS EventStatus
                    , IFNULL(kp.`PersonNm`,'-') AS Fasilitator
                    , CONCAT(su.`UserRealName`,', ',a.`DateGenerated`) AS CreatedByLabel
                    , a.EventType
                    , b.`ParticipantType`
                    , t.RemidialType
                FROM
                    `ktv_ims_training_generated` a
                    LEFT JOIN `ktv_ims_training_generated_id` b ON a.`ObjID` = b.`ObjID`
                    LEFT JOIN `ktv_cpg_batch_trainings` t ON b.`TrainingID` = t.`CpgBatchTrainingID`
                    LEFT JOIN `ktv_cpg_batch_trainings_farmers` tp ON 
                        t.CpgBatchTrainingID = tp.CpgBatchTrainingID
                        AND tp.StatusCode = 'active'
                    LEFT JOIN ktv_persons kp ON t.`FacilitatorPersonID` = kp.`PersonID`
                    LEFT JOIN sys_user su ON a.`GeneratedBy` = su.`UserId`
                WHERE
                    a.`ObjType` = 'CPG Training'
                    AND a.`IMSID` = ?
                GROUP BY t.CpgBatchTrainingID
                ORDER BY b.`TrainingID` DESC";
        $Data = $this->db->query($sql,array($IMSID))->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetCpgTrainingFormData($CpgBatchTrainingID,$IMSID){
        $sql = "SELECT
                    a.`CpgBatchTrainingID`
                    , a.CPGid
                    , CONCAT(bat.BatchNumber,' - ',bat_p.PartnerName,' (',IFNULL(bat.BatchName,'-'),'/',IFNULL(bat.BatchYear,'-'),')') AS BatchNr
                    , top.`CpgTrainings` AS TrainingTopic
                    , GROUP_CONCAT(DISTINCT tops_top.`CpgTrainings` SEPARATOR ', ') AS TrainingSubtopic
                    , a.ActivityType
                    , a.RemidialType
                    , DATE_FORMAT(a.TrainingStart,'%Y-%m-%d') AS TrainingStart
                    , DATE_FORMAT(a.TrainingEnd,'%Y-%m-%d') AS TrainingEnd
                    , a.TrainingDays
                    , ims.`CertEventName` AS IMSLabel
                    , ch.`CertHolderOrgName` AS CertHolderLabel
                    , cp.`CertProgName` AS CertProgramLabel
                    , a.ProgramStaffID AS FacilitatorPersonID
                    , a.ExtensionStaffID
                    , a.TrainingStatus
                    , ims_tid.`ParticipantType`
                FROM
                    `ktv_cpg_batch_trainings` a
                    
                    LEFT JOIN ktv_cpg_batch bat ON a.`CpgBatchID` = bat.`CpgBatchID`
                    LEFT JOIN ktv_program_partner bat_p ON bat.`PartnerID` = bat_p.`PartnerID`
                    
                    LEFT JOIN `ktv_cpg_trainings` top ON a.`CPGtrainingsID` = top.`CpgTrainingsID`
                    LEFT JOIN `ktv_cpg_batch_trainings_sub_topics` tops ON a.`CpgBatchTrainingID` = tops.`CpgBatchTrainingID`
                    LEFT JOIN `ktv_cpg_trainings` tops_top ON tops.`SubCpgTrainingsID` = tops_top.`CpgTrainingsID`
                    
                    LEFT JOIN ktv_ims ims ON a.`IMSID` = ims.`IMSID`
                    LEFT JOIN `ktv_certification_holders` ch ON a.`CertHolderID` = ch.`CertHolderID`
                    LEFT JOIN ktv_ref_certification_program cp ON ch.`CertProgID` = cp.`CertProgID`

                    LEFT JOIN `ktv_ims_training_generated_id` ims_tid ON a.`CpgBatchTrainingID` = ims_tid.`TrainingID`
		            LEFT JOIN `ktv_ims_training_generated` ims_t ON ims_tid.`ObjID` = ims_t.`ObjID`
                WHERE
                    a.`CpgBatchTrainingID` = ?
                    AND ims_t.`IMSID` = ?
                GROUP BY a.`CpgBatchTrainingID`
                LIMIT 1
                ";
        $p = array(
            $CpgBatchTrainingID
        );
        $Data = $this->db->query($sql,array($CpgBatchTrainingID,$IMSID))->row_array();

        //prep variable
        $DataRow = array();
        foreach ($Data as $key => $value) {
            $keyNew = "Koltiva.view.IMS.WinFormImsTrainingCpg-Form-".$key;
            $DataRow[$keyNew] = $value;
        }

        //Buat dipakai langsung di JS
        //$DataRow['CertificationProgram'] = $Data['CertificationProgram'];

        $return['success'] = true;
        $return['data'] = $DataRow;
        return $return;
    }

    public function UpdateCpgTrainingForm($ParamPost){
        //echo '<pre>'; print_r($ParamPost); exit;

        //Set FacilitatorPersonID
        if($ParamPost['FacilitatorPersonID'] != "" && $ParamPost['FacilitatorPersonID'] != "0"){
            $sql = "SELECT a.`PersonID` FROM ktv_staffs a WHERE a.`StaffID` = ? LIMIT 1";
            $Data = $this->db->query($sql,array($ParamPost['FacilitatorPersonID']))->row_array();
            $FacilitatorPersonID = $Data['PersonID'];
            $ProgramStaffID = $ParamPost['FacilitatorPersonID'];
        }else{
            $FacilitatorPersonID = null;
            $ProgramStaffID = null;
        }

        $sql = "UPDATE `ktv_cpg_batch_trainings` SET
                    `FacilitatorPersonID` = ?,
                    ProgramStaffID = ?,
                    `ExtensionStaffID` = ?,
                    `TrainingStart` = ?,
                    `TrainingEnd` = ?,
                    `TrainingStatus` = ?,
                    `DateUpdated` = NOW(),
                    `LastModifiedBy` = ?
                WHERE 
                    `CpgBatchTrainingID` = ?
                LIMIT 1";
        $p = array(
            $FacilitatorPersonID,
            $ProgramStaffID,
            $ParamPost['ExtensionStaffID'],
            $ParamPost['TrainingStart'],
            $ParamPost['TrainingEnd'],
            $ParamPost['TrainingStatus'],
            $_SESSION['userid'],
            $ParamPost['CpgBatchTrainingID']
        );
        $query = $this->db->query($sql,$p);

        //Hitung Persentase n Lolos Training === (Begin)
        if($ParamPost['TrainingStatus'] == '1'){ 
            $this->load->model('tools/mpetani');

            //Minimal Persentase
            $MinimalPersentase = $this->db->select('SetValue')->from('sys_setting')->where('SetKey', 'min_percent_training' )->get()->row()->SetValue;
            $this->mpetani->SetPercentageParticipantCpgTrainingProcess($ParamPost['CpgBatchTrainingID'],$ParamPost['TrainingDays'],$MinimalPersentase);
        }
        //Hitung Persentase n Lolos Training === (End)

        if($query == true){
            $return['success'] = true;
            $return['message'] = lang('Data Training Updated');
        }else{
            $return['success'] = false;
            $return['message'] = lang('Failed to update data');
        }
        return $return;
    }

    public function GetGridCpgTrainingParticipants($CpgBatchTrainingID){
        $sql = "SELECT
                    par.`FarmerID`
                    , far.`FarmerName`
                    , CONCAT(cpg.`CPGid`,' - ',cpg.`GroupName`) AS FarmerGroup
                    , par.`Percentage` AS AttendancePersentase
                    , par.StatusTraining AS TrainingPassed
                    , par.`WritingAwal`
                    , par.`WritingAkhir`
                FROM
                    `ktv_cpg_batch_trainings_farmers` par
                    LEFT JOIN ktv_cocoa_farmer far ON par.`FarmerID` = far.`FarmerID`
                    LEFT JOIN ktv_cpg cpg ON far.`CPGid` = cpg.`CPGid`
                WHERE
                    par.`StatusCode` = 'active'
                    AND par.`CpgBatchTrainingID` = ?
                ORDER BY par.`FarmerID`";
        $Data = $this->db->query($sql,array($CpgBatchTrainingID))->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetGridColumnSummary($OpsiSummary){
        $return = array();

        switch($OpsiSummary){
            case 'participants_not_assign':
                $return[0]['text'] = lang('ID');
                $return[0]['dataIndex'] = 'ID';
                $return[0]['width'] = '6%';

                $return[1]['text'] = lang('Farmer ID');
                $return[1]['dataIndex'] = 'FarmerID';
                $return[1]['width'] = '6%';

                $return[2]['text'] = lang('Type');
                $return[2]['dataIndex'] = 'ObjType';
                $return[2]['width'] = '10%';

                $return[3]['text'] = lang('Name');
                $return[3]['dataIndex'] = 'Name';
                $return[3]['width'] = '15%';

                $return[4]['text'] = lang('Gender');
                $return[4]['dataIndex'] = 'Gender';
                $return[4]['width'] = '6%';

                $return[5]['text'] = lang('District');
                $return[5]['dataIndex'] = 'District';
                $return[5]['width'] = '8%';

                $return[6]['text'] = lang('SubDistrict');
                $return[6]['dataIndex'] = 'SubDistrict';
                $return[6]['width'] = '8%';

                $return[7]['text'] = lang('Village');
                $return[7]['dataIndex'] = 'Village';
                $return[7]['width'] = '8%';

                $return[8]['text'] = lang('Farmer Group');
                $return[8]['dataIndex'] = 'FarmerGroup';
                $return[8]['width'] = '13%';

                $return[9]['text'] = lang('Approval By');
                $return[9]['dataIndex'] = 'ApprovalBy';
                $return[9]['width'] = '8%';

                $return[10]['text'] = lang('Approval Remark');
                $return[10]['dataIndex'] = 'ApprovalRemark';
                $return[10]['width'] = '11%';
            break;
        }

        return $return;
    }

    public function GetSummaryShowData($OpsiSummary,$IMSID){
        $return = array();

        switch($OpsiSummary){
            case 'participants_not_assign':
                //Query Datanya
                $sql = "SELECT
                            a.`ObjID` AS ID
                            , a.`DestObjID` AS FarmerID
                            , a.`ObjType`
                            , a.`Gender`
                            , a.`Name`
                            , a.`District`
                            , a.`SubDistrict`
                            , a.`Village`
                            , a.`FarmerGroup`
                            , su.`UserRealName` AS ApprovalBy
                            , a.`ApprovalRemark`
                        FROM
                            `ktv_ims_soc_sel` a
                            LEFT JOIN (
                                SELECT
                                    DISTINCT tfar.`FarmerID`
                                FROM
                                    `ktv_ims_training_generated` a
                                    INNER JOIN ktv_ims_training_generated_id b ON 1=1
                                        AND a.`ObjID` = b.`ObjID`
                                    
                                    LEFT JOIN `ktv_cpg_batch_trainings_farmers` tfar ON 1=1
                                        AND b.`TrainingID` = tfar.`CpgBatchTrainingID`
                                        AND tfar.`StatusCode` = 'active'
                                WHERE
                                    a.`ObjType` = 'CPG Training'
                                    AND a.`IMSID` = ?
                            ) AS t_tpar ON 1=1
                                AND a.`DestObjID` = t_tpar.FarmerID
                            
                            LEFT JOIN sys_user su ON a.`ApprovalBy` = su.`UserId`
                        WHERE
                            a.`ApprovalBy` IS NOT NULL
                            AND a.`ApprovalBy` != '0'
                            AND a.`IMSID`= ?
                            AND t_tpar.FarmerID IS NULL
                        ORDER BY a.`DestObjID` ASC";
                $DataList = $this->db->query($sql,array($IMSID,$IMSID))->result_array();
                
                if(isset($DataList[0]['ID'])){
                    //Susun urutan field ================== (Begin)
                    $IncreKolom = 0;
                    $DataKolom = array();
                    foreach ($DataList as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            $DataKolom[$IncreKolom]['name'] = $key1;
                            $IncreKolom++;
                        }
                        break;
                    }
                    //Susun urutan field ==================== (End)

                    //Susun grid kolom
                    $DataGridColumn = $this->GetGridColumnSummary($OpsiSummary);

                    $return['success'] = true;
                    $return['message'] = lang('Success');
                    $return['AdaData'] = true;
                    $return['GridModel'] = $DataKolom;
                    $return['GridColumn'] = $DataGridColumn;
                }else{
                    $return['success'] = true;
                    $return['message'] = lang('Success');
                    $return['AdaData'] = false;
                }

                $return['success'] = true;
                $return['message'] = lang('Success');
            break;
            default:
                $return['success'] = false;
                $return['message'] = lang('Opsi Summary not found');
            break;
        }
        
        return $return;
    }

    public function GetSummaryShowDataParNotAssign($IMSID,$start,$limit,$sortingField,$sortingDir,$CallForm){
        if($sortingField == "") $sortingField = 'FarmerID';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`ObjID` AS ID
                    , a.`DestObjID` AS FarmerID
                    , a.`ObjType`
                    , a.`Gender`
                    , a.`Name`
                    , a.`District`
                    , a.`SubDistrict`
                    , a.`Village`
                    , a.`FarmerGroup`
                    , su.`UserRealName` AS ApprovalBy
                    , a.`ApprovalRemark`
                FROM
                    `ktv_ims_soc_sel` a
                    LEFT JOIN (
                        SELECT
                            DISTINCT tfar.`FarmerID`
                        FROM
                            `ktv_ims_training_generated` a
                            INNER JOIN ktv_ims_training_generated_id b ON 1=1
                                AND a.`ObjID` = b.`ObjID`
                            
                            LEFT JOIN `ktv_cpg_batch_trainings_farmers` tfar ON 1=1
                                AND b.`TrainingID` = tfar.`CpgBatchTrainingID`
                                AND tfar.`StatusCode` = 'active'
                        WHERE
                            a.`ObjType` = 'CPG Training'
                            AND a.`IMSID` = ?
                    ) AS t_tpar ON 1=1
                        AND a.`DestObjID` = t_tpar.FarmerID
                    
                    LEFT JOIN sys_user su ON a.`ApprovalBy` = su.`UserId`
                WHERE
                    a.`ApprovalBy` IS NOT NULL
                    AND a.`ApprovalBy` != '0'
                    AND a.`IMSID`= ?
                    AND t_tpar.FarmerID IS NULL
                ORDER BY $sortingField $sortingDir";

        if($CallForm == 'non_grid'){
            return $this->db->query($sql,array($IMSID,$IMSID))->result_array();
        }

        $DataList = $this->db->query($sql." LIMIT ?,?",array($IMSID,$IMSID,(int) $start,(int) $limit))->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        $return['success'] = true;
        $return['data'] = $DataList;
        return $return;
    }

    public function GetCpgTrainingAddParMainGrid($IMSID,$CpgBatchTrainingID,$ParticipantType,$SearchStringParam,$SearchCpgParam,$start,$limit,$sortingField,$sortingDir){
        //Cek apakah remedial atau bukan
        $sql = "SELECT
                    a.`RemidialType`
                    , CASE
                        WHEN a.`CPGtrainingsID`='1' THEN 'TopikGAP'
                        WHEN a.`CPGtrainingsID`='14' THEN 'TopikCOC'
                    END AS TopikTraining
                FROM
                    `ktv_cpg_batch_trainings` a
                WHERE
                    a.`CpgBatchTrainingID` = ?
                LIMIT 1";
        $DataTraining = $this->db->query($sql,array($CpgBatchTrainingID))->row_array();

        //============ Get Data Grid (Begin) ==========================//
        if($sortingField == "") $sortingField = 'FarmerID';
        if($sortingDir == "") $sortingDir = 'ASC';

        switch($DataTraining['RemidialType']){
            case 'no':
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            a.`DestObjID` AS FarmerID
                            , a.`Name` AS FarmerName
                            , a.`Gender`
                            , a.`SubDistrict`
                            , a.`Village`
                            , a.`FarmerGroup`
                        FROM
                            ktv_ims_soc_sel a
                            LEFT JOIN (
                                SELECT
                                    DISTINCT ct_p.`FarmerID`
                                FROM
                                    `ktv_ims_training_generated` a
                                    INNER JOIN `ktv_ims_training_generated_id` b ON a.`ObjID` = b.`ObjID`
                                    
                                    LEFT JOIN `ktv_cpg_batch_trainings` ct ON 1=1 
                                        AND b.`TrainingID` = ct.`CpgBatchTrainingID`
                                        AND ct.`StatusCode` = 'active'
                                    LEFT JOIN `ktv_cpg_batch_trainings_farmers` ct_p ON 1=1
                                        AND ct.`CpgBatchTrainingID` = ct_p.`CpgBatchTrainingID`
                                        AND ct_p.`StatusCode` = 'active'
                                WHERE
                                    a.`IMSID` = ?
                                    AND a.`ActivityType` = 'Full'
                                    AND b.`TrainingTopik` = ?
                                    AND ct_p.`FarmerID` IS NOT NULL
                            ) AS tr_full ON 1=1
                                AND a.`DestObjID` = tr_full.FarmerID
                        WHERE
                            a.`IMSID` = ?
                            AND a.`ApprovalBy` IS NOT NULL
                            AND a.`ApprovalBy` != '0'
                            AND a.`ObjType` = ?
                            AND tr_full.FarmerID IS NULL
                            AND ( (a.`DestObjID` LIKE ?) OR (a.`Name` LIKE ?) )
	                        AND a.`FarmerGroup` LIKE ?
                        ORDER BY $sortingField $sortingDir
                        LIMIT ?,?";
                $p = array(
                    $IMSID,$DataTraining['TopikTraining'],$IMSID,$ParticipantType,
                    '%'.$SearchStringParam.'%','%'.$SearchStringParam.'%', '%'.$SearchCpgParam.'%',
                    (int) $start,(int) $limit
                );
                $DataList = $this->db->query($sql,$p)->result_array();
            break;
            case 'yes':
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            DISTINCT ct_p.`FarmerID`
                            , ss.`Name` AS FarmerName
                            , ss.`Gender`
                            , ss.`SubDistrict`
                            , ss.`Village`
                            , ss.`FarmerGroup`
                        FROM
                            `ktv_ims_training_generated` a
                            INNER JOIN `ktv_ims_training_generated_id` b ON a.`ObjID` = b.`ObjID`
                            
                            LEFT JOIN `ktv_cpg_batch_trainings` ct ON 1=1 
                                AND b.`TrainingID` = ct.`CpgBatchTrainingID`
                                AND ct.`StatusCode` = 'active'
                            LEFT JOIN `ktv_cpg_batch_trainings_farmers` ct_p ON 1=1
                                AND ct.`CpgBatchTrainingID` = ct_p.`CpgBatchTrainingID`
                                AND ct_p.`StatusCode` = 'active'
                            
                            LEFT JOIN ktv_ims_soc_sel ss ON 1=1 
                                AND ct_p.`FarmerID` = ss.`DestObjID`
                                AND ss.`IMSID` = ?
                        WHERE
                            a.`IMSID` = ?
                            AND b.`TrainingTopik` = ?
                            AND ct_p.`FarmerID` IS NOT NULL
                            AND ct.`TrainingStatus` = '1'
                            AND ct_p.`FarmerID` NOT IN (
                                SELECT
                                    DISTINCT ct_p.`FarmerID`
                                FROM
                                    `ktv_ims_training_generated` a
                                    INNER JOIN `ktv_ims_training_generated_id` b ON a.`ObjID` = b.`ObjID`
                                    
                                    LEFT JOIN `ktv_cpg_batch_trainings` ct ON 1=1 
                                        AND b.`TrainingID` = ct.`CpgBatchTrainingID`
                                        AND ct.`StatusCode` = 'active'
                                    LEFT JOIN `ktv_cpg_batch_trainings_farmers` ct_p ON 1=1
                                        AND ct.`CpgBatchTrainingID` = ct_p.`CpgBatchTrainingID`
                                        AND ct_p.`StatusCode` = 'active'
                                WHERE
                                    a.`IMSID` = ?
                                    AND b.`TrainingTopik` = ?
                                    AND ct_p.`FarmerID` IS NOT NULL
                                    AND ct.`TrainingStatus` = '2'
                            )
                            AND ( (ct_p.`FarmerID` LIKE ?) OR (ss.`Name` LIKE ?) )
	                        AND ss.`FarmerGroup` LIKE ?
                        ORDER BY $sortingField $sortingDir
                        LIMIT ?,?";
                $p = array(
                    $IMSID,$IMSID,$DataTraining['TopikTraining'],$IMSID,$DataTraining['TopikTraining'],
                    '%'.$SearchStringParam.'%','%'.$SearchStringParam.'%', '%'.$SearchCpgParam.'%',
                    (int) $start,(int) $limit
                );
                $DataList = $this->db->query($sql,$p)->result_array();
            break;
            default:
                $DataList = array();
            break;
        }

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        $return['success'] = true;
        $return['data'] = $DataList;
        return $return;
        //============ Get Data Grid (End) ==========================//
    }

    public function CpgTrainingAddParticipant($CpgBatchTrainingID,$FarmerIDSel){
        $this->db->trans_begin();

        for ($i=0; $i < count($FarmerIDSel); $i++) { 
            $sql = "INSERT INTO `ktv_cpg_batch_trainings_farmers` SET
                    `CpgBatchTrainingID` = ?,
                    `FarmerID` = ?,
                    `PetaniKakao` = 1,
                    `WritingAwal` = 0,
                    `WritingAkhir` = 0,
                    `BallotAwal` = 0,
                    `BallotAkhir` = 0,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $CpgBatchTrainingID,
                $FarmerIDSel[$i],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to insert participants");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Participants inserted");
        }
        return $results;
    }

    public function DeleteCpgTrainingParticipant($CpgBatchTrainingID,$FarmerID){
        //Cek dl apakah sudah ada data attendance
        $sql = "SELECT
                    COUNT(*) AS BANYAK
                FROM
                    `ktv_cpg_batch_trainings_attendance` a
                WHERE
                    a.`CpgBatchTrainingID` = ?
                    AND a.`FarmerID` = ?";
        $DataCek = $this->db->query($sql,array($CpgBatchTrainingID,$FarmerID))->row_array();
        $Attendance = (int) $DataCek['BANYAK'];

        if($Attendance > 0){
            $results['success'] = false;
            $results['message'] = lang("Participant already have attendance data");
            return $results;
        }else{
            $sql = "DELETE FROM `ktv_cpg_batch_trainings_farmers` WHERE CpgBatchTrainingID = ? AND FarmerID = ?";
            $query = $this->db->query($sql,array($CpgBatchTrainingID,$FarmerID));

            if($query == true){
                $results['success'] = true;
                $results['message'] = lang("Participants deleted");
            }else{
                $results['success'] = false;
                $results['message'] = lang("Failed to delete participant");
            }
            return $results;
        }
    }

    public function GetGridEventMappingTraining($IMSID,$TrainingType,$ActivityType,$ParticipantType){
        $sql = "SELECT
                a.`ActivityType`
                , a.`ParticipantType`
                , a.`TopikGAP`
                , a.`TopikCOC`
            FROM
                ktv_ims_training_event_mapping a
            WHERE
                a.`IMSID` = ?
                AND a.`TrainingType` = ?
                AND a.`ActivityType` = ?
                AND a.`ParticipantType` = ?
            LIMIT 1";
        $data = $this->db->query($sql,array($IMSID,$TrainingType,$ActivityType,$ParticipantType))->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetGridTrainingGapAvailableParticipant($IMSID,$TrainingType,$EventType,$ActivityType,$ParticipantType,$CPGid){
        $data = array();

        if($EventType == 'Single CPG'){
            //Cek dl apakah data mappingnya
            $sql = "SELECT
                    a.IMSID
                    , a.`TopikGAP`
                    , a.`TopikCOC`
                FROM
                    `ktv_ims_training_event_mapping` a
                WHERE
                    a.`IMSID` = ?
                    AND a.TrainingType = 'CPG Training'
                    AND a.`ActivityType` = ?
                    AND a.`ParticipantType` = ?
                LIMIT 1";
            $DataMapping = $this->db->query($sql,array($IMSID,$ActivityType,$ParticipantType))->row_array();
            if(isset($DataMapping['IMSID'])){
                if($DataMapping['TopikGAP'] > 0){
                    //Cari & Filter Peserta ================ (Begin)
                    //Peserta Proses
                    $sql = "SELECT
                                a.`DestObjID` AS FarmerID
                            FROM
                                `ktv_ims_soc_sel` a
                                INNER JOIN ktv_cocoa_farmer far ON a.`DestObjID` = far.`FarmerID`
                            WHERE
                                a.`ApprovalBy` IS NOT NULL
                                AND a.`ApprovalBy` != '0'
                                AND a.`IMSID` = ?
                                AND far.`CPGid` = ?
                                AND a.ObjType = ?";
                    $p = array(
                        $IMSID,
                        $CPGid,
                        $ParticipantType
                    );
                    $DataPesertaProses = $this->db->query($sql,$p)->result_array();
                    $ArrPesertaProses = $this->ConvertDataListPeserta($DataPesertaProses);

                    //Peserta yang sudah pernah ikut
                    $sql = "SELECT
                                DISTINCT tfar.`FarmerID`
                            FROM
                                `ktv_ims_training_generated` a
                                INNER JOIN ktv_ims_training_generated_id b ON 1=1
                                    AND a.`ObjID` = b.`ObjID`
                                    AND b.`TrainingTopik` = 'TopikGAP'
                                
                                LEFT JOIN `ktv_cpg_batch_trainings_farmers` tfar ON 1=1
                                    AND b.`TrainingID` = tfar.`CpgBatchTrainingID`
                                    AND tfar.`StatusCode` = 'active'

                                JOIN ktv_cpg_batch_trainings kcbt ON 1=1
                                    AND kcbt.`CpgBatchTrainingID` = tfar.`CpgBatchTrainingID`
                                    AND kcbt.`TrainingStatus` != '3' # bukan canceled
                                    
                                LEFT JOIN ktv_cocoa_farmer far ON 1=1
                                    AND tfar.`FarmerID` = far.`FarmerID`
                            WHERE
                                a.`ObjType` = 'CPG Training'
                                AND a.`IMSID` = ?
                                AND a.`ActivityType` = ?
                                AND far.`CPGid` = ?";
                    $p = array(
                        $IMSID,
                        $ActivityType,
                        $CPGid
                    );
                    $DataPesertaPernahIkut = $this->db->query($sql,$p)->result_array();
                    $ArrPesertaPernahIkut = $this->ConvertDataListPeserta($DataPesertaPernahIkut);
                    
                    //Bandingkan untuk cari $ArrPesertaInsert
                    $ArrPesertaInsert = $this->ProsesPesertaInsertTraining($ArrPesertaProses,$ArrPesertaPernahIkut);
                    //Cari & Filter Peserta ================ (End)

                    if(count($ArrPesertaInsert) > 0){
                        $TmpFarmerID = implode(",",$ArrPesertaInsert);

                        $sql = "SELECT
                                    a.`FarmerID`
                                    , a.`FarmerName`
                                    , dis.`District`
                                    , subd.`SubDistrict`
                                    , vil.`Village`
                                FROM
                                    ktv_cocoa_farmer a
                                    LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                                WHERE
                                    a.`FarmerID` IN ({$TmpFarmerID})
                                ORDER BY  a.`FarmerID` ASC";
                        $data = $this->db->query($sql)->result_array();
                    }
                }
            }
        }

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetGridTrainingCocAvailableParticipant($IMSID,$TrainingType,$EventType,$ActivityType,$ParticipantType,$CPGid){
        $data = array();

        if($EventType == 'Single CPG'){
            //Cek dl apakah data mappingnya
            $sql = "SELECT
                    a.IMSID
                    , a.`TopikGAP`
                    , a.`TopikCOC`
                FROM
                    `ktv_ims_training_event_mapping` a
                WHERE
                    a.`IMSID` = ?
                    AND a.TrainingType = 'CPG Training'
                    AND a.`ActivityType` = ?
                    AND a.`ParticipantType` = ?
                LIMIT 1";
            $DataMapping = $this->db->query($sql,array($IMSID,$ActivityType,$ParticipantType))->row_array();
            if(isset($DataMapping['IMSID'])){
                if($DataMapping['TopikCOC'] > 0){
                    //Cari & Filter Peserta ================ (Begin)
                    //Peserta Proses
                    $sql = "SELECT
                                a.`DestObjID` AS FarmerID
                            FROM
                                `ktv_ims_soc_sel` a
                                INNER JOIN ktv_cocoa_farmer far ON a.`DestObjID` = far.`FarmerID`
                            WHERE
                                a.`ApprovalBy` IS NOT NULL
                                AND a.`ApprovalBy` != '0'
                                AND a.`IMSID` = ?
                                AND far.`CPGid` = ?
                                AND a.ObjType = ?";
                    $p = array(
                        $IMSID,
                        $CPGid,
                        $ParticipantType
                    );
                    $DataPesertaProses = $this->db->query($sql,$p)->result_array();
                    $ArrPesertaProses = $this->ConvertDataListPeserta($DataPesertaProses);

                    //Peserta yang sudah pernah ikut
                    $sql = "SELECT
                                DISTINCT tfar.`FarmerID`
                            FROM
                                `ktv_ims_training_generated` a
                                INNER JOIN ktv_ims_training_generated_id b ON 1=1
                                    AND a.`ObjID` = b.`ObjID`
                                    AND b.`TrainingTopik` = 'TopikCOC'
                                
                                LEFT JOIN `ktv_cpg_batch_trainings_farmers` tfar ON 1=1
                                    AND b.`TrainingID` = tfar.`CpgBatchTrainingID`
                                    AND tfar.`StatusCode` = 'active'

                                JOIN ktv_cpg_batch_trainings kcbt ON 1=1
                                    AND kcbt.`CpgBatchTrainingID` = tfar.`CpgBatchTrainingID`
                                    AND kcbt.`TrainingStatus` != '3' # bukan canceled
                                    
                                LEFT JOIN ktv_cocoa_farmer far ON 1=1
                                    AND tfar.`FarmerID` = far.`FarmerID`
                            WHERE
                                a.`ObjType` = 'CPG Training'
                                AND a.`IMSID` = ?
                                AND a.`ActivityType` = ?
                                AND far.`CPGid` = ?";
                    $p = array(
                        $IMSID,
                        $ActivityType,
                        $CPGid
                    );
                    $DataPesertaPernahIkut = $this->db->query($sql,$p)->result_array();
                    $ArrPesertaPernahIkut = $this->ConvertDataListPeserta($DataPesertaPernahIkut);
                    //Cari & Filter Peserta ================ (End)

                    //Bandingkan untuk cari $ArrPesertaInsert
                    $ArrPesertaInsert = $this->ProsesPesertaInsertTraining($ArrPesertaProses,$ArrPesertaPernahIkut);

                    if(count($ArrPesertaInsert) > 0){
                        $TmpFarmerID = implode(",",$ArrPesertaInsert);

                        $sql = "SELECT
                                    a.`FarmerID`
                                    , a.`FarmerName`
                                    , dis.`District`
                                    , subd.`SubDistrict`
                                    , vil.`Village`
                                FROM
                                    ktv_cocoa_farmer a
                                    LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                                WHERE
                                    a.`FarmerID` IN ({$TmpFarmerID})
                                ORDER BY  a.`FarmerID` ASC";
                        $data = $this->db->query($sql)->result_array();
                    }
                }
            }
        }

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetGridTrainingEventMapping($IMSID){
        $this->db->from('ktv_ims_training_event_mapping');
        $this->db->where('IMSID', $IMSID);
        $data = $this->db->get()->result();

        return array(
                    'data' => $data,
                    'total' => $this->CounterTrainingEventMappingByIMSID($IMSID)
                );
    }

    private function CounterTrainingEventMappingByIMSID($IMSID){
        $this->db->from('ktv_ims_training_event_mapping');
        $this->db->where('IMSID', $IMSID);

        return $this->db->get()->num_rows();
    }

    public function CreateTrainingEventMapping($data){
        $this->db->from('ktv_ims_training_event_mapping');
        $this->db->where('IMSID', $data['IMSID']);
        $this->db->where('TrainingType', $data['TrainingType']);
        $this->db->where('ActivityType', $data['ActivityType']);
        $this->db->where('ParticipantType', $data['ParticipantType']);

        if ($this->db->get()->num_rows() > 0) {
            # code...
            return array(
                    'success' => false,
                    'message' => lang('Data already exist')
                );
        } else {
            # code...
            $this->db->insert('ktv_ims_training_event_mapping', $data);

            if($this->db->affected_rows() > 0){
                return array(
                    'success' => false,
                    'message' => lang('Insert success')
                );
            } else {
                return array(
                    'success' => false,
                    'message' => lang('Insert Failed')
                );
            }
        }
    }

    public function UpdateTrainingEventMapping($data){
        $this->db->where('IMSID', $data['IMSID']);
        $this->db->where('TrainingType', 'CPG Training');
        $this->db->where('ActivityType', $data['ActivityType']);
        $this->db->where('ParticipantType', $data['ParticipantType']);
        $this->db->update(
            'ktv_ims_training_event_mapping', 
            array('TopikGAP' => $data['TopikGAP'], 'TopikCOC' => $data['TopikCOC'])
        );

        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    public function DeleteTrainingEventMapping($IMSID, $TrainingType, $ActivityType, $ParticipantType){
        $this->db->where('IMSID', $IMSID);
        $this->db->where('TrainingType', $TrainingType);
        $this->db->where('ActivityType', $ActivityType);
        $this->db->where('ParticipantType', $ParticipantType);
        $this->db->delete('ktv_ims_training_event_mapping');

        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
    }
}