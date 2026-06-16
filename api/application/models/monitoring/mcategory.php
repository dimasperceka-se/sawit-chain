<?php
class Mcategory extends CI_Model {

    // Farmer 
    function cat_Farmers($key,$start,$limit){
        $sql = "
            SELECT %s
            FROM ktv_farmer kcf
            LEFT JOIN sce_farmer sf ON kcf.FarmerID=sf.FarmerID
            LEFT JOIN ktv_cpg kc    ON kcf.CPGid=kc.CPGid            
            WHERE sf.FarmerID is null and concat(kcf.FarmerID,kcf.FarmerName) LIKE ? %s";
        $add=null;
        $query = $this->db->query(sprintf($sql,'kcf.FarmerID id,FarmerName name,kcf.VillageID village', $add.' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));        
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',$add), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }
    // Farmer 
    function cat_Gardens($key,$start,$limit){
        $sql = "
            SELECT %s
            FROM ktv_farmer_garden kcfg
            JOIN ktv_farmer kcf ON kcf.FarmerID = kcfg.FarmerID
            WHERE concat(kcf.FarmerID,kcf.FarmerName) LIKE ? %s
            ";
        $add='GROUP BY kcfg.FarmerID, kcfg.GardenNr ORDER BY FarmerName,GardenNr';
        $query = $this->db->query(sprintf($sql,'CONCAT(kcfg.FarmerID,"-",kcfg.GardenNr) id,FarmerName name,kcf.VillageID village', $add.' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));        
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',$add), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    } 
    // CPG
    function cat_CPG($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM ktv_cpg kcpg               
            WHERE kcpg.CPGid is not null and concat(kcpg.CPGid,kcpg.GroupName) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kcpg.CPGid id,kcpg.GroupName name, kcpg.VillageID village', ' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

     // Demoplot
    function cat_Demoplot($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM ktv_cpg_demoplot kcp
            LEFT JOIN ktv_cpg sf ON kcp.CPGid=sf.CPGid            
            WHERE kcp.CpgDemoplotID is not null and concat(kcp.CpgDemoplotID,sf.GroupName,kcp.CPGid) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kcp.CpgDemoplotID id, CONCAT("[",sf.CPGid,"] ",sf.GroupName) name, sf.VillageID village',' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    } 
    
    // Coop
    function cat_Coop($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM ktv_cooperatives kc            
            WHERE kc.CoopID is not null and concat(kc.CoopID,kc.CoopName) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kc.CoopID id,kc.CoopName name, kc.VillageID village',' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }
    // Warehouse
    function cat_Warehouse($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM ktv_warehouse kcp            
            WHERE kcp.WarehouseID is not null and concat(kcp.WarehouseID,kcp.WarehouseName) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kcp.WarehouseID id, kcp.WarehouseName name, kcp.VillageID village',' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }
    // Trader
    function cat_Trader($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM ktv_traders kcp            
            WHERE kcp.TraderID is not null and concat(kcp.TraderID,kcp.TraderName,kcp.Company) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kcp.TraderID id, CONCAT(kcp.TraderName," [",kcp.Company,"]") name, kcp.VillageID village',' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }
    // SCE
    function cat_SCE($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM sce_farmer kcp
            LEFT JOIN ktv_farmer sf ON kcp.FarmerID=sf.FarmerID            
            WHERE kcp.SceID is not null and concat(kcp.SceID,sf.FarmerName,sf.FarmerID) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kcp.SceID id, CONCAT("[",sf.FarmerID,"] ",sf.FarmerName) name, sf.VillageID village',' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }  
    // SCE
    function cat_Village($key,$start,$limit){      
        $sql = "
            SELECT %s
            FROM ktv_village kcp
            WHERE CONCAT(kcp.VillageID,kcp.Village) LIKE ? %s";
        $query           = $this->db->query(sprintf($sql,'kcp.VillageID id, kcp.Village name, kcp.VillageID village',' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));
        $result['data']  = $query->result_array();
        $query           = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }   
    
}
?>
