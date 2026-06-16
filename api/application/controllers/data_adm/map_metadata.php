<?php

/**
 * @Author: Gitandi Nadzari
 * @Date:   2018-09-10 16:20:00
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Map_metadata extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->model('data_adm/m_metadata');
		 $this->load->model('data_adm/m_metadata');
    }
	//mapdataelemen
	function mapdataelemens_get() {
        $mapping = $this->m_metadata->readMapProgStageDataElements($this->get('key'),$this->get('ProgStageId'),$this->get('start'), $this->get('limit'));
        if ($mapping)
            $this->response($mapping, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Mapping between Program Stage and Data Element!'), 404);
    }

    function mapdataelemen_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $unit = $this->m_metadata->readMapProgStageDataElement($this->get('id'));
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Mapping between Program Stage and Data Element could not be found'), 404);
    }
	
    function mapdataelemen_put() {
        if (!$this->put('programstagedataelementid'))
            $this->response(NULL, 400);
			$unit = $this->m_metadata->updateMapProgStageDataElement($this->put('programstagedataelementid'), $this->put('reference_field'), $this->put('reference_display'), $this->put('custom'), $_SESSION['userid']);
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Mapping between Program Stage and Data Element could not be found'), 404);
    }
/*
    public function prog_stages_get()
    {
        $data = $this->m_metadata->mwProgramStages($this->get('ProgStageId'),$this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }*/
    
    public function prog_stages_get()
    {
        $data = $this->m_metadata->mwProgramStages($this->get('ProgStageId'),$this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

	public function prog_stage_get()
    {
        $data = $this->m_metadata->mwProgramStage();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

    public function program_get()
    {
        $data = $this->m_metadata->mwProgram();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

    public function metadata_grid_get()
    {
        $data = $this->m_metadata->mwMetadataGrid($this->get('ProgStageId'),$this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

    public function tablereff_get()
    {
        $data = $this->m_metadata->table_reff();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

    public function routine_get()
    {
        $data = $this->m_metadata->routine();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

    public function columnreff_get()
    {
        $data = $this->m_metadata->column_reff($this->get('TableName'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }

    public function metadata_form_open_get(){
       $MappingId = (int) $this->get('MappingId');
        $data = $this->m_metadata->GetMetadataFormOpen($MappingId);
        $this->response($data, 200);
    }

    public function MappingData_form_delete(){
        $MappingId = (int) $this->delete('MappingId');
        $proses = $this->m_metadata->DeleteMappingData($MappingId);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }
    public function metadata_form_post(){
  
        //Prep Var (Begin)
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_DataAdm_MainForm-FormBasicData-",'', $key);
            if ($value == "") {
                $value = null;
            }          
            $paramPost[$keyNew] = $value;
        }
     
        //Prep Var (End)
        
        if ($paramPost['new'] ==1) {
            $Proses = $this->m_metadata->InsertMappingData($paramPost);
        }else{
          
            $Proses = $this->m_metadata->UpdateMappingData($paramPost);
        }
        
        if($Proses['success'] == true){
            $this->response($Proses, 200);
        }else{
            $this->response($Proses, 400);
        }
    }


	public function prog_stage_put() {
        if (!$this->put('programid'))
            $this->response(NULL, 400);
			$unit = $this->m_metadata->updateMwProgram($this->put('programid'), $this->put('programName'), $this->put('description'), 
				$this->put('reference'), $this->put('status'), $this->put('order'), $_SESSION['userid']);
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Mw Program could not be found'), 404);
    }
	public function sync_metadata_get()
    {
        $data = $this->m_metadata->syncMetadataRecord();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
        
    }
    function testpgconnection_get() {
        $data = $this->m_metadata->testPgConnection();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t connect!'), 404);
        }
    }

    public function selmetadata_grid_get()
    {
        $data = $this->m_metadata->mwMetadataSelectedGrid($this->get('ProgStageId'),$this->get('MappingId'),$this->get('DeUid'),$this->get('tblReff'),$this->get('fieldReff'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any selected metadata!'), 404);
        }
        
    }
    public function dataelementreff_get()
    {
        $data = $this->m_metadata->dataelement_reff($this->get('ProgStageId'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data element!'), 404);
        }
        
    }
    public function rowmetadata_form_post(){
  
        //Prep Var (Begin)
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }          
            $paramPost[$key] = $value;
        }
        // echo print_r($paramPost);
        // die();
        if ($paramPost['mw_mapping_id'] == null) {
            $Proses = $this->m_metadata->InsertMappingData($paramPost);
        }else{
          
            $Proses = $this->m_metadata->UpdateMappingData($paramPost);
        }
        
        // echo print_r($varPost, true);
        // exit;
        // $data = $varPost;
        if($Proses['success'] == true){
            $this->response($Proses, 200);
        }else{
            $this->response($Proses, 400);
        }
        // if ($data) {
        //     $this->response($data, 200);
        // } else {
        //     $this->response(array('error' => 'Couldn\'t find any data element!'), 404);
        // }

    }
    public function updatepullinfo_post(){
  
        //Prep Var (Begin)
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }          
            $paramPost[$key] = $value;
        }
        // echo print_r($paramPost);
        // die();
        $Proses = $this->m_metadata->UpdatePullInfo($paramPost);
    
        if($Proses['success'] == true){
            $this->response($Proses, 200);
        }else{
            $this->response($Proses, 400);
        }
    }
    public function reloadmetadatakafka_get()
    {
        $data = $this->m_metadata->reloadMetadataByKafka();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t get result data!'), 404);
        }
        
    }
}
?>