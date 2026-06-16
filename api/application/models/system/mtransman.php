<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : mtransman.php
 *******************************************/
class Mtransman extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function GetTransmanMainGrid() {
        $return = array();

        $sql = "SELECT
                    a.`TransManID`
                    , a.`ModuleName`
                    , a.`ModuleDescription`
                    , IFNULL(b.`name`,'-') AS ProgramMobile
                    , (SELECT COUNT(sa.FilePath) FROM `sys_translation_man_files` sa WHERE sa.TransManID = a.`TransManID` ) AS FilesCount
                    , COUNT(ab.`TransManID`) AS KeysCount
                FROM
                    `sys_translation_man` a
                    LEFT JOIN mw_program b ON a.`MobileProgramUid` = b.`uid`
                    LEFT JOIN `sys_translation_man_transkey` ab ON a.`TransManID` = ab.`TransManID`
                WHERE 1=1
                GROUP BY a.`TransManID`
                ORDER BY a.TransManID DESC";
        $query = $this->db->query($sql);

        $return['success'] = true;
        $return['data'] = $query->result_array();
        return $return;
    }

    public function GetMainFormOpen($TransManID) {
        $return = array();

        $sql = "SELECT
                    `TransManID`,
                    `ModuleName`,
                    `ModuleDescription`,
                    `MobileProgramUid`
                FROM
                    `sys_translation_man`
                WHERE 1=1
                    AND TransManID = {$TransManID}
                LIMIT 1";
        $data = $this->db->query($sql)->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.System.Transman.MainForm-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        //$dataRow['SubTopicIDs'] = $data['SubTopicIDs'];

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function GetTransManData($TransManID) {
        $sql = "SELECT
                    `TransManID`,
                    `ModuleName`,
                    `ModuleDescription`,
                    `MobileProgramUid`
                FROM
                    `sys_translation_man`
                WHERE 1=1
                    AND TransManID = {$TransManID}
                LIMIT 1";
        return $this->db->query($sql)->row_array();
    }

    public function GetTransmanMainGridTranslate($key = null,$TransManID,$start, $limit){
        $key = strtolower($key);
        $SqlFilterTrans = ' AND ( (LOWER(a.`TransKey`) LIKE "%'.$key.'%") ) ';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
            a.TransKeyID id
            , a.TransKey 'key'
            , a.IsTranslated
        FROM
            `sys_translation_man_transkey` a
        WHERE
            a.TransManID = ?
        $SqlFilterTrans
        GROUP BY
            a.TransKey
        ORDER BY a.TransKey ASC
        LIMIT ?, ?
        ";

        $query = $this->db->query($sql, array($TransManID, (int)$start, (int)$limit));

        $result['data'] = $query->result_array();
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $total = $row->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }

    public function InsertMainForm($paramPost) {
        $results = array();
        //echo '<pre>'; print_r($paramPost); exit;
        $this->db->trans_begin();

        $sql = "INSERT INTO `sys_translation_man` SET
                `ModuleName` = ?,
                `ModuleDescription` = ?,
                `MobileProgramUid` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            $paramPost['ModuleName'],
            $paramPost['ModuleDescription'],
            (isset($paramPost['MobileProgramUid']) ? $paramPost['MobileProgramUid'] : null),
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $TransManID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['TransManID'] = $TransManID;
        }

        return $results;
    }

    public function UpdateMainForm($paramPost) {
        $results = array();
        //echo '<pre>'; print_r($paramPost); exit;
        $this->db->trans_begin();

        $sql = "UPDATE `sys_translation_man` SET
                    `ModuleName` = ?,
                    `ModuleDescription` = ?,
                    `MobileProgramUid` = ?,
                    `DateUpdated` = NOW(),
                    `LastModifiedBy` = ?
                WHERE 
                    `TransManID` = ?
                LIMIT 1";
        $p = array(
            $paramPost['ModuleName'],
            $paramPost['ModuleDescription'],
            $paramPost['MobileProgramUid'],
            $_SESSION['userid'],
            $paramPost['TransManID']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['TransManID'] = $paramPost['TransManID'];
        }

        return $results;
    }

    public function DeleteMainData($TransManID) {
        $results = array();
        $this->db->trans_begin();

        $sql = "DELETE FROM `sys_translation_man_transkey` WHERE TransManID = {$TransManID}";
        $query = $this->db->query($sql);

        $sql = "DELETE FROM `sys_translation_man_files` WHERE TransManID = {$TransManID}";
        $query = $this->db->query($sql);

        $sql = "DELETE FROM `sys_translation_man` WHERE TransManID = {$TransManID}";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        }

        return $results;
    }

    public function GetSourceCodeFilesGrid($TransManID) {
        $return = array();

        $sql = "SELECT
                    a.`TransManID`
                    , a.`FilePath`
                    , '-' AS IsFileExist
                FROM
                    `sys_translation_man_files` a
                WHERE 1=1
                    AND a.`TransManID` = ?
                ORDER BY a.`FilePath` ASC";
        $p = array(
            $TransManID
        );
        $data = $this->db->query($sql,$p)->result_array();

        for ($i=0; $i < count($data); $i++) { 
            $IsExists = 2;
            $FilesCheck = '../'.$data[$i]['FilePath'];
            
            if(file_exists($FilesCheck)) {
                if(is_file($FilesCheck)) {
                    $IsExists = 1;
                }
            }

            //echo '<pre>'; print_r(array($FilesCheck,$IsExists)); exit;
            $data[$i]['IsFileExist'] = $IsExists;
        }

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function InsertSourceCodeFiles($ParamPost) {
        $results = array();
        $this->db->trans_begin();

        $sql = "INSERT INTO `sys_translation_man_files` SET
                `TransManID` = ?,
                `FilePath` = ?";
        $p = array(
            $ParamPost['TransManID'],
            $ParamPost['FilePath']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;
    }

    public function UpdateSourceCodeFiles($ParamPost) {
        $results = array();
        $this->db->trans_begin();

        $sql = "UPDATE sys_translation_man_files a SET
                    a.`FilePath` = ?
                WHERE
                    a.`TransManID` = ?
                    AND a.`FilePath` = ?
                LIMIT 1";
        $p = array(
            $ParamPost['FilePath'],
            $ParamPost['TransManID'],
            $ParamPost['FilePath']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;
    }

    public function DeleteSourceCodeFiles($TransManID,$FilePath) {
        $results = array();
        $this->db->trans_begin();

        $sql = "DELETE FROM sys_translation_man_files WHERE TransManID = ? AND FilePath = ? LIMIT 1";
        $query = $this->db->query($sql, array($TransManID,$FilePath));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        }

        return $results;
    }

    public function GenerateTransKey($TransManID) {
        $this->load->helper('translation_helper');
        $results = array();
        $this->db->trans_begin();

        //Ambil list files
        $sql = "SELECT
                    a.`FilePath`
                FROM
                    `sys_translation_man_files` a
                WHERE 1=1
                    AND a.`TransManID` = ?
                ";
        $DataFile = $this->db->query($sql,array($TransManID))->result_array();

        //Hapus dulu data lama
        $sql = "DELETE FROM sys_translation_man_transkey WHERE TransManID = ?";
        $query = $this->db->query($sql,array($TransManID));

        if($DataFile[0]['FilePath'] != "") {
            for ($i=0; $i < count($DataFile); $i++) { 
                $ArrFin = array();
                $PathProcess = '../'.$DataFile[$i]['FilePath'];
                $ExtNya = GetFileExt($PathProcess);
    
                if($ExtNya == 'js' || $ExtNya == 'php') {
                    $fh = fopen($PathProcess, 'r') or die($php_errormsg);
                    while (!feof($fh)) {
                        $line = fgets($fh, 4096);
                        //echo $line.'<br>';
                        preg_match_all('/lang\((?:".*?"|\'.*?\')\)/', $line, $matches);
                        if(!empty($matches[0])) $ArrFin = array_merge($ArrFin,$matches[0]);
                    }
                    fclose($fh);
                }

                if($ArrFin[0] != "") {
                    for ($j=0; $j < count($ArrFin); $j++) { 
                        //hilang kan lang
                        $vPro = $ArrFin[$j];
                        $vPro = substr($vPro,6);
                        $vPro = substr($vPro,0,-2);

                        if(in_array($vPro,array('','-',' - ','%'))) { //skip bilang key bernilai ini
                            continue;
                        }

                        //cek apakah duplicate keynya di sys_translation_man_transkey
                        $exist = $this->CheckKeyTranslate($vPro);

                        if($exist == 0){
                            //cek apakah sudah ditranslate di sys_translate
                            $IsTranslated = $this->CheckIsTranslated($vPro);

                            $sql = "INSERT IGNORE INTO `sys_translation_man_transkey` SET
                                    `TransManID` = ?,
                                    `IsTranslated` = ?,
                                    `TransKey` = ?
                                    ";
                            $p = array(
                                $TransManID,
                                $IsTranslated,
                                $vPro
                            );
                            $query = $this->db->query($sql,$p);
                        }
                    }
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Process Failed");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Process Success");
        }

        return $results;
    }

    public function CheckKeyTranslate($key){
        $sql    = "SELECT TransKeyID FROM sys_translation_man_transkey WHERE TransKey = ?";
        $query  = $this->db->query($sql,array($key));

        return $query->num_rows();
    }

    public function CheckIsTranslated($key){
        $sql    = "SELECT a.id FROM sys_translation a WHERE a.key = ?";
        $query  = $this->db->query($sql,array($key));

        if($query->num_rows()>0){
            return "Yes";
        }else{
            return "No";
        }
    }

    public function GetRefLanguage() {
        $sql = "SELECT
                `code`,
                `name` AS `text`,
                `code` AS `dataIndex`,
                '' AS `hidden`,
                '24%' AS `width`
            FROM
                `sys_language`";
        return $this->db->query($sql, array())->result_array();
    }

    public function GetTransKey($TransManID) {
        $sql = "SELECT
                    a.`TransKey`
                    -- , a.`FilePath`
                FROM
                    `sys_translation_man_transkey` a
                WHERE 1=1
                    AND a.`TransManID` = ?";
        return $this->db->query($sql,array($TransManID))->result_array();
    }

    public function GetTranslationByKeyExport($key, $lang) {
        $sql = "SELECT
                a.id
                , a.text
            FROM
                `sys_translation` a
            WHERE a.`key` = ?
                AND a.`language` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($key, $lang));
        return $query->row_array();
    }

    public function GetTransKeyWithMobile($TransManID, $MobileProgramUid) {
        $ArrKey = array();
        $ArrReturn = array();

        //ambil key dari web
        $sql = "SELECT
                    a.`TransKey`
                    , a.`FilePath`
                    , b.`text` AS EngLabel
                FROM
                    `sys_translation_man_transkey` a
                    LEFT JOIN sys_translation b ON a.`TransKey` = b.`key` AND b.`language` = 'english'
                WHERE 1=1
                    AND a.`TransManID` = ?";
        $DataWeb = $this->db->query($sql,array($TransManID))->result_array();
        $DataWebRef = ConvertArrayWithCustomKey($DataWeb,'TransKey');
        $DataWebSingleArray = ConvertArrayToSingleArray($DataWeb, 'TransKey');
        //echo '<pre>'; print_r(array($DataWeb, $DataWebRef, $DataWebSingleArray)); exit;

        //Ambil key dari mobile
        $sql = "CALL translation_per_program(?)";
        $DataMobile = $this->db->query($sql,array($MobileProgramUid))->result_array();
        $DataMobileRef = ConvertArrayWithCustomKey($DataMobile,'key');
        $DataMobileSingleArray = ConvertArrayToSingleArray($DataMobile, 'key');
        //echo '<pre>'; print_r(array($DataMobile, $DataMobileRef, $DataMobileSingleArray)); exit;
        
        //Combine data Translation Key
        $ArrKey = array_merge($DataWebSingleArray,$DataMobileSingleArray);
        array_unique($ArrKey);
        //echo '<pre>'; print_r($ArrKey); exit;

        //Bentuk ArrReturn
        if($ArrKey[0] != "") {
            for ($k=0; $k < count($ArrKey); $k++) { 
                $TransKey = $ArrKey[$k];

                $ArrReturn[$k]['TransKey'] = $TransKey;
                $ArrReturn[$k]['WebFilePath'] = $DataWebRef[$TransKey]['FilePath'];
                $ArrReturn[$k]['WebEnglishLabel'] = $DataWebRef[$TransKey]['EngLabel'];
                $ArrReturn[$k]['MobileDescription'] = $DataMobileRef[$TransKey]['Program/DataElement/Option/Section'];
                $ArrReturn[$k]['MobileObject'] = $DataMobileRef[$TransKey]['objectclass'];
                $ArrReturn[$k]['MobileEnglishLabel'] = $DataMobileRef[$TransKey]['text'];
            }
        }

        //echo '<pre>'; print_r($ArrReturn); exit;
        return $ArrReturn;
    }

}