<?php
/*
 * @Author: sonny.fitriawan 
 * @Date: 2017-12-07 16:50:30 
 * @Last Modified by: sonny.fitriawan
 * @Last Modified time: 2017-12-08 15:12:18
 */
class Mvehicle extends CI_Model
{

    public function vehicle_list($start, $limit,$sortingField,$sortingDir)
    {
        if($sortingField == "") $sortingField = 'BrandName';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                `BrandID`,
                `BrandName`,
                `StatusCode`
            FROM
              `ktv_ref_vehicle_brand`
            WHERE
                StatusCode != 'nullified'
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start,
            (int) $limit,
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function vehicle_id($id)
    {
        $sql = "SELECT
                  `BrandName`,
                  StatusCode
               FROM
                  `ktv_ref_vehicle_brand`
               WHERE
                  BrandID = ?
               LIMIT 1";
        $p = array(
            (int) $id,
        );
        $query = $this->db->query($sql, $p);
        return $query->result_array();
    }

    public function create_vehicle($name, $statusCode, $userid)
    {
        /* $sql = "INSERT INTO ktv_ref_vehicle_brand SET
         BrandName = ?,
         StatusCode = IF(?='','active',?),
         DateCreated = NOW(),
         CreatedBy = ?
        ";
        $p = array(
            $name, $statusCode, $userid,
        );
        $query = $this->db->query($sql, $p);
        */        
        $vehicleEntry = array(
            'BrandName' => $name,
            'StatusCode' => (empty($statusCode) ? 'active' : $statusCode),
            'DateCreated' => date('Y-m-d H:i:s'),
            'CreatedBy' => $userid 
        );

        $query = $this->db->insert('ktv_ref_vehicle_brand',$vehicleEntry);
        
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function update_vehicle($id, $name, $statusCode, $userid)
    {
        $sql = "UPDATE ktv_ref_vehicle_brand SET
            BrandName = ?,
            StatusCode = ?,
            DateUpdated = NOW(),
            LastModifiedBy = ?
         WHERE
            BrandID = ?
         LIMIT 1
      ";
        $p = array(
            $name, $statusCode, $userid, (int) $id,
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

    public function delete_vehicle($id, $userid)
    {
        $sql = "UPDATE ktv_ref_vehicle_brand SET statusCode='nullified', DateUpdated = NOW(), LastModifiedBy = ? WHERE BrandID = ? LIMIT 1";
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
