<?php
class Mcocoa_price extends CI_Model {

    public function readCocoaPrices($prov, $key, $dateStart, $dateEnd, $start = 0, $limit = 50){
        $dateStart = substr($dateStart, 0,10);
        $dateEnd = substr($dateEnd, 0,10);
        IF($dateStart!=''&&$dateEnd!=''){
            $date = "AND a.CocoaPriceDate BETWEEN '{$dateStart} 00:00:00' AND '{$dateEnd} 23:59:59'";
        }else{
            $date = "";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.*, b.District, Format(a.CocoaPrice, '##,##0')  AS CocoaPrice
                FROM ktv_price a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
                WHERE 1 = 1 AND a.DistrictID LIKE %s AND b.District LIKE %s %s ORDER BY a.CocoaPriceDate DESC, District, CocoaPriceID DESC
                LIMIT ?, ?";
        $query = $this->db->query(sprintf($sql, "'{$prov}%'", "'%{$key}%'", $date), array(intval($start), intval($limit)));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }

    function readKabupatens($provID='',$partnerID){
        $sql_where = "and DistrictID not in (1171,7373,7271,1377,7371)";
        if($partnerID != 'ALL'){
            $sql_where2 = " AND DistrictID in (SELECT DistrictID FROM ktv_district_partner WHERE PartnerID = {$partnerID})";
        }
        $sql = "SELECT distinct District as label, DistrictID as id
                FROM ktv_district a
                    LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                WHERE
                    a.ProvinceID = ? OR b.Province = ? %s %s
                ORDER BY District";
        $query = $this->db->query(sprintf($sql,$sql_where,@$sql_where2), array($provID,$provID));

        $result['data'] = $query->result_array();
        return $result;
    }

    function readProvinsis($sesPartner){
        if($sesPartner != 'ALL'){
            $join = 'LEFT JOIN ktv_cpg_partner b ON SUBSTR(b.CPGid,1,2) = a.ProvinceID';
            $where = " AND b.PartnerID = {$sesPartner} ";
        }
        $sql = "SELECT
                    distinct a.Province as label,
                    a.ProvinceID as id
                FROM
                    ktv_province a
                    $join
                WHERE
                    a.ProvinceID not in (12,31)
                    
                ORDER BY a.Province";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function readProvinceName($prov){
        $sql = "SELECT Province FROM ktv_province WHERE ProvinceID=?";
        $query = $this->db->query($sql, array($prov))->result_array();
        return $query[0];        
    }

    public function create_cocoa_price($CocoaPriceDate, $DistrictID, $Type, $CocoaPrice){
        $sql = "INSERT INTO ktv_price (CocoaPriceDate, DistrictID, Type, CocoaPrice, DateCreated, CreatedBy) VALUES (?,?,?,?,NOW(),?)";
        $query = $this->db->query($sql, array($CocoaPriceDate, $DistrictID, $Type, $CocoaPrice, $_SESSION['userid']));
        if ($query){
            $results['success'] = "true";
            $results['message'] = "Record created.";
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function readCocoaPrice($CocoaPriceID){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.*, b.District, c.Province
                FROM ktv_price a
                    LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
                    LEFT JOIN ktv_province c ON SUBSTRING(a.DistrictID,1,2)=c.ProvinceID
                WHERE a.CocoaPriceID=?";
        $query = $this->db->query($sql, array($CocoaPriceID));
        $result = $query->result_array();
        return $result[0];
    }

    public function update_cocoa_price($CocoaPriceDate, $DistrictID, $Type, $CocoaPrice, $CocoaPriceID){
        $sql = "UPDATE ktv_price SET CocoaPriceDate=?, Type=?, CocoaPrice=?, DateUpdated=NOW(), LastModifiedBy=? WHERE CocoaPriceID=?";
        $query = $this->db->query($sql, array($CocoaPriceDate, $Type, $CocoaPrice, $_SESSION['userid'], $CocoaPriceID));
        if ($query){
            $results['success'] = "true";
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function delete_cocoa_price($CocoaPriceID){
        $sql="DELETE FROM ktv_price WHERE CocoaPriceID=?";
        $query = $this->db->query($sql, array($CocoaPriceID));
        if ($query) {
            $results['success'] = "true";
            $results['message'] = "Deleted";
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

}
?>
