<?php
class Msupplychain_new extends CI_Model
{

    public function getSupplyChain($start, $end, $province = '', $partner = '', $certification = '1', $warehouse = NULL)
    {
        $sql = "
                SELECT DISTINCT
                    '' 1_supplychainid, '' 1_orgid, '' 1_orgtype, '' 1_name, IFNULL(vso2.Latitude,vso.Latitude) 1_latitude, IFNULL(vso2.Longitude,vso.Longitude) 1_longitude,
                    vso2.SupplychainID 2_supplychainid, vso2.OrgID 2_orgid, so2.OrgType 2_orgtype, vso2.`Name` 2_name, IFNULL(vso2.Latitude,vso.Latitude) 2_latitude, IFNULL(vso2.Longitude,vso.Longitude) 2_longitude,
                    vso.SupplychainID wh_supplychainid, vso.OrgID wh_orgid, so.OrgType wh_orgtype, vso.`Name` wh_name, vso.Latitude wh_latitude, vso.Longitude wh_longitude
                FROM
                    ktv_td_supplychain_batch sb1
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=sb1.SupplyOrgID
                    LEFT JOIN ktv_td_supplychain_transaction st1 ON st1.SupplyBatchID=sb1.SupplyBatchID
                    LEFT JOIN ktv_td_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st1.SupplyID AND st1.SupplyType='Batch'
                    LEFT JOIN ktv_td_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb2.SupplyOrgID
                    LEFT JOIN ktv_td_supplychain_org so2 ON so2.SupplychainID=vso2.SupplychainID
                    LEFT JOIN ktv_td_supplychain_org so ON so.SupplychainID=vso.SupplychainID
                    LEFT JOIN ktv_village v2 ON v2.VillageID=vso2.VillageID
                WHERE vso.OrgID=? AND IFNULL(st2.DateTransaction,st1.DateTransaction) BETWEEN ? AND ?
        ";
        //$result = $this->db->query($sql, array($warehouse, $start, $end))->result_array();
        $sql= "SELECT
                    o3.SupplychainID 1_supplychainid, o3.OrgID 1_orgid, IF(o3.OrgType='agent', 'trader', o3.OrgType) 1_orgtype, o3.`Name` 1_name, o3.Latitude 1_latitude, o3.Longitude 1_longitude,
                    o2.SupplychainID 2_supplychainid, o2.OrgID 2_orgid, IF(o2.OrgType='agent', 'trader', o2.OrgType) 2_orgtype, o2.`Name` 2_name, o2.Latitude 2_latitude, o2.Longitude 2_longitude,
                    o1.SupplychainID wh_supplychainid, o1.OrgID wh_orgid, 'warehouse' wh_orgtype, o1.`Name` wh_name, o1.Latitude wh_latitude, o1.Longitude wh_longitude
                FROM
                    ktv_td_supplychain_org_rel r1
                    LEFT JOIN view_tc_supplychain_org o1 ON o1.SupplychainID=r1.ParentOrgId
                    LEFT JOIN view_tc_supplychain_org o2 ON o2.SupplychainID=r1.ChildOrgId
                    LEFT JOIN ktv_td_supplychain_org_rel r2 ON r2.ParentOrgId=r1.ChildOrgId
                    LEFT JOIN view_tc_supplychain_org o3 ON o3.SupplychainID=r2.ChildOrgId
                WHERE r1.ParentOrgId=? AND NOW() BETWEEN r2.StartDate AND r2.EndDate AND NOW() BETWEEN r1.StartDate AND r1.EndDate";
        $result = $this->db->query($sql, array($warehouse))->result_array();
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
                    CONCAT(IFNULL(m.MemberDisplayID, nf.FarmerID),'_',IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)) id,
                    IFNULL(m.MemberName, nf.FarmerName) name, 
                    IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr) PlotNr,
                    '-' CPGid, 
                    IFNULL(m.Latitude, IFNULL(plot.Latitude, tm.Latitude)) latitude, 
                    IFNULL(m.Longitude, IFNULL(plot.Longitude, tm.Longitude)) longitude,
                    SUM(st.VolumeBruto1) bruto, SUM(st.VolumeNetto) netto,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT st.SupplyID) supply_count,
                    COUNT(DISTINCT st.SupplyTransID) transaction_count
                FROM
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_td_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID OR m.MemberDisplayID=st.SupplyID
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)
                    LEFT JOIN ktv_td_supplychain_non_farmer nf ON nf.FarmerID=st.SupplyID
                    LEFT JOIN ktv_temp_member_plot tm ON tm.MemberDisplayID=IFNULL(m.MemberDisplayID,nf.FarmerID) AND tm.PlotNr=IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)
                WHERE 1=1  
                    AND vso.OrgType = 'agent' AND vso.OrgID = ? 
                    AND st.DateTransaction BETWEEN ? AND ?
                GROUP BY CONCAT(IFNULL(m.MemberDisplayID, nf.FarmerID),'_',IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr))
                HAVING longitude IS NOT NULL";
        $query = $this->db->query($sql, array($id, $start, $end));
        $result = $query->result_array();
        
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        return $result;
    }

    public function getDistricts()
    {
        $query = $this->db->get('ktv_district');
        return $query->result_array();
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
        $sql = "
        SELECT
            so.SupplychainID
            , so.Name
        FROM view_tc_supplychain_org so
        WHERE
            PartnerID = ?
        ";
        $query = $this->db->query($sql, array($_SESSION['PartnerID']));
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
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_td_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID OR m.MemberDisplayID=st.SupplyID
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)
                    LEFT JOIN ktv_td_supplychain_non_farmer nf ON nf.FarmerID=st.SupplyID
                    LEFT JOIN ktv_temp_member_plot tm ON tm.MemberDisplayID=IFNULL(m.MemberDisplayID,nf.FarmerID) AND tm.PlotNr=IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)
                    LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, IFNULL(m.VillageID, nf.FarmerVillageID))
                WHERE 1=1  
                    AND vso.OrgType = 'agent' AND vso.OrgID = ? 
                    AND st.DateTransaction BETWEEN ? AND ? 
                    AND IFNULL(m.MemberDisplayID, nf.FarmerID)=? AND IF(st.PlotNr IS NULL || st.PlotNr=0, 1, st.PlotNr)=?";
        $query = $this->db->query($sql, array($parent, $start, $end, $i[0], $i[1]));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->row_array(0);
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
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_td_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
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
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_td_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
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
                    view_tc_supplychain_org org 
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
                    LEFT JOIN ktv_td_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN ktv_td_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchNumber
                    LEFT JOIN ktv_td_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_td_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
                WHERE org.OrgID=? AND org.OrgType='agent' AND (st2.SupplychainID=? OR st3.SupplychainID=?)";
        $query = $this->db->query($sql, array("$start 00:00:00", "$end 23:59:59", $id, $wh, $wh));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->row_array(0);
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
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_td_supplychain_batch sb ON sb.SupplyOrgID=vso.SupplychainID
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplyBatchID=sb.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                    LEFT JOIN ktv_td_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchNumber
                    LEFT JOIN ktv_td_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    LEFT JOIN ktv_td_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchNumber
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
                    view_tc_supplychain_org org
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
                    view_tc_supplychain_org org
                    LEFT JOIN rpt_traceability r ON r.2_orgid=org.OrgID AND r.2_orgtype='koperasi' AND 2_date BETWEEN ? AND ? AND r.farmer_iscertified=? AND (2_destorgid IS NULL OR 2_destorgid=? OR wh_orgid IS NULL OR wh_orgid=?)
                    LEFT JOIN ktv_td_supplychain_batch ksb ON ksb.SupplyBatchID=2_batchid
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
                    view_tc_supplychain_org org
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
                    view_tc_supplychain_org org
                    LEFT JOIN sce_farmer sce ON sce.SceID=org.OrgID
                    LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=sce.FarmerID
                    LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
                    LEFT JOIN rpt_traceability r ON (r.1_supplychainid=org.SupplychainID OR r.2_supplychainid=org.SupplychainID) AND IF(r.1_supplychainid=org.SupplychainID, r.1_date, r.2_date) BETWEEN ? AND ? AND r.farmer_iscertified=? AND (2_destorgid IS NULL OR 2_destorgid=? OR wh_orgid IS NULL OR wh_orgid=?)
                    LEFT JOIN ktv_td_supplychain_batch ksb ON ksb.SupplyBatchID=IFNULL(1_batchid,2_batchid)
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
                    view_tc_supplychain_org org 
                    LEFT JOIN ktv_td_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
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
                    LEFT JOIN view_tc_supplychain_org c ON c.OrgID=a.OrgID
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
