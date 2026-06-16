<?php

/**
 * @Author: nikolius
 * @Date:   2017-11-02 16:20:10
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-11-03 11:16:54
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mfinance_survey extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    public function getGridFinanceSurveySummary($MemberID){
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
                ktv_survey_finance a
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

    public  function checkIfSurveyExist($paramPost){
        $sql="SELECT
                a.`MemberID`
            FROM
                ktv_survey_finance a
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

    public function getFinanceSurveyFormData($MemberID,$SurveyNr){
        $sql="SELECT
              a.`MemberID`,
              a.`MemberUid`,
              b.`MemberName`,
              b.`MemberDisplayID`,
              a.`SurveyNr`,
              a.`DateCollection`,
              CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS CreatedByLabel,
              CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedByLabel,
              a.`aInvestOnLivestock`,
              a.`aTypeOfLivestock`,
              a.`aValueOfLivestock`,
              a.`aMonthlyIncomeOtherCrop`,
              a.`aRevenueRemit`,
              a.`aValueRemitPerYear`,
              a.`aInvolvedNonAgriBusiness`,
              a.`aTypeOfNonAgriBusiness`,
              a.`aRevenueToHousehold`,
              a.`aIncomeOtherPlot`,
              a.`aTransportCost`,
              a.`bHaveOutstandingDebts`,
              a.`bValueOfDebt`,
              a.`bTenorYear`,
              a.`bTimeToMature`,
              a.`bInterestRate`,
              a.`bHowMuchInterestRate`,
              a.`bWhereDoYouHaveLoan`,
              a.`bNameOfBPR`,
              a.`bNameOfCoop`,
              a.`bNameOfBank`,
              a.`bLevelCurrentSavings`,
              a.`bTypeOfHealthInsurance`,
              a.`bFertAccess`,
              a.`bSourceOfFinancing`,
              a.`cApplyNewLoan`,
              a.`cAcceptableInterestRate`
            FROM
                `ktv_survey_finance` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
            WHERE
                a.`MemberID` = ?
                AND a.`SurveyNr` = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            (int) $SurveyNr
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertFinanceSurvey($paramPost){
        $this->db->trans_start();

        $MemberDisplayID = $paramPost['MemberDisplayID'];

        //buang var yg tidak perlu (begin)
        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['opsiDisplay']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;

        //insert
        $this->db->insert('ktv_survey_finance', $paramPost);

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

    public function resetSurveyField($MemberID,$SurveyNr){
        $sql="UPDATE `ktv_survey_finance` SET
              `aInvestOnLivestock` = null,
              `aTypeOfLivestock` = null,
              `aValueOfLivestock` = null,
              `aMonthlyIncomeOtherCrop` = null,
              `aRevenueRemit` = null,
              `aValueRemitPerYear` = null,
              `aInvolvedNonAgriBusiness` = null,
              `aTypeOfNonAgriBusiness` = null,
              `aRevenueToHousehold` = null,
              `aIncomeOtherPlot` = null,
              `aTransportCost` = null,
              `bHaveOutstandingDebts` = null,
              `bValueOfDebt` = null,
              `bTenorYear` = null,
              `bTimeToMature` = null,
              `bInterestRate` = null,
              `bHowMuchInterestRate` = null,
              `bWhereDoYouHaveLoan` = null,
              `bNameOfBPR` = null,
              `bNameOfCoop` = null,
              `bNameOfBank` = null,
              `bLevelCurrentSavings` = null,
              `bTypeOfHealthInsurance` = null,
              `bFertAccess` = null,
              `bSourceOfFinancing` = null,
              `cApplyNewLoan` = null,
              `cAcceptableInterestRate` = null
            WHERE
                `MemberID` = ?
                AND `SurveyNr` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($MemberID, $SurveyNr));
    }

    public function updateFinanceSurvey($paramPost){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberID = $paramPost['MemberID'];
        $SurveyNr = $paramPost['SurveyNr'];

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
        $this->resetSurveyField($MemberID,$SurveyNr);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('SurveyNr', $SurveyNr);
        $query = $this->db->update('ktv_survey_finance', $paramPost);

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

    public function deleteFinanceSurvey($MemberID,$SurveyNr){
        $this->db->trans_start();

        $sql="INSERT INTO ktv_survey_finance_nullified
            SELECT
                *
            FROM
                ktv_survey_finance a
            WHERE
                a.MemberID = ?
                AND a.SurveyNr = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $sql="DELETE FROM ktv_survey_finance WHERE MemberID = ? AND SurveyNr = ? LIMIT 1";
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