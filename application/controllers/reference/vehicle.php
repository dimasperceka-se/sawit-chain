<?php
/*
 * @Author: sonny.fitriawan 
 * @Date: 2017-12-07 14:13:16 
 * @Last Modified by:   sonny.fitriawan 
 * @Last Modified time: 2017-12-07 14:13:16 
 */
class Vehicle extends SS_Controller {
    
       public function __construct() {
          parent::__construct();
       }
    
       public function index() {
          $data['js'] = 'reference/vehicle';
          $api = $this->config->item('api');
          $data['action'] = array(
             'crud' => $api.'/reference/vehicle',
             'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
             'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
             'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'');
          $this->LoadView($data);
       }
    }
?>