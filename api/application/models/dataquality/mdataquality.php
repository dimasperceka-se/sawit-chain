<?php

class Mdataquality extends CI_Model {

    function getDataQuality($key, $start, $limit, $all = false) {
        $limits = $filter = "";
        $where = array();

        array_push($where, " b.dqvalue_status = 'active'");
        array_push($where, " a.dq_status = 'active'");

        if (!$all) {
            $limits = " LIMIT " . $start . "," . $limit;
        }

        if ($key) {
            array_push($where, " a.dq_name like '%" . $key . "%'");
        }

        if (count($where) > 0) {
            $filter = " WHERE " . implode(" AND ", $where);
        }

        $sql = "SELECT 
                a.dq_id,
                a.dq_name,
                a.dq_result,
                d.dqprogram_name AS program,
                b.dqvalue_value,
                b.dqvalue_date
              FROM
                ktv_dq a 
                LEFT JOIN ktv_dq_value b 
                  ON a.dq_id = b.dqvalue_dq_id 
                LEFT JOIN ktv_dq_programsection c 
                  ON c.dqsection_id = a.dq_dqprogramsection_id 
                LEFT JOIN ktv_dq_program d
                  ON d.dqprogram_id = c.dqsection_dqprogram_id
                    " . $filter . " " . $limits;
        $results = $this->db->query($sql);
        if ($this->db->_error_number()) {
            var_dump($this->db->_error_message());
            die;
        }
        $results = $results->result_array();
        $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

        $result['data'] = $results;
        $result['total'] = $total;

        return $result;
    }

    function deleteDataQuality($id) {
        $sql = "UPDATE ktv_dq SET dq_status = 'inactive',dq_updated_by='" . $_SESSION['userid'] . "',dq_updated_date=NOW() WHERE dq_id = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function calculateDataQualityItem($id) {
        $dq_query = $this->getQuery($id);

        if ($dq_query) {
            $sql = $dq_query;
            $query = $this->db->query($sql, array());
            $result = $query->result_array();
            
            $total = count($result);
            $this->dataProcess($id, $total);
        }

        $results['success'] = true;
        $results['message'] = "record generated.";
        return $results;
    }

    function getQuery($id) {
        $sql = "
                SELECT 
                  a.dq_query
                FROM
                  ktv_dq a 
                  WHERE a.dq_id = ?
            ";

        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0]['dq_query'];
    }

    function dataProcess($id, $total) {
        if ($total != 0) {
            $result = $this->updateDQResult($id, 'invalid');
        } else {
            $result = $this->updateDQResult($id, 'valid');
        }

        if ($result) {
            $result = $this->inactiveDQValue($id);
            $result = $this->insertDQValue($id, $total);
        }
    }

    function inactiveDQValue($id) {
        $sql = "UPDATE 
                ktv_dq_value 
              SET
                dqvalue_status = 'inactive' 
              WHERE dqvalue_dq_id = ?";
        $query = $this->db->query($sql, array($id));
        return true;
    }

    function insertDQValue($id, $total) {
        $sql = "INSERT INTO ktv_dq_value
            (
             dqvalue_dq_id,
             dqvalue_value,
             dqvalue_status,
             dqvalue_date,
             dqvalue_generated_by)
            VALUES (
                    ?,
                    ?,
                    'active',
                    NOW(),
                    ?)";
        $query = $this->db->query($sql, array($id, $total, $_SESSION['userid']));
    }

    function updateDQResult($id, $result) {
        $sql = "UPDATE 
                ktv_dq 
              SET
                dq_result = ? 
              WHERE dq_id = ?";
        $query = $this->db->query($sql, array($result, $id));
        return true;
    }

    public function getPrepRunQuery($dq_id) {
        //get querynya
        $sql = "SELECT
                a.`dq_query` AS sqlNya
                , a.dq_name AS sqlViewName
            FROM
                ktv_dq a
            WHERE
                a.`dq_id` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($dq_id));
        $dataSql = $query->row_array();
        $sqlStatement = $dataSql['sqlNya'];

        //cek apakah query ada kata kunci INSERT, UPDATE, DELETE, DROP
        if (
                strpos(strtolower($sqlStatement), 'insert ') !== false ||
                strpos(strtolower($sqlStatement), 'update ') !== false ||
                strpos(strtolower($sqlStatement), 'drop ') !== false ||
                strpos(strtolower($sqlStatement), 'delete ') !== false
        ) {
            $results['success'] = false;
        }

        //eksekusi query dari sql view
        $query = $this->db->query($sqlStatement);
        if (!empty($this->db->_error_message())) {
            $results['success'] = false;
        } else {
            //data hasil query
            $dataQuery = $query->result_array();

            //susun urutan field (begin)
            $increKolom = 0;
            $dataKolom = array();
            foreach ($dataQuery as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $dataKolom[$increKolom]['name'] = $key1;
                    $increKolom++;
                }
                break;
            }
            //susun urutan field (end)
            //susun grid kolom (begin)
            $dataParamGridColumn = array();

            $dataParamGridColumn[0]['text'] = lang('No');
            $dataParamGridColumn[0]['xtype'] = 'rownumberer';
            $dataParamGridColumn[0]['width'] = '4%';

            $increGridKolom = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $dataParamGridColumn[$increGridKolom]['text'] = $dataKolom[$i]['name'];
                $dataParamGridColumn[$increGridKolom]['dataIndex'] = $dataKolom[$i]['name'];
                $dataParamGridColumn[$increGridKolom]['width'] = '15%';
                $increGridKolom++;
            }
            //susun grid kolom (end)

            $results['success'] = true;
            $results['fieldNya'] = $dataKolom;
            $results['gridColumnNya'] = $dataParamGridColumn;
        }

        $results['sqlViewName'] = $dataSql['sqlViewName'];
        return $results;
    }

    public function getMainListDataQualityQuery($dq_id, $start, $limit, $opsiLimit) {
        if (!$start) {
            $start = 1;
        }

        if (!$limit) {
            $limit = 50;
        }

        //get querynya
        $sql = "SELECT
                a.`dq_query` AS sqlNya
            FROM
                ktv_dq a
            WHERE
                a.`dq_id` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($dq_id));
        $dataSql = $query->row_array();
        $sqlStatement = $dataSql['sqlNya'];

        if ($opsiLimit == 'limit') {
            $sqlStatementLimit = $sqlStatement . " LIMIT ?,?";

            $query = $this->db->query($sqlStatementLimit, array((int) $start, (int) $limit));

            $result['data'] = $query->result_array();


            $queryTotal = $this->db->query($sqlStatement);
            $data = $queryTotal->result_array();
            $result['total'] = count($data);

            return $result;
        } elseif ($opsiLimit == 'no_limit') {
            $query = $this->db->query($sqlStatement);
            return $query->result_array();
        }
    }

    public function insertDataQuality($varPost) {
        $this->db->trans_begin();

        //hilangkan karakter2 yg bisa menyebabkan query error nantinya
        $varPost['dq_query'] = str_replace('?', '', $varPost['dq_query']);

        $sql = "INSERT INTO `ktv_dq` SET
                  `dq_name` = ?,
                  `dq_description` = ?,
                  `dq_query` = ?,
                  `dq_dqprogramsection_id` = ?,
                  `dq_status` = ?,
                  `dq_created_date` = NOW(),
                  `dq_created_by` = ?";
        $p = array(
            $varPost['dq_name'],
            $varPost['dq_description'],
            $varPost['dq_query'],
            $varPost['dq_programsection'],
            'active',
            $varPost['userid']
        );

        $query = $this->db->query($sql, $p);

        $dq_id = $this->db->insert_id();

        if ($dq_id) {
            $this->calculateDataQualityItem($dq_id);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record saved";
        }

        return $results;
    }

    public function updateDataQuality($varPost) {
        //hilangkan karakter2 yg bisa menyebabkan query error nantinya
        $varPost['dq_query'] = str_replace('?', '', $varPost['dq_query']);

        $sql = "UPDATE `ktv_dq` SET
                  `dq_name` = ?,
                  `dq_description` = ?,
                  `dq_query` = ?,
                  `dq_dqprogramsection_id` = ?,
                  `dq_status` = ?,
                  `dq_updated_date` = NOW(),
                  `dq_updated_by` = ?
                WHERE
                    `dq_id` = ?
                LIMIT 1";
        $p = array(
            $varPost['dq_name'],
            $varPost['dq_description'],
            $varPost['dq_query'],
            $varPost['dq_programsection'],
            $varPost['dq_status'],
            $varPost['userid'],
            $varPost['dq_id']
        );
        $query = $this->db->query($sql, $p);

        if ($varPost['dq_id']) {
            $this->calculateDataQualityItem($varPost['dq_id']);
        }

        if ($query) {
            $result['success'] = true;
            $result['message'] = "Data Saved";
        } else {
            $result['success'] = false;
            $result['message'] = "Process Failed";
        }
        return $result;
    }

    function readPrograms() {
        $sql = "SELECT
                dqprogram_id AS id,
                dqprogram_name AS label
              FROM ktv_dq_program
              WHERE dqprogram_status = 'active'";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function readProgramSections($dqprogram_id) {
        if ($dqprogram_id) {
            $sql = "SELECT
                dqsection_id AS id,
                dqsection_name AS label
              FROM ktv_dq_programsection
              WHERE dqsection_status = 'active' AND 
                dqsection_dqprogram_id = ?";
            $query = $this->db->query($sql, array($dqprogram_id));
            $result['data'] = $query->result_array();
        } else {
            $result['data'] = array();
        }
        return $result;
    }

    public function getFormDataQuality($dq_id) {
        $sql = "SELECT 
                a.dq_id,
                a.dq_name,
                a.dq_query,
                a.dq_result,
                b.dqsection_dqprogram_id AS dq_program,
                a.dq_dqprogramsection_id AS dq_programsection,
                a.dq_description,
                a.dq_status,
                a.dq_created_date,
                a.dq_created_by,
                a.dq_updated_date,
                a.dq_updated_by 
              FROM
                ktv_dq a 
                LEFT JOIN ktv_dq_programsection b 
                  ON a.dq_dqprogramsection_id = b.dqsection_id 
              WHERE a.dq_id = ? 
              LIMIT 1 ";
        $query = $this->db->query($sql, array($dq_id));
        $data = $query->result_array();
        $return['success'] = true;
        $return['data'] = $data[0];
        return $return;
    }
    
    public function getActiveItems() {
        $sql = "SELECT 
                a.dq_id
              FROM
                ktv_dq a 
              WHERE a.dq_status = 'active'";
        $query = $this->db->query($sql, array());
        $data = $query->result_array();
        return $data;
    }
    
    

    function getQueryData($check_type) {
        $sql = "
            SELECT 
                ChecktypeQuery AS `sql`
            FROM `ktv_check_type`
            WHERE ChecktypeID = ?
            ";

        $query = $this->db->query($sql, array($check_type));
        $result = $query->result_array();
        return $result[0];
    }

    function readProvinsis($sesPartner) {
        if ($sesPartner != 'ALL') {
            $join = 'LEFT JOIN ktv_cpg_partner b ON SUBSTR(b.CPGid,1,2) = a.ProvinceID';
            $where = " AND b.PartnerID = {$sesPartner} ";
        }
        $sql = "SELECT 
                    distinct a.Province as label,
                    a.ProvinceID as id
                FROM 
                    ktv_province a
                    $join
                WHERE 
                    a.ProvinceID not in (12,31)
                    $where
                ORDER BY a.Province";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
//        $result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

    function readKabupatens($prov, $partnerID) {
        $sql_where = "/*and DistrictID not in (1171,7373,7271,1377,7371)*/";
        if ($partnerID != 'ALL') {
            $sql_where2 = " AND DistrictID in (SELECT DistrictID FROM ktv_district_partner WHERE PartnerID = {$partnerID})";
        }
        $sql = "SELECT 
                    DISTINCT District AS label, 
                    DistrictID AS id
                FROM ktv_district a
                    LEFT JOIN ktv_province b ON a.ProvinceID = b.ProvinceID
                WHERE 
                    a.ProvinceID = ? %s %s
                ORDER BY District";
        $query = $this->db->query(sprintf($sql, $sql_where, $sql_where2), array($prov));
        $result['data'] = $query->result_array();
//        $result['data'] = array_merge(array(array('label' => ' -- All --')), $result['data']);
        return $result;
    }

    function readPrintoutCpg($prov = '', $kab = '') {
        if ($prov != '') {
            $left = " LEFT JOIN ktv_village v ON v.`VillageID` = ktv_cpg.`VillageID`
                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = sd.`DistrictID`
                    LEFT JOIN ktv_province kp on kp.`ProvinceID` = kd.`ProvinceID`";
            $add = " AND kp.ProvinceID='$prov'";
        }
        if ($kab != '') {
            $left .= " LEFT JOIN ktv_village v ON v.`VillageID` = ktv_cpg.`VillageID`
                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = sd.`DistrictID`
            $add .= " AND kd.DistrictID IN ('$kab')";
        }
        $sql = "
            SELECT 
                CPGid id,
                CONCAT('[',CPGid,'] ',GroupName) label
            FROM ktv_cpg
            $left
            WHERE CPGid>0 $add";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
//        $result['data'] = array_merge(array(array('label' => ' -- All --')), $result['data']);
        return $result;
    }

    function getDetailProvince($prov) {
        $sql = "
            SELECT 
                Province AS `province`
            FROM `ktv_province`
            WHERE ProvinceID = ?
            ";

        $query = $this->db->query($sql, array($prov));
        $result = $query->result_array();
        return $result[0]['province'];
    }

    function getDetailDistrict($kab) {
        $sql = "
            SELECT 
                District AS `district`
            FROM `ktv_district`
            WHERE DistrictID = ?
            ";

        $query = $this->db->query($sql, array($kab));
        $result = $query->result_array();
        return $result[0]['district'];
    }

    function getDetailCpg($cpg) {
        $sql = "
            SELECT 
                GroupName
            FROM `ktv_cpg`
            WHERE CpgID = ?
            ";

        $query = $this->db->query($sql, array($cpg));
        $result = $query->result_array();
        return $result[0]['GroupName'];
    }

    function getDetailCheckType($check_type) {
        $sql = "
            SELECT 
                CheckcatName
            FROM `ktv_check_category`
            WHERE CheckcatID = ?
            ";

        $query = $this->db->query($sql, array($check_type));
        $result = $query->result_array();
        return $result[0]['CheckcatName'];
    }

    function readCheckType() {
        $sql = "
            SELECT 
                `ChecktypeID` AS id,
                -- CONCAT(`CheckcatName` , '-' , `ChecktypeName`) AS label
                `ChecktypeName` AS label
            FROM
                `ktv_check_type` 
            LEFT JOIN `ktv_check_category` ON ChecktypeCheckcatID = CheckcatID
            WHERE `ChecktypeActive` = 1
            ORDER BY CheckcatName
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
//        $result['data'] = array_merge(array(array('label' => ' -- All --', 'id' => '%%')), $result['data']);
        return $result;
    }

}

?>
