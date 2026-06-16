<?php 
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coop_tutup_tahun extends REST_Controller {

    public $_output = array('success' => false, 'msg' => 'Data is not valid'); //response data
    public $_num = 401; //response header

    public function __construct(){
        parent::__construct();
        $this->load->model('coop/mcashbox', '_model');
        $this->load->model('bank/mbank', '_mbnk');
    }

    function getclosedbook_get(){
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = '';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array(
            'CoopID' => getCoopID(),
        );

        
        $data = $this->_model->getList($start,$limit,$sort,$dir,$filter);
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function getbyid_post(){
        $data = $this->_model->getByID($this->input->post('cashboxID'));

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function deletecashbox_post(){
        $this->_model->deleteCashbox($this->input->post('did'));

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => array('success'=>true, 'msg'=>'Data has been deleted'), 'total' => 1);

        return $this->response($this->_output,  $this->_num);
    }

    function savedata_post(){
        if(strlen($this->input->post('cashboxID')) >= 1){
            $data = $this->_model->updateCashbox($this->input->post());
        }else{
            $data = $this->_model->createCashbox($this->input->post());
        }

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);
        
        return $this->response($this->_output,  $this->_num);
    }

    function getcombobank_get(){
        $filter = array();
        if(strlen($this->input->get('query')) != ''){ $filter['BankName'] = $this->input->get('query'); }

        $data = $this->_model->getComboBanks($filter);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any bank!'), 404);
    }

    function getcombocoa_get(){
        $filter = array();
        if(strlen($this->input->get('query')) != ''){ $filter['CoaTitle'] = $this->input->get('query'); }
        
        $data = $this->_model->getComboCOA($filter);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any bank!'), 404);
    }
} // end of class