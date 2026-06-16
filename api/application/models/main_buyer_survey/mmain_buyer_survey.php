<?php
/**
 * @Author: nikolius
 * @Date:   2017-06-01 13:21:52
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmain_buyer_survey extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridMainBuyerSurveySummary($MemberID){
        $sql="SELECT
                a.`SurveyNr`
                , a.PlotNr
                , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
                , a.`DateCollection`
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM
                ktv_survey_main_buyer a
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
            ORDER BY a.`SurveyNr` ASC";
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
                ktv_survey_main_buyer a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
                AND a.`SurveyNr` = ?
                AND a.PlotNr = ?
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['SurveyNr'],
            $paramPost['PlotNr']
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        if($data['MemberID'] != ""){
            return true;
        }else{
            return false;
        }
    }

    public function getMainBuyerSurveyFormData($MemberID,$SurveyNr,$DateCollection,$PlotNr){
        $sql="SELECT
                a.`MemberID`,
                b.MemberUID,
                b.`MemberDisplayID`,
                kd.ProvinceID AS ProvinceID,
                b.`MemberName`,
                a.`SurveyNr`,
                a.PlotNr,
                a.`DateCollection`,
                #(SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedByLabel,
                /*CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS CreatedByLabel,*/
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS CreatedByLabel,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedByLabel,
                a.`BuyerType`,
                a.`BuyerName`,
                a.`DistanceToBuyer`,
                a.`TransportMode`,
                a.`FFBPriceLastSold`,
                a.`FFBLastSoldDate`,
                a.`isFFBPriceAfterReduce`,
                a.ToWhichMillSellFFBLastYear,
                a.ToWhichMillSellFFBLastYearText,
                a.`TransportationCost`,
                a.`OtherRelatedCost`,
                a.`PenaltyDeduction`,
                a.`HarvestingCost`,
                a.`ReceiptPhotoLastSoldFFB`,
                a.`SatisfiedCropPriceLastYear`,
                a.`ExpectRelationContinue`,
                a.`EstimatePercentHouseholdIncome`,
                a.`HowImportantCrop`,
                a.`OverallEcoToLastYear`,
                a.`Comment`
            FROM
                `ktv_survey_main_buyer` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_village kv ON kv.VillageID = b.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`MemberID` = ?
                AND a.`SurveyNr` = ?
                AND a.`DateCollection` = ?
                AND a.PlotNr = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            $SurveyNr,
            $DateCollection,
            $PlotNr
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        //yg diperlukan untuk proses lebih lanjut
        $dataRow['MemberDisplayID'] = $dataRow['Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberDisplayID'];
        $dataRow['MemberUID'] = $dataRow['Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberUID'];
        $dataRow['ProvinceID'] = $dataRow['Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ProvinceID'];
        $dataRow['ReceiptPhotoLastSoldFFB'] = $dataRow['Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFB'];

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertMainBuyerSurvey($paramPost,$MemberData){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $ReceiptPhotoLastSoldFFB = $paramPost['ReceiptPhotoLastSoldFFBOld'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ReceiptPhotoLastSoldFFBOld']);
        unset($paramPost['opsiDisplay']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;

        //insert
        $this->db->insert('ktv_survey_main_buyer', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($ReceiptPhotoLastSoldFFB != ""){
                //get ext nya..
                $arrTemp = explode(".", $ReceiptPhotoLastSoldFFB);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/main_buyer_last_receipt/'.$MemberData['ProvinceID'])){
                    mkdir('images/main_buyer_last_receipt/'.$MemberData['ProvinceID'], 0777, true);
                }
                if(!file_exists('images/main_buyer_last_receipt/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberUID'])){
                    mkdir('images/main_buyer_last_receipt/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberUID'], 0777, true);
                }

                $gambarTujuan = 'images/main_buyer_last_receipt/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberUID'].'/'.$namaFileGambar;
                if(rename('images/main_buyer_last_receipt/'.$ReceiptPhotoLastSoldFFB,$gambarTujuan)){
                    $sql="UPDATE ktv_survey_main_buyer a SET
                            a.`ReceiptPhotoLastSoldFFB` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }

            }

        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateMainBuyerSurvey($paramPost,$MemberData){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $MemberID = $paramPost['MemberID'];
        $SurveyNr = $paramPost['SurveyNr'];
        $PlotNr = $paramPost['PlotNr'];
        $DateCollection = $paramPost['DateCollection'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['MemberID']);
        unset($paramPost['SurveyNr']);
        unset($paramPost['PlotNr']);
        unset($paramPost['DateCollection']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        //photo
        if($paramPost['ReceiptPhotoLastSoldFFBOld'] != ""){
            $arrTemp = explode("/", $paramPost['ReceiptPhotoLastSoldFFBOld']);
            $ReceiptPhotoLastSoldFFB = array_values(array_slice($arrTemp, -1))[0];
            $paramPost['ReceiptPhotoLastSoldFFB'] = $ReceiptPhotoLastSoldFFB;
        }else{
            unset($paramPost['ReceiptPhotoLastSoldFFB']);
        }
        unset($paramPost['ReceiptPhotoLastSoldFFBOld']);

        //reset semua dulu
        $sql="UPDATE `ktv_survey_main_buyer` SET
                `BuyerType` = null,
                `BuyerName` = null,
                `DistanceToBuyer` = null,
                `TransportMode` = null,
                `FFBPriceLastSold` = null,
                `FFBLastSoldDate` = null,
                `isFFBPriceAfterReduce` = null,
                ToWhichMillSellFFBLastYear = null,
                ToWhichMillSellFFBLastYearText = null,
                `TransportationCost` = null,
                `OtherRelatedCost` = null,
                `PenaltyDeduction` = null,
                `HarvestingCost` = null,
                `SatisfiedCropPriceLastYear` = null,
                `ExpectRelationContinue` = null,
                `EstimatePercentHouseholdIncome` = null,
                `HowImportantCrop` = null,
                `OverallEcoToLastYear` = null,
                `Comment` = null
            WHERE
                `MemberID` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
                AND PlotNr = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $SurveyNr,
            $DateCollection,
            $PlotNr
        );
        $query = $this->db->query($sql,$p);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('SurveyNr', $SurveyNr);
        $this->db->where('DateCollection', $DateCollection);
        $this->db->where('PlotNr', $PlotNr);
        $query = $this->db->update('ktv_survey_main_buyer', $paramPost);

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

    public function deleteMainBuyerSurvey($MemberID,$SurveyNr,$PlotNr,$DateCollection){
        $this->db->trans_start();

        $sql="INSERT INTO ktv_survey_main_buyer_nullified
            SELECT
                *
            FROM
                ktv_survey_main_buyer a
            WHERE
                a.MemberID = ?
                AND a.PlotNr = ?
                AND a.SurveyNr = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $sql="DELETE FROM ktv_survey_main_buyer WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ? LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
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