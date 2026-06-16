<?php

/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-18 13:43:18
 * @modify date 2020-05-18 13:43:18
 * @desc [description]
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Video extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('cms/mvideo');
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
        $Language = $this->get('Language');

		$proses = $this->mvideo->GetVideoList($page, 5, $Language);
        $this->response($proses,200);
    }
    
    public function detail_get(){
        $VidID = (int) $this->get('VidID');
        $Language = $this->get('Language');

        $data = $this->mvideo->GetVideoByID($VidID, $Language);
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

        if($ParamPost['VideoType'] == 'youtube'){
            $ParamPost['PicThumb'] = 'https://img.youtube.com/vi/'.$ParamPost['VideoTypeID'].'/hqdefault.jpg';
        } else {
            $ParamPost['PicThumb'] = null;
        }

        if($ParamPost['VidID'] == ""){
            $ParamPost['OpsiDisplay'] = 'insert';
            $proses = $this->mvideo->InsertVideo($ParamPost);
        }else{
            $ParamPost['OpsiDisplay'] = 'update';
            $proses = $this->mvideo->UpdateVideo($ParamPost);
        }

        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    public function detail_delete(){
        $VidID = (int) $this->delete('VidID');
        
        $proses = $this->mvideo->DeleteVideo($VidID);
        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }
}