<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:48:54
 * @modify date 2020-05-18 13:48:54
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Mannouncement extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetAnnouncementList($page, $limit, $lang){
        $return = "";
        $this->CleanUpInactiveAnnouncementData();

        //Paging Var === (Begin)
        $StartSql = ($page - 1) * $limit;
        $LimitSql = $limit;
        //Paging Var === (End)

        $CekAdmin = $_SESSION['is_admin'];
        if($CekAdmin == "1"){
            $sql = "SELECT
                        SQL_CALC_FOUND_ROWS
                        a.`AnnID`
                        , a.`StatusType`
                        , a.`StatusPublish`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_announcement a
                    WHERE
                        a.`StatusCode` = 'active'
                    ORDER BY a.`AnnID` DESC
                    LIMIT ?,?";
            $p = array(
                (int) $StartSql,(int) $LimitSql
            );
        }else{
            $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            tbl_uni.`AnnID`
                            , tbl_uni.`StatusType`
                            , tbl_uni.`StatusPublish`
                            , tbl_uni.`PostedBy`
							,tbl_uni.LastUpdatedUnix
                        FROM
                        (
                        SELECT
                            a.`AnnID`
                            , a.`StatusType`
                            , a.`StatusPublish`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                        FROM
                            cms_announcement a
                        WHERE
                            a.`StatusCode` = 'active'
                          
                            
                        UNION
                        
                        SELECT	
                            a.`AnnID`
                            , a.`StatusType`
                            , a.`StatusPublish`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS CreatedBy
                            , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                        FROM
                            cms_announcement a
                            LEFT JOIN cms_access b ON a.`AnnID` = b.`ObjID` AND b.`ObjType` =  'announcement'
                        WHERE
                            a.`StatusCode` = 'active'	
                            AND a.`StatusType` = 'private'
                            AND b.`RoleAccessStaff` = '1'
                            AND FIND_IN_SET(?, b.`PartnerIDImplode`)
                        ) AS tbl_uni
                        ORDER BY tbl_uni.AnnID DESC
                        LIMIT ?,?";
                $p = array(
                    $DataUser['PartnerID'],(int) $StartSql,(int) $LimitSql
                );
            }else{
                return array();
            }
        }
        
        $DataList = $this->db->query($sql,$p)->result_array();
        //Hitung Total Pages
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $TotalData = $query->row()->total;
        $TotalPage = ceil($TotalData / $LimitSql);

        if(isset($DataList[0]['AnnID'])){            
            for ($i=0; $i < count($DataList); $i++) {
                $DataList[$i]['LastUpdated'] = waktu_buat_orang($DataList[$i]['LastUpdatedUnix']);
                $DataList[$i]['StatusType'] = ucwords($DataList[$i]['StatusType']);
                $DataList[$i]['Title'] = $this->_cekContentLanguageValue($DataList[$i]['AnnID'], 'Title', $lang);
                $DataList[$i]['Summary'] = $this->_cekContentLanguageValue($DataList[$i]['AnnID'], 'Summary', $lang);
                $DataList[$i]['Content'] = $this->_cekContentLanguageValue($DataList[$i]['AnnID'], 'Content', $lang);
            }
        }

        $DataReturn = array();
        $DataReturn['Items'] = $DataList;

        //Generate HTML untuk Content =================================== (Begin)
        $DataReturn['TotalPage'] = $TotalPage;
        $DataReturn['CurrentPage'] = $page;
        //Next Button
        if($page < $TotalPage){
            $DataReturn['NextPage'] = $page+1;
        }else{
            //$NextButton = '';
            $DataReturn['NextPage'] = $page;
        }

        //Prev Button
        if($page > 1){
            $DataReturn['PrevPage'] = $page-1;
        }else{
            $DataReturn['PrevPage'] = $page;
        }

        return $DataReturn;
    }

    public function GetAnnouncementDetail($AnnID, $lang){
        $sql = "SELECT
                    a.AnnID
                    , a.`StatusType`
                    , a.`StatusPublish`
                    , b.`PartnerIDImplode`
                    , b.`RoleAccessFarmer`
                    , b.`RoleAccessStaff`
                    , b.`RoleAccessTrader`
                    , b.`RoleAccessRetailer`
                    , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                    , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                FROM
                    `cms_announcement` a
                    LEFT JOIN cms_access b ON a.`AnnID` = b.`ObjID` AND b.`ObjType` = 'announcement'
                WHERE
                    a.`AnnID` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($AnnID))->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.CMS.WinFormAnnouncement-Form-".$key;
            $DataForm[$key] = $value;
        }        

        $DataForm['Title'] = $this->_cekContentLanguageValue($DataForm['AnnID'], 'Title', $lang);
        $DataForm['Summary'] = $this->_cekContentLanguageValue($DataForm['AnnID'], 'Summary', $lang);
        $DataForm['Content'] = $this->_cekContentLanguageValue($DataForm['AnnID'], 'Content', $lang);
        $DataForm['LastUpdated'] = waktu_buat_orang($DataForm['LastUpdatedUnix']);
        $DataForm['StatusType'] = ucwords($DataForm['StatusType']);
        $DataForm['Language'] = $lang;

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    function _cekContentLanguageValue($objectID, $type, $lang){
        $this->db->select('Value');
        $this->db->where('ObjectType', 'Announcement');
        $this->db->where('ObjectID', $objectID);
        $this->db->where('Type', $type);
        $this->db->where('Language', $lang);
        $query = $this->db->get('cms_content');
        if($query->num_rows() == 0){
            return '';
        }
        return $query->row()->Value;
    }

    public function InsertAnnouncement($ParamPost){
        $this->db->trans_start();

        $sql = "
            INSERT INTO 
                `cms_announcement` 
            SET  
                `StatusType` = ?,  
                `StatusPublish` = ?,  
                `CreatedBy` = ?,
                `DateCreated` = NOW()
        ";
        $p = array(         
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $annID = $this->db->insert_id();

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Title' || $key == 'Summary' || $key == 'Content'){
                $this->SetContentLanguage($annID, $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }

        //Update or Insert or Delete on Cms Access
        $this->InputAccess('insert', 'announcement', $annID, $ParamPost);  

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data Saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        }
        return $results;
    }

    public function UpdateAnnouncement($ParamPost){
        $this->db->trans_start();

        //Update CMS News Table First
        $sql = "
            UPDATE 
                `cms_announcement` a 
            SET
                a.`StatusType` = ?,
                a.StatusCode = 'active',
                a.StatusPublish = ?,
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`AnnID` = ?
            LIMIT 1
        ";
        $p = array(
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : 'draft'),
            $_SESSION['userid'],
            $ParamPost['AnnID']
        );

        $query = $this->db->query($sql,$p);

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Title' || $key == 'Summary' || $key == 'Content'){
                $this->SetContentLanguage($ParamPost['AnnID'], $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }
        
        //Update or Insert or Delete on Cms Access
        $this->InputAccess('update', 'announcement', $ParamPost['AnnID'], $ParamPost);        

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data Saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        }
        return $results;
    }

    function SetContentLanguage($objectID, $type, $lang, $value){
        //check if avaliable on table cms_content
        if($this->_getContentLanguageID($objectID, $type, $lang) == 0){
            $sql = "
                INSERT 
                    INTO `cms_content` 
                SET  
                    `Language` = ?,
                    `Type` = ?,
                    `ObjectType` = ?,  
                    `ObjectID` = ?,
                    `Value` = ?
            ";
            $p = array(  
                $lang,
                $type,
                'Announcement',
                $objectID,
                $value
            );
            return $this->db->query($sql,$p);
        } else {
            $sql = "
                UPDATE 
                    `cms_content` a 
                SET
                    a.Value = ?
                WHERE
                    a.`ContentID` = ?
                LIMIT 
                    1
            ";
            $p = array(
                $value,
                $this->_getContentLanguageID($objectID, $type, $lang)
            );
            return $this->db->query($sql,$p);
        }
    }

    function _getContentLanguageID($objectID, $type, $lang){
        $this->db->where('ObjectType', 'Announcement');
        $this->db->where('ObjectID', $objectID);
        $this->db->where('Type', $type);
        $this->db->where('Language', $lang);
        $query = $this->db->get('cms_content');
        if($query->num_rows() == 0){
            return 0;
        }
        return $query->row()->ContentID;
    }

    public function InputAccess($OpsiProses, $ObjType, $ObjID, $ParamPost){
        //Insert Access
        if($OpsiProses == 'insert'){
            if($ParamPost['StatusType'] == "Private"){
                $sql = "
                    INSERT INTO 
                        `cms_access` 
                    SET
                        `ObjType` = ?,
                        `ObjID` = ?,
                        `PartnerIDImplode` = ?,  
                        RoleAccessFarmer = ?,
                        RoleAccessTrader = ?,
                        RoleAccessStaff = ?,
                        RoleAccessRetailer = ?,
                        `DateGenerated` = NOW(),
                        `GeneratedBy` = ?
                ";
                $p = array(
                    $ObjType,
                    $ObjID,
                    (isset($ParamPost['PartnerIDImplode']) ? $ParamPost['PartnerIDImplode'] : null),
                    (isset($ParamPost['RoleAccessFarmer']) ? $ParamPost['RoleAccessFarmer'] : null),
                    (isset($ParamPost['RoleAccessTrader']) ? $ParamPost['RoleAccessTrader'] : null),
                    (isset($ParamPost['RoleAccessStaff']) ? $ParamPost['RoleAccessStaff'] : null),
                    (isset($ParamPost['RoleAccessRetailer']) ? $ParamPost['RoleAccessRetailer'] : null),
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        //Update Access
        if($OpsiProses == 'update'){
            if($ParamPost['StatusType'] == "Private"){
                $sql = "
                    INSERT INTO 
                        `cms_access` (
                            ObjType,
                            ObjID,
                            PartnerIDImplode,
                            RoleAccessFarmer,
                            RoleAccessTrader,
                            RoleAccessStaff,
                            RoleAccessRetailer,
                            DateGenerated,
                            GeneratedBy
                        )
                    VALUES (
                        ?,?,?,?,?,?,?,NOW(),?
                    )
                    ON DUPLICATE KEY UPDATE
                        PartnerIDImplode = ?,
                        RoleAccessFarmer = ?,
                        RoleAccessTrader = ?,
                        RoleAccessStaff = ?,
                        RoleAccessRetailer = ?,
                        DateGenerated = NOW(),
                        GeneratedBy = ?
                ";
                $p = array(
                    $ObjType,
                    $ObjID,
                    (isset($ParamPost['PartnerIDImplode']) ? $ParamPost['PartnerIDImplode'] : null),
                    (isset($ParamPost['RoleAccessFarmer']) ? $ParamPost['RoleAccessFarmer'] : null),
                    (isset($ParamPost['RoleAccessTrader']) ? $ParamPost['RoleAccessTrader'] : null),
                    (isset($ParamPost['RoleAccessStaff']) ? $ParamPost['RoleAccessStaff'] : null),
                    (isset($ParamPost['RoleAccessRetailer']) ? $ParamPost['RoleAccessRetailer'] : null),
                    $_SESSION['userid'],
                    (isset($ParamPost['PartnerIDImplode']) ? $ParamPost['PartnerIDImplode'] : null),
                    (isset($ParamPost['RoleAccessFarmer']) ? $ParamPost['RoleAccessFarmer'] : null),
                    (isset($ParamPost['RoleAccessTrader']) ? $ParamPost['RoleAccessTrader'] : null),
                    (isset($ParamPost['RoleAccessStaff']) ? $ParamPost['RoleAccessStaff'] : null),
                    (isset($ParamPost['RoleAccessRetailer']) ? $ParamPost['RoleAccessRetailer'] : null),
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            } else {
                $sql = "
                    DELETE FROM cms_access WHERE ObjType = ? AND ObjID = ? LIMIT 1
                ";
                $p = array(
                    $ObjType,
                    $ObjID
                );
                $query = $this->db->query($sql,$p);
            }
        }
    }

    public function DeleteAnnouncement($AnnID){
        $sql = "
            UPDATE 
                cms_announcement a SET
                a.`StatusCode` = 'nullified',
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`AnnID` = ?
            LIMIT 
                1
        ";
        $p = array(
            $_SESSION['userid'],
            $AnnID
        );
        $query = $this->db->query($sql,$p);

        if($query){
            $results['success'] = true;
            $results['message'] = "Data Deleted";
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }

        return $results;
    }

    public function CleanUpInactiveAnnouncementData(){
        $sql="SELECT
                a.`AnnID`
            FROM
                cms_announcement a
            WHERE
                a.`StatusCode` = 'inactive'";
        $data = $this->db->query($sql)->result_array();
        
        $sql = "DELETE FROM cms_announcement WHERE StatusCode='inactive'";
        $query = $this->db->query($sql);
    }

}