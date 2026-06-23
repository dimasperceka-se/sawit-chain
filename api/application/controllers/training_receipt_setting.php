<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-30 16:52:11
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Training_receipt_setting extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('training/mreceipt_setting');
    }

    public function provinsi_get(){
        $data = $this->mreceipt_setting->getPropinsi($this->get('filter_prov'));
        $this->response($data, 200);
    }

    public function district_get(){
        $data = $this->mreceipt_setting->getDistrict($this->get('filter_district'),$this->get('prov'));
        $this->response($data, 200);
    }

    public function training_get(){
        $data = $this->mreceipt_setting->getTraining();
        $this->response($data, 200);
    }

    public function main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt_setting->getMainList($this->get('sTrainingId'),$this->get('sObjType'),$this->get('prov'),$this->get('dist'),$this->get('sub_dist'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function staff_farmer_autocom_get(){
        $data = $this->mreceipt_setting->getStaffFarmerAutocom(
            $this->get('query'),
            $this->get('isStaff'),
            $this->get('isFarmer'),
            $this->get('prov'),
            $this->get('dist'),
            $this->get('sub_dist'),
            $this->get('start'),
            $this->get('limit')
        );
        $this->response($data, 200);
    }

    public function seltrain_cpg_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt_setting->getSeltrainCpg($this->get('seltrainProvince'),$this->get('seltrainDistrict'),$this->get('seltrainTraining'),$this->get('seltrainTrainingDateRange'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function seltrain_cadre_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt_setting->getSeltrainCadre($this->get('seltrainProvince'),$this->get('seltrainDistrict'),$this->get('seltrainTraining'),$this->get('seltrainTrainingDateRange'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function seltrain_master_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt_setting->getSeltrainMaster($this->get('seltrainProvince'),$this->get('seltrainDistrict'),$this->get('seltrainTraining'),$this->get('seltrainTrainingDateRange'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function goods_list_rset_get(){
        $data = $this->mreceipt_setting->getGoodsListRSet($this->get('goods_tipe'),$this->get('ReceiptSetID'));
        $this->response($data, 200);
    }

    public function goods_list_filter_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt_setting->getGoodsList($this->get('call_from'),$this->get('filter_name'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function form_setting_get(){
        $data = $this->mreceipt_setting->getFormSetting($this->get('ReceiptSetID'));
        $this->response($data, 200);
    }

    public function setting_post(){
        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        if($varPost['ReceiptSetID'] == ""){
            //insert
            $proses = $this->mreceipt_setting->insertSetting($varPost);
        }else{
            //update
            $proses = $this->mreceipt_setting->updateSetting($varPost);
        }

        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function setting_delete(){
        $proses = $this->mreceipt_setting->deleteSetting($this->delete('ReceiptSetID'));
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function setting_insert_goods_post(){
        /*
        $proses['success'] = true;
        $proses['message'] = 'yosh';
        $this->response($proses, 200);
        $this->response('Yohoho', 404);
        */

        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }

            switch ($key) {
                case 'GoodsID':
                    $varPost[$key] = json_decode($value);
                break;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        $proses = $this->mreceipt_setting->insertSettingGoods($varPost);
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function setting_goods_delete(){
        $proses = $this->mreceipt_setting->deleteSettingGoods($this->delete('id'),$this->delete('callFrom'));
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function create_receipt_post(){
        $proses = $this->mreceipt_setting->createReceipt($this->post('ReceiptSetID'));
        $this->response($proses, 200);
    }
}
?>