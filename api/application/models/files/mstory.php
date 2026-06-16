<?php
class Mstory extends CI_Model {

    function readStory($key,$start,$limit){
        $sql = "
            select %s
            from ktv_story ks
            left join ktv_farmer kcf ON ks.FarmerID=kcf.FarmerID
            left join ktv_cpg kc ON kc.CPGid=kcf.CPGid
            left join sys_user su ON ks.CreatedBy=su.UserId
            left join ktv_village kv ON kv.VillageID = kcf.VillageID
            left join ktv_subdistrict ksd ON kv.SubDistrictID = ksd.SubDistrictID
            left join ktv_district kd ON ksd.DistrictID = kd.DistrictID
            left join ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            left join (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) sq
               ON kcf.FarmerID=sq.FarmerID
            left join ktv_farmer_garden kcfg ON kcfg.FarmerID=sq.FarmerID and kcfg.GardenNr=sq.GardenNr
            WHERE ks.StatusCode != 'nullified' AND FarmerName like ? OR kcf.FarmerID like ? OR kcf.OldFarmerID like ?
            GROUP BY kcf.FarmerID
            ORDER BY File %s";
        $query = $this->db->query(sprintf($sql,'StoryID,kcf.FarmerID,FarmerName,Birthdate,GroupName,concat(Village,", ",SubDistrict,
            ", ",District) as desa,TotalLahan,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as produksi,
               Photo as photo,
               -- concat(Province,"/",Photo) as photo,
               File','LIMIT ?,?'),
            array("%$key%","%$key%","%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%","%$key%","%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readStorySearch($key,$start,$limit){
        $sql = "
            select %s
            from ktv_farmer kcf
            left join ktv_cpg kc ON kc.CPGid=kcf.CPGid
            left join ktv_village kv ON kv.VillageID = kcf.VillageID
            left join ktv_subdistrict ksd ON kv.SubDistrictID = ksd.SubDistrictID
            left join ktv_district kd ON ksd.DistrictID = kd.DistrictID
            left join ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            left join (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) sq
               ON kcf.FarmerID=sq.FarmerID
            left join ktv_farmer_garden kcfg ON kcfg.FarmerID=sq.FarmerID and kcfg.GardenNr=sq.GardenNr
            WHERE FarmerName like ? OR kcf.FarmerID like ? OR kcf.OldFarmerID like ?
            GROUP BY kcf.FarmerID
            ORDER BY FarmerName %s";
        $query = $this->db->query(sprintf($sql,'kcf.FarmerID,FarmerName,kcf.CPGid,Birthdate,GroupName,concat(Village,", ",SubDistrict,
            ", ",District) as desa,TotalLahan,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as produksi,
               concat(Province,"/",Photo) as photo','LIMIT ?,?'),
            array("%$key%","%$key%","%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%","%$key%","%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createStory($FarmerID,$File,$userId){
        $sql = "
            INSERT INTO ktv_story(FarmerID, File, DateCreated, CreatedBy, DateUpdated, LastModifiedBy)
            VALUES (?,?,now(),$userId,now(),$userId)";
        $query = $this->db->query($sql, array($FarmerID,$File));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateStory($StoryID,$File,$userId){
        $sql = "
            UPDATE ktv_story SET File=?, DateUpdated=now(), LastModifiedBy=$userId
            WHERE StoryID=?";
        $query = $this->db->query($sql, array($File,$StoryID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record update.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteStory($id){
        //$sql = "DELETE FROM ktv_story WHERE StoryID=?";
         $sql="UPDATE ktv_story SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE StoryID = ? LIMIT 1";
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
