<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Cocoa_price extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('basic/mcocoa_price');
    }

    function datas_get() {
        $cocoa_price = $this->mcocoa_price->readCocoaPrices($this->get('prov'), $this->get('key'), $this->get('dateStart'), $this->get('dateEnd'), $this->get('start'), $this->get('limit'));
        //echo "<pre>".print_r($cocoa_price,1);exit;
        if ($cocoa_price)
            $this->response($cocoa_price, 200);
        else
            $this->response(array(), 200);
    }

    function Kabupatens_get() {
        $sesPartner = ($_SESSION['PartnerID'] > 1)? $_SESSION['PartnerID']:'ALL';
        $data = $this->mcocoa_price->readKabupatens($this->get('key'),$sesPartner);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Provinsis_get() {
        //$sesPartner = ($_SESSION['FlagAccess'] > 0) ? $_SESSION['PartnerID'] : 'ALL';
        $sesPartner = '';
        $data = $this->mcocoa_price->readProvinsis($sesPartner);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Province_name_get($prov){
        if($prov==''){
            return "";
        }else{
            $data = $this->mcocoa_price->readProvinceName($prov);
            if($data) $this->response($data, 200);
        }
    }

    function data_post(){
        if($this->post('CocoaPriceID')==""){
            $proses = $this->mcocoa_price->create_cocoa_price($this->post('CocoaPriceDate'), $this->post('Kabupaten'), $this->post('CocoaPriceType'), $this->post('CocoaPrice'));
        }else{
            $proses = $this->mcocoa_price->update_cocoa_price($this->post('CocoaPriceDate'), $this->post('Kabupaten'), $this->post('CocoaPriceType'), $this->post('CocoaPrice'), $this->post('CocoaPriceID'));
        }
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    function data_get() {
        $cocoa_price = $this->mcocoa_price->readCocoaPrice($this->get('CocoaPriceID'));
        if ($cocoa_price){
            $this->response($cocoa_price, 200);
        }else{
            $this->response(array(), 200);
        }
    }

    function data_put(){
        $proses = $this->mcocoa_price->update_cocoa_price($this->put('CocoaPriceDate'), $this->put('Kabupaten'), $this->put('CocoaPriceType'), $this->put('CocoaPrice'), $this->put('CocoaPriceID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    function data_delete()
    {
        if (!$this->delete('CocoaPriceID')) {
            $this->response(null, 400);
        }

        $proses = $this->mcocoa_price->delete_cocoa_price($this->delete('CocoaPriceID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }

    }

}
