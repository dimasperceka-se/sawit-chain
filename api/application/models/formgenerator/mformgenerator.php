<?php 

class Mformgenerator extends CI_Model {

 function getPrograms($id = null,$sectionId = null){
 	// echo "string";
 	$joinElement = ($sectionId != null ) ? ',mw_dataelement.name as elementName, mw_dataelement.valuetype as xtype' : '';
 	$joinSection = ($id != null) ? ',mw_programstagesection.name as sectionName,mw_programstagesection.programstagesectionid as sectionId'.$joinElement : '';
 	$this->db->select('mw_program.programid as programid,mw_program.name as programName'.$joinSection);
 	if($id){
 		$this->db->join('mw_programstage', 'mw_program.programid = mw_programstage.programid', 'left');
 		$this->db->join('mw_programstagesection', 'mw_programstagesection.programstageid = mw_programstage.programstageid', 'left');
 		if($sectionId){
	 		$this->db->join('mw_programstagedataelement', 'mw_programstagedataelement.programstagesectionid = mw_programstagesection.programstagesectionid', 'left');
			$this->db->join('mw_dataelement', 'mw_dataelement.dataelementid = mw_programstagedataelement.dataelementid','left');
	 		$this->db->where('mw_programstagesection.programstagesectionid', $sectionId);
 		}
 		$this->db->where('mw_program.programid', $id);
 	}
 		return $this->db->get('mw_program')->result_array();
 		// exit;
	}
}

?>