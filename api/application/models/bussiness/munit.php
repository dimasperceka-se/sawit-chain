<?php
class Munit extends CI_Model
{

    public function readDatas($sce_id,$start, $limit)
    {
        $sql = "
            select %s
            from ktv_inventory_unitmeasurement kic
            WHERE
                OrgType = 'sce' AND
                OrgID = ?
            %s";
        $query = $this->db->query(sprintf($sql, 'UnitMeasurementID,OrgType, OrgID, Name, Description', 'LIMIT ?,?'),
            array($sce_id,(int) $start, (int) $limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql, 'count(*) as total', ''),array($sce_id));
        $total           = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    public function readData($UnitMeasurementID)
    {
        $sql = "
            select *
            from ktv_inventory_unitmeasurement
            WHERE UnitMeasurementID=?";
        $query = $this->db->query($sql, array($UnitMeasurementID));
        $data  = $query->result_array();
        return $data[0];
    }

    public function getUnitComboData($sce_id){
        $sql="SELECT
                UnitMeasurementID id,
                `Name` label
            FROM
                ktv_inventory_unitmeasurement
            WHERE
                OrgType = 'sce' AND OrgID = ?
            ORDER BY `Name`";
        $query = $this->db->query($sql, array($sce_id));
        return $query->result_array();
    }

    public function createData($OrgType, $OrgID, $Name, $Description)
    {
        $sql = "
            INSERT INTO ktv_inventory_unitmeasurement(OrgType, OrgID, Name, Description)
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType, $OrgID, $Name, $Description));
        if ($query) {
            $results['UnitMeasurementID'] = $this->db->insert_id();
            $results['success']           = true;
            $results['message']           = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    public function updateData($Name, $Description, $UnitMeasurementID)
    {
        $sql = "
            UPDATE ktv_inventory_unitmeasurement
            SET Name=?, Description=?
            WHERE UnitMeasurementID=?";
        $query = $this->db->query($sql, array($Name, $Description, $UnitMeasurementID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    public function deleteData($id)
    {
        $sql = "
            DELETE FROM ktv_inventory_unitmeasurement WHERE UnitMeasurementID=?";
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

}
