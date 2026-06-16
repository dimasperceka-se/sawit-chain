<?php defined('BASEPATH') or exit('No direct script access allowed');

class Bussiness extends REST_Controller
{

    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('bussiness/msale');
        $this->load->model('bussiness/mpurchase');
        $this->load->model('bussiness/mretursale');
        $this->load->model('bussiness/mreturpurchase');
        $this->load->model('bussiness/mbarang');
        $this->load->model('bussiness/mwowfarm');
        $this->load->model('bussiness/mcustomer');
        $this->load->model('bussiness/msupplier');
        $this->load->model('bussiness/mcategory');
        $this->load->model('bussiness/munit');
    }

    //penjualan
    public function penjualans_get()
    {
        $data = $this->msale->readPenjualans($this->get('Awal'), $this->get('Akhir'), $this->get('OrgType'),
            $this->get('OrgID'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function penjualan_get()
    {
        if (!$this->get('SaleID')) {
            $this->response(null, 400);
        }

        $data = $this->msale->readPenjualan($this->get('SaleID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_post()
    {
        $data = $this->msale->createPenjualan($this->post('Date'), $this->post('OrgType'), $this->post('OrgID'), $this->post('CustomerID'),
            $this->post('Total'), $this->post('Pay'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_put()
    {
        $data = $this->msale->updatePenjualan($this->put('Date'), $this->put('Total'), $this->put('Pay'), $this->put('SaleID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->msale->deletePenjualan($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //detail
    public function penjualan_detail_get()
    {
        $data = $this->msale->readPenjualanDetail($this->get('id'));
        $this->response($data, 200);
    }
    public function penjualan_detail_post()
    {
        $data = $this->msale->createPenjualanDetail($this->post('SaleID'), $this->post('InventoryID'),
            $this->post('Problem'), $this->post('Solution'), $this->post('DateStart'), $this->post('DateEnd'), $this->post('Qty'),
            $this->post('Price'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_detail_put()
    {
        $data = $this->msale->updatePenjualanDetail($this->put('InventoryID'), $this->put('Problem'), $this->put('Solution'),
            $this->put('DateStart'), $this->put('DateEnd'), $this->put('Qty'), $this->put('Price'),
            $this->put('DetailID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_detail_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->msale->deletePenjualanDetail($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //end detail
    public function penjualan_org_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->msale->readPenjualanOrg($this->get('OrgType'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'data could not be found'), 404);
        }

    }
    public function penjualan_buyer_org_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->msale->readPenjualanBuyerOrg($this->get('BuyerOrgType'), $this->get('OrgType'), $this->get('OrgID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_customer_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->msale->readPenjualanCustomer($this->get('OrgType'), $this->get('OrgID'));
        $data = array_merge(array(array('id' => '-1', 'label' => '[Tambah Baru]')), $data);
        $this->response($data, 200);
    }
    public function penjualan_barang_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->msale->readPenjualanBarang($this->get('OrgType'), $this->get('OrgID'), $this->get('Name'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function penjualan_cetak_get($id)
    {
        $data['data']   = $this->msale->readPenjualan($this->get('SaleID'));
        $data['detail'] = $this->msale->readPenjualanDetail($this->get('SaleID'));
        $this->load->view('cetak_penjualan', $data);
    }
    public function penjualan_customer_post()
    {
        $data = $this->msale->createSaleCustomer($this->post('FarmerID'), $this->post('CustOrgType'), $this->post('CustOrgID'), $this->post('CustName'),
            $this->post('CustEmail'), $this->post('CustAddress'), $this->post('CustPhone'), $this->post('CustNote'),
            $this->post('Provinsi'), $this->post('Kabupaten'), $this->post('Kecamatan'), $this->post('Desa'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }

    //barang
    public function barangs_get()
    {
        $sce_id = getSceID();
        $data = $this->mbarang->readBarangs($sce_id, $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function barang_get()
    {
        if (!$this->get('InventoryID')) {
            $this->response(null, 400);
        }

        $data = $this->mbarang->readBarang($this->get('InventoryID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }

    public function barang_combo_autocom_get(){
        $sce_id = getSceID();
        $data = $this->mbarang->readBarangComboAutocom($sce_id, $this->get('query'),$this->get('InventoryID'),$this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data not found'), 404);
        }
    }

    public function barang_barang_get()
    {
        $data = $this->mbarang->readBarangBarang($this->get('OrgType'), $this->get('OrgID'), $this->get('query'),
            $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }

    public function barang_post()
    {
        $sce_id = getSceID();

        if ($this->post('InventoryID') == '') {
            $data = $this->mbarang->createBarang($sce_id, $this->post('UnitMeasurementID'),
                $this->post('Name'), $this->post('Number'), $this->post('CategoryID'), $this->post('SupplierID'),
                $this->post('IsSell'), $this->post('IsPaket'), $this->post('IsInventory'), $this->post('ParentInventoryID'), $this->post('ParentConvertion'),
                str_replace(',', '', $this->post('Cost')), str_replace(',', '', $this->post('SalePrice')), $this->post('Photo_old'));
        } else {
            $data = $this->mbarang->updateBarang(
                $this->post('UnitMeasurementID'),
                $this->post('Name'),
                $this->post('Number'),
                $this->post('CategoryID'),
                $this->post('SupplierID'),
                $this->post('IsSell'),
                $this->post('IsPaket'),
                $this->post('IsInventory'),
                str_replace(',', '', $this->post('Cost')),
                str_replace(',', '', $this->post('SalePrice')),
                $this->post('Photo_old'),
                $this->post('InventoryID'));
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function barang_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mbarang->deleteBarang($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Delete failed'), 404);
        }

    }
    //detail
    public function barang_detail_get()
    {
        $data = $this->mbarang->readBarangDetail($this->get('InventoryID'));
        $this->response($data, 200);
    }
    public function barang_detail_post()
    {
        $data = $this->mbarang->createBarangDetail($this->post('InventoryID'), $this->post('ChildInventoryID'), $this->post('Qty'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function barang_detail_put()
    {
        $data = $this->mbarang->updateBarangDetail($this->put('ChildInventoryID'), $this->put('Qty'), $this->put('PaketID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function barang_detail_delete()
    {
        if (!$this->delete('PaketID')) {
            $this->response(null, 400);
        }

        $data = $this->mbarang->deleteBarangDetail($this->delete('PaketID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }

    }
    //end detail

    public function barang_supplier_popup_post()
    {
        $sce_id = getSceID();

        $data = $this->mbarang->createBarangSupplierPopup($sce_id, $this->post('SuppName'),
            $this->post('SuppEmail'), $this->post('SuppAddress'), $this->post('SuppPhone'), $this->post('SuppNote'),
            $this->post('Provinsi'), $this->post('Kabupaten'), $this->post('Kecamatan'), $this->post('Desa'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data saved failed'), 404);
        }
    }

    public function barang_supplier_post()
    {
        $data = $this->mbarang->createBarangSupplier($this->post('SuppOrgType'), $this->post('SuppOrgID'), $this->post('SuppName'),
            $this->post('SuppEmail'), $this->post('SuppAddress'), $this->post('SuppPhone'), $this->post('SuppNote'),
            $this->post('Provinsi'), $this->post('Kabupaten'), $this->post('Kecamatan'), $this->post('Desa'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }

    public function supplier_combo_get(){
        $sce_id = getSceID();
        $data = $this->mbarang->getSupplierComboData($sce_id);
        $data = array_merge(array(array('id' => '-1', 'label' => '[Add New Data]')), $data);
        $this->response($data, 200);
    }

    public function supplier_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->mbarang->readSupplier($this->get('OrgType'), $this->get('OrgID'));
        $data = array_merge(array(array('id' => '-1', 'label' => '[Tambah Baru]')), $data);
        $this->response($data, 200);
    }

    public function barang_category_popup_post()
    {
        $sce_id = getSceID();
        $data = $this->mbarang->createBarangCategoryPopup($sce_id, $this->post('CatName'),$this->post('CatNote'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data save failed'), 404);
        }

    }

    public function barang_category_post()
    {
        $data = $this->mbarang->createBarangCategory($this->post('CatOrgType'), $this->post('CatOrgID'), $this->post('CatName'),
            $this->post('CatNote'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }

    public function kategori_combo_get(){
        $sce_id = getSceID();
        $data = $this->mbarang->getKategoriComboData($sce_id);
        $data = array_merge(array(array('id' => '-1', 'label' => '[Add New Data]')), $data);
        $this->response($data, 200);
    }

    public function category_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->mbarang->readCategory($this->get('OrgType'), $this->get('OrgID'));
        $data = array_merge(array(array('id' => '-1', 'label' => '[Tambah Baru]')), $data);
        $this->response($data, 200);
    }
    public function barang_image_post()
    {
        if ($this->file['Photo']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/Photo/sce/items/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/Photo/sce/items/' . $this->post('Photo_old'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            } else {
                // drop error here
            }
        }
    }

    //pembelian

    //wowfarm
    public function wowfarms_get()
    {
        $data = $this->mwowfarm->readDatas($this->get('Awal'), $this->get('Akhir'), $this->get('OrgType'), $this->get('OrgID'),
            $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function wowfarm_get()
    {
        if (!$this->get('DemoplotID')) {
            $this->response(null, 400);
        }

        $data = $this->mwowfarm->readData($this->get('DemoplotID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function wowfarm_post()
    {
        $data = $this->mwowfarm->createData($this->post('OrgType'), $this->post('OrgID'), $this->post('DateHarvest'),
            $this->post('AmountWetBeans'), $this->post('DateSales'), $this->post('DryingDay'), $this->post('AmountDryBeans'),
            $this->post('Price'), $this->post('Total'), $this->post('Description'), $this->post('BuyerOrgType'), $this->post('BuyerOrgID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function wowfarm_put()
    {
        $data = $this->mwowfarm->updateData($this->put('DateHarvest'), $this->put('AmountWetBeans'), $this->put('DateSales'),
            $this->put('DryingDay'), $this->put('AmountDryBeans'), $this->put('Price'), $this->put('Total'),
            $this->put('Description'), $this->put('BuyerOrgType'), $this->put('BuyerOrgID'), $this->put('DemoplotID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function wowfarm_delete()
    {
        if (!$this->delete('DemoplotID')) {
            $this->response(null, 400);
        }

        $data = $this->mwowfarm->deleteData($this->delete('DemoplotID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }

    //customer
    public function customers_get()
    {
        $data = $this->mcustomer->readDatas($this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function customer_get()
    {
        if (!$this->get('CustomerID')) {
            $this->response(null, 400);
        }

        $data = $this->mcustomer->readData($this->get('CustomerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function customer_post()
    {
        $data = $this->mcustomer->createData($this->post('OrgType'), $this->post('OrgID'), $this->post('FarmerID'),
            $this->post('Name'), $this->post('Email'), $this->post('Phone'), $this->post('Address'),
            $this->post('Note'), $this->post('Desa'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function customer_put()
    {
        $data = $this->mcustomer->updateData($this->put('OrgType'), $this->put('OrgID'), $this->put('Name'), $this->put('Email'), $this->put('Phone'),
            $this->put('Address'), $this->put('Note'), $this->put('Desa'), $this->put('CustomerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function customer_delete()
    {
        if (!$this->delete('CustomerID')) {
            $this->response(null, 400);
        }

        $data = $this->mcustomer->deleteData($this->delete('CustomerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }

    //supplier
    public function suppliers_get()
    {
        $data = $this->msupplier->readDatas($this->get('start'), $this->get('limit'), getSceID());
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }

    public function supplier_s_get()
    {
        if (!$this->get('SupplierID')) {
            $this->response(null, 400);
        }

        $data = $this->msupplier->readData($this->get('SupplierID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function supplier_post()
    {
        $data = $this->msupplier->createData($this->post('OrgType'), $this->post('OrgID'), $this->post('Name'),
            $this->post('Address'), $this->post('Phone'), $this->post('Email'), $this->post('VillageID'),
            $this->post('Note'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function supplier_put()
    {
        $data = $this->msupplier->updateData($this->put('Name'), $this->put('Address'), $this->put('Phone'),
            $this->put('Email'), $this->put('VillageID'), $this->put('Note'), $this->put('SupplierID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function supplier_delete()
    {
        if (!$this->delete('SupplierID')) {
            $this->response(null, 400);
        }

        $data = $this->msupplier->deleteData($this->delete('SupplierID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }

    //category
    public function categorys_get()
    {
        $sce_id = getSceID();
        $data = $this->mcategory->readDatas($sce_id,$this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function category_parent_get()
    {
        $data = $this->mcategory->readParentDatas($this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function category_c_get()
    {
        if (!$this->get('CategoryID')) {
            $this->response(null, 400);
        }

        $data = $this->mcategory->readData($this->get('CategoryID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function category_post()
    {
        $sce_id = getSceID();
        $data = $this->mcategory->createData($sce_id, $this->post('ParentCategoryID'), $this->post('Name'),
            $this->post('Description'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function category_put()
    {
        $data = $this->mcategory->updateData($this->put('Name'), $this->put('Description'), $this->put('CategoryID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function category_delete()
    {
        if (!$this->delete('CategoryID')) {
            $this->response(null, 400);
        }

        $data = $this->mcategory->deleteData($this->delete('CategoryID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //unit

    public function barang_unit_popup_post(){
        $sce_id = getSceID();
        $data = $this->mbarang->createBarangUnitPopup($sce_id, $this->post('UnitName'),$this->post('UnitNote'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Save failed'), 404);
        }
    }

    public function barang_unit_post()
    {
        $data = $this->mbarang->createBarangUnit($this->post('UnitOrgType'), $this->post('UnitOrgID'), $this->post('UnitName'),$this->post('UnitNote'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }
    }

    public function unit_combo_get(){
        $sce_id = getSceID();
        $data = $this->munit->getUnitComboData($sce_id);
        $data = array_merge(array(array('id' => '-1', 'label' => '[Add New Data]')), $data);
        $this->response($data, 200);
    }

    public function unit_get()
    {
        if (!$this->get('OrgType')) {
            $this->response(null, 400);
        }

        $data = $this->mbarang->readUnit($this->get('OrgType'), $this->get('OrgID'));
        $data = array_merge(array(array('id' => '-1', 'label' => '[Tambah Baru]')), $data);
        $this->response($data, 200);
    }
    public function units_get()
    {
        $sce_id = getSceID();
        $data = $this->munit->readDatas($sce_id,$this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function unit_c_get()
    {
        if (!$this->get('UnitMeasurementID')) {
            $this->response(null, 400);
        }

        $data = $this->munit->readData($this->get('UnitMeasurementID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function unit_post()
    {
        $data = $this->munit->createData($this->post('OrgType'), $this->post('OrgID'), $this->post('Name'),
            $this->post('Description'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function unit_put()
    {
        $data = $this->munit->updateData($this->put('Name'), $this->put('Description'), $this->put('UnitMeasurementID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function unit_delete()
    {
        if (!$this->delete('UnitMeasurementID')) {
            $this->response(null, 400);
        }

        $data = $this->munit->deleteData($this->delete('UnitMeasurementID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }

    public function penjualan_farmer_get()
    {
        $data = $this->msale->readFarmer($this->get('query'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }

    //pembelian
    public function pembelians_get()
    {
        $data = $this->mpurchase->readPembelians($this->get('Awal'), $this->get('Akhir'), $this->get('OrgType'),
            $this->get('OrgID'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function pembelian_get()
    {
        if (!$this->get('PurchaseID')) {
            $this->response(null, 400);
        }

        $data = $this->mpurchase->readPembelian($this->get('PurchaseID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function pembelian_post()
    {
        $data = $this->mpurchase->createPembelian($this->post('Date'), $this->post('OrgType'), $this->post('OrgID'),
            $this->post('SupplierID'), $this->post('Total'), $this->post('Pay'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function pembelian_put()
    {
        $data = $this->mpurchase->updatePembelian($this->put('Date'), $this->put('Total'), $this->put('Pay'), $this->put('PurchaseID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function pembelian_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mpurchase->deletePembelian($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //detail
    public function pembelian_detail_get()
    {
        $data = $this->mpurchase->readPembelianDetail($this->get('id'));
        $this->response($data, 200);
    }
    public function pembelian_detail_post()
    {
        $data = $this->mpurchase->createPembelianDetail($this->post('PurchaseID'), $this->post('InventoryID'),
            $this->post('Qty'), $this->post('Price'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function pembelian_detail_put()
    {
        $data = $this->mpurchase->updatePembelianDetail($this->put('InventoryID'), $this->put('Problem'), $this->put('Solution'),
            $this->put('Qty'), $this->put('Price'), $this->put('DetailID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function pembelian_detail_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mpurchase->deletePembelianDetail($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //end detail

    //retur penjualan
    public function retur_penjualans_get()
    {
        $data = $this->mretursale->readReturPenjualans($this->get('Awal'), $this->get('Akhir'), $this->get('OrgType'),
            $this->get('OrgID'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function retur_penjualan_penjualan_get()
    {
        if (!$this->get('query')) {
            $this->response(null, 400);
        }

        $data = $this->mretursale->readReturPenjualanPenjualan($this->get('OrgType'), $this->get('OrgID'), $this->get('query'),
            $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_penjualan_get()
    {
        if (!$this->get('SaleID')) {
            $this->response(null, 400);
        }

        $data = $this->mretursale->readReturPenjualan($this->get('SaleID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_penjualan_post()
    {
        $data = $this->mretursale->createReturPenjualan($this->post('SaleNumber'), $this->post('ReturSaleID'),
            $this->post('Date'), $this->post('OrgType'), $this->post('OrgID'), $this->post('CustomerID'),
            $this->post('Total'), $this->post('Pay'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_penjualan_put()
    {
        $data = $this->mretursale->updateReturPenjualan($this->put('Date'), $this->put('Total'), $this->put('Pay'), $this->put('SaleID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_penjualan_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mretursale->deleteReturPenjualan($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //detail
    public function retur_penjualan_detail_get()
    {
        $data = $this->mretursale->readReturPenjualanDetail($this->get('id'), $this->get('ReturSaleID'));
        $this->response($data, 200);
    }
    public function retur_penjualan_detail_post()
    {
        $data = $this->mretursale->createReturPenjualanDetail($this->post('SaleID'), $this->post('InventoryID'),
            $this->post('Problem'), $this->post('Solution'), $this->post('DateStart'), $this->post('DateEnd'), $this->post('Qty'),
            $this->post('Price'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_penjualan_detail_put()
    {
        $data = $this->mretursale->updateReturPenjualanDetail($this->put('InventoryID'), $this->put('Problem'), $this->put('Solution'),
            $this->put('DateStart'), $this->put('DateEnd'), $this->put('Qty'), $this->put('Price'),
            $this->put('DetailID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_penjualan_detail_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mretursale->deleteReturPenjualanDetail($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //end detail

    //retur pembelian
    public function retur_pembelians_get()
    {
        $data = $this->mreturpurchase->readReturPembelians($this->get('Awal'), $this->get('Akhir'), $this->get('OrgType'),
            $this->get('OrgID'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    public function retur_pembelian_pembelian_get()
    {
        if (!$this->get('query')) {
            $this->response(null, 400);
        }

        $data = $this->mreturpurchase->readReturPembelianPembelian($this->get('OrgType'), $this->get('OrgID'), $this->get('query'),
            $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_pembelian_get()
    {
        if (!$this->get('PurchaseID')) {
            $this->response(null, 400);
        }

        $data = $this->mreturpurchase->readReturPembelian($this->get('PurchaseID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_pembelian_post()
    {
        $data = $this->mreturpurchase->createReturPembelian($this->post('PurchaseNumber'), $this->post('ReturPurchaseID'),
            $this->post('Date'), $this->post('OrgType'), $this->post('OrgID'),
            $this->post('SupplierID'), $this->post('Total'), $this->post('Pay'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_pembelian_put()
    {
        $data = $this->mreturpurchase->updateReturPembelian($this->put('Date'), $this->put('Total'), $this->put('Pay'), $this->put('PurchaseID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_pembelian_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mreturpurchase->deleteReturPembelian($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //detail
    public function retur_pembelian_detail_get()
    {
        $data = $this->mreturpurchase->readReturPembelianDetail($this->get('id'), $this->get('ReturPurchaseID'));
        $this->response($data, 200);
    }
    public function retur_pembelian_detail_post()
    {
        $data = $this->mreturpurchase->createReturPembelianDetail($this->post('PurchaseID'), $this->post('InventoryID'),
            $this->post('Qty'), $this->post('Price'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_pembelian_detail_put()
    {
        $data = $this->mreturpurchase->updateReturPembelianDetail($this->put('InventoryID'), $this->put('Problem'), $this->put('Solution'),
            $this->put('Qty'), $this->put('Price'), $this->put('DetailID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be found'), 404);
        }

    }
    public function retur_pembelian_detail_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }

        $data = $this->mreturpurchase->deleteReturPembelianDetail($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Program could not be delete'), 404);
        }

    }
    //end detail
}
