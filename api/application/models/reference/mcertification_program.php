<?php
class Mcertification_program extends CI_Model {
    public function readCertificationPrograms($key, $start = 0, $limit = 50){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.CertProgID, a.CertProgName, a.CertProgOfficialName, a.CertProgLogo, a.CertProgAddress, a.CertProgPhone, a.CertProgEmail, a.CertProgWeb
                FROM ktv_ref_certification_program a
                WHERE 1 = 1 AND StatusCode != 'nullified' AND a.CertProgName LIKE %s
                LIMIT ?, ?";
        $query = $this->db->query(sprintf($sql, "'%{$key}%'"), array(intval($start), intval($limit)));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
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

    public function create_certification_program($CertProgName, $CertProgOfficialName, $CertProgAddress, $CertProgPhone, $CertProgEmail, $CertProgWeb, $PhotoOld){
        $this->db->trans_start();
        $sql = "INSERT INTO ktv_ref_certification_program (CertProgName, CertProgOfficialName, CertProgAddress, CertProgPhone, CertProgEmail, CertProgWeb, StatusCode, DateCreated, Createdby) VALUES (?,?,?,?,?,?,'active',NOW(),?)";
        $query = $this->db->query($sql, array($CertProgName, $CertProgOfficialName, $CertProgAddress, $CertProgPhone, $CertProgEmail, $CertProgWeb, $_SESSION['userid']));
        $CertProgID = $this->db->insert_id();  
        
        if ($PhotoOld != '') {

            $Photo = "";
            $file = explode("images/certification_provider/",$PhotoOld);

            //Insert ada photonya pakai aws
            if(file_exists('images/certification_provider/'.$file[0])) {
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload('images/certification_provider/'.$file[1],$file[1],AWSS3_CERT_PROG_PATH, 'images');
                if ($upload['success'] == true) {
                    delete_file($PhotoOld);
                    $Photo = $upload['filenamepath'];
                    $sql_update = "UPDATE ktv_ref_certification_program SET CertProgLogo=? WHERE CertProgID=?";
                    $query = $this->db->query($sql_update, array($Photo, $CertProgID));
                }
            }
        }else{
            $result['photo'] = "No Photo.";
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            $results['success'] = "true";
            $results['message'] = "Record created.";
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function readCertificationProgram($CertProgID){
        $this->load->library('awsfileupload');
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.CertProgID, a.CertProgName, a.CertProgOfficialName, a.CertProgLogo, a.CertProgAddress, a.CertProgPhone, a.CertProgEmail, a.CertProgWeb
                FROM ktv_ref_certification_program a
                WHERE 1 = 1 AND StatusCode != 'nullified' AND a.CertProgID=?";
        $query = $this->db->query($sql, array($CertProgID));
        $data = $query->row_array();

        if($this->awsfileupload->doesObjectExist($data['CertProgLogo']) == true) {
            $data['CertProgLogoPath'] = $data['CertProgLogo'];
            $data['CertProgLogo'] = $this->config->item('CTCDN')."/".$data['CertProgLogo'];
        }else{
            $data['CertProgLogoPath'] = $data['CertProgLogo'];
            $data['CertProgLogo'] = base_url().$data['CertProgLogo'];
        }
        //echo "<pre>".print_r($data,1);exit;
        return $data;
    }

    public function update_certification_program($CertProgName, $CertProgOfficialName, $CertProgAddress, $CertProgPhone, $CertProgEmail, $CertProgWeb, $CertProgID){
        $this->db->trans_start();
        $sql = "UPDATE ktv_ref_certification_program SET CertProgName=?, CertProgOfficialName=?, CertProgAddress=?, CertProgPhone=?, CertProgEmail=?, CertProgWeb=?, DateUpdated=NOW(), LastMOdifiedBy=? WHERE CertProgID=?";
        $query = $this->db->query($sql, array($CertProgName, $CertProgOfficialName, $CertProgAddress, $CertProgPhone, $CertProgEmail, $CertProgWeb, $_SESSION['userid'], $CertProgID));
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE){
            $results['success'] = "true";
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function updateLogo($CertProgID,$CertProgLogo){
        $sql_update = "UPDATE ktv_ref_certification_program SET CertProgLogo=? WHERE CertProgID=?";
        $query = $this->db->query($sql_update, array($CertProgLogo, $CertProgID));

        return true;
    }

    public function delete_certification_program($CertProgID){
        $CertProgLogo = $this->db->query("SELECT CertProgLogo FROM ktv_ref_certification_program WHERE CertProgID=?", array($CertProgID))->row()->CertProgLogo;
        $sql="UPDATE ktv_ref_certification_program SET StatusCode = 'nullified', LastMOdifiedBy=?, DateUpdated=NOW() WHERE CertProgID=?";
        $query = $this->db->query($sql, array($_SESSION['userid'],$CertProgID));
        if ($query) {
            if($CertProgLogo!=""){
                //@unlink('images/certification_provider/' . $CertProgLogo);
            }
            $results['success'] = "true";
            $results['message'] = "Deleted";
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
   
}
?>