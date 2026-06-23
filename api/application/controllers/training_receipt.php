<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-12 14:19:26
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Training_receipt extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('training/mreceipt');
    }

    public function main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt->getMainList($this->get('sTrainingId'),$this->get('sObjType'),$this->get('prov'),$this->get('dist'),$this->get('sub_dist'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function act_goods_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mreceipt->getActGoodsList($this->get('ReceiptID'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function receipt_post(){
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

        //update
        $proses = $this->mreceipt->updateReceipt($varPost);
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function receipt_delete(){
        $proses = $this->mreceipt->deleteReceipt($this->delete('ReceiptID'));
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function receipt_activity_post(){
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

        $proses = $this->mreceipt->updateReceiptActivity($varPost);
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function fill_form_receipt_get(){
        $data = $this->mreceipt->getFillFormReceipt($this->get('ReceiptID'));
        $this->response($data, 200);
    }

    public function receipt_field_model_get(){
        $data = $this->mreceipt->getReceiptPartFieldModel($this->get('ReceiptID'));
        $this->response($data, 200);
    }

    public function part_goods_item_get(){
        $data = $this->mreceipt->getPartGoodsItem($this->get('ReceiptID'));
        $this->response($data, 200);
    }

    public function receipt_participant_goods_post(){
        $paramKirim = json_decode($this->post('paramKirim'));
        //convert ke array
        foreach ($paramKirim as $key => $value) {
            $paramKirim[$key] = (array) $value;
        }

        $proses = $this->mreceipt->updateReceiptPartGoods($paramKirim);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response('Process Failed', 400);
        }
    }

    public function print_receipt_act_get(){
        $this->load->model('nursery/mnursery');
        $this->load->helper('date');
        $data = array();
        $this->load->language('general', 'indonesia');
        $ReceiptID = (int) $this->uri->segment(3);

        //get data receipt
        $dataReceipt = $this->mreceipt->getDataReceiptPrint($ReceiptID);
        $data['dataReceipt'] = $dataReceipt;

        //get data activity item
        $data['dataActGoods'] = $this->mreceipt->getDataActivityGoodsPrint($ReceiptID);

        switch ($dataReceipt['ObjType']) {
            case 'farmergroup':
                $data['labelTrainingHeader'] = "Field Farm School (FFS)";
            break;
            case 'cadre':
                $data['labelTrainingHeader'] = "Field Farm School (FFS)";
            break;
            case 'master':
                $data['labelTrainingHeader'] = "Master Training";
            break;
        }


        //logo atas
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($dataReceipt['DistrictID']);

        $this->load->view('cetak_receipt_activity', $data);
    }

    public function print_receipt_part_get(){
        $this->load->model('nursery/mnursery');
        $this->load->helper('date');
        $data = array();
        $this->load->language('general', 'indonesia');
        $ReceiptID = (int) $this->uri->segment(3);

        //get data receipt
        $dataReceipt = $this->mreceipt->getDataReceiptPrint($ReceiptID);
        $data['dataReceipt'] = $dataReceipt;

        //get data untuk tabel list
        $data['dataListSrc'] = $this->mreceipt->getDataListParticipantPrint($ReceiptID);

        //logo atas
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($dataReceipt['DistrictID']);

        switch ($dataReceipt['ObjType']) {
            case 'farmergroup':
                $data['labelTrainingHeader'] = "Field Farm School (FFS)";
            break;
            case 'cadre':
                $data['labelTrainingHeader'] = "Field Farm School (FFS)";
            break;
            case 'master':
                $data['labelTrainingHeader'] = "Master Training";
            break;
        }

        $this->load->view('cetak_receipt_participant', $data);
    }

}
?>