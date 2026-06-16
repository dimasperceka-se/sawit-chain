<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mhousehold extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function generateDash(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->db->truncate('dash_household');

        //isi datanya
        $sql="
INSERT INTO dash_household
SELECT
    p.ProvinceID
    , p.Province
    , d.DistrictID
    , d.District
    , sd.SubDistrictID
    , sd.SubDistrict
    , v.VillageID
    , v.Village
    , pp.PartnerID
    , pp.PartnerName
    , COUNT(m.`MemberID`) AS members
    , SUM(family) AS family
    , SUM(female_family) AS female_family
    , SUM(IF(child = 0, 1, 0)) AS child_0
    , SUM(IF(child = 1, 1, 0)) AS child_1
    , SUM(IF(child = 2, 1, 0)) AS child_2
    , SUM(IF(child >= 3, 1, 0)) AS child_3
    , SUM(school) AS school
    , SUM(working) AS working
    , SUM(work_lt_6) AS work_lt_6
    , SUM(work_bt_6_8) AS work_bt_6_8
    , SUM(work_gt_8) AS work_gt_8
    , IFNULL(SUM(activity_seedlings),0) AS activity_seedlings
    , IFNULL(SUM(activity_slashing),0) AS activity_slashing
    , IFNULL(SUM(activity_circle),0) AS activity_circle
    , IFNULL(SUM(activity_pruning),0) AS activity_pruning
    , IFNULL(SUM(activity_pemupukan),0) AS activity_pemupukan
    , IFNULL(SUM(activity_pest),0) AS activity_pest
    , IFNULL(SUM(activity_harvest),0) AS activity_harvest
    , IFNULL(SUM(activity_transport),0) AS activity_transport
    , IFNULL(SUM(lab_workers),0) AS lab_workers
    , IFNULL(SUM(female_lab_workers),0) AS female_lab_workers
    , IFNULL(SUM(lab_workers_use_ppe),0) AS lab_workers_use_ppe
    , IFNULL(SUM(lab_activity_seedlings),0) AS lab_activity_seedlings
    , IFNULL(SUM(lab_activity_slashing),0) AS lab_activity_slashing
    , IFNULL(SUM(lab_activity_circle),0) AS lab_activity_circle
    , IFNULL(SUM(lab_activity_pruning),0) AS lab_activity_pruning
    , IFNULL(SUM(lab_activity_pemupukan),0) AS lab_activity_pemupukan
    , IFNULL(SUM(lab_activity_pest),0) AS lab_activity_pest
    , IFNULL(SUM(lab_activity_harvest),0) AS lab_activity_harvest
    , IFNULL(SUM(lab_activity_transport),0) AS lab_activity_transport
    , NOW() AS DateUpdated
FROM ktv_members m
JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID
JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID AND pp.`IsGenDashboard` = 'Yes'
LEFT JOIN (
    SELECT
        f.MemberID
        , COUNT(f.FamLabID) AS family
        , SUM(IF(f.`Gender` = 'f',1,0)) AS female_family
        , SUM(IF(f.FamLabRelation = 2,1,0)) AS child
        , SUM(IF(f.InSchool = 'Yes',1,0)) AS school
        , SUM(IF(f.WorkingStatus = 'Yes',1,0)) AS working
        , SUM(IF(f.TotalWorkingHrsPerDay < 6,1,0)) AS work_lt_6
        , SUM(IF(f.TotalWorkingHrsPerDay BETWEEN 6 AND 8,1,0)) AS work_bt_6_8
        , SUM(IF(f.TotalWorkingHrsPerDay > 8,1,0)) AS work_gt_8
        , SUM(IF(f.TypeWorkSeed = 1, 1, 0)) AS activity_seedlings
        , SUM(IF(f.TypeWorkSlash = 1, 1, 0)) AS activity_slashing
        , SUM(IF(f.TypeWorkCircle = 1, 1, 0)) AS activity_circle
        , SUM(IF(f.TypeWorkPruning = 1, 1, 0)) AS activity_pruning
        , SUM(IF(f.TypeWorkPemupukan = 1, 1, 0)) AS activity_pemupukan
        , SUM(IF(f.TypeWorkPest = 1, 1, 0)) AS activity_pest
        , SUM(IF(f.TypeWorkHarvest = 1, 1, 0)) AS activity_harvest
        , SUM(IF(f.TypeWorkTransport = 1, 1, 0)) AS activity_transport
    FROM
        ktv_member_family_labour f
    WHERE
        f.StatusCode = 'active'
    GROUP BY f.MemberID
) AS f ON m.MemberID = f.MemberID
LEFT JOIN (
	SELECT
		lab.`MemberID`
		, COUNT(lab.`LaboID`) AS lab_workers
		, SUM(IF(lab.`Gender` = 'f',1,0)) AS female_lab_workers
		, SUM(IF(mx.labWorkerUseApd = '1',1,0)) AS lab_workers_use_ppe
		, SUM(IF(lab.TypeWorkSeed = 1, 1, 0)) AS lab_activity_seedlings
        , SUM(IF(lab.TypeWorkSlash = 1, 1, 0)) AS lab_activity_slashing
        , SUM(IF(lab.TypeWorkCircle = 1, 1, 0)) AS lab_activity_circle
        , SUM(IF(lab.TypeWorkPruning = 1, 1, 0)) AS lab_activity_pruning
        , SUM(IF(lab.TypeWorkPemupukan = 1, 1, 0)) AS lab_activity_pemupukan
        , SUM(IF(lab.TypeWorkPest = 1, 1, 0)) AS lab_activity_pest
        , SUM(IF(lab.TypeWorkHarvest = 1, 1, 0)) AS lab_activity_harvest
        , SUM(IF(lab.TypeWorkTransport = 1, 1, 0)) AS lab_activity_transport
	FROM
		ktv_member_labour lab
		LEFT JOIN ktv_members m ON lab.`MemberID` = m.`MemberID`
		LEFT JOIN ktv_members_extension mx ON m.`MemberID` = mx.`MemberID`
	WHERE
		lab.`StatusCode` = 'active'
	GROUP BY lab.`MemberID`
) AS lab ON m.`MemberID` = lab.MemberID
WHERE
    m.`StatusCode` = 'active'
GROUP BY
    p.ProvinceID
    , d.DistrictID
    , sd.SubDistrictID
    , v.VillageID
    , pp.PartnerID
        ";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function getDisplay($ProvinceID,$DistrictID)
    {
        $result     = array();
        $result['detail'] = array();
        $where      = '';
        $params     = array();
        $sql = "
SELECT
    %s AS label
    , IFNULL(SUM(members),0) AS members
    , IFNULL(SUM(family),0) AS family
    , IFNULL(SUM(female_family),0) AS female_family
    , IFNULL(SUM(child_0),0) AS child_0
    , IFNULL(SUM(child_1),0) AS child_1
    , IFNULL(SUM(child_2),0) AS child_2
    , IFNULL(SUM(child_3),0) AS child_3
    , IFNULL(SUM(school),0) AS school
    , IFNULL(SUM(working),0) AS working
    , IFNULL(SUM(work_lt_6),0) AS work_lt_6
    , IFNULL(SUM(work_bt_6_8),0) AS work_bt_6_8
    , IFNULL(SUM(work_gt_8),0) AS work_gt_8
    
    , IFNULL(SUM(activity_seedlings),0) AS activity_seedlings
    , IFNULL(SUM(activity_slashing),0) AS activity_slashing
    , IFNULL(SUM(activity_circle),0) AS activity_circle
    , IFNULL(SUM(activity_pruning),0) AS activity_pruning
    , IFNULL(SUM(activity_pemupukan),0) AS activity_pemupukan
    , IFNULL(SUM(activity_pest),0) AS activity_pest
    , IFNULL(SUM(activity_harvest),0) AS activity_harvest
    , IFNULL(SUM(activity_transport),0) AS activity_transport
    
    , IFNULL(SUM(lab_activity_seedlings),0) AS lab_activity_seedlings
    , IFNULL(SUM(lab_activity_slashing),0) AS lab_activity_slashing
    , IFNULL(SUM(lab_activity_circle),0) AS lab_activity_circle
    , IFNULL(SUM(lab_activity_pruning),0) AS lab_activity_pruning
    , IFNULL(SUM(lab_activity_pemupukan),0) AS lab_activity_pemupukan
    , IFNULL(SUM(lab_activity_pest),0) AS lab_activity_pest
    , IFNULL(SUM(lab_activity_harvest),0) AS lab_activity_harvest
    , IFNULL(SUM(lab_activity_transport),0) AS lab_activity_transport

    , IFNULL(SUM(lab_workers),0) AS lab_workers
    , IFNULL(SUM(female_lab_workers),0) AS female_lab_workers
    , IFNULL(SUM(lab_workers_use_ppe),0) AS lab_workers_use_ppe
FROM dash_household d
WHERE 1 = 1
    %s    
GROUP BY label    
        ";

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $where .= " AND d.PartnerID = '1'";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $where .= " AND DistrictID IN (".$_SESSION['daerah_access'].")";
            $where .= " AND d.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $where .= " AND DistrictID IN (".$_SESSION['daerah_access'].")";
            $where .= " AND d.PartnerID = '1'";
        }

        $label_data     = "''";
        $label_detail     = "Province";
        if (!empty($ProvinceID)) {
            $where .= " AND d.ProvinceID = ?";
            $params[] = intval($ProvinceID);
            $label_data     = 'Province';
            $label_detail     = 'District';
        }
        if (!empty($DistrictID)) {
            $where .= " AND d.DistrictID = ?";
            $params[] = intval($DistrictID);
            $label_data     = 'District';
            $label_detail     = 'SubDistrict';
        }
        
        $query = $this->db->query(sprintf($sql, $label_data, $where), $params);
        if ($query->num_rows()>0) {
            $result = $query->row_array(0);
            $query = $this->db->query(sprintf($sql, $label_detail, $where), $params);
            if ($query->num_rows()>0) {
                $result['detail'] = $query->result_array();
            }
        }
        return $result;
    }

}
?>