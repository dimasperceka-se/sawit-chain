<?php
/**
 * @Author: nikolius
 * @Date:   2017-06-01 16:48:54
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mhousehold_survey extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridHouseholdSurveySummary($MemberID){
        $sql="SELECT
                a.`SurveyNr`
                , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
                , a.`DateCollection`
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM
                ktv_survey_household a
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
            ORDER BY  a.`SurveyNr` ASC";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['SurveyNr'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function checkIfSurveyExist($paramPost){
        $sql="SELECT
                a.`MemberID`
            FROM
                ktv_survey_household a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
                AND a.`SurveyNr` = ?
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['SurveyNr']
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        if($data['MemberID'] != ""){
            return true;
        }else{
            return false;
        }
    }

    public function getHouseholdSurveyFormData($MemberID,$SurveyNr,$DateCollection){
        $sql="SELECT
                a.`MemberID`,
                b.`MemberDisplayID`,
                b.`MemberName`,
                a.`SurveyNr`,
                a.`DateCollection`,
                #(SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedByLabel,
                /*CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS CreatedByLabel,*/
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS CreatedByLabel,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedByLabel,
                a.`HaveBankAccount`,
                a.`UseMobileBanking`,
                a.HaveAndroIphone,
                a.HaveAccEmailFbWa,
                a.TypeOfPhone,
                a.TimeAccessInet,
                a.`HhMember`,
                a.`HhInSchoolEarlyAge`,
                a.`FemaleEduLevel`,
                a.`MaleMainOccu`,
                a.`TypeOfFloor`,
                a.TypeOfFloorText,
                a.`TypeOfToilet`,
                a.`PrimaryFuel`,
                a.`Own12KgGas`,
                a.`OwnRefri`,
                a.`OwnMotor`,
                a.`OwnPrivateCar`,
                a.`OwnGriddedElectricity`,
                a.`OwnComputer`,
                a.`OwnAC`,
                a.`AvgDaysConsumeBeef`,
                a.`ExpectationOfImprovement`,
                a.`WorkPalmCoverEconomy`,
                a.`NeedsCoverFoods`,
                a.`NeedsCoverHousing`,
                a.`NeedsCoverClothing`,
                a.`NeedsCoverEducation`,
                a.`NeedsCoverHouseEquip`,
                a.`NeedsCoverRecre`,
                a.`NeedsCoverOther`,
                a.`NeedsCoverOtherComment`,
                a.`ThinkAnotherJobPlant`,
                a.`HaveLoan`,
                a.`WhereLoanFrom`,
                a.`LoanForPalm`,
                a.HhMemberTenOWNotWork,
                a.HhMemberTenOWMainJob,
                a.HhMemberHavePhone,
                a.HHMainFloorType,
                a.BoughtPoorRice,
                a.HhInSchoolEarlyAge,
                a.OtherIncome,
                a.OtherIncomeType,
                a.OtherIncomeJob,
                a.OtherIncomeSendMoney,
                a.OtherIncomeSpouse,
                a.OtherIncomeBusiness,
                a.OtherIncomeCrops,
                a.OtherIncomeOther,
                a.ProvinceID,
                a.DiscloceIncome,
                a.DiscloceIncomeMonthly,
                a.DiscloceIncomeFarmer,
                a.DiscloceIncomeSpend,
                a.DiscloceIncomeHousehold
            FROM
                `ktv_survey_household` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
            WHERE
                a.`MemberID` = ?
                AND a.`SurveyNr` = ?
                AND a.`DateCollection` = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertHouseholdSurvey($paramPost){
        $this->db->trans_start();

        $this->load->model('grower/mgrower');

        $MemberDisplayID = $paramPost['MemberDisplayID'];

        //buang var yg tidak perlu (begin)
        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['opsiDisplay']);
        //buang var yg tidak perlu (end)

        $uid = $this->mgrower->getUID();
        
        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;
        $paramPost['uid']       = $uid;

        //insert
        $this->db->insert('ktv_survey_household', $paramPost);

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

    public function updateHouseholdSurvey($paramPost){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberID = $paramPost['MemberID'];
        $SurveyNr = $paramPost['SurveyNr'];
        $DateCollection = $paramPost['DateCollection'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['MemberID']);
        unset($paramPost['SurveyNr']);
        unset($paramPost['DateCollection']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        //reset semua dulu
        $sql="UPDATE `ktv_survey_household` SET
                `HaveBankAccount` = null,
                `UseMobileBanking` = null,
                HaveAndroIphone = null,
                HaveAccEmailFbWa = null,
                TypeOfPhone = null,
                TimeAccessInet = null,
                `HhMember` = null,
                `HhInSchoolEarlyAge` = null,
                `FemaleEduLevel` = null,
                `MaleMainOccu` = null,
                `TypeOfFloor` = null,
                `TypeOfFloorText` = null,
                `TypeOfToilet` = null,
                `PrimaryFuel` = null,
                `Own12KgGas` = null,
                `OwnRefri` = null,
                `OwnMotor` = null,
                `OwnPrivateCar` = null,
                `OwnGriddedElectricity` = null,
                `OwnComputer` = null,
                `OwnAC` = null,
                `AvgDaysConsumeBeef` = null,
                `ExpectationOfImprovement` = null,
                `WorkPalmCoverEconomy` = null,
                `NeedsCoverFoods` = null,
                `NeedsCoverHousing` = null,
                `NeedsCoverClothing` = null,
                `NeedsCoverEducation` = null,
                `NeedsCoverHouseEquip` = null,
                `NeedsCoverRecre` = null,
                `NeedsCoverOther` = null,
                `NeedsCoverOtherComment` = null,
                `ThinkAnotherJobPlant` = null,
                `HaveLoan` = null,
                `WhereLoanFrom` = null,
                `LoanForPalm` = null,
                `DiscloceIncome` = null,
                `DiscloceIncomeMonthly` = null,
                `DiscloceIncomeFarmer` = null,
                `DiscloceIncomeSpend` = null,
                `DiscloceIncomeHousehold` = null
            WHERE
                `MemberID` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('SurveyNr', $SurveyNr);
        $this->db->where('DateCollection', $DateCollection);
        $query = $this->db->update('ktv_survey_household', $paramPost);

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

    public function deleteHouseholdSurvey($MemberID,$SurveyNr,$DateCollection){
        $this->db->trans_start();

        $sql="INSERT INTO ktv_survey_household_nullified
            SELECT
                *
            FROM
                ktv_survey_household a
            WHERE
                a.MemberID = ?
                AND a.SurveyNr = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $sql="DELETE FROM ktv_survey_household WHERE MemberID = ? AND SurveyNr = ? LIMIT 1";
        $p = array(
            $MemberID,
            $SurveyNr
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