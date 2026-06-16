<?php
/**
 * @Author: Gitandi Nadzari
 * @Date:   2018-09-10 16:20:00
 */
class M_metadata extends CI_Model {

	public function __construct() {
        parent::__construct();
        $this->load->library('curl');
    }
    function readMapProgStageDataElements($key,$ProgStageId,$start,$limit){
			$sql = "SELECT SQL_CALC_FOUND_ROWS
				a.programstagedataelementid,
				b.`name` AS progStageName,
				b.description AS progStageDesc,
				c.`name` AS dataElementName,
				c.description AS dataElementDesc,
				a.section_sort_order,
				a.reference_field,
				a.reference_display,
			IF
				( a.custom = 1, 'true', 'false' ) AS custom 
			FROM
				mw_programstagedataelement AS a
				LEFT JOIN mw_programstage AS b ON a.programstageid = b.programstageid
				LEFT JOIN mw_dataelement AS c ON a.dataelementid = c.dataelementid
			WHERE 1 = 1 
			--filter--
			ORDER BY a.reference_field desc
			Limit ?,? ";
			$filter = '';
			if (!empty($key)) {
					$filter .= " AND (a.reference_field LIKE ('%{$key}%')";
					$filter .= " OR a.reference_display LIKE ('%{$key}%')"; // reference display
					$filter .= " OR c.`name` like ('%{$key}%'))"; // data element name
			}
			if (!empty($ProgStageId)) {
					$filter .= " AND a.programstageid = '{$ProgStageId}'"; // data element name
			}
			$sql = str_replace('--filter--',$filter,$sql);
			$query = $this->db->query($sql,array((int)$start,(int)$limit));
			$tmp = $this->db->last_query();
			//ini untuk ambil jumlah query sebelum dilimit
			$sql_total = "SELECT FOUND_ROWS() AS total";
			$query_total = $this->db->query($sql_total);
			if ($query->num_rows() > 0) {  //ini ambil dari variable $query diatas
					$total = $query_total->row_array(0);
					return array(
							'data'      => $query->result_array(),
							'total'     => $total['total'],
							'debugsql'   => $tmp
					);
        }
			// sampai sini
    }

    function readMapProgStageDataElement($id){
				$sql = "a.programstagedataelementid,
					b.`name` AS progStageName,
					b.description AS progStageDesc,
					c.`name` AS dataElementName,
					c.description AS dataElementDesc,
					a.section_sort_order,
					a.reference_field,
					a.reference_display,
					IF
						( a.custom = 1, 'true', 'false' ) AS custom 
					FROM
						mw_programstagedataelement AS a
						LEFT JOIN mw_programstage AS b ON a.programstageid = b.programstageid
						LEFT JOIN mw_dataelement AS c ON a.dataelementid = c.dataelementid 
					WHERE
						a.programstagedataelementid = ?";
				$query = $this->db->query($sql,array((int)$id));
				return $query->result_array();
    }

    function updateMapProgStageDataElement($programstagedataelementid,$reference_field,$reference_display,$custombool,$userid){
				if($custombool=="true"){
					$custom = 1;
				} else if($custombool=="false"){
					$custom = 0;
				}
        $sql = "UPDATE mw_programstagedataelement 
					SET reference_field =?,
						reference_display =?,
						custom =?,
						lastupdated = now() 
					WHERE
						programstagedataelementid =?";
        $query = $this->db->query($sql, array($reference_field,$reference_display,$custom,$programstagedataelementid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
		}
		
		public function mwProgram()
    {
			$sql = "SELECT a.`uid`, a.`name` FROM `mw_program` as a";
			$query = $this->db->query($sql);
			if ($query->num_rows()>0) {
				return $query->result_array();
			}
		}	
		
		public function table_reff()
    {
			/*
        $sql = "SELECT DISTINCT table_name
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE 
				table_schema = 'information_schema'
				AND
			table_name LIKE 'COLUMN_%'";
			*/
			$currdb = $this->db->database;
			$sql = "SELECT DISTINCT
				table_name 
			FROM
				INFORMATION_SCHEMA.COLUMNS 
			WHERE
				TABLE_SCHEMA = '${currdb}' 
				AND TABLE_NAME LIKE 'ktv%' 
			ORDER BY
				table_name ASC";

			$query = $this->db->query($sql);
			if ($query->num_rows()>0) {
				return $query->result_array();
			}
		}	

		public function routine()
    {
			$currdb = $this->db->database;
			$sql = "SELECT DISTINCT
					routine_name 
				FROM
					INFORMATION_SCHEMA.ROUTINES 
				WHERE
					ROUTINE_TYPE = 'FUNCTION' 
					AND ROUTINE_SCHEMA = '${currdb}' 
					AND routine_name LIKE 'get%' 
					OR routine_name LIKE 'set%' 
				ORDER BY
					routine_name ASC";

			$query = $this->db->query($sql);
			if ($query->num_rows()>0) {
				return $query->result_array();
			}
		}	

		public function column_reff($TableName)
    {

			/*
        $sql = "SELECT DISTINCT column_name
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE 
				table_schema = 'information_schema'
				AND
			table_name LIKE '$TableName%'";
			*/
			$currdb = $this->db->database;
			$sql = "SELECT DISTINCT
					column_name,
					concat( column_name, ' - ', column_comment ) AS column_detail 
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					TABLE_SCHEMA = '${currdb}' 
					AND TABLE_NAME = '$TableName' 
				ORDER BY
					column_name ASC";

			$query = $this->db->query($sql);
			if ($query->num_rows()>0) {
				return $query->result_array();
			}
		}	

		public function UpdateMappingData($paramPost){
			$mw_mapping_id=$paramPost['mw_mapping_id'];
			$program_uid=$paramPost['program_uid'];
			$TableName=$paramPost['TableName'];
			$DataElement=$paramPost['DataElement'];
			$ColumnName=$paramPost['ColumnName'];
			$CustomFuction=$paramPost['CustomFuction'];
			$Execute=$paramPost['Execute'];
			$Priority=$paramPost['Priority'];
			// $de_queue=$paramPost['de_queue'];
			$de_queue=($paramPost['de_queue']!=""?$paramPost['de_queue']:0);
			$field_queue=($paramPost['field_queue']!=""?$paramPost['field_queue']:0);

		//	exit;
			$sql = "UPDATE `mw_mapping` SET
				`program_uid` = '$program_uid',
				`table_reff` = '$TableName',
				`dataelement_uid` = '$DataElement',
				`field_reff` = '$ColumnName',
				`custom_function` = '$CustomFuction',
				`executeSql` = $Execute,
				`priority` = $Priority,
				`dataelement_queue` = $de_queue,
				`field_queue` = $field_queue
			WHERE
			mw_mapping_id = '$mw_mapping_id'";
			// echo $sql();
			// die();
		//	program_uid = '$program_uid'
	
			// $p = array(
			// 	(isset($paramPost['program_uid']) ? $paramPost['program_uid'] : null),
			// 	(isset($paramPost['TableName']) ? $paramPost['TableName'] : null),            
			// 	(isset($paramPost['DataElement']) ? $paramPost['DataElement'] : null),				
			// 	(isset($paramPost['ColumnName']) ? $paramPost['ColumnName'] : null),
			// 	(isset($paramPost['CustomFuction']) ? $paramPost['CustomFuction'] : null),
			// 	(isset($paramPost['Execute']) ? $paramPost['Execute'] : null),
			// 	(isset($paramPost['Priority']) ? $paramPost['Priority'] : null),
			// 	$_SESSION['userid']
			// );
		
			$query = $this->db->query($sql);
			// $query = $this->db->query($sql,$p);
		 
			if ($this->db->trans_status() === false) {
					$this->db->trans_rollback();
					$results['success'] = false;
					$results['message'] = lang("Failed to save data");
			} else {
					$this->db->trans_commit();
					$results['success'] = true;
					$results['message'] = lang("Data saved");
					// $results['PersonID'] = $paramPost['PersonID'];
					// $results['StaffID'] = $paramPost['StaffID'];
			}
			return $results;
	}
	
  
		public function InsertMappingData($paramPost){

			$sql = "INSERT INTO `mw_mapping` SET
							`program_uid` = ?,
							`table_reff` = ?,
							`dataelement_uid` = ?,
							`field_reff` = ?,
							`custom_function` = ?,
							`executeSql` = ?,
							`priority` = ?,
							`dataelement_queue` = ?,
							`field_queue` = ?";
							
			$p = array(
					(isset($paramPost['program_uid']) ? $paramPost['program_uid'] : null),
					(isset($paramPost['TableName']) ? $paramPost['TableName'] : null),            
					(isset($paramPost['DataElement']) ? $paramPost['DataElement'] : null),				
					(isset($paramPost['ColumnName']) ? $paramPost['ColumnName'] : null),
					(isset($paramPost['CustomFuction']) ? $paramPost['CustomFuction'] : null),
					(isset($paramPost['Execute']) ? $paramPost['Execute'] : null),
					(isset($paramPost['Priority']) ? $paramPost['Priority'] : null),
					(isset($paramPost['de_queue']) ? $paramPost['de_queue'] : 0),
					(isset($paramPost['field_queue']) ? $paramPost['field_queue'] : 0)
			);
			// $p = array(
			// 		(isset($paramPost['program_uid']) ? $paramPost['program_uid'] : null),
			// 		(isset($paramPost['TableName']) ? $paramPost['TableName'] : null),            
			// 		(isset($paramPost['DataElement']) ? $paramPost['DataElement'] : null),				
			// 		(isset($paramPost['ColumnName']) ? $paramPost['ColumnName'] : null),
			// 		(isset($paramPost['CustomFuction']) ? $paramPost['CustomFuction'] : null),
			// 		(isset($paramPost['Execute']) ? $paramPost['Execute'] : null),
			// 		(isset($paramPost['Priority']) ? $paramPost['Priority'] : null),
			// 		$_SESSION['userid']
			// );

			$query = $this->db->query($sql,$p);
		
			if ($this->db->trans_status() === false) {
					$this->db->trans_rollback();
					$results['success'] = false;
					$results['message'] = lang("Failed to save data");
			} else {
					$this->db->trans_commit();
					$results['success'] = true;
					$results['message'] = lang("Data saved");
					// $results['PersonID'] = $PersonID;
					// $results['StaffID'] = $StaffID;
			}
			return $results;
	}

	public function GetMetadataFormOpen($MappingId){
			// program_uid  local
			// mw_mapping_id server
			$sql = "SELECT  
							program_uid as program_uid,
							de_uid as DataElement,							
							de_name as NameDataElement,
							table_reff as TableName,
							field_reff as ColumnName,
							mw_mapping_id AS mw_mapping_id,
							custom_function as CustomFuction,
							executeSql as Execute,
							priority as Priority						
							FROM v_metadata_mapping
							WHERE
							mw_mapping_id ='$MappingId'";
			$query = $this->db->query($sql);
			$data = $query->row_array();
				//prep variable
			$DataForm = array();
			foreach ($data as $key => $value) {
					$keyNew = "Koltiva.view.DataAdm.MainForm-FormBasicData-".$key;
					$DataForm[$keyNew] = $value;
			}

			//Buat dipakai langsung di JS
			$return['success'] = true;
			$return['data'] = $DataForm;
			return $return;
	}


	public function DeleteMappingData($MappingId){
			
		$sql = "DELETE FROM `mw_mapping` 
				WHERE				
				   	mw_mapping_id = '$MappingId'
				";
		$p = array(
				$_SESSION['userid'],
				$ComActID
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$results['success'] = false;
				$results['message'] = lang("Failed to delete mapping data");
		} else {
				$this->db->trans_commit();
				$results['success'] = true;
				$results['message'] = lang("Mapping data deleted");
		}

		return $results;
	}

	
	public function mwProgramStage()
	{
			$sql = "SELECT
				a.programstageid,
				a.programid,
				a.`name`,
				b.`name` AS programName,
				b.description,
				b.reference,
				b.STATUS,
				b.`order` 
			FROM
				`mw_programstage` AS a
				LEFT JOIN mw_program AS b ON b.programid = a.programid 
			ORDER BY
				`b`.`order` IS NULL,
				`b`.`order` ASC";
		$query = $this->db->query($sql);
		if ($query->num_rows()>0) {
			return $query->result_array();
		}
	}
		
	public function mwMetadataGrid($ProgStageId,$start,$limit)
	{
			$sql = "SELECT SQL_CALC_FOUND_ROWS a.program_uid,
			a.`name`,
			a.sec_name,
			a.program_stage_uid,
			a.de_uid,
			a.de_name,
			a.mw_mapping_id,
			a.table_reff,
			a.field_reff,
			a.custom_function,
			a.executeSql,
			a.priority,
			a.sortorder,
			a.section_sort_order,
			a.de_sort_order,
			a.de_queue,
			a.field_queue,
			IF ( a.forPull = 1, 'true', 'false' ) as forPull 
			FROM v_metadata_mapping as a
			--filter--		
			LIMIT ?, ?";

			$filter = '';
			if (!empty($ProgStageId)) {
					$filter .= " WHERE a.program_uid = '{$ProgStageId}'"; // data element name
			}

			$sql = str_replace('--filter--',$filter,$sql);
			$query = $this->db->query($sql,array((int)$start,(int)$limit));
			//echo $this->db->last_query();
			//die();
			$sql_total = "SELECT FOUND_ROWS() AS total";

			$query_total = $this->db->query($sql_total);

			// if ($query->num_rows() > 0) {  //ini ambil dari variable $query diatas
			// 		$total = $query_total->row_array(0);
			// 		return array(
			// 			'data'      => $query->result_array(),
			// 			'total'     => $total['total'],
			// 		);
			// }
			if ($query) {
				$total = $query_total->row_array(0);
				$results['success'] = true;
				$results['data'] = $query->result_array();
				$results['total'] = $total['total'];
			} else {
				$results['success'] = false;
				$results['data'] = null;
				$results['message'] = "no record found";
			}
			return $results;
	}	
		
	public function mwProgramStages($ProgStageId,$start,$limit)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
					a.programstageid,
					a.programid,
					a.`name`,
					b.`name` AS programName,
					b.description,
					b.reference,
				IF
					( b.STATUS = 1, 'true', 'false' ) AS STATUS,
					b.`order` 
				FROM
					`mw_programstage` AS a
					LEFT JOIN mw_program AS b ON b.programid = a.programid  
				WHERE 1 = 1 
				--filter--
				ORDER BY `b`.`order` is null, `b`.`order` ASC
				Limit ?,? ";
				$filter = '';
						if (!empty($ProgStageId)) {
								$filter .= " AND a.programstageid = '{$ProgStageId}'"; // data element name
				}
				$sql = str_replace('--filter--',$filter,$sql);
        $query = $this->db->query($sql,array((int)$start,(int)$limit));
		
				//ini untuk ambil jumlah query sebelum dilimit
				$sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {  //ini ambil dari variable $query diatas
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total'],
                );
        }
    }
	function updateMwProgram($programid,$programName,$description,$reference,$statusbool,$order,$userid){
			if($statusbool=="true"){
				$status = 1;
			} else if($statusbool=="false"){
				$status = 0;
			}
			$sql = "
				UPDATE mw_program 
				SET `name` =?,
					description =?,
					reference =?,
					`status` =?,
					`order` =?,
					lastupdated = now() 
				WHERE
					programid =?";
        $query = $this->db->query($sql, array($programName,$description,$reference,$status,$order,$programid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
	}

	function syncMetadataRecord(){
		$sql1 = "
			INSERT INTO mw_dataelement ( dataelementid, uid, `code`,
				created, lastupdated, `name`,
				shortname, description, formname,
				valuetype, domaintype, aggregationtype,
				categorycomboid, url,
				zeroissignificant, optionsetid,
				commentoptionsetid, legendsetid,
				userid, publicaccess
			) 
			SELECT dataelementid, uid, `code`,
				created, lastupdated, `name`,
				shortname, description, formname,
				valuetype, domaintype, aggregationtype,
				categorycomboid, url, zeroissignificant,
				optionsetid, commentoptionsetid, legendsetid,
				userid, publicaccess 
			FROM
				dataelement b 
				ON DUPLICATE KEY 
				UPDATE `code` = b.code, created = b.created, lastupdated = b.lastupdated,
				`name` = b.name, shortname = b.shortname, description = b.description,
				formname = b.formname, valuetype = b.valuetype, domaintype = b.domaintype,
				aggregationtype = b.aggregationtype, categorycomboid = b.categorycomboid, url = b.url,
				zeroissignificant = b.zeroissignificant, optionsetid = b.optionsetid,
				commentoptionsetid = b.commentoptionsetid, legendsetid = b.legendsetid, userid = b.userid, publicaccess = b.publicaccess ;";
		$query1 = $this->db->query($sql1); //mw_dataelement
		if ($query1) {
				$results['debug'] = "Qry1 record updated.";
		} else {
				$results['debug'] = "Qry1 Failed to update record";
		}
		$sql2 = "INSERT INTO mw_organisationunit ( organisationunitid, uid, `code`,
		  created, lastupdated, `name`,
		  shortname, parentid, path, hierarchylevel,
		  `uuid`, description, openingdate, closeddate, 
		  `comment`, featuretype, coordinates, url,
		  contactperson, address, email, phonenumber, userid
		)  
		SELECT organisationunitid, uid, `code`,
		  created, lastupdated, `name`,
		  shortname, parentid, path, hierarchylevel,
		  `uuid`, description, openingdate, closeddate,
		  `comment`, featuretype, coordinates, url,
		  contactperson, address, email, phonenumber, userid 
		FROM
		  organisationunit b 
		  ON DUPLICATE KEY 
		  UPDATE 
			organisationunitid = b.organisationunitid, uid = b.uid, `code` = b.code,
			created = b.created, lastupdated = b.lastupdated, `name` = b.name,
			shortname = b.shortname, parentid = b.parentid, path = b.path, hierarchylevel = b.hierarchylevel,
			`uuid` = b.uuid, description = b.description, openingdate = b.openingdate, closeddate = b.closeddate,
			`comment` = b.comment, featuretype = b.featuretype, coordinates = b.coordinates, url = b.url,
			contactperson = b.contactperson, address = b.address, email = b.email, phonenumber = b.phonenumber, userid = b.userid ;";
		$query2 = $this->db->query($sql2); //mw_organisationunit
		if ($query2) {
				$results['debug'] .= "||Qry2 record updated.";
		} else {
				$results['debug'] .= "||Qry2 Failed to update record";
		}
		$sql3 = "INSERT INTO mw_program (programid, uid, `code`,
		  created, lastupdated, `name`, shortname, `version`,
		  enrollmentdatelabel, incidentdatelabel, `type`,
		  displayincidentdate, onlyenrollonce, skipoffline,
		  displayfrontpagelist, ignoreoverdueevents, selectenrollmentdatesinfuture,
		  selectincidentdatesinfuture, relationshiptext, relationshiptypeid, relationshipfroma,
		  relatedprogramid, dataentrymethod, categorycomboid, 
		  trackedentityid, dataentryformid, workflowid, userid,publicaccess
		) 
		SELECT programid, uid, `code`, 
		  created, lastupdated, `name`, shortname, `version`,
		  enrollmentdatelabel, incidentdatelabel, `type`,
		  displayincidentdate, onlyenrollonce, skipoffline,
		  displayfrontpagelist, ignoreoverdueevents, selectenrollmentdatesinfuture,
		  selectincidentdatesinfuture, relationshiptext, relationshiptypeid, relationshipfroma,
		  relatedprogramid, dataentrymethod, categorycomboid, 
		  trackedentityid, dataentryformid, workflowid, userid, publicaccess 
		FROM
		  program b 
		  ON DUPLICATE KEY 
		  UPDATE 
			`code` = b.code, created = b.created, lastupdated = b.lastupdated, `name` = b.name,
			shortname = b.shortname, `version` = b.version, enrollmentdatelabel = b.enrollmentdatelabel,
			incidentdatelabel = b.incidentdatelabel, `type` = b.type, displayincidentdate = b.displayincidentdate,
			onlyenrollonce = b.onlyenrollonce, skipoffline = b.skipoffline,	displayfrontpagelist = b.displayfrontpagelist,
			ignoreoverdueevents = b.ignoreoverdueevents, selectenrollmentdatesinfuture = b.selectenrollmentdatesinfuture,
			selectincidentdatesinfuture = b.selectincidentdatesinfuture, relationshiptext = b.relationshiptext,
			relationshiptypeid = b.relationshiptypeid, relationshipfroma = b.relationshipfroma, relatedprogramid = b.relatedprogramid,
			dataentrymethod = b.dataentrymethod, categorycomboid = b.categorycomboid, trackedentityid = b.trackedentityid,
			dataentryformid = b.dataentryformid, workflowid = b.workflowid,	userid = b.userid, publicaccess = b.publicaccess ;";
		$query3 = $this->db->query($sql3); //mw_program
		if ($query3) {
            $results['debug'] .= "||Qry3 record updated.";
        } else {
            $results['debug'] .= "||Qry3 Failed to update record";
        }
		
		$sql4 = "INSERT INTO mw_programstage (   programstageid,   uid,   `code`,
		  created,  lastupdated,  `name`,  description,  mindaysfromstart,  programid,  `repeatable`,
		  dataentryformid,  standardinterval,  excecutiondatelabel,  autogenerateevent,  validcompleteonly,  
		  displaygenerateeventbox,  capturecoordinates,  generatedbyenrollmentdate,  blockentryform,  remindcompleted,
		  allowgeneratenextvisit,  openafterenrollment,  reportdatetouse,  pregenerateuid,  hideduedate,  sort_order,  periodtypeid
		) 
		SELECT   programstageid,  uid,  `code`,  
		  created,  lastupdated,  `name`,  description,  mindaysfromstart,  programid,  `repeatable`,
		  dataentryformid,  standardinterval,  excecutiondatelabel,  autogenerateevent,  validcompleteonly,
		  displaygenerateeventbox,  capturecoordinates,  generatedbyenrollmentdate,  blockentryform,  remindcompleted,  
		  allowgeneratenextvisit,  openafterenrollment,  reportdatetouse,  pregenerateuid,  hideduedate,  sort_order,  periodtypeid 
		FROM
		  programstage b 
		  ON DUPLICATE KEY 
		  UPDATE 
			programstageid = b.programstageid, uid = b.uid, `code` = b.code, created = b.created,
			lastupdated = b.lastupdated, `name` = b.name, description = b.description, mindaysfromstart = b.mindaysfromstart,
			programid = b.programid, `repeatable` = b.repeatable, dataentryformid = b.dataentryformid, standardinterval = b.standardinterval,
			excecutiondatelabel = b.excecutiondatelabel, autogenerateevent = b.autogenerateevent, validcompleteonly = b.validcompleteonly,
			displaygenerateeventbox = b.displaygenerateeventbox, capturecoordinates = b.capturecoordinates,
			generatedbyenrollmentdate = b.generatedbyenrollmentdate, blockentryform = b.blockentryform,
			remindcompleted = b.remindcompleted, allowgeneratenextvisit = b.allowgeneratenextvisit,
			openafterenrollment = b.openafterenrollment, reportdatetouse = b.reportdatetouse,
			pregenerateuid = b.pregenerateuid, hideduedate = b.hideduedate,
			sort_order = b.sort_order,periodtypeid = b.periodtypeid ;";
		$query4 = $this->db->query($sql4); //mw_programstage
		if ($query4) {
            $results['debug'] .= "||Qry4 record updated.";
        } else {
            $results['debug'] .= "||Qry4 Failed to update record";
        }
		
		$sql5 = "INSERT INTO mw_programstagedataelement ( programstagedataelementid, uid, `CODE`, created,
		  lastupdated, programstageid, dataelementid, compulsory, allowprovidedelsewhere, sort_order,
		  displayinreports, allowfuturedate, programstagesectionid, section_sort_order
		) 
		SELECT 
		  programstagedataelementid, uid, `code`, created,
		  lastupdated, programstageid, dataelementid, compulsory, allowprovidedelsewhere, sort_order,
		  displayinreports, allowfuturedate, programstagesectionid, section_sort_order 
		FROM
		  programstagedataelement b 
		  ON DUPLICATE KEY 
		  UPDATE 
			programstagedataelementid = b.programstagedataelementid, uid = b.uid, `code` = b.code, created = b.created,
			lastupdated = b.lastupdated, programstageid = b.programstageid, dataelementid = b.dataelementid, compulsory = b.compulsory,
			allowprovidedelsewhere = b.allowprovidedelsewhere, sort_order = b.sort_order, displayinreports = b.displayinreports,
			allowfuturedate = b.allowfuturedate, programstagesectionid = b.programstagesectionid,
			section_sort_order = b.section_sort_order ;";
		$query5 = $this->db->query($sql5); //mw_programstagedataelement
		if ($query5) {
            $results['debug'] .= "||Qry5 record updated.";
        } else {
            $results['debug'] .= "||Qry5 Failed to update record";
        }
		
		$sql6 = "INSERT INTO mw_programstagesection ( programstagesectionid, uid, `code`, created, 
		  lastupdated, `name`, programstageid, sortorder
		) 
		SELECT 
		  programstagesectionid, uid, `code`, created, lastupdated, `name`, programstageid, sortorder 
		FROM
		  programstagesection b 
		  ON DUPLICATE KEY 
		  UPDATE 
			programstagesectionid = b.programstagesectionid, uid = b.uid, `code` = b.code, created = b.created,
			lastupdated = b.lastupdated, `name` = b.name, programstageid = b.programstageid, sortorder = b.sortorder ;";
		$query6 = $this->db->query($sql6); //mw_programstagesection
		if ($query6) {
            $results['debug'] .= "||Qry6 record updated.";
        } else {
            $results['debug'] .= "||Qry6 Failed to update record";
        }
		
		$sql7 = "INSERT INTO mw_translation ( translationid, uid, `CODE`, created, lastupdated,
			objectclass, objectuid, locale, objectproperty, `VALUE`
		) 
		SELECT translationid, uid, `CODE`, created, lastupdated,
		  objectclass, objectuid, locale, objectproperty, `VALUE` 
		FROM
		  translation b 
		  ON DUPLICATE KEY 
		  UPDATE 
			translationid = b.translationid, uid = b.uid, `CODE` = b.code,	created = b.created, lastupdated = b.lastupdated,
			objectclass = b.objectclass, objectuid = b.objectuid, locale = b.locale, objectproperty = b.objectproperty, `VALUE` = b.value ;";
		$query7 = $this->db->query($sql7); //mw_translation
		if ($query7) {
            $results['debug'] .= "||Qry7 record updated.";
        } else {
            $results['debug'] .= "||Qry7 Failed to update record";
        }
		
		$sql8 = "INSERT INTO mw_usergroup ( usergroupid, uid, `code`, created, lastupdated, `name`, userid, publicaccess) 
		SELECT usergroupid, uid, `code`, created, lastupdated, `name`, userid, publicaccess 
		FROM
		  usergroup b 
		  ON DUPLICATE KEY 
		  UPDATE 
			usergroupid = b.usergroupid, uid = b.uid, `code` = b.code, created = b.created,	lastupdated = b.lastupdated,
			`name` = b.name, userid = b.userid, publicaccess = b.publicaccess ;";
		$query8 = $this->db->query($sql8); //mw_usergroup
		if ($query8) {
            $results['debug'] .= "||Qry8 record updated.";
        } else {
            $results['debug'] .= "||Qry8 Failed to update record";
        }
		
		$sql9 = "INSERT INTO mw_userrole (userroleid, uid, `code`, created, lastupdated, `name`, description, userid, publicaccess) 
		SELECT 
		  userroleid, uid, `code`, created, lastupdated, `name`, description, userid, publicaccess 
		FROM
		  userrole b 
		  ON DUPLICATE KEY 
		  UPDATE 
			userroleid = b.userroleid, uid = b.uid, `code` = b.code, created = b.created, lastupdated = b.lastupdated,
			`name` = b.name, description = b.description, userid = b.userid, publicaccess = b.publicaccess ;";
		$query9 = $this->db->query($sql9); //mw_userrole
		if ($query9) {
            $results['debug'] .= "||Qry9 record updated.";
        } else {
            $results['debug'] .= "||Qry9 Failed to update record";
        }
		
		if ($query1||$query2||$query3||$query4||$query5||$query6||$query7||$query8||$query9) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
		return $results;
	}
	function testPgConnection(){
		ini_set('display_errors',true);
		error_reporting(E_ALL);
		$pgsqldb = $this->load->database('pgsqlcon', TRUE);
		$defdb = $this->load->database('default', TRUE);
		
		$sqlpgsql = "SELECT * FROM tbltest ORDER BY id ASC";
		$querypgsql = $pgsqldb->query($sqlpgsql);
		$tmp = "";
		if ($querypgsql->num_rows() > 0) {
			foreach ($querypgsql->result() as $rowabsen)
			{	
				$idx = $rowabsen->id;
				$testName = $rowabsen->testName;
				$tmp .= $testName." ";
			}
			
		}
		
		$results['success'] = true;
		$results['message'] = $tmp." || ".bin2hex("Andi@2018!");
		return $results;
	}
	public function mwMetadataSelectedGrid($ProgStageId,$MappingId,$DeUid,$tblReff,$fieldReff)
	{
			$sql = "SELECT SQL_CALC_FOUND_ROWS a.program_uid,
			a.`name`,
			a.sec_name,
			a.de_uid,
			a.de_name,
			a.mw_mapping_id,
			a.table_reff,
			a.field_reff,
			a.custom_function,
			a.executeSql,
			a.priority,
			a.sortorder,
			a.section_sort_order,
			a.de_sort_order,
			a.de_queue,
			a.field_queue,
			IF ( a.forPull = 1, 'true', 'false' ) as forPull
			FROM v_metadata_mapping as a
			--filter--		
			";

			$filter = '';
			if (!empty($ProgStageId)) {
					$filter .= " WHERE a.program_uid = '{$ProgStageId}' AND a.forPull = 1"; // data element name

					if(!empty($tblReff)&&!empty($fieldReff)&&!empty($DeUid)){
						$filter .= " AND (( a.table_reff = '{$tblReff}' AND a.field_reff = '{$fieldReff}' ) OR a.de_uid = '{$DeUid}')";
						// $filter .= " a.mw_mapping_id = '{$MappingId}' OR a.table_reff = '{$tblReff}' OR a.field_reff = '{$fieldReff}'";
					} else if(!empty($MappingId)){
						$filter .= " AND a.mw_mapping_id = '{$MappingId}'";
					} else {
						$filter .= " AND (";
						if(!empty($MappingId)){
							$filter .= " a.mw_mapping_id = '{$MappingId}'";
						} else {
							$filter .= " a.mw_mapping_id is NULL";
						}
						if(!empty($tblReff)){
							$filter .= " OR a.table_reff = '{$tblReff}'";
						} else {
							$filter .= " OR a.table_reff is NULL";
						}
						if(!empty($fieldReff)){
							$filter .= " OR a.field_reff = '{$fieldReff}'";
						} else {
							$filter .= " OR a.field_reff is NULL";
						}
						$filter .= ")";
					}
			}

			$sql = str_replace('--filter--',$filter,$sql);
			$query = $this->db->query($sql);
			$tmp = $this->db->last_query();
			// $query = $this->db->query($sql,array((int)$start,(int)$limit));
			// $tmp = $this->db->last_query();
			// die();
			$sql_total = "SELECT FOUND_ROWS() AS total";

			$query_total = $this->db->query($sql_total);

			if ($query) {
				$total = $query_total->row_array(0);
				$results['success'] = true;
				$results['data'] = $query->result_array();
				$results['total'] = $total['total'];
				$results['tmp'] = $tmp;
			} else {
				$results['success'] = false;
				$results['data'] = null;
				$results['message'] = "no record found";
			}
			return $results;
	}	
	function dataelement_reff($ProgUid){
			$sql = "SELECT de_uid as id, de_name as label FROM `v_metadata_mapping`
					WHERE program_uid = ?";
			$query = $this->db->query($sql, array($ProgUid));
			if ($query->num_rows()>0) {
				return $query->result_array();
			}
	}
	function UpdatePullInfo($paramPost){
		if($paramPost['forPull'] == "true"){
			$status = 1;
		} else if($paramPost['forPull'] == "false"){
			$status = 0;
		}
		$sql = "UPDATE mw_programstagedataelement 
			SET `forPull` =?
			WHERE
				uid =?";
		$p = array($status,$paramPost['program_stage_uid']);
		$query = $this->db->query($sql, $p);
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record updated.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
		}
		return $results;
	}
	function reloadMetadataByKafka(){
		$sql = "SELECT SetName, SetValue FROM `sys_setting`
			WHERE SetKey = 'reload_metadata_by_kafka'";
		$query = $this->db->query($sql);
		$row = $query->row();
		if (isset($row))
		{
			$params = $row->SetValue;
			// compose url
			$url = $this->config->item('kafka_url') . 'executeJob/?job=/opt/data-integration/' . $params.'&level=Debug';
			$this->curl->create($url);
			$this->curl->options(array(
				CURLOPT_HTTPHEADER => array(
					'Authorization: Basic Y2x1c3RlcjpjbHVzdGVy'
				)
				/*
				* ps : Autorization hardcode, sesuaikan lagi jika user dan password middleware diganti
				*/
			));
			
 
			$response = $this->curl->execute();
			$xml = simplexml_load_string($response);
			$jsonresponse = json_encode($xml);
			
			return json_decode($jsonresponse, true);
		}	
	}
}
?>
