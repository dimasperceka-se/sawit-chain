<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Certification_body extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('certification/mcertification_body');
    }
    
    public function data_get(){
        $data = $this->mcertification_body->readCertificationBody($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function data_post() {
        if ($this->post('CertBodyID')==''){
            $data = $this->mcertification_body->createCertificationBody($this->post('CertBodyName'), $this->post('CertBodyAddress'), $this->post('CertBodyPhone'), $this->post('CertBodyEmail'), $_SESSION['userid'], $this->post('PhotoOld'));
        }else{
            $data = $this->mcertification_body->updateCertificationBody($this->post('CertBodyName'), $this->post('CertBodyAddress'), $this->post('CertBodyPhone'), $this->post('CertBodyEmail'), $_SESSION['userid'], $this->post('CertBodyID'));
        }
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function data_logo_post() {
        $this->load->library('awsfileupload');
        if($this->post('CertBodyID')==""){
            if ($_FILES['Photo']['name'] != '') {
                $dir = FCPATH . 'images/certification_body';
                if(!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $gambar = 'certprog_' .date('Ymdhis').'.jpg';
                $upload = move_upload($_FILES, 'images/certification_body/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['message'] = lang('File uploaded');
                    $result['file'] = base_url().'images/certification_body/'.$gambar;
                    $result['filepath']   = 'images/certification_body/'.$gambar;
                    $this->response($result, 200);
                }else{
                    $result['file'] = "";
                    $result['filepath'] = "";
                    $result['message'] = "Photo not uploaded!";
                    $this->response($result, 200);
                }
            }else{
                $result['file'] = "";
                $result['filepath'] = "";
                $result['message'] = "No Photo.";
                $this->response($result, 200);
            }
        }else{
            if ($_FILES['Photo']['name'] != '') {
                $upload = $this->awsfileupload->upload($this->file['Photo']['tmp_name'],$this->file['Photo']['name'], AWSS3_CERT_BODY_PATH, 'images');
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('PhotoOld')) == true) {
                        $this->awsfileupload->delete($this->post('PhotoOld'));
                    }else{
                        delete_file($this->post('PhotoOld'));
                    }
                    $prosesUpdate = $this->mcertification_body->updateLogo($_POST["CertBodyID"],$upload['filenamepath']);
                    $result['success'] = true;
                    $result['message'] = lang('File uploaded');
                    $result['file'] = $upload['fileurl'];
                    $result['filepath']   = $upload['filenamepath'];
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('Upload to aws failed');
                    $this->response($result, 400);
                }
            }else{
                $result['file'] = "";
                $result['filepath'] = "";
                $result['message'] = "No Photo.";
                $this->response($result, 200);
            }
        }
    }
    
    public function detail_get(){
        $data = $this->mcertification_body->readCertificationBodyDetail($this->get('CertBodyID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function contacts_get(){
        $data = $this->mcertification_body->readContacts($this->get('CertBodyID'), $this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function contact_post() {
        $data = $this->mcertification_body->createContact($this->post('ContactCertBodyID'), $this->post('ContactName'), $this->post('ContactGender'), $this->post('ContactEmail'), $this->post('ContactPhone'), $this->post('ContactAddress'), $this->post('ContactPosition'), $this->post('StatusCode'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    function contact_delete() {
        if (!$this->delete('CertBodyContactID'))
            $this->response(NULL, 400);
        $data = $this->mcertification_body->deleteContact($this->delete('CertBodyContactID'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function contact_detail_get(){
        $data = $this->mcertification_body->readCertificationBodyContactDetail($this->get('CertBodyContactID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function contact_put() {
        $data = $this->mcertification_body->updateContact($this->put('ContactCertBodyID'), $this->put('ContactName'), $this->put('ContactGender'), $this->put('ContactEmail'), $this->put('ContactPhone'), $this->put('ContactAddress'), $this->put('ContactPosition'), $this->put('StatusCode'), $_SESSION['userid'], $this->put('CertBodyContactID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    function data_delete() {
        if (!$this->delete('CertBodyID'))
            $this->response(NULL, 400);
        $data = $this->mcertification_body->deleteCertificationBody($_SESSION['userid'], $this->delete('CertBodyID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
}
