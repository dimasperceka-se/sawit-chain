<?php
class Msupplychain_relation extends CI_Model {
    
    public function generateSupplychainRelation($get){
        $relation = array();
        $sql_parent = "SELECT
                            org1.SupplychainID AS id,
                            org1.ObjType,
                            org1.ObjID,
                            CASE org1.ObjType WHEN 'refinery' THEN r.RefineryName WHEN 'mill' THEN m.MillName WHEN 'agent' THEN mb.MemberName ELSE '-' END  AS `Name`,
                            CASE org1.ObjType WHEN 'refinery' THEN r.CompanyName WHEN 'mill' THEN m.CompanyName WHEN 'agent' THEN mx.agCompanyName ELSE '-' END  AS CompanyName,
                            0 AS ParentID
                        FROM
                            ktv_tc_supplychain_org org1
                            LEFT JOIN ktv_refinery r ON org1.ObjType='refinery' AND r.RefineryID=org1.ObjID
                            LEFT JOIN ktv_mill m ON org1.ObjType='mill' AND m.MillID=org1.ObjID
                            LEFT JOIN ktv_members mb ON org1.ObjType='agent' AND mb.MemberID=org1.ObjID
                            LEFT JOIN ktv_members_extension mx ON mx.MemberID=mb.MemberID
                        WHERE
                            org1.StatusCode='active'
                            AND org1.SupplychainID=?";
        $parent = $this->db->query($sql_parent, array(@$get['SupplychainID']));
        if($parent->num_rows > 0){
            foreach($parent->result() as $row){
                $this->getRelationBySupplychainID($row->id, $relation);
                $this->getRelationSupplychainIDxFarmerID($row->id, $relation);
            }
        }

        return $relation;
    }
    
    function getRelationBySupplychainID($SupplychainID, &$relation){
        $sql = "SELECT
                    org1.SupplychainID AS id,
                    org1.ObjType,
                    org1.ObjID,
                    CASE org1.ObjType WHEN 'refinery' THEN r.RefineryName WHEN 'mill' THEN m.MillName WHEN 'agent' THEN mb.MemberName ELSE '-' END  AS `Name`,
                    CASE org1.ObjType WHEN 'refinery' THEN r.CompanyName WHEN 'mill' THEN m.CompanyName WHEN 'agent' THEN mx.agCompanyName ELSE '-' END  AS CompanyName,
                    rel1.ParentID
                FROM
                    ktv_tc_supplychain_org_rel rel1
                    LEFT JOIN ktv_tc_supplychain_org org1 ON org1.SupplychainID=rel1.ChildID
                    LEFT JOIN ktv_refinery r ON org1.ObjType='refinery' AND r.RefineryID=org1.ObjID
                    LEFT JOIN ktv_mill m ON org1.ObjType='mill' AND m.MillID=org1.ObjID
                    LEFT JOIN ktv_members mb ON org1.ObjType='agent' AND mb.MemberID=org1.ObjID
                    LEFT JOIN ktv_members_extension mx ON mx.MemberID=mb.MemberID
                WHERE
                    rel1.StatusCode='active'
                    AND rel1.ParentID = ?";
        $query = $this->db->query($sql, array($SupplychainID));
        if($query->num_rows() > 0){
            //$relation[] = $query->result_array();
            $relation = array_merge($relation, $query->result_array());
            foreach($query->result() as $row){
                $this->getRelationBySupplychainID($row->id, $relation);
                $this->getRelationSupplychainIDxFarmerID($row->id, $relation);
            }
        }else{
            return false;
        }
        return true;
    }

    function getRelationSupplychainIDxFarmerID($SupplychainID, &$relation){
        $sql = "SELECT
                    m.MemberDisplayID AS id,
                    'farmer' AS ObjType,
                    m.MemberID AS ObjID,
                    m.MemberName AS `Name`,
                    '' AS CompanyName,
                    ? AS ParentID
                FROM
                    (
                        SELECT SupplychainID, FarmerID FROM ktv_tc_supplychain_farmer_rel_sales WHERE SupplychainID=?
                        UNION
                        SELECT DISTINCT SupplychainID, FarmerID FROM ktv_tc_supplychain_farmer WHERE StatusCode='active' AND SupplychainID=?
                    ) dt
                    LEFT JOIN ktv_members m ON m.MemberID=dt.FarmerID AND m.StatusCode='active'
                WHERE
                    m.MemberID IS NOT NULL
                GROUP BY m.MemberID";
        $query = $this->db->query($sql, array($SupplychainID, $SupplychainID, $SupplychainID));
        if($query->num_rows() > 0){
            $relation = array_merge($relation, $query->result_array());
        }else{
            return false;
        }
        return true;
    }
}

?>
