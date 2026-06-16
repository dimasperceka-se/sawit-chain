<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 29 2018
 *  File : mtrain.php
 *******************************************/

class Mtrain extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function GetTrainAttachmentFilesMainGrid($TrainType,$TrainID){
        $sql = "SELECT
                    a.TrainID
                    , a.TrainAttID
                    , a.Filename
                    , a.Remark
                FROM
                    ktv_training_attachment_files a
                WHERE
                    1=1
                    AND a.`TrainID` = ? AND a.TrainType = 'farmer' AND a.StatusCode = 'active'
                ORDER BY a.`Filename`";
        $Data = $this->db->query($sql,array($TrainID))->result_array();
        
        if(isset($Data[0]['TrainID'])){
            for ($i=0; $i < count($Data); $i++) {
                // $Data[$i]['Remark'] = nl2br($Data[$i]['Remark']);

                //Cek FileExist
                if($this->awsfileupload->doesObjectExist($Data[$i]['Filename']) == true) {
                    $Data[$i]['FileExist'] = 'yes';
                    $Data[$i]['Filename'] = $this->config->item('CTCDN').'/'.$Data[$i]['Filename'];
                }else{
                    if(file_exists($Data[$i]['Filename'])){
                        $Data[$i]['FileExist'] = 'yes';
                        $Data[$i]['Filename'] = base_url().$Data[$i]['Filename'];
                    }else{
                        $Data[$i]['FileExist'] = 'no';
                    }
                }

                //Kasih File Ext
                $arrTemp    = explode(".", $Data[$i]['Filename']);
                $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
                $arrTempExt = explode("?", $tempExtNya);
                $Data[$i]['ExtensionFile']     = $arrTempExt[0];
            }
        }
        $result['data'] = $Data;
        $result['success'] = true;
        return $result;
    }

    public function CleanupAttachmentFiles(){
        $sql = "SELECT
                    a.`TrainAttID`
                    , a.`Filename`
                FROM
                    `ktv_training_attachment_files` a
                WHERE
                    a.`StatusCode` = 'inactive'";
        $DataList = $this->db->query($sql)->result_array();

        for ($i=0; $i < count($DataList); $i++) { 
            //Hapus File Jika Ada
            if(file_exists('files/training_files/'.$DataList[$i]['TrainAttID'])){
                delete_file('files/training_files/'.$DataList[$i]['TrainAttID']);
            }
        }

        $sql = "DELETE FROM `ktv_training_attachment_files` WHERE `StatusCode` = 'inactive'";
        $query = $this->db->query($sql,array());
    }

    public function GetAttachmentFilesFormData($TrainAttID){
        $sql = "SELECT
                    a.`TrainAttID`
                    , a.`Filename`
                    , a.`Remark`
                FROM
                    `ktv_training_attachment_files` a
                WHERE
                    a.`TrainAttID` = ?
                LIMIT 1";
        $p = array(
            $TrainAttID
        );
        $Data = $this->db->query($sql,$p)->row_array();

        //prep variable
        $DataRow = array();
        foreach ($Data as $key => $value) {
            $keyNew = "Koltiva.view.Train.WinFormAttachmentFiles-Form-".$key;
            $DataRow[$keyNew] = $value;
        }

        //Extension
        $arrTemp    = explode(".", $Data['Filename']);
        $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
        $arrTempExt = explode("?", $tempExtNya);
        $DataRow['ExtensionFile']     = $arrTempExt[0];

        $DataRow['Filename'] = $Data['Filename'];
        $DataRow['FilePath'] = $Data['Filename'];

        //Cek FileExist
        if($this->awsfileupload->doesObjectExist($Data['Filename']) == true) {
            $DataRow['Filename'] = $this->config->item('CTCDN').'/'.$Data['Filename'];
        }else{
            if(file_exists('files/tmp/'.$Data['Filename'])){
                $DataRow['Filename'] = base_url().'files/tmp/'.$Data['Filename'];
            }
        }


        $return['success'] = true;
        $return['data']    = $DataRow;
        return $return;
    }

    public function InsertPrepAttachmentFiles($TrainID,$TrainType){
        $sql = "INSERT INTO `ktv_training_attachment_files` SET
                `TrainID` = ?,
                `TrainType` = ?,
                `StatusCode` = 'inactive'";
        $query = $this->db->query($sql,array($TrainID,$TrainType));
        $TrainAttID = $this->db->insert_id();

        $return['success'] = true;
        $return['TrainAttID'] = $TrainAttID;
        return $return;
    }

    public function UpdateAttachmentFile($TrainAttID, $NamafileNya){
        $sql = "UPDATE ktv_training_attachment_files a SET
                    a.`Filename` = ?,
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`TrainAttID` = ?
                LIMIT 1
                ";
        $p = array(
            $NamafileNya,
            $_SESSION['userid'],
            $TrainAttID
        );
        return $this->db->query($sql,$p);
    }

    public function InsertAttachmentFiles($ParamPost){
        $this->db->trans_start();
        $sql = "SELECT TrainID,Filename FROM `ktv_training_attachment_files` WHERE `TrainAttID` = ? ";
        $query = $this->db->query($sql,array($ParamPost['TrainAttID']));
        // echo '<pre>'; print_r($this->db->last_query()); exit;
        if($query->num_rows()>0){
            $row = $query->row();
            if($row->TrainID > 0){
                $Filename = $row->Filename;
                $arrTemp    = explode(".", $Filename);
                $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
                $arrTempExt = explode("?", $tempExtNya);
                $extNya     = $arrTempExt[0];

                if(in_array($extNya,array('jpg','jpeg','png','gif','pdf'))){
                    if($extNya == "pdf"){
                        $upload = $this->awsfileupload->upload('files/tmp/'.$Filename,$Filename, AWSS3_TRAINING_FILE_PATH, 'documents'); 
                    }else{
                        $upload = $this->awsfileupload->upload('files/tmp/'.$Filename,$Filename, AWSS3_TRAINING_IMAGE_PATH, 'images'); 
                    }

                    if ($upload['success'] == true) {
                        $this->db->query("
                            UPDATE 
                                ktv_training_attachment_files 
                            SET 
                                Filename = ? 
                            WHERE 
                                TrainAttID = ?", 
                        array($upload['filenamepath'],$ParamPost['TrainAttID']));
                    } 
                }
            }
        }
        $sql = "UPDATE `ktv_training_attachment_files` SET
                    `Remark` = ?,
                    `StatusCode` = 'active',
                    `CreatedBy` = ?,
                    `DateCreated` = NOW()
                WHERE
                    TrainAttID = ?
                LIMIT 1";
        $p = array(
            $ParamPost['Remark'],
            $_SESSION['userid'],
            $ParamPost['TrainAttID']
        );
        $query = $this->db->query($sql,$p);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang('Failed to insert data');
        } 
        else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang('Data saved');
        }
        return $return;
    }

    public function UpdateAttachmentFiles($ParamPost){
        $this->db->trans_start();
        $sql = "SELECT TrainID,Filename FROM `ktv_training_attachment_files` WHERE `TrainAttID` = ? AND `CreatedBy` = ? ";
        $query1 = $this->db->query($sql,array($ParamPost['TrainAttID'],$_SESSION['userid']));
        if($query1->num_rows()>0){
            $row = $query1->row();
            if($row->TrainID != '0'){
                $Filename = $row->Filename;
                $arrTemp    = explode(".", $Filename);
                $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
                $arrTempExt = explode("?", $tempExtNya);
                $extNya     = $arrTempExt[0];

                if(in_array($extNya,array('jpg','jpeg','png','gif','pdf'))){
                    if($extNya == "pdf"){
                        $upload = $this->awsfileupload->upload('files/tmp/'.$Filename,$Filename, AWSS3_TRAINING_FILE_PATH, 'documents'); 
                    }else{
                        $upload = $this->awsfileupload->upload('files/tmp/'.$Filename,$Filename, AWSS3_TRAINING_IMAGE_PATH, 'images'); 
                    }

                    if ($upload['success'] == true) {
                        $this->db->query("
                            UPDATE 
                                ktv_training_attachment_files 
                            SET 
                                Filename = ? 
                            WHERE 
                                TrainAttID = ?", 
                        array($upload['filenamepath'],$ParamPost['TrainAttID']));
                    } 
                }
            }
        }
        $sql = "UPDATE ktv_training_attachment_files SET
                    `Remark` = ?,
                    LastModifiedBy = ?,
                    `DateUpdated` = NOW()
                WHERE
                    TrainAttID = ?
                LIMIT 1
                ";
        $p = array(
            $ParamPost['Remark'],
            $_SESSION['userid'],
            $ParamPost['TrainAttID']
        );
        $query = $this->db->query($sql,$p);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang('Failed to update data');
        } 
        else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang('Data saved');
        }
        return $return;
    }

    public function DeleteAttachmentFiles($TrainAttID){
        $sql = "UPDATE ktv_training_attachment_files SET
                    `StatusCode` = 'nullified',
                    LastModifiedBy = ?,
                    `DateUpdated` = NOW()
                WHERE
                    TrainAttID = ?
                LIMIT 1
                ";
        $p = array(
            $_SESSION['userid'],
            $TrainAttID
        );
        $query = $this->db->query($sql,$p);

        if($query == true){
            $return['success'] = true;
            $return['message'] = lang('Data deleted');
        }else{
            $return['success'] = false;
            $return['message'] = lang('Failed to delete data');
        }
        return $return;
    }
}