<?php

class Mgarden extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function ImportGardenGrid($KeySearch, $start, $limit) {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    MemberID
                    , PlotNr
                    , SurveyNr
                    , Latitude
                    , Longitude
                    , GardenAreaHa
                FROM
                    ktv_garden_upload_temp
                WHERE
                    1=1 AND (MemberID LIKE ? OR PlotNr LIKE ? OR SurveyNr LIKE ? OR Latitude LIKE ? OR Longitude LIKE ? OR GardenAreaHa LIKE ?)
                ORDER BY MemberID ASC";
        $p = array(
            '%' . $KeySearch . '%',
            '%' . $KeySearch . '%',
            '%' . $KeySearch . '%',
            '%' . $KeySearch . '%',
            '%' . $KeySearch . '%',
            '%' . $KeySearch . '%',
            intval($start),
            intval($limit)
        );
        $query = $this->db->query($sql, $p);
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);

        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            $DataList = $query->result_array();

            return array(
                'data' => $DataList,
                'total' => $total['total'],
            );
        } else {
            return array(
                'data' => array(),
                'total' => 0
            );
        }
    }

    public function InsertGardenTmp($DataInsert) {
        $this->db->trans_begin();

        $allDataValid = true;
        //Delete dl datanya
        $sql = "DELETE FROM `ktv_garden_upload_temp`";
        $query = $this->db->query($sql);
        foreach ($DataInsert as $i => $value) {
            $sql = "INSERT INTO ktv_garden_upload_temp SET
                        MemberID = ?
                        , PlotNr = ?
                        , SurveyNr = ?
                        , Latitude = ?
                        , Longitude = ?
                        , GardenAreaHa = ?
                        , DateCreated = NOW()
                        , CreatedBy = ?
                    ";
            $p = array(
                $DataInsert[$i]['MemberID'],
                $DataInsert[$i]['PlotNr'],
                $DataInsert[$i]['SurveyNr'],
                $DataInsert[$i]['Latitude'],
                $DataInsert[$i]['Longitude'],
                $DataInsert[$i]['GardenAreaHa'],
                $_SESSION['userid']
            );

            $sqlCekDataExist = "SELECT MemberID FROM ktv_garden_upload_temp
                                WHERE
                                    1=1
                                    AND MemberID = ?
                                    AND PlotNr = ?
                                    AND SurveyNr = ?
                                UNION
                                SELECT MemberID FROM ktv_survey_plot
                                WHERE
                                    1=1
                                    AND MemberID = ?
                                    AND PlotNr = ?
                                    AND SurveyNr = ?";
            $queryCekDataExist = $this->db->query($sqlCekDataExist, array($DataInsert[$i]['MemberID'], 
                                                                            $DataInsert[$i]['PlotNr'], 
                                                                            $DataInsert[$i]['SurveyNr'], 
                                                                            $DataInsert[$i]['MemberID'], 
                                                                            $DataInsert[$i]['PlotNr'], 
                                                                            $DataInsert[$i]['SurveyNr'])
                                );
            if ($queryCekDataExist->num_rows() >= 1) {
                $allDataValid = false;
                break;
            } else {
                $query = $this->db->query($sql, $p);
            }
        }

        if (!$allDataValid) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Upload data gagal, pastikan kombinasi MemberID, PlotNr, dan SurveyNr tidak ada yang duplikat";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Upload data sukses";
        }

        return $results;
    }

    public function InsertGarden(){
        $this->db->trans_start();

        $this->db->from('ktv_garden_upload_temp');
        $garden = $this->db->get()->result_array();

        foreach ($garden as $key => $value) {
            $this->db->insert('ktv_survey_plot', $value);
            
            // Plot Status ==================================================== (Begin)
            $sql = "INSERT INTO `ktv_survey_plot_status` (
                `MemberID`,
                `PlotNr`,
                `ActiveStatus`,
                Remark,
                `DateCreated`,
                `CreatedBy`
            )
            SELECT
                t_gar.MemberID
                , t_gar.PlotNr
                , '1'
                , 'Insert dari script penyesuaian garden status'
                , NOW()
                , '1'
            FROM (
                SELECT
                    gar.`MemberID`
                    , gar.`PlotNr`
                FROM
                    ktv_survey_plot gar
                WHERE
                    gar.`MemberID` != '0'
                    AND gar.`PlotNr` != '0'
                GROUP BY gar.`MemberID`, gar.`PlotNr`
            ) AS t_gar
            LEFT JOIN (
                SELECT
                    gstat.`MemberID`
                    , gstat.`PlotNr`
                    , gstat.`ActiveStatus`
                FROM
                    `ktv_survey_plot_status` gstat
                WHERE
                    gstat.`MemberID` != '0'
                    AND gstat.`PlotNr` != '0'
            ) AS t_garstat ON 1=1
                AND t_gar.MemberID = t_garstat.MemberID
                AND t_gar.PlotNr = t_garstat.PlotNr
            WHERE
                t_garstat.MemberID IS NULL
                AND t_gar.MemberID = ?
                AND t_gar.PlotNr = ?
            ";
            $query = $this->db->query($sql, array($value['MemberID'],$value['PlotNr']));

            $sql = "UPDATE ktv_survey_plot_status tup
                    INNER JOIN (
                        SELECT
                            sgar.`MemberID`
                            , sgar.`PlotNr`
                            , sgar.`SurveyNr`
                            , sgar.`GardenAreaHa`
                            , sgar.`AnnualProduction`
                        FROM
                            `ktv_survey_plot` sgar
                            INNER JOIN (
                                SELECT
                                    lat_sgar.`MemberID`
                                    , lat_sgar.`PlotNr`
                                    , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                                FROM
                                    ktv_survey_plot lat_sgar
                                GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                            ) AS sgar_lat ON
                                sgar.MemberID = sgar_lat.MemberID
                                AND sgar.`PlotNr` = sgar_lat.PlotNr
                                AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                        WHERE 1=1
                            AND sgar.MemberID = ?
                            AND sgar.PlotNr = ?
                        GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                    ) AS gar_lat ON 1=1
                        AND tup.`MemberID` = gar_lat.MemberID
                        AND tup.`PlotNr` = gar_lat.PlotNr
                    SET
                        tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                        , tup.`AnnualProduction` = gar_lat.AnnualProduction";
            $query = $this->db->query($sql, array($value['MemberID'],$value['PlotNr']));
            
            $sql = "UPDATE ktv_survey_plot_status tup
                    INNER JOIN (
                        SELECT
                            sgar.`MemberID`
                            , sgar.`PlotNr`
                            , sgar.`SurveyNr`
                            , sgar.`Latitude`
                            , sgar.`Longitude`
                        FROM
                            `ktv_survey_plot` sgar
                            INNER JOIN (
                                SELECT
                                    lat_sgar.`MemberID`
                                    , lat_sgar.`PlotNr`
                                    , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                                FROM
                                    ktv_survey_plot lat_sgar
                                GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                            ) AS sgar_lat ON
                                sgar.MemberID = sgar_lat.MemberID
                                AND sgar.`PlotNr` = sgar_lat.PlotNr
                                AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                        WHERE 1=1
                            AND sgar.`Latitude` IS NOT NULL
                            AND sgar.`Latitude` != ''
                            AND sgar.`Latitude` != '0'
                            AND sgar.`Longitude` IS NOT NULL
                            AND sgar.`Longitude` != ''
                            AND sgar.`Longitude` != '0'
                            AND sgar.MemberID = ?
                            AND sgar.PlotNr = ?
                        GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                    ) AS gar_lat ON 1=1
                        AND tup.`MemberID` = gar_lat.MemberID
                        AND tup.`PlotNr` = gar_lat.PlotNr
                    SET
                        tup.`Latitude` = gar_lat.Latitude
                        , tup.`Longitude` = gar_lat.Longitude";
            $query = $this->db->query($sql, array($value['MemberID'], $value['PlotNr']));
            //Plot Status ==================================================== (End)
            
            // Untuk param dhis
            $dhisParam[$key] = $value;
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['mid'] = $dhisParam;
            $results['message'] = 'Import data berhasil';
        } else {
            $results['success'] = false;
            $results['message'] = 'Import data gagal, pastikan MemberID, PlotNr, dan SurveyNr belum ada pada sistem';
        }

        return $results;
    }

    public function cek_query(){
        $this->db->select('MemberID, PlotNr, SurveyNr, Latitude as lati, Longitude as longi, GardenAreaHa, DateCreated, CreatedBy');
        $this->db->from('ktv_garden_upload_temp');
        $garden = $this->db->get()->result_array();

        return $garden;
    }

}