<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-24 10:31:25
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtrader_survey extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridTraderSurveySummary($MemberID){
        $sql="SELECT
                a.`BusinessNr`
                , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
                , a.`SurveyNr`
                , a.`DateCollection`
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
            FROM
                ktv_survey_trader a
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.`MemberID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.`BusinessNr`, a.`SurveyNr`";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['BusinessNr'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function checkIfSurveyExist($paramPost){
        $sql="SELECT
                a.`MemberID`
            FROM
                ktv_survey_trader a
            WHERE
                a.MemberID = ?
                AND a.`BusinessNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`StatusCode` = 'active'
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['BusinessNr'],
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

    public function getTraderSurveyFormData($MemberID,$BusinessNr,$SurveyNr,$DateCollection){
        $sql="SELECT
                a.`MemberID`,
                b.`MemberDisplayID`,
                b.`MemberName`,
                (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedByLabel,
                a.`BusinessNr`,
                a.`SurveyNr`,
                a.`DateCollection`,
                a.`BusinessName`,
                a.`DateEstablish`,
                a.`Address`,
                a.`LandlinePhone`,
                a.`CellPhone`,
                a.`Email`,
                a.`Latitude`,
                a.`Longitude`,
                a.`FulltimeTrader`,
                a.`StatusTrader`,
                a.`YearRunning`
            FROM
                `ktv_survey_trader` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
            WHERE
                a.`MemberID` = ?
                AND a.`BusinessNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`DateCollection` = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            (int) $BusinessNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertTraderSurvey($paramPost){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['opsiDisplay']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;

        //insert
        $this->db->insert('ktv_survey_trader', $paramPost);

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

    public function updateTraderSurvey($paramPost){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $MemberID = $paramPost['MemberID'];
        $BusinessNr = $paramPost['BusinessNr'];
        $SurveyNr = $paramPost['SurveyNr'];
        $DateCollection = $paramPost['DateCollection'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['MemberID']);
        unset($paramPost['BusinessNr']);
        unset($paramPost['SurveyNr']);
        unset($paramPost['DateCollection']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        //reset semuanya dulu
        $sql="UPDATE `ktv_survey_trader` SET
                `BusinessName` = NULL,
                `DateEstablish` = NULL,
                `Address` = NULL,
                `LandlinePhone` = NULL,
                `CellPhone` = NULL,
                `Email` = NULL,
                `Latitude` = NULL,
                `Longitude` = NULL,
                `FulltimeTrader` = NULL,
                `StatusTrader` = NULL,
                `YearRunning` = NULL
            WHERE
                `MemberID` = ?
                AND `BusinessNr` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $BusinessNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('BusinessNr', $BusinessNr);
        $this->db->where('SurveyNr', $SurveyNr);
        $this->db->where('DateCollection', $DateCollection);
        $query = $this->db->update('ktv_survey_trader', $paramPost);

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

    public function deleteTraderSurvey($MemberID,$BusinessNr,$SurveyNr,$DateCollection){
        $sql="UPDATE `ktv_survey_trader` SET
                StatusCode = 'nullified'
            WHERE
                `MemberID` = ?
                AND `BusinessNr` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $BusinessNr,
            $SurveyNr,
            $DateCollection
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

}
?>