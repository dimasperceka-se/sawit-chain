<?php
class Mcertification_body extends CI_Model {
    
    public function readCertificationBody($key, $start = 0, $limit = 50)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ktv_certification_body
                WHERE 1 = 1 AND StatusCode!='nullified'
                AND (CertBodyName LIKE ? OR CertBodyAddress LIKE ? OR CertBodyPhone LIKE ? OR CertBodyEmail LIKE ?)
                LIMIT ?, ?";
        $query = $this->db->query($sql,array("%$key%" ,"%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
            );
        }
        return false;
    }
    
    public function createCertificationBody($CertBodyName, $CertBodyAddress, $CertBodyPhone, $CertBodyEmail, $userid, $PhotoOld){
        $this->db->trans_start();
        $sql = "INSERT INTO ktv_certification_body (CertBodyName, CertBodyAddress, CertBodyPhone, CertBodyEmail, StatusCode, DateCreated, CreatedBy) VALUES (?,?,?,?,'active',NOW(),?)";
        $query = $this->db->query($sql, array($CertBodyName, $CertBodyAddress, $CertBodyPhone, $CertBodyEmail, $userid));
        $CertBodyID = $this->db->insert_id();

        if ($PhotoOld != '') {
            $Photo = "";
            $file = explode("images/certification_body/",$PhotoOld);

            //Insert ada photonya pakai aws
            if(file_exists('images/certification_body/'.$file[1])) {
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload('images/certification_body/'.$file[1],$file[1],AWSS3_CERT_BODY_PATH, 'images');
                if ($upload['success'] == true) {
                    delete_file($PhotoOld);
                    $Photo = $upload['filenamepath'];
                    $sql_update = "UPDATE ktv_certification_body SET CertBodyLogo=? WHERE CertBodyID=?";
                    $query = $this->db->query($sql_update, array($Photo, $CertBodyID));
                }
            }
        }else{
            $result['photo'] = "No Photo.";
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            $results['success'] = "true";
            $results['message'] = "Record created.";
            $results['CertBodyID'] = $CertBodyID;
            $results['CertBodyLogo'] = $config['file_name'];
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    
    public function readCertificationBodyDetail($CertBodyID){
        $this->load->library('awsfileupload');
        $sql = "SELECT * FROM ktv_certification_body WHERE CertBodyID=?";
        $query = $this->db->query($sql,array($CertBodyID));
        $return = $query->row_array();
        if($this->awsfileupload->doesObjectExist($return['CertBodyLogo']) == true) {
            $return['CertBodyLogoPath'] = $return['CertBodyLogo'];
            $return['CertBodyLogo'] = $this->config->item('CTCDN')."/".$return['CertBodyLogo'];
        }else{
            $return['CertBodyLogoPath'] = $return['CertBodyLogo'];
            $return['CertBodyLogo'] = base_url().$return['CertBodyLogo'];
        }
        return $return;
    }
    
    public function updateCertificationBody($CertBodyName, $CertBodyAddress, $CertBodyPhone, $CertBodyEmail, $userid, $CertBodyID){
        $this->db->trans_start();
        $CertBodyLogo = $this->db->query("SELECT CertBodyLogo FROM ktv_certification_body WHERE CertBodyID=?",array($CertBodyID))->row()->CertBodyLogo;
        $sql = "UPDATE ktv_certification_body SET CertBodyName=?, CertBodyAddress=?, CertBodyPhone=?, CertBodyEmail=?, DateUpdated=NOW(), LastModifiedBy=? WHERE CertBodyID=?";
        $query = $this->db->query($sql, array($CertBodyName, $CertBodyAddress, $CertBodyPhone, $CertBodyEmail, $userid, $CertBodyID));
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            $results['success'] = "true";
            $results['message'] = "Record updated.";
            $results['CertBodyID'] = $CertBodyID;
            $results['CertBodyLogo'] = $config['file_name'];
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function updateLogo($CertBodyID,$CertBodyLogo){
        $sql_update = "UPDATE ktv_certification_body SET CertBodyLogo=? WHERE CertBodyID=?";
        $query = $this->db->query($sql_update, array($CertBodyLogo, $CertBodyID));

        return true;
    }
    
    public function readContacts($CertBodyID, $key, $start = 0, $limit = 50)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                   *, CASE ContactGender WHEN 'm' THEN 'Male' WHEN 'f' THEN 'Female' END ContactGender
                FROM ktv_certification_body_contact 
                WHERE 1 = 1 AND StatusCode!='nullified' AND CertBodyID=? AND (ContactName LIKE ? OR ContactEmail LIKE ? OR ContactPhone LIKE ? OR ContactAddress LIKE ? OR ContactPosition LIKE ?)
                LIMIT ?, ?";
        $query = $this->db->query($sql,array($CertBodyID, "%$key%", "%$key%", "%$key%", "%$key%", "%$key%", intval($start),intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }
    
    public function createContact($CertBodyID, $ContactName, $ContactGender, $ContactEmail, $ContactPhone, $ContactAddress, $ContactPosition, $StatusCode, $userid){
        $sql = " INSERT INTO ktv_certification_body_contact(CertBodyID, ContactName, ContactGender, ContactEMail, ContactPhone, ContactAddress, ContactPosition, CreatedBy, DateCreated, StatusCode)
            VALUES (?,?,?,?,?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($CertBodyID, $ContactName, $ContactGender, $ContactEmail, $ContactPhone, $ContactAddress, $ContactPosition,  $StatusCode, $userid));
        if ($query){
            $results['success']     = true;
            $results['message']     = "Contact / Staff added.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to add  Contact / Staff";
        }
        return $results;
    }
    
    public function deleteContact($CertBodyContactID, $userid){
        $sql = "UPDATE ktv_certification_body_contact SET StatusCode='nullified', DateUpdated=NOW(), LastModifiedBy=? WHERE CertBodyContactID=?";
        $query = $this->db->query($sql, array($userid, $CertBodyContactID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }
    
    public function readCertificationBodyContactDetail($CertBodyContactID){
        $sql = "SELECT * FROM ktv_certification_body_contact WHERE CertBodyContactID=?";
        $query = $this->db->query($sql,array($CertBodyContactID));
        $return = $query->result_array();
        return $return[0];
    }
    
    public function updateContact($CertBodyID, $ContactName, $ContactGender, $ContactEmail, $ContactPhone, $ContactAddress, $ContactPosition, $StatusCode, $userid, $CertBodyContactID){
        $sql = "UPDATE ktv_certification_body_contact SET CertBodyID=?, ContactName=?, ContactGender=?, ContactEMail=?, ContactPhone=?, ContactAddress=?, ContactPosition=?, StatusCode=?, LastModifiedBy=?, DateUpdated=NOW() WHERE CertBodyContactID=?";
        $query = $this->db->query($sql, array($CertBodyID, $ContactName, $ContactGender, $ContactEmail, $ContactPhone, $ContactAddress, $ContactPosition, $StatusCode, $userid, $CertBodyContactID));
        if ($query){
            $results['success']     = true;
            $results['message']     = "Contact / Staff updated.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to update Contact / Staff";
        }
        return $results;
    }
    
    public function deleteCertificationBody($userid, $CertBodyID){
        $sql = "UPDATE ktv_certification_body SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE CertBodyID=?";
        $query = $this->db->query($sql, array($userid, $CertBodyID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }
}
?>
