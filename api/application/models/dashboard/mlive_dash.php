<?php

class Mlive_dash extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    public function CargillFarmerSelection($ProgID) {
        $sql = "
        SELECT
            ch.CertHolderOrgName AS 'CH',
            sel.IMSID,
            CASE 
            WHEN sel.ObjType = 'Applicant' THEN 'New Farmer'
            WHEN sel.ObjType = 'Existing Farmer' THEN 'Existing Non-Cert Farmers'
            ELSE sel.ObjType 
            END AS 'StatusFarmer',
            sel.DestObjID AS 'FarmerID',
            sel.Name AS 'FarmerName',
            sel.FarmerGroup,
            sel.Village,
            sel.SubDistrict,
            sel.District,
            sel.Province
        FROM
            ktv_ims_soc_sel sel
            LEFT JOIN ktv_ims ims ON ims.IMSID = sel.IMSID
            LEFT JOIN ktv_first_buyer_program fbp ON fbp.ProgID = ims.ProgID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
        WHERE
            sel.SelectionStatus = 1 
            AND ims.ProgID = {$ProgID} 
        GROUP BY
            sel.IMSID,
            sel.DestObjID   
        ";
        $q = $this->db->query($sql,$p);

        $return['count_data'] = (int) $q->num_rows();
        $return['data'] = $q->result_array();
        return $return;        
    }
    
    public function CargillFarmerInspection($ProgID) {
        $sql = "
        SELECT  ch.CertHolderOrgName AS 'CH',
		afl.IMSID,
		IF(afl.CertStatusAudit = 'Comply', 'Comply', CONCAT('Not Comply (', afl.CertAuditNotComplyReason, ')')) AS 'Status Audit', 
		afl.FarmerID,
		afl.FarmerName,
		afl.GroupName AS 'FarmerGroup',
		afl.Village,
		afl.SubDistrict,
		afl.District,
		afl.Province
        FROM ktv_cocoa_certification_pre_afl pafl
        LEFT JOIN ktv_ims ims ON ims.IMSID = pafl.IMSID
        LEFT JOIN ktv_first_buyer_program fbp ON fbp.ProgID = ims.ProgID
        LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID 
        LEFT JOIN ktv_cocoa_certification_afl_farmer afl ON pafl.FarmerID = afl.FarmerID AND pafl.IMSID = afl.IMSID 
        WHERE 
            ims.ProgID = {$ProgID}
            AND afl.IMSID IS NOT NULL
        GROUP BY afl.IMSID,afl.FarmerID  
        ";
        $q = $this->db->query($sql,$p);

        $return['count_data'] = (int) $q->num_rows();
        $return['data'] = $q->result_array();
        return $return;
    }
    
    public function CargillPolygonComply($ProgID) {
        $sql = "
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'New Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr 
            WHERE a.CertStatusAudit IN (1,3) AND ims.ProgID = {$ProgID} AND b.FarmerID IS NOT NULL
            AND NOT EXISTS(SELECT b.FarmerID, b.GardenNr, b.SurveyNr FROM ktv_cocoa_farmer_garden_area b 
            WHERE a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr AND a.CertSurveyNr > b.SurveyNr) 
            GROUP BY a.FarmerID, a.CertGardenNr
        UNION
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'Existing Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr  
            WHERE a.CertStatusAudit IN (1,3) AND ims.ProgID = {$ProgID} AND b.FarmerID IS NOT NULL
            AND EXISTS(SELECT b.FarmerID FROM ktv_cocoa_farmer_garden_area b 
            WHERE a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr AND a.CertSurveyNr > b.SurveyNr) 
            GROUP BY a.FarmerID, a.CertGardenNr
        UNION
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'Comply Farms Without Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr 
            WHERE a.CertStatusAudit IN (1,3) AND b.FarmerID IS NULL AND ims.ProgID = {$ProgID}
            GROUP BY a.FarmerID, a.CertGardenNr
        ";
        $q = $this->db->query($sql,$p);

        $return['count_data'] = (int) $q->num_rows();
        $return['data'] = $q->result_array();
        return $return;
    }
    
    public function CargillPolygonSelected($ProgID) {
        $sql = "
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'Comply Farm With New Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr 
            WHERE a.CertStatusAudit IN (1,3) AND ims.ProgID = {$ProgID} AND b.FarmerID IS NOT NULL
            AND NOT EXISTS(SELECT b.FarmerID, b.GardenNr, b.SurveyNr FROM ktv_cocoa_farmer_garden_area b 
            WHERE a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr AND a.CertSurveyNr > b.SurveyNr) 
            GROUP BY a.FarmerID, a.CertGardenNr
        UNION
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'Comply Farm With Existing Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr  
            WHERE a.CertStatusAudit IN (1,3) AND ims.ProgID = {$ProgID} AND b.FarmerID IS NOT NULL
            AND EXISTS(SELECT b.FarmerID FROM ktv_cocoa_farmer_garden_area b 
            WHERE a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr AND a.CertSurveyNr > b.SurveyNr) 
            GROUP BY a.FarmerID, a.CertGardenNr
        UNION
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'Not Comply Farm With New Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr 
            WHERE a.CertStatusAudit = 2 AND ims.ProgID = {$ProgID} AND b.FarmerID IS NOT NULL
            AND NOT EXISTS(SELECT b.FarmerID, b.GardenNr, b.SurveyNr FROM ktv_cocoa_farmer_garden_area b 
            WHERE a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr AND a.CertSurveyNr > b.SurveyNr) 
            GROUP BY a.FarmerID, a.CertGardenNr
        UNION
        SELECT ch.CertHolderOrgName AS CH, ims.IMSID, 'Not Comply Farm With Existing Polygon' AS 'Status Garden', a.FarmerID, a.FarmerName, a.GroupName AS 'FarmerGroup',
            a.CertGardenNr AS GardenNr, a.CertSurveyNr AS SurveyNr, a.CertLatitude, a.CertLongitude, cfg.GardenHaUnCertified,
            a.Village, a.SubDistrict, a.District, a.Province 
            FROM ktv_cocoa_certification_afl_garden a
            LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
            LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
            LEFT JOIN ktv_cocoa_farmer_garden cfg ON a.FarmerID = cfg.FarmerID AND a.CertGardenNr = cfg.GardenNr AND a.CertSurveyNr = cfg.SurveyNr
            LEFT JOIN ktv_cocoa_farmer_garden_area b ON a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr  
            WHERE a.CertStatusAudit = 2 AND ims.ProgID = {$ProgID} AND b.FarmerID IS NOT NULL
            AND EXISTS(SELECT b.FarmerID FROM ktv_cocoa_farmer_garden_area b 
            WHERE a.FarmerID = b.FarmerID AND a.CertGardenNr = b.GardenNr AND a.CertSurveyNr > b.SurveyNr) 
            GROUP BY a.FarmerID, a.CertGardenNr
        ";
        $q = $this->db->query($sql,$p);

        $return['count_data'] = (int) $q->num_rows();
        $return['data'] = $q->result_array();
        return $return;
    }
    
    public function CargillCOCAttandence($ProgID) {
        $sql = "
        SELECT ch.CertHolderOrgName AS 'CH',
	   gc.IMSID,
	   CASE
	   WHEN gc.TrainingStatus = 1 AND gc.EligibleStatus = 1 THEN 'Passed'
	   WHEN gc.TrainingStatus = 1 AND gc.EligibleStatus = 2 THEN 'Not Passed'
	   WHEN gc.TrainingStatus = 2 THEN 'Pending'
	   ELSE ''
	   END AS 'Status Training',
	   gc.FarmerID,
	   far.FarmerName,
	   cpg.GroupName AS 'FarmerGroup',
	   vil.Village,
	   ksd.SubDistrict,
	   kd.District,
	   prv.Province 
        FROM ktv_cocoa_certification_pre_afl pafl
        INNER JOIN ktv_cocoa_farmer far ON pafl.FarmerID = far.FarmerID
        LEFT JOIN ktv_cpg cpg ON far.CPGid = cpg.CPGid
        LEFT JOIN ktv_village vil ON far.VillageID = vil.VillageID
        LEFT JOIN ktv_subdistrict ksd ON vil.SubDistrictID = ksd.SubDistrictID 
        LEFT JOIN ktv_district kd ON ksd.DistrictID = kd.DistrictID 
        LEFT JOIN ktv_province prv ON kd.ProvinceID = prv.ProvinceID 
        LEFT JOIN ktv_ims ims ON ims.IMSID = pafl.IMSID
        LEFT JOIN ktv_first_buyer_program fbp ON fbp.ProgID = ims.ProgID
        LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
        LEFT JOIN ktv_ims_training_gap_coc gc ON pafl.FarmerID = gc.FarmerID AND pafl.IMSID = gc.IMSID
        WHERE 
            ims.ProgID = {$ProgID} 
            AND gc.FarmerID IS NOT NULL
        GROUP BY gc.IMSID,gc.FarmerID 
        ";
        $q = $this->db->query($sql,$p);

        $return['count_data'] = (int) $q->num_rows();
        $return['data'] = $q->result_array();
        return $return;
    }
    
    public function CargillCoachingSession($ProgID) {
        $sql = "
        SELECT 
            ch.CertHolderOrgName AS 'CH',
            fc.IMSID,
            fc.FarmerID,
            far.FarmerName,
            cpg.GroupName AS 'FarmerGroup',
            COUNT(fc.FarmerID) AS 'Coaching Session',
            vil.Village,
            ksd.SubDistrict,
            kd.District,
            prv.Province 
        FROM ktv_cocoa_certification_pre_afl pafl
        INNER JOIN ktv_cocoa_farmer far ON pafl.FarmerID = far.FarmerID
        LEFT JOIN ktv_cpg cpg ON far.CPGid = cpg.CPGid
        LEFT JOIN ktv_village vil ON far.VillageID = vil.VillageID
        LEFT JOIN ktv_subdistrict ksd ON vil.SubDistrictID = ksd.SubDistrictID 
        LEFT JOIN ktv_district kd ON ksd.DistrictID = kd.DistrictID 
        LEFT JOIN ktv_province prv ON kd.ProvinceID = prv.ProvinceID
        LEFT JOIN ktv_ims ims ON ims.IMSID = pafl.IMSID
        LEFT JOIN ktv_first_buyer_program fbp ON fbp.ProgID = ims.ProgID
        LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = ims.CertHolderID
        LEFT JOIN ktv_ims_farmer_coaching fc ON pafl.FarmerID = fc.FarmerID AND pafl.IMSID = fc.IMSID 
        WHERE
            ims.ProgID = {$ProgID}
            AND fc.StatusCode='active'
            AND pafl.StatusCode = 'active'
        GROUP BY fc.IMSID,fc.FarmerID
        ";
        $q = $this->db->query($sql,$p);

        $return['count_data'] = (int) $q->num_rows();
        $return['data'] = $q->result_array();
        return $return;
    }

}
