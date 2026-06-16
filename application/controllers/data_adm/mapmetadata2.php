<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: Gitandi Nadzari
 * @Date:   2018-09-10 16:20:00
 */
class Mapmetadata2 extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }
 
   public function index() {
      $data['js'] = 'data_adm/mappingmetadata';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/data_adm/map_metadata/mapdataelemen',
     // 'prog_stage'=> $api.'/map_metadata/prog_stage',
      'program'=> $api.'/data_adm/map_metadata/program',
      'metadata'=> $api.'/data_adm/map_metadata/metadata_grid',
      'selmetadata'=> $api.'/data_adm/map_metadata/selmetadata_grid',
      'tablereff'=> $api.'/data_adm/map_metadata/tablereff',
      'columnreff'=> $api.'/data_adm/map_metadata/columnreff',
      'dataelementreff'=> $api.'/data_adm/map_metadata/dataelementreff',
      'routine'=> $api.'/data_adm/map_metadata/routine',
      'rowmetadata_form'=> $api.'/data_adm/map_metadata/rowmetadata_form',
      'updatepullinfo'=> $api.'/data_adm/map_metadata/updatepullinfo',
      'reloadmetadatakafka'=> $api.'/data_adm/map_metadata/reloadmetadatakafka',
		// 'sync_metadata'=> $api.'/map_metadata/sync_metadata',
      'act_add'=> !$this->system->CekAksi('add'),
      'act_update'=> !$this->system->CekAksi('update'),
      'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

