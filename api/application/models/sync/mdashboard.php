<?php
class Mdashboard extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

   function readDashboards(){
         $sql_cpg ="
            select 'All' label, count(*) total FROM ktv_cpg
            UNION
             SELECT IFNULL(Province,'none') as label,count(CPGid) as total 
            from ktv_cpg 
            left join ktv_province on ProvinceID=substr(VillageID,1,2)
            GROUP BY Province";
         $sql_farmer = "
            select 'All' label, count(*) total FROM ktv_farmer
            UNION
            SELECT IFNULL(Province,'none') as label,count(FarmerID) as total 
            from ktv_farmer 
            left join ktv_province on ProvinceID=substr(VillageID,1,2)
            group by Province";
         $sql_luas = "
            SELECT 'All' label, SUM(IFNULL(GardenHaUnCertified, 0)) AS total 
            FROM
              ktv_farmer_garden a,
              (SELECT 
                FarmerID,
                MAX(SurveyNr) LatestSurveyNr 
              FROM
                ktv_farmer_garden 
              GROUP BY FarmerID) z,
              ktv_farmer c
            WHERE a.FarmerID = z.FarmerID 
              AND a.SurveyNr = z.LatestSurveyNr 
              AND a.FarmerID = c.FarmerID 
            UNION
            SELECT IFNULL(Province,'none') as label,sum(IFNULL(GardenHaUnCertified,0)) as total 
            FROM ktv_farmer_garden a,
            (select FarmerID,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID) z,
             ktv_farmer c, 
            ktv_province b
            WHERE
            a.FarmerID = z.FarmerID
            AND a.SurveyNr = z.LatestSurveyNr
            AND a.FarmerID = c.FarmerID
            AND SUBSTR(c.VillageID FROM 1 FOR 2)=b.ProvinceID
            GROUP BY  b.Province";
         $sql_pohon = "
            SELECT 'All' label,SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0)) AS total 
            FROM ktv_farmer_garden a,
            (SELECT FarmerID,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID) z,
            ktv_farmer b
            WHERE
            a.FarmerID=z.FarmerID
            AND a.SurveyNr=z.LatestSurveyNr
            AND a.FarmerID=b.FarmerID
            UNION
            SELECT IFNULL(Province,'none') as label,sum(IFNULL(PohonTBM,0))+sum(IFNULL(PohonTM,0))+sum(IFNULL(PohonRehab,0)) as total 
            from ktv_farmer_garden a,
            (select FarmerID,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID) z,
            ktv_farmer b,
            ktv_province c
            WHERE
            a.FarmerID=z.FarmerID
            AND a.SurveyNr=z.LatestSurveyNr
            AND a.FarmerID=b.FarmerID
            AND c.ProvinceID=substr(VillageID,1,2)
            group by Province";
         $sql_total = "
            SELECT 'All' label,SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS total
            FROM ktv_farmer_garden a,
            (SELECT FarmerID,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID) z,
            ktv_farmer b
            WHERE 
            a.FarmerID = z.FarmerID
            AND a.SurveyNr = z.LatestSurveyNr
            AND a.FarmerID=b.FarmerID
            UNION
            SELECT IFNULL(Province,'none') as label,SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS total
            FROM ktv_farmer_garden a,
            (SELECT FarmerID,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID) z,
            ktv_farmer b,
            ktv_province c
            WHERE 
            a.FarmerID = z.FarmerID
            AND a.SurveyNr = z.LatestSurveyNr
            AND a.FarmerID=b.FarmerID
            AND c.ProvinceID=substr(VillageID,1,2)
            group by Province";
         $sql_training = "
              SELECT 'All' as label,count(CpgBatchTrainingID) as total 
            from ktv_cpg_batch_trainings a
            UNION
            SELECT IFNULL(Province,'none') as label,count(CpgBatchTrainingID) as total 
            from ktv_cpg_batch_trainings a
            LEFT JOIN ktv_cpg b ON a.CPGid=b.CPGid
            left join ktv_province on ProvinceID=substr(VillageID,1,2)
            group by Province";
      
      $query_cpg = $this->db->query($sql_cpg);
      $query_farmer = $this->db->query($sql_farmer);
      $query_luas = $this->db->query($sql_luas);
      $query_pohon = $this->db->query($sql_pohon);
      $query_total = $this->db->query($sql_total);
      $query_training = $this->db->query($sql_training);
      
      $results['cpg'] = $query_cpg->result_array();
      $results['farmer'] = $query_farmer->result_array();
      $results['luas'] = $query_luas->result_array();
      $results['pohon'] = $query_pohon->result_array();
      $results['total'] = $query_total->result_array();
      $results['training'] = $query_training->result_array();
      return $results;
    }



}
?>
