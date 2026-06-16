<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Petani extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->lang->load('tools');
        $data['lang'] = $this->lang->language;
        $data['js'] = 'petani';
        $api = $this->config->item('api');
        $api_image = $this->config->item('api_base_url');
        
        $data['action'] = array(
            'cetak_kartu' => $api . '/farmer/card/',
            'cetak_sertifikat' => $api . '/farmer/certificate/',
            'crud' => $api . '/petani/petani',
            'process' => $api . '/petani/process_photo',
            'photo_history' => $api . '/petani/photo_history',
            'photo_path' => $api_image . '/images/member/',
            'Kabupaten' => $api . '/farmer/Kabupatens',
            'Provinsi' => $api . '/farmer/Provinsis',
            'act_index' => !$this->system->CekAksi('index'),
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_cancel' => !$this->system->CekAksi('cancel'),
            'act_delete' => !$this->system->CekAksi('delete'));
        $data['style'] = "
         .error .x-grid-cell { 
             background-color: #F2DEDE;
             color: #333;
         }           
         .no-error .x-grid-cell { 
             background-color: #DFF0D8;
             color: #333;
         }
      ";
        $this->LoadView($data);
    }

}
