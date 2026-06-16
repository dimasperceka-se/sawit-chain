<?php
class Minstitution extends CI_Model {

    function readInstitutions($start,$limit){
        $sql = "
            select %s
            from ktv_institution
            ORDER BY InstitutionName %s";
        $query = $this->db->query(sprintf($sql,'InstitutionID as id,InstitutionName,PrivatePublic,IF(PrivatePublic="1",
            "Private",IF(PrivatePublic="2","Public","")) as nm','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readInstitution($id){
        $sql = "
            select InstitutionID as id, InstitutionName,PrivatePublic
            from ktv_institution
            WHERE InstitutionID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createInstitution($InstitutionName,$PrivatePublic){
        $sql = "
            INSERT INTO ktv_institution(InstitutionName,PrivatePublic) 
            VALUES (?,?)";
        $query = $this->db->query($sql, array($InstitutionName,$PrivatePublic));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateInstitution($InstitutionName,$PrivatePublic,$id){
        $sql = "
            UPDATE ktv_institution 
            SET InstitutionName=?,PrivatePublic=?
            WHERE InstitutionID=?";
        $query = $this->db->query($sql, array($InstitutionName,$PrivatePublic,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteInstitution($id){
        $sql = "
            DELETE FROM ktv_institution WHERE InstitutionID=?";
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
?>
