<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use GuzzleHttp\Client;

class Mmiddleware extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('curl');
        $this->load->model('tools/msyn');
        $this->load->model('grower/mgrower');
    }

    public function insertData($data, $table, $status) {
        $table_whitelist = array(
            'ktv_members',
            'ktv_member_family_labour',
            'ktv_member_labour',
            'ktv_member_plot_status',
            'ktv_member_other_land',
            'ktv_survey_household',
            'ktv_survey_main_buyer',
            'ktv_survey_finance',
            'ktv_survey_plot'
        );

        // fields yang di IGNORE
        $excluded_fields = array(
            'FlagSyncInsert', 'GardenCount', 'FlagSyncUpdate', 'id', 'PrePostSurvey',
            'Photo', 'Photo_path', 'Family', 'IsCertification', // farmer
            'InfrastructureID', 'VillageCropID', 'FarmerName',
            'frChildrenCount', 'frChildrenSchool', 'frChildrenWorkInFarm', 'frChildrenUnderAgeWork', 'frChildrenTypeOfWork'
        );

        // convert fields, field di excel (header) tidak sama dengan field di db
        $convert_fields = array(
            'RegionID' => 'VillageID',
            'MotorCycle' => 'Motorcycle',
            'Handphone' => 'HandPhone',
        );

        // fields table yang memerlukan kondisi khusus (field lain misal) agar bisa update
        $special_table_fields = array(
            'ktv_cocoa_farmer_garden' => array(
                // field => condition
                'Latitude' => "StatusGPS != 'verified'",
                'Longitude' => "StatusGPS != 'verified'",
            )
        );

        // table yang boleh update all data, meskipun di db sudah ada value nya
        $table_allowed_update = array(
            'ktv_members',
            'ktv_member_plot_status',
            'ktv_survey_plot'
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

            // process fields
            $excluded_key = array();
            foreach ($fields as $key => $value) {
                // remove excluded fields
                if (in_array($value, $excluded_fields)) {
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
                        $value[$k] = $v;
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
            if (!in_array($table, $table_allowed_update)) {
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
            $add = $this->db->query($sql);

            $result['status'] = $add;
            $result['query'] = $this->db->last_query();
            $result['message'] = $this->db->_error_message();
        } else {
            $result['status'] = false;
        }
        return $result;
    }

    public function pullUidData($uid) {
        if ($uid) {
            $data = $this->readUidEvents($uid);
            $date = date('Y-m-d H:i:s');
            if (!empty($data)) {
                $program = $this->getProgramByUid($data['program']);
                if (!empty($program)) {
                    /*
                     * initial variabel
                     */
                    $params = array();
                    $header = array();
                    $body = array();
                    $latitude = '';
                    $longitude = '';


                    if (isset($data['coordinate']['latitude'])) {
                        $latitude = $data['coordinate']['latitude'];
                    }
                    if (isset($data['coordinate']['longitude'])) {
                        $longitude = $data['coordinate']['longitude'];
                    }

                    /*
                     * Susun array header dan value
                     */
                    $VillageID = $MemberUID = $FarmerName = '';
                    foreach ($data['dataValues'] as $key => $value) {
                        $field = $this->getDataElementMapping($program['label'], $program['table'], $value['dataElement']);
                        if ($field) {
                            if ($value['value'] == "true" OR $value['value'] == "TRUE") {
                                $value['value'] = '1';
                            } elseif ($value['value'] == "false" OR $value['value'] == "FALSE") {
                                $value['value'] = '2';
                            }
                            if ($field == 'VillageID' && $program['table'] == 'ktv_members') {
                                $VillageID = $value['value'];
                            }

                            if ($field == 'MemberUID') {
                                $MemberUID = $value['value'];
                            }
                            if ($field == 'MemberName') {
                                $FarmerName = $value['value'];
                                if ($program['table'] != 'ktv_members') {
                                    continue;
                                }
                            }
                            $header[] = $field;
                            $body[] = $value['value'];
                        }
                    }
                    /*
                     * Susun array ke dalam parameter untuk
                     */
                    if (count($header) != 0 && count($body) != 0 && $data['status'] == 'COMPLETED') {
                        //cek apakah uid sudah ada di database atau belum
                        $update = $this->getUID($data['event'], $program['table']);
                        $headerIdentity = $this->getHeaderIdentity($update);
                        $header = array_merge($header, $headerIdentity);

                        $valueIdentity = $this->getValueIdentity($data['completedBy'], $data['created'], $data['lastUpdated'], $update);
                        $body = array_merge($body, $valueIdentity);

                        /*
                         * tambah parameter koordinat kebun
                         */
                        $coordinat = 0;
                        if ($longitude && $latitude && $program['table'] == 'ktv_survey_plot_polygon') {
                            $headerCoordinate = $this->getHeaderCoordinate();
                            $header = array_merge($header, $headerCoordinate);

                            $valueCoordinate = $this->getValueCoordinate($latitude, $longitude);
                            $body = array_merge($body, $valueCoordinate);

                            $coordinat = 1;
                        }
                        $params[] = $header;
                        $params[] = $body;

                        /*
                         * Ambil member id 
                         */
                        $member = $this->getMemberDisplayID($MemberUID, $FarmerName);
                        if (!$member) {
                            if ($program['table'] == 'ktv_members' && $program['uid'] == 'QxauNvjcpBw') {
                                $member = $this->mgrower->genMemberID($VillageID, 'F');
                            }
                        }

                        if ($member) {
                            $key1 = array_search('MemberID', $params[0]);
                            if ($key1 === false) {
                                array_push($params[0], 'MemberID');
                                array_push($params[1], $member['MemberID']);
                            } else {
                                $params[1][$key1] = $member['MemberID'];
                            }
                            if ($program['table'] == 'ktv_members' && $program['uid'] == 'QxauNvjcpBw') {
                                $key = array_search('MemberDisplayID', $params[0]);
                                if ($key === false) {
                                    array_push($params[0], 'MemberDisplayID');
                                    array_push($params[1], $member['MemberDisplayID']);
                                } else {
                                    $params[1][$key] = $member['MemberDisplayID'];
                                }
                            }
                        } else {
                            echo "uid : " . $uid . " (Member Not Found!)";
                            return false;
                        }


                        /*
                         * end Ambil member id
                         */

                        if ($program['table'] == 'ktv_survey_plot') {
                            $params = $this->generateCloneAndShadeTrees($params);
                        }
                        /*
                         * tambah parameter interview Date
                         */
//                        if (!$update) {
                        $params = $this->generateInterviewDate($params, $program['table'], $data['eventDate']);
//                        }
                        /*
                         * tambah parameter uid
                         */
                        $params = $this->generateUID($params, $data['event']);


                        /*
                         * proses insert ke database web
                         */
                        if ($coordinat) {
                            $this->updateCoordinate($params);
                        } else if ($program['uid'] == 'S4bVIIEGCuT') {
                            $this->updateConsentLetter($params);
                        } else {
                            if ($program['uid'] == 'nQxNqbkCil1') {
                                $params = $this->addHarvestMonthParameter($params);
                                $params = $this->calculateProduction($params);
                            }
                            $result = $this->insertData($params, $program['table'], $update);
                            if ($result['status']) {
                                if ($program['table'] == 'ktv_members' && !empty($member)) {
                                    $this->insertMemberRole($member, $update);
                                    $this->insertMemberPartner($member, $VillageID);
                                }
                                $success[$data['completedBy']][$data['program']][$iterasi]['results'] = $result;
                            } else {
                                $this->InsertErrorLog($result, $data['event']);
                            }
                            return $result;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function pullDHIS($syncdate, $program, $orgunit) {
        if ($syncdate) {
            $start = date('Y-m-d H:i:s', strtotime($syncdate));
            $end = date('Y-m-d H:i:s', strtotime(' + 1 day', strtotime($start)));
        } else {
            $start = date('Y-m-d H:i:s', strtotime(' - 1 hour'));
            $end = date('Y-m-d H:i:s');
        }
        $programs = $this->getProgramStages($program);
        $orgUnits = $this->getOrgUnits($orgunit);

        foreach ($programs as $program) {
            foreach ($orgUnits as $orgUnit) {
                if ($program['table'] != '') {
                    $event = $this->getUIDFromDHIS($start, $end, $program['programstageid'], $orgUnit['organisationunitid']);
                    if (count($event) > 0) {
                        foreach ($event as $val) {
                            if ($val['uid']) {
                                $this->pullUidData($val['uid']);
                            }
                        }
                    }
                }
            }
        }
    }

    function getUIDFromDHIS($start, $end, $programstageid, $organisationunitid) {
        $connStr = $this->config->item('postgre');
//        $connStr = "host=139.59.240.58 port=5432 dbname=cocoatrace_sc_app user=postgres password=KoltivaPL2013!";
        $conn = pg_connect($connStr);
        $result = pg_query($conn, "select * from programstageinstance WHERE lastupdated between '$start'::TIMESTAMP and '$end'::timestamp and organisationunitid = $organisationunitid and programstageid = $programstageid");

        return pg_fetch_all($result);
    }

    private function getProgramStages($program = null) {
        if ($program) {
            $program = str_replace("_", " ", $program);

            $this->db->select('a.programstageid,b.uid,b.reference,b.name', false);
            $this->db->from('mw_programstage a');
            $this->db->join('mw_program b', 'a.programid = b.programid');
            $this->db->where('b.status = 1');
            $this->db->where("b.name = '$program'");
            $this->db->order_by('b.order');
            $query = $this->db->get();
        } else {
            $this->db->select('a.programstageid,b.uid,b.reference,b.name', false);
            $this->db->from('mw_programstage a');
            $this->db->join('mw_program b', 'a.programid = b.programid');
            $this->db->where('b.status = 1');
            $this->db->order_by('b.order');
            $query = $this->db->get();
        }

        if ($query->num_rows() > 0) {
            $ProgramStages = array();
            foreach ($query->result_array() as $key => $value) {
                if ($value['reference'] != '') {
                    $ProgramStages[$key]['programstageid'] = $value['programstageid'];
                    $ProgramStages[$key]['uid'] = $value['uid'];
                    $ProgramStages[$key]['table'] = $value['reference'];
                    $ProgramStages[$key]['label'] = $value['name'];
                }
            }
            return $ProgramStages;
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

    public function getUIDs() {
        $sql = "SELECT a.uid FROM uid_add a WHERE a.status = 1";
        $query = $this->db->query($sql, array());
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function updateUIDAdd($result, $uid) {
        $sql = "UPDATE uid_add a SET a.status = 2, a.notes = ?, a.date = NOW() WHERE a.uid = ?";
        $query = $this->db->query($sql, array($result['message'], $uid));
    }

    private function getProgramByUid($uidprogram) {
        $query = $this->db->get_where('mw_program', array('uid' => $uidprogram, 'Status' => '1'));

        if ($query->num_rows() > 0) {
            $programs = array();
            foreach ($query->result_array() as $key => $value) {
                if ($value['reference'] != '') {
                    $programs['uid'] = $value['uid'];
                    $programs['table'] = $value['reference'];
                    $programs['label'] = $value['name'];
                }
            }
            return $programs;
        }
        return false;
    }

    public function cleanPolygon() {
        $sql = "SELECT
                a.`filezip`,
                a.statusProses,
                a.statusFile
            FROM
                log_survey_plot_polygon_process a
            WHERE a.statusProses = 'Sudah'
            AND a.statusFile IS NULL
            ";
        $query = $this->db->query($sql);
        $dataFilezip = $query->result_array();

        $path = 'files/survey_plot_polygon';
        $path_backup = 'files/polygon/backup';
        $path_failed = 'files/polygon/failed';

        $moved = array();
        foreach ($dataFilezip as $key => $val) {
            if ($val['filezip'] != '') {
                $filename = $val['filezip'];
                if ($val['statusProses'] == 'Sudah') {

                    $source = $path . '/' . $filename;
                    $destination = $path_backup . '/' . $filename;

                    if (file_exists($source)) {
                        $copy = copy($source, $destination);
                        if ($copy) {
                            unlink($source);
                            $this->updateFileStatusPolygon($source);
                            array_push($moved, $source);
                        }
                    } else {
                        echo "<pre>";
                        print_r('file ' . $source . ' not exist');
                        echo "</pre>";
                    }
                }
            }
        }
    }

    public function updateFileStatusPolygon($source) {

        $sql = "UPDATE log_survey_plot_polygon_process SET statusFile = 'backup' WHERE filezip = ?";
        $query = $this->db->query($sql, array($source));
    }

    public function pullMiddlewareData($syncdate = null, $program = null, $orgUnit = null) {

        /*
         * get programs dan org unit berdasar variabel
         */
        $programs = $this->getPrograms($program);
        $orgUnits = $this->getOrgUnits($orgUnit);
        $success = array();

        foreach ($programs as $program) {
            foreach ($orgUnits as $orgUnit) {
                if ($program['table'] != '') {
                    $data = $this->readEvents($syncdate, $orgUnit['uid'], $program['uid']);
//                    echo "<pre>";
//                    print_r($data['events']);
//                    echo "</pre>";exit;
                    if (!empty($data['events'])) {
                        $iterasi = 0;
                        foreach ($data['events'] as $event) {
                            /*
                             * Pengecekan event yang sudah diproses
                             */
                            if ($this->isEventProcessed($event['event'], $event['lastUpdated'])) {
                                continue;
                            }
                            /*
                             * initial variabel
                             */
                            $params = array();
                            $header = array();
                            $body = array();
                            $latitude = '';
                            $longitude = '';


                            if (isset($event['coordinate']['latitude'])) {
                                $latitude = $event['coordinate']['latitude'];
                            }
                            if (isset($event['coordinate']['longitude'])) {
                                $longitude = $event['coordinate']['longitude'];
                            }

                            /*
                             * Susun array header dan value
                             */
                            $VillageID = $MemberUID = $FarmerName = '';
                            foreach ($event['dataValues'] as $key => $value) {
                                $field = $this->getDataElementMapping($program['label'], $program['table'], $value['dataElement']);
                                if ($field) {
                                    if ($value['value'] == "true" OR $value['value'] == "TRUE") {
                                        $value['value'] = '1';
                                    } elseif ($value['value'] == "false" OR $value['value'] == "FALSE") {
                                        $value['value'] = '2';
                                    }
                                    if ($field == 'VillageID' && $program['table'] == 'ktv_members') {
                                        $VillageID = $value['value'];
                                    }

                                    if ($field == 'MemberUID') {
                                        $MemberUID = $value['value'];
                                    }
                                    if ($field == 'MemberName') {
                                        $FarmerName = $value['value'];
                                        if ($program['table'] != 'ktv_members') {
                                            continue;
                                        }
                                    }
                                    $header[] = $field;
                                    $body[] = $value['value'];
                                }
                            }
                            /*
                             * Susun array ke dalam parameter untuk
                             */
                            if (count($header) != 0 && count($body) != 0 && $event['status'] == 'COMPLETED') {
                                //cek apakah uid sudah ada di database atau belum
                                $update = $this->getUID($event['event'], $program['table']);
                                $headerIdentity = $this->getHeaderIdentity($update);
                                $header = array_merge($header, $headerIdentity);

                                $valueIdentity = $this->getValueIdentity($event['completedBy'], $event['created'], $event['lastUpdated'], $update);
                                $body = array_merge($body, $valueIdentity);

                                /*
                                 * tambah parameter koordinat kebun
                                 */
                                $coordinat = 0;
                                if ($longitude && $latitude && $program['table'] == 'ktv_survey_plot_polygon') {
                                    $headerCoordinate = $this->getHeaderCoordinate();
                                    $header = array_merge($header, $headerCoordinate);

                                    $valueCoordinate = $this->getValueCoordinate($latitude, $longitude);
                                    $body = array_merge($body, $valueCoordinate);

                                    $coordinat = 1;
                                }
                                $params[] = $header;
                                $params[] = $body;

                                /*
                                 * Ambil member id 
                                 */
                                if ($program['table'] == 'ktv_members' && $program['uid'] == 'QxauNvjcpBw') {
                                    if (empty($update)) {
                                        /*
                                         * insert
                                         */
                                        $member = $this->mgrower->genMemberID($VillageID, 'F');
                                        if ($member) {
                                            $key1 = array_search('MemberID', $params[0]);
                                            if ($key1 === false) {
                                                array_push($params[0], 'MemberID');
                                                array_push($params[1], $member['MemberID']);
                                            } else {
                                                $params[1][$key1] = $member['MemberID'];
                                            }
                                        }

                                        $key = array_search('MemberDisplayID', $params[0]);
                                        if ($key === false) {
                                            array_push($params[0], 'MemberDisplayID');
                                            array_push($params[1], $member['MemberDisplayID']);
                                        } else {
                                            $params[1][$key] = $member['MemberDisplayID'];
                                        }
                                    } else {
                                        /*
                                         * update
                                         */
                                        $member = $this->getMemberDisplayID($MemberUID, $FarmerName);
                                        $key3 = array_search('MemberID', $params[0]);
                                        if ($key3 === false) {
                                            array_push($params[0], 'MemberID');
                                            array_push($params[1], $member['MemberID']);
                                        } else {
                                            $params[1][$key3] = $member['MemberID'];
                                        }
                                    }
                                    $key2 = array_search('MemberUID', $params[0]);
                                    if ($key2 === false) {
                                        array_push($params[0], 'MemberUID');
                                        array_push($params[1], $MemberUID);
                                    } else {
                                        $params[1][$key2] = $MemberUID;
                                    }
                                } else {
                                    $member = $this->getMemberDisplayID($MemberUID, $FarmerName);
                                    $key3 = array_search('MemberID', $params[0]);
                                    if ($key3 === false) {
                                        array_push($params[0], 'MemberID');
                                        array_push($params[1], $member['MemberID']);
                                    } else {
                                        $params[1][$key3] = $member['MemberID'];
                                    }
                                }
                                /*
                                 * tambah parameter interview Date
                                 */
                                if ($program['table'] == 'ktv_survey_plot') {
                                    $params = $this->generateCloneAndShadeTrees($params);
                                }
                                if (!$update) {
                                    $params = $this->generateInterviewDate($params, $program['table'], $event['eventDate']);
                                }
                                /*
                                 * tambah parameter uid
                                 */
                                $params = $this->generateUID($params, $event['event']);


                                /*
                                 * proses insert ke database web
                                 */
                                if ($coordinat) {
                                    $this->updateCoordinate($params);
                                    continue;
                                }

                                if ($program['uid'] == 'S4bVIIEGCuT') {
                                    $this->updateConsentLetter($params);
                                    continue;
                                }

                                if ($program['uid'] == 'nQxNqbkCil1') {
                                    $params = $this->addHarvestMonthParameter($params);
                                    $params = $this->calculateProduction($params);
                                }
                                $result = $this->insertData($params, $program['table'], $update);
                                if ($result['status']) {
                                    if ($program['table'] == 'ktv_members' && !empty($member)) {
                                        $this->insertMemberRole($member, $update);
                                        $this->insertMemberPartner($member, $VillageID);
                                    }
                                    $success[$event['completedBy']][$event['program']][$iterasi]['results'] = $result;
                                } else {
                                    $this->InsertErrorLog($result, $event['event']);
                                }
                                $this->processEvent($event['event'], $event['lastUpdated']);
                            }
                            $iterasi++;
                        }
                    }
                }
            }
        }
        if (count($success) > 0)
//            $this->sendEmailNotification($success, true);
            return true;
    }

    private function addHarvestMonthParameter($params) {
        $january = $this->array_search('LeanHarvestSeasonJan', $params);
        $febuary = $this->array_search('LeanHarvestSeasonFeb', $params);
        $march = $this->array_search('LeanHarvestSeasonMar', $params);
        $april = $this->array_search('LeanHarvestSeasonApr', $params);
        $may = $this->array_search('LeanHarvestSeasonMay', $params);
        $june = $this->array_search('LeanHarvestSeasonJun', $params);
        $july = $this->array_search('LeanHarvestSeasonJul', $params);
        $august = $this->array_search('LeanHarvestSeasonAug', $params);
        $september = $this->array_search('LeanHarvestSeasonSep', $params);
        $october = $this->array_search('LeanHarvestSeasonOct', $params);
        $november = $this->array_search('LeanHarvestSeasonNov', $params);
        $december = $this->array_search('LeanHarvestSeasonDec', $params);

        $NrLowSeasonMonths = $january + $febuary + $march + $april + $march + $june + $july + $august + $september + $october + $november + $december;
        $NrHighSeasonMonths = 12 - $NrLowSeasonMonths;

        array_push($params[0], 'NrHighSeasonMonths', 'NrLowSeasonMonths');
        array_push($params[1], $NrHighSeasonMonths, $NrLowSeasonMonths);

        return $params;
    }

    private function InsertErrorLog($result, $uid) {

        $exist = $this->checkExistingUid($uid);

        if ($exist === false) {
            /*
             * insert
             */
            $this->db->set('error_query_uid', $uid);
            $this->db->set('error_query_query', $result['query']);
            $this->db->set('error_query_message', $result['message']);
            $this->db->set('error_query_date', date("Y-m-d H:i:s"));
            $result = $this->db->insert('mw_error_query');
        } else {
            /*
             * update
             */
            $data = array(
                'error_query_query' => $result['query'],
                'error_query_message' => $result['message'],
                'error_query_date' => date("Y-m-d H:i:s")
            );
            $this->db->where('error_query_uid', $uid);
            $this->db->update('mw_error_query', $data);
        }
    }

    private function checkExistingUid($uid) {
        $this->db->select('error_query_uid');
        $this->db->from('mw_error_query');
        $this->db->where("error_query_uid = '$uid'", NULL, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return true;
        } else {
            return false;
        }
    }

    public function SyncUserLogin() {
        $event = $this->getLoginEvents();
        $excluded_user = array(
            'mw1 Demo',
            'admin admin'
        );

        if ($event) {
            $success = $failed = 0;
            foreach ($event['users'] as $key => $val) {
                $params = array();
                if (!in_array($val['userCredentials']['name'], $excluded_user)) {
                    $user_id = $this->getUserID($val['userCredentials']['name']);
                    $timestamp = date('Y-m-d H:i:s', strtotime($val['userCredentials']['lastLogin']));
                    if ($user_id) {
                        $params = array(
                            'user_id' => $user_id,
                            'timestamp' => $timestamp
                        );
                        $result = $this->processLoginHistory($params);
                        $result = $this->UpdateUserLoginFlag($user_id);
                    }
                    if ($result['success']) {
                        $success++;
                    } else {
                        $failed++;
                    }
                }
            }
            $results['success'] = true;
            $results['message'] = "success : " . $success;
        } else {
            $results['success'] = true;
            $results['message'] = "No Event";
        }
        return $results;
    }

    public function getLoginEvents() {
        // compose url

        $date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
//        $date = '2016-01-01';
        $base_url = $this->config->item('dhis_url') . 'api/users';

        $params = array();
        $params[] = "fields=userCredentials[name,lastLogin]";
        $params[] = "filter=userCredentials.lastLogin:gt:{$date}";
        $params[] = "pageSize=1000";
        $url = $base_url . '?' . implode('&', $params);

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

    public function processLoginHistory($params) {
        $sql = "INSERT INTO sys_log_access (type,UserID,AttempProcess,application,Timestamp) VALUES (?,?,?,?,?)";
        $query = $this->db->query($sql, array('Login', $params['user_id'], 'Success', 'mobile', $params['timestamp']));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function UpdateUserLoginFlag($user_id) {
        $sql = "UPDATE sys_user a SET UserTorStatus = 1 WHERE UserID = ? AND a.UserTorStatus != 1";
        $query = $this->db->query($sql, array($user_id));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    private function calculateProduction($params) {
        $GardenAreaHa = $this->array_search('GardenAreaHa', $params);

        $HarvestRateDaysHighSeason = $this->array_search('HarvestRateDaysHighSeason', $params);
        $AverageProdHighSeason = $this->array_search('AverageProdHighSeason', $params);
        $NrHighSeasonMonths = $this->array_search('NrHighSeasonMonths', $params);

        $HarvestRateDaysLowSeason = $this->array_search('HarvestRateDaysLowSeason', $params);
        $AverageProdLowSeason = $this->array_search('AverageProdLowSeason', $params);
        $NrLowSeasonMonths = $this->array_search('NrLowSeasonMonths', $params);

        $HighSeasonProduction = 30 / $HarvestRateDaysHighSeason * $AverageProdHighSeason * $NrHighSeasonMonths;
        $LowSeasonProduction = 30 / $HarvestRateDaysLowSeason * $AverageProdLowSeason * $NrLowSeasonMonths;

        $AnnualProduction = $HighSeasonProduction + $LowSeasonProduction;

        $PlantationProductivity = round($AnnualProduction / $GardenAreaHa, 2);

        array_push($params[0], "HighSeasonProduction", "LowSeasonProduction", "AnnualProduction", "PlantationProductivity");
        array_push($params[1], $HighSeasonProduction, $LowSeasonProduction, $AnnualProduction, $PlantationProductivity);

        return $params;
    }

    private function getMemberID($memberdisplayid, $tabel) {
        $whitelist = array(
            'ktv_member_family_labour',
            'ktv_member_other_land',
            'ktv_member_plot_status',
            'ktv_survey_household',
            'ktv_survey_main_buyer',
            'ktv_survey_plot'
        );
        if (in_array($tabel, $whitelist)) {
            $this->db->select('MemberID', false);
            $this->db->from('ktv_members a');
            $this->db->where("MemberDisplayID = '$memberdisplayid'", NULL, FALSE);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                return $result[0]['MemberID'];
            }
        }
        return false;
    }

    private function insertMemberRole($member, $update) {
        $result = false;
        if (empty($update)) {
            $this->db->set('MemberID', $member['MemberID']);
            $this->db->set('MRoleID', '1');
            $this->db->set('DateCreated', date("Y-m-d H:i:s"));
            $this->db->set('DateSync', date("Y-m-d"));
            $result = $this->db->insert('ktv_member_role');
        }
        if ($result) {
            return true;
        }
        return $result;
    }

    private function insertMemberPartner($member, $VillageID) {
        $result = false;
        if ($VillageID) {

            $partner_id = $this->getPartnerID($VillageID);

            if (count($partner_id > 0)) {
                foreach ($partner_id as $key => $val) {
                    $result = $this->insertPartnerMember($member['MemberID'], $val['PartnerID']);
                }
            }

            if ($result) {
                return true;
            }
        }
        return $result;
    }

    private function getPartnerID($village_id) {
        $this->db->select('DistrictID,PartnerID');
        $this->db->from('ktv_district_partner');
        $this->db->where("DistrictID = SUBSTR('$village_id',1,4)", NULL, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
    }

    private function InsertPartnerMember($member_id, $partner_id) {
        $this->db->set('apmPartnerID', $partner_id);
        $this->db->set('apmMemberID', $member_id);
        $this->db->set('CreatedBy', 1);
        $this->db->set('DateCreated', date("Y-m-d H:i:s"));
        $result = $this->db->replace('ktv_access_partner_member');

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    private function array_search($params, $array) {
        $key = array_search($params, $array[0]);
        if ($key === false) {
            $data = '';
        } else {
            $data = $array[1][$key];
        }
        return $data;
    }

    private function insertMemberExtension($member, $params, $uid, $completedBy, $created, $lastUpdated) {
        $update = $this->getUID($uid, 'ktv_members_extension');

        $headerIdentity = $this->getHeaderIdentity($update);
        $valueIdentity = $this->getValueIdentity($completedBy, $created, $lastUpdated, $update);

        $identity[] = $headerIdentity;
        $identity[] = $valueIdentity;

        $frChildrenCount = $this->array_search('frChildrenCount', $params);
        $frChildrenSchool = $this->array_search('frChildrenSchool', $params);
        $frChildrenWorkInFarm = $this->array_search('frChildrenWorkInFarm', $params);
        $frChildrenUnderAgeWork = $this->array_search('frChildrenUnderAgeWork', $params);
        $frChildrenTypeOfWork = $this->array_search('frChildrenTypeOfWork', $params);

        $CreatedBy = $this->array_search('CreatedBy', $identity);
        $DateCreated = $this->array_search('DateCreated', $identity);
        $DateUpdated = $this->array_search('DateUpdated', $identity);
        $LastModifiedBy = $this->array_search('LastModifiedBy', $identity);

        $result = false;
        if (empty($update)) {
            $this->db->set('MemberID', $member['MemberID']);
            $this->db->set('frChildrenCount', $frChildrenCount);
            $this->db->set('frChildrenSchool', $frChildrenSchool);
            $this->db->set('frChildrenWorkInFarm', $frChildrenWorkInFarm);
            $this->db->set('frChildrenUnderAgeWork', $frChildrenUnderAgeWork);
            $this->db->set('frChildrenTypeOfWork', $frChildrenTypeOfWork);

            $this->db->set('CreatedBy', $CreatedBy);
            $this->db->set('DateCreated', $DateCreated);
            $this->db->set('LastModifiedBy', $LastModifiedBy);
            $this->db->set('DateUpdated', $DateUpdated);
            $this->db->set('DateSync', date("Y-m-d"));
            $this->db->set('uid', $uid);
            $result = $this->db->insert('ktv_members_extension');
        } else {
            $data = array(
                'frChildrenCount' => $frChildrenCount,
                'frChildrenSchool' => $frChildrenSchool,
                'frChildrenWorkInFarm' => $frChildrenWorkInFarm,
                'frChildrenUnderAgeWork' => $frChildrenUnderAgeWork,
                'frChildrenTypeOfWork' => $frChildrenTypeOfWork,
                'LastModifiedBy' => $LastModifiedBy,
                'DateUpdated' => $DateUpdated,
                'DateSync' => date("Y-m-d")
            );
            $this->db->where('uid', $uid);
            $result = $this->db->update('ktv_members_extension', $data);
        }
        if ($result) {
            return true;
        }
        return $result;
    }

    private function sendEmailNotification($success, $swisscontact_gis) {
        if (count($success) > 0) {
            $data = array();
            foreach ($success as $key => $val) {

                /*
                 * perulangan user
                 */
                $i = 0;
                $sync = array();
                foreach ($val as $k => $v) {
                    /*
                     * perulangan program/tabel
                     */
                    $sync[$i]['form'] = $this->getForm($k);
                    $sync[$i]['total'] = count($v);
                    $i++;
                }

                $date = date('Y-m-d H:i');
                $user = $this->getUser($key);
                $str = '';
                if ($user) {
                    if ($user['Gender'] == 'm') {
                        $gender = "Bapak";
                    } elseif ($user['Gender'] == 'f') {
                        $gender = "Ibu";
                    } else {
                        $gender = '';
                    }
                    /*
                     * Susun isi email
                     */
                    $str .= 'Yth. ' . $gender . ' ' . $user['PersonNm'] . ',<br/><br/>';
                    $str .= 'Anda telah melakukan sinkronisasi data pada tanggal ' . $date . '<br/> Berikut rincian data sinkronisasi : <br/>';
                    if (count($sync) > 0) {
                        foreach ($sync as $k => $v) {
                            $label = 'Data ' . $v['form'];
                            $str .= $label . ' = ' . $v['total'] . ' data<br/>';
                        }
                    }

                    $str .= "<br/>Harap tidak membalas email ini karena terkirim secara otomatis oleh sistem<br/>";
                    $str .= "<br/>Demikian disampaikan, atas perhatian $gender kami ucapkan terima kasih.<br/>";
                    $str .= "<br/>Salam Hangat,<br/>";
                    $str .= "<br/><br/>";
                    $str .= "<br/>&copy; Cocoa Trace.<br/>";

                    /*
                     * proses kirim email
                     */
                    if (!filter_var($user['OfficialEmail'], FILTER_VALIDATE_EMAIL) === false) {
                        $this->load->library('email');
                        $this->email->initialize($this->config->load('email'));
                        $this->email->from('support@koltiva.com', 'Koltiva Support');

                        $is_polygon = false;
                        foreach ($val as $k => $v) {
                            foreach ($v as $keys => $vals) {
                                if (isset($vals['kml'])) {
                                    $is_polygon = true;
                                    if (file_exists('./files/kml/' . $vals['kml'])) {
                                        $this->email->attach("./files/kml/" . $vals['kml']);
                                    }
                                }
                            }
                        }
                        $this->email->to($user['OfficialEmail']);
//                        $this->email->to('noersa.eka@gmail.com');

                        $gis_mail = array();
                        if ($is_polygon) {
                            if ($swisscontact_gis) {
                                $gis = $this->getGISMail();
                                if ($gis) {
                                    $gis_mail = explode(' ', $gis);
                                }
                            }
                        }

                        array_push($gis_mail, 'info@koltiva.com');
                        $this->email->cc($gis_mail);
                        $this->email->subject($user['PersonNm'] . ' - Sync : ' . $date);
//                        $this->email->subject('Maaf, testing bounce email untuk kml polygon');
                        $this->email->message($str);
                        $this->email->send();
                    }
                }
                $this->insertSyncLog($user['UserID'], $date, $str);
            }
        }
    }

    private function getGISMail() {
        $sql = "SELECT a.SetValue FROM sys_setting a WHERE a.SetKey = 'gis_email_account'";
        $query = $this->db->query($sql, array());
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['SetValue'];
        }
        return false;
    }

    private function insertSyncLog($user_id, $date_sync, $content) {
        $sql = "INSERT INTO sys_log_sync (UserId,MachineID,Content,DateFrom,Timestamp,Source) VALUES (?,null,?,?,?,?)";
        $query = $this->db->query($sql, array($user_id, $content, $date_sync, date('Y-m-d H:i:s'), '2'));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateCoordinate($params) {
        $key = array_search('MemberID', $params[0]);
        if ($key === false) {
            $MemberID = false;
        } else {
            $MemberID = $params[1][$key];
        }
        $key = array_search('SurveyNr', $params[0]);
        if ($key === false) {
            $SurveyNr = false;
        } else {
            $SurveyNr = $params[1][$key];
        }
        $key = array_search('PlotNr', $params[0]);
        if ($key === false) {
            $PlotNr = false;
        } else {
            $PlotNr = $params[1][$key];
        }
        $key = array_search('Latitude', $params[0]);
        if ($key === false) {
            $Latitude = false;
        } else {
            $Latitude = $params[1][$key];
        }
        $key = array_search('Longitude', $params[0]);
        if ($key === false) {
            $Longitude = false;
        } else {
            $Longitude = $params[1][$key];
        }

        $sql = "UPDATE ktv_survey_plot SET Latitude = ?, Longitude = ? WHERE MemberID = ? and SurveyNr = ? AND PlotNr = ?";
        $query = $this->db->query($sql, array($Latitude, $Longitude, $MemberID, $SurveyNr, $PlotNr));
        if ($query) {
            $results = true;
        } else {
            $results = false;
        }
    }

    public function updateConsentLetter($params) {
        $key = array_search('MemberID', $params[0]);
        if ($key === false) {
            $MemberID = null;
        } else {
            $MemberID = $params[1][$key];
        }
        $key = array_search('WithdrawalConsentStatus', $params[0]);
        if ($key === false) {
            $WithdrawalConsentStatus = null;
        } else {
            $WithdrawalConsentStatus = $params[1][$key];
        }
        $key = array_search('LearningContractStatus', $params[0]);
        if ($key === false) {
            $LearningContractStatus = null;
        } else {
            $LearningContractStatus = $params[1][$key];
        }
        $sql = "UPDATE ktv_members SET LearningContractStatus = ?, WithdrawalConsentStatus = ? WHERE MemberID = ?";
        $query = $this->db->query($sql, array($LearningContractStatus, $WithdrawalConsentStatus, $MemberID));
        if ($query) {
            $results = true;
        } else {
            $results = false;
        }
    }

    private function buildPolygonData($params) {
        $coordinates = $farmer_id = $garden_nr = $survey_nr = '';
        foreach ($params as $key => $val) {
            foreach ($val as $k => $v) {
                if ($v == 'FarmerID') {
                    $farmer_id = $params[1][$k];
                } else if ($v == 'GardenNr') {
                    $garden_nr = $params[1][$k];
                } else if ($v == 'SurveyNr') {
                    $survey_nr = $params[1][$k];
                } else if ($v == 'GardenAreaCoordinates') {
                    $coordinates = $params[1][$k];
                } else if ($v == 'CreatedBy') {
                    $created_by = $params[1][$k];
                } else if ($v == 'DateCreated') {
                    $date_created = $params[1][$k];
                } else if ($v == 'DateUpdated') {
                    $date_updated = $params[1][$k];
                } else if ($v == 'LastModifiedBy') {
                    $lastmodified_by = $params[1][$k];
                } else if ($v == 'DateSync') {
                    $date_sync = $params[1][$k];
                }
            }
        }
        $data = array();
        if ($coordinates) {
            /*
             * kalau coordinates di encode, decode dulu
             */
            if (base64_encode(base64_decode($coordinates)) === $coordinates) {
                $data_coordinates = base64_decode($coordinates);
                $coordinates = gzdecode($data_coordinates);
            }
            /*
             * contoh value coordinates = -6.263904 106.778044 49 7.0::-6.263872 106.777996 45 8.0::-6.263767 106.778021 90 4.0::
             */
            if ($coordinates) {
                $coordinate = explode('::', $coordinates);
                foreach ($coordinate as $key => $val) {
                    $data[$key] = explode(' ', $val);
                }
                /*
                 * unset array terakhir karena berisi array kosong hasil explode('::')
                 */
                unset($data[count($data) - 1]);
            }
        }
        if (count($data) != 0) {
            $revision = $this->getLastPolygonRevision($farmer_id, $garden_nr, $survey_nr);
            $identity = compact("farmer_id", "garden_nr", "survey_nr", "revision", "created_by", "date_created", "date_updated", "lastmodified_by", "date_sync");
            /*
             * delete polygon jika udah ada
             */
//            $this->deleteDuplicatePolygon($identity);
            /*
             * insert polygon baru
             */
            $polygon = $this->insertPolygon($data, $identity);
            /*
             * update tabel garden dengan luas polygon
             */
            $this->inputGardenHaPolygon($farmer_id);
            if ($polygon) {
                $kml = $this->KMLGenerator($farmer_id, $survey_nr, $garden_nr, $data);
                if ($kml['success'] == true) {
                    return $kml['kmlname'];
                } else {
                    return false;
                }
            }
        }
    }

    private function getLastPolygonRevision($farmer_id, $garden_nr, $survey_nr) {
        $sql = "SELECT MAX(IFNULL(a.`Revision`,0)) AS max_revision FROM ktv_farmer_garden_area a WHERE a.FarmerID = ? AND a.GardenNr = ? and a.SurveyNr = ?";
        $query = $this->db->query($sql, array($farmer_id, $garden_nr, $survey_nr));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['max_revision'] + 1;
        }
        return 0;
    }

    private function deleteDuplicatePolygon($identity) {
        extract($identity);
        $sql = "DELETE FROM ktv_farmer_garden_area WHERE FarmerID = ? AND GardenNr = ? AND SurveyNr = ?";
        $this->db->query($sql, array($farmer_id, $garden_nr, $survey_nr));
    }

    private function insertPolygon($data, $identity) {
        extract($identity);
        if (!$date_created) {
            $date_created = $dateupdated;
        }
        if (!$created_by) {
            $created_by = $lastmodified_by;
        }
        $sql = "INSERT INTO ktv_farmer_garden_area (FarmerID, GardenNr, SurveyNr, OrderNr, Latitude, Longitude, Altitude, Accuracy, Revision, Status, DateCreated, CreatedBy, DateUpdated, LastModifiedBy, DateSync) VALUES ";
        foreach ($data as $key => $val) {
            $order_nr = $key + 1;
            $latitude = $val[0];
            $longitude = $val[1];
            $altitude = $val[2];
            $accuracy = $val[3];
            $sql .= "(" . $farmer_id . "," . $garden_nr . "," . $survey_nr . "," . $order_nr . ",'" . $latitude . "','" . $longitude . "','" . $altitude . "','" . $accuracy . "','" . $revision . "','new','" . $date_created . "','" . $created_by . "','" . $date_updated . "','" . $lastmodified_by . "','" . $date_sync . "')";
            if (isset($data[$key + 1])) {
                $sql .= ",";
            }
        }
        $query = $this->db->query($sql, array());
        if ($query) {
            $results = true;
        } else {
            $results = false;
        }
        return $results;
    }

    public function KMLGenerator($farmer_id, $survey_nr, $garden_nr, $data) {
        $farmer = $this->getDetailFarmer($farmer_id, $survey_nr, $garden_nr);
        if ($farmer) {
            $kml = array('<?xml version="1.0" encoding="UTF-8"?>
            <kml xmlns="http://www.opengis.net/kml/2.2">
            <Document id="root_doc">
            <Schema name="Polygon" id="Polygon_FarmerID">
                <SimpleField name="FARMERID" type="float"></SimpleField>
                <SimpleField name="FARMERNAME" type="string"></SimpleField>
                <SimpleField name="GARDENNR" type="int"></SimpleField>
                <SimpleField name="SURVEYNR" type="float"></SimpleField>
                <SimpleField name="X" type="float"></SimpleField>
                <SimpleField name="Y" type="float"></SimpleField>
                <SimpleField name="TGLMASUK" type="string"></SimpleField>
                <SimpleField name="AREA_HA" type="float"></SimpleField>
                <SimpleField name="KETERANGAN" type="string"></SimpleField>
            </Schema>
            <Folder><name>Polygon</name>');

            $kml[] = '
                <Placemark>
                    <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
                        <ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
                            <SimpleData name="FARMERID">' . $farmer['FarmerID'] . '</SimpleData>
                            <SimpleData name="FARMERNAME">' . $farmer['FarmerName'] . '</SimpleData>
                            <SimpleData name="GARDENNR">' . $farmer['GardenNr'] . '</SimpleData>
                            <SimpleData name="SURVEYNR">' . $farmer['SurveyNr'] . '</SimpleData>
                            <SimpleData name="X">' . $farmer['Longitude'] . '</SimpleData>
                            <SimpleData name="Y">' . $farmer['Latitude'] . '</SimpleData>
                            <SimpleData name="AREA_HA">' . $farmer['GardenHaUnCertified'] . '</SimpleData>
                        </SchemaData></ExtendedData>
                    <Polygon><altitudeMode>relativeToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>relativeToGround</altitudeMode><coordinates>';

            // Iterates through the rows, printing a node for each row.
            if ($data) {
                foreach ($data as $key => $val) {
                    // $val[1] = longitude
                    // $val[0] = latitude
                    $kml[] = $val[1] . ',' . $val[0];
                }
            }
            // End XML file
            $kml[] = '</coordinates></LinearRing></outerBoundaryIs></Polygon>
                </Placemark>';

            $kml[] = '</Folder>
            </Document></kml>';
            $kmlOutput = join(" ", $kml);

            $name = "{$farmer_id}";
            if ($garden_nr !== false) {
                $name .= '-' . $garden_nr;
            }
            if ($survey_nr !== false) {
                $name .= '-' . $survey_nr;
            }
            $name .= '.kml';
            $this->load->helper('file');
            if (!write_file('./files/kml/' . $name, $kmlOutput)) {
                $result['success'] = false;
            } else {
                $result['success'] = true;
                $result['kmlname'] = $name;
            }
            return $result;
//            force_download($name, $kmlOutput);
        }
    }

    public function getKMLCoordinates($farmer_id, $gardenNr, $survey_nr) {
        $sql = "SELECT
                a.FarmerID,
                b.FarmerName,
                a.GardenNr,
                d.SurveyNr,
                a.OrderNr,
                a.Latitude,
                a.Longitude,
                c.GardenHaUnCertified
              FROM
                ktv_farmer_garden_area a
                LEFT JOIN ktv_farmer b
                  ON a.FarmerID = b.FarmerID
                LEFT JOIN ktv_farmer_garden c
                  ON c.FarmerID = a.FarmerID
                  AND c.GardenNr = a.GardenNr
                  AND a.SurveyNr = c.SurveyNr
                LEFT JOIN
                  (SELECT
                    FarmerID,
                    GardenNr,
                    MAX(SurveyNr) AS SurveyNr
                  FROM
                    ktv_farmer_garden_area
                  GROUP BY GardenNr, FarmerID) AS d
                  ON a.FarmerID = d.FarmerID
              WHERE a.FarmerID = ?
                AND a.GardenNr = ?
                AND a.SurveyNr = ?
            ";

        $query = $this->db->query($sql, array($farmer_id, $gardenNr, $survey_nr));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    private function getUID($event, $table) {
        $sql = "SELECT DateCreated,CreatedBy,uid FROM $table WHERE uid = ?";
        $query = $this->db->query($sql, array($event));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;
    }

    private function getDetailFarmer($farmer_id, $survey_nr = null, $garden_nr = null) {
        $where = '';
        $params[] = $farmer_id;
        $sql = "SELECT
                    b.FarmerID,
                    b.FarmerName,
                    c.GardenNr,
                    c.SurveyNr,
                    c.Longitude,
                    c.Latitude,
                    c.GardenHaUnCertified
                  FROM
                     ktv_farmer b
                  LEFT JOIN ktv_farmer_garden c ON c.FarmerID = b.FarmerID
                  WHERE b.FarmerID = ?
                  --where--
                  GROUP BY b.FarmerID
            ";
        if (!empty($garden_nr)) {
            $where .= ' AND c.GardenNr = ?';
            $params[] = $garden_nr;
        }
        if (!empty($survey_nr)) {
            $where .= ' AND c.SurveyNr = ?';
            $params[] = $survey_nr;
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array()[0];
            return $result;
        }
        return false;
    }

    private function generateCloneAndShadeTrees($params) {
        $data = $params;
        $clone = array(
            'TypePlantMateMarihatNr',
            'TypePlantMateDumpyNr',
            'TypePlantMateLonsumNr',
            'TypePlantMateSimalungunNr',
            'TypePlantMateDanimasNr',
            'TypePlantMateSriwijayaNr',
            'TypePlantMateSocfinNr',
            'TypePlantMateOtherNr',
            'TypePlantMateDoNotKnowNr'
        );

        foreach ($params[0] as $key => $val) {
            if ($params[1][$key] == 0) {
                $value = 0;
            } else {
                $value = 1;
            }
            if (in_array($val, $clone)) {
                $var = explode('Nr', $val);
                array_push($data[0], $var[0]);
                array_push($data[1], $value);
            }
        }

        return $data;
    }

    private function getMemberDisplayID($uid, $FarmerName) {
        $this->db->select('MemberID, MemberDisplayID ', false);
        $this->db->from('ktv_members a');
        $this->db->where("MemberUID = '$uid' OR MemberDisplayID = '$uid'", NULL, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;
    }

    private function generateSurveyFinancialBaseline($params) {
        $data = $params;

        if (!in_array('SurveyNr', $data[0])) {
            array_push($data[0], 'SurveyNr');
            array_push($data[1], '0');
        }

        return $data;
    }

    private function generateGardenAreaChecklist($params) {
        $data = $params;

        if (in_array('KebunArea', $data[0])) {
            array_push($data[0], 'IsFamilyGarden');
            array_push($data[1], '1');
        } else {
            array_push($data[0], 'IsFamilyGarden');
            array_push($data[1], '2');
        }

        if (in_array('ComKebunArea', $data[0])) {
            array_push($data[0], 'IsCommmercialGarden');
            array_push($data[1], '1');
        } else {
            array_push($data[0], 'IsCommmercialGarden');
            array_push($data[1], '2');
        }

        return $data;
    }

    private function generateInterviewDate($params, $table, $eventDate) {
        $data = $params;
        $tabel_interview = array();
        $tabel_collection = array(
            'ktv_members',
            'ktv_survey_plot',
            'ktv_survey_main_buyer',
            'ktv_survey_household',
            'ktv_survey_finance',
            'ktv_survey_trader'
        );

        if (in_array($table, $tabel_interview)) {
            array_push($data[0], 'InterviewDate');
            array_push($data[1], date("Y-m-d H:i:s", strtotime($eventDate)));
        }
        if (in_array($table, $tabel_collection)) {
            array_push($data[0], 'DateCollection');
            array_push($data[1], date("Y-m-d H:i:s", strtotime($eventDate)));
        }

        return $data;
    }

    private function generateUID($params, $uid) {
        $data = $params;

        array_push($data[0], 'uid');
        array_push($data[1], $uid);

        return $data;
    }

    private function getHeaderIdentity($exist) {
        if ($exist['uid']) {
            return array(
                'DateUpdated',
                'LastModifiedBy',
                'DateSync'
            );
        } else {
            return array(
                'CreatedBy',
                'DateCreated',
                'DateUpdated',
                'LastModifiedBy',
                'DateSync'
            );
        }
    }

    private function getValueIdentity($username, $createDate, $updateDate, $exist) {
        $user_id = $this->getUserId($username);
        if (!$user_id) {
            $user_id = '1'; // kalo user id ga ditemukan, diinput sebagai admin
        }

        $createdBy = $user_id;
        if ($createDate) {
            $createdDate = date('Y-m-d H:i:s', strtotime($createDate));
        } else {
            $createdDate = date('Y-m-d H:i:s');
        }

        $updatedBy = $user_id;
        if ($updateDate) {
            $updatedDate = date('Y-m-d H:i:s', strtotime($updateDate));
        } else {
            $updatedDate = date('Y-m-d H:i:s');
        }

        // Jika Data sudah ada di database, ambil created by dan date created data data existing
        if ($exist['uid']) {
            if ($exist['CreatedBy']) {
                $createdBy = $exist['CreatedBy'];
            }
            if ($exist['DateCreated'] && $exist['DateCreated'] != '0000-00-00 00:00:00') {
                $createdDate = $exist['DateCreated'];
            }
        }

        if ($exist['uid']) {
            return array(
                $updatedDate,
                $updatedBy,
                date('Y-m-d')
            );
        } else {
            return array(
                $createdBy,
                $createdDate,
                $updatedDate,
                $updatedBy,
                date('Y-m-d')
            );
        }
    }

    private function getHeaderCoordinate() {
        return array(
            'Latitude',
            'Longitude'
        );
    }

    private function getValueCoordinate($latitude, $longitude) {
        return array(
            $latitude,
            $longitude
        );
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

    private function getUser($username) {
        $this->db->select('a.*, b.*', false);
        $this->db->from('sys_user a');
        $this->db->join('ktv_persons b', 'a.UserID = b.UserID');
        $this->db->where("username = '$username'", NULL, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;
    }

    private function getForm($uid) {
        $this->db->select('a.name', false);
        $this->db->from('mw_program a');
        $this->db->where("uid = '$uid'", NULL, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['name'];
        }
        return false;
    }

    private function getPrograms($program = null) {
        if ($program) {
            $program = str_replace("_", " ", $program);
            $query = $this->db->get('mw_program');
            $this->db->select('a.*');
            $this->db->from('mw_program a');
            $this->db->where("a.name = '$program'", NULL, FALSE);
            $this->db->where("a.Status = '1'", NULL, FALSE);
            $this->db->order_by("a.order", "asc");
            $query = $this->db->get();
        } else {
            $this->db->select('*');
            $this->db->from('mw_program');
            $this->db->where("Status = '1'");
            $this->db->order_by("order", "asc");
            $query = $this->db->get();
        }
        if ($query->num_rows() > 0) {
            $programs = array();
            foreach ($query->result_array() as $key => $value) {
                if ($value['reference'] != '') {
                    $programs[$key]['uid'] = $value['uid'];
                    $programs[$key]['table'] = $value['reference'];
                    $programs[$key]['label'] = $value['name'];
                }
            }
            return $programs;
        }
        return false;
    }

    private function readEvents($syncdate, $orgUnit, $program, $startDate = '', $endDate = '', $currentDate = '') {
        // set default date, if not specified
        if (empty($startDate)) {
            $startDate = date('Y-m-d');
            // $startDate = '2016-01-01';
        } else {
            $startDate = date('Y-m-d', strtotime($startDate));
        }
        if (empty($endDate)) {
            $endDate = date('Y-m-d');
            // $endDate = '2016-12-31';
        } else {
            $endDate = date('Y-m-d', strtotime($endDate));
        }
        if (empty($currentDate)) {
            $currentDate = date('Y-m-d');
        } else {
            $currentDate = date('Y-m-d', strtotime($currentDate));
        }

        // compose url
//        $rooturl = explode('/', $this->config->item('base_url'));
//        if ($rooturl[2] == 'app.palmoiltrace.com') {
//            $base_url = $this->config->item('dhis_url') . 'api/events.json';
//        } else if ($rooturl[2] == 'demo.palmoiltrace.com') {
//            $base_url = $this->config->item('dhis_url_demo') . 'api/events.json';
//        }

        $base_url = $this->config->item('dhis_url') . 'api/events.json';

        $params = array();
        $params[] = "orgUnit={$orgUnit}";
        $params[] = "program={$program}";
//        $params[] = "startDate={$startDate}";
//        $params[] = "endDate={$endDate}";
//        $params[] = "lastUpdated={$currentDate}";
        if ($syncdate) {
            $params[] = "lastUpdated={$syncdate}";
        } else {
            $params[] = "lastUpdated={$currentDate}";
        }
//        $params[] = "lastUpdated=2016-12-23";
        $params[] = "skipPaging=true";
        $params[] = "status=COMPLETED";
        $url = $base_url . '?' . implode('&', $params);
        echo "<pre>";
        print_r($url);
        echo "</pre>";
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


// Execute - returns responce
        $response = $this->curl->execute();
        return json_decode($response, true);
    }

    private function getOrgUnits($orgUnit = null) {
        if ($orgUnit) {
            $orgUnit = str_replace("_", " ", $orgUnit);
            $this->db->select('*');
            $this->db->from('mw_organisationunit a');
            $this->db->join('ktv_district b', 'a.name = b.District');
            $this->db->where('a.name', $orgUnit);
            $this->db->where('a.Status', '1');
            $query = $this->db->get();
        } else {
            $this->db->select('*');
            $this->db->from('mw_organisationunit a');
            $this->db->join('ktv_district b', 'a.name = b.District');
            $this->db->where('a.Status', '1');
            $query = $this->db->get();
        }
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    private function getDataElementMapping($program, $table, $key) {
//        $map = array(
//            'ktv_farmer' => array(
//                'WSgxCBozxdW' => 'FarmerID',
//                'hgRo1qiF4rQ' => 'FarmerName',
//                'O1UIrn5Fgnr' => 'Birthdate',
//                'HPWIySnKrLy' => 'HandPhone',
//                'HMTQaZJXSYt' => 'Address',
//                'x2kdQFPcoJT' => 'Education',
//                'EGUGv8PsqON' => 'MaritalStatus',
//                'qwhmvER1Moy' => 'AccountNumber',
//                'IjQeJBEoVWf' => 'BankName',
//                'x9BFASr6QF1' => 'Gender',
//            ),
//        );
        $map = $this->getMapping($program);
        if (!empty($map[$table][$key])) {
            return $map[$table][$key];
        }
        return false;
    }

    private function getMapping($program) {
        $this->db->select('a.name,a.reference AS ref_table,b.name,d.uid,d.formname,c.reference_field AS ref_field', false);
        $this->db->from('mw_program a');
        $this->db->join('mw_programstage b', 'a.programid = b.programid', 'left');
        $this->db->join('mw_programstagedataelement c', 'c.programstageid = b.programstageid', 'left');
        $this->db->join('mw_dataelement d', 'd.dataelementid = c.dataelementid', 'left');
        $this->db->where('a.reference IS NOT NULL AND c.reference_field IS NOT NULL', NULL, FALSE);
        $this->db->where("a.name = '$program'", NULL, FALSE);
//        $this->db->order_by('a.name');

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $mapping = array();
            foreach ($query->result_array() as $key => $value) {
                $mapping[$value['ref_table']][$value['uid']] = $value['ref_field'];
            }
            return $mapping;
        }
        return false;
    }

    private function isEventProcessed($event, $completedDate) {
        $completedDate = date('Y-m-d H:i:s', strtotime($completedDate));
        $query = $this->db->get_where('mw_process_status', array('event' => $event, 'DateProcessed' => $completedDate), 1);
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    private function processEvent($event, $completedDate) {
        $completedDate = date('Y-m-d H:i:s', strtotime($completedDate));
        $query = $this->db->insert('mw_process_status', array('event' => $event, 'DateProcessed' => $completedDate));
        return true;
    }

    private function getCoordinates($farmerid) {
        $sql = "SELECT
            a.*
          FROM
            ktv_farmer_garden_area a
            LEFT OUTER JOIN ktv_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.Revision < b.Revision
          WHERE a.FarmerID = ?
          AND b.FarmerID IS NULL";

        $query = $this->db->query($sql, array($farmerid));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    private function PlanarPolygonAreaMeters($points) {

        $earthRadiusMeters = 6367460.0;
        $metersPerDegree = 2.0 * pi() * $earthRadiusMeters / 360.0;
        $radiansPerDegree = pi() / 180.0;

        $a = 0;
        for ($i = 0; $i < count($points); $i++) {
            $j = ($i + 1) % count($points);
            $xi = $points[$i]['Longitude'] * $metersPerDegree * cos($points[$i]['Latitude'] * $radiansPerDegree);
            $yi = $points[$i]['Latitude'] * $metersPerDegree;
            $xj = $points[$j]['Longitude'] * $metersPerDegree * cos($points[$j]['Latitude'] * $radiansPerDegree);
            $yj = $points[$j]['Latitude'] * $metersPerDegree;
            $a += $xi * $yj - $xj * $yi;
        }

        return abs($a / 2);
    }

    private function updateHaPolygon($farmer_id, $garden_nr, $survey_nr, $area) {
        $sql = "UPDATE
                `ktv_farmer_garden`
              SET
                `GardenHaPolygon` = ?
              WHERE `FarmerID` = ?
                AND `GardenNr` = ?
                AND `SurveyNr` = ?
                AND GardenHaPolygon IS NULL";
        $query = $this->db->query($sql, array($area, $farmer_id, $garden_nr, $survey_nr));
        if ($query) {
            $results = true;
        } else {
            $results = false;
        }
        return $results;
    }

    public function inputGardenHaPolygon($farmerid) {
        $coordinates = $this->getCoordinates($farmerid);
        $data = array();
        $coord = array();
        if ($coordinates) {
            foreach ($coordinates as $key => $val) {
                if (!isset($coord[$val['SurveyNr']])) {
                    $coord[$val['SurveyNr']] = array();
                    $coord[$val['SurveyNr']]['FarmerID'] = $val['FarmerID'];
                    $coord[$val['SurveyNr']]['GardenNr'] = $val['GardenNr'];
                    $coord[$val['SurveyNr']]['SurveyNr'] = $val['SurveyNr'];
                    $coord[$val['SurveyNr']]['Coordinate'] = array();
                }
                $data = array(
                    'Longitude' => $val['Longitude'],
                    'Latitude' => $val['Latitude']
                );
                array_push($coord[$val['SurveyNr']]['Coordinate'], $data);
            }

            foreach ($coord as $key => $val) {
                $area = $this->PlanarPolygonAreaMeters($val['Coordinate']);
                $area = round($area / 10000, 3);
//                echo "<pre>";
//                print_r($area);
//                echo "</pre>";
//                echo "<pre>";
//                print_r('=========================================');
//                echo "</pre>";
                if ($area) {
                    $result = $this->updateHaPolygon($val['FarmerID'], $val['GardenNr'], $val['SurveyNr'], $area);
                }
            }
        }
    }

    public function getFarmerPoso() {
        $sql = "SELECT
                -- a.`FarmerID`,b.`FarmerName`, e.`District`
                a.FarmerID,
                b.FarmerName,
                e.`District`
                FROM
                  `ktv_farmer_garden_area` a
                  LEFT JOIN `ktv_farmer` b ON a.`FarmerID` = b.farmerID
                  LEFT JOIN `ktv_village` c ON c.`VillageID` = b.`VillageID`
                  LEFT JOIN `ktv_subdistrict` d ON d.`SubDistrictID` = c.`SubDistrictID`
                  LEFT JOIN `ktv_district` e ON e.`DistrictID` = d.`DistrictID`
                  LEFT JOIN `ktv_cpg` f ON f.`CPGid` = b.`CPGid`
                -- WHERE
                -- e.`DistrictID` in (7204,7405)
                -- f.`OwnerClientID`= 13
                GROUP BY a.`FarmerID`";
        $query = $this->db->query($sql, array());
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    /*
     * sync process from backup middleware
     */

    function SyncProcess() {
        $data = $this->getData();
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
    }

    function getData() {

        ini_set('memory_limit', '256M');

        /*
         * by file
         */

//        $file = file_get_contents("./files/backup/databackup1-lite.csv");
//        $data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $file));

        /*
         * by query
         */

        $data = array();

        $event = $this->getEvent();
        if ($event) {
            foreach ($event as $key => $val) {
                $data = array();
                $events = array(
                    'programStage' => $val['programStage'],
                    'storedBy' => '',
                    'orgUnit' => $val['orgUnit'],
                    'program' => $val['program'],
                    'href' => 'http://mobile.palmoiltrace.com/api/events/' . $val['event'],
                    'event' => $val['event'],
                    'status' => $val['status'],
                    'eventDate' => $val['eventDate'],
                    'orgUnitName' => $val['dueDate'],
                    'created' => date('Y-m-d H:i:s'),
                    'completedDate' => date('Y-m-d H:i:s'),
                    'lastUpdated' => date('Y-m-d H:i:s'),
                    'coordinate' => Array(
                        'latitude' => $val['latitude'] != '' ? $val['latitude'] : 0,
                        'longitude' => $val['longitude'] != '' ? $val['longitude'] : 0
                    ),
                    'dataValues' => array()
                );
                $dataValues = $this->getDataValues($val['event']);
                $stored_by = 'admin';
                if ($dataValues) {
                    foreach ($dataValues as $k => $v) {
                        $stored_by = $v['storedBy'];
                        $events['dataValues'][] = array(
                            'lastUpdated' => date('Y-m-d H:i:s'),
                            'storedBy' => $v['storedBy'],
                            'created' => date('Y-m-d H:i:s'),
                            'dataElement' => $v['dataElement'],
                            'value' => $v['value'],
                            'providedElsewhere' => $v['providedElsewhere']
                        );
                    }
                }
                $events['completedBy'] = $stored_by;
                $data['events'][] = $events;
                if (!empty($data['events'])) {
                    $iterasi = 0;
                    foreach ($data['events'] as $event) {
                        $program = $this->getProgramTable($event['program']);
                        /*
                         * Pengecekan event yang sudah diproses
                         */
                        if ($this->isEventProcessed($event['event'], $event['lastUpdated'])) {
                            continue;
                        }

                        /*
                         * initial variabel
                         */
                        $params = array();
                        $header = array();
                        $body = array();
                        $latitude = '';
                        $longitude = '';


                        if (isset($event['coordinate']['latitude'])) {
                            $latitude = $event['coordinate']['latitude'];
                        }
                        if (isset($event['coordinate']['longitude'])) {
                            $longitude = $event['coordinate']['longitude'];
                        }

                        /*
                         * Susun array header dan value
                         */
                        foreach ($event['dataValues'] as $key => $value) {
                            $field = $this->getDataElementMapping($program['label'], $program['table'], $value['dataElement']);
                            if ($field) {
                                if ($value['value'] == "true" OR $value['value'] == "TRUE") {
                                    $value['value'] = '1';
                                } elseif ($value['value'] == "false" OR $value['value'] == "FALSE") {
                                    $value['value'] = '2';
                                }
                                $header[] = $field;
                                $body[] = $value['value'];
                            }
                        }

                        /*
                         * Susun array ke dalam parameter untuk
                         */
                        if (count($header) != 0 && count($body) != 0 && $event['status'] == 'COMPLETED') {
                            //cek apakah uid sudah ada di database atau belum
                            $update = $this->getUID($event['event'], $program['table']);

                            $headerIdentity = $this->getHeaderIdentity($update);
                            $header = array_merge($header, $headerIdentity);

                            $valueIdentity = $this->getValueIdentity($event['completedBy'], $event['created'], $event['lastUpdated'], $update);
                            $body = array_merge($body, $valueIdentity);

                            /*
                             * tambah parameter koordinat kebun
                             */
                            if ($longitude && $latitude and $program['table'] == 'ktv_farmer_garden') {
                                $headerCoordinate = $this->getHeaderCoordinate();
                                $header = array_merge($header, $headerCoordinate);

                                $valueCoordinate = $this->getValueCoordinate($latitude, $longitude);
                                $body = array_merge($body, $valueCoordinate);
                            }

                            $params[] = $header;
                            $params[] = $body;

                            /*
                             * Insert data polygon
                             */
                            if (in_array('GardenAreaCoordinates', $header)) {
                                $polygon = $this->buildPolygonData($params);
//                        if ($polygon) {
//                            $success[$event['completedBy']][$event['program']][$iterasi]['kml'] = $polygon;
//                        }
                            }

                            /*
                             * khusus untuk ktv_garden ada perlakuan khusus field clone dan shade trees
                             */
                            if ($program['table'] == 'ktv_farmer_garden') {
                                $params = $this->generateCloneAndShadeTrees($params);
                            }

                            /*
                             * khusus untuk ktv_farmer_financial 'baseline' survey number default ke 0
                             */
                            if ($program['table'] == 'ktv_farmer_financial') {
                                $params = $this->generateSurveyFinancialBaseline($params);
                            }

                            /*
                             * khusus untuk ktv_nutrition 'IsFamilyGarden' dan 'IsCommmercialGarden' diisi jika
                             */
                            if ($program['table'] == 'ktv_nutrition') {
                                $params = $this->generateGardenAreaChecklist($params);
                            }

                            /*
                             * tambah parameter interview Date
                             */
                            $params = $this->generateInterviewDate($params, $program['table'], $event['eventDate']);

                            /*
                             * tambah parameter uid
                             */
//                            $params = $this->generateUID($params, $event['event']);


                            /*
                             * proses insert ke database web
                             */
                            $result = $this->msyn->processSyn($params, $program['table']);
                            if ($result) {
                                $this->deleteEvent($event['event']);
//                        $success[$event['completedBy']][$event['program']][$iterasi]['results'] = $result;
                            } else {
                                $this->eventNotValid($event['event']);
                            }
                            $this->processEvent($event['event'], $event['lastUpdated']);
                        }
                        $iterasi++;
                    }
                }
            }
        }
        return $data;
    }

    private function getEvent() {
        $sql = "SELECT
                    a.event,
                    a.status,
                    a.program,
                    a.programStage,
                    a.enrollment,
                    a.orgUnit,
                    a.eventDate,
                    a.dueDate,
                    a.latitude,
                    a.longitude
                  FROM
                    temp_eventdatavalue a
                  WHERE a.processed = 'active'
                  GROUP BY a.event";
        $query = $this->db->query($sql, array());
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    private function getDataValues($event) {
        $sql = "SELECT
                    a.dataElement,
                    a.value,
                    a.storedBy,
                    a.providedElsewhere
                  FROM
                    temp_eventdatavalue a
                  WHERE a.event =?";
        $query = $this->db->query($sql, array($event));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    private function getProgramTable($program = null) {
        $query = $this->db->get_where('mw_program', array('uid' => $program, 'Status' => '1'));

        if ($query->num_rows() > 0) {
            $programs = array();
            foreach ($query->result_array() as $key => $value) {
                if ($value['reference'] != '') {
                    $programs['uid'] = $value['uid'];
                    $programs['table'] = $value['reference'];
                    $programs['label'] = $value['name'];
                }
            }
            return $programs;
        }
        return false;
    }

    private function deleteEvent($event) {
        $sql = "UPDATE
                temp_eventdatavalue
              SET
                processed = 'inactive'
                WHERE `event` = ?";
        $query = $this->db->query($sql, array($event));
    }

    private function eventNotValid($event) {
        $sql = "UPDATE
                temp_eventdatavalue
              SET
                processed = 'failed'
                WHERE `event` = ?";
        $query = $this->db->query($sql, array($event));
    }

    public function ExecuteDhisData($data, $success) {
        $iterasi = 0;
        foreach ($data as $event) {
            /*
             * Pengecekan event yang sudah diproses
             */
            if ($this->isEventProcessed($event['event'], $event['lastUpdated'])) {
                continue;
            }

            /*
             * initial variabel
             */
            $params = array();
            $header = array();
            $body = array();
            $latitude = '';
            $longitude = '';


            if (isset($event['coordinate']['latitude'])) {
                $latitude = $event['coordinate']['latitude'];
            }
            if (isset($event['coordinate']['longitude'])) {
                $longitude = $event['coordinate']['longitude'];
            }

            /*
             * Susun array header dan value
             */

            $program = $this->getProgramTable($event['program']);
            foreach ($event['dataValues'] as $key => $value) {
                $field = $this->getDataElementMapping($program['label'], $program['table'], $value['dataElement']);
                if ($field) {
                    if ($value['value'] == "true" OR $value['value'] == "TRUE") {
                        $value['value'] = '1';
                    } elseif ($value['value'] == "false" OR $value['value'] == "FALSE") {
                        $value['value'] = '2';
                    }
                    $header[] = $field;
                    $body[] = $value['value'];
                }
            }
            /*
             * Susun array ke dalam parameter untuk
             */
            if (count($header) != 0 && count($body) != 0 && $event['status'] == 'COMPLETED') {
                //cek apakah uid sudah ada di database atau belum
                $update = $this->getUID($event['event'], $program['table']);

                $headerIdentity = $this->getHeaderIdentity($update);
                $header = array_merge($header, $headerIdentity);

                $valueIdentity = $this->getValueIdentity($event['completedBy'], $event['created'], $event['lastUpdated'], $update);
                $body = array_merge($body, $valueIdentity);

                /*
                 * tambah parameter koordinat kebun
                 */
                if ($longitude && $latitude and $program['table'] == 'ktv_farmer_garden') {
                    $headerCoordinate = $this->getHeaderCoordinate();
                    $header = array_merge($header, $headerCoordinate);

                    $valueCoordinate = $this->getValueCoordinate($latitude, $longitude);
                    $body = array_merge($body, $valueCoordinate);
                }

                $params[] = $header;
                $params[] = $body;

                /*
                 * Insert data polygon
                 */
                if (in_array('GardenAreaCoordinates', $header)) {
                    $polygon = $this->buildPolygonData($params);
                    if ($polygon) {
                        $success[$event['completedBy']][$event['program']][$iterasi]['kml'] = $polygon;
                    }
                }

                /*
                 * khusus untuk ktv_garden ada perlakuan khusus field clone dan shade trees
                 */
                if ($program['table'] == 'ktv_farmer_garden') {
                    $params = $this->generateCloneAndShadeTrees($params);
                }

                /*
                 * khusus untuk ktv_farmer_financial 'baseline' survey number default ke 0
                 */
                if ($program['table'] == 'ktv_farmer_financial') {
                    $params = $this->generateSurveyFinancialBaseline($params);
                }

                /*
                 * khusus untuk ktv_nutrition 'IsFamilyGarden' dan 'IsCommmercialGarden' diisi jika
                 */
                if ($program['table'] == 'ktv_nutrition') {
                    $params = $this->generateGardenAreaChecklist($params);
                }

                /*
                 * tambah parameter interview Date
                 */
                $params = $this->generateInterviewDate($params, $program['table'], $event['eventDate']);

                /*
                 * tambah parameter uid
                 */
                $params = $this->generateUID($params, $event['event']);


                /*
                 * proses insert ke database web
                 */
                $result = $this->msyn->processSyn($params, $program['table']);
                if ($result) {
                    $success[$event['completedBy']][$event['program']][$iterasi]['results'] = $result;
                }
                $this->processEvent($event['event'], $event['lastUpdated']);
            }
            $iterasi++;
        }
        if (count($success) > 0) {
            return $success;
        } else {
            return array();
        }
    }

    public function generateFarmerDisplayID() {
        $sql = 'SELECT * FROM ktv_members WHERE MemberDisplayID IS NULL OR MemberDisplayID = ""';
        $query = $this->db->query($sql);
        $data = $query->result_array();
        if ($data) {
            foreach ($data as $key => $val) {
                $member = $this->mgrower->genMemberID($val['VillageID'], 'F');
                $update = $this->UpdateMemberImport($val['MemberID'], $member['MemberDisplayID']);
            }
        }
        exit;
    }

    public function UpdateMemberImport($MemberID, $MemberDisplayID) {
        $sql = "UPDATE ktv_members SET MemberDisplayID = '$MemberDisplayID' where MemberID = $MemberID";
        $query = $this->db->query($sql);
    }

    public function InsertMemberRoles($memberID) {
        $sql = "INSERT INTO ktv_member_role
            (MemberID,
             MRoleID,
             DateCreated,
             CreatedBy,
             DateUpdated,
             LastModifiedBy,
             DateSync,
             uid)
        VALUES ($memberID,
        '1',
        NOW(),
        1,
        NOW(),
        1,
        NULL,
        NULL)";
        $query = $this->db->query($sql);
    }

    public function dataCollectionEmailSummary() {
        //ambil range date eventnya
        $sql = "SELECT
                a.`SetValue`
            FROM
                sys_setting a
            WHERE
                a.SetKey = 'data_collection_start'

            UNION

            SELECT
                a.`SetValue`
            FROM
                sys_setting a
            WHERE
                a.SetKey = 'data_collection_end'";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        $startEvent = $data[0]['SetValue'];
        $endEvent = $data[1]['SetValue'];

        //list data FF (begin)
        $sql = "SELECT
                a.`UserId`
                , b.`PersonNm`
                , c.`OfficialEmail`
                , c.`CcEmail`
            FROM
                sys_log_sync a
                INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
            WHERE
                a.`DateFrom` >= CURDATE() - INTERVAL 1 DAY
                AND a.`UserId` IS NOT NULL
                AND a.`UserId` NOT IN (0,1)
                AND c.`OfficialEmail` IS NOT NULL
            GROUP BY a.`UserId`
            ORDER BY b.`PersonNm` ASC
            ";
        $query = $this->db->query($sql, array($startEvent, $endEvent));
        $dataListFF = $query->result_array();
        //list data FF (end)

        for ($i = 0; $i < count($dataListFF); $i++) {
            //query data yg diperlukan di Excel (begin)
            //1. Farmer
            $sql = "SELECT
                a.`FarmerID` AS 'ID Petani'
                ,a.ExtFarmerID AS 'ID Petani Eksternal'
                ,a.`FarmerName` AS 'Nama Petani'
                ,a.`DateCollection` AS 'Tgl Interview'
                ,a.CPGid AS 'ID Kelompok Tani'
                ,b.`GroupName` AS 'Kelompok Tani'
                ,c.`Province` AS 'Propinsi'
                ,d.`District` AS 'Kabupaten'
                ,e.`SubDistrict` AS 'Kecamatan'
                ,f.`Village` AS 'Desa'
                ,a.Address AS 'Alamat'
                ,RtRw AS 'RT / RW'
                ,IF(Gender=1,'L',IF(Gender=2,'P','')) AS 'Jenis Kelamin'
                ,IF(MaritalStatus=1,'Menikah',IF(MaritalStatus=2,'Lajang',IF(MaritalStatus=2,'Janda/Duda',''))) AS 'Status Perkawinan'
                ,BirthDate AS 'Tanggal Lahir'
                ,Handphone AS 'Handphone'
                ,IF(Education=1,'Tidak pernah sekolah',IF(Education=2,'Tidak tamat SD',IF(Education=3,'Tamat SD',IF(Education=4,'Tamat SMP',IF(Education=5,'Tamat SMA',IF(Education=6,'Tamat Perguruan Tinggi','')))))) AS 'Pendidikan terakhir'
                ,IF(StatusFarmer=1,'Yes',IF(StatusFarmer=2,'No','')) AS 'Status aktif'
                ,IF(ReasonStatusFarmer=1,'Meninggal',IF(ReasonStatusFarmer=2,'Pindah',IF(ReasonStatusFarmer=3,'Berhenti bertani',''))) AS 'Alasan jika petani sudah tidak aktif'
                ,AccountBeneficiary AS 'Nama Akun'
                ,i.`BankName` AS 'Nama Bank'
                ,BankBranch AS 'Cabang'
                ,AccountNumber AS 'Nomor rekening'
                ,Photo AS 'Foto Petani'
                ,LearningContractStatus AS 'Kontrak Pelatihan'
                ,LearningContractSign AS 'Tanda Tangan Petani'
                ,a.`DateCreated`
                ,g.`UserRealName` AS CreatedBy
                ,a.`DateUpdated`
                ,h.`UserRealName` AS LastModifiedBy
                FROM `ktv_farmer` a
                LEFT JOIN ktv_cpg b ON b.`CPGid`=a.`CPGid`
                LEFT JOIN ktv_village f ON f.`VillageID`=a.`VillageID`
                LEFT JOIN ktv_subdistrict e ON e.`SubDistrictID`=f.SubDistrictID
                LEFT JOIN ktv_district d ON d.`DistrictID`=e.DistrictID
                LEFT JOIN ktv_province c ON c.`ProvinceID`=d.ProvinceID
                LEFT JOIN sys_user g ON g.UserId=a.`CreatedBy`
                LEFT JOIN sys_user h ON h.UserId=a.`LastModifiedBy`
                LEFT JOIN ktv_bank i ON i.`BankID`=a.`BankID`
                WHERE
                    a.StatusCode='active'
                    AND (a.DateCreated BETWEEN '$startEvent' AND '$endEvent' OR a.DateUpdated BETWEEN '$startEvent' AND '$endEvent')
                    AND (a.CreatedBy='{$dataListFF[$i]['UserId']}' OR a.LastModifiedBy='{$dataListFF[$i]['UserId']}')
                GROUP BY a.`FarmerID`";
            $query = $this->db->query($sql);
            $dataFarmer = $query->result_array();

            //2. Garden
            $sql = "SELECT
                a.`FarmerID` AS 'ID Petani'
                ,b.`FarmerName` AS 'Nama Petani'
                ,c.`GroupName` AS 'Kelompok Tani'
                ,d.`Province` AS 'Propinsi'
                ,e.`District` AS 'Kabupaten'
                ,f.`SubDistrict` AS 'Kecamatan'
                ,g.`Village` AS 'Desa'
                ,a.`GardenNr` AS 'Nr Kebun'
                ,a.`SurveyNr` AS 'Nr Survey'
                ,a.`DateCollection` AS 'Tgl Interview'
                ,IF(RoadCondition=1,'Jalan Aspal',IF(RoadCondition=2,'Jalan Pengerasan',IF(RoadCondition=3,'Jalan Tanah',IF(RoadCondition=4,'Tidak ada Jalan','')))) AS 'Kondisi jalan ke kebun kakao'
                ,IF(OwnershipCocoa=1,'Pemilik Penggarap',IF(OwnershipCocoa=2,'Profit Sharing',IF(OwnershipCocoa=3,'Petani Penyewa',IF(OwnershipCocoa=4,'Lain-lain','')))) AS 'Status kepemilikan tanah'
                ,IF(LandOwner=1,'Saya Sendiri',IF(LandOwner=2,'Anggota Keluarga',IF(LandOwner=3,'Orang Lain',IF(LandOwner=4,'Tidak Tahu','')))) AS 'Pemilik tanah'
                ,IF(LandCertificate=1,'Tidak Ada',IF(LandCertificate=2,'Akte Notaris/BPN',IF(LandCertificate=3,'KKT (Camat)',IF(LandCertificate=4,'Desa/Lurah',IF(LandCertificate=5,'Tidak Tahu',''))))) AS 'Serfitikat kepemilikan tanah'
                ,GardenDistance AS 'Jarak rumah ke kebun kakao (m)'
                ,a.`Latitude`
                ,a.`Longitude`
                ,a.`Elevation`
                ,GardenHaUnCertified AS 'Ukuran kebun (Ha)'
                ,GardenAreaCoordinates AS 'Koordinat area kebun'
                ,IF(GardenLandUse=1,'Converted Forest',IF(GardenLandUse=2,'Limited Forest',IF(GardenLandUse=3,'Production Forest',IF(GardenLandUse=4,'Protected Forest',IF(GardenLandUse=5,'Unspecified Area',''))))) AS 'Pengggunaan Lahan'
                ,TahunTanamanCocoa AS 'Tahun tanam kakao'
                ,PohonTBM AS 'Jumlah tanaman belum menghasilkan (pohon)'
                ,PohonTM AS 'Jumlah tanaman menghasilkan (pohon)'
                ,PohonRehab AS 'Jumlah tanaman rusak (pohon)'
                ,GraftedTrees AS 'Jumlah pohon sambung samping/sambung pucuk tunas air '
                ,GraftedTreesTahun AS 'Tahun tanam pohon sambung samping/sambung pucuk tunas air '
                ,TopGraftedTrees AS 'Jumlah penanaman ulang dari sambung pucuk dan biji'
                ,TopGraftedTreesTahun AS 'Tahun tanam pohon pengan penanaman ulang dari sambung pucuk dan biji'
                ,ReplantedTrees AS 'Jumlah pohon dengan penanaman ulang dan sisipan'
                ,ReplantedTreesTahun AS 'Tahun tanam pohon dengan penanaman ulang dan sisipan'
                ,S1Nr AS 'S1'
                ,S2Nr AS 'S2'
                ,J45Nr AS '45/MCC02'
                ,M01Nr AS 'M01'
                ,TSH858Nr AS 'TSH 858'
                ,ICRRI3Nr AS 'ICCRI3'
                ,ICRRI4Nr AS 'ICCRI4'
                ,ICRRI5Nr AS 'ICCRI5'
                ,RCC70Nr AS 'RCC70'
                ,RCC71Nr AS 'RCC71'
                ,RCC72Nr AS 'RCC72'
                ,RCC73Nr AS 'RCC73'
                ,LokalNr AS 'Lokal'
                ,RCLNr AS 'RCL'
                ,THRNr AS 'THR'
                ,APNr AS 'AP'
                ,PRNr AS 'PR'
                ,ScavinaNr AS 'Scavina'
                ,MTNr AS 'MT'
                ,M02Nr AS 'M02'
                ,M04Nr AS 'M04'
                ,M06Nr AS 'M06'
                ,MHP03Nr AS 'MHP03'
                ,MHP04Nr AS 'MHP04'
                ,BB01Nr AS 'BB01'
                ,BLBNr AS 'BLB'
                ,BRTNr AS 'BRT'
                ,CloneLain AS 'Klon Lainnya (sebutkan nama varietas)'
                ,CloneLainNr AS 'Jumlah klon lain (pohon)'
                ,KelapaNr AS 'Kelapa'
                ,PinangNr AS 'Pinang'
                ,KaretNr AS 'Karet'
                ,CengkehNr AS 'Cengkeh'
                ,JambuMenteNr AS 'Jambu Mete'
                ,SawitNr AS 'Sawit'
                ,ArenNr AS 'Aren'
                ,PalaNr AS 'Pala'
                ,KemiriNr AS 'Kemiri'
                ,KapokNr AS 'Kapuk'
                ,MahoniNr AS 'Mahoni'
                ,JatiNr AS 'Jati'
                ,BitiNr AS 'Biti'
                ,UruNr AS 'Uru'
                ,JabonNr AS 'Jabon'
                ,SengonNr AS 'Sengon'
                ,AlpukatNr AS 'Alpukat'
                ,PisangNr AS 'Pisang'
                ,SukunNr AS 'Sukun'
                ,CempedakNr AS 'Cempedak'
                ,JerukNr AS 'Jeruk'
                ,JambuNr AS 'Fruit Trees-Guava'
                ,JackFruitNr AS 'Nangka'
                ,LangsatNr AS 'Langsat'
                ,ManggaNr AS 'Mangga'
                ,ManggisNr AS 'Manggis'
                ,PepayaNr AS 'Pepaya'
                ,RambutanNr AS 'Rambutan'
                ,KedondongNr AS 'Kedondong'
                ,DurianNr AS 'Durian'
                ,JengkolNr AS 'Jengkol'
                ,GamalNr AS 'Gamal'
                ,LamtoroNr AS 'Lamtoro'
                ,PetaiNr AS 'Petai'
                ,ShadeTreesNr AS 'Total pohon pelindung'
                ,IF(ShadeTreesIncProductivity=1,'Yes',IF(ShadeTreesIncProductivity=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk meningkatkan produktivitas tanaman kakao'
                ,IF(ShadeTreesExtraIncome=1,'Yes',IF(ShadeTreesExtraIncome=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mendapatkan penghasilan tambahan'
                ,IF(ShadeTreesProtectSoil=1,'Yes',IF(ShadeTreesProtectSoil=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk melindungi tanah'
                ,IF(ShadeTreesReducePests=1,'Yes',IF(ShadeTreesReducePests=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mengurangi serangan hama dan penyakit'
                ,IF(ShadeTreesReduceHeat=1,'Yes',IF(ShadeTreesReduceHeat=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mengurangi suhu panas di kebun'
                ,IF(ShadeTreesIncLandValue=1,'Yes',IF(ShadeTreesIncLandValue=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk meningkatkan nilai tanah'
                ,IF(ShadeTreesAddFirewood=1,'Yes',IF(ShadeTreesAddFirewood=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk menambah sumber kayu bakar'
                ,IF(ShadeTreesAddFodder=1,'Yes',IF(ShadeTreesAddFodder=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Untuk menambah sumber makanan ternak'
                ,IF(ShadeTreesDoNotKnow=1,'Yes',IF(ShadeTreesDoNotKnow=2,'No','')) AS 'Mengapa anda  menanam pohon  pelindung - Saya tidak tahu'
                ,ShadeTreesOthers AS 'Mengapa anda  menanam pohon  pelindung - Lainnya (Sebutkan)'
                ,IF(ShadeTreesSpreadEvently=1,'Yes',IF(ShadeTreesSpreadEvently=2,'No','')) AS '(C) Apakah pohon penaung tersebar merata di kebun'
                ,IF(ShadeTreesObtainSeeds=1,'Farmer Group',IF(ShadeTreesObtainSeeds=2,'Cooperatives/IMS',IF(ShadeTreesObtainSeeds=3,'Seeds Seller',IF(ShadeTreesObtainSeeds=4,'Make your own','')))) AS '(C) Darimana anda mendapatkan bibit pohon penaung'
                ,Nuts AS 'Kacang-kacangan'
                ,Tubers AS 'Umbi-umbian'
                ,Patchouli AS 'Nilam'
                ,CoverCropOthers AS 'Tanaman Penutup Lainnya (sebutkan)'
                ,IF(NoCoverCrop=1,'Yes',IF(NoCoverCrop=2,'No','')) AS 'Tidak Ada Tanaman Penutup Tanah'
                ,IF(ObtainSeedsToday=1,'Supplier yang direkomendasikan IMS',IF(ObtainSeedsToday=2,'Supplier diluar rekomendasi IMS',IF(ObtainSeedsToday=3,'Membuat bibit sendiri',''))) AS '(C) Darimana anda memperoleh bibit saat ini'
                ,IF(SeedsFreeFromPests=1,'Yes',IF(SeedsFreeFromPests=2,'No','')) AS '(C) Apakah secara kasat mata bibit anda bebas  hama & penyakit'
                ,IF(SeedsFillRoutineMaintenance=1,'Yes',IF(SeedsFillRoutineMaintenance=2,'No','')) AS '(C) Apakah anda mengisi lembar catatan perawatan bibit  secara rutin'
                ,IF(AfterCertSaveRecordOriginSeeds=1,'Yes',IF(AfterCertSaveRecordOriginSeeds=2,'No','')) AS '(C) Setelah bergabung dengan program sertifikasi UTZ/RA,  apakah anda menyimpan catatan, sertifikat atau  keterangan tertulis tentang asal bibit kakao anda'
                ,ProductionNext AS 'Perkiraan produksi setahun ke depan (kg)'
                ,Production AS 'Perkiraan produksi setahun yang lalu (kg)'
                ,PanenTrekMonths AS 'Panen trek/lama musim (jumlah bulan)'
                ,IF(PanenTrekPanenMonth=1,'Tidak Panen',IF(PanenTrekPanenMonth=2,'1 kali/minggu',IF(PanenTrekPanenMonth=3,'1 kali/2 minggu',IF(PanenTrekPanenMonth=4,'1 kali/bulan','')))) AS 'Panen trek/interval panen'
                ,PanenTrekKg AS 'Panen trek (kg/panen)'
                ,PanenBiasaMonths AS 'Panen biasa/lama musin (jumlah bulan)'
                ,IF(PanenBiasaPanenMonth=1,'Tidak Panen',IF(PanenBiasaPanenMonth=2,'1 kali/minggu',IF(PanenBiasaPanenMonth=3,'1 kali/2 minggu',IF(PanenBiasaPanenMonth=4,'1 kali/bulan','')))) AS 'Panen biasa/interval panen'
                ,PanenBiasaKg AS 'Panen biasa (kg/panen)'
                ,PanenRayaMonths AS 'Panen raya/lama musin (jumlah bulan)'
                ,IF(PanenRayaPanenMonth=1,'Tidak Panen',IF(PanenRayaPanenMonth=2,'1 kali/minggu',IF(PanenRayaPanenMonth=3,'1 kali/2 minggu',IF(PanenRayaPanenMonth=4,'1 kali/bulan','')))) AS 'Panen raya/interval panen'
                ,PanenRayaKg AS 'Panen raya (kg/panen)'
                ,SalesLastyear AS 'Penjualan dari hasil setahun yang lalu (kg)'
                ,a.`Comment` AS 'Komentar'
                ,IF(HarvestAwal=1,'Yes',IF(HarvestAwal=2,'No','')) AS 'Cara panen kakao - Buah masak awal'
                ,IF(HarvestMasak=1,'Yes',IF(HarvestMasak=2,'No','')) AS 'Cara panen kakao - Buah masak'
                ,IF(HarvestHama=1,'Yes',IF(HarvestHama=2,'No','')) AS 'Cara panen kakao - Buah terserang H/P'
                ,IF(HowToCleanSkin=1,'Ditumpuk di kebun kakao',IF(HowToCleanSkin=2,'Ditumpuk diluar kebun',IF(HowToCleanSkin=3,'Ditumpuk & ditutup dengan plastik',IF(HowToCleanSkin=4,'Diolah menjadi kompos',IF(HowToCleanSkin=5,'Dikuburkan',IF(HowToCleanSkin=6,'Dibakar',IF(HowToCleanSkin=7,'Ditumpuk jadi pakan ternak',IF(HowToCleanSkin=8,'Dibuang di sungai','')))))))) AS 'Sanitasi – Apa yang anda lakukan pada kulit buah setelah pembelahan'
                ,IF(HowToDealOrganicAnorganicWaste=1,'Limbah disimpan dan dibuang hanya pada area - area yang ditentukan',IF(HowToDealOrganicAnorganicWaste=2,'Limbah tidak berbahaya digunakan kembali atau didaur ulang manakala mungin',IF(HowToDealOrganicAnorganicWaste=1,'Limbah organik digunakan sebagai pupuk',''))) AS '(C) Bagaimana anda menangani limbah organik dan anorganik'
                ,IF(PruningOptStructure=1,'Yes',IF(PruningOptStructure=2,'No','')) AS 'Dilakukan Pemangkasan tanaman kakao untuk membentuk struktur yang optimal'
                ,FrequentPruningOptStructure AS 'Frekuensi pemangkasan (kali/tahun)'
                ,HeightPruningOptStructure AS 'Tinggi pemangkasan (meter)'
                ,IF(PruningBudInfected=1,'Yes',IF(PruningBudInfected=2,'No','')) AS 'Dilakukan Pemangkasan tanaman kakao Pemangkasan tunas atau bagian tanaman yang terinfeksi hama penyakit'
                ,FrequentPruningBudInfected AS 'Frekuensi pemangkasan (kali/tahun)'
                ,HeightPruningBudInfected AS 'Tinggi pemangkasan (meter)'
                ,IF(PruningNotProductive=1,'Yes',IF(PruningNotProductive=2,'No','')) AS 'Dilakukan Pemangkasan tanaman kakao Pemangkasan berat untuk tanaman yang tidak produktif'
                ,FrequentPruningNotProductive AS 'Frekuensi (kali/tahun)'
                ,HeightPruningNotProductive AS 'Tinggi pemangkasan (meter)'
                ,IF(DisinfectedTools=1,'Yes',IF(DisinfectedTools=2,'No','')) AS '(C) Apakah alat-alat yang anda gunakan selalu disterilkan'
                ,IF(PruningProtectPlants=1,'Yes',IF(PruningProtectPlants=2,'No','')) AS 'Pemangkasan pohon pelindung'
                ,FrequentPruningProtect AS 'Frekuensi Pemangkasan Pohon Pelindung'
                ,IF(PakaiKompos=1,'Yes',IF(PakaiKompos=2,'No','')) AS 'Apakah anda memakai pupuk kompos dan/atau organik'
                ,FrequentFertilizationKompos AS 'Kompos Frekuensi (kali/tahun)'
                ,DoseFertilizerKompos AS 'Dosis (kg/pohon/kali)'
                ,FrKomposKandang AS 'Pupuk Kandang Frekuensi (kali/tahun)'
                ,DoseKomposKandang AS 'Dosis (kg/pohon/kali)'
                ,FrKomposCair AS 'Pupuk Cair Frekuensi (kali/tahun)'
                ,DoseKomposCair AS 'Dosis (liter/pohon/kali)'
                ,FrKomposGranula AS 'Pupuk Granula Frekuensi (kali/tahun)'
                ,DoseKomposGranula AS 'Dosis (gram/pohon/kali)'
                ,IF(KomposTBM=1,'Yes',IF(KomposTBM=2,'No','')) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Belum Menghasilkan'
                ,IF(KomposTM=1,'Yes',IF(KomposTM=2,'No','')) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Menghasilkan'
                ,IF(KomposTR=1,'Yes',IF(KomposTR=2,'No','')) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Rusak'
                ,IF(AvailableOrganicFertilizer=1,'Yes',IF(AvailableOrganicFertilizer=2,'No','')) AS '(C) Apakah pupuk organik selalu tersedia dan mudah diperoleh'
                ,IF(RoutineWatchSoilFertility=1,'Yes',IF(RoutineWatchSoilFertility=2,'No','')) AS '(C) Apakah anda secara rutin memantau kesuburan tanah secara visual'
                ,IF(ImprovePlantFixNitrogenInSoil=1,'Yes',IF(ImprovePlantFixNitrogenInSoil=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menanam tanaman yang dapat memperbaiki unsur nitrogen dalam tanah'
                ,IF(ImproveApplyPracticeAgroforestry=1,'Yes',IF(ImproveApplyPracticeAgroforestry=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menerapkan praktek agroforestry'
                ,IF(ImproveFertilizingWithOrganic=1,'Yes',IF(ImproveFertilizingWithOrganic=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Melakukan pemupukan dengan pupuk alami/organik'
                ,IF(ImproveFertilizingWithAnorganic=1,'Yes',IF(ImproveFertilizingWithAnorganic=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Melakukan pemupukan dengan pupuk buatan/anorganik'
                ,IF(ImproveMakeBiopori=1,'Yes',IF(ImproveMakeBiopori=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Membuat biopori'
                ,IF(ImprovePlantingShadeTrees=1,'Yes',IF(ImprovePlantingShadeTrees=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menanam tanaman pelindung '
                ,IF(ImproveUseCoverCrop=1,'Yes',IF(ImproveUseCoverCrop=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menggunakan tanaman penutup tanah (cover crop)'
                ,IF(ImproveTerracing=1,'Yes',IF(ImproveTerracing=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Membuat terasering'
                ,IF(ImproveDoNothing=1,'Yes',IF(ImproveDoNothing=2,'No','')) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Tidak melakukan apa-apa'
                ,IF(TidakMemakaiKimia=1,'Yes',IF(TidakMemakaiKimia=2,'No','')) AS 'Apakah anda di kebun ini memakai pupuk non organik/kimia'
                ,FrUrea AS 'Urea Frekuensi (kali/tahun)'
                ,DoUrea AS 'Urea Dosis (gram/pohon/kali)'
                ,FrZa AS 'ZA Frekuensi (kali/tahun)'
                ,DoZa AS 'ZA Dosis (gram/pohon/kali)'
                ,FrTsp AS 'TSP Frekuensi (kali/tahun)'
                ,DoTsp AS 'TSP Dosis (gram/pohon/kali)'
                ,FrNpk AS 'NPK Frekuensi (kali/tahun)'
                ,DoNpk AS 'NPK Dosis (gram/pohon/kali)'
                ,FrKcl AS 'KCL Frekuensi (kali/tahun)'
                ,DoKcl AS 'KCL Dosis (gram/pohon/kali)'
                ,FrFoliar AS 'Foliar Frekuensi (kali/tahun)'
                ,DoFoliar AS 'Foliar Dosis (gram/pohon/kali)'
                ,IF(PupukTBM=1,'Yes',IF(PupukTBM=2,'No','')) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Belum Menghasilkan'
                ,IF(PupukTM=1,'Yes',IF(PupukTM=2,'No','')) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Menghasilkan'
                ,IF(PupukTR=1,'Yes',IF(PupukTR=2,'No','')) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Rusak'
                ,IF(KimiaDana=1,'Yes',IF(KimiaDana=2,'No','')) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak ada dana'
                ,IF(KimiaSupplier=1,'Yes',IF(KimiaSupplier=2,'No','')) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak menemukan supplier'
                ,IF(KimiaDilatih=1,'Yes',IF(KimiaDilatih=2,'No','')) AS 'Jika tidak memakai pupuk non organik, kenapa - Belum dilatih'
                ,IF(KimiaTidakSuka=1,'Yes',IF(KimiaTidakSuka=2,'No','')) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak suka menggunakan pupuk kimia'
                ,IF(KimiaTidakTersedia=1,'Yes',IF(KimiaTidakTersedia=2,'No','')) AS 'Jika tidak memakai pupuk non organik, kenapa - Pupuk tidak tersedia'
                ,IF(KimiaLain=1,'Yes',IF(KimiaLain=2,'No','')) AS 'Jika tidak memakai pupuk non organik, kenapa - Lain-lain'
                ,IF(HamaBPK=1,'Yes',IF(HamaBPK=2,'No','')) AS 'Hama Utama Kakao - Penggerek Buah Kakao'
                ,IF(HamaHelopeltis=1,'Yes',IF(HamaHelopeltis=2,'No','')) AS 'Hama Utama Kakao - Helopeltis'
                ,IF(HamaBatang=1,'Yes',IF(HamaBatang=2,'No','')) AS 'Hama Utama Kakao - Penggerek batang atau ranting'
                ,IF(PenyakitKanker=1,'Yes',IF(PenyakitKanker=2,'No','')) AS 'Penyakit Utama Kakao - Kanker Batang'
                ,IF(PenyakitBusuk=1,'Yes',IF(PenyakitBusuk=2,'No','')) AS 'Penyakit Utama Kakao - Busuk Buah'
                ,IF(PenyakitUpas=1,'Yes',IF(PenyakitUpas=2,'No','')) AS 'Penyakit Utama Kakao - Jamur Upas'
                ,IF(PenyakitAkar=1,'Yes',IF(PenyakitAkar=2,'No','')) AS 'Penyakit Utama Kakao - Jamur Akar'
                ,IF(PenyakitVSD=1,'Yes',IF(PenyakitVSD=2,'No','')) AS 'Penyakit Utama Kakao - VSD'
                ,IF(PenyakitAntraknose=1,'Yes',IF(PenyakitAntraknose=2,'No','')) AS 'Penyakit Utama Kakao - Antraknose'
                ,IF(RoutineMonitorPestInGarden=1,'Yes',IF(RoutineMonitorPestInGarden=2,'No','')) AS '(C) Apakah pemantauan hama dan penyakit rutin anda lakukan di kebun ini'
                ,IF(Herbisida,'Yes',IF(Herbisida,'No','')) AS 'Apakah Anda menggunakan herbisida'
                ,FrequentHerbisida AS 'Herbisida Frekuensi (kali/tahun)'
                ,DoseHerbisida AS 'Herbisida Dosis (ml/kali/kebun)'
                ,IF(Herbisida14=1,'Yes',IF(Herbisida14=2,'No','')) AS 'Bimastar'
                ,IF(Herbisida12=1,'Yes',IF(Herbisida12=2,'No','')) AS 'Bravo-xone'
                ,IF(Herbisida22=1,'Yes',IF(Herbisida22=2,'No','')) AS 'DMA'
                ,IF(Herbisida24=1,'Yes',IF(Herbisida24=2,'No','')) AS 'Konup'
                ,IF(Herbisida26=1,'Yes',IF(Herbisida26=2,'No','')) AS 'Mupxone'
                ,IF(Herbisida10=1,'Yes',IF(Herbisida10=2,'No','')) AS 'Noxone'
                ,IF(Herbisida20=1,'Yes',IF(Herbisida20=2,'No','')) AS 'Prima Up'
                ,IF(Herbisida16=1,'Yes',IF(Herbisida16=2,'No','')) AS 'Primastar'
                ,IF(Herbisida8=1,'Yes',IF(Herbisida8=2,'No','')) AS 'Rambo'
                ,IF(Herbisida28=1,'Yes',IF(Herbisida28=2,'No','')) AS 'Senus'
                ,IF(Herbisida18=1,'Yes',IF(Herbisida18=2,'No','')) AS 'Supretox'
                ,IF(Herbisida2=1,'Yes',IF(Herbisida2=2,'No','')) AS 'Basmilang'
                ,IF(Herbisida5=1,'Yes',IF(Herbisida5=2,'No','')) AS 'Gramo-xone'
                ,IF(Herbisida25=1,'Yes',IF(Herbisida25=2,'No','')) AS 'Herbatop'
                ,IF(Herbisida19=1,'Yes',IF(Herbisida19=2,'No','')) AS 'Kleenup'
                ,IF(Herbisida9=1,'Yes',IF(Herbisida9=2,'No','')) AS 'Para  Special'
                ,IF(Herbisida11=1,'Yes',IF(Herbisida11=2,'No','')) AS 'Paratop'
                ,IF(Herbisida3=1,'Yes',IF(Herbisida3=2,'No','')) AS 'Pilar Up'
                ,IF(Herbisida27=1,'Yes',IF(Herbisida27=2,'No','')) AS 'Pointer'
                ,IF(Herbisida15=1,'Yes',IF(Herbisida15=2,'No','')) AS 'Polado'
                ,IF(Herbisida23=1,'Yes',IF(Herbisida23=2,'No','')) AS 'Polaris'
                ,IF(Herbisida13=1,'Yes',IF(Herbisida13=2,'No','')) AS 'Primaxone'
                ,IF(Herbisida1=1,'Yes',IF(Herbisida1=2,'No','')) AS 'Round Up'
                ,IF(Herbisida17=1,'Yes',IF(Herbisida17=2,'No','')) AS 'Rumat'
                ,IF(Herbisida7=1,'Yes',IF(Herbisida7=2,'No','')) AS 'Sapurata'
                ,IF(Herbisida4=1,'Yes',IF(Herbisida4=2,'No','')) AS 'Sun Up'
                ,IF(Herbisida6=1,'Yes',IF(Herbisida6=2,'No','')) AS 'Supremo'
                ,IF(Herbisida29=1,'Yes',IF(Herbisida29=2,'No','')) AS 'Tamaxon'
                ,IF(Herbisida21=1,'Yes',IF(Herbisida21=2,'No','')) AS 'Tanistar'
                ,MerekHerbisida AS 'Herbisida Merk Lainnya'
                ,IF(Insectisida=1,'Yes',IF(Insectisida=2,'No','')) AS 'Apakah Anda menggunakan insektisida'
                ,FrequentInsectisida AS 'Insektisida Frekuensi (kali/tahun)'
                ,DoseInsectisida AS 'Insektisida Dosis (ml/kali/kebun)'
                ,IF(Insectisida1=1,'Yes',IF(Insectisida1=2,'No','')) AS 'Alika'
                ,IF(Insectisida16=1,'Yes',IF(Insectisida16=2,'No','')) AS 'Arrivo'
                ,IF(Insectisida18=1,'Yes',IF(Insectisida18=2,'No','')) AS 'Bestox'
                ,IF(Insectisida21=1,'Yes',IF(Insectisida21=2,'No','')) AS 'Buldok'
                ,IF(Insectisida3=1,'Yes',IF(Insectisida3=2,'No','')) AS 'Capture'
                ,IF(Insectisida4=1,'Yes',IF(Insectisida4=2,'No','')) AS 'Bento'
                ,IF(Insectisida5=1,'Yes',IF(Insectisida5=2,'No','')) AS 'Regent'
                ,IF(Insectisida9=1,'Yes',IF(Insectisida9=2,'No','')) AS 'Chlormite'
                ,IF(Insectisida20=1,'Yes',IF(Insectisida20=2,'No','')) AS 'Dangke'
                ,IF(Insectisida10=1,'Yes',IF(Insectisida10=2,'No','')) AS 'Decis'
                ,IF(Insectisida15=1,'Yes',IF(Insectisida15=2,'No','')) AS 'Deicer 505'
                ,IF(Insectisida6=1,'Yes',IF(Insectisida6=2,'No','')) AS 'Drusban'
                ,IF(Insectisida19=1,'Yes',IF(Insectisida19=2,'No','')) AS 'Halona'
                ,IF(Insectisida12=1,'Yes',IF(Insectisida12=2,'No','')) AS 'Klensect'
                ,IF(Insectisida22=1,'Yes',IF(Insectisida22=2,'No','')) AS 'Laser'
                ,IF(Insectisida2=1,'Yes',IF(Insectisida2=2,'No','')) AS 'Matador'
                ,IF(Insectisida8=1,'Yes',IF(Insectisida8=2,'No','')) AS 'Nurelle'
                ,IF(Insectisida11=1,'Yes',IF(Insectisida11=2,'No','')) AS 'Organik'
                ,IF(Insectisida7=1,'Yes',IF(Insectisida7=2,'No','')) AS 'Penalty'
                ,IF(Insectisida23=1,'Yes',IF(Insectisida23=2,'No','')) AS 'Sevin'
                ,IF(Insectisida17=1,'Yes',IF(Insectisida17=2,'No','')) AS 'Sidame-thrin'
                ,IF(Insectisida14=1,'Yes',IF(Insectisida14=2,'No','')) AS 'Unicide'
                ,IF(Insectisida13=1,'Yes',IF(Insectisida13=2,'No','')) AS 'Vigor'
                ,MerekInsectisida AS 'Insektisida Merk Lainnya'
                ,IF(Fungisida=1,'Yes',IF(Fungisida=2,'No','')) AS 'Apakah Anda menggunakan fungisida'
                ,FrequentFungisida AS 'Fungisida Frekuensi (kali/tahun)'
                ,DoseFungisida AS 'Fungisida Dosis (ml/kali/kebun)'
                ,IF(Fungisida3=1,'Yes',IF(Fungisida3=2,'No','')) AS 'Amistar-top'
                ,IF(Fungisida6=1,'Yes',IF(Fungisida6=2,'No','')) AS 'Antila'
                ,IF(Fungisida7=1,'Yes',IF(Fungisida7=2,'No','')) AS 'Antracol'
                ,IF(Fungisida13=1,'Yes',IF(Fungisida13=2,'No','')) AS 'Benhasil'
                ,IF(Fungisida10=1,'Yes',IF(Fungisida10=2,'No','')) AS 'Cozeb'
                ,IF(Fungisida2=1,'Yes',IF(Fungisida2=2,'No','')) AS 'Dithane'
                ,IF(Fungisida1=1,'Yes',IF(Fungisida1=2,'No','')) AS 'Nordox'
                ,IF(Fungisida11=1,'Yes',IF(Fungisida11=2,'No','')) AS 'Fungicide-Organik'
                ,IF(Fungisida9=1,'Yes',IF(Fungisida9=2,'No','')) AS 'Polydor'
                ,IF(Fungisida12=1,'Yes',IF(Fungisida12=2,'No','')) AS 'Rabbat'
                ,IF(Fungisida5=1,'Yes',IF(Fungisida5=2,'No','')) AS 'Rhidomil'
                ,IF(Fungisida4=1,'Yes',IF(Fungisida4=2,'No','')) AS 'Scorpio'
                ,MerekFungisida AS 'Fungisida Merk Lainnya'
                ,IF(UseChemicalPesticideDosage=1,'Yes',IF(UseChemicalPesticideDosage=2,'No','')) AS '(C) Apakah Anda menggunakan pestisida kimia sesuai dengan dosis yang dianjurkan'
                ,IF(ApplyAltNonChemicalControlPests=1,'Yes',IF(ApplyAltNonChemicalControlPests=2,'No','')) AS '(C) Apakah Anda menerapkan cara alternatif non-kimia untuk mengendalikan hama & penyakit'
                ,IF(UseOrganicControlPests=1,'Yes',IF(UseOrganicControlPests=2,'No','')) AS '(C) Apakah Anda menggunakan pestisida alami untuk mengendalikan hama dan penyakit'
                ,IF(UseChemicalLowestToxicity=1,'Yes',IF(UseChemicalLowestToxicity=2,'No','')) AS '(C) Apakah Anda selalu menggunakan pestisida kimia yang memiliki kadar racun terendah'
                ,IF(UseChemicalLastChoice=1,'Yes',IF(UseChemicalLastChoice=2,'No','')) AS '(C) Apakah pestisida kimia hanya Anda gunakan sebagai pilihan terakhir'
                ,IF(ApplyRotationStrategy=1,'Yes',IF(ApplyRotationStrategy=2,'No','')) AS '(C) Apakah Anda menerapkan strategi rotasi pada penggunaan pestisida kimia'
                ,IF(NoticeUseInorganicFertilizer=1,'Yes',IF(NoticeUseInorganicFertilizer=2,'No','')) AS '(C) Apakah Anda mencatat penggunaan pestisida dan pupuk anorganik'
                ,IF(TrainedUseProperly=1,'Yes',IF(TrainedUseProperly=2,'No','')) AS '(C) Apakah Anda sudah dilatih untuk menggunakan pestisida dengan tepat dan aman'
                ,IF(MixPesticideLiquidFertilizer=1,'Yes',IF(MixPesticideLiquidFertilizer=2,'No','')) AS '(C) Apakah ketika Anda menyiapkan dan mencampur pestisida dan pupuk cair sesuai dengan petunjuk dosis dan keamanan pada label'
                ,IF(ExcessPesticideDisposedSafely=1,'Yes',IF(ExcessPesticideDisposedSafely=2,'No','')) AS '(C) Apakah kelebihan campuran pestisida, pupuk cair atau limbah pencucian tangki dibuang dengan aman sesuai standar internal kelompok'
                ,IF(GiveNoEntrySignAfterSpraying=1,'Yes',IF(GiveNoEntrySignAfterSpraying=2,'No','')) AS '(C) Apakah Anda memberi tanda dilarang masuk atau mematuhi waktu masuk kembali ke kebun setelah penyemprotan pestisida'
                ,IF(AdherePreHarvestInterval=1,'Yes',IF(AdherePreHarvestInterval=2,'No','')) AS '(C) Apakah Anda mematuhi jeda waktu pra-panen yang direkomendasikan untuk seluruh pestisida  yang digunakan'
                ,IF(EquipmentGoodCondition=1,'Yes',IF(EquipmentGoodCondition=2,'No','')) AS '(C) Apakah seluruh perlengkapan yang digunakan untuk pemberian pupuk dan pestisida dalam kondisi yang baik dan berfungsi sebagaimana mestinya'
                ,IF(StoreAccordanceOnLabel=1,'Yes',IF(StoreAccordanceOnLabel=2,'No','')) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Sesuai dengan petunjuk pada label'
                ,IF(StoreOriginalPackaging=1,'Yes',IF(StoreOriginalPackaging=2,'No','')) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Dalam wadah atau kemasan asli'
                ,IF(StoreIndicationSuitablePlants=1,'Yes',IF(StoreIndicationSuitablePlants=2,'No','')) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Dengan indikasi jenis tanaman yang sesuai dengan penggunaannya'
                ,IF(StoreAvoidPossibleSpill=1,'Yes',IF(StoreAvoidPossibleSpill=2,'No','')) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Terhindar dari kemungkinan tumpah'
                ,IF(StoreSecuredPlace=1,'Yes',IF(StoreSecuredPlace=2,'No','')) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Diamankan ditempat yang tidak bisa diakses anak anak'
                ,IF(StoreFarFromProducts=1,'Yes',IF(StoreFarFromProducts=2,'No','')) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Jauh dari produk yang dipanen, alat-alat, materi kemasan, dan produk-produk makanan'
                ,IF(HandlingCleanDry=1,'Yes',IF(HandlingCleanDry=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Bersih dan kering'
                ,IF(HandlingEnoughVentilationLight=1,'Yes',IF(HandlingEnoughVentilationLight=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Cukup ventilasi dan cahaya'
                ,IF(HandlingStructurallySafe=1,'Yes',IF(HandlingStructurallySafe=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Secara struktur aman'
                ,IF(HandlingAntiAbsorptive=1,'Yes',IF(HandlingAntiAbsorptive=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Dilengkapi dengan bahan anti serap'
                ,IF(HandlingLeakproofedFloor=1,'Yes',IF(HandlingLeakproofedFloor=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Lantai yang kedap suara dan anti rembes'
                ,IF(HandlingFireproofMaterial=1,'Yes',IF(HandlingFireproofMaterial=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Rak-rak yang bersifat anti-serap dan bermateri tahan api'
                ,IF(HandlingCollectSpillage=1,'Yes',IF(HandlingCollectSpillage=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat sebuah sistem untuk menampung tumpahan'
                ,IF(HandlingClearWarningSign=1,'Yes',IF(HandlingClearWarningSign=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat tanda peringatan yang jelas dan permanen ada di dekat pintu masuk'
                ,IF(HandlingFirstAidInfo=1,'Yes',IF(HandlingFirstAidInfo=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat peringatan keselamatan yang kelihatan, lambang-lambang  peringatan, gejala keracunan, dan informasi pertolongan pertama untuk setiap  produk yang disimpan'
                ,IF(HandlingProcedureEmergency=1,'Yes',IF(HandlingProcedureEmergency=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat tata cara keadaan darurat yang jelas'
                ,IF(HandlingAreaCleanEye=1,'Yes',IF(HandlingAreaCleanEye=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat area untuk membersihkan mata'
                ,IF(HandlingAccommodateLiquidStored=1,'Yes',IF(HandlingAccommodateLiquidStored=2,'No','')) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Fasilitas diberi pembatas dan mampu menampung 110% dari seluruh volume  cair yang disimpan'
                ,IF(APD=1,'Yes',IF(APD=2,'No','')) AS 'Apakah Anda menggunakan Pakaian Perlindungan Diri (PPD)'
                ,IF(TempatSimpanPestisida=1,'Didalam rumah',IF(TempatSimpanPestisida=2,'Tempat khusus pestisida',IF(TempatSimpanPestisida=3,'Diluar rumah (kawasan rumah)',IF(TempatSimpanPestisida=4,'Diluar kebun',IF(TempatSimpanPestisida=5,'Lain-lain',''))))) AS 'Dimana Anda menyimpan pestisida sebelum dan selama pemakaian'
                ,IF(BuangKemasanPestisida=1,'Di buang sembarangan (di kebun atau sekitar rumah)',IF(BuangKemasanPestisida=2,'Digunakan untuk menyimpan sesuatu',IF(BuangKemasanPestisida=3,'Dicuci dengan bersih dan dikubur',IF(BuangKemasanPestisida=4,'Dibakar',IF(BuangKemasanPestisida=5,'Daur ulang',IF(BuangKemasanPestisida=6,'Lain-lain','')))))) AS 'Apa yang Anda lakukan dengan kemasan pestisida setelah pemakaian'
                ,IF(UsePesticideInorganicFertilizer=1,'Dalam jarak 5 meter dari badan air musiman maupun permanen yang lebarnya 3 meter atau kurang',IF(UsePesticideInorganicFertilizer=2,'Dalam jarak 10 meter dari badan air musiman ataupun permanen yang lebarnya lebih dari 3 meter',IF(UsePesticideInorganicFertilizer=3,'Dalam jarak 15 meter dari mata air',IF(UsePesticideInorganicFertilizer=4,'Tidak sesuai poin A, B dan C','')))) AS '(C) Apakah Anda menggunakan pestisida dan pupuk anorganik'
                ,a.`DateCreated`
                ,h.`UserRealName` AS CreatedBy
                ,a.`DateUpdated`
                ,i.`UserRealName` AS LastModifiedBy
                    FROM
                    `ktv_farmer_garden` a
                LEFT JOIN ktv_farmer b ON b.`FarmerID`=a.`FarmerID`
                LEFT JOIN ktv_cpg c ON c.`CPGid`=b.`CPGid`
                LEFT JOIN ktv_village g ON g.`VillageID`=b.`VillageID`
                LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID`=g.SubDistrictID
                LEFT JOIN ktv_district e ON e.`DistrictID`=f.DistrictID
                LEFT JOIN ktv_province d ON d.`ProvinceID`=e.ProvinceID
                LEFT JOIN sys_user h ON h.UserId=a.`CreatedBy`
                LEFT JOIN sys_user i ON i.UserId=a.`LastModifiedBy`
                WHERE
                b.`StatusCode`='active' and (a.DateCreated between '$startEvent' and '$endEvent' or a.DateUpdated between '$startEvent' and '$endEvent') and (a.CreatedBy='{$dataListFF[$i]['UserId']}' or a.LastModifiedBy='{$dataListFF[$i]['UserId']}')
                ";
            $query = $this->db->query($sql, array());
            $dataGarden = $query->result_array();

            //3. Sertifikasi
            $sql = "SELECT
                    a.`FarmerID` as 'ID Petani'
                    ,b.`FarmerName` as 'Nama Petani'
                    ,c.`CPGid` AS 'ID Kelompok'
                    ,c.`GroupName` AS 'Kelompok Tani'
                    ,d.`Province` as 'Propinsi'
                    ,e.`District` as 'Kabupaten'
                    ,f.`SubDistrict` as 'Kecamatan'
                    ,g.`Village` as 'Desa'
                    ,a.`GardenNr` as 'Nr Kebun'
                    ,a.`SurveyNr` as 'Nr Survey'
                    ,IF(`Certification`=1,'UTZ',IF(`Certification`=2,'Rainforest',IF(`Certification`=3,'Fairtrade',IF(`Certification`=4,'Organic','')))) AS 'Program Sertifikasi'
                    ,`CertificationHolderJenis` as 'Jenis Pemegang Sertifikasi'
                    ,IF(`CertificationHolderJenis`='Organisasi Petani',(SELECT CoopName FROM `ktv_cooperatives` WHERE CoopID=`CertificationHolder`),'') AS 'Pemegang Sertifikasi'
                    ,`Year` as 'Tahun Sertifikasi'
                    ,`CandidateSelection` as 'Seleksi Kandidat'
                    ,`ICSDate` as 'Tgl Audit Internal'
                    ,`DateRevisionAudit` as 'Tgl Revisi Audit Internal'
                    ,`CommentAudit` as 'Komentar Audit'
                    ,`RecommendationAudit` as 'Rekomendasi Audit'
                    ,IF(`StatusAudit`=1,'Passed',IF(`StatusAudit`=2,'Not Passed',IF(`StatusAudit`=3,'Passed with Requirement',''))) AS 'Status Audit Internal'
                    ,`ExternalDate` as 'Tgl Audit Eksternal'
                    ,`CertificationStart` as 'Tgl Awal Sertifikasi'
                    ,`CertificationEnd` as 'Tgl Akhir Sertifikasi'
                    ,`CertificationExtension` as 'Tgl Perpanjangan Sertifikasi'
                    ,j.`PersonNm` as 'Nama Auditor'
                    ,`InspectorSignature` as 'Ttd Auditor'
                    ,k.`PersonNm` as 'Nama Komite Audit'
                    ,`AuditCommiteeSignature` as 'Ttd Komite Audit'
                    ,l.`PersonNm` as 'Nama IMS Manager'
                    ,`IMSManagerSignature` as 'Ttd IMS Manager'
                    ,`FarmerSignature` as 'Ttd Petani'
                    ,a.`DateCreated`
                    ,h.`UserRealName` AS CreatedBy
                    ,a.`DateUpdated`
                    ,i.`UserRealName` AS LastModifiedBy
                    FROM `ktv_certification` a
                    LEFT JOIN ktv_farmer b ON b.`FarmerID`=a.`FarmerID`
                    LEFT JOIN ktv_cpg c ON c.`CPGid`=b.`CPGid`
                    LEFT JOIN ktv_village g ON g.`VillageID`=b.`VillageID`
                    LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID`=g.SubDistrictID
                    LEFT JOIN ktv_district e ON e.`DistrictID`=f.DistrictID
                    LEFT JOIN ktv_province d ON d.`ProvinceID`=e.ProvinceID
                    LEFT JOIN sys_user h ON h.UserId=a.`CreatedBy`
                    LEFT JOIN sys_user i ON i.UserId=a.`LastModifiedBy`
                    left join ktv_persons j on j.`PersonID`=a.`InspectorID`
                    left join ktv_persons k on k.`PersonID`=a.`AuditCommiteeID`
                    left join ktv_persons l on l.`PersonID`=a.`IMSManagerID`
                    WHERE
                    b.`StatusCode`='active' and (a.DateCreated between '$startEvent' and '$endEvent' or a.DateUpdated between '$startEvent' and '$endEvent') and (a.CreatedBy='{$dataListFF[$i]['UserId']}' or a.LastModifiedBy='{$dataListFF[$i]['UserId']}')";
            $query = $this->db->query($sql);
            $dataSertifikasi = $query->result_array();

            //4. Audit Log
            $sql = "SELECT
                a.`FarmerID` AS 'ID Petani'
                ,b.`FarmerName` AS 'Nama Petani'
                ,c.`CPGid` AS 'ID Kelompok'
                ,c.`GroupName` AS 'Kelompok Tani'
                ,d.`Province` AS 'Propinsi'
                ,e.`District` AS 'Kabupaten'
                ,f.`SubDistrict` AS 'Kecamatan'
                ,g.`Village` AS 'Desa'
                ,a.`GardenNr` AS 'Nr Kebun'
                ,a.`SurveyNr` AS 'Nr Survey'
                ,IF(`Certification`=1,'UTZ',IF(`Certification`=2,'Rainforest',IF(`Certification`=3,'Fairtrade',IF(`Certification`=4,'Organic','')))) AS 'Program Sertifikasi'
                ,`ICSDate` AS 'Tgl Audit Internal'
                ,`DateRevisionAudit` AS 'Tgl Revisi Audit Internal'
                ,`CommentAudit` AS 'Komentar Audit'
                ,`RecommendationAudit` AS 'Rekomendasi Audit'
                ,IF(`StatusAudit`=1,'Passed',IF(`StatusAudit`=2,'Not Passed',IF(`StatusAudit`=3,'Passed with Requirement',''))) AS 'Status Audit Internal'
                ,j.`PersonNm` AS 'Nama Auditor'
                ,`InspectorSignature` AS 'Ttd Auditor'
                ,k.`PersonNm` AS 'Nama Komite Audit'
                ,`AuditCommiteeSignature` AS 'Ttd Komite Audit'
                ,l.`PersonNm` AS 'Nama IMS Manager'
                ,`IMSManagerSignature` AS 'Ttd IMS Manager'
                ,`FarmerSignature` AS 'Ttd Petani'
                ,IF(ParticipateChildEducation=1,'Yes',IF(ParticipateChildEducation=2,'No','')) AS '(C) Apakah Anda berpartisipasi dalam usaha untuk memastikan agar semua anak usia sekolah mendapatkan akses pendidikan?'
                ,IF(CutWageForDisciplinary=1,'Yes',IF(CutWageForDisciplinary=2,'No','')) AS '(C) Apakah Anda mengalami pemotongan upah kerja untuk tujuan disipliner?'
                ,IF(DoCutWageForWorker=1,'Yes',IF(DoCutWageForWorker=2,'No','')) AS '(C) Apakah Anda melakukan pemotongan upah pekerja anda dengan tujuan disipliner?'
                ,IF(WagePaidByPerformance=1,'Yes',IF(WagePaidByPerformance=2,'No','')) AS '(C) Apakah upah Anda dibayarkan sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi?'
                ,IF(PayingWorkerWageByPerformance=1,'Yes',IF(PayingWorkerWageByPerformance=2,'No','')) AS '(C) Apakah Anda membayar upah pekerja anda sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi?'
                ,IF(HandlingFirstAidInGarden=1,'Yes',IF(HandlingFirstAidInGarden=2,'No','')) AS '(C) Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan di kebun?'
                ,IF(FirstAidKitLocation=1,'Yes',IF(FirstAidKitLocation=2,'No','')) AS '(C) Apakah kotak pertolongan pertama (P3K) tersedia di pusat lokasi produk, pengolahan dan pemeliharaan?'
                ,IF(WorkerNotHandlePesticide=1,'Yes',IF(WorkerNotHandlePesticide=2,'No','')) AS '(C) Apakah Anda sudah memastikan para pengurus kelompok, anggota kelompok, dan anggota kelompok yang termasuk pekerja, yang berusia di bawah 18 tahun, atau hamil dan sedang menyusui tidak boleh menangani pestisida?'
                ,IF(WorkerAccessSafeDrinkingWater=1,'Yes',IF(WorkerAccessSafeDrinkingWater=2,'No','')) AS '(C) Apakah staf kelompok, anggota kelompok, dan anggota kelompok yang merupakan pekerja mempunyai akses terhadap air minum yang aman.'
                ,IF(BufferZoneGarden=1,'Yes',IF(BufferZoneGarden=2,'No','')) AS '(C) Di kebun ini terdapat sebuah zona penyangga berisi vegetasi asli setidaknya selebar 5 meter  dipelihara di sepanjang batas badan air musiman dan permanen untuk mengurangi erosi, membatasi pencemaran pestisida dan pupuk, dan melindungi habitat satwa liar. Di lahan yang luasnya kurang dari 2 Ha, terdapat zona penyangga  dengan lebar setidaknya 2 meter?'
                ,IF(LandOpeningForest=1,'Yes',IF(LandOpeningForest=2,'No','')) AS '(C) Apakah lahan Anda dibuat dengan membuka hutan pada tahun 2008 atau sesudahnya?'
                ,IF(LandOpeningForestCertificate=1,'Yes',IF(LandOpeningForestCertificate=2,'No','')) AS '(C) Jika lahan Anda dibuat dengan membuka hutan, apakah Anda memiliki surat kepemilikan secara resmi dari pemerintah?'
                ,IF(IdentifyProtectRareSpecies=1,'Yes',IF(IdentifyProtectRareSpecies=2,'No','')) AS '(C) Apakah Anda melakukan identifikasi dan perlindungan terhadap spesies langka dan terancam punah di sekitar Anda?'
                ,a.`DateCreated`
                ,h.`UserRealName` AS CreatedBy
                ,a.`DateUpdated`
                ,i.`UserRealName` AS LastModifiedBy
                FROM `ktv_certification_audit_log` a
                LEFT JOIN ktv_farmer b ON b.`FarmerID`=a.`FarmerID`
                LEFT JOIN ktv_cpg c ON c.`CPGid`=b.`CPGid`
                LEFT JOIN ktv_village g ON g.`VillageID`=b.`VillageID`
                LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID`=g.SubDistrictID
                LEFT JOIN ktv_district e ON e.`DistrictID`=f.DistrictID
                LEFT JOIN ktv_province d ON d.`ProvinceID`=e.ProvinceID
                LEFT JOIN sys_user h ON h.UserId=a.`CreatedBy`
                LEFT JOIN sys_user i ON i.UserId=a.`LastModifiedBy`
                LEFT JOIN ktv_persons j ON j.`PersonID`=a.`InspectorID`
                LEFT JOIN ktv_persons k ON k.`PersonID`=a.`AuditCommiteeID`
                LEFT JOIN ktv_persons l ON l.`PersonID`=a.`IMSManagerID`
                WHERE
                b.`StatusCode`='active' and (a.DateCreated between '$startEvent' and '$endEvent' or a.DateUpdated between '$startEvent' and '$endEvent') and (a.CreatedBy='{$dataListFF[$i]['UserId']}' or a.LastModifiedBy='{$dataListFF[$i]['UserId']}')";
            $query = $this->db->query($sql);
            $dataAuditLog = $query->result_array();

            //5. Post Harvest
            $sql = "SELECT
                a.`FarmerID` AS 'ID Petani'
                ,b.`FarmerName` AS 'Nama Petani'
                ,c.`CPGid` AS 'ID Kelompok'
                ,c.`GroupName` AS 'Kelompok Tani'
                ,d.`Province` AS 'Propinsi'
                ,e.`District` AS 'Kabupaten'
                ,f.`SubDistrict` AS 'Kecamatan'
                ,g.`Village` AS 'Desa'
                ,a.`SurveyNr` AS 'Nr Survey'
                ,a.`DateCollection` AS 'Tgl Interview'
                ,if(Fermentation=1,'Yes',if(Fermentation=2,'No','')) AS 'Apakah anda melakukan fermentasi biji kakao sebelum menjemur (fermentasi minimal 4 hari)'
                ,FermentationDays AS 'Jika ya, berapa hari fermentasi biji dilakukan (hari)'
                ,if(NoFermentation=1,'Tidak punya cukup waktu',if(NoFermentation=2,'Tidak punya alat',if(NoFermentation=3,'Tidak tahu caranya',if(NoFermentation=4,'Tidak menguntungkan',if(NoFermentation=5,'Malas',if(NoFermentation=6,'Lain -lain','')))))) AS 'Jika tidak, mengapa?'
                ,IF(JemurYesNo=1,'Yes',IF(JemurYesNo=2,'No','')) AS 'Apakah anda menjemur biji kakao sebelum menjual?'
                ,DryingDays AS 'Jika ya, berapa hari anda mengeringkan biji kakao (hari)'
                ,IF(SunDryingSemen=1,'Yes',IF(SunDryingSemen=2,'No','')) AS 'Pengeringan pada lantai penjemuran?'
                ,IF(SunDryingAspal=1,'Yes',IF(SunDryingAspal=2,'No','')) AS 'Pengeringan di atas aspal?'
                ,IF(DryingAlat=1,'Yes',IF(DryingAlat=2,'No','')) AS 'Pengeringan dengan alat'
                ,IF(SunDryingAlas=1,'Yes',IF(SunDryingAlas=2,'No','')) AS 'Pengeringan menggunakan alas (terpal, plastik, anyaman daun kelapa)?'
                ,IF(BeanDryHygienic=1,'Yes',IF(BeanDryHygienic=2,'No','')) AS 'Apakah anda selalu memastikan jika biji kakao anda dikeringkan dengan cara yang higienis dan terhindar dari pencemaran asap, kotoran, benda asing dll yang dapat mempengaruhi mutu?'
                ,if(TidakJemur=1,'Lebih menguntungkan menjual biji basah',IF(TidakJemur=2,'Lebih mudah dikerjakan',IF(TidakJemur=3,'Lebih cepat memperoleh uang',IF(TidakJemur=4,'Sulit menjemur karena musim hujan',IF(TidakJemur=5,'Tidak cukup waktu and perlu bantuan tenaga kerja',IF(TidakJemur=6,'Lain-lain','')))))) AS 'Jika tidak, mengapa anda tidak menjemur biji kakao?'
                ,IF(DryMoistureStandard=1,'Yes',IF(DryMoistureStandard=2,'No','')) AS '(C) Apakah biji kakao anda keringkan hingga mencapai kadar kelembaban sesuai standar kelompok?'
                ,IF(ImplementBeanRemainDry=1,'Yes',IF(ImplementBeanRemainDry=2,'No','')) AS 'Apakah anda menerapkan langkah-langkah untuk memastikan agar biji kakao tetap kering dan terhindar dari basah selama proses pengangkutan dan penyimpanan?'
                ,IF(Sortasi=1,'Yes',IF(Sortasi=2,'No','')) AS 'Apakah anda memisahkan biji berkualitas bagus dan berkualitas jelek/rendah sebelum menjualnya?'
                ,if(NoSortasi=1,'Tidak ada perbedaan harga',IF(NoSortasi=2,'Terlalu banyak menghabiskan waktu',IF(NoSortasi=3,'Tidak banyak biji berkualitas bagus',IF(NoSortasi=4,'Tidak tahu cara memisahkan biji','')))) AS 'Jika tidak, mengapa anda tidak melakukan pemisahan biji?'
                ,if(CocoaBuyers=1,'Pedagang pengumpul di kampung',if(CocoaBuyers=2,'Pedagang pengumpul di kecamatan',if(CocoaBuyers=3,'Pedagangan kabupaten/eksportir',if(CocoaBuyers=4,'Kelompok petani','')))) AS 'Biasanya menjual biji kakao kepada?'
                ,IF(AntarSendiri=1,'Yes',IF(AntarSendiri=2,'No','')) AS 'Apakah anda mengantar kakao sendiri?'
                ,Distance AS 'Jika ya, berapa jarak dari rumah anda (m)?'
                ,a.`DateCreated`
                ,h.`UserRealName` AS CreatedBy
                ,a.`DateUpdated`
                ,i.`UserRealName` AS LastModifiedBy
                FROM `ktv_farmer_post_harvest` a
                LEFT JOIN ktv_farmer b ON b.`FarmerID`=a.`FarmerID`
                LEFT JOIN ktv_cpg c ON c.`CPGid`=b.`CPGid`
                LEFT JOIN ktv_village g ON g.`VillageID`=b.`VillageID`
                LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID`=g.SubDistrictID
                LEFT JOIN ktv_district e ON e.`DistrictID`=f.DistrictID
                LEFT JOIN ktv_province d ON d.`ProvinceID`=e.ProvinceID
                LEFT JOIN sys_user h ON h.UserId=a.`CreatedBy`
                LEFT JOIN sys_user i ON i.UserId=a.`LastModifiedBy`
                WHERE
                b.`StatusCode`='active' and (a.DateCreated between '$startEvent' and '$endEvent' or a.DateUpdated between '$startEvent' and '$endEvent') and (a.CreatedBy='{$dataListFF[$i]['UserId']}' or a.LastModifiedBy='{$dataListFF[$i]['UserId']}')";
            $query = $this->db->query($sql);
            $dataPostHarvest = $query->result_array();
            //echo '<pre>'; print_r(array($dataFarmer,$dataGarden,$dataSertifikasi,$dataAuditLog,$dataPostHarvest));
            //query data yg diperlukan di Excel (begin)
            // mulai tulis file excel (begin)
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

            $mem_ini = ini_get('memory_limit');
            ini_set('memory_limit', '1048576M');

            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("PT Koltiva")
                    ->setLastModifiedBy("PT Koltiva")
                    ->setTitle("Data Collection Summary")
                    ->setSubject("Data Collection Summary")
                    ->setDescription("Data Collection Summary")
                    ->setKeywords("Data Collection Summary")
                    ->setCategory("Data Collection Summary");

            //set style
            $styleFont = array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => '9',
                ),
                'alignment' => array(
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                ),
            );

            $styleFontBold = array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => '9',
                    'bold' => true,
                ),
            );

            $styleFontBoldTitle = array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => '9',
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
            );

            $styleFontBoldHeader = array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => '9',
                    'bold' => true,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '8DB4E3'),
                ),
            );
            $styleFontBoldBgRedCenter = array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => '9',
                    'bold' => true,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'C0504D'),
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            $styleBorderFull = array(
                'borders' => array(
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );


            // Sheet Farmer (begin)
            $objWorkSheet = $objPHPExcel->createSheet(0);
            $objWorkSheet->setTitle('Farmer');

            //tulis judul
            $objWorkSheet->setCellValue('B2', 'Data Collection Summary - Farmer');
            $objWorkSheet->getStyle('B2')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet->mergeCells('B2:G2');

            $objWorkSheet->setCellValue('B3', 'Event : ' . $startEvent . ' - ' . $endEvent);
            $objWorkSheet->getStyle('B3')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet->mergeCells('B3:G3');

            if (!empty($dataFarmer)) {

                //tabel header (begin)
                $objWorkSheet->setCellValue('B5', 'No');
                $columnstart = 2;
                $dataKolom = array();
                foreach ($dataFarmer as $keyRow => $row) {
                    foreach ($row as $key => $value) {
                        $objWorkSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '5', $key);
                        $dataKolom[] = $key;
                        $columnstart++;
                    }
                    break;
                }
                $columnstart--;
                $columnstartLast = $columnstart;
                $objWorkSheet->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleFontBoldHeader);
                $objWorkSheet->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleBorderFull, false);
                //tabel header (end)

                $rowStart = 6;
                $incre = 0;
                //tabel list data (begin)
                foreach ($dataFarmer as $val) {
                    $val['no'] = $incre + 1;
                    $objWorkSheet->setCellValue('B' . $rowStart, $val['no']);

                    $columnstart = 2;
                    for ($lvl1_i = 0; $lvl1_i < count($dataKolom); $lvl1_i++) {
                        $objWorkSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$lvl1_i]]);
                        $columnstart++;
                    }

                    $objWorkSheet->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
                    $objWorkSheet->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

                    $rowStart++;
                    $incre++;
                }
                //tabel list data (end)
            } else {
                $objWorkSheet->setCellValue('B5', 'No Data');
                $objWorkSheet->getStyle('B5')->applyFromArray($styleFontBoldTitle);
                $objWorkSheet->mergeCells('B5:G5');
            }

            // Sheet Farmer (end)
            // Sheet Garden (begin)
            $objWorkSheet1 = $objPHPExcel->createSheet(1);
            $objWorkSheet1->setTitle('Garden');

            //tulis judul
            $objWorkSheet1->setCellValue('B2', 'Data Collection Summary - Garden');
            $objWorkSheet1->getStyle('B2')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet1->mergeCells('B2:G2');

            $objWorkSheet1->setCellValue('B3', 'Event : ' . $startEvent . ' - ' . $endEvent);
            $objWorkSheet1->getStyle('B3')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet1->mergeCells('B3:G3');

            //tabel header (begin)
            if (!empty($dataGarden)) {

                //tabel header (begin)
                $objWorkSheet1->setCellValue('B5', 'No');
                $columnstart = 2;
                $dataKolom = array();
                foreach ($dataGarden as $keyRow => $row) {
                    foreach ($row as $key => $value) {
                        $objWorkSheet1->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '5', $key);
                        $dataKolom[] = $key;
                        $columnstart++;
                    }
                    break;
                }
                $columnstart--;
                $columnstartLast = $columnstart;
                $objWorkSheet1->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleFontBoldHeader);
                $objWorkSheet1->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleBorderFull, false);
                //tabel header (end)

                $rowStart = 6;
                $incre = 0;

                //tabel list data (begin)
                foreach ($dataGarden as $val) {
                    $val['no'] = $incre + 1;
                    $objWorkSheet1->setCellValue('B' . $rowStart, $val['no']);

                    $columnstart = 2;
                    for ($lvl1_i = 0; $lvl1_i < count($dataKolom); $lvl1_i++) {
                        $objWorkSheet1->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$lvl1_i]]);
                        $columnstart++;
                    }

                    $objWorkSheet1->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
                    $objWorkSheet1->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

                    $rowStart++;
                    $incre++;
                }
                //tabel list data (end)
            } else {
                $objWorkSheet1->setCellValue('B5', 'No Data');
                $objWorkSheet1->getStyle('B5')->applyFromArray($styleFontBoldTitle);
                $objWorkSheet1->mergeCells('B5:G5');
            }
            //tabel header (end)
            // Sheet Garden (end)
            // Sheet Sertifikasi (begin)

            $objWorkSheet2 = $objPHPExcel->createSheet(2);
            $objWorkSheet2->setTitle('Sertifikasi');

            //tulis judul
            $objWorkSheet2->setCellValue('B2', 'Data Collection Summary - Sertifikasi');
            $objWorkSheet2->getStyle('B2')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet2->mergeCells('B2:G2');

            $objWorkSheet2->setCellValue('B3', 'Event : ' . $startEvent . ' - ' . $endEvent);
            $objWorkSheet2->getStyle('B3')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet2->mergeCells('B3:G3');

            if (!empty($dataSertifikasi)) {

                //tabel header (begin)
                $objWorkSheet2->setCellValue('B5', 'No');
                $columnstart = 2;
                $dataKolom = array();
                foreach ($dataSertifikasi as $keyRow => $row) {
                    foreach ($row as $key => $value) {
                        $objWorkSheet2->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '5', $key);
                        $dataKolom[] = $key;
                        $columnstart++;
                    }
                    break;
                }
                $columnstart--;
                $columnstartLast = $columnstart;
                $objWorkSheet2->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleFontBoldHeader);
                $objWorkSheet2->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleBorderFull, false);
                //tabel header (end)

                $rowStart = 6;
                $incre = 0;

                //tabel list data (begin)
                foreach ($dataSertifikasi as $val) {
                    $val['no'] = $incre + 1;
                    $objWorkSheet2->setCellValue('B' . $rowStart, $val['no']);

                    $columnstart = 2;
                    for ($lvl1_i = 0; $lvl1_i < count($dataKolom); $lvl1_i++) {
                        $objWorkSheet2->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$lvl1_i]]);
                        $columnstart++;
                    }

                    $objWorkSheet2->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
                    $objWorkSheet2->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

                    $rowStart++;
                    $incre++;
                }
                //tabel list data (end)
            } else {
                $objWorkSheet2->setCellValue('B5', 'No Data');
                $objWorkSheet2->getStyle('B5')->applyFromArray($styleFontBoldTitle);
                $objWorkSheet2->mergeCells('B5:G5');
            }

            // Sheet Sertifikasi (end)
            // Sheet Audit Log (begin)

            $objWorkSheet3 = $objPHPExcel->createSheet(3);
            $objWorkSheet3->setTitle('Audit Log');

            //tulis judul
            $objWorkSheet3->setCellValue('B2', 'Data Collection Summary - Audit Log');
            $objWorkSheet3->getStyle('B2')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet3->mergeCells('B2:G2');

            $objWorkSheet3->setCellValue('B3', 'Event : ' . $startEvent . ' - ' . $endEvent);
            $objWorkSheet3->getStyle('B3')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet3->mergeCells('B3:G3');

            if (!empty($dataAuditLog)) {

                //tabel header (begin)
                $objWorkSheet3->setCellValue('B5', 'No');
                $columnstart = 2;
                $dataKolom = array();
                foreach ($dataAuditLog as $keyRow => $row) {
                    foreach ($row as $key => $value) {
                        $objWorkSheet3->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '5', $key);
                        $dataKolom[] = $key;
                        $columnstart++;
                    }
                    break;
                }
                $columnstart--;
                $columnstartLast = $columnstart;
                $objWorkSheet3->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleFontBoldHeader);
                $objWorkSheet3->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleBorderFull, false);
                //tabel header (end)

                $rowStart = 6;
                $incre = 0;

                //tabel list data (begin)
                foreach ($dataAuditLog as $val) {
                    $val['no'] = $incre + 1;
                    $objWorkSheet3->setCellValue('B' . $rowStart, $val['no']);

                    $columnstart = 2;
                    for ($lvl1_i = 0; $lvl1_i < count($dataKolom); $lvl1_i++) {
                        $objWorkSheet3->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$lvl1_i]]);
                        $columnstart++;
                    }

                    $objWorkSheet3->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
                    $objWorkSheet3->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

                    $rowStart++;
                    $incre++;
                }
                //tabel list data (end)
            } else {
                $objWorkSheet3->setCellValue('B5', 'No Data');
                $objWorkSheet3->getStyle('B5')->applyFromArray($styleFontBoldTitle);
                $objWorkSheet3->mergeCells('B5:G5');
            }

            // Sheet Audit Log (end)
            // Sheet Post Harvest (begin)

            $objWorkSheet4 = $objPHPExcel->createSheet(4);
            $objWorkSheet4->setTitle('Post Harvest');

            //tulis judul
            $objWorkSheet4->setCellValue('B2', 'Data Collection Summary - Post Harvest');
            $objWorkSheet4->getStyle('B2')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet4->mergeCells('B2:G2');

            $objWorkSheet4->setCellValue('B3', 'Event : ' . $startEvent . ' - ' . $endEvent);
            $objWorkSheet4->getStyle('B3')->applyFromArray($styleFontBoldTitle);
            $objWorkSheet4->mergeCells('B3:G3');

            if (!empty($dataPostHarvest)) {

                //tabel header (begin)
                $objWorkSheet4->setCellValue('B5', 'No');
                $columnstart = 2;
                $dataKolom = array();
                foreach ($dataPostHarvest as $keyRow => $row) {
                    foreach ($row as $key => $value) {
                        $objWorkSheet4->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '5', $key);
                        $dataKolom[] = $key;
                        $columnstart++;
                    }
                    break;
                }
                $columnstart--;
                $columnstartLast = $columnstart;
                $objWorkSheet4->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleFontBoldHeader);
                $objWorkSheet4->getStyle('B5:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '5')->applyFromArray($styleBorderFull, false);
                //tabel header (end)

                $rowStart = 6;
                $incre = 0;

                //tabel list data (begin)
                foreach ($dataPostHarvest as $val) {
                    $val['no'] = $incre + 1;
                    $objWorkSheet4->setCellValue('B' . $rowStart, $val['no']);

                    $columnstart = 2;
                    for ($lvl1_i = 0; $lvl1_i < count($dataKolom); $lvl1_i++) {
                        $objWorkSheet4->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$lvl1_i]]);
                        $columnstart++;
                    }

                    $objWorkSheet4->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
                    $objWorkSheet4->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

                    $rowStart++;
                    $incre++;
                }
                //tabel list data (end)
            } else {
                $objWorkSheet4->setCellValue('B5', 'No Data');
                $objWorkSheet4->getStyle('B5')->applyFromArray($styleFontBoldTitle);
                $objWorkSheet4->mergeCells('B5:G5');
            }

            // Sheet Post Harvest (end)
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $namaFileExcelForAttach = 'ct-datasum-' . date('YmdHis') . '-' . $dataListFF[$i]['UserId'] . '.xlsx';
            $objWriter->save('files/tmp/' . $namaFileExcelForAttach);
            ini_set('memory_limit', $mem_ini);

            // mulai tulis file excel (end)
            //========================================================= Mulai Kirim Email (BEGIN) =============================================================//
            require_once 'application/third_party/phpmailer-hr/class.phpmailer.php';
            $this->config->load('email'); //$this->config->item('smtp_host');
            // Create new PHPExcel object
            $ObjMail = new PHPMailer();
            $ObjMail->IsSMTP();
            //$ObjMail->SMTPDebug = 2; // enables SMTP debug information (for testing)
            $ObjMail->SMTPSecure = 'tls';
            $ObjMail->SMTPAuth = true; // enable SMTP authentication
            $ObjMail->Host = $this->config->item('smtp_host'); // sets the SMTP server
            $ObjMail->Port = $this->config->item('smtp_port'); // set the SMTP port for the GMAIL server
            $ObjMail->Username = $this->config->item('smtp_user'); // SMTP account username
            $ObjMail->Password = $this->config->item('smtp_pass'); // SMTP account password

            $ObjMail->Priority = 0;
            $ObjMail->SetFrom($this->config->item('email_from'), 'Koltiva Support');

            $str = '';
            $str .= 'Yth. ' . $dataListFF[$i]['PersonNm'] . ',<br/><br/>';
            $str .= 'Berikut adalah Summary dari Data Collection anda untuk Event dari ' . $startEvent . ' s/d ' . $endEvent . ' <br/>';

            $str .= "<br/>Salam Hangat,<br/>";
            $str .= "<br/><br/>";
            $str .= "<br/>&copy; Cocoa Trace.<br/>";

            $ObjMail->Subject = 'PalmoilTrace Data Collection Summary (' . date('Y-m-d') . ')';
            $ObjMail->Body = $str;
            $ObjMail->IsHTML(true);

            $ObjMail->AddAddress($dataListFF[$i]['OfficialEmail'], $dataListFF[$i]['PersonNm']);

            if ($dataListFF[$i]['CcEmail'] != "") {
                $arrTmp = explode(",", $dataListFF[$i]['CcEmail']);
                foreach ($arrTmp as $key => $value) {
                    $ObjMail->AddCC($value);
                }
            }
            $ObjMail->AddCC('info@koltiva.com');

            if (file_exists('files/tmp/' . $namaFileExcelForAttach)) {
                $ObjMail->AddAttachment("files/tmp/" . $namaFileExcelForAttach);
            }

            $result = $ObjMail->Send();
            $ObjMail->ClearAddresses();
            $ObjMail->ClearAllRecipients();
            $ObjMail->IsHTML(false);
            //========================================================= Mulai Kirim Email (END)   =============================================================//
            //hapus filenya
            if (file_exists('files/tmp/' . $namaFileExcelForAttach)) {
                @unlink('files/tmp/' . $namaFileExcelForAttach);
            }
        }

        return true;
    }

    function fixMemberDisplayID() {
        $sql = "SELECT * FROM ktv_members WHERE MemberDisplayID = ''";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        if ($data) {
            foreach ($data as $key => $val) {
                $member = $this->mgrower->genMemberID($val['VillageID'], 'F');
                $update = $this->UpdateMemberImport($val['MemberID'], $member['MemberDisplayID']);
            }
        }
        exit;
    }

    function getMemberWithoutRoles() {
        $sql = "SELECT 
                a.MemberID 
              FROM
                ktv_members a 
                LEFT JOIN ktv_member_role b 
                  ON a.MemberID = b.MemberID 
              WHERE a.StatusCode = 'active' 
                AND b.MemberID IS NULL ";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        if ($data) {
            return $data;
        } else {
            return false;
        }
    }

    public function getAllProgramWithView($program = false) {

        $this->db->select('uid');
        $this->db->from('mw_program');
        if (strlen($program) > 0) {
            $this->db->where('uid', $program);
        }
        $this->db->where('function_view IS NOT NULL', null, false);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            return $Q->result_array();
        }

        return false;
    }
    public function syncDataPerProgram($data, $program = false) {

        ini_set('display_errors', true);
        error_reporting('E_ALL');

        $program = $this->_getProgramByUid($program);

        //count($data);die;
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $keys => $value) {

                $elements = array();
                $uid = false;
                $cuid = false;
                $long = false;
                $lat = false;
                $user = 'admin';

                foreach ($value as $key => $element) {
                    if (!in_array($key, array('id', 'MemberID', 'PlotNr', 'SurveyNr', 'primarykeys', 'uid', 'cuid', 'long', 'lat', 'username', 'ApplicantID'))) {
                        array_push($elements, array(
                            'dataElement' => $key,
                            'value' => $element
                        ));
                    }

                    if ($key == 'id') {
                        $id = $element;
                    }

                    if ($key == 'MemberID') {
                        $MemberID = $element;
                    }
                    
                    if ($key == 'ApplicantID') {
                        $ApplicantID = $element;
                    }

                    if ($key == 'PlotNr') {
                        $PlotNr = $element;
                    }

                    if ($key == 'SurveyNr') {
                        $SurveyNr = $element;
                    }

                    if ($key == 'primarykeys') {
                        $primarykeys = $element;
                    }

                    if ($key == 'uid') {
                        $uid = $element;
                    }

                    if ($key == 'cuid') {
                        $cuid = $element;
                    }

                    if ($key == 'long') {
                        $long = $element;
                    }

                    if ($key == 'lat') {
                        $lat = $element;
                    }

                    if ($key == 'username') {
                        $user = $element;
                    }
                }

                $payloads = array('name' => 'events');

                $header = array(
                    'program' => $program['uid'],
                    'orgUnit' => $cuid,
                    'eventDate' => date('Y-m-d'),
                    'status' => 'COMPLETED',
                    'storedBy' => $user
                );

                //array_push($payloads,$header);
                $pl = json_encode(array('events' => $payloads), JSON_NUMERIC_CHECK);
                $el = array('name' => 'dataValues');

                foreach ($elements as $elekey => $elementval) {
                    array_push($el, array(
                        'name' => 'dataValue',
                        'attributes' => array(
                            'dataElement' => $elementval['dataElement'],
                            'value' => $elementval['value']
                        )
                    ));
                }

                $data = array(
                    'name' => 'event',
                    'attributes' => $header,
                    array(
                        'name' => 'coordinate',
                        'attributes' => array(
                            'latitude' => $lat,
                            'longitude' => $long
                        )
                    ),
                    $el
                );
                if($program['uid']=='nQxNqbkCil1'){ //untuk plantation
                    $arrPrimaryKey = array('MemberID'=>$MemberID, 'PlotNr'=>$PlotNr, 'SurveyNr'=>$SurveyNr);
                } else if($program['uid']=='eBCX1KfaDmA'){ //untuk farmer registration
                    $arrPrimaryKey = array('ApplicantID'=>$ApplicantID);
                } else {
                    $arrPrimaryKey = array('MemberID'=>$MemberID);
                }
                $dhis = $this->_curlItOut($data, $program['uid'], $cuid, $arrPrimaryKey, $uid);
            }
        }
    }
    public function getDataBy($onlyNew = false, $program, $id = false) {

        ini_set('display_errors', true);
        error_reporting('E_ALL');

        //off with the limit to milions of data
        ini_set('memory_limit', -1);

        //get program view by program uid
        $program = $this->_getProgramByUid($program);
        
        $this->db->select('*');
        $this->db->from($program['function_view']);

        //for certain id
        if (count($id) > 0) {
            if($program['uid']=='nQxNqbkCil1'){ //khusus plantation
                $this->db->where($program['function_view'] . '.MemberID', $id['MemberID'], false);
                $this->db->where($program['function_view'] . '.PlotNr', $id['PlotNr'], false);
                $this->db->where($program['function_view'] . '.SurveyNr', $id['SurveyNr'], false);
            } else if($program['uid'] == 'eBCX1KfaDmA'){ // khusus farmer registration
                $this->db->where($program['function_view'] . '.ApplicantID', $id['ApplicantID'], false);
            } else {
                $this->db->where($program['function_view'] . '.MemberID', $id['MemberID'], false);
            }
        }

        //only data which is not in dhis, yet
        if ($onlyNew == true) {
            $this->db->where($program['function_view'] . '.uid IS NULL', null, false);
        }

        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();
            return $result;
        }
        // echo "<pre>";
        // print_r($this->db->last_query());
        // echo "</pre>";
        
        return false;
    }

    private function _getProgramByUid($uid = false) {

        $this->db->select('programid,uid,function_view');
        $this->db->from('mw_program');
        if ($uid) {
            $this->db->where('uid', $uid);
        }
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row_array();
            $Q->free_result();
            return $row;
        }
        return false;
    }
    private function _curlItOut($payload, $program, $orgunit, $arrPrimaryKey, $uid = false) {

        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        // $doc = new DOMDocument();

        // $child = $this->generate_xml_element($doc, $payload);
        // if ($child) {
        //     $doc->appendChild($child);
        // }
        // $doc->formatOutput = true; // Add whitespace to make easier to read XML
        // $xml = (string) $doc->saveXML();
        // echo "<pre>";
        // print_r($payload);
        // echo "</pre>";
        // exit;
        //Susun JSON ===================== (Begin)
        $ArrayJsonKirim = array();
        $ArrayDatEl = array();

        $ArrayJsonKirim['program'] = $payload['attributes']['program'];
        $ArrayJsonKirim['orgUnit'] = $payload['attributes']['orgUnit'];
        $ArrayJsonKirim['eventDate'] = $payload['attributes']['eventDate'];
        $ArrayJsonKirim['status'] = $payload['attributes']['status'];
        $ArrayJsonKirim['storedBy'] = $payload['attributes']['storedBy'];

        $IncreDatEl = 0;
        foreach ($payload[1] as $key => $value) {
            if($value == 'dataValues') continue;

            $ArrayDatEl[$IncreDatEl]['dataElement'] = $value['attributes']['dataElement'];
            $ArrayDatEl[$IncreDatEl]['value'] = $value['attributes']['value'];

            $IncreDatEl++;
        }
        $ArrayJsonKirim['dataValues'] = $ArrayDatEl;
    //    echo "<pre>";
    //     print_r($ArrayJsonKirim);
    //     echo "</pre>";
    //     exit;
        //Susun JSON ===================== (End)
        
        $urldhis = $this->config->item('dhis_url');

        //untuk testing
        // $urldhis = "https://mobile.seaweedtrace.com/";

        if ($uid) {
            $action = 'PUT';
            $url = $urldhis . 'api/events/' . $uid;
        } else {
            $url = $urldhis . 'api/events';
            $action = 'POST';
        }

        // $this->load->helper('file');

        $dhispassword = 'Basic YWRtaW46S29sdGl2YTIwMTMh'; //$this->config->item('dhispasswd');
        // $pwd = $this->config->item('postgrepwd');
        // $dhispassword = pack("H*", $pwd);

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: '.$dhispassword
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, (json_encode($ArrayJsonKirim)) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        
        $curlresult = json_decode($result,true);
//        echo "<pre>";
//        print_r($curlresult);
//        echo "</pre>";
        // exit;

        if (is_array($curlresult) && !$uid) {
            $this->updateUID($curlresult, $program, $arrPrimaryKey);
        }
        // echo "<pre>";
        // print_r($curlresult);
        // echo "</pre>";
        // exit;
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/xml',
        //     'Authorization: ' . $dhispassword
        // ));

        // curl_setopt($ch, CURLOPT_POSTFIELDS, (strval($xml)));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $result = curl_exec($ch);

        // if (curl_errno($ch)) {
        //     print curl_error($ch);
        // } else {
        //     $curlresult = json_decode($result, true);
        //     if (is_array($curlresult) && !$uid) {
        //         $this->updateUID($curlresult, $program, $id);
        //     }
        // }

        // echo '<pre>';
        // echo($xml);
        // echo($result);
        // echo "\n";
        // echo "\n";exit;
    }
    public function updateUID($curlResult, $program, $arrPrimaryKey) {
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $this->db->select('reference');
        $this->db->from('mw_program');
        $this->db->where('uid', $program);
        $table = $this->db->get();
        if ($table->num_rows() > 0) {
            $row = $table->row();
            $table = $row->reference;
        }

        // switch ($table) {
        //     case 'ktv_members':
        //         $primary = array('MemberID' => $id);
        //         break;
        // }

        $this->db->where($arrPrimaryKey);
        $this->db->set('uid', $curlResult['response']['importSummaries'][0]['reference']);
        $this->db->update($table);
        if (strlen($this->db->_error_message()) == 0) {
            //dikomen dl, tidak usah update ke MemberUID, hanya ke uid aja untuk farmer
            // if ($table == 'ktv_members') {
            //     $sql = "UPDATE ktv_members a SET a.MemberUID = ? WHERE MemberID = ? LIMIT 1";
            //     $query = $this->db->query($sql, array($curlResult['response']['importSummaries'][0]['reference'], $id));
            // }
            return true;
        }

        return false;
    }

    public function getAllProgramWithViewWAGS($program = false) {
        $where = "function_view IS NOT NULL";
        $param = [];
        if (strlen($program) > 0) {
            $where .= " AND `uid` = ?";
            $param[] = $program;
        }
        $sql = "SELECT `uid` FROM mw_program WHERE $where";
        $Q = $this->db->query($sql, $param);
        if ($Q->num_rows() > 0) {
            return $Q->result_array();
        }

        return false;
    }
}

/* End of file mmiddleware.php */
/* Location: ./application/models/mmiddleware.php */