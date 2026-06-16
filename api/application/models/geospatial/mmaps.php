<?php
class mmaps extends CI_Model
{
    private $sql;
    public function __construct()
	{
		parent::__construct();

        $this->sql['farmer'] = "SELECT
				f.MemberID AS ID
				, f.MemberID
				, f.MemberName AS Name
				, f.MemberDisplayID
				, f.Address
                , f.StatusCode
				, p.Province
				, p.ProvinceID
				, d.District
				, d.DistrictID
				, sd.SubDistrict
				, v.Village
				, IF(f.Photo!='',f.Photo,'no-user.jpg') AS Photo
				, g.PlotNr AS GardenNr
				, g.SurveyNr
				, g.GardenAreaHa AS AreaHa
				, ps.AnnualProduction AS Production
				, IFNULL(ST_Latitude(g.LatLong), g.Latitude) Latitude
				, IFNULL(ST_Longitude(g.LatLong), g.Longitude) Longitude
				, IFNULL(g.FarmAge,0) AS FarmAge
                , Partner
			FROM (
				SELECT
					f.*
				FROM ktv_members f
				JOIN ktv_member_role mr ON mr.`MemberID` = f.`MemberID` -- AND mr.`MRoleID` = 1
				-- where_hakakses --
				-- WHERE
					-- f.StatusCode = 'active'
					-- where --
			) f
			JOIN (
				SELECT
					g.*, g.AverageAgeTree AS FarmAge
				FROM ktv_survey_plot g
				JOIN (SELECT g.MemberID, g.PlotNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_survey_plot g GROUP BY g.MemberID, g.PlotNr) z ON g.MemberID = z.MemberID AND g.PlotNr = z.PlotNr AND g.SurveyNr = z.SurveyNr
				WHERE 1 = 1
					AND g.StatusCode = 'active' 
					AND (ABS(g.`Latitude`) > 0 AND ABS(g.`Longitude`) > 0 or ST_Latitude(g.LatLong) IS NOT NULL AND ST_Longitude(g.LatLong) IS NOT NULL)
			) g ON f.MemberID = g.MemberID
			LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = g.MemberID AND ps.PlotNr = g.PlotNr
			JOIN ktv_village v ON v.VillageID = g.VillageID
			JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
            LEFT JOIN (SELECT apm.apmMemberID AS MemberID, GROUP_CONCAT(kpp.`PartnerFullName`) AS Partner FROM ktv_access_partner_member apm LEFT JOIN ktv_program_partner kpp ON kpp.`PartnerID` = apm.`apmPartnerID` GROUP BY apm.`apmMemberID`) part ON part.MemberID = f.`MemberID`
			WHERE 1 = 1
			-- where_garden --
			-- group_by_member --
		";
    }
    
    public function readMaps($userID)
    {
        $sqll = "
            SELECT PartnerID FROM ktv_program_staff WHERE UserId=?";
        $queryy = $this->db->query($sqll, array($userID));
        $resultt = $queryy->result_array();
        if ($resultt[0]['PartnerID']=='1') {
            $wh = 'OR kd.ProvinceID=11';
        }
        $sql = "
            SELECT %s
            FROM `ktv_farmer_garden` kcfg,
            `ktv_farmer` kcf,
            `ktv_district` kd,
            `ktv_subdistrict` ks,
            `ktv_village` kv
            WHERE kcf.StatusCode='active' and
            kcfg.FarmerID = kcf.FarmerID
            AND kv.VillageID=kcf.VillageID
            AND kv.SubDistrictID = ks.SubDistrictID
            AND ks.DistrictID = kd.DistrictID
            AND (kcf.Latitude is not null OR kcf.Latitude<>0)
            AND (kd.DistrictID IN (
               SELECT DistrictID FROM ktv_district a, ktv_private_staff b
               WHERE a.PartnerID=b.PartnerID AND b.UserID=$userID
               UNION
               SELECT DistrictID FROM ktv_district a, ktv_program_staff b
               WHERE a.PartnerID=b.PartnerID AND b.UserID=$userID )
               $wh)";
        $query = $this->db->query(sprintf($sql, 'kcf.FarmerID,kcf.OldFarmerID,kcf.FarmerName,kcf.Address,
            CONCAT(kv.Village,", ",ks.SubDistrict," - ",kd.District) Area, kcfg.GardenNr,kcfg.GardenHaUnCertified,
            (kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) Pohon,(IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*
               IFNULL(PanenTrekKg,0))+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS totalProduksi,
            kcfg.Latitude,kcfg.Longitude,IF(kcf.Photo!="",kcf.Photo,"no-user.jpg") as Photo'));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readMap($ProvinceID, $DistrictID, $Keyword, $status = 'verified')
    {
        if ($DistrictID=='null') {
            $DistrictID='';
        }

        $sqlHakAkses = "";
        $sqlHakAksesMill = "";
        if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sqlHakAkses = " INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
            $sqlHakAksesMill = " INNER JOIN ktv_access_partner_mill acc_pmi ON kc.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID = '{$_SESSION['PartnerID']}' ";
        }

        $sql = "
SELECT
    m.MemberID
    , m.MemberDisplayID
    , m.MemberName
    , m.Address
    , m.VillageID
    , v.Village
    , sd.SubDistrict
    , p.PlotNr
    , p.GardenAreaHa
    , p.SurveyNr
    , p.Latitude
    , p.Longitude
    , p.is_area
    , p.potential
    , IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
FROM ktv_members m
JOIN (
    SELECT
        p.MemberID, p.PlotNr, p.SurveyNr, p.GardenAreaHa, p.Latitude, p.Longitude, p.StatusCheck
        , IF(pl.MemberID, 1, 0) AS is_area
        , CASE 
            WHEN p.AverageAgeTree BETWEEN 1 AND 3 THEN 'Seedling'
            WHEN p.AverageAgeTree BETWEEN 4 AND 6 THEN 'Young'
            WHEN p.AverageAgeTree BETWEEN 7 AND 18 THEN 'Prime'
            WHEN p.AverageAgeTree > 19 THEN 'Old'
            ELSE ''
        END AS potential
    FROM ktv_survey_plot p
    JOIN (SELECT p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr FROM ktv_survey_plot p GROUP BY p.MemberID, p.PlotNr) z ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
    LEFT JOIN ktv_survey_plot_polygon pl ON pl.MemberID = p.MemberID AND pl.PlotNr = p.PlotNr AND pl.SurveyNr = p.SurveyNr
    WHERE 1 = 1
        AND p.Latitude IS NOT NULL AND p.Longitude IS NOT NULL
        AND ABS(p.Latitude) > 0 AND ABS(p.Longitude) > 0
    GROUP BY p.MemberID, p.PlotNr
) p ON p.MemberID = m.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
$sqlHakAkses
WHERE
    m.StatusCode = 'active'
    AND d.ProvinceID = ? AND d.DistrictID = ? AND (m.MemberDisplayID LIKE ? OR m.MemberName LIKE ? )
    -- where --
GROUP BY m.MemberID, p.PlotNr
";
        $where = '';

        //Sementara tidak pakai StatusCheck
        // if ($status !== 'undefined') {
        //     if ($status !== 'all') {
        //         $where .= " AND p.StatusCheck = '{$status}'";
        //     }
        // } else {
        //     $where .= " AND p.StatusCheck = 'verified'";
        // }

        $sql = str_replace('-- where --', $where, $sql);
        $query = $this->db->query($sql, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':"%{$Keyword}%",$Keyword==''?'%%':"%{$Keyword}%", $Keyword==''?'%%':"%$Keyword%"));
        //    echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result['farmer'] = $query->result_array();

        if ($DistrictID=='null') {
            $DistrictID='';
        }

        $sql_agent = "
SELECT
    m.MemberID
    , m.MemberDisplayID
    , m.MemberName
    , m.Address
    , m.VillageID
    , v.Village
    , sd.SubDistrict
    , m.Latitude
    , m.Longitude
    , mr.RoleName
    , IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
FROM ktv_members m
JOIN (SELECT r.MemberID, mr.MRoleName AS RoleName FROM ktv_member_role r JOIN ktv_ref_member_role mr ON mr.MRoleID = r.MRoleID WHERE r.MRoleID IN (5,6,7,8,9,10)) mr ON mr.MemberID = m.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
$sqlHakAkses
WHERE
    m.StatusCode = 'active'
    AND m.Latitude IS NOT NULL AND m.Longitude IS NOT NULL
    AND ABS(m.Latitude) > 0 AND ABS(m.Longitude) > 0
    AND d.ProvinceID = ? AND d.DistrictID = ? AND (m.MemberDisplayID LIKE ? OR m.MemberName LIKE ? )
    -- where --
GROUP BY m.MemberID
";
        // $sql_agent = str_replace('-- where --', $where, $sql_agent);
        $query_agent = $this->db->query($sql_agent, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
           $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
        $result['agent'] = $query_agent->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $result['mill'] = array();

        $sql_mill = "
            SELECT
                kc.MillID
                , kc.MillName
                , kc.Address
                , kv.Village
                , ks.SubDistrict
                , kc.Status
                , kc.Latitude
                , kc.Longitude
            FROM ktv_mill kc
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ks.`DistrictID`
            $sqlHakAksesMill
            WHERE kc.StatusCode = 'active'
                AND kc.Latitude IS NOT NULL AND kc.Longitude IS NOT NULL
                AND ABS(kc.Latitude) > 0 AND ABS(kc.Longitude) > 0
                AND kd.ProvinceID=? and kd.DistrictID like ? 
                AND (kc.MillID=? OR kc.MillName like ?)
               -- where --
            GROUP BY kc.MillID
";
        // $sql_mill = str_replace('-- where --', $where, $sql_mill);
        
        $query_mill = $this->db->query($sql_mill, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
         $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['mill'] = $query_mill->result_array();
        

        $result['total'] =
          sizeof($result['farmer'])
          +sizeof($result['agent'])
          +sizeof($result['mill'])
        ;
        return $result;
    }

    public function readBankMap($ProvinceID, $DistrictID, $Keyword)
    {
        $where = '';
        if ($_SESSION['daerah']) {
            $daerah = explode(',', $_SESSION['daerah']);
            $daerah_ids = array();
            foreach ($daerah as $key => $value) {
                $tmp = explode('##', $value);
                $daerah_ids[] = $tmp[0];
            }
            $daerah_ids = implode(',', $daerah_ids);
            if ($_SESSION['FlagAccess'] == 1) {
                $partner = $_SESSION['PartnerID'];
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
            } else {
                $where = "AND kd.DistrictID in ({$daerah_ids})";
            }
        }
        if ($DistrictID=='null') {
            $DistrictID='';
        }
        $sql = <<<SQL
        SELECT
        kcf.FarmerID,kcf.FarmerID AS id,
        kcf.OldFarmerID,kcf.FarmerName,kcf.Address,
        kcfg.SurveyNr,
        kv.Village,ks.SubDistrict,
        CONCAT(kv.Village,', ',ks.SubDistrict,' - ',kd.District) Area, kcfg.GardenNr,kcfg.GardenHaUnCertified,
        Pohon,
        Prod AS totalProduksi,
        Prod/IFNULL(GardenHaUnCertified,0) as Produktivitas,
        kcfg.Latitude,
        kcfg.Longitude
        ,IF(kcf.Photo!='',kcf.Photo,'no-user.jpg') as Photo
        ,IF(ga.FarmerID,1,0) AS is_area
FROM (
    SELECT
        g.FarmerID, g.GardenNr, g.SurveyNr, g.Latitude, g.Longitude, g.StatusGPS, g.GardenHaUnCertified,
        (g.PohonTM+g.PohonTBM+g.PohonRehab) Pohon,
        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Prod
    FROM ktv_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID = ks.SubDistrictID
LEFT JOIN ktv_district kd ON ks.DistrictID = kd.DistrictID
WHERE
  1 = 1 AND kcf.isLoanPassed = 1 AND kcf.isCertified = 0
  --where--
AND kcfg.StatusGPS = 'verified'
AND (kcfg.Latitude is not null AND kcfg.Latitude!='0.000000' AND kcfg.Longitude is not null AND kcfg.Longitude!='0.000000')
AND kd.ProvinceID=? and kd.DistrictID like ? and (kcfg.FarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY kcfg.Latitude, kcfg.Longitude
SQL;
// -- GROUP BY kcfg.FarmerID,kcfg.GardenNr
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':$Keyword,
            $Keyword==''?'%%':$Keyword, $Keyword==''?'%%':"%$Keyword%"));
// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result['bank_farmer_1'] = $query->result_array();

        // farmer certified
        $sql = <<<SQL
        SELECT
        kcf.FarmerID,kcf.FarmerID AS id,
        kcf.OldFarmerID,kcf.FarmerName,kcf.Address,
        kcfg.SurveyNr,
        kv.Village,ks.SubDistrict,
        CONCAT(kv.Village,', ',ks.SubDistrict,' - ',kd.District) Area, kcfg.GardenNr,kcfg.GardenHaUnCertified,
        Pohon,
        Prod AS totalProduksi,
        Prod/IFNULL(GardenHaUnCertified,0) as Produktivitas,
        kcfg.Latitude,
        kcfg.Longitude
        ,IF(kcf.Photo!='',kcf.Photo,'no-user.jpg') as Photo
        ,IF(ga.FarmerID,1,0) AS is_area
FROM (
    SELECT
        g.FarmerID, g.GardenNr, g.SurveyNr, g.Latitude, g.Longitude, g.StatusGPS, g.GardenHaUnCertified,
        (g.PohonTM+g.PohonTBM+g.PohonRehab) Pohon,
        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Prod
    FROM ktv_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID = ks.SubDistrictID
LEFT JOIN ktv_district kd ON ks.DistrictID = kd.DistrictID
WHERE
  1 = 1
  AND kcf.isLoanPassed = 1 AND kcf.isCertified = 1
  --where--
AND kcfg.StatusGPS = 'verified'
AND (kcfg.Latitude is not null AND kcfg.Latitude!='0.000000' AND kcfg.Longitude is not null AND kcfg.Longitude!='0.000000')
AND kd.ProvinceID=? and kd.DistrictID like ? and (kcfg.FarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY kcfg.Latitude, kcfg.Longitude
SQL;
// --GROUP BY kcfg.FarmerID,kcfg.GardenNr
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':$Keyword,
            $Keyword==''?'%%':$Keyword, $Keyword==''?'%%':"%$Keyword%"));
// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result['bank_farmer_2'] = $query->result_array();

        // farmer not meet the criteria
        $sql = <<<SQL
        SELECT
        kcf.FarmerID,kcf.FarmerID AS id,
        kcf.OldFarmerID,kcf.FarmerName,kcf.Address,
        kcfg.SurveyNr,
        kv.Village,ks.SubDistrict,
        CONCAT(kv.Village,', ',ks.SubDistrict,' - ',kd.District) Area, kcfg.GardenNr,kcfg.GardenHaUnCertified,
        Pohon,
        Prod AS totalProduksi,
        Prod/IFNULL(GardenHaUnCertified,0) as Produktivitas,
        kcfg.Latitude,
        kcfg.Longitude
        ,IF(kcf.Photo!='',kcf.Photo,'no-user.jpg') as Photo
        ,IF(ga.FarmerID,1,0) AS is_area
FROM (
    SELECT
        g.FarmerID, g.GardenNr, g.SurveyNr, g.Latitude, g.Longitude, g.StatusGPS, g.GardenHaUnCertified,
        (g.PohonTM+g.PohonTBM+g.PohonRehab) Pohon,
        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Prod
    FROM ktv_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID = ks.SubDistrictID
LEFT JOIN ktv_district kd ON ks.DistrictID = kd.DistrictID
WHERE
  1 = 1
  AND kcf.isLoanPassed = 0
  --where--
AND kcfg.StatusGPS = 'verified'
AND (kcfg.Latitude is not null AND kcfg.Latitude!='0.000000' AND kcfg.Longitude is not null AND kcfg.Longitude!='0.000000')
AND kd.ProvinceID=? and kd.DistrictID like ? and (kcfg.FarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY kcfg.Latitude, kcfg.Longitude
SQL;
// --GROUP BY kcfg.FarmerID,kcfg.GardenNr
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':$Keyword,
            $Keyword==''?'%%':$Keyword, $Keyword==''?'%%':"%$Keyword%"));
// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $result['bank_farmer_3'] = $query->result_array();


        $where = '';

        if ($_SESSION['daerah']) {
            $where = "AND ks.DistrictID in ({$daerah_ids})";
        }
        $sql_koperasi = <<<SQL
            SELECT
            kc.`CoopID`, kc.`CoopID` AS id
            ,CoopName
            ,kc.Latitude,kc.Longitude
            ,Village,SubDistrict,IFNULL(FarmerName,StaffName) as StaffName
            FROM ktv_cooperatives kc
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ks.`DistrictID`
            LEFT JOIN ktv_cooperative_staff kcs ON kc.CoopID=kcs.CoopID and Position='ketua'
            LEFT JOIN ktv_farmer kcf ON kcs.FarmerID=kcf.FarmerID
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and kd.ProvinceID=? and kd.DistrictID like ? and
               (kc.CoopID=? OR kc.CoopName like ?)
               --where--
            GROUP BY kc.CoopID
SQL;

        $sql_koperasi = str_replace('--where--', $where, $sql_koperasi);
        if ($skop=='1' or $skop=='5') {
            $query_koperasi = $this->db->query($sql_koperasi, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
               $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
            $result['farmer_organization'] = $query_koperasi->result_array();
        } else {
            $result['farmer_organization'] = array();
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_pedagang = <<<SQL
            SELECT
            kc.`TraderID`, kc.`TraderID` AS id
            ,TraderName CoopName
            ,kc.Latitude Latitude
            ,kc.Longitude Longitude
            ,Village,SubDistrict,StaffName
            FROM ktv_traders kc
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ks.`DistrictID`
            LEFT JOIN ktv_trader_staff kcs ON kc.TraderID=kcs.TraderID and Position='pemilik'
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and kd.ProvinceID=? and kd.DistrictID like ? and
               (kc.TraderID=? OR kc.TraderName like ?)
               --where--
            group by kc.TraderID
SQL;
        $sql_pedagang = str_replace('--where--', $where, $sql_pedagang);
        if ($skop=='1' or $skop=='7') {
            $query_pedagang = $this->db->query($sql_pedagang, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
               $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
            $result['trader'] = $query_pedagang->result_array();
        } else {
            $result['trader'] = array();
        }

        $result['total'] =
          sizeof($result['bank_farmer_1'])
          +sizeof($result['bank_farmer_2'])
          +sizeof($result['bank_farmer_3'])
          +sizeof($result['farmer_organization'])
          +sizeof($result['trader'])
        ;
        return $result;
    }

    public function getDistrictCount($ProvinceID, $DistrictID, $Keyword, $status = 'verified')
    {
        $districts = array();
        if (empty($DistrictID) && !empty($_SESSION['daerah'])) {
            $daerah = explode(',', $_SESSION['daerah']);
            foreach ($daerah as $val) {
                $tmp = explode('##', $val);
                $districts[] = $tmp[0];
            }
            $DistrictID = implode("','", $districts);
        } elseif (!empty($DistrictID)) {
            $districts[] = $DistrictID;
        }
        // if ($DistrictID=='null')$DistrictID='';

        $sqlHakAkses=""; $sqlHakAksesMill="";
        if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sqlHakAkses = " INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
            $sqlHakAksesMill = " INNER JOIN ktv_access_partner_mill acc_pmi ON m.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID = '{$_SESSION['PartnerID']}' ";
        }

        $sql = "
SELECT
    d.DistrictID
    , d.District
    , COUNT(DISTINCT p.MemberID) AS `count`
    , p.Latitude
    , p.Longitude
FROM ktv_members m
$sqlHakAkses
JOIN (
    SELECT
        p.MemberID, p.PlotNr, p.SurveyNr, p.GardenAreaHa, p.Latitude, p.Longitude, p.StatusCheck
        , IF(pl.MemberID, 1, 0) AS is_area
    FROM ktv_survey_plot p
    JOIN (SELECT p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr FROM ktv_survey_plot p GROUP BY p.MemberID, p.PlotNr) z ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
    LEFT JOIN ktv_survey_plot_polygon pl ON pl.MemberID = p.MemberID AND pl.PlotNr = p.PlotNr AND pl.SurveyNr = p.SurveyNr
    WHERE 1 = 1
        AND p.Latitude IS NOT NULL AND p.Longitude IS NOT NULL
        AND ABS(p.Latitude) > 0 AND ABS(p.Longitude) > 0
    GROUP BY p.MemberID, p.PlotNr
) p ON p.MemberID = m.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
WHERE
    m.StatusCode = 'active'
    AND d.ProvinceID = ? AND (m.MemberDisplayID LIKE ? OR m.MemberName LIKE ? )
    -- where --
GROUP BY d.DistrictID
";

        $where = '';

        //Sementara tidak pakai check status
        // if ($status !== 'undefined') {
        //     if ($status !== 'all') {
        //         $where .= " AND p.StatusCheck = '{$status}'";
        //     }
        // } else {
        //     $where .= " AND p.StatusCheck = 'verified'";
        // }

        $sql = str_replace('-- where --', $where, $sql);
        $params = array($ProvinceID,$Keyword==''?'%%':"%{$Keyword}%",$Keyword==''?'%%':"%{$Keyword}%");
        $query = $this->db->query($sql, $params);

        $result['farmer'] = $query->result_array();

        if ($DistrictID=='null') {
            $DistrictID='';
        }

        $sql = "
SELECT
    d.DistrictID
    , d.District
    , COUNT(DISTINCT m.MemberID) AS `count`
    , m.Latitude
    , m.Longitude
FROM ktv_members m
$sqlHakAkses
JOIN (SELECT r.MemberID, mr.MRoleName AS RoleName FROM ktv_member_role r JOIN ktv_ref_member_role mr ON mr.MRoleID = r.MRoleID WHERE r.MRoleID IN (5,6,7,8,9,10)) mr ON mr.MemberID = m.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
WHERE
    m.StatusCode = 'active'
    AND m.Latitude IS NOT NULL AND m.Longitude IS NOT NULL
    AND ABS(m.Latitude) > 0 AND ABS(m.Longitude) > 0
    AND d.ProvinceID = ? AND (m.MemberDisplayID LIKE ? OR m.MemberName LIKE ? )
GROUP BY d.DistrictID
";

        $params = array($ProvinceID,$Keyword==''?'%%':"%{$Keyword}%",$Keyword==''?'%%':"%{$Keyword}%");
        $query = $this->db->query($sql, $params);

        $result['agent'] = $query->result_array();

        $result['mill'] = array();
        $sql_mill = "SELECT
    r.*
    ,d.`District`
FROM (
    SELECT
        kd.DistrictID AS DistrictID
        , COUNT(m.MillID) AS `count`
        , m.Latitude
        , m.Longitude
    FROM ktv_mill m
    LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
    $sqlHakAksesMill
    WHERE
        m.Latitude IS NOT NULL AND m.Longitude IS NOT NULL
    AND kd.ProvinceID = ? and (m.MillID=? OR m.MillName like ?)
    group by DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";

        $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
        $query_mill = $this->db->query($sql_mill, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['mill'] = $query_mill->result_array();

        $result['total'] =
          @$result['farmer'][0]['count']
          +@$result['trader'][0]['count']
          +@$result['mill'][0]['count']
        ;
        return $result;
    }

    public function getBankDistrictCount($ProvinceID, $DistrictID, $Keyword)
    {
        $districts = array();
        if (empty($DistrictID) && !empty($_SESSION['daerah'])) {
            $daerah = explode(',', $_SESSION['daerah']);
            foreach ($daerah as $val) {
                $tmp = explode('##', $val);
                $districts[] = $tmp[0];
            }
            $DistrictID = implode("','", $districts);
        } elseif (!empty($DistrictID)) {
            $districts[] = $DistrictID;
        }
        // if ($DistrictID=='null')$DistrictID='';
        $sql = "SELECT
    r.*
    ,d.`District`
FROM (
SELECT
   kd.DistrictID AS DistrictID,
   SUM(IF(kcf.isLoanPassed = 1 AND kcf.isCertified = 0, 1, 0)) AS `farmer`,
   SUM(IF(kcf.isLoanPassed = 1 AND kcf.isCertified = 1, 1, 0)) AS `farmer_certified`,
   SUM(IF(kcf.isLoanPassed = 0, 0, 1)) AS `farmer_unmeet`,
   kcfg.Latitude AS Latitude,
   kcfg.Longitude AS Longitude
FROM (
    SELECT
        g.FarmerID, g.Latitude, g.Longitude, g.StatusGPS
    FROM ktv_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_farmer_view` kcf ON kcfg.FarmerID = kcf.FarmerID
LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
WHERE 1 = 1
   AND kcf.StatusCode='active'
   AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000') AND kcfg.StatusGPS = 'verified'
AND kd.DistrictID ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
AND kd.ProvinceID=? and (kcfg.FarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        $params = array($ProvinceID,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':$Keyword);
        $query = $this->db->query($sql, (!empty($districts)?array_merge($districts, $params):$params));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $ra = $query->result_array();
        if (!empty($ra)) {
            foreach ($ra as $key => $value) {
                $tmp = $value;
                $tmp['count'] = $tmp['farmer'];
                $result['bank_farmer_1'][] = $tmp;
                $tmp['count'] = $tmp['farmer_certified'];
                $result['bank_farmer_2'][] = $tmp;
                $tmp['count'] = $tmp['farmer_unmeet'];
                $result['bank_farmer_3'][] = $tmp;
            }
        }

//         $sql_tersertifikasi = "SELECT
//     r.*
//     ,d.`District`
// FROM (
// SELECT
//    SUBSTR(kcf.VillageID,1,4) AS DistrictID,
//    COUNT(kcfg.`FarmerID`) AS `count`,
//    kcfg.Latitude AS Latitude,
//    kcfg.Longitude AS Longitude
// FROM `ktv_farmer_garden` kcfg
// JOIN (
// SELECT
//   FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
// FROM `ktv_farmer_garden`
// GROUP BY FarmerID, GardenNr
// ) z ON z.FarmerID = kcfg.`FarmerID` AND z.GardenNr = kcfg.`GardenNr` AND z.SurveyNr = kcfg.`SurveyNr`
// JOIN `ktv_certification` kcc ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr=kcc.GardenNr AND kcfg.SurveyNr=kcc.SurveyNr AND ExternalDate>'0000-00-00'
// JOIN `ktv_farmer` kcf ON kcfg.FarmerID = kcf.FarmerID
// WHERE 1 = 1
//    AND kcf.StatusCode='active'
//    AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000')
// AND SUBSTR(kcf.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
// AND SUBSTR(kcf.VillageID,1,2)=? and (kcfg.FarmerID like ? OR kcf.OldFarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
// GROUP BY DistrictID
// ) r
// JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
// ";
//         $params = array($ProvinceID,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':$Keyword);
//         $query = $this->db->query($sql_tersertifikasi, (!empty($districts)?array_merge($districts,$params):$params));
//         $result['farmer_certified'] = $query->result_array();

        $sql_koperasi = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
            kd.DistrictID AS DistrictID
            ,COUNT(kc.`CoopID`) AS `count`
            ,kc.Latitude AS Latitude
            ,kc.Longitude AS Longitude
            FROM ktv_cooperatives kc
            LEFT JOIN ktv_village kv ON kv.VillageID = kc.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND kd.DistrictID ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and kd.ProvinceID=? and
               (kc.CoopID=? OR kc.CoopName like ?)
            GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
        $query_koperasi = $this->db->query($sql_koperasi, (!empty($districts)?array_merge($districts, $params):$params));
        $result['farmer_organization'] = $query_koperasi->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_pedagang = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
                kd.DistrictID AS DistrictID
                ,COUNT(kc.`TraderID`) AS `count`
                ,kc.Latitude AS Latitude
                ,kc.Longitude AS Longitude
            FROM ktv_traders kc
            LEFT JOIN ktv_village kv ON kv.VillageID = kc.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND kd.DistrictID ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and kd.ProvinceID=? and (kc.TraderID=? OR kc.TraderName like ?)
            group by DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
        $query_pedagang = $this->db->query($sql_pedagang, (!empty($districts)?array_merge($districts, $params):$params));
        $result['trader'] = $query_pedagang->result_array();

        $result['total'] =
          @$result['farmer'][0]['count']
          +@$result['farmer_certified'][0]['count']
          +@$result['farmer_organization'][0]['count']
          +@$result['trader'][0]['count']
        ;
        return $result;
    }

    public function readProvince()
    {
        $sql_where = '';
        if ($_SESSION['daerah_access']!='') {
            $sql_where = " AND DistrictID IN ({$_SESSION['daerah_access']})";
        }

        $sql = "
            SELECT a.ProvinceID AS id, a.Province AS province
            FROM ktv_province a
            LEFT JOIN ktv_district b ON a.ProvinceID=b.ProvinceID
            WHERE a.active='1' $sql_where
            GROUP BY a.ProvinceID
            ORDER BY a.Province";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        return $result;
    }

    public function readDistrict($ProvinceID, $userID)
    {
        $sql_where = '';
        if ($_SESSION['daerah_access']!='') {
            $sql_where = " AND DistrictID IN ({$_SESSION['daerah_access']})";
        }
        $sql = "SELECT DistrictID as id, District as district
             FROM ktv_district
             WHERE
                ktv_district.StatusCode = 'active'
                AND ProvinceID = {$ProvinceID} $sql_where
             ORDER BY District";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

/*    public function getPolygon($MemberID, $PlotNr, $SurveyNr = 0)
    {
        $sql = "
SELECT
  ga.Latitude,
  ga.Longitude
FROM `ktv_survey_plot_polygon` ga
WHERE
  ga.MemberID = ?
  AND ga.PlotNr = ?
  AND ga.SurveyNr = ?
ORDER BY ga.OrderNr
      ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
      // jika tidak ditemukan ambil latest survey
    }*/

    public function getPolygon($MemberID, $PlotNr, $SurveyNr = 0)
    {
        $sql = "
SELECT
  ga.Latitude,
  ga.Longitude,
  ga.Revision
FROM `ktv_survey_plot_polygon` ga
JOIN (
  SELECT
    ga.MemberID, ga.PlotNr, ga.SurveyNr, MAX(Revision) AS Revision
  FROM ktv_survey_plot_polygon ga
  WHERE 1 = 1
    #ga.Status = 'verified'
  GROUP BY ga.MemberID, ga.PlotNr, ga.SurveyNr
) r ON ga.MemberID = r.MemberID AND ga.PlotNr = r.PlotNr AND ga.SurveyNr = r.SurveyNr AND ga.Revision = r.Revision
WHERE
  ga.MemberID = ?
  AND ga.PlotNr = ?
  AND ga.SurveyNr = ?
ORDER BY ga.OrderNr
      ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        $sql = "
SELECT
  ga.Latitude,
  ga.Longitude
FROM `ktv_survey_plot_polygon` ga
JOIN (
    SELECT
        ga.MemberID, ga.PlotNr, ga.SurveyNr, MAX(Revision) AS Revision
    FROM ktv_survey_plot_polygon ga
    JOIN (
        SELECT MemberID, PlotNr, MAX(SurveyNr) AS SurveyNr
        FROM ktv_survey_plot_polygon
        GROUP BY MemberID, PlotNr
    ) z ON ga.MemberID = z.MemberID AND ga.PlotNr = z.PlotNr AND ga.SurveyNr = z.SurveyNr
    WHERE 1 = 1
        #AND ga.Status = 'verified'
    GROUP BY ga.MemberID, ga.PlotNr, ga.SurveyNr
) z ON z.MemberID = ga.MemberID AND z.PlotNr = ga.PlotNr AND z.SurveyNr = ga.SurveyNr
WHERE
  ga.MemberID = ?
  AND ga.PlotNr = ?
ORDER BY ga.OrderNr
      ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
      // jika tidak ditemukan ambil latest survey
    }

    public function isExistGardenArea($farmer_id, $garden_nr, $survey_nr)
    {
        $arr_criteria = array(
            'FarmerID'    => $farmer_id,
            'GardenNr'    => $garden_nr,
            'SurveyNr'    => $survey_nr,
      );
        $query = $this->db->get_where('ktv_farmer_garden_area', $arr_criteria);
      //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
      if ($query->num_rows() > 0) {
          return true;
      } else {
          return false;
      }
    }

    public function deleteGardenArea($farmer_id, $garden_nr, $survey_nr)
    {
        return $this->db->delete('ktv_farmer_garden_area', array(
            'FarmerID'    => $farmer_id,
            'GardenNr'    => $garden_nr,
            'SurveyNr'    => $survey_nr,
      ));
    }

    public function insertGardenArea($data)
    {
        return $this->db->insert('ktv_farmer_garden_area', $data);
    }

    public function updatePlotPolygon($area, $MemberID, $PlotNr, $SurveyNr)
    {
        $result = false;
        if (is_array($area)) {
            $this->db->trans_start(false);
        // delete old area
            $this->db->where('MemberID', $MemberID);
            $this->db->where('PlotNr', $PlotNr);
            $this->db->where('SurveyNr', $SurveyNr);

            $this->db->delete('ktv_survey_plot_polygon');
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // insert new area
        $no = 1;
            $data = array();
            foreach ($area as $val) {
                $data[] = array(
                    'MemberID'      => $MemberID,
                    'PlotNr'        => $PlotNr,
                    'SurveyNr'      => $SurveyNr,
                    'OrderNr'       => $no,
                    'Latitude'      => $val[0],
                    'Longitude'     => $val[1]
                    );
                $no++;
            }
            $this->db->insert_batch('ktv_survey_plot_polygon', $data);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            $this->db->trans_complete();
            $result = $this->db->trans_status();
        }
        return $result;
    }

    public function updateGarden($area, $farmer_id, $garden_nr, $survey_nr = 0)
    {
        $sql = "
UPDATE ktv_farmer_garden
SET
    GardenHaPolygon = ?
WHERE
    FarmerID = ?
    AND GardenNr = ?
    AND SurveyNr = ?
        ";
        return $this->db->query($sql, array($area, $farmer_id, $garden_nr, $survey_nr));

        // $data = array('GardenHaPolygon' => $area);
        // $condition = array('FarmerID'=>$farmer_id, 'GardenNr'=>$garden_nr, 'SurveyNr'=>$survey_nr);
        // return $this->db->update('ktv_farmer_garden', $data, $condition);
    }

    public function updateGardenHaPolygon()
    {
        $sql = "UPDATE ktv_farmer_garden g1, (
SELECT
    g.FarmerID, g.GardenNr, g.GardenHaPolygon
FROM ktv_farmer_garden g
WHERE
    g.GardenHaPolygon > 0
GROUP BY g.FarmerID, g.GardenNr
) g2
SET g1.GardenHaPolygon = g2.GardenHaPolygon
WHERE
    g1.FarmerID = g2.FarmerID
    AND g1.GardenNr = g2.GardenNr
    AND g1.GardenHaPolygon IS NULL
        ";
        return $this->db->query($sql);
    }

    public function readGarden($FarmerID = null, $GardenNr = null, $SurveyNr = null)
    {
      $filter = '';
        $params = array();
        if (!empty($FarmerID)) {
            $filter .= ' AND g.FarmerID = ?';
            $params[] = $FarmerID;
        }
        if (!empty($GardenNr)) {
            $filter .= ' AND g.GardenNr = ?';
            $params[] = $GardenNr;
        }
        if (!empty($SurveyNr)) {
            $filter .= ' AND g.SurveyNr = ?';
            $params[] = $SurveyNr;
        }
        $sql = "
SELECT
    ga.FarmerID
    ,ga.GardenNr
    ,ga.SurveyNr
FROM ktv_farmer_garden_area ga
JOIN ktv_farmer_garden g ON g.FarmerID = ga.FarmerID AND g.GardenNr = ga.GardenNr
WHERE
  1 = 1
  -- AND g.GardenHaPolygon IS NULL
  --filter--
GROUP BY
    ga.FarmerID,
    ga.GardenNr
      ";
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

//     public function getSupplyChain($start, $end, $province = '')
//     {
//         $sql = "SELECT
//     PerwakilanOrgID AS 1_perwakilan_orgid,
//     TraderName AS 1_perwakilan,
//     'Pedagang' AS 1_perwakilan_type,
//     IFNULL(kt.`Latitude`,0) AS 1_perwakilan_lat,
//     IFNULL(kt.`Longitude`,0) AS 1_perwakilan_long,

//     SupplyOrgID AS 1_orgid,
//     ksov.`Name` AS 1_nama,
//     ksov.`OrgType` 1_OrgType,
//     ksov.`Latitude` AS 1_latitude,
//     ksov.`Longitude` AS 1_longitude,

//     sup_org_2 AS 2_orgid,
//     ksov_2.`Name` AS 2_nama,
//     ksov_2.`OrgType` 2_OrgType,
//     ksov_2.`Latitude` AS 2_latitude,
//     ksov_2.`Longitude` AS 2_longitude,

//     sup_org_3 AS 3_orgid,
//     ksov_3.`Name` AS 3_nama,
//     ksov_3.`OrgType` 3_OrgType,
//     ksov_3.`Latitude` AS 3_latitude,
//     ksov_3.`Longitude` AS 3_longitude,

//     SUM(bruto) AS bruto,
//     ROUND(SUM(netto),2) AS netto,
//     COUNT(DISTINCT SupplyBatchID) AS batch_count,
//     COUNT(DISTINCT SupplyID) AS supply_count,
//     COUNT(SupplyTransID) AS transaction_count
// FROM (
//     SELECT a.SupplyTransID,a.SupplyBatchID,a.SupplyID,b.PerwakilanOrgID,b.SupplyOrgID,d.SupplyOrgID sup_org_2,
//     f.SupplyOrgID sup_org_3,b.`SupplyDestOrgID`,
//     SUM(IF(aa.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto)) bruto,
//     IF(SUM(((100-(aa.Moisture-7))/100*a.FAQVolumeBruto)/ab.nett*d.VolumeNetto)>0,
//      SUM(((100-(aa.Moisture-7))/100*a.FAQVolumeBruto)/ab.nett*d.VolumeNetto),
//      SUM(IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)/b.VolumeNetto*IFNULL(f.VolumeNetto,d.VolumeNetto))
//     ) netto
//     #IFNULL(SUM(((100-(aa.Moisture-7))/100*(IF(aa.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto)))/ab.nett*d.VolumeNetto),
//     #IFNULL(SUM(IF(e.FAQVolumeBruto>0,e.FAQVolumeBruto,e.FFVolumeBruto)),SUM(IF(c.FAQVolumeBruto>0,c.FAQVolumeBruto,c.FFVolumeBruto)))) netto
//     FROM ktv_supplychain_transaction a
//     LEFT JOIN ktv_supplychain_transaction_dtl aa ON a.SupplyTransID=aa.SupplyTransID
//     LEFT JOIN (
//         SELECT SUM((100-(b.Moisture-7))/100*(IF(b.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto))) nett,SupplyBatchID
//         FROM ktv_supplychain_transaction a
//         LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
//         GROUP BY SupplyBatchID
//     ) ab ON a.SupplyBatchID=ab.SupplyBatchID
//     LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID
//     LEFT JOIN ktv_supplychain_transaction c ON b.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
//     LEFT JOIN ktv_supplychain_batch d ON c.SupplyBatchID=d.SupplyBatchID
//     LEFT JOIN ktv_supplychain_transaction e ON d.SupplyBatchNumber=e.SupplyID AND e.SupplyType='Batch'
//     LEFT JOIN ktv_supplychain_batch f ON e.SupplyBatchID=f.SupplyBatchID
//     WHERE 1 = 1
//     AND (IFNULL(f.SupplyBatchDate,d.SupplyBatchDate) BETWEEN ? AND ?)
//     AND a.SupplyType='Farmer'
// GROUP BY a.SupplyBatchID,a.SupplyID
// ) a
// LEFT JOIN ktv_traders kt ON TraderID=PerwakilanOrgID
// LEFT JOIN ktv_supplychain_org_view ksov ON ksov.`SupplychainID`=SupplyOrgID
// LEFT JOIN ktv_supplychain_org_view ksov_2 ON ksov_2.`SupplychainID`=sup_org_2
// LEFT JOIN ktv_supplychain_org_view ksov_3 ON ksov_3.`SupplychainID`=sup_org_3
// WHERE
//     1 = 1
//     AND netto IS NOT NULL
//     AND IF(PerwakilanOrgID, kt.`Latitude` AND kt.`Longitude`, TRUE)
//     AND IF(SupplyOrgID, ksov.`Latitude` AND ksov.`Longitude`, TRUE)
//     AND IF(sup_org_2, ksov_2.`Latitude` AND ksov_2.`Longitude`, TRUE)
//     AND IF(sup_org_3, ksov_3.`Latitude` AND ksov_3.`Longitude`, TRUE)
//     AND SUBSTR(ksov.VillageID,1,2) = ?
// GROUP BY IF(PerwakilanOrgID IS NULL,ksov.`SupplychainID`,PerwakilanOrgID)
// ";

//         $result = $this->db->query($sql, array($start, $end, $province))->result_array();
//         echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
//         return $result;
//     }
    public function getSupplyChain($start, $end, $province = '', $partner = '', $certification = '1')
    {
        $sql = "SELECT
    IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid) 1_supplychainid,
    IF(a.1_orgid IS NULL, a.2_orgid, a.1_orgid) 1_orgid,
    IF(a.1_orgtype IS NULL, a.2_orgtype, a.1_orgtype) 1_orgtype,
    IF(a.1_orgid IS NULL, a.2_name, a.1_name) 1_name,
    b.Latitude AS 1_latitude,
    b.Longitude AS 1_longitude,

    IF(a.1_supplychainid IS NULL, NULL, a.2_supplychainid) 2_supplychainid,
    IF(a.1_orgid IS NULL, NULL, a.2_orgid) 2_orgid,
    IF(a.1_orgtype IS NULL, NULL, a.2_orgtype) 2_orgtype,
    IF(a.1_orgid IS NULL, NULL, a.2_name) 2_name,
    c.Latitude AS 2_latitude,
    c.Longitude AS 2_longitude,

    IFNULL(a.wh_supplychainid,e.SupplychainID) wh_supplychainid,
    IFNULL(a.wh_orgid,e.OrgID) wh_orgid,
    IFNULL(a.wh_name,e.Name) wh_name,
    IF(d.Latitude IS NULL OR d.Latitude='',e.Latitude,d.Latitude) AS wh_latitude,
    IF(d.Longitude IS NULL OR d.Longitude='',e.Longitude,d.Longitude) AS wh_longitude,

    SUM(IFNULL(a.wh_netto,a.farmer_netto)) AS netto,
    SUM(IF(a.1_orgid IS NULL,a.2_bruto,a.1_bruto)) AS bruto,
    COUNT( DISTINCT IF(a.1_batchid IS NULL, a.2_batchid, a.1_batchid)) AS batch_count,
    COUNT(DISTINCT a.farmer_id) AS supply_count,
    COUNT(IFNULL(a.wh_supplychainid,IFNULL(a.2_supplychainid,1_supplychainid))) AS transaction_count
FROM
        ktv_warehouse kw
    LEFT JOIN rpt_traceability a ON a.wh_dest LIKE CONCAT('%|',kw.WarehouseID,'|%')
    LEFT JOIN ktv_supplychain_org_view b ON IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid)=b.SupplychainID
    LEFT JOIN ktv_supplychain_org_view c ON IF(a.1_supplychainid IS NULL, NULL, a.2_supplychainid)=c.SupplychainID
    LEFT JOIN ktv_supplychain_org_view d ON wh_supplychainid=d.SupplychainID
        LEFT JOIN ktv_supplychain_org_view e ON e.OrgType='Gudang' AND e.OrgID=kw.WarehouseID
WHERE 1 = 1
    AND (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?)
    AND SUBSTR(a.Farmer_villageid,1,2)=?
    AND (kw.PartnerID = ? OR '' = ?)
    AND a.farmer_iscertified = ? AND a.1_status='Delivered' AND a.2_status='Delivered'
GROUP BY IF(1_orgid IS NULL, 2_orgid,1_orgid)
ORDER BY IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid)
        ";
        $result = $this->db->query($sql, array($start, $end, $province, $partner, $partner, $certification))->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        return $result;
    }

    public function getSupplyChainCPG($TraderID, $start, $end)
    {
        $sql = "SELECT
    d.CPGid AS id,
    IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid) AS supply_id,
    c.GroupName AS name,
    c.latitude,
    c.longitude,
    SUM(IFNULL(a.wh_netto,IFNULL(a.2_netto,1_netto)) ) AS netto,
    SUM(IF(a.1_orgid IS NULL,a.2_bruto,a.1_bruto)) AS bruto,
    COUNT( DISTINCT IF(a.1_batchid IS NULL, a.2_batchid, a.1_batchid)) AS batch_count,
    COUNT(DISTINCT a.farmer_id) AS supply_count,
    COUNT(IFNULL(a.wh_supplychainid,IFNULL(a.2_supplychainid,a.1_supplychainid))) AS transaction_count
FROM
    rpt_traceability a
    LEFT JOIN ktv_farmer d ON a.farmer_id=d.FarmerID
    LEFT JOIN (
        SELECT
            c.`CPGid`,
            c.`GroupName`,
            g.`latitude`,
            g.`longitude`
        FROM `ktv_cpg` c
        LEFT JOIN `ktv_cpg_batch_trainings` bt ON bt.CPGid = c.`CPGid`
        LEFT JOIN (
                SELECT
                  g.`FarmerID`,
                  g.`latitude`,
                  g.`longitude`
                FROM ktv_farmer_garden g
                JOIN (
                  SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
                  FROM ktv_farmer_garden g
                  WHERE
                    g.GardenNr = 1
                  GROUP BY FarmerID, GardenNr
                ) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
        ) g ON g.FarmerID = bt.`DemoplotOwnerID`
        GROUP BY c.`CPGid`
        ORDER BY c.`CPGid`, bt.`DemoplotOwnerID`
    ) c ON c.CPGid = d.`CPGid`
WHERE 1 = 1
    AND (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?)
    AND IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid)=?
GROUP BY IF(1_orgid IS NULL, 2_orgid,1_orgid)
ORDER BY IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid)
      ";
        $query = $this->db->query($sql, array($start, $end, $TraderID));
      // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
      return $query->result_array();
    }

    public function getSupplyChainFarmer($supply_id, $start, $end, $partner = '', $certification = '1')
    {
        $sql = "SELECT
 a.farmer_id AS id,
 a.farmer_name AS name,
 d.CPGid,
 c.latitude,
 c.longitude,
 SUM(IFNULL(a.wh_netto,a.farmer_netto) ) AS netto,
 SUM(IF(a.1_orgid IS NULL,a.2_bruto,a.1_bruto)) AS bruto,
 COUNT( DISTINCT IF(a.1_batchid IS NULL, a.2_batchid, a.1_batchid)) AS batch_count,
 COUNT(DISTINCT a.farmer_id) AS supply_count,
 COUNT(IFNULL(a.wh_supplychainid,IFNULL(a.2_supplychainid,1_supplychainid))) AS transaction_count
FROM
 ktv_warehouse kw
 LEFT JOIN rpt_traceability a ON a.wh_dest LIKE CONCAT('%|',kw.WarehouseID,'|%')
 LEFT JOIN ktv_farmer d ON a.farmer_id=d.FarmerID
 LEFT JOIN (
                SELECT
                  g.`FarmerID`,
                  g.`latitude`,
                  g.`longitude`
                FROM ktv_farmer_garden g
                JOIN (
                  SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
                  FROM ktv_farmer_garden g
                  WHERE
                    g.GardenNr = 1
                  GROUP BY FarmerID, GardenNr
                ) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
        ) c ON c.FarmerID = d.`FarmerID`
WHERE 1 = 1
 AND (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?)
 AND IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid)=?
 AND (kw.PartnerID = ? OR '' = ?)
 AND a.farmer_iscertified = ?
GROUP BY a.farmer_id
ORDER BY IF(a.1_supplychainid IS NULL, a.2_supplychainid, a.1_supplychainid)
      ";
        $result = $this->db->query($sql, array($start, $end, $supply_id, $partner, $partner, $certification))->result_array();
      // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
      return $result;
    }

    public function getDistricts()
    {
        $query = $this->db->get('ktv_district');
        return $query->result_array();
    }

    public function getDistrictWeather($district)
    {
        $api_url = 'http://api.openweathermap.org/data/2.5/weather?units=metric&appid=3f8f1b94005b1494664238d67cdde59d';
        $this->load->library('curl');
        $result = $this->curl->simple_get($api_url.'&q='.urlencode($district));
        return $result;
    }

    public function insertWeatherLog($district, $date, $data)
    {
        $sql = "

        ";
    }

    public function batchInsertWeatherLog($values)
    {
        $sql = "INSERT INTO `ktv_weather_log` (
    `DistrictID`,
    `district`,
    `date`,
    `data`,
    `lon`,
    `lat`,
    `weather_main`,
    `weather_desc`,
    `icon`,
    `temp`,
    `pressure`,
    `humidity`,
    `temp_min`,
    `temp_max`,
    `wind_speed`,
    `wind_deg`,
    `name`
            ) VALUES ";

        $sql .= $values;
        $sql .= "
    ON DUPLICATE KEY UPDATE
        data            = VALUES(data),
        lon             = VALUES(lon),
        lat             = VALUES(lat),
        weather_main    = VALUES(weather_main),
        weather_desc    = VALUES(weather_desc),
        icon            = VALUES(icon),
        temp            = VALUES(temp),
        pressure        = VALUES(pressure),
        humidity        = VALUES(humidity),
        temp_min        = VALUES(temp_min),
        temp_max        = VALUES(temp_max),
        wind_speed      = VALUES(wind_speed),
        wind_deg        = VALUES(wind_deg),
        name            = VALUES(name)
    ";
        return $this->db->query($sql);
    }

    public function getWeather($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $sql = "SELECT
    `DistrictID`,
    `district`,
    `date`,
    `lon`,
    `lat`,
    `weather_main`,
    `weather_desc`,
    `icon`,
    `temp`,
    `pressure`,
    `humidity`,
    `temp_min`,
    `temp_max`,
    `wind_speed`,
    `wind_deg`,
    `name`
FROM
    `ktv_weather_log`
WHERE
    `date` = ?";
        $query = $this->db->query($sql, array($date));
        return $query->result_array();
    }

    public function getBank($DistrictID, $BankID)
    {
        $sql = "SELECT
    bb.BranchID AS id,
    bb.BranchName AS `name`,
    bb.BranchAddress AS `address`,
    bb.BranchLatitude AS Latitude,
    bb.BranchLongitude AS Longitude
FROM ktv_bank_branch bb
WHERE
    bb.BranchDistrictID = ?
    AND (bb.BranchBankID = ? OR 'all' = ?)
        ";
        $query = $this->db->query($sql, array($DistrictID, $BankID, $BankID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getProvincePartner($ProvinceID)
    {
        $sql = "SELECT
    pp.PartnerID AS id,
    pp.PartnerName AS `name`
FROM ktv_district_partner_history dp
JOIN ktv_district d ON d.DistrictID = dp.DistrictID
JOIN ktv_program_partner pp ON pp.PartnerID = dp.PartnerID
JOIN ktv_warehouse wh ON wh.PartnerID = dp.PartnerID
JOIN rpt_traceability rt ON rt.wh_orgid = wh.WarehouseID
WHERE
    d.ProvinceID = ?
    --filter--
GROUP BY pp.PartnerID
ORDER BY `name`
        ";
        $filter = '';
        $PartnerID = $this->isPrivateStaff();
        if ($PartnerID !== false) {
            $filter = " AND pp.PartnerID IN ({$PartnerID})";
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, array($ProvinceID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function isPrivateStaff($UserID = null)
    {
        if (empty($UserID)) {
            $UserID = $_SESSION['userid'];
        }
        $sql = "SELECT
    ps.PartnerID
FROM ktv_private_staff ps
JOIN ktv_persons p ON p.PersonID = ps.PersonID
WHERE
    p.UserID = ?
        ";
        $query = $this->db->query($sql, array($UserID));
        if ($query->num_rows()>0) {
            $result = $query->row_array(0);
            return $result['PartnerID'];
        }
        return false;
    }

    public function readClonalArea($id)
    {
        $sql = "SELECT
    ga.Latitude,
    ga.Longitude
FROM ktv_clonal_garden_area ga
WHERE
    ga.ClonalID = ?
ORDER BY ga.OrderNr
        ";
        $query = $this->db->query($sql, array($id));

        $data = array();
        if ($query->num_rows()>0) {
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                $tmp = array();
                $tmp[] = $value['Latitude'];
                $tmp[] = $value['Longitude'];
                $data[] = $tmp;
            }
            return $data;
        }
        return false;
    }

    public function readClonal($id)
    {
        $sql = "    SELECT
        cg.ClonalID,
        cg.ObjType,
        CASE ObjType
           WHEN 'farmer' THEN CONCAT(kcf.FarmerName,'( ',kcf.FarmerID,')')
           WHEN 'koperasi' THEN kc.CoopName
        END AS `name`,
        CASE ObjType
           WHEN 'farmer' THEN kcf.VillageID
           WHEN 'koperasi' THEN kc.VillageID
        END AS VillageID,
        cg.Latitude,
        cg.Longitude,
        cg.GardenNr,
        cg.EstablishedYear,
        IF(cg.CertificationStatus='Yes','Yes, BP2MB','No') AS CertificationStatus,
        cg.DateAppliedCertification,
        cg.DateReceivedCertification,
        CASE cg.LandCertificate
           WHEN 1 THEN 'None'
           WHEN 2 THEN 'Notary Deed/BPN'
           WHEN 3 THEN 'Sub District'
           WHEN 4 THEN 'Village/ward'
           WHEN 5 THEN 'Do not know'
        END AS LandCertificate,
        cg.TotalClonesNr,
        cg.TotalShadeTreesNr,
        cg.Area
    FROM ktv_clonal_garden cg
    LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = cg.ObjID AND cg.ObjType = 'farmer'
    LEFT JOIN ktv_cooperatives kc ON kc.CoopID = cg.ObjID AND cg.ObjType = 'koperasi'
    WHERE
        cg.ClonalID = ?
        ";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getPlotDetail($MemberID, $PlotNr, $SurveyNr)
    {
        $sql = "
SELECT
    m.MemberID
    , m.MemberDisplayID
    , m.MemberName
    , p.GardenAreaHa
    , p.GardenAreaPolygon
    , m.Address
    , m.VillageID
    , v.Village
    , sd.SubDistrict
    , p.PlotNr
    , p.SurveyNr
    , p.Latitude
    , p.Longitude
    , p.is_area
    , IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
FROM ktv_members m
JOIN (
    SELECT
        p.MemberID, p.PlotNr, p.SurveyNr, p.GardenAreaHa, p.GardenAreaPolygon, p.Latitude, p.Longitude
        , IF(pl.MemberID, 1, 0) AS is_area
    FROM ktv_survey_plot p
    JOIN (SELECT p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr FROM ktv_survey_plot p GROUP BY p.MemberID, p.PlotNr) z ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
    LEFT JOIN ktv_survey_plot_polygon pl ON pl.MemberID = p.MemberID AND pl.PlotNr = p.PlotNr AND pl.SurveyNr = p.SurveyNr
    GROUP BY p.MemberID, p.PlotNr
) p ON p.MemberID = m.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
WHERE
    m.StatusCode = 'active'
    AND p.Latitude IS NOT NULL
    AND p.Longitude IS NOT NULL
    AND m.MemberID = ?
    AND p.PlotNr = ?
    AND p.SurveyNr = ?
        ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyProfileFarmer($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
    farmer_id AS id,
    farmer_name AS name,
    kcpg.CPGid AS CPGid,
    kcpg.GroupName,
    kvil.Village,
    IFNULL(survey_production, 0) survey,
    IFNULL(survey_production+(0.1*survey_production), 0) quota,
    COUNT(IFNULL(rt.wh_supplychainid,IFNULL(rt.2_supplychainid,1_supplychainid))) AS transaction_count,
    SUM(IF(rt.IsPremium='1',1,0)) delivered_count,
    COUNT( DISTINCT IF(rt.1_batchid IS NULL, rt.2_batchid, rt.1_batchid)) AS batch_count,
   SUM(IFNULL(1_bruto,2_bruto)) bruto,
    SUM(farmer_netto) netto
FROM
   rpt_traceability rt

   LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE
            !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
   ) e ON farmer_id=e.FarmerID
   LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=farmer_id
   LEFT JOIN ktv_cpg kcpg ON kcf.CPGid=kcpg.CPGid
    LEFT JOIN ktv_village kvil ON kvil.VillageID=kcf.VillageID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND farmer_id={$id} AND rt.wh_dest REGEXP ?
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionFarmer($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
   IFNULL(wh_date,IFNULL(2_date,1_date)) trans_date,
   IFNULL(1_transid,2_transid) trans_number,
   survey_production survey,
   survey_production+(0.1*survey_production) quota,
   IFNULL(1_bruto,2_bruto) bruto,
   farmer_netto netto,
   IFNULL(1_orgtype,2_orgtype) dest_orgtype,
   IFNULL(1_orgid,2_orgid) dest_orgid,
   IF(1_orgid IS NULL, 2_name, 1_name) dest_orgname
FROM
   rpt_traceability rt

   LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
   ) e ON farmer_id=e.FarmerID
   LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=farmer_id
   LEFT JOIN ktv_cpg kcpg ON kcf.CPGid=kcpg.CPGid
   LEFT JOIN ktv_village kvil ON kvil.VillageID=kcf.VillageID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND farmer_id={$id} AND rt.wh_dest REGEXP ?
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfilePedagang($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
    IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_orgid,IF(2_orgtype='Pedagang',2_orgid,NULL)) id,
    IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_name,IF(2_orgtype='Pedagang',2_name,NULL)) name,
    COUNT(IFNULL(rt.wh_supplychainid,IFNULL(rt.2_supplychainid,1_supplychainid))) AS transaction_count,
    SUM(IF(rt.IsPremium='1',1,0)) delivered_count,
    COUNT( DISTINCT IF(rt.1_batchid IS NULL, rt.2_batchid, rt.1_batchid)) AS batch_count,
    COUNT(DISTINCT farmer_id) farmer_count,
    SUM(IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_bruto,IF(2_orgtype='Pedagang',2_bruto,NULL))) bruto,
    SUM(IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_netto,IF(2_orgtype='Pedagang',2_netto,NULL))) netto
FROM
   rpt_traceability rt

   LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
   ) e ON farmer_id=e.FarmerID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_orgid,IF(2_orgtype='Pedagang',2_orgid,NULL))={$id} AND rt.wh_dest REGEXP ?
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionPedagang($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
    IFNULL(wh_date,IFNULL(2_date,1_date)) trans_date,
    IFNULL(1_transid,2_transid) trans_number,
    IFNULL(1_batchnumber,2_batchnumber) batch_number,
    IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_batchnumber,IF(2_orgtype='Pedagang',2_batchnumber,NULL)) batch_number,
    SUM(IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_bruto,IF(2_orgtype='Pedagang',2_bruto,NULL))) bruto,
    SUM(IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_netto,IF(2_orgtype='Pedagang',2_netto,NULL))) netto,
    IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_destorgtype,IF(2_orgtype='Pedagang',2_destorgtype,NULL)) dest_orgtype,
    IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_destorgid,IF(2_orgtype='Pedagang',2_destorgid,NULL)) dest_orgid,
    IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_destname,IF(2_orgtype='Pedagang',2_destname,NULL)) dest_orgname
FROM
    rpt_traceability rt

    LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
    ) e ON farmer_id=e.FarmerID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_orgid,IF(2_orgtype='Pedagang',2_orgid,NULL))={$id} AND rt.wh_dest REGEXP ?
GROUP BY IF(1_orgtype='Pedagang' OR 1_orgtype='Perwakilan',1_batchid,IF(2_orgtype='Pedagang',2_batchid,NULL))
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfileKoperasi($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
   2_orgid id,
   2_name name,
   COUNT(DISTINCT 2_transid) AS transaction_count,
   COUNT(DISTINCT IF(rt.IsPremium='1',2_batchid,NULL)) delivered_count,
   COUNT(DISTINCT rt.2_batchid) AS batch_count,
   COUNT(DISTINCT 1_orgid) bu_count,
   SUM(2_bruto) bruto,
   SUM(2_netto) netto
FROM
   rpt_traceability rt

   LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
   ) e ON farmer_id=e.FarmerID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND 2_orgtype='Koperasi' AND 2_orgid={$id} AND rt.wh_dest REGEXP ?
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionKoperasi($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
   IFNULL(wh_date,IFNULL(2_date,1_date)) trans_date,
   2_transid trans_number,
   2_batchnumber batch_number,
   SUM(2_bruto) bruto,
   SUM(2_netto) netto,
   2_destorgtype dest_orgtype,
   2_destorgid dest_orgid,
   2_destname dest_orgname
FROM
      rpt_traceability rt

      LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
      ) e ON farmer_id=e.FarmerID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND 2_orgtype='Koperasi' AND 2_orgid={$id} AND rt.wh_dest REGEXP ?
GROUP BY 2_batchid
        ";
        $query = $this->db->query($sql, array($wh));

        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfileSCE($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
    IF(1_orgtype='sce',1_orgid,IF(2_orgtype='sce',2_orgid,NULL)) id,
    IF(1_orgtype='sce',1_name,IF(2_orgtype='sce',2_name,NULL)) name,
    kcf.FarmerID,
    kcpg.CPGid AS CPGid,
    kcpg.GroupName,
    COUNT(IFNULL(rt.wh_supplychainid,IFNULL(rt.2_supplychainid,1_supplychainid))) AS transaction_count,
    SUM(IF(rt.IsPremium='1',1,0)) delivered_count,
    COUNT( DISTINCT IF(rt.1_batchid IS NULL, rt.2_batchid, rt.1_batchid)) AS batch_count,
    COUNT(DISTINCT farmer_id) farmer_count,
    SUM(IF(1_orgtype='sce',1_bruto,IF(2_orgtype='sce',2_bruto,NULL))) bruto,
    SUM(IF(1_orgtype='sce',1_netto,IF(2_orgtype='sce',2_netto,NULL))) netto
FROM
   rpt_traceability rt

   LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
   ) e ON farmer_id=e.FarmerID
   LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=SUBSTR(IF(1_orgtype='sce',1_name,IF(2_orgtype='sce',2_name,NULL)), 2,9)
   LEFT JOIN ktv_cpg kcpg ON kcf.CPGid=kcpg.CPGid
    LEFT JOIN ktv_village kvil ON kvil.VillageID=kcf.VillageID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND IF(1_orgtype='sce',1_orgid,IF(2_orgtype='sce',2_orgid,NULL))={$id} AND rt.wh_dest REGEXP ?
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionSCE($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
    IFNULL(wh_date,IFNULL(2_date,1_date)) trans_date,
    IFNULL(1_transid,2_transid) trans_number,
    IFNULL(1_batchnumber,2_batchnumber) batch_number,
    IF(1_orgtype='sce',1_batchnumber,IF(2_orgtype='sce',2_batchnumber,NULL)) batch_number,
    SUM(IF(1_orgtype='sce',1_bruto,IF(2_orgtype='sce',2_bruto,NULL))) bruto,
    SUM(IF(1_orgtype='sce',1_netto,IF(2_orgtype='sce',2_netto,NULL))) netto,
    IF(1_orgtype='sce',1_destorgtype,IF(2_orgtype='sce',2_destorgtype,NULL)) dest_orgtype,
    IF(1_orgtype='sce',1_destorgid,IF(2_orgtype='sce',2_destorgid,NULL)) dest_orgid,
    IF(1_orgtype='sce',1_destname,IF(2_orgtype='sce',2_destname,NULL)) dest_orgname
FROM
    rpt_traceability rt

    LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
    ) e ON farmer_id=e.FarmerID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND IF(1_orgtype='sce',1_orgid,IF(2_orgtype='sce',2_orgid,NULL))={$id} AND rt.wh_dest REGEXP ?
GROUP BY IF(1_orgtype='sce',1_batchid,IF(2_orgtype='sce',2_batchid,NULL))
        ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfileWarehouse($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
    wh_orgid id,
    wh_name name,
    COUNT(DISTINCT wh_batchid) AS transaction_count,
    COUNT(DISTINCT wh_batchid) delivered_count,
    COUNT(DISTINCT wh_batchid) AS batch_count,
    COUNT(DISTINCT IF(2_orgtype='Koperasi',2_orgid,NULL)) coop_count,
    SUM(wh_bruto) bruto,
    SUM(wh_netto) netto
FROM
   rpt_traceability rt

   LEFT JOIN (
      SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
         d.FarmerID
      FROM (
         SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
         FROM ktv_certification
         WHERE !('{$start}' > CertificationEnd OR '{$end}' < CertificationStart) AND ExternalDate != '0000-00-00' AND
            ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
      INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
         z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
      GROUP BY d.FarmerID
   ) e ON farmer_id=e.FarmerID
WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}') AND wh_orgid={$id} AND rt.wh_dest REGEXP ? ";
        $query = $this->db->query($sql, array($wh));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionWarehouse($id, $start, $end, $wh)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
                    wh_date trans_date,
                    wh_transid trans_number,
                    wh_po po,
                    ksb.SupplyBatchNumber batch_number,
                    SUM(wh_bruto) bruto,
                    SUM(wh_netto) netto
                FROM
                    rpt_traceability rt
                        LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=wh_batchid
                WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN '{$start}' AND '{$end}')
                -- AND 2_orgtype='Koperasi'
                AND wh_orgid={$id} AND rt.wh_dest REGEXP ?
                GROUP BY wh_po
                ";
        $query = $this->db->query($sql, array($wh));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getWarehouseID($partner)
    {
        $sql = "SELECT GROUP_CONCAT(WarehouseID) id FROM ktv_warehouse WHERE PartnerID=?";
        $query = $this->db->query($sql, array($partner));
        if ($query->num_rows()>0) {
            return $query->row()->id;
        }
        return false;
    }

    public function checkLastRevision($MemberID, $PlotNr, $SurveyNr)
    {
        $sql = "
SELECT
    MAX(Revision) AS Revision
FROM
    ktv_survey_plot_polygon
WHERE
    MemberID        = ?
    AND PlotNr    = ?
    AND SurveyNr    = ?
        ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr));
        if ($query->num_rows()>0) {
            return $query->row_array(0)['Revision'];
        }
        return 0;
    }

    public function getKMLFarmerList($Province, $District = null, $SubDistrictID = null, $VillageID = null, $PartnerID = null, $Status = null)
    {
        $where = '';
        $params = array();
        
        $sql = "
SELECT
    f.MemberID AS id
    , CONCAT('[',f.MemberDisplayID,'] ',f.MemberName) AS label
FROM ktv_members f
JOIN (
    SELECT
        a.MemberID
    FROM ktv_survey_plot_polygon a
    JOIN ktv_members f ON f.`MemberID` = a.`MemberID`
    LEFT JOIN ktv_village v ON v.VillageID = f.VillageID
    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
    LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
    WHERE 1 = 1
        AND a.StatusCode = 'active'
        -- where --
    GROUP BY a.MemberID, a.PlotNr, a.SurveyNr
) a ON f.`MemberID` = a.MemberID
GROUP BY f.MemberID
        ";
        if ($Status == 'verified') {
            $where .= " AND a.StatusCheck = 'verified'";
        } elseif ($Status == 'new') {
            $where .= " AND a.StatusCheck = 'new'";
        } else {
            $where .= " AND a.StatusCheck IN ('verified','new')";
        } 
        if (!empty($Province)) {
            $where .= " AND p.ProvinceID = ?";
            $params[] = $Province;
        }
        if (!empty($District)) {
            $where .= " AND d.DistrictID = ?";
            $params[] = $District;
        }
        if (!empty($SubDistrictID)) {
            $where .= " AND sd.SubDistrictID = ?";
            $params[] = $SubDistrictID;
        }
        if (!empty($VillageID)) {
            $where .= " AND v.VillageID = ?";
            $params[] = $VillageID;
        }
        if (!empty($PartnerID)) {
            $where .= " AND f.MemberID IN (SELECT `apmMemberID` FROM `ktv_access_partner_member` WHERE `apmPartnerID` = ?)";
            $params[] = $PartnerID;
        }
        
        $sql = str_replace('-- where --', $where, $sql);
        
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getKMLProvinsi()
    {
        $sql = "
SELECT
    p.`ProvinceID` AS id
    , p.`Province` AS label
FROM ktv_province p
JOIN (
    SELECT
        SUBSTR(f.`VillageID`,1,2) AS ProvinceID
    FROM ktv_members f
    JOIN (
        SELECT
            ga.`MemberID`
        FROM ktv_survey_plot_polygon ga
        WHERE ga.`StatusCode` = 'active'
        GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY ProvinceID
) r ON r.ProvinceID = p.`ProvinceID`
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getKMLKabupaten($Province)
    {
        if (!empty($Province)) {
            $sql = "
SELECT
    d.`DistrictID` AS id
    , d.`District` AS label
FROM ktv_district d
JOIN (
    SELECT
        SUBSTR(f.`VillageID`,1,4) AS DistrictID
    FROM ktv_members f
    JOIN (
        SELECT
            ga.`MemberID`
        FROM ktv_survey_plot_polygon ga
        WHERE ga.`StatusCode` = 'active'
        GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY DistrictID
) r ON r.DistrictID = d.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`
WHERE
    p.`ProvinceID` = ?
            ";
            $query = $this->db->query($sql, array($Province));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function getKMLProvince()
    {
        $sql = "
SELECT
    d.`ProvinceID` AS id
    , d.`Province` AS label
FROM ktv_province d
JOIN (
    SELECT
        kprov.ProvinceID AS ProvinceID
    FROM ktv_members f
    LEFT JOIN ktv_village kvil ON kvil.VillageID=f.VillageID
    LEFT JOIN ktv_subdistrict ksub ON ksub.SubDistrictID=kvil.SubDistrictID
    LEFT JOIN ktv_district kdis on kdis.DistrictID=ksub.DistrictID
    LEFT JOIN ktv_province kprov on kprov.ProvinceID=kdis.ProvinceID
    JOIN (
    SELECT
        ga.`MemberID`
    FROM ktv_survey_plot_polygon ga
    WHERE 1 = 1
        -- ga.`Status` = 'verified'
    GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY ProvinceID
) r ON r.ProvinceID = d.`ProvinceID`
ORDER BY label
            ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getKMLDistrict($Province)
    {
        if (!empty($Province)) {
            $sql = "
SELECT
    d.`DistrictID` AS id
    , d.`District` AS label
FROM ktv_district d
JOIN (
    SELECT
        kdis.DistrictID AS DistrictID
    FROM ktv_members f
    LEFT JOIN ktv_village kvil ON kvil.VillageID=f.VillageID
    LEFT JOIN ktv_subdistrict ksub ON ksub.SubDistrictID=kvil.SubDistrictID
    LEFT JOIN ktv_district kdis on kdis.DistrictID=ksub.DistrictID
    JOIN (
    SELECT
        ga.`MemberID`
    FROM ktv_survey_plot_polygon ga
    WHERE 1 = 1
        -- ga.`Status` = 'verified'
    GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY DistrictID
) r ON r.DistrictID = d.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`
WHERE
    p.`ProvinceID` = ?
ORDER BY label    
            ";
            $query = $this->db->query($sql, array($Province));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function getKMLSubDistrict($DistrictID)
    {
        if (!empty($DistrictID)) {
            $sql = "
SELECT
    d.`SubDistrictID` AS id
    , d.`SubDistrict` AS label
FROM ktv_subdistrict d
JOIN (
    SELECT
        SUBSTR(f.`VillageID`,1,7) AS SubDistrictID
    FROM ktv_members f
    JOIN (
    SELECT
        ga.`MemberID`
    FROM ktv_survey_plot_polygon ga
    WHERE 1 = 1
        -- ga.`Status` = 'verified'
    GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY SubDistrictID
) r ON r.SubDistrictID = d.`SubDistrictID`
LEFT JOIN ktv_district p ON p.`DistrictID` = d.`DistrictID`
WHERE
    p.`DistrictID` = ?
ORDER BY label    
            ";
            $query = $this->db->query($sql, array($DistrictID));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function getKMLVillage($SubDistrictID)
    {
        if (!empty($SubDistrictID)) {
            $sql = "
SELECT
    d.`VillageID` AS id
    , d.`Village` AS label
FROM ktv_village d
JOIN (
    SELECT
        f.VillageID AS VillageID
    FROM ktv_members f
    JOIN (
    SELECT
        ga.`MemberID`
    FROM ktv_survey_plot_polygon ga
    WHERE 1 = 1
        -- ga.`Status` = 'verified'
    GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY VillageID
) r ON r.VillageID = d.`VillageID`
LEFT JOIN ktv_subdistrict p ON p.`SubDistrictID` = d.`SubDistrictID`
WHERE
    p.`SubDistrictID` = ?
ORDER BY label    
            ";
            $query = $this->db->query($sql, array($SubDistrictID));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function getKMLPartner($DistrictID)
    {
        $sql = "
SELECT
    p.`PartnerID` AS id
    , p.`PartnerName` AS label
FROM
    `ktv_program_partner` p
LEFT JOIN ktv_district_partner dp ON dp.PartnerID = p.PartnerID
WHERE
    p.StatusCode = 'active'
    AND dp.DistrictID = ?
ORDER BY label
";
        $params = array($DistrictID);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getKMLFarmerGroup($District)
    {
        if (!empty($District)) {
            $sql = "
SELECT
    c.`FarmerGroupId` AS id
    , c.`GroupName` AS label
FROM ktv_farmer_group c
JOIN (
    SELECT
        f.`FarmerGroupId`
    FROM ktv_members f
    JOIN (
        SELECT
            ga.`MemberID`
        FROM ktv_survey_plot_polygon ga
        WHERE ga.`StatusCode` = 'active'
        GROUP BY ga.`MemberID`
    ) ga ON ga.MemberID = f.`MemberID`
    GROUP BY f.`FarmerGroupId`
) r ON r.FarmerGroupID = c.`FarmerGroupID`
LEFT JOIN ktv_district d ON d.`DistrictID` = SUBSTR(c.`VillageID`,1,4)
WHERE
    d.`DistrictID` = ?
            ";
            $query = $this->db->query($sql, array($District));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function getFarmData($farmer_id, $status = null) 
    {
        // get garden, survey, revision
        $sql = "
SELECT 
    a.MemberID
    , a.PlotNr
    , a.SurveyNr
    , a.Revision
    , a.StatusCheck
    , a.DateCreated
    , a.CreatedBy
FROM ktv_survey_plot_polygon a
JOIN (
    SELECT a.MemberID, a.PlotNr
            , a.SurveyNr, MAX(a.`Revision`) AS Revision
    FROM ktv_survey_plot_polygon a
    JOIN (
        SELECT a.MemberID, PlotNr
            , MAX(SurveyNr) AS SurveyNr
        FROM ktv_survey_plot_polygon a 
        WHERE 1 = 1
            AND a.MemberID = ?
        GROUP BY a.MemberID, PlotNr
    ) z ON a.MemberID = z.MemberID AND a.PlotNr = z.PlotNr AND a.SurveyNr = z.SurveyNr 
    GROUP BY a.MemberID, PlotNr
) z ON a.MemberID = z.MemberID AND a.PlotNr = z.PlotNr AND a.SurveyNr = z.SurveyNr AND a.Revision = z.Revision
WHERE 1 = 1 -- where --        
GROUP BY a.MemberID, a.PlotNr
        ";
        if ($status == 'verified') {
            $where .= " AND a.StatusCheck = 'verified'";
        } elseif ($status == 'new') {
            $where .= " AND a.StatusCheck = 'new'";
        } else {
            $where .= " AND a.StatusCheck IN ('verified','new')";
        } 
        $sql = str_replace('-- where --', $where, $sql);
        $query = $this->db->query($sql, array($farmer_id));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";
        if ($query->num_rows() > 0) {
            $returnData = [];
            foreach ($query->result_array() as $garden) {                
                $sql = "
        SELECT 
            a.MemberID AS FarmerID
            , b.MemberDisplayID
            , a.PlotNr
            , a.SurveyNr
            , a.Revision
            , a.StatusCheck
            , b.StatusMember
            , b.MemberName
            , c.Longitude
            , c.Latitude
            , c.GardenAreaHa
            , c.GardenAreaPolygon
            , p.Province
            , d.District
            , sd.SubDistrict
            , v.Village
            , MAX(a.DateCreated) AS DateCreated
            , ug.`UserRealName` AS EnumeratorGarden
            , ug.`UserRealName` AS EnumeratorPolygon
            , Partner
        FROM (
            SELECT 
                ? AS MemberID
                , ? AS PlotNr
                , ? AS SurveyNr
                , ? AS Revision
                , ? AS StatusCheck
                , ? AS DateCreated
                , ? AS CreatedBy
        ) a
        JOIN ktv_members b ON a.MemberID = b.MemberID
        JOIN ktv_survey_plot c ON c.PlotNr = a.PlotNr AND c.SurveyNr = a.SurveyNr AND c.MemberID = a.MemberID
        LEFT JOIN ktv_village v ON v.VillageID = b.VillageID
        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
        LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
        LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
        LEFT JOIN sys_user ug ON ug.`UserId` =  c.CreatedBy
        LEFT JOIN sys_user up ON up.`UserId` =  a.CreatedBy
        LEFT JOIN (SELECT a.apmMemberID AS MemberID, GROUP_CONCAT(p.`PartnerFullName`) AS Partner FROM ktv_access_partner_member a LEFT JOIN ktv_program_partner p ON p.`PartnerID` = a.`apmPartnerID` WHERE a.`apmMemberID` = ? GROUP BY a.`apmMemberID`) part ON part.MemberID = b.`MemberID`
        WHERE 1 = 1
            -- where --
        GROUP BY a.MemberID, a.PlotNr, a.StatusCheck
                    ";
                $query = $this->db->query($sql, [
                    $garden['MemberID'],
                    $garden['PlotNr'],
                    $garden['SurveyNr'],
                    $garden['Revision'],
                    $garden['StatusCheck'],
                    $garden['DateCreated'],
                    $garden['CreatedBy'],
                    $garden['MemberID'],
                ]);
                // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";
                if ($query->num_rows() > 0) {
                    $returnData[] = $query->row_array(0);
                }
            }
            return $returnData;
        }
        return false;
    }

    public function getFarmerGarden($MemberID, $SurveyNr=null, $PlotNr=null) {
        $where = '';
        $params[] = $MemberID; // polygon
        $params[] = $MemberID; // polygon
        $params[] = $MemberID; // plot
        $sql = "
SELECT 
    c.MemberID
    , b.MemberDisplayID
    , b.MemberName
    , c.PlotNr
    , c.Longitude
    , c.Latitude
    , c.GardenAreaHa
    , a.SurveyNr
    , a.RevFirst
    , a.RevLast
    , a.StatusCheck
    , a.DateCollection
    , (SELECT GROUP_CONCAT(b.PartnerName) as Partners FROM ktv_access_partner_member as a
	LEFT JOIN ktv_program_partner as b ON b.PartnerID = a.apmPartnerID
	where a.apmMemberID = c.MemberID
    group by a.apmMemberID) as Partners
    , CONCAT(
        (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = c.`CreatedBy` LIMIT 1),
        IF(c.`LastModifiedBy` IS NOT NULL OR c.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = c.`LastModifiedBy` LIMIT 1),'')
    ) AS 'EnumeratorPlantation'
    , CONCAT(
        (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
        IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
    ) AS 'EnumeratorPolygon'
FROM ktv_survey_plot c
JOIN (
    SELECT
        p.`MemberID`, p.`PlotNr`
        , MAX(p.`SurveyNr`) AS SurveyNr
        , MIN(p.`Revision`) AS RevFirst
        , z.Revision AS RevLast
        , p.StatusCheck
        , p.DateCollection
        , p.CreatedBy
        , p.LastModifiedBy
    FROM ktv_survey_plot_polygon p
    LEFT JOIN (
        SELECT
            MemberID, PlotNr, SurveyNr, MAX(Revision) AS Revision
        FROM ktv_survey_plot_polygon p
        WHERE
            StatusCheck in ('verified','new') AND p.`MemberID` = ?
        GROUP BY MemberID, PlotNr, SurveyNr
    ) z ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
    WHERE p.StatusCode = 'active' AND p.Revision >= 0
        AND p.`MemberID` = ?
    GROUP BY p.`MemberID`, p.`PlotNr`, p.`SurveyNr` 
) a ON c.PlotNr = a.PlotNr AND c.SurveyNr = a.SurveyNr AND c.MemberID = a.MemberID
JOIN ktv_members b ON a.MemberID = b.MemberID
WHERE 1 = 1
    AND a.MemberID = ?
    --where--
GROUP BY a.PlotNr,
         a.MemberID
            
            ";
        if (!empty($PlotNr)) {
            $where .= ' AND a.PlotNr = ?';
            $params[] = $PlotNr;
        }
        if (!empty($SurveyNr)) {
            $where .= ' AND a.SurveyNr = ?';
            $params[] = $SurveyNr;
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function getCoordinates($MemberID, $PlotNr, $SurveyNr, $revision = 0) {
        $sql = "
SELECT a.MemberID,
       b.MemberDisplayID,
       b.MemberName,
       a.PlotNr,
       a.SurveyNr,
       a.OrderNr,
       a.Latitude,
       a.Longitude,
       c.GardenAreaHa
FROM ktv_survey_plot_polygon a
LEFT JOIN ktv_members b ON a.MemberID = b.MemberID
LEFT JOIN ktv_survey_plot c ON c.MemberID = a.MemberID AND c.PlotNr = a.PlotNr AND a.SurveyNr = c.SurveyNr
WHERE 
    a.StatusCode = 'active'
    AND a.MemberID = ?
    AND a.PlotNr = ?
    AND a.SurveyNr = ?
    AND a.Revision = ?
            ";

        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr, $revision));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function getMemberID($MemberDisplayID)
    {
        $sql = "
SELECT
    m.MemberID
FROM ktv_members m
WHERE
    m.MemberDisplayID = '?'
        ";
        $query = $this->db->query($sql, array($MemberDisplayID));
        if ($query->num_rows()>0) {
            return $query->row_array(0)['MemberID'];
        }
        return false;
    }

    /**
     * new query based on map of all actor
     */

    public function getFarmersGroup($ProvinceID, $DistrictID, $key = '', $FarmAge = '', $PartnerID, $FarmerStatus = null){
		$where  = ' ';
		$where_garden = '';
		$params = [];
		if (!empty($ProvinceID)) {
			$where_garden .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where_garden .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {			
			$where_garden .= " AND f.MemberDisplayID = '{$key}'";
		}

        if (!empty($FarmerStatus)) {			
			$where .= " WHERE f.StatusCode = '{$FarmerStatus}'";
		}
		
		if (isset($FarmAge) && $FarmAge != '') {
			switch ($FarmAge) {
				case '0':
					$where_garden = " AND FarmAge BETWEEN 0 AND 4";
					break;
				case '4':
					$where_garden = " AND FarmAge BETWEEN 5 AND 8";
					break;
				case '8':
					$where_garden = " AND FarmAge BETWEEN 9 AND 18";
					break;
				case '18':
					$where_garden = " AND FarmAge > 18";
					break;
			}
		}

		//Pengecekan Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			if($_SESSION["PartnerAsParent"] == "Yes"){
				($PartnerID != '')?$PartnerID = $PartnerID: $PartnerID = $_SESSION['PartnerChild'];
			
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID})";
				$where .= " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";	
			}else{
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
				$where .= " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
			}
		}

		$sql_count = str_replace("-- where --", $where, $this->sql['farmer']);
		$sql_count = str_replace("-- where_garden --", $where_garden, $sql_count);
		$sql_count = str_replace("-- where_hakakses --", $where_hakakses, $sql_count);
		$sql_count = str_replace("-- group_by_member --", " GROUP BY f.MemberID ", $sql_count);
		$query = $this->db->query($sql_count, $params);		
		
		if ($query->num_rows() > 0) {
			$DataReturn = $query->result_array();
			return $DataReturn;
		}
		return false;
	}

    public function GetInfoGardenPolygonNEWUI($MemberID, $StatusPolygon = null){
		$sql = "SELECT
			MemberID
			, PlotNr
			, Max(Revision) as Revision
			FROM
			ktv_survey_plot_polygon_geo
			WHERE
				MemberID = ?
			GROUP BY MemberID, PlotNr
            ORDER BY Revision DESC
			LIMIT 1
		";
		$data = $this->db->query($sql,array($MemberID))->result_array();
		$DataReturn = $data;

        switch($StatusPolygon){
            case 'all' :
                $StatusCheck = "'new','verified','overlap','retake','partnerverified'";
            break;
            case 'new' :
                $StatusCheck = "'new'";
            break;
            case 'verified' :
                $StatusCheck = "'verified'";
            break;
        }
		if (!empty($DataReturn)) {			
			$DataGarden = [];
			
			foreach ($DataReturn as $key => $value) {
				$sql = "SELECT
						b.`MemberID`
						, b.`MemberID` AS ID
						, b.`PlotNr`
						, ar.SurveyNr
						, ST_ASGEOJSON(ar.Polygon) Polygon
						, ar.StatusCheck
						, ar.Revision
						, b.`GardenAreaHa`
						/*, IFNULL(b.`GardenAreaPolygon`, b.`GardenAreaHa`) GardenAreaPolygon*/
						, ar.AreaHa GardenAreaPolygon
						, YEAR(NOW())-b.FirstPlantingYear AS FarmAge
						, p.Province
						, p.ProvinceID
						, d.District
						, d.DistrictID
						, sd.SubDistrict
						, v.Village
						, b.GardenAreaHa AS AreaHa
						, ps.AnnualProduction AS Production
						, ar.PartnerName
                        , su.`UserRealName` AS EnumeratorGarden
                        , su.`UserRealName` AS EnumeratorPolygon
                        , ST_X(ST_Centroid(ST_GeomFromText(ST_AsText(ar.Polygon)))) as CenterLon
                        , ST_Y(ST_Centroid(ST_GeomFromText(ST_AsText(ar.Polygon)))) as CenterLat
					FROM
						ktv_survey_plot b
					LEFT JOIN ktv_survey_plot_polygon_geo ar ON 1=1
						AND b.`MemberID` = ar.`MemberID`
						AND b.`PlotNr` = ar.`PlotNr`
						AND ar.StatusCheck IN ({$StatusCheck})
						AND ar.Revision = ?
					LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = b.MemberID AND ps.PlotNr = b.PlotNr
                    LEFT JOIN sys_user su ON su.`UserId` =  b.CreatedBy
					JOIN ktv_village v ON v.VillageID = b.VillageID
					JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
					JOIN ktv_district d ON d.DistrictID = sd.DistrictID
					JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
					WHERE
						b.`MemberID` = ?
						AND b.`PlotNr` = ?
						AND ar.`MemberID` IS NOT NULL                        
					GROUP BY ar.`MemberID`, b.`PlotNr`
				";
				$data = $this->db->query($sql,array($value['Revision'], $MemberID, $value['PlotNr']))->result_array();
				$LatestRev = $data;
				
				$DataGarden = array_merge($DataGarden,$LatestRev);
			}

			return $DataGarden;
		}
		

		return $DataReturn;
	}

}
