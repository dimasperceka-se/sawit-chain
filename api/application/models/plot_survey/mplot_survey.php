<?php
/**
 * @Author: nikolius
 * @Date:   2017-05-31 11:48:08
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mplot_survey extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getComboCollection($DistrictID){
        $sql = "SELECT
            cp.CollectpointID id
            , cp.CollectpointName label
        FROM
            `ktv_collecting_point` cp
        LEFT JOIN
            ktv_village v on v.VillageID = cp.VillageID
        LEFT JOIN
            ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
        LEFT JOIN
            ktv_district d on d.DistrictID = sd.DistrictID
        WHERE
            cp.StatusCode = 'active'
        AND
            d.DistrictID = ?
        ";

    	$query = $this->db->query($sql, array($DistrictID));

        $return['data'] = $query->result_array();
        return $return;
    }

	public function GetICSLogLockStatus($MemberID,$PlotNr,$SurveyNr){
		$sql = "SELECT
				`LockStatus`,
				`LockBy`,
				`Comment`
			FROM
				`ktv_certification_audit_log_lock` a
			WHERE
				a.`MemberID` = ?
				AND a.`PlotNr` = ?
				AND a.`SurveyNr` = ?
			LIMIT 1";
		$data = $this->db->query($sql,array($MemberID,$PlotNr,$SurveyNr))->row_array();
		if(isset($data['LockStatus'])){
			$LockStatus = $data['LockStatus'];
		}else{
			$LockStatus = "No";
		}

		$return['success'] = true;
		$return['LockStatus'] = $LockStatus;
		return $return;
	}    

    public function GetICSLogMainGrid($FarmerID,$GardenNr,$SurveyNr,$Certification){
    	$sql="SELECT
                c.*,
                a.*,
                ps.PersonNm AS StaffName,
                CASE
                    WHEN a.StatusAudit=1 THEN '".lang('Lolos Audit')."'
                    WHEN a.StatusAudit=2 THEN '".lang('Tidak Lolos Audit')."'
                    WHEN a.StatusAudit=3 THEN '".lang('Disahkan Dengan Syarat')."'
                END AS StatusAuditName,
                refprog.CertProgName AS CertProgram
            FROM
                ktv_certification_audit_log a

                LEFT JOIN ktv_staffs st ON a.InspectorID = st.StaffID
                LEFT JOIN ktv_persons ps ON st.PersonID = ps.PersonID

                LEFT JOIN ktv_certification_signature c on a.FarmerID = c.FarmerID AND
                    a.GardenNr = c.GardenNr and
                    a.SurveyNr = c.SurveyNr and
                    a.Certification=c.Certification

                LEFT JOIN ktv_ref_certification_program refprog ON a.Certification = refprog.CertProgID
            WHERE
                a.FarmerID = ?
                AND a.GardenNr = ?
                AND a.SurveyNr = ?
                AND a.Certification = ?
            GROUP BY a.FarmerID,a.GardenNr,a.SurveyNr,a.Certification,a.ICSDate
            ORDER BY a.DateCreated DESC";
    	$query = $this->db->query($sql, array($FarmerID,$GardenNr,$SurveyNr,$Certification));

        $return['data'] = $query->result_array();
        return $return;
    }

	public function GetGardenGridSummary($MemberID,$SurveyNr,$PlotNr,$Certification,$ICSDate){
		$sql = "SELECT
					a.`DaconID`
					, a.`RemarkProcess`
					, COUNT(b.`DaconItemID`) AS NrOfIssue
				FROM
					ktv_farmer_garden_datacontrol a
					LEFT JOIN `ktv_farmer_garden_datacontrol_item` b ON a.`DaconID` = b.`DaconID`
				WHERE
					a.`MemberID` = ?
					AND a.`SurveyNr` = ?
					AND a.`PlotNr` = ?
					AND a.`Certification` = ?
					AND a.`ICSDate` = ?
				GROUP BY a.`DaconID`
				ORDER BY a.`DaconID` DESC";
		$p = array(
			$FarmerID,
			$SurveyNr,
			$GardenNr,
			$Certification,
			$ICSDate
		);
		$DataList = $this->db->query($sql,$p)->result_array();

		$return['data'] = $DataList;
		$return['success'] = true;
		return $return;
	}

    public function InsertIcsLog($paramPost){
    	$this->db->trans_start();

    	$sql="INSERT INTO `ktv_certification_audit_log` SET
		    `FarmerID` = ?,
		    `GardenNr` = ?,
		    `SurveyNr` = ?,
		    `Certification` = ?,
		    `ICSDate` = ?,
		    `StatusAudit` = ?,
		    `MasukHutanLindung` = ?,
		    `DateRevisionAudit` = ?,
		    `CommentAudit` = ?,
		    `RecommendationAudit` = ?,
		    `InspectorID` = ?,
		    `InspectorName` = ?,
		    `InspectorSignature` = ?,
		    `AuditCommiteeID` = ?,
		    `AuditCommiteeName` = ?,
		    `AuditCommiteeSignature` = ?,
		    `IMSManagerID` = ?,
		    `IMSManagerName` = ?,
		    `IMSManagerSignature` = ?,
		    `FarmerSignature` = ?,
		    `DateCreated` = NOW(),
		    `CreatedBy` = ?
		";
		$p = array(
			$paramPost['FarmerID'],
			$paramPost['GardenNr'],
			$paramPost['SurveyNr'],
			$paramPost['CertificationProgram'],
			$paramPost['ICSDate'],
			$paramPost['StatusAudit'],
			$paramPost['MasukHutanLindung'],
			$paramPost['DateRevisionAudit'],
			$paramPost['CommentAudit'],
			$paramPost['RecommendationAudit'],
			$paramPost['InspectorID'],
			$paramPost['InspectorName'],
			$paramPost['InspectorSignature'],
			$paramPost['AuditCommiteeID'],
			$paramPost['AuditCommiteeName'],
			$paramPost['AuditCommiteeSignature'],
			$paramPost['IMSManagerID'],
			$paramPost['IMSManagerName'],
			$paramPost['IMSManagerSignature'],
			$paramPost['FarmerSignature'],
			$_SESSION['userid']
		);
		$sql = "INSERT INTO `ktv_certification_audit_log` (
				`FarmerID`,
				`GardenNr`,
				`SurveyNr`,
				`Certification`,
				`ICSDate`,
				`StatusAudit`,
				`MasukHutanLindung`,
				`DateRevisionAudit`,
				`CommentAudit`,
				`RecommendationAudit`,
				`InspectorID`,
				`InspectorName`,
				`InspectorSignature`,
				`AuditCommiteeID`,
				`AuditCommiteeName`,
				`AuditCommiteeSignature`,
				`IMSManagerID`,
				`IMSManagerName`,
				`IMSManagerSignature`,
				`FarmerSignature`,
				`DateCreated`,
				`CreatedBy`
			)
			VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?
			)
			ON DUPLICATE KEY UPDATE
				`StatusAudit` = ?,
				`MasukHutanLindung` = ?,
				`DateRevisionAudit` = ?,
				`CommentAudit` = ?,
				`RecommendationAudit` = ?,
				`InspectorID` = ?,
				`InspectorName` = ?,
				`InspectorSignature` = ?,
				`AuditCommiteeID` = ?,
				`AuditCommiteeName` = ?,
				`AuditCommiteeSignature` = ?,
				`IMSManagerID` = ?,
				`IMSManagerName` = ?,
				`IMSManagerSignature` = ?,
				`FarmerSignature` = ?,
				`DateUpdated` = NOW(),
				`LastModifiedBy` = ?";
		$p = array(
			$paramPost['FarmerID'],
			$paramPost['GardenNr'],
			$paramPost['SurveyNr'],
			$paramPost['CertificationProgram'],
			$paramPost['ICSDate'],
			$paramPost['StatusAudit'],
			$paramPost['MasukHutanLindung'],
			$paramPost['DateRevisionAudit'],
			$paramPost['CommentAudit'],
			$paramPost['RecommendationAudit'],
			$paramPost['InspectorID'],
			$paramPost['InspectorName'],
			$paramPost['InspectorSignature'],
			$paramPost['AuditCommiteeID'],
			$paramPost['AuditCommiteeName'],
			$paramPost['AuditCommiteeSignature'],
			$paramPost['IMSManagerID'],
			$paramPost['IMSManagerName'],
			$paramPost['IMSManagerSignature'],
			$paramPost['FarmerSignature'],
			$_SESSION['userid'],
			//update
			$paramPost['StatusAudit'],
			$paramPost['MasukHutanLindung'],
			$paramPost['DateRevisionAudit'],
			$paramPost['CommentAudit'],
			$paramPost['RecommendationAudit'],
			$paramPost['InspectorID'],
			$paramPost['InspectorName'],
			$paramPost['InspectorSignature'],
			$paramPost['AuditCommiteeID'],
			$paramPost['AuditCommiteeName'],
			$paramPost['AuditCommiteeSignature'],
			$paramPost['IMSManagerID'],
			$paramPost['IMSManagerName'],
			$paramPost['IMSManagerSignature'],
			$paramPost['FarmerSignature'],
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		// $this->GenDataControlSurveyGarden('Update',date('Y-m-d H:i:s'),$_SESSION['userid'],$paramPost['FarmerID'],$paramPost['GardenNr'],$paramPost['SurveyNr'],$paramPost['CertificationProgram'],$paramPost['ICSDate']);

    	$this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function UpdateIcsLog($paramPost){
    	$this->db->trans_start();

    	$sql="UPDATE `ktv_certification_audit_log` SET
			    `StatusAudit` = ?,
				MasukHutanLindung = ?,
			    `DateRevisionAudit` = ?,
			    `CommentAudit` = ?,
			    `RecommendationAudit` = ?,
			    `InspectorID` = ?,
			    `InspectorName` = ?,
			    `InspectorSignature` = ?,
			    `AuditCommiteeID` = ?,
			    `AuditCommiteeName` = ?,
			    `AuditCommiteeSignature` = ?,
			    `IMSManagerID` = ?,
			    `IMSManagerName` = ?,
			    `IMSManagerSignature` = ?,
			    `FarmerSignature` = ?,
			    `LastModifiedBy` = ?,
			    DateUpdated = NOW()
			WHERE
				`FarmerID` = ?
			    AND `GardenNr` = ?
			    AND `SurveyNr` = ?
			    AND `Certification` = ?
			    AND `ICSDate` = ?
			LIMIT 1";
    	$p = array(
			$paramPost['StatusAudit'],
			$paramPost['MasukHutanLindung'],
			$paramPost['DateRevisionAudit'],
			$paramPost['CommentAudit'],
			$paramPost['RecommendationAudit'],
			$paramPost['InspectorID'],
			$paramPost['InspectorName'],
			$paramPost['InspectorSignature'],
			$paramPost['AuditCommiteeID'],
			$paramPost['AuditCommiteeName'],
			$paramPost['AuditCommiteeSignature'],
			$paramPost['IMSManagerID'],
			$paramPost['IMSManagerName'],
			$paramPost['IMSManagerSignature'],
			$paramPost['FarmerSignature'],
			$_SESSION['userid'],

			$paramPost['FarmerID'],
			$paramPost['GardenNr'],
			$paramPost['SurveyNr'],
			$paramPost['CertificationProgram'],
			$paramPost['ICSDate']
    	);
    	$query = $this->db->query($sql,$p);

		//Generate Audit Summary (dipindah ketika insert/update garden)
		//$this->GenDataControlSurveyGarden('Update',date('Y-m-d H:i:s'),$_SESSION['userid'],$paramPost['FarmerID'],$paramPost['GardenNr'],$paramPost['SurveyNr'],$paramPost['CertificationProgram'],$paramPost['ICSDate']);

    	$this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function DeleteIcsLog($FarmerID,$GardenNr,$SurveyNr,$Certification,$ICSDate){
    	$this->db->trans_start();

    	$sql="SELECT * FROM ktv_certification_audit_log
    		WHERE
	    		`FarmerID` = ?
				AND `GardenNr` = ?
				AND `SurveyNr` = ?
				AND Certification = ?
				AND ICSDate = ?
			LIMIT 1";
		$DataIcsLog = $this->db->query($sql,array($FarmerID,$GardenNr,$SurveyNr,$Certification,$ICSDate))->row_array();

		//Kurangi Param
		unset($DataIcsLog['uid']);

		//Tambah Param
		$DataIcsLog['DateHistory'] = date('Y-m-d');
		$DataIcsLog['DeleteBy'] = $_SESSION['userid'];

		//insert
        $this->db->insert('his_ktv_certification_audit_log', $DataIcsLog);

        $sql="DELETE FROM ktv_certification_audit_log
        	WHERE `FarmerID` = ?
				AND `GardenNr` = ?
				AND `SurveyNr` = ?
				AND Certification = ?
				AND ICSDate = ?
			LIMIT 1";
		$query = $this->db->query($sql,array($FarmerID,$GardenNr,$SurveyNr,$Certification,$ICSDate));

    	$this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
	}

	public function GetGardenGridSummaryIssue($DaconID){
		$sql = "SELECT
					b.`RefLabel` AS Issue
					, b.`StatusControl` AS IssueStatus
				FROM
					`ktv_farmer_garden_datacontrol_item` a
					INNER JOIN ktv_ref_survey_datacontrol b ON a.`RefID` = b.`RefID`
				WHERE
					a.`DaconID` = ?";
		$p = array(
			$DaconID
		);
		$DataList = $this->db->query($sql,$p)->result_array();

		$return['data'] = $DataList;
		$return['success'] = true;
		return $return;
	}

    public function getGridPlotCertification($MemberID){
        $sql = "SELECT
                a.SurveyID
                , a.MemberID
                , a.DateCollection
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM 
                ktv_survey_certification a
            WHERE
                a.`MemberID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.DateCollection DESC
        ";

        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        $return['data'] = $data;
        return $return;
    }

    public function getGridPlotSurveySummary($MemberID,$from){
        $surveynr = ($from == "certification") ? " AND a.SurveyNr >= '20'" : " AND a.SurveyNr < '20'";
        $sql="SELECT
                a.`PlotNr`
                , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
                , a.`SurveyNr`
                , a.`DateCollection`
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM
                ktv_survey_plot a
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.`MemberID` = ?
                AND a.`StatusCode` = 'active'
                $surveynr
            ORDER BY a.`PlotNr`, a.`SurveyNr`";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['PlotNr'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getGridPlotPolygonPanel($MemberID, $CallFrom) {
        if ($CallFrom == 'SME') {
            $id = "MemberID";
            $QueryTablePoly = " ktv_survey_plot_polygon_sme ";
            $QueryTablePoly2 = " ktv_survey_plot_polygon_sme_geo ";
            $QueryTablePlot = " ktv_survey_plot_sme ";
        } elseif ($CallFrom == 'Mill') {
            $id = "MillID";
            $QueryTablePoly = " ktv_survey_plot_polygon_mill ";
            $QueryTablePoly2 = " ktv_survey_plot_polygon_mill_geo ";
            $QueryTablePlot = " ktv_survey_plot_mill ";
        } else {
            $id = "MemberID";
            $QueryTablePoly = " ktv_survey_plot_polygon ";
            $QueryTablePoly2 = " ktv_survey_plot_polygon_geo ";
            $QueryTablePlot = " ktv_survey_plot ";
        }

        $sql="SELECT
            a.`PlotNr`
            , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
            , a.`SurveyNr`
            , a.`DateCollection`
            , c.StatusCheck
            , c.`DateCreated`
            , (SELECT UserRealName FROM sys_user WHERE UserId = c.`CreatedBy`) AS CreatedBy
            , CONCAT(
                (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = c.`CreatedBy` LIMIT 1),
                IF(c.`LastModifiedBy` IS NOT NULL OR c.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = c.`LastModifiedBy` LIMIT 1),'')
            ) AS Enumerator
        FROM
            $QueryTablePlot a
            LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            LEFT JOIN $QueryTablePoly2 c ON
                a.$id = c.$id
                AND a.`PlotNr` = c.`PlotNr`
                AND a.`SurveyNr` = c.`SurveyNr`
        WHERE
            a.$id = ?
            AND a.`StatusCode` = 'active'
            AND c.Revision = (SELECT MAX(c2.Revision) FROM $QueryTablePoly2 c2 WHERE c2.`MemberID` = c.$id
                AND c2.`PlotNr` = c.`PlotNr`
                AND c2.`SurveyNr` = c.`SurveyNr`
                -- AND c2.StatusCode = 'active'
                    limit 1)
        GROUP BY a.$id, a.`PlotNr`, a.`SurveyNr`
        ORDER BY a.`PlotNr`, a.`SurveyNr` DESC";
        $query = $this->db->query($sql,array((int) $MemberID));
        
        // echo "<pre>";
        // print_r($this->db->last_query());
        // die;
        
        $data = $query->result_array();
        if($data[0]['PlotNr'] == ""){
            $data = array();
        }
        
        $return['data'] = $data;
        return $return;
    }

    public function getComboSurveyNr($from){
        $surveynr = ($from == "certification") ? " AND a.SurveyType = '$from'" : " AND a.SurveyType = 'general'";

        $sql="SELECT
                a.`SurveyNr` AS id
                , CONCAT(a.`SurveyNr`,' - ',a.`SurveyTxt`) AS label
            FROM
                ktv_survey a
            WHERE
                a.`StatusCode` = 'active'
            $surveynr
            ORDER BY a.`SurveyNr`";
        $query = $this->db->query($sql);
        // echo "<pre>";print_r($this->db->last_query());die;
        return $query->result_array();
    }

    public function checkIfSurveyExist($paramPost){
        $sql="SELECT
                a.`MemberID`
            FROM
                ktv_survey_plot a
            WHERE
                a.MemberID = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`StatusCode` = 'active'
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['PlotNr'],
            $paramPost['SurveyNr']
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        if($data['MemberID'] != ""){
            return true;
        }else{
            return false;
        }
    }

    public function getMainHerbicide($MemberID,$PlotNr,$SurveyNr){
        $sql = "SELECT
                ph.HerbicideID
                , ph.MemberID
                , ph.MemberUid
                , ph.PlotNr
                , ph.SurveyNr
                , b.BrandName Brand
                , b.BrandID
                , ph.Frequency
                , CASE
                    WHEN ph.Applying = 1 THEN 'Blanket on The Whole Surface'
                    WHEN ph.Applying = 2 THEN 'Only Circle and Harvesting Path'
                    WHEN ph.Applying = 3 THEN 'Selective Area'
                    ELSE '-'
                END Applying
                , ph.Applying ApplyingID
                , ph.StatusCode
                , ph.CreatedBy
                , ph.DateCreated
                , ph.LastModifiedBy
                , ph.DateUpdated
            FROM
                ktv_survey_plot_herbicide ph
            LEFT JOIN
                ktv_brands b on b.BrandID = ph.Brand AND b.BrandType = 1
            WHERE
                1=1
            AND ph.MemberID = ?
            AND ph.SurveyNr = ?
            AND ph.PlotNr = ?
            AND ph.StatusCode = 'active'
            ORDER BY ph.HerbicideID DESC
            ";
        $query = $this->db->query($sql,array($MemberID,$SurveyNr,$PlotNr));

        $return["data"]     = $query->result_array();
        $return["total"]    = $query->num_Rows();

        return $return;
    }

    public function getMainFungicide($MemberID,$PlotNr,$SurveyNr){
        $sql = "SELECT
                ph.FungicideID
                , ph.MemberID
                , ph.MemberUid
                , ph.PlotNr
                , ph.SurveyNr
                , b.BrandName Brand
                , b.BrandID
                , ph.Frequency
                , CASE
                    WHEN ph.Applying = 1 THEN 'All Palms'
                    WHEN ph.Applying = 2 THEN 'Selected Palms'
                    ELSE '-'
                END Applying
                , ph.Applying ApplyingID
                , ph.StatusCode
                , ph.CreatedBy
                , ph.DateCreated
                , ph.LastModifiedBy
                , ph.DateUpdated
            FROM
                ktv_survey_plot_fungicide ph
            LEFT JOIN
                ktv_brands b on b.BrandID = ph.Brand AND b.BrandType = 3
            WHERE
                1=1
            AND ph.MemberID = ?
            AND ph.SurveyNr = ?
            AND ph.PlotNr = ?
            AND ph.StatusCode = 'active'
            ORDER BY ph.FungicideID DESC
            ";
        $query = $this->db->query($sql,array($MemberID,$SurveyNr,$PlotNr));

        $return["data"]     = $query->result_array();
        $return["total"]    = $query->num_Rows();

        return $return;
    }

    public function getMainInsecticide($MemberID,$PlotNr,$SurveyNr){
        $sql = "SELECT
                ph.InsecticideID
                , ph.MemberID
                , ph.MemberUid
                , ph.PlotNr
                , ph.SurveyNr
                , b.BrandName Brand
                , b.BrandID
                , ph.Frequency
                , CASE
                    WHEN ph.Applying = 1 THEN 'All Palms'
                    WHEN ph.Applying = 2 THEN 'Selected Palms'
                    ELSE '-'
                END Applying
                , ph.Applying ApplyingID
                , ph.StatusCode
                , ph.CreatedBy
                , ph.DateCreated
                , ph.LastModifiedBy
                , ph.DateUpdated
            FROM
                ktv_survey_plot_insecticide ph
            LEFT JOIN
                ktv_brands b on b.BrandID = ph.Brand AND b.BrandType = 2
            WHERE
                1=1
            AND ph.MemberID = ?
            AND ph.SurveyNr = ?
            AND ph.PlotNr = ?
            AND ph.StatusCode = 'active'
            ORDER BY ph.InsecticideID DESC
            ";
        $query = $this->db->query($sql,array($MemberID,$SurveyNr,$PlotNr));

        $data = array();
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $val){
                $data[$key] = $val;

                $sql2   = "SELECT ApplyFor ApplyForID
                , CASE 
                    WHEN ApplyFor = 1 THEN 'Rat Control' 
                    WHEN ApplyFor = 2 THEN 'Caterpillar Controll' 
                    WHEN ApplyFor = 3 THEN 'Oryctes Controll' 
                    WHEN ApplyFor = 4 THEN 'Others' 
                    ELSE '-' END ApplyFor
                FROM 
                    `ktv_survey_plot_insecticide_apply` 
                WHERE InsecticideID = ? ORDER BY ApplyFor ASC";
                $query2 = $this->db->query($sql2,array($val["InsecticideID"]));
                if($query2->num_rows()>0){
                    $applyFor = array();
                    $applyForID = array();
                    foreach($query2->result_array() as $num => $val){
                        $applyFor[$num] = $val["ApplyFor"];
                        $applyForID[$num] = $val["ApplyForID"];
                    }
                    $data[$key]["ApplyingFor"] = $applyFor;
                    $data[$key]["ApplyingForID"] = $applyForID;
                }
            }
        }

        $return["data"]     = $data;
        $return["total"]    = $query->num_Rows();

        return $return;
    }

    public function CmbListHerbicide(){
        $sql = "SELECT
            BrandID id
            , BrandName label
        FROM
            `ktv_brands`
        WHERE
            StatusCode = 'active'
        AND
            BrandType = '1'";
        $query = $this->db->query($sql);
        
        $return['data'] = $query->result_array();
        return $return;
    }

    public function CmbListInsecticide(){
        $sql = "SELECT
            BrandID id
            , BrandName label
        FROM
            `ktv_brands`
        WHERE
            StatusCode = 'active'
        AND
            BrandType = '2'";
        $query = $this->db->query($sql);
        
        $return['data'] = $query->result_array();
        return $return;
    }

    public function CmbListFungicide(){
        $sql = "SELECT
            BrandID id
            , BrandName label
        FROM
            `ktv_brands`
        WHERE
            StatusCode = 'active'
        AND
            BrandType = '3'";
        $query = $this->db->query($sql);
        
        $return['data'] = $query->result_array();
        return $return;
    }

    public function InsertHerbicide($post){
        unset($post["HerbicideID"]);

        $post["StatusCode"]     = 'active';
        $post["DateCreated"]    = date("Y-m-d H:i:s");
        $post["CreatedBy"]      = $_SESSION['userid'];

        $db = $this->db->insert("ktv_survey_plot_herbicide",$post);
        if($db){
            return array("success"=>true);
        }else{
            return array("success"=>false);
        }
    }

    public function UpdateHerbicide($post){
        $HerbicideID = $post["HerbicideID"];

        unset($post["HerbicideID"]);

        $post["StatusCode"]     = 'active';
        $post["DateUpdated"]    = date("Y-m-d H:i:s");
        $post["LastModifiedBy"] = $_SESSION['userid'];

        $this->db->where("HerbicideID",$HerbicideID);
        $db = $this->db->update("ktv_survey_plot_herbicide",$post);
        if($db){
            return array("success"=>true);
        }else{
            return array("success"=>false);
        }
    }

    public function InsertInsecticide($post){
        $arrApplyfor = json_decode($post["ApplyingFor"]);
        unset($post["InsecticideID"]);
        unset($post["ApplyingFor"]);

        $post["StatusCode"]     = 'active';
        $post["DateCreated"]    = date("Y-m-d H:i:s");
        $post["CreatedBy"]      = $_SESSION['userid'];

        $db = $this->db->insert("ktv_survey_plot_insecticide",$post);
        if($db){
            $dataPost = array();
            $InsecticideID = $this->db->insert_id();
            if(count($arrApplyfor)>0){
                foreach($arrApplyfor as $num => $value){
                    $dataPost[$num]["InsecticideID"] = $InsecticideID;
                    $dataPost[$num]["ApplyFor"]      = $value;
                }

                $this->db->insert_batch("ktv_survey_plot_insecticide_apply",$dataPost);
            }
            return array("success"=>true);
        }else{
            return array("success"=>false);
        }
    }

    public function UpdateInsecticide($post){
        $InsecticideID = $post["InsecticideID"];
        $arrApplyfor = json_decode($post["ApplyingFor"]);

        unset($post["InsecticideID"]);
        unset($post["ApplyingFor"]);

        $post["StatusCode"]     = 'active';
        $post["DateUpdated"]    = date("Y-m-d H:i:s");
        $post["LastModifiedBy"] = $_SESSION['userid'];

        $this->db->where("InsecticideID",$InsecticideID);
        $db = $this->db->update("ktv_survey_plot_insecticide",$post);
        
        if($db){
            $this->db->where("InsecticideID",$InsecticideID);
            $this->db->delete("ktv_survey_plot_insecticide_apply");
            $dataPost = array();
            if(count($arrApplyfor)>0){
                foreach($arrApplyfor as $num => $value){
                    $dataPost[$num]["InsecticideID"] = $InsecticideID;
                    $dataPost[$num]["ApplyFor"]      = $value;
                }

                $this->db->insert_batch("ktv_survey_plot_insecticide_apply",$dataPost);
            }
            return array("success"=>true);
        }else{
            return array("success"=>false);
        }
    }

    public function InsertFungicide($post){
        unset($post["FungicideID"]);

        $post["StatusCode"]     = 'active';
        $post["DateCreated"]    = date("Y-m-d H:i:s");
        $post["CreatedBy"]      = $_SESSION['userid'];

        $db = $this->db->insert("ktv_survey_plot_fungicide",$post);
        if($db){
            return array("success"=>true);
        }else{
            return array("success"=>false);
        }
    }

    public function UpdateFungicide($post){
        $FungicideID = $post["FungicideID"];

        unset($post["FungicideID"]);

        $post["StatusCode"]     = 'active';
        $post["DateUpdated"]    = date("Y-m-d H:i:s");
        $post["LastModifiedBy"] = $_SESSION['userid'];

        $this->db->where("FungicideID",$FungicideID);
        $db = $this->db->update("ktv_survey_plot_fungicide",$post);
        if($db){
            return array("success"=>true);
        }else{
            return array("success"=>false);
        }
    }

    public function getSurveyCertFormDataPolygon($SurveyID){
        $sql = "SELECT
                SurveyID
                , ST_ASGEOJSON(`Polygon`) AS `PolygonGeoJson`
            FROM
                ktv_survey_certification 
            WHERE
                SurveyID = ?";

        $query = $this->db->query($sql,array($SurveyID));
        $data = $query->row_array();

        $polygon = json_decode($data["PolygonGeoJson"]);

        $data["Latitude"] = $polygon->coordinates[0][0][1];
        $data["Longitude"] = $polygon->coordinates[0][0][0];

        return $data;
    }

    public function getSurveyCertFormData($SurveyID){
        $this->load->library('awsfileupload');

        $sql = "SELECT
                a.SurveyID
                , a.MemberID
                , a.DateCollection
                , a.PlanAcquireNewPalmOil
                , a.LocalAreaPlanned
                , a.ObtainConsent
                , a.InvolveLocalPeople
                , a.AcquireCoercing
                , a.StartDevPlantation
                , a.ProvideOwnerRelevantInform
                , a.HCVHCSApprocah
                , a.DocumentWritten
                , a.FarmPhoto
                , a.WrittenAgreement
                , a.BareLandPlantationArea
                , a.FoodCropsPlantationArea
                , a.MangrovePlantationArea
                , a.OtherPlantationArea
                , a.OilPalmPlantationArea
                , a.ForestPlantationArea
                , a.InfrastructurePlantationArea
                , a.StepSlopesPlantationArea
                , a.DontKnowPlantationArea
                , a.LandUseStatus
            FROM
                ktv_survey_certification a
            WHERE
                a.SurveyID = ?
        ";
        
        $query = $this->db->query($sql,array($SurveyID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['DocumentWritten'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWritten'];
        $dataRow['FarmPhoto'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhoto'];

        if($this->awsfileupload->doesObjectExist($dataRow['DocumentWritten']) == true) {
            $dataRow['DocumentWrittenOld']  = $dataRow['DocumentWritten'];
            $dataRow['DocumentWritten']     = $this->config->item('CTCDN')."/".$dataRow['DocumentWritten'];
        }
        if($this->awsfileupload->doesObjectExist($dataRow['FarmPhoto']) == true) {
            $dataRow['FarmPhotoOld']  = $dataRow['FarmPhoto'];
            $dataRow['FarmPhoto']     = $this->config->item('CTCDN')."/".$dataRow['FarmPhoto'];
        }

        $result["success"] = true;
        $result["data"] = $dataRow;

        return $result;
    }

    public function getPlotSurveyFormData($MemberID,$PlotNr,$SurveyNr,$DateCollection){
        $this->load->library('awsfileupload');
        $sql="SELECT
                a.`MemberID`,
                b.MemberUID,
                b.`MemberDisplayID`
                , b.`MemberName`
                ,a.`PlotNr`,
                a.`SurveyNr`,
                a.`DateCollection`,
                a.Certification,
                #(SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedByLabel,
                /*CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS CreatedByLabel,*/
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS CreatedByLabel,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedByLabel,
                SUBSTR(a.`VillageID`,1,2) AS ProvinceID,
                d.`DistrictID`,
                c.`SubDistrictID`,
                a.`VillageID`,
                a.`PhotoOfVisit`,
                a.`PhotoOfFire`,
                a.`SoilErotionFile`,
                a.`SoilAccumulationFile`,
                a.`OwnerDocPhoto`,
                a.`GardenAreaHa`,
                a.PlantedAreaHa,
                IFNULL(a.GardenAreaPolygon, a.GardenAreaHa) GardenAreaPolygon,
                a.`GardenLength`,
                a.`GardenWidth`,
                IFNULL(ST_Y(a.`LatLong`), a.Latitude) AS Latitude,
                IFNULL(ST_X(a.`LatLong`), a.Longitude) AS Longitude,
                a.North,
                a.East,
                a.South,
                a.West,
                a.FloodingPeatLand,
                a.AsCollectingPoint,
                a.SalinePeatLand,
                a.NoIssuesPeatLand,
                a.OthersPeatLand,
                a.OthersPeatLandText,
                a.WllManagePeatLand,
                a.TailoredPeatLand,
                a.MonitorRatePeatLand,
                a.NotAppliedPeatLand,
                a.`OwnershipDoc`,
                a.DocumentNumber,
                a.OwnershipDocShare,
                a.OwnershipDocNr,
                a.OwnershipDocText,
                a.LandOwnershipType,
                a.LandOwnership,
                a.OwnerOfTheGarden,
                a.OwnerOfPlantationNameText,
                a.OwnerOfPlantationLocationText,
                a.OwnerOfPlantationPhoneText,
                a.`BusinessModel`,
                a.PlantingType,
                a.ManageType,
                a.`PlantationConditionEst`,
                a.`AverageAgeTree`,
                a.`SoilType`,
                a.`TopographyType`,
                a.FirstPlantingYear,
                a.TreeTBM,
                a.TreeTM,
                a.TreeTR,
                a.PlaceCollection,
                a.CollectionID,
                a.`TypePlantMateMarihat`,
                a.`TypePlantMateMarihatNr`,
                a.`TypePlantMateDumpy`,
                a.`TypePlantMateDumpyNr`,
                a.`TypePlantMateLonsum`,
                a.`TypePlantMateLonsumNr`,
                a.`TypePlantMateSimalungun`,
                a.`TypePlantMateSimalungunNr`,
                a.`TypePlantMateDanimas`,
                a.`TypePlantMateDanimasNr`,
                a.`TypePlantMateSriwijaya`,
                a.`TypePlantMateSriwijayaNr`,
                a.`TypePlantMateSocfin`,
                a.`TypePlantMateSocfinNr`,
                a.`TypePlantMateOther`,
                a.`TypePlantMateOtherText`,
                a.`TypePlantMateOtherNr`,
                a.`TypePlantMateDoNotKnow`,
                a.TypePlantMateDoNotKnowNr,
                a.TypePlantMateDolokSinumbah,
                a.TypePlantMateLame,
                a.TypePlantMateBahJambi,
                a.TypePlantMateAvros,
                a.TypePlantMatePPKS540,
                a.TypePlantMateYangambi,
                a.TypePlantMatePKS718,
                a.TypePlantMateLangkat,
                a.TypePlantMateTopaz,
                a.VarietyDura,
                a.VarietyTenera,
                a.VarietyPisifera,
                a.PercentageDura,
                a.`HarvestRateDaysHighSeason`,
                a.`HarvestRateDaysLowSeason`,
                a.`AverageProdHighSeason`,
                a.`AverageProdLowSeason`,
                a.NrHighSeasonMonths,
                a.NrLowSeasonMonths,
                a.HighSeasonProduction,
                a.LowSeasonProduction,
                a.AnnualProduction,
                a.`LeanHarvestSeasonJan`,
                a.`LeanHarvestSeasonFeb`,
                a.`LeanHarvestSeasonMar`,
                a.`LeanHarvestSeasonApr`,
                a.`LeanHarvestSeasonMay`,
                a.`LeanHarvestSeasonJun`,
                a.`LeanHarvestSeasonJul`,
                a.`LeanHarvestSeasonAug`,
                a.`LeanHarvestSeasonSep`,
                a.`LeanHarvestSeasonOct`,
                a.`LeanHarvestSeasonNov`,
                a.`LeanHarvestSeasonDec`,
                a.`WhoHarvestFamily`,
                a.`WhoHarvestLabor`,
                a.`UseEFBFertilizer`,
                a.`useParaquat`,
                a.TPHLoc,
                a.SubDistrictIDTPH,
                a.DistrictIDTPH,
                a.ProvinceIDTPH,
                a.Distance,
                a.`Comment`,
                a.OwnerDocIsOwner,
                a.HaveSTDB,
                a.HaveSPPL,
                a.HowObPlantation,
                a.TypePlantMateOtherText,
                a.PhotoOfVisitDesc,
                a.OwnerCultivateFarm,
                a.FarmEmployHiredLabor,
                a.FarmEmployFamMem,
                a.FarmEmployLaborFamMem,
                a.FarmEmployNoLabor,
                a.HowManyWorkFarm,
                a.UnderAgeWorker,
                a.AveHoursPerDay,
                a.AveDaysPerMonth,
                a.WageNominalPerDayLabor,
                a.WageNominalPerDayLaborPeriod,
                a.WageNominalPerDayFamMember,
                a.WageNominalPerDayFamMemberPeriod,
                a.HowManyDiffBuyerSoldLastYear,
                a.HowManyDiffBuyerSoldLastYearText,
                a.ToWhoSellFFBLastYear,
                a.HowManyDiffMillSoldLastYear,
                a.HowManyDiffMillSoldLastYearText,
                a.ToWhichMillSellFFBLastYear,
                a.ToWhichMillSellFFBLastYearText,
                a.TPHLocation,
                a.FertilizerDesc,
                a.FertilizerNotes,
                a.PesticideDesc,
                a.PesticideNotes,
                a.`FertNonOrganicData`,
                a.`FertMoneySpentNonOrganic`,
                a.`FertUreaTimesYear`,
                a.`FertUreaDose`,
                a.`FertSSTimesYear`,
                a.`FertSSDose`,
                a.`FertNPKTimesYear`,
                a.`FertNPKDose`,
                a.`FertTSPTimesYear`,
                a.`FertTSPDose`,
                a.`FertCUTimesYear`,
                a.`FertCUDose`,
                a.`FertKCLTimesYear`,
                a.`FertKCLDose`,
                a.`FertNPKMutiTimesYear`,
                a.`FertNPKMutiDose`,
                a.`FertBoratTimesYear`,
                a.`FertBoratDose`,
                a.`FertDolomiteTimesYear`,
                a.`FertDolomiteDose`,
                a.`FertWithNonOrgaTBM`,
                a.`FertWithNonOrgaTM`,
                a.`FertWithNonOrgaTR`,
                a.`FertUseOrganic`,
                a.`FertMoneySpentOrganic`,
                a.`FertPBATimesYear`,
                a.`FertPBADose`,
                a.`FertPBTimesYear`,
                a.`FertPBDose`,
                a.`FertCPBTimesYear`,
                a.`FertCPBDose`,
                a.`FertManureTimesYear`,
                a.`FertManureDose`,
                a.`FertWithOrgaTBM`,
                a.`FertWithOrgaTM`,
                a.`FertWithOrgaTR`,
                a.`PeUsingHerbicide`,
                a.`PeMoneySpentHerbi`,
                a.`PeFreqHerbi`,
                a.`PeDoseHerbi`,
                a.`PeHerbi1`,
                a.FrequencyPeHerbi1,
                a.`PeHerbi2`,
                a.FrequencyPeHerbi2,
                a.`PeHerbi3`,
                a.FrequencyPeHerbi3,
                a.`PeHerbi4`,
                a.FrequencyPeHerbi4,
                a.`PeHerbi5`,
                a.FrequencyPeHerbi5,
                a.`PeHerbi6`,
                a.FrequencyPeHerbi6,
                a.`PeHerbi7`,
                a.FrequencyPeHerbi7,
                a.`PeHerbi8`,
                a.FrequencyPeHerbi8,
                a.`PeHerbi9`,
                a.FrequencyPeHerbi9,
                a.`PeHerbi10`,
                a.FrequencyPeHerbi10,
                a.`PeHerbi11`,
                a.FrequencyPeHerbi11,
                a.`PeHerbi12`,
                a.FrequencyPeHerbi12,
                a.`PeHerbi13`,
                a.FrequencyPeHerbi13,
                a.`PeHerbi14`,
                a.FrequencyPeHerbi14,
                a.`PeHerbi15`,
                a.FrequencyPeHerbi15,
                a.`PeHerbi16`,
                a.FrequencyPeHerbi16,
                a.`PeHerbi17`,
                a.FrequencyPeHerbi17,
                a.`PeHerbi18`,
                a.FrequencyPeHerbi18,
                a.`PeHerbi19`,
                a.FrequencyPeHerbi19,
                a.`PeHerbi20`,
                a.FrequencyPeHerbi20,
                a.`PeHerbi21`,
                a.FrequencyPeHerbi21,
                a.`PeHerbi22`,
                a.FrequencyPeHerbi22,
                a.`PeHerbi23`,
                a.FrequencyPeHerbi23,
                a.`PeHerbi24`,
                a.FrequencyPeHerbi24,
                a.`PeHerbi25`,
                a.FrequencyPeHerbi25,
                a.`PeHerbi26`,
                a.FrequencyPeHerbi26,
                a.`PeHerbi27`,
                a.FrequencyPeHerbi27,
                a.`PeHerbi28`,
                a.FrequencyPeHerbi28,
                a.`PeHerbi29`,
                a.FrequencyPeHerbi29,
                a.`PeHerbiOther`,
                a.FrequencyPeHerbiOther,
                a.`PeUsingInsecticide`,
                a.`PeMoneySpentInsec`,
                a.`PeFreqInsec`,
                a.`PeDoseInsec`,
                a.`PeInsec1`,
                a.FrequencyPeInsec1,
                a.`PeInsec2`,
                a.FrequencyPeInsec2,
                a.`PeInsec3`,
                a.FrequencyPeInsec3,
                a.`PeInsec4`,
                a.FrequencyPeInsec4,
                a.`PeInsec5`,
                a.FrequencyPeInsec5,
                a.`PeInsec6`,
                a.FrequencyPeInsec6,
                a.`PeInsec7`,
                a.FrequencyPeInsec7,
                a.`PeInsec8`,
                a.FrequencyPeInsec8,
                a.`PeInsec9`,
                a.FrequencyPeInsec9,
                a.`PeInsec10`,
                a.FrequencyPeInsec10,
                a.`PeInsec11`,
                a.FrequencyPeInsec11,
                a.`PeInsec12`,
                a.FrequencyPeInsec12,
                a.`PeInsec13`,
                a.FrequencyPeInsec13,
                a.`PeInsec14`,
                a.FrequencyPeInsec14,
                a.`PeInsec15`,
                a.FrequencyPeInsec15,
                a.`PeInsec16`,
                a.FrequencyPeInsec16,
                a.`PeInsec17`,
                a.FrequencyPeInsec17,
                a.`PeInsec18`,
                a.FrequencyPeInsec18,
                a.`PeInsec19`,
                a.FrequencyPeInsec19,
                a.`PeInsec20`,
                a.FrequencyPeInsec20,
                a.`PeInsec21`,
                a.FrequencyPeInsec21,
                a.`PeInsec22`,
                a.FrequencyPeInsec22,
                a.`PeInsec23`,
                a.FrequencyPeInsec23,
                a.`PeInsecOther`,
                a.FrequencyPeInsecOther,
                a.`PeUsingFungicide`,
                a.`PeMoneySpentFungi`,
                a.`PeFreqFungi`,
                a.`PeDoseFungi`,
                a.`PeFungi1`,
                a.FrequencyPeFungi1,
                a.`PeFungi2`,
                a.FrequencyPeFungi2,
                a.`PeFungi3`,
                a.FrequencyPeFungi3,
                a.`PeFungi4`,
                a.FrequencyPeFungi4,
                a.`PeFungi5`,
                a.FrequencyPeFungi5,
                a.`PeFungi6`,
                a.FrequencyPeFungi6,
                a.`PeFungi7`,
                a.FrequencyPeFungi7,
                a.`PeFungi8`,
                a.FrequencyPeFungi8,
                a.`PeFungi9`,
                a.FrequencyPeFungi9,
                a.`PeFungi10`,
                a.FrequencyPeFungi10,
                a.`PeFungi11`,
                a.FrequencyPeFungi11,
                a.`PeFungi12`,
                a.FrequencyPeFungi12,
                a.`PeFungiOther`,
                a.FrequencyPeFungiOther,
                a.`PestMain`,
                a.SeverityPest,
                a.`PestMainRats`,
                a.`PestMainOly`,
                a.`PestMainSatora`,
                a.`PestMainTira`,
                a.`PestMainRhino`,
                a.`PestMainElep`,
                a.`PestMainOrgUtan`,
                a.`PestMainLandak`,
                a.`PestMainBabi`,
                a.`PestMainOther`,
                a.`PestMainOtherText`,
                a.SeverityDisease,
                a.UnknownReasonDying,
                a.UnknownReasonDyingSpear,
                a.UnknownReasonDyingTrunk,
                a.UnknownReasonDyingOther,
                a.UnknownReasonDyingOtherText,
                a.PalmsDiedLastTwoYears,
                a.DisMain,
                a.UseProtectiveGear,
                a.ManagerOfTheGarden,
                a.EquipHelm,
                a.EquipBoots,
                a.EquipDodosProtector,
                a.EquipMask,
                a.EquipGloves,
                a.EquipSprayGlasses,
                a.EquipEgrekProtector,
                a.EquipProtectiveClothing,
                a.PestStoreLocation,
                a.PestPackageAfterUse,
                a.`DisMainBlast`,
                a.`DisMainGeno`,
                a.`DisMainSteam`,
                a.`DisMainBud`,
                a.`DisMainSpear`,
                a.`DisMainYellow`,
                a.`DisMainAnt`,
                a.`DisMainCrown`,
                a.`DisMainViscular`,
                a.`DisMainBunch`,
                a.`DisMainOther`,
                a.`DisMainOtherText`,
                a.GarWitnessProveOwnership,
                a.GarNameOfWitness,
                a.GarOwnerRelationship,
                a.YearPlantingCurrent,
                a.WagsCert,
                a.WagsCertStandardRSPO,
                a.WagsCertStandardMSPO,
                a.WagsPlantationStage,
                a.WagsCondEstPlantation,
                a.FarmPhoto,
                a.FarmPhotoDesc,
                a.PresentedBy,
                a.Signature,
                a.RecipientDealer,
                a.FarmManager,
                a.LandLegality,
                a.HaveLandSiputes,
                a.HaveLandSiputesText,
                a.Respondent,
                a.FarmStatus,
                a.InactiveReason,
                a.SwitchCommodityCorn,
                a.SwitchCommodityCocoa,
                a.SwitchCommodityRubber,
                a.SwitchCommodityClove,
                a.SwitchCommodityRice,
                a.SwitchCommodityFruits,
                a.SwitchCommodityTimber,
                a.SwitchCommodityOther,
                a.SwithCommodityAreaHa,
                a.LandUseStatus,
                a.PlantationBoundaries,
                a.WllManagePeatLand,
                a.TailoredPeatLand,
                a.MonitorRatePeatLand,
                a.NotAppliedPeatLand,
                a.AnyWaterBodies,
                a.AnyWaterBodiesHCV,
                a.WaterBody15m,
                a.WaterBody510m,
                a.WaterBody1020m,
                a.WaterBody2040m,
                a.WaterBody4050m,
                a.WaterBody50m,
                a.WaterBodyFar,
                a.SoilErotion,
                a.SoilAccumulation,
                a.QualityVegetarian,
                a.UseFirePreparation,
                a.UseFirePestControl,
                a.UseFireWasteManagement,
                a.UseFirePast,
                a.UseFireNever,
                IFNULL(ST_Y(a.FireVisableLatLong), a.FireVisableLatitude) FireVisableLatitude,
                IFNULL(ST_X(a.FireVisableLatLong), a.FireVisableLongitude) FireVisableLongitude,
                a.HCVApproach,
                a.HCSApproach,
                a.ImplementRSPOApprovedPlan,
                a.PlantingMaterialBy,
                a.PlantingMaterialFrom,
                a.PerceiveFFB,
                a.TellNeedApply,
                a.ChangeRateFertilizer,
                a.ChangeTypeFertilizer,
                a.DecideApplyFertilizer,
                a.AreaNotFertilizer,
                a.WhyNotApplyFertilizer,
                a.AppliedHerbicide,
                a.InsecAppliedOn,
                a.InsecRatControl,
                a.InsecCaterpillarControl,
                a.InsecOryctesControl,
                a.PestApplies,
                a.InstallBarnOwl,
                a.BeneficialPlants,
                a.PlanAcquireNewPalmOil,
                a.LocalAreaPlanned,
                a.FungiAppliedOn,
                a.ObtainConsent,
                a.InvolveLocalPeople,
                a.AcquireCoercing,
                a.StartDevPlantation,
                a.ProvideOwnerRelevantInform,
                a.HCVHCSApprocah,
                a.WrittenAgreement,
                a.BareLandPlantationArea,
                a.FoodCropsPlantationArea,
                a.MangrovePlantationArea,
                a.OtherPlantationArea,
                a.OilPalmPlantationArea,
                a.ForestPlantationArea,
                a.InfrastructurePlantationArea,
                a.StepSlopesPlantationArea,
                a.DontKnowPlantationArea,
                a.HowObPlantationText,
                a.HowObPlantationInheritance,
                a.HowObPlantationPurchased,
                a.HowObPlantationConvert,
                a.HowObPlantationReceived,
                a.HowObPlantationOther,
                a.AdditionalLocation,
                a.PlanReplanting,
                a.AnyPalmAttack,
                a.AnyPalmAttackDisease,
                a.DeliveryByPhoto,
                a.DocumentWritten,                
                a.SocAnimalProtect,
                a.ProvideSocAnimalProtect,
                a.ProvideSocAnimalProtectText,
                a.AnimalMonkey,
                a.AnimalDeer,
                a.AnimalElephant,
                a.AnimalCat,
                a.AnimalTiger,
                a.AnimalBear,
                a.AnimalOrangutans,
                a.AnimalGibbons,
                a.AnimalSlowloris,
                a.AnimalPorcupine,
                a.AnimalPangolin,
                a.AnimalEagle,
                a.AnimalKingfishers,
                a.AnimalHornbills,
                a.AnimalPeacock,
                a.AnimalPittas,
                a.AnimalParrots,
                a.AnimalOthers,
                a.AnimalOtherText,
                a.AnimalNone,
                a.DoAnimalUnbothered,
                a.DoAnimalCatch,
                a.DoAnimalHunt,
                a.DoAnimalSell,
                a.DoAnimalCare,
                a.DoAnimalOther,
                a.DoAnimalOtherText,
                a.PlantAroundIronWood,
                a.PlantAroundMerbau,
                a.PlantAroundSialang,
                a.PlantAroundPitcher,
                a.PlantAroundVenus,
                a.PlantAroundRafflesia,
                a.PlantAroundGiant,
                a.PlantAroundOther,
                a.PlantAroundNone,
                a.PlantAroundOtherText,
                a.PlantAcrossElephant,
                a.PlantAcrossTiger,
                a.PlantAcrossBird,
                a.PlantAcrossOrangutan,
                a.PlantAcrossNone,
                a.WaterBodies,
                a.BigRiver1,
                a.BigRiver5,
                a.BigRiver10,
                a.BigRiver20,
                a.BigRiver40,
                a.BigRiver50,
                a.HowFarWatebody,
                a.GrownOnEcological,
                a.GrownOnWetland,
                a.UsedLocalCommunity,
                a.KindUtilizeFulfill,
                a.KindUtilizeSeasonal,
                a.KindUtilizeConstuct,
                a.KindUtilizeFirewood,
                a.KindUtilizeSource,
                a.DistinctCulture,
                a.DistinctCultureForest,
                a.DistinctCultureGraves,
                a.DistinctCulturePerform,
                a.DistinctCultureCultural
            FROM
                `ktv_survey_plot` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.`SubDistrictID` = d.`SubDistrictID`
            WHERE
                a.`MemberID` = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`DateCollection` = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            (int) $PlotNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $sql2   = "SELECT CollectpointID FROM ktv_collecting_point WHERE OrgID = ? AND PlantationNr = ? AND OrgType = 'farmer'";
        $query2 = $this->db->query($sql2, array($data['MemberID'], $data['PlotNr']));

        if($query2->num_rows()>0){
            $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AsCollectingPoint'] = 1;
        }

        //yg diperlukan untuk proses lebih lanjut
        $dataRow['MemberDisplayID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberDisplayID'];
        $dataRow['MemberUID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberUID'];
        $dataRow['ProvinceID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceID'];
        $dataRow['DistrictID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictID'];
        $dataRow['SubDistrictID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID'];
        $dataRow['VillageID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID'];
        $dataRow['PhotoOfVisit'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisit'];
        $dataRow['PhotoOfFire'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFire'];
        $dataRow['SoilErotionFile'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilErotionFile'];
        $dataRow['SoilAccumulationFile'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilAccumulationFile'];
        $dataRow['OwnerDocPhoto'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocPhoto'];
        $dataRow['FarmPhoto'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmPhoto'];
        $dataRow['ProvinceIDTPH'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceIDTPH'];
        $dataRow['DistrictIDTPH'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictIDTPH'];
        $dataRow['SubDistrictIDTPH'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictIDTPH'];
        $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarm'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmPhoto'];
        $dataRow['Signature'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Signature'];
        $dataRow['Certification'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Certification'];
        $dataRow['DeliveryByPhoto'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DeliveryByPhoto'];
        $dataRow['DocumentWritten'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DocumentWritten'];

        if($this->awsfileupload->doesObjectExist($dataRow['PhotoOfVisit']) == true) {
            $dataRow['PhotoOfVisitPath']    = $dataRow['PhotoOfVisit'];
            $dataRow['PhotoOfVisit']        = $this->config->item('CTCDN')."/".$dataRow['PhotoOfVisit'];
        }else{
            if($dataRow['PhotoOfVisitPath'] != ''){
                $dataRow['PhotoOfVisitPath']    = '/images/plot_visit/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfVisit'];
                $dataRow['PhotoOfVisit']        = base_url().'images/plot_visit/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfVisit'];
            }
        }
        
        if($this->awsfileupload->doesObjectExist($dataRow['PhotoOfFire']) == true) {
            $dataRow['PhotoOfFirePath']    = $dataRow['PhotoOfFire'];
            $dataRow['PhotoOfFire']        = $this->config->item('CTCDN')."/".$dataRow['PhotoOfFire'];
        }else{
            if($dataRow['PhotoOfFirePath'] != ''){
                $dataRow['PhotoOfFirePath']    = '/images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfFire'];
                $dataRow['PhotoOfFire']        = base_url().'images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfFire'];
            }
        }
        
        if($this->awsfileupload->doesObjectExist($dataRow['SoilErotionFile']) == true) {
            $dataRow['SoilErotionFilePath']    = $dataRow['SoilErotionFile'];
            $dataRow['SoilErotionFile']        = $this->config->item('CTCDN')."/".$dataRow['SoilErotionFile'];
        }else{
            if($dataRow['SoilErotionFilePath'] != ''){
                $dataRow['SoilErotionFilePath']    = '/images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['SoilErotionFile'];
                $dataRow['SoilErotionFile']        = base_url().'images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['SoilErotionFile'];
            }
        }
        
        if($this->awsfileupload->doesObjectExist($dataRow['SoilAccumulationFile']) == true) {
            $dataRow['SoilAccumulationFilePath']    = $dataRow['SoilAccumulationFile'];
            $dataRow['SoilAccumulationFile']        = $this->config->item('CTCDN')."/".$dataRow['SoilAccumulationFile'];
        }else{
            if($dataRow['SoilAccumulationFilePath'] != ''){
                $dataRow['SoilAccumulationFilePath']    = '/images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['SoilAccumulationFile'];
                $dataRow['SoilAccumulationFile']        = base_url().'images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['SoilAccumulationFile'];
            }
        }
        
        if($this->awsfileupload->doesObjectExist($dataRow['OwnerDocPhoto']) == true) {
            $dataRow['OwnerDocPhotoPath']    = $dataRow['OwnerDocPhoto'];
            $dataRow['OwnerDocPhoto']        = $this->config->item('CTCDN')."/".$dataRow['OwnerDocPhoto'];
        }else{
            if($dataRow['OwnerDocPhotoPath'] != ''){
                $dataRow['OwnerDocPhotoPath']    = '/images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['OwnerDocPhoto'];
                $dataRow['OwnerDocPhoto']        = base_url().'images/plot_fire/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['OwnerDocPhoto'];
            }
        }

        if($this->awsfileupload->doesObjectExist($dataRow['FarmPhoto']) == true) {
            $dataRow['FarmPhotoPath']       = $dataRow['FarmPhoto'];
            $dataRow['FarmPhoto']           = $this->config->item('CTCDN')."/".$dataRow['FarmPhoto'];
        }else{
            if($dataRow['FarmPhotoPath'] != ''){
                $dataRow['FarmPhotoPath']   = '/images/plot_farm_sme/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['FarmPhoto'];
                $dataRow['FarmPhoto']       = base_url().'images/plot_farm_sme/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['FarmPhoto'];
            }
        }

        if($this->awsfileupload->doesObjectExist($dataRow['DeliveryByPhoto']) == true) {
            $dataRow['DeliveryByPhotoPath']       = $dataRow['DeliveryByPhoto'];
            $dataRow['DeliveryByPhoto']           = $this->config->item('CTCDN')."/".$dataRow['DeliveryByPhoto'];
        }else{
            if($dataRow['DeliveryByPhotoPath'] != ''){
                $dataRow['DeliveryByPhotoPath']   = '/images/plot_farm/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['DeliveryByPhoto'];
                $dataRow['DeliveryByPhoto']       = base_url().'images/plot_farm/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['DeliveryByPhoto'];
            }
        }

        if($this->awsfileupload->doesObjectExist($dataRow['DocumentWritten']) == true) {
            $dataRow['DocumentWrittenPath']       = $dataRow['DocumentWritten'];
            $dataRow['DocumentWritten']           = $this->config->item('CTCDN')."/".$dataRow['DocumentWritten'];
        }else{
            if($dataRow['DocumentWrittenPath'] != ''){
                $dataRow['DocumentWrittenPath']   = '/images/plot_farm/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['DocumentWritten'];
                $dataRow['DocumentWritten']       = base_url().'images/plot_farm/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['DocumentWritten'];
            }
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function updateDeliveryImage($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    DeliveryByPhoto = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    DeliveryByPhoto = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updateDocumentWritten($User,$SurveyID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_certification
                SET
                    DocumentWritten = '$filenamepath'
                WHERE SurveyID = '$SurveyID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    DocumentWritten = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updateFarmPhoto($User,$SurveyID,$filenamepath){
        $sql = "
            UPDATE
                ktv_survey_certification
            SET
                FarmPhoto = '$filenamepath'
            WHERE SurveyID = '$SurveyID'
        ";
        $this->db->query($sql);
    }

    public function updateFarmImage($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    FarmPhoto = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    FarmPhoto = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updatePlotImage($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    PhotoOfVisit = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    PhotoOfVisit = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updatePlotImageOwnerDoc($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    OwnerDocPhoto = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    OwnerDocPhoto = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updatePlotImageFire($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    PhotoOfFire = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    PhotoOfFire = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updatePlotImageSoilErotion($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    SoilErotionFile = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    SoilErotionFile = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function updatePlotImageSoilAccumulation($User,$MemberID,$filenamepath){
        if($User == "Farmer" OR $User == ""){
            $sql = "
                UPDATE
                    ktv_survey_plot
                SET
                    SoilAccumulationFile = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        if($User == "SME"){
            $sql = "
                UPDATE
                    ktv_survey_plot_sme
                SET
                    SoilAccumulationFile = '$filenamepath'
                WHERE MemberID = '$MemberID'
            ";
        }
        $this->db->query($sql);
    }

    public function delete_survey($MemberID,$SurveyID,$DateCollection){
        $this->db->trans_start();
        
        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];
        $paramPost['StatusCode'] = 'nullified';

        //update
        $this->db->where("SurveyID", $SurveyID);
        $this->db->update('ktv_survey_certification', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        }else{
            $results["success"] = false;
            $results['message'] = "Failed to Save Data";
        }

        return $results;
    }

    public function update_survey($paramPost){
        $this->db->trans_start();
        
        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        $SurveyID = $paramPost["SurveyID"];

        //buang var yg tidak perlu (begin)
        unset($paramPost['SurveyID']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['DocumentWrittenOld']);
        unset($paramPost['FarmPhotoOld']);

        //update
        $this->db->where("SurveyID", $SurveyID);
        $this->db->update('ktv_survey_certification', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        }else{
            $results["success"] = false;
            $results['message'] = "Failed to Save Data";
        }

        return $results;
    }

    public function insert_survey($paramPost){
        $this->db->trans_start();
        
        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;

        $DocumentWritten = $paramPost["DocumentWrittenOld"];
        $FarmPhoto = $paramPost["FarmPhotoOld"];

        //buang var yg tidak perlu (begin)
        unset($paramPost['opsiDisplay']);
        unset($paramPost['DocumentWrittenOld']);
        unset($paramPost['FarmPhotoOld']);

        //insert
        $this->db->insert('ktv_survey_certification', $paramPost);
        $SurveyID = $this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($DocumentWritten != ""){
                //get ext nya..
                $arrTemp = explode(".", $DocumentWritten);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';
    
                //foto di upload ke AWS
                if(file_exists($DocumentWritten)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$DocumentWritten);
                    
                    $upload = $this->awsfileupload->upload($DocumentWritten,$file[2],AWSS3_FARMER_SURVEY_CERT_PATH, 'images');
                        
                    if ($upload['success'] == true) {
                        delete_file($DocumentWritten);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_certification a SET
                            a.`DocumentWritten` = ?
                        WHERE
                            a.`SurveyID` = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $SurveyID
                    );
                    $query = $this->db->query($sql,$p);
                }
            }

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($FarmPhoto != ""){
                //get ext nya..
                $arrTemp = explode(".", $FarmPhoto);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';
    
                //foto di upload ke AWS
                if(file_exists($FarmPhoto)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$FarmPhoto);
                    
                    $upload = $this->awsfileupload->upload($FarmPhoto,$file[2],AWSS3_FARMER_PLOT_PATH, 'images');
                        
                    if ($upload['success'] == true) {
                        delete_file($FarmPhoto);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_certification a SET
                            a.`FarmPhoto` = ?
                        WHERE
                            a.`SurveyID` = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $SurveyID
                    );
                    $query = $this->db->query($sql,$p);
                }
            }

        }else{
            $results["success"] = false;
            $results['message'] = "Failed to Save Data";
        }

        return $results;
    }

    public function GetMemberName($MemberID){
        $sql    = "SELECT MemberName FROM ktv_members WHERE MemberID = ?";
        $query  = $this->db->query($sql, array($MemberID))->row_array();

        return $query["MemberName"];
    }

    public function insertPlotSurvey($paramPost,$MemberData){
        $this->db->trans_start();
        $this->load->model('grower/mgrower');
        $uid = $this->mgrower->getUID();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $PhotoOfVisit = $paramPost['PhotoOfVisitOld'];
        $FarmPhoto = $paramPost['PhotoOfFarmOld'];
        $SignaturePhoto = $paramPost['SignatureOld'];
        $PhotoofFire = $paramPost['PhotoofFireOld'];
        $PhotoSoilErotion = $paramPost['PhotoSoilErotionOld'];
        $PhotoSoilAccumulation = $paramPost['PhotoSoilAccumulationOld'];
        $PhotoOwnerDoc = $paramPost['PhotoOwnershipDocOld'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ProvinceID']);
        unset($paramPost['DistrictID']);
        unset($paramPost['SubDistrictID']);
        unset($paramPost['PhotoOfVisitOld']);
        unset($paramPost['PhotoofFireOld']);
        unset($paramPost['PhotoSoilErotionOld']);
        unset($paramPost['PhotoSoilAccumulationOld']);
        unset($paramPost['PhotoOwnershipDocOld']);
        unset($paramPost['PhotoOfFarmOld']);
        unset($paramPost['SignatureOld']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['TypePlantMateTotalTreeNr']);
        unset($paramPost['TreeTotalTBMTMTR']);
        unset($paramPost['FertUreaDosePlotYear']);
        unset($paramPost['FertSSDosePlotYear']);
        unset($paramPost['FertNPKDosePlotYear']);
        unset($paramPost['FertTSPDosePlotYear']);
        unset($paramPost['FertCUDosePlotYear']);
        unset($paramPost['FertKCLDosePlotYear']);
        unset($paramPost['FertNPKMutiDosePlotYear']);
        unset($paramPost['FertBoratDosePlotYear']);
        unset($paramPost['FertDolomiteDosePlotYear']);
        unset($paramPost['FertPBADosePlotYear']);
        unset($paramPost['FertPBDosePlotYear']);
        unset($paramPost['FertCPBDosePlotYear']);
        unset($paramPost['FertManureDosePlotYear']);
        unset($paramPost['PeTotalUsageHerbi']);
        unset($paramPost['PeTotalUsageInsec']);
        unset($paramPost['PeTotalUsageFungi']);
        unset($paramPost['GardenAreaPolygon']);
        unset($paramPost['TreeTotalTBMTMTRPerHa']);
        unset($paramPost['DeliveryByOld']);
        unset($paramPost['DocumentWrittenOld']);
        unset($paramPost['AnyWaterBodiesHidden']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;
        $paramPost['uid']   = $uid;

        //insert
        $this->db->insert('ktv_survey_plot', $paramPost);

        //Collecting Point//
        if($paramPost['AsCollectingPoint'] == "1"){
            $this->load->model('grower/mgrower');
            $uid = $this->mgrower->getUID();

            $CollectData["CollectpointDisplayID"]   = $this->GenTphID("farmer");
            $CollectData["OrgType"]                 = "farmer";
            $CollectData["OrgID"]                   = $paramPost["MemberID"];
            $CollectData["PlantationNr"]            = $paramPost["PlotNr"];
            $CollectData["CollectpointName"]        = $this->GetMemberName($paramPost["MemberID"]);
            $CollectData["VillageID"]               = $paramPost["VillageID"];
            $CollectData["CollectpointAddress"]     = $paramPost["AdditionalLocation"];
            $CollectData["Longitude"]               = $paramPost["Longitude"];
            $CollectData["Latitude"]                = $paramPost["Latitude"];
            $CollectData["StatusCode"]              = 'active';
            $CollectData["DateCreated"]             = date("Y-m-d H:i:s");
            $CollectData["CreatedBy"]               = $_SESSION['userid'];
            $CollectData["uid"]                     = $uid;

            $sql2   = "SELECT CollectpointID FROM ktv_collecting_point WHERE OrgID = ? AND PlantationNr = ? AND OrgType = 'farmer'";
            $query2 = $this->db->query($sql2, array($paramPost["MemberID"], $paramPost["PlotNr"]));

            if($query2->num_rows() == 0){
                $this->db->set("LatLong", "ST_GEOMFROMTEXT('POINT($paramPost[Latitude] $paramPost[Longitude])')",false);
                $this->db->insert("ktv_collecting_point", $CollectData);
            }
        }
        //Collecting Point//

        if($paramPost['Certification'] != "" && $paramPost['Longitude'] != "") {
            $sql = "INSERT INTO ktv_certification (
				FarmerID,
				GardenNr,
				SurveyNr,
				Certification,
				CreatedBy,
				DateCreated
			)
			VALUES (
				?,?,?,?,?,NOW()
			)
			ON DUPLICATE KEY UPDATE
				LastModifiedBy = ?,
				DateUpdated = NOW()";
			$p = array(
				$paramPost['MemberID'],
				$paramPost['PlotNr'],
				$paramPost['SurveyNr'],
				$paramPost['Certification'],
				$_SESSION['userid'],
				$_SESSION['userid']
			);
			$query = $this->db->query($sql,$p);
        }

        if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {

            $LatitudeProses = (float) $paramPost['Latitude'];
            $LongitudeProses = (float) $paramPost['Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                //Cek valid tidak koordinatnya
                $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql2)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
                    $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                    $sql2 = "UPDATE ktv_survey_plot a SET
                                a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                            WHERE
                                a.`MemberID` = ?
                                AND a.`PlotNr` = ?
                                AND a.`SurveyNr` = ?
                            LIMIT 1";
                    $p = array(
                        $paramPost["MemberID"],
                        $paramPost["PlotNr"],
                        $paramPost["SurveyNr"]
                    );
                    $query = $this->db->query($sql2,$p);

                    if($query){
                        $sql2 = "UPDATE ktv_survey_plot_status a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`MemberID` = ?
                                AND a.`PlotNr` = ?
                                LIMIT 1";
                        $p = array(
                            $paramPost["MemberID"],
                            $paramPost["PlotNr"]
                        );
                        $query = $this->db->query($sql2,$p);
                    }
                }

            }
        }

        //Plot Status ==================================================== (Begin)
        $sql = "INSERT INTO `ktv_survey_plot_status` (
            `MemberID`,
            `PlotNr`,
            `ActiveStatus`,
            Remark,
            `DateCreated`,
            `CreatedBy`
        )
        SELECT
            t_gar.MemberID
            , t_gar.PlotNr
            , '1'
            , 'Insert dari script penyesuaian garden status'
            , NOW()
            , '1'
        FROM (
            SELECT
                gar.`MemberID`
                , gar.`PlotNr`
            FROM
                ktv_survey_plot gar
            WHERE
                gar.`MemberID` != '0'
                AND gar.`PlotNr` != '0'
            GROUP BY gar.`MemberID`, gar.`PlotNr`
        ) AS t_gar
        LEFT JOIN (
            SELECT
                gstat.`MemberID`
                , gstat.`PlotNr`
                , gstat.`ActiveStatus`
            FROM
                `ktv_survey_plot_status` gstat
            WHERE
                gstat.`MemberID` != '0'
                AND gstat.`PlotNr` != '0'
        ) AS t_garstat ON 1=1
            AND t_gar.MemberID = t_garstat.MemberID
            AND t_gar.PlotNr = t_garstat.PlotNr
        WHERE
            t_garstat.MemberID IS NULL
            AND t_gar.MemberID = ?
            AND t_gar.PlotNr = ?
        ";
        $query = $this->db->query($sql, array($paramPost['MemberID'],$paramPost['PlotNr']));

        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`AnnualProduction`
                    FROM
                        `ktv_survey_plot` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction";
        $query = $this->db->query($sql, array($paramPost['MemberID'],$paramPost['PlotNr']));
        
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                    FROM
                        `ktv_survey_plot` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude";
        $query = $this->db->query($sql, array($paramPost['MemberID'],$paramPost['PlotNr']));
        //Plot Status ==================================================== (End)

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($PhotoOfVisit != ""){
                //get ext nya..
                $arrTemp = explode(".", $PhotoOfVisit);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';

                //foto di upload ke AWS
                if(file_exists($PhotoOfVisit)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$PhotoOfVisit);
                    if($file[1] == 'plot_visit_sme'){
                        $upload = $this->awsfileupload->upload($PhotoOfVisit,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($PhotoOfVisit,$file[3],AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    if ($upload['success'] == true) {
                        delete_file($PhotoOfVisit);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`PhotoOfVisit` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($PhotoofFire != ""){
                //get ext nya..
                $arrTemp = explode(".", $PhotoofFire);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';

                //foto di upload ke AWS
                if(file_exists($PhotoofFire)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$PhotoofFire);
                    if($file[1] == 'plot_fire_sme'){
                        $upload = $this->awsfileupload->upload($PhotoofFire,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($PhotoofFire,$file[3],AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    if ($upload['success'] == true) {
                        delete_file($PhotoofFire);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`PhotoofFire` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($PhotoSoilErotion != ""){
                //get ext nya..
                $arrTemp = explode(".", $PhotoSoilErotion);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';

                //foto di upload ke AWS
                if(file_exists($PhotoSoilErotion)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$PhotoSoilErotion);
                    if($file[1] == 'plot_soil_erotion_sme'){
                        $upload = $this->awsfileupload->upload($PhotoSoilErotion,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($PhotoSoilErotion,$file[3],AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    if ($upload['success'] == true) {
                        delete_file($PhotoSoilErotion);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`SoilErotionFile` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($PhotoSoilAccumulation != ""){
                //get ext nya..
                $arrTemp = explode(".", $PhotoSoilAccumulation);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';

                //foto di upload ke AWS
                if(file_exists($PhotoSoilAccumulation)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$PhotoSoilAccumulation);
                    if($file[1] == 'plot_soil_acc_sme'){
                        $upload = $this->awsfileupload->upload($PhotoSoilAccumulation,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($PhotoSoilAccumulation,$file[3],AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    if ($upload['success'] == true) {
                        delete_file($PhotoSoilAccumulation);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`SoilAccumulationFile` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($PhotoOwnerDoc != ""){
                //get ext nya..
                $arrTemp = explode(".", $PhotoOwnerDoc);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = '';

                //foto di upload ke AWS
                if(file_exists($PhotoOwnerDoc)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$PhotoOwnerDoc);
                    if($file[1] == 'plot_docs_sme'){
                        $upload = $this->awsfileupload->upload($PhotoOwnerDoc,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($PhotoOwnerDoc,$file[3],AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    if ($upload['success'] == true) {
                        delete_file($PhotoOwnerDoc);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`OwnerDocPhoto` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($FarmPhoto != ""){
                //get ext nya..
                $arrTemp = explode(".", $FarmPhoto);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = "";

                //foto di upload ke AWS
                if(file_exists($FarmPhoto)) {
                    $this->load->library('awsfileupload');
                    $file = explode("/",$FarmPhoto);
                    if($file[1] == 'plot_farm'){
                        $upload = $this->awsfileupload->upload($FarmPhoto,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($FarmPhoto,$file[3],AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    if ($upload['success'] == true) {
                        delete_file($FarmPhoto);
                        $namaFileGambar = $upload['filenamepath'];
                    }
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`FarmPhoto` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($SignaturePhoto != ""){
                //get ext nya..
                $arrTemp = explode(".", $SignaturePhoto);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/plot_signature/'.$MemberData['ProvinceID'])){
                    mkdir('images/plot_signature/'.$MemberData['ProvinceID'], 0777, true);
                }
                if(!file_exists('images/plot_signature/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'])){
                    mkdir('images/plot_signature/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'], 0777, true);
                }

                $gambarTujuan = 'images/plot_signature/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'].'/'.$namaFileGambar;
                if(rename('images/plot_signature/'.$SignaturePhoto,$gambarTujuan)){
                    $sql="UPDATE ktv_survey_plot a SET
                            a.`Signature` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }

        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    private function resetAllFieldPlot($MemberID,$PlotNr,$SurveyNr,$DateCollection){
        $sql="UPDATE `ktv_survey_plot` SET
                `VillageID` = null,
                `PhotoOfVisitDesc` = null,
                `GardenAreaHa` = null,
                `GardenLength` = null,
                `GardenWidth` = null,
                `Latitude` = null,
                `Longitude` = null,
                `OwnershipDoc` = null,
                `OwnershipDocText` = null,
                `OwnerDocIsOwner` = null,
                `HaveSTDB` = null,
                `HaveSPPL` = null,
                `BusinessModel` = null,
                `LandOwnership` = null,
                LandOwnershipType = null,
                OwnerOfTheGarden = null,
                `HowObPlantation` = null,
                `HowObPlantationText` = null,
                `PlantationConditionEst` = null,
                `AverageAgeTree` = null,
                `SoilType` = null,
                `TopographyType` = null,
                `FirstPlantingYear` = null,
                TreeTBM = null,
                TreeTM = null,
                TreeTR = null,
                `TypePlantMateMarihat` = null,
                `TypePlantMateMarihatNr` = null,
                `TypePlantMateDumpy` = null,
                `TypePlantMateDumpyNr` = null,
                `TypePlantMateLonsum` = null,
                `TypePlantMateLonsumNr` = null,
                `TypePlantMateSimalungun` = null,
                `TypePlantMateSimalungunNr` = null,
                `TypePlantMateDanimas` = null,
                `TypePlantMateDanimasNr` = null,
                `TypePlantMateSriwijaya` = null,
                `TypePlantMateSriwijayaNr` = null,
                `TypePlantMateSocfin` = null,
                `TypePlantMateSocfinNr` = null,
                `TypePlantMateOther` = null,
                `TypePlantMateOtherText` = null,
                `TypePlantMateOtherNr` = null,
                `TypePlantMateDoNotKnow` = null,
                TypePlantMateDoNotKnowNr = null,
                `OwnerCultivateFarm` = null,
                `FarmEmployHiredLabor` = null,
                `FarmEmployFamMem` = null,
                `FarmEmployLaborFamMem` = null,
                `FarmEmployNoLabor` = null,
                `HowManyWorkFarm` = null,
                `UnderAgeWorker` = null,
                `AveHoursPerDay` = null,
                `AveDaysPerMonth` = null,
                `WageNominalPerDayLabor` = null,
                `WageNominalPerDayLaborPeriod` = null,
                `WageNominalPerDayFamMember` = null,
                `WageNominalPerDayFamMemberPeriod` = null,
                `HarvestRateDaysHighSeason` = null,
                `HarvestRateDaysLowSeason` = null,
                `AverageProdHighSeason` = null,
                `AverageProdLowSeason` = null,
                NrHighSeasonMonths = null,
                NrLowSeasonMonths = null,
                HighSeasonProduction = null,
                LowSeasonProduction = null,
                AnnualProduction = null,
                PlantationProductivity = null,
                `LeanHarvestSeasonJan` = null,
                `LeanHarvestSeasonFeb` = null,
                `LeanHarvestSeasonMar` = null,
                `LeanHarvestSeasonApr` = null,
                `LeanHarvestSeasonMay` = null,
                `LeanHarvestSeasonJun` = null,
                `LeanHarvestSeasonJul` = null,
                `LeanHarvestSeasonAug` = null,
                `LeanHarvestSeasonSep` = null,
                `LeanHarvestSeasonOct` = null,
                `LeanHarvestSeasonNov` = null,
                `LeanHarvestSeasonDec` = null,
                `WhoHarvestFamily` = null,
                `WhoHarvestLabor` = null,
                `HowManyDiffBuyerSoldLastYear` = null,
                `HowManyDiffBuyerSoldLastYearText` = null,
                `ToWhoSellFFBLastYear` = null,
                `HowManyDiffMillSoldLastYear` = null,
                `HowManyDiffMillSoldLastYearText` = null,
                `ToWhichMillSellFFBLastYear` = null,
                `ToWhichMillSellFFBLastYearText` = null,
                `UseEFBFertilizer` = null,
                `FertilizerDesc` = null,
                `FertilizerNotes` = null,
                `useParaquat` = null,
                `PesticideDesc` = null,
                `PesticideNotes` = null,
                `Comment` = null,
                `FertNonOrganicData` = null,
                `FertMoneySpentNonOrganic` = null,
                `FertUreaTimesYear` = null,
                `FertUreaDose` = null,
                `FertSSTimesYear` = null,
                `FertSSDose` = null,
                `FertNPKTimesYear` = null,
                `FertNPKDose` = null,
                `FertTSPTimesYear` = null,
                `FertTSPDose` = null,
                `FertCUTimesYear` = null,
                `FertCUDose` = null,
                `FertKCLTimesYear` = null,
                `FertKCLDose` = null,
                `FertNPKMutiTimesYear` = null,
                `FertNPKMutiDose` = null,
                `FertBoratTimesYear` = null,
                `FertBoratDose` = null,
                `FertDolomiteTimesYear` = null,
                `FertDolomiteDose` = null,
                `FertWithNonOrgaTBM` = null,
                `FertWithNonOrgaTM` = null,
                `FertWithNonOrgaTR` = null,
                `FertUseOrganic` = null,
                `FertMoneySpentOrganic` = null,
                `FertPBATimesYear` = null,
                `FertPBADose` = null,
                `FertPBTimesYear` = null,
                `FertPBDose` = null,
                `FertCPBTimesYear` = null,
                `FertCPBDose` = null,
                `FertManureTimesYear` = null,
                `FertManureDose` = null,
                `FertWithOrgaTBM` = null,
                `FertWithOrgaTM` = null,
                `FertWithOrgaTR` = null,
                `PeUsingHerbicide` = null,
                `PeMoneySpentHerbi` = null,
                `PeFreqHerbi` = null,
                `PeDoseHerbi` = null,
                `PeHerbi1` = null,
                `PeHerbi2` = null,
                `PeHerbi3` = null,
                `PeHerbi4` = null,
                `PeHerbi5` = null,
                `PeHerbi6` = null,
                `PeHerbi7` = null,
                `PeHerbi8` = null,
                `PeHerbi9` = null,
                `PeHerbi10` = null,
                `PeHerbi11` = null,
                `PeHerbi12` = null,
                `PeHerbi13` = null,
                `PeHerbi14` = null,
                `PeHerbi15` = null,
                `PeHerbi16` = null,
                `PeHerbi17` = null,
                `PeHerbi18` = null,
                `PeHerbi19` = null,
                `PeHerbi20` = null,
                `PeHerbi21` = null,
                `PeHerbi22` = null,
                `PeHerbi23` = null,
                `PeHerbi24` = null,
                `PeHerbi25` = null,
                `PeHerbi26` = null,
                `PeHerbi27` = null,
                `PeHerbi28` = null,
                `PeHerbi29` = null,
                `PeHerbiOther` = null,
                `PeUsingInsecticide` = null,
                `PeMoneySpentInsec` = null,
                `PeFreqInsec` = null,
                `PeDoseInsec` = null,
                `PeInsec1` = null,
                `PeInsec2` = null,
                `PeInsec3` = null,
                `PeInsec4` = null,
                `PeInsec5` = null,
                `PeInsec6` = null,
                `PeInsec7` = null,
                `PeInsec8` = null,
                `PeInsec9` = null,
                `PeInsec10` = null,
                `PeInsec11` = null,
                `PeInsec12` = null,
                `PeInsec13` = null,
                `PeInsec14` = null,
                `PeInsec15` = null,
                `PeInsec16` = null,
                `PeInsec17` = null,
                `PeInsec18` = null,
                `PeInsec19` = null,
                `PeInsec20` = null,
                `PeInsec21` = null,
                `PeInsec22` = null,
                `PeInsec23` = null,
                `PeInsecOther` = null,
                `PeUsingFungicide` = null,
                `PeMoneySpentFungi` = null,
                `PeFreqFungi` = null,
                `PeDoseFungi` = null,
                `PeFungi1` = null,
                `PeFungi2` = null,
                `PeFungi3` = null,
                `PeFungi4` = null,
                `PeFungi5` = null,
                `PeFungi6` = null,
                `PeFungi7` = null,
                `PeFungi8` = null,
                `PeFungi9` = null,
                `PeFungi10` = null,
                `PeFungi11` = null,
                `PeFungi12` = null,
                `PeFungiOther` = null,
                `PestMainRats` = null,
                `PestMainOly` = null,
                `PestMainSatora` = null,
                `PestMainTira` = null,
                `PestMainRhino` = null,
                `PestMainElep` = null,
                `PestMainOrgUtan` = null,
                `PestMainLandak` = null,
                `PestMainBabi` = null,
                `PestMainOther` = null,
                `PestMainOtherText` = null,
                `DisMainBlast` = null,
                `DisMainGeno` = null,
                `DisMainSteam` = null,
                `DisMainBud` = null,
                `DisMainSpear` = null,
                `DisMainYellow` = null,
                `DisMainAnt` = null,
                `DisMainCrown` = null,
                `DisMainViscular` = null,
                `DisMainBunch` = null,
                `DisMainOther` = null,
                `DisMainOtherText` = null,
                UseProtectiveGear = null,
                EquipHelm = null,
                EquipBoots = null,
                EquipDodosProtector = null,
                EquipMask = null,
                EquipGloves = null,
                EquipSprayGlasses = null,
                EquipEgrekProtector = null,
                EquipProtectiveClothing = null,
                PestStoreLocation = null,
                PestPackageAfterUse = null,
                OwnerOfPlantationNameText = null,
                OwnerOfPlantationLocationText = null,
                OwnerOfPlantationPhoneText = null,
                GarWitnessProveOwnership = null,
                GarNameOfWitness = null,
                GarOwnerRelationship = null,
                YearPlantingCurrent = null,
                WagsCert = null,
                TPHLocation = null,
                WagsCertStandardRSPO = null,
                WagsCertStandardMSPO = null,
                WagsPlantationStage = null,
                WagsCondEstPlantation = null
            WHERE
                `MemberID` = ?
                AND `PlotNr` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
    }

    private function GenTphID($OrgType){
        switch($OrgType){
            case 'agent':
                $Prefix = 'TPHAG-';
            break;
            case 'farmer':
                $Prefix = 'TPHFR-';
            break;
            case 'collective':
                $Prefix = 'TPHCL-';
            break;
        }

        $sql = "SELECT
                    a.`CollectpointID`
                FROM
                    ktv_collecting_point a
                ORDER BY a.`CollectpointID` DESC
                LIMIT 1";
        $data = $this->db->query($sql)->row_array();
        if(isset($data['CollectpointID'])){
            $inc = (int) $data['CollectpointID'];
            $inc++;
            return $Prefix.$inc;
        }else{
            return $Prefix.'1';
        }
    }

    public function updatePlotSurvey($paramPost,$MemberData){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $MemberID = $paramPost['MemberID'];
        $PlotNr = $paramPost['PlotNr'];
        $SurveyNr = $paramPost['SurveyNr'];
        $DateCollection = $paramPost['DateCollection'];

        //Collecting Point//
        if($paramPost['AsCollectingPoint'] == "1"){
            $this->load->model('grower/mgrower');
            $uid = $this->mgrower->getUID();

            $CollectData["CollectpointDisplayID"]   = $this->GenTphID("farmer");
            $CollectData["OrgType"]                 = "farmer";
            $CollectData["OrgID"]                   = $paramPost["MemberID"];
            $CollectData["PlantationNr"]            = $paramPost["PlotNr"];
            $CollectData["CollectpointName"]        = $paramPost["MemberName"];
            $CollectData["VillageID"]               = $paramPost["VillageID"];
            $CollectData["CollectpointAddress"]     = $paramPost["AdditionalLocation"];
            $CollectData["Longitude"]               = $paramPost["Longitude"];
            $CollectData["Latitude"]                = $paramPost["Latitude"];
            $CollectData["StatusCode"]              = 'active';
            $CollectData["DateCreated"]             = date("Y-m-d H:i:s");
            $CollectData["CreatedBy"]               = $_SESSION['userid'];
            $CollectData["uid"]                     = $uid;

            $sql2   = "SELECT CollectpointID FROM ktv_collecting_point WHERE OrgID = ? AND PlantationNr = ? AND OrgType = 'farmer'";
            $query2 = $this->db->query($sql2, array($paramPost["MemberID"], $paramPost["PlotNr"]));

            if($query2->num_rows() == 0){
                $this->db->set("LatLong", "ST_GEOMFROMTEXT('POINT($paramPost[Latitude] $paramPost[Longitude])')",false);
                $this->db->insert("ktv_collecting_point", $CollectData);
            }
        }
        //Collecting Point//

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ProvinceID']);
        unset($paramPost['DistrictID']);
        unset($paramPost['SubDistrictID']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['TypePlantMateTotalTreeNr']);
        unset($paramPost['TreeTotalTBMTMTR']);
        unset($paramPost['MemberID']);
        unset($paramPost['PlotNr']);
        unset($paramPost['SurveyNr']);
        unset($paramPost['DateCollection']);
        unset($paramPost['FertUreaDosePlotYear']);
        unset($paramPost['FertSSDosePlotYear']);
        unset($paramPost['FertNPKDosePlotYear']);
        unset($paramPost['FertTSPDosePlotYear']);
        unset($paramPost['FertCUDosePlotYear']);
        unset($paramPost['FertKCLDosePlotYear']);
        unset($paramPost['FertNPKMutiDosePlotYear']);
        unset($paramPost['FertBoratDosePlotYear']);
        unset($paramPost['FertDolomiteDosePlotYear']);
        unset($paramPost['FertPBADosePlotYear']);
        unset($paramPost['FertPBDosePlotYear']);
        unset($paramPost['FertCPBDosePlotYear']);
        unset($paramPost['FertManureDosePlotYear']);
        unset($paramPost['PeTotalUsageHerbi']);
        unset($paramPost['PeTotalUsageInsec']);
        unset($paramPost['PeTotalUsageFungi']);
        unset($paramPost['GardenAreaPolygon']);
        unset($paramPost['TreeTotalTBMTMTRPerHa']);
        unset($paramPost['AnyWaterBodiesHidden']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        unset($paramPost['PhotoOfVisitOld']);
        unset($paramPost['PhotoOfFarmOld']);
        unset($paramPost['PhotoofFireOld']);
        unset($paramPost['PhotoSoilErotionOld']);
        unset($paramPost['PhotoSoilAccumulationOld']);
        unset($paramPost['PhotoOwnershipDocOld']);
        unset($paramPost['DeliveryByOld']);
        unset($paramPost['DocumentWrittenOld']);
        
        if($paramPost['SignatureOld'] != ""){
            $arrTemp = explode("/", $paramPost['SignatureOld']);
            $SignaturePhoto = array_values(array_slice($arrTemp, -1))[0];
            $paramPost['Signature'] = $SignaturePhoto;
        }else{
            unset($paramPost['SignatureOld']);
        }
        unset($paramPost['SignatureOld']);

        //reset semuanya dulu
        $this->resetAllFieldPlot($MemberID,$PlotNr,$SurveyNr,$DateCollection);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('PlotNr', $PlotNr);
        $this->db->where('SurveyNr', $SurveyNr);
        $this->db->where('DateCollection', $DateCollection);
        $query = $this->db->update('ktv_survey_plot', $paramPost);

        if($paramPost['Certification'] != "" && $paramPost['Longitude'] != "") {
            $sql = "INSERT INTO ktv_certification (
				FarmerID,
				GardenNr,
				SurveyNr,
				Certification,
				CreatedBy,
				DateCreated
			)
			VALUES (
				?,?,?,?,?,NOW()
			)
			ON DUPLICATE KEY UPDATE
				LastModifiedBy = ?,
				DateUpdated = NOW()";
			$p = array(
				$MemberID,
				$PlotNr,
				$SurveyNr,
				$paramPost['Certification'],
				$_SESSION['userid'],
				$_SESSION['userid']
			);
			$query = $this->db->query($sql,$p);
        }
        
        if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {

            $LatitudeProses = (float) $paramPost['Latitude'];
            $LongitudeProses = (float) $paramPost['Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                //Cek valid tidak koordinatnya
                $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql2)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
                    $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                    $sql2 = "UPDATE ktv_survey_plot a SET
                                a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                            WHERE
                                a.`MemberID` = ?
                                AND a.`PlotNr` = ?
                                AND a.`SurveyNr` = ?
                            LIMIT 1";
                    $p = array(
                        $MemberID,
                        $PlotNr,
                        $SurveyNr
                    );
                    $query = $this->db->query($sql2,$p);

                    if($query){
                        $sql2 = "UPDATE ktv_survey_plot_status a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`MemberID` = ?
                                AND a.`PlotNr` = ?
                                LIMIT 1";
                        $p = array(
                            $MemberID,
                            $PlotNr
                        );
                        $query = $this->db->query($sql2,$p);
                    }
                }

            }
        }

        //Plot Status ==================================================== (End)
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`AnnualProduction`
                    FROM
                        `ktv_survey_plot` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction";
        $query = $this->db->query($sql, array($MemberID,$PlotNr));
        
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                    FROM
                        `ktv_survey_plot` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude";
        $query = $this->db->query($sql, array($MemberID,$PlotNr));
        //Plot Status ==================================================== (End)

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }

        return $results;
    }

    public function GetICSLogFormData($FarmerID,$GardenNr,$SurveyNr,$Certification,$ICSDate){
    	$sql="SELECT
			    a.`FarmerID`,
			    a.`GardenNr`,
			    a.`SurveyNr`,
			    a.`Certification`,
			    a.`Certification` AS CertificationProgram,
			    a.`ICSDate`,
			    a.`StatusAudit`,
			    a.`MasukHutanLindung`,
			    a.`DateRevisionAudit`,
			    a.`CommentAudit`,
			    a.`RecommendationAudit`,
			    a.`InspectorID`,
			    a.`InspectorName`,
			    a.`InspectorSignature`,
			    a.`AuditCommiteeID`,
			    a.`AuditCommiteeName`,
			    a.`AuditCommiteeSignature`,
			    a.`IMSManagerID`,
			    a.`IMSManagerName`,
			    a.`IMSManagerSignature`,
			    a.`FarmerSignature`
			FROM
			    `ktv_certification_audit_log` a
			WHERE
				a.`FarmerID` = ?
				AND a.`GardenNr` = ?
				AND a.`SurveyNr` = ?
				AND a.`Certification` = ?
				AND a.`ICSDate` = ?
			LIMIT 1";
    	$p = array(
            $FarmerID,
            $GardenNr,
            $SurveyNr,
            $Certification,
            $ICSDate
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormICSLog-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        //Buat dipakai langsung di JS
        //$dataRow['CertificationProgram'] = $data['CertificationProgram'];

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function deletePlotSurvey($MemberID,$PlotNr,$SurveyNr,$DateCollection){
        $this->db->trans_start();

        $sql="INSERT INTO ktv_survey_plot_nullified
            SELECT
                *
            FROM
                ktv_survey_plot a
            WHERE
                a.MemberID = ?
                AND a.PlotNr = ?
                AND a.SurveyNr = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $sql="DELETE FROM ktv_survey_plot WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ? LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }

        return $results;
    }

    public function getPlotPolygonMap($MemberID,$PlotNr,$SurveyNr,$DateCollection,$CallFrom){
        if($CallFrom == 'SME') {
            $id = "MemberID";
            $QueryTable = "ktv_survey_plot_polygon_sme";
            $QueryTable2 = "ktv_survey_plot_polygon_sme_geo";
        } elseif ($CallFrom == 'Mill') {
            $id = "MillID";
            $QueryTable = " ktv_survey_plot_polygon_mill ";
            $QueryTable2 = " ktv_survey_plot_polygon_mill_geo ";
        } else {
            $id = "MemberID";
            $QueryTable = "ktv_survey_plot_polygon";
            $QueryTable2 = "ktv_survey_plot_polygon_geo";
        }

        $sql = "SELECT
                ST_ASGEOJSON(a.Polygon) polygon
            FROM
                $QueryTable2 a
            WHERE
                a.$id = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`Revision` = (
                SELECT
                    sub.Revision
                FROM
                    $QueryTable2 sub
                WHERE
                    sub.`$id` = ?
                    AND sub.`PlotNr` = ?
                    AND sub.`SurveyNr` = ?
                    ORDER BY sub.`Revision` DESC
                    LIMIT 1
                )
            ORDER BY a.`Revision` ASC
        ";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr,
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $result = $query->row();
        $data   = json_decode($result->polygon);
        $polygon = $data->coordinates;

        if($query->num_rows()>0){
            return json_encode($polygon[0]);
        }

        return array();
    }

    public function getPlotPolygonCenterCoor($MemberID,$PlotNr,$SurveyNr,$DateCollection){
        $sql="SELECT
                a.`latitude`
                , a.`longitude`
            FROM
                ktv_survey_plot_polygon a
            WHERE
                a.`MemberID` = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`StatusCheck` = 'verified'
                AND a.`Revision` = (
                    SELECT
                        sub.Revision
                    FROM
                        ktv_survey_plot_polygon sub
                    WHERE
                        sub.`MemberID` = ?
                        AND sub.`PlotNr` = ?
                        AND sub.`SurveyNr` = ?
                        AND sub.`StatusCheck` = 'verified'
                    ORDER BY sub.`Revision` DESC
                    LIMIT 1
                )
            ORDER BY a.`Revision` ASC, a.`OrderNr` ASC";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr,
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);
        $dataPoly = $query->result_array();

        $polygon = new stdClass();
        $polygon->rings = array();
        foreach ($dataPoly as $key => $value) {
            $polygon->rings[0][$key] = array($value['latitude'],$value['longitude']);
        }

        return $this->getCentroidOfPolygon($polygon);
    }

    public function getPlotPolygonCenterCoorOnlyFirst($MemberID,$PlotNr,$SurveyNr,$DateCollection,$CallFrom){
        if($CallFrom == 'SME') {
            $id = "MemberID";
            $QueryTable = "ktv_survey_plot_polygon_sme";
        } elseif ($CallFrom == 'Mill') {
            $id = "MillID";
            $QueryTable = " ktv_survey_plot_polygon_mill ";
        } else {
            $id = "MemberID";
            $QueryTable = "ktv_survey_plot_polygon";
        }

        $sql="SELECT
                a.`latitude`
                , a.`longitude`
            FROM
                $QueryTable a
            WHERE
                a.$id = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`Revision` = (
                    SELECT
                        sub.Revision
                    FROM
                        $QueryTable sub
                    WHERE
                        sub.$id = ?
                        AND sub.`PlotNr` = ?
                        AND sub.`SurveyNr` = ?
                    ORDER BY sub.`Revision` DESC
                    LIMIT 1
                )
            ORDER BY a.`Revision` ASC, a.`OrderNr` ASC
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr,
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);
        $dataPoly = $query->row_array();
        return array($dataPoly['latitude'],$dataPoly['longitude']);
    }

    public function getCentroidOfPolygon($geometry) {
        $cx = 0;
        $cy = 0;

        for ($ri=0, $rl=sizeof($geometry->rings); $ri<$rl; $ri++) {
            $ring = $geometry->rings[$ri];

            for ($vi=0, $vl=sizeof($ring); $vi<$vl; $vi++) {
                $thisx = $ring[ $vi ][0];
                $thisy = $ring[ $vi ][1];
                $nextx = $ring[ ($vi+1) % $vl ][0];
                $nexty = $ring[ ($vi+1) % $vl ][1];

                $p = ($thisx * $nexty) - ($thisy * $nextx);
                $cx += ($thisx + $nextx) * $p;
                $cy += ($thisy + $nexty) * $p;
            }
        }

        // last step of centroid: divide by 6*A
        $area = $this->getAreaOfPolygon($geometry);
        $cx = -$cx / ( 6 * $area);
        $cy = -$cy / ( 6 * $area);

        // done!
        return array($cx,$cy);
    }

    public function getAreaOfPolygon($geometry) {
        $area = 0;
        for ($ri=0, $rl=sizeof($geometry->rings); $ri<$rl; $ri++) {
            $ring = $geometry->rings[$ri];

            for ($vi=0, $vl=sizeof($ring); $vi<$vl; $vi++) {
                $thisx = $ring[ $vi ][0];
                $thisy = $ring[ $vi ][1];
                $nextx = $ring[ ($vi+1) % $vl ][0];
                $nexty = $ring[ ($vi+1) % $vl ][1];
                $area += ($thisx * $nexty) - ($thisy * $nextx);
            }
        }

        // done with the rings: "sign" the area and return it
        $area = abs(($area / 2));
        return $area;
    }

    public function GetGridPlotStatus($MemberID,$CallFrom){
        if ($CallFrom == 'SME') {
            $id = "a.`MemberID`";
            $QueryTable = " ktv_survey_plot_status_sme ";
        } elseif ($CallFrom == 'Mill') {
            $id = "a.`MillID`";
            $QueryTable = " ktv_survey_plot_status_mill ";
        } else {
            $id = "a.`MemberID`";
            $QueryTable = " ktv_survey_plot_status ";
        }

        $sql = "SELECT
                    $id MemberID
                    , a.`PlotNr`
                    , a.`GardenAreaHa`
                    , a.`AnnualProduction`
                    , a.`ActiveStatus`
                FROM
                    $QueryTable a
                WHERE
                    $id = ?
                ORDER BY a.`PlotNr` ASC";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['PlotNr'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getExportPlotStatusMill($MemberID,$CallFrom){
        if($CallFrom == "Mill"){
            $sql = "SELECT
                        a.MillID MemberID
                        , b.MillDisplayID
                        , b.MillName
                        , a.PlotNr PlantationNr
                        , prov.Province
                        , dis.District
                        , a.GardenAreaHa
                        , a.GardenAreaPolygon
                        , a.AnnualProduction
                        , a.PlantedAreaHa
                        , a.TreeTBM
                        , a.TreeTM
                        , a.TreeTR
                        , a.TreeTBM + a.TreeTR + a.TreeTM TotalTree
                        , (a.TreeTBM + a.TreeTR + a.TreeTM)/a.GardenAreaHa TotalTreeHa
                        , a.Comment
                        , a.Latitude
                        , a.Longitude
                        , CASE
                            WHEN a.ActiveStatus = 1 THEN 'Active'
                            ELSE 'Not Active'
                        END PlantationStatus
                    FROM
                        ktv_survey_plot_status_mill a
                    LEFT JOIN
                        ktv_mill b on b.MillID = a.MillID
                    LEFT JOIN
                        ktv_village vil on vil.VillageID = b.VillageID
                    LEFT JOIN
                        ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
                    LEFT JOIN
                        ktv_district dis on dis.DistrictID = subd.DistrictID
                    LEFT JOIN
                        ktv_province prov on prov.ProvinceID = dis.ProvinceID
                    WHERE
                        a.MillID = ?
                    ORDER BY a.`PlotNr` ASC";
        }

        if($CallFrom == "SME"){
            $sql = "
                SELECT
                    a.MemberID
                    , b.MemberDisplayID
                    , b. MemberName
                    , a.PlotNr PlantationNr
                    , prov.Province
                    , dis.District
                    , a.GardenAreaHa
                    , a.GardenAreaPolygon
                    , a.AnnualProduction
                    , a.Latitude
                    , a.Longitude
                FROM
                    ktv_survey_plot_status_sme a
                LEFT JOIN
                    ktv_members b on b.MemberID = a.MemberID
                LEFT JOIN
                    ktv_village vil on vil.VillageID = b.VillageID
                LEFT JOIN
                    ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN
                    ktv_district dis on dis.DistrictID = subd.DistrictID
                LEFT JOIN
                    ktv_province prov on prov.ProvinceID = dis.ProvinceID
                WHERE
                    a.MemberID = ?
                ORDER BY a.`PlotNr` ASC
            ";
        }
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['PlantationNr'] == "") $data = array();

        $return['data'] = $data;
        return $data;
    }

    public function GetPlantationStatusFormData($MemberID,$PlotNr,$CallFrom){
        $this->load->library('awsfileupload');
        if($CallFrom == 'SME') {
            $id = "a.`MemberID`";
            $JoinField = "b.`MemberID`";
            $display = "b.`MemberDisplayID`";
            $name = "b.`MemberName`";
            $QueryTable = " ktv_survey_plot_status_sme ";
            $JoinTable = "ktv_members";
            $FieldMill = "";
        } elseif ($CallFrom == 'Mill') {
            $id = "a.`MillID`";
            $JoinField = "b.`MillID`";
            $display = "b.`MillDisplayID`";
            $name = "b.`MillName`";
            $QueryTable = " ktv_survey_plot_status_mill ";
            $JoinTable = "ktv_mill";
            $FieldMill = "a.PlantedAreaHa,
                    a.TreeTBM,
                    a.TreeTM,
                    a.TreeTR,
                    a.FarmPhoto,
                    a.FarmPhotoDesc,
                    a.Comment,
                    b.VillageID,";
        } else {
            $id = "a.`MemberID`";
            $JoinField = "b.`MemberID`";
            $display = "b.`MemberDisplayID`";
            $name = "b.`MemberName`";
            $QueryTable = " ktv_survey_plot_status ";
            $JoinTable = "ktv_members";
            $FieldMill = "";
        }

        $sql = "SELECT
                    $id MemberID,
                    $display MemberDisplayID,
                    $name MemberName,
                    a.`PlotNr`,
                    a.`ActiveStatus`,
                    a.`NotActiveReason`,
                    a.`GardenAreaHa`,
                    IFNULL(a.GardenAreaPolygon, a.GardenAreaHa) GardenAreaPolygon,
                    a.`AnnualProduction`,
                    %s
                    IFNULL(ST_Y(a.`LatLong`), a.`Longitude`) AS Latitude,
                    IFNULL(ST_X(a.`LatLong`), a.`Latitude`) AS Longitude
                FROM
                    $QueryTable a
                    INNER JOIN $JoinTable b ON $JoinField = $id
                WHERE
                    $id = ?
                    AND a.`PlotNr` = ?
                LIMIT 1";
        $p = array(
            $MemberID,$PlotNr
        );
        $query = $this->db->query(sprintf($sql, $FieldMill), $p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-".$key;
            $dataRow[$keyNew] = $value;
        }
        if ($CallFrom == 'Mill') {
            $dataRow['FarmPhoto'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhoto'];
            $dataRow['ProvinceID'] = substr($dataRow['Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-VillageID'], 0, 2);
            $dataRow['MemberDisplayID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberDisplayID'];
        }

        if($this->awsfileupload->doesObjectExist($dataRow['FarmPhoto']) == true) {
            $dataRow['FarmPhotoPath'] = $dataRow['FarmPhoto'];
            $dataRow['FarmPhoto'] = $this->config->item('CTCDN')."/".$dataRow['FarmPhoto'];
        }else{
            $dataRow['FarmPhotoPath'] = 'images/mill/'.$dataRow["ProvinceID"].'/'.$dataRow["MemberDisplayID"].'/'.$dataRow['FarmPhoto'];
            $dataRow['FarmPhoto'] = base_url().'images/mill/'.$dataRow["ProvinceID"].'/'.$dataRow["MemberDisplayID"].'/'.$dataRow['FarmPhoto'];
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }
    
    public function GetMillDetail($MillID) {
        $sql = "SELECT
                    m.`MillID` MemberID,
                    `MillDisplayID` MemberDisplayID,
                    `MillName` MemberName,
                    count(s.PlotNr)+1 PlotNr
                FROM ktv_mill m
                LEFT JOIN ktv_survey_plot_status_mill s ON m.MillID=s.MillID 
                WHERE
                    m.MillID = ?
                LIMIT 1";
        $p = array($MillID);
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-".$key;
            $dataRow[$keyNew] = $value;
        }
        
        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;        
    }

    public function InsertPlantationStatus($ParamPost, $CallFrom){
        if ($CallFrom == 'SME') {
            $id = "MemberID";
            $QueryTable = " ktv_survey_plot_status_sme ";
            $FieldMill = "";
        } elseif ($CallFrom == 'Mill') {
            $id = "MillID";
            $QueryTable = " ktv_survey_plot_status_mill ";
            $FieldMill = "
                    PlantedAreaHa = ?,
                    TreeTBM = ?,
                    TreeTM = ?,
                    TreeTR = ?,
                    FarmPhotoDesc = ?,
                    Comment = ?,";
        } else {
            $id = "MemberID";
            $QueryTable = " ktv_survey_plot_status ";
            $FieldMill = "";
        }
        $sql = "INSERT INTO $QueryTable SET
                    $id = ?,
                    `PlotNr` = ?,
                    `ActiveStatus` = ?,
                    `NotActiveReason` = ?,
                    `GardenAreaHa` = ?,
                    `GardenAreaPolygon` = ?,
                    `AnnualProduction` = ?,
                    `Latitude` = ?,
                    `Longitude` = ?,
                    %s
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
        if ($CallFrom == 'Mill') {
            $p = array(
                $ParamPost['MemberID'],
                $ParamPost['PlotNr'],
                $ParamPost['ActiveStatus'],
                $ParamPost['NotActiveReason'],
                $ParamPost['GardenAreaHa'],
                $ParamPost['GardenAreaPolygon'],
                $ParamPost['AnnualProduction'],
                $ParamPost['Latitude'],
                $ParamPost['Longitude'],
                $ParamPost['PlantedAreaHa'],
                $ParamPost['TreeTBM'],
                $ParamPost['TreeTM'],
                $ParamPost['TreeTR'],
                $ParamPost['FarmPhotoDesc'],
                $ParamPost['Comment'],
                $_SESSION['userid']
            );
        } else {
            $p = array(
                $ParamPost['MemberID'],
                $ParamPost['PlotNr'],
                $ParamPost['ActiveStatus'],
                $ParamPost['NotActiveReason'],
                $ParamPost['GardenAreaHa'],
                $ParamPost['GardenAreaPolygon'],
                $ParamPost['AnnualProduction'],
                $ParamPost['Latitude'],
                $ParamPost['Longitude'],
                $_SESSION['userid']
            );
        }
        $query = $this->db->query(sprintf($sql, $FieldMill), $p);

        if ($query == true) {
            $results['success'] = true;
            $results['message'] = "Data saved";
            
            if ($CallFrom == 'Mill') {
                $FarmPhoto = $ParamPost['FarmPhotoOld'];
                if ($FarmPhoto != "") {
                    //get ext nya..
                    $file = explode("images/farm_photo_mill/temp/",$FarmPhoto);
                    // //Insert ada photonya pakai aws
                    if(file_exists('images/farm_photo_mill/temp/'.$file[1])) {
                        $this->load->library('awsfileupload');
                        $upload = $this->awsfileupload->upload('images/farm_photo_mill/temp/'.$file[1],$file[1],AWSS3_MILL_PLOT_PATH, 'images');
                        if ($upload['success'] == true) {
                            delete_file("/".$FarmPhoto);
                            $namaFileGambar = $upload['filenamepath'];
                        }
                    }

                    $sql = "UPDATE ktv_survey_plot_status_mill a SET
                        a.`FarmPhoto` = ?
                    WHERE
                        a.`MillID` = ?
                        AND a.`PlotNr` = ?
                    LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $ParamPost['MemberID'],
                        $ParamPost['PlotNr']
                    );
                    $query = $this->db->query($sql, $p);

                    //Koordinat Geometry ============= (Begin)
                    if($ParamPost['Latitude'] != "" && $ParamPost['Longitude'] != "") {

                        $LatitudeProses = (float) $ParamPost['Latitude'];
                        $LongitudeProses = (float) $ParamPost['Longitude'];
                        
                        //Check Latitude
                        if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                            //Cek valid tidak koordinatnya
                            $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                            $DataCekKoordinat = $this->db->query($sql2)->row_array();
                            
                            if ($DataCekKoordinat['HasilCek'] == "1") {
                                $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                                $sql2 = "UPDATE ktv_survey_plot_status a SET
                                            a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                        WHERE
                                            a.`MemberID` = ?
                                        AND a.`PlotNr` = ?
                                        LIMIT 1";
                                $p = array(
                                    $ParamPost["MemberID"],
                                    $ParamPost["PlotNr"]
                                );
                                $query = $this->db->query($sql2,$p);
                            }

                        }
                    }
                }
            }
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function UpdateImageMill($MillID,$PlotNr,$file){
        $sql = "UPDATE ktv_survey_plot_status_mill a SET
            a.`FarmPhoto` = ?
        WHERE
            a.`MillID` = ?
            AND a.`PlotNr` = ?
        LIMIT 1";
        $p = array(
            $file,
            $MillID,
            $PlotNr
        );
        $query = $this->db->query($sql, $p);
    }

    public function UpdatePlantationStatus($ParamPost, $CallFrom){
        if ($CallFrom == 'SME') {
            $id = "MemberID";
            $QueryTable = " ktv_survey_plot_status_sme ";
            $FieldMill = "";
        } elseif ($CallFrom == 'Mill') {
            $id = "MillID";
            $QueryTable = " ktv_survey_plot_status_mill ";
            $FieldMill = "
                    PlantedAreaHa = ?,
                    TreeTBM = ?,
                    TreeTM = ?,
                    TreeTR = ?,
                    FarmPhotoDesc = ?,
                    Comment = ?,";
        } else {
            $id = "MemberID";
            $QueryTable = " ktv_survey_plot_status ";
            $FieldMill = "";
        }

        $sql = "UPDATE $QueryTable a SET
                    a.`ActiveStatus` = ?,
                    a.`NotActiveReason` = ?,
                    a.`GardenAreaHa` = ?,
                    a.`GardenAreaPolygon` = ?,
                    a.`AnnualProduction` = ?,
                    a.`Latitude` = ?,
                    a.`Longitude` = ?,
                    %s
                    a.DateUpdated = NOW(),
                    a.LastModifiedBy = ?
                WHERE
                    $id = ?
                    AND a.`PlotNr` = ?
                LIMIT 1";
        if ($CallFrom == 'Mill') {
            $p = array(
                $ParamPost['ActiveStatus'],
                $ParamPost['NotActiveReason'],
                $ParamPost['GardenAreaHa'],
                $ParamPost['GardenAreaPolygon'],
                $ParamPost['AnnualProduction'],
                $ParamPost['Latitude'],
                $ParamPost['Longitude'],
                $ParamPost['PlantedAreaHa'],
                $ParamPost['TreeTBM'],
                $ParamPost['TreeTM'],
                $ParamPost['TreeTR'],
                $ParamPost['FarmPhotoDesc'],
                $ParamPost['Comment'],
                $_SESSION['userid'],
                $ParamPost['MemberID'],
                $ParamPost['PlotNr']
            );
        } else {
            $p = array(
                $ParamPost['ActiveStatus'],
                $ParamPost['NotActiveReason'],
                $ParamPost['GardenAreaHa'],
                $ParamPost['GardenAreaPolygon'],
                $ParamPost['AnnualProduction'],
                $ParamPost['Latitude'],
                $ParamPost['Longitude'],
                $_SESSION['userid'],
                $ParamPost['MemberID'],
                $ParamPost['PlotNr']
            );
        }
        $query = $this->db->query(sprintf($sql, $FieldMill), $p);

        //Koordinat Geometry ============= (Begin)
        if($ParamPost['Latitude'] != "" && $ParamPost['Longitude'] != "") {

            $LatitudeProses = (float) $ParamPost['Latitude'];
            $LongitudeProses = (float) $ParamPost['Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                //Cek valid tidak koordinatnya
                $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql2)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
                    $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                    $sql2 = "UPDATE ktv_survey_plot_status a SET
                                a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                            WHERE
                                a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            LIMIT 1";
                    $p = array(
                        $ParamPost["MemberID"],
                        $ParamPost["PlotNr"]
                    );
                    $query = $this->db->query($sql2,$p);
                }

            }
        }

        if($query == true){
            $results['success'] = true;
            $results['message'] = "Data saved";
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function getGridSmePlotSurveySummary($MemberID) {
        $sql="SELECT
                a.`PlotNr`
                , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
                , a.`SurveyNr`
                , a.`DateCollection`
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM
                ktv_survey_plot_sme a
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.`MemberID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.`PlotNr`, a.`SurveyNr`";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['PlotNr'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function checkIfSurveyExistSme($paramPost) {
        $sql="SELECT
                a.`MemberID`
            FROM
                ktv_survey_plot_sme a
            WHERE
                a.MemberID = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`StatusCode` = 'active'
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['PlotNr'],
            $paramPost['SurveyNr']
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        if($data['MemberID'] != ""){
            return true;
        }else{
            return false;
        }
    }

    public function insertSmePlotSurvey($paramPost, $MemberData) {
        //echo '<pre>'; print_r($paramPost); exit;
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $PhotoOfVisit = $paramPost['PhotoOfVisitOld'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ProvinceID']);
        unset($paramPost['DistrictID']);
        unset($paramPost['SubDistrictID']);
        unset($paramPost['PhotoOfVisitOld']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['TypePlantMateTotalTreeNr']);
        unset($paramPost['TreeTotalTBMTMTR']);
        unset($paramPost['FertUreaDosePlotYear']);
        unset($paramPost['FertSSDosePlotYear']);
        unset($paramPost['FertNPKDosePlotYear']);
        unset($paramPost['FertTSPDosePlotYear']);
        unset($paramPost['FertCUDosePlotYear']);
        unset($paramPost['FertKCLDosePlotYear']);
        unset($paramPost['FertNPKMutiDosePlotYear']);
        unset($paramPost['FertBoratDosePlotYear']);
        unset($paramPost['FertDolomiteDosePlotYear']);
        unset($paramPost['FertPBADosePlotYear']);
        unset($paramPost['FertPBDosePlotYear']);
        unset($paramPost['FertCPBDosePlotYear']);
        unset($paramPost['FertManureDosePlotYear']);
        unset($paramPost['PeTotalUsageHerbi']);
        unset($paramPost['PeTotalUsageInsec']);
        unset($paramPost['PeTotalUsageFungi']);
        unset($paramPost['GardenAreaPolygon']);
        unset($paramPost['TreeTotalTBMTMTRPerHa']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;

        //insert
        $this->db->insert('ktv_survey_plot_sme', $paramPost);

        //Koordinat Geometry ============= (Begin)
        if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {

            $LatitudeProses = (float) $paramPost['Latitude'];
            $LongitudeProses = (float) $paramPost['Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                //Cek valid tidak koordinatnya
                $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql2)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
                    $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                    $sql2 = "UPDATE ktv_survey_plot_sme a SET
                                a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                            WHERE
                                a.`MemberID` = ?
                            AND a.PlotNr = ?
                            AND a.SurveyNr = ?
                            LIMIT 1";
                    $p = array(
                        $paramPost["MemberID"],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr']
                    );
                    $query = $this->db->query($sql2,$p);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";

            //insert ke plot status
            $sql = "INSERT INTO `ktv_survey_plot_status_sme` SET
                    `MemberID` = ?,
                    `PlotNr` = ?,
                    `ActiveStatus` = '1',
                    `GardenAreaHa` = ?,
                    `AnnualProduction` = ?,
                    `Latitude` = ?,
                    `Longitude` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $paramPost['MemberID'],
                $paramPost['PlotNr'],
                $paramPost['GardenAreaHa'],
                $paramPost['AnnualProduction'],
                $paramPost['Latitude'],
                $paramPost['Longitude'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);            

            //Koordinat Geometry ============= (Begin)
            if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {

                $LatitudeProses = (float) $paramPost['Latitude'];
                $LongitudeProses = (float) $paramPost['Longitude'];
                
                //Check Latitude
                if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                    //Cek valid tidak koordinatnya
                    $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                    $DataCekKoordinat = $this->db->query($sql2)->row_array();
                    
                    if ($DataCekKoordinat['HasilCek'] == "1") {
                        $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                        $sql2 = "UPDATE ktv_survey_plot_status_sme a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`MemberID` = ?
                                AND a.PlotNr = ?
                                LIMIT 1";
                        $p = array(
                            $paramPost["MemberID"],
                            $paramPost['PlotNr']
                        );
                        $query = $this->db->query($sql2,$p);
                    }
                }
            }

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($PhotoOfVisit != ""){
                $this->load->library('awsfileupload');
                $file = explode("/",$PhotoOfVisit);
                $upload = $this->awsfileupload->upload($PhotoOfVisit,$file[3],AWSS3_SME_PLOT_PATH, 'images');
                if ($upload['success'] == true) {
                    delete_file($PhotoOfVisit);
                    $namaFileGambar = $upload['filenamepath'];
                    $sql="UPDATE ktv_survey_plot_sme a SET
                            a.`PhotoOfVisit` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }

        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateSmePlotSurvey($paramPost,$MemberData) {
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $MemberID = $paramPost['MemberID'];
        $PlotNr = $paramPost['PlotNr'];
        $SurveyNr = $paramPost['SurveyNr'];
        $DateCollection = $paramPost['DateCollection'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ProvinceID']);
        unset($paramPost['DistrictID']);
        unset($paramPost['SubDistrictID']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['TypePlantMateTotalTreeNr']);
        unset($paramPost['TreeTotalTBMTMTR']);
        unset($paramPost['MemberID']);
        unset($paramPost['PlotNr']);
        unset($paramPost['SurveyNr']);
        unset($paramPost['DateCollection']);
        unset($paramPost['FertUreaDosePlotYear']);
        unset($paramPost['FertSSDosePlotYear']);
        unset($paramPost['FertNPKDosePlotYear']);
        unset($paramPost['FertTSPDosePlotYear']);
        unset($paramPost['FertCUDosePlotYear']);
        unset($paramPost['FertKCLDosePlotYear']);
        unset($paramPost['FertNPKMutiDosePlotYear']);
        unset($paramPost['FertBoratDosePlotYear']);
        unset($paramPost['FertDolomiteDosePlotYear']);
        unset($paramPost['FertPBADosePlotYear']);
        unset($paramPost['FertPBDosePlotYear']);
        unset($paramPost['FertCPBDosePlotYear']);
        unset($paramPost['FertManureDosePlotYear']);
        unset($paramPost['PeTotalUsageHerbi']);
        unset($paramPost['PeTotalUsageInsec']);
        unset($paramPost['PeTotalUsageFungi']);
        unset($paramPost['GardenAreaPolygon']);
        unset($paramPost['TreeTotalTBMTMTRPerHa']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        //photo
        unset($paramPost['PhotoOfVisitOld']);

        //reset semuanya dulu
        $this->resetAllFieldPlotSme($MemberID,$PlotNr,$SurveyNr,$DateCollection);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('PlotNr', $PlotNr);
        $this->db->where('SurveyNr', $SurveyNr);
        $this->db->where('DateCollection', $DateCollection);
        $query = $this->db->update('ktv_survey_plot_sme', $paramPost);        

        //Koordinat Geometry ============= (Begin)
        if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {

            $LatitudeProses = (float) $paramPost['Latitude'];
            $LongitudeProses = (float) $paramPost['Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                //Cek valid tidak koordinatnya
                $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql2)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
                    $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                    $sql2 = "UPDATE ktv_survey_plot_sme a SET
                                a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                            WHERE
                                a.`MemberID` = ?
                            AND a.PlotNr = ?
                            AND a.SurveyNr = ?
                            LIMIT 1";
                    $p = array(
                        $MemberID,
                        $PlotNr,
                        $SurveyNr
                    );
                    $query = $this->db->query($sql2,$p);
                }
            }
        }

        //Untuk update Plot Status
        $sql = "UPDATE ktv_survey_plot_status_sme tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                        , sgar.`LatLong`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude
                    , tup.`LatLong` = gar_lat.LatLong";
        $query = $this->db->query($sql,array($MemberID,$PlotNr));

        $sql = "UPDATE ktv_survey_plot_status_sme tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`AnnualProduction`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction";
        $query = $this->db->query($sql,array($MemberID,$PlotNr));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }

        return $results;
    }

    private function resetAllFieldPlotSme($MemberID,$PlotNr,$SurveyNr,$DateCollection) {
        $sql="UPDATE `ktv_survey_plot_sme` SET
                `VillageID` = null,
                `PhotoOfVisitDesc` = null,
                `GardenAreaHa` = null,
                `GardenLength` = null,
                `GardenWidth` = null,
                `Latitude` = null,
                `Longitude` = null,
                `OwnershipDoc` = null,
                `OwnershipDocText` = null,
                `OwnerDocIsOwner` = null,
                `HaveSTDB` = null,
                `HaveSPPL` = null,
                `BusinessModel` = null,
                `LandOwnership` = null,
                LandOwnershipType = null,
                OwnerOfTheGarden = null,
                `HowObPlantation` = null,
                `HowObPlantationText` = null,
                `PlantationConditionEst` = null,
                `AverageAgeTree` = null,
                `SoilType` = null,
                `TopographyType` = null,
                `FirstPlantingYear` = null,
                TreeTBM = null,
                TreeTM = null,
                TreeTR = null,
                `TypePlantMateMarihat` = null,
                `TypePlantMateMarihatNr` = null,
                `TypePlantMateDumpy` = null,
                `TypePlantMateDumpyNr` = null,
                `TypePlantMateLonsum` = null,
                `TypePlantMateLonsumNr` = null,
                `TypePlantMateSimalungun` = null,
                `TypePlantMateSimalungunNr` = null,
                `TypePlantMateDanimas` = null,
                `TypePlantMateDanimasNr` = null,
                `TypePlantMateSriwijaya` = null,
                `TypePlantMateSriwijayaNr` = null,
                `TypePlantMateSocfin` = null,
                `TypePlantMateSocfinNr` = null,
                `TypePlantMateOther` = null,
                `TypePlantMateOtherText` = null,
                `TypePlantMateOtherNr` = null,
                `TypePlantMateDoNotKnow` = null,
                TypePlantMateDoNotKnowNr = null,
                `OwnerCultivateFarm` = null,
                `FarmEmployHiredLabor` = null,
                `FarmEmployFamMem` = null,
                `FarmEmployLaborFamMem` = null,
                `FarmEmployNoLabor` = null,
                `HowManyWorkFarm` = null,
                `UnderAgeWorker` = null,
                `AveHoursPerDay` = null,
                `AveDaysPerMonth` = null,
                `WageNominalPerDayLabor` = null,
                `WageNominalPerDayLaborPeriod` = null,
                `WageNominalPerDayFamMember` = null,
                `WageNominalPerDayFamMemberPeriod` = null,
                `HarvestRateDaysHighSeason` = null,
                `HarvestRateDaysLowSeason` = null,
                `AverageProdHighSeason` = null,
                `AverageProdLowSeason` = null,
                NrHighSeasonMonths = null,
                NrLowSeasonMonths = null,
                HighSeasonProduction = null,
                LowSeasonProduction = null,
                AnnualProduction = null,
                PlantationProductivity = null,
                `LeanHarvestSeasonJan` = null,
                `LeanHarvestSeasonFeb` = null,
                `LeanHarvestSeasonMar` = null,
                `LeanHarvestSeasonApr` = null,
                `LeanHarvestSeasonMay` = null,
                `LeanHarvestSeasonJun` = null,
                `LeanHarvestSeasonJul` = null,
                `LeanHarvestSeasonAug` = null,
                `LeanHarvestSeasonSep` = null,
                `LeanHarvestSeasonOct` = null,
                `LeanHarvestSeasonNov` = null,
                `LeanHarvestSeasonDec` = null,
                `WhoHarvestFamily` = null,
                `WhoHarvestLabor` = null,
                `HowManyDiffBuyerSoldLastYear` = null,
                `HowManyDiffBuyerSoldLastYearText` = null,
                `ToWhoSellFFBLastYear` = null,
                `HowManyDiffMillSoldLastYear` = null,
                `HowManyDiffMillSoldLastYearText` = null,
                `ToWhichMillSellFFBLastYear` = null,
                `ToWhichMillSellFFBLastYearText` = null,
                `UseEFBFertilizer` = null,
                `FertilizerDesc` = null,
                `FertilizerNotes` = null,
                `useParaquat` = null,
                `PesticideDesc` = null,
                `PesticideNotes` = null,
                `Comment` = null,
                `FertNonOrganicData` = null,
                `FertMoneySpentNonOrganic` = null,
                `FertUreaTimesYear` = null,
                `FertUreaDose` = null,
                `FertSSTimesYear` = null,
                `FertSSDose` = null,
                `FertNPKTimesYear` = null,
                `FertNPKDose` = null,
                `FertTSPTimesYear` = null,
                `FertTSPDose` = null,
                `FertCUTimesYear` = null,
                `FertCUDose` = null,
                `FertKCLTimesYear` = null,
                `FertKCLDose` = null,
                `FertNPKMutiTimesYear` = null,
                `FertNPKMutiDose` = null,
                `FertBoratTimesYear` = null,
                `FertBoratDose` = null,
                `FertDolomiteTimesYear` = null,
                `FertDolomiteDose` = null,
                `FertWithNonOrgaTBM` = null,
                `FertWithNonOrgaTM` = null,
                `FertWithNonOrgaTR` = null,
                `FertUseOrganic` = null,
                `FertMoneySpentOrganic` = null,
                `FertPBATimesYear` = null,
                `FertPBADose` = null,
                `FertPBTimesYear` = null,
                `FertPBDose` = null,
                `FertCPBTimesYear` = null,
                `FertCPBDose` = null,
                `FertManureTimesYear` = null,
                `FertManureDose` = null,
                `FertWithOrgaTBM` = null,
                `FertWithOrgaTM` = null,
                `FertWithOrgaTR` = null,
                `PeUsingHerbicide` = null,
                `PeMoneySpentHerbi` = null,
                `PeFreqHerbi` = null,
                `PeDoseHerbi` = null,
                `PeHerbi1` = null,
                `PeHerbi2` = null,
                `PeHerbi3` = null,
                `PeHerbi4` = null,
                `PeHerbi5` = null,
                `PeHerbi6` = null,
                `PeHerbi7` = null,
                `PeHerbi8` = null,
                `PeHerbi9` = null,
                `PeHerbi10` = null,
                `PeHerbi11` = null,
                `PeHerbi12` = null,
                `PeHerbi13` = null,
                `PeHerbi14` = null,
                `PeHerbi15` = null,
                `PeHerbi16` = null,
                `PeHerbi17` = null,
                `PeHerbi18` = null,
                `PeHerbi19` = null,
                `PeHerbi20` = null,
                `PeHerbi21` = null,
                `PeHerbi22` = null,
                `PeHerbi23` = null,
                `PeHerbi24` = null,
                `PeHerbi25` = null,
                `PeHerbi26` = null,
                `PeHerbi27` = null,
                `PeHerbi28` = null,
                `PeHerbi29` = null,
                `PeHerbiOther` = null,
                `PeUsingInsecticide` = null,
                `PeMoneySpentInsec` = null,
                `PeFreqInsec` = null,
                `PeDoseInsec` = null,
                `PeInsec1` = null,
                `PeInsec2` = null,
                `PeInsec3` = null,
                `PeInsec4` = null,
                `PeInsec5` = null,
                `PeInsec6` = null,
                `PeInsec7` = null,
                `PeInsec8` = null,
                `PeInsec9` = null,
                `PeInsec10` = null,
                `PeInsec11` = null,
                `PeInsec12` = null,
                `PeInsec13` = null,
                `PeInsec14` = null,
                `PeInsec15` = null,
                `PeInsec16` = null,
                `PeInsec17` = null,
                `PeInsec18` = null,
                `PeInsec19` = null,
                `PeInsec20` = null,
                `PeInsec21` = null,
                `PeInsec22` = null,
                `PeInsec23` = null,
                `PeInsecOther` = null,
                `PeUsingFungicide` = null,
                `PeMoneySpentFungi` = null,
                `PeFreqFungi` = null,
                `PeDoseFungi` = null,
                `PeFungi1` = null,
                `PeFungi2` = null,
                `PeFungi3` = null,
                `PeFungi4` = null,
                `PeFungi5` = null,
                `PeFungi6` = null,
                `PeFungi7` = null,
                `PeFungi8` = null,
                `PeFungi9` = null,
                `PeFungi10` = null,
                `PeFungi11` = null,
                `PeFungi12` = null,
                `PeFungiOther` = null,
                `PestMainRats` = null,
                `PestMainOly` = null,
                `PestMainSatora` = null,
                `PestMainTira` = null,
                `PestMainRhino` = null,
                `PestMainElep` = null,
                `PestMainOrgUtan` = null,
                `PestMainLandak` = null,
                `PestMainBabi` = null,
                `PestMainOther` = null,
                `PestMainOtherText` = null,
                `DisMainBlast` = null,
                `DisMainGeno` = null,
                `DisMainSteam` = null,
                `DisMainBud` = null,
                `DisMainSpear` = null,
                `DisMainYellow` = null,
                `DisMainAnt` = null,
                `DisMainCrown` = null,
                `DisMainViscular` = null,
                `DisMainBunch` = null,
                `DisMainOther` = null,
                `DisMainOtherText` = null,
                UseProtectiveGear = null,
                EquipHelm = null,
                EquipBoots = null,
                EquipDodosProtector = null,
                EquipMask = null,
                EquipGloves = null,
                EquipSprayGlasses = null,
                EquipEgrekProtector = null,
                EquipProtectiveClothing = null,
                PestStoreLocation = null,
                PestPackageAfterUse = null,
                OwnerOfPlantationNameText = null,
                OwnerOfPlantationLocationText = null,
                OwnerOfPlantationPhoneText = null,
                GarWitnessProveOwnership = null,
                GarNameOfWitness = null,
                GarOwnerRelationship = null,
                YearPlantingCurrent = null,
                WagsCert = null,
                TPHLocation = null,
                WagsCertStandardRSPO = null,
                WagsCertStandardMSPO = null,
                WagsPlantationStage = null,
                WagsCondEstPlantation = null
            WHERE
                `MemberID` = ?
                AND `PlotNr` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
    }

    public function getPlotSmeSurveyFormData($MemberID,$PlotNr,$SurveyNr,$DateCollection) {
        $this->load->library('awsfileupload');
        $sql="SELECT
                a.`MemberID`,
                b.MemberUID,
                b.`MemberDisplayID`
                , b.`MemberName`
                ,a.`PlotNr`,
                a.`SurveyNr`,
                a.`DateCollection`,
                #(SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedByLabel,
                /*CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS CreatedByLabel,*/
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS CreatedByLabel,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedByLabel,
                SUBSTR(a.`VillageID`,1,2) AS ProvinceID,
                d.`DistrictID`,
                c.`SubDistrictID`,
                a.`VillageID`,
                a.`PhotoOfVisit`,
                a.`GardenAreaHa`,
                IFNULL(a.GardenAreaPolygon, a.GardenAreaHa) GardenAreaPolygon,
                a.`GardenLength`,
                a.`GardenWidth`,
                ST_Y(a.`LatLong`) AS Latitude,
                ST_X(a.`LatLong`) AS Longitude,
                a.`OwnershipDoc`,
                a.OwnershipDocText,
                a.LandOwnershipType,
                a.LandOwnership,
                a.OwnerOfTheGarden,
                a.OwnerOfPlantationNameText,
                a.OwnerOfPlantationLocationText,
                a.OwnerOfPlantationPhoneText,
                a.`BusinessModel`,
                a.`PlantationConditionEst`,
                a.`AverageAgeTree`,
                a.`SoilType`,
                a.`TopographyType`,
                a.FirstPlantingYear,
                a.TreeTBM,
                a.TreeTM,
                a.TreeTR,
                a.`TypePlantMateMarihat`,
                a.`TypePlantMateMarihatNr`,
                a.`TypePlantMateDumpy`,
                a.`TypePlantMateDumpyNr`,
                a.`TypePlantMateLonsum`,
                a.`TypePlantMateLonsumNr`,
                a.`TypePlantMateSimalungun`,
                a.`TypePlantMateSimalungunNr`,
                a.`TypePlantMateDanimas`,
                a.`TypePlantMateDanimasNr`,
                a.`TypePlantMateSriwijaya`,
                a.`TypePlantMateSriwijayaNr`,
                a.`TypePlantMateSocfin`,
                a.`TypePlantMateSocfinNr`,
                a.`TypePlantMateOther`,
                a.`TypePlantMateOtherText`,
                a.`TypePlantMateOtherNr`,
                a.`TypePlantMateDoNotKnow`,
                a.TypePlantMateDoNotKnowNr,
                a.`HarvestRateDaysHighSeason`,
                a.`HarvestRateDaysLowSeason`,
                a.`AverageProdHighSeason`,
                a.`AverageProdLowSeason`,
                a.NrHighSeasonMonths,
                a.NrLowSeasonMonths,
                a.HighSeasonProduction,
                a.LowSeasonProduction,
                a.AnnualProduction,
                a.`LeanHarvestSeasonJan`,
                a.`LeanHarvestSeasonFeb`,
                a.`LeanHarvestSeasonMar`,
                a.`LeanHarvestSeasonApr`,
                a.`LeanHarvestSeasonMay`,
                a.`LeanHarvestSeasonJun`,
                a.`LeanHarvestSeasonJul`,
                a.`LeanHarvestSeasonAug`,
                a.`LeanHarvestSeasonSep`,
                a.`LeanHarvestSeasonOct`,
                a.`LeanHarvestSeasonNov`,
                a.`LeanHarvestSeasonDec`,
                a.`WhoHarvestFamily`,
                a.`WhoHarvestLabor`,
                a.`UseEFBFertilizer`,
                a.`useParaquat`,
                a.`Comment`,
                a.OwnerDocIsOwner,
                a.HaveSTDB,
                a.HaveSPPL,
                a.HowObPlantation,
                a.HowObPlantationText,
                a.TypePlantMateOtherText,
                a.PhotoOfVisitDesc,
                a.OwnerCultivateFarm,
                a.FarmEmployHiredLabor,
                a.FarmEmployFamMem,
                a.FarmEmployLaborFamMem,
                a.FarmEmployNoLabor,
                a.HowManyWorkFarm,
                a.UnderAgeWorker,
                a.AveHoursPerDay,
                a.AveDaysPerMonth,
                a.WageNominalPerDayLabor,
                a.WageNominalPerDayLaborPeriod,
                a.WageNominalPerDayFamMember,
                a.WageNominalPerDayFamMemberPeriod,
                a.HowManyDiffBuyerSoldLastYear,
                a.HowManyDiffBuyerSoldLastYearText,
                a.ToWhoSellFFBLastYear,
                a.HowManyDiffMillSoldLastYear,
                a.HowManyDiffMillSoldLastYearText,
                a.ToWhichMillSellFFBLastYear,
                a.ToWhichMillSellFFBLastYearText,
                a.TPHLocation,
                a.FertilizerDesc,
                a.FertilizerNotes,
                a.PesticideDesc,
                a.PesticideNotes,
                a.`FertNonOrganicData`,
                a.`FertMoneySpentNonOrganic`,
                a.`FertUreaTimesYear`,
                a.`FertUreaDose`,
                a.`FertSSTimesYear`,
                a.`FertSSDose`,
                a.`FertNPKTimesYear`,
                a.`FertNPKDose`,
                a.`FertTSPTimesYear`,
                a.`FertTSPDose`,
                a.`FertCUTimesYear`,
                a.`FertCUDose`,
                a.`FertKCLTimesYear`,
                a.`FertKCLDose`,
                a.`FertNPKMutiTimesYear`,
                a.`FertNPKMutiDose`,
                a.`FertBoratTimesYear`,
                a.`FertBoratDose`,
                a.`FertDolomiteTimesYear`,
                a.`FertDolomiteDose`,
                a.`FertWithNonOrgaTBM`,
                a.`FertWithNonOrgaTM`,
                a.`FertWithNonOrgaTR`,
                a.`FertUseOrganic`,
                a.`FertMoneySpentOrganic`,
                a.`FertPBATimesYear`,
                a.`FertPBADose`,
                a.`FertPBTimesYear`,
                a.`FertPBDose`,
                a.`FertCPBTimesYear`,
                a.`FertCPBDose`,
                a.`FertManureTimesYear`,
                a.`FertManureDose`,
                a.`FertWithOrgaTBM`,
                a.`FertWithOrgaTM`,
                a.`FertWithOrgaTR`,
                a.`PeUsingHerbicide`,
                a.`PeMoneySpentHerbi`,
                a.`PeFreqHerbi`,
                a.`PeDoseHerbi`,
                a.`PeHerbi1`,
                a.`PeHerbi2`,
                a.`PeHerbi3`,
                a.`PeHerbi4`,
                a.`PeHerbi5`,
                a.`PeHerbi6`,
                a.`PeHerbi7`,
                a.`PeHerbi8`,
                a.`PeHerbi9`,
                a.`PeHerbi10`,
                a.`PeHerbi11`,
                a.`PeHerbi12`,
                a.`PeHerbi13`,
                a.`PeHerbi14`,
                a.`PeHerbi15`,
                a.`PeHerbi16`,
                a.`PeHerbi17`,
                a.`PeHerbi18`,
                a.`PeHerbi19`,
                a.`PeHerbi20`,
                a.`PeHerbi21`,
                a.`PeHerbi22`,
                a.`PeHerbi23`,
                a.`PeHerbi24`,
                a.`PeHerbi25`,
                a.`PeHerbi26`,
                a.`PeHerbi27`,
                a.`PeHerbi28`,
                a.`PeHerbi29`,
                a.`PeHerbiOther`,
                a.`PeUsingInsecticide`,
                a.`PeMoneySpentInsec`,
                a.`PeFreqInsec`,
                a.`PeDoseInsec`,
                a.`PeInsec1`,
                a.`PeInsec2`,
                a.`PeInsec3`,
                a.`PeInsec4`,
                a.`PeInsec5`,
                a.`PeInsec6`,
                a.`PeInsec7`,
                a.`PeInsec8`,
                a.`PeInsec9`,
                a.`PeInsec10`,
                a.`PeInsec11`,
                a.`PeInsec12`,
                a.`PeInsec13`,
                a.`PeInsec14`,
                a.`PeInsec15`,
                a.`PeInsec16`,
                a.`PeInsec17`,
                a.`PeInsec18`,
                a.`PeInsec19`,
                a.`PeInsec20`,
                a.`PeInsec21`,
                a.`PeInsec22`,
                a.`PeInsec23`,
                a.`PeInsecOther`,
                a.`PeUsingFungicide`,
                a.`PeMoneySpentFungi`,
                a.`PeFreqFungi`,
                a.`PeDoseFungi`,
                a.`PeFungi1`,
                a.`PeFungi2`,
                a.`PeFungi3`,
                a.`PeFungi4`,
                a.`PeFungi5`,
                a.`PeFungi6`,
                a.`PeFungi7`,
                a.`PeFungi8`,
                a.`PeFungi9`,
                a.`PeFungi10`,
                a.`PeFungi11`,
                a.`PeFungi12`,
                a.`PeFungiOther`,
                a.`PestMainRats`,
                a.`PestMainOly`,
                a.`PestMainSatora`,
                a.`PestMainTira`,
                a.`PestMainRhino`,
                a.`PestMainElep`,
                a.`PestMainOrgUtan`,
                a.`PestMainLandak`,
                a.`PestMainBabi`,
                a.`PestMainOther`,
                a.`PestMainOtherText`,
                a.UseProtectiveGear,
                a.EquipHelm,
                a.EquipBoots,
                a.EquipDodosProtector,
                a.EquipMask,
                a.EquipGloves,
                a.EquipSprayGlasses,
                a.EquipEgrekProtector,
                a.EquipProtectiveClothing,
                a.PestStoreLocation,
                a.PestPackageAfterUse,
                a.`DisMainBlast`,
                a.`DisMainGeno`,
                a.`DisMainSteam`,
                a.`DisMainBud`,
                a.`DisMainSpear`,
                a.`DisMainYellow`,
                a.`DisMainAnt`,
                a.`DisMainCrown`,
                a.`DisMainViscular`,
                a.`DisMainBunch`,
                a.`DisMainOther`,
                a.`DisMainOtherText`,
                a.GarWitnessProveOwnership,
                a.GarNameOfWitness,
                a.GarOwnerRelationship,
                a.YearPlantingCurrent,
                a.WagsCert,
                a.WagsCertStandardRSPO,
                a.WagsCertStandardMSPO,
                a.WagsPlantationStage,
                a.WagsCondEstPlantation
            FROM
                `ktv_survey_plot_sme` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.`SubDistrictID` = d.`SubDistrictID`
            WHERE
                a.`MemberID` = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`DateCollection` = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            (int) $PlotNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        //yg diperlukan untuk proses lebih lanjut
        $dataRow['MemberDisplayID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-MemberDisplayID'];
        $dataRow['MemberUID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-MemberUID'];
        $dataRow['ProvinceID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-ProvinceID'];
        $dataRow['DistrictID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-DistrictID'];
        $dataRow['SubDistrictID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-SubDistrictID'];
        $dataRow['VillageID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-VillageID'];
        $dataRow['PhotoOfVisit'] = $dataRow['Koltiva.view.PlotSurvey.WinFormSmePlotSurvey-Form-PhotoOfVisit'];

        if($this->awsfileupload->doesObjectExist($dataRow['PhotoOfVisit']) == true) {
            $dataRow['PhotoOfVisitPath']    = $dataRow['PhotoOfVisit'];
            $dataRow['PhotoOfVisit']        = $this->config->item('CTCDN')."/".$dataRow['PhotoOfVisit'];
        }else{
            if($dataRow['PhotoOfVisitPath'] != ''){
                $dataRow['PhotoOfVisitPath']    = '/images/plot_visit/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfVisit'];
                $dataRow['PhotoOfVisit']        = base_url().'images/plot_visit/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfVisit'];
            }
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function deletePlotSmeSurvey($MemberID,$PlotNr,$SurveyNr,$DateCollection) {
        $this->db->trans_start();

        $sql="INSERT INTO ktv_survey_plot_sme_nullified
            SELECT
                *
            FROM
                ktv_survey_plot_sme a
            WHERE
                a.MemberID = ?
                AND a.PlotNr = ?
                AND a.SurveyNr = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        $sql="DELETE FROM ktv_survey_plot_sme WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ? LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr
        );
        $query = $this->db->query($sql,$p);

        //Cek apakah masih ada data survey untuk PlotNr ini
        $sql = "SELECT COUNT(MemberID) AS CekData FROM ktv_survey_plot_sme WHERE MemberID = ? AND PlotNr = ? AND SurveyNr = ?";
        $DataCek = $this->db->query($sql, array($MemberID,$PlotNr,$SurveyNr))->row_array();
        if((int) $DataCek['CekData'] == 0) {
            $sql = "DELETE FROM ktv_survey_plot_status_sme WHERE MemberID=? AND PlotNr=?";
            $query = $this->db->query($sql,array($MemberID,$PlotNr));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }

        return $results;
    }
}
?>