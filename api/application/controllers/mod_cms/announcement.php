<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:43:18
 * @modify date 2020-05-18 13:43:18
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Announcement extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('cms/mannouncement');
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

		$proses = $this->mannouncement->GetAnnouncementList($page, 5, $language);
        $this->response($proses,200);
    }
    public function detail_get(){
        $AnnID = (int) $this->get('AnnID');
        $Language = $this->get('Language');
        $data = $this->mannouncement->GetAnnouncementDetail($AnnID, $Language);
        $this->response($data, 200);
    }

    public function detail_post(){
        $ParamPost   = $this->post();
        
        $ParamPost['Content'] = html_entity_decode($ParamPost['Content']);
        $ParamPost['StatusPublish'] = $ParamPost['submit'];
        
        //If status type = private
        if($ParamPost['StatusType'] == 'Private'){
            $ParamPost['PartnerIDImplode'] = implode(',',$ParamPost['PartnerIDImplode']);
            if(isset($ParamPost['RoleAccess'])){
                foreach($ParamPost['RoleAccess'] as $key => $role){
                    $ParamPost[$role] = 1;
                }
                unset($ParamPost['RoleAccess']);
            }
        }

        if($ParamPost['AnnID'] == ""){
            $ParamPost['OpsiDisplay'] = 'insert';
            $proses = $this->mannouncement->InsertAnnouncement($ParamPost);
        }else{
            $ParamPost['OpsiDisplay'] = 'update';
            $proses = $this->mannouncement->UpdateAnnouncement($ParamPost);
        }

        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function detail_delete(){
        $AnnID = (int) $this->delete('AnnID');
        
        $proses = $this->mannouncement->DeleteAnnouncement($AnnID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

}