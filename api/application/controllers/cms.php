<?php
/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Wed Sep 05 2018
 *  File : cms.php
 *******************************************/

defined('BASEPATH') or exit('No direct script access allowed');

class Cms extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('mcms');
    }

    public function grid_main_announcement_get(){
        $data = $this->mcms->GetGridMainAnnouncement($this->get('start'), $this->get('limit'));
        $this->response($data, 200);
    }

    public function announcement_form_open_get(){
        $AnnID = (int) $this->get('AnnID');
        $data = $this->mcms->GetAnnouncementFormOpen($AnnID);
        $this->response($data, 200);
    }

    public function announcement_input_post(){    
        $VarPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($VarPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_CMS_WinFormAnnouncement-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        if($ParamPost['AnnID'] == ""){
            $proses = $this->mcms->InsertAnnouncement($ParamPost);
        }else{
            $proses = $this->mcms->UpdateAnnouncement($ParamPost);
        }        
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function announcement_delete(){
        $AnnID = (int) $this->delete('AnnID');
        $proses = $this->mcms->DeleteAnnouncement($AnnID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function video_content_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

		//Get Param
		$page = (int) $this->get('page');		
        $limit = (int) $this->get('limit');        

		$return = $this->mcms->GetVideoContent($page,$limit);
		echo $return; exit;
	}
	
	public function video_watch_get(){
		$VidID = (int) $this->get('VidID');

		$DataVideo = $this->mcms->GetVideoByID($VidID);
		if(isset($DataVideo['VidID'])){
			echo ' <iframe width="560" height="420" src="'.$DataVideo['VideoUrl'].'?controls=0"></iframe> '; exit;
		}else{
			echo '<div width="560" height="420"></div>'; exit;
		}
    }
    
    public function video_form_open_get(){
        $VidID = (int) $this->get('VidID');
        $data = $this->mcms->GetVideoFormOpen($VidID);
        $this->response($data, 200);
    }

    public function video_input_prep_get(){
        $Data = $this->mcms->GetVideoInputPrep();
        $this->response($Data, 200);
    }

    public function video_input_photo_thumb_post(){                
        if ($this->file['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput']['name'] != '') {
            $VidID = $this->post('VidID');
            $arrTemp    = explode(".", $this->file['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput']['name']);
            $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
            $arrTempExt = explode("?", $tempExtNya);
            $extNya     = $arrTempExt[0];

            $gambar = $VidID . "_thumb." . $extNya;
            //hapus dl filenya (jika ada)
            if(file_exists('images/video/'.$gambar)){
                unlink('images/video/' . $gambar);
            }
            $fileupload['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput'] = $this->file['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput'];
            $upload = move_upload($fileupload, 'images/video/' . $gambar);
            if (isset($upload['upload_data'])) {

                //Update ke tabel
                $proses = $this->mcms->UpdateVideoPhotoThumb($VidID, $gambar);

                $result['success']    = true;
                $result['file']       = $gambar . '?' . rand(1, 100);
                $this->response($result, 200);
            } else {
                $this->response(array(), 400);
            }
        }else{
            $this->response(array(), 400);
        }
    }

    public function video_input_post(){
        $VarPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($VarPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_CMS_WinFormVideo-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;
        
        $proses = $this->mcms->UpdateVideo($ParamPost);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function video_delete(){
        $VidID = (int) $this->delete('VidID');
        $proses = $this->mcms->DeleteVideo($VidID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }


    public function news_content_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

		//Get Param
		$page = (int) $this->get('page');		
        $limit = (int) $this->get('limit');        

		$return = $this->mcms->GetNewsContent($page,$limit);
		echo $return; exit;
    }

    public function news_form_open_get(){
        $NewsID = (int) $this->get('NewsID');
        $data = $this->mcms->GetNewsFormOpen($NewsID);        
        $this->response($data, 200);
    }

    public function news_input_prep_get(){
        $Data = $this->mcms->GetNewsInputPrep();
        $this->response($Data, 200);
    }

    public function news_input_photo_post(){
        if ($this->file['Koltiva_view_CMS_WinFormNews-Form-PhotoFile']['name'] != '') {
            $NewsID = $this->post('NewsID');
            $arrTemp    = explode(".", $this->file['Koltiva_view_CMS_WinFormNews-Form-PhotoFile']['name']);
            $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
            $arrTempExt = explode("?", $tempExtNya);
            $extNya     = $arrTempExt[0];

            $gambar = $NewsID . "_newspic." . $extNya;
            //hapus dl filenya (jika ada)
            if(file_exists('uploads/news/' . $gambar)){
                unlink('uploads/news/' . $gambar);
            }
            $fileupload['Koltiva_view_CMS_WinFormNews-Form-PhotoFile'] = $this->file['Koltiva_view_CMS_WinFormNews-Form-PhotoFile'];
            $upload = move_upload($fileupload, 'uploads/news/' . $gambar);
            if (isset($upload['upload_data'])) {

                //Update ke tabel
                $proses = $this->mcms->UpdateNewsPhoto($NewsID, $gambar);

                $result['success']    = true;
                $result['file']       = $gambar . '?' . rand(1, 100);
                $result['NewsID']     = $NewsID;
                $this->response($result, 200);
            } else {
                $this->response(array(), 400);
            }
        }else{
            $this->response(array(), 400);
        }

        if ($this->file['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput']['name'] != '') {
            $VidID = $this->post('VidID');
            $arrTemp    = explode(".", $this->file['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput']['name']);
            $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
            $arrTempExt = explode("?", $tempExtNya);
            $extNya     = $arrTempExt[0];

            $gambar = $VidID . "_thumb." . $extNya;
            //hapus dl filenya (jika ada)
            delete_file('images/video/' . $gambar);
            $fileupload['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput'] = $this->file['Koltiva_view_CMS_WinFormVideo-Form-ThumbPicInput'];
            $upload = move_upload($fileupload, 'images/video/' . $gambar);
            if (isset($upload['upload_data'])) {

                //Update ke tabel
                $proses = $this->mcms->UpdateVideoPhotoThumb($VidID, $gambar);

                $result['success']    = true;
                $result['file']       = $gambar . '?' . rand(1, 100);
                $this->response($result, 200);
            } else {
                $this->response(array(), 400);
            }
        }else{
            $this->response(array(), 400);
        }
    }

    public function news_input_post(){
        $VarPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($VarPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_CMS_WinFormNews-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        if($ParamPost['NewsID'] == ""){
            $proses = $this->mcms->InsertNews($ParamPost);
        }else{
            $proses = $this->mcms->UpdateNews($ParamPost);
        }
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function news_delete(){
        $NewsID = (int) $this->delete('NewsID');
        $proses = $this->mcms->DeleteNews($NewsID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function grid_main_document_get(){
        $data = $this->mcms->GetGridMainDocument($this->get('start'), $this->get('limit'));
        $this->response($data, 200);
    }

    public function document_view_get(){
        $DocID = (int) $this->get('DocID');
        $data = $this->mcms->GetDocumentViewByID($DocID);

        if(isset($data['DocID'])){
            $DocURL = base_url().'files/cms_document/'.$data['DocUrl'].'?'.rand(1, 100);
			echo ' <iframe style="padding:4px 22px 15px 4px;" width="724" height="1050" src="'.$DocURL.'"></iframe> '; exit;
		}else{
			echo '<div width="724" height="1050"></div>'; exit;
		}
    }

    public function document_input_prep_get(){
        $Data = $this->mcms->GetDocumentInputPrep();
        $this->response($Data, 200);
    }

    public function document_form_open_get(){
        $DocID = (int) $this->get('DocID');
        $data = $this->mcms->GetDocumentFormOpen($DocID);
        $this->response($data, 200);
    }

    public function document_input_file_post(){
        if ($this->file['Koltiva_view_CMS_WinFormDocument-Form-DocUrlInput']['name'] != '') {
            $DocID = $this->post('DocID');
            $DocName = $this->post('Name');
            $arrTemp    = explode(".", $this->file['Koltiva_view_CMS_WinFormDocument-Form-DocUrlInput']['name']);
            $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
            $arrTempExt = explode("?", $tempExtNya);
            $extNya     = strtolower($arrTempExt[0]);
            
            if($DocName == ""){
                $return['success'] = false;
                $return['message'] = lang('No document name yet');
                $this->response($return, 400);
            }

            if($extNya == "pdf"){
                //Strip character spesial
                $DocName = preg_replace('/[^A-Za-z0-9\-]/', '', $DocName);
                $documentatt = $DocID . "_".$DocName.".".$extNya;

                //hapus dl filenya (jika ada)
                if(file_exists('files/cms_document/' . $documentatt)){
                    unlink('files/cms_document/' . $documentatt);
                }

                $fileupload['Koltiva_view_CMS_WinFormDocument-Form-DocUrlInput'] = $this->file['Koltiva_view_CMS_WinFormDocument-Form-DocUrlInput'];                
                $upload = move_upload($fileupload, 'files/cms_document/' . $documentatt);                
                if (isset($upload['upload_data'])) {

                    //Update ke tabel
                    $proses = $this->mcms->UpdateDocumentFile($DocID, $documentatt);

                    $result['success']    = true;
                    $result['file_with_rand']       = $documentatt. '?' . rand(1, 100);
                    $result['file']       = $documentatt;
                    $this->response($result, 200);
                } else {
                    $return['success'] = false;
                    $return['message'] = lang('Upload Failed');
                    $this->response($return, 400);
                }
            }else{
                $return['success'] = false;
                $return['message'] = lang('Only PDF file allowed');
                $this->response($return, 400);
            }
        }else{
            $return['success'] = false;
            $return['message'] = lang('Upload Failed');
            $this->response($return, 400);
        }
    }

    public function document_input_post(){
        $VarPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($VarPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_CMS_WinFormDocument-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        $proses = $this->mcms->UpdateDocument($ParamPost);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function document_delete(){
        $DocID = (int) $this->delete('DocID');
        $proses = $this->mcms->DeleteDocument($DocID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }
}