<?php
class Mpartner_mapping extends CI_Model {

    public function readTraining($PartnerID)
    {
        $sql = "
        SELECT
            a.PartnerID,
            a.PartnerID as id,
            a.PartnerParentID,
            a.PartnerFullName
        FROM
            `ktv_program_partner` a
        WHERE 
            a.PartnerID = ?
        ";
        $query = $this->db->query($sql, array($PartnerID));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function readtrainings()
    {
        $data['text'] = '.';
        $data['children'] = $this->getTrainingTree();
        return $data;
    }

    public function getTrainingParent()
    {
        $sql = "
        SELECT
            a.PartnerID,
            a.PartnerID as id,
            a.PartnerParentID,
            a.PartnerFullName as label
        FROM
            `ktv_program_partner` a
        WHERE 
            a.StatusCode != 'nullified'
            AND a.PartnerParentID IS null
        ORDER BY a.PartnerFullName ASC
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getTrainingTree($parent=0)
    {
        if($parent == 0 OR $parent == ""){
            $filter = " AND a.PartnerParentID IS NULL";
        }else{
            $filter = " AND a.PartnerParentID = '$parent'";
        }
        $sql = "
        SELECT
            a.PartnerID,
            a.PartnerParentID,
            a.PartnerFullName
        FROM
            `ktv_program_partner` a
        WHERE 
            a.StatusCode != 'nullified'
            $filter  
        ORDER BY a.PartnerFullName ASC
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            $data = $query->result_array();
            foreach ($data as $key => $value) {
                $children = $this->getTrainingTree($value['PartnerID']);
                if (!empty($children)) {
                    $data[$key]['children'] = $children;
                } else {
                    $data[$key]['leaf'] = true;
                }
            }
            return $data;
        }
        return false;
    }

    function updateTraining($ParentID, $PartnerID){
        $sql = "
            UPDATE ktv_program_partner
            SET 
                PartnerParentID = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                PartnerID = ?";
        $query = $this->db->query($sql, array($ParentID, $_SESSION['userid'], $PartnerID));
        if ($query) {
            $results['success'] = true;
            $results['msg'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['msg'] = "Failed to update record";
        }
        return $results;
    }

    function deletetraining($id){
        //$sql = "DELETE FROM ktv_cpg_trainings WHERE CpgTrainingsID=?";
        $sql="UPDATE ktv_cpg_trainings SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE CpgTrainingsID=? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['msg'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['msg'] = "Failed to delete record";
        }
        return $results;
    }



}
?>
