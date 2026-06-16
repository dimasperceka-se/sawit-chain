<?php 


class Formgenerator extends REST_Controller {

	function __construct()
	{
				parent::__construct();
        $this->load->model('formgenerator/mformgenerator');
	}

	public function getPrograms_get($id = null,$sectionId = null){
		$programName = $this->mformgenerator->getPrograms($id,$sectionId);
		$data['data'] = $programName;
		$data['total'] = count($programName);
    return $this->response($data, 200);

	}

	public function createFile_post($program,$items){
		ini_set("display_errors", "1");
		error_reporting(E_ALL);

		$section = json_decode($this->post('section'));
		$items = json_decode($this->post('items'));

		for ($i=0; $i < count($section); $i++) { 
			$programId = $section[0]->programid;
			$sectionId = $section[$i]->sectionId;
			$tabName = $section[$i]->sectionName;
			$query[$tabName]= $this->mformgenerator->getPrograms($programId,$sectionId);
		}
		$programName = $section[0]->programName;
			// print_r($query);
		$data['data'] = $query;
		$data['programName'] = $programName;

		$datas = $this->load->view('viewgenerator/view',$data,true);
		$appDirectory = getcwd().'/../js/app/view/Generator/'.str_replace(' ', '',$programName).'.js';
		$fopen = fopen($appDirectory, "w");
		fputs($fopen,$datas,strlen($datas));
		fclose($fopen);
		exit;
	}


}

?>