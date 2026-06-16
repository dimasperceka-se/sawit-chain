<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-29 15:12:27
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Basic_goods extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('basic/mgoods');
    }

    public function main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $data = $this->mgoods->getMainList($this->get('sNama'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function ref_unit_get(){
        $result = $this->mgoods->getRefUnit();
        $this->response($result, 200);
    }

    public function goods_post(){
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];

        if($this->post('GoodsID') == ""){
            $proses = $this->mgoods->createGoods($varPost);
        }else{
            $proses = $this->mgoods->updateGoods($varPost);
        }
        $this->response($proses, 200);
    }

    public function goods_delete(){
        if (!$this->delete('GoodsID')) {
            $this->response(null, 400);
        }

        $proses = $this->mgoods->deleteGoods($this->delete('GoodsID'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Error'), 404);
        }
    }

}
?>