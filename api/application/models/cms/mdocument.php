<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:48:54
 * @modify date 2020-05-18 13:48:54
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Mdocument extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetDocumentList($page, $limit, $lang, $search = NULL){
        $return = "";
        $this->CleanUpInactiveDocumentData();

        //Paging Var === (Begin)
        $StartSql = ($page - 1) * $limit;
        $LimitSql = $limit;
        //Paging Var === (End)

        $CekAdmin = $_SESSION['is_admin'];

        /* if ($search != NULL) {
            $where .= " AND a.Name LIKE '%{$search}%' "; 
        } */

        if($CekAdmin == "1"){
            $sql = "
                SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`DocID`
                    , a.`StatusType`
                    , a.`StatusPublish`
                    , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy, 
                    UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                FROM
                    `cms_document` a
                INNER JOIN (
                    SELECT c.`ObjectID` AS id
                    FROM cms_content c
                    WHERE 
                        c.`Language` = ? AND c.ObjectType = ?
                    GROUP BY c.`ObjectID`
                ) b ON b.id = a.`DocID`
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`DocID` DESC
                LIMIT ?,?
            ";
            $p = array(
                ucwords($lang), 'Document', (int) $StartSql,(int) $LimitSql
            );
        }else{
            /* $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){ */
                $sql = "
                    SELECT
                        SQL_CALC_FOUND_ROWS
                        tbl_uni.`DocID`
                        , tbl_uni.`StatusType`
                        , tbl_uni.`StatusPublish`
                        , tbl_uni.PostedBy
                        , tbl_uni.LastUpdatedUnix
                    FROM
                    (
                    SELECT
                        a.`DocID`
                        , a.`StatusType`
                        , a.`StatusPublish`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_document a
                    WHERE
                        a.`StatusCode` = 'active'
                       
                        
                    UNION
                    
                    SELECT  
                        a.`DocID`
                        , a.`StatusType`
                        , a.`StatusPublish`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_document a
                        LEFT JOIN cms_access b ON a.`DocID` = b.`ObjID` AND b.`ObjType` =  'document'
                    WHERE
                        a.`StatusCode` = 'active'   
                        AND a.`StatusType` = 'private'
                        /* AND b.`RoleAccessStaff` = '1'
                        AND FIND_IN_SET(?, b.`PartnerIDImplode`) */
                    ) AS tbl_uni
                    INNER JOIN (
                        SELECT ca.`ObjectID` AS id
                        FROM cms_content ca
                        WHERE 
                            ca.`Language` = ? AND ca.ObjectType = ?
                        GROUP BY ca.`ObjectID`
                    ) bc ON bc.id = tbl_uni.`DocID`
                    ORDER BY tbl_uni.DocID DESC
                    LIMIT ?,?
                ";
                $p = array(
                    $DataUser['PartnerID'], ucwords($lang), 'Document', (int) $StartSql,(int) $LimitSql
                );
            /* }else{
                return array();
            } */
        }
        
        $DataList = $this->db->query($sql,$p)->result_array();

        //Hitung Total Pages
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $TotalData = $query->row()->total;
        $TotalPage = ceil($TotalData / $LimitSql);
        
        if(isset($DataList[0]['DocID'])){            
            for ($i=0; $i < count($DataList); $i++) {
                $DataList[$i]['LastUpdated'] = waktu_buat_orang($DataList[$i]['LastUpdatedUnix']);
                $DataList[$i]['StatusType'] = ucwords($DataList[$i]['StatusType']);
                $DataList[$i]['Name'] = $this->_cekContentLanguageValue($DataList[$i]['DocID'], 'Name', $lang);
                $DataList[$i]['Summary'] = $this->_cekContentLanguageValue($DataList[$i]['DocID'], 'Summary', $lang);
                $DataList[$i]['Description'] = $this->_cekContentLanguageValue($DataList[$i]['DocID'], 'Description', $lang);
                $DataList[$i]['count'] = $this->db->where('ObjectID', $DataList[$i]['DocID'])
                                                  ->where('ObjectType', 'Document')
                                                  ->where('Language', $lang)
                                                  ->get('cms_content')->num_rows();
            }
        }

        foreach ($DataList as $key => $value) {
            if (empty($value['Name']) && empty($value['Summary']) && empty($value['Description'])) {
                unset($DataList[$key]);
            }
        }

        $DataReturn = array();

        if (!empty($DataList)) {
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
        }

        return $DataReturn;
    }

    public function GetDocumentDetail($DocID, $lang){
        $sql = "SELECT
                a.DocID
                , a.`DocUrl`
                , a.`StatusType`
                , b.`PartnerIDImplode`
                , b.`RoleAccessFarmer`
                , b.`RoleAccessStaff`
                , b.`RoleAccessTrader`
                , b.`RoleAccessRetailer`
                , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
            FROM
                `cms_document` a
                LEFT JOIN cms_access b ON a.`DocID` = b.`ObjID` AND b.`ObjType` = 'document'
            WHERE
                a.`DocID` = ?
            LIMIT 1";
        $data = $this->db->query($sql,array($DocID))->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
        $keyNew = "Koltiva.view.CMS.WinFormAnnouncement-Form-".$key;
        $DataForm[$key] = $value;
        }        

        // $DataForm['LastUpdated'] = waktu_buat_orang($DataForm['LastUpdatedUnix']);
        // $DataForm['StatusType'] = ucwords($DataForm['StatusType']);
        // $DataForm['Name'] = $this->_cekContentLanguageValue($DataForm['DocID'], 'Name', $lang);
        // $DataForm['Summary'] = $this->_cekContentLanguageValue($DataForm['DocID'], 'Summary', $lang);
        // $DataForm['Description'] = $this->_cekContentLanguageValue($DataForm['DocID'], 'Description', $lang);

        $data['LastUpdated'] = waktu_buat_orang($DataForm['LastUpdatedUnix']);
        $data['StatusType'] = ucwords($DataForm['StatusType']);
        $data['Name'] = $this->_cekContentLanguageValue($DataForm['DocID'], 'Name', $lang);
        $data['Summary'] = $this->_cekContentLanguageValue($DataForm['DocID'], 'Summary', $lang);
        $data['Description'] = $this->_cekContentLanguageValue($DataForm['DocID'], 'Description', $lang);

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    function _cekContentLanguageValue($objectID, $type, $lang){
        $this->db->select('Value');
        $this->db->where('ObjectType', 'Document');
        $this->db->where('ObjectID', $objectID);
        $this->db->where('Type', $type);
        $this->db->where('Language', $lang);
        $query = $this->db->get('cms_content');

        if($query->num_rows() == 0){
            return '';
        }
        return $query->row()->Value;
    }

    public function DeleteDocument($DocID){
        $sql = "
            UPDATE 
                cms_document a SET
                a.`StatusCode` = 'nullified',
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`DocID` = ?
            LIMIT 
                1
        ";
        $p = array(
            $_SESSION['userid'],
            $DocID
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

    public function InsertDocument($ParamPost){
        $this->db->trans_start();

        $sql = "
            INSERT INTO 
                `cms_document` 
            SET  
                `DocUrl` = ?,
                `StatusType` = ?,  
                `StatusPublish` = ?,
                `CreatedBy` = ?,
                `DateCreated` = NOW()
        ";
        $p = array(
            (isset($ParamPost['DocUrl']) ? $ParamPost['DocUrl'] : null),
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $docID = $this->db->insert_id();

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Name' || $key == 'Summary' || $key == 'Description'){
                $this->SetContentLanguage($docID, $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }

        //Update or Insert or Delete on Cms Access
        $this->InputAccess('insert', 'document', $docID, $ParamPost);

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

    public function UpdateDocument($ParamPost){
        $this->db->trans_start();

        //Update CMS Document Table First
        $sql = "
            UPDATE 
                `cms_document` a 
            SET 
                a.`DocUrl` = ?,
                a.`StatusType` = ?,
                a.StatusCode = 'active',
                a.StatusPublish = ?,
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`DocID` = ?
            LIMIT 1
        ";
        $p = array(
            (isset($ParamPost['DocUrl']) ? $ParamPost['DocUrl'] : null),
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : 'draft'),
            $_SESSION['userid'],
            $ParamPost['DocID']
        );

        $query = $this->db->query($sql,$p);

        /* dicek dulu ada data yang existing di cms_content atau engga dengan id yang sama (biar ga double datanya di cms_content ini asumsinya 1 id hanya memuat satu bahasa datanya), kalo ada datanya, dihapus dulu data yang lamanya dan diganti dengan yang baru */

        $checkExistingData = checkExistingDataCMSNew($ParamPost['DocID'], 'Document');

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Name' || $key == 'Summary' || $key == 'Description'){
                $this->SetContentLanguage($ParamPost['DocID'], $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }

        //Update or Insert or Delete on Cms Access
        $this->InputAccess('update', 'document', $ParamPost['DocID'], $ParamPost);

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
                'Document',
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
        $this->db->where('ObjectType', 'Document');
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

    public function CleanUpInactiveDocumentData(){
        $sql="SELECT
                a.`DocID`,
                a.`DocUrl`	
            FROM
                cms_document a
            WHERE
                a.`StatusCode` = 'inactive'";
        $data = $this->db->query($sql)->result_array();
        
        /*for ($i=0; $i < count($data); $i++) {
			//hapus attachmentnya            
            delete_file('images/video/'.$data[$i]['PicThumb']);
        }*/
        
        $sql = "DELETE FROM cms_document WHERE StatusCode='inactive'";
        $query = $this->db->query($sql);
    }

}
