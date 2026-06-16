<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:48:54
 * @modify date 2020-05-18 13:48:54
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Mnews extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('awsfileupload');
    }

    public function GetNewsList($page, $limit, $lang, $search = NULL){
        $return = "";
        $this->CleanUpInactiveNewsData();

        //Paging Var === (Begin)
        $StartSql = ($page - 1) * $limit;
        $LimitSql = $limit;
        //Paging Var === (End)

        $where = '';

        /* if ($search != NULL) {
            $where .= " AND c.Value LIKE '%{$search}%' "; 
        } */

        $CekAdmin = $_SESSION['is_admin'];
        if($CekAdmin == "1"){
            $sql = "SELECT
                        SQL_CALC_FOUND_ROWS
                        a.`NewsID`
                        , a.PhotoFile
                        , a.`StatusType`
                        , a.`StatusPublish`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_news a
                    INNER JOIN (
                        SELECT c.`ObjectID` AS id
                        FROM cms_content c
                        WHERE 
                            c.`Language` = ? AND c.ObjectType = ?
                        GROUP BY c.`ObjectID`
                    ) b ON b.id = a.`NewsID`
                    WHERE
                        a.`StatusCode` = 'active'
                        $where
                    ORDER BY a.`NewsID` DESC
                    LIMIT ?,?";
            $p = array(
                ucwords($lang), 'News', $StartSql, $LimitSql
            );
        }else{
            /* $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){ */
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            tbl_uni.`NewsID`
                            , tbl_uni.PhotoFile
                            , tbl_uni.`StatusType`
                            , tbl_uni.`StatusPublish` 
                            , tbl_uni.PostedBy
                            , tbl_uni.LastUpdatedUnix
                        FROM
                        (
                            SELECT	
                                a.`NewsID`
                                , a.PhotoFile
                                , a.`StatusType`
                                , a.`StatusPublish`
                                , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                                , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                            FROM
                                cms_news a
                            WHERE
                                a.`StatusCode` = 'active'
                                AND a.`StatusType` = 'public'

                            UNION
                            
                            SELECT	
                                a.`NewsID`
                                , a.PhotoFile
                                , a.`StatusType`
                                , a.`StatusPublish`
                                , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                                , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                            FROM
                                cms_news a
                                LEFT JOIN cms_access b ON a.`NewsID` = b.`ObjID` AND b.`ObjType` =  'news'
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
                        ) bc ON bc.id = tbl_uni.`NewsID`
                        ORDER BY tbl_uni.NewsID DESC LIMIT ?,?";
                $p = array(
                    $_SESSION['PartnerID'], ucwords($lang), 'News', (int) $StartSql,(int) $LimitSql
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

        if(isset($DataList[0]['NewsID'])){
            for ($i=0; $i < count($DataList); $i++) {

                if($this->awsfileupload->doesObjectExist($DataList[$i]['PhotoFile']) == true) {
                    $DataList[$i]['PhotoFile'] = $this->config->item('CTCDN')."/".$DataList[$i]['PhotoFile'];
                }else{
                    $DataList[$i]['PhotoFile'] = $DataList[$i]['PhotoFile'];
                }

                $DataList[$i]['LastUpdated'] = waktu_buat_orang($DataList[$i]['LastUpdatedUnix']);
                $DataList[$i]['StatusType'] = ucwords($DataList[$i]['StatusType']);
                $DataList[$i]['Title'] = $this->_cekContentLanguageValue($DataList[$i]['NewsID'], 'Title', $lang);
                $DataList[$i]['Summary'] = $this->_cekContentLanguageValue($DataList[$i]['NewsID'], 'Summary', $lang);
                $DataList[$i]['Content'] = $this->_cekContentLanguageValue($DataList[$i]['NewsID'], 'Content', $lang);
                $DataList[$i]['count'] = $this->db->where('ObjectID', $DataList[$i]['NewsID'])
                                                  ->where('ObjectType', 'News')
                                                  ->where('Language', $lang)
                                                  ->get('cms_content')->num_rows();
            }
        }

        foreach ($DataList as $key => $value) {
            if (empty($value['Title']) && empty($value['Summary']) && empty($value['Content'])) {
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

    public function GetNewsDetail($newsID, $lang){
        $sql = "SELECT
                    a.NewsID
                    , a.PhotoFile
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
                    `cms_news` a
                    LEFT JOIN cms_access b ON a.`NewsID` = b.`ObjID` AND b.`ObjType` = 'news'
                WHERE
                    a.`NewsID` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($newsID))->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.CMS.WinFormNews-Form-".$key;
            $DataForm[$key] = $value;
        }

        $DataForm['Title'] = $this->_cekContentLanguageValue($DataForm['NewsID'], 'Title', $lang);
        $DataForm['Summary'] = $this->_cekContentLanguageValue($DataForm['NewsID'], 'Summary', $lang);
        $DataForm['Content'] = $this->_cekContentLanguageValue($DataForm['NewsID'], 'Content', $lang);
        $DataForm['LastUpdated'] = waktu_buat_orang($DataForm['LastUpdatedUnix']);
        $DataForm['StatusType'] = ucwords($DataForm['StatusType']);
        $DataForm['Language'] = $lang;
        $DataForm['Attachment'] = $this->getNewsAttachment($DataForm['NewsID']);

        /* if($this->awsfileupload->doesObjectExist($data['PhotoFile']) == true) {
            $DataForm['PhotoFile'] = $this->config->item('CTCDN')."/".$data['PhotoFile'];
            $DataForm['Photo']     = $data['PhotoFile'];
        }else{
            $DataForm['PhotoFile'] = base_url()."uploads/news/".$data['PhotoFile'];
            $DataForm['Photo']     = "uploads/news/".$data['PhotoFile'];
        } */

        $DataForm['LastUpdated'] = waktu_buat_orang($data['LastUpdatedUnix']);
        $DataForm['StatusType'] = ucwords($data['StatusType']);

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    function _cekContentLanguageValue($objectID, $type, $lang){
        $this->db->select('Value');
        $this->db->where('ObjectType', 'News');
        $this->db->where('ObjectID', $objectID);
        $this->db->where('Type', $type);
        $this->db->where('Language', $lang);
        $query = $this->db->get('cms_content');
        if($query->num_rows() == 0){
            return '';
        }
        return $query->row()->Value;
    }

    public function InsertNews($ParamPost){
        $this->db->trans_begin();

        $sql = "
            INSERT INTO 
                `cms_news` 
            SET  
                `PhotoFile` = ?,
                `StatusType` = ?,  
                `StatusPublish` = ?,  
                `CreatedBy` = ?,
                `DateCreated` = NOW()
        ";
        $p = array(
            $ParamPost['PhotoFile'],
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $newsID = $this->db->insert_id();

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Title' || $key == 'Summary' || $key == 'Content'){
                $this->SetContentLanguage($newsID, $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }

        //Update or Insert or Delete on Cms Access
        $this->InputAccess('insert', 'news', $newsID, $ParamPost);

        if (is_array($ParamPost['Attachment'])) {
            foreach ($ParamPost['Attachment'] as $key => $value) {
                $this->UploadAttachment($value, $newsID, $_SESSION['userid'], $ParamPost['OriginalAttachmentName'][$key]);
            }      
        }

        $sendToWebCentralCMS = $this->sendToWebCentralCMS($ParamPost, $newsID);

        if ($sendToWebCentralCMS["success"] == false) {
            return [
                "success" => $sendToWebCentralCMS["success"],
                "message" => $sendToWebCentralCMS["message"]
            ];
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Saved";
        }

        return $results;
    }

    public function UpdateNews($ParamPost){
        $this->db->trans_begin();

        //Update CMS News Table First
        $sql = "
            UPDATE 
                `cms_news` a 
            SET
                a.`PhotoFile` = ?,
                a.`StatusType` = ?,
                a.StatusCode = 'active',
                a.StatusPublish = ?,
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`NewsID` = ?
            LIMIT 1
        ";
        $p = array(
            $ParamPost['PhotoFile'],
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : 'draft'),
            $_SESSION['userid'],
            $ParamPost['NewsID']
        );

        $query = $this->db->query($sql,$p);

        /* dicek dulu ada data yang existing di cms_content atau engga dengan id yang sama (biar ga double datanya di cms_content ini asumsinya 1 id hanya memuat satu bahasa datanya), kalo ada datanya, dihapus dulu data yang lamanya dan diganti dengan yang baru */

        $checkExistingData = checkExistingDataCMSNew($ParamPost['NewsID'], 'News');

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Title' || $key == 'Summary' || $key == 'Content'){
                $this->SetContentLanguage($ParamPost['NewsID'], $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }

        //Update or Insert or Delete on Cms Access
        $this->InputAccess('update', 'news', $ParamPost['NewsID'], $ParamPost);

        // Delete Attachment
        $this->DeleteAttachment($ParamPost['NewsID'], $_SESSION['userid']);

        if (is_array($ParamPost['Attachment'])) {
            foreach ($ParamPost['Attachment'] as $key => $value) {
                $this->UploadAttachment($value, $ParamPost['NewsID'], $_SESSION['userid'], $ParamPost['OriginalAttachmentName'][$key]);
            }      
        }

        $sendToWebCentralCMS = $this->sendToWebCentralCMS($ParamPost, $ParamPost['NewsID']);

        if ($sendToWebCentralCMS["success"] == false) {
            return [
                "success" => $sendToWebCentralCMS["success"],
                "message" => $sendToWebCentralCMS["message"]
            ];
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Saved";
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
                'News',
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
        $this->db->where('ObjectType', 'News');
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

    public function DeleteNews($NewsID){
        $this->db->trans_begin();

        $sql = "
            UPDATE 
                cms_news a SET
                a.`StatusCode` = 'nullified',
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`NewsID` = ?
            LIMIT 
                1
        ";
        $p = array(
            $_SESSION['userid'],
            $NewsID
        );
        $query = $this->db->query($sql,$p);

        $sendToWebCentralCMS = $this->sendToWebCentralCMS('', $NewsID, 'delete');

        if ($sendToWebCentralCMS["success"] == false) {
            return [
                "success" => $sendToWebCentralCMS["success"],
                "message" => $sendToWebCentralCMS["message"]
            ];
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Deleted";
        }

        return $results;
    }


    public function CleanUpInactiveNewsData(){
        $sql="SELECT
                a.`NewsID`,
                a.`PhotoFile`
            FROM
                cms_news a
            WHERE
                a.`StatusCode` = 'inactive'";
        $data = $this->db->query($sql)->result_array();

        for ($i=0; $i < count($data); $i++) {
            //hapus attachmentnya
            delete_file('uploads/news/'.$data[$i]['PhotoFile']);
        }

        $sql = "DELETE FROM cms_news WHERE StatusCode='inactive'";
        $query = $this->db->query($sql);
    }

    public function UploadAttachment($Attachment, $newsID, $UserID, $OriginalAttachment)
    {
        $sql = "
            INSERT INTO 
                `cms_attachment` 
            SET  
                `CmsID` = ?,
                `Filename` = ?,
                `FileNameOriginal` = ?,
                `CreatedBy` = ?,  
                `DateCreated` = NOW(),  
                `StatusCode` = ?
            ";
        $p = array(
            $newsID,
            $Attachment,
            $OriginalAttachment,
            $UserID,
            'Active'
        );

        $this->db->query($sql, $p);
    }

    public function DeleteAttachment($NewsID, $UserID)
    {
        $sql = "
            UPDATE 
                cms_attachment a SET
                a.`StatusCode` = 'nullified',
                a.`DateModified` = NOW(),
                a.`ModifiedBy` = ?
            WHERE
                a.`CmsID` = ?
            LIMIT 
                1
            ";
        $p = array(
            $UserID,
            $NewsID
        );
        $this->db->query($sql, $p);
    }

    public function DeleteAttachmentByAttachmentID($CmsAttachmentID, $UserID)
    {
        $sql = "
                    UPDATE 
                        cms_attachment a SET
                        a.`StatusCode` = 'nullified',
                        a.`DateModified` = NOW(),
                        a.`ModifiedBy` = ?
                    WHERE
                        a.`CmsAttachmentID` = ?
                    LIMIT 
                        1
            ";

        $p = array(
            $UserID,
            $CmsAttachmentID
        );

        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data Deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }

        return $results;
    }

    public function getNewsAttachment($NewsID)
    {
        $query = $this->db->get_where('cms_attachment', ['CmsID' => $NewsID, 'StatusCode' => 'Active']);
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            foreach ($data as $key => $value) {
                $type = $this->awsfileupload->getTypeOfFile($value['Filename']);
                $data[$key]['FileURL'] = $this->config->item('CTCDN') . '/' . $value['Filename'];
            }
            return $data;
        }
        return false;
    }

    private function sendToWebCentralCMS($param = '', $newsID, $flag = '') {
        if ($this->config->item('send_to_db_central_cms') != '') {
            if ($this->config->item('send_to_db_central_cms') == false) {
                return [
                    "success" => true,
                    "message" => "not sending to db central"
                ];
            }
        } else {
            return [
                "success" => true,
                "message" => "not sending to db central"
            ];
        }

        $this->load->config('common');
        if ($this->config->item('CTEnv') == "demo" || $this->config->item('CTEnv') == "devel") {
            $hostname = $this->config->item('web-farmer-backend-devel');
        }
        else{
            $hostname = $this->config->item('web-farmer-backend-prod');
        }

        $userNameToWebCentral = "KoltivaUsers";
        $passwordToWebCentral = "Koltiva2022!";
        $urlSendData          = $hostname."/webapi/cms/news/submit";
        $cdnUrl               = $this->config->item('CTCDN').'/';
        $configUserPassword   = $userNameToWebCentral . ':' . $passwordToWebCentral;
        $PartnerIDImplode     = isset($param['PartnerIDImplode']) ? $param['PartnerIDImplode'] : null;

        $RoleAccessFarmer    = null;
        $RoleAccessTrader    = null;
        $RoleAccessStaff     = null;
        $RoleAccessRetailer  = null;

        if (!empty($param)) {
            if ($param["StatusType"] == "Private") {
                if (isset($param["RoleAccessFarmer"])) {
                    $RoleAccessFarmer = 1;
                }

                if (isset($param["RoleAccessTrader"])) {
                    $RoleAccessTrader = 1;
                }

                if (isset($param["RoleAccessStaff"])) {
                    $RoleAccessStaff = 1;
                }

                if (isset($param["RoleAccessRetailer"])) {
                    $RoleAccessRetailer = 1;
                }
            }

            $dataSendToCurl = [
                "PlatformCode"       => 121,
                "NewsTraceID"        => $newsID,
                "PhotoFile"          => $cdnUrl.$param['PhotoFile'],
                "StatusType"         => $param['StatusType'],
                "StatusPublish"      => $param['StatusPublish'],
                "UserID"             => $_SESSION['userid'],
                "TransactionDate"    => date('Y-m-d'),
                "Title"              => $param['Title'],
                "Summary"            => $param['Summary'],
                "Content"            => $param['Content'],
                "PartnerIDImplode"   => $PartnerIDImplode,
                "RoleAccessFarmer"   => $RoleAccessFarmer,
                "RoleAccessTrader"   => $RoleAccessTrader,
                "RoleAccessStaff"    => $RoleAccessStaff,
                "RoleAccessRetailer" => $RoleAccessRetailer,
                "Language"           => $param['Language']
            ];
        }

        if ($flag == 'delete') {
            $urlSendData         = $hostname."/webapi/cms/news/delete";
            $dataSendToCurl = [
                "PlatformCode"   => 121,
                "NewsTraceID"    => $newsID
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $urlSendData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($dataSendToCurl),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json'
            ],
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $configUserPassword,
            CURLOPT_FAILONERROR    => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0
        ]);
        
        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        $returnSuccess   = json_decode($response)->success;
        $returnMessage   = json_decode($response)->error_message;

        if ($checkError > 0) {
            $returnSuccess = false;
            $returnMessage = $checkErrorMsg;
        }

        return [
            "success"    => $returnSuccess,
            "message"    => lang($returnMessage)
        ];
    }
}