<?php
/**
 * @Author: nikolius
 * @Date:   2017-04-07 10:52:38
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Moff_data extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getMainListDistrict(){
        $sql="SELECT
                b.`ProvinceID`
                , b.`Province`
                , a.`DistrictID`
                , a.`District`
                , c.DhisSqlViewID
                , c.DhisSqlViewName
                , IF(c.DoffDisID IS NOT NULL,'".lang('Yes')."','".lang('No')."') AS Query_Available
            FROM
                ktv_district a
                INNER JOIN ktv_province b ON a.`ProvinceID` = b.`ProvinceID`
                LEFT JOIN adm_data_off c ON a.`DistrictID` = c.`ObjID` AND c.`ObjType` = 'District'
            WHERE
                a.`StatusCode` = 'active'
                AND a.active = '1'
            ORDER BY b.`Province`";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        //cek apakah file sudah tergenerate
        for ($i=0; $i < count($data); $i++) {
            if($data[$i]['Query_Available'] == lang("Yes")){
                $pathNamaFileCek = 'files/offline_data/'.date('Ymd').'_'.$data[$i]['DhisSqlViewName'].'.zip';
                if(file_exists($pathNamaFileCek)){
                    $data[$i]['File_Available'] = lang("Yes");
                }else{
                    $data[$i]['File_Available'] = lang("No");
                }
            }else{
                $data[$i]['File_Available'] = lang("No");
            }
        }

        $result['data'] = $data;
        return $result;
    }

    public function getMainListSubdistrict(){
        $sql="SELECT
                b.`DistrictID`
                , CONCAT(c.`Province`,' - ',b.`District`) AS DistrictLabel
                , a.`SubDistrictID` AS SubdistrictID
                , a.`SubDistrict` AS Subdistrict
                , IF(d.DoffDisID IS NOT NULL,'".lang('Yes')."','".lang('No')."') AS Query_Available
                , d.DhisSqlViewID
                , d.`DhisSqlViewName`
            FROM
                ktv_subdistrict a
                INNER JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                INNER JOIN ktv_province c ON b.`ProvinceID` = c.`ProvinceID`
                LEFT JOIN adm_data_off d ON a.`SubDistrictID` = d.`ObjID` AND d.`ObjType` = 'Subdistrict'
            WHERE
                a.`active` = '1'
                AND a.`StatusCode` = 'active'
            ORDER BY DistrictLabel ASC, a.`SubDistrict` ASC";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        //cek apakah file sudah tergenerate
        for ($i=0; $i < count($data); $i++) {
            if($data[$i]['Query_Available'] == lang("Yes")){
                $pathNamaFileCek = 'files/offline_data/'.date('Ymd').'_'.$data[$i]['DhisSqlViewName'].'.zip';
                if(file_exists($pathNamaFileCek)){
                    $data[$i]['File_Available'] = lang("Yes");
                }else{
                    $data[$i]['File_Available'] = lang("No");
                }
            }else{
                $data[$i]['File_Available'] = lang("No");
            }
        }

        $result['data'] = $data;
        return $result;
    }

    public function getMainListMetadata(){
        $sql="SELECT
                a.`MdoffID`
                , a.`MdoffFilename` AS Filename
                , a.`DateCreated`
                , b.`UserRealName` AS CreatedBy
            FROM
                adm_metadata_off a
                LEFT JOIN sys_user b ON a.`CreatedBy` = b.`UserId`
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.DateCreated DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getLatestMetadataFilename(){
        $sql="SELECT
                a.`MdoffFilename` AS Filename
            FROM
                adm_metadata_off a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.DateCreated DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        return $data['Filename'];
    }

    public function getLatestMetadataKCPFilename(){
        $sql="SELECT
                a.`MdoffFilename` AS Filename
            FROM
                adm_metadata_off_kcp a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.DateCreated DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        return $data['Filename'];
    }

    public function updateMetadataFilename($filename){
        if($_SESSION['userid'] == ""){
            $userid = 1;
        }else{
            $userid = $_SESSION['userid'];
        }

        $sql="INSERT INTO `adm_metadata_off` SET
              `MdoffFilename` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $filename,
            $userid
        );
        return $this->db->query($sql,$p);
    }

    public function updateMetadataKCPFilename($filename){
        if($_SESSION['userid'] == ""){
            $userid = 1;
        }else{
            $userid = $_SESSION['userid'];
        }

        $sql="INSERT INTO `adm_metadata_off_kcp` SET
              `MdoffFilename` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $filename,
            $userid
        );
        return $this->db->query($sql,$p);
    }

    public function getMasterRegion(){
        $sql="SELECT a.ProvinceID,a.Province,b.DistrictID,b.District,c.SubDistrictID,c.SubDistrict,d.VillageID,d.Village
            FROM ktv_province a, ktv_district b, ktv_subdistrict c,ktv_village d
            WHERE a.ProvinceID=b.ProvinceID AND b.DistrictID=c.DistrictID AND c.SubDistrictID=d.SubDistrictID AND a.`ProvinceID` IN ('14','15')";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function callStoredProcedureMetadata($spname,$param) {
        
        //csv config
        $this->load->dbutil();
        $delimiter = ",";
        $newline = "\r\n";

        $getmetadataoffline = "select
        p.uid p_uid
        ,replace(p.name,',','|') p_name
        ,replace(p.name,',','|') p_displayname
        ,SUBSTRING(IFNULL(DATE_FORMAT(p.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) p_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(p.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) p_lastupdated
        ,p.type p_programtype
        ,p.version p_version
        ,IFNULL(replace(p.description,',','|'),'N/A_N/A') p_description
        ,IF(p.onlyenrollonce = 1,'true','false') p_onlyenrollonce
        ,0 p_externalaccess
        ,IF(p.displayincidentdate,'true','false') p_displayincidentdate
        ,0 p_registration
        ,IF(p.selectenrollmentdatesinfuture,'true','false') p_selectenrollmentdatesinfuture
        ,IF(p.dataentrymethod,'true','false') p_dataentrymethod
        ,0 p_singleevent
        ,IF(p.ignoreoverdueevents,'true','false') p_ignoreoverdueevents
        ,IFNULL(p.relationshipfroma,0) p_relationshipfroma
        ,p.selectincidentdatesinfuture p_selectincidentdatesinfuture
        ,1 p_isassignedtouser
        ,ps.uid ps_uid
        ,replace(ps.name,',','|') ps_name
        ,replace(ps.name,',','|') ps_displayname
        ,SUBSTRING(IFNULL(DATE_FORMAT(ps.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) ps_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(ps.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) ps_lastupdated
        ,p.uid ps_program
        ,IF(ps.blockentryform,'true','false') ps_blockentryform
        ,ps.excecutiondatelabel ps_excecutiondatelabel
        ,IF(ps.displaygenerateeventbox,'true','false') ps_displaygenerateeventbox
        ,IFNULL(replace(ps.description,',','|'),'N/A_N/A') ps_description
        ,IF(ps.openafterenrollment,'true','false') ps_openafterenrollment
        ,IF(ps.capturecoordinates,'true','false') ps_capturecoordinates
        ,IF(ps.remindcompleted,'true','false') ps_remindcompleted
        ,IF(ps.validcompleteonly,'true','false') ps_validcompleteonly
        ,IFNULL(ps.sort_order,0) ps_sort_order
        ,IF(ps.generatedbyenrollmentdate,'true','false') ps_generatedbyenrollmentdate
        ,IF(ps.autogenerateevent,'true','false') ps_autogenerateevent
        ,IF(ps.allowgeneratenextvisit,'true','false') ps_allowgeneratenextvisit
        ,IF(ps.repeatable,'true','false') ps_repeatable
        ,0 ps_mindaysfromstart
        ,psec.uid psec_uid
        ,replace(psec.name,',','|') psec_name
        ,replace(psec.name,',','|') psec_displayname
        ,SUBSTRING(IFNULL(DATE_FORMAT(psec.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) psec_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(psec.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) psec_lastupdated
        ,psec.sortorder psec_sortorder
        ,ps.uid psec_programstageuid
        ,psde.uid psde_uid
        ,SUBSTRING(IFNULL(DATE_FORMAT(psde.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23)  psde_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(psde.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23)  psde_lastupdated
        ,ps.uid psde_programstageuid
        ,psec.uid psde_programstagesectionuid
        ,de.uid psde_dataelementuid
        ,IF(psde.allowfuturedate = 'true',1,0) psde_allowfuturedate
        ,IFNULL(psde.sort_order,1) psde_sort_order
        ,IF(psde.displayinreports = 'true',1,0) psde_displayinreports
        ,IF(psde.allowprovidedelsewhere = 'true',1,0) psde_allowprovidedelsewhere
        ,IF(psde.compulsory = 'true',1,0) psde_compulsory
        ,de.uid de_uid
        ,replace(de.name,',','|') de_name
        ,replace(de.name,',','|') de_displayname
        ,SUBSTRING(DATE_FORMAT(de.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) de_created
        ,SUBSTRING(DATE_FORMAT(de.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) de_lastupdated
        ,de.valuetype de_valuetype
        ,IF(de.zeroissignificant,'true','false') de_zeroissignificant
        ,replace(de.description,',','|') de_formname
        ,'TRACKER' de_domainType
        ,IFNULL(replace(de.shortname,',','|'),'N/A_N/A') de_displayformname
        ,de.optionsetuid
        ,psde.section_sort_order psde_section_sort_order
        from
        mw_program p,
        mw_programstage ps,
        mw_programstagesection psec,
        mw_programstagedataelement psde,
        (select de.*,
        case
        when os.uid is null then 'N/A_N/A'
        else
         os.uid
        end as optionsetuid from mw_dataelement de left join mw_optionset os
        on de.optionsetid=os.optionsetid) de
        where
        p.programid=ps.programid
        and ps.programstageid=psec.programstageid
        and ps.programstageid=psde.programstageid
        and psec.programstagesectionid=psde.programstagesectionid
        and psde.dataelementid=de.dataelementid
        order by p.uid,ps.uid,psec.uid,psde.uid,de.uid";
        
        $optionset_optionvalue = "select
        os.uid os_uid
        ,replace(os.name,',','|') os_name
        ,replace(os.name,',','|') os_displayname
        ,SUBSTRING(DATE_FORMAT(os.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) os_created
        ,SUBSTRING(DATE_FORMAT(os.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) os_lastupdated
        ,os.version os_version
        ,ov.uid ov_uid
        ,replace(ov.name,',','|') ov_name
        ,replace(ov.name,',','|') ov_displayname
        ,SUBSTRING(DATE_FORMAT(ov.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23)  ov_created
        ,SUBSTRING(DATE_FORMAT(ov.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23)  ov_lastupdated
        ,ov.sort_order ov_sortorder
        ,os.uid ov_optionset_uid
        ,replace(ov.code,',','|') ov_code
        from mw_optionvalue ov
        inner join mw_optionset os on os.optionsetid = ov.optionsetid
        where os.uid is not null";

        $programindicator = "select pi.uid pi_uid, 
        p.uid pi_programuid,ps.uid pi_programstageuid, 
        pi.expression, pi.filter, replace(replace(pi.description, ',', ' | '), ' ',' ') description, pi.name 
        from mw_programindicator pi, mw_program p, mw_programstage ps
        where p.programid=pi.programid
        and p.programid=ps.programid";

        $programrule = "select distinct
        pr.uId
        ,pr.name
        ,pr.name displayName
        ,SUBSTRING(DATE_FORMAT(pr.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pr_created
        ,SUBSTRING(DATE_FORMAT(pr.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pr_lastupdated
        ,p.uid as program
        ,pr.rulecondition `condition`,
        pr.description,0 externalAction
        from mw_programrule pr,
        (
            select pra.* from mw_programruleaction pra
            left join mw_dataelement de ON de.dataelementid = pra.dataelementid 
            left join mw_programstagesection sec ON sec.programstagesectionid = pra.programstagesectionid and pra.actiontype = 'HIDESECTION'
        ) pra
        , mw_program p
        where
        pr.programruleid=pra.programruleid
        and pr.programid=p.programid";

        $programrule_programruleaction = "select  
        pra.uid pra_uid
        ,SUBSTRING(DATE_FORMAT(pra.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pra_created
        ,SUBSTRING(DATE_FORMAT(pra.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pra_lastupdated
        ,pra.actiontype pra_actiontype
        ,pr.uid pra_programruleuid
        ,pra.psec_uid pra_psecuid
        ,pra.de_uid pra_deuid
        ,case
         when pra.content is null or pra.content='' then 'N/A_N/A'
         else pra.content
         end pra_content
        from mw_programrule pr,
        (
            select pra.*,sec.uid psec_uid,de.uid de_uid from mw_programruleaction pra
            left join mw_dataelement de ON de.dataelementid = pra.dataelementid 
            left join mw_programstagesection sec ON sec.programstagesectionid = pra.programstagesectionid and pra.actiontype = 'HIDESECTION'
        ) pra
        , mw_program p
        where
        pr.programruleid=pra.programruleid
        and pr.programid=p.programid";

        $programrulevariable = "select
        prv.uid prv_uid
        ,prv.name prv_name
        ,prv.name prv_displayname
        ,SUBSTRING(DATE_FORMAT(prv.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) prv_created
        ,SUBSTRING(DATE_FORMAT(prv.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) prv_lastupdated
        ,prv.sourcetype prv_sourcetype
        ,p.uid prv_programuid
        ,de.uid prv_dataelementuid
        from
        mw_programrulevariable prv
        ,mw_program p
        ,mw_dataelement de
        where
        prv.programid=p.programid
        and prv.dataelementid=de.dataelementid";

        $translation = "select DISTINCT 
            objectclass,
            objectuid,
            locale,
            objectproperty,
            IFNULL((
            select
                replace(text,
                ',',
                '|')
            from
                sys_translation
            where
                id = mw_translation.sys_translation_id),
            mw_translation.value) as value
        from
            mw_translation
        where mw_translation.objectuid is not null";

        $spcalled = $this->db->query($$spname);
        $result = $spcalled->result_array();
        $spcalled->free_result();
        
        $csv = $this->arraytocsv($result);
        
        return $csv;
    }

    public function arraytocsv($array) {

        $keys = [];
        $val = [];
        if(count($array) > 0) {
            foreach($array[0] as $key => $value) {
                $v = str_replace("\n"," ",$key);
                array_push($keys,$key);
            }

            foreach($array as $key => $value) {
                $o = [];
                foreach($value as $idx => $v) {
                    $v = str_replace("\n"," ",$v);
                    $v = str_replace("\r"," ",$v);
                    array_push($o,$v);
                }
                $o = implode(',',$o) . "\n";
                array_push($val,$o);
            }
        }   

        $keys = implode(',',$keys) . "\n";
        $val = implode('',$val);
        
        return $keys . $val;
    }

    public function getMainListMetadataBackend(){
        $sql="SELECT
                a.`MdoffID`
                , a.`MdoffFilename` AS Filename
                , a.`DateCreated`
                , b.`UserRealName` AS CreatedBy
            FROM
                adm_metadata_off_kcp a
                LEFT JOIN sys_user b ON a.`CreatedBy` = b.`UserId`
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.DateCreated DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function updateMetadataFGFilename($filename){
        if($_SESSION['userid'] == ""){
            $userid = 1;
        }else{
            $userid = $_SESSION['userid'];
        }

        $sql="INSERT INTO `adm_metadata_off_fg` SET
              `MdoffFilename` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $filename,
            $userid
        );
        return $this->db->query($sql,$p);
    }

    public function getMainListMetadataFG(){
        $sql="SELECT
                a.`MdoffID`
                , a.`MdoffFilename` AS Filename
                , a.`DateCreated`
                , b.`UserRealName` AS CreatedBy
            FROM
                adm_metadata_off_fg a
                LEFT JOIN sys_user b ON a.`CreatedBy` = b.`UserId`
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.DateCreated DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function callStoredProcedureMetadataFg($spname,$param) {
        
        //csv config
        $this->load->dbutil();
        $delimiter = ",";
        $newline = "\r\n";

        $getmetadataoffline = "select
        p.uid p_uid
        ,replace(p.name,',','|') p_name
        ,replace(p.name,',','|') p_displayname
        ,SUBSTRING(IFNULL(DATE_FORMAT(p.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) p_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(p.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) p_lastupdated
        ,p.type p_programtype
        ,p.version p_version
        ,IFNULL(replace(p.description,',','|'),'N/A_N/A') p_description
        ,IF(p.onlyenrollonce = 1,'true','false') p_onlyenrollonce
        ,0 p_externalaccess
        ,IF(p.displayincidentdate,'true','false') p_displayincidentdate
        ,0 p_registration
        ,IF(p.selectenrollmentdatesinfuture,'true','false') p_selectenrollmentdatesinfuture
        ,IF(p.dataentrymethod,'true','false') p_dataentrymethod
        ,0 p_singleevent
        ,IF(p.ignoreoverdueevents,'true','false') p_ignoreoverdueevents
        ,IFNULL(p.relationshipfroma,0) p_relationshipfroma
        ,p.selectincidentdatesinfuture p_selectincidentdatesinfuture
        ,1 p_isassignedtouser
        ,ps.uid ps_uid
        ,replace(ps.name,',','|') ps_name
        ,replace(ps.name,',','|') ps_displayname
        ,SUBSTRING(IFNULL(DATE_FORMAT(ps.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) ps_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(ps.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) ps_lastupdated
        ,p.uid ps_program
        ,IF(ps.blockentryform,'true','false') ps_blockentryform
        ,ps.excecutiondatelabel ps_excecutiondatelabel
        ,IF(ps.displaygenerateeventbox,'true','false') ps_displaygenerateeventbox
        ,IFNULL(replace(ps.description,',','|'),'N/A_N/A') ps_description
        ,IF(ps.openafterenrollment,'true','false') ps_openafterenrollment
        ,IF(ps.capturecoordinates,'true','false') ps_capturecoordinates
        ,IF(ps.remindcompleted,'true','false') ps_remindcompleted
        ,IF(ps.validcompleteonly,'true','false') ps_validcompleteonly
        ,IFNULL(ps.sort_order,0) ps_sort_order
        ,IF(ps.generatedbyenrollmentdate,'true','false') ps_generatedbyenrollmentdate
        ,IF(ps.autogenerateevent,'true','false') ps_autogenerateevent
        ,IF(ps.allowgeneratenextvisit,'true','false') ps_allowgeneratenextvisit
        ,IF(ps.repeatable,'true','false') ps_repeatable
        ,0 ps_mindaysfromstart
        ,psec.uid psec_uid
        ,replace(psec.name,',','|') psec_name
        ,replace(psec.name,',','|') psec_displayname
        ,SUBSTRING(IFNULL(DATE_FORMAT(psec.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) psec_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(psec.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23) psec_lastupdated
        ,psec.sortorder psec_sortorder
        ,ps.uid psec_programstageuid
        ,psde.uid psde_uid
        ,SUBSTRING(IFNULL(DATE_FORMAT(psde.created,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23)  psde_created
        ,SUBSTRING(IFNULL(DATE_FORMAT(psde.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),DATE_FORMAT(CURRENT_TIMESTAMP,'%Y-%m-%dT%H:%i:%s.%fZ')),1,23)  psde_lastupdated
        ,ps.uid psde_programstageuid
        ,psec.uid psde_programstagesectionuid
        ,de.uid psde_dataelementuid
        ,IF(psde.allowfuturedate = 'true',1,0) psde_allowfuturedate
        ,IFNULL(psde.sort_order,1) psde_sort_order
        ,IF(psde.displayinreports = 'true',1,0) psde_displayinreports
        ,IF(psde.allowprovidedelsewhere = 'true',1,0) psde_allowprovidedelsewhere
        ,IF(psde.compulsory = 'true',1,0) psde_compulsory
        ,de.uid de_uid
        ,replace(de.name,',','|') de_name
        ,replace(de.name,',','|') de_displayname
        ,SUBSTRING(DATE_FORMAT(de.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) de_created
        ,SUBSTRING(DATE_FORMAT(de.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) de_lastupdated
        ,de.valuetype de_valuetype
        ,IF(de.zeroissignificant,'true','false') de_zeroissignificant
        ,replace(de.description,',','|') de_formname
        ,'TRACKER' de_domainType
        ,IFNULL(replace(de.shortname,',','|'),'N/A_N/A') de_displayformname
        ,de.optionsetuid
        ,psde.section_sort_order psde_section_sort_order
        from
        mw_program p,
        mw_programstage ps,
        mw_programstagesection psec,
        mw_programstagedataelement psde,
        (select de.*,
        case
        when os.uid is null then 'N/A_N/A'
        else
         os.uid
        end as optionsetuid from mw_dataelement de left join mw_optionset os
        on de.optionsetid=os.optionsetid) de
        where
        p.programid=ps.programid
        and ps.programstageid=psec.programstageid
        and ps.programstageid=psde.programstageid
        and psec.programstagesectionid=psde.programstagesectionid
        and psde.dataelementid=de.dataelementid 
        and p.uid in('ufuKq3WIp2Z','w7yJZihCl4r','e5u8kDjncav','c64vCzHJaqE','ACKvO7JxdCu','A6mQfuKq1Ni','XQ3qreUNG2a')
        order by p.uid,ps.uid,psec.uid,psde.uid,de.uid";
        
        $optionset_optionvalue = "select
        os.uid os_uid
        ,replace(os.name,',','|') os_name
        ,replace(os.name,',','|') os_displayname
        ,SUBSTRING(DATE_FORMAT(os.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) os_created
        ,SUBSTRING(DATE_FORMAT(os.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) os_lastupdated
        ,os.version os_version
        ,ov.uid ov_uid
        ,replace(ov.name,',','|') ov_name
        ,replace(ov.name,',','|') ov_displayname
        ,SUBSTRING(DATE_FORMAT(ov.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23)  ov_created
        ,SUBSTRING(DATE_FORMAT(ov.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23)  ov_lastupdated
        ,ov.sort_order ov_sortorder
        ,os.uid ov_optionset_uid
        ,replace(ov.code,',','|') ov_code
        from mw_optionvalue ov
        inner join mw_optionset os on os.optionsetid = ov.optionsetid 
        inner join mw_dataelement md on md.optionsetid = os.optionsetid 
        inner join mw_programstagedataelement mp on mp.dataelementid = md.dataelementid 
        inner join mw_programstage ps on ps.programstageid = mp.programstageid 
        inner join mw_program p on p.programid = ps.programid 
        where os.uid is not null and p.uid in('ufuKq3WIp2Z','w7yJZihCl4r','e5u8kDjncav','c64vCzHJaqE','ACKvO7JxdCu','A6mQfuKq1Ni','XQ3qreUNG2a')";

        $programindicator = "select pi.uid pi_uid, 
        p.uid pi_programuid,ps.uid pi_programstageuid, 
        pi.expression, pi.filter, replace(replace(pi.description, ',', ' | '), ' ',' ') description, pi.name 
        from mw_programindicator pi, mw_program p, mw_programstage ps
        where p.programid=pi.programid
        and p.programid=ps.programid and p.uid in('ufuKq3WIp2Z','w7yJZihCl4r','e5u8kDjncav','c64vCzHJaqE','ACKvO7JxdCu','A6mQfuKq1Ni','XQ3qreUNG2a')";

        $programrule = "select distinct
        pr.uId
        ,pr.name
        ,pr.name displayName
        ,SUBSTRING(DATE_FORMAT(pr.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pr_created
        ,SUBSTRING(DATE_FORMAT(pr.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pr_lastupdated
        ,p.uid as program
        ,pr.rulecondition `condition`,
        pr.description,0 externalAction
        from mw_programrule pr,
        (
            select pra.* from mw_programruleaction pra
            left join mw_dataelement de ON de.dataelementid = pra.dataelementid 
            left join mw_programstagesection sec ON sec.programstagesectionid = pra.programstagesectionid and pra.actiontype = 'HIDESECTION'
        ) pra
        , mw_program p
        where
        pr.programruleid=pra.programruleid
        and pr.programid=p.programid and p.uid in('ufuKq3WIp2Z','w7yJZihCl4r','e5u8kDjncav','c64vCzHJaqE','ACKvO7JxdCu','A6mQfuKq1Ni','XQ3qreUNG2a')";

        $programrule_programruleaction = "select  
        pra.uid pra_uid
        ,SUBSTRING(DATE_FORMAT(pra.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pra_created
        ,SUBSTRING(DATE_FORMAT(pra.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) pra_lastupdated
        ,pra.actiontype pra_actiontype
        ,pr.uid pra_programruleuid
        ,pra.psec_uid pra_psecuid
        ,pra.de_uid pra_deuid
        ,case
         when pra.content is null or pra.content='' then 'N/A_N/A'
         else pra.content
         end pra_content
        from mw_programrule pr,
        (
            select pra.*,sec.uid psec_uid,de.uid de_uid from mw_programruleaction pra
            left join mw_dataelement de ON de.dataelementid = pra.dataelementid 
            left join mw_programstagesection sec ON sec.programstagesectionid = pra.programstagesectionid and pra.actiontype = 'HIDESECTION'
        ) pra
        , mw_program p
        where
        pr.programruleid=pra.programruleid
        and pr.programid=p.programid and p.uid in('ufuKq3WIp2Z','w7yJZihCl4r','e5u8kDjncav','c64vCzHJaqE','ACKvO7JxdCu','A6mQfuKq1Ni','XQ3qreUNG2a')";

        $programrulevariable = "select
        prv.uid prv_uid
        ,prv.name prv_name
        ,prv.name prv_displayname
        ,SUBSTRING(DATE_FORMAT(prv.created,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) prv_created
        ,SUBSTRING(DATE_FORMAT(prv.lastupdated,'%Y-%m-%dT%H:%i:%s.%fZ'),1,23) prv_lastupdated
        ,prv.sourcetype prv_sourcetype
        ,p.uid prv_programuid
        ,de.uid prv_dataelementuid
        from
        mw_programrulevariable prv
        ,mw_program p
        ,mw_dataelement de
        where
        prv.programid=p.programid
        and prv.dataelementid=de.dataelementid and p.uid in('ufuKq3WIp2Z','w7yJZihCl4r','e5u8kDjncav','c64vCzHJaqE','ACKvO7JxdCu','A6mQfuKq1Ni','XQ3qreUNG2a')";

        $translation = "select DISTINCT 
            objectclass,
            objectuid,
            locale,
            objectproperty,
            IFNULL((
            select
                replace(text,
                ',',
                '|')
            from
                sys_translation
            where
                id = mw_translation.sys_translation_id),
            mw_translation.value) as value
        from
            mw_translation
        where mw_translation.objectuid is not null";

        $spcalled = $this->db->query($$spname);
        $result = $spcalled->result_array();
        $spcalled->free_result();
        
        $csv = $this->arraytocsv($result);
        
        return $csv;
    }

    public function getLatestMetadataFGFilename(){
        $sql="SELECT
                a.`MdoffFilename` AS Filename
            FROM
                adm_metadata_off_fg a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.DateCreated DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        return $data['Filename'];
    }
}
?>