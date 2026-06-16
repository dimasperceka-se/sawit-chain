<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mserviceprovider extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getData($start, $limit)
    {
        $sql = "SELECT 
    sp.ServiceProvID AS id
    , sp.ServiceProvID
    , sp.ServiceProvName
    , sp.OfficialName
    , sp.Abbreviation
    , sp.BsnSectorID
    , BsnSectorName AS Sector
    , sp.Address
    , sp.DistrictID
    , kd.District
    , kd.ProvinceID
    , sp.OfficialPhone
    , sp.OfficialEmail
    , sp.Photo
    , sp.Logo
    , sp.StatusCode
    , sp.Remarks
    , sp.DateSynced
    , sp.DateCreated
    , sp.CreatedBy
    , sp.DateUpdated
    , sp.LastModifiedBy 
FROM
    ktv_service_provider sp
LEFT JOIN ktv_district kd ON sp.DistrictID = kd.DistrictID
LEFT JOIN ktv_ref_business_sector bs ON bs.BsnSectorID = sp.BsnSectorID
WHERE
    sp.StatusCode != 'nullified'
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
    sp.ServiceProvID AS id
    , sp.ServiceProvID
    , sp.ServiceProvName
    , sp.OfficialName
    , sp.Abbreviation
    , sp.BsnSectorID
    , BsnSectorName AS Sector
    , sp.Address
    , sp.DistrictID
    , kd.District
    , kd.ProvinceID
    , sp.OfficialPhone
    , sp.OfficialEmail
    , sp.Photo
    , sp.Logo
    , sp.StatusCode
    , sp.Remarks
    , sp.DateSynced
    , sp.DateCreated
    , sp.CreatedBy
    , sp.DateUpdated
    , sp.LastModifiedBy 
FROM
    ktv_service_provider sp
LEFT JOIN ktv_district kd ON sp.DistrictID = kd.DistrictID
LEFT JOIN ktv_ref_business_sector bs ON bs.BsnSectorID = sp.BsnSectorID
WHERE
    ServiceProvID = ?
LIMIT 1
        ";
        $query = $this->db->query($sql, array(intval($id)));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
        
    }

    public function insertData($ServiceProvName,$OfficialName,$Abbreviation,$BsnSectorID,$Address,$DistrictID,$OfficialPhone,$OfficialEmail,$StatusCode,$Remarks)
    {
        $sql = "INSERT INTO ktv_service_provider (
    ServiceProvName
    , OfficialName
    , Abbreviation
    , BsnSectorID
    , Address
    , DistrictID
    , OfficialPhone
    , OfficialEmail
    , StatusCode
    , Remarks
    , DateSynced
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
        , ?
        , ?
        , ?
        , ?
        , ?
        , NULL
        , NOW()
        , ?
        , NOW()
        , ?
    )
        ";
        $result = $this->db->query($sql, array($ServiceProvName,$OfficialName,$Abbreviation,$BsnSectorID,$Address,$DistrictID,$OfficialPhone,$OfficialEmail,$StatusCode,$Remarks,$_SESSION['userid'],$_SESSION['userid']));
        if ($result) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updateData($ServiceProvName,$OfficialName,$Abbreviation,$BsnSectorID,$Address,$DistrictID,$OfficialPhone,$OfficialEmail,$StatusCode,$Remarks,$ServiceProvID)
    {
        $sql = "UPDATE ktv_service_provider
SET        
    ServiceProvName = ?
    , OfficialName = ?
    , Abbreviation = ?
    , BsnSectorID = ?
    , Address = ?
    , DistrictID = ?
    , OfficialPhone = ?
    , OfficialEmail = ?
    , StatusCode = ?
    , Remarks = ?
    , DateUpdated = NOW()
    , LastModifiedBy = ?
WHERE
    ServiceProvID = ?
    ";
        return $this->db->query($sql, array($ServiceProvName,$OfficialName,$Abbreviation,$BsnSectorID,$Address,$DistrictID,$OfficialPhone,$OfficialEmail,$StatusCode,$Remarks,$_SESSION['userid'],$ServiceProvID));

    }

    public function deleteData($id)
    {
        // $sql = "DELETE FROM ktv_service_provider WHERE ServiceProvID = ?";
        $sql = "UPDATE ktv_service_provider SET StatusCode = 'nullified' WHERE ServiceProvID = ?";
        return $this->db->query($sql, array($id));
    }

    public function updatePhoto($file, $id)
    {
        $sql = "UPDATE ktv_service_provider SET Photo = ? WHERE ServiceProvID = ?";
        return $this->db->query($sql, array($file, intval($id)));
    }

    public function updateLogo($file, $id)
    {
        $sql = "UPDATE ktv_service_provider SET Logo = ? WHERE ServiceProvID = ?";
        return $this->db->query($sql, array($file, intval($id)));
    }

    public function listServiceProvider()
    {
        $sql = "SELECT 
    ServiceProvID AS id 
    , ServiceProvName AS label
FROM
    ktv_service_provider 
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