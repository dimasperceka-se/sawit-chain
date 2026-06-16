<?php
class Mdocuments extends CI_Model {

    function readDocuments($key,$start,$limit){
        $sql = "
            select %s
            from ktv_documents
            left join sys_user ON CreatedBy=UserId
            WHERE FileName like ? AND ktv_documents.StatusCode != 'nullified'
            ORDER BY FileName %s";
        $query = $this->db->query(sprintf($sql,'*,UserName,REPLACE(REPLACE(REPLACE(FORMAT(FileSize/1024, 2), ".", "@"), ",", "."), "@", ",") as FileSize,
         IF(substr(FileName,length(FileName)-2,3)="pdf","pdf",IF(substr(FileName,length(FileName)-2,3)="doc","doc",
            IF(substr(FileName,length(FileName)-2,3)="xls","xls","File"))) as FileType','LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createDocuments($FileLabel,$FileName,$FileType,$FileSize,$userId){
        $sql = "
            INSERT INTO ktv_documents(FileLabel,FileName, FileType, FileSize, DateCreated, CreatedBy, DateUpdated, UpdatedBy)
            VALUES (?,?,?,?,now(),$userId,now(),$userId)";
        $query = $this->db->query($sql, array($FileLabel,$FileName,$FileType,$FileSize));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function deleteDocuments($id){
        //$sql = "DELETE FROM ktv_documents WHERE FileId=?";
        $sql="UPDATE ktv_documents SET StatusCode = 'nullified',DateUpdated=NOW(),UpdatedBy='".$_SESSION['userid']."' WHERE FileId = ? LIMIT 1";
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
