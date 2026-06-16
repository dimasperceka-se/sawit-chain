<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-17 18:20:11
 * @Last Modified by:   nikolius
 * @Last Modified time: 2016-03-17 18:30:45
 */
class Mposition extends CI_Model
{

    public function position_combo_role()
    {
        $sql = "SELECT
                RoleId AS id,
                RoleName AS label
            FROM
                sys_role
            ORDER BY RoleId ASC";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function position_list($key, $start, $limit, $sortingField, $sortingDir)
    {
        if($sortingField == "") $sortingField = 'PositionName';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                `PositionID`,
                `PositionCode`,
                `PositionName`,
                RoleName AS Category,
                ktv_ref_position_type.StatusCode
            FROM
              `ktv_ref_position_type`
              LEFT JOIN sys_role ON PositionRoleId = RoleId
            WHERE
            ktv_ref_position_type.StatusCode != 'nullified'
            AND
            (`PositionCode` LIKE ? OR `PositionName` LIKE ? OR `RoleName` LIKE ?)  
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            "%$key%",
            "%$key%",
            "%$key%",
            (int) $start,
            (int) $limit,
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function position_id($id)
    {
        $sql = "SELECT
                  `PositionID`,
                  `PositionName`,
                  `Category`,
                  StatusCode
               FROM
                  `ktv_position`
               WHERE
                  PositionID = ?
               LIMIT 1";
        $p = array(
            (int) $id,
        );
        $query = $this->db->query($sql, $p);
        return $query->result_array();
    }

    public function create_position($PositionCode, $name, $category, $statusCode, $userid)
    {
        //ambil obj typenya
        $sql="SELECT
                RoleCode
            FROM
                sys_role
            WHERE
                RoleId = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($category));
        $data = $query->row_array();
        $objType = $data['RoleCode'];
        $sqlCategory = "PositionRoleId = $category, ObjType = '$objType',";

        $sql = "INSERT INTO ktv_ref_position_type SET
         PositionCode = ?,
         PositionName = ?,
         $sqlCategory
         StatusCode = IF(?='','active',?),
         DateCreated = NOW(),
         CreatedBy = ?
        ";
        $p = array(
            $PositionCode,$name, $category, $statusCode, $statusCode, $userid,
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function update_position($id, $PositionCode, $name, $category, $statusCode, $userid)
    {
        $cekCategory = (int) $category;
        if($cekCategory == 0){
            $sqlCategory = "";
        }else{
            //ambil obj typenya
            $sql="SELECT
                    RoleCode
                FROM
                    sys_role
                WHERE
                    RoleId = ?
                LIMIT 1";
            $query = $this->db->query($sql,array($cekCategory));
            $data = $query->row_array();
            $objType = $data['RoleCode'];

            $sqlCategory = "PositionRoleId = $cekCategory, ObjType = '$objType',";
        }

        $sql = "UPDATE ktv_ref_position_type SET
            PositionCode = ?,
            PositionName = ?,
            $sqlCategory
            StatusCode = ?,
            DateUpdated = NOW(),
            LastModifiedBy = ?
         WHERE
            PositionID = ?
         LIMIT 1
      ";
        $p = array(
            $PositionCode,$name, $statusCode, $userid, (int) $id,
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function delete_position($id, $userid)
    {
        $sql = "UPDATE ktv_ref_position_type SET statusCode='nullified', DateUpdated = NOW(), LastModifiedBy = ? WHERE PositionID = ? LIMIT 1";
        $p   = array(
            $userid,
            (int) $id,
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
}
