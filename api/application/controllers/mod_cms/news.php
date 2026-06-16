<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:43:18
 * @modify date 2020-05-18 13:43:18
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class News extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('cms/mnews');
        $this->load->model('basic/mbatch');
        $this->load->library('awsfileupload');
        $this->load->helper('text');

        require 'application/third_party/htmlpurifier-4.10.0/HTMLPurifier.standalone.php';
        $configPurifier = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($configPurifier);
    }

    public function list_get(){
        //Get Param
        $page = (int) $this->get('Page');
        //$limit = (int) $this->get('limit');
        $language = $this->get('Language');
        $search   = empty($this->get('search')) ? null : $this->get('search');

        $proses = $this->mnews->GetNewsList($page, 5, $language, $search);
        $this->response($proses,200);
        //echo $return; exit;
    }

    public function detail_get(){
        $NewsID   = (int) $this->get('NewsID');
        $Language = $this->get('Language');

        $data = $this->mnews->GetNewsDetail($NewsID, $Language);
        $this->response($data, 200);
    }

    public function detail_post(){
        $this->load->library('awsfileupload');
        
        $ParamPost   = $this->post();
        $ParamPost['Content'] = html_entity_decode($ParamPost['Content']);
        $ParamPost['StatusPublish'] = $ParamPost['submit'];

        if($ParamPost['StatusType'] == 'Private'){
            if (!isset($ParamPost['PartnerIDImplode'])) {
                $proses['success'] = false;
                $proses['message'] = lang('partner access is required');

                return $this->response($proses,400);
            }

            $ParamPost['PartnerIDImplode'] = array_values(array_unique($ParamPost['PartnerIDImplode'], SORT_REGULAR));
            $ParamPost['PartnerIDImplode'] = implode(',',$ParamPost['PartnerIDImplode']);

            if(isset($ParamPost['RoleAccess'])){
                $ParamPost['RoleAccess'] = array_values(array_unique($ParamPost['RoleAccess'], SORT_REGULAR));

                foreach($ParamPost['RoleAccess'] as $key => $role){
                    $ParamPost[$role] = 1;
                }

                unset($ParamPost['RoleAccess']);
            } else {
                $proses['success'] = false;
                $proses['message'] = lang('role access is required');

                return $this->response($proses,400);
            }
        }

        if (!empty($_FILES['file'])) {
            $ExtNya = GetFileExt($_FILES['file']['name']);
            if (!in_array($ExtNya, array('png', 'jpg', 'jpeg', 'gif'))) {
                $result['success'] = false;
                $result['message'] = lang('File types not allowed');
                $this->response($result, 400);
            } else {
                if ($this->file['file']['name'] != '') {

                    $upload = $this->awsfileupload->upload($this->file['file']['tmp_name'],$this->file['file']['name'], AWSS3_NEWS_IMAGE, 'images');
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('OldFile')) == true) {
                            $this->awsfileupload->delete($this->post('OldFile'));
                        }else{
                            delete_file($this->post('OldFile'));
                        }
                        $result['success'] = true;
                        $result['message'] = lang('File uploaded');
                        $result['file'] = $upload['fileurl'];
                        $result['filepath']   = $upload['filenamepath'];

                        $ParamPost['PhotoFile'] = $upload['filenamepath'];
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                    }
                }
            }
        } else {
            $ParamPost['PhotoFile'] = ($ParamPost['OldFile'] != null) ? $ParamPost['OldFile'] : null;
        }

        $ParamPost['Attachment'] = array();
        
        for ($i = 1; $i <= 12; $i++) {
            if (!empty($_FILES['upload_file' . $i])) {
                $ExtNya = $this->awsfileupload->getTypeOfFile($_FILES['upload_file' . $i]['name']);
                if (!in_array($ExtNya, array('images', 'documents'))) {
                    $result['success'] = false;
                    $result['message'] = lang('File types not allowed');
                    $this->response($result, 400);
                } else {
                    if ($this->file['upload_file' . $i]['name'] != '') {
                        $upload = $this->awsfileupload->upload($_FILES['upload_file' . $i]['tmp_name'], $_FILES['upload_file' . $i]['name'], AWSS3_NEWS_IMAGE, 'images');
                        if ($upload['success'] == false) {
                            $result['success'] = false;
                            $result['message'] = lang('Upload attachment failed');
                            $this->response($result, 400);
                        } else  {
                            $ParamPost['Attachment'][$i] = $upload['filenamepath'];
                            $ParamPost['OriginalAttachmentName'][$i] = $_FILES['upload_file' . $i]['name'];
                        }
                    }
                }
            }
        }

        if($ParamPost['NewsID'] == ""){
            $ParamPost['OpsiDisplay'] = 'insert';
            $proses = $this->mnews->InsertNews($ParamPost);
        }else{
            $ParamPost['OpsiDisplay'] = 'update';
            $proses = $this->mnews->UpdateNews($ParamPost);
        }

        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function partner_get(){
        $partners = $this->mbatch->readPartners();

        $this->response($partners, 200);
    }

    public function detail_delete(){
        $NewsID = (int) $this->delete('NewsID');

        $proses = $this->mnews->DeleteNews($NewsID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function attachment_delete()
    {
        // debugCode($this->delete());
        $CmsAttachmentID = (int) $this->delete('CmsAttachmentID');

        $proses = $this->mnews->DeleteAttachmentByAttachmentID($CmsAttachmentID, $this->userSess->UserId);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

}