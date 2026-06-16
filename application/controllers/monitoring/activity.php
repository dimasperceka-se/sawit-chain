<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class activity extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js']      = 'activity';
      $api             = $this->config->item('api');
      $data['action']  = array(
         'crud'        => $api.'/monitoring/activity',
         'grid'        => $api.'/monitoring/activity_grid',         
                  
         'objectid'    => $api.'/monitoring/categoryid',
         'foto_grid'   => $api.'/monitoring/foto_grid',
         'foto_read'   => $api.'/monitoring/foto_read',
         'foto'        => $api.'/monitoring/foto', 
         'Vfoto'       => $api.'/monitoring/Vfoto_image', 
         
         'param'       => $id, 
         'photo'       => $this->config->item('api_base_url').'images/photo_activity/',

         'Desa'        =>$api.'/monitoring/Desas',
         'Kecamatan'   =>$api.'/monitoring/Kecamatans',
         'Kabupaten'   =>$api.'/monitoring/Kabupatens',
         'Provinsi'    =>$api.'/monitoring/Provinsis',
               
         'act_add'     => !$this->system->CekAksi('add'),
         'act_cancle'  => !$this->system->CekAksi('cancle'),
         'act_update'  => !$this->system->CekAksi('update'),
         'act_delete'  => !$this->system->CekAksi('delete'));
         
      $data['style'] = "
         input.l-tcb {
            height      : 13px;
            width       : 13px;
            margin-left : 2px
         }
         .ext-ie .x-tree {
            position    : static !important;
         }";
      $this->LoadView($data);
   }

}

