<?php

class Mdashboard extends CI_Model
{

    function __construct()
    {
        parent::__construct();

        $this->month_list = "
    SELECT
        DATE_FORMAT(a.date, '%Y%m') AS 'yearmonth'
        , DATE_FORMAT(a.date, '%Y') AS 'year'
        , DATE_FORMAT(a.date, '%m') AS 'month'
    FROM (
        SELECT ? - INTERVAL (A.A + (10 * B.A) + (100 * C.A)) MONTH AS DATE
        FROM (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS A
        CROSS JOIN (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS B
        CROSS JOIN (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS C
    ) a
    WHERE a.date BETWEEN ? AND ?
    ORDER BY DATE
        ";
        //main dashboard
        $this->main_cpg = "
            SELECT count(aa.CPGid) as total
            from (
SELECT
 cpg.*
FROM ktv_cpg cpg
JOIN `ktv_cpg_batch_trainings` cbt ON cbt.`CPGid` = cpg.`CPGid` AND TrainingStart > 0
GROUP BY cpg.`CPGid`
) aa
            WHERE VillageID %s";
        $this->main_farmer = "SELECT COUNT(DISTINCT FarmerID) AS total FROM (
  SELECT *
  FROM (
   SELECT
      kcfg.FarmerID,
      CPGtrainingsID,
      VillageID,
      kcf.CPGid,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
   UNION ALL
   SELECT
      kcfg.FarmerID,
      CPGtrainingsID,
      VillageID,
      kcf.CPGid,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_kader_trainings_participants` kcfg
   LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
   )m GROUP BY FarmerID, CPGtrainingsID
) aa
WHERE
  CPGtrainingsID=1 %s";
        $this->main_luas = "
            SELECT sum(IFNULL(GardenHaUnCertified,0)) as total
            FROM ktv_farmer_garden a,
               (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z,
               ktv_farmer_view aa
            WHERE aa.StatusCode='active' and a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
               AND a.FarmerID = aa.FarmerID and a.GardenNr = z.GardenNr %s";
        $this->main_pohon = "
            SELECT sum(IFNULL(PohonTBM,0))+sum(IFNULL(PohonTM,0))+sum(IFNULL(PohonRehab,0)) as total
            FROM ktv_farmer_garden a,
               (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z,
               ktv_farmer_view aa
            WHERE aa.StatusCode='active' and a.FarmerID=z.FarmerID AND a.SurveyNr=z.LatestSurveyNr and aa.VillageId is not null
               AND a.FarmerID=aa.FarmerID and a.GardenNr = z.GardenNr %s";
        $this->main_total = "
            SELECT sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as total,
               sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
               SUM(IFNULL(GardenHaUnCertified,0))  as produktivitas
            from ktv_farmer_garden a,
               (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z,
               ktv_farmer_view aa
            WHERE aa.StatusCode='active' and a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
               AND GardenHaUnCertified>0 and a.FarmerID=aa.FarmerID %s";
        $this->main_training = "
            SELECT count(DISTINCT FarmerID) total FROM (
               SELECT kcf.VillageID,kcf.CPGid,kcf.`FarmerID`
               FROM ktv_cpg_batch_trainings kcbt, ktv_cpg_batch_trainings_farmers kcbtf,ktv_farmer_view kcf
               WHERE CPGtrainingsID=2 AND kcbt.CpgBatchTrainingID=kcbtf.CpgBatchTrainingID AND kcbtf.FarmerID=kcf.FarmerID AND kcf.StatusCode = 'active'
               UNION ALL
               SELECT kcf.VillageID,kcf.CPGid,kcf.`FarmerID`
               FROM ktv_kader_trainings kkt,ktv_kader_trainings_participants kktp,ktv_farmer_view kcf
               WHERE kkt.CPGtrainingsID=2 and kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID AND kktp.FarmerID=kcf.FarmerID AND kcf.StatusCode = 'active'
            ) aa WHERE VillageID is not null %s";
        $this->main_usia = "
            SELECT ROUND(AVG(total), 1) AS total
            FROM (
                SELECT ROUND(AVG(YEAR(NOW())-YEAR(Birthdate)),1) total
                from ktv_farmer_view aa
                JOIN `ktv_cpg_batch_trainings_farmers` kcbtf on kcbtf.`FarmerID`=aa.`FarmerID`
              JOIN ktv_cpg_batch_trainings kcbt on kcbt.`CpgBatchTrainingID`=kcbtf.`CpgBatchTrainingID` AND kcbt.TrainingStart > 0
                WHERE VillageID AND aa.StatusCode='active' and kcbt.`CPGtrainingsID`=1 and Birthdate is not null and Birthdate!='0000-00-00' %s
                %s
            ) r";
        $this->main_ukuran = "SELECT ROUND(AVG(total), 3) AS total
            FROM (
                SELECT ROUND(SUM(GardenHaUnCertified)/count(FarmerID),3) total
                FROM (
                   SELECT kcf.VillageID,kcf.SubDistrictID,kcf.CPGid,kcf.FarmerID,sum(GardenHaUnCertified) GardenHaUnCertified
                   FROM ktv_farmer_garden kcfg,ktv_farmer_view kcf,
                      (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z
                   WHERE kcf.StatusCode='active' and kcf.FarmerID=kcfg.FarmerID and GardenHaUnCertified>0 and
                      kcfg.FarmerID = z.FarmerID and kcfg.GardenNr = z.GardenNr AND kcfg.SurveyNr = z.LatestSurveyNr
                   GROUP BY kcf.FarmerID) aa
                WHERE VillageID is not null %s
                %s
            ) r";
        $this->main_perempuan = "SELECT ROUND(sum(if(Gender='2',1,0))/(sum(if(Gender='1',1,0))+sum(if(Gender='2',1,0))),3) total
            from (
              SELECT *
              FROM (
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Gender,
                  VillageID,
                  kcf.CPGid
               FROM
                  `ktv_cpg_batch_trainings_farmers` kcfg
               JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               UNION ALL
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Gender,
                  VillageID,
                  kcf.CPGid
               FROM
                  `ktv_kader_trainings_participants` kcfg
               LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               )m GROUP BY FarmerID, CPGtrainingsID
              ) aa
            WHERE aa.`CPGtrainingsID`=1 %s";
        $this->main_ketiga = "SELECT count(CoopID) total from ktv_cooperatives aa where VillageID is not null %s
            UNION
            -- 1
            SELECT count(*) total from (
               SELECT kcfg.GardenNr from ktv_farmer_garden kcfg
               INNER JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z ON
               kcfg.FarmerID = z.FarmerID AND kcfg.GardenNr = z.GardenNr AND kcfg.SurveyNr = z.LatestSurveyNr
               LEFT JOIN ktv_farmer_view aa on aa.FarmerID=kcfg.FarmerID
               where VillageID is not null AND aa.StatusCode='active' AND aa.VillageID AND GardenHaUnCertified>0 %s %s
               group by aa.FarmerID,GardenNr
            ) a
            UNION
            -- 2
SELECT
    AVG(produktifitas_pohon) AS total
FROM (
            SELECT
               produksi/luas AS produktifitas,
               produksi/pohon AS produktifitas_pohon
            FROM (
            SELECT Province label,
               SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS produksi,
               SUM(IFNULL(GardenHaUnCertified,0)) AS luas,
               SUM(PohonTM) AS pohon
            FROM ktv_farmer_garden a
            LEFT JOIN ktv_farmer_view aa ON aa.FarmerID=a.FarmerID
            INNER JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z ON
               a.GardenNr = z.GardenNr AND a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
             LEFT JOIN ktv_province kd ON kd.ProvinceID=SUBSTR(aa.VillageID,1,2)
            WHERE aa.StatusCode='active' AND GardenHaUnCertified>0  AND a.SurveyNr = z.LatestSurveyNr
            AND aa.VillageID %s %s
            %s
            ) r
) r
            UNION
            -- 3
            SELECT count(distinct aa.FarmerID)+0.0000001 total
            from ktv_certification kcfg
            LEFT JOIN ktv_farmer_view aa on aa.FarmerID=kcfg.FarmerID
            where aa.StatusCode = 'active' AND VillageID is not null and ExternalDate > '0000-00-00' AND CURRENT_DATE() BETWEEN CertificationStart AND CertificationEnd %s %s
            UNION
            -- 4
            SELECT sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) total
            FROM ktv_farmer_garden a
            INNER JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               WHERE ExternalDate>'0000-00-00' AND CURRENT_DATE() BETWEEN CertificationStart AND CertificationEnd GROUP BY FarmerID,GardenNr) z on
               a.FarmerID=z.FarmerID and a.GardenNr=z.GardenNr and a.SurveyNr=z.LatestSurveyNr
            LEFT JOIN ktv_farmer_view aa on aa.FarmerID=a.FarmerID
            where aa.StatusCode = 'active' AND VillageID is not null %s %s
            UNION
            -- 5
SELECT
    AVG(produktifitas) AS total
FROM (
            SELECT
               produksi/luas AS produktifitas,
               produksi/pohon AS produktifitas_pohon
            FROM (
            SELECT Province label,
               SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS produksi,
               SUM(IFNULL(GardenHaUnCertified,0)) AS luas,
               SUM(PohonTM) AS pohon
            FROM ktv_farmer_garden a
            LEFT JOIN ktv_farmer_view aa ON aa.FarmerID=a.FarmerID
            INNER JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z ON
               a.GardenNr = z.GardenNr AND a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
             LEFT JOIN ktv_province kd ON kd.ProvinceID=SUBSTR(aa.VillageID,1,2)
            WHERE aa.StatusCode='active' AND GardenHaUnCertified>0  AND a.SurveyNr = z.LatestSurveyNr
            AND aa.VillageID %s %s
            %s
            ) r
) r";
        $this->main_classification = "SELECT
  SUM(IF(Hectare < 1, 1, 0)) AS small
  ,SUM(IF(Hectare >= 1 AND Hectare < 2, 1, 0)) AS `medium`
  ,SUM(IF(Hectare >= 2, 1, 0)) AS `large`
  ,SUM(IF(KgHaYear < 500, 1, 0)) AS unprofessional
  ,SUM(IF(KgHaYear >= 500 AND KgHaYear < 1000, 1, 0)) AS `progressing`
  ,SUM(IF(KgHaYear >= 1000, 1, 0)) AS `professional`
FROM (
SELECT
   (IFNULL(GardenHaUnCertified,0)) AS Hectare,
   ((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
   (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
   (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/(IFNULL(GardenHaUnCertified,0))  AS KgHaYear
FROM ktv_farmer_garden a
JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden WHERE FarmerID GROUP BY FarmerID,GardenNr) z ON a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr AND a.`GardenNr` = z.GardenNr
JOIN ktv_farmer_view aa ON aa.StatusCode='active' AND a.FarmerID=aa.FarmerID
WHERE 1 = 1
%s
) r";
        $this->main_ha = "
            SELECT sum(IFNULL(GardenHaUnCertified,0)) total
            from ktv_farmer_garden kcc
            LEFT JOIN ktv_farmer_view aa on kcc.FarmerID=aa.FarmerID
            inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               where ExternalDate>'0000-00-00' AND CURRENT_DATE() BETWEEN CertificationStart AND CertificationEnd GROUP BY FarmerID,GardenNr) z
               on z.FarmerID=kcc.FarmerID and z.GardenNr=kcc.GardenNr and z.LatestSurveyNr=kcc.SurveyNr

            where aa.StatusCode='active' and GardenHaUnCertified>0
            %s
            ";
        $this->main_gfp = "
SELECT
   COUNT(DISTINCT aa.FarmerID) gfp
   ,COUNT(DISTINCT IF(Gender = 1,aa.FarmerID,NULL)) male
   ,COUNT(DISTINCT IF(Gender = 2,aa.FarmerID,NULL)) female
FROM (
   SELECT
      kcfg.FarmerID,
      IF(kcfg.PetaniKakao = 1,kcf.`Gender`,kf.`AnggotaGender`) AS Gender,
      VillageID,
      kcf.CPGid
   FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   LEFT JOIN ktv_family kf ON kf.`FamilyID` = kcfg.`FamilyID`
   LEFT JOIN `ktv_farmer_financial` fc ON fc.`FarmerID` = kcfg.`FarmerID` AND fc.`SurveyNr` = 0
   WHERE VillageID IS NOT NULL AND kcf.StatusCode = 'active'
   AND CPGtrainingsID=8
   UNION ALL
   SELECT
      kcfg.FarmerID,
      IF(kcfg.PetaniKakao = 1,kcf.`Gender`,kf.`AnggotaGender`) AS Gender,
      VillageID,
      kcf.CPGid
   FROM
      `ktv_kader_trainings_participants` kcfg
   JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   LEFT JOIN ktv_family kf ON kf.`FamilyID` = kcfg.`FamilyID`
   LEFT JOIN `ktv_farmer_financial` fc ON fc.`FarmerID` = kcfg.`FarmerID` AND fc.`SurveyNr` = 0
   WHERE VillageID IS NOT NULL AND kcf.StatusCode = 'active'
   AND CPGtrainingsID=8
   ) aa
WHERE VillageID IS NOT NULL %s
            ";

        //demographic
        $this->demographic_age = "SELECT
            %s label,
            ROUND(AVG(YEAR(NOW()) - YEAR(Birthdate)), 1) age,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 15 AND 34,kcf.FarmerID,NULL)) AS `young`,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 15 AND 24,kcf.FarmerID,NULL)) AS `15-24`,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 25 AND 34,kcf.FarmerID,NULL)) AS `25-34`,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 35 AND 44,kcf.FarmerID,NULL)) AS `35-44`,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 45 AND 54,kcf.FarmerID,NULL)) AS `45-54`,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) >= 55,kcf.FarmerID,NULL)) AS `55+`,
            COUNT(DISTINCT kcf.FarmerID) AS farmer
          from (
              SELECT *
              FROM (
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Birthdate,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_cpg_batch_trainings_farmers` kcfg
               JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               UNION ALL
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Birthdate,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_kader_trainings_participants` kcfg
               LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               )m GROUP BY FarmerID, CPGtrainingsID
              ) kcf
            %s
          WHERE 1 = 1
            and Birthdate is not null
            and Birthdate != '0000-00-00'
            AND kcf.`CPGtrainingsID`=1
            %s
          GROUP BY %s
            ORDER BY label";
        $this->demographic_gender = "SELECT
            %s label,
            SUM(IF(`Gender` = '1', 1, 0)) male,
            SUM(IF(`Gender` = '2', 1, 0)) female
          FROM (
              SELECT *
              FROM (
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Gender,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_cpg_batch_trainings_farmers` kcfg
               JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               UNION ALL
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Gender,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_kader_trainings_participants` kcfg
               LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               )m GROUP BY FarmerID, CPGtrainingsID
              ) kcf
            %s
          WHERE
            kcf.`CPGtrainingsID`=1
            %s
            GROUP BY %s
            ORDER BY label";
        $this->demographic_farmer = "SELECT
              %s label,
              COUNT(DISTINCT kcf.`FarmerID`) AS total,
              SUM(YEAR(NOW()) - YEAR(`Birthdate`)) umur
            FROM (
              SELECT *
              FROM (
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Birthdate,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_cpg_batch_trainings_farmers` kcfg
               JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               UNION ALL
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Birthdate,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_kader_trainings_participants` kcfg
               LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               )m GROUP BY FarmerID, CPGtrainingsID
              ) kcf
              %s
            WHERE
              kcf.`CPGtrainingsID`=1
            %s
            GROUP BY %s";
        $this->demographic_edu = "SELECT
            sum(if(Education = '1', 1, 0)) 'Belum pernah sekolah',
            sum(if(Education = '2', 1, 0)) 'Tidak tamat SD',
            sum(if(Education = '3', 1, 0)) 'Tamat SD, tidak melanjutkan',
            sum(if(Education = '4', 1, 0)) 'Tamat SMP',
            sum(if(Education = '5', 1, 0)) 'Tamat SMA/SMK',
            sum(if(Education = '6', 1, 0)) 'Tamat perguruan tinggi'
          from (
              SELECT *
              FROM (
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Education,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_cpg_batch_trainings_farmers` kcfg
               JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               UNION ALL
               SELECT
                  kcfg.FarmerID,
                  CPGtrainingsID,
                  Education,
                  VillageID,
                  SubDistrict,
                  SubDistrictID,
                  kcf.CPGid
               FROM
                  `ktv_kader_trainings_participants` kcfg
               LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
               LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
               WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
               )m GROUP BY FarmerID, CPGtrainingsID
              ) kcf
            %s
          where 1 = 1
            AND kcf.`CPGtrainingsID`=1
            %s
        ";
        $this->demographic_kedua = "SELECT
            COUNT(DISTINCT SUBSTR(`VillageID`, 1, 2)) province,
            COUNT(DISTINCT SUBSTR(`VillageID`, 1, 4)) district,
            COUNT(DISTINCT SUBSTR(`VillageID`, 1, 7)) subdistrict,
            COUNT(DISTINCT `VillageID`) village
          FROM
            `ktv_farmer_view` kcf
            %s
          WHERE `VillageID` IS NOT NULL AND kcf.StatusCode = 'active' %s
";
        $this->demographic_poverty = "SELECT
  %s AS label,
  COUNT(`National`) AS `count`,
  SUM(`National`) AS `National`,
  SUM(`1.25/day`) AS '1.25',
  SUM(`2.5/day`) AS '2.5'
FROM
  ktv_farmer_view kcf
  INNER JOIN
    (SELECT
      `FarmerID`,
      MAX(`SurveyNr`) `LatestSurveyNr`
    FROM
      ktv_ppiscore2012
    GROUP BY `FarmerID`) z ON kcf.`FarmerID` = z.`FarmerID`
  INNER JOIN ktv_ppiscore2012 ppi ON z.`FarmerID` = ppi.`FarmerID` AND z.`LatestSurveyNr` = ppi.`SurveyNr`
  %s
WHERE `VillageID` IS NOT NULL
    AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
  %s
GROUP BY label
         ";
        $this->demographic_poverty_postline = "SELECT
   %s AS label
   ,COUNT(`National`) AS `count`
   ,SUM(`National`) AS `National`
   ,SUM(`1.25/day`) AS '1.25'
   ,SUM(`2.5/day`) AS '2.5'
FROM
   ktv_farmer_view kcf
INNER JOIN
(
    SELECT
    FarmerID, MAX(SurveyNr) LatestSurveyNr
    FROM
    ktv_ppiscore2012
    WHERE
      SurveyNr > 0
    GROUP BY FarmerID
    ) z ON kcf.FarmerID = z.FarmerID
INNER JOIN ktv_ppiscore2012 ppi ON z.FarmerID = ppi.FarmerID AND z.LatestSurveyNr = ppi.SurveyNr
%s
WHERE VillageID is not null
    AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
%s
group by label
         ";
        $this->demographic_poverty_baseline = "SELECT
   %s AS label
   ,COUNT(`National`) AS `count`
   ,SUM(`National`) AS `National`
   ,SUM(`1.25/day`) AS '1.25'
   ,SUM(`2.5/day`) AS '2.5'
FROM
   ktv_farmer_view kcf
INNER JOIN ktv_ppiscore2012 ppi ON kcf.FarmerID = ppi.FarmerID AND ppi.SurveyNr = 0
%s
WHERE VillageID is not null AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
%s
group by label
         ";
        $this->demographic_household = "SELECT
   %s AS label
   ,COUNT(farmer_count) AS 'count'
   ,SUM(household) AS 'sum'
   ,SUM(IF(household = 1, 1, 0)) AS '1'
   ,SUM(IF(household = 2, 1, 0)) AS '2'
   ,SUM(IF(household = 3, 1, 0)) AS '3'
   ,SUM(IF(household = 4, 1, 0)) AS '4'
   ,SUM(IF(household = 5, 1, 0)) AS '5'
   ,SUM(IF(household >= 6, 1, 0)) AS '>=6'
FROM
(
  SELECT
    kcf.FarmerID,
    kcf.VillageID,
    SubDistrict,
    SubDistrictID,
    kcf.CPGid,
    (COUNT(FamilyID) + COUNT(DISTINCT kcf.FarmerID)) AS household,
    COUNT(DISTINCT kcf.FarmerID) AS farmer_count
  FROM
    ktv_farmer_view kcf
  INNER JOIN ktv_family ON kcf.FarmerID = ktv_family.FarmerID
  WHERE kcf.StatusCode = 'active'
  GROUP BY kcf.FarmerID
) kcf
  JOIN `ktv_cpg_batch_trainings_farmers` kcbtf ON kcbtf.`FarmerID`=kcf.`FarmerID`
  JOIN ktv_cpg_batch_trainings kcbt ON kcbt.`CpgBatchTrainingID`=kcbtf.`CpgBatchTrainingID` AND kcbt.TrainingStart > 0
%s
WHERE `VillageID` IS NOT NULL and
  kcbt.`CPGtrainingsID`=1
%s
GROUP BY label
         ";
        $this->demographic_hh_size = "SELECT
  label
   ,COUNT(DISTINCT FarmerID) AS farmer
   ,SUM(FamilyHH) AS household
   ,SUM(IF(FamilyHH = 1, 1, 0)) AS '1'
   ,SUM(IF(FamilyHH = 2, 1, 0)) AS '2'
   ,SUM(IF(FamilyHH = 3, 1, 0)) AS '3'
   ,SUM(IF(FamilyHH = 4, 1, 0)) AS '4'
   ,SUM(IF(FamilyHH = 5, 1, 0)) AS '5'
   ,SUM(IF(FamilyHH >= 6, 1, 0)) AS '>=6'
FROM (
  SELECT
     %s AS label,
     kcf.FarmerID,
    COUNT(DISTINCT kcf.FarmerID) AS FarmerCount,
    (COUNT(ktv_family.FamilyID) + COUNT(DISTINCT kcf.FarmerID)) AS FamilyHH
    -- (COUNT(ktv_family.FamilyID) + COUNT(DISTINCT kcf.FarmerID))/COUNT(DISTINCT kcf.FarmerID) AS AverageHH
  FROM
      ktv_farmer_view kcf
  INNER JOIN ktv_family ON kcf.FarmerID = ktv_family.FarmerID
    -- JOIN `ktv_cpg_batch_trainings_farmers` kcbtf ON kcbtf.`FarmerID`=kcf.`FarmerID`
    -- JOIN ktv_cpg_batch_trainings kcbt ON kcbt.`CpgBatchTrainingID`=kcbtf.`CpgBatchTrainingID` AND kcbt.TrainingStart > 0 AND kcbt.`CPGtrainingsID`=1
  %s
  WHERE 1 = 1
  AND kcf.`StatusCode` = 'active'
  %s
  GROUP BY kcf.FarmerID
) r
GROUP BY label
         ";
        $this->demographic_cook_fuel = "
SELECT
    %s AS label,
    SUM(CASE `CookingFuel` WHEN 1 THEN 1 ELSE 0 END) AS Firewood
    ,SUM(CASE `CookingFuel` WHEN 2 THEN 1 ELSE 0 END) AS Gas_Others
    ,SUM(CASE`Refrigerator` WHEN 2 THEN 1 ELSE 0 END) AS RefrigeratorYes
    ,SUM(CASE`Refrigerator` WHEN 1 THEN 1 ELSE 0 END) AS RefrigeratorNo
    ,SUM(CASE `Motorcycle` WHEN 2 THEN 1 ELSE 0 END) AS MotorcycleYes
    ,SUM(CASE `Motorcycle` WHEN 1 THEN 1 ELSE 0 END) AS MotorcycleNo
FROM
    `ktv_ppiscore2012`
INNER JOIN ktv_farmer_view kcf on kcf.FarmerID = ktv_ppiscore2012.FarmerID
  JOIN `ktv_cpg_batch_trainings_farmers` kcbtf ON kcbtf.`FarmerID`=kcf.`FarmerID`
  JOIN ktv_cpg_batch_trainings kcbt ON kcbt.`CpgBatchTrainingID`=kcbtf.`CpgBatchTrainingID` AND kcbt.TrainingStart > 0
%s
WHERE
    `GasCylinder`
    AND `Refrigerator`
    AND `Motorcycle` IS NOT NULL
    AND VillageID is not null
    AND kcf.StatusCode = 'active' AND kcbt.`CPGtrainingsID`=1
    %s
Group by label

         ";

        //garden
        $this->garden_luas = "SELECT %s label,sum(IFNULL(GardenHaUnCertified,0)) as total, count(distinct a.FarmerID) as jumlah,
               count(a.FarmerID) as kebun, SUM(a.PohonTM) AS pohon
            FROM ktv_farmer_garden a
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
            LEFT JOIN ktv_farmer_view kcf on a.FarmerID = kcf.FarmerID
            %s
            WHERE kcf.StatusCode='active' AND kcf.VillageID and GardenHaUnCertified>0 and kcf.VillageID is not null %s
            GROUP BY %s";
        $this->garden_produksi = "
            SELECT %s label,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as total,
               sum(PohonTM) tm
            from ktv_farmer_garden a
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
            LEFT JOIN ktv_farmer_view kcf on a.FarmerID = kcf.FarmerID
            %s
            WHERE kcf.StatusCode='active' AND kcf.VillageID and GardenHaUnCertified>0 %s
            group by %s";
        $this->garden_size = "SELECT
         label,
         SUM(IF(GardenHaUnCertified<0.3,1,0)) AS 'Marginal < 0.3 ha',
         SUM(IF(GardenHaUnCertified>=0.3 AND GardenHaUnCertified<0.6,1,0)) AS 'Micro 0.3 - 0.6 Ha',
         SUM(IF(GardenHaUnCertified>=0.6 AND GardenHaUnCertified<1,1,0)) AS 'Small 0.6 - 1 Ha',
         SUM(IF(GardenHaUnCertified>=1 AND GardenHaUnCertified<2,1,0)) AS 'Medium 1 - 2 ha',
         SUM(IF(GardenHaUnCertified>=2,1,0)) AS 'Large > 2 Ha'
      FROM (
         SELECT
            %s label
            ,IFNULL(GardenHaUnCertified,0) AS GardenHaUnCertified
         FROM
            ktv_farmer_garden a
            JOIN ktv_farmer_view kcf ON kcf.FarmerID = a.FarmerID
            LEFT JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z
               ON a.GardenNr = z.GardenNr AND a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
            %s
         WHERE kcf.StatusCode='active' AND kcf.VillageID
         %s
      ) a
      GROUP BY label
";
        $this->garden_avg = "SELECT %s label, ROUND(SUM(kcf.GardenHaUnCertified)/count(kcf.FarmerID),1) avg
            FROM (
               SELECT kcf.VillageID,kcf.SubDistrictID,kcf.SubDistrict,kcf.FarmerID,kcf.CPGid,a.GardenNr,a.SurveyNr,sum(GardenHaUnCertified) GardenHaUnCertified
               FROM ktv_farmer_garden a
               LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=a.FarmerID
               LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
                  a.GardenNr = z.GardenNr and a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
               WHERE kcf.StatusCode='active' and GardenHaUnCertified>0
               GROUP BY kcf.FarmerID) kcf
            LEFT JOIN ktv_farmer_garden a on kcf.FarmerID=a.FarmerID and a.GardenNr = kcf.GardenNr and a.SurveyNr = kcf.SurveyNr
            %s
            where kcf.VillageID %s
            GROUP BY %s
            ORDER BY label";
        $this->garden_komposisi = "
            SELECT sum(IFNULL(PohonTBM,0)) as TBM, sum(IFNULL(PohonTM,0)) as TM, sum(IFNULL(PohonRehab,0)) as TR,
               sum(IFNULL(ShadeTreesNr,0)) as 'Tanaman Lain'
            FROM ktv_farmer_garden a
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=a.FarmerID
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.GardenNr = z.GardenNr and a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
            %s
            WHERE kcf.StatusCode='active' AND kcf.VillageID %s";
        $this->garden_produktivity = "
            SELECT
               label,
               produksi,
               luas,
               pohon,
               produksi/luas AS produktifitas
            FROM (
            SELECT %s label,
               sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS produksi,
               SUM(IFNULL(GardenHaUnCertified,0)) as luas,
               SUM(PohonTM) as pohon
            FROM ktv_farmer_garden a
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=a.FarmerID
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.GardenNr = z.GardenNr and a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
            %s
            WHERE kcf.StatusCode='active' AND GardenHaUnCertified>0 %s
            AND kcf.VillageID
            group by %s
            ) r";
        $this->garden_lain = "
            SELECT sum(KelapaNr+PinangNr+KaretNr+CengkehNr+SawitNr+ArenNr+PalaNr+KemiriNr) 'Tanaman Produksi Selain Kakao',
               sum(MahoniNr+JatiNr+BitiNr+UruNr+JabonNr) 'Kayu Keras',
               sum(JackFruitNr+PisangNr+RambutanNr+ManggaNr+LangsatNr+DurianNr+AlpukatNr+SukunNr+PepayaNr+ManggaNr+JerukNr) 'Buah-buahan',
               sum(GamalNr+LamtoroNr+PetaiNr+JengkolNr) 'Leguminosa',
               sum(ShadeLainNr) 'Lainnya'
            FROM ktv_farmer_garden a
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.FarmerID=z.FarmerID and a.GardenNr=z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=a.FarmerID
            %s
            WHERE kcf.VillageID is not null AND kcf.StatusCode = 'active' %s";
        $this->environment_garden_lain = "
            SELECT sum(KelapaNr+PinangNr+KaretNr+CengkehNr+SawitNr+ArenNr+PalaNr+KemiriNr) 'Tanaman Produksi Selain Kakao',
               sum(MahoniNr+JatiNr+BitiNr+UruNr+JabonNr) 'Kayu Keras',
               sum(JackFruitNr+PisangNr+RambutanNr+ManggaNr+LangsatNr+DurianNr+AlpukatNr+SukunNr+PepayaNr+ManggaNr+JerukNr) 'Buah-buahan',
               sum(GamalNr+LamtoroNr+PetaiNr+JengkolNr) 'Leguminosa',
               sum(ShadeLainNr) 'Lainnya'
            FROM ktv_farmer_garden a
            INNER JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.FarmerID=z.FarmerID and a.GardenNr=z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=a.FarmerID
            %s
            WHERE kcf.VillageID is not null AND kcf.StatusCode = 'active' %s";
        $this->garden_tm = "
            SELECT %s label,ROUND(sum(PohonTM)/SUM(GardenHaUnCertified),1) tm,sum(PohonTM) po,
               sum(IFNULL(PohonTBM,0))+sum(IFNULL(PohonTM,0))+sum(IFNULL(PohonRehab,0)) po_all,SUM(GardenHaUnCertified) ha,
               SUM(PohonRehab) broken
            FROM ktv_farmer_garden a
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=a.FarmerID
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.GardenNr = z.GardenNr and a.FarmerID = z.FarmerID AND a.SurveyNr = z.LatestSurveyNr
            %s
            WHERE kcf.VillageID and kcf.StatusCode='active' %s
            GROUP BY %s
            order by label";
        $this->garden_yield = "
SELECT
    %s AS label,
COUNT(CASE
        WHEN
            (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))<500
THEN 1 END) AS 'below_500',
COUNT(CASE
        WHEN
            (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))>=500 AND (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))<1000
THEN 1 END) AS 'between_500_1000',
COUNT(CASE
        WHEN
            (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))>=1000 AND (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))<2000
THEN 1 END) AS 'between_1000_2000',
COUNT(CASE
        WHEN
            (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))>=2000
THEN 1 END) AS 'above_2000'
FROM
    ktv_farmer_garden a
         LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
            a.FarmerID=z.FarmerID and a.GardenNr=z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
        INNER JOIN
    ktv_farmer_view kcf ON kcf.FarmerID = a.FarmerID
    %s
    WHERE (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
    ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
    ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
    ))/(GardenHaUncertified))>0 AND kcf.VillageID AND kcf.StatusCode = 'active'
   %s
GROUP BY %s
order by label
         ";
        $this->garden_land = "
SELECT
    %s AS label
   ,SUM(IF(OwnershipCocoa=1,1,0)) AS 'owner'
   ,SUM(IF(OwnershipCocoa=2,1,0)) AS 'crop_share'
   ,SUM(IF(OwnershipCocoa=3,1,0)) AS 'rent'
   ,SUM(IF(OwnershipCocoa=4,1,0)) AS 'other'
   ,SUM(IF(LandCertificate=1,1,0)) AS 'no_land_certificate'
   ,SUM(IF(LandCertificate=2,1,0)) AS 'notarial_deed_bpn'
   ,SUM(IF(LandCertificate=3,1,0)) AS 'skkt_camat'
   ,SUM(IF(LandCertificate=4,1,0)) AS 'village_lurah'
   ,SUM(IF(LandOwner=1,1,0)) AS 'farmer_him_herself'
   ,SUM(IF(LandOwner=2,1,0)) AS 'family_member'
   ,SUM(IF(LandOwner=3,1,0)) AS 'other_person'
   ,SUM(IF(LandOwner=4,1,0)) AS 'do_not_know'
   ,AVG(IF(a.`TahunTanamanCocoa` IS NULL OR a.`TahunTanamanCocoa` = 0,NULL,YEAR(CURRENT_DATE)-a.`TahunTanamanCocoa`)) AS tree_age_avg
   ,SUM(IF(a.`TahunTanamanCocoa` IS NULL OR a.`TahunTanamanCocoa` = 0,NULL,YEAR(CURRENT_DATE)-a.`TahunTanamanCocoa`)) AS tree_age_sum
   ,SUM(IF(a.`TahunTanamanCocoa` IS NULL OR a.`TahunTanamanCocoa` = 0,0,1)) AS tree_age_count

   -- CASE LandOwner WHEN 1 THEN 'Farmer him/herself' WHEN 2 THEN 'Family Member' WHEN 3 THEN 'Other Person' WHEN 4 THEN 'Do not know' END AS 'Owner'
   -- CASE OwnershipCocoa WHEN 1 THEN 'Owner' WHEN 2 THEN 'Crop Share' WHEN 3 THEN 'Rent' WHEN 4 THEN 'Others' END AS Ownership,
   -- CASE LandCertificate WHEN 1 THEN 'No Land Certificate' WHEN 2 THEN 'Notarial Deed/BPN' WHEN 3 THEN 'SKKT (Camat)' WHEN 4 THEN 'Village/lurah' END AS LandCertificate
FROM
    ktv_farmer_garden a
    INNER JOIN
    ktv_farmer_view kcf ON kcf.FarmerID = a.FarmerID
    LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
      a.FarmerID=z.FarmerID and a.GardenNr=z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
   %s
    WHERE OwnershipCocoa IS NOT NULL AND LandCertificate IS NOT NULL AND kcf.VillageID AND kcf.StatusCode = 'active'
    %s
GROUP BY %s
order by label
         ";

        //certification
        $this->cert_farmer = "
            SELECT %s label,sum(IF(Certification=1,1,0)) UTZ,sum(IF(Certification=2,1,0)) Rainforest,
               sum(IF(Certification=3,1,0)) Fair,sum(IF(Certification=3,1,0)) Organic,count(kcc.FarmerID) petani
            from (SELECT * from ktv_certification where ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) group by FarmerID) kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            %s
            where VillageID AND kcf.StatusCode = 'active' AND %s is not null %s
            group by %s";
        $this->cert_gender = "
            SELECT %s label,sum(IF(Gender=1,1,0)) male,sum(IF(Gender=2,1,0)) female
            from (SELECT * from ktv_certification WHERE !('%s' > CertificationEnd OR '%s' < CertificationStart) group by FarmerID) kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            %s
            where VillageID AND kcf.StatusCode = 'active' AND %s is not null and ExternalDate>'0000-00-00' %s
            group by %s";
        $this->cert_ha = "
            SELECT %s label,sum(IFNULL(GardenHaUnCertified,0)) total
            from ktv_farmer_garden kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               where ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) GROUP BY FarmerID,GardenNr) z
               on z.FarmerID=kcc.FarmerID and z.GardenNr=kcc.GardenNr and z.LatestSurveyNr=kcc.SurveyNr
            %s
            where VillageID AND kcf.StatusCode='active' and GardenHaUnCertified>0 and %s is not null %s
            group by %s";
        $this->cert_volume = "
            SELECT %s label,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as total,
               SUM(IFNULL(GardenHaUnCertified,0)) luas
            from ktv_farmer_garden kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               where ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) GROUP BY FarmerID,GardenNr) z
               on z.FarmerID=kcc.FarmerID and z.GardenNr=kcc.GardenNr and z.LatestSurveyNr=kcc.SurveyNr
            %s
            where VillageID AND kcf.StatusCode = 'active' AND %s is not null %s
            group by %s";
        $this->cert_kebun = "
            SELECT %s label,sum(IF(Certification=1,1,0)) UTZ,sum(IF(Certification=2,1,0)) Rainforest,
               sum(IF(Certification=3,1,0)) Fair,sum(IF(Certification=3,1,0)) Organic
            from ktv_certification kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            %s
            where VillageID AND kcf.StatusCode = 'active' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) AND %s is not null and ExternalDate>'0000-00-00' %s
            group by %s";
        $this->cert_produktivitas = "
            SELECT %s label,
               round(sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
               SUM(IFNULL(GardenHaUnCertified,0)),0)  as produktivitas
            from ktv_farmer_garden kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               where ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) GROUP BY FarmerID,GardenNr) z
               on z.FarmerID=kcc.FarmerID and z.GardenNr=kcc.GardenNr and z.LatestSurveyNr=kcc.SurveyNr
            %s
            WHERE VillageID AND kcf.StatusCode='active' and GardenHaUnCertified>0 and %s is not null %s
            group by %s";
        $this->cert_holder = "
            SELECT %s label,sum(IF(CertificationHolderJenis='Pedagang',1,0)) trader,
               sum(IF(CertificationHolderJenis='Organisasi Petani',1,0)) koperasi,sum(IF(CertificationHolderJenis='Gudang',1,0)) warehouse
            from (SELECT * from ktv_certification WHERE !('%s' > CertificationEnd OR '%s' < CertificationStart) group by FarmerID) kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            %s
            where VillageID AND kcf.StatusCode = 'active' AND %s is not null and ExternalDate>'0000-00-00' %s
            group by %s";
        $this->cert_rerata = "
            SELECT %s label,sum(IFNULL(GardenHaUnCertified,0)) as total,count(kcc.FarmerID) as kebun
            FROM ktv_farmer_garden kcc
            LEFT JOIN ktv_farmer_view kcf on kcc.FarmerID=kcf.FarmerID
            inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               where ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) GROUP BY FarmerID,GardenNr) z
               on z.FarmerID=kcc.FarmerID and z.GardenNr=kcc.GardenNr and z.LatestSurveyNr=kcc.SurveyNr
            %s
            where VillageID AND kcf.StatusCode = 'active' AND %s is not null %s
            GROUP BY  %s";
        $this->cert_size = "
            SELECT %s label
              ,sum(IFNULL(GardenHaUnCertified,0)) as total
              ,count(distinct a.FarmerID) as jumlah
              ,count(a.FarmerID) as kebun
              ,sum(PohonTM) AS tree
              ,SUM(GardenHaUnCertified) ha
            FROM ktv_farmer_garden a
            INNER JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               where ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) GROUP BY FarmerID,GardenNr) z on z.FarmerID=a.FarmerID and z.GardenNr=a.GardenNr and z.LatestSurveyNr=a.SurveyNr
            INNER JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) y on a.FarmerID = y.FarmerID and a.GardenNr = y.GardenNr
            LEFT JOIN ktv_farmer_view kcf on a.FarmerID = kcf.FarmerID
            %s
            WHERE kcf.StatusCode='active' AND kcf.VillageID and GardenHaUnCertified>0 and kcf.VillageID is not null %s
            GROUP BY label";

        //survey
        $this->survey_jumlah = "SELECT %s label,
               count(distinct IF(SurveyNr=0,kcfg.FarmerID,null)) farmer_baseline,
               count(distinct IF(SurveyNr>0,kcfg.FarmerID,null)) farmer_postline,
               SUM(IF(SurveyNr=0,1,0)) baseline,
               SUM(IF(SurveyNr>0 AND SurveyNr = LatestSurveyNr,1,0)) postline,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())-6),1,0)) baseline1,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())-6),1,0)) postline1,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())-5),1,0)) baseline2,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())-5),1,0)) postline2,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())-4),1,0)) baseline3,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())-4),1,0)) postline3,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())-3),1,0)) baseline4,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())-3),1,0)) postline4,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())-2),1,0)) baseline5,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())-2),1,0)) postline5,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())-1),1,0)) baseline6,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())-1),1,0)) postline6,
               SUM(IF((SurveyNr=0 and YEAR(kcfg.DateCollection)=year(now())),1,0)) baseline7,
               SUM(IF((SurveyNr>0 AND SurveyNr = LatestSurveyNr and YEAR(kcfg.DateCollection)=year(now())),1,0)) postline7
            FROM ktv_farmer_garden kcfg
            LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               kcfg.FarmerID = z.FarmerID and kcfg.GardenNr = z.GardenNr and kcfg.SurveyNr = z.LatestSurveyNr
            JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
            %s
            WHERE VillageID is not null AND GardenHaUnCertified > 0  %s
            GROUP BY %s
            ORDER BY label";
        $this->survey_avg = "SELECT %s label,count(distinct kcf.FarmerID) total,
               sum((IFNULL(kcfg.PanenTrekMonths,0)*IFNULL(kcfg.PanenTrekPanenMonth,0)*IFNULL(kcfg.PanenTrekKg,0))+
                           (IFNULL(kcfg.PanenBiasaMonths,0)*IFNULL(kcfg.PanenBiasaPanenMonth,0)*IFNULL(kcfg.PanenBiasaKg,0))+
                           (IFNULL(kcfg.PanenRayaMonths,0)*IFNULL(kcfg.PanenRayaPanenMonth,0)*IFNULL(kcfg.PanenRayaKg,0))) panen_baseline,
               SUM(IFNULL(kcfg.PohonTBM,0))+SUM(IFNULL(kcfg.PohonTM,0))+SUM(IFNULL(kcfg.PohonRehab,0)) tree_baseline,
               sum(IFNULL(kcfg.GardenHaUnCertified,0)) as luas_baseline,
               sum((IFNULL(kcfgb.PanenTrekMonths,0)*IFNULL(kcfgb.PanenTrekPanenMonth,0)*IFNULL(kcfgb.PanenTrekKg,0))+
                           (IFNULL(kcfgb.PanenBiasaMonths,0)*IFNULL(kcfgb.PanenBiasaPanenMonth,0)*IFNULL(kcfgb.PanenBiasaKg,0))+
                           (IFNULL(kcfgb.PanenRayaMonths,0)*IFNULL(kcfgb.PanenRayaPanenMonth,0)*IFNULL(kcfgb.PanenRayaKg,0))) panen_postline,
               SUM(IFNULL(kcfgb.PohonTBM,0))+SUM(IFNULL(kcfgb.PohonTM,0))+SUM(IFNULL(kcfgb.PohonRehab,0)) tree_postline,
               sum(IFNULL(kcfgb.GardenHaUnCertified,0)) as luas_postline
            FROM ktv_farmer_garden kcfg
            LEFT JOIN (SELECT a.FarmerID,a.GardenNr,PanenTrekMonths,PanenTrekPanenMonth,PanenTrekKg,PanenBiasaMonths,
               PanenBiasaPanenMonth,PanenBiasaKg,PanenRayaMonths,PanenRayaPanenMonth,PanenRayaKg,GardenHaUnCertified
               ,PohonTBM,PohonTM,PohonRehab
               from ktv_farmer_garden a
               JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr and a.SurveyNr = z.LatestSurveyNr
               where SurveyNr>0 and GardenHaUnCertified > 0 AND PanenRayaMonths > 0 group by FarmerID,GardenNr) kcfgb
               ON kcfg.FarmerID=kcfgb.FarmerID and kcfg.GardenNr=kcfgb.GardenNr
            JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
            %s
            WHERE VillageID is not null AND kcfg.SurveyNr=0 and kcfg.GardenHaUnCertified > 0 AND kcfg.PanenRayaMonths > 0 %s
            GROUP BY %s
            ORDER BY label";
        $this->survey_nutrition = "SELECT
  %s AS label
  ,SUM(IF(b.`SurveyNr`=0,1,0)) AS base
  ,SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr,1,0)) AS post
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())-6),1,0)) baseline1
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())-6),1,0)) postline1
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())-5),1,0)) baseline2
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())-5),1,0)) postline2
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())-4),1,0)) baseline3
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())-4),1,0)) postline3
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())-3),1,0)) baseline4
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())-3),1,0)) postline4
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())-2),1,0)) baseline5
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())-2),1,0)) postline5
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())-1),1,0)) baseline6
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())-1),1,0)) postline6
  ,SUM(IF((b.SurveyNr=0 and YEAR(b.`InterviewDate`)=year(now())),1,0)) baseline7
  ,SUM(IF((b.SurveyNr>0 AND b.`SurveyNr` = r.SurveyNr and YEAR(b.`InterviewDate`)=year(now())),1,0)) postline7
  ,IFNULL(AVG(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10),b.`Score`,NULL)),0) AS score_avg_base
  ,IFNULL(AVG(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',b.`Score`,NULL)),0) AS score_avg_base_male
  ,IFNULL(AVG(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',b.`Score`,NULL)),0) AS score_avg_base_female
  ,IFNULL(AVG(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10),b.`Score`,NULL)),0) AS score_avg_post
  ,IFNULL(AVG(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',b.`Score`,NULL)),0) AS score_avg_post_male
  ,IFNULL(AVG(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',b.`Score`,NULL)),0) AS score_avg_post_female
  ,IFNULL(SUM(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10),b.`Score`,NULL)),0) AS score_sum_base
  ,IFNULL(SUM(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',b.`Score`,NULL)),0) AS score_sum_base_male
  ,IFNULL(SUM(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',b.`Score`,NULL)),0) AS score_sum_base_female
  ,IFNULL(SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10),b.`Score`,NULL)),0) AS score_sum_post
  ,IFNULL(SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',b.`Score`,NULL)),0) AS score_sum_post_male
  ,IFNULL(SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',b.`Score`,NULL)),0) AS score_sum_post_female
  ,IFNULL(SUM(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10),1,NULL)),0) AS score_count_base
  ,IFNULL(SUM(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',1,NULL)),0) AS score_count_base_male
  ,IFNULL(SUM(IF(b.`SurveyNr`=0 AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',1,NULL)),0) AS score_count_base_female
  ,IFNULL(SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10),1,NULL)),0) AS score_count_post
  ,IFNULL(SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',1,NULL)),0) AS score_count_post_male
  ,IFNULL(SUM(IF(b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',1,NULL)),0) AS score_count_post_female
  ,IFNULL(AVG(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10),b.score,NULL)),0) AS score_avg_max
  ,IFNULL(SUM(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10),b.score,NULL)),0) AS score_sum_max
  ,IFNULL(SUM(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10),1,NULL)),0) AS score_count_max
  ,IFNULL(AVG(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',b.score,NULL)),0) AS score_avg_max_male
  ,IFNULL(SUM(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',b.score,NULL)),0) AS score_sum_max_male
  ,IFNULL(SUM(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='1',1,NULL)),0) AS score_count_max_male
  ,IFNULL(AVG(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',b.score,NULL)),0) AS score_avg_max_female
  ,IFNULL(SUM(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',b.score,NULL)),0) AS score_sum_max_female
  ,IFNULL(SUM(IF(r.FarmerID AND (b.`Score` > 0 AND b.`Score` < 10) AND kcf.Gender='2',1,NULL)),0) AS score_count_max_female
  ,IFNULL(AVG(IF((b.`SurveyNr`=0 AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),b.`KebunPanjang`*b.`KebunLebar`,NULL)),0) AS area_avg_base
  ,IFNULL(AVG(IF((b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),b.`KebunPanjang`*b.`KebunLebar`,NULL)),0) AS area_avg_post
  ,IFNULL(AVG(IF((r.`SurveyNr` AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),b.`KebunPanjang`*b.`KebunLebar`,NULL)),0) AS area_avg_max
  ,IFNULL(SUM(IF((b.`SurveyNr`=0 AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),b.`KebunPanjang`*b.`KebunLebar`,0)),0) AS area_sum_base
  ,IFNULL(SUM(IF((b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),b.`KebunPanjang`*b.`KebunLebar`,0)),0) AS area_sum_post
  ,IFNULL(SUM(IF((b.`SurveyNr`=0 AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),1,0)),0) AS area_count_base
  ,IFNULL(SUM(IF((b.`SurveyNr`>0 AND b.`SurveyNr` = r.SurveyNr AND b.`KebunPanjang`<=10 AND b.`KebunLebar`<=10),1,0)),0) AS area_count_post
FROM ktv_nutrition b
LEFT JOIN (
  SELECT
    n.`FarmerID`
    ,MAX(n.`SurveyNr`) AS `SurveyNr`
  FROM ktv_nutrition n
  WHERE 1 = 1
  GROUP BY n.FarmerID
) r ON r.FarmerID = b.`FarmerID` AND b.`SurveyNr` = r.SurveyNr
LEFT JOIN ktv_farmer_view kcf  ON b.`FarmerID` = kcf.`FarmerID` AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
%s
WHERE
  kcf.`FarmerID`
  %s
GROUP BY label
          ";
        $this->survey_idds = "SELECT * FROM (
            SELECT %s label,round(sum(kn.Score)/count(knb.FarmerID),2) total,sum(kn.Score) sc,count(knb.FarmerID) fa, SUM(IF(kcf.Gender='1',kn.Score,0)) score_male, SUM(IF(kcf.Gender='2',kn.Score,0)) score_female,SUM(IF(kcf.Gender='1',1,0)) male, SUM(IF(kcf.Gender='2',1,0)) female
            from ktv_nutrition kn
            inner JOIN (SELECT FarmerID,max(SurveyNr) LastSurveyNr FROM ktv_nutrition GROUP BY FarmerID) knb on
               kn.FarmerID=knb.FarmerID and kn.SurveyNr=knb.LastSurveyNr
            JOIN ktv_farmer_view kcf ON knb.FarmerID = kcf.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
            %s
            WHERE 1 = 1
              -- substr(kcf.VillageID,1,4) not in (7317,7322,7325)
              and (kn.Score>0 AND kn.Score<10)
              %s
            GROUP BY %s
            ) a WHERE total>0 AND a.label IS NOT NULL
            order by label";

        //Traceability
        $this->sql_kelompok = "SELECT %s label,count(CPGid) as total
            from
(
SELECT
 cpg.*,
 SubDistrictID, SubDistrict
FROM ktv_cpg cpg
JOIN `ktv_cpg_batch_trainings` cbt ON cbt.`CPGid` = cpg.`CPGid` AND TrainingStart > 0
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = SUBSTR(VillageID,1,7)
GROUP BY cpg.`CPGid`
) kcf
            %s
            WHERE CPGid>0 %s
            GROUP BY %s
            ORDER BY label";
        $this->garden_produksi = "
            SELECT %s label,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as total,
               sum(PohonTM) tm
            from ktv_farmer_garden a
            inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr
            LEFT JOIN ktv_farmer_view kcf on a.FarmerID = kcf.FarmerID
            %s
            WHERE kcf.StatusCode='active' AND kcf.VillageID and GardenHaUnCertified>0 %s
            group by %s";
        $this->traceability_penjualan = "SELECT
  %s label,
        SUM(netto) total FROM (
   SELECT a.SupplyID,
    SUM(IF(aa.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto)) bruto,
    IF(SUM(((100-(aa.Moisture-7))/100*a.FAQVolumeBruto)/ab.nett*d.VolumeNetto)>0,
 SUM(((100-(aa.Moisture-7))/100*a.FAQVolumeBruto)/ab.nett*d.VolumeNetto),
 SUM(IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)/b.VolumeNetto*IFNULL(f.VolumeNetto,d.VolumeNetto))
    ) netto,
  IFNULL(f.SupplyBatchDate,d.SupplyBatchDate) AS DeliveryDate
    #IFNULL(SUM(((100-(aa.Moisture-7))/100*(IF(aa.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto)))/ab.nett*d.VolumeNetto),
    #IFNULL(SUM(IF(e.FAQVolumeBruto>0,e.FAQVolumeBruto,e.FFVolumeBruto)),SUM(IF(c.FAQVolumeBruto>0,c.FAQVolumeBruto,c.FFVolumeBruto)))) netto
    FROM ktv_supplychain_transaction a
    LEFT JOIN ktv_supplychain_transaction_dtl aa ON a.SupplyTransID=aa.SupplyTransID
    LEFT JOIN (
        SELECT SUM((100-(b.Moisture-7))/100*(IF(b.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto))) nett,SupplyBatchID
        FROM ktv_supplychain_transaction a
        LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
        GROUP BY SupplyBatchID
    ) ab ON a.SupplyBatchID=ab.SupplyBatchID
    LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID
    LEFT JOIN ktv_supplychain_transaction c ON b.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
    LEFT JOIN ktv_supplychain_batch d ON c.SupplyBatchID=d.SupplyBatchID
    LEFT JOIN ktv_supplychain_transaction e ON d.SupplyBatchNumber=e.SupplyID AND e.SupplyType='Batch'
    LEFT JOIN ktv_supplychain_batch f ON e.SupplyBatchID=f.SupplyBatchID
    WHERE 1 = 1
    AND a.SupplyType='Farmer'
GROUP BY a.SupplyBatchID,a.SupplyID) a
LEFT JOIN ktv_farmer_view kcf ON a.SupplyID = kcf.FarmerID
%s
WHERE
  1 = 1 AND kcf.StatusCode = 'active'
  %s
GROUP BY %s
ORDER BY label
            ";
        $this->traceability_transaction = "
            SELECT %s label,count(SupplyTransID) total
            FROM ktv_supplychain_transaction kst
            LEFT JOIN ktv_farmer_view kcf ON kst.SupplyID = kcf.FarmerID
            %s
            WHERE SupplyType='Farmer' AND kcf.StatusCode = 'active' %s
            GROUP BY %s
            ORDER BY label";
        $this->traceability_bu = "
            SELECT %s label,count(SupplychainID) total
            FROM ktv_supplychain_org_view kcf
            %s
            WHERE (OrgType='%s' OR OrgType='%s') %s
            GROUP BY %s
            ORDER BY label";
        $this->traceability_farmer_sell = "
            SELECT %s label,count(distinct SupplyID) total
            FROM ktv_supplychain_transaction kst
            LEFT JOIN ktv_farmer_view kcf ON kst.SupplyID = kcf.FarmerID
            %s
            WHERE SupplyType='Farmer' AND kcf.StatusCode = 'active' %s
            GROUP BY %s
            ORDER BY label";
        $this->traceability_sell = "
            SELECT %s label,
               sum(IFNULL(FFVolumeNetto,0)+IFNULL(FAQVolumeNetto,0)) total,
               count(SupplyTransID) total_trans,
               PERIOD_DIFF(DATE_FORMAT(max(DateTransaction),'%s'),DATE_FORMAT(min(DateTransaction),'%s'))+1 bulan,
               min(date(DateTransaction)) date_min, max(date(DateTransaction)) date_max
            FROM ktv_supplychain_transaction kst
            LEFT JOIN ktv_farmer_view kcf ON kst.SupplyID = kcf.FarmerID
            %s
            WHERE SupplyType='Farmer' AND kcf.StatusCode = 'active' %s
            GROUP BY %s
            ORDER BY label";
        $this->traceability_total = "SELECT
    %s AS label,
    SUM(wh_netto) AS total_penjualan,
    COUNT(wh_supplychainid) AS total_transaction,
    COUNT(DISTINCT farmer_id) AS total_farmer_sell,
    SUM(wh_netto) AS total_sell,
    PERIOD_DIFF(DATE_FORMAT(max(wh_date),'%%Y%%m'),DATE_FORMAT(min(wh_date),'%%Y%%m')) + 1 bulan,
    min(date(wh_date)) date_min,
    max(date(wh_date)) date_max
    %s
FROM
    rpt_traceability rpt
    LEFT JOIN ktv_farmer_view kcf ON rpt.farmer_id = kcf.FarmerID
    LEFT JOIN ktv_warehouse wh ON rpt.wh_orgid = wh.WarehouseID
    %s
WHERE
    1=1 AND kcf.StatusCode = 'active'
    AND Farmer_villageid IS NOT NULL
    %s
GROUP BY label
        ";
            $this->traceability_sales_certified = "SELECT
    %s AS label,
    COUNT(DISTINCT IF(rpt.farmer_iscertified='1',rpt.farmer_id,null)) AS farmer_certified,
    SUM(IF(rpt.farmer_iscertified='1',rpt.farmer_netto,0)) AS netto_certified,
    COUNT(DISTINCT IF(rpt.farmer_iscertified!='1',rpt.farmer_id,null)) AS farmer_uncertified,
    SUM(IF(rpt.farmer_iscertified!='1',rpt.farmer_netto,0)) AS netto_uncertified
FROM
    rpt_traceability rpt
    LEFT JOIN ktv_farmer_view kcf ON rpt.farmer_id = kcf.FarmerID
    LEFT JOIN ktv_warehouse wh ON rpt.wh_orgid = wh.WarehouseID
    %s
WHERE
(rpt.farmer_id!='' AND rpt.farmer_id IS NOT NULL) AND kcf.StatusCode = 'active'
    %s
    AND ((1_date between '%s' and '%s') OR (2_date between '%s' and '%s')  OR (wh_date between '%s' and '%s'))
GROUP BY label
ORDER BY label
            ";
            $this->traceability_farmer = "SELECT
            %s AS label,
    COUNT(kcf.FarmerID) AS farmer,
    SUM(IF(kc.FarmerID,1,0)) AS farmer_certified,
    SUM(IF(kc.FarmerID IS NULL,1,0)) AS farmer_uncertified,
    SUM(IF(rt.farmer_id,1,0)) AS farmer_selling,
    SUM(IF(kc.FarmerID AND rt.farmer_id,1,0)) AS farmer_certified_selling,
    SUM(IF(kc.FarmerID IS NULL AND rt.farmer_id,1,0)) AS farmer_uncertified_selling

FROM ktv_farmer_view kcf
LEFT JOIN (SELECT kc.FarmerID FROM ktv_certification kc GROUP BY kc.FarmerID) kc ON kc.FarmerID = kcf.FarmerID
LEFT JOIN (SELECT rt.farmer_id FROM rpt_traceability rt GROUP BY rt.farmer_id) rt ON rt.farmer_id = kcf.FarmerID
%s
WHERE
    1 = 1 AND kcf.StatusCode = 'active'
    %s
GROUP BY label
ORDER BY label
";
            $this->traceability_production = "SELECT
            %s AS label,
    SUM(kcfg.Production) AS production,
    SUM(IF(kc.FarmerID,kcfg.Production,0)) AS production_certified,
    SUM(IF(kc.FarmerID IS NULL,kcfg.Production,0)) AS production_uncertified,
    SUM(IF(rt.farmer_id,rt.netto,0)) AS farmer_selling,
    SUM(IF(kc.FarmerID AND rt.farmer_id,rt.netto,0)) AS farmer_certified_selling,
    SUM(IF(kc.FarmerID IS NULL AND rt.farmer_id,rt.netto,0)) AS farmer_uncertified_selling

FROM ktv_farmer_view kcf
LEFT JOIN (
    SELECT
        kcfg.FarmerID
        , kcfg.GardenNr
        , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`) AS Production
    FROM ktv_farmer_garden kcfg
    JOIN (SELECT g.FarmerID, g.GardenNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY g.FarmerID, g.GardenNr) g ON g.FarmerID = kcfg.FarmerID AND g.GardenNr = kcfg.GardenNr AND g.SurveyNr = kcfg.SurveyNr
) kcfg ON kcfg.FarmerID = kcf.FarmerID
LEFT JOIN (SELECT kc.FarmerID FROM ktv_certification kc GROUP BY kc.FarmerID) kc ON kc.FarmerID = kcf.FarmerID
LEFT JOIN (SELECT rt.farmer_id, SUM(rt.wh_netto) AS netto FROM rpt_traceability rt GROUP BY rt.farmer_id) rt ON rt.farmer_id = kcf.FarmerID
%s
WHERE
    1 = 1 AND kcf.StatusCode = 'active'
    %s
GROUP BY label
ORDER BY label
";

        //training
        $this->training_jumlah = "SELECT
   IFNULL(%s, 'unknown') label
   ,COUNT(DISTINCT IF(CPGtrainingsID=1,m.FarmerID,NULL)) gap
   ,COUNT(DISTINCT IF(CPGtrainingsID=2,m.FarmerID,NULL)) gnp
   ,COUNT(DISTINCT IF(CPGtrainingsID=8,m.FarmerID,NULL)) gfp
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gap_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gap_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gap_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gap_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gap_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gap_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gap_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gnp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gnp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gnp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gnp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gnp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gnp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gnp_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gfp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gfp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gfp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gfp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gfp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gfp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gfp_7
FROM (
  SELECT *
  FROM (
   SELECT
      kcfg.FarmerID,
      CPGtrainingsID,
      VillageID,
      SubDistrictID,
      SubDistrict,
      kcf.CPGid,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
   UNION ALL
   SELECT
      kcfg.FarmerID,
      CPGtrainingsID,
      VillageID,
      SubDistrictID,
      SubDistrict,
      kcf.CPGid,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_kader_trainings_participants` kcfg
   LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
   )m GROUP BY FarmerID, CPGtrainingsID
   ) m
%s
WHERE VillageID is not null %s
GROUP BY %s
ORDER BY label
            ";
        //training
        $this->training_master_jumlah = "SELECT
   IFNULL(%s, 'unknown') label
   ,COUNT(DISTINCT IF(CPGtrainingsID=1,m.FarmerID,NULL)) gap
   ,COUNT(DISTINCT IF(CPGtrainingsID=2,m.FarmerID,NULL)) gnp
   ,COUNT(DISTINCT IF(CPGtrainingsID=8,m.FarmerID,NULL)) gfp
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gap_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gap_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gap_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gap_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gap_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gap_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gap_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gnp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gnp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gnp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gnp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gnp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gnp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gnp_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gfp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gfp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gfp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gfp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gfp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gfp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gfp_7
FROM (
  SELECT
    tp.ParticipantStaffID AS FarmerID,
    mt.CPGtrainingsID,
    YEAR(mt.`TrainingStart`) AS `year`,
    mt.TrainingProvince AS ProvinceID,
    mt.DistrictID
  FROM ktv_master_trainings mt
  JOIN ktv_master_trainings_participants tp ON tp.MasterTrainingID = mt.MasterTrainingID
   ) m
%s
WHERE 1 = 1 %s
GROUP BY %s
ORDER BY label
            ";
        //training
        $this->training_kader_jumlah = "SELECT
   IFNULL(%s, 'unknown') label
   ,COUNT(DISTINCT IF(CPGtrainingsID=1,m.FarmerID,NULL)) gap
   ,COUNT(DISTINCT IF(CPGtrainingsID=2,m.FarmerID,NULL)) gnp
   ,COUNT(DISTINCT IF(CPGtrainingsID=8,m.FarmerID,NULL)) gfp
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gap_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gap_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gap_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gap_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gap_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gap_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gap_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gnp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gnp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gnp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gnp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gnp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gnp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gnp_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gfp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gfp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gfp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gfp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gfp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gfp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gfp_7
FROM (
  SELECT *
  FROM (
   SELECT
      kcfg.FarmerID,
      CPGtrainingsID,
      VillageID,
      SubDistrictID,
      SubDistrict,
      kcf.CPGid,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_kader_trainings_participants` kcfg
   LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
   )m GROUP BY FarmerID, CPGtrainingsID
   ) m
%s
WHERE VillageID is not null %s
GROUP BY %s
ORDER BY label
            ";
        //training farmer
        $this->training_farmer_jumlah = "SELECT
   IFNULL(%s, 'unknown') label
   ,COUNT(DISTINCT IF(CPGtrainingsID=1,m.FarmerID,NULL)) gap
   ,COUNT(DISTINCT IF(CPGtrainingsID=2,m.FarmerID,NULL)) gnp
   ,COUNT(DISTINCT IF(CPGtrainingsID=8,m.FarmerID,NULL)) gfp
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gap_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gap_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gap_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gap_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gap_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gap_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=1 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gap_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gnp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gnp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gnp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gnp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gnp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gnp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=2 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gnp_7
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-6),m.FarmerID,NULL)) gfp_1
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-5),m.FarmerID,NULL)) gfp_2
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-4),m.FarmerID,NULL)) gfp_3
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-3),m.FarmerID,NULL)) gfp_4
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-2),m.FarmerID,NULL)) gfp_5
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-1),m.FarmerID,NULL)) gfp_6
   ,COUNT(DISTINCT IF((CPGtrainingsID=8 AND m.`year`=YEAR(NOW())-0),m.FarmerID,NULL)) gfp_7
FROM (
  SELECT *
  FROM (
   SELECT
      kcfg.FarmerID,
      CPGtrainingsID,
      VillageID,
      SubDistrictID,
      SubDistrict,
      kcf.CPGid,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
   )m GROUP BY FarmerID, CPGtrainingsID
   ) m
%s
WHERE VillageID is not null %s
GROUP BY %s
ORDER BY label
            ";
        $this->training_tahun = "
SELECT
   IFNULL(%s, 'unknown') label
   ,`year`
   ,SUM(IF(CPGtrainingsID=1,1,0)) gap
   ,SUM(IF(CPGtrainingsID=2,1,0)) gnp
   ,SUM(IF(CPGtrainingsID=8,1,0)) gfp
FROM (
   SELECT
      CPGtrainingsID,
      kcfg.FarmerID,
      VillageID,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.StatusCode = 'active'
   UNION ALL
   SELECT
      CPGtrainingsID,
      kcfg.FarmerID,
      VillageID,
      YEAR(t.`TrainingStart`) AS `year`
   FROM
      `ktv_kader_trainings_participants` kcfg
   JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   WHERE VillageID IS NOT NULL AND kcf.StatusCode = 'active'
   ) m
%s
WHERE VillageID is not null %s
GROUP BY %s, `year`
ORDER BY label,`year`
            ";
        //finance
        $this->finance_jumlah = "SELECT
   %s AS label
   ,COUNT(DISTINCT m.FarmerID) gfp
   ,COUNT(DISTINCT IF(Gender = 1,m.FarmerID,NULL)) male
   ,COUNT(DISTINCT IF(Gender = 2,m.FarmerID,NULL)) female
   ,SUM(fin) AS fin
   ,SUM(IF(Account = 1,1,0)) AS account
   ,SUM(IF(MoneyUsageTabung OR MoneyUsageInvestasi OR MoneyUsageEmas,1,0)) AS saving
   ,SUM(IF(LoanYesNo IN (1,3,4),1,0)) AS loan
   ,SUM(IF(MoneyUsageTabung,1,0)) AS saving_money
   ,SUM(IF(MoneyUsageInvestasi,1,0)) AS saving_invest
   ,SUM(IF(MoneyUsageEmas,1,0)) AS saving_gold
   ,SUM(IF(MoneyUsageHarian OR MoneyUsageKonsumsi,1,0)) AS saving_no
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitBank=1,1,0)) AS loan_yes_current
   ,SUM(IF(LoanYesNo = 2,1,0)) AS loan_no
   ,SUM(IF(LoanYesNo = 3,1,0)) AS loan_yes_past
   ,SUM(IF(LoanYesNo = 1 AND (LoanUnitBank=0 OR LoanUnitBank IS NULL),1,0)) AS loan_yes_past_current
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitKeluarga,1,0)) AS loan_from_family
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitBank,1,0)) AS loan_from_bank
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitTengkulak,1,0)) AS loan_from_trader
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitKoperasi,1,0)) AS loan_from_coops
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanInvestasiKebun,1,0)) AS loan_for_farm
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanInvestasiLain,1,0)) AS loan_for_other
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanSekolah,1,0)) AS loan_for_school
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanHarian,1,0)) AS loan_for_daily
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanDarurat,1,0)) AS loan_for_emergency
   ,SUM(IF(Account = 1 AND DepositWithdrawnMoneyLast12m = 1,1,0)) AS account_active
   ,SUM(IF(Account = 2,1,0)) AS account_no
   ,SUM(IF(Account = 1 AND DepositWithdrawnMoneyLast12m = 2,1,0)) AS account_inactive
   ,SUM(IF(Account = 1 AND (SavingUnitBank=1 OR SavingUnitKoperasi=1),1,0)) AS product_saving
   ,SUM(IF(Account = 1 AND (LoanUnitBank=1 OR LoanUnitKoperasi=1),1,0)) AS product_loan
   ,SUM(IF(Account = 1 AND ((LoanUnitBank=1 OR LoanUnitKoperasi=1) AND (SavingUnitBank=1 OR SavingUnitKoperasi=1)),1,0)) AS product_saving_loan
   ,SUM(IF(NeedLoan = 1,1,0)) AS need_loan
   ,SUM(IF(NeedLoan = 2,1,0)) AS need_loan_no
   ,SUM(IF(FutureReasonSekolah OR FutureReasonInvestasiKebun OR FutureReasonInvestasiLain OR FutureReasonDarurat OR FutureReasonKesehatan, 1, 0)) AS future_count
   ,SUM(IF(FutureReasonSekolah,1,0)) AS future_school
   ,SUM(IF(FutureReasonInvestasiKebun,1,0)) AS future_invest_farm
   ,SUM(IF(FutureReasonInvestasiLain,1,0)) AS future_invest_other
   ,SUM(IF(FutureReasonDarurat,1,0)) AS future_emergency
   ,SUM(IF(FutureReasonKesehatan,1,0)) AS future_health
   ,SUM(IF(ValueCocoaFarm = 1 ,1,0)) AS value_10
   ,SUM(IF(ValueCocoaFarm = 2 ,1,0)) AS value_10_20
   ,SUM(IF(ValueCocoaFarm = 3 ,1,0)) AS value_20_50
   ,SUM(IF(ValueCocoaFarm = 4 ,1,0)) AS value_50_100
   ,SUM(IF(ValueCocoaFarm = 5 ,1,0)) AS value_100_200
   ,SUM(IF(ValueCocoaFarm = 6 ,1,0)) AS value_200
   ,SUM(IF(ValueCocoaFarm IS NULL OR ValueCocoaFarm = 7  ,1,0)) AS value_0
FROM (
   SELECT
      CPGtrainingsID,
      kcfg.FarmerID,
      kcf.CPGid,
      VillageID,
      SubDistrict,
      SubDistrictID,
      IF(kcfg.PetaniKakao = 1,kcf.`Gender`,kf.`AnggotaGender`) AS Gender,
      IF(fc.`FarmerID`,1,0) AS fin
      ,Account
      ,LoanYesNo
      ,MoneyUsageTabung
      ,MoneyUsageInvestasi
      ,MoneyUsageEmas
      ,MoneyUsageHarian
      ,MoneyUsageKonsumsi
      ,SavingUnitBank
      ,SavingUnitKoperasi
      ,LoanUnitKeluarga
      ,LoanUnitBank
      ,LoanUnitTengkulak
      ,LoanUnitKoperasi
      ,UsageCurrentLoanInvestasiKebun
      ,UsageCurrentLoanInvestasiLain
      ,UsageCurrentLoanSekolah
      ,UsageCurrentLoanHarian
      ,UsageCurrentLoanDarurat
      ,DepositWithdrawnMoneyLast12m
      ,NeedLoan
      ,FutureReasonSekolah
      ,FutureReasonInvestasiKebun
      ,FutureReasonInvestasiLain
      ,FutureReasonDarurat
      ,FutureReasonKesehatan
      ,ValueCocoaFarm
   FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   LEFT JOIN ktv_family kf ON kf.`FamilyID` = kcfg.`FamilyID`
   LEFT JOIN `ktv_farmer_financial` fc ON fc.`FarmerID` = kcfg.`FarmerID` AND fc.`SurveyNr` = 0
   WHERE VillageID AND kcf.StatusCode = 'active'
   AND CPGtrainingsID=8
   UNION ALL
   SELECT
      CPGtrainingsID,
      kcfg.FarmerID,
      kcf.CPGid,
      VillageID,
      SubDistrict,
      SubDistrictID,
      IF(kcfg.PetaniKakao = 1,kcf.`Gender`,kf.`AnggotaGender`) AS Gender,
      IF(fc.`FarmerID`,1,0) AS fin
      ,Account
      ,LoanYesNo
      ,MoneyUsageTabung
      ,MoneyUsageInvestasi
      ,MoneyUsageEmas
      ,MoneyUsageHarian
      ,MoneyUsageKonsumsi
      ,SavingUnitBank
      ,SavingUnitKoperasi
      ,LoanUnitKeluarga
      ,LoanUnitBank
      ,LoanUnitTengkulak
      ,LoanUnitKoperasi
      ,UsageCurrentLoanInvestasiKebun
      ,UsageCurrentLoanInvestasiLain
      ,UsageCurrentLoanSekolah
      ,UsageCurrentLoanHarian
      ,UsageCurrentLoanDarurat
      ,DepositWithdrawnMoneyLast12m
      ,NeedLoan
      ,FutureReasonSekolah
      ,FutureReasonInvestasiKebun
      ,FutureReasonInvestasiLain
      ,FutureReasonDarurat
      ,FutureReasonKesehatan
      ,ValueCocoaFarm
   FROM
      `ktv_kader_trainings_participants` kcfg
   JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   LEFT JOIN ktv_family kf ON kf.`FamilyID` = kcfg.`FamilyID`
   LEFT JOIN `ktv_farmer_financial` fc ON fc.`FarmerID` = kcfg.`FarmerID` AND fc.`SurveyNr` = 0
   WHERE VillageID AND kcf.StatusCode = 'active'
   AND CPGtrainingsID=8
   ) m
%s
WHERE VillageID %s
GROUP BY %s
ORDER BY label
            ";
        $this->finance_household = "SELECT
              %s AS label
              ,COUNT(a.FarmerID) as total
            FROM
               (SELECT kcf.FarmerID,kcf.VillageID,kcf.CPGid,kcf.SubDistrict,kcf.SubDistrictID
               FROM
               ktv_cpg_batch_trainings kcbt
               INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID = kcbtf.CpgBatchTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kcbtf.FarmerID = kcf.FarmerID AND kcf.VillageID
               WHERE kcbt.CPGtrainingsID=8 AND kcf.StatusCode = 'active'
               UNION ALL
               SELECT kcf.FarmerID,kcf.VillageID,kcf.CPGid,kcf.SubDistrict,kcf.SubDistrictID
               FROM
               ktv_kader_trainings kkt
               INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kktp.FarmerID = kcf.FarmerID AND kcf.VillageID
               WHERE
                kkt.CPGtrainingsID=8 AND kcf.StatusCode = 'active'
               ) a
            %s
            WHERE
            1 = 1 AND VillageID
            %s
            GROUP BY label
            order by label";
        $this->finance_kelamin = "
            SELECT
              %s AS label
              ,sum(IF(kelamin=1,1,0)) male
              ,sum(IF(kelamin=2,1,0)) female
              ,sum(IF((kelamin not in (1,2) || kelamin is null),1,0)) other
            FROM
               (SELECT kcf.FarmerID,kcf.VillageID,
                  if(kcbtf.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
               FROM
               ktv_cpg_batch_trainings kcbt
               INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID = kcbtf.CpgBatchTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kcbtf.FarmerID = kcf.FarmerID
               LEFT JOIN ktv_family kf ON kcbtf.FamilyID = kf.FamilyID
               WHERE kcbt.CPGtrainingsID=8 AND kcf.StatusCode = 'active'
               UNION ALL
               SELECT kcf.FarmerID,kcf.VillageID,
                  if(kktp.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
               FROM
               ktv_kader_trainings kkt
               INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kktp.FarmerID = kcf.FarmerID
               LEFT JOIN ktv_family kf ON kktp.FamilyID = kf.FamilyID
               WHERE kkt.CPGtrainingsID=8 AND kcf.StatusCode = 'active'
               ) a
            %s
            WHERE 1 = 1 AND VillageID
              %s
            GROUP BY label
            ORDER BY label
            ";

        $this->sql_environment = "SELECT
   label
    , SUM(TotalTrees) AS TotalTrees
    , SUM(Hectare) AS Hectare
    , SUM(Production) AS Production
    , SUM(Productivity) AS Productivity
    , SUM(Productivity_Trees) AS Productivity_Trees
    , SUM(Kg_Kompos_Tree) AS Kg_Kompos_Tree
    , SUM(TBM_Kompos) AS TBM_Kompos
    , SUM(TM_Kompos) AS TM_Kompos
    , SUM(TR_Kompos) AS TR_Kompos
    , SUM(Trees_Kompos) AS Trees_Kompos
    , SUM(Kg_Kompos) AS Kg_Kompos
    , AVG(Kg_Kompos_Hectare) AS Kg_Kompos_Hectare
    , SUM(CO2_Kompos) AS CO2_Kompos
    , SUM(TBM_Fertilized) AS TBM_Fertilized
    , SUM(TM_Fertilized) AS TM_Fertilized
    , SUM(TR_Fertilized) AS TR_Fertilized
    , SUM(Trees_Fertilized) AS Trees_Fertilized
    , SUM(G_Urea_Tree) AS G_Urea_Tree
    , SUM(G_NPK_Tree) AS G_NPK_Tree
    , SUM(Kg_Urea) AS Kg_Urea
    , SUM(Kg_NPK) AS Kg_NPK
    , SUM(Kg_Fertilizer) AS Kg_Fertilizer
    , SUM(Kg_Fertilizer_Tree) AS Kg_Fertilizer_Tree
    , AVG(Kg_Fertilizer_Hectare) AS Kg_Fertilizer_Hectare
    , SUM(CO2_Urea) AS CO2_Urea
    , SUM(CO2_NPK) AS CO2_NPK
    , SUM(CO2_ZA) AS CO2_ZA
    , SUM(CO2_Total) AS CO2_Total
    , AVG(CO2_Hectare) AS CO2_Hectare
    , SUM(tCO2e_tCocoa) AS tCO2e_tCocoa
    , SUM(C_Stock_Trees) AS C_Stock_Trees
    , SUM(C_Stock) AS C_Stock
    , AVG(C_Stock_Hectare) AS C_Stock_Hectare
FROM (
SELECT
   IFNULL(%s, 'unknown') label
   ,VillageID
   ,SubDistrict
   ,SubDistrictID
   ,CPGid
      -- `Province`
    -- , `District`
    -- , `ktv_farmer_garden`.`FarmerID`
    -- , `GardenNr`
    -- , `SurveyNr`
    -- , `TahunTanamanCocoa`
    -- ,  YEAR(NOW()) - `TahunTanamanCocoa` AS TreeAge
    , `PohonTBM`+`PohonTM`+`PohonRehab` AS TotalTrees
    , `GardenHaUnCertified` AS Hectare
    , `PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg` AS Production
    , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)
    /`GardenHaUnCertified` AS Productivity
    , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)
    /(`PohonTBM`+`PohonTM`+`PohonRehab`) AS Productivity_Trees
    , `FrequentFertilizationKompos`*`DoseFertilizerKompos` AS Kg_Kompos_Tree
    , `PohonTBM`*`KomposTBM` AS TBM_Kompos
    , `PohonTM`*`KomposTM` AS TM_Kompos
    , `PohonRehab`*`KomposTR` AS TR_Kompos
    , `PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR` AS Trees_Kompos
    , (`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`) AS Kg_Kompos
    , ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))/ `GardenHaUnCertified` AS Kg_Kompos_Hectare
    , 0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))
    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`)) AS CO2_Kompos
    , `PohonTBM`*`PupukTBM` AS TBM_Fertilized
    , `PohonTM`*`PupukTM` AS TM_Fertilized
    , `PohonRehab`*`PupukTR` AS TR_Fertilized
    , `PohonTBM`*`PupukTBM`+`PohonTM`*`PupukTM`+`PohonRehab`*`PupukTR` AS Trees_Fertilized
    , `FrUrea`*`DoUrea` AS G_Urea_Tree
    , `FrNpk`*`DoNpk` AS G_NPK_Tree
    , ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000 AS Kg_Urea
    , ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000 AS Kg_NPK
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`))/1000 AS Kg_Fertilizer
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    + ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/(`PohonTBM`*`PupukTBM`+`PohonTM`*`PupukTM`+`PohonRehab`*`PupukTR`) AS Kg_Fertilizer_Tree
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/`GardenHaUnCertified` AS Kg_Fertilizer_Hectare
    , 2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000) AS CO2_Urea
    , 0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000) AS CO2_NPK
    , 0.91986 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrZa`*`DoZa`)/1000) AS CO2_ZA
    , (2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))
    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000))
    + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))
    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))) AS CO2_Total
    , (((2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))
    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)))
    + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))
    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))))/`GardenHaUnCertified` AS CO2_Hectare
    , ((((2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))
    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)))
        + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))
    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))))
    /(`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)) AS tCO2e_tCocoa
    , `PohonTM` + `PohonRehab` AS C_Stock_Trees,
    CASE WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 2 THEN (0.47*(0.202*0.03^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 3 AND 5 THEN (0.47*(0.202*0.05^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 6 AND 10 THEN (0.47*(0.202*0.1^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`)  BETWEEN 11 AND 15 THEN (0.47*(0.202*0.12^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`)  BETWEEN  16 AND 20 THEN (0.47*(0.202*0.15^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN  21 AND 30 THEN (0.47*(0.202*0.2^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN  31 AND 50 THEN (0.47*(0.202*0.25^2.112)* (`PohonTM` + `PohonRehab`))
 END AS C_Stock
    , (CASE WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 2 THEN (0.47*(0.202*0.03^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 3 AND 5 THEN  (0.47*(0.202*0.05^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 6 AND 10 THEN (0.47*(0.202*0.1^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`)  BETWEEN 11 AND 15 THEN (0.47*(0.202*0.12^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`)  BETWEEN  16 AND 20 THEN (0.47*(0.202*0.15^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN  21 AND 30 THEN (0.47*(0.202*0.2^2.112)* (`PohonTM` + `PohonRehab`))
    WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN  31 AND 50 THEN (0.47*(0.202*0.25^2.112)* (`PohonTM` + `PohonRehab`))
 END )/ `GardenHaUnCertified` AS C_Stock_Hectare
FROM
    `ktv_farmer_garden`
    INNER JOIN `ktv_farmer_view` kcf
        ON (`ktv_farmer_garden`.`FarmerID` = kcf.`FarmerID`)
      %s
    WHERE  `GardenHaUnCertified` >0 AND kcf.StatusCode = 'active'
    AND (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 50
    AND (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)>0
    AND (((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))>0
    OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)>0
    OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)> 0)
    AND ((((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/`GardenHaUnCertified`) < 12000
    AND `FrequentFertilizationKompos`*`DoseFertilizerKompos` <20
    %s
) r
GROUP BY %s
ORDER BY label
         ";

        $this->sql_agrinput_data = "SELECT
   label
    , SUM(TotalTrees) AS TotalTrees
    , SUM(Hectare) AS Hectare
    , SUM(Production) AS Production
    , SUM(Productivity) AS Productivity
    , SUM(Productivity_Trees) AS Productivity_Trees
    , SUM(Kg_Kompos_Tree) AS Kg_Kompos_Tree
    , SUM(TBM_Kompos) AS TBM_Kompos
    , SUM(TM_Kompos) AS TM_Kompos
    , SUM(TR_Kompos) AS TR_Kompos
    , SUM(Trees_Kompos) AS Trees_Kompos
    , SUM(Kg_Kompos) AS Kg_Kompos
    , AVG(Kg_Kompos_Hectare) AS Kg_Kompos_Hectare
    , SUM(CO2_Kompos) AS CO2_Kompos
    , SUM(TBM_Fertilized) AS TBM_Fertilized
    , SUM(TM_Fertilized) AS TM_Fertilized
    , SUM(TR_Fertilized) AS TR_Fertilized
    , SUM(Trees_Fertilized) AS Trees_Fertilized
    , SUM(G_Urea_Tree) AS G_Urea_Tree
    , SUM(G_NPK_Tree) AS G_NPK_Tree
    , SUM(Kg_Urea) AS Kg_Urea
    , SUM(Kg_NPK) AS Kg_NPK
    , SUM(Kg_Fertilizer) AS Kg_Fertilizer
    , SUM(Kg_Fertilizer_Tree) AS Kg_Fertilizer_Tree
    , AVG(Kg_Fertilizer_Hectare) AS Kg_Fertilizer_Hectare
FROM (
SELECT
   IFNULL(%s, 'unknown') label
   ,VillageID
   ,SubDistrict
   ,SubDistrictID
   ,CPGid
      -- `Province`
    -- , `District`
    -- , `ktv_farmer_garden`.`FarmerID`
    -- , `GardenNr`
    -- , `SurveyNr`
    -- , `TahunTanamanCocoa`
    -- ,  YEAR(NOW()) - `TahunTanamanCocoa` AS TreeAge
    , `PohonTBM`+`PohonTM`+`PohonRehab` AS TotalTrees
    , `GardenHaUnCertified` AS Hectare
    , `PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg` AS Production
    , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)
    /`GardenHaUnCertified` AS Productivity
    , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)
    /(`PohonTBM`+`PohonTM`+`PohonRehab`) AS Productivity_Trees
    , `FrequentFertilizationKompos`*`DoseFertilizerKompos` AS Kg_Kompos_Tree
    , `PohonTBM`*`KomposTBM` AS TBM_Kompos
    , `PohonTM`*`KomposTM` AS TM_Kompos
    , `PohonRehab`*`KomposTR` AS TR_Kompos
    , `PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR` AS Trees_Kompos
    , (`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`) AS Kg_Kompos
    , ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))/ `GardenHaUnCertified` AS Kg_Kompos_Hectare
    , 0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))
    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`)) AS CO2_Kompos
    , `PohonTBM`*`PupukTBM` AS TBM_Fertilized
    , `PohonTM`*`PupukTM` AS TM_Fertilized
    , `PohonRehab`*`PupukTR` AS TR_Fertilized
    , `PohonTBM`*`PupukTBM`+`PohonTM`*`PupukTM`+`PohonRehab`*`PupukTR` AS Trees_Fertilized
    , `FrUrea`*`DoUrea` AS G_Urea_Tree
    , `FrNpk`*`DoNpk` AS G_NPK_Tree
    , ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000 AS Kg_Urea
    , ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000 AS Kg_NPK
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`))/1000 AS Kg_Fertilizer
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    + ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/(`PohonTBM`*`PupukTBM`+`PohonTM`*`PupukTM`+`PohonRehab`*`PupukTR`) AS Kg_Fertilizer_Tree
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/`GardenHaUnCertified` AS Kg_Fertilizer_Hectare
FROM
    `ktv_farmer_garden`
    INNER JOIN `ktv_farmer_view` kcf
        ON (`ktv_farmer_garden`.`FarmerID` = kcf.`FarmerID`)
      %s
    WHERE  `GardenHaUnCertified` >0 AND kcf.StatusCode = 'active'
    AND (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 50
    AND (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)>0
    AND (((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))>0
    OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)>0
    OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)> 0)
    AND ((((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/`GardenHaUnCertified`) < 12000
    AND `FrequentFertilizationKompos`*`DoseFertilizerKompos` <20
    %s
) r
GROUP BY %s
ORDER BY label
         ";
        $this->sql_env_fertilizer = "
SELECT
   IFNULL(%s, 'unknown') label,
   COUNT(DISTINCT kcf.FarmerID) TotalFarmer,
   #AVG(2014-TahunTanamanCocoa) AS FarmAge,
   SUM(kcfg.PohonTBM) TBM,
   SUM(kcfg.PohonTM) TM,
   SUM(kcfg.PohonRehab) TR,
   SUM(kcfg.PohonTBM) + SUM(kcfg.PohonTM) + SUM(kcfg.PohonRehab) AS TotalTrees,
   SUM(kcfg.ShadeTreesNr) ShadeTrees,
   SUM(kcfg.GardenHaUncertified) TotalHa,
   SUM(kcfg.GardenHaUncertified) AS GardenHaUncertified,
   SUM(
       (
         IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
         ) + (
         IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
         ) + (
         IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
         )
         ) AS ProductionKg,
   SUM(
       (
         IFNULL(kcfg.PanenTrekMonths, 0) * IFNULL(kcfg.PanenTrekPanenMonth, 0) * IFNULL(kcfg.PanenTrekKg, 0)
         ) + (
         IFNULL(kcfg.PanenBiasaMonths, 0) * IFNULL(kcfg.PanenBiasaPanenMonth, 0) * IFNULL(kcfg.PanenBiasaKg, 0)
         ) + (
         IFNULL(kcfg.PanenRayaMonths, 0) * IFNULL(kcfg.PanenRayaPanenMonth, 0) * IFNULL(kcfg.PanenRayaKg, 0)
         )
         )/ SUM(kcfg.GardenHaUncertified) AS Kg_Ha,

   SUM(CASE WHEN FrequentFertilizationKompos > 5 THEN 5 ELSE FrequentFertilizationKompos END * CASE WHEN DoseFertilizerKompos > 6 THEN 6 ELSE DoseFertilizerKompos END * PohonTM)/SUM(GardenHaUncertified) AS Compost_Kg_Ha,
   SUM(CASE WHEN FrUrea > 6 THEN 6 ELSE  FrUrea END * DoUrea/1000 * PohonTM)/SUM(GardenHaUncertified) AS Urea_Kg_Ha,
   SUM(FrTsp * DoTsp/1000 * PohonTM)/SUM(GardenHaUncertified) AS TSP_Kg_Ha,
   SUM(FrNpk * DoNpk/1000 * PohonTM)/SUM(GardenHaUncertified) AS NPK_Kg_Ha,
   SUM(FrKcl * DoKcl/1000 * PohonTM)/SUM(GardenHaUncertified) AS Kcl_Kg_Ha,
   SUM(CASE WHEN FrUrea > 6 THEN 6 ELSE  FrUrea END * DoUrea/1000 * PohonTM)/SUM(GardenHaUncertified) +
   SUM(FrTsp * DoTsp/1000 * PohonTM)/SUM(GardenHaUncertified) +
   SUM(FrNpk * DoNpk/1000 * PohonTM)/SUM(GardenHaUncertified) +
   SUM(FrKcl * DoKcl/1000 * PohonTM)/SUM(GardenHaUncertified) AS TotalFert_Kg_Ha,
   SUM(CASE WHEN FrUrea > 1 THEN 1 ELSE 0 END) AS Application_Urea,
   SUM(CASE WHEN FrTsp > 1 THEN 1 ELSE 0 END) AS Application_TSP,
   SUM(CASE WHEN FrNpk > 1 THEN 1 ELSE 0 END) AS Application_NPK,
   SUM(CASE WHEN FrKcl > 1 THEN 1 ELSE 0 END) AS Application_KCl,
   SUM(CASE WHEN FrZa > 1 THEN 1 ELSE 0 END) AS Application_ZA,
   SUM(CASE WHEN FrKomposKandang > 0 THEN 1 ELSE 0 END) AS Kompos_Kandang,
   SUM(CASE WHEN FrKomposCair > 0 THEN 1 ELSE 0 END) AS Kompos_Cair,
   SUM(CASE WHEN FrKomposGranula > 0 THEN 1 ELSE 0 END) AS Kompos_Granula,
   SUM(PupukTBM) AS TBM_Application,SUM(PupukTM) AS TM_Application, SUM(PupukTR) AS TR_Application
FROM
ktv_farmer_view kcf
INNER JOIN
(SELECT
  FarmerID,
  MAX(SurveyNr) LatestSurveyNr
  FROM
  ktv_farmer_garden
  GROUP BY FarmerID) z ON kcf.FarmerID = z.FarmerID
INNER JOIN ktv_farmer_garden kcfg
ON z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr=kcfg.SurveyNr
%s
WHERE kcf.StatusCode ='active'
%s
GROUP BY label
         ";
        $this->environment_pesticide_baseline = "
SELECT
  IFNULL(%s, 'unknown') label,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida5 = 1, 1, 0)) AS gramoxone_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida5 = 1, 1, 0)) AS gramoxone_postline,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida9 = 1, 1, 0)) AS para_spesial_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida9 = 1, 1, 0)) AS para_spesial_postline,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida10 = 1, 1, 0)) AS noxone_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida10 = 1, 1, 0)) AS noxone_postline,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida11 = 1, 1, 0)) AS paratop_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida11 = 1, 1, 0)) AS paratop_postline,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida12 = 1, 1, 0)) AS bravoxone_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida12 = 1, 1, 0)) AS bravoxone_postline,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida13 = 1, 1, 0)) AS primaxone_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida13 = 1, 1, 0)) AS primaxone_postline,
  SUM(IF(kcfg.SurveyNr = 0 AND Herbisida18 = 1, 1, 0)) AS supertox_baseline,
  SUM(IF(kcfg.SurveyNr > 0 AND Herbisida18 = 1, 1, 0)) AS supertox_postline
FROM
  ktv_farmer_garden kcfg
INNER JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.FarmerID
%s
WHERE
kcf.StatusCode ='active'
AND GardenHaUnCertified IS NOT NULL
%s
GROUP BY label
         ";
        $this->environment_pesticide_latest = "
SELECT
  IFNULL(%s, 'unknown') label,
  SUM(IF(Herbisida5 = 1, 1, 0)) AS gramoxone,
  SUM(IF(Herbisida9 = 1, 1, 0)) AS para_spesial,
  SUM(IF(Herbisida10 = 1, 1, 0)) AS noxone,
  SUM(IF(Herbisida11 = 1, 1, 0)) AS paratop,
  SUM(IF(Herbisida12 = 1, 1, 0)) AS bravoxone,
  SUM(IF(Herbisida13 = 1, 1, 0)) AS primaxone,
  SUM(IF(Herbisida18 = 1, 1, 0)) AS supertox,
  SUM(IF(Herbisida14 = 1, 1, 0)) AS bimastar,
  SUM(IF(Herbisida15 = 1, 1, 0)) AS polado,
  SUM(IF(Herbisida16 = 1, 1, 0)) AS primastar,
  SUM(IF(Herbisida17 = 1, 1, 0)) AS rumat
FROM
  ktv_farmer_garden kcfg
INNER JOIN (
  SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
  FROM ktv_farmer_garden
  GROUP BY FarmerID, GardenNr
) z ON kcfg.FarmerID = z.FarmerID AND kcfg.GardenNr = z.GardenNr AND kcfg.SurveyNr = z.SurveyNr
INNER JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.FarmerID
%s
WHERE
kcf.StatusCode ='active'
AND GardenHaUnCertified IS NOT NULL
%s
GROUP BY label
         ";

        $this->sql_env_diversification = "
SELECT
  IFNULL(%s, 'unknown') label
  ,SUM(kcfg.GardenHaUncertified) TotalHa
  ,SUM(PohonTBM+PohonTM+PohonRehab) AS CacaoTrees
  ,SUM(KelapaNr) AS 'Nr_Coconut'
  ,SUM(PinangNr) AS 'Nr_Areca_Palm'
  ,SUM(KaretNr) AS 'Nr_Rubber'
  ,SUM(CengkehNr) AS 'Nr_Clove'
  ,SUM(SawitNr) AS 'Nr_Oil_Palm'
  ,SUM(ArenNr) AS 'Nr_Sugar_Palm'
  ,SUM(PalaNr) AS 'Nr_Nutmeg'
  ,SUM(KemiriNr) AS 'Nr_Hazelnut'
  ,SUM(MahoniNr) AS 'Nr_Mahagony'
  ,SUM(JatiNr) AS 'Nr_Teak'
  ,SUM(BitiNr) AS 'Nr_Vitex'
  ,SUM(UruNr) AS 'Nr_Elmerilla'
  ,SUM(JabonNr) AS 'Nr_Anthocephalus'
  ,SUM(JackFruitNr) AS 'Nr_Jackfruit'
  ,SUM(PisangNr) AS 'Nr_Banana'
  ,SUM(RambutanNr) AS 'Nr_Rambutan'
  ,SUM(ManggaNr) AS 'Nr_Mango'
  ,SUM(LangsatNr) AS 'Nr_Langsat'
  ,SUM(Duriannr) AS 'Nr_Durian'
  ,SUM(AlpukatNr) AS 'Nr_Avocado'
  ,SUM(SukunNr) AS 'Nr_Breadfruit'
  ,SUM(PepayaNr) AS 'Nr_Papaya'
  ,SUM(ManggisNr) AS 'Nr_Mangosteen'
  ,SUM(JerukNr) AS 'Nr_Citrus'
  ,SUM(GamalNr) AS 'Nr_Gliricidia'
  ,SUM(LamtoroNr) AS 'Nr_Leucaena'
  ,SUM(PetaiNr) AS 'Nr_Parkia'
  ,SUM(JengkolNr) AS 'Nr_Archidendron'
  ,SUM(ShadeLainNr) AS 'Nr_Other'
  ,SUM(ShadeTreesNr) AS 'Total_Nr_Diversification'
  ,SUM(KelapaNr + PinangNr + KaretNr + CengkehNr + SawitNr + ArenNr + PalaNr + KemiriNr + MahoniNr + JatiNr + BitiNr + UruNr + JabonNr
     + JackFruitNr + PisangNr + RambutanNr + ManggaNr + LangsatNr + Duriannr + AlpukatNr + SukunNr + PepayaNr + ManggisNr + JerukNr
     + GamalNr + LamtoroNr + PetaiNr + JengkolNr + ShadeLainNr) AS 'Check_Total_Nr'
FROM ktv_farmer_view kcf
JOIN (SELECT FarmerID,MAX(SurveyNr) survey FROM ktv_farmer_garden GROUP BY FarmerID) kcfgt ON
    kcfgt.FarmerID=kcf.FarmerID
LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID=kcfgt.FarmerID AND  survey=kcfg.SurveyNr
%s
WHERE 1 = 1 AND kcf.StatusCode = 'active'
%s
GROUP BY label
         ";

    $this->sql_agriinput = "
SELECT
    %s AS 'label'
    ,COUNT(kcfg.`FarmerID`) AS total
    ,SUM(GardenHaUnCertified) AS Hectare
    ,SUM(IF(PenyakitKanker = 1,1,0)) AS PenyakitKanker
    ,SUM(IF(PenyakitBusuk = 1,1,0)) AS PenyakitBusuk
    ,SUM(IF(PenyakitUpas = 1,1,0)) AS PenyakitUpas
    ,SUM(IF(PenyakitAkar = 1,1,0)) AS PenyakitAkar
    ,SUM(IF(PenyakitVSD = 1,1,0)) AS PenyakitVSD
    ,SUM(IF(PenyakitAntraknose = 1,1,0)) AS PenyakitAntraknose
    ,SUM(IF(HamaBPK = 1,1,0)) AS HamaBPK
    ,SUM(IF(HamaHelopeltis = 1,1,0)) AS HamaHelopeltis
    ,SUM(IF(HamaBatang = 1,1,0)) AS HamaBatang
    ,SUM(IF(Herbisida = 1 OR Insectisida = 1 OR Fungisida = 1,1,0)) AS Pestisida_yes
    ,SUM(IF(Herbisida = 2 AND Insectisida = 2 AND Fungisida = 2,1,0)) AS Pestisida_no
    ,SUM(IF((Insectisida = 1 AND Insectisida11 = 1) OR (Fungisida = 1 AND Fungisida11 = 1),1,0)) AS Pestisida_organic
    ,SUM(IF(Herbisida = 1,1,0)) AS Herbisida_yes
    ,SUM(IF(Herbisida = 2,1,0)) AS Herbisida_no
    ,SUM(IF(Insectisida = 1,1,0)) AS Insectisida_yes
    ,SUM(IF(Insectisida = 2,1,0)) AS Insectisida_no
    ,SUM(IF(Fungisida = 1,1,0)) AS Fungisida_yes
    ,SUM(IF(Fungisida = 2,1,0)) AS Fungisida_no
    ,SUM(IF(PakaiKompos = 1,1,0)) AS Organic_fertilizer_yes
    ,SUM(IF(PakaiKompos = 2,1,0)) AS Organic_fertilizer_no
    ,SUM(IF(TidakMemakaiKimia = 1,1,0)) AS Chemical_fertilizer_yes
    ,SUM(IF(TidakMemakaiKimia = 2,1,0)) AS Chemical_fertilizer_no
    ,SUM(IF(APD = 1,1,0)) AS Protective_equip_yes
    ,SUM(IF(APD = 2,1,0)) AS Protective_equip_no
    ,SUM(IF(TempatSimpanPestisida = 1,1,0)) AS TempatSimpanPestisida_rumah
    ,SUM(IF(TempatSimpanPestisida = 2,1,0)) AS TempatSimpanPestisida_khusus
    ,SUM(IF(TempatSimpanPestisida = 3,1,0)) AS TempatSimpanPestisida_luar
    ,SUM(IF(TempatSimpanPestisida = 4,1,0)) AS TempatSimpanPestisida_kebun
    ,SUM(IF(TempatSimpanPestisida = 5,1,0)) AS TempatSimpanPestisida_lain
    ,SUM(IF(BuangKemasanPestisida = 1,1,0)) AS BuangKemasanPestisida_kebun
    ,SUM(IF(BuangKemasanPestisida = 2,1,0)) AS BuangKemasanPestisida_gunakan
    ,SUM(IF(BuangKemasanPestisida = 3,1,0)) AS BuangKemasanPestisida_kubur
    ,SUM(IF(BuangKemasanPestisida = 4,1,0)) AS BuangKemasanPestisida_bakar
    ,SUM(IF(BuangKemasanPestisida = 5,1,0)) AS BuangKemasanPestisida_lain
    ,SUM(IF(Herbisida1=1 OR Herbisida2=1 OR Herbisida3=1 OR Herbisida4=1 OR Herbisida5=1 OR Herbisida6=1 OR Herbisida7=1 OR Herbisida8=1 OR Herbisida9=1 OR Herbisida10=1 OR Herbisida11=1 OR Herbisida12=1 OR Herbisida13=1 OR Herbisida14=1 OR Herbisida15=1 OR Herbisida16=1 OR Herbisida17=1 OR Herbisida18=1 OR Herbisida19=1 OR Herbisida20=1 OR Herbisida21=1 OR Herbisida22=1 OR Herbisida23=1 OR Herbisida24=1 OR Herbisida25=1 OR Herbisida26=1 OR Herbisida27=1 OR Herbisida28=1 OR Herbisida29=1,1,0)) AS herbicide_all
    ,SUM(IF(Herbisida5=1 OR Herbisida9=1 OR Herbisida10=1 OR Herbisida11=1 OR Herbisida12=1 OR Herbisida13=1 OR Herbisida18=1 OR Herbisida25=1 OR Herbisida26=1 OR Herbisida27=1 OR Herbisida28=1 OR Herbisida29=1, 1, 0)) AS herbicide_paraquat
    ,SUM(IF(Herbisida1=1 OR Herbisida2=1 OR Herbisida3=1 OR Herbisida4=1 OR Herbisida6=1 OR Herbisida7=1 OR Herbisida8=1 OR Herbisida14=1 OR Herbisida15=1 OR Herbisida16=1 OR Herbisida17=1 OR Herbisida19=1 OR Herbisida20=1 OR Herbisida21=1 OR Herbisida23=1 OR Herbisida24=1,1,0)) AS herbicide_glyphosate
    ,SUM(IF(Herbisida14=1 OR Herbisida15=1 OR Herbisida16=1 OR Herbisida21=1 OR Herbisida22=1,1,0)) AS herbicide_24d
    ,SUM(IF(Insectisida1=1 OR Insectisida2=1 OR Insectisida3=1 OR Insectisida4=1 OR Insectisida5=1 OR Insectisida6=1 OR Insectisida7=1 OR Insectisida8=1 OR Insectisida9=1 OR Insectisida10=1 OR Insectisida12=1 OR Insectisida13=1 OR Insectisida14=1 OR Insectisida15=1 OR Insectisida16=1 OR Insectisida17=1 OR Insectisida18=1 OR Insectisida19=1 OR Insectisida20=1 OR Insectisida11=1 OR Insectisida21=1 OR Insectisida22=1 OR Insectisida23=1 OR Fungisida9=1,1,0)) AS insecticide_all
    ,SUM(IF(Insectisida12=1 OR Insectisida20=1 OR Insectisida21=1 OR Insectisida22=1 OR Insectisida23=1,1,0)) AS insecticide_banned
    ,SUM(IF(Insectisida1=1 OR Insectisida2=1 OR Insectisida5=1 OR Insectisida6=1 OR Insectisida7=1 OR Insectisida8=1 OR Insectisida9=1 OR Insectisida10=1 OR Insectisida15=1 OR Insectisida19=1 OR Fungisida9=1,1,0)) AS insecticide_watchlist
    ,SUM(IF(Insectisida3=1 OR Insectisida4=1 OR Insectisida13=1 OR Insectisida14=1 OR Insectisida16=1 OR Insectisida17=1 OR Insectisida18=1 OR Insectisida11=1,1,0)) AS insecticide_allowed
    ,SUM(IF(Fungisida1=1 OR Fungisida2=1 OR Fungisida3=1 OR Fungisida4=1 OR Fungisida5=1 OR Fungisida6=1 OR Fungisida7=1 OR Fungisida10=1 OR Fungisida12=1 OR Fungisida11=1 OR Fungisida13=1,1,0)) AS fungicide_all
    ,SUM(IF(Fungisida13=1,1,0)) AS fungicide_banned
    ,SUM(IF(Fungisida2=1 OR Fungisida5=1 OR Fungisida6=1 OR Fungisida10=1,1,0)) AS fungicide_watchlist
    ,SUM(IF(Fungisida1=1 OR Fungisida3=1 OR Fungisida4=1 OR Fungisida7=1 OR Fungisida12=1 OR Fungisida11=1,1,0)) AS fungicide_allowed
    ,COUNT(DISTINCT IF(GAP_yes = 0 AND Insectisida =0 AND Fungisida = 0, kcfg.FarmerID , NULL)) AS Farmers_NOGAP_NOFung_Insecticide
    ,COUNT(DISTINCT IF(GAP_yes = 0 AND (Insectisida =1 OR Fungisida = 1), kcfg.FarmerID , NULL)) AS Farmers_NOGAP_Fung_Insecticide
    ,COUNT(DISTINCT IF(GAP_yes = 1 AND (Insectisida =0 OR Fungisida = 0), kcfg.FarmerID , NULL)) AS Farmers_GAP_NOFung_Insecticide
    ,COUNT(DISTINCT IF(GAP_yes = 1 AND (Insectisida =1 OR Fungisida = 1), kcfg.FarmerID , NULL)) AS Farmers_GAP_Fung_Insecticide
FROM (
    SELECT
        kcfg.*
        ,(CASE
            WHEN
                ((HowToCleanSkin = 2 OR HowToCleanSkin = 5
                    OR HowToCleanSkin = 6
                    OR HowToCleanSkin = 7)
                    AND (HarvestAwal = 1 AND HarvestHama = 1)
                    AND PruningPlants = 1)
            THEN
                1
            ELSE 0
        END) AS GAP_yes
    FROM `ktv_farmer_garden` kcfg
    JOIN (
        SELECT
            kcfg.`FarmerID`,
            kcfg.`GardenNr`,
            MAX(kcfg.`SurveyNr`) AS SurveyNr
        FROM
        `ktv_farmer_garden` kcfg
        GROUP BY
            kcfg.`FarmerID`,
            kcfg.`GardenNr`
    ) r ON kcfg.`FarmerID` = r.FarmerID AND kcfg.`GardenNr` = r.GardenNr AND kcfg.`SurveyNr` = r.SurveyNr
) kcfg
JOIN ktv_farmer_view kcf ON kcf.`FarmerID` = kcfg.`FarmerID`
%s
WHERE
    1 = 1 AND kcf.StatusCode = 'active'
    %s
GROUP BY label
    ";
        $this->bank_farmer = "SELECT
    %s AS label,
    SUM(IF(fin.LoanYesNo IS NULL OR fin.LoanYesNo = 2, 1,0)) AS no_loan
FROM ktv_farmer_view kcf
LEFT JOIN (
SELECT f.FarmerID, f.LoanYesNo FROM ktv_farmer_financial f
INNER JOIN (SELECT z.FarmerID, MAX(z.SurveyNr) SurveyNr FROM ktv_farmer_financial z GROUP BY z.FarmerID) z ON z.FarmerID = f.FarmerID
) fin ON fin.FarmerID = kcf.FarmerID
%s
WHERE 1 = 1 AND kcf.StatusCode = 'active'
%s
GROUP BY label
        ";
        $this->bank_loan = "SELECT
    %s AS label,
    SUM(IF(fs.ApprovalStatus = 1, 1,0)) AS approved,
    SUM(IF(fs.ApprovalStatus = 2, 1,0)) AS finished,
    SUM(IF(fs.ApprovalStatus = 1, 1,0)) AS rejected,
    SUM(LoanAmount) AS total_amount
FROM ktv_farmer_view kcf
INNER JOIN ktv_farmer_summary fs ON fs.FarmerID = kcf.FarmerID
%s
WHERE 1 = 1 AND kcf.StatusCode = 'active'
%s
GROUP BY label
";
        $this->bank_distance = "SELECT
    %s AS label,
    COUNT(FarmerID) AS farmer
FROM (
SELECT
    f.FarmerID, f.VillageID, f.SubDistrictID, f.SubDistrict, f.CPGid,
    MIN( 6371 * ACOS( COS( RADIANS(bb.BranchLatitude) ) * COS( RADIANS( g.Latitude) )
    * COS( RADIANS(g.Longitude) - RADIANS(bb.BranchLongitude)) + SIN(RADIANS(bb.BranchLatitude))
    * SIN( RADIANS(g.Latitude)))) AS distance,
    f.StatusCode
FROM ktv_farmer_garden g
JOIN (SELECT g.FarmerID, MAX(g.SurveyNr) SurveyNr FROM ktv_farmer_garden g WHERE g.GardenNr = 1 GROUP BY g.FarmerID) z ON z.FarmerID = g.FarmerID
JOIN ktv_farmer_view f ON f.FarmerID = g.FarmerID
JOIN ktv_bank_branch bb ON bb.BranchDistrictID = SUBSTR(f.VillageID,1,4)
GROUP BY FarmerID
HAVING distance < 10
) kcf
%s
WHERE 1 = 1 AND kcf.StatusCode = 'active'
%s
GROUP BY label
        ";
        $this->group_kelompok = "SELECT
 %s AS label,
 COUNT(kcf.`CPGid`) AS total,
 SUM(IF(kcf.`AdaPengurus` = 1,1,0)) AS ada_pengurus,
 SUM(IF(kcf.`AdaPengurus` != 1,1,0)) AS tidak_ada_pengurus,
 SUM(IF(kcf.`Ketua`,1,0)) AS ketua,
 SUM(IF(kcf.`Ketua` AND fa.`Gender` = 1,1,0)) AS ketua_m,
 SUM(IF(kcf.`Ketua` AND fa.`Gender` = 2,1,0)) AS ketua_f,
 SUM(IF(kcf.`Sekretaris`,1,0)) AS sekretaris,
 SUM(IF(kcf.`Sekretaris` AND fb.`Gender` = 1,1,0)) AS sekretaris_m,
 SUM(IF(kcf.`Sekretaris` AND fb.`Gender` = 2,1,0)) AS sekretaris_f,
 SUM(IF(kcf.`Bendahara`,1,0)) AS bendahara,
 SUM(IF(kcf.`Bendahara` AND fc.`Gender` = 1,1,0)) AS bendahara_m,
 SUM(IF(kcf.`Bendahara` AND fc.`Gender` = 2,1,0)) AS bendahara_f
FROM
(
SELECT
 kcf.*, sd.SubDistrict, sd.SubDistrictID
FROM ktv_cpg kcf
JOIN `ktv_cpg_batch_trainings` cbt ON cbt.`CPGid` = kcf.`CPGid` AND TrainingStart > 0
LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID=substr(kcf.VillageID,1,7)
GROUP BY kcf.`CPGid`
) kcf
  LEFT JOIN ktv_farmer fa ON fa.`FarmerID` = kcf.Ketua
  LEFT JOIN ktv_farmer fb ON fb.`FarmerID` = kcf.Sekretaris
  LEFT JOIN ktv_farmer fc ON fc.`FarmerID` = kcf.Bendahara
  %s
WHERE
  kcf.VillageID
  %s
GROUP BY label
ORDER BY label
        ";
        $this->group_kelamin = "SELECT %s label,sum(if(Gender='1',1,0)) male,sum(if(Gender='2',1,0)) female
            from ktv_cpg kc
            inner JOIN ktv_cpg_batch_trainings kcbt on kcbt.CPGid=kc.CPGid
            inner JOIN ktv_farmer_view kcf on kcf.FarmerID=kcbt.KeyFarmerID
            %s
            WHERE kcbt.CPGtrainingsID=1 AND kcf.StatusCode = 'active' %s
            GROUP BY label
            ORDER BY label
        ";
        $this->group_nurseries = "SELECT %s label,count(NurseryID) total
        from ktv_nursery kcn
        LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kcn.Responsible
        %s
        WHERE VillageID is not null AND kcf.StatusCode = 'active' %s
        GROUP BY label
        ORDER BY label";
        $this->group_seedling = "SELECT
        %s label,sum(Kapasitas*2) total
        from ktv_nursery kcn
        LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kcn.Responsible
        %s
        WHERE VillageID AND kcf.StatusCode = 'active'
        %s
        GROUP BY label
        ORDER BY label";
        $this->group_pemilik = "SELECT
    %s AS label,
    SUM(IF(ObjType = 'farmer',1,0)) AS 'farmer',
    SUM(IF(ObjType = 'cpg',1,0)) AS 'cpg',
    SUM(IF(ObjType = 'trader',1,0)) AS 'trader',
    SUM(IF(ObjType = 'koperasi',1,0)) AS 'koperasi',
    SUM(IF(ObjType = 'warehouse',1,0)) AS 'warehouse'
FROM (
SELECT
    ObjType,NurseryID,
    IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))) AS VillageID,
    v.SubDistrictID
FROM ktv_nursery kn
LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID=kn.ObjID AND ObjType='farmer'
LEFT JOIN ktv_cpg kc ON kc.CPGid=kn.ObjID AND ObjType='cpg'
LEFT JOIN ktv_traders kt ON kt.TraderID=kn.ObjID AND ObjType='trader'
LEFT JOIN ktv_cooperatives kp ON kp.CoopID=kn.ObjID AND ObjType='koperasi'
LEFT JOIN ktv_warehouse kw ON kw.WarehouseID=kn.ObjID AND ObjType='warehouse'
LEFT JOIN ktv_village v ON v.VillageID = IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,'')))))
WHERE ObjType IS NOT NULL
) kcf
%s
WHERE kcf.VillageID
%s
GROUP BY label";
        $this->group_kapasitas = "
            SELECT %s label,
               SUM(IF(ObjType='farmer',Kapasitas*2,0)) farmer,SUM(IF(ObjType='cpg',Kapasitas*2,0)) cpg,
               SUM(IF(ObjType='koperasi',Kapasitas*2,0)) koperasi,SUM(IF(ObjType='trader',Kapasitas*2,0)) trader,
               SUM(IF(ObjType='warehouse',Kapasitas*2,0)) warehouse
            FROM
            (SELECT
                ObjType,Kapasitas, kcf.StatusCode,
                IF(ObjType='farmer',kcf.VillageID,IF(ObjType='cpg',kc.VillageID,IF(ObjType='trader',kt.VillageID,
                IF(ObjType='koperasi',kp.VillageID,IF(ObjType='warehouse',kw.VillageID,NULL))))) VillageID,v.SubDistrictID
            FROM ktv_nursery kn
            LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=kn.ObjID AND ObjType='farmer'
            LEFT JOIN ktv_cpg kc ON kc.CPGid=kn.ObjID AND ObjType='cpg'
            LEFT JOIN ktv_traders kt ON kt.TraderID=kn.ObjID AND ObjType='trader'
            LEFT JOIN ktv_cooperatives kp ON kp.CoopID=kn.ObjID AND ObjType='koperasi'
            LEFT JOIN ktv_warehouse kw ON kw.WarehouseID=kn.ObjID AND ObjType='warehouse'
            LEFT JOIN ktv_village v ON v.VillageID=IF(ObjType='farmer',kcf.VillageID,IF(ObjType='cpg',kc.VillageID,IF(ObjType='trader',kt.VillageID,
                IF(ObjType='koperasi',kp.VillageID,IF(ObjType='warehouse',kw.VillageID,NULL)))))
            WHERE v.VillageID is not null
            ) kcf
            %s
            GROUP BY label
            order by label";
        $this->group_koperasi = "SELECT
  %s label,
  COUNT(DISTINCT kcf.`CoopID`) AS total,
  COUNT(DISTINCT IF(s.`StaffID`,kcf.`CoopID`,NULL)) AS have_management,
  COUNT(DISTINCT IF(s.`StaffID` IS NULL,kcf.`CoopID`,NULL)) AS dont_have_management,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas', 1, 0)) AS total_staff,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas' AND (s.`StaffGender` = 1), 1, 0)) AS total_staff_m,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas' AND (s.`StaffGender` = 2), 1, 0)) AS total_staff_f,
  SUM(IF(s.`Position` = 'Ketua' AND (s.`StaffGender` = 1), 1, 0)) ketua_m,
  SUM(IF(s.`Position` = 'Ketua' AND (s.`StaffGender` = 2), 1, 0)) ketua_f,
  SUM(IF(s.`Position` = 'Wakil Ketua' AND (s.`StaffGender` = 1), 1, 0)) wakil_ketua_m,
  SUM(IF(s.`Position` = 'Wakil Ketua' AND (s.`StaffGender` = 2), 1, 0)) wakil_ketua_f,
  SUM(IF(s.`Position` = 'Sekretaris' AND (s.`StaffGender` = 1), 1, 0)) sekretaris_m,
  SUM(IF(s.`Position` = 'Sekretaris' AND (s.`StaffGender` = 2), 1, 0)) sekretaris_f,
  SUM(IF(s.`Position` = 'Wakil Sekretaris' AND (s.`StaffGender` = 1), 1, 0)) wakil_sekretaris_m,
  SUM(IF(s.`Position` = 'Wakil Sekretaris' AND (s.`StaffGender` = 2), 1, 0)) wakil_sekretaris_f,
  SUM(IF(s.`Position` = 'Bendahara' AND (s.`StaffGender` = 1), 1, 0)) bendahara_m,
  SUM(IF(s.`Position` = 'Bendahara' AND (s.`StaffGender` = 2), 1, 0)) bendahara_f,
  SUM(IF(s.`Position` = 'Wakil Bendahara' AND (s.`StaffGender` = 1), 1, 0)) wakil_bendahara_m,
  SUM(IF(s.`Position` = 'Wakil Bendahara' AND (s.`StaffGender` = 2), 1, 0)) wakil_bendahara_f
FROM `ktv_cooperatives` kcf
LEFT JOIN `ktv_cooperative_staff` s ON s.`CoopID` = kcf.`CoopID`
%s
WHERE 1=1
%s
GROUP BY label
ORDER BY label
          ";
        $this->group_trader = "
select    COUNT(kcf.TraderID) AS total,
   SUM(IF(`Sex`=1,1,0)) AS male,
   SUM(IF(`Sex`=2,1,0)) AS female,
   kcf.VillageID,kcf.SubDistrictID,%s AS label
from (
SELECT TraderID,Sex,kcf.VillageID,SubDistrictID
FROM ktv_traders kcf
LEFT JOIN ktv_village v ON v.VillageID = kcf.VillageID
) kcf
%s
            WHERE kcf.VillageID
            %s
            GROUP BY label
            ORDER BY label";
        $this->group_established_cpg = "SELECT
   %s label
   ,COUNT(DISTINCT kcf.CPGid) total_cpg
   ,MIN(`year`) AS min_year
   ,MAX(`year`) AS max_year
   ,SUM(IF(kcf.`year`=2010,1,0)) '2010'
   ,SUM(IF(kcf.`year`=2011,1,0)) '2011'
   ,SUM(IF(kcf.`year`=2012,1,0)) '2012'
   ,SUM(IF(kcf.`year`=2013,1,0)) '2013'
   ,SUM(IF(kcf.`year`=2014,1,0)) '2014'
   ,SUM(IF(kcf.`year`=2015,1,0)) '2015'
   ,SUM(IF(kcf.`year`=2016,1,0)) '2016'
FROM (
SELECT
  cpg.`CPGid`,
  COUNT(DISTINCT CpgBatchTrainingID) AS total_training,
  VillageID,
  YEAR(MIN(bt.`TrainingStart`)) AS `year`
FROM `ktv_cpg_batch_trainings` bt
LEFT JOIN `ktv_cpg` cpg ON bt.`CPGid` = cpg.`CPGid`
WHERE VillageID AND bt.`TrainingStart` > 0
GROUP BY cpg.`CPGid`
ORDER BY CpgBatchTrainingID ASC
) kcf
LEFT JOIN ktv_village v ON v.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
%s
WHERE kcf.VillageID
%s
GROUP BY label
";
        $this->nutrition_household = "SELECT %s AS label,COUNT(DISTINCT kcf.FarmerID) as total
            FROM
               (SELECT kcf.FarmerID, kcf.VillageID, kcf.CPGid, kcf.SubDistrict, kcf.SubDistrictID
               FROM
               ktv_cpg_batch_trainings kcbt
               INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID = kcbtf.CpgBatchTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kcbtf.FarmerID = kcf.FarmerID
               WHERE kcbt.CPGtrainingsID=2 AND kcf.StatusCode = 'active'
               UNION ALL
               SELECT kcf.FarmerID, kcf.VillageID, kcf.CPGid, kcf.SubDistrict, kcf.SubDistrictID
               FROM
               ktv_kader_trainings kkt
               INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kktp.FarmerID = kcf.FarmerID
               WHERE kkt.CPGtrainingsID=2 AND kcf.StatusCode = 'active'
               ) kcf
            %s
            WHERE kcf.VillageID
            %s
            GROUP BY label
            order by label";
        $this->nutrition_kelamin = "SELECT %s as label,sum(IF(kelamin=1,1,0)) male,sum(IF(kelamin=2,1,0)) female,
               sum(IF((kelamin not in (1,2) || kelamin is null),1,0)) other
            FROM
               (SELECT kcf.FarmerID, kcf.VillageID, kcf.CPGid, kcf.SubDistrict, kcf.SubDistrictID,
                  if(kcbtf.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
               FROM
               ktv_cpg_batch_trainings kcbt
               INNER JOIN ktv_cpg_batch_trainings_farmers kcbtf ON kcbt.CpgBatchTrainingID = kcbtf.CpgBatchTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kcbtf.FarmerID = kcf.FarmerID
               LEFT JOIN ktv_family kf ON kcbtf.FamilyID = kf.FamilyID
               WHERE kcbt.CPGtrainingsID=2 AND kcf.StatusCode = 'active'
               UNION ALL
               SELECT kcf.FarmerID, kcf.VillageID, kcf.CPGid, kcf.SubDistrict, kcf.SubDistrictID,
                  if(kktp.PetaniKakao=1,kcf.Gender,kf.AnggotaGender) kelamin
               FROM
               ktv_kader_trainings kkt
               INNER JOIN ktv_kader_trainings_participants kktp ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
               LEFT JOIN ktv_farmer_view kcf ON kktp.FarmerID = kcf.FarmerID
               LEFT JOIN ktv_family kf ON kktp.FamilyID = kf.FamilyID
               WHERE kkt.CPGtrainingsID=2 AND kcf.StatusCode = 'active'
               ) kcf
            %s
            WHERE kcf.VillageID
            %s
            GROUP BY label";
        $this->nutrition_idds = "SELECT * FROM (
            SELECT %s label,round(sum(kn.Score)/count(knb.FarmerID),2) total,sum(kn.Score) sc,count(knb.FarmerID) fa, SUM(IF(kcf.Gender='1',kn.Score,0)) score_male, SUM(IF(kcf.Gender='2',kn.Score,0)) score_female,SUM(IF(kcf.Gender='1',1,0)) male, SUM(IF(kcf.Gender='2',1,0)) female
            from ktv_nutrition kn
            inner JOIN (SELECT FarmerID,max(SurveyNr) LastSurveyNr FROM ktv_nutrition GROUP BY FarmerID) knb on
               kn.FarmerID=knb.FarmerID and kn.SurveyNr=knb.LastSurveyNr
            LEFT JOIN ktv_farmer_view kcf ON knb.FarmerID = kcf.FarmerID
            %s
            WHERE 1 = 1
            -- AND substr(kcf.VillageID,1,4) not in (7317,7322,7325)
            and (kn.Score>0 AND kn.Score<10) AND kcf.StatusCode = 'active'
            %s
            GROUP BY label
            ) a WHERE total>0 AND a.label IS NOT NULL
            order by label";
        $this->nutrition_size = "SELECT * FROM (
            SELECT %s label,round(sum(kn.KebunPanjang*kn.KebunLebar)/count(knb.FarmerID),1) total,
               sum(kn.KebunPanjang*kn.KebunLebar) luas,count(knb.FarmerID) fa
            from ktv_nutrition kn
            inner JOIN (SELECT FarmerID,max(SurveyNr) LastSurveyNr FROM ktv_nutrition GROUP BY FarmerID) knb on
               kn.FarmerID=knb.FarmerID and kn.SurveyNr=knb.LastSurveyNr
            LEFT JOIN ktv_farmer_view kcf ON knb.FarmerID = kcf.FarmerID
            %s
            WHERE 1 = 1
            AND kn.KebunPanjang*kn.KebunLebar<100
            -- and substr(kcf.VillageID,1,4) not in (7317,7322,7325)
            AND kcf.StatusCode = 'active'
            %s
            GROUP BY label
            ) a WHERE total>0 AND a.label IS NOT NULL
            order by label";
        $this->nutrition_lifestock = "SELECT
    %s AS label,
    COUNT(DISTINCT ktv_nutrition.FarmerID) AS Farmers,
    AVG(CASE WHEN Score > 0 AND Score < 10 THEN Score END) AS IDDS,
    SUM(CASE
        WHEN KebunPanjang > 0 THEN 1
        ELSE 0
    END) AS GardenYes,
    AVG(CASE
        WHEN KebunPanjang * KebunLebar <= 100
 THEN KebunPanjang * KebunLebar
    END) AS avgGardenSizeMod,
    SUM((CASE
        WHEN KebunPanjang * KebunLebar <= 100
 THEN KebunPanjang * KebunLebar
        WHEN KebunPanjang IS NULL THEN 0
        ELSE NULL
    END) ) / COUNT(DISTINCT kcf.FarmerID) AS GardenSizeMod_Farmer,
    SUM(KbBayam) AS Spinach, SUM(`KbCabai`) AS Chilli, SUM(`KbKacangPanjang`) AS LongBean, SUM(`KbKangkung`) AS WaterCress, SUM(`KbSawi`) AS Mustard,
    SUM(`KbTerong`) AS Eggplant, SUM(`KbTomat`) AS Tomato, SUM(`KbKambing`) AS Goat, SUM(`KbSapi`) AS Cow, SUM(`KbBebek`) AS Duck,
    SUM(`KbAyam`) AS Chicken, SUM(`KbIkan`) AS Fish
FROM
    ktv_nutrition
INNER JOIN ktv_farmer_view kcf ON kcf.farmerID = ktv_nutrition.FarmerID AND kcf.VillageID
%s
WHERE
    Score IS NOT NULL AND SurveyNr = 0 AND kcf.VillageID IS NOT NULL AND kcf.StatusCode = 'active'
    %s
GROUP BY label
         ";
   //dashboard
      $this->dash_main = "
         SELECT COUNT(CPGid) cpg,SUM(CocoaFarmers) farmer,SUM(SumAge)/(SUM(CocoaFarmers)-SUM(SumUnage)) usia,
            SUM(FemaleFarmers)/(SUM(FemaleFarmers)+SUM(MaleFarmers)) perempuan,
         	SUM(SumFarm) kebun,SUM(CocoaFarmArea)/SUM(CountFarmerArea) rerata_ha,SUM(CocoaTree) pohon,SUM(CocoaFarmArea) luas,
         	SUM(Production) produksi,SUM(Production)/SUM(CocoaFarmArea) produktifity,SUM(Production)/SUM(TMCocoaTree) produktivitas_pohon,
         	SUM(CertifiedCocoa) petani_sertifikasi,SUM(CertifiedFarmArea) luas_sertifikasi,SUM(CertifiedProduction) produksi_sertifikasi,
         	SUM(GNPParticipants) gnp,SUM(GFPParticipants) gfp,
         	sum(small) small,sum(`medium`) `medium`,sum(`large`) `large`,
	         sum(unprofessional) unprofessional,sum(`progressing`) `progressing`,sum(`professional`) `professional`
         FROM dash_main
         WHERE 1=1 %s";
     $this->demographic_satu = "
         SELECT SUM(Farmer) farmer,SUM(Female)/(SUM(Female)+SUM(Male))*100 female_persen,
            SUM(Age)/(SUM(Farmer)-SUM(Unage)) age_avg,SUM(PassPrimarySchool)/(SUM(PassPrimarySchool)+SUM(NotPassPrimarySchool))*100  cps_persen,
         	COUNT(DISTINCT ProvinceID) province,COUNT(DISTINCT DistrictID) district,
            COUNT(DISTINCT SubDistrictID) subdistrict,COUNT(DISTINCT VillageID) village,
      		SUM(Below125)/SUM(PpiFarmer) sdl,SUM(Below25)/SUM(PpiFarmer) dl,SUM(Young)/(SUM(Farmer)-SUM(Unage))*100 young,
            SUM(Household)/SUM(Farmer) household,
            SUM(Age1524) Age1524, SUM(Age2534) Age2534, SUM(Age3544) Age3544, SUM(Age4554) Age4554, SUM(Age55) Age55,
            SUM(NotSchool) NotSchool,SUM(PrimarySchoolIncomplete) PrimarySchoolIncomplete,
            SUM(PrimarySchoolcompleted) PrimarySchoolcompleted,SUM(JuniorHighSchool) JuniorHighSchool,
            SUM(SeniorHighSchool) SeniorHighSchool,SUM(TertiarySchool) TertiarySchool,
            sum(Family1) hh1,SUM(Family2) hh2,SUM(Family3) hh3,SUM(Family4) hh4,SUM(Family5) hh5,SUM(Family6) hh6,
            sum(Nasional)
         FROM dash_demographic a
         WHERE 1=1 %s";
     $this->groups_box = "
         SELECT COUNT(DISTINCT cpg) cpg,
         	(COUNT(DISTINCT CONCAT(ketua_f,cpg))+COUNT(DISTINCT CONCAT(sekretaris_f,cpg))+COUNT(DISTINCT CONCAT(bendahara_f,cpg)))/
         	((COUNT(DISTINCT CONCAT(ketua_f,cpg))+COUNT(DISTINCT CONCAT(sekretaris_f,cpg))+COUNT(DISTINCT CONCAT(bendahara_f,cpg)))+
         	(COUNT(DISTINCT CONCAT(ketua_m,cpg))+COUNT(DISTINCT CONCAT(sekretaris_m,cpg))+COUNT(DISTINCT CONCAT(bendahara_m,cpg))))*100 cpg_female,
         	count(distinct coop_id) cooperation,
         	(SUM(coop_ketua_f)+SUM(coop_sekretaris_f)+SUM(coop_bendahara_f)+
         	SUM(coop_wakil_ketua_f)+SUM(coop_wakil_sekretaris_f)+SUM(coop_wakil_bendahara_f))/
         	(sum(coop_ketua_m)+SUM(coop_sekretaris_m)+SUM(coop_bendahara_m)+SUM(coop_ketua_f)+SUM(coop_sekretaris_f)+SUM(coop_bendahara_f)+
         	SUM(coop_wakil_ketua_m)+SUM(coop_wakil_sekretaris_m)+SUM(coop_wakil_bendahara_m)+SUM(coop_wakil_ketua_f)+
         	SUM(coop_wakil_sekretaris_f)+SUM(coop_wakil_bendahara_f))*100 coop_female,
         	sum(pembibitan) pembibitan,sum(kapasitas) kapasitas,
         	COUNT(DISTINCT CONCAT(ada_pengurus,cpg)) ada_pengurus,COUNT(DISTINCT CONCAT(tidak_ada_pengurus,cpg)) tidak_ada_pengurus,
         	COUNT(DISTINCT CONCAT(ketua_f,cpg))ketua_f,COUNT(DISTINCT CONCAT(sekretaris_f,cpg))sekretaris_f,
         	COUNT(DISTINCT CONCAT(bendahara_f,cpg))bendahara_f,
         	COUNT(DISTINCT CONCAT(ketua_m,cpg))ketua_m,COUNT(DISTINCT CONCAT(sekretaris_m,cpg))sekretaris_m,
         	COUNT(DISTINCT CONCAT(bendahara_m,cpg))bendahara_m,
         	SUM(coop_ketua_f)coop_ketua_f,SUM(coop_sekretaris_f)coop_sekretaris_f,SUM(coop_bendahara_f)coop_bendahara_f,
         	SUM(coop_wakil_ketua_f)coop_wakil_ketua_f,SUM(coop_wakil_sekretaris_f)coop_wakil_sekretaris_f,
            SUM(coop_wakil_bendahara_f)coop_wakil_bendahara_f,
         	SUM(coop_ketua_m)coop_ketua_m,SUM(coop_sekretaris_m)coop_sekretaris_m,SUM(coop_bendahara_m)coop_bendahara_m,
         	SUM(coop_wakil_ketua_m)coop_wakil_ketua_m,SUM(coop_wakil_sekretaris_m)coop_wakil_sekretaris_m,
            SUM(coop_wakil_bendahara_m)coop_wakil_bendahara_m
          FROM dash_group kcf
          WHERE 1=1 %s";
     $this->group = "
         SELECT %s label,COUNT(DISTINCT cpg) cpg,SUM(pembibitan) pembibitan,SUM(kapasitas) kapasitas
            ,count(distinct IF(`year`=year(now())-6,cpg,null)) t1
            ,count(distinct IF(`year`=year(now())-5,cpg,null)) t2
            ,count(distinct IF(`year`=year(now())-4,cpg,null)) t3
            ,count(distinct IF(`year`=year(now())-3,cpg,null)) t4
            ,count(distinct IF(`year`=year(now())-2,cpg,null)) t5
            ,count(distinct IF(`year`=year(now())-1,cpg,null)) t6
            ,count(distinct IF(`year`=year(now()),cpg,null)) t7
         FROM dash_group kcf
         %s
         WHERE cpg is not null %s
         GROUP BY label
         ORDER BY label";
     $this->demographic_dua = "
         SELECT %s label,   SUM(Farmer) Farmer, SUM(Female) female,SUM(Male) male,SUM(Age)/(SUM(Farmer)) age,
         	SUM(Nasional)/SUM(NasionalCount) Nasional,SUM(Below125)/SUM(NasionalCount) sdl,SUM(Below25)/SUM(NasionalCount) dl,
         	SUM(Household)/SUM(Farmer) hh,SUM(Firewood) Firewood,SUM(GasOther) GasOther,
         	SUM(RefrigeratorYes) RefrigeratorYes,SUM(RefrigeratorNo) RefrigeratorNo,
         	SUM(MotorcycleYes) MotorcycleYes,SUM(MotorcycleNo) MotorcycleNo,
            sum(Family1) hh1,SUM(Family2) hh2,SUM(Family3) hh3,SUM(Family4) hh4,SUM(Family5) hh5,SUM(Family6) hh6
         FROM dash_demographic a
         %s
         WHERE Farmer>0 %s
         GROUP BY %s
         ORDER BY label";
     $this->garden_all = "
         SELECT
            SUM(Garden) garden,SUM(FarmArea) area,SUM(Production)/1000 produksi,
               SUM(`Medium`+Large)/SUM(Marginal+Micro+Small+`Medium`+Large)*100 kebun1,
         	SUM(FarmArea)/SUM(FarmerHaveArea) rerata,SUM(CocoaTree)tanaman_cacao, SUM(OtherTree)tanaman_lain,
               SUM(RehabTree) tanaman_rusak,
         	SUM(CocoaTree)/SUM(FarmArea) rerata_hektar,SUM(Production)/SUM(FarmArea) produktifitas,
               SUM(Production)/SUM(PohonTM) produktifitas_pohon,SUM(AgeTree)/SUM(CountAgeTree) rerata_umur,

         	SUM(Yield500)Yield500,SUM(Yield1000)Yield1000,SUM(Yield2000)Yield2000,SUM(YieldAbove2000)YieldAbove2000,
         	SUM(Marginal)Marginal,SUM(Micro)Micro,SUM(Small)Small,SUM(`Medium`)`Medium`,SUM(Large)Large,
         	SUM(PohonTBM)PohonTBM,SUM(PohonTM)PohonTM,SUM(RehabTree)RehabTree,SUM(PohonLain)PohonLain,
         	SUM(`Owner`)`Owner`,SUM(CropShare)CropShare,SUM(Rent)Rent,SUM(Other)Other,
         	SUM(NoLandCertificate)NoLandCertificate,SUM(NotarialDeepBpn)NotarialDeepBpn,SUM(SkktCamat)SkktCamat,
               SUM(VillageLurah)VillageLurah,
         	SUM(FarmerHimHerself)FarmerHimHerself,SUM(FamilyMember)FamilyMember,SUM(OtherPerson)OtherPerson,
               SUM(DoNotKnow)DoNotKnow
         FROM dash_farm kcf
         %s
         WHERE 1=1 %s";
     $this->garden_group = "
         SELECT %s label,
            SUM(Garden) kebun,SUM(FarmArea) luas_kebun,SUM(Production) produksi,SUM(Production)/SUM(FarmArea) produktifitas,
         	SUM(Production)/SUM(PohonTM) produktifitas_menghasilkan,SUM(FarmArea)/SUM(FarmerHaveArea) rerata_ukuran,
         	SUM(CocoaTree)/SUM(FarmArea) rerata_pohon,SUM(AgeTree)/SUM(CountAgeTree) rerata_umur
         FROM dash_farm kcf
         %s
         WHERE 1=1 %s
         GROUP BY %s
         ORDER BY label";
    }

    function readData($prov = '', $kab = '') {
        if ($prov == '') $where = '';
        elseif ($kab == '') $where = 'and substr(VillageID,1,2)=?';
        else $where = 'and substr(VillageID,1,4)=?';
        if ($kab != '') $prov = $kab;

        $query_main = $this->db->query(sprintf($this->dash_main, $where), array($prov));
        $main = $query_main->result_array();
        $results['ketiga'][1]['total']    = $main[0]['kebun'];
        $results['ketiga'][2]['total']    = $main[0]['produktivitas_pohon'];
        $results['ketiga'][3]['total']    = $main[0]['petani_sertifikasi'];
        $results['ketiga'][4]['total']    = $main[0]['produksi_sertifikasi'];
        $results['ketiga'][5]['total']    = $main[0]['produktifity'];
        $results['cpg'][0]['total']       = $main[0]['cpg'];
        $results['farmer'][0]['total']    = $main[0]['farmer'];
        $results['luas'][0]['total']      = $main[0]['luas'];
        $results['pohon'][0]['total']     = $main[0]['pohon'];
        $results['total'][0]['total']     = $main[0]['produksi'];
        $results['training'][0]['total']  = $main[0]['gnp'];
        $results['usia'][0]['total']      = $main[0]['usia'];
        $results['ukuran'][0]['total']    = $main[0]['rerata_ha'];
        $results['perempuan'][0]['total'] = $main[0]['perempuan'];
        $results['ha'][0]['total']        = $main[0]['luas_sertifikasi'];
        $results['gfp'][0]['gfp']         = $main[0]['gfp'];
        $results['classification'][0]['small']  =  $main[0]['small'];
        $results['classification'][0]['medium']  =  $main[0]['medium'];
        $results['classification'][0]['large']  =  $main[0]['large'];
        $results['classification'][0]['unprofessional']  =  $main[0]['unprofessional'];
        $results['classification'][0]['progressing']  =  $main[0]['progressing'];
        $results['classification'][0]['professional']  =  $main[0]['professional'];
        return $results;
    }
    function readDataDistrict($user, $district, $priv = '', $partner = '', $prov = '') {
        $where = '';
        if ( ! empty($partner)) {
            $cpgs = $this->get_cpgs($partner);
            if ( ! empty($cpgs)) {
                $where_cpg .= " AND `CPGid` IN (" . $cpgs . ")";
                $where .= $where_cpg;
            }
        }
        if ($prov != '') {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            $group = 'GROUP BY substr(VillageID,1,4)';
        }
        if ($priv == '') {
            $where .= ' and substr(VillageID,1,4) in (%s)';
            $group = 'GROUP BY SubDistrictID';
        } else {
            $where .= ' and substr(VillageID,1,4)=?';
            $group = 'GROUP BY SubDistrictID';
        }
        $dis = explode(',', $district);
        for ($i = 0; $i < sizeof($dis); $i++) {
            $di = explode('##', $dis[$i]);
            $dist[] = $di[0];
        }
        $query_main = $this->db->query(sprintf(sprintf($this->dash_main, $where), implode(',', $dist)), array($priv));
        $main = $query_main->result_array();

        $results['ketiga'][1]['total']    = $main[0]['kebun'];
        $results['ketiga'][2]['total']    = $main[0]['produktivitas_pohon'];
        $results['ketiga'][3]['total']    = $main[0]['petani_sertifikasi'];
        $results['ketiga'][4]['total']    = $main[0]['produksi_sertifikasi'];
        $results['ketiga'][5]['total']    = $main[0]['produktifity'];
        $results['cpg'][0]['total']       = $main[0]['cpg'];
        $results['farmer'][0]['total']    = $main[0]['farmer'];
        $results['luas'][0]['total']      = $main[0]['luas'];
        $results['pohon'][0]['total']     = $main[0]['pohon'];
        $results['total'][0]['total']     = $main[0]['produksi'];
        $results['training'][0]['total']  = $main[0]['gnp'];
        $results['usia'][0]['total']      = $main[0]['usia'];
        $results['ukuran'][0]['total']    = $main[0]['rerata_ha'];
        $results['perempuan'][0]['total'] = $main[0]['perempuan'];
        $results['ha'][0]['total']        = $main[0]['luas_sertifikasi'];
        $results['gfp'][0]['gfp']       = $main[0]['gfp'];
        $results['classification'][0]['small']  =  $main[0]['small'];
        $results['classification'][0]['medium']  =  $main[0]['medium'];
        $results['classification'][0]['large']  =  $main[0]['large'];
        $results['classification'][0]['unprofessional']  =  $main[0]['unprofessional'];
        $results['classification'][0]['progressing']  =  $main[0]['progressing'];
        $results['classification'][0]['professional']  =  $main[0]['professional'];
        return $results;
    }
    function readDataDemographicNew($prov = '', $kab = '', $petani = '', $tahun = '') {
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' JOIN ktv_province z on z.ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' JOIN ktv_district z on z.DistrictID=substr(VillageID,1,4)';
            $where .= ' and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            $LEFT .= ' JOIN ktv_subdistrict z on z.SubDistrictID=a.SubDistrictID';
            $where .= ' and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }

        if ($kab != '') $prov = $kab;
        $query_satu              = $this->db->query(sprintf($this->demographic_satu, $where), array($prov));
        $results['data']         = $query_satu->result_array();
        $query_kedua             = $this->db->query(sprintf($this->demographic_dua, $label,$LEFT, $where,$groupby), array($prov));
        $results['kedua']        = $query_kedua->result_array();

        return $results;
    }
    function readDataDistrictDemographicNew($user, $district, $priv = '', $petani = '', $partner = '', $prov = '') {
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province b on b.ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district b on b.DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_subdistrict b on a.SubDistrictID=b.SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'a.SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }
        $query_satu              = $this->db->query(sprintf(sprintf($this->demographic_satu,$where),
            implode(',', $dist)), array($priv));
        $results['data']         = $query_satu->result_array();
        $query_kedua             = $this->db->query(sprintf(sprintf($this->demographic_dua, $label,$LEFT, $where,$groupby),
            implode(',', $dist)), array($priv));
        $results['kedua']        = $query_kedua->result_array();

        return $results;
    }

    function readDataGroupsNew($prov = '', $kab = ''){
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' JOIN ktv_province b on b.ProvinceID=substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
            $where .= ' and substr(kcf.VillageID,1,2)=?';
        } else {
            $label = 'b.SubDistrict';
            $LEFT .= ' JOIN ktv_subdistrict b on kcf.SubDistrictID=b.SubDistrictID';
            $where = ' and substr(kcf.VillageID,1,4)=?';
        }
        if ($kab != '') $prov = $kab;
        $box               = $this->db->query(sprintf($this->groups_box, $where), array($prov));
        $results['box']    = $box->result_array();
        $group               = $this->db->query(sprintf($this->group, $label, $LEFT, $where), array($prov));
        $results['group']    = $group->result_array();
        $query_trader        = $this->db->query(sprintf($this->group_trader, $label, $LEFT, $where), array($prov));
        $results['trader']   = $query_trader->result_array();
        $query_koperasi       = $this->db->query(sprintf($this->group_koperasi, $label, $LEFT, $where), array($prov));
        $results['koperasi']  = $query_koperasi->result_array();
        $query_pemilik        = $this->db->query(sprintf($this->group_pemilik, $label, $LEFT, $where), array($prov));
        $results['pemilik']   = $query_pemilik->result_array();
        $query_kapasitas        = $this->db->query(sprintf($this->group_kapasitas, $label,$LEFT,$where), array($prov));
        $results['kapasitas']   = $query_kapasitas->result_array();

        return $results;
    }

    function readDataDistrictGroups($user, $district, $priv = '', $partner = '', $prov = ''){
        $where = '';
        $cpgs = '';
        if ( ! empty($partner)) {
            $cpgs = $this->get_cpgs($partner);
            if ( ! empty($cpgs)) {
                $where .= " AND kcf.`CPGid` IN (" . $cpgs . ")";
            }
        }
        if ($prov != '') {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
        }
        $dis = explode(',', $district);
        for ($i = 0; $i < sizeof($dis); $i++) {
            $di = explode('##', $dis[$i]);
            $dist[] = $di[0];
        }

        $where = '';
        $LEFT = '';
        $where .= ' and substr(kcf.VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province b on b.ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } else {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
                $groupby = 'substr(kcf.VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                 $LEFT .= ' LEFT JOIN ktv_subdistrict b on b.SubDistrictID=kcf.SubDistrictID';
                $where .= ' and substr(kcf.VillageID,1,4)=?';
                $groupby = 'kcf.SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where_cpg = " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
            $where .= $where_cpg;
        }
        $box               = $this->db->query(sprintf(sprintf($this->groups_box, $where),implode(',', $dist)), array($priv));
        $results['box']    = $box->result_array();
        $group               = $this->db->query(sprintf(sprintf($this->group, $label, $LEFT, $where,$groupby),
            implode(',', $dist)), array($priv));
        $results['group']    = $group->result_array();
        $query_trader        = $this->db->query(sprintf(sprintf($this->group_trader, $label, $LEFT, $where,$groupby),
            implode(',', $dist)), array($priv));
        $results['trader']   = $query_trader->result_array();
        $query_pemilik              = $this->db->query(sprintf(sprintf($this->group_pemilik, $label, $LEFT, $where,$groupby),
            implode(',', $dist)), array($priv));
        $results['pemilik']   = $query_pemilik->result_array();
        $query_kapasitas        = $this->db->query(sprintf(sprintf($this->group_kapasitas, $label,$LEFT,$where,$groupby),
            implode(',', $dist)), array($priv));
        $results['kapasitas']   = $query_kapasitas->result_array();

        return $results;
    }

    function readDataGarden($prov = '', $kab = '', $petani = '', $tahun = '', $survey = ''){
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        switch ($survey) {
          case '0':
            $where .= " AND Survey = 'baseline'";
            break;
          case '1':
            $where .= " AND Survey = 'postline'";
            break;
          case '2':
            $where .= " AND Survey = 'latest'";
            break;
        }
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province kd on kd.ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' LEFT JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
            $where .= " and substr(kcf.VillageID,1,2)=?";
            $groupby = 'substr(kcf.VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            $LEFT .= ' LEFT JOIN ktv_subdistrict kd on kd.SubDistrictID=kcf.SubDistrictID';
            $where .= " and substr(kcf.VillageID,1,4)=?";
            $groupby = 'kcf.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query             = $this->db->query(sprintf($this->garden_all,$LEFT,$where), array($prov));
        $results['all']    = $query->result_array();
        $query_group       = $this->db->query(sprintf($this->garden_group,$label,$LEFT,$where,$groupby), array($prov));
        $results['group']  = $query_group->result_array();
        return $results;
    }

    function readDataDistrictGarden($user, $district, $priv = '', $petani = '', $partner = '', $prov = '', $tahun = '', $survey = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        switch ($survey) {
          case '0':
            $where .= " AND Survey = 'baseline'";
            break;
          case '1':
            $where .= " AND Survey = 'postline'";
            break;
          case '2':
            $where .= " AND Survey = 'latest'";
            break;
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } else {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
                $groupby = 'substr(kcf.VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_subdistrict ks on ks.SubDistrictID=kcf.SubDistrictID';
                $where .= ' and substr(kcf.VillageID,1,4)=?';
                $groupby = 'kcf.SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query             = $this->db->query(sprintf($this->garden_all,$LEFT,$where), array($prov));
        $results['all']    = $query->result_array();
        $query_group       = $this->db->query(sprintf($this->garden_group,$label,$LEFT,$where,$groupby), array($prov));
        $results['group']  = $query_group->result_array();
        return $results;
    }
    function readDataAgriinput($prov = '', $kab = '') {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'JOIN ktv_province kp ON kp.ProvinceID = substr(kcf.VillageID,1,2)';
            $where = '';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'JOIN ktv_district kp ON kp.DistrictID = substr(kcf.VillageID,1,4)';
            $where = 'and substr(kcf.VillageID,1,2)=?';
            $groupby = 'substr(kcf.VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            $LEFT = 'JOIN ktv_subdistrict kp ON kp.SubDistrictID = kcf.SubDistrictID';
            $where = 'and substr(kcf.VillageID,1,4)=?';
            $groupby = 'kcf.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $this->agri_all = "
            SELECT
            	SUM(Kompos)/SUM(Hectare) kh,SUM(Fertilizer)/SUM(Hectare) fh,SUM(PestisidaYes)/SUM(PestisidaYes+PestisidaNo)*100 pes,
            	SUM(PestisidaOrganic)/SUM(PestisidaOrganic+PestisidaNonOrganic)*100 pp,
            	SUM(ChemicalYes)/SUM(ChemicalYes+ChemicalNo)*100 che,SUM(OrganicYes)/SUM(OrganicYes+OrganicNo)*100 org,
            	SUM(ProtectiveYes)/SUM(ProtectiveYes+ProtectiveNo)*100 py,
            	SUM(BuangKubur)/SUM(BuangKebun+BuangGunakan+BuangKubur+BuangBakar+BuangLain)*100 op,
            	SUM(PestisidaKhusus)/SUM(PestisidaRumah+PestisidaKhusus+PestisidaLuar+PestisidaKebun+PestisidaLain)*100 kp,

            	SUM(KomposKandang)KomposKandang,SUM(KomposCair)KomposCair,SUM(KomposGranula)KomposGranula,
            	SUM(ApplicationUrea)ApplicationUrea,SUM(ApplicationTSP)ApplicationTSP,SUM(ApplicationNPK)ApplicationNPK,
            	SUM(ApplicationKCl)ApplicationKCl,SUM(ApplicationZA)ApplicationZA,
            	SUM(TBMApplication) AS TBMApplication,SUM(TMApplication) AS TMApplication, SUM(TRApplication) AS TRApplication,
            	SUM(PenyakitKanker) AS PenyakitKanker,SUM(PenyakitBusuk) AS PenyakitBusuk,
            	SUM(PenyakitUpas) AS PenyakitUpas,SUM(PenyakitAkar) AS PenyakitAkar,
            	SUM(PenyakitVSD) AS PenyakitVSD,SUM(PenyakitAntraknose) AS PenyakitAntraknose,
            	SUM(HamaBPK) AS HamaBPK,SUM(HamaHelopeltis) AS HamaHelopeltis,SUM(HamaBatang) AS HamaBatang,
            	SUM(InsectisidaYes) AS InsectisidaYes,SUM(InsectisidaNo) AS InsectisidaNo,
            	SUM(FungisidaYes) AS FungisidaYes,SUM(FungisidaNo) AS FungisidaNo,
            	sum(BuangKebun)BuangKebun,SUM(BuangGunakan)BuangGunakan,SUM(BuangKubur)BuangKubur,SUM(BuangBakar)BuangBakar,SUM(BuangLain)BuangLain,
            	SUM(PestisidaRumah)PestisidaRumah,SUM(PestisidaKhusus)PestisidaKhusus,SUM(PestisidaLuar)PestisidaLuar,
            	SUM(PestisidaKebun)PestisidaKebun,SUM(PestisidaLain)PestisidaLain,
            	SUM(HerbisidaYes) AS HerbisidaYes,SUM(HerbisidaNo) AS HerbisidaNo,
            	SUM(herbicide_paraquat)/sum(HerbisidaYes)*100 herbicide_paraquat,
               SUM(herbicide_glyphosate)/sum(HerbisidaYes)*100 herbicide_glyphosate,
               SUM(herbicide_24d)/sum(HerbisidaYes)*100 herbicide_24d,
            	SUM(InsectisidaYes) AS InsectisidaYes,SUM(InsectisidaNo) AS InsectisidaNo,
            	SUM(insecticide_banned)/sum(InsectisidaYes)*100 insecticide_banned,
               SUM(insecticide_watchlist)/sum(InsectisidaYes)*100 insecticide_watchlist,
               SUM(insecticide_allowed)/sum(InsectisidaYes)*100 insecticide_allowed,
            	SUM(FungisidaYes) AS FungisidaYes,SUM(FungisidaNo) AS FungisidaNo,
            	SUM(fungicide_banned)/sum(FungisidaYes)*100 fungicide_banned,
               SUM(fungicide_watchlist)/sum(FungisidaYes)*100 fungicide_watchlist,
               SUM(fungicide_allowed)/sum(FungisidaYes)*100 fungicide_allowed,
            	SUM(NOGAP_NOFung)NOGAP_NOFung,SUM(NOGAP_Fung)NOGAP_Fung,SUM(GAP_NOFung)GAP_NOFung,SUM(GAP_Fung)GAP_Fung,
            	SUM(ProtectiveYes)ProtectiveYes,SUM(ProtectiveNo)ProtectiveNo
            FROM dash_agri kcf
            WHERE 1=1 %s";
         $this->agri_group = "
            SELECT
            	%s label,
            	SUM(Kompos)/SUM(Hectare) kompos,
            	SUM(Fertilizer)/SUM(Hectare) Fertilizer,
            	SUM(ProtectiveYes)ProtectiveYes,SUM(ProtectiveNo)ProtectiveNo
            FROM dash_agri kcf
            %s
            WHERE 1=1 %s
            GROUP BY %s
            ORDER BY label";
        $query = $this->db->query(sprintf($this->agri_all,$where), array($prov));
        $results['all'] = $query->result_array();
        $query = $this->db->query(sprintf($this->agri_group,$label, $LEFT, $where, $groupby), array($prov));
        $results['group'] = $query->result_array();
        return $results;
    }

    function readDataDistrictAgriinput($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->sql_agrinput_data, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['data'] = $query_jumlah->result_array();

        $query_fertilzer = $this->db->query(sprintf(sprintf($this->sql_env_fertilizer, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['fertilizer'] = $query_fertilzer->result_array();

        $query_agriinput = $this->db->query(sprintf(sprintf($this->sql_agriinput, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['agriinput'] = $query_agriinput->result_array();

        return $results;
    }
    function readDataCertification($prov = '', $kab = '', $startdate = '', $enddate = '')
    {
        if ($startdate == '') {
            $startdate = date('Y-m-d');
        }
        if ($enddate == '') {
            $enddate = date('Y-m-d');
        }
        $where = '';
        if ($prov == '') {
            $label = 'Province';
            $JOIN = 'LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $JOIN = 'LEFT JOIN ktv_district kp on kp.DistrictID=substr(kcf.VillageID,1,4)';
            $where .= ' and substr(kcf.VillageID,1,2)=?';
            $groupby = 'substr(kcf.VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $JOIN = 'LEFT JOIN ktv_subdistrict kp on kp.SubDistrictID=kcf.SubDistrictID';
            $where .= ' and substr(kcf.VillageID,1,4)=?';
            $groupby = 'kcf.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query_farmer         = $this->db->query(sprintf($this->cert_farmer, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_gender         = $this->db->query(sprintf($this->cert_gender, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_ha             = $this->db->query(sprintf($this->cert_ha, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_volume         = $this->db->query(sprintf($this->cert_volume, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_kebun          = $this->db->query(sprintf($this->cert_kebun, $label, $JOIN, $startdate, $enddate, $groupby, $where, $groupby), array($prov));
        $query_produktivitas  = $this->db->query(sprintf($this->cert_produktivitas, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_holder         = $this->db->query(sprintf($this->cert_holder, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_rerata         = $this->db->query(sprintf($this->cert_rerata, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), array($prov));
        $query_size           = $this->db->query(sprintf($this->cert_size, $label, $startdate, $enddate, $JOIN, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $results['kebun']           = $query_kebun->result_array();
        $results['produktivitas']   = $query_produktivitas->result_array();
        $results['holder']          = $query_holder->result_array();
        $results['rerata']          = $query_rerata->result_array();
        $results['farmer']          = $query_farmer->result_array();
        $results['gender']          = $query_gender->result_array();
        $results['ha']              = $query_ha->result_array();
        $results['volume']          = $query_volume->result_array();
        $results['size']            = $query_size->result_array();

        return $results;
    }

    function readDataDistrictCertification($user, $district, $priv = '', $partner = '', $prov = '', $startdate = '', $enddate = '')
    {
        if ($startdate == '') {
            $startdate = date('Y-01-01');
        }
        if ($enddate == '') {
            $enddate = date('Y-m-d');
        }
        $where = '';
        $JOIN = '';

        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $JOIN .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $JOIN .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_farmer = $this->db->query(sprintf(sprintf($this->cert_farmer, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_gender = $this->db->query(sprintf(sprintf($this->cert_gender, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_ha = $this->db->query(sprintf(sprintf($this->cert_ha, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_volume = $this->db->query(sprintf(sprintf($this->cert_volume, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_kebun = $this->db->query(sprintf(sprintf($this->cert_kebun, $label, $JOIN, $startdate, $enddate, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_produktivitas = $this->db->query(sprintf(sprintf($this->cert_produktivitas, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_holder = $this->db->query(sprintf(sprintf($this->cert_holder, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_rerata = $this->db->query(sprintf(sprintf($this->cert_rerata, $label, $startdate, $enddate, $JOIN, $groupby, $where, $groupby), implode(',',$dist)), array($priv));
        $query_size = $this->db->query(sprintf(sprintf($this->cert_size, $label, $startdate, $enddate, $JOIN, $where), implode(',',$dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $results['kebun'] = $query_kebun->result_array();
        $results['produktivitas'] = $query_produktivitas->result_array();
        $results['holder'] = $query_holder->result_array();
        $results['rerata'] = $query_rerata->result_array();
        $results['farmer'] = $query_farmer->result_array();
        $results['gender'] = $query_gender->result_array();
        $results['ha'] = $query_ha->result_array();
        $results['volume'] = $query_volume->result_array();
        $results['size'] = $query_size->result_array();

        return $results;
    }

    function readDataNutrition($prov = '', $kab = '')
    {
        $where  = '';
        $LEFT   = '';
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' JOIN ktv_province on ProvinceID=substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' JOIN ktv_district on DistrictID=substr(kcf.VillageID,1,4)';
            $where .= ' and substr(kcf.VillageID,1,2)=?';
        } else {
            $label = 'SubDistrict';
            // $LEFT .= ' JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
            $where = ' and substr(kcf.VillageID,1,4)=?';
        }
        if ($kab != '') $prov = $kab;
        $query_household    = $this->db->query(sprintf($this->nutrition_household, $label, $LEFT, $where), array($prov));
        $query_kelamin      = $this->db->query(sprintf($this->nutrition_kelamin, $label, $LEFT, $where), array($prov));
        $query_idds         = $this->db->query(sprintf($this->nutrition_idds, $label, $LEFT, $where), array($prov));
        $query_size         = $this->db->query(sprintf($this->nutrition_size, $label, $LEFT, $where), array($prov));
        $query_lifestock    = $this->db->query(sprintf($this->nutrition_lifestock, $label, $LEFT, $where), array($prov));

        $results['household']   = $query_household->result_array();
        $results['kelamin']     = $query_kelamin->result_array();
        $results['idds']        = $query_idds->result_array();
        $results['size']        = $query_size->result_array();
        $results['lifestock']   = $query_lifestock->result_array();
        return $results;
    }

    function readDataDistrictNutrition($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        $where .= ' AND substr(kcf.VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } else {
            $where .= ' AND substr(kcf.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(kcf.VillageID,1,4)';
                $groupby = 'substr(kcf.VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' AND substr(kcf.VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        // echo '<pre>'; print_r(sprintf(sprintf($this->nutrition_household, $label, $LEFT, $where), implode(',', $dist))); echo '</pre>';exit;
        $query_household    = $this->db->query(sprintf(sprintf($this->nutrition_household, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_kelamin      = $this->db->query(sprintf(sprintf($this->nutrition_kelamin, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_idds         = $this->db->query(sprintf(sprintf($this->nutrition_idds, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $query_size         = $this->db->query(sprintf(sprintf($this->nutrition_size, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_lifestock    = $this->db->query(sprintf(sprintf($this->nutrition_lifestock, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $results['household']   = $query_household->result_array();
        $results['kelamin']     = $query_kelamin->result_array();
        $results['idds']        = $query_idds->result_array();
        $results['size']        = $query_size->result_array();
        $results['lifestock']   = $query_lifestock->result_array();

        return $results;
    }

    function readDataSurvey($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = 'AND VillageID';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'AND VillageID and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'AND VillageID and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query_jumlah = $this->db->query(sprintf($this->survey_jumlah, $label, $LEFT, $where, $groupby), array($prov));
        $results['jumlah'] = $query_jumlah->result_array();

        $query_avg = $this->db->query(sprintf($this->survey_avg, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $results['avg'] = $query_avg->result_array();

        $query_nutrition = $this->db->query(sprintf($this->survey_nutrition, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['nutrition'] = $query_nutrition->result_array();
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;

        // $query_idds = $this->db->query(sprintf($this->survey_idds,$label,$LEFT,$where,$groupby), array($prov));
        // $results['idds'] = $query_idds->result_array();

        $query_poverty = $this->db->query(sprintf($this->demographic_poverty, $label, $LEFT, $where), array($prov));
        $query_poverty_baseline = $this->db->query(sprintf($this->demographic_poverty_baseline, $label, $LEFT, $where), array($prov));
        $query_poverty_postline = $this->db->query(sprintf($this->demographic_poverty_postline, $label, $LEFT, $where), array($prov));

        $results['poverty'] = $query_poverty->result_array();
        $results['poverty_baseline'] = $query_poverty_baseline->result_array();
        $results['poverty_postline'] = $query_poverty_postline->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $sql = "
SELECT
    %s AS label,
   SUM(
      (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))
      +(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))
      +(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
      )/(SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0))
   ) total
FROM ktv_farmer_garden a
LEFT JOIN ktv_farmer_view aa ON aa.FarmerID = a.FarmerID
%s
WHERE
   a.`SurveyNr` = 0
   AND VillageID AND aa.StatusCode = 'active'
   %s
GROUP BY label
      ";
        // $tree_baseline_prod = 0;
        $query = $this->db->query(sprintf($sql, $label, $LEFT, $where), array($prov));
        // if ($query->num_rows() > 0) {
        //     $tree_baseline_prod = $query->row(0)->total;
        // }
        $results['tree_baseline_prod'] = $query->result_array();

        $sql = "
SELECT
    %s AS label,
   SUM(
      (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))
      +(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))
      +(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
      )/(SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0))
   ) total
FROM ktv_farmer_garden a
INNER JOIN (
   SELECT
      FarmerID
      ,GardenNr
      ,MAX(SurveyNr) LatestSurveyNr
   FROM ktv_farmer_garden
   WHERE
    SurveyNr > 0
   GROUP BY FarmerID,GardenNr
) z ON a.FarmerID = z.FarmerID AND a.GardenNr = z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
LEFT JOIN ktv_farmer_view aa ON aa.FarmerID = a.FarmerID
%s
WHERE
   VillageID AND aa.StatusCode = 'active'
   %s
GROUP BY label
      ";
        $query = $this->db->query(sprintf($sql, $label, $LEFT, $where), array($prov));
        // if ($query->num_rows() > 0) {
        //     $tree_postline_prod = $query->row(0)->total;
        // }
        // $results['tree_postline_prod'] = $tree_postline_prod;
        $results['tree_postline_prod'] = $query->result_array();

        return $results;
    }

    function readDataDistrictSurvey($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah       = $this->db->query(sprintf(sprintf($this->survey_jumlah, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_avg          = $this->db->query(sprintf(sprintf($this->survey_avg, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_nutrition    = $this->db->query(sprintf(sprintf($this->survey_nutrition, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $sql = "
SELECT
   %s AS label,
   SUM(
      (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))
      +(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))
      +(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
      )/(SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0))
   ) total
FROM ktv_farmer_garden a
LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = a.FarmerID
%s
WHERE
   a.`SurveyNr` = 0 AND kcf.StatusCode = 'active'
   AND VillageID IS NOT NULL
   %s
GROUP BY label
      ";
        // $tree_baseline_prod = 0;
        $query = $this->db->query(sprintf(sprintf($sql, $label, $LEFT, $where),
            implode(',', $dist)), array($priv));

        // if ($query->num_rows() > 0) {
        //     $tree_baseline_prod = $query->row(0)->total;
        // }
        // $results['tree_baseline_prod'] = $tree_baseline_prod;
        $results['tree_baseline_prod'] = $query->result_array();

        $sql = "
SELECT
   %s AS label,
   SUM(
      (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))
      +(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))
      +(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
      )/(SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0))
   ) total
FROM ktv_farmer_garden a
INNER JOIN (
   SELECT
      FarmerID
      ,GardenNr
      ,MAX(SurveyNr) LatestSurveyNr
   FROM ktv_farmer_garden
   WHERE SurveyNr > 0
   GROUP BY FarmerID,GardenNr
) z ON a.FarmerID = z.FarmerID AND a.GardenNr = z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = a.FarmerID
%s
WHERE
   VillageID IS NOT NULL AND kcf.StatusCode = 'active'
   %s
GROUP BY label
      ";
        $tree_postline_prod = 0;
        $query = $this->db->query(sprintf(sprintf($sql, $label, $LEFT, $where),
            implode(',', $dist)), array($priv));
        // if ($query->num_rows() > 0) {
        //     $tree_postline_prod = $query->row(0)->total;
        // }
        // $results['tree_postline_prod'] = $tree_postline_prod;
        $results['tree_postline_prod'] = $query->result_array();

        $query_poverty = $this->db->query(sprintf(sprintf($this->demographic_poverty, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_poverty_baseline = $this->db->query(sprintf(sprintf($this->demographic_poverty_baseline, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_poverty_postline = $this->db->query(sprintf(sprintf($this->demographic_poverty_postline, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));

        $results['poverty'] = $query_poverty->result_array();
        $results['poverty_baseline'] = $query_poverty_baseline->result_array();
        $results['poverty_postline'] = $query_poverty_postline->result_array();

        $results['jumlah'] = $query_jumlah->result_array();
        $results['avg'] = $query_avg->result_array();
        $results['nutrition'] = $query_nutrition->result_array();
        return $results;
    }

    function readDataTraceability($prov = '', $kab = '', $awal = '', $akhir = '', $traceability_partner = '')
    {
        if ($petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif ($petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(kcf.VillageID,1,2)';
            $where = 'and Province is not null';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(kcf.VillageID,1,4)';
            $where = 'and substr(kcf.VillageID,1,2)=? and District is not null';
            $groupby = 'substr(kcf.VillageID,1,4)';
        } else {
            $label = 'kp.SubDistrict';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $where = 'and substr(kcf.VillageID,1,4)=? and kp.SubDistrict is not null';
            $groupby = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        if ($awal != '' and $akhir != '') {
            $between = " AND wh_date BETWEEN '{$awal}' AND '{$akhir}'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }
        $where_partner = '';
        if (!empty($traceability_partner)) {
            $partner = $this->getPartner($traceability_partner);
            if ($partner['FlagAccess'] == '1') {
                $where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                $where_partner = " AND SUBSTR(kcf.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }

        $query_cpg          = $this->db->query(sprintf($this->sql_kelompok, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_farmer       = $this->db->query(sprintf($this->demographic_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_luas         = $this->db->query(sprintf($this->garden_luas, $label, $LEFT, $where.$where_partner . $qps, $groupby), array($prov));
        $query_produksi     = $this->db->query(sprintf($this->garden_produksi, $label, $LEFT, $where.$where_partner . $qps, $groupby), array($prov));

        $query_traceability_farmer       = $this->db->query(sprintf($this->traceability_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_traceability_production   = $this->db->query(sprintf($this->traceability_production, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if (!empty($traceability_partner)) {
            $where_partner = " AND wh.PartnerID = {$traceability_partner}";
        }
        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(wh_date,'%Y%m')='{$value['yearmonth']}',wh_netto,0)) AS sell_{$value['yearmonth']}
    ,COUNT(DISTINCT IF(DATE_FORMAT(wh_date,'%Y%m')='{$value['yearmonth']}',farmer_id,NULL)) AS trans_{$value['yearmonth']}
                ";
            }
        }

        $query_total      = $this->db->query(sprintf($this->traceability_total, $label, $SELECT, $LEFT, $where.$where_partner . $between, $groupby), array($prov));
        $query_certified  = $this->db->query(sprintf($this->traceability_sales_certified, $label, $LEFT, $where.$where_partner, $awal, $akhir, $awal, $akhir, $awal, $akhir), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // $query_penjualan    = $this->db->query(sprintf($this->traceability_penjualan, $label, $LEFT, $where . $between, $groupby), array($prov));
        // $query_transaction  = $this->db->query(sprintf($this->traceability_transaction, $label, $LEFT, $where . $betweentrans, $groupby), array($prov));
        // $query_farmer_sell  = $this->db->query(sprintf($this->traceability_farmer_sell, $label, $LEFT, $where . $betweentrans, $groupby), array($prov));
        // $query_sell         = $this->db->query(sprintf($this->traceability_sell, $label, '%Y%m', '%Y%m', $LEFT, $where . $betweentrans, $groupby), array($prov));

        $query_trader       = $this->db->query(sprintf($this->traceability_bu, $label, $LEFT, 'Pedagang', 'sce', $where, $groupby), array($prov));
        $query_koperasi     = $this->db->query(sprintf($this->traceability_bu, $label, $LEFT, 'Organisasi Petani', 'Organisasi Petani', $where, $groupby), array($prov));
        $query_warehouse    = $this->db->query(sprintf($this->traceability_bu, $label, $LEFT, 'Gudang', 'Gudang', $where, $groupby), array($prov));

        $results['cpg']         = $query_cpg->result_array();
        $results['farmer']      = $query_farmer->result_array();
        $results['luas']        = $query_luas->result_array();
        $results['produksi']    = $query_produksi->result_array();

        $results['traceability_farmer']         = $query_traceability_farmer->result_array();
        $results['traceability_production']     = $query_traceability_production->result_array();

        $results['total']       = $query_total->result_array();
        $results['certified']   = $query_certified->result_array();
        // $results['penjualan']       = $query_penjualan->result_array();
        // $results['transaction']     = $query_transaction->result_array();
        // $results['farmer_sell']     = $query_farmer_sell->result_array();
        // $results['sell']            = $query_sell->result_array();

        $results['trader']          = $query_trader->result_array();
        $results['koperasi']        = $query_koperasi->result_array();
        $results['warehouse']       = $query_warehouse->result_array();

        $results['months'] = $query_months->result_array();
        return $results;
    }

    function readDataDistrictTraceability($user, $district, $priv = '', $awal = '', $akhir = '', $partner = '', $prov = '', $traceability_partner = '')
    {
        $where = '';
        $LEFT = '';
        $where .= ' and substr(kcf.VillageID,1,4) in (%s)';
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }
        if (empty($prov)) {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } else {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'kp.District';
                $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(kcf.VillageID,1,4)';

                $groupby = 'substr(kcf.VillageID,1,4)';
            } else {
                $label = 'kp.SubDistrict';
                $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
                // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
                $where .= ' and substr(kcf.VillageID,1,4)=? and kp.SubDistrict is not null';
                $groupby = 'kp.SubDistrictID';
            }
        }
        // if ($user['isProgramStaff'] == 1) {
        //     $dist[] = $user['accessStaff'];
        // } else {
        //     $dist[] = $user['districtPartner'];
        // }
        $dist[] = $user['district_access'];
        // if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
        // if ($_SESSION['FlagAccess']) {
        //     $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        // }
        if (!empty($traceability_partner)) {
            $partner = $this->getPartner($traceability_partner);
            if ($partner['FlagAccess'] == '1') {
                $where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                $where_partner = " AND SUBSTR(kcf.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }

        if ($petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif ($petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';

        if ($awal != '' and $akhir != '') {
            $between = " AND wh_date BETWEEN '{$awal}' AND '{$akhir}'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }

        $query_cpg = $this->db->query(sprintf(sprintf($this->sql_kelompok, $label, $LEFT, $where, $groupby), implode(',', $dist)),
            array($priv));
        $query_farmer = $this->db->query(sprintf(sprintf($this->demographic_farmer, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_luas = $this->db->query(sprintf(sprintf($this->garden_luas, $label, $LEFT, $where . $qps, $groupby),
            implode(',', $dist)), array($priv));
        $query_produksi = $this->db->query(sprintf(sprintf($this->garden_produksi, $label, $LEFT, $where . $qps, $groupby),
            implode(',', $dist)), array($priv));

        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(wh_date,'%%Y%%m')='{$value['yearmonth']}',wh_netto,0)) AS sell_{$value['yearmonth']}
    ,COUNT(DISTINCT IF(DATE_FORMAT(wh_date,'%%Y%%m')='{$value['yearmonth']}',farmer_id,NULL)) AS trans_{$value['yearmonth']}
                ";
            }
        }
        // echo '<pre>'; print_r(sprintf(str_replace('%%', '%%%%', $this->traceability_total), $label, $LEFT, $where . $between, $groupby)); echo '</pre>'; exit;
        $query_total = $this->db->query(sprintf(sprintf(str_replace('%%', '%%%%', $this->traceability_total), $label, $SELECT, $LEFT, $where.$where_partner . $between, $groupby),
            implode(',', $dist)), array($priv));

        $query_certified = $this->db->query(sprintf(sprintf($this->traceability_sales_certified, $label, $LEFT, $where.$where_partner, $awal, $akhir, $awal, $akhir, $awal, $akhir),
            implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // $query_penjualan = $this->db->query(sprintf(sprintf($this->traceability_penjualan, $label, $LEFT, $where . $between, $groupby),
        //     implode(',', $dist)), array($priv));
        // $query_transaction = $this->db->query(sprintf(sprintf($this->traceability_transaction, $label, $LEFT, $where . $betweentrans, $groupby),
        //     implode(',', $dist)), array($priv));
        // $query_farmer_sell = $this->db->query(sprintf(sprintf($this->traceability_farmer_sell, $label, $LEFT, $where . $betweentrans, $groupby),
        //     implode(',', $dist)), array($priv));
        // $query_sell = $this->db->query(sprintf(sprintf($this->traceability_sell, $label, '%s', '%s', $LEFT, $where . $betweentrans, $groupby),
        //     '%Y%m', '%Y%m', implode(',', $dist)), array($priv));

        $query_trader = $this->db->query(sprintf(sprintf($this->traceability_bu, $label, $LEFT, 'Pedagang', 'sce', $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_koperasi = $this->db->query(sprintf(sprintf($this->traceability_bu, $label, $LEFT, 'Organisasi Petani', 'Organisasi Petani', $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_warehouse = $this->db->query(sprintf(sprintf($this->traceability_bu, $label, $LEFT, 'Gudang', 'Gudang', $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['cpg'] = $query_cpg->result_array();
        $results['farmer'] = $query_farmer->result_array();
        $results['luas'] = $query_luas->result_array();
        $results['produksi'] = $query_produksi->result_array();

        $results['total']       = $query_total->result_array();
        $results['certified']       = $query_certified->result_array();
        // $results['sell'] = $query_sell->result_array();
        // $results['transaction'] = $query_transaction->result_array();
        // $results['farmer_sell'] = $query_farmer_sell->result_array();
        // $results['penjualan'] = $query_penjualan->result_array();

        $results['trader'] = $query_trader->result_array();
        $results['koperasi'] = $query_koperasi->result_array();
        $results['warehouse'] = $query_warehouse->result_array();
        $results['months'] = $query_months->result_array();
        return $results;
    }

    function readDataTraining($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        // echo '<pre>'; print_r(sprintf($this->training_jumlah, $label, $LEFT, $where, $groupby)); echo '</pre>'; exit;
        $query_jumlah = $this->db->query(sprintf($this->training_jumlah, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['jumlah'] = $query_jumlah->result_array();
        // $results['tahun'] = $query_tahun->result_array();
        return $results;
    }

    function readDataDistrictTraining($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->training_jumlah, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['jumlah'] = $query_jumlah->result_array();
        return $results;
    }

    function readDataTrainingMaster($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = m.ProvinceID';
            $where = '';
            $groupby = 'Province';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = m.DistrictID';
            $where = 'and m.ProvinceID=?';
            $groupby = 'District';
        } else {
            // $label = 'SubDistrict';
            // // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            // $where = 'and substr(VillageID,1,4)=?';
            // $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        // echo '<pre>'; print_r(sprintf($this->training_jumlah, $label, $LEFT, $where, $groupby)); echo '</pre>'; exit;
        $query_jumlah = $this->db->query(sprintf($this->training_master_jumlah, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['jumlah'] = $query_jumlah->result_array();
        // $results['tahun'] = $query_tahun->result_array();
        return $results;
    }

    function readDataDistrictTrainingMaster($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->training_master_jumlah, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['jumlah'] = $query_jumlah->result_array();
        return $results;
    }

    function readDataTrainingKader($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        // echo '<pre>'; print_r(sprintf($this->training_jumlah, $label, $LEFT, $where, $groupby)); echo '</pre>'; exit;
        $query_jumlah = $this->db->query(sprintf($this->training_kader_jumlah, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['jumlah'] = $query_jumlah->result_array();
        // $results['tahun'] = $query_tahun->result_array();
        return $results;
    }

    function readDataDistrictTrainingKader($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->training_kader_jumlah, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['jumlah'] = $query_jumlah->result_array();
        return $results;
    }

    function readDataTrainingFarmer($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        // echo '<pre>'; print_r(sprintf($this->training_jumlah, $label, $LEFT, $where, $groupby)); echo '</pre>'; exit;
        $query_jumlah = $this->db->query(sprintf($this->training_farmer_jumlah, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $results['jumlah'] = $query_jumlah->result_array();
        // $results['tahun'] = $query_tahun->result_array();
        return $results;
    }

    function readDataDistrictTrainingFarmer($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->training_farmer_jumlah, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['jumlah'] = $query_jumlah->result_array();
        return $results;
    }

    function readDataCoop($CoopID = '')
    {
      if($CoopID!='')
      {
        $wer = " AND coop_member.coopID = $CoopID";
      } else {
        $wer = "";
      }


      //jumlah anggota terdaftar box dashlet
      $sql = "SELECT COUNT(coop_member.memberID) as jum
                          FROM coop_member
                          WHERE TRUE AND coopID = $CoopID";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['registered_member'] = $r->jum;
      } else {
        $results['registered_member'] = 0;
      }
      //end jumlah anggota terdaftar

       //jumlah anggota kandidat box dashlet
      $sql = "SELECT COUNT(coop_member.memberID) as jum
                          FROM coop_member
                          WHERE TRUE AND coopID = $CoopID and status = 4";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['candidate_member'] = $r->jum;
      } else {
        $results['candidate_member'] = 0;
      }
      //end jumlah anggota kandidat


      // JUMLAH ANGGOTA AKTIF
      $sqlActiveMember = "SELECT COUNT(coop_member.memberID) as jum
                          FROM coop_member
                          LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
                          WHERE coop_member.status = 1 $wer";
      $qActiveMember = $this->db->query($sqlActiveMember);
      if($qActiveMember->num_rows()>0)
      {
        $r = $qActiveMember->row();
        $results['active_member'] = $r->jum;
      } else {
        $results['active_member'] = 0;
      }

       //jumlah anggota tidak aktif box dashlet
      $sql = "SELECT COUNT(coop_member.memberID) as jum
                          FROM coop_member
                          WHERE TRUE AND coopID = $CoopID and status = 2";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['inactive_member'] = $r->jum;
      } else {
        $results['inactive_member'] = 0;
      }
      //end jumlah anggota tidak aktif

      // JUMLAH ANGGOTA AKTIF BY GENDER - MALE
      $sqlActiveMember = "SELECT COUNT(coop_member.memberID) as jum
                           FROM coop_member
                           LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
                           WHERE coop_member.`status` = 1 and gender=1
                           $wer
                           GROUP BY coop_member.gender";
      $qActiveMemberMALE = $this->db->query($sqlActiveMember);
      if($qActiveMemberMALE->num_rows()>0)
      {
        $r = $qActiveMemberMALE->row();
        $results['active_member_male'] = $r->jum;
      } else {
        $results['active_member_male'] = 0;
      }

      // JUMLAH ANGGOTA AKTIF BY GENDER - FEMALE
      $sqlActiveMember = "SELECT COUNT(coop_member.memberID) as jum
                           FROM coop_member
                           LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
                           WHERE coop_member.`status` = 1 and gender=2
                           $wer
                           GROUP BY coop_member.gender";
      $qActiveMemberFEM = $this->db->query($sqlActiveMember);
      if($qActiveMemberFEM->num_rows()>0)
      {
        $r = $qActiveMemberFEM->row();
        $results['active_member_female'] = $r->jum;
      } else {
        $results['active_member_female'] = 0;
      }

      // JUMLAH SIMPANAN POKOK
      $sql = "SELECT sum(coop_member_transaction.MemberTransactionCurrentBalance) AS total
                FROM
                  coop_member_transaction
                LEFT JOIN coop_member_saving ON coop_member_saving.memberSavingID = coop_member_transaction.MemberSavingID
                LEFT JOIN coop_saving_type ON coop_saving_type.savingTypeID = coop_member_saving.savingTypeID
                LEFT JOIN coop_member ON coop_member.memberID = coop_member_saving.memberID
                LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
                WHERE
                  coop_saving_type.savingTypeSHU = 1
                AND coop_member.status = 1
                AND coop_member_type.coopID = 1 $wer
                GROUP BY coop_member_saving.savingTypeID";
      $q = $this->db->query($sql);

      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['saving_pokok'] = $r->total;
      } else {
        $results['saving_pokok'] = 0;
      }

      // JUMLAH JUMLAH SIMPANAN WAJIB
      $sql = "SELECT savingTypeName, SUM(coop_member_transaction.MemberTransactionCurrentBalance) AS total
            FROM coop_member_transaction
            LEFT JOIN coop_member_saving ON coop_member_saving.memberSavingID = coop_member_transaction.MemberSavingID
            LEFT JOIN coop_saving_type ON coop_saving_type.savingTypeID = coop_member_saving.savingTypeID
            LEFT JOIN coop_member ON coop_member.memberID = coop_member_saving.memberID
            LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
            WHERE coop_saving_type.savingTypeSHU = 1 AND coop_member.`status` = 1 $wer
            GROUP BY coop_member_saving.savingTypeID";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['saving_wajib'] = $r->total;
      } else {
        $results['saving_wajib'] = 0;
      }

      //   // JUMLAH ANGGOTA SCPP
      $sql = "SELECT COUNT(coop_member.memberID) AS total
            FROM coop_member
            LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
            WHERE coop_member.`status` = 1 AND coop_member.FarmerID IS NOT NULL  $wer";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['member_scpp'] = $r->total;
      } else {
        $results['member_scpp'] = 0;
      }

      // //  // JUMLAH ANGGOTA NONSCPP
      $sql = "SELECT COUNT(coop_member.memberID)  AS total
        FROM coop_member
        LEFT JOIN coop_member_type ON coop_member_type.typeID = coop_member.typeID
        WHERE coop_member.`status` = 1 AND coop_member.FarmerID IS NULL $wer";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['member_nonscpp'] = $r->total;
      } else {
        $results['member_nonscpp'] = 0;
      }

      //Total Members Who Have Loan box dashlet
      $sql = "select count(a.MemberID) as jum
              from coop_member a
              where a.MemberID IN (select MemberID from coop_member_loan) and a.coopID = $CoopID";
      $q = $this->db->query($sql);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['member_loan'] = $r->jum;
      } else {
        $results['member_loan'] = 0;
      }
      //End total Members Who Have Loan box dashlet

      //Total Members Who Have Dues on Simpanan Wajib
      $this->load->model('coop/mtransaction');
      $total_due = 0;
      $q = $this->db->query("select a.`memberID`,a.`memberSavingID`
                            from coop_member_saving a
                            join coop_saving_type b ON a.savingTypeID = a.savingTypeID
                            where b.savingTypeSHU = 2 and a.coopID = $CoopID");
      foreach ($q->result() as $r) {
          $due = $this->mtransaction->getOutStandingSaving($r->memberSavingID);
          if(intval($due['dueMonth']>0))
          {
            $total_due++;
          }
      }
      $results['member_due_saving_wajib'] = $total_due;
      //End Total Members Who Have Dues on Simpanan Wajib


      // //PERSENTASEI FEMALE
      $SQL = "select sum((female/total)*100) as PercentFemale
            from (select (select count(*) from coop_member where gender=2) as female) as a,
            (select (select count(*) from coop_member) as total) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['persen_female'] = $r->PercentFemale;
      } else {
        $results['persen_female'] = 0;
      }

      //  //PERSENTASEI MALE
      $SQL = "select sum((male/total)*100) as PercentMale
            from (select (select count(*) from coop_member where gender=1) as male) as a,
            (select (select count(*) from coop_member) as total) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['persen_male'] = $r->PercentMale;
      } else {
        $results['persen_male'] = 0;
      }

      //  //TOTAL SIMP POKOK
      $SQL = "select sum(deposit-withdraw) as totSimpPokok
              from
              (select sum(z.MemberTransactionAmount) as deposit
                from coop_member_transaction z
                join coop_member_saving a ON z.MemberSavingID = a.MemberSavingID
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where b.savingTypeSHU = 2 and z.MemberTransactionType=1) as a,
              (select sum(z.MemberTransactionAmount) as withdraw
                from coop_member_transaction z
                join coop_member_saving a ON z.MemberSavingID = a.MemberSavingID
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where b.savingTypeSHU = 2 and z.MemberTransactionType=2) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['totSimpPokok'] = $r->totSimpPokok;
      } else {
        $results['totSimpPokok'] = 0;
      }

      // //AVG AGE MEMBER
      $SQL = "select sum(totalage/2) as avgAge
              from (select count(*) as total
              from coop_member) as a,
              (select sum(age) as totalage
                from (SELECT YEAR(CURRENT_TIMESTAMP) - YEAR(dateOfBirth) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(dateOfBirth, 5)) as age
                FROM coop_member) as a) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['avgAge'] = $r->avgAge;
      } else {
        $results['avgAge'] = 0;
      }

      // //TOTAL SAVING ACCOUNT
      $SQL = "SELECT count(*) as total
              from coop_member
              where `status` = 1 and coopID = $CoopID";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['total_saving_account'] = $r->total;
      } else {
        $results['total_saving_account'] = 0;
      }

      $this->load->model('member/mmember');
      $loan = $this->mmember->loanMemberSummary();

      // //TOTAL ACTIVE LOAN
      $results['total_loan'] = $loan['totalLoan'];
      $results['total_loan_interest'] = $loan['totalInterest'];
      $results['total_loan_outstanding'] = $loan['totalOutstanding'];
      $results['total_loan_paid'] = $loan['totalPaid'];

      // //  1. Anggota berdasarkan Status - Bar
      //   //  a. Jumlah Anggota Petani SCPP
      //   //  b. Jumlah Anggota Umum
      $SQL = "select totscpp,totnonscpp
              from (select count(*) as totscpp
              from coop_member where farmerID is not null and coopID = $CoopID) as a,
              (select count(*) as totnonscpp
              from coop_member where farmerID is null and coopID = $CoopID) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        // $results['totnonscpp'] = $r->totnonscpp;
        // $results['totscpp'] = $r->totscpp;
        $results['bar1']['val'] = array($r->totscpp,$r->totnonscpp);
        $results['bar1']['label'] = array('SCPP','NON SCPP');
      } else {
        $results['bar1']['val'] = NULL;
        $results['bar1']['label'] = NULL;
      }

      // // /Anggota Berdasarkan Member type
      $SQL = "select *
              from (select count(*) as totang
              from coop_member where typeID =1 and coopID = $CoopID) as a,
              (select count(*) as totcert
              from coop_member where typeID =2 and coopID = $CoopID) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['bar2']['val'] = array($r->totang,$r->totcert);
        $results['bar2']['label'] = array('Anggota','Anggota Sertifikasi');
      } else {
        $results['bar2']['val'] = NULL;
        $results['bar2']['label'] = NULL;
      }

      // //Anggota berdasarkan Gender
      $SQL = "select *
            from (select count(*) as totmale
            from coop_member where gender =1 and coopID = $CoopID) as a,
            (select count(*) as totfemale
            from coop_member where gender =2 and coopID = $CoopID) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['bar3']['val'] = array($r->totmale,$r->totfemale);
        $results['bar3']['label'] = array('Laki-laki','Perempuan');
      } else {
        $results['bar3']['val'] = NULL;
        $results['bar3']['label'] = NULL;
      }

      // //Anggota Berdasarkan Pekerjaan -
      $q = $this->db->query("select distinct job from coop_member");
      if($q->num_rows()>0)
      {
         $i=0;
         foreach ($q->result() as $r) {
           $qjob = $this->db->query("select count(*) as tot from coop_member where job ='".$r->job."' and coopID = $CoopID");
           if($qjob->num_rows()>0)
           {
              $rjob = $qjob->row();
              $results['bar4']['val'][$i] = $rjob->tot;
              $results['bar4']['label'][$i] = $r->job;
           } else {
              $results['bar4']['val'][$i] = 0;
              $results['bar4']['label'][$i] = $r->job;
           }
           $i++;
         }
      } else {
        $results['bar4']['val'] = NULL;
        $results['bar4']['label'] = NULL;
      }

      // //Anggota berdasarkan marital status
      $SQL = "select *
              from (select count(*) as totlajang
              from coop_member where maritalStatus =1 and coopID = $CoopID) as a,
              (select count(*) as totmenikah
              from coop_member where maritalStatus =2 and coopID = $CoopID) as b";
      $q = $this->db->query($SQL);
      if($q->num_rows()>0)
      {
        $r = $q->row();
        $results['bar5']['val'] = array($r->totlajang,$r->totmenikah);
        $results['bar5']['label'] = array('Lajang','Menikah');
      } else {
        $results['bar5']['val'] = NULL;
        $results['bar5']['label'] = NULL;
      }

      // // Pergerakan Kas Per bulan
      $y = date('Y');
      $idx = 0;
      for($m=1;$m<12;$m++)
      {
         if($m<9)
         {
           $month = '0'.$m;
         } else {
           $month = $m;
         }

         $sd = $y.'-'.$month.'-01';
         $nd = $y.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, $month, $y);

         $q = $this->db->query("select sum(transactionAmount) as tot
              from coop_transactions
              where (transactionDate between '$sd' and '$nd') and CoaCode = '1.1.1.1'");
         if($q->num_rows()>0)
         {
            $r = $q->row();
            $results['bar6']['val'][$idx] = $r->tot == null ? 0 : $r->tot;
            // $results['bar6']['label'][$idx] = NULL;
         } else {
            $results['bar6']['val'][$idx] = 0;
            // $results['bar6']['label'][$idx] = NULL;
         }
         $idx++;
      }

       //profit and loss
        //pendapatan
      $y = date('Y');
      $idx = 0;
      for($m=1;$m<12;$m++)
      {
         if($m<9)
         {
           $month = '0'.$m;
         } else {
           $month = $m;
         }

         $sd = $y.'-'.$month.'-01';
         $nd = $y.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, $month, $y);

         $q = $this->db->query("select sum(JournalDetailSum) as tot
              from accounting_journal_detail a
              join accounting_journal b ON a.JournalID = b.JournalID
              join accounting_coa c ON a.CoaCode = c.CoaCode
              join accounting_coa_group d ON c.coaGroupID = d.coaGroupID
              where (JournalPostedDate between '$sd' and '$nd') and d.coaClassID = 4");
         if($q->num_rows()>0)
         {
            $r = $q->row();
            $results['bar7']['val'][$idx] = $r->tot == null ? 0 : $r->tot;
            // $results['bar6']['label'][$idx] = NULL;
         } else {
            $results['bar7']['val'][$idx] = 0;
            // $results['bar6']['label'][$idx] = NULL;
         }
         $idx++;
      }

      //biaya
       $y = date('Y');
      $idx = 0;
      for($m=1;$m<12;$m++)
      {
         if($m<9)
         {
           $month = '0'.$m;
         } else {
           $month = $m;
         }

         $sd = $y.'-'.$month.'-01';
         $nd = $y.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, $month, $y);

         $q = $this->db->query("select sum(JournalDetailSum) as tot
              from accounting_journal_detail a
              join accounting_journal b ON a.JournalID = b.JournalID
              join accounting_coa c ON a.CoaCode = c.CoaCode
              join accounting_coa_group d ON c.coaGroupID = d.coaGroupID
              where (JournalPostedDate between '$sd' and '$nd') and d.coaClassID = 5");
         if($q->num_rows()>0)
         {
            $r = $q->row();
            $results['bar8']['val'][$idx] = $r->tot == null ? 0 : $r->tot;
            // $results['bar6']['label'][$idx] = NULL;
         } else {
            $results['bar8']['val'][$idx] = 0;
            // $results['bar6']['label'][$idx] = NULL;
         }
         $idx++;
      }

      //end profit and los


      return $results;
    }

    function readDataFinance($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query_jumlah = $this->db->query(sprintf($this->finance_jumlah, $label, $LEFT, $where, $groupby), array($prov));
        $results['count'] = $query_jumlah->result_array();

        $query_household = $this->db->query(sprintf($this->finance_household, $label, $LEFT, $where, $groupby), array($prov));
        $results['household'] = $query_household->result_array();

        // $query_kelamin  = $this->db->query(sprintf($this->finance_kelamin,$label,$LEFT,$where,$groupby), array($prov));
        // $results['kelamin'] = $query_kelamin->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // exit;
        // $query_tahun  = $this->db->query(sprintf($this->training_tahun,$label,$LEFT,$where,$groupby), array($prov));
        // $results['tahun'] = $query_tahun->result_array();
        return $results;
    }

    function readDataDistrictFinance($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->finance_jumlah, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['count'] = $query_jumlah->result_array();

        $query_household = $this->db->query(sprintf(sprintf($this->finance_household, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['household'] = $query_household->result_array();

        // $query_kelamin = $this->db->query(sprintf(sprintf($this->finance_kelamin,$label,$LEFT,$where,$groupby), implode(',', $dist)), array($priv));
        // $results['kelamin'] = $query_kelamin->result_array();

        return $results;
    }

    function readDataEnvironment($prov = '', $kab = '')
    {
        if ($petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif ($petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;

        $query_jumlah = $this->db->query(sprintf($this->sql_environment, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $results['data'] = $query_jumlah->result_array();

        // $query_fertilzer = $this->db->query(sprintf($this->sql_env_fertilizer, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // $results['fertilizer'] = $query_fertilzer->result_array();

        $query_diversification = $this->db->query(sprintf($this->sql_env_diversification, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $results['diversification'] = $query_diversification->result_array();

        $query_lain = $this->db->query(sprintf($this->garden_lain, $LEFT, $where . $qps), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $results['other'] = $query_lain->result_array();
        // exit;

        //   $query_pesticide_baseline          = $this->db->query(sprintf($this->environment_pesticide_baseline,$label,$LEFT,$where), array($prov));
        //   $results['pesticide_baseline_postline']    = $query_pesticide_baseline->result_array();

        // $query_pesticide_latest = $this->db->query(sprintf($this->environment_pesticide_latest, $label, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // exit;
        // $results['pesticide_latest'] = $query_pesticide_latest->result_array();


        return $results;
    }

    function readDataDistrictEnvironment($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist = array();
        // if ($user['isProgramStaff'] == 1) {
        //     $dist[] = $user['accessStaff'];
        // } else {
        //     $dist[] = $user['districtPartner'];
        // }
        $dist[] = $user['district_access'];
        // if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
        if ($_SESSION['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->sql_environment, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));
        $results['data'] = $query_jumlah->result_array();

        $query_fertilzer = $this->db->query(sprintf(sprintf($this->sql_env_fertilizer, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['fertilizer'] = $query_fertilzer->result_array();

        $query_diversification = $this->db->query(sprintf(sprintf($this->sql_env_diversification, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['diversification'] = $query_diversification->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // exit;

        $qps = '';
        $query_lain = $this->db->query(sprintf(sprintf($this->environment_garden_lain, $LEFT, $where . $qps), implode(',', $dist)), array($priv));
        $results['other'] = $query_lain->result_array();

        //   $query_pesticide_baseline        = $this->db->query(sprintf(sprintf($this->environment_pesticide_baseline,$label,$LEFT,$where,$groupby),implode(',',$dist)), array($priv));
        //   $results['pesticide_baseline_postline']   = $query_pesticide_baseline->result_array();

        $query_pesticide_latest = $this->db->query(sprintf(sprintf($this->environment_pesticide_latest, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['pesticide_latest'] = $query_pesticide_latest->result_array();

        return $results;
    }

    function readDistrictByProvince($prov)
    {
        $sql = "
         SELECT District as label,DistrictID as id
         FROM ktv_district
         WHERE ProvinceID=? AND DistrictID not in (1171,7271,1377, 7373,7371)
         ORDER BY label";
        $query = $this->db->query($sql, array($prov));
        return $query->result_array();
    }

    function readDistrict($district)
    {
        $sql = "
            SELECT DistrictID as id, District as label
            from ktv_district
            WHERE DistrictID in (%s) AND DistrictID not in (1171,7271,1377, 7373,7371)
            ORDER BY District";
        $dis = explode(',', $district);
        for ($i = 0; $i < sizeof($dis); $i++) {
            $di = explode('##', $dis[$i]);
            $dist[] = $di[0];
        }
        $query = $this->db->query(sprintf($sql, implode(',', $dist)), array());
        return $query->result_array();
    }

    function readDataTraceability_($awal = '', $akhir = '', $orgid = '')
    {
        if ($awal != '' and $akhir != '') {
            $between = " and (SupplyBatchDate between '$awal' and '$akhir')";
            $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }

        $this->traceability_penjualan_ = "SELECT ks.SubDistrict label,sum(VolumeNetto) total,sum(VolumeBruto) total_bruto
         FROM ktv_supplychain_batch ksb
         LEFT JOIN ktv_supplychain_org_view ksovb ON ksovb.SupplychainID = ksb.SupplyOrgID
         LEFT JOIN ktv_supplychain_transaction kst ON ksb.SupplyBatchID = kst.SupplyBatchID
         LEFT JOIN ktv_farmer_view kcf ON kst.SupplyID = kcf.FarmerID
         LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kcf.SubDistrictID
         WHERE ks.SubDistrict is not null AND kcf.StatusCode = 'active' and ksb.SupplyOrgID=? %s
         GROUP BY ks.SubDistrict
         ORDER BY label";
        $this->traceability_transaction_ = "SELECT ks.SubDistrict label,count(SupplyTransID) total,count(distinct FarmerID) total_farmer
         FROM ktv_supplychain_transaction kst
         LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID = kst.SupplyBatchID
         LEFT JOIN ktv_farmer_view kcf ON kst.SupplyID = kcf.FarmerID
         LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kcf.SubDistrictID
         WHERE ksb.SupplyOrgID=? AND kcf.StatusCode = 'active' %s
         GROUP BY ks.SubDistrict ORDER BY label";
        $query_penjualan = $this->db->query(sprintf($this->traceability_penjualan_, $between), array($orgid));
        $query_transaction = $this->db->query(sprintf($this->traceability_transaction_, $betweentrans), array($orgid));

        $results['penjualan'] = $query_penjualan->result_array();
        $results['transaction'] = $query_transaction->result_array();
        return $results;
    }

     public function getCoops()
    {
      $q = $this->db->query("select CoopID as id,CoopName as name from ktv_cooperatives group by CoopName desc");
       if ($q) {
            return $q->result_array();
        }
    }

    public function getRegions($user, $prov, $kab, $daer, $region_status='')
    {
        //cek apakah admin
        if($_SESSION['is_admin'] == "1"){
            if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->order_by('name', 'asc');

                if($region_status == "not_active"){
                    $query = $this->db->get_where('ktv_province', array());
                }else{
                    $query = $this->db->get_where('ktv_province', array('active' => '1'));
                }

            } else {
                $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
                $this->db->order_by('name', 'asc');

                if($region_status == "not_active"){
                    $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov));
                }else{
                    $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov, 'active' => '1'));
                }

            }
            return $query->result_array();
        }

        $region = array();
        if (empty($daer)) {
            if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->order_by('name', 'asc');

                if($region_status == "not_active"){
                    $query = $this->db->get_where('ktv_province', array());
                }else{
                    $query = $this->db->get_where('ktv_province', array('active' => '1'));
                }

            } else {
                $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
                $this->db->order_by('name', 'asc');

                if($region_status == "not_active"){
                    $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov));
                }else{
                    $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov, 'active' => '1'));
                }
            }
        } else {
            // if ($_SESSION['FlagAccess'] == 1) {
            //     $daer = $user['accessStaff'];
            // } else {
            //     $daer = $user['districtPartner'];
            // }
            $daer = $user['district_access'];
            $daer = explode(',', $daer);
            $p_daer = array();
            $d_daer = array();
            foreach ($daer as $key => $value) {
                if (!in_array(substr($value, 0, 2), $p_daer)) {
                    $p_daer[] = substr($value, 0, 2);
                }
                $d_daer[] = $value;
            }
            if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->where_in('ProvinceID', $p_daer);
                $this->db->order_by('name', 'asc');

                if($region_status == "not_active"){
                    $query = $this->db->get_where('ktv_province', array());
                }else{
                    $query = $this->db->get_where('ktv_province', array('active' => '1'));
                }
            } else {
                $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
                $this->db->where_in('DistrictID', $d_daer);
                $this->db->order_by('name', 'asc');

                if($region_status == "not_active"){
                    $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov));
                }else{
                    $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov, 'active' => '1'));
                }
            }
        }

        if ($query) {
            return $query->result_array();
        }
    }

    public function getRegionMars($user, $prov, $kab, $daer)
    {
        $region = array();

        if (empty($prov)) {
            $sql = "
SELECT
    p.ProvinceID AS id
    , p.Province AS `name`
FROM ktv_province p
LEFT JOIN ktv_district d ON d.ProvinceID = p.ProvinceID
WHERE
    p.active = 1 AND d.active = 1
    --where--
GROUP BY `name`
ORDER BY `name`
            ";
        } else {
            $sql = "
SELECT
    d.DistrictID AS id
    , d.District AS `name`
FROM ktv_district d
WHERE
    d.active = 1 AND d.ProvinceID = {$prov}
    --where--
GROUP BY `name`
ORDER BY `name`
            ";
        }
        $daer = $user['district_access'];
        if (empty($daer)) {
            $where = '';
        } else {
            $where = " AND DistrictID IN ({$daer})";
        }
        $where .= ' AND DistrictID IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = 9)';
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getRegionCargill($user, $prov, $kab, $daer)
    {
        $region = array();

        if (empty($prov)) {
            $sql = "
SELECT
    p.ProvinceID AS id
    , p.Province AS `name`
FROM ktv_province p
LEFT JOIN ktv_district d ON d.ProvinceID = p.ProvinceID
WHERE
    p.active = 1 AND d.active = 1
    --where--
GROUP BY `name`
ORDER BY `name`
            ";
        } else {
            $sql = "
SELECT
    d.DistrictID AS id
    , d.District AS `name`
FROM ktv_district d
WHERE
    d.active = 1 AND d.ProvinceID = {$prov}
    --where--
GROUP BY `name`
ORDER BY `name`
            ";
        }
        $daer = $user['district_access'];
        if (empty($daer)) {
            $where = '';
        } else {
            $where = " AND DistrictID IN ({$daer})";
        }
        $where .= ' AND DistrictID IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = 9)';
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getRegionMaster($user, $prov, $kab, $daer)
    {
        $region = array();
        if (empty($daer)) {
            // if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_province');
            // } else {
            //     $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
            //     $this->db->order_by('name', 'asc');
            //     $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov));
            // }
        } else {
            $daer = $user['district_access'];
            $daer = explode(',', $daer);
            $p_daer = array();
            $d_daer = array();
            foreach ($daer as $key => $value) {
                $p_daer[] = substr($value, 0, 2);
                $d_daer[] = $value;
            }
            // if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->where_in('ProvinceID', $p_daer);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_province');
            // } else {
            //     $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
            //     $this->db->where_in('DistrictID', $d_daer);
            //     $this->db->order_by('name', 'asc');
            //     $query = $this->db->get_where('ktv_district');
            // }
        }

        if ($query) {
            return $query->result_array();
        }
    }

    public function getProvinceSession()
    {
        $district = '';
        if($_SESSION['daerah_access']){
          $district = $_SESSION['daerah_access'];
        }
        $sql = "SELECT
                p.ProvinceID id
                , p.Province	name
            FROM
                ktv_district d
            LEFT JOIN
                ktv_province p on p.ProvinceID = d.ProvinceID
            WHERE
                d.DistrictID IN ($district)
            GROUP BY
                p.ProvinceID";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->result_array();
        }
    }

    public function getDistrictSession()
    {
        $district = '';
        if($_SESSION['daerah_access']){
          $district = $_SESSION['daerah_access'];
        }
        $sql = "SELECT
                d.DistrictID id
                , d.District as name
            FROM
                ktv_district d
            LEFT JOIN
                ktv_province p on p.ProvinceID = d.ProvinceID
            WHERE
                d.DistrictID IN ($district)
            GROUP BY
                p.ProvinceID";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->result_array();
        }
    }

    public function getSubDistrictSession($id)
    {
        $subdistrict = '';
        if($_SESSION['daerah_access']){
          $subdistrict = $id;
        }
        
        $sql = "SELECT
                d.SubDistrictID id
                , d.SubDistrict as name
            FROM
                ktv_subdistrict d
            WHERE
                d.StatusCode = 'active'
            AND
                d.DistrictID IN ($subdistrict)";
        $query = $this->db->query($sql);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        if ($query) {
            return $query->result_array();
        }
    }

    public function getVillageNew($id)
    {
        $subdistrict = '';
        if($_SESSION['daerah_access']){
          $subdistrict = $id;
        }

        $sql = "SELECT
            d.VillageID id
            , d.Village as name
        FROM
            ktv_village d
        WHERE
            d.StatusCode = 'active'
        AND
            d.SubDistrictID = '$subdistrict'";
            
        $query = $this->db->query($sql);

        if ($query) {
            return $query->result_array();
        }
    }


    public function getRegionKpi($user, $prov, $kab, $daer)
    {
        $region = array();
        // if (empty($daer)) {
            if (empty($prov)) {
                $this->db->SELECT('p.ProvinceID AS id, p.Province AS name', FALSE);
                $this->db->join('ktv_district d', 'd.ProvinceID = p.ProvinceID', 'INNER');
                $this->db->join('ktv_kpi_target t', 't.DistrictID = d.DistrictID', 'INNER');
                if (!empty($user['accessStaff'])) {
                    $this->db->where("d.DistrictID IN ({$user['accessStaff']})");
                }
                $this->db->group_by('name');
                $this->db->order_by('name', 'asc');
                $query = $this->db->get('ktv_province p');
            } else {
                $this->db->SELECT('d.DistrictID AS id, d.District AS name', FALSE);
                $this->db->join('ktv_kpi_target t', 't.DistrictID = d.DistrictID', 'INNER');
                if (!empty($user['accessStaff'])) {
                    $this->db->where("d.DistrictID IN ({$user['accessStaff']})");
                }
                $this->db->group_by('name');
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_district d', array('ProvinceID' => $prov));
            }
        // } else {
        //     $daer = $user['district_access'];
        //     $daer = explode(',', $daer);
        //     $p_daer = array();
        //     $d_daer = array();
        //     foreach ($daer as $key => $value) {
        //         $p_daer[] = substr($value, 0, 2);
        //         $d_daer[] = $value;
        //     }
        //     if (empty($prov)) {
        //         $this->db->SELECT('p.ProvinceID AS id, p.Province AS name', FALSE);
        //         $this->db->join('ktv_district d', 'd.ProvinceID = p.ProvinceID', 'INNER');
        //         $this->db->join('ktv_kpi_target t', 't.DistrictID = d.DistrictID', 'INNER');
        //         $this->db->where_in('p.ProvinceID', $p_daer);
        //         $this->db->group_by('name');
        //         $this->db->order_by('name', 'asc');
        //         $query = $this->db->get_where('ktv_province p', array());
        //     } else {
        //         $this->db->SELECT('d.DistrictID AS id, d.District AS name', FALSE);
        //         $this->db->join('ktv_kpi_target t', 't.DistrictID = d.DistrictID', 'INNER');
        //         $this->db->group_by('name');
        //         $this->db->where_in('d.DistrictID', $d_daer);
        //         $this->db->order_by('name', 'asc');
        //         $query = $this->db->get_where('ktv_district d', array('ProvinceID' => $prov));
        //     }
        // }
        if ($query) {
            return $query->result_array();
        }
    }

    public function _getRegionKpi($user, $prov, $kab, $daer)
    {
        $region = array();
        if (empty($daer)) {
            $this->db->SELECT('p.ProvinceID AS id, p.Province AS name', FALSE);
            $this->db->from('ktv_province p');
            $this->db->join('ktv_district d', 'd.ProvinceID = p.ProvinceID', 'INNER');
            $this->db->join('ktv_kpi_target t', 't.DistrictID = d.DistrictID', 'INNER');
            $this->db->group_by('name');
            $this->db->order_by('name', 'asc');
            $query = $this->db->get('');
        } else {
            $daer = $user['district_access'];
            $daer = explode(',', $daer);
            $p_daer = array();
            $d_daer = array();
            foreach ($daer as $key => $value) {
                $p_daer[] = substr($value, 0, 2);
                $d_daer[] = $value;
            }
            $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
            $this->db->from('ktv_province p');
            $this->db->join('ktv_district d', 'd.ProvinceID = p.ProvinceID', 'INNER');
            $this->db->join('ktv_kpi_target t', 't.DistrictID = d.DistrictID', 'INNER');
            $this->db->where_in('ProvinceID', $p_daer);
            $this->db->group_by('name');
            $this->db->order_by('name', 'asc');
            $query = $this->db->get;
        }

        if ($query) {
            return $query->result_array();
        }
    }

    public function getProvince($id)
    {
        $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
        $query = $this->db->get_where('ktv_province', array('ProvinceID' => $id), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getPartners($user, $prov, $kab, $daer, $start, $end)
    {
        $sql = "SELECT
    pp.PartnerID AS id,
    pp.PartnerName AS `name`
FROM ktv_district_partner dp
JOIN ktv_district d ON d.DistrictID = dp.DistrictID
JOIN ktv_program_partner pp ON pp.PartnerID = dp.PartnerID
JOIN ktv_warehouse wh ON wh.PartnerID = dp.PartnerID
JOIN rpt_traceability rt ON rt.wh_orgid = wh.WarehouseID
WHERE
    1 = 1
    --filter--
GROUP BY pp.PartnerID
ORDER BY `name`
        ";
        $filter = '';
        $params = array();
        if (!empty($prov)) {
            $filter .= " AND d.ProvinceID = ?";
            $params[] = intval($prov);
        }
        if (!empty($kab)) {
            $filter .= " AND d.DistrictID = ?";
            $params[] = intval($kab);
        }
        if ($user['isProgramStaff'] == 1) {
            $filter .= " AND pp.PartnerID = ?";
            $params[] = $user['programPartner'];
        }
        if ($user['isPrivateStaff'] == 1) {
            $filter .= " AND pp.PartnerID = ?";
            $params[] = $user['privatePartner'];
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query) {
            return $query->result_array();
        }
    }

    public function getPartner($id)
    {
        $this->db->SELECT('PartnerID AS id, PartnerName AS name, FlagAccess', FALSE);
        $query = $this->db->get_where('ktv_program_partner', array('PartnerID' => $id), 1);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function readBank($prov = '', $kab = '', $petani = '', $tahun = '')
    {
        $where = '';
        $LEFT  = '';
        if ($prov == '') {
            $label      = 'Province';
            $LEFT      .= ' JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby    = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label      = 'District';
            $LEFT      .= ' JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
            $where     .= ' AND substr(VillageID,1,2)=?';
            $groupby    = 'substr(VillageID,1,4)';
        } else {
            $label      = 'SubDistrict';
            $where     .= ' AND substr(VillageID,1,4)=?';
            $groupby    = 'SubDistrictID';
        }

        if ($kab != '') $prov = $kab;

        $query_farmer       = $this->db->query(sprintf($this->bank_farmer, $label, $LEFT, $where, $groupby), array($prov));
        $query_loan         = $this->db->query(sprintf($this->bank_loan, $label, $LEFT, $where, $groupby), array($prov));
        $query_distance     = $this->db->query(sprintf($this->bank_distance, $label, $LEFT, $where, $groupby), array($prov));

        $results['farmer']      = $query_farmer->result_array();
        $results['loan']        = $query_loan->result_array();
        $results['distance']    = $query_distance->result_array();

        return $results;
    }

    public function readDistrictBank($district, $priv = '', $petani = '', $partner = '', $prov = '')
    {
        $where = '';
        if ( ! empty($partner)) {
                $where .= "
AND kcf.`CPGid` IN (
    SELECT
        CPGid
    FROM
        `ktv_cpg_partner`
    WHERE
       `PartnerID` = {$partner}
)
            ";
        }
        if ($prov != '') {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
        }
        if ($priv == '') {
            $label = 'District';
            $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
            $where .= ' and substr(VillageID,1,4) in (%s)';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            $where .= ' and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }

        // $dis = explode(',', $district);
        // for ($i = 0; $i < sizeof($dis); $i++) {
        //     $di = explode('##', $dis[$i]);
        //     $dist[] = $di[0];
        // }
        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }

        $query_farmer     = $this->db->query(sprintf(sprintf($this->bank_farmer, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_loan       = $this->db->query(sprintf(sprintf($this->bank_loan, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_distance   = $this->db->query(sprintf(sprintf($this->bank_distance, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));

        $results['farmer']      = $query_farmer->result_array();
        $results['loan']        = $query_loan->result_array();
        $results['distance']    = $query_distance->result_array();

        return $results;
    }

    public function check_user($userid = null)
    {
      $user = array();
      if (empty($userid)) {
        $userid = $_SESSION['userid'];
      }
      $sql = "SELECT
  u.UserId,
  u.UserIsAdmin,
  IF(pgs.StaffID,1,0) AS isProgramStaff,
  pgs.PartnerID AS programPartner,
  IF(pvs.PrivateStaffID,1,0) AS isPrivateStaff,
  pvs.PartnerID AS privatePartner,
  pp.FlagAccess,
  GROUP_CONCAT(DISTINCT cp.DistrictID) AS cpgPartner,
  GROUP_CONCAT(DISTINCT dp.DistrictID) AS districtPartner,
  GROUP_CONCAT(DISTINCT sa.DistrictID) AS accessStaff
FROM sys_user u
LEFT JOIN ktv_persons p ON p.UserID = u.UserId
LEFT JOIN ktv_private_staff pvs ON pvs.PersonID = p.PersonID
LEFT JOIN ktv_program_staff pgs ON pgs.PersonID = p.PersonID
LEFT JOIN ktv_program_partner pp ON pp.PartnerID = IFNULL(pvs.PartnerID,pgs.PartnerID)
LEFT JOIN ktv_district_partner dp ON IFNULL(pvs.PartnerID,pgs.PartnerID)=dp.PartnerID
LEFT JOIN (
    SELECT
        GROUP_CONCAT(DISTINCT sd.DistrictID) AS DistrictID,
        cp.PartnerID
    FROM ktv_cpg_partner cp
    JOIN ktv_cpg c ON c.CPGid = cp.CPGid
    LEFT JOIN ktv_village v ON v.VillageID = c.VillageID
    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    GROUP BY cp.PartnerID
) cp ON IFNULL(pvs.PartnerID,pgs.PartnerID)=cp.PartnerID
LEFT JOIN ktv_access_staff sa ON sa.UserId=u.UserId
WHERE
  u.UserId = ?
GROUP BY u.UserId
      ";
      $query = $this->db->query($sql, array($userid));
      if ($query->num_rows() > 0) {
        $result = $query->row_array(0);
        if ($result['isPrivateStaff'] || $result['isProgramStaff']) {
            // if (!empty($result['cpgPartner'])) {
            //   $result['district_access'] = $result['cpgPartner'];
            // } elseif (!empty($result['accessStaff'])) {
            //   $result['district_access'] = $result['accessStaff'];
            // } elseif (!empty($result['districtPartner'])) {
            //   $result['district_access'] = $result['districtPartner'];
            // }
            $result['district_access'] = implode(',', array_intersect(explode(',', $result['accessStaff']), explode(',', $result['cpgPartner'])));
        } else {
            $result['district_access'] = !empty($result['accessStaff']) ? $result['accessStaff'] : $result['districtPartner'];
        }

        //cek pengecualian terakhir, kalau tidak ada, maka ambil dari access staff
        if($result['district_access'] == "") {
            $result['district_access'] = $result['accessStaff'];
        }

        return $result;
      }
      return false;
    }


    function readDataDemographic($prov = '', $kab = '', $petani = '', $tahun = '')
    {
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $LEFT = 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE YEAR(ExternalDate) = ' . $tahun . ' group by FarmerID) ce on ce.farid=kcf.FarmerID';
            $qp = "and farid is not null and ExternalDate > '0000-00-00'";
        } elseif ($petani == '2') {
            $LEFT = 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification group by FarmerID) ce on ce.farid=kcf.FarmerID';
            $qp = "and farid is null and ExternalDate > '0000-00-00'";
        }
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $where = $qp;
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
            $where = $qp . ' and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT .= ' JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
            $where = $qp . ' and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }

        if ($kab != '') $prov = $kab;
        $query_age                  = $this->db->query(sprintf($this->demographic_age, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $query_gender               = $this->db->query(sprintf($this->demographic_gender, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $query_farmer               = $this->db->query(sprintf($this->demographic_farmer, $label, $LEFT, $where, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $query_edu                  = $this->db->query(sprintf($this->demographic_edu, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $query_kedua                = $this->db->query(sprintf($this->demographic_kedua, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $query_poverty              = $this->db->query(sprintf($this->demographic_poverty, $label, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // $query_poverty_baseline  = $this->db->query(sprintf($this->demographic_poverty_baseline,$label,$LEFT,$where), array($prov));
        // $query_poverty_postline  = $this->db->query(sprintf($this->demographic_poverty_postline,$label,$LEFT,$where), array($prov));
        // $query_household            = $this->db->query(sprintf($this->demographic_household, $label, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $query_hh_size              = $this->db->query(sprintf($this->demographic_hh_size, $label, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // exit;
        $query_cook_fuel            = $this->db->query(sprintf($this->demographic_cook_fuel, $label, $LEFT, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // exit;

        $results['kedua']                 = $query_kedua->result_array();
        $results['age']                   = $query_age->result_array();
        $results['gender']                = $query_gender->result_array();
        $results['farmer']                = $query_farmer->result_array();
        $results['edu']                   = $query_edu->result_array();
        $results['poverty']               = $query_poverty->result_array();
        // $results['poverty_baseline']   = $query_poverty_baseline->result_array();
        // $results['poverty_postline']   = $query_poverty_postline->result_array();
        // $results['household']             = $query_household->result_array();
        $results['hh_size']               = $query_hh_size->result_array();
        $results['cook_fuel']             = $query_cook_fuel->result_array();

        return $results;
    }
    function readDataOld($prov = '', $kab = '') {
        if ($prov == '') {
            $where = '';
            $group = 'GROUP BY substr(aa.VillageID,1,2)';
        } elseif ($kab == '') {
            $where = 'and substr(aa.VillageID,1,2)=?';
            $group = 'GROUP BY substr(aa.VillageID,1,4)';
        } else {
            $where = 'and substr(aa.VillageID,1,4)=?';
            $group = 'GROUP BY aa.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;

        $query_cpg              = $this->db->query(sprintf($this->main_cpg, $where), array($prov));
        $query_farmer           = $this->db->query(sprintf($this->main_farmer, $where), array($prov));
        $query_luas             = $this->db->query(sprintf($this->main_luas, $where), array($prov));
        $query_pohon            = $this->db->query(sprintf($this->main_pohon, $where), array($prov));
        $query_total            = $this->db->query(sprintf($this->main_total, $where), array($prov));
        $query_training         = $this->db->query(sprintf($this->main_training, $where), array($prov));
        $query_usia             = $this->db->query(sprintf($this->main_usia, $where, $group), array($prov));
        $query_ukuran           = $this->db->query(sprintf($this->main_ukuran, $where, $group), array($prov));
        $query_perempuan        = $this->db->query(sprintf($this->main_perempuan, $where), array($prov));
        $query_ketiga           = $this->db->query(sprintf($this->main_ketiga,
          $where, // 0
          $where, '', // 1
          $where, '', $group, // 2
          $where, '', // 3
          $where, '', // 4
          $where, '', $group // 5
          ),array($prov, $prov, $prov, $prov, $prov, $prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $query_classification   = $this->db->query(sprintf($this->main_classification, $where), array($prov));
        $query_ha               = $this->db->query(sprintf($this->main_ha, $where), array($prov));
        $query_gfp              = $this->db->query(sprintf($this->main_gfp, $where), array($prov));

        $results['ketiga']          = $query_ketiga->result_array();
        $results['cpg']             = $query_cpg->result_array();
        $results['farmer']          = $query_farmer->result_array();
        $results['luas']            = $query_luas->result_array();
        $results['pohon']           = $query_pohon->result_array();
        $results['total']           = $query_total->result_array();
        $results['training']        = $query_training->result_array();
        $results['usia']            = $query_usia->result_array();
        $results['ukuran']          = $query_ukuran->result_array();
        $results['perempuan']       = $query_perempuan->result_array();
        $results['classification']  = $query_classification->result_array();
        $results['ha']              = $query_ha->result_array();
        $results['gfp']             = $query_gfp->result_array();

        return $results;
    }
    function readDataDistrictOld($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        if ( ! empty($partner)) {
            $cpgs = $this->get_cpgs($partner);
            if ( ! empty($cpgs)) {
                $where_cpg .= " AND aa.`CPGid` IN (" . $cpgs . ")";
                $where .= $where_cpg;
            }
        }
        if ($prov != '') {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            $group = 'GROUP BY substr(aa.VillageID,1,4)';
        }
        if ($priv == '') {
            $where .= ' and substr(aa.VillageID,1,4) in (%s)';
            $group = 'GROUP BY aa.SubDistrictID';
        } else {
            $where .= ' and substr(aa.VillageID,1,4)=?';
            $group = 'GROUP BY aa.SubDistrictID';
        }
        $dis = explode(',', $district);
        for ($i = 0; $i < sizeof($dis); $i++) {
            $di = explode('##', $dis[$i]);
            $dist[] = $di[0];
        }
        $query_cpg          = $this->db->query(sprintf(sprintf($this->main_cpg, $where), implode(',', $dist)), array($priv));
        $query_farmer       = $this->db->query(sprintf(sprintf($this->main_farmer, $where), implode(',', $dist)), array($priv));
        $query_luas         = $this->db->query(sprintf(sprintf($this->main_luas, $where), implode(',', $dist)), array($priv));
        $query_pohon        = $this->db->query(sprintf(sprintf($this->main_pohon, $where), implode(',', $dist)), array($priv));
        $query_total        = $this->db->query(sprintf(sprintf($this->main_total, $where), implode(',', $dist)), array($priv));
        $query_training     = $this->db->query(sprintf(sprintf($this->main_training, $where), implode(',', $dist)), array($priv));
        $query_usia         = $this->db->query(sprintf(sprintf($this->main_usia, $where, $group), implode(',', $dist)), array($priv));
        $query_ukuran       = $this->db->query(sprintf(sprintf($this->main_ukuran, $where, $group), implode(',', $dist)), array($priv));
        $query_perempuan    = $this->db->query(sprintf(sprintf($this->main_perempuan, $where), implode(',', $dist)), array($priv));
        $query_ha           = $this->db->query(sprintf(sprintf($this->main_ha, $where), implode(',', $dist)), array($priv));
        $query_gfp          = $this->db->query(sprintf(sprintf($this->main_gfp, $where), implode(',', $dist)), array($priv));

        $query_classification = $this->db->query(sprintf(sprintf($this->main_classification, $where), implode(',', $dist)), array($priv));
        $where = str_replace($where_cpg, '', $where);
        // echo '<pre>'; print_r($where); echo '</pre>'; exit;
        $query_ketiga = $this->db->query(sprintf(sprintf($this->main_ketiga,
          $where, // 0
          $where, $where_cpg, // 1
          $where, $where_cpg, $group,  // 2
          $where, $where_cpg, // 3
          $where, $where_cpg, // 4
          $where, $where_cpg, $group // 5
          ),
            implode(',', $dist), implode(',', $dist), implode(',', $dist), implode(',', $dist), implode(',', $dist), implode(',', $dist)),
            array($priv, $priv, $priv, $priv, $priv, $priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $results['cpg']             = $query_cpg->result_array();
        $results['farmer']          = $query_farmer->result_array();
        $results['luas']            = $query_luas->result_array();
        $results['pohon']           = $query_pohon->result_array();
        $results['total']           = $query_total->result_array();
        $results['training']        = $query_training->result_array();
        $results['usia']            = $query_usia->result_array();
        $results['ukuran']          = $query_ukuran->result_array();
        $results['perempuan']       = $query_perempuan->result_array();
        $results['ketiga']          = $query_ketiga->result_array();
        $results['classification']  = $query_classification->result_array();
        $results['ha']              = $query_ha->result_array();
        $results['gfp']             = $query_gfp->result_array();
        return $results;
    }
    function readDataDistrictDemographic($user, $district, $priv = '', $petani = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_age                  = $this->db->query(sprintf(sprintf($this->demographic_age, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        $query_gender               = $this->db->query(sprintf(sprintf($this->demographic_gender, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_farmer               = $this->db->query(sprintf(sprintf($this->demographic_farmer, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));
        $query_edu                  = $this->db->query(sprintf(sprintf($this->demographic_edu, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_kedua                = $this->db->query(sprintf(sprintf($this->demographic_kedua, $LEFT, $where), implode(',', $dist)), array($priv));

        $query_poverty              = $this->db->query(sprintf(sprintf($this->demographic_poverty, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        // $query_poverty_baseline  = $this->db->query(sprintf(sprintf($this->demographic_poverty_baseline,$label,$LEFT,$where,$groupby), implode(',', $dist)), array($priv));
        // $query_poverty_postline  = $this->db->query(sprintf(sprintf($this->demographic_poverty_postline,$label,$LEFT,$where,$groupby), implode(',', $dist)), array($priv));
        $query_household            = $this->db->query(sprintf(sprintf($this->demographic_household, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_hh_size              = $this->db->query(sprintf(sprintf($this->demographic_hh_size, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_cook_fuel            = $this->db->query(sprintf(sprintf($this->demographic_cook_fuel, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $results['kedua'] = $query_kedua->result_array();
        $results['age'] = $query_age->result_array();
        $results['gender'] = $query_gender->result_array();
        $results['farmer'] = $query_farmer->result_array();
        $results['edu'] = $query_edu->result_array();
        $results['poverty'] = $query_poverty->result_array();
        // $results['poverty_baseline']     = $query_poverty_baseline->result_array();
        // $results['poverty_postline']     = $query_poverty_postline->result_array();
        $results['household'] = $query_household->result_array();
        $results['hh_size'] = $query_hh_size->result_array();
        $results['cook_fuel'] = $query_cook_fuel->result_array();

        return $results;
    }
    function readDataGroups($prov = '', $kab = ''){
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' JOIN ktv_province on ProvinceID=substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
            $where .= ' and substr(kcf.VillageID,1,2)=?';
        } else {
            $label = 'SubDistrict';
            // $LEFT .= ' JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
            $where = ' and substr(kcf.VillageID,1,4)=?';
        }
        if ($kab != '') $prov = $kab;
        $query_kelompok             = $this->db->query(sprintf($this->group_kelompok, $label, $LEFT, $where), array($prov));
        echo '<pre>query_kelompok
        : '; print_r($this->db->last_query()); echo '</pre>';
        // $query_kelamin           = $this->db->query($sql_kelamin, array($prov));
        $query_nurseries            = $this->db->query(sprintf($this->group_nurseries, $label, $LEFT, $where), array($prov));
        echo '<pre>query_nurseries :
        '; print_r($this->db->last_query()); echo '</pre>';
        // $query_seedling          = $this->db->query($sql_seedling, array($prov));
        $query_seedling             = $this->db->query(sprintf($this->group_seedling, $label, $LEFT, $where), array($prov));
        echo '<pre>query_seedling :
        '; print_r($this->db->last_query()); echo '</pre>';
        // $query_pemilik           = $this->db->query($sql_pemilik, array($prov));
        $query_pemilik              = $this->db->query(sprintf($this->group_pemilik, $label, $LEFT, $where), array($prov));
        echo '<pre>query_pemilik :
        '; print_r($this->db->last_query()); echo '</pre>';
        // $query_kapasitas         = $this->db->query($sql_kapasitas, array($prov));
        $query_kapasitas            = $this->db->query(sprintf($this->group_kapasitas, $label, $LEFT, $where), array($prov));
        echo '<pre>query_kapasitas :
        '; print_r($this->db->last_query()); echo '</pre>';
        // $query_koperasi          = $this->db->query($sql_koperasi, array($prov));
        $query_koperasi             = $this->db->query(sprintf($this->group_koperasi, $label, $LEFT, $where), array($prov));
        echo '<pre>query_koperasi :
        '; print_r($this->db->last_query()); echo '</pre>';
        // $query_trader            = $this->db->query($sql_trader, array($prov));
        $query_trader               = $this->db->query(sprintf($this->group_trader, $label, $LEFT, $where), array($prov));
        echo '<pre>query_trader :
        '; print_r($this->db->last_query()); echo '</pre>';
        // $query_established_cpg   = $this->db->query($sql_established_cpg, array($prov));
        $query_established_cpg      = $this->db->query(sprintf($this->group_established_cpg, $label, $LEFT, $where), array($prov));
        echo '<pre>query_established_cpg :
        '; print_r($this->db->last_query()); echo '</pre>';
        exit;
        $results['kelompok']            = $query_kelompok->result_array();
        // $results['kelamin']          = $query_kelamin->result_array();
        $results['nurseries']           = $query_nurseries->result_array();
        $results['seedling']            = $query_seedling->result_array();
        $results['pemilik']             = $query_pemilik->result_array();
        $results['kapasitas']           = $query_kapasitas->result_array();
        $results['koperasi']            = $query_koperasi->result_array();
        $results['trader']              = $query_trader->result_array();
        $results['established_cpg']     = $query_established_cpg->result_array();
        return $results;
    }

    function readDataDistrictGroupsOld($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $cpgs = '';
        if ( ! empty($partner)) {
            $cpgs = $this->get_cpgs($partner);
            if ( ! empty($cpgs)) {
                $where .= " AND kcf.`CPGid` IN (" . $cpgs . ")";
            }
        }
        if ($prov != '') {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
        }
        if ($priv == '') {
            $sql_kelompok = "
SELECT
  District AS label,
  COUNT(kcf.`CPGid`) AS total,
 SUM(IF(kcf.`AdaPengurus` = 1,1,0)) AS ada_pengurus,
 SUM(IF(kcf.`AdaPengurus` != 1,1,0)) AS tidak_ada_pengurus,
 SUM(IF(kcf.`Ketua`,1,0)) AS ketua,
 SUM(IF(kcf.`Ketua` AND fa.`Gender` = 1,1,0)) AS ketua_m,
 SUM(IF(kcf.`Ketua` AND fa.`Gender` = 2,1,0)) AS ketua_f,
 SUM(IF(kcf.`Sekretaris`,1,0)) AS sekretaris,
 SUM(IF(kcf.`Sekretaris` AND fb.`Gender` = 1,1,0)) AS sekretaris_m,
 SUM(IF(kcf.`Sekretaris` AND fb.`Gender` = 2,1,0)) AS sekretaris_f,
 SUM(IF(kcf.`Bendahara`,1,0)) AS bendahara,
 SUM(IF(kcf.`Bendahara` AND fc.`Gender` = 1,1,0)) AS bendahara_m,
 SUM(IF(kcf.`Bendahara` AND fc.`Gender` = 2,1,0)) AS bendahara_f
FROM
(
SELECT
 cpg.*
FROM ktv_cpg cpg
JOIN `ktv_cpg_batch_trainings` cbt ON cbt.`CPGid` = cpg.`CPGid` AND TrainingStart > 0
GROUP BY cpg.`CPGid`
) kcf
  LEFT JOIN ktv_farmer_view fa ON fa.`FarmerID` = kcf.Ketua
  LEFT JOIN ktv_farmer_view fb ON fb.`FarmerID` = kcf.Sekretaris
  LEFT JOIN ktv_farmer_view fc ON fc.`FarmerID` = kcf.Bendahara
  LEFT JOIN ktv_district on DistrictID=substr(kcf.VillageID,1,4)
WHERE
  kcf.VillageID
  %s
  AND substr(kcf.VillageID,1,4) in (%s)
GROUP BY label
ORDER BY label
            ";
            $sql_kelamin = "
            SELECT District label,sum(if(Gender='1',1,0)) male,sum(if(Gender='2',1,0)) female
            from ktv_cpg kc
            LEFT JOIN ktv_district on DistrictID=substr(kc.VillageID,1,4)
            inner JOIN ktv_cpg_batch_trainings kcbt on kcbt.CPGid=kc.CPGid
            inner JOIN ktv_farmer_view kcf on kcf.FarmerID=kcbt.KeyFarmerID
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND kcbt.CPGtrainingsID=1 and substr(kc.VillageID,1,4) in (%s)
            GROUP BY substr(kc.VillageID,1,4)
            ORDER BY label";
            $sql_nurseries = "
            SELECT District label,count(NurseryID) total
            from ktv_nursery kcn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kcn.Responsible
            LEFT JOIN ktv_district on DistrictID=substr(kcf.VillageID,1,4)
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(VillageID,1,4) in (%s) and District is not null
            GROUP BY substr(kcf.VillageID,1,4)
            ORDER BY label";
            $sql_seedling = "
            SELECT District label,sum(Kapasitas*2) total
            from ktv_nursery kcn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kcn.Responsible
            LEFT JOIN ktv_district on DistrictID=substr(kcf.VillageID,1,4)
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(kcf.VillageID,1,4) in (%s) and District is not null
            GROUP BY substr(kcf.VillageID,1,4)
            ORDER BY label";
            $sql_pemilik = "
            SELECT ObjType label,count(NurseryID) total
            from ktv_nursery kn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kn.ObjID and ObjType='farmer'
            LEFT JOIN ktv_cpg kc on kc.CPGid=kn.ObjID and ObjType='cpg'
            LEFT JOIN ktv_traders kt on kt.TraderID=kn.ObjID and ObjType='trader'
            LEFT JOIN ktv_cooperatives kp on kp.CoopID=kn.ObjID and ObjType='koperasi'
            LEFT JOIN ktv_warehouse kw on kw.WarehouseID=kn.ObjID and ObjType='warehouse'
            WHERE ObjType is not null AND kcf.StatusCode = 'active'
               %s
               AND substr(IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,4) in (%s)
            group by ObjType";
            $sql_kapasitas = "
            SELECT District label,
               sum(IF(ObjType='farmer',Kapasitas*2,0)) farmer,sum(IF(ObjType='cpg',Kapasitas*2,0)) cpg,
               sum(IF(ObjType='koperasi',Kapasitas*2,0)) koperasi,sum(IF(ObjType='trader',Kapasitas*2,0)) trader,
               sum(IF(ObjType='warehouse',Kapasitas*2,0)) warehouse
            from ktv_nursery kn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kn.ObjID and ObjType='farmer'
            LEFT JOIN ktv_cpg kc on kc.CPGid=kn.ObjID and ObjType='cpg'
            LEFT JOIN ktv_traders kt on kt.TraderID=kn.ObjID and ObjType='trader'
            LEFT JOIN ktv_cooperatives kp on kp.CoopID=kn.ObjID and ObjType='koperasi'
            LEFT JOIN ktv_warehouse kw on kw.WarehouseID=kn.ObjID and ObjType='warehouse'
            LEFT JOIN ktv_district on substr(IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,4)=DistrictID
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,2) in (%s)
            group by substr(IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,4)
            ORDER BY label";
            $sql_koperasi = "
SELECT
  District label,
  COUNT(DISTINCT kcf.`CoopID`) AS total,
  COUNT(DISTINCT IF(s.`StaffID`,kcf.`CoopID`,NULL)) AS have_management,
  COUNT(DISTINCT IF(s.`StaffID` IS NULL,kcf.`CoopID`,NULL)) AS dont_have_management,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas', 1, 0)) AS total_staff,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) AS total_staff_m,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) AS total_staff_f,
  SUM(IF(s.`Position` = 'Ketua' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) ketua_m,
  SUM(IF(s.`Position` = 'Ketua' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) ketua_f,
  SUM(IF(s.`Position` = 'Wakil Ketua' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) wakil_ketua_m,
  SUM(IF(s.`Position` = 'Wakil Ketua' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) wakil_ketua_f,
  SUM(IF(s.`Position` = 'Sekretaris' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) sekretaris_m,
  SUM(IF(s.`Position` = 'Sekretaris' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) sekretaris_f,
  SUM(IF(s.`Position` = 'Wakil Sekretaris' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) wakil_sekretaris_m,
  SUM(IF(s.`Position` = 'Wakil Sekretaris' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) wakil_sekretaris_f,
  SUM(IF(s.`Position` = 'Bendahara' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) bendahara_m,
  SUM(IF(s.`Position` = 'Bendahara' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) bendahara_f,
  SUM(IF(s.`Position` = 'Wakil Bendahara' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) wakil_bendahara_m,
  SUM(IF(s.`Position` = 'Wakil Bendahara' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) wakil_bendahara_f
FROM `ktv_cooperatives` kcf
LEFT JOIN `ktv_cooperative_staff` s ON s.`CoopID` = kcf.`CoopID`
LEFT JOIN `ktv_farmer_view` f ON f.`FarmerID` = s.`FarmerID`
LEFT JOIN ktv_district kp on kp.DistrictID=substr(kcf.VillageID,1,4)
where 1 = 1 AND f.StatusCode = 'active'
%s
and substr(kcf.VillageID,1,4) in (%s) and District is not null
GROUP BY label
ORDER BY label
          ";
            $sql_trader = "
SELECT
   District as label,
   COUNT(kcf.TraderID) AS total,
   SUM(IF(`Sex`=1,1,0)) AS male,
   SUM(IF(`Sex`=2,1,0)) AS female
FROM ktv_traders kcf
LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)
            WHERE 1 = 1 AND District
            %s
            and substr(VillageID,1,4) in (%s)
            GROUP BY substr(VillageID,1,4)
            ORDER BY label";
            $sql_established_cpg = "
SELECT
   District label
   ,COUNT(DISTINCT kcf.CPGid) total_cpg
   ,MIN(`year`) AS min_year
   ,MAX(`year`) AS max_year
   ,SUM(IF(kcf.`year`=2010,1,0)) '2010'
   ,SUM(IF(kcf.`year`=2011,1,0)) '2011'
   ,SUM(IF(kcf.`year`=2012,1,0)) '2012'
   ,SUM(IF(kcf.`year`=2013,1,0)) '2013'
   ,SUM(IF(kcf.`year`=2014,1,0)) '2014'
   ,SUM(IF(kcf.`year`=2015,1,0)) '2015'
   ,SUM(IF(kcf.`year`=2016,1,0)) '2016'
FROM (
SELECT
  cpg.`CPGid`,
  COUNT(DISTINCT CpgBatchTrainingID) AS total_training,
  VillageID,
  YEAR(MIN(bt.`TrainingStart`)) AS `year`
FROM `ktv_cpg_batch_trainings` bt
LEFT JOIN `ktv_cpg` cpg ON bt.`CPGid` = cpg.`CPGid`
WHERE VillageID AND bt.`TrainingStart` > 0
GROUP BY cpg.`CPGid`
ORDER BY CpgBatchTrainingID ASC
) kcf
LEFT JOIN ktv_district kp on kp.DistrictID=substr(VillageID,1,4)
WHERE 1 = 1
  %s
  AND substr(kcf.VillageID,1,4) in (%s) and District is not null
GROUP BY label
";
        } else {
            $sql_kelompok = "
SELECT
  sd.SubDistrict AS label,
  COUNT(kcf.`CPGid`) AS total,
 SUM(IF(kcf.`AdaPengurus` = 1,1,0)) AS ada_pengurus,
 SUM(IF(kcf.`AdaPengurus` != 1,1,0)) AS tidak_ada_pengurus,
 SUM(IF(kcf.`Ketua`,1,0)) AS ketua,
 SUM(IF(kcf.`Ketua` AND fa.`Gender` = 1,1,0)) AS ketua_m,
 SUM(IF(kcf.`Ketua` AND fa.`Gender` = 2,1,0)) AS ketua_f,
 SUM(IF(kcf.`Sekretaris`,1,0)) AS sekretaris,
 SUM(IF(kcf.`Sekretaris` AND fb.`Gender` = 1,1,0)) AS sekretaris_m,
 SUM(IF(kcf.`Sekretaris` AND fb.`Gender` = 2,1,0)) AS sekretaris_f,
 SUM(IF(kcf.`Bendahara`,1,0)) AS bendahara,
 SUM(IF(kcf.`Bendahara` AND fc.`Gender` = 1,1,0)) AS bendahara_m,
 SUM(IF(kcf.`Bendahara` AND fc.`Gender` = 2,1,0)) AS bendahara_f
FROM
(
SELECT
 cpg.*
FROM ktv_cpg cpg
JOIN `ktv_cpg_batch_trainings` cbt ON cbt.`CPGid` = cpg.`CPGid` AND TrainingStart > 0
GROUP BY cpg.`CPGid`
) kcf
  LEFT JOIN ktv_farmer_view fa ON fa.`FarmerID` = kcf.Ketua
  LEFT JOIN ktv_farmer_view fb ON fb.`FarmerID` = kcf.Sekretaris
  LEFT JOIN ktv_farmer_view fc ON fc.`FarmerID` = kcf.Bendahara
  LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID=SUBSTR(kcf.VillageID,1,7)
WHERE
  kcf.VillageID
  %s
  AND substr(kcf.VillageID,1,4)=?
GROUP BY label
ORDER BY label
            ";
            $sql_kelamin = "
            SELECT SubDistrict label,sum(if(Gender='1',1,0)) male,sum(if(Gender='2',1,0)) female
            from ktv_cpg kc
            LEFT JOIN ktv_subdistrict on SubDistrictID=substr(kc.VillageID,1,7)
            inner JOIN ktv_cpg_batch_trainings kcbt on kcbt.CPGid=kc.CPGid
            inner JOIN ktv_farmer_view kcf on kcf.FarmerID=kcbt.KeyFarmerID
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND kcbt.CPGtrainingsID=1 and substr(kc.VillageID,1,4)=?
            GROUP BY substr(kc.VillageID,1,7)
            ORDER BY label";
            $sql_nurseries = "SELECT SubDistrict label,count(NurseryID) total
            from ktv_nursery kcn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kcn.Responsible
            -- LEFT JOIN ktv_subdistrict on SubDistrictID=kcf.SubDistrictID
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(VillageID,1,4)=? and SubDistrict is not null
            GROUP BY kcf.SubDistrictID
            ORDER BY label";
            $sql_seedling = "SELECT SubDistrict label,sum(Kapasitas*2) total
            from ktv_nursery kcn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kcn.Responsible
            -- LEFT JOIN ktv_subdistrict on SubDistrictID=kcf.SubDistrictID
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(kcf.VillageID,1,4)=? and SubDistrict is not null
            GROUP BY kcf.SubDistrictID
            ORDER BY label";
            $sql_pemilik = "SELECT ObjType label,count(NurseryID) total
            from ktv_nursery kn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kn.ObjID and ObjType='farmer'
            LEFT JOIN ktv_cpg kc on kc.CPGid=kn.ObjID and ObjType='cpg'
            LEFT JOIN ktv_traders kt on kt.TraderID=kn.ObjID and ObjType='trader'
            LEFT JOIN ktv_cooperatives kp on kp.CoopID=kn.ObjID and ObjType='koperasi'
            LEFT JOIN ktv_warehouse kw on kw.WarehouseID=kn.ObjID and ObjType='warehouse'
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,4)=?
            group by ObjType";
            $sql_kapasitas = "SELECT sd.SubDistrict label,
               sum(IF(ObjType='farmer',Kapasitas*2,0)) farmer,sum(IF(ObjType='cpg',Kapasitas*2,0)) cpg,
               sum(IF(ObjType='koperasi',Kapasitas*2,0)) koperasi,sum(IF(ObjType='trader',Kapasitas*2,0)) trader,
               sum(IF(ObjType='warehouse',Kapasitas*2,0)) warehouse
            from ktv_nursery kn
            LEFT JOIN ktv_farmer_view kcf on kcf.FarmerID=kn.ObjID and ObjType='farmer'
            LEFT JOIN ktv_cpg kc on kc.CPGid=kn.ObjID and ObjType='cpg'
            LEFT JOIN ktv_traders kt on kt.TraderID=kn.ObjID and ObjType='trader'
            LEFT JOIN ktv_cooperatives kp on kp.CoopID=kn.ObjID and ObjType='koperasi'
            LEFT JOIN ktv_warehouse kw on kw.WarehouseID=kn.ObjID and ObjType='warehouse'
            LEFT JOIN ktv_subdistrict sd on substr(IFNULL(kcf.SubDistrictID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,7)=sd.SubDistrictID
            WHERE 1 = 1 AND kcf.StatusCode = 'active'
            %s
            AND substr(IFNULL(kcf.VillageID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,4)=?
            group by substr(IFNULL(kcf.SubDistrictID,IFNULL(kc.VillageID,IFNULL(kt.VillageID,IFNULL(kp.VillageID,IFNULL(kw.VillageID,''))))),1,7)
            ORDER BY label";
            $sql_koperasi = "SELECT
  SubDistrict label,
  COUNT(DISTINCT kcf.`CoopID`) AS total,
  COUNT(DISTINCT IF(s.`StaffID`,kcf.`CoopID`,NULL)) AS have_management,
  COUNT(DISTINCT IF(s.`StaffID` IS NULL,kcf.`CoopID`,NULL)) AS dont_have_management,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas', 1, 0)) AS total_staff,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) AS total_staff_m,
  SUM(IF(s.`Position` != 'Ketua Badan Pengawas' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) AS total_staff_f,
  SUM(IF(s.`Position` = 'Ketua' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) ketua_m,
  SUM(IF(s.`Position` = 'Ketua' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) ketua_f,
  SUM(IF(s.`Position` = 'Wakil Ketua' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) wakil_ketua_m,
  SUM(IF(s.`Position` = 'Wakil Ketua' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) wakil_ketua_f,
  SUM(IF(s.`Position` = 'Sekretaris' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) sekretaris_m,
  SUM(IF(s.`Position` = 'Sekretaris' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) sekretaris_f,
  SUM(IF(s.`Position` = 'Wakil Sekretaris' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) wakil_sekretaris_m,
  SUM(IF(s.`Position` = 'Wakil Sekretaris' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) wakil_sekretaris_f,
  SUM(IF(s.`Position` = 'Bendahara' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) bendahara_m,
  SUM(IF(s.`Position` = 'Bendahara' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) bendahara_f,
  SUM(IF(s.`Position` = 'Wakil Bendahara' AND (f.`Gender` = 1 OR s.`StaffGender` = 1), 1, 0)) wakil_bendahara_m,
  SUM(IF(s.`Position` = 'Wakil Bendahara' AND (f.`Gender` = 2 OR s.`StaffGender` = 2), 1, 0)) wakil_bendahara_f
FROM `ktv_cooperatives` kcf
LEFT JOIN `ktv_cooperative_staff` s ON s.`CoopID` = kcf.`CoopID`
LEFT JOIN `ktv_farmer_view` f ON f.`FarmerID` = s.`FarmerID`
-- LEFT JOIN ktv_subdistrict kp on kp.SubDistrictID=kcf.SubDistrictID
where 1 = 1 AND kcf.StatusCode = 'active'
%s
AND substr(kcf.VillageID,1,4)=? and SubDistrict is not null
GROUP BY label
ORDER BY label
          ";
            $sql_trader = "SELECT
   IFNULL(SubDistrict,'none') as label,
   COUNT(kcf.TraderID) AS total,
   SUM(IF(`Sex`=1,1,0)) AS male,
   SUM(IF(`Sex`=2,1,0)) AS female
FROM ktv_traders kcf
            LEFT JOIN ktv_subdistrict on SubDistrictID=SUBSTR(kcf.VillageID,1,7)
            WHERE 1 = 1
            %s
            AND substr(VillageID,1,4)=?
            GROUP BY SubDistrictID
            ORDER BY label";
            $sql_established_cpg = "SELECT
   SubDistrict label
   ,COUNT(DISTINCT kcf.CPGid) total_cpg
   ,MIN(`year`) AS min_year
   ,MAX(`year`) AS max_year
   ,SUM(IF(kcf.`year`=2010,1,0)) '2010'
   ,SUM(IF(kcf.`year`=2011,1,0)) '2011'
   ,SUM(IF(kcf.`year`=2012,1,0)) '2012'
   ,SUM(IF(kcf.`year`=2013,1,0)) '2013'
   ,SUM(IF(kcf.`year`=2014,1,0)) '2014'
   ,SUM(IF(kcf.`year`=2015,1,0)) '2015'
   ,SUM(IF(kcf.`year`=2016,1,0)) '2016'
FROM (
SELECT
  cpg.`CPGid`,
  COUNT(DISTINCT CpgBatchTrainingID) AS total_training,
  VillageID,
  YEAR(MIN(bt.`TrainingStart`)) AS `year`
FROM `ktv_cpg_batch_trainings` bt
LEFT JOIN `ktv_cpg` cpg ON bt.`CPGid` = cpg.`CPGid`
WHERE VillageID AND bt.`TrainingStart` > 0
GROUP BY cpg.`CPGid`
ORDER BY CpgBatchTrainingID ASC
) kcf
LEFT JOIN ktv_subdistrict kp on kp.SubDistrictID=SUBSTR(kcf.VillageID,1,7)
WHERE 1 = 1
  %s
  AND substr(kcf.VillageID,1,4)=? and SubDistrict is not null
GROUP BY label
";
        }
        $dis = explode(',', $district);
        for ($i = 0; $i < sizeof($dis); $i++) {
            $di = explode('##', $dis[$i]);
            $dist[] = $di[0];
        }

        $where = '';
        $LEFT = '';
        $where .= ' and substr(kcf.VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } else {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
                $groupby = 'substr(kcf.VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(kcf.VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where_cpg = " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
            $where .= $where_cpg;
        }

        $query_kelompok         = $this->db->query(sprintf(sprintf($this->group_kelompok, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        // $query_kelamin       = $this->db->query(sprintf(sprintf($this->group_kelamin, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        $query_nurseries        = $this->db->query(sprintf(sprintf($this->group_nurseries, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        $query_seedling         = $this->db->query(sprintf(sprintf($this->group_seedling, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        $query_established_cpg  = $this->db->query(sprintf(sprintf($this->group_established_cpg, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        // query di bawah ini tidak berhubungan dengan CPG, so remove it from where clause
        $where                  = str_replace($where_cpg, '', $where);
        $query_pemilik          = $this->db->query(sprintf(sprintf($this->group_pemilik, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        $query_kapasitas        = $this->db->query(sprintf(sprintf($this->group_kapasitas, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        $query_trader           = $this->db->query(sprintf(sprintf($this->group_trader, $label,$LEFT,$where), implode(',', $dist)), array($priv));
        $query_koperasi         = $this->db->query(sprintf(sprintf($this->group_koperasi, $label,$LEFT,$where), implode(',', $dist)), array($priv));

        $results['kelompok'] = $query_kelompok->result_array();
        // $results['kelamin']     = $query_kelamin->result_array();
        $results['nurseries'] = $query_nurseries->result_array();
        $results['seedling'] = $query_seedling->result_array();
        $results['pemilik'] = $query_pemilik->result_array();
        $results['kapasitas'] = $query_kapasitas->result_array();
        $results['koperasi'] = $query_koperasi->result_array();
        $results['trader'] = $query_trader->result_array();
        $results['established_cpg'] = $query_established_cpg->result_array();

        return $results;
    }
    function readDataGardenOld($prov = '', $kab = '', $petani = '', $tahun = '', $survey = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $LEFT .= " LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' AND YEAR(ExternalDate) = {$tahun} group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $where .= " AND farid is not null AND VillageID";
        } elseif ($petani == '2') {
            $LEFT .= " LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $where .= " AND farid is null AND VillageID";
        }
        switch ($survey) {
          case '0':
            $qps = ' AND a.SurveyNr = 0';
            break;
          case '1':
            $qps = ' AND a.SurveyNr = z.LatestSurveyNr AND a.SurveyNr > 0';
            break;
          case '2':
            $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
            break;
        }
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province kd on kd.ProvinceID=substr(kcf.VillageID,1,2)';
            $where = $qp;
            $groupby = 'substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' LEFT JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
            $where = $qp . ' and substr(kcf.VillageID,1,2)=?';
            $groupby = 'substr(kcf.VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT .= ' LEFT JOIN ktv_subdistrict kd on kd.SubDistrictID=kcf.SubDistrictID';
            $where = $qp . ' and substr(kcf.VillageID,1,4)=?';
            $groupby = 'kcf.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
       // printf($this->garden_komposisi, $LEFT, $where . $qps);exit;
        $query_luas             = $this->db->query(sprintf($this->garden_luas, $label, $LEFT, $where . $qps, $groupby), array($prov));
        $query_produksi         = $this->db->query(sprintf($this->garden_produksi, $label, $LEFT, $where . $qps, $groupby), array($prov));
        $query_size             = $this->db->query(sprintf($this->garden_size, $label, $LEFT, $where . $qps), array($prov));
        $query_avg              = $this->db->query(sprintf($this->garden_avg, $label, $LEFT, $where, $groupby), array($prov));
        $query_komposisi        = $this->db->query(sprintf($this->garden_komposisi, $LEFT, $where . $qps), array($prov));
        $query_produktivity     = $this->db->query(sprintf($this->garden_produktivity, $label, $LEFT, $where . $qps, $groupby), array($prov));
        $query_lain             = $this->db->query(sprintf($this->garden_lain, $LEFT, $where . $qps), array($prov));
        $query_tm               = $this->db->query(sprintf($this->garden_tm, $label, $LEFT, $where . $qps, $groupby), array($prov));
        $query_yield            = $this->db->query(sprintf($this->garden_yield, $label, $LEFT, $where . $qps, $groupby), array($prov));
        $query_land             = $this->db->query(sprintf($this->garden_land, $label, $LEFT, $where . $qps, $groupby), array($prov));

        $results['luas']            = $query_luas->result_array();
        $results['produksi']        = $query_produksi->result_array();
        $results['size']            = $query_size->result_array();
        $results['avg']             = $query_avg->result_array();
        $results['komposisi']       = $query_komposisi->result_array();
        $results['produktivity']    = $query_produktivity->result_array();
        $results['lain']            = $query_lain->result_array();
        $results['tm']              = $query_tm->result_array();
        $results['yield']           = $query_yield->result_array();
        $results['land']            = $query_land->result_array();

        return $results;
    }

    function readDataDistrictGardenOld($user, $district, $priv = '', $petani = '', $partner = '', $prov = '', $tahun = '', $survey = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $LEFT .= " LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' AND YEAR(ExternalDate) = {$tahun} group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $where .= " AND farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= " LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $where .= " AND farid is null";
        }
        switch ($survey) {
          case '0':
            $qps = ' AND a.SurveyNr = 0';
            break;
          case '1':
            $qps = ' AND a.SurveyNr = z.LatestSurveyNr AND a.SurveyNr > 0';
            break;
          case '2':
            $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
            break;
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_luas             = $this->db->query(sprintf(sprintf($this->garden_luas, $label, $LEFT, $where . $qps, $groupby), implode(',', $dist)), array($priv));
        $query_produksi         = $this->db->query(sprintf(sprintf($this->garden_produksi, $label, $LEFT, $where . $qps, $groupby), implode(',', $dist)), array($priv));
        $query_size             = $this->db->query(sprintf(sprintf($this->garden_size, $label, $LEFT, $where . $qps), implode(',', $dist)), array($priv));
        $query_avg              = $this->db->query(sprintf(sprintf($this->garden_avg, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_komposisi        = $this->db->query(sprintf(sprintf($this->garden_komposisi, $LEFT, $where . $qps), implode(',', $dist)), array($priv));
        $query_produktivity     = $this->db->query(sprintf(sprintf($this->garden_produktivity, $label, $LEFT, $where . $qps, $groupby), implode(',', $dist)), array($priv));
        $query_lain             = $this->db->query(sprintf(sprintf($this->garden_lain, $LEFT, $where . $qps), implode(',', $dist)), array($priv));
        $query_tm               = $this->db->query(sprintf(sprintf($this->garden_tm, $label, $LEFT, $where . $qps, $groupby), implode(',', $dist)), array($priv));
        $query_yield            = $this->db->query(sprintf(sprintf($this->garden_yield, $label, $LEFT, $where . $qps, $groupby), implode(',', $dist)), array($priv));
        $query_land             = $this->db->query(sprintf(sprintf($this->garden_land, $label, $LEFT, $where . $qps, $groupby), implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $results['luas'] = $query_luas->result_array();
        $results['produksi'] = $query_produksi->result_array();
        $results['size'] = $query_size->result_array();
        $results['avg'] = $query_avg->result_array();
        $results['komposisi'] = $query_komposisi->result_array();
        $results['produktivity'] = $query_produktivity->result_array();
        $results['lain'] = $query_lain->result_array();
        $results['tm'] = $query_tm->result_array();
        $results['yield'] = $query_yield->result_array();
        $results['land'] = $query_land->result_array();
        return $results;
    }
    function readDataAgriinputOld($prov = '', $kab = '')
    {
        if ($petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif ($petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
            $groupby = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where = 'and substr(VillageID,1,2)=?';
            $groupby = 'substr(VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            // $LEFT = 'JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
            $where = 'and substr(VillageID,1,4)=?';
            $groupby = 'SubDistrictID';
        }
        if ($kab != '') $prov = $kab;

        $query_jumlah = $this->db->query(sprintf($this->sql_agrinput_data, $label, $LEFT, $where, $groupby), array($prov));
        $results['data'] = $query_jumlah->result_array();

        $query_fertilzer = $this->db->query(sprintf($this->sql_env_fertilizer, $label, $LEFT, $where, $groupby), array($prov));
        $results['fertilizer'] = $query_fertilzer->result_array();

        $query_agriinput = $this->db->query(sprintf($this->sql_agriinput, $label, $LEFT, $where, $groupby), array($prov));
        $results['agriinput'] = $query_agriinput->result_array();

        return $results;
    }

    function readDataDistrictAgriinputOld($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
            }
        }

        $dist[] = $user['districtPartner'];
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }

        $query_jumlah = $this->db->query(sprintf(sprintf($this->sql_agrinput_data, $label, $LEFT, $where, $groupby),
            implode(',', $dist)), array($priv));

        $results['data'] = $query_jumlah->result_array();

        $query_fertilzer = $this->db->query(sprintf(sprintf($this->sql_env_fertilizer, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['fertilizer'] = $query_fertilzer->result_array();

        $query_agriinput = $this->db->query(sprintf(sprintf($this->sql_agriinput, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['agriinput'] = $query_agriinput->result_array();

        return $results;
    }
    private function get_cpgs($partner_id)
    {
        $sql = "
SELECT
    GROUP_CONCAT(`CPGid`) AS cpgs
FROM
    `ktv_cpg_partner`
WHERE
   `PartnerID` = ?
       ";
        $query = $this->db->query($sql, array($partner_id));
        if ($query->num_rows() > 0) {
            return $query->row(0)->cpgs;
        }
    }

    public function getRegionsTraceability($user, $prov, $kab, $kec, $desa, $daer)
    {
        //cek apakah admin
        if($_SESSION['is_admin'] == "1"){
            if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_province', array('active' => '1'));
            } else if($kab=='' && $kec=='') {
                $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov, 'active' => '1'));
            } else if($kab!='' && $kec=='') {
                $this->db->SELECT('SubDistrictID AS id, SubDistrict AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_subdistrict', array('DistrictID' => $kab, 'active' => '1'));
                echo "<pre>".$this->Db->last_query();exit;
            } else if($kab!='' && $kec!='') {
                $this->db->SELECT('VillageID AS id, Village AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_village', array('SubDistrictID' => $kec, 'StatusCode' => 'active'));
            }
            return $query->result_array();
        }

        $region = array();
        if (empty($daer)) {
            if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_province', array('active' => '1'));
            } else if($kab=='' && $kec=='') {
                $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov, 'active' => '1'));
            } else if($kab!='' && $kec=='') {
                $this->db->SELECT('SubDistrictID AS id, SubDistrict AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_subdistrict', array('DistrictID' => $kab, 'active' => '1'));
            } else if($kab!='' && $kec!='') {
                $this->db->SELECT('VillageID AS id, Village AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_village', array('SubDistrictID' => $kec, 'StatusCode' => 'active'));
            }
        } else {
            // if ($_SESSION['FlagAccess'] == 1) {
            //     $daer = $user['accessStaff'];
            // } else {
            //     $daer = $user['districtPartner'];
            // }
            $daer = $user['district_access'];
            $daer = explode(',', $daer);
            $p_daer = array();
            $d_daer = array();
            foreach ($daer as $key => $value) {
                if (!in_array(substr($value, 0, 2), $p_daer)) {
                    $p_daer[] = substr($value, 0, 2);
                }
                $d_daer[] = $value;
            }
            if (empty($prov)) {
                $this->db->SELECT('ProvinceID AS id, Province AS name', FALSE);
                $this->db->where_in('ProvinceID', $p_daer);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_province', array('active' => '1'));
            } else if($kab=='' && $kec=='') {
                $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
                $this->db->where_in('DistrictID', $d_daer);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_district', array('ProvinceID' => $prov, 'active' => '1'));
            } else if($kab!='' && $kec=='') {
                $this->db->SELECT('SubDistrictID AS id, SubDistrict AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_subdistrict', array('DistrictID' => $kab, 'active' => '1'));
            } else if($kab!='' && $kec!='') {
                $this->db->SELECT('VillageID AS id, Village AS name', FALSE);
                $this->db->order_by('name', 'asc');
                $query = $this->db->get_where('ktv_village', array('SubDistrictID' => $kec, 'StatusCode' => 'active'));
            }
        }
        if ($query) {
            return $query->result_array();
        }
    }

    public function getDistrict($id)
    {
        $this->db->SELECT('DistrictID AS id, District AS name', FALSE);
        $query = $this->db->get_where('ktv_district', array('DistrictID' => $id), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getSubDistrict($id)
    {
        $this->db->SELECT('SubDistrictID AS id, SubDistrict AS name', FALSE);
        $query = $this->db->get_where('ktv_subdistrict', array('SubDistrictID' => $id), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getVillage($id)
    {
        $this->db->SELECT('Village AS id, Village AS name', FALSE);
        $query = $this->db->get_where('ktv_village', array('VillageID' => $id), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }
    
    public function listDO()
    {
        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, op.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_supplychain_org o ON o.OrgID=s.ObjID AND o.OrgType=s.ObjType
                    LEFT JOIN ktv_supplychain_org_partner op ON op.SupplychainID=o.SupplychainID
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array($_SESSION['userid']))->result_array();
        
        $sql = "SELECT DISTINCT b.PartnerID id, b.PartnerName name FROM ktv_warehouse a LEFT JOIN ktv_program_partner b ON b.PartnerID=a.PartnerID WHERE a.StatusCode='active' AND b.PartnerID NOT IN (9, 8)";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    function readstore_supplyorg()
	{
		//$this->db->where('PartnerID', $_SESSION['apmPartnerID'] );
		//$this->db->where('ObjType','processing' );
		//$this->db->select('SupplychainID as id, Name as label');
		//$st = $this->db->from('view_tc_supplychain_org')->get()->result_array();
        //echo '<pre>';echo $this->db->last_query(); die;		
        if($_SESSION['PartnerID']=='1' || $_SESSION['is_admin'] == "1"){
            $k1 = '/*'; $k2 = '*/';
        }else{
            $k1 = ''; $k2 = '';
        }
        $sql = "SELECT `SupplychainID` as id, `Name` as label
                FROM (`view_tc_supplychain_org`)
                WHERE `ObjType` =  'mill' $k1 AND PartnerID=? $k2";
        $st = $this->db->query($sql, array($_SESSION['PartnerID']))->result_array();
        //echo $this->db->last_query();die;
		return $st; 
    }
    
    function readstore_supplyorgchild()
	{
		$SQL = "select A.ChildID as id, B.Name as label from 
				ktv_tc_supplychain_org_rel A, 
				view_tc_supplychain_org B
				WHERE 
				A.ChildID = B.SupplychainID and A.ParentID = ? ";
		$st = $this->db->query($SQL, array( $this->input->get('patnerID')) )->result_array();
		//echo $this->db->last_query();die;
		if($st)
		return $st;
    }
    

    function getTransactionFarmerPerMonthNew($DateStart, $DateEnd){ 
		$montharr = $this->get_months($DateStart, $DateEnd); 
		$categories=array(); 
		
		if($_SESSION['is_admin'] == "1"){
			$d1 ='/*';	$d2 ='*/';
		}else{
			$d1 =''; $d2 ='';
		}
		
		$SQL = "SELECT
				    DATE_FORMAT(st.DateTransaction,'%Y-%m') as monthTrans,
				    vso.Name as label,  
					COUNT(DISTINCT IFNULL(st4.SupplyTransID, IFNULL(st3.SupplyTransID, IFNULL(st2.SupplyTransID, st.SUpplyTransID)))) as Totaltrans  
					 FROM
					ktv_tc_supplychain_transaction st 
					 LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
					 LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch'
					 LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
					 LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
					 LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
					 LEFT JOIN ktv_tc_supplychain_batch sb4 ON sb4.SupplyBatchID=st3.SupplyID AND st3.SupplyType='Batch'
					 LEFT JOIN ktv_tc_supplychain_transaction st4 ON st4.SupplyBatchID=sb4.SupplyBatchID 
					 LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType!='Batch'
					 LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IFNULL(sb2.SupplyOrgID, st.SupplychainID)
					 LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID 
					WHERE 1=1 AND st.DateTransaction BETWEEN ? AND ? "; 
		 
		$sshSupplychainID = $this->input->get('sshSupplychainID'); 
		$sshSupplyChildID = $this->input->get('sshSupplyChildID'); 		
		if($sshSupplychainID != 'null' && $sshSupplychainID !='false' && $sshSupplychainID != '')
		 { 				
			 if($sshSupplyChildID != 'null' && $sshSupplyChildID !='false' && $sshSupplyChildID != '')
			 {
				$SQL .= " and st.SupplychainID = '".$sshSupplyChildID."'  ";
			 }else{
				 $SQL .= " and st.SupplychainID = '".$sshSupplychainID."'  "; 
			 }
		 }  
		$SQL .=" GROUP BY DATE_FORMAT(st.DateTransaction,'%Y-%m') "; 
		$query = $this->db->query($SQL, array("$DateStart", "$DateEnd"));
		//echo '<pre>';echo $this->db->last_query(); die;
		$a=array();
			 $b=array();
			 foreach($query->result() as $key => $val){   
				 if(!array_search($val->label, $a))
				 $a[] = $val->label;  
				 $b[$val->label][$val->monthTrans] = $val->Totaltrans; 
		     }
			 
			 //print_r($b);die;
			 //Show name
			 $sname=array();
			 $unik_a =  array_unique($a); 
             for($i=0; $i<count($unik_a); $i++){ 
				  if(@$unik_a[$i] != ''){
					  $name['name'] = @$unik_a[$i];   
					  $k = @$unik_a[$i]; $x=array();
					  for($s=0; $s<count($montharr); $s++){   
							 if(@$b[$k][$montharr[$s]]){
								 $x[]= (float)$b[$k][$montharr[$s]] ; 
							 }else{
								 $x[]= 0; 
							 }  					 
					  };
					//$c = explode(",", rtrim($x,",")); 
					$name['data'] = $x;		  
					array_push($sname,$name);  
				  }
			 }
			  //echo '<pre>';
			  //print_r($sname);die;  
			 // die;
			 
			 for($i=0; $i<count($montharr); $i++){   
				  $categories[] = $montharr[$i];  
				 
			 }
			 //echo '<pre>';
			 //print_r($sname);die;  
			
		    
		
		$data['results'] = $sname;
		$data['categories'] = $categories; 
		//echo json_encode($data);die;
        return $data;    
    }

    /*****************************TRACEBILTY NEW*********************/
	function getDataProdTracebilty(){ 
		$data = array();
		$SQL ="SELECT Province as Name, 
				(
				 select COALESCE(sum(AnnualProduction),0)/1000 from  ktv_survey_plot  
				 where  a.ProvinceID = substr(VillageID,1,2)
				) as panen
				from 
				ktv_province a
				join ktv_district b ON a.ProvinceID = SUBSTR(b.DistrictID,1,2)
				join ktv_district_partner c ON b.DistrictID = c.DistrictID 
				where 1=1 AND a.StatusCode = 'active'
				";
				
		if($_SESSION['province'] !='')
		 {
			//  $SQL .=" AND c.DistrictID IN (".$_SESSION['province'].") ";
		 }
		 $SQL .=" group by a.ProvinceID";
		$result = $this->db->query($SQL)->result_array();
		// echo "<pre>".$this->db->last_query();exit; 
		$jsonData = array();
		if($result)
		{
			 foreach($result as $key => $val){
				$jsonData[] = array( $val['Name'] , abs($val['panen']) );
			 }
		} 
		return json_encode($jsonData);  
	}
    
    function get_months($date1, $date2)
	{
		$time1  = strtotime($date1);
	    $time2  = strtotime($date2);
	    $my     = date('mY', $time2);

	   $months = array(date('Y-m', $time1));

	   while($time1 < $time2) {
		  $time1 = strtotime(date('Y-m-d', $time1).' +1 month');
		  if(date('mY', $time1) != $my && ($time1 < $time2))
			 $months[] = date('Y-m', $time1);
	   }

	   $months[] = date('Y-m', $time2);
	   return $months;
    }
    
    public function getTransactionSalesPerMonth($DateStart, $DateEnd){ 
        $montharr = $this->get_months($DateStart, $DateEnd); 
        $categories=array(); 
        
        if($_SESSION['is_admin'] == "1"){
            $d1 ='/*';	$d2 ='*/';
        }else{
            $d1 =''; $d2 ='';
        }
        
        $SQL = "SELECT
                DATE_FORMAT(st.DateTransaction,'%Y-%m') as monthTrans,
                vso.Name as label,  
                COUNT(DISTINCT sb2.SupplyOrgID) TotalBuyingUnit 
                 FROM
                ktv_tc_supplychain_transaction st 
                 LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                 LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch'
                 LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                 LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
                 LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                 LEFT JOIN ktv_tc_supplychain_batch sb4 ON sb4.SupplyBatchID=st3.SupplyID AND st3.SupplyType='Batch'
                 LEFT JOIN ktv_tc_supplychain_transaction st4 ON st4.SupplyBatchID=sb4.SupplyBatchID 
                 LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType!='Batch'
                 LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IFNULL(sb2.SupplyOrgID, st.SupplychainID)
                 LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID 
                WHERE 1=1 AND st.DateTransaction BETWEEN ? AND ? "; 
                 
            $sshSupplychainID = $this->input->get('sshSupplychainID'); 
            $sshSupplyChildID = $this->input->get('sshSupplyChildID'); 		
            if($sshSupplychainID != 'null' && $sshSupplychainID !='false' && $sshSupplychainID != '')
            { 				
                 if($sshSupplyChildID != 'null' && $sshSupplyChildID !='false' && $sshSupplyChildID != '')
                 {
                    $SQL .= " and st.SupplychainID = '".$sshSupplyChildID."'  ";
                 }else{
                     $SQL .= " and st.SupplychainID = '".$sshSupplychainID."'  "; 
                 }
             } 
                 
         $SQL .=" GROUP BY st.SupplychainID, DATE_FORMAT(st.DateTransaction,'%Y-%m')";
         
         $query = $this->db->query($SQL, array("$DateStart", "$DateEnd")); 
         //echo '<pre>';echo $this->db->last_query(); die;
         $a=array();
         $b=array();
         foreach($query->result() as $key => $val){   
             if(!array_search($val->label, $a))
             $a[] = $val->label;  
             $b[$val->label][$val->monthTrans] = $val->TotalBuyingUnit; 
         }
         
         //print_r($b);die;
         //Show name
         $sname=array();
         $unik_a =  array_unique($a);
         for($i=0; $i<count($unik_a); $i++){ 
              if(@$unik_a[$i] != ''){
                  $name['name'] = @$unik_a[$i];   
                  $k = @$unik_a[$i]; $x=array();
                  for($s=0; $s<count($montharr); $s++){   
                         if(@$b[$k][$montharr[$s]]){
                             $x[]= (float)$b[$k][$montharr[$s]] ; 
                         }else{
                             $x[]= 0; 
                         }  					 
                  };
                //$c = explode(",", rtrim($x,",")); 
                $name['data'] = $x;		  
                array_push($sname,$name);  
              }
         }
         //echo '<pre>';
         //print_r($sname);die;  
         //die;
         
         for($i=0; $i<count($montharr); $i++){   
              $categories[] = $montharr[$i];  
             
         }
         //echo '<pre>';
         //print_r($sname);die;  
        
        
    
    $data['results'] = $sname;
    $data['categories'] = $categories; 
    //echo json_encode($data);die;
    return $data;  
}


function getDataTraceSalesTracebiltyMonthNew($DateStart, $DateEnd){ 
    $data = array();
    
    if($_SESSION['is_admin'] == "1"){
        $d1 ='/*';	$d2 ='*/';
    }else{
        $d1 =''; $d2 ='';
    }
    
    $SQL = "SELECT 
            vst.Name,
            (  
            SELECT 
              
                 COALESCE(SUM(ROUND(st.VolumeNetto/1000, 2)),0)
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType!='Batch'
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID 
                WHERE 1=1 
                AND vst.SupplychainID = st.SupplychainID
                 AND st.DateTransaction BETWEEN ? AND ? 
                 
                 ) Netto
            FROM view_tc_supplychain_org vst	 
            WHERE vst.ObjType = 'processing' ";
    
     
    $sshSupplychainID = $this->input->get('sshSupplychainID'); 
    $sshSupplyChildID = $this->input->get('sshSupplyChildID'); 		
    if($sshSupplychainID != 'null' && $sshSupplychainID !='false' && $sshSupplychainID != '')
     { 				
         if($sshSupplyChildID != 'null' && $sshSupplyChildID !='false' && $sshSupplyChildID != '')
         {
            $SQL .= " and vst.SupplychainID = '".$sshSupplyChildID."'  ";
         }else{
             $SQL .= " and vst.SupplychainID = '".$sshSupplychainID."'  "; 
         }
     }  
     
    $result = $this->db->query($SQL, array("$DateStart", "$DateEnd"))->result_array();
    //echo '<pre>';echo $this->db->last_query(); die;
    $jsonData = array();
    if($result)
    {
         foreach($result as $key => $val){
            $jsonData[] = array( $val['Name'] , abs($val['Netto']) );
         }
    } 
    return json_encode($jsonData);  
}

function readDataTraceabilityNew($get)
{ 
    $SupplychainID = @$this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
    if($SupplychainID!=''){
        $w1 = "AND (vso.SupplychainID=$SupplychainID OR vso2.SupplychainID=$SupplychainID OR vso3.SupplychainID=$SupplychainID)";
    }else{
        $w1 = "";
    }
    $dt = "SELECT
            st.SupplyTransID transid_1,
            st.SupplyID,
            st2.SupplyTransID transid_2,
            st3.SupplyTransID transid_3,
            vso.SupplychainID supplychainid_1,
            vso2.SupplychainID supplychainid_2,
            vso3.SupplychainID supplychainid_3
        FROM
            ktv_tc_supplychain_transaction st
            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
            LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.SupplyType='Batch'
            LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IFNULL(st2.SupplychainID, sb.SupplyDestOrgID)
            LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID)
        WHERE
            st.SupplyType!='Batch'
            $w1 
            AND SUBSTR(st.DateTransaction,1,10) BETWEEN ".$get['DateStart']." AND ".$get['DateEnd']."
        GROUP BY st.SupplyTransID";

    if($get['sshSupplychainID']!='' && $get['sshSupplychainID']!='false'){
        $select = "IFNULL(dt.transid_2, dt.transid_1) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
        $where = "AND dt.supplychainid_2=".$get['sshSupplychainID'];
        $group = " IFNULL(dt.transid_2, dt.transid_1)";
        if($get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false'){
            $select = "IFNULL(dt.transid_3, dt.transid_2) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
            $group = " IFNULL(dt.transid_3, dt.transid_2)";
            $where = $where." AND dt.supplychainid_1=".$get['sshSupplyChildID'];
        }
    }else{
        if($get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false'){
            $select = "IFNULL(dt.transid_3, dt.transid_2) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
            $where = "AND dt.supplychainid_1 IS NOT NULL";
            $group = " IFNULL(dt.transid_3, dt.transid_2)";
        }else{
            $select = "IFNULL(dt.transid_2, dt.transid_1) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
            $where = "AND dt.supplychainid_2 IS NOT NULL";
            $group = " IFNULL(dt.transid_2, dt.transid_1)";
        }
    }

    $sql = "SELECT
                *
            FROM 
                (SELECT
                    $select    
                FROM ($dt) dt
                WHERE 1=1 $where 
                GROUP BY $group) td
                LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyTransID=td.trans_id";
    //$query = $this->db->query($sql);
    $v1 = $get['sshSupplychainID']!='' && $get['sshSupplychainID']!='false' ? '' : '/*';
    $v2 = $get['sshSupplychainID']!='' && $get['sshSupplychainID']!='false' ? '' : '*/';
    $v3 = $get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false' ? '' : '/*';
    $v4 = $get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false' ? '' : '*/';
    $sql = "SELECT
                ROUND(SUM(VolumeNetto)/1000,2) Netto,
                COUNT(DISTINCT dt.FarmerID) total_farmer,
                COUNT(DISTINCT dt.SupplyTransID) total_trans,
                COUNT(DISTINCT dt.supplyid_1) total_bu,
                COUNT(DISTINCT dt.supplyid_2) total_pc
            FROM
                (
                    SELECT
                        st.SupplyTransID,
                        st.SupplyBatchID,
                        st.DateTransaction,
                        st.TransNumber,
                        m.MemberDisplayID FarmerID,
                        m.MemberName FarmerName,
                        v.Village,
                        pp.PartnerID,
                        sd.SubDistrict,
                        d.District,
                        st.PlantationNr, 
                        st.VolumeBruto,
                        st.PackageNumber,
                        st.VolumeCutting,
                        st.VolumeNetto,
                        st.TotalPayment TotalPayment,
                        st.ContractPrice NetPrice,
                        vso.SupplychainID,
                        CONCAT(vso.ObjType,' - ', vso.`Name`) BuyingUnit,
                        d2.District Distric,
                        pp.PartnerName,
                        IFNULL(sb.SupplyBatchStatus, 'Open') Status,
                        sb.SupplyBatchNumber,
                        IF(vso2.SupplychainID IS NULL, '', CONCAT(vso2.ObjType,' - ', vso2.`Name`)) Destination,
                        pl.PlotNr,
                        pl.Longitude,
                        pl.Latitude,
                        vso.Longitude BSLongitude,
                        vso.Latitude BSLatitude,
                        vso.SupplychainID supplyid_1,
                        vso.`Name` name_1,
                        vso2.SupplychainID supplyid_2,
                        vso2.`Name` name_2,
                        vso3.SupplychainID supplyid_3,
                        vso3.`Name` name_3
                    FROM
                        ktv_tc_supplychain_transaction st
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
                        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.SupplyType='Batch'
                        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                        LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType!='Batch'
                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                        LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IFNULL(st2.SupplychainID, sb.SupplyDestOrgID)
                        LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID)
                        LEFT JOIN ktv_program_partner pp ON pp.PartnerID=vso.PartnerID
                        LEFT JOIN ktv_village v2 ON v2.VillageID=vso.VillageID
                        LEFT JOIN ktv_subdistrict sd2 ON sd2.SubDistrictID=v2.SubDistrictID
                        LEFT JOIN ktv_district d2 ON d2.DistrictID=sd2.DistrictID 
                        LEFT JOIN ktv_survey_plot pl ON pl.MemberID = m.MemberID
                        /*LEFT JOIN (
                            SELECT
                                d.SupplyTransID,
                                SUM(d.TotalPayment) TotalPayment,
                                ROUND(AVG(d.NetPrice), 0) NetPrice
                            FROM
                                ktv_tc_supplychain_transaction_detail d
                                LEFT JOIN ktv_tc_supplychain_transaction d2 ON d2.SupplyTransID=d.SupplyTransID
                            GROUP BY SupplyTransID
                        ) pay ON pay.SupplyTransID=st.SupplyTransID*/
                    WHERE
                        st.SupplyType!='Batch'
                        $w1
                        AND SUBSTR(st.DateTransaction,1,10) BETWEEN ? AND ?
                        $v1 AND vso2.SupplychainID=? $v2
                        $v3 OR vso.SupplychainID=? $v4
                        GROUP BY st.SupplyTransID
                ) dt";
    $query = $this->db->query($sql, array($get['DateStart'], $get['DateEnd'], $get['sshSupplychainID'], $get['sshSupplyChildID']));
    $ret = $query->result_array();
    //echo $this->db->last_query();die;
    $results['traceable_sales']     	  = $ret[0]['Netto']; //$this->SQLSTraceSalesNew2('traceable_sales')->traceable_sales;  
    $results['number_of_farmer']    	  = $ret[0]['total_farmer']; //$this->SQLSTraceSalesNew2('TotalFarmer')->TotalFarmer; 
    $results['number_of_transaction']     = $ret[0]['total_trans']; //$this->SQLSTraceSalesNew2('TotalTrans')->TotalTrans; 
    $results['number_of_agregator_1']     = $ret[0]['total_bu']; //$this->SQLSTraceSalesNew2('TotalBuyingUnit')->TotalBuyingUnit;  
    $results['number_of_processing']      = $ret[0]['total_pc']; //$this->SQLSTraceSalesNew2('TotalProcessing')->TotalProcessing;

    $results['number_of_traceable_bu'] = 1.00;
    $results['number_of_traceable_processing'] = 0.28;
    $results['number_of_traceable_received'] = 0.30;

    $chart['potential'] = [
        'potential' => 87.85,
        'total'     => 12.15,
    ];
    $chart['volume'] = [
        'buying_unit' => 1.00,
        'processing'  => 0.28,
        'continental' => 0.30,
    ];
    $chart['volume_sold'] = [
        ['label'=>'09/2018', 'value'=> 0],
        ['label'=>'10/2018', 'value'=> 0],
        ['label'=>'11/2018', 'value'=> 0],
        ['label'=>'12/2018', 'value'=> 1],
        ['label'=>'01/2019', 'value'=> 0],
        ['label'=>'02/2019', 'value'=> 0],
        ['label'=>'03/2019', 'value'=> 0],
        ['label'=>'04/2019', 'value'=> 0],
        ['label'=>'05/2019', 'value'=> 0],
        ['label'=>'06/2019', 'value'=> 0],
        ['label'=>'07/2019', 'value'=> 0],
        ['label'=>'08/2019', 'value'=> 0],
        ['label'=>'09/2019', 'value'=> 0],
    ];
    $chart['price'] = [];
    for ($i=1; $i < 30; $i++) { 
        $chart['price'][] = [
            'label' => str_pad($i, 2, "0", STR_PAD_LEFT).'/09/2019',
            'value' => rand(13000,16000)
        ];
    }
    $chart['volume_delivered'] = [
        ['label'=>'09/2018', 'value'=> 0],
        ['label'=>'10/2018', 'value'=> 0],
        ['label'=>'11/2018', 'value'=> 0],
        ['label'=>'12/2018', 'value'=> 1],
        ['label'=>'01/2019', 'value'=> 0],
        ['label'=>'02/2019', 'value'=> 0],
        ['label'=>'03/2019', 'value'=> 0],
        ['label'=>'04/2019', 'value'=> 0],
        ['label'=>'05/2019', 'value'=> 0],
        ['label'=>'06/2019', 'value'=> 0],
        ['label'=>'07/2019', 'value'=> 0],
        ['label'=>'08/2019', 'value'=> 0],
        ['label'=>'09/2019', 'value'=> 0],
    ];
    $chart['volume_shipped'] = [
        ['label'=>'09/2018', 'value'=> 0],
        ['label'=>'10/2018', 'value'=> 0],
        ['label'=>'11/2018', 'value'=> 0],
        ['label'=>'12/2018', 'value'=> 1],
        ['label'=>'01/2019', 'value'=> 0],
        ['label'=>'02/2019', 'value'=> 0],
        ['label'=>'03/2019', 'value'=> 0],
        ['label'=>'04/2019', 'value'=> 0],
        ['label'=>'05/2019', 'value'=> 0],
        ['label'=>'06/2019', 'value'=> 0],
        ['label'=>'07/2019', 'value'=> 0],
        ['label'=>'08/2019', 'value'=> 0],
        ['label'=>'09/2019', 'value'=> 0],
    ];
    $results['charts'] = $chart;

    return $results;
}

function readDataTraceabilityNew2($get)
    { 
        $SupplychainID = @$this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID!=''){
            $w1 = "AND (vso.SupplychainID=$SupplychainID OR vso2.SupplychainID=$SupplychainID OR vso3.SupplychainID=$SupplychainID)";
        }else{
            $w1 = "";
        }
        $dt = "SELECT
                st.SupplyTransID transid_1,
                st.SupplyID,
                st2.SupplyTransID transid_2,
                st3.SupplyTransID transid_3,
                vso.SupplychainID supplychainid_1,
                vso2.SupplychainID supplychainid_2,
                vso3.SupplychainID supplychainid_3
            FROM
                ktv_tc_supplychain_transaction st
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
                LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.SupplyType='Batch'
                LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IFNULL(st2.SupplychainID, sb.SupplyDestOrgID)
                LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID)
            WHERE
                st.SupplyType!='Batch'
                $w1 
                AND SUBSTR(st.DateTransaction,1,10) BETWEEN ".$get['DateStart']." AND ".$get['DateEnd']."
            GROUP BY st.SupplyTransID";

        if($get['sshSupplychainID']!='' && $get['sshSupplychainID']!='false'){
            $select = "IFNULL(dt.transid_2, dt.transid_1) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
            $where = "AND dt.supplychainid_2=".$get['sshSupplychainID'];
            $group = " IFNULL(dt.transid_2, dt.transid_1)";
            if($get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false'){
                $select = "IFNULL(dt.transid_3, dt.transid_2) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
                $group = " IFNULL(dt.transid_3, dt.transid_2)";
                $where = $where." AND dt.supplychainid_1=".$get['sshSupplyChildID'];
            }
        }else{
            if($get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false'){
                $select = "IFNULL(dt.transid_3, dt.transid_2) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
                $where = "AND dt.supplychainid_1 IS NOT NULL";
                $group = " IFNULL(dt.transid_3, dt.transid_2)";
            }else{
                $select = "IFNULL(dt.transid_2, dt.transid_1) trans_id, COUNT(DISTINCT dt.SupplyID) farmer_count, COUNT(DISTINCT dt.transid_1) trans_count, COUNT(DISTINCT dt.supplychainid_1) bu_count,  COUNT(DISTINCT dt.supplychainid_2) processing_count ";
                $where = "AND dt.supplychainid_2 IS NOT NULL";
                $group = " IFNULL(dt.transid_2, dt.transid_1)";
            }
        }

        $sql = "SELECT
                    *
                FROM 
                    (SELECT
                        $select    
                    FROM ($dt) dt
                    WHERE 1=1 $where 
                    GROUP BY $group) td
                    LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyTransID=td.trans_id";
        //$query = $this->db->query($sql);
        $v1 = $get['sshSupplychainID']!='' && $get['sshSupplychainID']!='false' ? '' : '/*';
        $v2 = $get['sshSupplychainID']!='' && $get['sshSupplychainID']!='false' ? '' : '*/';
        $v3 = $get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false' ? '' : '/*';
        $v4 = $get['sshSupplyChildID']!='' && $get['sshSupplyChildID']!='false' ? '' : '*/';
        $sql = "SELECT
                    ROUND(SUM(VolumeNetto)/1000,2) Netto,
                    COUNT(DISTINCT dt.FarmerID) total_farmer,
                    COUNT(DISTINCT dt.SupplyTransID) total_trans,
                    COUNT(DISTINCT dt.supplyid_1) total_bu,
                    COUNT(DISTINCT dt.supplyid_2) total_pc
                FROM
                    (
                        SELECT
                            st.SupplyTransID,
                            st.SupplyBatchID,
                            st.DateTransaction,
                            st.TransNumber,
                            m.MemberDisplayID FarmerID,
                            m.MemberName FarmerName,
                            v.Village,
                            pp.PartnerID,
                            sd.SubDistrict,
                            d.District,
                            st.PlantationNr, 
                            st.VolumeBruto,
                            st.PackageNumber,
                            st.VolumeCutting,
                            st.VolumeNetto,
                            st.TotalPayment TotalPayment,
                            st.ContractPrice NetPrice,
                            vso.SupplychainID,
                            CONCAT(vso.ObjType,' - ', vso.`Name`) BuyingUnit,
                            d2.District Distric,
                            pp.PartnerName,
                            IFNULL(sb.SupplyBatchStatus, 'Open') Status,
                            sb.SupplyBatchNumber,
                            IF(vso2.SupplychainID IS NULL, '', CONCAT(vso2.ObjType,' - ', vso2.`Name`)) Destination,
                            pl.PlotNr,
                            pl.Longitude,
                            pl.Latitude,
                            vso.Longitude BSLongitude,
                            vso.Latitude BSLatitude,
                            vso.SupplychainID supplyid_1,
                            vso.`Name` name_1,
                            vso2.SupplychainID supplyid_2,
                            vso2.`Name` name_2,
                            vso3.SupplychainID supplyid_3,
                            vso3.`Name` name_3
                        FROM
                            ktv_tc_supplychain_transaction st
                            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
                            LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.SupplyType='Batch'
                            LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                            LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType!='Batch'
                            LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IFNULL(st2.SupplychainID, sb.SupplyDestOrgID)
                            LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID)
                            LEFT JOIN ktv_program_partner pp ON pp.PartnerID=vso.PartnerID
                            LEFT JOIN ktv_village v2 ON v2.VillageID=vso.VillageID
                            LEFT JOIN ktv_subdistrict sd2 ON sd2.SubDistrictID=v2.SubDistrictID
                            LEFT JOIN ktv_district d2 ON d2.DistrictID=sd2.DistrictID 
                            LEFT JOIN ktv_survey_plot pl ON pl.MemberID = m.MemberID
                            /*LEFT JOIN (
                                SELECT
                                    d.SupplyTransID,
                                    SUM(d.TotalPayment) TotalPayment,
                                    ROUND(AVG(d.NetPrice), 0) NetPrice
                                FROM
                                    ktv_tc_supplychain_transaction_detail d
                                    LEFT JOIN ktv_tc_supplychain_transaction d2 ON d2.SupplyTransID=d.SupplyTransID
                                GROUP BY SupplyTransID
                            ) pay ON pay.SupplyTransID=st.SupplyTransID*/
                        WHERE
                            st.SupplyType!='Batch'
                            $w1
                            AND SUBSTR(st.DateTransaction,1,10) BETWEEN ? AND ?
                            $v1 AND vso2.SupplychainID=? $v2
                            $v3 OR vso.SupplychainID=? $v4
                            GROUP BY st.SupplyTransID
                    ) dt";
        $query = $this->db->query($sql, array($get['DateStart'], $get['DateEnd'], $get['sshSupplychainID'], $get['sshSupplyChildID']));
        $ret = $query->result_array();
        //echo $this->db->last_query();die;
        $results['traceable_sales']           = $ret[0]['Netto']; //$this->SQLSTraceSalesNew2('traceable_sales')->traceable_sales;  
        $results['number_of_farmer']          = $ret[0]['total_farmer']; //$this->SQLSTraceSalesNew2('TotalFarmer')->TotalFarmer; 
        $results['number_of_transaction']     = $ret[0]['total_trans']; //$this->SQLSTraceSalesNew2('TotalTrans')->TotalTrans; 
        $results['number_of_agregator_1']     = $ret[0]['total_bu']; //$this->SQLSTraceSalesNew2('TotalBuyingUnit')->TotalBuyingUnit;  
        $results['number_of_processing']      = $ret[0]['total_pc']; //$this->SQLSTraceSalesNew2('TotalProcessing')->TotalProcessing;
        return $results;
    }


}

?>
