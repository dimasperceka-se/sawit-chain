<?php
class Mextension extends CI_Model {

    function readExtensions($start,$limit){
        $sql = "
            select %s
            from ktv_extension_staff
            LEFT JOIN ktv_institution ON InstitutionID=InstitutionID
            ORDER BY StaffName %s";
        $query = $this->db->query(sprintf($sql,'ExtensionID as id,ExtensionID as PersonID,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,InstitutionID,PositionID,InstitutionName','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readExtension($id){
        $sql = "
            select ExtensionID as id, ExtensionID as PersonID,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,InstitutionID,PositionID,InstitutionName
            from ktv_extension_staff
            LEFT JOIN ktv_institution ON InstitutionID=InstitutionID
            WHERE ExtensionID=?";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    function createExtension($PersonID,$WritingAwal,$WritingAkhir,$BallotAwal,$BallotAkhir,$InstitutionID,$PositionID){
        $sql = "
            INSERT INTO ktv_extension_staff(ExtensionID,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,InstitutionID,PositionID) 
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($PersonID,$WritingAwal,$WritingAkhir,$BallotAwal,$BallotAkhir,$InstitutionID,$PositionID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateExtension($PersonID,$WritingAwal,$WritingAkhir,$BallotAwal,$BallotAkhir,$InstitutionID,$PositionID,$id){
        $sql = "
            UPDATE ktv_extension_staff 
            SET ExtensionID=?,WritingAwal=?,WritingAkhir=?,BallotAwal=?,BallotAkhir=?,InstitutionID=?,PositionID=?
            WHERE ExtensionID=?";
        $query = $this->db->query($sql, array($PersonID,$WritingAwal,$WritingAkhir,$BallotAwal,$BallotAkhir,$InstitutionID,$PositionID,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteExtension($id){
        $sql = "
            DELETE FROM ktv_extension_staff WHERE ExtensionID=?";
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
    
    function readInstitutionIDs(){
        $sql = "
            select %s
            from ktv_institution
            ORDER BY InstitutionName";
        $query = $this->db->query(sprintf($sql,'InstitutionID as id,InstitutionName as label'));
        return $query->result_array();
    }

}
?>
