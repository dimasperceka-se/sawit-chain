<?php
    class Mfoto extends CI_Model {
        
        // =============================================================================================================
        // foto --- > mactivity
        // =============================================================================================================
            // FOTO Grid
            function grid_foto($id,$start,$limit){                
                $sql = "
                    SELECT %s
                    FROM ktv_monitoring_files 
                    LEFT JOIN sys_user   b    ON CreatedBy=b.UserId
                    WHERE MonitoringID=? 
                    ORDER BY DateCreated DESC %s";           
                $query = $this->db->query(sprintf($sql,'MonitoringFilesID as MF_id,MonitoringFilesID,MonitoringID,MonitoringID as MonID,
                    FilePath,FileName, FileTitle, b.UserRealName as nama,
                    IF(substr(FileName,length(FileName)-2,3)="png","png",
                    IF(substr(FileName,length(FileName)-2,3)="jpg","jpg",
                    IF(substr(FileName,length(FileName)-2,3)="bmp","bmp","-"))) as FileType, 
                    REPLACE(REPLACE(REPLACE(FORMAT(FileSize/1024, 2), ".", "@"), ",", "."), "@", ",") as FileSize,
                    DateCreated,CreatedBy','LIMIT ?,?'),
                    array($id,(int)$start,(int)$limit));  
                  //echo $this->db->last_query(); die();       
                $result['data']  = $query->result_array();
                $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array($id));
                $result['total'] = $query->row()->total;
                return $result;        
            }           
        // =============================================================================================================
        // foto CRUD
        // =============================================================================================================
        //  CREATE
            function create_foto($MonitoringID,$FileTitle,$FilePath,$FileName,$FileType,$FileSize,$userId){                
                $sql = "
                    INSERT INTO ktv_monitoring_files
                    (MonitoringID,FileTitle,FilePath,FileName,FileType,FileSize,CreatedBy,DateCreated)
                    VALUES 
                    (?,?,?,?,?,?,?,now())";
                $query = $this->db->query($sql, array($MonitoringID,$FileTitle,$FilePath,$FileName,$FileType,$FileSize,$userId));
                 //echo $this->db->last_query(); die(); 
                
                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "Record created.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Failed to created record";
                }
                return $results;
            }
        //  DELETE 1 - per file
            function delete_foto($id){
                $sql = "
                    DELETE FROM ktv_monitoring_files WHERE MonitoringFilesID=?";
                $query = $this->db->query($sql, array($id));
                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "Record deleted.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Failed to delete record";
                }
                return $results;
            }
        //  DELETE 2 - per aktivity
            function delete_fotos($id){
                $sql = "
                    DELETE FROM ktv_monitoring_files WHERE MonitoringID=?";
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
        // =============================================================================================================   
    }
?>
