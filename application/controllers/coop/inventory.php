<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
       
      $data['js'] = 'coop_inventory';
      $api = $this->config->item('api');
      $data['action'] = array(
         'apiurl'=>$api, 
         'baseurl'=>base_url(),
         'data'=>$api.'/cooperatives/getDataInventory',
         'datas'=>$api.'/cooperatives/getDataInventorys',
         'add'=>$api.'/cooperatives/inventoryadd',
         'edit'=>$api.'/cooperatives/inventoryedit',
         'delete'=>$api.'/cooperatives/inventorydelete',
         'coadatas'=> $api . '/coa/fin_coas',
         'depreciatedinv'=>$api.'/cooperatives/inventorydata',
         'countdepreciate'=>$api.'/cooperatives/inventorydata',
         'invcatlist'=>$api.'/cooperatives/invcategorys',
         'supplierlist'=>$api.'/cooperatives/suppliers',
//         'staff'=>$api.'/cooperatives/staffs',
         'param'=>$_SESSION['userid'],
          'membertype_list'=>$api.'/system/membertypelist',
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'',
         'act_save'=> !$this->system->CekAksi('update')?'hide-icon':'',
      );
      
//      print_r($_SESSION);
      $this->LoadView($data);
   }

   public function barang(){      
      //current asset
      $data['js'] = 'bussiness_barang';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/bussiness/barang',
         'api'=>$api,
         'api_base_url'=>$this->config->item('api_base_url'),
         'CoopID'=>$this->coopid(),
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }
   
   public function opname()
   {
      $data['js'] = 'coop_inventory_opname';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/inventory/barang_opname',
         'api'=>$api,
         'api_base_url'=>$this->config->item('api_base_url'),
         'CoopID'=>$this->coopid(),
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

   function coopid()
   {
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

      return $coopID;
   }

}
