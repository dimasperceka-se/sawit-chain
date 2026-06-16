<?php

/* * ****************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jan 10 2019
 *  File : mtools.php
 * ***************************************** */

class Mfarmers extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function ImportFarmersGridMain($KeySearch, $start, $limit, $sortingField, $sortingDir) {
        if ($sortingField == "") {
            $sortingField = 'FarmerName';
        }

        if ($sortingDir == "") {
            $sortingDir = 'DESC';
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.`FarmerName`
                , IFNULL(a.`Birthdate`, 'NotValid') AS Birthdate
                , (CASE WHEN Gender = 'm' THEN 'Male' WHEN Gender = 'f' THEN 'Female' ELSE 'NotValid' END) AS Gender
                , IFNULL(vil.`VillageID`,'NotValid') AS VillageID
                , IFNULL(vil.Village,'NotValid') AS Village
                , IFNULL(partner.`PartnerID`,'NotValid') AS PartnerID
                , IFNULL(partner.`PartnerName`, 'NotValid') AS PartnerName
            FROM
                ktv_farmer_upload_temp a
            LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
            LEFT JOIN ktv_program_partner partner ON a.`PartnerID` = partner.`PartnerID`
            WHERE
                1=1 AND a.FarmerName LIKE ?
            ORDER BY a.`FarmerName` ASC";
        $p = array(
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

    public function ImportDataFarmerTabelTemp($DataInsert) {
        $this->db->trans_begin();

        //Delete dl datanya
        $sql = "DELETE FROM `ktv_farmer_upload_temp`";
        $query = $this->db->query($sql);

        for ($i = 0; $i < count($DataInsert); $i++) {
            $sql = "INSERT INTO ktv_farmer_upload_temp SET
					FarmerName = ?,
					Birthdate = ?,
					Gender = ?,
					VillageID = ?,
					PartnerID = ?,
					CreatedBy = ?,
					DateCreated = NOW()";
            $p = array(
                $DataInsert[$i]['FarmerName'],
                $DataInsert[$i]['Birthdate'],
                $DataInsert[$i]['Gender'],
                $DataInsert[$i]['VillageID'],
                $DataInsert[$i]['PartnerID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Transaction proses insert ke tabel temp gagal";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }

        return $results;
    }

    public function genMemberID($VillageID, $prefixId = 'F') {
        //MemberID
        $sql = "SELECT a.MemberID
                FROM ktv_members a
                ORDER BY a.`MemberID` DESC
                LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if ($data['MemberID'] != "") {
            $return['MemberID'] = $data['MemberID'] + 1;
        } else {
            $return['MemberID'] = 1;
        }
        //MemberDisplayID
        $awalan = $prefixId . substr($VillageID, 0, 7);
        $sql = "SELECT a.`MemberDisplayID`
                FROM ktv_members a
                WHERE a.`MemberDisplayID` LIKE '$awalan%'
                ORDER BY a.`MemberDisplayID` DESC
                LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if (isset($data['MemberDisplayID']) && $data['MemberDisplayID'] != "") {
            $temp = (int) substr($data['MemberDisplayID'], -4);
            $temp++;

            switch (strlen($temp)) {
                case '1':
                    $temp = $awalan . "000" . $temp;
                    break;
                case '2':
                    $temp = $awalan . "00" . $temp;
                    break;
                case '3':
                    $temp = $awalan . "0" . $temp;
                    break;
                default:
                    $temp = $awalan . $temp;
                    break;
            }
            $return['MemberDisplayID'] = $temp;
        } else {
            $return['MemberDisplayID'] = $awalan . "0001";
        }

        return $return;
    }

    public function getPartnerMemberByDistrict($VillageID) {
        $DistrictID = substr($VillageID, 0, 4);

        $sql = "SELECT
                a.`PartnerID`
            FROM
                ktv_district_partner_member a
            WHERE
                a.`DistrictID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($DistrictID));
        $data = $query->row_array();
        if (isset($data['PartnerID'])) {
            return $data['PartnerID'];
        } else {
            return false;
        }
    }

    public function getVillageByVillageID($VillageID) {
        $sql = "SELECT a.VillageID, b.DistrictID FROM ktv_village a
                JOIN ktv_subdistrict b ON a.SubDistrictID=b.SubDistrictID
                WHERE VillageID = ? LIMIT 1";
        $query = $this->db->query($sql, array($VillageID));
        $data = $query->row_array();
        if (isset($data['VillageID'])) {
            return $data;
        } else {
            return false;
        }
    }

    public function getPartnerIDByDistrictID($DistrictID) {
        $sql = "SELECT ktv_district_partner.PartnerID FROM ktv_district_partner
                JOIN ktv_program_partner ON ktv_district_partner.PartnerID=ktv_program_partner.PartnerID
                WHERE DistrictID = ? AND PartnerIndustry != '3'";
        $query = $this->db->query($sql, array($DistrictID));
        $data = $query->result_array();
        return $data;
    }

    public function getPartnerByPartnerID($PartnerID) {
        $sql = "SELECT a.`PartnerID` FROM ktv_program_partner a WHERE a.`PartnerID` = ? LIMIT 1";
        $query = $this->db->query($sql, array($PartnerID));
        $data = $query->row_array();
        if (isset($data['PartnerID'])) {
            return $data['PartnerID'];
        } else {
            return false;
        }
    }

    public function getPartnerSurveyByPartnerID($PartnerID) {
        $sql = "SELECT
                GROUP_CONCAT(a.`SurveyName` SEPARATOR ',') AS PartnerSurvey
                FROM ktv_program_partner_survey a
                WHERE a.`PartnerID` = ?";
        $query = $this->db->query($sql, array($PartnerID));
        $data = $query->row_array();
        return $data['PartnerSurvey'];
    }

    private function _getProgramByUid($uid = false) {
        $this->db->select('programid,uid,description');
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

    private function _getOrgUnitByDistrictID($id) {
        $this->db->select('mw_organisationunit.uid', false);
        $this->db->from('mw_organisationunit');
        $this->db->join('ktv_district', 'ktv_district.District = mw_organisationunit.name', 'left');
        $this->db->where('ktv_district.DistrictID', $id);
        $Q = $this->db->get(); //var_dump($this->db->_error_message());die;
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->uid;
        }

        return false;
    }

    private function _curlItOutFarmer($payload, $program, $orgunit, $farmerID, $uid = false, $primary = '', $partner = false) {
        //echo '<pre>'; print_r(array($uid,$payload)); exit;
        //echo '<pre>'; print_r($payload[1]); exit;
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
            if ($value == 'dataValues')
                continue;

            $ArrayDatEl[$IncreDatEl]['dataElement'] = $value['attributes']['dataElement'];
            $ArrayDatEl[$IncreDatEl]['value'] = $value['attributes']['value'];

            $IncreDatEl++;
        }
        $ArrayJsonKirim['dataValues'] = $ArrayDatEl;
        //echo '<pre>'; print_r(json_encode($ArrayJsonKirim)); exit;
        //Susun JSON ===================== (End)
        //$urldhis = $this->config->item('dhis_url_https');
        $urldhis = $this->config->item('dhis_url');
        if ($uid) {
            $action = 'PUT';
            $url = $urldhis . 'api/events/' . $uid;
        } else {
            $url = $urldhis . 'api/events';
            $action = 'POST';
        }

        $basicAuth = 'YWRtaW46S29sdGl2YTIwMTMh';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $basicAuth
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, (json_encode($ArrayJsonKirim)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $curlresult = json_decode($result, true);
        //echo '<pre>'; print_r($curlresult); exit;

        if (is_array($curlresult) && !$uid) {
            $this->updateFarmerUID($curlresult, $payload['attributes']['program'], $orgunit, $farmerID);
        }
    }

    public function updateFarmerUID($curlResult, $program, $orgunit, $farmerID) {
        if ($program == 'QxauNvjcpBw') { //Program Farmer
            $this->db->where('FarmerID', $farmerID);
            $this->db->set('uid', $curlResult['response']['importSummaries'][0]['reference']);
            $this->db->update('ktv_cocoa_farmer');
            if (strlen($this->db->_error_message()) == 0) {
                return true;
            }
        }
        if ($program == 'Cdm1W32kWhW') { //Program Waseda
            $this->db->where('FarmerID', $farmerID);
            $this->db->set('uid2', $curlResult['response']['importSummaries'][0]['reference']);
            $this->db->update('ktv_cocoa_farmer');
            if (strlen($this->db->_error_message()) == 0) {

                //Insert ke Farmer Access Waseda
                $sql = "INSERT INTO `ktv_farmer_access` (FarmerID,PartnerID,DateGenerated,GeneratedBy)
                        VALUES (?,52,NOW(),'1')
                        ON DUPLICATE KEY UPDATE
                            DateGenerated = NOW(),
                            GeneratedBy = '1'";
                $query = $this->db->query($sql, array($farmerID));
                return true;
            }
        }
        return false;
    }

    public function getDataByDistrictFarmer($district = false, $program, $farmer = false, $partner = false) {
        ini_set('display_errors', true);
        error_reporting('E_ALL');

        //off with the limit to milions of data
        ini_set('memory_limit', -1);

        //get program view by program uid
        $program = $this->_getProgramByUid($program);

        //if parameter partner is defined
        if ($partner) {
            $program['description'] = $program['description'] . '_' . $partner;
        }

        $this->db->select($program['description'] . '.*');
        $this->db->from('ktv_farmer');
        $this->db->join('ktv_village', 'ktv_village.VillageID = ktv_farmer.VillageID', 'INNER');
        $this->db->join('ktv_subdistrict', 'ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID', 'INNER');
        $this->db->join($program['description'], $program['description'] . '.FarmerID = ktv_cocoa_farmer.FarmerID', 'INNER');

        //for certain farmer
        if (strlen($farmer) > 0) {
            $this->db->where($program['description'] . '.FarmerID', $farmer, false);
        }

        //for certain district
        if (strlen($district) > 0) {
            $this->db->where('DistrictID', $district);
        }

        $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();
            return $result;
        }

        return false;
    }

    public function syncDataPerFarmer($data, $program = false, $district, $partner = false) {
        ini_set('display_errors', true);
        error_reporting('E_ALL');

        $program = $this->_getProgramByUid($program);
        $OrgUnit = $this->_getOrgUnitByDistrictID($district);

        //count($data);die;
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $keys => $value) {

                $farmerID = false;
                $elements = array();
                $primary = '';
                $uid = false;
                $long = '';
                $lat = '';

                foreach ($value as $key => $element) {
                    if (!in_array($key, array('FarmerID', 'uid', 'primarykeys', 'DateSync'))) {
                        array_push($elements, array(
                            'dataElement' => $key,
                            'value' => $element
                        ));
                    }

                    if ($key == 'FarmerID') {
                        $farmerID = $element;
                    }

                    if ($key == 'primarykeys') {
                        $primary = $element;
                    }

                    if ($key == 'uid') {
                        $uid = $element;
                    }
                }

                if ($program['uid'] == '') {
                    $long = $this->getLongitudeByPrimary($primary);
                    $lat = $this->getLatitudeByPrimary($primary);
                }

                $payloads = array('name' => 'events');

                $header = array(
                    'program' => $program['uid'],
                    'orgUnit' => $OrgUnit,
                    'eventDate' => date('Y-m-d'),
                    'status' => 'COMPLETED',
                    'storedBy' => 'admin'
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

                //var_dump($el);die;
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
                //var_dump($farmerID).'<br>';
                $dhis = $this->_curlItOutFarmer($data, $program['uid'], $OrgUnit, $farmerID, $uid, $primary, $partner);
            }
        }
    }

    private function insertDHIS($prog, $memberid, $onlyNew = 'false') {
        //Begin Insert ke DHIS
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $this->load->model('mmiddleware');
        if ($onlyNew === 'true') {
            $onlyNew = true;
        } else {
            $onlyNew = false;
        }
        $programs = $this->mmiddleware->getAllProgramWithView($prog);
        print_r($programs);
        if (count($programs) > 0) {
            foreach ($programs as $progkeys => $program) {
                $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $memberid);
                $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
            }
        }
        // End Insert Ke DHIS
    }

    public function ImportMemberExcel() {
        $this->db->trans_begin();

        $sql = "SELECT FarmerName, Birthdate, Gender, VillageID, PartnerID
                FROM `ktv_farmer_upload_temp`";
        $DataFarmerTemp = $this->db->query($sql)->result_array();

        if (isset($DataFarmerTemp[0]['FarmerName'])) {
            for ($i = 0; $i < count($DataFarmerTemp); $i++) {

                //generate MemberID dan MemberDisplayID
                $id = $this->genMemberID($DataFarmerTemp[$i]['VillageID'], 'F');

                //validate birtdate
                if ($DataFarmerTemp[$i]['Birthdate'] == NULL) {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = "Format Birtdate NotValid";
                    return $results;
                }
                //validate Gender
                if ($DataFarmerTemp[$i]['Gender'] == 'm' || $DataFarmerTemp[$i]['Gender'] == 'f') {
                    
                } else {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = "Gender Not Valid";
                    return $results;
                }
                //validate VillageID
                $VillageID = $this->getVillageByVillageID($DataFarmerTemp[$i]['VillageID']);
                if ($VillageID == false) {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = "VillageID not found in data";
                    return $results;
                }
                //validate PartnerID
                $PartnerID = $this->getPartnerByPartnerID($DataFarmerTemp[$i]['PartnerID']);
                if ($PartnerID == false) {
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = "No Partner assign to this farmer district yet";
                    return $results;
                }

                //Tabel Farmer
                $sql = "INSERT INTO ktv_members SET
                            MemberID = ?, MemberUID = ?, MemberDisplayID = ?, MemberName = ?,
                            DateCollection = NOW(), VillageID = ?, Gender = ?,
                            DateOfBirth = ?, PartnerID = ?, DateCreated = NOW(), CreatedBy = ?";
                $p = array(
                    $id['MemberID'], $id['MemberDisplayID'], $id['MemberDisplayID'],
                    $DataFarmerTemp[$i]['FarmerName'], $DataFarmerTemp[$i]['VillageID'],
                    $DataFarmerTemp[$i]['Gender'], $DataFarmerTemp[$i]['Birthdate'],
                    $DataFarmerTemp[$i]['PartnerID'], $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);

                //ktv_members_extension
                $sql = "INSERT INTO `ktv_members_extension` SET
                        `MemberID` = ?, `DateCreated` = NOW(), `CreatedBy` = ?";
                $p = array($id['MemberID'], $_SESSION['userid']);
                $query = $this->db->query($sql, $p);

                $arrRole = array();
                $arrRole[] = 1;
                foreach ($arrRole as $key => $value) {
                    $sql = "INSERT INTO `ktv_member_role` SET
                                    `MemberID` = ?, `MRoleID` = ?, `DateCreated` = NOW(), `CreatedBy` = ?";
                    $p = array(
                        $id['MemberID'],
                        $value,
                        $_SESSION['userid']
                    );
                    $query = $this->db->query($sql, $p);
                }

                //insert hak akses data control (Begin)
                //insertkan ke Koltiva
                $sql = "INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?, `apmMemberID` = ?, `DateCreated` = NOW(), `CreatedBy` = ?";
                $p = array(
                    '1',
                    $id['MemberID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
                //insertkan ke PartnerID yg diexcel
                if ($DataFarmerTemp[$i]['PartnerID'] != '1') {
                    //insertkan ke Partner
                    $sql = "INSERT INTO `ktv_access_partner_member` SET
                            `apmPartnerID` = ?, `apmMemberID` = ?, `DateCreated` = NOW(), `CreatedBy` = ?";
                    $p = array(
                        $DataFarmerTemp[$i]['PartnerID'],
                        $id['MemberID'],
                        $_SESSION['userid']
                    );
                    $query = $this->db->query($sql, $p);
                }
                $partner = $this->getPartnerIDByDistrictID($VillageID['DistrictID']);
                foreach ($partner as $k => $v) {
                    if ($v['PartnerID'] != '1' && $v['PartnerID'] != $DataFarmerTemp[$i]['PartnerID']) {
                        //insertkan ke PartnerID yang di district tersebut
                        $sql = "INSERT INTO `ktv_access_partner_member` SET
                                `apmPartnerID` = ?, `apmMemberID` = ?, `DateCreated` = NOW(), `CreatedBy` = ?";
                        $p = array($v['PartnerID'], $id['MemberID'], $_SESSION['userid']);
                        $query = $this->db->query($sql, $p);
                    }
                }
                //insert hak akses data control (End)
                
                //Delete di tabel temporary
                $sql = "DELETE FROM `ktv_farmer_upload_temp`
                        WHERE FarmerName = ? AND Gender = ? AND PartnerID = ? AND VillageID = ? AND Birthdate = ?";
                $p = array(
                    $DataFarmerTemp[$i]['FarmerName'], $DataFarmerTemp[$i]['Gender'], $DataFarmerTemp[$i]['PartnerID'],
                    $DataFarmerTemp[$i]['VillageID'], $DataFarmerTemp[$i]['Birthdate']
                );
                $query = $this->db->query($sql, $p);
                //insertDHIS       
                $mid[$i] = $id['MemberID'];
            }
        } else {
            $results['success'] = false;
            $results['message'] = "Tidak ada data ditabel farmer temporary";
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Transaction proses insert ke tabel temp gagal";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['mid'] = $mid;
            $results['message'] = "Calon petani sudah terimport menjadi petani";
        }
        return $results;
    }

}
