<?php
    class Mactivity extends CI_Model {

        // =============================================================================================================
        // activity --- > mactivity
        // =============================================================================================================
            // GRID
            function  grid_Activity($start,$limit){
               
                $sql = "
                    SELECT %s
                    FROM ktv_monitoring a                    
                    ORDER BY a.DateCreated DESC %s";
                $query = $this->db->query(sprintf($sql,'MonitoringID,ObjectCategory,ObjectType,ObjectID,ObjectName,VillageID,
                    concat(ObjectID," - ",ObjectName) AS ObjectIDName,VisitDate,VisitTime,Description','LIMIT ?,?'),
                    array((int)$start,(int)$limit));
                //echo $this->db->last_query();
                $result['data']  = $query->result_array();
                $query           = $this->db->query(sprintf($sql,'count(*) as total',''));
                $result['total'] = $query->row()->total;
                return $result;
            }
            // FILTER
            function  filter_Activity($id,$start,$limit){
                 $sql = "
                    SELECT %s
                    FROM ktv_monitoring a                    
                    WHERE a.ObjectCategory=? 
                    ORDER BY a.DateCreated DESC %s";           
                $query = $this->db->query(sprintf($sql,'MonitoringID,ObjectCategory,ObjectType,ObjectID,ObjectName,VillageID,
                    concat(ObjectID," - ",ObjectName) AS ObjectIDName,VisitDate,VisitTime,Description','LIMIT ?,?'),
                    array($id,(int)$start,(int)$limit));  
                  //echo $this->db->last_query(); die();       
                $result['data']  = $query->result_array();
                $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array($id));
                $result['total'] = $query->row()->total;
                return $result;                
            }
             // SEARCH
            function search_Activity($id,$start,$limit){
                $sql = "
                    SELECT %s
                    FROM ktv_monitoring a                    
                    WHERE a.VillageID=? 
                    ORDER BY a.DateCreated DESC %s";           
                $query = $this->db->query(sprintf($sql,'MonitoringID,ObjectCategory,ObjectType,ObjectID,ObjectName,VillageID,
                    concat(ObjectID," - ",ObjectName) AS ObjectIDName,VisitDate,VisitTime,Description','LIMIT ?,?'),
                    array($id,(int)$start,(int)$limit));  
                  //echo $this->db->last_query(); die();       
                $result['data']  = $query->result_array();
                $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array($id));
                $result['total'] = $query->row()->total;
                return $result;
            }
            function search_Activitys($id,$key,$start,$limit){                
                $sql = "
                    SELECT %s
                    FROM ktv_monitoring a
                    WHERE a.VillageID=? AND a.ObjectCategory=?
                    ORDER BY a.DateCreated DESC %s";           
                $query = $this->db->query(sprintf($sql,'MonitoringID,ObjectCategory,ObjectType,ObjectID,ObjectName,VillageID,
                    concat(ObjectID," - ",ObjectName) AS ObjectIDName, VisitDate,VisitTime,Description','LIMIT ?,?'),                    
                    array($id,$key,(int)$start,(int)$limit)); 
                  //echo $this->db->last_query(); die();       
                $result['data']  = $query->result_array();
                $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array($kab,"%$key%"));
                $result['total'] = $query->row()->total;
                return $result;                       
            }           
        // =============================================================================================================
        // Activity CRUD
        // =============================================================================================================
            // CREATE
            function create_Activity($ObjectCategory,$ObjectType,$ObjectID,$ObjectName,$VillageID,$Description,$VisitDate,$VisitTime,$userid){               

                $sql = "
                    INSERT INTO ktv_monitoring(ObjectCategory,ObjectType,ObjectID,ObjectName,VillageID,Description,VisitDate,VisitTime,CreatedBy,DateCreated)             
                    VALUES (?,?,?,?,?,?,?,?,?,now())";
                
                $query = $this->db->query($sql, array($ObjectCategory,$ObjectType,$ObjectID,$ObjectName,$VillageID,$Description,$VisitDate,$VisitTime,$userid));
                //echo $this->db->last_query(); //die();  
                if ($query) {
                    
                    $sql_periksa = "
                        SELECT count(MonitoringID) as jumlah, max(MonitoringID) as id FROM ktv_monitoring ";

                    $query  = $this->db->query($sql_periksa);
                    $result = $query->result_array();
                    if ($result[0]['jumlah'] > 0) {
                       $loadMonID    = $result[0]['id'];
                    } else{ 
                        $loadMonID   = $result[0]['id']+1;
                    }

                    $results['success'] = true;
                    $results['message'] = "record created.";
                    $results['newMon']  = $loadMonID;
                } else {
                    $results['success'] = false;
                    $results['message'] = "Failed to create record";
                }
                return $results;
            } 
            // READ
            function read_Activity($id){
                $sql = "
                    SELECT MonitoringId,ObjectCategory,ObjectType, ObjectID, ObjectName,VillageID,VisitDate,VisitTime,Description
                    FROM ktv_monitoring 
                    WHERE MonitoringId=?";
                $query  = $this->db->query($sql, array($id));
                $result = $query->result_array();
                return $result[0];
            }
            // UPDATE
            function update_Activity($id,$ObjectCategory,$ObjectType,$ObjectID,$ObjectName,$VillageID,$Description,$VisitDate,$VisitTime,$userid){
                $sql = "
                    UPDATE ktv_monitoring 
                    SET ObjectCategory=?,
                        ObjectType=?,   
                        ObjectID=?,
                        ObjectName=?,
                        VillageID=?,
                        Description=?,
                        VisitDate=?,
                        VisitTime=?,
                        LastModifiedBy=?,
                        DateUpdated=now()
                    WHERE MonitoringID=?";
                $query = $this->db->query($sql, array($ObjectCategory,$ObjectType,$ObjectID,$ObjectName,$VillageID,$Description,$VisitDate,$VisitTime,$userid,$id));
                //echo $this->db->last_query(); die();  
                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "record updated.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Failed to update record";
                }
                return $results;        
            }
            // DELETE
            function delete_Activity($id){
                
                $sql_cek = "
                        SELECT MonitoringFilesID, FilePath, FileName
                        FROM ktv_monitoring_files WHERE MonitoringID=?";
                $query  = $this->db->query($sql_cek, array($id));
                if ($query->num_rows() > 0) {
                    $result = $query->result_array();
                    // echo '<pre>'; print_r($result); echo '</pre>'; exit;
                    // $results['success'] = false;
                    // $results['message'] = "Failed to delete record, because activity have photo !";
                    // return $results;
                    // move files to backup
                    foreach ($result as $key => $value) {
                        $from = $value['FilePath'].'/'.$value['FileName'];
                        $to = $value['FilePath'].'/backup/'.$value['FileName'];
                        @rename($from, $to);
                    }
                }
                
                $sql = "
                    DELETE FROM ktv_monitoring WHERE MonitoringID=?";
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
            function readProvinsis(){
                $sql = "
                    SELECT distinct Province as label,ProvinceID as id
                    FROM ktv_province
                    ORDER BY Province";
                $query = $this->db->query($sql);

                $result['data'] = $query->result_array();
                return $result;               
            }
            function readKabupatens($provID=''){
                $sql = "
                    SELECT distinct a.District as label, DistrictID as id
                    FROM ktv_district a
                    LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                    WHERE b.ProvinceID = ?
                    ORDER BY District";
                $query = $this->db->query(sprintf($sql), array($provID));
                $result['data'] = $query->result_array();
                return $result;
            }            
            function readKecamatans($key){
                $sql = "
                    SELECT distinct SubDistrict as label, SubDistrictID as id
                    FROM ktv_subdistrict a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
                    WHERE a.DistrictID = ?
                    ORDER BY SubDistrict";
                $query = $this->db->query($sql, array($key));
                $return['data'] = $query->result_array();
                return $return;
            }
            function readDesas($key,$kab=''){
                $sql = "
                    SELECT distinct %s as label, VillageID as id
                    FROM ktv_village a
                    LEFT JOIN ktv_subdistrict b ON a.SubDistrictID=b.SubDistrictID
                    LEFT JOIN ktv_district c ON b.SubDistrictID=c.DistrictID
                    WHERE %s
                    ORDER BY a.SubDistrictID,a.Village";
                if ($kab!='') $query = $this->db->query(sprintf($sql,"concat(SubDistrict,' - ',Village)",'DistrictID = ?'), array($kab));
                else $query = $this->db->query(sprintf($sql,'Village','a.SubDistrictID = ?'), array($key));
                
                $return['data'] = $query->result_array();
                return $return;
            }
    }
?>
