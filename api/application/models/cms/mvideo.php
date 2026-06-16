<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:48:54
 * @modify date 2020-05-18 13:48:54
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Mvideo extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetVideoList($page, $limit, $lang){
        $return = "";
        $this->CleanUpInactiveVideoData();

        //Paging Var === (Begin)
        $StartSql = ($page - 1) * $limit;
        $LimitSql = $limit;
        //Paging Var === (End)

        $CekAdmin = $_SESSION['is_admin'];

        if($CekAdmin == "1"){
            $sql = "SELECT
                        SQL_CALC_FOUND_ROWS
                        a.`VidID`
                        , a.`VideoType`
                        , a.`VideoTypeID`
                        , a.`VideoUrl`
                        , a.PicThumb
                        , a.`StatusType`
                        , a.`StatusPublish`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_video a
                    INNER JOIN (
                        SELECT c.`ObjectID` AS id
                        FROM cms_content c
                        WHERE 
                            c.`Language` = ? AND c.ObjectType = ?
                        GROUP BY c.`ObjectID`
                    ) b ON b.id = a.`VidID`
                    WHERE
                        a.`StatusCode` = 'active'
                    ORDER BY a.`VidID` DESC
                    LIMIT ?,?";
            $p = array(
                ucwords($lang), 'Video', (int) $StartSql,(int) $LimitSql
            );
        }else{
            /* $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){ */
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            tbl_uni.`VidID`
                            , tbl_uni.`VideoType`
                            , tbl_uni.`VideoTypeID`
                            , tbl_uni.`VideoUrl`
                            , tbl_uni.PicThumb
                            , tbl_uni.`StatusType`
                            , tbl_uni.`StatusPublish`
                            , tbl_uni.PostedBy
                            , tbl_uni.LastUpdatedUnix
                        FROM
                        (
                        SELECT  
                            a.`VidID`
                            , a.`VideoType`
                            , a.`VideoTypeID`
                            , a.`VideoUrl`
                            , a.PicThumb
                            , a.`StatusType`
                            , a.`StatusPublish`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                        FROM
                            cms_video a
                        WHERE
                            a.`StatusCode` = 'active'
                            
                            
                        UNION
                        
                        SELECT  
                            a.`VidID`
                            , a.`VideoType`
                            , a.`VideoTypeID`
                            , a.`VideoUrl`
                            , a.PicThumb
                            , a.`StatusType`
                            , a.`StatusPublish`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                        FROM
                            cms_video a
                            LEFT JOIN cms_access b ON a.`VidID` = b.`ObjID` AND b.`ObjType` =  'video'
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
                        ) bc ON bc.id = tbl_uni.`VidID`
                        ORDER BY tbl_uni.VidID DESC LIMIT ?,?";
                $p = array(
                    $DataUser['PartnerID'], ucwords($lang), 'Video', (int) $StartSql,(int) $LimitSql
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

        if(isset($DataList[0]['VidID'])){            
            for ($i=0; $i < count($DataList); $i++) {
                $DataList[$i]['LastUpdated'] = waktu_buat_orang($DataList[$i]['LastUpdatedUnix']);
                $DataList[$i]['StatusType'] = ucwords($DataList[$i]['StatusType']);
                $DataList[$i]['Title'] = $this->_cekContentLanguageValue($DataList[$i]['VidID'], 'Title', $lang);
                $DataList[$i]['Summary'] = $this->_cekContentLanguageValue($DataList[$i]['VidID'], 'Summary', $lang);
                $DataList[$i]['Description'] = $this->_cekContentLanguageValue($DataList[$i]['VidID'], 'Description', $lang);
            }
        }

        foreach ($DataList as $key => $value) {
            if (empty($value['Title']) && empty($value['Summary']) && empty($value['Description'])) {
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

    public function GetVideoByID($VidID, $lang){
        $sql = "SELECT
                    a.`VidID`
                    , a.`PicThumb`
                    , a.`VideoType`
                    , a.`VideoTypeID`
                    , a.`VideoUrl`
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
                    cms_video a
                    LEFT JOIN cms_access b ON a.`VidID` = b.`ObjID` AND b.`ObjType` = 'video'
                WHERE
                    a.`VidID` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($VidID))->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            //$keyNew = "Koltiva.view.CMS.WinFormVideo-Form-".$key;
            $DataForm[$key] = $value;
        }

        $DataForm['Title'] = $this->_cekContentLanguageValue($DataForm['VidID'], 'Title', $lang);
        $DataForm['Summary'] = $this->_cekContentLanguageValue($DataForm['VidID'], 'Summary', $lang);
        $DataForm['Description'] = $this->_cekContentLanguageValue($DataForm['VidID'], 'Description', $lang);
        $DataForm['LastUpdated'] = waktu_buat_orang($DataForm['LastUpdatedUnix']);
        $DataForm['StatusType'] = ucwords($DataForm['StatusType']);
        $DataForm['Language'] = $lang;

        /*if($this->awsfileupload->doesObjectExist($data['PicThumb']) == true) {
            $DataForm['PicThumb'] = $this->config->item('CTCDN')."/".$data['PicThumb'];
        }else{
            $DataForm['PicThumb'] = base_url()."images/video/".$data['PicThumb'];
        }*/

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function InsertVideo($ParamPost){
        $this->db->trans_start();

        $sql = "
            INSERT INTO 
                `cms_video` 
            SET  
                `PicThumb` = ?,
                `VideoType` = ?,
                `VideoTypeID` = ?,
                `VideoUrl` = ?,
                `StatusType` = ?,  
                `StatusPublish` = ?,  
                `CreatedBy` = ?,
                `DateCreated` = NOW()
        ";
        $p = array( 
            (isset($ParamPost['PicThumb']) ? $ParamPost['PicThumb'] : null),
            (isset($ParamPost['VideoType']) ? $ParamPost['VideoType'] : null),
            (isset($ParamPost['VideoTypeID']) ? $ParamPost['VideoTypeID'] : null),
            (isset($ParamPost['VideoUrl']) ? $ParamPost['VideoUrl'] : null),   
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $videoID = $this->db->insert_id();

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Title' || $key == 'Summary' || $key == 'Description'){
                $this->SetContentLanguage($videoID, $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }

        //Update or Insert or Delete on Cms Access
        $this->InputAccess('insert', 'video', $videoID, $ParamPost);

        // check double insert, jika ada double langsung dihapus yang double yang ga sesuai sama bahasanya
        // ini untuk pengecekan karena ketika insert video baru selalu double datanya di cms_content , misal buat untuk bahasa indonesia entah kenapa otomatis ke bahasa inggris

        $checkDoubleData = $this->db->where('ObjectType', 'Video')
                                    ->where('ObjectID', $videoID)
                                    ->where('Language !=', trim($ParamPost['Language']))
                                    ->get('cms_content')->result();

        if (!empty($checkDoubleData)) {
            $this->db->delete('cms_content', [
                'ObjectType'  => 'Video',
                'ObjectID'    => $videoID,
                'Language !=' => trim($ParamPost['Language'])
            ]);
        }

        $sendToWebCentralCMS = $this->sendToWebCentralCMS($ParamPost, $videoID);

        if ($sendToWebCentralCMS["success"] == false) {
            return [
                "success" => $sendToWebCentralCMS["success"],
                "message" => $sendToWebCentralCMS["message"]
            ];
        }

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

    public function UpdateVideo($ParamPost){
        $this->db->trans_start();

        //Update CMS News Table First
        $sql = "
            UPDATE 
                `cms_video` a 
            SET  
                a.`PicThumb` = ?,
                a.`VideoType`= ?,
                a.`VideoTypeID` = ?,
                a.`VideoUrl` = ?,
                a.`StatusType` = ?,
                a.StatusCode = 'active',
                a.StatusPublish = ?,
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`VidID` = ?
            LIMIT 1
        ";
        $p = array(
            (isset($ParamPost['PicThumb']) ? $ParamPost['PicThumb'] : null),
            (isset($ParamPost['VideoType']) ? $ParamPost['VideoType'] : null),
            (isset($ParamPost['VideoTypeID']) ? $ParamPost['VideoTypeID'] : null),
            (isset($ParamPost['VideoUrl']) ? $ParamPost['VideoUrl'] : null), 
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            (isset($ParamPost['StatusPublish']) ? $ParamPost['StatusPublish'] : 'draft'),
            $_SESSION['userid'],
            $ParamPost['VidID']
        );

        $query = $this->db->query($sql,$p);

        /* dicek dulu ada data yang existing di cms_content atau engga dengan id yang sama (biar ga double datanya di cms_content ini asumsinya 1 id hanya memuat satu bahasa datanya), kalo ada datanya, dihapus dulu data yang lamanya dan diganti dengan yang baru */

        $checkExistingData = checkExistingDataCMSNew($ParamPost['VidID'], 'Video');

        //Update or Insert Language Content
        foreach($ParamPost as $key => $value){
            if($key == 'Title' || $key == 'Summary' || $key == 'Description'){
                $this->SetContentLanguage($ParamPost['VidID'], $key, $ParamPost['Language'], $ParamPost[$key]);
            }
        }
        
        //Update or Insert or Delete on Cms Access
        $this->InputAccess('update', 'video', $ParamPost['VidID'], $ParamPost);

        $sendToWebCentralCMS = $this->sendToWebCentralCMS($ParamPost, $ParamPost['VidID']);

        if ($sendToWebCentralCMS["success"] == false) {
            return [
                "success" => $sendToWebCentralCMS["success"],
                "message" => $sendToWebCentralCMS["message"]
            ];
        }        

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
                INSERT INTO 
                    `cms_content` 
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
                'Video',
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

    function _cekContentLanguageValue($objectID, $type, $lang){
        $this->db->select('Value');
        $this->db->where('ObjectType', 'Video');
        $this->db->where('ObjectID', $objectID);
        $this->db->where('Type', $type);
        $this->db->where('Language', $lang);
        $query = $this->db->get('cms_content');
        if($query->num_rows() == 0){
            return '';
        }
        return $query->row()->Value;
    }

    function _getContentLanguageID($objectID, $type, $lang){
        $this->db->where('ObjectType', 'Video');
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

    public function DeleteVideo($VidID){
        $this->db->trans_start();
        
        $sql = "
            UPDATE cms_video a SET
                a.`StatusCode` = 'nullified',
                a.`DateUpdated` = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`VidID` = ?
            LIMIT 
                1
        ";
        $p = array(
            $_SESSION['userid'],
            $VidID
        );
        $query = $this->db->query($sql,$p);

        $sendToWebCentralCMS = $this->sendToWebCentralCMS('', $VidID, 'delete');

        if ($sendToWebCentralCMS["success"] == false) {
            return [
                "success" => $sendToWebCentralCMS["success"],
                "message" => $sendToWebCentralCMS["message"]
            ];
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data Deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }

        return $results;
    }

    public function CleanUpInactiveVideoData(){
        $sql="SELECT
                a.`VidID`,
                a.`PicThumb`	
            FROM
                cms_video a
            WHERE
                a.`StatusCode` = 'inactive'";
        $data = $this->db->query($sql)->result_array();
        
        for ($i=0; $i < count($data); $i++) {
			//hapus attachmentnya            
            delete_file('images/video/'.$data[$i]['PicThumb']);
        }
        
        $sql = "DELETE FROM cms_video WHERE StatusCode='inactive'";
        $query = $this->db->query($sql);
    }

    private function sendToWebCentralCMS($param = '', $videoID, $flag = '') {
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
        $urlSendData          = $hostname."/webapi/cms/video/submit";
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
                "VidTraceID"         => $videoID,
                "PicThumb"           => $param['PicThumb'],
                "VideoType"          => $param['VideoType'],
                "VideoTypeID"        => $param['VideoTypeID'],
                "VideoUrl"           => $param['VideoUrl'],
                "StatusType"         => $param['StatusType'],
                "StatusPublish"      => $param['StatusPublish'],
                "PlatformCode"       => 121,
                "TransactionDate"    => date('Y-m-d'),
                "UserID"             => $_SESSION['userid'],
                "Language"           => $param['Language'],
                "Title"              => $param['Title'],
                "Summary"            => $param['Summary'],
                "Description"        => $param['Description'],
                "PartnerIDImplode"   => $PartnerIDImplode,
                "RoleAccessFarmer"   => $RoleAccessFarmer,
                "RoleAccessTrader"   => $RoleAccessTrader,
                "RoleAccessStaff"    => $RoleAccessStaff,
                "RoleAccessRetailer" => $RoleAccessRetailer
            ];
        }

        if ($flag == 'delete') {
            $urlSendData         = $hostname."/webapi/cms/video/delete";
            $dataSendToCurl = [
                "PlatformCode"   => 121,
                "VidTraceID"     => $videoID
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