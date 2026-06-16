<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_new extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('msupplychain_new', '_model');
        // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'dummy'));
    }

    public function supplychain_get()
    {
        $province       = $this->get('province');
        $partner        = $this->get('partner');
        $warehouse      = $this->get('warehouse');
        $certification  = $this->get('certification');
        $start          = $this->get('start');
        $end            = $this->get('end');

        $data = $this->_model->getSupplyChain($start, $end, $province, $partner, $certification, $warehouse);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_cpg_get()
    {
        $traderid   = $this->get('traderid');
        $start      = $this->get('start');
        $end        = $this->get('end');

        $data = $this->_model->getSupplyChainCPG($traderid, $start, $end);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_farmer_get()
    {
        $id         = $this->get('id');
        $supply_id  = $this->get('supply_id');
        $partner    = $this->get('partner');
        $certification    = $this->get('certification');
        $start      = $this->get('start');
        $end        = $this->get('end');
        $wh         = $this->get('warehouse');

        $data = $this->_model->getSupplyChainFarmer($id, $supply_id, $start, $end, $partner, $certification, $wh);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function partner_get()
    {
        $ProvinceID     = $this->get('ProvinceID');
        $data = $this->_model->getProvincePartner($ProvinceID);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }
    
    public function new_partner_get()
    {
        //$ProvinceID     = $this->get('ProvinceID');
        $data = $this->_model->getNewProvincePartner($_SESSION['PartnerID']);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }

    public function warehouse_get()
    {
        $PartnerID     = $this->get('PartnerID');
        $data = $this->_model->getPartnerWarehouse($PartnerID);
        $this->response($data, 200);
    }

    public function garden_detail_get()
    {
        $FarmerID = $this->get('FarmerID');
        $GardenNr = $this->get('GardenNr');
        $data = $this->_model->getGardenDetail($FarmerID, $GardenNr);
        $this->response($data, 200);
    }

    public function supply_profile_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyProfileFarmer($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

    public function supply_transaction_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyTransactionFarmer($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

    public function supply_profile_koperasi_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyProfileKoperasi($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_koperasi_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyTransactionKoperasi($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_profile_sce_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyProfileSCE($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_sce_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyTransactionSCE($id, $start, $end, $wh, $cert);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }

    public function supply_profile_trader_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyProfilePedagang($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_trader_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyTransactionPedagang($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_profile_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyProfileWarehouse($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->_model->getWarehouseID($this->get('partner'));
        $data = $this->_model->getSupplyTransactionWarehouse($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function kml_farmer_list_get()
    {
        $Province   = $this->get('Province');
        $District   = $this->get('District');
        $cpg        = $this->get('cpg');
        $data       = $this->_model->getKMLFarmerList($Province, $District, $cpg);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        if (!empty($data)) {
            $all[0] = array('id' => 'all', 'label' => 'All');
            $this->response(array_merge($all, $data), 200);
        }
        // $this->response($data, 200);
    }

    public function kml_kabupaten_get()
    {
        $this->response($this->_model->getKMLKabupaten($this->get('key')),200);
    }

    public function kml_cpg_get()
    {
        $this->response($this->_model->getKMLCPG($this->get('kab')),200);
    }

}
