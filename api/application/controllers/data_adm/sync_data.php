<?php
/*
* @Author: Gitandi Nadzari
* @Date: 2020-02-10 14:11:10
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_data extends REST_Controller {
    
    public function view_to_mobile_get(){
        $this->load->model('data_adm/msync_data');
        $sqlViewName = $this->get('sqlview');
        $UserName = $this->get('UserName');
        if($sqlViewName){
            if(!$UserName){
                $UserName = "";
            }
            $arrParams = array($sqlViewName, $UserName);
            $result = $this->msync_data->sqlViewToMobile($arrParams);
            if($result["success"]==0){
                $this->response($result, 404);
            } else {
                $this->response($result, 200);
            }

        } else {
            $result["success"] = 0;
            $result["error"] = "sqlViewName needed";
            $this->response($result, 400);
        }
    }
    public function send_to_mobile_get(){
        
        ini_set('display_errors',true);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        error_reporting(E_ALL);

        $this->load->model('data_adm/msync_data');
        $ProgramUid = $this->get('ProgramUid'); 
        $DateTimeFilter = $this->get('DateTimeFilter'); 
        $UserName = $this->get('UserName');
        $ExtType = $this->get('ExtType');
        $ExtUid = $this->get('ExtUid');
        
        if(!$ProgramUid){ // bisa tanpa tanggal tapi programuid juga harus ada
            $result["success"] = 0;
            $result["error"] = "Program not found";
            $this->response($result, 400);
        }
        if(!$UserName){
            $result["success"] = 0;
            $result["error"] = "UserName not found";
            $this->response($result, 400);
        }

        
        $arrParams = array($ProgramUid, $DateTimeFilter, $UserName, $ExtUid);
        
        $result = $this->msync_data->sendToMobile($arrParams);
        if($result["success"]==0){
            $this->response($result, 200);
        } else {
            $this->response($result, 200);
        }
        
    }

    public function gettrainingstatus_post() {
        
        $this->load->model('data_adm/msync_data');
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        
        $result = $this->msync_data->getTrainingStatus($data);
        $this->response($result, 200);
    }
}

?>