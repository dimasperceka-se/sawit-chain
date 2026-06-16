<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mbusinesssector extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getData($start, $limit)
    {
        $sql = "SELECT 
    BsnSectorID AS id
    , BsnSectorID
    , BsnSectorName
    , StatusCode
    , Remarks
    , DateCreated
    , CreatedBy
    , DateUpdated
    , LastModifiedBy 
FROM
    ktv_ref_business_sector 
WHERE
    StatusCode != 'nullified'
LIMIT ?, ?
        ";
        $query = $this->db->query($sql, array(intval($start),intval($limit)));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getDetail($id)
    {
        $sql = "SELECT 
    BsnSectorID AS id
    , BsnSectorID
    , BsnSectorName
    , StatusCode
    , Remarks
    , DateCreated
    , CreatedBy
    , DateUpdated
    , LastModifiedBy 
FROM
    ktv_ref_business_sector 
WHERE
    BsnSectorID = ?
LIMIT 1
        ";
        $query = $this->db->query($sql, array(intval($id)));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
        
    }

    public function insertData($BsnSectorName,$StatusCode,$Remarks)
    {
        $sql = "INSERT INTO ktv_ref_business_sector (
    BsnSectorName
    , StatusCode
    , Remarks
    , DateCreated
    , CreatedBy
    , DateUpdated
    , LastModifiedBy
) 
VALUES
    (
        ?
        , ?
        , ?
        , NOW()
        , ?
        , NOW()
        , ?
    )
        ";
        return $this->db->query($sql, array($BsnSectorName,$StatusCode,$Remarks,$_SESSION['userid'],$_SESSION['userid']));
    }

    public function updateData($BsnSectorName,$StatusCode,$Remarks,$BsnSectorID)
    {
        $sql = "UPDATE ktv_ref_business_sector 
SET        
    BsnSectorName = ?
    , StatusCode = ?
    , Remarks = ?
    , DateUpdated = NOW()
    , LastModifiedBy = ?
WHERE
    BsnSectorID = ?
    ";
        return $this->db->query($sql, array($BsnSectorName,$StatusCode,$Remarks,$_SESSION['userid'],$BsnSectorID));

    }

    public function deleteData($id)
    {
        // $sql = "DELETE FROM ktv_ref_business_sector WHERE BsnSectorID = ?";
        $sql = "UPDATE ktv_ref_business_sector SET StatusCode = 'nullified' WHERE BsnSectorID = ?";
        return $this->db->query($sql, array($id));
    }

    public function listSector()
    {
        $sql = "SELECT 
    BsnSectorID AS id 
    , BsnSectorName AS label
FROM
    ktv_ref_business_sector 
WHERE
    StatusCode = 'active'
ORDER BY label
        ";
        $query = $this->db->query($sql, array());
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

}

/* End of file mserviceprovider.php */
/* Location: ./application/models/mserviceprovider.php */