<?php
/******************************************
 *  Author : Fashah Darullah   
 *  Created On : Wed Aug 14 2019
 *  File : mmessages.php
 *******************************************/

defined('BASEPATH') or exit('No direct script access allowed');

class Mmessages extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetGridMainMessages($start, $limit){
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`MessagesID`
                    , a.`Title`
                    , a.`Content`
                    , a.`StatusType`
                    , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS CreatedBy
                    , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
                FROM
                    farmcloud_messages a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`MessagesID` DESC
                LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );

        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

    	return $result;
    }

    public function InputAccess($OpsiProses,$ObjType,$ObjID,$ParamPost){
        if($OpsiProses == 'insert'){
            if($ParamPost['StatusType'] == "private"){
                $sql = "INSERT INTO `cms_access` SET
                        `ObjType` = ?,
                        `ObjID` = ?,
                        `PartnerIDImplode` = ?,  
                        RoleAccessFarmer = ?,
                        RoleAccessTrader = ?,
                        RoleAccessStaff = ?,
                        `DateGenerated` = NOW(),
                        `GeneratedBy` = ?";
                $p = array(
                    $ObjType,
                    $ObjID,
                    (isset($ParamPost['PartnerIDImplode']) ? $ParamPost['PartnerIDImplode'] : null),
                    (isset($ParamPost['RoleAccessFarmer']) ? $ParamPost['RoleAccessFarmer'] : null),
                    (isset($ParamPost['RoleAccessTrader']) ? $ParamPost['RoleAccessTrader'] : null),
                    (isset($ParamPost['RoleAccessStaff']) ? $ParamPost['RoleAccessStaff'] : null),
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
            if($ParamPost['StatusType'] == "individu"){    
                $ContactID  = json_decode($ParamPost["ContactID"]);
                $proses = array();
                if($ContactID){
                    foreach($ContactID as $key => $value){
                        $proses[] = array(
                            "MessagesID" => $ObjID,
                            "PersonName" => $value->PersonName,
                            "ObjType"    => $ObjType,
                            "ContactID"  => $value->PersonExtID,
                            "Email"      => $value->Email,
                            "CreatedBy" => $_SESSION['userid']
                        );
                    }
                    $this->db->insert_batch('farmcloud_messages_recipient_list', $proses);
                }
            }
        }

        if($OpsiProses == 'update'){
            if($ParamPost['StatusType'] == "private"){
                $sql = "INSERT INTO `cms_access` (
                            ObjType,
                            ObjID,
                            PartnerIDImplode,
                            RoleAccessFarmer,
                            RoleAccessTrader,
                            RoleAccessStaff,
                            DateGenerated,
                            GeneratedBy
                        )
                        VALUES (
                            ?,?,?,?,?,?,NOW(),?
                        )
                        ON DUPLICATE KEY UPDATE
                            PartnerIDImplode = ?,
                            RoleAccessFarmer = ?,
                            RoleAccessTrader = ?,
                            RoleAccessStaff = ?,
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
                    $_SESSION['userid'],
    
                    (isset($ParamPost['PartnerIDImplode']) ? $ParamPost['PartnerIDImplode'] : null),
                    (isset($ParamPost['RoleAccessFarmer']) ? $ParamPost['RoleAccessFarmer'] : null),
                    (isset($ParamPost['RoleAccessTrader']) ? $ParamPost['RoleAccessTrader'] : null),
                    (isset($ParamPost['RoleAccessStaff']) ? $ParamPost['RoleAccessStaff'] : null),
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }else if($ParamPost['StatusType'] == "individu"){   
                $this->db->where("MessagesID",$ObjID);
                $this->db->delete("farmcloud_messages_recipient_list");                    
                $ContactID  = json_decode($ParamPost["ContactID"]);
                $proses = array();
                if($ContactID){
                    foreach($ContactID as $key => $value){
                        $proses[] = array(
                            "MessagesID" => $ObjID,
                            "PersonName" => $value->PersonName,
                            "ObjType"    => $ObjType,
                            "ContactID"  => $value->PersonExtID,
                            "Email"      => $value->Email,
                            "CreatedBy" => $_SESSION['userid']
                        );
                    }
                    $this->db->insert_batch('farmcloud_messages_recipient_list', $proses);
                }
            }else{
                $sql = "DELETE FROM cms_access WHERE ObjType = ? AND ObjID = ? LIMIT 1";
                $p = array(
                    $ObjType,
                    $ObjID
                );
                $query = $this->db->query($sql,$p);
            }
        }
    }

    function getContactListMessages($MessagesID){
        $sql = "
        SELECT
            a.ContactID as PersonExtID,
            a.PersonName,
            a.Email,
            c.PartnerName GroupName
        FROM 
            `farmcloud_messages_recipient_list` a
        LEFT JOIN  
            ktv_members b on b.MemberID = a.ContactID
        LEFT JOIN
            ktv_program_partner c on c.PartnerID = b.PartnerID
        WHERE 
            MessagesID = ?
        ";
        $data = $this->db->query($sql,array($MessagesID))->result_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $DataForm[$key] = $value;
        }        

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function getMessages($MessagesID){
        $sql = "SELECT
                    a.MessagesID
                    , a.`Title`
                    , a.`Content` as `ContentEncode`
                    , a.`StatusType`
                    , b.`PartnerIDImplode`
                    , b.`RoleAccessFarmer`
                    , b.`RoleAccessStaff`
                    , b.`RoleAccessTrader`
                FROM
                    `farmcloud_messages` a
                    LEFT JOIN cms_access b ON a.`MessagesID` = b.`ObjID` AND b.`ObjType` = 'messages'
                WHERE
                    a.`MessagesID` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($MessagesID))->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $DataForm[$key] = $value;
        }        

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function InsertMessages($ParamPost){
        $this->db->trans_start();

        $sql = "INSERT INTO `farmcloud_messages` SET  
                `Title` = ?,
                `Content` = ?,
                `StatusType` = ?,  
                `CreatedBy` = ?,
                `DateCreated` = NOW()";
        $p = array(            
            (isset($ParamPost['Title']) ? $ParamPost['Title'] : null),
            (isset($ParamPost['ContentCKEditor']) ? $ParamPost['ContentCKEditor'] : null),
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $MessagesID = $this->db->insert_id();

        $ProsesAccess = $this->InputAccess('insert','messages',$MessagesID,$ParamPost);

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

    public function UpdateMessages($ParamPost){
        $this->db->trans_start();

        $sql = "UPDATE `farmcloud_messages` a SET
                    a.Title = ?,
                    a.Content = ?,
                    a.`StatusType` = ?,
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`MessagesID` = ?
                LIMIT 1";
        $p = array(
            (isset($ParamPost['Title']) ? $ParamPost['Title'] : null),
            (isset($ParamPost['ContentCKEditor']) ? $ParamPost['ContentCKEditor'] : null),
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            $_SESSION['userid'],
            $ParamPost['MessagesID']
        );
        $query = $this->db->query($sql,$p);

        $ProsesAccess = $this->InputAccess('update','messages',$ParamPost['MessagesID'],$ParamPost);        

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

    public function DeleteMessages($MessagesID){
        $sql = "UPDATE farmcloud_messages a SET
                    a.`StatusCode` = 'nullified',
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`MessagesID` = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $MessagesID
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

    public function GetVideoByID($VidID){
        $sql = "SELECT
                    a.`VidID`
                    , a.`Title`
                    , a.`Description`
                    , a.`VideoUrl`
                    , a.`StatusType`	
                FROM
                    cms_video a
                WHERE
                    a.`VidID` = ?
                LIMIT 1";
        return $this->db->query($sql,array($VidID))->row_array();
    }

    public function GetVideoFormOpen($VidID){
        $sql = "SELECT
                    a.`VidID`
                    , a.`Title`
                    , a.`Description`
                    , a.`PicThumb`
                    , a.`VideoUrl`
                    , a.`StatusType`
                    , b.`PartnerIDImplode`
                    , b.`RoleAccessFarmer`
                    , b.`RoleAccessStaff`
                    , b.`RoleAccessTrader`
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
            $keyNew = "Koltiva.view.CMS.WinFormVideo-Form-".$key;
            $DataForm[$keyNew] = $value;
        }
        
        //Untuk Js
        $DataForm['PicThumb'] = $data['PicThumb'];

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function GetVideoContent($page,$limit){
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
                        , a.`Title`
                        , a.`Description`
                        , a.`VideoUrl`
                        , a.PicThumb
                        , a.`StatusType`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_video a
                    WHERE
                        a.`StatusCode` = 'active'
                    ORDER BY a.`VidID` DESC
                    LIMIT ?,?";
            $p = array(
                (int) $StartSql,(int) $LimitSql
            );
        }else{
            $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            tbl_uni.`VidID`
                            , tbl_uni.`Title`
                            , tbl_uni.`Description`
                            , tbl_uni.`VideoUrl`
                            , tbl_uni.PicThumb
                            , tbl_uni.`StatusType`
                            , tbl_uni.PostedBy
                            , tbl_uni.LastUpdatedUnix
                        FROM
                        (
                        SELECT	
                            a.`VidID`
                            , a.`Title`
                            , a.`Description`
                            , a.`VideoUrl`
                            , a.PicThumb
                            , a.`StatusType`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                        FROM
                            cms_video a
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`StatusType` = 'public'
                            
                        UNION
                        
                        SELECT	
                            a.`VidID`
                            , a.`Title`
                            , a.`Description`
                            , a.`VideoUrl`
                            , a.PicThumb
                            , a.`StatusType`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
                        FROM
                            cms_video a
                            LEFT JOIN cms_access b ON a.`VidID` = b.`ObjID` AND b.`ObjType` =  'video'
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`StatusType` = 'private'
                            AND b.`RoleAccessStaff` = '1'
                            AND FIND_IN_SET(?, b.`PartnerIDImplode`)
                        ) AS tbl_uni
                        ORDER BY tbl_uni.VidID DESC LIMIT ?,?";
                $p = array(
                    $DataUser['PartnerID'],(int) $StartSql,(int) $LimitSql
                );
            }else{
                return array();
            }
        }
        
        $DataList = $this->db->query($sql,$p)->result_array();
        if(isset($DataList[0]['VidID'])){            
            for ($i=0; $i < count($DataList); $i++) {
                $DataList[$i]['LastUpdated'] = waktu_buat_orang($DataList[$i]['LastUpdatedUnix']);
                $DataList[$i]['StatusType'] = ucwords($DataList[$i]['StatusType']);
            }
        }

        //Hitung Total Pages
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $TotalData = $query->row()->total;
        $TotalPage = ceil($TotalData / $LimitSql);


        //Generate HTML untuk Content ============= (Begin)

        //Prev Button
        if($page < $TotalPage){
            $NextButton = '<button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainVideo\').NextVideo()" style="float:right;" type="button" class="btn btn-space btn-info btn-rounded"><i class="icon icon-left s7-angle-right-circle"></i>&nbsp;&nbsp;'.lang('Next Video').'</button><input id="Koltiva.view.CMS.GridMainVideo-NextPageInfo" type="hidden" value="'.($page+1).'" />';
        }else{
            $NextButton = '';
        }

        //Prev Button
        if($page > 1){
            $PrevButton = '<button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainVideo\').PrevVideo()" style="float:right;" type="button" class="btn btn-space btn-info btn-rounded"><i class="icon icon-left s7-angle-left-circle"></i>&nbsp;&nbsp;'.lang('Previous Video').'</button><input id="Koltiva.view.CMS.GridMainVideo-PrevPageInfo" type="hidden" value="'.($page-1).'" />';
        }else{
            $PrevButton = '';
        }


        //Header Button
        $return = '
        <div style="margin-bottom:15px;margin-left: 0px;margin-right: 0px;" class="row">        
            <button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainVideo\').NewVideo()" style="float:left;" type="button" class="btn btn-space btn-success btn-rounded Koltiva.view.CMS.GridMainVideo-BtnAdd"><i class="icon icon-left s7-video"></i>&nbsp;&nbsp;'.lang('New Video').'</button>'.$NextButton.$PrevButton.'</div><div class="row">';
        
        //Data Video === (Begin)
        if(isset($DataList[0]['VidID'])){
            for ($i=0; $i < count($DataList); $i++) {              
                
                //cek apakah ada file PicThubmnya
                if(file_exists('images/video/'.$DataList[$i]['PicThumb'])){
                    $PicThumbUrl = base_url().'/images/video/'.$DataList[$i]['PicThumb'].'?'.rand(1,100);
                }else{
                    $PicThumbUrl = base_url().'/images/video/thumb-defa.png';
                }

                $return .= '<div class="col-md-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <span style="font-size:12px;font-weight:bold;" class="title">'.$DataList[$i]['Title'].'</span>					
                        </div>
                        <div class="panel-body">
                            <div class="cms_video_overlay" style="width:320px;height:240px;">
                                <img width="320" height="240" src="'.$PicThumbUrl.'" class="cms_video_thumbnail" />
                                <a href="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainVideo\').PlayVideo('.$DataList[$i]['VidID'].')" title="Play Video" class="cms_video_playWrapper"><img src="'.base_url().'/images/play-button.png" width="50" height="50" alt=""></a>
                            </div>
                            <p class="cms_video_info"><span style="float:left;">'.lang('Posted by').' <b>'.$DataList[$i]['PostedBy'].'</b> ('.$DataList[$i]['LastUpdated'].')</span><span style="float:right;">Status: '.$DataList[$i]['StatusType'].'</span></p>
                            <div style="clear:both;margin-bottom:7px;"></div>
                            
                            <div>'.$DataList[$i]['Description'].'</div>

                            <p style="text-align:right;margin-top:10px;">
                                <button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainVideo\').UpdateVideo('.$DataList[$i]['VidID'].')" type="button" class="btn btn-space btn-success btn-rounded btn-xs Koltiva.view.CMS.GridMainVideo-BtnUpdate"><i class="icon icon-left s7-note"></i> '.lang('Update').'</button>
                                <button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainVideo\').DeleteVideo('.$DataList[$i]['VidID'].')" type="button" class="btn btn-space btn-danger btn-rounded btn-xs Koltiva.view.CMS.GridMainVideo-BtnDelete"><i class="icon icon-left s7-trash"></i> '.lang('Delete').'</button>
                            </p>
                        </div>
                    </div>
                </div>';

                //Ganti Baris
                if(($i+1) % 3 == 0){
                    $return .= '</div><div class="row">';
                }
            }
        }else{            
            $return .= '<div class="DivCustomContentHtmlNoData"><h3 align="center">'.lang('No Video Available').'</h3></div>';
        }
        //Data Video === (End)

        //Footer Button
        $return .= '</div><div style="margin-top:15px;margin-left: 0px;margin-right: 0px;" class="row">'.$NextButton.$PrevButton.'</div>';

        //Generate HTML untuk Content ============= (End)        

        return $return;
    }

    public function GetVideoInputPrep(){
        $sql = "INSERT INTO `cms_video` SET  
                `Title` = 'temp',
                `StatusCode` = 'inactive'";
        $query = $this->db->query($sql);
        $VidID = $this->db->insert_id();

        $return['success'] = true;
        $return['VidID'] = $VidID;
        return $return;
    }

    public function UpdateVideoPhotoThumb($VidID, $gambar){
        $sql = "UPDATE cms_video a SET
                    a.`PicThumb` = ?,
                    a.`LastModifiedBy` = ?,
                    a.`DateUpdated` = NOW()	
                WHERE
                    a.`VidID` = ?
                LIMIT 1";
        $p = array(
            $gambar,
            $_SESSION['userid'],
            $VidID
        );
        $query = $this->db->query($sql,$p);
    }

    public function UpdateVideo($ParamPost){
        $this->db->trans_start();

        if($ParamPost['OpsiDisplay'] == 'insert'){
            $sqltime = ", `CreatedBy` = '{$_SESSION['userid']}'
                        , `DateCreated` = NOW()";
        }else{
            $sqltime = ", `LastModifiedBy` = '{$_SESSION['userid']}'
                        , `DateUpdated` = NOW()";
        }

        $sql = "UPDATE `cms_video` SET  
                    `Title` = ?,
                    `Description` = ?,  
                    `VideoUrl` = ?,
                    `StatusType` = ?,
                    `StatusCode` = 'active'
                    $sqltime
                WHERE
                    `VidID` = ?
                LIMIT 1";        
        $p = array(
            $ParamPost['Title'],            
            (isset($ParamPost['Description']) ? $ParamPost['Description'] : null),
            $ParamPost['VideoUrl'],
            $ParamPost['StatusType'],
            $ParamPost['VidID']
        );
        $query = $this->db->query($sql,$p);

        $ProsesAccess = $this->InputAccess($ParamPost['OpsiDisplay'],'video',$ParamPost['VidID'],$ParamPost);

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

    public function DeleteVideo($VidID){
        $sql = "UPDATE cms_video a SET
                    a.`StatusCode` = 'nullified',
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`VidID` = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $VidID
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

    public function GetNewsFormOpen($NewsID){
        $sql = "SELECT
                    a.NewsID
                    , a.`Title`
                    , a.`Content`
                    , a.PhotoFile
                    , a.`StatusType`
                    , b.`PartnerIDImplode`
                    , b.`RoleAccessFarmer`
                    , b.`RoleAccessStaff`
                    , b.`RoleAccessTrader`
                FROM
                    `cms_news` a
                    LEFT JOIN cms_access b ON a.`NewsID` = b.`ObjID` AND b.`ObjType` = 'news'
                WHERE
                    a.`NewsID` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($NewsID))->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.CMS.WinFormNews-Form-".$key;
            $DataForm[$keyNew] = $value;
        }        

        $DataForm['PhotoFile'] = $data['PhotoFile'];

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function GetNewsContent($page,$limit){
        $return = "";
        $this->CleanUpInactiveNewsData();

        //Paging Var === (Begin)
        $StartSql = ($page - 1) * $limit;
        $LimitSql = $limit;
        //Paging Var === (End)

        $CekAdmin = $_SESSION['is_admin'];
        if($CekAdmin == "1"){
            $sql = "SELECT
                        SQL_CALC_FOUND_ROWS
                        a.`NewsID`
                        , a.`Title`
                        , a.PhotoFile
                        , a.`Content`
                        , a.`StatusType`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , UNIX_TIMESTAMP(IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`)) AS LastUpdatedUnix
                    FROM
                        cms_news a
                    WHERE
                        a.`StatusCode` = 'active'
                    ORDER BY a.`NewsID` DESC
                    LIMIT ?,?";
            $p = array(
                $StartSql,$LimitSql
            );
        }else{
            $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            tbl_uni.`NewsID`
                            , tbl_uni.`Title`
                            , tbl_uni.`Content`
                            , tbl_uni.PhotoFile
                            , tbl_uni.`StatusType`
                            , tbl_uni.PostedBy
                            , tbl_uni.LastUpdatedUnix
                        FROM
                        (
                        SELECT	
                            a.`NewsID`
                            , a.`Title`
                            , a.PhotoFile
                            , a.`Content`
                            , a.`StatusType`
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
                            , a.`Title`
                            , a.PhotoFile
                            , a.`Content`
                            , a.`StatusType`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
                        FROM
                            cms_news a
                            LEFT JOIN cms_access b ON a.`NewsID` = b.`ObjID` AND b.`ObjType` =  'news'
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`StatusType` = 'private'
                            AND b.`RoleAccessStaff` = '1'
                            AND FIND_IN_SET(?, b.`PartnerIDImplode`)
                        ) AS tbl_uni
                        ORDER BY tbl_uni.NewsID DESC LIMIT ?,?";
                $p = array(
                    $DataUser['PartnerID'],(int) $StartSql,(int) $LimitSql
                );
            }else{
                return array();
            }
        }

        $DataList = $this->db->query($sql,$p)->result_array();
        if(isset($DataList[0]['NewsID'])){            
            for ($i=0; $i < count($DataList); $i++) {
                $DataList[$i]['LastUpdated'] = waktu_buat_orang($DataList[$i]['LastUpdatedUnix']);
                $DataList[$i]['StatusType'] = ucwords($DataList[$i]['StatusType']);
            }
        }

        //Hitung Total Pages
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $TotalData = $query->row()->total;
        $TotalPage = ceil($TotalData / $LimitSql);

        //Generate HTML untuk Content =================================== (Begin)

        //Next Button
        if($page < $TotalPage){
            $NextButton = '<button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainNews\').NextNews()" style="float:right;" type="button" class="btn btn-space btn-info btn-rounded"><i class="icon icon-left s7-angle-right-circle"></i>&nbsp;&nbsp;'.lang('Next News').'</button><input id="Koltiva.view.CMS.GridMainNews-NextPageInfo" type="hidden" value="'.($page+1).'" />';
        }else{
            $NextButton = '';
        }

        //Prev Button
        if($page > 1){
            $PrevButton = '<button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainNews\').PrevNews()" style="float:right;" type="button" class="btn btn-space btn-info btn-rounded"><i class="icon icon-left s7-angle-left-circle"></i>&nbsp;&nbsp;'.lang('Previous News').'</button><input id="Koltiva.view.CMS.GridMainNews-PrevPageInfo" type="hidden" value="'.($page-1).'" />';
        }else{
            $PrevButton = '';
        }

        //Header Button
        $return = '
        <div style="margin-bottom:15px;margin-left: 0px;margin-right: 0px;" class="row">        
            <button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainNews\').NewNews()" style="float:left;" type="button" class="btn btn-space btn-success btn-rounded Koltiva.view.CMS.GridMainNews-BtnAdd"><i class="icon icon-left s7-news-paper"></i>&nbsp;&nbsp;'.lang('Add News').'</button>'.$NextButton.$PrevButton.'</div>';

        
        //Data News === (Begin)
        if(isset($DataList[0]['NewsID'])){
            for ($i=0; $i < count($DataList); $i++) {
                //Cek ada images tidak
                if($DataList[$i]['PhotoFile'] != "" && file_exists('uploads/news/'.$DataList[$i]['PhotoFile'])){
                    $ImgImages = '<img src="'.base_url().'/uploads/news/'.$DataList[$i]['PhotoFile'].'" />';
                }else{
                    $ImgImages = '';
                }

                $return .= '<div class="row"><div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <span style="font-size:12px;font-weight:bold;" class="title">'.$DataList[$i]['Title'].'</span>					
                        </div>
                        <div class="panel-body">
                            <p style="margin-top:-11px;" class="cms_video_info"><span style="float:left;">'.lang('Posted by').' <b>'.$DataList[$i]['PostedBy'].'</b> ('.$DataList[$i]['LastUpdated'].')</span><span style="float:right;">Status: '.$DataList[$i]['StatusType'].'</span></p>
                            <div style="clear:both;margin-bottom:7px;"></div>
                            
                            <div class="DivNewsContent">'.$ImgImages.$DataList[$i]['Content'].'</div>

                            <p style="text-align:right;margin-top:10px;">
                                <button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainNews\').UpdateNews('.$DataList[$i]['NewsID'].')" type="button" class="btn btn-space btn-success btn-rounded btn-xs Koltiva.view.CMS.GridMainNews-BtnUpdate"><i class="icon icon-left s7-note"></i> '.lang('Update').'</button>
                                <button onclick="javascript:Ext.getCmp(\'Koltiva.view.CMS.GridMainNews\').DeleteNews('.$DataList[$i]['NewsID'].')" type="button" class="btn btn-space btn-danger btn-rounded btn-xs Koltiva.view.CMS.GridMainNews-BtnDelete"><i class="icon icon-left s7-trash"></i> '.lang('Delete').'</button>
                            </p>
                        </div>
                    </div>
                </div></div>';       
            }
        }else{
            $return .= '<div class="DivCustomContentHtmlNoData"><h3 align="center">'.lang('No News Available').'</h3></div>';
        }
        //Data News === (End)

        //Footer Button
        $return .= '</div><div style="margin-top:15px;margin-left: 0px;margin-right: 0px;" class="row">'.$NextButton.$PrevButton.'</div>';        

        //Generate HTML untuk Content =================================== (End)

        return $return;
    }

    public function GetNewsInputPrep(){
        $sql = "INSERT INTO `cms_news` SET  
                `Title` = 'temp',
                `StatusCode` = 'inactive',
                `CreatedBy` = '{$_SESSION['userid']}',
                `DateCreated` = NOW()
                ";
        $query = $this->db->query($sql);
        $NewsID = $this->db->insert_id();

        $return['success'] = true;
        $return['NewsID'] = $NewsID;
        return $return;
    }

    public function UpdateNewsPhoto($NewsID,$gambar){
        $sql = "UPDATE cms_news a SET
                    a.`PhotoFile` = ?,
                    a.`LastModifiedBy` = ?,
                    a.`DateUpdated` = NOW()	
                WHERE
                    a.`NewsID` = ?
                LIMIT 1";
        $p = array(
            $gambar,
            $_SESSION['userid'],
            $NewsID
        );
        $query = $this->db->query($sql,$p);
    }

    public function InsertNews($ParamPost){
        $this->db->trans_start();

        $sql = "INSERT INTO `cms_news` SET  
                `Title` = ?,
                `Content` = ?,
                `StatusType` = ?,  
                `CreatedBy` = ?,
                `DateCreated` = NOW()";
        $p = array(            
            (isset($ParamPost['Title']) ? $ParamPost['Title'] : null),
            (isset($ParamPost['Content']) ? $ParamPost['Content'] : null),
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $NewsID = $this->db->insert_id();

        $ProsesAccess = $this->InputAccess('insert','news',$NewsID,$ParamPost);

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

    public function UpdateNews($ParamPost){
        $this->db->trans_start();

        $sql = "UPDATE `cms_news` a SET
                    a.Title = ?,
                    a.Content = ?,
                    a.`StatusType` = ?,
                    a.StatusCode = 'active',
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`NewsID` = ?
                LIMIT 1";
        $p = array(
            (isset($ParamPost['Title']) ? $ParamPost['Title'] : null),
            (isset($ParamPost['Content']) ? $ParamPost['Content'] : null),
            (isset($ParamPost['StatusType']) ? $ParamPost['StatusType'] : null),
            $_SESSION['userid'],
            $ParamPost['NewsID']
        );
        $query = $this->db->query($sql,$p);

        $ProsesAccess = $this->InputAccess('update','news',$ParamPost['NewsID'],$ParamPost);        

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

    public function DeleteNews($NewsID){
        $sql = "UPDATE cms_news a SET
                    a.`StatusCode` = 'nullified',
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`NewsID` = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $NewsID
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

    public function CleanUpInactiveDocumentData(){
        $sql="SELECT
                a.`DocID`,
                a.`DocUrl`	
            FROM
                cms_document a
            WHERE
                a.`StatusCode` = 'inactive'";
        $data = $this->db->query($sql)->result_array();
        
        for ($i=0; $i < count($data); $i++) {
			//hapus attachmentnya            
            delete_file('files/cms_document/'.$data[$i]['DocUrl']);
        }
        
        $sql = "DELETE FROM cms_document WHERE StatusCode='inactive'";
        $query = $this->db->query($sql);
    }

    public function GetGridMainDocument($start, $limit){
        $this->CleanUpInactiveDocumentData();

        $CekAdmin = $_SESSION['is_admin'];
        if($CekAdmin == "1"){
            $sql = "SELECT
                        a.`DocID`
                        , a.`Name`
                        , a.`Description`
                        , a.`StatusType`
                        , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                        , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
                    FROM
                        `cms_document` a
                    WHERE
                        a.`StatusCode` = 'active'
                    ORDER BY a.`DocID` DESC
                    LIMIT ?,?";
            $p = array(
                (int) $start, (int) $limit
            );
        }else{
            $DataUser = $this->muserprofile->getUserProfile($_SESSION['userid']);
            if($DataUser['type'] == 'program' || $DataUser['type'] == 'private'){
                $sql = "SELECT
                            SQL_CALC_FOUND_ROWS
                            tbl_uni.`DocID`
                            , tbl_uni.`Name`
                            , tbl_uni.`Description`
                            , tbl_uni.`StatusType`
                            , tbl_uni.PostedBy
                            , tbl_uni.LastUpdated
                        FROM
                        (
                        SELECT
                            a.`DocID`
                            , a.`Name`
                            , a.`Description`
                            , a.`StatusType`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
                        FROM
                            cms_document a
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`StatusType` = 'public'
                            
                        UNION
                        
                        SELECT	
                            a.`DocID`
                            , a.`Name`
                            , a.`Description`
                            , a.`StatusType`
                            , (SELECT suba.UserRealName FROM sys_user suba WHERE suba.UserId = a.`CreatedBy` LIMIT 1) AS PostedBy
                            , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
                        FROM
                            cms_document a
                            LEFT JOIN cms_access b ON a.`DocID` = b.`ObjID` AND b.`ObjType` =  'document'
                        WHERE
                            a.`StatusCode` = 'active'	
                            AND a.`StatusType` = 'private'
                            AND b.`RoleAccessStaff` = '1'
                            AND FIND_IN_SET(?, b.`PartnerIDImplode`)
                        ) AS tbl_uni
                        ORDER BY tbl_uni.DocID DESC
                        LIMIT ?,?";
                $p = array(
                    $DataUser['PartnerID'],
                    (int) $start, (int) $limit
                );
            }else{
                return array();
            }
        }

        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

    	return $result;
    }

    public function GetDocumentViewByID($DocID){
        $sql = "SELECT 
                    `DocID`,
                    `Name`,
                    `DocUrl`,
                    `Description`,
                    `StatusType` 
                FROM
                    `cms_document` a
                WHERE
                    a.`DocID` = ?
                LIMIT 1";
        $p = array(
            $DocID
        );
        return $this->db->query($sql,$p)->row_array();
    }

    public function GetDocumentInputPrep(){
        $sql = "INSERT INTO `cms_document` SET  
                `Name` = 'temp',
                `StatusCode` = 'inactive'";
        $query = $this->db->query($sql);
        $DocID = $this->db->insert_id();

        $return['success'] = true;
        $return['DocID'] = $DocID;
        return $return;
    }

    public function GetDocumentFormOpen($DocID){
        $sql = "SELECT 
                    a.`DocID`,
                    a.`Name`,
                    a.`DocUrl`,
                    a.`Description`,
                    a.`StatusType`
                    , b.`PartnerIDImplode`
                    , b.`RoleAccessFarmer`
                    , b.`RoleAccessStaff`
                    , b.`RoleAccessTrader`
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
            $keyNew = "Koltiva.view.CMS.WinFormDocument-Form-".$key;
            $DataForm[$keyNew] = $value;
        }
        
        //Untuk Js
        $DataForm['DocUrl'] = $data['DocUrl'];

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function UpdateDocumentFile($DocID, $documentatt){
        $sql = "UPDATE cms_document a SET
                    a.`DocUrl` = ?,
                    a.`LastModifiedBy` = ?,
                    a.`DateUpdated` = NOW()
                WHERE
                    a.`DocID` = ?
                LIMIT 1";
        $p = array(
            $documentatt,
            $_SESSION['userid'],
            $DocID
        );
        $query = $this->db->query($sql,$p);
    }

    public function UpdateDocument($ParamPost){
        $this->db->trans_start();

        if($ParamPost['OpsiDisplay'] == 'insert'){
            $sqltime = ", `CreatedBy` = '{$_SESSION['userid']}'
                        , `DateCreated` = NOW()";
        }else{
            $sqltime = ", `LastModifiedBy` = '{$_SESSION['userid']}'
                        , `DateUpdated` = NOW()";
        }

        $sql = "UPDATE `cms_document` SET  
                    `Name` = ?,
                    `Description` = ?,                      
                    `StatusType` = ?,
                    `StatusCode` = 'active'
                    $sqltime
                WHERE
                    `DocID` = ?
                LIMIT 1";        
        $p = array(
            $ParamPost['Name'],            
            (isset($ParamPost['Description']) ? $ParamPost['Description'] : null),            
            $ParamPost['StatusType'],
            $ParamPost['DocID']
        );
        $query = $this->db->query($sql,$p);

        $ProsesAccess = $this->InputAccess($ParamPost['OpsiDisplay'],'document',$ParamPost['DocID'],$ParamPost);

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

    public function DeleteDocument($DocID){
        $sql = "UPDATE cms_document a SET
                    a.`StatusCode` = 'nullified',
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`DocID` = ?
                LIMIT 1";
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

}