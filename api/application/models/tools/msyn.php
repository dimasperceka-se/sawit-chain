<?php

use GuzzleHttp\Client;

class Msyn extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->library('curl');
    }

    function replaceData($table, $data, $userId) {
        $sql = "
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME=?";
        $this->db->trans_begin();
        $query = $this->db->query($sql, array($table));
        $fie = $query->result_array();
        for ($i = 0; $i < sizeof($fie); $i++) {
            $arrFie[] = $fie[$i]['COLUMN_NAME'];
        }
        for ($i = 0; $i < sizeof($data[0]); $i++) {
            if ($data[0][$i] == 'RegionID')
                $data[0][$i] = 'VillageID';
            if ($data[0][$i] == 'MotorCycle')
                $data[0][$i] = 'Motorcycle';
            if ($data[0][$i] == 'Handphone')
                $data[0][$i] = 'HandPhone';
        }
        $fiel = array_intersect($data[0], $arrFie);
        $field = implode('`,`', $fiel);
        for ($i = 0; $i < sizeof($fiel); $i++) {
            $isian[$i] = '?';
        }
        $isi = implode(',', $isian);
        $sql = "REPLACE into $table (`$field`) values ($isi)";
        $this->db->query('set FOREIGN_KEY_CHECKS=0', array());
        $fiel = array_values($fiel);
        for ($i = 1; $i < sizeof($data) - 1; $i++) {
            for ($j = 0; $j < sizeof($data[0]); $j++) {
                for ($k = 0; $k < sizeof($fiel); $k++) {
                    if (trim($data[0][$j]) == trim($fiel[$k])) {
                        $value[$j] = $data[$i][$j];
                        $fieldd[$j] = $data[0][$j];
                        if ($table == 'ktv_ppiscore2012') {
                            if ($data[0][$j] == 'FarmerID')
                                $sc[$i] .= $data[$i][$j];
                            if ($data[0][$j] == 'SurveyNr')
                                $sc[$i] .= $data[$i][$j];
                        }
                        break;
                    }
                }
            }
            $this->db->query($sql, $value);
        }
        if ($table == 'ktv_ppiscore2012') {
            $sql = '
               update ktv_ppiscore2012 a left join
               (SELECT
                  FarmerID,SurveyNr,
                  CASE Householdmembers
                  when 1 THEN 0
                  WHEN 2 THEN 6
                  WHEN 3 THEN 12
                  WHEN 4 THEN 18
                  WHEN 5 THEN 24
                  WHEN 6 THEN 35
                  END + CASE Schooling
                  WHEN 1 THEN 0
                  WHEN 2 THEN 0
                  WHEN 3 THEN 2
                  END + CASE Education
                  WHEN 1 THEN 0
                  WHEN 2 THEN 3
                  WHEN 3 THEN 4
                  WHEN 4 THEN 4
                  WHEN 5 THEN 5
                  WHEN 6 THEN 7
                  WHEN 7 THEN 18
                  END + CASE Employment
                  WHEN 1 THEN 0
                  WHEN 2 THEN 0
                  WHEN 3 THEN 1
                  WHEN 4 THEN 3
                  WHEN 5 THEN 4
                  WHEN 6 THEN 6
                  END + CASE ToiletFacility
                  WHEN 1 THEN 0
                  WHEN 2 THEN 2
                  WHEN 3 THEN 4
                  END + CASE HouseFloor
                  WHEN 1 THEN 0
                  WHEN 2 THEN 5
                  END + CASE CookingFuel
                  WHEN 1 THEN 0
                  WHEN 2 THEN 5
                  END + CASE Refrigerator
                  WHEN 1 THEN 0
                  WHEN 2 THEN 9
                  END + CASE GasCylinder
                  WHEN 1 THEN 0
                  WHEN 2 THEN 7
                  END + CASE Motorcycle
                  WHEN 1 THEN 0
                  WHEN 2 THEN 9
                  END as TotalScore
                  FROM
                  ktv_ppiscore2012) b on a.FarmerID=b.FarmerID and a.SurveyNr=b.SurveyNr
                  left join ktv_ppi_calculation c on Type="PPI 2012" and (TotalScore between ScoreMin and ScoreMax)
                  set Score=TotalScore,a.National=c.National,`1.25/day`=`$1.25/day`,`2.5/day`=`$2.5/day`
                  where concat(b.FarmerID,b.SurveyNr) in ("' . implode('","', $sc) . '")';
            $this->db->query($sql, array());
        } elseif ($table == 'ktv_family')
            $this->perbaiki_family();
        $this->db->query('set FOREIGN_KEY_CHECKS=1', array());
        $this->db->trans_complete(); //exit;
        return $this->db->trans_status();
    }

    function getDataFoto($id) {
        $sql = "
            SELECT Province,FarmerID,Photo
            FROM ktv_farmer
            LEFT JOIN ktv_village kv ON kv.VillageID = ktv_farmer.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            WHERE FarmerID=? OR OldFarmerID=?";
        $query = $this->db->query($sql, array($id, $id));
        $result = $query->result_array();
        //$this->db->query($sql_update, array($result[0]['Province'], $file, $result[0]['FarmerID']));
        return $result[0];
    }

    /**
     * Proses synchronize
     * Memproses data sync dari web maupun dari mobile
     * Hanya table yang di whitelist yang bisa di update
     * @param  array $data  array data, row pertama adalah nama kolom/field, row setelahnya value dari field sesuai urutan kolom
     * @param  string $table nama table yang akan di update
     * @return boolean        true/false
     */
    public function processSyn($data, $table) {
        $table_whitelist = array(
            'ktv_adoption_observations',
            'ktv_access_cpg',
            'ktv_access_staff',
            'ktv_area',
            'ktv_buying_transaction',
            'ktv_buying_transaction_detail',
            'ktv_certification',
            'ktv_certification_audit_log',
            'ktv_certification_old',
            'ktv_certification_signature',
            'ktv_farmer',
            'ktv_farmer_financial',
            'ktv_farmer_garden',
            'ktv_farmer_garden_area',
            'ktv_farmer_garden_area_detail',
            'ktv_farmer_garden_status',
            'ktv_farmer_gps_temp',
            'ktv_farmer_other_land',
            'ktv_farmer_post_harvest',
            'ktv_farmer_seq',
            'ktv_farmer_temp',
            'ktv_compost',
            'ktv_compost_transaction',
            'ktv_cooperative_staff',
            'ktv_cooperatives',
            'ktv_cpg',
            'ktv_cpg_batch',
            'ktv_cpg_batch_trainings',
            'ktv_cpg_batch_trainings_attendance',
            'ktv_cpg_batch_trainings_farmers',
            'ktv_cpg_farmer_member',
            'ktv_cpg_partner',
            'ktv_cpg_staff',
            'ktv_cpg_trainings',
            'ktv_district',
            'ktv_district_partner',
            'ktv_documents',
            'ktv_environment',
            'ktv_extension_staff',
            'ktv_family',
            'ktv_ics',
            'ktv_ics_members',
            'ktv_institution',
            'ktv_kader_trainings',
            'ktv_kader_trainings_participants',
            'ktv_master_trainings',
            'ktv_master_trainings_participants',
            'ktv_nursery',
            'ktv_nursery_monitoring',
            'ktv_nursery_transaction',
            'ktv_nutrition',
            'ktv_organization_type',
            'ktv_persons',
            'ktv_photo',
            'ktv_position',
            'ktv_ppi_calculation',
            'ktv_ppiscore',
            'ktv_ppiscore2012',
            'ktv_private_staff',
            'ktv_program_partner',
            'ktv_program_staff',
            'ktv_province',
            'ktv_regional',
            'ktv_saving_pilot',
            'ktv_sms_inbound',
            'ktv_social',
            'ktv_story',
            'ktv_subdistrict',
            'ktv_supplychain',
            'ktv_supplychain_batch',
            'ktv_supplychain_non_farmer',
            'ktv_supplychain_org',
            'ktv_supplychain_org_rel',
            'ktv_supplychain_package',
            'ktv_supplychain_payment',
            'ktv_supplychain_payment_detail',
            'ktv_supplychain_premium',
            'ktv_supplychain_price',
            'ktv_supplychain_quality',
            'ktv_supplychain_quality_standard',
            'ktv_supplychain_reward',
            'ktv_supplychain_staff_',
            'ktv_supplychain_transaction',
            'ktv_supplychain_transaction_dtl',
            'ktv_supplychain_transaction_dtl_farmer',
            'ktv_survey',
            'ktv_trace_package',
            'ktv_trace_price',
            'ktv_trace_quality',
            'ktv_trace_quality_standard',
            'ktv_trader_staff',
            'ktv_traders',
            'ktv_video',
            'ktv_village',
            'ktv_warehouse',
            'ktv_warehouse_staff',
            'ktv_village_crop',
            'ktv_village_infrastructure'
        );

        // fields yang di IGNORE
        $excluded_fields = array(
            'FlagSyncInsert', 'GardenCount', 'FlagSyncUpdate', 'id', 'PrePostSurvey',
            'Photo', 'Photo_path', 'Family', 'IsCertification', // farmer
            'InfrastructureID', 'VillageCropID', 'FarmerName'
        );

        // field yang masuk IGNORE, tapi tetap di masukkan untuk table tertentu
        $special_included_fields = array(
            'ktv_farmer' => 'FarmerName' // FarmerName di exclude di table selain ktv_farmer, tapi diinput di table ktv_farmer
        );

        // convert fields, field di excel (header) tidak sama dengan field di db
        $convert_fields = array(
            'RegionID' => 'VillageID',
            'MotorCycle' => 'Motorcycle',
            'Handphone' => 'HandPhone',
        );

        // fields table yang memerlukan kondisi khusus (field lain misal) agar bisa update
        $special_table_fields = array(
            'ktv_farmer_garden' => array(
                // field => condition
                'Latitude' => "StatusGPS != 'verified'",
                'Longitude' => "StatusGPS != 'verified'",
            )
        );

        // table yang boleh update all data, meskipun di db sudah ada value nya
        $table_allowed_update = array(
            'ktv_farmer',
            'ktv_family'
        );

        //field dengan format datetime
        $datetime_field = array(
            'DateCreated',
            'DateUpdated',
            'DateSync',
            'DateCollection',
            'CandidateSelection'
        );

        // cek if table ada dalam whitelist yg boleh di update
        if (in_array($table, $table_whitelist)) {
            $fields = $data[0];
            unset($data[0]);

            // get table primary key
            $primary_keys = $this->getPrimaryKey($table);

            // process fields
            $excluded_key = array();
            foreach ($fields as $key => $value) {
                // remove excluded fields
                if (array_key_exists($table, $special_included_fields)) {
                    if ($value != $special_included_fields[$table]) {
                        if (in_array($value, $excluded_fields)) {
                            $excluded_key[] = $key;
                            unset($fields[$key]);
                        }
                    }
                } else if (in_array($value, $excluded_fields)) {
                    $excluded_key[] = $key;
                    unset($fields[$key]);
                }
                // convert fields
                if (in_array($value, array_keys($convert_fields))) {
                    $fields[$key] = $convert_fields[$value];
                }
            }
            // compose basic insert query
            $sql = "INSERT INTO {$table} (`" . implode('`,`', $fields) . "`) VALUES ";

            // compose VALUES query
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
                ('" . implode("','", $value) . "')";
            }
            $sql .= implode(', ', $values);
            // set conditions, default to IF value of field di db NULL OR '0'
            $conditions = array();
            // special conditions set before
            $special_fields = array();
            if (in_array($table, array_keys($special_table_fields))) {
                $special_fields = $special_table_fields[$table];
            }
            // cek if table termasuk table yang boleh update tanpa kondisi
            if (!in_array($table, $table_allowed_update)) {
                // default, update with condition
                foreach ($fields as $key => $field) {
                    // skip update if field is primary key
                    if (!in_array($field, $primary_keys)) {
                        if (!empty($special_fields[$field])) {
                            $conditions[] = "
                            `{$field}` = IF((`{$field}` IS NULL OR `{$field}` = '0') AND {$special_fields[$field]}, VALUES(`{$field}`), `{$field}`)";
                        } else {
                            if (!in_array($field, $datetime_field)) {
                                $conditions[] = "
                            `{$field}` = IF(`{$field}` IS NULL OR `{$field}` = '0', VALUES(`{$field}`), `{$field}`)";
                            } else {
                                $conditions[] = "
                            `{$field}` = IF(`{$field}` IS NULL, VALUES(`{$field}`), `{$field}`)";
                            }
                        }
                    }
                }
            } else {
                // allow update no condition
                foreach ($fields as $key => $field) {
                    // skip update if field is primary key
                    if (!in_array($field, $primary_keys)) {
                        if (!empty($special_fields[$field])) {
                            $conditions[] = "
                            `{$field}` = IF({$special_fields[$field]}, VALUES(`{$field}`), `{$field}`)";
                        } else {
                            $conditions[] = "
                            `{$field}` = VALUES(`{$field}`)";
                        }
                    }
                }
            }
            $sql .= "
            ON DUPLICATE KEY UPDATE " . implode(',', $conditions);
//            echo "<pre>";
//            print_r($sql);
//            echo "</pre>";
            $add = $this->db->query($sql);
//            echo "<pre>";
//            print_r($this->db->last_query());
//            echo "</pre>";
        } else {
            // exit('Table not allowed');
//            echo "<pre>";
//            print_r('tabel not in whitelist');
//            echo "</pre>";
            return false;
        }
        if (trim($table) == 'ktv_family' and $add) {
//            $this->perbaiki_family();
        }
        return $add;
    }

    public function getPrimaryKey($table) {
        $keys = array();
        $sql = "SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $key => $value) {
                $keys[] = $value['Column_name'];
            }
        }
        return $keys;
    }

    public function process_data($post, $table, $primary_key, $allowed_fields, $not_null) {
        $result['success'] = false;
        $result['message'] = '';
        $fields = array_keys($post);

        // check if primary key exist on data
        $array_check = array_intersect($fields, $primary_key);
        $array_diff = array_diff($array_check, $primary_key);
        if (empty($array_check) || !empty($array_diff)) {
            $primary = implode(', ', $primary_key);
            $result['message'] = "You must provide : {$primary}";
            return $result;
        }

        // check fields
        $invalid_fields = array();
        foreach ($fields as $key => $value) {
            if (!in_array($value, $allowed_fields)) {
                $invalid_fields[] = $value;
            }
        }
        if (!empty($invalid_fields)) {
            $invalid_fields = implode(', ', $invalid_fields);
            $result['message'] = "Invalid parameters : {$invalid_fields}";
            return $result;
        }
        // chek not null fields
        $null_fields = array();
        foreach ($post as $key => $value) {
            if (in_array($key, $not_null) && $value == null) {
                $null_fields[] = $key;
            }
        }
        if (!empty($null_fields)) {
            $null_fields = implode(', ', $null_fields);
            $result['message'] = "These field/s must be filled : {$null_fields}";
            return $result;
        }

        $values = array_values($post);
        $process = $this->msyn->processSyn(array($fields, $values), $table);
        if ($process == false) {
            $result['message'] = 'Process failed';
        } else {
            $result['success'] = true;
            $result['message'] = 'Process success';
        }

        return $result;
    }

    function perbaiki_family() {
        //return;
        $sql_221 = "
         SELECT MAX(total) tot FROM (
         	SELECT COUNT(FamilyID) total FROM ktv_family GROUP BY FamilyID
          ) a WHERE total>1";
        $query = $this->db->query($sql_221, array());
        $ata = $query->result_array();
        for ($j = 1; $j < $ata[0]['tot']; $j++) {
            $sql_11 = "update ktv_family a
            left join ktv_family b on a.FarmerID=b.FarmerID and a.AnggotaName=b.AnggotaName and a.FamilyID<b.FamilyID
            set a.HubunganKeluarga=b.HubunganKeluarga, a.AnggotaAge=b.AnggotaAge, a.AnggotaGender=b.AnggotaGender,
               a.StatusSekolah=b.StatusSekolah,a.Photo=b.Photo,a.DateUpdated=b.DateUpdated
            where b.FarmerID is not null";
            $sql_12 = "delete b.* from ktv_family a
            left join ktv_family b on a.FarmerID=b.FarmerID and a.AnggotaName=b.AnggotaName and a.FamilyID<b.FamilyID
            where b.FarmerID is not null";
            //update ktv_family set FamilyID=0-FamilyID where FamilyID<0;

            /* $sql_22 = "update ktv_family
              set FamilyID=(@rownum := @rownum + 1 ) + ?
              where FamilyID in (
              select FamilyID from (
              select FamilyID,count(FamilyID) total from ktv_family group by FamilyID
              ) a where total>1
              ) and date(DateUpdated)>'2014-09-01'"; */
            $this->db->query($sql_11, array());
            $this->db->query($sql_12, array());

            $sql_21 = "select max(FamilyID) id from ktv_family";
            $sql_22 = "select FamilyID,DateCreated,DateUpdated from (
            	select FamilyID,max(DateCreated) DateCreated,max(DateUpdated) DateUpdated, count(FamilyID) total
               from ktv_family group by FamilyID
            ) a where total>1";
            $sql_23 = "update ktv_family set FamilyId=?+? where FamilyId=? and DateCreated=?";
            $query = $this->db->query($sql_21, array());
            $dat = $query->result_array();
            $query = $this->db->query($sql_22, array());
            $da = $query->result_array();
            for ($i = 0; $i < sizeof($da); $i++) {
                $this->db->query($sql_23, array(($dat[0]['id'] - 0), ($i + 1), $da[$i]['FamilyID'], $da[$i]['DateCreated']));
                //echo $this->db->last_query();
            }
        }
    }

    /**
     * @param  Int FarmerID
     * @param  array upload data
     * @param  string upload path
     * @return boolean true/false
     */
    public function updateFarmerPhoto($FarmerID, $upload_data, $upload_path) {
        $sql = "UPDATE
    `ktv_farmer`
SET
    `Photo`         = ?,
    `Photo_base64`  = ?
WHERE
    `FarmerID`      = ?
        ";
        $base64 = base64_encode(file_get_contents($upload_data['full_path']));
        return $this->db->query($sql, array(
                    $upload_path . $upload_data['file_name'],
                    $base64,
                    $FarmerID,
        ));
    }

    function processSynPhoto($FarmerID, $Photo) {
        $this->db->trans_start();
        $sql1 = "INSERT INTO ktv_photo_history(FarmerID, Photo, IsActive, DateCreated) VALUES (?,?,'1',NOW())";
        $query1 = $this->db->query($sql1, array($FarmerID, $Photo));

        $sql2 = "UPDATE ktv_farmer SET Photo=? where FarmerID=?";
        $query2 = $this->db->query($sql2, array($Photo, $FarmerID));

        $Path = base_url() . 'images/Photo/' . $Photo;
        $sql3 = "INSERT INTO cek_photo (FarmerID,Path,Status) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE Path=?, Status=?";
        $query3 = $this->db->query($sql3, array($FarmerID, $Path, 'ada', $Path, 'ada'));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $result['success'] = false;
        } else {
            $result['success'] = true;
        }
        return $result;
    }

    function processSynPhotoSign($FarmerID, $Photo) {
        $sql2 = "UPDATE ktv_farmer SET LearningContractSign=? where FarmerID=?";
        $query2 = $this->db->query($sql2, array($Photo, $FarmerID));

        if ($query2) {
            $result['success'] = false;
        } else {
            $result['success'] = true;
        }
        return $result;
    }

    public function prosesUploadSurveyPlotPolygon($jsonProses, $filenameGeotrace) {
        $this->db->trans_start();
        $arrLabel = array();
        $isTuntas = false;

        $arrEvents = $jsonProses['events'];
        foreach ($arrEvents as $k => $v) {
            $data = $this->readUidEvents($v['event']);

            $dateCollection = date('Y-m-d H:i:s');
            if (isset($data['eventDate'])) {
                $dateCollection = $data['eventDate'];
            }

            if ($data) {
                $user_id = $this->getUserId($data['completedBy']);
                if (!$user_id) {
                    $user_id = '1'; // kalo user id ga ditemukan, diinput sebagai admin
                }
                $polygon_identity = array();

                $latitude = $longitude = '';
                if (isset($data['coordinate']['latitude'])) {
                    $latitude = $data['coordinate']['latitude'];
                }
                if (isset($data['coordinate']['longitude'])) {
                    $longitude = $data['coordinate']['longitude'];
                }


                foreach ($data['dataValues'] as $key => $val) {
                    // $field = $this->getDataElementName($val['dataElement']);
                    $field = $this->getDataElementNameForKafka($val['dataElement'],$data['program']);
                    if ($val['dataElement'] == 'zu98l7YBtMy') {
                        $luas = $val['value'];
                    }
                    if ($field) {
                        $polygon_identity[$field] = $val['value'];
                    }
                }
            } else {
                _debuglog("eventuid not found");
            }

            //cari surveynya dulu
            // $sql = "SELECT
            //         a.`MemberID`
            //         , a.`PlotNr`
            //         , a.`SurveyNr`
            //         , a.`DateCollection`
            //     FROM
            //         ktv_survey_plot a
            //         LEFT JOIN ktv_members b ON b.MemberID = a.MemberID
            //     WHERE
            //         b.`MemberUID` = ?
            //         AND a.PlotNr = ?
            //         AND a.SurveyNr = ?";
//            sementara survey di harcode survey 0
            // $query = $this->db->query($sql, array($polygon_identity['MemberUID'], $polygon_identity['PlotNr'], $polygon_identity['SurveyNr']));
            
            // data polygon langsung diproses masuk ke database meskipun data plantation belum masuk ke database//
            // hasil keputusan dani setelah banyak kasus data polygon masuk lebih dulu sebelum data plantation 
            $sql = "SELECT
                    a.`MemberID`
                FROM
                    ktv_members a
                WHERE
                    a.`MemberUID` = ?";
            //============================//
            $query = $this->db->query($sql, array($polygon_identity['MemberUID']));
            $dataSurvey = $query->row_array();
            if ($dataSurvey['MemberID'] != "") {
//                //hapus dulu data polygon
//                $sql = "DELETE FROM ktv_survey_plot_polygon WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ? AND DateCollection = ? AND StatusCheck != 'verified'";
//                $query = $this->db->query($sql, array($dataSurvey['MemberID'], $dataSurvey['PlotNr'], $dataSurvey['SurveyNr'], $dataSurvey['DateCollection']));
                // $revision = $this->getLatestRevisionPolygon($dataSurvey['MemberID'], $dataSurvey['PlotNr'], $dataSurvey['SurveyNr']);
                $revision = $this->getLatestRevisionPolygon($dataSurvey['MemberID'], $polygon_identity['PlotNr'], $polygon_identity['SurveyNr']);
                //insert polygon
                for ($j = 0; $j < count($v['geotraceValues']); $j++) {
                    $sql = "INSERT INTO `ktv_survey_plot_polygon` SET
                        `MemberID` = ?,
                        `MemberUID` = ?,
                        `PlotNr` = ?,
                        `SurveyNr` = ?,
                        `DateCollection` = ?,
                        `Revision` = ?,
                        `coordId` = ?,
                        `latitude` = ?,
                        `longitude` = ?,
                        `accuracy` = ?,
                        `altitude` = ?,
                        OrderNr = ?,
                        StatusCheck = ?,
                        DateCreated = NOW(),
                        CreatedBy = ?,
                        DateSync = NOW(),
                        `uid` = ?";
                    $jIncre = $j + 1;
                    $p = array(
                        $dataSurvey['MemberID'],
                        $polygon_identity['MemberUID'],
                        $polygon_identity['PlotNr'],
                        $polygon_identity['SurveyNr'],
                        $dateCollection,
                        $revision,
                        $arrEvents[$k]['geotraceValues'][$j]['coordId'],
                        $arrEvents[$k]['geotraceValues'][$j]['latitude'],
                        $arrEvents[$k]['geotraceValues'][$j]['longitude'],
                        $arrEvents[$k]['geotraceValues'][$j]['accuracy'],
                        $arrEvents[$k]['geotraceValues'][$j]['altitude'],
                        $jIncre,
                        'new',
                        $user_id,
                        $data['event']
                    );
                    $query = $this->db->query($sql, $p);
                }

                $arrLabel[] = $arrEvents[$i]['event'] . " - Polygon updated";
                $isTuntas = true;
                if ($luas) {
                    $arrLuas = explode("Ha", $luas);
                    $luasPolygon = explode(":", $arrLuas[0]);
                    $luas_polygon = trim(str_replace(',', '.', $luasPolygon[1]));
                    $this->updateHaPolygonData($luas_polygon, $latitude, $longitude, $dataSurvey['MemberID'], $polygon_identity['PlotNr'], $polygon_identity['SurveyNr']);
                }
            } else {
                $arrLabel[] = $arrEvents[$i]['event'] . " - Survey not found";
                $isTuntas = false;
            }
        }

        //kasih lognya
        if ($isTuntas == true) {
            $sql = "UPDATE log_survey_plot_polygon_process SET
                    statusProses = 'Sudah',
                    waktuProses = NOW()
                WHERE
                    filezip = ?
                LIMIT 1";
            $p = array(
                $filenameGeotrace
            );
            $query = $this->db->query($sql, $p);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $labelProses = implode(", ", $arrLabel);
            if($isTuntas == true){
                $result['proses'] = true;
                $result['labelProses'] = $labelProses;
            } else {
                $result['proses'] = false;
                $result['labelProses'] = $labelProses;
            }
        } else {
            $result['proses'] = false;
            $result['labelProses'] = "";
        }

        return $result;
    }

    public function getLatestRevisionPolygon($memberid, $plotNr, $surveyNr) {
        $sql = "SELECT MAX(Revision) AS Revision FROM ktv_survey_plot_polygon a WHERE a.MemberID = ? AND a.PlotNr = ? AND a.SurveyNr = ?";
        $p = array(
            $memberid,
            $plotNr,
            $surveyNr
        );
        $query = $this->db->query($sql, $p);

        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['Revision'] + 1;
        }
    }

    public function updateHaPolygonData($luas_polygon, $latitude, $longitude, $MemberID, $PlotNr, $SurveyNr) {
        if(($latitude !="" || $latitude != 0) && ($longitude != "" || $longitude != 0)){
            $sql = "UPDATE ktv_survey_plot
                    SET
                        Latitude = ?,
                        Longitude = ?,
                        GardenAreaPolygon = ?
                    WHERE
                        MemberID = ? AND
                        PlotNr = ? AND
                        SurveyNr = ?";
            $params = array(
                $latitude,
                $longitude,
                $luas_polygon,
                $MemberID,
                $PlotNr,
                $SurveyNr
            );
            $query = $this->db->query($sql, $params);
        }
    }

    public function getDataElementName($uid) {
        if ($uid) {
            $this->db->select('b.reference_field');
            $this->db->from('mw_dataelement a');
            $this->db->join('mw_programstagedataelement b', 'a.dataelementid = b.dataelementid');
            $this->db->where('a.uid', $uid);
            $this->db->where('b.programstageid', '6912740');
            $this->db->where('b.reference_field IS NOT NULL');
            $query = $this->db->get();
        }
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            return $data[0]['reference_field'];
        }
        return false;
    }

    public function getDataElementNameForKafka($uid, $program_uid){
        if(isset($uid) && isset($program_uid)){
            $sql = "SELECT b.field_reff FROM mw_dataelement as a
            LEFT JOIN mw_mapping b ON a.uid = b.dataelement_uid
            Where a.uid = ? and b.program_uid = ? and b.field_reff is not null";
            $p = array(
                $uid,
                $program_uid
            );
            $query = $this->db->query($sql, $p);
            if ($query->num_rows() > 0) {
                $data = $query->result_array();
                return $data[0]['field_reff'];
            }
        }
        return false;
    }

    private function readUidEvents($uid) {
        // compose url
        $url = $this->config->item('dhis_url') . 'api/events/' . $uid;

        $this->curl->create($url);
        $this->curl->options(array(
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
            )
        ));
        /*
         * ps : Autorization hardcode, sesuaikan lagi jika user dan password middleware diganti
         */

        $response = $this->curl->execute();

        return json_decode($response, true);
    }

    public function updatePolygon_From_pullMiddlewareData() {
        $mem_ini = ini_get('memory_limit');

        //ambil list file geotrace yg sudah terupload dan belum di proses
        $sql = "SELECT
                a.`filezip`
            FROM
                log_survey_plot_polygon_process a
            WHERE
                a.`statusProses` = 'Belum'
            ";
        $query = $this->db->query($sql);
        $dataFilezip = $query->result_array();

        for ($i = 0; $i < count($dataFilezip); $i++) {
            if ($dataFilezip[$i]['filezip'] != "") {
                $filenameProses = $dataFilezip[$i]['filezip'];

                //ambil namafile
                $namaFilefolder = str_replace('.zip', '', $filenameProses);

                $zipLib = new ZipArchive;
                $openZip = $zipLib->open('files/survey_plot_polygon/' . $filenameProses);

                if ($openZip == true) {
                    $zipLib->extractTo('files/tmp/' . $namaFilefolder);
                    $zipLib->close();

                    //proses buka dan read file .json nya
                    $jsonProsesString = file_get_contents('files/tmp/' . $namaFilefolder . '/geotrace.json');
                    $jsonProses = json_decode($jsonProsesString, true);

                    $prosesSave = $this->prosesUploadSurveyPlotPolygon($jsonProses, $filenameProses);
                    if ($prosesSave['proses'] == true) {
                        $statusProses = true;
                        $msgProses = "Proses save polygon success, Detail : " . $prosesSave['labelProses'];
                        $this->moveFileToProcessedFolder($filenameProses, true);
                    } else {
                        $statusProses = false;
                        $msgProses = "Proses save polygon failed - $filenameProses";
                        $this->moveFileToProcessedFolder($filenameProses, false);
                    }

                    //hapus folder
                    unlinkr("files/tmp/" . $namaFilefolder);
                } else {
                    echo "Failed to open $filenameProses<br />";
                }
            }
        }

        ini_set('memory_limit', $mem_ini);
    }

    public function moveFileToProcessedFolder($filename, $status) {
        $sql = "SELECT a.`statusProses` FROM
                log_survey_plot_polygon_process a
            WHERE
                a.`filezip` = ?";
        $query = $this->db->query($sql, array($filename));
        if ($query->num_rows() > 0) {
            $dataFilezip = $query->result_array();
            $statusProses =  $dataFilezip[0]['statusProses'];
        } else {
            $statusProses = "";
        }
        if($statusProses == 'Sudah'){
            $path = 'files/survey_plot_polygon';
            $path_backup = 'files/polygon/backup';
            $path_failed = 'files/polygon/failed';
            
            $moved = array();
            $source = $path . '/' . $filename;
            if ($status) {
                $destination = $path_backup . '/' . $filename;
                $statusfile = 'backup';
            } else {
                $destination = $path_failed . '/' . $filename;
                $statusfile = 'failed';
            }
    
            if (file_exists($source)) {
                $copy = copy($source, $destination);
                if ($copy) {
                    unlink($source);
                    $this->updateFileStatusPolygon($filename, $statusfile);
                    array_push($moved, $source);
                }
            }
        }
    }

    public function updateFileStatusPolygon($filename, $statusfile) {
        $sql = "UPDATE log_survey_plot_polygon_process SET statusFile = ? WHERE filezip = ?";
        $query = $this->db->query($sql, array($statusfile, $filename));
    }

    public function updateImages_From_pullMiddlewareData() {
        $this->load->model('mcommon', '_model');
        $path = 'files/upload';
        $path_backup = 'files/backup';
        $path_failed = 'files/failed';
        $files = scandir($path);
        if (count($files) > 2) {
            for ($i = 2; $i < 1000; $i++) {
                $name = @$files[$i];
                $filename = pathinfo(@$files[$i], PATHINFO_FILENAME);
                $arrFilename = explode("_", $filename);

                $FarmerUID = $arrFilename[1];
                $eventUID = $arrFilename[2];
                $timestamp = $arrFilename[3];

                //get member data
                $this->load->model('grower/mgrower');
                $getData = $this->mgrower->getMemberDataByUID($FarmerUID);
                $MemberData = $getData['data'];
                $ProvinceID = $MemberData['ProvinceID'];

                //ambil extensionnya
                $arrTemp = explode(".", $name);
                $extNya = array_values(array_slice($arrTemp, -1))[0];

                switch ($arrFilename[0]) {
                    case 'Farmer Photo':
                        //upload foto petani
                        //cek folder propinsi itu sudah ada belum
                        if (!file_exists('images/member/' . $ProvinceID)) {
                            mkdir('images/member/' . $ProvinceID, 0777, true);
                        }
                        $nameFileUpload = $FarmerUID . "." . $extNya;
                        $namaFilePhotoUpdate = $FarmerUID . "." . $extNya;

                        copy($path . '/' . $files[$i], $path_backup . '/' . $files[$i]);
                        if (!rename($path . '/' . $files[$i], "images/member/" . $ProvinceID . "/" . $nameFileUpload)) {
                            rename($path . '/' . $files[$i], $path_failed . '/' . $files[$i]);
                            echo "<pre>";
                            print_r('Unable to write the file' . $files[$i]);
                            echo "</pre>";
                            break;
                        } else {
                            //update datanya
                            $prosesUpdate = $this->_model->updateFotoFarmer($namaFilePhotoUpdate, $MemberData['MemberID']);
                            echo "<pre>";
                            print_r('Proccess save image succes : ' . $files[$i]);
                            echo "</pre>";
                        }
                        break;
                    case 'Contract Photo':
                        //upload foto consent notes
                        //cek folder propinsi itu sudah ada belum
                        if (!file_exists('images/consent/' . $ProvinceID)) {
                            mkdir('images/consent/' . $ProvinceID, 0777, true);
                        }
                        $nameFileUpload = $FarmerUID . "." . $extNya;
                        $namaFilePhotoUpdate = $FarmerUID . "." . $extNya;

                        copy($path . '/' . $files[$i], $path_backup . '/' . $files[$i]);
                        if (!rename($path . '/' . $files[$i], "images/consent/" . $ProvinceID . "/" . $nameFileUpload)) {
                            rename($path . '/' . $files[$i], $path_failed . '/' . $files[$i]);
                            echo "<pre>";
                            print_r('Unable to write the file' . $files[$i]);
                            echo "</pre>";
                            break;
                        } else {
                            //update datanya
                            $prosesUpdate = $this->_model->updateFotoFarmerConsentNote($namaFilePhotoUpdate, $MemberData['MemberID']);
                            echo "<pre>";
                            print_r('Proccess save image succes : ' . $files[$i]);
                            echo "</pre>";
                        }
                        break;
                    case 'receipt':
                        //upload kwitansi survey main buyer
                        $eventUID = $arrFilename[2];

                        //cek ada UID nya tidak
                        $cek = $this->_model->cekSurveyMainBuyerByUID($eventUID);
                        if ($cek == false) {
                            echo "<pre>";
                            print_r('Main Buyer Survey not found' . $files[$i]);
                            echo "</pre>";
                            rename($path . '/' . $files[$i], $path_failed . '/' . $files[$i]);
                            break;
                        }

                        //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                        if (!file_exists('images/main_buyer_last_receipt/' . $ProvinceID)) {
                            mkdir('images/main_buyer_last_receipt/' . $ProvinceID, 0777, true);
                        }
                        if (!file_exists('images/main_buyer_last_receipt/' . $ProvinceID . '/' . $MemberData['MemberUID'])) {
                            mkdir('images/main_buyer_last_receipt/' . $ProvinceID . '/' . $MemberData['MemberUID'], 0777, true);
                        }
                        $namaFilePhotoUpdate = $timestamp . "." . $extNya;
                        $nameFileUpload = $MemberData['MemberUID'] . "/" . $namaFilePhotoUpdate;

                        copy($path . '/' . $files[$i], $path_backup . '/' . $files[$i]);
                        if (!rename($path . '/' . $files[$i], "images/main_buyer_last_receipt/" . $ProvinceID . "/" . $nameFileUpload)) {
                            rename($path . '/' . $files[$i], $path_failed . '/' . $files[$i]);
                            echo "<pre>";
                            print_r('Unable to write the file' . $files[$i]);
                            echo "</pre>";
                            break;
                        } else {
                            //update datanya
                            $prosesUpdate = $this->_model->updateFotoReceiptMainBuyer($namaFilePhotoUpdate, $eventUID);
                            echo "<pre>";
                            print_r('Proccess save image succes : ' . $files[$i]);
                            echo "</pre>";
                        }
                        break;
                    case 'Picture of Visit':
                        //upload foto visitasi garden
                        $eventUID = $arrFilename[2];
                        //cek ada UID nya tidak
                        $cek = $this->_model->cekSurveyGardenByUID($eventUID);
                        if ($cek == false) {
                            echo "<pre>";
                            print_r('Garden Survey not found' . $files[$i]);
                            echo "</pre>";
                            rename($path . '/' . $files[$i], $path_failed . '/' . $files[$i]);
                            break;
                        }

                        //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                        if (!file_exists('images/plot_visit/' . $ProvinceID)) {
                            mkdir('images/plot_visit/' . $ProvinceID, 0777, true);
                        }
                        if (!file_exists('images/plot_visit/' . $ProvinceID . '/' . $MemberData['MemberUID'])) {
                            mkdir('images/plot_visit/' . $ProvinceID . '/' . $MemberData['MemberUID'], 0777, true);
                        }
                        $namaFilePhotoUpdate = $timestamp . "." . $extNya;
                        $nameFileUpload = $MemberData['MemberUID'] . "/" . $namaFilePhotoUpdate;

                        copy($path . '/' . $files[$i], $path_backup . '/' . $files[$i]);
                        if (!rename($path . '/' . $files[$i], "images/plot_visit/" . $ProvinceID . "/" . $nameFileUpload)) {
                            rename($path . '/' . $files[$i], $path_failed . '/' . $files[$i]);
                            echo "<pre>";
                            print_r('Unable to write the file');
                            echo "</pre>";
                            break;
                        } else {
                            //update datanya
                            $prosesUpdate = $this->_model->updateFotoGardenVisit($namaFilePhotoUpdate, $eventUID);
                            echo "<pre>";
                            print_r('Proccess save image succes : ' . $files[$i]);
                            echo "</pre>";
                        }
                        break;
                }
            }
        }
    }

    public function fixUpdateImages() {
        $this->load->model('mcommon', '_model');
        $path = 'images/member';
        $path_failed = 'files/failed';
        $files = scandir($path);
        if (count($files) > 2) {
            for ($i = 2; $i < 30; $i++) {
                $name = @$files[$i];
                $filename = pathinfo(@$files[$i], PATHINFO_FILENAME);
                $extention = pathinfo(@$files[$i], PATHINFO_EXTENSION);
                if ($extention) {
                    //get member data
                    $this->load->model('grower/mgrower');
                    $getData = $this->mgrower->getMemberDataByUID($filename);
                    $MemberData = $getData['data'];
                    $ProvinceID = $MemberData['ProvinceID'];

                    //ambil extensionnya
                    $arrTemp = explode(".", $name);
                    $extNya = array_values(array_slice($arrTemp, -1))[0];

                    $nameFileUpload = $filename . "." . $extNya;
                    if ($MemberData) {
                        rename($path . "/" . $files[$i], $path . "/" . $ProvinceID . '/' . $files[$i]);
                        $prosesUpdate = $this->_model->updateFotoFarmer($nameFileUpload, $MemberData['MemberID']);
                    } else {
                        rename($path . "/" . $files[$i], $path_failed . '/' . $files[$i]);
                    }
                }
            }
        }
    }

    public function autoAssignDataControl() {
        $this->db->trans_begin();

        //ambil data Partner yg ada
        $sql = "SELECT
                a.`PartnerID`
                , a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartner = $query->result_array();

        //====================== BAGIAN AKSES PARTNER (BEGIN) =========================================================//
        //for ($i = 0; $i < count($dataPartner); $i++) {
            //proses untuk ktv_access_partner_member (begin)

            // $sql = "
            //     INSERT INTO ktv_access_partner_member
            //     SELECT
            //         NULL
            //         , '{$dataPartner[$i]['PartnerID']}'
            //         , b.`MemberID`
            //         , NOW()
            //         , '{$_SESSION['userid']}'
            //         , NULL
            //         , NULL
            //     FROM
            //         ktv_district_partner a
            //         INNER JOIN ktv_members b ON a.`DistrictID` = SUBSTR(b.`VillageID`,1,4)
            //         LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID = b.`MemberID` AND apm.`apmPartnerID` = '{$dataPartner[$i]['PartnerID']}'
            //     WHERE
            //         a.`PartnerID` = '{$dataPartner[$i]['PartnerID']}'
            //         AND b.`StatusCode` = 'active'
            //         AND apm.apmID IS NULL
            // ";
            // $query = $this->db->query($sql);

            //proses untuk ktv_access_partner_member (end)

            //proses untuk ktv_access_partner_mill (begin)

            // $sql = "
            //     INSERT INTO ktv_access_partner_mill
            //     SELECT
            //         NULL
            //         , '{$dataPartner[$i]['PartnerID']}'
            //         , b.`MillID`
            //         , NOW()
            //         , '{$_SESSION['userid']}'
            //         , NULL
            //         , NULL
            //     FROM
            //         ktv_district_partner a
            //         INNER JOIN ktv_mill b ON a.`DistrictID` = SUBSTR(b.`VillageID`,1,4)
            //         LEFT JOIN ktv_access_partner_mill apm ON apm.apmiMillID = b.`MillID` AND apm.apmiPartnerID = '{$dataPartner[$i]['PartnerID']}'
            //     WHERE
            //         a.`PartnerID` = '{$dataPartner[$i]['PartnerID']}'
            //         AND b.`StatusCode` = 'active'
            //         AND apm.apmiID IS NULL
            // ";
            // $query = $this->db->query($sql);

            //proses untuk ktv_access_partner_mill (end)
        //}
        //====================== BAGIAN AKSES PARTNER (END)   =========================================================//

        //====================== BAGIAN PARTNER MEMBER (BEGIN)   =========================================================//
        $sql = "UPDATE ktv_members a
                INNER JOIN ktv_district_partner_member b ON SUBSTR(a.`VillageID`,1,4) = b.`DistrictID`
            SET
                a.`PartnerID` = b.`PartnerID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` != '' AND a.`VillageID` != '0' AND a.`VillageID` IS NOT NULL
                AND (a.`PartnerID` IS NULL OR a.`PartnerID` = 0) #Hanya isi yg belum ada PartnerID nya
            ";
        $query = $this->db->query($sql);
        //====================== BAGIAN PARTNER MEMBER (END)     =========================================================//

        //====================== BAGIAN PARTNER MILL (BEGIN)   =========================================================//
        $sql = "UPDATE ktv_mill m
                    INNER JOIN ktv_district_partner_member b ON SUBSTR(m.`VillageID`,1,4) = b.`DistrictID`
                SET
                    m.`PartnerID` = b.`PartnerID`
                WHERE
                    m.`StatusCode` = 'active'
                    AND m.`VillageID` != '' AND m.`VillageID` != '0' AND m.`VillageID` IS NOT NULL
                    AND ( m.`PartnerID` IS NULL OR m.`PartnerID` = 0 ) #Hanya isi yg belum ada PartnerID nya
            ";
        $query = $this->db->query($sql);
        //====================== BAGIAN PARTNER MILL (END)     =========================================================//

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function CompletePlotStatus(){
        $this->db->trans_start();

        /* ========================================= Insert ke Garden Status (Begin) ==================================== */
        //Sudah punya Survey Garden, tapi tidak ada di Garden Status

        $sql = "INSERT IGNORE INTO `ktv_survey_plot_status` (
                    MemberID,
                    PlotNr,
                    DateCreated,
                    CreatedBy
                )
                SELECT
                    t_gar.MemberID
                    , t_gar.PlotNr
                    , NOW()
                    , '1'
                FROM
                (
                    SELECT
                        gar.`MemberID`
                        , gar.`PlotNr`
                    FROM
                        ktv_survey_plot gar
                    WHERE
                        1=1
                        AND gar.`MemberID` != 0
                        AND gar.`PlotNr` != 0
                    GROUP BY gar.`MemberID`, gar.`PlotNr`
                ) AS t_gar
                LEFT JOIN (
                    SELECT
                        gstat.`MemberID`
                        , gstat.`PlotNr`
                    FROM
                        ktv_survey_plot_status gstat
                    WHERE
                        1=1
                        AND gstat.`MemberID` != 0
                        AND gstat.`PlotNr` != 0
                ) AS t_garstat ON 1=1
                    AND t_gar.MemberID = t_garstat.MemberID
                    AND t_gar.PlotNr = t_garstat.PlotNr
                
                INNER JOIN ktv_members mem ON t_gar.MemberID = mem.`MemberID`
                
                WHERE
                    t_garstat.MemberID IS NULL";
        $query = $this->db->query($sql);
        /* ========================================= Insert ke Garden Status (End) ==================================== */

        /* ================ Update Garden Status (Ha, Polygon Ha, Annual Production, Lat, Long) (Begin) ==================================== */
        
        $sql = "UPDATE ktv_survey_plot_status tup
                    INNER JOIN (
                        SELECT
                            sub_pt.`MemberID`
                            , sub_pt.`PlotNr`
                            , sub_pt.`SurveyNr`
                            , sub_pt.`GardenAreaHa`
                            , sub_pt.`GardenAreaPolygon`
                            , sub_pt.`AnnualProduction`
                        FROM
                            ktv_survey_plot sub_pt
                            INNER JOIN (
                                SELECT
                                    sub_pt_max.`MemberID`
                                    , sub_pt_max.`PlotNr`
                                    , MAX(sub_pt_max.`SurveyNr`) AS SurveyNr
                                FROM
                                    ktv_survey_plot sub_pt_max
                                WHERE
                                    1=1
                                GROUP BY sub_pt_max.`MemberID`, sub_pt_max.`PlotNr`
                            ) AS sub_pt_lat ON 1=1
                                AND sub_pt.`MemberID` = sub_pt_lat.MemberID
                                AND sub_pt.`PlotNr` = sub_pt_lat.PlotNr
                                AND sub_pt.`SurveyNr` = sub_pt_lat.SurveyNr
                        WHERE
                            1=1
                        GROUP BY sub_pt.`MemberID`, sub_pt.`PlotNr`	
                    ) AS pt ON 1=1
                        AND tup.`MemberID` = pt.MemberID
                        AND tup.`PlotNr` = pt.PlotNr
                SET
                    tup.`GardenAreaHa` = pt.GardenAreaHa,
                    tup.`GardenAreaPolygon` = pt.GardenAreaPolygon,
                    tup.`AnnualProduction` = pt.AnnualProduction,
                    tup.`DateUpdated` = NOW(),
                    tup.`LastModifiedBy` = '1'";
        $query = $this->db->query($sql);
        
        $sql = "UPDATE ktv_survey_plot_status tup
                    INNER JOIN (
                        SELECT
                            sub_pt.`MemberID`
                            , sub_pt.`PlotNr`
                            , sub_pt.`SurveyNr`
                            #, sub_pt.`Latitude`
                            , ST_Y(sub_pt.`LatLong`) AS Latitude
                            #, sub_pt.`Longitude`
                            , ST_X(sub_pt.`LatLong`) AS Longitude
                            , sub_pt.`LatLong`
                        FROM
                            ktv_survey_plot sub_pt
                            INNER JOIN (
                                SELECT
                                    sub_pt_max.`MemberID`
                                    , sub_pt_max.`PlotNr`
                                    , MAX(sub_pt_max.`SurveyNr`) AS SurveyNr
                                FROM
                                    ktv_survey_plot sub_pt_max
                                WHERE
                                    1=1
                                GROUP BY sub_pt_max.`MemberID`, sub_pt_max.`PlotNr`
                            ) AS sub_pt_lat ON 1=1
                                AND sub_pt.`MemberID` = sub_pt_lat.MemberID
                                AND sub_pt.`PlotNr` = sub_pt_lat.PlotNr
                                AND sub_pt.`SurveyNr` = sub_pt_lat.SurveyNr
                        WHERE
                            1=1
                            /*AND sub_pt.`Latitude` IS NOT NULL
                            AND sub_pt.`Latitude` != ''
                            AND sub_pt.`Latitude` != '0'
                            AND sub_pt.`Longitude` IS NOT NULL
                            AND sub_pt.`Longitude` != ''
                            AND sub_pt.`Longitude` != '0'*/
                            AND ABS(ST_Y(sub_pt.`LatLong`)) > 0
                            AND ABS(ST_X(sub_pt.`LatLong`)) > 0
                        GROUP BY sub_pt.`MemberID`, sub_pt.`PlotNr` 
                    ) AS pt ON 1=1
                        AND tup.`MemberID` = pt.MemberID
                        AND tup.`PlotNr` = pt.PlotNr
                SET
                    tup.`Latitude` = pt.Latitude,
                    tup.`Longitude` = pt.Longitude,
                    tup.`LatLong` = pt.LatLong,
                    tup.`DateUpdated` = NOW(),
                    tup.`LastModifiedBy` = '1'";
        $query = $this->db->query($sql);
        /* ================ Update Garden Status (Ha, Polygon Ha, Annual Production, Lat, Long) (End) ==================================== */

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Process Finished";
        } else {
            $results['success'] = false;
            $results['message'] = "Process Failed";
        }
        return $results;
    }
    private function getUserId($username) {
        $this->db->select('UserID', false);
        $this->db->from('sys_user a');
        $this->db->where("username = '$username'", NULL, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['UserID'];
        }
        return false;
    }
}

?>