<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class News extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cms/mnews', '_model');
    }

    public function data_get() {
        $data = $this->_model->readNews($this->get('key'), $_SESSION['userid'], $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }

    function combo_partner_get() {
        $data = $this->_model->ComboPartner();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any first buyer!'), 404);
        }
    }

    public function data_post() {
        $data = $this->_model->createNews($this->post('PartnerID'), $this->post('StatusNews'), $this->post('PublishDate'), $this->post('ImageName'), $this->post('ImagePath'), $this->post('ImageShow'), $this->post('Title'), $this->post('Content'), $_SESSION['userid']);
        
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function data_put() {
        $data = $this->_model->updateNews($this->put('NewsID'), $this->put('PartnerID'), $this->put('StatusNews'), $this->put('PublishDate'), $this->put('ImageName'), $this->put('ImagePath'), $this->put('ImageShow'), $this->put('Title'), $this->put('Content'), $_SESSION['userid']);
        
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }
    
    public function detail_get() {
        $data = $this->_model->readNewsDetail($this->get('NewsID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }

    function data_delete() {
        if (!$this->delete('NewsID'))
            $this->response(NULL, 400);
        $data = $this->_model->deleteNews($_SESSION['userid'], $this->delete('NewsID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function upload_post() {
        $data = $this->_model->uploadFiles($this->post('ImageShow'),$this->post('OpsiDisplay'),$this->post('NewsID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

}
