<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mcooperativetraining extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getData($start, $limit)
    {
        $sql = "SELECT 
    CoopTrainingID AS id
    , CoopTrainingID
    , CoopTrainingName
    , AltName
    , Abbreviation
    , StatusCode
    , Remarks
    , DateCreated
    , CreatedBy
    , DateUpdated
    , LastModifiedBy 
FROM
    ktv_ref_cooperative_trainings 
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
    CoopTrainingID AS id
    , CoopTrainingID
    , CoopTrainingName
    , AltName
    , Abbreviation
    , StatusCode
    , Remarks
    , DateCreated
    , CreatedBy
    , DateUpdated
    , LastModifiedBy 
FROM
    ktv_ref_cooperative_trainings  
WHERE
    CoopTrainingID = ?
LIMIT 1
        ";
        $query = $this->db->query($sql, array(intval($id)));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
        
    }

    public function insertData($CoopTrainingName,$AltName,$Abbreviation,$StatusCode,$Remarks)
    {
        $sql = "INSERT INTO ktv_ref_cooperative_trainings (
    CoopTrainingName
    , AltName
    , Abbreviation
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
        , ?
        , ?
        , NOW()
        , ?
        , NOW()
        , ?
    )
        ";
        return $this->db->query($sql, array($CoopTrainingName,$AltName,$Abbreviation,$StatusCode,$Remarks,$_SESSION['userid'],$_SESSION['userid']));
    }

    public function updateData($CoopTrainingName,$AltName,$Abbreviation,$StatusCode,$Remarks,$CoopTrainingID)
    {
        $sql = "UPDATE ktv_ref_cooperative_trainings 
SET        
    CoopTrainingName = ?
    , AltName = ?
    , Abbreviation = ?
    , StatusCode = ?
    , Remarks = ?
    , DateUpdated = NOW()
    , LastModifiedBy = ?
WHERE
    CoopTrainingID = ?
    ";
        return $this->db->query($sql, array($CoopTrainingName,$AltName,$Abbreviation,$StatusCode,$Remarks,$_SESSION['userid'],$CoopTrainingID));

    }

    public function deleteData($id)
    {
        // $sql = "DELETE FROM ktv_ref_cooperative_trainings WHERE CoopTrainingID = ?";
        $sql = "UPDATE ktv_ref_cooperative_trainings SET StatusCode = 'nullified' WHERE CoopTrainingID = ?";
        return $this->db->query($sql, array($id));
    }

    public function listSector()
    {
        $sql = "SELECT 
    CoopTrainingID AS id 
    , CoopTrainingName AS label
FROM
    ktv_ref_cooperative_trainings 
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