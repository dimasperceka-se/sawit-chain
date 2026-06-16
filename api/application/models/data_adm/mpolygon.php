<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpolygon extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getFarmerPolygon($user, $ProvinceID, $DistrictID, $SubDistrictID, $Keyword)
    {
        $farmers = $this->getFarmerList($ProvinceID, $DistrictID, $SubDistrictID, $user, $Keyword);
        if ($farmers !== false) {
            foreach ($farmers as $key => $farmer) {
                $farmers[$key]['area'] = $this->getFarmerArea($farmer['MemberID'], $farmer['PlotNr'], $farmer['SurveyNr'], $farmer['Revision']);
            }
            return $farmers;
        }
        return false;
    }

    public function updateFarmerPolygon($MemberID,$PlotNr,$SurveyNr,$Last_revision,$area)
    {
        $revision = $Last_revision+1;
        $this->db->trans_start(false);
        // $query = $this->db->query("DELETE FROM ktv_survey_plot_polygon WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ?", array($MemberID, $PlotNr, $SurveyNr));
        
        $sql = "INSERT INTO `ktv_survey_plot_polygon` (
    `MemberID`
    , `PlotNr`
    , `SurveyNr`
    , `OrderNr`
    , `Revision`
    , `Latitude`
    , `Longitude`
    , `StatusCode`
    , `DateCreated`
    , `CreatedBy`
) 
VALUES
";
        $values = array();
        $order = 1;
        foreach ($area as $latlng) {
            $value = "({$MemberID}, {$PlotNr}, {$SurveyNr}, {$order}, {$revision}, {$latlng[0]}, {$latlng[1]}, 'active', NOW(), {$_SESSION['userid']})\n";
            $values[] = $value;
            $order++;
        }
        $sql .= implode(', ', $values);
        // echo '<pre>'; print_r($sql); echo '</pre>';exit;
        $result = $this->db->query($sql);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // if ($result !== false) {
            // $this->area_calc($farmer_id,$garden_nr,$survey_nr);
        // }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->db->trans_complete();
        // return $result;            
        return $this->db->trans_status();
    }
   
    public function getFarmerList($ProvinceID, $DistrictID, $SubDistrictID, $user = null, $Keyword = '')
    {
        // echo '<pre>'; print_r($user); echo '</pre>'; exit;
        $sql = "
SELECT
    m.MemberID
    , m.MemberDisplayID
    , m.MemberName
    , p.PlotNr
    , p.SurveyNr
    , p.GardenAreaHa
    , p.GardenAreaPolygon
    , p.Latitude
    , p.Longitude
    , MAX(pl.Revision) AS Revision
    , IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
    , m.VillageID
FROM ktv_survey_plot_polygon pl
JOIN ktv_survey_plot p ON p.MemberID = pl.MemberID AND p.PlotNr = pl.PlotNr AND p.SurveyNr = pl.SurveyNr
JOIN (
    SELECT
    *
    FROM ktv_members m
    WHERE 1 = 1
        AND m.StatusCode = 'active'
        -- where partner --
) m ON m.MemberID = p.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province pv ON pv.ProvinceID = d.ProvinceID
WHERE
    1 = 1
    -- where --
    AND (p.Latitude IS NOT NULL AND p.Longitude IS NOT NULL)
GROUP BY m.MemberID
    , p.PlotNr
    , p.SurveyNr
        ";
        $where = '';
        $where_partner = '';
        $params = array();
        if (!empty($user) && $user['is_admin'] == 0) {
            $where_partner .= " AND m.MemberID IN (SELECT apm.apmMemberID FROM ktv_access_partner_member apm WHERE apm.apmPartnerID = {$user['PartnerID']})";
        }
        if (!empty($ProvinceID)) {
            $where .= " AND pv.ProvinceID = ?";
            $params[] = $ProvinceID;
        }
        if (!empty($DistrictID)) {
            $where .= " AND d.DistrictID = ?";
            $params[] = $DistrictID;
        }
        if (!empty($SubDistrictID)) {
            $where .= " AND sd.SubDistrictID = ?";
            $params[] = $SubDistrictID;
        }
        if (!empty($Keyword)) {
            $where .= " AND (m.MemberName LIKE '%{$Keyword}%' OR m.MemberDisplayID LIKE '%{$Keyword}%')";
        }
        $sql = str_replace('-- where partner --', $where_partner, $sql);
        $sql = str_replace('-- where --', $where, $sql);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;        
    }

    public function getFarmerArea($MemberID, $PlotNr, $SurveyNr, $Revision)
    {
        $sql = "
SELECT
    pl.Latitude
    , pl.Longitude
FROM ktv_survey_plot_polygon pl
WHERE
    pl.MemberID = ?
    AND pl.PlotNr = ?
    AND pl.SurveyNr = ?
    AND pl.Revision = ?
    AND (pl.Latitude IS NOT NULL AND pl.Longitude IS NOT NULL)
ORDER BY pl.OrderNr
        ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr, $Revision));

        if ($query->num_rows()>0) {
            $return = array();
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                $return[$key][0] = floatval($value['Latitude']);
                $return[$key][1] = floatval($value['Longitude']);
            }
            return $return;
        }
        return false;
    }

    public function getGardenDetail($MemberID, $PlotNr, $SurveyNr, $Revision = 0)
    {
        $sql = "
SELECT
    f.MemberID
    , f.MemberName
    , f.Photo
    , g.PlotNr
    , g.SurveyNr
    , g.GardenAreaHa
FROM ktv_survey_plot g
JOIN ktv_members f ON f.MemberID = g.MemberID
WHERE
    g.MemberID = ?
    AND g.PlotNr = ?
    AND g.SurveyNr = ?
        ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function listProvince($user = null)
    {
        $sql = "SELECT 
    p.ProvinceID AS id
    , p.Province AS label
FROM
    ktv_province p
JOIN (
    SELECT
        kd.ProvinceID AS ProvinceID
    FROM ktv_survey_plot sp
    JOIN ktv_survey_plot_polygon spp ON spp.MemberID = sp.MemberID AND spp.PlotNr = sp.PlotNr AND spp.SurveyNr = sp.SurveyNr
    JOIN ktv_members m ON m.MemberID = sp.MemberID
    LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
    WHERE
        sp.Latitude IS NOT NULL AND sp.Longitude IS NOT NULL 
        AND spp.MemberID
    GROUP BY ProvinceID
) m ON m.ProvinceID = p.ProvinceID
WHERE   
    active = 1
    --filter--
ORDER BY label
        ";
        $filter = '';
        $params = array();
        if (!empty($user) && $user['is_admin'] != 1) {
            $filter .= " AND p.ProvinceID IN (SELECT SUBSTR(DistrictID,1,2) FROM ktv_access_staff WHERE UserId = ?)";
            $params[] = $user['userid'];   
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function listDistrict($ProvinceID = null, $user = null)
    {
        $sql = "SELECT 
    d.DistrictID AS id
    , d.District AS label
FROM
    ktv_district d
JOIN (
    SELECT
        kd.DistrictID AS DistrictID
    FROM ktv_survey_plot sp
    JOIN ktv_survey_plot_polygon spp ON spp.MemberID = sp.MemberID AND spp.PlotNr = sp.PlotNr AND spp.SurveyNr = sp.SurveyNr
    JOIN ktv_members m ON m.MemberID = sp.MemberID
    LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
    WHERE
        sp.Latitude IS NOT NULL AND sp.Longitude IS NOT NULL 
        AND spp.MemberID
    GROUP BY DistrictID
) m ON m.DistrictID = d.DistrictID
WHERE   
    active = 1
    --filter--
ORDER BY label
        ";
        $filter = '';
        $params = array();
        if (!empty($ProvinceID)) {
            $filter .= " AND ProvinceID = ?";
            $params[] = $ProvinceID;
        }
        if (!empty($user) && $user['is_admin'] != 1) {
            $filter .= " AND d.DistrictID IN (SELECT DistrictID FROM ktv_access_staff WHERE UserId = ?)";
            $params[] = $user['userid'];   
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function listSubDistrict($DistrictID = null, $user = null)
    {
        $sql = "SELECT 
    sd.SubDistrictID AS id
    , sd.SubDistrict AS label
FROM
    ktv_subdistrict sd
JOIN (
    SELECT
        ksd.SubDistrictID AS SubDistrictID
    FROM ktv_survey_plot sp
    JOIN ktv_survey_plot_polygon spp ON spp.MemberID = sp.MemberID AND spp.PlotNr = sp.PlotNr AND spp.SurveyNr = sp.SurveyNr
    JOIN ktv_members m ON m.MemberID = sp.MemberID
    LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
    WHERE
        sp.Latitude IS NOT NULL AND sp.Longitude IS NOT NULL 
        AND spp.Latitude IS NOT NULL AND spp.Longitude IS NOT NULL 
        AND spp.MemberID
    GROUP BY SubDistrictID
) m ON m.SubDistrictID = sd.SubDistrictID
WHERE   
    active = 1
    --filter--
ORDER BY label
        ";
        $filter = '';
        $params = array();
        if (!empty($DistrictID)) {
            $filter .= " AND DistrictID = ?";
            $params[] = $DistrictID;
        }
        if (!empty($user) && $user['is_admin'] != 1) {
            $filter .= " AND DistrictID IN (SELECT DistrictID FROM ktv_access_staff WHERE UserId = ?)";
            $params[] = $user['userid'];   
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

}

/* End of file mpolygon.php */
/* Location: ./application/models/mpolygon.php */