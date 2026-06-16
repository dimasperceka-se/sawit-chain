<?php
/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 08-01-2020
 *  File : mfarm_survey_loc.php
 *******************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Mfarm_survey_loc extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetDetailFarmer($FarmerID) {
        $sql = "SELECT
                    f.`MemberDisplayID` FarmerID
                    , f.`MemberName` FarmerName
                    , f.`Gender`
                FROM
                    ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                WHERE
                    ref.MRoleType = 'Farmer' AND f.`MemberDisplayID` = ? AND f.StatusCode = 'active'
                LIMIT 1";
        return $this->db->query($sql,array($FarmerID))->row_array();
    }

    public function GetGridCoor($FarmerID) {
        $sql = "SELECT
                    f.`MemberDisplayID` FarmerID
                   , plot.`PlotNr` GardenNr
                   , plot.`SurveyNr`
                   , plot.Latitude
                   , plot.Longitude
                   , CONCAT(plot.`Longitude`,',',plot.`Latitude`) AS CoordinateLabel
                FROM ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                LEFT JOIN ktv_survey_plot plot ON f.MemberID = plot.MemberID
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active' AND plot.StatusCode = 'active' 
                    AND f.`MemberDisplayID` = ? AND ABS(plot.`Latitude`) > 0 AND ABS(plot.`Longitude`) > 0
                ORDER BY plot.`PlotNr`, plot.`SurveyNr`";
        $DataList = $this->db->query($sql,array($FarmerID))->result_array();

        if($DataList[0]['FarmerID'] != "") {
            for ($i=0; $i < count($DataList); $i++) { 
                $DataList[$i]['urutanIndex'] = $i;
            }
        }

        return $DataList;
    }

    public function GetGridPolygon($isTitikPolygon,$FarmerID) {
        //fields: ['FarmerID', 'GardenNr','SurveyNr','Revision','StatusPolygon','JumlahTitik','GardenInfo','UrutanIndex','ColorName','ColorCode'],
        $DataList = array();
        $increList = 0;

        //Data Warna
        $sql = "SELECT
                    a.`ColorID`
                    , a.`ColorName`
                    , a.`ColorCode`
                FROM
                    ref_color a
                ORDER BY a.`ColorID`";
        $DataWarna = $this->db->query($sql)->result_array();

        $sql = "SELECT DISTINCT poly.PlotNr
                FROM
                    ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                WHERE   
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active' 
                    AND f.MemberDisplayID = ?
                ORDER BY poly.PlotNr
                ";
        $DataGarden = $this->db->query($sql,array($FarmerID))->result_array();
        if(isset($DataGarden[0]['PlotNr'])) {
            for ($i=0; $i < count($DataGarden); $i++) { 
                $InsertGardenNr = $DataGarden[$i]['PlotNr'];

                //Survey
                $sql = "SELECT
                            DISTINCT poly.`SurveyNr`
                        FROM
                            ktv_members f
                        LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                        LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                        LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                        WHERE
                            ref.MRoleType = 'Farmer' AND f.StatusCode='active' 
                            AND f.MemberDisplayID = ? AND poly.`PlotNr` = ?
                        ORDER BY poly.SurveyNr
                        ";
                $DataSurvey = $this->db->query($sql,array($FarmerID,$DataGarden[$i]['PlotNr']))->result_array();
                if(isset($DataSurvey[0]['SurveyNr'])) {
                    for ($j=0; $j < count($DataSurvey); $j++) { 
                        $InsertSurveyNr = $DataSurvey[$j]['SurveyNr'];

                        //Revision
                        $sql = "SELECT
                                    DISTINCT poly.`Revision`
                                FROM
                                    ktv_members f
                                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                                LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                                WHERE
                                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                                    AND f.MemberDisplayID = ? 
                                    AND poly.`PlotNr` = ?
                                    AND poly.`SurveyNr` = ?
                                ORDER BY poly.Revision;";
                        $DataRevision = $this->db->query($sql,array($FarmerID,$DataGarden[$i]['PlotNr'],$DataSurvey[$j]['SurveyNr']))->result_array();
                        if(isset($DataRevision[0]['Revision'])) {
                            for ($k=0; $k < count($DataRevision); $k++) { 
                                $InsertRevision = $DataRevision[$k]['Revision'];

                                //Detail Polygon
                                $sql = "SELECT
                                            COUNT(poly.`OrderNr`) AS JumlahTitik
                                            , poly.`StatusCheck` AS StatusPolygon
                                        FROM
                                            ktv_members f
                                        LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                                        LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                                        LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                                        WHERE
                                            ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                                            AND f.MemberDisplayID = ? 
                                            AND poly.`PlotNr` = ?
                                            AND poly.`SurveyNr` = ?
                                            AND poly.`Revision` = ?;";
                                $DataDetPolygon = $this->db->query($sql,array($FarmerID,$DataGarden[$i]['PlotNr'],$DataSurvey[$j]['SurveyNr'],$DataRevision[$k]['Revision']))->row_array();
                                if(isset($DataDetPolygon['StatusPolygon'])) {
                                    $InsertStatusPolygon = $DataDetPolygon['StatusPolygon'];
                                    $InsertJumlahTitik = $DataDetPolygon['JumlahTitik'];
                                } else {
                                    $InsertStatusPolygon = '-';
                                    $InsertJumlahTitik = 0;
                                }

                                //Detail Titik Polygon
                                $TitikPolygon = array();
                                if($isTitikPolygon == 'Yes') {
                                    $sql = "SELECT
                                                poly.`latitude` Latitude
                                                , poly.`longitude` Longitude
                                            FROM
                                                ktv_members f
                                            LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                                            LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                                            LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                                            WHERE
                                                ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                                                AND f.`MemberDisplayID` = ?
                                                AND poly.`PlotNr` = ?
                                                AND poly.`SurveyNr` = ?
                                                AND poly.`Revision` = ?
                                            ORDER BY poly.`OrderNr`;";
                                    $DataTitikPolygon = $this->db->query($sql,array($FarmerID,$DataGarden[$i]['PlotNr'],$DataSurvey[$j]['SurveyNr'],$DataRevision[$k]['Revision']))->result_array();
                                    if(isset($DataTitikPolygon[0]['Latitude'])) {
                                        for ($l=0; $l < count($DataTitikPolygon); $l++) { 
                                            $TitikPolygon[$l]['lat'] = (float) $DataTitikPolygon[$l]['Latitude'];
                                            $TitikPolygon[$l]['lng'] = (float) $DataTitikPolygon[$l]['Longitude'];
                                        }
                                    }
                                }

                                //Insert kan data list disini
                                $DataList[$increList]['GardenInfo'] = '';
                                $DataList[$increList]['FarmerID'] = $FarmerID;
                                $DataList[$increList]['GardenNr'] = $InsertGardenNr;

                                //Warna
                                $DataList[$increList]['UrutanIndex'] = $increList;
                                if($DataWarna[$increList]['ColorName'] != "") {
                                    $DataList[$increList]['ColorName'] = $DataWarna[$increList]['ColorName'];
                                } else {
                                    $DataList[$increList]['ColorName'] = "Magenta";
                                }
                                if($DataWarna[$increList]['ColorCode'] != "") {
                                    $DataList[$increList]['ColorCode'] = $DataWarna[$increList]['ColorCode'];
                                } else {
                                    $DataList[$increList]['ColorCode'] = "#FF00FF";
                                }

                                $DataList[$increList]['SurveyNr'] = $InsertSurveyNr;
                                $DataList[$increList]['Revision'] = $InsertRevision;

                                $DataList[$increList]['StatusPolygon'] = $InsertStatusPolygon;
                                $DataList[$increList]['JumlahTitik'] = $InsertJumlahTitik;
                                $DataList[$increList]['TitikPolygon'] = $TitikPolygon;

                                //incre data list
                                $increList++;
                            }
                        }
                    }
                }
            }
        }

        return $DataList;
    }

    public function UpdateCoorFormData($FarmerID,$GardenNr,$SurveyNr) {
        $return = array();

        $sql = "SELECT
                    plot.`Latitude` AS \"Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-Latitude\"
                    , plot.`Longitude` AS \"Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-Longitude\"
                FROM
                    ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                LEFT JOIN ktv_survey_plot plot ON plot.MemberID = f.MemberID
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                    AND f.`MemberDisplayID` = ?
                    AND plot.`PlotNr` = ?
                    AND plot.`SurveyNr` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($FarmerID,$GardenNr,$SurveyNr))->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function UpdateCoor($ParamPost) {
        $this->db->trans_begin();
        $proses = array();

        $sql = "UPDATE ktv_survey_plot a 
                LEFT JOIN ktv_members f ON f.MemberID = a.MemberID
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                SET
                    a.`Latitude` = ?
                    , a.`Longitude` = ?
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                    AND f.`MemberDisplayID` = ?
                    AND a.`PlotNr` = ?
                    AND a.`SurveyNr` = ?
                LIMIT 1";
        $p = array(
            (float) $ParamPost['Latitude'],
            (float) $ParamPost['Longitude'],
            $ParamPost['FarmerID'],
            $ParamPost['GardenNr'],
            $ParamPost['SurveyNr']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $proses['success'] = false;
            $proses['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $proses['success'] = true;
            $proses['message'] = "Data updated";
        }
        return $proses;
    }

    public function UpdatePolyFormData($FarmerID,$GardenNr,$SurveyNr,$Revision) {
        $return = array();

        $sql = "SELECT
                    DISTINCT poly.StatusCheck AS \"Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateStatusPoly-Form-StatusPolygon\"
                FROM
                    ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                    AND f.`MemberDisplayID` = ?
                    AND poly.`PlotNr` = ?
                    AND poly.`SurveyNr` = ?
                    AND poly.`Revision` = ?";
        $data = $this->db->query($sql,array($FarmerID,$GardenNr,$SurveyNr,$Revision))->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function UpdatePolyStatus($ParamPost) {
        $this->db->trans_begin();
        $proses = array();

        $sql = "UPDATE ktv_survey_plot_polygon a 
                LEFT JOIN ktv_members f ON f.MemberID = a.MemberID
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                SET
                    a.`StatusCheck` = ?
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                    AND f.`MemberDisplayID` = ?
                    AND a.`PlotNr` = ?
                    AND a.`SurveyNr` = ?
                    AND a.`Revision` = ?";
        $p = array(
            $ParamPost['StatusPolygon'],
            $ParamPost['FarmerID'],
            $ParamPost['GardenNr'],
            $ParamPost['SurveyNr'],
            $ParamPost['Revision']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $proses['success'] = false;
            $proses['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $proses['success'] = true;
            $proses['message'] = "Data updated";
        }
        return $proses;
    }

    public function DeletePolygon($FarmerID,$GardenNr,$SurveyNr,$Revision) {
        $this->db->trans_begin();
        $proses = array();

        $sql = "INSERT INTO `his_ktv_survey_plot_polygon` (
                    `HisID`,
                    `DateHistory`,
                    `DeleteBy`,
                    `MemberID`,
                    `PlotNr`,
                    `SurveyNr`,
                    `OrderNr`,
                    `Latitude`,
                    `Longitude`,
                    `Altitude`,
                    `Accuracy`,
                    `DateCreated`,
                    `CreatedBy`
                )
                SELECT
                    NULL,
                    NOW(),
                    ?,
                    f.`MemberID`,
                    poly.`PlotNr`,
                    poly.`SurveyNr`,
                    poly.`OrderNr`,
                    poly.`Latitude`,
                    poly.`Longitude`,
                    poly.`Altitude`,
                    poly.`Accuracy`,
                    NOW(),
                    ?
                FROM
                    ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                LEFT JOIN ktv_survey_plot_polygon poly ON poly.MemberID = f.MemberID
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                    AND f.`MemberDisplayID` = ?
                    AND poly.`PlotNr` = ?
                    AND poly.`SurveyNr` = ?
                    AND poly.`Revision` = ?";
        $p = array(
            $_SESSION['userid'],$_SESSION['userid'],
            $FarmerID,$GardenNr,$SurveyNr,$Revision
        );
        $query = $this->db->query($sql,$p);

        $sql = "DELETE poly FROM ktv_survey_plot_polygon poly
                LEFT JOIN ktv_members f ON f.MemberID = poly.MemberID
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                WHERE 
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                    AND f.`MemberDisplayID` = ?
                    AND `PlotNr` = ?
                    AND `SurveyNr` = ?
                    AND `Revision` = ?";
        $query = $this->db->query($sql,array($FarmerID,$GardenNr,$SurveyNr,$Revision));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $proses['success'] = false;
            $proses['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $proses['success'] = true;
            $proses['message'] = "Data updated";
        }
        return $proses;
    }

}