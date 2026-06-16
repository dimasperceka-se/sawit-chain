<?php
/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : mpolygonover.php
 *******************************************/

defined('BASEPATH') OR exit('No direct script access allowed');

class Mpolygonover extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetMainGrid($start, $limit, $sortingField, $sortingDir) {
        $result = array();
        if ($sortingField == "") $sortingField = "a.DateCreated";
        if ($sortingDir == "") $sortingDir = ' DESC';

        $sql = "SELECT
                    m.MemberID
                    , m.MemberDisplayID
                    , m.MemberName
                    , a.PlotNr
                    , a.Revision
                    , a.StatusCheck
                    , l.`Function`
                    , l.FunctionDescription
                    , l.OGR_FID
                    , l.FunctionCode
                    , a.DateCreated
                FROM
                    `ktv_survey_plot_polygon_geo_temp` a
                LEFT JOIN 
                    ktv_members m on m.MemberID = a.MemberID
                LEFT JOIN
                    ktv_landuse_idn_new l on l.OGR_FID = a.OGR_FID    
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";
        $p = array(
            $start, $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT
                m.MemberID
                , m.MemberDisplayID
                , m.MemberName
                , a.PlotNr
                , a.Revision
                , a.StatusCheck
                , l.`Function`
                , l.FunctionDescription
                , l.OGR_FID
                , l.FunctionCode
                , a.DateCreated
            FROM
                `ktv_survey_plot_polygon_geo_temp` a
            LEFT JOIN 
                ktv_members m on m.MemberID = a.MemberID
            LEFT JOIN
                ktv_landuse_idn_new l on l.OGR_FID = a.OGR_FID ');
        $result['total'] = $query->num_rows();

        return $result;
    }

    public function GetAddPolygonGrid($pSearch, $start, $limit, $sortingField, $sortingDir) {
        $result = array();
        if ($sortingField == "") $sortingField = 'DateCreated';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.`SupplierID`
                    , b.`SupplierDisplayID` AS ID
                    , b.`SupplierName` AS `Name`
                    , a.`FarmNr`
                    , a.`Revision`
                    , a.`StatusCheck`
                    , a.`DateCreated`
                FROM
                    `ktv_survey_farm_polygon_geo` a
                    INNER JOIN ktv_supplier b ON a.`SupplierID` = b.`SupplierID`

                    LEFT JOIN ktv_survey_farm_polygon_geo_overlap ov ON 1=1
                        AND a.`SupplierID` = ov.`SupplierID`
                        AND a.`FarmNr` = ov.`FarmNr`
                        AND a.`Revision` = ov.`Revision`
                        AND ov.`UserId` = ?
                WHERE 1=1
                    AND ov.`SupplierID` IS NULL
                    AND a.`StatusCheck` != 'nullified'
                    AND ( (b.`SupplierDisplayID` LIKE ?) OR (b.`SupplierName` LIKE ?) )
                    AND ( (a.`StatusCheck` = ?) OR ('' = ?) )
                LIMIT ?,?";
        $p = array(
            $pSearch['UserId'],
            '%'.$pSearch['TxtSearchLabel'].'%','%'.$pSearch['TxtSearchLabel'].'%',
            $pSearch['CmbStatusCheck'],$pSearch['CmbStatusCheck'],
            $start,$limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function GetPolygonCompareGrid($pSearch, $start, $limit, $sortingField, $sortingDir) {
        $return = array();

        //Proses Compare ========================= (Begin)
        $this->db->trans_start();

        //Delete dl datanya
        $sql = "DELETE FROM `ktv_survey_farm_polygon_geo_overlap_tmp` WHERE UserId = ?";
        $p = array(
            $pSearch['UserId']
        );
        $query = $this->db->query($sql,$p);

        $sql = "SELECT
                    a.UserId
                    , a.`SupplierID`
                    , b.SupplierDisplayID
                    , b.SupplierName
                    , a.`FarmNr`
                    , a.`Revision`
                    , ST_ASTEXT(a.Polygon) AS Polytext
                    , a.`StatusCheck`
                FROM
                    `ktv_survey_farm_polygon_geo_overlap` a
                    LEFT JOIN ktv_supplier b ON a.SupplierID = b.SupplierID
                WHERE 1=1
                    AND a.`UserId` = ?
                ORDER BY a.`SupplierID`, a.`FarmNr`, a.`Revision`";
        $p = array(
            $pSearch['UserId']
        );
        $DataListProses = $this->db->query($sql,$p)->result_array();
        //echo '<pre>'; print_r($DataListProses); exit;

        for ($i=0; $i < count($DataListProses); $i++) {

            $sql = "SELECT
                        a.`SupplierID`
                        , b.`SupplierDisplayID`
                        , b.`SupplierName`
                        , a.`FarmNr`
                        , a.`Revision`
                        , a.`StatusCheck`
                    FROM
                        `ktv_survey_farm_polygon_geo` a
                        LEFT JOIN ktv_supplier b ON a.`SupplierID` = b.`SupplierID`
                    WHERE 1=1
                        AND ST_Intersects(a.`Polygon`, ST_POLYFROMTEXT(?, 4326) )
                    ";
            $p = array(
                $DataListProses[$i]['Polytext']
            );
            $DataCompare = $this->db->query($sql,$p)->result_array();
            //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            //echo '<pre>'; print_r($DataCompare); exit;

            if($DataCompare[0]['SupplierID'] != "") {
                for ($j=0; $j < count($DataCompare); $j++) {
                    if(
                        $DataListProses[$i]['SupplierID'].$DataListProses[$i]['FarmNr'].$DataListProses[$i]['Revision'] != $DataCompare[$j]['SupplierID'].$DataCompare[$j]['FarmNr'].$DataCompare[$j]['Revision']
                    ) {
                        $sql = "INSERT INTO `ktv_survey_farm_polygon_geo_overlap_tmp` SET
                                `UserId` = ?,
                                SupplierID = ?,
                                `ID` = ?,
                                `Name` = ?,
                                `FarmNr` = ?,
                                `Revision` = ?,
                                `StatusCheck` = ?,
                                SupplierIDOver = ?,
                                `IDOver` = ?,
                                `NameOver` = ?,
                                `FarmNrOver` = ?,
                                `RevisionOver` = ?,
                                `StatusCheckOver` = ?,
                                `DateGenerated` = NOW(),
                                `GeneratedBy` = ?
                                ";
                        $p = array(
                            $pSearch['UserId'],
                            $DataListProses[$i]['SupplierID'],
                            $DataListProses[$i]['SupplierDisplayID'],
                            $DataListProses[$i]['SupplierName'],
                            $DataListProses[$i]['FarmNr'],
                            $DataListProses[$i]['Revision'],
                            $DataListProses[$i]['StatusCheck'],
                            $DataCompare[$j]['SupplierID'],
                            $DataCompare[$j]['SupplierDisplayID'],
                            $DataCompare[$j]['SupplierName'],
                            $DataCompare[$j]['FarmNr'],
                            $DataCompare[$j]['Revision'],
                            $DataCompare[$j]['StatusCheck'],
                            $_SESSION['userid']
                        );
                        $query = $this->db->query($sql,$p);
                    }
                }
            }

        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return['data'] = array();
            $return['total'] = 0;
            return $return;
        }
        else {
            $this->db->trans_commit();
        }
        //Proses Compare ========================= (End)

        $sql = "SELECT
                    a.`UserId`,
                    a.SupplierID,
                    a.`ID`,
                    a.`Name`,
                    a.`FarmNr`,
                    a.`Revision`,
                    a.`StatusCheck`,
                    a.SupplierIDOver,
                    a.`IDOver`,
                    a.`NameOver`,
                    a.`FarmNrOver`,
                    a.`RevisionOver`,
                    a.`StatusCheckOver`,
                    a.`DateGenerated`,
                    a.`GeneratedBy`
                FROM
                    `ktv_survey_farm_polygon_geo_overlap_tmp` a
                WHERE 1=1
                    AND a.`UserId` = ?
                LIMIT ?,?";
        $p = array(
            $pSearch['UserId'], $start, $limit
        );
        $query = $this->db->query($sql,$p);
        $return['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        return $return;
    }

    public function AddPolygon($Ids) {
        $return = array();
        $this->db->trans_start();

        for ($i=0; $i < count($Ids); $i++) {
            $ArrTmp = explode('@',$Ids[$i]);
            $SupplierID = $ArrTmp[0];
            $FarmNr = $ArrTmp[1];
            $Revision = $ArrTmp[2];

            $sql = "INSERT INTO `ktv_survey_farm_polygon_geo_overlap` (
                        `UserId`,
                        `SupplierID`,
                        `FarmNr`,
                        `Revision`,
                        `Polygon`,
                        `StatusCheck`,
                        `DateCreated`,
                        `CreatedBy`
                    )
                    SELECT
                        ?
                        , a.SupplierID
                        , a.FarmNr
                        , a.Revision
                        , a.Polygon
                        , a.StatusCheck
                        , NOW()
                        , ?
                    FROM
                        `ktv_survey_farm_polygon_geo` a
                    WHERE 1=1
                        AND a.SupplierID = ?
                        AND a.FarmNr = ?
                        AND a.Revision = ?
                    LIMIT 1";
            $p = array(
                $_SESSION['userid'],$_SESSION['userid'],
                $SupplierID,$FarmNr,$Revision
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang('Failed to update data');
        }
        else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang('Data saved');
        }
        return $return;
    }

    public function AddAllPolygon($TxtSearchLabel,$CmbStatusCheck) {
        $return = array();
        $this->db->trans_start();

        $sql = "SELECT
                    a.`SupplierID`
                    , b.`SupplierDisplayID` AS ID
                    , b.`SupplierName` AS `Name`
                    , a.`FarmNr`
                    , a.`Revision`
                    , a.`StatusCheck`
                    , a.`DateCreated`
                FROM
                    `ktv_survey_farm_polygon_geo` a
                    INNER JOIN ktv_supplier b ON a.`SupplierID` = b.`SupplierID`

                    LEFT JOIN ktv_survey_farm_polygon_geo_overlap ov ON 1=1
                        AND a.`SupplierID` = ov.`SupplierID`
                        AND a.`FarmNr` = ov.`FarmNr`
                        AND a.`Revision` = ov.`Revision`
                        AND ov.`UserId` = ?
                WHERE 1=1
                    AND ov.`SupplierID` IS NULL
                    AND a.`StatusCheck` != 'nullified'
                    AND ( (b.`SupplierDisplayID` LIKE ?) OR (b.`SupplierName` LIKE ?) )
                    AND ( (a.`StatusCheck` = ?) OR ('' = ?) )";
        $p = array(
            $_SESSION['userid'],
            '%'.$TxtSearchLabel.'%','%'.$TxtSearchLabel.'%',
            $CmbStatusCheck,$CmbStatusCheck
        );
        $query = $this->db->query($sql,$p);
        $DataList = $query->result_array();

        for ($i=0; $i < count($DataList); $i++) {
            $sql = "INSERT INTO `ktv_survey_farm_polygon_geo_overlap` (
                        `UserId`,
                        `SupplierID`,
                        `FarmNr`,
                        `Revision`,
                        `Polygon`,
                        `StatusCheck`,
                        `DateCreated`,
                        `CreatedBy`
                    )
                    SELECT
                        ?
                        , a.SupplierID
                        , a.FarmNr
                        , a.Revision
                        , a.Polygon
                        , a.StatusCheck
                        , NOW()
                        , ?
                    FROM
                        `ktv_survey_farm_polygon_geo` a
                    WHERE 1=1
                        AND a.SupplierID = ?
                        AND a.FarmNr = ?
                        AND a.Revision = ?
                    LIMIT 1";
            $p = array(
                $_SESSION['userid'],$_SESSION['userid'],
                $DataList[$i]['SupplierID'],$DataList[$i]['FarmNr'],$DataList[$i]['Revision']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang('Failed to update data');
        }
        else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang('Data saved');
        }
        return $return;
    }

    public function DeletePolygon($Ids) {
        $return = array();
        $this->db->trans_start();

        for ($i=0; $i < count($Ids); $i++) {
            $ArrTmp = explode('@',$Ids[$i]);
            $SupplierID = $ArrTmp[0];
            $FarmNr = $ArrTmp[1];
            $Revision = $ArrTmp[2];

            $sql = "DELETE FROM `ktv_survey_farm_polygon_geo_overlap` WHERE SupplierID = ?
                        AND FarmNr = ?
                        AND Revision = ?
                        AND UserId = ?
                    LIMIT 1";
            $p = array(
                $SupplierID,$FarmNr,$Revision,$_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang('Failed to delete data');
        }
        else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang('Data deleted');
        }
        return $return;
    }

    public function DeleteAllPolygon() {
        $return = array();

        $sql = "DELETE FROM ktv_survey_farm_polygon_geo_overlap WHERE UserId = ?";
        $p = array(
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);

        if($query) {
            $return['success'] = true;
            $return['message'] = lang('Data deleted');
        } else {
            $return['success'] = false;
            $return['message'] = lang('Failed to delete data');
        }

        return $return;
    }

    public function GetPolygonCompareGridExport() {
        $query = $this->db->query('SELECT
                m.MemberID
                , m.MemberDisplayID
                , m.MemberName
                , a.PlotNr
                , a.Revision
                , a.StatusCheck
                , l.`Function`
                , l.FunctionDescription
                , l.OGR_FID
                , l.FunctionCode
                , a.DateCreated
            FROM
                `ktv_survey_plot_polygon_geo_temp` a
            LEFT JOIN 
                ktv_members m on m.MemberID = a.MemberID
            LEFT JOIN
                ktv_landuse_idn_new l on l.OGR_FID = a.OGR_FID ');
        return $query->result_array();
    }

}