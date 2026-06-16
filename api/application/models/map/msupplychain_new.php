<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Msupplychain_new extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
    }   

    public function getSupplyChain($start, $end, $id = NULL)
    {
        $sql = "
SELECT
    vsop.SupplychainID parent_id,
    IF(vsop.MRoleID IN (7,9), 'do', vsop.ObjType) parent_type,
    vsop.`Name` parent_name,
    vsop.DisplayID parent_displayid,
    vsop.VillageID parent_villageid,
    vsop.Latitude parent_latitude,
    vsop.Longitude parent_longitude,
    COUNT(DISTINCT st.SupplyTransID) total_transaction,
    SUM(st.VolumeNetto) total_netto_kg,
    tp.SupplybaseCategoryID,
    tp.CategoryName,
    
    IFNULL(vsoc.SupplychainID, m.MemberDisplayID) child_id,
    m.MemberID AS child_member_id,
    st.PlantationNr child_plantation,
    IF(vsoc.MRoleID IN (7,9), 'do', IFNULL(vsoc.ObjType, IF(m.MemberID > 0, 'farmer', NULL))) child_type,
    IFNULL(vsoc.`Name`, m.MemberName) child_name,
    IFNULL(vsoc.DisplayID, m.MemberDisplayID) child_displayid,
    IFNULL(vsoc.VillageID, m.VillageID) child_villageid,
    IFNULL(vsoc.Latitude, ps.Latitude) child_latitude,
    IFNULL(vsoc.Longitude, ps.Longitude) child_longitude
FROM
    ktv_tc_supplychain_transaction st
    LEFT JOIN view_tc_supplychain_org vsop ON vsop.SupplychainID=st.SupplychainID
    LEFT JOIN ref_tc_supplybase_category tp ON tp.SupplybaseCategoryID=st.SupplybaseCategoryID
    
    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID AND st.SupplyType='Batch' AND (st.SupplyBatchType IS NULL OR st.SupplyBatchType!='Untraceable')
    LEFT JOIN view_tc_supplychain_org vsoc ON st.SupplyType='Batch' AND vsoc.SupplychainID=IF(sb2.SupplyOrgID > 0, sb2.SupplyOrgID, IF(st.DOID > 0 , st.DOID, st.AgentID))
    
    LEFT JOIN ktv_survey_plot_core_mill plotm ON st.SupplyType='Nonfarmer' AND vsop.ObjType='mill' AND vsop.ObjID=plotm.MillID AND st.PlantationNr=plotm.PlotNr
    LEFT JOIN ktv_survey_plot_sme plots ON st.SupplyType='Nonfarmer' AND vsop.ObjType='agent' AND vsop.ObjID=plots.MemberID AND st.PlantationNr=plots.PlotNr
    LEFT JOIN ktv_members m ON st.SupplyType='Nonfarmer' AND st.SupplyID > 0 AND st.SupplyID=m.MemberID
    LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID=m.MemberID AND ps.PlotNr=st.PlantationNr
WHERE 1=1
    AND vsop.SupplychainID=?
    AND st.DateTransaction BETWEEN ? AND ?
GROUP BY tp.SupplybaseCategoryID, IFNULL(vsoc.SupplychainID, m.MemberDisplayID)";
        $result = $this->db->query($sql, array($id, $start, $end))->result_array();
        return $result;
    }

    public function getSupplyProfileMill($id, $start, $end, $wh)
    {
        $sql = "SELECT
                    vso.DisplayID id, vso.`Name` name, 
                    COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    SUM(IFNULL(st.VolumeBruto,0)) bruto,
                    SUM(st.VolumeNetto) netto
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                WHERE
                    st.SupplychainID = ?
                    AND st.SupplyType='Batch'
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ?";
        $query = $this->db->query($sql, array($wh, $start, $end));

        if ($query->num_rows()>0) {
            $ret = $query->row_array(0);
            if($ret['id']==''){
                $whouse = $this->db->query("SELECT * FROM ktv_mill WHERE MillID=?", array($wh))->row_array(0);
                $ret['id'] = $whouse['MillDisplayID'];
                $ret['name'] = $whouse['MillName'];
            }
            return $ret;
        }
        return false;
    }

    public function getSupplyProfileDo($id, $start, $end, $wh)
    {
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
                        IFNULL(org.DisplayID, ObjID) id, 
                        org.`Name` name, 
                        st.SupplyTransID,
                        st.SupplyBatchID,
                        IF(st.SupplyBatchID IS NOT NULL, st.SupplyBatchID, NULL) delivered_id,
                        st.VolumeBruto bruto,
                        IF(st.SupplyType='Farmer', st.SupplyID, NULL) farmer_id,
                        st.VolumeNetto netto
                    FROM
                        view_tc_supplychain_org org 
                        LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID AND DateTransaction BETWEEN ? AND ?
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
                        LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchID AND st3.SupplyType='Batch'
                    WHERE org.SupplychainID=? AND org.ObjType='agent' AND (st2.SupplychainID=? OR st3.SupplychainID=?)
                    GROUP BY st.SupplyTransID
                ) dt";
        $query = $this->db->query($sql, array("$start 00:00:00", "$end 23:59:59", $id, $wh, $wh));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
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
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                    LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
                    LEFT JOIN ktv_survey_plot plot ON plot.MemberID=m.MemberID AND plot.PlotNr=st.PlantationNr
                    LEFT JOIN ktv_village v ON v.VillageID=IFNULL(plot.VillageID, m.VillageID)
                    
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyType='Batch' AND st2.SupplyID=sb.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
                    
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyType='Batch' AND st3.SupplyID=sb2.SupplyBatchID
                    LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
                WHERE
                    st.SupplyType='Farmer'
                    AND vso.SupplychainID=?
                    AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ?
                    AND m.MemberDisplayID=?
                    AND st.PlantationNr=?
                    AND (IFNULL(sb3.SupplyOrgID, IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID))=? OR IFNULL(sb2.SupplyOrgID, IFNULL(st2.SupplychainID, sb.SupplyDestOrgID))=?)";
        $query = $this->db->query($sql, array($parent, $start, $end, $i[0], $i[1], $wh, $wh));
        
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

}

/* End of file msupplychain_new.php */
/* Location: ./application/models/msupplychain_new.php */