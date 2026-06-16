<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 29 2018
 *  File : train.php
 *******************************************/

defined('BASEPATH') or exit('No direct script access allowed');

class Train extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('training/mtrain');
        $this->load->library('awsfileupload');
    }

    public function train_attachment_files_main_grid_get(){
        $TrainType = $this->get('TrainType');
        $TrainID = (int) $this->get('TrainID');

        //Cleanup inactive file
        $proses = $this->mtrain->CleanupAttachmentFiles();

        $Data = $this->mtrain->GetTrainAttachmentFilesMainGrid($TrainType,$TrainID);
        $this->response($Data, 200);
    }

    public function attachment_file_post(){

        if($this->post("OpsiDisplay") == "insert"){
            if ($this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['name'] != '') {
                $TrainAttID = $this->post('TrainAttID');

                $arrTemp    = explode(".", $this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['name']);
                $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
                $arrTempExt = explode("?", $tempExtNya);
                $extNya     = $arrTempExt[0];
                $delete_aws = null;
                
                //Cek ext
                if(in_array($extNya,array('jpg','jpeg','png','gif','pdf'))){
                    $NamafileNya = $TrainAttID."_attachment.".$extNya;

                    //hapus dl filenya (jika ada)
                    if (file_exists('files/tmp/' . $NamafileNya))
                        delete_file('files/tmp/' . $NamafileNya);

                    $fileupload['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput'] = $this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput'];                
                    $upload = move_upload($fileupload, 'files/tmp/' . $NamafileNya);                
                    if (isset($upload['upload_data'])) {
                        //Update ke tabel
                        $proses = $this->mtrain->UpdateAttachmentFile($TrainAttID, $NamafileNya);
                        $existingphoto = $this->post('Koltiva_view_Train_WinFormAttachmentFiles-Form-OldPhoto');
                        if($this->awsfileupload->doesObjectExist($existingphoto) == true){
                            $delete_aws = $this->awsfileupload->delete($existingphoto);
                        }

                        $result['success'] = true;
                        $result['file_with_rand'] = base_url().'/files/tmp/'.$NamafileNya. '?' . rand(1, 100);
                        $result['file'] = base_url().'/files/tmp/'.$NamafileNya;
                        $result['FilePath'] = 'files/tmp/'.$NamafileNya;
                        $result['extNya'] = $extNya;
                        $result['delete_aws'] = $delete_aws;
                        $this->response($result, 200);
                    }else{
                        $return['success'] = false;
                        $return['message'] = lang('Upload Failed');
                        $return['status'] = $upload;
                        $this->response($return, 400);
                    }
                }else{
                    $return['success'] = false;
                    $return['message'] = lang('File type not allowed');
                    $this->response($return, 400);
                }
            }else{
                $return['success'] = false;
                $return['message'] = lang('Upload Failed');
                $this->response($return, 400);
            }
        }else{
            if ($this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['name'] != '') {
                $TrainAttID = $this->post('TrainAttID');

                $arrTemp    = explode(".", $this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['name']);
                $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
                $arrTempExt = explode("?", $tempExtNya);
                $extNya     = $arrTempExt[0];

                //Cek ext
                if(in_array($extNya,array('jpg','jpeg','png','gif','pdf'))){
                    if($extNya == "pdf"){
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['tmp_name'],$this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['name'], AWSS3_TRAINING_FILE_PATH, 'documents'); 
                    }else{
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['tmp_name'],$this->file['Koltiva_view_Train_WinFormAttachmentFiles-Form-FilenameInput']['name'], AWSS3_TRAINING_IMAGE_PATH, 'images'); 
                    }
                    
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Train_WinFormAttachmentFiles-Form-OldPhoto')) == true) {
                            $this->awsfileupload->delete($this->post('Koltiva_view_Train_WinFormAttachmentFiles-Form-OldPhoto'));
                        }else{
                            delete_file($this->post('Koltiva_view_Train_WinFormAttachmentFiles-Form-OldPhoto'));
                        }

                        $proses = $this->mtrain->UpdateAttachmentFile($TrainAttID, $upload['filenamepath']);

                        $result['success'] = true;
                        $result['message'] = lang('File uploaded');
                        $result['file'] = $upload['fileurl'];
                        $result['FilePath']   = $upload['filenamepath'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                        $this->response($result, 400);
                    }
                }else{
                    $return['success'] = false;
                    $return['message'] = lang('File type not allowed');
                    $this->response($return, 400);
                }
            }
        }
    }

    public function attachment_file_form_data_get(){
        $TrainAttID = (int) $this->get('TrainAttID');

        $data = $this->mtrain->GetAttachmentFilesFormData($TrainAttID);
        $this->response($data, 200);
    }

    public function attachment_file_input_prep_get(){
        $TrainID = (int) $this->get('TrainID');
        $TrainType = $this->get('TrainType');

        $data = $this->mtrain->InsertPrepAttachmentFiles($TrainID,$TrainType);
        $this->response($data, 200);
    }

    public function attachment_file_input_post(){
        $varPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Train_WinFormAttachmentFiles-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        // echo '<pre>'; print_r($ParamPost); exit;

        if($ParamPost['OpsiDisplay'] == "insert"){
            $proses = $this->mtrain->InsertAttachmentFiles($ParamPost);
        }else{
            $proses = $this->mtrain->UpdateAttachmentFiles($ParamPost);
        }        
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function attachment_file_delete(){
        $TrainAttID = (int) $this->delete('TrainAttID');

        $proses = $this->mtrain->DeleteAttachmentFiles($TrainAttID);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }
}