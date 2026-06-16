<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gallery extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js']      = 'gallery';
      $api             = $this->config->item('api');
      $data['action']  =  array(
         
         'grid'        => $api.'/monitoring/gallery',         
         'photo'       => $this->config->item('api_base_url').'images/photo_activity/',
         
         'act_index'   => !$this->system->CekAksi('index'),
         'act_cancle'  => !$this->system->CekAksi('cancle'),
         'act_add'     => !$this->system->CekAksi('add'),
         'act_update'  => !$this->system->CekAksi('update'),
         'act_delete'  => !$this->system->CekAksi('delete'),

         'api'         => str_replace('index.php','',$api));
      
      $data['style'] = "
         .search-item {
            padding     : 5px;
            white-space : normal;
            color       : #555;
            font-size   : 12px;
            line-height : 10px;
            
         }
         .search-item h3 {
            display     : block;
            font        : inherit;
            font-weight : bold;
            color       : #222;
            margin      : 5px 0;
         }
         .search-item h3 span {
            float       : right;
            font-weight : normal;
            width       : 100px;
            clear       : none;
         }";
      $this->LoadView($data);
   }

}

