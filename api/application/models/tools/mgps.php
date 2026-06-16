<?php

class Mgps extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function readDatas($userId) {
        $sql = "
            SELECT t.*
              -- ,IF (g.`StatusGPS` != 'verified'
              --   OR g.`Latitude` IS NULL
              --   OR g.`Longitude` IS NULL
              --   OR g.`Latitude` = 0.000000
              --   OR g.`Longitude` = 0.000000
              --   OR g.`Latitude` = -1000.000000
              --   OR g.`Longitude` = -1000.000000
              --   ,0,1) AS error
            FROM ktv_farmer_gps_temp t
            LEFT JOIN `ktv_farmer_garden` g ON g.`FarmerID` = t.`FarmerID` AND g.`GardenNr` = t.`GardenNr` AND g.`SurveyNr` = t.`SurveyNr`
            WHERE t.CreatedBy=?
            ORDER BY FarmerName
            ";
        $query = $this->db->query($sql, array($userId));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $result['data'] = $query->result_array();
        return $result;
    }

    function injectData($data, $userId) {
        /* tools*
          $sql = "
          insert into ktv_certification_temp (FarmerID, GardenNr, Certification, CandidateSelection, FirstYear,
          SecondYear, ThirdYear, FourthYear, DateCreated, DateUpdated, CreatedBy, LastModifiedBy)
          values (?,?,?,?,?,   ?,?,?,now(),now(),?,?)";
          for ($i=2;$i<sizeof($data)-2;$i++) {
          $this->db->query($sql, array($data[$i][1],$data[$i][2],$data[$i][3],$data[$i][4].' 00:00:00',$data[$i][5].' 00:00:00',
          null,null,null,$userId,$userId));
          }
          return;
          // */
        $field = implode(',', $data[4]);
        for ($i = 1; $i < sizeof($data[4]) + 1; $i++) {
            $isian[$i] = '?';
        }
        $isi = implode(',', $isian);
        $sql = "insert into ktv_farmer_gps_temp ($field,CreatedBy) values ($isi,$userId)";
        /* $sql = "
          insert into ktv_farmer_gps_temp (CPGid,FarmerID,GardenNr,FarmerName,Longitude,Latitude,CreatedBy)
          values (?,?,?,?,?,?,$userId)"; */
        $result = true;
        $result = $result && $this->db->query("DELETE FROM ktv_farmer_gps_temp WHERE CreatedBy=$userId");
        for ($i = 5; $i < sizeof($data) + 10; $i++) {
            if ($data[$i][1] != '') {
                for ($j = 1; $j < sizeof($data[4]) + 1; $j++) {
                    $value[$j] = $data[$i][$j];
                }
                $value[] = $userId;
                $result = $result && $this->db->query($sql, $value);
            }
        }
        return $result;
    }

    public function deleteUpload($UserId) {
        return $this->db->query("DELETE FROM ktv_farmer_gps_temp WHERE CreatedBy=?", array($UserId));
    }

    public function processUpload($data, $userId) {
        $status = false;
        $message = '';
        $allowed = array(
            'FarmerID',
            'GardenNr',
            'SurveyNr',
            'FarmerName',
            'Longitude',
            'Latitude',
            'StatusGPS',
            'LandUse',
        );
        $excluded_key = array();
        $fields = $data[0];
        foreach ($fields as $key => $value) {
            $fields[$key] = trim($value);
        }
        unset($data[0]);
        if ($fields === $allowed) {
            $this->deleteUpload($userId);
            $sql = "INSERT INTO ktv_farmer_gps_temp (`" . implode('`,`', $fields) . "`, `CreatedBy`) VALUES ";
            // return $sql;
            $values = array();
            foreach ($data as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $excluded_key)) {
                        unset($value[$k]);
                    } else {
                        $value[$k] = mysql_real_escape_string($v);
                    }
                }
                $values[] = "
            ('" . implode("','", $value) . "'," . $userId . ")";
            }
            $sql .= implode(', ', $values);
            $result = $this->db->query($sql);
            if ($result) {
                $status = true;
            } else {
                $message = 'Failed to insert data';
            }
        } else {
            $message = 'Wrong file! Please upload correct file!';
        }
        return array(
            'success' => $status,
            'msg' => $message,
        );
    }

    /* function updateData($userId) {
      $sql_get = "SELECT * FROM ktv_farmer_gps_temp WHERE CreatedBy=?";
      $sql_update = "UPDATE ktv_farmer_garden SET Longitude=?,Latitude=?,DateUpdated=now()
      WHERE FarmerID=? and GardenNr=? and SurveyNr=?";
      $this->db->trans_start();
      $query = $this->db->query($sql_get, array($userId));
      $data = $query->result_array();
      $stat = true;
      for ($i = 0; $i < sizeof($data); $i++) {
      if ($stat)
      $this->db->query($sql_update, array($data[$i]['Longitude'], $data[$i]['Latitude'], $data[$i]['FarmerID'],
      $data[$i]['GardenNr'], $data[$i]['SurveyNr']));
      else
      break;
      }
      if ($stat)
      $this->db->query("DELETE FROM ktv_farmer_gps_temp WHERE CreatedBy=$userId");
      $this->db->trans_complete();
      return $stat;
      } */

    public function updateData($userId) {
        $sql = "
UPDATE
  ktv_farmer_garden g
JOIN ktv_farmer_gps_temp t ON g.`FarmerID` = t.`FarmerID` AND g.`GardenNr` = t.`GardenNr` AND g.`SurveyNr` = t.`SurveyNr`
SET
  g.`Latitude` = t.`Latitude`,
  g.`Longitude` = t.`Longitude`,
  g.`StatusGPS` = t.`StatusGPS`,
  g.`GardenLandUse` = t.`LandUse`
WHERE
  1 = 1
  -- AND ( g.`StatusGPS` != 'verified'
  -- OR g.`Latitude` IS NULL
  -- OR g.`Longitude` IS NULL
  -- OR g.`Latitude` = 0.000000
  -- OR g.`Longitude` = 0.000000
  -- OR g.`Latitude` = -1000.000000
  -- OR g.`Longitude` = -1000.000000 )
  AND t.`CreatedBy` = ?
      ";
        return $this->db->query($sql, array($userId));
    }

    public function importKMLtmp($file) {
        $this->load->helper('file');
        $kml = read_file($file);

        if (!empty($kml)) {
            $this->db->query("DELETE FROM ktv_survey_plot_polygon_temp WHERE CreatedBy = {$_SESSION['userid']}");
            $this->load->model('geospatial/mmaps');
            $query = "INSERT INTO `ktv_survey_plot_polygon_temp` (
    `MemberID`
    , `PlotNr`
    , `SurveyNr`
    , `OrderNr`
    , `Revision`
    , `latitude`
    , `longitude`
    , `altitude`
    , `CenterLatitude`
    , `CenterLongitude`
    , `StatusCheck`
    , `StatusCode`
    , `DateCreated`
    , `CreatedBy`
    , `GardenAreaPolygon`
)
VALUES
";
            $places_xml = simplexml_load_string($kml);
            $errors = array();
            $success = array();
            if ($places_xml) {
                foreach ($places_xml->Document->Folder->Placemark as $key => $value) {
                    $coordinates = $value
                            // ->MultiGeometry
                            ->Polygon
                            ->outerBoundaryIs
                            ->LinearRing
                            ->coordinates;

                    if (!empty($coordinates)) {
                        $MemberDisplayID_key = null;
                        $PlotNr_key = null;
                        $SurveyNr_key = null;
                        $CenterLatitude_key = null;
                        $CenterLongitude_key = null;
                        $StatusCheck_key = null;
                        $PolygonHa_key = null;
                        for ($i = 0; $i < count($value->ExtendedData->SchemaData->SimpleData); $i++) {
                            $v = reset($value->ExtendedData->SchemaData->SimpleData[$i]);
                            if (strtoupper($v['name']) == 'MEMBERID') {
                                $MemberDisplayID_key = $i;
                            }
                            if (strtoupper($v['name']) == 'PLOTNR') {
                                $PlotNr_key = $i;
                            }
                            if (strtoupper($v['name']) == 'SURVEYNR') {
                                $SurveyNr_key = $i;
                            }
                            if (strtoupper($v['name']) == 'CTR_LAT') {
                                $CenterLatitude_key = $i;
                            }
                            if (strtoupper($v['name']) == 'CTR_LONG') {
                                $CenterLongitude_key = $i;
                            }
                            if (strtoupper($v['name']) == 'STAT_POLYG') {
                                $StatusCheck_key = $i;
                            }
                            if (strtoupper($v['name']) == 'POLYGON_HA') {
                                $PolygonHa_key = $i;
                            }
                        }
                        $MemberDisplayID    = $value->ExtendedData->SchemaData->SimpleData[$MemberDisplayID_key];
                        $MemberID           = $this->mmaps->getMemberID($MemberDisplayID);
                        if ($MemberID !== false) {
                            $PlotNr             = intval($value->ExtendedData->SchemaData->SimpleData[$PlotNr_key]);
                            $SurveyNr           = intval($value->ExtendedData->SchemaData->SimpleData[$SurveyNr_key]);
                            $CenterLatitude     = floatval($value->ExtendedData->SchemaData->SimpleData[$CenterLatitude_key]);
                            $CenterLongitude    = floatval($value->ExtendedData->SchemaData->SimpleData[$CenterLongitude_key]);
                            $StatusCheck        = $value->ExtendedData->SchemaData->SimpleData[$StatusCheck_key];
                            $PolygonHa          = floatval($value->ExtendedData->SchemaData->SimpleData[$PolygonHa_key]);

                            $lastRevision   = $this->mmaps->checkLastRevision($MemberID, $PlotNr, $SurveyNr);
                            $revision       = $lastRevision + 1;
                            $coordinates    = explode(' ', $coordinates);
                            $order          = 1;
                            // $this->db->trans_start(TRUE);
                            $sql = $query;
                            $values = array();
                            foreach ($coordinates as $coord) {
                                $coord = trim($coord);
                                if (!empty($coord)) {
                                    $latlng = explode(',', $coord);
                                    $value = "({$MemberID}, {$PlotNr}, {$SurveyNr}, {$order}, {$revision}, {$latlng[1]}, {$latlng[0]}, {$latlng[2]}, {$CenterLatitude}, {$CenterLongitude}, '{$StatusCheck}', 'active', NOW(), {$_SESSION['userid']}, {$PolygonHa})\n";
                                    $values[] = $value;
                                    $order++;
                                }
                            }
                            $sql .= implode(', ', $values);
                            // echo '<pre>'; print_r($sql); echo '</pre>'; exit;
                            $result = $this->db->query($sql);
                            if ($result) {
                                $this->updateGardenAreaPolygon($MemberID, $PlotNr, $SurveyNr, $PolygonHa);
                            }
                            // $this->area_calc($MemberID,$PlotNr,$SurveyNr);
                            // $success[] = array('FarmerID' => $MemberID, 'GardenNr' => $PlotNr, 'SurveyNr' => $SurveyNr);
                        }
                    }
                }
            }
        }
    }

    public function updateGardenAreaPolygon($MemberID, $PlotNr, $SurveyNr, $PolygonHa)
    {
        $this->db->update('ktv_survey_plot', array('GardenAreaPolygon' => $PolygonHa), compact('MemberID', 'PlotNr', 'SurveyNr'));
    }

    public function getKMLFarmers($start = 0, $limit = 10) {
        $sql = "
SELECT SQL_CALC_FOUND_ROWS
    f.MemberID
    , f.MemberDisplayID
    , f.MemberName
    , ga.PlotNr
    , ga.SurveyNr
    , ga.Revision
FROM ktv_survey_plot_polygon_temp ga
JOIN ktv_survey_plot g ON g.MemberID = ga.MemberID AND g.PlotNr = ga.PlotNr
JOIN ktv_members f ON f.MemberID = ga.MemberID
WHERE
    ga.CreatedBy = ?
GROUP BY f.MemberID
LIMIT ?,?
        ";
        $query = $this->db->query($sql, array($_SESSION['userid'], intval($start), intval($limit)));
        if ($query->num_rows() > 0) {
            $return['data'] = $query->result_array();
            $query = $this->db->query("SELECT FOUND_ROWS() as total");
            $return['total'] = $query->row_array(0)['total'];
            return $return;
        }
        return false;
    }

    public function updateKML() {
        $this->db->trans_start(FALSE);
        $sql = "
INSERT INTO ktv_survey_plot_polygon (
    `MemberID`
    , `PlotNr`
    , `SurveyNr`
    , `OrderNr`
    , `Revision`
    , `latitude`
    , `longitude`
    , `altitude`
    , `CenterLatitude`
    , `CenterLongitude`
    , `StatusCheck`
    , `StatusCode`
    , `DateCreated`
    , `CreatedBy`
)
SELECT 
    `MemberID`
    , `PlotNr`
    , `SurveyNr`
    , `OrderNr`
    , `Revision`
    , `latitude`
    , `longitude`
    , `altitude`
    , `CenterLatitude`
    , `CenterLongitude`
    , `StatusCheck`
    , `StatusCode`
    , `DateCreated`
    , `CreatedBy`
FROM ktv_survey_plot_polygon_temp WHERE CreatedBy = ?
        ";
        $query = $this->db->query($sql, array($_SESSION['userid']));
        $query = $this->db->query('SELECT * FROM ktv_survey_plot_polygon_temp WHERE CreatedBy = ? GROUP BY MemberID, PlotNr', array($_SESSION['userid']));
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $key => $value) {
                // $this->db->query($sql, $value);
                $area = $value['GardenAreaPolygon'];
                $this->db->query("UPDATE ktv_survey_plot SET GardenAreaPolygon = ?, PolygonRevision = ?, Latitude = ?, Longitude = ? WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ?", array($area, $value['Revision'], $value['CenterLatitude'], $value['CenterLongitude'], $value['MemberID'], $value['PlotNr'], $value['SurveyNr']));
            }
        }
        $sql = "
DELETE FROM ktv_survey_plot_polygon_temp WHERE CreatedBy = ?
        ";
        $query = $this->db->query($sql, array($_SESSION['userid']));
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function checkGPSFarmerGarden($DistrictID) {
        //req class untuk perhitungan
        require_once APPPATH . 'third_party/pointLocation.php';
        $pointLocation = new pointLocation();

        $sql = "SELECT
                a.`Latitude`
                , a.`Longitude`
            FROM
                ktv_ref_polygon_district a
            WHERE
                a.`DistrictID` = '{$DistrictID}'
            ORDER BY a.`OrderNr` ASC";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        $districtPolygon = array();

        for ($i = 0; $i < count($data); $i++) {
            $districtPolygon[] = "{$data[$i]['Longitude']} {$data[$i]['Latitude']}";
        }

        //Cek Datanya
        $sql = "SELECT
                a.`MemberID`
                , b.`MemberDisplayID`
                , b.`MemberName`
                , a.`PlotNr`
                , a.`SurveyNr`
                , CONCAT(a.`Longitude`,' ',a.`Latitude`) AS GPSLocation
            FROM
                ktv_survey_plot a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
            WHERE
                a.`StatusCode` = 'active'
                AND b.`StatusCode` = 'active'
                AND SUBSTR(b.`VillageID`,1,4) = '{$DistrictID}'
                AND a.`Latitude` IS NOT NULL
                AND a.`Longitude` IS NOT NULL
            ";
        $query = $this->db->query($sql);
        $dataListFarmerGarden = $query->result_array();

        $outOfPos = array();
        $incre = 0;
        for ($i = 0; $i < count($dataListFarmerGarden); $i++) {
            $checkGPS = $pointLocation->pointInPolygon($dataListFarmerGarden[$i]['GPSLocation'], $districtPolygon);
            if ($checkGPS == "outside") {
                $outOfPos[$incre] = $dataListFarmerGarden[$i];
                $incre++;
            }
        }

        return $outOfPos;
    }

    public function runCheckProtectedForest($alldata) {
        //req class untuk perhitungan
        require_once APPPATH . 'third_party/pointLocation.php';

        $restrictedPolygon = array();
        $pointLocation = new pointLocation();
        $sql = "SELECT 
                a.AreaID,
                a.RestrictedAreaName,
                a.Longitude,
                a.Latitude,
                a.OrderNr
              FROM
                ktv_ref_restricted_area_polygon a 
              GROUP BY a.AreaID";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        foreach ($data as $val) {
            $sql = "SELECT 
                    a.AreaID,
                    a.RestrictedAreaName,
                    a.Longitude,
                    a.Latitude,
                    a.OrderNr
                  FROM
                    ktv_ref_restricted_area_polygon a 
                  WHERE a.AreaID = ?";
            $query = $this->db->query($sql, array($val['AreaID']));
            $area = $query->result_array();
            foreach ($area as $k => $v) {
                $restrictedPolygon[$v['AreaID']][] = "{$v['Longitude']} {$v['Latitude']}";
            }
        }

        $dataListFarmerGarden = $this->getFarmerGardenCoordinates();
        //Cek Datanya

        $outOfPos = array();
        for ($i = 0; $i < count($dataListFarmerGarden); $i++) {
            foreach ($restrictedPolygon as $key => $val) {
                $checkGPS = $pointLocation->pointInPolygon($dataListFarmerGarden[$i]['GPSLocation'], $val);
                $status = 'outside';

                if ($checkGPS != "outside") {
                    $status = 'inside';
                    $this->insertRestrictedAreaStatus($dataListFarmerGarden[$i], $key, $status);
                    break;
                }

                if (!isset($restrictedPolygon[$key + 1])) {
                    $this->insertRestrictedAreaStatus($dataListFarmerGarden[$i], null, $status);
                }
            }
        }
        return $outOfPos;
    }

    public function getFarmerGardenCoordinates() {

        $sql = "SELECT 
                a.`MemberID`,
                b.`MemberDisplayID`,
                b.`MemberName`,
                a.`PlotNr`,
                a.`SurveyNr`,
                CONCAT(a.`Longitude`, ' ', a.`Latitude`) AS GPSLocation 
              FROM
                ktv_survey_plot a 
                LEFT JOIN ktv_members b 
                  ON a.`MemberID` = b.`MemberID` 
                LEFT JOIN ktv_restricted_area_coordinate c 
                  ON c.MemberID = a.MemberID 
                  AND a.PlotNr = c.PlotNr 
              WHERE a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND a.`Latitude` IS NOT NULL 
                AND a.`Longitude` IS NOT NULL 
                AND (
                  c.RestrictionAreaStatus IN ('inside', 'unchecked') 
                  OR c.RestrictionAreaStatus IS NULL
                )
            ";
        $query = $this->db->query($sql);
        $dataListFarmerGarden = $query->result_array();
        return $dataListFarmerGarden;
    }

    public function insertRestrictedAreaStatus($data, $areaid, $status) {
        $sql = "INSERT INTO ktv_restricted_area_coordinate (
                MemberID,
                PlotNr,
                RestrictionAreaID,
                RestrictionAreaStatus
              ) 
              VALUES
                (?, ?, ?, ?) 
                ON DUPLICATE KEY 
                UPDATE 
                  RestrictionAreaID = ?,
                  RestrictionAreaStatus = ?";

        $query = $this->db->query($sql, array($data['MemberID'], $data['PlotNr'], $areaid, $status, $areaid, $status));
    }

    public function importGPSStatus($file) {
        $this->load->library('Excel');

        $data = $this->excel->import($file);

        $key = array();
        $key[0] = 'MEMBERID';
        $key[1] = 'MEMBERNAME';
        $key[2] = 'PLOTNR';
        $key[3] = 'SURVEYNR';
        $key[4] = 'STAT_POINT';
        $key[5] = 'CTR_LONG';
        $key[6] = 'CTR_LAT';

        if (!empty($data) && $data[0] === $key) {
            unset($data[0]);
            $this->db->delete('ktv_gps_status', array('UserID' => $_SESSION['userid']));

            if (!empty($data)) {
                $data_insert = array();
                foreach ($data as $value) {
                    $tmp = array();
                    $tmp['UserID'] = $_SESSION['userid'];

                    $tmp['MemberDisplayID'] = $value[0];
                    $tmp['MemberName'] = $value[1];
                    $tmp['PlotNr'] = $value[2];
                    $tmp['SurveyNr'] = $value[3];
                    $tmp['StatusPoint'] = $value[4];
                    $tmp['Longitude'] = $value[5];
                    $tmp['Latitude'] = $value[6];

                    $Member = $this->getMember($tmp['MemberDisplayID']);
                    if ($Member !== false) {
                        $tmp['MemberID'] = $Member['MemberID'];
                    } else {
                        $tmp['Valid'] = 0;
                        $tmp['Errors'] = "Member doesn't exist";
                        $data_insert[] = $tmp;
                        continue;
                    }


                    $Plot = $this->getPlot($tmp['MemberID'], $tmp['PlotNr'], $tmp['SurveyNr']);
                    // var_dump($Plot);die();

                    if ($Plot !== false) {
                        $tmp['MemberID'] = $Plot['MemberID'];
                    } else {
                        $tmp['Valid'] = 0;
                        $tmp['Errors'] = "Garden doesn't exist";
                        $data_insert[] = $tmp;
                        continue;
                    }
                    
                    $tmp['Valid'] = 1;
                    $tmp['Errors'] = "";
                    $this->db->insert('ktv_gps_status', $tmp);
                    // $data_insert[] = $tmp;
                    // break;
                }
                // if (!empty($data_insert)) {
                // echo '<pre>'; print_r($data_insert); echo '</pre>'; exit;
                // $this->db->insert_batch('ktv_gps_status', $data_insert);
                // }
            }
        }
    }

    public function getMember($MemberDisplayID) {
        $query = $this->db->get_where('ktv_members', array('MemberDisplayID' => $MemberDisplayID), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getPlot($MemberID, $PlotNr, $SurveyNr) {
        $query = $this->db->get_where('ktv_survey_plot', array('MemberID' => $MemberID, 'PlotNr' => $PlotNr, 'SurveyNr' => $SurveyNr), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getGPSStatus($offset = 0, $limit = 20) {
        $sql = "
SELECT SQL_CALC_FOUND_ROWS
    *
FROM ktv_gps_status
WHERE
    UserID = ?
LIMIT ?,?
        ";
        $query = $this->db->query($sql, array($_SESSION['userid'], intval($offset), intval($limit)));

        if ($query->num_rows() > 0) {
            $return['data'] = $query->result_array();
            $query = $this->db->query("SELECT FOUND_ROWS() AS total");
            $return['total'] = $query->row_array(0)['total'];
            return $return;
        }
        return false;
    }

    public function updateGPSStatus() {
        $this->db->trans_start(FALSE);
        $sql = "UPDATE ktv_survey_plot p
                JOIN ktv_gps_status g ON g.MemberID = p.MemberID AND p.PlotNr = g.PlotNr AND g.SurveyNr = p.SurveyNr
                SET
                    p.Latitude = g.Latitude
                    , p.Longitude = g.Longitude
                    , p.StatusCheck = g.StatusPoint
                    , p.StatusCheckBy = g.UserID
                    , p.StatusCheckDate = NOW()
                WHERE
                    g.Valid = 1
                    AND g.UserID = ?
        ";
        $query = $this->db->query($sql, array($_SESSION['userid']));
        $query = $this->db->get_where('ktv_gps_status', array('UserID' => $_SESSION['userid']));
        foreach ($query->result_array() as $value) {
            $sql = "SELECT MAX(Revision) AS Revision FROM ktv_survey_plot_polygon p WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ? LIMIT 1";
            $query = $this->db->query($sql, array($value['MemberID'], $value['PlotNr'], $value['SurveyNr']));
            if ($query->num_rows() > 0) {
                $polygon = $query->row_array(0);
                $this->db->update('ktv_survey_plot_polygon', array('StatusCheck' => $value['StatusPoint']), array('MemberID' => $value['MemberID'], 'PlotNr' => $value['PlotNr'], 'SurveyNr' => $value['SurveyNr'], 'Revision' => $polygon['Revision']));
            }
        }
        
        // ktv_survey_plot_polygon_geo
        foreach ($query->result_array() as $value) {
            $sql = "SELECT MAX(Revision) AS Revision FROM ktv_survey_plot_polygon_geo p WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ? LIMIT 1";
            $query = $this->db->query($sql, array($value['MemberID'], $value['PlotNr'], $value['SurveyNr']));
            if ($query->num_rows() > 0) {
                $polygon = $query->row_array(0);
                $this->db->update('ktv_survey_plot_polygon_geo', array('StatusCheck' => $value['StatusPoint']), array('MemberID' => $value['MemberID'], 'PlotNr' => $value['PlotNr'], 'SurveyNr' => $value['SurveyNr'], 'Revision' => $polygon['Revision']));
            }
        }

        $sql = "DELETE FROM ktv_gps_status WHERE UserID = ?";
        $query = $this->db->query($sql, array($_SESSION['userid']));
        $this->db->trans_complete();
        // exit;
        return $this->db->trans_status();
    }

    public function clearData($UserId)
    {
      return $this->db->query("DELETE FROM ktv_gps_status WHERE UserID=?", array($UserId));
    }

    public function clearDataKML($UserId)
    {
      return $this->db->query("DELETE FROM ktv_survey_plot_polygon_temp WHERE CreatedBy=?", array($UserId));
    }

}

?>