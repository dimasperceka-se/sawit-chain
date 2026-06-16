<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mdsync extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('curl');
    }

    public function getData($start = 0, $limit = 50, $all = false, $district = false, $production = 0, $landsize = 0, $yearcertification = 0, $name = '', $nameop = 'like', $prodop = '=', $landop = '=', $yearop = '=') {

        //filter by
        $where = array();
        $having = array();
        $filter = '';
        $filter2 = '';
        $limits = "";
        $all = true;
        if (!$all) {
            $limits = " LIMIT " . $start . "," . $limit;
        }


        //operators
        switch ($landop) {
            case 'equal':
                $nameop = '=';
                break;
            case 'fewer':
                $nameop = '<';
                break;
            case 'greater':
                $nameop = '>';
                break;
        }

        switch ($prodop) {
            case 'equal':
                $nameop = '=';
                break;
            case 'fewer':
                $nameop = '<';
                break;
            case 'greater':
                $nameop = '>';
                break;
        }
        //var_dump($nameop);die;
        if ($nameop == 'equal') {
            $nameop = '=';
        } elseif ($nameop == 'like') {
            $name = "%" . $name . "%";
        } else {
            $nameop = 'like';
            $name = "%" . $name . "%";
        }

        if ($district) {
            array_push($where, " ks.DistrictID = '" . $district . "'");
        }

        if ($production > 0) {
            array_push($having, " Production " . $prodop . " '" . $production . "'");
        }

        if ($landsize > 0) {
            array_push($having, " Landsize " . $landop . " '" . $landsize . "'");
        }

        if ($yearcertification > 0) {
            array_push($having, " YearCertification " . $yearop . " '" . $yearcertification . "'");
        }

        if (strlen($name) > 0) {
            array_push($where, " (kcf.FarmerID " . $nameop . " '" . $name . "' OR FarmerName " . $nameop . " '" . $name . "')");
        }

        if (count($having) > 0) {
            $filter = " HAVING " . implode(" AND ", $having);
        }

        if (count($where) > 0) {
            $filter2 = " WHERE " . implode(" AND ", $where);
        }

        //get farmer
        $sql = "SELECT SQL_CALC_FOUND_ROWS kcf.FarmerID, kcf.FarmerName, kcf.CPGid, kc.GroupName, kv.Village, ks.SubDistrict, kd.District, kp.Province, SUM(kcfg.GardenHaUnCertified) LandSize,SUM( ( kcfg.PanenTrekMonths * kcfg.PanenTrekPanenMonth * kcfg.PanenTrekKg) + ( kcfg.PanenBiasaMonths * kcfg.PanenBiasaPanenMonth * kcfg.PanenBiasaKg ) + ( kcfg.PanenRayaMonths * kcfg.PanenRayaPanenMonth * kcfg.PanenRayaKg ) ) Production,'2016' AS YearCertification, candidates.DateSynced as Synced, kcf.DateUpdated as LastUpdated FROM ktv_cocoa_farmer kcf LEFT JOIN ktv_certification_candidates candidates ON candidates.FarmerID = kcf.FarmerID LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = ks.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN ( SELECT FarmerID, GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID, GardenNr ) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr AND kcf.StatusCode = 'active' " . $filter2 . " GROUP BY kcf.FarmerID " . $filter . " " . $limits;
        $results = $this->db->query($sql);
        if ($this->db->_error_number()) {
            var_dump($this->db->_error_message());
            die;
        }
        $results = $results->result_array();
        $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

        return array('data' => $results, 'total' => $total);
    }

    private function _buildPayload($data, $table) {

        //ambil program utk farmer -> array('uid' => <string>, 'programid' => <int>)
        $farmerProgram = $this->_getProgramByShortName($table);
        //var_dump($farmerProgram);
        //ambil orgunit district -> array('uid' => <string>, 'orgunit' => <int>)
        $OrgUnit = $this->_getOrgUnitByFarmerID($data);
        //var_dump($OrgUnit);die;
        //ambil data element
        if ($farmerProgram) {
            /*
              $dataElement = json_decode('[{"dataElement":"MCV7rsw4Oob","value":"0"},{"dataElement":"F273IuLVLqh","value":"0"},{"dataElement"
              :"XIdrwXwLC5m","value":"0"},{"dataElement":"LNfnqFBqZOw","value":"0"},{"dataElement":"uFHQfB5Ppj0","value"
              :"0"},{"dataElement":"l0Wnl8JA0od","value":"0"},{"dataElement":"VxbY49zNTrA","value":"0"},{"dataElement"
              :"NSW41QD09As","value":"1"},{"dataElement":"WSgxCBozxdW","value":"110400001"},{"dataElement":"VgJ4ejmskx5"
              ,"value":"3"},{"dataElement":"XNPJnYlvDFj","value":"0"},{"dataElement":"OBezgbcRc7A","value":"1"},{"dataElement"
              :"v6w9XsJxIGO","value":"0"},{"dataElement":"cp3ysvbiMIO","value":"100"},{"dataElement":"oAXtDalvOSJ"
              ,"value":"1.50"},{"dataElement":"i4ugDBrguKA","value":"1997"},{"dataElement":"YWnddAFkQzj","value":"0"
              },{"dataElement":"zw3ud3Slqta","value":"600"},{"dataElement":"p5s7PVH2q4u","value":"350"},{"dataElement"
              :"pmtWKqGj0h1","value":"0"},{"dataElement":"G146YbSBTvA","value":"0"},{"dataElement":"xBXAHfkORWA","value"
              :"0"},{"dataElement":"NCm7EJMfyjv","value":"0"},{"dataElement":"zvS2Q7YHiXE","value":"0"},{"dataElement"
              :"MPkn86jBEBM","value":"0"},{"dataElement":"YHBjJ1oVdUb","value":"0"},{"dataElement":"evfOza1yJh7","value"
              :"350"},{"dataElement":"Y9UUoVknphf","value":"0"},{"dataElement":"QX9j3Mu9rVr","value":"0"},{"dataElement"
              :"HjzBVQLs3n4","value":"0"},{"dataElement":"Kda5ioTIYRc","value":"0"},{"dataElement":"zbaFsCVY61m","value"
              :"0"},{"dataElement":"d4mRY6KNU7s","value":"0"},{"dataElement":"T8pZME2OHRt","value":"0"},{"dataElement"
              :"UWqDqS5Byub","value":null},{"dataElement":"fT4NrXMKcBr","value":null},{"dataElement":"BgtU1HDQn3J"
              ,"value":null},{"dataElement":"EOU7czDlxL8","value":null},{"dataElement":"aarAEhQsM4B","value":null}
              ]',true);
             */
            $dataElement = $this->_getDataElementByProgram($farmerProgram['programid'], $farmerProgram['description'], $data);

            $header = array(
                'program' => $farmerProgram['uid'],
                'orgUnit' => $OrgUnit,
                'eventDate' => date('Y-m-d'),
                'status' => 'COMPLETED',
                'storedBy' => 'admin',
                'coordinate' => array(
                    'latitude' => '',
                    'longitude' => ''
                ),
                'dataValues' => $dataElement
            );
            if (count($dataElement) > 0) {
                return $header;
            }
        }

        return false;
    }

    public function generate_xml_element($dom, $data) {
        if (empty($data['name']))
            return false;

        // Create the element
        $element_value = (!empty($data['value']) ) ? $data['value'] : null;
        $element = $dom->createElement($data['name'], $element_value);

        // Add any attributes
        if (!empty($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attribute_key => $attribute_value) {
                $element->setAttribute($attribute_key, $attribute_value);
            }
        }

        // Any other items in the data array should be child elements
        foreach ($data as $data_key => $child_data) {
            if (!is_numeric($data_key))
                continue;

            $child = $this->generate_xml_element($dom, $child_data);
            if ($child)
                $element->appendChild($child);
        }

        return $element;
    }

    public function syncDataPerProgram($data, $program = false, $district, $partner = false) {

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
                    if (!in_array($key, array('MemberID', 'uid', 'primarykeys', 'DateSync'))) {
                        array_push($elements, array(
                            'dataElement' => $key,
                            'value' => $element
                        ));
                    }

                    if ($key == 'MemberID') {
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
                    ;
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
                $dhis = $this->_curlItOut($data, $program['uid'], $OrgUnit, $farmerID, $uid, $primary, $partner);
            }
        }
    }

    public function getLongitudeByPrimary($primary) {
        $this->db->select('Longitude');
        $this->db->from('ktv_cocoa_farmer_garden');
        $this->db->where(json_decode($primary, TRUE));
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->Longitude;
        }
        return '';
    }

    public function getLatitudeByPrimary($primary) {
        $this->db->select('Latitude');
        $this->db->from('ktv_cocoa_farmer_garden');
        $this->db->where(json_decode($primary, TRUE));
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->Latitude;
        }
        return '';
    }

    public function _getDataValueByProgram($view, $farmerid) {
        $output = array();
        $this->db->select('*');
        $this->db->from($view);
        $this->db->where('FarmerID', $farmerid);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();
            foreach ($result as $keys => $values) {
                $elements = array();
                unset($values['FarmerID']);
                foreach ($values as $key => $element) {
                    array_push($elements, array(
                        'dataElement' => $key,
                        'value' => $element
                    ));
                }
                array_push($output, $elements);
            }
        }

        return $output;
    }

    public function syncData($data, $sql) {

        //ambil program utk farmer -> array('uid' => <string>, 'programid' => <int>)
        $farmerProgram = $this->_getProgramByShortName();

        //ambil orgunit district -> array('uid' => <string>, 'orgunit' => <int>)
        $OrgUnit = $this->_getOrgUnitByFarmerID($data);

        //ambil data terkait farmer
        $related = $this->_getEventRelatedToFarmer();
        $payloads = array();

        //ambil uid milik farmerid
        $uid = $this->_getFarmerUID($data);

        //ambil data element
        if ($farmerProgram) {
            $dataElement = $this->_getDataElementByProgram($farmerProgram['programid'], $farmerProgram['description'], $data);

            $header = array(
                'program' => $farmerProgram['uid'],
                'orgUnit' => $OrgUnit,
                'eventDate' => date('Y-m-d'),
                'status' => 'COMPLETED',
                'storedBy' => 'admin',
                'coordinate' => array(
                    'latitude' => '',
                    'longitude' => ''
                ),
                'dataValues' => $dataElement
            );

            array_push($payloads, $header);

            //di halt dulu
            $related = false;

            if (is_array($related) && count($related) > 0) {

                foreach ($related as $rel => $table) {
                    $extra = $this->_buildPayload($data, $table['description']);
                    if ($extra) {
                        array_push($payloads, $extra);
                    }
                }
            }

            $pl = json_encode(array('events' => $payloads)); //var_dump($pl);die;
            return $this->_curlItOut($pl, $farmerProgram['uid'], $OrgUnit, $data, $uid);
        }
    }

    private function _getFarmerUID($farmerid) {
        $this->db->select('uid');
        $this->db->from('ktv_cocoa_farmer');
        $this->db->where('FarmerID', $farmerid);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->uid;
        }

        return false;
    }

    private function _getEventRelatedToFarmer() {

        $this->db->select('programid,uid,description,shortname');
        $this->db->from('mw_program');
        $this->db->where('reference != "ktv_cocoa_farmer"', null, false);
        $this->db->where('description is not null', null, false);
        $this->db->where('char_length(description) > 0', null, false);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->result_array();
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

    private function _getProgramByShortName($name = 'view_farmer_profile_v1') {
        $this->db->select('programid,uid,description');
        $this->db->from('mw_program');
        $this->db->where('description', $name);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row_array();
            $Q->free_result();
            return $row;
        }
        return false;
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

    private function _getDataElementByProgram($pid = '', $table = '', $farmer = '') {

        $this->db->select('mw_dataelement.uid as dataElement,mw_programstagedataelement.reference_display,valuetype,mw_dataelement.name', false);
        $this->db->from('mw_dataelement');
        $this->db->join('mw_programstagedataelement', 'mw_programstagedataelement.dataelementid=mw_dataelement.dataelementid', 'left');
        $this->db->join('mw_programstage', 'mw_programstage.programstageid = mw_programstagedataelement.programstageid', 'left');
        $this->db->join('mw_program', 'mw_program.programid = mw_programstage.programid', 'left');
        $this->db->where('mw_programstage.programid', $pid, false);
        $this->db->where('mw_programstagedataelement.section_sort_order <> 0', NULL, false);
        $this->db->where('(mw_programstagedataelement.reference_display IS NOT NULL)', NULL, false); //var_dump($this->db->_compile_select());die;
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {

            $result = $Q->result_array();
            $fields = array();
            foreach ($result as $keys => $values) {
                if ($this->db->field_exists($values['reference_display'], $table)) {
                    array_push($fields, $values['reference_display']);
                } else {
                    //echo $values['reference_display'].'<br />';
                    unset($result[$keys]);
                }
            }

            $output = array();

            $valuesss = $this->_getDataElementValue($fields, $table, $farmer); //var_dump($fields);die;
            foreach ($result as $keys => $values) {
                if ($values['valuetype']) {
                    $validate = $this->_validateValueForType($values, $valuesss[$values['reference_display']]);
                    if ($validate['value'] == NULL) {
                        $validate['value'] = 0;
                    }
                    if ($validate) {
                        array_push($output, $validate);
                    } else {
                        unset($result[$keys]);
                    }
                } else {
                    array_push($output, array(
                        'dataElement' => $values['dataElement'],
                        'value' => $valuesss[$values['reference_display']],
                            //'valuetype' => $values['valuetype'],
                            //'name' => $values['name'],
                    ));
                }
            }
            //var_dump($result);die;
            return $output;
        }

        return array();
    }

    private function _validateValueForType($data, $value) {

        $output = array(
            'dataElement' => $data['dataElement'],
            'value' => $value
        );

        switch ($data['valuetype']) {
            case 'BOOLEAN':
                if ($value == 1) {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => true
                    );
                } else {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => true
                    );
                }
                break;
            case 'TRUE_ONLY':
                if ($value == 1) {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => true
                    );
                } else {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => true
                    );
                    //$output = false;
                }
                break;
            case 'DATE':
                if (!strtotime($value)) {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => date('Y-m-d')
                    );
                } else {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => date('Y-m-d', strtotime($value))
                    );
                }
                break;
            case 'FILE_RESOURCE':
                $output = array(
                    'dataElement' => $data['dataElement'],
                    'value' => ''
                );
                break;
            case 'NUMBER':
                if (strlen($value) == 0 || is_null($value)) {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => 0
                    );
                }
                break;
            case 'INTEGER_POSITIVE':
                if (strlen($value) == 0 || is_null($value) || $value < 1) {
                    $output = array(
                        'dataElement' => $data['dataElement'],
                        'value' => 1
                    );
                }
                break;
            default:
                if (strlen($value) == 0 || is_null($value)) {
                    $value = '-';
                }

                $output = array(
                    'dataElement' => $data['dataElement'],
                    'value' => $value
                );
                break;
        }

        return $output;
    }

    private function _getDataElementValue($dataElement, $table, $farmer) { //var_dump($dataElement); die;
        //if($this->db->field_exists($dataElement,$table)){
        $this->db->select($dataElement);
        $this->db->from($table);
        $this->db->where('FarmerID', $farmer);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row_array();
            return $row;
        }
        //}
        return '';
    }

    private function _curlItOut($payload, $program, $orgunit, $farmerID, $uid = false, $primary = '', $partner = false) {

        $doc = new DOMDocument();
        $child = $this->generate_xml_element($doc, $payload);
        if ($child) {
            $doc->appendChild($child);
        }
        $doc->formatOutput = true; // Add whitespace to make easier to read XML
        $xml = (string) $doc->saveXML();

        //echo $xml;      
        $urldhis = $this->config->item('dhis_url');

        //untuk testing
        //$urldhis = 'http://demo-mobile.cocoatrace.com/';

        if ($partner) {
            $urldhis = $this->config->item('dhis_url_' . $partner);
        }

        if ($uid) {
            $action = 'PUT';
            $url = $urldhis . 'api/events/' . $uid;
        } else {
            $url = $urldhis . 'api/events';
            $action = 'POST';
        }

        $this->load->helper('file');

        $dhispassword = 'Basic YWRtaW46S29sdGl2YTIwMTMh';
        if ($partner == 'ecom') {
            $dhispassword = 'Basic c3VwZXJhZG1pbjpTdXBlcmFkbWluMTIzIQ==';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/xml',
            'Authorization: ' . $dhispassword
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, (strval($xml)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $curlresult = json_decode($result, true);

        //echo($result);
        //echo "\n";
        //echo "\n";
        //echo "\n";
        if (is_array($curlresult) && !$uid) {
            switch ($program) {
                case 'QxauNvjcpBw':
                    $this->updateFarmerUID($curlresult, $program, $orgunit, $farmerID, $primary);
                    break;
                default:
                    $this->updateUID($curlresult, $program, $primary);
                    break;
            }
        }

        return $result;
    }

    public function updateFarmerUID($curlResult, $program, $orgunit, $farmerID) {

        $this->db->where('MemberID', $farmerID);
        $this->db->set('uid', $curlResult['response']['importSummaries'][0]['reference']);
        $this->db->update('ktv_members');
        if (strlen($this->db->_error_message()) == 0) {
            return true;
        }

        return false;
    }

    public function updateUID($curlResult, $program, $primary) {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        $primary = json_decode($primary, true);
        $this->db->select('reference');
        $this->db->from('mw_program');
        $this->db->where('uid', $program);
        $table = $this->db->get();
        if ($table->num_rows() > 0) {
            $row = $table->row();
            $table = $row->reference;
        }

        $this->db->where($primary);
        $this->db->set('uid', $curlResult['response']['importSummaries'][0]['reference']);
        $this->db->update($table);
    }

    public function syncDataX($data, $seq) {
        $rows = array();

        $ids = "'" . implode("','", $data) . "'";

        $sql = "SELECT kcf.FarmerID AS 'WSgxCBozxdW', kcf.FarmerName AS 'hgRo1qiF4rQ', kcf.Birthdate AS 'O1UIrn5Fgnr', kc.GroupName AS 'suFnKPQt16t', kv.Village AS 'TnKKYIHm1vj', kcf.HandPhone AS 'HPWIySnKrLy', kcf.Address AS 'HMTQaZJXSYt', kcf.Education AS 'x2kdQFPcoJT', kcf.MaritalStatus AS 'EGUGv8PsqON', kcf.AccountNumber AS 'qwhmvER1Moy', kcf.BankName AS 'IjQeJBEoVWf', kcf.Gender AS 'x9BFASr6QF1',SUM(kcfg.GardenHaUnCertified) LandSize,SUM( ( kcfg.PanenTrekMonths * kcfg.PanenTrekPanenMonth * kcfg.PanenTrekKg) + ( kcfg.PanenBiasaMonths * kcfg.PanenBiasaPanenMonth * kcfg.PanenBiasaKg ) + ( kcfg.PanenRayaMonths * kcfg.PanenRayaPanenMonth * kcfg.PanenRayaKg ) ) Production FROM ktv_cocoa_farmer kcf LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = ks.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN( SELECT FarmerID, GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID, GardenNr) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr AND kcf.StatusCode = 'active' WHERE kcf.FarmerID in(" . $ids . ") GROUP BY kcf.FarmerID";
        $row = $this->db->query($sql)->result_array();
        //var_dump($this->db->query($sql)->num_rows());die;
        $payload = array();
        foreach ($row as $keys => $value) {
            $rowoutput = array();
            foreach ($value as $fields => $val) {
                if ($fields != 'Production' && $fields != 'LandSize') {
                    array_push($rowoutput, array(
                        'dataElement' => $fields,
                        'value' => $val
                    ));
                }
            }

            array_push($payload, array(
                'program' => 'QxauNvjcpBw',
                'orgUnit' => 'AsAl92H61kN',
                'eventDate' => date('Y-m-d'),
                'status' => 'COMPLETED',
                'storedBy' => 'admin',
                'coordinate' => array(
                    'latitude' => '',
                    'longitude' => ''
                ),
                'dataValues' => $rowoutput
            ));
        }

        $payloads = array('events' => $payload);

        //echo json_encode($payloads);

        $this->load->helper('file');

        $url = 'http://mw1.cocoatrace.com/api/events';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic YWRtaW46ZGlzdHJpY3Q='
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $curlresult = json_decode($result, true);

        if (!write_file('dhis' . $seq . '.json', json_encode($result))) {
            //write_file('testingg-error.json', json_encode($payloads))
        } else {
            //echo 'File written!:';
        }

        if (is_array($curlresult) && $curlresult['httpStatusCode'] == 200) {
            $execute = true;
            foreach ($rows as $keys => $value) {
                if (!$this->checkForSynced($value)) {
                    $execute = $this->createSyncedLog($value);
                } else {
                    $execute = $this->updateSyncedLog($value);
                }
            }
        }

        if ($execute) {
            return array('success' => true);
        }

        return array('success' => false);
    }

    public function checkForSynced($data) {
        $this->db->select('FarmerID,DateSynced');
        $this->db->from('ktv_certification_candidates');
        $this->db->where('FarmerID', $data['WSgxCBozxdW']);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->DateSynced;
        }
        return false;
    }

    public function createSyncedLog($data) {

        $insert = array(
            'FarmerID' => $data['WSgxCBozxdW'],
            'LandSize' => $data['LandSize'],
            'Production' => $data['Production'],
            'CreatedBy' => $_SESSION['userid'],
            'DateCreated' => date('Y-m-d'),
            'DateSynced' => date('Y-m-d')
        );

        $this->db->insert('ktv_certification_candidates', $insert);

        if ($this->db->insert_id()) {
            return true;
        }

        return false;
    }

    public function updateSyncedLog($data) {

        $update = array(
            'FarmerID' => $data['WSgxCBozxdW'],
            'LandSize' => $data['LandSize'],
            'Production' => $data['Production'],
            'CreatedBy' => '',
            'CreatedDate' => date('Y-m-d')
        );

        $this->db->where('FarmerID', $data['WSgxCBozxdW']);
        $this->db->update('ktv_certification_candidates', $update);

        if ($this->db->affected_rows()) {
            return true;
        }

        return false;
    }

    public function getDataByDistrict($district = false, $onlyNew = false, $program, $farmer = false, $partner = false) {

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
        $this->db->from('ktv_members');
        $this->db->join('ktv_village', 'ktv_village.VillageID = ktv_members.VillageID', 'INNER');
        $this->db->join('ktv_subdistrict', 'ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID', 'INNER');
        $this->db->join($program['description'], $program['description'] . '.MemberID = ktv_members.MemberID', 'INNER');

        //for certain farmer
        if (strlen($farmer) > 0) {
            $this->db->where($program['description'] . '.MemberID', $farmer, false);
        }

        //for certain district
        if (strlen($district) > 0) {
            $this->db->where('DistrictID', $district);
        }

        //only CT data which is not in dhis, yet
        if ($onlyNew == true) {
            $this->db->where($program['description'] . '.uid IS NULL', null, false);
        } else {
            $this->db->where($program['description'] . '.uid IS NOT NULL', null, false);
        }

        $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();
            return $result;
        }

        return false;
    }

    public function getAllDistrict($district = false) {

        $this->db->select('DistrictID');
        $this->db->from('ktv_district');
        $this->db->join('mw_organisationunit', 'mw_organisationunit.name = ktv_district.District', 'left');
        if ($district) {
            $this->db->where('DistrictID', $district);
        } else {
            $this->db->where('mw_organisationunit.status', 1);
        }
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            return $Q->result_array();
        }

        return false;
    }

    public function getAllProgramWithView($program = false) {

        $this->db->select('uid');
        $this->db->from('mw_program');
        if (strlen($program) > 0) {
            $this->db->where('uid', $program);
        }
        $this->db->where('description IS NOT NULL', null, false);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            return $Q->result_array();
        }

        return false;
    }

    public function getFarmerUpdates($startdate = false, $enddate = false, $newonly = false) {

        $this->load->dbutil();

        if (!$startdate && !$enddate) {
            return false;
        }

        $this->db->select('FarmerID,FarmerName,SUBSTR(FarmerID,1,4) AS district,VillageID,CPGid,Birthdate', false);
        $this->db->from('ktv_cocoa_farmer');
        $this->db->order_by('FarmerID', 'DESC');

        if ($newonly) {
            $this->db->where('(DateCreated BETWEEN "' . $startdate . '" AND "' . $enddate . '") ', '', FALSE);
        } else {
            $this->db->where('(DateUpdated BETWEEN "' . $startdate . '" AND "' . $enddate . '") ', '', FALSE);
            $this->db->where('DateSync IS NULL', '', FALSE);
        }

        $this->db->where('uid IS NOT NULL', '', FALSE);

        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();

            return array('csv' => $this->dbutil->csv_from_result($Q), 'data' => $result, 'district' => $this->getDistrictByResult($result));
        }

        return false;
    }

    public function getDistrictByResult($data) {
        $district = array();

        foreach ($data as $key => $value) {
            if (!in_array($value['district'], $district)) {
                array_push($district, $value['district']);
            }
        }

        return $district;
    }

    public function getOfficial($district) {


        $this->db->select('OfficialEmail');
        $this->db->distinct();
        $this->db->from('ktv_staffs');
        $this->db->join('ktv_staff_positions', 'ktv_staff_positions.StaffPosStaffID = ktv_staffs.StaffID', 'left');
        $this->db->join('ktv_ref_position_type', 'ktv_ref_position_type.PositionID = ktv_staff_positions.StaffPosPositionID', 'left');
        $this->db->join('ktv_ref_work_area', 'ktv_ref_work_area.WorkAreaID = ktv_staffs.WorkAreaID', 'left');
        $this->db->where('OfficialEmail IS NOT NULL', '', false);
        $this->db->where('ktv_staff_positions.StaffPostEnd > NOW()', '', false);
        $this->db->where('ktv_staffs.StatusCode', 'active');
        $this->db->where_in('DistrictID', $district);
        $this->db->where_in('PositionCode', array('FF', 'FC', 'PO'));
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $output = array();
            $result = $Q->result_array();
            foreach ($result as $key => $val) {
                array_push($output, $val['OfficialEmail']);
            }

            return $output;
        }

        return array('ardiantoro@koltiva.com');
    }

    public function removeDhisEvent($uid) {
        $base_url = $this->config->item('dhis_url') . 'api/events';
        $authentication = $this->config->item('dhis_authentication');
        if (!$authentication) {
            $authentication = "Basic YWRtaW46S29sdGl2YTIwMTMh";
        }
        $url = $base_url . '/' . $uid;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/xml',
            'Authorization: ' . $authentication
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $curlresult = json_decode($result, true);
        
        return $curlresult;
        
    }

}
