<?php
class Mgallery extends CI_Model {
   
    function tampil_Gallery($start,$limit){        
        $sql = "
            SELECT %s 
            FROM  ktv_monitoring a
            JOIN ( 
                SELECT MonitoringID,MonitoringFilesID,FileTitle,FilePath,FileName,DateCreated,CreatedBy,UserRealName
                FROM ktv_monitoring_files 
                LEFT JOIN sys_user       ON CreatedBy=UserId
                ORDER BY DateCreated DESC 
                )  b ON a.MonitoringID = b.MonitoringID
            GROUP BY a.MonitoringID
            ORDER BY b.DateCreated DESC %s";
                
        $query = $this->db->query(sprintf($sql,'b.MonitoringFilesID,a.MonitoringID,b.FileTitle,b.FilePath,b.FileName,b.DateCreated,b.CreatedBy,
            b.UserRealName as nama,a.Description as ket',' LIMIT ?,?'),array((int)$start,(int)$limit));

        $result['data'] = $query->result_array();               
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;        
        return $result;
    }

    function cari_Gallery($category,$type,$key,$start,$limit){        
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
            %s
            FROM  ktv_monitoring a
            JOIN ( 
                SELECT MonitoringID,MonitoringFilesID,FileTitle,FilePath,FileName,DateCreated,CreatedBy,UserRealName
                FROM ktv_monitoring_files 
                   LEFT JOIN sys_user       ON CreatedBy=UserId               
                ORDER BY DateCreated DESC 
                )  b ON a.MonitoringID = b.MonitoringID 
            WHERE 1 = 1
            AND (a.ObjectCategory = ? OR '' = ?)
            AND (a.ObjectType = ? OR '' = ?)
            AND b.FileTitle LIKE ?           
            GROUP BY a.MonitoringID
            ORDER BY b.DateCreated DESC %s";
                
        $query = $this->db->query(sprintf($sql,'b.MonitoringFilesID,a.MonitoringID,b.FileTitle,b.FilePath,b.FileName,b.DateCreated,b.CreatedBy,b.UserRealName as nama,a.Description as ket',' LIMIT ?,?'),array($category,$category,$type,$type,"%$key%",(int)$start,(int)$limit));
        
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        
        $result['data'] = $query->result_array();               
        $query           = $this->db->query("SELECT FOUND_ROWS() AS total");
        $result['total'] = $query->row()->total;
        return $result;

    }   
}
?>
