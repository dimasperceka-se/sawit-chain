<?php
class Msurvey extends CI_Model {

    function readSurveys($start,$limit){
        $sql = "
            select %s
            from ktv_survey
            WHERE
               StatusCode != 'nullified'
            ORDER BY SurveyTxt %s";
        $query = $this->db->query(sprintf($sql,'SurveyID as id,SurveyNr as nr,SurveyTxt as name','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createSurvey($nr,$name){
        $sql = "
            INSERT INTO ktv_survey(SurveyNr,SurveyTxt)
            VALUES (?,?)";
        $query = $this->db->query($sql, array($nr,$name));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateSurvey($nr,$name,$id){
        $sql = "
            UPDATE ktv_survey
            SET SurveyNr=?,SurveyTxt=?
            WHERE SurveyID=?";
        $query = $this->db->query($sql, array($nr,$name,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletesurvey($id){
        //$sql = "DELETE FROM ktv_survey WHERE SurveyID=?";
        $sql="UPDATE ktv_survey SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE SurveyID=? LIMIT 1";
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
