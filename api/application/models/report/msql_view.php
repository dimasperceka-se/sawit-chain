<?php
/**
 * @Author: nikolius
 * @Date:   2017-02-09 16:13:11
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Msql_view extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_sql_view = $this->load->database('sql_view', TRUE);
    }

    public function getMainListSqlView($name,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'SqlvID';
        if($sortingDir == "") $sortingDir = 'DESC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.SqlvID
                , a.UserID AS UserIDOwner
                , b.`UserRealName` AS `User`
                , a.`SqlvName` AS `Name`
                , a.`SqlvDesc` AS `Description`
                , a.`DateCreated` AS CreatedDate
                , a.`DateUpdated` AS UpdatedDate
            FROM
                adm_sql_views a
                LEFT JOIN sys_user b ON a.`UserID` = b.`UserId`
                LEFT JOIN adm_sql_views_share_setting c ON a.`SqlvID` = c.`SqlvID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`SqlvName` LIKE ?
                AND c.UserID = '{$_SESSION['userid']}'
            GROUP BY a.SqlvID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
            ";
        $query = $this->db->query($sql,array('%'.$name.'%',(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getFormSqlView($SqlvID){
        $sql="SELECT
                `SqlvID`,
                `SqlvName`,
                `SqlvDesc`,
                `SqlvStatement`
            FROM
                `adm_sql_views`
            WHERE
                `SqlvID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($SqlvID));
        $data = $query->result_array();

        $return['success'] = true;
        $return['data'] = $data[0];
        return $return;
    }

    public function insertSqlView($varPost){
        $this->db->trans_begin();

        //hilangkan karakter2 yg bisa menyebabkan query error nantinya
        $varPost['SqlvStatement'] = str_replace('?', '', $varPost['SqlvStatement']);

        $sql="INSERT INTO `adm_sql_views` SET
                  `UserID` = ?,
                  `SqlvName` = ?,
                  `SqlvDesc` = ?,
                  `SqlvStatement` = ?,
                  `DateCreated` = NOW(),
                  `CreatedBy` = ?";
        $p = array(
            $varPost['userid'],
            $varPost['SqlvName'],
            $varPost['SqlvDesc'],
            $varPost['SqlvStatement'],
            $varPost['userid']
        );
        $query = $this->db->query($sql,$p);
        $SqlvID = $this->db->insert_id();

        //insert ke share setting
        $sql="INSERT INTO `adm_sql_views_share_setting` SET
              `SqlvID` = ?,
              `UserID` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $query = $this->db->query($sql,array($SqlvID,$varPost['userid'],$varPost['userid']));

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

    public function updateSqlView($varPost){
        //hilangkan karakter2 yg bisa menyebabkan query error nantinya
        $varPost['SqlvStatement'] = str_replace('?', '', $varPost['SqlvStatement']);

        $sql="UPDATE `adm_sql_views` SET
                  `SqlvName` = ?,
                  `SqlvDesc` = ?,
                  `SqlvStatement` = ?,
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    `SqlvID` = ?
                LIMIT 1";
        $p = array(
            $varPost['SqlvName'],
            $varPost['SqlvDesc'],
            $varPost['SqlvStatement'],
            $varPost['userid'],
            $varPost['SqlvID']
        );
        $query = $this->db->query($sql,$p);

        if($query){
            $result['success'] = true;
            $result['message'] = "Data Saved";
        }else{
            $result['success'] = false;
            $result['message'] = "Process Failed";
        }
        return $result;
    }

    public function deleteSqlView($SqlvID){
        $sql="UPDATE `adm_sql_views` SET StatusCode = 'nullified',`DateUpdated` = NOW(), `LastModifiedBy` = ?  WHERE `SqlvID` = ? LIMIT 1";
        $query = $this->db->query($sql,array($_SESSION['userid'],$SqlvID));

        if($query){
            $result['success'] = true;
            $result['message'] = "Data Deleted";
        }else{
            $result['success'] = false;
            $result['message'] = "Process Failed";
        }
        return $result;
    }

    public function getPrepRunQuery($SqlvID){
        //get querynya
        $sql="SELECT
                a.`SqlvStatement` AS sqlNya
                , a.SqlvName AS sqlViewName
            FROM
                adm_sql_views a
            WHERE
                a.`SqlvID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($SqlvID));
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
        $query = $this->db_sql_view->query($sqlStatement);
        if (!empty($this->db_sql_view->_error_message())) {
            $results['success'] = false;
        }else{
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
            for ($i=0; $i < count($dataKolom); $i++) {
                $dataParamGridColumn[$increGridKolom]['text'] = $dataKolom[$i]['name'];
                $dataParamGridColumn[$increGridKolom]['dataIndex'] = $dataKolom[$i]['name'];
                $dataParamGridColumn[$increGridKolom]['width'] = '15%';
                $increGridKolom++;
            }
            //susun grid kolom (end)

            $results['success'] = true;
            $results['fieldNya'] = $dataKolom;
            $results['gridColumnNya'] = $dataParamGridColumn;
            $results['sqlViewName'] = $dataSql['sqlViewName'];
        }

        return $results;
    }

    public function getMainListSqlViewQuery($SqlvID,$start,$limit,$opsiLimit){
        //get querynya
        $sql="SELECT
                a.`SqlvStatement` AS sqlNya
            FROM
                adm_sql_views a
            WHERE
                a.`SqlvID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($SqlvID));
        $dataSql = $query->row_array();
        $sqlStatement = $dataSql['sqlNya'];

        //lengkapi querynya untuk penyesuaian jika ada filternya (begin)
        $sql="SELECT
                a.FilterID
                , a.`FilterBy`
                , a.`Operator`
                , a.`FilterValue`
            FROM
                `adm_sql_views_filter` a
            WHERE
                a.`SqlvID` = ?
                AND a.UserID = '{$_SESSION['userid']}'
            ORDER BY a.`FilterID` ASC";
        $query = $this->db->query($sql,array((int) $SqlvID));
        $dataFilter = $query->result_array();

        if($dataFilter[0]['FilterID'] != ""){
            //ambil data kolomnya
            $dataKolom = $this->getCmbAddFilterBy($SqlvID);
            $arrSqlKolom = array();
            foreach ($dataKolom as $key => $value) {
                $arrSqlKolom[] = $value['id'];
            }
            $sqlKolom = implode("`,`",$arrSqlKolom);

            $sqlStatementPrefix = "SELECT `$sqlKolom` FROM ( ";

            $arrSqlWhere = array();
            foreach ($dataFilter as $key => $value) {
                $arrSqlWhere[] = " `{$value['FilterBy']}` {$value['Operator']} '{$value['FilterValue']}' ";
            }
            $sqlWhereFilter = implode(" AND ",$arrSqlWhere);

            $sqlStatementSuffix = " ) AS tbl_filter WHERE $sqlWhereFilter ";

            $sqlStatement = $sqlStatementPrefix.$sqlStatement.$sqlStatementSuffix;
        }
        //lengkapi querynya untuk penyesuaian jika ada filternya (end)

        if($opsiLimit == 'limit'){
            $sqlStatementLimit = $sqlStatement." LIMIT ?,?";
            $query = $this->db_sql_view->query($sqlStatementLimit, array( (int) $start, (int) $limit));
            $result['data'] = $query->result_array();

            $queryTotal = $this->db_sql_view->query($sqlStatement);
            $data = $queryTotal->result_array();
            $result['total'] = count($data);

            return $result;
        } elseif($opsiLimit == 'no_limit') {
            $query = $this->db_sql_view->query($sqlStatement);
            return $query->result_array();
        }
    }

    public function getShareUserList($SqlvID){
        $sql="SELECT
                a.`SqlvSetID` AS id
                , b.`PersonNm` AS name
                , pt.`PositionName` AS position
                , d.`RoleName` AS role
            FROM
                adm_sql_views_share_setting a
                LEFT JOIN ktv_persons b ON a.`UserID` = b.`UserID`
                LEFT JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                LEFT JOIN sys_role d ON c.`ObjType` = d.`RoleCode`
                LEFT JOIN ktv_staff_positions sp ON sp.StaffPosStaffID = c.StaffID AND CURDATE() BETWEEN sp.StaffPostStart AND sp.StaffPostEnd
                LEFT JOIN ktv_ref_position_type pt ON pt.PositionID = sp.StaffPosPositionID
            WHERE
                a.`SqlvID` = ?
                AND a.UserID != {$_SESSION['userid']}
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql,array((int) $SqlvID));
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getShareUserFilterList($filterName,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'PersonNm';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                su.UserID AS id
                , b.`PersonNm` AS name
                , pt.`PositionName` AS position
                , d.`RoleName` AS role
            FROM
                ktv_persons b
                INNER JOIN sys_user su ON b.`UserID` = su.`UserId`
                LEFT JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                LEFT JOIN sys_role d ON c.`ObjType` = d.`RoleCode`
                LEFT JOIN ktv_staff_positions sp ON sp.StaffPosStaffID = c.StaffID AND CURDATE() BETWEEN sp.StaffPostStart AND sp.StaffPostEnd
                LEFT JOIN ktv_ref_position_type pt ON pt.PositionID = sp.StaffPosPositionID
            WHERE
                b.`StatusCd` = 'active'
                AND c.`StatusCode` = 'active'
                AND b.PersonNm != ''
                AND b.PersonNm LIKE ?
                AND su.`UserActive` = 'Yes'
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            '%'.$filterName.'%',
            (int) $start,
            (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $data = $query->result_array();
        $result['data'] = $data;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function insertShareUser($varPost){
        $this->db->trans_begin();

        if($varPost['UserIDs'][0] != ""){
            foreach ($varPost['UserIDs'] as $key => $value) {
                $sql="INSERT INTO `adm_sql_views_share_setting` (SqlvID,UserID,DateCreated,CreatedBy) VALUES(?,?,NOW(),?)
                        ON DUPLICATE KEY UPDATE
                        DateUpdated = NOW(),
                        LastModifiedBy = ?";
                $p = array(
                    $varPost['SqlvID'],
                    $value,
                    $varPost['userid'],
                    $varPost['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to insert record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record saved";
        }

        return $results;
    }

    public function deleteShareUser($SqlvSetID){
        $sql="DELETE FROM adm_sql_views_share_setting WHERE SqlvSetID = ? LIMIT 1";
        $proses = $this->db->query($sql,array((int) $SqlvSetID));

        if ($proses) {
            $results['success']    = true;
            $results['message'] = 'User Deleted';
        } else {
            $results['success'] = false;
            $results['message'] = 'Delete Failed';
        }
        return $results;
    }

    public function getGridAddFilter($SqlvID){
        $sql="SELECT
                a.`FilterID`
                , a.`FilterBy`
                , a.`Operator`
                , a.`FilterValue`
            FROM
                adm_sql_views_filter a
            WHERE
                a.`SqlvID` = ?
                AND a.UserID = '{$_SESSION['userid']}'
            ORDER BY a.`FilterID` ASC";
        $query = $this->db->query($sql,array((int) $SqlvID));
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getCmbAddFilterBy($SqlvID){
        $sql="SELECT
                a.`SqlvStatement` AS sqlNya
            FROM
                adm_sql_views a
            WHERE
                a.`SqlvID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($SqlvID));
        $dataSql = $query->row_array();
        $sqlStatement = $dataSql['sqlNya'];

        //eksekusi query dari sql view
        $query = $this->db_sql_view->query($sqlStatement);
        if (!empty($this->db_sql_view->_error_message())) {
            return array();
        }else{
            //data hasil query
            $dataQuery = $query->result_array();

            //susun urutan field (begin)
            $increKolom = 0;
            $dataKolom = array();
            foreach ($dataQuery as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $dataKolom[$increKolom]['id'] = $key1;
                    $dataKolom[$increKolom]['label'] = $key1;
                    $increKolom++;
                }
                break;
            }
            //susun urutan field (end)

            return $dataKolom;
        }
    }

    public function insertAddFilterItem($paramPost){
        $sql="INSERT INTO `adm_sql_views_filter` SET
                `SqlvID` = ?,
                UserID = ?,
                `FilterBy` = ?,
                `Operator` = ?,
                `FilterValue` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            $paramPost['SqlvID'],
            $_SESSION['userid'],
            $paramPost['FilterBy'],
            $paramPost['Operator'],
            $paramPost['FilterValue'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateAddFilterItem($paramPost){
        $sql="UPDATE `adm_sql_views_filter` SET
                `FilterBy` = ?,
                `Operator` = ?,
                `FilterValue` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `FilterID` = ?
            LIMIT 1";
        $p = array(
            $paramPost['FilterBy'],
            $paramPost['Operator'],
            $paramPost['FilterValue'],
            $_SESSION['userid'],
            $paramPost['FilterID']
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteAddFilterItem($FilterID){
        $sql="DELETE FROM adm_sql_views_filter WHERE FilterID = ? LIMIT 1";
        $query = $this->db->query($sql,array($FilterID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

}
?>