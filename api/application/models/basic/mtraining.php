<?php
class Mtraining extends CI_Model {

    // function readtrainings($start,$limit){
    //     $sql = "
    //         SELECT
    //            %s
    //         from
    //            ktv_cpg_trainings
    //         WHERE
    //            StatusCode != 'nullified'
    //         ORDER BY CpgTrainingsID %s";
    //     $query = $this->db->query(sprintf($sql,'CpgTrainingsID as id,CpgTrainings as name','LIMIT ?,?'),
    //         array((int)$start,(int)$limit));
    //     $result['data'] = $query->result_array();
    //     $query = $this->db->query(sprintf($sql,'count(*) as total',''));
    //     $result['total'] = $query->row()->total;
    //     return $result;
    // }
    public function readTraining($CPGTrainingsID)
    {
        $sql = "
SELECT 
    CpgTrainingsID
    , ParentID
    , CpgTrainings
    , AltName
    , CpgAbbre
FROM
    ktv_cpg_trainings 
WHERE
    CpgTrainingsID = ?
        ";
        $query = $this->db->query($sql, array($CPGTrainingsID));
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
    CpgTrainingsID AS id
    , CpgTrainings AS label
FROM
    ktv_cpg_trainings 
WHERE
    ParentID = '0'
    AND StatusCode != 'nullified'
ORDER BY label
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getTrainingTree($parent='0')
    {
        $sql = "
SELECT
    t.CpgTrainingsID,
    t.CpgTrainings,
    t.AltName,
    t.CpgAbbre
FROM ktv_cpg_trainings t
WHERE
    StatusCode != 'nullified'
    AND t.ParentID = ?
ORDER BY `CpgTrainings`    
        ";
        $query = $this->db->query($sql, array($parent));
        if ($query->num_rows()>0) {
            $data = $query->result_array();
            foreach ($data as $key => $value) {
                $children = $this->getTrainingTree($value['CpgTrainingsID']);
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

    function createTraining($ParentID, $CpgTrainings, $CpgAbbre){
        $sql = "
            INSERT INTO ktv_cpg_trainings(CpgTrainingsID,ParentID,CpgTrainings,CpgAbbre,DateCreated,CreatedBy)
            SELECT max(CpgTrainingsID)+1,?,?,?,NOW(),? FROM ktv_cpg_trainings";
        $query = $this->db->query($sql, array($ParentID, $CpgTrainings, $CpgAbbre, $_SESSION['userid']));
        if ($query) {
            $results['success'] = true;
            $results['msg'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['msg'] = "Failed to create record";
        }
        return $results;
    }

    function updateTraining($ParentID, $CpgTrainings, $CpgAbbre, $CPGTrainingsID){
        $sql = "
            UPDATE ktv_cpg_trainings
            SET 
                ParentID = ?,
                CpgTrainings = ?,
                CpgAbbre = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE CpgTrainingsID=?";
        $query = $this->db->query($sql, array($ParentID, $CpgTrainings, $CpgAbbre, $_SESSION['userid'], $CPGTrainingsID));
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
