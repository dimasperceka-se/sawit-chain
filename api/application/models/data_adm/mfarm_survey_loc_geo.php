<?php
/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : mfarm_survey_loc.php
 *******************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Mfarm_survey_loc_geo extends CI_Model {

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

                   , ST_Latitude(plot.LatLong) AS Latitude
                    , ST_Longitude(plot.LatLong) AS Longitude
                    , CONCAT(ST_Longitude(plot.`LatLong`),',',ST_Latitude(plot.`LatLong`)) AS CoordinateLabel
                FROM ktv_members f
                LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                LEFT JOIN ktv_survey_plot plot ON f.MemberID = plot.MemberID
                WHERE
                    ref.MRoleType = 'Farmer' AND f.StatusCode='active' AND plot.StatusCode = 'active' 
                    AND f.`MemberDisplayID` = ? 
                    AND ((ABS(plot.`Latitude`) > 0 AND ABS(plot.`Longitude`) > 0) OR (ABS(ST_Latitude(plot.LatLong)) > 0 AND ABS(ST_Longitude(plot.LatLong)) > 0 ))
                ORDER BY plot.`PlotNr`, plot.`SurveyNr`";


        $DataList = $this->db->query($sql,array($FarmerID))->result_array();

        if($DataList[0]['FarmerID'] != "") {
            for ($i=0; $i < count($DataList); $i++) { 
                $DataList[$i]['urutanIndex'] = $i;
            }
        }

        return $DataList;
    }

    public function GetGridPolygonM8($isTitikPolygon,$FarmerID)  {
        // ktv_survey_plot_polygon_geo
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
                LEFT JOIN ktv_survey_plot_polygon_geo poly ON poly.MemberID = f.MemberID
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
                        LEFT JOIN ktv_survey_plot_polygon_geo poly ON poly.MemberID = f.MemberID
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
                                LEFT JOIN ktv_survey_plot_polygon_geo poly ON poly.MemberID = f.MemberID
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
                                            poly.`StatusCheck` AS StatusPolygon
                                            , ST_ASTEXT(poly.Polygon) AS PolyText
                                        FROM
                                            ktv_members f
                                        LEFT JOIN ktv_member_role role ON role.MemberID = f.MemberID
                                        LEFT JOIN ktv_ref_member_role ref ON ref.MRoleID = role.MRoleID
                                        LEFT JOIN ktv_survey_plot_polygon_geo poly ON poly.MemberID = f.MemberID
                                        WHERE
                                            ref.MRoleType = 'Farmer' AND f.StatusCode='active'
                                            AND f.MemberDisplayID = ? 
                                            AND poly.`PlotNr` = ?
                                            AND poly.`SurveyNr` = ?
                                            AND poly.`Revision` = ?;";
                                $DataDetPolygon = $this->db->query($sql,array($FarmerID,$DataGarden[$i]['PlotNr'],$DataSurvey[$j]['SurveyNr'],$DataRevision[$k]['Revision']))->row_array();

                                //Detail Titik Polygon
                                $TitikPolygon = array();
                                $InsertJumlahTitik = 0;

                                if ($DataDetPolygon['PolyText'] != "") {
                                    $ProsesPoly = $DataDetPolygon['PolyText'];
                                    $ProsesPoly = substr($ProsesPoly, 9);
                                    $ProsesPoly = substr($ProsesPoly, 0, -2);

                                    if ($ProsesPoly != "") {
                                        $increPoly = 0;
                                        $ArrPoly = explode(",", $ProsesPoly);
                                        $InsertJumlahTitik = count($ArrPoly);
                                        if ($isTitikPolygon == 'Yes') {
                                            for ($l = 0; $l < count($ArrPoly); $l++) {
                                                $ArrLatLong = explode(' ', $ArrPoly[$l]);

                                                $DataPoly[$increPoly]['lat'] = (float) $ArrLatLong[0];
                                                $DataPoly[$increPoly]['lng'] = (float) $ArrLatLong[1];
                                                $increPoly++;
                                            }
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
                                $DataList[$increList]['TitikPolygon'] = $DataPoly;

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



}