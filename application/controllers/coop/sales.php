<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){

       $this->db->select('coopID');
       $this->db->from('ktv_cooperative_staff');
       $this->db->where('userId', $_SESSION['userid']);
       $Q = $this->db->get();
       if ($Q->num_rows() > 0) {
           $row = $Q->row();
           $coopID = $row->coopID;
       } else {
         $coopID = null;
       }


      $data['js'] = 'coop_sales';
      $api = $this->config->item('api');
      $data['action'] = array(
         'api'=>$api,
         'crud'=>$api.'/cooperatives/sales/',
         'retur'=>base_url.index_page().'/cooperatives/retur_sales/add/',
         'customers'=>$api.'/cooperatives/customers',
         'OrgID'=>$coopID,
         'OrgType'=>'koperasi',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $data['style'] = "
         .biggertext{font-size: 16px;font-weight:bold}"; 
      $this->LoadView($data);
   }

}
