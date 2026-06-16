<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:43:18
 * @modify date 2020-05-18 13:43:18
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Document extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('cms/mdocument');
        $this->load->library('awsfileupload');
        $this->load->helper('text');

        require 'application/third_party/htmlpurifier-4.10.0/HTMLPurifier.standalone.php';
        $configPurifier = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($configPurifier);
    }

    public function list_get(){
		//Get Param
		$page     = (int) $this->get('Page');
        //$limit = (int) $this->get('limit');
        $language = $this->get('Language');
        $search   = empty($this->get('search')) ? null : $this->get('search');

		$proses = $this->mdocument->GetDocumentList($page, 5, $language, $search);
        $this->response($proses,200);
    }

    public function detail_get(){
        $DocID = (int) $this->get('DocID');
        $language = $this->get('Language');
        $data = $this->mdocument->GetDocumentDetail($DocID, $language);
        $this->response($data, 200);
    }

    public function detail_post(){
        $ParamPost   = $this->post();
        
        $ParamPost['Description'] = html_entity_decode($ParamPost['Description']);
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

        if(!empty($_FILES)){
            $ExtNya = GetFileExt($_FILES['file']['name']);
            if (!in_array($ExtNya, array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'pptx'))) {
                $result['success'] = false;
                $result['message'] = lang('File types not allowed');
                $this->response($result, 400);
            } else {
                if ($this->file['file']['name'] != '') {
                    //Untuk AWS S3, wajib ada
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload($_FILES['file']['tmp_name'],$_FILES['file']['name'], AWSS3_CMSDOC_PATH, 'documents');
                    if ($upload['success'] == false) {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                        $this->response($result, 400);
                    }
                    $ParamPost['DocUrl'] = $upload['filenamepath'];
                }
            }
        } else {
            $ParamPost['DocUrl'] = ($ParamPost['OldFile'] != null) ? $ParamPost['OldFile'] : null;
        }

        if($ParamPost['DocID'] == ""){
            $ParamPost['OpsiDisplay'] = 'insert';
            $proses = $this->mdocument->InsertDocument($ParamPost);
        }else{
            $ParamPost['OpsiDisplay'] = 'update';
            $proses = $this->mdocument->UpdateDocument($ParamPost);
        }

        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function detail_delete(){
        $DocID = (int) $this->delete('DocID');
        $proses = $this->mdocument->DeleteDocument($DocID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

}

