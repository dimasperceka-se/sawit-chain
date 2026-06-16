<?php

class Mdboardtraceability extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        
        ////////////////////////////////////////////////////////////////
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
                    LEFT JOIN ktv_cocoa_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
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
                    LEFT JOIN ktv_cocoa_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
                    WHERE VillageID IS NOT NULL AND kcf.`StatusCode` = 'active' AND TrainingStart > 0
                    )m GROUP BY FarmerID, CPGtrainingsID
                    ) kcf
                    %s
                    WHERE
                    kcf.`CPGtrainingsID`=1
                    %s
                    GROUP BY %s";
                
                $this->garden_luas = "SELECT %s label,sum(IFNULL(GardenHaUnCertified,0)) as total, count(distinct a.FarmerID) as jumlah,
                    count(a.FarmerID) as kebun, SUM(a.PohonTM) AS pohon
                    FROM ktv_cocoa_farmer_garden a
                    LEFT JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID,GardenNr) z on
                    a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr AND a.SurveyNr = z.LatestSurveyNr
                    LEFT JOIN ktv_cocoa_farmer_view kcf on a.FarmerID = kcf.FarmerID
                    %s
                    WHERE kcf.StatusCode='active' AND kcf.VillageID and GardenHaUnCertified>0 and kcf.VillageID is not null %s
                    GROUP BY %s";
                
                $this->garden_produksi = "
                    SELECT %s label,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                    (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                    (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as total,
                    sum(PohonTM) tm
                    from ktv_cocoa_farmer_garden a
                    inner JOIN (SELECT FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID,GardenNr) z on
                    a.FarmerID = z.FarmerID and a.GardenNr = z.GardenNr
                    LEFT JOIN ktv_cocoa_farmer_view kcf on a.FarmerID = kcf.FarmerID
                    %s
                    WHERE kcf.StatusCode='active' AND kcf.VillageID and GardenHaUnCertified>0 %s
                    group by %s";
                
                $this->traceability_farmer = "SELECT
                    %s AS label,
            COUNT(kcf.FarmerID) AS farmer,
            SUM(IF(kc.FarmerID,1,0)) AS farmer_certified,
            SUM(IF(kc.FarmerID IS NULL,1,0)) AS farmer_uncertified,
            SUM(IF(rt.farmer_id,1,0)) AS farmer_selling,
            SUM(IF(kc.FarmerID AND rt.farmer_id,1,0)) AS farmer_certified_selling,
            SUM(IF(kc.FarmerID IS NULL AND rt.farmer_id,1,0)) AS farmer_uncertified_selling

        FROM ktv_cocoa_farmer_view kcf
        LEFT JOIN (SELECT kc.FarmerID FROM ktv_cocoa_certification kc GROUP BY kc.FarmerID) kc ON kc.FarmerID = kcf.FarmerID
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
        FROM ktv_cocoa_farmer_view kcf
        LEFT JOIN (
            SELECT
                kcfg.FarmerID
                , kcfg.GardenNr
                , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`) AS Production
            FROM ktv_cocoa_farmer_garden kcfg
            JOIN (SELECT g.FarmerID, g.GardenNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_cocoa_farmer_garden g GROUP BY g.FarmerID, g.GardenNr) g ON g.FarmerID = kcfg.FarmerID AND g.GardenNr = kcfg.GardenNr AND g.SurveyNr = kcfg.SurveyNr
        ) kcfg ON kcfg.FarmerID = kcf.FarmerID
        LEFT JOIN (SELECT kc.FarmerID FROM ktv_cocoa_certification kc GROUP BY kc.FarmerID) kc ON kc.FarmerID = kcf.FarmerID
        LEFT JOIN (SELECT rt.farmer_id, SUM(rt.wh_netto) AS netto FROM rpt_traceability rt GROUP BY rt.farmer_id) rt ON rt.farmer_id = kcf.FarmerID
        %s
        WHERE
            1 = 1 AND kcf.StatusCode = 'active'
            %s
        GROUP BY label
        ORDER BY label
        ";
            
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
            
        $this->traceability_total = "SELECT
            %s AS label,
            SUM(FAQVolumeNetto) AS total_penjualan,
            COUNT(SupplyTransID) AS total_transaction,
            COUNT(DISTINCT MemberID) AS total_farmer_sell,
            SUM(FAQVolumeNetto) AS total_sell,
            PERIOD_DIFF(DATE_FORMAT(max(DateTransaction),'%%Y%%m'),DATE_FORMAT(min(DateTransaction),'%%Y%%m')) + 1 bulan,
            min(date(DateTransaction)) date_min,
            max(date(DateTransaction)) date_max
            %s
        FROM
            ktv_supplychain_transaction a
            LEFT JOIN ktv_members  ON a.SupplyID=ktv_members.MemberDisplayID
            %s
        WHERE
            1=1 AND ktv_members.StatusCode = 'active'
            AND a.SupplyType NOT IN ('Batch', 'NonFarmer')
            %s
        GROUP BY label
        ";
        
        $this->traceability_sales_certified = "SELECT
            %s AS label,
            COUNT(DISTINCT MemberID) AS farmer,
            /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
            IFNULL(SUM(VolumeNetto),0) AS netto,
            /*SUM(VolumeNetto)*/ 0 AS netto_certified,
            COUNT(DISTINCT MemberID) AS farmer_uncertified,
            IFNULL(SUM(VolumeNetto),0) AS netto_uncertified
        FROM
            ktv_supplychain_transaction a
            LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )
            %s
        WHERE
            ktv_members.MemberID IS NOT NULL AND a.SupplyType NOT IN ('Batch', 'NonFarmer') AND ktv_members.StatusCode = 'active'
            %s
            AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
        GROUP BY label
        ORDER BY label
        ";
            
        $this->traceability_bu = "
        SELECT %s label,count(SupplychainID) total
        FROM ktv_tc_supplychain_org_view kcf
        %s
        WHERE (OrgType='%s' OR OrgType='%s') %s
        GROUP BY %s
        ORDER BY label";
            
        $this->traceability_agent = "
            SELECT %s label, COUNT(ktv_members.MemberID) total
            FROM ktv_members 
                LEFT JOIN ktv_member_role mr ON mr.MemberID=ktv_members.MemberID
                LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID=ktv_members.MemberID
                %s
            WHERE mr.MRoleID='5' AND apm.apmPartnerID='10' AND ktv_members.StatusCode='active' %s
            GROUP BY %s
            ORDER BY label
        ";

        $this->traceability_mill = "
            SELECT %s label, COUNT(mi.MillID) total
            FROM ktv_mill mi
                LEFT JOIN ktv_access_partner_mill apmi ON apmi.apmiMillID=mi.MillID
                %s
            WHERE apmi.apmiPartnerID='10' AND mi.StatusCode='active' %s
            GROUP BY %s
            ORDER BY label
        ";    
    }        
    

    function readDataTraceability($prov = '', $kab = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent='')
    {
        $do1 = $do=='' ? '/*' : '';
        $do2 = $do=='' ? '*/' : '';
        $ag1 = $agent=='' ? '/*' : '';
        $ag2 = $agent=='' ? '*/' : '';
        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, op.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_tc_supplychain_org o ON o.OrgID=s.ObjID AND o.OrgType=s.ObjType
                    LEFT JOIN ktv_tc_supplychain_org_partner op ON op.SupplychainID=o.SupplychainID
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array(@$_SESSION['userid']))->result_array();
        $staff = @$query[0];
        $SupplychainID = @$staff['SupplychainID'];
        $OrgType = @$staff['ObjType'];
        if($OrgType=='mill'){
            //$SupplychainID = '';
        }
        if($SupplychainID==''){
            $s1 = "/*"; $s2 = "*/";
        }else{
            $s1 = ""; $s2 = "";
        }
        
        if (@$petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif (@$petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
        if ($prov == '') {
            $label = "IFNULL(Province,'-')";
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(ktv_members.VillageID,1,2)';
            $LEFT_agent = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(c.VillageID,1,2)';
            $LEFT_mill = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            //$where = 'and Province is not null';
            $where = '';
            $groupby = 'substr(ktv_members.VillageID,1,2)';
            $groupby_mill = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = "IFNULL(District,'-')";
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(ktv_members.VillageID,1,4)';
            $LEFT_agent = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(c.VillageID,1,4)';
            $LEFT_mill = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            //$where = 'and substr(ktv_members.VillageID,1,2)=? and District is not null';
            $where = 'and substr(ktv_members.VillageID,1,2)=?';
            $where_mill = 'and substr(VillageID,1,2)=? and District is not null';
            $groupby = 'substr(ktv_members.VillageID,1,4)';
            $groupby_mill = 'substr(VillageID,1,4)';
        } else {
            $label = "IFNULL(kp.SubDistrict,'-')";
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = ktv_members.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_agent = 'LEFT JOIN ktv_village kv ON kv.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_mill = 'LEFT JOIN ktv_village kv ON kv.VillageID = kv.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            //$where = 'and substr(ktv_members.VillageID,1,4)=? and kp.SubDistrict is not null';
            $where = 'and substr(ktv_members.VillageID,1,4)=?';
            $where_mill = 'and substr(kv.VillageID,1,4)=? and kp.SubDistrict is not null';
            $groupby = 'kp.SubDistrictID';
            $groupby_mill = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        if ($awal != '' and $akhir != '') {
            $between = " AND DateTransaction BETWEEN '{$awal} 00:00:00' AND '{$akhir} 23:59:59'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }
        $where_partner = '';
        if (!empty($traceability_partner)) {
            $partner = $this->getPartner($traceability_partner);
            if ($partner['FlagAccess'] == '1') {
                //$where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                //$where_partner = " AND SUBSTR(ktv_members.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }

        /*$query_cpg          = $this->db->query(sprintf($this->sql_kelompok, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_farmer       = $this->db->query(sprintf($this->demographic_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_luas         = $this->db->query(sprintf($this->garden_luas, $label, $LEFT, $where.$where_partner . $qps, $groupby), array($prov));
        $query_produksi     = $this->db->query(sprintf($this->garden_produksi, $label, $LEFT, $where.$where_partner . $qps, $groupby), array($prov));*/

        //$query_traceability_farmer       = $this->db->query(sprintf($this->traceability_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        //$query_traceability_production   = $this->db->query(sprintf($this->traceability_production, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if (!empty($traceability_partner)) {
            //$where_partner = " AND wh.PartnerID = {$traceability_partner}";
        }
        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',VolumeNetto,0)) AS sell_{$value['yearmonth']}
        ,COUNT(DISTINCT IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',SupplyID,NULL)) AS trans_{$value['yearmonth']}
                    ";
                }
        }
        
        $this->traceability_total = "SELECT
                                        %s AS label,
                                        SUM(VolumeNetto) AS total_penjualan,
                                        COUNT(SupplyTransID) AS total_transaction,
                                        SUM(total_farmer) AS total_farmer_sell,
                                        SUM(VolumeNetto) AS total_sell,
                                        PERIOD_DIFF(DATE_FORMAT(max(DateTransaction),'%%Y%%m'),DATE_FORMAT(min(DateTransaction),'%%Y%%m')) + 1 bulan,
                                        min(date(DateTransaction)) date_min,
                                        max(date(DateTransaction)) date_max
                                        %s
                                    FROM
                                        ktv_supplychain_transaction a
                                        LEFT JOIN ktv_members ON (a.SupplyID=ktv_members.MemberID ) AND a.SupplyType='Farmer'
                                        LEFT JOIN (
                                        SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, nf.FarmerID)) total_farmer
                                        FROM
                                            ktv_supplychain_transaction st1
                                            LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st1.SupplyID 
                                            LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                                            LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID 
                                            LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                                            LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                                            LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID))
                                        WHERE st1.SupplychainID = $SupplychainID
                                            GROUP BY st1.SupplyTransID
                                        ) fr ON fr.transid=a.SupplyTransID
                                        %s
                                    WHERE
                                        1=1 $s1 AND a.SupplychainID=$SupplychainID $s2
                                        %s
                                    GROUP BY label";
        $query_total      = $this->db->query(sprintf($this->traceability_total, $label, $SELECT, $LEFT, $where . $between, $groupby), array($prov));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->traceability_sales_certified = "SELECT
                                                    %s AS label,
                                                    SUM(fr.total_farmer) AS farmer,
                                                    /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
                                                    IFNULL(SUM(VolumeNetto),0) AS netto,
                                                    /*SUM(VolumeNetto)*/ 0 AS netto_certified,
                                                    SUM(fr.total_farmer) AS farmer_uncertified,
                                                    IFNULL(SUM(VolumeNetto),0) AS netto_uncertified
                                                FROM
                                                    ktv_supplychain_transaction a
                                                    LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )
                                                    LEFT JOIN (
                                                    SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, nf.FarmerID)) total_farmer
                                                    FROM
                                                        ktv_supplychain_transaction st1
                                                        LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st1.SupplyID 
                                                        LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                                                        LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID 
                                                        LEFT JOIN ktv_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                                                        LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                                                        LEFT JOIN ktv_supplychain_non_farmer nf ON nf.FarmerID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID))
                                                    WHERE st1.SupplychainID = $SupplychainID
                                                        GROUP BY st1.SupplyTransID
                                                    ) fr ON fr.transid=a.SupplyTransID
                                                    %s
                                                WHERE
                                                    1=1 $s1 AND a.SupplychainID=$SupplychainID $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_certified  = $this->db->query(sprintf($this->traceability_sales_certified, $label, $LEFT, $where, $awal, $akhir), array($prov));
        
        $results['cpg']         = array(); //@$query_cpg->result_array();
        $results['farmer']      = array(); //@$query_farmer->result_array();
        $results['luas']        = array(); //@$query_luas->result_array();
        $results['produksi']    = array(); //@$query_produksi->result_array();

        $results['traceability_farmer']         = array(); //@$query_traceability_farmer->result_array();
        $results['traceability_production']     = array(); //@$query_traceability_production->result_array();
        
        $results['total']       = $query_total->result_array();
        $results['certified']   = $query_certified->result_array();
        
        $results['trader']          = array(); //$query_trader->result_array();
        $results['koperasi']        = array(); //$query_koperasi->result_array();
        $results['warehouse']       = array(); //$query_warehouse->result_array();
        
        /**Start**/
        $this->traceability_agent = "SELECT
                                        %s AS label,
                                        COUNT(DISTINCT c.SupplychainID) total
                                    FROM
                                        ktv_supplychain_transaction a
                                        LEFT JOIN ktv_supplychain_batch b ON a.SupplyID=b.SupplyBatchNumber
                                        LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID=b.SupplyOrgID
                                        %s
                                    WHERE
                                        a.SupplyType='Batch' $s1 AND a.SupplychainID=$SupplychainID $s2
                                        %s
                                        AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                    GROUP BY label
                                    ORDER BY label";

        $query_agent = $this->db->query(sprintf($this->traceability_agent, $label, $LEFT_agent, $where, $awal, $akhir, $groupby), array($prov));
        
        $debug['agent'] = $this->db->last_query();
        $results['agent'] = $query_agent->result_array();
        
        $query_mill = $this->db->query(sprintf($this->traceability_mill, $label, $LEFT_mill, $where_mill, $groupby_mill), array($prov));
        $debug['mill'] = $this->db->last_query();
        $results['mill'] = $query_mill->result_array();
        /**End**/
        
        $results['months']          = $query_months->result_array();
        $results['debug']           = $debug;
      
        return $results;
    }

    /* Dashboard Traceability */
    private function getSuplychainID(){
        $SQL="SELECT 
                    GROUP_CONCAT(DISTINCT SupplychainID) SupplychainID
                FROM
                (
                SELECT
                    vso.SupplychainID 
                FROM
                    ktv_access_partner_mill am
                    LEFT JOIN view_tc_supplychain_org vso ON vso.ObjType='mill' AND am.apmiMillID=vso.ObjID
                WHERE
                    am.apmiPartnerID=".$_SESSION['PartnerID']."
                    AND vso.SupplychainID IS NOT NULL
                UNION
                SELECT
                    vso.SupplychainID 
                FROM
                    ktv_access_partner_member am
                    LEFT JOIN view_tc_supplychain_org vso ON vso.ObjType='agent' AND am.apmMemberID=vso.ObjID
                WHERE
                    am.apmPartnerID= ".$_SESSION['PartnerID']."
                    AND vso.SupplychainID IS NOT NULL
                ) dt";
        return $SQL;
    }

    private function query_counter_tc($prov, $kab, $kec, $desa, $awal, $akhir, $traceability_partner, $mill, $do, $agent, $SupplychainID){
        $SQL = "SELECT
                    COUNT(DISTINCT transid_1) total_transaction,
                    COUNT(DISTINCT batchid_1) total_batch,
                    COUNT(DISTINCT IF(type_3='mill', supplychainid_3, IF(type_2='mill', supplychainid_2, IF(type_1='mill', supplychainid_1, NULL)))) total_mill,
                    COUNT(DISTINCT IF(type_1='agent', supplychainid_1, IF(type_2='agent', supplychainid_2, IF(type_3='agent', supplychainid_3, NULL)))) total_agent,
                    COUNT(DISTINCT MemberID) total_farmer,
                    COUNT(DISTINCT IF(supplytype_1='Batch' AND supplybatchtype_1='Untraceable', NULL, CONCAT(supplyid_1, '_', plot_1))) plot_total,
                    SUM(netto_1) traceable_netto_total,
                    SUM(IF(type_3='mill' OR type_2='mill' OR type_1='mill', NULL, netto_1)) traceable_netto_agent,
                    SUM(IF(type_3='mill' OR type_2='mill' OR type_1='mill', netto_1, NULL)) traceable_netto_mill
                FROM 
                (
                    SELECT
                    st.SupplyTransID transid_1,
                    st.TransNumber transnumber_1,
                    st.SupplyType transtype_1,
                    st.DateTransaction date_1,
                    st.SupplyType supplytype_1,
                    st.SupplyBatchType supplybatchtype_1,
                    st.SupplyID supplyid_1,
                    st.PlantationNr plot_1,
                    m.MemberID,
                    st.VolumeBruto bruto_1,
                    st.VolumeNetto netto_1,
                    IFNULL(m.MemberName, IFNULL(vso_1.`Name`, '-')) supplier_1,
                    vso.`Name` name_1,
                    vso.SupplychainID supplychainid_1,
                    vso.ObjType type_1,
                    st.SupplyBatchID batchid_1,
                    sb.DeliveryDate deliverydate_1,
                    
                    st2.SupplyTransID transid_2,
                    st2.TransNumber transnumber_2,
                    st2.DateTransaction date_2,
                    st2.SupplyType supplytype_2,
                    st2.SupplyID supplyid_2,
                    st2.VolumeBruto bruto_2,
                    st2.VolumeNetto netto_2,
                    vso2.`Name` name_2,
                    vso2.SupplychainID supplychainid_2,
                    vso2.ObjType type_2,
                    st2.SupplyBatchID batchid_2,
                    sb2.DeliveryDate deliverydate_2,
                    
                    st3.SupplyTransID transid_3,
                    st3.TransNumber transnumber_3,
                    st3.DateTransaction date_3,
                    st3.SupplyType supplytype_3,
                    st3.SupplyID supplyid_3,
                    st3.VolumeBruto bruto_3,
                    st3.VolumeNetto netto_3,
                    vso3.`Name` name_3, 
                    vso3.SupplychainID supplychainid_3,
                    vso3.ObjType type_3
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL(st.SupplychainID, sb.SupplyOrgID)
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN view_tc_supplychain_org vso_1 ON vso_1.SupplychainID = IF(st.DOID > 0 , st.DOID, IF(st.AgentID > 0, st.AgentID, IF(st.MillID > 0, st.MillID, NULL)))
                    
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.StatusCode='active' AND st2.SupplyType='Batch' AND (st2.SupplyBatchType IS NULL OR st2.SupplyBatchType='Traceable') AND st2.SupplyID > 0 AND st2.SupplyID!=st2.SupplychainID
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = sb.SupplyDestOrgID
                    
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.StatusCode='active' AND st3.SupplyType='Batch' AND (st3.SupplyBatchType IS NULL OR st3.SupplyBatchType='Traceable') AND st3.SupplyID > 0 AND st3.SupplyID!=st3.SupplychainID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = sb2.SupplyDestOrgID
                    
                    LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
                    
                WHERE 1=1
                    AND st.StatusCode='active'
                    AND (st.SupplyType IN ('Farmer', 'Nonfarmer') OR (st.SupplyType='Batch' AND st.SupplyBatchType='Untraceable'))
                    AND sb.SupplyBatchStatus IN ( 'Delivered', 'Sent' ) 
                    AND st.SupplyID > 0
                    AND st.SupplychainID NOT IN (586, 852, 587)
                    AND DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') BETWEEN '".$awal."' AND '".$akhir."'
                    AND (
                        (vso.SupplychainID IN (".$SupplychainID."))
                        OR (vso2.SupplychainID IN (".$SupplychainID."))
                        OR (vso3.SupplychainID IN (".$SupplychainID.")) 
                    )";

            if($prov){
                $SQL .= " AND p.ProvinceID = ".$prov;
            }

            if((int)$mill > 0){
                $SQL .= " AND IF(vso3.ObjType='mill', vso3.SupplychainID, IF(vso2.ObjType='mill', vso2.SupplychainID, IF(vso.ObjType='mill', vso.SupplychainID, NULL)))= ".$mill;
            }

            if((int)$do > 0){
                $SQL .= " AND (vso.SupplychainID=".$do." OR vso2.SupplychainID=".$do.")";
            }

            if((int)$agent > 0){
                $SQL .= " AND (vso.SupplychainID=".$agent." OR vso2.SupplychainID=".$agent.")";
            }

            $SQL .=" GROUP BY st.SupplyTransID) dt";

            return $SQL;
    }
    private function getAnnualPotential($prov, $awal, $akhir, $mill, $do = '', $agent = ''){

        $sqlwhere = "";
        $sqlwhere2 = "";

        if($_SESSION['is_admin'] == "1"){
            $sqlwhere = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlwhere = " AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlwhere = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlwhere = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlwhere = "AND apm.apmiPartnerID = '$_SESSION[PartnerID]'";
        }

        if($mill != '' OR $mill <> '' OR $mill != null){
            if($mill == "other"){
                $sqlwhere = " AND (vso.SupplychainID IS NULL OR vso.SupplychainID = 0)";
            }
            $sqlwhere = " AND vso.SupplychainID = '$mill'";
        }

        $SQL = "SELECT
                    SUM(sp.AnnualProduction) AnnualProduction
                FROM
                    view_tc_supplychain_org vso
                LEFT JOIN ktv_mill mill on mill.MillID = vso.ObjID
                LEFT JOIN ktv_access_partner_mill apm on apm.apmiMillID = vso.ObjID                
                LEFT JOIN ktv_village vil ON mill.VillageID = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
                LEFT JOIN
                    ktv_tc_supplychain_org_rel orel on orel.ParentID = vso.SupplychainID
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = orel.ChildID
                INNER JOIN
                    ktv_members m on m.MemberID = vso2.ObjID AND m.StatusCode = 'active'
                LEFT JOIN
                    ktv_member_role mr on mr.MemberID = m.MemberID
                INNER JOIN
                    ktv_tc_supplychain_farmer sf on sf.SupplychainID = orel.ChildID AND sf.StatusCode = 'active'
                INNER JOIN
                    ktv_members m2 on m2.MemberID = sf.FarmerID AND m2.StatusCode = 'active'
                INNER JOIN
                    (
                        SELECT
                                p.MemberID, p.PlotNr, MAX(p.SurveyNr) SurveyNr  
                        FROM
                                ktv_survey_plot p
                        WHERE
                                p.StatusCode='active' AND p.MemberID > 0
                        GROUP BY p.MemberID, p.PlotNr
                    ) dt on dt.MemberID = m2.MemberID
                INNER JOIN
                    ktv_survey_plot sp on sp.MemberID = dt.MemberID AND sp.PlotNr = dt.PlotNr AND sp.SurveyNr = dt.SurveyNr
                WHERE
                    1=1
                    $sqlwhere
                    $sqlwhere2
                AND
                    vso.ObjType = 'mill'
                AND
                    mr.MRoleID IN (5,6,7,8,9,10,13)";

        $SQL .= "UNION SELECT
                    SUM(sp.AnnualProduction) AnnualProduction
                FROM
                    view_tc_supplychain_org vso
                LEFT JOIN ktv_access_partner_mill apm on apm.apmiMillID = vso.ObjID    
                LEFT JOIN
                    ktv_tc_supplychain_org_rel orel on orel.ParentID = vso.SupplychainID
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = orel.ChildID
                INNER JOIN
                    ktv_members m on m.MemberID = vso2.ObjID AND m.StatusCode = 'active'                
                LEFT JOIN ktv_village vil ON m.VillageID = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
                LEFT JOIN
                    ktv_member_role mr on mr.MemberID = m.MemberID
                INNER JOIN
                    (
                            SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) SurveyNr  
                            FROM
                                    ktv_survey_plot_sme p
                            WHERE
                                    p.StatusCode='active' AND p.MemberID > 0
                            GROUP BY p.MemberID, p.PlotNr
                    ) dt on dt.MemberID = m.MemberID
                INNER JOIN
                    ktv_survey_plot_sme sp on sp.MemberID = dt.MemberID AND sp.PlotNr = dt.PlotNr AND sp.SurveyNr = dt.SurveyNr
                WHERE
                    1=1
                    $sqlwhere
                    $sqlwhere2
                AND
                    vso.ObjType = 'mill'
                AND
                    mr.MRoleID IN (11,12,14)";
        $SQL .= " UNION SELECT
                    SUM(sp.AnnualProduction) AnnualProduction
                FROM
                    view_tc_supplychain_org vso
                LEFT JOIN ktv_mill mill on mill.MillID = vso.ObjID  
                LEFT JOIN ktv_access_partner_mill apm on apm.apmiMillID = vso.ObjID                  
                LEFT JOIN ktv_village vil ON mill.VillageID = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
                INNER JOIN
                    ktv_tc_supplychain_farmer sf on sf.SupplychainID = vso.SupplychainID AND sf.StatusCode = 'active'
                INNER JOIN
                    ktv_members m2 on m2.MemberID = sf.FarmerID AND m2.StatusCode = 'active'
                INNER JOIN
                    (
                            SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) SurveyNr  
                            FROM
                                    ktv_survey_plot p
                            WHERE
                                    p.StatusCode='active' AND p.MemberID > 0
                            GROUP BY p.MemberID, p.PlotNr
                    ) dt on dt.MemberID = m2.MemberID
                INNER JOIN
                    ktv_survey_plot sp on sp.MemberID = dt.MemberID AND sp.PlotNr = dt.PlotNr AND sp.SurveyNr = dt.SurveyNr
                WHERE
                    1=1
                $sqlwhere
                AND
                    vso.ObjType = 'mill'";

        return $SQL;
    }

    public function readDataTraceabilityMill($awal = '', $akhir = '', $traceability_partner = '', $mill='')
    {

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sql = "SELECT
                    GROUP_CONCAT(vso.SupplychainID) SupplychainID
                FROM
                    ktv_access_partner_mill apm
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.ObjID = apm.apmiMillID AND vso.ObjType = 'mill'
                WHERE
                    apm.apmiPartnerID = ?";
            $query = $this->db->query($sql,array($_SESSION['PartnerID']));
            if($query->num_rows()>0){
                $row = $query->row();
                $sqlHakAksesPartner   = " AND sb.StatusCode = 'active' AND sb.SupplyDestMillOrgID IN ($row->SupplychainID) ";
                $sqlHakAksesPartnerMill  = " AND st.SupplychainID IN ($row->SupplychainID)";
            }
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
                $sqlHakAksesPartnerMill = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND sd.SupplyDestMillOrgID = '$_SESSION[SupplychainID]' ";
            $sqlHakAksesPartnerMill = " AND st.SupplychainID = '$_SESSION[SupplychainID]'";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
            $sqlHakAksesPartnerMill = "";
        }

        if($mill != '' OR $mill <> '' OR $mill != null){
            $sqlHakAksesPartner = " AND sb.SupplyDestMillOrgID = '$mill' ";
            $sqlHakAksesPartnerMill = " AND st.SupplychainID = '$mill'";
        }
        // echo "<pre>";
        // print_r($sqlHakAksesPartner);
        // die;

        $yearStart = $awal;
        $yearEnd = $akhir;

        if($yearStart < $yearEnd){
            $konten = array();
            for ($a=$yearStart;$a<=$yearEnd;$a++){
                $konten[] = (string)$a;
            }
            $header = array();
            $plasma = array();
            $direct = array();
            $agent  = array();
            $owner  = array();
            $external  = array();
            $plasma_transaksi = array();
            $direct_transaksi = array();
            $agent_transaksi  = array();
            $owner_transaksi  = array();
            $external_transaksi  = array();
            $result = array();
            $result_transaksi = array();
            // print_r($konten);
            // die;
            for($k = 0;$k<count($konten);$k++){
          
            }
    
            array_push($result,array("name"=>"Plasma","data"=>$plasma));
            array_push($result,array("name"=>"Direct Smallholder","data"=>$direct));
            array_push($result,array("name"=>"Agent/DO/Vendor","data"=>$agent));
            array_push($result,array("name"=>"Owner Estate","data"=>$owner));
            array_push($result,array("name"=>"External Estate","data"=>$external));
    
            array_push($result_transaksi,array("name"=>"Plasma","data"=>$plasma_transaksi));
            array_push($result_transaksi,array("name"=>"Direct Smallholder","data"=>$direct_transaksi));
            array_push($result_transaksi,array("name"=>"Agent/DO/Vendor","data"=>$agent_transaksi));
            array_push($result_transaksi,array("name"=>"Owner Estate","data"=>$owner_transaksi));
            array_push($result_transaksi,array("name"=>"External Estate","data"=>$external_transaksi));
            
            $grafik = array('hasil_volume' => $result, 'hasil_transaksi'=>$result_transaksi, 'header' => $header);
        }

        $PID = $_SESSION['PartnerID'];

        $sql = "SELECT COUNT(*) AS SumSupplyTransaction
                FROM
                (
                SELECT
                    COUNT(DISTINCT(m.MemberID)) AS SumSupplyTransaction
                FROM
                    ktv_access_partner_member s_ma
                LEFT JOIN 
                    ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                LEFT JOIN 
                    ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                LEFT JOIN 
                    view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
                LEFT JOIN 
                    ktv_members m ON m.MemberID = s_ma.apmMemberID
                LEFT JOIN 
                    ktv_tc_supplychain_farmer ktsm ON ktsm.FarmerID = m.MemberID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction ktst ON ktst.SupplyID = m.MemberID
                WHERE
                    1 = 1 
                AND 
                    s_mi.StatusCode = 'active' 
                AND 
                    s_mi.PartnerID = '$PID'
                AND 
                    ktst.SupplyID IS NOT NULL
                GROUP BY 
                    ktst.SupplyID
                ) AS SumSupplyTransaction";
        $query = $this->db->query($sql);
        
        $transaction = 0;

        if($query->num_rows()>0){
            foreach($query->result() as $row){
                $transaction        += $row->SumSupplyTransaction;
            }
        }

        $sql = "SELECT 
                    (
                        SELECT
                            COUNT(DISTINCT(m.MemberID)) AS total
                        FROM
                            ktv_access_partner_member s_ma
                        LEFT JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                        LEFT JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                        LEFT JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
                        LEFT JOIN ktv_members m ON m.MemberID = s_ma.apmMemberID
                        LEFT JOIN ktv_tc_supplychain_farmer ktsm ON ktsm.FarmerID = m.MemberID
                        LEFT JOIN ktv_survey_plot sp on sp.MemberID = m.MemberID
                        WHERE
                        1 = 1 
                        AND s_mi.StatusCode = 'active' 
                        AND s_mi.PartnerID = '$PID'
                        AND ktsm.FarmerID IS NOT NULL
                    ) AS totalfarmer,
                    (
                        SELECT
                            COUNT(DISTINCT(a.MemberDisplayID)) AS total
                        FROM
                            ktv_members a
                        LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                        LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                        LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                        LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                        LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                        LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                        LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                        LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                        LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                        LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                        WHERE
                            op.PartnerID = '$PID'
                            AND o.ObjType = 'agent' 
                            AND a.StatusCode = 'active'
                            AND ktsd.SupplychainID IS NOT NULL
                            AND ktsd.DeliveryStatusID = '4'
                    ) AS totalAgent,
                    (
                        SELECT
                            SUM(ktst.VolumeBruto) AS VolumeBrutoFarmer
                        FROM
                            ktv_access_partner_member s_ma
                            LEFT JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                            LEFT JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                            LEFT JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
                            LEFT JOIN ktv_members m ON m.MemberID = s_ma.apmMemberID
                            LEFT JOIN ktv_tc_supplychain_farmer ktsm ON ktsm.FarmerID = m.MemberID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst ON ktst.SupplyID = m.MemberID
                            WHERE
                            1 = 1 
                            AND s_mi.StatusCode = 'active' 
                            AND s_mi.PartnerID = '$PID'
                            AND ktst.SupplyID IS NOT NULL
                    ) AS VolumeBrutoFarmer,
                    (
                        SELECT 
                            SUM(ktstd.TotalCapacity) AS totalReceivedMill
                        FROM 
                            ktv_tc_supplychain_delivery sd
                        LEFT JOIN 
                            ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID= sd.DeliveryID
                        LEFT JOIN 
                            ktv_tc_supplychain_transaction_detail ktstd ON ktstd.DeliveryDetailID = ktsdd.DeliveryID
                        LEFT JOIN 
                            view_tc_supplychain_org vso ON vso.SupplychainID = sd.SupplyDestMillOrgID
                        WHERE 
                            sd.StatusCode =  'active'
                        AND 
                            vso.PartnerID = '$PID'
                        AND 
                            sd.SupplyDestProcessType =  'mill'
                        AND 
                            sd.DeliveryStatusID IN (3, 4, 5)
                    ) AS totalReceivedMill
                FROM 
                    `ktv_tc_supplychain_delivery` sd
                LEFT JOIN 
                    `ktv_tc_supplychain_transaction` stn ON `stn`.`SupplyID` = `sd`.`DeliveryID` 
                LEFT JOIN 
                    `ktv_tc_supplychain_delivery_detail` ktsdd ON `ktsdd`.`DeliveryID` = `sd`.`DeliveryID` 
                LEFT JOIN 
                    `ktv_tc_supplychain_transaction_detail` ktstd ON `ktstd`.`DeliveryDetailID` = `ktsdd`.`DeliveryID` 
                LEFT JOIN 
                    `ktv_tc_supplychain_batch` ktsb ON `ktsb`.`SupplyBatchID` = `ktsdd`.`SupplyBatchID`
                WHERE 
                    YEAR(sd.DeliveryDate) BETWEEN '$yearStart' AND '$yearEnd'
                AND
                    `sd`.`StatusCode` = 'active' 
                $sqlHakAksesPartner
                AND 
                    `sd`.`SupplyDestProcessType` = 'mill' 
                ";
        $query = $this->db->query($sql);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        
        $supplybase         = 0;
        // $transaction        = 0;
        $supplyplot         = 0;
        $supplyvolume       = 0;
        $totalAgent         = 0;
        $VolumeBrutoFarmer  = 0;
        $totalfarmer        = 0;
        $totaltransaction   = 0;
        $totalvolume        = 0;
        $totalReceivedMill   = 0;
        $supplybasedetail   = array();
        $transactiondetail  = array();
        $plotdetail         = array();
        $volumedetail       = array();
        if($query->num_rows()>0){
            foreach($query->result() as $row){
                $totalfarmer        += $row->totalfarmer;
                // $transaction        += $row->SumSupplyTransaction;
                $supplyplot         += $row->SumSupplyPlot;
                $supplyvolume       += $row->SumSupplyVolume;
                $totalAgent         += $row->totalAgent;
                $totaltransaction   += $row->totaltransaction;
                $totalvolume        += $row->totalvolume;
                $VolumeBrutoFarmer  += number_format(($row->VolumeBrutoFarmer/1000),2);
                $totalReceivedMill  += $row->totalReceivedMill;
                $supplybasedata = array(
                    "CategoryName"  => $row->CategoryName,
                    "value"         => $row->SumSupplyBase
                );
                $transactiondata = array(
                    "CategoryName"  => $row->CategoryName,
                    "value"         => $row->SumSupplyTransaction
                );
                $plotdata = array(
                    "CategoryName"  => $row->CategoryName,
                    "value"         => $row->SumSupplyPlot
                );
                $volumedata = array(
                    "CategoryName"  => $row->CategoryName,
                    "value"         => number_format(($row->SumSupplyVolume/1000),2)
                );

                array_push($supplybasedetail,$supplybasedata);
                array_push($transactiondetail,$transactiondata);
                array_push($plotdetail,$plotdata);
                array_push($volumedetail,$volumedata);
            }
        }else{
            $category = array(lang("Plasma"),lang("Direct Smallholder"),lang("Agent/Dealer/Vendor"),lang("Owned Estate"),lang("External Estate"));
            for($i=0;$i<count($category);$i++){
                $supplybasedata = array(
                    "CategoryName"  => $category[$i],
                    "value"         => 0
                );
                $transactiondata = array(
                    "CategoryName"  => $category[$i],
                    "value"         => 0
                );
                $plotdata = array(
                    "CategoryName"  => $category[$i],
                    "value"         => 0
                );
                $volumedata = array(
                    "CategoryName"  => $category[$i],
                    "value"         => 0
                );
    
                array_push($supplybasedetail,$supplybasedata);
                array_push($transactiondetail,$transactiondata);
                array_push($plotdetail,$plotdata);
                array_push($volumedetail,$volumedata);
            }
        }

        $SID = $_SESSION['SupplychainID'];

        //cpo transaction refinery
        $sqlcpotrans = "SELECT count(total) AS total
                                FROM
                                (
                                SELECT
                                    tp.GenerateDelivery as total
                                FROM
                                    `ktv_tc_despatch_detail` a
                                    LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                    LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                    LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                    LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                                WHERE
                                tp.SupplychainID = '$SID'
                                AND
                                tp.GenerateFrom = 'CPO'
                                AND
                                dp.DestpatchStatusID = '5'
                                GROUP BY tp.ProcessingNumber
                                ) a";
        $datasqlcpotrans = $this->db->query($sqlcpotrans);

        if($datasqlcpotrans->num_rows()){
            $dt = $datasqlcpotrans->row();
            $totalcpotrans  = $dt->total;
        }

        //pk transaction refinery
        $sqlpktrans = "SELECT count(total) AS total
                                FROM
                                (
                                SELECT
                                    tp.GenerateDelivery as total
                                FROM
                                    `ktv_tc_despatch_detail` a
                                    LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                    LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                    LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                    LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                                WHERE
                                tp.SupplychainID = '$SID'
                                AND
                                tp.GenerateFrom = 'PK'
                                AND
                                dp.DestpatchStatusID = '5'
                                GROUP BY tp.ProcessingNumber
                                ) a";
        $datasqlpktrans = $this->db->query($sqlpktrans);

        if($datasqlpktrans->num_rows()){
            $dt = $datasqlcpotrans->row();
            $totalpktrans  = $dt->total;
        }
        
        $sqlpk = "SELECT
                    SUM(dd.DespatchVolume) AS DispatchVolumePk
                FROM
                    ktv_tc_despatch a
                LEFT JOIN 
                    ktv_tc_despatch_vehicle b ON b.DespatchID = a.DespatchID
                LEFT JOIN 
                    ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                LEFT JOIN 
                    view_tc_supplychain_org vso ON vso.SupplychainID = a.DestinationID
                LEFT JOIN 
                    view_tc_supplychain_org vso2 ON vso2.SupplychainID = a.SupplychainID
                LEFT JOIN 
                    ktv_tc_despatch_detail dd ON dd.DespatchID = a.DespatchID
                WHERE
                    a.SupplychainID = '$SID'
                AND 
                    a.StatusCode = 'active'
                AND 
                    a.ProductID = '2'
                GROUP BY
                    a.DespatchID";
        $datasqlpk = $this->db->query($sqlpk);

        if($datasqlpk->num_rows()){
            $dt= $datasqlpk->row();
            $totalpk        = ($dt->DispatchVolumePk / 1000);
        }

        $sqlcpo ="SELECT
                        SUM(dd.DespatchVolume) AS DispatchVolumeCpo
                    FROM
                        ktv_tc_despatch a
                    LEFT JOIN 
                        ktv_tc_despatch_vehicle b ON b.DespatchID = a.DespatchID
                    LEFT JOIN 
                        ref_transaction_status ts ON ts.TransactionStatusID = a.DestpatchStatusID
                    LEFT JOIN 
                        view_tc_supplychain_org vso ON vso.SupplychainID = a.DestinationID
                    LEFT JOIN 
                        view_tc_supplychain_org vso2 ON vso2.SupplychainID = a.SupplychainID
                    LEFT JOIN 
                        ktv_tc_despatch_detail dd ON dd.DespatchID = a.DespatchID
                    WHERE
                        a.SupplychainID = '$SID'
                    AND 
                        a.StatusCode = 'active'
                    AND 
                        a.ProductID = '1'
                    GROUP BY
                        a.DespatchID";
        $datasqlcpo = $this->db->query($sqlcpo);

        if($datasqlcpo->num_rows()){
            $dt= $datasqlcpo->row();
            $totalcpo        = ($dt->DispatchVolumeCpo / 1000);
        }
        
        //traceable pk 
        $sqlpktraceable = "SELECT sum(total) AS total
                                FROM
                                (
                                    SELECT
                                        a.DespatchVolume as total
                                    FROM
                                        `ktv_tc_despatch_detail` a
                                        LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                        LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                        LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                        LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                                    WHERE
                                    tp.SupplychainID = '$SID'
                                    AND
                                    tp.GenerateFrom = 'PK'
                                    AND
                                    dp.DestpatchStatusID = '4'
                                    GROUP BY tp.ProcessingNumber
                                ) a";
        $datasqlpktraceable = $this->db->query($sqlpktraceable);

        if($datasqlpktraceable->num_rows()){
            $dt= $datasqlpktraceable->row();
            $totalpktraceable        = ROUND((float)$dt->total * 1000);
        }

        //traceable cp
        $sqlcpreceived = "SELECT sum(total) AS total
                                FROM
                                (
                                    SELECT
                                        a.DespatchVolume as total
                                    FROM
                                        `ktv_tc_despatch_detail` a
                                        LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                        LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                        LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                        LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                                    WHERE
                                        tp.SupplychainID = '$SID'
                                    AND
                                        tp.GenerateFrom = 'CPO'
                                    AND
                                        dp.DestpatchStatusID = '4'
                                    GROUP BY tp.ProcessingNumber
                                ) a";
        $datasqlcpreceived = $this->db->query($sqlcpreceived);

        if($datasqlcpreceived->num_rows()){
            $dt= $datasqlcpreceived->row();
            $totalcptraceable       = ROUND((float)$dt->total * 1000);
        }

        //stock pk 
        $sqlpkstock = "SELECT 
                            SUM(b.RemainingVolume) as total
                        FROM 
                            ktv_tc_despatch_detail a 
                            LEFT JOIN ktc_tc_processing_product b on b.ProcessingProductID = a.ProcessingProductID 
                            LEFT JOIN ktv_tc_processing c on c.ProcessingID = b.ProcessingID 
                            LEFT JOIN ref_tc_processing_product pp on pp.ProductID = a.ProductID 
                            LEFT JOIN ktv_tc_despatch ktd on ktd.DespatchID = a.DespatchID
                        WHERE 
                            ktd.SupplychainID = '$SID'
                        AND 
                            a.ProductID = '2'
                        AND 
                            a.StatusCode = 'active'
                        AND 
                            ktd.DestpatchStatusID = '1'";
        $datasqlpkstock = $this->db->query($sqlpkstock);

        if($datasqlpkstock->num_rows()){
            $dt= $datasqlpkstock->row();
            $totalpkstock       = ($dt->total / 1000);
        }

        //stock cpo
        $sqlcpostock = "SELECT 
                            SUM(b.RemainingVolume) as total
                        FROM 
                            ktv_tc_despatch_detail a 
                            LEFT JOIN ktc_tc_processing_product b on b.ProcessingProductID = a.ProcessingProductID 
                            LEFT JOIN ktv_tc_processing c on c.ProcessingID = b.ProcessingID 
                            LEFT JOIN ref_tc_processing_product pp on pp.ProductID = a.ProductID 
                            LEFT JOIN ktv_tc_despatch ktd on ktd.DespatchID = a.DespatchID
                        WHERE 
                            ktd.SupplychainID = '$SID'
                        AND 
                            a.ProductID = '1'
                        AND 
                            a.StatusCode = 'active'
                        AND 
                            ktd.DestpatchStatusID = '1'";
        $datasqlcpostock = $this->db->query($sqlcpostock);

        if($datasqlcpostock->num_rows()){
            $dt= $datasqlcpostock->row();
            $totalcpostock        = ($dt->total / 1000);
        }

        //traceable pk received to refinery
        $sqlpkreceived = "SELECT sum(total) AS total
                            FROM
                            (
                            SELECT
                                a.DespatchVolume as total
                            FROM
                                `ktv_tc_despatch_detail` a
                                LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                            WHERE
                            tp.SupplychainID = '$SID'
                            AND
                            dp.DestpatchStatusID = '5'
                            AND
                            dp.ProductID = '2'
                            GROUP BY tp.ProcessingNumber
                            ) a";
        $datasqlpkreceived = $this->db->query($sqlpkreceived);

        if($datasqlpkreceived->num_rows()){
            $dt= $datasqlpkreceived->row();
            $totalpkreceivedrefinery        = ($dt->total / 1000);
        }

        //traceable cp received to refinery
        $sqlcpreceived = "SELECT sum(total) AS total
                                FROM
                                (
                                SELECT
                                    a.DespatchVolume as total
                                FROM
                                    `ktv_tc_despatch_detail` a
                                    LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                    LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                    LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                    LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                                WHERE
                                tp.SupplychainID = '$SID'
                                AND
                                dp.DestpatchStatusID = '5'
                                AND
                                dp.ProductID = '1'
                                GROUP BY tp.ProcessingNumber
                                ) a";
        $datasqlcpreceived = $this->db->query($sqlcpreceived);

        if($datasqlcpreceived->num_rows()){
            $dt= $datasqlcpreceived->row();
            
            $totalcpreceivedrefinery        = ($dt->total / 1000);
        }

        //traceable sales
        $sqltraceablesales = "SELECT 
                                SUM(ktst.VolumeBruto) AS totalFFB
                            FROM
                                ktv_members a
                            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID 
                            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID 
                            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID 
                            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID 
                            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID 
                            LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID 
                            LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID 
                            LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID 
                            AND msc.MillID = op.ObjID 
                            LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID 
                            LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID 
                            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID 
                            LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID 
                            LEFT JOIN ktv_tc_supplychain_batch ktsb on ktsb.SupplyBatchID = ktsdd.SupplyBatchID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplyBatchID = ktsb.SupplyBatchID
                            WHERE
                                o.ObjType = 'agent' 
                                AND op.PartnerID ='$PID'
                                AND a.StatusCode = 'active'
                                AND ktsd.SupplychainID IS NOT NULL
                            GROUP BY op.PartnerID";
        $datasqltraceablesales = $this->db->query($sqltraceablesales);
        
        if($datasqltraceablesales->num_rows()){
            
            $dt= $datasqltraceablesales->row();

            $totalsqltraceablesales         = ROUND((float)$dt->totalFFB);
            $name                           = $dt->SupplierName;
        }

        /*traceable sales */
        $data['data']['traceable_sales'] = array('data' => array(
            array(
                "name" => "agent",
                "y" => $totalsqltraceablesales,
                "sliced" => true,
                "selected" => true
            )
        ),

        'judul' => lang('Traceable sales'),
        'yjudul' => lang('Volume'),
        'label' => array('Volume'));

        $sqlsourceffb = "SELECT 
                                SUM(ktst.VolumeBruto) AS totalFFB
                            FROM
                                ktv_members a
                            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID 
                            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID 
                            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID 
                            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID 
                            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID 
                            LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID 
                            LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID 
                            LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID 
                            AND msc.MillID = op.ObjID 
                            LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID 
                            LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID 
                            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID 
                            LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID 
                            LEFT JOIN ktv_tc_supplychain_batch ktsb on ktsb.SupplyBatchID = ktsdd.SupplyBatchID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplyBatchID = ktsb.SupplyBatchID
                            WHERE
                                o.ObjType = 'agent' 
                                AND op.PartnerID ='$PID'
                                AND a.StatusCode = 'active'
                                AND ktsd.SupplychainID IS NOT NULL
                            GROUP BY op.PartnerID";
        $datasqlsourceffb = $this->db->query($sqlsourceffb);
        
        if($datasqlsourceffb->num_rows()){
            
            $dt= $datasqlsourceffb->row();

            $totalsqlsourceffb              = ROUND((float)$dt->totalFFB /1000);
            $name                           = $dt->SupplierName;
        }
        
        /* source ffb */
        $data['data']['source_ffb'] =   array('data' => array(
                                            array(
                                                "name" => 'agent',
                                                "y" => $totalsqlsourceffb,
                                                "sliced" => true,
                                                "selected" => true
                                            )
                                        ),
                                        'judul' => lang('Source of FFB'),
                                        'yjudul' => lang('Volume'),
                                        'label' => array('Volume'));
        
         //ffb traceability traceable
         $sqlFfbTraceable = "SELECT 
                                SUM(a.VolumeBruto) AS TotalFFBTraceable
                            FROM
                                ktv_tc_supplychain_transaction a
                            LEFT JOIN
                                ktv_members b ON a.SupplyID = b.MemberID 
                            LEFT JOIN
                                ktv_tc_supplychain_batch d ON a.SupplyBatchID =d.SupplyBatchID
                            LEFT JOIN 
                                ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = d.SupplyBatchID
                            LEFT JOIN 
                                ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                            WHERE 
                                a.statusCode = 'active'
                            AND
                                ktsd.SupplyDestMillOrgID = '$SID'
                            AND
                                ktsd.DeliveryStatusID IN ('3','4','5')
                            AND
                                a.isTraceable = 'YES'";

        $datasqlffbtraceable = $this->db->query($sqlFfbTraceable);

        if($datasqlffbtraceable->num_rows()){
            
            $dt= $datasqlffbtraceable->row();

            $TotalFFBTraceablePie  = ROUND((float)$dt->TotalFFBTraceable /1000);
        }

        //ffb traceability non traceable
        $sqlFfbNonTraceable = "SELECT 
                                SUM(a.VolumeBruto) AS TotalFFBTraceable
                            FROM
                                ktv_tc_supplychain_transaction a
                            LEFT JOIN
                                ktv_members b ON a.SupplyID = b.MemberID 
                            LEFT JOIN
                                ktv_tc_supplychain_batch d ON a.SupplyBatchID =d.SupplyBatchID
                            LEFT JOIN 
                                ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = d.SupplyBatchID
                            LEFT JOIN 
                                ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                            WHERE 
                                a.statusCode = 'active'
                            AND
                                ktsd.SupplyDestMillOrgID = '$SID'
                            AND
                                ktsd.DeliveryStatusID IN ('3','4','5')
                            AND
                                a.isTraceable = 'NO'";

        $datasqlffbNontraceable = $this->db->query($sqlFfbNonTraceable);

        if($datasqlffbNontraceable->num_rows()){

            $dt= $datasqlffbNontraceable->row();

            $TotalFFBNonTraceablePie  = ROUND((float)$dt->TotalFFBTraceable /1000);
        }

         /* ffb traceability */
         $data['data']['ffb_traceability_traceable'] = array('data' => array(
                                                                        array(
                                                                            "name" => 'Traceable',
                                                                            "y" => $TotalFFBTraceablePie,
                                                                            "sliced" => true,
                                                                            "selected" => true
                                                                        ),
                                                                        array(
                                                                            "name" => 'Non Traceable',
                                                                            "y" => $TotalFFBNonTraceablePie,
                                                                            "sliced" => true,
                                                                            "selected" => true
                                                                        ),
                                                            ),'judul' => lang('Source of FFB'),'yjudul' => lang('Volume'),'label' => array('Volume'));

            
        $where = "";
        
        $users = $this->db->query("SELECT * FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->result_array();

        $PartnerID = @$users[0]['PartnerID'];
        
        if (!empty($PartnerID)) {
            $where = "AND op.PartnerID = ".$PartnerID;
        }

        //grafik suplier agent
        $SqlSupplier = "SELECT
                            IF(MONTH(ktst.`DateTransaction`) = 1, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJanuary,
                            IF(MONTH(ktst.`DateTransaction`) = 2, COUNT(ktst.`SupplyTransID`),0) AS TotalTransFebruary,
                            IF(MONTH(ktst.`DateTransaction`) = 3, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMarch,
                            IF(MONTH(ktst.`DateTransaction`) = 4, COUNT(ktst.`SupplyTransID`),0) AS TotalTransApril,
                            IF(MONTH(ktst.`DateTransaction`) = 5, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMay,
                            IF(MONTH(ktst.`DateTransaction`) = 6, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJune,
                            IF(MONTH(ktst.`DateTransaction`) = 7, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJuly,
                            IF(MONTH(ktst.`DateTransaction`) = 8, COUNT(ktst.`SupplyTransID`),0) AS TotalTransAugust,
                            IF(MONTH(ktst.`DateTransaction`) = 9, COUNT(ktst.`SupplyTransID`),0) AS TotalTransSeptember,
                            IF(MONTH(ktst.`DateTransaction`) = 10, COUNT(ktst.`SupplyTransID`),0) AS TotalTransOctober,
                            IF(MONTH(ktst.`DateTransaction`) = 11, COUNT(ktst.`SupplyTransID`),0) AS TotalTransNovember,
                            IF(MONTH(ktst.`DateTransaction`) = 12, COUNT(ktst.`SupplyTransID`),0) AS TotalTransDecember
                        FROM
                        ktv_members a
                            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                            LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                            LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                            LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                            LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                            LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                            LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                            LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                        WHERE
                            1 = 1 
                        $where
                        AND ktst.SupplybaseCategoryID = 3
                        AND ktsd.StatusCode = 'active'
                        AND ktsd.SupplychainID IS NOT NULL
                        AND ktsd.DeliveryStatusID = '4'
                        GROUP BY
                        MONTH(ktst.`DateTransaction`)";

        $QuerySqlSupplierAgent = $this->db->query($SqlSupplier);

        $dataSqlSupplierAgent = $QuerySqlSupplierAgent->row();
        //end

        //grafik direct small holder
        $SqlSmallHolder = "SELECT
                IF(MONTH(ktst.`DateTransaction`) = 1, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJanuary,
                IF(MONTH(ktst.`DateTransaction`) = 2, COUNT(ktst.`SupplyTransID`),0) AS TotalTransFebruary,
                IF(MONTH(ktst.`DateTransaction`) = 3, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMarch,
                IF(MONTH(ktst.`DateTransaction`) = 4, COUNT(ktst.`SupplyTransID`),0) AS TotalTransApril,
                IF(MONTH(ktst.`DateTransaction`) = 5, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMay,
                IF(MONTH(ktst.`DateTransaction`) = 6, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJune,
                IF(MONTH(ktst.`DateTransaction`) = 7, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJuly,
                IF(MONTH(ktst.`DateTransaction`) = 8, COUNT(ktst.`SupplyTransID`),0) AS TotalTransAugust,
                IF(MONTH(ktst.`DateTransaction`) = 9, COUNT(ktst.`SupplyTransID`),0) AS TotalTransSeptember,
                IF(MONTH(ktst.`DateTransaction`) = 10, COUNT(ktst.`SupplyTransID`),0) AS TotalTransOctober,
                IF(MONTH(ktst.`DateTransaction`) = 11, COUNT(ktst.`SupplyTransID`),0) AS TotalTransNovember,
                IF(MONTH(ktst.`DateTransaction`) = 12, COUNT(ktst.`SupplyTransID`),0) AS TotalTransDecember
            FROM
            ktv_members a
                LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
            WHERE
                1 = 1 
            $where
            AND ktst.SupplybaseCategoryID = 2
            AND ktsd.StatusCode = 'active'
            AND ktsd.SupplychainID IS NOT NULL
            AND ktsd.DeliveryStatusID = '4'
            GROUP BY
            MONTH(ktst.`DateTransaction`)";

        $QuerySqlSmallHolder = $this->db->query($SqlSmallHolder);

        $dataSqlSmallHolder = $QuerySqlSmallHolder->row();
        //end

        //grafik eksternal estate
        $SqlEksternalEstate = "SELECT
                IF(MONTH(ktst.`DateTransaction`) = 1, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJanuary,
                IF(MONTH(ktst.`DateTransaction`) = 2, COUNT(ktst.`SupplyTransID`),0) AS TotalTransFebruary,
                IF(MONTH(ktst.`DateTransaction`) = 3, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMarch,
                IF(MONTH(ktst.`DateTransaction`) = 4, COUNT(ktst.`SupplyTransID`),0) AS TotalTransApril,
                IF(MONTH(ktst.`DateTransaction`) = 5, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMay,
                IF(MONTH(ktst.`DateTransaction`) = 6, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJune,
                IF(MONTH(ktst.`DateTransaction`) = 7, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJuly,
                IF(MONTH(ktst.`DateTransaction`) = 8, COUNT(ktst.`SupplyTransID`),0) AS TotalTransAugust,
                IF(MONTH(ktst.`DateTransaction`) = 9, COUNT(ktst.`SupplyTransID`),0) AS TotalTransSeptember,
                IF(MONTH(ktst.`DateTransaction`) = 10, COUNT(ktst.`SupplyTransID`),0) AS TotalTransOctober,
                IF(MONTH(ktst.`DateTransaction`) = 11, COUNT(ktst.`SupplyTransID`),0) AS TotalTransNovember,
                IF(MONTH(ktst.`DateTransaction`) = 12, COUNT(ktst.`SupplyTransID`),0) AS TotalTransDecember
            FROM
            ktv_members a
                LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
            WHERE
                1 = 1 
            $where
            AND ktst.SupplybaseCategoryID = 5
            AND ktsd.StatusCode = 'active'
            AND ktsd.SupplychainID IS NOT NULL
            AND ktsd.DeliveryStatusID = '4'
            GROUP BY
            MONTH(ktst.`DateTransaction`)";

        $QuerySqlEksternalEstate = $this->db->query($SqlEksternalEstate);

        $dataSqlEksternalEstate = $QuerySqlEksternalEstate->row();
        //end

        //grafik own plantation
        $SqlOwnPlantation = "SELECT
                IF(MONTH(ktst.`DateTransaction`) = 1, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJanuary,
                IF(MONTH(ktst.`DateTransaction`) = 2, COUNT(ktst.`SupplyTransID`),0) AS TotalTransFebruary,
                IF(MONTH(ktst.`DateTransaction`) = 3, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMarch,
                IF(MONTH(ktst.`DateTransaction`) = 4, COUNT(ktst.`SupplyTransID`),0) AS TotalTransApril,
                IF(MONTH(ktst.`DateTransaction`) = 5, COUNT(ktst.`SupplyTransID`),0) AS TotalTransMay,
                IF(MONTH(ktst.`DateTransaction`) = 6, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJune,
                IF(MONTH(ktst.`DateTransaction`) = 7, COUNT(ktst.`SupplyTransID`),0) AS TotalTransJuly,
                IF(MONTH(ktst.`DateTransaction`) = 8, COUNT(ktst.`SupplyTransID`),0) AS TotalTransAugust,
                IF(MONTH(ktst.`DateTransaction`) = 9, COUNT(ktst.`SupplyTransID`),0) AS TotalTransSeptember,
                IF(MONTH(ktst.`DateTransaction`) = 10, COUNT(ktst.`SupplyTransID`),0) AS TotalTransOctober,
                IF(MONTH(ktst.`DateTransaction`) = 11, COUNT(ktst.`SupplyTransID`),0) AS TotalTransNovember,
                IF(MONTH(ktst.`DateTransaction`) = 12, COUNT(ktst.`SupplyTransID`),0) AS TotalTransDecember
            FROM
            ktv_members a
                LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
            WHERE
                1 = 1 
            $where
            AND ktst.SupplybaseCategoryID = 4
            AND ktsd.StatusCode = 'active'
            AND ktsd.SupplychainID IS NOT NULL
            AND ktsd.DeliveryStatusID = '4'
            GROUP BY
            MONTH(ktst.`DateTransaction`)";

        $QuerySqlOwnPlantation = $this->db->query($SqlOwnPlantation);

        $dataSqlOwnPlantation = $QuerySqlOwnPlantation->row();
        //end

        //grafik sum suplier agent
        $SqlSumSupplier = "SELECT
                            IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJanuary,
                            IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeFebruary,
                            IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMarch,
                            IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeApril,
                            IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMay,
                            IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJune,
                            IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJuly,
                            IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeAugust,
                            IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeSeptember,
                            IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeOctober,
                            IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeNovember,
                            IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeDecember
                        FROM
                            ktv_members a
                        LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                        LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                        LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                        LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                        LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                        LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                        LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                        LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                        LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                        LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                        LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                        LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                        WHERE
                            1 = 1 
                        $where
                        AND ktst.SupplybaseCategoryID = 3
                        AND ktsd.StatusCode = 'active'
                        AND ktsd.SupplychainID IS NOT NULL
                        AND ktsd.DeliveryStatusID = '4'
                        GROUP BY
                        MONTH(ktst.`DateTransaction`)";

        $QuerySqlSumSupplierAgent = $this->db->query($SqlSumSupplier);

        $dataSqlSumSupplierAgent = $QuerySqlSumSupplierAgent->row();
        //end

        //grafik sum direct small holder
        $SqlSumSmallHolder = "SELECT
                                IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJanuary,
                                IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeFebruary,
                                IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMarch,
                                IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeApril,
                                IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMay,
                                IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJune,
                                IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJuly,
                                IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeAugust,
                                IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeSeptember,
                                IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeOctober,
                                IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeNovember,
                                IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeDecember
                            FROM
                                ktv_members a
                            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                            LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                            LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                            LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                            LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                            LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                            LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                            LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                            WHERE
                            1 = 1 
                            $where
                            AND ktst.SupplybaseCategoryID = 2
                            AND ktsd.StatusCode = 'active'
                            AND ktsd.SupplychainID IS NOT NULL
                            AND ktsd.DeliveryStatusID = '4'
                            GROUP BY
                            MONTH(ktst.`DateTransaction`)";

        $QuerySqlSumSmallHolder = $this->db->query($SqlSumSmallHolder);

        $dataSqlSumSmallHolder = $QuerySqlSumSmallHolder->row();
        //end

        //grafik sum eksternal estate
        $SumEksternalEstate = "SELECT
                                IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJanuary,
                                IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeFebruary,
                                IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMarch,
                                IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeApril,
                                IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMay,
                                IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJune,
                                IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJuly,
                                IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeAugust,
                                IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeSeptember,
                                IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeOctober,
                                IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeNovember,
                                IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeDecember
                            FROM
                                ktv_members a
                            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                            LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                            LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                            LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                            LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                            LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                            LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                            LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                            WHERE
                            1 = 1 
                            $where
                            AND ktst.SupplybaseCategoryID = 5
                            AND ktsd.StatusCode = 'active'
                            AND ktsd.SupplychainID IS NOT NULL
                            AND ktsd.DeliveryStatusID = '4'
                            GROUP BY
                            MONTH(ktst.`DateTransaction`)";

        $QuerySumEksternalEstate = $this->db->query($SumEksternalEstate);

        $dataSumEksternalEstate = $QuerySumEksternalEstate->row();
        //end

        //grafik sum direct own plantation
        $SqlSumOwnPlantation = "SELECT
                                IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJanuary,
                                IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeFebruary,
                                IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMarch,
                                IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeApril,
                                IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeMay,
                                IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJune,
                                IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeJuly,
                                IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeAugust,
                                IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeSeptember,
                                IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeOctober,
                                IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeNovember,
                                IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto`),0) AS TotalVolumeDecember
                            FROM
                                ktv_members a
                            LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                            LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                            LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                            LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                            LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                            LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                            LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                            LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                            LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                            LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                            LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                            LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                            WHERE
                            1 = 1 
                            $where
                            AND ktst.SupplybaseCategoryID = 4
                            AND ktsd.StatusCode = 'active'
                            AND ktsd.SupplychainID IS NOT NULL
                            AND ktsd.DeliveryStatusID = '4'
                            GROUP BY
                            MONTH(ktst.`DateTransaction`)";

        $QuerySqlSumOwnPlantation = $this->db->query($SqlSumOwnPlantation);

        $dataSqlSumOwnPlantation = $QuerySqlSumOwnPlantation->row();
        //end

        //grafik cpo production
        $sqlCpoProcessing = "SELECT 
                        IF(MONTH(a.`ProcessingDate`) = 1, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingJanuary,
                        IF(MONTH(a.`ProcessingDate`) = 2, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingFebruary,
                        IF(MONTH(a.`ProcessingDate`) = 3, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingMarch,
                        IF(MONTH(a.`ProcessingDate`) = 4, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingApril,
                        IF(MONTH(a.`ProcessingDate`) = 5, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingMay,
                        IF(MONTH(a.`ProcessingDate`) = 6, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingJune,
                        IF(MONTH(a.`ProcessingDate`) = 7, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingJuly,
                        IF(MONTH(a.`ProcessingDate`) = 8, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingAugust,
                        IF(MONTH(a.`ProcessingDate`) = 9, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingSeptember,
                        IF(MONTH(a.`ProcessingDate`) = 10, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingOctober,
                        IF(MONTH(a.`ProcessingDate`) = 11, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingNovember,
                        IF(MONTH(a.`ProcessingDate`) = 12, SUM(b.`ProcessingVolume`),0) AS TotalCpoProcessingDecember
                        FROM 
                            ktv_tc_processing a 
                        LEFT JOIN 
                            ktv_tc_processing_detail b ON b.ProcessingID = a.ProcessingID
                        WHERE
                            a.SupplychainID = '$SID' 
                        AND 
                            a.GenerateFrom = 'CPO'
                        GROUP BY
                            MONTH(a.`ProcessingDate`)";
        $QuerysqlCpoProcessing = $this->db->query($sqlCpoProcessing);

        $dataCpoProcessing = $QuerysqlCpoProcessing->row();
        
        //grafik pk production
        $sqlPkProcessing = "SELECT 
                        IF(MONTH(a.`ProcessingDate`) = 1, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingJanuary,
                        IF(MONTH(a.`ProcessingDate`) = 2, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingFebruary,
                        IF(MONTH(a.`ProcessingDate`) = 3, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingMarch,
                        IF(MONTH(a.`ProcessingDate`) = 4, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingApril,
                        IF(MONTH(a.`ProcessingDate`) = 5, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingMay,
                        IF(MONTH(a.`ProcessingDate`) = 6, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingJune,
                        IF(MONTH(a.`ProcessingDate`) = 7, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingJuly,
                        IF(MONTH(a.`ProcessingDate`) = 8, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingAugust,
                        IF(MONTH(a.`ProcessingDate`) = 9, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingSeptember,
                        IF(MONTH(a.`ProcessingDate`) = 10, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingOctober,
                        IF(MONTH(a.`ProcessingDate`) = 11, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingNovember,
                        IF(MONTH(a.`ProcessingDate`) = 12, SUM(b.`ProcessingVolume`),0) AS TotalPkProcessingDecember
                        FROM 
                            ktv_tc_processing a 
                        LEFT JOIN 
                            ktv_tc_processing_detail b ON b.ProcessingID = a.ProcessingID
                        WHERE
                            a.SupplychainID = '$SID' 
                        AND 
                            a.GenerateFrom = 'PK'
                        GROUP BY
                            MONTH(a.`ProcessingDate`)";
        $QuerysqlPkProcessing = $this->db->query($sqlPkProcessing);

        $dataPkProcessing = $QuerysqlPkProcessing->row();
        //end
        
        //grafik cpo despatch
        $sqlCpoDespatch = "SELECT 
                            IF(MONTH(dp.`ShippingDate`) = 1, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchJanuary,
                            IF(MONTH(dp.`ShippingDate`) = 2, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchFebruary,
                            IF(MONTH(dp.`ShippingDate`) = 3, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchMarch,
                            IF(MONTH(dp.`ShippingDate`) = 4, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchApril,
                            IF(MONTH(dp.`ShippingDate`) = 5, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchMay,
                            IF(MONTH(dp.`ShippingDate`) = 6, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchJune,
                            IF(MONTH(dp.`ShippingDate`) = 7, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchJuly,
                            IF(MONTH(dp.`ShippingDate`) = 8, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchAugust,
                            IF(MONTH(dp.`ShippingDate`) = 9, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchSeptember,
                            IF(MONTH(dp.`ShippingDate`) = 10, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchOctober,
                            IF(MONTH(dp.`ShippingDate`) = 11, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchNovember,
                            IF(MONTH(dp.`ShippingDate`) = 12, SUM(ktpp.`ProcessingVolume`),0) AS TotalCpoDespatchDecember
                        FROM
                            `ktv_tc_despatch_detail` a
                            LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                            LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                            LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                            LEFT JOIN ktv_tc_processing_detail ktpp ON ktpp.ProcessingID = tsp.ProcessingID
                            LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                        WHERE
                            tp.SupplychainID = '$SID'
                        AND
                            tp.GenerateFrom = 'CPO'
                        AND
                            dp.DestpatchStatusID = '4'
                        GROUP BY
                            MONTH(dp.`ShippingDate`)";
        $QuerysqlCpoDespatch = $this->db->query($sqlCpoDespatch);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        
        $dataCpoDespatch = $QuerysqlCpoDespatch->row();

        //grafik cpo despatch
        $sqlPKDespatch = "SELECT 
                            IF(MONTH(dp.`ShippingDate`) = 1, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchJanuary,
                            IF(MONTH(dp.`ShippingDate`) = 2, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchFebruary,
                            IF(MONTH(dp.`ShippingDate`) = 3, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchMarch,
                            IF(MONTH(dp.`ShippingDate`) = 4, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchApril,
                            IF(MONTH(dp.`ShippingDate`) = 5, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchMay,
                            IF(MONTH(dp.`ShippingDate`) = 6, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchJune,
                            IF(MONTH(dp.`ShippingDate`) = 7, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchJuly,
                            IF(MONTH(dp.`ShippingDate`) = 8, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchAugust,
                            IF(MONTH(dp.`ShippingDate`) = 9, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchSeptember,
                            IF(MONTH(dp.`ShippingDate`) = 10, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchOctober,
                            IF(MONTH(dp.`ShippingDate`) = 11, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchNovember,
                            IF(MONTH(dp.`ShippingDate`) = 12, SUM(ktpp.`ProcessingVolume`),0) AS TotalPKDespatchDecember
                        FROM
                            `ktv_tc_despatch_detail` a
                            LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                            LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                            LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                            LEFT JOIN ktv_tc_processing_detail ktpp ON ktpp.ProcessingID = tsp.ProcessingID
                            LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID
                        WHERE
                            tp.SupplychainID = '$SID'
                        AND
                            tp.GenerateFrom = 'PK'
                        AND
                            dp.DestpatchStatusID = '4'
                        GROUP BY
                            MONTH(dp.`ShippingDate`)";

        $QuerysqlPKDespatch = $this->db->query($sqlPKDespatch);
        
        $dataPKDespatch = $QuerysqlPKDespatch->row();
                                        
        $data["data"]["totalfarmer"]            = $totalfarmer;
        $data["data"]["transaction"]            = $transaction;
        $data["data"]["supplyplot"]             = $supplyplot;
        $data["data"]["supplyvolume"]           = number_format(($supplyvolume/1000),2);
        $data["data"]["totalAgent"]             = $totalAgent;
        $data["data"]["totaltransaction"]       = $totaltransaction;
        $data["data"]["totalvolume"]            = $totalvolume;
        $data["data"]["VolumeBrutoFarmer"]      = number_format(($VolumeBrutoFarmer/1000),2);
        $data["data"]["totalReceivedMill"]      = number_format(($totalReceivedMill/1000),2);
        $data["data"]["totalcpostock"]          = $totalcpostock;
        $data["data"]["totalpkstock"]           = $totalpkstock;
        $data["data"]["totalcpotrans"]          = $totalcpotrans;
        $data["data"]["totalpktrans"]           = $totalpktrans;
        $data["data"]["totalpk"]                = $totalpk;
        $data["data"]["totalcpo"]               = $totalcpo;
        $data["data"]["totalpktraceable"]       = $totalpktraceable;
        $data["data"]["totalcptraceable"]       = $totalcptraceable;
        $data["data"]["totalpkreceivedrefinery"] = $totalpkreceivedrefinery;
        $data["data"]["totalcpreceivedrefinery"] = $totalcpreceivedrefinery;
        $data["detail"]["Supplybase"]         = $supplybasedetail;
        $data["detail"]["Transaction"]        = $transactiondetail;
        $data["detail"]["Plot"]               = $plotdetail;
        $data["detail"]["Volume"]             = $volumedetail;
        $data["grafik"]                       = $grafik;
        
        //line grafik supplier agent
        $data['line']['agent']['TotalTransJanuary'] =  (float) $dataSqlSupplierAgent->TotalTransJanuary;
        $data['line']['agent']['TotalTransFebruary'] = (float) $dataSqlSupplierAgent->TotalTransFebruary;
        $data['line']['agent']['TotalTransMarch'] = (float) $dataSqlSupplierAgent->TotalTransMarch;
        $data['line']['agent']['TotalTransApril'] =  (float) $dataSqlSupplierAgent->TotalTransApril;
        $data['line']['agent']['TotalTransMay'] = (float) $dataSqlSupplierAgent->TotalTransMay;
        $data['line']['agent']['TotalTransJune'] = (float) $dataSqlSupplierAgent->TotalTransJune;
        $data['line']['agent']['TotalTransJuly'] = (float) $dataSqlSupplierAgent->TotalTransJuly;
        $data['line']['agent']['TotalTransAugust'] = (float) $dataSqlSupplierAgent->TotalTransAugust;
        $data['line']['agent']['TotalTransSeptember'] = (float) $dataSqlSupplierAgent->TotalTransSeptember;
        $data['line']['agent']['TotalTransOctober'] = (float) $dataSqlSupplierAgent->TotalTransOctober;
        $data['line']['agent']['TotalTransNovember'] =  (float)$dataSqlSupplierAgent->TotalTransNovember;
        $data['line']['agent']['TotalTransDecember'] =  (float) $dataSqlSupplierAgent->TotalTransDecember;
        //end

        //line grafik small holder
        $data['line']['smallholder']['TotalTransJanuary'] =  (float) $dataSqlSmallHolder->TotalTransJanuary;
        $data['line']['smallholder']['TotalTransFebruary'] = (float) $dataSqlSmallHolder->TotalTransFebruary;
        $data['line']['smallholder']['TotalTransMarch'] = (float) $dataSqlSmallHolder->TotalTransMarch;
        $data['line']['smallholder']['TotalTransApril'] =  (float) $dataSqlSmallHolder->TotalTransApril;
        $data['line']['smallholder']['TotalTransMay'] = (float) $dataSqlSmallHolder->TotalTransMay;
        $data['line']['smallholder']['TotalTransJune'] = (float) $dataSqlSmallHolder->TotalTransJune;
        $data['line']['smallholder']['TotalTransJuly'] = (float) $dataSqlSmallHolder->TotalTransJuly;
        $data['line']['smallholder']['TotalTransAugust'] = (float) $dataSqlSmallHolder->TotalTransAugust;
        $data['line']['smallholder']['TotalTransSeptember'] = (float) $dataSqlSmallHolder->TotalTransSeptember;
        $data['line']['smallholder']['TotalTransOctober'] = (float) $dataSqlSmallHolder->TotalTransOctober;
        $data['line']['smallholder']['TotalTransNovember'] =  (float)$dataSqlSmallHolder->TotalTransNovember;
        $data['line']['smallholder']['TotalTransDecember'] =  (float) $dataSqlSmallHolder->TotalTransDecember;
        //end

        //line grafik eksternal estate
        $data['line']['eksternalestate']['TotalTransJanuary'] =  (float) $dataSqlEksternalEstate->TotalTransJanuary;
        $data['line']['eksternalestate']['TotalTransFebruary'] = (float) $dataSqlEksternalEstate->TotalTransFebruary;
        $data['line']['eksternalestate']['TotalTransMarch'] = (float) $dataSqlEksternalEstate->TotalTransMarch;
        $data['line']['eksternalestate']['TotalTransApril'] =  (float) $dataSqlEksternalEstate->TotalTransApril;
        $data['line']['eksternalestate']['TotalTransMay'] = (float) $dataSqlEksternalEstate->TotalTransMay;
        $data['line']['eksternalestate']['TotalTransJune'] = (float) $dataSqlEksternalEstate->TotalTransJune;
        $data['line']['eksternalestate']['TotalTransJuly'] = (float) $dataSqlEksternalEstate->TotalTransJuly;
        $data['line']['eksternalestate']['TotalTransAugust'] = (float) $dataSqlEksternalEstate->TotalTransAugust;
        $data['line']['eksternalestate']['TotalTransSeptember'] = (float) $dataSqlEksternalEstate->TotalTransSeptember;
        $data['line']['eksternalestate']['TotalTransOctober'] = (float) $dataSqlEksternalEstate->TotalTransOctober;
        $data['line']['eksternalestate']['TotalTransNovember'] =  (float)$dataSqlEksternalEstate->TotalTransNovember;
        $data['line']['eksternalestate']['TotalTransDecember'] =  (float) $dataSqlEksternalEstate->TotalTransDecember;
        //end

        //line grafik own plantation
        $data['line']['ownplantation']['TotalTransJanuary'] =  (float) $dataOwnPlantation->TotalTransJanuary;
        $data['line']['ownplantation']['TotalTransFebruary'] = (float) $dataOwnPlantation->TotalTransFebruary;
        $data['line']['ownplantation']['TotalTransMarch'] = (float) $dataOwnPlantation->TotalTransMarch;
        $data['line']['ownplantation']['TotalTransApril'] =  (float) $dataOwnPlantation->TotalTransApril;
        $data['line']['ownplantation']['TotalTransMay'] = (float) $dataOwnPlantation->TotalTransMay;
        $data['line']['ownplantation']['TotalTransJune'] = (float) $dataOwnPlantation->TotalTransJune;
        $data['line']['ownplantation']['TotalTransJuly'] = (float) $dataOwnPlantation->TotalTransJuly;
        $data['line']['ownplantation']['TotalTransAugust'] = (float) $dataOwnPlantation->TotalTransAugust;
        $data['line']['ownplantation']['TotalTransSeptember'] = (float) $dataOwnPlantation->TotalTransSeptember;
        $data['line']['ownplantation']['TotalTransOctober'] = (float) $dataOwnPlantation->TotalTransOctober;
        $data['line']['ownplantation']['TotalTransNovember'] =  (float)$dataOwnPlantation->TotalTransNovember;
        $data['line']['ownplantation']['TotalTransDecember'] =  (float) $dataOwnPlantation->TotalTransDecember;
        //end

        //line grafik sum supplier agent
        $data['line']['sumagent']['TotalVolumeJanuary'] =  (float) $dataSqlSumSupplierAgent->TotalVolumeJanuary;
        $data['line']['sumagent']['TotalVolumeFebruary'] = (float) $dataSqlSumSupplierAgent->TotalVolumeFebruary;
        $data['line']['sumagent']['TotalVolumeMarch'] = (float) $dataSqlSumSupplierAgent->TotalVolumeMarch;
        $data['line']['sumagent']['TotalVolumeApril'] =  (float) $dataSqlSumSupplierAgent->TotalVolumeApril;
        $data['line']['sumagent']['TotalVolumeMay'] = (float) $dataSqlSumSupplierAgent->TotalVolumeMay;
        $data['line']['sumagent']['TotalVolumeJune'] = (float) $dataSqlSumSupplierAgent->TotalVolumeJune;
        $data['line']['sumagent']['TotalVolumeJuly'] = (float) $dataSqlSumSupplierAgent->TotalVolumeJuly;
        $data['line']['sumagent']['TotalVolumeAugust'] = (float) $dataSqlSumSupplierAgent->TotalVolumeAugust;
        $data['line']['sumagent']['TotalVolumeSeptember'] = (float) $dataSqlSumSupplierAgent->TotalVolumeSeptember;
        $data['line']['sumagent']['TotalVolumeOctober'] = (float) $dataSqlSumSupplierAgent->TotalVolumeOctober;
        $data['line']['sumagent']['TotalVolumeNovember'] =  (float)$dataSqlSumSupplierAgent->TotalVolumeNovember;
        $data['line']['sumagent']['TotalVolumeDecember'] =  (float) $dataSqlSumSupplierAgent->TotalVolumeDecember;
        //end

        //line grafik sum small holder
        $data['line']['sumsmallholder']['TotalVolumeJanuary'] =  (float) $dataSqlSmallHolder->TotalVolumeJanuary;
        $data['line']['sumsmallholder']['TotalVolumeFebruary'] = (float) $dataSqlSmallHolder->TotalVolumeFebruary;
        $data['line']['sumsmallholder']['TotalVolumeMarch'] = (float) $dataSqlSmallHolder->TotalVolumeMarch;
        $data['line']['sumsmallholder']['TotalVolumeApril'] =  (float) $dataSqlSmallHolder->TotalVolumeApril;
        $data['line']['sumsmallholder']['TotalVolumeMay'] = (float) $dataSqlSmallHolder->TotalVolumeMay;
        $data['line']['sumsmallholder']['TotalVolumeJune'] = (float) $dataSqlSmallHolder->TotalVolumeJune;
        $data['line']['sumsmallholder']['TotalVolumeJuly'] = (float) $dataSqlSmallHolder->TotalVolumeJuly;
        $data['line']['sumsmallholder']['TotalVolumeAugust'] = (float) $dataSqlSmallHolder->TotalVolumeAugust;
        $data['line']['sumsmallholder']['TotalVolumeSeptember'] = (float) $dataSqlSmallHolder->TotalVolumeSeptember;
        $data['line']['sumsmallholder']['TotalVolumeOctober'] = (float) $dataSqlSmallHolder->TotalVolumeOctober;
        $data['line']['sumsmallholder']['TotalVolumeNovember'] =  (float)$dataSqlSmallHolder->TotalVolumeNovember;
        $data['line']['sumsmallholder']['TotalVolumeDecember'] =  (float) $dataSqlSmallHolder->TotalVolumeDecember;
        //end

        //line grafik sum eksternal estate
        $data['line']['sumeksternalestate']['TotalVolumeJanuary'] =  (float) $dataSqlEksternalEstate->TotalVolumeJanuary;
        $data['line']['sumeksternalestate']['TotalVolumeFebruary'] = (float) $dataSqlEksternalEstate->TotalVolumeFebruary;
        $data['line']['sumeksternalestate']['TotalVolumeMarch'] = (float) $dataSqlEksternalEstate->TotalVolumeMarch;
        $data['line']['sumeksternalestate']['TotalVolumeApril'] =  (float) $dataSqlEksternalEstate->TotalVolumeApril;
        $data['line']['sumeksternalestate']['TotalVolumeMay'] = (float) $dataSqlEksternalEstate->TotalVolumeMay;
        $data['line']['sumeksternalestate']['TotalVolumeJune'] = (float) $dataSqlEksternalEstate->TotalVolumeJune;
        $data['line']['sumeksternalestate']['TotalVolumeJuly'] = (float) $dataSqlEksternalEstate->TotalVolumeJuly;
        $data['line']['sumeksternalestate']['TotalVolumeAugust'] = (float) $dataSqlEksternalEstate->TotalVolumeAugust;
        $data['line']['sumeksternalestate']['TotalVolumeSeptember'] = (float) $dataSqlEksternalEstate->TotalVolumeSeptember;
        $data['line']['sumeksternalestate']['TotalVolumeOctober'] = (float) $dataSqlEksternalEstate->TotalVolumeOctober;
        $data['line']['sumeksternalestate']['TotalVolumeNovember'] =  (float)$dataSqlEksternalEstate->TotalVolumeNovember;
        $data['line']['sumeksternalestate']['TotalVolumeDecember'] =  (float) $dataSqlEksternalEstate->TotalVolumeDecember;
        //end

        //line grafik sum own plantation
        $data['line']['sumownplantation']['TotalVolumeJanuary'] =  (float) $dataOwnPlantation->TotalVolumeJanuary;
        $data['line']['sumownplantation']['TotalVolumeFebruary'] = (float) $dataOwnPlantation->TotalVolumeFebruary;
        $data['line']['sumownplantation']['TotalVolumeMarch'] = (float) $dataOwnPlantation->TotalVolumeMarch;
        $data['line']['sumownplantation']['TotalVolumeApril'] =  (float) $dataOwnPlantation->TotalVolumeApril;
        $data['line']['sumownplantation']['TotalVolumeMay'] = (float) $dataOwnPlantation->TotalVolumeMay;
        $data['line']['sumownplantation']['TotalVolumeJune'] = (float) $dataOwnPlantation->TotalVolumeJune;
        $data['line']['sumownplantation']['TotalVolumeJuly'] = (float) $dataOwnPlantation->TotalVolumeJuly;
        $data['line']['sumownplantation']['TotalVolumeAugust'] = (float) $dataOwnPlantation->TotalVolumeAugust;
        $data['line']['sumownplantation']['TotalVolumeSeptember'] = (float) $dataOwnPlantation->TotalVolumeSeptember;
        $data['line']['sumownplantation']['TotalVolumeOctober'] = (float) $dataOwnPlantation->TotalVolumeOctober;
        $data['line']['sumownplantation']['TotalVolumeNovember'] =  (float)$dataOwnPlantation->TotalVolumeNovember;
        $data['line']['sumownplantation']['TotalVolumeDecember'] =  (float) $dataOwnPlantation->TotalVolumeDecember;
        //end
        
        //line grafik cpo pk processing
        $data['line']['TotalCpoProcessingJanuary'] = (float) $dataCpoProcessing->TotalCpoProcessingJanuary;
        $data['line']['TotalCpoProcessingFebruary'] = (float) $dataCpoProcessing->TotalCpoProcessingFebruary;
        $data['line']['TotalCpoProcessingMarch'] = (float) $dataCpoProcessing->TotalCpoProcessingMarch;
        $data['line']['TotalCpoProcessingApril'] = (float) $dataCpoProcessing->TotalCpoProcessingApril;
        $data['line']['TotalCpoProcessingMay'] = (float) $dataCpoProcessing->TotalCpoProcessingMay;
        $data['line']['TotalCpoProcessingJune'] = (float) $dataCpoProcessing->TotalCpoProcessingJune;
        $data['line']['TotalCpoProcessingJuly'] = (float) $dataCpoProcessing->TotalCpoProcessingJuly;
        $data['line']['TotalCpoProcessingAugust'] = (float) $dataCpoProcessing->TotalCpoProcessingAugust;
        $data['line']['TotalCpoProcessingSeptember'] = (float) $dataCpoProcessing->TotalCpoProcessingSeptember;
        $data['line']['TotalCpoProcessingOctober'] = (float) $dataCpoProcessing->TotalCpoProcessingOctober;
        $data['line']['TotalCpoProcessingNovember'] = (float) $dataCpoProcessing->TotalCpoProcessingNovember;
        $data['line']['TotalCpoProcessingDecember'] = (float) $dataCpoProcessing->TotalCpoProcessingDecember;

        $data['line']['TotalPkProcessingJanuary'] = (float) $dataPkProcessing->TotalPkProcessingJanuary;
        $data['line']['TotalPkProcessingFebruary'] = (float) $dataPkProcessing->TotalPkProcessingFebruary;
        $data['line']['TotalPkProcessingMarch'] = (float) $dataPkProcessing->TotalPkProcessingMarch;
        $data['line']['TotalPkProcessingApril'] = (float) $dataPkProcessing->TotalPkProcessingApril;
        $data['line']['TotalPkProcessingMay'] = (float) $dataPkProcessing->TotalPkProcessingMay;
        $data['line']['TotalPkProcessingJune'] = (float) $dataPkProcessing->TotalPkProcessingJune;
        $data['line']['TotalPkProcessingJuly'] = (float) $dataPkProcessing->TotalPkProcessingJuly;
        $data['line']['TotalPkProcessingAugust'] = (float) $dataPkProcessing->TotalPkProcessingAugust;
        $data['line']['TotalPkProcessingSeptember'] = (float) $dataPkProcessing->TotalPkProcessingSeptember;
        $data['line']['TotalPkProcessingOctober'] = (float) $dataPkProcessing->TotalPkProcessingOctober;
        $data['line']['TotalPkProcessingNovember'] = (float) $dataPkProcessing->TotalPkProcessingNovember;
        $data['line']['TotalPkProcessingDecember'] = (float) $dataPkProcessing->TotalPkProcessingDecember;
        //end
        
        //line grafik cpo pk Despatch
        $data['line']['TotalCpoDespatchJanuary'] = (float) $dataCpoDespatch->TotalCpoDespatchJanuary;
        $data['line']['TotalCpoDespatchFebruary'] = (float) $dataCpoDespatch->TotalCpoDespatchFebruary;
        $data['line']['TotalCpoDespatchMarch']    = (float) $dataCpoDespatch->TotalCpoDespatchMarch;
        $data['line']['TotalCpoDespatchApril'] = (float) $dataCpoDespatch->TotalCpoDespatchApril;
        $data['line']['TotalCpoDespatchMay'] = (float) $dataCpoDespatch->TotalCpoDespatchMay;
        $data['line']['TotalCpoDespatchJune'] = (float) $dataCpoDespatch->TotalCpoDespatchJune;
        $data['line']['TotalCpoDespatchJuly'] = (float) $dataCpoDespatch->TotalCpoDespatchJuly;
        $data['line']['TotalCpoDespatchAugust'] = (float) $dataCpoDespatch->TotalCpoDespatchAugust;
        $data['line']['TotalCpoDespatchSeptember'] = (float) $dataCpoDespatch->TotalCpoDespatchSeptember;
        $data['line']['TotalCpoDespatchOctober'] = (float) $dataCpoDespatch->TotalCpoDespatchOctober;
        $data['line']['TotalCpoDespatchNovember'] = (float) $dataCpoDespatch->TotalCpoDespatchNovember;
        $data['line']['TotalCpoDespatchDecember'] = (float) $dataCpoDespatch->TotalCpoDespatchDecember;

        $data['line']['TotalPKDespatchJanuary'] = (float) $dataPKDespatch->TotalPKDespatchJanuary;
        $data['line']['TotalPKDespatchFebruary'] = (float) $dataPKDespatch->TotalPKDespatchFebruary;
        $data['line']['TotalPKDespatchMarch']    = (float) $dataPKDespatch->TotalPKDespatchMarch;
        $data['line']['TotalPKDespatchApril'] = (float) $dataPKDespatch->TotalPKDespatchApril;
        $data['line']['TotalPKDespatchMay'] = (float) $dataPKDespatch->TotalPKDespatchMay;
        $data['line']['TotalPKDespatchJune'] = (float) $dataPKDespatch->TotalPKDespatchJune;
        $data['line']['TotalPKDespatchJuly'] = (float) $dataPKDespatch->TotalPKDespatchJuly;
        $data['line']['TotalPKDespatchAugust'] = (float) $dataPKDespatch->TotalPKDespatchAugust;
        $data['line']['TotalPKDespatchSeptember'] = (float) $dataPKDespatch->TotalPKDespatchSeptember;
        $data['line']['TotalPKDespatchOctober'] = (float) $dataPKDespatch->TotalPKDespatchOctober;
        $data['line']['TotalPKDespatchNovember'] = (float) $dataPKDespatch->TotalPKDespatchNovember;
        $data['line']['TotalPKDespatchDecember'] = (float) $dataPKDespatch->TotalPKDespatchDecember;
        //end
        
        

        // echo "<pre>";
        // // echo print_r($data);
        // echo print_r($query->result());
        // echo "</pre>";
        return $data;

    }

    public function Gettransactionfarmer($get)
    {   
        if (!isset($get['start'])) {
           $get['start'] = 0;
        }

        $where = "";
        $sqlFilter = "";
        
        if($get['sshSupplyChildID']=='false'){
            $get['sshSupplyChildID'] = '';
        }

        $users = $this->db->query("SELECT * FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->result_array();

        $SupplychainID = @$users[0]['SupplychainID'];
        
        if (!empty($get['sshSupplyChildID'])) {
            $SupplychainID = $get['sshSupplyChildID'];
        }

        if (!empty($SupplychainID)) {
            $where = "AND a.SupplychainID = ".$SupplychainID;
        }

        if ($get['textSearch'] != "") {
            $sqlFilter .= " AND b.MemberName like '%{$get['textSearch']}%'";
            $get['start'] = 0;
        }
        
        if ($get['dropdownYear'] != "") {
            $year = $get['dropdownYear'];
            $get['start']     = 0;
           
            $whereFilter = "AND YEAR(a.DateTransaction) = '$year'";

        } else {
            $whereFilter = "";
        }
        
        if ($get['dropdownProvince'] != "") {
            $provinceID = $get['dropdownProvince'];
            $get['start']     = 0;
           
            $whereFilterProvince = "AND i.ProvinceID = '$provinceID'";

        } else {
            $whereFilterProvince = "";
        }

        if ($get['dropdownDistrict'] != "") {
            $districtID = $get['dropdownDistrict'];
            $get['start']     = 0;
           
            $whereFilterdistrict = "AND h.districtID = '$districtID'";

        } else {
            $whereFilterdistrict = "";
        }
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    IFNULL(b.MemberDisplayID, IFNULL(bb.MemberID, '-')) AS MemberDisplayID,
                    a.`TransNumber` AS TransNumber,
                    a.`DateTransaction` AS DateTransaction,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                       kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) AS SupplierName,
                    a.`VolumeNetto`,
                    IFNULL(i.ProvinceID,'-') AS ProvinceID,
                    IFNULL(i.Province,'-') AS Province,
                    IFNULL(h.DistrictID,'-') AS DistrictID,
                    IFNULL(h.District,'-') AS District,
                    IFNULL(g.SubDistrictID,'-') AS SubDistrictID,
                    IFNULL(g.SubDistrict,'-') AS SubDistrict,
                    IFNULL(f.VillageID,'-') AS VillageID,
                    IFNULL(f.Village,'-') AS Village,
                    (
                        SELECT 
                            SUM(a.VolumeBruto)
                        FROM 
                            ktv_tc_supplychain_delivery ktsd 
                        LEFT JOIN 
                            ktv_tc_supplychain_delivery_detail ktsdd ON ktsd.DeliveryID = ktsdd.DeliveryID
                        LEFT JOIN 
                            ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID
                        LEFT JOIN 
                            ktv_tc_supplychain_transaction a ON a.SupplyBatchID = ktsb.SupplyBatchID
                        WHERE 
                            ktsd.DeliveryStatusID IN ('4','5')
                        $where
                    ) AS FFBReceived
                    FROM
                        ktv_tc_supplychain_transaction a
                    LEFT JOIN
                        ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
                    LEFT JOIN
                        ktv_members bb on a.SupplyID = bb.MemberID 
                    LEFT JOIN
                        ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                    LEFT JOIN
                        ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
                    LEFT JOIN
                        view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                    LEFT JOIN
                        ktv_trace_package c on a.PackageID=c.PackageID
                    LEFT JOIN
                        ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                    LEFT JOIN
                        view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN
                        ktv_mill m2 on m2.MillID = a.MillID
                    LEFT JOIN
                        view_tc_supplychain_org mem on mem.SupplychainID = a.DOID
                    LEFT JOIN
                        ktv_members mem2 on mem2.MemberID = a.AgentID
                    LEFT JOIN
                        view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                    LEFT JOIN 
                        ktv_members_extension kms on kms.MemberID = vso3.ObjID
                    LEFT JOIN 
                        ktv_village f ON f.VillageID = b.VillageID
                    LEFT JOIN 
                        ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
                    LEFT JOIN 
                        ktv_district h ON h.DistrictID = g.DistrictID
                    LEFT JOIN 
                        ktv_province i ON i.ProvinceID = h.ProvinceID
                  WHERE 
                     a.StatusCode = 'active'
                  $where
                  $sqlFilter
                  $whereFilter
                  $whereFilterProvince
                  $whereFilterdistrict
                  LIMIT ? , ?
               ";

        $query = $this->db->query($sql,array(intval($get['start']), intval($get['limit'])));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $result['data'] =  $query->result();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function GetTransactionFarmerExport($get)
    {   
        $where = "";
        $sqlFilter = "";
        
        if($get['sshSupplyChildID']=='false'){
            $get['sshSupplyChildID'] = '';
        }

        $users = $this->db->query("SELECT * FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->result_array();

        $SupplychainID = @$users[0]['SupplychainID'];
        
        if (!empty($get['sshSupplyChildID'])) {
            $SupplychainID = $get['sshSupplyChildID'];
        }

        if (!empty($SupplychainID)) {
            $where = "AND a.SupplychainID = ".$SupplychainID;
        }

        if ($get['textSearch'] != "") {
            $sqlFilter .= " AND b.MemberName like '%{$get['textSearch']}%'";
        }
        
        if ($get['dropdownYear'] != "") {
            $year = $get['dropdownYear'];
           
            $whereFilter = "AND YEAR(a.DateTransaction) = '$year'";

        } else {
            $whereFilter = "";
        }
        
        if ($get['dropdownProvince'] != "") {
            $provinceID = $get['dropdownProvince'];
           
            $whereFilterProvince = "AND i.ProvinceID = '$provinceID'";

        } else {
            $whereFilterProvince = "";
        }

        if ($get['dropdownDistrict'] != "") {
            $districtID = $get['dropdownDistrict'];
           
            $whereFilterdistrict = "AND h.districtID = '$districtID'";

        } else {
            $whereFilterdistrict = "";
        }
        
        $sql = "SELECT IFNULL(b.MemberDisplayID, IFNULL(bb.MemberID, '-')) AS MemberDisplayID,
                    a.`TransNumber` AS TransNumber,
                    a.`DateTransaction` AS DateTransaction,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                       kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) AS SupplierName,
                    a.`VolumeNetto`,
                    IFNULL(i.ProvinceID,'-') AS ProvinceID,
                    IFNULL(i.Province,'-') AS Province,
                    IFNULL(h.DistrictID,'-') AS DistrictID,
                    IFNULL(h.District,'-') AS District,
                    IFNULL(g.SubDistrictID,'-') AS SubDistrictID,
                    IFNULL(g.SubDistrict,'-') AS SubDistrict,
                    IFNULL(f.VillageID,'-') AS VillageID,
                    IFNULL(f.Village,'-') AS Village,
                    (
                        SELECT 
                            SUM(a.VolumeBruto)
                        FROM 
                            ktv_tc_supplychain_delivery ktsd 
                        LEFT JOIN 
                            ktv_tc_supplychain_delivery_detail ktsdd ON ktsd.DeliveryID = ktsdd.DeliveryID
                        LEFT JOIN 
                            ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID
                        LEFT JOIN 
                            ktv_tc_supplychain_transaction a ON a.SupplyBatchID = ktsb.SupplyBatchID
                        WHERE 
                            ktsd.DeliveryStatusID IN ('4','5')
                        $where
                    ) AS FFBReceived
                    FROM
                        ktv_tc_supplychain_transaction a
                    LEFT JOIN
                        ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
                    LEFT JOIN
                        ktv_members bb on a.SupplyID = bb.MemberID 
                    LEFT JOIN
                        ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                    LEFT JOIN
                        ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
                    LEFT JOIN
                        view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                    LEFT JOIN
                        ktv_trace_package c on a.PackageID=c.PackageID
                    LEFT JOIN
                        ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                    LEFT JOIN
                        view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN
                        ktv_mill m2 on m2.MillID = a.MillID
                    LEFT JOIN
                        view_tc_supplychain_org mem on mem.SupplychainID = a.DOID
                    LEFT JOIN
                        ktv_members mem2 on mem2.MemberID = a.AgentID
                    LEFT JOIN
                        view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                    LEFT JOIN 
                        ktv_members_extension kms on kms.MemberID = vso3.ObjID
                    LEFT JOIN 
                        ktv_village f ON f.VillageID = b.VillageID
                    LEFT JOIN 
                        ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
                    LEFT JOIN 
                        ktv_district h ON h.DistrictID = g.DistrictID
                    LEFT JOIN 
                        ktv_province i ON i.ProvinceID = h.ProvinceID
                  WHERE 
                     a.StatusCode = 'active'
                  $where
                  $sqlFilter
                  $whereFilter
                  $whereFilterProvince
                  $whereFilterdistrict
               ";

        $query = $this->db->query($sql);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $result['data'] =  $query->result();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function GettransactionSupplier($get)
    {   
        if (!isset($get['start'])) {
           $get['start'] = 0;
        }

        $where = "";
        $sqlFilter = "";
        
        if($get['sshSupplyChildID']=='false'){
            $get['sshSupplyChildID'] = '';
        }

        $users = $this->db->query("SELECT * FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->result_array();
        
        $PartnerID = @$users[0]['PartnerID'];
        
        if (!empty($get['sshSupplyChildID'])) {
            $PartnerID = $get['sshSupplyChildID'];
        }

        if (!empty($PartnerID)) {
            $where = "AND op.PartnerID = ".$PartnerID;
        }
        
        if ($get['AdvRowDateTransaction'] == "true") {
            $DateStart = $get['AdvDateTransactionBegin'];
            $DateEnd   = $get['AdvDateTransactionEnd'];
            $get['start']     = 0;

            $whereFilter = "AND SUBSTR(ktst.DateTransaction,1,10) BETWEEN '$DateStart' AND '$DateEnd'";

        } else {
            $whereFilter = "";
        }

        if ($get['textSearch'] != "") {
            $sqlFilter .= " AND me.agCompanyName like '%{$get['textSearch']}%'";
            $get['start'] = 0;
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                   a.MemberDisplayID AS SupplierID
                    , IFNULL(me.agCompanyName, a.MemberName) AS SupplierName
                    , sd.SubDistrict
                    , d.District
                    , SUM(ktstd.TotalCapacity) / 1000  AS totalffbreceived
                    FROM
                        ktv_members a
                        LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                        LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                        LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                        LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                        LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                        LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                        LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                        LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                        LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                        LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                        LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                        LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                    WHERE
                        a.StatusCode = 'active'
                        AND ktsd.SupplychainID IS NOT NULL
                        AND ktsd.DeliveryStatusID = '4'
                        $where
                        $sqlFilter
                        $whereFilter
                    GROUP BY a.MemberID
                  LIMIT ? , ?
               ";

        $query = $this->db->query($sql,array(intval($get['start']), intval($get['limit'])));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $result['data'] =  $query->result();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function ListSupplier()
    {   

        $users = $this->db->query("SELECT * FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->result_array();
        
        $PartnerID = @$users[0]['PartnerID'];
        
        if (!empty($PartnerID)) {
            $where = "AND op.PartnerID = ".$PartnerID;
        }
        
        $sql = "SELECT 
                   a.MemberDisplayID AS id
                    , IFNULL(me.agCompanyName, a.MemberName) AS name
                    FROM
                        ktv_members a
                        LEFT JOIN ktv_tc_supplychain_org o ON a.MemberID = o.ObjID
                        LEFT JOIN ktv_tc_supplychain_org_rel orel ON orel.ChildID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_org op ON orel.ParentID = op.SupplychainID
                        LEFT JOIN ktv_member_role mr on mr.MemberID = a.MemberID
                        LEFT JOIN ktv_ref_member_role rmr on rmr.MRoleID = mr.MRoleID
                        LEFT JOIN ktv_members_extension me on me.MemberID = a.MemberID
                        LEFT JOIN ktv_sme_sp_code sc on sc.MemberID = a.MemberID
                        LEFT JOIN ktv_mill_sp_code msc on msc.SPCodeID = sc.SPCodeID AND msc.MillID = op.ObjID
                        LEFT JOIN ktv_village v on v.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
                        LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
                        LEFT JOIN ktv_survey_plot_sme sps on sps.MemberID = a.MemberID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd on ktsd.SupplychainID = o.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd on ktsdd.DeliveryID = ktsd.DeliveryID
                        LEFT JOIN ktv_tc_supplychain_transaction_detail ktstd on ktstd.DeliveryDetailID = ktsdd.DeliveryID
                        LEFT JOIN ktv_tc_supplychain_transaction ktst on ktst.SupplychainID = ktsd.SupplychainID
                    WHERE
                        a.StatusCode = 'active'
                        AND ktsd.SupplychainID IS NOT NULL
                        AND ktsd.DeliveryStatusID = '4'
                        $where
                    GROUP BY a.MemberID
               ";

        $query = $this->db->query($sql,array(intval($get['start']), intval($get['limit'])));

        if ($query->num_rows()>0) {
            return $query->result_array();
        }

        return false;

    }

    private function getAnnualPotentialNew($prov, $awal, $akhir, $mill, $do = '', $agent = ''){

        // $sqlwhere = "";
        // $sqlwhere2 = "";

        // if($_SESSION['is_admin'] == "1"){
        //     $sqlwhere = "";
        // } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
        //     //cek ktv_access_staff
        //     $sqlwhere = " AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
        //     if($_SESSION['PartnerID'] == 1){
        //         $sqlwhere = "";
        //     }
        // }elseif($_SESSION['role'] == "Mill"){
        //     $sqlwhere = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        // } else {
        //     //cek ktv_access_staff
        //     $sqlwhere = "AND apm.apmiPartnerID = '$_SESSION[PartnerID]'";
        // }

        // if($mill != '' OR $mill <> '' OR $mill != null){
        //     if($mill == "other"){
        //         $sqlwhere = " AND (vso.SupplychainID IS NULL OR vso.SupplychainID = 0)";
        //     }
        //     $sqlwhere = " AND vso.SupplychainID = '$mill'";
        // }
        // $SQL = "SELECT 
        //             SUM(km.MemberID) AS TransactionSupplier
        //         FROM 
        //             ktv_members km
        //         WHERE km.FarmerCategory = 'Mapped'";
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        $SQL = "SELECT 
                    SUM(ksp.AnnualProduction) AS AnnualProduction
                FROM 
                    ktv_tc_supplychain_transaction st 
                LEFT JOIN ktv_members km ON km.memberid = st.supplyid
                LEFT JOIN ktv_survey_plot ksp ON ksp.memberid = km.memberid
                WHERE st.SupplychainID = '$SupplychainID'";

        return $SQL;
    }

    private function getAnnualTraceableNew($prov, $awal, $akhir, $mill, $do = '', $agent = ''){
        
        @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }
        
        $SQL = "SELECT 
                    SUM(ksp.AnnualProduction) AS AnnualProductionTraceable
                FROM 
                    ktv_tc_supplychain_transaction st 
                LEFT JOIN ktv_members km ON km.memberid = st.supplyid
                LEFT JOIN ktv_survey_plot ksp ON ksp.memberid = km.memberid
                WHERE 
                st.SupplychainID = '$SupplychainID'
                AND
                st.isTraceable = 'YES'";

        return $SQL;
    }

    // public function readDataTraceabilityNew($prov = '', $district = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent='')
    // {
    //     @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
    //     if($SupplychainID==''){
    //         @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
    //     }

    //     $sqlwhere   = "";
    //     $sqlwhere2  = "";
    //     if($_SESSION['is_admin'] == "1"){
    //         $sqlwhere = "";
    //         $sqlwhere2 = "";
    //     } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
    //         $sql = "SELECT
    //                 GROUP_CONCAT(vso.SupplychainID) SupplychainID
    //             FROM
    //                 ktv_access_partner_mill apm
    //             LEFT JOIN
    //                 view_tc_supplychain_org vso on vso.ObjID = apm.apmiMillID AND vso.ObjType = 'mill'
    //             WHERE
    //                 apm.apmiPartnerID = ?";
    //         $query = $this->db->query($sql,array($_SESSION['PartnerID']));
    //         if($query->num_rows()>0){
    //             $row = $query->row();
    //             $sqlwhere   = "  AND a.SupplyDestMillOrgID IN ($row->SupplychainID) ";
    //             $sqlwhere2  = " AND vso.SupplychainID IN ($row->SupplychainID)";
    //         }
    //         if($_SESSION['PartnerID'] == 1){
    //             $sqlwhere = "";
    //             $sqlwhere2 = "";
    //         }
    //     }elseif($_SESSION['role'] == "Mill"){
    //         $sqlwhere   = " AND a.SupplyDestMillOrgID = '$_SESSION[SupplychainID]' ";
    //         $sqlwhere2  = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
    //     } else {
    //         //cek ktv_access_staff
    //         $sqlHakAksesPartner = "";
    //     }


    //     if($prov != ''){
    //         $sqlwhere .= " AND prov.ProvinceID = '$prov'";
    //     }

    //     if($district != ''){
    //         $sqlwhereDistrict .= " AND h.DistrictID = '$district'";
    //     }
        
    //     if($mill != ''){
    //         if($mill == "other"){
    //             $sqlwhere .= " AND (a.SupplyDestMillOrgID IS NULL OR a.SupplyDestMillOrgID = 0)";
    //             $sqlwhere2 .= " AND (vso.SupplychainID IS NULL OR vso.SupplychainID = 0)";
    //         }else{
    //             $sqlwhere .= " AND a.SupplyDestMillOrgID = '$mill'";
    //             $sqlwhere2 .= " AND vso.SupplychainID = '$mill'";
    //         }
    //     }
    //     if($do != ''){
    //         $sqlwhere .= " AND d.SupplychainID = '$do'";
    //     }

    //     $dateStart = date_create($awal);
    //     $dateEnd = date_create($akhir);

    //     $start = date_format($dateStart,"Y-m-d H:i:s");
    //     $end = date_format($dateEnd,"Y-m-d H:i:s");

    //     $transaction = 0;
        
    //     $sqlTrans = "SELECT COUNT(*) AS TransactionFarmer
    //                     FROM
    //                     (
    //                     SELECT           
    //                         COUNT(a.SupplyTransID) AS Total
    //                     FROM
    //                         ktv_tc_supplychain_transaction a
    //                     LEFT JOIN
    //                         ktv_members b on a.SupplyID = b.MemberID 
    //                     LEFT JOIN
    //                         ktv_tc_supplychain_batch d on a.SupplyBatchID=d.SupplyBatchID 
    //                     LEFT JOIN 
    //                         ktv_village f ON f.VillageID = b.VillageID
    //                     LEFT JOIN 
    //                         ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                     LEFT JOIN 
    //                         ktv_district h ON h.DistrictID = g.DistrictID
    //                     LEFT JOIN 
    //                         ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                     WHERE 
    //                         a.statusCode = 'active'
    //                     AND
    //                         a.SupplychainID = '$SupplychainID'
    //                     AND 
    //                         a.SupplyType = 'farmer'
    //                     $sqlwhereDistrict
    //                     AND a.DateTransaction BETWEEN '$start' AND '$end'
    //                 GROUP BY b.memberid
    //                 ) a";
    //     $datatrans = $this->db->query($sqlTrans);
    //     // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        
    //     if($datatrans->num_rows()){
    //         $dt= $datatrans->row();
    //         $transaction = $dt->TransactionFarmer;
    //     }

    //     $UserID = $_SESSION['userid'];
       
    //     $sql ="SELECT 
    //             (
    //                 SELECT 
    //                     SUM(st.PlantationNr) AS TotalPlantationTraceable
    //                 FROM 
    //                     ktv_tc_supplychain_transaction st 
    //                 WHERE 
    //                     st.SupplychainID = '$SupplychainID'
    //             ) AS TotalPlantationTraceable,
    //             (
    //                 SELECT 
    //                     SUM(st.VolumeBruto) AS TotalTraceableAgent
    //                 FROM 
    //                     ktv_tc_supplychain_transaction st
    //                 LEFT JOIN
    //                     ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = st.SupplyBatchID
    //                 WHERE 
    //                     st.SupplychainID = '$SupplychainID'
    //                 AND 
    //                     ktsb.SupplyBatchStatus = 'Closed'
    //             ) AS TotalTraceableAgent,
    //             (
    //                 SELECT 
    //                     SUM(ktst.VolumeBruto) AS TotalTransactionSupplier
    //                 FROM 
    //                     ktv_tc_supplychain_transaction ktst
    //                 LEFT JOIN
    //                     ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktst.SupplyBatchID
    //                 WHERE 
    //                     ktst.SupplychainID = '$SupplychainID'
    //                 AND 
    //                     ktsb.SupplyBatchStatus = 'Open'
    //             ) AS TotalTransactionSupplier,
    //             (
    //                 SELECT           
    //                     SUM(a.VolumeBruto) AS TotalWeigth
    //                 FROM
    //                     ktv_tc_supplychain_transaction a
    //                 LEFT JOIN
    //                     ktv_members b ON a.SupplyID = b.MemberID 
    //                 LEFT JOIN
    //                     ktv_tc_supplychain_batch d ON a.SupplyBatchID =d.SupplyBatchID
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = d.SupplyBatchID
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
    //                 LEFT JOIN 
    //                     ktv_village f ON f.VillageID = b.VillageID
    //                 LEFT JOIN 
    //                     ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                 LEFT JOIN 
    //                     ktv_district h ON h.DistrictID = g.DistrictID
    //                 LEFT JOIN 
    //                     ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                 WHERE 
    //                     a.statusCode = 'active'
    //                 AND
    //                     a.SupplychainID = '$SupplychainID'
    //                 AND 
    //                     ktsd.DeliveryStatusID IN ('3','4','5')
    //                     $sqlwhereDistrict
    //                 AND 
    //                     a.DateTransaction BETWEEN '$start' AND '$end'
    //             ) AS TotalFFBSold,
    //             (
    //                 SELECT           
    //                     SUM(a.VolumeBruto) AS TotalWeigth
    //                 FROM
    //                     ktv_tc_supplychain_transaction a
    //                 LEFT JOIN
    //                     ktv_members b ON a.SupplyID = b.MemberID 
    //                 LEFT JOIN
    //                     ktv_tc_supplychain_batch d ON a.SupplyBatchID =d.SupplyBatchID
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = d.SupplyBatchID
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
    //                 LEFT JOIN 
    //                     ktv_village f ON f.VillageID = b.VillageID
    //                 LEFT JOIN 
    //                     ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                 LEFT JOIN 
    //                     ktv_district h ON h.DistrictID = g.DistrictID
    //                 LEFT JOIN 
    //                     ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                 WHERE 
    //                     a.statusCode = 'active'
    //                 AND 
    //                     a.DateTransaction BETWEEN '$start' AND '$end'
    //                     $sqlwhereDistrict
    //                 AND
    //                     a.SupplychainID = '$SupplychainID'
    //             ) AS TotalFFBDealer,
    //             (
    //                 SELECT           
    //                     SUM(a.VolumeBruto) AS TotalWeigth
    //                 FROM
    //                     ktv_tc_supplychain_transaction a
    //                 LEFT JOIN
    //                     ktv_members b ON a.SupplyID = b.MemberID 
    //                 LEFT JOIN
    //                     ktv_tc_supplychain_batch d ON a.SupplyBatchID =d.SupplyBatchID
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = d.SupplyBatchID
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
    //                 LEFT JOIN 
    //                     ktv_village f ON f.VillageID = b.VillageID
    //                 LEFT JOIN 
    //                     ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                 LEFT JOIN 
    //                     ktv_district h ON h.DistrictID = g.DistrictID
    //                 LEFT JOIN 
    //                     ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                 WHERE 
    //                     a.statusCode = 'active'
    //                 AND
    //                     a.SupplychainID = '$SupplychainID'
    //                 AND
    //                     a.isTraceable = 'NO'
    //                     $sqlwhereDistrict
    //                 AND 
    //                     a.DateTransaction BETWEEN '$start' AND '$end'
    //             ) AS TotalFFBTraceablePlantation,
    //             (
    //                 SELECT           
    //                     COUNT(a.SupplyTransID) AS Total
    //                 FROM
    //                     ktv_tc_supplychain_transaction a
    //                 LEFT JOIN
    //                     ktv_members b on a.SupplyID = b.MemberID 
    //                 LEFT JOIN
    //                     ktv_tc_supplychain_batch d on a.SupplyBatchID=d.SupplyBatchID 
    //                 LEFT JOIN 
    //                     ktv_village f ON f.VillageID = b.VillageID
    //                 LEFT JOIN 
    //                     ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                 LEFT JOIN 
    //                     ktv_district h ON h.DistrictID = g.DistrictID
    //                 LEFT JOIN 
    //                     ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                 WHERE 
    //                     a.statusCode = 'active'
    //                 AND
    //                     a.SupplychainID = '$SupplychainID'
    //                 AND 
    //                     a.DateTransaction BETWEEN '$start' AND '$end'
    //                 AND 
    //                  d.SupplyBatchID IS NULL
    //                 $sqlwhereDistrict
    //             ) AS TotalFarmerFFB,
    //             (
    //                 SELECT 
    //                     COUNT(a.PlotNr) 
    //                 FROM 
    //                     ktv_survey_plot a
    //                 LEFT JOIN 
    //                     ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
    //                 LEFT JOIN 
    //                     ktv_village c ON a.VillageID=c.VillageID
    //                 LEFT JOIN 
    //                     ktv_members km ON km.MemberID = b.FarmerID
    //                 LEFT JOIN 
    //                     ktv_village f ON f.VillageID = km.VillageID
    //                 LEFT JOIN 
    //                     ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                 LEFT JOIN 
    //                     ktv_district h ON h.DistrictID = g.DistrictID
    //                 LEFT JOIN 
    //                     ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                 WHERE 
    //                     b.SupplychainID = '$SupplychainID'
    //                 $sqlwhereDistrict
    //             ) AS TotalPlantation
    //             FROM 
    //                 ktv_tc_supplychain_transaction st 
    //             LEFT JOIN
    //                 ktv_members b on st.SupplyID = b.MemberID 
    //             LEFT JOIN
    //                 ktv_tc_supplychain_batch d on st.SupplyID=d.SupplyBatchID 
    //             WHERE 
    //                 st.statusCode = 'active'
    //             AND
    //                 st.SupplychainID = '$SupplychainID'
    //             GROUP BY st.SupplychainID";

    //     $data = $this->db->query($sql);
    //     // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        
    //     $TotalFarmerFFB = 0;
    //     $t_mill = 0;
    //     $production = 0;
    //     $productionAll = 0;
    //     $stock = 0;
    //     $plantation = 0;
    //     $traceable_volume_farmer = 0;
    //     $traceable_volume_agent = 0;

    //     if($data->num_rows()){
    //         $dt= $data->row();
    //         $TotalFarmerFFB = $dt->TotalFarmerFFB;
    //         $plantation = $dt->TotalPlantation;
    //         $production = ROUND((float)$dt->TotalFFBSold / 1000, 2);
    //         $productionAll = ROUND((float)$dt->TotalFFBDealer / 1000, 2);
    //         $stock = ROUND((float)$dt->TotalFFBTraceablePlantation / 1000, 2);

    //         $traceable_volume_farmer        = ROUND((float)$dt->TotalTransactionSupplier / 1000, 2);
    //         $traceable_volume_agent         = ROUND((float)$dt->TotalTraceableAgent / 1000, 2);
    //         $traceable_volume_plantation    = ROUND((float)$dt->TotalPlantationTraceable / 1000, 2);
    //     }
    //     $sql2 = "SELECT
    //                 SUM(st.VolumeBruto) VolumeNetto
    //                 , sb.SupplyDestMillOrgID
    //             FROM
    //                 ktv_tc_supplychain_batch sb
    //             LEFT JOIN 
    //                 view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyDestMillOrgID
    //             LEFT JOIN ktv_mill mill on mill.MillID = vso.ObjID
    //             LEFT JOIN ktv_village vil ON mill.VillageID = vil.VillageID
    //             LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
    //             LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
    //             LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
    //             INNER JOIN 
    //                 ktv_tc_supplychain_transaction st ON st.SupplyBatchID = sb.SupplyBatchID
    //             LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
    //             LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
    //             WHERE
    //                 1 = 1 
    //             $sqlwhere 
    //             AND ktsd.DeliveryStatusID IN ('3','4','5')
    //             AND
    //                 ktsd.DeliveryDate BETWEEN '$awal' AND '$akhir'
    //             GROUP BY
    //                 ktsd.SupplyDestMillOrgID";
    //     $data2 = $this->db->query($sql2);
        
    //     if($data2->num_rows()>0){
    //         $traceable_volume_sme = ROUND((float)$data2->row()->VolumeBruto / 1000, 2);
    //     }
        
    //     $total_traceable_sales  = ROUND((float)($traceable_volume_mill + $traceable_volume_sme));

    //     $TotalFarmer = 0;

    //     $sqlTotalFarmer = "SELECT COUNT(*) AS TotalFarmer
    //                             FROM
    //                             (
    //                             SELECT 
    //                                 COUNT(km.MemberID) AS TotalFarmer 
    //                             FROM 
    //                                 ktv_members km
    //                             LEFT JOIN 
    //                                 ktv_tc_supplychain_farmer tsf on tsf.FarmerID = km.MemberID
    //                             LEFT JOIN
    //                                 ktv_tc_supplychain_transaction st ON st.SupplyID = tsf.FarmerID
    //                             LEFT JOIN 
    //                                 ktv_member_role mrole ON km.MemberID = mrole.MemberID
    //                             LEFT JOIN 
    //                                 ktv_village f ON f.VillageID = km.VillageID
    //                             LEFT JOIN 
    //                                 ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                             LEFT JOIN 
    //                                 ktv_district h ON h.DistrictID = g.DistrictID
    //                             LEFT JOIN 
    //                                 ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                             WHERE 
    //                                 tsf.SupplychainID = '$SupplychainID'
    //                             AND 
    //                                 MRoleID = '1'
    //                             $sqlwhereDistrict
    //                             GROUP BY km.MemberID
    //                         ) a";
    //     $dataFarmer = $this->db->query($sqlTotalFarmer);
    //     // echo $this->db->last_query();die;
        
    //     if($dataFarmer->num_rows()){
    //         $dt= $dataFarmer->row();
    //         $TotalFarmer = $dt->TotalFarmer;
    //     }

    //     $TotalDelivery = 0;

    //     $sqlTotalDelivery = "SELECT COUNT(*) AS TotalDelivery 
    //                             FROM (
    //                                 SELECT 
    //                                     COUNT(ktsd.DeliveryID) AS TotalDelivery 
    //                                 FROM 
    //                                     ktv_tc_supplychain_delivery ktsd 
    //                                 LEFT JOIN 
    //                                     ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID
    //                                 LEFT JOIN
    //                                     ktv_tc_supplychain_batch d ON d.SupplyBatchID = ktsdd.SupplyBatchID
    //                                 LEFT JOIN
    //                                     ktv_tc_supplychain_transaction a ON a.SupplychainID = ktsd.SupplychainID
    //                                 LEFT JOIN
    //                                     ktv_members b ON a.SupplyID = b.MemberID 
    //                                 LEFT JOIN 
    //                                     ktv_village f ON f.VillageID = b.VillageID
    //                                 LEFT JOIN 
    //                                     ktv_subdistrict g ON g.SubDistrictID = f.SubDistrictID
    //                                 LEFT JOIN 
    //                                     ktv_district h ON h.DistrictID = g.DistrictID
    //                                 LEFT JOIN 
    //                                     ktv_province prov ON prov.ProvinceID = h.ProvinceID
    //                                 WHERE 
    //                                     ktsd.SupplychainID = '$SupplychainID'
    //                                 AND
    //                                     ktsd.statusCode = 'active'
    //                                 AND 
    //                                     ktsd.DeliveryID IS NOT NULL
    //                                     $sqlwhereDistrict
    //                                 AND 
    //                                     ktsd.DeliveryDate BETWEEN '$start' AND '$end'
    //                                 GROUP by ktsd.DeliveryID
    //                             ) AS TotalDelivery";

    //     $dataDelivery = $this->db->query($sqlTotalDelivery);
    //     // echo $this->db->last_query();die;
        
    //     if($dataFarmer->num_rows()){
    //         $dt= $dataDelivery->row();
    //         $TotalDelivery = $dt->TotalDelivery;
    //     }
        
    //     $results['TotalDelivery'] = $TotalDelivery;
    //     $results['transaction'] = $transaction;
    //     $results['mill'] = $t_mill;
    //     $results['sme'] = $sme;
    //     $results['plot'] = $plot;
    //     $results['farmer'] = $TotalFarmer;
    //     $results['production'] = $production;
    //     $results['productionAll'] = $productionAll;
    //     $results['stock'] = $stock;
    //     $results['plantation'] = $plantation;
    //     $results['TotalFarmerFFB'] = $TotalFarmerFFB;
        
    //     /* annual potential */
    //     $annual_potential = $this->db->query($this->getAnnualPotentialNew($prov, $awal, $akhir, $mill));
        
    //     $annual = 0;
    //     if($annual_potential->num_rows()){
    //         foreach($annual_potential->result() as $ap){
    //             $annual += $ap->AnnualProduction;
    //         }
    //     }
        
    //     $total_traceable = $this->db->query($this->getAnnualTraceableNew($prov, $awal, $akhir, $mill));
        
    //     $totaltraceable = 0;
    //     if($total_traceable->num_rows()){
    //         foreach($total_traceable->result() as $at){
    //             $totaltraceable += $at->AnnualProductionTraceable;
    //         }
    //     }
       
    //     $results['potential_annual'] = array('data' => array(array('name' => 'Potential Volume', 'data' => array($annual)),
    //                                     array('name' => 'Total Traceable Volume', 'data' => array($totaltraceable))),
    //                                     'label' => array('Volume (TON)')
    //                                 );
        
    //     /* traceable volume */
    //     $results['traceable_volume'] = array('data' => array(
    //                                             array(
    //                                                 "name" => 'Traceable to Farmer',
    //                                                 "y" => $traceable_volume_farmer,
    //                                                 "sliced" => true,
    //                                                 "selected" => true
    //                                             ),
    //                                             array(
    //                                                 "name" => 'Traceable to Agent/Dealer',
    //                                                 "y" =>  $traceable_volume_agent
    //                                             ),
    //                                             array(
    //                                                 "name" => 'Traceable to Plantation',
    //                                                 "y" =>  $traceable_volume_plantation
    //                                             )
    //                                         ),
    //                                          'judul' => lang('FFB Traceability Percentage'),
    //                                          'yjudul' => lang('Volume'),
    //                                          'label' => array('Volume'));
    //     //farmer
    //     $SQLTransactionSupplier = "SELECT 
    //                             IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierJanuary,
    //                             IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierFebruary,
    //                             IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierMarch,
    //                             IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierApril,
    //                             IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierMay,
    //                             IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierJune,
    //                             IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierJuly,
    //                             IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierAugust,
    //                             IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierSeptember,
    //                             IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierOctober,
    //                             IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierNovember,
    //                             IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTransactionSupplierDecember
    //                             FROM 
    //                                 ktv_tc_supplychain_transaction ktst
    //                             LEFT JOIN
    //                                 ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktst.SupplyBatchID
    //                             WHERE 
    //                                 ktst.SupplychainID = '$SupplychainID'
    //                             AND 
    //                                 ktsb.SupplyBatchStatus = 'Open'
    //                             GROUP BY
    //                             MONTH(ktst.`DateTransaction`)";
    //     $QuerySQLTransactionSupplier= $this->db->query($SQLTransactionSupplier);

    //     $dataTransactionSupplier = $QuerySQLTransactionSupplier->row();
    //     //line chart 
    //     $results['line']['number_farmer_january'] = (float) $dataTransactionSupplier->TotalTransactionSupplierJanuary;
    //     $results['line']['number_farmer_february'] = (float) $dataTransactionSupplier->TotalTransactionSupplierFebruary;
    //     $results['line']['number_farmer_march'] = (float) $dataTransactionSupplier->TotalTransactionSupplierMarch;
    //     $results['line']['number_farmer_april'] = (float) $dataTransactionSupplier->TotalTransactionSupplierApril;
    //     $results['line']['number_farmer_may'] = (float) $dataTransactionSupplier->TotalTransactionSupplierMay;
    //     $results['line']['number_farmer_june'] = (float) $dataTransactionSupplier->TotalTransactionSupplierJune;
    //     $results['line']['number_farmer_july'] = (float) $dataTransactionSupplier->TotalTransactionSupplierJuly;
    //     $results['line']['number_farmer_august'] = (float) $dataTransactionSupplier->TotalTransactionSupplierAugust;
    //     $results['line']['number_farmer_september'] = (float) $dataTransactionSupplier->TotalTransactionSupplierSeptember;
    //     $results['line']['number_farmer_october'] = (float) $dataTransactionSupplier->TotalTransactionSupplierOctober;
    //     $results['line']['number_farmer_november'] = (float) $dataTransactionSupplier->TotalTransactionSupplierNovember;
    //     $results['line']['number_farmer_december'] = (float) $dataTransactionSupplier->TotalTransactionSupplierDecember;
    //     //end line chart
    //     //endfarmer

    //     //agent
    //     $sqlTraceableAgent = "SELECT 
    //                             IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentJanuary,
    //                             IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentFebruary,
    //                             IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentMarch,
    //                             IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentApril,
    //                             IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentMay,
    //                             IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentJune,
    //                             IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentJuly,
    //                             IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentAugust,
    //                             IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentSeptember,
    //                             IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentOctober,
    //                             IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentNovember,
    //                             IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentDecember
    //                             FROM 
    //                                 ktv_tc_supplychain_transaction ktst
    //                             LEFT JOIN
    //                                 ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktst.SupplyBatchID
    //                             WHERE 
    //                                 ktst.SupplychainID = '$SupplychainID'
    //                             AND 
    //                                 ktsb.SupplyBatchStatus = 'Closed'
    //                             GROUP BY
    //                             MONTH(ktst.`DateTransaction`)";

    //     $QuerysqlTraceableAgent= $this->db->query($sqlTraceableAgent);

    //     $dataTraceableAgent = $QuerysqlTraceableAgent->row();
    //     //line chart 
    //     $results['line']['number_agent_january'] = (float) $dataTraceableAgent->TotalTraceableAgentJanuary;
    //     $results['line']['number_agent_february'] = (float) $dataTraceableAgent->TotalTraceableAgentFebruary;
    //     $results['line']['number_agent_march'] = (float) $dataTraceableAgent->TotalTraceableAgentMarch;
    //     $results['line']['number_agent_april'] = (float) $dataTraceableAgent->TotalTraceableAgentApril;
    //     $results['line']['number_agent_may'] = (float) $dataTraceableAgent->TotalTraceableAgentMay;
    //     $results['line']['number_agent_june'] = (float) $dataTraceableAgent->TotalTraceableAgentJune;
    //     $results['line']['number_agent_july'] = (float) $dataTraceableAgent->TotalTraceableAgentJuly;
    //     $results['line']['number_agent_august'] = (float) $dataTraceableAgent->TotalTraceableAgentAugust;
    //     $results['line']['number_agent_september'] = (float) $dataTraceableAgent->TotalTraceableAgentSeptember;
    //     $results['line']['number_agent_october'] = (float) $dataTraceableAgent->TotalTraceableAgentOctober;
    //     $results['line']['number_agent_november'] = (float) $dataTraceableAgent->TotalTraceableAgentNovember;
    //     $results['line']['number_agent_december'] = (float) $dataTraceableAgent->TotalTraceableAgentDecember;
    //     //end line chart
    //     //end agent

    //     //plantation
    //     $sqlTraceablePlanatation = "SELECT 
    //                             IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationJanuary,
    //                             IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationFebruary,
    //                             IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationMarch,
    //                             IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationApril,
    //                             IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationMay,
    //                             IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationJune,
    //                             IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationJuly,
    //                             IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationAugust,
    //                             IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationSeptember,
    //                             IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationOctober,
    //                             IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationNovember,
    //                             IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`PlantationNr` /1000),0) AS TotalTraceablePlanatationDecember
    //                             FROM 
    //                                 ktv_tc_supplychain_transaction ktst
    //                             LEFT JOIN
    //                                 ktv_survey_plot ksp ON ksp.memberid = ktst.supplyid 
    //                             WHERE 
    //                                 ktst.SupplychainID = '$SupplychainID'
    //                             GROUP BY
    //                             MONTH(ktst.`DateTransaction`)";

    //     $QuerysqlTraceablePlanatation= $this->db->query($sqlTraceablePlanatation);

    //     $dataTraceablePlanatation = $QuerysqlTraceablePlanatation->row();
    //     //line chart 
    //     $results['line']['number_plantation_january'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationJanuary;
    //     $results['line']['number_plantation_february'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationFebruary;
    //     $results['line']['number_plantation_march'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationMarch;
    //     $results['line']['number_plantation_april'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationApril;
    //     $results['line']['number_plantation_may'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationMay;
    //     $results['line']['number_plantation_june'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationJune;
    //     $results['line']['number_plantation_july'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationJuly;
    //     $results['line']['number_plantation_august'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationAugust;
    //     $results['line']['number_plantation_september'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationSeptember;
    //     $results['line']['number_plantation_october'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationOctober;
    //     $results['line']['number_plantation_november'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationNovember;
    //     $results['line']['number_plantation_december'] = (float) $dataTraceablePlanatation->TotalTraceablePlanatationDecember;
    //     //end line chart
    //     //end plantation

    //      //delivery FFB
    //      $sqlTraceableAgent = "SELECT 
    //      IF(MONTH(ktst.`DateTransaction`) = 1, SUM(ktst.`VolumeBruto` /1000),0) AS TotalTraceableAgentJanuary,
    //      IF(MONTH(ktst.`DateTransaction`) = 2, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentFebruary,
    //      IF(MONTH(ktst.`DateTransaction`) = 3, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentMarch,
    //      IF(MONTH(ktst.`DateTransaction`) = 4, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentApril,
    //      IF(MONTH(ktst.`DateTransaction`) = 5, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentMay,
    //      IF(MONTH(ktst.`DateTransaction`) = 6, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentJune,
    //      IF(MONTH(ktst.`DateTransaction`) = 7, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentJuly,
    //      IF(MONTH(ktst.`DateTransaction`) = 8, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentAugust,
    //      IF(MONTH(ktst.`DateTransaction`) = 9, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentSeptember,
    //      IF(MONTH(ktst.`DateTransaction`) = 10, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentOctober,
    //      IF(MONTH(ktst.`DateTransaction`) = 11, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentNovember,
    //      IF(MONTH(ktst.`DateTransaction`) = 12, SUM(ktst.`VolumeBruto` / 1000),0) AS TotalTraceableAgentDecember
    //      FROM 
    //         ktv_tc_supplychain_transaction ktst
    //      LEFT JOIN
    //         ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktst.SupplyBatchID
    //      WHERE 
    //         ktst.SupplychainID = '$SupplychainID'
    //      AND 
    //         ktsb.SupplyBatchStatus = 'Closed'
    //      GROUP BY
    //      MONTH(ktst.`DateTransaction`)";

    //     $QuerysqlTraceableAgent= $this->db->query($sqlTraceableAgent);

    //     $dataTraceableAgent = $QuerysqlTraceableAgent->row();
    //     //line chart 
    //     $results['line']['number_agent_january'] = (float) $dataTraceableAgent->TotalTraceableAgentJanuary;
    //     $results['line']['number_agent_february'] = (float) $dataTraceableAgent->TotalTraceableAgentFebruary;
    //     $results['line']['number_agent_march'] = (float) $dataTraceableAgent->TotalTraceableAgentMarch;
    //     $results['line']['number_agent_april'] = (float) $dataTraceableAgent->TotalTraceableAgentApril;
    //     $results['line']['number_agent_may'] = (float) $dataTraceableAgent->TotalTraceableAgentMay;
    //     $results['line']['number_agent_june'] = (float) $dataTraceableAgent->TotalTraceableAgentJune;
    //     $results['line']['number_agent_july'] = (float) $dataTraceableAgent->TotalTraceableAgentJuly;
    //     $results['line']['number_agent_august'] = (float) $dataTraceableAgent->TotalTraceableAgentAugust;
    //     $results['line']['number_agent_september'] = (float) $dataTraceableAgent->TotalTraceableAgentSeptember;
    //     $results['line']['number_agent_october'] = (float) $dataTraceableAgent->TotalTraceableAgentOctober;
    //     $results['line']['number_agent_november'] = (float) $dataTraceableAgent->TotalTraceableAgentNovember;
    //     $results['line']['number_agent_december'] = (float) $dataTraceableAgent->TotalTraceableAgentDecember;
    //     //end line chart
        
    //     //Delivery FFB
    //     $sqlTraceableDelivery = "SELECT 
    //             IF(MONTH(ktsd.`DeliveryDate`) = 1, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryJanuary,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 2, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryFebruary,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 3, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryMarch,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 4, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryApril,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 5, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryMay,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 6, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryJune,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 7, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryJuly,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 8, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryAugust,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 9, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliverySeptember,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 10, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryOctober,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 11, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryNovember,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 12, SUM(ktsdd.`Weight` /1000),0) AS TotalTraceableDeliveryDecember
    //             FROM 
    //                 ktv_tc_supplychain_delivery ktsd 
    //             LEFT JOIN 
    //                 ktv_tc_supplychain_delivery_detail ktsdd ON ktsd.DeliveryID = ktsdd.DeliveryID
    //             LEFT JOIN 
    //                 ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID
    //             WHERE 
    //                 ktsd.SupplychainID = '$SupplychainID'
    //             AND 
    //                 ktsd.DeliveryStatusID = '3'
    //             GROUP BY
    //             MONTH(ktsd.`DeliveryDate`)";

    //     $QuerysqlTraceableDelivery= $this->db->query($sqlTraceableDelivery);
    
    //     $dataTraceableDelivery = $QuerysqlTraceableDelivery->row();

    //     //column chart 
    //     $results['column_delivery']['number_delivery_january'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryJanuary;
    //     $results['column_delivery']['number_delivery_february'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryFebruary;
    //     $results['column_delivery']['number_delivery_march'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryMarch;
    //     $results['column_delivery']['number_delivery_april'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryApril;
    //     $results['column_delivery']['number_delivery_may'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryMay;
    //     $results['column_delivery']['number_delivery_june'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryJune;
    //     $results['column_delivery']['number_delivery_july'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryJuly;
    //     $results['column_delivery']['number_delivery_august'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryAugust;
    //     $results['column_delivery']['number_delivery_september'] = (float) $dataTraceableDelivery->TotalTraceableDeliverySeptember;
    //     $results['column_delivery']['number_delivery_october'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryOctober;
    //     $results['column_delivery']['number_delivery_november'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryNovember;
    //     $results['column_delivery']['number_delivery_december'] = (float) $dataTraceableDelivery->TotalTraceableDeliveryDecember;
    //     //end column chart
    //     //end Delivery FFB

    //     //Received FFB
    //     $sqlTraceableReceived = "SELECT 
    //             IF(MONTH(ktsd.`DeliveryDate`) = 1, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedJanuary,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 2, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedFebruary,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 3, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedMarch,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 4, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedApril,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 5, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedMay,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 6, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedJune,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 7, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedJuly,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 8, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedAugust,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 9, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedSeptember,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 10, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedOctober,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 11, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedNovember,
    //             IF(MONTH(ktsd.`DeliveryDate`) = 12, SUM(ktsdd.`Weight`/ 1000),0) AS TotalTraceableReceivedDecember
    //             FROM 
    //                 ktv_tc_supplychain_delivery ktsd 
    //             LEFT JOIN 
    //                 ktv_tc_supplychain_delivery_detail ktsdd ON ktsd.DeliveryID = ktsdd.DeliveryID
    //             LEFT JOIN 
    //                 ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = ktsdd.SupplyBatchID
    //             WHERE 
    //                 ktsd.SupplychainID = '$SupplychainID'
    //             AND 
    //                 ktsd.DeliveryStatusID IN ('4','5')
    //             GROUP BY
    //             MONTH(ktsd.`DeliveryDate`)";

    //     $QueryTraceableReceived= $this->db->query($sqlTraceableReceived);
    
    //     $dataTracesqlTraceableReceived = $QueryTraceableReceived->row();

    //     //column chart 
    //     $results['column_delivery']['number_received_january'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedJanuary;
    //     $results['column_delivery']['number_received_february'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedFebruary;
    //     $results['column_delivery']['number_received_march'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedMarch;
    //     $results['column_delivery']['number_received_april'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedApril;
    //     $results['column_delivery']['number_received_may'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedMay;
    //     $results['column_delivery']['number_received_june'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedJune;
    //     $results['column_delivery']['number_received_july'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedJuly;
    //     $results['column_delivery']['number_received_august'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedAugust;
    //     $results['column_delivery']['number_received_september'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedSeptember;
    //     $results['column_delivery']['number_received_october'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedOctober;
    //     $results['column_delivery']['number_received_november'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedNovember;
    //     $results['column_delivery']['number_received_december'] = (float) $dataTracesqlTraceableReceived->TotalTraceableReceivedDecember;
    //     //end column chart
    //     //end Received FFB

    //     return $results;
    // }

    // public function readDataTraceabilityNew($prov = '', $kab = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent='')
    // {
    //     $sqlwhere   = "";
    //     $sqlwhere2  = "";
    //     if($_SESSION['is_admin'] == "1"){
    //         $sqlwhere = "";
    //         $sqlwhere2 = "";
    //     } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
    //         $sql = "SELECT
    //                 GROUP_CONCAT(vso.SupplychainID) SupplychainID
    //             FROM
    //                 ktv_access_partner_mill apm
    //             LEFT JOIN
    //                 view_tc_supplychain_org vso on vso.ObjID = apm.apmiMillID AND vso.ObjType = 'mill'
    //             WHERE
    //                 apm.apmiPartnerID = ?";
    //         $query = $this->db->query($sql,array($_SESSION['PartnerID']));
    //         if($query->num_rows()>0){
    //             $row = $query->row();
    //             $sqlwhere   = " AND sb.StatusCode = 'active' AND sb.SupplyDestMillOrgID IN ($row->SupplychainID) ";
    //             $sqlwhere2  = " AND vso.SupplychainID IN ($row->SupplychainID)";
    //         }
    //         if($_SESSION['PartnerID'] == 1){
    //             $sqlwhere = "";
    //             $sqlwhere2 = "";
    //         }
    //     }elseif($_SESSION['role'] == "Mill"){
    //         $sqlwhere   = " AND sb.StatusCode = 'active' AND sb.SupplyDestMillOrgID = '$_SESSION[SupplychainID]' ";
    //         $sqlwhere2  = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
    //     } else {
    //         //cek ktv_access_staff
    //         $sqlHakAksesPartner = "";
    //     }


    //     if($prov != ''){
    //         $sqlwhere .= " AND prov.ProvinceID = '$prov'";
    //     }
    //     if($mill != ''){
    //         if($mill == "other"){
    //             $sqlwhere .= " AND (sb.SupplyDestMillOrgID IS NULL OR sb.SupplyDestMillOrgID = 0)";
    //             $sqlwhere2 .= " AND (vso.SupplychainID IS NULL OR vso.SupplychainID = 0)";
    //         }else{
    //             $sqlwhere .= " AND sb.SupplyDestMillOrgID = '$mill'";
    //             $sqlwhere2 .= " AND vso.SupplychainID = '$mill'";
    //         }
    //     }
    //     if($do != ''){
    //         $sqlwhere .= " AND st.SupplychainID = '$do'";
    //     }
    //     // echo "<pre>";
    //     // print_r($sqlwhere);
    //     // die;
    //     $sql = "SELECT
    //             SUM(dash.total_batch) total_batch
    //             ,SUM(dash.total_transaction) total_transaction
    //             ,SUM(dash.total_farmer) total_farmer
    //             ,SUM(dash.total_production) total_production
    //             ,SUM(dash.total_traceable_sme) total_traceable_sme
    //             ,SUM(dash.total_do) total_do
    //             ,SUM(dash.plot_total) plot_total
    //             ,SUM(dash.total_agent) total_agent
    //         FROM
    //         (
    //             SELECT
    //                 COUNT(DISTINCT sb.SupplyBatchID) total_batch
    //                 ,COUNT(st.SupplyTransID) total_transaction
    //                 ,COUNT(DISTINCT st.SupplyID) total_farmer
    //                 ,SUM(st.VolumeNetto) total_production
    //                 ,SUM(vsme.VolumeNetto) total_traceable_sme
    //                 ,COUNT( DISTINCT ksr.ParentID ) total_do
    //                 ,COUNT(DISTINCT CONCAT(st.SupplyID,st.PlantationNr)) plot_total
    //                 ,COUNT( DISTINCT st.SupplychainID ) total_agent
    //             FROM
    //                 ktv_tc_supplychain_batch sb
    //             LEFT JOIN 
    //                 view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyDestMillOrgID AND vso.ObjType = 'mill'
    //             LEFT JOIN
    //             (
    //                 SELECT
    //                         COUNT(ksr.ParentID) jml_do
    //                         , ksr.ParentID
    //                 FROM
    //                         ktv_tc_supplychain_org_rel ksr
    //                 GROUP BY
    //                         ksr.ParentID
    //             ) ksr ON ksr.ParentID = vso.SupplychainID AND vso.ObjType = 'mill'
    //             INNER JOIN
    //                 ktv_tc_supplychain_transaction st on st.SupplyBatchID = sb.SupplyBatchID AND st.StatusCode = 'active' AND st.SupplyType IN ('Farmer','Nonfarmer')
    //             LEFT JOIN
    //                 view_tc_supplychain_org vso2 on vso2.SupplychainID = st.SupplychainID
    //             LEFT JOIN 
    //                 ktv_members m ON m.MemberID = vso2.ObjID 
    //             LEFT JOIN 
    //             (
    //                 SELECT 
    //                     GROUP_CONCAT(mr.MRoleID) MRoleID
    //                     , mr.MemberID
    //                 FROM 
    //                     ktv_member_role mr
    //                 GROUP BY
    //                     mr.MemberID
    //                 ORDER BY
    //                     mr.MRoleID ASC
    //             ) mr ON m.MemberID = mr.MemberID
    //             LEFT JOIN
    //                 (
    //                     SELECT
    //                         SUM(st.VolumeNetto) VolumeNetto
    //                         , sb.SupplyDestMillOrgID
    //                     FROM
    //                         ktv_tc_supplychain_batch sb
    //                     LEFT JOIN 
    //                         view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyDestMillOrgID
    //                     LEFT JOIN ktv_mill mill on mill.MillID = vso.ObjID
    //                     LEFT JOIN ktv_village vil ON mill.VillageID = vil.VillageID
    //                     LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
    //                     LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
    //                     LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
    //                     INNER JOIN 
    //                         ktv_tc_supplychain_transaction st ON st.SupplyBatchID = sb.SupplyBatchID AND st.SupplyType IN ('Farmer','NonFarmer')
    //                     WHERE
    //                         1 = 1 
    //                     $sqlwhere
    //                     AND 
    //                         sb.SupplyBatchStatus IN ('Sent','Open')
    //                     AND
    //                         sb.SupplyBatchDate BETWEEN '$awal' AND '$akhir'
    //                     GROUP BY
    //                         sb.SupplyDestMillOrgID
    //                 ) vsme on vsme.SupplyDestMillOrgID = sb.SupplyDestMillOrgID
    //             WHERE
    //                 1 = 1 
    //                 $sqlwhere
    //             AND
    //                 sb.SupplyBatchStatus IN ('Delivered')
    //             AND
    //                 sb.SupplyBatchDate BETWEEN '$awal' AND '$akhir'
    //             AND 
    //                 ( find_in_set(5,mr.MRoleID) OR find_in_set(6,mr.MRoleID) OR find_in_set(7,mr.MRoleID) OR find_in_set(8,mr.MRoleID) OR find_in_set(9,mr.MRoleID) OR find_in_set(10,mr.MRoleID) OR find_in_set(12,mr.MRoleID) OR find_in_set(13,mr.MRoleID) OR find_in_set(14,mr.MRoleID) )
    //             UNION
    //             SELECT
    //                 COUNT(DISTINCT sb.SupplyBatchID) total_batch
    //                 ,COUNT(st.SupplyTransID) total_transaction
    //                 ,COUNT(DISTINCT st.SupplyID) total_farmer
    //                 ,SUM(st.VolumeNetto) total_production
    //                 ,SUM(vsme.VolumeNetto) total_traceable_sme
    //                 ,COUNT( DISTINCT ksr.ParentID ) total_do
    //                 ,COUNT(DISTINCT CONCAT(st.SupplyID,st.PlantationNr)) plot_total
    //                 ,COUNT( DISTINCT st.SupplychainID ) total_agent
    //             FROM
    //                 ktv_tc_supplychain_batch sb
    //             LEFT JOIN 
    //                 view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyDestMillOrgID AND vso.ObjType = 'mill'
    //             LEFT JOIN
    //             (
    //                 SELECT
    //                         COUNT(ksr.ParentID) jml_do
    //                         , ksr.ParentID
    //                 FROM
    //                         ktv_tc_supplychain_org_rel ksr
    //                 GROUP BY
    //                         ksr.ParentID
    //             ) ksr ON ksr.ParentID = vso.SupplychainID AND vso.ObjType = 'mill'
    //             INNER JOIN
    //                 ktv_tc_supplychain_transaction st on st.SupplyBatchID = sb.SupplyBatchID AND st.StatusCode = 'active' AND st.SupplyType IN ('Farmer','Nonfarmer')
    //             LEFT JOIN
    //                 view_tc_supplychain_org vso2 on vso2.SupplychainID = st.SupplychainID
    //             LEFT JOIN 
    //                 ktv_members m ON m.MemberID = vso2.ObjID 
    //             LEFT JOIN 
    //             (
    //                 SELECT 
    //                     GROUP_CONCAT(mr.MRoleID) MRoleID
    //                     , mr.MemberID
    //                 FROM 
    //                     ktv_member_role mr
    //                 GROUP BY
    //                     mr.MemberID
    //                 ORDER BY
    //                     mr.MRoleID ASC
    //             ) mr ON m.MemberID = mr.MemberID
    //             LEFT JOIN
    //                 (
    //                     SELECT
    //                         SUM(st.VolumeNetto) VolumeNetto
    //                         , sb.SupplyDestMillOrgID
    //                     FROM
    //                         ktv_tc_supplychain_batch sb
    //                     LEFT JOIN 
    //                         view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyDestMillOrgID
    //                     LEFT JOIN ktv_mill mill on mill.MillID = vso.ObjID
    //                     LEFT JOIN ktv_village vil ON mill.VillageID = vil.VillageID
    //                     LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
    //                     LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
    //                     LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
    //                     INNER JOIN 
    //                         ktv_tc_supplychain_transaction st ON st.SupplyBatchID = sb.SupplyBatchID AND st.SupplyType IN ('Farmer','NonFarmer')
    //                     WHERE
    //                         1 = 1 
    //                     $sqlwhere
    //                     AND 
    //                         sb.SupplyBatchStatus IN ('Sent','Open')
    //                     AND
    //                         sb.SupplyBatchDate BETWEEN '$awal' AND '$akhir'
    //                     GROUP BY
    //                         sb.SupplyDestMillOrgID
    //                 ) vsme on vsme.SupplyDestMillOrgID = sb.SupplyDestMillOrgID
    //             WHERE
    //                 1 = 1 
    //                 $sqlwhere
    //             AND
    //                 sb.SupplyBatchStatus IN ('Delivered')
    //             AND
    //                 sb.SupplyBatchDate BETWEEN '$awal' AND '$akhir'
    //             AND 
    //                 ( find_in_set(11,mr.MRoleID))
    //         ) dash
    //         -- ORDER BY mr.MRoleID;";
    //     $data = $this->db->query($sql);
       
    //     $do = 0;
    //     $batch = 0;
    //     $transaksi = 0;
    //     $t_mill = 0;
    //     $sme = 0;
    //     $plot = 0;
    //     $farmer = 0;
    //     $production = 0;

    //     if($data->num_rows()){
    //         $dt= $data->row();
    //         $do = $dt->total_do;
    //         $transaksi = $dt->total_transaction;
    //         $batch = $dt->total_batch;
    //         $t_mill = $dt->total_mill;
    //         $sme = $dt->total_agent;
    //         $plot = $dt->plot_total;
    //         $farmer = $dt->total_farmer;
    //         $production = ($dt->total_production/1000);
    //         $traceable_volume_sme   = ROUND((float)$dt->total_traceable_sme / 1000, 2);
    //         $traceable_volume_mill  = ROUND((float)$dt->total_production / 1000, 2);
    //     }
    //     $sql2 = "SELECT
    //                 SUM(st.VolumeNetto) VolumeNetto
    //                 , sb.SupplyDestMillOrgID
    //             FROM
    //                 ktv_tc_supplychain_batch sb
    //             LEFT JOIN 
    //                 view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyDestMillOrgID
    //             LEFT JOIN ktv_mill mill on mill.MillID = vso.ObjID
    //             LEFT JOIN ktv_village vil ON mill.VillageID = vil.VillageID
    //             LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
    //             LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
    //             LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID
    //             INNER JOIN 
    //                 ktv_tc_supplychain_transaction st ON st.SupplyBatchID = sb.SupplyBatchID
    //             WHERE
    //                 1 = 1 
    //             $sqlwhere 
    //             AND 
    //                 sb.SupplyBatchStatus IN ('Sent','Open')
    //             AND
    //                 sb.SupplyBatchDate BETWEEN '$awal' AND '$akhir'
    //             GROUP BY
    //                 sb.SupplyDestMillOrgID";
    //     $data2 = $this->db->query($sql2);
       
    //     if($data2->num_rows()>0){
    //         $traceable_volume_sme = ROUND((float)$data2->row()->VolumeNetto / 1000, 2);
    //     }
    //     $total_traceable_sales  = ROUND((float)($traceable_volume_mill + $traceable_volume_sme));
    //     // $sql2 = "SELECT
    //     //         COUNT(DISTINCT vso2.SupplychainID) total_do
    //     //     FROM
    //     //         view_tc_supplychain_org vso
    //     //     LEFT JOIN
    //     //         ktv_tc_supplychain_org_rel orel on orel.ParentID = vso.SupplychainID AND NOW() BETWEEN orel.StartDate AND orel.EndDate AND orel.StatusCode = 'active'
    //     //     INNER JOIN
    //     //         view_tc_supplychain_org vso2 on vso2.SupplychainID = orel.ChildID AND vso2.ObjType = 'agent'
    //     //     WHERE
    //     //         1=1
    //     //         $sqlwhere2
    //     //     AND
    //     //         vso.ObjType = 'mill'
    //     // ";
    //     // $query = $this->db->query($sql2);
    //     // if($query->num_rows()>0){
    //     //     $row2 = $query->row();
    //     //     $do = $row2->total_do;
    //     //     $sme = $row2->total_do;
    //     // }

    //     $results['do'] = $do;
    //     $results['batch'] = $batch;
    //     $results['transaksi'] = $transaksi;
    //     $results['mill'] = $t_mill;
    //     $results['sme'] = $sme;
    //     $results['plot'] = $plot;
    //     $results['farmer'] = $farmer;
    //     $results['production'] = $production;

    //     /* annual potential */
    //     $annual_potential = $this->db->query($this->getAnnualPotential($prov, $awal, $akhir, $mill));
        

    //     // echo "<pre>";
    //     // print_r($this->db->last_query());
    //     // die;
    //     $annual = 0;
    //     if($annual_potential->num_rows()){
    //         foreach($annual_potential->result() as $ap){
    //             $annual += $ap->AnnualProduction;
    //         }
    //     }

    //     $results['potential_annual'] = array('data' => array(array('name' => 'Potential Volume', 'data' => array(ROUND((float)$annual / 1000, 2))),
    //                                                          array('name' => 'Total Traceable Volume', 'data' => array($total_traceable_sales))),
    //                                          'judul' => lang('Potential Annual Production Compared to Real Sales'),
    //                                          'yjudul' => '',
    //                                          'label' => array('Volume (MT)')
    //                                         );
    //     /* traceable volume */
    //     $results['traceable_volume'] = array('data' => array(
    //                                                         array(
    //                                                             "name" => 'Traceable Volume at SME',
    //                                                             "y" => $traceable_volume_sme,
    //                                                             "sliced" => true,
    //                                                             "selected" => true
    //                                                         ),
    //                                                         array(
    //                                                             "name" => 'Traceable Volume at Mill',
    //                                                             "y" =>  $traceable_volume_mill
    //                                                         )
    //                                                     ),
    //                                          'judul' => lang('Traceable Volume at Different levels (t)'),
    //                                          'yjudul' => lang('Volume'),
    //                                          'label' => array('Volume')
    //                                         );
    //     /* jumlah penjualan */
    //         $traceability_sales_certified = "SELECT
    //                                                 IFNULL(Province,'-') AS label,
    //                                                 ktv_members.VillageID province_id,
    //                                                 SUM(ktv_members.total_farmer) AS farmer,
    //                                                 /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
    //                                                 IFNULL(SUM(a.VolumeNetto),0) AS netto,
    //                                                 /*SUM(VolumeNetto)*/ 0 AS netto_certified,
    //                                                 SUM(ktv_members.total_farmer) AS farmer_uncertified,
    //                                                 IFNULL(SUM(a.VolumeNetto),0) AS netto_uncertified
    //                                             FROM
    //                                                 ktv_tc_supplychain_transaction a
    //                                                 LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
    //                                                 /* LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )*/
    //                                                 LEFT JOIN (
    //                                                     SELECT
    //                                                         st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, vso.SupplychainID)) total_farmer, p.ProvinceID VillageID   
    //                                                     FROM
    //                                                         ktv_tc_supplychain_transaction st1
    //                                                         LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = st1.SupplyID 
    //                                                         AND st1.SupplyType = 'Batch'
    //                                                         LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID = sb2.SupplyBatchID
    //                                                         LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID = st2.SupplyID 
    //                                                         AND st2.SupplyType = 'Batch'
    //                                                         LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID = sb3.SupplyBatchID
    //                                                         LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st1.SupplyType='Farmer', st1.SupplyID, NULL)))
    //                                                         LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st1.SupplyType='Nonfarmer', st1.SupplyID, NULL)))
    //                                                         LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vso.VillageID)
    //                                                         LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    //                                                         LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
    //                                                         LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
    //                                                     WHERE
    //                                                         st1.SupplychainID IN ($_SESSION[PartnerID]) AND v.VillageID is not null AND st1.SupplyID > 0
    //                                                     GROUP BY
    //                                                         st1.SupplyTransID
    //                                                 ) ktv_members ON ktv_members.transid=a.SupplyTransID
    //                                                 LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(ktv_members.VillageID,1,2)
    //                                             WHERE
    //                                                 1=1 
    //                                                 AND a.SupplychainID IN ($_SESSION[PartnerID])
    //                                                 AND DateTransaction between '$awal 00:00:00' and '$akhir 23:59:59' ";

    //                             if($mill){
    //                                 if($mill == 'other'){
    //                                     $traceability_sales_certified .=" AND (a.SupplychainID IS NULL OR a.SupplychainID = 0)";
    //                                 }else{
    //                                     $traceability_sales_certified .=" AND a.SupplychainID=".$mill;
    //                                 }
    //                             }

    //                             if($do){
    //                                 $traceability_sales_certified .=" AND sb2.SupplyOrgID=".$do;
    //                             }

    //                             if($prov){
    //                                 $traceability_sales_certified .=" AND kp.ProvinceID=".$prov;
    //                             }

    //             $traceability_sales_certified .=" GROUP BY label ORDER BY label ";
    //     $query_certified  = $this->db->query($traceability_sales_certified);
    //     /*
    //     $results['jumlah_penjualan'] = array('data' => array(array('name' => 'Certified', 'data' => array(1,2,3,4)),
    //                                                          array('name' => 'Not Certified', 'data' => array(1,2,3,4))),
    //                                          'judul' => lang('Number of Farmer Sales Detail'),
    //                                          'yjudul' => '',
    //                                          'label' => array(array('a'), array('b'), 'c', 'd')
    //                                         );
    //     */
    //     $results['jumlah_penjualan']['certified'] = $query_certified->result_array();

    //     return $results;
    // }
    public function readDataTraceabilityNew($prov = '', $kab = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent='')
    {
        $sqlwhere   = "";
        $sqlwhere2  = "";
        if($_SESSION['is_admin'] == "1"){
            $sqlwhere = "";
            $sqlwhere2 = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sql = "SELECT
                    GROUP_CONCAT(vso.SupplychainID) SupplychainID
                FROM
                    ktv_access_partner_mill apm
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.ObjID = apm.apmiMillID AND vso.ObjType = 'mill'
                WHERE
                    apm.apmiPartnerID = ?";
            $query = $this->db->query($sql,array($_SESSION['PartnerID']));
            if($query->num_rows()>0){
                $row = $query->row();
                $sqlwhere   = "  AND a.SupplyDestMillOrgID IN ($row->SupplychainID) ";
                $sqlwhere2  = " AND vso.SupplychainID IN ($row->SupplychainID)";
            }
            if($_SESSION['PartnerID'] == 1){
                $sqlwhere = "";
                $sqlwhere2 = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlwhere   = " AND a.SupplyDestMillOrgID = '$_SESSION[SupplychainID]' ";
            $sqlwhere2  = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }


        if($prov != ''){
            $sqlwhere .= " AND prov.ProvinceID = '$prov'";
        }
        if($mill != ''){
            if($mill == "other"){
                $sqlwhere .= " AND (a.SupplyDestMillOrgID IS NULL OR a.SupplyDestMillOrgID = 0)";
                $sqlwhere2 .= " AND (vso.SupplychainID IS NULL OR vso.SupplychainID = 0)";
            }else{
                $sqlwhere .= " AND a.SupplyDestMillOrgID = '$mill'";
                $sqlwhere2 .= " AND vso.SupplychainID = '$mill'";
            }
        }
        if($do != ''){
            $sqlwhere .= " AND d.SupplychainID = '$do'";
        }
        // echo "<pre>";
        // print_r($sqlwhere);
        // die;
        $sql = "SELECT 
                    COUNT(d.SupplyTransID) AS total_transaction,
                    COUNT(c.SupplyOrgID) AS total_batch,
                    COUNT(e.MemberID) AS total_farmer,
                    SUM(b.Weight)/1000 AS total_production,
                    COUNT(vso.SupplychainID) AS total_agent,
                    COUNT(vso2.SupplychainID) AS total_do,
                    COUNT(d.PlantationNr) AS plot_total
                FROM `ktv_tc_supplychain_delivery` a
                LEFT JOIN ktv_tc_supplychain_delivery_detail b ON a.DeliveryID = b.DeliveryID
                LEFT JOIN ktv_tc_supplychain_batch c ON c.SupplyBatchID = b.SupplyBatchID 
                LEFT JOIN ktv_tc_supplychain_transaction d ON d.SupplyBatchID = c.SupplyBatchID
                LEFT JOIN ktv_members e ON e.MemberID = d.SupplyID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = a.SupplychainID AND vso.ObjType = 'agent'
                LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = a.SupplyDestDoOrgID AND vso2.ObjType = 'do'
                WHERE
                    1 = 1 
                $sqlwhere 
                AND a.DeliveryStatusID IN ('3','4','5')";
        $data = $this->db->query($sql);
       
        $do = 0;
        $batch = 0;
        $transaksi = 0;
        $t_mill = 0;
        $sme = 0;
        $plot = 0;
        $farmer = 0;
        $production = 0;

        if($data->num_rows()){
            $dt= $data->row();
            $do = $dt->total_do;
            $transaksi = $dt->total_transaction;
            $batch = $dt->total_batch;
            $t_mill = $dt->total_mill;
            $sme = $dt->total_agent;
            $plot = $dt->plot_total;
            $farmer = $dt->total_farmer;
            $production = ($dt->total_production/1000);
            $traceable_volume_sme   = ROUND((float)$dt->total_traceable_sme / 1000, 2);
            $traceable_volume_mill  = ROUND((float)$dt->total_production / 1000, 2);
        }
        $sql2 = "SELECT 
                    SUM(d.VolumeNetto) AS VolumeNetto
                FROM `ktv_tc_supplychain_delivery` a
                LEFT JOIN ktv_tc_supplychain_delivery_detail b ON a.DeliveryID = b.DeliveryID
                LEFT JOIN ktv_tc_supplychain_batch c ON c.SupplyBatchID = b.SupplyBatchID 
                LEFT JOIN ktv_tc_supplychain_transaction d ON d.SupplyBatchID = c.SupplyBatchID
                LEFT JOIN ktv_members e ON e.MemberID = d.SupplyID
                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = a.SupplychainID AND vso.ObjType = 'agent'
                LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = a.SupplyDestDoOrgID AND vso2.ObjType = 'do'
                WHERE
                    1 = 1 
                $sqlwhere 
                AND a.DeliveryStatusID IN ('3','4','5')
                AND
                    a.DeliveryDate BETWEEN '$awal' AND '$akhir'
                GROUP BY
                    a.SupplyDestMillOrgID";
        $data2 = $this->db->query($sql2);
       
        if($data2->num_rows()>0){
            $traceable_volume_sme = ROUND((float)$data2->row()->VolumeNetto / 1000, 2);
        }
        $total_traceable_sales  = ROUND((float)($traceable_volume_mill + $traceable_volume_sme));
        $sql2 = "SELECT
                COUNT(DISTINCT vso2.SupplychainID) total_do
            FROM
                view_tc_supplychain_org vso
            LEFT JOIN
                ktv_tc_supplychain_org_rel orel on orel.ParentID = vso.SupplychainID AND NOW() BETWEEN orel.StartDate AND orel.EndDate AND orel.StatusCode = 'active'
            INNER JOIN
                view_tc_supplychain_org vso2 on vso2.SupplychainID = orel.ChildID AND vso2.ObjType = 'agent'
            WHERE
                1=1
                $sqlwhere2
            AND
                vso.ObjType = 'mill'
        ";
        $query = $this->db->query($sql2);
        if($query->num_rows()>0){
            $row2 = $query->row();
            $do = $row2->total_do;
            $sme = $row2->total_do;
        }

        $results['do'] = $do;
        $results['batch'] = $batch;
        $results['transaksi'] = $transaksi;
        $results['mill'] = $t_mill;
        $results['sme'] = $sme;
        $results['plot'] = $plot;
        $results['farmer'] = $farmer;
        $results['production'] = $production;

        /* annual potential */
        $annual_potential = $this->db->query($this->getAnnualPotential($prov, $awal, $akhir, $mill));
        

        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;
        $annual = 0;
        if($annual_potential->num_rows()){
            foreach($annual_potential->result() as $ap){
                $annual += $ap->AnnualProduction;
            }
        }

        $results['potential_annual'] = array('data' => array(array('name' => 'Potential Volume', 'data' => array(ROUND((float)$annual / 1000, 2))),
                                                             array('name' => 'Total Traceable Volume', 'data' => array($total_traceable_sales))),
                                             'judul' => lang('Potential Annual Production Compared to Real Sales'),
                                             'yjudul' => '',
                                             'label' => array('Volume (MT)')
                                            );
        /* traceable volume */
        $results['traceable_volume'] = array('data' => array(
                                                            array(
                                                                "name" => 'Traceable Volume at SME',
                                                                "y" => $traceable_volume_sme,
                                                                "sliced" => true,
                                                                "selected" => true
                                                            ),
                                                            array(
                                                                "name" => 'Traceable Volume at Mill',
                                                                "y" =>  $traceable_volume_mill
                                                            )
                                                        ),
                                             'judul' => lang('Traceable Volume at Different levels (t)'),
                                             'yjudul' => lang('Volume'),
                                             'label' => array('Volume')
                                            );
        /* jumlah penjualan */
            $traceability_sales_certified = "SELECT
                                                    IFNULL(Province,'-') AS label,
                                                    ktv_members.VillageID province_id,
                                                    SUM(ktv_members.total_farmer) AS farmer,
                                                    /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto,
                                                    /*SUM(VolumeNetto)*/ 0 AS netto_certified,
                                                    SUM(ktv_members.total_farmer) AS farmer_uncertified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto_uncertified
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                                    /* LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )*/
                                                    LEFT JOIN (
                                                        SELECT
                                                            st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, vso.SupplychainID)) total_farmer, p.ProvinceID VillageID   
                                                        FROM
                                                            ktv_tc_supplychain_transaction st1
                                                            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = st1.SupplyID 
                                                            AND st1.SupplyType = 'Batch'
                                                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID = sb2.SupplyBatchID
                                                            LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID = st2.SupplyID 
                                                            AND st2.SupplyType = 'Batch'
                                                            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID = sb3.SupplyBatchID
                                                            LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st1.SupplyType='Farmer', st1.SupplyID, NULL)))
                                                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st1.SupplyType='Nonfarmer', st1.SupplyID, NULL)))
                                                            LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vso.VillageID)
                                                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                                                            LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                                                            LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                                                        WHERE
                                                            st1.SupplychainID IN ($_SESSION[PartnerID]) AND v.VillageID is not null AND st1.SupplyID > 0
                                                        GROUP BY
                                                            st1.SupplyTransID
                                                    ) ktv_members ON ktv_members.transid=a.SupplyTransID
                                                    LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(ktv_members.VillageID,1,2)
                                                WHERE
                                                    1=1 
                                                    AND a.SupplychainID IN ($_SESSION[PartnerID])
                                                    AND DateTransaction between '$awal 00:00:00' and '$akhir 23:59:59' ";

                                if($mill){
                                    if($mill == 'other'){
                                        $traceability_sales_certified .=" AND (a.SupplychainID IS NULL OR a.SupplychainID = 0)";
                                    }else{
                                        $traceability_sales_certified .=" AND a.SupplychainID=".$mill;
                                    }
                                }

                                if($do){
                                    $traceability_sales_certified .=" AND sb2.SupplyOrgID=".$do;
                                }

                                if($prov){
                                    $traceability_sales_certified .=" AND kp.ProvinceID=".$prov;
                                }

                $traceability_sales_certified .=" GROUP BY label ORDER BY label ";
        $query_certified  = $this->db->query($traceability_sales_certified);
        /*
        $results['jumlah_penjualan'] = array('data' => array(array('name' => 'Certified', 'data' => array(1,2,3,4)),
                                                             array('name' => 'Not Certified', 'data' => array(1,2,3,4))),
                                             'judul' => lang('Number of Farmer Sales Detail'),
                                             'yjudul' => '',
                                             'label' => array(array('a'), array('b'), 'c', 'd')
                                            );
        */
        $results['jumlah_penjualan']['certified'] = $query_certified->result_array();

        return $results;
    }

    function readDataTraceabilityNew_lama($prov = '', $kab = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent='')
    {
        $mi1 = $mill=='' || $mill=='false' ? '/*' : '';
        $mi2 = $mill=='' || $mill=='false' ? '*/' : '';
        $do1 = $do=='' || $do=='false' ? '/*' : '';
        $do2 = $do=='' || $do=='false' ? '*/' : '';
        $ag1 = $agent=='' ? '/*' : '';
        $ag2 = $agent=='' ? '*/' : '';

        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, o.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_tc_supplychain_org o ON o.ObjID=s.ObjID AND o.ObjType=s.ObjType
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array(@$_SESSION['userid']))->result_array();
        $staff = @$query[0];
        $SupplychainID = @$staff['SupplychainID'];
        if($SupplychainID==''){
            //$sql = "SELECT GROUP_CONCAT(DISTINCT SupplychainID) SupplychainID FROM view_tc_supplychain_org WHERE PartnerID=? AND ObjType='mill'";
            $sql = "SELECT GROUP_CONCAT(DISTINCT vso.SupplychainID) SupplychainID
                    FROM
                        ktv_access_partner_mill a
                        LEFT JOIN ktv_mill b ON b.MillID = a.apmiMillID 
                        LEFT JOIN view_tc_supplychain_org vso ON vso.ObjID=a.apmiMillID AND vso.ObjType='mill'
                    WHERE
                        a.apmiPartnerID = ?
                        AND b.NDAAgree = 1 
                        AND b.StatusCode = 'active' AND vso.SupplychainID IS NOT NULL";
            $querym = $this->db->query($sql, array($_SESSION['PartnerID']));
            $SupplychainID = $querym->row()->SupplychainID;
        }

        $OrgType = @$staff['ObjType'];
        if($OrgType=='mill'){
            //$SupplychainID = '';
        }
        if($SupplychainID==''){
            $s1 = "/*"; $s2 = "*/";
        }else{
            $s1 = ""; $s2 = "";
        }
        
        if (@$petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif (@$petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';
        if ($prov == '') {
            $label = "IFNULL(Province,'-')";
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(ktv_members.VillageID,1,2)';
            $LEFT_agent = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(c.VillageID,1,2)';
            $LEFT_mill = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            //$where = 'and Province is not null';
            $where = '';
            $groupby = 'substr(ktv_members.VillageID,1,2)';
            $groupby_mill = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = "IFNULL(District,'-')";
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(ktv_members.VillageID,1,4)';
            $LEFT_agent = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(c.VillageID,1,4)';
            $LEFT_mill = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            //$where = 'and substr(ktv_members.VillageID,1,2)=? and District is not null';
            $where = 'and substr(ktv_members.VillageID,1,2)=?';
            $where_mill = 'and substr(VillageID,1,2)=? and District is not null';
            $groupby = 'substr(ktv_members.VillageID,1,4)';
            $groupby_mill = 'substr(VillageID,1,4)';
        } else {
            $label = "IFNULL(kp.SubDistrict,'-')";
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = ktv_members.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_agent = 'LEFT JOIN ktv_village kv ON kv.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_mill = 'LEFT JOIN ktv_village kv ON kv.VillageID = kv.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            //$where = 'and substr(ktv_members.VillageID,1,4)=? and kp.SubDistrict is not null';
            $where = 'and substr(ktv_members.VillageID,1,4)=?';
            $where_mill = 'and substr(kv.VillageID,1,4)=? and kp.SubDistrict is not null';
            $groupby = 'kp.SubDistrictID';
            $groupby_mill = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        if ($awal != '' and $akhir != '') {
            $between = " AND DateTransaction BETWEEN '{$awal} 00:00:00' AND '{$akhir} 23:59:59'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }
        $where_partner = '';
        if (!empty($traceability_partner)) {
            $partner = $this->getPartner($traceability_partner);
            if ($partner['FlagAccess'] == '1') {
                //$where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                //$where_partner = " AND SUBSTR(ktv_members.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }

        /*$query_cpg          = $this->db->query(sprintf($this->sql_kelompok, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_farmer       = $this->db->query(sprintf($this->demographic_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        $query_luas         = $this->db->query(sprintf($this->garden_luas, $label, $LEFT, $where.$where_partner . $qps, $groupby), array($prov));
        $query_produksi     = $this->db->query(sprintf($this->garden_produksi, $label, $LEFT, $where.$where_partner . $qps, $groupby), array($prov));*/

        //$query_traceability_farmer       = $this->db->query(sprintf($this->traceability_farmer, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        //$query_traceability_production   = $this->db->query(sprintf($this->traceability_production, $label, $LEFT, $where.$where_partner, $groupby), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if (!empty($traceability_partner)) {
            //$where_partner = " AND wh.PartnerID = {$traceability_partner}";
        }
        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',a.VolumeNetto,0)) AS sell_{$value['yearmonth']}
    ,COUNT(DISTINCT IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',SupplyID,NULL)) AS trans_{$value['yearmonth']}
                ";
            }
        }
        $this->traceability_total = "SELECT
                                        %s AS label,
                                        SUM(a.VolumeNetto) AS total_penjualan,
                                        COUNT(SupplyTransID) AS total_transaction,
                                        SUM(total_farmer) AS total_farmer_sell,
                                        SUM(a.VolumeNetto) AS total_sell,
                                        PERIOD_DIFF(DATE_FORMAT(max(DateTransaction),'%%Y%%m'),DATE_FORMAT(min(DateTransaction),'%%Y%%m')) + 1 bulan,
                                        min(date(DateTransaction)) date_min,
                                        max(date(DateTransaction)) date_max
                                        %s
                                    FROM
                                        ktv_tc_supplychain_transaction a
                                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                        LEFT JOIN ktv_members ON (a.SupplyID=ktv_members.MemberID ) AND a.SupplyType='Farmer'
                                        LEFT JOIN (
                                            SELECT
                                                st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, vso.SupplychainID)) total_farmer, p.ProvinceID VillageID   
                                            FROM
                                                ktv_tc_supplychain_transaction st1
                                                LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = st1.SupplyID 
                                                AND st1.SupplyType = 'Batch'
                                                LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID = sb2.SupplyBatchID
                                                LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID = st2.SupplyID 
                                                AND st2.SupplyType = 'Batch'
                                                LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID = sb3.SupplyBatchID
                                                LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st1.SupplyType='Farmer', st1.SupplyID, NULL)))
                                                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st1.SupplyType='Nonfarmer', st1.SupplyID, NULL)))
                                                LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vso.VillageID)
                                                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                                                LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                                                LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                                            WHERE
                                                st1.SupplychainID IN ($SupplychainID) AND v.VillageID is not null AND st1.SupplyID > 0
                                            GROUP BY
                                                st1.SupplyTransID
                                        ) fr ON fr.transid=a.SupplyTransID
                                        %s
                                    WHERE
                                        1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                        %s
                                        $mi1 AND a.SupplychainID=? $mi2
                                        $do1 AND sb2.SupplyOrgID=? $do2
                                    GROUP BY label";
        //echo $this->traceability_total; die;
        $query_total      = $this->db->query(sprintf($this->traceability_total, $label, $SELECT, $LEFT, $where . $between, $groupby), array($mill, $do));

        $this->traceability_total_farmer = "SELECT
                                        %s AS label,
                                        COUNT(DISTINCT fr.MemberID) AS total_farmer_sell
                                        %s
                                    FROM
                                        ktv_tc_supplychain_transaction a
                                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                        LEFT JOIN ktv_members ON (a.SupplyID=ktv_members.MemberID ) AND a.SupplyType='Farmer'
                                        LEFT JOIN (
                                            SELECT DISTINCT m.MemberID, st1.SupplyTransID AS transid
                                            FROM
                                                ktv_tc_supplychain_transaction st1
                                                LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = st1.SupplyID 
                                                AND st1.SupplyType = 'Batch'
                                                LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID = sb2.SupplyBatchID
                                                LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID = st2.SupplyID 
                                                AND st2.SupplyType = 'Batch'
                                                LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID = sb3.SupplyBatchID
                                                LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st1.SupplyType='Farmer', st1.SupplyID, NULL))) AND m.MemberID > 0
                                                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st1.SupplyType='Nonfarmer', st1.SupplyID, NULL)))
                                                LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vso.VillageID)
                                                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                                                LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                                                LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                                            WHERE
                                                st1.SupplychainID IN ($SupplychainID) AND v.VillageID is not null AND st1.SupplyID > 0
                                                AND m.MemberID IS NOT NULL
                                            GROUP BY
                                                st1.SupplyTransID
                                        ) fr ON fr.transid=a.SupplyTransID
                                        %s
                                    WHERE
                                        1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                        %s
                                        $mi1 AND a.SupplychainID=? $mi2
                                        $do1 AND sb2.SupplyOrgID=? $do2
                                    GROUP BY label";
        $query_total_farmer = $this->db->query(sprintf($this->traceability_total_farmer, $label, $SELECT, $LEFT, $where . $between, $groupby), array($mill, $do));

        $this->traceability_sales_certified = "SELECT
                                                    %s AS label,
                                                    ktv_members.VillageID province_id,
                                                    SUM(ktv_members.total_farmer) AS farmer,
                                                    /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto,
                                                    /*SUM(VolumeNetto)*/ 0 AS netto_certified,
                                                    SUM(ktv_members.total_farmer) AS farmer_uncertified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto_uncertified
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                                    /* LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )*/
                                                    LEFT JOIN (
                                                        SELECT
                                                            st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, vso.SupplychainID)) total_farmer, p.ProvinceID VillageID   
                                                        FROM
                                                            ktv_tc_supplychain_transaction st1
                                                            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = st1.SupplyID 
                                                            AND st1.SupplyType = 'Batch'
                                                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID = sb2.SupplyBatchID
                                                            LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID = st2.SupplyID 
                                                            AND st2.SupplyType = 'Batch'
                                                            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID = sb3.SupplyBatchID
                                                            LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st1.SupplyType='Farmer', st1.SupplyID, NULL)))
                                                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st1.SupplyType='Nonfarmer', st1.SupplyID, NULL)))
                                                            LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vso.VillageID)
                                                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                                                            LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                                                            LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                                                        WHERE
                                                            st1.SupplychainID IN ($SupplychainID) AND v.VillageID is not null AND st1.SupplyID > 0
                                                        GROUP BY
                                                            st1.SupplyTransID
                                                    ) ktv_members ON ktv_members.transid=a.SupplyTransID
                                                    %s
                                                WHERE
                                                    1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                    $mi1 AND a.SupplychainID=? $mi2
                                                    $do1 AND sb2.SupplyOrgID=? $do2
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_certified  = $this->db->query(sprintf($this->traceability_sales_certified, $label, $LEFT, $where, $awal, $akhir), array($mill, $do));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // $query_penjualan    = $this->db->query(sprintf($this->traceability_penjualan, $label, $LEFT, $where . $between, $groupby), array($prov));
        // $query_transaction  = $this->db->query(sprintf($this->traceability_transaction, $label, $LEFT, $where . $betweentrans, $groupby), array($prov));
        // $query_farmer_sell  = $this->db->query(sprintf($this->traceability_farmer_sell, $label, $LEFT, $where . $betweentrans, $groupby), array($prov));
        // $query_sell         = $this->db->query(sprintf($this->traceability_sell, $label, '%Y%m', '%Y%m', $LEFT, $where . $betweentrans, $groupby), array($prov));
        
        //$query_trader       = $this->db->query(sprintf($this->traceability_bu, $label, $LEFT, 'Pedagang', 'sce', $where, $groupby), array($prov));
        //$debug['trader'] = $this->db->last_query();
        //$query_koperasi     = $this->db->query(sprintf($this->traceability_bu, $label, $LEFT, 'Organisasi Petani', 'Organisasi Petani', $where, $groupby), array($prov));
        //$debug['koperasi'] = $this->db->last_query();
        //$query_warehouse    = $this->db->query(sprintf($this->traceability_bu, $label, $LEFT, 'Gudang', 'Gudang', $where, $groupby), array($prov));
        //$debug['warehouse'] = $this->db->last_query();
        
        $results['cpg']         = array(); //@$query_cpg->result_array();
        $results['farmer']      = array(); //@$query_farmer->result_array();
        $results['luas']        = array(); //@$query_luas->result_array();
        $results['produksi']    = array(); //@$query_produksi->result_array();

        $results['traceability_farmer']         = array(); //@$query_traceability_farmer->result_array();
        $results['traceability_production']     = array(); //@$query_traceability_production->result_array();
        
        $results['total']        = $query_total->result_array();
        $results['total_farmer'] = $query_total_farmer->result_array();
        $results['certified']    = $query_certified->result_array();
        /*$sql1 = "SELECT COUNT(DISTINCT dt.farmer) farmer FROM
                (SELECT 
                    DISTINCT IFNULL(m.MemberID, NULL) farmer, 
                    p.ProvinceID
                FROM
                    ktv_tc_supplychain_transaction st1
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID = st1.SupplyID 
                    AND st1.SupplyType = 'Batch'
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID = sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID = st2.SupplyID 
                    AND st2.SupplyType = 'Batch'
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID = sb3.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st1.SupplyType='Farmer', st1.SupplyID, NULL)))
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st1.SupplyType='Nonfarmer', st1.SupplyID, NULL)))
                    LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vso.VillageID)
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID 
                WHERE
                    st1.SupplychainID IN ($SupplychainID) AND v.VillageID is not null AND st1.SupplyID > 0 AND m.MemberID IS NOT NULL
                    AND p.ProvinceID=?) dt";
        foreach($results['certified'] as $k=>$v){
            $total_farmer = $this->db->query($sql1, array($v['province_id']))->row()->farmer;
            if($total_farmer!=''){
                $results['certified'][$k]['farmer'] = $total_farmer;
                $results['certified'][$k]['farmer_uncertified'] = $total_farmer;
            }
        }*/
        $results['trader']          = array(); //$query_trader->result_array();
        $results['koperasi']        = array(); //$query_koperasi->result_array();
        $results['warehouse']       = array(); //$query_warehouse->result_array();
        
        /**Start**/
        $this->traceability_agent = "SELECT
                                                    %s AS label,
                                                    COUNT(DISTINCT c.SupplychainID) total
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch b ON a.SupplyID=b.SupplyBatchID AND a.SupplyType='Batch'
                                                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID=IFNULL(b.SupplyOrgID, IF(a.DOID > 0 , a.DOID, IF(a.AgentID > 0, a.AgentID, NULL)))
                                                    %s
                                                WHERE
                                                    a.SupplyType='Batch' $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_agent = $this->db->query(sprintf($this->traceability_agent, $label, $LEFT_agent, $where, $awal, $akhir, $groupby), array($prov));
        
        $checkingObjType = $this->db->query("SELECT ObjType FROM view_tc_supplychain_org WHERE SupplychainID='$SupplychainID' ")->row()->ObjType;
        if(empty($query_agent->result_array()) AND $checkingObjType=="agent"){
            $results['agent'][] = array(
                'label' => '-',
                'total' => 1);
        }else{
            $results['agent'] = $query_agent->result_array();
        }

        $debug['agent'] = $this->db->last_query();
        
        
        $query_mill = $this->db->query(sprintf($this->traceability_mill, $label, $LEFT_mill, @$where_mill, $groupby_mill), array($prov));
        $debug['mill'] = $this->db->last_query();
        $results['mill'] = $query_mill->result_array();
        /**End**/
        
        $results['months']          = $query_months->result_array();
        $results['debug']           = $debug;
        //echo "<pre>".print_r($debug['agent'],1);
        //echo "<pre>".print_r($results['agent'],1);exit;
        //echo '<pre>'.print_r($results, 1);die;
        return $results;
    }

    function readDataDistrictTraceability($user, $district, $priv = '', $awal = '', $akhir = '', $partner = '', $prov = '', $traceability_partner = '')
    {
        $debug = array();
        $where = '';
        $LEFT = '';
        $where .= ' and substr(ktv_members.VillageID,1,4) in (%s)';
        $where_private = '';
        if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
            //$where_private .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        }
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(ktv_members.VillageID,1,2)';
            $groupby = 'substr(ktv_members.VillageID,1,2)';
        } else {
            $where .= ' and substr(ktv_members.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(ktv_members.VillageID,1,4)';

                $groupby = 'substr(ktv_members.VillageID,1,4)';
            } else {
                $label = 'kp.SubDistrict';
                $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = ktv_members.VillageID
                LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
                // $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = SubDistrictID';
                $where .= ' and substr(ktv_members.VillageID,1,4)=? and kp.SubDistrict is not null';
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
                //$where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                $where_partner = " AND SUBSTR(ktv_members.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }

        if (@$petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif (@$petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';

        if ($awal != '' and $akhir != '') {
            $between = " AND DateTransaction BETWEEN '{$awal}' AND '{$akhir}'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }

        /*$query_cpg = $this->db->query(sprintf(sprintf($this->sql_kelompok, $label, $LEFT, $where.$where_private, $groupby), implode(',', $dist)), array($priv));
        $query_farmer = $this->db->query(sprintf(sprintf($this->demographic_farmer, $label, $LEFT, $where.$where_private, $groupby), implode(',', $dist)), array($priv));
        $query_luas = $this->db->query(sprintf(sprintf($this->garden_luas, $label, $LEFT, $where.$where_private . $qps, $groupby), implode(',', $dist)), array($priv));
        $query_produksi = $this->db->query(sprintf(sprintf($this->garden_produksi, $label, $LEFT, $where.$where_private . $qps, $groupby), implode(',', $dist)), array($priv));*/

        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(DateTransaction,'%%Y%%m')='{$value['yearmonth']}',FAQVolumeBruto,0)) AS sell_{$value['yearmonth']}
    ,COUNT(DISTINCT IF(DATE_FORMAT(DateTransaction,'%%Y%%m')='{$value['yearmonth']}',MemberID,NULL)) AS trans_{$value['yearmonth']}
                ";
            }
        }
        // echo '<pre>'; print_r(sprintf(str_replace('%%', '%%%%', $this->traceability_total), $label, $LEFT, $where.$where_private . $between, $groupby)); echo '</pre>'; exit;
        $query_total = $this->db->query(sprintf(sprintf(str_replace('%%', '%%%%', $this->traceability_total), $label, $SELECT, $LEFT, $where. $between, $groupby),
            implode(',', $dist)), array($priv));

        $query_certified = $this->db->query(sprintf(sprintf($this->traceability_sales_certified, $label, $LEFT, $where, $awal, $akhir),
            implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // $query_penjualan = $this->db->query(sprintf(sprintf($this->traceability_penjualan, $label, $LEFT, $where . $between, $groupby),
        //     implode(',', $dist)), array($priv));
        // $query_transaction = $this->db->query(sprintf(sprintf($this->traceability_transaction, $label, $LEFT, $where . $betweentrans, $groupby),
        //     implode(',', $dist)), array($priv));
        // $query_farmer_sell = $this->db->query(sprintf(sprintf($this->traceability_farmer_sell, $label, $LEFT, $where . $betweentrans, $groupby),
        //     implode(',', $dist)), array($priv));
        // $query_sell = $this->db->query(sprintf(sprintf($this->traceability_sell, $label, '%s', '%s', $LEFT, $where . $betweentrans, $groupby),
        //     '%Y%m', '%Y%m', implode(',', $dist)), array($priv));
        // echo '<pre>'; print_r($where); echo '</pre>'; exit;
        //$query_trader = $this->db->query(sprintf(sprintf($this->traceability_bu, $label, $LEFT, 'Pedagang', 'sce', $where, $groupby), implode(',', $dist)), array($priv));
        // $debug['trader'] = $this->db->last_query();
        //$query_koperasi = $this->db->query(sprintf(sprintf($this->traceability_bu, $label, $LEFT, 'Organisasi Petani', 'Organisasi Petani', $where, $groupby), implode(',', $dist)), array($priv));
        // $debug['koperasi'] = $this->db->last_query();
        //$query_warehouse = $this->db->query(sprintf(sprintf($this->traceability_bu, $label, $LEFT, 'Gudang', 'Gudang', $where, $groupby), implode(',', $dist)), array($priv));
        // $debug['warehouse'] = $this->db->last_query();

        $results['cpg']             = array(); //$query_cpg->result_array();
        $results['farmer']          = array(); //$query_farmer->result_array();
        $results['luas']            = array(); //$query_luas->result_array();
        $results['produksi']        = array(); //$query_produksi->result_array();

        $results['total']           = $query_total->result_array();
        $results['certified']       = $query_certified->result_array();
        // $results['sell']         = $query_sell->result_array();
        // $results['transaction']  = $query_transaction->result_array();
        // $results['farmer_sell']  = $query_farmer_sell->result_array();
        // $results['penjualan']    = $query_penjualan->result_array();
        /**Start**/
        $query_agent = $this->db->query(sprintf($this->traceability_agent, $label, $LEFT, $where, $groupby), array($prov));
        $debug['agent'] = $this->db->last_query();
        $results['agent'] = $query_agent->result_array();
        
        $query_mill = $this->db->query(sprintf($this->traceability_mill, $label, $LEFT, $where, $groupby), array($prov));
        $debug['mill'] = $this->db->last_query();
        $results['mill'] = $query_mill->result_array();
        /**End**/
        $results['trader']          = array(); //$query_trader->result_array();
        $results['koperasi']        = array(); //$query_koperasi->result_array();
        $results['warehouse']       = array(); //$query_warehouse->result_array();
        $results['months']          = $query_months->result_array();
        // $results['debug']           = $debug;
        return $results;
    }
    
    public function _getUserDetail(){
        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, o.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_tc_supplychain_org o ON o.ObjID=s.ObjID AND o.ObjType=s.ObjType
                    /* LEFT JOIN ktv_tc_supplychain_org_partner op ON op.SupplychainID=o.SupplychainID */
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
        $this->SupplychainID = $staff['SupplychainID'];
        $this->OrgID = $staff['ObjID'];
        $this->OrgType = $staff['ObjType'];
    }

    public function lisGroupMillRefinery(){
        $sql = "SELECT
                    m.MillID id
                    , m.MillName name
                FROM
                    ktv_tc_supplychain_org_rel orel
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = orel.ChildID
                LEFT JOIN
                    ktv_mill m on m.MillID = vso.ObjID
                LEFT JOIN
                    ktv_mill_group mg on mg.MillGroupID = m.MillGroupID
                WHERE
                    mg.StatusCode = 'active'
                    AND 
                    orel.ParentID = ?
                ";
        $query = $this->db->query($sql, array($_SESSION["SupplychainID"]));
        
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function listMillFA(){
        // echo "<pre>";
        // print_r($_SESSION);
        // die;
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND m.PartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
            if($_SESSION['group'] == "Field Agent"){
                $sqlHakAksesPartner = " AND m.MillID = '$_SESSION[MillID]'";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }

        $sql = "SELECT
                m.MillID id
                , m.MillName name
                , m.PartnerID
            FROM
                ktv_mill m
            LEFT JOIN
                view_tc_supplychain_org vso on vso.ObjID = m.MillID AND vso.ObjType = 'mill'
            WHERE
                m.StatusCode = 'active'
                $sqlHakAksesPartner
            GROUP BY
                m.MillID
            ORDER BY
                m.MillName";
        $query = $this->db->query($sql);
        // echo "<pre>";
        // print_r($_SESSION);
        // die;
        return $query->result_array();
    }

    public function listMillRefinery(){
        $where = "";
        
        if(isset($_GET["MillGroupID"]) && $_GET["MillGroupID"] != ''){
            $where .= " AND m.MillGroupID = '$_GET[MillGroupID]'";
        }
        $sql = "SELECT
                m.MillID id
                , m.MillName name
            FROM
                ktv_tc_supplychain_org_rel orel
            LEFT JOIN
                view_tc_supplychain_org vso on vso.SupplychainID = orel.ChildID
            LEFT JOIN
                ktv_mill m on m.MillID = vso.ObjID
            LEFT JOIN
                ktv_mill_group mg on mg.MillGroupID = m.MillGroupID
            WHERE
                m.StatusCode = 'active'
                AND orel.ParentID = ? 
                $where
            GROUP BY
                m.MillID";
        $query = $this->db->query($sql, array($_SESSION["SupplychainID"]));
        
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    
    public function listGroupMill()
    {
        $sql = "SELECT
                    mg.MillGroupID id,
                    mg.GroupName name 
                FROM
                    ktv_mill_group mg
                LEFT JOIN
                    ktv_mill m on m.MillGroupID = mg.MillGroupID
                WHERE
                    mg.StatusCode = 'active'
                    AND m.PartnerID = ?";
        $query = $this->db->query($sql, array($_SESSION["PartnerID"]));
        
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function listMill()
    {

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND m.StatusCode = 'active' AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
            $sqlHakAksesPartnerMill = " AND m.StatusCode = 'active' AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND m.StatusCode = 'active' AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }

        $where = "";
        if(isset($_GET["province"]) && $_GET["province"] != ''){
            $where .= " AND p.ProvinceID = '$_GET[province]'";
        }
        if(isset($_GET["MillGroupID"]) && $_GET["MillGroupID"] != ''){
            $where .= " AND m.MillGroupID = '$_GET[MillGroupID]'";
        }
        $sql = "SELECT
                    vso.SupplychainID id,
                    m.MillName name,
                    apm.apmiPartnerID
                FROM
                    ktv_mill m
                    LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
                    INNER JOIN ktv_access_partner_mill apm on apm.apmiMillID = m.MillID
                    LEFT JOIN view_tc_supplychain_org vso on vso.ObjID = m.MillID
                    INNER JOIN ktv_tc_supplychain_batch sb on ( sb.SupplyDestMillOrgID = vso.SupplychainID OR sb.SupplyDestOrgID = vso.SupplychainID) AND sb.StatusCode ='active'
                WHERE
                    1 = 1
                    $sqlHakAksesPartner
                    $where
                AND
                    vso.SupplychainID IS NOT NULL
                GROUP BY m.MillID
                ORDER BY
                    m.MillName ASC";
        $query = $this->db->query($sql, array($_SESSION["PartnerID"]));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    
    public function listDO($mill)
    {
        //$this->_getUserDetail();
        $sql = "SELECT  vso.SupplychainID id, vso.`Name` name
                    FROM ktv_tc_supplychain_org_rel orel LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=orel.ChildID
                    WHERE orel.ParentID=? AND orel.ParentID!='' AND orel.StatusCode='active' AND vso.SupplychainID IS NOT NULL";
        $query = $this->db->query($sql, array($mill));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    
    public function listAgent($mill, $do)
    {
        $this->_getUserDetail();
        $sql = "SELECT  vso.SupplychainID id, vso.`Name` name
                FROM ktv_tc_supplychain_org_rel orel
                    LEFT JOIN ktv_tc_supplychain_org_rel orel2 ON orel2.ParentID=orel.ChildID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=orel2.ChildID
                WHERE orel.ParentID=? AND orel.ParentID!='' AND orel2.ParentID=? AND orel.StatusCode='active' AND orel2.StatusCode='active' AND vso.SupplychainID IS NOT NULL";
        //$query = $this->db->query($sql, array($mill, $do));
        $sql = "SELECT  vso.SupplychainID id, vso.`Name` name
                    FROM ktv_tc_supplychain_org_rel orel LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=orel.ChildID
                    WHERE orel.ParentID=? AND orel.ParentID!='' AND orel.StatusCode='active' AND vso.SupplychainID IS NOT NULL";
        $query = $this->db->query($sql, array($do));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
    public function readDataSupplyChainTraceability($prov = '', $kab = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent=''){
        $result['dataDisplay']['sales'] = 0;
        $result['dataDisplay']['farmersales'] = 0;
        $result['dataDisplay']['nrtrnsct'] = 0;
        $result['dataDisplay']['agentsales'] = 0;
        $result['dataDisplay']['farmernosales'] = 0;
        $result['dataDisplay']['production'] = 100000;
        $result['dataDisplay']['distance'] = 12;
        $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');

        $result['dataChart']['production'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['production'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['production'][] = array("label"=>"test3", "total"=>0);
        
        $result['dataChart']['traceablesales'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['traceablesales'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['traceablesales'][] = array("label"=>"test3", "total"=>0);

        $result['dataChart']['nrfarmertrnsct'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['nrfarmertrnsct'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['nrfarmertrnsct'][] = array("label"=>"test3", "total"=>0);
        
        $result['dataChart']['nrfarmersales'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['nrfarmersales'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['nrfarmersales'][] = array("label"=>"test3", "total"=>0);
        
        $result['dataChart']['average_distance'] = [
            ['label' => '< 5', 'total' => 0],
            ['label' => '5 - 10', 'total' => 0],
            ['label' => '10 - 20', 'total' => 0],
            ['label' => '20 - 50', 'total' => 0],
            ['label' => '> 50', 'total' => 0],
        ];

        return $result;
    }
    public function readDataSupplyChainMillTraceability($millgroup = '', $mill = '', $awal = '', $akhir = ''){

       

        $result['dataDisplay']['sales'] = 0;
        $result['dataDisplay']['farmersales'] = 0;
        $result['dataDisplay']['nrtrnsct'] = 0;
        $result['dataDisplay']['agentsales'] = 0;
        $result['dataDisplay']['farmernosales'] = 0;
        $result['dataDisplay']['production'] = 100000;
        $result['dataDisplay']['distance'] = 12;
        $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');

        $result['dataChart']['production'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['production'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['production'][] = array("label"=>"test3", "total"=>0);
        
        $result['dataChart']['traceablesales'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['traceablesales'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['traceablesales'][] = array("label"=>"test3", "total"=>0);

        $result['dataChart']['nrfarmertrnsct'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['nrfarmertrnsct'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['nrfarmertrnsct'][] = array("label"=>"test3", "total"=>0);
        
        $result['dataChart']['nrfarmersales'][] = array("label"=>"test1", "total"=>0);
        $result['dataChart']['nrfarmersales'][] = array("label"=>"test2", "total"=>0);
        $result['dataChart']['nrfarmersales'][] = array("label"=>"test3", "total"=>0);

        $result['dataChart']['average_distance'] = [
            ['label' => '< 5', 'total' => 0],
            ['label' => '5 - 10', 'total' => 0],
            ['label' => '10 - 20', 'total' => 0],
            ['label' => '20 - 50', 'total' => 0],
            ['label' => '> 50', 'total' => 0],
        ];

        return $result;
    }

    function readDataTraceabilityMillNew($millgroup = '', $mill = '', $awal = '', $akhir = '')
    {
        $mi1 = $mill=='' || $mill=='false' ? '/*' : '';
        $mi2 = $mill=='' || $mill=='false' ? '*/' : '';
        $miGorup1   = $millgroup=='' || $millgroup=='false' ? '/*' : '';
        $miGorup12 = $millgroup=='' || $millgroup=='false' ? '*/' : '';
        $do1 = $do=='' || $do=='false' ? '/*' : '';
        $do2 = $do=='' || $do=='false' ? '*/' : '';
        $ag1 = $agent=='' ? '/*' : '';
        $ag2 = $agent=='' ? '*/' : '';
        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, o.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_tc_supplychain_org o ON o.ObjID=s.ObjID AND o.ObjType=s.ObjType
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array(@$_SESSION['userid']))->result_array();
        $staff = @$query[0];
        $SupplychainID = @$staff['SupplychainID'];
        if($SupplychainID==''){
            //$sql = "SELECT GROUP_CONCAT(DISTINCT SupplychainID) SupplychainID FROM view_tc_supplychain_org WHERE PartnerID=? AND ObjType='mill'";
            $sql = "SELECT GROUP_CONCAT(DISTINCT vso.SupplychainID) SupplychainID
                    FROM
                        ktv_access_partner_mill a
                        LEFT JOIN ktv_mill b ON b.MillID = a.apmiMillID 
                        LEFT JOIN view_tc_supplychain_org vso ON vso.ObjID=a.apmiMillID AND vso.ObjType='mill'
                    WHERE
                        a.apmiPartnerID = ?
                        AND b.NDAAgree = 1 
                        AND b.StatusCode = 'active' AND vso.SupplychainID IS NOT NULL";
            $querym = $this->db->query($sql, array($_SESSION['PartnerID']));
            $SupplychainID = $querym->row()->SupplychainID;
        }

        $OrgType = @$staff['ObjType'];
        if($OrgType=='mill'){
            //$SupplychainID = '';
        }
        if($SupplychainID==''){
            $s1 = "/*"; $s2 = "*/";
        }else{
            $s1 = ""; $s2 = "";
        }
        
        if (@$petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif (@$petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';

        if ($prov == '') {
            $label = "IFNULL(Province,'-')";
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(ktv_members.VillageID,1,2)';
            $LEFT_agent = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(c.VillageID,1,2)';
            $LEFT_mill = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            //$where = 'and Province is not null';
            $where = '';
            $groupby = 'substr(ktv_members.VillageID,1,2)';
            $groupby_mill = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = "IFNULL(District,'-')";
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(ktv_members.VillageID,1,4)';
            $LEFT_agent = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(c.VillageID,1,4)';
            $LEFT_mill = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            //$where = 'and substr(ktv_members.VillageID,1,2)=? and District is not null';
            $where = 'and substr(ktv_members.VillageID,1,2)=?';
            $where_mill = 'and substr(VillageID,1,2)=? and District is not null';
            $groupby = 'substr(ktv_members.VillageID,1,4)';
            $groupby_mill = 'substr(VillageID,1,4)';
        } else {
            $label = "IFNULL(kp.SubDistrict,'-')";
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = ktv_members.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_agent = 'LEFT JOIN ktv_village kv ON kv.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_mill = 'LEFT JOIN ktv_village kv ON kv.VillageID = kv.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            //$where = 'and substr(ktv_members.VillageID,1,4)=? and kp.SubDistrict is not null';
            $where = 'and substr(ktv_members.VillageID,1,4)=?';
            $where_mill = 'and substr(kv.VillageID,1,4)=? and kp.SubDistrict is not null';
            $groupby = 'kp.SubDistrictID';
            $groupby_mill = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        if ($awal != '' and $akhir != '') {
            $between = " AND DateTransaction BETWEEN '{$awal} 00:00:00' AND '{$akhir} 23:59:59'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }
        $where_partner = '';
        if (!empty($traceability_partner)) {
            $partner = $this->getPartner($traceability_partner);
            if ($partner['FlagAccess'] == '1') {
                //$where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                //$where_partner = " AND SUBSTR(ktv_members.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }


        if (!empty($traceability_partner)) {
            //$where_partner = " AND wh.PartnerID = {$traceability_partner}";
        }
        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',a.VolumeNetto,0)) AS sell_{$value['yearmonth']}
    ,COUNT( IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',SupplyID,NULL)) AS trans_{$value['yearmonth']}
                ";
            }
        }
        $this->traceability_total = "SELECT
                                        %s AS label,
                                        SUM(a.VolumeNetto) AS total_penjualan,
                                        COUNT(SupplyTransID) AS total_transaction,
                                        COUNT(total_farmer) AS total_farmer_sell,
                                        SUM(a.VolumeNetto) AS total_sell,
                                        PERIOD_DIFF(DATE_FORMAT(max(DateTransaction),'%%Y%%m'),DATE_FORMAT(min(DateTransaction),'%%Y%%m')) + 1 bulan,
                                        min(date(DateTransaction)) date_min,
                                        max(date(DateTransaction)) date_max
                                        %s
                                    FROM
                                        ktv_tc_supplychain_transaction a
                                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                        LEFT JOIN ktv_members ON (a.SupplyID=ktv_members.MemberID ) AND a.SupplyType='Farmer'
                                        LEFT JOIN (
                                        SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, NULL)) total_farmer
                                        FROM
                                            ktv_tc_supplychain_transaction st1
                                            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st1.SupplyID AND st1.SupplyType='Batch'
                                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                                            LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
                                            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                                            LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                                        WHERE st1.SupplychainID IN ($SupplychainID)
                                            GROUP BY st1.SupplyTransID
                                        ) fr ON fr.transid=a.SupplyTransID
                                        %s
                                    WHERE
                                        1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                        %s
                                        $mi1 AND a.SupplychainID=$mill $mi2
                                        
                                    GROUP BY label";
        $query_total      = sprintf($this->traceability_total, $label, $SELECT, $LEFT, $where . $between, $groupby);

        $ConnMysql = GetMySqlConn();
        $query_mysql = $ConnMysql->prepare($query_total);
        $query_mysql->execute();
        $query_mysql->setFetchMode(PDO::FETCH_ASSOC);
        $output_total = $query_mysql->fetchAll();
        //print_r($query_mysql); die;
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->traceability_sales_certified = "SELECT
                                                    %s AS label,
                                                    SUM(ktv_members.total_farmer) AS farmer,
                                                    /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto,
                                                    /*SUM(VolumeNetto)*/ 0 AS netto_certified,
                                                    SUM(ktv_members.total_farmer) AS farmer_uncertified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto_uncertified
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                                    /* LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )*/
                                                    LEFT JOIN (
                                                    SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, NULL)) total_farmer, p.ProvinceID VillageID
                                                    FROM
                                                        ktv_tc_supplychain_transaction st1
                                                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st1.SupplyID AND st1.SupplyType='Batch'
                                                        LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                                                        LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
                                                        LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                                                        LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                                                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                                                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                                                        LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
                                                    WHERE st1.SupplychainID IN ($SupplychainID)
                                                        GROUP BY st1.SupplyTransID
                                                    ) ktv_members ON ktv_members.transid=a.SupplyTransID
                                                    %s
                                                WHERE
                                                    1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                    $mi1 AND a.SupplychainID=$mill $mi2
                                                    
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_certified  = sprintf($this->traceability_sales_certified, $label, $LEFT, $where, $awal, $akhir);
        $ConnMysql1 = GetMySqlConn();
        $query_mysql1 = $ConnMysql1->prepare($query_certified);
        $query_mysql1->execute();
        $query_mysql1->setFetchMode(PDO::FETCH_ASSOC);
        $result_certified = $query_mysql1->fetchAll();
        //print_r($result_certified); die;
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
       
        //$results['cpg']         = array(); //@$query_cpg->result_array();
        //$results['farmer']      = array(); //@$query_farmer->result_array();
        //$results['luas']        = array(); //@$query_luas->result_array();
        $results['produksi']    = array(); //@$query_produksi->result_array();

        //$total_ = $query_total->row();

        $results['total_penjualan']         = $output_total[0]['total_penjualan']; 
        $results['total_transaction']       = $output_total[0]['total_transaction'];
        $results['total_farmer_sell']       = $output_total[0]['total_farmer_sell'];
        $results['total_farmer_before_sell']= 0;
        $results['total_sell']              = $output_total[0]['total_sell'];
        
        

        $results['traceability_farmer']         = array(); //@$query_traceability_farmer->result_array();
        $results['traceability_production']     = array(); //@$query_traceability_production->result_array();
        
        $results['total']       = $output_total;
        $results['certified']   = $result_certified;
        // $results['penjualan']       = $query_penjualan->result_array();
        // $results['transaction']     = $query_transaction->result_array();
        // $results['farmer_sell']     = $query_farmer_sell->result_array();
        // $results['sell']            = $query_sell->result_array();
        
        $results['trader']          = array(); //$query_trader->result_array();
        $results['koperasi']        = array(); //$query_koperasi->result_array();
        $results['warehouse']       = array(); //$query_warehouse->result_array();
        
        /**Start**/
        $this->traceability_agent = "SELECT
                                                    %s AS label,
                                                    COUNT(DISTINCT c.SupplychainID) total
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch b ON a.SupplyID=b.SupplyBatchID AND a.SupplyType='Batch'
                                                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID=IFNULL(b.SupplyOrgID, IF(a.DOID > 0 , a.DOID, IF(a.AgentID > 0, a.AgentID, NULL)))
                                                    %s
                                                WHERE
                                                    a.SupplyType='Batch' $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_agent = $this->db->query(sprintf($this->traceability_agent, $label, $LEFT_agent, $where, $awal, $akhir, $groupby), array($prov));
        
        $debug['agent'] = $this->db->last_query();
        $results['agent'] = $query_agent->result_array();
        
        $query_mill = $this->db->query(sprintf($this->traceability_mill, $label, $LEFT_mill, @$where_mill, $groupby_mill), array($prov));
        $debug['mill'] = $this->db->last_query();
        $results['mill'] = $query_mill->result_array();
        /**End**/
        
        $results['months']          = $query_months->result_array();
        $results['debug']           = $debug;
        //echo "<pre>".print_r($debug['agent'],1);
        //echo "<pre>".print_r($results['agent'],1);exit;
        //echo '<pre>'.print_r($results, 1);die;
        return $results;
    }


    function readDataTraceabilityMillNewx($millgroup = '', $mill = '', $awal = '', $akhir = '')
    {
        $mi1 = $mill=='' || $mill=='false' ? '/*' : '';
        $mi2 = $mill=='' || $mill=='false' ? '*/' : '';
        $miGorup1   = $millgroup=='' || $millgroup=='false' ? '/*' : '';
        $miGorup12 = $millgroup=='' || $millgroup=='false' ? '*/' : '';
        $do1 = $do=='' || $do=='false' ? '/*' : '';
        $do2 = $do=='' || $do=='false' ? '*/' : '';
        $ag1 = $agent=='' ? '/*' : '';
        $ag2 = $agent=='' ? '*/' : '';
        $sql = "SELECT s.ObjID, s.ObjType, s.PersonID, p.UserID, o.SupplychainID, o.PartnerID, GROUP_CONCAT(acs.DistrictID) DistrictID
                FROM ktv_staffs s
                    LEFT JOIN ktv_persons p ON p.PersonID=s.PersonID
                    LEFT JOIN ktv_tc_supplychain_org o ON o.ObjID=s.ObjID AND o.ObjType=s.ObjType
                    LEFT JOIN ktv_access_staff acs ON (acs.StaffID=s.StaffID OR acs.UserId=p.UserID)
                WHERE p.UserID =?
                GROUP BY s.StaffID";
        $query = $this->db->query($sql, array(@$_SESSION['userid']))->result_array();
        $staff = @$query[0];
        $SupplychainID = @$staff['SupplychainID'];
        if($SupplychainID==''){
            //$sql = "SELECT GROUP_CONCAT(DISTINCT SupplychainID) SupplychainID FROM view_tc_supplychain_org WHERE PartnerID=? AND ObjType='mill'";
            $sql = "SELECT GROUP_CONCAT(DISTINCT vso.SupplychainID) SupplychainID
                    FROM
                        ktv_access_partner_mill a
                        LEFT JOIN ktv_mill b ON b.MillID = a.apmiMillID 
                        LEFT JOIN view_tc_supplychain_org vso ON vso.ObjID=a.apmiMillID AND vso.ObjType='mill'
                    WHERE
                        a.apmiPartnerID = ?
                        AND b.NDAAgree = 1 
                        AND b.StatusCode = 'active' AND vso.SupplychainID IS NOT NULL";
            $querym = $this->db->query($sql, array($_SESSION['PartnerID']));
            $SupplychainID = $querym->row()->SupplychainID;
        }

        $OrgType = @$staff['ObjType'];
        if($OrgType=='mill'){
            //$SupplychainID = '';
        }
        if($SupplychainID==''){
            $s1 = "/*"; $s2 = "*/";
        }else{
            $s1 = ""; $s2 = "";
        }
        
        if (@$petani == '1') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is not null";
        } elseif (@$petani == '2') {
            $LEFT = "LEFT JOIN (SELECT FarmerID farid,GardenNr,max(SurveyNr) LatestSurveyNr,ExternalDate from ktv_cocoa_certification
            where ExternalDate > '0000-00-00' group by FarmerID,GardenNr) ce
            on ce.farid=a.FarmerID and ce.GardenNr=a.GardenNr AND a.SurveyNr = ce.LatestSurveyNr";
            $qp = "and farid is null";
        } else $qps = ' AND a.SurveyNr = z.LatestSurveyNr';

        if ($prov == '') {
            $label = "IFNULL(Province,'-')";
            $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(ktv_members.VillageID,1,2)';
            $LEFT_agent = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(c.VillageID,1,2)';
            $LEFT_mill = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            //$where = 'and Province is not null';
            $where = '';
            $groupby = 'substr(ktv_members.VillageID,1,2)';
            $groupby_mill = 'substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label = "IFNULL(District,'-')";
            $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(ktv_members.VillageID,1,4)';
            $LEFT_agent = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(c.VillageID,1,4)';
            $LEFT_mill = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            //$where = 'and substr(ktv_members.VillageID,1,2)=? and District is not null';
            $where = 'and substr(ktv_members.VillageID,1,2)=?';
            $where_mill = 'and substr(VillageID,1,2)=? and District is not null';
            $groupby = 'substr(ktv_members.VillageID,1,4)';
            $groupby_mill = 'substr(VillageID,1,4)';
        } else {
            $label = "IFNULL(kp.SubDistrict,'-')";
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = ktv_members.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_agent = 'LEFT JOIN ktv_village kv ON kv.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $LEFT_mill = 'LEFT JOIN ktv_village kv ON kv.VillageID = kv.VillageID
            LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            //$where = 'and substr(ktv_members.VillageID,1,4)=? and kp.SubDistrict is not null';
            $where = 'and substr(ktv_members.VillageID,1,4)=?';
            $where_mill = 'and substr(kv.VillageID,1,4)=? and kp.SubDistrict is not null';
            $groupby = 'kp.SubDistrictID';
            $groupby_mill = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        if ($awal != '' and $akhir != '') {
            $between = " AND DateTransaction BETWEEN '{$awal} 00:00:00' AND '{$akhir} 23:59:59'";
            // $between = " and (a.DeliveryDate between '$awal' and '$akhir')";
            // $betweentrans = " and (DateTransaction between '$awal' and '$akhir')";
        }
        $where_partner = '';
        if (!empty($traceability_partner)) {
            $partner = $this->getPartner($traceability_partner);
            if ($partner['FlagAccess'] == '1') {
                //$where_partner = " AND CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$traceability_partner})";
            } else {
                //$where_partner = " AND SUBSTR(ktv_members.VillageID,1,4) IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = {$traceability_partner})";
            }
        }


        if (!empty($traceability_partner)) {
            //$where_partner = " AND wh.PartnerID = {$traceability_partner}";
        }
        $query_months = $this->db->query($this->month_list, array($akhir, $awal, $akhir));
        $SELECT = "";
        if ($query_months->num_rows()>0) {
            foreach ($query_months->result_array() as $key => $value) {
                $SELECT .= ",SUM(IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',a.VolumeNetto,0)) AS sell_{$value['yearmonth']}
    ,COUNT(DISTINCT IF(DATE_FORMAT(DateTransaction,'%Y%m')='{$value['yearmonth']}',SupplyID,NULL)) AS trans_{$value['yearmonth']}
                ";
            }
        }
        $this->traceability_total = "SELECT
                                        %s AS label,
                                        SUM(a.VolumeNetto) AS total_penjualan,
                                        COUNT(SupplyTransID) AS total_transaction,
                                        COUNT(total_farmer) AS total_farmer_sell,
                                        SUM(a.VolumeNetto) AS total_sell,
                                        PERIOD_DIFF(DATE_FORMAT(max(DateTransaction),'%%Y%%m'),DATE_FORMAT(min(DateTransaction),'%%Y%%m')) + 1 bulan,
                                        min(date(DateTransaction)) date_min,
                                        max(date(DateTransaction)) date_max
                                        %s
                                    FROM
                                        ktv_tc_supplychain_transaction a
                                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                        LEFT JOIN ktv_members ON (a.SupplyID=ktv_members.MemberID ) AND a.SupplyType='Farmer'
                                        LEFT JOIN (
                                        SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, NULL)) total_farmer
                                        FROM
                                            ktv_tc_supplychain_transaction st1
                                            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st1.SupplyID AND st1.SupplyType='Batch'
                                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                                            LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
                                            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                                            LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                                        WHERE st1.SupplychainID IN ($SupplychainID)
                                            GROUP BY st1.SupplyTransID
                                        ) fr ON fr.transid=a.SupplyTransID
                                        %s
                                    WHERE
                                        1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                        %s
                                        $mi1 AND a.SupplychainID=? $mi2
                                        $do1 AND sb2.SupplyOrgID=? $do2
                                    GROUP BY label";
        $query_total      = $this->db->query(sprintf($this->traceability_total, $label, $SELECT, $LEFT, $where . $between, $groupby), array($mill, $do));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->traceability_sales_certified = "SELECT
                                                    %s AS label,
                                                    SUM(ktv_members.total_farmer) AS farmer,
                                                    /*COUNT(DISTINCT IF(SupplyType='Farmer',MemberID,null))*/ 0 AS farmer_certified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto,
                                                    /*SUM(VolumeNetto)*/ 0 AS netto_certified,
                                                    SUM(ktv_members.total_farmer) AS farmer_uncertified,
                                                    IFNULL(SUM(a.VolumeNetto),0) AS netto_uncertified
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                                                    /* LEFT JOIN ktv_members  ON (a.SupplyID=ktv_members.MemberID )*/
                                                    LEFT JOIN (
                                                    SELECT st1.SupplyTransID transid, COUNT(DISTINCT IFNULL(m.MemberID, NULL)) total_farmer, p.ProvinceID VillageID
                                                    FROM
                                                        ktv_tc_supplychain_transaction st1
                                                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st1.SupplyID AND st1.SupplyType='Batch'
                                                        LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                                                        LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch'
                                                        LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                                                        LEFT JOIN ktv_members m ON (m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)) OR m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st1.SupplyID)))
                                                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                                                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                                                        LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
                                                    WHERE st1.SupplychainID IN ($SupplychainID)
                                                        GROUP BY st1.SupplyTransID
                                                    ) ktv_members ON ktv_members.transid=a.SupplyTransID
                                                    %s
                                                WHERE
                                                    1=1 $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                    $mi1 AND a.SupplychainID=? $mi2
                                                    $do1 AND sb2.SupplyOrgID=? $do2
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_certified  = $this->db->query(sprintf($this->traceability_sales_certified, $label, $LEFT, $where, $awal, $akhir), array($mill, $do));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
       
        //$results['cpg']         = array(); //@$query_cpg->result_array();
        //$results['farmer']      = array(); //@$query_farmer->result_array();
        //$results['luas']        = array(); //@$query_luas->result_array();
        $results['produksi']    = array(); //@$query_produksi->result_array();

        $total_ = $query_total->row();

        $results['total_penjualan']         = $total_->total_penjualan; 
        $results['total_transaction']       = $total_->total_transaction;
        $results['total_farmer_sell']       = $total_->total_farmer_sell;
        $results['total_farmer_before_sell']= 0;
        $results['total_sell']              = $total_->total_sell;
        
        

        $results['traceability_farmer']         = array(); //@$query_traceability_farmer->result_array();
        $results['traceability_production']     = array(); //@$query_traceability_production->result_array();
        
        $results['total']       = $query_total->result_array();
        $results['certified']   = $query_certified->result_array();
        // $results['penjualan']       = $query_penjualan->result_array();
        // $results['transaction']     = $query_transaction->result_array();
        // $results['farmer_sell']     = $query_farmer_sell->result_array();
        // $results['sell']            = $query_sell->result_array();
        
        $results['trader']          = array(); //$query_trader->result_array();
        $results['koperasi']        = array(); //$query_koperasi->result_array();
        $results['warehouse']       = array(); //$query_warehouse->result_array();
        
        /**Start**/
        $this->traceability_agent = "SELECT
                                                    %s AS label,
                                                    COUNT(DISTINCT c.SupplychainID) total
                                                FROM
                                                    ktv_tc_supplychain_transaction a
                                                    LEFT JOIN ktv_tc_supplychain_batch b ON a.SupplyID=b.SupplyBatchID AND a.SupplyType='Batch'
                                                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID=b.SupplyOrgID
                                                    %s
                                                WHERE
                                                    a.SupplyType='Batch' $s1 AND a.SupplychainID IN ($SupplychainID) $s2
                                                    %s
                                                    AND DateTransaction between '%s 00:00:00' and '%s 23:59:59'
                                                GROUP BY label
                                                ORDER BY label
                                                            ";
        $query_agent = $this->db->query(sprintf($this->traceability_agent, $label, $LEFT_agent, $where, $awal, $akhir, $groupby), array($prov));
        
        $debug['agent'] = $this->db->last_query();
        $results['agent'] = $query_agent->result_array();
        
        $query_mill = $this->db->query(sprintf($this->traceability_mill, $label, $LEFT_mill, @$where_mill, $groupby_mill), array($prov));
        $debug['mill'] = $this->db->last_query();
        $results['mill'] = $query_mill->result_array();
        /**End**/
        
        $results['months']          = $query_months->result_array();
        $results['debug']           = $debug;
        //echo "<pre>".print_r($debug['agent'],1);
        //echo "<pre>".print_r($results['agent'],1);exit;
        //echo '<pre>'.print_r($results, 1);die;
        return $results;
    }

    public function readDataTraceabilityBaru($prov = '', $district = '', $kec='', $desa='', $awal = '', $akhir = '', $traceability_partner = '', $mill='', $do='', $agent='')
    {
       
        if(intval($district)==0){
            $dis1 = "/*"; $dis2 = "*/";
        }else{
            $dis1 = ""; $dis2 = "";
        }

        if(intval($kec)==0){
            $kec1 = "/*"; $kec2 = "*/";
        }else{
            $kec1 = ""; $kec2 = "";
        }

        if(intval($desa)==0){
            $des1 = "/*"; $des2 = "*/";
        }else{
            $des1 = ""; $des2 = "";
        }

        @$org = $this->db->query("SELECT * FROM view_tc_supplychain_staff vss LEFT JOIN ktv_tc_supplychain_org kso ON kso.SupplychainID=vss.SupplychainID WHERE vss.UserID=?", array($_SESSION['userid']))->row_array(0);
        $SupplychainID = $org['SupplychainID'];
        $AccessBy = $org['AccessBy'];
        $PartnerID = $org['PartnerID'];
        
        if($AccessBy=='district'){

            $sql_total_farmer_fg ="SELECT COUNT(TotalFarmer) AS number_of_farmer
            FROM
            (
                SELECT 
                COUNT(km.MemberID) AS TotalFarmer 
            FROM 
                ktv_members km
            LEFT JOIN 
                ktv_tc_supplychain_farmer tsf on tsf.FarmerID = km.MemberID
            LEFT JOIN 
                ktv_member_role mrole ON km.MemberID = mrole.MemberID
            LEFT JOIN 
                ktv_tc_supplychain_org kso ON kso.SupplychainID = tsf.SupplychainID
            LEFT JOIN 
                ktv_village v ON v.VillageID = km.VillageID
            LEFT JOIN 
                ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
            LEFT JOIN 
                ktv_district d ON d.DistrictID=sd.DistrictID
            WHERE 
                km.StatusCode IN ('active','inactive')
            AND 
                kso.PartnerID = ?
            AND
                tsf.SupplychainID = ?
            AND 
                mrole.MRoleID = '1' 
            GROUP BY km.MemberID
            ) a";

            $sql_farmer = "SELECT
                                COUNT(DISTINCT dt.MemberID) AS number_of_farmer,
                                IFNULL(SUM(dt.Plot),0) number_of_plantations,
                                IFNULL(SUM(dt.Production), 0) AS number_of_farmer_production
                            FROM
                                (
                                    SELECT 
                                        dt.MemberID,
                                        COUNT(DISTINCT plot.PlotNr) AS Plot,
                                        SUM(IFNULL(plot.AnnualProduction,0)) AS Production
                                    FROM
                                    (
                                    SELECT
                                        m.MemberID,
                                        plot.PlotNr,
                                        MAX(plot.SurveyNr) AS SurveyNr
                                    FROM
                                        ktv_members m
                                        LEFT JOIN ktv_member_role mr ON mr.MemberID=m.MemberID
                                        LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID=m.MemberID
                                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                                        LEFT JOIN ktv_survey_plot plot ON plot.StatusCode='active' AND plot.MemberID=m.MemberID
                                        LEFT JOIN ktv_tc_supplychain_area sa ON sa.DistrictID=d.DistrictID
                                    WHERE
                                      m.MemberID!=0
                                        AND apm.apmPartnerID=?
                                        AND m.StatusCode='active'
                                        AND mr.MRoleID=1
                                        AND sa.SupplychainID=?
                                        $dis1 AND d.DistrictID=? $dis2
                                        $kec1 AND sd.SubDistrictID=? $kec2
                                        $des1 AND v.VillageID=? $des2
                                    GROUP BY m.MemberID, plot.PlotNr
                                    ) dt
                                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=dt.MemberID AND plot.PlotNr=dt.PlotNr AND plot.SurveyNr=dt.SurveyNr
                                    GROUP BY dt.MemberID
                                ) dt";
        }else{

            $sql_total_farmer_fg ="SELECT COUNT(TotalFarmer) AS number_of_farmer
                                    FROM
                                    (
                                        SELECT 
                                        COUNT(km.MemberID) AS TotalFarmer 
                                    FROM 
                                        ktv_members km
                                    LEFT JOIN 
                                        ktv_tc_supplychain_farmer tsf on tsf.FarmerID = km.MemberID
                                    LEFT JOIN 
                                        ktv_member_role mrole ON km.MemberID = mrole.MemberID
                                    LEFT JOIN 
                                        ktv_tc_supplychain_org kso ON kso.SupplychainID = tsf.SupplychainID
                                    LEFT JOIN 
                                        ktv_village v ON v.VillageID = km.VillageID
                                    LEFT JOIN 
                                        ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                    LEFT JOIN 
                                        ktv_district d ON d.DistrictID=sd.DistrictID
                                    WHERE 
                                        km.StatusCode IN ('active','inactive')
                                    AND 
                                        kso.PartnerID = ?
                                    AND
                                        tsf.SupplychainID = ?
                                    AND 
                                        mrole.MRoleID = '1' 
                                    GROUP BY km.MemberID
                                    ) a";

            $sql_farmer = "SELECT
                                COUNT(DISTINCT dt.MemberID) AS number_of_farmer,
                                IFNULL(SUM(dt.Plot),0) number_of_plantations,
                                IFNULL(SUM(dt.Production), 0) AS number_of_farmer_production
                            FROM
                                (
                                    SELECT 
                                        dt.MemberID,
                                        COUNT(DISTINCT plot.PlotNr) AS Plot,
                                        SUM(IFNULL(plot.AnnualProduction,0)) AS Production
                                    FROM
                                    (
                                    SELECT
                                        m.MemberID,
                                        plot.PlotNr,
                                        MAX(plot.SurveyNr) AS SurveyNr
                                    FROM
                                        ktv_tc_supplychain_farmer sf
                                        LEFT JOIN ktv_tc_supplychain_org kso ON kso.SupplychainID=sf.SupplychainID
                                        LEFT JOIN ktv_members m ON m.MemberID=sf.FarmerID AND m.MemberID!=0
                                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                                        LEFT JOIN ktv_survey_plot plot ON plot.StatusCode='active' AND plot.MemberID=m.MemberID
                                    WHERE
                                        kso.PartnerID=?
                                        AND sf.SupplychainID = ?
                                        AND sf.StatusCode='active'
                                        AND NOW() BETWEEN sf.DateStart AND sf.DateEnd
                                        $dis1 AND d.DistrictID=? $dis2
                                        $kec1 AND sd.SubDistrictID=? $kec2
                                        $des1 AND v.VillageID=? $des2
                                    GROUP BY m.MemberID, plot.PlotNr
                                    ) dt
                                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=dt.MemberID AND plot.PlotNr=dt.PlotNr AND plot.SurveyNr=dt.SurveyNr
                                    GROUP BY dt.MemberID
                                ) dt";
        }
        $query_farmer = $this->db->query($sql_farmer, array($PartnerID, $SupplychainID, $district, $kec, $desa))->row_array(0);

        $query_farmer_fg = $this->db->query($sql_total_farmer_fg, array($PartnerID,$SupplychainID))->row_array(0);

        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $date_range = $this->dateRangeLineChart($awal, $akhir);
        $categories_month = array();
        if(count($date_range) > 0){
            $select = "";
            $select2 = "";
            foreach ($date_range as $key => $value) {
                $categories_month[] = $value;
                $select .= ", SUM(IF(SUBSTR(st.DateTransaction, 1, 7)='$value', IF(m.MemberID IS NOT NULL AND st.PlantationNr > 0, st.VolumeNetto, 0), 0)) AS `ffb_ttp_$value`";
                $select .= ", SUM(IF(SUBSTR(st.DateTransaction, 1, 7)='$value', IF(m.MemberID IS NOT NULL AND (st.PlantationNr = 0 OR st.PlantationNr IS NULL), st.VolumeNetto, 0), 0)) AS `ffb_ttf_$value`";
                $select .= ", SUM(IF(SUBSTR(st.DateTransaction, 1, 7)='$value', IF(sb2.SupplyBatchID IS NOT NULL OR sdv.DeliveryID IS NOT NULL OR MillID > 0 OR DOID > 0 OR AgentID > 0, st.VolumeNetto, 0), 0)) AS `ffb_tta_$value`";
                $select .= ", SUM(IF(SUBSTR(st.DateTransaction, 1, 7)='$value', st.VolumeNetto, 0)) AS `ffb_all_$value`";
                $select .= ", SUM(IF(SUBSTR(st.DateTransaction, 1, 7)='$value', (st.VolumeNetto-IF(m.MemberID IS NOT NULL AND st.PlantationNr > 0, st.VolumeNetto, 0)-IF(m.MemberID IS NOT NULL AND (st.PlantationNr = 0 OR st.PlantationNr IS NULL), st.VolumeNetto, 0)-IF(sb2.SupplyBatchID IS NOT NULL OR sdv.DeliveryID IS NOT NULL OR MillID > 0 OR DOID > 0 OR AgentID > 0, st.VolumeNetto, 0)), 0)) AS `ffb_utc_$value`";

                $select2 .= ", SUM(IF(SUBSTR(sd.DeliveryDate, 1, 7)='$value', sdd.Weight, 0)) AS `ffb_delivered_$value`";
                $select2 .= ", SUM(IF(SUBSTR(sd.DeliveryDate, 1, 7)='$value', IF(st2.SupplyTransID > 0, sdd.Weight, 0), 0)) AS `ffb_received_$value`";
            }
        }else{
            $select = "";
        }


        $sql_transaction_new = "SELECT 
                                    COUNT(DISTINCT m.MemberID) total_number_farmer_with_transaction,
                                    COUNT(DISTINCT IF(m.MemberID IS NOT NULL, st.SupplyTransID, NULL)) AS total_number_of_transactions,
                                    SUM(st.VolumeNetto) AS total_ffb_received_at_dealer,
                                    SUM(IF(m.MemberID IS NOT NULL AND st.PlantationNr > 0, st.VolumeNetto, 0)) AS total_ffb_traceable_to_plantation_at_dealer,
                                    SUM(IF(m.MemberID IS NOT NULL AND st.PlantationNr > 0, st.VolumeNetto, 0)) AS total_ffb_traceable_plantation,
                                    SUM(IF(m.MemberID IS NOT NULL AND (st.PlantationNr = 0 OR st.PlantationNr IS NULL), st.VolumeNetto, 0)) AS total_ffb_traceable_farmer,
                                    SUM(IF(sb2.SupplyBatchID IS NOT NULL OR sdv.DeliveryID IS NOT NULL OR MillID > 0 OR DOID > 0 OR AgentID > 0, st.VolumeNetto, 0)) AS total_ffb_traceable_agent,
                                    SUM(st.VolumeNetto) AS total_ffb_all
                                    $select
                                FROM 
                                    ktv_tc_supplychain_transaction st 
                                LEFT JOIN 
                                    ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer' AND m.MemberID!=0
                                LEFT JOIN 
                                    ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND st.SupplyID > 0
                                LEFT JOIN 
                                    ktv_tc_supplychain_delivery sdv ON sdv.DeliveryID=st.SupplyID AND st.SupplyType='Delivery' AND st.SupplyID > 0
                                LEFT JOIN 
                                    ktv_village v ON v.VillageID = m.VillageID
                                LEFT JOIN 
                                    ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                LEFT JOIN 
                                    ktv_district d ON d.DistrictID=sd.DistrictID
                                WHERE 
                                st.SupplychainID = ? 
                                AND st.StatusCode='active'
                                AND DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') BETWEEN ? AND ?
                                $dis1 AND d.DistrictID=? $dis2
                                $kec1 AND sd.SubDistrictID=? $kec2
                                $des1 AND v.VillageID=? $des2";
        $query_transaction_new0 = $this->db->query($sql_transaction_new, array($SupplychainID, $awal, $akhir, $district, $kec, $desa));
        $query_transaction_new = $query_transaction_new0->row_array(0);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        // $sql_transaction = "SELECT
        //                         COUNT(DISTINCT m.MemberID) total_number_farmer_with_transaction,
        //                         COUNT(DISTINCT IF(m.MemberID IS NOT NULL, st.SupplyTransID, NULL)) AS total_number_of_transactions,
        //                         SUM(st.VolumeNetto) AS total_ffb_received_at_dealer,
        //                         SUM(IF(m.MemberID IS NOT NULL AND st.PlantationNr > 0, st.VolumeNetto, 0)) AS total_ffb_traceable_to_plantation_at_dealer,
        //                         SUM(IF(m.MemberID IS NOT NULL AND st.PlantationNr > 0, st.VolumeNetto, 0)) AS total_ffb_traceable_plantation,
        //                         SUM(IF(m.MemberID IS NOT NULL AND (st.PlantationNr = 0 OR st.PlantationNr IS NULL), st.VolumeNetto, 0)) AS total_ffb_traceable_farmer,
        //                         SUM(IF(sb2.SupplyBatchID IS NOT NULL OR sdv.DeliveryID IS NOT NULL OR MillID > 0 OR DOID > 0 OR AgentID > 0, st.VolumeNetto, 0)) AS total_ffb_traceable_agent,
        //                         SUM(st.VolumeNetto) AS total_ffb_all
        //                         $select
        //                     FROM
        //                         ktv_tc_supplychain_transaction st
        //                         LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer' AND m.MemberID!=0
        //                         LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND st.SupplyID > 0
        //                         LEFT JOIN ktv_tc_supplychain_delivery sdv ON sdv.DeliveryID=st.SupplyID AND st.SupplyType='Delivery' AND st.SupplyID > 0
        //                         LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=IF(sdv.SupplychainID > 0, sdv.SupplychainID, IF(sb2.SupplyOrgID > 0, sb2.SupplyOrgID, IF(st.MillID > 0, st.MillID, IF(st.DOID > 0, st.DOID, IF(st.AgentID > 0, st.AgentID, '')))))
        //                         LEFT JOIN ktv_village v ON v.VillageID=IFNULL(m.VillageID, vso.VillageID)
        //                         LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
        //                         LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
        //                         LEFT JOIN ktv_survey_plot plot ON plot.StatusCode='active' AND plot.MemberID=m.MemberID
        //                         LEFT JOIN ktv_tc_supplychain_area sa ON sa.DistrictID=d.DistrictID
        //                     WHERE
        //                         st.SupplychainID=?
        //                         AND st.StatusCode='active'
        //                         AND DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') BETWEEN ? AND ?
        //                         $dis1 AND d.DistrictID=? $dis2
        //                         $kec1 AND sd.SubDistrictID=? $kec2
        //                         $des1 AND v.VillageID=? $des2";
        // $query_transaction0 = $this->db->query($sql_transaction, array($SupplychainID, $awal, $akhir, $district, $kec, $desa));
        // $query_transaction = $query_transaction0->row_array(0);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $series_ffb_transaction = array();
        if($query_transaction_new0->num_rows() > 0 && count($date_range) > 0){
            $series_ffb_transaction[0]['name'] = lang('Traceable to Plantation');
            $series_ffb_transaction[1]['name'] = lang('Traceable to Farmer');
            $series_ffb_transaction[2]['name'] = lang('Traceable to Agent/Dealer');
            // $series_ffb_transaction[3]['name'] = lang('Untraceable');
            foreach($categories_month as $k => $v){
                $series_ffb_transaction[0]['data'][] = round((floatval($query_transaction_new["ffb_ttp_$v"])/1000), 2);
                $series_ffb_transaction[1]['data'][] = round((floatval($query_transaction_new["ffb_ttf_$v"])/1000), 2);
                // $series_ffb_transaction[2]['data'][] = round((floatval($query_transaction_new["ffb_tta_$v"])/1000), 2);
                $series_ffb_transaction[2]['data'][] = round((floatval($query_transaction_new["ffb_tta_$v"])/1000), 2);
                // $series_ffb_transaction[3]['data'][] = round((floatval($query_transaction_new["ffb_utc_$v"])/1000), 2);
            }
        }

        $sql_delivery = "SELECT
                             COUNT(DISTINCT sd.DeliveryID) AS number_of_delivery,
                             SUM(IFNULL(sdd.Weight, 0)) AS total_ffb_sold
                             $select2
                        FROM
                            ktv_tc_supplychain_delivery sd
                            LEFT JOIN ktv_tc_supplychain_delivery_detail sdd ON sdd.DeliveryID=sd.DeliveryID AND sdd.StatusCode='active'
                            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sd.DeliveryID AND st2.SupplyType='Delivery' AND st2.StatusCode='active'
                        WHERE
                            sd.StatusCode = 'active'
                            AND sd.SupplychainID=?
                            AND sd.DeliveryStatusID IN (3,4,5,6)
                            AND DATE_FORMAT(sd.DeliveryDate, '%Y-%m-%d') BETWEEN ? AND ?";
        $query_delivery0 = $this->db->query($sql_delivery, array($SupplychainID, $awal, $akhir));
        $query_delivery = $query_delivery0->row_array(0);

        $series_ffb_delivery = array();
        if($query_delivery0->num_rows() > 0 && count($date_range) > 0){
            $series_ffb_delivery[0]['name'] = lang('Delivered FFB (TON)');
            $series_ffb_delivery[1]['name'] = lang('Received FFB (TON)');
            foreach($categories_month as $k => $v){
                $series_ffb_delivery[0]['data'][] = round((floatval($query_delivery["ffb_delivered_$v"])/1000), 2);
                $series_ffb_delivery[1]['data'][] = round((floatval($query_delivery["ffb_received_$v"])/1000), 2);
            }
        }
        // echo '<pre>'; print_r($series_ffb_delivery, 1); echo '</pre>'; exit;
        $potential_annual['data'][0]['name'] = lang("Potential Volume");
        $potential_annual['data'][0]['data'][0] = round((floatval($query_farmer['number_of_farmer_production'])/1000), 2);
        $potential_annual['data'][1]['name'] = lang("Total Traceable Volume");
        $potential_annual['data'][1]['data'][0] = round((floatval($query_transaction_new['total_ffb_traceable_to_plantation_at_dealer'])/1000), 2);
        $potential_annual['label'][0] = lang("Volume (TON)");
        // var_dump($potential_annual['data'][0]['data']['0']);exit;
        
        $traceable_volume = array('data' => array(
                                                array(
                                                    "name" => 'Traceable to Farmer',
                                                    "y" => round((floatval($query_transaction_new['total_ffb_traceable_farmer'])/1000), 2),
                                                    "sliced" => true,
                                                    "selected" => true
                                                ),
                                                array(
                                                    "name" => 'Traceable to Agent/Dealer',
                                                    "y" =>round(((floatval($query_transaction_new['total_ffb_all']) - floatval($query_transaction_new['total_ffb_traceable_farmer']) - floatval($query_transaction_new['total_ffb_traceable_agent']) - floatval($query_transaction_new['total_ffb_traceable_plantation']))/1000), 2)
                                                ),
                                                array(
                                                    "name" => 'Traceable to Plantation',
                                                    "y" =>  round((floatval($query_transaction_new['total_ffb_traceable_to_plantation_at_dealer'])/1000), 2),
                                                ),
                                                // array(
                                                //     "name" => 'Untraceable',
                                                //     "y" =>  round(((floatval($query_transaction_new['total_ffb_all']) - floatval($query_transaction_new['total_ffb_traceable_farmer']) - floatval($query_transaction_new['total_ffb_traceable_agent']) - floatval($query_transaction_new['total_ffb_traceable_plantation']))/1000), 2),
                                                // )
                                            ),
                                             'judul' => lang('FFB Traceability Percentage'),
                                             'yjudul' => lang('Volume'),
                                             'label' => array('Volume'));
        
        $return = array(
            // 'number_of_farmer' => $query_farmer['number_of_farmer'],
            'number_of_farmer' => $query_farmer_fg['number_of_farmer'],

            'number_of_plantations' => $query_farmer['number_of_plantations'],
            'number_of_farmer_production' => $query_farmer['number_of_farmer_production'],

            'total_number_farmer_with_transaction' => $query_transaction_new['total_number_farmer_with_transaction'],
            'total_number_of_transactions' => $query_transaction_new['total_number_of_transactions'],
            'total_ffb_received_at_dealer' => round((floatval($query_transaction_new['total_ffb_received_at_dealer'])/1000), 2),
            'total_ffb_traceable_to_plantation_at_dealer' => round((floatval($query_transaction_new['total_ffb_traceable_to_plantation_at_dealer'])/1000), 2),
            'total_ffb_sold' => round((floatval($query_delivery['total_ffb_sold'])/1000), 2),
            'number_of_delivery' => $query_delivery['number_of_delivery'],

            'potential_annual' => $potential_annual,
            'traceable_volume' => $traceable_volume,

            'categories_month' => $categories_month,
            'series_ffb_transaction' => $series_ffb_transaction,
            'series_ffb_delivery' => $series_ffb_delivery,
        );

        return $return;
    }

    public function GettransactionfarmerBaru($get)
    {   
        //echo "<pre>".print_r($get, 1);die;
        @$org = $this->db->query("SELECT * FROM view_tc_supplychain_staff vss LEFT JOIN ktv_tc_supplychain_org kso ON kso.SupplychainID=vss.SupplychainID WHERE vss.UserID=?", array($_SESSION['userid']))->row_array(0);
        $SupplychainID = $org['SupplychainID'];
        $order = json_decode(@$get['sort'], true);
        $order_by = @$order[0]['property']=='' ? 'MemberDisplayID ' : $order[0]['property'];
        $sort = @$order[0]['direction']=='' ? '' : $order[0]['direction'];
        $keyword = "%".$get['keyword_filter']."%";

        $year1 = intval(@$get['year_filter']) > 0 ? '' : '/*';
        $year2 = intval(@$get['year_filter']) > 0 ? '' : '*/';

        $p1 = intval(@$get['province_filter']) > 0 ? '' : '/*';
        $p2 = intval(@$get['province_filter']) > 0 ? '' : '*/';

        $d1 = intval(@$get['district_filter']) > 0 ? '' : '/*';
        $d2 = intval(@$get['district_filter']) > 0 ? '' : '*/';

        if(intval($get['district']) > 0){
            $dis1 = ""; $dis2 = "";
        }else{
            $dis1 = "/*"; $dis2 = "*/";
        }

        if(intval($get['subdistrict']) > 0){
            $kec1 = ""; $kec2 = "";
        }else{
            $kec1 = "/*"; $kec2 = "*/";
        }

        if(intval($get['village']) > 0){
            $des1 = ""; $des2 = "";
        }else{
            $des1 = "/*"; $des2 = "*/";
        }
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    m.MemberID,
                    m.MemberDisplayID,
                    m.MemberName AS SupplierName,
                    p.Province,
                    d.District,
                    sd.SubDistrict,
                    v.Village,
                    mp.Production,
                    SUM(IFNULL(st2.VolumeNetto, st.VolumeNetto)) AS VolumeNetto
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND st.SupplyID > 0
                    LEFT JOIN ktv_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=IFNULL(st2.SupplyID,st.SupplyID) AND IFNULL(st2.SupplyType,st.SupplyType)='Farmer' AND m.MemberID!=0
                    LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
                    LEFT JOIN view_member_plot mp ON mp.MemberID=m.MemberID
                WHERE
                    st.SupplychainID=?
                    AND st.StatusCode='active'
                    AND DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') BETWEEN ? AND ?
                    $dis1 AND d.DistrictID=? $dis2
                    $kec1 AND sd.SubDistrictID=? $kec2
                    $des1 AND v.VillageID=? $des2
                    AND m.MemberID IS NOT NULL
                    AND (m.MemberDisplayID LIKE ? OR m.MemberName LIKE ? OR p.Province LIKE ? OR d.District LIKE ? OR sd.SubDistrict LIKE ? OR v.Village LIKE ?)
                    $year1 AND st.DateTransaction LIKE ? $year2
                    $p1 AND p.ProvinceID=? $p2
                    $d1 AND d.DistrictID=? $d2
                GROUP BY m.MemberID
                ORDER BY $order_by $sort
                LIMIT ?,?";
        $query = $this->db->query($sql, array($SupplychainID, $get['awal'], $get['akhir'],
            $get['district'], $get['subdistrict'],  $get['village'],  
            $keyword, $keyword, $keyword, $keyword, $keyword, $keyword,
            $get['year_filter']."%", $get['province_filter'], $get['district_filter'],
            intval($get['start']), intval($get['limit'])));
        //echo "<pre>".$this->db->last_query();exit; 
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                );
        }else{
            return array(
                'data'          => 0,
                'total'         => 0,
                );
        }
    }

    function dateRangeLineChart($start_date, $end_date){
        $start    = (new DateTime($start_date))->modify('first day of this month');
        $end      = (new DateTime($end_date))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $i = 0;
        $bulan = array();
        foreach ($period as $dt) {
            $bulan[$i] = $dt->format("Y-m");
            $i++;
        }
        $total = count($bulan);
        if($total > 0){
            $j = 0;
            for($i=($total-1); $j < 12; $i--){
                if(@$bulan[$i]!=''){
                    $month[$j] = $bulan[$i];
                }
                $j++;
            }
        }else{
            $month = array();
        }
        return $month;
    }

    function readDataTraceabilityMillBaru($get){
        $daerah_access = $_SESSION['daerah_access'];
        $date_range = $this->dateRangeLineChart($get['awal'], $get['akhir']);
        $categories_month = array();
        //echo "<pre>".print_r($get, 1);       
        //echo "<pre>".print_r($_SESSION, 1);  
        @$org = $this->db->query("SELECT * FROM view_tc_supplychain_org vtso WHERE vtso.PartnerID=? AND vtso.ObjType = 'mill'", array($_SESSION['PartnerID']))->row_array(0);
        $SupplychainID = $org['SupplychainID'];
        $AccessBy = $org['AccessBy'];
        $PartnerID = $org['PartnerID'];
        if(intval($get['district']) > 0){
            $dis1 = ""; $dis2 = "";
        }else{
            $dis1 = "/*"; $dis2 = "*/";
        }

        if(intval($get['subdistrict']) > 0){
            $kec1 = ""; $kec2 = "";
        }else{
            $kec1 = "/*"; $kec2 = "*/";
        }

        if(intval($get['village']) > 0){
            $des1 = ""; $des2 = "";
        }else{
            $des1 = "/*"; $des2 = "*/";
        }

        $daer1 = '/*'; $daer2 = '*/';

        $sql_farmer = "SELECT
                            COUNT(DISTINCT m.MemberID) AS total_farmer
                        FROM
                            ktv_members m
                            LEFT JOIN ktv_member_role mr ON mr.MemberID=m.MemberID
                            LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID=m.MemberID
                            LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        WHERE
                            m.MemberID!=0
                            AND apm.apmPartnerID=?
                            /*AND m.PartnerID=7*/
                            AND m.StatusCode='active'
                            AND mr.MRoleID=1
                            $daer1 AND d.DistrictID IN ($daerah_access) $daer2
                            $dis1 AND d.DistrictID=? $dis2
                            $kec1 AND sd.SubDistrictID=? $kec2
                            $des1 AND v.VillageID=? $des2";
        $query_farmer = $this->db->query($sql_farmer, array($PartnerID, $get['district'], $get['subdistrict'], $get['village']))->row_array(0);
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';

        $sql_agent = "SELECT
                            COUNT(DISTINCT vso.SupplychainID) AS total_dealer
                        FROM
                            ktv_tc_supplychain_org_rel rel
                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=rel.ChildID
                            LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        WHERE
                            rel.StatusCode = 'active'
                            AND rel.ParentID=?
                            $daer1 AND d.DistrictID IN ($daerah_access) $daer2
                            $dis1 AND d.DistrictID=? $dis2
                            $kec1 AND sd.SubDistrictID=? $kec2
                            $des1 AND v.VillageID=? $des2";
        $query_agent = $this->db->query($sql_agent, array($SupplychainID, $get['district'], $get['subdistrict'], $get['village']))->row_array(0);

        $sql_farmer_transaction = "SELECT 
                COUNT(DISTINCT dt.SupplyID) AS total_number_of_farmer_selling,
                SUM(VolumeNetto) AS total_number_of_transaction_from_farmers,
                SUM(IF(dt.PlantationNr > 0, dt.VolumeNetto, 0)) AS total_number_of_plantation_numbers
            FROM 
                (
                    SELECT
                        IFNULL(stdv2.SupplyTransID,IFNULL(st3.SupplyTransID,IFNULL(stdv.SupplyTransID,IFNULL(st2.SupplyTransID,IF(st.SupplyType='Farmer', st.SupplyTransID, NULL))))) AS SupplyTransID,
                        IFNULL(stdv2.SupplyID,IFNULL(st3.SupplyID,IFNULL(stdv.SupplyID,IFNULL(st2.SupplyID,IF(st.SupplyType='Farmer', st.SupplyID, NULL))))) AS SupplyID,
                        IFNULL(stdv2.VolumeNetto,IFNULL(st3.VolumeNetto,IFNULL(stdv.VolumeNetto,IFNULL(st2.VolumeNetto,IF(st.SupplyType='Farmer', st.VolumeNetto, NULL))))) AS VolumeNetto,
                        IFNULL(stdv2.PlantationNr,IFNULL(st3.PlantationNr,IFNULL(stdv.PlantationNr,IFNULL(st2.PlantationNr,IF(st.SupplyType='Farmer', st.PlantationNr, NULL))))) AS PlantationNr
                    FROM
                        ktv_tc_supplychain_delivery sd0
                        LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=sd0.SupplyDestMillOrgID AND st.SupplyID=sd0.DeliveryID AND st.SupplyType='Delivery'
                        /**/
                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND st.SupplyID > 0 AND sb2.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID AND st2.StatusCode='active'
                        
                        LEFT JOIN ktv_tc_supplychain_delivery sdv ON sdv.DeliveryID=st.SupplyID AND st.SupplyType='Delivery' AND st.SupplyID > 0 AND sdv.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_delivery_detail sdvd ON sdvd.DeliveryID=IFNULL(sdv.DeliveryID, sd0.DeliveryID) AND sdvd.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_batch sbdv ON sbdv.SupplyBatchID=sdvd.SupplyBatchID AND sbdv.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_transaction stdv ON stdv.SupplyBatchID=sbdv.SupplyBatchID
                        /**/
                        LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID AND st2.SupplyType='Batch' AND st2.SupplyID > 0 AND sb3.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID AND st3.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_delivery sdv2 ON sdv2.DeliveryID=st2.SupplyID AND st2.SupplyType='Delivery' AND st2.SupplyID > 0 AND sdv2.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_delivery_detail sdvd2 ON sdvd2.DeliveryID=sdv2.DeliveryID AND sdvd2.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_batch sbdv2 ON sbdv2.SupplyBatchID=sdvd2.SupplyBatchID AND sbdv2.StatusCode='active'
                        LEFT JOIN ktv_tc_supplychain_transaction stdv2 ON stdv2.SupplyBatchID=sbdv2.SupplyBatchID
                        
                        LEFT JOIN ktv_members m ON m.MemberID=IFNULL(stdv2.SupplyID,IFNULL(st3.SupplyID,IFNULL(stdv.SupplyID,IFNULL(st2.SupplyID,IF(st.SupplyType='Farmer', st.VolumeNetto, NULL))))) 
                            AND IFNULL(stdv2.SupplyType,IFNULL(st3.SupplyType,IFNULL(stdv.SupplyType,IFNULL(st2.SupplyType,st.SupplyType))))='Farmer' AND m.MemberID!=0
                        LEFT JOIN ktv_village v ON v.VillageID=IFNULL(m.VillageID, m.VillageID)
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        LEFT JOIN ktv_survey_plot plot ON plot.StatusCode='active' AND plot.MemberID=m.MemberID
                        LEFT JOIN ktv_tc_supplychain_area sa ON sa.DistrictID=d.DistrictID
                    WHERE
                        sd0.SupplyDestMillOrgID=?
                        AND sd0.StatusCode='active'
                        AND m.MemberID IS NOT NULL
                        AND DATE_FORMAT(IFNULL(stdv2.SupplyTransID,IFNULL(st3.DateTransaction,IFNULL(stdv.DateTransaction,IFNULL(st2.DateTransaction,IF(st.SupplyType='Farmer', st.DateTransaction, NULL))))), '%Y-%m-%d') BETWEEN ? AND ?
                        $daer1 AND d.DistrictID IN ($daerah_access) $daer2
                        $dis1 AND d.DistrictID=? $dis2
                        $kec1 AND sd.SubDistrictID=? $kec2
                        $des1 AND v.VillageID=? $des2
                    GROUP BY IFNULL(stdv2.SupplyTransID,IFNULL(st3.SupplyTransID,IFNULL(stdv.SupplyTransID,IFNULL(st2.SupplyTransID,IF(st.SupplyType='Farmer', st.SupplyTransID, NULL)))))
                ) dt";
        $query_farmer_transaction = $this->db->query($sql_farmer_transaction, array($SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village']))->row_array(0);
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $dt_mill_transaction = "SELECT
                                    CONCAT('D',sdel.DeliveryID) AS id,
                                    SUM(sdd.Weight) AS weight,
                                    vso.SupplychainID AS supplierid,
                                    vso.`Name` AS suppliername,
                                    3 AS SupplybaseCategoryID,
                                    'Traceable' AS Traceability,
                                    sdel.DeliveryDate AS DateTransaction
                                FROM
                                    ktv_tc_supplychain_delivery sdel
                                    LEFT JOIN ktv_tc_supplychain_delivery_detail sdd ON sdd.DeliveryID=sdel.DeliveryID
                                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sdel.SupplychainID
                                    LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                                WHERE
                                    sdel.SupplyDestMillOrgID=?
                                    AND sdel.StatusCode='active'
                                    AND sdel.DeliveryStatusID = '4'
                                    AND DATE_FORMAT(sdel.DeliveryDate, '%Y-%m-%d') BETWEEN ? AND ?
                                    /* AND d.DistrictID=? */
                                    /* AND sd.SubDistrictID=? */
                                    /* AND v.VillageID=? */
                                GROUP BY sdel.DeliveryID
                                UNION
                                SELECT
                                    CONCAT('T', st.SupplyTransID) AS id,
                                    st.VolumeNetto AS weight,
                                    vso2.SupplychainID AS supplierid,
                                    vso2.`Name` AS suppliername,
                                    IFNULL(st.SupplybaseCategoryID, '3') AS SupplybaseCategoryID,
                                    IF(vso2.SupplychainID IS NOT NULL, 'Traceable', 'Untraceable')  AS Traceability,
                                    st.DateTransaction
                                FROM
                                    ktv_tc_supplychain_transaction st
                                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' 
                                        AND (st.SupplyBatchType IS NULL OR st.SupplyBatchType!='Untraceable') AND st.SupplyID!=st.SupplychainID
                                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IFNULL(sb.SupplyOrgID, IF(st.MillID > 0, st.MillID, IF(st.DOID > 0, st.DOID, st.AgentID)))
                                    LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                                    LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                                WHERE
                                    st.StatusCode='active'
                                    AND vso.ObjType='mill'
                                    AND vso.SupplychainID=?
                                    AND st.SupplyType!='' AND st.SupplyType!='Delivery'
                                    AND sb.SupplyBatchID IS  NULL
                                    AND DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') BETWEEN ? AND ?
                                    /* AND d.DistrictID=? */
                                    /* AND sd.SubDistrictID=? */
                                    /* AND v.VillageID=? */";

        $sql_mill_transaction = "SELECT 
                                    COUNT(DISTINCT IF(dt.SupplybaseCategoryID='3', dt.id, NULL)) AS total_number_transactions_from_dealer,
                                    SUM(dt.weight) AS traceable_volume_received_at_mill,
                                    SUM(IF(dt.SupplybaseCategoryID='1', dt.weight, 0)) AS traceable_volume_farmer_plasma,
                                    SUM(IF(dt.SupplybaseCategoryID='2', dt.weight, 0)) AS traceable_volume_direct_smallholder,
                                    SUM(IF(dt.SupplybaseCategoryID='3', dt.weight, 0)) AS traceable_volume_agent_dealer,
                                    SUM(IF(dt.SupplybaseCategoryID='4', dt.weight, 0)) AS traceable_volume_owned_estate,
                                    SUM(IF(dt.SupplybaseCategoryID='5', dt.weight, 0)) AS traceable_volume_external_estate,
                                    SUM(IF(dt.Traceability='Traceable', dt.weight, 0)) AS traceable_volume,
                                    SUM(IF(dt.Traceability='Untraceable', dt.weight, 0)) AS untraceable_volume
                                FROM
                                    ($dt_mill_transaction) dt";
        $query_mill_transaction = $this->db->query($sql_mill_transaction, array(
            $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village'],
            $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village']
        ))->row_array(0);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $series_production = array();
        $series_despatch = array();
        if(count($date_range) > 0){
            $select1 = "";
            $select2 = "";
            foreach ($date_range as $key => $value) {
                $categories_month[] = $value;
                $select1 .= ", SUM(IF(SUBSTR(p.ProcessingDate, 1, 7)='$value', IF(pp.ProductID='1', pp.ProductVolume, 0), 0)) AS `cpo_$value`";
                $select1 .= ", SUM(IF(SUBSTR(p.ProcessingDate, 1, 7)='$value', IF(pp.ProductID='2', pp.ProductVolume, 0), 0)) AS `pk_$value`";
                
                $select2 .= ", SUM(IF(SUBSTR(dt.Tanggal, 1, 7)='$value', IF(dt.ProductID='1', dt.DespatchVolume, 0), 0)) AS `despatch_cpo_$value`";
                $select2 .= ", SUM(IF(SUBSTR(dt.Tanggal, 1, 7)='$value', IF(dt.ProductID='2', dt.DespatchVolume, 0), 0)) AS `despatch_pk_$value`";
            }
        }else{
            $select1 = "";
            $select2 = "";
        }
        $sql_mill_production = "SELECT
                SUM(IF(pp.ProductID='1', pp.ProductVolume, 0)) AS total_cpo_production,
                SUM(IF(pp.ProductID='2', pp.ProductVolume, 0)) AS total_pk_production
                $select1
            FROM
                `ktc_tc_processing_product` pp 
                LEFT JOIN ktv_tc_processing p ON p.ProcessingID=pp.ProcessingID
            WHERE
                pp.StatusCode = 'active' AND p.StatusCode='active'
                AND p.SupplychainID = ?
                AND p.ProcessingDate BETWEEN ? AND ?";
        $query_mill_production0 = $this->db->query($sql_mill_production, array($SupplychainID, $get['awal'], $get['akhir']));
        $query_mill_production = $query_mill_production0->row_array(0);
        
        if($query_mill_production0->num_rows() > 0 && count($date_range) > 0){
            $series_production[0]['name'] = 'CPO';
            $series_production[1]['name'] = 'PK';
            foreach($categories_month as $k => $v){
                $series_production[0]['data'][] = round((floatval($query_mill_production["cpo_$v"])/1000), 3);
                $series_production[1]['data'][] = round((floatval($query_mill_production["pk_$v"])/1000), 3);
            }
        }
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $sql_mill_despatch = "SELECT 
                    SUM(IF(dt.ProductID='1', dt.DespatchVolume, 0)) AS total_traceable_cpo_dispatched_to_refinery,
                    SUM(IF(dt.ProductID='2', dt.DespatchVolume, 0)) AS total_traceable_pk_dispatched_to_refinery
                    $select2
                FROM
                (
                    SELECT
                        d.DespatchID,
                        dd.DespatchVolume,
                        dd.ProductID,
                        p.ProcessingDate AS Tanggal
                    FROM
                        `ktc_tc_processing_product` pp 
                        LEFT JOIN ktv_tc_processing p ON p.ProcessingID=pp.ProcessingID
                        LEFT JOIN ktv_tc_despatch_detail dd ON dd.ProcessingProductID=pp.ProcessingProductID  AND pp.StatusCode='active'
                        LEFT JOIN ktv_tc_despatch d ON d.DespatchID=dd.DespatchID AND d.StatusCode='active' 
                    WHERE
                        pp.StatusCode = 'active' AND p.StatusCode='active'
                        AND p.SupplychainID = ?
                        AND p.ProcessingDate BETWEEN ? AND ?
                        AND d.DespatchID IS NOT NULL
                        AND d.DestpatchStatusID IN (1,4)
                    GROUP BY dd.DespatchDetailID
                ) dt";
        $query_mill_despatch0 = $this->db->query($sql_mill_despatch, array($SupplychainID, $get['awal'], $get['akhir']));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $query_mill_despatch0 = $query_mill_despatch0->row_array(0);

        $sql_mill_despatch_received = "SELECT 
                    COUNT(DISTINCT IF(dt.ProductID='1', dt.DespatchID, NULL)) AS total_number_of_cpo_refinery_transaction,
                    COUNT(DISTINCT IF(dt.ProductID='2', dt.DespatchID, NULL)) AS total_number_of_pk_refinery_transaction,
                    SUM(IF(dt.ProductID='1', dt.DespatchVolume, 0)) AS total_traceable_cpo_received_to_refinery,
                    SUM(IF(dt.ProductID='2', dt.DespatchVolume, 0)) AS total_traceable_pk_received_to_refinery
                    $select2
                FROM
                (
                    SELECT
                        d.DespatchID,
                        dd.DespatchVolume,
                        dd.ProductID,
                        p.ProcessingDate AS Tanggal
                    FROM
                        `ktc_tc_processing_product` pp 
                        LEFT JOIN ktv_tc_processing p ON p.ProcessingID=pp.ProcessingID
                        LEFT JOIN ktv_tc_despatch_detail dd ON dd.ProcessingProductID=pp.ProcessingProductID  AND pp.StatusCode='active'
                        LEFT JOIN ktv_tc_despatch d ON d.DespatchID=dd.DespatchID AND d.StatusCode='active' AND d.DestpatchStatusID IN (3,5,6)
                    WHERE
                        pp.StatusCode = 'active' AND p.StatusCode='active'
                        AND p.SupplychainID = ?
                        AND p.ProcessingDate BETWEEN ? AND ?
                        AND d.DespatchID IS NOT NULL
                    GROUP BY dd.DespatchDetailID
                ) dt";
        $query_mill_despatch1 = $this->db->query($sql_mill_despatch_received, array($SupplychainID, $get['awal'], $get['akhir']));
        $query_mill_despatch = $query_mill_despatch1->row_array(0);

        if($query_mill_despatch1->num_rows() > 0 && count($date_range) > 0){
            $series_despatch[0]['name'] = 'CPO';
            $series_despatch[1]['name'] = 'PK';
            foreach($categories_month as $k => $v){
                $series_despatch[0]['data'][] = round((floatval($query_mill_despatch["despatch_cpo_$v"])/1000), 3);
                $series_despatch[1]['data'][] = round((floatval($query_mill_despatch["despatch_pk_$v"])/1000), 3);
            }
        }

        $sql_mill_remaining = "SELECT
                SUM(IF(pp.ProductID='1', pp.RemainingVolume, 0)) AS total_stock_cpo,
                SUM(IF(pp.ProductID='2', pp.RemainingVolume, 0)) AS total_stock_pk
            FROM
                `ktc_tc_processing_product` pp 
                LEFT JOIN ktv_tc_processing p ON p.ProcessingID=pp.ProcessingID
            WHERE
                pp.StatusCode = 'active' AND p.StatusCode='active'
                AND p.SupplychainID = ?";
        $query_mill_remaining = $this->db->query($sql_mill_remaining, array($SupplychainID))->row_array(0);

        $sql_mill_transaction_dealer = "SELECT 
                                    dt.suppliername AS `name`,
                                    SUM(dt.weight) AS `y`
                                FROM
                                    ($dt_mill_transaction) dt
                                WHERE dt.supplierid IS NOT NULL
                                GROUP BY dt.supplierid";
        $query_mill_transaction_dealer = $this->db->query($sql_mill_transaction_dealer, array(
            $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village'],
            $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village']
        ));
        if($query_mill_transaction_dealer->num_rows() > 0){
            $i = 0;
            foreach ($query_mill_transaction_dealer->result_array() as $key => $value) {
                $traceable_sales_pie[$i]['name'] = $value['name'];
                $traceable_sales_pie[$i]['y'] = round((floatval($value['y'])/1000), 2);
                $i++;
            }
        }else{
            $traceable_sales_pie = array();
        }

        $series_transaction = array();
        $series_volume = array();
        if(count($date_range) > 0){
            $select = "";
            foreach ($date_range as $key => $value) {
                //$categories_month[] = $value;
                $select .= ", SUM(IF(SUBSTR(dt.DateTransaction, 1, 7)='$value', dt.weight, 0)) AS `volume_$value`";
                $select .= ", COUNT(DISTINCT IF(SUBSTR(dt.DateTransaction, 1, 7)='$value', dt.id, NULL)) AS `transaction_$value`";
            }
            $sql_mill_transaction_dealer_detail = "SELECT 
                                        dt.suppliername AS `supplier`
                                        $select
                                    FROM
                                        ($dt_mill_transaction) dt
                                    WHERE dt.supplierid IS NOT NULL
                                    GROUP BY dt.supplierid";
            $query_mill_transaction_dealer_detail = $this->db->query($sql_mill_transaction_dealer_detail, array(
                $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village'],
                $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village']
            ));
            //echo '<pre>'; echo json_encode($categories_month); echo '</pre>'; 
            //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            if($query_mill_transaction_dealer_detail->num_rows() > 0){
                $i = 0;
                foreach ($query_mill_transaction_dealer_detail->result_array() as $key => $value) {
                    $series_transaction[$i]['name'] = $value['supplier'];
                    $series_volume[$i]['name'] = $value['supplier'];
                    $series_transaction[$i]['data'] = array();
                    $series_volume[$i]['data'] = array();
                    foreach($categories_month as $k => $v){
                        $series_transaction[$i]['data'][] = intval($value["transaction_$v"]);
                        $series_volume[$i]['data'][] = round((floatval($value["volume_$v"])/1000), 2);
                    }
                    $i++;
                }
            }else{
                $series_transaction = array();
                $series_volume = array();
            }
        }else{
            $series_transaction = array();
            $series_volume = array();
        }

        $sql_mill_ffb_traceability_percentage = "
            SELECT
                SUM(IF(dtx.MemberID > 0 AND dtx.PlantationNr > 0, dtx.TotalCapacity, 0)) AS traceable_to_plantation,
                SUM(IF(dtx.MemberID > 0 AND (dtx.PlantationNr <= 0 OR dtx.PlantationNr IS NULL), dtx.TotalCapacity, 0)) AS traceable_to_farmer,
                SUM(IF(dtx.MemberID > 0, 0, dtx.TotalCapacity)) AS traceable_to_agent
            FROM 
                (
                    SELECT 
                        dt.*,
                        SUM(std.TotalCapacity) AS TotalCapacity
                    FROM
                        (
                            SELECT
                                sd.DeliveryID, 
                                sd.TotalWeight,
                                st0.SupplyTransID,
                                st0.DateTransaction,
                                sd.DeliveryDate,
                                st0.VolumeBruto, 
                                st0.VolumeNetto,
                                m.MemberID,
                                IF(st_2.SupplyTransID > 0, st_2.PlantationNr, st.PlantationNr) AS PlantationNr
                            FROM
                                ktv_tc_supplychain_delivery sd
                                    LEFT JOIN ktv_tc_supplychain_transaction st0 ON sd.DeliveryID=st0.SupplyID AND st0.SupplyType='Delivery' AND st0.SupplyID > 0 AND st0.StatusCode='active'
                                LEFT JOIN ktv_tc_supplychain_delivery_detail sdd ON sdd.DeliveryID=sd.DeliveryID
                                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=sdd.SupplyBatchID
                                LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                                
                                LEFT JOIN ktv_tc_supplychain_delivery sd_2 ON sd_2.DeliveryID=st.SupplyID AND st.SupplyType='Delivery' AND st.SupplyID > 0
                                LEFT JOIN ktv_tc_supplychain_delivery_detail sdd_2 ON sdd_2.DeliveryID=sd_2.DeliveryID
                                LEFT JOIN ktv_tc_supplychain_batch sb_2 ON sb_2.SupplyBatchID=sdd_2.SupplyBatchID
                                LEFT JOIN ktv_tc_supplychain_transaction st_2 ON st_2.SupplyBatchID=sb_2.SupplyBatchID
                                LEFT JOIN ktv_members m ON m.MemberID!=0 AND m.MemberID=IFNULL(st_2.SupplyID, st.SupplyID) AND IFNULL(st_2.SupplyType,st.SupplyType)='Farmer'
                            WHERE
                                sd.StatusCode='active'
                                AND sd.SupplyDestMillOrgID=?
                                AND st0.SupplyTransID IS NOT NULL
                                AND `sd`.`DeliveryStatusID` IN (4, 5, 6)
                                AND DATE_FORMAT(IFNULL(st0.DateTransaction, sd.DeliveryDate), '%Y-%m-%d') BETWEEN ? AND ?
                            GROUP BY sd.DeliveryID
                            ORDER BY DeliveryID, MemberID DESC, PlantationNr DESC
                        ) dt
                        LEFT JOIN ktv_tc_supplychain_transaction_detail std ON std.SupplyTransID=dt.SupplyTransID
                    GROUP BY dt.DeliveryID
                ) dtx";
        $query_mill_ffb_traceability_percentage = $this->db->query($sql_mill_ffb_traceability_percentage, array(
            $SupplychainID, $get['awal'], $get['akhir']
        ))->row_array(0);


        
        $return['data'] = array(
            'total_farmer' => $query_farmer['total_farmer'],
            'total_dealer' => $query_agent['total_dealer'],
            'total_number_of_farmer_selling' => $query_farmer_transaction['total_number_of_farmer_selling'],
            'total_number_of_transaction_from_farmers' => round((floatval($query_farmer_transaction['total_number_of_transaction_from_farmers'])/1000), 2),
            'total_number_transactions_from_dealer' => $query_mill_transaction['total_number_transactions_from_dealer'],
            'total_number_of_plantation_numbers' => round((floatval($query_farmer_transaction['total_number_of_plantation_numbers'])/1000), 2),
            'traceable_volume_received_at_mill' => round((floatval($query_mill_transaction['traceable_volume_received_at_mill'])/1000), 2),
            'traceable_volume_farmer_plasma' => round((floatval($query_mill_transaction['traceable_volume_farmer_plasma'])/1000), 2),
            'traceable_volume_direct_smallholder' => round((floatval($query_mill_transaction['traceable_volume_direct_smallholder'])/1000), 2),
            'traceable_volume_agent_dealer' => round((floatval($query_mill_transaction['traceable_volume_agent_dealer'])/1000), 2),
            'traceable_volume_owned_estate' => round((floatval($query_mill_transaction['traceable_volume_owned_estate'])/1000), 2),
            'traceable_volume_external_estate' => round((floatval($query_mill_transaction['traceable_volume_external_estate'])/1000), 2),
            'traceable_volume' => round((floatval($query_mill_transaction['traceable_volume'])/1000), 2),
            'untraceable_volume' => round((floatval($query_mill_transaction['untraceable_volume'])/1000), 2),
            'total_cpo_production' => round((floatval($query_mill_production['total_cpo_production'])/1000), 2),
            'total_pk_production' => round((floatval($query_mill_production['total_pk_production'])/1000), 2),
            'total_number_of_cpo_refinery_transaction' => $query_mill_despatch['total_number_of_cpo_refinery_transaction'],
            'total_number_of_pk_refinery_transaction' => $query_mill_despatch['total_number_of_pk_refinery_transaction'],
            'total_stock_cpo' => round((floatval($query_mill_remaining['total_stock_cpo'])/1000), 2),
            'total_stock_pk' => round((floatval($query_mill_remaining['total_stock_pk'])/1000), 2),
            'total_traceable_cpo_dispatched_to_refinery' => round((floatval($query_mill_despatch0['total_traceable_cpo_dispatched_to_refinery'])/1000), 3),
            'total_traceable_pk_dispatched_to_refinery' => round((floatval($query_mill_despatch0['total_traceable_pk_dispatched_to_refinery'])/1000), 3),
            'total_traceable_cpo_received_to_refinery' => round((floatval($query_mill_despatch['total_traceable_cpo_received_to_refinery'])/1000), 3),
            'total_traceable_pk_received_to_refinery' => round((floatval($query_mill_despatch['total_traceable_pk_received_to_refinery'])/1000), 3),
            'traceable_sales_pie' => $traceable_sales_pie,
            'categories_month' => $categories_month,
            'series_transaction' => $series_transaction,
            'series_volume' => $series_volume,
            'series_production' => $series_production,
            'series_despatch' => $series_despatch,
            'traceable_to_plantation' => round((floatval($query_mill_ffb_traceability_percentage['traceable_to_plantation'])/1000), 2),
            'traceable_to_farmer' => round((floatval($query_mill_ffb_traceability_percentage['traceable_to_farmer'])/1000), 2),
            'traceable_to_agent' => round((floatval($query_mill_ffb_traceability_percentage['traceable_to_agent'])/1000), 2),
        );
        // echo "<pre>".print_r($return, 1);die;
        // var_dump($return);exit;
        return $return;
    }

    public function GettransactionSupplierNew($get)
    {   
        //echo "<pre>".print_r($get, 1);
        if(intval($get['district']) > 0){
            $dis1 = ""; $dis2 = "";
        }else{
            $dis1 = "/*"; $dis2 = "*/";
        }

        if(intval($get['subdistrict']) > 0){
            $kec1 = ""; $kec2 = "";
        }else{
            $kec1 = "/*"; $kec2 = "*/";
        }

        if(intval($get['village']) > 0){
            $des1 = ""; $des2 = "";
        }else{
            $des1 = "/*"; $des2 = "*/";
        }

        @$org = $this->db->query("SELECT * FROM view_tc_supplychain_org vtso WHERE vtso.PartnerID=? AND vtso.ObjType = 'mill'", array($_SESSION['PartnerID']))->row_array(0);
        $SupplychainID = $org['SupplychainID'];
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'supplierid ' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? '' : $order[0]['direction'];
        $keyword = "%".$get['keyword_filter']."%";

        $year1 = intval(@$get['year_filter']) > 0 ? '' : '/*';
        $year2 = intval(@$get['year_filter']) > 0 ? '' : '*/';

        $p1 = intval(@$get['province_filter']) > 0 ? '' : '/*';
        $p2 = intval(@$get['province_filter']) > 0 ? '' : '*/';

        $d1 = intval(@$get['district_filter']) > 0 ? '' : '/*';
        $d2 = intval(@$get['district_filter']) > 0 ? '' : '*/';
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    dt.supplierid AS SupplierID, 
                    dt.suppliername AS SupplierName,
                    dt.District,
                    dt.SubDistrict,
                    SUM(dt.weight) AS totalffbreceived
                FROM
                    (
                        SELECT
                            CONCAT('D',sdel.DeliveryID) AS id,
                            SUM(sdd.Weight) AS weight,
                            IFNULL(vso.DisplayID, vso.SupplychainID) AS supplierid,
                            vso.`Name` AS suppliername,
                            3 AS SupplybaseCategoryID,
                            'Traceable' AS Traceability,
                            sdel.DeliveryDate AS DateTransaction,
                            d.District,
                            sd.SubDistrict
                        FROM
                            ktv_tc_supplychain_delivery sdel
                            LEFT JOIN ktv_tc_supplychain_delivery_detail sdd ON sdd.DeliveryID=sdel.DeliveryID
                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sdel.SupplychainID
                            LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        WHERE
                            sdel.SupplyDestMillOrgID=?
                            AND sdel.StatusCode='active'
                            AND DATE_FORMAT(sdel.DeliveryDate, '%Y-%m-%d') BETWEEN ? AND ?
                            /* AND d.DistrictID=? */
                            /* AND sd.SubDistrictID=? */
                            /* AND v.VillageID=? */
                            $year1 AND SUBSTR(sdel.DeliveryDate, 1, 4)= ? $year2
                            $p1 AND d.ProvinceID=? $p2
                            $d1 AND d.DistrictID=? $d2
                        GROUP BY sdel.DeliveryID
                        UNION
                        SELECT
                            CONCAT('T', st.SupplyTransID) AS id,
                            st.VolumeNetto AS weight,
                            IFNULL(vso2.DisplayID, vso2.SupplychainID) AS supplierid,
                            vso2.`Name` AS suppliername,
                            IFNULL(st.SupplybaseCategoryID, '3') AS SupplybaseCategoryID,
                            IF(vso2.SupplychainID IS NOT NULL, 'Traceable', 'Untraceable')  AS Traceability,
                            st.DateTransaction,
                            d.District,
                            sd.SubDistrict
                        FROM
                            ktv_tc_supplychain_transaction st
                            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' 
                                AND (st.SupplyBatchType IS NULL OR st.SupplyBatchType!='Untraceable') AND st.SupplyID!=st.SupplychainID
                            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IFNULL(sb.SupplyOrgID, IF(st.MillID > 0, st.MillID, IF(st.DOID > 0, st.DOID, st.AgentID)))
                            LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        WHERE
                            st.StatusCode='active'
                            AND vso.ObjType='mill'
                            AND vso.SupplychainID=?
                            AND st.SupplyType!='' AND st.SupplyType!='Delivery'
                            AND sb.SupplyBatchID IS  NULL
                            AND DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') BETWEEN ? AND ?
                            /* AND d.DistrictID=? */
                            /* AND sd.SubDistrictID=? */
                            /* AND v.VillageID=? */
                            $year1 AND SUBSTR(st.DateTransaction, 1, 4)= ? $year2
                            $p1 AND d.ProvinceID=? $p2
                            $d1 AND d.DistrictID=? $d2
                    ) dt
                WHERE dt.supplierid IS NOT NULL
                    AND (
                        dt.supplierid LIKE ?
                        OR dt.suppliername LIKE ?
                        OR dt.District LIKE ?
                        OR dt.SubDistrict LIKE ?
                    )
                GROUP BY dt.supplierid
                ORDER BY $order_by $sort
                LIMIT ?,?";
        $query = $this->db->query($sql, array(
            $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village'], @$get['year_filter'], @$get['province_filter'], @$get['district_filter'],
            $SupplychainID, $get['awal'], $get['akhir'], $get['district'], $get['subdistrict'], $get['village'], @$get['year_filter'], @$get['province_filter'], @$get['district_filter'],
            $keyword, $keyword, $keyword, $keyword,
            intval($get['start']), intval($get['limit'])));
        //echo "<pre>".$this->db->last_query();exit; 
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                );
        }else{
            return array(
                'data'          => 0,
                'total'         => 0,
                );
        }
    }

}

?>