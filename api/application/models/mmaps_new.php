<?php
class Mmaps_new extends CI_Model
{

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
            FROM `ktv_cocoa_farmer_garden` kcfg,
            `ktv_cocoa_farmer` kcf,
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

    public function readMap($ProvinceID, $DistrictID, $Keyword, $skop)
    {
        if ($DistrictID=='null') {
            $DistrictID='';
        }
        $sql = <<<SQL
SELECT
        kcf.FarmerID,kcf.FarmerID AS id,
        kcf.OldFarmerID,kcf.FarmerName,kcf.Address,
        kcfg.`SurveyNr`,
        kv.Village,ks.SubDistrict,
        CONCAT(kv.Village,', ',ks.SubDistrict,' - ',kd.District) Area, kcfg.GardenNr,kcfg.GardenHaUnCertified,
        (kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) Pohon,
        IFNULL(ProductionCalc,Production) AS totalProduksi,
        IFNULL(ProductionCalc,Production)/IFNULL(GardenHaUnCertified,0) as Produktivitas,
        gps.Latitude,
        gps.Longitude
        ,IF(kcf.Photo!='',kcf.Photo,'no-user.jpg') as Photo
        ,IF(ga.FarmerID,1,0) AS is_area
FROM `ktv_cocoa_farmer_garden_view` kcfg
INNER JOIN (
  SELECT
  FarmerID, GardenNr, MAX(g.SurveyNr) AS SurveyNr
  FROM ktv_cocoa_farmer_garden g
  GROUP BY FarmerID, GardenNr
) ss ON ss.FarmerID = kcfg.FarmerID AND kcfg.GardenNr = ss.GardenNr AND kcfg.SurveyNr = ss.SurveyNr
JOIN `ktv_cocoa_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
JOIN (SELECT g.FarmerID, g.GardenNr, g.Latitude, g.Longitude
FROM ktv_cocoa_farmer_garden g WHERE g.StatusGPS = 'verified' GROUP BY g.FarmerID, g.GardenNr) gps ON gps.FarmerID = kcfg.FarmerID AND gps.GardenNr = kcfg.GardenNr
-- LEFT JOIN `ktv_cocoa_certification` kcc ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr=kcc.GardenNr AND kcfg.SurveyNr=kcc.SurveyNr AND ExternalDate>'0000-00-00'
LEFT JOIN `ktv_cocoa_farmer_garden_area` ga ON ga.FarmerID = ss.FarmerID AND ga.GardenNr = ss.GardenNr
LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID = ks.SubDistrictID
LEFT JOIN ktv_district kd ON ks.DistrictID = kd.DistrictID
WHERE
  1 = 1
  --where--
    AND kcf.StatusCode='active'
    AND kcf.isCertified = 0
    -- AND kcfg.StatusGPS = 'verified'
    AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000')
    AND kd.ProvinceID=? and kd.DistrictID like ? and (kcfg.FarmerID like ? OR kcf.OldFarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY kcfg.FarmerID,kcfg.GardenNr
SQL;
        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= "
    AND kcf.`CPGid` IN (
        SELECT
            CPGid
        FROM
            `ktv_cpg_partner`
        WHERE
           `PartnerID` = {$_SESSION['PartnerID']}
    )
            ";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND substr(kcf.VillageID,1,4) IN (SELECT DistrictID FROM ktv_access_staff WHERE UserID = {$_SESSION['userid']})";
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':$Keyword,$Keyword==''?'%%':$Keyword, $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':$Keyword));
       // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
       $result['farmer'] = $query->result_array();

        if ($DistrictID=='null') {
            $DistrictID='';
        }
        $sql_tersertifikasi = <<<SQL
            SELECT
               kcf.FarmerID,kcf.FarmerID AS id,
               kcf.OldFarmerID,kcf.FarmerName,kcf.Address,
                kcfg.`SurveyNr`,
               kv.Village,ks.SubDistrict,
               CONCAT(kv.Village,', ',ks.SubDistrict,' - ',kd.District) AREA, kcfg.GardenNr,kcfg.GardenHaUnCertified,
               (kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) Pohon,
                IFNULL(ProductionCalc,Production) AS totalProduksi,
                IFNULL(ProductionCalc,Production)/IFNULL(GardenHaUnCertified,0) as Produktivitas,
               gps.Latitude,gps.Longitude
               ,IF(kcf.Photo!='',kcf.Photo,'no-user.jpg') AS Photo
               ,IF(ga.FarmerID,1,0) AS is_area
            FROM `ktv_cocoa_farmer_garden_view` kcfg
            JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID, GardenNr) z ON z.FarmerID = kcfg.FarmerID AND z.GardenNr = kcfg.GardenNr AND z.SurveyNr = kcfg.SurveyNr
            -- JOIN `ktv_cocoa_certification` kcc ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr=kcc.GardenNr AND kcfg.SurveyNr=kcc.SurveyNr AND ExternalDate>'0000-00-00'
            JOIN `ktv_cocoa_farmer` kcf ON kcfg.FarmerID = kcf.FarmerID
            JOIN (SELECT g.FarmerID, g.GardenNr, g.Latitude, g.Longitude
            FROM ktv_cocoa_farmer_garden g WHERE g.StatusGPS = 'verified' GROUP BY g.FarmerID, g.GardenNr) gps ON gps.FarmerID = kcfg.FarmerID AND gps.GardenNr = kcfg.GardenNr
            LEFT JOIN `ktv_village` kv ON kv.VillageID=kcf.VillageID
            LEFT JOIN `ktv_subdistrict` ks ON kv.SubDistrictID = ks.SubDistrictID
            LEFT JOIN `ktv_district` kd ON ks.DistrictID = kd.DistrictID
            LEFT JOIN `ktv_cocoa_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
            WHERE 1 = 1
            --where--
               AND kcf.StatusCode='active'
               AND kcf.isCertified = 1
               -- AND kcfg.StatusGPS = 'verified'
               AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000')
               AND kd.ProvinceID=?
               AND kd.DistrictID LIKE ?
               AND (kcfg.FarmerID LIKE ? OR kcf.OldFarmerID LIKE ? OR kcf.FarmerName LIKE ? OR kcf.CPGid = ?)
            GROUP BY kcfg.FarmerID,kcfg.GardenNr
SQL;
        $sql_tersertifikasi = str_replace('--where--', $where, $sql_tersertifikasi);
        
        $query = $this->db->query($sql_tersertifikasi, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':$Keyword,
              $Keyword==''?'%%':$Keyword, $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':$Keyword));
        $result['farmer_certified'] = $query->result_array();
         // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $sql_nursery = <<<SQL
SELECT
    *
FROM
(
SELECT
    kcn.Established,kcn.Panjang,kcn.Lebar
    ,kcn.Latitude,kcn.Longitude
    ,kcn.Kapasitas
    ,kcn.ObjType AS ObjTypeNya
    ,CASE ObjType
       WHEN 'farmer' THEN kcf.PartnerID
       WHEN 'cpg' THEN kcg.PartnerID
       WHEN 'koperasi' THEN kc.PartnerID
       WHEN 'trader' THEN kt.PartnerID
    END AS PartnerID
    , CASE
        WHEN kcn.ObjType = 'cpg' THEN 'CPG'
        WHEN kcn.ObjType = 'farmer' THEN 'SCE'
        WHEN kcn.ObjType = 'koperasi' THEN 'Coop'
        WHEN kcn.`ObjType` = 'trader' THEN 'Trader'
    END AS ObjTypeLabel
    , IFNULL(CASE
        WHEN kcn.ObjType = 'cpg' THEN
            (SELECT GroupName FROM ktv_cpg WHERE CPGid = kcn.`ObjID`)
        WHEN kcn.ObjType = 'farmer' THEN
            (SELECT FarmerName FROM ktv_cocoa_farmer WHERE FarmerID = kcn.ObjID)
        WHEN kcn.ObjType = 'koperasi' THEN
            (SELECT CoopName FROM ktv_cooperatives WHERE CoopID = kcn.`ObjID`)
        WHEN kcn.`ObjType` = 'trader' THEN
            (SELECT TraderName FROM ktv_traders WHERE TraderID = kcn.`ObjID`)
    END,'-') AS ObjNameNya
    , IFNULL(CASE
        WHEN kcn.ResponsibleType = 'farmer' THEN
            (SELECT FarmerName FROM ktv_cocoa_farmer WHERE FarmerID = kcn.Responsible)
        WHEN kcn.ResponsibleType = 'staff' THEN
            (SELECT
                crn_b.PersonNm
            FROM
                ktv_staffs crn_a
                LEFT JOIN ktv_persons crn_b ON crn_a.PersonID = crn_b.PersonID
            WHERE
                crn_a.StaffID = kcn.Responsible
            LIMIT 1)
        WHEN kcn.ResponsibleType = 'other' THEN kcn.ResponsibleName
    END,'-') AS Caretaker
    , CASE
        WHEN kcn.ObjType = 'cpg' THEN kcn.ObjID
        WHEN kcn.ObjType = 'farmer' THEN kcn.ObjID
        WHEN kcn.ObjType = 'koperasi' THEN
            (SELECT VillageID FROM ktv_cooperatives WHERE CoopID = kcn.`ObjID`)
        WHEN kcn.`ObjType` = 'trader' THEN
            (SELECT VillageID FROM ktv_traders WHERE TraderID = kcn.`ObjID`)
    END AS DistrictIDnya
    ,kcn.`ObjID`
    ,kcn.NurseryNr
    ,kcn.Responsible
FROM
    ktv_nursery kcn
LEFT JOIN (
    SELECT
        kcf.FarmerID
        , kcf.FarmerName
        , kcf.VillageID
        , c.PartnerID
    FROM ktv_cocoa_farmer kcf
    LEFT JOIN ktv_cpg_partner c ON c.CPGid = kcf.CPGid    
) kcf ON kcf.FarmerID = kcn.ObjID AND kcn.ObjType = 'farmer'
LEFT JOIN (
    SELECT
        kc.CPGid
        , kc.GroupName
        , kc.VillageID
        , c.PartnerID
    FROM ktv_cpg kc
    LEFT JOIN ktv_cpg_partner c ON c.CPGid = kc.CPGid    
) kcg ON kcg.CPGid = kcn.ObjID AND kcn.ObjType = 'cpg'
LEFT JOIN (
    SELECT
        c.CoopID
        , c.CoopName
        , c.VillageID
        , sop.PartnerID
    FROM ktv_cooperatives c
    LEFT JOIN ktv_supplychain_org so ON so.OrgID = c.CoopID AND so.OrgType = 'koperasi'
    LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
) kc ON kc.CoopID = kcn.ObjID AND kcn.ObjType = 'koperasi'
LEFT JOIN (
    SELECT
        t.TraderID
        , t.TraderName
        , t.VillageID
        , sop.PartnerID
    FROM ktv_traders t
    LEFT JOIN ktv_supplychain_org so ON so.OrgID = t.TraderID AND so.OrgType = 'trader'
    LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
) kt ON kt.TraderID = kcn.ObjID AND kcn.ObjType = 'trader'    
WHERE
    kcn.Latitude IS NOT NULL AND kcn.Latitude != '0.000000' AND kcn.Longitude IS NOT NULL AND kcn.Longitude != '0.000000'
    AND kcn.StatusCode = 'active'
) AS tbl_where_district
WHERE
    SUBSTR(DistrictIDnya,1,2)=? AND SUBSTR(DistrictIDnya,1,4) LIKE ?
    AND (ObjID = ? OR ObjNameNya LIKE ?)
    --where--
SQL;

        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= " AND PartnerID = {$_SESSION['PartnerID']}";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND DistrictIDnya IN (SELECT DistrictID FROM ktv_access_staff WHERE UserID = {$_SESSION['userid']})";
        }
        $sql_nursery = str_replace('--where--', $where, $sql_nursery);
        $query_nursery = $this->db->query($sql_nursery, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
           $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
        $result['nursery'] = $query_nursery->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_demoplot = <<<SQL
            SELECT kc.CPGid, kc.CPGid AS id
            ,kc.GroupName
            ,kct.CpgTrainings
            ,kcfg.GardenNr
            ,kcfg.Latitude,kcfg.Longitude
            ,kcf.FarmerName,kcf.FarmerID,kcfg.GardenHaUnCertified
            ,(kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) Pohon,
                IFNULL(ProductionCalc,Production) AS totalProduksi,
                IFNULL(ProductionCalc,Production)/IFNULL(GardenHaUnCertified,0) as Produktivitas
            FROM ktv_cpg_batch_trainings kcbt
            JOIN ktv_cpg_trainings kct ON kct.CpgTrainingsID = kcbt.CPGtrainingsID AND kcbt.CpgTrainingsID IN (1,2)
            LEFT JOIN ktv_cpg kc ON kcbt.CPGid=kc.CPGid
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcbt.DemoplotOwnerID
            LEFT JOIN (
                  SELECT a.FarmerID,max(SurveyNr) as survey from `ktv_cocoa_farmer_garden` a where GardenNr=1 group by FarmerID
               ) kcfgt ON kcfgt.FarmerID = kcf.FarmerID
            LEFT JOIN ktv_cocoa_farmer_garden_view kcfg ON kcfgt.FarmerID=kcfg.FarmerID and kcfg.GardenNr=1 and kcfg.SurveyNr=kcfgt.survey
            WHERE kcfg.Latitude is not null AND kcfg.Latitude!='0.000000' AND kcfg.Longitude is not null AND
               kcfg.Longitude!='0.000000' AND kcfg.StatusGPS = 'verified' and substr(kcf.VillageID,1,2)=? and
               substr(kcf.VillageID,1,4) like ? and (kc.CPGid=? OR kc.GroupName like ? OR kcf.FarmerID=? OR kcf.FarmerName like ?)
               --where--
SQL;
        
        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= "
    AND kcf.`CPGid` IN (
        SELECT
            CPGid
        FROM
            `ktv_cpg_partner`
        WHERE
           `PartnerID` = {$_SESSION['PartnerID']}
    )
            ";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND substr(kcf.VillageID,1,4) IN (SELECT DistrictID FROM ktv_access_staff WHERE UserID = {$_SESSION['userid']})";
        }
        $sql_demoplot = str_replace('--where--', $where, $sql_demoplot);
        
        $query_demoplot = $this->db->query($sql_demoplot, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
           $Keyword, $Keyword==''?'%%':"%$Keyword%", $Keyword, $Keyword==''?'%%':"%$Keyword%"));
        $result['demoplot'] = $query_demoplot->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $where = '';
        $sql_koperasi = <<<SQL
            SELECT
            kc.`CoopID`, kc.`CoopID` AS id
            ,CoopName
            ,kc.Latitude,kc.Longitude
            ,Village,SubDistrict,IFNULL(FarmerName,StaffName) as StaffName
            FROM ktv_cooperatives kc
            LEFT JOIN ktv_supplychain_org so ON so.OrgID = kc.CoopID AND so.OrgType = 'koperasi'
            LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_cooperative_staff kcs ON kc.CoopID=kcs.CoopID and Position='ketua'
            LEFT JOIN ktv_cocoa_farmer kcf ON kcs.FarmerID=kcf.FarmerID
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and substr(kc.VillageID,1,2)=? and substr(kc.VillageID,1,4) like ? and
               (kc.CoopID=? OR kc.CoopName like ?)
               --where--
            GROUP BY kc.CoopID
SQL;
            
        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= " AND sop.PartnerID = {$_SESSION['PartnerID']}";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND substr(kc.VillageID,1,4) IN (SELECT DistrictID FROM ktv_access_staff WHERE StaffID = {$_SESSION['staffid']})";
        }
        $sql_koperasi = str_replace('--where--', $where, $sql_koperasi);
        
        $query_koperasi = $this->db->query($sql_koperasi, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
           $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
        $result['farmer_organization'] = $query_koperasi->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_gudang = <<<SQL
            SELECT
            kc.`WarehouseID`, kc.`WarehouseID` AS id
            ,WarehouseName CoopName
            ,kc.Latitude Latitude
            ,kc.Longitude Longitude
            ,Village,SubDistrict,StaffName
            FROM ktv_warehouse kc
            LEFT JOIN ktv_supplychain_org so ON so.OrgID = kc.WarehouseID AND so.OrgType = 'warehouse'
            LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_warehouse_staff kcs ON kc.WarehouseID=kcs.WarehouseID and Position='pemilik'
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and substr(kc.VillageID,1,2)=? and substr(kc.VillageID,1,4) like ? and
               (kc.WarehouseID=? OR kc.WarehouseName like ?)
               --where--
            group by kc.WarehouseName
SQL;
        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= " AND sop.PartnerID = {$_SESSION['PartnerID']}";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND substr(kc.VillageID,1,4) IN (SELECT DistrictID FROM ktv_access_staff WHERE StaffID = {$_SESSION['staffid']})";
        }
        $sql_gudang = str_replace('--where--', $where, $sql_gudang);
        if ($skop=='1' or $skop=='6') {
            $query_gudang = $this->db->query($sql_gudang, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
               $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
            $result['warehouse'] = $query_gudang->result_array();
        } else {
            $result['warehouse'] = array();
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
            LEFT JOIN ktv_supplychain_org so ON so.OrgID = kc.TraderID AND so.OrgType = 'trader'
            LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_trader_staff kcs ON kc.TraderID=kcs.TraderID and Position='pemilik'
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and substr(kc.VillageID,1,2)=? and substr(kc.VillageID,1,4) like ? and
               (kc.TraderID=? OR kc.TraderName like ?)
               --where--
            group by kc.TraderID
SQL;
        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= " AND sop.PartnerID = {$_SESSION['PartnerID']}";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND substr(kc.VillageID,1,4) IN (SELECT DistrictID FROM ktv_access_staff WHERE StaffID = {$_SESSION['staffid']})";
        }
        $sql_pedagang = str_replace('--where--', $where, $sql_pedagang);
        if ($skop=='1' or $skop=='7') {
            $query_pedagang = $this->db->query($sql_pedagang, array($ProvinceID, $DistrictID==''?'%%':$DistrictID,
               $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
            $result['trader'] = $query_pedagang->result_array();
        } else {
            $result['trader'] = array();
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;
        // exit;

        /*$sql_unitbuying = "
            SELECT Name CoopName,(kc.LatDeg+(kc.LatMin/60.0)+(kc.LatSec/3600.0))*IF(kc.LatDir='s',-1,1) Latitude,
               (kc.LongDeg+(kc.LongMin/60.0)+(kc.LongSec/3600.0))*IF(kc.LongDir='w',-1,1) Longitude,Village,SubDistrict,FarmerName as StaffName
            FROM ktv_supplychain_org kc
            LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
            LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
            LEFT JOIN ktv_cooperative_staff kcs ON kc.SupplychainID=kcs.CoopID and Position='Chairman'
            LEFT JOIN ktv_cocoa_farmer kcf ON kcs.FarmerID=kcf.FarmerID
            WHERE (kc.LatDeg+(kc.LatMin/60.0)+(kc.LatSec/3600.0)) is not null AND
               (kc.LatDeg+(kc.LatMin/60.0)+(kc.LatSec/3600.0))!='0.000000' AND
               (kc.LongDeg+(kc.LongMin/60.0)+(kc.LongSec/3600.0)) is not null AND
               (kc.LongDeg+(kc.LongMin/60.0)+(kc.LongSec/3600.0))!='0.000000' and
               Type='unitbuying' and substr(kc.VillageID,1,2)=? and substr(kc.VillageID,1,4) like ? and
               (kc.SupplychainID=? OR kc.Name like ?)
            group by kc.SupplychainID";
        if ($skop=='1' OR $skop=='8') {
           $query_unitbuying = $this->db->query($sql_unitbuying, array($ProvinceID,$DistrictID==''?'%%':$DistrictID,
               $Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%"));
           $result['unitbuying'] = $query_unitbuying->result_array();
        } else $result['unitbuying'] = array();*/
        $sql_clonal = "SELECT
    *
FROM (
    SELECT
        cg.ClonalID,
        cg.ClonalID AS id,
        cg.ObjType,
        CASE ObjType
           WHEN 'farmer' THEN CONCAT(kcf.FarmerName,'( ',kcf.FarmerID,')')
           WHEN 'cpg' THEN kcg.GroupName
           WHEN 'koperasi' THEN kc.CoopName
           WHEN 'trader' THEN kt.TraderName
        END AS `name`,
        CASE ObjType
           WHEN 'farmer' THEN kcf.VillageID
           WHEN 'cpg' THEN kcg.VillageID
           WHEN 'koperasi' THEN kc.VillageID
           WHEN 'trader' THEN kt.VillageID
        END AS VillageID,
        CASE ObjType
           WHEN 'farmer' THEN kcf.PartnerID
           WHEN 'cpg' THEN kcg.PartnerID
           WHEN 'koperasi' THEN kc.PartnerID
           WHEN 'trader' THEN kt.PartnerID
        END AS PartnerID,
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
        IF(ga.ClonalID,1,0) AS is_area
    FROM ktv_clonal_garden cg
    LEFT JOIN (
        SELECT
            kcf.FarmerID
            , kcf.FarmerName
            , kcf.VillageID
            , c.PartnerID
        FROM ktv_cocoa_farmer kcf
        LEFT JOIN ktv_cpg_partner c ON c.CPGid = kcf.CPGid    
    ) kcf ON kcf.FarmerID = cg.ObjID AND cg.ObjType = 'farmer'
    LEFT JOIN (
        SELECT
            kc.CPGid
            , kc.GroupName
            , kc.VillageID
            , c.PartnerID
        FROM ktv_cpg kc
        LEFT JOIN ktv_cpg_partner c ON c.CPGid = kc.CPGid    
    ) kcg ON kcg.CPGid = cg.ObjID AND cg.ObjType = 'cpg'
    LEFT JOIN (
        SELECT
            c.CoopID
            , c.CoopName
            , c.VillageID
            , sop.PartnerID
        FROM ktv_cooperatives c
        LEFT JOIN ktv_supplychain_org so ON so.OrgID = c.CoopID AND so.OrgType = 'koperasi'
        LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
    ) kc ON kc.CoopID = cg.ObjID AND cg.ObjType = 'koperasi'
    LEFT JOIN (
        SELECT
            t.TraderID
            , t.TraderName
            , t.VillageID
            , sop.PartnerID
        FROM ktv_traders t
        LEFT JOIN ktv_supplychain_org so ON so.OrgID = t.TraderID AND so.OrgType = 'trader'
        LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
    ) kt ON kt.TraderID = cg.ObjID AND cg.ObjType = 'trader'
    LEFT JOIN ktv_clonal_garden_area ga ON ga.ClonalID = cg.ClonalID
    WHERE cg.StatusCode = 'active'
        AND cg.Latitude is not null AND cg.Latitude!='0.000000' AND cg.Longitude is not null AND cg.Longitude!='0.000000'
    GROUP BY cg.ClonalID
) kc
WHERE
    1 = 1
    AND substr(kc.VillageID,1,2)=? AND substr(kc.VillageID,1,4) like ?
    AND (kc.`name`=? OR kc.`name` like ?)
    --where--
        ";
        $where = '';
        if ($_SESSION['role'] == 'Private') {
            $where .= " AND kc.PartnerID = {$_SESSION['PartnerID']}";
        } elseif ($_SESSION['role'] == 'Program') {
            $where .= " AND substr(kc.VillageID,1,4) IN (SELECT DistrictID FROM ktv_access_staff WHERE StaffID = {$_SESSION['staffid']})";
        }
        $sql_clonal = str_replace('--where--', $where, $sql_clonal);
        $query_clonal = $this->db->query($sql_clonal, array($ProvinceID, $DistrictID==''?'%%':$DistrictID, $Keyword==''?'%%':"%$Keyword%", $Keyword==''?'%%':"%$Keyword%"));
        $result['clonal'] = $query_clonal->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $result['total'] =
          sizeof($result['farmer'])
          +sizeof($result['farmer_certified'])
          +sizeof($result['nursery'])
          +sizeof($result['demoplot'])
          +sizeof($result['farmer_organization'])
          +sizeof($result['trader'])
          +sizeof($result['clonal'])
          // +sizeof($result['unitbuying'])
          +sizeof($result['warehouse']);
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
                $where = "AND substr(kcf.VillageID,1,4) in ({$daerah_ids})";
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
    FROM ktv_cocoa_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_cocoa_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_cocoa_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_cocoa_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
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
    FROM ktv_cocoa_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_cocoa_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_cocoa_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_cocoa_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
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
    FROM ktv_cocoa_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_cocoa_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_cocoa_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_cocoa_farmer_garden_area` ga ON ga.FarmerID = kcfg.FarmerID AND ga.GardenNr = kcfg.GardenNr
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
            $where = "AND substr(kc.VillageID,1,4) in ({$daerah_ids})";
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
            LEFT JOIN ktv_cooperative_staff kcs ON kc.CoopID=kcs.CoopID and Position='ketua'
            LEFT JOIN ktv_cocoa_farmer kcf ON kcs.FarmerID=kcf.FarmerID
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and substr(kc.VillageID,1,2)=? and substr(kc.VillageID,1,4) like ? and
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
            LEFT JOIN ktv_trader_staff kcs ON kc.TraderID=kcs.TraderID and Position='pemilik'
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND
               kc.Longitude!='0.000000' and substr(kc.VillageID,1,2)=? and substr(kc.VillageID,1,4) like ? and
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

    public function getDistrictCount($ProvinceID, $DistrictID, $Keyword, $skop)
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
   SUBSTR(kcf.VillageID,1,4) AS DistrictID,
   COUNT(DISTINCT kcfg.`FarmerID`) AS `count`,
   kcfg.Latitude AS Latitude,
   kcfg.Longitude AS Longitude
FROM `ktv_cocoa_farmer_garden` kcfg
JOIN (
SELECT
  FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
FROM `ktv_cocoa_farmer_garden`
GROUP BY FarmerID, GardenNr
) z ON z.FarmerID = kcfg.`FarmerID` AND z.GardenNr = kcfg.`GardenNr` AND z.SurveyNr = kcfg.`SurveyNr`
JOIN `ktv_cocoa_farmer` kcf ON kcfg.FarmerID = kcf.FarmerID
LEFT JOIN `ktv_cocoa_certification` kcc ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr=kcc.GardenNr AND kcfg.SurveyNr=kcc.SurveyNr AND ExternalDate>'0000-00-00'
WHERE 1 = 1
   AND kcf.StatusCode='active' AND kcc.FarmerID IS NULL
   AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000') AND kcfg.StatusGPS = 'verified'
AND SUBSTR(kcf.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
AND SUBSTR(kcf.VillageID,1,2)=? and (kcfg.FarmerID like ? OR kcf.OldFarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':$Keyword);
            $query = $this->db->query($sql, (!empty($districts)?array_merge($districts, $params):$params));
            $result['farmer'] = $query->result_array();
        } else {
            $result['farmer'] = array();
        }

        if ($DistrictID=='null') {
            $DistrictID='';
        }
        $sql_tersertifikasi = "SELECT
    r.*
    ,d.`District`
FROM (
SELECT
   SUBSTR(kcf.VillageID,1,4) AS DistrictID,
   COUNT(DISTINCT kcfg.`FarmerID`) AS `count`,
   kcfg.Latitude AS Latitude,
   kcfg.Longitude AS Longitude
FROM `ktv_cocoa_farmer_garden` kcfg
JOIN (
SELECT
  FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
FROM `ktv_cocoa_farmer_garden`
GROUP BY FarmerID, GardenNr
) z ON z.FarmerID = kcfg.`FarmerID` AND z.GardenNr = kcfg.`GardenNr` AND z.SurveyNr = kcfg.`SurveyNr`
JOIN `ktv_cocoa_certification` kcc ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr=kcc.GardenNr AND kcfg.SurveyNr=kcc.SurveyNr AND ExternalDate>'0000-00-00'
JOIN `ktv_cocoa_farmer` kcf ON kcfg.FarmerID = kcf.FarmerID
WHERE 1 = 1
   AND kcf.StatusCode='active'
   AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000') AND kcfg.StatusGPS = 'verified'
AND SUBSTR(kcf.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
AND SUBSTR(kcf.VillageID,1,2)=? and (kcfg.FarmerID like ? OR kcf.OldFarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':$Keyword);
            $query = $this->db->query($sql_tersertifikasi, (!empty($districts)?array_merge($districts, $params):$params));
            $result['farmer_certified'] = $query->result_array();
        } else {
            $result['farmer_certified'] = array();
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $sql_nursery = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
                SUBSTR(DistrictIDWhere,1,4) AS DistrictID
                , COUNT(DISTINCT NurseryID) AS `count`
                , Latitude
                , Longitude
            FROM
            (
            SELECT
                kcn.NurseryID
                , kcn.ObjID
                , CASE
                    WHEN kcn.ObjType = 'cpg' THEN kcn.ObjID
                    WHEN kcn.ObjType = 'farmer' THEN kcn.ObjID
                    WHEN kcn.ObjType = 'koperasi' THEN
                        (SELECT VillageID FROM ktv_cooperatives WHERE CoopID = kcn.`ObjID`)
                    WHEN kcn.`ObjType` = 'trader' THEN
                        (SELECT VillageID FROM ktv_traders WHERE TraderID = kcn.`ObjID`)
                END AS DistrictIDWhere
                , IFNULL(CASE
                    WHEN kcn.ObjType = 'cpg' THEN
                        (SELECT GroupName FROM ktv_cpg WHERE CPGid = kcn.`ObjID`)
                    WHEN kcn.ObjType = 'farmer' THEN
                        (SELECT FarmerName FROM ktv_cocoa_farmer WHERE FarmerID = kcn.ObjID)
                    WHEN kcn.ObjType = 'koperasi' THEN
                        (SELECT CoopName FROM ktv_cooperatives WHERE CoopID = kcn.`ObjID`)
                    WHEN kcn.`ObjType` = 'trader' THEN
                        (SELECT TraderName FROM ktv_traders WHERE TraderID = kcn.`ObjID`)
                END,'-') AS ObjNameNya
                ,kcn.Latitude AS Latitude
                ,kcn.Longitude AS Longitude
            FROM
                ktv_nursery kcn
            WHERE
                kcn.Latitude IS NOT NULL AND kcn.Latitude!='0.000000' AND kcn.Longitude IS NOT NULL AND kcn.Longitude!='0.000000'
                AND kcn.StatusCode = 'active'
            ) AS tbl_grouped
            WHERE
                SUBSTR(DistrictIDWhere,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
                AND SUBSTR(DistrictIDWhere,1,2) = ?
                AND (ObjID = ? OR ObjNameNya LIKE ?)
            GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':$Keyword,$Keyword==''?'%%':"%$Keyword%");
            $query_nursery = $this->db->query($sql_nursery, (!empty($districts)?array_merge($districts, $params):$params));
            $result['nursery'] = $query_nursery->result_array();
        } else {
            $result['nursery'] = array();
        }
        //echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_demoplot = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
            SUBSTR(kcf.VillageID,1,4) AS DistrictID
            ,COUNT(DISTINCT kcf.FarmerID) AS `count`
            ,kcfg.Latitude AS Latitude
            ,kcfg.Longitude AS Longitude
            FROM ktv_cpg_batch_trainings kcbt
            JOIN ktv_cpg_trainings kct ON kct.CpgTrainingsID = kcbt.CPGtrainingsID AND kcbt.CpgTrainingsID IN (1,2)
            LEFT JOIN ktv_cpg kc ON kcbt.CPGid=kc.CPGid
            LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID=kcbt.DemoplotOwnerID
            LEFT JOIN (
                  SELECT a.FarmerID,MAX(SurveyNr) AS survey FROM `ktv_cocoa_farmer_garden` a WHERE GardenNr=1 GROUP BY FarmerID
               ) kcfgt ON kcfgt.FarmerID = kcf.FarmerID
            LEFT JOIN ktv_cocoa_farmer_garden kcfg ON kcfgt.FarmerID=kcfg.FarmerID AND kcfg.GardenNr=1 AND kcfg.SurveyNr=kcfgt.survey
            WHERE kcfg.Latitude is not null AND kcfg.Latitude!='0.000000' AND kcfg.Longitude is not null AND kcfg.Longitude!='0.000000' AND kcfg.StatusGPS = 'verified'
AND SUBSTR(kcf.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
               AND substr(kcf.VillageID,1,2)=? and (kc.CPGid=? OR kc.GroupName like ? OR kcf.FarmerID=? OR kcf.FarmerName like ?)
            GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
               // and kc.Status='active'
        if (true) {
            $params = array($ProvinceID,$Keyword,$Keyword==''?'%%':"%$Keyword%",$Keyword,$Keyword==''?'%%':"%$Keyword%");
            $query_demoplot = $this->db->query($sql_demoplot, (!empty($districts)?array_merge($districts, $params):$params));
            $result['demoplot'] = $query_demoplot->result_array();
        } else {
            $result['demoplot'] = array();
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_koperasi = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
            SUBSTR(kc.VillageID,1,4) AS DistrictID
            ,COUNT(DISTINCT kc.`CoopID`) AS `count`
            ,kc.Latitude AS Latitude
            ,kc.Longitude AS Longitude
            FROM ktv_cooperatives kc
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND SUBSTR(kc.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and substr(kc.VillageID,1,2)=? and
               (kc.CoopID=? OR kc.CoopName like ?)
            GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
            $query_koperasi = $this->db->query($sql_koperasi, (!empty($districts)?array_merge($districts, $params):$params));
            $result['farmer_organization'] = $query_koperasi->result_array();
        } else {
            $result['farmer_organization'] = array();
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo ';</pre>';exit;

        $sql_gudang = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
            SUBSTR(kc.VillageID,1,4) AS DistrictID
            ,COUNT(DISTINCT kc.`WarehouseID`) AS `count`
            ,kc.Latitude AS Latitude
            ,kc.Longitude AS Longitude
            FROM ktv_warehouse kc
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND SUBSTR(kc.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and substr(kc.VillageID,1,2)=?
               AND (kc.WarehouseID=? OR kc.WarehouseName like ?)
            GROUP BY DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
            $query_gudang = $this->db->query($sql_gudang, (!empty($districts)?array_merge($districts, $params):$params));
            $result['warehouse'] = $query_gudang->result_array();
        } else {
            $result['warehouse'] = array();
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $sql_pedagang = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
                SUBSTR(kc.VillageID,1,4) AS DistrictID
                ,COUNT(DISTINCT kc.`TraderID`) AS `count`
                ,kc.Latitude AS Latitude
                ,kc.Longitude AS Longitude
            FROM ktv_traders kc
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND SUBSTR(kc.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and substr(kc.VillageID,1,2)=? and (kc.TraderID=? OR kc.TraderName like ?)
            group by DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
            $query_pedagang = $this->db->query($sql_pedagang, (!empty($districts)?array_merge($districts, $params):$params));
            $result['trader'] = $query_pedagang->result_array();
        } else {
            $result['trader'] = array();
        }

        $sql_clonal = "SELECT
    r.*
    ,d.`District`
FROM (
            SELECT
                SUBSTR(kc.VillageID,1,4) AS DistrictID
                ,COUNT(DISTINCT kc.`ClonalID`) AS `count`
                ,kc.Latitude AS Latitude
                ,kc.Longitude AS Longitude
            FROM (
    SELECT
        ClonalID,
        CASE ObjType
           WHEN 'farmer' THEN CONCAT(kcf.FarmerName,'( ',kcf.FarmerID,')')
           WHEN 'koperasi' THEN kc.CoopName
        END AS `name`,
        CASE ObjType
           WHEN 'farmer' THEN kcf.VillageID
           WHEN 'koperasi' THEN kc.VillageID
        END AS VillageID,
        cg.Latitude,
        cg.Longitude
    FROM ktv_clonal_garden cg
    LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID = cg.ObjID AND cg.ObjType = 'farmer'
    LEFT JOIN ktv_cooperatives kc ON kc.CoopID = cg.ObjID AND cg.ObjType = 'koperasi'
    WHERE
        cg.StatusCode = 'active' AND cg.Latitude is not null AND cg.Latitude!='0.000000' AND cg.Longitude is not null AND cg.Longitude!='0.000000'
            ) kc
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND SUBSTR(kc.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and substr(kc.VillageID,1,2)=? AND (kc.name like ?)
            group by DistrictID
) r
JOIN `ktv_district` d ON d.`DistrictID` = r.DistrictID;
";
        if (true) {
            $params = array($ProvinceID,$Keyword==''?'%%':"%$Keyword%",$Keyword==''?'%%':"%$Keyword%");
            $query_clonal = $this->db->query($sql_clonal, (!empty($districts)?array_merge($districts, $params):$params));
            $result['clonal'] = $query_clonal->result_array();
        } else {
            $result['clonal'] = array();
        }

        $result['total'] =
          @$result['farmer'][0]['count']
          +@$result['farmer_certified'][0]['count']
          +@$result['nursery'][0]['count']
          +@$result['demoplot'][0]['count']
          +@$result['farmer_organization'][0]['count']
          +@$result['trader'][0]['count']
          +@$result['clonal'][0]['count']
          // +@sizeof($result['unitbuying'])
          +@$result['warehouse'][0]['count'];
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
   SUBSTR(kcf.VillageID,1,4) AS DistrictID,
   SUM(IF(kcf.isLoanPassed = 1 AND kcf.isCertified = 0, 1, 0)) AS `farmer`,
   SUM(IF(kcf.isLoanPassed = 1 AND kcf.isCertified = 1, 1, 0)) AS `farmer_certified`,
   SUM(IF(kcf.isLoanPassed = 0, 0, 1)) AS `farmer_unmeet`,
   kcfg.Latitude AS Latitude,
   kcfg.Longitude AS Longitude
FROM (
    SELECT
        g.FarmerID, g.Latitude, g.Longitude, g.StatusGPS
    FROM ktv_cocoa_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_cocoa_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) kcfg
JOIN `ktv_cocoa_farmer_view` kcf ON kcfg.FarmerID = kcf.FarmerID
WHERE 1 = 1
   AND kcf.StatusCode='active'
   AND (kcfg.Latitude IS NOT NULL AND kcfg.Latitude!='0.000000' AND kcfg.Longitude IS NOT NULL AND kcfg.Longitude!='0.000000') AND kcfg.StatusGPS = 'verified'
AND SUBSTR(kcf.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
AND SUBSTR(kcf.VillageID,1,2)=? and (kcfg.FarmerID like ? OR kcf.FarmerName like ? OR kcf.CPGid = ?)
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
// FROM `ktv_cocoa_farmer_garden` kcfg
// JOIN (
// SELECT
//   FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
// FROM `ktv_cocoa_farmer_garden`
// GROUP BY FarmerID, GardenNr
// ) z ON z.FarmerID = kcfg.`FarmerID` AND z.GardenNr = kcfg.`GardenNr` AND z.SurveyNr = kcfg.`SurveyNr`
// JOIN `ktv_cocoa_certification` kcc ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr=kcc.GardenNr AND kcfg.SurveyNr=kcc.SurveyNr AND ExternalDate>'0000-00-00'
// JOIN `ktv_cocoa_farmer` kcf ON kcfg.FarmerID = kcf.FarmerID
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
            SUBSTR(kc.VillageID,1,4) AS DistrictID
            ,COUNT(kc.`CoopID`) AS `count`
            ,kc.Latitude AS Latitude
            ,kc.Longitude AS Longitude
            FROM ktv_cooperatives kc
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND SUBSTR(kc.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and substr(kc.VillageID,1,2)=? and
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
                SUBSTR(kc.VillageID,1,4) AS DistrictID
                ,COUNT(kc.`TraderID`) AS `count`
                ,kc.Latitude AS Latitude
                ,kc.Longitude AS Longitude
            FROM ktv_traders kc
            WHERE kc.Latitude is not null AND kc.Latitude!='0.000000' AND kc.Longitude is not null AND kc.Longitude!='0.000000'
            AND SUBSTR(kc.VillageID,1,4) ".(!empty($districts)?("IN (". implode(',', array_fill(0, count($districts), '?')).")"):'')."
            and substr(kc.VillageID,1,2)=? and (kc.TraderID=? OR kc.TraderName like ?)
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

    public function readProvince($user)
    {
        if ($user['is_admin'] == 1) {
            $user['district_access'] = '0';
        } else {
            if ($user['type'] == 'private' || $user['type'] == 'program') {
                if ($user ['FlagAccess'] == 1 && !empty($user['accessCPG'])) {
                    $user['district_access'] = $user['accessCPG'];
                } elseif (!empty($user['accessStaff'])) {
                    $user['district_access'] = $user['accessStaff'];
                } elseif (!empty($user['accessDistrict'])) {
                    $user['district_access'] = $user['accessDistrict'];
                }
            }
        }
        // echo '<pre>'; print_r($user); echo '</pre>'; exit;
        
         $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, op.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_supplychain_org o ON o.OrgID=s.ObjID AND o.OrgType=s.ObjType
                    LEFT JOIN ktv_supplychain_org_partner op ON op.SupplychainID=o.SupplychainID
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array($_SESSION['userid']))->result_array();
        //echo "<pre>".$this->db->last_query();exit;
        $staff = $query[0];
        if($staff['ObjType']=='private' || $staff['ObjType']=='private'){
            $this->PartnerID = $staff['ObjID'];
        }else{
            $this->PartnerID = $staff['PartnerID'];
        }
        $this->DistrictID = $staff['DistrictID'];
        $d1 = $this->DistrictID!='' ? '' : '*/';
        $d2 = $this->DistrictID!='' ? '' : '*/';
        
        
        
        $sql_where = '';
        $sql_where = " and (DistrictID in ({$user['district_access']}) OR '0' = '{$user['district_access']}')";
        $sql = "
            SELECT a.ProvinceID AS id, a.Province AS province
            FROM ktv_province a
            LEFT JOIN ktv_district b ON a.ProvinceID=b.ProvinceID
            WHERE a.ProvinceID NOT IN (31) $d1 AND b.DistrictID IN ($this->DistrictID) $d2
            GROUP BY a.ProvinceID
            ORDER BY a.Province";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readDistrict($ProvinceID, $user)
    {
        // $sql = "
        //     select DistrictID as id, District as district
        //      from ktv_district
        //      where ProvinceID=$ProvinceID AND DistrictID IN (SELECT
        //          DistrictID
        //       FROM
        //         ktv_district a,
        //         ktv_private_staff b
        //       WHERE a.PartnerID = b.PartnerID
        //         AND b.UserID = $userID
        //       UNION
        //       SELECT
        //         DistrictID
        //       FROM
        //         ktv_district a,
        //         ktv_program_staff b
        //       WHERE a.PartnerID = b.PartnerID
        //         AND b.UserID =  $userID) ";
         $sql_where = '';
        // if ($_SESSION['daerah']!='') {
        //     $dae = explode(',', $_SESSION['daerah']);
        //     for ($i=0;$i<sizeof($dae);$i++) {
        //         $da = explode('##', $dae[$i]);
        //         $d[] = $da[0];
        //     }
        //     $sql_where = " and (DistrictID in (".implode(',', $d).") OR DistrictID is null)";
        // }
         if ($user['is_admin'] == 1) {
            $user['district_access'] = '0';
        } else {
            if ($user['type'] == 'private' || $user['type'] == 'program') {
                if ($user ['FlagAccess'] == 1 && !empty($user['accessCPG'])) {
                    $user['district_access'] = $user['accessCPG'];
                } elseif (!empty($user['accessStaff'])) {
                    $user['district_access'] = $user['accessStaff'];
                } elseif (!empty($user['accessDistrict'])) {
                    $user['district_access'] = $user['accessDistrict'];
                }
            }
        }
        $sql_where = " and (DistrictID in ({$user['district_access']}) OR '0' = '{$user['district_access']}')";
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

    public function readArea($farmer, $garden, $survey = 0)
    {
        $sql = "
SELECT
  ga.Latitude,
  ga.Longitude,
  ga.Revision
FROM `ktv_cocoa_farmer_garden_area` ga
JOIN (
  SELECT
    ga.FarmerID, ga.GardenNr, ga.SurveyNr, MAX(Revision) AS Revision
  FROM ktv_cocoa_farmer_garden_area ga
  WHERE
    ga.Status = 'verified'
  GROUP BY ga.FarmerID, ga.GardenNr, ga.SurveyNr
) r ON ga.FarmerID = r.FarmerID AND ga.GardenNr = r.GardenNr AND ga.SurveyNr = r.SurveyNr AND ga.Revision = r.Revision
WHERE
  ga.FarmerID = ?
  AND ga.GardenNr = ?
  AND ga.SurveyNr = ?
ORDER BY ga.OrderNr
      ";
        $query = $this->db->query($sql, array($farmer, $garden, $survey));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        $sql = "
SELECT
  ga.Latitude,
  ga.Longitude
FROM `ktv_cocoa_farmer_garden_area` ga
JOIN (
    SELECT
        ga.FarmerID, ga.GardenNr, ga.SurveyNr, MAX(Revision) AS Revision
    FROM ktv_cocoa_farmer_garden_area ga
    JOIN (
        SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
        FROM ktv_cocoa_farmer_garden_area
        GROUP BY FarmerID, GardenNr
    ) z ON ga.FarmerID = z.FarmerID AND ga.GardenNr = z.GardenNr AND ga.SurveyNr = z.SurveyNr
    WHERE
        ga.Status = 'verified'
    GROUP BY ga.FarmerID, ga.GardenNr, ga.SurveyNr
) z ON z.FarmerID = ga.FarmerID AND z.GardenNr = ga.GardenNr AND z.SurveyNr = ga.SurveyNr
WHERE
  ga.FarmerID = ?
  AND ga.GardenNr = ?
ORDER BY ga.OrderNr
      ";
        $query = $this->db->query($sql, array($farmer, $garden, $survey));
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
        $query = $this->db->get_where('ktv_cocoa_farmer_garden_area', $arr_criteria);
      //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
      if ($query->num_rows() > 0) {
          return true;
      } else {
          return false;
      }
    }

    public function deleteGardenArea($farmer_id, $garden_nr, $survey_nr)
    {
        return $this->db->delete('ktv_cocoa_farmer_garden_area', array(
            'FarmerID'    => $farmer_id,
            'GardenNr'    => $garden_nr,
            'SurveyNr'    => $survey_nr,
      ));
    }

    public function insertGardenArea($data)
    {
        return $this->db->insert('ktv_cocoa_farmer_garden_area', $data);
    }

    public function updateGardenArea($area, $farmer_id, $garden_nr, $survey_nr)
    {
        $result = false;
        if (is_array($area)) {
            $this->db->trans_start(false);
        // delete old area
        $this->db->where('FarmerID', $farmer_id);
            $this->db->where('GardenNr', $garden_nr);
            $this->db->where('SurveyNr', $survey_nr);
            $this->db->delete('ktv_cocoa_farmer_garden_area');
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // insert new area
        $no = 1;
            $data = array();
            foreach ($area as $val) {
                $data[] = array(
            'FarmerID'    => $farmer_id,
            'GardenNr'    => $garden_nr,
            'OrderNr'     => $no,
            'Latitude'    => $val[0],
            'Longitude'   => $val[1]
           );
                $no++;
            }
            $this->db->insert_batch('ktv_cocoa_farmer_garden_area', $data);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        $this->db->trans_complete();
            $result = $this->db->trans_status();
        }
        return $result;
    }

    public function updateGarden($area, $revision, $farmer_id, $garden_nr, $survey_nr = 0)
    {
        $sql = "
UPDATE ktv_cocoa_farmer_garden
SET
    GardenHaPolygon = ?
    , AreaRevision = ?
WHERE
    FarmerID = ?
    AND GardenNr = ?
    AND SurveyNr = ?
        ";
        return $this->db->query($sql, array($area, $revision, $farmer_id, $garden_nr, $survey_nr));

        // $data = array('GardenHaPolygon' => $area);
        // $condition = array('FarmerID'=>$farmer_id, 'GardenNr'=>$garden_nr, 'SurveyNr'=>$survey_nr);
        // return $this->db->update('ktv_cocoa_farmer_garden', $data, $condition);
    }

    public function updateGardenHaPolygon()
    {
        $sql = "UPDATE ktv_cocoa_farmer_garden g1, (
SELECT
    g.FarmerID, g.GardenNr, g.GardenHaPolygon
FROM ktv_cocoa_farmer_garden g
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
FROM ktv_cocoa_farmer_garden_area ga
JOIN ktv_cocoa_farmer_garden g ON g.FarmerID = ga.FarmerID AND g.GardenNr = ga.GardenNr
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
    public function getSupplyChain($start, $end, $province = '', $partner = '', $certification = '1', $warehouse = NULL)
    {
        $sql = "
                SELECT DISTINCT
                    '' 1_supplychainid, '' 1_orgid, '' 1_orgtype, '' 1_name, IFNULL(vso2.Latitude,vso.Latitude) 1_latitude, IFNULL(vso2.Longitude,vso.Longitude) 1_longitude,
                    vso2.SupplychainID 2_supplychainid, vso2.OrgID 2_orgid, so2.OrgType 2_orgtype, vso2.`Name` 2_name, IFNULL(vso2.Latitude,vso.Latitude) 2_latitude, IFNULL(vso2.Longitude,vso.Longitude) 2_longitude,
                    vso.SupplychainID wh_supplychainid, vso.OrgID wh_orgid, so.OrgType wh_orgtype, vso.`Name` wh_name, vso.Latitude wh_latitude, vso.Longitude wh_longitude
                FROM
                    ktv_supplychain_batch sb1
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=sb1.SupplyOrgID
                    LEFT JOIN ktv_supplychain_transaction st1 ON st1.SupplyBatchID=sb1.SupplyBatchID
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st1.SupplyID AND st1.SupplyType='Batch'
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN ktv_supplychain_org so2 ON so2.SupplychainID=vso2.SupplychainID
                    LEFT JOIN ktv_supplychain_org so ON so.SupplychainID=vso.SupplychainID
                    LEFT JOIN ktv_village v2 ON v2.VillageID=vso2.VillageID
                WHERE vso.OrgID=? AND IFNULL(st2.DateTransaction,st1.DateTransaction) BETWEEN ? AND ?
        ";
        //$result = $this->db->query($sql, array($warehouse, $start, $end))->result_array();
        $sql= "SELECT
                    o3.SupplychainID 1_supplychainid, o3.OrgID 1_orgid, IF(o3.OrgType='agent', 'trader', o3.OrgType) 1_orgtype, o3.`Name` 1_name, o3.Latitude 1_latitude, o3.Longitude 1_longitude,
                    o2.SupplychainID 2_supplychainid, o2.OrgID 2_orgid, IF(o2.OrgType='agent', 'trader', o2.OrgType) 2_orgtype, o2.`Name` 2_name, o2.Latitude 2_latitude, o2.Longitude 2_longitude,
                    o1.SupplychainID wh_supplychainid, o1.OrgID wh_orgid, 'warehouse' wh_orgtype, o1.`Name` wh_name, o1.Latitude wh_latitude, o1.Longitude wh_longitude
                FROM
                    ktv_supplychain_org_rel r1
                    LEFT JOIN view_supplychain_org o1 ON o1.SupplychainID=r1.ParentOrgId
                    LEFT JOIN view_supplychain_org o2 ON o2.SupplychainID=r1.ChildOrgId
                    LEFT JOIN ktv_supplychain_org_rel r2 ON r2.ParentOrgId=r1.ChildOrgId
                    LEFT JOIN view_supplychain_org o3 ON o3.SupplychainID=r2.ChildOrgId
                WHERE r1.ParentOrgId=? AND NOW() BETWEEN r2.StartDate AND r2.EndDate AND NOW() BETWEEN r1.StartDate AND r1.EndDate";
        $result = $this->db->query($sql, array($warehouse))->result_array();
        return $result;
    }
    

    public function getSupplyChainNew($start, $end, $province = '', $partner = '', $certification = '1', $warehouse = NULL)
    {
        // $sql= "SELECT
        //             o3.SupplychainID 1_supplychainid, o3.ObjID 1_orgid, IF(o3.ObjType='agent', 'trader', o3.ObjType) 1_orgtype, o3.`Name` 1_name, o3.Latitude 1_latitude, o3.Longitude 1_longitude,
        //             o2.SupplychainID 2_supplychainid, o2.ObjID 2_orgid, IF(o2.ObjType='agent', 'trader', o2.ObjType) 2_orgtype, o2.`Name` 2_name, o2.Latitude 2_latitude, o2.Longitude 2_longitude,
        //             o1.SupplychainID wh_supplychainid, o1.ObjID wh_orgid, 'warehouse' wh_orgtype, o1.`Name` wh_name, o1.Latitude wh_latitude, o1.Longitude wh_longitude
        //         FROM
        //             ktv_tc_supplychain_org_rel r1
        //             LEFT JOIN view_tc_supplychain_org o1 ON o1.SupplychainID=r1.ParentID
        //             LEFT JOIN view_tc_supplychain_org o2 ON o2.SupplychainID=r1.ChildID
        //             LEFT JOIN ktv_tc_supplychain_org_rel r2 ON r2.ParentID=r1.ChildID
        //             LEFT JOIN view_tc_supplychain_org o3 ON o3.SupplychainID=r2.ChildID
        //         WHERE r1.ParentID=? AND NOW() BETWEEN r2.StartDate AND r2.EndDate AND NOW() BETWEEN r1.StartDate AND r1.EndDate";
        // $result = $this->db->query($sql, array($warehouse))->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;

        $sql = "SELECT DISTINCT
                    vso.SupplychainID AS wh_supplychainid, 
                    vso.ObjID AS wh_orgid,
                    'warehouse' AS wh_orgtype,
                    vso.`Name` AS wh_name,
                    vso.Latitude AS wh_latitude,
                    vso.Longitude AS wh_longitude,
                    
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, '', vso2.SupplychainID) `1_supplychainid`, 
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, '', vso2.ObjID) `1_orgid`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, 'trader', 'trader') `1_orgtype`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, '', vso2.`Name`) `1_name`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso.Latitude, vso2.Latitude) `1_latitude`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso.Longitude, vso2.Longitude) `1_longitude`,
                    
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.SupplychainID, vso3.SupplychainID) `1_supplychainid`, 
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.ObjID, vso3.ObjID) `1_orgid`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, 'trader', 'trader') `1_orgtype`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.`Name`, vso3.`Name`) `1_name`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.Latitude, vso3.Latitude) `1_latitude`,
                    IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.Longitude, vso3.Longitude) `1_longitude`
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyBatchID 
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID 
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID

                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IF(st.SupplyBatchType='Untraceable', IF(st.SupplyBatchSourceType='1', st.MIllID, st.DOID), sb2.SupplyOrgID)
                    LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IF(st2.SupplyBatchType='Untraceable', IF(st2.SupplyBatchSourceType='1', st2.MIllID, st2.DOID), sb3.SupplyOrgID)
                WHERE
                    ktsd.SupplyDestMillOrgID = ?
                    AND vso2.SupplychainID IS NOT NULL
                    AND ktsd.DeliveryStatusID = '4'
                    AND ktsd.StatusCode = 'active'
                    AND vso3.SupplychainID IS NULL
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ?
                    GROUP BY ktsdd.DeliveryID ";
                    
         $query = $this->db->query($sql,array($warehouse,$start,$end));
         
         return $query->result_array();
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
    LEFT JOIN ktv_cocoa_farmer d ON a.farmer_id=d.FarmerID
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
                FROM ktv_cocoa_farmer_garden g
                JOIN (
                  SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr
                  FROM ktv_cocoa_farmer_garden g
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

    public function getSupplyChainRefinery($id, $supply_id, $start, $end, $partner = '', $certification = '1', $wh){
        $filter = '';
        if($_SESSION['role'] == 'Refinery'){
            $filter .= ' AND desp.DestinationID = "'.$_SESSION['SupplychainID'].'"';
        }

        $sql = "SELECT
                desp.DestinationID id
                , vso.`Name` name
                , 'refinery' orgtype
                , vso.Latitude latitude
                , vso.Longitude longitude
                , count(tr.ReceptionID) reception_count
                , SUM(desp.DestpatchNetto) netto
            FROM
                ktv_tc_despatch desp
            INNER JOIN
                ktv_tc_reception tr on tr.DespatchID = desp.DespatchID
            LEFT JOIN
                view_tc_supplychain_org vso on vso.SupplychainID = desp.DestinationID
            WHERE
                desp.SupplychainID = ?
                $filter
            AND
                desp.StatusCode = 'active'
            AND
                tr.StatusCode = 'active'
            AND
                tr.ReceptionDate BETWEEN ? AND ?
            GROUP BY
                desp.DestinationID
            HAVING longitude IS NOT NULL";

        $query = $this->db->query($sql, array($supply_id, $start, $end));
        $result = $query->result_array();
        
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        return $result;
    }

    public function getSupplyChainFarmer($id, $supply_id, $start, $end, $partner = '', $certification = '1', $wh)
    {
        if($certification==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($certification=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
       
        $sql = "SELECT
                    CONCAT(m.MemberDisplayID,'_',IF(st.PlantationNr IS NULL || st.PlantationNr=0, 1, st.PlantationNr)) id,
                    m.MemberName name,
                    st.PlantationNr PlotNr,
                    '-' CPGid,
                    plot.latitude,
                    plot.longitude,
                    SUM(st.VolumeBruto) bruto,
                    SUM(st.VolumeNetto) netto,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT st.SupplyID) supply_count,
                    COUNT(DISTINCT st.SupplyTransID) transaction_count
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=IF(st.PlantationNr IS NULL || st.PlantationNr=0, 1, st.PlantationNr)

                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                    
                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

                WHERE
                    st2.SupplychainID = ? 
                    AND m.StatusCode = 'active'
                    AND m.StatusCode = 'active'
                    AND st.StatusCode =  'active'
                    AND sb.StatusCode = 'active'
                    AND ktsd.StatusCode = 'active'
                    AND ktsdd.StatusCode = 'active'
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ?
                    -- AND (IFNULL(sb3.SupplyOrgID, IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID))=? OR IFNULL(sb2.SupplyOrgID, IFNULL(st2.SupplychainID, sb.SupplyDestOrgID))=?)
                GROUP BY CONCAT(m.MemberDisplayID,'_',IF(st.PlantationNr IS NULL || st.PlantationNr=0, 1, st.PlantationNr))
                HAVING longitude IS NOT NULL";
        $query = $this->db->query($sql, array($supply_id, $start, $end, $wh, $wh));
        $result = $query->result_array();
        
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
    AND bb.StatusCode = 'active'
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
    
    public function getNewProvincePartner($PartnerID)
    {
        if($PartnerID=='' || $PartnerID=='1'){
            $p1 = "/*"; $p2 = "*/";
        }else{
            $p1 = ""; $p2 = "";
        }
        $sql = "SELECT DISTINCT b.PartnerID id, b.PartnerName name
                FROM ktv_warehouse a
                LEFT JOIN ktv_program_partner b ON a.PartnerID=b.PartnerID
                WHERE b.PartnerID IS NOT NULL AND a.StatusCode='active' AND a.PartnerID NOT IN (9) $p1 AND a.PartnerID=? $p2
                ORDER BY name";
        $query = $this->db->query($sql, array($PartnerID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getPartnerWarehouse($PartnerID)
    {
        $SupplychainID = $_SESSION['SupplychainID'];
        if($SupplychainID==''){
            $s1 = "/*"; $s2 = "*/";
        }else{
            $s1 = ""; $s2 = "";
        }

        if($_SESSION['role'] == 'Refinery'){
            $sql = "SELECT
                    vso2.SupplychainID id,
                    vso2.`Name` name
                FROM
                    view_tc_supplychain_org vso
                    INNER JOIN ktv_tc_supplychain_org_rel apm ON apm.ParentID = vso.SupplychainID
                    INNER JOIN view_tc_supplychain_org vso2 on vso2.SupplychainID = apm.ChildID
                -- 	INNER JOIN ktv_tc_supplychain_batch sb ON ( sb.SupplyDestMillOrgID = vso.SupplychainID OR sb.SupplyDestOrgID = vso.SupplychainID ) 
                -- 	AND sb.StatusCode = 'active' 
                WHERE
                1=1
                    AND vso2.ObjType = 'mill' 
                    AND vso2.ObjID != 0 
                    AND vso.SupplychainID = ?
                GROUP BY
                    vso.SupplychainID";
            $query = $this->db->query($sql, array($SupplychainID));
            return $query->result_array();
        }else{
            $sql = "SELECT SupplychainID id, Name name 
                FROM view_tc_supplychain_org vso 
                INNER JOIN ktv_access_partner_mill apm on apm.apmiMillID = vso.ObjID
                INNER JOIN ktv_tc_supplychain_batch sb on ( sb.SupplyDestMillOrgID = vso.SupplychainID OR sb.SupplyDestOrgID = vso.SupplychainID) AND sb.StatusCode ='active'
            WHERE ObjType='mill' AND ObjID != 0 $s1 AND SupplychainID=? $s2 AND apm.apmiPartnerID=? GROUP BY vso.SupplychainID";
            $query = $this->db->query($sql, array($SupplychainID, $PartnerID));
            if ($query->num_rows() > 0) {
                return $query->result_array();
            }else{
                $sql = "SELECT
                            vso.SupplychainID id,
                            vso.`Name` name
                        FROM
                            ktv_access_partner_mill apm 
                            LEFT JOIN view_tc_supplychain_org vso ON vso.ObjID=apm.apmiMillID AND vso.ObjType='mill'
                        WHERE 1=1
                            $s1 AND vso.SupplychainID=? $s2
                            AND apm.apmiPartnerID =? AND vso.SupplychainID IS NOT NULL AND vso.ObjID!=0
                        ORDER BY vso.`Name`";
                $query = $this->db->query($sql, array($SupplychainID, $PartnerID));
                if ($query->num_rows()>0) {
                    return $query->result_array();
                }   
            }
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
    LEFT JOIN ktv_cocoa_farmer kcf ON kcf.FarmerID = cg.ObjID AND cg.ObjType = 'farmer'
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

    public function getGardenDetail($FarmerID, $GardenNr)
    {
        $sql = "
SELECT
    kcf.FarmerID,
    kcf.FarmerName,
    kcfg.`SurveyNr`,
    kcfg.GardenNr,
    kcfg.GardenHaUnCertified,
    (kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) Pohon,
    (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS totalProduksi,
    ((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
      (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
      (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
    IFNULL(GardenHaUnCertified,0) as Produktivitas,
    kcfg.Latitude,
    kcfg.Longitude
FROM `ktv_cocoa_farmer_garden` kcfg
INNER JOIN (
  SELECT
  FarmerID, GardenNr, MAX(g.SurveyNr) AS SurveyNr
  FROM ktv_cocoa_farmer_garden g
  GROUP BY FarmerID, GardenNr
) ss ON ss.FarmerID = kcfg.FarmerID AND kcfg.GardenNr = ss.GardenNr AND kcfg.SurveyNr = ss.SurveyNr
JOIN `ktv_cocoa_farmer` kcf ON kcf.StatusCode='active' AND kcfg.FarmerID = kcf.FarmerID
WHERE
    1 = 1
    AND kcfg.FarmerID = ?
    AND kcfg.GardenNr = ?
GROUP BY kcfg.FarmerID,kcfg.GardenNr
        ";
        $query = $this->db->query($sql, array($FarmerID, $GardenNr));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyProfileRefinery($id, $start, $end, $wh, $cert, $parent)
    {
        $sql = "SELECT
                vso.DisplayID id
                , vso.`Name` name
                , 'refinery' orgtype
                , vso.Latitude latitude
                , vso.Longitude longitude
                , count(tr.ReceptionID) transaction_count
                , SUM(desp.DestpatchNetto) netto
            FROM
                ktv_tc_despatch desp
            INNER JOIN
                ktv_tc_reception tr on tr.DespatchID = desp.DespatchID
            LEFT JOIN
                view_tc_supplychain_org vso on vso.SupplychainID = desp.DestinationID
            WHERE
                desp.DestinationID = ?
            AND
                desp.SupplychainID = ?
            AND
                desp.StatusCode = 'active'
            AND
                tr.StatusCode = 'active'
            AND
                tr.ReceptionDate BETWEEN ? AND ?
            GROUP BY
                desp.DestinationID
            HAVING longitude IS NOT NULL";

        $query = $this->db->query($sql, array($id, $wh, $start, $end));
        
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyProfileFarmer($id, $start, $end, $wh, $cert, $parent)
    {
        if($certification==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($certification=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $i = explode("_",$id);
        
        $sql = "SELECT
                    IF(m.MemberDisplayID IS NOT NULL, 'Farmer', 'NewFarmer') SupplyType,
                    'farmer' member_type,
                    IFNULL(m.MemberDisplayID, nf.FarmerID) id,
                    IFNULL(m.MemberID, 'tba') member_id,
                    IFNULL(m.MemberName, nf.FarmerName) name, 
                    IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr) PlotNr,
                    '-' CPGid, '-' GroupName, 0 survey, 0 quota,
                    SUM(st.VolumeBruto1) bruto, SUM(st.VolumeNetto) netto,
                    IFNULL(v.Village, '-') Village,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT IF(sb.SupplyDestStatus IN ('Delivered', 'Sent'),st.SupplyTransID, NULL)) delivered_count
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID OR m.MemberDisplayID=st.SupplyID
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)
                    LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=st.SupplyID
                    LEFT JOIN ktv_temp_member_plot tm ON tm.MemberDisplayID=IFNULL(m.MemberDisplayID,nf.FarmerID) AND tm.PlotNr=IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)
                    LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, IFNULL(m.VillageID, nf.FarmerVillageID))
                WHERE 1=1  
                    AND vso.OrgType = 'agent' AND vso.OrgID = ? 
                    AND st.DateTransaction BETWEEN ? AND ? 
                    AND IFNULL(m.MemberDisplayID, nf.FarmerID)=? AND IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)=?";
        $sql = "SELECT 
                    st.SupplyType,
                    'farmer' member_type,
                    m.MemberDisplayID id,
                    m.MemberID member_id,
                    m.MemberName name,
                    IFNULL(st.PlantationNr, 1) PlotNr,
                    '-' CPGid, '-' GroupName, 0 survey, 0 quota,
                    SUM(st.VolumeBruto) bruto, SUM(st.VolumeNetto) netto,
                    IFNULL(v.Village, '-') Village,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT IF(sb.SupplyBatchStatus IN ('Delivered', 'Sent'),st.SupplyTransID, NULL)) delivered_count
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID 
                    LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, m.VillageID)
                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=ktsd.SupplyDestMillOrgID
                WHERE
                    m.MemberDisplayID=?
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ?";
        $query = $this->db->query($sql, array($i[0], $start, $end));
        
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionRefineryNew($id, $start, $end, $wh, $cert, $parent)
    {
        if($certification==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($certification=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $i = explode("_",$id);
        $sql = "SELECT
                    DATE_FORMAT(tr.ReceptionDate,'%Y-%m-%d') trans_date
                    , tr.ReceptionNumber trans_number
                    , SUM(desp.DestpatchNetto) netto
                    , vso2.SupplychainID dest_orgid
                    , vso2.ObjType dest_orgtype
                    , vso2.`name` dest_orgname
                    , 'Received' batch_status
                FROM
                    ktv_tc_despatch desp
                INNER JOIN
                    ktv_tc_reception tr on tr.DespatchID = desp.DespatchID
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = desp.DestinationID
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = desp.SupplychainID
                WHERE
                    desp.DestinationID = ?
                AND
                    desp.StatusCode = 'active'
                AND
                    tr.StatusCode = 'active'
                AND
                    desp.SupplychainID = ?
                AND
                    tr.ReceptionDate BETWEEN ? AND ?
                GROUP BY tr.ReceptionDate";
        $query = $this->db->query($sql, array($id, $wh, $start, $end));
        
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getSupplyTransactionFarmerNew($id, $start, $end, $wh, $cert, $parent)
    {
        if($certification==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($certification=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $i = explode("_",$id);
        $sql = "SELECT 
                    DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') trans_date,
                    st.TransNumber trans_number,
                    st.VolumeBruto bruto,
                    st.VolumeNetto netto,
                    vso2.ObjID dest_orgid, 
                    vso2.ObjType dest_orgtype, 
                    IFNULL(vso2.Name,'-') dest_orgname,
                    IFNULL(sb.SupplyBatchStatus, 'Open') batch_status

                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID 
                    LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, m.VillageID)
                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID= st.SupplychainID
                WHERE
                    m.MemberDisplayID=?
                    AND
                    DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ?
                ORDER BY st.DateTransaction DESC";
        $query = $this->db->query($sql, array($i[0], $start, $end ));
        
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getSupplyTransactionFarmer($id, $start, $end, $wh, $cert, $parent)
    {
        if($certification==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($certification=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $sql = "SELECT
                    st.DateTransaction trans_date, 
                    st.FakturNumber trans_number, 
                    (st.FAQVolumeBruto) bruto, (st.FAQVolumeBruto - (IFNULL(st.FAQVolumePackage,0)*IFNULL(st.FAQNumberPackage,0))) netto,
                    vso.OrgID dest_orgid, vso.OrgType dest_orgtype, vso.Name dest_orgname, sb.SupplyDestStatus batch_status
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=st.SupplyID
                WHERE f.FarmerID IS NOT NULL AND vso.OrgType = 'Pedagang' AND f.FarmerID=? AND vso.OrgID = ? AND (vso2.OrgID IS NULL OR vso2.OrgID=?) AND st.DateTransaction BETWEEN ? AND ? $c1 AND st.SupplyType IN $where_cert $c2";
        $query = $this->db->query($sql, array($id, $parent, $wh, $start, $end));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfilePedagang($id, $start, $end, $wh, $cert)
    {
        if($cert==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($cert=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $sql = "SELECT
                    vso.OrgID id,
                    vso.`Name` name,
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT IF(sb.SupplyDestStatus IN ('Delivered', 'Sent'),st.SupplyTransID, NULL)) delivered_count,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT st.SupplyID) farmer_count,
                    SUM(st.FAQVolumeBruto) bruto, SUM(st.FAQVolumeBruto - (IFNULL(st.FAQVolumePackage,0)*IFNULL(st.FAQNumberPackage,0))) netto
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                WHERE vso.OrgType = 'Pedagang' AND vso.OrgID = ? AND (vso2.OrgID IS NULL OR vso2.OrgID=?) AND st.DateTransaction BETWEEN ? AND ? $c1 AND st.SupplyType IN $where_cert $c2";
        $sql = "SELECT
                    org.OrgID id, org.`Name` name, 
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT IF(st.SupplyBatchID IS NOT NULL, st.SupplyBatchID, NULL)) delivered_count,
                    SUM(IFNULL(st.VolumeBruto1,0) - IFNULL(st.VolumeBruto2,0)) bruto,
                    COUNT(DISTINCT IF(st.SupplyType='Farmer', st.SupplyID, NULL)) farmer_count,
                    SUM(st.VolumeNetto) netto
                FROM
                    view_supplychain_org org 
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                WHERE org.OrgID=? AND org.OrgType='agent' AND (st2.SupplychainID=? OR st3.SupplychainID=?)";
        $query = $this->db->query($sql, array("$start 00:00:00", "$end 23:59:59", $id, $wh, $wh));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyProfilePedagangNew($id, $start, $end, $wh, $cert)
    {
        // $sql = "SELECT
        //             IFNULL(org.DisplayID, ObjID) id, org.`Name` name, 
        //             COUNT(DISTINCT st.SupplyTransID) transaction_count,
        //             COUNT(DISTINCT st.SupplyBatchID) batch_count,
        //             COUNT(DISTINCT IF(st.SupplyBatchID IS NOT NULL, st.SupplyBatchID, NULL)) delivered_count,
        //             IFNULL(SUM(IFNULL(st.VolumeBruto,0)), 0) bruto,
        //             COUNT(DISTINCT IF(st.SupplyType='Farmer', st.SupplyID, NULL)) farmer_count,
        //             IFNULL(SUM(st.VolumeNetto), 0) netto
        //         FROM
        //             view_tc_supplychain_org org 
        //             LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
        //             LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
        //             LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
        //             LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
        //             LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchID AND st3.SupplyType='Batch'
        //         WHERE org.ObjID=? AND org.ObjType='agent' AND (st2.SupplychainID=? OR st3.SupplychainID=?)";
        $sql = "SELECT 
                    dt.id, 
                    dt.name, 
                    COUNT(DISTINCT dt.SupplyTransID) transaction_count,
                    COUNT(DISTINCT dt.SupplyBatchID) batch_count,
                    COUNT(DISTINCT dt.delivered_id) delivered_count,
                    IFNULL(SUM(IFNULL(dt.bruto,0)), 0) bruto,
                    COUNT(DISTINCT farmer_id) farmer_count,
                    IFNULL(SUM(dt.netto), 0) netto
                FROM
                (
                    SELECT
                        IFNULL(km.MemberDisplayID, org.ObjID) id,
                        IFNULL(kme.agCompanyName, org.Name) AS name,
                        st.SupplyTransID,
                        st.SupplyBatchID,
                        IF(st.SupplyBatchID IS NOT NULL, st.SupplyBatchID, NULL) delivered_id,
                        st.VolumeBruto bruto,
                        IF(st.SupplyType='Farmer', st.SupplyID, NULL) farmer_id,
                        st.VolumeNetto netto
                    FROM
                        view_tc_supplychain_org org 
                        LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                        LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID
                        LEFT JOIN ktv_members_extension kme ON kme.MemberID = org.ObjID
                        LEFT JOIN ktv_members km ON km.MemberID = kme.MemberID
                        WHERE
                        km.StatusCode = 'active'
                        AND org.objID = ? AND st.DateTransaction BETWEEN ? AND ?
                    GROUP BY st.SupplyTransID
                ) dt";
        $query = $this->db->query($sql, array($id, "$start 00:00:00", "$end 23:59:59"));
        
        if ($query->num_rows()>0) {
            $ret = $query->row_array(0);
            if($ret['id']==''){
                $whouse = $this->db->query("SELECT IFNULL(DisplayID, ObjID) DisplayID, `Name` FROM view_tc_supplychain_org WHERE `ObjID`=? AND ObjType='agent'", array($id))->row_array(0);
                $ret['id'] = $whouse['DisplayID'];
                $ret['name'] = $whouse['Name'];
            }
            return $ret;
        }
        return false;
    }

    public function getSupplyTransactionPedagangNew($id, $start, $end, $wh, $cert)
    {
        $sql = "SELECT
                    DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') trans_date,
                    st.TransNumber batch_number,
                    st.VolumeBruto bruto,
                    st.VolumeNetto netto,
                    sb.SupplyBatchStatus batch_status,
                    IFNULL(vso.ObjID,'-') dest_orgid,
                    IFNULL(vso.ObjType,'-') dest_orgtype,
                    IFNULL(vso.Name,'-') dest_orgname
                FROM
                    view_tc_supplychain_org org 
                    LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID
                WHERE   
                    st.StatusCode = 'active' AND ktsd.StatusCode ='active' AND ktsdd.StatusCode = 'active' AND ktsd.SupplyDestMillOrgID = ? AND st.DateTransaction BETWEEN ? AND ?
                GROUP BY st.SupplyTransID ORDER BY st.DateTransaction DESC";
        $query = $this->db->query($sql, array($wh, "$start 00:00:00", "$end 23:59:59", ));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            $ret = $query->result_array();
            return $ret;
        }
        return false;
    }

    public function getSupplyTransactionPedagang($id, $start, $end, $wh, $cert)
    {
        $wh = str_replace(",", "|", $wh);
        if($cert==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($cert=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $sql = "SELECT
                    st.DateTransaction trans_date,
                    st.FakturNumber batch_number,
                    st.FAQVolumeBruto bruto, 
                    (st.FAQVolumeBruto - (st.FAQVolumePackage*st.FAQNumberPackage)) netto,
                    sb.SupplyDestStatus batch_status,
                    IFNULL(vso2.OrgID,'-') dest_orgid,
                    IFNULL(vso2.OrgType,'-') dest_orgtype,
                    IFNULL(vso2.Name,'-') dest_orgname
                FROM
                    view_supplychain_org vso
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchNumber
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                WHERE vso.OrgType = 'Pedagang' AND vso.OrgID = ? AND (st2.SupplychainID=? OR st3.SupplychainID=?) AND st.DateTransaction BETWEEN ? AND ? $c1 AND st.SupplyType IN $where_cert $c2";
        $query = $this->db->query($sql, array($id, $wh, $wh, $start, $end));
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfileKoperasi($id, $start, $end, $wh, $cert)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
                    org.OrgID id, org.`Name` name,
                    COUNT(DISTINCT 2_transid) transaction_count,
                    COUNT(DISTINCT IF(r.2_status='Delivered',r.2_transid,NULL)) delivered_count,
                    COUNT(DISTINCT r.2_batchid) batch_count,
                    COUNT(DISTINCT r.1_supplychainid) bu_count, SUM(r.2_bruto) bruto, SUM(r.2_netto) netto
                FROM
                    view_supplychain_org org
                    LEFT JOIN rpt_traceability r ON r.2_orgid=org.OrgID AND r.2_orgtype='koperasi' AND 2_date BETWEEN ? AND ? AND r.farmer_iscertified=? AND (2_destorgid IS NULL OR 2_destorgid=? OR wh_orgid IS NULL OR wh_orgid=?)
                WHERE
                    org.OrgType = 'Organisasi Petani' AND org.OrgID=?";
        $query = $this->db->query($sql, array($start, $end, $cert, $wh, $wh, $id));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionKoperasi($id, $start, $end, $wh, $cert)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT
                    IFNULL(ksb.SupplyBatchDate,'-') trans_date,
                    2_transid trans_number,
                    2_batchnumber batch_number,
                    SUM(2_bruto) bruto,
                    SUM(2_netto) netto,
                    2_destorgtype dest_orgtype,
                    2_destorgid dest_orgid,
                    2_destname dest_orgname,
                    2_status batch_status
                FROM
                    view_supplychain_org org
                    LEFT JOIN rpt_traceability r ON r.2_orgid=org.OrgID AND r.2_orgtype='koperasi' AND 2_date BETWEEN ? AND ? AND r.farmer_iscertified=? AND (2_destorgid IS NULL OR 2_destorgid=? OR wh_orgid IS NULL OR wh_orgid=?)
                    LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=2_batchid
                WHERE
                    org.OrgType = 'Organisasi Petani' AND org.OrgID=?
                GROUP BY 2_batchid";
        $query = $this->db->query($sql, array($start, $end, $cert, $wh, $wh, $id));
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfileSCE($id, $start, $end, $wh, $cert)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT org.OrgID id, org.`Name` name, f.FarmerID, f.CPGid, c.GroupName,
                    COUNT(DISTINCT IF(r.1_supplychainid=org.SupplychainID, r.1_transid, r.2_transid)) transaction_count,
                    COUNT(DISTINCT IF(r.1_supplychainid=org.SupplychainID, IF(r.1_status='Delivered',r.1_transid,NULL), IF(r.2_status='Delivered',r.2_transid,NULL))) delivered_count,
                    COUNT(DISTINCT IF(r.1_supplychainid=org.SupplychainID, r.1_batchid, r.2_batchid)) batch_count,
                    COUNT(DISTINCT r.farmer_id) farmer_count, SUM(IFNULL(r.1_bruto,IFNULL(r.2_bruto,0))) bruto, SUM(IFNULL(r.1_netto,IFNULL(r.2_netto,0))) netto
                FROM
                    view_supplychain_org org
                    LEFT JOIN sce_farmer sce ON sce.SceID=org.OrgID
                    LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=sce.FarmerID
                    LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    LEFT JOIN rpt_traceability r ON (r.1_supplychainid=org.SupplychainID OR r.2_supplychainid=org.SupplychainID) AND IF(r.1_supplychainid=org.SupplychainID, r.1_date, r.2_date) BETWEEN ? AND ? AND r.farmer_iscertified=? AND (2_destorgid IS NULL OR 2_destorgid=? OR wh_orgid IS NULL OR wh_orgid=?)
                WHERE
                    org.OrgType = 'sce' AND org.OrgID = ?";
        $query = $this->db->query($sql, array($start, $end, $cert, $wh, $wh, $id));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getSupplyTransactionSCE($id, $start, $end, $wh, $cert)
    {
        $wh = str_replace(",", "|", $wh);
        $sql = "SELECT 
                    IFNULL(ksb.SupplyBatchDate,'-')) trans_date, 
                    IFNULL(1_transid,2_transid) trans_number,
                    IFNULL(1_batchnumber,IFNULL(2_batchnumber,'-')) batch_number,
                    SUM(IFNULL(1_bruto,2_bruto)) bruto,
                    SUM(IFNULL(1_netto,2_netto)) netto,
                    SUM(IFNULL(1_status,2_status)) batch_status,
                    IFNULL(1_destorgtype,2_destorgtype) dest_orgtype,
                    IFNULL(1_destorgid,2_destorgid) dest_orgid,
                    IFNULL(1_destname,IFNULL(2_destname,'-')) dest_orgname
                FROM
                    view_supplychain_org org
                    LEFT JOIN sce_farmer sce ON sce.SceID=org.OrgID
                    LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=sce.FarmerID
                    LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    LEFT JOIN rpt_traceability r ON (r.1_supplychainid=org.SupplychainID OR r.2_supplychainid=org.SupplychainID) AND IF(r.1_supplychainid=org.SupplychainID, r.1_date, r.2_date) BETWEEN ? AND ? AND r.farmer_iscertified=? AND (2_destorgid IS NULL OR 2_destorgid=? OR wh_orgid IS NULL OR wh_orgid=?)
                    LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=IFNULL(1_batchid,2_batchid)
                WHERE
                    org.OrgType = 'sce' AND org.OrgID = ? GROUP BY IFNULL(1_batchid,2_batchid)";
        $query = $this->db->query($sql, array($start, $end, $cert, $wh, $wh, $id));
        if ($query->num_rows()>0) {
            return $query->result_array(0);
        }
        return false;
    }

    public function getSupplyProfileWarehouse($id, $start, $end, $wh, $cert)
    {
        $wh = str_replace(",", "|", $wh);
        if($cert==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($cert=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        
        $sql = " SELECT
                    org.OrgID id, org.`Name` name, 
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    SUM(IFNULL(VolumeBruto1,0) - IFNULL(VolumeBruto2,0)) bruto,
                    SUM(VolumeNetto) netto
                FROM
                    view_supplychain_org org 
                    LEFT JOIN ktv_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
                WHERE org.SupplychainID=?";
        $query = $this->db->query($sql, array("$start 00:00:00", "$end 23:59:59", $wh));
        if ($query->num_rows()>0) {
            $ret = $query->row_array(0);
            if($ret['id']==''){
                $whouse = $this->db->query("SELECT * FROM ktv_warehouse WHERE WarehouseID=?", array($wh))->row_array(0);
                $ret['id'] = $whouse['WarehouseID'];
                $ret['name'] = $whouse['WarehouseName'];
            }
            //echo "<pre>".print_r($ret, 1);exit;
            return $ret;
        }
        return false;
    }

    public function getSupplyProfileWarehouseNew($id, $start, $end, $wh, $cert)
    {
        $sql = " SELECT
                    org.DisplayID id, org.`Name` name, 
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    SUM(IFNULL(VolumeBruto,0)) bruto,
                    SUM(VolumeNetto) netto
                FROM
                    view_tc_supplychain_org org 
                    LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
                WHERE org.SupplychainID=?";
        //$query = $this->db->query($sql, array("$start 00:00:00", "$end 23:59:59", $wh));
        $sql = "SELECT
                    vso.DisplayID id, vso.`Name` name, 
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    SUM(IFNULL(st.VolumeBruto,0)) bruto,
                    SUM(st.VolumeNetto) netto
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyBatchID 
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID 
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID

                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IF(st.SupplyBatchType='Untraceable', IF(st.SupplyBatchSourceType='1', st.MIllID, st.DOID), sb2.SupplyOrgID)
                    LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IF(st2.SupplyBatchType='Untraceable', IF(st2.SupplyBatchSourceType='1', st2.MIllID, st2.DOID), sb3.SupplyOrgID)
                WHERE
                    ktsd.SupplyDestMillOrgID = ?
                    AND ktsd.DeliveryStatusID = '4'
                    AND ktsd.StatusCode = 'active'
                    AND vso3.SupplychainID IS NULL
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ? GROUP BY ktsdd.DeliveryID";
        $query = $this->db->query($sql, array($wh, $start, $end));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        if ($query->num_rows()>0) {
            $ret = $query->row_array(0);
            if($ret['id']==''){
                $whouse = $this->db->query("SELECT * FROM ktv_mill WHERE MillID=?", array($wh))->row_array(0);
                $ret['id'] = $whouse['MillDisplayID'];
                $ret['name'] = $whouse['MillName'];
            }
            //echo "<pre>".print_r($ret, 1);exit;
            return $ret;
        }
        return false;
    }

    public function getSupplyTransactionWarehouseNew($id, $start, $end, $wh, $cert)
    {
        // echo "ASd"
        $sql = "SELECT
                    DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') trans_date,
                    st.TransNumber trans_number,
                    '' po,
                    'Received' batch_status,
                    st.TransNumber batch_number,
                    IFNULL(vso2.`Name`, IF(st.SupplyBatchSourceType='1', IFNULL(st.MillOther,'-'), IFNULL(st.DOOther,'-'))) batch_from,
                    st.VolumeBruto bruto,
                    st.VolumeNetto netto
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyBatchID 
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID 
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID

                    LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IF(st.SupplyBatchType='Untraceable', IF(st.SupplyBatchSourceType='1', st.MIllID, st.DOID), sb2.SupplyOrgID)
                    LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IF(st2.SupplyBatchType='Untraceable', IF(st2.SupplyBatchSourceType='1', st2.MIllID, st2.DOID), sb3.SupplyOrgID)
                WHERE
                    ktsd.SupplyDestMillOrgID = ?
                    AND ktsd.DeliveryStatusID = '4'
                    AND ktsd.StatusCode = 'active'
                    AND vso3.SupplychainID IS NULL
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ? GROUP BY ktsdd.DeliveryID ORDER BY st.DateTransaction DESC
                    ";
        $query = $this->db->query($sql, array($wh, $start, $end));

        if ($query->num_rows()>0) {
            $ret = $query->result_array();
            return $ret;
        }
        return false;
    }

    public function getSupplyTransactionWarehouse($id, $start, $end, $wh, $cert)
    {
        if($cert==""){
            $c1 = "/*"; $c2 = "*/";
            $where_cert = "";
        }else{
            if($cert=="1"){
                $where_cert = "('Farmer')";
            }else{
                $where_cert = "('FarmerNonCert', 'NonFarmer')";
            }
            $c1 = ""; $c2 = "";
        }
        $sql = "SELECT
                    a.DateTransaction trans_date,
                    a.SupplyTransID trans_number,
                    a.DestPO po,
                    a.SupplyDestStatus batch_status,
                    a.FakturNumber batch_number,
                    a.From batch_from,
                    ROUND(SUM(
                            IF(b.bruto IS NULL, a.bruto, (b.bruto / batch.bruto * a.bruto))
                    ),2) bruto,
                    ROUND(SUM(
                        IF(b.netto IS NULL, a.netto, (b.netto / batch.netto * a.netto))
                    ),2) netto
                FROM
                    view_transaction_warehouse_mars a
                    LEFT JOIN view_transaction_warehouse_detail_mars b ON a.SupplyTransID=b.wh_transid
                    LEFT JOIN view_supplychain_org c ON c.OrgID=a.OrgID
                    LEFT JOIN (
                        SELECT SupplyBatchID, SUM(bruto) bruto, SUM(netto) netto FROM view_transaction_warehouse_detail_mars GROUP BY SupplyBatchID
                    ) batch ON batch.SupplyBatchID=b.SupplyBatchID
                WHERE a.OrgID=? AND IFNULL(b.DateTransaction,a.DateTransaction) BETWEEN ? AND ? $c1 AND IFNULL(b.SupplyType, a.SupplyType) IN $where_cert $c2 GROUP BY a.SupplyTransID";
        $query = $this->db->query($sql, array($id, $start, $end));
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

    public function checkLastRevision($farmer_id, $garden_nr, $survey_nr)
    {
        $sql = "
SELECT
    MAX(Revision) AS Revision
FROM
    ktv_cocoa_farmer_garden_area
WHERE
    FarmerID        = ?
    AND GardenNr    = ?
    AND SurveyNr    = ?
        ";
        $query = $this->db->query($sql, array($farmer_id, $garden_nr, $survey_nr));
        if ($query->num_rows()>0) {
            return $query->row_array(0)['Revision'];
        }
        return 0;
    }

    public function getKMLFarmerList($Province = null, $District = null, $CPGid = null, $year = null)
    {
        $where = '';
        $params = array();
        
        //edited: Ardi TypeFarmer = 'Cocoalife' > 'CL'
        $sql = "
SELECT
    f.FarmerID AS id
    , CONCAT('[',f.FarmerID,'] ',f.FarmerName) AS label
FROM ktv_cocoa_farmer_garden_area a
JOIN ktv_cocoa_farmer f ON f.FarmerID = a.FarmerID
LEFT JOIN ktv_cocoa_farmer_type t ON t.FarmerID = f.FarmerID 
LEFT JOIN ktv_cocoa_farmer_garden g ON g.FarmerID = a.FarmerID AND g.GardenNr = a.GardenNr AND g.SurveyNr = a.SurveyNr
LEFT JOIN ktv_province p ON SUBSTR(f.VillageID,1,2) = p.ProvinceID
LEFT JOIN ktv_district d ON SUBSTR(f.VillageID,1,4) = d.DistrictID
WHERE
    1 = 1
    --where--
GROUP BY f.FarmerID
        ";
        if (!empty($Province)) {
            $where .= " AND Province = ?";
            $params[] = $Province;
        }
        if (!empty($District)) {
            $where .= " AND District = ?";
            $params[] = $District;
        }
        if (!empty($CPGid)) {
            $where .= " AND f.CPGid = ?";
            $params[] = $CPGid;
        }
        if (!empty($year)) {
            $where .= " AND YEAR(g.DateCollection) = ?";
            $params[] = $year;
        }
        
        $sql = str_replace('--where--', $where, $sql);
        
        $query = $this->db->query($sql, $params);
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
FROM ktv_cocoa_farmer f
JOIN (
SELECT
    ga.`FarmerID`
FROM ktv_cocoa_farmer_garden_area ga
WHERE ga.`Status` = 'verified'
GROUP BY ga.`FarmerID`
) ga ON ga.FarmerID = f.`FarmerID`
GROUP BY DistrictID
) r ON r.DistrictID = d.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`
WHERE
    p.`Province` = ?
            ";
            $query = $this->db->query($sql, array($Province));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function getKMLCPG($District)
    {
        if (!empty($District)) {
            $sql = "
SELECT
    c.`CPGid` AS id
    , c.`GroupName` AS label
FROM ktv_cpg c
JOIN (
SELECT
    f.`CPGid`
FROM ktv_cocoa_farmer f
JOIN (
SELECT
    ga.`FarmerID`
FROM ktv_cocoa_farmer_garden_area ga
WHERE ga.`Status` = 'verified'
GROUP BY ga.`FarmerID`
) ga ON ga.FarmerID = f.`FarmerID`
GROUP BY f.`CPGid`
) r ON r.CPGid = c.`CPGid`
LEFT JOIN ktv_district d ON d.`DistrictID` = SUBSTR(c.`VillageID`,1,4)
WHERE
    d.`District` = ?
            ";
            $query = $this->db->query($sql, array($District));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
            return false;
        }
    }

    public function listEmptyGardenHaPolygon()
    {
        $sql = "
SELECT
    g.FarmerID
    , g.GardenNr
    , g.SurveyNr
FROM ktv_cocoa_farmer_garden g
JOIN ktv_cocoa_farmer_garden_area ga ON ga.FarmerID = g.FarmerID AND ga.GardenNr = g.GardenNr AND ga.SurveyNr = g.SurveyNr AND ga.Status = 'verified'
WHERE
    g.GardenHaPolygon IS NULL
GROUP BY g.FarmerID
    , g.GardenNr
    , g.SurveyNr
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
}
